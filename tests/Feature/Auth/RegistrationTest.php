<?php

namespace Tests\Feature\Auth;

use App\Models\Cell;
use App\Models\Supervision;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        // O formulário de registro carrega células.
        Zone::create(['name' => 'Zona Teste']);
        $zone = Zone::firstOrFail();
        $supervision = Supervision::create(['name' => 'Supervisao Teste', 'zone_id' => $zone->id]);
        Cell::create(['name' => 'Celula Teste', 'supervision_id' => $supervision->id]);

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $zone = Zone::create(['name' => 'Zona Teste']);
        $supervision = Supervision::create(['name' => 'Supervisao Teste', 'zone_id' => $zone->id]);
        $cell = Cell::create(['name' => 'Celula Teste', 'supervision_id' => $supervision->id]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '841234567',
            'cell_id' => $cell->id,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard.membro', absolute: false));
    }
}
