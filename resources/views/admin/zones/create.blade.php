@extends('layouts.app')

@section('title', 'Nova Zona - Projeto Edificar')
@section('page-title', 'Nova Zona')
@section('page-subtitle', 'Registar uma nova área geográfica')

@section('content')
<div class="grid grid-max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-xl p-8">
        <form action="{{ route('zones.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Nome da Zona -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome da Zona <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                        value="{{ old('name') }}" placeholder="Ex: Zona Centro" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Seleção do Pastor de Zona -->
                <div>
                    <label for="pastor_id" class="block text-sm font-medium text-gray-700 mb-2">Pastor de Zona (Opcional)</label>
                    <select name="pastor_id" id="pastor_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pastor_id') border-red-500 @enderror">
                        <option value="">-- Selecione um Pastor --</option>
                        {{-- A variável $pastors é passada pelo ZoneController@create --}}
                        @foreach ($pastors as $pastor)
                            <option value="{{ $pastor->id }}" @selected(old('pastor_id') == $pastor->id)>
                                {{ $pastor->name }} ({{ $pastor->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Apenas utilizadores com função **'Pastor de Zona'** aparecem aqui.</p>
                    @error('pastor_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-8">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição (Opcional)</label>
                <textarea name="description" id="description" rows="4" placeholder="Detalhes sobre a cobertura geográfica da zona..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-bold shadow-md">
                    <i class="bi bi-send mr-2"></i>Criar Zona
                </button>
                <a href="{{ route('zones.index') }}" class="flex-1 bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-bold text-center">
                    <i class="bi bi-arrow-left mr-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection