@extends('layouts.app')

@section('title', 'Gestão de Pacotes - Projeto Edificar')
@section('page-title', 'Pacotes de Compromisso')
@section('page-subtitle', 'Gestão dos pacotes de contribuição')

@section('content')
<div class="flex justify-end mb-6">
    <a href="{{ route('packages.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
        <i class="bi bi-plus-circle mr-2"></i>Novo Pacote
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intervalo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membros Ativos</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($packages as $package)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-800">{{ $package->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ number_format($package->min_amount, 2, ',', '.') }} - 
                    @if($package->max_amount)
                        {{ number_format($package->max_amount, 2, ',', '.') }}
                    @else
                        <span class="text-lg">∞</span>
                    @endif
                    MT
                </td>
                <td class="px-6 py-4 text-sm font-medium">{{ $package->getActiveMembersCount() }}</td>
                <td class="px-6 py-4 text-sm">
                    @if($package->is_active)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✓ Ativo</span>
                    @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">✗ Inativo</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm space-x-3">
                    <a href="{{ route('packages.edit', $package) }}" class="text-blue-600 hover:text-blue-800">Editar</a>
                    <form action="{{ route('packages.destroy', $package) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Tem certeza?')">
                            Deletar
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection