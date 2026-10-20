@extends('layouts.app')

@section('title', 'Lista de Membros - Portal Life Church')
@section('page-title', 'Gestão de Membros')

@section('page-subtitle')
    @if($userRole === 'lider_celula')
        Membros da sua célula
    @elseif($userRole === 'supervisor')
        Membros da sua supervisão
    @elseif($userRole === 'pastor_zona')
        Membros da sua zona
    @else
        Todos os membros da igreja
    @endif
@endsection

@section('header-actions')
    @if($userRole !== 'secretaria')
        <a href="{{ route('members.create') }}"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-orange-700 text-white hover:bg-orange-800 shadow-sm transition-colors"
            title="Novo Membro">
            <i class="bi bi-person-plus-fill text-sm"></i>
        </a>
    @endif
@endsection

@section('content')
    <div class="space-y-4"
        x-data="{
            view: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('members_view') || 'list'),
            selected: [],
            showAssignCellModal: false,
            selectedMember: { id: null, name: '' },
            toggleAll() {
                const allIds = {{ Js::from($members->pluck('id')->values()) }};
                this.selected = this.selected.length === allIds.length ? [] : allIds;
            },
            openAssignCellModal(member) {
                this.selectedMember = member;
                this.showAssignCellModal = true;
            }
        }"
        x-init="$watch('view', value => localStorage.setItem('members_view', value))">

        <!-- Header da Página (Desktop) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Membros & Discipulado</span>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white tracking-tight">Gestão de Membros</h1>
                <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium">Cadastro, células e acompanhamento dos membros da igreja.</p>
            </div>
            @if($userRole !== 'secretaria')
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('members.create') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                        <i class="bi bi-person-plus-fill text-xs"></i>
                        <span>Novo Membro</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Total de Membros</p>
                    <p class="mt-1 text-2xl font-black text-gray-900 tracking-tight">{{ $members->total() }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-people text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Ativos (nesta pág.)</p>
                    <p class="mt-1 text-2xl font-black text-emerald-700 tracking-tight">{{ $members->getCollection()->where('is_active', true)->count() }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <i class="bi bi-check2-circle text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Células Cobertas</p>
                    <p class="mt-1 text-2xl font-black text-blue-700 tracking-tight">{{ $availableCells->count() }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                    <i class="bi bi-house-door text-lg"></i>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Líderes de Célula</p>
                    <p class="mt-1 text-2xl font-black text-purple-700 tracking-tight">{{ $members->getCollection()->where('role', 'lider_celula')->count() }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-700">
                    <i class="bi bi-person-badge text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('members.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 items-end">
                    <div class="sm:col-span-1 lg:col-span-6">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Pesquisar</label>
                        <div class="relative">
                            <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, email ou telefone..."
                                class="w-full rounded-lg border-gray-300 pl-7 pr-3 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>

                    @if($userRole !== 'lider_celula' && $availableCells->count() > 1)
                        <div class="sm:col-span-1 lg:col-span-4">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Célula</label>
                            <select name="cell_id" class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Todas as Células</option>
                                @foreach($availableCells as $cell)
                                    <option value="{{ $cell->id }}" {{ request('cell_id') == $cell->id ? 'selected' : '' }}>
                                        {{ $cell->name }} ({{ $cell->type_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="lg:col-span-2 flex items-center gap-1.5">
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-orange-800 transition-colors shadow-sm">
                            <i class="bi bi-filter text-xs"></i> Filtrar
                        </button>
                        @if(request('search') || request('cell_id'))
                            <a href="{{ route('members.index') }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                                <i class="bi bi-x-lg text-xs"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <span class="text-xs text-gray-500 font-semibold">{{ $members->total() }} membros encontrados</span>
                    <div class="flex items-center rounded-lg border border-gray-200 p-0.5 bg-gray-50">
                        <button type="button" @click="view = 'list'"
                            :class="view === 'list' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-900'"
                            class="rounded-md px-2 py-1 text-xs transition-colors">
                            <i class="bi bi-list-ul"></i>
                        </button>
                        <button type="button" @click="view = 'grid'"
                            :class="view === 'grid' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-900'"
                            class="rounded-md px-2 py-1 text-xs transition-colors">
                            <i class="bi bi-grid-fill"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-[10px] uppercase font-black tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2.5 w-8">
                                <input type="checkbox" @click="toggleAll" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            </th>
                            <th class="px-4 py-2.5">Nome / Identificação</th>
                            <th class="px-4 py-2.5">Cargo / Papel</th>
                            <th class="px-4 py-2.5">Contacto</th>
                            <th class="px-4 py-2.5">Célula / Zona</th>
                            <th class="px-4 py-2.5 text-center">Estado</th>
                            <th class="px-4 py-2.5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($members as $member)
                            <tr class="hover:bg-gray-50/70 transition-colors {{ $member->role === 'lider_celula' ? 'bg-purple-50/20' : '' }}"
                                :class="selected.includes({{ $member->id }}) ? 'bg-orange-50/40' : ''">
                                <td class="px-4 py-2.5">
                                    <input type="checkbox" value="{{ $member->id }}" x-model="selected"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                </td>
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 items-center justify-center rounded-lg font-bold text-xs shrink-0 {{ $member->role === 'lider_celula' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('members.show', $member) }}" class="font-bold text-gray-900 hover:text-orange-700 transition-colors">
                                                {{ $member->name }}
                                            </a>
                                            <span class="text-[10px] text-gray-400 block">#{{ $member->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($member->role === 'lider_celula')
                                        <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-0.5 text-[10px] font-bold text-purple-700 border border-purple-200">Líder</span>
                                    @elseif($member->role === 'timoteo')
                                        <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-bold text-orange-700 border border-orange-200">Timóteo</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 border border-blue-200">Membro</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-gray-900 block truncate max-w-xs">{{ $member->email }}</span>
                                    <span class="text-[11px] text-gray-400 block">{{ $member->phone ?: 'Sem telefone' }}</span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="font-semibold text-gray-800 block truncate">{{ $member->cell->name ?? 'Sem célula' }}</span>
                                    <span class="text-[10px] text-gray-400 block truncate">{{ $member->cell->supervision->zone->name ?? '' }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    @if($member->is_active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Ativo</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-bold text-red-600">Inativo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('members.show', $member) }}"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-gray-300 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                                            title="Ver perfil">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                        @if($userRole !== 'secretaria' && $member->id !== auth()->user()->id)
                                            <a href="{{ route('members.edit', $member) }}"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-orange-300 hover:text-orange-700 hover:bg-orange-50 transition-colors"
                                                title="Editar">
                                                <i class="bi bi-pencil-square text-xs"></i>
                                            </a>
                                            <form method="POST" action="{{ route('members.destroy', $member) }}" onsubmit="return confirm('Tem certeza que deseja excluir este membro?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                    title="Eliminar">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            </form>
                                            @if(is_null($member->cell_id))
                                                <button @click="openAssignCellModal({ id: {{ $member->id }}, name: '{{ addslashes($member->name) }}' })"
                                                    title="Atribuir Célula"
                                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-emerald-200 text-emerald-700 hover:bg-emerald-50 transition-colors">
                                                    <i class="bi bi-people-fill text-xs"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400 italic">
                                    Nenhum membro encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" style="display: none;" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @forelse($members as $member)
                <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm hover:border-orange-300 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider {{ $member->role === 'lider_celula' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ $member->role === 'lider_celula' ? 'Líder' : ($member->role === 'timoteo' ? 'Timóteo' : 'Membro') }}
                            </span>
                            <span class="inline-flex items-center rounded-full px-1.5 py-0.2 text-[9px] font-bold {{ $member->is_active ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ $member->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 truncate">{{ $member->name }}</h4>
                        <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ $member->cell->name ?? 'Sem célula' }}</p>
                        <p class="text-[10px] text-gray-400 truncate mt-0.5">{{ $member->phone ?: $member->email }}</p>
                    </div>

                    <div class="flex items-center gap-1.5 pt-3 border-t border-gray-100 mt-3">
                        <a href="{{ route('members.show', $member) }}"
                            class="flex-1 text-center py-1 rounded-md bg-gray-50 text-gray-700 hover:bg-gray-100 text-xs font-bold border border-gray-200">
                            Ver
                        </a>
                        @if($userRole !== 'secretaria' && $member->id !== auth()->user()->id)
                            <a href="{{ route('members.edit', $member) }}"
                                class="inline-flex h-6 w-6 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:text-orange-700">
                                <i class="bi bi-pencil text-[11px]"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-200 p-8 text-center text-xs text-gray-400 italic">
                    Nenhum membro encontrado.
                </div>
            @endforelse
        </div>

        @if($members->hasPages())
            <div class="border-t border-gray-200 pt-3">
                {{ $members->appends(request()->query())->links() }}
            </div>
        @endif

        <!-- Floating Bulk Bar -->
        <div x-show="selected.length > 0" x-cloak
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 rounded-xl bg-gray-900 text-white px-4 py-2.5 shadow-xl flex items-center gap-3 border border-gray-700 text-xs">
            <span class="font-bold"><span x-text="selected.length"></span> selecionado(s)</span>
            <div class="h-4 w-px bg-gray-700"></div>
            @if($userRole !== 'secretaria')
                <form method="POST" action="{{ route('members.bulk-destroy') }}" onsubmit="return confirm('Tem certeza que deseja excluir os membros selecionados?')" class="inline">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="selected_ids[]" :value="id">
                    </template>
                    <button type="submit" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-2.5 py-1 text-xs font-bold text-white hover:bg-red-700">
                        <i class="bi bi-trash text-xs"></i> Excluir
                    </button>
                </form>
            @endif
            <button @click="selected = []" class="text-gray-400 hover:text-white ml-1">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Assign Cell Modal -->
        <div x-show="showAssignCellModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;" x-cloak>
            <div @click.away="showAssignCellModal = false" class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/60 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Atribuir a Célula</h3>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="'Membro: ' + selectedMember.name"></p>
                    </div>
                    <button @click="showAssignCellModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>
                
                <form :action="'{{ url('/members') }}/' + selectedMember.id + '/assign-cell'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700">Selecione a Célula de Destino</label>
                        <select name="cell_id" required class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Escolha uma célula...</option>
                            @foreach($availableCells as $cell)
                                <option value="{{ $cell->id }}">{{ $cell->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" @click="showAssignCellModal = false" class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">Confirmar Atribuição</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
