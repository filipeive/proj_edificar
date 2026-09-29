@php
    $authUser = auth()->user();
    $unreadNotifications = $authUser ? $authUser->unreadNotifications()->count() : 0;
@endphp
@auth
<div class="md:hidden fixed bottom-4 left-4 right-4 z-40 bg-zinc-950/95 backdrop-blur-2xl border border-white/10 rounded-full shadow-2xl transition-all duration-300 pb-[max(0.25rem,env(safe-area-inset-bottom))]">
    <div class="flex items-center justify-around h-14 px-2 max-w-md mx-auto">
        <!-- Dashboard / Home -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-[10px] font-bold tracking-tight transition-all duration-200 active:scale-90 {{ request()->routeIs('dashboard*') ? 'text-orange-500 font-extrabold' : 'text-slate-400 hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-grid-1x2-fill text-xl"></i>
                @if(request()->routeIs('dashboard*'))
                    <span class="absolute -bottom-1 w-1 h-1 bg-orange-500 rounded-full shadow-lg shadow-orange-500/50"></span>
                @endif
            </div>
            <span>Início</span>
        </a>

        <!-- Células -->
        @if ($authUser->hasRole('membro') || $authUser->isLider() || $authUser->isTimoteo() || $authUser->isSupervisor() || $authUser->isPastorZona() || $authUser->isPastor() || $authUser->isAdmin())
        <a href="{{ route('cells.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-[10px] font-bold tracking-tight transition-all duration-200 active:scale-90 {{ request()->routeIs('cells*') ? 'text-orange-500 font-extrabold' : 'text-slate-400 hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-diagram-3-fill text-xl"></i>
                @if(request()->routeIs('cells*'))
                    <span class="absolute -bottom-1 w-1 h-1 bg-orange-500 rounded-full shadow-lg shadow-orange-500/50"></span>
                @endif
            </div>
            <span>Células</span>
        </a>
        @endif

        <!-- Cultos -->
        @if ($authUser->hasPermission('menu_services'))
        <a href="{{ route('services.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-[10px] font-bold tracking-tight transition-all duration-200 active:scale-90 {{ request()->routeIs('services*') ? 'text-orange-500 font-extrabold' : 'text-slate-400 hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-calendar-event-fill text-xl"></i>
                @if(request()->routeIs('services*'))
                    <span class="absolute -bottom-1 w-1 h-1 bg-orange-500 rounded-full shadow-lg shadow-orange-500/50"></span>
                @endif
            </div>
            <span>Cultos</span>
        </a>
        @endif

        <!-- Notificações / Alertas -->
        <a href="{{ route('notifications.all') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-[10px] font-bold tracking-tight relative transition-all duration-200 active:scale-90 {{ request()->routeIs('notifications*') ? 'text-orange-500 font-extrabold' : 'text-slate-400 hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-bell-fill text-xl"></i>
                <span data-notification-badge class="{{ $unreadNotifications > 0 ? '' : 'hidden' }} absolute -top-1 -right-1.5 flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-600 text-[8px] font-black text-white items-center justify-center shadow-md badge-count">
                        {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                    </span>
                </span>
                @if(request()->routeIs('notifications*'))
                    <span class="absolute -bottom-1 w-1 h-1 bg-orange-500 rounded-full shadow-lg shadow-orange-500/50"></span>
                @endif
            </div>
            <span>Alertas</span>
        </a>

        <!-- Menu Drawer Trigger -->
        <button type="button" @click="mobileSidebarOpen = !mobileSidebarOpen" class="flex flex-col items-center justify-center flex-1 py-1 text-[10px] font-bold tracking-tight text-slate-400 hover:text-slate-200 transition-all duration-200 active:scale-90">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-grid-fill text-xl"></i>
            </div>
            <span>Menu</span>
        </button>
    </div>
</div>
@endauth
