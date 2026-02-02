@extends('layouts.app')

@section('title', 'Encontros de Célula - Portal Life Church')
@section('page-title', 'Encontros de Célula')
@section('page-subtitle', 'Gestão de Reuniões e Atas')

@section('header-actions')
    <div class="flex items-center gap-3 md:hidden">
        <a href="{{ route('cell-meetings.export', request()->all()) }}"
            class="flex items-center gap-2 px-4 md:px-6 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-black text-[10px] uppercase tracking-widest rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm"
            title="Exportar para Excel">
            <i class="bi bi-file-earmark-excel-fill text-xl md:text-2xl"></i>
            <span class="hidden md:inline">Exportar</span>
        </a>

        @if(auth()->user()->isAdmin() || auth()->user()->can('create', App\Models\CellMeeting::class))
            <a href="{{ route('cell-meetings.create') }}"
                class="flex items-center gap-2 px-4 md:px-6 py-2.5 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20"
                title="Novo Encontro">
                <i class="bi bi-plus-lg text-xl md:text-2xl"></i>
                <span class="hidden md:inline">Novo Encontro</span>
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="space-y-8" 
        x-data="{ 
            view: window.innerWidth < 768 ? 'grid' : 'list',
            selectedIds: [],
            allSelected: false,
            updateView() {
                if (window.innerWidth < 768 && this.view === 'list') {
                    this.view = 'grid';
                }
            },
            toggleSelectAll() {
                this.allSelected = !this.allSelected;
                if (this.allSelected) {
                    this.selectedIds = Array.from(document.querySelectorAll('.item-checkbox')).map(cb => cb.value);
                } else {
                    this.selectedIds = [];
                }
            },
            toggleSelection(id) {
                if (this.selectedIds.includes(id)) {
                    this.selectedIds = this.selectedIds.filter(i => i !== id);
                    this.allSelected = false;
                } else {
                    this.selectedIds.push(id);
                    if (this.selectedIds.length === document.querySelectorAll('.item-checkbox').length) {
                        this.allSelected = true;
                    }
                }
            }
        }"
        x-init="$watch('view', value => localStorage.setItem('cell_meetings_view', value)); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('cell_meetings_view') || 'list')"
        @resize.window.debounce.500ms="updateView()">
         <!-- Stats Overview -->
        @if(isset($stats))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 hidden md:flex">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-lg group">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Encontros</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ $stats['total_meetings'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-lg group">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-2xl group-hover:bg-emerald-600 group-hover:text-white transition-all">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Participação Total</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ number_format($stats['total_attendance'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-lg group">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center text-2xl group-hover:bg-purple-600 group-hover:text-white transition-all">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Média / Encontro</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ $stats['avg_attendance'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-lg group">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/30 text-red-600 flex items-center justify-center text-2xl group-hover:bg-red-600 group-hover:text-white transition-all">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Conversões</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ $stats['total_decisions'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- Filter Bar -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
            <form action="{{ route('cell-meetings.index') }}" method="GET" class="flex flex-col xl:flex-row xl:items-end gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 flex-1">
                    <div class="text-black dark:text-white">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Pesquisar</label>
                        <div class="relative group">
                            <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tema, líder ou célula..."
                                class="w-full pl-14 pr-6 py-4 bg-gray-50/50 dark:bg-gray-900/50 border-transparent focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 rounded-2xl font-bold text-sm transition-all text-black dark:text-white">
                        </div>
                    </div>

                    <div class="text-black dark:text-white">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Célula</label>
                        <select name="cell_id" class="searchable-select w-full py-4 bg-gray-50/50 dark:bg-gray-900/50 border-transparent focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 rounded-2xl font-bold text-sm transition-all text-black dark:text-white" data-label="Célula">
                            <option value="">Todas as Células</option>
                            @foreach($cells as $cell)
                                <option value="{{ $cell->id }}" {{ request('cell_id') == $cell->id ? 'selected' : '' }}>{{ $cell->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-black dark:text-white">
                        <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Tipo</label>
                        <select name="meeting_type" class="w-full py-4 bg-gray-50/50 dark:bg-gray-900/50 border-transparent focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/20 rounded-2xl font-bold text-sm transition-all text-black dark:text-white">
                            <option value="">Todos os Tipos</option>
                            <option value="normal" {{ request('meeting_type') == 'normal' ? 'selected' : '' }}>Reunião de Célula</option>
                            <option value="leadership" {{ request('meeting_type') == 'leadership' ? 'selected' : '' }}>Liderança</option>
                            <option value="supervision" {{ request('meeting_type') == 'supervision' ? 'selected' : '' }}>Supervisão</option>
                            <option value="zone" {{ request('meeting_type') == 'zone' ? 'selected' : '' }}>Zona</option>
                        </select>
                    </div>

                    <div class="flex gap-2 text-black dark:text-white items-end">
                        <button type="submit" class="flex-1 bg-gray-900 dark:bg-black text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all flex items-center justify-center gap-2 py-4">
                            <i class="bi bi-filter"></i> Filtrar
                        </button>
                        @if(request()->anyFilled(['search', 'cell_id', 'meeting_type', 'date_start', 'date_end']))
                            <a href="{{ route('cell-meetings.index') }}" class="w-14 h-14 bg-gray-100 dark:bg-gray-700 text-gray-500 rounded-2xl hover:bg-red-50 hover:text-red-600 transition-all flex items-center justify-center">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex bg-gray-100 dark:bg-gray-700 p-1 rounded-xl items-center shadow-inner">
                    <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-md' : 'text-gray-400 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                        class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all duration-300">
                        <i class="bi bi-list-ul mr-2 text-sm"></i> Lista
                    </button>
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-md' : 'text-gray-400 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                        class="px-5 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all duration-300">
                        <i class="bi bi-grid-fill mr-2 text-sm"></i> Grid
                    </button>
                </div>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-gray-900 px-4 py-2 rounded-full border border-gray-100 dark:border-gray-700">
                    {{ $meetings->total() }} Registros
                </span>
            </div>

            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('cell-meetings.export', request()->all()) }}"
                    class="flex items-center gap-2 px-4 md:px-6 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-black text-[10px] uppercase tracking-widest rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm"
                    title="Exportar para Excel">
                    <i class="bi bi-file-earmark-excel-fill text-xl md:text-2xl"></i>
                    <span class="hidden md:inline">Exportar</span>
                </a>

                @if(auth()->user()->isAdmin() || auth()->user()->can('create', App\Models\CellMeeting::class))
                    <a href="{{ route('cell-meetings.create') }}"
                        class="flex items-center gap-2 px-4 md:px-6 py-2.5 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20"
                        title="Novo Encontro">
                        <i class="bi bi-plus-lg text-xl md:text-2xl"></i>
                        <span class="hidden md:inline">Novo Encontro</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- List View -->
        <div x-show="view === 'list'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-compact">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                            <th class="px-8 py-6 text-left">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" @click="toggleSelectAll()" :checked="allSelected"
                                        class="w-5 h-5 text-blue-600 border-gray-200 rounded-lg focus:ring-blue-500/20 transition-all">
                                </label>
                            </th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Data / Célula</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Responsável / Tema</th>
                            <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Participação</th>
                            <th class="px-10 py-5 text-center text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Tipo</th>
                            <th class="px-10 py-5 text-right text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @forelse($meetings as $meeting)
                            <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                                <td class="px-8 py-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" value="{{ $meeting->id }}" x-model="selectedIds"
                                            class="item-checkbox w-5 h-5 text-blue-600 border-gray-200 rounded-lg focus:ring-blue-500/20 transition-all">
                                    </label>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors tooltip" title="Ver Detalhes">
                                            <a href="{{ route('cell-meetings.show', $meeting) }}">{{ $meeting->meeting_date->format('d/m/Y') }}</a>
                                        </span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 font-black uppercase tracking-widest mt-0.5">
                                            {{ $meeting->cell->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            @if($meeting->leader)
                                                <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex items-center justify-center text-[10px] font-black">
                                                    {{ substr($meeting->leader->name, 0, 1) }}
                                                </div>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white leading-tight">{{ $meeting->leader->name }}</span>
                                            @else
                                                <span class="text-sm font-bold text-gray-400 dark:text-gray-500 italic">N/A</span>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 italic mt-1 line-clamp-1 truncate max-w-[200px]" title="{{ $meeting->theme }}">
                                            {{ $meeting->theme ?? 'Sem tema registrado' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <div class="inline-flex flex-col items-center bg-gray-50 dark:bg-gray-900 px-4 py-2 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-inner group-hover:bg-white dark:group-hover:bg-gray-800 transition-all">
                                        <span class="text-lg font-black text-gray-900 dark:text-white tracking-tighter">
                                            {{ $meeting->adults_count + $meeting->children_count + $meeting->visitors_count }}
                                        </span>
                                        <span class="text-[8px] text-gray-400 dark:text-gray-500 font-black uppercase tracking-[0.15em]">Total</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    @php
                                        $typeStyles = [
                                            'normal' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
                                            'leadership' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 border-purple-100 dark:border-purple-800',
                                            'supervision' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-800',
                                            'zone' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-100 dark:border-indigo-800',
                                        ];
                                        $style = $typeStyles[$meeting->meeting_type] ?? 'bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 border-gray-100 dark:border-gray-600';
                                    @endphp
                                    <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $style }} shadow-sm">
                                        @switch($meeting->meeting_type)
                                            @case('leadership') Liderança @break
                                            @case('supervision') Supervisão @break
                                            @case('zone') Zona @break
                                            @default Célula
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('cell-meetings.show', $meeting) }}" title="Detalhes"
                                            class="action-icon bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 hover:bg-blue-600 dark:hover:bg-blue-600 hover:text-white dark:hover:text-white shadow-sm">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        @can('update', $meeting)
                                            <a href="{{ route('cell-meetings.edit', $meeting) }}"
                                                class="action-icon bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 hover:bg-orange-500 dark:hover:bg-orange-500 hover:text-white dark:hover:text-white shadow-sm" title="Editar">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $meeting)
                                            <button type="button" @click="confirmDelete('delete-meeting-{{ $meeting->id }}', 'Deseja excluir este encontro?')"
                                                class="action-icon bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 hover:bg-red-600 dark:hover:bg-red-600 hover:text-white dark:hover:text-white shadow-sm" title="Excluir">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                            <form action="{{ route('cell-meetings.destroy', $meeting) }}" method="POST" id="delete-meeting-{{ $meeting->id }}" class="hidden">
                                                @csrf @method('DELETE')
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-10 py-24 text-center">
                                    <div class="flex flex-col items-center gap-6 text-gray-300 dark:text-gray-600 animate-pulse">
                                        <div class="w-24 h-24 rounded-[2rem] bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-5xl">
                                            <i class="bi bi-calendar-x"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-lg text-gray-400 dark:text-gray-500 uppercase tracking-widest">Nenhum encontro localizado</p>
                                            <p class="text-xs font-bold text-gray-300 dark:text-gray-600 mt-2">Tente ajustar os filtros ou pesquisar por outro termo.</p>
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
            @foreach($meetings as $meeting)
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col group hover:shadow-2xl hover:shadow-blue-600/5 transition-all duration-500 relative overflow-hidden active:scale-95 compact-card">
                    <div class="absolute top-8 right-8 flex flex-col items-end gap-3 z-10">
                        <label class="flex items-center cursor-pointer mb-2">
                            <input type="checkbox" value="{{ $meeting->id }}" x-model="selectedIds"
                                class="item-checkbox w-6 h-6 text-blue-600 border-gray-100 dark:border-gray-700 rounded-xl focus:ring-blue-500/20 transition-all bg-gray-50 dark:bg-gray-900 shadow-inner">
                        </label>
                        @php
                            $typeStyles = [
                                'normal' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
                                'leadership' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 border-purple-100 dark:border-purple-800',
                                'supervision' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-800',
                                'zone' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-100 dark:border-indigo-800',
                            ];
                            $style = $typeStyles[$meeting->meeting_type] ?? 'bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 border-gray-100 dark:border-gray-600';
                        @endphp
                        <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest border {{ $style }} shadow-sm backdrop-blur-sm">
                            @switch($meeting->meeting_type)
                                @case('leadership') Lider @break
                                @case('supervision') Sup @break
                                @case('zone') Zona @break
                                @default Célula
                            @endswitch
                        </span>
                    </div>

                    <div class="w-20 h-20 rounded-[2.2rem] bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-3xl group-hover:from-blue-600 group-hover:to-blue-700 group-hover:text-white transition-all duration-700 mb-8 shadow-inner">
                        <i class="bi bi-calendar-week"></i>
                    </div>

                    <div class="mb-4 space-y-1">
                        <h4 class="text-lg font-black text-gray-900 dark:text-white leading-none tracking-tighter group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-1">
                            {{ $meeting->meeting_date->format('d/m/Y') }}
                        </h4>
                        <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] line-clamp-1">{{ $meeting->cell->name }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-inner">
                            <span class="block text-[8px] font-black uppercase text-gray-400 dark:text-gray-500 tracking-widest mb-1">Participação</span>
                            <span class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter">{{ $meeting->adults_count + $meeting->children_count + $meeting->visitors_count }}</span>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-inner">
                            <span class="block text-[8px] font-black uppercase text-gray-400 dark:text-gray-500 tracking-widest mb-1">Decisões</span>
                            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tighter">{{ $meeting->decisions ? 'SIM' : 'NÃO' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-auto pt-6 border-t border-gray-50 dark:border-gray-700/50">
                        <a href="{{ route('cell-meetings.show', $meeting) }}"
                            class="flex-1 bg-gray-900 dark:bg-black text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 dark:hover:bg-blue-600 transition-all flex items-center justify-center gap-3 active:scale-95 shadow-lg shadow-gray-200 dark:shadow-none">
                            <i class="bi bi-eye text-base"></i> Abrir
                        </a>
                        @can('update', $meeting)
                            <a href="{{ route('cell-meetings.edit', $meeting) }}"
                                class="w-14 h-14 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 flex items-center justify-center rounded-2xl hover:bg-orange-500 hover:text-white dark:hover:bg-orange-500 dark:hover:text-white transition-all shadow-sm active:scale-95">
                                <i class="bi bi-pencil-square text-lg"></i>
                            </a>
                        @endcan
                        @can('delete', $meeting)
                            <form action="{{ route('cell-meetings.destroy', $meeting) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-14 h-14 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 flex items-center justify-center rounded-2xl hover:bg-red-500 hover:text-white dark:hover:bg-red-500 dark:hover:text-white transition-all shadow-sm active:scale-95">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        @if($meetings->hasPages())
            <div class="mt-12">
                {{ $meetings->links() }}
            </div>
        @endif

        <!-- Bulk Action Bar -->
        <div x-show="selectedIds.length > 0" x-transition:enter="transition-all ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-y-24" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition-all ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-24"
            class="fixed bottom-10 left-1/2 -translate-x-1/2 z-50 w-full max-w-2xl px-6">
            <div class="bg-gray-900 dark:bg-black p-5 rounded-[2.5rem] shadow-2xl border border-white/10 backdrop-blur-xl flex items-center justify-between gap-6 overflow-hidden relative">
                <div class="absolute inset-0 bg-blue-600/5 -skew-x-12 translate-x-1/2"></div>
                <div class="relative flex items-center gap-6">
                    <div class="w-14 h-14 bg-blue-600 text-white rounded-[1.5rem] flex items-center justify-center font-black text-xl shadow-lg shadow-blue-600/20">
                        <span x-text="selectedIds.length"></span>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Registros Selecionados</p>
                        <p class="text-white font-bold text-sm">Ações em Massa Disponíveis</p>
                    </div>
                </div>
                <div class="relative flex items-center gap-3">
                    @can('deleteAny', App\Models\CellMeeting::class)
                        <button type="button" @click="confirmAction('Eliminar Registos', 'Tem certeza que deseja eliminar ' + selectedIds.length + ' encontros selecionados?', 'warning', 'Sim, Eliminar', 'bulk-delete-form')"
                            class="px-8 py-4 bg-red-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition-all flex items-center gap-3 active:scale-95 shadow-lg shadow-red-600/20">
                            <i class="bi bi-trash3-fill"></i> Eliminar
                        </button>
                    @endcan
                    <button @click="selectedIds = []; allSelected = false" class="text-gray-400 hover:text-white p-4 transition-colors">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <form action="{{ route('cell-meetings.bulk-destroy') }}" method="POST" id="bulk-delete-form" class="hidden">
            @csrf @method('DELETE')
            <template x-for="id in selectedIds" :key="id">
                <input type="hidden" name="selected_ids[]" :value="id">
            </template>
        </form>
    </div>
@endsection
