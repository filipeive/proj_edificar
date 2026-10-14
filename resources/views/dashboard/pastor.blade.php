@extends('layouts.app')

@section('title', 'Dashboard Pastor - Portal Life Church')
@section('page-title', 'Dashboard do Pastor de Zona')
@section('page-subtitle', 'Visão geral da Zona ' . $zoneName)

@section('content')
    <div class="space-y-6 md:space-y-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
            <!-- Zona Info -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-3 sm:p-4 md:p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-2.5 sm:mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-2 sm:p-2.5 md:p-3.5 rounded-lg sm:rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-geo-alt-fill text-orange-600 dark:text-orange-400 text-base sm:text-lg md:text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Localização</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] sm:text-xs font-bold uppercase tracking-wider">Zona</p>
                    <p class="text-lg sm:text-xl md:text-2xl font-black text-gray-900 dark:text-white mt-0.5 sm:mt-1 tracking-tight truncate">{{ $zoneName }}</p>
                    <p class="text-[9px] sm:text-[10px] text-gray-400 dark:text-gray-500 mt-1 sm:mt-2 font-bold uppercase tracking-widest">
                        {{ $supervisions->count() }} Supervisões
                    </p>
                </div>
            </div>

            <!-- Total Membros -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-3 sm:p-4 md:p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-2.5 sm:mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-2 sm:p-2.5 md:p-3.5 rounded-lg sm:rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-people-fill text-orange-600 dark:text-orange-400 text-base sm:text-lg md:text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Dimensão</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] sm:text-xs font-bold uppercase tracking-wider">Membros</p>
                    <p class="text-lg sm:text-xl md:text-2xl font-black text-gray-900 dark:text-white mt-0.5 sm:mt-1 tracking-tight">{{ $totalMembers }}</p>
                    <p class="text-[9px] sm:text-[10px] text-gray-400 dark:text-gray-500 mt-1 sm:mt-2 font-bold uppercase tracking-widest">Membros Ativos</p>
                </div>
            </div>

            <!-- Total Arrecadado -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-3 sm:p-4 md:p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-2.5 sm:mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-2 sm:p-2.5 md:p-3.5 rounded-lg sm:rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-cash-coin text-orange-600 dark:text-orange-400 text-base sm:text-lg md:text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Financeiro</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] sm:text-xs font-bold uppercase tracking-wider">Total da Zona</p>
                    <p class="text-base sm:text-xl md:text-2xl font-black text-gray-900 dark:text-white mt-0.5 sm:mt-1 tracking-tight">
                        {{ number_format($total, 2, ',', '.') }} MT
                    </p>
                    <p class="text-[9px] sm:text-[10px] text-gray-400 dark:text-gray-500 mt-1 sm:mt-2 font-bold uppercase tracking-widest">Este Mês</p>
                </div>
            </div>

            <!-- Desempenho -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-3 sm:p-4 md:p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-2.5 sm:mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-2 sm:p-2.5 md:p-3.5 rounded-lg sm:rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-grid-3x3-gap-fill text-orange-600 dark:text-orange-400 text-base sm:text-lg md:text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Estrutura</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] sm:text-xs font-bold uppercase tracking-wider">Células</p>
                    <p class="text-lg sm:text-xl md:text-2xl font-black text-gray-900 dark:text-white mt-0.5 sm:mt-1 tracking-tight">
                        {{ $zone->cells()->count() }}</p>
                    <div class="flex items-center mt-1 sm:mt-2 text-[9px] sm:text-[10px]">
                        <span class="text-orange-500 dark:text-orange-400 font-bold flex items-center">
                            <i class="bi bi-activity mr-1"></i> Ativas
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
            <!-- Gráfico de Arrecadação -->
            <div
                class="lg:col-span-2 bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-4 sm:p-6 md:p-8">
                <div class="flex items-center justify-between mb-4 sm:mb-8">
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white tracking-tight">Histórico Financeiro</h3>
                        <p class="text-[9px] sm:text-[10px] text-gray-405 dark:text-zinc-500 font-bold uppercase tracking-widest mt-0.5 sm:mt-1">
                            Últimos 6 Meses (MT)
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="w-2 sm:w-2.5 h-2 sm:h-2.5 bg-orange-600 rounded-full"></span>
                        <span
                            class="text-[8px] sm:text-[9px] font-black text-gray-450 dark:text-zinc-400 uppercase tracking-widest">Contribuições</span>
                    </div>
                </div>
                <div class="w-full h-[220px] sm:h-[300px]">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="space-y-4 sm:space-y-6">
                <div
                    class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-4 sm:p-6 md:p-8">
                    <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight mb-4 sm:mb-6">
                        Ações Rápidas</h3>
                    <div class="grid grid-cols-1 gap-2.5 sm:gap-4">
                        <a href="{{ route('reports.zone') }}"
                            class="flex items-center p-3 sm:p-4 bg-gray-50 dark:bg-zinc-850 rounded-xl sm:rounded-2xl hover:bg-orange-600 group transition-all duration-300 border border-transparent hover:border-orange-500/20">
                            <div
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-3 sm:mr-4 shadow-sm group-hover:bg-orange-700 group-hover:text-white transition-colors">
                                <i class="bi bi-file-earmark-pdf text-base sm:text-lg"></i>
                            </div>
                            <span
                                class="text-xs sm:text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white transition-colors">Relatório da Zona</span>
                        </a>
                        <a href="{{ route('cells.index') }}"
                            class="flex items-center p-3 sm:p-4 bg-gray-50 dark:bg-zinc-850 rounded-xl sm:rounded-2xl hover:bg-orange-600 group transition-all duration-300 border border-transparent hover:border-orange-500/20">
                            <div
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-3 sm:mr-4 shadow-sm group-hover:bg-orange-700 group-hover:text-white transition-colors">
                                <i class="bi bi-people text-base sm:text-lg"></i>
                            </div>
                            <span
                                class="text-xs sm:text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white transition-colors">Listar Células</span>
                        </a>
                        <a href="{{ route('contributions.index') }}"
                            class="flex items-center p-3 sm:p-4 bg-gray-50 dark:bg-zinc-850 rounded-xl sm:rounded-2xl hover:bg-orange-600 group transition-all duration-300 border border-transparent hover:border-orange-500/20">
                            <div
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-3 sm:mr-4 shadow-sm group-hover:bg-orange-700 group-hover:text-white transition-colors">
                                <i class="bi bi-cash-coin text-base sm:text-lg"></i>
                            </div>
                            <span
                                class="text-xs sm:text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white transition-colors">Contribuições</span>
                        </a>
                    </div>
                </div>

                <div class="bg-zinc-950 rounded-xl sm:rounded-2xl shadow-xl p-5 sm:p-8 text-white relative overflow-hidden border border-zinc-900">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-600/10 rounded-full -mr-16 -mt-16 blur-3xl font-black">
                    </div>
                    <h3 class="text-sm sm:text-base font-black tracking-tight mb-2 sm:mb-3 relative z-10">Status da Zona</h3>
                    <p class="text-xs text-gray-400 leading-relaxed mb-4 sm:mb-5 relative z-10">
                        A zona <span class="text-orange-500 font-bold">{{ $zoneName }}</span> está a operar com <span
                            class="text-white font-bold">{{ $supervisions->count() }}</span> supervisões.
                    </p>
                    <div class="flex items-center space-x-2 relative z-10">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-[9px] font-black uppercase tracking-widest text-green-500">Sistema Online</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
            <!-- Supervisões da Zona -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 overflow-hidden">
                <div
                    class="px-4 sm:px-6 py-3.5 sm:py-5 border-b border-gray-50 dark:border-zinc-850 flex items-center justify-between">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white tracking-tight">Supervisões</h3>
                    <a href="{{ route('supervisions.index') }}"
                        class="text-[10px] sm:text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todas</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr
                                class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-50 dark:border-zinc-850">
                                <th class="px-3 sm:px-6 py-2.5 sm:py-4 text-left">Supervisão</th>
                                <th class="px-3 sm:px-6 py-2.5 sm:py-4 text-center">Células</th>
                                <th class="px-3 sm:px-6 py-2.5 sm:py-4 text-right">Total Arrecadado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-zinc-850">
                            @foreach($supervisions as $supervision)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-900/40 transition-colors group">
                                    <td class="px-3 sm:px-6 py-2.5 sm:py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-orange-50 dark:bg-orange-950/20 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black mr-2.5 sm:mr-4 group-hover:bg-orange-600 group-hover:text-white transition-all text-xs sm:text-sm">
                                                {{ strtoupper(substr($supervision['name'], 0, 1)) }}
                                            </div>
                                            <span
                                                class="font-black text-gray-900 dark:text-white text-xs sm:text-sm">{{ $supervision['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-2.5 sm:py-4 text-center">
                                        <span
                                            class="px-2 sm:px-2.5 py-0.5 bg-gray-100 dark:bg-zinc-850 rounded-full text-[8px] sm:text-[9px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">
                                            {{ $supervision['cells'] }} Células
                                        </span>
                                    </td>
                                    <td
                                        class="px-3 sm:px-6 py-2.5 sm:py-4 text-right font-black text-green-600 dark:text-green-400 tracking-tight text-xs sm:text-sm">
                                        {{ number_format($supervision['total'], 2, ',', '.') }} MT
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reuniões de Célula Recentes -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-4 sm:p-6 md:p-8">
                <div class="flex items-center justify-between mb-4 sm:mb-8">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white tracking-tight">Células: Reuniões Recentes</h3>
                    <a href="{{ route('cell-meetings.index') }}"
                        class="text-[10px] sm:text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todas</a>
                </div>
                <div class="space-y-4 sm:space-y-6">
                    @forelse($recentCellMeetings as $meeting)
                        <div class="flex items-center space-x-3 sm:space-x-6 group">
                            <div
                                class="bg-orange-50 dark:bg-orange-950/20 px-3 py-2 sm:px-4 sm:py-3 rounded-xl sm:rounded-2xl text-center min-w-[55px] sm:min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                <span
                                    class="block text-base sm:text-xl font-black leading-none text-gray-900 dark:text-white group-hover:text-white">{{ $meeting->meeting_date->format('d') }}</span>
                                <span
                                    class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 group-hover:text-white">{{ $meeting->meeting_date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4
                                    class="font-black text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors leading-tight text-xs sm:text-sm truncate">
                                    {{ $meeting->cell->name }}
                                </h4>
                                <p
                                    class="text-[9px] sm:text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest mt-0.5 sm:mt-1 truncate">
                                    Líder: {{ $meeting->leader->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span
                                    class="block font-black text-gray-900 dark:text-white text-xs sm:text-sm">{{ ($meeting->adults_count ?? 0) + ($meeting->children_count ?? 0) + ($meeting->visitors_count ?? 0) }}</span>
                                <span
                                    class="text-[8px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Total</span>
                            </div>
                        </div>
                    @empty
                        <p
                            class="text-center text-gray-400 dark:text-gray-550 py-8 sm:py-10 font-bold uppercase tracking-widest text-[9px] sm:text-[10px]">
                            Nenhuma reunião registada recentemente.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
            <!-- Próximos Eventos da Zona -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-4 sm:p-6 md:p-8">
                <div class="flex items-center justify-between mb-4 sm:mb-8">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white tracking-tight">Próximos Eventos</h3>
                    <a href="{{ route('events.index') }}"
                        class="text-[10px] sm:text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todos</a>
                </div>
                <div class="space-y-4 sm:space-y-6">
                    @forelse($upcomingEvents as $event)
                        <div class="flex items-center space-x-3 sm:space-x-6 group">
                            <div
                                class="bg-gray-50 dark:bg-zinc-850 px-3 py-2 sm:px-4 sm:py-3 rounded-xl sm:rounded-2xl text-center min-w-[55px] sm:min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                <span
                                    class="block text-base sm:text-xl font-black leading-none text-gray-900 dark:text-gray-100 group-hover:text-white">{{ $event->date->format('d') }}</span>
                                <span
                                    class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-zinc-400 group-hover:text-white">{{ $event->date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4
                                    class="font-black text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors text-xs sm:text-sm truncate">
                                    {{ $event->name }}
                                </h4>
                                <p class="text-[11px] sm:text-xs text-gray-500 dark:text-zinc-400 flex items-center mt-0.5 sm:mt-1 truncate">
                                    <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Local a definir' }}
                                    @if($event->end_date)
                                        <span
                                            class="ml-1.5 sm:ml-2 px-1.5 sm:px-2 py-0.5 bg-orange-50 dark:bg-orange-950/20 text-orange-650 dark:text-orange-400 rounded text-[8px] sm:text-[9px] font-bold uppercase tracking-widest">Até
                                            {{ $event->end_date->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            </div>
                            <span
                                class="px-2 sm:px-3 py-0.5 sm:py-1 bg-gray-100 dark:bg-zinc-850 rounded-full text-[7px] sm:text-[8px] font-black uppercase tracking-widest text-gray-500 dark:text-zinc-300 flex-shrink-0">
                                {{ $event->eventType->name ?? 'Evento' }}
                            </span>
                        </div>
                    @empty
                        <p
                            class="text-center text-gray-400 dark:text-zinc-500 py-8 sm:py-10 font-bold uppercase tracking-widest text-[9px] sm:text-[10px]">
                            Nenhum evento programado para esta zona.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('financialChart').getContext('2d');

            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(249, 115, 22, 0.25)');
            gradient.addColorStop(1, 'rgba(249, 115, 22, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Contribuições (MT)',
                        data: @json($chartData['data']),
                        borderColor: 'rgba(249, 115, 22, 1)',
                        borderWidth: 4,
                        fill: true,
                        backgroundColor: gradient,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(249, 115, 22, 1)',
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
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return context.parsed.y.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }) + ' MT';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: document.documentElement.getAttribute('data-theme') === 'dark' ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                font: { weight: 'bold', size: 10 },
                                color: '#9ca3af',
                                callback: function (value) {
                                    return value.toLocaleString('pt-MZ') + ' MT';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: { weight: 'bold', size: 10 },
                                color: '#9ca3af'
                            }
                        }
                    }
                }
        });
    </script>
@endpush
