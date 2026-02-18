@extends('layouts.auth')

@section('title', 'Login - Portal Life Church')

@section('content')
    <div class="min-h-screen bg-gray-900 flex items-center justify-center py-12 px-4 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl text-orange-600"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl text-blue-600"></div>
        </div>

        <div class="max-w-md w-full space-y-8 relative z-10">
            <div class="text-center">
                <a href="{{ route('welcome') }}" class="inline-block mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 w-auto mx-auto mb-4">
                </a>
                <h1 class="text-4xl font-black text-white tracking-tighter mb-2">Portal Life Church</h1>
                <p class="text-orange-500 font-bold tracking-widest uppercase text-[10px]">Gestão Eclesiástica</p>
            </div>

            <!-- Login Card -->
            <div
                class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative overflow-hidden">
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl">
                        <p class="text-xs font-black uppercase tracking-widest mb-2">Erro de Acesso</p>
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm font-medium">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email"
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">
                            <i class="bi bi-envelope mr-2"></i>E-mail Institucional
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600 @error('email') border-red-500/50 @enderror"
                            placeholder="seu@perfil.com" required autofocus>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password"
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">
                            <i class="bi bi-lock mr-2"></i>Palavra-passe
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600 @error('password') border-red-500/50 @enderror"
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" name="remember"
                                    class="w-5 h-5 rounded-lg border-white/10 bg-white/5 text-orange-600 focus:ring-orange-500 focus:ring-offset-gray-900 transition-all">
                            </div>
                            <span class="text-xs font-bold text-gray-400 group-hover:text-white transition">Manter
                                conectado</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-[10px] font-black text-orange-500 uppercase tracking-widest hover:text-orange-400 transition">
                                Esqueceu a senha?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-700 text-white font-black py-4 rounded-2xl uppercase tracking-widest text-sm shadow-2xl shadow-orange-500/20 hover:scale-[1.02] transition-all duration-300">
                            <i class="bi bi-box-arrow-in-right mr-2"></i>Aceder ao Portal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Back to Welcome -->
            <div class="text-center pt-4">
                <a href="{{ route('welcome') }}"
                    class="text-gray-500 hover:text-white transition text-[10px] font-black uppercase tracking-[0.2em]">
                    <i class="bi bi-arrow-left mr-2"></i>Voltar ao Início
                </a>
            </div>
        </div>
    </div>
@endsection