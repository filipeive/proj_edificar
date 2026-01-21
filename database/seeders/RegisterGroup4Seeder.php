<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CommitmentPackage;
use App\Models\UserCommitment;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RegisterGroup4Seeder extends Seeder
{
    public function run()
    {
        $packageId = 2; // Pacote 1500
        $responsibleId = 50; // Filipe
        $startDate = Carbon::create(2026, 1, 1);

        // 1. Assign Filipe as Responsible for Package 1500
        $package = CommitmentPackage::find($packageId);
        if ($package) {
            $package->update(['responsible_id' => $responsibleId]);
            $this->command->info("Packote {$package->name} agora é responsabilidade de Filipe (ID: $responsibleId).");
        }

        // List extracted from PDF
        $members = [
            ['name' => 'Ângela Nobre', 'phone' => '862397541'],
            ['name' => 'Bete Manuel', 'phone' => '874677959'],
            ['name' => 'Celino Jorge', 'phone' => null],
            ['name' => 'Dara Jose', 'phone' => null],
            ['name' => 'Chanda Massande', 'phone' => '848720372'],
            ['name' => 'Chande', 'phone' => '845754950'],
            ['name' => 'Colaco Fernando', 'phone' => null],
            ['name' => 'Egas Ernesto', 'phone' => '840270670'],
            ['name' => 'Erneo Somaso', 'phone' => '866204329'],
            ['name' => 'Esteves Eusebio', 'phone' => null],
            ['name' => 'Familia Camacho', 'phone' => '844419945'],
            ['name' => 'Fernando Mafuca', 'phone' => '842006890'],
            ['name' => 'Helton Francisco', 'phone' => '840252519'],
            ['name' => 'Jacinto Francisco', 'phone' => null],
            ['name' => 'Luciano Miguel', 'phone' => '855206273'],
            ['name' => 'Marcelo', 'phone' => '845210577'],
            ['name' => 'Waissone Maluate', 'phone' => '873939863'],
            ['name' => 'Bosque', 'phone' => '873723304'],
            ['name' => 'Jaime', 'phone' => '846031015'],
            ['name' => 'Wilsone', 'phone' => '842755701'],
            ['name' => 'Alice Machaieie', 'phone' => '849196960'],
            ['name' => 'Marta Uamusse', 'phone' => '863775207'],
            ['name' => 'Ana Cristina', 'phone' => '849283967'],
            ['name' => 'Celso Gualhardo', 'phone' => '878261161'],
        ];

        foreach ($members as $data) {
            // Find or Create User
            $query = User::query();

            if ($data['phone']) {
                $query->where('phone', 'like', '%' . substr($data['phone'], -9));
            } else {
                $query->where('name', $data['name']);
            }

            $user = $query->first();

            if (!$user) {
                // Generate unique email if needed
                $slug = \Str::slug($data['name']);
                $email = $slug . '@edificar.com'; // Placeholder

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $email,
                    'phone' => $data['phone'],
                    'password' => Hash::make('mudar123'), // Default password
                    'role' => 'membro',
                    'is_active' => true,
                ]);
                $this->command->info("Novo utilizador criado: {$user->name}");
            } else {
                $this->command->info("Utilizador encontrado: {$user->name}");
            }

            // Ensure user has a cell_id (required for commitments)
            if (!$user->cell_id) {
                // Assign to first available cell as default
                $defaultCell = \App\Models\Cell::first();
                if ($defaultCell) {
                    $user->update(['cell_id' => $defaultCell->id]);
                    $this->command->info("  → Atribuído à célula: {$defaultCell->name}");
                } else {
                    $this->command->warn("  → Sem célula disponível, pulando compromisso");
                    continue;
                }
            }

            // Create Commitment
            UserCommitment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'package_id' => $packageId,
                    'start_date' => $startDate,
                ],
                [
                    'cell_id' => $user->cell_id,
                    'committed_amount' => 1500
                ]
            );
        }
    }
}
