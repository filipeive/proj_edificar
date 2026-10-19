<?php

namespace Tests\Feature;

use App\Models\Cell;
use App\Models\Contribution;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminDashboardOptimizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_dashboard_renders_with_optimized_queries()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $zone = Zone::create([
            'name' => 'Zona Norte',
            'is_active' => true,
        ]);

        $supervision = Supervision::create([
            'name' => 'Supervisão 1',
            'zone_id' => $zone->id,
            'is_active' => true,
        ]);

        $cell = Cell::create([
            'name' => 'Célula Alpha',
            'supervision_id' => $supervision->id,
            'type' => 'membros',
        ]);

        $member = User::factory()->create([
            'role' => 'membro',
            'cell_id' => $cell->id,
            'is_active' => true,
        ]);

        Contribution::create([
            'user_id' => $member->id,
            'zone_id' => $zone->id,
            'supervision_id' => $supervision->id,
            'cell_id' => $cell->id,
            'amount' => 1500,
            'status' => 'verificada',
            'contribution_date' => now()->toDateString(),
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('dashboard.admin'));

        $response->assertStatus(200);
        $response->assertViewHas('zoneStats');
        $response->assertViewHas('zoneStructures');
        $response->assertViewHas('topCells');
    }
}
