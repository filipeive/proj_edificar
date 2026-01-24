@extends('layouts.app')

@section('title', 'Tipos de Evento')
@section('page-title', 'Tipos de Evento')
@section('page-subtitle', 'Gerenciar categorias de eventos e cerimônias')

@section('header-actions')
    <a href="{{ route('event-types.create') }}"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="bi bi-plus-lg mr-2"></i> Novo Tipo
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold">
                    <th class="px-6 py-4">Nome</th>
                    <th class="px-6 py-4">Descrição</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($eventTypes as $type)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $type->name }}</td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $type->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($type->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Ativo</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">Inativo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('event-types.edit', $type) }}"
                                    class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('event-types.destroy', $type) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este tipo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-list-ul text-4xl mb-3"></i>
                                <span class="font-medium">Nenhum tipo de evento cadastrado.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($eventTypes->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $eventTypes->links() }}
            </div>
        @endif
    </div>
@endsection