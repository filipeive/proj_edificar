@extends('layouts.app')

@section('title', 'Gestão de Cultos')
@section('page-title', 'Cultos')
@section('page-subtitle', 'Registro e acompanhamento de cultos e ofertas')

@section('content')
    <div class="container-fluid">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-2xl font-bold text-gray-800">Lista de Cultos</h3>
            @can('create', App\Models\Service::class)
                <a href="{{ route('services.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition shadow-md">
                    <i class="bi bi-plus-lg mr-2"></i> Novo Culto
                </a>
            @endcan
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex items-center">
                <i class="bi bi-check-circle-fill mr-3 text-xl"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Data</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Tipo</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Pregador</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600">Tema</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Participantes</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-right">Total Ofertas</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($services as $service)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm text-gray-800 font-medium">
                                                {{ $service->date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                                    @if($service->service_type == 'special') bg-purple-100 text-purple-700 
                                                    @else bg-blue-100 text-blue-700 @endif">
                                                    {{ $service->service_type == '1st' ? '1º Culto' :
                            ($service->service_type == '2nd' ? '2º Culto' :
                                ($service->service_type == '3rd' ? '3º Culto' :
                                    ($service->service_type == '4th' ? '4º Culto' : 'Especial'))) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $service->preacher->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 italic">
                                                {{ $service->theme ?? 'Sem tema' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-gray-800 font-semibold">{{ $service->adults_count + $service->children_count }}</span>
                                                    <span class="text-xs text-gray-500">{{ $service->adults_count }}A /
                                                        {{ $service->children_count }}C</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-right font-bold text-green-600">
                                                {{ number_format($service->total_offerings, 2, ',', '.') }} MT
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('services.show', $service) }}"
                                                        class="text-blue-600 hover:text-blue-800 p-1" title="Ver Detalhes">
                                                        <i class="bi bi-eye text-lg"></i>
                                                    </a>
                                                    @can('update', $service)
                                                        <a href="{{ route('services.edit', $service) }}"
                                                            class="text-yellow-600 hover:text-yellow-800 p-1" title="Editar">
                                                            <i class="bi bi-pencil text-lg"></i>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $service)
                                                        <form action="{{ route('services.destroy', $service) }}" method="POST"
                                                            onsubmit="return confirm('Tem certeza que deseja excluir este culto?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Excluir">
                                                                <i class="bi bi-trash text-lg"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">
                                    Nenhum culto registrado até o momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($services->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection