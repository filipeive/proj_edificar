@extends('layouts.app')

@section('title', 'Novo Visitante - Portal Life Church')
@section('page-title', 'Cadastrar Visitante')
@section('page-subtitle', 'Registrar informações do visitante')

@section('header-actions')
    <a href="{{ route('visitors.index') }}"
        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
        <i class="bi bi-arrow-left text-xs"></i>
        <span>Voltar</span>
    </a>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Top Navigation (Desktop) -->
        <div class="hidden md:flex items-center justify-between gap-3">
            <a href="{{ route('visitors.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 dark:text-zinc-400 hover:text-orange-700 dark:hover:text-orange-400 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Visitantes</span>
            </a>
        </div>

        <!-- Executive Header Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-person-plus text-xl"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">Recepção e Integração</span>
                    <h1 class="text-lg font-black text-gray-900 tracking-tight">Cadastrar Visitante</h1>
                    <p class="text-xs text-gray-500">Registe os dados do visitante para acompanhamento e direcionamento à célula</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('visitors.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Coluna Esquerda: Dados do Visitante -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Dados Pessoais -->
                    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">1. Dados Pessoais</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2 space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Nome Completo <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    placeholder="Ex: Manuel dos Santos"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Idade</label>
                                <input type="number" name="age" value="{{ old('age') }}" min="1" max="150"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('age') border-red-500 @enderror"
                                    placeholder="Ex: 28">
                                @error('age')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Sexo</label>
                                <select name="gender"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('gender') border-red-500 @enderror">
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
                    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">2. Contato e Localização</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Telefone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('phone') border-red-500 @enderror"
                                    placeholder="84 123 4567">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Bairro</label>
                                <input type="text" name="neighborhood" value="{{ old('neighborhood') }}"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('neighborhood') border-red-500 @enderror"
                                    placeholder="Ex: Polana Caniço">
                                @error('neighborhood')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Cidade</label>
                                <input type="text" name="city" value="{{ old('city', 'Maputo') }}"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('city') border-red-500 @enderror">
                                @error('city')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Data da Visita <span class="text-red-500">*</span></label>
                                <input type="date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" required
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('visit_date') border-red-500 @enderror">
                                @error('visit_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-2">
                        <label class="block text-xs font-bold text-gray-700">Observações e Notas</label>
                        <textarea name="notes" rows="3"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 resize-none"
                            placeholder="Informações adicionais sobre o visitante...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Coluna Direita: Contexto & Integração -->
                <div class="space-y-4">
                    <!-- Culto e Convite -->
                    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Contexto da Visita</h3>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700">Culto Visitado</label>
                            <select name="service_id"
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Selecione...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->date->format('d/m/Y') }} - {{ $service->service_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="invited_by_someone" id="invited_by_someone" value="1"
                                    {{ old('invited_by_someone') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                                    onchange="document.getElementById('inviter_name_field').classList.toggle('hidden', !this.checked)">
                                <span class="text-xs font-bold text-gray-700">Veio a convite de alguém?</span>
                            </label>

                            <div id="inviter_name_field" class="{{ old('invited_by_someone') ? '' : 'hidden' }} mt-2.5 space-y-1">
                                <label class="block text-xs font-bold text-gray-700">Nome de quem convidou</label>
                                <input type="text" name="inviter_name" value="{{ old('inviter_name') }}"
                                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                                    placeholder="Ex: Irmão João">
                            </div>
                        </div>
                    </div>

                    <!-- Atribuição Pastoral -->
                    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-3">
                        <h3 class="text-xs font-black uppercase tracking-wider text-gray-900">Integração Pastoral</h3>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700">Zona para Integração</label>
                            <select name="zone_id" id="zone_select"
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Atribuir depois...</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700">Célula para Integração</label>
                            <select name="cell_id" id="cell_select"
                                class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="">Selecione a Zona Primeiro...</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                            <i class="bi bi-check-lg text-sm"></i>
                            <span>Finalizar Registo</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const zoneSelect = document.getElementById('zone_select');
            const cellSelect = document.getElementById('cell_select');

            if (zoneSelect && cellSelect) {
                zoneSelect.addEventListener('change', async function() {
                    const zoneId = this.value;
                    cellSelect.innerHTML = '<option value="">Carregando...</option>';

                    if (!zoneId) {
                        cellSelect.innerHTML = '<option value="">Selecione a Zona Primeiro...</option>';
                        return;
                    }

                    try {
                        const response = await fetch(`{{ route('visitors.cells-by-zone') }}?zone_id=${zoneId}`);
                        const cells = await response.json();

                        cellSelect.innerHTML = '<option value="">Selecione a Célula...</option>';
                        if (cells.length > 0) {
                            cells.forEach(cell => {
                                const option = document.createElement('option');
                                option.value = cell.id;
                                option.textContent = `${cell.name} (${cell.type_label || 'Geral'})`;
                                cellSelect.appendChild(option);
                            });
                        } else {
                            cellSelect.innerHTML = '<option value="">Nenhuma célula nesta zona</option>';
                        }
                    } catch (error) {
                        console.error('Error loading cells:', error);
                        cellSelect.innerHTML = '<option value="">Erro ao carregar células</option>';
                    }
                });

                if (zoneSelect.value) {
                    zoneSelect.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
@endsection
