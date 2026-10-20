@extends('layouts.app')

@section('title', 'Editar Despesa')
@section('page-title', 'Editar Despesa')
@section('page-subtitle', 'Atualizar informações do registro financeiro')

@section('content')
    <div class="max-w-3xl mx-auto space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Tesouraria & Finanças</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Editar Despesa</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Atualizar informações do registro financeiro</p>
            </div>
            <div>
                <a href="{{ route('expenses.index') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-arrow-left"></i> <span>Voltar</span>
                </a>
            </div>
        </header>

        <form action="{{ route('expenses.update', $expense->id) }}" method="POST"
            class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Descrição</label>
                <input type="text" name="description" value="{{ $expense->description }}" required
                    class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all font-bold text-gray-900 placeholder-gray-400">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Valor (MT)</label>
                    <input type="number" name="amount" value="{{ $expense->amount }}" step="0.01" min="0.01" required
                        class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all font-bold text-gray-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Data</label>
                    <input type="date" name="date" value="{{ $expense->date->format('Y-m-d') }}" required
                        class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all font-bold text-gray-900">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Categoria</label>
                    <select name="category" required
                        class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all font-bold text-gray-900 appearance-none custom-select">
                        <option value="Operacional" {{ $expense->category == 'Operacional' ? 'selected' : '' }}>Custos
                            Operacionais</option>
                        <option value="Material" {{ $expense->category == 'Material' ? 'selected' : '' }}>Material de Consumo
                        </option>
                        <option value="Manutenção" {{ $expense->category == 'Manutenção' ? 'selected' : '' }}>Manutenção e
                            Reparos</option>
                        <option value="Eventos" {{ $expense->category == 'Eventos' ? 'selected' : '' }}>Eventos</option>
                        <option value="Taxas" {{ $expense->category == 'Taxas' ? 'selected' : '' }}>Taxas e Serviços</option>
                        <option value="Outros" {{ $expense->category == 'Outros' ? 'selected' : '' }}>Outros</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Âmbito</label>
                    <select name="scope" required
                        class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all font-bold text-gray-900 appearance-none custom-select">
                        <option value="eclesiastico" {{ ($expense->scope ?? 'eclesiastico') == 'eclesiastico' ? 'selected' : '' }}>Eclesiástico (Igreja)</option>
                        <option value="edificar" {{ ($expense->scope ?? 'eclesiastico') == 'edificar' ? 'selected' : '' }}>
                            Projeto Edificar</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex justify-end gap-2">
                <a href="{{ route('expenses.index') }}"
                    class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-bold text-xs sm:text-sm hover:bg-gray-50 shadow-sm">
                    Cancelar
                </a>
                <button type="submit"
                    class="bg-orange-700 text-white px-5 py-2 rounded-lg font-bold text-xs sm:text-sm hover:bg-orange-800 transition-all shadow-sm">
                    Atualizar Despesa
                </button>
            </div>
        </form>
    </div>
@endsection