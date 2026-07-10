@extends('layouts.app')

@section('title', 'Configurações do Sistema')
@section('page-title', 'Configurações')
@section('page-subtitle', 'Gerir configurações gerais do sistema')

@section('content')
    <div class="space-y-6" x-data="{ activeTab: '{{ $group }}' }">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black tracking-tight text-gray-900">Centro de Configurações</h2>
                <p class="text-sm text-gray-500">Branding, região, email, permissões e sistema.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('settings.backup') }}"
                    class="inline-flex items-center justify-center bg-gray-900 text-white px-5 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-800 transition-all">
                    <i class="bi bi-database-down mr-2"></i> Backup
                </a>
                <form action="{{ route('settings.reset') }}" method="POST" onsubmit="return confirm('Tem certeza? Isso irá restaurar os padrões.');">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center bg-red-600 text-white px-5 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-700 transition-all">
                        <i class="bi bi-arrow-counterclockwise mr-2"></i> Restaurar
                    </button>
                </form>
            </div>
        </div>
        <!-- Tabs -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-2">
            <div class="flex gap-2 overflow-x-auto whitespace-nowrap custom-scrollbar">
                @foreach($groups as $groupName)
                    <button @click="activeTab = '{{ $groupName }}'" type="button"
                        :class="activeTab === '{{ $groupName }}' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-600 hover:bg-gray-100'"
                        class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 shrink-0">
                        {{ ucfirst($groupName === 'branding' ? 'Marca e Design' : ($groupName === 'permissions' ? 'Acessos & Permissões' : $groupName)) }}
                    </button>
                @endforeach
                <button @click="activeTab = 'backup'" type="button"
                    :class="activeTab === 'backup' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                    class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all shrink-0">
                    Backup
                </button>
            </div>
        </div>

        <form action="{{ route('settings.update') }}" method="POST"
            class="bg-white rounded-3xl shadow-sm border border-gray-200 p-6 md:p-8">
            @csrf
            <input type="hidden" name="active_tab" x-model="activeTab">


            <!-- General Settings -->
            <div x-show="activeTab === 'general'" x-transition>
                <h3 class="text-2xl font-black text-gray-900 mb-6">Informações Gerais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nome da Igreja</label>
                        <input type="text" name="settings[church.name]"
                            value="{{ $settings['church.name']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Congregação / Filial</label>
                        <input type="text" name="settings[church.congregation]"
                            value="{{ $settings['church.congregation']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <input type="email" name="settings[church.email]"
                            value="{{ $settings['church.email']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Descrição</label>
                        <textarea name="settings[church.description]" rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">{{ $settings['church.description']['value'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Telefone</label>
                        <input type="text" name="settings[church.phone]"
                            value="{{ $settings['church.phone']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Website</label>
                        <input type="url" name="settings[church.website]"
                            value="{{ $settings['church.website']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Endereço</label>
                        <input type="text" name="settings[church.address]"
                            value="{{ $settings['church.address']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Branding Settings -->
            <div x-show="activeTab === 'branding'" x-transition>
                <h3 class="text-2xl font-black text-gray-900 mb-6">Personalização Visual</h3>
                <div class="space-y-6">
                    <!-- Logo Upload -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Logo Principal</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center">
                                <img src="{{ isset($settings['branding.logo_primary']['value']) ? asset($settings['branding.logo_primary']['value']) : asset('images/logo-white-orange.png') }}"
                                    alt="Logo Principal" class="h-16 mx-auto mb-2">
                                <button type="button" onclick="document.getElementById('logo-primary').click()"
                                    class="text-sm text-blue-600 font-bold">Alterar</button>
                                <input type="file" name="logo" id="logo-primary" class="hidden" accept="image/*"
                                    form="logoPrimaryForm" onchange="this.form.submit()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Logo Secundário</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center">
                                <img src="{{ isset($settings['branding.logo_secondary']['value']) ? asset($settings['branding.logo_secondary']['value']) : asset('images/logo.png') }}"
                                    alt="Logo Secundário" class="h-16 mx-auto mb-2">
                                <button type="button" onclick="document.getElementById('logo-secondary').click()"
                                    class="text-sm text-blue-600 font-bold">Alterar</button>
                                <input type="file" name="logo" id="logo-secondary" class="hidden" accept="image/*"
                                    form="logoSecondaryForm" onchange="this.form.submit()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Favicon</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center">
                                <img src="{{ isset($settings['branding.favicon']['value']) ? asset($settings['branding.favicon']['value']) : asset('favicon.png') }}"
                                    alt="Favicon" class="h-16 mx-auto mb-2">
                                <button type="button" onclick="document.getElementById('logo-favicon').click()"
                                    class="text-sm text-blue-600 font-bold">Alterar</button>
                                <input type="file" name="logo" id="logo-favicon" class="hidden" accept="image/*"
                                    form="logoFaviconForm" onchange="this.form.submit()">
                            </div>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Cor Primária</label>
                            <input type="color" name="settings[branding.color_primary]"
                                value="{{ $settings['branding.color_primary']['value'] ?? '#3B82F6' }}"
                                class="w-full h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Cor Secundária</label>
                            <input type="color" name="settings[branding.color_secondary]"
                                value="{{ $settings['branding.color_secondary']['value'] ?? '#F97316' }}"
                                class="w-full h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Cor de Destaque</label>
                            <input type="color" name="settings[branding.color_accent]"
                                value="{{ $settings['branding.color_accent']['value'] ?? '#8B5CF6' }}"
                                class="w-full h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regional Settings -->
            <div x-show="activeTab === 'regional'" x-transition>
                <h3 class="text-2xl font-black text-gray-900 mb-6">Configurações Regionais</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Moeda</label>
                        <select name="settings[regional.currency]"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none custom-select">
                            <option value="MZN" {{ ($settings['regional.currency']['value'] ?? 'MZN') == 'MZN' ? 'selected' : '' }}>Metical (MZN)</option>
                            <option value="USD" {{ ($settings['regional.currency']['value'] ?? '') == 'USD' ? 'selected' : '' }}>Dólar (USD)</option>
                            <option value="EUR" {{ ($settings['regional.currency']['value'] ?? '') == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Símbolo da Moeda</label>
                        <input type="text" name="settings[regional.currency_symbol]"
                            value="{{ $settings['regional.currency_symbol']['value'] ?? 'MT' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fuso Horário</label>
                        <select name="settings[regional.timezone]"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none custom-select">
                            <option value="Africa/Maputo" {{ ($settings['regional.timezone']['value'] ?? 'Africa/Maputo') == 'Africa/Maputo' ? 'selected' : '' }}>África/Maputo</option>
                            <option value="UTC" {{ ($settings['regional.timezone']['value'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Formato de Data</label>
                        <select name="settings[regional.date_format]"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none custom-select">
                            <option value="d/m/Y" {{ ($settings['regional.date_format']['value'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/AAAA</option>
                            <option value="Y-m-d" {{ ($settings['regional.date_format']['value'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>AAAA-MM-DD</option>
                            <option value="m/d/Y" {{ ($settings['regional.date_format']['value'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/AAAA</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Formato de Hora</label>
                        <select name="settings[regional.time_format]"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none custom-select">
                            <option value="H:i" {{ ($settings['regional.time_format']['value'] ?? 'H:i') == 'H:i' ? 'selected' : '' }}>24h (HH:MM)</option>
                            <option value="h:i A" {{ ($settings['regional.time_format']['value'] ?? '') == 'h:i A' ? 'selected' : '' }}>12h (HH:MM AM/PM)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Email Settings -->
            <div x-show="activeTab === 'email'" x-transition>
                <h3 class="text-2xl font-black text-gray-900 mb-6">Configurações de Email</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nome do Remetente</label>
                        <input type="text" name="settings[email.from_name]"
                            value="{{ $settings['email.from_name']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email do Remetente</label>
                        <input type="email" name="settings[email.from_address]"
                            value="{{ $settings['email.from_address']['value'] ?? '' }}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div x-show="activeTab === 'system'" x-transition>
                <h3 class="text-2xl font-black text-gray-900 mb-6">Sistema</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6">
                        <h4 class="text-sm font-black uppercase tracking-widest text-gray-500 mb-4">Estado do Sistema</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-600">Versão</span>
                                <input type="text" name="settings[system.version]"
                                    value="{{ $settings['system.version']['value'] ?? '1.0.0' }}"
                                    class="px-3 py-2 border border-gray-200 rounded-xl text-sm font-bold w-32 text-right">
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-600">Setup Concluído</span>
                                <span class="text-xs font-black px-3 py-1 rounded-full {{ ($settings['system.setup_completed']['value'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ($settings['system.setup_completed']['value'] ?? false) ? 'SIM' : 'NÃO' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-gray-600">Modo Manutenção</span>
                                <label class="inline-flex items-center gap-2">
                                    <input type="hidden" name="settings[system.maintenance_mode]" value="0">
                                    <input type="checkbox" name="settings[system.maintenance_mode]" value="1"
                                        {{ ($settings['system.maintenance_mode']['value'] ?? false) ? 'checked' : '' }}
                                        class="w-5 h-5 text-blue-600 rounded border-gray-300">
                                    <span class="text-xs font-bold text-gray-500">Ativar</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6">
                        <h4 class="text-sm font-black uppercase tracking-widest text-gray-500 mb-4">Ações Rápidas</h4>
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex items-center justify-between">
                                <span>Ambiente</span>
                                <span class="font-black">{{ config('app.env') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>URL</span>
                                <span class="font-black">{{ config('app.url') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Locale</span>
                                <span class="font-black">{{ config('app.locale') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Settings -->
            <div x-show="activeTab === 'permissions'" x-transition x-data="{ activeRole: 'membro' }">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900">Acessos & Permissões</h3>
                        <p class="text-sm text-gray-500">Gerencie a visibilidade padrão do menu para cada papel do sistema.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="text-sm font-bold text-gray-700">Editar Papel:</label>
                        <select x-model="activeRole" class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-blue-500 outline-none font-bold text-sm bg-gray-50">
                            <option value="super_admin">Super Admin</option>
                            <option value="admin">Administrador</option>
                            <option value="membro">Membro</option>
                            <option value="lider_celula">Líder de Célula</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="pastor_zona">Pastor de Zona</option>
                            <option value="secretaria">Secretária</option>
                            <option value="comissao_obra">Comissão de Obra</option>
                            <option value="responsavel_pacote">Responsável de Pacote</option>
                            <option value="tesouraria">Tesouraria</option>
                            <option value="pastor">Pastor</option>
                            <option value="pastor_senior">Pastor Senior</option>
                            <option value="administracao">Administração</option>
                        </select>
                    </div>
                </div>

                @php
                    $permissionCategories = [
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
                            'menu_zones' => ['label' => 'Zonas', 'icon' => 'bi-geo-fill'],
                            'menu_supervisions' => ['label' => 'Supervisões', 'icon' => 'bi-diagram-2-fill'],
                            'menu_cells' => ['label' => 'Células', 'icon' => 'bi-diagram-3-fill'],
                        ],
                        'Financeira & Edificar' => [
                            'menu_packages' => ['label' => 'Gestão de Pacotes', 'icon' => 'bi-box-seam-fill'],
                            'menu_contributions_all' => ['label' => 'Ver Todas Contribuições', 'icon' => 'bi-cash-stack'],
                            'menu_finance' => ['label' => 'Módulo Financeiro', 'icon' => 'bi-pie-chart-fill'],
                        ],
                        'Sistema' => [
                            'menu_stats' => ['label' => 'Estatísticas Globais', 'icon' => 'bi-bar-chart-line-fill'],
                            'menu_users' => ['label' => 'Gestão de Utilizadores', 'icon' => 'bi-person-lock'],
                            'menu_settings' => ['label' => 'Configurações do Sistema', 'icon' => 'bi-gear-fill'],
                        ],
                    ];

                    $roles = ['super_admin', 'admin', 'membro', 'lider_celula', 'supervisor', 'pastor_zona', 'secretaria', 'comissao_obra', 'responsavel_pacote', 'tesouraria', 'pastor', 'pastor_senior', 'administracao'];
                @endphp

                <div class="space-y-8">
                    @foreach($roles as $roleKey)
                        <div x-show="activeRole === '{{ $roleKey }}'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                            @foreach($permissionCategories as $category => $items)
                                <div class="bg-gray-50 border border-gray-100 rounded-3xl overflow-hidden">
                                    <div class="px-6 py-4 bg-white border-b border-gray-100">
                                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ $category }}</h4>
                                    </div>
                                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($items as $key => $data)
                                            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-blue-200 transition-colors group">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                                        <i class="bi {{ $data['icon'] }} text-lg"></i>
                                                    </div>
                                                    <span class="text-sm font-bold text-gray-700">{{ $data['label'] }}</span>
                                                </div>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    @php
                                                        $settingKey = "permissions.role_{$roleKey}";
                                                        $rolePerms = $settings[$settingKey]['value'] ?? [];
                                                        if(is_string($rolePerms)) $rolePerms = json_decode($rolePerms, true) ?? [];
                                                        $isChecked = $rolePerms[$key] ?? false;
                                                        
                                                        // Fallback logic for initial visual state if no setting exists
                                                        if (!isset($settings[$settingKey])) {
                                                            // Simple default logic to WOW the user with pre-filled state
                                                            $isChecked = match($roleKey) {
                                                                'super_admin' => true,
                                                                'admin' => true,
                                                                'pastor_senior' => true,
                                                                'pastor' => !in_array($key, ['menu_users', 'menu_settings']),
                                                                'secretaria' => in_array($key, ['menu_services', 'menu_weddings', 'menu_visitors', 'menu_members', 'menu_inventory']),
                                                                'lider_celula' => in_array($key, ['menu_cell_meetings', 'menu_members', 'menu_contributions_all']),
                                                                default => false
                                                            };
                                                        }
                                                    @endphp
                                                    <input type="hidden" name="settings[{{ $settingKey }}][{{ $key }}]" value="0">
                                                    <input type="checkbox" name="settings[{{ $settingKey }}][{{ $key }}]" value="1" 
                                                        class="sr-only peer" {{ $isChecked ? 'checked' : '' }}>
                                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Backup -->
            <div x-show="activeTab === 'backup'" x-transition>
                @php
                    $formatBytes = function ($bytes) {
                        if ($bytes <= 0) return '0 B';
                        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                        $power = (int) floor(log($bytes, 1024));
                        $power = min($power, count($units) - 1);
                        $value = $bytes / (1024 ** $power);
                        return number_format($value, $power === 0 ? 0 : 2, ',', '.') . ' ' . $units[$power];
                    };
                @endphp

                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900">Database Backups</h3>
                        <p class="text-sm text-gray-500">Gerencie os backups armazenados no servidor.</p>
                    </div>
                    <a href="{{ route('settings.index', ['group' => 'backup']) }}"
                        class="inline-flex items-center gap-2 bg-gray-50 text-gray-600 px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 flex items-center gap-4 shadow-sm">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                            <i class="bi bi-hdd-stack"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Total Backups</p>
                            <p class="text-2xl font-black text-gray-900">{{ $backupCount }}</p>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 flex items-center gap-4 shadow-sm">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                            <i class="bi bi-cloud-check"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Armazenamento</p>
                            <p class="text-2xl font-black text-gray-900">{{ $formatBytes($backupTotalBytes) }}</p>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 flex items-center gap-4 shadow-sm">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Último Backup</p>
                            <p class="text-2xl font-black text-gray-900">
                                {{ $lastBackupAt ? \Carbon\Carbon::createFromTimestamp($lastBackupAt)->diffForHumans() : '—' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                        <div>
                            <h4 class="text-lg font-black text-gray-900">Criar Backup</h4>
                            <p class="text-sm text-gray-500">Gera um dump do banco e faz o download imediato.</p>
                        </div>
                        <a href="{{ route('settings.backup') }}"
                            class="inline-flex items-center justify-center bg-blue-600 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                            <i class="bi bi-play-fill mr-2"></i> Criar Backup Agora
                        </a>
                    </div>
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm text-blue-700 flex items-start gap-3">
                        <i class="bi bi-info-circle text-lg"></i>
                        <p class="font-medium">Backups automáticos podem ser mantidos por scripts externos. O histórico abaixo exibe apenas ficheiros existentes no servidor.</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 md:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-black text-gray-900">Histórico de Backups</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b">
                                    <th class="py-3 pr-4">Ficheiro</th>
                                    <th class="py-3 pr-4">Tamanho</th>
                                    <th class="py-3 pr-4">Criado</th>
                                    <th class="py-3 pr-4">Idade</th>
                                    <th class="py-3 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($backups as $index => $backup)
                                    <tr class="text-sm text-gray-700">
                                        <td class="py-4 pr-4 font-bold text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <i class="bi bi-file-earmark-zip text-gray-400"></i>
                                                <span class="truncate">{{ $backup['filename'] }}</span>
                                                @if($index === 0)
                                                    <span class="ml-2 px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase">Último</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 pr-4">{{ $formatBytes($backup['size']) }}</td>
                                        <td class="py-4 pr-4">
                                            {{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="py-4 pr-4">
                                            {{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->diffForHumans() }}
                                        </td>
                                        <td class="py-4 text-right">
                                            <a href="{{ route('settings.backup.download', $backup['filename']) }}"
                                                class="inline-flex items-center gap-2 text-blue-600 font-bold hover:text-blue-700">
                                                <i class="bi bi-download"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-10 text-center text-gray-400 font-medium">
                                            Nenhum backup disponível no servidor.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                <button type="submit"
                    class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/30">
                    <i class="bi bi-check-circle mr-2"></i>
                    Salvar Configurações
                </button>
            </div>
        </form>
        <form id="logoPrimaryForm" action="{{ route('settings.upload-logo') }}" method="POST"
            enctype="multipart/form-data" class="hidden">
            @csrf
            <input type="hidden" name="type" value="primary">
        </form>
        <form id="logoSecondaryForm" action="{{ route('settings.upload-logo') }}" method="POST"
            enctype="multipart/form-data" class="hidden">
            @csrf
            <input type="hidden" name="type" value="secondary">
        </form>
        <form id="logoFaviconForm" action="{{ route('settings.upload-logo') }}" method="POST"
            enctype="multipart/form-data" class="hidden">
            @csrf
            <input type="hidden" name="type" value="favicon">
        </form>
        <form id="resetSettingsForm" action="{{ route('settings.reset') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
@endsection
