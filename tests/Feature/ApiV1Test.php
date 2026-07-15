<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cell;
use App\Models\Supervision;
use App\Models\Zone;
use App\Models\Contribution;
use App\Models\Requisition;
use App\Models\Wedding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $memberUser;
    protected Cell $cell;
    protected Supervision $supervision;
    protected Zone $zone;

    protected function setUp(): void
    {
        parent::setUp();

        // Create hierarchy
        $this->zone = Zone::create(['name' => 'Test Zone']);
        $this->supervision = Supervision::create([
            'name' => 'Test Supervision',
            'zone_id' => $this->zone->id
        ]);
        $this->cell = Cell::create([
            'name' => 'Test Cell',
            'supervision_id' => $this->supervision->id
        ]);

        // Create Admin user
        $this->adminUser = User::create([
            'name' => 'Test Admin',
            'email' => 'admin_test_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
            'cell_id' => $this->cell->id,
        ]);

        // Create Member user
        $this->memberUser = User::create([
            'name' => 'Test Member',
            'email' => 'member_test_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'membro',
            'is_active' => true,
            'cell_id' => $this->cell->id,
        ]);
    }

    /** @test */
    public function it_can_authenticate_a_user_and_access_endpoints()
    {
        // 1. Unauthenticated request to profile should fail
        $response = $this->getJson('/api/v1/profile');
        $response->assertStatus(401);

        // 2. Login request
        $loginResponse = $this->postJson('/api/v1/login', [
            'email' => $this->adminUser->email,
            'password' => 'password123',
            'device_name' => 'Test Device'
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email']
                ]
            ]);

        $token = $loginResponse->json('data.token');

        // 3. Authenticated request to profile
        $profileResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/profile');

        $profileResponse->assertStatus(200)
            ->assertJsonPath('data.email', $this->adminUser->email);

        // 4. Authenticated request to logout
        $logoutResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/v1/logout');

        $logoutResponse->assertStatus(200);

        // Clear local memory cache of resolved auth user
        auth()->forgetUser();
        \Illuminate\Support\Facades\Auth::forgetUser();

        // 5. Subsequent request should fail
        $subsequentResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/v1/profile');
        $subsequentResponse->assertStatus(401);
    }

    /** @test */
    public function it_can_fetch_dashboard_metrics()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/v1/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_members',
                    'total_cells',
                    'total_supervisions',
                    'total_zones',
                    'recent_services'
                ]
            ]);
    }

    /** @test */
    public function it_can_perform_members_crud()
    {
        // Index
        $indexResponse = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/v1/members');
        $indexResponse->assertStatus(200);

        // Create
        $email = 'new_member_' . uniqid() . '@example.com';
        $createResponse = $this->actingAs($this->adminUser, 'sanctum')->postJson('/api/v1/members', [
            'name' => 'New Member API',
            'email' => $email,
            'password' => 'password123',
            'role' => 'membro',
            'cell_id' => $this->cell->id,
            'is_active' => true
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.name', 'New Member API');

        $memberId = $createResponse->json('data.id');

        // Show
        $showResponse = $this->actingAs($this->adminUser, 'sanctum')->getJson('/api/v1/members/' . $memberId);
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.email', $email);

        // Update
        $updateResponse = $this->actingAs($this->adminUser, 'sanctum')->putJson('/api/v1/members/' . $memberId, [
            'name' => 'Updated Member API'
        ]);
        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Member API');

        // Delete
        $deleteResponse = $this->actingAs($this->adminUser, 'sanctum')->deleteJson('/api/v1/members/' . $memberId);
        $deleteResponse->assertStatus(200);
    }

    /** @test */
    public function it_can_transfer_a_member_between_cells()
    {
        $newCell = Cell::create([
            'name' => 'Another Cell',
            'supervision_id' => $this->supervision->id
        ]);

        $transferResponse = $this->actingAs($this->adminUser, 'sanctum')->postJson('/api/v1/cells/transfer-member', [
            'member_id' => $this->memberUser->id,
            'cell_id' => $newCell->id
        ]);

        $transferResponse->assertStatus(200);

        $this->assertEquals($newCell->id, $this->memberUser->fresh()->cell_id);
    }

    /** @test */
    public function it_can_manage_contributions_and_verify_them()
    {
        // 1. Create contribution
        $createResponse = $this->actingAs($this->memberUser, 'sanctum')->postJson('/api/v1/contributions', [
            'amount' => 500.00,
            'contribution_date' => now()->toDateString(),
            'notes' => 'Tithe contribution'
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.status', 'pendente');

        $contributionId = $createResponse->json('data.id');

        // 2. Verify contribution as Admin
        $verifyResponse = $this->actingAs($this->adminUser, 'sanctum')->postJson("/api/v1/contributions/{$contributionId}/verify");
        $verifyResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'verificada');
    }

    /** @test */
    public function it_can_manage_requisitions_and_approve_them()
    {
        // 1. Create requisition
        $createResponse = $this->actingAs($this->memberUser, 'sanctum')->postJson('/api/v1/requisitions', [
            'amount' => 1200.00,
            'description' => 'Buying chairs',
            'category' => 'infraestrutura',
            'scope' => 'regular'
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.status', Requisition::STATUS_PENDING);

        $requisitionId = $createResponse->json('data.id');

        // 2. Approve requisition as Admin
        $approveResponse = $this->actingAs($this->adminUser, 'sanctum')->postJson("/api/v1/requisitions/{$requisitionId}/approve");
        $approveResponse->assertStatus(200)
            ->assertJsonPath('data.status', Requisition::STATUS_APPROVED);
    }

    /** @test */
    public function it_can_manage_weddings()
    {
        $createResponse = $this->actingAs($this->adminUser, 'sanctum')->postJson('/api/v1/weddings', [
            'groom_name' => 'John Doe',
            'bride_name' => 'Jane Smith',
            'date' => now()->addDays(30)->toDateString(),
            'time' => '14:00:00',
            'location' => 'Central Temple',
            'status' => 'scheduled'
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.groom_name', 'John Doe');
    }
}
