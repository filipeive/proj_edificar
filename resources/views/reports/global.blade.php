@extends('layouts.app')

@section('title', 'Relatório Global - Portal Life Church')
@section('page-title', 'Projecto Edificar')
@section('page-subtitle', 'Consolidação Geral de Métricas e Performance Financeira')

@section('content')
    <div class="min-h-screen bg-gray-100 dark:bg-[#05060f] p-4 md:p-8 -m-4 md:-m-8 transition-all duration-500">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;500;700;900&display=swap');

            .font-outfit {
                font-family: 'Outfit', sans-serif;
            }

            .glass-card {
                background: var(--bg-secondary);
                backdrop-filter: blur(20px);
                border: 1px solid var(--border-color);
                box-shadow: 0 10px 25px -5px var(--shadow);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            [data-theme="dark"] .glass-card {
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }

            .glass-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px -10px var(--shadow);
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
                height: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            [data-theme="dark"] .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.1);
            }
        </style>

        <div class="w-full mx-auto space-y-8 font-outfit transition-all duration-500">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h2 class="text-4xl font-black tracking-tighter text-gray-900 dark:text-white">Relatório <span
                            class="text-blue-500">Global</span></h2>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Análise detalhada de contribuições e zonas</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('reports.export.pdf', array_merge(['type' => 'global'], request()->query())) }}"
                        class="px-5 py-3 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-2xl border border-red-100 dark:border-red-500/20 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-sm">
                        <i class="bi bi-file-pdf mr-2 text-lg"></i> PDF
                    </a>
                    <a href="{{ route('reports.export.excel', array_merge(['type' => 'global'], request()->query())) }}"
                        class="px-5 py-3 bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 rounded-2xl border border-green-100 dark:border-green-500/20 hover:bg-green-600 hover:text-white dark:hover:bg-green-600 dark:hover:text-white transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-sm">
                        <i class="bi bi-file-excel mr-2 text-lg"></i> Excel
                    </a>
                    <a href="{{ route('reports.export.excel', ['type' => 'structure']) }}"
                        class="px-5 py-3 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-blue-500/30">
                        <i class="bi bi-diagram-3 mr-2 text-lg"></i> Estrutura
                    </a>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="glass-card p-6 md:p-8 rounded-[2rem]">
                <form action="{{ route('reports.global') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 items-end">
                    <div class="lg:col-span-3 space-y-2">
                        <label
                            class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Data
                            Início</label>
                        <div class="relative">
                            <i
                                class="bi bi-calendar-event absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                            <input type="date" name="start_date"
                                value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                                class="w-full pl-11 pr-5 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl text-gray-700 dark:text-gray-200 font-bold focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        </div>
                    </div>
                    <div class="lg:col-span-3 space-y-2">
                        <label
                            class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Data
                            Fim</label>
                        <div class="relative">
                            <i
                                class="bi bi-calendar-check absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                            <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                                class="w-full pl-11 pr-5 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl text-gray-700 dark:text-gray-200 font-bold focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        </div>
                    </div>
                    <div class="lg:col-span-2 space-y-2">
                        <label
                            class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Zona</label>
                        <div class="relative">
                            <select name="zone_id"
                                class="w-full px-5 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl text-gray-700 dark:text-gray-200 font-bold focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all appearance-none cursor-pointer">
                                <option value="">Todas</option>
                                @foreach($allZones as $zone)
                                    <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i
                                class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none text-xs"></i>
                        </div>
                    </div>
                    <div class="lg:col-span-2 space-y-2">
                        <label
                            class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Status</label>
                        <div class="relative">
                            <select name="status"
                                class="w-full px-5 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl text-gray-700 dark:text-gray-200 font-bold focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all appearance-none cursor-pointer">
                                <option value="">Todos</option>
                                <option value="verificada" {{ request('status') == 'verificada' ? 'selected' : '' }}>
                                    Verificada
                                </option>
                                <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente
                                </option>
                                <option value="rejeitada" {{ request('status') == 'rejeitada' ? 'selected' : '' }}>Rejeitada
                                </option>
                            </select>
                            <i
                                class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 pointer-events-none text-xs"></i>
                        </div>
                    </div>
                    <div class="lg:col-span-2 flex items-center gap-2">
                        <button type="submit"
                            class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 h-[50px]">
                            Filtrar
                        </button>
                        <a href="{{ route('reports.global') }}"
                            class="w-[50px] h-[50px] flex items-center justify-center bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-500 dark:text-gray-400 rounded-2xl transition-all border border-transparent dark:border-white/5"
                            title="Limpar Filtros">
                            <i class="bi bi-arrow-counterclockwise text-xl"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Stats & Chart Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Total Card -->
                <div class="glass-card p-8 rounded-[2.5rem] relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-700">
                        <i class="bi bi-cash-stack text-9xl text-blue-500"></i>
                    </div>
                    <div class="relative z-10 flex flex-col h-full justify-between gap-8">
                        <div>
                            <div
                                class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/30 mb-6">
                                <i class="bi bi-wallet2 text-3xl"></i>
                            </div>
                            <h3 class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">
                                Total Filtrado</h3>
                            <p class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter">
                                {{ number_format($total, 0, ',', '.') }}<span
                                    class="text-2xl ml-2 text-blue-500 font-bold">MT</span>
                            </p>
                        </div>
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-100 dark:border-white/5 w-fit">
                            <i class="bi bi-calendar-range text-gray-400"></i>
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400">
                                {{ $startDate->format('d/m/Y') }} — {{ $endDate->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Chart Card -->
                <div class="lg:col-span-2 glass-card p-8 rounded-[2.5rem] flex flex-col">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-3 mb-6">
                        <i class="bi bi-pie-chart-fill text-orange-500 text-2xl"></i> Distribuição por Zona
                    </h3>
                    <div class="flex-1 min-h-[300px] relative">
                        <canvas id="zoneChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="glass-card rounded-[2.5rem] overflow-hidden">
                <div
                    class="p-8 border-b border-gray-100 dark:border-white/5 flex flex-wrap items-center justify-between gap-4">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                        <i class="bi bi-table text-purple-500 text-2xl"></i> Detalhamento
                    </h3>
                    <span
                        class="px-4 py-1.5 bg-gray-100 dark:bg-white/5 rounded-full text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                        {{ $contributions->count() }} Registros
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-white/[0.02] border-b border-gray-100 dark:border-white/5">
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                    Doador</th>
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                    Origem</th>
                                <th
                                    class="px-8 py-5 text-center text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                    Status</th>
                                <th
                                    class="px-8 py-5 text-right text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                    Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @forelse($contributions as $contribution)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-300 flex items-center justify-center font-black text-sm group-hover:bg-blue-500 group-hover:text-white transition-all shadow-sm">
                                                {{ substr($contribution->user->name, 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-bold text-gray-900 dark:text-white">{{ $contribution->user->name }}</span>
                                                <span
                                                    class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $contribution->contribution_date->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                                {{ $contribution->zone->name ?? 'N/A' }}
                                            </span>
                                            <span class="text-[10px] text-gray-400 uppercase tracking-wider">
                                                {{ $contribution->user->cell->name ?? 'Sem Célula' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        @php
                                            $statusClasses = match ($contribution->status) {
                                                'verificada' => 'bg-green-50 text-green-600 border-green-100 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20',
                                                'pendente' => 'bg-orange-50 text-orange-600 border-orange-100 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20',
                                                'rejeitada' => 'bg-red-50 text-red-600 border-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
                                                default => 'bg-gray-50 text-gray-600 border-gray-100',
                                            };
                                        @endphp
                                        <span
                                            class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClasses }}">
                                            {{ $contribution->status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <span class="text-sm font-black text-gray-900 dark:text-white">
                                            {{ number_format($contribution->amount, 0, ',', '.') }} <span
                                                class="text-[10px] text-gray-400 dark:text-gray-500 font-bold ml-0.5">MT</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div
                                                class="w-16 h-16 rounded-full bg-gray-50 dark:bg-white/5 flex items-center justify-center text-gray-300 dark:text-gray-600">
                                                <i class="bi bi-search text-3xl"></i>
                                            </div>
                                            <p
                                                class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                                Nenhum registro encontrado</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($contributions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="p-6 border-t border-gray-100 dark:border-white/5">
                        {{ $contributions->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Chart Script -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                const textColor = isDark ? '#94a3b8' : '#6b7280';
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

                Chart.defaults.color = textColor;
                Chart.defaults.font.family = "'Outfit', sans-serif";
                Chart.defaults.font.weight = '600';

                const ctx = document.getElementById('zoneChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($zoneStats->pluck('name')),
                        datasets: [{
                            label: 'Total Arrecadado',
                            data: @json($zoneStats->pluck('total')),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'transparent',
                            borderRadius: 6,
                            barThickness: 30,
                            hoverBackgroundColor: '#2563eb'
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
                                backgroundColor: isDark ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                                titleColor: isDark ? '#fff' : '#111827',
                                bodyColor: isDark ? '#94a3b8' : '#4b5563',
                                padding: 12,
                                borderRadius: 12,
                                displayColors: false,
                                callbacks: {
                                    label: (context) => `Total: ${new Intl.NumberFormat('pt-MZ').format(context.raw)} MT`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: gridColor,
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: (value) => value >= 1000 ? `${value / 1000}k` : value,
                                    font: {
                                        size: 10
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </div>
@endsection