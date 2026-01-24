@extends('layouts.app')

@section('title', 'Novo Visitante - Portal Life Church')
@section('page-title', 'Cadastrar Visitante')
@section('page-subtitle', 'Registrar informações do visitante')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
            <form method="POST" action="{{ route('visitors.store') }}">
                @csrf

                <!-- Informações Básicas -->
                <div class="mb-8">
                    <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-person-fill text-orange-600 mr-3"></i>
                        Informações Básicas
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nome Completo *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Idade</label>
                            <input type="number" name="age" value="{{ old('age') }}" min="1" max="150"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('age') border-red-500 @enderror">
                            @error('age')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Sexo</label>
                            <select name="gender"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('gender') border-red-500 @enderror">
                                <option value="">Selecione...</option>
                                <option value="masculino" {{ old('gender') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="feminino" {{ old('gender') == 'feminino' ? 'selected' : '' }}>Feminino</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contato e Localização -->
                <div class="mb-8">
                    <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-geo-alt-fill text-orange-600 mr-3"></i>
                        Contato e Localização
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Telefone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                placeholder="84 123 4567">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Bairro</label>
                            <input type="text" name="neighborhood" value="{{ old('neighborhood') }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('neighborhood') border-red-500 @enderror">
                            @error('neighborhood')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Cidade</label>
                            <input type="text" name="city" value="{{ old('city', 'Maputo') }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('city') border-red-500 @enderror">
                            @error('city')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Data da Visita *</label>
                            <input type="date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('visit_date') border-red-500 @enderror">
                            @error('visit_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Culto que Visitou</label>
                            <select name="service_id"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('service_id') border-red-500 @enderror">
                                <option value="">Selecione...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->date->format('d/m/Y') }} - {{ $service->service_type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Convite -->
                <div class="mb-8">
                    <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-person-plus-fill text-orange-600 mr-3"></i>
                        Informações do Convite
                    </h3>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="invited_by_someone" id="invited_by_someone" value="1"
                                {{ old('invited_by_someone') ? 'checked' : '' }}
                                class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                onchange="document.getElementById('inviter_name_field').classList.toggle('hidden', !this.checked)">
                            <label for="invited_by_someone" class="ml-3 text-sm font-bold text-gray-700">
                                Veio a convite de alguém?
                            </label>
                        </div>

                        <div id="inviter_name_field" class="{{ old('invited_by_someone') ? '' : 'hidden' }}">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nome de quem convidou</label>
                            <input type="text" name="inviter_name" value="{{ old('inviter_name') }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('inviter_name') border-red-500 @enderror">
                            @error('inviter_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Atribuição (Opcional) -->
                <div class="mb-8">
                    <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-diagram-3-fill text-orange-600 mr-3"></i>
                        Atribuição (Opcional)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Zona</label>
                            <select name="zone_id"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('zone_id') border-red-500 @enderror">
                                <option value="">Atribuir depois...</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('zone_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="mb-8">
                    <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center">
                        <i class="bi bi-chat-left-text-fill text-orange-600 mr-3"></i>
                        Observações
                    </h3>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Notas adicionais</label>
                        <textarea name="notes" rows="4"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                            placeholder="Informações adicionais sobre o visitante...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('visitors.index') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-8 py-3 bg-orange-600 text-white rounded-xl font-bold text-sm hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20">
                        <i class="bi bi-check-lg mr-2"></i>Cadastrar Visitante
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle inviter name field
        document.getElementById('invited_by_someone').addEventListener('change', function() {
            document.getElementById('inviter_name_field').classList.toggle('hidden', !this.checked);
        });
    </script>
@endsection
