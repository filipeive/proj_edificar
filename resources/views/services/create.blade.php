@extends('layouts.app')

@section('title', 'Novo Culto')
@section('page-title', 'Registrar Culto')
@section('page-subtitle', 'Preencha os dados do culto e as ofertas coletadas')

@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <a href="{{ route('services.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center transition">
                <i class="bi bi-arrow-left mr-2"></i> Voltar para Lista
            </a>
        </div>

        <form action="{{ route('services.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Informações Gerais -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i class="bi bi-info-circle mr-2 text-blue-600"></i> Informações Gerais
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data do Culto *</label>
                                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Culto *</label>
                                <select name="service_type" required
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="1st" {{ old('service_type') == '1st' ? 'selected' : '' }}>1º Culto</option>
                                    <option value="2nd" {{ old('service_type') == '2nd' ? 'selected' : '' }}>2º Culto</option>
                                    <option value="3rd" {{ old('service_type') == '3rd' ? 'selected' : '' }}>3º Culto</option>
                                    <option value="4th" {{ old('service_type') == '4th' ? 'selected' : '' }}>4º Culto</option>
                                    <option value="special" {{ old('service_type') == 'special' ? 'selected' : '' }}>Especial
                                    </option>
                                </select>
                                @error('service_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pregador</label>
                                <select name="preacher_id"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Selecione o pregador</option>
                                    @foreach($preachers as $preacher)
                                        <option value="{{ $preacher->id }}" {{ old('preacher_id') == $preacher->id ? 'selected' : '' }}>
                                            {{ $preacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('preacher_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tema</label>
                                <input type="text" name="theme" value="{{ old('theme') }}"
                                    placeholder="Ex: O Poder da Oração"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                @error('theme') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Resumo da Mensagem</label>
                            <textarea name="message" rows="4"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('message') }}</textarea>
                            @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i class="bi bi-people mr-2 text-blue-600"></i> Participação
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Adultos *</label>
                                <input type="number" name="adults_count" value="{{ old('adults_count', 0) }}" min="0"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                @error('adults_count') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Crianças *</label>
                                <input type="number" name="children_count" value="{{ old('children_count', 0) }}" min="0"
                                    required
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                @error('children_count') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ofertas -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                            <i class="bi bi-cash-coin mr-2 text-green-600"></i> Ofertas do Culto
                        </h4>

                        <div class="space-y-4">
                            @foreach($offeringTypes as $index => $type)
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $type->name }}</label>
                                    <div class="relative">
                                        <input type="hidden" name="offerings[{{ $index }}][offering_type_id]"
                                            value="{{ $type->id }}">
                                        <input type="number" step="0.01" name="offerings[{{ $index }}][amount]"
                                            value="{{ old("offerings.{$index}.amount", 0) }}" min="0"
                                            class="w-full pl-12 rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 text-right font-bold text-green-700">
                                        <span
                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium">MT</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div
                            class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100 flex justify-between items-center">
                            <span class="text-sm font-bold text-blue-800">TOTAL ESTIMADO:</span>
                            <span id="totalOfferings" class="text-lg font-black text-blue-900">0,00 MT</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h4 class="text-lg font-bold text-gray-800 mb-4">Observações</h4>
                        <textarea name="observations" rows="3" placeholder="Notas adicionais..."
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('observations') }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all transform hover:-translate-y-1">
                        <i class="bi bi-check-lg mr-2"></i> SALVAR CULTO
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const amountInputs = document.querySelectorAll('input[name^="offerings"][name$="[amount]"]');
            const totalDisplay = document.getElementById('totalOfferings');

            function calculateTotal() {
                let total = 0;
                amountInputs.forEach(input => {
                    total += parseFloat(input.value) || 0;
                });
                totalDisplay.textContent = total.toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MT';
            }

            amountInputs.forEach(input => {
                input.addEventListener('input', calculateTotal);
            });

            calculateTotal();
        });
    </script>
@endsection