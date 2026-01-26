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
        return [
            $meeting->meeting_date->format('d/m/Y'),
            $meeting->cell->name,
            ucfirst($meeting->meeting_type),
            $meeting->leader->name ?? 'N/A',
            $meeting->theme ?? '',
            $meeting->adults_count,
            $meeting->children_count,
            $meeting->visitors_count,
            $meeting->adults_count + $meeting->children_count + $meeting->visitors_count,
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
