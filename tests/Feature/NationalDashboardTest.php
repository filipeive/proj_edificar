<?php

namespace Tests\Feature;

use App\Models\Congregation;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NationalDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_super_admin_can_open_national_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('national.dashboard'))
            ->assertForbidden();

        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($superAdmin)
            ->get(route('national.dashboard'))
            ->assertOk();
    }

    public function test_national_dashboard_aggregates_congregations_without_changing_local_tenant_scope(): void
    {
        $primary = Congregation::where('is_primary', true)->firstOrFail();
        $second = Congregation::create([
            'name' => 'Comunidade Esperança',
            'slug' => 'comunidade-esperanca',
            'is_active' => true,
        ]);

        Zone::create(['name' => 'Zona Life']);
        app(\App\Services\CongregationContext::class)->set($second);
        Zone::create(['name' => 'Zona Esperança']);

        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($superAdmin);

        $response = $this->get(route('national.dashboard'));

        $response->assertOk()
            ->assertViewHas('congregations', function ($congregations) use ($primary, $second) {
                return $congregations->contains(fn($item) => $item->id === $primary->id && $item->zones_count === 1)
                    && $congregations->contains(fn($item) => $item->id === $second->id && $item->zones_count === 1);
            });

        $this->assertCount(1, Zone::all());
        $this->assertSame('Zona Esperança', Zone::first()->name);
    }

    public function test_super_admin_can_register_a_congregation_with_its_first_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->post(route('national.congregations.store'), [
                'name' => 'Igreja Nova Aliança',
                'slug' => 'nova-alianca',
                'domain' => '',
                'subdomain' => 'nova-alianca',
                'latitude' => '-18.6657',
                'longitude' => '35.5296',
                'admin_name' => 'Administrador Local',
                'admin_email' => 'admin@nova-alianca.test',
                'admin_password' => 'Strong-password-2026',
                'admin_password_confirmation' => 'Strong-password-2026',
            ])
            ->assertRedirect(route('national.congregations.index'));

        $congregation = Congregation::where('slug', 'nova-alianca')->firstOrFail();
        $admin = User::withoutGlobalScope('congregation')->where('email', 'admin@nova-alianca.test')->firstOrFail();

        $this->assertSame($congregation->id, $admin->congregation_id);
        $this->assertSame('admin', $admin->role);
        $this->assertSame('-18.6657000', $congregation->latitude);
        $this->assertSame('35.5296000', $congregation->longitude);
        $this->assertDatabaseHas('congregations', ['id' => $congregation->id, 'is_active' => true]);
    }

    public function test_national_map_receives_geolocated_congregations(): void
    {
        $primary = Congregation::where('is_primary', true)->firstOrFail();
        $primary->update(['latitude' => -18.6657, 'longitude' => 35.5296]);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->get(route('national.dashboard'))
            ->assertOk()
            ->assertSee('data-national-congregation-map', false)
            ->assertSee('national-congregation-map-data', false)
            ->assertSee('Life Church')
            ->assertSee('-18.6657', false)
            ->assertSee('35.5296', false);
    }

    public function test_congregation_edit_form_labels_its_map_as_headquarters_location(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $primary = Congregation::where('is_primary', true)->firstOrFail();

        $this->actingAs($superAdmin)
            ->get(route('national.congregations.edit', $primary))
            ->assertOk()
            ->assertSee('Localização da congregação')
            ->assertSee('marcar a sede da congregação');
    }

    public function test_super_admin_can_edit_a_congregation_but_cannot_change_primary_flag(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $primary = Congregation::where('is_primary', true)->firstOrFail();

        $this->actingAs($superAdmin)
            ->put(route('national.congregations.update', $primary), [
                'name' => 'Life Church Nacional',
                'slug' => 'life-church',
                'domain' => '',
                'subdomain' => '',
                'is_active' => '1',
                'is_primary' => '0',
            ])
            ->assertRedirect(route('national.congregations.index'));

        $this->assertSame('Life Church Nacional', $primary->fresh()->name);
        $this->assertTrue($primary->fresh()->is_primary);

        $this->put(route('national.congregations.update', $primary), [
            'name' => $primary->name,
            'slug' => $primary->slug,
            'is_active' => '0',
        ])->assertSessionHasErrors('is_active');
    }

    public function test_super_admin_can_view_and_export_a_national_report(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($superAdmin);

        $filters = [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ];

        $this->get(route('national.reports.index', $filters))
            ->assertOk()
            ->assertSee('Relatório por congregação');

        $this->get(route('national.reports.export', $filters))
            ->assertDownload('relatorio-nacional-' . now()->startOfMonth()->format('Ymd') . '-' . now()->endOfMonth()->format('Ymd') . '.xlsx');

        $this->get(route('national.reports.export.pdf', $filters))
            ->assertDownload('relatorio-nacional-' . now()->startOfMonth()->format('Ymd') . '-' . now()->endOfMonth()->format('Ymd') . '.pdf');
    }

    public function test_super_admin_can_add_secondary_admin_and_reset_admin_password(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $congregation = Congregation::create([
            'name' => 'Comunidade Beira',
            'slug' => 'comunidade-beira',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin)
            ->post(route('national.congregations.admins.store', $congregation), [
                'admin_name' => 'Segundo Admin',
                'admin_email' => 'admin2@beira.test',
                'admin_password' => 'Beira-Secured-2026',
                'admin_password_confirmation' => 'Beira-Secured-2026',
            ])
            ->assertRedirect();

        $admin = User::withoutGlobalScope('congregation')->where('email', 'admin2@beira.test')->firstOrFail();
        $this->assertSame($congregation->id, $admin->congregation_id);
        $this->assertSame('admin', $admin->role);

        $this->post(route('national.congregations.admins.reset-password', [$congregation, $admin]), [
            'new_password' => 'New-Secret-Password-99',
            'new_password_confirmation' => 'New-Secret-Password-99',
        ])->assertRedirect();

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('New-Secret-Password-99', $admin->fresh()->password));
    }

    public function test_edificar_routes_are_blocked_for_congregations_without_edificar_module(): void
    {
        $tenant = Congregation::create([
            'name' => 'Igreja Nampula',
            'slug' => 'igreja-nampula',
            'is_active' => true,
            'has_edificar_project' => false,
        ]);

        app(\App\Services\CongregationContext::class)->set($tenant);

        $admin = User::factory()->create([
            'role' => 'admin',
            'congregation_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->get(route('packages.index'))
            ->assertNotFound();

        $this->get(route('commitments.index'))
            ->assertNotFound();

        $this->get(route('packages.dashboard'))
            ->assertNotFound();
    }

    public function test_national_dashboard_ignores_edificar_package_contributions_in_totals(): void
    {
        $primary = Congregation::where('is_primary', true)->firstOrFail();
        $context = app(\App\Services\CongregationContext::class);
        $context->set($primary);

        $zone = \App\Models\Zone::create(['name' => 'Zona Central']);
        $supervision = \App\Models\Supervision::create([
            'name' => 'Supervisão Central',
            'zone_id' => $zone->id,
        ]);
        $cell = \App\Models\Cell::create([
            'name' => 'Célula Emanuel',
            'zone_id' => $zone->id,
            'supervision_id' => $supervision->id,
        ]);

        $package = \App\Models\CommitmentPackage::create([
            'name' => 'Pacote Pilares',
            'min_amount' => 5000,
            'order' => 1,
        ]);

        $member = User::factory()->create(['role' => 'membro']);

        // Contribuição com pacote (Projeto Edificar - 10.000 MT)
        \App\Models\Contribution::create([
            'user_id' => $member->id,
            'zone_id' => $zone->id,
            'supervision_id' => $supervision->id,
            'cell_id' => $cell->id,
            'package_id' => $package->id,
            'amount' => 10000,
            'contribution_date' => now()->toDateString(),
            'status' => 'verificada',
            'payment_method' => 'mpesa',
        ]);

        // Contribuição eclesiástica geral sem pacote (2.500 MT)
        \App\Models\Contribution::create([
            'user_id' => $member->id,
            'zone_id' => $zone->id,
            'supervision_id' => $supervision->id,
            'cell_id' => $cell->id,
            'package_id' => null,
            'amount' => 2500,
            'contribution_date' => now()->toDateString(),
            'status' => 'verificada',
            'payment_method' => 'mpesa',
        ]);

        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($superAdmin);

        $response = $this->get(route('national.dashboard'));
        $response->assertOk();

        $stats = $response->viewData('stats');
        // Total de contribuições verificadas eclesiásticas nacionais deve ser estritamente 2.500 MT (excluindo os 10.000 MT de pacote)
        $this->assertEquals(2500, $stats['verified_contributions_this_month']);
    }

    public function test_national_reports_page_displays_details_button_for_each_congregation(): void
    {
        $primary = Congregation::where('is_primary', true)->firstOrFail();
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->get(route('national.reports.index'))
            ->assertOk()
            ->assertSee('Detalhes')
            ->assertSee(route('national.congregations.show', $primary));
    }

    public function test_super_admin_can_view_congregation_details_with_cultos_and_ecclesiastical_finances(): void
    {
        $congregation = Congregation::create([
            'name' => 'Igreja Zambézia',
            'slug' => 'igreja-zambezia',
            'is_active' => true,
        ]);

        app(\App\Services\CongregationContext::class)->run($congregation, function () {
            $admin = User::factory()->create(['role' => 'admin', 'name' => 'Pastor Zambézia']);
            $zone = \App\Models\Zone::create(['name' => 'Zona Quelimane']);
            $supervision = \App\Models\Supervision::create(['name' => 'Supervisão Quelimane', 'zone_id' => $zone->id]);
            $cell = \App\Models\Cell::create(['name' => 'Célula Boas Novas', 'zone_id' => $zone->id, 'supervision_id' => $supervision->id]);

            \App\Models\Service::create([
                'date' => now()->toDateString(),
                'service_type' => '1st',
                'theme' => 'Culto de Celebração',
                'adults_members' => 120,
                'adults_visitors' => 15,
                'adults_salvations' => 4,
                'special_offerings_total' => 3500,
            ]);

            \App\Models\Visitor::create([
                'name' => 'Visitante Ilustre',
                'phone' => '+258841234567',
                'visit_date' => now()->toDateString(),
                'contact_status' => 'contatado',
                'created_by' => $admin->id,
            ]);
        });

        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($superAdmin)
            ->get(route('national.congregations.show', $congregation));

        $response->assertOk()
            ->assertSee('Igreja Zambézia')
            ->assertSee('Culto de Celebração')
            ->assertSee('Visitante Ilustre')
            ->assertSee('Pastor Zambézia')
            ->assertSee('3.500,00 MT');
    }

    public function test_super_admin_can_toggle_admin_status(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $congregation = Congregation::create([
            'name' => 'Igreja Gaza',
            'slug' => 'igreja-gaza',
            'is_active' => true,
        ]);

        $admin = null;
        app(\App\Services\CongregationContext::class)->run($congregation, function () use (&$admin) {
            $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        });

        $this->actingAs($superAdmin)
            ->post(route('national.congregations.admins.toggle-status', [$congregation, $admin]))
            ->assertRedirect();

        $this->assertFalse($admin->fresh()->is_active);

        $this->post(route('national.congregations.admins.toggle-status', [$congregation, $admin]))
            ->assertRedirect();

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_super_admin_can_switch_portal_context_between_national_and_local(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        // Default: hits /dashboard -> national.dashboard
        $this->actingAs($superAdmin)
            ->get(route('dashboard'))
            ->assertRedirect(route('national.dashboard'));

        // Switch to local
        $this->actingAs($superAdmin)
            ->get(route('national.switch.local'))
            ->assertRedirect(route('dashboard.admin'))
            ->assertSessionHas('portal_context', 'local');

        // With session 'portal_context' = 'local', /dashboard redirects to dashboard.admin
        $this->withSession(['portal_context' => 'local'])
            ->actingAs($superAdmin)
            ->get(route('dashboard'))
            ->assertRedirect(route('dashboard.admin'));

        // Switch to national
        $this->withSession(['portal_context' => 'local'])
            ->actingAs($superAdmin)
            ->get(route('national.switch.national'))
            ->assertRedirect(route('national.dashboard'))
            ->assertSessionHas('portal_context', 'national');
    }

    public function test_super_admin_can_download_single_service_pdf_for_congregation(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $congregation = Congregation::create([
            'name' => 'Igreja Nampula',
            'slug' => 'igreja-nampula',
            'is_active' => true,
        ]);

        $service = null;
        app(\App\Services\CongregationContext::class)->run($congregation, function () use (&$service) {
            $service = \App\Models\Service::create([
                'date' => now()->toDateString(),
                'service_type' => '1st',
                'theme' => 'Graça e Poder',
                'adults_members' => 85,
                'adults_visitors' => 12,
                'adults_salvations' => 3,
                'special_offerings_total' => 2000,
            ]);
        });

        $response = $this->actingAs($superAdmin)
            ->get(route('national.congregations.services.pdf', [$congregation, $service]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_super_admin_can_download_services_report_pdf_for_congregation(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $congregation = Congregation::create([
            'name' => 'Igreja Beira',
            'slug' => 'igreja-beira',
            'is_active' => true,
        ]);

        app(\App\Services\CongregationContext::class)->run($congregation, function () {
            \App\Models\Service::create([
                'date' => now()->toDateString(),
                'service_type' => '1st',
                'theme' => 'Fé Inabalável',
                'adults_members' => 150,
                'adults_visitors' => 20,
                'adults_salvations' => 5,
                'special_offerings_total' => 5000,
            ]);
        });

        $response = $this->actingAs($superAdmin)
            ->get(route('national.congregations.services.report-pdf', [
                $congregation,
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->endOfMonth()->toDateString(),
            ]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }
}
