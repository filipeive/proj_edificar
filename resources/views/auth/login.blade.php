@extends('layouts.auth')

@section('title', 'Login - Portal Life Church')

@section('content')
    <div class="min-h-screen bg-gray-900 flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 mx-auto mb-4">
                <h1 class="text-4xl font-black text-white mb-2">Portal Life Church</h1>
                <p class="text-orange-500 font-bold tracking-widest uppercase text-xs">Gestão Eclesiástica</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-lg shadow-2xl p-8">
                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <p class="font-bold mb-2">Erro na autenticação:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-envelope mr-2"></i>Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                            placeholder="seu@email.com" required autofocus>
                        @error('email')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-lock mr-2"></i>Senha
                        </label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                            placeholder="••••••••" required>
                        @error('password')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Manter-me conectado</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl hover:bg-orange-700 transition mb-4 shadow-lg shadow-orange-600/20">
                        <i class="bi bi-box-arrow-in-right mr-2"></i>ENTRAR
                    </button>
                </form>

                <!-- Forgot Password Link -->
                @if (Route::has('password.request'))
                    <div class="text-center">
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            Esqueceu sua senha?
                        </a>
                    </div>
                @endif
            </div>


            <!-- Back to Welcome -->
            <div class="mt-6 text-center">
                <a href="/" class="text-blue-100 hover:text-white text-sm">
                    <i class="bi bi-arrow-left mr-2"></i>Voltar ao Início
                </a>
            </div>
        </div>
    </div>
@endsection