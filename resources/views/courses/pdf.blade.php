@extends('reports.pdf.layout')

@section('title', $title)
@section('report_type', $title)

@section('content')
    <div class="stats-box">
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ count($courses) }}</div>
                    <div class="stats-label">Total de Cursos</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-green-600">{{ $courses->where('registration_open', true)->count() }}</div>
                    <div class="stats-label">Inscrições Abertas</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-blue-600">{{ $courses->sum('classes_count') }}</div>
                    <div class="stats-label">Total de Turmas</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-orange-600">{{ $courses->sum('enrollments_count') + $courses->sum('couple_enrollments_count') }}</div>
                    <div class="stats-label">Total de Alunos/Casais</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 30%;">Curso / Descrição</th>
                <th style="width: 15%;">Categoria</th>
                <th style="width: 15%; text-align: center;">Turmas Ativas</th>
                <th style="width: 15%; text-align: center;">Alunos / Inscritos</th>
                <th style="width: 20%; text-align: center;">Status Inscrições</th>
            </tr>
        </thead>
        <tbody>
            @forelse($courses as $index => $course)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $course->name }}</strong>
                        @if($course->description)
                            <br><span style="font-size: 8px; color: #6b7280;">{{ Str::limit($course->description, 70) }}</span>
                        @endif
                    </td>
                    <td>{{ $course->category ?? 'ACADEMIA' }}</td>
                    <td style="text-align: center;">{{ $course->classes_count }}</td>
                    <td style="text-align: center;">{{ $course->enrollments_count + $course->couple_enrollments_count }}</td>
                    <td style="text-align: center;">
                        @if($course->registration_open)
                            <span class="badge badge-success">Abertas</span>
                        @else
                            <span class="badge badge-danger">Fechadas</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Nenhum curso cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
