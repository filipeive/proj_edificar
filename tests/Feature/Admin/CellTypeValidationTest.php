<?php

namespace Tests\Feature\Admin;

use App\Models\Cell;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CellTypeValidationTest extends TestCase
{
    use RefreshDatabase;

    protected Zone $zone;
    protected Supervision $supervision;
    protected Cell $cell;
    protected User $admin;
    protected User $membro;
    protected User $liderCelula;
    protected User $pastorSenior;
    protected User $supervisor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->zone = Zone::create(['name' => 'Zona Teste']);
        $this->supervision = Supervision::create(['name' => 'Supervisão Teste', 'zone_id' => $this->zone->id]);
        $this->cell = Cell::create([
            'name' => 'Célula Teste',
            'type' => Cell::TYPE_MEMBROS,
            'supervision_id' => $this->supervision->id,
            'leader_id' => null,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Teste',
            'email' => 'admin_cell_type_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->membro = User::create([
            'name' => 'Membro Comum',
            'email' => 'membro_cell_type_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'membro',
            'is_active' => true,
        ]);

        $this->liderCelula = User::create([
            'name' => 'Líder de Célula',
            'email' => 'lider_cell_type_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'lider_celula',
            'is_active' => true,
            'cell_id' => $this->cell->id,
        ]);

        $this->pastorSenior = User::create([
            'name' => 'Pastor Sénior',
            'email' => 'pastor_senior_cell_type_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'pastor_senior',
            'is_active' => true,
        ]);

        $this->supervisor = User::create([
            'name' => 'Supervisor Teste',
            'email' => 'supervisor_cell_type_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin);
    }

    public function test_update_redirects_with_toast_when_leader_is_incompatible_with_type(): void
    {
        $this->get(route('cells.edit', $this->cell));

        $response = $this->put(route('cells.update', $this->cell), [
            'name' => 'Célula Teste',
            'type' => Cell::TYPE_LIDERES,
            'supervision_id' => $this->supervision->id,
            'leader_id' => $this->membro->id,
        ]);

        $response->assertRedirect(route('cells.edit', $this->cell));
        $response->assertSessionHas('error', 'O líder selecionado não é compatível com o tipo de célula selecionado.');
        $response->assertSessionHasErrors('leader_id');
        $this->assertDatabaseHas('cells', ['id' => $this->cell->id, 'type' => Cell::TYPE_MEMBROS]);
    }

    public function test_update_succeeds_with_compatible_leader_for_type(): void
    {
        $response = $this->put(route('cells.update', $this->cell), [
            'name' => 'Célula Teste',
            'type' => Cell::TYPE_LIDERES,
            'supervision_id' => $this->supervision->id,
            'leader_id' => $this->pastorSenior->id,
            'timoteos' => [],
        ]);

        $response->assertRedirect(route('cells.show', $this->cell));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('cells', ['id' => $this->cell->id, 'type' => Cell::TYPE_LIDERES]);
    }

    public function test_store_redirects_with_toast_when_timoteo_is_incompatible_with_type(): void
    {
        $this->get(route('cells.create'));

        $response = $this->post(route('cells.store'), [
            'name' => 'Nova Célula',
            'type' => Cell::TYPE_MEMBROS,
            'supervision_id' => $this->supervision->id,
            'leader_id' => $this->liderCelula->id,
            'timoteos' => [$this->supervisor->id],
        ]);

        $response->assertSessionHas('error', 'O membro Supervisor Teste não é compatível com o tipo de célula selecionado.');
        $response->assertSessionHasErrors('timoteos');
        $this->assertDatabaseMissing('cells', ['name' => 'Nova Célula']);
    }

    public function test_eligible_leaders_endpoint_works_without_cell_id_for_create(): void
    {
        $response = $this->getJson(route('cells.eligible-leaders') . '?cell_type=' . Cell::TYPE_LIDERES);

        $response->assertOk()
            ->assertJsonStructure([['id', 'name', 'email', 'role']]);

        $roles = collect($response->json())->pluck('role')->unique()->all();

        $this->assertContains('supervisor', $roles);
        $this->assertContains('pastor_senior', $roles);
        $this->assertNotContains('membro', $roles);
    }
}