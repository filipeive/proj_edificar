@extends('layouts.app')

@section('title', 'Editar Contribuição - Portal Life Church')
@section('page-title', 'Editar Contribuição')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8">
        <form action="{{ route('contributions.update', $contribution) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-cash-coin mr-2"></i>Valor (MT)
                </label>
                <input type="number" name="amount" id="amount" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror"
                    value="{{ old('amount', $contribution->amount) }}" required>
                @error('amount')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="contribution_date" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-calendar mr-2"></i>Data da Contribuição
                </label>
                <input type="date" name="contribution_date" id="contribution_date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('contribution_date') border-red-500 @enderror"
                    value="{{ old('contribution_date', $contribution->contribution_date->format('Y-m-d')) }}" required>
                @error('contribution_date')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8" x-data="{ proofType: '{{ $contribution->proof_message ? 'message' : 'file' }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-4">
                    <i class="bi bi-paperclip mr-2"></i>Comprovativo
                </label>

                <div class="flex p-1 bg-gray-100 rounded-xl mb-6">
                    <button type="button" @click="proofType = 'file'"
                        :class="proofType === 'file' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500'"
                        class="flex-1 py-2 rounded-lg text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="bi bi-file-earmark-arrow-up"></i> Ficheiro
                    </button>
                    <button type="button" @click="proofType = 'message'"
                        :class="proofType === 'message' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500'"
                        class="flex-1 py-2 rounded-lg text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="bi bi-chat-left-text"></i> Mensagem
                    </button>
                </div>

                <div x-show="proofType === 'file'">
                    @if($contribution->proof_path)
                        <div
                            class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-file-earmark-check text-green-600 text-xl"></i>
                                <div>
                                    <p class="text-xs text-green-600 font-bold uppercase">Arquivo Atual</p>
                                    <a href="{{ route('contributions.receipt', $contribution) }}" target="_blank"
                                        class="text-sm font-medium text-green-800 hover:underline">Visualizar
                                        Comprovativo</a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition group"
                        id="dropZone">
                        <input type="file" name="proof_path" id="proof_path" class="hidden"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <i
                            class="bi bi-cloud-upload text-3xl text-gray-400 mb-2 group-hover:text-blue-500 transition-colors"></i>
                        <p class="text-gray-600 text-sm">Clique para alterar ou arraste o arquivo</p>
                        <p class="text-[10px] text-gray-400 mt-2">PDF, JPG, PNG (Máx. 5MB)</p>
                    </div>
                    <div id="fileName" class="mt-3 text-sm text-green-600 hidden">
                        <i class="bi bi-check-circle"></i> <span id="fileNameText"></span>
                    </div>
                </div>

                <div x-show="proofType === 'message'">
                    <textarea name="proof_message" id="proof_message" rows="5"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 text-sm"
                        placeholder="Cole aqui a mensagem da operadora...">{{ old('proof_message', $contribution->proof_message) }}</textarea>
                </div>

                @error('proof_path')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
                @error('proof_message')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-4">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="bi bi-check mr-2"></i>Atualizar Contribuição
                </button>
                <a href="{{ route('contributions.index') }}"
                    class="flex-1 bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition font-medium text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('proof_path');
    const fileName = document.getElementById('fileName');
    const fileNameText = document.getElementById('fileNameText');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('bg-gray-100');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('bg-gray-100');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('bg-gray-100');
        fileInput.files = e.dataTransfer.files;
        updateFileName();
    });

    fileInput.addEventListener('change', updateFileName);

    function updateFileName() {
        if (fileInput.files.length > 0) {
            fileNameText.textContent = fileInput.files[0].name;
            fileName.classList.remove('hidden');
        }
    }
</script>