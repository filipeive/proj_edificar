<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        /* Sidebar Styles */
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

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
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

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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

        [data-theme="dark"] .text-gray-500 {
            color: var(--text-secondary) !important;
        }

        [data-theme="dark"] .border-gray-200 {
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .hover\:bg-gray-100:hover {
            background-color: var(--hover-bg) !important;
        }

        [data-theme="dark"] .bg-gray-50 {
            background-color: var(--bg-primary) !important;
        }

        [data-theme="dark"] .bg-gray-100 {
            background-color: var(--hover-bg) !important;
        }

        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Toast Container - Fixed Position */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 420px;
            width: calc(100% - 40px);
        }

        @media (max-width: 640px) {
            .toast-container {
                top: 10px;
                right: 10px;
                width: calc(100% - 20px);
            }
        }

        /* Enhanced Toast Styles */
        .toast {
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            transform-origin: right center;
        }

        @keyframes slideInFromRight {
            from {
                opacity: 0;
                transform: translateX(400px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes slideOutToRight {
            from {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateX(400px) scale(0.9);
            }
        }

        .toast-enter {
            animation: slideInFromRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .toast-exit {
            animation: slideOutToRight 0.3s cubic-bezier(0.6, -0.28, 0.735, 0.045);
        }

        /* Progress Bar */
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background: currentColor;
            opacity: 0.3;
            animation: progressBar 5s linear;
        }

        @keyframes progressBar {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden mobile-overlay md:hidden"
        onclick="toggleMobileSidebar()"></div>

    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="sidebar-expanded bg-gradient-to-b from-gray-900 to-gray-800 text-white flex flex-col overflow-y-auto shadow-2xl">
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
                <button onclick="toggleSidebar()"
                    class="hidden md:block text-gray-400 hover:text-white transition-colors p-2 hover:bg-gray-700 rounded-lg">
                    <i id="sidebarIcon" class="bi bi-layout-sidebar-inset-reverse text-xl"></i>
                </button>
            </div>

            <!-- Navigation -->
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
                    $pendingCount = \App\Models\Contribution::where('status', 'pendente')->count();
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
                        <i class="bi bi-chevron-down sidebar-text ml-2 text-xs {{ request()->routeIs('contributions.*') ? 'rotate-180' : '' }}"></i>
                        <span class="tooltip">Contribuições</span>
                    </button>
                    <div id="contributions" class="overflow-hidden {{ request()->routeIs('contributions.*') ? '' : 'hidden' }}">
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
                        <i class="bi bi-chevron-down sidebar-text ml-2 text-xs {{ request()->routeIs('reports.*') ? 'rotate-180' : '' }}"></i>
                        <span class="tooltip">Relatórios</span>
                    </button>
                    <div id="reports" class="overflow-hidden {{ request()->routeIs('reports.*') ? '' : 'hidden' }}">
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

                <!-- Notificações -->
                <a href="{{ route('notifications.all') }}"
                    class="nav-item relative flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group {{ request()->routeIs('notifications.*') ? 'bg-blue-600 shadow-lg shadow-blue-600/50' : '' }}">
                    <i class="bi bi-bell text-xl flex-shrink-0"></i>
                    <span class="sidebar-text ml-4 font-medium">Notificações</span>
                    @if ($unreadNotifications > 0)
                    <span class="badge bg-red-500 text-white sidebar-text ml-2">{{ $unreadNotifications }}</span>
                    @endif
                    <span class="tooltip">Notificações</span>
                </a>

                <!-- Administração -->
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
                        <button
                            class="nav-item relative w-full text-left flex items-center px-4 py-3 rounded-xl hover:bg-gray-700/50 transition-all duration-200 group"
                            onclick="toggleMenu('users')">
                            <i class="bi bi-person-badge text-xl flex-shrink-0"></i>
                            <span class="sidebar-text ml-4 font-medium flex-1">Utilizadores</span>
                            <i class="bi bi-chevron-down sidebar-text ml-2 text-xs {{ request()->routeIs('users.*') ? 'rotate-180' : '' }}"></i>
                            <span class="tooltip">Utilizadores</span>
                        </button>
                        <div id="users" class="overflow-hidden {{ request()->routeIs('users.*') ? '' : 'hidden' }}">
                            <div class="ml-8 mt-2 space-y-1 border-l-2 border-gray-700 pl-4">
                                <a href="{{ route('users.index') }}"
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->routeIs('users.index') && !request()->query('role') ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                    <i class="bi bi-people mr-2"></i>Todos
                                </a>
                                <a href="{{ route('users.index', ['role' => 'admin']) }}"
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->query('role') == 'admin' ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                    <i class="bi bi-shield-lock mr-2"></i>Administradores
                                </a>
                                <a href="{{ route('users.index', ['role' => 'pastor_zona']) }}"
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->query('role') == 'pastor_zona' ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                    <i class="bi bi-geo-alt mr-2"></i>Pastores de Zona
                                </a>
                                <a href="{{ route('users.index', ['role' => 'supervisor']) }}"
                                    class="block px-4 py-2 text-sm rounded-lg hover:bg-gray-700/50 transition {{ request()->query('role') == 'supervisor' ? 'text-blue-400 bg-gray-700/30' : 'text-gray-300' }}">
                                    <i class="bi bi-diagram-3-fill mr-2"></i>Supervisores
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
                        @if ($pendingCount > 0)
                        <span class="badge bg-yellow-500 text-yellow-900 sidebar-text ml-2">{{ $pendingCount }}</span>
                        @endif
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
        <div id="mainContent" class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 flex-1">
                        <!-- Mobile Menu Button -->
                        <button onclick="toggleMobileSidebar()"
                            class="md:hidden text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="bi bi-list text-2xl"></i>
                        </button>

                        <div class="hidden md:block">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-xs md:text-sm text-gray-500 mt-0.5">@yield('page-subtitle', 'Bem-vindo ao Projeto Edificar')</p>
                        </div>

                        <!-- Search Bar (Desktop) -->
                        <div class="hidden lg:flex flex-1 max-w-md ml-8 search-container">
                            <div class="relative w-full">
                                <input type="text" id="searchInput"
                                    placeholder="Pesquisar membros, contribuições..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    oninput="debouncedDesktopSearch(this.value)">
                                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                                <!-- Search Results Dropdown -->
                                <div id="searchResults" class="search-results hidden"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 md:space-x-4">
                        <!-- Mobile Search Button -->
                        <button onclick="toggleMobileSearch()"
                            class="lg:hidden text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="bi bi-search text-xl"></i>
                        </button>

                        <!-- Notifications -->
                        <div class="relative">
                            <button onclick="toggleNotifications()"
                                class="text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-100 rounded-lg transition-colors relative">
                                <i class="bi bi-bell text-xl"></i>
                                @if ($unreadNotifications > 0)
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                @endif
                            </button>

                            <!-- Notifications Panel -->
                            <div id="notificationsPanel" class="notifications-panel hidden fade-in">
                                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                                    <h4 class="font-semibold text-gray-800">Notificações</h4>
                                    <button onclick="markAllAsRead()"
                                        class="text-xs text-blue-600 hover:text-blue-800 transition">
                                        Marcar todas como lidas
                                    </button>
                                </div>
                                <div id="notificationsContent" class="p-4 text-sm text-gray-600 space-y-2 max-h-96 overflow-y-auto">
                                    <div class="text-center text-gray-500">
                                        <i class="bi bi-arrow-clockwise animate-spin mr-2"></i>A carregar...
                                    </div>
                                </div>
                                <div class="p-3 border-t border-gray-200 bg-gray-50">
                                    <a href="{{ route('notifications.all') }}"
                                        class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Ver todas as notificações <i class="bi bi-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Theme Toggle -->
                        <div class="flex items-center space-x-2">
                            <i class="bi bi-sun text-gray-600 text-sm"></i>
                            <div onclick="toggleTheme()" class="theme-switch" id="themeSwitch">
                                <div class="theme-switch-handle">
                                    <i id="themeIcon" class="bi bi-moon-fill text-xs text-gray-800"></i>
                                </div>
                            </div>
                            <i class="bi bi-moon text-gray-600 text-sm"></i>
                        </div>

                        <div class="hidden md:block border-l border-gray-300 pl-4">
                            <div class="relative">
                                <button type="button" onclick="toggleUserMenu()" class="flex items-center space-x-2 hover:bg-gray-100 p-2 rounded-lg transition">
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-800 truncate max-w-[150px]">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $role)) }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 font-bold text-white">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <i class="bi bi-chevron-down text-gray-600 text-sm transition-transform duration-200"></i>
                                </button>

                                <!-- User Dropdown Menu -->
                                <div id="userMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                    <div class="p-3 border-b border-gray-100">
                                        <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                    </div>
                                    <div class="py-2">
                                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            <i class="bi bi-person-circle mr-3 text-blue-600"></i>
                                            Meu Perfil
                                        </a>
                                        <a href="{{ route('commitments.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            <i class="bi bi-handshake mr-3 text-green-600"></i>
                                            Meus Compromissos
                                        </a>
                                        <a href="{{ route('notifications.all') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            <i class="bi bi-bell mr-3 text-purple-600"></i>
                                            Notificações
                                            @if ($unreadNotifications > 0)
                                            <span class="ml-auto badge bg-red-500 text-white text-xs">{{ $unreadNotifications }}</span>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="border-t border-gray-100">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition font-medium">
                                                <i class="bi bi-box-arrow-right mr-3"></i>
                                                Sair
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Search Input -->
                <div id="mobileSearchInput" class="lg:hidden mt-4 search-container hidden fade-in">
                    <div class="relative w-full">
                        <input type="text" placeholder="Pesquisar..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            oninput="debouncedMobileSearch(this.value)">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <div id="mobileSearchResults" class="search-results hidden"></div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container mx-auto px-4 md:px-6 py-6 md:py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Toast Container (Floating) -->
    <div class="toast-container" id="toastContainer">
        @if ($message = Session::get('success'))
        <div id="successToast"
            class="toast relative p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl flex items-center justify-between shadow-2xl"
            role="alert">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur-sm p-2 rounded-lg mr-3">
                    <i class="bi bi-check-circle-fill text-2xl"></i>
                </div>
                <div>
                    <p class="font-bold text-sm">Sucesso!</p>
                    <span class="text-sm opacity-90">{{ $message }}</span>
                </div>
            </div>
            <button onclick="closeToast('successToast')" class="text-white/80 hover:text-white p-2 ml-4 hover:bg-white/10 rounded-lg transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
            <div class="toast-progress"></div>
        </div>
        @endif

        @if ($message = Session::get('error'))
        <div id="errorToast"
            class="toast relative p-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl flex items-center justify-between shadow-2xl"
            role="alert">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur-sm p-2 rounded-lg mr-3">
                    <i class="bi bi-exclamation-circle-fill text-2xl"></i>
                </div>
                <div>
                    <p class="font-bold text-sm">Erro!</p>
                    <span class="text-sm opacity-90">{{ $message }}</span>
                </div>
            </div>
            <button onclick="closeToast('errorToast')" class="text-white/80 hover:text-white p-2 ml-4 hover:bg-white/10 rounded-lg transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
            <div class="toast-progress"></div>
        </div>
        @endif

        @if ($message = Session::get('warning'))
        <div id="warningToast"
            class="toast relative p-4 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl flex items-center justify-between shadow-2xl"
            role="alert">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur-sm p-2 rounded-lg mr-3">
                    <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
                </div>
                <div>
                    <p class="font-bold text-sm">Atenção!</p>
                    <span class="text-sm opacity-90">{{ $message }}</span>
                </div>
            </div>
            <button onclick="closeToast('warningToast')" class="text-white/80 hover:text-white p-2 ml-4 hover:bg-white/10 rounded-lg transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
            <div class="toast-progress"></div>
        </div>
        @endif

        @if ($message = Session::get('info'))
        <div id="infoToast"
            class="toast relative p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl flex items-center justify-between shadow-2xl"
            role="alert">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur-sm p-2 rounded-lg mr-3">
                    <i class="bi bi-info-circle-fill text-2xl"></i>
                </div>
                <div>
                    <p class="font-bold text-sm">Informação</p>
                    <span class="text-sm opacity-90">{{ $message }}</span>
                </div>
            </div>
            <button onclick="closeToast('infoToast')" class="text-white/80 hover:text-white p-2 ml-4 hover:bg-white/10 rounded-lg transition">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
            <div class="toast-progress"></div>
        </div>
        @endif
    </div>

    <script>
        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const mainContent = document.getElementById('mainContent');
        const sidebarIcon = document.getElementById('sidebarIcon');
        const themeSwitch = document.getElementById('themeSwitch');
        const themeIcon = document.getElementById('themeIcon');
        const searchResults = document.getElementById('searchResults');
        const mobileSearchResults = document.getElementById('mobileSearchResults');
        const notificationsPanel = document.getElementById('notificationsPanel');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Routes
        const searchRoute = "{{ route('api.search') }}";
        const notificationsIndexRoute = "{{ route('notifications.api.index') }}";
        const notificationsReadRoute = "{{ route('notifications.read') }}";
        const notificationsUnreadCountRoute = "{{ route('notifications.unread-count') }}";

        // State
        let isSidebarExpanded = true;

        // ===== SIDEBAR FUNCTIONS =====
        function toggleSidebar() {
            isSidebarExpanded = !isSidebarExpanded;

            if (isSidebarExpanded) {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                sidebarIcon?.classList.replace('bi-layout-sidebar-inset', 'bi-layout-sidebar-inset-reverse');
            } else {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                sidebarIcon?.classList.replace('bi-layout-sidebar-inset-reverse', 'bi-layout-sidebar-inset');
            }

            localStorage.setItem('sidebarCollapsed', !isSidebarExpanded);
        }

        function toggleMobileSidebar() {
            sidebar.classList.toggle('mobile-open');
            mobileOverlay.classList.toggle('hidden');
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        }

        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            const button = menu.previousElementSibling;
            const icon = button.querySelector('.bi-chevron-down');

            menu.classList.toggle('hidden');

            if (!menu.classList.contains('hidden')) {
                menu.classList.add('submenu-enter');
            }

            icon?.classList.toggle('rotate-180');
        }

        // ===== THEME FUNCTIONS =====
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

        function initializeTheme() {
            const storedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
                document.body.setAttribute('data-theme', 'dark');
                themeSwitch.classList.add('active');
                themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            }
        }

        // ===== SEARCH FUNCTIONS =====
        function toggleMobileSearch() {
            const mobileSearchInput = document.getElementById('mobileSearchInput');
            mobileSearchInput.classList.toggle('hidden');
            if (!mobileSearchInput.classList.contains('hidden')) {
                mobileSearchInput.querySelector('input').focus();
            }
        }

        function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        }

        function handleSearch(query, isMobile = false) {
            const targetResults = isMobile ? mobileSearchResults : searchResults;

            if (query.length < 3) {
                targetResults.classList.add('hidden');
                return;
            }

            targetResults.innerHTML = '<div class="p-3 text-center text-gray-500"><i class="bi bi-arrow-clockwise animate-spin mr-2"></i>A carregar...</div>';
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
                        html += `<div class="p-3 border-b border-gray-100 bg-gray-50 font-semibold text-xs uppercase text-gray-500">${category}</div>`;

                        items.forEach(item => {
                            if (category === 'Membros') {
                                const userLink = "{{ route('users.show', ['user' => '__ID__']) }}".replace('__ID__', item.id);
                                html += `<a href="${userLink}" class="block p-3 hover:bg-gray-100 text-sm text-gray-800 transition">
                                    <i class="bi bi-person-circle mr-2 text-blue-500"></i>${item.name} 
                                    <span class="text-xs text-gray-500 ml-1">(${item.role})</span>
                                </a>`;
                            } else if (category === 'Contribuições') {
                                const date = new Date(item.contribution_date).toLocaleDateString('pt-PT');
                                const contributionLink = "{{ route('contributions.show', ['contribution' => '__ID__']) }}".replace('__ID__', item.id);
                                html += `<a href="${contributionLink}" class="block p-3 hover:bg-gray-100 text-sm text-gray-800 transition">
                                    <i class="bi bi-cash-coin mr-2 text-green-500"></i>${item.amount} MT em ${date}
                                    <span class="text-xs text-gray-500 block ml-6">${item.user.name}</span>
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
                targetResults.innerHTML = '<div class="p-3 text-center text-red-500">Erro ao carregar resultados.</div>';
                console.error('Search Error:', error);
            });
        }

        const debouncedDesktopSearch = debounce((q) => handleSearch(q, false), 300);
        const debouncedMobileSearch = debounce((q) => handleSearch(q, true), 300);

        // ===== NOTIFICATIONS FUNCTIONS =====
        function toggleNotifications() {
            notificationsPanel.classList.toggle('hidden');
            if (!notificationsPanel.classList.contains('hidden')) {
                loadNotifications();
            }
        }

        function toggleUserMenu() {
            const userMenu = document.getElementById('userMenu');
            const chevron = document.querySelector('[onclick="toggleUserMenu()"] .bi-chevron-down');
            
            userMenu.classList.toggle('hidden');
            
            // Rotacionar o chevron
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }

        function loadNotifications(showSuccess = false) {
            const targetContent = document.getElementById('notificationsContent');
            
            fetch(notificationsIndexRoute)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        let html = '<ul class="space-y-2">';
                        data.forEach(n => {
                            html += `
                                <a href="${n.link}" class="flex items-start p-3 hover:bg-gray-50 rounded-lg cursor-pointer block transition">
                                    <i class="bi bi-bell-fill text-blue-500 mr-3 mt-1 flex-shrink-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-800 text-sm">${n.title}</p>
                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">${n.message}</p>
                                        <p class="text-xs text-gray-400 mt-1">${n.time_ago}</p>
                                    </div>
                                </a>
                            `;
                        });
                        html += '</ul>';
                        targetContent.innerHTML = html;
                    } else {
                        targetContent.innerHTML = showSuccess 
                            ? '<div class="text-center text-green-600 py-4"><i class="bi bi-check-circle mr-2"></i>Todas marcadas como lidas!</div>'
                            : '<div class="text-center text-gray-500 py-4"><i class="bi bi-inbox mr-2"></i>Nenhuma notificação.</div>';
                    }
                })
                .catch(error => {
                    targetContent.innerHTML = '<div class="text-center text-red-500 py-4">Erro ao carregar notificações.</div>';
                    console.error('Notifications Error:', error);
                });
        }

        function markAllAsRead() {
            fetch(notificationsReadRoute, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(() => loadNotifications(true))
            .catch(error => console.error('Mark as read error:', error));
        }

        function updateNotificationBadge() {
            fetch(notificationsUnreadCountRoute)
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.bi-bell').nextElementSibling;
                    if (badge) {
                        badge.style.display = data.count > 0 ? 'block' : 'none';
                    }
                })
                .catch(error => console.error('Badge update error:', error));
        }

        // ===== TOAST FUNCTIONS =====
        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }
        }

        // ===== INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize theme
            initializeTheme();

            // Initialize sidebar state
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed && window.innerWidth >= 768) {
                toggleSidebar();
            }

            // Initialize toasts
            document.querySelectorAll('[role="alert"]').forEach(toast => {
                toast.classList.add('toast-enter');
                setTimeout(() => closeToast(toast.id), 5000);
            });

            // Update notification badge periodically
            updateNotificationBadge();
            setInterval(updateNotificationBadge, 30000);

            // Close dropdowns on outside click
            document.addEventListener('click', function(event) {
                // Close notifications
                const notifButton = document.querySelector('[onclick="toggleNotifications()"]');
                if (notifButton && !notificationsPanel.contains(event.target) && !notifButton.contains(event.target)) {
                    notificationsPanel.classList.add('hidden');
                }

                // Close user menu
                const userMenuButton = event.target.closest('[onclick="toggleUserMenu()"]');
                const userMenu = document.getElementById('userMenu');
                
                if (userMenu && !userMenu.contains(event.target) && !userMenuButton) {
                    userMenu.classList.add('hidden');
                    const chevron = document.querySelector('[onclick="toggleUserMenu()"] .bi-chevron-down');
                    if (chevron) {
                        chevron.classList.remove('rotate-180');
                    }
                }

                // Close search results
                const searchContainer = document.querySelector('.search-container');
                if (searchContainer && !searchContainer.contains(event.target)) {
                    searchResults?.classList.add('hidden');
                    mobileSearchResults?.classList.add('hidden');
                }
            });

            // Close mobile sidebar on link click
            document.querySelectorAll('aside a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        toggleMobileSidebar();
                    }
                });
            });

            // Keyboard shortcut (Ctrl+B) for sidebar toggle
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

            // Responsive sidebar on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('mobile-open');
                    mobileOverlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
</body>

</html>