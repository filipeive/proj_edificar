@extends('layouts.app')

@section('title', 'Notificações - Portal Life Church')

@section('content')
    <div class="space-y-8">
        <!-- Header & Stats Overview -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col lg:flex-row justify-between items-center gap-8">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Notificações</h1>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Alertas e Atualizações do
                        Sistema</p>
                </div>

                <div class="hidden md:flex items-center gap-6">
                    <div class="h-12 w-px bg-gray-100"></div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-black text-gray-900 leading-none">{{ $notifications->total() }}</span>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Total</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-black text-blue-600 leading-none">{{ $unreadCount }}</span>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Não Lidas</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($unreadCount > 0)
                    <form action="{{ route('notifications.read') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-blue-100">
                            <i class="bi bi-check-all text-lg mr-2"></i> Marcar todas lidas
                        </button>
                    </form>
                @endif
                <form action="{{ route('notifications.clear-read') }}" method="POST"
                    onsubmit="return confirm('Deseja remover todas as notificações lidas?')">
                    @csrf
                    <button type="submit"
                        class="bg-gray-100 text-gray-600 px-6 py-4 rounded-2xl hover:bg-gray-200 transition-all font-black text-xs uppercase tracking-widest flex items-center">
                        <i class="bi bi-trash text-lg mr-2"></i> Limpar lidas
                    </button>
                </form>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 bg-gray-100/50 p-1.5 rounded-2xl w-fit">
            <a href="{{ route('notifications.all', ['filter' => 'all']) }}"
                class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $filter === 'all' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                Todas
            </a>
            <a href="{{ route('notifications.all', ['filter' => 'unread']) }}"
                class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $filter === 'unread' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                Não lidas
            </a>
            <a href="{{ route('notifications.all', ['filter' => 'read']) }}"
                class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $filter === 'read' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-400 hover:text-gray-600' }}">
                Lidas
            </a>
        </div>

        <!-- Notifications List -->
        @if($notifications->isEmpty())
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-20 text-center">
                <div class="w-24 h-24 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-6">
                    <i class="bi bi-bell-slash text-4xl text-gray-200"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Sem notificações</h3>
                <p class="text-sm font-bold text-gray-400 max-w-xs mx-auto uppercase tracking-tighter">
                    @if($filter === 'unread') Tudo em dia! Você visualizou todos os alertas recentes.
                    @elseif($filter === 'read') Você ainda não possui histórico de alertas visualizados.
                    @else Não há nenhum alerta ou mensagem no sistema no momento. @endif
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($notifications as $notification)
                    <div
                        class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all group {{ $notification->read_at ? 'opacity-60 grayscale-[0.5]' : 'border-blue-100' }}">
                        <div class="flex flex-col md:flex-row md:items-center gap-6">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                @php
                                    $type = $notification->data['type'] ?? 'general';
                                    $config = match ($type) {
                                        'contribution_verified' => ['icon' => 'bi-check-circle-fill', 'color' => 'bg-green-50 text-green-600'],
                                        'contribution_rejected' => ['icon' => 'bi-x-circle-fill', 'color' => 'bg-red-50 text-red-600'],
                                        'contribution_created' => ['icon' => 'bi-cash-coin', 'color' => 'bg-blue-50 text-blue-600'],
                                        'member_created' => ['icon' => 'bi-person-plus-fill', 'color' => 'bg-purple-50 text-purple-600'],
                                        'commitment_chosen' => ['icon' => 'bi-handshake-fill', 'color' => 'bg-indigo-50 text-indigo-600'],
                                        'commitment_expiring' => ['icon' => 'bi-clock-fill', 'color' => 'bg-yellow-50 text-yellow-600'],
                                        'pending_contributions' => ['icon' => 'bi-exclamation-triangle-fill', 'color' => 'bg-orange-50 text-orange-600'],
                                        'user_promoted' => ['icon' => 'bi-star-fill', 'color' => 'bg-yellow-50 text-yellow-400'],
                                        default => ['icon' => 'bi-bell-fill', 'color' => 'bg-gray-50 text-gray-500'],
                                    };
                                @endphp
                     <div
                                    class="w-14 h-14 rounded-2xl {{ $config['color'] }} flex items-center justify-center text-2xl shadow-sm">
                                    <i class="bi {{ $config['icon'] }}"></i>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-sm font-black text-gray-900 uppercase tracking-tight">
                                        {{ $notification->data['title'] ?? 'Notificação' }}
                                    </h4>
                                    @if(!$notification->read_at)
                                        <span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-gray-500 line-clamp-2">
                                    {{ $notification->data['message'] ?? 'Sem descrição vinculada' }}
                                </p>
                                <div class="flex items-center gap-4 pt-1">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1">
                                        <i class="bi bi-clock"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    @if(isset($notification->data['link']) && $notification->data['link'] !== '#')
                                        <a href="{{ route('notifications.mark-read', $notification->id) }}"
                                            class="text-[10px] font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest flex items-center gap-1 group/link">
                                            Ver Detalhes
                                            <i class="bi bi-arrow-right transition-transform group-hover/link:translate-x-1"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div
                                class="flex md:flex-col items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all translate-x-2 group-hover:translate-x-0">
                                @if(!$notification->read_at)
                                    <a href="{{ route('notifications.mark-read', $notification->id) }}"
                                        class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm"
                                        title="Lido">
                                        <i class="bi bi-check-lg"></i>
                                    </a>
                                @endif

                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                                    onsubmit="return confirm('Deseja remover esta notificação?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all shadow-sm"
                                        title="Excluir">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection