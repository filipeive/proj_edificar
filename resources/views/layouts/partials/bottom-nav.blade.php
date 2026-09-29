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

        <!-- Perfil / Minha Conta -->
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-[10px] font-bold tracking-tight transition-all duration-200 active:scale-90 {{ request()->routeIs('profile*') ? 'text-orange-500 font-extrabold' : 'text-slate-400 hover:text-slate-200' }}">
            <div class="relative flex items-center justify-center mb-0.5">
                <i class="bi bi-person-fill text-xl"></i>
                @if(request()->routeIs('profile*'))
                    <span class="absolute -bottom-1 w-1 h-1 bg-orange-500 rounded-full shadow-lg shadow-orange-500/50"></span>
                @endif
            </div>
            <span>Perfil</span>
        </a>
    </div>
</div>
@endauth
