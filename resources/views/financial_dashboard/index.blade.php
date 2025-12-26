@extends('layouts.app')

@section('title', 'Painel Financeiro')
@section('page-title', 'Painel Financeiro')
@section('page-subtitle', 'Consolidado de dízimos, ofertas e contribuições')

@section('content')
    <div class="container-fluid">
        <!-- Filtros -->
        <div class="mb-8 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <form action="{{ route('financial.dashboard') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mês</label>
                    <select name="month" class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ano</label>
                    <select name="year" class="rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold transition">
                    <i class="bi bi-filter mr-2"></i> Filtrar
                </button>
            </form>
        </div>

        <!-- Cards de Resumo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200 relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">Total Geral</p>
                    <h3 class="text-4xl font-black text-gray-800">{{ number_format($grandTotal, 2, ',', '.') }} <span
                            class="text-lg">MT</span></h3>
                </div>
                <i class="bi bi-cash-stack absolute right-6 bottom-4 text-7xl text-gray-100"></i>
            </div>

            <div class="bg-green-600 p-8 rounded-2xl shadow-lg text-white relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-sm font-bold text-green-200 uppercase tracking-widest mb-1">Dízimos e Ofertas (Membros)
                    </p>
                    @php $memberTotal = collect($totals)->sum('contributions'); @endphp
                    <h3 class="text-4xl font-black">{{ number_format($memberTotal, 2, ',', '.') }} <span
                            class="text-lg">MT</span></h3>
                </div>
                <i class="bi bi-person-check absolute right-6 bottom-4 text-7xl text-white opacity-10"></i>
            </div>

            <div class="bg-blue-600 p-8 rounded-2xl shadow-lg text-white relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-sm font-bold text-blue-200 uppercase tracking-widest mb-1">Ofertas de Cultos</p>
                    @php $serviceTotal = collect($totals)->sum('services'); @endphp
                    <h3 class="text-4xl font-black">{{ number_format($serviceTotal, 2, ',', '.') }} <span
                            class="text-lg">MT</span></h3>
                </div>
                <i class="bi bi-church absolute right-6 bottom-4 text-7xl text-white opacity-10"></i>
            </div>
        </div>

        <!-- Tabela Detalhada -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                <h4 class="text-xl font-bold text-gray-800">Detalhamento por Tipo</h4>
                <button onclick="window.print()" class="text-gray-500 hover:text-gray-800 transition">
                    <i class="bi bi-printer text-xl"></i>
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-8 py-4 text-sm font-bold text-gray-600 uppercase tracking-wider">Tipo de
                                Contribuição</th>
                            <th class="px-8 py-4 text-sm font-bold text-gray-600 uppercase tracking-wider text-right">
                                Membros (Dízimos/Ofertas)</th>
                            <th class="px-8 py-4 text-sm font-bold text-gray-600 uppercase tracking-wider text-right">Cultos
                                (Ofertas)</th>
                            <th class="px-8 py-4 text-sm font-bold text-gray-600 uppercase tracking-wider text-right">Total
                                Consolidado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($totals as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-8 py-5 text-gray-800 font-bold">{{ $item['type'] }}</td>
                                <td class="px-8 py-5 text-right text-gray-600">
                                    {{ number_format($item['contributions'], 2, ',', '.') }} MT</td>
                                <td class="px-8 py-5 text-right text-gray-600">
                                    {{ number_format($item['services'], 2, ',', '.') }} MT</td>
                                <td class="px-8 py-5 text-right font-black text-blue-600 text-lg">
                                    {{ number_format($item['total'], 2, ',', '.') }} MT</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-900 text-white">
                            <td class="px-8 py-6 font-black uppercase">TOTAL GERAL</td>
                            <td class="px-8 py-6 text-right font-bold">{{ number_format($memberTotal, 2, ',', '.') }} MT
                            </td>
                            <td class="px-8 py-6 text-right font-bold">{{ number_format($serviceTotal, 2, ',', '.') }} MT
                            </td>
                            <td class="px-8 py-6 text-right font-black text-2xl text-green-400">
                                {{ number_format($grandTotal, 2, ',', '.') }} MT</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection