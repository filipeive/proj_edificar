@extends('layouts.app')

@section('title', 'Editar Pacote - Portal Life Church')
@section('page-title', 'Editar Pacote')
@section('page-subtitle', 'Atualize as configurações do pacote ' . $package->name)

@section('content')
    <div class="max-w-2xl mx-auto space-y-5">
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Projeto Edificar</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Editar Pacote</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Atualize as configurações do pacote {{ $package->name }}</p>
            </div>
            <div>
                <a href="{{ route('packages.index') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-arrow-left"></i> <span>Voltar</span>
                </a>
            </div>
        </header>

        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <form action="{{ route('packages.update', $package) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-xs font-bold text-gray-700 mb-1.5">Nome do Pacote</label>
                    <input type="text" name="name" id="name"
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('name') border-red-500 @enderror"
                        value="{{ old('name', $package->name) }}" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="min_amount" class="block text-xs font-bold text-gray-700 mb-1.5">Valor Mínimo
                            (MT)</label>
                        <input type="number" name="min_amount" id="min_amount" step="0.01" min="0"
                            class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('min_amount') border-red-500 @enderror"
                            value="{{ old('min_amount', $package->min_amount) }}" required>
                        @error('min_amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_amount" class="block text-xs font-bold text-gray-700 mb-1.5">Valor Máximo
                            (MT)</label>
                        <input type="number" name="max_amount" id="max_amount" step="0.01" min="0"
                            class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('max_amount') border-red-500 @enderror"
                            value="{{ old('max_amount', $package->max_amount) }}" placeholder="Deixe vazio para infinito">
                        @error('max_amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-gray-700 mb-1.5">Descrição</label>
                    <textarea name="description" id="description" rows="2"
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">{{ old('description', $package->description) }}</textarea>
                </div>

                <div>
                    <label for="sms_template" class="block text-xs font-bold text-gray-700 mb-1.5">Template de SMS</label>
                    <textarea name="sms_template" id="sms_template" rows="2"
                        placeholder="Olá [NOME], lembrete de contribuição para o Projetor Edificar..."
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">{{ old('sms_template', $package->sms_template) }}</textarea>
                    <p class="text-[10px] text-gray-500 mt-1">Use [NOME] para o nome do membro.</p>
                </div>

                <div>
                    <label for="whatsapp_template" class="block text-xs font-bold text-gray-700 mb-1.5">Template de WhatsApp
                        (Mensagem Preparada)</label>
                    <textarea name="whatsapp_template" id="whatsapp_template" rows="2"
                        placeholder="Paz do Senhor! Este é o grupo do Pacote..."
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">{{ old('whatsapp_template', $package->whatsapp_template) }}</textarea>
                    <p class="text-[10px] text-gray-500 mt-1">Esta mensagem poderá ser copiada rapidamente na gestão do
                        pacote.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="whatsapp_link" class="block text-xs font-bold text-gray-700 mb-1.5">Link do Grupo
                            WhatsApp</label>
                        <input type="url" name="whatsapp_link" id="whatsapp_link" placeholder="https://chat.whatsapp.com/..."
                            class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('whatsapp_link') border-red-500 @enderror"
                            value="{{ old('whatsapp_link', $package->whatsapp_link) }}">
                        @error('whatsapp_link')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="order" class="block text-xs font-bold text-gray-700 mb-1.5">Ordem de Exibição</label>
                        <input type="number" name="order" id="order" min="0"
                            class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500"
                            value="{{ old('order', $package->order) }}" required>
                    </div>
                </div>

                <div>
                    <label for="responsible_id" class="block text-xs font-bold text-gray-700 mb-1.5">Irmão Responsável pelo Pacote</label>
                    <select name="responsible_id" id="responsible_id" class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 custom-select">
                        <option value="">Nenhum responsável atribuído</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('responsible_id', $package->responsible_id) == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->role }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1"
                            class="rounded border-gray-300 text-orange-700 shadow-sm focus:border-orange-500"
                            @checked(old('is_active', $package->is_active))>
                        <span class="ml-2 text-xs sm:text-sm font-bold text-gray-700">Pacote Ativo</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-200">
                    <button type="submit" class="flex-1 bg-orange-700 text-white px-4 py-2.5 rounded-lg hover:bg-orange-800 font-bold text-xs sm:text-sm shadow-sm">
                        Atualizar Pacote
                    </button>
                    <a href="{{ route('packages.index') }}"
                        class="flex-1 border border-gray-300 bg-white text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-50 font-bold text-xs sm:text-sm text-center shadow-sm">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection