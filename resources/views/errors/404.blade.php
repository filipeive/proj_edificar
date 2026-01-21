@extends('layouts.app')

@section('title', 'Página Não Encontrada - Life Church')

@section('content')
    <div class="min-h-[70vh] flex flex-col items-center justify-center text-center p-6">
        <div class="relative mb-12">
            <h1 class="text-[12rem] font-black text-blue-600 opacity-10 leading-none">404</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="bi bi-compass text-8xl text-blue-600 animate-bounce"></i>
            </div>
        </div>

        <h2 class="text-4xl font-black text-gray-900 tracking-tighter mb-4 uppercase">Caminho Não Encontrado</h2>
        <p class="text-lg font-medium text-gray-400 mb-12 max-w-md mx-auto">
            Opa! Parece que você se perdeu ou a página que procura foi movida para uma nova dimensão.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ url()->previous() }}"
                class="px-10 py-5 bg-gray-100 text-gray-600 rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all flex items-center gap-3">
                <i class="bi bi-arrow-left"></i> Voltar Agora
            </a>
            <a href="{{ route('dashboard') }}"
                class="px-10 py-5 bg-blue-600 text-white rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 flex items-center gap-3">
                <i class="bi bi-house-door-fill"></i> Ir para o Portal
            </a>
        </div>

        <div class="mt-20 pt-10 border-t border-gray-100 w-full max-w-sm">
            <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]"> Life Church Edificar • Sistema de
                Gestão </p>
        </div>
    </div>
@endsection