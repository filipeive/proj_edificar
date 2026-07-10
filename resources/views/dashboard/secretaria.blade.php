@extends('layouts.app')

@section('title', 'Dashboard Secretaria')
@section('page-title', 'Visão Geral')
@section('page-subtitle', 'Administração e gestão eclesiástica')

@section('content')
    <div class="space-y-6 md:space-y-8">
        <div class="space-y-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Membros -->
                <div class="bg-white dark:bg-zinc-900/30 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 flex flex-col justify-center group">
                    <div
                        class="flex items-center gap-3 text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-[0.2em] mb-2">
                        <i class="bi bi-people-fill"></i>
                        <span>Membros</span>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalMembers }}</p>
                    <div class="mt-3">
                        <a href="{{ route('members.index') }}"
                            class="text-[10px] font-bold text-gray-400 dark:text-gray-500 hover:text-orange-600 dark:hover:text-orange-400 transition-colors flex items-center">
                            Ver todos <i class="bi bi-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Células -->
                <div class="bg-white dark:bg-zinc-900/30 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 flex flex-col justify-center group">
                    <div
                        class="flex items-center gap-3 text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-[0.2em] mb-2">
                        <i class="bi bi-diagram-3-fill"></i>
                        <span>Células</span>
                    </div>
                    <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $totalCells }}</p>
                    <div class="mt-3">
                        <a href="{{ route('cells.index') }}"
                            class="text-[10px] font-bold text-gray-400 dark:text-gray-500 hover:text-orange-600 dark:hover:text-orange-400 transition-colors flex items-center">
                            Ver todas <i class="bi bi-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Atalho Cultos -->
                <a href="{{ route('services.create') }}"
                    class="group bg-zinc-950 p-6 rounded-2xl shadow-md text-white relative overflow-hidden flex flex-col justify-center hover:scale-[1.02] transition-all border border-zinc-900">
                    <div class="relative z-10">
                        <div
                            class="flex items-center gap-3 text-[9px] font-black text-orange-400 uppercase tracking-[0.2em] mb-2">
                            <i class="bi bi-plus-lg"></i>
                            <span>Novo Culto</span>
                        </div>
                        <p
                            class="text-lg font-black tracking-tight group-hover:text-orange-400 transition-colors">
                            Lançar Relatório</p>
                    </div>
                    <i
                        class="bi bi-church absolute -right-3 -bottom-3 text-7xl text-white/5 group-hover:text-orange-500/10 group-hover:scale-110 transition-all duration-300"></i>
                </a>

                <!-- Atalho Eventos -->
                <a href="{{ route('events.create') }}"
                    class="group bg-orange-600 p-6 rounded-2xl shadow-md text-white relative overflow-hidden flex flex-col justify-center hover:scale-[1.02] transition-all">
                    <div class="relative z-10">
                        <div
                            class="flex items-center gap-3 text-[9px] font-black text-orange-200 uppercase tracking-[0.2em] mb-2">
                            <i class="bi bi-calendar-plus"></i>
                            <span>Novo Evento</span>
                        </div>
                        <p
                            class="text-lg font-black tracking-tight group-hover:text-orange-100 transition-colors">
                            Agendar Evento</p>
                    </div>
                    <i
                        class="bi bi-calendar-event absolute -right-3 -bottom-3 text-7xl text-white/10 group-hover:scale-110 transition-all duration-300"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Próximos Eventos -->
                <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Próximos Eventos</h3>
                        <a href="{{ route('events.index') }}"
                            class="text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Gerir Todos</a>
                    </div>
                    <div class="space-y-6">
                        @forelse($upcomingEvents as $event)
                            <div class="flex items-center space-x-6 group">
                                <div
                                    class="bg-gray-50 dark:bg-zinc-850 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                    <span class="block text-xl font-black leading-none text-gray-900 dark:text-white group-hover:text-white">{{ $event->date->format('d') }}</span>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-zinc-400 group-hover:text-white">{{ $event->date->translatedFormat('M') }}</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-black text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors text-sm">
                                        {{ $event->name }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-zinc-400 flex items-center mt-1">
                                        <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Local a definir' }}
                                        @if($event->end_date)
                                            <span class="ml-2 px-2 py-0.5 bg-orange-50 dark:bg-orange-950/20 text-orange-655 dark:text-orange-400 rounded text-[9px] font-bold">Até {{ $event->end_date->format('d/m/Y') }}</span>
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('events.edit', $event) }}"
                                    class="px-3 py-1 bg-gray-100 dark:bg-zinc-850 rounded-full text-[9px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 hover:bg-orange-50 dark:hover:bg-orange-950/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                    Editar
                                </a>
                            </div>
                        @empty
                            <p class="text-center text-gray-450 dark:text-zinc-500 py-10 text-xs font-bold uppercase tracking-wider">Nenhum evento agendado.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Últimos Cultos -->
                <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Últimos Cultos</h3>
                        <a href="{{ route('services.index') }}"
                            class="text-[9px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Histórico</a>
                    </div>
                    <div class="space-y-6">
                        @forelse($recentServices as $service)
                            <div
                                class="flex items-center justify-between group border-b border-gray-50 dark:border-zinc-850 last:border-0 pb-4 last:pb-0">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/20 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                        <i class="bi bi-church-fill text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-orange-600 transition-colors">
                                            {{ $service->topic ?? 'Culto de Celebração' }}</p>
                                        <p class="text-xs text-gray-400 dark:text-zinc-500">{{ $service->date->format('d/m/Y') }} •
                                            <span class="font-bold text-gray-700 dark:text-zinc-350">{{ $service->attendance_total }}</span> pessoas
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('services.show', $service) }}" class="text-gray-300 dark:text-zinc-700 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        @empty
                            <p class="text-center text-gray-450 dark:text-zinc-500 py-10 text-xs font-bold uppercase tracking-wider">Nenhum relatório de culto recente.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Novos Membros -->
            <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6 md:p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">Novos Membros</h3>
                    <div class="flex gap-3">
                        <a href="{{ route('members.create') }}"
                            class="text-[9px] font-black text-gray-450 dark:text-zinc-500 uppercase tracking-widest hover:text-orange-600 dark:hover:text-orange-400">
                            <i class="bi bi-plus-lg mr-1"></i> Novo
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($recentMembers as $member)
                        <div class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-zinc-900/40 rounded-2xl transition-colors">
                            <div
                                class="w-8 h-8 rounded-full bg-orange-50 dark:bg-orange-950/20 flex items-center justify-center text-xs font-black text-orange-600 dark:text-orange-400">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $member->name }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-zinc-550">{{ $member->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 dark:text-zinc-500 col-span-full py-4 text-xs font-bold uppercase tracking-wider">Nenhum membro novo recentemente.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection