@extends('layouts.app')

@section('title', 'Relatório Global - Portal Life Church')

@section('content')
    <div class="space-y-8">
        <!-- Header & Export Actions -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Relatório Global</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Consolidação de Contribuições e Estrutura</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.export.pdf', array_merge(['type' => 'global'], request()->query())) }}"
                    class="bg-red-50 text-red-600 px-6 py-4 rounded-2xl hover:bg-red-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest flex items-center border border-red-100 shadow-sm">
                    <i class="bi bi-file-pdf mr-2"></i> PDF
                </a>
                <a href="{{ route('reports.export.excel', array_merge(['type' => 'global'], request()->query())) }}"
                    class="bg-green-50 text-green-600 px-6 py-4 rounded-2xl hover:bg-green-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest flex items-center border border-green-100 shadow-sm">
                    <i class="bi bi-file-excel mr-2"></i> Excel
                </a>
                <a href="{{ route('reports.export.excel', ['type' => 'structure']) }}"
                    class="bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-blue-100">
                    <i class="bi bi-diagram-3 mr-2"></i> Estrutura
                </a>
            </div>
        </div>

        <!-- Filter Panel -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
            <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="bi bi-funnel text-blue-600"></i>
                Filtros de Pesquisa
            </h2>
            <form action="{{ route('reports.global') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Data Início</label>
                    <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Data Fim</label>
                    <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Zona</label>
                    <select name="zone_id" 
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none">
                        <option value="">Todas as Zonas</option>
                        @foreach($allZones as $zone)
                            <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status</label>
                    <select name="status" 
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none">
                        <option value="">Todos os Status</option>
                        <option value="verificada" {{ request('status') == 'verificada' ? 'selected' : '' }}>Verificada</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="rejeitada" {{ request('status') == 'rejeitada' ? 'selected' : '' }}>Rejeitada</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                        Filtrar
                    </button>
                    <a href="{{ route('reports.global') }}"
                        class="p-3 bg-gray-100 text-gray-400 rounded-2xl hover:bg-gray-200 transition-all">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-gradient-to-br from-green-500 to-green-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-green-100 relative overflow-hidden group">
                <i class="bi bi-cash-stack absolute right-8 top-1/2 -translate-y-1/2 text-[8rem] opacity-10 group-hover:scale-110 transition-transform duration-500"></i>
                <div class="relative z-10">
                    <p class="text-xs font-black uppercase tracking-[0.2em] opacity-80 mb-2">Total Arrecadado</p>
                    <h3 class="text-5xl font-black tracking-tighter">{{ number_format($total, 0, ',', '.') }}<span class="text-xl ml-2 uppercase opacity-60 font-black">MT</span></h3>
                    <div class="mt-6 flex items-center gap-2 text-xs font-bold bg-white/20 w-fit px-4 py-1.5 rounded-full">
                        <i class="bi bi-calendar-range"></i>
                        {{ $startDate->format('d/m/Y') }} — {{ $endDate->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden relative group">
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4">Distribuição Geográfica</h3>
                        <div class="space-y-4 max-h-[120px] overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($zoneStats as $stat)
                                <div class="flex items-center justify-between pb-2 border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors px-2 rounded-lg">
                                    <span class="text-xs font-bold text-gray-600">{{ $stat['name'] }}</span>
                                    <span class="text-xs font-black text-green-600">{{ number_format($stat['total'], 0, ',', '.') }} MT</span>
                                </div>
                            @empty
                                <p class="text-gray-400 text-xs italic">Nenhum dado por zona.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Contributions Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Extrato de Contribuições (Filtro)</h3>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $contributions->count() }} Registros Encontrados</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Doador / Origem</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Unidade Ministerial</th>
                            <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                            <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Valor Financeiro</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($contributions as $contribution)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-sm group-hover:bg-blue-600 group-hover:text-white transition-all">
                                            {{ substr($contribution->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-900 leading-tight">{{ $contribution->user->name }}</span>
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">DATA: {{ $contribution->contribution_date->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <p class="text-xs font-bold text-gray-700">Célula: {{ $contribution->user->cell->name ?? 'N/A' }}</p>
                                        <p class="text-[10px] text-gray-400 font-medium italic">Sede / Subsede</p>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border 
                                        @if($contribution->status == 'verificada') bg-green-50 text-green-600 border-green-100
                                        @elseif($contribution->status == 'pendente') bg-yellow-50 text-yellow-600 border-yellow-100
                                        @else bg-red-50 text-red-600 border-red-100 @endif">
                                        {{ $contribution->status }}
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <span class="text-lg font-black text-gray-900 tracking-tighter">{{ number_format($contribution->amount, 0, ',', '.') }}<span class="text-[10px] ml-1 uppercase opacity-50">MT</span></span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-10 py-20 text-center">
                                    <div class="flex flex-col items-center gap-4 text-gray-300">
                                        <i class="bi bi-journal-x text-7xl"></i>
                                        <p class="font-bold text-lg">Nenhuma contribuição encontrada para este período.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection