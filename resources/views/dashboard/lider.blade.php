@extends('layouts.app')

@section('title', 'Dashboard Líder - Portal Life Church')
@section('page-title', 'Dashboard da Liderança')
@section('page-subtitle', 'Monitorização de ' . $cellName)

@section('content')
    <div class="space-y-6 md:space-y-8">
        @if($cells->isEmpty())
            <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-8">
                <div class="flex flex-col md:flex-row md:items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 dark:bg-orange-950/20 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                        <i class="bi bi-diagram-3-fill text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Nenhuma célula atribuída</h2>
                        <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">A sua conta ainda não está marcada como líder ou timóteo de uma célula.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-orange-50 dark:bg-orange-950/20 p-3 rounded-xl text-orange-600 dark:text-orange-400">
                            <i class="bi bi-diagram-3-fill text-xl"></i>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Gestão</span>
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Células Geridas</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $cells->count() }}</p>
                </div>

                <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-sky-50 dark:bg-sky-950/20 p-3 rounded-xl text-sky-600 dark:text-sky-400">
                            <i class="bi bi-people-fill text-xl"></i>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Comunidade</span>
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Membros Ativos</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $totalMembers }}</p>
                </div>

                <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-emerald-50 dark:bg-emerald-950/20 p-3 rounded-xl text-emerald-600 dark:text-emerald-400">
                            <i class="bi bi-cash-coin text-xl"></i>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Financeiro</span>
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total do Período</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($total, 2, ',', '.') }} MT</p>
                </div>

                <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-violet-50 dark:bg-violet-950/20 p-3 rounded-xl text-violet-600 dark:text-violet-400">
                            <i class="bi bi-percent text-xl"></i>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Participação</span>
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Contribuição</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $percentage }}%</p>
                    <div class="w-full bg-gray-100 dark:bg-zinc-800 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-violet-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-50 dark:border-zinc-850 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight">Células sob sua liderança</h3>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">Ações e indicadores separados por célula.</p>
                    </div>
                    <a href="{{ route('cells.index') }}"
                        class="inline-flex items-center justify-center gap-2 bg-gray-900 dark:bg-white text-white dark:text-zinc-900 px-4 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 dark:hover:bg-orange-500 dark:hover:text-white transition-all">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                        Ver Gestão
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 p-5">
                    @foreach($cellCards as $managedCell)
                        <div class="border border-gray-100 dark:border-zinc-850 rounded-2xl p-5 bg-gray-50/40 dark:bg-zinc-950/20">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-orange-600 dark:text-orange-400">{{ $managedCell['type'] }}</p>
                                    <h4 class="text-lg font-black text-gray-900 dark:text-white mt-1 truncate">{{ $managedCell['name'] }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1 truncate">
                                        {{ $managedCell['supervision'] ?? 'Sem supervisão' }} @if($managedCell['zone']) / {{ $managedCell['zone'] }} @endif
                                    </p>
                                </div>
                                <span class="flex-shrink-0 w-10 h-10 rounded-xl bg-white dark:bg-zinc-900 text-orange-600 dark:text-orange-400 flex items-center justify-center shadow-sm">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mt-5">
                                <div class="bg-white dark:bg-zinc-900 rounded-xl p-3">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Membros</p>
                                    <p class="text-lg font-black text-gray-900 dark:text-white">{{ $managedCell['active_members_count'] }}</p>
                                </div>
                                <div class="bg-white dark:bg-zinc-900 rounded-xl p-3">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Total</p>
                                    <p class="text-lg font-black text-gray-900 dark:text-white">{{ number_format($managedCell['total'], 0, ',', '.') }} MT</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mt-5">
                                <a href="{{ route('cells.show', $managedCell['id']) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white dark:bg-zinc-900 px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-700 dark:text-zinc-200 hover:text-orange-600 dark:hover:text-orange-400 transition">
                                    <i class="bi bi-eye-fill"></i>
                                    Abrir
                                </a>
                                <a href="{{ route('cells.attendance', $managedCell['id']) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-white hover:bg-orange-700 transition">
                                    <i class="bi bi-calendar-check-fill"></i>
                                    Ficha
                                </a>
                                <a href="{{ route('cell-meetings.create', ['cell_id' => $managedCell['id']]) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white dark:bg-zinc-900 px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-700 dark:text-zinc-200 hover:text-orange-600 dark:hover:text-orange-400 transition">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    Encontro
                                </a>
                                <a href="{{ route('reports.cell', ['cell_id' => $managedCell['id']]) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white dark:bg-zinc-900 px-3 py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-700 dark:text-zinc-200 hover:text-orange-600 dark:hover:text-orange-400 transition">
                                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                                    Relatório
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                <div class="xl:col-span-2 bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-50 dark:border-zinc-850 flex items-center justify-between">
                        <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight">Membros das Células</h3>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $members->count() }} ativos</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-50 dark:border-zinc-850">
                                    <th class="px-6 py-4 text-left">Membro</th>
                                    <th class="px-6 py-4 text-left">Célula</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-zinc-850">
                                @forelse($members as $member)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-900/40 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-zinc-850 text-gray-500 dark:text-zinc-400 flex items-center justify-center font-black mr-4">
                                                    {{ strtoupper(substr($member['name'], 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-black text-gray-900 dark:text-white text-sm">{{ $member['name'] }}</p>
                                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold truncate max-w-[170px]">{{ $member['email'] }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-600 dark:text-zinc-300">{{ $member['cell_name'] }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if($member['status'] === 'Contribuiu')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 rounded-full text-[9px] font-black uppercase tracking-widest">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Contribuiu
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 rounded-full text-[9px] font-black uppercase tracking-widest">
                                                    <i class="bi bi-exclamation-circle-fill"></i>
                                                    Faltoso
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-gray-900 dark:text-white text-sm">{{ number_format($member['total'], 2, ',', '.') }} MT</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">Nenhum membro ativo encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6">
                        <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight mb-5">Atalhos</h3>
                        <div class="grid grid-cols-1 gap-3">
                            <a href="{{ route('cell-meetings.index') }}"
                                class="flex items-center p-4 bg-gray-50 dark:bg-zinc-850 rounded-2xl hover:bg-orange-600 group transition-all">
                                <span class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-4 group-hover:bg-orange-700 group-hover:text-white">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                                <span class="text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white">Encontros</span>
                            </a>
                            <a href="{{ route('members.index') }}"
                                class="flex items-center p-4 bg-gray-50 dark:bg-zinc-850 rounded-2xl hover:bg-orange-600 group transition-all">
                                <span class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-4 group-hover:bg-orange-700 group-hover:text-white">
                                    <i class="bi bi-person-lines-fill"></i>
                                </span>
                                <span class="text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white">Membros</span>
                            </a>
                            <a href="{{ route('contributions.create') }}"
                                class="flex items-center p-4 bg-gray-50 dark:bg-zinc-850 rounded-2xl hover:bg-orange-600 group transition-all">
                                <span class="w-10 h-10 rounded-xl bg-white dark:bg-zinc-900 flex items-center justify-center text-orange-600 dark:text-orange-400 mr-4 group-hover:bg-orange-700 group-hover:text-white">
                                    <i class="bi bi-cash-coin"></i>
                                </span>
                                <span class="text-sm font-black text-gray-700 dark:text-zinc-200 group-hover:text-white">Registar Contribuição</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight">Próximos Eventos</h3>
                        <a href="{{ route('events.index') }}" class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Ver Todos</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($upcomingEvents as $event)
                            <div class="flex items-center gap-4">
                                <div class="bg-gray-50 dark:bg-zinc-850 px-4 py-3 rounded-2xl text-center min-w-[70px]">
                                    <span class="block text-xl font-black leading-none text-gray-900 dark:text-gray-100">{{ $event->date->format('d') }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-zinc-400">{{ $event->date->translatedFormat('M') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-black text-gray-900 dark:text-white truncate">{{ $event->name }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1 truncate">
                                        <i class="bi bi-geo-alt mr-1"></i>{{ $event->location ?? 'Local a definir' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 dark:text-zinc-500 py-8">Nenhum evento programado.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-850 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-base font-black text-gray-900 dark:text-white tracking-tight">Últimos Encontros</h3>
                        <a href="{{ route('cell-meetings.index') }}" class="text-xs font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Ver Todos</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentMeetings as $meeting)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-850 rounded-2xl">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-11 h-11 bg-white dark:bg-zinc-900 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400">
                                        <i class="bi bi-calendar-check-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-black text-gray-900 dark:text-white truncate">{{ $meeting->cell?->name ?? 'Célula' }}</h4>
                                        <p class="text-[10px] text-gray-400 dark:text-zinc-500 font-bold uppercase tracking-widest">
                                            {{ $meeting->meeting_date->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-black text-gray-900 dark:text-white">{{ $meeting->attendees_count ?? 0 }}</p>
                                    <p class="text-[8px] text-gray-400 dark:text-zinc-500 font-bold uppercase tracking-widest">Presentes</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 dark:text-zinc-500 py-8">Nenhum encontro registado.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
