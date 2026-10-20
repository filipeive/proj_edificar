@extends('layouts.app')

@section('title', 'Dashboard Secretaria')
@section('page-title', 'Visão Geral')
@section('page-subtitle', 'Administração e gestão eclesiástica')

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Secretaria Eclesiástica</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Visão Geral da Secretaria</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Gestão de membros, células, relatórios de cultos e agenda de eventos.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('events.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-calendar-plus text-orange-700"></i> <span>Agendar Evento</span>
                </a>
                <a href="{{ route('services.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-plus-lg"></i> <span>Novo Culto</span>
                </a>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <!-- Total Membros -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Membros</p>
                    <i class="bi bi-people text-orange-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $totalMembers }}</p>
                <a href="{{ route('members.index') }}" class="inline-flex items-center text-[10px] sm:text-[11px] font-bold text-orange-800 hover:underline mt-0.5">
                    Ver todos <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </article>

            <!-- Total Células -->
            <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Células</p>
                    <i class="bi bi-diagram-3 text-blue-700 text-sm sm:text-base"></i>
                </div>
                <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $totalCells }}</p>
                <a href="{{ route('cells.index') }}" class="inline-flex items-center text-[10px] sm:text-[11px] font-bold text-orange-800 hover:underline mt-0.5">
                    Ver todas <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </article>

            <!-- Atalho Cultos -->
            <a href="{{ route('services.create') }}"
                class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm hover:bg-gray-50 transition-colors flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Novo Culto</p>
                    <i class="bi bi-journal-plus text-emerald-700 text-sm sm:text-base"></i>
                </div>
                <div>
                    <p class="mt-1 text-sm sm:text-base font-black text-gray-950 tracking-tight">Lançar Relatório</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Registar celebração</p>
                </div>
            </a>

            <!-- Atalho Eventos -->
            <a href="{{ route('events.create') }}"
                class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm hover:bg-gray-50 transition-colors flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Novo Evento</p>
                    <i class="bi bi-calendar-plus text-amber-700 text-sm sm:text-base"></i>
                </div>
                <div>
                    <p class="mt-1 text-sm sm:text-base font-black text-gray-950 tracking-tight">Agendar Evento</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Calendário da igreja</p>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Próximos Eventos -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Próximos Eventos</h3>
                        <p class="text-xs text-gray-500">Programação agendada</p>
                    </div>
                    <a href="{{ route('events.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Gerir Todos</a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingEvents as $event)
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                            <div class="bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-center min-w-[54px]">
                                <span class="block text-base font-black leading-none text-gray-900 tabular-nums">{{ $event->date->format('d') }}</span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500">{{ $event->date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 text-xs sm:text-sm truncate">
                                    {{ $event->name }}
                                </h4>
                                <p class="text-[11px] text-gray-500 flex items-center mt-0.5 truncate">
                                    <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Local a definir' }}
                                    @if($event->end_date)
                                        <span class="ml-2 px-1.5 py-0.5 bg-orange-50 text-orange-800 rounded text-[9px] font-bold">Até {{ $event->end_date->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('events.edit', $event) }}"
                                class="rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-gray-50 shrink-0">
                                Editar
                            </a>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6 text-xs">Nenhum evento agendado.</p>
                    @endforelse
                </div>
            </section>

            <!-- Últimos Cultos -->
            <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Últimos Cultos</h3>
                        <p class="text-xs text-gray-500">Relatórios de celebrações</p>
                    </div>
                    <a href="{{ route('services.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Histórico</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentServices as $service)
                        <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center shrink-0 text-sm">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">
                                        {{ $service->topic ?? 'Culto de Celebração' }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 truncate tabular-nums">
                                        {{ $service->date->format('d/m/Y') }} • <span class="font-bold text-gray-800">{{ $service->attendance_total }}</span> pessoas
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('services.show', $service) }}" class="text-xs font-bold text-orange-800 hover:underline shrink-0">
                                Detalhes
                            </a>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6 text-xs">Nenhum relatório de culto recente.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <!-- Novos Membros -->
        <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-black text-gray-900">Novos Membros</h3>
                    <p class="text-xs text-gray-500">Registos recentes na congregação</p>
                </div>
                <a href="{{ route('members.create') }}" class="inline-flex items-center gap-1 rounded-lg bg-orange-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-orange-800">
                    <i class="bi bi-plus-lg"></i> Novo Membro
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                @forelse($recentMembers as $member)
                    <div class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-xl border border-gray-200/80">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-xs font-black text-orange-800 shrink-0">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ $member->name }}</p>
                            <p class="text-[10px] text-gray-500">{{ $member->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 col-span-full py-6 text-xs">Nenhum membro novo recentemente.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
