@extends('layouts.app')

@section('title', 'Nova Requisição - Portal Life Church')
@section('page-title', 'Nova Requisição')
@section('page-subtitle', 'Solicitar saída de fundos')

@section('content')
    <div class="max-w-2xl mx-auto space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Tesouraria & Finanças</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Nova Requisição</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Solicitar saída de fundos</p>
            </div>
            <div>
                <a href="{{ route('requisitions.index') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-arrow-left"></i> <span>Voltar</span>
                </a>
            </div>
        </header>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50">
                <h2 class="text-xs sm:text-sm font-black uppercase tracking-wider text-gray-700 flex items-center gap-2">
                    <i class="bi bi-cash-stack text-orange-700"></i>
                    Detalhes da Requisição
                </h2>
            </div>

            <form action="{{ route('requisitions.store') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-5">
                @csrf

                <div class="space-y-4">
                    <!-- Valor -->
                    <div>
                        <label for="amount" class="block text-xs font-bold text-gray-700 mb-1.5">Valor (MT) <span
                                class="text-red-500">*</span></label>
                        <div class="relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-xs font-bold">MT</span>
                            </div>
                            <input type="number" name="amount" id="amount" step="0.01" min="0" required
                                class="focus:ring-orange-500/20 focus:border-orange-500 block w-full pl-10 pr-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg font-bold text-gray-900"
                                placeholder="0.00">
                        </div>
                    </div>

                    <!-- Categoria -->
                    <div>
                        <label for="category" class="block text-xs font-bold text-gray-700 mb-1.5">Categoria <span
                                class="text-red-500">*</span></label>
                        <select name="category" id="category" required
                            class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg text-xs sm:text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 appearance-none custom-select">
                            <option value="">Selecione uma categoria</option>
                            <option value="Operacional">Custos Operacionais</option>
                            <option value="Material">Material de Consumo</option>
                            <option value="Manutenção">Manutenção e Reparos</option>
                            <option value="Eventos">Eventos</option>
                            <option value="Social">Ação Social</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>

                    <!-- Descrição -->
                    <div>
                        <label for="description" class="block text-xs font-bold text-gray-700 mb-1.5">Descrição / Motivo <span
                                class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="3" required
                            class="focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 block w-full text-xs sm:text-sm border border-gray-300 rounded-lg px-3 py-2"
                            placeholder="Descreva detalhadamente o motivo desta requisição..."></textarea>
                    </div>

                    <!-- Comprovante (Opcional) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Anexo / Cotação (Opcional)</label>
                        <div class="flex justify-center px-4 py-5 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50/40">
                            <div class="space-y-1 text-center">
                                <i class="bi bi-cloud-upload text-2xl text-gray-400"></i>
                                <div class="flex text-xs text-gray-600 justify-center">
                                    <label for="proof_file"
                                        class="relative cursor-pointer rounded-md font-bold text-orange-700 hover:text-orange-800 focus-within:outline-none">
                                        <span>Upload de arquivo</span>
                                        <input id="proof_file" name="proof_file" type="file" class="sr-only">
                                    </label>
                                    <p class="pl-1">ou arraste e solte</p>
                                </div>
                                <p class="text-[11px] text-gray-500">
                                    PNG, JPG, PDF até 2MB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-gray-200 flex justify-end gap-2">
                    <a href="{{ route('requisitions.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-xs sm:text-sm font-bold text-gray-700 bg-white hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-5 py-2 rounded-lg shadow-sm text-xs sm:text-sm font-bold text-white bg-orange-700 hover:bg-orange-800">
                        Enviar Requisição
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection