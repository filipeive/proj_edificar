@extends('layouts.app')

@section('title', 'Detalhes do Relatório Trimestral')
@section('page-title', 'Relatório Trimestral')
@section('page-subtitle', 'Visualização detalhada dos indicadores da zona')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('quarterly-reports.index') }}"
                class="text-blue-600 hover:text-blue-800 flex items-center transition">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
            <div class="flex space-x-2">
                @can('update', $quarterlyReport)
                    <a href="{{ route('quarterly-reports.edit', $quarterlyReport) }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                        <i class="bi bi-pencil mr-2"></i> Editar
                    </a>
                @endcan
                <button onclick="window.print()"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg flex items-center transition shadow-sm">
                    <i class="bi bi-printer mr-2"></i> Imprimir
                </button>
            </div>
        </div>

        <div class="space-y-8 print:space-y-4">
            <!-- Cabeçalho do Relatório -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-blue-600 px-8 py-12 text-white relative">
                    <div class="relative z-10">
                        <div class="flex items-center space-x-4 mb-4">
                            <span class="bg-white/20 px-4 py-1 rounded-full text-sm font-bold backdrop-blur-md">
                                {{ $quarterlyReport->quarter }}º Trimestre / {{ $quarterlyReport->year }}
                            </span>
                            <span class="bg-green-500 px-4 py-1 rounded-full text-sm font-bold shadow-lg">
                                SUBMETIDO
                            </span>
                        </div>
                        <h3 class="text-5xl font-black mb-2">Zona {{ $quarterlyReport->zone->name }}</h3>
                        <p class="text-blue-100 text-lg">Responsável: <span
                                class="font-bold">{{ $quarterlyReport->supervisor->name }}</span></p>
                    </div>
                    <i
                        class="bi bi-file-earmark-bar-graph absolute right-12 bottom-6 text-[12rem] text-white opacity-10"></i>
                </div>

                <div class="p-8 grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Células</p>
                        <p class="text-3xl font-black text-gray-800">{{ $quarterlyReport->cells_count }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Líderes</p>
                        <p class="text-3xl font-black text-gray-800">{{ $quarterlyReport->leaders_count }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Membros</p>
                        <p class="text-3xl font-black text-blue-600">{{ $quarterlyReport->members_count }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Almas Ganhas</p>
                        <p class="text-3xl font-black text-green-600">{{ $quarterlyReport->saved_count }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Coluna de Indicadores -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Resultados Ministeriais -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                        <h4 class="text-xl font-bold text-gray-800 mb-8 flex items-center">
                            <i class="bi bi-trophy mr-3 text-blue-600"></i> Resultados Ministeriais
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 font-medium">Batismos Realizados</span>
                                    <span class="text-xl font-black text-gray-800">{{ $quarterlyReport->baptized_count }} /
                                        {{ $quarterlyReport->planned_baptism_count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    @php $perc = $quarterlyReport->planned_baptism_count > 0 ? ($quarterlyReport->baptized_count / $quarterlyReport->planned_baptism_count) * 100 : 0; @endphp
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(100, $perc) }}%"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-gray-50 rounded-xl text-center">
                                    <p class="text-2xl font-black text-gray-800">
                                        {{ $quarterlyReport->cell_multiplications_count }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Multiplicações</p>
                                </div>
                                <div class="p-4 bg-red-50 rounded-xl text-center">
                                    <p class="text-2xl font-black text-red-600">{{ $quarterlyReport->closed_cells_count }}
                                    </p>
                                    <p class="text-[10px] font-bold text-red-400 uppercase">Células Fechadas</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Avaliação Qualitativa -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                        <h4 class="text-xl font-bold text-gray-800 mb-8 flex items-center">
                            <i class="bi bi-star mr-3 text-blue-600"></i> Avaliação Qualitativa
                        </h4>
                        <div class="space-y-6">
                            @php
                                $scores = [
                                    'discipleship_score' => 'Discipulado',
                                    'pastoral_score' => 'Trabalho Pastoral',
                                    'cell_participation_score' => 'Participação nas Células',
                                    'service_participation_score' => 'Participação nos Cultos',
                                    'communion_in_cells_score' => 'Comunhão nas Células',
                                    'relationship_building_score' => 'Relacionamento',
                                    'prayer_intercession_score' => 'Oração e Intercessão'
                                ];
                            @endphp
                            @foreach($scores as $field => $label)
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="font-bold text-gray-700">{{ $label }}</span>
                                        <span class="font-black text-blue-600">{{ $quarterlyReport->$field }}/10</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                        <div class="h-full rounded-full @if($quarterlyReport->$field >= 8) bg-green-500 @elseif($quarterlyReport->$field >= 5) bg-blue-500 @else bg-yellow-500 @endif"
                                            style="width: {{ $quarterlyReport->$field * 10 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div class="space-y-8">
                    <!-- Eventos -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-800 px-6 py-4 text-white">
                            <h4 class="font-bold flex items-center">
                                <i class="bi bi-calendar-event mr-2"></i> Eventos do Período
                            </h4>
                        </div>
                        <div class="p-6 space-y-4">
                            @forelse($quarterlyReport->events as $event)
                                <div class="flex justify-between items-start pb-4 border-b border-gray-100 last:border-0">
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $event->eventType->name }}</p>
                                        @if($event->description)
                                            <p class="text-xs text-gray-500 italic">{{ $event->description }}</p>
                                        @endif
                                    </div>
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-lg font-black text-sm">
                                        {{ $event->count }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 italic py-4">Nenhum evento registrado.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                        <h4 class="text-lg font-bold text-gray-800 mb-4">Observações Ministeriais</h4>
                        <div class="text-gray-600 text-sm leading-relaxed italic">
                            {!! nl2br(e($quarterlyReport->ministerial_observations ?? 'Nenhuma observação registrada.')) !!}
                        </div>
                    </div>

                    <!-- Metadados -->
                    <div class="bg-gray-900 rounded-2xl p-6 text-white text-xs space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Submetido em:</span>
                            <span>{{ $quarterlyReport->submitted_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">ID do Relatório:</span>
                            <span>#{{ str_pad($quarterlyReport->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {

            header,
            aside,
            .mb-6,
            .print\:hidden {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .shadow-sm,
            .shadow-lg {
                shadow: none !important;
                border: 1px solid #eee !important;
            }

            .bg-blue-600 {
                background-color: #1e40af !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
@endsection