@extends('layouts.app')

@section('title', 'Calendário de Casamentos')
@section('page-title', 'Calendário de Casamentos')
@section('page-subtitle', 'Gestão de casamentos e eventos matrimoniais')

@section('header-actions')
    <a href="{{ route('weddings.pdf', ['year' => now()->year]) }}" target="_blank"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 transition-colors" title="Exportar PDF do Ano">
        <i class="bi bi-file-earmark-pdf text-sm"></i>
    </a>
    <a href="{{ route('weddings.create') }}"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-orange-700 text-white hover:bg-orange-800 shadow-sm transition-colors"
        title="Novo Casamento">
        <i class="bi bi-plus-lg text-sm"></i>
    </a>
@endsection

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="space-y-5" x-data="{ 
        view: window.innerWidth < 768 ? 'grid' : 'grid',
        selected: [],
        updateView() {
            if (window.innerWidth < 768 && this.view === 'list') {
                this.view = 'grid'; 
            }
        },
        toggleAll() {
            const allIds = {{ Js::from($weddings->pluck('id')) }};
            if (this.selected.length === allIds.length) {
                this.selected = [];
            } else {
                this.selected = allIds;
            }
        }
    }"
    x-init="$watch('view', value => { if(value !== 'calendar') localStorage.setItem('weddings_view', value) }); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('weddings_view') || 'grid')"
    @resize.window.debounce.500ms="updateView()" x-cloak>

    <!-- Bulk Action Bar -->
    <div x-show="selected.length > 0" x-transition
        class="fixed top-20 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
        <div class="bg-gray-900 text-white rounded-xl shadow-2xl px-4 py-2.5 flex items-center gap-4 pointer-events-auto border border-gray-700">
            <div class="flex items-center gap-2">
                <span class="bg-orange-600 text-xs font-black px-2 py-0.5 rounded" x-text="selected.length"></span>
                <span class="text-xs font-medium text-gray-200">selecionados</span>
            </div>
            <div class="h-4 w-px bg-gray-700"></div>
            <button @click="selected = []" class="text-xs font-bold text-gray-400 hover:text-white transition">
                Cancelar
            </button>
            @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
                <form method="POST" action="{{ route('weddings.bulk-delete') }}" @submit.prevent="
                    if (confirm('Tem certeza que deseja excluir ' + selected.length + ' casamento(s)? Esta ação é irreversível.')) {
                        $el.submit();
                    }
                ">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="wedding_ids[]" :value="id">
                    </template>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="flex items-center gap-2">
            <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Agendamentos</span>
            <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold text-orange-800">
                {{ $totalCount ?? $weddings->total() }} registros
            </span>
        </div>

        <div class="flex items-center gap-2 self-end sm:self-auto">
            <!-- View Toggle -->
            <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-sm">
                <button @click="view = 'list'"
                    :class="view === 'list' ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="rounded-md px-2.5 py-1 text-xs font-bold transition">
                    <i class="bi bi-list-ul mr-1"></i> Lista
                </button>
                <button @click="view = 'grid'"
                    :class="view === 'grid' ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="rounded-md px-2.5 py-1 text-xs font-bold transition">
                    <i class="bi bi-grid-fill mr-1"></i> Grelha
                </button>
                <button @click="view = 'calendar'; setTimeout(() => initCalendar(), 100)"
                    :class="view === 'calendar' ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="rounded-md px-2.5 py-1 text-xs font-bold transition">
                    <i class="bi bi-calendar-week mr-1"></i> Calendário
                </button>
            </div>

            <a href="{{ route('weddings.pdf', ['year' => now()->year]) }}" target="_blank"
                class="hidden sm:inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-zinc-200 shadow-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                <i class="bi bi-file-earmark-pdf text-rose-600"></i>
                <span>PDF {{ now()->year }}</span>
            </a>

            <a href="{{ route('weddings.create') }}"
                class="hidden sm:inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-plus-lg"></i>
                <span>Novo Agendamento</span>
            </a>
        </div>
    </div>

    <!-- Search / Filter -->
    <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
        <form action="{{ route('weddings.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1 w-full">
                <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Buscar</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" data-live-search="ajax" value="{{ request('search') }}"
                        class="w-full rounded-lg border-gray-300 pl-8 pr-3 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                        placeholder="Nome dos noivos, local...">
                </div>
            </div>
            <div class="w-full sm:w-44">
                <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 px-3 py-1.5 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 bg-white">
                    <option value="">Todos</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Agendado</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Realizado</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <button type="submit" class="w-full sm:w-auto rounded-lg bg-gray-900 px-4 py-1.5 text-xs font-bold text-white hover:bg-black transition">
                Filtrar
            </button>
        </form>
    </div>

    <!-- List View -->
    <div id="view-list" x-show="view === 'list'" class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/75 text-[10px] font-black uppercase tracking-wider text-gray-500">
                            @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
                                <th class="px-4 py-3 w-8">
                                    <input type="checkbox" @click="toggleAll()"
                                        :checked="selected.length === {{ $weddings->count() }} && selected.length > 0"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                                </th>
                            @endif
                            <th class="px-4 py-3">Data</th>
                            <th class="px-4 py-3">Noivos</th>
                            <th class="px-4 py-3">Local</th>
                            <th class="px-4 py-3">Padrinhos</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                        @forelse($weddings as $wedding)
                            <tr class="hover:bg-gray-50/60 transition">
                                @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
                                    <td class="px-4 py-3">
                                        <input type="checkbox" value="{{ $wedding->id }}" x-model="selected"
                                            class="wedding-checkbox rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                                    </td>
                                @endif
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-bold text-gray-900 block">{{ $wedding->date->format('d/m/Y') }}</span>
                                    <span class="text-[10px] text-gray-400 uppercase font-medium">{{ $wedding->date->translatedFormat('l') }}</span>
                                    @if($wedding->time)
                                        <span class="text-[10px] font-bold text-orange-700 block">{{ $wedding->time->format('H:i') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-orange-50 text-orange-700">
                                            <i class="bi bi-heart-fill text-xs"></i>
                                        </span>
                                        <div>
                                            <span class="block font-bold text-gray-900">{{ $wedding->groom_name }}</span>
                                            <span class="text-xs text-gray-500">& {{ $wedding->bride_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    <i class="bi bi-geo-alt text-gray-400 mr-1"></i>
                                    {{ $wedding->location ?? 'Não definido' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    @if($wedding->godparents)
                                        <span class="font-medium text-gray-700">{{ Illuminate\Support\Str::limit($wedding->godparents, 25) }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Não informado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $status = $wedding->status ?? 'scheduled';
                                        $statusClasses = [
                                            'scheduled' => 'bg-amber-100 text-amber-800',
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusLabels = [
                                            'scheduled' => 'Agendado',
                                            'completed' => 'Realizado',
                                            'cancelled' => 'Cancelado'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $statusLabels[$status] ?? $status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('weddings.show', $wedding) }}" class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-orange-700 transition" title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('weddings.edit', $wedding) }}" class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-blue-600 transition" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
                                            <form id="list-delete-wedding-{{ $wedding->id }}" action="{{ route('weddings.destroy', $wedding->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('list-delete-wedding-{{ $wedding->id }}')" class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-red-600 transition" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-xs text-gray-400">
                                    Nenhum casamento encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($weddings->hasPages())
                <div class="p-3 border-t border-gray-100 bg-gray-50/50">
                    {{ $weddings->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Grid View -->
    <div id="view-grid" x-show="view === 'grid'" class="space-y-4">
        <div class="flex flex-col lg:flex-row gap-5">
            <!-- Grid Cards -->
            <div class="flex-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5 sm:gap-4">
                    @forelse($weddings as $wedding)
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between"
                            :class="{'ring-2 ring-orange-500 bg-orange-50/20': selected.includes({{ $wedding->id }})}">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 text-orange-700">
                                            <i class="bi bi-heart-fill text-sm"></i>
                                        </span>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900 leading-tight">
                                                {{ explode(' ', $wedding->groom_name)[0] }} & {{ explode(' ', $wedding->bride_name)[0] }}
                                            </h4>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $wedding->date->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        @php
                                            $status = $wedding->status ?? 'scheduled';
                                            $statusClasses = [
                                                'scheduled' => 'bg-amber-100 text-amber-800',
                                                'completed' => 'bg-emerald-100 text-emerald-800',
                                                'cancelled' => 'bg-red-100 text-red-800'
                                            ];
                                            $statusLabels = [
                                                'scheduled' => 'Agendado',
                                                'completed' => 'Realizado',
                                                'cancelled' => 'Cancelado'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $statusLabels[$status] ?? $status }}
                                        </span>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
                                            <input type="checkbox" value="{{ $wedding->id }}" x-model="selected"
                                                class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                                        @endif
                                    </div>
                                </div>

                                <div class="space-y-1.5 text-xs text-gray-600 my-3 pt-2 border-t border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-400 text-[11px]">Data:</span>
                                        <span class="font-bold text-gray-900">{{ $wedding->date->format('d/m/Y') }} ({{ $wedding->date->translatedFormat('D') }})</span>
                                    </div>
                                    @if($wedding->time)
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-400 text-[11px]">Horário:</span>
                                            <span class="font-bold text-orange-700">{{ $wedding->time->format('H:i') }}</span>
                                        </div>
                                    @endif
                                    @if($wedding->location)
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-400 text-[11px]">Local:</span>
                                            <span class="font-medium text-gray-800 truncate max-w-[160px]">{{ $wedding->location }}</span>
                                        </div>
                                    @endif
                                    @if($wedding->godparents)
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-400 text-[11px]">Padrinhos:</span>
                                            <span class="font-medium text-gray-800 truncate max-w-[160px]">{{ $wedding->godparents }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-1 pt-3 border-t border-gray-100">
                                <a href="{{ route('weddings.show', $wedding) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <a href="{{ route('weddings.edit', $wedding) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-blue-700 hover:bg-blue-50 transition">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(auth()->user()->isAdmin() || auth()->user()->isSecretaria() || auth()->user()->isPastor() || auth()->user()->isPastorSenior())
                                    <form id="grid-delete-wedding-{{ $wedding->id }}" action="{{ route('weddings.destroy', $wedding) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('grid-delete-wedding-{{ $wedding->id }}')" class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-red-600 transition" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-xs text-gray-500">
                            Nenhum casamento encontrado.
                        </div>
                    @endforelse
                </div>
                @if($weddings->hasPages())
                    <div class="mt-4">
                        {{ $weddings->links() }}
                    </div>
                @endif
            </div>

            <!-- Sidebar Summary -->
            <div class="w-full lg:w-72 space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-wider text-orange-700 mb-3">Resumo {{ now()->year }}</p>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <span class="text-gray-500">Total</span>
                            <span class="font-black text-gray-900 text-sm">{{ $totalCount ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <span class="text-gray-500">Realizados</span>
                            <span class="font-bold text-emerald-700">{{ $completedCount ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <span class="text-gray-500">Próximos</span>
                            <span class="font-bold text-amber-700">{{ $upcomingCount ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Cancelados</span>
                            <span class="font-bold text-red-600">{{ $cancelledCount ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                @if(isset($upcoming) && $upcoming->isNotEmpty())
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-700 mb-3">Próximos Agendamentos</p>
                        <div class="space-y-3">
                            @foreach($upcoming as $uWedding)
                                <a href="{{ route('weddings.show', $uWedding) }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition group">
                                    <div class="flex-shrink-0 w-10 text-center bg-orange-50 rounded-lg py-1 border border-orange-100 group-hover:bg-orange-700 group-hover:text-white transition">
                                        <span class="block text-[9px] font-black uppercase text-orange-700 group-hover:text-white">{{ $uWedding->date->translatedFormat('M') }}</span>
                                        <span class="block text-sm font-black leading-tight text-orange-950 group-hover:text-white">{{ $uWedding->date->format('d') }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <h5 class="text-xs font-bold text-gray-900 truncate group-hover:text-orange-700 transition">
                                            {{ explode(' ', $uWedding->groom_name)[0] }} & {{ explode(' ', $uWedding->bride_name)[0] }}
                                        </h5>
                                        <p class="text-[10px] text-gray-400">{{ $uWedding->date->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div id="view-calendar" x-show="view === 'calendar'" class="rounded-xl border border-gray-200 bg-white shadow-sm p-4 sm:p-5">
        <div id="calendar"></div>
    </div>
</div>

<!-- FullCalendar CSS -->
<style>
    .fc { font-family: inherit; }
    .fc-toolbar-title {
        font-size: 1.1rem !important;
        font-weight: 800 !important;
        color: #111827;
    }
    .fc-button {
        background-color: white !important;
        color: #374151 !important;
        border: 1px solid #d1d5db !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        border-radius: 0.5rem !important;
        padding: 0.35rem 0.75rem !important;
        box-shadow: none !important;
    }
    .fc-button:hover {
        background-color: #f3f4f6 !important;
        color: #c2410c !important;
        border-color: #fdba74 !important;
    }
    .fc-button-active {
        background-color: #c2410c !important;
        color: white !important;
        border-color: #c2410c !important;
    }
    .fc-daygrid-day-number {
        font-weight: 700;
        color: #4b5563;
        font-size: 0.75rem;
        padding: 0.25rem !important;
    }
    .fc-col-header-cell-cushion {
        text-transform: uppercase;
        font-size: 0.65rem;
        font-weight: 900;
        letter-spacing: 0.05em;
        color: #6b7280;
        padding: 0.5rem 0 !important;
    }
    .fc-event {
        border: none !important;
        border-radius: 0.25rem !important;
        padding: 2px 4px !important;
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        cursor: pointer;
    }
    .fc-day-today { background-color: #fff7ed !important; }

    /* Tooltip */
    .wedding-tooltip {
        position: absolute;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.75rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        min-width: 240px;
        display: none;
    }
    .wedding-tooltip.show { display: block; }
    .tooltip-header {
        font-weight: 800;
        font-size: 0.85rem;
        color: #111827;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }
    .tooltip-header i { margin-right: 0.35rem; color: #c2410c; }
    .tooltip-row {
        display: flex;
        align-items: center;
        margin: 0.25rem 0;
        font-size: 0.75rem;
        color: #4b5563;
    }
    .tooltip-row i { margin-right: 0.35rem; width: 14px; color: #9ca3af; }
    .tooltip-status {
        display: inline-block;
        padding: 0.15rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-top: 0.25rem;
    }
    .status-scheduled { background: #fef3c7; color: #92400e; }
    .status-completed { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
</style>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/pt.global.min.js'></script>
<script>
    let calendar;
    let tooltip = null;

    function confirmDelete(formId) {
        if (confirm('Tem certeza que deseja excluir este casamento?')) {
            document.getElementById(formId).submit();
        }
    }

    function createTooltip() {
        tooltip = document.createElement('div');
        tooltip.className = 'wedding-tooltip';
        document.body.appendChild(tooltip);
    }

    function showTooltip(info) {
        const event = info.event;
        const props = event.extendedProps;
        const statusLabels = {
            'scheduled': 'Agendado',
            'completed': 'Realizado',
            'cancelled': 'Cancelado'
        };

        let tooltipContent = `
            <div class="tooltip-header">
                <i class="bi bi-heart-fill"></i>
                <span>${event.title}</span>
            </div>
            <div class="tooltip-row">
                <i class="bi bi-calendar-event"></i>
                <span>${event.start.toLocaleDateString('pt-BR', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' })}</span>
            </div>
        `;

        if (props.time) {
            tooltipContent += `
                <div class="tooltip-row">
                    <i class="bi bi-clock"></i>
                    <span>${props.time}</span>
                </div>
            `;
        }

        if (props.location) {
            tooltipContent += `
                <div class="tooltip-row">
                    <i class="bi bi-geo-alt"></i>
                    <span>${props.location}</span>
                </div>
            `;
        }

        if (props.status) {
            tooltipContent += `
                <div class="tooltip-status status-${props.status}">
                    ${statusLabels[props.status] || props.status}
                </div>
            `;
        }

        tooltip.innerHTML = tooltipContent;
        tooltip.classList.add('show');

        const rect = info.el.getBoundingClientRect();
        tooltip.style.left = rect.left + 'px';
        tooltip.style.top = (rect.bottom + 5) + 'px';
    }

    function hideTooltip() {
        if (tooltip) {
            tooltip.classList.remove('show');
        }
    }

    function initCalendar() {
        var calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;
        if (calendar) {
            calendar.render();
            return;
        }
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,listMonth'
            },
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                list: 'Lista'
            },
            height: 'auto',
            eventDisplay: 'block',
            displayEventTime: false,
            events: '{{ route("weddings.feed") }}',
            eventClick: function (info) {
                if (info.event.url) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault();
                }
            },
            eventMouseEnter: function (info) { showTooltip(info); },
            eventMouseLeave: function (info) { hideTooltip(); },
            eventDidMount: function (info) {
                const iconEl = document.createElement('i');
                iconEl.className = 'bi bi-heart-fill mr-1';
                const titleEl = info.el.querySelector('.fc-event-title');
                if (titleEl) titleEl.prepend(iconEl);
            },
            dateClick: function (info) {
                window.location.href = '{{ route("weddings.create") }}?date=' + info.dateStr;
            }
        });
        calendar.render();
    }

    document.addEventListener('DOMContentLoaded', function () {
        createTooltip();

        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            const filterForm = searchInput.closest('form');
            let timeout = null;
            searchInput.addEventListener('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams(formData);
                    document.body.style.cursor = 'wait';

                    fetch(`${filterForm.action}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        ['view-list', 'view-grid'].forEach(id => {
                            const newEl = doc.getElementById(id);
                            const currentEl = document.getElementById(id);
                            if (newEl && currentEl) currentEl.innerHTML = newEl.innerHTML;
                        });
                        document.body.style.cursor = 'default';
                    })
                    .catch(err => {
                        console.error('Search failed', err);
                        document.body.style.cursor = 'default';
                    });
                }, 500);
            });
        }
    });
</script>
@endsection
