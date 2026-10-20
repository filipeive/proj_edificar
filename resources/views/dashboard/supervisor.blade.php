@extends('layouts.app')

@section('title', 'Dashboard Supervisor - Portal Life Church')
@section('page-title', 'Dashboard do Supervisor')
@section('page-subtitle', 'Monitorização da Supervisão ' . $supervisionName)

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gestão de Supervisão</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Supervisão {{ $supervisionName }}</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Monitorização de células, líderes, reuniões e participação financeira.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('cell-meetings.index') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-people text-orange-700"></i> <span>Encontros de Célula</span>
                </a>
                <a href="{{ route('reports.supervision') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-file-earmark-bar-graph"></i> <span>Relatório Supervisão</span>
                </a>
            </div>
        </header>

        @php
            $totalMembers = $cells->sum('members');
            $totalContributed = $cells->sum('contributed');
            $percentage = $totalMembers > 0 ? round(($totalContributed / $totalMembers) * 100, 1) : 0;
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
            <!-- Supervisão Info -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Supervisão</p>
                    <i class="bi bi-layers text-orange-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight truncate">{{ $supervisionName }}</p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">{{ $cells->count() }} Células Ativas</p>
            </article>

            <!-- Total Arrecadado -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Total da Supervisão</p>
                    <i class="bi bi-cash-coin text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">
                    {{ number_format($total, 2, ',', '.') }} MT
                </p>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Este mês</p>
            </article>

            <!-- Desempenho -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Taxa Geral de Participação</p>
                    <i class="bi bi-graph-up-arrow text-blue-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $percentage }}%</p>
                <div class="w-full bg-gray-100 h-1.5 rounded-full mt-1.5 overflow-hidden">
                    <div class="bg-orange-600 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                </div>
            </article>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Células da Supervisão -->
            <section class="lg:col-span-2 rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Células da Supervisão</h3>
                        <p class="text-xs text-gray-500">Indicadores por célula e líder</p>
                    </div>
                    <a href="{{ route('cells.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todas</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3">Célula</th>
                                <th class="px-4 py-3 text-center">Líder</th>
                                <th class="px-4 py-3 text-center">Membros</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($cells as $cell)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center font-black text-xs shrink-0">
                                                {{ strtoupper(substr($cell['name'], 0, 1)) }}
                                            </div>
                                            <span class="font-bold text-gray-900 text-xs sm:text-sm">{{ $cell['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-600">
                                        {{ $cell['leader'] }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 bg-gray-100 rounded-full text-[10px] font-black text-gray-700 tabular-nums">
                                            {{ $cell['contributed'] }}/{{ $cell['members'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-emerald-700 text-xs sm:text-sm tabular-nums">
                                        {{ number_format($cell['total'], 2, ',', '.') }} MT
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Ações Rápidas & Status -->
            <div class="space-y-4">
                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <h3 class="text-sm font-black text-gray-900 mb-3">Ações Rápidas</h3>
                    <div class="grid grid-cols-1 gap-2.5">
                        <a href="{{ route('reports.supervision') }}"
                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80 hover:bg-orange-50 hover:border-orange-200 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-orange-700 shrink-0">
                                <i class="bi bi-file-earmark-pdf text-sm"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Relatório Supervisão</span>
                        </a>
                        <a href="{{ route('cell-meetings.index') }}"
                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80 hover:bg-orange-50 hover:border-orange-200 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-orange-700 shrink-0">
                                <i class="bi bi-people text-sm"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Encontros de Célula</span>
                        </a>
                        <a href="{{ route('contributions.index') }}"
                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80 hover:bg-orange-50 hover:border-orange-200 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-orange-700 shrink-0">
                                <i class="bi bi-cash-coin text-sm"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Contribuições</span>
                        </a>
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <h3 class="text-sm font-black text-gray-900 mb-1.5">Status da Supervisão</h3>
                    <p class="text-xs text-gray-600 leading-relaxed mb-3">
                        A supervisão <span class="text-orange-700 font-bold">{{ $supervisionName }}</span> possui <span class="text-gray-950 font-bold">{{ $cells->count() }}</span> células e <span class="text-gray-950 font-bold">{{ $cells->sum('members') }}</span> membros sob sua responsabilidade.
                    </p>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-green-50 border border-green-200 px-2.5 py-0.5">
                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>
                        <span class="text-[10px] font-black uppercase tracking-wider text-green-800">Monitorização Ativa</span>
                    </div>
                </section>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Próximos Eventos -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Próximos Eventos</h3>
                        <p class="text-xs text-gray-500">Agenda ministerial</p>
                    </div>
                    <a href="{{ route('events.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todos</a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingEvents as $event)
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                            <div class="bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-center min-w-[54px]">
                                <span class="block text-base font-black leading-none text-gray-900 tabular-nums">{{ $event->date->format('d') }}</span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500">{{ $event->date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 text-xs sm:text-sm truncate">{{ $event->name }}</h4>
                                <p class="text-[11px] text-gray-500 flex items-center mt-0.5 truncate">
                                    <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Local a definir' }}
                                    @if($event->end_date)
                                        <span class="ml-2 px-1.5 py-0.5 bg-orange-50 text-orange-800 rounded text-[9px] font-bold uppercase">Até {{ $event->end_date->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            </div>
                            <span class="px-2 py-0.5 bg-gray-100 rounded-full text-[9px] font-black uppercase text-gray-600 shrink-0">
                                {{ $event->eventType->name ?? 'Evento' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6 text-xs">Nenhum evento programado.</p>
                    @endforelse
                </div>
            </section>

            <!-- Últimos Cultos -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Relatórios de Cultos</h3>
                        <p class="text-xs text-gray-500">Celebrações recentes</p>
                    </div>
                    <a href="{{ route('services.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todos</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentServices as $service)
                        <div class="flex items-center justify-between gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-orange-700 shrink-0">
                                    <i class="bi bi-journal-text text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-gray-900 text-xs sm:text-sm truncate">{{ $service->name }}</h4>
                                    <p class="text-[11px] text-gray-500 tabular-nums">
                                        {{ $service->date->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-black text-gray-950 text-xs sm:text-sm tabular-nums">{{ $service->attendance_total ?? 0 }}</p>
                                <p class="text-[9px] text-gray-400 font-bold uppercase">Presentes</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6 text-xs">Nenhum relatório de culto registado.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
