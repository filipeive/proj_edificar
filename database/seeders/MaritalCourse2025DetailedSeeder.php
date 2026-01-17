<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseEnrollment;
use App\Models\CourseClassMeeting;
use App\Models\CourseClassAttendance;
use App\Models\Wedding;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MaritalCourse2025DetailedSeeder extends Seeder
{
    public function run(): void
    {
        // Limpar dados de 2025 para evitar duplicatas
        CourseClass::where('name', 'like', '%2025%')->delete();

        $cursoCasais = Course::firstOrCreate(['name' => 'Curso de Casais'], [
            'description' => 'Curso para casais vivendo juntos ou casados.',
            'is_active' => true
        ]);

        $cursoPreNupcial = Course::firstOrCreate(['name' => 'Curso Pré-Nupcial (Casais)'], [
            'description' => 'Curso preparatório para o casamento.',
            'is_active' => true
        ]);

        $startDate = Carbon::parse('2025-09-13');
        $endDate = Carbon::parse('2025-12-06');

        // --- TURMA 1: Filipe e Ivete ---
        $profFilipe = $this->getOrCreateUser('Filipe Domingos dos Santos', 'filipe.santos@lifechurch.com');
        $profIvete = $this->getOrCreateUser('Ivete dos Santos', 'ivete.santos@lifechurch.com');
        $auxNando = $this->getOrCreateUser('Nando', 'nando@lifechurch.com');
        $auxPaulla = $this->getOrCreateUser('Paulla', 'paulla@lifechurch.com');

        $turma1 = CourseClass::create([
            'course_id' => $cursoCasais->id,
            'name' => 'Turma Filipe & Ivete 2025',
            'type' => 'casais_vivendo',
            'teacher_male_id' => $profFilipe->id,
            'teacher_female_id' => $profIvete->id,
            'assistant_male_id' => $auxNando->id,
            'assistant_female_id' => $auxPaulla->id,
            'status' => 'concluida',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'notes' => 'Casais vivendo juntos.'
        ]);

        $this->createEnrollment($turma1, 'Leine', 'Chorona');
        $this->createEnrollment($turma1, 'Manuel', 'Siloia');
        $this->createEnrollment($turma1, 'Florencio', 'Jaimina');

        // --- TURMA 2: Dany e Gervasia ---
        $profDany = $this->getOrCreateUser('Dany', 'dany@lifechurch.com');
        $profGervasia = $this->getOrCreateUser('Gervasia', 'gervasia@lifechurch.com');

        $turma2 = CourseClass::create([
            'course_id' => $cursoCasais->id,
            'name' => 'Turma Dany & Gervasia 2025',
            'type' => 'casais_vivendo',
            'teacher_male_id' => $profDany->id,
            'teacher_female_id' => $profGervasia->id,
            'status' => 'concluida',
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $meetings2 = [
            ['date' => '2025-09-13', 'topic' => 'Um firme fundamento'],
            ['date' => '2025-09-20', 'topic' => 'Um firme fundamento (cont.)'],
            ['date' => '2025-09-27', 'topic' => 'A arte de comunicação'],
            ['date' => '2025-10-04', 'topic' => 'Resolução de conflitos'],
            ['date' => '2025-10-11', 'topic' => 'Família e parentes'],
            ['date' => '2025-10-25', 'topic' => 'As cinco linguagens do amor'],
            ['date' => '2025-11-01', 'topic' => 'O poder do perdão'],
            ['date' => '2025-11-08', 'topic' => 'Finanças'],
            ['date' => '2025-11-22', 'topic' => 'Bom sexo'],
        ];

        $m2 = [];
        foreach ($meetings2 as $idx => $meetInfo) {
            $m2[] = CourseClassMeeting::create([
                'course_class_id' => $turma2->id,
                'meeting_number' => $idx + 1,
                'date' => $meetInfo['date'],
                'topic' => $meetInfo['topic']
            ]);
        }

        $couples2 = [
            ['m' => 'Arcenio', 'f' => 'Divana', 'attendance' => ['p', 'P', 'F', 'P', 'p', 'p', 'F', 'P', 'P']],
            ['m' => 'Chababe', 'f' => 'Zamira', 'attendance' => ['p', 'P', 'P', 'P', 'P', 'P', 'P', 'P', 'P']],
            ['m' => 'Edwin', 'f' => 'Ângela', 'attendance' => ['F', 'P', 'F', 'P', 'P', 'P', 'P', 'P', 'P'], 'notes' => 'O casal Edwin entrou no curso após a primeira sessão ter iniciado.'],
            ['m' => 'Eunício', 'f' => 'Maria', 'attendance' => ['F', 'F', 'F', 'P', 'F', 'F', 'F', 'F', 'F']],
            ['m' => 'Rogério', 'f' => 'Ducha', 'attendance' => ['P', 'P', 'P', 'P', 'P', 'P', 'P', 'P', 'P']],
            ['m' => 'Mabote', 'f' => 'Inês', 'attendance' => ['p', 'P', 'P', 'P', 'P', 'F', 'P', 'P', 'P']],
        ];

        foreach ($couples2 as $c) {
            $en = $this->createEnrollment($turma2, $c['m'], $c['f'], true, $c['notes'] ?? null);
            $presences = 0;
            $absences = 0;

            foreach ($c['attendance'] as $idx => $statusChar) {
                $status = (strtolower($statusChar) === 'p') ? 'present' : 'absent';
                if ($status === 'present')
                    $presences++;
                else
                    $absences++;

                CourseClassAttendance::create([
                    'course_class_meeting_id' => $m2[$idx]->id,
                    'enrollable_type' => CourseEnrollment::class,
                    'enrollable_id' => $en->id,
                    'status' => $status
                ]);
            }
            $en->status = ($absences > 2 || ($c['m'] === 'Eunício')) ? 'reprovado' : 'aprovado';
            $en->attendance_count = $presences;
            $en->absence_count = $absences;
            $en->save();
        }

        // --- TURMA 3: Nando e Helena ---
        $profHelena = $this->getOrCreateUser('Helena', 'helena@lifechurch.com');

        $turma3 = CourseClass::create([
            'course_id' => $cursoCasais->id,
            'name' => 'Turma Nando & Helena 2025',
            'type' => 'casais_vivendo',
            'teacher_male_id' => $auxNando->id, // Reutilizando Nando
            'teacher_female_id' => $profHelena->id,
            'status' => 'concluida',
            'start_date' => $startDate,
            'end_date' => Carbon::parse('2025-11-15')
        ]);

        $couples3 = [
            ['m' => 'Elísio', 'f' => 'Neusa', 'absences' => 1, 'is_member' => true],
            ['m' => 'Tembe', 'f' => 'Shaquila', 'absences' => 0, 'is_member' => true],
            ['m' => 'Aniceto', 'f' => 'Bista', 'absences' => 0, 'is_member' => true],
            ['m' => 'Jacinto', 'f' => 'Fabia', 'absences' => 0, 'is_member' => true],
            ['m' => 'Elias', 'f' => 'Fernanda', 'absences' => 4, 'is_member' => false, 'notes' => 'Não terminaram as aulas. Justificativa: trabalho e cerimónias familiares.'],
        ];

        // Criar 10 encontros para teste de faltas
        $meetings = [];
        for ($i = 1; $i <= 10; $i++) {
            $meetings[] = CourseClassMeeting::create([
                'course_class_id' => $turma3->id,
                'meeting_number' => $i,
                'date' => $startDate->copy()->addWeeks($i - 1),
                'topic' => "Encontro $i"
            ]);
        }

        foreach ($couples3 as $c) {
            $en = $this->createEnrollment($turma3, $c['m'], $c['f'], $c['is_member'], $c['notes'] ?? null);
            $en->status = ($c['absences'] > 2) ? 'reprovado' : 'aprovado';

            // Simular presenças/faltas
            foreach ($meetings as $idx => $m) {
                $status = ($idx < $c['absences']) ? 'absent' : 'present';
                CourseClassAttendance::create([
                    'course_class_meeting_id' => $m->id,
                    'enrollable_type' => CourseEnrollment::class,
                    'enrollable_id' => $en->id,
                    'status' => $status
                ]);
            }
            $en->attendance_count = 10 - $c['absences'];
            $en->absence_count = $c['absences'];
            $en->save();
        }

        // --- TURMA 4: Paulo e Joaquina ---
        $profPaulo = $this->getOrCreateUser('Paulo Nazare', 'paulo.nazare@lifechurch.com');
        $profJoaquina = $this->getOrCreateUser('Joaquina', 'joaquina@lifechurch.com');

        $turma4 = CourseClass::create([
            'course_id' => $cursoPreNupcial->id,
            'name' => 'Turma Paulo & Joaquina 2025',
            'type' => 'pre_nupcial',
            'teacher_male_id' => $profPaulo->id,
            'teacher_female_id' => $profJoaquina->id,
            'status' => 'concluida',
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        // João & Laurinda - Wedding: 6 June 2026
        $en1 = $this->createEnrollment($turma4, 'João Máfia', 'Laurinda');
        $en1->update([
            'wedding_date' => '2026-06-06',
            'recommendation' => 'Avaliado ✓'
        ]);
        $this->createWedding('João Máfia', 'Laurinda', '2026-06-06');

        // Magide & Rosa
        $this->createEnrollment($turma4, 'Magide', 'Rosa', true, 'Avaliado ✓');

        // Nico & Joana - Wedding: 11 July 2026, Noivado: March 2026, Padrinhos: Pr Isaías & Atalia
        $en3 = $this->createEnrollment($turma4, 'Nico', 'Joana');
        $en3->update([
            'wedding_date' => '2026-07-11',
            'engagement_date' => '2006-03-01', // Conforme mensagem: Março/2006
            'godparents_male' => 'Pr Isaías',
            'godparents_female' => 'Atalia',
            'recommendation' => 'Avaliado ✓'
        ]);
        $this->createWedding('Nico', 'Joana', '2026-07-11', 'Pr Isaías & Atalia');

        // Hermínio & Cilena - Wedding: July 2026
        $en4 = $this->createEnrollment($turma4, 'Hermínio', 'Cilena');
        $en4->update([
            'wedding_date' => '2026-07-01', // Data estimada
            'recommendation' => 'Avaliado ✓'
        ]);
        $this->createWedding('Hermínio', 'Cilena', '2026-07-01');

        // Tino & Jéssica - Married
        $en5 = $this->createEnrollment($turma4, 'Tino', 'Jéssica');
        $en5->update([
            'status' => 'aprovado',
            'notes' => 'Cumpriram e casaram.'
        ]);

        // Chico & Alice - Stopped at 2 pillars
        $en6 = $this->createEnrollment($turma4, 'Chico', 'Alice');
        $en6->update([
            'status' => 'desistente',
            'notes' => 'Só terminaram 2 pilares. Alice foi estagiar em Molocué.',
            'completed_pillars' => 2
        ]);
    }

    private function getOrCreateUser($name, $email)
    {
        return User::firstOrCreate(['name' => $name], [
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'membro'
        ]);
    }

    private function createEnrollment($class, $male, $female, $isMember = true, $notes = null)
    {
        $maleUser = $this->getOrCreateUser($male, strtolower(str_replace(' ', '.', $male)) . '@example.com');
        $femaleUser = $this->getOrCreateUser($female, strtolower(str_replace(' ', '.', $female)) . '@example.com');

        return CourseEnrollment::create([
            'course_id' => $class->course_id,
            'course_class_id' => $class->id,
            'male_partner_id' => $maleUser->id,
            'female_partner_id' => $femaleUser->id,
            'is_church_member' => $isMember,
            'status' => 'cursando',
            'notes' => $notes
        ]);
    }

    private function createWedding($groom, $bride, $date, $godparents = null)
    {
        Wedding::create([
            'groom_name' => $groom,
            'bride_name' => $bride,
            'date' => $date,
            'godparents' => $godparents,
            'status' => 'scheduled'
        ]);
    }
}
