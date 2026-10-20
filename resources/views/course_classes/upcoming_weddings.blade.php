@extends('layouts.app')

@section('title', 'Casamentos Próximos - Portal Life Church')
@section('page-title', 'Casamentos Próximos')
@section('page-subtitle', 'Cronograma de Casamentos e Status de Cursos')

@section('header-actions')
    <div class="flex items-center gap-2">
        <button onclick="window.print()" class="action-icon text-gray-600 hover:text-gray-900 hover:bg-gray-100" title="Imprimir Lista">
            <i class="bi bi-printer"></i>
        </button>
    </div>
@endsection

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="flex items-center gap-2">
            <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Planejamento Matrimonial</span>
            <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-xs font-bold text-rose-800">
                {{ $enrollments->count() }} casais
            </span>
        </div>

        <button onclick="window.print()" class="hidden sm:inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-black transition">
            <i class="bi bi-printer"></i>
            <span>Imprimir Lista</span>
        </button>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/75 text-[10px] font-black uppercase tracking-wider text-gray-500">
                        <th class="px-4 py-3">Data do Casamento</th>
                        <th class="px-4 py-3">O Casal</th>
                        <th class="px-4 py-3">Turma / Curso</th>
                        <th class="px-4 py-3 text-center">Status Curso</th>
                        <th class="px-4 py-3 text-center">Presenças</th>
                        <th class="px-4 py-3 text-center">Membros?</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                    @forelse($enrollments as $enrollment)
                        @php
                            $daysToWedding = now()->diffInDays($enrollment->wedding_date, false);
                            $isUrgent = $daysToWedding < 30 && $enrollment->status != 'aprovado';
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition {{ $isUrgent ? 'bg-rose-50/30' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-gray-900 block">{{ $enrollment->wedding_date->format('d/m/Y') }}</span>
                                <span class="text-[10px] font-bold {{ $daysToWedding < 0 ? 'text-gray-400' : ($daysToWedding < 30 ? 'text-rose-600' : 'text-blue-600') }}">
                                    {{ $daysToWedding < 0 ? 'Já realizado' : 'Em ' . $daysToWedding . ' dias' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900">
                                    {{ $enrollment->malePartner->name ?? 'N/A' }} & {{ $enrollment->femalePartner->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800">{{ $enrollment->courseClass->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-gray-400 font-medium">{{ $enrollment->course->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusClasses = [
                                        'cursando' => 'bg-blue-100 text-blue-800',
                                        'aprovado' => 'bg-emerald-100 text-emerald-800',
                                        'reprovado' => 'bg-rose-100 text-rose-800',
                                        'desistente' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusClasses[$enrollment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $enrollment->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-gray-900">
                                {{ $enrollment->attendance_count }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($enrollment->is_church_member)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">Sim</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-600">Não</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('course-enrollments.show', $enrollment) }}" 
                                    class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-orange-700 transition" title="Ver Detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">
                                Nenhum casamento próximo registrado com matrícula em curso.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
