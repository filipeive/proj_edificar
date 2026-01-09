@extends('layouts.app')

@section('title', 'Configurações do Sistema')
@section('page-title', 'Configurações')
@section('page-subtitle', 'Gerir configurações gerais do sistema')

@section('content')
    <div class="space-y-8" x-data="{ activeTab: '{{ $group }}' }">
        <!-- Tabs -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2">
            <div class="flex space-x-2">
                @foreach($groups as $groupName)
                    <button @click="activeTab = '{{ $groupName }}'"
                        :class="activeTab === '{{ $groupName }}' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                        class="px-6 py-3 rounded-xl font-bold text-sm transition-all">
                        {{ ucfirst($groupName) }}
                    </button>
                @endforeach
            </div>
        </div>

        <form action="{{ route('settings.update') }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @csrf

            <!-- General Settings -->
            <div x-show="activeTab === 'general'" x-transition>
                <h3 class="text-2xl font-black text-gray-900 mb-6">Informações Gerais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nome da Igreja</label>
                        <input type="text" name="settings[church.name]"
                            value="{{ $settings['church.name']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <input type="email" name="settings[church.email]"
                            value="{{ $settings['church.email']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Descrição</label>
                        <textarea name="settings[church.description]" rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">{{ $settings['church.description']['value'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Telefone</label>
                        <input type="text" name="settings[church.phone]"
                            value="{{ $settings['church.phone']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Website</label>
                        <input type="url" name="settings[church.website]"
                            value="{{ $settings['church.website']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Endereço</label>
                        <input type="text" name="settings[church.address]"
                            value="{{ $settings['church.address']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Branding Settings -->
            <div x-show="activeTab === 'branding'" x-transition>
                <h3 class="text-2xl font-black text-gray-900 mb-6">Personalização Visual</h3>
                <div class="space-y-6">
                    <!-- Logo Upload -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Logo Principal</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center">
                                <img src="{{ $settings['branding.logo_primary']['value'] ?? '/images/logo-white-orange.png' }}"
                                    alt="Logo" class="h-16 mx-auto mb-2">
                                <button type="button" onclick="document.getElementById('logo-primary').click()"
                                    class="text-sm text-blue-600 font-bold">Alterar</button>
                                <input type="file" id="logo-primary" class="hidden" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Cor Primária</label>
                            <input type="color" name="settings[branding.color_primary]"
                                value="{{ $settings['branding.color_primary']['value'] ?? '#3B82F6' }}"
                                class="w-full h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Cor Secundária</label>
                            <input type="color" name="settings[branding.color_secondary]"
                                value="{{ $settings['branding.color_secondary']['value'] ?? '#F97316' }}"
                                class="w-full h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Cor de Destaque</label>
                            <input type="color" name="settings[branding.color_accent]"
                                value="{{ $settings['branding.color_accent']['value'] ?? '#8B5CF6' }}"
                                class="w-full h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regional Settings -->
            <div x-show="activeTab === 'regional'" x-transition>
                <h3 class="text-2xl font-black text-gray-900 mb-6">Configurações Regionais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Moeda</label>
                        <select name="settings[regional.currency]"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                            <option value="MZN" {{ ($settings['regional.currency']['value'] ?? 'MZN') == 'MZN' ? 'selected' : '' }}>Metical (MZN)</option>
                            <option value="USD" {{ ($settings['regional.currency']['value'] ?? '') == 'USD' ? 'selected' : '' }}>Dólar (USD)</option>
                            <option value="EUR" {{ ($settings['regional.currency']['value'] ?? '') == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Símbolo da Moeda</label>
                        <input type="text" name="settings[regional.currency_symbol]"
                            value="{{ $settings['regional.currency_symbol']['value'] ?? 'MT' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fuso Horário</label>
                        <select name="settings[regional.timezone]"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                            <option value="Africa/Maputo" {{ ($settings['regional.timezone']['value'] ?? 'Africa/Maputo') == 'Africa/Maputo' ? 'selected' : '' }}>África/Maputo</option>
                            <option value="UTC" {{ ($settings['regional.timezone']['value'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Formato de Data</label>
                        <select name="settings[regional.date_format]"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                            <option value="d/m/Y" {{ ($settings['regional.date_format']['value'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/AAAA</option>
                            <option value="Y-m-d" {{ ($settings['regional.date_format']['value'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>AAAA-MM-DD</option>
                            <option value="m/d/Y" {{ ($settings['regional.date_format']['value'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/AAAA</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Email Settings -->
            <div x-show="activeTab === 'email'" x-transition>
                <h3 class="text-2xl font-black text-gray-900 mb-6">Configurações de Email</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nome do Remetente</label>
                        <input type="text" name="settings[email.from_name]"
                            value="{{ $settings['email.from_name']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email do Remetente</label>
                        <input type="email" name="settings[email.from_address]"
                            value="{{ $settings['email.from_address']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                <button type="submit"
                    class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/30">
                    <i class="bi bi-check-circle mr-2"></i>
                    Salvar Configurações
                </button>
            </div>
        </form>
    </div>
@endsection