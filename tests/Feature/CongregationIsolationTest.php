<?php

namespace Tests\Feature;

use App\Models\Cell;
use App\Models\Congregation;
use App\Models\CellMeeting;
use App\Models\CommitmentPackage;
use App\Models\Supervision;
use App\Models\EventType;
use App\Models\User;
use App\Models\Zone;
use App\Http\Middleware\IdentifyCongregation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Illuminate\Validation\ValidationException;
use App\Services\CongregationContext;
use Tests\TestCase;

class CongregationIsolationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tenant_models_are_scoped_and_foreign_congregation_links_are_rejected()
    {
        $context = app(CongregationContext::class);
        $lifeChurch = Congregation::where('is_primary', true)->firstOrFail();
        $otherChurch = Congregation::create([
            'name' => 'Comunidade Esperança',
            'slug' => 'comunidade-esperanca',
            'is_active' => true,
        ]);

        $context->set($lifeChurch);
        $this->assertSame((int) $lifeChurch->id, $context->id());
        $lifeZone = Zone::create(['name' => 'Zona Centro']);
        $lifeSupervision = Supervision::create([
            'name' => 'Supervisão Centro',
            'zone_id' => $lifeZone->id,
        ]);

        $context->set($otherChurch);
        $otherZone = Zone::create(['name' => 'Zona Centro']);
        $this->assertSame($otherChurch->id, $otherZone->congregation_id);
        $this->assertCount(1, Zone::all());
        $this->assertSame($otherZone->id, Zone::first()->id);

        try {
            Supervision::create([
                'name' => 'Vínculo indevido',
                'zone_id' => $lifeZone->id,
            ]);
            $this->fail('A supervisão não pode referenciar uma zona de outra congregação.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('zone_id', $exception->errors());
        }

        $this->assertSame($lifeChurch->id, $lifeSupervision->congregation_id);
    }

    /** @test */
    public function authenticated_users_only_see_their_congregations_zones()
    {
        $context = app(CongregationContext::class);
        $lifeChurch = Congregation::where('is_primary', true)->firstOrFail();
        $otherChurch = Congregation::create([
            'name' => 'Igreja do Norte',
            'slug' => 'igreja-do-norte',
            'is_active' => true,
        ]);

        $context->set($lifeChurch);
        Zone::create(['name' => 'Zona da Life Church']);

        $context->set($otherChurch);
        Zone::create(['name' => 'Zona da Igreja do Norte']);
        $admin = User::factory()->create([
            'name' => 'Admin da Igreja do Norte',
            'role' => 'admin',
        ]);

        $this->actingAs($admin);
        $request = Request::create('/api/v1/profile', 'GET');
        $response = app(IdentifyCongregation::class)->handle(
            $request,
            fn() => response()->noContent()
        );

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame($otherChurch->id, app(CongregationContext::class)->id());
        $this->assertSame(['Zona da Igreja do Norte'], Zone::orderBy('name')->pluck('name')->all());

        Sanctum::actingAs($admin);
        $this->getJson('/api/v1/profile')
            ->assertOk()
            ->assertJsonPath('data.name', 'Admin da Igreja do Norte');
    }

    /** @test */
    public function syncing_meeting_participants_stores_the_meetings_congregation()
    {
        $context = app(CongregationContext::class);
        $congregation = Congregation::where('is_primary', true)->firstOrFail();
        $context->set($congregation);
        $user = User::factory()->create(['role' => 'admin']);
        $meeting = CellMeeting::create([
            'meeting_date' => now()->toDateString(),
            'meeting_type' => 'general',
            'theme' => 'Encontro de teste',
        ]);

        $meeting->participants()->sync([$user->id]);

        $this->assertSame(
            $congregation->id,
            (int) $meeting->participants()->first()->pivot->congregation_id
        );
    }

    /** @test */
    public function separate_congregations_can_reuse_package_names_and_event_type_codes()
    {
        $context = app(CongregationContext::class);
        $primary = Congregation::where('is_primary', true)->firstOrFail();
        $other = Congregation::create([
            'name' => 'Igreja de Teste',
            'slug' => 'igreja-de-teste',
            'is_active' => true,
        ]);

        $context->set($primary);
        CommitmentPackage::create(['name' => 'Pacote 1', 'min_amount' => 100, 'order' => 1]);
        EventType::create(['name' => 'Conferência', 'code' => 'conferencia', 'category' => 'other']);

        $context->set($other);
        CommitmentPackage::create(['name' => 'Pacote 1', 'min_amount' => 150, 'order' => 1]);
        EventType::create(['name' => 'Conferência', 'code' => 'conferencia', 'category' => 'other']);

        $this->assertSame(['Pacote 1'], CommitmentPackage::pluck('name')->all());
        $this->assertSame(['conferencia'], EventType::pluck('code')->all());
    }

    /** @test */
    public function user_of_another_congregation_can_login_and_access_dashboard()
    {
        $otherChurch = Congregation::create([
            'name' => 'Life Church Maputo',
            'slug' => 'lcmap',
            'is_active' => true,
        ]);

        $user = app(CongregationContext::class)->run($otherChurch, function () use ($otherChurch) {
            return User::create([
                'name' => 'Pastor Maputo',
                'email' => 'pastor.maputo@example.com',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'congregation_id' => $otherChurch->id,
                'is_active' => true,
            ]);
        });

        $response = $this->post('/login', [
            'email' => 'pastor.maputo@example.com',
            'password' => 'password123',
            'congregation' => 'lcmap',
        ]);

        $response->assertRedirect('/dashboard');

        $dashResponse = $this->get('/dashboard');
        $dashResponse->assertRedirect(route('dashboard.admin'));

        $adminDash = $this->get(route('dashboard.admin'));
        $adminDash->assertOk();
        $adminDash->assertSee('Life Church Maputo');
        $adminDash->assertDontSee('Projeto Edificar');
    }
}
