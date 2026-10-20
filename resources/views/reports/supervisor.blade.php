@extends('layouts.app')

@section('title', 'Relatório da Supervisão - Portal Life Church')
@section('page-title', 'Relatório da Supervisão')
@section('page-subtitle', $supervision ? $supervision->name : 'Selecione uma supervisão para começar')

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Relatório de Supervisão</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Relatório da Supervisão</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">{{ $supervision ? $supervision->name : 'Selecione uma supervisão para começar' }}</p>
            </div>
            @if($supervision)
                <div class="flex flex-wrap items-center gap-2">
                    <a href="#" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3.5 py-2 text-xs font-bold text-red-700 hover:bg-red-50 shadow-sm">
                        <i class="bi bi-file-earmark-pdf"></i> <span>PDF</span>
                    </a>
                    <a href="#" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-green-200 bg-white px-3.5 py-2 text-xs font-bold text-green-800 hover:bg-green-50 shadow-sm">
                        <i class="bi bi-file-earmark-spreadsheet"></i> <span>Excel</span>
                    </a>
                </div>
            @endif
        </header>

        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                @if(auth()->user()->role === 'admin')
                    <div class="lg:col-span-1">
                        <label for="supervision_id" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Selecionar Supervisão</label>
                        <select name="supervision_id" id="supervision_id" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800" onchange="this.form.submit()">
                            <option value="">-- Escolha uma supervisão --</option>
                            @foreach($allSupervisions as $s)
                                <option value="{{ $s->id }}" @if($supervision && $supervision->id == $s->id) selected @endif>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label for="start_date" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Data de Início</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800">
                </div>
                <div>
                    <label for="end_date" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Data de Fim</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-xs sm:text-sm py-2 px-3 font-semibold text-gray-800">
                </div>
                <div>
                    <button type="submit" class="w-full rounded-lg bg-gray-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-gray-800 transition shadow-sm">
                        <i class="bi bi-funnel mr-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        @if($supervision)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Total Arrecadado (Verificado)</p>
                        <i class="bi bi-cash-coin text-emerald-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">{{ number_format($total, 2, ',', '.') }} MT</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Registos no Período</p>
                        <i class="bi bi-list-check text-orange-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $contributions->count() }}</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Contribuições listadas</p>
                </article>
            </div>

            <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Membro</th>
                                <th class="px-4 py-3">Célula</th>
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($contributions as $contribution)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-xs sm:text-sm font-bold text-gray-900">{{ $contribution->user->name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $contribution->user->cell->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600 tabular-nums">{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-[10px] font-black uppercase rounded-full 
                                            @if($contribution->status == 'verificada') bg-green-100 text-green-800
                                            @elseif($contribution->status == 'pendente') bg-amber-100 text-amber-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($contribution->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs sm:text-sm font-black text-gray-950 text-right tabular-nums">{{ number_format($contribution->amount, 2, ',', '.') }} MT</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-xs text-gray-500">Nenhuma contribuição encontrada para este período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @else
            @if(auth()->user()->role === 'admin')
                <div class="text-center p-8 bg-white rounded-xl border border-gray-200 shadow-sm">
                    <i class="bi bi-info-circle text-2xl text-orange-700"></i>
                    <h3 class="mt-2 text-sm sm:text-base font-black text-gray-900">Nenhuma supervisão selecionada</h3>
                    <p class="mt-1 text-xs text-gray-500">Por favor, escolha uma supervisão no filtro acima para visualizar o relatório.</p>
                </div>
            @endif
        @endif
    </div>
@endsection
