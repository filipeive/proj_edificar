@extends('layouts.app')

@section('title', 'Detalhes do Encontro')
@section('page-title', 'Detalhes do Encontro')
@section('page-subtitle', 'Informações completas sobre a reunião')

@section('header-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('cell-meetings.index') }}"
            class="hidden md:flex items-center gap-2 px-6 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-black text-[10px] uppercase tracking-widest rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm">
            <i class="bi bi-arrow-left"></i>
            Voltar
        </a>

        <div class="h-8 w-px bg-gray-100 dark:bg-gray-700 mx-1 hidden md:block"></div>

        <button onclick="toggleEmailModal()"
            class="text-gray-600 dark:text-gray-400 hover:text-blue-600 p-2.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-all duration-300 border border-transparent hover:border-blue-100"
            title="Enviar por Email">
            <i class="bi bi-envelope text-2xl"></i>
        </button>
        <a href="{{ route('cell-meetings.pdf', $cellMeeting) }}"
            class="text-gray-600 dark:text-gray-400 hover:text-orange-600 p-2.5 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-xl transition-all duration-300 border border-transparent hover:border-orange-100"
            title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf text-2xl"></i>
        </a>

        @can('update', $cellMeeting)
            <a href="{{ route('cell-meetings.edit', $cellMeeting) }}"
                class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                <i class="bi bi-pencil-square"></i>
                <span class="hidden md:inline">Editar Encontro</span>
            </a>
        @endcan

        @can('delete', $cellMeeting)
            <button type="button" onclick="confirmDelete('delete-form-{{ $cellMeeting->id }}')"
                class="flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-red-700 transition-all shadow-lg shadow-red-600/20">
                <i class="bi bi-trash-fill"></i>
                <span class="hidden md:inline">Excluir</span>
            </button>
            <form id="delete-form-{{ $cellMeeting->id }}" action="{{ route('cell-meetings.destroy', $cellMeeting) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endcan
    </div>
@endsection

@section('content')
    <div class="space-y-8 animate-fade-in">
        <!-- Main Info Banner -->
        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors">
            <div
                class="bg-gradient-to-br from-blue-600 to-indigo-700 px-8 md:px-12 py-12 text-white relative overflow-hidden">
                <div
                    class="absolute inset-0 bg-blue-900 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
                </div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            @php
                                $typeStyles = [
                                    'normal' => 'bg-emerald-400/20 text-emerald-100 border-emerald-400/30',
                                    'leadership' => 'bg-purple-400/20 text-purple-100 border-purple-400/30',
                                    'supervision' => 'bg-amber-400/20 text-amber-100 border-amber-400/30',
                                    'zone' => 'bg-indigo-400/20 text-indigo-100 border-indigo-400/30',
                                ];
                                $typeLabel = [
                                    'normal' => 'Reunião de Célula',
                                    'leadership' => 'Reunião de Liderança',
                                    'supervision' => 'Reunião de Supervisão',
                                    'zone' => 'Reunião de Zona',
                                ];
                                $style = $typeStyles[$cellMeeting->meeting_type] ?? 'bg-white/20 text-white border-white/30';
                            @endphp
                            <span
                                class="px-4 py-1.5 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest border {{ $style }}">
                                {{ $typeLabel[$cellMeeting->meeting_type] ?? 'Encontro' }}
                            </span>
                            <span class="text-white/40">•</span>
                            <span
                                class="text-sm font-bold text-blue-100">{{ $cellMeeting->meeting_date->format('d/m/Y') }}</span>
                        </div>
                        <h3 class="text-4xl md:text-6xl font-black tracking-tighter">{{ $cellMeeting->cell->name }}</h3>
                        <div class="flex flex-wrap gap-4 text-[10px] font-black uppercase tracking-widest">
                            <a href="{{ route('cells.show', $cellMeeting->cell) }}"
                                class="group flex items-center bg-white/20 hover:bg-white/40 px-6 py-3 rounded-2xl transition-all border border-white/20 hover:border-white/40 shadow-sm">
                                <i class="bi bi-box-arrow-up-right mr-3 group-hover:scale-110 transition-transform"></i>
                                Ir para Célula: {{ $cellMeeting->cell->name }}
                            </a>
                            <span class="flex items-center bg-black/10 px-4 py-2 rounded-xl">
                                <i class="bi bi-diagram-3 mr-2"></i> {{ $cellMeeting->cell->supervision->name }}
                            </span>
                        </div>
                    </div>
                    <div
                        class="flex flex-col items-center md:items-end gap-2 bg-white/10 backdrop-blur-xl p-6 rounded-[2rem] border border-white/10">
                        <span
                            class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-100 opacity-60">Participação
                            Total</span>
                        <div class="flex items-baseline gap-1">
                            <span
                                class="text-5xl font-black tracking-tighter">{{ $cellMeeting->adults_count + $cellMeeting->children_count + $cellMeeting->visitors_count }}</span>
                            <i class="bi bi-people-fill text-2xl opacity-40"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-12 grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-10">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">
                            Ministração do Dia</h4>
                        <div class="space-y-6">
                            <div
                                class="flex items-start gap-4 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-gray-100 dark:border-gray-700">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl shrink-0">
                                    <i class="bi bi-chat-heart"></i>
                                </div>
                                <div>
                                    <p
                                        class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-1">
                                        Tema Central</p>
                                    <p class="text-2xl font-black text-gray-900 dark:text-white leading-tight italic">
                                        "{{ $cellMeeting->theme ?? 'Maturidade Cristã' }}"</p>
                                </div>
                            </div>
                            @if($cellMeeting->biblical_text)
                                <div class="flex items-center gap-4 px-6 text-gray-500 dark:text-gray-400 font-bold">
                                    <i class="bi bi-book text-xl text-blue-500"></i>
                                    <span class="text-lg">{{ $cellMeeting->biblical_text }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">
                            Liderança Responsável</h4>
                        <div
                            class="flex items-center gap-5 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-gray-100 dark:border-gray-700">
                            <div
                                class="w-16 h-16 rounded-[1.5rem] bg-blue-600 text-white flex items-center justify-center text-3xl font-black shadow-lg shadow-blue-600/20">
                                {{ substr($cellMeeting->leader->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ $cellMeeting->leader->name }}
                                </p>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">
                                    {{ $cellMeeting->leader->role }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 dark:bg-gray-900/50 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-700">
                    <h4
                        class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] text-center mb-10">
                        Métricas de Engajamento</h4>
                    <div class="grid grid-cols-2 gap-8">
                        @php
                            $metrics = [
                                ['label' => 'Adultos', 'value' => $cellMeeting->adults_count, 'icon' => 'bi-person'],
                                ['label' => 'Crianças', 'value' => $cellMeeting->children_count, 'icon' => 'bi-emoji-smile'],
                                ['label' => 'Visitantes', 'value' => $cellMeeting->visitors_count, 'icon' => 'bi-person-plus'],
                                ['label' => 'Total', 'value' => $cellMeeting->adults_count + $cellMeeting->children_count + $cellMeeting->visitors_count, 'icon' => 'bi-people', 'highlight' => true],
                            ];
                        @endphp
                        @foreach($metrics as $metric)
                            <div class="text-center group transition-all duration-300">
                                <div class="mb-2">
                                    <i
                                        class="{{ $metric['icon'] }} text-xl {{ isset($metric['highlight']) ? 'text-blue-500' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                </div>
                                <p
                                    class="text-4xl font-black {{ isset($metric['highlight']) ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white' }} tracking-tighter group-hover:scale-110 transition-transform">
                                    {{ $metric['value'] }}
                                </p>
                                <p
                                    class="text-[9px] font-black uppercase tracking-[0.2em] {{ isset($metric['highlight']) ? 'text-blue-500' : 'text-gray-400 dark:text-gray-500' }} mt-2">
                                    {{ $metric['label'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Content Sections -->
                @if($cellMeeting->minutes)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div
                            class="bg-orange-50 dark:bg-orange-900/20 px-10 py-8 border-b border-orange-100 dark:border-orange-800 flex justify-between items-center">
                            <div>
                                <h3
                                    class="text-xl font-black text-orange-900 dark:text-orange-400 flex items-center uppercase tracking-tighter">
                                    <i class="bi bi-file-earmark-text-fill mr-3 text-orange-600"></i>
                                    Atas e Registos
                                </h3>
                                <p
                                    class="text-[10px] font-black text-orange-600/70 dark:text-orange-400/50 mt-1 uppercase tracking-widest italic">
                                    Documentação Detalhada</p>
                            </div>
                            <i class="bi bi-journal-check text-4xl text-orange-200 dark:text-orange-700/30"></i>
                        </div>
                        <div class="p-10">
                            <article
                                class="prose prose-orange dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 font-medium leading-[1.8] text-lg">
                                {!! nl2br(e($cellMeeting->minutes)) !!}
                            </article>
                        </div>
                    </div>
                @endif

                @if($cellMeeting->decisions)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group">
                        <div class="bg-red-50/50 dark:bg-red-900/10 px-10 py-8 border-b border-red-100 dark:border-red-800/30">
                            <h4
                                class="text-xl font-black text-red-600 dark:text-red-400 uppercase tracking-tighter flex items-center">
                                <i class="bi bi-heart-fill mr-3 group-hover:scale-125 transition-transform duration-500"></i>
                                Decisões e Conversões
                            </h4>
                        </div>
                        <div class="p-10">
                            <div
                                class="p-8 bg-red-50/30 dark:bg-red-900/5 rounded-3xl text-red-900 dark:text-red-300 font-bold leading-relaxed border border-red-100/50 dark:border-red-800/20 text-lg">
                                {!! nl2br(e($cellMeeting->decisions)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Combined Attendance Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div
                        class="bg-gray-50 dark:bg-gray-900/50 px-10 py-8 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <h3
                                class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter flex items-center">
                                <i class="bi bi-check-all mr-3 text-emerald-500 text-2xl"></i>
                                Lista de Presenças
                            </h3>
                            <p
                                class="text-[10px] font-black text-gray-400 dark:text-gray-500 mt-1 uppercase tracking-widest italic">
                                Membros e Convidados</p>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Show Official Participants if not normal cell meeting -->
                            @if($cellMeeting->meeting_type !== 'normal')
                                @foreach($cellMeeting->participants as $participant)
                                    <div
                                        class="flex items-center gap-4 p-5 rounded-2xl bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100/50 dark:border-blue-800/30 transition-all hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black">
                                            {{ substr($participant->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white tracking-tight">
                                                {{ $participant->name }}
                                            </p>
                                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest">
                                                {{ $participant->role }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Show Cell Member Attendance -->
                            @if($cellMeeting->meeting_type === 'normal')
                                @foreach($cellMeeting->attendances ?? [] as $attendance)
                                    <div
                                        class="flex items-center justify-between p-5 rounded-2xl bg-emerald-50/30 dark:bg-emerald-900/5 border border-emerald-100/50 dark:border-emerald-800/20">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black">
                                                {{ substr($attendance->member->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 dark:text-white tracking-tight text-sm">
                                                    {{ $attendance->member->name ?? 'Membro Desconhecido' }}</p>
                                                <p
                                                    class="text-[10px] font-black text-emerald-600/60 dark:text-emerald-400/60 uppercase tracking-widest">
                                                    {{ $attendance->status ? 'Presente' : 'Ausente' }}</p>
                                                @if(!$attendance->status && $attendance->reason)
                                                    <p class="text-[9px] text-gray-500 italic mt-1">"{{ $attendance->reason }}"</p>
                                                @endif
                                            </div>
                                        </div>
                                        <i
                                            class="bi {{ $attendance->status ? 'bi-shield-check text-emerald-500' : 'bi-shield-x text-red-400' }} text-xl"></i>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Visitors -->
                            @foreach($cellMeeting->visitors ?? [] as $visitor)
                                <div
                                    class="flex flex-col p-6 rounded-2xl bg-purple-50/50 dark:bg-purple-900/10 border border-purple-100/50 dark:border-purple-800/30 group">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-12 h-12 rounded-2xl bg-purple-600 text-white flex items-center justify-center font-black shadow-lg shadow-purple-600/20">
                                                {{ substr($visitor->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900 dark:text-white tracking-tight">
                                                    {{ $visitor->name }}
                                                </p>
                                                <p class="text-[10px] font-black text-purple-600 uppercase tracking-widest">
                                                    Visitante</p>
                                            </div>
                                        </div>
                                        @if($visitor->pivot->converted)
                                            <span
                                                class="px-3 py-1 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-full text-[9px] font-black uppercase tracking-widest flex items-center gap-1">
                                                <i class="bi bi-heart-fill"></i> Decisão
                                            </span>
                                        @endif
                                    </div>
                                    @if($visitor->pivot->notes)
                                        <div
                                            class="bg-white dark:bg-gray-800 px-4 py-3 rounded-xl border border-purple-100 dark:border-purple-800/30 text-xs text-gray-600 dark:text-gray-400 italic">
                                            "{!! nl2br(e($visitor->pivot->notes)) !!}"
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Administrative Info -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-10 space-y-8">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-6">
                            Pastoral do Encontro</h4>
                        @if($cellMeeting->observations)
                            <div class="relative">
                                <i class="bi bi-quote absolute -top-4 -left-2 text-4xl text-blue-500/10"></i>
                                <p
                                    class="text-gray-600 dark:text-gray-300 font-medium leading-relaxed italic text-sm pl-4 border-l-2 border-blue-500/20">
                                    "{!! nl2br(e($cellMeeting->observations)) !!}"
                                </p>
                            </div>
                        @else
                            <p class="text-gray-400 text-xs italic">Sem observações pastorais.</p>
                        @endif
                    </div>

                    @if($cellMeeting->offering_amount > 0)
                        <div
                            class="bg-amber-50 dark:bg-amber-900/20 p-8 rounded-[2rem] border border-amber-100 dark:border-amber-800 flex flex-col items-center">
                            <i class="bi bi-cash-coin text-3xl text-amber-600 mb-2"></i>
                            <p class="text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-widest mb-1">
                                Oferta Arrecadada</p>
                            <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">
                                {{ number_format($cellMeeting->offering_amount, 2, ',', '.') }} MT
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Action Hub -->
                <div class="bg-gray-900 dark:bg-black rounded-[2.5rem] p-10 text-white space-y-6">
                    <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Centro de Operações</h4>
                    <div class="space-y-3">
                        <a href="{{ route('cells.show', $cellMeeting->cell) }}"
                            class="flex items-center gap-4 p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all font-bold group">
                            <div
                                class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="bi bi-house-door"></i>
                            </div>
                            <span class="text-sm">Explorar Unidade</span>
                        </a>

                        <button onclick="toggleEmailModal()"
                            class="w-full flex items-center gap-4 p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all font-bold group">
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="bi bi-send"></i>
                            </div>
                            <span class="text-sm">Notificar Equipa</span>
                        </button>
                    </div>

                    <div class="pt-6 border-t border-white/10">
                        <div
                            class="flex justify-between items-center text-[8px] font-bold text-gray-600 uppercase tracking-widest mb-4">
                            <span>Sincronização</span>
                            <i class="bi bi-hdd-network"></i>
                        </div>
                        <div class="space-y-4 opacity-60">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-clock-history text-xs"></i>
                                <div class="text-[10px]">
                                    <p class="font-black">Criado</p>
                                    <p>{{ $cellMeeting->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="bi bi-arrow-repeat text-xs"></i>
                                <div class="text-[10px]">
                                    <p class="font-black">Atualizado</p>
                                    <p>{{ $cellMeeting->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Modal (Tailwind Styled) -->
    <div id="emailModal"
        class="fixed inset-0 bg-gray-900/80 backdrop-blur-md z-50 hidden items-center justify-center p-6 bg-animate">
        <div
            class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-2xl w-full max-w-md overflow-hidden animate-zoom-in border border-white/20">
            <div class="bg-blue-600 p-8 text-white relative">
                <i class="bi bi-envelope-paper absolute right-8 top-1/2 -translate-y-1/2 text-5xl opacity-20"></i>
                <h4 class="text-2xl font-black flex items-center tracking-tighter">
                    Enviar Relatório
                </h4>
                <p class="text-xs font-bold text-blue-100 opacity-80 mt-1 uppercase tracking-widest">Distribuição Digital
                </p>
            </div>
            <form action="{{ route('cell-meetings.email', $cellMeeting) }}" method="POST" class="p-10 space-y-8">
                @csrf
                <div>
                    <label
                        class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Destinatário
                        Principal</label>
                    <input type="email" name="email" required placeholder="exemplo@igreja.com"
                        class="w-full bg-gray-50 dark:bg-gray-900 border-transparent focus:ring-4 focus:ring-blue-500/10 rounded-2xl p-5 font-bold text-gray-900 dark:text-white transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <button type="button" onclick="toggleEmailModal()"
                        class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-black text-[10px] uppercase tracking-widest py-5 rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                        CANCELAR
                    </button>
                    <button type="submit"
                        class="bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest py-5 rounded-2xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-600/30">
                        CONFIRMAR ENVIO
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleEmailModal() {
            const modal = document.getElementById('emailModal');
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    </script>
@endsection