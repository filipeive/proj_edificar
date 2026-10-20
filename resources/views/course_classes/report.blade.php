@extends('layouts.app')

@section('title', 'Relatório da Turma - ' . $courseClass->name)
@section('page-title', 'Relatório de Desempenho')
@section('page-subtitle', $courseClass->name . ' - ' . $courseClass->course->name)

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('course-classes.export', $courseClass) }}"
            class="action-icon text-gray-600 hover:text-emerald-600 hover:bg-emerald-50" title="Exportar Excel">
            <i class="bi bi-file-earmark-spreadsheet"></i>
        </a>
        <button onclick="window.print()"
            class="action-icon text-gray-600 hover:text-gray-900 hover:bg-gray-100" title="Imprimir Relatório">
            <i class="bi bi-printer"></i>
        </button>
    </div>
@endsection

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between print:hidden">
        <a href="{{ route('course-classes.show', $courseClass) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para a Turma</span>
        </a>

        <div class="hidden sm:flex items-center gap-2">
            <a href="{{ route('course-classes.export', $courseClass) }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-emerald-700 shadow-sm hover:bg-gray-50 transition">
                <i class="bi bi-file-earmark-excel"></i>
                <span>Exportar Excel</span>
            </a>
            <button onclick="window.print()"
                class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-black transition">
                <i class="bi bi-printer"></i>
                <span>Imprimir</span>
            </button>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Total Inscritos</p>
            <p class="text-xl sm:text-2xl font-black text-gray-900">{{ $stats['total_enrolled'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-wider mb-1">Iniciaram (Presença > 0)</p>
            <p class="text-xl sm:text-2xl font-black text-blue-700">{{ $stats['started'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-wider mb-1">Concluíram</p>
            <p class="text-xl sm:text-2xl font-black text-emerald-700">{{ $stats['completed'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
            <p class="text-[10px] font-black text-rose-600 uppercase tracking-wider mb-1">Reprovados / Desistentes</p>
            <p class="text-xl sm:text-2xl font-black text-rose-700">{{ $stats['failed'] }}</p>
        </div>
    </div>

    <!-- Tabela de Desempenho Individual -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 bg-gray-50/75 border-b border-gray-100">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-900">Desempenho Individual dos Alunos</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/50 text-[10px] font-black uppercase tracking-wider text-gray-500">
                        <th class="px-4 py-3">Nome / Casal</th>
                        <th class="px-4 py-3 text-center">Faltas</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                    @forelse($allEnrollments as $enrollment)
                        @php $absences = $enrollment instanceof \App\Models\CourseEnrollment ? $enrollment->absence_count : 0; @endphp
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-4 py-3">
                                @if($enrollment instanceof \App\Models\CoupleEnrollment)
                                    <div class="font-bold text-gray-900">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase">Casal (Inscrição Pública)</div>
                                @elseif($enrollment instanceof \App\Models\MinisterialEnrollment)
                                    <div class="font-bold text-gray-900">{{ $enrollment->full_name }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase">Individual (Inscrição Pública)</div>
                                @elseif($enrollment->malePartner && $enrollment->femalePartner)
                                    <div class="font-bold text-gray-900">{{ $enrollment->malePartner->name }} & {{ $enrollment->femalePartner->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase">Casal</div>
                                @else
                                    <div class="font-bold text-gray-900">{{ $enrollment->user->name ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase">Individual</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-bold {{ $absences > 2 ? 'text-rose-600' : 'text-gray-700' }}">
                                {{ $absences }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $labelClasses = [
                                        'cursando' => 'bg-blue-100 text-blue-800',
                                        'aprovado' => 'bg-emerald-100 text-emerald-800',
                                        'reprovado' => 'bg-rose-100 text-rose-800',
                                        'desistente' => 'bg-gray-100 text-gray-800',
                                        'approved' => 'bg-emerald-100 text-emerald-800',
                                        'pending' => 'bg-amber-100 text-amber-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $labelClasses[$enrollment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $enrollment->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-xs text-gray-400">
                                Nenhum aluno matriculado nesta turma.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
