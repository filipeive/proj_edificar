@extends('layouts.app')

@section('title', 'Criar Membro / Líder')
@section('page-title', 'Adicionar Novo')
@section('page-subtitle', 'Registar um novo membro ou líder de célula')

@section('content')
    <div class="grid grid-max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-2xl font-bold">
                    <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                    Verifique os campos destacados e tente novamente.
                </div>
                @push('scripts')
                    <script>
                        window.showError('Verifique os campos destacados e tente novamente.');
                    </script>
                @endpush
            @endif
            <form action="{{ route('members.store') }}" method="POST">
                @csrf

                <!-- Informação Contextual -->
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
                    <div class="flex items-start">
                        <i class="bi bi-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-blue-800 mb-1">Criação de Membro</h4>
                            <p class="text-sm text-blue-700">
                                Este formulário cria um novo membro com acesso ao sistema.
                                @if($userRole === 'lider_celula')
                                    O membro será adicionado à sua célula.
                                @elseif($userRole === 'supervisor')
                                    Você pode adicionar membros a qualquer célula da sua supervisão.
                                @elseif($userRole === 'pastor_zona')
                                    Você pode adicionar membros a qualquer célula da sua zona.
                                @else
                                    Você pode adicionar membros a qualquer célula.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Seleção de Célula -->
                @if($userRole === 'lider_celula' && $selectedCell)
                    <!-- Líder de célula: célula fixa -->
                    <input type="hidden" name="cell_id" value="{{ $selectedCell->id }}">
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                        <div class="flex items-center">
                            <i class="bi bi-check-circle text-green-600 text-xl mr-3"></i>
                            <div>
                                <p class="font-semibold text-green-800">Célula Selecionada</p>
                                <p class="text-sm text-green-700">{{ $selectedCell->name }}</p>
                                @if($selectedCell->supervision)
                                    <p class="text-xs text-green-600">{{ $selectedCell->supervision->name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Outros roles: podem escolher célula -->
                    <div class="mb-6">
                        <label for="cell_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-people-fill mr-2"></i>Célula <span class="text-red-500">*</span>
                        </label>
                        @if($availableCells->isEmpty())
                            <div
                                class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm font-bold">
                                <i class="bi bi-exclamation-triangle mr-2"></i>
                                Nenhuma célula disponível para o seu perfil. Contacte o administrador.
                            </div>
                        @endif
                        <select name="cell_id" id="cell_id" required
                            class="searchable-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 custom-select @error('cell_id') border-red-500 @enderror"
                            data-label="Célula">
                            <option value="">-- Selecione uma célula --</option>
                            @foreach($availableCells as $cell)
                                <option value="{{ $cell->id }}" {{ (old('cell_id', $prefill['cell_id'] ?? '')) == $cell->id ? 'selected' : '' }}>
                                    {{ $cell->name }}
                                    @if($cell->supervision)
                                        ({{ $cell->supervision->name }})
                                        @if($cell->supervision->zone)
                                            - {{ $cell->supervision->zone->name }}
                                        @endif
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('cell_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Dados Pessoais & Função -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-person-vcard mr-2"></i>Informação do Registo
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <!-- Nome -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nome Completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                value="{{ old('name', $prefill['name'] ?? '') }}" placeholder="João Silva">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cargo / Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                Cargo / Função <span class="text-red-500">*</span>
                            </label>
                            <select name="role" id="role" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 custom-select @error('role') border-red-500 @enderror">
                                @foreach($allowedRoles as $role)
                                    <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Telefone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Telefone
                            </label>
                            <input type="tel" name="phone" id="phone"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror"
                                value="{{ old('phone', $prefill['phone'] ?? '') }}" placeholder="823562000">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mt-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                            value="{{ old('email') }}" placeholder="joao@example.com">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Compromisso (Opcional) -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-handshake mr-2"></i>Compromisso (Opcional)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Pacote -->
                        <div>
                            <label for="package_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Pacote de Compromisso
                            </label>
                            <select name="package_id" id="package_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 custom-select">
                                <option value="">Nenhum (pode escolher depois)</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }} - {{ number_format($package->min_amount, 2, ',', '.') }} MT
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Valor comprometido (opcional) -->
                        <div>
                            <label for="committed_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Valor Comprometido (MT)
                            </label>
                            <input type="number" name="committed_amount" id="committed_amount" step="0.01" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                value="{{ old('committed_amount') }}" placeholder="0.00">
                            <p class="text-xs text-gray-500 mt-1">Deixe vazio para usar o valor do pacote</p>
                        </div>
                    </div>
                </div>

                <!-- Credenciais de Acesso -->
                <div class="mb-6 pb-6 border-b">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="bi bi-lock-fill mr-2"></i>Credenciais de Acesso
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Senha -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Senha <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required minlength="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                                placeholder="••••••••">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Mínimo 6 caracteres</p>
                        </div>

                        <!-- Confirmar Senha -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Senha <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                minlength="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password_confirmation') border-red-500 @enderror"
                                placeholder="••••••••">
                            @error('password_confirmation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                        <p class="text-sm text-yellow-800">
                            <i class="bi bi-exclamation-triangle mr-2"></i>
                            <strong>Importante:</strong> O membro receberá uma notificação com as credenciais de acesso.
                        </p>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-bold shadow-md">
                        <i class="bi bi-plus-circle mr-2"></i>Salvar Registo
                    </button>
                    <a href="{{ route('members.index') }}"
                        class="flex-1 bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-bold text-center shadow-md">
                        <i class="bi bi-x-circle mr-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection