@extends('layouts.app')

@section('title', 'Editar Zona - Projeto Edificar')
@section('page-title', 'Editar Zona ' . $zone->name)
@section('page-subtitle', 'Atualizar informações do Pastor e descrição')

@section('content')
<div class="grid grid-max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-xl p-8">
        <form action="{{ route('zones.update', $zone) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Nome da Zona -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                    <input id="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" type="text" name="name"
                        value="{{ old('name', $zone->name) }}" required />
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
                        {{-- A variável $pastors é passada pelo ZoneController@edit --}}
                        @foreach ($pastors as $pastor)
                            <option value="{{ $pastor->id }}" @selected(old('pastor_id', $zone->pastor_id) == $pastor->id)>
                                {{ $pastor->name }} ({{ $pastor->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pastor atual: 
                        <span class="font-semibold text-gray-800">{{ $zone->pastor->name ?? 'Ninguém atribuído' }}</span>
                    </p>
                    @error('pastor_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-8">
                 <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição (Opcional)</label>
                 <textarea name="description" id="description" rows="4" placeholder="Detalhes sobre a cobertura geográfica da zona..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $zone->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-4 justify-end">
                <a href="{{ route('zones.index') }}" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-bold text-center">
                    <i class="bi bi-arrow-left mr-2"></i>Voltar
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-bold shadow-md">
                    <i class="bi bi-save mr-2"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection