@extends('layouts.app')

@section('title', 'Sessão Expirada - Life Church')

@section('content')
    <div class="min-h-[70vh] flex flex-col items-center justify-center text-center p-6">
        <div class="relative mb-12">
            <h1 class="text-[12rem] font-black text-orange-600 opacity-10 leading-none">419</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="bi bi-clock-history text-8xl text-orange-600"></i>
            </div>
        </div>

        <h2 class="text-4xl font-black text-gray-900 tracking-tighter mb-4 uppercase">Sessão Expirada</h2>
        <p class="text-lg font-medium text-gray-400 mb-12 max-w-md mx-auto">
            Por segurança, sua sessão expirou por falta de atividade ou autenticidade. Por favor, atualize a página.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ url()->current() }}"
                class="px-10 py-5 bg-orange-600 text-white rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-orange-700 transition-all shadow-xl shadow-orange-100 flex items-center gap-3">
                <i class="bi bi-arrow-clockwise text-lg"></i> Atualizar Página
            </a>
            <a href="{{ route('login') }}"
                class="px-10 py-5 bg-gray-900 text-white rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all">
                Ir para Login
            </a>
        </div>
    </div>
@endsection