@extends('layouts.app')

@section('title', 'Meu Dashboard - Portal Life Church')
@section('page-title', 'Meu Dashboard')
@section('page-subtitle', 'Bem-vindo de volta, ' . $authUser->name)

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Área do Membro</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Bem-vindo, {{ $authUser->name }}</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Acompanhe o seu compromisso, contribuições, célula e próximos eventos.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('contributions.index', ['mine' => 1]) }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-clock-history text-orange-700"></i> <span>Ver Histórico</span>
                </a>
                <a href="{{ route('contributions.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-plus-lg"></i> <span>Nova Contribuição</span>
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Meu Compromisso -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500">Meu Compromisso</p>
                    <a href="{{ route('commitments.index') }}" class="text-[11px] font-bold text-orange-800 hover:underline">
                        Alterar <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                @if($commitment)
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">
                            {{ $commitment->package->name }}
                        </p>
                        <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">
                            {{ number_format($commitment->package->min_amount, 0, ',', '.') }} -
                            @if($commitment->package->max_amount)
                                {{ number_format($commitment->package->max_amount, 0, ',', '.') }}
                            @else
                                ∞
                            @endif
                            MT
                        </p>
                        <p class="text-[10px] text-gray-400 mt-1">
                            Desde {{ $commitment->start_date->format('d/m/Y') }}
                        </p>
                    </div>
                @else
                    <div class="flex items-center justify-between gap-3 pt-1">
                        <p class="text-xs text-gray-500">Sem compromisso ativo</p>
                        <a href="{{ route('commitments.index') }}" class="rounded-lg bg-orange-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-orange-800">
                            Escolher Agora
                        </a>
                    </div>
                @endif
            </article>

            <!-- Total Este Mês -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500">Total Este Mês</p>
                    <i class="bi bi-cash-stack text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">
                        {{ number_format($totalThisMonth, 2, ',', '.') }} MT
                    </p>
                    <p class="text-[10px] text-gray-400 mt-1">Contribuição acumulada (Dia 20 ao 5)</p>
                </div>
            </article>

            <!-- Ações Rápidas -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <p class="text-[11px] sm:text-xs font-bold text-gray-500 mb-2.5">Próximos Passos</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('contributions.create') }}"
                        class="flex items-center justify-center gap-1.5 rounded-lg bg-orange-700 px-3 py-2 text-xs font-bold text-white hover:bg-orange-800 transition">
                        <i class="bi bi-plus-lg"></i>
                        <span>Contribuir</span>
                    </a>
                    <a href="{{ route('contributions.index', ['mine' => 1]) }}"
                        class="flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                        <i class="bi bi-clock-history text-orange-700"></i>
                        <span>Histórico</span>
                    </a>
                </div>
            </article>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Últimas Contribuições -->
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Minhas Contribuições</h3>
                        <p class="text-xs text-gray-500">Registos recentes</p>
                    </div>
                    <a href="{{ route('contributions.index', ['mine' => 1]) }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todas</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($contributions as $contribution)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3 font-bold text-gray-900 text-xs sm:text-sm tabular-nums">
                                        {{ $contribution->contribution_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($contribution->status === 'verificada')
                                            <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-[10px] font-black uppercase">
                                                ✓ Verificada
                                            </span>
                                        @elseif($contribution->status === 'pendente')
                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[10px] font-black uppercase">
                                                ⌚ Pendente
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-[10px] font-black uppercase">
                                                ✕ Rejeitada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-gray-950 text-xs sm:text-sm tabular-nums">
                                        {{ number_format($contribution->amount, 2, ',', '.') }} MT
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 text-xs">
                                        Nenhuma contribuição registada recentemente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Próximos Eventos -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm" x-data="{ activeEvent: null }">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Próximos Eventos</h3>
                        <p class="text-xs text-gray-500">Clique num evento para detalhes</p>
                    </div>
                    <a href="{{ route('events.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Calendário</a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingEvents as $event)
                        <div @click="activeEvent = {{ $event->toJson() }}"
                            class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors border-b border-gray-100 last:border-0">
                            <div class="bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-center min-w-[54px]">
                                <span class="block text-base font-black leading-none text-gray-900 tabular-nums">{{ $event->date->format('d') }}</span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500">{{ $event->date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 text-xs sm:text-sm truncate">
                                    {{ $event->name }}
                                </h4>
                                <p class="text-[11px] text-gray-500 flex items-center mt-0.5 truncate">
                                    <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Life Church' }}
                                    @if($event->end_date)
                                        <span class="ml-2 px-1.5 py-0.5 bg-orange-50 text-orange-800 rounded text-[9px] font-bold">
                                            Até {{ $event->end_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 bg-gray-100 rounded-full text-[9px] font-black uppercase text-gray-600">
                                {{ $event->eventType->name ?? 'Evento' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 text-xs py-6">Nenhum evento programado.</p>
                    @endforelse
                </div>

                <!-- Modal de Detalhes do Evento -->
                <div x-show="activeEvent" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="activeEvent = null"></div>
                    <div class="relative bg-white rounded-xl p-5 sm:p-6 max-w-lg w-full shadow-lg border border-gray-200">
                        <button @click="activeEvent = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700">
                            <i class="bi bi-x-lg text-base"></i>
                        </button>

                        <div class="mb-4">
                            <span class="inline-block px-2.5 py-0.5 bg-orange-50 text-orange-800 rounded-full text-[10px] font-black uppercase tracking-wider mb-1.5"
                                x-text="activeEvent?.event_type?.name ?? 'Evento'"></span>
                            <h3 class="text-lg font-black text-gray-950" x-text="activeEvent?.name"></h3>
                        </div>

                        <div class="space-y-3 mb-5">
                            <div class="flex items-center text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <i class="bi bi-calendar-event text-base mr-3 text-orange-700"></i>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase">Data e Hora</p>
                                    <p class="font-bold text-xs sm:text-sm">
                                        <span x-text="new Date(activeEvent?.date).toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></span>
                                        <template x-if="activeEvent?.end_date">
                                            <span class="text-orange-700">
                                                - até <span x-text="new Date(activeEvent.end_date).toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></span>
                                            </span>
                                        </template>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <i class="bi bi-geo-alt text-base mr-3 text-orange-700"></i>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase">Local</p>
                                    <p class="font-bold text-xs sm:text-sm" x-text="activeEvent?.location ?? 'Life Church'"></p>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200" x-show="activeEvent?.description">
                                <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">Detalhes</p>
                                <p class="text-xs leading-relaxed text-gray-600" x-text="activeEvent?.description"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <button @click="activeEvent = null"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                Fechar
                            </button>
                            <a href="{{ route('events.index') }}"
                                class="w-full rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white flex items-center justify-center hover:bg-orange-800">
                                Ver no Calendário
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div>
            @if(isset($myCell) && $myCell)
                <!-- Minha Célula (Gestão Eclesiástica) -->
                <section id="minha-celula" class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">Minha Célula (Gestão)</span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-orange-50 px-2 py-0.5 text-[9px] font-black uppercase text-orange-800">
                                    <span class="h-1.5 w-1.5 rounded-full bg-orange-600"></span> Ativa
                                </span>
                            </div>
                            <h3 class="text-lg font-black text-gray-950 mt-0.5">{{ $myCell->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <i class="bi bi-person-badge mr-1"></i> Líder: {{ $myCell->leader->name ?? 'N/A' }}
                            </p>
                            @if(isset($attendanceStats['last_attendance']) && $attendanceStats['last_attendance'])
                                <p class="text-[11px] font-semibold text-emerald-700 mt-1">
                                    Última presença: {{ \Carbon\Carbon::parse($attendanceStats['last_attendance'])->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-3 sm:w-64">
                            <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg text-center">
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Presenças</p>
                                <p class="text-lg font-black text-gray-950 tabular-nums">{{ $attendanceStats['total_present'] ?? 0 }}</p>
                            </div>
                            <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg text-center">
                                <p class="text-[10px] font-bold text-gray-500 uppercase">Últ. 3 Meses</p>
                                <p class="text-lg font-black text-gray-950 tabular-nums">{{ $attendanceStats['last_3_months_present'] ?? 0 }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @php
                                $cellDetailsLink = ($authUser->isLider() || $authUser->isSupervisor() || $authUser->isPastorZona() || $authUser->isAdmin())
                                    ? route('cells.show', $myCell)
                                    : route('dashboard.membro') . '#minha-celula';
                            @endphp
                            <a href="{{ $cellDetailsLink }}"
                                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-gray-900 px-3.5 py-2 text-xs font-bold text-white hover:bg-gray-800">
                                <i class="bi bi-diagram-3"></i>
                                Ver Detalhes
                            </a>

                            @if($authUser->isLider() || $authUser->isSupervisor() || $authUser->isPastorZona() || $authUser->isAdmin())
                                <a href="{{ route('members.index', ['cell_id' => $myCell->id]) }}"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800">
                                    <i class="bi bi-person-lines-fill"></i>
                                    Gerir Membros
                                </a>
                            @endif
                        </div>
                    </div>
                </section>
            @else
                <!-- Sem célula vinculada -->
                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-950">Minha Célula (Gestão)</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Você não pertence a nenhuma célula neste momento. Contacte o seu líder, supervisor ou administração para ser vinculado a uma célula.
                        </p>
                    </div>
                    <a href="{{ route('members.index') }}"
                        class="shrink-0 inline-flex items-center justify-center rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800">
                        Ver Estrutura de Membros
                    </a>
                </section>
            @endif
        </div>
    </div>
@endsection
