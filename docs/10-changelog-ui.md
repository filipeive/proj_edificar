# 10 — Changelog UI

> Registo cronológico de todas as alterações aprovadas e implementadas na interface.

---

## [2026-07-08] — Ciclo 1: Quick Wins

### QW-01: Remover TailwindCSS CDN Runtime ✔️
- **Ficheiros alterados:** `resources/views/layouts/app.blade.php` (linha 27)
- **Alteração:** Removido `<script src="https://cdn.tailwindcss.com">`, substituído por comentário explicativo
- **Impacto:** Eliminação de ~300KB de JS desnecessário. Tailwind continua a ser compilado via Vite (`@vite` na linha 26).
- **Benefício:** Redução significativa no First Contentful Paint. Eliminação de FOUC.
- **Risco:** ⬜ Nenhum — o Tailwind já era compilado via Vite/PostCSS

### QW-02: Eliminar Flash Messages Duplicadas ✔️
- **Ficheiros alterados:** `resources/views/layouts/app.blade.php` (linhas 949-961 removidas)
- **Alteração:** Removido o primeiro bloco de `Session::get('success/error/warning/info')` que disparava antes das funções `showSuccess/showError/etc.` serem definidas.
- **Impacto:** Mensagens de sucesso/erro agora aparecem apenas 1 vez (bloco mantido nas linhas ~1578-1593, após definição das funções).
- **Benefício:** UX correta — notificações não duplicadas.
- **Risco:** ⬜ Nenhum — o bloco mantido é mais robusto (dentro de `DOMContentLoaded`)

### QW-03: Corrigir CSS Sidebar Conflitante ✔️
- **Ficheiros alterados:** `resources/views/layouts/app.blade.php` (linhas 75-84 removidas)
- **Alteração:** Removidas regras CSS `.sidebar-collapsed { width: 280px !important }` e `.sidebar-collapsed .sidebar-text { opacity: 1 !important }` que anulavam o comportamento de collapse.
- **Impacto:** A classe `.sidebar-collapsed` agora respeita `width: 80px` conforme definido nas linhas 67-69.
- **Benefício:** Sidebar collapse funcional, CSS previsível.
- **Risco:** ⬜ Baixo — se o sidebar collapse não era usado ativamente, não há impacto visual

### QW-04: Remover x-cloak Duplicados ✔️
- **Ficheiros alterados:** `resources/views/layouts/app.blade.php` (linhas 449-451 e 740-742)
- **Alteração:** Removidas 2 declarações duplicadas de `[x-cloak] { display: none !important; }`, mantendo apenas a primeira (linha 102).
- **Impacto:** CSS mais limpo, sem redundância.
- **Benefício:** Manutenibilidade.
- **Risco:** ⬜ Nenhum — funcionalidade inalterada

### QW-05: Mostrar Métricas no Mobile (Dashboard Admin) ✔️
- **Ficheiros alterados:** `resources/views/dashboard/admin.blade.php` (linhas 15-16, 19, 45, 75, 99)
- **Alteração:**
  - `hidden md:grid` → `grid` (métricas agora visíveis em todos os breakpoints)
  - `grid-cols-1 md:grid-cols-2` → `grid-cols-2 md:grid-cols-2` (2 colunas em mobile)
  - `gap-6` → `gap-3 md:gap-6` (gap reduzido em mobile)
  - `p-8` → `p-4 md:p-8` em todos os 4 cards (padding responsivo)
- **Impacto:** As 4 métricas eclesiásticas (Membros, Dízimos, Eventos, Cultos) agora são visíveis em telemóvel.
- **Benefício:** Mobile-first dashboard. Informação crítica acessível a todos os utilizadores.
- **Risco:** ⬜ Nenhum — apenas alterações visuais responsivas

## [2026-07-08] — Estabilização do Dark Mode e Alinhamento de Views

### ME-01: Dark Mode Consistente (Ajustes de Contraste e Inputs) ✔️
- **Ficheiros alterados:** `resources/css/layout.css`
- **Alteração:** Adicionados overrides globais detalhados para as classes de texto (`text-gray-*`, `text-zinc-*`, `text-slate-*`), fundos e bordas do Tailwind em modo escuro (`[data-theme="dark"]`). Estilização global e robusta de inputs de formulários, selects, textareas e botões secundários ("Cancelar") sem classes `dark:` individuais.
- **Impacto:** Todas as páginas administrativas e modais agora exibem contraste excelente e legibilidade correta de textos e campos de entrada no tema escuro.
- **Benefício:** Acessibilidade visual aprimorada, visual premium e consistência em todo o dashboard administrativo.
- **Risco:** ⬜ Nenhum — regras CSS restritas ao atributo `[data-theme="dark"]`

