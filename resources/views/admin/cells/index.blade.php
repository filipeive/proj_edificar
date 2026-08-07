@extends('layouts.app')

@section('title', 'Gestão de Células - Portal Life Church')
@section('page-title', 'Gestão de Células')
@section('page-subtitle', 'Controle de liderança, membros e expansão das células')

@section('header-actions')
    <div class="md:hidden">
        <a href="{{ route('cells.create') }}"
            class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
            <i class="bi bi-plus-circle-fill text-2xl"></i>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-8" 
        x-data="{ 
            view: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('cells_view') || 'grid'),
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
            <div class="bg-gray-900 text-white rounded-2xl shadow-2xl p-4 flex items-center gap-6 pointer-events-auto border border-gray-700/50 backdrop-blur-md bg-opacity-90">
                <div class="flex items-center gap-3 pl-2">
                    <span class="bg-blue-600 text-xs font-black px-2.5 py-1 rounded-lg" x-text="selected.length"></span>
                    <span class="text-sm font-medium">selecionados</span>
                </div>
                <div class="h-8 w-px bg-gray-700"></div>
                <div class="flex items-center gap-2">
                    <button @click="selected = []" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button @click="confirmDelete('bulk-delete-form', 'Deseja excluir as ' + selected.length + ' células selecionadas? Apenas células sem membros serão excluídas.')"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-red-600/20 flex items-center gap-2">
                        <i class="bi bi-trash-fill"></i> Excluir
                    </button>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 transition-all">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <span>Comunhão</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">Gestão de Células</h1>
                <p class="text-gray-500 font-medium">Controle de liderança, membros e expansão das células</p>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                {{-- View Switcher --}}
                <div class="hidden md:flex bg-gray-100/50 p-1.5 rounded-2xl border border-gray-100">
                    <button @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-xl transition-all duration-300 flex items-center gap-2">
                        <i class="bi bi-grid-fill text-sm"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Cards</span>
                    </button>
                    <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                        class="px-4 py-2 rounded-xl transition-all duration-300 flex items-center gap-2">
                        <i class="bi bi-list-ul text-sm"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest leading-none">Lista</span>
                    </button>
                </div>

                <div class="hidden md:flex gap-3">
                    <a href="{{ route('cells.create') }}" class="group flex items-center bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-200">
                        <i class="bi bi-plus-circle text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                        Nova Célula
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters Panel -->
        <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <form action="{{ route('cells.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[300px] space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Pesquisar</label>
                    <div class="relative group">
                        <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                        <input type="text" name="search" data-live-search="manual" value="{{ request('search') }}"
                            placeholder="Célula, líder ou zona..."
                            class="w-full pl-12 pr-6 py-3 bg-white border-transparent focus:ring-4 focus:ring-blue-100 rounded-xl text-sm font-bold transition-all"
                            @input.debounce.500ms="$el.form.submit()">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Zona</label>
                    <select name="zone" onchange="this.form.submit()"
                        class="searchable-select px-6 py-3 bg-white border-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl transition-all font-bold text-gray-700 text-sm min-w-[200px] custom-select" data-label="Zona">
                        <option value="">Todas as Zonas</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" {{ request('zone') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Supervisão</label>
                    <select name="supervision" onchange="this.form.submit()"
                        class="searchable-select px-6 py-3 bg-white border-transparent focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-xl transition-all font-bold text-gray-700 text-sm min-w-[220px] custom-select" data-label="Supervisão">
                        <option value="">Todas as Supervisões</option>
                        @foreach($supervisions as $supervision)
                            <option value="{{ $supervision->id }}" {{ request('supervision') == $supervision->id ? 'selected' : '' }}>
                                {{ $supervision->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(request()->hasAny(['search', 'zone', 'supervision']))
                    <div class="flex gap-2">
                        <a href="{{ route('cells.index') }}"
                            class="flex items-center gap-2 px-6 py-3 text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest">
                            <i class="bi bi-x-circle-fill"></i> Limpar
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
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($cells as $cell)
                    <div
                        class="group bg-white rounded-[2rem] p-7 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-100 relative compact-card"
                        :class="{'ring-2 ring-blue-500 bg-blue-50/10': selected.includes({{ $cell->id }})}">
                        
                        <div class="absolute top-4 left-4 z-20">
                            <input type="checkbox" value="{{ $cell->id }}" x-model="selected"
                                class="w-5 h-5 rounded-lg border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer shadow-sm">
                        </div>

                        <div class="flex items-start justify-between mb-8 ml-8">
                            <div class="flex items-center gap-5">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl font-black shadow-sm group-hover:scale-110 transition-transform duration-300">
                                    {{ substr($cell->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 leading-tight mb-1">{{ $cell->name }}</h3>
                                    <div
                                        class="flex items-center text-[10px] font-black text-gray-400 uppercase tracking-widest gap-2">
                                        <span
                                            class="bg-gray-50 px-2 py-1 rounded-lg text-blue-600">{{ $cell->supervision->name ?? 'Independente' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2 opacity-70 hover:opacity-100 transition-opacity">
                                <button @click="openAssignModal({ id: {{ $cell->id }}, name: '{{ $cell->name }}', members: {{ $cell->members->values() }} })"
                                    title="Atribuir Auxiliar/Timóteo"
                                    class="w-10 h-10 rounded-xl bg-orange-50 hover:bg-orange-600 text-orange-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                                <a href="{{ route('cells.edit', $cell) }}" title="Editar"
                                    class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-yellow-500 text-gray-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if($cell->members->count() == 0)
                                    <form action="{{ route('cells.destroy', $cell) }}" method="POST" id="grid-delete-cell-{{ $cell->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('grid-delete-cell-{{ $cell->id }}', 'Deseja excluir esta célula?')" title="Eliminar"
                                            class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button"
                                        onclick="Swal.fire({icon: 'warning', title: 'Não é possível eliminar', text: 'Esta célula possui membros associados. Remova ou transfira os membros antes de eliminar a célula.'})"
                                        title="Não é possível eliminar célula com membros"
                                        class="w-10 h-10 rounded-xl bg-red-50 text-red-300 flex items-center justify-center transition-all shadow-sm cursor-not-allowed opacity-75">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100">
                                <span
                                    class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2 block">Liderança</span>
                                <p class="text-sm font-black text-gray-700 truncate">
                                    {{ $cell->leader->name ?? 'Vago' }}
                                </p>
                            </div>
                            <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100 text-center">
                                <span
                                    class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2 block">Membros</span>
                                <p class="text-xl font-black text-gray-900">
                                    {{ $cell->members->count() }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-auto pt-6 border-t border-gray-50 space-y-4">
                            <div class="flex items-center justify-between px-2">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Membros</span>
                                <div class="flex -space-x-3">
                                    @foreach($cell->members->take(5) as $member)
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=EBF4FF&color=3B82F6&bold=true"
                                            class="w-10 h-10 rounded-full border-4 border-white shadow-sm ring-1 ring-gray-100"
                                            title="{{ $member->name }}">
                                    @endforeach
                                    @if($cell->members->count() > 5)
                                        <div
                                            class="w-10 h-10 rounded-full border-4 border-white bg-gray-100 flex items-center justify-center text-xs font-black text-gray-500 shadow-sm ring-1 ring-gray-100">
                                            +{{ $cell->members->count() - 5 }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('cells.show', $cell) }}"
                                class="flex items-center justify-center w-full py-4 rounded-2xl bg-gray-900 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-md group">
                                Aceder Dashboard
                                <i class="bi bi-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center bg-white rounded-[2.5rem] border border-gray-100 border-dashed">
                        <div
                            class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                            <i class="bi bi-people text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-2">Sem células registradas</h3>
                        <p class="text-gray-500 font-medium mb-8">Nenhuma célula atende aos critérios ou a base está vazia.</p>
                        <a href="{{ route('cells.create') }}"
                            class="inline-flex items-center bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-200">
                            <i class="bi bi-plus-lg mr-2"></i>
                            Adicionar Agora
                        </a>
                    </div>
                @endforelse
            </div>
        </template>

        <template x-if="view === 'list'">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden overflow-x-auto">
                <table class="w-full text-left table-compact">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-6 w-10">
                                <input type="checkbox" @click="toggleAll()"
                                    :checked="selected.length === {{ $cells->count() }} && selected.length > 0"
                                    class="w-5 h-5 rounded-lg border-gray-300 text-blue-600 cursor-pointer shadow-sm">
                            </th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Identificação da Célula</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Líder
                                Responsável</th>
                            <th class="px-6 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Estrutura
                            </th>
                            <th
                                class="px-6 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Membros</th>
                            <th
                                class="px-6 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Gestão</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($cells as $cell)
                            <tr class="hover:bg-gray-50/50 transition-colors group" :class="{'bg-blue-50/30': selected.includes({{ $cell->id }})}">
                                <td class="px-6 py-7">
                                    <input type="checkbox" value="{{ $cell->id }}" x-model="selected"
                                        class="w-5 h-5 rounded-lg border-gray-300 text-blue-600 cursor-pointer shadow-sm">
                                </td>
                                <td class="px-6 py-7">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-black">
                                            {{ substr($cell->name, 0, 1) }}
                                        </div>
                                        <p class="font-black text-gray-900 uppercase tracking-tight">{{ $cell->name }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-7">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($cell->leader->name ?? 'V') }}&background=F3F4F6&color=6B7280&bold=true"
                                            class="w-8 h-8 rounded-full shadow-sm">
                                        <p class="text-sm font-bold text-gray-700 leading-none">
                                            {{ $cell->leader->name ?? 'Não Atribuído' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-7">
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-black text-gray-900 uppercase tracking-tight leading-none">
                                            {{ $cell->supervision->name ?? 'Sem Supervisão' }}</p>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $cell->supervision->zone->name ?? 'Zonal Life' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-7 text-center font-black text-blue-600 text-lg tracking-tighter">
                                    {{ $cell->members->count() }}</td>
                                <td class="px-6 py-7 text-right">
                                    <div class="flex justify-end gap-2 opacity-70 hover:opacity-100 transition-all">
                                        <button @click="openAssignModal({ id: {{ $cell->id }}, name: '{{ $cell->name }}', members: {{ $cell->members->values() }} })"
                                            title="Atribuir Auxiliar/Timóteo"
                                            class="action-icon bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white">
                                            <i class="bi bi-person-plus"></i>
                                        </button>
                                        <a href="{{ route('cells.show', $cell) }}" title="Detalhes"
                                            class="action-icon bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white">
                                            <i class="bi bi-speedometer2"></i>
                                        </a>
                                        <a href="{{ route('cells.edit', $cell) }}" title="Editar"
                                            class="action-icon bg-gray-50 text-gray-400 hover:bg-yellow-500 hover:text-white">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        {{-- Eliminar Celula --}}
                                        @if($cell->members->count() == 0)
                                            <form action="{{ route('cells.destroy', $cell) }}" method="POST" id="list-delete-cell-{{ $cell->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('list-delete-cell-{{ $cell->id }}', 'Deseja excluir esta célula?')" title="Eliminar"
                                                    class="action-icon bg-red-50 text-red-600 hover:bg-red-600 hover:text-white">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button"
                                                onclick="Swal.fire({icon: 'warning', title: 'Não é possível eliminar', text: 'Esta célula possui membros associados. Remova ou transfira os membros antes de eliminar a célula.'})"
                                                title="Não é possível eliminar célula com membros"
                                                class="action-icon bg-red-50 text-red-300 cursor-not-allowed opacity-75">
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
            <div @click.away="showAssignModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-In">
                <div class="p-8 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 leading-tight">Gestão de Timóteos</h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="'Célula ' + selectedCell.name"></p>
                    </div>
                    <button @click="showAssignModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
                
                <div class="p-8">
                    <div class="space-y-6 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Secção: Timóteos Ativos -->
                        <div x-show="selectedCell.timoteos && selectedCell.timoteos.length > 0" class="space-y-3">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Timóteos Ativos</h4>
                            <template x-for="timoteo in selectedCell.timoteos" :key="timoteo.id">
                                <div class="flex items-center justify-between p-4 bg-orange-50/30 rounded-2xl border border-orange-100/50 transition-all group gap-2">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center font-bold flex-shrink-0" x-text="timoteo.name.substring(0, 1)"></div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-gray-900 truncate" x-text="timoteo.name"></p>
                                            <p class="text-[10px] text-gray-400 truncate" x-text="timoteo.email"></p>
                                        </div>
                                    </div>
                                    <form :action="'{{ url('/admin/cells') }}/' + selectedCell.id + '/remove-timoteo'" method="POST" class="flex-shrink-0">
                                        @csrf
                                        <input type="hidden" name="user_id" :value="timoteo.id">
                                        <button type="submit" 
                                            class="px-3.5 py-2 bg-white text-red-600 border border-red-100 rounded-xl text-[9px] font-black uppercase tracking-wider hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                            Remover
                                        </button>
                                    </form>
                                </div>
                            </template>
                        </div>

                        <!-- Secção: Disponíveis para Promoção -->
                        <div class="space-y-3">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Disponíveis para Promoção</h4>
                            <template x-for="member in selectedCell.members" :key="member.id">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-transparent hover:border-blue-200 transition-all group gap-2">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-blue-600 font-bold flex-shrink-0" x-text="member.name.substring(0, 1)"></div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-gray-900 truncate" x-text="member.name"></p>
                                            <p class="text-[10px] text-gray-400 truncate" x-text="member.email"></p>
                                        </div>
                                    </div>
                                    <form :action="'{{ url('/admin/cells') }}/' + selectedCell.id + '/assign-timoteo'" method="POST" class="flex-shrink-0">
                                        @csrf
                                        <input type="hidden" name="user_id" :value="member.id">
                                        <button type="submit" 
                                            class="px-3.5 py-2 bg-white text-orange-600 border border-orange-100 rounded-xl text-[9px] font-black uppercase tracking-wider hover:bg-orange-600 hover:text-white transition-all shadow-sm">
                                            Promover
                                        </button>
                                    </form>
                                </div>
                            </template>
                            <template x-if="selectedCell.members && selectedCell.members.length === 0">
                                <div class="text-center py-6 bg-gray-50 rounded-2xl">
                                    <p class="text-gray-400 italic text-xs">Nenhum membro comum nesta célula disponível para promoção.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <div class="p-8 bg-gray-50 border-t border-gray-100">
                    <p class="text-[10px] text-gray-400 font-medium leading-relaxed">
                        <i class="bi bi-info-circle mr-1"></i> A promoção a Timóteo concede ao membro as mesmas permissões operacionais do líder para esta célula.
                    </p>
                </div>
            </div>
        </div>

        <!-- Paginação -->
        @if($cells->hasPages())
            <div class="pt-4">
                {{ $cells->links() }}
            </div>
        @endif
    </div>
@endsection
