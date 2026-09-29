@php
    $authUser = auth()->user();
    $unreadNotifications = $authUser ? $authUser->unreadNotifications()->count() : 0;
@endphp
@auth
<div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl border-t border-gray-200/80 dark:border-white/10 shadow-[0_-4px_25px_rgba(0,0,0,0.06)] transition-all duration-300 pb-[max(0.6rem,env(safe-area-inset-bottom))] pt-1.5">
    <div class="flex items-center justify-around h-12 px-1 max-w-md mx-auto">
        <!-- Dashboard / Home -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 pt-1 pb-0.5 text-[10px] font-bold tracking-tight transition-all duration-200 active:scale-95 {{ request()->routeIs('dashboard*') ? 'text-orange-600 dark:text-orange-500 font-extrabold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-grid-1x2-fill text-xl"></i>
                @if(request()->routeIs('dashboard*'))
                    <span class="absolute -bottom-1.5 w-1 h-1 bg-orange-600 dark:bg-orange-500 rounded-full shadow-md"></span>
                @endif
            </div>
            <span class="text-[10px]">Início</span>
        </a>

        <!-- Células -->
        @if ($authUser->hasRole('membro') || $authUser->isLider() || $authUser->isTimoteo() || $authUser->isSupervisor() || $authUser->isPastorZona() || $authUser->isPastor() || $authUser->isAdmin())
        <a href="{{ route('cells.index') }}" class="flex flex-col items-center justify-center flex-1 pt-1 pb-0.5 text-[10px] font-bold tracking-tight transition-all duration-200 active:scale-95 {{ request()->routeIs('cells*') ? 'text-orange-600 dark:text-orange-500 font-extrabold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-diagram-3-fill text-xl"></i>
                @if(request()->routeIs('cells*'))
                    <span class="absolute -bottom-1.5 w-1 h-1 bg-orange-600 dark:bg-orange-500 rounded-full shadow-md"></span>
                @endif
            </div>
            <span class="text-[10px]">Células</span>
        </a>
        @endif

        <!-- Cultos -->
        @if ($authUser->hasPermission('menu_services'))
        <a href="{{ route('services.index') }}" class="flex flex-col items-center justify-center flex-1 pt-1 pb-0.5 text-[10px] font-bold tracking-tight transition-all duration-200 active:scale-95 {{ request()->routeIs('services*') ? 'text-orange-600 dark:text-orange-500 font-extrabold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-calendar-event-fill text-xl"></i>
                @if(request()->routeIs('services*'))
                    <span class="absolute -bottom-1.5 w-1 h-1 bg-orange-600 dark:bg-orange-500 rounded-full shadow-md"></span>
                @endif
            </div>
            <span class="text-[10px]">Cultos</span>
        </a>
        @endif

        <!-- Alertas / Notificações -->
        <a href="{{ route('notifications.all') }}" class="flex flex-col items-center justify-center flex-1 pt-1 pb-0.5 text-[10px] font-bold tracking-tight relative transition-all duration-200 active:scale-95 {{ request()->routeIs('notifications*') ? 'text-orange-600 dark:text-orange-500 font-extrabold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-bell-fill text-xl"></i>
                <span data-notification-badge class="{{ $unreadNotifications > 0 ? '' : 'hidden' }} absolute -top-1 -right-1.5 flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-600 text-[8px] font-black text-white items-center justify-center shadow-md badge-count">
                        {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                    </span>
                </span>
                @if(request()->routeIs('notifications*'))
                    <span class="absolute -bottom-1.5 w-1 h-1 bg-orange-600 dark:bg-orange-500 rounded-full shadow-md"></span>
                @endif
            </div>
            <span class="text-[10px]">Alertas</span>
        </a>

        <!-- Perfil / Minha Conta -->
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center flex-1 pt-1 pb-0.5 text-[10px] font-bold tracking-tight transition-all duration-200 active:scale-95 {{ request()->routeIs('profile*') ? 'text-orange-600 dark:text-orange-500 font-extrabold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-person-fill text-xl"></i>
                @if(request()->routeIs('profile*'))
                    <span class="absolute -bottom-1.5 w-1 h-1 bg-orange-600 dark:bg-orange-500 rounded-full shadow-md"></span>
                @endif
            </div>
            <span class="text-[10px]">Perfil</span>
        </a>
    </div>
</div>
@endauth
