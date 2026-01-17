<?php

namespace App\Exports;

use App\Models\CourseClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourseClassReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $courseClass;

    public function __construct(CourseClass $courseClass)
    {
        $this->courseClass = $courseClass->load(['courseEnrollments.malePartner', 'courseEnrollments.femalePartner']);
    }

    public function collection()
    {
        return $this->courseClass->courseEnrollments;
    }

    public function title(): string
    {
        return 'Relatório ' . $this->courseClass->name;
    }

    public function headings(): array
    {
        return [
            'O Casal (Ele & Ela)',
            'Status',
            'Data Casamento',
            'Data Noivado',
            'Presenças',
            'Faltas',
            'Membros?',
            'Pilares Concluídos',
            'Observações'
        ];
    }

    public function map($enrollment): array
    {
        return [
            ($enrollment->malePartner->name ?? 'N/A') . ' & ' . ($enrollment->femalePartner->name ?? 'N/A'),
            ucfirst($enrollment->status),
            $enrollment->wedding_date ? $enrollment->wedding_date->format('d/m/Y') : 'N/A',
            $enrollment->engagement_date ? $enrollment->engagement_date->format('d/m/Y') : 'N/A',
            $enrollment->attendance_count,
            $enrollment->absence_count,
            $enrollment->is_church_member ? 'Sim' : 'Não',
            $enrollment->completed_pillars,
            $enrollment->notes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
