@extends('layouts.app')

@section('title', 'Gestão de Despesas')
@section('page-title', 'Despesas e Saídas')
@section('page-subtitle', 'Histórico de todas as saídas financeiras')

@section('content')
    <div
        class="mb-6 bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-xl font-black text-gray-900 tracking-tight">Total de Despesas</h2>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Soma de todas as saídas no sistema</p>
        </div>
        <div class="text-3xl font-black text-red-600 tracking-tighter">
            {{ number_format($totalExpenses, 2, ',', '.') }} MT
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div
            class="p-6 md:p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Fluxo de Saídas</h3>

            <!-- Modal Button for Direct Expense -->
            <button onclick="document.getElementById('newExpenseModal').classList.remove('hidden')"
                class="bg-red-600 text-white px-6 py-3 rounded-2xl hover:bg-red-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-red-200">
                <i class="bi bi-dash-circle mr-2"></i> Nova Despesa
            </button>
        </div>

        <!-- Mobile Grid View -->
        <div class="grid grid-cols-1 gap-4 md:hidden p-4 bg-gray-50/50">
            @forelse($expenses as $expense)
                <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-gray-100 relative">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span
                                class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-lg mb-2">
                                {{ $expense->category }}
                            </span>
                            <h4 class="font-bold text-gray-900 leading-tight">{{ $expense->description }}</h4>
                        </div>
                        <span class="text-red-600 font-black text-lg">-{{ number_format($expense->amount, 2, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs text-gray-500 mt-3 pt-3 border-t border-gray-50">
                        <span>{{ $expense->date->format('d/m/Y') }}</span>
                        <span>{{ $expense->user->name }}</span>
                    </div>

                    <div class="mt-2 flex items-center justify-between">
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest {{ $expense->scope == 'edificar' ? 'text-orange-500' : 'text-blue-500' }}">
                            {{ ucfirst($expense->scope ?? 'eclesiastico') }}
                        </span>
                        @if($expense->requisition_id)
                            <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-1 rounded-lg font-bold">Req
                                #{{ $expense->requisition_id }}</span>
                        @else
                            <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded-lg font-bold">Direta</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 font-bold text-sm">Nenhuma despesa registada.</div>
            @endforelse
            <div class="mt-4">
                {{ $expenses->links() }}
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Descrição</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Categoria</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Âmbito</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Responsável
                        </th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Origem</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                            Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($expenses as $expense)
                        <tr class="bg-white hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-600">{{ $expense->date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $expense->description }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 bg-gray-100 rounded-full text-xs font-bold">{{ $expense->category }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs font-bold uppercase {{ $expense->scope == 'edificar' ? 'text-orange-500' : 'text-blue-500' }}">
                                    {{ ucfirst($expense->scope ?? 'eclesiastico') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium">{{ $expense->user->name }}</td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-500">
                                @if($expense->requisition_id)
                                    <a href="#" class="text-blue-600 hover:text-blue-800 transition">Req
                                        #{{ $expense->requisition_id }}</a>
                                @else
                                    Direta
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-black text-red-600">
                                - {{ number_format($expense->amount, 2, ',', '.') }} MT
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400 font-bold uppercase tracking-widest">
                                Nenhuma despesa registada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-6 border-t border-gray-50">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Nova Despesa Direta -->
    <div id="newExpenseModal"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg shadow-2xl rounded-[2rem] bg-white overflow-hidden">
            <div class="p-6 md:p-8 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-black text-gray-900 tracking-tight">Registar Despesa Direta</h3>
                <button onclick="document.getElementById('newExpenseModal').classList.add('hidden')"
                    class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <form action="{{ route('expenses.store') }}" method="POST" class="p-6 md:p-8 space-y-5">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Descrição</label>
                    <input type="text" name="description" required
                        class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 rounded-2xl transition-all font-bold text-gray-900">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Valor
                            (MT)</label>
                        <input type="number" name="amount" step="0.01" required
                            class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 rounded-2xl transition-all font-bold text-gray-900">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Data</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                            class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 rounded-2xl transition-all font-bold text-gray-900">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Categoria</label>
                        <select name="category" required
                            class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 rounded-2xl transition-all font-bold text-gray-900 appearance-none custom-select">
                            <option value="Operacional">Custos Operacionais</option>
                            <option value="Material">Material de Consumo</option>
                            <option value="Manutenção">Manutenção e Reparos</option>
                            <option value="Eventos">Eventos</option>
                            <option value="Taxas">Taxas e Serviços</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Âmbito</label>
                        <select name="scope" required
                            class="w-full px-5 py-3 bg-gray-50 border-transparent focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 rounded-2xl transition-all font-bold text-gray-900 appearance-none custom-select">
                            <option value="eclesiastico">Eclesiástico (Igreja)</option>
                            <option value="edificar">Projeto Edificar</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('newExpenseModal').classList.add('hidden')"
                        class="px-6 py-3 border border-gray-200 rounded-2xl text-gray-600 font-bold hover:bg-gray-50 transition-colors">Cancelar</button>
                    <button type="submit"
                        class="px-8 py-3 bg-red-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-200">
                        Registar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection