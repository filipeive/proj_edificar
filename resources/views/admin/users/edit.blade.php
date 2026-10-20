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
    <div class="space-y-5">
        <!-- Header with Back Button and Quick Stats -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div class="flex items-start gap-3">
                <a href="{{ route('users.index') }}"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-orange-600 transition shadow-sm mt-0.5">
                    <i class="bi bi-arrow-left text-sm"></i>
                </a>
                <div>
                    <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Gestão de Utilizadores</p>
                    <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Editar Utilizador</h1>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Atualize as informações de <span class="font-bold text-orange-600">{{ $user->name }}</span></p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="px-3 py-1.5 bg-orange-50 border border-orange-200 rounded-lg text-orange-700 text-xs font-bold">
                    ID: #{{ $user->id }}
                </div>
                <div
                    class="px-3 py-1.5 {{ $user->is_active ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' }} border rounded-lg text-xs font-bold">
                    {{ $user->is_active ? 'Conta Ativa' : 'Conta Inativa' }}
                </div>
            </div>
        </header>

        <form id="user-edit-form" action="{{ route('users.update', $user) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Tabs Navigation -->
            <div class="rounded-xl border border-gray-200 bg-white p-1.5 shadow-sm flex gap-1.5" x-data="{ activeTab: 'general' }">
                <button type="button" @click="activeTab = 'general'; $dispatch('tab-change', 'general')" 
                    :class="activeTab === 'general' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 px-4 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
                    <i class="bi bi-person-lines-fill"></i>
                    Dados Gerais
                </button>
                <button type="button" @click="activeTab = 'permissions'; $dispatch('tab-change', 'permissions')"
                    :class="activeTab === 'permissions' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 px-4 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
                    <i class="bi bi-shield-lock-fill"></i>
                    Menu & Permissões
                </button>
            </div>

            <div x-data="{ activeTab: 'general' }" @tab-change.window="activeTab = $event.detail">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <!-- Main Content Area -->
                    <div class="lg:col-span-2 space-y-5">
                        <!-- Profile Section -->
                        <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                            <x-card title="Informações Pessoais" subtitle="Dados de identificação e contacto do utilizador">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                    <div class="space-y-1.5">
                                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500">
                                            Estado da Conta
                                        </label>
                                        <div class="flex gap-3">
                                            <label class="relative flex-1 cursor-pointer">
                                                <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active) == '1' ? 'checked' : '' }} class="peer sr-only">
                                                <div class="py-2.5 px-3 text-center rounded-lg border border-gray-200 bg-gray-50 text-gray-500 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-all">
                                                    <span class="text-xs font-black uppercase tracking-wider">Ativo</span>
                                                </div>
                                            </label>
                                            <label class="relative flex-1 cursor-pointer">
                                                <input type="radio" name="is_active" value="0" {{ old('is_active', $user->is_active) == '0' ? 'checked' : '' }} class="peer sr-only">
                                                <div class="py-2.5 px-3 text-center rounded-lg border border-gray-200 bg-gray-50 text-gray-500 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all">
                                                    <span class="text-xs font-black uppercase tracking-wider">Inativo</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </x-card>

                            <!-- Role and Cell Assignment Section -->
                            <x-card title="Atribuição de Papel & Localização" subtitle="Configure o nível de privilégio e a célula eclesiástica">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Role Selection -->
                                    <div class="space-y-1.5">
                                        <label for="role" class="block text-[10px] font-black uppercase tracking-wider text-gray-500">
                                            Papel no Sistema
                                        </label>
                                        <div class="relative">
                                            <select name="role" id="role" required
                                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 focus:border-orange-500 focus:ring-orange-500 transition-all custom-select @error('role') border-red-500 @enderror"
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
                                            <p class="mt-1 text-xs text-amber-600 font-bold">Conta privilegiada: apenas super admin pode alterar este papel.</p>
                                        @endif
                                        @error('role')
                                            <p class="mt-1 text-xs font-bold text-red-600 flex items-center gap-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Cell Selection -->
                                    <div class="space-y-1.5">
                                        <label for="cell_id" class="block text-[10px] font-black uppercase tracking-wider text-gray-500">
                                            Célula Associada
                                        </label>
                                        <div class="relative">
                                            <select name="cell_id" id="cell_id"
                                                class="searchable-select w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-gray-900 focus:border-orange-500 focus:ring-orange-500 transition-all custom-select @error('cell_id') border-red-500 @enderror"
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
                                            <p class="mt-1 text-xs font-bold text-red-600 flex items-center gap-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </x-card>
                        </div>

                        <!-- Permissions Section -->
                        <div x-show="activeTab === 'permissions'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                            <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
                                <div class="flex gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                                        <i class="bi bi-info-circle-fill text-base"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-amber-900 uppercase tracking-wider mb-0.5">Como funcionam as permissões</h4>
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

                            <div class="space-y-4">
                                @foreach($categories as $category => $permissions)
                                    <x-card title="{{ $category }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($permissions as $key => $data)
                                                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors group">
                                                    <div class="flex items-center gap-2.5">
                                                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                                                            <i class="bi {{ $data['icon'] }} text-sm"></i>
                                                        </div>
                                                        <span class="text-xs sm:text-sm font-bold text-gray-800">{{ $data['label'] }}</span>
                                                    </div>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="hidden" name="menu_permissions[{{ $key }}]" value="0">
                                                        <input type="checkbox" name="menu_permissions[{{ $key }}]" value="1" class="sr-only peer"
                                                            @if(old("menu_permissions.$key", $user->menu_permissions[$key] ?? $user->hasPermission($key))) checked @endif>
                                                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-orange-600"></div>
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
                    <div class="lg:col-span-1 space-y-5">
                        <!-- System Info Card -->
                        <x-card title="Informações do Sistema">
                            <div class="space-y-2.5">
                                <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600">
                                        <i class="bi bi-calendar-event text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Criado em</p>
                                        <p class="text-xs font-black text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                                        <i class="bi bi-clock-history text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Última atualização</p>
                                        <p class="text-xs font-black text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                                        <i class="bi bi-box-arrow-in-right text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Último acesso</p>
                                        <p class="text-xs font-black text-gray-900">
                                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca acedeu' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 p-3 bg-amber-50 rounded-xl border border-amber-200">
                                <div class="flex gap-2.5">
                                    <i class="bi bi-shield-lock text-amber-600"></i>
                                    <div class="text-xs text-amber-800 leading-relaxed">
                                        <p class="font-bold mb-0.5 uppercase">Segurança de Dados</p>
                                        Para alterar a senha deste utilizador, por favor utilize a funcionalidade dedicada na página de detalhes ou o acesso direto por email.
                                    </div>
                                </div>
                            </div>
                        </x-card>

                        <!-- Submit Button Area -->
                        <x-card compact="true">
                            <div class="space-y-3">
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    Certifique-se de que os dados estão corretos antes de guardar as alterações.
                                </p>
                                <x-button type="submit" variant="primary" size="md" icon="bi bi-check2-circle" class="w-full">
                                    Guardar Alterações
                                </x-button>
                                <a href="{{ route('users.show', $user) }}"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition text-xs uppercase tracking-wider">
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
