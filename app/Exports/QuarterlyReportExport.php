<?php

namespace App\Exports;

use App\Models\QuarterlyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuarterlyReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function collection()
    {
        return QuarterlyReport::with(['zone', 'supervision', 'supervisor', 'events.eventType'])
            ->get()
            ->map(function ($report) {
                $eventsSummary = $report->events->map(function ($event) {
                    return $event->eventType->name.': '.$event->count.($event->description ? ' ('.$event->description.')' : '');
                })->implode('; ');

                return [
                    'Ano' => $report->year,
                    'Trimestre' => $report->quarter.'º Trimestre',
                    'Zona' => $report->zone->name ?? 'N/A',
                    'Supervisão' => $report->supervision->name ?? 'N/A',
                    'Supervisor' => $report->supervisor->name ?? 'N/A',
                    'Pastores' => $report->pastors_count,
                    'Supervisores' => $report->supervisors_count,
                    'Líderes' => $report->leaders_count,
                    'Auxiliares' => $report->timoteos_count,
                    'Membros' => $report->members_count,
                    'Visitantes' => $report->visitors_count,
                    'Células' => $report->cells_count,
                    'Participantes' => $report->participants_count,
                    'Salvações' => $report->saved_count,
                    'Baptismos Planificados' => $report->planned_baptism_count,
                    'Batizados' => $report->baptized_count,
                    'Multiplicações' => $report->cell_multiplications_count,
                    'Líderes Disciplinados' => $report->disciplined_leaders_count,
                    'Células Fechadas' => $report->closed_cells_count,
                    'Eventos e Cerimônias' => $eventsSummary ?: 'N/A',
                    'Discipulado' => $report->discipleship_score,
                    'Estratégia Evangelismo' => $report->evangelism_strategy,
                    'Consolidação' => $report->consolidation_growth,
                    'Cuidado Pastoral' => $report->pastoral_score,
                    'Visitação' => $report->visitation_routine,
                    'Apoio Líderes' => $report->leader_support,
                    'Participação Células' => $report->cell_participation_score,
                    'Participação Cultos' => $report->service_participation_score,
                    'TADEL' => $report->tadium_participation,
                    'Comunhão' => $report->communion_in_cells_score,
                    'Integração' => $report->relationship_building_score,
                    'Oração' => $report->prayer_intercession_score,
                    'Observações' => $report->ministerial_observations,
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
            'Pastores',
            'Supervisores',
            'Líderes',
            'Auxiliares',
            'Membros',
            'Visitantes',
            'Células',
            'Participantes',
            'Salvações',
            'Baptismos Planificados',
            'Batizados',
            'Multiplicações',
            'Líderes Disciplinados',
            'Células Fechadas',
            'Eventos e Cerimônias',
            'Discipulado',
            'Estratégia Evangelismo',
            'Consolidação',
            'Cuidado Pastoral',
            'Visitação',
            'Apoio Líderes',
            'Participação Células',
            'Participação Cultos',
            'TADEL',
            'Comunhão',
            'Integração',
            'Oração',
            'Observações',
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
                    'startColor' => ['rgb' => 'F97316'],
                ],
            ],
        ];
    }
}
