@extends('layouts.app')

@section('title', 'Relatório Nacional')
@section('page-title', 'Relatório Nacional')
@section('page-subtitle', 'Comparação de atividade e contribuições por congregação')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div>
                <a href="{{ route('national.dashboard') }}" class="text-xs font-bold text-orange-800 hover:underline"><i class="bi bi-arrow-left mr-1"></i>Painel nacional</a>
                <h1 class="mt-2 text-2xl font-black text-gray-950">Relatório por congregação</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('national.reports.export.pdf', request()->query()) }}" class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2 text-xs font-bold text-red-700 hover:bg-red-50"><i class="bi bi-file-pdf"></i>PDF</a>
                <a href="{{ route('national.reports.export', request()->query()) }}" class="inline-flex items-center gap-2 rounded-lg border border-green-200 bg-white px-4 py-2 text-xs font-bold text-green-800 hover:bg-green-50"><i class="bi bi-file-excel"></i>Excel</a>
            </div>
        </div>

        <form method="GET" action="{{ route('national.reports.index') }}" class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div><label for="start_date" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Data inicial</label><input id="start_date" name="start_date" type="date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="rounded-lg border-gray-300 text-sm"></div>
            <div><label for="end_date" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Data final</label><input id="end_date" name="end_date" type="date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="rounded-lg border-gray-300 text-sm"></div>
            <button class="rounded-lg bg-gray-900 px-4 py-2.5 text-xs font-bold text-white">Aplicar período</button>
            <a href="{{ route('national.reports.index') }}" class="px-3 py-2.5 text-xs font-bold text-gray-500 hover:text-gray-900">Limpar</a>
        </form>

        <section class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Congregação</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Membros</th>
                        <th class="px-4 py-3 text-right">Zonas</th>
                        <th class="px-4 py-3 text-right">Células</th>
                        <th class="px-4 py-3 text-right">Cultos</th>
                        <th class="px-4 py-3 text-right">Contribuições (MT)</th>
                        <th class="px-4 py-3 text-right">Cultos (MT)</th>
                        <th class="px-4 py-3 text-right">Total Entradas (MT)</th>
                        <th class="px-4 py-3 text-right">Despesas (MT)</th>
                        <th class="px-4 py-3 text-right">Saldo (MT)</th>
                        <th class="px-4 py-3 text-right">Pendentes</th>
                        <th class="px-4 py-3 text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 font-bold text-gray-900">
                                <a href="{{ route('national.congregations.show', $row['congregation_id']) }}" class="hover:text-orange-800 hover:underline">
                                    {{ $row['congregation'] }}
                                </a>
                                @if(!empty($row['has_edificar']))
                                    <span class="ml-1 text-[9px] font-black uppercase text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">Edificar</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-black {{ $row['status'] === 'Ativa' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['active_members']) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['zones']) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['cells']) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['services']) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['verified_contributions'], 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ number_format($row['service_income'], 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-bold tabular-nums text-emerald-700">{{ number_format($row['total_income'], 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-rose-600">{{ number_format($row['expenses'] ?? 0, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-black tabular-nums {{ ($row['net_balance'] ?? 0) >= 0 ? 'text-gray-900' : 'text-rose-600' }}">
                                {{ number_format($row['net_balance'] ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-amber-600 font-bold">{{ number_format($row['pending_contributions']) }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('national.congregations.show', $row['congregation_id']) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:border-orange-300 hover:text-orange-800 hover:bg-orange-50/50 shadow-sm transition">
                                    <i class="bi bi-eye text-orange-700"></i> Detalhes
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="13" class="px-4 py-12 text-center text-gray-500">Sem dados neste período.</td></tr>
                    @endforelse
                </tbody>
                @if($rows->isNotEmpty())
                    <tfoot class="border-t-2 border-gray-200 bg-gray-50 font-black">
                        <tr>
                            <td colspan="2" class="px-4 py-3">Total nacional</td>
                            <td class="px-4 py-3 text-right">{{ number_format($rows->sum('active_members')) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($rows->sum('zones')) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($rows->sum('cells')) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($rows->sum('services')) }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($rows->sum('verified_contributions'), 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($rows->sum('service_income'), 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-emerald-700">{{ number_format($rows->sum('total_income'), 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-600">{{ number_format($rows->sum('expenses'), 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($rows->sum('net_balance'), 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-amber-600">{{ number_format($rows->sum('pending_contributions')) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </section>
    </div>
@endsection
