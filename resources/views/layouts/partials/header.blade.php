@php
    $authUser = auth()->user();
    $role = $authUser->role ?? 'membro';
@endphp

<header class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 shadow-sm">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4 flex-1">
            <!-- Mobile Menu Button -->
            <button @click="mobileSidebarOpen = true"
                class="md:hidden text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="bi bi-list text-2xl"></i>
            </button>

            <!-- Desktop Sidebar Toggle -->
            <button @click="sidebarOpen = !sidebarOpen"
                class="hidden md:flex text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-100 rounded-lg transition-colors mr-2">
                <i class="bi bi-list text-2xl"></i>
            </button>

            <div class="hidden md:block">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                <p class="text-xs md:text-sm text-gray-500 mt-0.5">@yield('page-subtitle', 'Bem-vindo ao Portal Life Church')</p>
            </div>

            <!-- Search Bar (Desktop) -->
            <div class="hidden lg:flex flex-1 max-w-md ml-8 search-container">
                <div class="relative w-full">
                    <input type="text" id="searchInput" placeholder="Pesquisar membros, contribuições..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-zinc-900/60 border border-gray-300 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-gray-900 dark:text-zinc-100 placeholder-gray-400 dark:placeholder-zinc-500 transition-all"
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
                    class="text-gray-600 hover:text-orange-600 p-2.5 hover:bg-orange-50 rounded-xl transition-all duration-300 relative group border border-transparent hover:border-orange-100">
                    <i class="bi bi-bell-fill text-2xl"></i>
                    @if ($unreadNotifications > 0)
                        <span class="absolute -top-1 -right-1 flex h-5 w-5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-5 w-5 bg-red-600 text-[10px] font-bold text-white items-center justify-center shadow-sm">
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
                            class="text-xs text-orange-600 hover:text-orange-700 transition">
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
                            class="block text-center text-sm text-orange-600 hover:text-orange-700 font-medium">
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
                            <div class="w-10 h-10 rounded-full bg-orange-600 flex items-center justify-center flex-shrink-0 font-bold text-white">
                                {{ strtoupper(substr($authUser->name, 0, 1)) }}
                            </div>
                            <i class="bi bi-chevron-down text-gray-600 text-sm transition-transform duration-200"></i>
                        </button>
 
                        <!-- User Dropdown Menu -->
                        <div id="userMenu"
                            class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-zinc-900 rounded-lg shadow-xl border border-gray-200 dark:border-zinc-800 z-50">
                            <div class="p-3 border-b border-gray-100 dark:border-zinc-800">
                                <p class="text-sm font-semibold text-gray-800 dark:text-zinc-100">{{ $authUser->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">{{ $authUser->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-600 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <i class="bi bi-person mr-3 text-lg"></i> O Meu Perfil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                        <i class="bi bi-box-arrow-right mr-3 text-lg"></i> Sair do Sistema
                                      </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="hidden md:block border-l border-gray-300 pl-4">
                    <a href="{{ route('login') }}"
                        class="bg-orange-600 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-lg shadow-orange-600/20">
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
                class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-zinc-900/60 border border-gray-300 dark:border-zinc-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-gray-900 dark:text-zinc-100 placeholder-gray-400 dark:placeholder-zinc-500 transition-all"
                oninput="debouncedMobileSearch(this.value)">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <div id="mobileSearchResults" class="search-results hidden"></div>
        </div>
    </div>
</header>
