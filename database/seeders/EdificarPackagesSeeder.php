<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommitmentPackage;

class EdificarPackagesSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Pacote 1',
                'min_amount' => 10,
                'max_amount' => 1400,
                'description' => 'Contribuições de 10 a 1400 MT',
                'order' => 1,
                'whatsapp_link' => 'https://chat.whatsapp.com/example-group-1'
            ],
            [
                'name' => 'Pacote 2',
                'min_amount' => 1500,
                'max_amount' => 1500,
                'description' => 'Contribuição de 1500 MT',
                'order' => 2,
                'whatsapp_link' => 'https://chat.whatsapp.com/example-group-2'
            ],
            [
                'name' => 'Pacote 3',
                'min_amount' => 2500,
                'max_amount' => 2500,
                'description' => 'Contribuição de 2500 MT',
                'order' => 3,
                'whatsapp_link' => 'https://chat.whatsapp.com/example-group-3'
            ],
            [
                'name' => 'Pacote 4',
                'min_amount' => 5000,
                'max_amount' => 5000,
                'description' => 'Contribuição de 5000 MT',
                'order' => 4,
                'whatsapp_link' => 'https://chat.whatsapp.com/example-group-4'
            ],
            [
                'name' => 'Pacote 5',
                'min_amount' => 10000,
                'max_amount' => null,
                'description' => 'Contribuição de 10.000 MT ao infinito',
                'order' => 5,
                'whatsapp_link' => 'https://chat.whatsapp.com/example-group-5'
            ],
        ];

        foreach ($packages as $pkg) {
            CommitmentPackage::updateOrCreate(
                ['name' => $pkg['name']],
                $pkg
            );
        }
    }
}
