@extends('layouts.app')

@section('title', 'Editar Item - Inventário')
@section('page-title', 'Editar Item')
@section('page-subtitle', 'Atualizar dados do património ' . $inventoryItem->name)

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('inventory-items.index') }}"
                class="w-10 h-10 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Editar Item</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Atualizar registro</p>
            </div>
        </div>

        <form action="{{ route('inventory-items.update', $inventoryItem->id) }}" method="POST"
            class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nome do Item</label>
                    <input type="text" name="name" value="{{ $inventoryItem->name }}" required
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-900 placeholder-gray-400">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Categoria</label>
                    <input type="text" name="category" value="{{ $inventoryItem->category }}" required
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-900 placeholder-gray-400">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Quantidade</label>
                    <input type="number" name="quantity" value="{{ $inventoryItem->quantity }}" required min="0"
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-900 placeholder-gray-400">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Condição</label>
                    <select name="condition"
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-900 custom-select">
                        <option value="Novo" {{ $inventoryItem->condition == 'Novo' ? 'selected' : '' }}>Novo</option>
                        <option value="Bom" {{ $inventoryItem->condition == 'Bom' ? 'selected' : '' }}>Bom</option>
                        <option value="Razoável" {{ $inventoryItem->condition == 'Razoável' ? 'selected' : '' }}>Razoável
                        </option>
                        <option value="Ruim" {{ $inventoryItem->condition == 'Ruim' ? 'selected' : '' }}>Ruim</option>
                        <option value="Inutilizável" {{ $inventoryItem->condition == 'Inutilizável' ? 'selected' : '' }}>
                            Inutilizável</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Localização</label>
                    <input type="text" name="location" value="{{ $inventoryItem->location }}"
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-900 placeholder-gray-400">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Valor Estimado
                        (Unitário)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">MT</span>
                        <input type="number" name="value" value="{{ $inventoryItem->value }}" step="0.01" min="0"
                            class="w-full pl-12 pr-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-900 placeholder-gray-400">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Data de
                        Aquisição</label>
                    <input type="date" name="purchased_at"
                        value="{{ $inventoryItem->purchased_at ? $inventoryItem->purchased_at->format('Y-m-d') : '' }}"
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-900">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Descrição /
                    Detalhes</label>
                <textarea name="description" rows="3"
                    class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-900 placeholder-gray-400">{{ $inventoryItem->description }}</textarea>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    Atualizar Item
                </button>
            </div>
        </form>
    </div>
@endsection