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
    protected $classIds;

    public function __construct(array $classIds = [])
    {
        $this->classIds = $classIds;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Overview Sheet
        $sheets[] = new CourseOverviewSheet($this->classIds);

        // individual Sheets for selected or recent classes
        $query = CourseClass::with(['course', 'courseEnrollments.malePartner', 'courseEnrollments.femalePartner'])
            ->where('status', '!=', 'cancelada');

        if (!empty($this->classIds)) {
            $query->whereIn('id', $this->classIds);
        } else {
            $query->orderBy('start_date', 'desc')->take(10);
        }

        $classes = $query->get();

        foreach ($classes as $courseClass) {
            $sheets[] = new CourseClassReportExport($courseClass);
        }

        return $sheets;
    }
}

class CourseOverviewSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $classIds;

    public function __construct(array $classIds = [])
    {
        $this->classIds = $classIds;
    }

    public function collection()
    {
        $query = CourseClass::with('course')->withCount('courseEnrollments');

        if (!empty($this->classIds)) {
            $query->whereIn('id', $this->classIds);
        }

        return $query->get();
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
