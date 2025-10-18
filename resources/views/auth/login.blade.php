@extends('layouts.auth')

@section('title', 'Login - Projeto Edificar')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <i class="bi bi-building text-6xl text-white mb-4"></i>
            <h1 class="text-4xl font-bold text-white mb-2">Projeto Edificar</h1>
            <p class="text-blue-100">Sistema de Contribuições</p>
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
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Manter-me conectado</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700 transition mb-4">
                    <i class="bi bi-box-arrow-in-right mr-2"></i>Entrar
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

        <!-- Demo Info -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6 text-center">
            <p class="text-sm text-gray-700 mb-3"><strong>Credenciais de Demonstração:</strong></p>
            <div class="space-y-2 text-sm">
                <div>
                    <p class="text-gray-600">Admin: <code class="bg-gray-200 px-2 py-1 rounded">admin@chiesa.local</code></p>
                    <p class="text-gray-600">Senha: <code class="bg-gray-200 px-2 py-1 rounded">123456</code></p>
                </div>
                <hr class="my-3">
                <div>
                    <p class="text-gray-600">Pastor: <code class="bg-gray-200 px-2 py-1 rounded">pastor@chiesa.local</code></p>
                    <p class="text-gray-600">Senha: <code class="bg-gray-200 px-2 py-1 rounded">123456</code></p>
                </div>
            </div>
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
