<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CommitmentPackage;
use App\Models\UserCommitment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegisterGroup3Seeder extends Seeder
{
    public function run()
    {
        $packageId = 3; // Pacote 2500
        $responsibleId = app()->environment('production') ? 226 : 255; // Irmã Telma (prod: #00226, local: #00255)
        $startDate = Carbon::create(2026, 1, 1);

        // 1. Assign Responsible to Package 2500 (if provided)
        if ($responsibleId) {
            $package = CommitmentPackage::find($packageId);
            if ($package) {
                $package->update(['responsible_id' => $responsibleId]);
                $this->command->info("Pacote {$package->name} agora é responsabilidade de Telma (ID: $responsibleId).");
            }
        }

        // List extracted from Grupo 3.docx
        $members = [
            ['name' => 'Agnalda Nomeada', 'phone' => '843507769'],
            ['name' => 'Ainda Lingula', 'phone' => '864438849'],
            ['name' => 'Anapaula Mario', 'phone' => '845626879'],
            ['name' => 'Anatalia Lopes', 'phone' => null],
            ['name' => 'Anita', 'phone' => '862083948'],
            ['name' => 'Arcanjo Joao', 'phone' => '870708740'],
            ['name' => 'Armando Tayobo', 'phone' => '843880305'],
            ['name' => 'Asia e Baptista', 'phone' => '853369781'],
            ['name' => 'Betina Joao', 'phone' => '847795970'],
            ['name' => 'Caridade Vachaneque', 'phone' => '845300280'],
            ['name' => 'Carolina Manjor', 'phone' => '843791859'],
            ['name' => 'Casal Baltazar', 'phone' => '867899067'],
            ['name' => 'Casal Bendito', 'phone' => null],
            ['name' => 'Casal Carlos Mulaleia', 'phone' => '846306707'],
            ['name' => 'Casal Francisco Telma', 'phone' => '845177421'],
            ['name' => 'Casal Santos', 'phone' => null],
            ['name' => 'Casal Sergio Zaina', 'phone' => '842983398'],
            ['name' => 'Casal Tino', 'phone' => '844196424'],
            ['name' => 'Cesar Augusto', 'phone' => '848996088'],
            ['name' => 'Chinedu Mwahiri', 'phone' => null],
            ['name' => 'Dercio Jamal', 'phone' => '845693440'],
            ['name' => 'Ditosa B. Tamele', 'phone' => '842243300'],
            ['name' => 'Elise Araujo', 'phone' => '875888566'],
            ['name' => 'Eugenia Joao', 'phone' => '855811619'],
            ['name' => 'Fatima Eduardo', 'phone' => '857696395'],
            ['name' => 'Helio Manjor', 'phone' => '845344427'],
            ['name' => 'Hermenegilda de Sousa', 'phone' => '845430541'],
            ['name' => 'Hortencia Agustinho', 'phone' => '843702601'],
            ['name' => 'Inacio Victor', 'phone' => '847059452'],
            ['name' => 'Inocencio Pequenino', 'phone' => '846619044'],
            ['name' => 'Jacinta Antonio', 'phone' => '845204676'],
            ['name' => 'Joana Joaquim', 'phone' => '844158772'],
            ['name' => 'Joao Francisco', 'phone' => '840420962'],
            ['name' => 'Judite Bone', 'phone' => '842270982'],
            ['name' => 'Laura Armando', 'phone' => '847387493'],
            ['name' => 'Laura Fiscal', 'phone' => null],
            ['name' => 'Leinece Chikakuda', 'phone' => '858001698'],
            ['name' => 'Leticia Estevao', 'phone' => '847708487'],
            ['name' => 'Lucinda Benedito', 'phone' => '846039464'],
            ['name' => 'Luis Elioterio', 'phone' => '846039864'],
            ['name' => 'Luisa Domingos', 'phone' => '847177766'],
            ['name' => 'Luisa Nazare', 'phone' => '841831421'],
            ['name' => 'Maiza Paz', 'phone' => null],
            ['name' => 'Manuel Joao', 'phone' => '842017610'],
            ['name' => 'Margarethe Matavele', 'phone' => '878975191'],
            ['name' => 'Maria do Ceu', 'phone' => '845540511'],
            ['name' => 'Maria Helena', 'phone' => '846360328'],
            ['name' => 'Marta Eduardo', 'phone' => '842406292'],
            ['name' => 'Micaela Figueredo & Tayobo', 'phone' => '845748206'],
            ['name' => 'Miria Jose', 'phone' => '845548807'],
            ['name' => 'Neide Tarela', 'phone' => '846942795'],
            ['name' => 'Nelita Rosario', 'phone' => '874348783'],
            ['name' => 'Palmira Liginha', 'phone' => '840235284'],
            ['name' => 'Raimundo Luis & Assia', 'phone' => '847082127'],
            ['name' => 'Reigone Oliveira', 'phone' => '866729727'],
            ['name' => 'Ricardino Ricardo', 'phone' => '850265118'],
            ['name' => 'Rosaria Moreira', 'phone' => '845663376'],
            ['name' => 'Sandra Mariza', 'phone' => '845238656'],
            ['name' => 'Silvio Lauce', 'phone' => '847242835'],
            ['name' => 'Sina Schers', 'phone' => '879066348'],
            ['name' => 'Tomasia', 'phone' => '849119819'],
            ['name' => 'Vania Andre', 'phone' => '845583556'],
            ['name' => 'Vania Martinho', 'phone' => '847771165'],
            ['name' => 'Vania Silvino', 'phone' => '843975791'],
            ['name' => 'Magide', 'phone' => '841488281'],
            ['name' => 'Celso Galhardo', 'phone' => '878261161'],
            ['name' => 'Rafael & Maria', 'phone' => '843760479'],
            ['name' => 'Joaquim & Mery', 'phone' => '878305395'],
            ['name' => 'Herminio Sozinho', 'phone' => '844355431'],
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
                $slug = Str::slug($data['name']);
                $email = $slug . '@edificar.com';
                if (User::where('email', $email)->exists()) {
                    $email = $slug . rand(10, 99) . '@edificar.com';
                }

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

            // Ensure user has a cell_id (required for commitments)
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
                    'committed_amount' => 2500,
                ]
            );
        }
    }
}
