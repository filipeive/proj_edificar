@extends('layouts.app')

@push('styles')
    <style>
        /* Removed #zonesMap and .leaflet-container styles */
    </style>
@endpush

@section('title', 'Dashboard Admin - Portal Life Church')
@section('page-title', 'Dashboard Administrativo')
@section('page-subtitle', 'Visão geral de todas as atividades e contribuições Eclesiásticas')

@section('content')
    <div class="space-y-6 md:space-y-8">
        <!-- QW-05: Métricas agora visíveis em mobile (era hidden md:grid) -->
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            <!-- Card: Membros Ativos -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-people-fill text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Comunidade</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Membros Ativos
                    </p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">{{ $totalMembers }}
                    </p>
                    <div class="flex items-center mt-3 text-[10px]">
                        <span class="text-orange-500 dark:text-orange-400 font-bold flex items-center">
                            <i class="bi bi-graph-up mr-1 text-sm"></i> Crescendo
                        </span>
                        <span class="text-gray-400 dark:text-gray-500 ml-1.5">este mês</span>
                    </div>
                </div>
            </div>

            <!-- Card: Total Dízimos/Ofertas -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-cash-coin text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Eclesiástico</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Dízimos e Ofertas
                    </p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">
                        {{ number_format($totalContributed, 2, ',', '.') }} MT
                    </p>
                    <div class="flex items-center mt-3 text-[10px]">
                        @if($totalContributed == 0)
                            <span class="text-yellow-600 dark:text-yellow-400 font-bold flex items-center">
                                <i class="bi bi-info-circle mr-1"></i> Sem registos
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">Total do Mês (Calendário)</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card: Próximos Eventos -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-calendar-event-fill text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Agenda</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Próximos Eventos
                    </p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">
                        {{ count($upcomingEvents) }}</p>
                    <a href="{{ route('events.index') }}"
                        class="inline-flex items-center mt-3 text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">
                        Ver Calendário <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Card: Cultos Recentes -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-journal-bookmark-fill text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Cultos</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Relatórios</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">
                        {{ count($recentServices) }}</p>
                    <a href="{{ route('services.index') }}"
                        class="inline-flex items-center mt-3 text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">
                        Ver Todos <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <!-- Link to Edificar Dashboard -->
                <a href="{{ route('edificar.dashboard') }}"
                    class="bg-zinc-950 dark:bg-zinc-900 border border-zinc-950 dark:border-zinc-850 hover:bg-zinc-900 dark:hover:bg-zinc-800/80 transition-all p-4 rounded-2xl text-center group shadow-sm hover:shadow-md">
                    <div
                        class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                        <i class="bi bi-bricks text-lg"></i>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-white">Painel Edificar</span>
                </a>

                <!-- Novo Evento -->
                <a href="{{ route('events.create') }}"
                    class="bg-white dark:bg-zinc-900/30 p-4 rounded-2xl border border-gray-100 dark:border-zinc-850 shadow-sm hover:shadow-md transition-all text-center group">
                    <div
                        class="w-10 h-10 bg-orange-50 dark:bg-orange-950/20 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 mx-auto mb-3 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                        <i class="bi bi-calendar-plus text-lg"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-500 dark:text-zinc-400 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Novo Evento</span>
                </a>

                <!-- Novo Culto -->
                <a href="{{ route('services.create') }}"
                    class="bg-white dark:bg-zinc-900/30 p-4 rounded-2xl border border-gray-100 dark:border-zinc-850 shadow-sm hover:shadow-md transition-all text-center group">
                    <div
                        class="w-10 h-10 bg-orange-50 dark:bg-orange-950/20 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 mx-auto mb-3 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                        <i class="bi bi-journal-plus text-lg"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-500 dark:text-zinc-400 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Novo Culto</span>
                </a>

                <!-- Novo Membro -->
                <a href="{{ route('users.create') }}"
                    class="bg-white dark:bg-zinc-900/30 p-4 rounded-2xl border border-gray-100 dark:border-zinc-850 shadow-sm hover:shadow-md transition-all text-center group">
                    <div
                        class="w-10 h-10 bg-orange-50 dark:bg-orange-950/20 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 mx-auto mb-3 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                        <i class="bi bi-person-plus text-lg"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-500 dark:text-zinc-400 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Novo Membro</span>
                </a>

                <!-- Nova Célula -->
                <a href="{{ route('cells.create') }}"
                    class="bg-white dark:bg-zinc-900/30 p-4 rounded-2xl border border-gray-100 dark:border-zinc-850 shadow-sm hover:shadow-md transition-all text-center group">
                    <div
                        class="w-10 h-10 bg-orange-50 dark:bg-orange-950/20 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 mx-auto mb-3 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                        <i class="bi bi-diagram-3 text-lg"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-500 dark:text-zinc-400 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Nova Célula</span>
                </a>

                <!-- Relatório Trimestral -->
                <a href="{{ route('quarterly-reports.create') }}"
                    class="bg-white dark:bg-zinc-900/30 p-4 rounded-2xl border border-gray-100 dark:border-zinc-850 shadow-sm hover:shadow-md transition-all text-center group">
                    <div
                        class="w-10 h-10 bg-orange-50 dark:bg-orange-950/20 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 mx-auto mb-3 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                        <i class="bi bi-file-earmark-bar-graph text-lg"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-500 dark:text-zinc-400 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Relat. Trimestral</span>
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
            
            <div class="flex-1 p-6 md:p-8 flex flex-col justify-between">
                <!-- Placeholder Ilustrativo do Mapa -->
                <div class="flex-1 bg-gray-50 dark:bg-zinc-800/40 rounded-[2rem] border-2 border-dashed border-gray-200 dark:border-zinc-800 flex flex-col items-center justify-center text-center p-12 relative overflow-hidden group/map transition-all hover:bg-gray-100/50 dark:hover:bg-zinc-800/20">
                    <div class="relative z-10">
                        <i class="bi bi-geo-alt-fill text-6xl text-gray-200 dark:text-zinc-700 mb-6 block transform group-hover/map:scale-110 group-hover/map:rotate-12 transition-all duration-500"></i>
                        <h3 class="text-xl font-black text-gray-400 dark:text-zinc-500 mb-2 uppercase tracking-widest leading-none">
                            Mapa Interativo
                        </h3>
                        <p class="text-xs font-bold text-gray-400 dark:text-zinc-650 italic">Visualize a presença da Life Church em Quelimane...</p>
                    </div>

                    <!-- Mini estatísticas flutuantes no mapa placeholder -->
                    <div class="absolute top-10 right-10 flex flex-col gap-3">
                        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur px-4 py-2 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-800 flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                            <span class="text-[10px] font-black text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Norte: 12 Células</span>
                        </div>
                        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur px-4 py-2 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-800 flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></div>
                            <span class="text-[10px] font-black text-gray-500 dark:text-zinc-400 uppercase tracking-widest">Centro: 25 Células</span>
                        </div>
                    </div>
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