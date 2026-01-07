@extends('layouts.app')

@section('title', $pageTitle . ' - Portal Life Church') 

@section('content')
    <div class="space-y-8">
        <!-- Actions & Filter Header -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="space-y-1 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">
                    <i class="bi bi-piggy-bank"></i>
                    <span>Tesouraria</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $pageTitle }}</h1>
                <p class="text-gray-500 font-medium">Histórico consolidado de contribuições e ofertas</p>
            </div>

            <div class="flex gap-4">
                @if (in_array(auth()->user()->role, ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin']))
                    <a href="{{ route('contributions.create') }}"
                        class="px-8 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 hover:shadow-blue-200 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg shadow-blue-100">
                        <i class="bi bi-plus-lg text-lg"></i>
                        Nova Oferta
                    </a>
                @endif
            </div>
        </div>

        <!-- Contributions Table Container -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Listagem de Movimentações</h3>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Dados em tempo real</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Informação Temporal</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Valor Financeiro</th>
                            @if (isset($showUserColumn) && $showUserColumn)
                                <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Identificação do Membro</th>
                            @endif
                            <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado de Validação</th>
                            <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Documento</th>
                            <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Menu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($contributions as $contribution)
                            <tr class="hover:bg-gray-50/70 transition-colors group">
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 leading-tight group-hover:text-blue-600 transition-colors">{{ $contribution->contribution_date->format('d/m/Y') }}</span>
                                        <span class="text-[10px] text-gray-400 font-mono uppercase tracking-tighter">REF: #{{ str_pad($contribution->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <span class="text-lg font-black text-green-600 tracking-tighter">{{ number_format($contribution->amount, 0, ',', '.') }}<span class="text-xs ml-1 uppercase">MT</span></span>
                                </td>
                                
                                @if (isset($showUserColumn) && $showUserColumn)
                                    <td class="px-10 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-900 uppercase leading-tight">{{ $contribution->user->name }}</span>
                                            <span class="text-[10px] text-gray-400 font-medium">Célula: {{ $contribution->cell->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                @endif

                                <td class="px-10 py-6 text-center">
                                    <div class="flex justify-center">
                                        @if ($contribution->status === 'verificada')
                                            <span class="px-4 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100 flex items-center gap-2">
                                                <i class="bi bi-patch-check-fill"></i> Validado
                                            </span>
                                        @elseif($contribution->status === 'pendente')
                                            <span class="px-4 py-1 bg-yellow-50 text-yellow-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-yellow-100 flex items-center gap-2 animate-pulse">
                                                <i class="bi bi-lightning-charge-fill"></i> Em Análise
                                            </span>
                                        @else
                                            <span class="px-4 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-100 flex items-center gap-2">
                                                <i class="bi bi-x-square-fill"></i> Rejeitado
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-10 py-6 text-center">
                                    @if ($contribution->proof_path)
                                        <a href="{{ Storage::url($contribution->proof_path) }}" target="_blank"
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all mx-auto shadow-sm">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </a>
                                    @else
                                        <span class="text-[10px] text-gray-300 font-black uppercase tracking-widest">Nenhum</span>
                                    @endif
                                </td>

                                <td class="px-10 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2 text-sm">
                                        <a href="{{ route('contributions.show', $contribution) }}"
                                            class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @if ($contribution->status === 'pendente' && auth()->id() === $contribution->user_id)
                                            <a href="{{ route('contributions.edit', $contribution) }}"
                                                class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-orange-500 hover:text-white flex items-center justify-center transition-all">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ (isset($showUserColumn) && $showUserColumn) ? 6 : 5 }}" class="px-10 py-24 text-center">
                                    <div class="flex flex-col items-center gap-4 text-gray-200">
                                        <i class="bi bi-journal-x text-8xl"></i>
                                        <div class="space-y-1">
                                            <p class="font-black text-xl text-gray-400">Sem histórico de ofertas</p>
                                            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Nenhum registro encontrado no sistema</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination -->
            @if($contributions->hasPages())
                <div class="p-8 bg-gray-50/50 border-t border-gray-50">
                    {{ $contributions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection