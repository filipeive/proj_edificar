@extends('layouts.app')

@section('title', 'Painel Edificar - Evolução da Obra')
@section('page-title', 'Projecto Edificar')
@section('page-subtitle', 'Acompanhamento financeiro e evolução da construção')

@section('content')
    <div class="space-y-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="bi bi-cash-stack text-3xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-gray-400">Total Arrecadado</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ number_format($totalArrecadado, 2, ',', '.') }} MT</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-green-50 flex items-center justify-center text-green-600">
                    <i class="bi bi-graph-up-arrow text-3xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-gray-400">Este Mês</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ number_format($arrecadadoMes, 2, ',', '.') }} MT</h3>
                </div>
            </div>

            <div class="bg-blue-600 p-6 rounded-[2.5rem] shadow-xl shadow-blue-100 flex items-center space-x-4 text-white">
                <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="bi bi-people text-3xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-blue-100">Compromissos Ativos</p>
                    <h3 class="text-2xl font-black">{{ $pacotes->sum('membros') }} Membros</h3>
                </div>
            </div>
        </div>

        <!-- Charts and Table -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Evolution Chart -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
                    <i class="bi bi-activity text-blue-600"></i> Evolução Mensal
                </h3>
                <canvas id="evolutionChart" height="250"></canvas>
            </div>

            <!-- Pacotes Performance -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
                    <i class="bi bi-box-seam text-blue-600"></i> Desempenho por Pacote
                </h3>
                <div class="space-y-6">
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: false }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
@endsection