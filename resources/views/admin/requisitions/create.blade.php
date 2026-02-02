@extends('layouts.app')

@section('title', 'Nova Requisição - Portal Life Church')
@section('page-title', 'Nova Requisição')
@section('page-subtitle', 'Solicitar saída de fundos')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="bi bi-cash-stack mr-3"></i>
                Detalhes da Requisição
            </h2>
        </div>

        <form action="{{ route('requisitions.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Valor -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Valor (MT) <span
                            class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">MT</span>
                        </div>
                        <input type="number" name="amount" id="amount" step="0.01" min="0" required
                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-lg border-gray-300 rounded-md"
                            placeholder="0.00">
                    </div>
                </div>

                <!-- Categoria -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Categoria <span
                            class="text-red-500">*</span></label>
                    <select name="category" id="category" required
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm appearance-none custom-select">
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
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição / Motivo <span
                            class="text-red-500">*</span></label>
                    <textarea name="description" id="description" rows="3" required
                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        placeholder="Descreva detalhadamente o motivo desta requisição..."></textarea>
                </div>

                <!-- Comprovante (Opcional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Anexo / Cotação (Opcional)</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <i class="bi bi-cloud-upload text-4xl text-gray-400"></i>
                            <div class="flex text-sm text-gray-600">
                                <label for="proof_file"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload de arquivo</span>
                                    <input id="proof_file" name="proof_file" type="file" class="sr-only">
                                </label>
                                <p class="pl-1">ou arraste e solte</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, PDF até 2MB
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('requisitions.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Enviar Requisição
                </button>
            </div>
        </form>
    </div>
@endsection