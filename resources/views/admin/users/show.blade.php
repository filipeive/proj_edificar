@extends('layouts.app')

@section('title', "Perfil de $user->name - Projeto Edificar")
@section('page-title', $user->name)
@section('page-subtitle', "Detalhes e gestão do utilizador")

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informações do Utilizador -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-8 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">
                <i class="bi bi-person-circle mr-2"></i>Informações Pessoais
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="pb-4 border-b">
                    <p class="text-sm text-gray-500 font-medium">Nome Completo</p>
                    <p class="text-lg font-bold text-gray-800 mt-2">{{ $user->name }}</p>
                </div>

                <div class="pb-4 border-b">
                    <p class="text-sm text-gray-500 font-medium">Email</p>
                    <p class="text-lg font-bold text-gray-800 mt-2">{{ $user->email }}</p>
                </div>

                <div class="pb-4 border-b">
                    <p class="text-sm text-gray-500 font-medium">Telefone</p>
                    <p class="text-lg font-bold text-gray-800 mt-2">{{ $user->phone ?? '-' }}</p>
                </div>

                <div class="pb-4 border-b">
                    <p class="text-sm text-gray-500 font-medium">Papel</p>
                    <span class="inline-block px-4 py-2 bg-blue-100 text-blue-800 rounded-full font-medium mt-2">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                </div>

                <div class="pb-4 border-b">
                    <p class="text-sm text-gray-500 font-medium">Célula Atual</p>
                    @if($user->cell)
                        <a href="{{ route('cells.edit', $user->cell) }}" class="text-lg font-bold text-blue-600 hover:text-blue-800 mt-2">
                            {{ $user->cell->name }}
                        </a>
                    @else
                        <p class="text-lg text-gray-400 mt-2">Não atribuída</p>
                    @endif
                </div>

                <div class="pb-4 border-b">
                    <p class="text-sm text-gray-500 font-medium">Status</p>
                    <span class="inline-block px-4 py-2 {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full font-medium mt-2">
                        {{ $user->is_active ? '✓ Ativo' : '✗ Inativo' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Compromisso Atual -->
        <div class="bg-white rounded-lg shadow p-8 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">
                <i class="bi bi-handshake mr-2"></i>Compromisso Atual
            </h3>

            @if($user->getActiveCommitment())
                @php $commitment = $user->getActiveCommitment(); @endphp
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <p class="text-sm text-gray-600 mb-2">Pacote de Compromisso</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $commitment->package->name }}</p>
                    
                    <div class="grid grid-cols-3 gap-4 mt-6">
                        <div class="bg-white p-4 rounded border border-blue-200">
                            <p class="text-xs text-gray-500">Mínimo</p>
                            <p class="text-lg font-bold text-blue-600">{{ number_format($commitment->package->min_amount, 0) }} MT</p>
                        </div>
                        <div class="bg-white p-4 rounded border border-blue-200">
                            <p class="text-xs text-gray-500">Máximo</p>
                            <p class="text-lg font-bold text-blue-600">
                                @if($commitment->package->max_amount)
                                    {{ number_format($commitment->package->max_amount, 0) }} MT
                                @else
                                    ∞
                                @endif
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded border border-blue-200">
                            <p class="text-xs text-gray-500">Desde</p>
                            <p class="text-lg font-bold text-blue-600">{{ $commitment->start_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <i class="bi bi-exclamation-circle text-3xl text-yellow-600 mb-2"></i>
                    <p class="text-yellow-700 font-medium">Nenhum compromisso ativo</p>
                </div>
            @endif
        </div>

        <!-- Contribuições Recentes -->
        <div class="bg-white rounded-lg shadow p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">
                <i class="bi bi-cash-coin mr-2"></i>Contribuições Recentes
            </h3>

            @if($user->contributions()->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">Data</th>
                                <th class="px-4 py-3 text-left">Valor</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->contributions()->orderBy('contribution_date', 'desc')->take(10)->get() as $contribution)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-bold text-green-600">{{ number_format($contribution->amount, 2, ',', '.') }} MT</td>
                                <td class="px-4 py-3">
                                    @if($contribution->status === 'verificada')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">✓ Verificada</span>
                                    @elseif($contribution->status === 'pendente')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">⏱ Pendente</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-medium">✗ Rejeitada</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('contributions.show', $contribution) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Ver →
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded">
                    <p class="text-gray-500">Nenhuma contribuição registada</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar Ações -->
    <div class="lg:col-span-1">
        <!-- Ações Rápidas -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h4 class="font-bold text-gray-800 mb-4">Ações</h4>
            <div class="space-y-2">
                <a href="{{ route('users.edit', $user) }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center text-sm font-medium">
                    <i class="bi bi-pencil mr-2"></i>Editar
                </a>
                <form action="{{ route('users.destroy', $user) }}" method="POST" class="block" onsubmit="return confirm('Tem certeza?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm font-medium">
                        <i class="bi bi-trash mr-2"></i>Deletar
                    </button>
                </form>
                <a href="{{ route('users.index') }}" class="block w-full bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 text-center text-sm font-medium">
                    <i class="bi bi-arrow-left mr-2"></i>Voltar
                </a>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow p-6 border border-blue-200">
            <h4 class="font-bold text-gray-800 mb-4">
                <i class="bi bi-bar-chart mr-2"></i>Estatísticas
            </h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-sm text-gray-700">Total Contribuído</span>
                    <span class="font-bold text-green-600">{{ number_format($user->contributions()->where('status', 'verificada')->sum('amount'), 2, ',', '.') }} MT</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-sm text-gray-700">Nº Contribuições</span>
                    <span class="font-bold text-blue-600">{{ $user->contributions()->count() }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-sm text-gray-700">Verificadas</span>
                    <span class="font-bold text-green-600">{{ $user->contributions()->where('status', 'verificada')->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-700">Pendentes</span>
                    <span class="font-bold text-yellow-600">{{ $user->contributions()->where('status', 'pendente')->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection