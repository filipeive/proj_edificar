@extends('layouts.app')

@section('title', 'Dashboard Líder - Portal Life Church')
@section('page-title', 'Dashboard da Liderança')
@section('page-subtitle', 'Monitorização de ' . $cellName)

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Liderança de Célula</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Dashboard da Liderança</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Monitorização pastoral e financeira de {{ $cellName }}.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('cell-meetings.index') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-calendar-check text-orange-700"></i> <span>Encontros</span>
                </a>
                <a href="{{ route('contributions.create') }}" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 shadow-sm">
                    <i class="bi bi-plus-lg"></i> <span>Registar Contribuição</span>
                </a>
            </div>
        </header>

        @if($cells->isEmpty())
            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center shrink-0">
                        <i class="bi bi-diagram-3-fill text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-black text-gray-950 tracking-tight">Nenhuma célula atribuída</h2>
                        <p class="text-xs text-gray-500 mt-0.5">A sua conta ainda não está marcada como líder ou timóteo de uma célula.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Células Geridas</p>
                        <i class="bi bi-diagram-3 text-orange-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $cells->count() }}</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Unidades sob gestão</p>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Membros Ativos</p>
                        <i class="bi bi-people text-blue-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $totalMembers }}</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Comunidade acompanhada</p>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Total do Período</p>
                        <i class="bi bi-cash-coin text-emerald-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-emerald-700 tracking-tight tabular-nums">{{ number_format($total, 2, ',', '.') }} MT</p>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5 truncate">Contribuições registadas</p>
                </article>

                <article class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] sm:text-xs font-bold text-gray-500 truncate">Participação</p>
                        <i class="bi bi-percent text-indigo-700 text-sm sm:text-base"></i>
                    </div>
                    <p class="mt-1 text-lg sm:text-2xl font-black text-gray-950 tracking-tight tabular-nums">{{ $percentage }}%</p>
                    <div class="w-full bg-gray-100 h-1.5 rounded-full mt-1.5 overflow-hidden">
                        <div class="bg-indigo-600 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </article>
            </div>

            <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-black text-gray-900">Células sob sua liderança</h3>
                        <p class="text-xs text-gray-500">Ações e indicadores separados por célula.</p>
                    </div>
                    <a href="{{ route('cells.index') }}"
                        class="inline-flex items-center justify-center gap-1.5 bg-gray-900 text-white rounded-lg px-3.5 py-2 text-xs font-bold hover:bg-gray-800 transition-colors">
                        <i class="bi bi-grid-3x3-gap"></i>
                        <span>Ver Gestão</span>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 p-4 sm:p-5">
                    @foreach($cellCards as $managedCell)
                        <div class="border border-gray-200 rounded-xl p-3.5 sm:p-4 bg-gray-50/50">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">{{ $managedCell['type'] }}</p>
                                    <h4 class="text-sm sm:text-base font-black text-gray-950 mt-0.5 truncate">{{ $managedCell['name'] }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                                        {{ $managedCell['supervision'] ?? 'Sem supervisão' }} @if($managedCell['zone']) / {{ $managedCell['zone'] }} @endif
                                    </p>
                                </div>
                                <span class="shrink-0 w-8 h-8 rounded-lg bg-white border border-gray-200 text-orange-700 flex items-center justify-center text-sm">
                                    <i class="bi bi-people"></i>
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5 mt-3.5">
                                <div class="bg-white border border-gray-200 rounded-lg p-2.5">
                                    <p class="text-[10px] font-bold text-gray-500">Membros</p>
                                    <p class="text-base font-black text-gray-950 tabular-nums">{{ $managedCell['active_members_count'] }}</p>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-lg p-2.5">
                                    <p class="text-[10px] font-bold text-gray-500">Total</p>
                                    <p class="text-base font-black text-gray-950 tabular-nums">{{ number_format($managedCell['total'], 0, ',', '.') }} MT</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mt-3.5">
                                <a href="{{ route('cells.show', $managedCell['id']) }}"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                                    <i class="bi bi-eye"></i>
                                    Abrir
                                </a>
                                <a href="{{ route('cells.attendance', $managedCell['id']) }}"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-orange-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-orange-800 transition">
                                    <i class="bi bi-calendar-check"></i>
                                    Ficha
                                </a>
                                <a href="{{ route('cell-meetings.create', ['cell_id' => $managedCell['id']]) }}"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                                    <i class="bi bi-plus-circle"></i>
                                    Encontro
                                </a>
                                <a href="{{ route('reports.cell', ['cell_id' => $managedCell['id']]) }}"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                                    <i class="bi bi-file-earmark-bar-graph"></i>
                                    Relatório
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                <section class="xl:col-span-2 rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-black text-gray-900">Membros das Células</h3>
                            <p class="text-xs text-gray-500">Estado de participação no período</p>
                        </div>
                        <span class="text-xs font-bold text-gray-500">{{ $members->count() }} ativos</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-[10px] font-black uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3">Membro</th>
                                    <th class="px-4 py-3">Célula</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse($members as $member)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-black shrink-0">
                                                    {{ strtoupper(substr($member['name'], 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-bold text-gray-900 text-sm truncate">{{ $member['name'] }}</p>
                                                    <p class="text-xs text-gray-500 truncate max-w-[180px]">{{ $member['email'] }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-xs font-semibold text-gray-600">{{ $member['cell_name'] }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($member['status'] === 'Contribuiu')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black uppercase">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Contribuiu
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 text-red-700 rounded-full text-[10px] font-black uppercase">
                                                    <i class="bi bi-exclamation-circle-fill"></i>
                                                    Faltoso
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right font-black text-gray-900 text-sm tabular-nums">{{ number_format($member['total'], 2, ',', '.') }} MT</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-500">Nenhum membro ativo encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="space-y-4">
                    <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                        <h3 class="text-sm font-black text-gray-900 mb-3">Atalhos</h3>
                        <div class="grid grid-cols-1 gap-2.5">
                            <a href="{{ route('cell-meetings.index') }}"
                                class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80 hover:bg-orange-50 hover:border-orange-200 transition-colors">
                                <span class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-orange-700 shrink-0">
                                    <i class="bi bi-people text-sm"></i>
                                </span>
                                <span class="text-xs font-bold text-gray-800">Encontros</span>
                            </a>
                            <a href="{{ route('members.index') }}"
                                class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80 hover:bg-orange-50 hover:border-orange-200 transition-colors">
                                <span class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-orange-700 shrink-0">
                                    <i class="bi bi-person-lines-fill text-sm"></i>
                                </span>
                                <span class="text-xs font-bold text-gray-800">Membros</span>
                            </a>
                            <a href="{{ route('contributions.create') }}"
                                class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80 hover:bg-orange-50 hover:border-orange-200 transition-colors">
                                <span class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-orange-700 shrink-0">
                                    <i class="bi bi-cash-coin text-sm"></i>
                                </span>
                                <span class="text-xs font-bold text-gray-800">Registar Contribuição</span>
                            </a>
                        </div>
                    </section>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <h3 class="text-sm font-black text-gray-900">Próximos Eventos</h3>
                        <a href="{{ route('events.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todos</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($upcomingEvents as $event)
                            <div class="flex items-center gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                <div class="bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg text-center min-w-[54px]">
                                    <span class="block text-base font-black leading-none text-gray-900 tabular-nums">{{ $event->date->format('d') }}</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-500">{{ $event->date->translatedFormat('M') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $event->name }}</h4>
                                    <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                        <i class="bi bi-geo-alt mr-1"></i>{{ $event->location ?? 'Local a definir' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-xs text-gray-500 py-6">Nenhum evento programado.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <h3 class="text-sm font-black text-gray-900">Últimos Encontros</h3>
                        <a href="{{ route('cell-meetings.index') }}" class="text-xs font-bold text-orange-800 hover:underline">Ver Todos</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentMeetings as $meeting)
                            <div class="flex items-center justify-between gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200/80">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-orange-700 shrink-0">
                                        <i class="bi bi-calendar-check text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $meeting->cell?->name ?? 'Célula' }}</h4>
                                        <p class="text-[11px] text-gray-500 tabular-nums">
                                            {{ $meeting->meeting_date->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-black text-gray-950 tabular-nums">{{ $meeting->attendees_count ?? 0 }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase">Presentes</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-xs text-gray-500 py-6">Nenhum encontro registado.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        @endif
    </div>
@endsection
