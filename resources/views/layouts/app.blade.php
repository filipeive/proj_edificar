<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Projeto Edificar')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .sidebar-collapsed {
            width: 80px;
        }

        .sidebar-expanded {
            width: 280px;
        }

        .sidebar-text {
            transition: opacity 0.2s ease-in-out;
        }

        .sidebar-collapsed .sidebar-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar-collapsed .menu-label {
            display: none;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        .bi-chevron-down {
            transition: transform 0.3s ease-in-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
            }
            to {
                opacity: 1;
                max-height: 500px;
            }
        }

        .submenu-enter {
            animation: slideDown 0.3s ease-out;
        }

        .toast-enter {
            animation: slideInRight 0.4s ease-out;
        }

        .toast-exit {
            animation: slideOutRight 0.3s ease-out;
        }

        /* Mobile Menu Overlay */
        .mobile-overlay {
            transition: opacity 0.3s ease-in-out;
        }

        /* Smooth transitions */
        aside {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Mobile styles */
        @media (max-width: 768px) {
            aside {
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh;
                z-index: 50;
                transform: translateX(-100%);
            }

            aside.mobile-open {
                transform: translateX(0);
            }

            .sidebar-collapsed {
                width: 280px;
            }
        }

        /* Tooltip for collapsed sidebar */
        .tooltip {
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 10px;
            padding: 6px 12px;
            background-color: #1f2937;
            color: white;
            border-radius: 6px;
            font-size: 0.875rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
            z-index: 1000;
        }

        .sidebar-collapsed .nav-item:hover .tooltip {
            opacity: 1;
        }

        /* Badge styles */
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
        }

        /* Scrollbar customization */
        aside::-webkit-scrollbar {
            width: 6px;
        }

        aside::-webkit-scrollbar-track {
            background: #1f2937;
        }

        aside::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 3px;
        }

        aside::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden mobile-overlay md:hidden"
        onclick="toggleMobileSidebar()"></div>

    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-expanded bg-gradient-to-b from-gray-900 to-gray-800 text-white flex flex-col overflow-y-auto shadow-2xl">
            <!-- Header -->
            <div class="px-6 py-6 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="bg-blue-600 p-2 rounded-lg flex-shrink-0">
                        <i class="bi bi-building text-2xl"></i>
                    </div>
                    <div class="sidebar-text">
                        <h1 class="text-xl font-bold tracking-tight">Edificar</h1>
                        <p class="text-xs text-gray-400">Sistema de Contribuições</p>
                    </div>
                </div>
                <button onclick="toggleSidebar()" class="hidden md:block text-gray-400 hover:text-white transition-colors p-2 hover:bg-gray-700 rounded-lg">
                    <i class="bi bi-layout-sidebar text-xl"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                @php
                    $user = auth()->user();
                    $role = $user->role ?? 'membro';
                    $can = function ($permission) use ($user) {
                        $permissions = [
                            'admin' => ['view_global_report', 'view_zone_report', 'view_supervision_report', 'view_cell_report'],
                            'pastor_zona' => ['view_zone_report', 'view_supervision_report', 'view_cell_report'],
                            'supervisor' => ['view_supervision_report', 'view_cell_report'],
                            'lider_celula' => ['view_cell_report'],
                            'membro' => [],
                        ];
                        return in_array($permission, $permissions[$user->role] ?? []);
                    };
                @endphp

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                    <i class="bi bi-speedometer2 text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-medium">Dashboard</span>
                    <span class="tooltip">Dashboard</span>
                </a>

                <!-- Contribuições -->
                <div>
                    <button class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group"
                        onclick="toggleMenu('contributions')">
                        <i class="bi bi-cash-coin text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium flex-1">Contribuições</span>
                        <i class="bi bi-chevron-down sidebar-text ml-2 text-xs {{ request()->routeIs('contributions.*') ? 'rotate-180' : '' }}"></i>
                        <span class="tooltip">Contribuições</span>
                    </button>
                    <div id="contributions" class="submenu-enter overflow-hidden {{ request()->routeIs('contributions.*') ? '' : 'hidden' }}">
                        <div class="ml-8 mt-2 space-y-1 border-l-2 border-gray-700 pl-4">
                            @if ($role !== 'admin')
                            <a href="{{ route('contributions.index', ['mine' => 1]) }}"
                                class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->routeIs('contributions.index') && request()->query('mine') ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                <i class="bi bi-person-circle mr-2"></i>Minhas Contribuições
                            </a>
                            @endif
                            
                            @if (in_array($role, ['admin', 'pastor_zona', 'supervisor', 'lider_celula']))
                            <a href="{{ route('contributions.index') }}"
                                class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->routeIs('contributions.index') && !request()->query('mine') ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                <i class="bi bi-list-ul mr-2"></i>
                                @if ($role === 'admin') Todas as Contribuições
                                @elseif ($role === 'pastor_zona') Contribuições da Zona
                                @elseif ($role === 'supervisor') Contribuições da Supervisão
                                @else Contribuições da Célula
                                @endif
                            </a>
                            @endif

                            @if (in_array($role, ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin']))
                            <a href="{{ route('contributions.create') }}"
                                class="block px-4 py-2 text-sm rounded-lg hover:bg-green-600/20 transition {{ request()->routeIs('contributions.create') ? 'text-green-400 bg-green-600/20' : 'text-gray-300' }}">
                                <i class="bi bi-plus-circle mr-2"></i>Nova Contribuição
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Criar Membro -->
                @if (auth()->user()->role !== 'membro')
                <div>
                    <button class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group"
                        onclick="toggleMenu('members')">
                        <i class="bi bi-person-plus text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium flex-1">Membros</span>
                        <i class="bi bi-chevron-down sidebar-text ml-2 text-xs"></i>
                        <span class="tooltip">Membros</span>
                    </button>
                    <div id="members" class="hidden overflow-hidden">
                        <div class="ml-8 mt-2 space-y-1 border-l-2 border-gray-700 pl-4">
                            <a href="{{ route('members.create') }}"
                                class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition text-gray-300">
                                <i class="bi bi-plus-circle mr-2"></i>Novo Membro
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Compromissos -->
                <a href="{{ route('commitments.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('commitments.*') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                    <i class="bi bi-handshake text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-medium">Meu Compromisso</span>
                    <span class="tooltip">Meu Compromisso</span>
                </a>

                <!-- Relatórios -->
                @if ($role !== 'membro')
                <div>
                    <button class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group"
                        onclick="toggleMenu('reports')">
                        <i class="bi bi-file-earmark-bar-graph text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium flex-1">Relatórios</span>
                        <i class="bi bi-chevron-down sidebar-text ml-2 text-xs {{ request()->routeIs('reports.*') ? 'rotate-180' : '' }}"></i>
                        <span class="tooltip">Relatórios</span>
                    </button>
                    <div id="reports" class="submenu-enter overflow-hidden {{ request()->routeIs('reports.*') ? '' : 'hidden' }}">
                        <div class="ml-8 mt-2 space-y-1 border-l-2 border-gray-700 pl-4">
                            @if ($can('view_cell_report'))
                            <a href="{{ route('reports.cell') }}"
                                class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->routeIs('reports.cell') ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                <i class="bi bi-people mr-2"></i>Célula
                            </a>
                            @endif
                            @if ($can('view_supervision_report'))
                            <a href="{{ route('reports.supervision') }}"
                                class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->routeIs('reports.supervision') ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                <i class="bi bi-diagram-3 mr-2"></i>Supervisão
                            </a>
                            @endif
                            @if ($can('view_zone_report'))
                            <a href="{{ route('reports.zone') }}"
                                class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->routeIs('reports.zone') ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                <i class="bi bi-map mr-2"></i>Zona
                            </a>
                            @endif
                            @if ($can('view_global_report'))
                            <a href="{{ route('reports.global') }}"
                                class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->routeIs('reports.global') ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                <i class="bi bi-globe mr-2"></i>Global
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Admin Section -->
                @if ($role === 'admin')
                <div class="pt-6">
                    <div class="flex items-center px-4 mb-3">
                        <div class="h-px bg-gray-700 flex-1"></div>
                        <span class="sidebar-text px-3 text-xs uppercase font-bold text-gray-500">Administração</span>
                        <div class="h-px bg-gray-700 flex-1"></div>
                    </div>

                    <a href="{{ route('zones.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('zones.*') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                        <i class="bi bi-map text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium">Zonas</span>
                        <span class="tooltip">Zonas</span>
                    </a>

                    <a href="{{ route('supervisions.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('supervisions.*') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                        <i class="bi bi-diagram-3 text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium">Supervisões</span>
                        <span class="tooltip">Supervisões</span>
                    </a>

                    <a href="{{ route('cells.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('cells.*') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                        <i class="bi bi-people-fill text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium">Células</span>
                        <span class="tooltip">Células</span>
                    </a>

                    <div>
                        <button class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group"
                            onclick="toggleMenu('users')">
                            <i class="bi bi-person-badge text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-medium flex-1">Utilizadores</span>
                            <i class="bi bi-chevron-down sidebar-text ml-2 text-xs {{ request()->routeIs('users.*') ? 'rotate-180' : '' }}"></i>
                            <span class="tooltip">Utilizadores</span>
                        </button>
                        <div id="users" class="submenu-enter overflow-hidden {{ request()->routeIs('users.*') ? '' : 'hidden' }}">
                            <div class="ml-8 mt-2 space-y-1 border-l-2 border-gray-700 pl-4">
                                <a href="{{ route('users.index') }}"
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->routeIs('users.index') && !request()->query('role') ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                    <i class="bi bi-people mr-2"></i>Todos
                                </a>
                                <a href="{{ route('users.index', ['role' => 'lider_celula']) }}"
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->query('role') == 'lider_celula' ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                    <i class="bi bi-star mr-2"></i>Líderes
                                </a>
                                <a href="{{ route('users.index', ['role' => 'membro']) }}"
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->query('role') == 'membro' ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                    <i class="bi bi-person mr-2"></i>Membros
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('packages.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('packages.*') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                        <i class="bi bi-box-seam text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium">Pacotes</span>
                        <span class="tooltip">Pacotes</span>
                    </a>

                    <a href="{{ route('contributions.pending') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('contributions.pending') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                        <i class="bi bi-clock-history text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium">Pendentes</span>
                        <span class="badge bg-yellow-500 text-yellow-900 sidebar-text ml-2">3</span>
                        <span class="tooltip">Contribuições Pendentes</span>
                    </a>
                </div>
                @endif
            </nav>

            <!-- User Profile Footer -->
            <div class="mt-auto border-t border-gray-700">
                <div class="p-4">
                    <div class="flex items-center space-x-3 mb-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="sidebar-text flex-1 min-w-0">
                            <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ ucfirst(str_replace('_', ' ', $role)) }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                            class="sidebar-text text-gray-400 hover:text-white transition-colors p-2 hover:bg-gray-700 rounded-lg"
                            title="Editar Perfil">
                            <i class="bi bi-gear"></i>
                        </a>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center px-4 py-2.5 text-red-400 hover:bg-red-900/30 transition-all rounded-lg font-medium border border-red-900/30 hover:border-red-600">
                            <i class="bi bi-box-arrow-right mr-2"></i>
                            <span class="sidebar-text">Sair</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button onclick="toggleMobileSidebar()" class="md:hidden text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="bi bi-list text-2xl"></i>
                        </button>
                        
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs md:text-sm text-gray-500 mt-0.5">@yield('page-subtitle', 'Bem-vindo ao Projeto Edificar')</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 md:space-x-6">
                        <button class="text-gray-600 hover:text-gray-800 relative p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="bi bi-bell text-xl"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                        </button>
                        <div class="hidden md:block border-l border-gray-300 pl-6">
                            <p class="text-sm text-gray-600 truncate max-w-xs">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container mx-auto px-4 md:px-6 py-6 md:py-8">
                    <!-- Toast Messages -->
                    @if ($message = Session::get('success'))
                    <div id="successToast"
                        class="mb-4 p-4 bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 text-green-800 rounded-lg flex items-center justify-between shadow-lg"
                        role="alert">
                        <div class="flex items-center">
                            <div class="bg-green-500 text-white p-2 rounded-lg mr-3">
                                <i class="bi bi-check-circle text-lg"></i>
                            </div>
                            <span class="font-medium">{{ $message }}</span>
                        </div>
                        <button onclick="closeToast('successToast')" class="text-green-700 hover:text-green-900 p-1">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    @endif

                    @if ($message = Session::get('error'))
                    <div id="errorToast"
                        class="mb-4 p-4 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-800 rounded-lg flex items-center justify-between shadow-lg"
                        role="alert">
                        <div class="flex items-center">
                            <div class="bg-red-500 text-white p-2 rounded-lg mr-3">
                                <i class="bi bi-exclamation-circle text-lg"></i>
                            </div>
                            <span class="font-medium">{{ $message }}</span>
                        </div>
                        <button onclick="closeToast('errorToast')" class="text-red-700 hover:text-red-900 p-1">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    @endif

                    @if ($message = Session::get('warning'))
                    <div id="warningToast"
                        class="mb-4 p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded-lg flex items-center justify-between shadow-lg"
                        role="alert">
                        <div class="flex items-center">
                            <div class="bg-yellow-500 text-white p-2 rounded-lg mr-3">
                                <i class="bi bi-exclamation-triangle text-lg"></i>
                            </div>
                            <span class="font-medium">{{ $message }}</span>
                        </div>
                        <button onclick="closeToast('warningToast')" class="text-yellow-700 hover:text-yellow-900 p-1">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    @endif

                    @if ($message = Session::get('info'))
                    <div id="infoToast"
                        class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 text-blue-800 rounded-lg flex items-center justify-between shadow-lg"
                        role="alert">
                        <div class="flex items-center">
                            <div class="bg-blue-500 text-white p-2 rounded-lg mr-3">
                                <i class="bi bi-info-circle text-lg"></i>
                            </div>
                            <span class="font-medium">{{ $message }}</span>
                        </div>
                        <button onclick="closeToast('infoToast')" class="text-blue-700 hover:text-blue-900 p-1">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toggle Sidebar (Desktop)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('sidebar-collapsed');
            sidebar.classList.toggle('sidebar-expanded');
            
            // Salvar estado no localStorage
            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Toggle Mobile Sidebar
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('hidden');
            
            // Prevenir scroll do body quando menu está aberto
            if (sidebar.classList.contains('mobile-open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        // Toggle Submenu
        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            const button = menu.previousElementSibling;
            const icon = button.querySelector('.bi-chevron-down');

            // Fechar outros menus abertos (comportamento accordion)
            const allMenus = document.querySelectorAll('[id$="contributions"], [id$="members"], [id$="reports"], [id$="users"]');
            allMenus.forEach(m => {
                if (m.id !== menuId && !m.classList.contains('hidden')) {
                    m.classList.add('hidden');
                    const otherIcon = m.previousElementSibling.querySelector('.bi-chevron-down');
                    if (otherIcon) otherIcon.classList.remove('rotate-180');
                }
            });

            // Toggle menu atual
            menu.classList.toggle('hidden');
            if (icon) {
                icon.classList.toggle('rotate-180');
            }

            // Adicionar animação de entrada
            if (!menu.classList.contains('hidden')) {
                menu.classList.add('submenu-enter');
                setTimeout(() => menu.classList.remove('submenu-enter'), 300);
            }
        }

        // Close Toast
        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }
        }

        // Auto-hide toasts e animação de entrada
        document.addEventListener('DOMContentLoaded', function() {
            // Restaurar estado do sidebar do localStorage
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarCollapsed && window.innerWidth >= 768) {
                sidebar.classList.add('sidebar-collapsed');
                sidebar.classList.remove('sidebar-expanded');
            }

            // Toasts
            const toasts = document.querySelectorAll('[role="alert"]');
            toasts.forEach(toast => {
                toast.classList.add('toast-enter');
                setTimeout(() => closeToast(toast.id), 5000);
            });

            // Fechar menu mobile ao clicar em um link
            const sidebarLinks = document.querySelectorAll('aside a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        toggleMobileSidebar();
                    }
                });
            });

            // Fechar menu mobile ao redimensionar para desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    const sidebar = document.getElementById('sidebar');
                    const overlay = document.getElementById('mobileOverlay');
                    
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });

            // Atalho de teclado para toggle do sidebar (Ctrl+B)
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'b') {
                    e.preventDefault();
                    if (window.innerWidth >= 768) {
                        toggleSidebar();
                    } else {
                        toggleMobileSidebar();
                    }
                }
            });
        });

        // Prevenir fechamento acidental ao clicar dentro do sidebar
        document.getElementById('sidebar').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    </script>
</body>

</html>