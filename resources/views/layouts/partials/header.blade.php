@php
    $authUser = auth()->user();
    $role = $authUser->role ?? 'membro';
@endphp

<header class="bg-white dark:bg-zinc-900 border-b border-gray-200 dark:border-zinc-800 px-3.5 md:px-5 py-2 pt-[max(0.5rem,env(safe-area-inset-top))] shadow-sm sticky top-0 z-30 transition-colors">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center space-x-2.5 flex-1 min-w-0">
            <!-- Mobile Menu Button -->
            <button @click="mobileSidebarOpen = true"
                aria-label="Abrir Menu"
                class="md:hidden text-gray-700 dark:text-zinc-300 hover:text-orange-700 dark:hover:text-orange-400 p-1.5 hover:bg-orange-50 dark:hover:bg-zinc-800 rounded-lg transition-colors flex-shrink-0">
                <i class="bi bi-list text-lg"></i>
            </button>

            <!-- Mobile Page Title -->
            <div class="md:hidden min-w-0 flex-1">
                <h2 class="text-sm font-black text-gray-950 dark:text-white truncate tracking-tight">@yield('page-title', 'Dashboard')</h2>
            </div>

            <!-- Desktop Sidebar Toggle -->
            <button @click="sidebarOpen = !sidebarOpen"
                class="hidden md:flex text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white p-1.5 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                <i class="bi bi-layout-sidebar text-sm"></i>
            </button>

            <div class="hidden md:block min-w-0">
                <h2 class="text-sm md:text-base font-black text-gray-950 dark:text-white tracking-tight truncate">@yield('page-title', 'Dashboard')</h2>
                <p class="text-[11px] text-gray-500 dark:text-zinc-400 truncate">@yield('page-subtitle', 'Bem-vindo ao Portal Life Church')</p>
            </div>

            <!-- Search Bar (Desktop) -->
            <div class="hidden lg:flex flex-1 max-w-sm ml-4 search-container">
                <div class="relative w-full">
                    <input type="text" id="searchInput" placeholder="Pesquisar células, supervisões, zonas, membros..."
                        class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 dark:bg-zinc-800/70 border border-gray-300 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-gray-900 dark:text-zinc-100 placeholder-gray-400 dark:placeholder-zinc-400 transition-all"
                        oninput="debouncedDesktopSearch(this.value)">
                    <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 text-xs"></i>

                    <!-- Search Results Dropdown -->
                    <div id="searchResults" class="search-results hidden"></div>
                </div>
            </div>

            <!-- Mobile Page Actions (somente no mobile, apenas ícones) -->
            <div class="flex md:hidden items-center ml-auto gap-1 header-actions">
                @yield('header-actions')
            </div>
        </div>

        <div class="flex items-center space-x-1.5 md:space-x-2.5">
            <!-- Mobile Search Button -->
            <button onclick="toggleMobileSearch()"
                class="lg:hidden text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white p-1.5 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                <i class="bi bi-search text-base"></i>
            </button>

            <!-- Notifications -->
            <div class="relative">
                <button onclick="toggleNotifications()"
                    class="text-gray-600 dark:text-zinc-400 hover:text-orange-700 dark:hover:text-orange-400 p-1.5 hover:bg-orange-50 dark:hover:bg-zinc-800 rounded-lg transition-colors relative border border-transparent hover:border-orange-200 dark:hover:border-zinc-700">
                    <i class="bi bi-bell text-base"></i>
                    <span data-notification-badge class="{{ $unreadNotifications > 0 ? '' : 'hidden' }} absolute -top-1 -right-1 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-600 text-[9px] font-black text-white items-center justify-center shadow-sm badge-count">
                            {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                        </span>
                    </span>
                </button>

                <!-- Notifications Panel -->
                <div id="notificationsPanel" class="notifications-panel hidden fade-in bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-zinc-800 flex justify-between items-center">
                        <h4 class="text-xs font-black uppercase tracking-wider text-gray-800 dark:text-zinc-200">Notificações</h4>
                        <button onclick="markAllAsRead()"
                            class="text-[11px] font-bold text-orange-700 dark:text-orange-400 hover:text-orange-800 transition">
                            Marcar todas como lidas
                        </button>
                    </div>
                    <div id="notificationsContent"
                        class="p-3 text-xs text-gray-600 dark:text-zinc-400 space-y-2 max-h-80 overflow-y-auto">
                        <div class="text-center text-gray-500 dark:text-zinc-500 py-4">
                            <i class="bi bi-arrow-clockwise animate-spin mr-1.5"></i>A carregar...
                        </div>
                    </div>
                    <div class="px-3 py-2.5 border-t border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-950/60">
                        <a href="{{ route('notifications.all') }}"
                            class="block text-center text-xs text-orange-700 dark:text-orange-400 hover:text-orange-800 font-bold">
                            Ver todas as notificações <i class="bi bi-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Theme Toggle (Compact) -->
            <button onclick="toggleTheme()"
                class="bg-gray-100 hover:bg-gray-200 p-1.5 rounded-lg transition-colors text-gray-600 dark:text-gray-300 dark:bg-zinc-800 dark:hover:bg-zinc-700">
                <i id="themeIcon" class="bi bi-moon-fill text-sm"></i>
            </button>

            @if($authUser)
                <div class="hidden md:block border-l border-gray-200 dark:border-zinc-800 pl-2.5">
                    <div class="relative">
                        <button type="button" onclick="toggleUserMenu()"
                            class="flex items-center space-x-2 hover:bg-gray-50 dark:hover:bg-zinc-800 px-2 py-1 rounded-lg transition border border-transparent hover:border-gray-200 dark:hover:border-zinc-700">
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-900 dark:text-zinc-100 truncate max-w-[140px]">
                                    {{ $authUser->name }}
                                </p>
                                <p class="text-[10px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ ucfirst(str_replace('_', ' ', $role ?? 'membro')) }}
                                </p>
                            </div>
                            <div class="w-8 h-8 rounded-lg bg-orange-700 flex items-center justify-center flex-shrink-0 text-xs font-black text-white shadow-sm">
                                {{ strtoupper(substr($authUser->name, 0, 1)) }}
                            </div>
                            <i class="bi bi-chevron-down text-gray-500 dark:text-zinc-400 text-[10px] transition-transform duration-200"></i>
                        </button>
 
                        <!-- User Dropdown Menu -->
                        <div id="userMenu"
                            class="hidden absolute right-0 mt-1.5 w-52 bg-white dark:bg-zinc-900 rounded-xl shadow-lg border border-gray-200 dark:border-zinc-800 z-50 overflow-hidden">
                            <div class="px-3.5 py-2.5 border-b border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-800/40">
                                <p class="text-xs font-bold text-gray-900 dark:text-zinc-100 truncate">{{ $authUser->name }}</p>
                                <p class="text-[11px] text-gray-500 dark:text-zinc-400 truncate">{{ $authUser->email }}</p>
                            </div>
                            <div class="py-1">
                                @if($authUser && $authUser->role === 'super_admin')
                                    @if(session('portal_context') === 'local')
                                        <a href="{{ route('national.switch.national') }}"
                                            class="flex items-center px-3.5 py-2 text-xs text-orange-700 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-zinc-800 font-bold transition-colors">
                                            <i class="bi bi-globe-americas mr-2.5 text-sm"></i> Consola Nacional
                                        </a>
                                    @else
                                        <a href="{{ route('national.switch.local') }}"
                                            class="flex items-center px-3.5 py-2 text-xs text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800 font-bold transition-colors">
                                            <i class="bi bi-box-arrow-in-right mr-2.5 text-sm text-orange-700"></i> Portal Local (Life Church)
                                        </a>
                                    @endif
                                @endif
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <i class="bi bi-person mr-2.5 text-sm text-gray-400"></i> O Meu Perfil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center px-3.5 py-2 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                        <i class="bi bi-box-arrow-right mr-2.5 text-sm"></i> Sair do Sistema
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="hidden md:block border-l border-gray-200 dark:border-zinc-800 pl-3">
                    <a href="{{ route('login') }}"
                        class="bg-orange-700 text-white px-3.5 py-1.5 rounded-lg font-bold text-xs shadow-sm hover:bg-orange-800">
                        <i class="bi bi-box-arrow-in-right mr-1.5"></i> Entrar
                    </a>
                </div>
            @endif
        </div>
    </div>
  
    <!-- Mobile Search Input -->
    <div id="mobileSearchInput" class="lg:hidden mt-2.5 search-container hidden fade-in">
        <div class="relative w-full">
            <input type="text" placeholder="Pesquisar células, supervisões, zonas, membros..."
                class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 dark:bg-zinc-800/70 border border-gray-300 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-gray-900 dark:text-zinc-100 placeholder-gray-400 dark:placeholder-zinc-400 transition-all"
                oninput="debouncedMobileSearch(this.value)">
            <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-zinc-500 text-xs"></i>
            <div id="mobileSearchResults" class="search-results hidden"></div>
        </div>
    </div>
</header>
