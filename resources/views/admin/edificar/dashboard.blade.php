@extends('layouts.app')

@section('title', 'Painel Edificar - Evolução da Obra')
@section('page-title', 'Projecto Edificar')
@section('page-subtitle', 'Acompanhamento financeiro e evolução da construção')

@section('content')
    <div class="space-y-5">
        <style>
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.12); border-radius: 10px; }
        </style>

        <!-- Header section with Title and Period -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Projecto Edificar</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Evolução Financeira da Obra</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Dados atualizados em tempo real para o fundo de construção ({{ now()->translatedFormat('F Y') }}).</p>
            </div>
            @if($authUser->isComissaoObra() || $authUser->isAdmin())
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('dashboard.admin') }}"
                        class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-arrow-left"></i> <span>Painel Geral</span>
                    </a>
                    <a href="{{ route('reports.global') }}"
                        class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-file-earmark-bar-graph text-orange-700"></i> <span>Relatórios</span>
                    </a>
                    <a href="{{ route('contributions.index', ['status' => 'pendente']) }}"
                        class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-shield-check text-amber-700"></i> <span>Validar ({{ $pendingContributions }})</span>
                    </a>
                    <a href="{{ route('contributions.create') }}"
                        class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                        <i class="bi bi-plus-lg"></i> <span>Registar</span>
                    </a>
                </div>
            @endif
        </header>

        <!-- Dashboard Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <!-- Total Arrecadado -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Total Arrecadado</p>
                    <i class="bi bi-bank2 text-blue-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">
                    {{ number_format($totalArrecadado, 2, ',', '.') }} MT
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Acumulado geral</p>
            </article>

            <!-- Este Mês -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Este Mês</p>
                    <i class="bi bi-stars text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">
                    {{ number_format($arrecadadoMes, 2, ',', '.') }} MT
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Período corrente</p>
            </article>

            <!-- Membros Ativos -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Compromissos</p>
                    <i class="bi bi-people text-indigo-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">
                    {{ $pacotes->sum('membros') }}
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Membros ativos</p>
            </article>

            <!-- Pendentes -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Validação Pendente</p>
                    <i class="bi bi-clock-history text-amber-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-amber-700 tracking-tight tabular-nums">
                    {{ $pendingContributions }}
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Registos em análise</p>
            </article>
        </div>

        <!-- Main Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Evolution Chart -->
            <section class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Fluxo Mensal</h3>
                        <p class="text-xs text-gray-500">Evolução histórica de arrecadação</p>
                    </div>
                    <span class="px-2.5 py-0.5 bg-orange-50 text-orange-800 rounded-full text-[10px] font-black uppercase tracking-wider">
                        Edificar
                    </span>
                </div>
                <div class="h-64 relative">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </section>

            <!-- Package Performance -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-sm font-black text-gray-900">Desempenho por Pacote</h3>
                    <p class="text-xs text-gray-500">Realizado vs Meta mensal</p>
                </div>

                <div class="space-y-4 max-h-64 overflow-y-auto pr-1 custom-scrollbar">
                    @foreach($pacotes as $pacote)
                        <div class="space-y-1.5 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                            <div class="flex justify-between items-end">
                                <div>
                                    <span class="text-xs font-bold text-gray-900">{{ $pacote['name'] }}</span>
                                    <span class="block text-[10px] text-gray-500">{{ $pacote['membros'] }} Membros</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-black text-gray-950 tabular-nums">
                                        {{ number_format($pacote['arrecadado'], 0, ',', '.') }} MT
                                    </div>
                                    <div class="text-[10px] text-gray-400 tabular-nums">
                                        Meta: {{ number_format($pacote['esperado'], 0, ',', '.') }} MT
                                    </div>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-orange-600 h-full rounded-full transition-all"
                                    style="width: {{ min($pacote['percentual'], 100) }}%">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <span class="text-[10px] font-black tabular-nums @if($pacote['percentual'] >= 80) text-emerald-700 @elseif($pacote['percentual'] >= 40) text-orange-700 @else text-gray-500 @endif">
                                    {{ $pacote['percentual'] }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Lower Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Zone Breakdown -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-sm font-black text-gray-900">Distribuição por Zona</h3>
                    <p class="text-xs text-gray-500">Contributo territorial para o projeto</p>
                </div>
                <div class="h-64 relative">
                    <canvas id="zoneChart"></canvas>
                </div>
            </section>

            <!-- Top Células -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Top Células</h3>
                        <p class="text-xs text-gray-500">Destaques do período</p>
                    </div>
                    <a href="{{ route('reports.cell') }}" class="text-xs font-bold text-orange-800 hover:underline">
                        Ver Todas
                    </a>
                </div>

                <div class="space-y-2.5 max-h-64 overflow-y-auto pr-1 custom-scrollbar">
                    @forelse($topCells as $index => $cell)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200/80">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-black text-gray-700 text-xs shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 text-xs sm:text-sm truncate">{{ $cell['name'] }}</p>
                                    <p class="text-[10px] text-gray-500">Destaque Edificar</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-black text-emerald-700 text-sm tabular-nums">
                                    {{ number_format($cell['total'], 0, ',', '.') }} MT
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <p class="text-xs text-gray-500">Sem movimentação este mês</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                const textColor = isDark ? '#94a3b8' : '#6b7280';
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

                Chart.defaults.color = textColor;
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
                            borderWidth: 3,
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
                                padding: 10,
                                borderRadius: 8,
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
                            backgroundColor: 'rgba(249, 115, 22, 0.75)',
                            borderColor: '#f97316',
                            borderWidth: 1,
                            borderRadius: 6,
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
