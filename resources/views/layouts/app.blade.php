<!DOCTYPE html>
<html lang="pt">

@include('layouts.partials.head')

<body class="bg-gray-50">
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden mobile-overlay md:hidden"
        onclick="toggleMobileSidebar()"></div>

    <div class="flex h-screen bg-gray-100"
        x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', mobileSidebarOpen: false }"
        x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value));">

        <!-- Sidebar Desktop Wrapper -->
        <div :style="sidebarOpen ? 'width: 280px' : 'width: 0px'"
            class="hidden md:block transition-all duration-300 ease-in-out h-full overflow-hidden flex-shrink-0 relative">
            <div style="width: 280px" class="h-full absolute top-0 left-0">
                @include('layouts.sidebar', ['sidebarId' => 'sidebar-desktop'])
            </div>
        </div>

        <!-- Mobile Sidebar -->
        <div class="md:hidden fixed inset-0 z-50 flex mobile-sidebar" x-show="mobileSidebarOpen" x-cloak>
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="mobileSidebarOpen = false"
                x-transition.opacity></div>
            <div class="relative w-[280px] h-full" x-show="mobileSidebarOpen"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full">
                @include('layouts.sidebar', ['sidebarId' => 'sidebar-mobile'])
            </div>
        </div>

        <!-- Main Content -->
        <div id="mainContent" class="flex-1 flex flex-col overflow-hidden">
            <!-- Header Parcial -->
            @include('layouts.partials.header')

            <!-- Main Yield -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 md:p-8 lg:p-12">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Flash Messages Parcial -->
    @include('layouts.partials.flash-messages')

    @stack('scripts')

</body>
</html>