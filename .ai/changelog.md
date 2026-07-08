# Changelog — Governança AI

> Registro de todas as alterações implementadas pelo arquiteto e engenheiro de produto AI.

---

## [Ciclo 1: Quick Wins] — 2026-07-08

### Resolvido
- **Remoção de TailwindCSS CDN Runtime:** Substituído o script CDN externo do Tailwind CSS no layout base (`app.blade.php`) pela compilação estática oficial do Tailwind via Vite + PostCSS, gerando ganho massivo de performance de renderização inicial.
- **Eliminação de Flash Messages Duplicadas:** Removida a renderização redundante das mensagens de alertas (success/error/warning/info) que apareciam duas vezes na tela.
- **Correção de CSS da Sidebar:** Sanados conflitos de layout e classes da sidebar que prejudicavam a legibilidade.
- **Remoção de `x-cloak` duplicados:** Limpeza de diretivas Alpine.js desnecessárias no HTML.
- **Métricas no Mobile:** Ajustadas as métricas globais para serem exibidas corretamente em dispositivos mobile.

---

## [Ciclo 2: Performance & Organização] — 2026-07-08

### Resolvido
- **Extração de CSS Inline:** Removidas mais de 700 linhas de CSS inline do layout `app.blade.php`, organizando os estilos em `resources/css/layout.css` importados no entrypoint de build.
- **Extração de Javascript Inline:** Eliminadas todas as tags `<script>` inline do arquivo `app.blade.php`, movendo a lógica de PWA para `resources/js/pwa.js` e helpers de UI/SweetAlert para o arquivo modular `resources/js/layout.js`.
- **Compilação via Vite:** Os novos assets modulares JS e CSS foram vinculados ao Vite, testados e compilados (`npm run build`) para geração de builds limpos e performáticos.

---

## [Ciclo 3: Design System & Otimização de UI/Backend] — 2026-07-08

### Resolvido
- **Memoização no Model User (`app/Models/User.php`):**
  - Adicionado suporte a cache de instância em tempo de execução para os métodos `isLiderOfAnyCell()`, `getFirstLedCell()`, `getZoneId()`, `getManagedZoneIds()`, `getManagedSupervisionIds()` e `getPendingContributionsCount()`.
  - Evita queries repetitivas ao banco de dados sobre liderança, supervisão e contribuições no mesmo ciclo de requisição HTTP.
- **Otimização do View Composer (`app/Providers/AppServiceProvider.php`):**
  - Refatorado o View Composer global (`*`) para usar cache estático (`static $cachedData`).
  - Garante que a contagem global de notificações não lidas e contribuições pendentes seja executada **apenas uma vez** por requisição, independentemente do número de partials e subviews incluídas no layout.
- **Limpeza na Sidebar (`resources/views/layouts/sidebar.blade.php`):**
  - Removido o script inline de autoscroll ao item ativo e integrado diretamente no `resources/js/layout.js` no escopo do `DOMContentLoaded`.
  - Substituídas as queries diretas do banco e métodos redundantes por chamadas aos novos métodos memoizados do model `User`.
- **Implementação de Design Tokens (`resources/css/layout.css`):**
  - Definição centralizada de variáveis CSS (:root e dark mode) para cores primárias (marca), funcionais (success, danger, warning, info), shadows dinâmicas e border-radius.
- **Criação da Component Library Blade (`resources/views/components/`):**
  - Desenvolvida a biblioteca de componentes reutilizáveis contendo `button.blade.php`, `card.blade.php`, `badge.blade.php` e `text-input-premium.blade.php` seguindo os novos tokens visuais do projeto.
- **Tabelas Responsivas Mobile (`resources/css/layout.css`):**
  - Criadas classes `.table-responsive-container` e `.table-responsive-shadows` adicionando scroll horizontal suave e sombras dinâmicas de rolagem para melhorar a experiência em telas de celulares.

---

## [Ciclo 4: Arquitetura Limpa & Dark Mode] — 2026-07-08

### Resolvido
- **Refatoração do Layout Monolítico (`app.blade.php`):**
  - Reduzido o arquivo base de 279 linhas para menos de 50 linhas altamente semânticas e modulares.
  - Extraídos os blocos estruturais para parciais em `resources/views/layouts/partials/`:
    - `head.blade.php` (meta tags, favicons, imports e rotas globais do Laravel no JS).
    - `header.blade.php` (barra superior, barra de pesquisa, painel de notificações, toggle de tema e menu do utilizador).
    - `flash-messages.blade.php` (scripts de tratamento de alertas e toasts dinâmicos).
- **Refatoração do Dark Mode Nativo (`resources/js/layout.js`):**
  - Atualizadas as funções `toggleTheme()` e `initializeTheme()` para setar o atributo `data-theme="dark"` no elemento de topo do documento (`document.documentElement`).
  - Permite que o compilador do Tailwind ative nativamente e em tempo real as classes prefixadas com `dark:` (ex: `dark:bg-zinc-900`) sem a necessidade de hacks CSS `!important`.
- **Padronização de Formulários com Componentes Unificados:**
  - Refatorados os formulários de criação (`admin/users/create.blade.php`) e edição (`admin/users/edit.blade.php`) de utilizadores.
  - Substituídos os blocos redundantes de formulários de inputs pelos novos componentes Blade dinâmicos e elegantes (`<x-card>`, `<x-button>` e `<x-text-input-premium>`), gerando consistência e reduzindo centenas de linhas de código duplicadas.

---

## [Ciclo 5: UX & Estados da Aplicação] — 2026-07-08

### Adicionado
- **Componente Blade `<x-skeleton>` (`resources/views/components/skeleton.blade.php`):**
  - Criação de um componente premium para simulação de carregamento animado (Skeleton Loaders).
  - Suporta vários formatos (`text` com linhas configuráveis, `avatar`, `circle`, `rect`, `card` e `table` simulados).
- **Componente Blade `<x-empty-state>` (`resources/views/components/empty-state.blade.php`):**
  - Criação de componente elegante para exibir quando não há registros em buscas, listagens ou tabelas.
  - Inclui suporte a ícones Bootstrap, títulos personalizados, subtítulos motivacionais e botão de chamada à ação integrado.

### Resolvido
- **Estados de Carregamento nos Botões (`resources/views/components/button.blade.php`):**
  - Refatoração completa do componente de botão para suportar o estado `loading` dinâmico do Alpine.js ou valor fixo PHP.
  - Integra um spinner SVG rotativo nativo, desabilita cliques indesejados durante requisições e fornece transição visual limpa.
  - Adicionado suporte a renderização como link (`<a>`) quando o atributo `href` estiver presente.

---

## [Ciclo 6: Refatoração de Listagem & Navegação] — 2026-07-08

### Adicionado
- **Componente Blade `<x-breadcrumbs>` (`resources/views/components/breadcrumbs.blade.php`):**
  - Criação de um componente reutilizável para navegação estruturada.
  - Suporte a rotas e links dinâmicos e de fácil declaração, perfeitamente integrado ao design de cabeçalho e dark mode.

### Resolvido
- **Refatoração da Listagem de Utilizadores (`resources/views/admin/users/index.blade.php`):**
  - Redução maciça de 551 linhas para 392 linhas de código.
  - Substituição de tabelas e contêineres hardcoded por componentes modulares (`<x-breadcrumbs>`, `<x-card>`, `<x-button>`, `<x-badge>` e `<x-empty-state>`).
  - Adicionado suporte completo a rolagem horizontal suave no mobile (`.table-responsive-container` e `.table-responsive-shadows`).
  - Adaptação nativa de layouts e bordas com suporte a variantes `dark:` do TailwindCSS sem dependência de hacks CSS `!important`.



