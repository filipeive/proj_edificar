@extends('layouts.app')

@section('title', 'Painel Financeiro - Portal Life Church')

@section('content')
    <div class="space-y-8">
        <!-- Header & Top Actions -->
        <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-center md:text-left">
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Painel Financeiro</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Visão Geral & Métricas</p>
            </div>
            <div class="flex flex-wrap justify-center gap-3">
                <button onclick="window.print()" class="bg-gray-100 text-gray-600 px-6 py-3 rounded-2xl hover:bg-gray-200 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-sm">
                    <i class="bi bi-printer text-lg mr-2"></i> Report
                </button>
                <a href="{{ route('expenses.create') }}" class="bg-red-50 text-red-600 px-6 py-3 rounded-2xl hover:bg-red-100 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-sm border border-red-100">
                    <i class="bi bi-dash-circle mr-2"></i> Despesa
                </a>
                <a href="{{ route('contributions.create') }}" class="bg-green-50 text-green-600 px-6 py-3 rounded-2xl hover:bg-green-100 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-sm border border-green-100">
                    <i class="bi bi-plus-circle mr-2"></i> Entrada
                </a>
            </div>
        </div>

        <!-- Scope Tabs -->
        <div class="flex p-1 bg-gray-100 rounded-2xl w-fit mx-auto md:mx-0">
            <a href="{{ route('financial.dashboard', array_merge(request()->all(), ['scope' => 'eclesiastico'])) }}" 
               class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $scope == 'eclesiastico' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
               Eclesiástico (Igreja)
            </a>
            <a href="{{ route('financial.dashboard', array_merge(request()->all(), ['scope' => 'edificar'])) }}" 
               class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $scope == 'edificar' ? 'bg-white text-orange-500 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
               Projeto Edificar
            </a>
        </div>

        <!-- Filter Panel -->
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
            <form action="{{ route('financial.dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <input type="hidden" name="scope" value="{{ $scope }}">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Mês</label>
                    <select name="month"
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 rounded-2xl font-bold text-gray-700">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ano</label>
                    <select name="year"
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 rounded-2xl font-bold text-gray-700">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit"
                    class="w-full py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                    Filtrar
                </button>
            </form>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Entradas -->
            <div
                class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-[2.5rem] text-white shadow-xl shadow-green-100 relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-green-100 uppercase tracking-widest mb-1">Entradas
                        ({{ $month }}/{{ $year }})</p>
                    <h3 class="text-3xl md:text-4xl font-black tracking-tighter">
                        {{ number_format($grandTotal, 2, ',', '.') }}<small class="text-lg opacity-60">MT</small></h3>
                </div>
                <i
                    class="bi bi-graph-up-arrow absolute right-6 bottom-6 text-6xl text-white opacity-10 group-hover:scale-110 transition-transform"></i>
            </div>

            <!-- Saídas -->
            <div
                class="bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-[2.5rem] text-white shadow-xl shadow-red-100 relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-red-100 uppercase tracking-widest mb-1">Saídas
                        ({{ $month }}/{{ $year }})</p>
                    <h3 class="text-3xl md:text-4xl font-black tracking-tighter">
                        {{ number_format($totalExpenses, 2, ',', '.') }}<small class="text-lg opacity-60">MT</small></h3>
                </div>
                <i
                    class="bi bi-graph-down-arrow absolute right-6 bottom-6 text-6xl text-white opacity-10 group-hover:scale-110 transition-transform"></i>
            </div>

            <!-- Balanço -->
            <div class="bg-white p-6 rounded-[2.5rem] shadow-lg border border-gray-100 relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Saldo Líquido</p>
                    <h3
                        class="text-3xl md:text-4xl font-black {{ $balance >= 0 ? 'text-blue-600' : 'text-red-500' }} tracking-tighter">
                        {{ number_format($balance, 2, ',', '.') }}<small class="text-lg opacity-60">MT</small>
                    </h3>
                </div>
                <i class="bi bi-wallet2 absolute right-6 bottom-6 text-6xl text-gray-100 opacity-50"></i>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Line Chart: Annual Trend -->
            <div class="lg:col-span-2 bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Fluxo Financeiro Anual
                    ({{ $year }})</h3>
                <div class="relative h-72 w-full">
                    <canvas id="financeTrendChart"></canvas>
                </div>
            </div>

            <!-- Doughnut Chart: Expenses -->
            <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Despesas por Categoria</h3>
                <div class="relative h-64 w-full flex items-center justify-center">
                    @if(collect($expenseValues)->sum() > 0)
                        <canvas id="expenseDoughnutChart"></canvas>
                    @else
                        <div class="text-gray-400 text-center text-xs font-bold uppercase">Sem dados de despesas</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Transactions & Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Transactions -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-50 bg-gray-50/30 flex justify-between items-center">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Últimas Movimentações</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentTransactions as $transaction)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 rounded-full flex items-center justify-center text-lg {{ $transaction->type == 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                                <i
                                                    class="bi {{ $transaction->type == 'income' ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }}"></i>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-xs font-bold text-gray-900">{{ $transaction->description }}</p>
                                                <p class="text-[10px] text-gray-400 font-medium">
                                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span
                                            class="text-sm font-black {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $transaction->type == 'income' ? '+' : '-' }}
                                            {{ number_format($transaction->amount, 2, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-8 text-center text-gray-400 text-xs uppercase font-bold">
                                        Nenhuma movimentação recente</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detailed Breakdown (Income) -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-50 bg-gray-50/30">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Entradas por Tipo</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($totals as $item)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-4 text-xs font-bold text-gray-700">{{ $item['type'] }}</td>
                                    <td class="px-6 py-4 text-right text-xs font-black text-blue-600">
                                        {{ number_format($item['total'], 2, ',', '.') }} MT</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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