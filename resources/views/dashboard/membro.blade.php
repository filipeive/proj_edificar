@extends('layouts.app')

@section('title', 'Meu Dashboard - Portal Life Church')
@section('page-title', 'Meu Dashboard')
@section('page-subtitle', 'Bem-vindo de volta, ' . $authUser->name)

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Meu Compromisso -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-gray-900 tracking-tight">Meu Compromisso</h3>
                <a href="{{ route('commitments.index') }}"
                    class="text-[10px] font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">
                    Alterar <i class="bi bi-chevron-right ml-1"></i>
                </a>
            </div>
            @if($commitment)
                <div
                    class="bg-blue-50 border border-blue-100 rounded-[1.5rem] p-6 group-hover:bg-blue-600 group-hover:border-blue-600 transition-all duration-500">
                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-600 group-hover:text-blue-100 mb-2">
                        {{ $commitment->package->name }}</p>
                    <p class="text-2xl font-black text-blue-900 group-hover:text-white mt-1 tracking-tighter">
                        {{ number_format($commitment->package->min_amount, 0, ',', '.') }} -
                        @if($commitment->package->max_amount)
                            {{ number_format($commitment->package->max_amount, 0, ',', '.') }}
                        @else
                            ∞
                        @endif
                        MT
                    </p>
                    <p class="text-[10px] text-blue-400 group-hover:text-blue-200 mt-4 font-bold uppercase tracking-widest">
                        Desde {{ $commitment->start_date->format('d/m/Y') }}</p>
                </div>
            @else
                <div class="text-center py-6 bg-gray-50 rounded-[1.5rem] border border-dashed border-gray-200">
                    <p class="text-gray-500 text-sm mb-4">Você ainda não escolheu um compromisso</p>
                    <a href="{{ route('commitments.index') }}"
                        class="inline-flex items-center px-6 py-2 bg-orange-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-orange-600/20 hover:bg-orange-700 transition-all">
                        Escolher Agora
                    </a>
                </div>
            @endif
        </div>

        <!-- Total Este Mês -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500 group">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-gray-900 tracking-tight">Total Este Mês</h3>
                <div class="bg-green-50 p-3 rounded-xl">
                    <i class="bi bi-cash-stack text-green-600 text-xl"></i>
                </div>
            </div>
            <div
                class="bg-green-50 border border-green-100 rounded-[1.5rem] p-6 group-hover:bg-green-600 group-hover:border-green-600 transition-all duration-500">
                <p class="text-[10px] font-black uppercase tracking-widest text-green-600 group-hover:text-green-100 mb-2">
                    Contribuição Acumulada</p>
                <p class="text-3xl font-black text-green-900 group-hover:text-white mt-1 tracking-tighter">
                    {{ number_format($totalThisMonth, 2, ',', '.') }} MT</p>
                <p class="text-[10px] text-green-400 group-hover:text-green-200 mt-4 font-bold uppercase tracking-widest">
                    Período: Dia 20 ao 5</p>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div
            class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-xl transition-all duration-500">
            <h3 class="text-lg font-black text-gray-900 tracking-tight mb-6">Próximos Passos</h3>
            <div class="space-y-4">
                <a href="{{ route('contributions.create') }}"
                    class="flex items-center p-4 bg-orange-600 text-white rounded-2xl shadow-lg shadow-orange-600/20 hover:bg-orange-700 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center mr-4">
                        <i class="bi bi-plus-lg text-xl"></i>
                    </div>
                    <span class="font-black uppercase tracking-widest text-xs">Nova Contribuição</span>
                </a>
                <a href="{{ route('contributions.index', ['mine' => 1]) }}"
                    class="flex items-center p-4 bg-gray-50 text-gray-600 rounded-2xl border border-gray-100 hover:bg-gray-100 transition-all group">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center mr-4 shadow-sm">
                        <i class="bi bi-clock-history text-xl"></i>
                    </div>
                    <span class="font-black uppercase tracking-widest text-xs">Ver Histórico</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Últimas Contribuições -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Minhas Contribuições</h3>
                <a href="{{ route('contributions.index', ['mine' => 1]) }}"
                    class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Ver Todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-50">
                            <th class="px-8 py-4 text-left">Data</th>
                            <th class="px-8 py-4 text-center">Status</th>
                            <th class="px-8 py-4 text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($contributions as $contribution)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-8 py-6">
                                    <span
                                        class="font-black text-gray-900">{{ $contribution->contribution_date->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @if($contribution->status === 'verificada')
                                        <span
                                            class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                            ✓ Verificada
                                        </span>
                                    @elseif($contribution->status === 'pendente')
                                        <span
                                            class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                            ⌚ Pendente
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                            ✕ Rejeitada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right font-black text-gray-900 tracking-tight">
                                    {{ number_format($contribution->amount, 2, ',', '.') }} MT
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-8 py-10 text-center text-gray-400">
                                    Nenhuma contribuição registada recentemente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Próximos Eventos -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Próximos Eventos</h3>
                <a href="{{ route('events.index') }}"
                    class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Ver
                    Calendário</a>
            </div>
            <div class="space-y-6">
                @forelse($upcomingEvents as $event)
                    <div class="flex items-center space-x-6 group">
                        <div
                            class="bg-gray-50 px-4 py-3 rounded-2xl text-center min-w-[70px] group-hover:bg-orange-600 group-hover:text-white transition-colors">
                            <span class="block text-xl font-black leading-none">{{ $event->date->format('d') }}</span>
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest">{{ $event->date->translatedFormat('M') }}</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-gray-900 group-hover:text-orange-600 transition-colors">
                                {{ $event->name }}</h4>
                            <p class="text-xs text-gray-500 flex items-center mt-1">
                                <i class="bi bi-geo-alt mr-1"></i> {{ $event->location ?? 'Life Church' }}
                            </p>
                        </div>
                        <span
                            class="px-3 py-1 bg-gray-100 rounded-full text-[8px] font-black uppercase tracking-widest text-gray-500">
                            {{ $event->eventType->name ?? 'Evento' }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-10">Nenhum evento programado.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Últimos Cultos -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Relatórios de Cultos</h3>
                <a href="{{ route('services.index') }}"
                    class="text-xs font-black text-orange-600 uppercase tracking-widest hover:text-orange-700">Ver Todos</a>
            </div>
            <div class="space-y-6">
                @forelse($recentServices as $service)
                    <div
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl hover:bg-white hover:shadow-lg transition-all duration-300 border border-transparent hover:border-gray-100">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-orange-600 shadow-sm">
                                <i class="bi bi-journal-text text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900">{{ $service->name }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                    {{ $service->date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-gray-900">{{ $service->attendance_count ?? 0 }}</p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">Presentes</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-10">Nenhum relatório de culto registado.</p>
                @endforelse
            </div>
        </div>

        <!-- Card Informativo -->
        <div
            class="bg-gray-900 rounded-[2.5rem] shadow-xl p-8 text-white relative overflow-hidden flex flex-col justify-center">
            <div class="absolute top-0 right-0 w-64 h-64 bg-orange-600/20 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="relative z-10">
                <h3 class="text-2xl font-black tracking-tight mb-4">Comunidade Life Church</h3>
                <p class="text-gray-400 leading-relaxed mb-8">
                    Sua participação é fundamental para o crescimento da nossa igreja. Juntos estamos edificando vidas e
                    transformando destinos.
                </p>
                <div class="flex items-center space-x-4">
                    <div class="flex -space-x-2">
                        <div
                            class="w-10 h-10 rounded-full bg-orange-600 border-2 border-gray-900 flex items-center justify-center font-bold text-xs">
                            L</div>
                        <div
                            class="w-10 h-10 rounded-full bg-blue-600 border-2 border-gray-900 flex items-center justify-center font-bold text-xs">
                            I</div>
                        <div
                            class="w-10 h-10 rounded-full bg-green-600 border-2 border-gray-900 flex items-center justify-center font-bold text-xs">
                            F</div>
                        <div
                            class="w-10 h-10 rounded-full bg-purple-600 border-2 border-gray-900 flex items-center justify-center font-bold text-xs">
                            E</div>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest text-gray-500">+ de 500 membros</span>
                </div>
            </div>
        </div>
    </div>
@endsection