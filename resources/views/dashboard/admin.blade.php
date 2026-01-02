@extends('layouts.app')

@section('title', 'Dashboard Admin - Portal Life Church')
@section('page-title', 'Dashboard Administrativo')
@section('page-subtitle', 'Visão geral de todas as atividades e contribuições')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card: Total Arrecadado -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-50 p-4 rounded-2xl group-hover:bg-green-600 transition-colors duration-500">
                    <i
                        class="bi bi-cash-coin text-green-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Financeiro</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Total Arrecadado</p>
                <p class="text-3xl font-black text-gray-900 mt-2 tracking-tighter">
                    {{ number_format($totalContributed, 2, ',', '.') }} MT</p>
                <div class="flex items-center mt-4 text-xs">
                    <span class="text-green-500 font-bold flex items-center">
                        <i class="bi bi-arrow-up-short text-lg"></i> 12%
                    </span>
                    <span class="text-gray-400 ml-2">vs mês anterior</span>
                </div>
            </div>
        </div>

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
                        <i class="bi bi-plus text-lg"></i> 5 novos
                    </span>
                    <span class="text-gray-400 ml-2">esta semana</span>
                </div>
            </div>
        </div>

        <!-- Card: Taxa de Participação -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-50 p-4 rounded-2xl group-hover:bg-purple-600 transition-colors duration-500">
                    <i
                        class="bi bi-percent text-purple-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Desempenho</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Participação</p>
                <p class="text-3xl font-black text-gray-900 mt-2 tracking-tighter">{{ $percentageContributed }}%</p>
                <div class="w-full bg-gray-100 h-1.5 rounded-full mt-4 overflow-hidden">
                    <div class="bg-purple-600 h-full rounded-full" style="width: {{ $percentageContributed }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card: Pendentes Verificação -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-orange-50 p-4 rounded-2xl group-hover:bg-orange-600 transition-colors duration-500">
                    <i
                        class="bi bi-clock-history text-orange-600 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pendentes</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-wider">Aguardando</p>
                <p class="text-3xl font-black text-orange-600 mt-2 tracking-tighter">{{ $pendingContributions }}</p>
                <a href="{{ route('contributions.pending') }}"
                    class="inline-flex items-center mt-4 text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">
                    Verificar Agora <i class="bi bi-arrow-right ml-1"></i>
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
            <a href="{{ route('events.create') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 mx-auto mb-3 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <i class="bi bi-calendar-plus"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Novo Evento</span>
            </a>
            <a href="{{ route('services.create') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mx-auto mb-3 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="bi bi-journal-plus"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Novo Culto</span>
            </a>
            <a href="{{ route('users.create') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600 mx-auto mb-3 group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <i class="bi bi-person-plus"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Novo Membro</span>
            </a>
            <a href="{{ route('contributions.create') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 mx-auto mb-3 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Lançar Oferta</span>
            </a>
            <a href="{{ route('quarterly-reports.create') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-600 mx-auto mb-3 group-hover:bg-red-600 group-hover:text-white transition-colors">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Relat. Trimestral</span>
            </a>
            <a href="{{ route('quarterly-reports.export') }}" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 mx-auto mb-3 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Exportar Excel</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Contribuições por Zona (Chart) -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Contribuições por Zona</h3>
                <div class="flex space-x-2">
                    <button class="px-4 py-2 bg-orange-600 text-white rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-orange-600/20">Este Mês</button>
                </div>
            </div>
            <div class="h-[350px] relative">
                <canvas id="zoneChart"></canvas>
            </div>
        </div>

        <!-- Top Células -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8">Top 10 Células</h3>
            <div class="space-y-4">
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
                                {{ $cell['contributed'] }}/{{ $cell['members'] }} Membros</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Growth Chart -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Crescimento de Membros</h3>
                <span class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-xs font-bold uppercase tracking-widest">Últimos 6 Meses</span>
            </div>
            <div class="h-[350px] relative">
                <canvas id="growthChart"></canvas>
            </div>
        </div>

        <!-- Atividade Recente -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-black text-gray-900 tracking-tight mb-8">Atividade Recente</h3>
            <div class="space-y-6">
                @forelse($recentActivity as $activity)
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 {{ $activity['bg'] }} {{ $activity['color'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="bi {{ $activity['icon'] }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-black text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $activity['description'] }}</p>
                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                                {{ $activity['time']->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-10">Nenhuma atividade recente.</p>
                @endforelse
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
                                <i class="bi bi-journal-text text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900">{{ $service->name }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                    {{ $service->date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-gray-900">{{ $service->attendance_count ?? 0 }}</p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">Presentes</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-10">Nenhum relatório de culto registado.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Zone Chart
            const zoneCtx = document.getElementById('zoneChart').getContext('2d');
            const zoneGradient = zoneCtx.createLinearGradient(0, 0, 0, 400);
            zoneGradient.addColorStop(0, 'rgba(249, 115, 22, 0.8)');
            zoneGradient.addColorStop(1, 'rgba(249, 115, 22, 0.1)');

            new Chart(zoneCtx, {
                type: 'bar',
                data: {
                    labels: @json($zoneStats->pluck('name')),
                    datasets: [{
                        label: 'Total Arrecadado (MT)',
                        data: @json($zoneStats->pluck('total')),
                        backgroundColor: zoneGradient,
                        borderColor: 'rgba(249, 115, 22, 1)',
                        borderWidth: 2,
                        borderRadius: 12,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: { font: { size: 11, weight: 'bold' }, color: '#9ca3af' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: 'bold' }, color: '#9ca3af' }
                        }
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
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: { font: { size: 11, weight: 'bold' }, color: '#9ca3af' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: 'bold' }, color: '#9ca3af' }
                        }
                    }
                }
            });
        });
    </script>
@endsection