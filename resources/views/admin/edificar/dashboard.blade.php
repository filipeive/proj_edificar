@extends('layouts.app')

@section('title', 'Painel Edificar - Evolução da Obra')
@section('page-title', 'Projecto Edificar')
@section('page-subtitle', 'Acompanhamento financeiro e evolução da construção')

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

            .text-glow-blue {
                text-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
            }

            .text-glow-green {
                text-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
            }

            .text-glow-orange {
                text-shadow: 0 0 20px rgba(249, 115, 22, 0.3);
            }

            [data-theme="dark"] .text-white-target {
                color: white;
            }

            [data-theme="light"] .text-white-target {
                color: var(--text-primary);
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
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
            <!-- Header section with Title and Period -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h2 class="text-4xl font-black tracking-tighter text-gray-900 dark:text-white">Evolução <span
                            class="text-blue-500">Financeira</span></h2>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Dados atualizados em tempo real para o Projecto
                        Edificar</p>
                </div>
                <div
                    class="flex items-center gap-3 px-4 py-2 bg-white dark:bg-white/5 rounded-2xl border border-gray-200 dark:border-white/10 backdrop-blur-sm shadow-sm">
                    <i class="bi bi-calendar3 text-blue-500"></i>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ now()->translatedFormat('F Y') }}</span>
                </div>
            </div>

            <!-- Dashboard Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Arrecadado -->
                <div class="glass-card p-8 rounded-[2rem] relative overflow-hidden group">
                    <div class="flex flex-col h-full justify-between gap-6 relative z-10">
                        <div
                            class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/30">
                            <i class="bi bi-bank2 text-2xl"></i>
                        </div>
                        <div>
                            <p
                                class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-1">
                                Total Arrecadado</p>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white">
                                {{ number_format($totalArrecadado, 2, ',', '.') }} <span
                                    class="text-sm font-bold text-blue-500">MT</span></h3>
                        </div>
                    </div>
                </div>

                <!-- Este Mês -->
                <div class="glass-card p-8 rounded-[2rem] relative overflow-hidden group">
                    <div class="flex flex-col h-full justify-between gap-6 relative z-10">
                        <div
                            class="w-14 h-14 rounded-2xl bg-green-50 dark:bg-green-500/20 flex items-center justify-center text-green-600 dark:text-green-400 border border-green-100 dark:border-green-500/30">
                            <i class="bi bi-stars text-2xl"></i>
                        </div>
                        <div>
                            <p
                                class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-1">
                                Este Mês</p>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white">
                                {{ number_format($arrecadadoMes, 2, ',', '.') }} <span
                                    class="text-sm font-bold text-green-500">MT</span></h3>
                        </div>
                    </div>
                </div>

                <!-- Membros Ativos -->
                <div class="glass-card p-8 rounded-[2rem] relative overflow-hidden group">
                    <div class="flex flex-col h-full justify-between gap-6 relative z-10">
                        <div
                            class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-500/20 flex items-center justify-center text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-500/30">
                            <i class="bi bi-people-fill text-2xl"></i>
                        </div>
                        <div>
                            <p
                                class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-1">
                                Compromissos</p>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white">{{ $pacotes->sum('membros') }}
                                <span class="text-sm font-bold text-purple-500">Membros Ativos</span></h3>
                        </div>
                    </div>
                </div>

                <!-- Pendentes -->
                <div class="glass-card p-8 rounded-[2rem] relative overflow-hidden group">
                    <div class="flex flex-col h-full justify-between gap-6 relative z-10">
                        <div
                            class="w-14 h-14 rounded-2xl bg-orange-50 dark:bg-orange-500/20 flex items-center justify-center text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-500/30">
                            <i class="bi bi-clock-history text-2xl"></i>
                        </div>
                        <div>
                            <p
                                class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-1">
                                Validação Pendente</p>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white">{{ $pendingContributions }} <span
                                    class="text-sm font-bold text-orange-500">Registos</span></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($authUser->isComissaoObra() || $authUser->isAdmin())
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('contributions.create') }}"
                        class="glass-card p-6 rounded-3xl flex items-center gap-4 hover:border-blue-500/50 group transition-all">
                        <div
                            class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-500 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                            <i class="bi bi-plus-lg text-xl"></i>
                        </div>
                        <span
                            class="text-sm font-black text-gray-700 dark:text-gray-200 uppercase tracking-widest">Registrar</span>
                    </a>

                    <a href="{{ route('contributions.index', ['status' => 'pendente']) }}"
                        class="glass-card p-6 rounded-3xl flex items-center gap-4 hover:border-orange-500/50 group transition-all">
                        <div
                            class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-500 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all shadow-sm">
                            <i class="bi bi-shield-check text-xl"></i>
                        </div>
                        <span
                            class="text-sm font-black text-gray-700 dark:text-gray-200 uppercase tracking-widest leading-tight">Validar</span>
                    </a>

                    <a href="{{ route('reports.global') }}"
                        class="glass-card p-6 rounded-3xl flex items-center gap-4 hover:border-green-500/50 group transition-all">
                        <div
                            class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-500/10 text-green-500 flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-all shadow-sm">
                            <i class="bi bi-file-earmark-bar-graph text-xl"></i>
                        </div>
                        <span
                            class="text-sm font-black text-gray-700 dark:text-gray-200 uppercase tracking-widest">Relatórios</span>
                    </a>

                    <a href="{{ route('dashboard.admin') }}"
                        class="glass-card p-6 rounded-3xl flex items-center gap-4 hover:border-gray-500/50 group transition-all">
                        <div
                            class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-500/10 text-gray-400 flex items-center justify-center group-hover:bg-gray-600 group-hover:text-white transition-all shadow-sm">
                            <i class="bi bi-arrow-left text-xl"></i>
                        </div>
                        <span
                            class="text-sm font-black text-gray-700 dark:text-gray-200 uppercase tracking-widest">Painel Geral</span>
                    </a>
                </div>
            @endif

            <!-- Main Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Evolution Chart -->
                <div class="lg:col-span-2 glass-card p-8 rounded-[2.5rem] relative overflow-hidden">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                            <i class="bi bi-soundwave text-blue-500 text-2xl"></i> Fluxo Mensal
                        </h3>
                        <div
                            class="px-4 py-1.5 bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-full text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest">
                            EDIFICAR v1.1</div>
                    </div>
                    <div class="h-[350px]">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>

                <!-- Package Performance -->
                <div class="glass-card p-8 rounded-[2.5rem]">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-3 mb-8">
                        <i class="bi bi-layers-half text-blue-500 text-2xl"></i> Desempenho / Pacote
                    </h3>

                    <div class="space-y-8 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($pacotes as $pacote)
                            <div class="space-y-3 group">
                                <div class="flex justify-between items-end">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-wider">{{ $pacote['name'] }}</span>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $pacote['membros'] }}
                                            Membros</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-black text-blue-600 dark:text-blue-400">
                                            {{ number_format($pacote['arrecadado'], 0, ',', '.') }} MT</div>
                                        <div class="text-[10px] font-bold text-gray-500 dark:text-gray-600 uppercase">Meta:
                                            {{ number_format($pacote['esperado'], 0, ',', '.') }} MT</div>
                                    </div>
                                </div>
                                <div
                                    class="relative w-full bg-gray-100 dark:bg-white/5 rounded-full h-3 p-[2px] overflow-hidden border border-gray-200 dark:border-white/5">
                                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-full transition-all duration-1000 group-hover:brightness-110"
                                        style="width: {{ min($pacote['percentual'], 100) }}%">
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <span
                                        class="text-[11px] font-black @if($pacote['percentual'] >= 80) text-green-500 @elseif($pacote['percentual'] >= 40) text-blue-500 @else text-gray-400 @endif">
                                        {{ $pacote['percentual'] }}%
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Lower Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Zone Breakdown -->
                <div class="glass-card p-8 rounded-[2.5rem]">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-3 mb-8">
                        <i class="bi bi-geo-alt-fill text-orange-500 text-2xl"></i> Distribuição por Zona
                    </h3>
                    <div class="h-[300px]">
                        <canvas id="zoneChart"></canvas>
                    </div>
                </div>

                <!-- Top Células -->
                <div class="glass-card p-8 rounded-[2.5rem]">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                            <i class="bi bi-trophy-fill text-yellow-500 text-2xl"></i> Top Células
                        </h3>
                        <a href="{{ route('reports.cell') }}"
                            class="text-[10px] font-black text-gray-400 hover:text-blue-500 uppercase tracking-widest transition-colors">Ver
                            Todas <i class="bi bi-arrow-right"></i></a>
                    </div>

                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($topCells as $index => $cell)
                            <div
                                class="flex items-center p-5 bg-gray-50 dark:bg-white/[0.03] rounded-2xl hover:bg-gray-100 dark:hover:bg-white/[0.08] hover:border-gray-200 dark:hover:border-white/10 border border-transparent transition-all group">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white dark:bg-white/5 shadow-sm flex items-center justify-center font-black text-gray-400 md:mr-4 group-hover:bg-yellow-500 group-hover:text-white transition-all text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-900 dark:text-white text-base truncate">{{ $cell['name'] }}</p>
                                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Destaque
                                        Edificar</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-black text-green-600 dark:text-green-400 text-lg tracking-tight">
                                        {{ number_format($cell['total'], 0, ',', '.') }} <span
                                            class="text-xs font-bold opacity-60">MT</span>
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-12 opacity-40">
                                <i class="bi bi-inbox text-4xl mb-2 text-gray-400"></i>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Sem movimentação este
                                    mês</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                const textColor = isDark ? '#94a3b8' : '#6b7280';
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

                Chart.defaults.color = textColor;
                Chart.defaults.font.family = "'Outfit', sans-serif";
                Chart.defaults.font.weight = '600';

                // Evolution Chart
                const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
                const blueGradient = evolutionCtx.createLinearGradient(0, 0, 0, 400);
                blueGradient.addColorStop(0, isDark ? 'rgba(59, 130, 246, 0.4)' : 'rgba(59, 130, 246, 0.1)');
                blueGradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

                new Chart(evolutionCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($evolucaoMensal->pluck('mes')) !!},
                        datasets: [{
                            label: 'Total Arrecadado',
                            data: {!! json_encode($evolucaoMensal->pluck('total')) !!},
                            borderColor: '#3b82f6',
                            borderWidth: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#3b82f6',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.4,
                            fill: true,
                            backgroundColor: blueGradient
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
                                grid: {
                                    color: gridColor,
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Zone Chart
                const zoneCtx = document.getElementById('zoneChart').getContext('2d');
                new Chart(zoneCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($zoneStats->pluck('name')),
                        datasets: [{
                            data: @json($zoneStats->pluck('total')),
                            backgroundColor: 'rgba(249, 115, 22, 0.4)',
                            borderColor: '#f97316',
                            borderWidth: 1,
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                grid: {
                                    color: gridColor,
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </div>
@endsection