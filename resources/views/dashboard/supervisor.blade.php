@extends('layouts.app')

@section('title', 'Dashboard Supervisor - Portal Life Church')
@section('page-title', 'Dashboard do Supervisor')
@section('page-subtitle', 'Monitorização da Supervisão ' . $supervisionName)

@section('content')
    <div class="space-y-6 md:space-y-8">
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6 sm:mb-8">
            <!-- Supervisão Info -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-3 sm:p-4 md:p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-2.5 sm:mb-4">
                    <div class="bg-orange-50 dark:bg-orange-950/20 p-2 sm:p-2.5 md:p-3.5 rounded-lg sm:rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-layers-fill text-orange-600 dark:text-orange-400 text-base sm:text-lg md:text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Estrutura</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] sm:text-xs font-bold uppercase tracking-wider">Supervisão</p>
                    <p class="text-lg sm:text-xl md:text-2xl font-black text-gray-900 dark:text-white mt-0.5 sm:mt-1 tracking-tight truncate">{{ $supervisionName }}</p>
                    <p class="text-[9px] sm:text-[10px] text-gray-400 dark:text-gray-500 mt-1 sm:mt-2 font-bold uppercase tracking-widest">{{ $cells->count() }} Células Ativas</p>
                </div>
            </div>

            <!-- Total Arrecadado -->
            <div
                class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-3 sm:p-4 md:p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-2.5 sm:mb-4">
                    <div class="bg-orange-50 dark:bg-orange-950/20 p-2 sm:p-2.5 md:p-3.5 rounded-lg sm:rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-cash-coin text-orange-600 dark:text-orange-400 text-base sm:text-lg md:text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Financeiro</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] sm:text-xs font-bold uppercase tracking-wider">Total da Supervisão</p>
                    <p class="text-base sm:text-xl md:text-2xl font-black text-gray-900 dark:text-white mt-0.5 sm:mt-1 tracking-tight">
                        {{ number_format($total, 2, ',', '.') }} MT
                    </p>
                    <p class="text-[9px] sm:text-[10px] text-gray-400 dark:text-gray-500 mt-1 sm:mt-2 font-bold uppercase tracking-widest">Este Mês</p>
                </div>
            </div>

            <!-- Desempenho -->
            <div
                class="col-span-2 lg:col-span-1 bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-3 sm:p-4 md:p-6 hover:shadow-md transition-all duration-300 group">
                <div class="flex items-center justify-between mb-2.5 sm:mb-4">
                    <div class="bg-orange-50 dark:bg-orange-950/20 p-2 sm:p-2.5 md:p-3.5 rounded-lg sm:rounded-xl group-hover:bg-orange-600 transition-colors duration-300">
                        <i
                            class="bi bi-graph-up-arrow text-orange-600 dark:text-orange-400 text-base sm:text-lg md:text-xl group-hover:text-white transition-colors duration-300"></i>
                    </div>
                    <span class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Participação</span>
                </div>
                <div>
                    <p class="text-gray-400 dark:text-gray-500 text-[10px] sm:text-xs font-bold uppercase tracking-wider">Taxa Geral</p>
                    @php
                        $totalMembers = $cells->sum('members');
                        $totalContributed = $cells->sum('contributed');
                        $percentage = $totalMembers > 0 ? round(($totalContributed / $totalMembers) * 100, 1) : 0;
                    @endphp
                    <p class="text-lg sm:text-xl md:text-2xl font-black text-gray-900 dark:text-white mt-0.5 sm:mt-1 tracking-tight">{{ $percentage }}%</p>
                    <div class="w-full bg-gray-100 dark:bg-zinc-800 h-1.5 rounded-full mt-2 sm:mt-3 overflow-hidden">
                        <div class="bg-orange-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
            <!-- Células da Supervisão -->
            <div class="lg:col-span-2 bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 overflow-hidden">
                <div class="px-4 sm:px-6 py-3.5 sm:py-5 border-b border-gray-50 dark:border-zinc-850 flex items-center justify-between">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white tracking-tight">Células da Supervisão</h3>
                    <a href="{{ route('cells.index') }}"
                        class="text-[9px] sm:text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todas</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-50 dark:border-zinc-850">
                                <th class="px-3 sm:px-6 py-2.5 sm:py-4 text-left">Célula</th>
                                <th class="px-3 sm:px-6 py-2.5 sm:py-4 text-center">Líder</th>
                                <th class="px-3 sm:px-6 py-2.5 sm:py-4 text-center">Membros</th>
                                <th class="px-3 sm:px-6 py-2.5 sm:py-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-zinc-850">
                            @foreach($cells as $cell)
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-900/40 transition-colors group">
                                    <td class="px-3 sm:px-6 py-2.5 sm:py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-orange-50 dark:bg-orange-950/20 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black mr-2.5 sm:mr-4 group-hover:bg-orange-600 group-hover:text-white transition-all text-xs sm:text-sm">
                                                {{ strtoupper(substr($cell['name'], 0, 1)) }}
                                            </div>
                                            <span class="font-black text-gray-900 dark:text-white text-xs sm:text-sm">{{ $cell['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-6 py-2.5 sm:py-4 text-center text-xs sm:text-sm text-gray-650 dark:text-zinc-400">
                                        {{ $cell['leader'] }}
                                    </td>
                                    <td class="px-3 sm:px-6 py-2.5 sm:py-4 text-center">
                                        <span
                                            class="px-2 sm:px-2.5 py-0.5 bg-gray-100 dark:bg-zinc-850 rounded-full text-[8px] sm:text-[9px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">
                                            {{ $cell['contributed'] }}/{{ $cell['members'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-6 py-2.5 sm:py-4 text-right font-black text-green-600 dark:text-green-400 tracking-tight text-xs sm:text-sm">
                                        {{ number_format($cell['total'], 2, ',', '.') }} MT
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="space-y-4 sm:space-y-6">
                <div class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-4 sm:p-6 md:p-8">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white tracking-tight mb-4 sm:mb-6">Ações Rápidas</h3>
                    <div class="grid grid-cols-1 gap-2.5 sm:gap-4">
                        <a href="{{ route('reports.supervision') }}"
                            class="flex items-center p-3 sm:p-4 bg-gray-50 dark:bg-zinc-850 rounded-xl sm:rounded-2xl hover:bg-orange-600 group transition-all duration-300 border border-transparent hover:border-orange-500/20">
                            <div
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-3 sm:mr-4 shadow-sm group-hover:bg-orange-700 group-hover:text-white transition-colors">
                                <i class="bi bi-file-earmark-pdf text-base sm:text-lg"></i>
                            </div>
                            <span class="text-xs sm:text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white transition-colors">Relatório Supervisão</span>
                        </a>
                        <a href="{{ route('cell-meetings.index') }}"
                            class="flex items-center p-3 sm:p-4 bg-gray-50 dark:bg-zinc-850 rounded-xl sm:rounded-2xl hover:bg-orange-600 group transition-all duration-300 border border-transparent hover:border-orange-500/20">
                            <div
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-3 sm:mr-4 shadow-sm group-hover:bg-orange-700 group-hover:text-white transition-colors">
                                <i class="bi bi-people text-base sm:text-lg"></i>
                            </div>
                            <span class="text-xs sm:text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white transition-colors">Encontros de Célula</span>
                        </a>
                        <a href="{{ route('contributions.index') }}"
                            class="flex items-center p-3 sm:p-4 bg-gray-50 dark:bg-zinc-850 rounded-xl sm:rounded-2xl hover:bg-orange-600 group transition-all duration-300 border border-transparent hover:border-orange-500/20">
                            <div
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-3 sm:mr-4 shadow-sm group-hover:bg-orange-700 group-hover:text-white transition-colors">
                                <i class="bi bi-cash-coin text-base sm:text-lg"></i>
                            </div>
                            <span class="text-xs sm:text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white transition-colors">Contribuições</span>
                        </a>
                    </div>
                </div>

                <div class="bg-zinc-950 rounded-xl sm:rounded-2xl shadow-xl p-5 sm:p-8 text-white relative overflow-hidden border border-zinc-900">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-600/10 rounded-full -mr-16 -mt-16 blur-3xl"></div>
                    <h3 class="text-sm sm:text-base font-black tracking-tight mb-2 sm:mb-3 relative z-10">Status da Supervisão</h3>
                    <p class="text-xs text-gray-400 leading-relaxed mb-4 sm:mb-5 relative z-10">
                        A supervisão <span class="text-orange-500 font-bold">{{ $supervisionName }}</span> possui <span
                            class="text-white font-bold">{{ $cells->count() }}</span> células e <span
                            class="text-white font-bold">{{ $cells->sum('members') }}</span> membros sob sua responsabilidade.
                    </p>
                    <div class="flex items-center space-x-2 relative z-10">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        <span class="text-[9px] font-black uppercase tracking-widest text-green-500">Monitorização Ativa</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
            <!-- Próximos Eventos -->
            <div class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-4 sm:p-6 md:p-8">
                <div class="flex items-center justify-between mb-4 sm:mb-8">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white tracking-tight">Próximos Eventos</h3>
                    <a href="{{ route('events.index') }}"
                        class="text-[10px] sm:text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todos</a>
                </div>
                <div class="space-y-4 sm:space-y-6">
                    @forelse($upcomingEvents as $event)
                        <div class="flex items-center space-x-3 sm:space-x-6 group">
                            <div
                                class="bg-gray-50 dark:bg-zinc-850 px-3 py-2 sm:px-4 sm:py-3 rounded-xl sm:rounded-2xl text-center min-w-[55px] sm:min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                                <span class="block text-base sm:text-xl font-black leading-none text-gray-900 dark:text-white group-hover:text-white">{{ $event->date->format('d') }}</span>
                                <span
                                    class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-zinc-400 group-hover:text-white">{{ $event->date->translatedFormat('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-black text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors text-xs sm:text-sm truncate">
                                    {{ $event->name }}</h4>
                                <p class="text-[11px] sm:text-xs text-gray-500 dark:text-zinc-400 flex items-center mt-0.5 sm:mt-1 truncate">
                                    <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Local a definir' }}
                                    @if($event->end_date)
                                        <span class="ml-1.5 sm:ml-2 px-1.5 sm:px-2 py-0.5 bg-orange-50 dark:bg-orange-950/20 text-orange-650 dark:text-orange-400 rounded text-[8px] sm:text-[9px] font-bold uppercase tracking-widest">Até {{ $event->end_date->format('d/m/Y') }}</span>
                                    @endif
                                </p>
                            </div>
                            <span
                                class="px-2 sm:px-3 py-0.5 sm:py-1 bg-gray-100 dark:bg-zinc-850 rounded-full text-[7px] sm:text-[8px] font-black uppercase tracking-widest text-gray-500 dark:text-zinc-300 flex-shrink-0">
                                {{ $event->eventType->name ?? 'Evento' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 dark:text-zinc-500 py-8 sm:py-10 text-xs sm:text-sm">Nenhum evento programado.</p>
                    @endforelse
                </div>
            </div>

            <!-- Últimos Cultos -->
            <div class="bg-white dark:bg-zinc-900/30 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-4 sm:p-6 md:p-8">
                <div class="flex items-center justify-between mb-4 sm:mb-8">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white tracking-tight">Relatórios de Cultos</h3>
                    <a href="{{ route('services.index') }}"
                        class="text-[10px] sm:text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todos</a>
                </div>
                <div class="space-y-3 sm:space-y-6">
                    @forelse($recentServices as $service)
                        <div
                            class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-zinc-850 rounded-xl sm:rounded-2xl hover:bg-white dark:hover:bg-zinc-900/40 hover:shadow-lg transition-all duration-300 border border-transparent hover:border-gray-100 dark:hover:border-zinc-800">
                            <div class="flex items-center space-x-3 sm:space-x-4 min-w-0">
                                <div
                                    class="w-9 h-9 sm:w-12 sm:h-12 bg-white dark:bg-zinc-900 rounded-lg sm:rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 shadow-sm flex-shrink-0">
                                    <i class="bi bi-journal-text text-lg sm:text-2xl"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-black text-gray-900 dark:text-white text-xs sm:text-sm truncate">{{ $service->name }}</h4>
                                    <p class="text-[9px] sm:text-[10px] text-gray-400 dark:text-zinc-500 font-bold uppercase tracking-widest">
                                        {{ $service->date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-black text-gray-900 dark:text-white text-xs sm:text-sm">{{ $service->attendance_total ?? 0 }}</p>
                                <p class="text-[7px] sm:text-[8px] text-gray-400 dark:text-zinc-500 font-bold uppercase tracking-widest">Presentes</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 dark:text-zinc-500 py-8 sm:py-10 text-xs sm:text-sm">Nenhum relatório de culto registado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection