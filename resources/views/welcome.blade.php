<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Life Church - Edificando Vidas, Transformando Destinos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
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
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.7) 0%, rgba(249, 115, 22, 0.3) 100%),
                url('https://images.unsplash.com/photo-1438232992991-995b7058bbb3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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
    </style>
</head>

<body class="bg-white text-gray-900">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
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

                <button class="md:hidden text-gray-900 text-2xl" onclick="toggleMobileMenu()">
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

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 py-32">
            <div class="text-center max-w-5xl mx-auto">
                <div
                    class="inline-flex items-center px-6 py-3 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 text-orange-400 text-[10px] font-black uppercase tracking-[0.3em] mb-8 hover:bg-white/20 transition-all duration-300 cursor-default">
                    <span class="flex h-2 w-2 rounded-full bg-orange-500 mr-3">
                        <span
                            class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-orange-400 opacity-75"></span>
                    </span>
                    Edificando Vidas, Transformando Destinos
                </div>

                <h1
                    class="text-6xl md:text-8xl lg:text-9xl font-black text-white mb-8 leading-[0.9] tracking-tighter text-shadow-glow">
                    Onde a Fé Encontra a<br>
                    <span
                        class="orange-gradient-text inline-block hover:scale-105 transition-transform duration-300">Excelência
                        Digital</span>
                </h1>

                <p class="text-xl md:text-2xl text-gray-200 mb-12 leading-relaxed max-w-3xl mx-auto font-light">
                    Uma plataforma de gestão eclesiástica de classe mundial, desenhada para potencializar o crescimento
                    da sua igreja e o cuidado com cada membro.
                </p>
                
                <!-- Floating Decorative Elements -->
                <div class="absolute top-1/2 left-10 w-24 h-24 border border-white/10 rounded-full animate-spin-slow hidden md:block"></div>
                <div class="absolute bottom-20 right-10 w-32 h-32 border border-white/5 rounded-full animate-bounce-slow hidden md:block"></div>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    <a href="{{ route('register') }}"
                        class="w-full sm:w-auto bg-white text-gray-900 px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 hover:text-white transition-all duration-500 shadow-2xl group text-center">
                        Começar Agora
                        <i
                            class="bi bi-arrow-right ml-2 group-hover:translate-x-2 transition-transform inline-block"></i>
                    </a>
                    <a href="#features"
                        class="w-full sm:w-auto bg-white/5 backdrop-blur-xl text-white border-2 border-white/20 px-12 py-5 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-white/10 hover:border-white/40 transition-all duration-500 text-center">
                        Explorar Recursos
                    </a>
                </div>

                <!-- Stats Preview -->
                <div class="grid grid-cols-3 gap-6 mt-20 max-w-3xl mx-auto">
                    <div class="stat-card p-6 rounded-2xl text-center">
                        <div class="text-3xl font-black text-white mb-1">100%</div>
                        <div class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Seguro</div>
                    </div>
                    <div class="stat-card p-6 rounded-2xl text-center">
                        <div class="text-3xl font-black text-white mb-1">+{{ $memberCount ?? '2.5k' }}</div>
                        <div class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Membros</div>
                    </div>
                    <div class="stat-card p-6 rounded-2xl text-center">
                        <div class="text-3xl font-black text-white mb-1">24/7</div>
                        <div class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Online</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="scroll-indicator absolute bottom-10 left-1/2 text-white/60 cursor-pointer z-10">
            <a href="#features"><i class="bi bi-chevron-down text-3xl animate-bounce"></i></a>
        </div>
    </section>

    </section>

    <!-- Vision & Values Section -->
    <section id="vision" class="py-32 bg-gray-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 w-full h-full bg-[url('https://images.unsplash.com/photo-1548625361-8889aa3fb942?ixlib=rb-4.0.3&fit=crop&w=2000&q=80')] bg-cover bg-center"></div>
            <div class="absolute inset-0 bg-gradient-to-l from-gray-900 via-gray-900/50 to-transparent"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <!-- Top Vision Block -->
            <div class="flex flex-col lg:flex-row items-center gap-16 mb-32">
                <div class="lg:w-1/2">
                    <span class="text-orange-500 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">A Visão da Life Church</span>
                    <h2 class="text-4xl md:text-5xl font-black text-white mb-8 tracking-tighter leading-none">
                        "Onde não há visão,<br> o povo <span class="orange-gradient-text">perece</span>"
                    </h2>
                    <blockquote class="text-orange-400 text-sm font-bold uppercase tracking-widest mb-8 border-l-4 border-orange-500 pl-4">
                        Provérbios 29:18
                    </blockquote>
                    <p class="text-gray-400 text-lg font-light leading-relaxed mb-6">
                        Na Life Church somos pessoas com visão, temos um propósito, temos um sentido e estamos focados.
                    </p>
                    <div class="bg-white/5 p-6 rounded-2xl border border-white/10 mb-8">
                        <h4 class="text-white font-bold mb-2">Nossa Declaração:</h4>
                        <p class="text-gray-300 italic leading-relaxed">
                            "Somos uma Igreja baseada em grupos células, amando a Jesus, servindo e discipulando pessoas, transformando comunidades e mudando as nações; uma vida de cada vez."
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
                    <div class="relative aspect-video rounded-[2.5rem] overflow-hidden border border-white/10 shadow-2xl group">
                         <iframe class="w-full h-full" 
                                src="https://www.youtube.com/embed/sHLqLp_7Uv8"
                                title="Visão Life Church" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen>
                        </iframe>
                    </div>
                    <!-- Decorative Circle -->
                     <div class="absolute -bottom-10 -right-10 w-40 h-40 orange-gradient rounded-full blur-[80px] opacity-30"></div>
                </div>
            </div>

            <!-- Detailed Vision Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-32">
                <div class="bg-white/5 p-8 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="w-12 h-12 orange-gradient rounded-xl flex items-center justify-center text-white text-xl mb-6">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-4">Família em Células</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        A igreja não é um edifício, são pessoas vivendo em comunidade. Enfatizamos reuniões juntos no culto de celebração e em nossos grupos de células que funcionam em muitos bairros durante a semana.
                    </p>
                </div>
                <div class="bg-white/5 p-8 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white text-xl mb-6">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-4">Amar e Servir</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Amar a Jesus significa amar o que Ele ama: as pessoas. Servimos e discipulamos uns aos outros, equipando cada membro para alcançar a próxima geração ("Ide e fazei discípulos" - Mateus 28:19-20).
                    </p>
                </div>
                <div class="bg-white/5 p-8 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center text-white text-xl mb-6">
                        <i class="bi bi-globe-americas"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-4">Transformar Nações</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Para mudar uma nação, transformamos uma comunidade, uma vida de cada vez. Somos sal e luz (Mateus 5:13-16) impactando cidades e expandindo para nações como Moçambique, Malawi e Zimbábue.
                    </p>
                </div>
            </div>

            <!-- Values Section -->
            <div class="text-center mb-16">
                 <span class="text-orange-500 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Nossa Identidade</span>
                 <h2 class="text-3xl md:text-5xl font-black text-white mb-12">Valores da <span class="orange-gradient-text">Life Church</span></h2>
                 
                 <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-left">
                     <div class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                         <i class="bi bi-book text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                         <h4 class="text-white font-bold text-sm mb-1">Base Bíblica</h4>
                         <p class="text-xs text-gray-500">Inspirados pela Palavra e ensino forte.</p>
                     </div>
                     <div class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i class="bi bi-person-lines-fill text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Discipulado</h4>
                        <p class="text-xs text-gray-500">Crescer, equipar e liberar liderança.</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i class="bi bi-house-heart text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Família</h4>
                        <p class="text-xs text-gray-500">Relacionamentos fortes e herança divina.</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i class="bi bi-fire text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Disciplinas</h4>
                        <p class="text-xs text-gray-500">Oração, adoração, jejum e servir.</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i class="bi bi-globe text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Multi-nacional</h4>
                        <p class="text-xs text-gray-500">Diversificada e inclusiva, espaço para todos.</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group">
                        <i class="bi bi-send text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Missional</h4>
                        <p class="text-xs text-gray-500">Diferença na comunidade e onde formos.</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 hover:bg-orange-600/20 border border-white/5 transition-all group col-span-2 md:col-span-2">
                        <i class="bi bi-people text-2xl text-orange-500 mb-3 block group-hover:scale-110 transition-transform"></i>
                        <h4 class="text-white font-bold text-sm mb-1">Geração</h4>
                        <p class="text-xs text-gray-500">Todas as idades, os mais velhos construindo a próxima geração.</p>
                    </div>
                 </div>
            </div>

            <!-- Journey Section -->
            <div class="bg-white rounded-[3rem] p-8 md:p-16">
                <div class="text-center mb-16">
                    <span class="text-orange-600 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Crescimento</span>
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-4 tracking-tighter">Nossa Jornada de Vida</h2>
                    <p class="text-gray-500">4 Passos para o seu propósito</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Step 1 -->
                    <div class="text-center relative">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center text-gray-400 text-2xl font-black mb-6 border-4 border-white shadow-xl relative z-10">1</div>
                        <div class="absolute top-8 left-1/2 w-full h-1 bg-gray-100 hidden md:block -z-0"></div>
                        <h4 class="font-black text-gray-900 mb-2">Pertencer</h4>
                        <span class="text-[10px] text-orange-600 font-bold uppercase tracking-widest block mb-4">Fase Infantil</span>
                        <p class="text-xs text-gray-500 leading-relaxed">Descobrir sua família, Salvação e encontrar sua Célula.</p>
                    </div>
                    <!-- Step 2 -->
                    <div class="text-center relative">
                        <div class="w-16 h-16 mx-auto bg-orange-100 rounded-full flex items-center justify-center text-orange-600 text-2xl font-black mb-6 border-4 border-white shadow-xl relative z-10">2</div>
                        <div class="absolute top-8 left-1/2 w-full h-1 bg-gray-100 hidden md:block -z-0"></div>
                        <h4 class="font-black text-gray-900 mb-2">Conectar</h4>
                        <span class="text-[10px] text-orange-600 font-bold uppercase tracking-widest block mb-4">Relacionamentos</span>
                        <p class="text-xs text-gray-500 leading-relaxed">Discipulado "vivendo juntos" e começar a Servir.</p>
                    </div>
                     <!-- Step 3 -->
                     <div class="text-center relative">
                        <div class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-black mb-6 border-4 border-white shadow-xl relative z-10">3</div>
                        <div class="absolute top-8 left-1/2 w-full h-1 bg-gray-100 hidden md:block -z-0"></div>
                        <h4 class="font-black text-gray-900 mb-2">Equipar</h4>
                        <span class="text-[10px] text-orange-600 font-bold uppercase tracking-widest block mb-4">Jovem Adulto</span>
                        <p class="text-xs text-gray-500 leading-relaxed">Desenvolvimento espiritual, liderança e aplicação prática.</p>
                    </div>
                    <!-- Step 4 -->
                    <div class="text-center relative">
                        <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center text-green-600 text-2xl font-black mb-6 border-4 border-white shadow-xl relative z-10">4</div>
                        <h4 class="font-black text-gray-900 mb-2">Liberar</h4>
                        <span class="text-[10px] text-orange-600 font-bold uppercase tracking-widest block mb-4">Fase dos Pais</span>
                        <p class="text-xs text-gray-500 leading-relaxed">Multiplicação, discipular outros e transformação da comunidade.</p>
                    </div>
                </div>
            </div>

            <!-- Appendix / Beliefs Section (Redesigned) -->
            <div class="mt-32">
                <div class="text-center mb-16">
                     <span class="text-orange-600 font-black uppercase tracking-[0.4em] text-[10px] mb-4 block">Fundamentos</span>
                     <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Nossas <span class="orange-gradient-text">Crenças</span></h2>
                     <p class="text-gray-400 max-w-2xl mx-auto">A base inabalável da nossa fé e prática</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-book-fill"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Doutrina</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">A Bíblia é a palavra inspirada de Deus; existe um só Deus (Pai, Filho, Espírito Santo).</p>
                    </div>

                    <div class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-person-heart"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Jesus Cristo</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Sua divindade, humanidade, morte expiatória, ressurreição e retorno.</p>
                    </div>

                    <div class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-fire"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Espírito Santo</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Poder santificador e batismo para viver a vida cristã.</p>
                    </div>

                    <div class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Salvação</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Todos pecaram; salvação apenas pelo arrependimento e fé no sangue de Cristo.</p>
                    </div>

                    <div class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-droplet-fill"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Práticas</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Batismo nas águas e Ceia do Senhor.</p>
                    </div>

                    <div class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                         <div class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-infinity"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Ressurreição</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Vida eterna para os salvos.</p>
                    </div>

                    <div class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                         <div class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-house-door-fill"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Igreja & Família</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Sacerdócio, milagres atuais, igreja local e casamento como base da família.</p>
                    </div>

                    <div class="bg-black/40 backdrop-blur-md p-8 rounded-3xl border border-white/5 hover:border-orange-500/30 transition-all hover:-translate-y-2 group">
                        <div class="w-10 h-10 orange-gradient rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform">
                            <i class="bi bi-globe"></i>
                        </div>
                        <h4 class="text-white font-bold mb-2">Missões</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">Fazer discípulos de todas as nações (Mateus 28:20).</p>
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
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
                        @forelse($recentServices as $service)
                            <div
                                class="service-item flex items-center p-6 bg-white rounded-2xl border border-gray-100 group cursor-pointer">
                                <div
                                    class="w-14 h-14 orange-gradient rounded-xl flex items-center justify-center text-xl text-white shadow-md group-hover:scale-110 transition-transform">
                                    <i class="bi bi-play-circle-fill"></i>
                                </div>
                                <div class="ml-5 flex-1">
                                    <h4 class="font-black text-gray-900 text-lg">
                                        {{ $service->name ?? 'Culto de Celebração' }}
                                    </h4>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                                        {{ $service->date->format('d/m/Y') }} • {{ $service->date->translatedFormat('l') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="text-2xl font-black text-gray-900">{{ $service->attendance_count ?? 0 }}</span>
                                    <p class="text-[8px] text-gray-400 font-black uppercase tracking-widest">Presentes</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 font-light italic">Nenhum culto registrado recentemente.</p>
                        @endforelse
                    </div>
                </div>

                <div class="relative">
                    <div class="aspect-square rounded-[3rem] overflow-hidden shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1438232992991-995b7058bbb3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                            alt="Worship"
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
            <div
                class="absolute top-0 left-0 w-full h-full bg-[url('https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80')] bg-cover bg-center">
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
                    <a href="https://www.youtube.com/@LifeChurch" target="_blank"
                        class="inline-flex items-center space-x-3 bg-white/10 hover:bg-white/20 text-white px-8 py-4 rounded-2xl transition-all duration-300 backdrop-blur-sm border border-white/10 group">
                        <i class="bi bi-youtube text-2xl text-red-600 group-hover:scale-110 transition-transform"></i>
                        <span class="font-bold uppercase tracking-widest text-xs">Visitar Canal no YouTube</span>
                    </a>
                </div>
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
                <div>&copy; 2025 Portal Life Church. Todos os direitos reservados.</div>
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
    </script>
</body>

</html>