@extends('layouts.app')

@section('title', 'Painel Financeiro - Portal Life Church')
@section('page-title', 'Painel Financeiro')
@section('page-subtitle', 'Visão Geral & Métricas Consolidadas')

@section('content')
    <div class="space-y-5">
        <!-- Header & Top Actions -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gestão Financeira</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Painel Financeiro</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Visão consolidada de entradas, saídas e fluxo de caixa ({{ $month }}/{{ $year }}).</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('financial.dashboard.export.pdf', request()->query()) }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3.5 py-2 text-xs font-bold text-red-700 hover:bg-red-50 shadow-sm">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>PDF</span>
                </a>
                <a href="{{ route('financial.dashboard.export.excel', request()->query()) }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-green-200 bg-white px-3.5 py-2 text-xs font-bold text-green-800 hover:bg-green-50 shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    <span>Excel</span>
                </a>
                <button onclick="window.print()" title="Imprimir painel"
                    class="inline-flex justify-center items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-printer"></i>
                </button>
                <a href="{{ route('expenses.create') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3.5 py-2 text-xs font-bold text-red-700 hover:bg-red-100 shadow-sm">
                    <i class="bi bi-dash-circle"></i> <span>Despesa</span>
                </a>
                <a href="{{ route('contributions.create') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-plus-circle"></i> <span>Entrada</span>
                </a>
            </div>
        </header>

        @if(!empty($hasEdificar))
            <!-- Scope Tabs -->
            <div class="inline-flex p-1 bg-gray-100 rounded-xl border border-gray-200">
                <a href="{{ route('financial.dashboard', array_merge(request()->all(), ['scope' => 'eclesiastico'])) }}"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $scope == 'eclesiastico' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">
                    Eclesiástico (Igreja)
                </a>
                <a href="{{ route('financial.dashboard', array_merge(request()->all(), ['scope' => 'edificar'])) }}"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ $scope == 'edificar' ? 'bg-white text-orange-700 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">
                    Projeto Edificar
                </a>
            </div>
        @endif

        <!-- Filter Panel -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <form action="{{ route('financial.dashboard') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                <input type="hidden" name="scope" value="{{ $scope }}">
                <div>
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Mês</label>
                    <select name="month" data-searchable="false"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Ano</label>
                    <select name="year" data-searchable="false"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-gray-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-gray-800 transition shadow-sm">
                    <i class="bi bi-funnel mr-1"></i> Filtrar
                </button>
            </form>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Entradas -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Entradas ({{ $month }}/{{ $year }})</p>
                    <i class="bi bi-graph-up-arrow text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">
                    {{ number_format($grandTotal, 2, ',', '.') }} MT
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Total de receitas no período</p>
            </article>

            <!-- Saídas -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Saídas ({{ $month }}/{{ $year }})</p>
                    <i class="bi bi-graph-down-arrow text-rose-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-rose-700 tracking-tight tabular-nums">
                    {{ number_format($totalExpenses, 2, ',', '.') }} MT
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Total de despesas operacionais</p>
            </article>

            <!-- Balanço -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Saldo Líquido</p>
                    <i class="bi bi-wallet2 text-teal-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black tracking-tight tabular-nums {{ $balance >= 0 ? 'text-teal-700' : 'text-red-700' }}">
                    {{ number_format($balance, 2, ',', '.') }} MT
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Balanço líquido do período</p>
            </article>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Line Chart: Annual Trend -->
            <section class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-sm font-black text-gray-900">Fluxo Financeiro Anual ({{ $year }})</h3>
                    <p class="text-xs text-gray-500">Evolução mensal de entradas e saídas</p>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="financeTrendChart"></canvas>
                </div>
            </section>

            <!-- Doughnut Chart: Expenses -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-sm font-black text-gray-900">Despesas por Categoria</h3>
                    <p class="text-xs text-gray-500">Distribuição percentual</p>
                </div>
                <div class="relative h-64 w-full flex items-center justify-center">
                    @if(collect($expenseValues)->sum() > 0)
                        <canvas id="expenseDoughnutChart"></canvas>
                    @else
                        <div class="text-gray-400 text-center text-xs font-semibold">Sem dados de despesas</div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Recent Transactions & Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Recent Transactions -->
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Últimas Movimentações</h3>
                        <p class="text-xs text-gray-500">Entradas e saídas recentes</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($recentTransactions as $transaction)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0 {{ $transaction->type == 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                <i class="bi {{ $transaction->type == 'income' ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }}"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $transaction->description }}</p>
                                                <p class="text-[11px] text-gray-500 tabular-nums">
                                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right shrink-0">
                                        <span class="text-xs sm:text-sm font-black tabular-nums {{ $transaction->type == 'income' ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $transaction->type == 'income' ? '+' : '-' }}
                                            {{ number_format($transaction->amount, 2, ',', '.') }} MT
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-gray-500 text-xs">
                                        Nenhuma movimentação recente
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Detailed Breakdown (Income) -->
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-200">
                    <h3 class="text-sm font-black text-gray-900">Entradas por Tipo</h3>
                    <p class="text-xs text-gray-500">Agrupamento por categoria de receita</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($totals as $item)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-4 py-3 text-xs sm:text-sm font-bold text-gray-800">{{ $item['type'] }}</td>
                                    <td class="px-4 py-3 text-right text-xs sm:text-sm font-black text-gray-950 tabular-nums">
                                        {{ number_format($item['total'], 2, ',', '.') }} MT
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            // Common Options
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#9ca3af';

            // 1. Trend Chart
            const ctxTrend = document.getElementById('financeTrendChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [
                        {
                            label: 'Entradas',
                            data: @json($monthlyIncome),
                            borderColor: '#10b981', // green-500
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Saídas',
                            data: @json($monthlyExpenses),
                            borderColor: '#ef4444', // red-500
                            backgroundColor: 'rgba(239, 68, 68, 0.05)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 2], drawBorder: false } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. Expense Doughnut
            const expenseValues = @json($expenseValues);
            if (expenseValues.length > 0) {
                const ctxDoughnut = document.getElementById('expenseDoughnutChart').getContext('2d');
                new Chart(ctxDoughnut, {
                    type: 'doughnut',
                    data: {
                        labels: @json($expenseLabels),
                        datasets: [{
                            data: expenseValues,
                            backgroundColor: ['#3b82f6', '#ef4444', '#f59e0b', '#10b981', '#6366f1', '#ec4899', '#8b5cf6'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 15 } }
                        },
                        cutout: '70%',
                    }
                });
            }
        </script>
    @endpush
@endsection
