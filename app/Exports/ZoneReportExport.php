<?php
namespace App\Exports;

use App\Models\Zone;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ZoneReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles {
    protected $zone;
    protected $startDate;
    protected $endDate;

    public function __construct(Zone $zone, $startDate, $endDate) {
        $this->zone = $zone;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection() {
        return $this->zone->contributions()
            ->whereBetween('contribution_date', [$this->startDate, $this->endDate])
            ->with('user', 'cell', 'supervision')
            ->get()
            ->map(function ($contribution) {
                return [
                    'Data' => $contribution->contribution_date->format('d/m/Y'),
                    'Membro' => $contribution->user->name,
                    'Supervisão' => $contribution->supervision->name,
                    'Célula' => $contribution->cell->name,
                    'Valor (MT)' => number_format($contribution->amount, 2, ',', '.'),
                    'Status' => ucfirst($contribution->status),
                ];
            });
    }

    public function headings(): array {
        return ['Data', 'Membro', 'Supervisão', 'Célula', 'Valor (MT)', 'Status'];
    }

    public function title(): string {
        return 'Zona - ' . $this->zone->name;
    }

    public function styles(Worksheet $sheet) {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0082C4']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}