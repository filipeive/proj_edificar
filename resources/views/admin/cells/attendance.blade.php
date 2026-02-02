@extends('layouts.app')

@section('title', 'Ficha Guia - ' . $cell->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Header -->
        <div class="bg-black p-6 text-white">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div>
                    <a href="{{ route('cells.show', $cell) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition text-sm font-bold"><i class="bi bi-arrow-left mr-2"></i> Voltar</a>
                </div>
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tight">Ficha Guia de Participação</h1>
                    <p class="text-orange-500 font-bold text-sm uppercase tracking-widest mt-1">{{ $cell->name }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <form action="{{ route('cells.attendance', $cell) }}" method="GET" class="flex items-center space-x-2">
                        <select name="month" class="bg-zinc-900 border-zinc-800 text-white rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500 custom-select">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="bg-zinc-900 border-zinc-800 text-white rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500 custom-select">
                            @foreach(range(now()->year - 1, now()->year + 1) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition text-sm font-bold">
                            Filtrar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <form action="{{ route('cells.attendance.store', $cell) }}" method="POST">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-[10px] font-black uppercase tracking-widest border-b border-gray-200">
                                <th class="p-4 text-left border-r border-gray-200" rowspan="2">Nº</th>
                                <th class="p-4 text-left border-r border-gray-200" rowspan="2">Nome do Membro</th>
                                <th class="p-2 text-center border-r border-gray-200" colspan="{{ count($saturdays) }}">Célula (Sábados)</th>
                                <th class="p-2 text-center border-r border-gray-200" colspan="{{ count($sundays) }}">Culto (Domingos)</th>
                                <th class="p-2 text-center border-r border-gray-200" colspan="{{ count($wednesdays) }}">4ª Feira</th>
                                <th class="p-4 text-left" rowspan="2">Observações</th>
                            </tr>
                            <tr class="bg-gray-50 text-gray-500 text-[9px] font-bold uppercase border-b border-gray-200">
                                {{-- Saturdays --}}
                                @foreach($saturdays as $sat)
                                <th class="p-2 border-r border-gray-100">{{ $sat->format('d/m') }}</th>
                                @endforeach

                                {{-- Sundays --}}
                                @foreach($sundays as $sun)
                                <th class="p-2 border-r border-gray-100">{{ $sun->format('d/m') }}</th>
                                @endforeach

                                {{-- Wednesdays --}}
                                @foreach($wednesdays as $wed)
                                <th class="p-2 border-r border-gray-100">{{ $wed->format('d/m') }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($members as $index => $member)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 text-gray-400 font-mono text-xs border-r border-gray-100">{{ $index + 1 }}</td>
                                <td class="p-4 border-r border-gray-100">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs mr-3">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-bold text-gray-800">{{ $member->name }}</span>
                                    </div>
                                </td>
                                
                                {{-- Célula (Saturdays) --}}
                                @foreach($saturdays as $sat)
                                <td class="p-2 text-center border-r border-gray-100">
                                    <input type="checkbox" name="attendance[{{ $member->id }}][cell][{{ $sat->format('Y-m-d') }}]" value="1"
                                        class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                        {{ isset($attendances[$member->id]['cell'][$sat->format('Y-m-d')]) && $attendances[$member->id]['cell'][$sat->format('Y-m-d')]->first()->status ? 'checked' : '' }}>
                                </td>
                                @endforeach

                                {{-- Culto (Sundays) --}}
                                @foreach($sundays as $sun)
                                <td class="p-2 text-center border-r border-gray-100">
                                    <input type="checkbox" name="attendance[{{ $member->id }}][service][{{ $sun->format('Y-m-d') }}]" value="1"
                                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        {{ isset($attendances[$member->id]['service'][$sun->format('Y-m-d')]) && $attendances[$member->id]['service'][$sun->format('Y-m-d')]->first()->status ? 'checked' : '' }}>
                                </td>
                                @endforeach

                                {{-- 4ª Feira --}}
                                @foreach($wednesdays as $wed)
                                <td class="p-2 text-center border-r border-gray-100">
                                    <input type="checkbox" name="attendance[{{ $member->id }}][wednesday][{{ $wed->format('Y-m-d') }}]" value="1"
                                        class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                        {{ isset($attendances[$member->id]['wednesday'][$wed->format('Y-m-d')]) && $attendances[$member->id]['wednesday'][$wed->format('Y-m-d')]->first()->status ? 'checked' : '' }}>
                                </td>
                                @endforeach

                                <td class="p-4">
                                    <input type="text" name="reason[{{ $member->id }}]" placeholder="..." 
                                        class="w-full bg-transparent border-none text-xs text-gray-500 focus:ring-0 p-0">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-black hover:bg-zinc-800 text-white px-8 py-3 rounded-2xl transition-all duration-300 font-black text-xs uppercase tracking-widest shadow-xl hover:shadow-black/20 flex items-center">
                        <i class="bi bi-check2-circle mr-2 text-lg"></i>
                        Guardar Presenças
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Extra Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        {{-- Visitas --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-orange-50 p-4 border-b border-orange-100 flex justify-between items-center">
                <h3 class="text-sm font-black uppercase tracking-widest text-orange-800 flex items-center">
                    <i class="bi bi-person-plus-fill mr-2"></i> Visitas do Mês
                </h3>
                <button type="button" onclick="toggleModal('visitorModal')" class="text-xs bg-orange-600 text-white px-3 py-1 rounded-lg font-bold hover:bg-orange-700 transition">
                    + Adicionar
                </button>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 uppercase font-black border-b border-gray-100">
                                <th class="pb-2">Data</th>
                                <th class="pb-2">Nome</th>
                                <th class="pb-2">Contacto</th>
                                <th class="pb-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($visitors as $visitor)
                            <tr>
                                <td class="py-3 text-gray-500">{{ $visitor->visit_date->format('d/m') }}</td>
                                <td class="py-3 font-bold text-gray-800">{{ $visitor->name }}</td>
                                <td class="py-3 text-gray-500">{{ $visitor->phone ?? '---' }}</td>
                                <td class="py-3">
                                    @if($visitor->became_participant)
                                        <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-bold">Participante</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full text-[10px] font-bold">Visitante</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-400 italic">Nenhuma visita registada este mês.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Discipulados --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-blue-50 p-4 border-b border-blue-100 flex justify-between items-center">
                <h3 class="text-sm font-black uppercase tracking-widest text-blue-800 flex items-center">
                    <i class="bi bi-mortarboard-fill mr-2"></i> Discipulados em Curso
                </h3>
                <button type="button" onclick="toggleModal('discipleshipModal')" class="text-xs bg-blue-600 text-white px-3 py-1 rounded-lg font-bold hover:bg-blue-700 transition">
                    + Adicionar
                </button>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 uppercase font-black border-b border-gray-100">
                                <th class="pb-2">Membro</th>
                                <th class="pb-2">Mentor</th>
                                <th class="pb-2">Lição Atual</th>
                                <th class="pb-2">Início</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($discipleships as $discipleship)
                            <tr>
                                <td class="py-3 font-bold text-gray-800">{{ $discipleship->user->name }}</td>
                                <td class="py-3 text-gray-600">{{ $discipleship->mentor_name ?? '---' }}</td>
                                <td class="py-3"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-lg font-bold">{{ $discipleship->current_lesson ?? '1' }}</span></td>
                                <td class="py-3 text-gray-500">{{ $discipleship->start_date->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-400 italic">Nenhum discipulado em curso.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Conversões e Baptismos --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden lg:col-span-2">
            <div class="bg-purple-50 p-4 border-b border-purple-100 flex justify-between items-center">
                <h3 class="text-sm font-black uppercase tracking-widest text-purple-800 flex items-center">
                    <i class="bi bi-stars mr-2"></i> Salvações e Candidatos a Baptismo
                </h3>
                <button type="button" onclick="toggleModal('conversionModal')" class="text-xs bg-purple-600 text-white px-3 py-1 rounded-lg font-bold hover:bg-purple-700 transition">
                    + Adicionar
                </button>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach(['is_new_salvation' => 'Novas Salvações', 'is_water_baptism_candidate' => 'Baptismo de Água', 'is_holy_spirit_baptism_candidate' => 'Baptismo Espírito Santo'] as $key => $label)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="text-[10px] font-black uppercase text-gray-400 mb-3 border-b border-gray-200 pb-2">{{ $label }}</h4>
                        <ul class="space-y-2">
                            @forelse($conversions->where($key, true) as $conv)
                            <li class="flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-700">{{ $conv->name }}</span>
                                <span class="text-[9px] text-gray-400">{{ $conv->date->format('d/m') }}</span>
                            </li>
                            @empty
                            <li class="text-xs text-gray-400 italic">Nenhum registo.</li>
                            @endforelse
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div id="visitorModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-black mb-4">Registar Visita</h3>
        <form action="{{ route('cells.visitors.store', $cell) }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-1">Nome Completo</label>
                <input type="text" name="name" required class="w-full border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black uppercase text-gray-400 mb-1">Telefone</label>
                    <input type="text" name="phone" class="w-full border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-gray-400 mb-1">Data</label>
                    <input type="date" name="visit_date" value="{{ now()->format('Y-m-d') }}" required class="w-full border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="toggleModal('visitorModal')" class="px-4 py-2 text-sm font-bold text-gray-500">Cancelar</button>
                <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-orange-700 transition">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div id="discipleshipModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-black mb-4">Novo Discipulado</h3>
        <form action="{{ route('cells.discipleships.store', $cell) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-1">Membro</label>
                <select name="user_id" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 custom-select">
                    @foreach($members as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-1">Mentor</label>
                <input type="text" name="mentor_name" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black uppercase text-gray-400 mb-1">Data de Início</label>
                    <input type="date" name="start_date" value="{{ now()->format('Y-m-d') }}" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase text-gray-400 mb-1">Lição Atual</label>
                    <input type="text" name="current_lesson" value="1" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="toggleModal('discipleshipModal')" class="px-4 py-2 text-sm font-bold text-gray-500">Cancelar</button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-700 transition">Salvar</button>
            </div>
        </form>
    </div>
</div>

<div id="conversionModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-black mb-4">Salvação / Baptismo</h3>
        <form action="{{ route('cells.conversions.store', $cell) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-1">Nome</label>
                <input type="text" name="name" required class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-1">Data</label>
                <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div class="space-y-2">
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="is_new_salvation" value="1" class="w-5 h-5 text-purple-600 border-gray-300 rounded">
                    <span class="text-sm font-bold text-gray-700">Nova Salvação</span>
                </label>
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="is_water_baptism_candidate" value="1" class="w-5 h-5 text-purple-600 border-gray-300 rounded">
                    <span class="text-sm font-bold text-gray-700">Candidato Baptismo de Água</span>
                </label>
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="is_holy_spirit_baptism_candidate" value="1" class="w-5 h-5 text-purple-600 border-gray-300 rounded">
                    <span class="text-sm font-bold text-gray-700">Candidato Baptismo Espírito Santo</span>
                </label>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="toggleModal('conversionModal')" class="px-4 py-2 text-sm font-bold text-gray-500">Cancelar</button>
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-purple-700 transition">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.classList.toggle('hidden');
    }
</script>

@endsection
