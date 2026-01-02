@extends('layouts.app')

@section('title', 'Calendário de Casamentos')
@section('page-title', 'Calendário de Casamentos')
@section('page-subtitle', 'Gestão de casamentos e eventos matrimoniais')

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
            <div>
                <h2 class="text-4xl font-black text-gray-900 tracking-tighter uppercase leading-none">
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-amber-500">Calendário</span>
                    <span class="text-gray-300">de Casamentos</span>
                </h2>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('weddings.pdf', ['year' => now()->year]) }}" target="_blank"
                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-orange-600 hover:border-orange-200 transition-all duration-300 shadow-sm"
                    title="Exportar PDF do Ano">
                    <i class="bi bi-file-earmark-pdf-fill text-lg"></i>
                </a>

                <a href="{{ route('weddings.create') }}"
                    class="flex items-center px-6 py-2 bg-gray-900 hover:bg-black text-white rounded-xl font-bold text-sm transition-all duration-300 shadow-lg shadow-gray-900/20">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Agendamento
                </a>
            </div>
        </div>

        <div class="flex flex-col xl:flex-row gap-8">
            <!-- Calendar Grid -->
            <div
                class="flex-1 bg-white rounded-[2rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden p-6">
                <div id="calendar"></div>
            </div>

            <!-- Sidebar -->
            <div class="w-full xl:w-80 space-y-6">
                <!-- Summary Card -->
                <div class="bg-gray-900 rounded-[2rem] p-8 text-white relative overflow-hidden shadow-2xl">
                    <div
                        class="absolute top-0 right-0 w-40 h-40 bg-orange-500 rounded-full blur-[60px] opacity-20 -mr-10 -mt-10">
                    </div>

                    <h3 class="font-bold text-gray-400 text-xs uppercase tracking-widest mb-8">Resumo {{ now()->year }}</h3>

                    <div class="space-y-6 relative z-10">
                        <div class="flex items-center justify-between">
                            <span class="text-3xl font-black">{{ $totalCount }}</span>
                            <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Total</span>
                        </div>
                        <div class="w-full h-px bg-white/10"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-green-400">{{ $completedCount }}</span>
                            <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Realizados</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-orange-400">{{ $upcomingCount }}</span>
                            <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Próximos</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-red-400">{{ $cancelledCount ?? 0 }}</span>
                            <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Cancelados</span>
                        </div>
                    </div>
                </div>

                <!-- Upcoming List -->
                @if($upcoming->isNotEmpty())
                    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100">
                        <h3 class="text-gray-900 font-black uppercase tracking-widest text-xs mb-6">Próximos Eventos</h3>
                        <div class="space-y-4">
                            @foreach($upcoming as $wedding)
                                <a href="{{ route('weddings.edit', $wedding) }}" class="block group">
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex-shrink-0 w-12 text-center bg-orange-50 rounded-xl py-2 group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300">
                                            <span
                                                class="block text-xs font-bold uppercase opacity-60">{{ $wedding->date->translatedFormat('M') }}</span>
                                            <span
                                                class="block text-lg font-black leading-none">{{ $wedding->date->format('d') }}</span>
                                        </div>
                                        <div>
                                            <h4
                                                class="font-bold text-gray-900 text-sm group-hover:text-orange-600 transition-colors">
                                                {{ explode(' ', $wedding->groom_name)[0] }} &
                                                {{ explode(' ', $wedding->bride_name)[0] }}
                                            </h4>
                                            <p class="text-xs text-gray-400 mt-1">{{ $wedding->date->diffForHumans() }}</p>
                                            @if($wedding->location)
                                                <p class="text-xs text-gray-500 mt-1"><i
                                                        class="bi bi-geo-alt-fill mr-1"></i>{{ $wedding->location }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
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
            color: #ea580c !important;
            border-color: #fed7aa !important;
        }

        .fc-button-active {
            background-color: #ea580c !important;
            color: white !important;
            border-color: #ea580c !important;
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
            background-color: #fff7ed !important;
        }

        .fc-daygrid-event {
            margin: 2px 4px !important;
        }

        /* Tooltip Styles */
        .wedding-tooltip {
            position: absolute;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            min-width: 280px;
            display: none;
        }

        .wedding-tooltip.show {
            display: block;
        }

        .tooltip-header {
            font-weight: 800;
            font-size: 1rem;
            color: #111827;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
        }

        .tooltip-header i {
            margin-right: 0.5rem;
            color: #ea580c;
        }

        .tooltip-row {
            display: flex;
            align-items: center;
            margin: 0.5rem 0;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .tooltip-row i {
            margin-right: 0.5rem;
            width: 18px;
            color: #9ca3af;
        }

        .tooltip-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
        }

        .status-scheduled {
            background: #fed7aa;
            color: #ea580c;
        }

        .status-completed {
            background: #bbf7d0;
            color: #16a34a;
        }

        .status-cancelled {
            background: #fecaca;
            color: #dc2626;
        }
    </style>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/pt.global.min.js'></script>
    <script>
        let tooltip = null;

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
                        <span>${event.start.toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                    </div>
                `;

            if (props.time) {
                tooltipContent += `
                        <div class="tooltip-row">
                            <i class="bi bi-clock-fill"></i>
                            <span>${props.time}</span>
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

            if (props.godparents) {
                tooltipContent += `
                        <div class="tooltip-row">
                            <i class="bi bi-people-fill"></i>
                            <span>Padrinhos: ${props.godparents.substring(0, 50)}${props.godparents.length > 50 ? '...' : ''}</span>
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

        document.addEventListener('DOMContentLoaded', function () {
            createTooltip();

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
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
                eventMouseEnter: function (info) {
                    showTooltip(info);
                },
                eventMouseLeave: function (info) {
                    hideTooltip();
                },
                eventDidMount: function (info) {
                    const event = info.event;
                    const props = event.extendedProps;

                    // Add heart icon to event
                    const iconEl = document.createElement('i');
                    iconEl.className = 'bi bi-heart-fill mr-1';
                    info.el.querySelector('.fc-event-title').prepend(iconEl);
                },
                dateClick: function (info) {
                    window.location.href = '{{ route("weddings.create") }}?date=' + info.dateStr;
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
        });
    </script>
@endsection