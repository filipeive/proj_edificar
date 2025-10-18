<?php
namespace Database\Seeders;
use App\Models\CommitmentPackage;
use Illuminate\Database\Seeder;

class CommitmentPackageSeeder extends Seeder {
    public function run(): void {
        $packages = [
            [
                'name' => 'Pacote 1',
                'min_amount' => 10,
                'max_amount' => 250,
                'description' => 'Entrada - Até 250 MT',
                'order' => 1,
            ],
            [
                'name' => 'Pacote 2',
                'min_amount' => 250,
                'max_amount' => 500,
                'description' => 'Intermediário - De 250 a 500 MT',
                'order' => 2,
            ],
            [
                'name' => 'Pacote 3',
                'min_amount' => 500,
                'max_amount' => 1000,
                'description' => 'Intermediário-Alto - De 500 a 1000 MT',
                'order' => 3,
            ],
            [
                'name' => 'Pacote 4',
                'min_amount' => 1000,
                'max_amount' => 2000,
                'description' => 'Alto - De 1000 a 2000 MT',
                'order' => 4,
            ],
            [
                'name' => 'Pacote 5',
                'min_amount' => 2000,
                'max_amount' => null,
                'description' => 'Visionário - Acima de 2000 MT (sem limite)',
                'order' => 5,
            ],
        ];

        foreach ($packages as $package) {
            CommitmentPackage::create($package);
        }
    }
}
