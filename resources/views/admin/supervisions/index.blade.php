
@extends('layouts.app')

@section('title', 'Gestão de Supervisões - Projeto Edificar')
@section('page-title', 'Supervisões')
@section('page-subtitle', 'Gestão de supervisões da igreja')

@section('content')
<div class="flex justify-end mb-6">
    <a href="{{ route('supervisions.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="bi bi-plus-circle mr-2"></i>Nova Supervisão
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zona</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Células</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($supervisions as $supervision)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-800">{{ $supervision->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $supervision->zone->name }}</td>
                <td class="px-6 py-4 text-sm font-medium">{{ $supervision->cells->count() }}</td>
                <td class="px-6 py-4 text-sm space-x-3">
                    <a href="{{ route('supervisions.show', $supervision) }}" class="text-blue-600 hover:text-blue-800">Ver</a>
                    <a href="{{ route('supervisions.edit', $supervision) }}" class="text-blue-600 hover:text-blue-800">Editar</a>
                    <form action="{{ route('supervisions.destroy', $supervision) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Tem certeza?')">
                            Deletar
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhuma supervisão criada</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection