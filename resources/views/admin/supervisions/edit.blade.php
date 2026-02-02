@extends('layouts.app')

@section('title', 'Editar Supervisão - Portal Life Church')
@section('page-title', 'Editar Supervisão')
@section('page-subtitle', 'Atualizar informações da estrutura de supervisão')

@section('content')
    <div class="w-full ">
        <div class="bg-white rounded-lg shadow p-8">
            <!-- voltar -->
            <div class="flex justify-between items-center mb-6">
                <div class="btn btn-danger">
                    <a href="{{ route('supervisions.index') }}">
                        <i class="bi bi-arrow-left-short"></i> Voltar
                    </a>
                </div>
                <a href="{{ route('supervisions.merge', $supervision) }}"
                    class="bg-red-50 text-red-600 px-4 py-2 rounded-lg hover:bg-red-100 transition-all font-bold text-sm border border-red-200 flex items-center">
                    <i class="bi bi-intersect mr-2"></i>
                    Mesclar Supervisão
                </a>
            </div>
            <form action="{{ route('supervisions.update', $supervision) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="zone_id" class="block text-sm font-medium text-gray-700 mb-2">Zona</label>
                    <select name="zone_id" id="zone_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 searchable-select custom-select @error('zone_id') border-red-500 @enderror"
                        required>
                        <option value="">-- Selecione uma zona --</option>
                        @foreach($zones as $zone)
                            <option value="{{ $zone->id }}" @selected(old('zone_id', $supervision->zone_id) == $zone->id)>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('zone_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-2">Supervisor
                        Responsável</label>
                    <select name="supervisor_id" id="supervisor_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 searchable-select custom-select @error('supervisor_id') border-red-500 @enderror">
                        <option value="">-- Selecione um supervisor --</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}" @selected(old('supervisor_id', $supervision->supervisor_id) == $supervisor->id)>
                                {{ $supervisor->name }} ({{ $supervisor->role }})
                            </option>
                        @endforeach
                    </select>
                    @error('supervisor_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome da Supervisão</label>
                    <input type="text" name="name" id="name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        value="{{ old('name', $supervision->name) }}" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $supervision->description) }}</textarea>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Atualizar Supervisão
                    </button>
                    <a href="{{ route('supervisions.index') }}"
                        class="flex-1 bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection