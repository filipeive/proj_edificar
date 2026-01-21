<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Hash;

class PreMaritalCourses2025Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Garantir que os cursos base existem
        $cursoCasais = Course::firstOrCreate(['name' => 'Curso de Casais'], [
            'description' => 'Curso para casais vivendo juntos ou casados.',
            'status' => 'active'
        ]);

        $cursoPreNupcial = Course::firstOrCreate(['name' => 'Curso Pré-Nupcial (Casais)'], [
            'description' => 'Curso preparatório para o casamento.',
            'status' => 'active'
        ]);

        // 2. Criar/Buscar Professores
        $filipe = User::where('name', 'like', '%Filipe Domingos%')->first()
            ?? User::firstOrCreate(['email' => 'filipe.lider@lifechurch.com'], [
                'name' => 'Filipe Domingos dos Santos',
                'password' => Hash::make('password'),
                'role' => 'membro'
            ]);

        $ivete = User::firstOrCreate(['name' => 'Ivete dos Santos'], [
            'email' => 'ivete.lider@lifechurch.com',
            'password' => Hash::make('password'),
            'role' => 'membro'
        ]);

        $dany = User::firstOrCreate(['name' => 'Dany'], [
            'email' => 'dany.lider@lifechurch.com',
            'password' => Hash::make('password'),
            'role' => 'membro'
        ]);

        $gervasia = User::firstOrCreate(['name' => 'Gervasia'], [
            'email' => 'gervasia.lider@lifechurch.com',
            'password' => Hash::make('password'),
            'role' => 'membro'
        ]);

        $nandoLider = User::firstOrCreate(['name' => 'Nando'], [
            'email' => 'nando.lider@lifechurch.com',
            'password' => Hash::make('password'),
            'role' => 'membro'
        ]);

        $helena = User::firstOrCreate(['name' => 'Helena'], [
            'email' => 'helena.lider@lifechurch.com',
            'password' => Hash::make('password'),
            'role' => 'membro'
        ]);

        $paulo = User::where('name', 'like', '%Paulo Nazare%')->first()
            ?? User::firstOrCreate(['email' => 'paulo.lider@lifechurch.com'], [
                'name' => 'Paulo Nazare',
                'password' => Hash::make('password'),
                'role' => 'membro'
            ]);

        $joaquina = User::where('name', 'like', '%Joaquina%')->first()
            ?? User::firstOrCreate(['email' => 'joaquina.lider@lifechurch.com'], [
                'name' => 'Joaquina',
                'password' => Hash::make('password'),
                'role' => 'membro'
            ]);

        // 3. Criar Turmas
        $turma1 = CourseClass::updateOrCreate(['name' => 'Turma Filipe & Ivete 2025'], [
            'course_id' => $cursoCasais->id,
            'type' => 'casais_vivendo',
            'teacher_male_id' => $filipe->id,
            'teacher_female_id' => $ivete->id,
            'status' => 'em_andamento',
            'start_date' => '2025-01-01',
            'notes' => 'Turma de casais vcs'
        ]);

        $turma2 = CourseClass::updateOrCreate(['name' => 'Turma Dany & Gervasia 2025'], [
            'course_id' => $cursoCasais->id,
            'type' => 'casais_vivendo',
            'teacher_male_id' => $dany->id,
            'teacher_female_id' => $gervasia->id,
            'status' => 'em_andamento',
            'start_date' => '2025-02-01',
        ]);

        $turma3 = CourseClass::updateOrCreate(['name' => 'Turma Nando & Helena 2025'], [
            'course_id' => $cursoCasais->id,
            'type' => 'casais_vivendo',
            'teacher_male_id' => $nandoLider->id,
            'teacher_female_id' => $helena->id,
            'status' => 'em_andamento',
            'start_date' => '2025-03-01',
            'notes' => '5 casais matriculados'
        ]);

        $turma4 = CourseClass::updateOrCreate(['name' => 'Turma Paulo & Joaquina 2025'], [
            'course_id' => $cursoPreNupcial->id,
            'type' => 'pre_nupcial',
            'teacher_male_id' => $paulo->id,
            'teacher_female_id' => $joaquina->id,
            'status' => 'em_andamento',
            'start_date' => '2025-04-01',
        ]);

        // 4. Exemplo de Inscrição (Casal Nando & Paulla na Turma 1)
        $paulla = User::firstOrCreate(['email' => 'paulla@example.com'], [
            'name' => 'Paulla',
            'password' => Hash::make('password'),
            'role' => 'membro'
        ]);

        $nandoMembro = User::firstOrCreate(['email' => 'nando.membro@example.com'], [
            'name' => 'Nando Membro',
            'password' => Hash::make('password'),
            'role' => 'membro'
        ]);

        CourseEnrollment::firstOrCreate([
            'course_class_id' => $turma1->id,
            'male_partner_id' => $nandoMembro->id,
            'female_partner_id' => $paulla->id,
        ], [
            'course_id' => $cursoCasais->id,
            'status' => 'cursando',
            'is_church_member' => true
        ]);
    }
}
