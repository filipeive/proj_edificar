<?php

namespace Tests\Feature;

use App\Models\OfferingType;
use App\Models\Contribution;
use App\Models\Service;
use App\Models\ServiceIndividualOffering;
use App\Models\ServiceOffering;
use App\Models\ServiceTithe;
use App\Models\Cell;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialDashboardServiceIncomeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ecclesiastical_dashboard_includes_all_financial_entries_recorded_for_services()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $offeringType = OfferingType::create([
            'name' => 'Oferta geral',
            'code' => 'oferta-geral-dashboard-test',
            'scope' => 'eclesiastico',
            'is_active' => true,
            'order' => 1,
        ]);

        $service = Service::create([
            'date' => now()->toDateString(),
            'service_type' => '1st',
            'special_offerings_total' => 25,
        ]);

        ServiceOffering::create([
            'service_id' => $service->id,
            'offering_type_id' => $offeringType->id,
            'amount' => 100,
        ]);

        $inactiveType = OfferingType::create([
            'name' => 'Oferta histórica',
            'code' => 'oferta-historica-dashboard-test',
            'scope' => 'eclesiastico',
            'is_active' => false,
            'order' => 2,
        ]);
        ServiceOffering::create([
            'service_id' => $service->id,
            'offering_type_id' => $inactiveType->id,
            'amount' => 10,
        ]);

        ServiceTithe::create([
            'service_id' => $service->id,
            'member_name' => 'Membro de teste',
            'amount' => 20,
        ]);

        ServiceIndividualOffering::create([
            'service_id' => $service->id,
            'member_name' => 'Visitante de teste',
            'amount' => 30,
            'description' => 'Oferta individual',
        ]);

        $zone = Zone::create(['name' => 'Zona das contribuições de teste']);
        $supervision = Supervision::create(['name' => 'Supervisão das contribuições de teste', 'zone_id' => $zone->id]);
        $cell = Cell::create([
            'name' => 'Célula das contribuições de teste',
            'type' => 'membros',
            'supervision_id' => $supervision->id,
        ]);
        Contribution::forceCreate([
            'user_id' => $user->id,
            'cell_id' => $cell->id,
            'supervision_id' => $supervision->id,
            'zone_id' => $zone->id,
            'amount' => 40,
            'contribution_date' => now()->toDateString(),
            'status' => 'verificada',
            'offering_type_id' => null,
        ]);

        $response = $this->get(route('financial.dashboard', [
            'month' => now()->format('m'),
            'year' => now()->format('Y'),
            'scope' => 'eclesiastico',
        ]));

        $response->assertOk();
        $response->assertViewHas('grandTotal', 225.0);
        $response->assertViewHas('totals', function ($totals) {
            return collect($totals)->contains(fn($item) => $item['type'] === 'Contribuições sem tipo' && $item['total'] == 40)
                && collect($totals)->contains(fn($item) => $item['type'] === 'Oferta histórica' && $item['total'] == 10);
        });

        $filters = [
            'month' => now()->format('m'),
            'year' => now()->format('Y'),
            'scope' => 'eclesiastico',
        ];

        $this->get(route('financial.dashboard.export.excel', $filters))
            ->assertDownload('relatorio_financeiro_eclesiastico_' . now()->format('Y-m') . '.xlsx');

        $this->get(route('financial.dashboard.export.pdf', $filters))
            ->assertDownload('relatorio_financeiro_eclesiastico_' . now()->format('Y-m') . '.pdf');
    }
}
