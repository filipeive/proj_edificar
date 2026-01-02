<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Course::create([
            'name' => 'Curso Pré-Nupcial (Casais)',
            'slug' => 'curso-pre-nupcial',
            'description' => 'Preparação sólida para o matrimônio com base em princípios cristãos e práticos.',
            'category' => 'Casais',
            'duration' => '8 semanas',
            'is_active' => true,
            'registration_open' => true,
        ]);

        \App\Models\Course::create([
            'name' => 'Academia de Vida',
            'slug' => 'academia-de-vida',
            'description' => 'O primeiro passo na jornada de crescimento espiritual e integração na igreja.',
            'category' => 'Teologia',
            'duration' => '12 semanas',
            'is_active' => true,
            'registration_open' => true,
        ]);

        \App\Models\Course::create([
            'name' => 'Escola de Líderes',
            'slug' => 'escola-de-lideres',
            'description' => 'Treinamento avançado para aqueles que desejam servir e liderar na casa de Deus.',
            'category' => 'Liderança',
            'duration' => '6 meses',
            'is_active' => true,
            'registration_open' => false,
        ]);
    }
}
