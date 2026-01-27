@extends('layouts.app')

@section('title', 'Detalhes do Relatório - Portal Life Church')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        @can('update', $quarterlyReport)
            <a href="{{ route('quarterly-reports.edit', $quarterlyReport) }}"
                class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
                title="Editar dados">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="space-y-8">
        <!-- Header Section (Hidden md part for mobile optimization) -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">
                    <a href="{{ route('quarterly-reports.index') }}" class="hover:underline">Relatórios Trimestrais</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span>Ver Detalhes</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">
                    Trimestre {{ $quarterlyReport->quarter }}/{{ $quarterlyReport->year }}
                </h1>
                <p class="text-gray-500 font-bold">
                    Supervisão: <span class="text-blue-600">{{ $quarterlyReport->supervision->name ?? 'N/A' }}</span> |
                    Zona: {{ $quarterlyReport->zone->name ?? 'N/A' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 hidden md:flex">
                @can('update', $quarterlyReport)
                    <a href="{{ route('quarterly-reports.edit', $quarterlyReport) }}"
                        class="flex items-center bg-blue-50 text-blue-600 px-6 py-4 rounded-2xl hover:bg-blue-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest shadow-sm">
                        <i class="bi bi-pencil-square text-lg mr-2"></i>
                        Editar Dados
                    </a>
                @endcan
                <a href="{{ route('quarterly-reports.index') }}"
                    class="flex items-center bg-gray-50 text-gray-400 px-6 py-4 rounded-2xl hover:bg-gray-100 transition-all font-black text-xs uppercase tracking-widest">
                    <i class="bi bi-arrow-left text-lg mr-2"></i>
                    Voltar
                </a>
            </div>

            <div class="md:hidden">
                <a href="{{ route('quarterly-reports.index') }}"
                    class="flex items-center bg-gray-50 text-gray-400 px-6 py-4 rounded-2xl hover:bg-gray-100 transition-all font-black text-xs uppercase tracking-widest">
                    <i class="bi bi-arrow-left text-lg mr-2"></i>
                    Voltar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            <!-- Left Column: Primary Content -->
            <div class="xl:col-span-8 space-y-8">
                <!-- Data Grid -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-8 flex items-center gap-2">
                        <i class="bi bi-bar-chart-fill text-blue-600"></i>
                        Métricas de Crescimento e Estrutura
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        @php
                            $stats = [
                                ['label' => 'Pastores', 'value' => $quarterlyReport->pastors_count, 'icon' => 'bi-person-workspace', 'color' => 'red'],
                                ['label' => 'Supervisores', 'value' => $quarterlyReport->supervisors_count, 'icon' => 'bi-person-check', 'color' => 'purple'],
                                ['label' => 'Líderes Ativos', 'value' => $quarterlyReport->leaders_count, 'icon' => 'bi-person-badge', 'color' => 'blue'],
                                ['label' => 'Células Totais', 'value' => $quarterlyReport->cells_count, 'icon' => 'bi-grid-3x3-gap', 'color' => 'purple'],
                                ['label' => 'Timóteos (Aux)', 'value' => $quarterlyReport->timoteos_count, 'icon' => 'bi-award', 'color' => 'indigo'],
                                ['label' => 'Membros Arrolados', 'value' => $quarterlyReport->members_count, 'icon' => 'bi-people', 'color' => 'green'],
                                ['label' => 'Visitantes', 'value' => $quarterlyReport->visitors_count, 'icon' => 'bi-person-plus', 'color' => 'orange'],
                                ['label' => 'Média Participação', 'value' => $quarterlyReport->participants_count, 'icon' => 'bi-graph-up', 'color' => 'orange'],
                                ['label' => 'Novas Conversões', 'value' => $quarterlyReport->saved_count, 'icon' => 'bi-heart-pulse', 'color' => 'red'],
                            ];
                        @endphp
                        @foreach($stats as $stat)
                            <div
                                class="p-6 bg-gray-50/50 rounded-[2rem] border border-gray-100/50 hover:bg-white hover:shadow-md transition-all group">
                                <i
                                    class="bi {{ $stat['icon'] }} text-{{ $stat['color'] }}-500 text-xl block mb-3 opacity-60 group-hover:opacity-100 transition-all"></i>
                                <span
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-[0.15em] block mb-1 group-hover:text-{{ $stat['color'] }}-600 transition-colors">{{ $stat['label'] }}</span>
                                <span class="text-2xl font-black text-gray-900">{{ $stat['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Health Indicators -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                            <i class="bi bi-activity text-blue-600"></i>
                            Avaliação de Saúde Ministerial
                        </h3>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Escala 0-3</span>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                        @php
                            $assessments = [
                                'discipleship_score' => 'Discipulado um a um',
                                'evangelism_strategy' => 'Estratégia de Evangelismo',
                                'consolidation_growth' => 'Consolidação de Novos',
                                'pastoral_score' => 'Cuidado Pastoral (Líderes)',
                                'visitation_routine' => 'Rotina de Visitação',
                                'leader_support' => 'Apoio aos Líderes',
                                'cell_participation_score' => 'Participação em Células',
                                'service_participation_score' => 'Presença nos Cultos',
                                'tadium_participation' => 'Envolvimento no TADEL',
                                'communion_in_cells_score' => 'Comunhão Interna',
                                'relationship_building_score' => 'Integração de Novos',
                                'prayer_intercession_score' => 'Vida de Oração'
                            ];
                        @endphp
                        @foreach($assessments as $field => $label)
                            <div class="space-y-4 group">
                                <div class="flex justify-between items-end">
                                    <span
                                        class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ $label }}</span>
                                    <span
                                        class="text-lg font-black text-blue-600 tracking-tighter">{{ $quarterlyReport->$field }}<span
                                            class="text-[10px] text-gray-400 ml-0.5 opacity-50">/3</span></span>
                                </div>
                                <div class="h-3 bg-gray-100 rounded-full overflow-hidden p-0.5">
                                    <div class="h-full bg-blue-600 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(37,99,235,0.3)]"
                                        style="width: {{ ($quarterlyReport->$field / 3) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Observations -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="bi bi-journal-text text-blue-600"></i>
                        Parecer Ministerial do Supervisor
                    </h3>
                    <div
                        class="prose prose-blue max-w-none text-gray-600 font-medium leading-relaxed bg-gray-50/50 p-6 rounded-3xl border border-gray-100 italic">
                        {!! nl2br(e($quarterlyReport->ministerial_observations ?? 'Nenhum detalhe adicional fornecido.')) !!}
                    </div>
                </div>

                <!-- Visual Charts Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Saúde Ministerial
                            (Radar)</h3>
                        <div class="aspect-square relative flex items-center justify-center">
                            <canvas id="healthRadarChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Tendência de Estrutura
                        </h3>
                        <div class="aspect-square relative flex items-center justify-center">
                            <canvas id="structureBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar Stats -->
            <div class="xl:col-span-4 space-y-8">
                <!-- Baptism Target Card -->
                <div class="bg-blue-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-blue-100 group">
                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-500 text-blue-100 border border-white/10 shadow-inner">
                            <i class="bi bi-droplet-half"></i>
                        </div>
                        <div>
                            <span
                                class="text-[10px] font-black text-blue-200 uppercase tracking-[0.2em] block mb-1">Batismos
                                em Águas</span>
                            <p class="text-3xl font-black tracking-tighter">{{ $quarterlyReport->baptized_count }} <span
                                    class="text-sm opacity-50 font-normal">REALIZADOS</span></p>
                        </div>
                    </div>

                    @php
                        $target = $quarterlyReport->planned_baptism_count ?: 1;
                        $perc = min(100, ($quarterlyReport->baptized_count / $target) * 100);
                    @endphp

                    <div class="space-y-4">
                        <div
                            class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-blue-100">
                            <span>Meta do Período: {{ $quarterlyReport->planned_baptism_count }}</span>
                            <span class="bg-white/20 px-2 py-0.5 rounded-full">{{ round($perc) }}%</span>
                        </div>
                        <div class="h-4 bg-white/10 rounded-full overflow-hidden p-1 border border-white/5">
                            <div class="h-full bg-white rounded-full shadow-[0_0_15px_white]" style="width: {{ $perc }}%">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Events Activity -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Fluxo de Atividades /
                            Eventos</h3>
                    </div>
                    <div class="p-8 space-y-6">
                        @forelse($quarterlyReport->events as $event)
                            <div class="flex items-start gap-4 group">
                                <div
                                    class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center font-black flex-shrink-0 border border-purple-100 shadow-sm group-hover:bg-purple-600 group-hover:text-white transition-all">
                                    {{ $event->count }}
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-xs font-black text-gray-900 uppercase tracking-tight">
                                        {{ $event->eventType->name ?? 'Evento' }}
                                    </h4>
                                    <p class="text-[10px] text-gray-500 font-bold leading-relaxed line-clamp-2 italic">
                                        {{ $event->description ?: 'Atividade padrão concluída sem ressalvas.' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <i class="bi bi-calendar-x text-3xl text-gray-200 block mb-2"></i>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nenhum evento
                                    registrado</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Submission Audit -->
                <div
                    class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 flex flex-col items-center text-center space-y-4">
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($quarterlyReport->supervisor->name ?? 'U') }}&background=0D6EFD&color=fff&bold=true"
                            class="w-16 h-16 rounded-[1.5rem] shadow-xl ring-4 ring-white">
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 border-4 border-gray-50 rounded-full">
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Submetido
                            Responsavelmente por</span>
                        <span
                            class="font-black text-gray-900 uppercase tracking-tight">{{ $quarterlyReport->supervisor->name ?? 'Supervisor do Sistema' }}</span>
                    </div>
                    <div class="bg-white px-4 py-2 rounded-2xl border border-gray-100 shadow-sm">
                        <span class="text-[10px] font-bold text-gray-500 flex items-center gap-2">
                            <i class="bi bi-shield-check text-blue-600"></i>
                            Em
                            {{ $quarterlyReport->submitted_at ? $quarterlyReport->submitted_at->format('d/m/Y \à\s H:i') : $quarterlyReport->created_at->format('d/m/Y \à\s H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Radar Chart for Health Indicators
                const radarCtx = document.getElementById('healthRadarChart').getContext('2d');
                new Chart(radarCtx, {
                    type: 'radar',
                    data: {
                        labels: [
                            'Discipulado', 'Pastoreio', 'Freq. Células',
                            'Freq. Cultos', 'Koinonia', 'Consolidação', 'Oração'
                        ],
                        datasets: [{
                            label: 'Pontuação Real',
                            data: [
                                                            {{ $quarterlyReport->discipleship_score }},
                                                            {{ $quarterlyReport->pastoral_score }},
                                                            {{ $quarterlyReport->cell_participation_score }},
                                                            {{ $quarterlyReport->service_participation_score }},
                                                            {{ $quarterlyReport->communion_in_cells_score }},
                                                            {{ $quarterlyReport->relationship_building_score }},
                                {{ $quarterlyReport->prayer_intercession_score }}
                            ],
                            backgroundColor: 'rgba(37, 99, 235, 0.2)',
                            borderColor: 'rgb(37, 99, 235)',
                            pointBackgroundColor: 'rgb(37, 99, 235)',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgb(37, 99, 235)'
                        }, {
                            label: 'Meta Mínima',
                            data: [2, 2, 2, 2, 2, 2, 2],
                            backgroundColor: 'rgba(209, 213, 219, 0.1)',
                            borderColor: 'rgba(209, 213, 219, 0.5)',
                            borderDash: [5, 5],
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 3,
                                ticks: { stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });

                // Bar Chart for Structure
                const barCtx = document.getElementById('structureBarChart').getContext('2d');
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Líderes', 'Células', 'Timóteos', 'Membros'],
                        datasets: [{
                            label: 'Quantidade',
                            data: [
                                                            {{ $quarterlyReport->leaders_count }},
                                                            {{ $quarterlyReport->cells_count }},
                                                            {{ $quarterlyReport->timoteos_count }},
                                {{ $quarterlyReport->members_count }}
                            ],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.6)',
                                'rgba(147, 51, 234, 0.6)',
                                'rgba(99, 102, 241, 0.6)',
                                'rgba(34, 197, 94, 0.6)'
                            ],
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
