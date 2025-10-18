<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Projeto Edificar')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- CSS para a rotação do ícone do submenu --}}
    <style>
        .rotate-180 {
            transform: rotate(180deg);
        }

        .bi-chevron-down {
            transition: transform 0.2s ease-in-out;
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

        .toast-enter {
            animation: slideInRight 0.4s ease-out;
        }

        .toast-exit {
            animation: slideOutRight 0.3s ease-out;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="flex h-screen bg-gray-100">
        <aside class="w-64 bg-gray-900 text-white flex flex-col overflow-y-auto">
            <div class="px-6 py-8 border-b border-gray-700">
                <h1 class="text-2xl font-bold">
                    <i class="bi bi-building mr-2"></i>Edificar
                </h1>
                <p class="text-sm text-gray-400 mt-1">Sistema de Contribuições</p>
            </div>

            <nav class="flex-1 px-4 py-8 space-y-4 overflow-y-auto">
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
                @endphp

                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('dashboard') ? 'bg-blue-600' : '' }}">
                    <i class="bi bi-speedometer2 mr-3"></i>Dashboard
                </a>
<div>
    <button class="w-full text-left flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition"
        onclick="toggleMenu('contributions')">
        <i class="bi bi-cash-coin mr-3"></i>Contribuições
        <i
            class="bi bi-chevron-down ml-auto text-xs {{ request()->routeIs('contributions.*') ? 'rotate-180' : '' }}"></i>
    </button>
    <div id="contributions"
        class="pl-8 space-y-2 mt-2 {{ request()->routeIs('contributions.*') ? '' : 'hidden' }}">
        
        @if ($role !== 'admin')
        <a href="{{ route('contributions.index', ['mine' => 1]) }}"
            class="block px-4 py-2 text-sm rounded hover:bg-gray-800 {{ request()->routeIs('contributions.index') && request()->query('mine') ? 'text-blue-400' : '' }}">
            Minhas Contribuições
        </a>
        @endif
        
        @if (in_array($role, ['admin', 'pastor_zona', 'supervisor', 'lider_celula']))
            <a href="{{ route('contributions.index') }}"
                class="block px-4 py-2 text-sm rounded hover:bg-gray-800 {{ request()->routeIs('contributions.index') && !request()->query('mine') ? 'text-blue-400' : '' }}">
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
            class="block px-4 py-2 text-sm rounded hover:bg-gray-800 {{ request()->routeIs('contributions.create') ? 'text-blue-400' : '' }}">
            + Nova Contribuição
        </a>
        @endif
    </div>
</div>

                <div>

                    @if (auth()->user()->role !== 'membro')
                        <!-- Membros Rápido -->
                        <div>
                            <button
                                class="w-full text-left flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition"
                                onclick="toggleMenu('members')">
                                <i class="bi bi-person-plus mr-3 text-lg"></i>
                                <span class="font-medium">Criar Membro</span>
                                <i class="bi bi-chevron-down ml-auto text-xs"></i>
                            </button>
                            <div id="members" class="hidden pl-8 space-y-1 mt-2 bg-blue-950 rounded">
                                <a href="{{ route('members.create') }}"
                                    class="block px-4 py-2 text-sm rounded hover:bg-blue-700 text-blue-100">
                                    <i class="bi bi-plus-circle mr-2"></i>+ Novo Membro
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <a href="{{ route('commitments.index') }}"
                    class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('commitments.*') ? 'bg-blue-600' : '' }}">
                    <i class="bi bi-handshake mr-3"></i>Meu Compromisso
                </a>

                @if ($role !== 'membro')
                    <div>
                        <button
                            class="w-full text-left flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition"
                            onclick="toggleMenu('reports')">
                            <i class="bi bi-file-earmark-pdf mr-3"></i>Relatórios
                            <i
                                class="bi bi-chevron-down ml-auto text-xs {{ request()->routeIs('reports.*') ? 'rotate-180' : '' }}"></i>
                        </button>
                        <div id="reports"
                            class="pl-8 space-y-2 mt-2 {{ request()->routeIs('reports.*') ? '' : 'hidden' }}">
                            @if ($can('view_cell_report'))
                                <a href="{{ route('reports.cell') }}"
                                    class="block px-4 py-2 text-sm rounded hover:bg-gray-800 {{ request()->routeIs('reports.cell') ? 'text-blue-400' : '' }}">Célula</a>
                            @endif
                            @if ($can('view_supervision_report'))
                                <a href="{{ route('reports.supervision') }}"
                                    class="block px-4 py-2 text-sm rounded hover:bg-gray-800 {{ request()->routeIs('reports.supervision') ? 'text-blue-400' : '' }}">Supervisão</a>
                            @endif
                            @if ($can('view_zone_report'))
                                <a href="{{ route('reports.zone') }}"
                                    class="block px-4 py-2 text-sm rounded hover:bg-gray-800 {{ request()->routeIs('reports.zone') ? 'text-blue-400' : '' }}">Zona</a>
                            @endif
                            @if ($can('view_global_report'))
                                <a href="{{ route('reports.global') }}"
                                    class="block px-4 py-2 text-sm rounded hover:bg-gray-800 {{ request()->routeIs('reports.global') ? 'text-blue-400' : '' }}">Global</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($role === 'admin')
                    <hr class="border-gray-700 my-4">
                    <div class="text-xs uppercase font-bold text-gray-500 px-4 mb-4">Administração</div>

                    <a href="{{ route('zones.index') }}"
                        class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('zones.*') ? 'bg-blue-600' : '' }}">
                        <i class="bi bi-map mr-3"></i>Zonas
                    </a>
                    <a href="{{ route('supervisions.index') }}"
                        class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('supervisions.*') ? 'bg-blue-600' : '' }}">
                        <i class="bi bi-diagram-3 mr-3"></i>Supervisões
                    </a>
                    <a href="{{ route('cells.index') }}"
                        class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('cells.*') ? 'bg-blue-600' : '' }}">
                        <i class="bi bi-people-fill mr-3"></i>Células
                    </a>

                    <div>
                        <button
                            class="w-full text-left flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition"
                            onclick="toggleMenu('users')">
                            <i class="bi bi-person-badge mr-3"></i>Utilizadores
                            <i
                                class="bi bi-chevron-down ml-auto text-xs {{ request()->routeIs('users.*') ? 'rotate-180' : '' }}"></i>
                        </button>
                        <div id="users"
                            class="pl-8 space-y-2 mt-2 {{ request()->routeIs('users.*') ? '' : 'hidden' }}">
                            <a href="{{ route('users.index') }}"
                                class="block px-4 py-2 text-sm rounded hover:bg-gray-800 {{ request()->routeIs('users.index') && !request()->query('role') ? 'text-blue-400' : '' }}">
                                Todos
                            </a>
                            <a href="{{ route('users.index', ['role' => 'lider_celula']) }}"
                                class="block px-4 py-2 text-sm rounded hover:bg-gray-800 {{ request()->query('role') == 'lider_celula' ? 'text-blue-400' : '' }}">
                                Líderes
                            </a>
                            <a href="{{ route('users.index', ['role' => 'membro']) }}"
                                class="block px-4 py-2 text-sm rounded hover:bg-gray-800 {{ request()->query('role') == 'membro' ? 'text-blue-400' : '' }}">
                                Membros
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('packages.index') }}"
                        class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('packages.*') ? 'bg-blue-600' : '' }}">
                        <i class="bi bi-box-seam mr-3"></i>Pacotes
                    </a>
                    <a href="{{ route('contributions.pending') }}"
                        class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('contributions.pending') ? 'bg-blue-600' : '' }}">
                        <i class="bi bi-clock-history mr-3"></i>Contribuições Pendentes
                    </a>
                @endif
            </nav>

            <div class="mt-auto">
                <div class="px-6 py-4 border-t border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-sm font-semibold">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $role)) }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="text-gray-400 hover:text-white"
                            title="Editar Perfil">
                            <i class="bi bi-gear"></i>
                        </a>
                    </div>
                </div>
                <div class="border-t border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center px-6 py-3 text-red-400 hover:bg-red-900/50 transition">
                            <i class="bi bi-box-arrow-right mr-3"></i>Sair
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
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
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container mx-auto px-6 py-8">
                    <!-- Toast Messages -->
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
        // Toggle Menu Sidebar (Versão melhorada com rotação)
        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            const icon = menu.previousElementSibling.querySelector('.bi-chevron-down');

            menu.classList.toggle('hidden');
            if (icon) {
                icon.classList.toggle('rotate-180');
            }
        }

        // Close Toast (Usando suas animações CSS)
        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                // Adiciona a classe de animação de saída que você criou
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');

                // Remover do DOM após a animação (300ms do seu CSS)
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }

        // Auto-hide e animação de entrada (TUDO EM UM SÓ LUGAR)
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('[role="alert"]');

            toasts.forEach(toast => {
                // 1. Adicionar animação de entrada (do seu CSS)
                toast.classList.add('toast-enter');

                // 2. Definir o auto-hide para chamar a sua função closeToast
                //    Isto faz com que desapareçam após 5 segundos
                setTimeout(() => {
                    // Chama a mesma função do botão 'X'
                    closeToast(toast.id);
                }, 5000); // 5000ms = 5 segundos
            });
        });
    </script>
</body>

</html>
