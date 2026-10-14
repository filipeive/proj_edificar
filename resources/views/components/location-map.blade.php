@props(['id', 'mode' => 'point', 'latitude' => null, 'longitude' => null, 'boundary' => null, 'labelText' => null, 'helpText' => null])

@php
    $latitude = old('latitude', $latitude);
    $longitude = old('longitude', $longitude);
    $boundaryValue = old('boundary', is_array($boundary) ? json_encode($boundary) : $boundary);
@endphp

<section data-location-map class="space-y-3">
    <div class="flex items-center justify-between gap-4">
        <label class="text-xs font-black uppercase tracking-widest text-gray-500">
            {{ $labelText ?? ($mode === 'area' ? 'Área geográfica da zona' : 'Localização da célula') }}
        </label>
        <span class="text-xs text-gray-500" data-map-coordinate-status>
            {{ $latitude && $longitude ? number_format((float) $latitude, 5) . ', ' . number_format((float) $longitude, 5) : 'Sem coordenadas' }}
        </span>
    </div>
    <p class="text-xs text-gray-500">
        @if($mode === 'area')
            Use a ferramenta de polígono no mapa: marque os vértices do limite da zona e feche a forma. A área pode conter várias células.
        @else
            {{ $helpText ?? 'Clique no mapa para marcar a célula; arraste o marcador para ajustar a posição.' }}
        @endif
    </p>

    <div id="{{ $id }}" data-location-editor data-mode="{{ $mode }}"
        data-boundary="{{ $boundaryValue }}"
        class="h-80 md:h-96 w-full rounded-xl border border-gray-200 overflow-hidden bg-gray-100"
        aria-label="Mapa para selecionar a localização"></div>

    <input type="hidden" name="latitude" value="{{ $latitude }}">
    <input type="hidden" name="longitude" value="{{ $longitude }}">
    @if($mode === 'area')
        <input type="hidden" name="boundary" value="{{ $boundaryValue }}">
    @endif

    @error('latitude') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
    @error('longitude') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
    @error('boundary') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
</section>