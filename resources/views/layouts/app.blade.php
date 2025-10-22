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
        /* Theme Variables */

        :root {

            --bg-primary: #f9fafb;

            --bg-secondary: #ffffff;

            --bg-sidebar: linear-gradient(to bottom, #111827, #1f2937);

            --text-primary: #111827;

            --text-secondary: #6b7280;

            --border-color: #e5e7eb;

            --hover-bg: #f3f4f6;

            --shadow: rgba(0, 0, 0, 0.1);

        }


        [data-theme="dark"] {

            --bg-primary: #0f172a;

            --bg-secondary: #1e293b;

            --bg-sidebar: linear-gradient(to bottom, #020617, #0f172a);

            --text-primary: #f1f5f9;

            --text-secondary: #94a3b8;

            --border-color: #334155;

            --hover-bg: #334155;

            --shadow: rgba(0, 0, 0, 0.3);

        }


        body {

            background-color: var(--bg-primary);

            color: var(--text-primary);

            transition: background-color 0.3s ease, color 0.3s ease;

        }


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


        /* Animations */

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


        @keyframes fadeIn {

            from {

                opacity: 0;

            }

            to {

                opacity: 1;

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


        .fade-in {

            animation: fadeIn 0.3s ease-out;

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


        /* Tooltip */

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


        /* Badge */

        .badge {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 0.125rem 0.5rem;

            font-size: 0.75rem;

            font-weight: 600;

            border-radius: 9999px;

        }


        /* Scrollbar */

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


        /* Search Box */

        .search-container {

            position: relative;

        }


        .search-results {

            position: absolute;

            top: 100%;

            left: 0;

            right: 0;

            margin-top: 0.5rem;

            background: var(--bg-secondary);

            border: 1px solid var(--border-color);

            border-radius: 0.75rem;

            max-height: 400px;

            overflow-y: auto;

            z-index: 50;

            box-shadow: 0 10px 25px var(--shadow);

        }


        /* Notifications Panel */

        .notifications-panel {

            position: absolute;

            top: 100%;

            right: 0;

            margin-top: 0.5rem;

            width: 380px;

            max-width: calc(100vw - 2rem);

            background: var(--bg-secondary);

            border: 1px solid var(--border-color);

            border-radius: 0.75rem;

            max-height: 500px;

            overflow-y: auto;

            z-index: 50;

            box-shadow: 0 10px 25px var(--shadow);

        }


        /* Theme Toggle Switch */

        .theme-switch {

            position: relative;

            width: 60px;

            height: 30px;

            background: #4b5563;

            border-radius: 15px;

            cursor: pointer;

            transition: background 0.3s;

        }


        .theme-switch.active {

            background: #3b82f6;

        }


        .theme-switch-handle {

            position: absolute;

            top: 3px;

            left: 3px;

            width: 24px;

            height: 24px;

            background: white;

            border-radius: 50%;

            transition: transform 0.3s;

            display: flex;

            align-items: center;

            justify-content: center;

        }


        .theme-switch.active .theme-switch-handle {

            transform: translateX(30px);

        }


        /* Dark theme specific styles */

        [data-theme="dark"] .bg-white {

            background-color: var(--bg-secondary) !important;

        }


        [data-theme="dark"] .text-gray-800 {

            color: var(--text-primary) !important;

        }


        [data-theme="dark"] .text-gray-600 {

            color: var(--text-secondary) !important;

        }


        [data-theme="dark"] .border-gray-200 {

            border-color: var(--border-color) !important;

        }


        [data-theme="dark"] .hover\:bg-gray-100:hover {

            background-color: var(--hover-bg) !important;

        }
    </style>
</head>

<body class="bg-gray-50">
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden mobile-overlay md:hidden"
        onclick="toggleMobileSidebar()"></div>

    <div class="flex h-screen bg-gray-100">
        <aside id="sidebar"
            class="sidebar-expanded bg-gradient-to-b from-gray-900 to-gray-800 text-white flex flex-col overflow-y-auto shadow-2xl">
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
                <button onclick="toggleSidebar()"
                    class="hidden md:block text-gray-400 hover:text-white transition-colors p-2 hover:bg-gray-700 rounded-lg">
                    <i id="sidebarIcon" class="bi bi-layout-sidebar-inset-reverse text-xl"></i>
                </button>
            </div>

            <!-- Navegação -->
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                @php
                    $user = auth()->user();
                    $role = $user->role ?? 'membro';
                    $can = function ($permission) use ($user) {
                        $permissions = [
                            'admin' => [
                                'view_global_report',
                                'view_zone_report',
                                'view_supervision_report',
                                'view_cell_report',
                            ],
                            'pastor_zona' => ['view_zone_report', 'view_supervision_report', 'view_cell_report'],
                            'supervisor' => ['view_supervision_report', 'view_cell_report'],
                            'lider_celula' => ['view_cell_report'],
                            'membro' => [],
                        ];
                        return in_array($permission, $permissions[$user->role] ?? []);
                    };
                    $pendingCount = \App\Models\Contribution::where('status', 'pending')->count();
                    $unreadNotifications = $user->unreadNotifications()->count();
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
                    <button
                        class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group"
                        onclick="toggleMenu('contributions')">
                        <i class="bi bi-cash-coin text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium flex-1">Contribuições</span>
                        <i
                            class="bi bi-chevron-down sidebar-text ml-2 text-xs transition-transform duration-300 {{ request()->routeIs('contributions.*') ? 'rotate-180' : '' }}"></i>
                        <span class="tooltip">Contribuições</span>
                    </button>
                    <div id="contributions"
                        class="overflow-hidden {{ request()->routeIs('contributions.*') ? '' : 'hidden' }}">
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
                                    @if ($role === 'admin')
                                        Todas as Contribuições
                                    @elseif ($role === 'pastor_zona')
                                        Contribuições da Zona
                                    @elseif ($role === 'supervisor')
                                        Contribuições da Supervisão
                                    @else
                                        Contribuições da Célula
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

                <!-- Membros -->
                @if ($role !== 'membro')
                    <a href="{{ route('members.index') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('members.*') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                        <i class="bi bi-people text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium">Membros</span>
                        <span class="tooltip">Gestão de Membros</span>
                    </a>
                @endif

                <!-- Meu Compromisso -->
                <a href="{{ route('commitments.index') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('commitments.*') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                    <i class="bi bi-handshake text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-medium">Meu Compromisso</span>
                    <span class="tooltip">Meu Compromisso</span>
                </a>

                <!-- Relatórios -->
                @if ($role !== 'membro')
                    <div>
                        <button
                            class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group"
                            onclick="toggleMenu('reports')">
                            <i class="bi bi-file-earmark-bar-graph text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-medium flex-1">Relatórios</span>
                            <i
                                class="bi bi-chevron-down sidebar-text ml-2 text-xs transition-transform duration-300 {{ request()->routeIs('reports.*') ? 'rotate-180' : '' }}"></i>
                            <span class="tooltip">Relatórios</span>
                        </button>
                        <div id="reports"
                            class="overflow-hidden {{ request()->routeIs('reports.*') ? '' : 'hidden' }}">
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

                <!-- ADMINISTRAÇÃO (apenas admin) -->
                @if ($role === 'admin')
                    <div class="pt-6">
                        <div class="flex items-center px-4 mb-3">
                            <div class="h-px bg-gray-700 flex-1"></div>
                            <span
                                class="sidebar-text px-3 text-xs uppercase font-bold text-gray-500">Administração</span>
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
                            <button
                                class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group"
                                onclick="toggleMenu('users')">
                                <i class="bi bi-person-badge text-xl flex-shrink-0"></i>
                                <span class="sidebar-text ml-4 font-medium flex-1">Utilizadores</span>
                                <i
                                    class="bi bi-chevron-down sidebar-text ml-2 text-xs transition-transform duration-300 {{ request()->routeIs('users.*') ? 'rotate-180' : '' }}"></i>
                                <span class="tooltip">Utilizadores</span>
                            </button>
                            <div id="users"
                                class="overflow-hidden {{ request()->routeIs('users.*') ? '' : 'hidden' }}">
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
                            @php
                                // Contar pendentes diretamente do modelo apenas para o admin
                                $pendingCount = \App\Models\Contribution::where('status', 'pendente')->count();
                            @endphp
                            @if ($pendingCount > 0)
                                <span
                                    class="badge bg-yellow-500 text-yellow-900 sidebar-text ml-2">{{ $pendingCount }}</span>
                            @endif
                            <span class="tooltip">Contribuições Pendentes</span>
                        </a>
                    </div>
                @endif

                <!-- Notificações -->
                <div class="pt-6">
                    <a href="{{ route('notifications.all') }}"
                        class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('notifications.*') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                        <i class="bi bi-bell text-xl flex-shrink-0"></i>
                        <span class="sidebar-text ml-4 font-medium">Notificações</span>
                        @if ($unreadNotifications > 0)
                            <span
                                class="badge bg-red-500 text-white sidebar-text ml-2">{{ $unreadNotifications }}</span>
                        @endif
                        <span class="tooltip">Notificações</span>
                    </a>
                </div>
            </nav>
            <!-- Footer da Sidebar (Perfil e Logout) -->
            <div class="mt-auto border-t border-gray-700">
                <div class="p-4">
                    <div class="flex items-center space-x-3 mb-3 overflow-hidden">
                        <div
                            class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 font-bold">
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

        <div id="mainContent" class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 flex-1">
                        <button onclick="toggleMobileSidebar()"
                            class="md:hidden text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="bi bi-list text-2xl"></i>
                        </button>

                        <div class="hidden md:block">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs md:text-sm text-gray-500 mt-0.5">@yield('page-subtitle', 'Bem-vindo ao Projeto Edificar')</p>
                        </div>

                        <div class="hidden lg:flex flex-1 max-w-md ml-8 search-container">
                            <div class="relative w-full">
                                <input type="text" id="searchInput"
                                    placeholder="Pesquisar membros, contribuições..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    oninput="handleSearch(this.value)">
                                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                                <div id="searchResults" class="search-results hidden"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 md:space-x-4">
                        <button onclick="toggleMobileSearch()"
                            class="lg:hidden text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="bi bi-search text-xl"></i>
                        </button>

                        <div class="relative">
                            <button onclick="toggleNotifications()"
                                class="text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-100 rounded-lg transition-colors relative">
                                <i class="bi bi-bell text-xl"></i>
                                <!-- Badge dinâmico -->
                                <span id="notificationBadge"
                                    class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border border-white hidden"></span>
                            </button>

                            <!-- Painel dropdown -->
                            <div id="notificationsPanel" class="notifications-panel hidden">
                                <div class="p-4 border-b flex justify-between items-center">
                                    <h4 class="font-semibold text-gray-800">Notificações</h4>
                                    <button onclick="markAllAsRead()"
                                        class="text-xs text-blue-600 hover:text-blue-800 transition">
                                        Marcar todas como lidas
                                    </button>
                                </div>
                                <div id="notificationsContent"
                                    class="p-4 text-sm text-gray-600 space-y-2 max-h-96 overflow-y-auto">
                                    <div class="text-center text-gray-500">
                                        <i class="bi bi-arrow-clockwise animate-spin mr-2"></i>A carregar...
                                    </div>
                                </div>
                                <!-- Link para página de todas as notificações -->
                                <div class="p-3 border-t bg-gray-50">
                                    <a href="{{ route('notifications.all') }}"
                                        class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Ver todas as notificações <i class="bi bi-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <i class="bi bi-sun text-gray-600"></i>
                            <div onclick="toggleTheme()" class="theme-switch" id="themeSwitch">
                                <div class="theme-switch-handle">
                                    <i id="themeIcon" class="bi bi-moon-fill text-sm text-gray-800"></i>
                                </div>
                            </div>
                            <i class="bi bi-moon text-gray-600"></i>
                        </div>

                        <div class="hidden md:block border-l border-gray-300 pl-4">
                            <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                        </div>
                    </div>
                </div>

                <div id="mobileSearchInput" class="lg:hidden mt-4 search-container hidden">
                    <div class="relative w-full">
                        <input type="text" placeholder="Pesquisar..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            oninput="handleSearch(this.value)">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <div id="mobileSearchResults" class="search-results hidden"></div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container mx-auto px-4 md:px-6 py-8">
                    @if ($message = Session::get('success'))
                        <div id="successToast"
                            class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center justify-between shadow-lg"
                            role="alert">
                            <div class="flex items-center">
                                <i class="bi bi-check-circle mr-3 text-lg"></i>
                                <span class="font-medium">{{ $message }}</span>
                            </div>
                            <button onclick="closeToast('successToast')" class="text-green-700 hover:text-green-900">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    @endif

                    @if ($message = Session::get('error'))
                        <div id="errorToast"
                            class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center justify-between shadow-lg"
                            role="alert">
                            <div class="flex items-center">
                                <i class="bi bi-exclamation-circle mr-3 text-lg"></i>
                                <span class="font-medium">{{ $message }}</span>
                            </div>
                            <button onclick="closeToast('errorToast')" class="text-red-700 hover:text-red-900">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    @endif

                    @if ($message = Session::get('warning'))
                        <div id="warningToast"
                            class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg flex items-center justify-between shadow-lg"
                            role="alert">
                            <div class="flex items-center">
                                <i class="bi bi-exclamation-triangle mr-3 text-lg"></i>
                                <span class="font-medium">{{ $message }}</span>
                            </div>
                            <button onclick="closeToast('warningToast')"
                                class="text-yellow-700 hover:text-yellow-900">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    @endif

                    @if ($message = Session::get('info'))
                        <div id="infoToast"
                            class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg flex items-center justify-between shadow-lg"
                            role="alert">
                            <div class="flex items-center">
                                <i class="bi bi-info-circle mr-3 text-lg"></i>
                                <span class="font-medium">{{ $message }}</span>
                            </div>
                            <button onclick="closeToast('infoToast')" class="text-blue-700 hover:text-blue-900">
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
        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const mainContent = document.getElementById('mainContent');
        const sidebarIcon = document.getElementById('sidebarIcon');
        const themeSwitch = document.getElementById('themeSwitch');
        const themeIcon = document.getElementById('themeIcon');
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const mobileSearchResults = document.getElementById('mobileSearchResults');
        const searchRoute = "{{ route('api.search') }}";
        const notificationsPanel = document.getElementById('notificationsPanel');

        // Estado da Sidebar (Desktop)
        let isSidebarExpanded = true;

        // --- SIDEBAR EXPANDIR/RETRAIR (Desktop) ---
        function toggleSidebar() {
            isSidebarExpanded = !isSidebarExpanded;

            if (isSidebarExpanded) {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                mainContent.classList.remove('content-moved');
                // Ícone muda ao colapsar
                if (sidebarIcon) {
                    sidebarIcon.classList.replace(
                        'bi-layout-sidebar-inset-reverse',
                        'bi-layout-sidebar-inset'
                    );
                }
            } else {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.add('content-moved');
                // Ícone volta ao expandir
                if (sidebarIcon) {
                    sidebarIcon.classList.replace(
                        'bi-layout-sidebar-inset',
                        'bi-layout-sidebar-inset-reverse'
                    );
                }
            }
        }

        // --- SIDEBAR MOBILE ---
        function toggleMobileSidebar() {
            sidebar.classList.toggle('mobile-open');
            mobileOverlay.classList.toggle('hidden');
        }

        // --- SUBMENU TOGGLE (Nav) ---
        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            const button = menu.previousElementSibling;
            const icon = button.querySelector('.bi-chevron-down');

            menu.classList.toggle('hidden');

            // Remove animação anterior e adiciona nova
            if (!menu.classList.contains('hidden')) {
                menu.classList.add('submenu-enter');
                menu.classList.remove('submenu-exit');
            } else {
                menu.classList.remove('submenu-enter');
            }

            // Rotaciona o ícone
            if (icon) {
                icon.classList.toggle('rotate-180');
            }
        }

        // --- THEME TOGGLE (Dark/Light) ---
        function toggleTheme() {
            const isDark = document.body.getAttribute('data-theme') === 'dark';

            if (isDark) {
                document.body.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                themeSwitch.classList.remove('active');
                themeIcon.classList.replace('bi-sun-fill', 'bi-moon-fill');
            } else {
                document.body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                themeSwitch.classList.add('active');
                themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            }
        }

        // --- MOBILE SEARCH TOGGLE ---
        function toggleMobileSearch() {
            const mobileSearchInput = document.getElementById('mobileSearchInput');
            mobileSearchInput.classList.toggle('hidden');
            if (!mobileSearchInput.classList.contains('hidden')) {
                // Foco na pesquisa ao abrir
                mobileSearchInput.querySelector('input').focus();
            }
        }

        // --- DEBOUNCE FUNCTION ---
        function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        }

        // --- SEARCH HANDLER ---
        function handleSearch(query, isMobile = false) {
            const targetResults = isMobile ? mobileSearchResults : searchResults;

            if (query.length < 3) {
                targetResults.classList.add('hidden');
                return;
            }

            // Exibir spinner de carregamento
            targetResults.innerHTML =
                '<div class="p-3 text-center text-gray-500 fade-in"><i class="bi bi-arrow-clockwise animate-spin mr-2"></i>A carregar...</div>';
            targetResults.classList.remove('hidden');

            fetch(`${searchRoute}?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    let totalResults = 0;

                    for (const category in data.results) {
                        const items = data.results[category];
                        if (items.length > 0) {
                            totalResults += items.length;
                            html +=
                                `<div class="p-3 border-b border-gray-100 bg-gray-50 font-semibold text-xs uppercase text-gray-500">${category}</div>`;

                            items.forEach(item => {
                                if (category === 'Membros') {
                                    // Gerar link correto baseado na rota users.show
                                    const userLink = "{{ route('users.show', ['user' => '__ID__']) }}".replace(
                                        '__ID__', item.id);
                                    html += `<a href="${userLink}" class="block p-3 hover:bg-gray-100 text-sm text-gray-800 transition">
                            <i class="bi bi-person-circle mr-2"></i>${item.name} 
                            <span class="text-xs text-blue-500">(${item.role})</span>
                        </a>`;
                                } else if (category === 'Contribuições') {
                                    const date = new Date(item.contribution_date).toLocaleDateString('pt-PT');
                                    const contributionLink =
                                        "{{ route('contributions.show', ['contribution' => '__ID__']) }}"
                                        .replace('__ID__', item.id);
                                    html += `<a href="${contributionLink}" class="block p-3 hover:bg-gray-100 text-sm text-gray-800 transition">
                            <i class="bi bi-cash-coin mr-2"></i>${item.amount} MT em ${date}
                            <span class="text-xs text-gray-500">(${item.user.name})</span>
                        </a>`;
                                }
                            });
                        }
                    }

                    if (totalResults === 0) {
                        html = '<div class="p-3 text-center text-gray-500">Nenhum resultado encontrado.</div>';
                    }

                    targetResults.innerHTML = html;
                })
                .catch(error => {
                    targetResults.innerHTML =
                        '<div class="p-3 text-center text-red-500">Erro ao carregar a pesquisa.</div>';
                    console.error('AJAX Search Error:', error);
                });
        }

        // Criar as funções debounced para os inputs
        const debouncedDesktopSearch = debounce((q) => handleSearch(q, false), 300);
        const debouncedMobileSearch = debounce((q) => handleSearch(q, true), 300);

        // --- NOTIFICATIONS TOGGLE ---
        const notificationsIndexRoute = "{{ route('notifications.api.index') }}";
        const notificationsReadRoute = "{{ route('notifications.read') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function toggleNotifications() {
            notificationsPanel.classList.toggle('hidden');

            if (!notificationsPanel.classList.contains('hidden')) {
                loadNotifications();
            }
        }

        // --- MARCAR TUDO COMO LIDO ---
        function markAllAsRead() {
            fetch(notificationsReadRoute, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    }
                })
                .then(() => {
                    loadNotifications(true);
                })
                .catch(error => {
                    console.error('Erro ao marcar como lido:', error);
                });
        }

        // --- CARREGAR NOTIFICAÇÕES VIA AJAX ---
        // Atualizar a função loadNotifications para usar o badge
        function loadNotifications(showSuccess = false) {
            const htmlContent = `
        <div class="p-4 border-b flex justify-between items-center">
            <h4 class="font-semibold text-gray-800">Notificações</h4>
            <button onclick="markAllAsRead()" class="text-xs text-blue-600 hover:text-blue-800 transition">
                Marcar todas como lidas
            </button>
        </div>
        <div id="notificationsContent" class="p-4 text-sm text-gray-600 space-y-2 max-h-96 overflow-y-auto">
            <div class="text-center text-gray-500"><i class="bi bi-arrow-clockwise animate-spin mr-2"></i>A carregar...</div>
        </div>
        <div class="p-3 border-t bg-gray-50">
            <a href="{{ route('notifications.all') }}" class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                Ver todas as notificações <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
    `;

            notificationsPanel.innerHTML = htmlContent;
            const targetContent = document.getElementById('notificationsContent');
            const notificationBadge = document.getElementById('notificationBadge');

            fetch(notificationsIndexRoute)
                .then(response => response.json())
                .then(data => {
                    const unreadCount = data.length;

                    // Atualizar badge
                    if (unreadCount > 0) {
                        notificationBadge.classList.remove('hidden');
                    } else {
                        notificationBadge.classList.add('hidden');
                    }

                    if (unreadCount > 0) {
                        let listHtml = '<ul class="space-y-2">';
                        data.forEach(n => {
                            listHtml += `
                        <a href="${n.link}" class="flex items-start p-2 hover:bg-gray-50 rounded cursor-pointer block">
                            <i class="bi bi-bell-fill text-blue-500 mr-3 mt-1 flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 truncate">${n.title}</p>
                                <p class="text-xs text-gray-600 line-clamp-2">${n.message}</p>
                                <p class="text-xs text-gray-400 mt-1">${n.time_ago}</p>
                            </div>
                        </a>
                    `;
                        });
                        listHtml += '</ul>';
                        targetContent.innerHTML = listHtml;
                    } else {
                        if (showSuccess) {
                            targetContent.innerHTML =
                                '<div class="text-center text-green-600 py-4">Marcado como lido com sucesso!</div>';
                        } else {
                            targetContent.innerHTML =
                                '<div class="text-center text-gray-500 py-4">Nenhuma notificação não lida.</div>';
                        }
                    }
                })
                .catch(error => {
                    targetContent.innerHTML =
                        '<div class="text-center text-red-500 py-4">Erro ao carregar notificações.</div>';
                    console.error('Erro ao carregar notificações:', error);
                });
        }

        // Atualizar badge periodicamente (a cada 30 segundos)
        function updateNotificationBadge() {
            fetch("{{ route('notifications.unread-count') }}")
                .then(response => response.json())
                .then(data => {
                    const notificationBadge = document.getElementById('notificationBadge');
                    if (data.count > 0) {
                        notificationBadge.classList.remove('hidden');
                    } else {
                        notificationBadge.classList.add('hidden');
                    }
                })
                .catch(error => console.error('Erro ao atualizar badge:', error));
        }

        // Iniciar atualização automática
        document.addEventListener('DOMContentLoaded', function() {
            // ... código existente ...

            // Atualizar badge a cada 30 segundos
            setInterval(updateNotificationBadge, 30000);

            // Atualizar imediatamente
            updateNotificationBadge();
        });

        // --- CLOSE TOAST ---
        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }

        // --- INITIALIZATION ---
        document.addEventListener('DOMContentLoaded', function() {
            const desktopInput = document.getElementById('searchInput');
            const mobileInputContainer = document.getElementById('mobileSearchInput');
            const mobileInput = mobileInputContainer ? mobileInputContainer.querySelector('input') : null;

            // Setup search inputs
            if (desktopInput) {
                desktopInput.addEventListener('input', (e) => debouncedDesktopSearch(e.target.value));
            }
            if (mobileInput) {
                mobileInput.addEventListener('input', (e) => debouncedMobileSearch(e.target.value));
            }

            // Inicializar tema
            const storedTheme = localStorage.getItem('theme');
            if (storedTheme === 'dark') {
                document.body.setAttribute('data-theme', 'dark');
                themeSwitch.classList.add('active');
                themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            } else if (!storedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.body.setAttribute('data-theme', 'dark');
                themeSwitch.classList.add('active');
                themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            } else {
                themeSwitch.classList.remove('active');
                themeIcon.classList.replace('bi-sun-fill', 'bi-moon-fill');
            }

            // Inicializa Toast Messages (animação e auto-hide)
            const toasts = document.querySelectorAll('[role="alert"]');
            toasts.forEach(toast => {
                toast.classList.add('toast-enter');
                setTimeout(() => {
                    closeToast(toast.id);
                }, 5000);
            });

            // Inicializa a sidebar no estado expandido no desktop
            if (window.innerWidth > 768) {
                isSidebarExpanded = true;
            }

            // Fechar painel de notificações ao clicar fora
            document.addEventListener('click', function(event) {
                const notificationButton = document.querySelector('header .bi-bell').parentElement;
                const isClickInside = notificationsPanel.contains(event.target) ||
                    notificationButton.contains(event.target);

                if (!isClickInside && !notificationsPanel.classList.contains('hidden')) {
                    notificationsPanel.classList.add('hidden');
                }
            });

            // Fechar search results ao clicar fora
            document.addEventListener('click', function(event) {
                const searchContainer = document.querySelector('.search-container');
                if (searchContainer && !searchContainer.contains(event.target)) {
                    if (searchResults) searchResults.classList.add('hidden');
                    if (mobileSearchResults) mobileSearchResults.classList.add('hidden');
                }
            });
            // Fechar mobile sidebar ao clicar na overlay
            mobileOverlay.addEventListener('click', toggleMobileSidebar);
        });
    </script>
</body>

</html>
