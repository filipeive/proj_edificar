@extends('layouts.app')

@section('title', 'Editar Utilizador - Portal Life Church')
@section('page-title', 'Editar Utilizador')
@section('page-subtitle', 'Atualize as informações do membro ou líder do sistema')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header with Back Button and Quick Stats -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('users.index') }}"
                    class="group w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-200 transition-all duration-300 shadow-sm">
                    <i class="bi bi-arrow-left transition-transform group-hover:-translate-x-1"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Utilizador</h1>
                    <p class="text-sm text-gray-500">Atualize as informações de <span
                            class="font-semibold text-blue-600">{{ $user->name }}</span></p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-blue-50 border border-blue-100 rounded-xl text-blue-700 text-sm font-medium">
                    ID: #{{ $user->id }}
                </div>
                <div
                    class="px-4 py-2 {{ $user->is_active ? 'bg-green-50 border-green-100 text-green-700' : 'bg-red-50 border-red-100 text-red-700' }} border rounded-xl text-sm font-medium">
                    {{ $user->is_active ? 'Conta Ativa' : 'Conta Inativa' }}
                </div>
            </div>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content Area -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Profile Section -->
                    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
                            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                                <i class="bi bi-person-badge"></i>
                                Informações Pessoais
                            </h2>
                        </div>

                        <div class="p-6 md:p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nome -->
                                <div class="space-y-2">
                                    <label for="name" class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <i class="bi bi-person text-blue-500"></i>
                                        Nome Completo
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                        class="w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 @error('name') border-red-500 ring-red-500/10 @enderror"
                                        placeholder="Ex: João Silva">
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="space-y-2">
                                    <label for="email" class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <i class="bi bi-envelope text-blue-500"></i>
                                        Endereço de Email
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                        required
                                        class="w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 @error('email') border-red-500 ring-red-500/10 @enderror"
                                        placeholder="joao@exemplo.com">
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Telefone -->
                                <div class="space-y-2">
                                    <label for="phone" class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <i class="bi bi-telephone text-blue-500"></i>
                                        Telefone
                                    </label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                        class="w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 @error('phone') border-red-500 ring-red-500/10 @enderror"
                                        placeholder="+258 8x xxx xxxx">
                                    @error('phone')
                                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-3">
                                        <i class="bi bi-toggle-on text-blue-500"></i>
                                        Estado da Conta
                                    </label>
                                    <div class="flex gap-4">
                                        <label class="relative flex-1 cursor-pointer">
                                            <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active) == '1' ? 'checked' : '' }} class="peer sr-only">
                                            <div
                                                class="p-3 text-center rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-600 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 transition-all duration-200">
                                                <span class="text-sm font-bold">Ativo</span>
                                            </div>
                                        </label>
                                        <label class="relative flex-1 cursor-pointer">
                                            <input type="radio" name="is_active" value="0" {{ old('is_active', $user->is_active) == '0' ? 'checked' : '' }} class="peer sr-only">
                                            <div
                                                class="p-3 text-center rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-600 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all duration-200">
                                                <span class="text-sm font-bold">Inativo</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role and Cell Assignment Section -->
                    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
                            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                                <i class="bi bi-diagram-3"></i>
                                Atribuição de Papel e Localização
                            </h2>
                        </div>

                        <div class="p-6 md:p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Role Selection -->
                                <div class="space-y-2">
                                    <label for="role" class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <i class="bi bi-shield-check text-blue-500"></i>
                                        Papel no Sistema
                                    </label>
                                    <div class="relative">
                                        <select name="role" id="role" required
                                            class="w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 appearance-none custom-select @error('role') border-red-500 ring-red-500/10 @enderror"
                                            {{ $user->role === 'admin' ? 'disabled' : '' }}>
                                            <option value="">Selecione o papel</option>
                                            <option value="membro" {{ old('role', $user->role) == 'membro' ? 'selected' : '' }}>Membro</option>
                                            <option value="lider_celula" {{ old('role', $user->role) == 'lider_celula' ? 'selected' : '' }}>Líder de Célula</option>
                                            <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                            <option value="pastor_zona" {{ old('role', $user->role) == 'pastor_zona' ? 'selected' : '' }}>Pastor de Zona</option>
                                            <option value="secretaria" {{ old('role', $user->role) == 'secretaria' ? 'selected' : '' }}>Secretaria</option>
                                            <option value="comissao_obra" {{ old('role', $user->role) == 'comissao_obra' ? 'selected' : '' }}>Comissão de Obra</option>
                                            <option value="responsavel_pacote" {{ old('role', $user->role) == 'responsavel_pacote' ? 'selected' : '' }}>Responsável de
                                                Pacote</option>
                                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                                Administrador</option>
                                        </select>
                                        <div
                                            class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                    </div>
                                    @if($user->role === 'admin')
                                        <input type="hidden" name="role" value="admin">
                                        <p class="mt-1 text-xs text-amber-600 font-medium">O papel de administrador não pode ser
                                            alterado por segurança.</p>
                                    @endif
                                    @error('role')
                                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Cell Selection -->
                                <div class="space-y-2">
                                    <label for="cell_id"
                                        class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <i class="bi bi-house-door text-blue-500"></i>
                                        Célula Associada
                                    </label>
                                    <div class="relative">
                                        <select name="cell_id" id="cell_id"
                                            class="searchable-select w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 appearance-none custom-select @error('cell_id') border-red-500 ring-red-500/10 @enderror"
                                            data-label="Célula">
                                            <option value="">Sem célula associada</option>
                                            @foreach($cells as $cell)
                                                <option value="{{ $cell->id }}" {{ old('cell_id', $user->cell_id) == $cell->id ? 'selected' : '' }}>
                                                    {{ $cell->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                            <i class="bi bi-chevron-down"></i>
                                        </div>
                                    </div>
                                    @error('cell_id')
                                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info/Actions -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- System Info Card -->
                    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-50 pb-4">Informações do Sistema</h3>

                        <div class="space-y-4">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                                <div
                                    class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Criado em</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Última atualização
                                    </p>
                                    <p class="text-sm font-bold text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                                <div
                                    class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Último acesso</p>
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca acedeu' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                            <div class="flex gap-3">
                                <i class="bi bi-shield-lock text-amber-600"></i>
                                <div class="text-xs text-amber-800 leading-relaxed font-medium">
                                    <p class="font-bold mb-1 uppercase">Segurança de Dados</p>
                                    Para alterar a senha deste utilizador, por favor utilize a funcionalidade dedicada na
                                    página de detalhes ou o acesso direto por email.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button Area -->
                    <div class="bg-blue-600 rounded-3xl p-6 shadow-xl shadow-blue-500/30 text-white space-y-4">
                        <p class="text-sm text-blue-100 font-medium">
                            Certifique-se de que os dados estão corretos antes de guardar as alterações.
                        </p>
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-white text-blue-600 font-bold rounded-2xl hover:bg-blue-50 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                            <i class="bi bi-check2-circle text-xl"></i>
                            Guardar Alterações
                        </button>
                        <a href="{{ route('users.show', $user) }}"
                            class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-blue-700/50 text-white font-semibold rounded-2xl hover:bg-blue-700 transition-all duration-300">
                            <i class="bi bi-eye"></i>
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection