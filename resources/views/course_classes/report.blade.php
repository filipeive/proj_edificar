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
        <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-black transition flex items-center shadow-lg shadow-gray-800/20">
            <i class="bi bi-printer mr-2"></i> Imprimir Relatório
        </button>
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
                        @foreach($courseClass->coupleEnrollments as $couple)
                            @php $absences = $couple->attendances()->where('status', 'absent')->count(); @endphp
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $couple->husband_name }} & {{ $couple->wife_name }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Casal</div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold {{ $absences > 2 ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ $absences }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($couple->status == 'completed')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-[8px] font-black uppercase tracking-widest">Concluído</span>
                                    @elseif($couple->status == 'failed')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-[8px] font-black uppercase tracking-widest">Reprovado</span>
                                    @else
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-[8px] font-black uppercase tracking-widest">Em Curso</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @foreach($courseClass->courseEnrollments as $enrollment)
                            @php $absences = $enrollment->attendances()->where('status', 'absent')->count(); @endphp
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $enrollment->user->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Individual</div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold {{ $absences > 2 ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ $absences }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($enrollment->status == 'completed')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-[8px] font-black uppercase tracking-widest">Concluído</span>
                                    @elseif($enrollment->status == 'failed')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-[8px] font-black uppercase tracking-widest">Reprovado</span>
                                    @else
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-[8px] font-black uppercase tracking-widest">Em Curso</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
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
                                            $total = $courseClass->enrollments_count;
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
