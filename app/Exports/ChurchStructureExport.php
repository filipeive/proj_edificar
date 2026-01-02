<?php

namespace App\Exports;

use App\Models\Cell;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ChurchStructureExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function collection()
    {
        return Cell::with(['supervision.zone', 'leader'])
            ->get()
            ->map(function ($cell) {
                return [
                    'Zona' => $cell->supervision->zone->name ?? 'N/A',
                    'Supervisão' => $cell->supervision->name ?? 'N/A',
                    'Célula' => $cell->name,
                    'Líder' => $cell->leader->name ?? 'N/A',
                    'Total Membros' => $cell->getMembersCount(),
                    'Membros que Contribuíram (Mês)' => $cell->getMembersContributedThisMonth(),
                    'Total Arrecadado (Mês)' => number_format($cell->getTotalContributedThisMonth(), 2, ',', '.'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Zona',
            'Supervisão',
            'Célula',
            'Líder',
            'Total Membros',
            'Membros que Contribuíram (Mês)',
            'Total Arrecadado (Mês)',
        ];
    }

    public function title(): string
    {
        return 'Estrutura da Igreja';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '2563EB']
                ],
            ],
        ];
    }
}
