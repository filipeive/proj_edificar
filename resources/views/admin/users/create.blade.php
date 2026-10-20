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
    <div class="w-full space-y-5">
        <!-- Page Header -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Controle Administrativo</p>
                <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Novo Utilizador</h1>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Preencha os dados abaixo para registar um novo acesso ao portal</p>
            </div>
            <div>
                <a href="{{ route('users.index') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-arrow-left"></i> <span>Voltar</span>
                </a>
            </div>
        </header>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Main Info Area -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Informações Pessoais -->
                    <x-card title="Informações Pessoais" subtitle="Dados principais de contacto e identificação">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                            <div class="space-y-1.5">
                                <label for="cell_id" class="block text-xs font-bold text-gray-700">
                                    Célula Designada
                                </label>
                                <div class="relative">
                                    <select name="cell_id" id="cell_id"
                                        class="searchable-select w-full px-3 py-2 bg-white border border-gray-300 rounded-lg font-bold text-xs sm:text-sm text-gray-900 transition-all appearance-none cursor-pointer custom-select"
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
                                    <p class="mt-1 text-xs font-bold text-red-600 flex items-center gap-1">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </x-card>

                    <!-- Credenciais de Acesso -->
                    <x-card title="Credenciais de Acesso" subtitle="Segurança e senha de acesso inicial para o utilizador">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Password -->
                            <div class="space-y-1.5">
                                <label for="password" class="block text-xs font-bold text-gray-700">
                                    Definir Senha <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="bi bi-key text-sm"></i>
                                    </div>
                                    <input type="password" name="password" id="password" required
                                        class="w-full pl-9 pr-10 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 @error('password') border-red-300 @enderror">
                                    <button type="button" onclick="togglePassword('password')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-600 transition-colors">
                                        <i class="bi bi-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-xs font-bold text-red-600 flex items-center gap-1">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-1.5">
                                <label for="password_confirmation" class="block text-xs font-bold text-gray-700">
                                    Confirmar Senha <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="bi bi-check-all text-sm"></i>
                                    </div>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                        class="w-full pl-9 pr-10 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs sm:text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                                    <button type="button" onclick="togglePassword('password_confirmation')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-600 transition-colors">
                                        <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dica de Segurança -->
                        <div class="mt-4 p-3.5 bg-orange-50 rounded-xl border border-orange-200 flex gap-3 items-start">
                            <div class="w-8 h-8 rounded-lg bg-white border border-orange-200 text-orange-700 flex items-center justify-center shrink-0">
                                <i class="bi bi-info-circle text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-orange-950 uppercase tracking-wider mb-0.5">Dica de Segurança</h4>
                                <p class="text-xs font-medium text-orange-800 leading-relaxed">
                                    O utilizador receberá um email de boas-vindas com as suas credenciais. Certifique-se de que o email inserido está correto.
                                </p>
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Sidebar Settings & Actions -->
                <div class="space-y-4">
                    <!-- Configurações de Papel -->
                    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-4">
                        <div class="space-y-1.5">
                            <label for="role" class="block text-xs font-black text-gray-700 uppercase tracking-wider">
                                Nível de Acesso
                            </label>
                            <div class="relative">
                                <select name="role" id="role" required
                                    class="w-full px-3 py-2 bg-white border border-gray-300 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 rounded-lg font-bold text-xs sm:text-sm text-gray-900 transition-all appearance-none cursor-pointer custom-select">
                                    <option value="">Selecione o papel</option>
                                    <option value="membro" {{ old('role') == 'membro' ? 'selected' : '' }}>Membro</option>
                                    <option value="lider_celula" {{ old('role') == 'lider_celula' ? 'selected' : '' }}>Líder de Célula</option>
                                    <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="pastor_zona" {{ old('role') == 'pastor_zona' ? 'selected' : '' }}>Pastor de Zona</option>
                                    <option value="secretaria" {{ old('role') == 'secretaria' ? 'selected' : '' }}>Secretária</option>
                                    <option value="comissao_obra" {{ old('role') == 'comissao_obra' ? 'selected' : '' }}>Comissão de Obra</option>
                                    <option value="responsavel_pacote" {{ old('role') == 'responsavel_pacote' ? 'selected' : '' }}>Resp. de Pacote</option>
                                    <option value="tesouraria" {{ old('role') == 'tesouraria' ? 'selected' : '' }}>Tesouraria</option>
                                    <option value="pastor" {{ old('role') == 'pastor' ? 'selected' : '' }}>Pastor</option>
                                    <option value="pastor_senior" {{ old('role') == 'pastor_senior' ? 'selected' : '' }}>Pastor Senior</option>
                                    <option value="administracao" {{ old('role') == 'administracao' ? 'selected' : '' }}>Administração</option>
                                    @if($canManageAdminRoles ?? false)
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2 pt-2 border-t border-gray-200">
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider">
                                Status do Perfil
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="hidden peer">
                                    <div class="w-full py-2 text-center rounded-lg border border-gray-300 bg-gray-50 text-gray-700 text-xs font-bold peer-checked:bg-emerald-600 peer-checked:border-emerald-600 peer-checked:text-white transition-all">
                                        Ativo
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }} class="hidden peer">
                                    <div class="w-full py-2 text-center rounded-lg border border-gray-300 bg-gray-50 text-gray-700 text-xs font-bold peer-checked:bg-red-600 peer-checked:border-red-600 peer-checked:text-white transition-all">
                                        Inativo
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação Final -->
                    <x-card compact="true">
                        <div class="space-y-2">
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
