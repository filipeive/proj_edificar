@extends('layouts.app')

@section('title', "Editar Zona: $zone->name - Portal Life Church")
@section('page-title', 'Editar Zona')
@section('page-subtitle', 'Atualizar informações da área pastoral ' . $zone->name)

@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('zones.index') }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-left text-xs"></i>
            <span>Voltar</span>
        </a>
        <a href="{{ route('zones.merge', $zone) }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 shadow-sm hover:bg-red-100 transition-colors">
            <i class="bi bi-intersect text-xs"></i>
            <span>Mesclar</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-5">
        <!-- Executive Header Card -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-700">
                    <i class="bi bi-geo-alt text-xl"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-orange-700">Edição de Zona</span>
                    <h1 class="text-lg font-black text-gray-900 tracking-tight">{{ $zone->name }}</h1>
                    <p class="text-xs text-gray-500">Atualize as informações e limites territoriais desta zona pastoral</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <form action="{{ route('zones.update', $zone) }}" method="POST" class="p-4 sm:p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nome da Zona -->
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-bold text-gray-700">
                            Nome da Zona <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('name') border-red-500 @enderror"
                            value="{{ old('name', $zone->name) }}" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seleção do Pastor de Zona -->
                    <div class="space-y-1">
                        <label for="pastor_id" class="block text-xs font-bold text-gray-700">
                            Pastor de Zona (Opcional)
                        </label>
                        <select name="pastor_id" id="pastor_id"
                            class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 @error('pastor_id') border-red-500 @enderror">
                            <option value="">A definir futuramente</option>
                            @foreach ($pastors as $pastor)
                                <option value="{{ $pastor->id }}" @selected(old('pastor_id', $zone->pastor_id) == $pastor->id)>
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
                        class="w-full rounded-lg border-gray-300 px-3 py-2 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 resize-none">{{ old('description', $zone->description) }}</textarea>
                </div>

                <div class="pt-2">
                    <x-location-map id="zone-location-map" mode="area" :latitude="$zone->latitude" :longitude="$zone->longitude" :boundary="$zone->boundary" />
                </div>

                <!-- Zone Visibility in Teaching Services -->
                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-lg border border-gray-200">
                    <div>
                        <p class="text-xs font-bold text-gray-900">Visível em Cultos de Ensino</p>
                        <p class="text-[11px] text-gray-500">Habilita esta zona na ficha de presença de quarta-feira</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="show_in_teaching_service" value="1" 
                            class="sr-only peer" @checked(old('show_in_teaching_service', $zone->show_in_teaching_service ?? false))>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-orange-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('zones.index') }}"
                        class="rounded-lg border border-gray-200 px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-800 transition-colors">
                        <i class="bi bi-check-lg text-xs"></i>
                        <span>Atualizar Zona</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
