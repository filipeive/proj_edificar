@extends('layouts.app')

@section('title', 'Criar Utilizador - Portal Life Church')
@section('page-title', 'Criar Novo Utilizador')
@section('page-subtitle', 'Adicionar um novo membro ou líder ao sistema')

@section('header-actions')
    <a href="{{ route('users.index') }}"
        class="inline-flex items-center gap-3 text-gray-400 hover:text-blue-600 transition-all font-black text-xs uppercase tracking-widest">
        <i class="bi bi-arrow-left text-lg"></i>
        <span class="hidden md:inline">Voltar à Lista</span>
    </a>
@endsection

@section('content')
    <div class="w-full space-y-8">
        <!-- Header Card -->
        <x-card variant="gradient">
            <div class="flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                <div class="w-20 h-20 rounded-[2rem] bg-orange-500 text-white flex items-center justify-center text-3xl shadow-xl shadow-orange-500/20">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-zinc-900 dark:text-zinc-100 tracking-tight uppercase mb-1">Novo Utilizador</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 font-medium">Preencha os dados abaixo para registar um novo acesso ao portal</p>
                </div>
            </div>
        </x-card>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Info Area -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Informações Pessoais -->
                    <x-card title="Informações Pessoais" subtitle="Dados principais de contacto e identificação">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <x-text-input-premium 
                                label="Nome Completo" 
                                name="name" 
                                placeholder="Ex: João Silva" 
                                icon="bi bi-person" 
                                required 
                            />

                            <!-- Email -->
                            <x-text-input-premium 
                                label="Endereço de Email" 
                                name="email" 
                                type="email"
                                placeholder="joao.silva@email.com" 
                                icon="bi bi-envelope" 
                                required 
                            />

                            <!-- Telefone -->
                            <x-text-input-premium 
                                label="Telemóvel (WhatsApp)" 
                                name="phone" 
                                placeholder="+258 84 000 0000" 
                                icon="bi bi-telephone" 
                            />

                            <!-- Célula -->
                            <div class="space-y-2">
                                <label for="cell_id" class="block mb-2 text-xs font-black uppercase tracking-[0.08em] text-zinc-500 dark:text-zinc-400">
                                    Célula Designada
                                </label>
                                <div class="relative">
                                    <select name="cell_id" id="cell_id"
                                        class="searchable-select w-full px-4 py-3 bg-gray-50/50 dark:bg-zinc-900/20 border border-gray-200 dark:border-zinc-800 rounded-2xl font-bold text-sm text-zinc-900 dark:text-zinc-100 transition-all appearance-none cursor-pointer custom-select"
                                        data-label="Célula">
                                        <option value="">Sem célula</option>
                                        @foreach($cells as $cell)
                                            <option value="{{ $cell->id }}" {{ old('cell_id') == $cell->id ? 'selected' : '' }}>
                                                {{ $cell->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('cell_id')
                                    <p class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-1.5 animate-pulse">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </x-card>

                    <!-- Credenciais de Acesso -->
                    <x-card title="Credenciais de Acesso" subtitle="Segurança e senha de acesso inicial para o utilizador">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div class="space-y-2">
                                <label for="password" class="block mb-2 text-xs font-black uppercase tracking-[0.08em] text-zinc-500 dark:text-zinc-400">
                                    Definir Senha <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-zinc-400">
                                        <i class="bi bi-key text-base"></i>
                                    </div>
                                    <input type="password" name="password" id="password" required
                                        class="w-full pl-11 pr-12 py-3 bg-gray-50/50 dark:bg-zinc-900/20 border border-gray-200 dark:border-zinc-800 rounded-2xl font-semibold text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 @error('password') border-red-300 ring-red-500/10 @enderror">
                                    <button type="button" onclick="togglePassword('password')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-orange-500 transition-colors">
                                        <i class="bi bi-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-1.5 animate-pulse">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-2">
                                <label for="password_confirmation" class="block mb-2 text-xs font-black uppercase tracking-[0.08em] text-zinc-500 dark:text-zinc-400">
                                    Confirmar Senha <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-zinc-400">
                                        <i class="bi bi-check-all text-base"></i>
                                    </div>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                        class="w-full pl-11 pr-12 py-3 bg-gray-50/50 dark:bg-zinc-900/20 border border-gray-200 dark:border-zinc-800 rounded-2xl font-semibold text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500">
                                    <button type="button" onclick="togglePassword('password_confirmation')"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-orange-500 transition-colors">
                                        <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dica de Segurança -->
                        <div class="mt-6 p-5 bg-orange-50/50 dark:bg-orange-500/5 rounded-2xl border border-orange-100 dark:border-orange-500/10 flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                                <i class="bi bi-info-circle"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-orange-950 dark:text-orange-400 uppercase tracking-widest mb-1">Dica de Segurança</h4>
                                <p class="text-xs font-semibold text-orange-700/80 dark:text-zinc-400 leading-relaxed">
                                    O utilizador receberá um email de boas-vindas com as suas credenciais. Certifique-se de que o email inserido está correto.
                                </p>
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Sidebar Settings & Actions -->
                <div class="space-y-8">
                    <!-- Configurações de Papel -->
                    <div class="bg-zinc-950 dark:bg-zinc-900/80 border border-zinc-800 text-white rounded-[2.5rem] p-8 space-y-6">
                        <div class="space-y-2">
                            <label for="role" class="block text-xs font-black text-orange-400 uppercase tracking-widest">
                                Nível de Acesso
                            </label>
                            <div class="relative">
                                <select name="role" id="role" required
                                    class="w-full px-4 py-3 bg-white/5 border border-zinc-800 focus:bg-white/10 focus:ring-4 focus:ring-orange-500/20 rounded-2xl font-bold text-sm text-white transition-all appearance-none cursor-pointer custom-select">
                                    <option value="" class="bg-zinc-950 text-white">Selecione o papel</option>
                                    <option value="membro" {{ old('role') == 'membro' ? 'selected' : '' }} class="bg-zinc-950">Membro</option>
                                    <option value="lider_celula" {{ old('role') == 'lider_celula' ? 'selected' : '' }} class="bg-zinc-950">Líder de Célula</option>
                                    <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }} class="bg-zinc-950">Supervisor</option>
                                    <option value="pastor_zona" {{ old('role') == 'pastor_zona' ? 'selected' : '' }} class="bg-zinc-950">Pastor de Zona</option>
                                    <option value="secretaria" {{ old('role') == 'secretaria' ? 'selected' : '' }} class="bg-zinc-950">Secretária</option>
                                    <option value="comissao_obra" {{ old('role') == 'comissao_obra' ? 'selected' : '' }} class="bg-zinc-950">Comissão de Obra</option>
                                    <option value="responsavel_pacote" {{ old('role') == 'responsavel_pacote' ? 'selected' : '' }} class="bg-zinc-950">Resp. de Pacote</option>
                                    <option value="tesouraria" {{ old('role') == 'tesouraria' ? 'selected' : '' }} class="bg-zinc-950">Tesouraria</option>
                                    <option value="pastor" {{ old('role') == 'pastor' ? 'selected' : '' }} class="bg-zinc-950">Pastor</option>
                                    <option value="pastor_senior" {{ old('role') == 'pastor_senior' ? 'selected' : '' }} class="bg-zinc-950">Pastor Senior</option>
                                    <option value="administracao" {{ old('role') == 'administracao' ? 'selected' : '' }} class="bg-zinc-950">Administração</option>
                                    @if($canManageAdminRoles ?? false)
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }} class="bg-zinc-950">Administrador</option>
                                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }} class="bg-zinc-950">Super Admin</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4 pt-2">
                            <label class="block text-xs font-black text-orange-400 uppercase tracking-widest">
                                Status do Perfil
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="hidden peer">
                                    <div class="w-full py-3.5 text-center rounded-2xl border-2 border-white/5 bg-white/5 text-xs font-black uppercase tracking-widest peer-checked:bg-emerald-600 peer-checked:border-emerald-500 peer-checked:text-white group-hover:bg-white/10 transition-all">
                                        Ativo
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }} class="hidden peer">
                                    <div class="w-full py-3.5 text-center rounded-2xl border-2 border-white/5 bg-white/5 text-xs font-black uppercase tracking-widest peer-checked:bg-red-600 peer-checked:border-red-500 peer-checked:text-white group-hover:bg-white/10 transition-all">
                                        Inativo
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação Final -->
                    <x-card compact="true">
                        <div class="space-y-3 p-1">
                            <x-button type="submit" variant="primary" size="md" icon="bi bi-person-check" class="w-full">
                                Finalizar Registo
                            </x-button>
                            <x-button href="{{ route('users.index') }}" variant="outline" size="md" icon="bi bi-x-circle" class="w-full">
                                Cancelar
                            </x-button>
                        </div>
                    </x-card>
                </div>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
@endsection
