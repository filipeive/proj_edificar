<!DOCTYPE html>
<html lang="pt">

@include('layouts.partials.head')

<body class="bg-gray-50">
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden mobile-overlay md:hidden"
        onclick="toggleMobileSidebar()"></div>

    <div class="flex h-screen bg-gray-100"
        x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', mobileSidebarOpen: false }"
        x-init="
            $watch('sidebarOpen', value => {
                localStorage.setItem('sidebarOpen', value);
                setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
            });
            $watch('mobileSidebarOpen', value => {
                const overlay = document.getElementById('mobileOverlay');
                if (overlay) {
                    if (value) overlay.classList.remove('hidden');
                    else overlay.classList.add('hidden');
                }
            });
            window.toggleSidebar = () => { sidebarOpen = !sidebarOpen; };
            window.toggleMobileSidebar = () => { mobileSidebarOpen = !mobileSidebarOpen; };
        "
        @keydown.window.ctrl.b.prevent="if (window.innerWidth >= 768) { sidebarOpen = !sidebarOpen; } else { mobileSidebarOpen = !mobileSidebarOpen; }">

        <!-- Sidebar Desktop Wrapper -->
        <div :style="sidebarOpen ? 'width: 280px' : 'width: 80px'"
            class="hidden md:block transition-all duration-300 ease-in-out h-full overflow-hidden flex-shrink-0 relative">
            <div class="h-full w-full absolute top-0 left-0">
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

    <!-- Global Sidebar Tooltip (for collapsed state) -->
    <div id="sidebar-global-tooltip"
         class="fixed bg-slate-900 text-white px-3 py-1.5 rounded-lg text-xs font-bold pointer-events-none opacity-0 transition-opacity duration-150 z-[9999] shadow-xl border border-white/5 whitespace-nowrap"
         style="left: 90px; transform: translateY(-50%);">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tooltip = document.getElementById('sidebar-global-tooltip');
            if (!tooltip) return;

            document.addEventListener('mouseover', (e) => {
                const sidebar = document.querySelector('.sidebar-collapsed');
                if (!sidebar) return;

                const navItem = e.target.closest('.nav-item');
                if (!navItem) return;

                const text = navItem.getAttribute('data-tooltip');
                if (!text) return;

                const rect = navItem.getBoundingClientRect();
                tooltip.textContent = text;
                tooltip.style.top = `${rect.top + rect.height / 2}px`;
                tooltip.style.left = `${rect.right + 10}px`;
                tooltip.style.opacity = '1';
            });

            document.addEventListener('mouseout', (e) => {
                const navItem = e.target.closest('.nav-item');
                if (!navItem) return;

                tooltip.style.opacity = '0';
            });

            document.addEventListener('scroll', () => {
                tooltip.style.opacity = '0';
            }, true);
        });
    </script>

    @stack('scripts')

</body>
</html>