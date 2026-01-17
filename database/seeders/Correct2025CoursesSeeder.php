<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Correct2025CoursesSeeder extends Seeder
{
    public function run(): void
    {
        // Limpar duplicatas de 2025
        CourseClass::where('name', 'like', '%2025%')->delete();

        $cursoCasais = Course::firstOrCreate(['name' => 'Curso de Casais'], [
            'description' => 'Curso para casais vivendo juntos ou casados.',
            'status' => 'active'
        ]);

        $cursoPreNupcial = Course::firstOrCreate(['name' => 'Curso Pré-Nupcial (Casais)'], [
            'description' => 'Curso preparatório para o casamento.',
            'status' => 'active'
        ]);

        // Professores (já devem existir, mas vamos garantir)
        $teachers = [
            ['male' => 'Filipe Domingos dos Santos', 'female' => 'Ivete dos Santos', 'type' => 'casais_vivendo'],
            ['male' => 'Dany', 'female' => 'Gervasia', 'type' => 'casais_vivendo'],
            ['male' => 'Nando', 'female' => 'Helena', 'type' => 'casais_vivendo'],
            ['male' => 'Paulo Nazare', 'female' => 'Joaquina', 'type' => 'pre_nupcial'],
        ];

        foreach ($teachers as $pair) {
            $male = User::firstOrCreate(['name' => $pair['male']], [
                'email' => strtolower(str_replace(' ', '.', $pair['male'])) . '@lifechurch.com',
                'password' => Hash::make('password'),
                'role' => 'membro'
            ]);
            $female = User::firstOrCreate(['name' => $pair['female']], [
                'email' => strtolower(str_replace(' ', '.', $pair['female'])) . '@lifechurch.com',
                'password' => Hash::make('password'),
                'role' => 'membro'
            ]);

            $course = $pair['type'] === 'casais_vivendo' ? $cursoCasais : $cursoPreNupcial;

            $turma = CourseClass::create([
                'course_id' => $course->id,
                'name' => "Turma {$pair['male']} & {$pair['female']} 2025",
                'type' => $pair['type'],
                'teacher_male_id' => $male->id,
                'teacher_female_id' => $female->id,
                'status' => 'concluida',
                'start_date' => '2025-09-13',
                'end_date' => '2025-12-06',
                'notes' => 'Aulas aos sábados.'
            ]);

            // Adicionar alguns inscritos fictícios
            for ($i = 1; $i <= 3; $i++) {
                $mName = "Inscrito {$i} " . substr($pair['male'], 0, 3);
                $fName = "Inscrita {$i} " . substr($pair['female'], 0, 3);

                $mUser = User::firstOrCreate(['name' => $mName], [
                    'email' => strtolower(str_replace(' ', '.', $mName)) . '@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'membro'
                ]);
                $fUser = User::firstOrCreate(['name' => $fName], [
                    'email' => strtolower(str_replace(' ', '.', $fName)) . '@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'membro'
                ]);

                CourseEnrollment::create([
                    'course_id' => $course->id,
                    'course_class_id' => $turma->id,
                    'male_partner_id' => $mUser->id,
                    'female_partner_id' => $fUser->id,
                    'status' => 'aprovado',
                    'is_church_member' => true,
                    'attendance_count' => 10,
                    'absence_count' => 0
                ]);
            }
        }
    }
}
