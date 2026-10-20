@extends('layouts.auth')

@section('title', 'Inscrição - ' . $course->name)

@section('content')
    <div class="min-h-screen bg-gray-50 flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full space-y-5">
            <div class="text-center">
                <a href="{{ route('welcome') }}" class="inline-block mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto mx-auto">
                </a>
                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-orange-700">Portal Life Church • Edificando Vidas</p>
                <h1 class="mt-1 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Inscrição: {{ $course->name }}</h1>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm">
                <form action="{{ route('public.courses.store', $course->slug) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Nome Completo <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                            placeholder="Seu nome completo">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                            placeholder="seu@email.com">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Telefone / WhatsApp <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                            placeholder="+258 8X XXX XXXX">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Observações (Opcional)</label>
                        <textarea name="observations" rows="3"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                            placeholder="Alguma informação adicional?"></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full rounded-lg bg-orange-700 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm hover:bg-orange-800 transition">
                            Confirmar Inscrição
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center">
                <a href="{{ route('welcome') }}"
                    class="text-gray-500 hover:text-gray-900 transition text-xs font-bold">
                    <i class="bi bi-arrow-left mr-1"></i> Voltar para o Início
                </a>
            </div>
        </div>
    </div>
@endsection
