@extends('layouts.app')

@section('title', 'Dashboard Líder - Projeto Edificar')
@section('page-title', 'Dashboard da Célula')
@section('page-subtitle', 'Monitorize as contribuições de sua célula')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Célula Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">CÉLULA</h3>
        <p class="text-2xl font-bold text-gray-800">{{ $cellName }}</p>
        <p class="text-sm text-gray-500 mt-2">Total de membros: {{ $members->count() }}</p>
    </div>

    <!-- Total Arrecadado -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">TOTAL ESTE MÊS</h3>
        <p class="text-3xl font-bold text-green-600">{{ number_format($total, 2, ',', '.') }} MT</p>
    </div>

    <!-- Taxa Participação -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">TAXA PARTICIPAÇÃO</h3>
        <p class="text-3xl font-bold text-blue-600">
            {{ $members->count() > 0 ? round(($members->where('status', 'Contribuiu')->count() / $members->count()) * 100, 1) : 0 }}%
        </p>
    </div>
</div>

<!-- Membros da Célula -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-800">Membros da Célula</h3>
        <a href="{{ route('contributions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
            + Registar Contribuição
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contribuição</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $member)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $member['name'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $member['email'] }}</td>
                    <td class="px-6 py-4 text-sm font-medium">{{ number_format($member['total'], 2, ',', '.') }} MT</td>
                    <td class="px-6 py-4 text-sm">
                        @if($member['status'] === 'Contribuiu')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                ✓ Contribuiu
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                ⚠ Faltoso
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection