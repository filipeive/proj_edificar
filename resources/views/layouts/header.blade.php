<header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
        <p class="text-sm text-gray-500 mt-1">@yield('page-subtitle', 'Bem-vindo ao Projeto Edificar')</p>
    </div>
    
    <div class="flex items-center space-x-6">
        <button class="text-gray-600 hover:text-gray-800">
            <i class="bi bi-bell text-xl"></i>
        </button>
        <div class="border-l border-gray-300 pl-6">
            <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
        </div>
    </div>
</header>
