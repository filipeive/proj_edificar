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
            class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-white">
            <i class="bi bi-check2-circle"></i>
            Guardar
        </button>
    </div>
@endsection

@section('content')
    <div class="w-full space-y-6">
        <!-- Header with Back Button and Quick Stats -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('users.index') }}"
                    class="group w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 text-gray-500 hover:text-orange-500 transition-all duration-300 shadow-sm">
                    <i class="bi bi-arrow-left transition-transform group-hover:-translate-x-1"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-zinc-100">Editar Utilizador</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Atualize as informações de <span
                            class="font-semibold text-orange-500">{{ $user->name }}</span></p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-orange-50 dark:bg-orange-500/5 border border-orange-100 dark:border-orange-500/10 rounded-xl text-orange-700 dark:text-orange-400 text-sm font-medium">
                    ID: #{{ $user->id }}
                </div>
                <div
                    class="px-4 py-2 {{ $user->is_active ? 'bg-emerald-50 dark:bg-emerald-500/5 border-emerald-100 dark:border-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-red-50 dark:bg-red-500/5 border-red-100 dark:border-red-500/10 text-red-700 dark:text-red-400' }} border rounded-xl text-sm font-medium">
                    {{ $user->is_active ? 'Conta Ativa' : 'Conta Inativa' }}
                </div>
            </div>
        </div>

        <form id="user-edit-form" action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Tabs Navigation -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-gray-200 dark:border-zinc-800 p-2 flex gap-2" x-data="{ activeTab: 'general' }">
                <button type="button" @click="activeTab = 'general'; $dispatch('tab-change', 'general')" 
                    :class="activeTab === 'general' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-500 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50'"
                    class="flex-1 px-6 py-3 rounded-xl font-bold text-sm transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="bi bi-person-lines-fill"></i>
                    Dados Gerais
                </button>
                <button type="button" @click="activeTab = 'permissions'; $dispatch('tab-change', 'permissions')"
                    :class="activeTab === 'permissions' ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-500 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-800/50'"
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
                            <x-card title="Informações Pessoais" subtitle="Dados de identificação e contacto do utilizador">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Nome -->
                                    <x-text-input-premium 
                                        label="Nome Completo" 
                                        name="name" 
                                        value="{{ old('name', $user->name) }}" 
                                        placeholder="Ex: João Silva" 
                                        icon="bi bi-person" 
                                        required 
                                    />

                                    <!-- Email -->
                                    <x-text-input-premium 
                                        label="Endereço de Email" 
                                        name="email" 
                                        type="email"
                                        value="{{ old('email', $user->email) }}" 
                                        placeholder="joao@exemplo.com" 
                                        icon="bi bi-envelope" 
                                        required 
                                    />

                                    <!-- Telefone -->
                                    <x-text-input-premium 
                                        label="Telefone" 
                                        name="phone" 
                                        value="{{ old('phone', $user->phone) }}" 
                                        placeholder="+258 8x xxx xxxx" 
                                        icon="bi bi-telephone" 
                                    />

                                    <!-- Status -->
                                    <div class="space-y-2">
                                        <label class="block mb-2 text-xs font-black uppercase tracking-[0.08em] text-zinc-500 dark:text-zinc-400">
                                            Estado da Conta
                                        </label>
                                        <div class="flex gap-4">
                                            <label class="relative flex-1 cursor-pointer">
                                                <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active) == '1' ? 'checked' : '' }} class="peer sr-only">
                                                <div class="p-3 text-center rounded-xl border-2 border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-900/20 text-gray-500 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-500/10 peer-checked:text-emerald-700 dark:peer-checked:text-emerald-400 transition-all duration-200">
                                                    <span class="text-xs font-black uppercase tracking-wider">Ativo</span>
                                                </div>
                                            </label>
                                            <label class="relative flex-1 cursor-pointer">
                                                <input type="radio" name="is_active" value="0" {{ old('is_active', $user->is_active) == '0' ? 'checked' : '' }} class="peer sr-only">
                                                <div class="p-3 text-center rounded-xl border-2 border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-900/20 text-gray-500 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-500/10 peer-checked:text-red-700 dark:peer-checked:text-red-400 transition-all duration-200">
                                                    <span class="text-xs font-black uppercase tracking-wider">Inativo</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </x-card>

                            <!-- Role and Cell Assignment Section -->
                            <x-card title="Atribuição de Papel & Localização" subtitle="Configure o nível de privilégio e a célula eclesiástica">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Role Selection -->
                                    <div class="space-y-2">
                                        <label for="role" class="block mb-2 text-xs font-black uppercase tracking-[0.08em] text-zinc-500 dark:text-zinc-400">
                                            Papel no Sistema
                                        </label>
                                        <div class="relative">
                                            <select name="role" id="role" required
                                                class="w-full px-4 py-3 bg-gray-50/50 dark:bg-zinc-900/20 border border-gray-200 dark:border-zinc-800 rounded-2xl font-bold text-sm text-zinc-900 dark:text-zinc-100 transition-all appearance-none cursor-pointer custom-select @error('role') border-red-500 ring-red-500/10 @enderror"
                                                {{ (in_array($user->role, ['admin', 'super_admin'], true) && !($canManageAdminRoles ?? false)) ? 'disabled' : '' }}>
                                                <option value="">Selecione o papel</option>
                                                <option value="membro" {{ old('role', $user->role) == 'membro' ? 'selected' : '' }}>Membro</option>
                                                <option value="lider_celula" {{ old('role', $user->role) == 'lider_celula' ? 'selected' : '' }}>Líder de Célula</option>
                                                <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                                <option value="pastor_zona" {{ old('role', $user->role) == 'pastor_zona' ? 'selected' : '' }}>Pastor de Zona</option>
                                                <option value="secretaria" {{ old('role', $user->role) == 'secretaria' ? 'selected' : '' }}>Secretaria</option>
                                                <option value="comissao_obra" {{ old('role', $user->role) == 'comissao_obra' ? 'selected' : '' }}>Comissão de Obra</option>
                                                <option value="responsavel_pacote" {{ old('role', $user->role) == 'responsavel_pacote' ? 'selected' : '' }}>Responsável de Pacote</option>
                                                <option value="tesouraria" {{ old('role', $user->role) == 'tesouraria' ? 'selected' : '' }}>Tesouraria</option>
                                                <option value="pastor" {{ old('role', $user->role) == 'pastor' ? 'selected' : '' }}>Pastor</option>
                                                <option value="pastor_senior" {{ old('role', $user->role) == 'pastor_senior' ? 'selected' : '' }}>Pastor Senior</option>
                                                <option value="administracao" {{ old('role', $user->role) == 'administracao' ? 'selected' : '' }}>Administração</option>
                                                @if($canManageAdminRoles ?? false)
                                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                                    <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                                @endif
                                            </select>
                                        </div>
                                        @if(in_array($user->role, ['admin', 'super_admin'], true) && !($canManageAdminRoles ?? false))
                                            <input type="hidden" name="role" value="{{ $user->role }}">
                                            <p class="mt-1.5 text-xs text-amber-600 font-bold">Conta privilegiada: apenas super admin pode alterar este papel.</p>
                                        @endif
                                        @error('role')
                                            <p class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-1.5 animate-pulse">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Cell Selection -->
                                    <div class="space-y-2">
                                        <label for="cell_id" class="block mb-2 text-xs font-black uppercase tracking-[0.08em] text-zinc-500 dark:text-zinc-400">
                                            Célula Associada
                                        </label>
                                        <div class="relative">
                                            <select name="cell_id" id="cell_id"
                                                class="searchable-select w-full px-4 py-3 bg-gray-50/50 dark:bg-zinc-900/20 border border-gray-200 dark:border-zinc-800 rounded-2xl font-bold text-sm text-zinc-900 dark:text-zinc-100 transition-all appearance-none custom-select @error('cell_id') border-red-500 ring-red-500/10 @enderror"
                                                data-label="Célula">
                                                <option value="">Sem célula associada</option>
                                                @foreach($cells as $cell)
                                                    <option value="{{ $cell->id }}" {{ old('cell_id', $user->cell_id) == $cell->id ? 'selected' : '' }}>
                                                        {{ $cell->display_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('cell_id')
                                            <p class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-1.5 animate-pulse">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </x-card>
                        </div>

                        <!-- Permissions Section -->
                        <div x-show="activeTab === 'permissions'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                            <div class="bg-amber-50 dark:bg-amber-500/5 rounded-3xl p-6 border border-amber-100 dark:border-amber-500/10 mb-6">
                                <div class="flex gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 flex-shrink-0">
                                        <i class="bi bi-info-circle-fill text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-amber-905 dark:text-amber-400 uppercase tracking-widest mb-1">Como funcionam as permissões</h4>
                                        <p class="text-xs text-amber-800 dark:text-zinc-400 leading-relaxed font-semibold">
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
                                    <x-card title="{{ $category }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($permissions as $key => $data)
                                                <div class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 dark:border-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-800/30 transition-colors group">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                                            <i class="bi {{ $data['icon'] }} text-lg"></i>
                                                        </div>
                                                        <span class="text-sm font-bold text-zinc-700 dark:text-zinc-300">{{ $data['label'] }}</span>
                                                    </div>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="hidden" name="menu_permissions[{{ $key }}]" value="0">
                                                        <input type="checkbox" name="menu_permissions[{{ $key }}]" value="1" class="sr-only peer"
                                                            @if(old("menu_permissions.$key", $user->menu_permissions[$key] ?? $user->hasPermission($key))) checked @endif>
                                                        <div class="w-11 h-6 bg-gray-200 dark:bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </x-card>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Info/Actions -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- System Info Card -->
                        <x-card title="Informações do Sistema">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-zinc-900/50 rounded-2xl">
                                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Criado em</p>
                                        <p class="text-xs font-black text-zinc-950 dark:text-zinc-200">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-zinc-900/50 rounded-2xl">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Última atualização</p>
                                        <p class="text-xs font-black text-zinc-950 dark:text-zinc-200">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-zinc-900/50 rounded-2xl">
                                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Último acesso</p>
                                        <p class="text-xs font-black text-zinc-950 dark:text-zinc-200">
                                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca acedeu' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-500/5 rounded-2xl border border-amber-100 dark:border-amber-500/10">
                                <div class="flex gap-3">
                                    <i class="bi bi-shield-lock text-amber-600 dark:text-amber-400"></i>
                                    <div class="text-xs text-amber-800 dark:text-zinc-400 leading-relaxed font-semibold">
                                        <p class="font-bold mb-1 uppercase">Segurança de Dados</p>
                                        Para alterar a senha deste utilizador, por favor utilize a funcionalidade dedicada na página de detalhes ou o acesso direto por email.
                                    </div>
                                </div>
                            </div>
                        </x-card>

                        <!-- Submit Button Area -->
                        <x-card compact="true">
                            <div class="space-y-3 p-1">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-semibold leading-relaxed">
                                    Certifique-se de que os dados estão corretos antes de guardar as alterações.
                                </p>
                                <x-button type="submit" variant="primary" size="md" icon="bi bi-check2-circle" class="w-full">
                                    Guardar Alterações
                                </x-button>
                                <a href="{{ route('users.show', $user) }}"
                                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-300 text-sm">
                                    <i class="bi bi-eye"></i>
                                    Ver Detalhes
                                </a>
                            </div>
                        </x-card>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
