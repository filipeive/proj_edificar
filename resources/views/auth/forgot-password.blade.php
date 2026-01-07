@extends('layouts.auth')

@section('title', 'Recuperar Senha - Portal Life Church')

@section('content')
    <div class="min-h-screen bg-gray-900 flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                        class="h-20 mx-auto mb-4 hover:scale-105 transition-transform">
                </a>
                <h1 class="text-3xl font-black text-white mb-2">Recuperar Acesso</h1>
                <p class="text-orange-500 font-bold tracking-widest uppercase text-xs">Portal Life Church</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 border border-white/10">
                <div class="mb-6 text-sm text-gray-600 leading-relaxed text-center">
                    {{ __('Esqueceu sua senha? Sem problemas. Informe seu endereço de e-mail e enviaremos um link de redefinição de senha para você escolher uma nova.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                            <i class="bi bi-envelope mr-2 text-orange-500"></i>Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                            placeholder="seu@email.com" required autofocus>
                        @error('email')
                            <p class="text-red-500 text-xs font-bold mt-2 uppercase">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-orange-600 text-white font-black py-4 rounded-xl hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20 transform hover:-translate-y-1 uppercase tracking-widest text-xs">
                        <i class="bi bi-send-fill mr-2"></i>Enviar Link de Recuperação
                    </button>
                </form>

                <!-- Back to Login -->
                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <a href="{{ route('login') }}"
                        class="text-sm font-bold text-gray-500 hover:text-orange-600 transition flex items-center justify-center">
                        <i class="bi bi-arrow-left mr-2"></i> Voltar ao Login
                    </a>
                </div>
            </div>

            <div class="mt-8 text-center text-white/20 text-[10px] font-black uppercase tracking-[0.2em]">
                &copy; {{ date('Y') }} Life Church System
            </div>
        </div>
    </div>
@endsection