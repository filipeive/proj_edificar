@extends('layouts.app')

@section('title', 'Relatório de Supervisão - Portal Life Church')

@section('content')
    <div class="space-y-5">
        <!-- Header & Top Actions -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Supervisão Geral</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Painel de Supervisão</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">{{ $supervision->name ?? 'N/A' }} — Estrutura e Performance.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('reports.export.pdf', ['type' => 'supervision', 'id' => $supervision->id]) }}" 
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3.5 py-2 text-xs font-bold text-red-700 hover:bg-red-50 shadow-sm">
                    <i class="bi bi-file-earmark-pdf"></i> <span>PDF</span>
                </a>
                <a href="{{ route('reports.export.excel', ['type' => 'supervision', 'id' => $supervision->id]) }}" 
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-green-200 bg-white px-3.5 py-2 text-xs font-bold text-green-800 hover:bg-green-50 shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet"></i> <span>Excel</span>
                </a>
            </div>
        </header>

        <!-- Stats Overview Cards -->
        @php
            $totalVerified = $cellsData->sum('verified');
            $totalPending = $cellsData->sum('pending');
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Células Ativas</p>
                    <i class="bi bi-grid-3x3-gap text-blue-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $totalCells }}</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Capilaridade pastoral</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Meta Comprometida</p>
                    <i class="bi bi-bullseye text-indigo-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ number_format($totalCommitted, 0, ',', '.') }} MT</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Projeção ministerial</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Contribuições Verificadas</p>
                    <i class="bi bi-shield-check text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">{{ number_format($totalVerified, 0, ',', '.') }} MT</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Fluxo consolidado</p>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Aguardando Verificação</p>
                    <i class="bi bi-clock-history text-amber-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-amber-700 tracking-tight tabular-nums">{{ number_format($totalPending, 0, ',', '.') }} MT</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Pendências em análise</p>
            </article>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <!-- Detailed Performance Table -->
            <section class="xl:col-span-2 rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Performance Analítica por Célula</h3>
                        <p class="text-xs text-gray-500">Cumprimento de metas por célula</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Célula / Líder</th>
                                <th class="px-4 py-3 text-right">Meta (MT)</th>
                                <th class="px-4 py-3 text-right">Realiz. (MT)</th>
                                <th class="px-4 py-3 text-right">% Perf.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse ($cellsData as $cell)
                                @php
                                    $fulfillmentRate = $cell['committed'] > 0 ? ($cell['verified'] / $cell['committed']) * 100 : ($cell['verified'] > 0 ? 100 : 0);
                                    $rateColor = $fulfillmentRate >= 100 ? 'text-emerald-700' : ($fulfillmentRate >= 80 ? 'text-orange-700' : 'text-red-700');
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col">
                                            <span class="text-xs sm:text-sm font-bold text-gray-900">{{ $cell['name'] }}</span>
                                            <span class="inline-block mt-0.5 w-fit px-2 py-0.5 rounded border text-[9px] font-black uppercase {{ $cell['type_badge_classes'] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                                {{ $cell['type_label'] ?? 'Célula de Membros' }}
                                            </span>
                                            <span class="text-[11px] text-gray-500 mt-0.5">Liderança: {{ $cell['lider'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-600 tabular-nums">{{ number_format($cell['committed'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-black text-emerald-700 tabular-nums">{{ number_format($cell['verified'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-black {{ $rateColor }} text-sm tabular-nums">
                                        {{ round($fulfillmentRate, 1) }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-xs text-gray-500">
                                        Nenhuma célula associada a esta supervisão.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Monthly Aggregation -->
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-gray-200">
                    <h3 class="text-sm font-black text-gray-900">Histórico Mensal</h3>
                    <p class="text-xs text-gray-500">Resumo mensal verificado e pendente</p>
                </div>
                <div class="flex-1 overflow-y-auto max-h-[480px]">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-gray-100 text-sm">
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
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3.5">
                                        <span class="text-xs font-black text-gray-900 uppercase tracking-wider block mb-1.5">{{ $date->translatedFormat('F Y') }}</span>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <span class="text-[10px] font-bold text-gray-400 uppercase block">Verificado</span>
                                                <span class="text-xs sm:text-sm font-black text-emerald-700 tabular-nums">{{ number_format($verified, 0, ',', '.') }} MT</span>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-bold text-gray-400 uppercase block">Pendente</span>
                                                <span class="text-xs sm:text-sm font-black text-amber-700 tabular-nums">{{ number_format($pending, 0, ',', '.') }} MT</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
