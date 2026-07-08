/**
 * MI-02: Layout JS — Extraído de app.blade.php
 * Portal Life Church — Design System JS
 */

// Importações locais após instalação NPM (MI-03)
import Swal from 'sweetalert2';
import TomSelect from 'tom-select';

// Tornar o Swal global para uso em outras partes do sistema
window.Swal = Swal;
window.TomSelect = TomSelect;

// Variáveis de Estado
let isSidebarExpanded = true;

// ===== SIDEBAR FUNCTIONS =====
export function toggleSidebar() {
    const sidebar = document.getElementById('sidebar-desktop') || document.getElementById('sidebar');
    const sidebarIcon = document.getElementById('sidebarIcon');
    if (!sidebar) return;

    isSidebarExpanded = !isSidebarExpanded;

    if (isSidebarExpanded) {
        sidebar.classList.remove('sidebar-collapsed');
        sidebar.classList.add('sidebar-expanded');
        sidebarIcon?.classList.replace('bi-layout-sidebar-inset', 'bi-layout-sidebar-inset-reverse');
    } else {
        sidebar.classList.remove('sidebar-expanded');
        sidebar.classList.add('sidebar-collapsed');
        sidebarIcon?.classList.replace('bi-layout-sidebar-inset-reverse', 'bi-layout-sidebar-inset');
    }

    localStorage.setItem('sidebarCollapsed', !isSidebarExpanded);

    // Notify Leaflet maps to recalculate size after transition
    setTimeout(() => {
        window.dispatchEvent(new Event('resize'));
    }, 300);
}

export function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar-desktop') || document.getElementById('sidebar');
    const mobileSidebar = document.getElementById('sidebar-mobile') || sidebar;
    const mobileOverlay = document.getElementById('mobileOverlay');
    if (!mobileSidebar) return;
    mobileSidebar.classList.toggle('mobile-open');
    mobileOverlay?.classList.toggle('hidden');
    document.body.style.overflow = mobileSidebar.classList.contains('mobile-open') ? 'hidden' : '';
}

export function toggleMenu(menuId) {
    const menu = document.getElementById(menuId);
    if (!menu) return;
    const button = menu.previousElementSibling;
    const icon = button?.querySelector('.bi-chevron-down');

    menu.classList.toggle('hidden');

    if (!menu.classList.contains('hidden')) {
        menu.classList.add('submenu-enter');
    }

    icon?.classList.toggle('rotate-180');
}

// ===== THEME FUNCTIONS =====
export function toggleTheme() {
    const themeSwitch = document.getElementById('themeSwitch');
    const themeIcon = document.getElementById('themeIcon');
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

    if (isDark) {
        document.documentElement.removeAttribute('data-theme');
        document.body.removeAttribute('data-theme');
        localStorage.setItem('theme', 'light');
        themeSwitch?.classList.remove('active');
        if (themeIcon) themeIcon.className = 'bi bi-moon-fill';
    } else {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.body.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
        themeSwitch?.classList.add('active');
        if (themeIcon) themeIcon.className = 'bi bi-sun-fill';
    }
}

export function initializeTheme() {
    const themeSwitch = document.getElementById('themeSwitch');
    const themeIcon = document.getElementById('themeIcon');
    const storedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.body.setAttribute('data-theme', 'dark');
        themeSwitch?.classList.add('active');
        if (themeIcon) themeIcon.className = 'bi bi-sun-fill';
    } else {
        document.documentElement.removeAttribute('data-theme');
        document.body.removeAttribute('data-theme');
        themeSwitch?.classList.remove('active');
        if (themeIcon) themeIcon.className = 'bi bi-moon-fill';
    }
}

// ===== SEARCH FUNCTIONS =====
export function toggleMobileSearch() {
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    if (!mobileSearchInput) return;
    mobileSearchInput.classList.toggle('hidden');
    if (!mobileSearchInput.classList.contains('hidden')) {
        mobileSearchInput.querySelector('input')?.focus();
    }
}

export function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

export function handleSearch(query, isMobile = false) {
    const searchResults = document.getElementById('searchResults');
    const mobileSearchResults = document.getElementById('mobileSearchResults');
    const targetResults = isMobile ? mobileSearchResults : searchResults;

    if (!targetResults) return;

    if (query.length < 3) {
        targetResults.classList.add('hidden');
        return;
    }

    targetResults.innerHTML = '<div class="p-3 text-center text-gray-500"><i class="bi bi-arrow-clockwise animate-spin mr-2"></i>A carregar...</div>';
    targetResults.classList.remove('hidden');

    const searchRoute = window.AppConfig?.routes?.search;
    if (!searchRoute) {
        console.error('Search route not configured');
        return;
    }

    fetch(`${searchRoute}?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        }
    })
        .then(response => response.json())
        .then(data => {
            let html = '';
            let totalResults = 0;

            for (const category in data.results) {
                const items = data.results[category];
                if (items.length > 0) {
                    totalResults += items.length;
                    html += `<div class="p-3 border-b border-gray-100 bg-gray-50 font-semibold text-xs uppercase text-gray-500">${category}</div>`;

                    items.forEach(item => {
                        if (category === 'Membros') {
                            const userLink = window.AppConfig.routes.usersShowTemplate.replace('__ID__', item.id);
                            html += `<a href="${userLink}" class="block p-3 hover:bg-gray-100 text-sm text-gray-800 transition">
                            <i class="bi bi-person-circle mr-2 text-blue-500"></i>${item.name} 
                            <span class="text-xs text-gray-500 ml-1">(${item.role})</span>
                        </a>`;
                        } else if (category === 'Contribuições') {
                            const date = new Date(item.contribution_date).toLocaleDateString('pt-PT');
                            const contributionLink = window.AppConfig.routes.contributionsShowTemplate.replace('__ID__', item.id);
                            html += `<a href="${contributionLink}" class="block p-3 hover:bg-gray-100 text-sm text-gray-800 transition">
                            <i class="bi bi-cash-coin mr-2 text-green-500"></i>${item.amount} MT em ${date}
                            <span class="text-xs text-gray-500 block ml-6">${item.user?.name || ''}</span>
                        </a>`;
                        }
                    });
                }
            }

            if (totalResults === 0) {
                html = '<div class="p-3 text-center text-gray-500">Nenhum resultado encontrado.</div>';
            }

            targetResults.innerHTML = html;
        })
        .catch(error => {
            targetResults.innerHTML = '<div class="p-3 text-center text-red-500">Erro ao carregar resultados.</div>';
            console.error('Search Error:', error);
        });
}

export const debouncedDesktopSearch = debounce((q) => handleSearch(q, false), 300);
export const debouncedMobileSearch = debounce((q) => handleSearch(q, true), 300);

export function initSearchableSelects() {
    const selects = document.querySelectorAll('select.searchable-select, select:not([data-searchable="false"]):not([data-tomselect-ready])');

    selects.forEach((select) => {
        if (select.dataset.tomselectReady || select.multiple || select.size > 1) return;
        if (!select.classList.contains('searchable-select') && select.options.length <= 6) return;

        select.setAttribute('data-tomselect-ready', 'true');

        try {
            new TomSelect(select, {
                create: false,
                placeholder: select.dataset.placeholder || select.dataset.searchPlaceholder || 'Pesquisar...',
                allowEmptyOption: true,
                maxOptions: 100,
                copyAttributesToRoot: true,
                render: {
                    no_results: function (data, escape) {
                        return '<div class="no-results">Nenhum resultado encontrado para "' + escape(data.input) + '"</div>';
                    },
                    option: function (data, escape) {
                        return '<div class="option">' + data.text + '</div>';
                    },
                    item: function (data, escape) {
                        return '<div class="item">' + data.text + '</div>';
                    }
                },
                dropdownParent: 'body',
                onInitialize: function () {
                    const control = this.control;
                    if (select.classList.contains('custom-select')) {
                        control.classList.add('custom-select-ready');
                    }
                }
            });
        } catch (e) {
            console.error('TomSelect Init Error:', e, select);
        }
    });
}

function hasSelectLabel(select) {
    if (select.closest('label')) return true;
    if (select.id && document.querySelector(`label[for="${select.id}"]`)) return true;
    if (select.previousElementSibling && select.previousElementSibling.tagName === 'LABEL') return true;
    let node = select.parentElement;
    let depth = 0;
    while (node && depth < 3) {
        if (node.querySelector && node.querySelector('label')) return true;
        node = node.parentElement;
        depth += 1;
    }
    return false;
}

function getSelectLabelText(select) {
    if (select.dataset.label) return select.dataset.label;
    if (select.getAttribute('aria-label')) return select.getAttribute('aria-label');
    if (select.name) {
        const raw = select.name
            .replace(/\[\]$/g, '')
            .replace(/\[\d+\]/g, '')
            .replace(/_?id$/i, '')
            .replace(/[_\-]+/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();

        const key = raw.toLowerCase();
        const map = {
            'preacher': 'Pregador',
            'zone': 'Zona',
            'supervision': 'Supervisão',
            'leader': 'Líder',
            'cell': 'Célula',
            'package': 'Pacote',
            'role': 'Função',
            'status': 'Estado',
            'category': 'Categoria',
            'month': 'Mês',
            'year': 'Ano',
            'type': 'Tipo',
            'service type': 'Tipo de Culto',
            'meeting type': 'Tipo de Encontro',
            'event type': 'Tipo de Evento',
            'course': 'Curso',
            'course class': 'Turma',
            'teacher male': 'Professor (Masculino)',
            'teacher female': 'Professora (Feminino)',
            'assistant male': 'Assistente (Masculino)',
            'assistant female': 'Assistente (Feminino)',
            'responsible': 'Responsável',
            'pastor': 'Pastor',
            'gender': 'Gênero',
            'currency': 'Moeda',
            'timezone': 'Fuso Horário',
            'date format': 'Formato de Data',
        };
        if (map[key]) return map[key];

        return raw.replace(/\b\w/g, (c) => c.toUpperCase());
    }
    return null;
}

export function addMissingSelectLabels() {
    const selects = document.querySelectorAll('select');
    selects.forEach((select) => {
        if (hasSelectLabel(select)) return;
        const labelText = getSelectLabelText(select);
        if (!labelText) return;
        if (!select.id) {
            select.id = `select-${Math.random().toString(36).slice(2)}`;
        }
        const label = document.createElement('label');
        label.className = 'block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2';
        label.setAttribute('for', select.id);
        label.textContent = labelText;
        select.parentNode.insertBefore(label, select);
    });
}

export function initPhonePrefixInputs() {
    const selector = 'input[type="tel"], input[name*="phone"], input[name*="whatsapp"]';
    document.querySelectorAll(selector).forEach((input) => {
        if (input.dataset.phonePrefix === 'false') return;
        if (input.closest('.phone-prefix-wrapper')) return;
        if (input.dataset.phonePrefixReady === 'true') return;
        input.dataset.phonePrefixReady = 'true';

        const wrapper = document.createElement('div');
        wrapper.className = 'phone-prefix-wrapper';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        const label = document.createElement('span');
        label.className = 'phone-prefix-label';
        label.textContent = '+258';
        wrapper.appendChild(label);

        input.classList.add('phone-prefix-input');
        if (!input.placeholder) {
            input.placeholder = '823562000';
        }

        const normalizeLocalPhone = (value) => {
            const digits = value.replace(/\D/g, '');
            const local = digits.startsWith('258') ? digits.slice(3) : digits;
            if (local.length === 9) {
                return `${local.slice(0, 2)} ${local.slice(2, 5)} ${local.slice(5, 9)}`;
            }
            return value.replace(/^\+?258\s*/, '');
        };

        input.value = normalizeLocalPhone(input.value);

        input.addEventListener('blur', () => {
            if (!input.value.trim()) return;
            input.value = normalizeLocalPhone(input.value);
        });
    });
}

// ===== NOTIFICATIONS FUNCTIONS =====
export function toggleNotifications() {
    const notificationsPanel = document.getElementById('notificationsPanel');
    if (!notificationsPanel) return;
    notificationsPanel.classList.toggle('hidden');
    if (!notificationsPanel.classList.contains('hidden')) {
        loadNotifications();
    }
}

export function toggleUserMenu() {
    const userMenu = document.getElementById('userMenu');
    const chevron = document.querySelector('[onclick="toggleUserMenu()"] .bi-chevron-down');
    if (!userMenu) return;

    userMenu.classList.toggle('hidden');

    if (chevron) {
        chevron.classList.toggle('rotate-180');
    }
}

export function loadNotifications(showSuccess = false) {
    const targetContent = document.getElementById('notificationsContent');
    const notificationsIndexRoute = window.AppConfig?.routes?.notificationsIndex;
    if (!targetContent || !notificationsIndexRoute) return;

    fetch(notificationsIndexRoute, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const typeMap = {
                    contribution_verified: { icon: 'bi-check-circle-fill', bg: 'bg-green-50', text: 'text-green-600', label: 'Confirmada', badge: 'bg-green-50 text-green-700' },
                    contribution_rejected: { icon: 'bi-x-circle-fill', bg: 'bg-red-50', text: 'text-red-600', label: 'Rejeitada', badge: 'bg-red-50 text-red-700' },
                    contribution_created: { icon: 'bi-cash-coin', bg: 'bg-blue-50', text: 'text-blue-600', label: 'Registo', badge: 'bg-blue-50 text-blue-700' },
                    contribution_pending_validation: { icon: 'bi-exclamation-triangle-fill', bg: 'bg-orange-50', text: 'text-orange-600', label: 'Validação', badge: 'bg-orange-50 text-orange-700' },
                    contribution_verified_manager: { icon: 'bi-check-circle-fill', bg: 'bg-emerald-50', text: 'text-emerald-600', label: 'Pacote', badge: 'bg-emerald-50 text-emerald-700' },
                    contribution_rejected_manager: { icon: 'bi-x-circle-fill', bg: 'bg-rose-50', text: 'text-rose-600', label: 'Pacote', badge: 'bg-rose-50 text-rose-700' },
                    pending_contributions: { icon: 'bi-exclamation-triangle-fill', bg: 'bg-orange-50', text: 'text-orange-600', label: 'Comissão', badge: 'bg-orange-50 text-orange-700' },
                    member_created: { icon: 'bi-person-plus-fill', bg: 'bg-purple-50', text: 'text-purple-600', label: 'Conta', badge: 'bg-purple-50 text-purple-700' },
                    member_added_to_cell: { icon: 'bi-people-fill', bg: 'bg-sky-50', text: 'text-sky-600', label: 'Célula', badge: 'bg-sky-50 text-sky-700' },
                    commitment_chosen: { icon: 'bi-handshake-fill', bg: 'bg-indigo-50', text: 'text-indigo-600', label: 'Compromisso', badge: 'bg-indigo-50 text-indigo-700' },
                    commitment_expiring: { icon: 'bi-clock-fill', bg: 'bg-yellow-50', text: 'text-yellow-600', label: 'Prazo', badge: 'bg-yellow-50 text-yellow-700' },
                    user_promoted: { icon: 'bi-star-fill', bg: 'bg-yellow-50', text: 'text-yellow-600', label: 'Cargo', badge: 'bg-yellow-50 text-yellow-700' },
                };

                let html = '<ul class="space-y-2">';
                data.forEach(n => {
                    const type = n.type || 'general';
                    const cfg = typeMap[type] || { icon: 'bi-bell-fill', bg: 'bg-gray-50', text: 'text-gray-500', label: 'Sistema', badge: 'bg-gray-100 text-gray-600' };

                    html += `
                        <a href="${n.link}" class="flex items-start p-3 hover:bg-gray-50 rounded-lg cursor-pointer block transition">
                            <div class="w-9 h-9 rounded-xl ${cfg.bg} ${cfg.text} flex items-center justify-center mr-3 mt-1 flex-shrink-0">
                                <i class="bi ${cfg.icon}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-gray-800 text-sm">${n.title}</p>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest ${cfg.badge}">
                                        ${cfg.label}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">${n.message}</p>
                                <p class="text-xs text-gray-400 mt-1">${n.time_ago}</p>
                            </div>
                        </a>
                    `;
                });
                html += '</ul>';
                targetContent.innerHTML = html;
            } else {
                targetContent.innerHTML = showSuccess
                    ? '<div class="text-center text-green-600 py-4"><i class="bi bi-check-circle mr-2"></i>Todas marcadas como lidas!</div>'
                    : '<div class="text-center text-gray-500 py-4"><i class="bi bi-inbox mr-2"></i>Nenhuma notificação.</div>';
            }
        })
        .catch(error => {
            targetContent.innerHTML = '<div class="text-center text-red-500 py-4">Erro ao carregar notificações.</div>';
            console.error('Notifications Error:', error);
        });
}

export function markAllAsRead() {
    const notificationsReadRoute = window.AppConfig?.routes?.notificationsRead;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    if (!notificationsReadRoute) return;

    fetch(notificationsReadRoute, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        }
    })
        .then(() => loadNotifications(true))
        .catch(error => console.error('Mark as read error:', error));
}

export function updateNotificationBadge() {
    const notificationsUnreadCountRoute = window.AppConfig?.routes?.notificationsUnreadCount;
    if (!notificationsUnreadCountRoute) return;

    fetch(notificationsUnreadCountRoute, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            const bell = document.querySelector('.bi-bell');
            if (!bell) return;
            // O badge pode ser um elemento seguinte (nextElementSibling) ou um filho, dependendo do markup
            const badge = bell.nextElementSibling || bell.parentElement.querySelector('.badge');
            if (badge) {
                badge.style.display = data.count > 0 ? 'block' : 'none';
            }
        })
        .catch(error => console.error('Badge update error:', error));
}

// ===== TOAST FUNCTIONS =====
export function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.remove('toast-enter');
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 300);
    }
}

// ===== SweetAlert2 Helper Functions =====
export function confirmDelete(formId, message = 'Tem certeza que deseja deletar?', title = 'Confirmar Exclusão') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sim, deletar!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed && formId) {
            document.getElementById(formId)?.submit();
        }
        return result;
    });
}

export function confirmAction(title, message, icon = 'question', confirmText = 'Sim, confirmar!', formId = null) {
    return Swal.fire({
        title: title,
        text: message,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed && formId) {
            document.getElementById(formId)?.submit();
        }
        return result;
    });
}

export function showSuccess(message, title = 'Sucesso!') {
    Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        timer: 3000,
        showConfirmButton: false
    });
}

export function showError(message, title = 'Erro!') {
    Swal.fire({
        icon: 'error',
        title: title,
        text: message
    });
}

export function showWarning(message, title = 'Atenção!') {
    Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        timer: 4000,
        showConfirmButton: false
    });
}

export function showInfo(message, title = 'Informação') {
    Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        timer: 8000,
        showConfirmButton: true
    });
}

// Exportação global de todas as funções necessárias para os handlers HTML onclick/oninput
window.toggleSidebar = toggleSidebar;
window.toggleMobileSidebar = toggleMobileSidebar;
window.toggleMenu = toggleMenu;
window.toggleTheme = toggleTheme;
window.initializeTheme = initializeTheme;
window.toggleMobileSearch = toggleMobileSearch;
window.debouncedDesktopSearch = debouncedDesktopSearch;
window.debouncedMobileSearch = debouncedMobileSearch;
window.initSearchableSelects = initSearchableSelects;
window.addMissingSelectLabels = addMissingSelectLabels;
window.initPhonePrefixInputs = initPhonePrefixInputs;
window.toggleNotifications = toggleNotifications;
window.toggleUserMenu = toggleUserMenu;
window.loadNotifications = loadNotifications;
window.markAllAsRead = markAllAsRead;
window.updateNotificationBadge = updateNotificationBadge;
window.closeToast = closeToast;
window.confirmDelete = confirmDelete;
window.confirmAction = confirmAction;
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function () {
    initializeTheme();

    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed && window.innerWidth >= 768) {
        toggleSidebar();
    }

    document.querySelectorAll('[role="alert"]').forEach(toast => {
        toast.classList.add('toast-enter');
        setTimeout(() => closeToast(toast.id), 5000);
    });

    updateNotificationBadge();
    setInterval(updateNotificationBadge, 30000);
    addMissingSelectLabels();
    initSearchableSelects();
    initPhonePrefixInputs();

    // Close dropdowns on outside click
    document.addEventListener('click', function (event) {
        const notificationsPanel = document.getElementById('notificationsPanel');
        const notifButton = document.querySelector('[onclick="toggleNotifications()"]');
        if (notificationsPanel && notifButton && !notificationsPanel.contains(event.target) && !notifButton.contains(event.target)) {
            notificationsPanel.classList.add('hidden');
        }

        const userMenuButton = event.target.closest('[onclick="toggleUserMenu()"]');
        const userMenu = document.getElementById('userMenu');
        if (userMenu && !userMenu.contains(event.target) && !userMenuButton) {
            userMenu.classList.add('hidden');
            const chevron = document.querySelector('[onclick="toggleUserMenu()"] .bi-chevron-down');
            if (chevron) {
                chevron.classList.remove('rotate-180');
            }
        }

        const searchContainer = document.querySelector('.search-container');
        const searchResults = document.getElementById('searchResults');
        const mobileSearchResults = document.getElementById('mobileSearchResults');
        if (searchContainer && !searchContainer.contains(event.target)) {
            searchResults?.classList.add('hidden');
            mobileSearchResults?.classList.add('hidden');
        }
    });

    document.querySelectorAll('aside a').forEach(link => {
        link.addEventListener('click', function () {
            if (window.innerWidth < 768) {
                toggleMobileSidebar();
            }
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            if (window.innerWidth >= 768) {
                toggleSidebar();
            } else {
                toggleMobileSidebar();
            }
        }
    });

    window.addEventListener('resize', function () {
        const sidebar = document.getElementById('sidebar-desktop') || document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');
        if (window.innerWidth >= 768 && sidebar) {
            sidebar.classList.remove('mobile-open');
            mobileOverlay?.classList.add('hidden');
            document.body.style.overflow = '';
        }
    });

    // Auto-scroll para item ativo na sidebar
    const initSidebarScroll = () => {
        const sidebars = document.querySelectorAll('.app-sidebar');
        if (!sidebars.length) return;

        sidebars.forEach((sidebar) => {
            const activeItem = sidebar.querySelector(
                '.nav-item.bg-orange-600, .nav-item.bg-blue-600, .nav-item.bg-indigo-600, .nav-item.bg-zinc-900, .nav-item.bg-zinc-900\\/50'
            );
            if (!activeItem) return;

            const scrollContainer = activeItem.closest('.custom-scrollbar') || sidebar;
            const stickyHeader = sidebar.querySelector('.sticky.top-0');
            const headerOffset = stickyHeader ? stickyHeader.offsetHeight + 12 : 24;

            const runScroll = () => {
                const visibleTop = scrollContainer.scrollTop + headerOffset;
                const visibleBottom = scrollContainer.scrollTop + scrollContainer.clientHeight - 16;
                const itemTop = activeItem.offsetTop;
                const itemBottom = itemTop + activeItem.offsetHeight;

                const targetTop = itemTop - headerOffset;
                const shouldScroll = itemTop < visibleTop || itemBottom > visibleBottom;
                if (shouldScroll) {
                    scrollContainer.scrollTo({ top: Math.max(targetTop, 0), behavior: 'smooth' });
                }

                activeItem.classList.add('nav-item-highlight');
                setTimeout(() => activeItem.classList.remove('nav-item-highlight'), 1800);
            };

            requestAnimationFrame(() => setTimeout(runScroll, 60));
        });
    };
    initSidebarScroll();

    // Submissão automática do formulário de busca após 500ms
    const searchInputs = document.querySelectorAll('input[name="search"]');
    searchInputs.forEach((input) => {
        const mode = (input.dataset.liveSearch || 'submit').toLowerCase();
        if (mode === 'ajax' || mode === 'manual' || mode === 'off') return;

        const form = input.closest('form');
        if (!form) return;

        let timeout = null;
        input.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
});

