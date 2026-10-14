<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NationalCongregationReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private readonly Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows->map(fn(array $row) => [
            'Congregação' => $row['congregation'],
            'Estado' => $row['status'],
            'Membros ativos' => $row['active_members'],
            'Zonas' => $row['zones'],
            'Células' => $row['cells'],
            'Cultos no período' => $row['services'],
            'Contribuições da Igreja (MT)' => $row['verified_contributions'],
            'Dízimos e Ofertas de Cultos (MT)' => $row['service_income'],
            'Total de Entradas (MT)' => $row['total_income'],
            'Despesas da Igreja (MT)' => $row['expenses'] ?? 0,
            'Saldo Líquido (MT)' => $row['net_balance'] ?? ($row['total_income'] - ($row['expenses'] ?? 0)),
            'Contribuições Pendentes' => $row['pending_contributions'],
        ]);
    }

    public function headings(): array
    {
        return [
            'Congregação',
            'Estado',
            'Membros ativos',
            'Zonas',
            'Células',
            'Cultos no período',
            'Contribuições da Igreja (MT)',
            'Dízimos e Ofertas de Cultos (MT)',
            'Total de Entradas (MT)',
            'Despesas da Igreja (MT)',
            'Saldo Líquido (MT)',
            'Contribuições Pendentes',
        ];
    }

    public function title(): string
    {
        return 'Relatório Nacional';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '7C2D12']],
            ],
        ];
    }
}
