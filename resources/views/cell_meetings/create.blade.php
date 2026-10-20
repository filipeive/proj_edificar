@extends('layouts.app')

@section('title', 'Novo Encontro de Célula - Portal Life Church')
@section('page-title', 'Registrar Encontro')
@section('page-subtitle', 'Preencha os dados da reunião da célula')

@section('header-actions')
    <a href="{{ route('cell-meetings.index') }}"
        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
        <i class="bi bi-arrow-left text-xs"></i>
        <span>Voltar</span>
    </a>
@endsection

@section('content')
    <div class="space-y-5" x-data="{ 
        meetingType: '{{ old('meeting_type', ($isCellRestricted ?? false) ? 'normal' : 'normal') }}',
        selectedRoles: ['admin', 'pastor', 'pastor_senior', 'pastor_zona', 'supervisor', 'sub_supervisor', 'lider_celula', 'timoteo']
    }">
        <!-- Executive Header Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-calendar-plus text-xl"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">Comunhão e Atas</span>
                    <h1 class="text-lg font-black text-gray-900 tracking-tight">Registrar Encontro</h1>
                    <p class="text-xs text-gray-500">Capture os detalhes, presenças e ocorrências da reunião</p>
                </div>
            </div>
        </div>

        <form action="{{ route('cell-meetings.store') }}" method="POST"
            class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            @csrf

            <div class="p-4 sm:p-6 space-y-5">
                <!-- Seção 1: Configuração Principal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tipo de Encontro <span class="text-red-500">*</span></label>
                        @if($isCellRestricted ?? false)
                            <input type="hidden" name="meeting_type" value="normal">
                            <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-bold text-gray-700">
                                Reunião de Célula
                            </div>
                        @else
                            <select name="meeting_type" x-model="meetingType" required
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="normal">Reunião de Célula</option>
                                <option value="leadership">Reunião de Liderança</option>
                                <option value="supervision">Reunião de Supervisão</option>
                                <option value="zone">Reunião de Zona</option>
                                <option value="general">Reunião Geral</option>
                                <option value="other">Outro / Especial</option>
                            </select>
                        @endif
                        @error('meeting_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="meetingType === 'normal'">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Célula <span class="text-red-500">*</span></label>
                        @if($isCellRestricted ?? false)
                            @if($cells->count() > 1)
                                <select name="cell_id"
                                    onchange="window.location.href='{{ route('cell-meetings.create') }}?cell_id=' + this.value"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                                    required>
                                    @foreach($cells as $cell)
                                        <option value="{{ $cell->id }}" {{ old('cell_id', $restrictedCellId) == $cell->id ? 'selected' : '' }}>
                                            {{ $cell->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="cell_id" value="{{ $restrictedCellId }}">
                                <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-bold text-gray-700">
                                    {{ optional($cells->first())->name ?? 'Célula não definida' }}
                                </div>
                            @endif
                        @else
                            <select name="cell_id"
                                onchange="window.location.href='{{ route('cell-meetings.create') }}?cell_id=' + this.value + '&meeting_type=' + document.querySelector('[name=meeting_type]').value"
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                                :required="meetingType === 'normal'">
                                <option value="">Selecione a célula</option>
                                @foreach($cells as $cell)
                                    <option value="{{ $cell->id }}" {{ old('cell_id', request('cell_id')) == $cell->id ? 'selected' : '' }}>
                                        {{ $cell->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('cell_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="meetingType === 'zone'" style="display: none;">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Zona <span class="text-red-500">*</span></label>
                        <select name="zone_id"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                            :required="meetingType === 'zone'">
                            <option value="">Selecione a zona</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('zone_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="meetingType === 'supervision'" style="display: none;">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Supervisão <span class="text-red-500">*</span></label>
                        <select name="supervision_id"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                            :required="meetingType === 'supervision'">
                            <option value="">Selecione a supervisão</option>
                            @foreach($supervisions as $sup)
                                <option value="{{ $sup->id }}" {{ old('supervision_id') == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supervision_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Data do Encontro <span class="text-red-500">*</span></label>
                        <input type="date" name="meeting_date" value="{{ old('meeting_date', date('Y-m-d')) }}" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                        @error('meeting_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Líder do Encontro <span class="text-red-500">*</span></label>
                        <select name="leader_id" required
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Selecione o líder</option>
                            @foreach($leaders as $leader)
                                <option value="{{ $leader->id }}" {{ old('leader_id', auth()->id()) == $leader->id ? 'selected' : '' }}>
                                    {{ $leader->name }} ({{ $leader->role }})
                                </option>
                            @endforeach
                        </select>
                        @error('leader_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Seção 2: Tema e Texto -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tema do Estudo</label>
                        <input type="text" name="theme" value="{{ old('theme') }}"
                            placeholder="Ex: Vida no Espírito"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Texto Bíblico</label>
                        <input type="text" name="biblical_text" value="{{ old('biblical_text') }}"
                            placeholder="Ex: Salmos 23:1"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </div>

                <!-- Seção 3: Participantes Oficiais (para Reuniões de Liderança, Supervisão ou Zona) -->
                <div x-show="meetingType !== 'normal'" style="display: none;" class="space-y-3 pt-2 border-t border-gray-100">
                    <div class="rounded-lg border border-orange-200 bg-orange-50/50 p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                            <h5 class="text-xs font-black uppercase tracking-wider text-orange-900 flex items-center gap-1.5">
                                <i class="bi bi-person-badge text-orange-700"></i> Participantes Oficiais Presentes
                            </h5>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="role in [
                                    {id: 'pastor_senior', label: 'Senior'},
                                    {id: 'pastor', label: 'Pastores'},
                                    {id: 'pastor_zona', label: 'Zona'},
                                    {id: 'supervisor', label: 'Sup'},
                                    {id: 'sub_supervisor', label: 'Sub-Sup'},
                                    {id: 'lider_celula', label: 'Líderes'},
                                    {id: 'timoteo', label: 'Timóteos'}
                                ]">
                                    <label class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase transition-colors cursor-pointer border"
                                        :class="selectedRoles.includes(role.id) ? 'bg-orange-700 text-white border-orange-700' : 'bg-white text-gray-700 border-gray-200 hover:border-orange-400'">
                                        <input type="checkbox" :value="role.id" x-model="selectedRoles" class="hidden">
                                        <span x-text="role.label"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-64 overflow-y-auto p-1 bg-white rounded-lg border border-orange-100">
                            @foreach($allLeaders as $official)
                                <label x-show="selectedRoles.includes('{{ $official->role }}') || ['admin', 'pastor_senior', 'pastor'].includes('{{ $official->role }}')" 
                                    class="flex items-center p-2 rounded hover:bg-orange-50/50 cursor-pointer border border-transparent hover:border-orange-200">
                                    <input type="checkbox" name="participants[]" value="{{ $official->id }}"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                    <div class="ml-2 min-w-0">
                                        <p class="text-xs font-bold text-gray-900 truncate">{{ $official->name }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase truncate">{{ $official->role }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ata do Encontro (Resumo Formal)</label>
                        <textarea name="minutes" rows="4" placeholder="Descreva os pontos abordados, deliberações e orientações..."
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('minutes') }}</textarea>
                    </div>
                </div>

                <!-- Seção 4: Chamada de Membros (para Reunião de Célula) -->
                <div x-show="meetingType === 'normal'" class="space-y-3 pt-2 border-t border-gray-100">
                    @if($members->count() > 0)
                        <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-4">
                            <h5 class="text-xs font-black uppercase tracking-wider text-gray-900 mb-3 flex items-center gap-1.5">
                                <i class="bi bi-check2-square text-orange-700"></i> Chamada da Célula
                            </h5>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                @foreach($members as $member)
                                    <div class="rounded-lg border border-gray-200 bg-white p-3 shadow-xs space-y-2" x-data="{ isPresent: true }">
                                        <div class="flex items-center justify-between gap-2">
                                            <label class="flex items-center cursor-pointer min-w-0 flex-1">
                                                <input type="checkbox" name="present_members[]" value="{{ $member->id }}" x-model="isPresent"
                                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                                <span class="ml-2 text-xs font-bold text-gray-900 truncate">{{ $member->name }}</span>
                                            </label>
                                            <span x-show="isPresent" class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Presente</span>
                                            <span x-show="!isPresent" class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-bold text-red-600">Ausente</span>
                                        </div>
                                        <div x-show="!isPresent" style="display: none;" class="pt-2 border-t border-gray-100">
                                            <input type="text" name="reasons[{{ $member->id }}]" placeholder="Motivo da ausência..."
                                                class="w-full rounded-md border-gray-200 px-2 py-1 text-[11px] focus:border-orange-500 focus:ring-orange-500">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg border border-dashed border-gray-200 p-6 text-center text-xs text-gray-400 italic">
                            Selecione uma célula para carregar a lista de chamada dos membros.
                        </div>
                    @endif
                </div>

                <!-- Seção 5: Números e Contagens -->
                <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-4">
                    <h5 class="text-xs font-black uppercase tracking-wider text-gray-900 mb-3">Contagem de Participantes</h5>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700 text-center">Adultos Presentes</label>
                            <input type="number" name="adults_count" id="adults_count"
                                value="{{ old('adults_count', $members->count()) }}" min="0" required
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-base font-black text-center text-gray-900 focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700 text-center">Crianças Presentes</label>
                            <input type="number" name="children_count" value="{{ old('children_count', 0) }}" min="0" required
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-base font-black text-center text-gray-900 focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700 text-center">Visitantes</label>
                            <input type="number" name="visitors_count" value="{{ old('visitors_count', 0) }}" min="0" required
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-base font-black text-center text-gray-900 focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                </div>

                <!-- Seção 6: Decisões e Observações -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Decisões (Corações para Jesus)</label>
                        <textarea name="decisions" rows="3" placeholder="Nomes das pessoas que entregaram a vida hoje..."
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 resize-none">{{ old('decisions') }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Observações Pastorais (Opcional)</label>
                        <textarea name="observations" rows="3" placeholder="Algo relevante sobre o encontro..."
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 resize-none">{{ old('observations') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('cell-meetings.index') }}"
                        class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                        <i class="bi bi-check-lg text-xs"></i>
                        <span>Registrar Encontro</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[name="present_members[]"]');
            const adultsCountInput = document.getElementById('adults_count');

            if (checkboxes.length > 0 && adultsCountInput) {
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const checkedCount = document.querySelectorAll('input[name="present_members[]"]:checked').length;
                        adultsCountInput.value = checkedCount;
                    });
                });

                const initialCheckedCount = document.querySelectorAll('input[name="present_members[]"]:checked').length;
                if (initialCheckedCount > 0) {
                    adultsCountInput.value = initialCheckedCount;
                }
            }
        });
    </script>
@endpush
