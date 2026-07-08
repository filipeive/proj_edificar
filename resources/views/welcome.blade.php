<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Life Church - Edificando Vidas, Transformando Destinos</title>
    <meta name="description"
        content="Portal Life Church: gestão de cultos, células, contribuições e relatórios para fortalecer a missão da igreja.">
    <meta name="theme-color" content="#f97316">
    <meta name="application-name" content="Portal Life Church">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Life Church">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="color-scheme" content="light">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.72) 0%, rgba(249, 115, 22, 0.28) 100%),
                url('{{ asset('images/hero-2.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        @media (max-width: 768px) {
            .hero-section {
                background-attachment: scroll;
            }
        }

        .orange-gradient {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        }

        .orange-gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-premium {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            box-shadow: 0 20px 40px -15px rgba(249, 115, 22, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-premium:hover::before {
            left: 100%;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 50px -12px rgba(249, 115, 22, 0.5);
        }

        .card-float {
            transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .card-float:hover {
            transform: translateY(-15px) scale(1.02);
        }

        .feature-card {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #f97316, #fb923c);
            transform: scaleX(0);
            transition: transform 0.5s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            box-shadow: 0 30px 60px -15px rgba(249, 115, 22, 0.2);
            border-color: rgba(249, 115, 22, 0.2);
        }

        .feature-icon {
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.15) rotate(-5deg);
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s ease;
        }

        .stat-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
        }

        .event-card {
            transition: all 0.5s ease;
            position: relative;
        }

        .event-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 3rem;
            padding: 2px;
            background: linear-gradient(135deg, #f97316, #fb923c);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .event-card:hover::after {
            opacity: 1;
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 40px 80px -20px rgba(249, 115, 22, 0.3);
        }

        .service-item {
            transition: all 0.4s ease;
        }

        .service-item:hover {
            background: white !important;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(249, 115, 22, 0.3);
            }

            50% {
                box-shadow: 0 0 40px rgba(249, 115, 22, 0.6);
            }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .parallax-bg {
            transform: translateZ(-1px) scale(2);
        }

        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        @keyframes gradient-shift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .animated-gradient {
            background: linear-gradient(270deg, #f97316, #fb923c, #ea580c);
            background-size: 200% 200%;
            animation: gradient-shift 5s ease infinite;
        }

        .scroll-indicator {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0) translateX(-50%);
            }

            50% {
                transform: translateY(10px) translateX(-50%);
            }
        }

        .text-shadow-glow {
            text-shadow: 0 0 40px rgba(249, 115, 22, 0.5);
        }

        .mobile-scroll-row {
            display: grid;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .mobile-scroll-row {
                display: flex;
                overflow-x: auto;
                scroll-snap-type: x mandatory;
                gap: 1rem;
                padding-bottom: 1rem;
            }

            .mobile-scroll-row>* {
                min-width: 84%;
                scroll-snap-align: center;
            }

            .mobile-scroll-row::-webkit-scrollbar {
                display: none;
            }
        }

        .hero-slide {
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .hero-slide.is-active {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .hero-slide.is-hidden {
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
            position: absolute;
            inset: 0;
        }

        .map-quelimane {
            transform: scale(4.0) translate(0%, 20%);
            transform-origin: center;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>

@php
    $churchName = \App\Models\Setting::get('church.name', 'Life Church');
    $logoPrimary = \App\Models\Setting::get('branding.logo_primary', asset('images/logo-white-orange.png'));
@endphp

<body class="bg-white text-gray-900">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3 group cursor-pointer">
                    <div
                        class="p-2 orange-gradient rounded-2xl shadow-lg transition-transform duration-300 group-hover:scale-110 pulse-glow">
                        <img src="{{ $logoPrimary }}" alt="{{ $churchName }}" class="h-8 w-auto">
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="text-xl font-black tracking-tighter text-gray-900 uppercase leading-none">{{ $churchName }}</span>
                        <span class="text-[9px] text-orange-600 font-black uppercase tracking-[0.3em]">Portal de
                            Gestão</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features"
                        class="text-xs font-bold text-gray-600 hover:text-orange-600 transition-all uppercase tracking-wider relative group">
                        Recursos
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 orange-gradient transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#events"
                        class="text-xs font-bold text-gray-600 hover:text-orange-600 transition-all uppercase tracking-wider relative group">
                        Eventos
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 orange-gradient transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#services"
                        class="text-xs font-bold text-gray-600 hover:text-orange-600 transition-all uppercase tracking-wider relative group">
                        Cultos
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 orange-gradient transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#courses"
                        class="text-xs font-bold text-gray-600 hover:text-orange-600 transition-all uppercase tracking-wider relative group">
                        Cursos
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 orange-gradient transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#online"
                        class="text-xs font-bold text-gray-600 hover:text-orange-600 transition-all uppercase tracking-wider relative group">
                        Online
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 orange-gradient transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="btn-premium text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-300">
                            <i class="bi bi-speedometer2 mr-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="btn-premium text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-300">
                            <i class="bi bi-shield-lock-fill mr-2"></i>Login
                        </a>
                    @endauth
                </div>

                <button
                    class="md:hidden text-gray-900 text-2xl flex-shrink-0 p-2 hover:bg-gray-100 rounded-xl transition-colors"
                    onclick="toggleMobileMenu()">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu fixed top-0 right-0 w-full h-full bg-white z-40 md:hidden shadow-2xl" id="mobileMenu">
        <div class="p-8">
            <button class="absolute top-6 right-6 text-3xl text-gray-900" onclick="toggleMobileMenu()">
                <i class="bi bi-x"></i>
            </button>
            <div class="mt-16 space-y-6">
                <a href="#features" class="block text-lg font-bold text-gray-900 hover:text-orange-600 transition"
                    onclick="toggleMobileMenu()">Recursos</a>
                <a href="#events" class="block text-lg font-bold text-gray-900 hover:text-orange-600 transition"
                    onclick="toggleMobileMenu()">Eventos</a>
                <a href="#services" class="block text-lg font-bold text-gray-900 hover:text-orange-600 transition"
                    onclick="toggleMobileMenu()">Cultos</a>
                <a href="#courses" class="block text-lg font-bold text-gray-900 hover:text-orange-600 transition"
                    onclick="toggleMobileMenu()">Cursos</a>
                <a href="#online" class="block text-lg font-bold text-gray-900 hover:text-orange-600 transition"
                    onclick="toggleMobileMenu()">Online</a>
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="btn-premium text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-wider w-full inline-block text-center">
                        <i class="bi bi-speedometer2 mr-2"></i>Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="btn-premium text-white px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-wider w-full inline-block text-center">
                        <i class="bi bi-shield-lock-fill mr-2"></i>Login
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center hero-section">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/10 to-white/90 z-0"></div>

        <!-- Floating Elements -->
        <div class="absolute top-40 left-20 w-20 h-20 orange-gradient rounded-full opacity-20 blur-xl float-animation"
            style="animation-delay: 0s;"></div>
        <div class="absolute top-60 right-40 w-32 h-32 orange-gradient rounded-full opacity-20 blur-xl float-animation"
            style="animation-delay: 1s;"></div>
        <div class="absolute bottom-40 left-1/3 w-24 h-24 orange-gradient rounded-full opacity-20 blur-xl float-animation"
            style="animation-delay: 2s;"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 py-28 lg:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="text-center lg:text-left">
                    <div
                        class="inline-flex items-center px-6 py-3 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 text-orange-400 text-[10px] font-black uppercase tracking-[0.3em] mb-8 hover:bg-white/20 transition-all duration-300 cursor-default">
                        <span class="flex h-2 w-2 rounded-full bg-orange-500 mr-3">
                            <span
                                class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-orange-400 opacity-75"></span>
                        </span>
                        Edificando Vidas, Transformando Destinos
                    </div>

                    <h1
                        class="text-[clamp(2.75rem,6vw,5.5rem)] font-black text-white mb-6 tracking-tighter leading-[0.95]">
                        Transformando <span class="orange-gradient-text">Comunidades</span>,<br class="hidden sm:block">
                        Mudando <span class="orange-gradient-text">Nações</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-white mb-6 font-black uppercase tracking-widest">
                        Uma Vida de Cada Vez
                    </p>
                    <p class="text-lg text-gray-300 mb-10 leading-relaxed max-w-2xl lg:mx-0 mx-auto font-light">
                        Somos uma igreja familiar baseada em células, amando a Jesus, servindo e discipulando pessoas.
                    </p>

                    <div
                        class="flex flex-col sm:flex-row items-center lg:items-start justify-center lg:justify-start gap-4">
                        <a href="{{ route('register') }}"
                            class="w-full sm:w-auto bg-white text-gray-900 px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 hover:text-white transition-all duration-500 shadow-2xl group text-center">
                            Começar Agora
                            <i
                                class="bi bi-arrow-right ml-2 group-hover:translate-x-2 transition-transform inline-block"></i>
                        </a>
                        <a href="#features"
                            class="w-full sm:w-auto bg-white/5 backdrop-blur-xl text-white border-2 border-white/20 px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-white/10 hover:border-white/40 transition-all duration-500 text-center">
                            Explorar Recursos
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div
                        class="bg-white/10 backdrop-blur-2xl border border-white/20 rounded-[2.5rem] p-6 md:p-8 shadow-2xl">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-orange-400 text-lg">
                                    <i class="bi bi-collection-fill"></i>
                                </div>
                                <div>
                                    <p class="text-white font-black text-sm uppercase tracking-widest">Painel em
                                        Destaque</p>
                                    <p class="text-[10px] text-orange-300 uppercase tracking-[0.2em]">Quelimane • Life
                                        Church</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button"
                                    class="hero-slide-dot w-2.5 h-2.5 rounded-full bg-white/40"></button>
                                <button type="button"
                                    class="hero-slide-dot w-2.5 h-2.5 rounded-full bg-white/20"></button>
                                <button type="button"
                                    class="hero-slide-dot w-2.5 h-2.5 rounded-full bg-white/20"></button>
                                <button type="button"
                                    class="hero-slide-dot w-2.5 h-2.5 rounded-full bg-white/20"></button>
                                <button type="button"
                                    class="hero-slide-dot w-2.5 h-2.5 rounded-full bg-white/20"></button>
                            </div>
                        </div>

                        <div class="hero-slides relative min-h-[20rem]">
                            <div class="hero-slide is-active">
                                <div
                                    class="bg-white rounded-2xl p-4 border border-white/10 text-gray-900 overflow-hidden">
                                    <img src="{{ asset('images/map-mozambique.png') }}"
                                        alt="Mapa Life Church Moçambique"
                                        class="w-full h-44 md:h-48 object-contain map-quelimane">
                                </div>
                                <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-orange-200 uppercase tracking-widest">Células</p>
                                        <p class="text-white font-black text-xl">
                                            {{ number_format($cellCount, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-blue-200 uppercase tracking-widest">Zonas</p>
                                        <p class="text-white font-black text-xl">
                                            {{ number_format($zoneCount, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-emerald-200 uppercase tracking-widest">Membros</p>
                                        <p class="text-white font-black text-xl">
                                            {{ number_format($memberCount ?? 0, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="hero-slide is-hidden">
                                <div class="bg-white rounded-2xl p-5 border border-white/10 text-gray-900">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-sm font-black uppercase tracking-widest text-gray-500">Horários
                                            de Culto</h4>
                                        <span
                                            class="text-[10px] font-black uppercase tracking-widest text-orange-600 bg-orange-50 px-3 py-1 rounded-full">Quelimane</span>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between text-sm font-bold">
                                            <span>Domingo • 08:00</span>
                                            <span class="text-orange-600">Celebração</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm font-bold">
                                            <span>Domingo • 10:00</span>
                                            <span class="text-orange-600">Celebração</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm font-bold">
                                            <span>Domingo • 15:00</span>
                                            <span class="text-yellow-600">Adolescentes</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm font-bold">
                                            <span>Domingo • 17:00</span>
                                            <span class="text-green-600">Jovens</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm font-bold">
                                            <span>Quarta • 17:30</span>
                                            <span class="text-purple-600">Palavra</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 grid grid-cols-2 gap-3 text-center">
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-orange-200 uppercase tracking-widest">Impacto</p>
                                        <p class="text-white font-black text-lg">Semanas cheias</p>
                                    </div>
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-blue-200 uppercase tracking-widest">Equipe</p>
                                        <p class="text-white font-black text-lg">Servindo juntos</p>
                                    </div>
                                </div>
                            </div>

                            <div class="hero-slide is-hidden">
                                <div class="bg-white rounded-2xl p-5 border border-white/10 text-gray-900">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-sm font-black uppercase tracking-widest text-gray-500">Cursos em
                                            Destaque</h4>
                                        <span
                                            class="text-[10px] font-black uppercase tracking-widest text-green-600 bg-green-50 px-3 py-1 rounded-full">Inscrições</span>
                                    </div>
                                    <div class="space-y-3">
                                        @forelse($activeCourses->take(3) as $course)
                                            <div class="flex items-center justify-between text-sm font-bold">
                                                <span class="line-clamp-1">{{ $course->name }}</span>
                                                <span class="text-green-600">Aberto</span>
                                            </div>
                                        @empty
                                            <div class="text-sm text-gray-500">Inscrições em breve.</div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="mt-5 grid grid-cols-2 gap-3 text-center">
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-green-200 uppercase tracking-widest">Formação</p>
                                        <p class="text-white font-black text-lg">Crescimento</p>
                                    </div>
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-blue-200 uppercase tracking-widest">Liderança</p>
                                        <p class="text-white font-black text-lg">Equipar</p>
                                    </div>
                                </div>
                                @if($activeCourses->count() || Route::has('public.forms.pre-marital'))
                                    <a href="{{ route('public.forms.pre-marital') }}"
                                        class="mt-4 inline-flex items-center justify-center w-full bg-green-600 text-white py-3 rounded-2xl text-[10px] uppercase tracking-widest font-black hover:bg-green-700 transition">
                                        Inscrever-se
                                    </a>
                                @else
                                    <a href="#courses"
                                        class="mt-4 inline-flex items-center justify-center w-full bg-white/10 text-white py-3 rounded-2xl text-[10px] uppercase tracking-widest font-black hover:bg-white/20 transition">
                                        Ver Cursos
                                    </a>
                                @endif
                            </div>

                            <div class="hero-slide is-hidden">
                                <div class="bg-white rounded-2xl p-4 border border-white/10 text-gray-900">
                                    <img src="{{ asset('images/map-family.png') }}" alt="Mapa Life Church Family"
                                        class="w-full h-44 md:h-48 object-contain">
                                </div>
                                <div class="mt-5 grid grid-cols-2 gap-3 text-center">
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-orange-200 uppercase tracking-widest">Nacional</p>
                                        <p class="text-white font-black text-lg">Moçambique</p>
                                    </div>
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-blue-200 uppercase tracking-widest">Internacional</p>
                                        <p class="text-white font-black text-lg">Life Church Family</p>
                                    </div>
                                </div>
                            </div>

                            <div class="hero-slide is-hidden">
                                <div class="bg-white rounded-2xl p-5 border border-white/10 text-gray-900">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-sm font-black uppercase tracking-widest text-gray-500">Eventos
                                            Públicos</h4>
                                        <span
                                            class="text-[10px] font-black uppercase tracking-widest text-orange-600 bg-orange-50 px-3 py-1 rounded-full">Aberto</span>
                                    </div>
                                    <div class="space-y-3">
                                        @forelse($publicEvents as $event)
                                            <div class="flex items-center justify-between text-sm font-bold">
                                                <span class="line-clamp-1">{{ $event->name }}</span>
                                                <span class="text-orange-600">{{ $event->eventType->name }}</span>
                                            </div>
                                        @empty
                                            <div class="text-sm text-gray-500">Sem eventos públicos no momento.</div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="mt-5 grid grid-cols-2 gap-3 text-center">
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-orange-200 uppercase tracking-widest">Jejum & Oração
                                        </p>
                                        <p class="text-white font-black text-lg">Unidos</p>
                                    </div>
                                    <div class="bg-white/10 rounded-2xl py-4 border border-white/10">
                                        <p class="text-[9px] text-blue-200 uppercase tracking-widest">Público</p>
                                        <p class="text-white font-black text-lg">Aberto</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="absolute -bottom-8 -right-8 w-24 h-24 orange-gradient rounded-full blur-[60px] opacity-60">
                    </div>
                </div>
            </div>

            <!-- Stats Preview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-20 max-w-4xl mx-auto">
                <div
                    class="stat-card p-6 rounded-2xl text-center backdrop-blur-md bg-white/5 border border-white/10 hover:bg-white/10 transition-colors group">
                    <div class="text-3xl font-black text-white mb-2 group-hover:scale-110 transition-transform"><i
                            class="bi bi-heart-fill text-red-500"></i></div>
                    <div class="text-sm font-bold text-white uppercase tracking-widest">Amar</div>
                    <div class="text-[10px] text-gray-400 font-medium mt-1">Jesus & Pessoas</div>
                </div>
                <div
                    class="stat-card p-6 rounded-2xl text-center backdrop-blur-md bg-white/5 border border-white/10 hover:bg-white/10 transition-colors group">
                    <div class="text-3xl font-black text-white mb-2 group-hover:scale-110 transition-transform"><i
                            class="bi bi-people-fill text-blue-500"></i></div>
                    <div class="text-sm font-bold text-white uppercase tracking-widest">Servir</div>
                    <div class="text-[10px] text-gray-400 font-medium mt-1">E Discipular</div>
                </div>
                <div
                    class="stat-card p-6 rounded-2xl text-center backdrop-blur-md bg-white/5 border border-white/10 hover:bg-white/10 transition-colors group">
                    <div class="text-3xl font-black text-white mb-2 group-hover:scale-110 transition-transform"><i
                            class="bi bi-globe-americas text-green-500"></i></div>
                    <div class="text-sm font-bold text-white uppercase tracking-widest">Transformar</div>
                    <div class="text-[10px] text-gray-400 font-medium mt-1">Comunidades & Nações</div>
                </div>
            </div>
        </div>

        <div class="scroll-indicator absolute bottom-10 left-1/2 text-white/60 cursor-pointer z-10">
            <a href="#features"><i class="bi bi-chevron-down text-3xl animate-bounce"></i></a>
        </div>
    </section>

    <!-- Vision & Values Section -->
    <section id="vision" class="py-32 bg-gray-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div
                class="absolute top-0 right-0 w-full h-full bg-[url('https://images.unsplash.com/photo-1529070538774-1843cb3265df?auto=format&fit=crop&w=2000&q=80')] bg-cover bg-center">
            </div>
            <div class="absolute inset-0 bg-gradient-to-l from-gray-900 via-gray-900/50 to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <!-- Top Vision Block -->
            <div class="flex flex-col lg:flex-row items-center gap-16 mb-32">
                <div class="lg:w-1/2">
                    <span class="text-orange-500 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">A Visão
                        da Life Church</span>
                    <h2 class="text-4xl md:text-5xl font-black text-white mb-8 tracking-tighter leading-none">
                        "Onde não há visão,<br> o povo <span class="orange-gradient-text">perece</span>"
                    </h2>
                    <blockquote
                        class="text-orange-400 text-sm font-bold uppercase tracking-widest mb-8 border-l-4 border-orange-500 pl-4">
                        Provérbios 29:18
                    </blockquote>
                    <p class="text-gray-400 text-lg font-light leading-relaxed mb-6">
                        Na Life Church somos pessoas com visão, temos um propósito, temos um sentido e estamos focados.
                    </p>
                    <div class="bg-white/5 p-6 rounded-2xl border border-white/10 mb-8">
                        <h4 class="text-white font-bold mb-2">Nossa Declaração:</h4>
                        <p class="text-gray-300 italic leading-relaxed">
                            "Somos uma Igreja baseada em grupos células, amando a Jesus, servindo e discipulando
                            pessoas, transformando comunidades e mudando as nações; uma vida de cada vez."
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ asset('documents/visao-igreja.pdf') }}" target="_blank"
                            class="btn-premium text-white px-8 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-transform duration-300 shadow-xl inline-flex items-center justify-center">
                            <i class="bi bi-file-earmark-pdf-fill mr-3 text-lg"></i>
                            Download PDF da Visão
                        </a>
                    </div>
                </div>
                <div class="lg:w-1/2 relative">
                    <div
                        class="relative aspect-video rounded-[2.5rem] overflow-hidden border border-white/10 shadow-2xl group">
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/sHLqLp_7Uv8" loading="lazy"
                            title="Visão Life Church" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    </div>
                    <!-- Decorative Circle -->
                    <div
                        class="absolute -bottom-10 -right-10 w-40 h-40 orange-gradient rounded-full blur-[80px] opacity-30">
                    </div>
                </div>
            </div>

            <!-- Detailed Vision Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-32">
                <div class="bg-white/5 p-8 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                    <div
                        class="w-12 h-12 orange-gradient rounded-xl flex items-center justify-center text-white text-xl mb-6">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-4">Família em Células</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        A igreja não é um edifício, são pessoas vivendo em comunidade. Enfatizamos reuniões juntos no
                        culto de celebração e em nossos grupos de células que funcionam em muitos bairros durante a
                        semana.
                    </p>
                </div>
                <div class="bg-white/5 p-8 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                    <div
                        class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white text-xl mb-6">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-4">Amar e Servir</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Amar a Jesus significa amar o que Ele ama: as pessoas. Servimos e discipulamos uns aos outros,
                        equipando cada membro para alcançar a próxima geração ("Ide e fazei discípulos" - Mateus
                        28:19-20).
                    </p>
                </div>
                <div class="bg-white/5 p-8 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                    <div
                        class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center text-white text-xl mb-6">
                        <i class="bi bi-globe-americas"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-4">Transformar Nações</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Para mudar uma nação, transformamos uma comunidade, uma vida de cada vez. Somos sal e luz
                        (Mateus 5:13-16) impactando cidades e expandindo para nações como Moçambique, Malawi e Zimbábue.
                    </p>
                </div>
            </div>

            <!-- Values Section -->
            <div class="text-center mb-16">
                <span class="text-orange-500 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Nossa
                    Identidade</span>
                <h2 class="text-3xl md:text-5xl font-black text-white mb-12">Valores da <span
                        class="orange-gradient-text">Life Church</span></h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-left">
                    <div
                        class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i
                            class="bi bi-book text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Base Bíblica</h4>
                        <p class="text-xs text-gray-500">Inspirados pela Palavra e ensino forte.</p>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i
                            class="bi bi-person-lines-fill text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Discipulado</h4>
                        <p class="text-xs text-gray-500">Crescer, equipar e liberar liderança.</p>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i
                            class="bi bi-house-heart text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Família</h4>
                        <p class="text-xs text-gray-500">Relacionamentos fortes e herança divina.</p>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i
                            class="bi bi-fire text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Disciplinas</h4>
                        <p class="text-xs text-gray-500">Oração, adoração, jejum e servir.</p>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i
                            class="bi bi-globe text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Multi-nacional</h4>
                        <p class="text-xs text-gray-500">Diversificada e inclusiva, espaço para todos.</p>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i
                            class="bi bi-send text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Missional</h4>
                        <p class="text-xs text-gray-500">Diferença na comunidade e onde formos.</p>
                    </div>
                    <div
                        class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group col-span-2 md:col-span-2">
                        <i
                            class="bi bi-people text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Geração</h4>
                        <p class="text-xs text-gray-500">Todas as idades, os mais velhos construindo a próxima geração.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Journey Section -->
            <div class="bg-white rounded-[3rem] p-8 md:p-16">
                <div class="text-center mb-16">
                    <span
                        class="text-orange-600 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Crescimento</span>
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4 tracking-tighter">Nossa Jornada de
                        Vida</h2>
                    <p class="text-gray-500">4 Passos para o seu propósito</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Step 1 -->
                    <div class="text-center relative">
                        <div
                            class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center text-gray-400 text-2xl font-black mb-6 border-4 border-white shadow-xl relative z-10">
                            1</div>
                        <div class="absolute top-8 left-1/2 w-full h-1 bg-gray-100 hidden md:block -z-0"></div>
                        <h4 class="font-black text-gray-900 mb-2">Pertencer</h4>
                        <span class="text-[10px] text-orange-600 font-bold uppercase tracking-widest block mb-4">Fase
                            Infantil</span>
                        <p class="text-xs text-gray-500 leading-relaxed">Descobrir sua família, Salvação e encontrar sua
                            Célula.</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="text-center relative">
                        <div
                            class="w-16 h-16 mx-auto bg-orange-100 rounded-full flex items-center justify-center text-orange-600 text-2xl font-black mb-6 border-4 border-white shadow-xl relative z-10">
                            2</div>
                        <div class="absolute top-8 left-1/2 w-full h-1 bg-gray-100 hidden md:block -z-0"></div>
                        <h4 class="font-black text-gray-900 mb-2">Conectar</h4>
                        <span
                            class="text-[10px] text-orange-600 font-bold uppercase tracking-widest block mb-4">Relacionamentos</span>
                        <p class="text-xs text-gray-500 leading-relaxed">Discipulado "vivendo juntos" e começar a
                            Servir.</p>
                    </div>
                    <!-- Step 3 -->
                    <div class="text-center relative">
                        <div
                            class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-black mb-6 border-4 border-white shadow-xl relative z-10">
                            3</div>
                        <div class="absolute top-8 left-1/2 w-full h-1 bg-gray-100 hidden md:block -z-0"></div>
                        <h4 class="font-black text-gray-900 mb-2">Equipar</h4>
                        <span class="text-[10px] text-orange-600 font-bold uppercase tracking-widest block mb-4">Jovem
                            Adulto</span>
                        <p class="text-xs text-gray-500 leading-relaxed">Desenvolvimento espiritual, liderança e
                            aplicação prática.</p>
                    </div>
                    <!-- Step 4 -->
                    <div class="text-center relative">
                        <div
                            class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center text-green-600 text-2xl font-black mb-6 border-4 border-white shadow-xl relative z-10">
                            4</div>
                        <h4 class="font-black text-gray-900 mb-2">Liberar</h4>
                        <span class="text-[10px] text-orange-600 font-bold uppercase tracking-widest block mb-4">Fase
                            dos Pais</span>
                        <p class="text-xs text-gray-500 leading-relaxed">Multiplicação, discipular outros e
                            transformação da comunidade.</p>
                    </div>
                </div>
            </div>

            <!-- Appendix / Beliefs Section (Redesigned) -->
            <div class="mt-32">
                <div class="text-center mb-16">
                    <span
                        class="text-orange-600 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Fundamentos</span>
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Nossas <span
                            class="orange-gradient-text">Crenças</span></h2>
                    <p class="text-gray-400 max-w-2xl mx-auto">A base inabalável da nossa fé e prática</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div
                            class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-book-fill"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Doutrina</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">A Bíblia é a palavra inspirada de Deus; existe
                            um só Deus (Pai, Filho, Espírito Santo).</p>
                    </div>

                    <div
                        class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div
                            class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-person-heart"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Jesus Cristo</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Sua divindade, humanidade, morte expiatória,
                            ressurreição e retorno.</p>
                    </div>

                    <div
                        class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div
                            class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-fire"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Espírito Santo</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Poder santificador e batismo para viver a vida
                            cristã.</p>
                    </div>

                    <div
                        class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div
                            class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Salvação</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Todos pecaram; salvação apenas pelo
                            arrependimento e fé no sangue de Cristo.</p>
                    </div>

                    <div
                        class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div
                            class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-droplet-fill"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Práticas</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Batismo nas águas e Ceia do Senhor.</p>
                    </div>

                    <div
                        class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div
                            class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-infinity"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Ressurreição</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Vida eterna para os salvos.</p>
                    </div>

                    <div
                        class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div
                            class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-house-door-fill"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Igreja & Família</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Sacerdócio, milagres atuais, igreja local e
                            casamento como base da família.</p>
                    </div>

                    <div
                        class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div
                            class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-globe"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Missões</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Fazer discípulos de todas as nações (Mateus
                            28:20).</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-32 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-20">
                <span class="text-orange-600 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Nossos
                    Pilares</span>
                <h2 class="text-5xl md:text-7xl font-black text-gray-900 mb-6 tracking-tighter leading-none">
                    Tecnologia a serviço<br>do <span class="orange-gradient-text">Reino</span>
                </h2>
                <p class="text-gray-500 text-xl font-light leading-relaxed max-w-2xl mx-auto">
                    Ferramentas que eliminam a burocracia para você focar no que realmente importa
                </p>
            </div>

            <div class="mobile-scroll-row grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="feature-card card-float p-10 rounded-3xl group cursor-pointer">
                    <div
                        class="w-16 h-16 orange-gradient rounded-2xl flex items-center justify-center text-3xl text-white mb-8 feature-icon shadow-lg">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <h3
                        class="text-2xl font-black text-gray-900 mb-4 tracking-tight group-hover:text-orange-600 transition-colors">
                        Gestão de Cultos
                    </h3>
                    <p class="text-gray-500 leading-relaxed font-light mb-6">
                        Relatórios inteligentes com métricas de presença, ofertas e decisões. PDFs profissionais
                        instantâneos.
                    </p>
                    <div
                        class="flex items-center text-orange-600 text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                        Saiba mais <i class="bi bi-arrow-right ml-2"></i>
                    </div>
                </div>

                <div class="feature-card card-float p-10 rounded-3xl group cursor-pointer">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-3xl text-white mb-8 feature-icon shadow-lg">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <h3
                        class="text-2xl font-black text-gray-900 mb-4 tracking-tight group-hover:text-orange-600 transition-colors">
                        Rede de Células
                    </h3>
                    <p class="text-gray-500 leading-relaxed font-light mb-6">
                        Acompanhe o crescimento através de supervisões e zonas. Monitoramento em tempo real.
                    </p>
                    <div
                        class="flex items-center text-orange-600 text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                        Saiba mais <i class="bi bi-arrow-right ml-2"></i>
                    </div>
                </div>

                <!-- Cursos -->
                <div class="card-premium p-12 rounded-[3rem]">
                    <div
                        class="w-20 h-20 bg-green-50 rounded-3xl flex items-center justify-center text-4xl text-green-600 mb-10 feature-icon">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-6 tracking-tight">Trilho de Ensino</h3>
                    <p class="text-gray-500 leading-relaxed font-light mb-8">Gerencie inscrições e progresso em cursos
                        como Academia de Vida e Escola de Líderes.</p>

                    @if(isset($activeCourses) && $activeCourses->count() > 0)
                        <div class="space-y-4">
                            @foreach($activeCourses as $course)
                                <div class="bg-green-50 p-4 rounded-2xl border border-green-100">
                                    <h4 class="font-bold text-gray-900 text-sm mb-1">{{ $course->name }}</h4>
                                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $course->description }}</p>
                                    <a href="{{ route('public.courses.register', $course->slug) }}"
                                        class="inline-flex items-center text-green-700 font-black uppercase tracking-widest text-[10px] hover:text-green-800 transition group">
                                        Inscrever-se <i
                                            class="bi bi-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <a href="#"
                            class="inline-flex items-center text-gray-400 font-black uppercase tracking-widest text-[10px] cursor-not-allowed">
                            Inscrições em Breve <i class="bi bi-clock ml-2"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section id="events" class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-20">
                <span
                    class="text-orange-600 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Calendário</span>
                <h2 class="text-5xl md:text-7xl font-black text-gray-900 mb-6 tracking-tighter">
                    Próximos <span class="orange-gradient-text">Eventos</span>
                </h2>
                <p class="text-gray-500 text-xl font-light max-w-2xl mx-auto">
                    Momentos especiais que você não pode perder
                </p>
            </div>

            <div class="mobile-scroll-row grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($events as $event)
                    <div class="event-card bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-lg">
                        <div class="p-8">
                            <div class="flex justify-between items-start mb-6">
                                <div class="orange-gradient text-white px-5 py-4 rounded-2xl text-center shadow-lg">
                                    <span class="block text-3xl font-black">{{ $event->date->format('d') }}</span>
                                    <span
                                        class="text-[9px] font-bold uppercase tracking-widest">{{ $event->date->translatedFormat('M') }}</span>
                                </div>
                                <span
                                    class="px-4 py-2 bg-orange-50 rounded-full text-[9px] font-black uppercase tracking-wider text-orange-600">
                                    {{ $event->eventType->name }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mb-4 tracking-tight">
                                {{ $event->name }}
                            </h3>
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center text-gray-500 text-sm">
                                    <i class="bi bi-geo-alt text-orange-600 mr-3"></i>
                                    {{ $event->location ?? 'Life Church' }}
                                </div>
                                <div class="flex items-center text-gray-500 text-sm">
                                    <i class="bi bi-clock text-orange-600 mr-3"></i>
                                    {{ $event->date->format('H:i') }}h
                                </div>
                            </div>
                            <p class="text-gray-400 text-sm leading-relaxed line-clamp-2">
                                {{ $event->description ?? 'Um momento especial de comunhão e adoração.' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-20">
                        <div class="text-gray-300 text-6xl mb-6">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <p class="text-gray-500 font-light">Nenhum evento programado para os próximos dias.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-32 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div>
                    <span
                        class="text-orange-600 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Celebrações</span>
                    <h2 class="text-5xl md:text-7xl font-black text-gray-900 mb-8 tracking-tighter leading-none">
                        Últimos<br><span class="orange-gradient-text">Cultos</span>
                    </h2>
                    <p class="text-gray-500 text-xl font-light leading-relaxed mb-12">
                        Cada culto é uma nova oportunidade de encontro com Deus
                    </p>

                    <div class="space-y-4">
                        <!-- Sunday 8h -->
                        <div
                            class="service-item flex items-center p-6 bg-white rounded-2xl border border-gray-100 group cursor-pointer hover:border-orange-500/30 transition-all">
                            <div
                                class="w-14 h-14 orange-gradient rounded-xl flex items-center justify-center text-xl text-white shadow-md group-hover:scale-110 transition-transform">
                                <i class="bi bi-sun-fill"></i>
                            </div>
                            <div class="ml-5 flex-1">
                                <h4 class="font-black text-gray-900 text-lg">Primeiro Culto</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                                    Domingo • 08:00h
                                </p>
                            </div>
                            <div
                                class="px-4 py-2 bg-orange-50 text-orange-600 rounded-lg text-xs font-bold uppercase tracking-wider">
                                Celebração
                            </div>
                        </div>

                        <!-- Sunday 10h -->
                        <div
                            class="service-item flex items-center p-6 bg-white rounded-2xl border border-gray-100 group cursor-pointer hover:border-orange-500/30 transition-all">
                            <div
                                class="w-14 h-14 orange-gradient rounded-xl flex items-center justify-center text-xl text-white shadow-md group-hover:scale-110 transition-transform">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="ml-5 flex-1">
                                <h4 class="font-black text-gray-900 text-lg">Segundo Culto</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                                    Domingo • 10:00h
                                </p>
                            </div>
                            <div
                                class="px-4 py-2 bg-orange-50 text-orange-600 rounded-lg text-xs font-bold uppercase tracking-wider">
                                Celebração
                            </div>
                        </div>

                        <!-- Sunday 15h (Teens) -->
                        <div
                            class="service-item flex items-center p-6 bg-white rounded-2xl border border-gray-100 group cursor-pointer hover:border-yellow-500/30 transition-all">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center text-xl text-white shadow-md group-hover:scale-110 transition-transform">
                                <i class="bi bi-emoji-smile-fill"></i>
                            </div>
                            <div class="ml-5 flex-1">
                                <h4 class="font-black text-gray-900 text-lg">Culto de Adolescentes</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                                    Domingo • 15:00h
                                </p>
                            </div>
                            <div
                                class="px-4 py-2 bg-yellow-50 text-yellow-600 rounded-lg text-xs font-bold uppercase tracking-wider">
                                Teens
                            </div>
                        </div>

                        <!-- Sunday 17h (Youth & Adults) -->
                        <div
                            class="service-item flex items-center p-6 bg-white rounded-2xl border border-gray-100 group cursor-pointer hover:border-blue-500/30 transition-all">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-xl text-white shadow-md group-hover:scale-110 transition-transform">
                                <i class="bi bi-fire"></i>
                            </div>
                            <div class="ml-5 flex-1">
                                <h4 class="font-black text-gray-900 text-lg">Jovens e Adultos</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                                    Domingo • 17:00h
                                </p>
                            </div>
                            <div
                                class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold uppercase tracking-wider">
                                Revival
                            </div>
                        </div>

                        <!-- Wednesday 17:30 (Word) -->
                        <div
                            class="service-item flex items-center p-6 bg-white rounded-2xl border border-gray-100 group cursor-pointer hover:border-purple-500/30 transition-all">
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center text-xl text-white shadow-md group-hover:scale-110 transition-transform">
                                <i class="bi bi-book-half"></i>
                            </div>
                            <div class="ml-5 flex-1">
                                <h4 class="font-black text-gray-900 text-lg">Culto da Palavra</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                                    Quarta-feira • 17:30h
                                </p>
                            </div>
                            <div
                                class="px-4 py-2 bg-purple-50 text-purple-600 rounded-lg text-xs font-bold uppercase tracking-wider">
                                Ensino
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="aspect-square rounded-[3rem] overflow-hidden shadow-2xl">
                        <img src="{{ asset('images/hero-1.jpg') }}" alt="Culto Life Church"
                            class="w-full h-full object-cover hover:scale-110 transition-transform duration-700">
                    </div>
                    <div
                        class="absolute -bottom-10 -left-10 orange-gradient p-10 rounded-[2.5rem] shadow-2xl text-white max-w-sm hover:scale-105 transition-transform duration-300">
                        <i class="bi bi-quote text-4xl mb-4 block opacity-80"></i>
                        <p class="text-lg font-medium leading-relaxed italic">
                            "Onde dois ou três estiverem reunidos em meu nome, ali eu estou."
                        </p>
                        <span class="text-[10px] font-black uppercase tracking-widest mt-6 block opacity-60">
                            — Mateus 18:20
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- YouTube Section -->
    <section id="online" class="py-32 bg-black relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-0 w-full h-full bg-center bg-no-repeat bg-contain"
                style="background-image: url('{{ asset('images/map-family.png') }}');">
            </div>
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <span class="text-orange-500 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Assista
                    Online</span>
                <h2 class="text-5xl md:text-7xl font-black text-white mb-6 tracking-tighter">
                    Nossos <span class="orange-gradient-text">Cultos</span>
                </h2>
                <p class="text-gray-400 text-xl font-light max-w-2xl mx-auto">
                    Acompanhe nossas transmissões e seja edificado onde estiver
                </p>
            </div>

            <div class="max-w-5xl mx-auto">
                <div class="relative aspect-video rounded-3xl overflow-hidden shadow-2xl border border-white/10 group">
                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/-X1wWrLCnBs?si=Edificar2025"
                        title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>

                    <!-- Decorative elements -->
                    <div
                        class="absolute -top-10 -right-10 w-40 h-40 bg-orange-600 rounded-full blur-[100px] opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                    </div>
                    <div
                        class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-600 rounded-full blur-[100px] opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                    </div>
                </div>

                <div class="mt-12 text-center">
                    <a href="https://www.youtube.com/@lifechurchmozambique8792" target="_blank"
                        class="inline-flex items-center space-x-3 bg-white/10 hover:bg-white/20 text-white px-8 py-4 rounded-2xl transition-all duration-300 backdrop-blur-sm border border-white/10 group">
                        <i class="bi bi-youtube text-2xl text-red-600 group-hover:scale-110 transition-transform"></i>
                        <span class="font-bold uppercase tracking-widest text-xs">Visitar Canal no YouTube</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section id="courses" class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-20">
                <span class="text-orange-600 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Formação &
                    Crescimento</span>
                <h2 class="text-4xl md:text-6xl font-black text-gray-900 mb-6 tracking-tighter">
                    Conheça Nossos <span class="orange-gradient-text">Cursos</span>
                </h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Invista no seu crescimento espiritual e ministerial através
                    de nossas escolas de formação.</p>
            </div>

            <div class="mobile-scroll-row grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($activeCourses as $course)
                    <div
                        class="bg-gray-50 p-8 rounded-[3rem] border border-gray-100 group hover:bg-white hover:shadow-2xl transition-all duration-500">
                        <div
                            class="w-16 h-16 orange-gradient rounded-2xl flex items-center justify-center text-white text-3xl mb-8 group-hover:scale-110 transition-transform">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 mb-4">{{ $course->name }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-8 line-clamp-3">
                            {{ $course->description }}
                        </p>
                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</span>
                                <span class="text-xs font-bold text-green-600 uppercase">Inscrições Abertas</span>
                            </div>
                            <a href="{{ route('public.courses.register', $course->slug) }}"
                                class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                                Inscrever-se
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-3 text-center py-12">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="bi bi-calendar-x text-3xl text-gray-300"></i>
                        </div>
                        <p class="text-gray-400 italic">No momento não há cursos com inscrições abertas via portal.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-32 bg-gray-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-1/2 h-full orange-gradient skew-x-12 translate-x-1/4"></div>
            <div class="absolute bottom-0 left-0 w-1/2 h-full bg-blue-600 -skew-x-12 -translate-x-1/4"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <h2 class="text-5xl md:text-8xl font-black text-white mb-8 tracking-tighter leading-none">
                    Pronto para<br>
                    <span class="orange-gradient-text">Edificar</span>?
                </h2>
                <p class="text-gray-400 text-xl md:text-2xl mb-16 font-light leading-relaxed">
                    Junte-se à liderança da Life Church e tenha em mãos a ferramenta definitiva para gestão eclesiástica
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                    <div class="stat-card p-8 rounded-2xl hover:scale-105 transition-transform duration-300">
                        <div class="text-4xl font-black text-orange-500 mb-2">100%</div>
                        <div class="text-[10px] text-gray-500 uppercase font-black tracking-[0.3em]">Nuvem & Seguro
                        </div>
                    </div>
                    <div class="stat-card p-8 rounded-2xl hover:scale-105 transition-transform duration-300">
                        <div class="text-4xl font-black text-orange-500 mb-2">+{{ $memberCount ?? '2.5k' }}</div>
                        <div class="text-[10px] text-gray-500 uppercase font-black tracking-[0.3em]">Membros Ativos
                        </div>
                    </div>
                    <div class="stat-card p-8 rounded-2xl hover:scale-105 transition-transform duration-300">
                        <div class="text-4xl font-black text-orange-500 mb-2">24/7</div>
                        <div class="text-[10px] text-gray-500 uppercase font-black tracking-[0.3em]">Acesso Global</div>
                    </div>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}"
                        class="btn-premium text-white px-16 py-6 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all duration-500 shadow-2xl group inline-block">
                        Aceder ao Dashboard
                        <i class="bi bi-arrow-right ml-3 group-hover:translate-x-2 transition-transform inline-block"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="btn-premium text-white px-16 py-6 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all duration-500 shadow-2xl group inline-block">
                        Aceder ao Portal
                        <i class="bi bi-arrow-right ml-3 group-hover:translate-x-2 transition-transform inline-block"></i>
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-20">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-16 mb-16">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 group cursor-pointer">
                        <div
                            class="p-2 orange-gradient rounded-2xl shadow-lg transition-transform duration-300 group-hover:scale-110 pulse-glow">
                            <img src="{{ asset('images/logo-white-orange.png') }}" alt="Life Church" class="h-8 w-auto">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-black tracking-tighter text-gray-900 uppercase leading-none">Life
                                Church</span>
                            <span class="text-[9px] text-orange-600 font-black uppercase tracking-[0.3em]">Portal de
                                Gestão</span>
                        </div>
                    </div>
                    <p class="text-gray-500 text-base font-light leading-relaxed max-w-md">
                        Uma comunidade dedicada a amar a Deus, amar as pessoas e servir o mundo com excelência e paixão.
                    </p>
                </div>

                <div>
                    <h5 class="text-xs font-black text-gray-900 uppercase tracking-[0.3em] mb-6">Navegação</h5>
                    <ul class="space-y-3">
                        <li>
                            <a href="#features"
                                class="text-gray-500 hover:text-orange-600 transition text-sm font-medium flex items-center group">
                                <i
                                    class="bi bi-arrow-right mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                Recursos
                            </a>
                        </li>
                        <li>
                            <a href="#events"
                                class="text-gray-500 hover:text-orange-600 transition text-sm font-medium flex items-center group">
                                <i
                                    class="bi bi-arrow-right mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                Eventos
                            </a>
                        </li>
                        <li>
                            <a href="#services"
                                class="text-gray-500 hover:text-orange-600 transition text-sm font-medium flex items-center group">
                                <i
                                    class="bi bi-arrow-right mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                Cultos
                            </a>
                        </li>
                        <li>
                            <a href="#online"
                                class="text-gray-500 hover:text-orange-600 transition text-sm font-medium flex items-center group">
                                <i
                                    class="bi bi-arrow-right mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                Online
                            </a>
                        </li>
                        <li>
                            @auth
                                <a href="{{ route('dashboard') }}"
                                    class="text-gray-500 hover:text-orange-600 transition text-sm font-medium flex items-center group">
                                    <i
                                        class="bi bi-arrow-right mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="text-gray-500 hover:text-orange-600 transition text-sm font-medium flex items-center group">
                                    <i
                                        class="bi bi-arrow-right mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    Login
                                </a>
                            @endauth
                        </li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-xs font-black text-gray-900 uppercase tracking-[0.3em] mb-6">Social</h5>
                    <div class="flex space-x-3">
                        <a href="https://www.facebook.com/profile.php?id=61561915645001" target="_blank"
                            class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-orange-600 hover:text-white transition-all duration-300 shadow-sm hover:scale-110">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/lifechurchmoz_" target="_blank"
                            class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-orange-600 hover:text-white transition-all duration-300 shadow-sm hover:scale-110">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://www.youtube.com/@lifechurchmozambique8792" target="_blank"
                            class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-orange-600 hover:text-white transition-all duration-300 shadow-sm hover:scale-110">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div
                class="pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6 text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em]">
                <div>&copy;
                    <script>document.write(new Date().getFullYear());</script> - Portal Life Church. Todos os direitos
                    reservados.
                </div>
                <div class="flex space-x-8">
                    <a href="#" class="hover:text-orange-600 transition">Privacidade</a>
                    <a href="#" class="hover:text-orange-600 transition">Termos</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-lg');
            } else {
                navbar.classList.remove('shadow-lg');
            }
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.feature-card, .event-card, .service-item').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });

        // Scroll to Top Button
        window.addEventListener('load', () => {
            const scrollToTopBtn = document.getElementById('scrollToTop');
            if (!scrollToTopBtn) return;

            window.addEventListener('scroll', () => {
                if (window.scrollY > 500) {
                    scrollToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                    scrollToTopBtn.classList.add('opacity-100');
                } else {
                    scrollToTopBtn.classList.add('opacity-0', 'pointer-events-none');
                    scrollToTopBtn.classList.remove('opacity-100');
                }
            });

            scrollToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });

        window.addEventListener('load', () => {
            const heroSlides = Array.from(document.querySelectorAll('.hero-slide'));
            const heroDots = Array.from(document.querySelectorAll('.hero-slide-dot'));
            if (!heroSlides.length) return;

            let heroIndex = 0;
            const setHeroSlide = (index) => {
                heroSlides.forEach((slide, i) => {
                    const isActive = i === index;
                    slide.classList.toggle('is-active', isActive);
                    slide.classList.toggle('is-hidden', !isActive);
                });
                heroDots.forEach((dot, i) => {
                    dot.classList.toggle('bg-white/40', i === index);
                    dot.classList.toggle('bg-white/20', i !== index);
                });
            };

            setHeroSlide(0);
            heroDots.forEach((dot, i) => {
                dot.addEventListener('click', () => {
                    heroIndex = i;
                    setHeroSlide(heroIndex);
                });
            });

            setInterval(() => {
                heroIndex = (heroIndex + 1) % heroSlides.length;
                setHeroSlide(heroIndex);
            }, 6500);
        });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => { });
        }
    </script>

    @php
        $whatsappRaw = \App\Models\Setting::get('church.phone', '');
        $whatsappNumber = preg_replace('/\D+/', '', $whatsappRaw);
    @endphp

    @if($whatsappNumber)
        <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener"
            class="fixed bottom-8 left-8 z-50 w-14 h-14 bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-full shadow-2xl hover:shadow-green-500/50 transition-all duration-300 hover:scale-110 group flex items-center justify-center"
            aria-label="Falar no WhatsApp">
            <i class="bi bi-whatsapp text-2xl group-hover:scale-110 transition-transform"></i>
        </a>
    @endif

    <!-- Scroll to Top Button -->
    <button id="scrollToTop"
        class="fixed bottom-8 right-8 z-50 w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-full shadow-2xl hover:shadow-orange-500/50 transition-all duration-300 opacity-0 pointer-events-none hover:scale-110 group"
        aria-label="Voltar ao topo">
        <i class="bi bi-arrow-up text-xl group-hover:animate-bounce"></i>
    </button>
</body>

</html>