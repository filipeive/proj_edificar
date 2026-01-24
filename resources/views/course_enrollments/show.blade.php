@extends('layouts.app')

@section('title', 'Detalhes da Matrícula - Portal Life Church')
@section('page-title', 'Detalhes da Matrícula')
@section('page-subtitle', $enrollment->course->name)

@section('content')
    <div class="container-fluid">
        <div class="w-full">
            <div class="mb-6 flex justify-between items-center">
                @if($enrollment->course_class_id)
                    <a href="{{ route('course-classes.show', $enrollment->course_class_id) }}" class="text-gray-500 hover:text-gray-700 flex items-center">
                        <i class="bi bi-arrow-left mr-2"></i> Voltar para a turma
                    </a>
                @else
                    <a href="{{ route('courses.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
                        <i class="bi bi-arrow-left mr-2"></i> Voltar para cursos
                    </a>
                @endif
                <a href="{{ route('course-enrollments.edit', $enrollment) }}" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                    <i class="bi bi-pencil mr-2"></i> Editar Dados
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Coluna Esquerda: Dados do Casal -->
                <div class="md:col-span-2 space-y-8">
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8">
                             <h4 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                                <i class="bi bi-heart-fill text-red-500 mr-3"></i> O Casal
                            </h4>

                            @if(!$enrollment->course_class_id)
                                <div class="mb-6 p-4 bg-orange-50 border border-orange-100 rounded-2xl flex items-center gap-4">
                                    <div class="p-3 bg-orange-100 text-orange-600 rounded-xl">
                                        <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-orange-900 uppercase tracking-widest">Pendente de Atribuição</p>
                                        <p class="text-xs text-orange-700">Este casal ainda não foi atribuído a uma turma específica.</p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-400 mb-1">Ele</p>
                                    <p class="text-lg font-black text-gray-900">{{ $enrollment->malePartner->name ?? $enrollment->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $enrollment->malePartner->email ?? '' }}</p>
                                </div>
                                <div class="p-6 bg-pink-50 rounded-2xl border border-pink-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-pink-400 mb-1">Ela</p>
                                    <p class="text-lg font-black text-gray-900">{{ $enrollment->femalePartner->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $enrollment->femalePartner->email ?? '' }}</p>
                                </div>
                            </div>

                            <div class="mt-8 grid grid-cols-2 md:grid-cols-3 gap-6">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Casamento</p>
                                    <p class="text-sm font-bold text-gray-700">{{ $enrollment->wedding_date ? $enrollment->wedding_date->format('d/m/Y') : 'Não definida' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Noivado</p>
                                    <p class="text-sm font-bold text-gray-700">{{ $enrollment->engagement_date ? $enrollment->engagement_date->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Membros</p>
                                    <p class="text-sm font-bold text-gray-700">{{ $enrollment->is_church_member ? 'Sim' : 'Não' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8">
                            <h4 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                                <i class="bi bi-journal-text text-blue-600 mr-3"></i> Avaliação e Notas
                            </h4>
                            
                            <div class="space-y-6">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Recomendação Final</p>
                                    <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-700 italic">
                                        {{ $enrollment->recommendation ?? 'Nenhuma recomendação registrada até o momento.' }}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Observações Internas</p>
                                    <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-700">
                                        {{ $enrollment->notes ?? 'Nenhuma observação.' }}
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Padrinhos (Ele)</p>
                                        <p class="text-sm font-bold text-gray-700">{{ $enrollment->godparents_male ?? 'Não informado' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Padrinhos (Ela)</p>
                                        <p class="text-sm font-bold text-gray-700">{{ $enrollment->godparents_female ?? 'Não informado' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coluna Direita: Status e Frequência -->
                <div class="space-y-8">
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-center">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Status Atual</p>
                        @php
                            $statusLabel = [
                                'cursando' => 'Em Curso',
                                'aprovado' => 'Aprovado',
                                'reprovado' => 'Reprovado',
                                'desistente' => 'Desistente',
                            ][$enrollment->status] ?? $enrollment->status;
                            
                            $statusColor = [
                                'cursando' => 'text-blue-600 bg-blue-50',
                                'aprovado' => 'text-green-600 bg-green-50',
                                'reprovado' => 'text-red-600 bg-red-50',
                                'desistente' => 'text-gray-600 bg-gray-50',
                            ][$enrollment->status] ?? 'text-gray-600 bg-gray-50';
                        @endphp
                        <span class="px-6 py-2 rounded-full text-xs font-black uppercase tracking-widest {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>

                        <div class="mt-8 pt-8 border-t border-gray-100">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-2xl font-black text-gray-900">{{ $enrollment->attendance_count }}</p>
                                    <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">Presenças</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-black text-red-600">{{ $enrollment->absence_count }}</p>
                                    <p class="text-[8px] font-black uppercase tracking-widest text-gray-400">Faltas</p>
                                </div>
                            </div>
                            @if($enrollment->absence_reasons)
                                <div class="mt-4 p-3 bg-red-50 rounded-xl text-[10px] text-red-700 text-left">
                                    <p class="font-bold mb-1">Motivos:</p>
                                    {{ $enrollment->absence_reasons }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
