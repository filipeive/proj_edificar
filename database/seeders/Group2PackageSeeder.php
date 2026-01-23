<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CommitmentPackage;
use App\Models\UserCommitment;
use App\Models\Zone;
use App\Models\Supervision;
use App\Models\Cell;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Group2PackageSeeder extends Seeder
{
    public function run()
    {
        $packageId = 4; // Pacote 5000
        $responsibleId = 61; // Gervasia
        $startDate = Carbon::create(2026, 1, 1);

        // 1. Register Zones
        $newZones = ['117 de Setembro', 'Aeroporto', 'Janeiro', 'Coalane'];
        foreach ($newZones as $zoneName) {
            Zone::firstOrCreate(['name' => $zoneName]);
            $this->command->info("Zona registrada: $zoneName");
        }

        // 2. Update Package Responsible
        $package = CommitmentPackage::find($packageId);
        if ($package) {
            $package->update(['responsible_id' => $responsibleId]);
            $this->command->info("Pacote {$package->name} agora é responsabilidade de Gervasia (ID: $responsibleId).");
        }

        // 3. Find or Create Temporary Cell
        // We'll look for an existing supervision or create one for the temporary cell
        $zone = Zone::where('name', 'Aeroporto')->first() ?? Zone::first();
        $supervision = Supervision::firstOrCreate(
            ['name' => 'Supervisão Geral'],
            ['zone_id' => $zone->id]
        );

        $tempCell = Cell::firstOrCreate(
            ['name' => 'Célula Temporária Grupo 2'],
            ['supervision_id' => $supervision->id]
        );
        $this->command->info("Usando célula temporária: {$tempCell->name}");

        // 4. List extracted from PDF
        $members = [
            ['name' => 'Casal Saide', 'phone' => '878896437'],
            ['name' => 'Casal Paulo Ernesto', 'phone' => '863035584'],
            ['name' => 'Casal Pastor Luis', 'phone' => '847716998'],
            ['name' => 'Casal Carlos Jorge', 'phone' => '874720519'],
            ['name' => 'Casal Cremilgo Agnelia', 'phone' => '840336963'],
            ['name' => 'Casal Edson Zunguze', 'phone' => '842670681'],
            ['name' => 'Casal Filipe dos Santos', 'phone' => '862134230'],
            ['name' => 'Casal Nando Meia', 'phone' => '849125254'],
            ['name' => 'Casal Manuel Assuncao', 'phone' => '847220686'],
            ['name' => 'Casal Manuel Novaldina', 'phone' => '866973738'],
            ['name' => 'Casal Ataide', 'phone' => '822349760'],
            ['name' => 'Casal Rogerio Nipato', 'phone' => '844960409'],
            ['name' => 'Casal Carlos Afilhado de Dercio', 'phone' => '842211618'],
            ['name' => 'Casal Bispo', 'phone' => '845833325'],
            ['name' => 'Casal Danny Raso', 'phone' => '849283949'],
            ['name' => 'Casal Elisio', 'phone' => '872704020'],
            ['name' => 'Casal Dany Arquiteto', 'phone' => '874034448'],
            ['name' => 'Casal Helio', 'phone' => '845344427'],
            ['name' => 'Casal Bertil', 'phone' => '834256787'],
            ['name' => 'Casal Castigo Antonio', 'phone' => '873985657'],
            ['name' => 'Casal Julio Mudubai', 'phone' => '866951817'],
            ['name' => 'Suzana Libelela', 'phone' => '875283069'],
            ['name' => 'Sonia Matinada', 'phone' => '878638391'],
            ['name' => 'Agness', 'phone' => '829527797'],
            ['name' => 'Mila', 'phone' => '861400614'],
            ['name' => 'Casal Araujo Cesar', 'phone' => '848871940'],
            ['name' => 'Sheila Russo', 'phone' => null],
            ['name' => 'Rafaela', 'phone' => null],
            ['name' => 'Joy Mwahiri', 'phone' => null],
        ];

        foreach ($members as $data) {
            // Find or Create User
            $user = User::where('name', 'like', '%' . $data['name'] . '%')->first();

            if (!$user) {
                $email = Str::slug($data['name']) . '@edificar.com';
                if (User::where('email', $email)->exists()) {
                    $email = Str::slug($data['name']) . rand(10, 99) . '@edificar.com';
                }

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $email,
                    'phone' => $data['phone'],
                    'password' => Hash::make('mudar123'),
                    'role' => 'membro',
                    'cell_id' => $tempCell->id,
                    'is_active' => true,
                ]);
                $this->command->info("Novo utilizador criado: {$user->name}");
            } else {
                $this->command->info("Utilizador encontrado: {$user->name}");
                if (!$user->cell_id) {
                    $user->update(['cell_id' => $tempCell->id]);
                    $this->command->info("  → Célula atualizada para utilizador existente.");
                }
            }

            // Create or Update Commitment
            UserCommitment::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'package_id' => $packageId,
                ],
                [
                    'cell_id' => $user->cell_id,
                    'committed_amount' => 5000,
                    'start_date' => $startDate,
                ]
            );
            $this->command->info("  → Compromisso de 5000 registrado.");
        }
    }
}
