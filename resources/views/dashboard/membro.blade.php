@extends('layouts.app')

@section('title', 'Meu Dashboard - Projeto Edificar')
@section('page-title', 'Meu Dashboard')
@section('page-subtitle', 'Acompanhe suas contribuições')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Meu Compromisso -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">Meu Compromisso</h3>
            <a href="{{ route('commitments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                Alterar <i class="bi bi-chevron-right"></i>
            </a>
        </div>
        @if($commitment)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-gray-600">{{ $commitment->package->name }}</p>
            <p class="text-2xl font-bold text-blue-600 mt-2">
                {{ number_format($commitment->package->min_amount, 2, ',', '.') }} - 
                @if($commitment->package->max_amount)
                    {{ number_format($commitment->package->max_amount, 2, ',', '.') }}
                @else
                    ∞
                @endif
                MT
            </p>
            <p class="text-xs text-gray-500 mt-2">Desde {{ $commitment->start_date->format('d/m/Y') }}</p>
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-500 mb-4">Você ainda não escolheu um compromisso</p>
            <a href="{{ route('commitments.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Escolher Agora
            </a>
        </div>
        @endif
    </div>

    <!-- Total Este Mês -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Total Este Mês</h3>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-3xl font-bold text-green-600">{{ number_format($totalThisMonth, 2, ',', '.') }} MT</p>
            <p class="text-xs text-gray-500 mt-2">Do dia 20 ao 5</p>
        </div>
    </div>

    <!-- Últimas Contribuições -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Próximos Passos</h3>
        <div class="space-y-2">
            <a href="{{ route('contributions.create') }}" class="block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-center text-sm">
                + Nova Contribuição
            </a>
            <a href="{{ route('contributions.index') }}" class="block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 text-center text-sm">
                Ver Histórico
            </a>
        </div>
    </div>
</div>

<!-- Últimas Contribuições -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-800">Últimas Contribuições</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributions as $contribution)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $contribution->contribution_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ number_format($contribution->amount, 2, ',', '.') }} MT</td>
                    <td class="px-6 py-4 text-sm">
                        @if($contribution->status === 'verificada')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                <i class="bi bi-check-circle"></i> Verificada
                            </span>
                        @elseif($contribution->status === 'pendente')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">
                                <i class="bi bi-clock"></i> Pendente
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                <i class="bi bi-x-circle"></i> Rejeitada
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('contributions.show', $contribution) }}" class="text-blue-600 hover:text-blue-800">
                            Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        Nenhuma contribuição ainda
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection