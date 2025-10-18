@extends('layouts.app')

@section('title', 'Relatório de Supervisão - Projeto Edificar')
@section('page-title', 'Relatório da Supervisão')
@section('page-subtitle', 'Dados agregados da supervisão de ' . ($supervision->name ?? 'N/A'))

@section('content')
    <div class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6 border-b-4 border-blue-500">
                <p class="text-sm font-medium text-gray-500">Nº de Células</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalCells }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 border-b-4 border-purple-500">
                <p class="text-sm font-medium text-gray-500">Total Comprometido</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalCommitted, 2, ',', '.') }} MT</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 border-b-4 border-green-500">
                <p class="text-sm font-medium text-gray-500">Contribuições Verificadas</p>
                @php
                    $totalVerified = $cellsData->sum('verified');
                @endphp
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalVerified, 2, ',', '.') }} MT</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 border-b-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-500">Contribuições Pendentes</p>
                @php
                    $totalPending = $cellsData->sum('pending');
                @endphp
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalPending, 2, ',', '.') }} MT</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Performance Detalhada por Célula</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Célula</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Líder</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Comprometido (MT)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Verificado (MT)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pendente (MT)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Taxa de Cumprimento</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($cellsData as $cell)
                        @php
                            $fulfillmentRate = $cell['committed'] > 0 ? ($cell['verified'] / $cell['committed']) * 100 : ($cell['verified'] > 0 ? 100 : 0);
                            $rateColor = $fulfillmentRate >= 100 ? 'text-green-600' : ($fulfillmentRate >= 80 ? 'text-orange-600' : 'text-red-600');
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cell['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cell['lider'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-800">{{ number_format($cell['committed'], 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-700 font-semibold">{{ number_format($cell['verified'], 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-yellow-700">{{ number_format($cell['pending'], 2, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $rateColor }}">
                                {{ round($fulfillmentRate, 1) }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhuma célula encontrada nesta supervisão.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden p-6">
             <h2 class="text-xl font-semibold text-gray-800 mb-4">Contribuições Agregadas por Mês</h2>
             <div class="max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mês/Ano</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Verificado (MT)</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pendente (MT)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        {{-- Agrupar por Mês/Ano e status --}}
                        @php
                            $monthlyData = $contributions->groupBy(function ($item) {
                                return $item->year . '-' . $item->month;
                            });
                        @endphp
                        
                        @foreach($monthlyData as $key => $data)
                            @php
                                $date = \Carbon\Carbon::createFromDate($data->first()->year, $data->first()->month, 1);
                                $verified = $data->where('status', 'verificada')->sum('total_amount');
                                $pending = $data->where('status', 'pendente')->sum('total_amount');
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $date->format('F Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-700">{{ number_format($verified, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-yellow-700">{{ number_format($pending, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
             </div>
        </div>
    </div>
@endsection