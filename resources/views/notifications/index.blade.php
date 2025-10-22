@extends('layouts.app')

@section('title', 'Notificações')
@section('page-title', 'Notificações')
@section('page-subtitle', 'Gerir suas notificações')

@section('content')
<div class="grid grid-max-w-4xl mx-auto">
    <!-- Header com Filtros e Ações -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Estatísticas -->
            <div class="flex items-center space-x-6">
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $notifications->total() }}</p>
                    <p class="text-sm text-gray-500">Total</p>
                </div>
                <div class="h-12 w-px bg-gray-200"></div>
                <div>
                    <p class="text-2xl font-bold text-blue-600">{{ $unreadCount }}</p>
                    <p class="text-sm text-gray-500">Não lidas</p>
                </div>
            </div>

            <!-- Ações -->
            <div class="flex flex-wrap gap-2">
                @if($unreadCount > 0)
                    <form action="{{ route('notifications.read') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                            <i class="bi bi-check-all mr-1"></i> Marcar todas como lidas
                        </button>
                    </form>
                @endif

                <form action="{{ route('notifications.clear-read') }}" method="POST" class="inline"
                    onsubmit="return confirm('Deseja remover todas as notificações lidas?')">
                    @csrf
                    <button type="submit" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                        <i class="bi bi-trash mr-1"></i> Limpar lidas
                    </button>
                </form>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mt-4 flex gap-2">
            <a href="{{ route('notifications.all', ['filter' => 'all']) }}" 
                class="px-4 py-2 rounded-lg text-sm transition {{ $filter === 'all' ? 'bg-blue-100 text-blue-700 font-semibold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Todas
            </a>
            <a href="{{ route('notifications.all', ['filter' => 'unread']) }}" 
                class="px-4 py-2 rounded-lg text-sm transition {{ $filter === 'unread' ? 'bg-blue-100 text-blue-700 font-semibold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Não lidas
            </a>
            <a href="{{ route('notifications.all', ['filter' => 'read']) }}" 
                class="px-4 py-2 rounded-lg text-sm transition {{ $filter === 'read' ? 'bg-blue-100 text-blue-700 font-semibold' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Lidas
            </a>
        </div>
    </div>

    <!-- Lista de Notificações -->
    @if($notifications->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="bi bi-bell-slash text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhuma notificação</h3>
            <p class="text-gray-500">
                @if($filter === 'unread')
                    Você não tem notificações não lidas.
                @elseif($filter === 'read')
                    Você não tem notificações lidas.
                @else
                    Você ainda não recebeu nenhuma notificação.
                @endif
            </p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all {{ $notification->read_at ? 'opacity-75' : '' }}">
                    <div class="p-4 flex items-start gap-4">
                        <!-- Ícone por tipo -->
                        <div class="flex-shrink-0 mt-1">
                            @php
                                $type = $notification->data['type'] ?? 'general';
                                $iconClass = match($type) {
                                    'contribution_verified' => 'bi-check-circle-fill text-green-500',
                                    'contribution_rejected' => 'bi-x-circle-fill text-red-500',
                                    'contribution_created' => 'bi-cash-coin text-blue-500',
                                    'member_created' => 'bi-person-plus-fill text-purple-500',
                                    'commitment_chosen' => 'bi-handshake-fill text-indigo-500',
                                    'commitment_expiring' => 'bi-clock-fill text-yellow-500',
                                    'pending_contributions' => 'bi-exclamation-triangle-fill text-orange-500',
                                    'user_promoted' => 'bi-star-fill text-yellow-400',
                                    default => 'bi-bell-fill text-gray-500',
                                };
                            @endphp
                            <i class="bi {{ $iconClass }} text-2xl"></i>
                        </div>

                        <!-- Conteúdo -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-1">
                                        {{ $notification->data['title'] ?? 'Notificação' }}
                                        @if(!$notification->read_at)
                                            <span class="ml-2 inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ $notification->data['message'] ?? 'Sem descrição' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        <i class="bi bi-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <!-- Ações -->
                                <div class="flex gap-1">
                                    @if(!$notification->read_at)
                                        <a href="{{ route('notifications.mark-read', $notification->id) }}" 
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition"
                                            title="Marcar como lida">
                                            <i class="bi bi-check"></i>
                                        </a>
                                    @endif

                                    <form action="{{ route('notifications.destroy', $notification->id) }}" 
                                        method="POST" class="inline"
                                        onsubmit="return confirm('Deseja remover esta notificação?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition"
                                            title="Remover">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Link de ação -->
                            @if(isset($notification->data['link']) && $notification->data['link'] !== '#')
                                <a href="{{ route('notifications.mark-read', $notification->id) }}" 
                                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mt-2">
                                    Ver detalhes <i class="bi bi-arrow-right ml-1"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection