@extends('layouts.app')

@section('title', 'Novo Tipo de Evento')
@section('page-title', 'Novo Tipo de Evento')
@section('page-subtitle', 'Criar uma nova categoria de evento')

@section('content')
<div class="space-y-5 max-w-2xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('event-types.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-orange-700 transition">
            <i class="bi bi-arrow-left"></i>
            <span>Voltar para Tipos</span>
        </a>
    </div>

    <form action="{{ route('event-types.store') }}" method="POST" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        @csrf

        <div class="border-b border-gray-100 bg-gray-50/60 px-4 py-3 sm:px-5">
            <p class="text-[10px] font-black uppercase tracking-wider text-orange-700">Formulário de Categoria</p>
            <h2 class="text-sm font-bold text-gray-900">Novo Tipo de Evento</h2>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nome do Tipo <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Culto de Domingo, Casamento, Batismo"
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Descrição</label>
                <textarea name="description" rows="3" placeholder="Breve descrição da categoria..."
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                    class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <label for="is_active" class="text-xs font-bold text-gray-700 cursor-pointer">Tipo Ativo</label>
            </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-5 border-t border-gray-200 flex items-center justify-end gap-2">
            <a href="{{ route('event-types.index') }}" class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition">
                <i class="bi bi-check-lg"></i>
                <span>Salvar Tipo</span>
            </button>
        </div>
    </form>
</div>
@endsection
