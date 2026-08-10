@extends('layouts.app')

@section('title', 'Novo Encontro de Célula')
@section('page-title', 'Registrar Encontro')
@section('page-subtitle', 'Preencha os dados da reunião da célula')

@section('content')
    <div class="space-y-8" x-data="{ 
        meetingType: '{{ old('meeting_type', ($isCellRestricted ?? false) ? 'normal' : 'normal') }}',
        selectedRoles: ['admin', 'pastor', 'pastor_senior', 'pastor_zona', 'supervisor', 'sub_supervisor', 'lider_celula', 'timoteo']
    }">
        <div class="mb-6">
            <a href="{{ route('cell-meetings.index') }}"
                class="text-blue-600 hover:text-blue-800 flex items-center transition font-bold">
                <i class="bi bi-arrow-left mr-2 font-black"></i> Voltar para Lista
            </a>
        </div>

        <form action="{{ route('cell-meetings.store') }}" method="POST"
            class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            @csrf

            <div class="p-8 md:p-12 space-y-12">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-gray-50 pb-8">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-widest mb-1">
                            <i class="bi bi-people-fill"></i>
                            <span>Comunhão</span>
                        </div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Registrar Encontro</h1>
                        <p class="text-gray-500 font-medium">Capture os detalhes e a vida da sua reunião</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Tipo de Encontro *</label>
                        @if($isCellRestricted ?? false)
                            <input type="hidden" name="meeting_type" value="normal">
                            <div
                                class="w-full rounded-2xl border border-gray-100 bg-gray-50/70 text-gray-700 font-bold py-3 px-4">
                                Reunião de Célula
                            </div>
                        @else
                            <select name="meeting_type" x-model="meetingType" required
                                class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold py-3">
                                <option value="normal">Reunião de Célula</option>
                                <option value="leadership">Reunião de Liderança</option>
                                <option value="supervision">Reunião de Supervisão</option>
                                <option value="zone">Reunião de Zona</option>
                                <option value="general">Reunião Geral</option>
                                <option value="other">Outro / Especial</option>
                            </select>
                        @endif
                        @error('meeting_type') <p class="text-red-500 text-[10px] font-bold mt-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="meetingType === 'normal'" x-transition>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Célula *</label>
                        @if($isCellRestricted ?? false)
                            @if($cells->count() > 1)
                                <select name="cell_id"
                                    onchange="window.location.href='{{ route('cell-meetings.create') }}?cell_id=' + this.value"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold py-3"
                                    required>
                                    @foreach($cells as $cell)
                                        <option value="{{ $cell->id }}" {{ old('cell_id', $restrictedCellId) == $cell->id ? 'selected' : '' }}>
                                            {{ $cell->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="cell_id" value="{{ $restrictedCellId }}">
                                <div
                                    class="w-full rounded-2xl border border-gray-100 bg-gray-50/70 text-gray-700 font-bold py-3 px-4">
                                    {{ optional($cells->first())->name ?? 'Célula não definida' }}
                                </div>
                            @endif
                        @else
                            <select name="cell_id"
                                onchange="window.location.href='{{ route('cell-meetings.create') }}?cell_id=' + this.value + '&meeting_type=' + document.querySelector('[name=meeting_type]').value"
                                class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold py-3"
                                :required="meetingType === 'normal'">
                                <option value="">Selecione a célula</option>
                                @foreach($cells as $cell)
                                    <option value="{{ $cell->id }}" {{ old('cell_id', request('cell_id')) == $cell->id ? 'selected' : '' }}>
                                        {{ $cell->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('cell_id') <p class="text-red-500 text-[10px] font-bold mt-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="meetingType === 'zone'" x-transition>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Zona *</label>
                        <select name="zone_id"
                            class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold py-3"
                            :required="meetingType === 'zone'">
                            <option value="">Selecione a zona</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('zone_id') <p class="text-red-500 text-[10px] font-bold mt-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>

                    <div x-show="meetingType === 'supervision'" x-transition>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Supervisão *</label>
                        <select name="supervision_id"
                            class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold py-3"
                            :required="meetingType === 'supervision'">
                            <option value="">Selecione a supervisão</option>
                            @foreach($supervisions as $sup)
                                <option value="{{ $sup->id }}" {{ old('supervision_id') == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supervision_id') <p class="text-red-500 text-[10px] font-bold mt-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Data *</label>
                        <input type="date" name="meeting_date" value="{{ old('meeting_date', date('Y-m-d')) }}" required
                            class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold py-3">
                        @error('meeting_date') <p class="text-red-500 text-[10px] font-bold mt-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Líder do Encontro *</label>
                        <select name="leader_id" required
                            class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold py-3">
                            <option value="">Selecione o líder</option>
                            @foreach($leaders as $leader)
                                <option value="{{ $leader->id }}" {{ old('leader_id', auth()->id()) == $leader->id ? 'selected' : '' }}>
                                    {{ $leader->name }} ({{ $leader->role }})
                                </option>
                            @endforeach
                        </select>
                        @error('leader_id') <p class="text-red-500 text-[10px] font-bold mt-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Tema do Estudo</label>
                        <input type="text" name="theme" value="{{ old('theme') }}"
                            placeholder="Ex: Vida no Espírito"
                            class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold py-3 px-4">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Texto Bíblico</label>
                        <input type="text" name="biblical_text" value="{{ old('biblical_text') }}"
                            placeholder="Ex: Salmos 23:1"
                            class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold py-3 px-4">
                    </div>
                </div>

                <!-- Participants Section (Visible only for non-normal meetings) -->
                <div x-show="meetingType !== 'normal'" x-transition.fade class="space-y-6">
                    <div class="p-8 bg-orange-50/50 rounded-[2.5rem] border border-orange-100">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                            <h5 class="text-xs font-black uppercase tracking-widest text-orange-600 flex items-center">
                                <i class="bi bi-person-badge-fill mr-2"></i> Registro de Participantes (Oficiais)
                            </h5>
                            
                            <div class="flex flex-wrap gap-2">
                                <template x-for="role in [
                                    {id: 'pastor_senior', label: 'Pastor Senior'},
                                    {id: 'pastor', label: 'Pastores'},
                                    {id: 'pastor_zona', label: 'Coordenação'},
                                    {id: 'supervisor', label: 'Supervisores'},
                                    {id: 'sub_supervisor', label: 'Sub-Sup'},
                                    {id: 'lider_celula', label: 'Líderes'},
                                    {id: 'timoteo', label: 'Timóteos'}
                                ]">
                                    <label class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-tighter transition-all cursor-pointer border"
                                        :class="selectedRoles.includes(role.id) ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-orange-600 border-orange-200 hover:border-orange-400'">
                                        <input type="checkbox" :value="role.id" x-model="selectedRoles" class="hidden">
                                        <span x-text="role.label"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 max-h-96 overflow-y-auto p-2">
                            @foreach($allLeaders as $official)
                                <label x-show="selectedRoles.includes('{{ $official->role }}') || ['admin', 'pastor_senior', 'pastor'].includes('{{ $official->role }}')" 
                                    x-transition.fade
                                    class="flex items-center p-4 bg-white rounded-2xl border border-orange-100 hover:border-orange-300 transition-all cursor-pointer group shadow-sm">
                                    <input type="checkbox" name="participants[]" value="{{ $official->id }}"
                                        class="w-5 h-5 text-orange-600 border-gray-200 rounded-lg focus:ring-orange-500">
                                    <div class="ml-3">
                                        <p class="text-xs font-black text-gray-800 group-hover:text-orange-700">{{ $official->name }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $official->role }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Ata do Encontro (Resumo Formal)</label>
                        <textarea name="minutes" rows="10" placeholder="Descreva os pontos abordados, decisões tomadas e orientações dadas..."
                            class="w-full rounded-[2rem] border-gray-100 bg-gray-50/50 focus:ring-orange-500 focus:border-orange-500 font-medium p-6">{{ old('minutes') }}</textarea>
                    </div>
                </div>

                <!-- Attendance Section (For Normal Meetings) -->
                <div x-show="meetingType === 'normal'" x-transition.fade class="space-y-8">
                    @if($members->count() > 0)
                        <div class="bg-blue-50/50 p-8 rounded-[2.5rem] border border-blue-100">
                            <h5 class="text-xs font-black uppercase tracking-widest text-blue-600 mb-6 flex items-center">
                                <i class="bi bi-check2-square mr-2"></i> Chamada da Célula
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($members as $member)
                                    <div class="bg-white rounded-2xl border border-blue-100 p-6 shadow-sm hover:border-blue-300 transition-all" x-data="{ isPresent: true }">
                                        <div class="flex items-center justify-between gap-4">
                                            <label class="flex items-center cursor-pointer group flex-1">
                                                <input type="checkbox" name="present_members[]" value="{{ $member->id }}" x-model="isPresent"
                                                    class="w-6 h-6 text-blue-600 border-gray-200 rounded-lg focus:ring-blue-500">
                                                <span class="ml-4 font-bold text-gray-700 group-hover:text-blue-700">{{ $member->name }}</span>
                                            </label>
                                            <div class="flex items-center gap-2">
                                                <span x-show="isPresent" class="text-[10px] font-black text-green-500 uppercase tracking-widest bg-green-50 px-3 py-1 rounded-full border border-green-100">Presente</span>
                                                <span x-show="!isPresent" class="text-[10px] font-black text-red-400 uppercase tracking-widest bg-red-50 px-3 py-1 rounded-full border border-red-100">Ausente</span>
                                            </div>
                                        </div>
                                        <div x-show="!isPresent" x-transition class="mt-4 pt-4 border-t border-gray-50">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Justificação da Ausência</label>
                                            <input type="text" name="reasons[{{ $member->id }}]" placeholder="Ex: Doença, Trabalho..."
                                                class="w-full text-xs font-bold bg-gray-50 border-transparent focus:bg-white focus:ring-2 focus:ring-blue-100 rounded-xl py-2 px-3 transition-all">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="p-8 bg-gray-50 rounded-[2.5rem] border border-dashed border-gray-200 text-center">
                            <i class="bi bi-info-circle text-2xl text-gray-400 mb-2 block"></i>
                            <p class="text-gray-500 font-medium">Selecione uma célula para carregar a lista de membros.</p>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50/50 p-8 rounded-[2.5rem] border border-gray-100">
                    <h5 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-8">Participação e Decisões</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                        <div class="text-center space-y-4">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest">Adultos</label>
                            <input type="number" name="adults_count" id="adults_count"
                                value="{{ old('adults_count', $members->count()) }}" min="0" required
                                class="w-32 mx-auto rounded-2xl border-gray-100 bg-white focus:ring-blue-500 focus:border-blue-500 text-center text-3xl font-black py-4 shadow-sm">
                        </div>

                        <div class="text-center space-y-4">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest">Crianças</label>
                            <input type="number" name="children_count" value="{{ old('children_count', 0) }}" min="0" required
                                class="w-32 mx-auto rounded-2xl border-gray-100 bg-white focus:ring-blue-500 focus:border-blue-500 text-center text-3xl font-black py-4 shadow-sm">
                        </div>

                        <div class="text-center space-y-4">
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest">Visitantes</label>
                            <input type="number" name="visitors_count" value="{{ old('visitors_count', 0) }}" min="0" required
                                class="w-32 mx-auto rounded-2xl border-gray-100 bg-white focus:ring-blue-500 focus:border-blue-500 text-center text-3xl font-black py-4 shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Decisões (Corações para Jesus)</label>
                        <textarea name="decisions" rows="3" placeholder="Nomes das pessoas que entregaram a vida hoje..."
                            class="w-full rounded-[1.5rem] border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold p-4">{{ old('decisions') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Observações (Opcional)</label>
                        <textarea name="observations" rows="3" placeholder="Algo especial que queira compartilhar..."
                            class="w-full rounded-[1.5rem] border-gray-100 bg-gray-50/50 focus:ring-blue-500 focus:border-blue-500 font-bold p-4">{{ old('observations') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="px-8 md:px-12 py-8 bg-gray-50 border-t border-gray-50 flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-black px-12 py-4 rounded-2xl shadow-xl shadow-blue-100 transition-all transform hover:-translate-y-1">
                    <i class="bi bi-check-lg mr-2 text-2xl"></i> REGISTRAR ENCONTRO
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[name="present_members[]"]');
            const adultsCountInput = document.getElementById('adults_count');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const checkedCount = document.querySelectorAll('input[name="present_members[]"]:checked').length;
                    adultsCountInput.value = checkedCount;
                });
            });

            // Initial count
            const initialCheckedCount = document.querySelectorAll('input[name="present_members[]"]:checked').length;
            if (initialCheckedCount > 0) {
                adultsCountInput.value = initialCheckedCount;
            }
        });
    </script>
@endpush
