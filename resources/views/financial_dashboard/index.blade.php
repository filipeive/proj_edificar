@extends('layouts.app')

@section('title', 'Painel Financeiro - Portal Life Church')

@section('content')
    <div class="space-y-8">
        <!-- Header & Top Actions -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Painel Financeiro</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Consolidado de Dízimos e Ofertas
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()"
                    class="bg-gray-100 text-gray-600 px-6 py-4 rounded-2xl hover:bg-gray-200 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-sm">
                    <i class="bi bi-printer text-lg mr-2"></i> Imprimir
                </button>
            </div>
        </div>

        <!-- Filter Panel -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="bi bi-funnel text-blue-600"></i>
                Filtros de Período
            </h2>
            <form action="{{ route('financial.dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Mês de
                        Referência</label>
                    <select name="month"
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ano Civil</label>
                    <select name="year"
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                        Visualizar Dados
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Overview Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Entradas -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 relative group overflow-hidden">
                <i class="bi bi-cash-stack absolute right-6 top-1/2 -translate-y-1/2 text-5xl text-gray-50 opacity-50"></i>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Entradas</p>
                    <h3 class="text-3xl font-black text-green-600 tracking-tighter">
                        {{ number_format($grandTotal, 0, ',', '.') }}<span class="text-sm ml-1 opacity-60">MT</span>
                    </h3>
                </div>
            </div>

            <!-- Saídas -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 relative group overflow-hidden">
                <i class="bi bi-dash-circle absolute right-6 top-1/2 -translate-y-1/2 text-5xl text-red-50 opacity-50"></i>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Saídas</p>
                    <h3 class="text-3xl font-black text-red-600 tracking-tighter">
                        {{ number_format($totalExpenses, 0, ',', '.') }}<span class="text-sm ml-1 opacity-60">MT</span>
                    </h3>
                </div>
            </div>

            <!-- Saldo -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 relative group overflow-hidden">
                <i class="bi bi-wallet2 absolute right-6 top-1/2 -translate-y-1/2 text-5xl text-blue-50 opacity-50"></i>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Saldo Atual</p>
                    <h3 class="text-3xl font-black {{ $balance >= 0 ? 'text-blue-600' : 'text-red-500' }} tracking-tighter">
                        {{ number_format($balance, 0, ',', '.') }}<span class="text-sm ml-1 opacity-60">MT</span>
                    </h3>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div
                class="bg-gray-900 p-6 rounded-[2rem] text-white shadow-xl relative overflow-hidden flex flex-col justify-center gap-3">
                <h4 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Ações Rápidas</h4>
                <a href="{{ route('requisitions.create') }}"
                    class="flex items-center text-sm font-bold hover:text-blue-300 transition">
                    <i class="bi bi-plus-circle mr-2"></i> Nova Requisição
                </a>
                <a href="{{ route('expenses.index') }}"
                    class="flex items-center text-sm font-bold hover:text-red-300 transition">
                    <i class="bi bi-list-check mr-2"></i> Gerir Despesas
                </a>
                <a href="{{ route('requisitions.index') }}"
                    class="flex items-center text-sm font-bold hover:text-yellow-300 transition">
                    <i class="bi bi-clock-history mr-2"></i> Aprovar Requisições
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Membros Link -->
            <div
                class="bg-gradient-to-br from-green-500 to-green-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-green-100 relative group overflow-hidden">
                <i
                    class="bi bi-person-check absolute right-8 top-1/2 -translate-y-1/2 text-7xl text-white opacity-10 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-green-100 uppercase tracking-widest mb-1">Membros
                        (Dízimos/Ofertas)</p>
                    @php $memberTotal = collect($totals)->sum('contributions'); @endphp
                    <h3 class="text-3xl font-black tracking-tighter">{{ number_format($memberTotal, 0, ',', '.') }} MT</h3>
                </div>
            </div>

            <!-- Cultos Link -->
            <div
                class="bg-gradient-to-br from-blue-500 to-blue-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-blue-100 relative group overflow-hidden">
                <i
                    class="bi bi-church absolute right-8 top-1/2 -translate-y-1/2 text-7xl text-white opacity-10 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-blue-100 uppercase tracking-widest mb-1">Cultos / Celebrações</p>
                    @php $serviceTotal = collect($totals)->sum('services'); @endphp
                    <h3 class="text-3xl font-black tracking-tighter">{{ number_format($serviceTotal, 0, ',', '.') }} MT</h3>
                </div>
            </div>
        </div>

        <!-- Detailed Audit Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Detalhamento Analítico de Entradas
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Segmentação</th>
                            <th
                                class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Membros</th>
                            <th
                                class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Cultos</th>
                            <th
                                class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($totals as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-10 py-6">
                                    <span
                                        class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $item['type'] }}</span>
                                </td>
                                <td class="px-10 py-6 text-right font-bold text-gray-600">
                                    {{ number_format($item['contributions'], 0, ',', '.') }} MT
                                </td>
                                <td class="px-10 py-6 text-right font-bold text-gray-600">
                                    {{ number_format($item['services'], 0, ',', '.') }} MT
                                </td>
                                <td class="px-10 py-6 text-right font-black text-blue-600 text-lg tracking-tighter">
                                    {{ number_format($item['total'], 0, ',', '.') }} MT
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection