@extends('layouts.app')

@section('title', 'Novo Pacote - Portal Life Church')
@section('page-title', 'Novo Pacote de Compromisso')
@section('page-subtitle', 'Configure um novo plano de contribuição financeira')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow p-8">
            <form action="{{ route('packages.store') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome do Pacote</label>
                    <input type="text" name="name" id="name" placeholder="Ex: Pacote 1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="min_amount" class="block text-sm font-medium text-gray-700 mb-2">Valor Mínimo
                            (MT)</label>
                        <input type="number" name="min_amount" id="min_amount" step="0.01" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('min_amount') border-red-500 @enderror"
                            value="{{ old('min_amount') }}" required>
                        @error('min_amount')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_amount" class="block text-sm font-medium text-gray-700 mb-2">Valor Máximo
                            (MT)</label>
                        <input type="number" name="max_amount" id="max_amount" step="0.01" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('max_amount') border-red-500 @enderror"
                            value="{{ old('max_amount') }}" placeholder="Deixe vazio para infinito">
                        @error('max_amount')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                    <textarea name="description" id="description" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="sms_template" class="block text-sm font-medium text-gray-700 mb-2">Template de SMS</label>
                    <textarea name="sms_template" id="sms_template" rows="2"
                        placeholder="Olá [NOME], lembrete de contribuição para o Projetor Edificar..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('sms_template') }}</textarea>
                    <p class="text-[10px] text-gray-500 mt-1">Use [NOME] para o nome do membro.</p>
                </div>

                <div class="mb-6">
                    <label for="whatsapp_template" class="block text-sm font-medium text-gray-700 mb-2">Template de WhatsApp
                        (Mensagem Preparada)</label>
                    <textarea name="whatsapp_template" id="whatsapp_template" rows="2"
                        placeholder="Paz do Senhor! Este é o grupo do Pacote..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('whatsapp_template') }}</textarea>
                    <p class="text-[10px] text-gray-500 mt-1">Esta mensagem poderá ser copiada rapidamente na gestão do
                        pacote.</p>
                </div>

                <div class="mb-6">
                    <label for="whatsapp_link" class="block text-sm font-medium text-gray-700 mb-2">Link do Grupo
                        WhatsApp</label>
                    <input type="url" name="whatsapp_link" id="whatsapp_link" placeholder="https://chat.whatsapp.com/..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('whatsapp_link') border-red-500 @enderror"
                        value="{{ old('whatsapp_link') }}">
                    @error('whatsapp_link')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Ordem de Exibição</label>
                    <input type="number" name="order" id="order" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        value="{{ old('order', 0) }}" required>
                </div>

                <div class="mb-6">
                    <label for="responsible_id" class="block text-sm font-medium text-gray-700 mb-2">Irmão Responsável pelo
                        Pacote</label>
                    <select name="responsible_id" id="responsible_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 custom-select">
                        <option value="">Nenhum responsável atribuído</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('responsible_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->role }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Criar Pacote
                    </button>
                    <a href="{{ route('packages.index') }}"
                        class="flex-1 bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection