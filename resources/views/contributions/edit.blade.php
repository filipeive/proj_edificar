@extends('layouts.app')

@section('title', 'Editar Contribuição - Projeto Edificar')
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

            @if($contribution->proof_path)
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded">
                <p class="text-sm text-gray-600 mb-2">Comprovativo Atual:</p>
                <a href="{{ Storage::url($contribution->proof_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="bi bi-download"></i> Ver Arquivo
                </a>
            </div>
            @endif

            <div class="mb-6">
                <label for="proof_path" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-paperclip mr-2"></i>Novo Comprovativo (Opcional)
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-gray-400 transition" id="dropZone">
                    <input type="file" name="proof_path" id="proof_path" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                    <i class="bi bi-cloud-upload text-3xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600">Clique para enviar ou arraste o arquivo</p>
                    <p class="text-sm text-gray-500 mt-2">PDF, JPG, PNG (Máx. 5MB)</p>
                </div>
                <div id="fileName" class="mt-3 text-sm text-green-600 hidden">
                    <i class="bi bi-check-circle"></i> <span id="fileNameText"></span>
                </div>
                @error('proof_path')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="bi bi-check mr-2"></i>Atualizar Contribuição
                </button>
                <a href="{{ route('contributions.index') }}" class="flex-1 bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition font-medium text-center">
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