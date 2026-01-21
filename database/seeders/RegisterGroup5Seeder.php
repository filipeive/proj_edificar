<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CommitmentPackage;
use App\Models\UserCommitment;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RegisterGroup5Seeder extends Seeder
{
    public function run()
    {
        $packageId = 1; // Pacote 500
        $responsibleId = 49; // Paulo Lucas
        $startDate = Carbon::create(2026, 1, 1);

        // 1. Assign Paulo as Responsible for Package 500
        $package = CommitmentPackage::find($packageId);
        if ($package) {
            $package->update(['responsible_id' => $responsibleId]);
            $this->command->info("Pacote {$package->name} agora é responsabilidade de Paulo Lucas (ID: $responsibleId).");
        }

        // List extracted from PDF - 54 members
        $members = [
            ['name' => 'Rachida Francisco', 'phone' => '878343998'],
            ['name' => 'Telma Pedro', 'phone' => '870942153'],
            ['name' => 'Sara Pascoal', 'phone' => '869384006'],
            ['name' => 'Luisa Bernardo', 'phone' => '871393714'],
            ['name' => 'Jéssica Massas', 'phone' => '875978845'],
            ['name' => 'Minjurda Cesar', 'phone' => '879898033'],
            ['name' => 'Felizardo Augusto', 'phone' => '860653160'],
            ['name' => 'Linda Antonio', 'phone' => null],
            ['name' => 'Chababe Fernando', 'phone' => '877033238'],
            ['name' => 'Zamira Ramos', 'phone' => '874639608'],
            ['name' => 'Manuel Raimundo', 'phone' => '845265249'],
            ['name' => 'Siloia Henriques', 'phone' => null],
            ['name' => 'Ana Omar', 'phone' => '872090508'],
            ['name' => 'Helena Simoes', 'phone' => '874309984'],
            ['name' => 'Anastacia Tomas', 'phone' => '878973646'],
            ['name' => 'Arize Fernando', 'phone' => '871055768'],
            ['name' => 'Dino Cisinio', 'phone' => '842569384'],
            ['name' => 'Nelson Alberto Viegas', 'phone' => '867112964'],
            ['name' => 'Jonito Felizardo Augusto', 'phone' => '876071582'],
            ['name' => 'Benjamim Filipe Virgílio', 'phone' => '869972086'],
            ['name' => 'Constantin Sérgio Da Costa', 'phone' => '844196424'],
            ['name' => 'Olinda', 'phone' => '852857776'],
            ['name' => 'Eulália Domingos', 'phone' => '873902534'],
            ['name' => 'Tionisia Zito', 'phone' => '862296810'],
            ['name' => 'Irene Gomes', 'phone' => '870517304'],
            ['name' => 'Bete Alberto', 'phone' => '870247538'],
            ['name' => 'Maiza Muloia', 'phone' => null],
            ['name' => 'Felicidade Carlos', 'phone' => '855672829'],
            ['name' => 'Carlos Dionisio', 'phone' => '874491639'],
            ['name' => 'Leticia Estevao', 'phone' => '865267396'],
            ['name' => 'Sara Manito', 'phone' => '874826009'],
            ['name' => 'Tania Miguel', 'phone' => '873590198'],
            ['name' => 'Isa Papel', 'phone' => '874558607'],
            ['name' => 'Manucha', 'phone' => '870741036'],
            ['name' => 'Betlita Rene', 'phone' => null],
            ['name' => 'Didiane', 'phone' => '875997207'],
            ['name' => 'Claudia', 'phone' => '864233190'],
            ['name' => 'Leny e Chirona', 'phone' => '867528989'],
            ['name' => 'Janeite Teteneia', 'phone' => '862580414'],
            ['name' => 'Eugenia Carlitos', 'phone' => '879103376'],
            ['name' => 'Jamila Raimundo', 'phone' => '869806859'],
            ['name' => 'Agnalda Jose', 'phone' => '863414226'],
            ['name' => 'Anchia', 'phone' => '867472238'],
            ['name' => 'Amisse e Eufrasia', 'phone' => '875941215'],
            ['name' => 'Alexandre e Janete', 'phone' => '860407497'],
            ['name' => 'Simon', 'phone' => '878029736'],
            ['name' => 'Nito Dias', 'phone' => '842187657'],
            ['name' => 'Ofelia Miguel', 'phone' => '841653778'],
            ['name' => 'Eugenio', 'phone' => '849680421'],
            ['name' => 'Nico Francisco', 'phone' => '861004777'],
            ['name' => 'Caldina', 'phone' => '877215653'],
            ['name' => 'Erica', 'phone' => '874717506'],
            ['name' => 'Rosa', 'phone' => '872485077'],
            ['name' => 'Laurinda', 'phone' => '870020632'],
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
                // Generate unique email
                $slug = \Str::slug($data['name']);
                $email = $slug . '@edificar.com';

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $email,
                    'phone' => $data['phone'],
                    'password' => Hash::make('mudar123'),
                    'role' => 'membro',
                    'is_active' => true,
                ]);
                $this->command->info("Novo utilizador criado: {$user->name}");
            } else {
                $this->command->info("Utilizador encontrado: {$user->name}");
            }

            // Ensure user has a cell_id
            if (!$user->cell_id) {
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
                    'committed_amount' => 500
                ]
            );
        }

        $totalMembers = count($members);
        $this->command->info("\n✅ Total de {$totalMembers} membros registrados no Pacote 500!");
    }
}
