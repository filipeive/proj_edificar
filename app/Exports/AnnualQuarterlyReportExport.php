<?php

namespace App\Exports;

use App\Models\Zone;
use App\Models\QuarterlyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AnnualQuarterlyReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
            }
        ])->get();
    }

    public function headings(): array
    {
        return [
            ['RELATÓRIO ANUAL CONSOLIDADO - ' . $this->year],
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
                'DECISÕES (TOTAL)',
                'BAPTISMOS PLANO (TOTAL)',
                'BAPTISMOS REAL (TOTAL)',
                '% BAPTISMOS',
                'MULTIPLICAÇÕES (TOTAL)',
                'OBS'
            ]
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

        $total_saved = $reports->sum('saved_count');
        $total_plan_baptism = $reports->sum('planned_baptism_count');
        $total_real_baptism = $reports->sum('baptized_count');
        $total_multiplications = $reports->sum('cell_multiplications_count');

        $perc_baptism = $total_plan_baptism > 0 ? ($total_real_baptism / $total_plan_baptism) : 0;

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
            $total_saved,
            $total_plan_baptism,
            $total_real_baptism,
            round($perc_baptism * 100, 1) . '%',
            $total_multiplications,
            $observations
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
