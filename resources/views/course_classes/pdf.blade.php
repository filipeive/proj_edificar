@extends('reports.pdf.layout')

@section('title', $title)
@section('report_type', $title)

@section('content')
    <div class="stats-box">
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ count($classes) }}</div>
                    <div class="stats-label">Total de Turmas</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-green-600">{{ $classes->where('status', 'em_andamento')->count() }}</div>
                    <div class="stats-label">Em Andamento</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-blue-600">{{ $classes->where('status', 'concluida')->count() }}</div>
                    <div class="stats-label">Concluídas</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-orange-600">{{ $classes->sum('course_enrollments_count') + $classes->sum('couple_enrollments_count') }}</div>
                    <div class="stats-label">Total Alunos Alocados</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Nome da Turma</th>
                <th style="width: 25%;">Curso</th>
                <th style="width: 20%;">Professores / Responsáveis</th>
                <th style="width: 12%; text-align: center;">Inscritos</th>
                <th style="width: 13%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classes as $index => $class)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $class->name }}</strong>
                        @if($class->start_date)
                            <br><span style="font-size: 8px; color: #6b7280;">Início: {{ $class->start_date->format('d/m/Y') }}</span>
                        @endif
                    </td>
                    <td>{{ $class->course->name ?? 'N/A' }}</td>
                    <td>
                        {{ $class->teacherMale->name ?? '' }} {{ $class->teacherMale && $class->teacherFemale ? '&' : '' }} {{ $class->teacherFemale->name ?? '' }}
                    </td>
                    <td style="text-align: center;">
                        {{ $class->course_enrollments_count + $class->couple_enrollments_count }}
                    </td>
                    <td style="text-align: center;">
                        @if($class->status === 'em_andamento')
                            <span class="badge badge-success">Em Curso</span>
                        @elseif($class->status === 'concluida')
                            <span class="badge badge-warning">Concluída</span>
                        @else
                            <span class="badge badge-danger">{{ ucfirst($class->status) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Nenhuma turma encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
