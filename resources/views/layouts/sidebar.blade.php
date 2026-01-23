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

            @if ($authUser->isResponsavelPacote())
                <a href="{{ route('packages.dashboard') }}"
                    class="nav-item relative flex items-center px-4 py-3.5 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('packages.dashboard') ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-600/30' : 'text-slate-400' }}">
                    <i class="bi bi-speedometer2 text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Painel do Pacote</span>
                    <span class="tooltip">Gestão de Compromissos</span>
                </a>
            @endif
        </div>

        <!-- ATALHOS RÁPIDOS (Frequent Actions) -->
        @if($authUser->isLider() || $authUser->isResponsavelPacote() || $authUser->isAdmin() || $authUser->isComissaoObra())
            <div class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-2 mt-2">Atalhos Rápidos</div>
            <div class="grid grid-cols-2 gap-2 px-2 py-2">
                @if($authUser->isSecretaria() || $authUser->isAdmin())
                    <a href="{{ route('services.create') }}" class="flex flex-col items-center justify-center p-3 rounded-2xl bg-white/5 border border-white/10 hover:bg-orange-600/20 hover:border-orange-500/50 transition-all group">
                        <i class="bi bi-plus-circle text-orange-500 mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[9px] font-black uppercase tracking-tighter text-slate-400 group-hover:text-white">Relatório de Culto</span>
                    </a>
                @endif
                <!-- resgistar encontro de celula -->
                @if($authUser->isLider() || $authUser->isAdmin() || $authUser->isSupervisor() || $authUser->isPastorZona())
                    <a href="{{ route('cell-meetings.create') }}" class="flex flex-col items-center justify-center p-3 rounded-2xl bg-white/5 border border-white/10 hover:bg-orange-600/20 hover:border-orange-500/50 transition-all group">
                        <i class="bi bi-plus-circle text-orange-500 mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[9px] font-black uppercase tracking-tighter text-slate-400 group-hover:text-white">Encontro de Celula</span>
                    </a>
                @endif
                @if($authUser->isResponsavelPacote() || $authUser->isAdmin())
                    <a href="{{ route('contributions.create') }}" class="flex flex-col items-center justify-center p-3 rounded-2xl bg-white/5 border border-white/10 hover:bg-blue-600/20 hover:border-blue-500/50 transition-all group">
                        <i class="bi bi-cash-coin text-blue-500 mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[9px] font-black uppercase tracking-tighter text-slate-400 group-hover:text-white">Contribuição</span>
                    </a>
                @endif
                @if($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastorZona())
                    <a href="{{ route('visitors.create') }}" class="flex flex-col items-center justify-center p-3 rounded-2xl bg-white/5 border border-white/10 hover:bg-green-600/20 hover:border-green-500/50 transition-all group">
                        <i class="bi bi-person-plus text-green-500 mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[9px] font-black uppercase tracking-tighter text-slate-400 group-hover:text-white">Visitante</span>
                    </a>
                @endif
                @if($authUser->isAdmin() || $authUser->isTesouraria())
                    <a href="{{ route('requisitions.create') }}" class="flex flex-col items-center justify-center p-3 rounded-2xl bg-white/5 border border-white/10 hover:bg-red-600/20 hover:border-red-500/50 transition-all group">
                        <i class="bi bi-file-earmark-plus text-red-500 mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[9px] font-black uppercase tracking-tighter text-slate-400 group-hover:text-white">Requisição</span>
                    </a>
                @endif
                @if($authUser->isAdmin() || $authUser->isComissaoObra())
                    <a href="{{ route('contributions.index', ['status' => 'pendente']) }}" class="flex flex-col items-center justify-center p-3 rounded-2xl bg-white/5 border border-white/10 hover:bg-yellow-600/20 hover:border-yellow-500/50 transition-all group">
                        <i class="bi bi-patch-check text-yellow-500 mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[9px] font-black uppercase tracking-tighter text-slate-400 group-hover:text-white">Validar</span>
                    </a>
                @endif
            </div>
        @endif

        @php
            $order = ['operacao', 'celulas', 'financeira'];
            if ($authUser->isResponsavelPacote() || $authUser->isTesouraria()) {
                $order = ['financeira', 'celulas', 'operacao'];
            } elseif ($authUser->isLider() || $authUser->isSupervisor() || $authUser->isPastorZona()) {
                $order = ['celulas', 'operacao', 'financeira'];
            }
        @endphp

        @foreach($order as $sectionName)
            @if($sectionName === 'operacao')
                @if ($authUser && ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastor() || $authUser->isPastorZona() || $authUser->isSupervisor()))
                    <!-- OPERAÇÃO ECLESIÁSTICA -->
                    <div class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-2">Operação Eclesiástica</div>

                    @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastor() || $authUser->isPastorZona())
                        <a href="{{ route('services.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('services.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                            <i class="bi bi-journal-bookmark-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight">Cultos</span>
                        </a>
                    @endif

                    <a href="{{ route('events.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('events.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                        <i class="bi bi-calendar-check-fill text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Eventos</span>
                    </a>

                    @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastor())
                        <a href="{{ route('weddings.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('weddings.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                            <i class="bi bi-heart-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight">Casamentos</span>
                        </a>
                    @endif

                    @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastorZona())
                        <a href="{{ route('visitors.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('visitors.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                            <i class="bi bi-person-plus-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight">Visitantes</span>
                        </a>
                    @endif

                    @if ($authUser->isAdmin() || $authUser->role !== 'comissao_obra')
                        <div>
                            <button class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('courses.*') || request()->routeIs('course-classes.*') ? 'bg-zinc-900/50 text-white' : 'text-slate-400' }}" onclick="toggleMenu('courses_menu')">
                                <i class="bi bi-mortarboard-fill text-xl flex-shrink-0"></i>
                                <span class="sidebar-text ml-4 font-bold tracking-tight flex-1">Escola Ministerial</span>
                                <i class="bi bi-chevron-down sidebar-text ml-2 text-[10px] transition-transform duration-300 {{ request()->routeIs('courses.*') || request()->routeIs('course-classes.*') ? 'rotate-180' : '' }}"></i>
                            </button>
                            <div id="courses_menu" class="overflow-hidden {{ request()->routeIs('courses.*') || request()->routeIs('course-classes.*') ? '' : 'hidden' }}">
                                <div class="ml-12 mt-2 space-y-1 border-l border-white/10 pl-4">
                                    <a href="{{ route('courses.index') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('courses.index') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Cursos</a>
                                    <a href="{{ route('course-classes.index') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('course-classes.*') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Turmas</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @elseif($sectionName === 'celulas')
                @if ($authUser->isAdmin() || $authUser->isPastor() || $authUser->isPastorZona() || $authUser->isSupervisor() || $authUser->isLider() || $authUser->isTimoteo() || $authUser->isSecretaria() || $authUser->hasRole('membro'))
                    <!-- CÉLULAS & DISCIPULADO -->
                    <div class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">Células & Discipulado</div>

                    @if ($authUser->hasRole('membro'))
                        <a href="{{ route('dashboard.membro') }}#minha-celula" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('dashboard.membro') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                            <i class="bi bi-people-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight">Minha Célula</span>
                        </a>
                    @endif

                    @if ($authUser->role !== 'membro' && $authUser->role !== 'comissao_obra')
                        <a href="{{ route('cell-meetings.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('cell-meetings.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                            <i class="bi bi-people-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight">Encontros</span>
                        </a>

                        <a href="{{ route('members.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('members.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                            <i class="bi bi-person-lines-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight">Membros</span>
                        </a>
                    @endif

                    @if ($authUser->isLider() && $authUser->ledCells()->exists())
                        <a href="{{ route('cells.attendance', $authUser->ledCells()->first()) }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('cells.attendance') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                            <i class="bi bi-calendar-check-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight">Ficha Guia</span>
                        </a>
                    @endif

                    @if ($authUser->isAdmin() || $authUser->isPastorZona() || $authUser->isSupervisor())
                        @if($authUser->isAdmin() || $authUser->isPastorZona())
                        <a href="{{ route('zones.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('zones.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                            <i class="bi bi-geo-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight">Zonas</span>
                        </a>
                        <a href="{{ route('supervisions.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('supervisions.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                            <i class="bi bi-diagram-2-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight">Supervisões</span>
                        </a>
                        @endif
                        <a href="{{ route('cells.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('cells.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                            <i class="bi bi-diagram-3-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight">Células</span>
                        </a>
                    @endif
                @endif
            @elseif($sectionName === 'financeira')
                <!-- GESTÃO FINANCEIRA & EDIFICAR -->
                <div class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">Gestão Financeira & Edificar</div>

                @if ($authUser->isAdmin() || $authUser->isEdificarManager() || $authUser->isResponsavelPacote() || $authUser->isComissaoObra())
                    <a href="{{ route('packages.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('packages.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                        <i class="bi bi-box-seam-fill text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Pacotes</span>
                    </a>
                @endif

                <div>
                    <button class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('contributions.*') || request()->routeIs('commitments.*') ? 'bg-zinc-900/50 text-white' : 'text-slate-400' }}" onclick="toggleMenu('contributions')">
                        <i class="bi bi-cash-stack text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight flex-1">Contribuições</span>
                        <i class="bi bi-chevron-down sidebar-text ml-2 text-[10px] transition-transform duration-300 {{ request()->routeIs('contributions.*') || request()->routeIs('commitments.*') ? 'rotate-180' : '' }}"></i>
                    </button>
                    <div id="contributions" class="overflow-hidden {{ request()->routeIs('contributions.*') || request()->routeIs('commitments.*') ? '' : 'hidden' }}">
                        <div class="ml-12 mt-2 space-y-1 border-l border-white/10 pl-4">
                            <a href="{{ route('commitments.index') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('commitments.*') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Meu Compromisso</a>
                            @if (!$authUser->isAdmin())
                                <a href="{{ route('contributions.index', ['mine' => 1]) }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('contributions.index') && request()->query('mine') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Minhas Contribuições</a>
                            @endif
                            @if ($authUser->isAdmin() || $authUser->isPastorSenior() || $authUser->isPastorZona() || $authUser->isSupervisor() || $authUser->isLider() || $authUser->isComissaoObra() || $authUser->isResponsavelPacote())
                                <a href="{{ route('contributions.index') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('contributions.index') && !request()->query('mine') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">
                                    @if ($authUser->isAdmin() || $authUser->isComissaoObra())Todas
                                    @elseif ($authUser->isResponsavelPacote()) Dos Meus Pacotes
                                    @elseif ($authUser->isPastorZona()) Da Zona
                                    @elseif ($authUser->isSupervisor()) Da Supervisão @else Da Célula @endif
                                </a>
                            @endif
                            @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isTesouraria())    
                                <a href="{{ route('contributions.create') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('contributions.create') ? 'text-green-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Nova Contribuição</a>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($authUser->isAdmin() || $authUser->isPastor() || $authUser->isTesouraria())
                    <div>
                        <button class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('financial.dashboard') || request()->routeIs('requisitions.*') || request()->routeIs('expenses.*') ? 'bg-zinc-900/50 text-white' : 'text-slate-400' }}" onclick="toggleMenu('financial_menu')">
                            <i class="bi bi-pie-chart-fill text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-bold tracking-tight flex-1">Financeiro</span>
                            <i class="bi bi-chevron-down sidebar-text ml-2 text-[10px] transition-transform duration-300 {{ request()->routeIs('financial.dashboard') || request()->routeIs('requisitions.*') || request()->routeIs('expenses.*') ? 'rotate-180' : '' }}"></i>
                        </button>
                        <div id="financial_menu" class="overflow-hidden {{ request()->routeIs('financial.dashboard') || request()->routeIs('requisitions.*') || request()->routeIs('expenses.*') ? '' : 'hidden' }}">
                            <div class="ml-12 mt-2 space-y-1 border-l border-white/10 pl-4">
                                <a href="{{ route('financial.dashboard') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('financial.dashboard') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Dashboard</a>
                                <a href="{{ route('requisitions.index') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('requisitions.*') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Requisições</a>
                                <a href="{{ route('expenses.index') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('expenses.*') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Despesas</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastor() || $authUser->isTesouraria())
                    <a href="{{ route('inventory-items.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('inventory-items.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                        <i class="bi bi-box-seam text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight">Inventário</span>
                    </a>
                @endif
            @endif
        @endforeach

        @if ($authUser->role !== 'membro')
            <!-- SISTEMA & RELATÓRIOS -->
            <div class="sidebar-section-header sidebar-text text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] px-4 py-4 mt-4">Sistema & Relatórios</div>

            @if ($authUser->isAdmin() || $authUser->isPastor() || $authUser->isPastorZona() || $authUser->isSupervisor() || $authUser->isComissaoObra())
                <div>
                    <button class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('reports.*') ? 'bg-zinc-900/50 text-white' : 'text-slate-400' }}" onclick="toggleMenu('reports')">
                        <i class="bi bi-bar-chart-line-fill text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-bold tracking-tight flex-1">Estatísticas</span>
                        <i class="bi bi-chevron-down sidebar-text ml-2 text-[10px] transition-transform duration-300 {{ request()->routeIs('reports.*') ? 'rotate-180' : '' }}"></i>
                    </button>
                    <div id="reports" class="overflow-hidden {{ request()->routeIs('reports.*') ? '' : 'hidden' }}">
                        <div class="ml-12 mt-2 space-y-1 border-l border-white/10 pl-4">
                            @if($authUser->role !== 'comissao_obra')
                                <a href="{{ route('reports.cell') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('reports.cell') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Célula</a>
                            @endif
                            @if ($authUser->isAdmin() || $authUser->isPastorZona() || $authUser->isSupervisor())
                                <a href="{{ route('reports.supervision') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('reports.supervision') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Supervisão</a>
                            @endif
                            @if ($authUser->isAdmin() || $authUser->isPastorZona())
                                <a href="{{ route('reports.zone') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('reports.zone') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Zona</a>
                            @endif
                            @if ($authUser->isAdmin() || $authUser->isComissaoObra())
                                <a href="{{ route('reports.global') }}" class="block py-2 text-sm transition-all duration-200 {{ request()->routeIs('reports.global') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-300' }}">Global</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if ($authUser->isAdmin())
                <a href="{{ route('users.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('users.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-person-lock text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Utilizadores</span>
                </a>
            @endif

            @if ($authUser->isAdmin() || $authUser->isSecretaria())
                <a href="{{ route('settings.index') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('settings.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
                    <i class="bi bi-gear-fill text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-bold tracking-tight">Configurações</span>
                </a>
            @endif
        @endif

        <a href="{{ route('notifications.all') }}" class="nav-item relative flex items-center px-4 py-3 rounded-2xl hover:bg-white/5 transition-all duration-300 group {{ request()->routeIs('notifications.*') ? 'bg-zinc-900 text-orange-500 border border-white/5' : 'text-slate-400' }}">
            <i class="bi bi-bell-fill text-xl flex-shrink-0"></i>
            <span class="sidebar-text ml-4 font-bold tracking-tight">Notificações</span>
            @if ($unreadNotifications > 0)
                <span class="absolute right-4 top-1/2 -translate-y-1/2 bg-red-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg shadow-red-600/20">{{ $unreadNotifications }}</span>
            @endif
        </a>
    </nav>

    <!-- User Profile Footer -->
    <div class="mt-auto border-t border-white/5 bg-black/80 backdrop-blur-md sticky bottom-0 z-20">
        <div class="p-4">
            <div class="flex items-center space-x-3 mb-4 overflow-hidden">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center flex-shrink-0 font-black text-white shadow-lg shadow-orange-600/20">
                    {{ strtoupper(substr($authUser->name, 0, 1)) }}
                </div>
                <div class="sidebar-text flex-1 min-w-0">
                    <p class="text-sm font-black text-white truncate">{{ $authUser->name }}</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider truncate">{{ ucfirst(str_replace('_', ' ', $authUser->role)) }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="sidebar-text text-slate-400 hover:text-white transition-all p-2 hover:bg-white/5 rounded-xl border border-transparent hover:border-white/10" title="Editar Perfil">
                    <i class="bi bi-gear-fill"></i>
                </a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-3 text-slate-400 hover:text-white hover:bg-red-600 transition-all duration-300 rounded-2xl font-black text-xs uppercase tracking-widest border border-white/5 hover:border-red-600 shadow-lg hover:shadow-red-600/20">
                    <i class="bi bi-power mr-2 text-lg"></i>
                    <span class="sidebar-text">Sair do Sistema</span>
                </button>
            </form>
        </div>
    </div>
</aside>