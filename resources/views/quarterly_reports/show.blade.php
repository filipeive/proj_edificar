@extends('layouts.app')

@section('title', 'Detalhes do Relatório - Portal Life Church')

@section('content')
    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Header -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">
                    <a href="{{ route('quarterly-reports.index') }}" class="hover:underline">Relatórios Trimestrais</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span>Visualizar</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">
                    Trimestre {{ $report->quarter }}/{{ $report->year }}
                </h1>
                <p class="text-gray-500 font-bold">Supervisão: <span
                        class="text-blue-600">{{ $report->supervision->name }}</span> | Zona: {{ $report->zone->name }}</p>
            </div>
            <div class="flex gap-3">
                @can('update', $report)
                    <a href="{{ route('quarterly-reports.edit', $report) }}"
                        class="flex items-center bg-blue-50 text-blue-600 px-6 py-3 rounded-2xl hover:bg-blue-600 hover:text-white transition-all font-black">
                        <i class="bi bi-pencil-square mr-2"></i>
                        Editar
                    </a>
                @endcan
                <a href="{{ route('quarterly-reports.index') }}"
                    class="group flex items-center bg-gray-50 text-gray-500 px-6 py-3 rounded-2xl hover:bg-gray-100 transition-all font-bold">
                    <i class="bi bi-arrow-left text-sm mr-2"></i>
                    Sair
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Stats Grid -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                    <h3 class="text-lg font-black text-gray-900 mb-8 flex items-center gap-2">
                        <i class="bi bi-bar-chart text-blue-600"></i>
                        Estatísticas Organizacionais
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        @php
                            $stats = [
                                ['label' => 'Líderes', 'value' => $report->leaders_count, 'icon' => 'bi-person-badge', 'color' => 'blue'],
                                ['label' => 'Células', 'value' => $report->cells_count, 'icon' => 'bi-grid-3x3-gap', 'color' => 'purple'],
                                ['label' => 'Timóteos', 'value' => $report->timoteos_count, 'icon' => 'bi-award', 'color' => 'indigo'],
                                ['label' => 'Membros', 'value' => $report->members_count, 'icon' => 'bi-people', 'color' => 'green'],
                                ['label' => 'Participantes', 'value' => $report->participants_count, 'icon' => 'bi-graph-up', 'color' => 'orange'],
                                ['label' => 'Almas Ganhas', 'value' => $report->saved_count, 'icon' => 'bi-heart-pulse', 'color' => 'red'],
                            ];
                        @endphp
                        @foreach($stats as $stat)
                            <div
                                class="p-6 bg-gray-50 rounded-[2rem] border border-transparent hover:border-{{ $stat['color'] }}-100 transition-all">
                                <i class="bi {{ $stat['icon'] }} text-{{ $stat['color'] }}-500 text-xl block mb-2"></i>
                                <span
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">{{ $stat['label'] }}</span>
                                <span class="text-2xl font-black text-gray-900">{{ $stat['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Assessments -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                            <i class="bi bi-star-half text-yellow-500"></i>
                            Indicadores de Saúde
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                        @php
                            $assessments = [
                                'discipleship_score' => 'Discipulado',
                                'pastoral_score' => 'Cuidado Pastoral',
                                'cell_participation_score' => 'Participação Células',
                                'service_participation_score' => 'Participação Cultos',
                                'communion_in_cells_score' => 'Comunhão',
                                'relationship_building_score' => 'Integração',
                                'prayer_intercession_score' => 'Oração'
                            ];
                        @endphp
                        @foreach($assessments as $field => $label)
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ $label }}</span>
                                    <span
                                        class="px-3 py-1 bg-blue-600 text-white rounded-lg text-xs font-black">{{ $report->$field }}/3</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-600 rounded-full transition-all duration-1000"
                                        style="width: {{ ($report->$field / 3) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Observations -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                    <h3 class="text-lg font-black text-gray-900 mb-6 flex items-center gap-2">
                        <i class="bi bi-journal-text text-blue-600"></i>
                        Relato Ministerial
                    </h3>
                    <div class="prose prose-blue max-w-none text-gray-600 font-medium leading-relaxed">
                        {!! nl2br(e($report->ministerial_observations)) !!}
                    </div>
                </div>
            </div>

            <!-- Side Content -->
            <div class="space-y-8">
                <!-- Achievement Card -->
                <div class="bg-blue-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-blue-200">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                            <i class="bi bi-trophy text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="font-black uppercase tracking-widest text-xs text-blue-200">Alvos de Batismo</h4>
                            <p class="text-2xl font-black">{{ $report->baptized_count }} /
                                {{ $report->planned_baptism_count }}</p>
                        </div>
                    </div>
                    @php
                        $perc = $report->planned_baptism_count > 0 ? min(100, ($report->baptized_count / $report->planned_baptism_count) * 100) : 0;
                    @endphp
                    <div class="space-y-2">
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-blue-200">
                            <span>Atingimento</span>
                            <span>{{ round($perc) }}%</span>
                        </div>
                        <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-white rounded-full" style="width: {{ $perc }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Events List -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Eventos Realizados</h3>
                    </div>
                    <div class="p-8 space-y-6">
                        @foreach($report->events as $event)
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center font-black flex-shrink-0">
                                    {{ $event->count }}
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-black text-gray-900">{{ $event->type->name }}</h4>
                                    <p class="text-xs text-gray-500 font-medium">{{ $event->description ?: 'Sem observações.' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Meta Info -->
                <div
                    class="p-8 bg-gray-50 rounded-[2.5rem] border border-gray-100 flex flex-col items-center text-center space-y-3">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Relatório enviado
                        por</span>
                    @if($report->supervisor)
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($report->supervisor->name) }}&background=0D6EFD&color=fff"
                            class="w-12 h-12 rounded-full ring-4 ring-white shadow-md">
                        <span class="font-black text-gray-900">{{ $report->supervisor->name }}</span>
                    @endif
                    <span class="text-xs font-bold text-gray-400 italic">Em
                        {{ $report->created_at->format('d/m/Y \à\s H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection