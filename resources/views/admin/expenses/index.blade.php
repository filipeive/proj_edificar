@extends('layouts.app')

@section('title', 'Gestão de Despesas')
@section('page-title', 'Despesas e Saídas')
@section('page-subtitle', 'Histórico de todas as saídas financeiras')

@section('content')
    <div class="mb-6 bg-white rounded-lg shadow p-4 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Total de Despesas Registadas</h2>
            <p class="text-sm text-gray-500">Soma de todas as saídas no sistema</p>
        </div>
        <div class="text-3xl font-bold text-red-600">
            {{ number_format($totalExpenses, 2, ',', '.') }} MT
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Fluxo de Saídas</h3>

            <!-- Modal Button for Direct Expense -->
            <button onclick="document.getElementById('newExpenseModal').classList.remove('hidden')"
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                <i class="bi bi-dash-circle mr-1"></i> Registar Saída Direta
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Data</th>
                        <th class="px-6 py-3">Descrição</th>
                        <th class="px-6 py-3">Categoria</th>
                        <th class="px-6 py-3">Responsável</th>
                        <th class="px-6 py-3">Origem</th>
                        <th class="px-6 py-3 text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $expense->date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $expense->description }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $expense->category }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $expense->user->name }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                @if($expense->requisition_id)
                                    <a href="#" class="text-blue-600 hover:underline">Requisição #{{ $expense->requisition_id }}</a>
                                @else
                                    Direta
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-red-600">
                                - {{ number_format($expense->amount, 2, ',', '.') }} MT
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhuma despesa registada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">
            {{ $expenses->links() }}
        </div>
    </div>

    <!-- Modal Nova Despesa Direta -->
    <div id="newExpenseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Registar Despesa Direta</h3>
                <button onclick="document.getElementById('newExpenseModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-500">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descrição</label>
                        <input type="text" name="description" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Valor (MT)</label>
                            <input type="number" name="amount" step="0.01" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoria</label>
                        <select name="category" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            <option value="Operacional">Custos Operacionais</option>
                            <option value="Material">Material de Consumo</option>
                            <option value="Manutenção">Manutenção e Reparos</option>
                            <option value="Eventos">Eventos</option>
                            <option value="Taxas">Taxas e Serviços</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('newExpenseModal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">Registar
                        Despesa</button>
                </div>
            </form>
        </div>
    </div>
@endsection