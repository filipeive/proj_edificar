@extends('layouts.app')

@section('title', 'Ficha Guia de Participação - ' . $cell->name)
@section('page-title', 'Ficha Guia')

@section('content')
<div class="w-full px-4 md:px-10 space-y-8 animate-In" x-data="{ activeTab: 'attendance', saving: false }">
    <!-- Header Card -->
    <div class="bg-zinc-950 rounded-2xl p-6 md:p-8 shadow-md border border-zinc-900 relative overflow-hidden text-white">
        <div class="absolute top-0 right-0 w-32 h-32 bg-orange-600/10 rounded-full -mr-16 -mt-16 blur-3xl"></div>
        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('cells.show', $cell) }}" 
                        class="w-10 h-10 rounded-xl bg-white/5 text-gray-400 hover:text-white hover:bg-white/10 flex items-center justify-center transition-all border border-white/5">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-[9px] font-black text-orange-500 uppercase tracking-[0.2em]">
                            <i class="bi bi-calendar-check-fill"></i>
                            <span>Ficha Guia de Participação</span>
                        </div>
                        <h1 class="text-2xl font-black tracking-tight uppercase">{{ $cell->name }}</h1>
                    </div>
                </div>
                
                <!-- Leadership Info -->
                <div class="flex flex-wrap gap-3 pt-1">
                    @if($cell->leader)
                        <div class="flex items-center gap-2 bg-orange-500/10 px-3.5 py-1.5 rounded-xl border border-orange-500/10">
                            <i class="bi bi-person-badge text-orange-400"></i>
                            <span class="text-[9px] font-black uppercase text-gray-300 tracking-wider">Líder: <span class="text-white">{{ $cell->leader->name }}</span></span>
                        </div>
                    @endif
                    @foreach($cell->timoteos as $timoteo)
                        <div class="flex items-center gap-2 bg-zinc-800 px-3.5 py-1.5 rounded-xl border border-zinc-700/50">
                            <i class="bi bi-person-hearts text-orange-450"></i>
                            <span class="text-[9px] font-black uppercase text-gray-300 tracking-wider">Auxiliar: <span class="text-white">{{ $timoteo->name }}</span></span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white/5 p-3 rounded-xl border border-white/5 backdrop-blur-md w-full lg:w-auto">
                <form action="{{ route('cells.attendance', $cell) }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-2">
                    <div class="grid grid-cols-2 gap-2 w-full sm:w-auto">
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Mês</label>
                            <select name="month" data-searchable="false" class="h-[44px] bg-white/10 border border-white/10 text-white rounded-lg text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-orange-500 focus:border-orange-500 custom-select px-4 cursor-pointer appearance-none">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ano</label>
                            <select name="year" data-searchable="false" class="h-[44px] bg-white/10 border border-white/10 text-white rounded-lg text-xs font-black uppercase tracking-widest focus:ring-2 focus:ring-orange-500 focus:border-orange-500 custom-select px-4 cursor-pointer appearance-none">
                                @foreach(range(now()->year - 1, now()->year + 1) as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="h-[44px] w-full sm:w-auto bg-orange-600 hover:bg-orange-700 text-white px-6 rounded-lg transition-all font-black text-xs uppercase tracking-widest">
                        Filtrar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js Tabs Navigation -->
    <div class="border-b border-gray-200 dark:border-zinc-800">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <button 
                @click="activeTab = 'attendance'" 
                :class="activeTab === 'attendance' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all">
                <i class="bi bi-calendar-check mr-2"></i> Controle de Presença
            </button>
            <button 
                @click="activeTab = 'decisions'" 
                :class="activeTab === 'decisions' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all">
                <i class="bi bi-stars mr-2"></i> Visitas e Decisões
            </button>
            <button 
                @click="activeTab = 'discipleship'" 
                :class="activeTab === 'discipleship' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:border-gray-300'"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all">
                <i class="bi bi-mortarboard mr-2"></i> Acompanhamento & Discipulado
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div>
        <!-- TAB 1: ATTENDANCE -->
        <div x-show="activeTab === 'attendance'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-150 dark:border-zinc-850 overflow-hidden">
                <div class="bg-gray-50 dark:bg-zinc-850/50 border-b border-gray-150 dark:border-zinc-850 p-6 flex justify-between items-center">
                    <h3 class="text-xs font-black text-gray-800 dark:text-zinc-200 uppercase tracking-widest flex items-center gap-2">
                        <i class="bi bi-table"></i> Controle de Presença - {{ Carbon\Carbon::create()->month($month)->translatedFormat('F') }} / {{ $year }}
                    </h3>
                    <div class="hidden md:flex gap-4 text-[9px] font-black uppercase tracking-widest text-gray-400 dark:text-zinc-500">
                        <span class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-md bg-orange-500"></div> Célula</span>
                        <span class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-md bg-zinc-800 dark:bg-zinc-700"></div> Culto</span>
                        <span class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-md bg-orange-600/35"></div> Doutrina</span>
                    </div>
                </div>

                <form action="{{ route('cells.attendance.store', $cell) }}" method="POST" @submit="saving = true">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <!-- Horizontal Scroll Container with dynamic fade shadows -->
                    <div class="table-responsive-container table-responsive-shadows border-none shadow-none mb-0">
                        <table class="w-full border-collapse text-[11px]">
                            <thead>
                                <tr class="bg-gray-100/80 dark:bg-zinc-850/40 text-gray-600 dark:text-zinc-400 font-black uppercase border-b border-gray-150 dark:border-zinc-850">
                                    <th class="p-3 text-left border-r border-gray-150 dark:border-zinc-850 sticky left-0 bg-gray-100 dark:bg-zinc-900 z-10" rowspan="2">Nome do Membro</th>
                                    <th class="p-2 text-center border-r border-gray-150 dark:border-zinc-850 bg-orange-50/50 dark:bg-orange-950/10 text-orange-850 dark:text-orange-400" colspan="{{ count($saturdays) }}">Sábados (Célula)</th>
                                    <th class="p-2 text-center border-r border-gray-150 dark:border-zinc-850 bg-zinc-100/55 dark:bg-zinc-800/20 text-zinc-800 dark:text-zinc-300" colspan="{{ count($sundays) }}">Domingos (Culto)</th>
                                    <th class="p-2 text-center border-r border-gray-150 dark:border-zinc-850 bg-orange-50/20 dark:bg-orange-950/5 text-orange-900 dark:text-orange-300/80" colspan="{{ count($wednesdays) }}">4ª Feira (Doutrina)</th>
                                    <th class="p-3 text-left" rowspan="2">Observações</th>
                                </tr>
                                <tr class="bg-white dark:bg-zinc-900/10 text-gray-400 dark:text-zinc-550 font-bold border-b border-gray-150 dark:border-zinc-850">
                                    @foreach($saturdays as $sat) <th class="p-1 border-r border-gray-150 dark:border-zinc-850 text-center">{{ $sat->format('d/m') }}</th> @endforeach
                                    @foreach($sundays as $sun) <th class="p-1 border-r border-gray-150 dark:border-zinc-850 text-center">{{ $sun->format('d/m') }}</th> @endforeach
                                    @foreach($wednesdays as $wed) <th class="p-1 border-r border-gray-150 dark:border-zinc-850 text-center">{{ $wed->format('d/m') }}</th> @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-zinc-850">
                                @foreach($members as $member)
                                <tr class="hover:bg-orange-500/[0.02] dark:hover:bg-orange-500/[0.01] transition-colors group">
                                    <td class="p-3 border-r border-gray-150 dark:border-zinc-850 sticky left-0 bg-white dark:bg-zinc-900 group-hover:bg-gray-50 dark:group-hover:bg-zinc-850/50 z-10 font-bold text-gray-900 dark:text-white border-l border-gray-100 dark:border-zinc-850 uppercase tracking-tight">
                                        {{ $member->name }}
                                    </td>
                                    
                                    {{-- Célula (Saturdays) --}}
                                    @foreach($saturdays as $sat)
                                    <td class="p-0 text-center border-r border-gray-150 dark:border-zinc-850 bg-orange-50/5 dark:bg-orange-950/2">
                                        <label class="flex items-center justify-center p-2 cursor-pointer h-full w-full">
                                            <input type="checkbox" name="attendance[{{ $member->id }}][cell][{{ $sat->format('Y-m-d') }}]" value="1"
                                                class="w-4 h-4 text-orange-600 border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded focus:ring-orange-500"
                                                {{ isset($attendances[$member->id]['cell'][$sat->format('Y-m-d')]) && $attendances[$member->id]['cell'][$sat->format('Y-m-d')]->first()->status ? 'checked' : '' }}>
                                        </label>
                                    </td>
                                    @endforeach

                                    {{-- Culto (Sundays) --}}
                                    @foreach($sundays as $sun)
                                    <td class="p-0 text-center border-r border-gray-150 dark:border-zinc-850 bg-zinc-50/20 dark:bg-zinc-800/5">
                                        <label class="flex items-center justify-center p-2 cursor-pointer h-full w-full">
                                            <input type="checkbox" name="attendance[{{ $member->id }}][service][{{ $sun->format('Y-m-d') }}]" value="1"
                                                class="w-4 h-4 text-zinc-700 border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded focus:ring-zinc-500"
                                                {{ isset($attendances[$member->id]['service'][$sun->format('Y-m-d')]) && $attendances[$member->id]['service'][$sun->format('Y-m-d')]->first()->status ? 'checked' : '' }}>
                                        </label>
                                    </td>
                                    @endforeach

                                    {{-- 4ª Feira --}}
                                    @foreach($wednesdays as $wed)
                                    <td class="p-0 text-center border-r border-gray-150 dark:border-zinc-850 bg-orange-500/[0.01]">
                                        <label class="flex items-center justify-center p-2 cursor-pointer h-full w-full">
                                            <input type="checkbox" name="attendance[{{ $member->id }}][wednesday][{{ $wed->format('Y-m-d') }}]" value="1"
                                                class="w-4 h-4 text-orange-700 border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded focus:ring-orange-500"
                                                {{ isset($attendances[$member->id]['wednesday'][$wed->format('Y-m-d')]) && $attendances[$member->id]['wednesday'][$wed->format('Y-m-d')]->first()->status ? 'checked' : '' }}>
                                        </label>
                                    </td>
                                    @endforeach

                                    <td class="p-2 border-r border-gray-100 dark:border-zinc-850">
                                        <input type="text" name="reason[{{ $member->id }}]" placeholder="..." 
                                            class="w-full bg-transparent border-none text-[10px] text-gray-400 dark:text-zinc-500 focus:ring-0 p-1 font-semibold">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 bg-gray-50 dark:bg-zinc-850/30 border-t border-gray-150 dark:border-zinc-850 flex justify-end">
                        <button type="submit" 
                            class="bg-zinc-950 hover:bg-black text-white dark:bg-orange-600 dark:hover:bg-orange-700 px-8 py-3 rounded-xl transition-all font-black text-xs uppercase tracking-widest shadow-md flex items-center gap-3"
                            :disabled="saving">
                            <span x-show="!saving" class="flex items-center gap-2">
                                <i class="bi bi-save2-fill"></i> Salvar Registro de Frequência
                            </span>
                            <span x-show="saving">Processando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAB 2: DECISIONS & VISITORS -->
        <div x-show="activeTab === 'decisions'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-150 dark:border-zinc-850 p-6 md:p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-zinc-850 pb-4">
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-[0.15em] flex items-center gap-2">
                        <i class="bi bi-graph-up text-orange-500"></i> Relatório de Decisões e Visitas
                    </h3>
                    <div class="flex gap-2">
                        <button onclick="toggleModal('visitorModal')" class="bg-orange-600 text-white px-3.5 py-2 rounded-xl text-[9px] font-black uppercase hover:bg-orange-700 transition-all shadow-sm">+ Visita</button>
                        <button onclick="toggleModal('conversionModal')" class="bg-zinc-950 text-white dark:bg-zinc-800 px-3.5 py-2 rounded-xl text-[9px] font-black uppercase hover:bg-black dark:hover:bg-zinc-700 transition-all border border-zinc-800 shadow-sm">+ Decisão</button>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-zinc-850/30 p-4 rounded-xl border border-gray-100 dark:border-zinc-850/50 text-center">
                        <p class="text-2xl font-black text-orange-600">{{ $visitors->count() }}</p>
                        <p class="text-[8px] font-black text-gray-400 dark:text-zinc-550 uppercase">Visitas</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-zinc-850/30 p-4 rounded-xl border border-gray-100 dark:border-zinc-850/50 text-center">
                        <p class="text-2xl font-black text-orange-500">{{ $conversions->where('is_new_salvation', true)->count() }}</p>
                        <p class="text-[8px] font-black text-gray-400 dark:text-zinc-550 uppercase">Salvações</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-zinc-850/30 p-4 rounded-xl border border-gray-100 dark:border-zinc-850/50 text-center">
                        <p class="text-2xl font-black text-stone-600 dark:text-zinc-400">{{ $conversions->where('is_water_baptism_candidate', true)->count() }}</p>
                        <p class="text-[8px] font-black text-gray-400 dark:text-zinc-550 uppercase">Batismos</p>
                    </div>
                </div>

                <div class="space-y-3 mt-4 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                    @forelse($visitors as $visitor)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-zinc-850/20 rounded-xl border border-gray-100 dark:border-zinc-850/50 group/visitor">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/25 text-orange-655 flex items-center justify-center text-sm">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase truncate">{{ $visitor->name }}</p>
                                <span class="text-[8px] font-bold text-gray-400 dark:text-zinc-500">{{ $visitor->visit_date->format('d/m') }} • 
                                    @if($visitor->isIntegrated())
                                        <span class="text-green-600 font-bold">INTEGRADO</span>
                                    @else
                                        {{ strtoupper($visitor->contact_status) }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex gap-1 opacity-0 group-hover/visitor:opacity-100 transition-all">
                                <button onclick="openVisitorDetailsModal({{ json_encode($visitor) }})" class="p-1.5 text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-950/20 rounded-lg transition-all" title="Ver Detalhes">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if(!$visitor->isIntegrated())
                                    <a href="{{ route('members.create', ['visitor_id' => $visitor->id]) }}" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Tornar Membro">
                                        <i class="bi bi-person-plus"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-10 text-gray-400 dark:text-zinc-550 text-[10px] uppercase font-bold tracking-wider">Nenhuma visita registada neste mês.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 3: DISCIPLESHIP -->
        <div x-show="activeTab === 'discipleship'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            <div class="bg-white dark:bg-zinc-900/30 rounded-2xl shadow-sm border border-gray-150 dark:border-zinc-850 p-6 md:p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-zinc-850 pb-4">
                    <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-[0.15em] flex items-center gap-2">
                        <i class="bi bi-mortarboard text-orange-500"></i> Discipulado e Acompanhamento
                    </h3>
                    <button onclick="toggleModal('discipleshipModal')" class="bg-orange-600 text-white px-3.5 py-2 rounded-xl text-[9px] font-black uppercase hover:bg-orange-700 transition-all shadow-sm">+ Novo Acompanhamento</button>
                </div>

                <div class="space-y-3">
                    @forelse($discipleships as $discipleship)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-zinc-850/20 rounded-xl border border-gray-100 dark:border-zinc-850/50 group/item">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/25 text-orange-655 flex items-center justify-center text-sm">
                                <i class="bi bi-book"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase truncate">{{ $discipleship->user->name }}</p>
                                <span class="text-[8px] font-bold text-gray-400 dark:text-zinc-500">LIÇÃO {{ $discipleship->current_lesson ?? '1' }} • MENTOR: {{ $discipleship->mentor_name ?? '---' }}</span>
                                @if($discipleship->observations)
                                    <p class="text-[8px] text-gray-550 dark:text-zinc-500 italic mt-0.5 truncate">{{ $discipleship->observations }}</p>
                                @endif
                            </div>
                            <div class="flex gap-1 opacity-0 group-hover/item:opacity-100 transition-all">
                                <button onclick="openEditDiscipleshipModal({{ json_encode($discipleship) }})" class="p-1.5 text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-950/20 rounded-lg transition-all" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('cells.discipleships.destroy', [$cell, $discipleship]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este registro?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-650 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-lg transition-all" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-10 text-gray-400 dark:text-zinc-550 text-[10px] uppercase font-bold tracking-wider">Sem discipulados ativos nesta célula.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div id="visitorModal" class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-md p-6 md:p-8 shadow-xl animate-In border border-gray-100 dark:border-zinc-800">
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="bi bi-person-plus text-orange-600"></i> Registar Visita
        </h3>
        <form action="{{ route('cells.visitors.store', $cell) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Nome Completo</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Telefone</label>
                    <input type="text" name="phone" placeholder="8..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-455 dark:text-zinc-550 uppercase tracking-widest block mb-1">Data</label>
                    <input type="date" name="visit_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Bairro / Morada</label>
                    <input type="text" name="neighborhood" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Cidade</label>
                    <input type="text" name="city" value="Maputo" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="button" onclick="toggleModal('visitorModal')" class="flex-1 px-4 py-2.5 text-[10px] font-black uppercase text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-850 rounded-xl transition-all">Cancelar</button>
                <button type="submit" class="flex-[2] bg-orange-600 hover:bg-orange-700 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase transition-all shadow-md">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div id="discipleshipModal" class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-md p-6 md:p-8 shadow-xl animate-In border border-gray-100 dark:border-zinc-800">
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="bi bi-mortarboard text-orange-600"></i> Novo Discipulado
        </h3>
        <form action="{{ route('cells.discipleships.store', $cell) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Membros (Selecione um ou mais)</label>
                <select name="user_ids[]" multiple required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 custom-select min-h-[100px]">
                    @foreach($members as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
                <p class="text-[8px] text-gray-400 dark:text-zinc-500 mt-1 font-bold uppercase tracking-widest">Segure Ctrl (ou Cmd) para selecionar vários</p>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-455 dark:text-zinc-550 uppercase tracking-widest block mb-1">Mentor</label>
                <input type="text" name="mentor_name" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Início</label>
                    <input type="date" name="start_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Lição</label>
                    <input type="text" name="current_lesson" value="1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-455 dark:text-zinc-550 uppercase tracking-widest block mb-1">Observações / Notas do Encontro</label>
                <textarea name="observations" rows="2" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all" placeholder="O que foi tratado..."></textarea>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="button" onclick="toggleModal('discipleshipModal')" class="flex-1 px-4 py-2.5 text-[10px] font-black uppercase text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-850 rounded-xl transition-all">Cancelar</button>
                <button type="submit" class="flex-[2] bg-orange-600 hover:bg-orange-700 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase transition-all shadow-md">Registrar</button>
            </div>
        </form>
    </div>
</div>

<div id="conversionModal" class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-md p-6 md:p-8 shadow-xl animate-In border border-gray-100 dark:border-zinc-800">
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="bi bi-stars text-orange-600"></i> Decisão de Fé
        </h3>
        <form action="{{ route('cells.conversions.store', $cell) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Nome</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
            </div>
            <div class="space-y-3 p-4 bg-gray-50 dark:bg-zinc-850 border border-gray-100 dark:border-zinc-800 rounded-xl">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_new_salvation" value="1" class="w-4 h-4 text-orange-600 border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded focus:ring-orange-500">
                    <span class="text-[10px] font-black uppercase text-gray-700 dark:text-zinc-300">Nova Salvação</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_water_baptism_candidate" value="1" class="w-4 h-4 text-orange-600 border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 rounded focus:ring-orange-500">
                    <span class="text-[10px] font-black uppercase text-gray-700 dark:text-zinc-300">Baptismo Água</span>
                </label>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="button" onclick="toggleModal('conversionModal')" class="flex-1 px-4 py-2.5 text-[10px] font-black uppercase text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-850 rounded-xl transition-all">Cancelar</button>
                <button type="submit" class="flex-[2] bg-orange-600 hover:bg-orange-700 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase transition-all shadow-md">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div id="editDiscipleshipModal" class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-md p-6 md:p-8 shadow-xl animate-In border border-gray-100 dark:border-zinc-800">
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="bi bi-pencil-square text-orange-600"></i> Editar Discipulado
        </h3>
        <form id="editDiscipleshipForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" id="edit_user_id">
            <div>
                <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Membro</label>
                <input type="text" id="edit_user_name" readonly class="w-full px-4 py-2.5 bg-gray-100 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-gray-500 dark:text-zinc-500 cursor-not-allowed">
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-455 dark:text-zinc-550 uppercase tracking-widest block mb-1">Mentor</label>
                <input type="text" name="mentor_name" id="edit_mentor_name" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Início</label>
                    <input type="date" name="start_date" id="edit_start_date" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Lição</label>
                    <input type="text" name="current_lesson" id="edit_current_lesson" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-455 dark:text-zinc-550 uppercase tracking-widest block mb-1">Observações / Notas do Encontro</label>
                <textarea name="observations" id="edit_observations" rows="2" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"></textarea>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="button" onclick="toggleModal('editDiscipleshipModal')" class="flex-1 px-4 py-2.5 text-[10px] font-black uppercase text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-850 rounded-xl transition-all">Cancelar</button>
                <button type="submit" class="flex-[2] bg-orange-600 hover:bg-orange-700 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase transition-all shadow-md">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<div id="visitorDetailsModal" class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl w-full max-w-md p-6 md:p-8 shadow-xl animate-In border border-gray-100 dark:border-zinc-800">
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="bi bi-person-vcard text-orange-600"></i> Detalhes do Visitante
        </h3>
        <form id="visitorFeedbackForm" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Nome</label>
                    <p id="visitor_name" class="text-sm font-bold text-gray-800 dark:text-white"></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Telefone</label>
                    <p id="visitor_phone" class="text-sm font-bold text-gray-800 dark:text-white"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Data Visita</label>
                    <p id="visitor_date" class="text-sm font-bold text-gray-800 dark:text-white"></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Idade / Gênero</label>
                    <p id="visitor_bio" class="text-sm font-bold text-gray-800 dark:text-white"></p>
                </div>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-450 dark:text-zinc-550 uppercase tracking-widest block mb-1">Endereço</label>
                <p id="visitor_address" class="text-sm font-bold text-gray-800 dark:text-white"></p>
            </div>
            
            <hr class="border-gray-100 dark:border-zinc-800 my-2">
            
            <div>
                <label class="text-[9px] font-black text-orange-600 uppercase tracking-widest block mb-1">Status do Contacto / Acompanhamento</label>
                <select name="contact_status" id="visitor_status_select" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all custom-select">
                    <option value="pendente">Pendente</option>
                    <option value="contatado">Contatado</option>
                    <option value="integrado">Integrado (Célula/Membro)</option>
                    <option value="sem_interesse">Sem Interesse</option>
                </select>
            </div>
            
            <div>
                <label class="text-[9px] font-black text-orange-600 uppercase tracking-widest block mb-1">Feedback do Líder (Notas)</label>
                <textarea name="notes" id="visitor_notes_textarea" rows="3" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-850 border border-gray-200 dark:border-zinc-800 rounded-xl text-sm font-medium text-zinc-900 dark:text-white focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all" placeholder="Escreva o feedback sobre o contacto..."></textarea>
            </div>

            <div class="flex gap-2 pt-6">
                <button type="button" onclick="toggleModal('visitorDetailsModal')" class="flex-1 px-4 py-2.5 text-[10px] font-black uppercase text-gray-500 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-850 rounded-xl transition-all">Cancelar</button>
                <button type="submit" class="flex-[2] bg-orange-600 hover:bg-orange-700 text-white px-4 py-2.5 rounded-xl text-center font-black text-[10px] uppercase transition-all shadow-md">Salvar Feedback</button>
                <a id="visitor_promote_btn" href="#" class="flex-[2] bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl text-center font-black text-[10px] uppercase transition-all shadow-md">Tornar Membro</a>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (!modal) {
            console.error('Modal not found:', id);
            return;
        }
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Ensure child animation triggers
            const content = modal.querySelector('.animate-In');
            if (content) {
                content.style.display = 'block';
            }
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function openEditDiscipleshipModal(discipleship) {
        if (!discipleship) return;
        const form = document.getElementById('editDiscipleshipForm');
        form.action = `/admin/cells/{{ $cell->id }}/discipleships/${discipleship.id}`;
        
        document.getElementById('edit_user_id').value = discipleship.user_id;
        document.getElementById('edit_user_name').value = discipleship.user ? discipleship.user.name : '---';
        document.getElementById('edit_mentor_name').value = discipleship.mentor_name || '';
        
        // Safe date handling
        if (discipleship.start_date) {
            document.getElementById('edit_start_date').value = discipleship.start_date.split('T')[0];
        }
        
        document.getElementById('edit_current_lesson').value = discipleship.current_lesson || '';
        document.getElementById('edit_observations').value = discipleship.observations || '';
        
        toggleModal('editDiscipleshipModal');
    }

    function openVisitorDetailsModal(visitor) {
        document.getElementById('visitor_name').innerText = visitor.name;
        document.getElementById('visitor_phone').innerText = visitor.phone || 'N/A';
        document.getElementById('visitor_date').innerText = new Date(visitor.visit_date).toLocaleDateString();
        document.getElementById('visitor_bio').innerText = `${visitor.age || '?'} anos / ${visitor.gender || '?'}`;
        document.getElementById('visitor_address').innerText = `${visitor.neighborhood || '?'}, ${visitor.city || '?'}`;
        
        document.getElementById('visitor_status_select').value = visitor.contact_status || 'pendente';
        document.getElementById('visitor_notes_textarea').value = visitor.notes || '';
        
        const form = document.getElementById('visitorFeedbackForm');
        form.action = `/admin/cells/{{ $cell->id }}/visitors/${visitor.id}/feedback`;
        
        const promoteBtn = document.getElementById('visitor_promote_btn');
        if (visitor.contact_status === 'integrado') {
            promoteBtn.classList.add('hidden');
        } else {
            promoteBtn.classList.remove('hidden');
            promoteBtn.href = `{{ route('members.create') }}?visitor_id=${visitor.id}`;
        }
        
        toggleModal('visitorDetailsModal');
    }
</script>

@endsection
