@extends('layouts.app')

@section('title', 'Notificações - Portal Life Church')
@section('page-title', 'Notificações')
@section('page-subtitle', 'Alertas e atualizações do sistema')

@section('header-actions')
    <div class="flex items-center gap-2">
        @if($unreadCount > 0)
            <form action="{{ route('notifications.read') }}" method="POST">
                @csrf
                <button type="submit" class="action-icon text-gray-600 hover:text-blue-600 hover:bg-blue-50" title="Marcar todas como lidas">
                    <i class="bi bi-check-all"></i>
                </button>
            </form>
        @endif
        <form action="{{ route('notifications.clear-read') }}" method="POST" id="clear-read-form">
            @csrf
            <button type="button"
                onclick="if(confirm('Deseja remover todas as notificações lidas?')) document.getElementById('clear-read-form').submit();"
                class="action-icon text-gray-600 hover:text-red-600 hover:bg-red-50" title="Limpar lidas">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-5">
    <!-- Header & Summary Card -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Central de Alertas</span>
                <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold text-orange-800">
                    {{ $notifications->total() }} total
                </span>
                @if($unreadCount > 0)
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-800">
                        {{ $unreadCount }} não lidas
                    </span>
                @endif
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900 mt-0.5">Notificações do Sistema</h1>
            <p class="text-xs text-gray-500 mt-0.5">Alertas operacionais, validações de pacotes e avisos ministeriais.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button" id="bulkDeleteBtn" onclick="bulkDelete()" disabled
                class="hidden inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100 transition">
                <i class="bi bi-trash"></i>
                <span>Remover Selecionadas</span>
            </button>

            @if($unreadCount > 0)
                <form action="{{ route('notifications.read') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                        <i class="bi bi-check-all text-blue-600"></i>
                        <span>Marcar Todas Lidas</span>
                    </button>
                </form>
            @endif

            <form action="{{ route('notifications.clear-read') }}" method="POST" id="clear-read-form-btn">
                @csrf
                <button type="button"
                    onclick="if(confirm('Deseja remover todas as notificações lidas?')) document.getElementById('clear-read-form-btn').submit();"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition">
                    <i class="bi bi-trash"></i>
                    <span>Limpar Lidas</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-1 border-b border-gray-200 pb-2">
        <a href="{{ route('notifications.all', ['filter' => 'all']) }}"
            class="rounded-lg px-3 py-1.5 text-xs font-bold transition {{ $filter === 'all' ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            Todas ({{ $notifications->total() }})
        </a>
        <a href="{{ route('notifications.all', ['filter' => 'unread']) }}"
            class="rounded-lg px-3 py-1.5 text-xs font-bold transition {{ $filter === 'unread' ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            Não lidas ({{ $unreadCount }})
        </a>
        <a href="{{ route('notifications.all', ['filter' => 'read']) }}"
            class="rounded-lg px-3 py-1.5 text-xs font-bold transition {{ $filter === 'read' ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            Lidas
        </a>
    </div>

    <!-- Notifications List -->
    <form id="bulkActionForm" method="POST">
        @csrf
        @if($notifications->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-12 text-center text-xs text-gray-500">
                <i class="bi bi-bell-slash text-3xl text-gray-300 block mb-2"></i>
                <span class="font-bold">
                    @if($filter === 'unread') Tudo em dia! Você visualizou todas as notificações recentes.
                    @elseif($filter === 'read') Você ainda não possui histórico de alertas visualizados.
                    @else Não há nenhum alerta ou mensagem no sistema no momento. @endif
                </span>
            </div>
        @else
            <div class="space-y-2">
                @foreach($notifications as $notification)
                    @php
                        $type = $notification->data['type'] ?? 'general';
                        $config = match ($type) {
                            'contribution_verified' => ['icon' => 'bi-check-circle-fill', 'color' => 'bg-emerald-50 text-emerald-700', 'label' => 'Confirmada', 'badge' => 'bg-emerald-100 text-emerald-800'],
                            'contribution_rejected' => ['icon' => 'bi-x-circle-fill', 'color' => 'bg-rose-50 text-rose-700', 'label' => 'Rejeitada', 'badge' => 'bg-rose-100 text-rose-800'],
                            'contribution_created' => ['icon' => 'bi-cash-coin', 'color' => 'bg-blue-50 text-blue-700', 'label' => 'Registo', 'badge' => 'bg-blue-100 text-blue-800'],
                            'contribution_pending_validation' => ['icon' => 'bi-exclamation-triangle-fill', 'color' => 'bg-amber-50 text-amber-700', 'label' => 'Validação', 'badge' => 'bg-amber-100 text-amber-800'],
                            'contribution_verified_manager' => ['icon' => 'bi-check-circle-fill', 'color' => 'bg-emerald-50 text-emerald-700', 'label' => 'Pacote', 'badge' => 'bg-emerald-100 text-emerald-800'],
                            'contribution_rejected_manager' => ['icon' => 'bi-x-circle-fill', 'color' => 'bg-rose-50 text-rose-700', 'label' => 'Pacote', 'badge' => 'bg-rose-100 text-rose-800'],
                            'member_created' => ['icon' => 'bi-person-plus-fill', 'color' => 'bg-purple-50 text-purple-700', 'label' => 'Conta', 'badge' => 'bg-purple-100 text-purple-800'],
                            'member_added_to_cell' => ['icon' => 'bi-people-fill', 'color' => 'bg-sky-50 text-sky-700', 'label' => 'Célula', 'badge' => 'bg-sky-100 text-sky-800'],
                            'commitment_chosen' => ['icon' => 'bi-handshake-fill', 'color' => 'bg-indigo-50 text-indigo-700', 'label' => 'Compromisso', 'badge' => 'bg-indigo-100 text-indigo-800'],
                            'commitment_expiring' => ['icon' => 'bi-clock-fill', 'color' => 'bg-amber-50 text-amber-700', 'label' => 'Prazo', 'badge' => 'bg-amber-100 text-amber-800'],
                            'pending_contributions' => ['icon' => 'bi-exclamation-triangle-fill', 'color' => 'bg-orange-50 text-orange-700', 'label' => 'Comissão', 'badge' => 'bg-orange-100 text-orange-800'],
                            'user_promoted' => ['icon' => 'bi-star-fill', 'color' => 'bg-amber-50 text-amber-700', 'label' => 'Cargo', 'badge' => 'bg-amber-100 text-amber-800'],
                            default => ['icon' => 'bi-bell-fill', 'color' => 'bg-gray-100 text-gray-700', 'label' => 'Sistema', 'badge' => 'bg-gray-100 text-gray-700'],
                        };
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-white p-3.5 shadow-sm hover:shadow-md transition flex items-center gap-3 {{ $notification->read_at ? 'opacity-60 bg-gray-50/50' : '' }}">
                        <div class="flex-shrink-0">
                            <input type="checkbox" name="notification_ids[]" value="{{ $notification->id }}"
                                class="notification-checkbox rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                        </div>

                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $config['color'] }} text-sm font-bold">
                                <i class="bi {{ $config['icon'] }}"></i>
                            </span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-gray-900 truncate">
                                    {{ $notification->data['title'] ?? 'Notificação' }}
                                </h4>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold {{ $config['badge'] }}">
                                    {{ $config['label'] }}
                                </span>
                                @if(!$notification->read_at)
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 line-clamp-1 mt-0.5">
                                {{ $notification->data['message'] ?? 'Sem descrição vinculada' }}
                            </p>
                            <div class="flex items-center gap-3 text-[10px] text-gray-400 mt-1">
                                <span><i class="bi bi-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}</span>
                                @if(isset($notification->data['link']) && $notification->data['link'] !== '#')
                                    <a href="{{ route('notifications.mark-read', $notification->id) }}"
                                        class="font-bold text-orange-700 hover:underline inline-flex items-center gap-0.5">
                                        <span>Ver Detalhes</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-1 shrink-0">
                            @if(!$notification->read_at)
                                <a href="{{ route('notifications.mark-read', $notification->id) }}"
                                    class="rounded p-1 text-gray-400 hover:text-blue-600 hover:bg-gray-100 transition" title="Marcar como lido">
                                    <i class="bi bi-check-lg"></i>
                                </a>
                            @endif
                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Tem certeza que deseja excluir esta notificação?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded p-1 text-gray-400 hover:text-red-600 hover:bg-gray-100 transition" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif
    </form>
</div>

<script>
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    const bulkBtn = document.getElementById('bulkDeleteBtn');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBtn);
    });

    function updateBulkBtn() {
        const count = document.querySelectorAll('.notification-checkbox:checked').length;
        if (count > 0) {
            bulkBtn.disabled = false;
            bulkBtn.classList.remove('hidden');
            bulkBtn.innerHTML = `<i class="bi bi-trash mr-1"></i> Remover (${count})`;
        } else {
            bulkBtn.disabled = true;
            bulkBtn.classList.add('hidden');
        }
    }

    function bulkDelete() {
        if (confirm('Deseja remover as notificações selecionadas?')) {
            const form = document.getElementById('bulkActionForm');
            form.action = "{{ route('notifications.bulk-delete') }}";
            form.submit();
        }
    }
</script>
@endsection
