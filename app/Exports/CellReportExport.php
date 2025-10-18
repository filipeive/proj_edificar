<?php
namespace App\Exports;

use App\Models\Cell;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CellReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles {
    protected $cell;
    protected $startDate;
    protected $endDate;

    public function __construct(Cell $cell, $startDate, $endDate) {
        $this->cell = $cell;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection() {
        return $this->cell->contributions()
            ->whereBetween('contribution_date', [$this->startDate, $this->endDate])
            ->with('user')
            ->get()
            ->map(function ($contribution) {
                return [
                    'Data' => $contribution->contribution_date->format('d/m/Y'),
                    'Membro' => $contribution->user->name,
                    'Valor (MT)' => number_format($contribution->amount, 2, ',', '.'),
                    'Status' => ucfirst($contribution->status),
                ];
            });
    }

    public function headings(): array {
        return ['Data', 'Membro', 'Valor (MT)', 'Status'];
    }

    public function title(): string {
        return 'Célula - ' . $this->cell->name;
    }

    public function styles(Worksheet $sheet) {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0082C4']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}