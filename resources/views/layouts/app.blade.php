<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Life Church')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Theme Variables */
        :root {
            --bg-primary: #f9fafb;
            --bg-secondary: #ffffff;
            --bg-sidebar: #000000;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --hover-bg: #f3f4f6;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-sidebar: #000000;
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

            0%,
            100% {
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

        @media (max-width: 640px) {
            .notifications-panel {
                right: 50%;
                transform: translateX(50%);
                width: calc(100vw - 2rem);
            }
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

        /* Custom Scrollbar for Sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Professional Sidebar Headers */
        .sidebar-section-header {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #6b7280;
            /* gray-500 */
            margin-bottom: 0.5rem;
            margin-top: 1.5rem;
            padding-left: 1rem;
            display: flex;
            align-items: center;
        }

        .sidebar-section-header::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgba(75, 85, 99, 0.3);
            /* gray-600 with opacity */
            margin-left: 1rem;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden mobile-overlay md:hidden"
        onclick="toggleMobileSidebar()"></div>

    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        @include('layouts.sidebar')

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
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')
                            </h2>
                            <p class="text-xs md:text-sm text-gray-500 mt-0.5">
                                @yield('page-subtitle', 'Bem-vindo ao Portal Life Church')</p>
                        </div>

                        <!-- Search Bar (Desktop) -->
                        <div class="hidden lg:flex flex-1 max-w-md ml-8 search-container">
                            <div class="relative w-full">
                                <input type="text" id="searchInput" placeholder="Pesquisar membros, contribuições..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    oninput="debouncedDesktopSearch(this.value)">
                                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                                <!-- Search Results Dropdown -->
                                <div id="searchResults" class="search-results hidden"></div>
                            </div>
                        </div>

                        <!-- Mobile Page Actions (Yield area for Add buttons on mobile) -->
                        <div class="md:hidden flex items-center ml-2">
                            @yield('header-actions')
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
                                class="text-gray-600 hover:text-blue-600 p-2.5 hover:bg-blue-50 rounded-xl transition-all duration-300 relative group border border-transparent hover:border-blue-100">
                                <i class="bi bi-bell-fill text-2xl"></i>
                                @if ($unreadNotifications > 0)
                                    <span class="absolute -top-1 -right-1 flex h-5 w-5">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span
                                            class="relative inline-flex rounded-full h-5 w-5 bg-red-600 text-[10px] font-bold text-white items-center justify-center shadow-sm">
                                            {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                                        </span>
                                    </span>
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
                                <div id="notificationsContent"
                                    class="p-4 text-sm text-gray-600 space-y-2 max-h-96 overflow-y-auto">
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

                        <!-- Theme Toggle (Compact) -->
                        <button onclick="toggleTheme()"
                            class="bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-colors text-gray-600 dark:text-gray-300 dark:bg-white/5 dark:hover:bg-white/10">
                            <i id="themeIcon" class="bi bi-moon-fill text-lg"></i>
                        </button>

                        <!-- Mobile Logout Button -->
                        <div class="md:hidden">
                            <form method="POST" action="{{ route('logout') }}" id="mobile-logout-form">
                                @csrf
                                <button type="submit" class="text-red-500 hover:text-red-700 p-2 transition-colors">
                                    <i class="bi bi-power text-2xl"></i>
                                </button>
                            </form>
                        </div>

                        <div class="hidden md:block border-l border-gray-300 pl-4">
                            <div class="relative">
                                <button type="button" onclick="toggleUserMenu()"
                                    class="flex items-center space-x-2 hover:bg-gray-100 p-2 rounded-lg transition">
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-800 truncate max-w-[150px]">
                                            {{ $authUser->name }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $role)) }}</p>
                                    </div>
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 font-bold text-white">
                                        {{ strtoupper(substr($authUser->name, 0, 1)) }}
                                    </div>
                                    <i
                                        class="bi bi-chevron-down text-gray-600 text-sm transition-transform duration-200"></i>
                                </button>

                                <!-- User Dropdown Menu -->
                                <div id="userMenu"
                                    class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                    <div class="p-3 border-b border-gray-100">
                                        <p class="text-sm font-semibold text-gray-800">{{ $authUser->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $authUser->email }}</p>
                                    </div>
                                    <div class="py-2">
                                        <a href="{{ route('profile.edit') }}"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            <i class="bi bi-person-circle mr-3 text-blue-600"></i>
                                            Meu Perfil
                                        </a>
                                        <a href="{{ route('commitments.index') }}"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            <i class="bi bi-handshake mr-3 text-green-600"></i>
                                            Meus Compromissos
                                        </a>
                                        <a href="{{ route('notifications.all') }}"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            <i class="bi bi-bell mr-3 text-purple-600"></i>
                                            Notificações
                                            @if ($unreadNotifications > 0)
                                                <span
                                                    class="ml-auto badge bg-red-500 text-white text-xs">{{ $unreadNotifications }}</span>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="border-t border-gray-100">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition font-medium">
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

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="px-2 sm:px-4 md:px-8 py-4 sm:py-6 md:py-8">
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
                <button onclick="closeToast('successToast')"
                    class="text-white/80 hover:text-white p-2 ml-4 hover:bg-white/10 rounded-lg transition">
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
                <button onclick="closeToast('errorToast')"
                    class="text-white/80 hover:text-white p-2 ml-4 hover:bg-white/10 rounded-lg transition">
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
                <button onclick="closeToast('warningToast')"
                    class="text-white/80 hover:text-white p-2 ml-4 hover:bg-white/10 rounded-lg transition">
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
                <button onclick="closeToast('infoToast')"
                    class="text-white/80 hover:text-white p-2 ml-4 hover:bg-white/10 rounded-lg transition">
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
            return function (...args) {
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

            fetch(notificationsIndexRoute, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
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
            fetch(notificationsUnreadCountRoute, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
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
        document.addEventListener('DOMContentLoaded', function () {
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
            document.addEventListener('click', function (event) {
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
                link.addEventListener('click', function () {
                    if (window.innerWidth < 768) {
                        toggleMobileSidebar();
                    }
                });
            });

            // Keyboard shortcut (Ctrl+B) for sidebar toggle
            document.addEventListener('keydown', function (e) {
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
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('mobile-open');
                    mobileOverlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>

    <!-- SweetAlert2 -->
    <script>
        // SweetAlert2 Helper Functions
        window.confirmDelete = function (formId, message = 'Tem certeza que deseja deletar?', title = 'Confirmar Exclusão') {
            return Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sim, deletar!',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed && formId) {
                    document.getElementById(formId).submit();
                }
                return result;
            });
        };

        window.confirmAction = function (title, message, icon = 'question', confirmText = 'Sim, confirmar!', formId = null) {
            return Swal.fire({
                title: title,
                text: message,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed && formId) {
                    document.getElementById(formId).submit();
                }
                return result;
            });
        };

        window.showSuccess = function (message, title = 'Sucesso!') {
            Swal.fire({
                icon: 'success',
                title: title,
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        };

        window.showError = function (message, title = 'Erro!') {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message
            });
        };
    </script>
    @stack('scripts')
</body>

</html>