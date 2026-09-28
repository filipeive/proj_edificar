@php
    $authUser = auth()->user();
@endphp
@auth
<div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-zinc-950/90 backdrop-blur-xl border-t border-white/10 pb-[env(safe-area-inset-bottom)] transition-transform duration-300">
    <div class="flex items-center justify-around h-16 px-2 max-w-lg mx-auto">
        <!-- Dashboard / Home -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-xs font-semibold transition-all duration-200 {{ request()->routeIs('dashboard*') ? 'text-orange-500 font-bold scale-105' : 'text-slate-400 hover:text-slate-200' }}">
            <i class="bi bi-grid-1x2-fill text-lg mb-0.5"></i>
            <span>Início</span>
        </a>

        <!-- Células -->
        @if ($authUser->hasRole('membro') || $authUser->isLider() || $authUser->isTimoteo() || $authUser->isSupervisor() || $authUser->isPastorZona() || $authUser->isPastor() || $authUser->isAdmin())
        <a href="{{ route('cells.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-xs font-semibold transition-all duration-200 {{ request()->routeIs('cells*') ? 'text-orange-500 font-bold scale-105' : 'text-slate-400 hover:text-slate-200' }}">
            <i class="bi bi-diagram-3-fill text-lg mb-0.5"></i>
            <span>Células</span>
        </a>
        @endif

        <!-- Cultos -->
        @if ($authUser->hasPermission('menu_services'))
        <a href="{{ route('services.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-xs font-semibold transition-all duration-200 {{ request()->routeIs('services*') ? 'text-orange-500 font-bold scale-105' : 'text-slate-400 hover:text-slate-200' }}">
            <i class="bi bi-calendar-event-fill text-lg mb-0.5"></i>
            <span>Cultos</span>
        </a>
        @endif

        <!-- Notificações -->
        <a href="{{ route('notifications.all') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-xs font-semibold relative transition-all duration-200 {{ request()->routeIs('notifications*') ? 'text-orange-500 font-bold scale-105' : 'text-slate-400 hover:text-slate-200' }}">
            <i class="bi bi-bell-fill text-lg mb-0.5"></i>
            <span>Alertas</span>
        </a>

        <!-- Menu Drawer Trigger -->
        <button type="button" @click="mobileSidebarOpen = !mobileSidebarOpen" class="flex flex-col items-center justify-center flex-1 py-1 text-xs font-semibold text-slate-400 hover:text-slate-200 transition-all duration-200 active:scale-95">
            <i class="bi bi-list text-xl mb-0.5"></i>
            <span>Menu</span>
        </button>
    </div>
</div>
@endauth
