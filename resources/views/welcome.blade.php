<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Life Church - Gestão Eclesiástica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.9)), url('{{ asset('images/hero-bg.png') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-5px);
            border-color: rgba(249, 115, 22, 0.3);
        }

        .orange-gradient-text {
            background: linear-gradient(to right, #f97316, #fb923c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            box-shadow: 0 10px 20px -10px rgba(249, 115, 22, 0.5);
        }

        .btn-primary:hover {
            box-shadow: 0 15px 25px -10px rgba(249, 115, 22, 0.6);
            transform: translateY(-2px);
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .floating {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-white text-gray-900 overflow-x-hidden">
    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto">
                    <div class="hidden sm:block">
                        <span class="text-xl font-black tracking-tighter text-gray-900 uppercase">Portal Life
                            Church</span>
                    </div>
                </div>
                <div class="flex items-center space-x-8">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-bold text-gray-700 hover:text-orange-600 transition uppercase tracking-widest">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-gray-900 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-orange-600 transition-all duration-300 shadow-lg uppercase text-xs tracking-widest">Sair</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                                class="btn-primary text-white px-8 py-3 rounded-xl font-bold transition-all duration-300 uppercase text-xs tracking-widest flex items-center">
                                <i class="bi bi-person-circle mr-2 text-lg"></i>Entrar no Portal
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center hero-section overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-32">
            <div class="text-center max-w-4xl mx-auto">
                <div
                    class="inline-flex items-center px-4 py-2 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-[10px] font-black uppercase tracking-[0.2em] mb-8 floating">
                    <span class="mr-2">✨</span> Gestão Eclesiástica de Próxima Geração
                </div>
                <h1 class="text-6xl md:text-8xl font-black text-white mb-8 leading-[0.9] tracking-tighter">
                    O Futuro da Sua <br>
                    <span class="orange-gradient-text">Igreja Conectada</span>
                </h1>
                <p class="text-xl text-gray-300 mb-12 leading-relaxed max-w-2xl mx-auto font-light">
                    Uma plataforma inteligente para pastores e líderes que buscam excelência na gestão de membros,
                    células e formação ministerial.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="w-full sm:w-auto bg-white text-gray-900 px-12 py-5 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all duration-300 shadow-2xl">
                            Aceder ao Painel
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="w-full sm:w-auto btn-primary text-white px-12 py-5 rounded-2xl font-black text-sm uppercase tracking-widest transition-all duration-300">
                            Começar Agora
                        </a>
                        <a href="#features"
                            class="w-full sm:w-auto bg-white/5 backdrop-blur-md text-white border border-white/10 px-12 py-5 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-white/10 transition-all duration-300">
                            Explorar
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 text-white/30 animate-bounce">
            <i class="bi bi-chevron-down text-3xl"></i>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-32 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-20 gap-8">
                <div class="max-w-2xl">
                    <h2 class="text-4xl md:text-6xl font-black text-gray-900 mb-6 tracking-tighter leading-none">Tudo o
                        que você precisa <br>em um só lugar.</h2>
                    <p class="text-gray-500 text-lg">Desenvolvido para simplificar processos e focar no que realmente
                        importa: pessoas.</p>
                </div>
                <div class="w-24 h-1 bg-orange-500 rounded-full hidden md:block"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Eclesiástico -->
                <div
                    class="group p-12 rounded-[2.5rem] bg-white border border-gray-100 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500">
                    <div
                        class="w-20 h-20 bg-orange-50 rounded-3xl flex items-center justify-center text-4xl text-orange-600 mb-10 group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                        <i class="bi bi-church"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4 tracking-tight">Gestão de Cultos</h3>
                    <p class="text-gray-500 leading-relaxed">Relatórios detalhados de cada celebração, com exportação em
                        PDF e partilha instantânea com a liderança.</p>
                </div>

                <!-- Células -->
                <div
                    class="group p-12 rounded-[2.5rem] bg-white border border-gray-100 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500">
                    <div
                        class="w-20 h-20 bg-blue-50 rounded-3xl flex items-center justify-center text-4xl text-blue-600 mb-10 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4 tracking-tight">Células Ativas</h3>
                    <p class="text-gray-500 leading-relaxed">Acompanhe o pulsar da sua igreja através das células.
                        Visitantes, decisões e crescimento real em tempo real.</p>
                </div>

                <!-- Cursos -->
                <div
                    class="group p-12 rounded-[2.5rem] bg-white border border-gray-100 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500">
                    <div
                        class="w-20 h-20 bg-green-50 rounded-3xl flex items-center justify-center text-4xl text-green-600 mb-10 group-hover:bg-green-600 group-hover:text-white transition-all duration-500">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4 tracking-tight">Ensino & Formação</h3>
                    <p class="text-gray-500 leading-relaxed mb-6">De Academia de Vida a Cursos de Casais. Gerencie o
                        trilho
                        de crescimento espiritual de cada membro.</p>
                    <a href="{{ route('public.courses.casais') }}"
                        class="inline-flex items-center text-orange-600 font-black uppercase tracking-widest text-xs hover:text-orange-700 transition group">
                        Inscrição Curso de Casais
                        <i class="bi bi-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats / CTA -->
    <section class="py-32 bg-gray-900 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-orange-600/5 skew-x-12 translate-x-1/4"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div>
                    <h2 class="text-4xl md:text-6xl font-black text-white mb-8 tracking-tighter leading-none">Pronto
                        para elevar o nível da sua gestão?</h2>
                    <p class="text-gray-400 text-xl mb-12 font-light">Junte-se a centenas de líderes que já
                        transformaram a forma como cuidam do rebanho.</p>
                    <div class="flex flex-wrap gap-4">
                        <div class="bg-white/5 backdrop-blur-sm border border-white/10 p-6 rounded-2xl">
                            <div class="text-3xl font-black text-orange-500 mb-1">100%</div>
                            <div class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Seguro & Cloud
                            </div>
                        </div>
                        <div class="bg-white/5 backdrop-blur-sm border border-white/10 p-6 rounded-2xl">
                            <div class="text-3xl font-black text-orange-500 mb-1">+2k</div>
                            <div class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Membros Ativos
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-br from-orange-500 to-orange-700 p-1 rounded-[3rem] shadow-2xl">
                        <div class="bg-gray-900 rounded-[2.8rem] p-12">
                            <h3 class="text-2xl font-black text-white mb-6 tracking-tight">Inscrição Rápida</h3>
                            <p class="text-gray-400 mb-8">Acesse o portal agora e comece a gerenciar sua igreja com
                                inteligência.</p>
                            <a href="{{ route('login') }}"
                                class="block w-full btn-primary text-white text-center py-5 rounded-2xl font-black text-sm uppercase tracking-widest transition-all duration-300">
                                Entrar no Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-12">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto">
                    <span class="text-xl font-black text-gray-900 uppercase tracking-tighter">Portal Life Church</span>
                </div>
                <div class="flex space-x-12">
                    <a href="#"
                        class="text-gray-400 hover:text-orange-600 transition text-sm font-bold uppercase tracking-widest">Termos</a>
                    <a href="#"
                        class="text-gray-400 hover:text-orange-600 transition text-sm font-bold uppercase tracking-widest">Privacidade</a>
                    <a href="#"
                        class="text-gray-400 hover:text-orange-600 transition text-sm font-bold uppercase tracking-widest">Suporte</a>
                </div>
                <div class="flex space-x-6">
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-orange-500 hover:text-white transition"><i
                            class="bi bi-facebook"></i></a>
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-orange-500 hover:text-white transition"><i
                            class="bi bi-instagram"></i></a>
                    <a href="#"
                        class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-orange-500 hover:text-white transition"><i
                            class="bi bi-youtube"></i></a>
                </div>
            </div>
            <div
                class="mt-20 pt-8 border-t border-gray-50 text-center text-gray-400 text-xs font-bold uppercase tracking-widest">
                &copy; {{ date('Y') }} Portal Life Church. Todos os direitos reservados.
            </div>
        </div>
    </footer>
</body>

</html>