@extends('layouts.app')

@section('title', 'Dashboard Líder - Portal Life Church')
@section('page-title', 'Dashboard da Célula')
@section('page-subtitle', 'Monitorização da Célula ' . $cellName)

@section('content')
    <div class="space-y-6 md:space-y-8">
        <div class="hidden md:grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Célula Info -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 hover:shadow-xl transition-all duration-500 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-orange-50 dark:bg-orange-900/30 p-4 rounded-2xl group-hover:bg-orange-600 transition-colors duration-500">
                        <i
                            class="bi bi-people-fill text-orange-600 dark:text-orange-400 text-2xl group-hover:text-white transition-colors duration-500"></i>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Comunidade</span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Célula</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 tracking-tighter">{{ $cellName }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 font-bold uppercase tracking-widest">
                        {{ $members->count() }}
                        Membros
                        Ativos</p>
                </div>
            </div>

            <!-- Total Arrecadado -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 hover:shadow-xl transition-all duration-500 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-green-50 dark:bg-green-900/30 p-4 rounded-2xl group-hover:bg-green-600 transition-colors duration-500">
                        <i
                            class="bi bi-cash-coin text-green-600 dark:text-green-400 text-2xl group-hover:text-white transition-colors duration-500"></i>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Financeiro</span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Total da Célula
                    </p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 tracking-tighter">
                        {{ number_format($total, 2, ',', '.') }}
                        MT
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 font-bold uppercase tracking-widest">Este Mês
                    </p>
                </div>
            </div>

            <!-- Taxa Participação -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 hover:shadow-xl transition-all duration-500 group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-2xl group-hover:bg-blue-600 transition-colors duration-500">
                        <i
                            class="bi bi-percent text-blue-600 dark:text-blue-400 text-2xl group-hover:text-white transition-colors duration-500"></i>
                    </div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Participação</span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Taxa de
                        Contribuição</p>
                    @php
                        $contributedCount = $members->where('status', 'Contribuiu')->count();
                        $totalCount = $members->count();
                        $percentage = $totalCount > 0 ? round(($contributedCount / $totalCount) * 100, 1) : 0;
                    @endphp
                    <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 tracking-tighter">{{ $percentage }}%
                    </p>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full mt-4 overflow-hidden">
                        <div class="bg-blue-600 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Membros da Célula -->
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl md:rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div
                    class="px-5 md:px-8 py-4 md:py-6 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-700/30">
                    <h3 class="text-lg md:text-xl font-black text-gray-900 dark:text-white tracking-tight">Membros da Célula
                    </h3>
                    <a href="{{ route('contributions.create') }}"
                        class="bg-orange-600 text-white px-3 md:px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:bg-orange-700 transition-all">
                        + Registar
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-50 dark:border-gray-700">
                                <th class="px-8 py-4 text-left">Membro</th>
                                <th class="px-8 py-4 text-center">Status</th>
                                <th class="px-8 py-4 text-right">Total Mês</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            @foreach($members as $member)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 flex items-center justify-center font-black mr-4 group-hover:bg-orange-600 group-hover:text-white transition-all">
                                                {{ strtoupper(substr($member['name'], 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900 dark:text-white">{{ $member['name'] }}</p>
                                                <p
                                                    class="text-[10px] text-gray-400 dark:text-gray-500 font-bold truncate max-w-[150px]">
                                                    {{ $member['email'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($member['status'] === 'Contribuiu')
                                            <span
                                                class="px-3 py-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full text-[10px] font-black uppercase tracking-widest">
                                                ✓ Contribuiu
                                            </span>
                                        @else
                                            <span
                                                class="px-3 py-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full text-[10px] font-black uppercase tracking-widest">
                                                ⚠ Faltoso
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-right font-black text-gray-900 dark:text-white tracking-tight">
                                        {{ number_format($member['total'], 2, ',', '.') }} MT
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="space-y-6">
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl md:rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                    <h3 class="text-lg md:text-xl font-black text-gray-900 dark:text-white tracking-tight mb-5 md:mb-6">
                        Ações Rápidas</h3>
                    <div class="grid grid-cols-1 gap-3 md:gap-4">
                        <a href="{{ route('cells.attendance', $cell->id) }}"
                            class="flex items-center p-3 md:p-4 bg-blue-50 dark:bg-blue-900/30 rounded-2xl hover:bg-blue-600 group transition-all duration-300">
                            <div
                                class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white dark:bg-gray-700 flex items-center justify-center text-blue-600 dark:text-blue-400 mr-3 md:mr-4 shadow-sm">
                                <i class="bi bi-calendar-check text-xl"></i>
                            </div>
                            <span
                                class="text-sm md:text-base font-black text-blue-900 dark:text-blue-100 group-hover:text-white transition-colors">Ficha
                                Guia</span>
                        </a>
                        <a href="{{ route('cell-meetings.create') }}"
                            class="flex items-center p-3 md:p-4 bg-green-50 dark:bg-green-900/30 rounded-2xl hover:bg-green-600 group transition-all duration-300">
                            <div
                                class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white dark:bg-gray-700 flex items-center justify-center text-green-600 dark:text-green-400 mr-3 md:mr-4 shadow-sm">
                                <i class="bi bi-plus-circle text-xl"></i>
                            </div>
                            <span
                                class="text-sm md:text-base font-black text-green-900 dark:text-green-100 group-hover:text-white transition-colors">Novo
                                Encontro</span>
                        </a>
                        <a href="{{ route('reports.cell') }}"
                            class="flex items-center p-3 md:p-4 bg-purple-50 dark:bg-purple-900/30 rounded-2xl hover:bg-purple-600 group transition-all duration-300">
                            <div
                                class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white dark:bg-gray-700 flex items-center justify-center text-purple-600 dark:text-purple-400 mr-3 md:mr-4 shadow-sm">
                                <i class="bi bi-file-earmark-bar-graph text-xl"></i>
                            </div>
                            <span
                                class="text-sm md:text-base font-black text-purple-900 dark:text-purple-100 group-hover:text-white transition-colors">Relatório
                                Célula</span>
                        </a>
                    </div>
                </div>

                <div class="bg-orange-600 rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-3xl"></div>
                    <h3 class="text-lg font-black tracking-tight mb-4 relative z-10">Lembrete</h3>
                    <p class="text-sm text-orange-100 leading-relaxed mb-6 relative z-10">
                        Não esqueça de registar o encontro de célula desta semana e validar as presenças na Ficha Guia.
                    </p>
                    <div class="flex items-center space-x-2 relative z-10">
                        <i class="bi bi-info-circle-fill text-orange-200"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest text-orange-200">Ação
                            Necessária</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Próximos Eventos -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Próximos Eventos</h3>
                    <a href="{{ route('events.index') }}"
                        class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver
                        Todos</a>
                </div>
                <div class="space-y-6">
                    @forelse($upcomingEvents as $event)
                        <div class="flex items-center space-x-6 group">
                            <div
                                class="bg-gray-50 dark:bg-gray-700 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                <span
                                    class="block text-xl font-black leading-none text-gray-900 dark:text-gray-100 group-hover:text-white">{{ $event->date->format('d') }}</span>
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 group-hover:text-white">{{ $event->date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1">
                                <h4
                                    class="font-black text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                    {{ $event->name }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center mt-1">
                                    <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Local a definir' }}
                                    @if($event->end_date)
                                        <span
                                            class="ml-2 px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded text-[9px] font-bold uppercase tracking-widest">Até
                                            {{ $event->end_date->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            </div>
                            <span
                                class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-[8px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">
                                {{ $event->eventType->name ?? 'Evento' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 dark:text-gray-500 py-10">Nenhum evento programado.</p>
                    @endforelse
                </div>
            </div>

            <!-- Últimos Encontros -->
            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Últimos Encontros</h3>
                    <a href="{{ route('cell-meetings.index') }}"
                        class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver
                        Todos</a>
                </div>
                <div class="space-y-6">
                    @forelse($recentMeetings as $meeting)
                        <div
                            class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-2xl hover:bg-white dark:hover:bg-gray-600 hover:shadow-lg transition-all duration-300 border border-transparent hover:border-gray-100 dark:hover:border-gray-600">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 shadow-sm">
                                    <i class="bi bi-people text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-gray-900 dark:text-white">Encontro de Célula</h4>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">
                                        {{ $meeting->meeting_date->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-gray-900 dark:text-white">{{ $meeting->attendees_count ?? 0 }}</p>
                                <p class="text-[8px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">
                                    Presentes</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 dark:text-gray-500 py-10">Nenhum encontro registado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection