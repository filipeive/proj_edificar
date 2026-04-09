<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Treinamentos',
                'code' => 'training',
                'category' => 'training',
            ],
            [
                'name' => 'Confraternizações',
                'code' => 'fellowship',
                'category' => 'fellowship',
            ],
            [
                'name' => 'Funeral',
                'code' => 'funeral',
                'category' => 'ceremony',
            ],
            [
                'name' => 'Casamento',
                'code' => 'wedding',
                'category' => 'ceremony',
            ],
            [
                'name' => 'Dedicação de Bebé',
                'code' => 'baby_dedication',
                'category' => 'ceremony',
            ],
            [
                'name' => 'Servindo a Comunidade',
                'code' => 'community_service',
                'category' => 'service',
            ],
            [
                'name' => 'Outro',
                'code' => 'other',
                'category' => 'other',
            ],
        ];

        foreach ($types as $type) {
            EventType::updateOrCreate(['code' => $type['code']], $type);
        }
    }
}
