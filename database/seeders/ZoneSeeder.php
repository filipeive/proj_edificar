<?php
namespace Database\Seeders;

use App\Models\Cell;
use App\Models\CommitmentPackage; // Importado
use App\Models\Supervision;
use App\Models\User;
use App\Models\UserCommitment;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ZoneSeeder extends Seeder {
    public function run(): void {
        
        // Garante que o pacote existe para que a FK não falhe
        $packages = CommitmentPackage::all();
        if ($packages->isEmpty()) {
            $this->call(CommitmentPackageSeeder::class);
            $packages = CommitmentPackage::all();
        }

        // Criar 2 zonas de teste
        $zones = [
            ['name' => 'Zona Centro', 'description' => 'Centro da Cidade'],
            ['name' => 'Zona Norte', 'description' => 'Bairro Norte'],
        ];

        foreach ($zones as $zoneData) {
            $zone = Zone::create($zoneData);

            // Cada zona tem 2 supervisões
            for ($s = 1; $s <= 2; $s++) {
                $supervision = Supervision::create([
                    'zone_id' => $zone->id,
                    'name' => "{$zone->name} - Supervisão {$s}",
                    'description' => "Supervisão {$s} da {$zone->name}",
                ]);

                // Criar supervisor (membro especial)
                $supervisor = User::create([
                    'name' => "Pastor {$zone->name} - Sup{$s}",
                    'email' => "supervisor_{$zone->id}_{$s}@chiesa.local",
                    'password' => bcrypt('123456'),
                    'phone' => '82356200' . rand(100, 999),
                    'role' => 'supervisor',
                    'is_active' => true,
                    // cell_id ainda é NULL aqui. Vamos atribuir depois.
                ]);
                
                // --- Supervisor Precisa de uma Célula para ter Compromisso ---
                // Para ter um compromisso válido (com cell_id), o supervisor precisa estar em uma célula.
                // Criamos uma célula placeholder para o supervisor (se for a regra de negócio)
                $supervisorCell = Cell::create([
                    'name' => "Célula Supervisor S{$s}",
                    'supervision_id' => $supervision->id,
                    'leader_id' => $supervisor->id,
                ]);
                $supervisor->update(['cell_id' => $supervisorCell->id]);
                $supervisorCell->update(['member_count' => 1]); // Ele é o único membro dela por enquanto

                // Atribuir compromisso ao supervisor
                $committedAmountSupervisor = 2500.00;
                UserCommitment::create([
                    'user_id' => $supervisor->id,
                    'package_id' => 3, // Pacote 3
                    'start_date' => now()->startOfMonth(),
                    'committed_amount' => $committedAmountSupervisor, // NOVO: Campo obrigatório
                    'cell_id' => $supervisorCell->id, // NOVO: Campo obrigatório
                ]);

                // Cada supervisão tem 2 células
                for ($c = 1; $c <= 2; $c++) {
                    // Criar líder de célula
                    $leader = User::create([
                        'name' => "Líder {$zone->name} - S{$s}C{$c}",
                        'email' => "lider_{$zone->id}_{$s}_{$c}@chiesa.local",
                        'password' => bcrypt('123456'),
                        'phone' => '82356200' . rand(100, 999),
                        'role' => 'lider_celula',
                        'is_active' => true,
                    ]);

                    // Criar célula
                    $cell = Cell::create([
                        'name' => "Célula {$zone->name} - S{$s}C{$c}",
                        'supervision_id' => $supervision->id,
                        'leader_id' => $leader->id,
                    ]);

                    // Atribuir líder à sua célula
                    $leader->update(['cell_id' => $cell->id]);

                    // Atribuir compromisso ao líder
                    $committedAmountLeader = 1500.00;
                    UserCommitment::create([
                        'user_id' => $leader->id,
                        'package_id' => 2, // Pacote 2
                        'start_date' => now()->startOfMonth(),
                        'committed_amount' => $committedAmountLeader, // NOVO: Campo obrigatório
                        'cell_id' => $cell->id, // NOVO: Campo obrigatório
                    ]);

                    // Criar 4 membros por célula
                    for ($m = 1; $m <= 4; $m++) {
                        $member = User::create([
                            'name' => "Membro {$zone->name}-S{$s}C{$c}-M{$m}",
                            'email' => "membro_{$zone->id}_{$s}_{$c}_{$m}@chiesa.local",
                            'password' => bcrypt('123456'),
                            'phone' => '82356200' . rand(100, 999),
                            'role' => 'membro',
                            'cell_id' => $cell->id, // cell_id é definido aqui
                            'is_active' => true,
                        ]);

                        // Atribuir compromisso aleatório
                        $packageId = rand(1, count($packages));
                        $committedAmountMember = rand(500, 1000); // Valor aleatório de exemplo
                        
                        UserCommitment::create([
                            'user_id' => $member->id,
                            'package_id' => $packageId,
                            'start_date' => now()->startOfMonth()->subMonth(rand(0, 3)),
                            'committed_amount' => $committedAmountMember, // NOVO: Campo obrigatório
                            'cell_id' => $cell->id, // NOVO: Campo obrigatório
                        ]);
                    }

                    $cell->update(['member_count' => $cell->getMembersCount()]);
                }
            }
        }

        // Criar admin (no final)
        User::firstOrCreate(
            ['email' => 'admin@chiesa.local'],
            [
                'name' => 'Admin Projeto Edificar',
                'password' => Hash::make('password'),
                'phone' => '823562000',
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}