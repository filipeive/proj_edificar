<!DOCTYPE html>
<html lang="pt">

@include('layouts.partials.head')

<body class="bg-gray-50">
    <!-- Animated App Splash Screen (Mobile & Web App Opening) -->
    <div id="app-splash-screen" class="fixed inset-0 z-[9999] bg-slate-950 flex flex-col items-center justify-center transition-opacity duration-500 ease-out">
        <div class="relative flex flex-col items-center text-center px-4">
            <div class="w-24 h-24 mb-4 rounded-3xl bg-white/10 p-3.5 backdrop-blur-2xl border border-white/15 shadow-2xl flex items-center justify-center animate-bounce">
                <img src="{{ asset(config('branding.logo_primary', 'images/logo.png')) }}" alt="Life App Logo" class="w-16 h-16 object-contain drop-shadow-lg" onError="this.src='/images/logo.png'">
            </div>
            <h1 class="text-white text-2xl font-extrabold tracking-tight">Portal Life Church</h1>
            <p class="text-orange-400 text-xs font-semibold tracking-widest uppercase mt-1">Life App</p>
            <div class="mt-6 flex items-center gap-1.5">
                <div class="w-2 h-2 rounded-full bg-orange-500 animate-ping"></div>
                <span class="text-slate-400 text-xs font-medium">A carregar aplicação...</span>
            </div>
        </div>
    </div>
    <script>
        (function() {
            function hideSplash() {
                const splash = document.getElementById('app-splash-screen');
                if (!splash) return;
                splash.style.opacity = '0';
                splash.style.pointerEvents = 'none';
                setTimeout(() => { if (splash.parentNode) splash.parentNode.removeChild(splash); }, 500);
            }
            if (document.readyState === 'complete') {
                setTimeout(hideSplash, 300);
            } else {
                window.addEventListener('load', () => setTimeout(hideSplash, 300));
                setTimeout(hideSplash, 2000); // Fallback maximum wait 2s
            }
        })();
    </script>

    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden mobile-overlay md:hidden"
        onclick="toggleMobileSidebar()"></div>

    <div class="flex h-screen bg-gray-100"
        x-data="{ sidebarOpen: (function() { try { return localStorage.getItem('sidebarOpen') !== 'false'; } catch(e) { return true; } })(), mobileSidebarOpen: false }"
        x-init="
            $watch('sidebarOpen', value => {
                try { localStorage.setItem('sidebarOpen', value); } catch(e) {}
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
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 pb-28 md:p-8 md:pb-8 lg:p-12">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    @include('layouts.partials.bottom-nav')

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
                if (window.innerWidth < 768) return;

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