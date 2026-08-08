@extends('layouts.app')

@section('title', 'Nova Célula - Portal Life Church')
@section('page-title', 'Nova Célula')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow p-8">
            <form action="{{ route('cells.store') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="supervision_id" class="block text-sm font-medium text-gray-700 mb-2">Supervisão</label>
                    <select name="supervision_id" id="supervision_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 searchable-select custom-select @error('supervision_id') border-red-500 @enderror"
                        required>
                        <option value="">-- Selecione uma supervisão --</option>
                        @foreach($supervisions as $supervision)
                            <option value="{{ $supervision->id }}" @selected(old('supervision_id') == $supervision->id)>
                                {{ $supervision->zone->name }} - {{ $supervision->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supervision_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome da Célula</label>
                    <input type="text" name="name" id="name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Célula</label>
                    <select name="type" id="type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 searchable-select custom-select @error('type') border-red-500 @enderror"
                        required>
                        <option value="membros" {{ old('type', 'membros') == 'membros' ? 'selected' : '' }}>Célula de Membros</option>
                        <option value="lideres" {{ old('type') == 'lideres' ? 'selected' : '' }}>Célula de Líderes</option>
                        <option value="supervisores" {{ old('type') == 'supervisores' ? 'selected' : '' }}>Célula de Supervisores</option>
                        <option value="pastores_zona" {{ old('type') == 'pastores_zona' ? 'selected' : '' }}>Célula de Pastores de Zona</option>
                        <option value="pastores" {{ old('type') == 'pastores' ? 'selected' : '' }}>Célula de Pastores</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="leader_id" class="block text-sm font-medium text-gray-700 mb-2">Líder da Célula</label>
                    <select name="leader_id" id="leader_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 searchable-select custom-select @error('leader_id') border-red-500 @enderror"
                        required>
                        <option value="">-- Selecione um líder --</option>
                        @foreach($leaders as $leader)
                            <option value="{{ $leader->id }}" @selected(old('leader_id') == $leader->id)>
                                {{ $leader->name }} ({{ $leader->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('leader_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="timoteos" class="block text-sm font-medium text-gray-700 mb-2">Timóteos da Célula
                        (Auxiliares)</label>
                    <select name="timoteos[]" id="timoteos"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 searchable-select custom-select"
                        multiple>
                        @foreach($timoteos as $timoteo)
                            <option value="{{ $timoteo->id }}" @selected(in_array($timoteo->id, old('timoteos', [])))>
                                {{ $timoteo->name }} ({{ $timoteo->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1 italic">Segure Ctrl (ou Cmd) para selecionar múltiplos.</p>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Criar Célula
                    </button>
                    <a href="{{ route('cells.index') }}"
                        class="flex-1 bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection