<aside class="w-64 bg-gray-900 text-white flex flex-col">
    <!-- Logo -->
    <div class="px-6 py-8 border-b border-gray-700">
        <h1 class="text-2xl font-bold"><i class="bi bi-building mr-2"></i>Edificar</h1>
        <p class="text-sm text-gray-400 mt-1">Sistema de Contribuições</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-8 space-y-4 overflow-y-auto">
        
        <!-- Dashboard -->
        <a href="{{ route('dashboard.membro') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('dashboard.*') ? 'bg-blue-600' : '' }}">
            <i class="bi bi-speedometer2 mr-3"></i>Dashboard
        </a>

        <!-- Contribuições -->
        <div>
            <button class="w-full text-left flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition" onclick="toggleMenu('contributions')">
                <i class="bi bi-cash-coin mr-3"></i>Contribuições
                <i class="bi bi-chevron-down ml-auto text-xs"></i>
            </button>
            <div id="contributions" class="hidden pl-8 space-y-2 mt-2">
                <a href="{{ route('contributions.index') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                    Minhas Contribuições
                </a>
                <a href="{{ route('contributions.create') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                    + Nova Contribuição
                </a>
            </div>
        </div>

        <!-- Pacotes de Compromisso -->
        <a href="{{ route('commitments.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
            <i class="bi bi-handshake mr-3"></i>Meu Compromisso
        </a>

        <!-- Relatórios (se autorizado) -->
        @if(auth()->user()->role !== 'membro')
        <div>
            <button class="w-full text-left flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition" onclick="toggleMenu('reports')">
                <i class="bi bi-file-earmark-pdf mr-3"></i>Relatórios
                <i class="bi bi-chevron-down ml-auto text-xs"></i>
            </button>
            <div id="reports" class="hidden pl-8 space-y-2 mt-2">
                @if(auth()->user()->role === 'lider_celula' || auth()->user()->role === 'supervisor' || auth()->user()->role === 'pastor_zona')
                <a href="{{ route('reports.cell') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                    Célula
                </a>
                @endif
                @if(auth()->user()->role === 'supervisor' || auth()->user()->role === 'pastor_zona')
                <a href="{{ route('reports.supervision') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                    Supervisão
                </a>
                @endif
                @if(auth()->user()->role === 'pastor_zona')
                <a href="{{ route('reports.zone') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                    Zona
                </a>
                @endif
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('reports.global') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                    Global
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- Admin Menu -->
        @if(auth()->user()->role === 'admin')
        <hr class="border-gray-700 my-4">
        <div class="text-xs uppercase font-bold text-gray-500 px-4 mb-4">Administração</div>
        
        <a href="{{ route('zones.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
            <i class="bi bi-map mr-3"></i>Zonas
        </a>
        <a href="{{ route('supervisions.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
            <i class="bi bi-diagram-3 mr-3"></i>Supervisões
        </a>
        <a href="{{ route('cells.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
            <i class="bi bi-people-fill mr-3"></i>Células
        </a>
        <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
            <i class="bi bi-person-badge mr-3"></i>Utilizadores
        </a>
        <a href="{{ route('packages.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
            <i class="bi bi-box-seam mr-3"></i>Pacotes
        </a>
        <a href="{{ route('contributions.pending') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
            <i class="bi bi-clock-history mr-3"></i>Contribuições Pendentes
        </a>
        @endif
    </nav>

    <!-- User Info -->
    <div class="px-6 py-4 border-t border-gray-700">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="text-gray-400 hover:text-white">
                <i class="bi bi-gear"></i>
            </a>
        </div>
    </div>

    <!-- Logout -->
    <div class="px-6 py-3 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button class="w-full text-left px-4 py-2 text-sm rounded hover:bg-gray-800 transition">
                <i class="bi bi-box-arrow-right mr-2"></i>Sair
            </button>
        </form>
    </div><!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Projeto Edificar')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col overflow-y-auto">
            <!-- Logo -->
            <div class="px-6 py-8 border-b border-gray-700">
                <h1 class="text-2xl font-bold">
                    <i class="bi bi-building mr-2"></i>Edificar
                </h1>
                <p class="text-sm text-gray-400 mt-1">Sistema de Contribuições</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-8 space-y-4 overflow-y-auto">
                
                <!-- Dashboard -->
                <a href="{{ route('dashboard.membro') }}" 
                   class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('dashboard.*') ? 'bg-blue-600' : '' }}">
                    <i class="bi bi-speedometer2 mr-3"></i>Dashboard
                </a>

                <!-- Contribuições -->
                <div>
                    <button class="w-full text-left flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition" onclick="toggleMenu('contributions')">
                        <i class="bi bi-cash-coin mr-3"></i>Contribuições
                        <i class="bi bi-chevron-down ml-auto text-xs"></i>
                    </button>
                    <div id="contributions" class="hidden pl-8 space-y-2 mt-2">
                        <a href="{{ route('contributions.index') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                            Minhas Contribuições
                        </a>
                        @if(auth()->user()->role !== 'membro')
                        <a href="{{ route('contributions.create') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                            + Nova Contribuição
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Pacotes de Compromisso -->
                <a href="{{ route('commitments.index') }}" 
                   class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                    <i class="bi bi-handshake mr-3"></i>Meu Compromisso
                </a>

                <!-- Relatórios -->
                @if(auth()->user()->role !== 'membro')
                <div>
                    <button class="w-full text-left flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition" onclick="toggleMenu('reports')">
                        <i class="bi bi-file-earmark-pdf mr-3"></i>Relatórios
                        <i class="bi bi-chevron-down ml-auto text-xs"></i>
                    </button>
                    <div id="reports" class="hidden pl-8 space-y-2 mt-2">
                        @if(auth()->user()->role === 'lider_celula' || auth()->user()->role === 'supervisor' || auth()->user()->role === 'pastor_zona')
                        <a href="{{ route('reports.cell') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                            Célula
                        </a>
                        @endif
                        @if(auth()->user()->role === 'supervisor' || auth()->user()->role === 'pastor_zona')
                        <a href="{{ route('reports.supervision') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                            Supervisão
                        </a>
                        @endif
                        @if(auth()->user()->role === 'pastor_zona')
                        <a href="{{ route('reports.zone') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                            Zona
                        </a>
                        @endif
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('reports.global') }}" class="block px-4 py-2 text-sm rounded hover:bg-gray-800">
                            Global
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Admin Menu -->
                @if(auth()->user()->role === 'admin')
                <hr class="border-gray-700 my-4">
                <div class="text-xs uppercase font-bold text-gray-500 px-4 mb-4">Administração</div>
                
                <a href="{{ route('zones.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                    <i class="bi bi-map mr-3"></i>Zonas
                </a>
                <a href="{{ route('supervisions.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                    <i class="bi bi-diagram-3 mr-3"></i>Supervisões
                </a>
                <a href="{{ route('cells.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                    <i class="bi bi-people-fill mr-3"></i>Células
                </a>
                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                    <i class="bi bi-person-badge mr-3"></i>Utilizadores
                </a>
                <a href="{{ route('packages.index') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                    <i class="bi bi-box-seam mr-3"></i>Pacotes
                </a>
                <a href="{{ route('contributions.pending') }}" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                    <i class="bi bi-clock-history mr-3"></i>Contribuições Pendentes
                </a>
                @endif
            </nav>

            <!-- User Info -->
            <div class="px-6 py-4 border-t border-gray-700">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="text-gray-400 hover:text-white">
                        <i class="bi bi-gear"></i>
                    </a>
                </div>
            </div>

            <!-- Logout -->
            <div class="px-6 py-3 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}" class="inline w-full">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm rounded hover:bg-gray-800 transition">
                        <i class="bi bi-box-arrow-right mr-2"></i>Sair
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-sm text-gray-500 mt-1">@yield('page-subtitle', 'Bem-vindo ao Projeto Edificar')</p>
                </div>
                
                <div class="flex items-center space-x-6">
                    <button class="text-gray-600 hover:text-gray-800 relative">
                        <i class="bi bi-bell text-xl"></i>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div class="border-l border-gray-300 pl-6">
                        <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container mx-auto px-6 py-8">
                    <!-- Toast Messages -->
                    @if ($message = Session::get('success'))
                    <div id="successToast" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center justify-between shadow-lg" role="alert">
                        <div class="flex items-center">
                            <i class="bi bi-check-circle mr-3"></i>
                            <span>{{ $message }}</span>
                        </div>
                        <button onclick="closeToast('successToast')" class="text-green-700 hover:text-green-900">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    @endif

                    @if ($message = Session::get('error'))
                    <div id="errorToast" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center justify-between shadow-lg" role="alert">
                        <div class="flex items-center">
                            <i class="bi bi-exclamation-circle mr-3"></i>
                            <span>{{ $message }}</span>
                        </div>
                        <button onclick="closeToast('errorToast')" class="text-red-700 hover:text-red-900">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    @endif

                    @if ($message = Session::get('warning'))
                    <div id="warningToast" class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg flex items-center justify-between shadow-lg" role="alert">
                        <div class="flex items-center">
                            <i class="bi bi-exclamation-triangle mr-3"></i>
                            <span>{{ $message }}</span>
                        </div>
                        <button onclick="closeToast('warningToast')" class="text-yellow-700 hover:text-yellow-900">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    @endif

                    @if ($message = Session::get('info'))
                    <div id="infoToast" class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg flex items-center justify-between shadow-lg" role="alert">
                        <div class="flex items-center">
                            <i class="bi bi-info-circle mr-3"></i>
                            <span>{{ $message }}</span>
                        </div>
                        <button onclick="closeToast('infoToast')" class="text-blue-700 hover:text-blue-900">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        // Toggle Menu Sidebar
        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        }

        // Close Toast
        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            toast.style.display = 'none';
        }

        // Auto-hide toasts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('[role="alert"]');
            toasts.forEach(toast => {
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 5000);
            });
        });
    </script>
</body>
</html>
</aside>
