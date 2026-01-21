<?php

namespace App\Exports;

use App\Models\CommitmentPackage;
use App\Models\Contribution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PackageMembersExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $package;
    protected $startDate;
    protected $endDate;

    public function __construct(CommitmentPackage $package)
    {
        $this->package = $package;

        $now = now();
        // Lógica de ciclo: 20 do mês anterior ao dia 5 do mês atual
        if ($now->day <= 5) {
            $this->startDate = $now->copy()->subMonth()->startOfMonth()->addDays(19);
            $this->endDate = $now->copy()->startOfMonth()->addDays(4);
        } else {
            $this->startDate = $now->copy()->startOfMonth()->addDays(19);
            $this->endDate = $now->copy()->addMonth()->startOfMonth()->addDays(4);
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

                return [
                    'Membro' => $user->name,
                    'Telefone' => $user->phone ?? 'N/A',
                    'Célula' => $user->cell->name ?? 'N/A',
                    'Zona' => $user->cell->supervision->zone->name ?? 'N/A',
                    'Comprometido' => number_format((float) $commitment->committed_amount, 2, ',', '.') . ' MT',
                    'Contribuído?' => $contribution ? 'SIM' : 'NÃO',
                    'Valor Pago' => $contribution ? number_format((float) $contribution->amount, 2, ',', '.') . ' MT' : '0,00 MT',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Membro',
            'Telefone',
            'Célula',
            'Zona',
            'Valor Comprometido',
            'Contribuído? (' . $this->startDate->format('d/m') . ' - ' . $this->endDate->format('d/m') . ')',
            'Valor Pago'
        ];
    }

    public function title(): string
    {
        return 'Membros - ' . $this->package->name;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0082C4']]
            ],
        ];
    }
}
