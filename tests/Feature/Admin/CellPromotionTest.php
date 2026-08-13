<?php

namespace Tests\Feature\Admin;

use App\Models\Cell;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CellPromotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_promote_lider_in_lideres_cell_to_sub_supervisor()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $zone = Zone::create(['name' => 'Zona Teste']);
        $supervision = Supervision::create(['name' => 'Supervisão Teste', 'zone_id' => $zone->id]);
        $cellLideres = Cell::create([
            'name' => 'Célula de Líderes Teste',
            'supervision_id' => $supervision->id,
            'type' => Cell::TYPE_LIDERES,
        ]);

        $liderMember = User::factory()->create([
            'role' => 'lider_celula',
            'cell_id' => $cellLideres->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('cells.promote-sub-supervisor', ['cell' => $cellLideres, 'user' => $liderMember]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('sub_supervisor', $liderMember->fresh()->role);
        $this->assertEquals($liderMember->id, $supervision->fresh()->sub_supervisor_id);
    }

    public function test_can_promote_supervisor_in_supervisores_cell_to_subpastor_zona()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $zone = Zone::create(['name' => 'Zona Teste 2']);
        $supervision = Supervision::create(['name' => 'Supervisão Teste 2', 'zone_id' => $zone->id]);
        $cellSupervisores = Cell::create([
            'name' => 'Célula de Supervisores Teste',
            'supervision_id' => $supervision->id,
            'type' => Cell::TYPE_SUPERVISORES,
        ]);

        $supervisorMember = User::factory()->create([
            'role' => 'supervisor',
            'cell_id' => $cellSupervisores->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('cells.promote-subpastor-zona', ['cell' => $cellSupervisores, 'user' => $supervisorMember]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('subpastor_zona', $supervisorMember->fresh()->role);
    }
}
