<?php

namespace App\Exports;

use App\Models\CourseClass;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GlobalCourseReportExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        // Overview Sheet
        $sheets[] = new CourseOverviewSheet();

        // individual Sheets for active/recent classes
        $classes = CourseClass::with(['course', 'courseEnrollments.malePartner', 'courseEnrollments.femalePartner'])
            ->where('status', '!=', 'cancelada')
            ->orderBy('start_date', 'desc')
            ->take(10) // Limit to recent 10 to not explode file size if too many
            ->get();

        foreach ($classes as $courseClass) {
            $sheets[] = new CourseClassReportExport($courseClass);
        }

        return $sheets;
    }
}

class CourseOverviewSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return CourseClass::with('course')->withCount('courseEnrollments')->get();
    }

    public function title(): string
    {
        return 'Visão Geral dos Cursos';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Curso',
            'Turma',
            'Status',
            'Data Início',
            'Data Fim',
            'Total Inscritos'
        ];
    }

    public function map($class): array
    {
        return [
            $class->id,
            $class->course->name,
            $class->name,
            ucfirst($class->status),
            $class->start_date ? $class->start_date->format('d/m/Y') : 'N/A',
            $class->end_date ? $class->end_date->format('d/m/Y') : 'N/A',
            $class->course_enrollments_count
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
