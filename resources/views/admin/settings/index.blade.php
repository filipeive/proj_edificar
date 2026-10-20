@extends('layouts.app')

@section('title', 'Configurações do Sistema - Portal Life Church')

@section('content')
<div class="space-y-4" x-data="{ activeTab: '{{ $group }}' }">
    <!-- Header Executivo -->
    <div class="rounded-xl border border-gray-200 bg-white p-3.5 sm:p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Administração & Sistema</span>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight">Centro de Configurações</h1>
                <p class="text-xs text-gray-500 font-medium">Branding, região, email, permissões e integridade do sistema.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('settings.backup') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <i class="bi bi-database-down text-orange-600"></i> Backup
                </a>
                <form action="{{ route('settings.reset') }}" method="POST" onsubmit="return confirm('Tem certeza? Isso irá restaurar as configurações padrão.');">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100 transition-colors">
                        <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Barra de Abas Compacta -->
    <div class="rounded-xl border border-gray-200 bg-white p-1.5 shadow-sm">
        <div class="flex gap-1.5 overflow-x-auto whitespace-nowrap scrollbar-thin">
            @foreach($groups as $groupName)
                <button @click="activeTab = '{{ $groupName }}'" type="button"
                    :class="activeTab === '{{ $groupName }}' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                    class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-colors shrink-0">
                    {{ ucfirst($groupName === 'branding' ? 'Marca & Design' : ($groupName === 'permissions' ? 'Acessos & Permissões' : ($groupName === 'general' ? 'Geral' : ($groupName === 'regional' ? 'Regional' : ($groupName === 'email' ? 'Email' : ($groupName === 'system' ? 'Sistema' : $groupName)))))) }}
                </button>
            @endforeach
            <button @click="activeTab = 'backup'" type="button"
                :class="activeTab === 'backup' ? 'bg-orange-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition-colors shrink-0">
                Backups
            </button>
        </div>
    </div>

    <!-- Formulário Principal -->
    <form action="{{ route('settings.update') }}" method="POST"
        class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
        @csrf
        <input type="hidden" name="active_tab" x-model="activeTab">

        <!-- General Settings -->
        <div x-show="activeTab === 'general'" x-cloak class="space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Informações Gerais da Igreja</h2>
                <p class="text-xs text-gray-500">Dados cadastrais exibidos nos relatórios e cabeçalhos oficiais.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nome da Igreja</label>
                    <input type="text" name="settings[church.name]"
                        value="{{ $settings['church.name']['value'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Congregação / Filial</label>
                    <input type="text" name="settings[church.congregation]"
                        value="{{ $settings['church.congregation']['value'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Email Oficial</label>
                    <input type="email" name="settings[church.email]"
                        value="{{ $settings['church.email']['value'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Telefone de Contato</label>
                    <input type="text" name="settings[church.phone]"
                        value="{{ $settings['church.phone']['value'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Website</label>
                    <input type="url" name="settings[church.website]"
                        value="{{ $settings['church.website']['value'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Endereço Físico</label>
                    <input type="text" name="settings[church.address]"
                        value="{{ $settings['church.address']['value'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Descrição Institucional</label>
                    <textarea name="settings[church.description]" rows="3"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">{{ $settings['church.description']['value'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Branding Settings -->
        <div x-show="activeTab === 'branding'" x-cloak class="space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Marca & Personalização Visual</h2>
                <p class="text-xs text-gray-500">Identidade visual do portal, logótipos e esquemas de cores.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                <div class="rounded-lg border border-dashed border-gray-300 p-4 text-center bg-gray-50/50">
                    <span class="block text-xs font-bold text-gray-700 mb-2">Logo Principal</span>
                    <div class="h-14 flex items-center justify-center mb-2">
                        <img src="{{ isset($settings['branding.logo_primary']['value']) ? asset($settings['branding.logo_primary']['value']) : asset('images/logo-white-orange.png') }}"
                            alt="Logo Principal" class="max-h-12 max-w-full object-contain">
                    </div>
                    <button type="button" onclick="document.getElementById('logo-primary').click()"
                        class="inline-flex items-center gap-1 rounded-md bg-white border border-gray-300 px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-upload"></i> Alterar
                    </button>
                    <input type="file" name="logo" id="logo-primary" class="hidden" accept="image/*"
                        form="logoPrimaryForm" onchange="this.form.submit()">
                </div>

                <div class="rounded-lg border border-dashed border-gray-300 p-4 text-center bg-gray-50/50">
                    <span class="block text-xs font-bold text-gray-700 mb-2">Logo Secundário</span>
                    <div class="h-14 flex items-center justify-center mb-2">
                        <img src="{{ isset($settings['branding.logo_secondary']['value']) ? asset($settings['branding.logo_secondary']['value']) : asset('images/logo.png') }}"
                            alt="Logo Secundário" class="max-h-12 max-w-full object-contain">
                    </div>
                    <button type="button" onclick="document.getElementById('logo-secondary').click()"
                        class="inline-flex items-center gap-1 rounded-md bg-white border border-gray-300 px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-upload"></i> Alterar
                    </button>
                    <input type="file" name="logo" id="logo-secondary" class="hidden" accept="image/*"
                        form="logoSecondaryForm" onchange="this.form.submit()">
                </div>

                <div class="rounded-lg border border-dashed border-gray-300 p-4 text-center bg-gray-50/50">
                    <span class="block text-xs font-bold text-gray-700 mb-2">Favicon</span>
                    <div class="h-14 flex items-center justify-center mb-2">
                        <img src="{{ isset($settings['branding.favicon']['value']) ? asset($settings['branding.favicon']['value']) : asset('favicon.png') }}"
                            alt="Favicon" class="max-h-10 max-w-full object-contain">
                    </div>
                    <button type="button" onclick="document.getElementById('logo-favicon').click()"
                        class="inline-flex items-center gap-1 rounded-md bg-white border border-gray-300 px-2.5 py-1 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        <i class="bi bi-upload"></i> Alterar
                    </button>
                    <input type="file" name="logo" id="logo-favicon" class="hidden" accept="image/*"
                        form="logoFaviconForm" onchange="this.form.submit()">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 pt-2">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Cor Primária</label>
                    <input type="color" name="settings[branding.color_primary]"
                        value="{{ $settings['branding.color_primary']['value'] ?? '#3B82F6' }}"
                        class="w-full h-10 rounded-lg border border-gray-300 p-1 cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Cor Secundária</label>
                    <input type="color" name="settings[branding.color_secondary]"
                        value="{{ $settings['branding.color_secondary']['value'] ?? '#F97316' }}"
                        class="w-full h-10 rounded-lg border border-gray-300 p-1 cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Cor de Destaque</label>
                    <input type="color" name="settings[branding.color_accent]"
                        value="{{ $settings['branding.color_accent']['value'] ?? '#8B5CF6' }}"
                        class="w-full h-10 rounded-lg border border-gray-300 p-1 cursor-pointer">
                </div>
            </div>
        </div>

        <!-- Regional Settings -->
        <div x-show="activeTab === 'regional'" x-cloak class="space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Configurações Regionais & Moeda</h2>
                <p class="text-xs text-gray-500">Fusos horários, formatação de valores e calendários.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Moeda</label>
                    <select name="settings[regional.currency]"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="MZN" {{ ($settings['regional.currency']['value'] ?? 'MZN') == 'MZN' ? 'selected' : '' }}>Metical (MZN)</option>
                        <option value="USD" {{ ($settings['regional.currency']['value'] ?? '') == 'USD' ? 'selected' : '' }}>Dólar (USD)</option>
                        <option value="EUR" {{ ($settings['regional.currency']['value'] ?? '') == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Símbolo da Moeda</label>
                    <input type="text" name="settings[regional.currency_symbol]"
                        value="{{ $settings['regional.currency_symbol']['value'] ?? 'MT' }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Fuso Horário</label>
                    <select name="settings[regional.timezone]"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="Africa/Maputo" {{ ($settings['regional.timezone']['value'] ?? 'Africa/Maputo') == 'Africa/Maputo' ? 'selected' : '' }}>África/Maputo</option>
                        <option value="UTC" {{ ($settings['regional.timezone']['value'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Formato de Data</label>
                    <select name="settings[regional.date_format]"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="d/m/Y" {{ ($settings['regional.date_format']['value'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/AAAA</option>
                        <option value="Y-m-d" {{ ($settings['regional.date_format']['value'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>AAAA-MM-DD</option>
                        <option value="m/d/Y" {{ ($settings['regional.date_format']['value'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/AAAA</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Formato de Hora</label>
                    <select name="settings[regional.time_format]"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="H:i" {{ ($settings['regional.time_format']['value'] ?? 'H:i') == 'H:i' ? 'selected' : '' }}>24h (HH:MM)</option>
                        <option value="h:i A" {{ ($settings['regional.time_format']['value'] ?? '') == 'h:i A' ? 'selected' : '' }}>12h (HH:MM AM/PM)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div x-show="activeTab === 'email'" x-cloak class="space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Configurações de Notificações por E-mail</h2>
                <p class="text-xs text-gray-500">Identificação de envio das mensagens do sistema.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nome do Remetente</label>
                    <input type="text" name="settings[email.from_name]"
                        value="{{ $settings['email.from_name']['value'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">E-mail do Remetente</label>
                    <input type="email" name="settings[email.from_address]"
                        value="{{ $settings['email.from_address']['value'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div x-show="activeTab === 'system'" x-cloak class="space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h2 class="text-sm font-bold text-gray-900 tracking-tight">Parâmetros do Sistema</h2>
                <p class="text-xs text-gray-500">Informações técnicas de execução e manutenção.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3.5">
                <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-4 space-y-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-500 block">Status Operacional</span>
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-gray-700">Versão da Aplicação</span>
                        <input type="text" name="settings[system.version]"
                            value="{{ $settings['system.version']['value'] ?? '1.0.0' }}"
                            class="rounded-md border border-gray-300 px-2 py-1 text-xs font-mono font-bold w-24 text-right">
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-gray-700">Setup Inicial Concluído</span>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-black uppercase tracking-wider {{ ($settings['system.setup_completed']['value'] ?? false) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ ($settings['system.setup_completed']['value'] ?? false) ? 'SIM' : 'NÃO' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-gray-700">Modo de Manutenção</span>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="hidden" name="settings[system.maintenance_mode]" value="0">
                            <input type="checkbox" name="settings[system.maintenance_mode]" value="1"
                                {{ ($settings['system.maintenance_mode']['value'] ?? false) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                            <span class="text-xs font-bold text-gray-600">Ativar</span>
                        </label>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-4 space-y-3">
                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-500 block">Ambiente do Servidor</span>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-600">Ambiente (APP_ENV)</span>
                        <span class="font-mono font-bold text-gray-900">{{ config('app.env') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-600">URL Base</span>
                        <span class="font-mono font-bold text-gray-900 truncate max-w-[200px]">{{ config('app.url') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-600">Idioma Padrão</span>
                        <span class="font-mono font-bold text-gray-900">{{ config('app.locale') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Settings -->
        <div x-show="activeTab === 'permissions'" x-cloak x-data="{ activeRole: 'membro' }" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-gray-900 tracking-tight">Acessos & Visibilidade de Menus</h2>
                    <p class="text-xs text-gray-500">Configure quais módulos do menu lateral cada função eclesiástica pode visualizar.</p>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-gray-700 whitespace-nowrap">Função:</label>
                    <select x-model="activeRole" class="rounded-lg border-gray-300 text-xs font-bold py-1.5 focus:border-orange-500 focus:ring-orange-500">
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

            <div class="space-y-4">
                @foreach($roles as $roleKey)
                    <div x-show="activeRole === '{{ $roleKey }}'" x-cloak class="space-y-4">
                        @foreach($permissionCategories as $category => $items)
                            <div class="rounded-lg border border-gray-200 overflow-hidden">
                                <div class="bg-gray-50/75 px-3.5 py-2 border-b border-gray-200">
                                    <h4 class="text-[10px] font-black uppercase tracking-wider text-gray-500">{{ $category }}</h4>
                                </div>
                                <div class="p-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 bg-white">
                                    @foreach($items as $key => $data)
                                        @php
                                            $settingKey = "permissions.role_{$roleKey}";
                                            $rolePerms = $settings[$settingKey]['value'] ?? [];
                                            if(is_string($rolePerms)) $rolePerms = json_decode($rolePerms, true) ?? [];
                                            $isChecked = $rolePerms[$key] ?? false;

                                            if (!isset($settings[$settingKey])) {
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
                                        <label class="flex items-center justify-between p-2.5 rounded-lg border border-gray-100 hover:border-orange-200 hover:bg-orange-50/30 transition-colors cursor-pointer group">
                                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                                <i class="bi {{ $data['icon'] }} text-gray-400 group-hover:text-orange-600 transition-colors text-sm"></i>
                                                <span class="text-xs font-medium text-gray-700 truncate">{{ $data['label'] }}</span>
                                            </div>
                                            <div>
                                                <input type="hidden" name="settings[{{ $settingKey }}][{{ $key }}]" value="0">
                                                <input type="checkbox" name="settings[{{ $settingKey }}][{{ $key }}]" value="1"
                                                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4"
                                                    {{ $isChecked ? 'checked' : '' }}>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Backup -->
        <div x-show="activeTab === 'backup'" x-cloak class="space-y-4">
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

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-gray-900 tracking-tight">Cópias de Segurança (Database Backups)</h2>
                    <p class="text-xs text-gray-500">Gestão e download de dumps do banco de dados.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('settings.index', ['group' => 'backup']) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-xs font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                        <i class="bi bi-arrow-clockwise"></i> Atualizar
                    </a>
                    <a href="{{ route('settings.backup') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3 py-1 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                        <i class="bi bi-play-fill"></i> Gerar Novo Backup
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center text-base">
                        <i class="bi bi-hdd-stack"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Total de Dumps</span>
                        <span class="text-lg font-bold text-gray-900">{{ $backupCount }}</span>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-base">
                        <i class="bi bi-cloud-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Espaço Ocupado</span>
                        <span class="text-lg font-bold text-gray-900">{{ $formatBytes($backupTotalBytes) }}</span>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50/60 p-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-base">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Último Backup</span>
                        <span class="text-sm font-bold text-gray-900">
                            {{ $lastBackupAt ? \Carbon\Carbon::createFromTimestamp($lastBackupAt)->diffForHumans() : 'Nenhum' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tabela de Backups -->
            <div class="rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/75 border-b border-gray-200 text-[10px] font-black uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="py-2.5 px-3">Ficheiro</th>
                                <th class="py-2.5 px-3">Tamanho</th>
                                <th class="py-2.5 px-3">Data de Criação</th>
                                <th class="py-2.5 px-3">Idade</th>
                                <th class="py-2.5 px-3 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs sm:text-sm">
                            @forelse($backups as $index => $backup)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-2.5 px-3 font-medium text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-file-earmark-zip text-gray-400 text-sm"></i>
                                            <span class="truncate max-w-xs font-mono text-xs">{{ $backup['filename'] }}</span>
                                            @if($index === 0)
                                                <span class="rounded bg-emerald-50 px-1.5 py-0.2 text-[9px] font-black uppercase text-emerald-700 border border-emerald-200">Mais recente</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-600 text-xs font-mono">{{ $formatBytes($backup['size']) }}</td>
                                    <td class="py-2.5 px-3 text-gray-600 text-xs">
                                        {{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-500 text-xs">
                                        {{ \Carbon\Carbon::createFromTimestamp($backup['last_modified'])->diffForHumans() }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right">
                                        <a href="{{ route('settings.backup.download', $backup['filename']) }}"
                                            class="inline-flex items-center gap-1 rounded border border-gray-200 bg-white px-2 py-1 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                                            <i class="bi bi-download text-orange-600"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-xs text-gray-500">
                                        Nenhum backup disponível no servidor.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Botão Salvar -->
        <div class="flex justify-end pt-4 mt-4 border-t border-gray-100">
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                <i class="bi bi-check2"></i>
                Salvar Configurações
            </button>
        </div>
    </form>

    <!-- Formulários Ocultos de Upload de Logos -->
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
</div>
@endsection
