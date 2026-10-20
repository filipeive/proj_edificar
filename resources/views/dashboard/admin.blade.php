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
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gestão Local da Congregação</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Panorama Eclesiástico</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Indicadores do mês corrente, células, eventos e atividades.</p>
            </div>
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2 w-full sm:w-auto">
                @if(app(\App\Services\CongregationContext::class)->hasEdificar())
                    <a href="{{ route('edificar.dashboard') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-gray-800 shadow-sm">
                        <i class="bi bi-bricks text-orange-400"></i> <span>Painel Edificar</span>
                    </a>
                @endif
                <a href="{{ route('services.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-journal-plus text-orange-700"></i> <span>Novo Culto</span>
                </a>
                <a href="{{ route('users.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-person-plus"></i> <span>Novo Membro</span>
                </a>
            </div>
        </header>

        <!-- Indicadores Principais (Estilo Gestão Nacional) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <!-- Card: Membros Ativos -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Membros Ativos</p>
                    <i class="bi bi-people text-orange-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums truncate">{{ $totalMembers }}</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate"><i class="bi bi-graph-up text-orange-600 mr-1"></i>Comunidade ativa</p>
            </article>

            <!-- Card: Total Dízimos/Ofertas -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Dízimos e Ofertas</p>
                    <i class="bi bi-cash-coin text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums truncate">
                    {{ number_format($totalContributed, 2, ',', '.') }} MT
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">
                    {{ $totalContributed == 0 ? 'Sem registos no mês' : 'Total eclesiástico do mês' }}
                </p>
            </article>

            <!-- Card: Próximos Eventos -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Próximos Eventos</p>
                    <i class="bi bi-calendar-event text-blue-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums truncate">{{ count($upcomingEvents) }}</p>
                <a href="{{ route('events.index') }}" class="inline-flex items-center text-[10px] sm:text-[11px] font-bold text-orange-800 hover:underline mt-0.5 truncate">
                    Ver calendário <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </article>

            <!-- Card: Cultos Recentes -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Relatórios de Cultos</p>
                    <i class="bi bi-journal-bookmark text-amber-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums truncate">{{ count($recentServices) }}</p>
                <a href="{{ route('services.index') }}" class="inline-flex items-center text-[10px] sm:text-[11px] font-bold text-orange-800 hover:underline mt-0.5 truncate">
                    Ver todos <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </article>
        </div>

        <!-- Ações Rápidas Compactas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
            <a href="{{ route('events.create') }}" class="flex items-center gap-2.5 rounded-xl border border-gray-200 bg-white p-3 shadow-sm hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center shrink-0">
                    <i class="bi bi-calendar-plus text-sm"></i>
                </div>
                <span class="text-xs font-bold text-gray-800 truncate">Novo Evento</span>
            </a>
            <a href="{{ route('cells.create') }}" class="flex items-center gap-2.5 rounded-xl border border-gray-200 bg-white p-3 shadow-sm hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                    <i class="bi bi-diagram-3 text-sm"></i>
                </div>
                <span class="text-xs font-bold text-gray-800 truncate">Nova Célula</span>
            </a>
            <a href="{{ route('quarterly-reports.create') }}" class="flex items-center gap-2.5 rounded-xl border border-gray-200 bg-white p-3 shadow-sm hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                    <i class="bi bi-file-earmark-bar-graph text-sm"></i>
                </div>
                <span class="text-xs font-bold text-gray-800 truncate">Relatório Trimestral</span>
            </a>
            <a href="{{ route('visitors.index') }}" class="flex items-center gap-2.5 rounded-xl border border-gray-200 bg-white p-3 shadow-sm hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                    <i class="bi bi-person-hearts text-sm"></i>
                </div>
                <span class="text-xs font-bold text-gray-800 truncate">Visitantes</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Members by Zone -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-sm font-black text-gray-900">Membros por Zona</h2>
                    <p class="text-xs text-gray-500">Distribuição territorial ativa</p>
                </div>
                <div class="h-64 relative">
                    <canvas id="zoneMembersChart"></canvas>
                </div>
            </section>

            <!-- Structure by Zone (Cells/Supervisions) -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-sm font-black text-gray-900">Estrutura (Células e Supervisões)</h2>
                    <p class="text-xs text-gray-500">Unidades eclesiásticas por zona</p>
                </div>
                <div class="h-64 relative">
                    <canvas id="zoneStructureChart"></canvas>
                </div>
            </section>
        </div>

        <!-- Mapa da Obra -->
        <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-black text-gray-900">Mapeamento Geográfico</h2>
                    <p class="text-xs text-gray-500">Expansão do Reino — Zonas pastorais e células.</p>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                    <span class="inline-flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>Zona</span>
                    <span class="inline-flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>Célula</span>
                    <a href="{{ route('zones.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Configurar zonas</a>
                    <a href="{{ route('cells.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Configurar células</a>
                </div>
            </div>
            <div>
                <div id="church-map" data-church-map data-map-data="church-map-data"
                    class="church-map w-full h-[420px] rounded-lg overflow-hidden border border-gray-200 bg-gray-100"
                    aria-label="Mapa das zonas pastorais e células"></div>
                <script id="church-map-data" type="application/json">@json(['zones' => $mapZones])</script>
            </div>
        </section>

        <!-- Gestão Eclesiástica Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Crescimento de Membros -->
            <section class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Crescimento de Membros</h3>
                        <p class="text-xs text-gray-500">Evolução nos últimos 6 meses</p>
                    </div>
                    <span class="px-2.5 py-1 bg-orange-50 text-orange-700 rounded-lg text-[10px] font-black uppercase tracking-wider">Últimos 6 Meses</span>
                </div>
                <div class="h-64 relative">
                    <canvas id="growthChart"></canvas>
                </div>
            </section>

            <!-- Recent Activity Feed -->
            <section id="recent-activity" class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-sm font-black text-gray-900">Atividade Recente</h3>
                    <p class="text-xs text-gray-500">Registos e ações recentes</p>
                </div>
                <div class="space-y-3 max-h-[260px] overflow-y-auto pr-1 custom-scrollbar">
                    @forelse($recentActivity as $activity)
                        <div class="flex items-start gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                            <div class="w-8 h-8 rounded-lg {{ $activity['bg'] }} {{ $activity['color'] }} flex items-center justify-center shrink-0">
                                <i class="bi {{ $activity['icon'] }} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-bold text-gray-900 text-xs truncate">{{ $activity['title'] }}</p>
                                    <span class="text-[10px] font-semibold text-gray-400 shrink-0">{{ $activity['time']->diffForHumans() }}</span>
                                </div>
                                <p class="text-[11px] text-gray-500 line-clamp-2 mt-0.5">
                                    {{ $activity['description'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <p class="text-xs font-semibold text-gray-400">Nenhuma atividade recente</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Próximos Eventos -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Próximos Eventos</h3>
                        <p class="text-xs text-gray-500">Agenda ministerial</p>
                    </div>
                    <a href="{{ route('events.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Calendário</a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingEvents as $event)
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                            <div class="bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-center min-w-[54px]">
                                <span class="block text-base font-black leading-none text-gray-900 tabular-nums">{{ $event->date->format('d') }}</span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500">{{ $event->date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $event->name }}</h4>
                                <p class="text-[11px] text-gray-500 flex items-center mt-0.5 truncate">
                                    <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Life Church' }}
                                    <span class="mx-1.5">•</span>
                                    <i class="bi bi-clock mr-1"></i> {{ $event->date->format('H:i') }}h
                                    @if($event->end_date)
                                        <span class="ml-1 text-blue-700 font-bold">até {{ $event->end_date->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 bg-gray-100 rounded-full text-[9px] font-black uppercase tracking-wider text-gray-600">
                                {{ $event->eventType->name ?? 'Evento' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-xs text-gray-500 py-6">Nenhum evento programado.</p>
                    @endforelse
                </div>
            </section>

            <!-- Últimos Cultos -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Relatórios de Cultos</h3>
                        <p class="text-xs text-gray-500">Celebrações recentes</p>
                    </div>
                    <a href="{{ route('services.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todos</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentServices as $service)
                        <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 bg-orange-50 rounded-lg flex items-center justify-center text-orange-700 shrink-0">
                                    <i class="bi bi-journal-text text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $service->name }}</h4>
                                    <p class="text-[11px] text-gray-500 tabular-nums">
                                        {{ $service->date->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-black text-gray-950 tabular-nums">{{ $service->total_participation ?? 0 }}</p>
                                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Presentes</p>
                            </div>
                            <a href="{{ route('services.show', $service->id) }}" class="shrink-0 text-xs font-bold text-orange-800 hover:underline">
                                Detalhes
                            </a>
                        </div>
                    @empty
                        <p class="text-center text-xs text-gray-500 py-6">Nenhum relatório de culto registado.</p>
                    @endforelse
                </div>
            </section>
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
        <style>
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        </style>
    </div>
@endsection
