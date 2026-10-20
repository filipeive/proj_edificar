@extends('layouts.app')

@section('title', 'Nova Célula - Portal Life Church')
@section('page-title', 'Nova Célula')

@section('content')
    <div class="space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Comunhão & Estrutura</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Nova Célula</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Registe uma nova unidade celular e atribua a liderança</p>
            </div>
            <div>
                <a href="{{ route('cells.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </header>

        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <form action="{{ route('cells.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="supervision_id" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Supervisão</label>
                        <select name="supervision_id" id="supervision_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 searchable-select custom-select @error('supervision_id') border-red-500 @enderror"
                            required>
                            <option value="">-- Selecione uma supervisão --</option>
                            @foreach($supervisions as $supervision)
                                <option value="{{ $supervision->id }}" @selected(old('supervision_id') == $supervision->id)>
                                    {{ $supervision->zone->name }} - {{ $supervision->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supervision_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Nome da Célula</label>
                        <input type="text" name="name" id="name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('name') border-red-500 @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Tipo de Célula</label>
                        <select name="type" id="type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 searchable-select custom-select @error('type') border-red-500 @enderror"
                            required>
                            <option value="membros" {{ old('type', 'membros') == 'membros' ? 'selected' : '' }}>Célula de Membros</option>
                            <option value="lideres" {{ old('type') == 'lideres' ? 'selected' : '' }}>Célula de Líderes</option>
                            <option value="supervisores" {{ old('type') == 'supervisores' ? 'selected' : '' }}>Célula de Supervisores</option>
                            <option value="pastores_zona" {{ old('type') == 'pastores_zona' ? 'selected' : '' }}>Célula de Pastores de Zona</option>
                            <option value="pastores" {{ old('type') == 'pastores' ? 'selected' : '' }}>Célula de Pastores</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="leader_id" class="mb-1 block text-[10px] font-black uppercase tracking-wider text-gray-500">Líder da Célula</label>
                        <select name="leader_id" id="leader_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 searchable-select custom-select @error('leader_id') border-red-500 @enderror"
                            required>
                            <option value="">-- Selecione um líder --</option>
                            @foreach($leaders as $leader)
                                <option value="{{ $leader->id }}" @selected(old('leader_id') == $leader->id)>
                                    {{ $leader->name }} ({{ $leader->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('leader_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <x-location-map id="cell-location-map" />
                </div>

                <div class="flex gap-2.5 pt-2">
                    <button type="submit" class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-5 py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm transition">
                        <i class="bi bi-check-lg"></i> Criar Célula
                    </button>
                    <a href="{{ route('cells.index') }}"
                        class="inline-flex justify-center items-center rounded-lg border border-gray-300 bg-white px-5 py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const supervisionSelect = document.getElementById('supervision_id');
        const leaderSelect = document.getElementById('leader_id');

        if (!typeSelect || !supervisionSelect || !leaderSelect) return;

        const loadEligibleLeaders = async function() {
            const params = new URLSearchParams({ cell_type: typeSelect.value });
            if (supervisionSelect.value) {
                params.set('supervision_id', supervisionSelect.value);
            }

            try {
                const response = await fetch(`{{ route('cells.eligible-leaders') }}?${params.toString()}`);
                const leaders = await response.json();

                const tomSelect = leaderSelect.tomselect;
                const previousValue = tomSelect ? tomSelect.getValue() : leaderSelect.value;

                if (tomSelect) {
                    tomSelect.clear();
                    tomSelect.clearOptions();
                } else {
                    leaderSelect.innerHTML = '';
                }

                if (leaders.length === 0) {
                    const fallbackText = 'Nenhum líder disponível para este tipo de célula';
                    if (tomSelect) {
                        tomSelect.addOption({value: '', text: fallbackText});
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = fallbackText;
                        leaderSelect.appendChild(option);
                    }
                }

                leaders.forEach(leader => {
                    if (tomSelect) {
                        tomSelect.addOption({value: leader.id, text: `${leader.name} (${leader.email})`});
                    } else {
                        const option = document.createElement('option');
                        option.value = leader.id;
                        option.textContent = `${leader.name} (${leader.email})`;
                        leaderSelect.appendChild(option);
                    }
                });

                if (previousValue && leaders.some(leader => leader.id == previousValue)) {
                    if (tomSelect) {
                        tomSelect.setValue(previousValue);
                    } else {
                        leaderSelect.value = previousValue;
                    }
                }

                if (tomSelect) tomSelect.refreshOptions(false);
            } catch (error) {
                console.error('Erro ao carregar líderes elegíveis:', error);
            }
        };

        typeSelect.addEventListener('change', loadEligibleLeaders);
        supervisionSelect.addEventListener('change', loadEligibleLeaders);

        // Pré-carregar os líderes elegíveis conforme o tipo/supervisão selecionados
        loadEligibleLeaders();
    });
</script>
@endpush