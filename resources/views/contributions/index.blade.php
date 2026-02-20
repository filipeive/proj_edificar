@extends('layouts.app')

@section('title', $pageTitle . ' - Portal Life Church')
@section('page-title', $pageTitle)
@section('page-subtitle', 'Gerencie as contribuições e dízimos da igreja')


@section('header-actions')
    @if (in_array(auth()->user()->role, ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin']))
        <div class="md:hidden">
            <a href="{{ route('contributions.create') }}"
                class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
                <i class="bi bi-plus-circle-fill text-2xl"></i>
            </a>
        </div>
    @endif
@endsection

@section('content')
    <div class="space-y-8" 
        x-data="{ 
            view: window.innerWidth < 768 ? 'grid' : 'list',
            updateView() {
                if (window.innerWidth < 768 && this.view === 'list') {
                    this.view = 'grid';
                }
            }
        }"
        x-init="$watch('view', value => localStorage.setItem('contributions_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('contributions_view') || 'list')"
        @resize.window.debounce.500ms="updateView()">
        <!-- Actions & Filter Header -->
        <div class="bg-white p-4 md:p-8 rounded-2xl md:rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="space-y-1 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">
                    <i class="bi bi-piggy-bank"></i>
                    <span>Tesouraria</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $pageTitle }}</h1>
                <p class="text-gray-500 font-medium">Histórico consolidado de contribuições e ofertas</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden md:flex bg-gray-100 p-1 rounded-xl items-center">
                    <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                        <i class="bi bi-list-ul mr-2"></i> Lista
                    </button>
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-400 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                        <i class="bi bi-grid-fill mr-2"></i> Grid
                    </button>
                </div>
                @if (in_array(auth()->user()->role, ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin']))
                    <a href="{{ route('contributions.create') }}"
                        class="hidden md:flex px-8 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 hover:shadow-blue-200 transition-all font-black text-xs uppercase tracking-widest items-center justify-center gap-2 shadow-lg shadow-blue-100">
                        <i class="bi bi-plus-lg text-lg"></i>
                        Nova Oferta
                    </a>
                @endif
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100">
            <form action="{{ route('contributions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @if(request()->filled('mine'))
                    <input type="hidden" name="mine" value="1">
                @endif
                @if(request()->filled('scope'))
                    <input type="hidden" name="scope" value="{{ request('scope') }}">
                @endif
                
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-2">Pesquisar Membro</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome do membro..." 
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-2">Estado</label>
                    <select name="status" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500 transition-all custom-select">
                        <option value="">Todos Estados</option>
                        <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="verificada" {{ request('status') === 'verificada' ? 'selected' : '' }}>Verificada</option>
                        <option value="rejeitada" {{ request('status') === 'rejeitada' ? 'selected' : '' }}>Rejeitada</option>
                        <option value="cancelada" {{ request('status') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 px-2">Pacote</label>
                    <select name="package_id" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500 transition-all custom-select">
                        <option value="">Todos Pacotes</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Filtrar</button>
                    <a href="{{ route('contributions.index', request()->only('mine', 'scope')) }}" class="p-3 bg-gray-50 text-gray-400 rounded-2xl hover:bg-gray-100 transition-all">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>

        @if(!empty($cellScopeUnavailable))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                Você não pertence a nenhuma célula no momento. Peça ao líder ou à administração para associar sua conta.
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-white border border-gray-100 rounded-2xl p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</p>
                <p class="text-2xl font-black text-gray-900">{{ $contributions->total() }}</p>
            </div>
            <div class="bg-white border border-yellow-100 rounded-2xl p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-yellow-500">Pendentes</p>
                <p class="text-2xl font-black text-yellow-600">{{ $contributions->getCollection()->where('status', 'pendente')->count() }}</p>
            </div>
            <div class="bg-white border border-green-100 rounded-2xl p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-green-500">Verificadas</p>
                <p class="text-2xl font-black text-green-600">{{ $contributions->getCollection()->where('status', 'verificada')->count() }}</p>
            </div>
            <div class="bg-white border border-red-100 rounded-2xl p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-red-500">Rejeit./Cancel.</p>
                <p class="text-2xl font-black text-red-600">
                    {{ $contributions->getCollection()->whereIn('status', ['rejeitada', 'cancelada'])->count() }}
                </p>
            </div>
        </div>

        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Listagem de Movimentações</h3>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Dados em tempo real</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full table-compact">
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
                                        @elseif($contribution->status === 'rejeitada')
                                            <span class="px-4 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-100 flex items-center gap-2">
                                                <i class="bi bi-x-square-fill"></i> Rejeitado
                                            </span>
                                        @else
                                            <span class="px-4 py-1 bg-gray-50 text-gray-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-100 flex items-center gap-2">
                                                <i class="bi bi-slash-circle-fill"></i> Cancelado
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-10 py-6 text-center">
                                    @if ($contribution->proof_path)
                                        <a href="{{ route('contributions.receipt', $contribution) }}" target="_blank" title="Comprovativo"
                                            class="action-icon bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white mx-auto shadow-sm">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </a>
                                    @else
                                        <span class="text-[10px] text-gray-300 font-black uppercase tracking-widest">Nenhum</span>
                                    @endif
                                </td>

                                <td class="px-10 py-6 text-right">
                                    @php
                                        $canCancel = auth()->user()->isAdmin() && $contribution->status !== 'cancelada';
                                        $canDelete = (auth()->user()->isAdmin() || auth()->user()->isComissaoObra()) && in_array($contribution->status, ['pendente', 'cancelada', 'rejeitada'], true);
                                    @endphp
                                    <div class="flex items-center justify-end gap-2 text-sm">
                                        <a href="{{ route('contributions.show', $contribution) }}" title="Detalhes"
                                            class="action-icon bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @if ($contribution->status === 'pendente')
                                            @if(auth()->user()->isComissaoObra() || auth()->user()->isAdmin())
                                                <form action="{{ route('contributions.verify', $contribution) }}" method="POST" onsubmit="return confirm('Confirmar verificação desta contribuição?');">
                                                    @csrf
                                                    <button type="submit" class="action-icon bg-green-50 text-green-600 hover:bg-green-600 hover:text-white" title="Verificar">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                                <button onclick="document.getElementById('reject-form-{{ $contribution->id }}').classList.toggle('hidden')" 
                                                    class="action-icon bg-red-50 text-red-600 hover:bg-red-600 hover:text-white" title="Rejeitar">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            @elseif(auth()->id() === $contribution->user_id)
                                                <a href="{{ route('contributions.edit', $contribution) }}"
                                                    class="action-icon bg-gray-50 text-gray-400 hover:bg-orange-500 hover:text-white" title="Editar">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endif
                                        @endif

                                        @if($canCancel)
                                            <button onclick="document.getElementById('cancel-form-{{ $contribution->id }}').classList.toggle('hidden')" 
                                                class="action-icon bg-gray-100 text-gray-500 hover:bg-gray-800 hover:text-white" title="Cancelar Lançamento">
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                        @endif
                                        @if($canDelete)
                                            <button onclick="document.getElementById('delete-form-{{ $contribution->id }}').classList.toggle('hidden')"
                                                class="action-icon bg-red-50 text-red-600 hover:bg-red-600 hover:text-white" title="Eliminar Registo">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @if(auth()->user()->isComissaoObra() || auth()->user()->isAdmin())
                                    <div id="reject-form-{{ $contribution->id }}" class="hidden mt-2 p-4 bg-gray-50 rounded-2xl border border-gray-100 text-left">
                                        <form action="{{ route('contributions.reject', $contribution) }}" method="POST">
                                            @csrf
                                            <label class="text-[10px] font-black uppercase text-gray-400 block mb-2">Motivo da Rejeição</label>
                                            <textarea name="notes" required class="w-full p-3 rounded-xl border-gray-200 text-sm mb-2" placeholder="Descreva o motivo..."></textarea>
                                            <button type="submit" class="w-full py-2 bg-red-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest">Rejeitar Agora</button>
                                        </form>
                                    </div>

                                    @if($canCancel)
                                        <div id="cancel-form-{{ $contribution->id }}" class="hidden mt-2 p-4 bg-gray-50 rounded-2xl border border-gray-100 text-left">
                                            <form action="{{ route('contributions.cancel', $contribution) }}" method="POST">
                                                @csrf
                                                <label class="text-[10px] font-black uppercase text-gray-400 block mb-2">Motivo do Cancelamento (Histórico)</label>
                                                <textarea name="notes" required class="w-full p-3 rounded-xl border-gray-200 text-sm mb-2" placeholder="Explique porque está cancelando este lançamento..."></textarea>
                                                <button type="submit" class="w-full py-2 bg-gray-800 text-white rounded-xl font-black text-[10px] uppercase tracking-widest">Confirmar Cancelamento</button>
                                            </form>
                                        </div>
                                    @endif
                                    @if($canDelete)
                                        <div id="delete-form-{{ $contribution->id }}" class="hidden mt-2 p-4 bg-red-50 rounded-2xl border border-red-100 text-left">
                                            <form action="{{ route('contributions.destroy', $contribution) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <label class="text-[10px] font-black uppercase text-red-500 block mb-2">Motivo da Eliminação</label>
                                                <textarea name="notes" required class="w-full p-3 rounded-xl border-red-200 text-sm mb-2" placeholder="Explique porque está eliminando este registo..."></textarea>
                                                <button type="submit" class="w-full py-2 bg-red-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest">Confirmar Eliminação</button>
                                            </form>
                                        </div>
                                    @endif
                                    @endif
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
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($contributions as $contribution)
                @php
                    $canCancel = auth()->user()->isAdmin() && $contribution->status !== 'cancelada';
                    $canDelete = (auth()->user()->isAdmin() || auth()->user()->isComissaoObra()) && in_array($contribution->status, ['pendente', 'cancelada', 'rejeitada'], true);
                @endphp
                <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col group hover:shadow-xl transition-all duration-300 relative compact-card {{ $contribution->status === 'cancelada' ? 'opacity-60 grayscale bg-gray-50' : '' }}">
                    <div class="absolute top-6 right-6">
                        @if ($contribution->status === 'verificada')
                            <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-green-100">Validado</span>
                        @elseif($contribution->status === 'pendente')
                            <span class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-yellow-100 animate-pulse">Pendente</span>
                        @elseif($contribution->status === 'rejeitada')
                            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-red-100">Rejeitado</span>
                        @else
                            <span class="px-3 py-1 bg-gray-50 text-gray-400 rounded-full text-[9px] font-black uppercase tracking-widest border border-gray-100">Cancelado</span>
                        @endif
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center font-black text-2xl group-hover:bg-green-600 group-hover:text-white transition-all duration-500 mb-6">
                        <i class="bi bi-piggy-bank"></i>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-lg font-black text-green-600 tracking-tighter mb-1">
                            {{ number_format($contribution->amount, 0, ',', '.') }} MT
                        </h4>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $contribution->contribution_date->format('d/m/Y') }}</p>
                    </div>

                    <div class="space-y-3 mb-6 flex-1 bg-gray-50 p-4 rounded-2xl">
                        @if (isset($showUserColumn) && $showUserColumn)
                            <div class="flex flex-col border-b border-gray-100 pb-2">
                                <span class="text-[9px] font-black uppercase text-gray-400">Membro</span>
                                <span class="text-xs font-bold text-gray-700 line-clamp-1">{{ $contribution->user->name }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase text-gray-400">Referência</span>
                            <span class="text-xs font-mono text-gray-600">#{{ str_pad($contribution->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-4 border-t border-gray-50">
                        <a href="{{ route('contributions.show', $contribution) }}"
                            class="flex-1 bg-gray-900 text-white text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                            <i class="bi bi-eye"></i> Detalhes
                        </a>
                        @if ($contribution->status === 'pendente')
                            @if(auth()->user()->isComissaoObra() || auth()->user()->isAdmin())
                                <form action="{{ route('contributions.verify', $contribution) }}" method="POST" onsubmit="return confirm('Confirmar verificação desta contribuição?');">
                                    @csrf
                                    <button type="submit" class="w-10 h-10 bg-green-50 text-green-600 flex items-center justify-center rounded-xl hover:bg-green-600 hover:text-white transition-all">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            @elseif(auth()->id() === $contribution->user_id)
                                <a href="{{ route('contributions.edit', $contribution) }}"
                                    class="w-10 h-10 bg-gray-50 text-gray-400 flex items-center justify-center rounded-xl hover:bg-orange-500 hover:text-white transition-all">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
                        @endif

                        @if($canCancel)
                            <button onclick="document.getElementById('cancel-form-grid-{{ $contribution->id }}').classList.toggle('hidden')" 
                                class="w-10 h-10 bg-gray-100 text-gray-500 flex items-center justify-center rounded-xl hover:bg-gray-800 hover:text-white transition-all" title="Cancelar Lançamento">
                                <i class="bi bi-slash-circle"></i>
                            </button>
                        @endif
                        @if($canDelete)
                            <button onclick="document.getElementById('delete-form-grid-{{ $contribution->id }}').classList.toggle('hidden')"
                                class="w-10 h-10 bg-red-50 text-red-600 flex items-center justify-center rounded-xl hover:bg-red-600 hover:text-white transition-all"
                                title="Eliminar Registo">
                                <i class="bi bi-trash3"></i>
                            </button>
                        @endif
                    </div>
                    @if($canCancel)
                        <div id="cancel-form-grid-{{ $contribution->id }}" class="hidden absolute left-0 bottom-16 mb-2 w-full p-6 bg-white shadow-2xl rounded-2xl border border-gray-100 z-10">
                            <form action="{{ route('contributions.cancel', $contribution) }}" method="POST">
                                @csrf
                                <label class="text-[10px] font-black uppercase text-gray-400 block mb-2">Motivo do Cancelamento</label>
                                <textarea name="notes" required class="w-full p-3 rounded-xl bg-gray-50 border-none text-sm mb-4" rows="3" placeholder="Explique..."></textarea>
                                <div class="flex gap-2">
                                     <button type="button" onclick="document.getElementById('cancel-form-grid-{{ $contribution->id }}').classList.add('hidden')" class="flex-1 py-2 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-black uppercase">Cancelar</button>
                                     <button type="submit" class="flex-1 py-2 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase">Confirmar</button>
                                </div>
                            </form>
                        </div>
                    @endif
                    @if($canDelete)
                        <div id="delete-form-grid-{{ $contribution->id }}" class="hidden absolute left-0 bottom-16 mb-2 w-full p-6 bg-white shadow-2xl rounded-2xl border border-red-100 z-10">
                            <form action="{{ route('contributions.destroy', $contribution) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <label class="text-[10px] font-black uppercase text-red-500 block mb-2">Motivo da Eliminação</label>
                                <textarea name="notes" required class="w-full p-3 rounded-xl bg-red-50 border border-red-100 text-sm mb-4" rows="3" placeholder="Explique..."></textarea>
                                <div class="flex gap-2">
                                     <button type="button" onclick="document.getElementById('delete-form-grid-{{ $contribution->id }}').classList.add('hidden')" class="flex-1 py-2 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-black uppercase">Cancelar</button>
                                     <button type="submit" class="flex-1 py-2 bg-red-600 text-white rounded-lg text-[10px] font-black uppercase">Eliminar</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Custom Pagination -->
        @if($contributions->hasPages())
            <div class="pt-4">
                {{ $contributions->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
