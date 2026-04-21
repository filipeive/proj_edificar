<?php

namespace App\Exports;

use App\Models\Zone;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnnualQuarterlyReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function collection()
    {
        return Zone::with([
            'pastor',
            'quarterlyReports' => function ($query) {
                $query->where('year', $this->year);
            },
        ])->get();
    }

    public function headings(): array
    {
        return [
            ['RELATÓRIO ANUAL CONSOLIDADO - '.$this->year],
            [''],
            [
                'ORD',
                'CAMPUS/ZONA',
                'PASTOR DA ZONA',
                'PASTORES (MÉDIA)',
                'SUPERVISORAS (MÉDIA)',
                'LÍDERES (MÉDIA)',
                'AUXILIARES (MÉDIA)',
                'MEMBROS (MÉDIA)',
                'VISITANTES (MÉDIA)',
                'CÉLULAS (MÉDIA)',
                'SALVAÇÕES (TOTAL)',
                'BAPTISMOS PLANO (TOTAL)',
                'BAPTISMOS REAL (TOTAL)',
                '% BAPTISMOS',
                'MULTIPLICAÇÕES (TOTAL)',
                'DISCIPULADO (MÉDIA)',
                'EVANGELISMO (MÉDIA)',
                'CONSOLIDAÇÃO (MÉDIA)',
                'CUIDADO PASTORAL (MÉDIA)',
                'VISITAÇÃO (MÉDIA)',
                'APOIO LÍDERES (MÉDIA)',
                'PARTIC. CÉLULAS (MÉDIA)',
                'PARTIC. CULTOS (MÉDIA)',
                'TADEL (MÉDIA)',
                'COMUNHÃO (MÉDIA)',
                'INTEGRAÇÃO (MÉDIA)',
                'ORAÇÃO (MÉDIA)',
                'OBS',
            ],
        ];
    }

    public function map($zone): array
    {
        static $ord = 0;
        $ord++;

        $reports = $zone->quarterlyReports;
        $count = $reports->count() ?: 1;

        $avg_pastors = $reports->avg('pastors_count');
        $avg_supervisors = $reports->avg('supervisors_count');
        $avg_leaders = $reports->avg('leaders_count');
        $avg_auxiliares = $reports->avg('timoteos_count');
        $avg_members = $reports->avg('members_count');
        $avg_visitors = $reports->avg('visitors_count');
        $avg_cells = $reports->avg('cells_count');

        $total_saved = $reports->sum('saved_count');
        $total_plan_baptism = $reports->sum('planned_baptism_count');
        $total_real_baptism = $reports->sum('baptized_count');
        $total_multiplications = $reports->sum('cell_multiplications_count');

        $perc_baptism = $total_plan_baptism > 0 ? ($total_real_baptism / $total_plan_baptism) : 0;

        $avg_discipleship = $reports->avg('discipleship_score');
        $avg_evangelism = $reports->avg('evangelism_strategy');
        $avg_consolidation = $reports->avg('consolidation_growth');
        $avg_pastoral = $reports->avg('pastoral_score');
        $avg_visitation = $reports->avg('visitation_routine');
        $avg_leader_support = $reports->avg('leader_support');
        $avg_cell_part = $reports->avg('cell_participation_score');
        $avg_service_part = $reports->avg('service_participation_score');
        $avg_tadium = $reports->avg('tadium_participation');
        $avg_communion = $reports->avg('communion_in_cells_score');
        $avg_relationship = $reports->avg('relationship_building_score');
        $avg_prayer = $reports->avg('prayer_intercession_score');

        $observations = $zone->quarterlyReports->pluck('ministerial_observations')->filter()->unique()->implode('; ');

        return [
            $ord,
            $zone->name,
            $zone->pastor->name ?? 'N/A',
            round($avg_pastors),
            round($avg_supervisors),
            round($avg_leaders),
            round($avg_auxiliares),
            round($avg_members),
            round($avg_visitors),
            round($avg_cells),
            $total_saved,
            $total_plan_baptism,
            $total_real_baptism,
            round($perc_baptism * 100, 1).'%',
            $total_multiplications,
            round($avg_discipleship, 1),
            round($avg_evangelism, 1),
            round($avg_consolidation, 1),
            round($avg_pastoral, 1),
            round($avg_visitation, 1),
            round($avg_leader_support, 1),
            round($avg_cell_part, 1),
            round($avg_service_part, 1),
            round($avg_tadium, 1),
            round($avg_communion, 1),
            round($avg_relationship, 1),
            round($avg_prayer, 1),
            $observations,
        ];
    }

    public function styles(Worksheet $worksheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
