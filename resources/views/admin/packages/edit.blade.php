
@extends('layouts.app')

@section('title', 'Editar Pacote - Projeto Edificar')
@section('page-title', 'Editar Pacote')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8">
        <form action="{{ route('packages.update', $package) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome do Pacote</label>
                <input type="text" name="name" id="name" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                    value="{{ old('name', $package->name) }}" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="min_amount" class="block text-sm font-medium text-gray-700 mb-2">Valor Mínimo (MT)</label>
                    <input type="number" name="min_amount" id="min_amount" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('min_amount') border-red-500 @enderror"
                        value="{{ old('min_amount', $package->min_amount) }}" required>
                    @error('min_amount')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_amount" class="block text-sm font-medium text-gray-700 mb-2">Valor Máximo (MT)</label>
                    <input type="number" name="max_amount" id="max_amount" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('max_amount') border-red-500 @enderror"
                        value="{{ old('max_amount', $package->max_amount) }}" placeholder="Deixe vazio para infinito">
                    @error('max_amount')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $package->description) }}</textarea>
            </div>

            <div class="mb-6">
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Ordem de Exibição</label>
                <input type="number" name="order" id="order" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    value="{{ old('order', $package->order) }}" required>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300" 
                        @checked(old('is_active', $package->is_active))>
                    <span class="ml-2 text-sm text-gray-700">Pacote Ativo</span>
                </label>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Atualizar Pacote
                </button>
                <a href="{{ route('packages.index') }}" class="flex-1 bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
