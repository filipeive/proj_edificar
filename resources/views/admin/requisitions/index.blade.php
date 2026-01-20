@extends('layouts.app')

@section('title', 'Requisições de Fundos')
@section('page-title', 'Requisições de Fundos')
@section('page-subtitle', 'Gerencie solicitações de saída de caixa')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
            <div class="text-gray-500 text-sm">Pendentes</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
            <div class="text-gray-500 text-sm">Aprovadas</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Histórico de Requisições</h3>
            <a href="{{ route('requisitions.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                <i class="bi bi-plus-lg mr-1"></i> Nova Requisição
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Data</th>
                        <th class="px-6 py-3">Solicitante</th>
                        <th class="px-6 py-3">Descrição</th>
                        <th class="px-6 py-3 text-right">Valor</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitions as $requisition)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">#{{ $requisition->id }}</td>
                            <td class="px-6 py-4">{{ $requisition->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $requisition->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $requisition->category }}</div>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate" title="{{ $requisition->description }}">
                                {{ $requisition->description }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                {{ number_format($requisition->amount, 2, ',', '.') }} MT
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($requisition->status === 'pending')
                                    <span
                                        class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Pendente</span>
                                @elseif($requisition->status === 'approved')
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Aprovada</span>
                                @elseif($requisition->status === 'rejected')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Rejeitada</span>
                                @else
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $requisition->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($requisition->status === 'pending' && (auth()->user()->isAdmin() || auth()->user()->isTesouraria()))
                                    <div class="flex justify-center space-x-2">
                                        <form action="{{ route('requisitions.approve', $requisition) }}" method="POST"
                                            onsubmit="return confirm('Aprovar esta requisição? Isso gerará uma despesa automaticamente.')">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Aprovar">
                                                <i class="bi bi-check-circle-fill text-xl"></i>
                                            </button>
                                        </form>
                                        <button
                                            onclick="document.getElementById('rejectModal-{{ $requisition->id }}').classList.remove('hidden')"
                                            class="text-red-600 hover:text-red-900" title="Rejeitar">
                                            <i class="bi bi-x-circle-fill text-xl"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de Rejeição -->
                                    <div id="rejectModal-{{ $requisition->id }}"
                                        class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
                                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-2">Rejeitar Requisição
                                                #{{ $requisition->id }}</h3>
                                            <form action="{{ route('requisitions.reject', $requisition) }}" method="POST">
                                                @csrf
                                                <textarea name="rejection_reason" class="w-full border rounded p-2 mb-4"
                                                    placeholder="Motivo da rejeição..." required></textarea>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button"
                                                        onclick="document.getElementById('rejectModal-{{ $requisition->id }}').classList.add('hidden')"
                                                        class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-red-600 text-white rounded">Rejeitar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nenhuma requisição encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">
            {{ $requisitions->links() }}
        </div>
    </div>
@endsection