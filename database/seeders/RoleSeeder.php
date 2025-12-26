<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@church.com'],
            [
                'name' => 'Administrador Geral',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $pastor = User::updateOrCreate(
            ['email' => 'pastor@church.com'],
            [
                'name' => 'Pastor Principal',
                'password' => Hash::make('password'),
                'role' => 'pastor',
                'is_active' => true,
            ]
        );

        // Adicionar outros usuários de teste se necessário
    }
}
