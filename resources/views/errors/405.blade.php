@extends('layouts.app')

@section('title', 'Método Não Permitido - Life Church')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm text-center">
        <div class="w-14 h-14 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="bi bi-slash-circle"></i>
        </div>

        <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-amber-700">Erro 405 • Ação Não Permitida</span>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight mt-1 mb-2">Método Inválido</h1>
        <p class="text-xs text-gray-500 mb-6">
            O método de requisição HTTP utilizado não é suportado por esta rota.
        </p>

        <div class="flex items-center justify-center gap-2">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                <i class="bi bi-house-door"></i> Ir para o Portal
            </a>
        </div>
    </div>
</div>
@endsection
