@extends('layouts.app')

@section('title', 'Relatório Global - Portal Life Church')
@section('page-title', 'Projecto Edificar')
@section('page-subtitle', 'Consolidação Geral de Métricas e Performance Financeira')

@section('content')
    <div class="space-y-5">
        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Projecto Edificar</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Relatório Global</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Análise detalhada de contribuições, zonas e estrutura ministerial.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('reports.export.pdf', array_merge(['type' => 'global'], request()->query())) }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3.5 py-2 text-xs font-bold text-red-700 hover:bg-red-50 shadow-sm">
                    <i class="bi bi-file-earmark-pdf"></i> <span>PDF</span>
                </a>
                <a href="{{ route('reports.export.excel', array_merge(['type' => 'global'], request()->query())) }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-green-200 bg-white px-3.5 py-2 text-xs font-bold text-green-800 hover:bg-green-50 shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet"></i> <span>Excel</span>
                </a>
                <a href="{{ route('reports.export.excel', ['type' => 'structure']) }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-diagram-3"></i> <span>Estrutura</span>
                </a>
            </div>
        </header>

        <!-- Filters Section -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <form action="{{ route('reports.global') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <div class="lg:col-span-3">
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Data Início</label>
                    <input type="date" name="start_date"
                        value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800">
                </div>
                <div class="lg:col-span-3">
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Data Fim</label>
                    <input type="date" name="end_date"
                        value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800">
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Zona</label>
                    <select name="zone_id"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800">
                        <option value="">Todas</option>
                        @foreach($allZones as $zone)
                            <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Status</label>
                    <select name="status"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800">
                        <option value="">Todos</option>
                        <option value="verificada" {{ request('status') == 'verificada' ? 'selected' : '' }}>Verificada</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="rejeitada" {{ request('status') == 'rejeitada' ? 'selected' : '' }}>Rejeitada</option>
                    </select>
                </div>
                <div class="lg:col-span-2 flex items-center gap-2">
                    <button type="submit"
                        class="flex-1 rounded-lg bg-gray-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-gray-800 transition shadow-sm">
                        <i class="bi bi-funnel mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('reports.global') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm"
                        title="Limpar Filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats & Chart Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Total Card -->
            <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Total Filtrado</p>
                        <i class="bi bi-wallet2 text-emerald-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black text-gray-950 tracking-tight tabular-nums">
                        {{ number_format($total, 0, ',', '.') }} <span class="text-sm font-bold text-emerald-700">MT</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Soma consolidada dos registos filtrados</p>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2 text-xs font-semibold text-gray-600 tabular-nums">
                    <i class="bi bi-calendar-range text-orange-700"></i>
                    <span>{{ $startDate->format('d/m/Y') }} — {{ $endDate->format('d/m/Y') }}</span>
                </div>
            </article>

            <!-- Chart Card -->
            <section class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-sm font-black text-gray-900">Distribuição por Zona</h3>
                    <p class="text-xs text-gray-500">Montante arrecadado por zona pastoral</p>
                </div>
                <div class="h-64 relative">
                    <canvas id="zoneChart"></canvas>
                </div>
            </section>
        </div>

        <!-- Table Section -->
        <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Detalhamento de Contribuições</h3>
                    <p class="text-xs text-gray-500">Lista de lançamentos no período</p>
                </div>
                <span class="px-2.5 py-0.5 bg-gray-100 rounded-full text-xs font-bold text-gray-600">
                    {{ $contributions->count() }} Registos
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3">Doador</th>
                            <th class="px-4 py-3">Origem</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($contributions as $contribution)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-700 flex items-center justify-center font-black text-xs shrink-0">
                                            {{ substr($contribution->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs sm:text-sm font-bold text-gray-900">{{ $contribution->user->name }}</span>
                                            <span class="text-[11px] text-gray-500 tabular-nums">{{ $contribution->contribution_date->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-800">
                                            {{ $contribution->zone->name ?? 'N/A' }}
                                        </span>
                                        <span class="text-[11px] text-gray-500">
                                            {{ $contribution->user->cell->name ?? 'Sem Célula' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $statusClasses = match ($contribution->status) {
                                            'verificada' => 'bg-green-100 text-green-800',
                                            'pendente' => 'bg-amber-100 text-amber-800',
                                            'rejeitada' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $statusClasses }}">
                                        {{ $contribution->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-xs sm:text-sm font-black text-gray-950 tabular-nums">
                                        {{ number_format($contribution->amount, 0, ',', '.') }} MT
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-xs text-gray-500">
                                    Nenhum registo encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($contributions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="p-4 border-t border-gray-200">
                    {{ $contributions->links() }}
                </div>
            @endif
        </section>

        <!-- Chart Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                const textColor = isDark ? '#94a3b8' : '#6b7280';
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

                Chart.defaults.color = textColor;
                Chart.defaults.font.weight = '600';

                const ctx = document.getElementById('zoneChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($zoneStats->pluck('name')),
                        datasets: [{
                            label: 'Total Arrecadado',
                            data: @json($zoneStats->pluck('total')),
                            backgroundColor: 'rgba(234, 88, 12, 0.85)',
                            borderColor: 'transparent',
                            borderRadius: 6,
                            barThickness: 28,
                            hoverBackgroundColor: '#c2410c'
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
                                beginAtZero: true,
                                grid: {
                                    color: gridColor,
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: (value) => value >= 1000 ? `${value / 1000}k` : value,
                                    font: { size: 10 }
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 10 },
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
