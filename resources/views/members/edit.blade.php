@extends('layouts.app')

@section('title', 'Editar Membro')
@section('page-title', 'Editar Membro')
@section('page-subtitle', $member->name)

@section('content')
<div class="grid grid-max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <form action="{{ route('members.update', ['member' => $member->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Aviso -->
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                <div class="flex items-start">
                    <i class="bi bi-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>
                    <p class="text-sm text-yellow-800">
                        <strong>Atenção:</strong> Esta tela permite atualizar dados pessoais e hierarquia (Célula/Supervisão). A senha deve ser alterada pelo próprio usuário ou por um Admin no perfil completo.
                    </p>
                </div>
            </div>

            <!-- Dados Pessoais -->
            <div class="mb-8 pb-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="bi bi-person-vcard mr-2"></i>Dados Pessoais
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                            value="{{ old('name', $member->name) }}" placeholder="Nome Completo">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telefone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefone
                        </label>
                        <input type="tel" name="phone" id="phone"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            value="{{ old('phone', $member->phone) }}" placeholder="823562000">
                    </div>
                </div>

                <!-- Email (CORREÇÃO APLICADA AQUI) -->
                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    
                    {{-- Campo visível (READONLY) para exibição --}}
                    <input type="email" 
                        class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-lg"
                        value="{{ $member->email }}" readonly>
                        
                    {{-- Campo oculto (HIDDEN) para enviar o valor ao servidor --}}
                    <input type="hidden" name="email" value="{{ $member->email }}"> 

                    <p class="text-xs text-gray-500 mt-1">O email não pode ser alterado através deste formulário contextual.</p>
                </div>
            </div>

            <!-- Hierarquia (Célula) -->
            <div class="mb-8 pb-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="bi bi-diagram-3 mr-2"></i>Atribuição de Célula
                </h3>
                
                <label for="cell_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bi bi-people-fill mr-2"></i>Nova Célula <span class="text-red-500">*</span>
                </label>
                <select name="cell_id" id="cell_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('cell_id') border-red-500 @enderror">
                    <option value="">-- Selecione a célula --</option>
                    @foreach($availableCells as $cell)
                        <option value="{{ $cell->id }}" {{ old('cell_id', $member->cell_id) == $cell->id ? 'selected' : '' }}>
                            {{ $cell->name }}
                            @if($cell->supervision)
                                ({{ $cell->supervision->name }})
                                @if($cell->supervision->zone)
                                    - {{ $cell->supervision->zone->name }}
                                @endif
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('cell_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">A célula atual é: **{{ $member->cell->name ?? 'N/A' }}**</p>
            </div>

            <!-- Status -->
            <div class="mb-6">
                 <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="bi bi-activity mr-2"></i>Status do Membro
                </h3>
                <label for="is_active" class="flex items-center space-x-3">
                    <input type="hidden" name="is_active" value="0"> {{-- Hidden field para garantir que 0 é enviado se unchecked --}}
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        {{ old('is_active', $member->is_active) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">Membro Ativo (Pode contribuir e aceder ao sistema)</span>
                </label>
            </div>

            <!-- Botões -->
            <div class="flex gap-4 mt-6">
                <button type="submit" 
                    class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-bold shadow-md">
                    <i class="bi bi-save mr-2"></i>Salvar Alterações
                </button>
                <a href="{{ route('members.show', ['member' => $member->id]) }}" 
                    class="flex-1 bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-bold text-center shadow-md">
                    <i class="bi bi-arrow-left mr-2"></i>Voltar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection