# 08 — Roadmap UI / Plano de Evolução

> **Data:** 2026-07-08 | **Estado:** Fase 3 — Planejamento (Aguardando Aprovação)

---

## Estrutura de Prioridades

| Prioridade | Tipo | Prazo Esperado |
|-----------|------|---------------|
| 🔴 P0 | Quick Wins — Impacto imediato, esforço mínimo | 1-2 dias |
| 🟠 P1 | Melhorias Importantes — Impacto significativo | 3-5 dias |
| 🟡 P2 | Melhorias Estruturais — Fundação para escala | 1-2 semanas |
| 🟢 P3 | Refatorações Futuras — Evolução contínua | 2-4 semanas |

---

## 🔴 P0 — QUICK WINS

### QW-01: Remover TailwindCSS CDN Runtime
- **Descrição:** Remover `<script src="https://cdn.tailwindcss.com">` do layout. O projeto já compila Tailwind via Vite.
- **Motivo:** CDN runtime compila CSS no browser em cada carregamento, degradando performance e gerando FOUC (Flash of Unstyled Content).
- **Benefício:** Redução de ~50% no tempo de first paint. Eliminação de ~300KB de JS desnecessário.
- **Impacto:** 🔴 Alto
- **Dificuldade:** ⬜ Trivial
- **Estimativa:** 15 min
- **Ficheiros:** `resources/views/layouts/app.blade.php` (linha 27)
- **Status:** ⏳ Planejado

### QW-02: Eliminar Flash Messages Duplicadas
- **Descrição:** Remover bloco duplicado de session flash messages (renderizado 2x no layout).
- **Motivo:** Mensagens de sucesso/erro aparecem duplicadas para o utilizador.
- **Benefício:** UX limpa, menos JS executado.
- **Impacto:** 🟡 Médio
- **Dificuldade:** ⬜ Trivial
- **Estimativa:** 10 min
- **Ficheiros:** `resources/views/layouts/app.blade.php` (linhas 950-961 vs 1591-1604)
- **Status:** ⏳ Planejado

### QW-03: Corrigir CSS Sidebar Conflitante
- **Descrição:** Remover regras CSS conflitantes do sidebar collapsed (`.sidebar-collapsed` com `!important` override).
- **Motivo:** CSS conflitante em linhas 67-78 e 76-78, onde `.sidebar-collapsed` é definido duas vezes com valores opostos.
- **Benefício:** Sidebar collapse funcional, menor confusão no CSS.
- **Impacto:** 🟡 Médio
- **Dificuldade:** ⬜ Trivial
- **Estimativa:** 15 min
- **Ficheiros:** `resources/views/layouts/app.blade.php` (CSS inline)
- **Status:** ⏳ Planejado

### QW-04: Remover Declarações x-cloak Duplicadas
- **Descrição:** Consolidar as 3 declarações `[x-cloak] { display: none !important; }` em uma única.
- **Motivo:** Código duplicado sem necessidade.
- **Benefício:** CSS mais limpo.
- **Impacto:** ⬜ Baixo
- **Dificuldade:** ⬜ Trivial
- **Estimativa:** 5 min
- **Ficheiros:** `resources/views/layouts/app.blade.php`
- **Status:** ⏳ Planejado

### QW-05: Mostrar Métricas no Mobile (Dashboard Admin)
- **Descrição:** As 4 métricas eclesiásticas do dashboard admin estão `hidden md:grid` — invisíveis em mobile.
- **Motivo:** Informação crucial inacessível para utilizadores mobile (que são maioria).
- **Benefício:** Mobile-first dashboard com informação relevante sempre visível.
- **Impacto:** 🔴 Alto
- **Dificuldade:** ⬜ Trivial
- **Estimativa:** 20 min
- **Ficheiros:** `resources/views/dashboard/admin.blade.php` (linha 16)
- **Status:** ⏳ Planejado

---

## 🟠 P1 — MELHORIAS IMPORTANTES

### MI-01: Extrair CSS Inline para Ficheiro Dedicado
- **Descrição:** Mover os ~740 linhas de CSS do `<style>` tag no layout para `resources/css/layout.css` ou componentizar.
- **Motivo:** CSS inline não é cacheável, não é reusável, dificulta manutenção.
- **Benefício:** Cache do browser, código organizado, menor tamanho do HTML.
- **Impacto:** 🔴 Alto
- **Dificuldade:** 🟡 Média
- **Estimativa:** 2-3 horas
- **Ficheiros:** `resources/views/layouts/app.blade.php`, `resources/css/app.css`, `vite.config.js`
- **Status:** ⏳ Planejado

### MI-02: Extrair JS Inline para Módulos
- **Descrição:** Mover os ~550 linhas de JavaScript do layout para ficheiros modulares em `resources/js/`.
- **Motivo:** JS inline bloqueia rendering, não é cacheável, dificulta debugging.
- **Benefício:** Code splitting, cache, testing, manutenibilidade.
- **Impacto:** 🔴 Alto
- **Dificuldade:** 🟡 Média
- **Estimativa:** 3-4 horas
- **Ficheiros:** `resources/views/layouts/app.blade.php`, `resources/js/`
- **Status:** ⏳ Planejado

### MI-03: Internalizar Dependências CDN
- **Descrição:** Instalar Chart.js, SweetAlert2, Tom Select e Bootstrap Icons via npm e importar via Vite.
- **Motivo:** CDNs são pontos de falha, bloqueiam rendering, não são cacheáveis pelo service worker.
- **Benefício:** Performance, offline-first, controle de versão.
- **Impacto:** 🟠 Alto
- **Dificuldade:** 🟡 Média
- **Estimativa:** 2-3 horas
- **Ficheiros:** `package.json`, `resources/js/app.js`, `resources/views/layouts/app.blade.php`
- **Status:** ⏳ Planejado

### MI-04: Criar Design Tokens (CSS Custom Properties)
- **Descrição:** Estabelecer um sistema formal de design tokens para cores, espaçamentos, tipografia, radius, sombras.
- **Motivo:** Sem design tokens, cada view inventa suas próprias cores e espaçamentos.
- **Benefício:** Consistência total, facilidade de theming, dark mode robusto.
- **Impacto:** 🔴 Alto
- **Dificuldade:** 🟡 Média
- **Estimativa:** 3-4 horas
- **Ficheiros:** `resources/css/tokens.css`, `tailwind.config.js`
- **Status:** ⏳ Planejado

### MI-05: Criar Component Library (Blade)
- **Descrição:** Criar componentes reutilizáveis para Button, Card, Badge, Input, Select, Table, Modal, Alert, EmptyState.
- **Motivo:** Atualmente cada view reinventa botões, cards e inputs com classes diferentes.
- **Benefício:** Consistência visual, velocidade de desenvolvimento, manutenção centralizada.
- **Impacto:** 🔴 Alto
- **Dificuldade:** 🟠 Alta
- **Estimativa:** 6-8 horas
- **Ficheiros:** `resources/views/components/ui/`
- **Status:** ⏳ Planejado

### MI-06: Resolver Sidebar Query N+1
- **Descrição:** Mover a query de contagem de contribuições pendentes para um View Composer ou cache.
- **Motivo:** Query executada em TODA requisição, mesmo onde o sidebar não é renderizado.
- **Benefício:** Performance (eliminar 1-3 queries por request).
- **Impacto:** 🟡 Médio
- **Dificuldade:** ⬜ Baixa
- **Estimativa:** 30 min
- **Ficheiros:** `resources/views/layouts/sidebar.blade.php`, `app/Providers/AppServiceProvider.php`
- **Status:** ⏳ Planejado

### MI-07: Melhorar Tabelas Mobile (Scroll Indicator)
- **Descrição:** Adicionar indicador visual de scroll horizontal em tabelas no mobile.
- **Motivo:** Utilizadores não sabem que podem scrollar lateralmente.
- **Benefício:** UX mobile significativamente melhor.
- **Impacto:** 🟡 Médio
- **Dificuldade:** ⬜ Baixa
- **Estimativa:** 45 min
- **Ficheiros:** `resources/css/app.css`, views com tabelas
- **Status:** ⏳ Planejado

---

## 🟡 P2 — MELHORIAS ESTRUTURAIS

### ME-01: Implementar Dark Mode Consistente
- **Descrição:** Substituir os overrides `[data-theme="dark"] .bg-white { ... !important }` por classes Tailwind `dark:` nativas.
- **Motivo:** O dark mode atual usa hacks CSS com `!important` que são frágeis.
- **Benefício:** Dark mode robusto e manutenível.
- **Impacto:** 🟡 Médio
- **Dificuldade:** 🟠 Alta
- **Estimativa:** 1-2 dias
- **Status:** ⏳ Planejado

### ME-02: Refatorar Layout Monolítico
- **Descrição:** Dividir `app.blade.php` (1711 linhas) em partials lógicos: `@include('layouts.header')`, `@include('layouts.scripts')`, etc.
- **Motivo:** Ficheiro impossível de manter, alto risco de regressão.
- **Benefício:** Manutenibilidade, code review mais fácil.
- **Impacto:** 🔴 Alto
- **Dificuldade:** 🟡 Média
- **Estimativa:** 1 dia
- **Status:** ⏳ Planejado

### ME-03: Estabelecer Padrão de Formulários
- **Descrição:** Criar componente Blade para formulários com validação, labels, hints, error states padronizados.
- **Motivo:** Formulários extensos (31KB+ cada) sem padrão consistente.
- **Benefício:** Formulários consistentes, acessíveis, mais rápidos de criar.
- **Impacto:** 🔴 Alto
- **Dificuldade:** 🟠 Alta
- **Estimativa:** 1-2 dias
- **Status:** ⏳ Planejado

### ME-04: Implementar Loading States e Skeleton Screens
- **Descrição:** Adicionar estados de carregamento para operações assíncronas e skeleton para carregamento de página.
- **Motivo:** Sem feedback visual durante carregamento (perceção de lentidão).
- **Benefício:** Performance percebida muito melhor.
- **Impacto:** 🟡 Médio
- **Dificuldade:** 🟡 Média
- **Estimativa:** 1 dia
- **Status:** ⏳ Planejado

### ME-05: Otimizar Empty States
- **Descrição:** Padronizar empty states com ilustração, mensagem contextual e CTA.
- **Motivo:** Empty states atuais são texto simples sem guia para ação.
- **Benefício:** UX guiada, redução de abandono.
- **Impacto:** 🟡 Médio
- **Dificuldade:** ⬜ Baixa
- **Estimativa:** 4 horas
- **Status:** ⏳ Planejado

---

## 🟢 P3 — REFATORAÇÕES FUTURAS

### RF-01: Migrar para Component-First Architecture
- **Descrição:** Converter views em composições de componentes Blade reutilizáveis.
- **Status:** ⏳ Planejado

### RF-02: Implementar Lazy Loading de Gráficos
- **Descrição:** Carregar Chart.js apenas em páginas com gráficos, via dynamic import.
- **Status:** ⏳ Planejado

### RF-03: Implementar Breadcrumbs
- **Descrição:** Adicionar breadcrumb navigation para todas as páginas internas.
- **Status:** ⏳ Planejado

### RF-04: Accessibility Audit (WCAG 2.1 AA)
- **Descrição:** Auditoria completa de acessibilidade e correções.
- **Status:** ⏳ Planejado

### RF-05: Implementar PWA Offline-First
- **Descrição:** Cache assets via service worker, formulários offline.
- **Status:** ⏳ Planejado

---

## Ordem de Implementação Sugerida

```
Ciclo 1: Quick Wins (P0)
  → QW-01 + QW-02 + QW-03 + QW-04 + QW-05

Ciclo 2: Performance & Organização (P1)
  → MI-01 + MI-02 + MI-03

Ciclo 3: Design System Foundation (P1)
  → MI-04 + MI-05 + MI-06 + MI-07

Ciclo 4: Estrutura (P2)
  → ME-01 + ME-02 + ME-03

Ciclo 5: UX Polish (P2)
  → ME-04 + ME-05

Ciclo 6+: Futuro (P3)
  → RF-01 → RF-05
```
