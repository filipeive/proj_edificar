@extends('layouts.app')

@section('title', 'Eventos e Cerimônias')
@section('page-title', 'Eventos e Cerimônias')
@section('page-subtitle', 'Gestão de cultos, batismos e eventos especiais')

@section('header-actions')
    <a href="{{ route('event-types.index') }}"
        class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100">
        <i class="bi bi-gear-fill text-2xl"></i>
    </a>
    <a href="{{ route('events.create') }}"
        class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100">
        <i class="bi bi-calendar-plus text-2xl"></i>
    </a>
@endsection


@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <div class="container-fluid" x-data="{ 
            view: window.innerWidth < 768 ? 'grid' : 'list',
            selected: [],
            updateView() {
                if (window.innerWidth < 768 && this.view === 'list') {
                    this.view = 'grid'; 
                }
            },
            toggleAll() {
                const allIds = {{ Js::from($events->pluck('id')) }};
                if (this.selected.length === allIds.length) {
                    this.selected = [];
                } else {
                    this.selected = allIds;
                }
            }
        }"
        x-init="$watch('view', value => { if(value !== 'calendar') localStorage.setItem('events_view', value) }); view = window.innerWidth < 768 ? 'grid' : (localStorage.getItem('events_view') || 'list')"
        @resize.window.debounce.500ms="updateView()" x-cloak>

        <!-- Bulk Action Bar -->
        <div x-show="selected.length > 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
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
                    @if(auth()->user()->role === 'admin')
                        <form method="POST" action="{{ route('events.bulk-delete') }}" 
                              @submit.prevent="
                                Swal.fire({
                                    title: 'Confirmação de Exclusão',
                                    text: 'Tem certeza que deseja excluir ' + selected.length + ' evento(s)? Esta ação é irreversível.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'Sim, excluir!',
                                    cancelButtonText: 'Cancelar'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $el.submit();
                                    }
                                })
                              ">
                            @csrf
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="event_ids[]" :value="id">
                            </template>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-red-600/20 flex items-center gap-2">
                                <i class="bi bi-trash-fill"></i> Excluir
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
            <div>
                <h2 class="text-4xl font-black text-gray-900 dark:text-white tracking-tighter uppercase leading-none">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">Eventos</span>
                    <span class="text-gray-300 dark:text-gray-600">& Cerimônias</span>
                </h2>
            </div>

            <div class="flex gap-3">
                <!-- Old Bulk Button Removed -->

                <!-- View Toggle -->
                <div class="bg-gray-100 dark:bg-gray-700 p-1 rounded-xl flex items-center">
                    <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                        <i class="bi bi-list-ul mr-2"></i> Lista
                    </button>
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                        <i class="bi bi-grid-fill mr-2"></i> Grelha
                    </button>
                    <button @click="view = 'calendar'; setTimeout(() => initCalendar(), 100)"
                        :class="view === 'calendar' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300">
                        <i class="bi bi-calendar-week mr-2"></i> Calendário
                    </button>
                </div>

                @can('create', App\Models\Event::class)
                    <a href="{{ route('events.create') }}"
                        class="hidden md:flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all duration-300 shadow-lg shadow-blue-600/30">
                        <i class="bi bi-plus-lg mr-2"></i> Novo Evento
                    </a>
                @endcan
            </div>
        </div>

        <!-- Search Form -->
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8 transition-colors">
            <form action="{{ route('events.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Buscar</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="Nome do evento, local...">
                    </div>
                </div>
                <div class="w-full md:w-auto">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                    <select name="type"
                        class="w-full md:w-48 px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Todos</option>
                        <option value="culto" {{ request('type') == 'culto' ? 'selected' : '' }}>Culto</option>
                        <option value="batismo" {{ request('type') == 'batismo' ? 'selected' : '' }}>Batismo</option>
                        <option value="evento" {{ request('type') == 'evento' ? 'selected' : '' }}>Outros</option>
                    </select>
                </div>
                <button type="submit"
                    class="w-full md:w-auto px-6 py-2 bg-gray-900 dark:bg-gray-700 text-white rounded-xl font-bold hover:bg-black dark:hover:bg-gray-600 transition-all">
                    Filtrar
                </button>
            </form>
        </div>

        <!-- List View -->
        <div id="view-list" x-show="view === 'list'" class="transition-opacity duration-300">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-xl shadow-gray-100/50 dark:shadow-none border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Form Removed (Using Alpine Template in Bar) -->
                <table class="w-full transition-colors">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                @if(auth()->user()->role === 'admin')
                                    <th class="px-8 py-5 text-left w-10">
                                        <input type="checkbox" @click="toggleAll()"
                                            :checked="selected.length === {{ $events->count() }} && selected.length > 0"
                                            class="rounded-lg border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5 bg-white dark:bg-gray-700">
                                    </th>
                                @endif
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                Data</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                Evento</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                Localização</th>
                            <th
                                class="px-8 py-5 text-center text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                Participantes</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($events as $event)
                                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                            @if(auth()->user()->role === 'admin')
                                                <td class="px-8 py-5 relative">
                                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 opacity-0 transition-opacity" :class="{'opacity-100': selected.includes({{ $event->id }})}"></div>
                                                    <input type="checkbox" value="{{ $event->id }}" x-model="selected"
                                                        class="event-checkbox rounded-lg border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5 bg-white dark:bg-gray-700">
                                                </td>
                                            @endif
                                            <td class="px-8 py-5 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-black text-gray-900 dark:text-gray-100">{{ $event->date->format('d/m/Y') }}</span>
                                                    <span
                                                        class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase">{{ $event->date->translatedFormat('l') }}</span>
                                                    @if($event->end_date)
                                                        <span class="text-[10px] font-bold text-blue-500 dark:text-blue-400 mt-1">até
                                                            {{ $event->end_date->format('d/m/Y') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-8 py-5">
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-10 h-10 rounded-xl flex items-center justify-center mr-4 
                                                                                                                                                                                                                                                                                                                                                                                                                                                    {{ $event->eventType->name == 'Culto' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' :
                            ($event->eventType->name == 'Batismo' ? 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400') }}">
                                                        <i
                                                            class="bi {{ $event->eventType->name == 'Culto' ? 'bi-church' : ($event->eventType->name == 'Batismo' ? 'bi-droplet-fill' : 'bi-calendar-event') }} text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <span class="block text-sm font-bold text-gray-900 dark:text-white">{{ $event->name }}</span>
                                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $event->eventType->name }}</span>
                                                        @if($event->cell)
                                                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Célula:
                                                                {{ $event->cell->name }}</span>
                                                        @elseif($event->zone)
                                                            <span class="text-xs text-gray-500 dark:text-gray-400 block">Zona: {{ $event->zone->name }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5">
                                                <div class="flex items-center text-gray-500 dark:text-gray-400">
                                                    <i class="bi bi-geo-alt-fill mr-2 text-gray-300 dark:text-gray-600"></i>
                                                    <span class="text-sm font-medium">{{ $event->location ?? 'Local não definido' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-5 text-center">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold">
                                                    <i class="bi bi-people-fill mr-2 text-gray-400 dark:text-gray-500"></i>
                                                    {{ $event->participants_count }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-5 text-right">
                                                <div
                                                    class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                    <a href="{{ route('events.show', $event) }}"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                                        title="Ver Detalhes">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                    <a href="{{ route('events.pdf', $event) }}" target="_blank"
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                                        title="PDF">
                                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                                    </a>
                                                    @can('update', $event)
                                                        <a href="{{ route('events.edit', $event) }}"
                                                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                                            title="Editar">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $event)
                                                        <form action="{{ route('events.destroy', $event) }}" method="POST"
                                                            id="delete-event-{{ $event->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                onclick="confirmDelete('delete-event-{{ $event->id }}', 'Deseja excluir este evento?')"
                                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                                                title="Excluir">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <i class="bi bi-calendar-x text-4xl mb-4 block opacity-20"></i>
                                    <p class="text-sm font-medium">Nenhum evento encontrado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-6 border-t border-gray-100 dark:border-gray-700">
                {{ $events->links() }}
            </div>
        </div>

        <!-- Grid View -->
        <div id="view-grid" x-show="view === 'grid'" class="transition-opacity duration-300">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($events as $event)
                            <div class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-2xl md:rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col group hover:shadow-xl transition-all duration-300 relative"
                                 :class="{'ring-2 ring-blue-500 bg-blue-50/10 dark:bg-blue-900/10': selected.includes({{ $event->id }})}">
                                <div class="absolute top-6 left-6 z-10 transition-opacity" :class="{'opacity-0 group-hover:opacity-100': !selected.includes({{ $event->id }})}">
                                     @if(auth()->user()->role === 'admin')
                                        <input type="checkbox" value="{{ $event->id }}" x-model="selected"
                                            class="event-checkbox rounded-lg border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-5 h-5 shadow-sm bg-white dark:bg-gray-700">
                                    @endif
                                </div>
                                <div class="absolute top-6 right-6 flex flex-col gap-2 items-end">
                                <span
                                    class="px-3 py-1 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                    <i class="bi bi-people-fill text-gray-300 dark:text-gray-500"></i> {{ $event->participants_count }}
                                </span>
                            </div>

                            <div
                                class="w-10 h-10 md:w-14 md:h-14 rounded-2xl flex items-center justify-center font-black text-lg md:text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 mb-6 
                                                                                                                                                                                            {{ $event->eventType->name == 'Culto' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' :
                    ($event->eventType->name == 'Batismo' ? 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400') }}">
                                <i
                                    class="bi {{ $event->eventType->name == 'Culto' ? 'bi-church' : ($event->eventType->name == 'Batismo' ? 'bi-droplet-fill' : 'bi-calendar-event') }}"></i>
                            </div>

                            <div class="mb-4">
                                <h4
                                    class="text-lg font-black text-gray-900 dark:text-white leading-tight mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors uppercase tracking-tighter">
                                    {{ $event->name }}
                                </h4>
                                <span
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $event->eventType->name }}</span>
                            </div>

                            <div class="space-y-3 mb-6 flex-1">
                                <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-sm">
                                        <i class="bi bi-calendar-event text-blue-500 dark:text-blue-400"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-xs font-black uppercase text-gray-900 dark:text-white">{{ $event->date->format('d/m/Y') }}</span>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase">{{ $event->date->translatedFormat('l') }}</span>
                                        @if($event->end_date)
                                            <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase mt-1">
                                                Até {{ $event->end_date->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($event->location)
                                    <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-sm font-bold">
                                            <i class="bi bi-geo-alt text-red-500 dark:text-red-400"></i>
                                        </div>
                                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate">{{ $event->location }}</span>
                                    </div>
                                @endif

                                @if($event->cell || $event->zone)
                                    <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-sm font-bold">
                                            <i class="bi bi-diagram-3 text-purple-500 dark:text-purple-400"></i>
                                        </div>
                                        <span class="text-[10px] font-black uppercase text-gray-900 dark:text-white">
                                            {{ $event->cell ? 'Célula: ' . $event->cell->name : 'Zona: ' . $event->zone->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 pt-4 border-t border-gray-50 dark:border-gray-700">
                                <a href="{{ route('events.show', $event) }}"
                                    class="flex-1 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-300 text-center py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition-all flex items-center justify-center gap-2">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                @can('update', $event)
                                    <a href="{{ route('events.edit', $event) }}"
                                        class="w-10 h-10 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-300 flex items-center justify-center rounded-xl hover:bg-orange-500 hover:text-white dark:hover:bg-orange-500 dark:hover:text-white transition-all">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endcan
                                @can('delete', $event)
                                    <form action="{{ route('events.destroy', $event) }}" method="POST"
                                        id="grid-delete-event-{{ $event->id }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            onclick="confirmDelete('grid-delete-event-{{ $event->id }}', 'Deseja excluir este evento?')"
                                            class="w-10 h-10 bg-red-50 dark:bg-red-900/30 text-red-400 flex items-center justify-center rounded-xl hover:bg-red-600 hover:text-white transition-all">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                @empty
                    <div
                        class="col-span-full py-20 bg-white dark:bg-gray-800 rounded-[2rem] border border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center gap-4 text-gray-300 dark:text-gray-600">
                        <i class="bi bi-calendar-x text-7xl opacity-20"></i>
                        <p class="font-bold text-lg">Nenhum evento encontrado</p>
                    </div>
                @endforelse
            </div>
            <div class="px-8 py-6">
                {{ $events->links() }}
            </div>
        </div>

        <!-- Calendar View -->
        <div id="view-calendar" x-show="view === 'calendar'" class="transition-opacity duration-300">
            <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden p-6">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- FullCalendar CSS -->
    <style>
        .fc {
            font-family: inherit;
        }

        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 900 !important;
            text-transform: uppercase;
            letter-spacing: -0.05em;
            color: #111827;
        }

        /* Calendar Dark Mode Overrides */
        [data-theme="dark"] .fc-toolbar-title {
            color: #f3f4f6 !important;
        }

        [data-theme="dark"] .fc-button {
            background-color: #1f2937 !important;
            color: #d1d5db !important;
            border-color: #374151 !important;
        }

        [data-theme="dark"] .fc-button:hover {
            background-color: #374151 !important;
            color: #60a5fa !important;
            border-color: #60a5fa !important;
        }

        [data-theme="dark"] .fc-button-active {
            background-color: #2563eb !important;
            color: white !important;
        }

        [data-theme="dark"] .fc-daygrid-day-number {
            color: #9ca3af;
        }

        [data-theme="dark"] .fc-col-header-cell-cushion {
            color: #6b7280;
        }

        [data-theme="dark"] .fc-day-today {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }

        [data-theme="dark"] .event-tooltip {
            background: #1f2937;
            border-color: #374151;
            color: #f3f4f6;
        }

        [data-theme="dark"] .tooltip-header {
            color: #f3f4f6;
        }

        [data-theme="dark"] .tooltip-row {
            color: #9ca3af;
        }


        .fc-button:hover {
            background-color: #f9fafb !important;
            color: #2563eb !important;
            border-color: #bfdbfe !important;
        }

        .fc-button-active {
            background-color: #2563eb !important;
            color: white !important;
            border-color: #2563eb !important;
        }

        .fc-daygrid-day-number {
            font-weight: 700;
            color: #6b7280;
            text-decoration: none !important;
            padding: 0.5rem !important;
        }

        .fc-col-header-cell-cushion {
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 900;
            letter-spacing: 0.1em;
            color: #9ca3af;
            padding: 1rem 0 !important;
        }

        .fc-event {
            border: none !important;
            border-radius: 0.375rem !important;
            padding: 4px 6px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            cursor: pointer;
            transition: all 0.2s;
        }

        .fc-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .fc-day-today {
            background-color: #eff6ff !important;
        }

        .fc-daygrid-event {
            margin: 2px 4px !important;
        }

        /* Tooltip Styles */
        .event-tooltip {
            position: absolute;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            min-width: 250px;
            display: none;
        }

        .event-tooltip.show {
            display: block;
        }

        .tooltip-header {
            font-weight: 800;
            font-size: 0.95rem;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .tooltip-row {
            display: flex;
            align-items: center;
            margin: 0.4rem 0;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .tooltip-row i {
            margin-right: 0.5rem;
            width: 16px;
        }
    </style>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/pt.global.min.js'></script>
    <script>
        let calendar;
        let tooltip = null;

        function toggleView(view) {
            // Deprecated: Logic moved to AlpineJS
        }

        // Old bulk logic removed


            function createTooltip() {
                tooltip = document.createElement('div');
                tooltip.className = 'event-tooltip';
                document.body.appendChild(tooltip);
            }

        function showTooltip(info) {
            const event = info.event;
            const props = event.extendedProps;

            let tooltipContent = `
                                                                                    <div class="tooltip-header">${event.title}</div>
                                                                                    <div class="tooltip-row">
                                                                                        <i class="bi bi-calendar-event"></i>
                                                                                        <span>${event.start.toLocaleDateString('pt-BR')}</span>
                                                                                    </div>
                                                                                `;

            if (event.end && event.end.getTime() !== event.start.getTime()) {
                const endDate = new Date(event.end);
                endDate.setDate(endDate.getDate() - 1); // FullCalendar exclusive end
                tooltipContent += `
                                                                                        <div class="tooltip-row">
                                                                                            <i class="bi bi-arrow-right"></i>
                                                                                            <span>${endDate.toLocaleDateString('pt-BR')}</span>
                                                                                        </div>
                                                                                    `;
            }

            if (props.location) {
                tooltipContent += `
                                                                                        <div class="tooltip-row">
                                                                                            <i class="bi bi-geo-alt-fill"></i>
                                                                                            <span>${props.location}</span>
                                                                                        </div>
                                                                                    `;
            }

            if (props.participants_count !== undefined) {
                tooltipContent += `
                                                                                        <div class="tooltip-row">
                                                                                            <i class="bi bi-people-fill"></i>
                                                                                            <span>${props.participants_count} participantes</span>
                                                                                        </div>
                                                                                    `;
            }

            if (props.description) {
                tooltipContent += `
                                                                                        <div class="tooltip-row" style="margin-top: 0.5rem; font-style: italic;">
                                                                                            <i class="bi bi-info-circle"></i>
                                                                                            <span>${props.description.substring(0, 100)}${props.description.length > 100 ? '...' : ''}</span>
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
            createTooltip();

            var calendarEl = document.getElementById('calendar');
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
                events: '{{ route("events.feed") }}',
                eventClick: function (info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                },
                eventMouseEnter: function (info) {
                    showTooltip(info);
                },
                eventMouseLeave: function (info) {
                    hideTooltip();
                },
                eventDidMount: function (info) {
                    // Add icon to event
                    const event = info.event;
                    const props = event.extendedProps;

                    let icon = 'bi-calendar-event';
                    if (props.event_type === 'Culto') icon = 'bi-church';
                    if (props.event_type === 'Batismo') icon = 'bi-droplet-fill';

                    const iconEl = document.createElement('i');
                    iconEl.className = `bi ${icon} mr-1`;
                    info.el.querySelector('.fc-event-title').prepend(iconEl);
                },
                dateClick: function (info) {
                    window.location.href = '{{ route("events.create") }}?date=' + info.dateStr;
                },
                loading: function (isLoading) {
                    if (isLoading) {
                        calendarEl.style.opacity = '0.5';
                    } else {
                        calendarEl.style.opacity = '1';
                    }
                }
            });
            calendar.render();
        }

        document.addEventListener('DOMContentLoaded', function () {
            createTooltip();
            // view logic handled by alpine
        });

        // Dynamic Search Script
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.querySelector('input[name="search"]');
            const filterForm = searchInput.closest('form');

            if (searchInput) {
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

                                // Replace Views
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