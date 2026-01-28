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
        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Header Card -->
            <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-blue-50/50 rounded-full -mr-32 -mt-32 transition-transform group-hover:scale-110 duration-700">
                </div>

                <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                    <div
                        class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center text-4xl shadow-2xl shadow-blue-100">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase mb-2">Novo Utilizador</h1>
                        <p class="text-gray-500 font-medium">Preencha os dados abaixo para registar um novo acesso ao portal</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('users.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Info -->
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-8 border-b border-gray-50 flex items-center gap-3 bg-gray-50/30">
                                <i class="bi bi-person-vcard text-blue-600"></i>
                                <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Informações Pessoais
                                </h2>
                            </div>
                            <div class="p-10 space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <!-- Nome -->
                                    <div class="space-y-2">
                                        <label for="name"
                                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                            Nome Completo <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                            placeholder="Ex: João Silva"
                                            class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl font-bold text-sm transition-all @error('name') ring-2 ring-red-100 border-red-200 @enderror">
                                        @error('name')
                                            <p class="mt-1 text-[10px] font-black text-red-500 uppercase tracking-widest">
                                                {{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="space-y-2">
                                        <label for="email"
                                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                            Endereço de Email <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                            placeholder="joao.silva@email.com"
                                            class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl font-bold text-sm transition-all @error('email') ring-2 ring-red-100 border-red-200 @enderror">
                                        @error('email')
                                            <p class="mt-1 text-[10px] font-black text-red-500 uppercase tracking-widest">
                                                {{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Telefone -->
                                    <div class="space-y-2">
                                        <label for="phone"
                                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                            Telemóvel (WhatsApp)
                                        </label>
                                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                            placeholder="+258 84 000 0000"
                                            class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl font-bold text-sm transition-all @error('phone') ring-2 ring-red-100 border-red-200 @enderror">
                                        @error('phone')
                                            <p class="mt-1 text-[10px] font-black text-red-500 uppercase tracking-widest">
                                                {{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Célula -->
                                    <div class="space-y-2">
                                        <label for="cell_id"
                                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                            Célula Designada
                                        </label>
                                        <select name="cell_id" id="cell_id"
                                            class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-100 rounded-2xl font-bold text-sm transition-all appearance-none cursor-pointer">
                                            <option value="">Sem célula</option>
                                            @foreach($cells as $cell)
                                                <option value="{{ $cell->id }}" {{ old('cell_id') == $cell->id ? 'selected' : '' }}>
                                                    {{ $cell->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cell_id')
                                            <p class="mt-1 text-[10px] font-black text-red-500 uppercase tracking-widest">
                                                {{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-8 border-b border-gray-50 flex items-center gap-3 bg-gray-50/30">
                                <i class="bi bi-shield-lock text-purple-600"></i>
                                <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Credenciais de
                                    Acesso</h2>
                            </div>
                            <div class="p-10 space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <!-- Password -->
                                    <div class="space-y-2">
                                        <label for="password"
                                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                            Definir Senha <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative group">
                                            <i
                                                class="bi bi-key absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-purple-600 transition-colors"></i>
                                            <input type="password" name="password" id="password" required
                                                class="w-full pl-14 pr-14 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-purple-100 rounded-2xl font-bold text-sm transition-all @error('password') ring-2 ring-red-100 border-red-200 @enderror">
                                            <button type="button" onclick="togglePassword('password')"
                                                class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600 transition-colors">
                                                <i class="bi bi-eye" id="password-icon"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <p class="mt-1 text-[10px] font-black text-red-500 uppercase tracking-widest">
                                                {{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="space-y-2">
                                        <label for="password_confirmation"
                                            class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                            Confirmar Senha <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative group">
                                            <i
                                                class="bi bi-check-all absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-purple-600 transition-colors"></i>
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                required
                                                class="w-full pl-14 pr-14 py-4 bg-gray-50 border-transparent focus:bg-white focus:ring-4 focus:ring-purple-100 rounded-2xl font-bold text-sm transition-all">
                                            <button type="button" onclick="togglePassword('password_confirmation')"
                                                class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-600 transition-colors">
                                                <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6 bg-blue-50/50 rounded-3xl border border-blue-100 flex gap-4 items-start">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-blue-100">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-1">Dica de
                                            Segurança</h4>
                                        <p class="text-xs font-bold text-blue-700/70 leading-relaxed">
                                            O utilizador receberá um email de boas-vindas com as suas credenciais. Certifique-se
                                            de que o email inserido está correto.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Settings -->
                    <div class="space-y-8">
                        <!-- Configurações de Papel -->
                        <div class="bg-gray-900 text-white rounded-[2.5rem] shadow-xl p-10 space-y-8">
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label for="role"
                                        class="block text-[10px] font-black text-blue-400 uppercase tracking-[0.2em]">Nível de
                                        Acesso</label>
                                    <select name="role" id="role" required
                                        class="w-full px-6 py-4 bg-white/5 border-transparent focus:bg-white/10 focus:ring-4 focus:ring-blue-500/20 rounded-2xl font-bold text-sm text-white transition-all appearance-none cursor-pointer">
                                        <option value="" class="bg-gray-900">Selecione o papel</option>
                                        <option value="membro" {{ old('role') == 'membro' ? 'selected' : '' }}
                                            class="bg-gray-900">Membro</option>
                                        <option value="lider_celula" {{ old('role') == 'lider_celula' ? 'selected' : '' }}
                                            class="bg-gray-900">Líder de Célula</option>
                                        <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}
                                            class="bg-gray-900">Supervisor</option>
                                        <option value="pastor_zona" {{ old('role') == 'pastor_zona' ? 'selected' : '' }}
                                            class="bg-gray-900">Pastor de Zona</option>
                                        <option value="secretaria" {{ old('role') == 'secretaria' ? 'selected' : '' }}
                                            class="bg-gray-900">Secretária</option>
                                        <option value="comissao_obra" {{ old('role') == 'comissao_obra' ? 'selected' : '' }}
                                            class="bg-gray-900">Comissão de Obra</option>
                                        <option value="responsavel_pacote" {{ old('role') == 'responsavel_pacote' ? 'selected' : '' }} class="bg-gray-900">Resp. de Pacote</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }} class="bg-gray-900">
                                            Administrador</option>
                                    </select>
                                </div>

                                <div class="space-y-4 pt-4">
                                    <label class="block text-[10px] font-black text-blue-400 uppercase tracking-[0.2em]">Status
                                        do Perfil</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="cursor-pointer group">
                                            <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="hidden peer">
                                            <div
                                                class="w-full py-4 text-center rounded-2xl border-2 border-white/5 bg-white/5 text-[10px] font-black uppercase tracking-widest peer-checked:bg-green-600 peer-checked:border-green-500 peer-checked:text-white group-hover:bg-white/10 transition-all">
                                                Ativo
                                            </div>
                                        </label>
                                        <label class="cursor-pointer group">
                                            <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }} class="hidden peer">
                                            <div
                                                class="w-full py-4 text-center rounded-2xl border-2 border-white/5 bg-white/5 text-[10px] font-black uppercase tracking-widest peer-checked:bg-red-600 peer-checked:border-red-500 peer-checked:text-white group-hover:bg-white/10 transition-all">
                                                Inativo
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação Final -->
                        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 space-y-4">
                            <button type="submit"
                                class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all flex items-center justify-center gap-3">
                                <i class="bi bi-person-check text-lg"></i>
                                Finalizar Registo
                            </button>
                            <a href="{{ route('users.index') }}"
                                class="w-full py-5 bg-gray-50 text-gray-400 rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-red-50 hover:text-red-600 transition-all flex items-center justify-center gap-3">
                                <i class="bi bi-x-circle"></i>
                                Cancelar Operação
                            </a>
                        </div>
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

@endsection