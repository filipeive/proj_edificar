@extends('layouts.app')

@section('title', 'Nova Despesa')
@section('page-title', 'Nova Despesa')
@section('page-subtitle', 'Registar saída de caixa do sistema')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('expenses.index') }}"
                class="w-10 h-10 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-500 hover:text-red-500 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Nova Despesa</h1>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Registar saída de caixa</p>
            </div>
        </div>

        <form action="{{ route('expenses.store') }}" method="POST"
            class="bg-white p-6 md:p-8 rounded-[2.5rem] shadow-sm border border-gray-100 space-y-6">
            @csrf

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Descrição</label>
                <input type="text" name="description" required
                    class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 rounded-2xl transition-all font-bold text-gray-900 placeholder-gray-400"
                    placeholder="Ex: Compra de Material de Limpeza">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Valor (MT)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 rounded-2xl transition-all font-bold text-gray-900">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Data</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 rounded-2xl transition-all font-bold text-gray-900">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Categoria</label>
                    <select name="category" required
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 rounded-2xl transition-all font-bold text-gray-900 appearance-none custom-select">
                        <option value="Operacional">Custos Operacionais</option>
                        <option value="Material">Material de Consumo</option>
                        <option value="Manutenção">Manutenção e Reparos</option>
                        <option value="Eventos">Eventos</option>
                        <option value="Taxas">Taxas e Serviços</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Âmbito</label>
                    <select name="scope" required
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 rounded-2xl transition-all font-bold text-gray-900 appearance-none custom-select">
                        <option value="eclesiastico">Eclesiástico (Igreja)</option>
                        <option value="edificar">Projeto Edificar</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit"
                    class="bg-red-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-200">
                    Salvar Despesa
                </button>
            </div>
        </form>
    </div>
@endsection