<?php

namespace App\Exports;

use App\Models\Visitor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitorsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->query) {
            return $this->query->with(['zone', 'cell', 'service'])->get();
        }

        return Visitor::with(['zone', 'cell', 'service'])->get();
    }

    /**
     * Cabeçalhos da planilha
     */
    public function headings(): array
    {
        return [
            'Nome',
            'Idade',
            'Sexo',
            'Telefone',
            'Bairro',
            'Cidade',
            'Data da Visita',
            'Culto',
            'Zona',
            'Célula',
            'Status',
            'Convidado por',
            'Observações',
        ];
    }

    /**
     * Mapear dados para cada linha
     */
    public function map($visitor): array
    {
        return [
            $visitor->name,
            $visitor->age,
            $visitor->gender ? ucfirst($visitor->gender) : '',
            $visitor->phone,
            $visitor->neighborhood,
            $visitor->city,
            $visitor->visit_date->format('d/m/Y'),
            $visitor->service ? $visitor->service->date->format('d/m/Y') . ' - ' . $visitor->service->service_type : '',
            $visitor->zone ? $visitor->zone->name : 'Não atribuído',
            $visitor->cell ? $visitor->cell->name : 'Não atribuído',
            match ($visitor->contact_status) {
                'pendente' => 'Pendente',
                'contatado' => 'Contatado',
                'integrado' => 'Integrado',
                'sem_interesse' => 'Sem Interesse',
                default => 'Desconhecido'
            },
            $visitor->invited_by_someone && $visitor->inviter_name ? $visitor->inviter_name : 'Não',
            $visitor->notes ?? '',
        ];
    }

    /**
     * Estilos da planilha
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo do cabeçalho
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F97316'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
