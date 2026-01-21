<?php

namespace App\Exports;

use App\Models\CourseEnrollment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllClassesExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $classIds;

    public function __construct($classIds = null)
    {
        $this->classIds = $classIds;
    }

    public function collection()
    {
        $query = CourseEnrollment::with(['courseClass.course', 'malePartner', 'femalePartner']);

        if ($this->classIds && is_array($this->classIds)) {
            $query->whereIn('course_class_id', $this->classIds);
        }

        return $query->get();
    }

    public function title(): string
    {
        return 'Relatório Geral de Turmas';
    }

    public function headings(): array
    {
        return [
            'Turma',
            'Curso',
            'O Casal (Ele & Ela)',
            'Status',
            'Data Casamento',
            'Presenças',
            'Faltas',
            'Recomendação',
            'Observações'
        ];
    }

    public function map($enrollment): array
    {
        return [
            $enrollment->courseClass->name ?? 'N/A',
            $enrollment->courseClass->course->name ?? 'N/A',
            ($enrollment->malePartner->name ?? 'N/A') . ' & ' . ($enrollment->femalePartner->name ?? 'N/A'),
            ucfirst($enrollment->status),
            $enrollment->wedding_date ? $enrollment->wedding_date->format('d/m/Y') : 'N/A',
            $enrollment->attendance_count,
            $enrollment->absence_count,
            $enrollment->recommendation,
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
