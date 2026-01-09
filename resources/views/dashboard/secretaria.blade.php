@extends('layouts.app')

@section('title', 'Dashboard Secretaria')
@section('page-title', 'Visão Geral')
@section('page-subtitle', 'Administração e gestão eclesiástica')

@section('content')
    <div class="space-y-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Membros -->
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-center">
                <div class="flex items-center gap-3 text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-2">
                    <i class="bi bi-people-fill"></i>
                    <span>Membros</span>
                </div>
                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $totalMembers }}</p>
                <div class="mt-2">
                    <a href="{{ route('members.index') }}" class="text-xs font-bold text-gray-400 hover:text-blue-600 transition-colors flex items-center">
                        Ver todos <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Total Células -->
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 flex flex-col justify-center">
                <div class="flex items-center gap-3 text-[10px] font-black text-orange-600 uppercase tracking-[0.2em] mb-2">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Células</span>
                </div>
                <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ $totalCells }}</p>
                <div class="mt-2">
                    <a href="{{ route('cells.index') }}" class="text-xs font-bold text-gray-400 hover:text-orange-600 transition-colors flex items-center">
                        Ver todas <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Atalho Cultos -->
            <a href="{{ route('services.create') }}" class="group bg-gradient-to-br from-indigo-900 to-blue-900 p-8 rounded-[2rem] shadow-xl text-white relative overflow-hidden flex flex-col justify-center hover:scale-[1.02] transition-all">
                <div class="relative z-10">
                    <div class="flex items-center gap-3 text-[10px] font-black text-blue-300 uppercase tracking-[0.2em] mb-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>Novo Culto</span>
                    </div>
                    <p class="text-2xl font-black tracking-tighter group-hover:text-blue-200 transition-colors">Lançar Relatório</p>
                </div>
                <i class="bi bi-church absolute -right-4 -bottom-4 text-8xl text-white opacity-5 group-hover:opacity-10 transition-opacity"></i>
            </a>

            <!-- Atalho Eventos -->
            <a href="{{ route('events.create') }}" class="group bg-gradient-to-br from-orange-600 to-red-600 p-8 rounded-[2rem] shadow-xl text-white relative overflow-hidden flex flex-col justify-center hover:scale-[1.02] transition-all">
                <div class="relative z-10">
                    <div class="flex items-center gap-3 text-[10px] font-black text-orange-200 uppercase tracking-[0.2em] mb-2">
                        <i class="bi bi-calendar-plus"></i>
                        <span>Novo Evento</span>
                    </div>
                    <p class="text-2xl font-black tracking-tighter group-hover:text-orange-100 transition-colors">Agendar Evento</p>
                </div>
                <i class="bi bi-calendar-event absolute -right-4 -bottom-4 text-8xl text-white opacity-5 group-hover:opacity-10 transition-opacity"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Próximos Eventos -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Próximos Eventos</h3>
                    <a href="{{ route('events.index') }}" class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Gerir Todos</a>
                </div>
                <div class="space-y-6">
                    @forelse($upcomingEvents as $event)
                        <div class="flex items-center space-x-6 group">
                            <div class="bg-gray-50 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                <span class="block text-xl font-black leading-none">{{ $event->date->format('d') }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest">{{ $event->date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-gray-900 group-hover:text-orange-600 transition-colors">{{ $event->name }}</h4>
                                <p class="text-xs text-gray-500 flex items-center mt-1">
                                    <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Local a definir' }}
                                </p>
                            </div>
                            <a href="{{ route('events.edit', $event) }}" class="px-3 py-1 bg-gray-100 rounded-full text-[10px] font-black uppercase tracking-widest text-gray-500 hover:bg-orange-100 hover:text-orange-600 transition-colors">
                                Editar
                            </a>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-10">Nenhum evento agendado.</p>
                    @endforelse
                </div>
            </div>

            <!-- Últimos Cultos -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Últimos Cultos</h3>
                    <a href="{{ route('services.index') }}" class="text-xs font-black text-blue-600 uppercase tracking-widest hover:text-blue-700">Ver Histórico</a>
                </div>
                <div class="space-y-6">
                    @forelse($recentServices as $service)
                        <div class="flex items-center justify-between group border-b border-gray-50 last:border-0 pb-4 last:pb-0">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i class="bi bi-church-fill"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $service->topic ?? 'Culto de Celebração' }}</p>
                                    <p class="text-xs text-gray-400">{{ $service->date->format('d/m/Y') }} • 
                                        <span class="font-bold">{{ $service->attendance_total }}</span> pessoas
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('services.show', $service) }}" class="text-gray-300 hover:text-blue-600">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-10">Nenhum relatório de culto recente.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Novos Membros -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Novos Membros</h3>
                <div class="flex gap-3">
                    <a href="{{ route('members.create') }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-blue-600">
                        <i class="bi bi-plus-lg mr-1"></i> Novo
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($recentMembers as $member)
                    <div class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-2xl transition-colors">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-black text-gray-500">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-900">{{ $member->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $member->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 col-span-full py-4">Nenhum membro novo recentemente.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
