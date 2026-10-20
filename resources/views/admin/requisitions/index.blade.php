@extends('layouts.app')

@section('title', 'Requisições de Fundos')
@section('page-title', 'Requisições de Fundos')
@section('page-subtitle', 'Gerencie solicitações de saída de caixa')

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Tesouraria & Finanças</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Requisições de Fundos</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Gerencie solicitações de saída de caixa</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('requisitions.create') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-plus-lg"></i> <span>Nova Requisição</span>
                </a>
            </div>
        </header>

        <div class="grid grid-cols-2 gap-3">
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="text-[11px] sm:text-xs font-bold text-amber-600">Pendentes</div>
                <div class="mt-1 text-lg sm:text-2xl font-black text-amber-700 tracking-tight">{{ $stats['pending'] }}</div>
            </article>
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="text-[11px] sm:text-xs font-bold text-emerald-600">Aprovadas</div>
                <div class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight">{{ $stats['approved'] }}</div>
            </article>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-xs font-black text-gray-700 uppercase tracking-wider">Histórico de Requisições</h3>
            </div>

            <!-- Mobile Grid View -->
            <div class="grid grid-cols-1 gap-3 md:hidden p-3.5 bg-gray-50/50">
                @forelse($requisitions as $requisition)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                 <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-wider rounded-md mb-1.5">
                                    {{ $requisition->category }}
                                </span>
                                <h4 class="text-sm font-bold text-gray-900 leading-tight">#{{ $requisition->id }} - {{ $requisition->user->name }}</h4>
                            </div>
                            <span class="text-gray-900 font-black text-base">{{ number_format($requisition->amount, 2, ',', '.') }}</span>
                        </div>

                        <p class="text-xs sm:text-sm text-gray-600 mb-3">{{ $requisition->description }}</p>

                        <div class="flex justify-between items-center text-xs mb-3 font-medium">
                            <span class="text-gray-400">{{ $requisition->created_at->format('d/m/Y') }}</span>
                            <div class="flex items-center gap-2">
                                 <span class="text-[10px] font-bold uppercase tracking-wider {{ ($requisition->scope ?? 'eclesiastico') == 'edificar' ? 'text-orange-600' : 'text-blue-600' }}">
                                    {{ ucfirst($requisition->scope ?? 'eclesiastico') }}
                                </span>
                                @if($requisition->status === 'pending')
                                    <span class="text-yellow-700 bg-yellow-50 border border-yellow-200 px-2 py-0.5 rounded-full text-[10px] font-black uppercase">Pendente</span>
                                @elseif($requisition->status === 'approved')
                                    <span class="text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full text-[10px] font-black uppercase">Aprovada</span>
                                @elseif($requisition->status === 'rejected')
                                    <span class="text-red-700 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full text-[10px] font-black uppercase">Rejeitada</span>
                                @else
                                    <span class="text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full text-[10px] font-black uppercase">{{ $requisition->status }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Actions (Mobile) -->
                         @if($requisition->status === 'pending' && (auth()->user()->isAdmin() || auth()->user()->isTesouraria()))
                            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                                <form action="{{ route('requisitions.approve', $requisition) }}" method="POST" id="mob-approve-form-{{ $requisition->id }}" class="flex-1">
                                    @csrf
                                    <button type="button"
                                        onclick="confirmAction('Aprovar esta requisição?', 'Aprovar').then(result => { if(result.isConfirmed) document.getElementById('mob-approve-form-{{ $requisition->id }}').submit(); })"
                                        class="w-full bg-green-50 text-green-700 border border-green-200 py-2 rounded-lg font-bold text-xs hover:bg-green-100 transition-colors">
                                        Aprovar
                                    </button>
                                </form>
                                <button onclick="document.getElementById('rejectModal-{{ $requisition->id }}').classList.remove('hidden')"
                                    class="flex-1 bg-red-50 text-red-700 border border-red-200 py-2 rounded-lg font-bold text-xs hover:bg-red-100 transition-colors">
                                    Rejeitar
                                </button>
                            </div>
                         @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 font-bold text-xs uppercase tracking-wider">Nenhuma requisição encontrada.</div>
                @endforelse
                <div class="mt-3">{{ $requisitions->links() }}</div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3">ID / Data</th>
                            <th class="px-4 py-3">Solicitante</th>
                            <th class="px-4 py-3">Descrição</th>
                            <th class="px-4 py-3">Âmbito</th>
                            <th class="px-4 py-3 text-right">Valor</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($requisitions as $requisition)
                            <tr class="bg-white hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-900">#{{ $requisition->id }}</span>
                                    <div class="text-xs text-gray-500 font-medium">{{ $requisition->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900">{{ $requisition->user->name }}</div>
                                    <div class="text-[11px] text-gray-500 font-medium uppercase">{{ $requisition->category }}</div>
                                </td>
                                <td class="px-4 py-3 max-w-xs truncate font-medium text-gray-600" title="{{ $requisition->description }}">
                                    {{ $requisition->description }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-bold uppercase {{ ($requisition->scope ?? 'eclesiastico') == 'edificar' ? 'text-orange-600' : 'text-blue-600' }}">
                                        {{ ucfirst($requisition->scope ?? 'eclesiastico') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-black text-gray-900">
                                    {{ number_format($requisition->amount, 2, ',', '.') }} MT
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($requisition->status === 'pending')
                                        <span class="bg-yellow-50 text-yellow-800 border border-yellow-200 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">Pendente</span>
                                    @elseif($requisition->status === 'approved')
                                        <span class="bg-green-50 text-green-800 border border-green-200 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">Aprovada</span>
                                    @elseif($requisition->status === 'rejected')
                                        <span class="bg-red-50 text-red-800 border border-red-200 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">Rejeitada</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase">{{ $requisition->status }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                @if($requisition->status === 'pending' && (auth()->user()->isAdmin() || auth()->user()->isTesouraria()))
                                    <div class="flex justify-center space-x-2">
                                        <form action="{{ route('requisitions.approve', $requisition) }}" method="POST" id="approve-form-{{ $requisition->id }}">
                                            @csrf
                                            <button type="button"
                                                onclick="confirmAction('Aprovar esta requisição? Isso gerará uma despesa automaticamente.', 'Aprovar').then(result => { if(result.isConfirmed) document.getElementById('approve-form-{{ $requisition->id }}').submit(); })"
                                                class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-100 transition-colors" title="Aprovar">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <button onclick="document.getElementById('rejectModal-{{ $requisition->id }}').classList.remove('hidden')"
                                            class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-colors" title="Rejeitar">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de Rejeição (Inside loop for desktop context) -->
                                    <div id="rejectModal-{{ $requisition->id }}" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden overflow-y-auto z-50 flex items-center justify-center p-4">
                                        <div class="bg-white p-6 rounded-[2rem] shadow-2xl w-full max-w-sm">
                                            <h3 class="text-lg font-black text-gray-900 mb-4">Rejeitar Requisição #{{ $requisition->id }}</h3>
                                            <form action="{{ route('requisitions.reject', $requisition) }}" method="POST">
                                                @csrf
                                                <textarea name="rejection_reason" class="w-full bg-gray-50 border-transparent focus:bg-white rounded-2xl p-4 mb-4 text-sm font-medium" placeholder="Motivo da rejeição..." required rows="3"></textarea>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="document.getElementById('rejectModal-{{ $requisition->id }}').classList.add('hidden')"
                                                        class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-50 rounded-xl">Cancelar</button>
                                                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-xl font-bold uppercase text-xs tracking-widest shadow-lg shadow-red-200">Rejeitar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-xs font-bold uppercase tracking-wider">Nenhuma requisição encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-200">{{ $requisitions->links() }}</div>
        </div>
    </div>
    </div>
@endsection