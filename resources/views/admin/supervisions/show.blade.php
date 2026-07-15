@extends('layouts.app')

@section('title', "Supervisão $supervision->name - Portal Life Church")
@section('page-title', $supervision->name)
@section('page-subtitle', "Gestão da supervisão e suas células")

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('cells.create') }}?supervision_id={{ $supervision->id }}"
            class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
            title="Criar célula">
            <i class="bi bi-plus-circle"></i>
        </a>
        <a href="{{ route('supervisions.edit', $supervision) }}"
            class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50"
            title="Editar estrutura">
            <i class="bi bi-pencil-square"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-8" x-data="{
        showTransferModal: false,
        showZoneTransferModal: false,
        selectedCell: {},
        transfer(cell) {
            this.selectedCell = cell;
            this.showTransferModal = true;
        }
    }">
        <!-- Header & Stats Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Info Supervisão -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center">
                <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-3">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Supervisão</span>
                </div>
                <p class="text-3xl font-black text-gray-900 tracking-tighter">{{ $supervision->name }}</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="px-3 py-1 bg-gray-100 rounded-full text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                        Zona: {{ $supervision->zone->name }}
                    </span>
                    @if($supervision->supervisor)
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-bold uppercase tracking-widest">
                            Sup: {{ $supervision->supervisor->name }}
                        </span>
                    @endif
                    @if($supervision->subSupervisor)
                        <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-[10px] font-bold uppercase tracking-widest">
                            Sub: {{ $supervision->subSupervisor->name }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Total Células -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center">
                <p class="text-5xl font-black text-purple-600 tracking-tighter">{{ $supervision->cells->count() }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Células Ativas</p>
            </div>

            <!-- Total Membros -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center">
                @php $memberCount = $supervision->cells->flatMap(function($c) { return $c->members()->where('is_active', true)->get(); })->unique('id')->count(); @endphp
                <p class="text-5xl font-black text-blue-600 tracking-tighter">{{ $memberCount }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Corpo de Membros</p>
            </div>

            <!-- Total Arrecadado -->
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 flex flex-col justify-center text-center relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-4xl font-black text-green-600 tracking-tighter">
                        {{ number_format($supervision->getTotalContributedThisMonth(), 0, ',', '.') }}<span class="text-sm ml-1 uppercase">MT</span>
                    </p>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-2">Arrecadado este mês</p>
                </div>
                <div class="absolute -right-4 -bottom-4 text-8xl text-green-50 opacity-50"><i class="bi bi-cash-stack"></i></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3 space-y-6">
                <!-- Células da Supervisão -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 md:p-10 flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-gray-50">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 tracking-tighter">Unidades de Células</h3>
                            <p class="text-sm font-medium text-gray-400">Distribuição das células sob esta supervisão</p>
                        </div>
                        <a href="{{ route('cells.create') }}?supervision_id={{ $supervision->id }}" 
                           class="w-full sm:w-auto bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-5 py-3 rounded-2xl flex items-center justify-center transition-all font-bold text-sm">
                            <i class="bi bi-plus-lg mr-2"></i> Criar Célula
                        </a>
                    </div>
                    <!-- Desktop Table -->
                    <div class="overflow-x-auto hidden md:block">
                        <table class="w-full table-compact">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Unidade</th>
                                    <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Liderança</th>
                                    <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Membros</th>
                                    <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Contribuição</th>
                                    <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($cells as $cell)
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-10 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                                    {{ substr($cell->name, 0, 1) }}
                                                </div>
                                                <p class="text-sm font-bold text-gray-900 leading-tight group-hover:text-blue-600 transition-colors line-clamp-1">{{ $cell->name }}</p>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6">
                                            @if($cell->leader)
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-800 line-clamp-1">{{ $cell->leader->name }}</span>
                                                    <span class="text-[10px] text-gray-400 font-medium font-mono uppercase">Líder Principal</span>
                                                </div>
                                            @else
                                                <span class="text-gray-300 italic text-sm">Sem líder</span>
                                            @endif
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-[10px] font-black tracking-widest">
                                                {{ $cell->members()->where('is_active', true)->count() }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 text-right font-black text-gray-900">
                                            {{ number_format($cell->getTotalContributedThisMonth(), 0, ',', '.') }} MT
                                        </td>
                                        <td class="px-10 py-6 text-right">
                                            <div class="flex justify-end gap-2 opacity-70 hover:opacity-100 transition-all">
                                                <button @click="transfer({ id: {{ $cell->id }}, name: '{{ $cell->name }}' })"
                                                    class="action-icon bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600"
                                                    title="Transferir supervisão">
                                                    <i class="bi bi-arrow-left-right"></i>
                                                </button>
                                                <a href="{{ route('cells.show', $cell) }}"
                                                    class="action-icon bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600" title="Detalhes">
                                                    <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-10 py-16 text-center text-gray-400 font-medium italic">
                                            Nenhuma célula registrada nesta supervisão.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card Grid -->
                    <div class="grid grid-cols-1 gap-4 md:hidden p-4">
                        @forelse($cells as $cell)
                            <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4 hover:shadow-lg transition-shadow">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-sm uppercase">
                                        {{ substr($cell->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-black text-gray-900 leading-tight truncate">{{ $cell->name }}</h4>
                                        @if($cell->leader)
                                            <p class="text-[10px] font-bold text-gray-400 mt-0.5 truncate">Líder: {{ $cell->leader->name }}</p>
                                        @else
                                            <p class="text-[10px] font-bold text-gray-300 italic mt-0.5">Sem líder</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 border-t border-gray-50/80 pt-4">
                                    <div class="flex-1 space-y-0.5">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Membros</p>
                                        <span class="inline-block bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-widest">
                                            {{ $cell->members()->where('is_active', true)->count() }}
                                        </span>
                                    </div>
                                    <div class="flex-1 space-y-0.5 text-right">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Contribuição</p>
                                        <p class="text-sm font-black text-gray-900">{{ number_format($cell->getTotalContributedThisMonth(), 0, ',', '.') }} MT</p>
                                    </div>
                                </div>

                                <div class="flex gap-2 border-t border-gray-50/80 pt-4">
                                    <button @click="transfer({ id: {{ $cell->id }}, name: '{{ $cell->name }}' })"
                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition-all"
                                        title="Transferir supervisão">
                                        <i class="bi bi-arrow-left-right text-lg"></i>
                                    </button>
                                    <a href="{{ route('cells.show', $cell) }}"
                                        class="flex-1 bg-gray-50 text-gray-500 hover:bg-blue-50 hover:text-blue-600 h-10 rounded-xl flex items-center justify-center text-[10px] font-black uppercase tracking-widest transition-all">
                                        <i class="bi bi-chevron-right mr-1.5"></i> Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white border border-gray-100 rounded-3xl p-12 text-center text-gray-400 font-medium italic">
                                Nenhuma célula registrada nesta supervisão.
                            </div>
                        @endforelse
                    </div>
                    @if($cells->hasPages())
                        <div class="mt-6">
                            {{ $cells->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Coluna de Ações Rápidas (Hidden on Mobile) -->
            <div class="space-y-6 hidden md:block">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Gestão Regional</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('supervisions.edit', $supervision) }}"
                            class="w-full bg-blue-600 text-white px-6 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-pencil-square"></i> Editar Estrutura
                        </a>
                        <a href="{{ route('supervisions.index') }}"
                            class="w-full bg-gray-50 text-gray-500 px-6 py-4 rounded-2xl hover:bg-gray-100 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                            <i class="bi bi-arrow-left"></i> Voltar à Lista
                        </a>
                        @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria())
                            <button @click="showZoneTransferModal = true"
                                class="w-full bg-amber-50 text-amber-600 px-6 py-4 rounded-2xl hover:bg-amber-100 transition-all font-black text-xs uppercase tracking-widest flex items-center justify-center gap-3">
                                <i class="bi bi-arrow-left-right"></i> Transferir de Zona
                            </button>
                        @endif
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-900 to-indigo-900 rounded-[2.5rem] shadow-xl p-10 text-white relative overflow-hidden">
                    <div class="relative z-10 space-y-6">
                        <p class="text-[10px] font-black text-purple-300 uppercase tracking-[0.2em]">Desempenho Geral</p>
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-purple-100">Crescimento da Supervisão</p>
                            <div class="flex items-end gap-2">
                                <span class="text-4xl font-black tracking-tighter text-white">{{ $supervision->cells->count() }}</span>
                                <span class="text-xs font-bold text-purple-300 mb-1">Células Ativas</span>
                            </div>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5 mt-4">
                            <div class="bg-purple-400 h-1.5 rounded-full" style="width: 80%"></div>
                        </div>
                    </div>
                    <i class="bi bi-briefcase-fill absolute -right-4 -bottom-4 text-9xl text-white opacity-5"></i>
                </div>
            </div>
        </div>

        <!-- Transfer Cell Modal -->
        <div x-show="showTransferModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            <div @click.away="showTransferModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden animate-In">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-black text-gray-900 leading-tight">Transferir Célula</h3>
                    <p class="text-sm text-gray-500 mt-1" x-text="'Mover ' + selectedCell.name + ' para outra supervisão'"></p>
                </div>
                <form :action="'{{ url('/admin/cells') }}/' + selectedCell.id + '/reassign-supervision'" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Selecione a Supervisão de Destino</label>
                        <select name="supervision_id" required data-searchable="false" class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all custom-select">
                            <option value="">Escolha uma supervisão...</option>
                            @foreach($availableSupervisions as $availSup)
                                <option value="{{ $availSup->id }}">{{ $availSup->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showTransferModal = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-50 text-gray-500 font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">Cancelar</button>
                        <button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-blue-600 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transfer Supervision to Zone Modal -->
        <div x-show="showZoneTransferModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            <div @click.away="showZoneTransferModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden animate-In">
                <div class="p-8 border-b border-gray-100">
                    <h3 class="text-xl font-black text-gray-900 leading-tight">Transferir Supervisão</h3>
                    <p class="text-sm text-gray-500 mt-1">Mover {{ $supervision->name }} para outra zona pastoral</p>
                </div>
                <form action="{{ route('supervisions.reassign-zone', $supervision) }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Selecione a Zona de Destino</label>
                        <select name="zone_id" required data-searchable="false" class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all custom-select">
                            <option value="">Escolha uma zona...</option>
                            @foreach($availableZones as $availZone)
                                <option value="{{ $availZone->id }}">{{ $availZone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showZoneTransferModal = false" class="flex-1 px-6 py-4 rounded-2xl bg-gray-50 text-gray-500 font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">Cancelar</button>
                        <button type="submit" class="flex-1 px-6 py-4 rounded-2xl bg-blue-600 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">Confirmar Transferência</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
