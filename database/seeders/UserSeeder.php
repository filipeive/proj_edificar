<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Criar usuário admin principal
        User::updateOrCreate(['email' => 'admin@chiesa.local'], [
            'name' => 'Administrador Portal Life Church',
            'password' => bcrypt('123456'),
            'phone' => '823562000',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Criar usuário pastor de zona (teste)
        User::updateOrCreate(['email' => 'pastor@chiesa.local'], [
            'name' => 'Pastor da Zona Centro',
            'password' => bcrypt('123456'),
            'phone' => '823562001',
            'role' => 'pastor_zona',
            'is_active' => true,
        ]);

        // criar usuário supervisor (teste)
        User::updateOrCreate(['email' => 'supervisor@chiesa.local'], [
            'name' => 'Supervisor da Zona Centro',
            'password' => bcrypt('123456'),
            'phone' => '823562002',
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        // Criar usuário membro comum (teste)
        User::updateOrCreate(['email' => 'membro@chiesa.local'], [
            'name' => 'Membro Comum',
            'password' => bcrypt('123456'),
            'phone' => '823562003',
            'role' => 'membro',
            'is_active' => true,
        ]);

    }
}