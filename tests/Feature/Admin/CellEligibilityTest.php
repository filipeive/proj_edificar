<?php

namespace Tests\Feature\Admin;

use App\Models\Cell;
use App\Models\Supervision;
use App\Models\User;
use App\Models\Zone;
use App\Services\CellEligibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CellEligibilityTest extends TestCase
{
    use RefreshDatabase;

    protected Zone $zone1;
    protected Zone $zone2;
    protected Supervision $supervision1;
    protected Supervision $supervision2;
    protected Cell $cellMembros;
    protected Cell $cellLideres;
    protected Cell $cellSupervisores;
    protected Cell $cellPastoresZona;
    protected Cell $cellPastores;
    protected User $admin;
    protected CellEligibilityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->zone1 = Zone::create(['name' => 'Zona 1']);
        $this->zone2 = Zone::create(['name' => 'Zona 2']);
        $this->supervision1 = Supervision::create(['name' => 'Sup Zona 1', 'zone_id' => $this->zone1->id]);
        $this->supervision2 = Supervision::create(['name' => 'Sup Zona 2', 'zone_id' => $this->zone2->id]);

        $this->cellMembros = Cell::create(['name' => 'Cel Membros', 'type' => Cell::TYPE_MEMBROS, 'supervision_id' => $this->supervision1->id]);
        $this->cellLideres = Cell::create(['name' => 'Cel Lideres', 'type' => Cell::TYPE_LIDERES, 'supervision_id' => $this->supervision1->id]);
        $this->cellSupervisores = Cell::create(['name' => 'Cel Supervisores', 'type' => Cell::TYPE_SUPERVISORES, 'supervision_id' => $this->supervision1->id]);
        $this->cellPastoresZona = Cell::create(['name' => 'Cel Pastores Zona', 'type' => Cell::TYPE_PASTORES_ZONA, 'supervision_id' => $this->supervision2->id]);
        $this->cellPastores = Cell::create(['name' => 'Cel Pastores', 'type' => Cell::TYPE_PASTORES, 'supervision_id' => $this->supervision2->id]);

        $this->admin = User::create([
            'name' => 'Admin Teste',
            'email' => 'admin_elig_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->service = app(CellEligibilityService::class);
    }

    protected function makeUser(string $role, ?Cell $cell = null): User
    {
        return User::create([
            'name' => 'Pessoa ' . uniqid(),
            'email' => 'user_elig_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => $role,
            'is_active' => true,
            'cell_id' => $cell?->id,
        ]);
    }

    // --- Matriz de elegibilidade ---

    public function test_membro_so_pode_entrar_em_celula_de_membros(): void
    {
        $membro = $this->makeUser('membro');

        $this->assertTrue($this->service->podeSerAdicionado($membro, $this->cellMembros));
        $this->assertNotTrue($this->service->podeSerAdicionado($membro, $this->cellSupervisores));
        $this->assertNotTrue($this->service->podeSerAdicionado($membro, $this->cellPastores));
    }

    public function test_timoteo_pode_subir_para_celula_de_lideranca(): void
    {
        $timoteo = $this->makeUser('timoteo');

        $this->assertTrue($this->service->podeSerAdicionado($timoteo, $this->cellLideres));
    }

    public function test_lider_sem_subsupervisor_nao_entra_em_celula_de_supervisao(): void
    {
        // No modelo atual, a "subcategoria subsupervisor" é o role sub_supervisor.
        // Um lider_celula (sem ser sub_supervisor) NÃO pode entrar numa célula de supervisores.
        $lider = $this->makeUser('lider_celula');

        $result = $this->service->podeSerAdicionado($lider, $this->cellSupervisores);
        $this->assertIsString($result);
    }

    public function test_sub_supervisor_pode_subir_para_pastores_de_zona(): void
    {
        $sub = $this->makeUser('sub_supervisor');

        $this->assertTrue($this->service->podeSerAdicionado($sub, $this->cellPastoresZona));
    }

    public function test_pastor_zona_nao_entra_em_supervisores_mas_subpastor_zona_sobe(): void
    {
        $pastorZona = $this->makeUser('pastor_zona');
        $this->assertNotTrue($this->service->podeSerAdicionado($pastorZona, $this->cellSupervisores));

        $subPastorZona = $this->makeUser('subpastor_zona');

        $this->assertTrue($this->service->podeSerAdicionado($subPastorZona, $this->cellPastores));
    }

    // --- Regra de zona ---

    public function test_zona_diferente_rejeitada_para_membros(): void
    {
        $membroZona1 = $this->makeUser('membro', Cell::create(['name' => 'Outra Z1', 'type' => Cell::TYPE_MEMBROS, 'supervision_id' => $this->supervision1->id]));
        $this->assertTrue($this->service->podeSerAdicionado($membroZona1, $this->cellMembros));

        $membroZona2 = $this->makeUser('membro', Cell::create(['name' => 'Outra Z2', 'type' => Cell::TYPE_MEMBROS, 'supervision_id' => $this->supervision2->id]));
        $result = $this->service->podeSerAdicionado($membroZona2, $this->cellMembros);
        $this->assertIsString($result);
    }

    public function test_zona_diferente_aceite_para_pastores_de_zona(): void
    {
        // Célula alvo de pastores_zona está na zona 2; pessoa está na zona 1 -> aceite.
        $pastorZona = $this->makeUser('pastor_zona', Cell::create(['name' => 'Z1', 'type' => Cell::TYPE_PASTORES_ZONA, 'supervision_id' => $this->supervision1->id]));
        $this->assertTrue($this->service->podeSerAdicionado($pastorZona, $this->cellPastoresZona));
    }

    // --- Endpoints ---

    public function test_getEligibleMembers_only_returns_eligible_users(): void
    {
        $this->actingAs($this->admin);

        $membro = $this->makeUser('membro');
        $supervisor = $this->makeUser('supervisor');
        $jaNaCelula = $this->makeUser('membro', $this->cellMembros);

        $response = $this->getJson(route('cells.eligible-members', $this->cellMembros));

        $response->assertOk();
        $ids = collect($response->json())->pluck('id')->all();
        $this->assertContains($membro->id, $ids);
        $this->assertNotContains($supervisor->id, $ids);
        $this->assertNotContains($jaNaCelula->id, $ids);
    }

    public function test_addMember_moves_user_and_returns_success(): void
    {
        $this->actingAs($this->admin);

        $membro = $this->makeUser('membro');

        $this->post(route('cells.add-member', $this->cellMembros), ['member_id' => $membro->id, 'role_in_cell' => 'membro'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $membro->id, 'cell_id' => $this->cellMembros->id]);
    }

    public function test_addMember_rejects_incompatible_role_with_error(): void
    {
        $this->actingAs($this->admin);

        $supervisor = $this->makeUser('supervisor');

        $this->post(route('cells.add-member', $this->cellMembros), ['member_id' => $supervisor->id, 'role_in_cell' => 'membro'])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('users', ['id' => $supervisor->id, 'cell_id' => $this->cellMembros->id]);
    }

    public function test_create_new_member_button_flow_still_works(): void
    {
        // O botão "Adicionar Membro" existente (que cria um novo membro) continua funcional.
        $this->actingAs($this->admin);

        $this->get(route('members.create').'?cell_id='.$this->cellMembros->id)->assertOk();
    }
}

