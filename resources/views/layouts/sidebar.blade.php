@php
    // O View Composer já passa $user e $role
    $authUser = auth()->user();
@endphp
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
        <!-- DASHBOARD -->
        <div class="pb-4">
            <a href="{{ route('dashboard') }}"
                class="nav-item relative flex items-center px-4 py-3.5 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-orange-600 text-white shadow-xl shadow-orange-600/30' : 'text-slate-400' }}">
                <i class="bi bi-grid-1x2-fill text-xl flex-shrink-0"></i>
                <span class="sidebar-text ml-4 font-bold tracking-tight">Dashboard</span>
                <span class="tooltip">Dashboard Geral</span>
            </a>

            @if ($authUser->isEdificarManager())
                <a href="{{ route('edificar.dashboard') }}"
                    class="nav-item relative flex items-center px-4 py-3.5 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('edificar.dashboard') ? 'bg-blue-600 text-white shadow-xl shadow-blue-600/30' : 'text-slate-400' }}">
                    <i class="bi bi-graph-up-arrow text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Painel Edificar</span>
                    <span class="tooltip">Evolução da Obra</span>
                </a>
            @endif
        </div>

        @if ($authUser && ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastor() || $authUser->isPastorZona() || $authUser->isSupervisor()))
            <!-- GESTÃO ECLESIÁSTICA -->
            <div
                class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-2">
                Gestão Eclesiástica</div>

            @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastor() || $authUser->isPastorZona())
                <a href="{{ route('services.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('services.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-journal-bookmark-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Cultos</span>
                    <span class="tooltip">Relatórios de Celebração</span>
                </a>
            @endif

            <a href="{{ route('events.index') }}"
                class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('events.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                <i class="bi bi-calendar-check-fill text-xl flex-shrink-0"></i>
                <span class="sidebar-text ml-4 font-bold tracking-tight">Eventos</span>
                <span class="tooltip">Eventos e Cerimônias</span>
            </a>

            @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastor())
                <a href="{{ route('weddings.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('weddings.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-heart-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Casamentos</span>
                    <span class="tooltip">Calendário Matrimonial</span>
                </a>
            @endif

            @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastorZona())
                <a href="{{ route('visitors.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('visitors.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-person-plus-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Visitantes</span>
                    @php
                        $pendingVisitors = \App\Models\Visitor::pending()->count();
                    @endphp
                    @if ($pendingVisitors > 0)
                        <span
                            class="absolute right-4 top-1/2 -translate-y-1/2 bg-yellow-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg shadow-yellow-600/20">{{ $pendingVisitors }}</span>
                    @endif
                    <span class="tooltip">Acompanhamento de Visitantes</span>
                </a>
            @endif
        @endif

        @if ($authUser->isAdmin() || $authUser->isPastor() || $authUser->isPastorZona() || $authUser->isSupervisor() || $authUser->isLider() || $authUser->isTimoteo() || $authUser->isSecretaria() || $authUser->hasRole('membro'))
            <!-- CÉLULAS & GRUPOS -->
            <div
                class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">
                Células & Grupos</div>

            @if ($authUser->hasRole('membro'))
                <a href="{{ route('dashboard.membro') }}#minha-celula"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('dashboard.membro') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-people-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Minha Célula</span>
                    <span class="tooltip">Ver Célula</span>
                </a>
            @endif

            @if (!$authUser->hasRole('membro'))
                <a href="{{ route('cell-meetings.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('cell-meetings.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-people-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Encontros</span>
                    <span class="tooltip">Reuniões de Célula</span>
                </a>

                <a href="{{ route('members.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('members.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-person-lines-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Membros</span>
                    <span class="tooltip">Listagem de Membros</span>
                </a>
            @endif

            @if ($authUser->isLider() && $authUser->ledCells()->exists())
                <a href="{{ route('cells.attendance', $authUser->ledCells()->first()) }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('cells.attendance') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-calendar-check-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Ficha Guia</span>
                    <span class="tooltip">Presenças da Célula</span>
                </a>
            @endif

            @if ($authUser->isAdmin() || $authUser->isPastorZona() || $authUser->isSupervisor())
                <a href="{{ route('cells.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('cells.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-diagram-3-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Listagem de Células</span>
                    <span class="tooltip">Gestão de Células</span>
                </a>
            @endif

            @if ($authUser->isAdmin() || $authUser->isPastorZona())
                <a href="{{ route('supervisions.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('supervisions.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-layers-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Supervisões</span>
                    <span class="tooltip">Gestão de Supervisões</span>
                </a>
            @endif

            @if ($authUser->isAdmin())
                <a href="{{ route('zones.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('zones.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-geo-alt-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Zonas</span>
                    <span class="tooltip">Gestão de Zonas</span>
                </a>
            @endif
        @endif

        @if (!$authUser->hasRole('membro'))
            <!-- ACADEMIA & ENSINO -->
            <div
                class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">
                Academia & Ensino</div>

            <a href="{{ route('courses.index') }}"
                class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('courses.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                <i class="bi bi-mortarboard-fill text-xl flex-shrink-0"></i>
                <span class="sidebar-text ml-4 font-bold tracking-tight">Cursos</span>
                <span class="tooltip">Academia de Vida e Cursos</span>
            </a>

            @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastor())
                <a href="{{ route('course-classes.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('course-classes.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-collection-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Turmas</span>
                    <span class="tooltip">Gestão de Turmas</span>
                </a>
            @endif
        @endif

        @if (!$authUser->hasRole('membro'))
            <!-- PESSOAS & RELATÓRIOS -->
            <div
                class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">
                Pessoas & Relatórios</div>


            @if ($authUser->isAdmi())
                <a href="{{ route('users.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('users.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-person-lock text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Utilizadores</span>
                    <span class="tooltip">Acessos ao Sistema</span>
                </a>
            @endif

            @if ($authUser->isAdmin() || $authUser->isPastor() || $authUser->isPastorZona() || $authUser->isSupervisor())
                <div>
                    <button
                        class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('reports.*') ? 'bg-zinc-900/50 text-white' : 'text-slate-400' }}"
                        onclick="toggleMenu('reports')">
                        <i class="bi bi-bar-chart-line-fill text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight flex-1">Estatísticas</span>
                        <i
                            class="bi bi-chevron-down sidebar-text ml-2 text-[10px] transition-transform duration-300 {{ request()->routeIs('reports.*') ? 'rotate-180' : '' }}"></i>
                        <span class="tooltip">Relatórios de Desempenho</span>
                    </button>
                    <div id="reports" class="overflow-hidden {{ request()->routeIs('reports.*') ? '' : 'hidden' }}">
                        <div class="ml-12 mt-2 space-y-1 border-l border-white/10 pl-4">
                            <a href="{{ route('reports.cell') }}"
                                class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('reports.cell') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Célula</a>

                            @if ($authUser->isAdmin() || $authUser->isPastorZona() || $authUser->isSupervisor())
                                <a href="{{ route('reports.supervision') }}"
                                    class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('reports.supervision') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Supervisão</a>
                            @endif

                            @if ($authUser->isAdmin() || $authUser->isPastorZona())
                                <a href="{{ route('reports.zone') }}"
                                    class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('reports.zone') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Zona</a>
                            @endif

                            @if ($authUser->isAdmin())
                                <a href="{{ route('reports.global') }}"
                                    class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('reports.global') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Global</a>
                                <a href="{{ route('course-classes.upcoming-weddings') }}"
                                    class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('course-classes.upcoming-weddings') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Casamentos
                                    Próximos</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if ($authUser->isAdmin() || $authUser->isPastorZona() || $authUser->isSupervisor())
                <a href="{{ route('quarterly-reports.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('quarterly-reports.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-file-earmark-text-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Trimestrais</span>
                    <span class="tooltip">Relatórios Trimestrais</span>
                </a>
            @endif
        @endif

        @if ($authUser->isAdmin())
            <!-- CONFIGURAÇÕES -->
            <div
                class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">
                Sistema</div>

            <a href="{{ route('settings.index') }}"
                class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('settings.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                <i class="bi bi-gear-fill text-xl flex-shrink-0"></i>
                <span class="sidebar-text ml-4 font-bold tracking-tight">Configurações</span>
                <span class="tooltip">Configurações do Sistema</span>
            </a>
        @endif

        <!-- GESTÃO FINANCEIRA & EDIFICAR -->
        <div
            class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">
            Gestão Financeira & Edificar
        </div>

        @if ($authUser->isAdmin() || $authUser->isEdificarManager() || $authUser->isResponsavelPacote())
            <a href="{{ route('packages.index') }}"
                class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('packages.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                <i class="bi bi-box-seam-fill text-xl flex-shrink-0"></i>
                <span class="sidebar-text ml-4 font-bold tracking-tight">Pacotes</span>
                <span class="tooltip">Pacotes de Compromisso</span>
            </a>
        @endif

        @if ($authUser->isAdmin() || $authUser->isEdificarManager() || $authUser->isTesouraria())
            <!-- GESTÃO DE DESPESAS (NEW) -->
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

        <div>
            <button
                class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('contributions.*') ? 'bg-zinc-900/50 text-white' : 'text-slate-400' }}"
                onclick="toggleMenu('contributions')">
                <i class="bi bi-cash-stack text-xl flex-shrink-0"></i>
                <span class="sidebar-text ml-4 font-bold tracking-tight flex-1">Contribuições</span>
                <i
                    class="bi bi-chevron-down sidebar-text ml-2 text-[10px] transition-transform duration-300 {{ request()->routeIs('contributions.*') ? 'rotate-180' : '' }}"></i>
                <span class="tooltip">Dízimos e Ofertas</span>
            </button>
            <div id="contributions" class="overflow-hidden {{ request()->routeIs('contributions.*') ? '' : 'hidden' }}">
                <div class="ml-12 mt-2 space-y-1 border-l border-white/10 pl-4">
                    @if (!$authUser->isAdmin())
                        <a href="{{ route('contributions.index', ['mine' => 1]) }}"
                            class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('contributions.index') && request()->query('mine') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">
                            Minhas Contribuições
                        </a>
                    @endif

                    @if ($authUser->isAdmin() || $authUser->isPastorZona() || $authUser->isSupervisor() || $authUser->isLider())
                        <a href="{{ route('contributions.index') }}"
                            class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('contributions.index') && !request()->query('mine') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">
                            @if ($authUser->isAdmin()) Todas
                            @elseif ($authUser->isPastorZona()) Da Zona
                            @elseif ($authUser->isSupervisor()) Da Supervisão @else Da Célula @endif
                        </a>
                    @endif

                    <a href="{{ route('contributions.create') }}"
                        class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('contributions.create') ? 'text-green-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">
                        Nova Contribuição
                    </a>
                </div>
            </div>
        </div>

        @if ($authUser->isAdmin() || $authUser->isPastor() || $authUser->isTesouraria())
            <a href="{{ route('financial.dashboard') }}"
                class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('financial.dashboard') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                <i class="bi bi-pie-chart-fill text-xl flex-shrink-0"></i>
                <span class="sidebar-text ml-4 font-bold tracking-tight">Painel Financeiro</span>
                <span class="tooltip">Consolidado Financeiro</span>
            </a>
        @endif

        @if ($authUser->isAdmin() || $authUser->isSecretaria())
            <a href="{{ route('contributions.pending') }}"
                class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('contributions.pending') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                <i class="bi bi-shield-lock-fill text-xl flex-shrink-0"></i>
                <span class="sidebar-text ml-4 font-bold tracking-tight">Confirmar Contribuição</span>
                @if ($pendingCount > 0)
                    <span
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-orange-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg shadow-orange-600/20">{{ $pendingCount }}</span>
                @endif
                <span class="tooltip">Validação Pendente</span>
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
</aside>