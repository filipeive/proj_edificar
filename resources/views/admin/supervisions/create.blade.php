@extends('layouts.app')

@section('title', 'Criar Supervisão - Portal Life Church')
@section('page-title', 'Criar Supervisão')
@section('page-subtitle', 'Registar uma nova estrutura de supervisão no sistema')

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Estrutura Ministerial</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Criar Supervisão</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Registe uma nova estrutura de supervisão no sistema</p>
            </div>
            <div>
                <a href="{{ route('supervisions.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </header>

        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <form action="{{ route('supervisions.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="zone_id" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Zona</label>
                        <select name="zone_id" id="zone_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 searchable-select custom-select @error('zone_id') border-red-500 @enderror"
                            required>
                            <option value="">-- Selecione uma zona --</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" @selected(old('zone_id') == $zone->id)>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('zone_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ supervisionZoneId: '{{ old('zone_id') }}' }">
                        <label for="supervisor_id" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Supervisor Responsável</label>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <select name="supervisor_id" id="supervisor_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 searchable-select custom-select @error('supervisor_id') border-red-500 @enderror">
                                    <option value="">-- Selecione um supervisor --</option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}" @selected(old('supervisor_id') == $supervisor->id)>
                                            {{ $supervisor->name }} ({{ $supervisor->role }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button"
                                @click="$dispatch('open-quick-supervisor-modal', { zoneId: document.getElementById('zone_id').value })"
                                class="inline-flex items-center gap-1 rounded-lg border border-orange-200 bg-orange-50 px-2.5 py-1.5 text-xs font-bold text-orange-800 hover:bg-orange-100 transition shrink-0">
                                <i class="bi bi-person-plus"></i> Rápido
                            </button>
                        </div>
                        @error('supervisor_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sub_supervisor_id" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Sub-Supervisor (Opcional)</label>
                        <select name="sub_supervisor_id" id="sub_supervisor_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 searchable-select custom-select @error('sub_supervisor_id') border-red-500 @enderror">
                            <option value="">-- Selecione um sub-supervisor --</option>
                            @foreach($subSupervisors as $sub)
                                <option value="{{ $sub->id }}" @selected(old('sub_supervisor_id') == $sub->id)>
                                    {{ $sub->name }} ({{ $sub->role }})
                                </option>
                            @endforeach
                        </select>
                        @error('sub_supervisor_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-gray-400 mt-1">
                            <i class="bi bi-info-circle"></i> O usuário selecionado será promovido a Sub-Supervisor.
                        </p>
                    </div>

                    <div>
                        <label for="name" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Nome da Supervisão</label>
                        <input type="text" name="name" id="name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('name') border-red-500 @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Descrição</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-2.5 pt-2">
                    <button type="submit" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-5 py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm transition">
                        <i class="bi bi-check-lg"></i> Salvar Supervisão
                    </button>
                    <a href="{{ route('supervisions.index') }}"
                        class="inline-flex justify-center items-center rounded-lg border border-gray-300 bg-white px-5 py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
