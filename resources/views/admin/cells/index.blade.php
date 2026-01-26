@extends('layouts.app')

@section('title', 'Gestão de Células - Portal Life Church')

@section('content')
@section('header-actions')
    <a href="{{ route('cells.create') }}"
        class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
        <i class="bi bi-plus-circle-fill text-2xl"></i>
    </a>
@endsection

@section('content')
    <div class="space-y-8" 
        x-data="{ 
            view: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('cells_view') || 'grid'),
            selected: [],
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
                    <button @click="if(confirm('Deseja excluir as ' + selected.length + ' células selecionadas?')) deleteSelected()"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-red-600/20 flex items-center gap-2">
                        <i class="bi bi-trash-fill"></i> Excluir
                    </button>
                </div>
            </div>
        </div>
        <!-- Header -->
        <div class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col lg:flex-row justify-between items-center gap-6 transition-all">
            <div class="flex-1 w-full lg:max-w-md relative group">
                <form action="{{ route('cells.index') }}" method="GET" class="contents">
                    <i class="bi bi-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Pesquisar célula, líder ou zona..."
                        class="w-full pl-14 pr-6 py-4 bg-gray-50/50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all"
                        @input.debounce.500ms="$el.form.submit()">
                </form>
            </div>

            <div class="flex flex-wrap items-center justify-center md:justify-end gap-4 w-full lg:w-auto">
                {{-- View Switcher --}}
                <div class="flex bg-gray-100/50 p-1.5 rounded-2xl border border-gray-100">
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

                <a href="{{ route('cells.create') }}" class="group flex items-center bg-blue-600 text-white px-8 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-200">
                    <i class="bi bi-plus-circle text-lg mr-2 group-hover:scale-110 transition-transform"></i>
                    Nova Célula
                </a>
            </div>
        </div>

        <!-- Filters Panel -->
        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100">
            <form action="{{ route('cells.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                
                @if(count($zones) > 1)
                    <div class="w-full md:w-64">
                        <select name="zone" onchange="this.form.submit()"
                            class="w-full px-6 py-4 bg-gray-50/50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                            <option value="">Todas as Zonas</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ request('zone') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="w-full md:w-64">
                    <select name="supervision" onchange="this.form.submit()"
                        class="w-full px-6 py-4 bg-gray-50/50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700">
                        <option value="">Todas as Supervisões</option>
                        @foreach($supervisions as $supervision)
                            <option value="{{ $supervision->id }}" {{ request('supervision') == $supervision->id ? 'selected' : '' }}>
                                {{ $supervision->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(request()->hasAny(['search', 'zone', 'supervision']))
                    <a href="{{ route('cells.index') }}"
                        class="flex items-center gap-2 px-6 py-4 text-red-600 bg-red-50 hover:bg-red-100 rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest ml-auto">
                        <i class="bi bi-x-circle-fill"></i> Limpar Filtros
                    </a>
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
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($cells as $cell)
                    <div
                        class="group bg-white rounded-[2rem] p-7 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-100 relative"
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
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('cells.edit', $cell) }}"
                                    class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-400 hover:text-blue-600 flex items-center justify-center transition-all shadow-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
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

        <!-- List View -->
        <template x-if="view === 'list'">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden overflow-x-auto">
                <table class="w-full text-left">
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
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <a href="{{ route('cells.show', $cell) }}"
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                            <i class="bi bi-speedometer2"></i>
                                        </a>
                                        <a href="{{ route('cells.edit', $cell) }}"
                                            class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-yellow-500 hover:text-white transition-all">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </template>

        <!-- Paginação -->
        @if($cells->hasPages())
            <div class="pt-4">
                {{ $cells->links() }}
            </div>
        @endif
    </div>
@endsection