@extends('layouts.app')

@section('title', 'Acesso Negado - Life Church')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm text-center">
        <div class="w-14 h-14 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="bi bi-shield-lock-fill"></i>
        </div>

        <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-red-600">Erro 403 • Não Autorizado</span>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight mt-1 mb-2">Acesso Restrito</h1>
        <p class="text-xs text-gray-500 mb-6">
            Suas credenciais não têm permissão para acessar esta página ou recurso. Contacte o administrador caso necessite de liberação.
        </p>

        <div class="flex items-center justify-center gap-2">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                <i class="bi bi-house-door"></i> Painel Principal
            </a>
        </div>

        @if(config('app.debug'))
            <div class="mt-6 p-3 bg-gray-50 rounded-lg text-left text-[11px] font-mono text-gray-500 border border-gray-200 space-y-1">
                <p class="font-bold text-[10px] uppercase text-gray-400">Debug Info:</p>
                <p>Papel: <span class="font-bold text-gray-700">{{ auth()->user()->role ?? 'Convidado' }}</span></p>
                <p>Rota: <span class="font-bold text-gray-700">{{ request()->route() ? request()->route()->getName() : 'N/A' }}</span></p>
            </div>
        @endif
    </div>
</div>
@endsection
