<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialDashboardExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly array $rows,
        private readonly int $month,
        private readonly int $year,
        private readonly string $scope,
    ) {
    }

    public function collection(): Collection
    {
        return collect($this->rows)->map(fn(array $row) => [
            'Data' => $row['date'],
            'Origem' => $row['source'],
            'Descrição' => $row['description'],
            'Entradas (MT)' => $row['income'],
            'Saídas (MT)' => $row['expense'],
            'Estado' => $row['status'],
        ]);
    }

    public function headings(): array
    {
        return ['Data', 'Origem', 'Descrição', 'Entradas (MT)', 'Saídas (MT)', 'Estado'];
    }

    public function title(): string
    {
        return 'Financeiro ' . $this->month . '-' . $this->year;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '155E75']],
            ],
        ];
    }
}