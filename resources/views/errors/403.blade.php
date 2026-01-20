@extends('layouts.app')

@section('title', 'Acesso Negado')

@section('content')
    <div class="min-h-[60vh] flex flex-col items-center justify-center text-center px-4">
        <div class="bg-red-100 rounded-full p-6 mb-6">
            <i class="bi bi-shield-lock-fill text-6xl text-red-500"></i>
        </div>

        <h1 class="text-3xl font-black text-slate-800 mb-2">Acesso Negado</h1>

        <p class="text-slate-500 max-w-md mb-8">
            Você não tem permissão para acessar esta página. Se acredita que isto é um erro, por favor contacte o
            administrador do sistema.
        </p>

        <div class="flex gap-4">
            <a href="{{ url()->previous() }}"
                class="px-6 py-2 bg-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-300 transition-colors">
                Voltar
            </a>
            <a href="{{ route('dashboard') }}"
                class="px-6 py-2 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors">
                Ir para Dashboard
            </a>
        </div>

        @if(config('app.debug'))
            <div
                class="mt-12 p-4 bg-slate-100 rounded-lg text-left w-full max-w-lg overflow-auto text-xs font-mono text-slate-600">
                <p class="font-bold mb-2">Debug Info:</p>
                <p>User Role: {{ auth()->user()->role ?? 'Guest' }}</p>
                <p>Route: {{ request()->route()->getName() }}</p>
            </div>
        @endif
    </div>
@endsection