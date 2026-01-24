@extends('layouts.app')

@section('title', 'Dashboard Admin - Portal Life Church')
@section('page-title', 'Dashboard Administrativo')
@section('page-subtitle', 'Visão geral de todas as atividades e contribuições Eclesiásticas')

@section('content')
    <!-- Métricas Eclesiásticas (Escondidas no Mobile) -->
    <div class="hidden md:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card: Membros Ativos -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-50 p-4 rounded-2xl group-hover:bg-blue-600 transition-colors duration-500">
                    <i
                        class="bi bi-people-fill text-blue-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Comunidade</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Membros Ativos</p>
                <p class="text-3xl font-black text-gray-900 mt-2 tracking-tighter">{{ $totalMembers }}</p>
                <div class="flex items-center mt-4 text-xs">
                    <span class="text-blue-500 font-bold flex items-center">
                        <i class="bi bi-graph-up text-lg"></i> Crescendo
                    </span>
                    <span class="text-gray-400 ml-2">este mês</span>
                </div>
            </div>
        </div>

        <!-- Card: Total Dízimos/Ofertas -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-50 p-4 rounded-2xl group-hover:bg-green-600 transition-colors duration-500">
                    <i
                        class="bi bi-cash-coin text-green-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Eclesiástico</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Dízimos e Ofertas</p>
                <p class="text-3xl font-black text-gray-900 mt-2 tracking-tighter">
                    {{ number_format($totalContributed, 2, ',', '.') }} MT</p>
                <div class="flex items-center mt-4 text-xs">
                    @if($totalContributed == 0)
                        <span class="text-yellow-600 font-bold flex items-center">
                            <i class="bi bi-info-circle mr-1"></i> Sem registos este mês
                        </span>
                    @else
                        <span class="text-gray-400">Total do Mês (Calendário)</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card: Próximos Eventos -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-50 p-4 rounded-2xl group-hover:bg-purple-600 transition-colors duration-500">
                    <i
                        class="bi bi-calendar-event-fill text-purple-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Agenda</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Próximos Eventos</p>
                <p class="text-3xl font-black text-gray-900 mt-2 tracking-tighter">{{ count($upcomingEvents) }}</p>
                <a href="{{ route('events.index') }}"
                    class="inline-flex items-center mt-4 text-xs font-black text-purple-600 uppercase tracking-widest hover:text-purple-700">
                    Ver Calendário <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Card: Cultos Recentes -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-orange-50 p-4 rounded-2xl group-hover:bg-orange-600 transition-colors duration-500">
                    <i
                        class="bi bi-journal-bookmark-fill text-orange-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Cultos</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Relatórios</p>
                <p class="text-3xl font-black text-gray-900 mt-2 tracking-tighter">{{ count($recentServices) }}</p>
                <a href="{{ route('services.index') }}"
                    class="inline-flex items-center mt-4 text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">
                    Ver Todos <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h3 class="text-xl font-black text-gray-900 tracking-tight mb-6 flex items-center">
            <i class="bi bi-lightning-charge-fill text-orange-600 mr-3"></i> Ações Rápidas
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <!-- Link to Edificar Dashboard -->
            <a href="{{ route('edificar.dashboard') }}"
                class="bg-blue-600 p-4 rounded-2xl border border-blue-600 shadow-lg shadow-blue-200 hover:shadow-xl hover:bg-blue-700 transition-all text-center group">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white mx-auto mb-3">
                    <i class="bi bi-bricks"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white">Painel Edificar</span>
            </a>

            <a href="{{ route('events.create') }}"
                class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 mx-auto mb-3 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <i class="bi bi-calendar-plus"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Novo Evento</span>
            </a>
            <a href="{{ route('services.create') }}"
                class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mx-auto mb-3 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="bi bi-journal-plus"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Novo Culto</span>
            </a>
            <a href="{{ route('users.create') }}"
                class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600 mx-auto mb-3 group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <i class="bi bi-person-plus"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Novo Membro</span>
            </a>
            <a href="{{ route('cells.create') }}"
                class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 mx-auto mb-3 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Nova Célula</span>
            </a>
            <a href="{{ route('quarterly-reports.create') }}"
                class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div
                    class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-600 mx-auto mb-3 group-hover:bg-red-600 group-hover:text-white transition-colors">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Relat. Trimestral</span>
            </a>
        </div>
    </div>

    <!-- Geo & Structure Charts (Escondidos no Mobile) -->
    <div class="hidden md:grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Members by Zone -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8">Membros por Zona</h3>
            <div class="h-[350px] relative">
                <canvas id="zoneMembersChart"></canvas>
            </div>
        </div>

        <!-- Structure by Zone (Cells/Supervisions) -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8">Estrutura (Células e Supervisões)</h3>
            <div class="h-[350px] relative">
                <canvas id="zoneStructureChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Map Placeholder (Escondido no Mobile) -->
    <div class="hidden md:block mb-8">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8 flex items-center">
                <i class="bi bi-map-fill text-blue-600 mr-3"></i> Mapa de Quelimane
            </h3>
            <div
                class="bg-blue-50/50 rounded-3xl h-[400px] flex flex-col items-center justify-center border-2 border-dashed border-blue-200">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 mb-4">
                    <i class="bi bi-geo-alt-fill text-4xl"></i>
                </div>
                <h4 class="text-lg font-black text-gray-900">Mapeamento em Breve</h4>
                <p class="text-gray-500 text-sm max-w-md text-center mt-2">
                    Estamos preparando o mapeamento geográfico das zonas e células na cidade de Quelimane.
                </p>
                <div class="mt-6 flex gap-2">
                    <span
                        class="px-3 py-1 bg-white rounded-lg text-xs font-bold text-gray-600 shadow-sm border border-gray-100">10
                        Zonas</span>
                    <span
                        class="px-3 py-1 bg-white rounded-lg text-xs font-bold text-gray-600 shadow-sm border border-gray-100">{{ $totalMembers }}
                        Membros</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestão Eclesiástica Stats (Escondido no Mobile) -->
    <div class="hidden md:grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Crescimento de Membros -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Crescimento de Membros</h3>
                <span
                    class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-xs font-bold uppercase tracking-widest">Últimos
                    6 Meses</span>
            </div>
            <div class="h-[350px] relative">
                <canvas id="growthChart"></canvas>
            </div>
        </div>

        <!-- Top Células (Ecclesiastical) -->
        <div id="top-cells" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8">Top Células (Dízimos)</h3>
            <div class="space-y-4 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($topCells as $index => $cell)
                    <div
                        class="flex items-center p-4 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-lg transition-all duration-300 border border-transparent hover:border-gray-100 group">
                        <div
                            class="w-10 h-10 rounded-xl bg-white flex items-center justify-center font-black text-gray-400 mr-4 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-black text-gray-900 truncate">{{ $cell['name'] }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                {{ $cell['contributed'] }}/{{ $cell['members'] }} Membros
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-green-600 tracking-tight">{{ number_format($cell['total'], 0, ',', '.') }}
                            </p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">MT</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Próximos Eventos -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Próximos Eventos</h3>
                <a href="{{ route('events.index') }}"
                    class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Ver
                    Calendário</a>
            </div>
            <div class="space-y-6">
                @forelse($upcomingEvents as $event)
                    <div class="flex items-center space-x-6 group">
                        <div
                            class="bg-gray-50 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                            <span class="block text-xl font-black leading-none">{{ $event->date->format('d') }}</span>
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest">{{ $event->date->translatedFormat('M') }}</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-gray-900 group-hover:text-orange-600 transition-colors">
                                {{ $event->name }}</h4>
                            <p class="text-xs text-gray-500 flex items-center mt-1">
                                <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Life Church' }}
                                <span class="mx-2">•</span>
                                <i class="bi bi-clock mr-1"></i> {{ $event->date->format('H:i') }}h
                            </p>
                        </div>
                        <span
                            class="px-3 py-1 bg-gray-100 rounded-full text-[8px] font-black uppercase tracking-widest text-gray-500">
                            {{ $event->eventType->name ?? 'Evento' }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-10">Nenhum evento programado.</p>
                @endforelse
            </div>
        </div>

        <!-- Últimos Cultos -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Relatórios de Cultos</h3>
                <a href="{{ route('services.index') }}"
                    class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Ver Todos</a>
            </div>
            <div class="space-y-6">
                @forelse($recentServices as $service)
                    <div
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-lg transition-all duration-300 border border-transparent hover:border-gray-100">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-orange-600 shadow-sm">
                                <i class="bi bi-journal-text text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900">{{ $service->name }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                    {{ $service->date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-gray-900">{{ $service->total_participation ?? 0 }}</p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">Presentes</p>
                        </div>
                        <a href="{{ route('services.show', $service->id) }}"
                            class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">
                            <i class="bi bi-arrow-right ml-1"></i> Detalhes
                        </a>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-10">Nenhum relatório de culto registado.</p>
                @endforelse
            </div>
        </div>
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
                        backgroundColor: 'rgba(59, 130, 246, 0.8)', // Blue
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)' } },
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
                            backgroundColor: 'rgba(168, 85, 247, 0.8)', // Purple
                            borderRadius: 6,
                        },
                        {
                            label: 'Supervisões',
                            data: @json($zoneStructures->pluck('supervisions')),
                            backgroundColor: 'rgba(234, 179, 8, 0.8)', // Yellow
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Growth Chart
            const growthCtx = document.getElementById('growthChart').getContext('2d');
            const growthGradient = growthCtx.createLinearGradient(0, 0, 0, 400);
            growthGradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            growthGradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: @json($growthLabels),
                    datasets: [{
                        label: 'Total de Membros',
                        data: @json($growthData),
                        backgroundColor: growthGradient,
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: false, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
    <style>
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
@endsection