<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CellMeetingsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $meetings;

    public function __construct($meetings)
    {
        $this->meetings = $meetings;
    }

    public function collection()
    {
        return $this->meetings;
    }

    public function title(): string
    {
        return 'Encontros de Célula';
    }

    public function headings(): array
    {
        return [
            'Data',
            'Célula',
            'Tipo',
            'Líder',
            'Tema',
            'Adultos',
            'Crianças',
            'Visitantes',
            'Total',
            'Decisões',
        ];
    }

    public function map($meeting): array
    {
        $targetName = 'Geral / Outros';

        if ($meeting->cell) {
            $targetName = $meeting->cell->name;
        } elseif ($meeting->supervision) {
            $targetName = "SUPERVISÃO: " . $meeting->supervision->name;
        } elseif ($meeting->zone) {
            $targetName = "ZONA: " . $meeting->zone->name;
        }

        return [
            $meeting->meeting_date->format('d/m/Y'),
            $targetName,
            match ($meeting->meeting_type) {
                'leadership' => 'Liderança',
                'supervision' => 'Supervisão',
                'zone' => 'Zona',
                'general' => 'Geral',
                'other' => 'Especial',
                default => 'Célula',
            },
            $meeting->leader->name ?? 'N/A',
            $meeting->theme ?? '',
            (int) $meeting->adults_count,
            (int) $meeting->children_count,
            (int) $meeting->visitors_count,
            (int) ($meeting->adults_count + $meeting->children_count + $meeting->visitors_count),
            $meeting->decisions ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
