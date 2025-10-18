<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
    public function run(): void {
        // Criar usuário admin principal
        User::create([
            'name' => 'Administrador Projeto Edificar',
            'email' => 'admin@chiesa.local',
            'password' => bcrypt('123456'),
            'phone' => '823562000',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Criar usuário pastor de zona (teste)
        User::create([
            'name' => 'Pastor da Zona Centro',
            'email' => 'pastor@chiesa.local',
            'password' => bcrypt('123456'),
            'phone' => '823562001',
            'role' => 'pastor_zona',
            'is_active' => true,
        ]);

        // criar usuário supervisor (teste)
        User::create([
            'name' => 'Supervisor da Zona Centro',
            'email' => 'supervisor@chiesa.local',
            'password' => bcrypt('123456'),
            'phone' => '823562002',
            'role' => 'supervisor',
            'is_active' => true,
        ]);


        // Criar usuário membro comum (teste)
        User::create([
            'name' => 'Membro Comum',
            'email' => 'membro  @chiesa.local',
            'password' => bcrypt('123456'),
            'phone' => '823562002',
            'role' => 'membro',
            'is_active' => true,
        ]);

    }
}