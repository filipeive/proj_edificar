@extends('layouts.app')

@section('title', 'Gestão de Células - Portal Life Church')
@section('page-title', 'Gestão de Células')
@section('page-subtitle', 'Controle de liderança, membros e expansão das células')

@section('header-actions')
    <div class="md:hidden">
        <a href="{{ route('cells.create') }}"
            class="inline-flex items-center justify-center rounded-lg bg-orange-700 p-2 text-white shadow-sm hover:bg-orange-800 transition">
            <i class="bi bi-plus-lg text-base"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-5" 
        x-data="{ 
            view: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('cells_view') || 'list'),
            selected: [],
            showAssignModal: false,
            selectedCell: { name: '', id: null, members: [], timoteos: [] },
            updateView() {
                if (window.innerWidth < 768 && this.view === 'list') {
                    this.view = 'grid';
                }
            },
            toggleAll() {
                const allIds = @json($cells->pluck('id'));
                if (this.selected.length === allIds.length) {
                    this.selected = [];
                } else {
                    this.selected = allIds;
                }
            },
            deleteSelected() {
                document.getElementById('bulk-delete-form').submit();
            },
            openAssignModal(cell) {
                this.selectedCell = {
                    id: cell.id,
                    name: cell.name,
                    members: (cell.members || []).filter(m => m.role === 'membro'),
                    timoteos: (cell.members || []).filter(m => m.role === 'timoteo')
                };
                this.showAssignModal = true;
            }
        }"
        x-init="$watch('view', value => localStorage.setItem('cells_view', value))"
        @resize.window.debounce.500ms="updateView()">

        <!-- Bulk Action Bar -->
        <div x-show="selected.length > 0" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="fixed top-24 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
            <div class="bg-gray-900 text-white rounded-xl shadow-lg p-3.5 flex items-center gap-4 pointer-events-auto border border-gray-700/50 backdrop-blur-md bg-opacity-90">
                <div class="flex items-center gap-2 pl-1">
                    <span class="bg-orange-700 text-xs font-black px-2 py-0.5 rounded-md" x-text="selected.length"></span>
                    <span class="text-xs font-medium">selecionados</span>
                </div>
                <div class="h-6 w-px bg-gray-700"></div>
                <div class="flex items-center gap-2">
                    <button @click="selected = []" class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button @click="confirmDelete('bulk-delete-form', 'Deseja excluir as ' + selected.length + ' células selecionadas? Apenas células sem membros serão excluídas.')"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider transition-all shadow-sm flex items-center gap-1.5">
                        <i class="bi bi-trash-fill"></i> Excluir
                    </button>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Comunhão & Estrutura</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Gestão de Células</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Controle de liderança, membros e expansão das células</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- View Switcher --}}
                <div class="hidden md:inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-sm">
                    <button type="button" @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold transition">
                        <i class="bi bi-list-ul"></i>
                        <span>Lista</span>
                    </button>
                    <button type="button" @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                        class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-bold transition">
                        <i class="bi bi-grid"></i>
                        <span>Cards</span>
                    </button>
                </div>

                <div class="hidden md:flex gap-2">
                    <a href="{{ route('cells.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm transition">
                        <i class="bi bi-plus-lg"></i>
                        <span>Nova Célula</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Filters Panel -->
        <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
            <form action="{{ route('cells.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[240px]">
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Pesquisar</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" data-live-search="manual" value="{{ request('search') }}"
                            placeholder="Célula, líder, supervisão ou zona..."
                            class="w-full pl-8 pr-3 py-2 text-xs sm:text-sm rounded-lg border border-gray-300 font-medium text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                            @input.debounce.500ms="$el.form.submit()">
                    </div>
                </div>

                <div class="min-w-[160px]">
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Zona</label>
                    <select name="zone" onchange="this.form.submit()"
                        class="searchable-select rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-800 custom-select" data-label="Zona">
                        <option value="">Todas as Zonas</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ request('zone') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[180px]">
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Supervisão</label>
                    <select name="supervision" onchange="this.form.submit()"
                        class="searchable-select rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-800 custom-select" data-label="Supervisão">
                        <option value="">Todas as Supervisões</option>
                        @foreach($supervisions as $supervision)
                            <option value="{{ $supervision->id }}" {{ request('supervision') == $supervision->id ? 'selected' : '' }}>
                                {{ $supervision->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[160px]">
                    <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Tipo</label>
                    <select name="type" onchange="this.form.submit()"
                        class="searchable-select rounded-lg border border-gray-300 px-3 py-2 text-xs sm:text-sm font-semibold text-gray-800 custom-select" data-label="Tipo">
                        <option value="">Todos os Tipos</option>
                        <option value="membros" {{ request('type') == 'membros' ? 'selected' : '' }}>Membros</option>
                        <option value="lideres" {{ request('type') == 'lideres' ? 'selected' : '' }}>Líderes</option>
                        <option value="supervisores" {{ request('type') == 'supervisores' ? 'selected' : '' }}>Supervisores</option>
                        <option value="pastores_zona" {{ request('type') == 'pastores_zona' ? 'selected' : '' }}>Pastores de Zona</option>
                        <option value="pastores" {{ request('type') == 'pastores' ? 'selected' : '' }}>Pastores</option>
                    </select>
                </div>

                @if(request()->hasAny(['search', 'zone', 'supervision', 'type']))
                    <div class="flex gap-2">
                        <a href="{{ route('cells.index') }}"
                            class="inline-flex items-center justify-center gap-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 transition">
                            <i class="bi bi-x-circle"></i> Limpar
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <form id="bulk-delete-form" action="{{ route('cells.bulk-destroy') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
            <template x-for="id in selected">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </form>

        <!-- Grid View -->
        <template x-if="view === 'grid'">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                @forelse($cells as $cell)
                    <div
                        class="group rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm hover:border-orange-300 transition-all relative flex flex-col"
                        :class="{'ring-2 ring-orange-500 bg-orange-50/10': selected.includes({{ $cell->id }})}">
                        
                        <div class="absolute top-3.5 left-3.5 z-20">
                            <input type="checkbox" value="{{ $cell->id }}" x-model="selected"
                                class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500 cursor-pointer">
                        </div>

                        <div class="flex items-start justify-between mb-4 ml-6">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-10 h-10 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center text-base font-black shrink-0">
                                    {{ substr($cell->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-black text-gray-950 leading-tight truncate">{{ $cell->name }}</h3>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                        <span class="inline-block px-2 py-0.5 rounded border text-[9px] font-black uppercase tracking-wider {{ $cell->type_badge_classes }}">
                                            {{ $cell->type_label }}
                                        </span>
                                        <span class="bg-gray-100 px-1.5 py-0.5 rounded text-[9px] font-bold text-gray-600 truncate">{{ $cell->supervision->name ?? 'Independente' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <button @click="openAssignModal({ id: {{ $cell->id }}, name: '{{ $cell->name }}', members: {{ $cell->members->values() }} })"
                                    title="Atribuir Auxiliar/Timóteo"
                                    class="w-7 h-7 rounded-lg bg-orange-50 hover:bg-orange-600 text-orange-600 hover:text-white flex items-center justify-center text-xs transition-all">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                                @if(!auth()->user()->isLider())
                                    <a href="{{ route('cells.edit', $cell) }}" title="Editar"
                                        class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-blue-600 text-gray-600 hover:text-white flex items-center justify-center text-xs transition-all">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endif
                                @if($cell->members->count() == 0)
                                    <form action="{{ route('cells.destroy', $cell) }}" method="POST" id="grid-delete-cell-{{ $cell->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('grid-delete-cell-{{ $cell->id }}', 'Deseja excluir esta célula?')" title="Eliminar"
                                            class="w-7 h-7 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center text-xs transition-all">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button"
                                        onclick="Swal.fire({icon: 'warning', title: 'Não é possível eliminar', text: 'Esta célula possui membros associados. Remova ou transfira os membros antes de eliminar a célula.'})"
                                        title="Não é possível eliminar célula com membros"
                                        class="w-7 h-7 rounded-lg bg-red-50 text-red-300 flex items-center justify-center text-xs cursor-not-allowed opacity-75">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5 mb-4">
                            <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                <span class="text-[9px] font-black uppercase tracking-wider text-gray-400 block">Liderança</span>
                                <p class="text-xs font-bold text-gray-800 truncate mt-0.5">
                                    {{ $cell->leader->name ?? 'Vago' }}
                                </p>
                            </div>
                            <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100 text-center">
                                <span class="text-[9px] font-black uppercase tracking-wider text-gray-400 block">Membros</span>
                                <p class="text-base font-black text-gray-950 mt-0.5 tabular-nums">
                                    {{ $cell->members->count() }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-auto pt-3 border-t border-gray-100 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Membros</span>
                                <div class="flex -space-x-2">
                                    @foreach($cell->members->take(5) as $member)
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=EBF4FF&color=3B82F6&bold=true"
                                            class="w-6 h-6 rounded-full border-2 border-white shadow-sm"
                                            title="{{ $member->name }}">
                                    @endforeach
                                    @if($cell->members->count() > 5)
                                        <div
                                            class="w-6 h-6 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-[9px] font-black text-gray-600 shadow-sm">
                                            +{{ $cell->members->count() - 5 }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('cells.show', $cell) }}"
                                class="flex items-center justify-center w-full py-2 rounded-lg bg-gray-900 text-white font-bold text-xs hover:bg-orange-700 transition-all shadow-sm group">
                                Aceder Dashboard
                                <i class="bi bi-arrow-right ml-1.5 group-hover:translate-x-0.5 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                            <i class="bi bi-people text-2xl"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 mb-1">Sem células registradas</h3>
                        <p class="text-xs text-gray-500 mb-4">Nenhuma célula atende aos critérios ou a base está vazia.</p>
                        <a href="{{ route('cells.create') }}"
                            class="inline-flex items-center bg-orange-700 text-white px-4 py-2 rounded-lg hover:bg-orange-800 transition-all font-bold text-xs shadow-sm">
                            <i class="bi bi-plus-lg mr-1.5"></i>
                            Adicionar Agora
                        </a>
                    </div>
                @endforelse
            </div>
        </template>

        <template x-if="view === 'list'">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 w-9">
                                <input type="checkbox" @click="toggleAll()"
                                    :checked="selected.length === {{ $cells->count() }} && selected.length > 0"
                                    class="w-4 h-4 rounded border-gray-300 text-orange-600 cursor-pointer">
                            </th>
                            <th class="px-4 py-3">Identificação da Célula</th>
                            <th class="px-4 py-3">Líder Responsável</th>
                            <th class="px-4 py-3">Estrutura</th>
                            <th class="px-4 py-3 text-center">Tipo</th>
                            <th class="px-4 py-3 text-right">Membros</th>
                            <th class="px-4 py-3 text-right">Gestão</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($cells as $cell)
                            <tr class="hover:bg-gray-50/80 transition-colors group" :class="{'bg-orange-50/30': selected.includes({{ $cell->id }})}">
                                <td class="px-4 py-3">
                                    <input type="checkbox" value="{{ $cell->id }}" x-model="selected"
                                        class="w-4 h-4 rounded border-gray-300 text-orange-600 cursor-pointer">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center text-xs font-black shrink-0">
                                            {{ substr($cell->name, 0, 1) }}
                                        </div>
                                        <a href="{{ route('cells.show', $cell) }}" class="font-bold text-gray-900 hover:text-orange-700 hover:underline">{{ $cell->name }}</a>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($cell->leader->name ?? 'V') }}&background=F3F4F6&color=6B7280&bold=true"
                                            class="w-6 h-6 rounded-full">
                                        <p class="text-xs font-semibold text-gray-700">
                                            {{ $cell->leader->name ?? 'Não Atribuído' }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="text-xs font-bold text-gray-900 leading-tight">
                                            {{ $cell->supervision->name ?? 'Sem Supervisão' }}</p>
                                        <span class="text-[11px] text-gray-500">{{ $cell->supervision->zone->name ?? 'Zonal Life' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded border text-[10px] font-black uppercase tracking-wider {{ $cell->type_badge_classes }}">
                                        {{ $cell->type_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-black text-gray-900 tabular-nums">
                                    {{ $cell->members->count() }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        <button @click="openAssignModal({ id: {{ $cell->id }}, name: '{{ $cell->name }}', members: {{ $cell->members->values() }} })"
                                            title="Atribuir Auxiliar/Timóteo"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-orange-600 hover:border-orange-300 hover:bg-orange-50 transition">
                                            <i class="bi bi-person-plus"></i>
                                        </button>
                                        <a href="{{ route('cells.show', $cell) }}" title="Detalhes"
                                            class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:border-orange-300 hover:text-orange-800 hover:bg-orange-50/50 transition">
                                            <i class="bi bi-eye text-orange-700"></i> <span class="hidden xl:inline">Ver</span>
                                        </a>
                                        @if(!auth()->user()->isLider())
                                            <a href="{{ route('cells.edit', $cell) }}" title="Editar"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-gray-600 hover:border-blue-300 hover:text-blue-700 transition">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        {{-- Eliminar Celula --}}
                                        @if($cell->members->count() == 0)
                                            <form action="{{ route('cells.destroy', $cell) }}" method="POST" id="list-delete-cell-{{ $cell->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('list-delete-cell-{{ $cell->id }}', 'Deseja excluir esta célula?')" title="Eliminar"
                                                    class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-xs text-gray-500 hover:border-red-300 hover:text-red-600 transition">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button"
                                                onclick="Swal.fire({icon: 'warning', title: 'Não é possível eliminar', text: 'Esta célula possui membros associados. Remova ou transfira os membros antes de eliminar a célula.'})"
                                                title="Não é possível eliminar célula com membros"
                                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 p-1.5 text-xs text-red-300 cursor-not-allowed opacity-75">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </template>

        <!-- Assign Timoteo Modal -->
        <div x-show="showAssignModal" 
            class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none; margin-top: -15px">
            <div @click.away="showAssignModal = false" class="bg-white rounded-xl border border-gray-200 shadow-lg w-full max-w-lg overflow-hidden animate-In">
                <div class="p-4 sm:p-5 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-black text-gray-900 leading-tight">Gestão de Timóteos</h3>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="'Célula ' + selectedCell.name"></p>
                    </div>
                    <button @click="showAssignModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg text-base"></i>
                    </button>
                </div>
                
                <div class="p-4 sm:p-5">
                    <div class="space-y-4 max-h-[360px] overflow-y-auto pr-1 custom-scrollbar">
                        <!-- Secção: Timóteos Ativos -->
                        <div x-show="selectedCell.timoteos && selectedCell.timoteos.length > 0" class="space-y-2">
                            <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Timóteos Ativos</h4>
                            <template x-for="timoteo in selectedCell.timoteos" :key="timoteo.id">
                                <div class="flex items-center justify-between p-3 bg-orange-50/40 rounded-lg border border-orange-200/60 transition-all group gap-2">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center text-xs font-bold flex-shrink-0" x-text="timoteo.name.substring(0, 1)"></div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-900 truncate" x-text="timoteo.name"></p>
                                            <p class="text-[10px] text-gray-500 truncate" x-text="timoteo.email"></p>
                                        </div>
                                    </div>
                                    <form :action="'{{ url('/admin/cells') }}/' + selectedCell.id + '/remove-timoteo'" method="POST" class="flex-shrink-0">
                                        @csrf
                                        <input type="hidden" name="user_id" :value="timoteo.id">
                                        <button type="submit" 
                                            class="px-3 py-1.5 bg-white text-red-600 border border-red-200 rounded-lg text-[10px] font-bold uppercase tracking-wider hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                            Remover
                                        </button>
                                    </form>
                                </div>
                            </template>
                        </div>

                        <!-- Secção: Disponíveis para Promoção -->
                        <div class="space-y-2">
                            <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-wider">Disponíveis para Promoção</h4>
                            <template x-for="member in selectedCell.members" :key="member.id">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-orange-300 transition-all group gap-2">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-orange-700 text-xs font-bold flex-shrink-0" x-text="member.name.substring(0, 1)"></div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-900 truncate" x-text="member.name"></p>
                                            <p class="text-[10px] text-gray-500 truncate" x-text="member.email"></p>
                                        </div>
                                    </div>
                                    <form :action="'{{ url('/admin/cells') }}/' + selectedCell.id + '/assign-timoteo'" method="POST" class="flex-shrink-0">
                                        @csrf
                                        <input type="hidden" name="user_id" :value="member.id">
                                        <button type="submit" 
                                            class="px-3 py-1.5 bg-white text-orange-700 border border-orange-200 rounded-lg text-[10px] font-bold uppercase tracking-wider hover:bg-orange-700 hover:text-white transition-all shadow-sm">
                                            Promover
                                        </button>
                                    </form>
                                </div>
                            </template>
                            <template x-if="selectedCell.members && selectedCell.members.length === 0">
                                <div class="text-center py-6 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-gray-400 italic text-xs">Nenhum membro comum nesta célula disponível para promoção.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    <p class="text-[11px] text-gray-500 font-medium leading-relaxed">
                        <i class="bi bi-info-circle mr-1"></i> A promoção a Timóteo concede ao membro as mesmas permissões operacionais do líder para esta célula.
                    </p>
                </div>
            </div>
        </div>

        <!-- Paginação -->
        @if($cells->hasPages())
            <div class="pt-2">
                {{ $cells->links() }}
            </div>
        @endif
    </div>
@endsection
