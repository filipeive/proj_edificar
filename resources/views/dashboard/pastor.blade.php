@extends('layouts.app')

@section('title', 'Dashboard Pastor - Portal Life Church')
@section('page-title', 'Dashboard do Pastor de Zona')
@section('page-subtitle', 'Visão geral da Zona ' . $zoneName)

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Pastorado de Zona</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Zona {{ $zoneName }}</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Acompanhamento de supervisões, células, reuniões e contribuições da zona.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('cells.index') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-people text-orange-700"></i> <span>Listar Células</span>
                </a>
                <a href="{{ route('reports.zone') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-file-earmark-bar-graph"></i> <span>Relatório da Zona</span>
                </a>
            </div>
        </header>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <!-- Zona Info -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Zona</p>
                    <i class="bi bi-geo-alt text-orange-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight truncate">{{ $zoneName }}</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">{{ $supervisions->count() }} Supervisões</p>
            </article>

            <!-- Total Membros -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Membros</p>
                    <i class="bi bi-people text-blue-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $totalMembers }}</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Membros ativos</p>
            </article>

            <!-- Total Arrecadado -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Total da Zona</p>
                    <i class="bi bi-cash-coin text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">
                    {{ number_format($total, 2, ',', '.') }} MT
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Este mês</p>
            </article>

            <!-- Desempenho -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Células</p>
                    <i class="bi bi-grid-3x3-gap text-indigo-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $zone->cells()->count() }}</p>
                <p class="text-[10px] sm:text-[11px] text-orange-700 font-semibold mt-0.5 truncate">
                    <i class="bi bi-activity mr-1"></i> Ativas
                </p>
            </article>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Gráfico de Arrecadação -->
            <section class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Histórico Financeiro</h3>
                        <p class="text-xs text-gray-500">Últimos 6 Meses (MT)</p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 bg-orange-600 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Contribuições</span>
                    </div>
                </div>
                <div class="w-full h-64 relative">
                    <canvas id="financialChart"></canvas>
                </div>
            </section>

            <!-- Ações Rápidas & Status -->
            <div class="space-y-4">
                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <h3 class="text-sm font-black text-gray-900 mb-3">Ações Rápidas</h3>
                    <div class="grid grid-cols-1 gap-2.5">
                        <a href="{{ route('reports.zone') }}"
                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80 hover:bg-orange-50 hover:border-orange-200 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-orange-700 shrink-0">
                                <i class="bi bi-file-earmark-pdf text-sm"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Relatório da Zona</span>
                        </a>
                        <a href="{{ route('cells.index') }}"
                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80 hover:bg-orange-50 hover:border-orange-200 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-orange-700 shrink-0">
                                <i class="bi bi-people text-sm"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Listar Células</span>
                        </a>
                        <a href="{{ route('contributions.index') }}"
                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80 hover:bg-orange-50 hover:border-orange-200 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-orange-700 shrink-0">
                                <i class="bi bi-cash-coin text-sm"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Contribuições</span>
                        </a>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <h3 class="text-sm font-black text-gray-900 mb-1.5">Status da Zona</h3>
                    <p class="text-xs text-gray-600 leading-relaxed mb-3">
                        A zona <span class="text-orange-700 font-bold">{{ $zoneName }}</span> está a operar com <span class="text-gray-950 font-bold">{{ $supervisions->count() }}</span> supervisões.
                    </p>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-green-50 border border-green-200 px-2.5 py-0.5">
                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>
                        <span class="text-[10px] font-black uppercase tracking-wider text-green-800">Sistema Online</span>
                    </div>
                </section>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Supervisões da Zona -->
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Supervisões</h3>
                        <p class="text-xs text-gray-500">Desempenho por supervisão</p>
                    </div>
                    <a href="{{ route('supervisions.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todas</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Supervisão</th>
                                <th class="px-4 py-3 text-center">Células</th>
                                <th class="px-4 py-3 text-right">Total Arrecadado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($supervisions as $supervision)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center font-black text-xs shrink-0">
                                                {{ strtoupper(substr($supervision['name'], 0, 1)) }}
                                            </div>
                                            <span class="font-bold text-gray-900 text-xs sm:text-sm">{{ $supervision['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 bg-gray-100 rounded-full text-[10px] font-black text-gray-700 uppercase">
                                            {{ $supervision['cells'] }} Células
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-emerald-700 text-xs sm:text-sm tabular-nums">
                                        {{ number_format($supervision['total'], 2, ',', '.') }} MT
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Reuniões de Célula Recentes -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Células: Reuniões Recentes</h3>
                        <p class="text-xs text-gray-500">Encontros registados na zona</p>
                    </div>
                    <a href="{{ route('cell-meetings.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todas</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentCellMeetings as $meeting)
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                            <div class="bg-orange-50 border border-orange-200/60 px-3 py-1.5 rounded-lg text-center min-w-[54px]">
                                <span class="block text-base font-black leading-none text-orange-900 tabular-nums">{{ $meeting->meeting_date->format('d') }}</span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-orange-700">{{ $meeting->meeting_date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 text-xs sm:text-sm truncate">
                                    {{ $meeting->cell->name }}
                                </h4>
                                <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                    Líder: {{ $meeting->leader->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="block font-black text-gray-950 text-xs sm:text-sm tabular-nums">{{ ($meeting->adults_count ?? 0) + ($meeting->children_count ?? 0) + ($meeting->visitors_count ?? 0) }}</span>
                                <span class="text-[9px] font-bold uppercase text-gray-400">Total</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6 text-xs">
                            Nenhuma reunião registada recentemente.
                        </p>
                    @endforelse
                </div>
            </section>
        </div>

        <!-- Próximos Eventos da Zona -->
        <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Próximos Eventos</h3>
                    <p class="text-xs text-gray-500">Programação para a zona</p>
                </div>
                <a href="{{ route('events.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todos</a>
            </div>
            <div class="space-y-3">
                @forelse($upcomingEvents as $event)
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                        <div class="bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-center min-w-[54px]">
                            <span class="block text-base font-black leading-none text-gray-900 tabular-nums">{{ $event->date->format('d') }}</span>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500">{{ $event->date->translatedFormat('M') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-gray-900 text-xs sm:text-sm truncate">
                                {{ $event->name }}
                            </h4>
                            <p class="text-[11px] text-gray-500 flex items-center mt-0.5 truncate">
                                <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Local a definir' }}
                                @if($event->end_date)
                                    <span class="ml-2 px-1.5 py-0.5 bg-orange-50 text-orange-800 rounded text-[9px] font-bold uppercase">
                                        Até {{ $event->end_date->format('d/m/Y') }}
                                    </span>
                                @endif
                            </p>
                        </div>
                        <span class="px-2.5 py-0.5 bg-gray-100 rounded-full text-[9px] font-black uppercase text-gray-600 shrink-0">
                            {{ $event->eventType->name ?? 'Evento' }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-6 text-xs">
                        Nenhum evento programado para esta zona.
                    </p>
                @endforelse
            </div>
        </section>
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
                        borderWidth: 3,
                        fill: true,
                        backgroundColor: gradient,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(249, 115, 22, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
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
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
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
        });
    </script>
@endpush
