@extends('layouts.app')

@section('title', 'Detalhes do Membro')
@section('page-title', $member->name)
@section('page-subtitle', 'Informações completas do membro')

@section('content')
<div class="grid grid-max-w-6xl mx-auto">
    <!-- Header com ações -->
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('members.index') }}" 
            class="text-blue-600 hover:text-blue-800 transition font-medium">
            <i class="bi bi-arrow-left mr-2"></i>Voltar para lista
        </a>
        
        <div class="flex gap-3">
            <a href="{{ route('members.edit', $member) }}" 
                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                <i class="bi bi-pencil mr-2"></i>Editar
            </a>
            <a href="{{ route('contributions.create', ['user_id' => $member->id]) }}" 
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="bi bi-plus-circle mr-2"></i>Nova Contribuição
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Esquerda - Informações Básicas -->
        <div class="lg:col-span-1">
            <!-- Card de Perfil -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-blue-600 font-bold text-4xl">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 text-center mb-1">{{ $member->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">Membro</p>
                    
                    @if($member->is_active)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            <i class="bi bi-check-circle mr-1"></i>Ativo
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                            <i class="bi bi-x-circle mr-1"></i>Inativo
                        </span>
                    @endif
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex items-start">
                        <i class="bi bi-envelope text-gray-400 mt-1 mr-3"></i>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm text-gray-800">{{ $member->email }}</p>
                        </div>
                    </div>

                    @if($member->phone)
                        <div class="flex items-start">
                            <i class="bi bi-telephone text-gray-400 mt-1 mr-3"></i>
                            <div>
                                <p class="text-xs text-gray-500">Telefone</p>
                                <p class="text-sm text-gray-800">{{ $member->phone }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-start">
                        <i class="bi bi-calendar text-gray-400 mt-1 mr-3"></i>
                        <div>
                            <p class="text-xs text-gray-500">Membro desde</p>
                            <p class="text-sm text-gray-800">{{ $member->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hierarquia -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="font-bold text-gray-800 mb-4 flex items-center">
                    <i class="bi bi-diagram-3 mr-2"></i>Hierarquia
                </h4>

                @if($member->cell)
                    <div class="space-y-3">
                        <!-- Célula -->
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Célula</p>
                            <p class="font-semibold text-blue-800">
                                <i class="bi bi-people mr-2"></i>{{ $member->cell->name }}
                            </p>
                        </div>

                        <!-- Supervisão -->
                        @if($member->cell->supervision)
                            <div class="p-3 bg-purple-50 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Supervisão</p>
                                <p class="font-semibold text-purple-800">
                                    <i class="bi bi-diagram-3 mr-2"></i>{{ $member->cell->supervision->name }}
                                </p>
                            </div>
                        @endif

                        <!-- Zona -->
                        @if($member->cell->supervision && $member->cell->supervision->zone)
                            <div class="p-3 bg-green-50 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Zona</p>
                                <p class="font-semibold text-green-800">
                                    <i class="bi bi-map mr-2"></i>{{ $member->cell->supervision->zone->name }}
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">Sem célula associada</p>
                @endif
            </div>
        </div>

        <!-- Coluna Direita - Detalhes -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Compromissos -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h4 class="font-bold text-gray-800 flex items-center">
                        <i class="bi bi-handshake mr-2"></i>Compromissos
                    </h4>
                </div>
                <div class="p-6">
                    @if($member->commitments->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($member->commitments as $commitment)
                                <div class="p-4 {{ $commitment->end_date === null ? 'bg-green-50 border-l-4 border-green-500' : 'bg-gray-50' }} rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold text-gray-800">
                                                {{ $commitment->package->name ?? 'Pacote Desconhecido' }}
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Início: {{ $commitment->start_date->format('d/m/Y') }}
                                                @if($commitment->end_date)
                                                    <br>Fim: {{ $commitment->end_date->format('d/m/Y') }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $commitment->end_date === null ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                            {{ $commitment->end_date === null ? 'Ativo' : 'Encerrado' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            <i class="bi bi-inbox text-4xl text-gray-300 block mb-2"></i>
                            Nenhum compromisso registrado
                        </p>
                    @endif
                </div>
            </div>

            <!-- Contribuições Recentes -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800 flex items-center">
                        <i class="bi bi-cash-coin mr-2"></i>Contribuições Recentes
                    </h4>
                    <a href="{{ route('contributions.index', ['user_id' => $member->id]) }}" 
                        class="text-sm text-blue-600 hover:text-blue-800">
                        Ver todas <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-6">
                    @if($member->contributions->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($member->contributions->take(5) as $contribution)
                                <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg transition">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ number_format($contribution->amount, 2, ',', '.') }} MT
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $contribution->contribution_date->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $contribution->status === 'verificada' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $contribution->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $contribution->status === 'rejeitada' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($contribution->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Estatísticas -->
                        <div class="mt-6 pt-6 border-t grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-blue-600">
                                    {{ $member->contributions->count() }}
                                </p>
                                <p class="text-xs text-gray-500">Total de Contribuições</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">
                                    {{ number_format($member->contributions->where('status', 'verificada')->sum('amount'), 2, ',', '.') }} MT
                                </p>
                                <p class="text-xs text-gray-500">Total Verificado</p>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            <i class="bi bi-inbox text-4xl text-gray-300 block mb-2"></i>
                            Nenhuma contribuição registrada
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection