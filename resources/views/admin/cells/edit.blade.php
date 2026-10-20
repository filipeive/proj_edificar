@extends('layouts.app')

@section('title', "Editar Célula {$cell->name} - Portal Life Church")
@section('page-title', 'Editar Célula')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Configurações da Unidade</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">{{ $cell->name }}</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Ajuste as informações principais e a equipe de liderança</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('cells.show', $cell) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </div>
        </header>

        <form action="{{ route('cells.update', $cell) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Main Info Section -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center text-sm">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900">Informações Básicas</h3>
                    </div>

                    <div class="space-y-3.5">
                        <div>
                            <label for="name"
                                class="mb-1 block text-[10px] font-black text-gray-500 uppercase tracking-wider">Nome da
                                Célula</label>
                            <input type="text" name="name" id="name"
                                class="w-full px-3 py-2 bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg text-xs sm:text-sm font-semibold text-gray-900 transition-all @error('name') border-red-500 @enderror"
                                value="{{ old('name', $cell->name) }}" required>
                            @error('name')
                                <p class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                            @enderror
                         </div>

                        <div>
                            <label for="type"
                                class="mb-1 block text-[10px] font-black text-gray-500 uppercase tracking-wider">Tipo de
                                Célula</label>
                            <select name="type" id="type"
                                class="searchable-select custom-select w-full px-3 py-2 bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg text-xs sm:text-sm font-semibold text-gray-900 transition-all @error('type') border-red-500 @enderror"
                                required>
                                <option value="membros" {{ old('type', $cell->type) == 'membros' ? 'selected' : '' }}>Célula de Membros</option>
                                <option value="lideres" {{ old('type', $cell->type) == 'lideres' ? 'selected' : '' }}>Célula de Líderes</option>
                                <option value="supervisores" {{ old('type', $cell->type) == 'supervisores' ? 'selected' : '' }}>Célula de Supervisores</option>
                                <option value="pastores_zona" {{ old('type', $cell->type) == 'pastores_zona' ? 'selected' : '' }}>Célula de Pastores de Zona</option>
                                <option value="pastores" {{ old('type', $cell->type) == 'pastores' ? 'selected' : '' }}>Célula de Pastores</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="supervision_id"
                                class="mb-1 block text-[10px] font-black text-gray-500 uppercase tracking-wider">Supervisão
                                Pertencente</label>
                            <select name="supervision_id" id="supervision_id"
                                class="searchable-select custom-select w-full px-3 py-2 bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg text-xs sm:text-sm font-semibold text-gray-900 transition-all @error('supervision_id') border-red-500 @enderror"
                                required>
                                @foreach($supervisions as $supervision)
                                    <option value="{{ $supervision->id }}" @selected(old('supervision_id', $cell->supervision_id) == $supervision->id)>
                                        {{ $supervision->zone->name }} - {{ $supervision->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supervision_id')
                                <p class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Leadership Team Section -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-orange-50 text-orange-700 flex items-center justify-center text-sm">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <h3 class="text-sm font-black text-gray-900">Equipe de Liderança</h3>
                    </div>

                    <div class="space-y-3.5">
                        <div>
                            <label for="leader_id"
                                class="mb-1 block text-[10px] font-black text-gray-500 uppercase tracking-wider">Líder
                                Principal</label>
                            <select name="leader_id" id="leader_id"
                                class="searchable-select custom-select w-full px-3 py-2 bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg text-xs sm:text-sm font-semibold text-gray-900 transition-all @error('leader_id') border-red-500 @enderror"
                                required>
                                @foreach($leaders as $leader)
                                    <option value="{{ $leader->id }}" @selected(old('leader_id', $cell->leader_id) == $leader->id)>
                                        {{ $leader->name }} ({{ $leader->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('leader_id')
                                <p class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="timoteos"
                                class="mb-1 block text-[10px] font-black text-gray-500 uppercase tracking-wider">Auxiliares
                                (Timóteos)</label>
                            @php $assignedTimoteoIds = $cell->timoteos->pluck('id')->toArray(); @endphp
                            <select name="timoteos[]" id="timoteos"
                                class="searchable-select custom-select w-full px-3 py-2 bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg text-xs sm:text-sm font-semibold text-gray-900 transition-all"
                                multiple>
                                @foreach($timoteos as $timoteo)
                                    <option value="{{ $timoteo->id }}" @selected(in_array($timoteo->id, old('timoteos', $assignedTimoteoIds)))>
                                        {{ $timoteo->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-500 mt-1.5">
                                <i class="bi bi-info-circle mr-1"></i> Timóteos têm as mesmas permissões que o líder para
                                esta célula.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                <x-location-map id="cell-location-map" :latitude="$cell->latitude" :longitude="$cell->longitude" />
            </div>

            <!-- Footer Actions -->
            <div class="flex flex-wrap gap-2.5 pt-1">
                <button type="submit"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg bg-orange-700 px-5 py-2 text-xs sm:text-sm font-bold text-white hover:bg-orange-800 shadow-sm transition">
                    <i class="bi bi-check2-circle"></i>
                    <span>Guardar Alterações</span>
                </button>
                <a href="{{ route('cells.index') }}"
                    class="inline-flex justify-center items-center rounded-lg border border-gray-300 bg-white px-5 py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const leaderSelect = document.getElementById('leader_id');
        const cellId = {{ $cell->id }};

        if (!typeSelect || !leaderSelect) return;

        const updateLeaders = async function(preserveCurrent = false) {
            const cellType = typeSelect.value;
            const tomSelect = leaderSelect.tomselect;

            try {
                const response = await fetch(`{{ route('cells.eligible-leaders') }}?cell_id=${cellId}&cell_type=${cellType}`);
                const leaders = await response.json();

                if (!tomSelect) return;

                const currentValue = tomSelect.getValue();
                const currentOption = currentValue ? tomSelect.options[currentValue] : null;
                const currentText = currentOption ? currentOption.text : '';

                tomSelect.clear();
                tomSelect.clearOptions();

                if (leaders.length > 0) {
                    leaders.forEach(leader => {
                        tomSelect.addOption({value: leader.id, text: `${leader.name} (${leader.email})`});
                    });

                    if (currentValue && leaders.some(l => l.id == currentValue)) {
                        tomSelect.setValue(currentValue);
                    } else if (currentValue && preserveCurrent && currentText) {
                        tomSelect.addOption({value: currentValue, text: `${currentText} (atual — incompatível)`});
                        tomSelect.setValue(currentValue);
                    }
                } else {
                    tomSelect.addOption({value: '', text: 'Nenhum líder elegível para este tipo de célula'});
                    if (currentValue && preserveCurrent && currentText) {
                        tomSelect.addOption({value: currentValue, text: `${currentText} (atual — incompatível)`});
                        tomSelect.setValue(currentValue);
                    }
                }

                tomSelect.refreshOptions(false);
            } catch (error) {
                console.error('Error loading eligible leaders:', error);
            }
        };

        typeSelect.addEventListener('change', function() { updateLeaders(false); });

        // Pré-filtrar os líderes elegíveis ao carregar a página, mantendo o líder atual
        updateLeaders(true);
    });
</script>
@endpush