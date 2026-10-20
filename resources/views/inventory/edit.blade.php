@extends('layouts.app')

@section('title', 'Editar Item - Inventário')
@section('page-title', 'Editar Item')
@section('page-subtitle', 'Atualizar dados do património ' . $inventoryItem->name)

@section('content')
    <div class="max-w-3xl mx-auto space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div class="flex items-start gap-3">
                <a href="{{ route('inventory-items.index') }}"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-orange-600 transition shadow-sm mt-0.5">
                    <i class="bi bi-arrow-left text-sm"></i>
                </a>
                <div>
                    <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Inventário Eclesiástico</p>
                    <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Editar Item</h1>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Atualizar registo de {{ $inventoryItem->name }}</p>
                </div>
            </div>
        </header>

        <form action="{{ route('inventory-items.update', $inventoryItem->id) }}" method="POST"
            class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Nome do Item</label>
                    <input type="text" name="name" value="{{ $inventoryItem->name }}" required
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Categoria</label>
                    <input type="text" name="category" value="{{ $inventoryItem->category }}" required
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Quantidade</label>
                    <input type="number" name="quantity" value="{{ $inventoryItem->quantity }}" required min="0"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Condição</label>
                    <select name="condition"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all custom-select">
                        <option value="Novo" {{ $inventoryItem->condition == 'Novo' ? 'selected' : '' }}>Novo</option>
                        <option value="Bom" {{ $inventoryItem->condition == 'Bom' ? 'selected' : '' }}>Bom</option>
                        <option value="Razoável" {{ $inventoryItem->condition == 'Razoável' ? 'selected' : '' }}>Razoável
                        </option>
                        <option value="Ruim" {{ $inventoryItem->condition == 'Ruim' ? 'selected' : '' }}>Ruim</option>
                        <option value="Inutilizável" {{ $inventoryItem->condition == 'Inutilizável' ? 'selected' : '' }}>
                            Inutilizável</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Localização</label>
                    <input type="text" name="location" value="{{ $inventoryItem->location }}"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Valor Estimado
                        (Unitário)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-bold">MT</span>
                        <input type="number" name="value" value="{{ $inventoryItem->value }}" step="0.01" min="0"
                            class="w-full rounded-lg border border-gray-300 bg-white pl-10 pr-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Data de
                        Aquisição</label>
                    <input type="date" name="purchased_at"
                        value="{{ $inventoryItem->purchased_at ? $inventoryItem->purchased_at->format('Y-m-d') : '' }}"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider">Descrição /
                    Detalhes</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 placeholder-gray-400 focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all">{{ $inventoryItem->description }}</textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                <a href="{{ route('inventory-items.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 transition shadow-sm">
                    Atualizar Item
                </button>
            </div>
        </form>
    </div>
@endsection
