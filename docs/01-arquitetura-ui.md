# 01 — Arquitetura UI

> **Data:** 2026-07-08 | **Estado:** Fase 2 — Auditoria

---

## 1. Mapa da Arquitetura Visual

```
┌──────────────────────────────────────────────────┐
│                    BODY                           │
│  ┌────────┐  ┌──────────────────────────────────┐│
│  │SIDEBAR │  │          MAIN CONTENT            ││
│  │ (280px)│  │  ┌──────────────────────────────┐││
│  │        │  │  │         HEADER               │││
│  │ Logo   │  │  │  hamburger | title | search  │││
│  │ Nav    │  │  │  notif | theme | user        │││
│  │ Items  │  │  └──────────────────────────────┘││
│  │ Sections│ │  ┌──────────────────────────────┐││
│  │ Submenus│ │  │         CONTENT              │││
│  │        │  │  │  @yield('content')            │││
│  │ User   │  │  │                              │││
│  │ Footer │  │  │                              │││
│  │ Logout │  │  └──────────────────────────────┘││
│  └────────┘  └──────────────────────────────────┘│
└──────────────────────────────────────────────────┘
```

## 2. Layout Principal: `app.blade.php`

### Dimensões e Problemas

| Métrica | Valor | Avaliação |
|---------|-------|-----------|
| Total de linhas | **1711** | 🔴 Crítico — ficheiro monolítico |
| CSS inline (tag `<style>`) | ~740 linhas | 🔴 CSS deveria estar em ficheiros separados |
| JavaScript inline | ~550 linhas | 🔴 JS deveria estar modularizado |
| HTML estrutural | ~420 linhas | 🟡 Aceitável, mas com duplicações |

### Dependências Externas (CDN)

| Recurso | Tipo | Risco |
|---------|------|-------|
| `cdn.tailwindcss.com` | CSS Runtime | 🔴 CDN runtime em produção é inaceitável |
| `cdn.jsdelivr.net/bootstrap-icons` | Ícones | 🟡 Deveria ser local |
| `cdn.jsdelivr.net/chart.js` | Gráficos | 🟡 Deveria ser local |
| `cdn.jsdelivr.net/sweetalert2` | UI | 🟡 Deveria ser local |
| `cdn.jsdelivr.net/tom-select` | UI | 🟡 Deveria ser local |

### Problemas Estruturais

1. **Ficheiro monolítico** — 1711 linhas misturando HTML, CSS inline e JS inline
2. **CSS em `<style>` tags** — Deveria estar em `resources/css/`
3. **JS inline extenso** — Funções globais sem modularização
4. **Duplicate `x-cloak`** — Declarado 3 vezes
5. **Session flash duplicada** — Mensagens de sessão renderizadas 2x (linhas 950-961 e 1591-1604)
6. **Sidebar collapsed contradição** — CSS conflituante (linhas 67-78 e 76-78 com `!important`)

## 3. Sidebar: `sidebar.blade.php`

### Dimensões

| Métrica | Valor | Avaliação |
|---------|-------|-----------|
| Total de linhas | **481** | 🟡 Grande mas funcional |
| Lógica PHP inline | ~25 linhas | 🟡 Queries no Blade (N+1 potencial) |
| Itens de navegação | ~40+ | 🟡 Menu complexo, mas organizado |

### Problemas

1. **Query N+1 no sidebar** — `Contribution::where('status', 'pendente')->count()` executado em toda página
2. **Lógica de permissão complexa** — Condicionais extensos diretamente no Blade
3. **Ordem dinâmica de seções** — Lógica PHP para reordenar sidebar por role, dificulta manutenção
4. **Ícones duplicados** — `bi-people-fill` usado em 2 itens diferentes
5. **CSS hardcoded** — Cores inline para atalhos rápidos

## 4. Componentes Blade Existentes

| Componente | Ficheiro | Estado |
|-----------|---------|--------|
| Application Logo | `application-logo.blade.php` | ✅ Funcional |
| Auth Session Status | `auth-session-status.blade.php` | ✅ Funcional |
| Danger Button | `danger-button.blade.php` | ⚠️ Pouco usado |
| Dropdown | `dropdown.blade.php` | ⚠️ Legacy, Alpine-based |
| Dropdown Link | `dropdown-link.blade.php` | ⚠️ Legacy |
| Input Error | `input-error.blade.php` | ✅ Funcional |
| Input Label | `input-label.blade.php` | ✅ Funcional |
| Modal | `modal.blade.php` | ⚠️ Pouco padronizado |
| Nav Link | `nav-link.blade.php` | ⚠️ Não usado na sidebar atual |
| Primary Button | `primary-button.blade.php` | ⚠️ Classes Tailwind inconsistentes |
| Responsive Nav Link | `responsive-nav-link.blade.php` | ⚠️ Legacy |
| Secondary Button | `secondary-button.blade.php` | ⚠️ Pouco usado |
| Text Input | `text-input.blade.php` | ⚠️ Muito básico |

### Problema Principal

Os componentes Blade existentes são **herança do scaffolding Laravel Breeze** e **praticamente não são utilizados** nas views atuais. As views preferem escrever HTML inline com classes Tailwind diretamente, gerando:

- **Zero reutilização** de estilos de botão
- **Inconsistência visual** entre páginas
- **Manutenibilidade baixa** — qualquer mudança de estilo precisa ser replicada em dezenas de ficheiros

## 5. Sistema de Cores

### Cores Primárias (CSS Variables)

```css
:root {
    --bg-primary: #f9fafb;      /* Light mode background */
    --bg-secondary: #ffffff;
    --bg-sidebar: #000000;       /* Sidebar sempre preto */
    --text-primary: #111827;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
}
```

### Cores Funcionais (Tailwind utility classes)

| Contexto | Cor |
|----------|-----|
| Primary/Brand | `orange-500/600` |
| Success | `green-500/600` |
| Warning | `yellow-500/600` |
| Danger | `red-500/600` |
| Info | `blue-500/600` |
| Active Item | `orange-600` (sidebar) |
| Dashboard Active | `blue-600`, `indigo-600` |

### Problemas com Cores

1. **Sem paleta formal** — Cores são ad-hoc via Tailwind utilities
2. **Dark mode incompleto** — Overrides com `!important` para classes Tailwind
3. **Inconsistência de hover states** — Algumas cores mudam no hover, outras não
4. **Sidebar hardcoded** — Sempre `bg-black`, não responde ao tema

## 6. Responsividade

### Breakpoints Utilizados

| Breakpoint | Tailwind | Uso |
|-----------|----------|-----|
| < 640px | `sm:` | Mobile |
| < 768px | `md:` | Tablet/Mobile boundary |
| < 1024px | `lg:` | Desktop small |
| < 1280px | `xl:` | Desktop |
| < 1536px | `2xl:` | Desktop large |

### Problemas Mobile

1. **Sidebar mobile** — Duplicação: existe tanto via `mobileOverlay` (JS puro) quanto via Alpine.js `mobileSidebarOpen`
2. **Header actions** — Escondidas em mobile via CSS, não há alternativa
3. **Tabelas** — Overflow-x sem indicação visual de scroll horizontal
4. **Dashboard admin** — Métricas eclesiásticas `hidden md:grid` — completamente ocultas em mobile
5. **Formulários** — Inputs não têm tamanho adequado para touch (mínimo 44px)

## 7. Performance UI

### Carregamento de Assets

1. **TailwindCSS CDN Runtime** — Compila CSS no browser, degradando performance
2. **5 CDN requests** para bibliotecas externas em cada page load
3. **1711 linhas de layout** carregadas em cada página
4. **CSS inline** não é cacheável pelo browser
5. **Sem lazy loading** de imagens ou componentes
6. **Chart.js carregado globalmente** — Mesmo em páginas sem gráficos
