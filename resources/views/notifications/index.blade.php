@extends('layouts.app')

@section('title', 'Notificações - Portal Life Church')
@section('page-title', 'Notificações')
@section('page-subtitle', 'Alertas e atualizações do sistema')

@section('content')
    <div class="space-y-8">
        <!-- Header & Stats Overview -->
        <div
            class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col lg:flex-row justify-between items-center gap-8 transition-colors">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Notificações</h1>
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Alertas e
                        Atualizações do
                        Sistema</p>
                </div>

                <div class="hidden md:flex items-center gap-6">
                    <div class="h-12 w-px bg-gray-100 dark:bg-gray-700"></div>
                    <div class="flex flex-col">
                        <span
                            class="text-2xl font-black text-gray-900 dark:text-white leading-none">{{ $notifications->total() }}</span>
                        <span
                            class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Total</span>
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="text-2xl font-black text-blue-600 dark:text-blue-400 leading-none">{{ $unreadCount }}</span>
                        <span
                            class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Não
                            Lidas</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="bulkDeleteBtn" onclick="bulkDelete()" disabled
                    class="bg-red-50 text-red-600 px-6 py-4 rounded-2xl hover:bg-red-600 hover:text-white transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-sm hidden">
                    <i class="bi bi-trash-fill text-lg mr-2"></i> Remover Selecionadas
                </button>
                @if($unreadCount > 0)
                    <form action="{{ route('notifications.read') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-4 rounded-2xl hover:bg-blue-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-blue-100">
                            <i class="bi bi-check-all text-lg mr-2"></i> Marcar todas lidas
                        </button>
                    </form>
                @endif
                <form action="{{ route('notifications.clear-read') }}" method="POST" id="clear-read-form">
                    @csrf
                    <button type="button"
                        onclick="confirmAction('Deseja remover todas as notificações lidas?', 'Esta ação não poderá ser desfeita.', 'info', 'Sim, limpar!', 'clear-read-form')"
                        class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-6 py-4 rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all font-black text-xs uppercase tracking-widest flex items-center">
                        <i class="bi bi-trash text-lg mr-2"></i> Limpar lidas
                    </button>
                </form>
            </div>
        </div>

        <form id="bulkActionForm" method="POST">
            @csrf

            <!-- Filter Tabs -->
            <div class="flex items-center gap-2 bg-gray-100/50 dark:bg-gray-700/50 p-1.5 rounded-2xl w-fit">
                <a href="{{ route('notifications.all', ['filter' => 'all']) }}"
                    class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $filter === 'all' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                    Todas
                </a>
                <a href="{{ route('notifications.all', ['filter' => 'unread']) }}"
                    class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $filter === 'unread' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                    Não lidas
                </a>
                <a href="{{ route('notifications.all', ['filter' => 'read']) }}"
                    class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $filter === 'read' ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-400 dark:text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                    Lidas
                </a>
            </div>

            <!-- Notifications List -->
            @if($notifications->isEmpty())
                <div
                    class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-700 p-20 text-center transition-colors">
                    <div
                        class="w-24 h-24 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center mx-auto mb-6">
                        <i class="bi bi-bell-slash text-4xl text-gray-200 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">Sem notificações</h3>
                    <p class="text-sm font-bold text-gray-400 dark:text-gray-500 max-w-xs mx-auto uppercase tracking-tighter">
                        @if($filter === 'unread') Tudo em dia! Você visualizou todos os alertas recentes.
                        @elseif($filter === 'read') Você ainda não possui histórico de alertas visualizados.
                        @else Não há nenhum alerta ou mensagem no sistema no momento. @endif
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach($notifications as $notification)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-all group {{ $notification->read_at ? 'opacity-60 grayscale-[0.5]' : 'border-blue-100 dark:border-blue-900/50' }}">
                            <div class="flex items-center gap-6">
                                <!-- Checkbox for Bulk Action -->
                                <div class="flex-shrink-0">
                                    <input type="checkbox" name="notification_ids[]" value="{{ $notification->id }}"
                                        class="notification-checkbox rounded-lg border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all cursor-pointer w-6 h-6 bg-white dark:bg-gray-700">
                                </div>

                                <div class="flex flex-col md:flex-row md:items-center gap-6 flex-1">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        @php
                                            $type = $notification->data['type'] ?? 'general';
                                            $config = match ($type) {
                                                'contribution_verified' => ['icon' => 'bi-check-circle-fill', 'color' => 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400', 'label' => 'Confirmada', 'badge' => 'bg-green-50 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
                                                'contribution_rejected' => ['icon' => 'bi-x-circle-fill', 'color' => 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400', 'label' => 'Rejeitada', 'badge' => 'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-300'],
                                                'contribution_created' => ['icon' => 'bi-cash-coin', 'color' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400', 'label' => 'Registo', 'badge' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'],
                                                'contribution_pending_validation' => ['icon' => 'bi-exclamation-triangle-fill', 'color' => 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400', 'label' => 'Validação', 'badge' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'],
                                                'contribution_verified_manager' => ['icon' => 'bi-check-circle-fill', 'color' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400', 'label' => 'Pacote', 'badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'],
                                                'contribution_rejected_manager' => ['icon' => 'bi-x-circle-fill', 'color' => 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400', 'label' => 'Pacote', 'badge' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300'],
                                                'member_created' => ['icon' => 'bi-person-plus-fill', 'color' => 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400', 'label' => 'Conta', 'badge' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300'],
                                                'member_added_to_cell' => ['icon' => 'bi-people-fill', 'color' => 'bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400', 'label' => 'Célula', 'badge' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300'],
                                                'commitment_chosen' => ['icon' => 'bi-handshake-fill', 'color' => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400', 'label' => 'Compromisso', 'badge' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300'],
                                                'commitment_expiring' => ['icon' => 'bi-clock-fill', 'color' => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400', 'label' => 'Prazo', 'badge' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300'],
                                                'pending_contributions' => ['icon' => 'bi-exclamation-triangle-fill', 'color' => 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400', 'label' => 'Comissão', 'badge' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'],
                                                'user_promoted' => ['icon' => 'bi-star-fill', 'color' => 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-400', 'label' => 'Cargo', 'badge' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300'],
                                                default => ['icon' => 'bi-bell-fill', 'color' => 'bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400', 'label' => 'Sistema', 'badge' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300'],
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
                                            <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                                {{ $notification->data['title'] ?? 'Notificação' }}
                                            </h4>
                                            <span
                                                class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $config['badge'] }}">
                                                {{ $config['label'] }}
                                            </span>
                                            @if(!$notification->read_at)
                                                <span class="w-2 h-2 bg-blue-600 dark:bg-blue-400 rounded-full animate-pulse"></span>
                                            @endif
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 line-clamp-2">
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
                                                    <i
                                                        class="bi bi-arrow-right transition-transform group-hover/link:translate-x-1"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div
                                        class="flex md:flex-col items-center justify-end gap-2 opacity-70 hover:opacity-100 transition-all">
                                        @if(!$notification->read_at)
                                            <a href="{{ route('notifications.mark-read', $notification->id) }}"
                                                class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white flex items-center justify-center transition-all shadow-sm"
                                                title="Lido">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                        @endif

                                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                                            id="delete-notification-{{ $notification->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('delete-notification-{{ $notification->id }}', 'Deseja excluir esta notificação?')"
                                                class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-500 hover:bg-red-500 hover:text-white dark:hover:bg-red-600 dark:hover:text-white flex items-center justify-center transition-all shadow-sm"
                                                title="Excluir">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
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
                bulkBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'hidden');
                bulkBtn.innerHTML = `<i class="bi bi-trash-fill mr-2"></i> Remover ${count} Selecionada(s)`;
            } else {
                bulkBtn.disabled = true;
                bulkBtn.classList.add('opacity-50', 'cursor-not-allowed', 'hidden');
            }
        }

        function bulkDelete() {
            confirmAction(
                'Confirmação de Remoção',
                'Deseja remover as notificações selecionadas?',
                'warning',
                'Sim, remover!',
                null
            ).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('bulkActionForm');
                    form.action = "{{ route('notifications.bulk-delete') }}";
                    form.submit();
                }
            });
        }
    </script>
@endsection