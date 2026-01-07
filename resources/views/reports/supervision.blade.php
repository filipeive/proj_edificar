@extends('layouts.app')

@section('title', 'Relatório de Supervisão - Portal Life Church')

@section('content')
    <div class="space-y-8">
        <!-- Header & Top Actions -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-bold text-purple-600 uppercase tracking-widest mb-1">
                    <i class="bi bi-diagram-3"></i>
                    <span>Supervisão Geral</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Painel de Supervisão</h1>
                <p class="text-gray-500 font-medium">{{ $supervision->name ?? 'N/A' }} — Estrutura e Performance</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.export.pdf', ['type' => 'supervision', 'id' => $supervision->id]) }}" 
                    class="bg-red-50 text-red-600 px-6 py-4 rounded-2xl hover:bg-red-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest flex items-center border border-red-100 shadow-sm">
                    <i class="bi bi-file-pdf mr-2"></i> PDF
                </a>
                <a href="{{ route('reports.export.excel', ['type' => 'supervision', 'id' => $supervision->id]) }}" 
                    class="bg-green-50 text-green-600 px-6 py-4 rounded-2xl hover:bg-green-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest flex items-center border border-green-100 shadow-sm">
                    <i class="bi bi-file-excel mr-2"></i> Excel
                </a>
            </div>
        </div>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative group overflow-hidden">
                <i class="bi bi-grid-3x3-gap absolute right-8 top-1/2 -translate-y-1/2 text-7xl text-gray-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Células Ativas</p>
                    <h3 class="text-4xl font-black text-gray-900 tracking-tighter">{{ $totalCells }}</h3>
                    <p class="mt-4 text-[10px] font-black text-blue-600 uppercase tracking-tighter">Capilaridade Pastoral</p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative group overflow-hidden">
                <i class="bi bi-bullseye absolute right-8 top-1/2 -translate-y-1/2 text-7xl text-gray-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Meta Comprometida</p>
                    <h3 class="text-4xl font-black text-gray-900 tracking-tighter">{{ number_format($totalCommitted, 0, ',', '.') }}<span class="text-lg ml-1 uppercase opacity-50 font-bold">MT</span></h3>
                    <p class="mt-4 text-[10px] font-black text-purple-600 uppercase tracking-tighter">Projeção Ministerial</p>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-green-100 relative group overflow-hidden">
                <i class="bi bi-shield-check absolute right-8 top-1/2 -translate-y-1/2 text-7xl text-white opacity-10 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-green-100 uppercase tracking-widest mb-1">Contribuições Verificadas</p>
                    @php $totalVerified = $cellsData->sum('verified'); @endphp
                    <h3 class="text-4xl font-black tracking-tighter">{{ number_format($totalVerified, 0, ',', '.') }}<span class="text-lg ml-1 uppercase opacity-60">MT</span></h3>
                    <p class="mt-4 text-[10px] font-black text-green-200 uppercase tracking-tighter">Fluxo Consolidado</p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative group overflow-hidden">
                <i class="bi bi-clock-history absolute right-8 top-1/2 -translate-y-1/2 text-7xl text-gray-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Aguardando Verificação</p>
                    @php $totalPending = $cellsData->sum('pending'); @endphp
                    <h3 class="text-4xl font-black text-gray-900 tracking-tighter">{{ number_format($totalPending, 0, ',', '.') }}<span class="text-lg ml-1 uppercase opacity-50 font-bold">MT</span></h3>
                    <p class="mt-4 text-[10px] font-black text-yellow-600 uppercase tracking-tighter">Pendências em Análise</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Detailed Performance Table -->
            <div class="xl:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Performance Analítica por Célula</h3>
                    <i class="bi bi-table text-gray-400"></i>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/50 text-left">
                                <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Célula / Líder</th>
                                <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Meta (MT)</th>
                                <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Realiz. (MT)</th>
                                <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">% Perf.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($cellsData as $cell)
                                @php
                                    $fulfillmentRate = $cell['committed'] > 0 ? ($cell['verified'] / $cell['committed']) * 100 : ($cell['verified'] > 0 ? 100 : 0);
                                    $rateColor = $fulfillmentRate >= 100 ? 'text-green-600' : ($fulfillmentRate >= 80 ? 'text-orange-600' : 'text-red-600');
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-10 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $cell['name'] }}</span>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Liderança: {{ $cell['lider'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-10 py-6 text-right font-bold text-gray-600">{{ number_format($cell['committed'], 0, ',', '.') }}</td>
                                    <td class="px-10 py-6 text-right font-black text-green-700">{{ number_format($cell['verified'], 0, ',', '.') }}</td>
                                    <td class="px-10 py-6 text-right font-black {{ $rateColor }} text-lg tracking-tighter">
                                        {{ round($fulfillmentRate, 1) }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-10 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4 text-gray-300">
                                            <i class="bi bi-journal-x text-7xl"></i>
                                            <p class="font-bold text-lg">Nenhuma célula associada a esta supervisão.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Monthly Aggregation -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Histórico Mensal</h3>
                </div>
                <div class="flex-1 overflow-y-auto max-h-[600px] custom-scrollbar">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-50">
                            @php
                                $monthlyData = $contributions->groupBy(function ($item) {
                                    return $item->year . '-' . $item->month;
                                });
                            @endphp
                            
                            @foreach($monthlyData as $key => $data)
                                @php
                                    $date = \Carbon\Carbon::createFromDate($data->first()->year, $data->first()->month, 1);
                                    $verified = $data->where('status', 'verificada')->sum('total_amount');
                                    $pending = $data->where('status', 'pendente')->sum('total_amount');
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest mb-1">{{ $date->translatedFormat('F Y') }}</span>
                                            <div class="flex items-center gap-4 mt-2">
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Verificado</span>
                                                    <span class="text-sm font-black text-green-600">{{ number_format($verified, 0, ',', '.') }} MT</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Pendente</span>
                                                    <span class="text-sm font-black text-yellow-600">{{ number_format($pending, 0, ',', '.') }} MT</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection