@extends('layouts.app')
@section('title', "Zona $zone->name - Portal Life Church")
@section('page-title', 'Detalhes da Zona')
@section('page-subtitle', 'Visão consolidada da área pastoral ' . $zone->name)

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('zones.index') }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-left text-xs"></i>
            <span>Voltar</span>
        </a>
        <a href="{{ route('zones.edit', $zone) }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
            title="Configurar zona">
            <i class="bi bi-pencil-square text-xs text-orange-700"></i>
            <span class="hidden sm:inline">Configurar</span>
        </a>
        <a href="{{ route('supervisions.create') }}?zone_id={{ $zone->id }}"
            class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
            <i class="bi bi-plus-lg text-xs"></i>
            <span class="hidden sm:inline">Nova Supervisão</span>
        </a>
        @if($zone->supervisions->count() === 0)
            <form action="{{ route('zones.destroy', $zone) }}" method="POST" id="delete-zone-{{ $zone->id }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete('delete-zone-{{ $zone->id }}', 'Deseja excluir esta zona?')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-600 shadow-sm hover:bg-red-100 transition-colors"
                    title="Excluir Zona">
                    <i class="bi bi-trash text-xs"></i>
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
    <div class="space-y-5" x-data="{ 
        activeTab: 'supervisions',
        showTransferModal: false,
        selectedSupervision: {},
        transfer(supervision) {
            this.selectedSupervision = supervision;
            this.showTransferModal = true;
        }
    }">
        <!-- Top Navigation & Action Bar (Desktop) -->
        <div class="hidden md:flex items-center justify-between gap-3">
            <a href="{{ route('zones.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 dark:text-zinc-400 hover:text-orange-700 dark:hover:text-orange-400 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Zonas</span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('supervisions.create') }}?zone_id={{ $zone->id }}"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                    <i class="bi bi-plus-lg text-xs"></i>
                    <span>Nova Supervisão</span>
                </a>
                <a href="{{ route('zones.edit', $zone) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3.5 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-200 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
                    title="Configurar zona">
                    <i class="bi bi-pencil-square text-xs text-orange-700 dark:text-orange-400"></i>
                    <span>Configurar</span>
                </a>
                @if($zone->supervisions->count() === 0)
                    <form action="{{ route('zones.destroy', $zone) }}" method="POST" id="delete-zone-desktop-{{ $zone->id }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete('delete-zone-desktop-{{ $zone->id }}', 'Deseja excluir esta zona?')"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-950/40 px-3 py-1.5 text-xs font-bold text-red-600 dark:text-red-400 shadow-sm hover:bg-red-100 dark:hover:bg-red-900/60 transition-colors"
                            title="Excluir Zona">
                            <i class="bi bi-trash text-xs"></i>
                            <span>Excluir</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Executive Banner -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-orange-800 border border-orange-200">
                            <i class="bi bi-geo-alt-fill text-[9px]"></i> Zona Pastoral
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-800 border border-blue-200">
                            Pastor: {{ $zone->pastor->name ?? 'Pendente' }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ $zone->name }}</h1>
                    @if($zone->description)
                        <p class="text-xs text-gray-500 max-w-2xl">{{ $zone->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.zone', ['zone_id' => $zone->id]) }}"
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
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Supervisões</p>
                    <p class="mt-1 text-2xl font-black text-purple-700 tracking-tight">{{ $zone->supervisions->count() }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-700">
                    <i class="bi bi-diagram-3 text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Unidades de Células</p>
                    <p class="mt-1 text-2xl font-black text-orange-700 tracking-tight">{{ $cells->total() }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-house-door text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Membros Totais</p>
                    <p class="mt-1 text-2xl font-black text-blue-700 tracking-tight">{{ $members->total() }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                    <i class="bi bi-people text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Arrecadação Mês</p>
                    <p class="mt-1 text-2xl font-black text-emerald-700 tracking-tight">
                        {{ number_format($zone->getTotalContributedThisMonth(), 0, ',', '.') }} <span class="text-xs font-bold text-gray-400">MT</span>
                    </p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <i class="bi bi-cash-stack text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-1.5 border-b border-gray-200 pb-2">
            <button @click="activeTab = 'supervisions'"
                :class="activeTab === 'supervisions' ? 'bg-orange-700 text-white font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 font-semibold'"
                class="rounded-lg px-3.5 py-1.5 text-xs transition-colors">
                <i class="bi bi-diagram-3 mr-1"></i> Supervisões ({{ $zone->supervisions->count() }})
            </button>
            <button @click="activeTab = 'cells'"
                :class="activeTab === 'cells' ? 'bg-orange-700 text-white font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 font-semibold'"
                class="rounded-lg px-3.5 py-1.5 text-xs transition-colors">
                <i class="bi bi-house mr-1"></i> Células ({{ $cells->total() }})
            </button>
            <button @click="activeTab = 'members'"
                :class="activeTab === 'members' ? 'bg-orange-700 text-white font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 font-semibold'"
                class="rounded-lg px-3.5 py-1.5 text-xs transition-colors">
                <i class="bi bi-people mr-1"></i> Membros ({{ $members->total() }})
            </button>
        </div>

        <!-- Tab 1: Supervisões -->
        <div x-show="activeTab === 'supervisions'" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($zone->supervisions as $supervision)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:border-orange-300 transition-colors flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-50 font-black text-orange-700 text-xs">
                                        {{ substr($supervision->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-900 line-clamp-1">{{ $supervision->name }}</h3>
                                        <p class="text-[11px] text-gray-500">
                                            {{ $supervision->supervisor ? $supervision->supervisor->name : 'Sem supervisor' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button @click="transfer({{ Js::from($supervision) }})"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-amber-300 hover:text-amber-600 hover:bg-amber-50"
                                        title="Transferir para outra zona">
                                        <i class="bi bi-arrow-left-right text-xs"></i>
                                    </button>
                                    <a href="{{ route('supervisions.edit', $supervision) }}"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-gray-300 hover:text-gray-900 hover:bg-gray-50"
                                        title="Editar">
                                        <i class="bi bi-pencil-square text-xs"></i>
                                    </a>
                                    <a href="{{ route('supervisions.show', $supervision) }}"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-orange-300 hover:text-orange-700 hover:bg-orange-50"
                                        title="Ver detalhes">
                                        <i class="bi bi-chevron-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-xs">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Células:</span>
                                <span class="font-bold text-gray-900 ml-1">{{ $supervision->cells->count() }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Membros:</span>
                                <span class="font-bold text-blue-700 ml-1">{{ $supervision->cells->flatMap(fn($c) => $c->members)->count() }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-gray-200 p-8 text-center text-xs text-gray-400 italic">
                        Nenhuma supervisão registrada nesta zona.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 2: Células -->
        <div x-show="activeTab === 'cells'" style="display: none;">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                @if($cells->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-[10px] uppercase font-black tracking-wider text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2.5">Unidade</th>
                                    <th class="px-4 py-2.5">Supervisão</th>
                                    <th class="px-4 py-2.5">Liderança</th>
                                    <th class="px-4 py-2.5 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($cells as $cell)
                                    <tr class="hover:bg-gray-50/70 transition-colors">
                                        <td class="px-4 py-2.5 font-bold text-gray-900">
                                            <a href="{{ route('cells.show', $cell) }}" class="hover:text-orange-700">
                                                {{ $cell->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-2.5 text-gray-500">
                                            {{ $cell->supervision->name ?? 'Sem supervisão' }}
                                        </td>
                                        <td class="px-4 py-2.5 text-gray-700 font-medium">
                                            {{ $cell->leader->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2.5 text-right">
                                            <a href="{{ route('cells.show', $cell) }}"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-gray-300 hover:text-gray-900 hover:bg-gray-50">
                                                <i class="bi bi-chevron-right text-xs"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($cells->hasPages())
                        <div class="border-t border-gray-200 px-4 py-2.5 bg-gray-50">
                            {{ $cells->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-8 text-center text-xs text-gray-400 italic">
                        Nenhuma célula registrada nesta zona.
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab 3: Membros -->
        <div x-show="activeTab === 'members'" x-data="{ 
            memberSearch: '',
            showTransferMemberModal: false,
            showObservationsModal: false,
            selectedMember: {},
            memberObservations: '',
            openTransferMember(member) {
                this.selectedMember = member;
                this.showTransferMemberModal = true;
            },
            openObservations(member) {
                this.selectedMember = member;
                this.memberObservations = member.observations || '';
                this.showObservationsModal = true;
            }
        }" style="display: none;">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="p-3 border-b border-gray-200 bg-gray-50/60 flex items-center">
                    <div class="relative w-full max-w-sm">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" x-model="memberSearch" placeholder="Pesquisar por nome ou email..."
                            class="w-full rounded-lg border-gray-300 pl-8 pr-3 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-[10px] uppercase font-black tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2.5">Membro</th>
                                <th class="px-4 py-2.5">Célula</th>
                                <th class="px-4 py-2.5 text-center">Status</th>
                                <th class="px-4 py-2.5 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($members as $member)
                                <tr class="hover:bg-gray-50/70 transition-colors"
                                    x-show="memberSearch === '' || '{{ strtolower($member->name) }}'.includes(memberSearch.toLowerCase()) || '{{ strtolower($member->email) }}'.includes(memberSearch.toLowerCase())">
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gray-100 font-bold text-gray-600 text-xs shrink-0">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-gray-900 truncate">{{ $member->name }}</p>
                                                <p class="text-[11px] text-gray-400 truncate">{{ $member->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-gray-600">
                                        {{ $member->cell->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <form action="{{ route('users.toggle-status', $member) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold transition-all {{ $member->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                                                {{ $member->is_active ? 'Ativo' : 'Inativo' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button @click="openObservations({{ Js::from($member) }})"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-orange-300 hover:text-orange-700 hover:bg-orange-50"
                                                title="Observações">
                                                <i class="bi bi-chat-dots text-xs"></i>
                                            </button>
                                            <button @click="openTransferMember({{ Js::from($member) }})"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-amber-300 hover:text-amber-700 hover:bg-amber-50"
                                                title="Transferir Célula">
                                                <i class="bi bi-arrow-left-right text-xs"></i>
                                            </button>
                                            <a href="{{ route('users.show', $member) }}"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-gray-300 hover:text-gray-900 hover:bg-gray-50"
                                                title="Ver perfil">
                                                <i class="bi bi-person text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-400 italic">
                                        Nenhum membro registrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($members->hasPages())
                    <div class="border-t border-gray-200 px-4 py-2.5 bg-gray-50">
                        {{ $members->links() }}
                    </div>
                @endif

                <!-- Transfer Member Modal -->
                <div x-show="showTransferMemberModal"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;"
                    x-cloak>
                    <div @click.away="showTransferMemberModal = false"
                        class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/60">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Transferir Membro</h3>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="'Mover ' + selectedMember.name + ' para outra célula'"></p>
                        </div>
                        <form :action="'{{ url('/admin/users') }}/' + selectedMember.id + '/reassign-cell'"
                            method="POST" class="p-5 space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700">Selecione a Célula de Destino</label>
                                <select name="cell_id" required
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Escolha uma célula...</option>
                                    @foreach($cells as $availCell)
                                        <option value="{{ $availCell->id }}">{{ $availCell->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                                <button type="button" @click="showTransferMemberModal = false"
                                    class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancelar</button>
                                <button type="submit"
                                    class="rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">Confirmar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Member Observations Modal -->
                <div x-show="showObservationsModal"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;"
                    x-cloak>
                    <div @click.away="showObservationsModal = false"
                        class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/60">
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Observações</h3>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="'Notas sobre ' + selectedMember.name"></p>
                        </div>
                        <form :action="'{{ url('/admin/users') }}/' + selectedMember.id + '/observations'" method="POST"
                            class="p-5 space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-gray-700">Conteúdo das Notas</label>
                                <textarea name="observations" x-model="memberObservations" rows="4"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 resize-none"
                                    placeholder="Escreva observações importantes aqui..."></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                                <button type="button" @click="showObservationsModal = false"
                                    class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancelar</button>
                                <button type="submit"
                                    class="rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">Salvar Notas</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transfer Supervision Modal -->
        <div x-show="showTransferModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;" x-cloak>
            <div @click.away="showTransferModal = false"
                class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/60">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Transferir Supervisão</h3>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="'Mover ' + selectedSupervision.name + ' para outra zona'"></p>
                </div>
                <form :action="'{{ url('/admin/supervisions') }}/' + selectedSupervision.id + '/reassign-zone'"
                    method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700">Selecione a Zona de Destino</label>
                        <select name="zone_id" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Escolha uma zona...</option>
                            @foreach($availableZones as $availZone)
                                <option value="{{ $availZone->id }}">{{ $availZone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" @click="showTransferModal = false"
                            class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancelar</button>
                        <button type="submit"
                            class="rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">Confirmar Mudança</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
