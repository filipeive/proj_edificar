@extends('layouts.app')

@section('title', 'Gestão Admin - Portal Life Church')
@section('page-title', 'Painel de Administração')
@section('page-subtitle', 'Gestão e acompanhamento de Cultos e Visitantes')

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Administração & Cultos</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Painel de Administração</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Gestão e acompanhamento de cultos, presenças e integração de visitantes.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('visitors.index') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-person-heart text-orange-700"></i> <span>Visitantes</span>
                </a>
                <a href="{{ route('services.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-plus-lg"></i> <span>Registar Culto</span>
                </a>
            </div>
        </header>

        <!-- Top Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <!-- Card: Total Cultos -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Total Cultos</p>
                    <i class="bi bi-journal-text text-orange-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $totalServices }}</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">
                    Último em: {{ $latestService ? $latestService->date->format('d/m/Y') : 'N/A' }}
                </p>
            </article>

            <!-- Card: Média Participação -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Média Membros/Vis.</p>
                    <i class="bi bi-graph-up-arrow text-blue-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $avgMembers }} / {{ $avgVisitors }}</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Participação consolidada</p>
            </article>

            <!-- Card: Total Visitantes -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Novas Visitas</p>
                    <i class="bi bi-person-heart text-amber-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $totalVisitors }}</p>
                <p class="text-[10px] sm:text-[11px] text-orange-700 font-semibold mt-0.5 truncate">{{ $pendingVisitors }} pendentes</p>
            </article>

            <!-- Card: Taxa Integração -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Taxa de Sucesso</p>
                    <i class="bi bi-check-circle text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $integrationRate }}%</p>
                <p class="text-[10px] sm:text-[11px] text-emerald-700 font-semibold mt-0.5 truncate">{{ $integratedVisitors }} integrados</p>
            </article>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Main Attendance Chart -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Participação nos Cultos</h3>
                        <p class="text-xs text-gray-500">Evolução de membros, visitantes e salvações</p>
                    </div>
                    <span class="px-2.5 py-1 bg-gray-100 rounded-lg text-[10px] font-black uppercase tracking-wider text-gray-600">Últimos 6 Meses</span>
                </div>
                <div class="h-64 relative">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </section>

            <!-- Visitor Trend Chart -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Tendência de Visitas</h3>
                        <p class="text-xs text-gray-500">Comparação entre novas visitas e integrados</p>
                    </div>
                    <span class="px-2.5 py-1 bg-gray-100 rounded-lg text-[10px] font-black uppercase tracking-wider text-gray-600">Novos vs Integrados</span>
                </div>
                <div class="h-64 relative">
                    <canvas id="visitorChart"></canvas>
                </div>
            </section>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Upcoming Pending Visitors -->
            <section class="lg:col-span-2 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-gray-200 p-4 sm:p-5">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Acompanhamento de Visitas</h3>
                        <p class="text-xs text-gray-500">Visitantes pendentes de contacto e integração</p>
                    </div>
                    <a href="{{ route('visitors.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todos</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Visitante</th>
                                <th class="px-4 py-3">Data Visita</th>
                                <th class="px-4 py-3">Zona</th>
                                <th class="px-4 py-3 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($pendingVisitorsList as $visitor)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-gray-900 text-sm">{{ $visitor->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $visitor->phone ?? 'Sem telefone' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600 tabular-nums">
                                        {{ $visitor->visit_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-orange-50 px-2.5 py-0.5 text-[10px] font-black uppercase text-orange-800">
                                            {{ $visitor->zone->name ?? 'S/ Zona' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('visitors.show', $visitor) }}" class="inline-flex items-center gap-1 text-xs font-bold text-orange-800 hover:underline">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('visitors.edit', $visitor) }}" class="inline-flex items-center gap-1 text-xs font-bold text-gray-600 hover:text-gray-900 hover:underline">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-500">Nenhum visitante pendente de acompanhamento.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Recent Services List -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Últimos Cultos</h3>
                        <p class="text-xs text-gray-500">Resumo de participação</p>
                    </div>
                    <a href="{{ route('services.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todos</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentServices as $service)
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200/80">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-black text-orange-700 uppercase tracking-wider tabular-nums">{{ $service->date->format('d/m/Y') }}</span>
                                <span class="text-[9px] font-black bg-white px-2 py-0.5 rounded border border-gray-200 text-gray-600 uppercase">{{ $service->service_type }}</span>
                            </div>
                            <div class="font-bold text-gray-900 mb-2.5 text-xs sm:text-sm truncate">
                                {{ $service->theme ?: 'Sem tema' }}
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="text-center p-1.5 bg-white rounded-lg border border-gray-200">
                                    <div class="text-xs font-black text-gray-950 tabular-nums">{{ $service->total_members }}</div>
                                    <div class="text-[9px] font-bold text-gray-500 uppercase">Membros</div>
                                </div>
                                <div class="text-center p-1.5 bg-white rounded-lg border border-gray-200">
                                    <div class="text-xs font-black text-orange-700 tabular-nums">{{ $service->total_visitors }}</div>
                                    <div class="text-[9px] font-bold text-gray-500 uppercase">Visitas</div>
                                </div>
                                <div class="text-center p-1.5 bg-white rounded-lg border border-gray-200">
                                    <div class="text-xs font-black text-emerald-700 tabular-nums">
                                        {{ ($service->adults_salvations ?? 0) + ($service->children_salvations ?? 0) }}
                                    </div>
                                    <div class="text-[9px] font-bold text-gray-500 uppercase">Salvações</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-8 text-xs text-gray-500">Nenhum culto registado.</p>
                    @endforelse
                </div>
            </section>
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
