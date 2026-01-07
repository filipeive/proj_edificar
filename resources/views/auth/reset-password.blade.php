@extends('layouts.auth')

@section('title', 'Redefinir Senha - Portal Life Church')

@section('content')
    <div class="min-h-screen bg-gray-900 flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo"
                        class="h-20 mx-auto mb-4 hover:scale-105 transition-transform">
                </a>
                <h1 class="text-3xl font-black text-white mb-2">Redefinir Senha</h1>
                <p class="text-orange-500 font-bold tracking-widest uppercase text-xs">Portal Life Church</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 border border-white/10">
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                            <i class="bi bi-envelope mr-2 text-orange-500"></i>Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                            required autofocus autocomplete="username">
                        @error('email')
                            <p class="text-red-500 text-xs font-bold mt-2 uppercase">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                            <i class="bi bi-lock mr-2 text-orange-500"></i>Nova Senha
                        </label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                            required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
                        @error('password')
                            <p class="text-red-500 text-xs font-bold mt-2 uppercase">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-8">
                        <label for="password_confirmation"
                            class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                            <i class="bi bi-shield-lock mr-2 text-orange-500"></i>Confirmar Senha
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all @error('password_confirmation') border-red-500 @enderror"
                            required autocomplete="new-password" placeholder="Repita a nova senha">
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs font-bold mt-2 uppercase">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-orange-600 text-white font-black py-4 rounded-xl hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20 transform hover:-translate-y-1 uppercase tracking-widest text-xs">
                        <i class="bi bi-check-circle-fill mr-2"></i>Redefinir Senha
                    </button>
                </form>
            </div>

            <div class="mt-8 text-center text-white/20 text-[10px] font-black uppercase tracking-[0.2em]">
                &copy; {{ date('Y') }} Life Church System
            </div>
        </div>
    </div>
@endsection