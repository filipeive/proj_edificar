<?php

namespace App\Exports;

use App\Models\CommitmentPackage;
use App\Models\Contribution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PackageMembersExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected $package;
    protected $startDate;
    protected $endDate;

    public function __construct(CommitmentPackage $package, $startDate = null, $endDate = null)
    {
        $this->package = $package;

        if ($startDate && $endDate) {
            $this->startDate = \Carbon\Carbon::parse($startDate);
            $this->endDate = \Carbon\Carbon::parse($endDate);
        } else {
            $now = now();
            if ($now->day >= 20) {
                $this->startDate = $now->copy()->startOfMonth()->addDays(19);
                $this->endDate = $now->copy()->addMonth()->startOfMonth()->addDays(4);
            } else {
                $this->startDate = $now->copy()->subMonth()->startOfMonth()->addDays(19);
                $this->endDate = $now->copy()->startOfMonth()->addDays(4);
            }
        }
    }

    public function collection()
    {
        return $this->package->userCommitments()
            ->with(['user.cell.supervision.zone'])
            ->get()
            ->map(function ($commitment) {
                $user = $commitment->user;

                // Buscar contribuição verificada neste pacote para o período atual
                $contribution = Contribution::verified()
                    ->where('user_id', $user->id)
                    ->where('package_id', $this->package->id)
                    ->whereBetween('contribution_date', [$this->startDate, $this->endDate])
                    ->first();

                $cell = $user->cell;
                $supervision = $cell?->supervision;
                $zone = $supervision?->zone;
                $pastorZona = $zone?->pastor;

                return [
                    'ID' => $user->id,
                    'Membro' => $user->name,
                    'Telefone' => $user->phone ?? 'N/A',
                    'Célula' => $cell->name ?? 'N/A',
                    'Supervisão' => $supervision->name ?? 'N/A',
                    'Zona' => $zone->name ?? 'N/A',
                    'Pastor Zona' => $pastorZona?->name ?? 'N/A',
                    'Valor Comprometido' => number_format((float) $commitment->committed_amount, 2, ',', '.') . ' MT',
                    'Contribuiu no período?' => $contribution ? 'SIM' : 'NÃO',
                    'Valor Pago' => $contribution ? number_format((float) $contribution->amount, 2, ',', '.') . ' MT' : '0,00 MT',
                    'Data da Contribuição' => $contribution ? $contribution->contribution_date->format('d/m/Y') : '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Membro',
            'Telefone',
            'Célula',
            'Supervisão',
            'Zona',
            'Pastor Zona',
            'Valor Comprometido',
            'Contribuiu? (' . $this->startDate->format('d/m') . ' - ' . $this->endDate->format('d/m') . ')',
            'Valor Pago',
            'Data da Contribuição'
        ];
    }

    public function title(): string
    {
        return 'Membros - ' . $this->package->name;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getDefaultRowDimension()->setRowHeight(20);
        $sheet->getStyle('A:K')->getAlignment()->setWrapText(true)->setVertical('center');
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0082C4']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 28,
            'C' => 16,
            'D' => 22,
            'E' => 22,
            'F' => 18,
            'G' => 22,
            'H' => 18,
            'I' => 22,
            'J' => 16,
            'K' => 18,
        ];
    }
}
