@php
    $authUser = auth()->user();
    $unreadNotifications = $authUser ? $authUser->unreadNotifications()->count() : 0;
@endphp
@auth
<div class="md:hidden fixed bottom-0 left-0 right-0 z-[1001] bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-t border-gray-200 dark:border-white/10 shadow-sm pb-[max(0.4rem,env(safe-area-inset-bottom))] pt-1">
    <div class="flex items-center justify-around h-11 px-1 max-w-md mx-auto">
        <!-- Dashboard / Home -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-0.5 text-[10px] font-bold tracking-tight transition-colors {{ request()->routeIs('dashboard*') ? 'text-orange-700 dark:text-orange-500 font-black' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center">
                <i class="bi bi-grid-1x2-fill text-base"></i>
            </div>
            <span class="text-[9px] mt-0.5">Início</span>
        </a>

        <!-- Células -->
        @if ($authUser->hasRole('membro') || $authUser->isLider() || $authUser->isTimoteo() || $authUser->isSupervisor() || $authUser->isPastorZona() || $authUser->isPastor() || $authUser->isAdmin())
        <a href="{{ route('cells.index') }}" class="flex flex-col items-center justify-center flex-1 py-0.5 text-[10px] font-bold tracking-tight transition-colors {{ request()->routeIs('cells*') ? 'text-orange-700 dark:text-orange-500 font-black' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center">
                <i class="bi bi-diagram-3-fill text-base"></i>
            </div>
            <span class="text-[9px] mt-0.5">Células</span>
        </a>
        @endif

        <!-- Cultos -->
        @if ($authUser->hasPermission('menu_services'))
        <a href="{{ route('services.index') }}" class="flex flex-col items-center justify-center flex-1 py-0.5 text-[10px] font-bold tracking-tight transition-colors {{ request()->routeIs('services*') ? 'text-orange-700 dark:text-orange-500 font-black' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center">
                <i class="bi bi-calendar-event-fill text-base"></i>
            </div>
            <span class="text-[9px] mt-0.5">Cultos</span>
        </a>
        @endif

        <!-- Alertas / Notificações -->
        <a href="{{ route('notifications.all') }}" class="flex flex-col items-center justify-center flex-1 py-0.5 text-[10px] font-bold tracking-tight relative transition-colors {{ request()->routeIs('notifications*') ? 'text-orange-700 dark:text-orange-500 font-black' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center">
                <i class="bi bi-bell-fill text-base"></i>
                <span data-notification-badge class="{{ $unreadNotifications > 0 ? '' : 'hidden' }} absolute -top-1 -right-1.5 flex h-3.5 w-3.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-red-600 text-[8px] font-black text-white items-center justify-center badge-count">
                        {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                    </span>
                </span>
            </div>
            <span class="text-[9px] mt-0.5">Alertas</span>
        </a>

        <!-- Perfil / Minha Conta -->
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center flex-1 py-0.5 text-[10px] font-bold tracking-tight transition-colors {{ request()->routeIs('profile*') ? 'text-orange-700 dark:text-orange-500 font-black' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center">
                <i class="bi bi-person-fill text-base"></i>
            </div>
            <span class="text-[9px] mt-0.5">Perfil</span>
        </a>
    </div>
</div>
@endauth
