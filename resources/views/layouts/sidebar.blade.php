@php
    // O View Composer já passa $user e $role
    $authUser = auth()->user();
@endphp
@if($authUser)
    <aside id="sidebar"
        class="sidebar-expanded bg-black text-white flex flex-col overflow-y-auto shadow-2xl transition-all duration-300 ease-in-out border-r border-white/5">
        <!-- Header -->
        <div
            class="px-6 py-8 border-b border-white/5 flex items-center justify-between bg-black/50 backdrop-blur-xl sticky top-0 z-20">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="flex-shrink-0 p-2 bg-orange-600 rounded-xl shadow-lg shadow-orange-600/20">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto brightness-0 invert">
                </div>
                <div class="sidebar-text">
                    <h1 class="text-lg font-black tracking-tighter text-white uppercase leading-none">Life - APP</h1>
                    <p class="text-[9px] text-orange-500 font-black uppercase tracking-[0.2em] mt-1"><small>Portal de
                            Gestão/Edificar</small></p>
                </div>
            </div>
            <button onclick="toggleSidebar()"
                class="hidden md:flex text-gray-400 hover:text-white transition-all p-2 hover:bg-white/5 rounded-xl border border-transparent hover:border-white/10">
                <i id="sidebarIcon" class="bi bi-layout-sidebar-inset-reverse text-xl"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
            <!-- DASHBOARD GERAL / FINANCEIRO -->
            <div class="pb-4">
                @if ($authUser->isEdificarManager())
                    <a href="{{ route('edificar.dashboard') }}"
                        class="nav-item relative flex items-center px-4 py-3.5 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('edificar.dashboard') ? 'bg-blue-600 text-white shadow-xl shadow-blue-600/30' : 'text-slate-400' }}">
                        <i class="bi bi-graph-up-arrow text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Painel Edificar</span>
                        <span class="tooltip">Evolução da Obra</span>
                    </a>
                @endif

                @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isTesouraria())
                    <!-- Financeiro Header -->
                    <div
                        class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-2">
                        Gestão Financeira
                    </div>

                    <a href="{{ route('financial.dashboard') }}"
                        class="nav-item relative flex items-center px-4 py-3.5 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('financial.dashboard') ? 'bg-orange-600 text-white shadow-xl shadow-orange-600/30' : 'text-slate-400' }}">
                        <i class="bi bi-wallet2 text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Dashboard Financeiro</span>
                    </a>

                    <!-- Submenu Despesas -->
                    <a href="{{ route('requisitions.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('requisitions.*') ? 'bg-zinc-900 text-red-500 border border-white/5' : 'text-slate-400' }}">
                        <i class="bi bi-cash-coin text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Requisições</span>
                        @php $pendingReqs = \App\Models\Requisition::where('status', 'pending')->count(); @endphp
                        @if ($pendingReqs > 0)
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 bg-yellow-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg shadow-yellow-600/20">{{ $pendingReqs }}</span>
                        @endif
                    </a>

                    <a href="{{ route('expenses.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('expenses.*') ? 'bg-zinc-900 text-red-500 border border-white/5' : 'text-slate-400' }}">
                        <i class="bi bi-receipt text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Despesas</span>
                    </a>
                @endif
            </div>

            @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastor() || $authUser->isPastorZona() || $authUser->isSupervisor())
                <!-- PAINEL SECRETARIA / ECLESIÁSTICO -->
                <div
                    class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-2">
                    Gestão Eclesiástica</div>

                <a href="{{ route('services.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('services.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-journal-bookmark-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Cultos</span>
                </a>

                <a href="{{ route('events.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('events.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-calendar-check-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Eventos</span>
                </a>

                @if ($authUser->isAdmin() || $authUser->isSecretaria())
                    <a href="{{ route('weddings.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('weddings.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                        <i class="bi bi-heart-fill text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Casamentos</span>
                    </a>
                @endif

                @if ($authUser->isAdmin() || $authUser->isSecretaria())
                    <a href="{{ route('visitors.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('visitors.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                        <i class="bi bi-person-plus-fill text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Visitantes</span>
                        @php $pendingVisitors = \App\Models\Visitor::pending()->count(); @endphp
                        @if ($pendingVisitors > 0)
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 bg-yellow-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg shadow-yellow-600/20">{{ $pendingVisitors }}</span>
                        @endif
                    </a>
                @endif
            @endif

            @if (!$authUser->isSecretaria() && ($authUser->isAdmin() || $authUser->isPastor() || $authUser->isSupervisor() || $authUser->isLider()))
                <!-- CÉLULAS & ENSINO (Escondido da Secretaria conforme solicitado) -->
                <div
                    class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">
                    Células & Academia</div>

                <a href="{{ route('cell-meetings.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('cell-meetings.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-people-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Encontros</span>
                </a>

                <a href="{{ route('courses.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('courses.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-mortarboard-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Cursos</span>
                </a>

                @if ($authUser->isAdmin())
                    <a href="{{ route('course-classes.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('course-classes.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                        <i class="bi bi-collection-fill text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Turmas</span>
                    </a>
                @endif
            @endif

            @if ($authUser->isEdificarManager() || $authUser->isResponsavelPacote())
                <!-- PAINEL PROJECTO EDIFICAR -->
                <div
                    class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">
                    Projecto Edificar</div>

                <a href="{{ route('packages.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('packages.*') ? 'bg-zinc-900 text-blue-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-box-seam-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">@if($authUser->isResponsavelPacote()) Meu Pacote
                    @else Gestão de Pacotes @endif</span>
                </a>

                <a href="{{ route('contributions.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('contributions.*') ? 'bg-zinc-900 text-blue-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-cash-stack text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Contribuições Obra</span>
                </a>

                @if ($authUser->isEdificarManager() || $authUser->isSecretaria())
                    <a href="{{ route('contributions.pending') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('contributions.pending') ? 'bg-red-900/20 text-red-500 border border-red-500/20' : 'text-slate-400' }}">
                        <i class="bi bi-shield-lock-fill text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Validar Contribuições</span>
                        @php $pendingCount = \App\Models\Contribution::pending()->count(); @endphp
                        @if ($pendingCount > 0)
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 bg-red-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg shadow-red-600/20">{{ $pendingCount }}</span>
                        @endif
                    </a>
                @endif
            @endif

            @if ($authUser->isAdmin())
                <!-- ADMIN SISTEMA -->
                <div
                    class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">
                    Administração</div>

                @if ($authUser->isAdmin())
                    <a href="{{ route('users.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('users.*') ? 'bg-zinc-900 text-white border border-white/5' : 'text-slate-400' }}">
                        <i class="bi bi-person-lock text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Utilizadores</span>
                    </a>
                @endif

                <a href="{{ route('settings.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('settings.*') ? 'bg-zinc-900 text-white border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-gear-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Configurações</span>
                </a>
            @endif

            <a href="{{ route('commitments.index') }}"
                class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('commitments.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                <i class="bi bi-heart-pulse-fill text-xl flex-shrink-0"></i>
                <span class="sidebar-text ml-4 font-bold tracking-tight">Meu Compromisso</span>
                <span class="tooltip">Meus Compromissos</span>
            </a>

            <!-- SISTEMA -->
            <div
                class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">
                Sistema</div>

            <a href="{{ route('notifications.all') }}"
                class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('notifications.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                <i class="bi bi-bell-fill text-xl flex-shrink-0"></i>
                <span class="sidebar-text ml-4 font-bold tracking-tight">Notificações</span>
                @if ($unreadNotifications > 0)
                    <span
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-red-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg shadow-red-600/20">{{ $unreadNotifications }}</span>
                @endif
                <span class="tooltip">Central de Alertas</span>
            </a>
        </nav>

        <!-- User Profile Footer -->
        <div class="mt-auto border-t border-white/5 bg-black/80 backdrop-blur-md sticky bottom-0 z-20">
            <div class="p-4">
                <div class="flex items-center space-x-3 mb-4 overflow-hidden">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center flex-shrink-0 font-black text-white shadow-lg shadow-orange-600/20">
                        {{ strtoupper(substr($authUser->name, 0, 1)) }}
                    </div>
                    <div class="sidebar-text flex-1 min-w-0">
                        <p class="text-sm font-black text-white truncate">{{ $authUser->name }}</p>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider truncate">
                            {{ ucfirst(str_replace('_', ' ', $authUser->role)) }}
                        </p>
                    </div>
                    <a href="{{ route('profile.edit') }}"
                        class="sidebar-text text-slate-400 hover:text-white transition-all p-2 hover:bg-white/5 rounded-xl border border-transparent hover:border-white/10"
                        title="Editar Perfil">
                        <i class="bi bi-gear-fill"></i>
                    </a>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center px-4 py-3 text-slate-400 hover:text-white hover:bg-red-600 transition-all duration-300 rounded-2xl font-black text-xs uppercase tracking-widest border border-white/5 hover:border-red-600 shadow-lg hover:shadow-red-600/20">
                        <i class="bi bi-power mr-2 text-lg"></i>
                        <span class="sidebar-text">Sair do Sistema</span>
                    </button>
                </form>
            </div>
        </div>
</aside>@endif