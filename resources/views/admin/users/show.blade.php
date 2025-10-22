@extends('layouts.app')

@section('title', 'Detalhes do Utilizador - Projeto Edificar')
@section('page-title', 'Detalhes do Utilizador')
@section('page-subtitle', 'Informações completas do utilizador')

@section('content')
<div class="grid grid mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800">Utilizadores</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600">{{ $user->name }}</li>
        </ol>
    </nav>

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-3xl">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="text-white">
                    <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                    <p class="text-blue-100 mt-1">{{ $user->email }}</p>
                    <div class="flex items-center space-x-3 mt-2">
                        @switch($user->role)
                            @case('admin')
                                <span class="inline-flex items-center px-3 py-1 bg-red-500 text-white rounded-full text-xs font-medium">
                                    <i class="bi bi-shield-check mr-1"></i>Administrador
                                </span>
                                @break
                            @case('pastor_zona')
                                <span class="inline-flex items-center px-3 py-1 bg-purple-500 text-white rounded-full text-xs font-medium">
                                    <i class="bi bi-person-fill-gear mr-1"></i>Pastor de Zona
                                </span>
                                @break
                            @case('supervisor')
                                <span class="inline-flex items-center px-3 py-1 bg-orange-500 text-white rounded-full text-xs font-medium">
                                    <i class="bi bi-diagram-3 mr-1"></i>Supervisor
                                </span>
                                @break
                            @case('lider_celula')
                                <span class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-full text-xs font-medium">
                                    <i class="bi bi-people-fill mr-1"></i>Líder de Célula
                                </span>
                                @break
                            @default
                                <span class="inline-flex items-center px-3 py-1 bg-gray-500 text-white rounded-full text-xs font-medium">
                                    <i class="bi bi-person-circle mr-1"></i>Membro
                                </span>
                        @endswitch

                        @if($user->is_active)
                            <span class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-full text-xs font-medium">
                                <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                                Ativo
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 bg-gray-500 text-white rounded-full text-xs font-medium">
                                <span class="w-2 h-2 bg-white rounded-full mr-2"></span>
                                Inativo
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('users.edit', $user) }}" 
                    class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition font-medium">
                    <i class="bi bi-pencil mr-2"></i>
                    Editar
                </a>
                @if($user->role !== 'admin')
                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                    onsubmit="return confirm('Tem certeza que deseja deletar este utilizador?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-medium">
                        <i class="bi bi-trash mr-2"></i>
                        Deletar
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informações Pessoais -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="bi bi-person-badge text-blue-600 mr-2"></i>
                        Informações Pessoais
                    </h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome Completo</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $user->phone ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Papel</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Célula</dt>
                            <dd class="mt-1">
                                @if($user->cell)
                                    <a href="{{ route('cells.show', $user->cell) }}" 
                                        class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="bi bi-geo-alt-fill mr-1"></i>
                                        {{ $user->cell->name }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400">Sem célula</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($user->is_active)
                                    <span class="inline-flex items-center text-sm font-semibold text-green-600">
                                        <i class="bi bi-check-circle-fill mr-1"></i>
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-sm font-semibold text-gray-600">
                                        <i class="bi bi-x-circle-fill mr-1"></i>
                                        Inativo
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Compromissos -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="bi bi-handshake text-green-600 mr-2"></i>
                        Compromissos Ativos
                    </h2>
                    <a href="{{ route('commitments.index') }}" 
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Ver todos <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-6">
                    @if($user->commitments && $user->commitments->count() > 0)
                        <div class="space-y-4">
                            @foreach($user->commitments->whereNull('end_date')->take(1) as $commitment)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $commitment->package->name ?? 'Pacote Padrão' }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Comprometido: {{ number_format($commitment->committed_amount, 2, ',', '.') }} MT
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-green-600">{{ number_format($commitment->committed_amount, 2, ',', '.') }} MT</p>
                                    <p class="text-xs text-gray-500">por mês</p>
                                </div>
                            </div>
                            @endforeach
                            @if($user->commitments->whereNull('end_date')->isEmpty())
                                <div class="text-center py-8">
                                    <i class="bi bi-inbox text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">Nenhum compromisso ativo</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="bi bi-inbox text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Nenhum compromisso registado</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contribuições Recentes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="bi bi-cash-coin text-yellow-600 mr-2"></i>
                        Contribuições Recentes
                    </h2>
                    <a href="{{ route('contributions.index', ['mine' => 1]) }}" 
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Ver todas <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-6">
                    @if($user->contributions && $user->contributions->count() > 0)
                        <div class="space-y-3">
                            @foreach($user->contributions->take(5) as $contribution)
                            <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="bi bi-cash text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ number_format($contribution->amount, 2, ',', '.') }} MT</p>
                                        <p class="text-xs text-gray-500">{{ $contribution->contribution_date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    @if($contribution->status == 'verificada') bg-green-100 text-green-800
                                    @elseif($contribution->status == 'pendente') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($contribution->status) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="bi bi-inbox text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Nenhuma contribuição registada</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Coluna Lateral -->
        <div class="space-y-6">
            <!-- Estatísticas -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="bi bi-bar-chart text-purple-600 mr-2"></i>
                        Estatísticas
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-600">Total Contribuições</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $user->contributions->count() }}</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="bi bi-hash text-blue-600 text-xl"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-600">Total Contribuído (Verificado)</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ number_format($user->contributions->where('status', 'verificada')->sum('amount'), 2, ',', '.') }} MT
                            </p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="bi bi-currency-dollar text-green-600 text-xl"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-600">Compromissos Registados</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $user->commitments->count() }}</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <i class="bi bi-handshake text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="bi bi-info-circle text-gray-600 mr-2"></i>
                        Informações do Sistema
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">ID do Utilizador</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">#{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Data de Registo</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            {{ $user->created_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $user->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Última Atualização</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            {{ $user->updated_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $user->updated_at->diffForHumans() }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Último Acesso</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->format('d/m/Y H:i') }}
                            @else
                                Nunca acessou
                            @endif
                        </p>
                        @if($user->last_login_at)
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $user->last_login_at->diffForHumans() }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="bi bi-lightning text-yellow-600 mr-2"></i>
                        Ações Rápidas
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('contributions.create', ['user_id' => $user->id]) }}" 
                        class="flex items-center justify-between p-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition font-medium">
                        <span class="flex items-center">
                            <i class="bi bi-plus-circle mr-2"></i>
                            Nova Contribuição
                        </span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    
                   {{--  <a href="{{ route('users.commitment.set', ['user' => $user->id]) }}" 
                        class="flex items-center justify-between p-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition font-medium">
                        <span class="flex items-center">
                            <i class="bi bi-handshake mr-2"></i>
                            Novo Compromisso
                        </span>
                        <i class="bi bi-arrow-right"></i>
                    </a> --}}
                    
                    <button onclick="resetPassword({{ $user->id }})" 
                        class="w-full flex items-center justify-between p-3 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition font-medium">
                        <span class="flex items-center">
                            <i class="bi bi-key mr-2"></i>
                            Redefinir Senha
                        </span>
                        <i class="bi bi-arrow-right"></i>
                    </button>

                    <a href="mailto:{{ $user->email }}" 
                        class="flex items-center justify-between p-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition font-medium">
                        <span class="flex items-center">
                            <i class="bi bi-envelope mr-2"></i>
                            Enviar Email
                        </span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão Voltar -->
    <div class="mt-6">
        <a href="{{ route('users.index') }}" 
            class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
            <i class="bi bi-arrow-left mr-2"></i>
            Voltar à Lista de Utilizadores
        </a>
    </div>
</div>

<script>
function resetPassword(userId) {
    if (confirm('Tem certeza que deseja redefinir a senha deste utilizador? Um email será enviado com as novas credenciais.')) {
        // Implementar lógica de redefinição de senha via AJAX
        fetch(`/api/users/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Senha redefinida com sucesso! Um email foi enviado ao utilizador.');
            } else {
                alert('Erro ao redefinir senha. Tente novamente.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao redefinir senha. Tente novamente.');
        });
    }
}
</script>
@endsection