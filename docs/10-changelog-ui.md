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

## [2026-07-10] — Ciclo 2: Redesign Premium UI/UX (UIProMax)

### ME-02: Alinhamento de Design System e Fontes ✔️
- **Ficheiros alterados:** `tailwind.config.js`, `layouts/partials/head.blade.php`, `layouts/partials/header.blade.php`, `layouts/sidebar.blade.php`
- **Alteração:**
  - Configuração das fontes `Jost`, `Bodoni Moda` e `Outfit` no `tailwind.config.js` e importação via Google Fonts em `head.blade.php`.
  - Correção de overflow na sidebar colapsada: os submenus dropdown agora são explicitamente ocultados quando a sidebar está colapsada (`width: 80px`).
  - Alinhamento de cores do cabeçalho (search inputs, notifications, profile e avatares) para usar realces e anéis de foco laranja (`#F97316`) da marca em vez de azul desatualizado.
- **Benefício:** Identidade de marca alinhada, visual premium de alta fidelidade e correção de bugs na navegação.

### ME-03: Refatoração Bento Grid e Otimização de Dashboards ✔️
- **Ficheiros alterados:** `dashboard/admin.blade.php`, `dashboard/lider.blade.php`, `dashboard/membro.blade.php`, `dashboard/pastor.blade.php`, `dashboard/supervisor.blade.php`, `dashboard/secretaria.blade.php`, `dashboard/administracao.blade.php`
- **Alteração:**
  - Padronização de cantos arredondados de cartões e tabelas para `rounded-2xl` corporativo de alta-fidelidade.
  - Tornou as métricas de topo e Bento Grids responsivos e visíveis em ecrãs móveis (removendo `hidden md:grid` e implementando grelhas fluidas de 2 ou 3 colunas no mobile).
  - Unificação de cores secundárias e realces (azuis, roxos, verdes) para o laranja da marca e Stone.
  - Atualização dos tons do Chart.js para gradientes e linhas laranja da marca.
- **Benefício:** Consistência visual uniforme em todos os 7 dashboards do sistema.

### ME-04: Alpine.js Tabs na Ficha Guia e Scroll Horizontal Premium ✔️
- **Ficheiros alterados:** `admin/cells/attendance.blade.php`, `resources/css/layout.css`
- **Alteração:**
  - Restruturação completa da Ficha Guia de Célula: implementação de abas interativas do Alpine.js (`Controle de Presença`, `Visitas e Decisões`, `Acompanhamento & Discipulado`) para maior densidade e melhor arranjo espacial das informações.
  - Otimização do scroll horizontal da tabela de presença com gradiente fade-out (utilizando a classe `.table-responsive-shadows` refinada com `transparent` em vez de cor estática para compatibilidade 100% com o tema escuro).
  - Uniformização de modais (Visitas, Discipulado, Decisões) para cores da marca e layout de cantos `rounded-2xl`.
- **Benefício:** UX e usabilidade móvel drasticamente melhorada para o registo semanal de presença e acompanhamento de visitantes pelos líderes de célula.

### ME-05: Ajustes Responsive Mobile e Novas Funcionalidades de Gestão ✔️
- **Ficheiros alterados:** `admin/cells/show.blade.php`, `admin/cells/attendance.blade.php`, `admin/supervisions/show.blade.php`, `admin/cells/index.blade.php`, `members/index.blade.php`, `app/Http/Controllers/Admin/CellController.php`, `app/Http/Controllers/Admin/UserController.php`, `routes/web.php`
- **Alterações:**
  - **Redesign Mobile (Cells Show):** Implementação de visualização responsive móvel em formato de grelha de cartões nas tabelas de Membros e Reuniões.
  - **Redesign Mobile (Supervision Show):** Stack vertical do header da secção no mobile e substituição da tabela de células por cartões touch-friendly responsivos.
  - **Ficha Guia (Attendance):** Títulos responsivos (com abreviaturas curtas em mobile) e correção do alinhamento do botão "Salvar".
  - **Gestão de Timóteos:** Extensão do modal "Escolher Novo Timóteo" (agora "Gestão de Timóteos") para permitir tanto a promoção a Timóteo como a remoção imediata desta função na listagem de células.
  - **Atribuição de Células:** Adicionado botão verde com ícone de grupo e modal "Atribuir Célula" na listagem de membros para utilizadores sem célula associada, permitindo a sua atribuição rápida.
- **Benefício:** Acessibilidade, consistência e capacidade de gestão completa a partir de dispositivos móveis.

## [2026-07-16] — Ciclo 3: Gestão de Visitas, Acompanhamento e Permissões do Líder

### ME-06: Acompanhamento de Visitantes e Ajustes de UI/UX do Líder ✔️
- **Ficheiros alterados:** `resources/views/admin/cells/show.blade.php`, `app/Http/Controllers/VisitorController.php`, `routes/web.php`, `resources/views/layouts/sidebar.blade.php`, `app/Http/Controllers/Admin/UserController.php`, `resources/views/members/index.blade.php`, `resources/views/members/show.blade.php`
- **Alterações:**
  - **Acompanhamento & Feedback de Visitantes:** Adicionado botão de feedback (ícone de chat) e modal interativo para registo de estado de contacto e notas de visitas na célula, com rotas e permissões de acesso associadas aos líderes de célula (`lider_celula`).
  - **Ocultação de Ações Inconsistentes:** O botão "Tornar Membro" para visitantes com status "Sem Interesse" foi removido da listagem.
  - **Correção de Sidebar:** Habilitação do menu "Visitantes" para o líder e ocultação da secção "Formulários Públicos" (Inscrição Pré-Marital e Relatório Trimestral) para líderes e membros.
  - **Restrição de Edição de Si Mesmo:** Ocultação de botões de edição de si mesmo na gestão de membros e bloqueio no backend (403), permitindo apenas a visualização.
- **Benefício:** Maior consistência de permissões e segurança operacional, evitando a autodegradação de cargos ou reatribuições acidentais de célula.

### ME-07: Integração de Cultos de Ensino por Zonas (Mapeamento & Relatórios) ✔️
- **Ficheiros alterados:** `app/Exports/ServicesExport.php`, `app/Http/Controllers/Api/V1/ServicesController.php`, `app/Http/Controllers/ServiceController.php`, `app/Http/Resources/ServiceResource.php`, `resources/views/services/create-teaching.blade.php`, `resources/views/services/edit-teaching.blade.php`, `resources/views/services/pdf.blade.php`, `resources/views/services/report.blade.php`, `resources/views/services/show.blade.php`
- **Alterações:**
  - **Estatísticas Dinâmicas no API Resource:** O `ServiceResource` foi ajustado para somar os valores das participações de zonas ministeriais (`zoneParticipations`) quando o tipo de culto for `teaching` (Ensino/Doutrina), calculando dinamicamente membros de adultos, visitantes de adultos, membros de crianças e visitantes de crianças.
  - **Exportação & Relatórios Consolidados:** Integração dos cálculos dinâmicos por zonas em `ServicesExport` e carregamento das relações de zonas nos controllers.
  - **Geração de PDF de Culto de Ensino:** Refatoração completa do layout do PDF de culto de ensino para listar e somar com rigor as participações das diferentes zonas em formato tabular com totais.
- **Benefício:** Relatórios e exportações de dados qualitativos e quantitativos fiéis ao modelo de participação por Zonas Ministeriais nos cultos de doutrina/ensino.
