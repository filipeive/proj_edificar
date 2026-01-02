<?php

namespace App\Exports;

use App\Models\QuarterlyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuarterlyReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function collection()
    {
        return QuarterlyReport::with(['zone', 'supervision', 'supervisor'])
            ->get()
            ->map(function ($report) {
                return [
                    'Ano' => $report->year,
                    'Trimestre' => $report->quarter . 'º Trimestre',
                    'Zona' => $report->zone->name ?? 'N/A',
                    'Supervisão' => $report->supervision->name ?? 'N/A',
                    'Supervisor' => $report->supervisor->name ?? 'N/A',
                    'Líderes' => $report->leaders_count,
                    'Células' => $report->cells_count,
                    'Timóteos' => $report->timoteos_count,
                    'Membros' => $report->members_count,
                    'Participantes' => $report->participants_count,
                    'Salvos' => $report->saved_count,
                    'Batismos Planejados' => $report->planned_baptism_count,
                    'Batizados' => $report->baptized_count,
                    'Multiplicações' => $report->cell_multiplications_count,
                    'Líderes Disciplinados' => $report->disciplined_leaders_count,
                    'Células Fechadas' => $report->closed_cells_count,
                    'Status' => ucfirst($report->status),
                    'Data de Submissão' => $report->submitted_at ? $report->submitted_at->format('d/m/Y H:i') : 'N/A',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Ano',
            'Trimestre',
            'Zona',
            'Supervisão',
            'Supervisor',
            'Líderes',
            'Células',
            'Timóteos',
            'Membros',
            'Participantes',
            'Salvos',
            'Batismos Planejados',
            'Batizados',
            'Multiplicações',
            'Líderes Disciplinados',
            'Células Fechadas',
            'Status',
            'Data de Submissão',
        ];
    }

    public function title(): string
    {
        return 'Relatórios Trimestrais';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'F97316']
                ],
            ],
        ];
    }
}
