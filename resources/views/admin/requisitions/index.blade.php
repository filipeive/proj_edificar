@extends('layouts.app')

@section('title', 'Requisições de Fundos')
@section('page-title', 'Requisições de Fundos')
@section('page-subtitle', 'Gerencie solicitações de saída de caixa')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 border-l-8 border-yellow-500">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pendentes</div>
            <div class="text-3xl font-black text-yellow-600 tracking-tighter">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 border-l-8 border-green-500">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Aprovadas</div>
            <div class="text-3xl font-black text-green-600 tracking-tighter">{{ $stats['approved'] }}</div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 md:p-8 border-b border-gray-50 flex justify-between items-center">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Histórico de Requisições</h3>
            <a href="{{ route('requisitions.create') }}"
                class="bg-blue-600 text-white px-6 py-3 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-blue-200">
                <i class="bi bi-plus-lg mr-2"></i> Nova
            </a>
        </div>

        <!-- Mobile Grid View -->
        <div class="grid grid-cols-1 gap-4 md:hidden p-4 bg-gray-50/50">
            @forelse($requisitions as $requisition)
                <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                             <span class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-2">
                                {{ $requisition->category }}
                            </span>
                            <h4 class="font-bold text-gray-900 leading-tight">#{{ $requisition->id }} - {{ $requisition->user->name }}</h4>
                        </div>
                        <span class="text-gray-900 font-black text-lg">{{ number_format($requisition->amount, 2, ',', '.') }}</span>
                    </div>

                    <p class="text-sm text-gray-600 mb-3">{{ $requisition->description }}</p>

                    <div class="flex justify-between items-center text-xs mb-3 font-medium">
                        <span class="text-gray-400">{{ $requisition->created_at->format('d/m/Y') }}</span>
                        <div class="flex items-center gap-2">
                             <span class="text-[10px] font-bold uppercase tracking-widest {{ ($requisition->scope ?? 'eclesiastico') == 'edificar' ? 'text-orange-500' : 'text-blue-500' }}">
                                {{ ucfirst($requisition->scope ?? 'eclesiastico') }}
                            </span>
                            @if($requisition->status === 'pending')
                                <span class="text-yellow-600 bg-yellow-100 px-2 py-1 rounded-lg">Pendente</span>
                            @elseif($requisition->status === 'approved')
                                <span class="text-green-600 bg-green-100 px-2 py-1 rounded-lg">Aprovada</span>
                            @elseif($requisition->status === 'rejected')
                                <span class="text-red-600 bg-red-100 px-2 py-1 rounded-lg">Rejeitada</span>
                            @else
                                <span class="text-gray-600 bg-gray-100 px-2 py-1 rounded-lg">{{ $requisition->status }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions (Mobile) -->
                     @if($requisition->status === 'pending' && (auth()->user()->isAdmin() || auth()->user()->isTesouraria()))
                        <div class="flex gap-2 mt-4 pt-4 border-t border-gray-50">
                            <form action="{{ route('requisitions.approve', $requisition) }}" method="POST" id="mob-approve-form-{{ $requisition->id }}" class="flex-1">
                                @csrf
                                <button type="button"
                                    onclick="confirmAction('Aprovar esta requisição?', 'Aprovar').then(result => { if(result.isConfirmed) document.getElementById('mob-approve-form-{{ $requisition->id }}').submit(); })"
                                    class="w-full bg-green-50 text-green-600 py-3 rounded-2xl font-bold text-xs uppercase hover:bg-green-100 transition-colors">
                                    Aprovar
                                </button>
                            </form>
                            <button onclick="document.getElementById('rejectModal-{{ $requisition->id }}').classList.remove('hidden')"
                                class="flex-1 bg-red-50 text-red-600 py-3 rounded-2xl font-bold text-xs uppercase hover:bg-red-100 transition-colors">
                                Rejeitar
                            </button>
                        </div>
                        <!-- Rejection Modal Mobile (Shared logic, kept simple) -->
                     @endif
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 font-bold text-sm">Nenhuma requisição encontrada.</div>
            @endforelse
            <div class="mt-4">{{ $requisitions->links() }}</div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID / Data</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Solicitante</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Descrição</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Âmbito</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Valor</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($requisitions as $requisition)
                        <tr class="bg-white hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900">#{{ $requisition->id }}</span>
                                <div class="text-xs text-gray-500 font-medium">{{ $requisition->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $requisition->user->name }}</div>
                                <div class="text-xs text-gray-500 font-medium uppercase">{{ $requisition->category }}</div>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate font-medium text-gray-600" title="{{ $requisition->description }}">
                                {{ $requisition->description }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold uppercase {{ ($requisition->scope ?? 'eclesiastico') == 'edificar' ? 'text-orange-500' : 'text-blue-500' }}">
                                    {{ ucfirst($requisition->scope ?? 'eclesiastico') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-gray-900">
                                {{ number_format($requisition->amount, 2, ',', '.') }} MT
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($requisition->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full uppercase">Pendente</span>
                                @elseif($requisition->status === 'approved')
                                    <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full uppercase">Aprovada</span>
                                @elseif($requisition->status === 'rejected')
                                    <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full uppercase">Rejeitada</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-bold px-3 py-1 rounded-full uppercase">{{ $requisition->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
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
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400 font-bold uppercase tracking-widest">Nenhuma requisição encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-6 border-t border-gray-50">{{ $requisitions->links() }}</div>
        </div>
    </div>
@endsection