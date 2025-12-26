@extends('layouts.app')

@section('title', 'Novo Encontro de Célula')
@section('page-title', 'Registrar Encontro')
@section('page-subtitle', 'Preencha os dados da reunião da célula')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <a href="{{ route('cell-meetings.index') }}"
                class="text-blue-600 hover:text-blue-800 flex items-center transition">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
        </div>

        <div class="max-w-4xl mx-auto">
            <form action="{{ route('cell-meetings.store') }}" method="POST"
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @csrf

                <div class="p-8 space-y-8">
                    <h4 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center">
                        <i class="bi bi-people-fill mr-3 text-blue-600"></i> Dados do Encontro
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Célula *</label>
                            <select name="cell_id" required
                                onchange="window.location.href='{{ route('cell-meetings.create') }}?cell_id=' + this.value"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecione a célula</option>
                                @foreach($cells as $cell)
                                    <option value="{{ $cell->id }}" {{ old('cell_id', request('cell_id')) == $cell->id ? 'selected' : '' }}>
                                        {{ $cell->name }} ({{ $cell->supervision->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('cell_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Data do Encontro *</label>
                            <input type="date" name="meeting_date" value="{{ old('meeting_date', date('Y-m-d')) }}" required
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            @error('meeting_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Líder do Encontro *</label>
                            <select name="leader_id" required
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecione o líder</option>
                                @foreach($leaders as $leader)
                                    <option value="{{ $leader->id }}" {{ old('leader_id', auth()->id()) == $leader->id ? 'selected' : '' }}>
                                        {{ $leader->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('leader_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tema do Estudo</label>
                            <input type="text" name="theme" value="{{ old('theme') }}"
                                placeholder="Ex: A Importância da Comunhão"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            @error('theme') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Texto Bíblico</label>
                            <input type="text" name="biblical_text" value="{{ old('biblical_text') }}"
                                placeholder="Ex: João 3:16"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            @error('biblical_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @if($members->count() > 0)
                        <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                            <h5 class="text-sm font-black uppercase tracking-widest text-blue-800 mb-4 flex items-center">
                                <i class="bi bi-check2-square mr-2"></i> Chamada de Membros
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($members as $member)
                                    <label
                                        class="flex items-center p-3 bg-white rounded-xl border border-blue-100 hover:border-blue-300 transition-all cursor-pointer group">
                                        <input type="checkbox" name="present_members[]" value="{{ $member->id }}" checked
                                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span
                                            class="ml-3 text-sm font-bold text-gray-700 group-hover:text-blue-700 transition-colors">{{ $member->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-[10px] text-blue-600 mt-4 font-bold italic">* Os membros marcados serão
                                automaticamente registados na Ficha Guia.</p>
                        </div>
                    @endif

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <h5 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-4">Resumo de Participação
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Adultos Presentes</label>
                                <input type="number" name="adults_count" id="adults_count"
                                    value="{{ old('adults_count', $members->count()) }}" min="0" required
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-center text-xl font-bold">
                                @error('adults_count') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Crianças *</label>
                                <input type="number" name="children_count" value="{{ old('children_count', 0) }}" min="0"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-center text-xl font-bold">
                                @error('children_count') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Visitantes *</label>
                                <input type="number" name="visitors_count" value="{{ old('visitors_count', 0) }}" min="0"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-center text-xl font-bold">
                                @error('visitors_count') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Decisões (Conversões)</label>
                        <textarea name="decisions" rows="2" placeholder="Nomes das pessoas que aceitaram a Jesus..."
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('decisions') }}</textarea>
                        @error('decisions') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Observações / Pedidos de
                            Oração</label>
                        <textarea name="observations" rows="4" placeholder="Relate como foi o encontro..."
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('observations') }}</textarea>
                        @error('observations') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-lg transition-all transform hover:-translate-y-1">
                        <i class="bi bi-check-lg mr-2"></i> REGISTRAR ENCONTRO
                    </button>
                </div>
            </form>
        </div>
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
        });
    </script>
@endpush