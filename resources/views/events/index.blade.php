@extends('layouts.app')

@section('title', 'Eventos e Cerimônias')
@section('page-title', 'Eventos e Cerimônias')
@section('page-subtitle', 'Gestão de cultos, batismos e eventos especiais')

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase leading-none">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Eventos</span>
                    <span class="text-gray-300">& Cerimônias</span>
                </h2>
            </div>

            <div class="flex gap-3">
                <!-- View Toggle -->
                <div class="bg-gray-100 p-1 rounded-xl flex items-center">
                    <button onclick="toggleView('list')" id="btn-list"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300 bg-white text-gray-900 shadow-sm">
                        <i class="bi bi-list-ul mr-2"></i> Lista
                    </button>
                    <button onclick="toggleView('calendar')" id="btn-calendar"
                        class="px-4 py-2 rounded-lg text-sm font-bold text-gray-500 hover:text-gray-900 transition-all duration-300">
                        <i class="bi bi-calendar-week mr-2"></i> Calendário
                    </button>
                </div>

                <a href="{{ route('events.create') }}"
                    class="flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm transition-all duration-300 shadow-lg shadow-blue-600/30">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Evento
                </a>
            </div>
        </div>

        <!-- List View -->
        <div id="view-list" class="transition-opacity duration-300">
            <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Data</th>
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Evento</th>
                                <th
                                    class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Localização</th>
                                <th
                                    class="px-8 py-5 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Participantes</th>
                                <th
                                    class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($events as $event)
                                                <tr class="group hover:bg-gray-50/50 transition-colors duration-200">
                                                    <td class="px-8 py-5 whitespace-nowrap">
                                                        <div class="flex flex-col">
                                                            <span
                                                                class="text-sm font-black text-gray-900">{{ $event->date->format('d/m/Y') }}</span>
                                                            <span
                                                                class="text-[10px] font-bold text-gray-400 uppercase">{{ $event->date->translatedFormat('l') }}</span>
                                                            @if($event->end_date)
                                                                <span class="text-[10px] font-bold text-blue-500 mt-1">até
                                                                    {{ $event->end_date->format('d/m/Y') }}</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-8 py-5">
                                                        <div class="flex items-center">
                                                            <div
                                                                class="w-10 h-10 rounded-xl flex items-center justify-center mr-4 
                                                                    {{ $event->eventType->name == 'Culto' ? 'bg-amber-100 text-amber-600' :
                                ($event->eventType->name == 'Batismo' ? 'bg-cyan-100 text-cyan-600' : 'bg-blue-100 text-blue-600') }}">
                                                                <i
                                                                    class="bi {{ $event->eventType->name == 'Culto' ? 'bi-church' : ($event->eventType->name == 'Batismo' ? 'bi-droplet-fill' : 'bi-calendar-event') }} text-lg"></i>
                                                            </div>
                                                            <div>
                                                                <span class="block text-sm font-bold text-gray-900">{{ $event->name }}</span>
                                                                <span class="text-xs text-gray-400">{{ $event->eventType->name }}</span>
                                                                @if($event->cell)
                                                                    <span class="text-xs text-gray-500 block">Célula:
                                                                        {{ $event->cell->name }}</span>
                                                                @elseif($event->zone)
                                                                    <span class="text-xs text-gray-500 block">Zona: {{ $event->zone->name }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-8 py-5">
                                                        <div class="flex items-center text-gray-500">
                                                            <i class="bi bi-geo-alt-fill mr-2 text-gray-300"></i>
                                                            <span
                                                                class="text-sm font-medium">{{ $event->location ?? 'Local não definido' }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-8 py-5 text-center">
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-bold">
                                                            <i class="bi bi-people-fill mr-2 text-gray-400"></i>
                                                            {{ $event->participants_count }}
                                                        </span>
                                                    </td>
                                                    <td class="px-8 py-5 text-right">
                                                        <div
                                                            class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                            <a href="{{ route('events.pdf', $event) }}" target="_blank"
                                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                                title="PDF">
                                                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                                            </a>
                                                            <a href="{{ route('events.edit', $event) }}"
                                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                                                title="Editar">
                                                                <i class="bi bi-pencil-fill"></i>
                                                            </a>
                                                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline"
                                                                onsubmit="return confirm('Tem certeza que deseja excluir este evento?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                                    title="Excluir">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-12 text-center text-gray-400">
                                        <i class="bi bi-calendar-x text-4xl mb-4 block opacity-20"></i>
                                        <p class="text-sm font-medium">Nenhum evento encontrado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-6 border-t border-gray-100">
                    {{ $events->links() }}
                </div>
            </div>
        </div>

        <!-- Calendar View -->
        <div id="view-calendar" class="hidden transition-opacity duration-300">
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

        .fc-button {
            background-color: white !important;
            color: #4b5563 !important;
            border: 1px solid #e5e7eb !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.75rem !important;
            letter-spacing: 0.05em;
            border-radius: 0.5rem !important;
            padding: 0.5rem 1rem !important;
            box-shadow: none !important;
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
            const listBtn = document.getElementById('btn-list');
            const calendarBtn = document.getElementById('btn-calendar');
            const listView = document.getElementById('view-list');
            const calendarView = document.getElementById('view-calendar');

            if (view === 'list') {
                listBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                listBtn.classList.remove('text-gray-500');
                calendarBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                calendarBtn.classList.add('text-gray-500');

                listView.classList.remove('hidden');
                calendarView.classList.add('hidden');
            } else {
                calendarBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                calendarBtn.classList.remove('text-gray-500');
                listBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                listBtn.classList.add('text-gray-500');

                listView.classList.add('hidden');
                calendarView.classList.remove('hidden');

                if (!calendar) {
                    initCalendar();
                } else {
                    calendar.render();
                }
            }
        }

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
    </script>
@endsection