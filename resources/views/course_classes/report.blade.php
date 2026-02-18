@extends('layouts.app')

@section('title', 'Relatório da Turma - ' . $courseClass->name)
@section('page-title', 'Relatório de Desempenho')
@section('page-subtitle', $courseClass->name . ' - ' . $courseClass->course->name)

@section('content')
<div class="container-fluid">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('course-classes.show', $courseClass) }}" class="text-gray-500 hover:text-gray-700 flex items-center">
            <i class="bi bi-arrow-left mr-2"></i> Voltar para a turma
        </a>
        <div class="flex space-x-3">
            <a href="{{ route('course-classes.export', $courseClass) }}" class="bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-green-700 transition flex items-center shadow-lg shadow-green-600/20">
                <i class="bi bi-file-earmark-excel mr-2"></i> Exportar Excel
            </a>
            <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-black transition flex items-center shadow-lg shadow-gray-800/20">
                <i class="bi bi-printer mr-2"></i> Imprimir Relatório
            </button>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Inscritos</p>
            <p class="text-3xl font-black text-gray-900">{{ $stats['total_enrolled'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Iniciaram (Presença > 0)</p>
            <p class="text-3xl font-black text-blue-600">{{ $stats['started'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Concluíram</p>
            <p class="text-3xl font-black text-green-600">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Reprovados/Desistentes</p>
            <p class="text-3xl font-black text-red-600">{{ $stats['failed'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Detalhes por Inscrito -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h4 class="text-lg font-black text-gray-900">Desempenho Individual</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nome</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Faltas</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($allEnrollments as $enrollment)
                            @php $absences = $enrollment instanceof \App\Models\CourseEnrollment ? $enrollment->absence_count : 0; @endphp
                            <tr>
                                <td class="px-6 py-4">
                                    @if($enrollment instanceof \App\Models\CoupleEnrollment)
                                        <div class="font-bold text-gray-900">{{ $enrollment->husband_name }} & {{ $enrollment->wife_name }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">CASAL (INS. PÚBLICA)</div>
                                    @elseif($enrollment instanceof \App\Models\MinisterialEnrollment)
                                        <div class="font-bold text-gray-900">{{ $enrollment->full_name }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">INDIVIDUAL (INS. PÚBLICA)</div>
                                    @elseif($enrollment->malePartner && $enrollment->femalePartner)
                                        <div class="font-bold text-gray-900">{{ $enrollment->malePartner->name }} & {{ $enrollment->femalePartner->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">CASAL</div>
                                    @else
                                        <div class="font-bold text-gray-900">{{ $enrollment->user->name ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">INDIVIDUAL</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-bold {{ $absences > 2 ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ $absences }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $labelClasses = [
                                            'cursando' => 'bg-blue-100 text-blue-800',
                                            'aprovado' => 'bg-green-100 text-green-800',
                                            'reprovado' => 'bg-red-100 text-red-800',
                                            'desistente' => 'bg-gray-100 text-gray-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 {{ $labelClasses[$enrollment->status] ?? 'bg-gray-100 text-gray-800' }} rounded-full text-[8px] font-black uppercase tracking-widest">
                                        {{ $enrollment->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-400 italic">Nenhum inscrito.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Resumo de Encontros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h4 class="text-lg font-black text-gray-900">Resumo de Encontros</h4>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    @foreach($courseClass->meetings as $meeting)
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-gray-50 rounded-xl flex flex-col items-center justify-center text-gray-400 border border-gray-100">
                                <span class="text-[10px] font-black leading-none">{{ $meeting->meeting_number }}º</span>
                                <span class="text-[8px] font-bold uppercase tracking-tighter">Enc.</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $meeting->topic ?? 'Encontro' }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $meeting->date->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        @php 
                                            $present = $meeting->attendances->where('status', 'present')->count();
                                            $total = $courseClass->courseEnrollments->count();
                                            $percent = $total > 0 ? round(($present / $total) * 100) : 0;
                                        @endphp
                                        <span class="text-sm font-black text-gray-900">{{ $percent }}%</span>
                                        <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">Presença</p>
                                    </div>
                                </div>
                                <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-blue-600 h-full rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .container-fluid { padding: 0 !important; }
        button, a { display: none !important; }
        .bg-white { border: none !important; shadow: none !important; }
        .shadow-sm, .shadow-lg { box-shadow: none !important; }
    }
</style>
@endsection
