@extends('layouts.app')

@section('title', "Editar Célula {$cell->name} - Portal Life Church")
@section('page-title', 'Editar Célula')

@section('content')
    <div class="w-full">
        <!-- Header Card -->
        <div class="bg-white rounded-[2.5rem] p-8 md:p-12 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-[10px] font-black text-blue-600 uppercase tracking-[0.2em]">
                        <i class="bi bi-pencil-square"></i>
                        <span>Configurações da Unidade</span>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase">{{ $cell->name }}</h1>
                    <p class="text-gray-500 font-medium">Ajuste as informações principais e a equipe de liderança</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('cells.show', $cell) }}"
                        class="px-6 py-3 rounded-2xl bg-gray-50 text-gray-500 font-black text-[10px] uppercase tracking-widest hover:bg-gray-100 transition-all border border-gray-100">
                        <i class="bi bi-speedometer2 mr-2"></i> Dashboard
                    </a>
                </div>
            </div>
            <!-- Decorative background element -->
            <div class="absolute -right-12 -bottom-12 text-[12rem] text-gray-50 opacity-50 rotate-12">
                <i class="bi bi-layers"></i>
            </div>
        </div>

        <form action="{{ route('cells.update', $cell) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Main Info Section -->
                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-sm border border-gray-100 space-y-8">
                    <div class="flex items-center gap-3 border-b border-gray-50 pb-6">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Informações Básicas</h3>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label for="name"
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Nome da
                                Célula</label>
                            <input type="text" name="name" id="name"
                                class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all @error('name') border-red-500 @enderror"
                                value="{{ old('name', $cell->name) }}" required>
                            @error('name')
                                <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="supervision_id"
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Supervisão
                                Pertencente</label>
                            <select name="supervision_id" id="supervision_id"
                                class="searchable-select custom-select w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl text-sm font-bold transition-all @error('supervision_id') border-red-500 @enderror"
                                required>
                                @foreach($supervisions as $supervision)
                                    <option value="{{ $supervision->id }}" @selected(old('supervision_id', $cell->supervision_id) == $supervision->id)>
                                        {{ $supervision->zone->name }} - {{ $supervision->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supervision_id')
                                <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Leadership Team Section -->
                <div class="bg-gray-900 rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-gray-800 space-y-8 text-white">
                    <div class="flex items-center gap-3 border-b border-white/5 pb-6">
                        <div
                            class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-500 flex items-center justify-center text-lg shadow-lg shadow-orange-500/10">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <h3 class="text-xl font-black text-white tracking-tight">Equipe de Liderança</h3>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label for="leader_id"
                                class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Líder
                                Principal</label>
                            <select name="leader_id" id="leader_id"
                                class="searchable-select custom-select w-full px-6 py-4 bg-white/5 border-transparent focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 rounded-2xl text-sm font-bold transition-all text-white @error('leader_id') border-red-500 @enderror"
                                required>
                                @foreach($leaders as $leader)
                                    <option value="{{ $leader->id }}" @selected(old('leader_id', $cell->leader_id) == $leader->id)>
                                        {{ $leader->name }} ({{ $leader->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('leader_id')
                                <p class="text-red-400 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="timoteos"
                                class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">Auxiliares
                                (Timóteos)</label>
                            @php $assignedTimoteoIds = $cell->timoteos->pluck('id')->toArray(); @endphp
                            <select name="timoteos[]" id="timoteos"
                                class="searchable-select custom-select w-full px-6 py-4 bg-white/5 border-transparent focus:bg-white/10 focus:ring-4 focus:ring-orange-500/20 rounded-2xl text-sm font-bold transition-all text-white"
                                multiple>
                                @foreach($timoteos as $timoteo)
                                    <option value="{{ $timoteo->id }}" @selected(in_array($timoteo->id, old('timoteos', $assignedTimoteoIds)))>
                                        {{ $timoteo->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[9px] text-gray-500 mt-2 italic px-1">
                                <i class="bi bi-info-circle mr-1"></i> Timóteos têm as mesmas permissões que o líder para
                                esta célula.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex flex-col md:flex-row gap-4 pt-4">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white px-8 py-5 rounded-[2rem] hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-500/20 flex items-center justify-center gap-2 group">
                    <span>Guardar Alterações</span>
                    <i class="bi bi-check2-circle text-lg group-hover:scale-110 transition-transform"></i>
                </button>
                <a href="{{ route('cells.index') }}"
                    class="md:w-48 bg-white text-gray-500 border border-gray-100 px-8 py-5 rounded-[2rem] hover:bg-gray-50 transition-all font-black text-xs uppercase tracking-widest text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection