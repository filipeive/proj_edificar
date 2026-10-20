@extends('layouts.app')

@section('title', "Supervisão $supervision->name - Portal Life Church")
@section('page-title', $supervision->name)
@section('page-subtitle', "Gestão da supervisão e suas células")

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('supervisions.index') }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-left text-xs"></i>
            <span>Voltar</span>
        </a>
        <a href="{{ route('cells.create') }}?supervision_id={{ $supervision->id }}"
            class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors"
            title="Criar célula">
            <i class="bi bi-plus-lg text-xs"></i>
            <span class="hidden sm:inline">Criar Célula</span>
        </a>
        <a href="{{ route('supervisions.edit', $supervision) }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
            title="Editar estrutura">
            <i class="bi bi-pencil-square text-xs text-orange-700"></i>
            <span class="hidden sm:inline">Editar</span>
        </a>
        @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria())
            <button type="button" @click="$dispatch('open-zone-transfer-modal')"
                class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 shadow-sm hover:bg-amber-100 transition-colors">
                <i class="bi bi-arrow-left-right text-xs"></i>
                <span class="hidden sm:inline">Transferir Zona</span>
            </button>
        @endif
        @if($supervision->cells->count() == 0)
            <form action="{{ route('supervisions.destroy', $supervision) }}" method="POST" id="delete-supervision-{{ $supervision->id }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete('delete-supervision-{{ $supervision->id }}', 'Deseja excluir esta supervisão?')" title="Eliminar"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-600 shadow-sm hover:bg-red-100 transition-colors">
                    <i class="bi bi-trash text-xs"></i>
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
    <div class="space-y-5" x-data="{
        showTransferModal: false,
        showZoneTransferModal: false,
        selectedCell: {},
        transfer(cell) {
            this.selectedCell = cell;
            this.showTransferModal = true;
        }
    }" @open-zone-transfer-modal.window="showZoneTransferModal = true">

        <!-- Executive Banner -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-orange-800 border border-orange-200">
                            <i class="bi bi-diagram-3-fill text-[9px]"></i> Supervisão
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-700">
                            Zona: {{ $supervision->zone->name }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ $supervision->name }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 pt-0.5">
                        @if($supervision->supervisor)
                            <span class="inline-flex items-center gap-1 font-semibold text-gray-700">
                                <i class="bi bi-person-badge text-orange-700"></i> Supervisor: <strong class="text-gray-900">{{ $supervision->supervisor->name }}</strong>
                            </span>
                        @endif
                        @if($supervision->subSupervisor)
                            <span class="inline-flex items-center gap-1 font-semibold text-gray-700">
                                <i class="bi bi-person text-purple-700"></i> Sub-supervisor: <strong class="text-gray-900">{{ $supervision->subSupervisor->name }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.supervision', ['supervision_id' => $supervision->id]) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                        <i class="bi bi-file-earmark-bar-graph text-xs text-orange-700"></i>
                        <span>Relatório Geral</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Células Ativas</p>
                    <p class="mt-1 text-2xl font-black text-gray-900 tracking-tight">{{ $supervision->cells->count() }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-house-door text-lg"></i>
                </div>
            </div>

            @php $memberCount = $supervision->cells->flatMap(function($c) { return $c->members()->where('is_active', true)->get(); })->unique('id')->count(); @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Corpo de Membros</p>
                    <p class="mt-1 text-2xl font-black text-blue-700 tracking-tight">{{ $memberCount }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                    <i class="bi bi-people text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Arrecadação Mês</p>
                    <p class="mt-1 text-2xl font-black text-emerald-700 tracking-tight">
                        {{ number_format($supervision->getTotalContributedThisMonth(), 0, ',', '.') }} <span class="text-xs font-bold text-gray-400">MT</span>
                    </p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <i class="bi bi-cash-stack text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Status</p>
                    <p class="mt-1 text-sm font-bold text-gray-900">
                        @if($supervision->is_active ?? true)
                            <span class="inline-flex items-center gap-1 text-emerald-700"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Ativa</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-gray-400"><span class="h-2 w-2 rounded-full bg-gray-400"></span> Inativa</span>
                        @endif
                    </p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-700">
                    <i class="bi bi-check2-circle text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Células da Supervisão -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 px-4 py-3 sm:px-5 bg-gray-50/60">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-wider text-gray-900">Unidades de Células</h2>
                    <p class="text-xs text-gray-500">Distribuição das células associadas sob esta supervisão</p>
                </div>
                <a href="{{ route('cells.create') }}?supervision_id={{ $supervision->id }}" 
                   class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors self-start sm:self-auto">
                    <i class="bi bi-plus-lg text-xs"></i>
                    <span>Nova Célula</span>
                </a>
            </div>

            <!-- Desktop Table -->
            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-[10px] uppercase font-black tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2.5">Unidade</th>
                            <th class="px-4 py-2.5">Liderança</th>
                            <th class="px-4 py-2.5 text-center">Membros</th>
                            <th class="px-4 py-2.5 text-right">Contribuição</th>
                            <th class="px-4 py-2.5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($cells as $cell)
                            <tr class="hover:bg-gray-50/70 transition-colors group">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 font-black text-orange-700 text-xs border border-orange-200/50">
                                            {{ substr($cell->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('cells.show', $cell) }}" class="font-bold text-gray-900 group-hover:text-orange-700 transition-colors">
                                                {{ $cell->name }}
                                            </a>
                                            @if($cell->address)
                                                <p class="text-[11px] text-gray-400 truncate max-w-xs">{{ $cell->address }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($cell->leader)
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-gray-900">{{ $cell->leader->name }}</span>
                                            <span class="text-[10px] text-gray-400">Líder Principal</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Sem líder</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-700">
                                        {{ $cell->members()->where('is_active', true)->count() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">
                                    {{ number_format($cell->getTotalContributedThisMonth(), 0, ',', '.') }} MT
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button @click="transfer({ id: {{ $cell->id }}, name: '{{ addslashes($cell->name) }}' })"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:border-orange-300 hover:text-orange-700 hover:bg-orange-50 transition-colors"
                                            title="Transferir supervisão">
                                            <i class="bi bi-arrow-left-right text-xs"></i>
                                        </button>
                                        <a href="{{ route('cells.show', $cell) }}"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                                            title="Detalhes">
                                            <i class="bi bi-chevron-right text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-xs text-gray-400 italic">
                                    Nenhuma célula registrada nesta supervisão.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Grid -->
            <div class="grid grid-cols-1 gap-2.5 p-3 md:hidden">
                @forelse($cells as $cell)
                    <div class="rounded-lg border border-gray-200 bg-white p-3 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 font-black text-orange-700 text-xs shrink-0">
                                    {{ substr($cell->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-black text-gray-900 truncate">{{ $cell->name }}</h4>
                                    <p class="text-[11px] text-gray-500 truncate">{{ $cell->leader ? $cell->leader->name : 'Sem líder' }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 shrink-0">
                                {{ $cell->members()->where('is_active', true)->count() }} membros
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                            <span class="text-gray-500 font-medium">Contribuição mês:</span>
                            <span class="font-black text-gray-900">{{ number_format($cell->getTotalContributedThisMonth(), 0, ',', '.') }} MT</span>
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            <button @click="transfer({ id: {{ $cell->id }}, name: '{{ addslashes($cell->name) }}' })"
                                class="flex-1 inline-flex items-center justify-center gap-1 rounded-md border border-gray-200 bg-gray-50 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100">
                                <i class="bi bi-arrow-left-right text-xs"></i>
                                <span>Transferir</span>
                            </button>
                            <a href="{{ route('cells.show', $cell) }}"
                                class="flex-1 inline-flex items-center justify-center gap-1 rounded-md bg-orange-700 py-1.5 text-xs font-bold text-white hover:bg-orange-800">
                                <span>Detalhes</span>
                                <i class="bi bi-chevron-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-gray-200 p-8 text-center text-xs text-gray-400 italic">
                        Nenhuma célula registrada nesta supervisão.
                    </div>
                @endforelse
            </div>

            @if($cells->hasPages())
                <div class="border-t border-gray-200 px-4 py-3 bg-gray-50">
                    {{ $cells->links() }}
                </div>
            @endif
        </div>

        <!-- Transfer Cell Modal -->
        <div x-show="showTransferModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            <div @click.away="showTransferModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/60">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Transferir Célula</h3>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="'Mover ' + selectedCell.name + ' para outra supervisão'"></p>
                </div>
                <form :action="'{{ url('/admin/cells') }}/' + selectedCell.id + '/reassign-supervision'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700">Selecione a Supervisão de Destino</label>
                        <select name="supervision_id" required class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Escolha uma supervisão...</option>
                            @foreach($availableSupervisions as $availSup)
                                <option value="{{ $availSup->id }}">{{ $availSup->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" @click="showTransferModal = false" class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">Confirmar Transferência</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transfer Supervision to Zone Modal -->
        <div x-show="showZoneTransferModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            <div @click.away="showZoneTransferModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/60">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Transferir Supervisão de Zona</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Mover {{ $supervision->name }} para outra zona pastoral</p>
                </div>
                <form action="{{ route('supervisions.reassign-zone', $supervision) }}" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700">Selecione a Zona de Destino</label>
                        <select name="zone_id" required class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Escolha uma zona...</option>
                            @foreach($availableZones as $availZone)
                                <option value="{{ $availZone->id }}">{{ $availZone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" @click="showZoneTransferModal = false" class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">Confirmar Mudança</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
