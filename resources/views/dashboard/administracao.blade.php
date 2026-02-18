@extends('layouts.app')

@section('title', 'Gestão Admin - Portal Life Church')
@section('page-title', 'Painel de Administração')
@section('page-subtitle', 'Gestão e acompanhamento de Cultos e Visitantes')

@section('content')
    <div class="space-y-6 md:space-y-8">
        <!-- Top Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card: Total Cultos -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 group hover:shadow-xl transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-900/30 p-4 rounded-2xl group-hover:bg-orange-600 transition-colors duration-500">
                        <i
                            class="bi bi-journal-text text-orange-600 dark:text-orange-400 text-2xl group-hover:text-white transition-colors duration-500"></i>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Cultos</span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Total Registrados
                    </p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 tracking-tighter">{{ $totalServices }}
                    </p>
                    <div class="flex items-center mt-4 text-xs font-bold text-gray-400">
                        Último em: {{ $latestService ? $latestService->date->format('d/m/Y') : 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- Card: Média Participação -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 group hover:shadow-xl transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-2xl group-hover:bg-blue-600 transition-colors duration-500">
                        <i
                            class="bi bi-graph-up-arrow text-blue-600 dark:text-blue-400 text-2xl group-hover:text-white transition-colors duration-500"></i>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Presença</span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Média
                        Membros/Vis.</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 tracking-tighter">{{ $avgMembers }} /
                        {{ $avgVisitors }}</p>
                    <div class="flex items-center mt-4 text-xs font-bold text-blue-500">
                        <i class="bi bi-person-check-fill mr-1"></i> Participação Consolidada
                    </div>
                </div>
            </div>

            <!-- Card: Total Visitantes -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 group hover:shadow-xl transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-2xl group-hover:bg-purple-600 transition-colors duration-500">
                        <i
                            class="bi bi-person-heart text-purple-600 dark:text-purple-400 text-2xl group-hover:text-white transition-colors duration-500"></i>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Visitantes</span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Novas Visitas</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 tracking-tighter">{{ $totalVisitors }}
                    </p>
                    <div class="flex items-center mt-4 text-xs font-bold text-purple-500">
                        {{ $pendingVisitors }} Pendentes de Acompanhamento
                    </div>
                </div>
            </div>

            <!-- Card: Taxa Integração -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 group hover:shadow-xl transition-all duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-green-50 dark:bg-green-900/30 p-4 rounded-2xl group-hover:bg-green-600 transition-colors duration-500">
                        <i
                            class="bi bi-check-circle-fill text-green-600 dark:text-green-400 text-2xl group-hover:text-white transition-colors duration-500"></i>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Integração</span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Taxa de Sucesso
                    </p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 tracking-tighter">
                        {{ $integrationRate }}%</p>
                    <div class="flex items-center mt-4 text-xs font-bold text-green-500">
                        <i class="bi bi-heart-fill mr-1"></i> {{ $integratedVisitors }} Integrados no total
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Main Attendance Chart -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Participação nos Cultos</h3>
                    <span
                        class="px-4 py-1.5 bg-gray-50 dark:bg-gray-700 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-500">Últimos
                        6 Meses</span>
                </div>
                <div class="h-[350px]">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            <!-- Visitor Trend Chart -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Tendência de Visitas</h3>
                    <span
                        class="px-4 py-1.5 bg-gray-50 dark:bg-gray-700 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-500">Novos
                        vs Integrados</span>
                </div>
                <div class="h-[350px]">
                    <canvas id="visitorChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Upcoming Pending Visitors -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Acompanhamento de Visitas
                    </h3>
                    <a href="{{ route('visitors.index') }}"
                        class="text-xs font-black text-blue-600 uppercase tracking-widest">Ver Todos</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-3">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                <th class="px-4 pb-2">Visitante</th>
                                <th class="px-4 pb-2">Data Visita</th>
                                <th class="px-4 pb-2">Zona/Célula</th>
                                <th class="px-4 pb-2 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingVisitorsList as $visitor)
                                <tr class="group">
                                    <td
                                        class="px-4 py-4 bg-gray-50 dark:bg-gray-700/50 rounded-l-2xl border-y border-l border-transparent group-hover:border-blue-100 dark:group-hover:border-blue-900 transition-all">
                                        <div class="font-black text-gray-900 dark:text-white">{{ $visitor->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase">
                                            {{ $visitor->phone ?? 'Sem telefone' }}</div>
                                    </td>
                                    <td
                                        class="px-4 py-4 bg-gray-50 dark:bg-gray-700/50 border-y border-transparent group-hover:border-blue-100 dark:group-hover:border-blue-900 transition-all">
                                        <span
                                            class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ $visitor->visit_date->format('d/m/Y') }}</span>
                                    </td>
                                    <td
                                        class="px-4 py-4 bg-gray-50 dark:bg-gray-700/50 border-y border-transparent group-hover:border-blue-100 dark:group-hover:border-blue-900 transition-all">
                                        <span
                                            class="text-[10px] font-black text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded-lg uppercase">
                                            {{ $visitor->zone->name ?? 'S/ Zona' }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-4 bg-gray-50 dark:bg-gray-700/50 rounded-r-2xl border-y border-r border-transparent group-hover:border-blue-100 dark:group-hover:border-blue-900 transition-all text-right">
                                        <a href="{{ route('visitors.show', $visitor) }}"
                                            class="p-2 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all inline-block">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('visitors.edit', $visitor) }}"
                                            class="p-2 text-orange-600 hover:bg-orange-600 hover:text-white rounded-xl transition-all inline-block">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-gray-400 italic font-bold">Nenhum visitante
                                        pendente de acompanhamento.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Services List -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 h-full">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Últimos Cultos</h3>
                    <a href="{{ route('services.index') }}"
                        class="text-xs font-black text-orange-600 uppercase tracking-widest">Ver Todos</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentServices as $service)
                        <div
                            class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-3xl border border-transparent hover:border-orange-100 dark:hover:border-orange-900 transition-all group">
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-[10px] font-black text-orange-600 uppercase tracking-widest">{{ $service->date->format('d/m/Y') }}</span>
                                <span
                                    class="text-[9px] font-black bg-white dark:bg-gray-800 px-2 py-0.5 rounded-lg border border-gray-100 dark:border-gray-700 text-gray-400 uppercase">{{ $service->service_type }}</span>
                            </div>
                            <div
                                class="font-black text-gray-900 dark:text-white mb-3 group-hover:text-orange-600 transition-colors">
                                {{ $service->theme ?: 'Sem tema' }}</div>
                            <div class="grid grid-cols-3 gap-2">
                                <div
                                    class="text-center p-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-50 dark:border-gray-700">
                                    <div class="text-xs font-black text-gray-900 dark:text-white">{{ $service->total_members }}
                                    </div>
                                    <div class="text-[8px] font-bold text-gray-400 uppercase">Membros</div>
                                </div>
                                <div
                                    class="text-center p-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-50 dark:border-gray-700">
                                    <div class="text-xs font-black text-blue-600">{{ $service->total_visitors }}</div>
                                    <div class="text-[8px] font-bold text-gray-400 uppercase">Visitas</div>
                                </div>
                                <div
                                    class="text-center p-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-50 dark:border-gray-700">
                                    <div class="text-xs font-black text-green-600">
                                        {{ ($service->adults_salvations ?? 0) + ($service->children_salvations ?? 0) }}</div>
                                    <div class="text-[8px] font-bold text-gray-400 uppercase">Salvações</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-10 text-gray-400 italic font-bold">Nenhum culto registrado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Attendance Chart
                const attCtx = document.getElementById('attendanceChart').getContext('2d');
                new Chart(attCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Membros',
                                data: @json($chartMembers),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 4,
                                pointBackgroundColor: '#3b82f6'
                            },
                            {
                                label: 'Visitantes',
                                data: @json($chartVisitors),
                                borderColor: '#a855f7',
                                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 4,
                                pointBackgroundColor: '#a855f7'
                            },
                            {
                                label: 'Salvações',
                                data: @json($chartSalvations),
                                borderColor: '#10b981',
                                borderDash: [5, 5],
                                fill: false,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    font: { weight: 'bold', size: 10 }
                                }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Visitor Trend Chart
                const visCtx = document.getElementById('visitorChart').getContext('2d');
                new Chart(visCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($visitorChartLabels),
                        datasets: [
                            {
                                label: 'Novas Visitas',
                                data: @json($visitorChartTotal),
                                backgroundColor: '#8b5cf6',
                                borderRadius: 8
                            },
                            {
                                label: 'Integrados',
                                data: @json($visitorChartIntegrated),
                                backgroundColor: '#10b981',
                                borderRadius: 8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    font: { weight: 'bold', size: 10 }
                                }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection