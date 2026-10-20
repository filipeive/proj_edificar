@php
    // O View Composer já passa $user e $role
    $authUser = auth()->user();
    $currentCongregation = app(\App\Services\CongregationContext::class)->get();
    $hasEdificar = app(\App\Services\CongregationContext::class)->hasEdificar();
    $isSuperAdmin = $authUser && $authUser->role === 'super_admin';
    $portalContext = session('portal_context', 'national');

    if ($isSuperAdmin) {
        if ($portalContext === 'local') {
            $isNationalArea = request()->routeIs('national.*');
        } else {
            $isExplicitLocalRoute = request()->routeIs('dashboard.admin', 'members.*', 'visitors.*', 'services.*', 'packages.*', 'financial.*', 'edificar.*', 'cells.*', 'zones.*', 'supervisions.*', 'settings.*');
            $isNationalArea = request()->routeIs('national.*') || !$isExplicitLocalRoute;
        }
    } else {
        $isNationalArea = request()->routeIs('national.*');
    }

    $pendingContributionsCount = $authUser ? $authUser->getPendingContributionsCount() : null;
    $logoPrimary = \App\Models\Setting::get('branding.logo_primary');
    $logoPrimary = $logoPrimary ? asset($logoPrimary) : asset('images/logo.png');
    $showInventoryOperacao = $authUser && ($authUser->isAdmin() || $authUser->isSecretaria());
    $showInventoryFinanceira = $authUser && ($authUser->isAdmin() || ($hasEdificar && $authUser->isEdificarManager()) || ($hasEdificar && $authUser->isComissaoObra()) || $authUser->isTesouraria() || $authUser->isPastor());
@endphp
<aside id="{{ $sidebarId ?? 'sidebar' }}" data-sidebar-id="{{ $sidebarId ?? 'sidebar' }}"
    @if(($sidebarId ?? 'sidebar') === 'sidebar-desktop')
        :class="sidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'"
    @endif
    class="app-sidebar {{ ($sidebarId ?? 'sidebar') === 'sidebar-desktop' ? '' : 'sidebar-expanded' }} bg-gray-950 text-white flex flex-col h-full min-h-0 overflow-hidden transition-all duration-300 ease-in-out border-r border-gray-800/80">
    <!-- Header -->
    <div class="sidebar-header px-4 py-3.5 border-b border-gray-800/80 flex items-center justify-between bg-gray-950 sticky top-0 z-20">
        <div class="flex items-center space-x-2.5 overflow-hidden">
            <div class="logo-wrapper flex-shrink-0 p-1.5 bg-orange-700 rounded-lg shadow-sm">
                <img src="{{ $logoPrimary }}" alt="Logo" class="logo-img h-6 w-auto">
            </div>
            <div class="sidebar-text min-w-0">
                <h1 class="text-xs font-black tracking-tight text-white uppercase leading-none truncate max-w-[165px]" title="{{ $isNationalArea ? 'Consola Nacional' : ($currentCongregation->name ?? 'Life - APP') }}">
                    {{ $isNationalArea ? 'Consola Nacional' : ($currentCongregation->name ?? 'Life - APP') }}
                </h1>
                <p class="text-[9px] text-orange-400 font-bold uppercase tracking-[0.16em] mt-1 truncate">
                    {{ $isNationalArea ? 'Gestão Nacional' : ($hasEdificar ? 'Gestão & Edificar' : 'Portal Eclesiástico') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 min-h-0 px-2.5 py-3 space-y-0.5 overflow-y-auto custom-scrollbar">
        @if($isNationalArea)
            <!-- NAVEGAÇÃO NACIONAL (Super Admin) -->
            <div class="space-y-0.5 pb-2">
                <div class="sidebar-section-header sidebar-text text-gray-500 text-[9px] font-black uppercase tracking-[0.16em] px-2.5 py-1.5">
                    Painel Central
                </div>
                <a href="{{ route('national.dashboard') }}" data-tooltip="Painel Nacional"
                    class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('national.dashboard') ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <i class="bi bi-speedometer2 text-sm flex-shrink-0"></i>
                    <span class="sidebar-text ml-3 tracking-tight">Painel Nacional</span>
                </a>
                <a href="{{ route('national.congregations.index') }}" data-tooltip="Congregações"
                    class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('national.congregations.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <i class="bi bi-buildings text-sm flex-shrink-0"></i>
                    <span class="sidebar-text ml-3 tracking-tight">Congregações</span>
                </a>
                <a href="{{ route('national.reports.index') }}" data-tooltip="Relatório Nacional"
                    class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('national.reports.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <i class="bi bi-file-earmark-bar-graph text-sm flex-shrink-0"></i>
                    <span class="sidebar-text ml-3 tracking-tight">Relatório Nacional</span>
                </a>
                <a href="{{ route('national.congregations.create') }}" data-tooltip="Registar Congregação"
                    class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group text-gray-400 hover:bg-white/5 hover:text-white">
                    <i class="bi bi-plus-circle-fill text-sm flex-shrink-0 text-emerald-400"></i>
                    <span class="sidebar-text ml-3 tracking-tight">Registar Congregação</span>
                </a>
            </div>

            <div class="pt-2 border-t border-gray-800/80">
                <div class="sidebar-section-header sidebar-text text-gray-500 text-[9px] font-black uppercase tracking-[0.16em] px-2.5 py-1.5">
                    Acesso Local
                </div>
                <a href="{{ route('national.switch.local') }}" data-tooltip="Ver Congregação Local"
                    class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group text-gray-400 hover:bg-white/5 hover:text-white">
                    <i class="bi bi-box-arrow-in-right text-sm flex-shrink-0 text-orange-400"></i>
                    <span class="sidebar-text ml-3 tracking-tight">Portal Local (Life Church)</span>
                </a>
            </div>
        @else
            <!-- DASHBOARD LOCAL -->
            <div class="space-y-0.5 pb-2">
                @if($authUser && $authUser->role === 'super_admin')
                    <a href="{{ route('national.switch.national') }}" data-tooltip="Painel Nacional"
                        class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group bg-orange-600/10 text-orange-400 border border-orange-500/30 mb-1.5 hover:bg-orange-600/20">
                        <i class="bi bi-globe-americas text-sm flex-shrink-0 text-orange-400"></i>
                        <span class="sidebar-text ml-3 tracking-tight">Consola Nacional</span>
                    </a>
                @endif
                <a href="{{ $isSuperAdmin && session('portal_context') === 'local' ? route('dashboard.admin') : route('dashboard') }}" data-tooltip="Dashboard Geral"
                    class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('dashboard', 'dashboard.admin') ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <i class="bi bi-grid-1x2-fill text-sm flex-shrink-0"></i>
                    <span class="sidebar-text ml-3 tracking-tight">Dashboard</span>
                </a>

                @if ($hasEdificar && $authUser && $authUser->hasPermission('dashboard_edificar'))
                    <a href="{{ route('edificar.dashboard') }}" data-tooltip="Painel Edificar"
                        class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('edificar.dashboard') ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                        <i class="bi bi-graph-up-arrow text-sm flex-shrink-0"></i>
                        <span class="sidebar-text ml-3 tracking-tight">Painel Edificar</span>
                    </a>
                @endif

                @if ($hasEdificar && $authUser && $authUser->hasPermission('dashboard_packages'))
                    <a href="{{ route('packages.dashboard') }}" data-tooltip="Painel do Pacote"
                        class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('packages.dashboard') ? 'bg-orange-700 text-white shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                        <i class="bi bi-speedometer2 text-sm flex-shrink-0"></i>
                        <span class="sidebar-text ml-3 tracking-tight">Painel do Pacote</span>
                    </a>
                @endif
            </div>

        <!-- ATALHOS RÁPIDOS (Frequent Actions) -->
        @if($authUser && ($authUser->isLider() || $authUser->isResponsavelPacote() || $authUser->isAdmin() || $authUser->isComissaoObra()))
            <div class="sidebar-quick-actions pb-1">
                <div class="sidebar-section-header sidebar-text text-gray-500 text-[9px] font-black uppercase tracking-[0.16em] px-2.5 py-1">Atalhos Rápidos</div>
                <div class="grid grid-cols-2 gap-1.5 px-1 py-1">
                    @if($authUser->isSecretaria() || $authUser->isAdmin() || $authUser->isAdministracao() || $authUser->isPastorZona())
                        <a href="{{ route('services.create-teaching') }}" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-orange-600/20 hover:border-orange-500/40 transition-colors group">
                            <i class="bi bi-mortarboard text-orange-400 text-xs shrink-0"></i>
                            <span class="text-[10px] font-bold text-gray-300 group-hover:text-white truncate">Estudo</span>
                        </a>
                        <a href="{{ route('services.create') }}" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-blue-600/20 hover:border-blue-500/40 transition-colors group">
                            <i class="bi bi-plus-circle text-blue-400 text-xs shrink-0"></i>
                            <span class="text-[10px] font-bold text-gray-300 group-hover:text-white truncate">Culto</span>
                        </a>
                    @endif
                    @if($authUser->isLider() || $authUser->isAdmin() || $authUser->isSupervisor() || $authUser->isPastorZona())
                        <a href="{{ route('cell-meetings.create') }}" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-orange-600/20 hover:border-orange-500/40 transition-colors group">
                            <i class="bi bi-people text-orange-400 text-xs shrink-0"></i>
                            <span class="text-[10px] font-bold text-gray-300 group-hover:text-white truncate">Encontro</span>
                        </a>
                    @endif
                    @if($authUser->isResponsavelPacote() || $authUser->isAdmin())
                        <a href="{{ route('contributions.create') }}" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-blue-600/20 hover:border-blue-500/40 transition-colors group">
                            <i class="bi bi-cash-coin text-blue-400 text-xs shrink-0"></i>
                            <span class="text-[10px] font-bold text-gray-300 group-hover:text-white truncate">Contribuição</span>
                        </a>
                    @endif
                    @if($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastorZona() || $authUser->isAdministracao())
                        <a href="{{ route('visitors.create') }}" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-emerald-600/20 hover:border-emerald-500/40 transition-colors group">
                            <i class="bi bi-person-plus text-emerald-400 text-xs shrink-0"></i>
                            <span class="text-[10px] font-bold text-gray-300 group-hover:text-white truncate">Visitante</span>
                        </a>
                    @endif
                    @if($authUser->isAdmin() || $authUser->isTesouraria())
                        <a href="{{ route('requisitions.create') }}" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-rose-600/20 hover:border-rose-500/40 transition-colors group">
                            <i class="bi bi-file-earmark-plus text-rose-400 text-xs shrink-0"></i>
                            <span class="text-[10px] font-bold text-gray-300 group-hover:text-white truncate">Requisição</span>
                        </a>
                    @endif
                    @if($authUser->isAdmin() || $authUser->isComissaoObra())
                        <a href="{{ route('contributions.index', ['status' => 'pendente']) }}" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white/5 border border-white/10 hover:bg-amber-600/20 hover:border-amber-500/40 transition-colors group">
                            <i class="bi bi-patch-check text-amber-400 text-xs shrink-0"></i>
                            <span class="text-[10px] font-bold text-gray-300 group-hover:text-white truncate">Validar</span>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @php
            $order = ['operacao', 'celulas', 'financeira'];
            if ($authUser) {
                if ($authUser->isResponsavelPacote() || $authUser->isTesouraria()) {
                    $order = ['financeira', 'celulas', 'operacao'];
                } elseif ($authUser->isLider() || $authUser->isSupervisor() || $authUser->isPastorZona()) {
                    $order = ['celulas', 'operacao', 'financeira'];
                }
            }
        @endphp

        @foreach($order as $sectionName)
            @if($sectionName === 'operacao')
                @if ($authUser && ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isPastor() || $authUser->isPastorZona() || $authUser->isSupervisor() || $authUser->isAdministracao() || $authUser->isLider()))
                    <!-- OPERAÇÃO ECLESIÁSTICA -->
                    <div class="sidebar-section-header sidebar-text text-gray-500 text-[9px] font-black uppercase tracking-[0.16em] px-2.5 pt-3 pb-1">Operação Eclesiástica</div>

                    @if ($authUser && $authUser->hasPermission('menu_services'))
                        <a href="{{ route('services.index') }}" data-tooltip="Cultos" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('services.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-journal-bookmark-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Cultos</span>
                        </a>
                    @endif

                    @if ($authUser && $authUser->hasPermission('menu_events'))
                        <a href="{{ route('events.index') }}" data-tooltip="Eventos" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('events.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-calendar-check-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Eventos</span>
                        </a>
                    @endif

                    @if ($authUser && $authUser->hasPermission('menu_weddings'))
                        <a href="{{ route('weddings.index') }}" data-tooltip="Casamentos" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('weddings.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-heart-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Casamentos</span>
                        </a>
                    @endif

                    @if ($authUser && ($authUser->hasPermission('menu_visitors') || $authUser->isLider()))
                        <a href="{{ route('visitors.index') }}" data-tooltip="Visitantes" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('visitors.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-person-plus-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Visitantes</span>
                        </a>
                    @endif

                    @if ($authUser && $authUser->hasPermission('menu_courses'))
                        <div x-data="{ open: {{ request()->routeIs('courses.*') || request()->routeIs('course-classes.*') || request()->routeIs('couple-enrollments.*') || request()->routeIs('ministerial-enrollments.*') ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open; if (window.innerWidth >= 768 && !sidebarOpen) sidebarOpen = true" class="nav-item relative w-full text-left flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('courses.*') || request()->routeIs('course-classes.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}" data-tooltip="Escola Ministerial">
                                <i class="bi bi-mortarboard-fill text-sm flex-shrink-0"></i>
                                <span class="sidebar-text ml-3 tracking-tight flex-1">Escola Ministerial</span>
                                <i class="bi bi-chevron-down sidebar-text ml-2 text-[10px] transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" x-cloak class="overflow-hidden">
                                <div class="ml-8 mt-1 space-y-0.5 border-l border-gray-800 pl-3">
                                    <a href="{{ route('courses.index') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('courses.index') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Cursos</a>
                                    @if ($authUser && $authUser->hasPermission('menu_public_enrollments'))
                                        @if(!$authUser->isSupervisor())
                                            <a href="{{ route('couple-enrollments.index') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('couple-enrollments.*') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Inscrições de Casais</a>
                                        @endif
                                        <a href="{{ route('ministerial-enrollments.index') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('ministerial-enrollments.*') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Inscrições Individuais</a>
                                    @endif
                                    @if ($authUser && ((!$authUser->isSupervisor() && !$authUser->isSecretaria() && !$authUser->isPastorZona()) || (($authUser->isSupervisor() || $authUser->isSecretaria() || $authUser->isPastorZona()) && $authUser->hasAnyCourseEnrollment())))
                                        <a href="{{ route('course-classes.index') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('course-classes.*') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Turmas</a>
                                    @endif
                                    @if ($authUser && $authUser->isAdmin())
                                        <a href="{{ route('settings.public-forms') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('settings.public-forms') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Config. Formulários</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($authUser && $authUser->hasPermission('menu_quarterly_reports'))
                        <a href="{{ route('quarterly-reports.index') }}" data-tooltip="Relatórios Trimestrais" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('quarterly-reports.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-file-earmark-bar-graph-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Relatórios Trimestrais</span>
                        </a>
                    @endif

                    @if ($authUser && $authUser->hasPermission('menu_inventory'))
                        <a href="{{ route('inventory-items.index') }}" data-tooltip="Inventário" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('inventory-items.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-box-seam-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Inventário</span>
                        </a>
                    @endif
                @endif
            @elseif($sectionName === 'celulas')
                @if ($authUser && ($authUser->isAdmin() || $authUser->isPastor() || $authUser->isPastorZona() || $authUser->isSupervisor() || $authUser->isLider() || $authUser->isTimoteo() || $authUser->isSecretaria() || $authUser->hasRole('membro')))
                    <!-- CÉLULAS & DISCIPULADO -->
                    <div class="sidebar-section-header sidebar-text text-gray-500 text-[9px] font-black uppercase tracking-[0.16em] px-2.5 pt-3 pb-1">Células & Discipulado</div>

                    @if ($authUser && ($authUser->hasRole('membro') || $authUser->isLider() || $authUser->isTimoteo()))
                        @php
                            $myCellLink = route('dashboard.membro') . '#minha-celula';
                            $myCellLabel = 'Minha Célula (Gestão)';
                            $myCellTooltip = 'Minha Célula (Gestão)';
                            $managedCellIds = collect();

                            if ($authUser->isLider() && $authUser->isLiderOfAnyCell()) {
                                $managedCellIds = $authUser->getManagedCellIds();
                                $myCellLink = $managedCellIds->count() > 1
                                    ? route('cells.index')
                                    : route('cells.show', $managedCellIds->first());
                                $myCellLabel = $managedCellIds->count() > 1 ? 'Minhas Células' : 'Minha Célula (Gestão)';
                                $myCellTooltip = $managedCellIds->count() > 1 ? 'Minhas Células' : 'Minha Célula (Gestão)';
                            }
                        @endphp
                        <a href="{{ $myCellLink }}" data-tooltip="{{ $myCellTooltip }}" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('dashboard.membro') || request()->routeIs('cells.show') || request()->routeIs('cells.index') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-people-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">{{ $myCellLabel }}</span>
                            @if($managedCellIds->count() > 1)
                                <span class="sidebar-text ml-auto text-[10px] font-black bg-orange-600/20 text-orange-400 rounded-full px-1.5 py-0.5">{{ $managedCellIds->count() }}</span>
                            @endif
                        </a>
                    @endif

                    @if ($authUser && $authUser->hasPermission('menu_cell_meetings'))
                        <a href="{{ route('cell-meetings.index') }}" data-tooltip="Encontros" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('cell-meetings.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-people-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Encontros</span>
                        </a>
                    @endif

                    @if ($authUser && $authUser->hasPermission('menu_members'))
                        <a href="{{ route('members.index') }}" data-tooltip="Membros" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('members.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-person-lines-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Membros</span>
                        </a>
                    @endif

                    @if ($authUser && $authUser->isLider() && $authUser->isLiderOfAnyCell())
                        @php
                            $managedCellIds = $authUser->getManagedCellIds();
                            $attendanceLink = $managedCellIds->count() > 1
                                ? route('dashboard.lider')
                                : route('cells.attendance', $managedCellIds->first());
                        @endphp
                        <a href="{{ $attendanceLink }}" data-tooltip="Ficha Guia" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('cells.attendance') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-calendar-check-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Ficha de Célula</span>
                        </a>
                    @endif

                    @if ($authUser && $authUser->hasPermission('menu_zones'))
                        <a href="{{ route('zones.index') }}" data-tooltip="Zonas" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('zones.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-geo-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Zonas</span>
                        </a>
                    @endif

                    @if ($authUser && $authUser->hasPermission('menu_supervisions'))
                        <a href="{{ route('supervisions.index') }}" data-tooltip="Supervisões" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('supervisions.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-diagram-2-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Supervisões</span>
                        </a>
                    @endif

                    @if ($authUser && $authUser->hasPermission('menu_cells'))
                        <a href="{{ route('cells.index') }}" data-tooltip="Células" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('cells.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <i class="bi bi-diagram-3-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight">Células</span>
                        </a>
                    @endif
                @endif
            @elseif($sectionName === 'financeira')
                <!-- GESTÃO FINANCEIRA -->
                <div class="sidebar-section-header sidebar-text text-gray-500 text-[9px] font-black uppercase tracking-[0.16em] px-2.5 pt-3 pb-1">{{ $hasEdificar ? 'Gestão Financeira & Edificar' : 'Gestão Financeira' }}</div>

                @if ($hasEdificar && $authUser && $authUser->hasPermission('menu_packages'))
                    <a href="{{ route('packages.index') }}" data-tooltip="Pacotes" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('packages.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                        <i class="bi bi-box-seam-fill text-sm flex-shrink-0"></i>
                        <span class="sidebar-text ml-3 tracking-tight">Pacotes</span>
                    </a>
                @endif

                <div x-data="{ open: {{ request()->routeIs('contributions.*') || request()->routeIs('commitments.*') ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open; if (window.innerWidth >= 768 && !sidebarOpen) sidebarOpen = true" class="nav-item relative w-full text-left flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('contributions.*') || request()->routeIs('commitments.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}" data-tooltip="Contribuições">
                        <i class="bi bi-cash-stack text-sm flex-shrink-0"></i>
                        <span class="sidebar-text ml-3 tracking-tight flex-1">Contribuições</span>
                        <i class="bi bi-chevron-down sidebar-text ml-2 text-[10px] transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-cloak class="overflow-hidden">
                        <div class="ml-8 mt-1 space-y-0.5 border-l border-gray-800 pl-3">
                            @if ($hasEdificar)
                                <a href="{{ route('commitments.index') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('commitments.*') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Meu Compromisso</a>
                            @endif
                            @if ($authUser && !$authUser->isAdmin())
                                <a href="{{ route('contributions.index', ['mine' => 1]) }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('contributions.index') && request()->query('mine') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Minhas Contribuições</a>
                            @endif
                            @if ($authUser && ($authUser->hasRole('membro') || $authUser->isLider() || $authUser->isSupervisor() || $authUser->isTimoteo()))
                                <a href="{{ route('contributions.index', ['scope' => 'my_cell']) }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('contributions.index') && request()->query('scope') === 'my_cell' ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">
                                    Minha Célula (Financeiro)
                                </a>
                            @endif
                            @if ($authUser && $authUser->hasPermission('menu_contributions_all'))
                                <a href="{{ route('contributions.index') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('contributions.index') && !request()->query('mine') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">
                                    @if ($authUser->isAdmin() || ($hasEdificar && $authUser->isComissaoObra())) Todas
                                    @elseif ($hasEdificar && $authUser->isResponsavelPacote()) Dos Meus Pacotes
                                    @elseif ($authUser->isPastorZona()) Da Zona
                                    @elseif ($authUser->isSupervisor()) Da Supervisão @elseif($authUser->isLider()) Da Célula @else Geral @endif
                                </a>
                            @endif
                            @if ($authUser && ($authUser->isAdmin() || $authUser->isComissaoObra() || $authUser->isPastorZona()))
                                <a href="{{ route('contributions.pending') }}" class="flex items-center justify-between py-1.5 text-xs transition-colors {{ request()->routeIs('contributions.pending') ? 'text-amber-400 font-bold' : 'text-gray-400 hover:text-white' }}">
                                    <span>Pendentes</span>
                                    @if($pendingContributionsCount !== null && $pendingContributionsCount > 0)
                                        <span class="ml-2 text-[10px] font-black px-1.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            {{ $pendingContributionsCount > 99 ? '99+' : $pendingContributionsCount }}
                                        </span>
                                    @endif
                                </a>
                            @endif
                            @if ($authUser && ($authUser->isAdmin() || $authUser->isSecretaria() || $authUser->isTesouraria()))    
                                <a href="{{ route('contributions.create') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('contributions.create') ? 'text-emerald-400 font-bold' : 'text-gray-400 hover:text-white' }}">Nova Contribuição</a>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($authUser && $authUser->hasPermission('menu_finance'))
                    <div x-data="{ open: {{ request()->routeIs('financial.dashboard') || request()->routeIs('requisitions.*') || request()->routeIs('expenses.*') ? 'true' : 'false' }} }">
                        <button type="button" @click="open = !open; if (window.innerWidth >= 768 && !sidebarOpen) sidebarOpen = true" class="nav-item relative w-full text-left flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('financial.dashboard') || request()->routeIs('requisitions.*') || request()->routeIs('expenses.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}" data-tooltip="Financeiro">
                            <i class="bi bi-pie-chart-fill text-sm flex-shrink-0"></i>
                            <span class="sidebar-text ml-3 tracking-tight flex-1">Financeiro</span>
                            <i class="bi bi-chevron-down sidebar-text ml-2 text-[10px] transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-cloak class="overflow-hidden">
                            <div class="ml-8 mt-1 space-y-0.5 border-l border-gray-800 pl-3">
                                <a href="{{ route('financial.dashboard') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('financial.dashboard') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Dashboard</a>
                                <a href="{{ route('requisitions.index') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('requisitions.*') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Requisições</a>
                                <a href="{{ route('expenses.index') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('expenses.*') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Despesas</a>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showInventoryFinanceira && !$showInventoryOperacao)
                    <a href="{{ route('inventory-items.index') }}" data-tooltip="Inventário" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('inventory-items.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                        <i class="bi bi-box-seam text-sm flex-shrink-0"></i>
                        <span class="sidebar-text ml-3 tracking-tight">Inventário</span>
                    </a>
                @endif
            @endif
        @endforeach

        @if ($authUser && $authUser->role !== 'membro')
            <!-- SISTEMA & RELATÓRIOS -->
            <div class="sidebar-section-header sidebar-text text-gray-500 text-[9px] font-black uppercase tracking-[0.16em] px-2.5 pt-3 pb-1">Sistema & Relatórios</div>

            @if ($authUser && $authUser->hasPermission('menu_stats'))
                <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open; if (window.innerWidth >= 768 && !sidebarOpen) sidebarOpen = true" class="nav-item relative w-full text-left flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('reports.*') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}" data-tooltip="Estatísticas">
                        <i class="bi bi-bar-chart-line-fill text-sm flex-shrink-0"></i>
                        <span class="sidebar-text ml-3 tracking-tight flex-1">Estatísticas</span>
                        <i class="bi bi-chevron-down sidebar-text ml-2 text-[10px] transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-cloak class="overflow-hidden">
                        <div class="ml-8 mt-1 space-y-0.5 border-l border-gray-800 pl-3">
                            @if($authUser && $authUser->role !== 'comissao_obra')
                                <a href="{{ route('reports.cell') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('reports.cell') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Célula</a>
                            @endif
                            @if ($authUser && ($authUser->isAdmin() || $authUser->isPastorZona() || $authUser->isSupervisor()))
                                <a href="{{ route('reports.supervision') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('reports.supervision') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Supervisão</a>
                            @endif
                            @if ($authUser && ($authUser->isAdmin() || $authUser->isPastorZona()))
                                <a href="{{ route('reports.zone') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('reports.zone') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Zona</a>
                            @endif
                            @if ($authUser->isAdmin() || $authUser->isComissaoObra())
                                <a href="{{ route('reports.global') }}" class="block py-1.5 text-xs transition-colors {{ request()->routeIs('reports.global') ? 'text-orange-400 font-bold' : 'text-gray-400 hover:text-white' }}">Global</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if ($authUser && $authUser->hasPermission('menu_users'))
                <a href="{{ route('users.index') }}" data-tooltip="Utilizadores" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('users.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <i class="bi bi-person-lock text-sm flex-shrink-0"></i>
                    <span class="sidebar-text ml-3 tracking-tight">Utilizadores</span>
                </a>
            @endif

            @if ($authUser && $authUser->hasPermission('menu_settings'))
                <a href="{{ route('settings.index') }}" data-tooltip="Configurações" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('settings.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <i class="bi bi-gear-fill text-sm flex-shrink-0"></i>
                    <span class="sidebar-text ml-3 tracking-tight">Configurações</span>
                </a>
            @endif
        @endif

        <a href="{{ route('notifications.all') }}" data-tooltip="Notificações" class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('notifications.*') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i class="bi bi-bell-fill text-sm flex-shrink-0"></i>
            <span class="sidebar-text ml-3 tracking-tight">Notificações</span>
            @if ($unreadNotifications > 0)
                <span class="absolute right-3 top-1/2 -translate-y-1/2 bg-red-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">{{ $unreadNotifications }}</span>
            @endif
        </a>

        <!-- FORMULÁRIOS PÚBLICOS -->
        @if($authUser && !$authUser->isAdministracao() && !$authUser->isSupervisor() && !$authUser->isLider() && $authUser->role !== 'membro')
        <div class="sidebar-section-header sidebar-text text-gray-500 text-[9px] font-black uppercase tracking-[0.16em] px-2.5 pt-3 pb-1">Formulários Públicos</div>
        <div class="space-y-0.5">
            <a href="{{ route('public.forms.pre-marital') }}" data-tooltip="Inscrição Pré-Marital"
                class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('public.forms.pre-marital') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <i class="bi bi-clipboard-heart-fill text-sm flex-shrink-0"></i>
                <span class="sidebar-text ml-3 tracking-tight">Inscrição Pré‑Marital</span>
            </a>
            <a href="{{ route('public.reports.quarterly') }}" data-tooltip="Relatório Trimestral"
                class="nav-item relative flex items-center px-3 py-2 rounded-lg text-xs font-bold transition-colors group {{ request()->routeIs('public.reports.quarterly') ? 'bg-white/10 text-orange-400 border border-white/10' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <i class="bi bi-file-earmark-text-fill text-sm flex-shrink-0"></i>
                <span class="sidebar-text ml-3 tracking-tight">Relatório Trimestral</span>
            </a>
        </div>
        @endif
        @endif
    </nav>

    <!-- User Profile Footer -->
    @if($authUser)
    <div class="mt-auto border-t border-gray-800/80 bg-gray-950 sticky bottom-0 z-20 p-3">
        <div class="flex items-center space-x-2.5 mb-2.5 overflow-hidden">
            <div class="w-8 h-8 rounded-lg bg-orange-700 flex items-center justify-center flex-shrink-0 text-xs font-black text-white shadow-sm">
                {{ strtoupper(substr($authUser->name, 0, 1)) }}
            </div>
            <div class="sidebar-text flex-1 min-w-0">
                <p class="text-xs font-bold text-white truncate">{{ $authUser->name }}</p>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider truncate">{{ ucfirst(str_replace('_', ' ', $authUser->role)) }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="sidebar-text text-gray-400 hover:text-white transition-colors p-1.5 hover:bg-white/5 rounded-lg" title="Editar Perfil">
                <i class="bi bi-gear-fill text-xs"></i>
            </a>
        </div>
        <!-- Logout Button (Universal) -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" data-tooltip="Sair do Sistema" class="w-full flex items-center justify-center px-3 py-2 text-gray-300 hover:text-white hover:bg-red-600/90 transition-colors rounded-lg font-bold text-xs border border-gray-800 hover:border-red-600 group">
                <i class="bi bi-box-arrow-right mr-2 text-sm"></i>
                <span class="sidebar-text">Sair do Sistema</span>
            </button>
        </form>
    </div>
    @else
    <div class="mt-auto border-t border-gray-800/80 bg-gray-950 sticky bottom-0 z-20 p-3">
        <a href="{{ route('login') }}" data-tooltip="Entrar" class="w-full flex items-center justify-center px-3 py-2 bg-orange-700 text-white rounded-lg font-bold text-xs">
            <i class="bi bi-box-arrow-in-right mr-2 text-sm"></i>
            <span class="sidebar-text">Entrar</span>
        </a>
    </div>
    @endif
</aside>
