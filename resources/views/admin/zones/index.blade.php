@extends('layouts.app')

@section('title', 'Gestão de Zonas - Projeto Edificar')
@section('page-title', 'Zonas')
@section('page-subtitle', 'Gestão das zonas da igreja')

@section('content')
<div class="flex justify-end mb-6">
    <a href="{{ route('zones.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
        <i class="bi bi-plus-circle mr-2"></i>Nova Zona
    </a>
</div>

<div class="bg-white rounded-xl shadow-xl overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nome</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pastor</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Supervisões</th>
                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Células</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($zones as $zone)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="{{ route('zones.show', $zone) }}" class="font-bold text-gray-800 hover:text-blue-600 transition">
                        {{ $zone->name }}
                    </a>
                    <p class="text-xs text-gray-500 truncate max-w-xs">{{ $zone->description ?? 'Sem descrição' }}</p>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    <span class="font-medium">{{ $zone->pastor->name ?? 'Não Atribuído' }}</span>
                    @if ($zone->pastor)
                        <span class="text-xs block text-blue-500">({{ $zone->pastor->email }})</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-700">
                    {{ $zone->supervisions->count() }}
                </td>
                 <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-purple-600">
                    {{ $zone->getTotalCells() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                    <a href="{{ route('zones.edit', $zone) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="{{ route('zones.show', $zone) }}" class="text-gray-600 hover:text-gray-800 font-medium ml-3">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-10 text-gray-500 text-lg">
                    <i class="bi bi-map-fill mr-2"></i>Nenhuma zona cadastrada.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection