@extends('reports.pdf.layout')

@section('title', $title)
@section('report_type', $title)

@section('content')
    <div class="stats-box">
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ count($enrollments) }}</div>
                    <div class="stats-label">Total de Inscritos</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-green-600">{{ $enrollments->whereNotNull('course_class_id')->count() }}</div>
                    <div class="stats-label">Alocados em Turma</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-amber-600">{{ $enrollments->whereNull('course_class_id')->count() }}</div>
                    <div class="stats-label">Aguardando Turma</div>
                </td>
                <td class="stats-item">
                    <div class="stats-value text-blue-600">{{ $enrollments->where('is_church_member', true)->count() }}</div>
                    <div class="stats-label">Membros de Célula</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Nome Completo</th>
                <th style="width: 20%;">Curso Pretendido</th>
                <th style="width: 20%;">Contatos (Email/Telefone)</th>
                <th style="width: 15%;">Turma Alocada</th>
                <th style="width: 15%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $index => $enrollment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $enrollment->full_name }}</strong>
                        <br><span style="font-size: 8px; color: #6b7280;">{{ $enrollment->is_church_member ? 'Membro' : 'Visitante' }} • {{ $enrollment->cell_name ?: 'Sem Célula' }}</span>
                    </td>
                    <td>{{ $enrollment->course->name ?? 'N/A' }}</td>
                    <td>
                        {{ $enrollment->phone }}<br>
                        <span style="font-size: 8px; color: #6b7280;">{{ $enrollment->email }}</span>
                    </td>
                    <td>{{ $enrollment->courseClass->name ?? 'Sem Turma' }}</td>
                    <td style="text-align: center;">
                        @if($enrollment->course_class_id)
                            <span class="badge badge-success">Alocado</span>
                        @else
                            <span class="badge badge-warning">Pendente</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Nenhuma inscrição encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
