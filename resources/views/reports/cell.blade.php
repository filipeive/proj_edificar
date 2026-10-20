@extends('layouts.app')

@section('title', 'Relatório da Célula - Portal Life Church')

@section('content')
    <div class="space-y-5">
        <!-- Header & Top Actions -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Relatório Analítico</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Relatório de Célula</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">
                    {{ $cell ? $cell->name . ' (' . $cell->type_label . ')' : 'Selecione uma célula para começar' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($cell)
                    <a href="{{ route('reports.export.pdf', ['type' => 'cell', 'id' => $cell->id, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                        class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3.5 py-2 text-xs font-bold text-red-700 hover:bg-red-50 shadow-sm">
                        <i class="bi bi-file-earmark-pdf"></i> <span>PDF</span>
                    </a>
                    <a href="{{ route('reports.export.excel', ['type' => 'cell', 'id' => $cell->id, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                        class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-green-200 bg-white px-3.5 py-2 text-xs font-bold text-green-800 hover:bg-green-50 shadow-sm">
                        <i class="bi bi-file-earmark-spreadsheet"></i> <span>Excel</span>
                    </a>
                @endif
            </div>
        </header>

        <!-- Filter Panel -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                @if($allCells->isNotEmpty())
                    <div>
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Selecionar Célula</label>
                        <select name="cell_id"
                            class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800"
                            onchange="this.form.submit()">
                            <option value="">-- Escolha uma célula --</option>
                            @foreach($allCells as $c)
                                <option value="{{ $c->id }}" @if($cell && $cell->id == $c->id) selected @endif>{{ $c->name }} ({{ $c->type_label }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Data Início</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800">
                </div>
                <div>
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Data Fim</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800">
                </div>
                <button type="submit"
                    class="rounded-lg bg-gray-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-gray-800 transition shadow-sm">
                    <i class="bi bi-funnel mr-1"></i> Gerar Relatório
                </button>
            </form>
        </div>

        @if($cell)
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Total Arrecadado (Verificado)</p>
                        <i class="bi bi-wallet2 text-emerald-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">
                        {{ number_format($total, 0, ',', '.') }} MT
                    </p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">
                        Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                    </p>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Doadores Distintos</p>
                        <i class="bi bi-people text-blue-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">
                        {{ $contributions->unique('user_id')->count() }} <span class="text-xs font-semibold text-gray-400">Membros</span>
                    </p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Alcance ministerial</p>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Status de Verificação</p>
                        <i class="bi bi-check2-circle text-orange-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">
                        {{ round(($contributions->where('status', 'verificada')->count() / max($contributions->count(), 1)) * 100) }}%
                    </p>
                    <p class="text-[10px] sm:text-[11px] text-amber-700 font-semibold mt-0.5 truncate">
                        {{ $contributions->where('status', 'pendente')->count() }} Pendentes
                    </p>
                </article>
            </div>

            <!-- Detailed Contributions Table -->
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Extrato de Movimentações</h3>
                        <p class="text-xs text-gray-500">Detalhamento por membro no período</p>
                    </div>
                    <span class="text-xs font-bold text-gray-500">{{ $contributions->count() }} Lançamentos</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Doador</th>
                                <th class="px-4 py-3 text-center">Data</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right">Valor (MT)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($contributions as $contribution)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center font-black text-xs shrink-0">
                                                {{ substr($contribution->user->name, 0, 1) }}
                                            </div>
                                            <span class="text-xs sm:text-sm font-bold text-gray-900">{{ $contribution->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs font-semibold text-gray-600 tabular-nums">
                                        {{ $contribution->contribution_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase
                                            @if($contribution->status == 'verificada') bg-green-100 text-green-800
                                            @elseif($contribution->status == 'pendente') bg-amber-100 text-amber-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $contribution->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-gray-950 text-sm tabular-nums">
                                        {{ number_format($contribution->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-xs text-gray-500">
                                        Nenhuma contribuição encontrada para este período.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @else
            @if(auth()->user()->role === 'admin')
                <div class="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm">
                    <div class="w-12 h-12 bg-orange-50 text-orange-700 rounded-xl flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3 class="text-sm sm:text-base font-black text-gray-900 mb-1">Aguardando Seleção</h3>
                    <p class="text-xs text-gray-500 max-w-md mx-auto">
                        Escolha uma célula no menu de filtros acima para gerar o relatório consolidado de contribuições.
                    </p>
                </div>
            @endif
        @endif
    </div>
@endsection
