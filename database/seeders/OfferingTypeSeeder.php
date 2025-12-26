<?php

namespace Database\Seeders;

use App\Models\OfferingType;
use Illuminate\Database\Seeder;

class OfferingTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Dízimos',
                'code' => 'tithes',
                'description' => 'Dízimos regulares',
                'order' => 1,
            ],
            [
                'name' => 'Ofertas',
                'code' => 'offerings',
                'description' => 'Ofertas regulares',
                'order' => 2,
            ],
            [
                'name' => 'Ofertas Especiais',
                'code' => 'special_offerings',
                'description' => 'Ofertas especiais do dia',
                'order' => 3,
            ],
            [
                'name' => 'Portal Life Church',
                'code' => 'edificar',
                'description' => 'Contribuições para o Portal Life Church',
                'order' => 4,
            ],
        ];

        foreach ($types as $type) {
            OfferingType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}
