@php
    $authUser = auth()->user();
    $role = $authUser->role ?? 'membro';
@endphp
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

    <!-- PWA & Mobile Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Life Church">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tom Select for better searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

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
                position: fixed;
                top: 70px;
                left: 1rem;
                right: 1rem;
                margin: 0 auto;
                width: calc(100vw - 2rem);
                transform: none;
                z-index: 1000;
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

        /* SweetAlert notifications are now used globally - toast CSS removed */

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

        [x-cloak] {
            display: none !important;
        }

        @media (max-width: 767px) {

            .header-actions a,
            .header-actions button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.5rem;
                border-radius: 0.5rem;
                color: #4b5563;
                background: transparent;
                border: 0;
                box-shadow: none;
                transition: color 0.2s ease, background-color 0.2s ease;
            }

            .header-actions a:hover,
            .header-actions button:hover {
                color: #1f2937;
                background-color: #f3f4f6;
            }
        }

        .grid-compact {
            display: grid;
            width: 100%;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 0.75rem;
            justify-content: stretch;
            align-content: stretch;
        }

        .grid-compact>* {
            min-width: 0;
        }

        @media (min-width: 640px) {
            .grid-compact {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 768px) {
            .grid-compact {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 1rem;
            }
        }

        @media (min-width: 1024px) {
            .grid-compact {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .grid-compact {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (min-width: 1536px) {
            .grid-compact {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .compact-card {
            padding: 0.875rem !important;
            border-radius: 1.25rem !important;
        }

        @media (min-width: 768px) {
            .compact-card {
                padding: 1rem !important;
                border-radius: 1.5rem !important;
            }
        }

        .compact-card .card-body {
            padding: 0.875rem !important;
        }

        .compact-card .card-footer {
            padding: 0.75rem 0.875rem !important;
        }

        .nav-item-highlight {
            box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.25), 0 10px 24px rgba(249, 115, 22, 0.18);
            transition: box-shadow 0.6s ease;
        }

        .phone-prefix-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .phone-prefix-label {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            font-weight: 800;
            color: #6b7280;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            padding: 2px 8px;
            letter-spacing: 0.08em;
        }

        .phone-prefix-input {
            padding-left: 84px !important;
        }

        .line-clamp-1,
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-1 {
            -webkit-line-clamp: 1;
        }

        .line-clamp-2 {
            -webkit-line-clamp: 2;
        }

        .table-compact th,
        .table-compact td {
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
        }

        .table-compact th {
            font-size: 0.625rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .action-icon {
            width: 2.25rem;
            height: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }

        /* Tom Select Custom Styling */
        .ts-wrapper {
            width: 100%;
        }

        .ts-wrapper .ts-control {
            padding: 0.75rem 1rem;
            border: 1px solid transparent;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: #374151;
            background: #f9fafb;
            min-height: 48px;
            box-shadow: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .ts-wrapper .ts-control:hover {
            background: #f3f4f6;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            background: white;
            color: #111827;
        }

        .ts-wrapper .ts-control input {
            font-weight: 700;
            font-size: 0.875rem;
            color: #111827;
        }

        .ts-wrapper .ts-control .item {
            font-weight: 700;
        }

        .ts-wrapper .ts-dropdown {
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            margin-top: 4px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            background: white;
            overflow: hidden;
        }

        .ts-wrapper .ts-dropdown .option {
            padding: 0.75rem 1rem;
            font-weight: 500;
            color: #374151;
            transition: all 0.15s ease;
        }

        .ts-wrapper .ts-dropdown .option:hover,
        .ts-wrapper .ts-dropdown .option.active {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .ts-wrapper .ts-dropdown .option.selected {
            background: #eff6ff;
            color: #2563eb;
            font-weight: 700;
        }

        .ts-wrapper .ts-dropdown .highlight {
            background: transparent;
            color: inherit;
            font-weight: 900;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .ts-wrapper .ts-dropdown .no-results {
            padding: 1rem;
            text-align: center;
            color: #9ca3af;
            font-style: italic;
        }

        /* Dark theme support */
        [data-theme="dark"] .ts-wrapper .ts-control {
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        [data-theme="dark"] .ts-wrapper.focus .ts-control {
            border-color: #3b82f6;
            background: #0f172a;
        }

        [data-theme="dark"] .ts-wrapper .ts-dropdown {
            background: #1e293b;
            border-color: #334155;
        }

        [data-theme="dark"] .ts-wrapper .ts-dropdown .option {
            color: #e2e8f0;
        }

        [data-theme="dark"] .ts-wrapper .ts-dropdown .option:hover,
        [data-theme="dark"] .ts-wrapper .ts-dropdown .option.active {
            background: #334155;
            color: #60a5fa;
        }

        [data-theme="dark"] .ts-wrapper .ts-dropdown .option.selected {
            background: #1e3a5f;
            color: #93c5fd;
        }

        .custom-select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 1rem center;
            background-repeat: no-repeat;
            background-size: 1.25em 1.25em;
            padding-right: 3rem !important;
        }

        [data-theme="dark"] .custom-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
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

                        <!-- Mobile & Desktop Page Actions -->
                        <div class="flex items-center ml-2 gap-2 header-actions">
                            @yield('header-actions')
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 md:space-x-4">
                        <!-- Mobile Search Button -->
                        <button onclick="toggleMobileSearch()"
                            class="lg:hidden text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="bi bi-search text-2xl"></i>
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



                        @if($authUser)
                            <div class="hidden md:block border-l border-gray-300 pl-4">
                                <div class="relative">
                                    <button type="button" onclick="toggleUserMenu()"
                                        class="flex items-center space-x-2 hover:bg-gray-100 p-2 rounded-lg transition">
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-800 truncate max-w-[150px]">
                                                {{ $authUser->name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ ucfirst(str_replace('_', ' ', $role ?? 'membro')) }}
                                            </p>
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
                                        <div class="py-1">
                                            <a href="{{ route('profile.edit') }}"
                                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <i class="bi bi-person mr-3"></i> O Meu Perfil
                                            </a>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit"
                                                    class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                    <i class="bi bi-box-arrow-right mr-3"></i> Sair do Sistema
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="hidden md:block border-l border-gray-300 pl-4">
                                <a href="{{ route('login') }}"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-lg shadow-blue-600/20">
                                    <i class="bi bi-box-arrow-in-right mr-2"></i> Entrar
                                </a>
                            </div>
                        @endif
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

    <!-- SweetAlert Session Messages -->
    @if ($message = Session::get('success'))
        <script>document.addEventListener('DOMContentLoaded', () => window.showSuccess(@json($message)));</script>
    @endif
    @if ($message = Session::get('error'))
        <script>document.addEventListener('DOMContentLoaded', () => window.showError(@json($message)));</script>
    @endif
    @if ($message = Session::get('warning'))
        <script>document.addEventListener('DOMContentLoaded', () => window.showWarning(@json($message)));</script>
    @endif
    @if ($message = Session::get('info'))
        <script>document.addEventListener('DOMContentLoaded', () => window.showInfo(@json($message)));</script>
    @endif

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

            // Notify Leaflet maps to recalculate size after transition
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 300);
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

        function initSearchableSelects() {
            const selects = document.querySelectorAll('select.searchable-select, select:not([data-searchable="false"]):not([data-tomselect-ready])');

            selects.forEach((select) => {
                // Skip if already initialized or not suitable
                if (select.dataset.tomselectReady || select.multiple || select.size > 1) return;

                // Only auto-apply to selects with > 6 options if they don't have the class
                if (!select.classList.contains('searchable-select') && select.options.length <= 6) return;

                select.setAttribute('data-tomselect-ready', 'true');

                try {
                    new TomSelect(select, {
                        create: false,
                        placeholder: select.dataset.placeholder || select.dataset.searchPlaceholder || 'Pesquisar...',
                        allowEmptyOption: true,
                        maxOptions: 100,
                        copyAttributesToRoot: true,
                        render: {
                            no_results: function (data, escape) {
                                return '<div class="no-results">Nenhum resultado encontrado para "' + escape(data.input) + '"</div>';
                            },
                            option: function (data, escape) {
                                return '<div class="option">' + data.text + '</div>';
                            },
                            item: function (data, escape) {
                                return '<div class="item">' + data.text + '</div>';
                            }
                        },
                        onInitialize: function () {
                            // Ensure the control has the same height/feel as other inputs
                            const control = this.control;
                            if (select.classList.contains('custom-select')) {
                                control.classList.add('custom-select-ready');
                            }
                        }
                    });
                } catch (e) {
                    console.error('TomSelect Init Error:', e, select);
                }
            });
        }

        function hasSelectLabel(select) {
            if (select.closest('label')) return true;
            if (select.id && document.querySelector(`label[for="${select.id}"]`)) return true;
            if (select.previousElementSibling && select.previousElementSibling.tagName === 'LABEL') return true;
            let node = select.parentElement;
            let depth = 0;
            while (node && depth < 3) {
                if (node.querySelector && node.querySelector('label')) return true;
                node = node.parentElement;
                depth += 1;
            }
            return false;
        }

        function getSelectLabelText(select) {
            if (select.dataset.label) return select.dataset.label;
            if (select.getAttribute('aria-label')) return select.getAttribute('aria-label');
            if (select.name) {
                const raw = select.name
                    .replace(/\[\]$/g, '')
                    .replace(/\[\d+\]/g, '')
                    .replace(/_?id$/i, '')
                    .replace(/[_\-]+/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();

                const key = raw.toLowerCase();
                const map = {
                    'preacher': 'Pregador',
                    'zone': 'Zona',
                    'supervision': 'Supervisão',
                    'leader': 'Líder',
                    'cell': 'Célula',
                    'package': 'Pacote',
                    'role': 'Função',
                    'status': 'Estado',
                    'category': 'Categoria',
                    'month': 'Mês',
                    'year': 'Ano',
                    'type': 'Tipo',
                    'service type': 'Tipo de Culto',
                    'meeting type': 'Tipo de Encontro',
                    'event type': 'Tipo de Evento',
                    'course': 'Curso',
                    'course class': 'Turma',
                    'teacher male': 'Professor (Masculino)',
                    'teacher female': 'Professora (Feminino)',
                    'assistant male': 'Assistente (Masculino)',
                    'assistant female': 'Assistente (Feminino)',
                    'responsible': 'Responsável',
                    'pastor': 'Pastor',
                    'gender': 'Gênero',
                    'currency': 'Moeda',
                    'timezone': 'Fuso Horário',
                    'date format': 'Formato de Data',
                };
                if (map[key]) return map[key];

                return raw.replace(/\b\w/g, (c) => c.toUpperCase());
            }
            return null;
        }

        function addMissingSelectLabels() {
            const selects = document.querySelectorAll('select');
            selects.forEach((select) => {
                if (hasSelectLabel(select)) return;
                const labelText = getSelectLabelText(select);
                if (!labelText) return;
                if (!select.id) {
                    select.id = `select-${Math.random().toString(36).slice(2)}`;
                }
                const label = document.createElement('label');
                label.className = 'block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2';
                label.setAttribute('for', select.id);
                label.textContent = labelText;
                select.parentNode.insertBefore(label, select);
            });
        }

        function initPhonePrefixInputs() {
            const selector = 'input[type="tel"], input[name*="phone"], input[name*="whatsapp"]';
            document.querySelectorAll(selector).forEach((input) => {
                if (input.dataset.phonePrefix === 'false') return;
                if (input.closest('.phone-prefix-wrapper')) return;
                if (input.dataset.phonePrefixReady === 'true') return;
                input.dataset.phonePrefixReady = 'true';

                const wrapper = document.createElement('div');
                wrapper.className = 'phone-prefix-wrapper';
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);

                const label = document.createElement('span');
                label.className = 'phone-prefix-label';
                label.textContent = '+258';
                wrapper.appendChild(label);

                input.classList.add('phone-prefix-input');
                if (!input.placeholder) {
                    input.placeholder = '823562000';
                }

                input.addEventListener('focus', () => {
                    input.value = input.value.replace(/\s+/g, '');
                    if (!input.value) {
                        input.value = '+258';
                    }
                });

                input.addEventListener('blur', () => {
                    if (input.value === '+258') {
                        input.value = '';
                        return;
                    }
                    const digits = input.value.replace(/\D/g, '');
                    if (digits.length === 9 && !input.value.startsWith('+258')) {
                        input.value = `+258${digits}`;
                    }
                    const normalized = input.value.replace(/\D/g, '');
                    if (normalized.startsWith('258') && normalized.length === 12) {
                        const local = normalized.slice(3);
                        input.value = `+258 ${local.slice(0, 2)} ${local.slice(2, 5)} ${local.slice(5, 9)}`;
                    }
                });
            });
        }

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
                        const typeMap = {
                            contribution_verified: { icon: 'bi-check-circle-fill', bg: 'bg-green-50', text: 'text-green-600', label: 'Confirmada', badge: 'bg-green-50 text-green-700' },
                            contribution_rejected: { icon: 'bi-x-circle-fill', bg: 'bg-red-50', text: 'text-red-600', label: 'Rejeitada', badge: 'bg-red-50 text-red-700' },
                            contribution_created: { icon: 'bi-cash-coin', bg: 'bg-blue-50', text: 'text-blue-600', label: 'Registo', badge: 'bg-blue-50 text-blue-700' },
                            contribution_pending_validation: { icon: 'bi-exclamation-triangle-fill', bg: 'bg-orange-50', text: 'text-orange-600', label: 'Validação', badge: 'bg-orange-50 text-orange-700' },
                            contribution_verified_manager: { icon: 'bi-check-circle-fill', bg: 'bg-emerald-50', text: 'text-emerald-600', label: 'Pacote', badge: 'bg-emerald-50 text-emerald-700' },
                            contribution_rejected_manager: { icon: 'bi-x-circle-fill', bg: 'bg-rose-50', text: 'text-rose-600', label: 'Pacote', badge: 'bg-rose-50 text-rose-700' },
                            pending_contributions: { icon: 'bi-exclamation-triangle-fill', bg: 'bg-orange-50', text: 'text-orange-600', label: 'Comissão', badge: 'bg-orange-50 text-orange-700' },
                            member_created: { icon: 'bi-person-plus-fill', bg: 'bg-purple-50', text: 'text-purple-600', label: 'Conta', badge: 'bg-purple-50 text-purple-700' },
                            member_added_to_cell: { icon: 'bi-people-fill', bg: 'bg-sky-50', text: 'text-sky-600', label: 'Célula', badge: 'bg-sky-50 text-sky-700' },
                            commitment_chosen: { icon: 'bi-handshake-fill', bg: 'bg-indigo-50', text: 'text-indigo-600', label: 'Compromisso', badge: 'bg-indigo-50 text-indigo-700' },
                            commitment_expiring: { icon: 'bi-clock-fill', bg: 'bg-yellow-50', text: 'text-yellow-600', label: 'Prazo', badge: 'bg-yellow-50 text-yellow-700' },
                            user_promoted: { icon: 'bi-star-fill', bg: 'bg-yellow-50', text: 'text-yellow-600', label: 'Cargo', badge: 'bg-yellow-50 text-yellow-700' },
                        };

                        let html = '<ul class="space-y-2">';
                        data.forEach(n => {
                            const type = n.type || 'general';
                            const cfg = typeMap[type] || { icon: 'bi-bell-fill', bg: 'bg-gray-50', text: 'text-gray-500', label: 'Sistema', badge: 'bg-gray-100 text-gray-600' };

                            html += `
                                <a href="${n.link}" class="flex items-start p-3 hover:bg-gray-50 rounded-lg cursor-pointer block transition">
                                    <div class="w-9 h-9 rounded-xl ${cfg.bg} ${cfg.text} flex items-center justify-center mr-3 mt-1 flex-shrink-0">
                                        <i class="bi ${cfg.icon}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-medium text-gray-800 text-sm">${n.title}</p>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest ${cfg.badge}">
                                                ${cfg.label}
                                            </span>
                                        </div>
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
                    const bell = document.querySelector('.bi-bell');
                    if (!bell || !bell.nextElementSibling) return;
                    const badge = bell.nextElementSibling;
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
            addMissingSelectLabels();
            initSearchableSelects();
            initPhonePrefixInputs();

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

        window.showWarning = function (message, title = 'Atenção!') {
            Swal.fire({
                icon: 'warning',
                title: title,
                text: message,
                timer: 4000,
                showConfirmButton: false
            });
        };

        window.showInfo = function (message, title = 'Informação') {
            Swal.fire({
                icon: 'info',
                title: title,
                text: message,
                timer: 4000,
                showConfirmButton: false
            });
        };
    </script>
    @stack('scripts')
    <!-- PWA & Immersive Experience Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Register Service Worker
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW Registered'))
                    .catch(err => console.log('SW Error', err));
            }

            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                // Prevent Chrome 67 and earlier from automatically showing the prompt
                e.preventDefault();
                // Stash the event so it can be triggered later.
                deferredPrompt = e;
                console.log('beforeinstallprompt event fired');

                // If we are on mobile, show the prompt with the Install option
                showImmersivePrompt(true);
            });

            // check if on mobile
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

            // DISABLED: Intrusive popup removed per user request
            // Auto-prompt is now disabled. Users can use the header button instead.

            function showImmersivePrompt(canInstall) {
                if (sessionStorage.getItem('pwa_prompt_shown')) return;

                Swal.fire({
                    title: 'Portal Life Church App',
                    text: canInstall
                        ? 'Deseja instalar o aplicativo para acesso rápido e em tela cheia?'
                        : 'Para uma melhor experiência, você pode usar o modo tela cheia ou adicionar à tela de início.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: canInstall ? 'Instalar App' : 'Tela Cheia',
                    cancelButtonText: 'Agora não',
                    footer: '<span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Dica: Abrir como app economiza dados e melhora a navegação.</span>'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (canInstall && deferredPrompt) {
                            deferredPrompt.prompt();
                            deferredPrompt.userChoice.then((choiceResult) => {
                                if (choiceResult.outcome === 'accepted') {
                                    console.log('User accepted the install prompt');
                                }
                                deferredPrompt = null;
                            });
                        } else {
                            enterFullScreen();
                        }
                    }
                    sessionStorage.setItem('pwa_prompt_shown', 'true');
                });
            }

            function enterFullScreen() {
                const doc = window.document;
                const docEl = doc.documentElement;

                const requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;

                if (requestFullScreen) {
                    requestFullScreen.call(docEl);
                } else if (isMobile && /iPhone|iPad|iPod/.test(navigator.userAgent)) {
                    Swal.fire({
                        title: 'Instalação no iOS',
                        text: 'Para instalar o app no iPhone/iPad: clique no botão de Compartilhar e selecione "Adicionar à Tela de Início".',
                        icon: 'info',
                        confirmButtonText: 'Entendido'
                    });
                }
            }
        });
    </script>
</body>

</html>