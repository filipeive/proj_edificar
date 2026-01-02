@extends('layouts.auth')

@section('title', 'Inscrição - ' . $course->name)

@section('content')
    <div
        class="min-h-screen bg-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-xl w-full space-y-8 relative z-10">
            <div class="text-center">
                <a href="{{ route('welcome') }}" class="inline-block mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto mx-auto">
                </a>
                <h2 class="text-4xl font-black text-white tracking-tighter">Inscrição: {{ $course->name }}</h2>
                <p class="mt-2 text-gray-400 font-medium uppercase tracking-widest text-xs">Portal Life Church -
                    Edificando Vidas</p>
            </div>

            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2.5rem] p-8 md:p-12 shadow-2xl">
                <form action="{{ route('public.courses.store', $course->slug) }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Nome Completo -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nome
                            Completo</label>
                        <input type="text" name="name" required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                            placeholder="Seu nome completo">
                    </div>

                    <!-- Email -->
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Email</label>
                        <input type="email" name="email" required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                            placeholder="seu@email.com">
                    </div>

                    <!-- Telefone -->
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Telefone /
                            WhatsApp</label>
                        <input type="text" name="phone" required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                            placeholder="+244 9XX XXX XXX">
                    </div>

                    <!-- Observações -->
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Observações
                            (Opcional)</label>
                        <textarea name="observations" rows="3"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-gray-600"
                            placeholder="Alguma informação adicional?"></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-700 text-white font-black py-5 rounded-2xl uppercase tracking-widest text-sm shadow-2xl shadow-orange-500/20 hover:scale-[1.02] transition-all duration-300">
                            Confirmar Inscrição
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center">
                <a href="{{ route('welcome') }}"
                    class="text-gray-500 hover:text-white transition text-xs font-bold uppercase tracking-widest">
                    <i class="bi bi-arrow-left mr-2"></i> Voltar para o Início
                </a>
            </div>
        </div>
    </div>
@endsection