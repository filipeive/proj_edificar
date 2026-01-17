@extends('layouts.app')

@section('title', 'Casamentos Próximos - Portal Life Church')
@section('page-title', 'Casamentos Próximos')
@section('page-subtitle', 'Cronograma de Casamentos e Status de Cursos')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
             <div class="flex items-center space-x-4">
                <div class="p-3 bg-red-100 rounded-2xl">
                    <i class="bi bi-heart-fill text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-gray-900">Planejamento Matrimonial</h3>
                    <p class="text-xs text-gray-500 font-medium">Casais inscritos em cursos com data de casamento definida</p>
                </div>
             </div>
             <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-black transition flex items-center shadow-lg shadow-gray-800/20">
                <i class="bi bi-printer mr-2"></i> Imprimir Lista
            </button>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data do Casamento</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">O Casal</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Turma / Curso</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status Curso</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Freq.</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros?</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($enrollments as $enrollment)
                            @php
                                $daysToWedding = now()->diffInDays($enrollment->wedding_date, false);
                                $isUrgent = $daysToWedding < 30 && $enrollment->status != 'aprovado';
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors {{ $isUrgent ? 'bg-red-50/30' : '' }}">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900">{{ $enrollment->wedding_date->format('d/m/Y') }}</span>
                                        <span class="text-[10px] font-bold {{ $daysToWedding < 0 ? 'text-gray-400' : ($daysToWedding < 30 ? 'text-red-500' : 'text-blue-500') }}">
                                            {{ $daysToWedding < 0 ? 'Já realizado' : 'Em ' . $daysToWedding . ' dias' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="font-bold text-gray-900">
                                        {{ $enrollment->malePartner->name ?? 'N/A' }} & {{ $enrollment->femalePartner->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm font-bold text-gray-700">{{ $enrollment->courseClass->name ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $enrollment->course->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $statusClasses = [
                                            'cursando' => 'bg-blue-100 text-blue-800',
                                            'aprovado' => 'bg-green-100 text-green-800',
                                            'reprovado' => 'bg-red-100 text-red-800',
                                            'desistente' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 {{ $statusClasses[$enrollment->status] ?? 'bg-gray-100 text-gray-800' }} rounded-full text-[8px] font-black uppercase tracking-widest">
                                        {{ $enrollment->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="text-sm font-black text-gray-900">{{ $enrollment->attendance_count }}</div>
                                    <div class="text-[8px] text-gray-400 font-black uppercase tracking-widest">Presenças</div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @if($enrollment->is_church_member)
                                        <span class="text-blue-600 font-black text-[10px] uppercase">Sim</span>
                                    @else
                                        <span class="text-gray-400 font-black text-[10px] uppercase">Não</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('course-enrollments.show', $enrollment) }}" 
                                        class="bg-gray-100 text-gray-700 p-2 rounded-xl border border-gray-200 hover:bg-white hover:border-blue-500 hover:text-blue-600 transition flex items-center justify-center inline-flex">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-20 text-center text-gray-400">
                                    <i class="bi bi-calendar-x text-4xl mb-4 block"></i>
                                    <span class="font-medium italic">Nenhum casamento próximo registrado com matricula em curso.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .container-fluid { padding: 0 !important; }
            button, .text-right, .px-8.py-6.text-right { display: none !important; }
            .bg-white { border: none !important; shadow: none !important; }
        }
    </style>
@endsection
