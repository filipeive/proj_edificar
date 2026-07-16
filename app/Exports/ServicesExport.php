<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServicesExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $services;
    protected $title;

    public function __construct($services, $title = 'Relatório de Cultos')
    {
        $this->services = $services;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->services;
    }

    public function headings(): array
    {
        return [
            'Data',
            'Tipo de Culto',
            'Tema',
            'Pregador',
            'Adultos Membros',
            'Adultos Visitantes',
            'Adultos Salvações',
            'Crianças Membros',
            'Crianças Visitantes',
            'Crianças Salvações',
            'Total Participação',
            'Total Visitantes',
            'Total Salvações',
            'Ofertas Especiais',
            'Total Dízimos',
            'Total Ofertas',
        ];
    }

    public function map($service): array
    {
        $isTeaching = $service->service_type === 'teaching';

        $adultsMembers = $isTeaching
            ? $service->zoneParticipations->sum(function ($p) {
                return $p->adults_members + $p->leaders + $p->auxiliary_leaders + $p->supervisors + $p->zone_pastors;
            })
            : ($service->adults_members ?? 0);

        $adultsVisitors = $isTeaching
            ? ($service->zoneParticipations->sum('adults_visitors') + ($service->adults_visitors ?? 0))
            : ($service->adults_visitors ?? 0);

        $childrenMembers = $isTeaching
            ? $service->zoneParticipations->sum('children_members')
            : ($service->children_members ?? 0);

        $childrenVisitors = $isTeaching
            ? ($service->zoneParticipations->sum('children_visitors') + ($service->children_visitors ?? 0))
            : ($service->children_visitors ?? 0);

        return [
            \Carbon\Carbon::parse($service->date)->format('d/m/Y'),
            $this->getServiceTypeLabel($service->service_type),
            $service->theme ?? '-',
            $service->preacher ? $service->preacher->name : ($service->preacher_name ?? '-'),
            $adultsMembers,
            $adultsVisitors,
            $service->adults_salvations ?? 0,
            $childrenMembers,
            $childrenVisitors,
            $service->children_salvations ?? 0,
            $service->total_participation,
            $service->total_visitors,
            ($service->adults_salvations ?? 0) + ($service->children_salvations ?? 0),
            number_format($service->special_offerings_total ?? 0, 2, ',', '.'),
            number_format($service->tithes->sum('amount'), 2, ',', '.'),
            number_format($service->offerings->sum('amount') + $service->individualOfferings->sum('amount'), 2, ',', '.'),
        ];
    }

    public function title(): string
    {
        return substr($this->title, 0, 31); // Excel sheet name limit
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function getServiceTypeLabel($type)
    {
        return match ($type) {
            '1st' => '1º Culto',
            '2nd' => '2º Culto',
            '3rd' => '3º Culto',
            '4th' => '4º Culto',
            'teaching' => 'Ensino',
            'special' => 'Especial',
            default => $type,
        };
    }
}
