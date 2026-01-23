@extends('layouts.app')

@section('title', 'Meu Perfil - Portal Life Church')
@section('page-title', 'Meu Perfil')
@section('page-subtitle', 'Gestão de informações pessoais e segurança')

@section('content')
    <div class="w-full">
        <!-- Header Profile Card -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-10 overflow-hidden relative group">
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-blue-50/30 rounded-full -mr-48 -mt-48 transition-transform group-hover:scale-110 duration-700">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-10">
                <div
                    class="w-32 h-32 rounded-[2.5rem] bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center font-black text-5xl shadow-2xl shadow-blue-100 group-hover:rotate-6 transition-transform">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div class="flex-1 space-y-4 text-center md:text-left">
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-4">
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter">{{ $user->name }}</h1>
                        <span
                            class="px-4 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    </div>
                    <p class="text-lg font-bold text-gray-400">{{ $user->email }}</p>

                    <div class="flex flex-wrap justify-center md:justify-start gap-3">
                        @if($user->cell)
                            <div
                                class="flex items-center gap-2 px-6 py-2 bg-indigo-50 text-indigo-600 rounded-2xl text-xs font-black uppercase tracking-widest border border-indigo-100">
                                <i class="bi bi-geo-alt-fill"></i> Célula: {{ $user->cell->name }}
                            </div>
                        @endif
                        <div
                            class="flex items-center gap-2 px-6 py-2 bg-gray-50 text-gray-500 rounded-2xl text-xs font-black uppercase tracking-widest border border-gray-100">
                            <i class="bi bi-calendar-check-fill"></i> Desde: {{ $user->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Edit Profile Form -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                    <h2 class="text-xl font-black text-gray-900 flex items-center gap-3">
                        <i class="bi bi-person-gear text-blue-600"></i>
                        Informações Pessoais
                    </h2>
                </div>
                <div class="p-10">
                    <form action="{{ route('profile.update') }}" method="POST" id="profileUpdateForm" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-2">
                            <label for="name"
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Nome
                                Completo</label>
                            <input type="text" name="name" id="name" required
                                class="w-full h-14 bg-gray-50 border-none rounded-2xl px-6 font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 transition-all"
                                value="{{ $user->name }}">
                        </div>

                        <div class="space-y-2">
                            <label for="email"
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Endereço de
                                Email</label>
                            <input type="email" name="email" id="email" required
                                class="w-full h-14 bg-gray-50 border-none rounded-2xl px-6 font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 transition-all"
                                value="{{ $user->email }}">
                        </div>

                        <div class="space-y-2">
                            <label for="phone"
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Telefone /
                                WhatsApp</label>
                            <input type="tel" name="phone" id="phone"
                                class="w-full h-14 bg-gray-50 border-none rounded-2xl px-6 font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 transition-all"
                                value="{{ $user->phone }}" placeholder="Ex: 840000000">
                        </div>

                        <button type="submit"
                            class="w-full h-16 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center gap-3">
                            <i class="bi bi-save2-fill text-lg"></i>
                            Atualizar Meus Dados
                        </button>
                    </form>
                </div>
            </div>

            <!-- Update Password Form -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                    <h2 class="text-xl font-black text-gray-900 flex items-center gap-3">
                        <i class="bi bi-shield-lock-fill text-orange-600"></i>
                        Segurança da Conta
                    </h2>
                </div>
                <div class="p-10">
                    <form action="{{ route('password.update') }}" method="POST" id="passwordUpdateForm" class="space-y-6">
                        @csrf
                        @method('put')

                        <div class="space-y-2">
                            <label for="current_password"
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Senha
                                Atual</label>
                            <input type="password" name="current_password" id="current_password" required
                                class="w-full h-14 bg-gray-50 border-none rounded-2xl px-6 font-bold text-gray-700 focus:ring-2 focus:ring-orange-500 transition-all"
                                placeholder="••••••••">
                        </div>

                        <div class="space-y-2">
                            <label for="password"
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Nova
                                Senha</label>
                            <input type="password" name="password" id="password" required
                                class="w-full h-14 bg-gray-50 border-none rounded-2xl px-6 font-bold text-gray-700 focus:ring-2 focus:ring-orange-500 transition-all"
                                placeholder="Mínimo 8 caracteres">
                        </div>

                        <div class="space-y-2">
                            <label for="password_confirmation"
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Confirmar Nova
                                Senha</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full h-14 bg-gray-50 border-none rounded-2xl px-6 font-bold text-gray-700 focus:ring-2 focus:ring-orange-500 transition-all"
                                placeholder="Confirme sua nova senha">
                        </div>

                        <button type="button"
                            onclick="confirmAction('Alterar Senha', 'Tem certeza que deseja alterar sua senha?', 'question', 'Sim, alterar', 'passwordUpdateForm')"
                            class="w-full h-16 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all active:scale-95 flex items-center justify-center gap-3">
                            <i class="bi bi-key-fill text-lg"></i>
                            Redefinir Senha
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Intelligence Footer -->
        <div
            class="bg-gray-50 rounded-[2.5rem] p-10 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div
                    class="w-16 h-16 rounded-3xl bg-white shadow-sm flex items-center justify-center text-2xl text-blue-600 border border-gray-100">
                    <i class="bi bi-fingerprint"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-tight">Identificador de
                        Sistema</p>
                    <p class="text-xl font-mono font-black text-gray-900 tracking-tighter">
                        #{{ str_pad($user->id, 8, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('dashboard') }}"
                    class="px-8 py-4 bg-white text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest border border-gray-200 hover:bg-gray-100 transition-all">
                    Voltar ao Portal
                </a>
            </div>
        </div>
    </div>

    @if (session('status') === 'profile-updated')
        <script>
            window.addEventListener('load', () => {
                showSuccess('Suas informações foram atualizadas com sucesso!');
            });
        </script>
    @endif

    @if (session('status') === 'password-updated')
        <script>
            window.addEventListener('load', () => {
                showSuccess('Sua senha foi alterada com sucesso!');
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            window.addEventListener('load', () => {
                showError('Não foi possível processar sua solicitação. Verifique os dados inseridos.');
            });
        </script>
    @endif
@endsection