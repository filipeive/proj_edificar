@extends('layouts.app')

@section('title', 'Meus Compromissos - Projeto Edificar')
@section('page-title', 'Pacotes de Compromisso')
@section('page-subtitle', 'Escolha seu nível de compromisso mensal')

@section('content')
{{-- errors --}}

@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <h3 class="font-bold text-red-800 mb-2">
            <i class="bi bi-exclamation-triangle-fill mr-2"></i>Ocorreram alguns erros:
        </h3>
        <ul class="list-disc list-inside text-red-700 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
    <h3 class="font-bold text-blue-800 mb-2">
        <i class="bi bi-info-circle mr-2"></i>Como Funciona?
    </h3>
    <p class="text-blue-700 text-sm">
        Escolha um pacote de compromisso mensal. Você pode contribuir qualquer valor dentro do intervalo do seu pacote. 
        Pode alterar seu pacote a qualquer momento.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
    @foreach($packages as $package)
    <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition @if($currentCommitment && $currentCommitment->package_id === $package->id) ring-2 ring-blue-500 @endif">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $package->name }}</h3>
            <div class="mb-4">
                <p class="text-3xl font-bold text-blue-600">
                    {{ number_format($package->min_amount, 0) }}
                </p>
                <p class="text-sm text-gray-500">
                    até 
                    @if($package->max_amount)
                        {{ number_format($package->max_amount, 0) }}
                    @else
                        ∞
                    @endif
                    MT
                </p>
            </div>
            <p class="text-xs text-gray-600 mb-6">{{ $package->description }}</p>
            @php($isActive = $currentCommitment && $currentCommitment->package_id === $package->id)

            <form action="{{ route('commitments.choose') }}" method="POST" class="w-full">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}">
                <button
                    type="submit"
                    class="w-full px-4 py-2 rounded transition text-sm font-medium {{ $isActive ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white' }}"
                >
                    @if($isActive)
                        <span class="inline-flex items-center">
                            <i class="bi bi-check-lg mr-2"></i> Ativo
                        </span>
                    @else
                        Escolher
                    @endif
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection