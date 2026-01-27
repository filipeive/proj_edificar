@extends('layouts.app')

@section('title', 'Painel Edificar - Evolução da Obra')
@section('page-title', 'Projecto Edificar')
@section('page-subtitle', 'Acompanhamento financeiro e evolução da construção')

@section('content')
    <div class="space-y-6 md:space-y-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div
                class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center space-x-4">
                <div
                    class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="bi bi-cash-stack text-2xl md:text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] md:text-xs font-black uppercase tracking-widest text-gray-400">Total Arrecadado
                    </p>
                    <h3 class="text-xl md:text-2xl font-black text-gray-800">
                        {{ number_format($totalArrecadado, 2, ',', '.') }} MT</h3>
                </div>
            </div>

            <div
                class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center space-x-4">
                <div
                    class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-green-50 flex items-center justify-center text-green-600">
                    <i class="bi bi-graph-up-arrow text-2xl md:text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] md:text-xs font-black uppercase tracking-widest text-gray-400">Este Mês</p>
                    <h3 class="text-xl md:text-2xl font-black text-gray-800">
                        {{ number_format($arrecadadoMes, 2, ',', '.') }} MT</h3>
                </div>
            </div>

            <div
                class="bg-blue-600 p-5 md:p-6 rounded-3xl md:rounded-[2.5rem] shadow-xl shadow-blue-100 flex items-center space-x-4 text-white">
                <div class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="bi bi-people text-2xl md:text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] md:text-xs font-black uppercase tracking-widest text-blue-100">Compromissos</p>
                    <h3 class="text-xl md:text-2xl font-black">{{ $pacotes->sum('membros') }} Membros Ativos</h3>
                </div>
            </div>

            <div
                class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center space-x-4">
                <div
                    class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-yellow-50 flex items-center justify-center text-yellow-600">
                    <i class="bi bi-patch-check text-2xl md:text-3xl"></i>
                </div>
                <div>
                    <p class="text-[10px] md:text-xs font-black uppercase tracking-widest text-gray-400">Pendentes</p>
                    <h3 class="text-xl md:text-2xl font-black text-gray-800">{{ $pendingContributions }}</h3>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        @if($authUser->isComissaoObra() || $authUser->isAdmin())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <a href="{{ route('contributions.create') }}"
                    class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2rem] border border-gray-100 flex flex-col items-center justify-center gap-2 md:gap-3 hover:shadow-lg transition-all group">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="bi bi-plus-lg text-xl md:text-2xl"></i>
                    </div>
                    <span class="text-[9px] md:text-[10px] font-black text-gray-900 uppercase tracking-widest">Registrar</span>
                </a>

                <a href="{{ route('contributions.index', ['status' => 'pendente']) }}"
                    class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2rem] border border-gray-100 flex flex-col items-center justify-center gap-2 md:gap-3 hover:shadow-lg transition-all group">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center group-hover:bg-yellow-600 group-hover:text-white transition-all">
                        <i class="bi bi-patch-check text-xl md:text-2xl"></i>
                    </div>
                    <span
                        class="text-[9px] md:text-[10px] font-black text-gray-900 uppercase tracking-widest text-center leading-tight">Validar
                        Pendentes</span>
                </a>

                <a href="{{ route('reports.global') }}"
                    class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2rem] border border-gray-100 flex flex-col items-center justify-center gap-2 md:gap-3 hover:shadow-lg transition-all group">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all">
                        <i class="bi bi-bar-chart-line text-xl md:text-2xl"></i>
                    </div>
                    <span class="text-[9px] md:text-[10px] font-black text-gray-900 uppercase tracking-widest">Global</span>
                </a>

                <a href="{{ route('dashboard.admin') }}"
                    class="bg-white p-5 md:p-6 rounded-3xl md:rounded-[2rem] border border-gray-100 flex flex-col items-center justify-center gap-2 md:gap-3 hover:shadow-lg transition-all group opacity-75 hover:opacity-100">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-gray-50 text-gray-600 flex items-center justify-center group-hover:bg-gray-600 group-hover:text-white transition-all">
                        <i class="bi bi-arrow-left text-xl md:text-2xl"></i>
                    </div>
                    <span class="text-[9px] md:text-[10px] font-black text-gray-900 uppercase tracking-widest">Voltar</span>
                </a>
            </div>
        @endif

        <!-- Main Charts: Evolution & Packages -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Evolution Chart -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
                    <i class="bi bi-activity text-blue-600"></i> Evolução Mensal
                </h3>
                <div class="relative h-[300px]">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>

            <!-- Pacotes Performance -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden">
                <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
                    <i class="bi bi-box-seam text-blue-600"></i> Desempenho por Pacote
                </h3>
                <div class="space-y-6 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($pacotes as $pacote)
                        <div class="space-y-2">
                            <div class="flex justify-between items-end">
                                <span class="text-sm font-bold text-gray-700">{{ $pacote['name'] }}</span>
                                <span class="text-xs font-black text-blue-600">{{ number_format($pacote['arrecadado'], 0) }} /
                                    {{ number_format($pacote['esperado'], 0) }} MT</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-1000"
                                    style="width: {{ min($pacote['percentual'], 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] uppercase font-black tracking-widest text-gray-400">
                                <span>{{ $pacote['membros'] }} Membros</span>
                                <span>{{ $pacote['percentual'] }}% da meta</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Secondary Stats: Zones & Cells (New Section) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Zone Chart -->
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-black text-gray-800 flex items-center gap-2">
                        <i class="bi bi-geo-alt text-orange-500"></i> Contribuições por Zona
                    </h3>
                    <div class="flex space-x-2">
                        <span
                            class="px-3 py-1 bg-orange-50 text-orange-600 rounded-lg text-xs font-bold uppercase tracking-widest">Este
                            Ano</span>
                    </div>
                </div>
                <div class="h-[300px] relative">
                    <canvas id="zoneChart"></canvas>
                </div>
            </div>

            <!-- Top Cells -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-black text-gray-800 mb-8 flex items-center gap-2">
                    <i class="bi bi-trophy text-yellow-500"></i> Top Células (Edificar)
                </h3>
                <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($topCells as $index => $cell)
                        <div
                            class="flex items-center p-4 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-lg transition-all duration-300 border border-transparent hover:border-gray-100 group">
                            <div
                                class="w-8 h-8 rounded-lg bg-white flex items-center justify-center font-black text-gray-400 mr-3 group-hover:bg-yellow-500 group-hover:text-white transition-colors text-xs">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-900 text-sm truncate">{{ $cell['name'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-green-600 tracking-tight text-sm">
                                    {{ number_format($cell['total'], 0, ',', '.') }} MT
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 text-xs py-4">Nenhum dado disponível.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Evolution Chart
            const ctx = document.getElementById('evolutionChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($evolucaoMensal->pluck('mes')) !!},
                    datasets: [{
                        label: 'Contribuições (MT)',
                        data: {!! json_encode($evolucaoMensal->pluck('total')) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { display: false } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Zone Chart (New)
            const zoneCtx = document.getElementById('zoneChart').getContext('2d');
            new Chart(zoneCtx, {
                type: 'bar',
                data: {
                    labels: @json($zoneStats->pluck('name')),
                    datasets: [{
                        label: 'Total Arrecadado (MT)',
                        data: @json($zoneStats->pluck('total')),
                        backgroundColor: 'rgba(249, 115, 22, 0.8)',
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
    </div>
@endsection
