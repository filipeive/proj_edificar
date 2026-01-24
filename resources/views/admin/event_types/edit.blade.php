@extends('layouts.app')

@section('title', 'Editar Tipo de Evento')
@section('page-title', 'Editar Tipo de Evento')
@section('page-subtitle', 'Atualizar categoria de evento')

@section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('event-types.index') }}"
            class="text-gray-500 hover:text-gray-700 mb-6 inline-flex items-center transition">
            <i class="bi bi-arrow-left mr-2"></i> Voltar
        </a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form action="{{ route('event-types.update', $eventType) }}" method="POST" class="p-6 md:p-8 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nome do Tipo <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $eventType->name) }}" required
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Descrição</label>
                    <textarea name="description" rows="3"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $eventType->description) }}</textarea>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $eventType->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-5 w-5">
                    <label for="is_active" class="ml-2 text-sm text-gray-700 font-medium">Ativo</label>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition shadow-lg">
                        Atualizar Tipo
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection