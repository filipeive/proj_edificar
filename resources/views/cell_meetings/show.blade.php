@extends('layouts.app')

@section('title', 'Detalhes do Encontro')
@section('page-title', 'Detalhes do Encontro')
@section('page-subtitle', 'Informações completas sobre a reunião')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <button onclick="toggleEmailModal()"
            class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:text-blue-600 transition-all"
            title="Enviar por email">
            <i class="bi bi-envelope"></i>
        </button>
        <a href="{{ route('cell-meetings.pdf', $cellMeeting) }}"
            class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:text-orange-600 transition-all"
            title="Exportar PDF">
            <i class="bi bi-file-earmark-pdf"></i>
        </a>
        <a href="{{ route('cell-meetings.export', ['meeting_id' => $cellMeeting->id]) }}"
            class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:text-emerald-600 transition-all"
            title="Exportar Excel">
            <i class="bi bi-file-earmark-excel"></i>
        </a>

        @can('update', $cellMeeting)
            <a href="{{ route('cell-meetings.edit', $cellMeeting) }}"
                class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:text-blue-500 transition-all"
                title="Editar">
                <i class="bi bi-pencil-square"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="space-y-8 animate-fade-in">
        <!-- Premium Hero & Context -->
        <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[3rem] blur opacity-25 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-br from-blue-700 via-indigo-800 to-slate-900 px-8 md:px-14 py-16 text-white relative overflow-hidden">
                    <!-- Decorative patterns -->
                    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl -ml-10 -mb-10"></div>
                    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/graphy.png')]"></div>
                    
                    <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-end gap-10">
                        <div class="space-y-6 max-w-3xl">
                            <!-- Voltar-->
                            <div class="flex flex-wrap items-center gap-4">
                                <a href="{{ route('cell-meetings.index') }}"
                                    class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:text-blue-600 transition-all"
                                    title="Voltar">
                                    <i class="bi bi-arrow-left"></i>
                                </a>
                            </div>
                            <div class="flex flex-wrap items-center gap-4">
                                @php
                                    $typeStyles = [
                                        'normal' => 'bg-emerald-400/20 text-emerald-300 border-emerald-400/30',
                                        'leadership' => 'bg-purple-400/20 text-purple-300 border-purple-400/30',
                                        'supervision' => 'bg-amber-400/20 text-amber-300 border-amber-400/30',
                                        'zone' => 'bg-blue-400/20 text-blue-300 border-blue-400/30',
                                    ];
                                    $typeLabel = [
                                        'normal' => 'Reunião de Célula',
                                        'leadership' => 'Liderança Ativa',
                                        'supervision' => 'Supervisão Geral',
                                        'zone' => 'Encontro de Zona',
                                    ];
                                    $style = $typeStyles[$cellMeeting->meeting_type] ?? 'bg-white/10 text-white border-white/20';
                                @endphp
                                <span class="px-5 py-2 backdrop-blur-md rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] border shadow-2xl {{ $style }}">
                                    {{ $typeLabel[$cellMeeting->meeting_type] ?? 'Encontro Especial' }}
                                </span>
                                
                                <div class="flex items-center gap-2 px-4 py-2 bg-black/20 backdrop-blur-md rounded-2xl border border-white/10 text-blue-100 text-xs font-bold shadow-lg">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $cellMeeting->meeting_date->format('d/m/Y') }}
                                </div>
                            </div>

                            <h1 class="text-5xl md:text-7xl font-black tracking-tight leading-[0.9] drop-shadow-2xl">
                                {{ $cellMeeting->cell?->name ?? $cellMeeting->zone?->name ?? $cellMeeting->supervision?->name ?? $cellMeeting->meeting_type_label }}
                            </h1>
                            
                            <div class="flex flex-wrap gap-3 mt-4">
                                @if($cellMeeting->cell)
                                    <a href="{{ route('cells.show', $cellMeeting->cell) }}"
                                        class="flex items-center bg-white/10 hover:bg-white/20 px-6 py-3 rounded-2xl border border-white/10 transition-all backdrop-blur-md text-[10px] font-black uppercase tracking-widest group/link">
                                        <i class="bi bi-box-arrow-in-right mr-3 group-hover/link:translate-x-1 transition-transform"></i>
                                        Célula: {{ $cellMeeting->cell->name }}
                                    </a>
                                    @if($cellMeeting->cell->supervision)
                                        <div class="flex items-center bg-black/30 px-5 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-white/5">
                                            <i class="bi bi-diagram-3 mr-3 opacity-60"></i> 
                                            {{ $cellMeeting->cell->supervision->name }}
                                        </div>
                                    @endif
                                @elseif($cellMeeting->supervision)
                                    <a href="{{ route('supervisions.show', $cellMeeting->supervision) }}"
                                        class="flex items-center bg-white/10 hover:bg-white/20 px-6 py-3 rounded-2xl border border-white/10 transition-all backdrop-blur-md text-[10px] font-black uppercase tracking-widest group/link">
                                        <i class="bi bi-diagram-3 mr-3 group-hover/link:scale-110 transition-transform"></i>
                                        Supervisão: {{ $cellMeeting->supervision->name }}
                                    </a>
                                @endif
                                
                                @if($cellMeeting->leader)
                                    <div class="flex items-center bg-white/5 px-5 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-white/5 opacity-80">
                                        <i class="bi bi-person-badge mr-3"></i> 
                                        Líder: {{ $cellMeeting->leader->name }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white/10 backdrop-blur-2xl p-8 rounded-[2.5rem] border border-white/20 shadow-2xl flex flex-col items-center lg:items-end min-w-[200px] group/stats hover:bg-white/15 transition-all">
                            <span class="text-[11px] font-black uppercase tracking-[0.3em] text-blue-200 mb-2">Impacto Real</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-6xl font-black tracking-tighter group-hover/stats:scale-105 transition-transform">{{ $cellMeeting->adults_count + $cellMeeting->children_count + $cellMeeting->visitors_count }}</span>
                                <i class="bi bi-people-fill text-3xl opacity-30"></i>
                            </div>
                            <p class="text-[9px] font-black text-white/50 uppercase mt-2 tracking-widest">Pessoas Presentes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <div class="p-8 md:p-14 grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="space-y-12">
                    <!-- Ministry Info -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-8 bg-blue-600 rounded-full"></div>
                            <h4 class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em]">Ministração</h4>
                        </div>
                        
                        <div class="group relative">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl blur opacity-20 group-hover:opacity-40 transition duration-500"></div>
                            <div class="relative flex items-start gap-6 p-8 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-gray-100 dark:border-gray-800 transition-colors group-hover:bg-white dark:group-hover:bg-gray-900">
                                <div class="w-16 h-16 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-3xl shrink-0 shadow-lg shadow-blue-600/20 group-hover:scale-110 transition-transform">
                                    <i class="bi bi-chat-heart"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-2 opacity-70">Mensagem Central</p>
                                    <p class="text-3xl font-black text-gray-900 dark:text-white leading-[1.1] tracking-tighter">
                                        "{{ $cellMeeting->theme ?? 'Maturidade Cristã' }}"
                                    </p>
                                    @if($cellMeeting->biblical_text)
                                        <div class="mt-4 flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400">
                                            <i class="bi bi-book text-blue-500"></i>
                                            {{ $cellMeeting->biblical_text }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leadership Info -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-8 bg-indigo-600 rounded-full"></div>
                            <h4 class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em]">Liderança Responsável</h4>
                        </div>
                        
                        <div class="flex items-center gap-6 p-6 bg-white dark:bg-gray-800/50 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-indigo-600 to-blue-700 text-white flex items-center justify-center text-4xl font-black shadow-xl shadow-indigo-600/20 border-4 border-white dark:border-gray-700">
                                {{ substr($cellMeeting->leader->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                                    {{ $cellMeeting->leader->name }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-indigo-100 dark:border-indigo-800">
                                        {{ $cellMeeting->leader->role }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Engagement Metrics Grid -->
                <div class="relative">
                    <div class="bg-gray-50 dark:bg-gray-900/60 rounded-[3rem] p-12 border border-blue-50 dark:border-gray-800 relative h-full flex flex-col justify-center">
                        <h4 class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em] text-center mb-12">Detalhamento de Fluência</h4>
                        
                        <div class="grid grid-cols-2 gap-10">
                            @php
                                $metrics = [
                                    ['label' => 'Adultos', 'value' => $cellMeeting->adults_count, 'icon' => 'bi-person', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
                                    ['label' => 'Crianças', 'value' => $cellMeeting->children_count, 'icon' => 'bi-emoji-smile', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
                                    ['label' => 'Visitantes', 'value' => $cellMeeting->visitors_count, 'icon' => 'bi-person-plus', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50'],
                                    ['label' => 'Participação', 'value' => $cellMeeting->adults_count + $cellMeeting->children_count + $cellMeeting->visitors_count, 'icon' => 'bi-people', 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50', 'highlight' => true],
                                ];
                            @endphp
                            @foreach($metrics as $metric)
                                <div class="text-center group">
                                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl {{ $metric['bg'] }} dark:bg-white/5 flex items-center justify-center {{ $metric['color'] }} text-2xl group-hover:scale-110 transition-all duration-500 group-hover:rotate-3">
                                        <i class="{{ $metric['icon'] }}"></i>
                                    </div>
                                    <p class="text-5xl font-black {{ isset($metric['highlight']) ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-900 dark:text-white' }} tracking-tighter tabular-nums">
                                        {{ str_pad($metric['value'], 2, '0', STR_PAD_LEFT) }}
                                    </p>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] {{ $metric['color'] }} mt-2 opacity-80 font-inter">
                                        {{ $metric['label'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-10">
                <!-- Content Sections -->
                @if($cellMeeting->minutes)
                    <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group/card hover:shadow-xl transition-all duration-500">
                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/10 dark:to-amber-900/10 px-10 py-10 border-b border-orange-100 dark:border-orange-800/30 flex justify-between items-center">
                            <div class="space-y-1">
                                <h3 class="text-2xl font-black text-orange-950 dark:text-orange-400 flex items-center tracking-tighter uppercase italic">
                                    <span class="w-8 h-8 rounded-lg bg-orange-600 text-white flex items-center justify-center mr-3 text-sm not-italic">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </span>
                                    Ficha de Ocorrência
                                </h3>
                                <p class="text-[10px] font-black text-orange-600/70 dark:text-orange-400/50 uppercase tracking-[0.2em]">Registos e Deliberações</p>
                            </div>
                            <div class="w-16 h-16 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-inner group-hover/card:rotate-12 transition-transform">
                                <i class="bi bi-journal-check text-3xl text-orange-400 opacity-40"></i>
                            </div>
                        </div>
                        <div class="p-12">
                            <article class="prose prose-orange dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 font-medium leading-[1.8] text-lg selection:bg-orange-100 dark:selection:bg-orange-900/30 first-letter:text-5xl first-letter:font-black first-letter:text-orange-600 first-letter:mr-3 first-letter:float-left">
                                {!! nl2br(e($cellMeeting->minutes)) !!}
                            </article>
                        </div>
                    </div>
                @endif

                @if($cellMeeting->decisions)
                    <div class="group relative">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-red-600 to-rose-600 rounded-[3rem] blur opacity-15 group-hover:opacity-30 transition"></div>
                        <div class="relative bg-white dark:bg-gray-800 rounded-[3rem] shadow-sm border border-red-50 dark:border-red-900/20 overflow-hidden">
                            <div class="bg-red-50/30 dark:bg-red-900/10 px-10 py-8 border-b border-red-100 dark:border-red-800/20 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-red-600 text-white flex items-center justify-center shadow-lg shadow-red-600/20">
                                    <i class="bi bi-heart-fill"></i>
                                </div>
                                <h4 class="text-xl font-black text-red-700 dark:text-red-400 uppercase tracking-tighter">Colheita do Dia</h4>
                            </div>
                            <div class="p-10">
                                <div class="p-8 bg-red-50/30 dark:bg-red-900/5 rounded-[2rem] text-red-900 dark:text-red-200 font-bold leading-relaxed border border-dashed border-red-200 dark:border-red-800/30 text-xl italic select-all">
                                    "{!! nl2br(e($cellMeeting->decisions)) !!}"
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Combined Attendance Section -->
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-900/50 px-10 py-10 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div class="space-y-1">
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tighter flex items-center">
                                <i class="bi bi-person-check-fill mr-4 text-emerald-500 text-3xl"></i>
                                Rol de Participação
                            </h3>
                            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Fluxo Consolidado de Presenças</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-4 py-2 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-emerald-200 dark:border-emerald-800">
                                {{ $cellMeeting->attendances?->count() ?? 0 }} Membros
                            </span>
                            <span class="px-4 py-2 bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-purple-200 dark:border-purple-800">
                                {{ $cellMeeting->visitors?->count() ?? 0 }} Novos
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Official Participants -->
                            @if($cellMeeting->meeting_type !== 'normal')
                                @foreach($cellMeeting->participants as $participant)
                                    <div class="flex items-center gap-5 p-5 rounded-3xl bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100/50 dark:border-blue-800/30 transition-all hover:translate-y-[-4px] hover:shadow-lg group">
                                        <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-blue-600/20 group-hover:scale-110 transition-transform">
                                            {{ substr($participant->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-gray-900 dark:text-white text-lg tracking-tight">
                                                {{ $participant->name }}
                                            </p>
                                            <span class="px-3 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-[9px] font-black uppercase tracking-widest rounded-lg border border-blue-200/50">
                                                {{ $participant->role }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Cell Member Attendance -->
                            @if($cellMeeting->meeting_type === 'normal')
                                @foreach($cellMeeting->attendances ?? [] as $attendance)
                                    <div class="flex items-center justify-between p-6 rounded-3xl bg-white dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 hover:border-emerald-500/30 transition-all hover:bg-emerald-50/10">
                                        <div class="flex items-center gap-5">
                                            <div class="relative">
                                                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-black text-slate-600 dark:text-slate-400 shadow-inner">
                                                    {{ substr($attendance->member->name ?? '?', 0, 1) }}
                                                </div>
                                                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full {{ $attendance->status ? 'bg-emerald-500 shadow-emerald-500/50' : 'bg-rose-400 shadow-rose-400/50' }} flex items-center justify-center text-white text-[10px] border-2 border-white dark:border-gray-800 shadow-lg">
                                                    <i class="bi {{ $attendance->status ? 'bi-check-lg' : 'bi-x' }}"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 dark:text-white leading-tight">
                                                    {{ $attendance->member->name ?? 'Membro' }}
                                                </p>
                                                @if(!$attendance->status && $attendance->reason)
                                                    <p class="text-[10px] text-rose-500 italic mt-1 font-medium bg-rose-50 dark:bg-rose-950/30 px-2 py-0.5 rounded-md">
                                                        "{{ $attendance->reason }}"
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-black uppercase tracking-widest {{ $attendance->status ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500 opacity-60' }}">
                                            {{ $attendance->status ? 'PRESENTE' : 'AUSENTE' }}
                                        </span>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Visitors Card -->
                            @foreach($cellMeeting->visitors ?? [] as $visitor)
                                <div class="flex flex-col p-8 rounded-3xl bg-gradient-to-br from-purple-50 via-white to-purple-50 dark:from-purple-900/10 dark:via-gray-800 dark:to-purple-900/10 border border-purple-100 dark:border-purple-800/30 group/visitor shadow-sm hover:shadow-xl transition-all duration-500">
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="flex items-center gap-5">
                                            <div class="w-16 h-16 rounded-2xl bg-purple-600 text-white flex items-center justify-center text-2xl font-black shadow-xl shadow-purple-600/30 group-hover/visitor:rotate-3 transition-transform">
                                                {{ substr($visitor->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-xl font-black text-gray-900 dark:text-white tracking-tight">
                                                    {{ $visitor->name }}
                                                </p>
                                                <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-purple-200">
                                                    <i class="bi bi-stars"></i> Visitante
                                                </span>
                                            </div>
                                        </div>
                                        @if($visitor->isIntegrated())
                                            <div class="flex flex-col items-end gap-1">
                                                <span class="px-4 py-1.5 bg-rose-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 animate-pulse shadow-lg shadow-rose-600/30">
                                                    <i class="bi bi-heart-fill"></i> DECISÃO
                                                </span>
                                                <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest">Integrado</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($visitor->notes)
                                        <div class="bg-white/50 dark:bg-black/20 p-5 rounded-2xl border border-purple-100/50 dark:border-purple-800/20 text-sm text-gray-600 dark:text-gray-400 italic leading-relaxed relative">
                                            <i class="bi bi-quote absolute -top-2 -left-1 text-2xl text-purple-200 opacity-50"></i>
                                            "{!! nl2br(e($visitor->notes)) !!}"
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
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-10 space-y-10">
                        <div class="space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-6 bg-slate-400 rounded-full"></div>
                                <h4 class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.3em]">Cofre Pastoral</h4>
                            </div>
                            
                            @if($cellMeeting->observations)
                                <div class="relative p-6 bg-slate-50 dark:bg-slate-900/40 rounded-3xl border border-slate-100 dark:border-slate-800">
                                    <i class="bi bi-quote absolute top-2 right-4 text-5xl text-blue-500/10"></i>
                                    <p class="text-gray-600 dark:text-gray-300 font-medium leading-relaxed italic text-sm">
                                        "{!! nl2br(e($cellMeeting->observations)) !!}"
                                    </p>
                                </div>
                            @else
                                <div class="p-6 bg-slate-50/50 dark:bg-slate-900/20 rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 text-center">
                                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Sem notas pastorais</p>
                                </div>
                            @endif
                        </div>

                        @if($cellMeeting->offering_amount > 0)
                            <div class="group relative">
                                <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-400 to-orange-500 rounded-[2.5rem] blur opacity-20 group-hover:opacity-40 transition"></div>
                                <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 p-8 rounded-[2rem] border border-amber-100 dark:border-amber-800 flex flex-col items-center">
                                    <div class="w-14 h-14 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl mb-4 shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
                                    <p class="text-[10px] font-black text-amber-700 dark:text-amber-500 uppercase tracking-[0.2em] mb-1">Tesouraria</p>
                                    <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter tabular-nums">
                                        {{ number_format($cellMeeting->offering_amount, 2, ',', '.') }} <span class="text-xs text-amber-600">MT</span>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Hub -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-slate-800 to-black rounded-[3rem] blur opacity-20 transition duration-1000"></div>
                    <div class="relative bg-slate-900 dark:bg-black rounded-[3rem] p-10 text-white space-y-8 overflow-hidden shadow-2xl">
                        <!-- BG elements -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10">
                            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-8 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Centro de Controlo
                            </h4>
                            
                            <div class="space-y-4">
                                @if($cellMeeting->cell)
                                <a href="{{ route('cells.show', $cellMeeting->cell) }}"
                                    class="flex items-center gap-5 p-5 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group/btn">
                                    <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-600/20 group-hover/btn:scale-110 transition-transform">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black uppercase tracking-widest text-white">Visualizar</span>
                                        <span class="text-[10px] text-white/50 font-medium italic">Unidade de Base</span>
                                    </div>
                                </a>
                                @endif

                                <button onclick="toggleEmailModal()"
                                    class="w-full flex items-center gap-5 p-5 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group/btn text-left">
                                    <div class="w-12 h-12 rounded-xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover/btn:scale-110 transition-transform">
                                        <i class="bi bi-send-fill"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black uppercase tracking-widest text-white">Notificar</span>
                                        <span class="text-[10px] text-white/50 font-medium italic">Distribuição Digital</span>
                                    </div>
                                </button>

                                <a href="{{ route('cell-meetings.pdf', $cellMeeting) }}"
                                    class="w-full flex items-center gap-5 p-5 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group/btn">
                                    <div class="w-12 h-12 rounded-xl bg-orange-600 flex items-center justify-center shadow-lg shadow-orange-600/20 group-hover/btn:scale-110 transition-transform">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black uppercase tracking-widest text-white text-left">Baixar Acta</span>
                                        <span class="text-[10px] text-white/50 font-medium italic">Documento PDF</span>
                                    </div>
                                </a>

                                <a href="{{ route('cell-meetings.export', ['meeting_id' => $cellMeeting->id]) }}"
                                    class="w-full flex items-center gap-5 p-5 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all group/btn">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover/btn:scale-110 transition-transform">
                                        <i class="bi bi-file-earmark-excel-fill"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black uppercase tracking-widest text-white text-left">Relatório XL</span>
                                        <span class="text-[10px] text-white/50 font-medium italic">Backup Estruturado</span>
                                    </div>
                                </a>
                            </div>

                            <div class="mt-10 pt-10 border-t border-white/5 space-y-6">
                                <div class="flex justify-between items-center text-[9px] font-black text-slate-600 uppercase tracking-widest">
                                    <span>Sync Status</span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Ligado
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1 p-3 bg-white/5 rounded-xl border border-white/5">
                                        <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Criado em</p>
                                        <p class="text-[10px] font-bold text-white/80 tabular-nums">{{ $cellMeeting->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="space-y-1 p-3 bg-white/5 rounded-xl border border-white/5">
                                        <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Update</p>
                                        <p class="text-[10px] font-bold text-white/80 tabular-nums">{{ $cellMeeting->updated_at->format('H:i') }}h</p>
                                    </div>
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
