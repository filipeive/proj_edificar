@extends('layouts.app')

@section('title', 'Editar Utilizador - Portal Life Church')
@section('page-title', 'Editar Utilizador')
@section('page-subtitle', 'Atualize as informações do membro ou líder do sistema')

@section('header-actions')
    <div class="flex items-center gap-2 md:hidden">
        <a href="{{ route('users.show', $user) }}"
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 transition hover:bg-gray-50"
            title="Voltar">
            <i class="bi bi-arrow-left"></i>
        </a>
        <button type="submit" form="user-edit-form"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-white">
            <i class="bi bi-check2-circle"></i>
            Guardar
        </button>
    </div>
@endsection

@section('content')
    <div class="w-full">
        <!-- Header with Back Button and Quick Stats -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('users.index') }}"
                    class="group w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-200 transition-all duration-300 shadow-sm">
                    <i class="bi bi-arrow-left transition-transform group-hover:-translate-x-1"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-gray-900">Editar Utilizador</h1>
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

        <form id="user-edit-form" action="{{ route('users.update', $user) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Tabs Navigation -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-2 mb-6 flex gap-2" x-data="{ activeTab: 'general' }">
                <button type="button" @click="activeTab = 'general'; $dispatch('tab-change', 'general')" 
                    :class="activeTab === 'general' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-500 hover:bg-gray-50'"
                    class="flex-1 px-6 py-3 rounded-xl font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="bi bi-person-lines-fill"></i>
                    Dados Gerais
                </button>
                <button type="button" @click="activeTab = 'permissions'; $dispatch('tab-change', 'permissions')"
                    :class="activeTab === 'permissions' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-500 hover:bg-gray-50'"
                    class="flex-1 px-6 py-3 rounded-xl font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="bi bi-shield-lock-fill"></i>
                    Menu & Permissões
                </button>
            </div>

            <div x-data="{ activeTab: 'general' }" @tab-change.window="activeTab = $event.detail">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content Area -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Profile Section -->
                        <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="bg-gray-50 border-b border-gray-100 p-4">
                                    <h2 class="text-base font-black text-gray-800 flex items-center gap-2">
                                        <i class="bi bi-person-badge"></i>
                                        Informações Pessoais
                                    </h2>
                                </div>

                                <div class="p-5 md:p-6 space-y-6">
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
                            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="bg-gray-50 border-b border-gray-100 p-4">
                                    <h2 class="text-base font-black text-gray-800 flex items-center gap-2">
                                        <i class="bi bi-diagram-3"></i>
                                        Atribuição de Papel e Localização
                                    </h2>
                                </div>

                                <div class="p-5 md:p-6 space-y-6">
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
                                                    {{ (in_array($user->role, ['admin', 'super_admin'], true) && !($canManageAdminRoles ?? false)) ? 'disabled' : '' }}>
                                                    <option value="">Selecione o papel</option>
                                                    <option value="membro" {{ old('role', $user->role) == 'membro' ? 'selected' : '' }}>Membro</option>
                                                    <option value="lider_celula" {{ old('role', $user->role) == 'lider_celula' ? 'selected' : '' }}>Líder de Célula</option>
                                                    <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                                    <option value="pastor_zona" {{ old('role', $user->role) == 'pastor_zona' ? 'selected' : '' }}>Pastor de Zona</option>
                                                    <option value="secretaria" {{ old('role', $user->role) == 'secretaria' ? 'selected' : '' }}>Secretaria</option>
                                                    <option value="comissao_obra" {{ old('role', $user->role) == 'comissao_obra' ? 'selected' : '' }}>Comissão de Obra</option>
                                                    <option value="responsavel_pacote" {{ old('role', $user->role) == 'responsavel_pacote' ? 'selected' : '' }}>Responsável de
                                                        Pacote</option>
                                                    <option value="tesouraria" {{ old('role', $user->role) == 'tesouraria' ? 'selected' : '' }}>Tesouraria</option>
                                                    <option value="pastor" {{ old('role', $user->role) == 'pastor' ? 'selected' : '' }}>Pastor</option>
                                                    <option value="pastor_senior" {{ old('role', $user->role) == 'pastor_senior' ? 'selected' : '' }}>Pastor Senior</option>
                                                    <option value="administracao" {{ old('role', $user->role) == 'administracao' ? 'selected' : '' }}>Administração</option>
                                                    @if($canManageAdminRoles ?? false)
                                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                                            Administrador</option>
                                                        <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>
                                                            Super Admin</option>
                                                    @endif
                                                </select>
                                                <div
                                                    class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                                    <i class="bi bi-chevron-down"></i>
                                                </div>
                                            </div>
                                            @if(in_array($user->role, ['admin', 'super_admin'], true) && !($canManageAdminRoles ?? false))
                                                <input type="hidden" name="role" value="{{ $user->role }}">
                                                <p class="mt-1 text-xs text-amber-600 font-medium">Conta privilegiada: apenas super admin pode alterar este papel.</p>
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
                                                            {{ $cell->display_name }}
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

                        <!-- Permissions Section -->
                        <div x-show="activeTab === 'permissions'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                            <div class="bg-amber-50 rounded-3xl p-6 border border-amber-100 mb-6">
                                <div class="flex gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                                        <i class="bi bi-info-circle-fill text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-amber-900 uppercase tracking-widest mb-1">Como funcionam as permissões</h4>
                                        <p class="text-xs text-amber-800 leading-relaxed">
                                            Por padrão, o utilizador tem acesso baseado no seu <strong>Papel no Sistema</strong>. 
                                            Ao ativar ou desativar um item abaixo, você está criando uma <strong>exceção personalizada</strong> para este utilizador específico. 
                                            Se um item estiver desativado mas o Papel permitir acesso, o utilizador será bloqueado.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @php
                                $categories = [
                                    'Dashboards' => [
                                        'dashboard_edificar' => ['label' => 'Painel Edificar', 'icon' => 'bi-graph-up-arrow'],
                                        'dashboard_packages' => ['label' => 'Painel do Pacote', 'icon' => 'bi-speedometer2'],
                                    ],
                                    'Operação Eclesiástica' => [
                                        'menu_services' => ['label' => 'Cultos', 'icon' => 'bi-journal-bookmark-fill'],
                                        'menu_events' => ['label' => 'Eventos', 'icon' => 'bi-calendar-check-fill'],
                                        'menu_weddings' => ['label' => 'Casamentos', 'icon' => 'bi-heart-fill'],
                                        'menu_visitors' => ['label' => 'Visitantes', 'icon' => 'bi-person-plus-fill'],
                                        'menu_courses' => ['label' => 'Escola Ministerial', 'icon' => 'bi-mortarboard-fill'],
                                        'menu_quarterly_reports' => ['label' => 'Relatórios Trimestrais', 'icon' => 'bi-file-earmark-bar-graph-fill'],
                                        'menu_inventory' => ['label' => 'Inventário', 'icon' => 'bi-box-seam-fill'],
                                    ],
                                    'Células & Discipulado' => [
                                        'menu_cell_meetings' => ['label' => 'Encontros de Célula', 'icon' => 'bi-people-fill'],
                                        'menu_members' => ['label' => 'Gestão de Membros', 'icon' => 'bi-person-lines-fill'],
                                        'menu_zones' => ['label' => 'Zonas Pastorais', 'icon' => 'bi-geo-fill'],
                                        'menu_supervisions' => ['label' => 'Supervisões', 'icon' => 'bi-diagram-2-fill'],
                                        'menu_cells' => ['label' => 'Células', 'icon' => 'bi-diagram-3-fill'],
                                    ],
                                    'Gestão Financeira' => [
                                        'menu_packages' => ['label' => 'Pacotes de Compromisso', 'icon' => 'bi-box-seam-fill'],
                                        'menu_contributions_all' => ['label' => 'Todas as Contribuições', 'icon' => 'bi-cash-stack'],
                                        'menu_finance' => ['label' => 'Módulo Financeiro (Despesas/Requisições)', 'icon' => 'bi-pie-chart-fill'],
                                    ],
                                    'Sistema & Relatórios' => [
                                        'menu_stats' => ['label' => 'Estatísticas & Relatórios', 'icon' => 'bi-bar-chart-line-fill'],
                                        'menu_users' => ['label' => 'Gestão de Utilizadores', 'icon' => 'bi-person-lock'],
                                        'menu_settings' => ['label' => 'Configurações do Sistema', 'icon' => 'bi-gear-fill'],
                                    ],
                                ];
                            @endphp

                            <div class="space-y-6">
                                @foreach($categories as $category => $permissions)
                                    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
                                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $category }}</h3>
                                        </div>
                                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($permissions as $key => $data)
                                                <div class="flex items-center justify-between p-4 rounded-2xl border border-gray-50 hover:bg-gray-50 transition-colors group">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                                            <i class="bi {{ $data['icon'] }} text-lg"></i>
                                                        </div>
                                                        <span class="text-sm font-bold text-gray-700">{{ $data['label'] }}</span>
                                                    </div>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="hidden" name="menu_permissions[{{ $key }}]" value="0">
                                                        <input type="checkbox" name="menu_permissions[{{ $key }}]" value="1" class="sr-only peer"
                                                            @if(old("menu_permissions.$key", $user->menu_permissions[$key] ?? $user->hasPermission($key))) checked @endif>
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                <!-- Sidebar Info/Actions -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- System Info Card -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-5 space-y-5">
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
                    <div class="bg-white rounded-3xl border border-gray-200 p-5 shadow-sm space-y-4">
                        <p class="text-sm text-gray-500 font-medium">
                            Certifique-se de que os dados estão corretos antes de guardar as alterações.
                        </p>
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 transition-all duration-300">
                            <i class="bi bi-check2-circle text-xl"></i>
                            Guardar Alterações
                        </button>
                        <a href="{{ route('users.show', $user) }}"
                            class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 font-black rounded-2xl hover:bg-gray-200 transition-all duration-300">
                            <i class="bi bi-eye"></i>
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
