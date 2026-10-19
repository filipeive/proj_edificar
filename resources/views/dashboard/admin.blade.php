@extends('layouts.app')

@push('styles')
    <style>
        .church-map .leaflet-container { min-height: 420px; z-index: 0; }
        .leaflet-draw-toolbar a { background-size: 300px 30px; }
    </style>
@endpush

@section('title', 'Dashboard Admin - Portal Life Church')
@section('page-title', 'Dashboard Administrativo')
@section('page-subtitle', 'Visão geral de todas as atividades e contribuições Eclesiásticas')

@section('content')
    <div class="space-y-5 sm:space-y-6">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gestão Local da Congregação</p>
                <h1 class="mt-0.5 sm:mt-1 text-xl sm:text-2xl font-black text-gray-950 dark:text-white tracking-tight">Panorama Eclesiástico</h1>
                <p class="mt-0.5 sm:mt-1 text-xs sm:text-sm text-gray-500 dark:text-zinc-400">Indicadores do mês corrente, células, eventos e atividades.</p>
            </div>
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2 w-full sm:w-auto">
                @if(app(\App\Services\CongregationContext::class)->hasEdificar())
                    <a href="{{ route('edificar.dashboard') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-zinc-900 px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-zinc-800 shadow-sm">
                        <i class="bi bi-bricks text-orange-400"></i> <span>Painel Edificar</span>
                    </a>
                @endif
                <a href="{{ route('services.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white dark:bg-zinc-900 dark:border-zinc-700 px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 dark:text-zinc-200 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-journal-plus text-orange-700"></i> <span>Novo Culto</span>
                </a>
                <a href="{{ route('users.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-person-plus"></i> <span>Novo Membro</span>
                </a>
            </div>
        </header>

        <!-- Indicadores Principais (Estilo Gestão Nacional) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <!-- Card: Membros Ativos -->
            <article class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/30 p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 dark:text-zinc-400 truncate">Membros Ativos</p>
                    <i class="bi bi-people text-orange-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 dark:text-white tracking-tight truncate">{{ $totalMembers }}</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 dark:text-zinc-500 mt-0.5 truncate"><i class="bi bi-graph-up text-orange-600 mr-1"></i>Comunidade ativa</p>
            </article>

            <!-- Card: Total Dízimos/Ofertas -->
            <article class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/30 p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 dark:text-zinc-400 truncate">Dízimos e Ofertas</p>
                    <i class="bi bi-cash-coin text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-base sm:text-xl font-black text-emerald-700 dark:text-emerald-400 tracking-tight truncate">
                    {{ number_format($totalContributed, 2, ',', '.') }} MT
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 dark:text-zinc-500 mt-0.5 truncate">
                    {{ $totalContributed == 0 ? 'Sem registos no mês' : 'Total eclesiástico do mês' }}
                </p>
            </article>

            <!-- Card: Próximos Eventos -->
            <article class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/30 p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 dark:text-zinc-400 truncate">Próximos Eventos</p>
                    <i class="bi bi-calendar-event text-blue-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 dark:text-white tracking-tight truncate">{{ count($upcomingEvents) }}</p>
                <a href="{{ route('events.index') }}" class="inline-flex items-center text-[10px] sm:text-[11px] font-bold text-orange-800 dark:text-orange-400 hover:underline mt-0.5 truncate">
                    Ver calendário <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </article>

            <!-- Card: Cultos Recentes -->
            <article class="rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/30 p-3 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 dark:text-zinc-400 truncate">Relatórios de Cultos</p>
                    <i class="bi bi-journal-bookmark text-amber-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 dark:text-white tracking-tight truncate">{{ count($recentServices) }}</p>
                <a href="{{ route('services.index') }}" class="inline-flex items-center text-[10px] sm:text-[11px] font-bold text-orange-800 dark:text-orange-400 hover:underline mt-0.5 truncate">
                    Ver todos <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </article>
        </div>

        <!-- Ações Rápidas Compactas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
            <a href="{{ route('events.create') }}" class="flex items-center gap-2.5 rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/30 p-3 shadow-sm hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/30 text-orange-700 dark:text-orange-400 flex items-center justify-center shrink-0">
                    <i class="bi bi-calendar-plus text-sm"></i>
                </div>
                <span class="text-xs font-bold text-gray-800 dark:text-zinc-200 truncate">Novo Evento</span>
            </a>
            <a href="{{ route('cells.create') }}" class="flex items-center gap-2.5 rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/30 p-3 shadow-sm hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <i class="bi bi-diagram-3 text-sm"></i>
                </div>
                <span class="text-xs font-bold text-gray-800 dark:text-zinc-200 truncate">Nova Célula</span>
            </a>
            <a href="{{ route('quarterly-reports.create') }}" class="flex items-center gap-2.5 rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/30 p-3 shadow-sm hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i class="bi bi-file-earmark-bar-graph text-sm"></i>
                </div>
                <span class="text-xs font-bold text-gray-800 dark:text-zinc-200 truncate">Relatório Trimestral</span>
            </a>
            <a href="{{ route('visitors.index') }}" class="flex items-center gap-2.5 rounded-xl border border-gray-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/30 p-3 shadow-sm hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <i class="bi bi-person-hearts text-sm"></i>
                </div>
                <span class="text-xs font-bold text-gray-800 dark:text-zinc-200 truncate">Visitantes</span>
            </a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Members by Zone -->
            <x-card title="Membros por Zona">
                <div class="h-[350px] relative">
                    <canvas id="zoneMembersChart"></canvas>
                </div>
            </x-card>

            <!-- Structure by Zone (Cells/Supervisions) -->
            <x-card title="Estrutura (Células e Supervisões)">
                <div class="h-[350px] relative">
                    <canvas id="zoneStructureChart"></canvas>
                </div>
            </x-card>
        </div>

        <!-- Mapa da Obra -->
        <x-card class="flex flex-col h-[600px] overflow-hidden group" compact="true">
            <x-slot name="header">
                <div class="flex items-center justify-between w-full">
                    <div>
                        <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] mb-2">
                            <i class="bi bi-map-fill"></i>
                            <span>Expansão do Reino</span>
                        </div>
                        <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">Mapeamento Geográfico</h2>
                    </div>
                </div>
            </x-slot>
            
            <div class="flex-1 p-6 md:p-8">
                <div id="church-map" data-church-map data-map-data="church-map-data"
                    class="church-map w-full h-[420px] rounded-2xl overflow-hidden border border-gray-200 dark:border-zinc-700"
                    aria-label="Mapa das zonas pastorais e células"></div>
                <script id="church-map-data" type="application/json">@json(['zones' => $mapZones])</script>
                <div class="flex flex-wrap items-center gap-5 pt-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                    <span class="inline-flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-teal-500"></span>Zona</span>
                    <span class="inline-flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span>Célula</span>
                    <a href="{{ route('zones.index') }}" class="ml-auto text-blue-600 hover:text-blue-700">Configurar zonas</a>
                    <a href="{{ route('cells.index') }}" class="text-blue-600 hover:text-blue-700">Configurar células</a>
                </div>
            </div>
        </x-card>

        <!-- Gestão Eclesiástica Stats (Enabled on Mobile) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Crescimento de Membros -->
            <x-card class="lg:col-span-2">
                <x-slot name="header">
                    <div class="flex items-center justify-between w-full">
                        <h3 class="text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Crescimento de Membros</h3>
                        <span class="px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-bold uppercase tracking-widest">Últimos 6 Meses</span>
                    </div>
                </x-slot>
                <div class="h-[350px] relative">
                    <canvas id="growthChart"></canvas>
                </div>
            </x-card>

            <!-- Recent Activity Feed -->
            <x-card id="recent-activity" title="Atividade Recente">
                <div class="space-y-5 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($recentActivity as $activity)
                        <div class="flex items-start gap-4 group">
                            <div class="w-10 h-10 rounded-xl {{ $activity['bg'] }} {{ $activity['color'] }} flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="bi {{ $activity['icon'] }} text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0 border-b border-gray-50 dark:border-zinc-800 pb-4 group-last:border-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="font-black text-gray-900 dark:text-zinc-100 text-sm truncate">
                                        {{ $activity['title'] }}</p>
                                    <span class="text-[9px] font-bold text-gray-400 dark:text-zinc-500 uppercase">{{ $activity['time']->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-zinc-400 line-clamp-2 leading-relaxed">
                                    {{ $activity['description'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-zinc-800 rounded-full flex items-center justify-center text-gray-300 dark:text-zinc-600 mb-4">
                                <i class="bi bi-activity text-3xl"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-400 dark:text-zinc-500">Nenhuma atividade recente</p>
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Próximos Eventos -->
            <x-card>
                <x-slot name="header">
                    <div class="flex items-center justify-between w-full">
                        <h3 class="text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Próximos Eventos</h3>
                        <a href="{{ route('events.index') }}" class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Calendário</a>
                    </div>
                </x-slot>
                <div class="space-y-6">
                    @forelse($upcomingEvents as $event)
                        <div class="flex items-center space-x-6 group">
                            <div class="bg-gray-50 dark:bg-zinc-800/50 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                <span class="block text-xl font-black leading-none text-gray-900 dark:text-zinc-100 group-hover:text-white">{{ $event->date->format('d') }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-zinc-400 group-hover:text-white">{{ $event->date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-gray-900 dark:text-zinc-100 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                    {{ $event->name }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-zinc-455 flex items-center mt-1">
                                    <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Life Church' }}
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-clock mr-1"></i> {{ $event->date->format('H:i') }}h
                                    @if($event->end_date)
                                        <span class="ml-1 text-blue-500 dark:text-blue-400 font-bold">até {{ $event->end_date->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            </div>
                            <span class="px-3 py-1 bg-gray-100 dark:bg-zinc-800 rounded-full text-[8px] font-black uppercase tracking-widest text-gray-500 dark:text-zinc-350">
                                {{ $event->eventType->name ?? 'Evento' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 dark:text-zinc-550 py-10">Nenhum evento programado.</p>
                    @endforelse
                </div>
            </x-card>

            <!-- Últimos Cultos -->
            <x-card>
                <x-slot name="header">
                    <div class="flex items-center justify-between w-full">
                        <h3 class="text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Relatórios de Cultos</h3>
                        <a href="{{ route('services.index') }}" class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todos</a>
                    </div>
                </x-slot>
                <div class="space-y-6">
                    @forelse($recentServices as $service)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-800/50 rounded-2xl hover:bg-white dark:hover:bg-zinc-850 hover:shadow-lg transition-all duration-300 border border-transparent hover:border-gray-100 dark:hover:border-zinc-800">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-white dark:bg-zinc-900 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 shadow-sm">
                                    <i class="bi bi-journal-text text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-zinc-100">{{ $service->name }}</h4>
                                    <p class="text-[10px] text-gray-400 dark:text-zinc-500 font-bold uppercase tracking-widest">
                                        {{ $service->date->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-gray-900 dark:text-zinc-100">{{ $service->total_participation ?? 0 }}</p>
                                <p class="text-[8px] text-gray-400 dark:text-zinc-500 font-bold uppercase tracking-widest">Presentes</p>
                            </div>
                            <a href="{{ route('services.show', $service->id) }}" class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">
                                <i class="bi bi-arrow-right ml-1"></i> Detalhes
                            </a>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 dark:text-zinc-550 py-10">Nenhum relatório de culto registado.</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        <!-- Chart Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Zone Members Chart (Renamed from zoneChart)
                const zoneMemCtx = document.getElementById('zoneMembersChart').getContext('2d');
                new Chart(zoneMemCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($zoneStats->pluck('name')),
                        datasets: [{
                            label: 'Membros',
                            data: @json($zoneStats->pluck('total')),
                            backgroundColor: 'rgba(249, 115, 22, 0.85)', // Brand Orange
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { drawBorder: false, color: document.documentElement.getAttribute('data-theme') === 'dark' ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Zone Structure Chart (New)
                const zoneStrCtx = document.getElementById('zoneStructureChart').getContext('2d');
                new Chart(zoneStrCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($zoneStructures->pluck('name')),
                        datasets: [
                            {
                                label: 'Células',
                                data: @json($zoneStructures->pluck('cells')),
                                backgroundColor: 'rgba(249, 115, 22, 0.85)', // Brand Orange
                                borderRadius: 6,
                            },
                            {
                                label: 'Supervisões',
                                data: @json($zoneStructures->pluck('supervisions')),
                                backgroundColor: 'rgba(68, 64, 60, 0.85)', // Secondary Stone
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, grid: { drawBorder: false, color: document.documentElement.getAttribute('data-theme') === 'dark' ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Growth Chart
                const growthCtx = document.getElementById('growthChart').getContext('2d');
                const growthGradient = growthCtx.createLinearGradient(0, 0, 0, 400);
                growthGradient.addColorStop(0, 'rgba(249, 115, 22, 0.25)');
                growthGradient.addColorStop(1, 'rgba(249, 115, 22, 0)');

                new Chart(growthCtx, {
                    type: 'line',
                    data: {
                        labels: @json($growthLabels),
                        datasets: [{
                            label: 'Total de Membros',
                            data: @json($growthData),
                            backgroundColor: growthGradient,
                            borderColor: 'rgba(249, 115, 22, 1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(249, 115, 22, 1)',
                            pointBorderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: false, grid: { color: document.documentElement.getAttribute('data-theme') === 'dark' ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            });
        </script>
        {{-- Removed Leaflet map initialization script --}}
        <style>
            [data-theme="dark"] .custom-scrollbar::-webkit-scrollbar-track {
                background: #1f2937;
            }

            [data-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #4b5563;
            }

            [data-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #6b7280;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }
        </style>
    </div>
@endsection