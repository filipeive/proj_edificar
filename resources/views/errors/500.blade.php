@extends('layouts.app')

@section('title', 'Erro Interno - Life Church')

@section('content')
    <div class="min-h-[70vh] flex flex-col items-center justify-center text-center p-6">
        <div class="relative mb-12">
            <h1 class="text-[12rem] font-black text-red-600 opacity-10 leading-none">500</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="bi bi-exclamation-triangle-fill text-8xl text-red-600"></i>
            </div>
        </div>

        <h2 class="text-4xl font-black text-gray-900 tracking-tighter mb-4 uppercase">Erro de Servidor</h2>
        <p class="text-lg font-medium text-gray-400 mb-12 max-w-md mx-auto">
            Algo não saiu como esperado nos nossos bastidores. Nossa equipe técnica já foi notificada.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <button onclick="window.location.reload()"
                class="px-10 py-5 bg-gray-900 text-white rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all flex items-center gap-3">
                <i class="bi bi-arrow-clockwise text-lg"></i> Tentar Novamente
            </button>
            <a href="{{ route('dashboard') }}"
                class="px-10 py-5 bg-blue-600 text-white rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-100">
                Painel Principal
            </a>
        </div>

        <div class="mt-20 pt-10 border-t border-gray-100 w-full max-w-sm">
            <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]"> Caso o erro persista, contacte o
                suporte técnico. </p>
        </div>
    </div>
@endsection