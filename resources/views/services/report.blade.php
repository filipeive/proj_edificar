@extends('layouts.app')

@section('title', 'Análise de Cultos - Portal Life Church')
@section('page-title', 'Análise de Cultos')
@section('page-subtitle', 'Tendências de Frequência e Crescimento')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <a href="{{ route('services.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para a lista
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Gráfico Principal -->
            <div class="lg:col-span-8">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-8 flex items-center gap-2">
                        <i class="bi bi-graph-up text-blue-600"></i>
                        Tendência de Frequência (Últimos 12 Cultos)
                    </h3>
                    <div class="aspect-[16/9] relative">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Stats Rápidas -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Métrica de Visitantes</h3>
                    <div class="aspect-square relative">
                        <canvas id="visitorsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Resumo dos Últimos 12 Cultos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Público
                                Total</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Visitantes
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Decisões
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($trendServices as $service)
                            @php
                                $total = $service->adults_members + $service->adults_visitors + $service->children_members + $service->children_visitors;
                                $members = $service->adults_members + $service->children_members;
                                $visitors = $service->adults_visitors + $service->children_visitors;
                                $salvations = $service->adults_salvations + $service->children_salvations;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-4 font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($service->date)->format('d/m/Y') }}</td>
                                <td class="px-8 py-4 font-black text-blue-600">{{ $total }}</td>
                                <td class="px-8 py-4 text-gray-600">{{ $members }}</td>
                                <td class="px-8 py-4 text-gray-600">{{ $visitors }}</td>
                                <td class="px-8 py-4">
                                    <span
                                        class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-[10px] font-black">{{ $salvations }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Main Attendance Trend Chart
                const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($stats['labels']) !!},
                        datasets: [{
                            label: 'Público Total',
                            data: {!! json_encode($stats['attendance']) !!},
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#2563eb',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Visitors Doughnut Chart
                const visitorsCtx = document.getElementById('visitorsChart').getContext('2d');
                new Chart(visitorsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Membros', 'Visitantes'],
                        datasets: [{
                            data: [
                                    {{ $trendServices->sum(fn($s) => $s->adults_members + $s->children_members) }},
                                {{ $trendServices->sum(fn($s) => $s->adults_visitors + $s->children_visitors) }}
                            ],
                            backgroundColor: ['#2563eb', '#eab308'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 20, font: { weight: 'bold', size: 10 } } }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection