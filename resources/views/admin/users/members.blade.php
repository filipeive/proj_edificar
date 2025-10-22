@extends('layouts.app')

@section('title', 'Lista de Membros - Projeto Edificar')
@section('page-title', 'Membros')
@section('page-subtitle', 'Membros sob sua alçada hierárquica')

@section('content')
<div class="flex justify-end mb-6">
    <a href="{{ route('members.create') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-bold shadow-md">
        <i class="bi bi-person-plus-fill mr-2"></i>+ Novo Membro
    </a>
</div>

<div class="bg-white rounded-xl shadow-xl overflow-hidden">
    <div class="p-5 border-b border-gray-200">
        <h4 class="text-lg font-semibold text-gray-800">Membros Registrados ({{ $members->total() }})</h4>
        <p class="text-sm text-gray-500">Exibindo membros da sua {{ auth()->user()->role === 'lider_celula' ? 'célula' : (auth()->user()->role === 'supervisor' ? 'supervisão' : (auth()->user()->role === 'pastor_zona' ? 'zona' : 'igreja')) }}</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email / Telefone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Célula</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($members as $member)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <div class="flex items-center">
                            <i class="bi bi-person-circle text-xl mr-3 text-blue-500"></i>
                            {{ $member->name }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <div class="text-xs font-medium">{{ $member->email }}</div>
                        <div class="text-xs text-gray-400">{{ $member->phone ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        @if($member->cell)
                            {{ $member->cell->name }}
                            <div class="text-xs text-gray-400">{{ $member->cell->supervision->name ?? '' }}</div>
                        @else
                            <span class="text-red-500 text-xs font-medium">Sem Célula</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($member->is_active)
                            <span class="badge bg-green-100 text-green-800">Ativo</span>
                        @else
                            <span class="badge bg-red-100 text-red-800">Inativo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('users.show', $member) }}" class="text-blue-600 hover:text-blue-800 transition">
                            Ver
                        </a>
                        <a href="{{ route('users.edit', $member) }}" class="text-orange-600 hover:text-orange-800 transition">
                            Editar
                        </a>
                        {{-- Opcional: Adicionar botão de contribuição rápida --}}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        Nenhum membro encontrado sob sua alçada hierárquica.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Paginação -->
    <div class="p-5 border-t">
        {{ $members->links() }}
    </div>
</div>
@endsection
