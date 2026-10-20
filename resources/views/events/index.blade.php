@extends('layouts.app')

@section('title', 'Eventos e Cerimônias - Portal Life Church')
@section('page-title', 'Eventos e Cerimônias')
@section('page-subtitle', 'Gestão de cultos, batismos e celebrações especiais')

@section('header-actions')
    <div class="flex items-center gap-2">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('event-types.index') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
                title="Tipos de Eventos">
                <i class="bi bi-gear-fill text-xs text-orange-700"></i>
                <span class="hidden sm:inline">Tipos</span>
            </a>
        @endif
        @can('create', App\Models\Event::class)
            <a href="{{ route('events.create') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                <i class="bi bi-calendar-plus text-xs"></i>
                <span>Novo Evento</span>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="space-y-5" x-data="{ 
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
        @resize.window.debounce.500ms="updateView()">

        <!-- Filter Bar -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <form action="{{ route('events.index') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5 items-end">
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Buscar Evento</label>
                        <div class="relative">
                            <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" name="search" data-live-search="ajax" value="{{ request('search') }}"
                                class="w-full rounded-lg border-gray-300 pl-7 pr-3 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500"
                                placeholder="Nome do evento, local...">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">Tipo de Evento</label>
                        <select name="type" class="w-full rounded-lg border-gray-300 px-2 py-1.5 text-xs focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Todos os Tipos</option>
                            <option value="culto" {{ request('type') == 'culto' ? 'selected' : '' }}>Culto</option>
                            <option value="batismo" {{ request('type') == 'batismo' ? 'selected' : '' }}>Batismo</option>
                            <option value="evento" {{ request('type') == 'evento' ? 'selected' : '' }}>Outros</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-orange-700 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-orange-800 transition-colors shadow-sm">
                            <i class="bi bi-filter text-xs"></i> Filtrar
                        </button>
                        <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                            <i class="bi bi-x-lg text-xs"></i>
                        </a>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <span class="text-xs text-gray-500 font-semibold">{{ $events->total() }} eventos cadastrados</span>
                    <div class="flex items-center rounded-lg border border-gray-200 p-0.5 bg-gray-50">
                        <button type="button" @click="view = 'list'"
                            :class="view === 'list' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-900'"
                            class="rounded-md px-2 py-1 text-xs transition-colors" title="Visualização em Lista">
                            <i class="bi bi-list-ul"></i>
                        </button>
                        <button type="button" @click="view = 'grid'"
                            :class="view === 'grid' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-900'"
                            class="rounded-md px-2 py-1 text-xs transition-colors" title="Visualização em Grade">
                            <i class="bi bi-grid-fill"></i>
                        </button>
                        <button type="button" @click="view = 'calendar'; setTimeout(() => initCalendar(), 100)"
                            :class="view === 'calendar' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-500 hover:text-gray-900'"
                            class="rounded-md px-2 py-1 text-xs transition-colors" title="Visualização em Calendário">
                            <i class="bi bi-calendar-week"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- List View -->
        <div id="view-list" x-show="view === 'list'" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-[10px] uppercase font-black tracking-wider text-gray-500 border-b border-gray-200">
                        <tr>
                            @if(auth()->user()->role === 'admin')
                                <th class="px-4 py-2.5 w-8">
                                    <input type="checkbox" @click="toggleAll()"
                                        :checked="selected.length === {{ $events->count() }} && selected.length > 0"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                </th>
                            @endif
                            <th class="px-4 py-2.5">Data</th>
                            <th class="px-4 py-2.5">Evento / Tipo</th>
                            <th class="px-4 py-2.5">Localização</th>
                            <th class="px-4 py-2.5 text-center">Participantes</th>
                            <th class="px-4 py-2.5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($events as $event)
                            <tr class="hover:bg-gray-50/70 transition-colors"
                                :class="{'bg-orange-50/40': selected.includes({{ $event->id }})}">
                                @if(auth()->user()->role === 'admin')
                                    <td class="px-4 py-2.5">
                                        <input type="checkbox" value="{{ $event->id }}" x-model="selected"
                                            class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                    </td>
                                @endif
                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    <p class="font-bold text-gray-900">{{ $event->date->format('d/m/Y') }}</p>
                                    <span class="text-[10px] text-gray-400 block">{{ $event->date->translatedFormat('l') }}</span>
                                    @if($event->isPassed())
                                        <span class="inline-flex items-center rounded px-1.5 py-0.2 text-[9px] font-bold bg-gray-100 text-gray-500 mt-0.5">Realizado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg font-bold text-xs shrink-0
                                            {{ $event->eventType->name == 'Culto' ? 'bg-amber-50 text-amber-700' :
                                                ($event->eventType->name == 'Batismo' ? 'bg-cyan-50 text-cyan-700' : 'bg-orange-50 text-orange-700') }}">
                                            <i class="bi {{ $event->eventType->name == 'Culto' ? 'bi-church' : ($event->eventType->name == 'Batismo' ? 'bi-droplet-fill' : 'bi-calendar-event') }}"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('events.show', $event) }}" class="font-bold text-gray-900 hover:text-orange-700 transition-colors">
                                                {{ $event->name }}
                                            </a>
                                            <span class="text-[10px] text-gray-400 block">{{ $event->eventType->name }}</span>
                                            @if($event->cell)
                                                <span class="text-[10px] text-gray-500 block">Célula: {{ $event->cell->name }}</span>
                                            @elseif($event->zone)
                                                <span class="text-[10px] text-gray-500 block">Zona: {{ $event->zone->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="text-gray-700 block truncate max-w-xs">{{ $event->location ?? 'Local não definido' }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-700">
                                        {{ $event->participants_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('events.show', $event) }}"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-gray-300 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                                            title="Ver detalhes">
                                            <i class="bi bi-eye text-xs"></i>
                                        </a>
                                        @can('update', $event)
                                            <a href="{{ route('events.edit', $event) }}"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-orange-300 hover:text-orange-700 hover:bg-orange-50 transition-colors"
                                                title="Editar">
                                                <i class="bi bi-pencil-square text-xs"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $event)
                                            <form action="{{ route('events.destroy', $event) }}" method="POST"
                                                id="list-delete-event-{{ $event->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="confirmDelete('list-delete-event-{{ $event->id }}', 'Deseja excluir este evento?')"
                                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                    title="Excluir">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-xs text-gray-400 italic">
                                    Nenhum evento encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grid View -->
        <div id="view-grid" x-show="view === 'grid'" style="display: none;" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @forelse($events as $event)
                <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm hover:border-orange-300 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200/50">
                                {{ $event->eventType->name }}
                            </span>
                            <span class="text-[10px] font-bold text-gray-400">{{ $event->date->format('d/m/Y') }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 truncate">{{ $event->name }}</h4>
                        @if($event->location)
                            <p class="text-[11px] text-gray-500 truncate mt-0.5"><i class="bi bi-geo-alt text-[10px]"></i> {{ $event->location }}</p>
                        @endif
                        @if($event->cell || $event->zone)
                            <p class="text-[10px] text-gray-400 truncate mt-0.5">{{ $event->cell ? 'Célula: ' . $event->cell->name : 'Zona: ' . $event->zone->name }}</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-1.5 pt-3 border-t border-gray-100 mt-3">
                        <a href="{{ route('events.show', $event) }}"
                            class="flex-1 text-center py-1 rounded-md bg-gray-50 text-gray-700 hover:bg-gray-100 text-xs font-bold border border-gray-200">
                            Ver
                        </a>
                        @can('update', $event)
                            <a href="{{ route('events.edit', $event) }}"
                                class="inline-flex h-6 w-6 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:text-orange-700">
                                <i class="bi bi-pencil text-[11px]"></i>
                            </a>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-200 p-8 text-center text-xs text-gray-400 italic">
                    Nenhum evento encontrado.
                </div>
            @endforelse
        </div>

        <!-- Calendar View -->
        <div id="view-calendar" x-show="view === 'calendar'" style="display: none;" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div id="calendar"></div>
        </div>

        @if($events->hasPages())
            <div class="border-t border-gray-200 pt-3">
                {{ $events->links() }}
            </div>
        @endif

        <!-- Floating Bulk Bar -->
        <div x-show="selected.length > 0" x-cloak
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 rounded-xl bg-gray-900 text-white px-4 py-2.5 shadow-xl flex items-center gap-3 border border-gray-700 text-xs">
            <span class="font-bold"><span x-text="selected.length"></span> selecionado(s)</span>
            <div class="h-4 w-px bg-gray-700"></div>
            @if(auth()->user()->role === 'admin')
                <form method="POST" action="{{ route('events.bulk-delete') }}" onsubmit="return confirm('Tem certeza que deseja excluir os eventos selecionados?')" class="inline">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="event_ids[]" :value="id">
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
    </div>

    <!-- FullCalendar Logic -->
    <script>
        var calendar = null;
        var tooltip = null;

        function createTooltip() {
            if (!tooltip) {
                tooltip = document.createElement('div');
                tooltip.className = 'event-tooltip fixed z-[100] bg-gray-900 text-white text-xs rounded-lg p-2.5 shadow-xl pointer-events-none opacity-0 transition-opacity';
                document.body.appendChild(tooltip);
            }
        }

        function showTooltip(info) {
            createTooltip();
            const event = info.event;
            const props = event.extendedProps;

            let tooltipContent = `<div class="font-bold mb-1">${event.title}</div>`;
            if (props.location) tooltipContent += `<div class="text-[11px] text-gray-300">Local: ${props.location}</div>`;
            if (props.description) tooltipContent += `<div class="text-[11px] text-gray-400">${props.description.substring(0, 80)}...</div>`;

            tooltip.innerHTML = tooltipContent;
            tooltip.classList.remove('opacity-0');
            tooltip.classList.add('opacity-100');

            const rect = info.el.getBoundingClientRect();
            tooltip.style.left = rect.left + 'px';
            tooltip.style.top = (rect.bottom + 5) + 'px';
        }

        function hideTooltip() {
            if (tooltip) {
                tooltip.classList.remove('opacity-100');
                tooltip.classList.add('opacity-0');
            }
        }

        function initCalendar() {
            if (calendar) return;
            createTooltip();

            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth'
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
                eventMouseEnter: function (info) { showTooltip(info); },
                eventMouseLeave: function (info) { hideTooltip(); },
                dateClick: function (info) {
                    @can('create', App\Models\Event::class)
                    window.location.href = '{{ route("events.create") }}?date=' + info.dateStr;
                    @endcan
                }
            });
            calendar.render();
        }
    </script>
@endsection
