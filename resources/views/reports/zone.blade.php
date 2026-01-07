@extends('layouts.app')

@section('title', 'Relatório da Zona - Portal Life Church')

    @section('content')
        <div class="space-y-8">
            <!-- Header & Top Actions -->
            <div
                class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-xs font-bold text-orange-600 uppercase tracking-widest mb-1">
                        <i class="bi bi-geo-alt"></i>
                        <span>Relatório Geográfico</span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Painel de Zona</h1>
                    <p class="text-gray-500 font-medium">{{ $zone ? $zone->name : 'Selecione uma zona para começar' }} —
                        Consolidado Ministerial</p>
                </div>

                <div class="flex items-center gap-3">
                    @if($zone)
                        <a href="{{ route('reports.export.pdf', ['type' => 'zone', 'id' => $zone->id, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                            class="bg-red-50 text-red-600 px-6 py-4 rounded-2xl hover:bg-red-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest flex items-center border border-red-100 shadow-sm"
                            target="_blank">
                            <i class="bi bi-file-pdf mr-2"></i> PDF
                        </a>
                        <a href="{{ route('reports.export.excel', ['type' => 'zone', 'id' => $zone->id, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                            class="bg-green-50 text-green-600 px-6 py-4 rounded-2xl hover:bg-green-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest flex items-center border border-green-100 shadow-sm">
                            <i class="bi bi-file-excel mr-2"></i> Excel
                        </a>
                    @endif
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i class="bi bi-funnel text-blue-600"></i>
                    Filtros Estruturais
                </h2>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
                    @if(auth()->user()->role === 'admin')
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Selecionar
                                Zona</label>
                            <select name="zone_id"
                                class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 appearance-none"
                                onchange="this.form.submit()">
                                <option value="">-- Escolha uma zona --</option>
                                @foreach($allZones as $z)
                                    <option value="{{ $z->id }}" @if($zone && $zone->id == $z->id) selected @endif>{{ $z->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Data Início</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                            class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Data Fim</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                            class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                    </div>
                    <button type="submit"
                        class="py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 h-[52px]">
                        Gerar Relatório
                    </button>
                </form>
            </div>

            @if($zone)
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        class="bg-gradient-to-br from-green-500 to-green-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-green-100 relative group overflow-hidden">
                        <i
                            class="bi bi-wallet2 absolute right-8 top-1/2 -translate-y-1/2 text-7xl text-white opacity-10 group-hover:scale-110 transition-transform duration-500"></i>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-green-100 uppercase tracking-widest mb-1">Total Consolidado
                                (Verificado)</p>
                            <h3 class="text-4xl font-black tracking-tighter">{{ number_format($total, 0, ',', '.') }}<span
                                    class="text-lg ml-1 uppercase opacity-60">MT</span></h3>
                            <p class="mt-4 text-[10px] font-black text-green-200 uppercase tracking-tighter">Período:
                                {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative group overflow-hidden">
                        <i
                            class="bi bi-diagram-3 absolute right-8 top-1/2 -translate-y-1/2 text-7xl text-gray-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Alcance da Zona</p>
                            <h3 class="text-4xl font-black text-gray-900 tracking-tighter">
                                {{ $contributions->unique('cell_id')->count() }}<span
                                    class="text-lg ml-1 uppercase opacity-50 font-bold">Células</span></h3>
                            <p class="mt-4 text-[10px] font-black text-orange-600 uppercase tracking-tighter">Movimentação
                                Territorial</p>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 relative group overflow-hidden">
                        <i
                            class="bi bi-people absolute right-8 top-1/2 -translate-y-1/2 text-7xl text-gray-50 opacity-50 group-hover:scale-110 transition-transform duration-500"></i>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Participação Financeira
                            </p>
                            <h3 class="text-4xl font-black text-gray-900 tracking-tighter">
                                {{ $contributions->unique('user_id')->count() }}<span
                                    class="text-lg ml-1 uppercase opacity-50 font-bold">Membros</span></h3>
                            <p class="mt-4 text-[10px] font-black text-blue-600 uppercase tracking-tighter">Engajamento Ministerial
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Detailed Contributions Table -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Extrato Consolidado da Zona</h3>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $contributions->count() }}
                            Lançamentos</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50 text-left">
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Doador /
                                        Estrutura</th>
                                    <th
                                        class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Data</th>
                                    <th
                                        class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Status</th>
                                    <th
                                        class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                        Valor (MT)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($contributions as $contribution)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-10 py-6">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center font-black text-sm group-hover:bg-orange-600 group-hover:text-white transition-all shadow-sm">
                                                    {{ substr($contribution->user->name, 0, 1) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-black text-gray-900 tracking-tight">{{ $contribution->user->name }}</span>
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                        {{ $contribution->user->cell?->supervision?->name ?? 'N/A' }} —
                                                        {{ $contribution->user->cell?->name ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6 text-center text-sm font-bold text-gray-500">
                                            {{ $contribution->contribution_date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-10 py-6">
                                            <div class="flex justify-center">
                                                <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border 
                                                                @if($contribution->status == 'verificada') bg-green-50 text-green-600 border-green-100
                                                                @elseif($contribution->status == 'pendente') bg-yellow-50 text-yellow-600 border-yellow-100
                                                                @else bg-red-50 text-red-600 border-red-100 @endif">
                                                    {{ $contribution->status }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6 text-right font-black text-gray-900 text-lg tracking-tighter">
                                            {{ number_format($contribution->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-10 py-20 text-center">
                                            <div class="flex flex-col items-center gap-4 text-gray-300">
                                                <i class="bi bi-geo-fill text-7xl"></i>
                                                <p class="font-bold text-lg">Nenhuma movimentação financeira encontrada para esta zona.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                @if(auth()->user()->role === 'admin')
                    <div class="bg-white rounded-[2.5rem] p-20 text-center border border-gray-100 shadow-sm">
                        <div
                            class="w-24 h-24 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 mb-2">Aguardando Seleção de Zona</h3>
                        <p class="text-gray-500 font-medium max-w-md mx-auto">Por favor, selecione uma zona no filtro acima para
                            carregar o histórico consolidado de ofertas.</p>
                    </div>
                @endif
            @endif
        </div>
    @endsection
@endsection