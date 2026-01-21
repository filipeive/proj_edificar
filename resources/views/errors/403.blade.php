@extends('layouts.app')

@section('title', 'Acesso Negado - Life Church')

@section('content')
    <div class="min-h-[70vh] flex flex-col items-center justify-center text-center p-6">
        <div class="relative mb-12">
            <h1 class="text-[12rem] font-black text-red-600 opacity-10 leading-none">403</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="bi bi-shield-lock-fill text-8xl text-red-600"></i>
            </div>
        </div>

        <h2 class="text-4xl font-black text-gray-900 tracking-tighter mb-4 uppercase">Acesso Interditado</h2>
        <p class="text-lg font-medium text-gray-400 mb-12 max-w-md mx-auto">
            Desculpe, mas suas credenciais de acesso não permitem visualizar este conteúdo. Se acredita que isto é um erro,
            contacte o administrador.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ url()->previous() }}"
                class="px-10 py-5 bg-gray-100 text-gray-600 rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all flex items-center gap-3">
                <i class="bi bi-arrow-left"></i> Voltar Agora
            </a>
            <a href="{{ route('dashboard') }}"
                class="px-10 py-5 bg-blue-600 text-white rounded-3xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-100">
                Painel Principal
            </a>
        </div>

        @if(config('app.debug'))
            <div
                class="mt-12 p-6 bg-gray-50 rounded-2xl text-left w-full max-w-lg overflow-auto text-[10px] font-mono text-gray-400 border border-gray-100">
                <p class="font-black mb-2 uppercase tracking-widest">Debug Intelligence:</p>
                <div class="space-y-1">
                    <p>USER_ROLE: {{ auth()->user()->role ?? 'GUEST' }}</p>
                    <p>TARGET_ROUTE: {{ request()->route() ? request()->route()->getName() : 'N/A' }}</p>
                    <p>AUTH_STATUS: {{ auth()->check() ? 'AUTHENTICATED' : 'GUEST_MODE' }}</p>
                </div>
            </div>
        @endif
    </div>
@endsection