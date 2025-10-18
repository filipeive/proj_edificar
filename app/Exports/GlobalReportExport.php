<?php
namespace App\Exports;

use App\Models\Contribution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GlobalReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles {
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection() {
        return Contribution::whereBetween('contribution_date', [$this->startDate, $this->endDate])
            ->with('user', 'cell', 'supervision', 'zone')
            ->get()
            ->map(function ($contribution) {
                return [
                    'Data' => $contribution->contribution_date->format('d/m/Y'),
                    'Membro' => $contribution->user->name,
                    'Zona' => $contribution->zone->name,
                    'Supervisão' => $contribution->supervision->name,
                    'Célula' => $contribution->cell->name,
                    'Valor (MT)' => number_format($contribution->amount, 2, ',', '.'),
                    'Status' => ucfirst($contribution->status),
                ];
            });
    }

    public function headings(): array {
        return ['Data', 'Membro', 'Zona', 'Supervisão', 'Célula', 'Valor (MT)', 'Status'];
    }

    public function title(): string {
        return 'Relatório Global';
    }

    public function styles(Worksheet $sheet) {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0082C4']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}