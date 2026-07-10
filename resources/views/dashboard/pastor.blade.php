@extends('layouts.app')

@section('title', 'Dashboard Pastor - Portal Life Church')
@section('page-title', 'Dashboard do Pastor de Zona')
@section('page-subtitle', 'Visão geral da Zona ' . $zoneName)

@section('content')
    <div class="space-y-6 md:space-y-8">        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Zona Info -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-geo-alt-fill text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Localização</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Zona</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">{{ $zoneName }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2 font-bold uppercase tracking-widest">
                        {{ $supervisions->count() }} Supervisões
                    </p>
                </div>
            </div>

            <!-- Total Membros -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-people-fill text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Dimensão</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Membros</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">{{ $totalMembers }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2 font-bold uppercase tracking-widest">Membros Ativos</p>
                </div>
            </div>

            <!-- Total Arrecadado -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-cash-coin text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Financeiro</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Total da Zona</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">
                        {{ number_format($total, 2, ',', '.') }} MT
                    </p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2 font-bold uppercase tracking-widest">Este Mês</p>
                </div>
            </div>

            <!-- Desempenho -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-grid-3x3-gap-fill text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Estrutura</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Células</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">
                        {{ $zone->cells()->count() }}</p>
                    <div class="flex items-center mt-3 text-[10px]">
                        <span class="text-orange-500 dark:text-orange-400 font-bold flex items-center">
                            <i class="bi bi-activity mr-1"></i> Ativas
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Gráfico de Arrecadação -->
            <div
                class="lg:col-span-2 bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Histórico Financeiro</h3>
                        <p class="text-[10px] text-gray-405 dark:text-zinc-500 font-bold uppercase tracking-widest mt-1">
                            Últimos 6 Meses (MT)
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-orange-600 rounded-full"></span>
                        <span
                            class="text-[9px] font-black text-gray-450 dark:text-zinc-400 uppercase tracking-widest">Contribuições</span>
                    </div>
                </div>
                <div class="w-full h-[300px]">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="space-y-6">
                <div
                    class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight mb-6">
                        Ações Rápidas</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <a href="{{ route('reports.zone') }}"
                            class="flex items-center p-4 bg-gray-50 dark:bg-zinc-850 rounded-2xl hover:bg-orange-600 group transition-all duration-300 border border-transparent hover:border-orange-500/20">
                            <div
                                class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-4 shadow-sm group-hover:bg-orange-700 group-hover:text-white transition-colors">
                                <i class="bi bi-file-earmark-pdf text-lg"></i>
                            </div>
                            <span
                                class="text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white transition-colors">Relatório da Zona</span>
                        </a>
                        <a href="{{ route('cells.index') }}"
                            class="flex items-center p-4 bg-gray-50 dark:bg-zinc-850 rounded-2xl hover:bg-orange-600 group transition-all duration-300 border border-transparent hover:border-orange-500/20">
                            <div
                                class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-4 shadow-sm group-hover:bg-orange-700 group-hover:text-white transition-colors">
                                <i class="bi bi-people text-lg"></i>
                            </div>
                            <span
                                class="text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white transition-colors">Listar Células</span>
                        </a>
                        <a href="{{ route('contributions.index') }}"
                            class="flex items-center p-4 bg-gray-50 dark:bg-zinc-850 rounded-2xl hover:bg-orange-600 group transition-all duration-300 border border-transparent hover:border-orange-500/20">
                            <div
                                class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-4 shadow-sm group-hover:bg-orange-700 group-hover:text-white transition-colors">
                                <i class="bi bi-cash-coin text-lg"></i>
                            </div>
                            <span
                                class="text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white transition-colors">Contribuições</span>
                        </a>
                    </div>
                </div>

                <div class="bg-zinc-950 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden border border-zinc-900">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-600/10 rounded-full -mr-16 -mt-16 blur-3xl font-black">
                    </div>
                    <h3 class="text-base font-black tracking-tight mb-3 relative z-10">Status da Zona</h3>
                    <p class="text-xs text-gray-400 leading-relaxed mb-5 relative z-10">
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Supervisões da Zona -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 overflow-hidden">
                <div
                    class="px-6 py-5 border-b border-gray-50 dark:border-zinc-850 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Supervisões</h3>
                    <a href="{{ route('supervisions.index') }}"
                        class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todas</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-50 dark:border-zinc-850">
                                <th class="px-6 py-4 text-left">Supervisão</th>
                                <th class="px-6 py-4 text-center">Células</th>
                                <th class="px-6 py-4 text-right">Total Arrecadado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-zinc-850">
                            @foreach($supervisions as $supervision)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-900/40 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/20 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black mr-4 group-hover:bg-orange-600 group-hover:text-white transition-all">
                                                {{ strtoupper(substr($supervision['name'], 0, 1)) }}
                                            </div>
                                            <span
                                                class="font-black text-gray-900 dark:text-white text-sm">{{ $supervision['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-2.5 py-0.5 bg-gray-100 dark:bg-zinc-850 rounded-full text-[9px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">
                                            {{ $supervision['cells'] }} Células
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right font-black text-green-600 dark:text-green-400 tracking-tight text-sm">
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
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Células: Reuniões Recentes</h3>
                    <a href="{{ route('cell-meetings.index') }}"
                        class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todas</a>
                </div>
                <div class="space-y-6">
                    @forelse($recentCellMeetings as $meeting)
                        <div class="flex items-center space-x-6 group">
                            <div
                                class="bg-orange-50 dark:bg-orange-950/20 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                <span
                                    class="block text-xl font-black leading-none text-gray-900 dark:text-white group-hover:text-white">{{ $meeting->meeting_date->format('d') }}</span>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 group-hover:text-white">{{ $meeting->meeting_date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1">
                                <h4
                                    class="font-black text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors leading-tight">
                                    {{ $meeting->cell->name }}
                                </h4>
                                <p
                                    class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest mt-1">
                                    Líder: {{ $meeting->leader->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="block font-black text-gray-900 dark:text-white">{{ ($meeting->adults_count ?? 0) + ($meeting->children_count ?? 0) + ($meeting->visitors_count ?? 0) }}</span>
                                <span
                                    class="text-[8px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Total</span>
                            </div>
                        </div>
                    @empty
                        <p
                            class="text-center text-gray-400 dark:text-gray-550 py-10 font-bold uppercase tracking-widest text-[10px]">
                            Nenhuma reunião registada recentemente.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 mb-8">
            <!-- Próximos Eventos da Zona -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Próximos Eventos</h3>
                    <a href="{{ route('events.index') }}"
                        class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todos</a>
                </div>
                <div class="space-y-6">
                    @forelse($upcomingEvents as $event)
                        <div class="flex items-center space-x-6 group">
                            <div
                                class="bg-gray-50 dark:bg-zinc-850 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                <span
                                    class="block text-xl font-black leading-none text-gray-900 dark:text-gray-100 group-hover:text-white">{{ $event->date->format('d') }}</span>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-zinc-400 group-hover:text-white">{{ $event->date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1">
                                <h4
                                    class="font-black text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                    {{ $event->name }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-zinc-400 flex items-center mt-1">
                                    <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Local a definir' }}
                                    @if($event->end_date)
                                        <span
                                            class="ml-2 px-2 py-0.5 bg-orange-50 dark:bg-orange-950/20 text-orange-650 dark:text-orange-400 rounded text-[9px] font-bold uppercase tracking-widest">Até
                                            {{ $event->end_date->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            </div>
                            <span
                                class="px-3 py-1 bg-gray-100 dark:bg-zinc-850 rounded-full text-[8px] font-black uppercase tracking-widest text-gray-500 dark:text-zinc-300">
                                {{ $event->eventType->name ?? 'Evento' }}
                            </span>
                        </div>
                    @empty
                        <p
                            class="text-center text-gray-400 dark:text-zinc-500 py-10 font-bold uppercase tracking-widest text-[10px]">
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
