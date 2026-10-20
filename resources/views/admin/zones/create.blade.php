@extends('layouts.app')

@section('title', 'Nova Zona Pastoral - Portal Life Church')
@section('page-title', 'Criar Nova Zona')
@section('page-subtitle', 'Registar uma nova divisão ministerial ou área pastoral')

@section('header-actions')
    <a href="{{ route('zones.index') }}"
        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
        <i class="bi bi-arrow-left text-xs"></i>
        <span>Voltar</span>
    </a>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Top Navigation (Desktop) -->
        <div class="hidden md:flex items-center justify-between gap-3">
            <a href="{{ route('zones.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 dark:text-zinc-400 hover:text-orange-700 dark:hover:text-orange-400 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar para Zonas</span>
            </a>
        </div>

        <!-- Executive Header Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-geo-alt text-xl"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">Estrutura Pastoral</span>
                    <h1 class="text-lg font-black text-gray-900 tracking-tight">Criar Nova Zona</h1>
                    <p class="text-xs text-gray-500">Registe uma nova divisão ministerial ou área territorial</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <form action="{{ route('zones.store') }}" method="POST" class="p-4 sm:p-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nome da Zona -->
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-bold text-gray-700">
                            Nome da Zona <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('name') border-red-500 @enderror"
                            value="{{ old('name') }}" placeholder="Ex: Zona Norte" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pastor de Zona -->
                    <div class="space-y-1">
                        <label for="pastor_id" class="block text-xs font-bold text-gray-700">
                            Pastor de Zona (Opcional)
                        </label>
                        <select name="pastor_id" id="pastor_id"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('pastor_id') border-red-500 @enderror">
                            <option value="">A definir futuramente</option>
                            @foreach ($pastors as $pastor)
                                <option value="{{ $pastor->id }}" @selected(old('pastor_id') == $pastor->id)>
                                    {{ $pastor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('pastor_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descrição -->
                <div class="space-y-1">
                    <label for="description" class="block text-xs font-bold text-gray-700">Descrição Detalhada</label>
                    <textarea name="description" id="description" rows="3"
                        placeholder="Detalhes sobre a cobertura geográfica ou características da zona..."
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="pt-2">
                    <x-location-map id="zone-location-map" mode="area" />
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('zones.index') }}"
                        class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                        <i class="bi bi-check-lg text-xs"></i>
                        <span>Registar Zona</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
