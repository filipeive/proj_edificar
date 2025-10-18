
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeto Edificar - Sistema de Contribuições</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <i class="bi bi-building text-3xl text-blue-600 mr-3"></i>
                    <span class="text-2xl font-bold text-gray-800">Projeto Edificar</span>
                </div>
                <div class="space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-900">Sair</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                                Login
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center text-white mb-20">
            <h1 class="text-5xl md:text-6xl font-bold mb-6">
                <i class="bi bi-building mr-3"></i>Projeto Edificar
            </h1>
            <p class="text-2xl mb-4">Sistema de Gestão de Contribuições para Obras</p>
            <p class="text-lg text-blue-100 mb-8">Gerencie contribuições de forma transparente e organizada com base em pacotes de compromisso</p>

            @auth
                <a href="{{ url('/dashboard') }}" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                    Ir para Dashboard
                </a>
            @else
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition">
                        <i class="bi bi-box-arrow-in-right mr-2"></i>Login
                    </a>
                </div>
            @endauth
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-20">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="text-4xl text-blue-600 mb-4"><i class="bi bi-cash-coin"></i></div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">Contribuições Transparentes</h3>
                <p class="text-gray-600">Registre suas contribuições de forma segura com comprovativos e rastreie o status em tempo real.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="text-4xl text-blue-600 mb-4"><i class="bi bi-handshake"></i></div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">Pacotes de Compromisso</h3>
                <p class="text-gray-600">Escolha livremente entre 5 pacotes de compromisso mensal, com flexibilidade para atualizar a qualquer momento.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="text-4xl text-blue-600 mb-4"><i class="bi bi-file-earmark-pdf"></i></div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">Relatórios Detalhados</h3>
                <p class="text-gray-600">Gere relatórios em PDF e Excel com filtros avançados para acompanhar o progresso da obra.</p>
            </div>
        </div>

        <!-- How It Works -->
        <div class="mt-20 bg-white rounded-lg shadow-lg p-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-12 text-center">Como Funciona?</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-full font-bold text-xl mx-auto mb-4">1</div>
                    <h4 class="font-bold text-gray-800 mb-2">Escolha Pacote</h4>
                    <p class="text-gray-600 text-sm">Escolha seu nível de compromisso mensal</p>
                </div>
                <div class="text-center">
                    <div class="flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-full font-bold text-xl mx-auto mb-4">2</div>
                    <h4 class="font-bold text-gray-800 mb-2">Registre</h4>
                    <p class="text-gray-600 text-sm">Registre suas contribuições com comprovativo</p>
                </div>
                <div class="text-center">
                    <div class="flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-full font-bold text-xl mx-auto mb-4">3</div>
                    <h4 class="font-bold text-gray-800 mb-2">Verifique</h4>
                    <p class="text-gray-600 text-sm">Admin valida as contribuições</p>
                </div>
                <div class="text-center">
                    <div class="flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-full font-bold text-xl mx-auto mb-4">4</div>
                    <h4 class="font-bold text-gray-800 mb-2">Relatório</h4>
                    <p class="text-gray-600 text-sm">Gere relatórios da obra</p>
                </div>
            </div>
        </div>

        <!-- Organization -->
        <div class="mt-20 bg-white rounded-lg shadow-lg p-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Estrutura Organizacional</h2>
            <div class="bg-gray-50 rounded-lg p-8">
                <div class="text-center mb-6">
                    <div class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-bold mb-4">Admin (Comissão da Obra)</div>
                </div>
                <div class="text-center mb-6 flex justify-center">
                    <div class="border-l-2 border-blue-600 h-8"></div>
                </div>
                <div class="text-center mb-6">
                    <div class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg font-bold mb-4">Pastor de Zona</div>
                </div>
                <div class="text-center mb-6 flex justify-center">
                    <div class="border-l-2 border-blue-500 h-8"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="text-center">
                        <div class="inline-block bg-blue-400 text-white px-6 py-2 rounded-lg font-bold mb-4">Supervisor</div>
                    </div>
                    <div class="text-center">
                        <div class="inline-block bg-blue-400 text-white px-6 py-2 rounded-lg font-bold mb-4">Supervisor</div>
                    </div>
                    <div class="text-center">
                        <div class="inline-block bg-blue-400 text-white px-6 py-2 rounded-lg font-bold mb-4">Supervisor</div>
                    </div>
                </div>
                <div class="text-center mb-6 flex justify-center">
                    <div class="border-l-2 border-blue-400 h-8"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <div class="text-center font-bold text-gray-800">Célula 1</div>
                        <div class="text-center text-sm text-gray-600">Líder + Membros</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-center font-bold text-gray-800">Célula 2</div>
                        <div class="text-center text-sm text-gray-600">Líder + Membros</div>
                    </div>
                    <div class="space-y-2">
                        <div class="text-center font-bold text-gray-800">Célula 3</div>
                        <div class="text-center text-sm text-gray-600">Líder + Membros</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Packages Info -->
        <div class="mt-20 mb-20">
            <h2 class="text-3xl font-bold text-gray-800 mb-12 text-center text-white">Pacotes de Compromisso</h2>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-lg shadow-lg p-6 text-center hover:shadow-xl transition">
                    <h4 class="font-bold text-blue-600 mb-2">Pacote 1</h4>
                    <p class="text-2xl font-bold text-gray-800 mb-2">10 - 250 MT</p>
                    <p class="text-sm text-gray-600">Entrada</p>
                </div>
                <div class="bg-white rounded-lg shadow-lg p-6 text-center hover:shadow-xl transition">
                    <h4 class="font-bold text-blue-600 mb-2">Pacote 2</h4>
                    <p class="text-2xl font-bold text-gray-800 mb-2">250 - 500 MT</p>
                    <p class="text-sm text-gray-600">Intermediário</p>
                </div>
                <div class="bg-white rounded-lg shadow-lg p-6 text-center hover:shadow-xl transition">
                    <h4 class="font-bold text-blue-600 mb-2">Pacote 3</h4>
                    <p class="text-2xl font-bold text-gray-800 mb-2">500 - 1000 MT</p>
                    <p class="text-sm text-gray-600">Intermediário-Alto</p>
                </div>
                <div class="bg-white rounded-lg shadow-lg p-6 text-center hover:shadow-xl transition">
                    <h4 class="font-bold text-blue-600 mb-2">Pacote 4</h4>
                    <p class="text-2xl font-bold text-gray-800 mb-2">1000 - 2000 MT</p>
                    <p class="text-sm text-gray-600">Alto</p>
                </div>
                <div class="bg-white rounded-lg shadow-lg p-6 text-center hover:shadow-xl transition">
                    <h4 class="font-bold text-blue-600 mb-2">Pacote 5</h4>
                    <p class="text-2xl font-bold text-gray-800 mb-2">2000+ MT</p>
                    <p class="text-sm text-gray-600">Visionário</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="mb-2"><i class="bi bi-building mr-2"></i>Projeto Edificar</p>
                <p class="text-gray-400">Sistema de Gestão de Contribuições para Obras &copy; 2024</p>
                <p class="text-gray-500 text-sm mt-4">Construindo juntos, com compromisso e fé</p>
            </div>
        </div>
    </footer>
</body>
</html>