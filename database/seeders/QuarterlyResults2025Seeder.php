<?php

namespace Database\Seeders;

use App\Models\QuarterlyReport;
use App\Models\Zone;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuarterlyResults2025Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pega um supervisor padrão ou admin para os relatórios se não houver um específico
        $defaultSupervisor = User::where('role', 'admin')->first();

        $data = [
            [
                'zone_name' => 'Coalane',
                'pastors_count' => 1,
                'supervisors_count' => 4,
                'leaders_count' => 15,
                'timoteos_count' => 11,
                'members_count' => 73,
                'participants_count' => 21,
                'saved_count' => 36,
                'planned_baptism_count' => 45,
                'baptized_count' => 11,
                'cell_multiplications_count' => 1,
                'ministerial_observations' => '1 Lidere disciplinado | 1 celulas fechadas',
            ],
            [
                'zone_name' => 'Torrone',
                'pastors_count' => 1,
                'supervisors_count' => 2,
                'leaders_count' => 8,
                'timoteos_count' => 8,
                'members_count' => 46,
                'participants_count' => 14,
                'saved_count' => 8,
                'planned_baptism_count' => 12,
                'baptized_count' => 4,
                'cell_multiplications_count' => 3,
                'ministerial_observations' => '1 Lider & 1 Supervisor disciplinados',
            ],
            [
                'zone_name' => 'Zona Centro A',
                'pastors_count' => 1,
                'supervisors_count' => 4,
                'leaders_count' => 16,
                'timoteos_count' => 14,
                'members_count' => 85,
                'participants_count' => 42,
                'saved_count' => 25,
                'planned_baptism_count' => 24,
                'baptized_count' => 13,
                'cell_multiplications_count' => 2,
                'ministerial_observations' => '3 Lideres disciplinados | 3celulas fechadas',
            ],
            [
                'zone_name' => 'Zona Centro B',
                'pastors_count' => 0,
                'supervisors_count' => 1,
                'leaders_count' => 6,
                'timoteos_count' => 5,
                'members_count' => 47,
                'participants_count' => 61,
                'saved_count' => 14,
                'planned_baptism_count' => 25,
                'baptized_count' => 22,
                'cell_multiplications_count' => 2,
                'ministerial_observations' => '2 Lideres disciplinados | 1 Celula fechada',
            ],
            [
                'zone_name' => '17 de Setembro',
                'pastors_count' => 1,
                'supervisors_count' => 3,
                'leaders_count' => 17,
                'timoteos_count' => 4,
                'members_count' => 71,
                'participants_count' => 22,
                'saved_count' => 30,
                'planned_baptism_count' => 23,
                'baptized_count' => 6,
                'cell_multiplications_count' => 3,
                'ministerial_observations' => '4 Lideres disciplinados | 2 Celulas fechadas',
            ],
            [
                'zone_name' => 'Zona do Manhaua',
                'pastors_count' => 1,
                'supervisors_count' => 3,
                'leaders_count' => 14,
                'timoteos_count' => 3,
                'members_count' => 63,
                'participants_count' => 9,
                'saved_count' => 27,
                'planned_baptism_count' => 32,
                'baptized_count' => 11,
                'cell_multiplications_count' => 1,
                'ministerial_observations' => '1 Celula fechada',
            ],
            [
                'zone_name' => 'Aeroporto',
                'pastors_count' => 0,
                'supervisors_count' => 4,
                'leaders_count' => 18,
                'timoteos_count' => 8,
                'members_count' => 91,
                'participants_count' => 14,
                'saved_count' => 37,
                'planned_baptism_count' => 35,
                'baptized_count' => 5,
                'cell_multiplications_count' => 6,
                'ministerial_observations' => '2 Lideres disciplinados | 1 Celula fechada',
            ],
            [
                'zone_name' => 'Coalane - MIA',
                'pastors_count' => 0,
                'supervisors_count' => 2,
                'leaders_count' => 9,
                'timoteos_count' => 5,
                'members_count' => 55,
                'participants_count' => 19,
                'saved_count' => 17,
                'planned_baptism_count' => 7,
                'baptized_count' => 2,
                'cell_multiplications_count' => 0,
                'ministerial_observations' => '3 Celulas fechadas',
            ],
            [
                'zone_name' => 'Zona do Santagua - MIA',
                'pastors_count' => 1,
                'supervisors_count' => 3,
                'leaders_count' => 15,
                'timoteos_count' => 7,
                'members_count' => 123,
                'participants_count' => 14,
                'saved_count' => 76,
                'planned_baptism_count' => 33,
                'baptized_count' => 22,
                'cell_multiplications_count' => 3,
                'ministerial_observations' => '2 Celulas fechadas',
            ],
            [
                'zone_name' => '17 de Setembro - MIA',
                'pastors_count' => 0,
                'supervisors_count' => 1,
                'leaders_count' => 8,
                'timoteos_count' => 9,
                'members_count' => 50,
                'participants_count' => 18,
                'saved_count' => 11,
                'planned_baptism_count' => 12,
                'baptized_count' => 10,
                'cell_multiplications_count' => 2,
                'ministerial_observations' => '',
            ],
            [
                'zone_name' => 'Centro - MIA',
                'pastors_count' => 1,
                'supervisors_count' => 2,
                'leaders_count' => 8,
                'timoteos_count' => 3,
                'members_count' => 109,
                'participants_count' => 17,
                'saved_count' => 46,
                'planned_baptism_count' => 14,
                'baptized_count' => 0,
                'cell_multiplications_count' => 0,
                'ministerial_observations' => '1 Celula fechada',
            ],
            [
                'zone_name' => 'Torrone - MIA',
                'pastors_count' => 0,
                'supervisors_count' => 1,
                'leaders_count' => 2,
                'timoteos_count' => 2,
                'members_count' => 15,
                'participants_count' => 13,
                'saved_count' => 7,
                'planned_baptism_count' => 1,
                'baptized_count' => 0,
                'cell_multiplications_count' => 0,
                'ministerial_observations' => '1 Superv discipl. | 1 suprvisao fechada',
            ],
        ];

        foreach ($data as $item) {
            $zone = Zone::firstOrCreate(['name' => $item['zone_name']]);

            QuarterlyReport::updateOrCreate(
                [
                    'zone_id' => $zone->id,
                    'year' => 2025,
                    'quarter' => 4,
                ],
                [
                    'supervisor_id' => $defaultSupervisor->id,
                    'pastors_count' => $item['pastors_count'],
                    'supervisors_count' => $item['supervisors_count'],
                    'leaders_count' => $item['leaders_count'],
                    'timoteos_count' => $item['timoteos_count'],
                    'members_count' => $item['members_count'],
                    'participants_count' => $item['participants_count'],
                    'saved_count' => $item['saved_count'],
                    'planned_baptism_count' => $item['planned_baptism_count'],
                    'baptized_count' => $item['baptized_count'],
                    'cell_multiplications_count' => $item['cell_multiplications_count'],
                    'ministerial_observations' => $item['ministerial_observations'],
                    'status' => 'approved',
                    'submitted_at' => now(),
                ]
            );
        }
    }
}
