@extends('layouts.app')

@section('title', 'Ficha Guia de Participação - ' . $cell->name)
@section('page-title', 'Ficha Guia')

@section('content')
<div class="w-full px-4 md:px-10 space-y-8 animate-In" x-data="{ saving: false }">
    <!-- Header Card - Keeping it premium but professional -->
    <div class="bg-gray-900 rounded-[2rem] p-8 md:p-10 shadow-2xl border border-gray-800 relative overflow-hidden text-white">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('cells.show', $cell) }}" 
                        class="w-10 h-10 rounded-xl bg-white/5 text-gray-400 hover:text-white hover:bg-white/10 flex items-center justify-center transition-all border border-white/5">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-[10px] font-black text-orange-500 uppercase tracking-[0.2em]">
                            <i class="bi bi-calendar-check-fill"></i>
                            <span>Ficha Guia de Participação</span>
                        </div>
                        <h1 class="text-3xl font-black tracking-tighter uppercase">{{ $cell->name }}</h1>
                    </div>
                </div>
                
                <!-- Leadership Info - Integrated as requested -->
                <div class="flex flex-wrap gap-3 pt-1">
                    @if($cell->leader)
                        <div class="flex items-center gap-2 bg-blue-500/10 px-4 py-2 rounded-xl border border-blue-500/10">
                            <i class="bi bi-person-badge text-blue-400"></i>
                            <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Líder: <span class="text-white">{{ $cell->leader->name }}</span></span>
                        </div>
                    @endif
                    @foreach($cell->timoteos as $timoteo)
                        <div class="flex items-center gap-2 bg-orange-500/10 px-4 py-2 rounded-xl border border-orange-500/10">
                            <i class="bi bi-person-hearts text-orange-400"></i>
                            <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Auxiliar: <span class="text-white">{{ $timoteo->name }}</span></span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white/5 p-4 rounded-2xl border border-white/5 backdrop-blur-md">
                <form action="{{ route('cells.attendance', $cell) }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
                    <div class="grid grid-cols-2 gap-2 w-full sm:w-auto">
                        <select name="month" class="bg-gray-800 border-transparent text-white rounded-lg text-xs font-black uppercase tracking-widest focus:ring-orange-500 custom-select px-4 py-2.5">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="bg-gray-800 border-transparent text-white rounded-lg text-xs font-black uppercase tracking-widest focus:ring-orange-500 custom-select px-4 py-2.5">
                            @foreach(range(now()->year - 1, now()->year + 1) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full sm:w-auto bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg transition-all font-black text-xs uppercase tracking-widest">
                        Filtrar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Attendance Table Section - Reverting to "Physical Sheet/List" style -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-200 p-6 flex justify-between items-center">
            <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest flex items-center gap-2">
                <i class="bi bi-table"></i> Controle de Presença - {{ Carbon\Carbon::create()->month($month)->translatedFormat('F') }} / {{ $year }}
            </h3>
            <div class="hidden md:flex gap-4 text-[9px] font-black uppercase tracking-widest text-gray-400">
                <span class="flex items-center gap-1"><div class="w-2 h-2 rounded bg-orange-500"></div> Célula</span>
                <span class="flex items-center gap-1"><div class="w-2 h-2 rounded bg-blue-500"></div> Culto</span>
                <span class="flex items-center gap-1"><div class="w-2 h-2 rounded bg-purple-500"></div> 4ª Feira</span>
            </div>
        </div>

        <form action="{{ route('cells.attendance.store', $cell) }}" method="POST" @submit="saving = true">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-[11px]">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 font-black uppercase border-b border-gray-200">
                            <th class="p-3 text-left border-r border-gray-200 sticky left-0 bg-gray-100 z-10" rowspan="2">Nome do Membro</th>
                            <th class="p-2 text-center border-r border-gray-200 bg-orange-50 text-orange-800" colspan="{{ count($saturdays) }}">Sábados (Célula)</th>
                            <th class="p-2 text-center border-r border-gray-200 bg-blue-50 text-blue-800" colspan="{{ count($sundays) }}">Domingos (Culto)</th>
                            <th class="p-2 text-center border-r border-gray-200 bg-purple-50 text-purple-800" colspan="{{ count($wednesdays) }}">4ª Feira (Doutrina)</th>
                            <th class="p-3 text-left" rowspan="2">Observações</th>
                        </tr>
                        <tr class="bg-white text-gray-400 font-bold border-b border-gray-200">
                            @foreach($saturdays as $sat) <th class="p-1 border-r border-gray-200 text-center">{{ $sat->format('d/m') }}</th> @endforeach
                            @foreach($sundays as $sun) <th class="p-1 border-r border-gray-200 text-center">{{ $sun->format('d/m') }}</th> @endforeach
                            @foreach($wednesdays as $wed) <th class="p-1 border-r border-gray-200 text-center">{{ $wed->format('d/m') }}</th> @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($members as $member)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="p-3 border-r border-gray-200 sticky left-0 bg-white group-hover:bg-blue-50 z-10 font-bold text-gray-900 border-l border-gray-100 uppercase tracking-tight">
                                {{ $member->name }}
                            </td>
                            
                            {{-- Célula (Saturdays) --}}
                            @foreach($saturdays as $sat)
                            <td class="p-0 text-center border-r border-gray-200 bg-orange-50/5">
                                <label class="flex items-center justify-center p-2 cursor-pointer h-full w-full">
                                    <input type="checkbox" name="attendance[{{ $member->id }}][cell][{{ $sat->format('Y-m-d') }}]" value="1"
                                        class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                        {{ isset($attendances[$member->id]['cell'][$sat->format('Y-m-d')]) && $attendances[$member->id]['cell'][$sat->format('Y-m-d')]->first()->status ? 'checked' : '' }}>
                                </label>
                            </td>
                            @endforeach

                            {{-- Culto (Sundays) --}}
                            @foreach($sundays as $sun)
                            <td class="p-0 text-center border-r border-gray-200 bg-blue-50/5">
                                <label class="flex items-center justify-center p-2 cursor-pointer h-full w-full">
                                    <input type="checkbox" name="attendance[{{ $member->id }}][service][{{ $sun->format('Y-m-d') }}]" value="1"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        {{ isset($attendances[$member->id]['service'][$sun->format('Y-m-d')]) && $attendances[$member->id]['service'][$sun->format('Y-m-d')]->first()->status ? 'checked' : '' }}>
                                </label>
                            </td>
                            @endforeach

                            {{-- 4ª Feira --}}
                            @foreach($wednesdays as $wed)
                            <td class="p-0 text-center border-r border-gray-200 bg-purple-50/5">
                                <label class="flex items-center justify-center p-2 cursor-pointer h-full w-full">
                                    <input type="checkbox" name="attendance[{{ $member->id }}][wednesday][{{ $wed->format('Y-m-d') }}]" value="1"
                                        class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                        {{ isset($attendances[$member->id]['wednesday'][$wed->format('Y-m-d')]) && $attendances[$member->id]['wednesday'][$wed->format('Y-m-d')]->first()->status ? 'checked' : '' }}>
                                </label>
                            </td>
                            @endforeach

                            <td class="p-2 border-r border-gray-100">
                                <input type="text" name="reason[{{ $member->id }}]" placeholder="..." 
                                    class="w-full bg-transparent border-none text-[10px] text-gray-400 focus:ring-0 p-1">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="submit" 
                    class="bg-gray-900 text-white px-10 py-3 rounded-xl hover:bg-black transition-all font-black text-xs uppercase tracking-widest shadow-xl flex items-center gap-3"
                    :disabled="saving">
                    <span x-show="!saving" class="flex items-center gap-2">
                        <i class="bi bi-save2-fill"></i> Salvar Registro de Frequência
                    </span>
                    <span x-show="saving">Processando...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Supplementary Sections - Modern but functional -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Resultados Rápidos (Visitas/Conversões) --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <h3 class="text-xs font-black text-gray-900 uppercase tracking-[0.15em] flex items-center gap-2">
                    <i class="bi bi-graph-up text-orange-500"></i> Relatório de Decisões
                </h3>
                <div class="flex gap-2">
                    <button onclick="toggleModal('visitorModal')" class="bg-orange-600 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase hover:bg-orange-700 transition-all">+ Visita</button>
                    <button onclick="toggleModal('conversionModal')" class="bg-purple-600 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase hover:bg-purple-700 transition-all">+ Decisão</button>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                    <p class="text-2xl font-black text-orange-600">{{ $visitors->count() }}</p>
                    <p class="text-[8px] font-black text-gray-400 uppercase">Visitas</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                    <p class="text-2xl font-black text-purple-600">{{ $conversions->where('is_new_salvation', true)->count() }}</p>
                    <p class="text-[8px] font-black text-gray-400 uppercase">Salvações</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                    <p class="text-2xl font-black text-blue-600">{{ $conversions->where('is_water_baptism_candidate', true)->count() }}</p>
                    <p class="text-[8px] font-black text-gray-400 uppercase">Batismos</p>
                </div>
            </div>

            <div class="space-y-2 mt-4 max-h-[200px] overflow-y-auto custom-scrollbar">
                @forelse($visitors as $visitor)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 group/visitor">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-sm">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[9px] font-black text-gray-900 uppercase truncate">{{ $visitor->name }}</p>
                            <span class="text-[8px] font-bold text-gray-400">{{ $visitor->visit_date->format('d/m') }} • 
                                @if($visitor->isIntegrated())
                                    <span class="text-green-600">INTEGRADO</span>
                                @else
                                    {{ strtoupper($visitor->contact_status) }}
                                @endif
                            </span>
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover/visitor:opacity-100 transition-all">
                            <button onclick="openVisitorDetailsModal({{ json_encode($visitor) }})" class="p-1 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Ver Detalhes">
                                <i class="bi bi-eye"></i>
                            </button>
                            @if(!$visitor->isIntegrated())
                                <a href="{{ route('members.create', ['visitor_id' => $visitor->id]) }}" class="p-1 text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Tornar Membro">
                                    <i class="bi bi-person-plus"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center py-4 text-gray-300 text-[9px] italic">Nenhuma visita registada</p>
                @endforelse
            </div>
        </div>

        {{-- Discipulado e Mentoria --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <h3 class="text-xs font-black text-gray-900 uppercase tracking-[0.15em] flex items-center gap-2">
                    <i class="bi bi-mortarboard text-blue-500"></i> Acompanhamento
                </h3>
                <button onclick="toggleModal('discipleshipModal')" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase hover:bg-blue-700 transition-all">+ Novo</button>
            </div>

            <div class="space-y-3">
                @forelse($discipleships as $discipleship)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 group/item">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-sm">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-black text-gray-900 uppercase truncate">{{ $discipleship->user->name }}</p>
                            <span class="text-[8px] font-bold text-gray-400">LIÇÃO {{ $discipleship->current_lesson ?? '1' }} • {{ $discipleship->mentor_name ?? '---' }}</span>
                            @if($discipleship->observations)
                                <p class="text-[8px] text-gray-500 italic mt-0.5 truncate">{{ $discipleship->observations }}</p>
                            @endif
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover/item:opacity-100 transition-all">
                            <button onclick="openEditDiscipleshipModal({{ json_encode($discipleship) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('cells.discipleships.destroy', [$cell, $discipleship]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este registro?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-6 text-gray-300 text-[10px] italic">Sem discipulados ativos</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div id="visitorModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl animate-In">
        <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
            <i class="bi bi-person-plus text-orange-600"></i> Registar Visita
        </h3>
        <form action="{{ route('cells.visitors.store', $cell) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Nome Completo</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Telefone</label>
                    <input type="text" name="phone" placeholder="8..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Data</label>
                    <input type="date" name="visit_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Bairro / Morada</label>
                    <input type="text" name="neighborhood" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Cidade</label>
                    <input type="text" name="city" value="Maputo" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all">
                </div>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="button" onclick="toggleModal('visitorModal')" class="flex-1 px-4 py-2.5 text-[10px] font-black uppercase text-gray-500 hover:bg-gray-100 rounded-xl transition-all">Cancelar</button>
                <button type="submit" class="flex-[2] bg-orange-600 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase hover:bg-orange-700 transition-all shadow-lg shadow-orange-500/20">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div id="discipleshipModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl animate-In">
        <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
            <i class="bi bi-mortarboard text-blue-600"></i> Novo Discipulado
        </h3>
        <form action="{{ route('cells.discipleships.store', $cell) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Membros (Selecione um ou mais)</label>
                <select name="user_ids[]" multiple required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 custom-select min-h-[100px]">
                    @foreach($members as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
                <p class="text-[8px] text-gray-400 mt-1 font-bold uppercase tracking-widest">Segure Ctrl (ou Cmd) para selecionar vários</p>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Mentor</label>
                <input type="text" name="mentor_name" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Início</label>
                    <input type="date" name="start_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Lição</label>
                    <input type="text" name="current_lesson" value="1" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Observações / Notas do Encontro</label>
                <textarea name="observations" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="O que foi tratado..."></textarea>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="button" onclick="toggleModal('discipleshipModal')" class="flex-1 px-4 py-2.5 text-[10px] font-black uppercase text-gray-500 hover:bg-gray-100 rounded-xl transition-all">Cancelar</button>
                <button type="submit" class="flex-[2] bg-blue-600 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Registrar</button>
            </div>
        </form>
    </div>
</div>

<div id="conversionModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl animate-In">
        <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
            <i class="bi bi-stars text-purple-600"></i> Decisão de Fé
        </h3>
        <form action="{{ route('cells.conversions.store', $cell) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Nome</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
            </div>
            <div class="space-y-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_new_salvation" value="1" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <span class="text-[10px] font-black uppercase text-gray-700">Nova Salvação</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_water_baptism_candidate" value="1" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <span class="text-[10px] font-black uppercase text-gray-700">Baptismo Água</span>
                </label>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="button" onclick="toggleModal('conversionModal')" class="flex-1 px-4 py-2.5 text-[10px] font-black uppercase text-gray-500 hover:bg-gray-100 rounded-xl transition-all">Cancelar</button>
                <button type="submit" class="flex-[2] bg-purple-600 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase hover:bg-purple-700 transition-all shadow-lg shadow-purple-500/20">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div id="editDiscipleshipModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl animate-In">
        <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
            <i class="bi bi-pencil-square text-blue-600"></i> Editar Discipulado
        </h3>
        <form id="editDiscipleshipForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" id="edit_user_id">
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Membro</label>
                <input type="text" id="edit_user_name" readonly class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 cursor-not-allowed">
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Mentor</label>
                <input type="text" name="mentor_name" id="edit_mentor_name" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Início</label>
                    <input type="date" name="start_date" id="edit_start_date" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Lição</label>
                    <input type="text" name="current_lesson" id="edit_current_lesson" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Observações / Notas do Encontro</label>
                <textarea name="observations" id="edit_observations" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"></textarea>
            </div>
            <div class="flex gap-2 pt-4">
                <button type="button" onclick="toggleModal('editDiscipleshipModal')" class="flex-1 px-4 py-2.5 text-[10px] font-black uppercase text-gray-500 hover:bg-gray-100 rounded-xl transition-all">Cancelar</button>
                <button type="submit" class="flex-[2] bg-blue-600 text-white px-4 py-2.5 rounded-xl font-black text-[10px] uppercase hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<div id="visitorDetailsModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl animate-In">
        <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
            <i class="bi bi-person-vcard text-green-600"></i> Detalhes do Visitante
        </h3>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Nome</label>
                    <p id="visitor_name" class="text-sm font-bold text-gray-800"></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Telefone</label>
                    <p id="visitor_phone" class="text-sm font-bold text-gray-800"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Data Visita</label>
                    <p id="visitor_date" class="text-sm font-bold text-gray-800"></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Idade / Gênero</label>
                    <p id="visitor_bio" class="text-sm font-bold text-gray-800"></p>
                </div>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Endereço</label>
                <p id="visitor_address" class="text-sm font-bold text-gray-800"></p>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Observações</label>
                <p id="visitor_notes" class="text-sm font-medium text-gray-600 bg-gray-50 p-3 rounded-xl border border-gray-100 min-h-[60px]"></p>
            </div>
        </div>
        <div class="flex gap-2 pt-6">
            <button type="button" onclick="toggleModal('visitorDetailsModal')" class="flex-1 px-4 py-2.5 text-[10px] font-black uppercase text-gray-500 hover:bg-gray-100 rounded-xl transition-all">Fechar</button>
            <a id="visitor_promote_btn" href="#" class="flex-[2] bg-green-600 text-white px-4 py-2.5 rounded-xl text-center font-black text-[10px] uppercase hover:bg-green-700 transition-all shadow-lg shadow-green-500/20">Tornar Membro</a>
        </div>
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
        document.getElementById('visitor_notes').innerText = visitor.notes || 'Sem observações';
        
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
