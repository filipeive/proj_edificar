@extends('layouts.app')

@section('title', 'Gestão Admin - Portal Life Church')
@section('page-title', 'Painel de Administração')
@section('page-subtitle', 'Gestão e acompanhamento de Cultos e Visitantes')

@section('content')
    <div class="space-y-6 md:space-y-8">
        <!-- Top Stats         <!-- Top Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Card: Total Cultos -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 group hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-journal-text text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Cultos</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Total Registados
                    </p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">{{ $totalServices }}
                    </p>
                    <div class="flex items-center mt-3 text-[10px] font-bold text-gray-400">
                        Último em: {{ $latestService ? $latestService->date->format('d/m/Y') : 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- Card: Média Participação -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 group hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-graph-up-arrow text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Presença</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Média Membros/Vis.</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">{{ $avgMembers }} /
                        {{ $avgVisitors }}</p>
                    <div class="flex items-center mt-3 text-[10px] font-bold text-orange-500">
                        <i class="bi bi-person-check-fill mr-1"></i> Participação Consolidada
                    </div>
                </div>
            </div>

            <!-- Card: Total Visitantes -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 group hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-person-heart text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Visitantes</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Novas Visitas</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">{{ $totalVisitors }}
                    </p>
                    <div class="flex items-center mt-3 text-[10px] font-bold text-orange-500">
                        {{ $pendingVisitors }} Pendentes
                    </div>
                </div>
            </div>

            <!-- Card: Taxa Integração -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 group hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-950/20 p-3.5 rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-check-circle-fill text-orange-600 dark:text-orange-400 text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span
                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Integração</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-xs font-bold uppercase tracking-wider">Taxa de Sucesso
                    </p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1 tracking-tight">
                        {{ $integrationRate }}%</p>
                    <div class="flex items-center mt-3 text-[10px] font-bold text-green-500">
                        <i class="bi bi-heart-fill mr-1"></i> {{ $integratedVisitors }} Integrados
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Main Attendance Chart -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Participação nos Cultos</h3>
                    <span
                        class="px-3 py-1 bg-gray-100 dark:bg-zinc-850 rounded-xl text-[9px] font-black uppercase tracking-widest text-gray-500">Últimos
                        6 Meses</span>
                </div>
                <div class="h-[350px]">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>

            <!-- Visitor Trend Chart -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Tendência de Visitas</h3>
                    <span
                        class="px-3 py-1 bg-gray-100 dark:bg-zinc-850 rounded-xl text-[9px] font-black uppercase tracking-widest text-gray-500">Novos
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
                class="lg:col-span-2 bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Acompanhamento de Visitas
                    </h3>
                    <a href="{{ route('visitors.index') }}"
                        class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700">Ver Todos</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-450 dark:text-zinc-500 uppercase tracking-[0.2em] border-b border-gray-50 dark:border-zinc-850">
                                <th class="px-4 pb-4">Visitante</th>
                                <th class="px-4 pb-4">Data Visita</th>
                                <th class="px-4 pb-4">Zona</th>
                                <th class="px-4 pb-4 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-zinc-850">
                            @forelse($pendingVisitorsList as $visitor)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-900/40 transition-colors group">
                                    <td class="px-4 py-4">
                                        <div class="font-black text-gray-900 dark:text-white text-sm">{{ $visitor->name }}</div>
                                        <div class="text-[10px] text-gray-400 dark:text-zinc-500 font-bold">
                                            {{ $visitor->phone ?? 'Sem telefone' }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-xs font-bold text-gray-655 dark:text-zinc-400">{{ $visitor->visit_date->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-[9px] font-black text-orange-600 dark:text-orange-450 bg-orange-50 dark:bg-orange-950/20 px-2.5 py-1 rounded-lg uppercase">
                                            {{ $visitor->zone->name ?? 'S/ Zona' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <a href="{{ route('visitors.show', $visitor) }}"
                                            class="p-2 text-orange-655 hover:bg-orange-50 dark:hover:bg-orange-950/20 rounded-xl transition-all inline-block">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('visitors.edit', $visitor) }}"
                                            class="p-2 text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-950/20 rounded-xl transition-all inline-block">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-gray-400 dark:text-zinc-500 italic font-bold">Nenhum visitante pendente de acompanhamento.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Services List -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8 h-full">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Últimos Cultos</h3>
                    <a href="{{ route('services.index') }}"
                        class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700">Ver Todos</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentServices as $service)
                        <div
                            class="p-4 bg-gray-50 dark:bg-zinc-850 rounded-2xl border border-transparent hover:border-orange-500/20 transition-all group">
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">{{ $service->date->format('d/m/Y') }}</span>
                                <span
                                    class="text-[8px] font-black bg-white dark:bg-zinc-900 px-2 py-0.5 rounded-lg border border-gray-105 dark:border-zinc-800 text-gray-400 dark:text-zinc-500 uppercase">{{ $service->service_type }}</span>
                            </div>
                            <div
                                class="font-black text-gray-900 dark:text-white mb-3 group-hover:text-orange-600 transition-colors leading-tight text-sm">
                                {{ $service->theme ?: 'Sem tema' }}</div>
                            <div class="grid grid-cols-3 gap-2">
                                <div
                                    class="text-center p-2 bg-white dark:bg-zinc-900 rounded-xl border border-gray-100 dark:border-zinc-800">
                                    <div class="text-xs font-black text-gray-900 dark:text-white">{{ $service->total_members }}
                                    </div>
                                    <div class="text-[8px] font-bold text-gray-400 uppercase">Membros</div>
                                </div>
                                <div
                                    class="text-center p-2 bg-white dark:bg-zinc-900 rounded-xl border border-gray-100 dark:border-zinc-800">
                                    <div class="text-xs font-black text-orange-600 dark:text-orange-400">{{ $service->total_visitors }}</div>
                                    <div class="text-[8px] font-bold text-gray-400 uppercase">Visitas</div>
                                </div>
                                <div
                                    class="text-center p-2 bg-white dark:bg-zinc-900 rounded-xl border border-gray-100 dark:border-zinc-800">
                                    <div class="text-xs font-black text-green-600">
                                        {{ ($service->adults_salvations ?? 0) + ($service->children_salvations ?? 0) }}</div>
                                    <div class="text-[8px] font-bold text-gray-400 uppercase">Salvações</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-10 text-gray-400 dark:text-zinc-550 italic font-bold">Nenhum culto registado.</p>
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
                                borderColor: 'rgba(249, 115, 22, 1)', // Brand Orange
                                backgroundColor: 'rgba(249, 115, 22, 0.15)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 4,
                                pointBackgroundColor: 'rgba(249, 115, 22, 1)'
                            },
                            {
                                label: 'Visitantes',
                                data: @json($chartVisitors),
                                borderColor: 'rgba(68, 64, 60, 1)', // Stone Secondary
                                backgroundColor: 'rgba(68, 64, 60, 0.15)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 4,
                                pointBackgroundColor: 'rgba(68, 64, 60, 1)'
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
                            y: { beginAtZero: true, grid: { color: document.documentElement.getAttribute('data-theme') === 'dark' ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' } },
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
                                backgroundColor: 'rgba(249, 115, 22, 0.85)',
                                borderRadius: 6
                            },
                            {
                                label: 'Integrados',
                                data: @json($visitorChartIntegrated),
                                backgroundColor: 'rgba(68, 64, 60, 0.85)',
                                borderRadius: 6
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
                            y: { beginAtZero: true, grid: { color: document.documentElement.getAttribute('data-theme') === 'dark' ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection