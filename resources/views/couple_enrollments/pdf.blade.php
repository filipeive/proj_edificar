@extends('reports.pdf.layout')

@section('title', $title)
@section('report_type', $title)

@section('content')
    <div class="stats-box">
        <table class="stats-grid">
            <tr>
                <td class="stats-item">
                    <div class="stats-value">{{ count($enrollments) }}</div>
                    <div class="stats-label">Total de Inscritos Registados</div>
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
                    <div class="stats-value text-blue-600">{{ $enrollments->pluck('course_id')->unique()->count() }}</div>
                    <div class="stats-label">Cursos Distintos</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Casal / Nome</th>
                <th style="width: 18%;">Curso</th>
                <th style="width: 15%;">Relação / Anos</th>
                <th style="width: 15%;">Contatos</th>
                <th style="width: 12%;">Turma</th>
                <th style="width: 10%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $index => $enrollment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $enrollment->husband_name }}</strong> & <strong>{{ $enrollment->wife_name }}</strong>
                        @if($enrollment->address)
                            <br><span style="font-size: 8px; color: #6b7280;">End: {{ $enrollment->address }}</span>
                        @endif
                    </td>
                    <td>{{ $enrollment->course->name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $relTypes = [
                                'namoro' => 'Em relacionamento',
                                'em_relacionamento' => 'Em relacionamento',
                                'noivos' => 'Noivos',
                                'vivendo_maritalmente' => 'Vivendo Maritalmente',
                                'casados' => 'Casados',
                            ];
                            $relLabel = $relTypes[$enrollment->relationship_type] ?? ucfirst(str_replace('_', ' ', $enrollment->relationship_type));
                        @endphp
                        {{ $relLabel }}
                        <br><span style="font-size: 8px; color: #6b7280;">{{ $enrollment->years_together }} anos juntos</span>
                    </td>
                    <td>
                        @if($enrollment->husband_phone)
                            M: {{ $enrollment->husband_phone }}<br>
                        @endif
                        @if($enrollment->wife_phone)
                            E: {{ $enrollment->wife_phone }}<br>
                        @endif
                        @if(!$enrollment->husband_phone && !$enrollment->wife_phone && $enrollment->contacts)
                            {{ $enrollment->contacts }}
                        @endif
                    </td>
                    <td>
                        {{ $enrollment->courseClass->name ?? 'Sem Turma' }}
                    </td>
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
                    <td colspan="7" style="text-align: center; padding: 20px;">Nenhuma inscrição encontrada para o filtro selecionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
