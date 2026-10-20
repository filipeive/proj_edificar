@extends('layouts.app')

@section('title', 'Encontros de Célula - Portal Life Church')
@section('page-title', 'Encontros de Célula')
@section('page-subtitle', 'Gestão de reuniões e atas das células')

@section('header-actions')
    <a href="{{ route('cell-meetings.export', request()->all()) }}"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 transition-colors"
        title="Exportar para Excel">
        <i class="bi bi-file-earmark-excel text-emerald-600 text-sm"></i>
    </a>
    @if(auth()->user()->isAdmin() || auth()->user()->can('create', App\Models\CellMeeting::class))
        <a href="{{ route('cell-meetings.create') }}"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-orange-700 text-white hover:bg-orange-800 shadow-sm transition-colors"
            title="Novo Encontro">
            <i class="bi bi-plus-lg text-sm"></i>
        </a>
    @endif
@endsection

@section('content')
    <div class="space-y-4" 
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

        <!-- Header da Página (Desktop) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Comunhão & Discipulado</span>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white tracking-tight">Encontros de Célula</h1>
                <p class="text-xs text-gray-500 dark:text-zinc-400 font-medium">Gestão de reuniões semanais, presenças e atas das células.</p>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <a href="{{ route('cell-meetings.export', request()->all()) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-200 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors"
                    title="Exportar para Excel">
                    <i class="bi bi-file-earmark-excel text-emerald-600 text-xs"></i>
                    <span>Exportar Excel</span>
                </a>
                @if(auth()->user()->isAdmin() || auth()->user()->can('create', App\Models\CellMeeting::class))
                    <a href="{{ route('cell-meetings.create') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors"
                        title="Novo Encontro">
                        <i class="bi bi-plus-lg text-xs"></i>
                        <span>Novo Encontro</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Stats Overview -->
        @if(isset($stats))
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Total Encontros</p>
                        <p class="mt-1 text-2xl font-black text-gray-900 tracking-tight">{{ $stats['total_meetings'] }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                        <i class="bi bi-calendar-check text-lg"></i>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Participação Total</p>
                        <p class="mt-1 text-2xl font-black text-emerald-700 tracking-tight">{{ number_format($stats['total_attendance'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                        <i class="bi bi-people text-lg"></i>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Média / Encontro</p>
                        <p class="mt-1 text-2xl font-black text-purple-700 tracking-tight">{{ $stats['avg_attendance'] }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-700">
                        <i class="bi bi-graph-up-arrow text-lg"></i>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Relatório Excel</p>
                        <a href="{{ route('cell-meetings.export', request()->all()) }}" 
                           class="mt-1 inline-flex items-center gap-1 text-xs font-bold text-orange-700 hover:text-orange-800">
                            <span>Descarregar Lista</span>
                            <i class="bi bi-download text-[11px]"></i>
                        </a>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                        <i class="bi bi-file-earmark-excel text-lg"></i>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <form action="{{ route('cell-meetings.index') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-2.5">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Pesquisar</label>
                        <div class="relative">
                            <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tema, líder..."
                                class="w-full rounded-lg border-gray-300 pl-7 pr-3 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Célula</label>
                        <select name="cell_id" class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Todas</option>
                            @foreach($cells as $cell)
                                <option value="{{ $cell->id }}" {{ request('cell_id') == $cell->id ? 'selected' : '' }}>{{ $cell->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Supervisão</label>
                        <select name="supervision_id" class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Todas</option>
                            @foreach($supervisions as $sup)
                                <option value="{{ $sup->id }}" {{ request('supervision_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Zona</label>
                        <select name="zone_id" class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Todas</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Tipo</label>
                        <select name="meeting_type" class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Todos</option>
                            <option value="normal" {{ request('meeting_type') == 'normal' ? 'selected' : '' }}>Célula</option>
                            <option value="leadership" {{ request('meeting_type') == 'leadership' ? 'selected' : '' }}>Liderança</option>
                            <option value="supervision" {{ request('meeting_type') == 'supervision' ? 'selected' : '' }}>Supervisão</option>
                            <option value="zone" {{ request('meeting_type') == 'zone' ? 'selected' : '' }}>Zona</option>
                            <option value="other" {{ request('meeting_type') == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Data Início</label>
                        <input type="date" name="date_start" value="{{ request('date_start') }}"
                            class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Data Fim</label>
                        <input type="date" name="date_end" value="{{ request('date_end') }}"
                            class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <div class="flex items-center gap-1.5">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-orange-800 transition-colors shadow-sm">
                            <i class="bi bi-filter text-xs"></i> Filtrar
                        </button>
                        @if(request()->anyFilled(['search', 'cell_id', 'supervision_id', 'zone_id', 'meeting_type', 'date_start', 'date_end']))
                            <a href="{{ route('cell-meetings.index') }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                                <i class="bi bi-x-lg text-xs"></i> Limpar
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
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
                        <span class="text-xs text-gray-500 font-semibold">{{ $meetings->total() }} registros</span>
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
                                <input type="checkbox" @click="toggleSelectAll()" :checked="allSelected"
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                            </th>
                            <th class="px-4 py-2.5">Data / Tipo</th>
                            <th class="px-4 py-2.5">Célula / Estrutura</th>
                            <th class="px-4 py-2.5">Líder / Tema</th>
                            <th class="px-4 py-2.5 text-center">Presenças</th>
                            <th class="px-4 py-2.5 text-center">Decisões</th>
                            <th class="px-4 py-2.5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($meetings as $meeting)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-4 py-2.5">
                                    <input type="checkbox" value="{{ $meeting->id }}" x-model="selectedIds"
                                        class="item-checkbox rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    <p class="font-bold text-gray-900">{{ $meeting->meeting_date->format('d/m/Y') }}</p>
                                    @php
                                        $typeStyles = [
                                            'normal' => 'bg-emerald-50 text-emerald-700',
                                            'leadership' => 'bg-purple-50 text-purple-700',
                                            'supervision' => 'bg-amber-50 text-amber-700',
                                            'zone' => 'bg-blue-50 text-blue-700',
                                        ];
                                        $style = $typeStyles[$meeting->meeting_type] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $style }}">
                                        @switch($meeting->meeting_type)
                                            @case('leadership') Liderança @break
                                            @case('supervision') Supervisão @break
                                            @case('zone') Zona @break
                                            @default Célula
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="font-bold text-gray-900 block truncate max-w-xs">
                                        {{ $meeting->cell?->name ?? $meeting->zone?->name ?? $meeting->supervision?->name ?? $meeting->meeting_type_label }}
                                    </span>
                                    @if($meeting->cell && $meeting->cell->supervision)
                                        <span class="text-[10px] text-gray-400 block truncate">{{ $meeting->cell->supervision->name }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="font-semibold text-gray-800 block truncate">{{ $meeting->leader?->name ?? '-' }}</span>
                                    <span class="text-[11px] text-gray-500 italic block truncate max-w-xs">{{ $meeting->theme ?? 'Sem tema cadastrado' }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-700">
                                        {{ $meeting->adults_count + $meeting->children_count + $meeting->visitors_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    @if($meeting->decisions)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">SIM</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">NÃO</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('cell-meetings.show', $meeting) }}"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                                            title="Ver detalhes">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                        @can('update', $meeting)
                                            <a href="{{ route('cell-meetings.edit', $meeting) }}"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:border-orange-300 hover:text-orange-700 hover:bg-orange-50 transition-colors"
                                                title="Editar">
                                                <i class="bi bi-pencil-square text-xs"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $meeting)
                                            <form action="{{ route('cell-meetings.destroy', $meeting) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Deseja eliminar este encontro?')"
                                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                    title="Eliminar">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-xs text-gray-400 italic">
                                    Nenhum encontro localizado com os filtros selecionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="view === 'grid'" style="display: none;" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($meetings as $meeting)
                <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm hover:border-orange-300 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200/50">
                                {{ $meeting->meeting_date->format('d/m/Y') }}
                            </span>
                            <span class="text-[10px] font-bold uppercase text-gray-400">
                                {{ $meeting->meeting_type }}
                            </span>
                        </div>
                        <h4 class="text-xs font-black text-gray-900 line-clamp-1">
                            {{ $meeting->cell?->name ?? $meeting->zone?->name ?? $meeting->supervision?->name ?? $meeting->meeting_type_label }}
                        </h4>
                        <p class="text-[11px] text-gray-500 line-clamp-1 mt-0.5">Líder: {{ $meeting->leader?->name ?? '-' }}</p>
                        @if($meeting->theme)
                            <p class="text-[11px] text-gray-400 italic line-clamp-1 mt-0.5">"{{ $meeting->theme }}"</p>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-gray-100 mt-3 flex items-center justify-between text-xs">
                        <span class="text-gray-500">Presenças: <strong class="text-blue-700">{{ $meeting->adults_count + $meeting->children_count + $meeting->visitors_count }}</strong></span>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('cell-meetings.show', $meeting) }}"
                                class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:bg-gray-100">
                                <i class="bi bi-eye text-xs"></i>
                            </a>
                            @can('update', $meeting)
                                <a href="{{ route('cell-meetings.edit', $meeting) }}"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:text-orange-700 hover:bg-orange-50">
                                    <i class="bi bi-pencil-square text-xs"></i>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($meetings->hasPages())
            <div class="border-t border-gray-200 pt-3">
                {{ $meetings->links() }}
            </div>
        @endif

        <!-- Bulk Action Bar -->
        <div x-show="selectedIds.length > 0" x-cloak
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 rounded-xl bg-gray-900 text-white px-4 py-2.5 shadow-xl flex items-center gap-3 border border-gray-700 text-xs">
            <span class="font-bold"><span x-text="selectedIds.length"></span> selecionado(s)</span>
            <div class="h-4 w-px bg-gray-700"></div>
            @can('deleteAny', App\Models\CellMeeting::class)
                <button type="button" @click="confirmAction('Eliminar Registos', 'Tem certeza que deseja eliminar ' + selectedIds.length + ' encontros selecionados?', 'warning', 'Sim, Eliminar', 'bulk-delete-form')"
                    class="inline-flex items-center gap-1 rounded-md bg-red-600 px-2.5 py-1 text-xs font-bold text-white hover:bg-red-700">
                    <i class="bi bi-trash text-xs"></i> Eliminar
                </button>
            @endcan
            <button @click="selectedIds = []; allSelected = false" class="text-gray-400 hover:text-white ml-1">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('cell-meetings.bulk-destroy') }}" method="POST" id="bulk-delete-form" class="hidden">
            @csrf @method('DELETE')
            <template x-for="id in selectedIds" :key="id">
                <input type="hidden" name="selected_ids[]" :value="id">
            </template>
        </form>
    </div>
@endsection
