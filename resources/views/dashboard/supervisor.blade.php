@extends('layouts.app')

@section('title', 'Dashboard Supervisor - Portal Life Church')
@section('page-title', 'Dashboard do Supervisor')
@section('page-subtitle', 'Monitorização da Supervisão ' . $supervisionName)

@section('content')
    <div class="space-y-6 md:space-y-8">
    <div class="hidden md:grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Supervisão Info -->
        <div
            class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-2xl group-hover:bg-blue-600 transition-colors duration-500">
                    <i
                        class="bi bi-layers-fill text-blue-600 dark:text-blue-400 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Estrutura</span>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Supervisão</p>
                <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 tracking-tighter">{{ $supervisionName }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 font-bold uppercase tracking-widest">{{ $cells->count() }} Células
                    Ativas</p>
            </div>
        </div>

        <!-- Total Arrecadado -->
        <div
            class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-2xl group-hover:bg-green-600 transition-colors duration-500">
                    <i
                        class="bi bi-cash-coin text-green-600 dark:text-green-400 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Financeiro</span>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Total da Supervisão</p>
                <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 tracking-tighter">{{ number_format($total, 2, ',', '.') }}
                    MT</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 font-bold uppercase tracking-widest">Este Mês</p>
            </div>
        </div>

        <!-- Desempenho -->
        <div
            class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-2xl group-hover:bg-purple-600 transition-colors duration-500">
                    <i
                        class="bi bi-graph-up-arrow text-purple-600 dark:text-purple-400 text-2xl group-hover:text-white transition-colors duration-500"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">Participação</span>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase tracking-wider">Taxa Geral</p>
                @php
                    $totalMembers = $cells->sum('members');
                    $totalContributed = $cells->sum('contributed');
                    $percentage = $totalMembers > 0 ? round(($totalContributed / $totalMembers) * 100, 1) : 0;
                @endphp
                <p class="text-3xl font-black text-gray-900 dark:text-white mt-2 tracking-tighter">{{ $percentage }}%</p>
                <div class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full mt-4 overflow-hidden">
                    <div class="bg-purple-600 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Células da Supervisão -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl md:rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 md:px-8 py-4 md:py-6 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-700/30">
                <h3 class="text-lg md:text-xl font-black text-gray-900 dark:text-white tracking-tight">Células da Supervisão</h3>
                <a href="{{ route('cells.index') }}"
                    class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-50 dark:border-gray-700">
                            <th class="px-8 py-4 text-left">Célula</th>
                            <th class="px-8 py-4 text-center">Líder</th>
                            <th class="px-8 py-4 text-center">Membros</th>
                            <th class="px-8 py-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach($cells as $cell)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black mr-4 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                            {{ strtoupper(substr($cell['name'], 0, 1)) }}
                                        </div>
                                        <span class="font-black text-gray-900 dark:text-white">{{ $cell['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center text-sm text-gray-600 dark:text-gray-400">
                                    {{ $cell['leader'] }}
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span
                                        class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-[10px] font-black text-gray-500 dark:text-gray-300 uppercase tracking-widest">
                                        {{ $cell['contributed'] }}/{{ $cell['members'] }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right font-black text-green-600 dark:text-green-400 tracking-tight">
                                    {{ number_format($cell['total'], 2, ',', '.') }} MT
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl md:rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8">
                <h3 class="text-lg md:text-xl font-black text-gray-900 dark:text-white tracking-tight mb-5 md:mb-6">Ações Rápidas</h3>
                <div class="grid grid-cols-1 gap-3 md:gap-4">
                    <a href="{{ route('reports.supervision') }}"
                        class="flex items-center p-3 md:p-4 bg-blue-50 dark:bg-blue-900/30 rounded-2xl hover:bg-blue-600 group transition-all duration-300">
                        <div
                            class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white dark:bg-gray-700 flex items-center justify-center text-blue-600 dark:text-blue-400 mr-3 md:mr-4 shadow-sm">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </div>
                        <span class="text-sm md:text-base font-black text-blue-900 dark:text-blue-100 group-hover:text-white transition-colors">Relatório Supervisão</span>
                    </a>
                    <a href="{{ route('cell-meetings.index') }}"
                        class="flex items-center p-3 md:p-4 bg-green-50 dark:bg-green-900/30 rounded-2xl hover:bg-green-600 group transition-all duration-300">
                        <div
                            class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white dark:bg-gray-700 flex items-center justify-center text-green-600 dark:text-green-400 mr-3 md:mr-4 shadow-sm">
                            <i class="bi bi-people"></i>
                        </div>
                        <span class="text-sm md:text-base font-black text-green-900 dark:text-green-100 group-hover:text-white transition-colors">Encontros de Célula</span>
                    </a>
                    <a href="{{ route('contributions.index') }}"
                        class="flex items-center p-3 md:p-4 bg-orange-50 dark:bg-orange-900/30 rounded-2xl hover:bg-orange-600 group transition-all duration-300">
                        <div
                            class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white dark:bg-gray-700 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-3 md:mr-4 shadow-sm">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <span class="text-sm md:text-base font-black text-orange-900 dark:text-orange-100 group-hover:text-white transition-colors">Contribuições</span>
                    </a>
                </div>
            </div>

            <div class="bg-blue-900 rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/20 rounded-full -mr-16 -mt-16 blur-3xl"></div>
                <h3 class="text-lg font-black tracking-tight mb-4 relative z-10">Status da Supervisão</h3>
                <p class="text-sm text-gray-400 leading-relaxed mb-6 relative z-10">
                    A supervisão <span class="text-blue-400 font-bold">{{ $supervisionName }}</span> possui <span
                        class="text-white font-bold">{{ $cells->count() }}</span> células e <span
                        class="text-white font-bold">{{ $cells->sum('members') }}</span> membros sob sua responsabilidade.
                </p>
                <div class="flex items-center space-x-2 relative z-10">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-green-500">Monitorização Ativa</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Próximos Eventos -->
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Próximos Eventos</h3>
                <a href="{{ route('events.index') }}"
                    class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todos</a>
            </div>
            <div class="space-y-6">
                @forelse($upcomingEvents as $event)
                    <div class="flex items-center space-x-6 group">
                        <div
                            class="bg-gray-50 dark:bg-gray-700 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                            <span class="block text-xl font-black leading-none text-gray-900 dark:text-white group-hover:text-white">{{ $event->date->format('d') }}</span>
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 group-hover:text-white">{{ $event->date->translatedFormat('M') }}</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                {{ $event->name }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center mt-1">
                                <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Local a definir' }}
                                @if($event->end_date)
                                    <span class="ml-2 px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded text-[9px] font-bold uppercase tracking-widest">Até {{ $event->end_date->format('d/m/Y') }}</span>
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

        <!-- Últimos Cultos -->
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Relatórios de Cultos</h3>
                <a href="{{ route('services.index') }}"
                    class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest hover:text-orange-700 dark:hover:text-orange-300">Ver Todos</a>
            </div>
            <div class="space-y-6">
                @forelse($recentServices as $service)
                    <div
                        class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-2xl hover:bg-white dark:hover:bg-gray-600 hover:shadow-lg transition-all duration-300 border border-transparent hover:border-gray-100 dark:hover:border-gray-600">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 shadow-sm">
                                <i class="bi bi-journal-text text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 dark:text-white">{{ $service->name }}</h4>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">
                                    {{ $service->date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-gray-900 dark:text-white">{{ $service->attendance_total ?? 0 }}</p>
                            <p class="text-[8px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest">Presentes</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 dark:text-gray-500 py-10">Nenhum relatório de culto registado.</p>
                @endforelse
            </div>
        </div>
    </div>
    </div>
@endsection