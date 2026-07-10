# VIEWS — Life Church Management System

> **Data:** 2026-07-10 | **Tecnologia:** Blade, Alpine.js, TailwindCSS (3.x)

---

## 1. Estrutura do Directório de Views

As views da aplicação estão organizadas em 25 directórios principais no caminho `resources/views/`:

```
resources/views/
├── admin/                     # 13 Módulos de gestão administrativa
│   ├── cells/                 # Células e Ficha Guia
│   ├── users/                 # Gestão de contas e permissões
│   └── ...
├── auth/                      # Formulários de Login/Registo (Breeze)
├── components/                # 20 Componentes Blade reutilizáveis
├── dashboard/                 # 7 Painéis principais por Role
├── layouts/                   # Templates base e componentes de navegação
│   ├── app.blade.php          # Main layout (desktop e mobile)
│   ├── sidebar.blade.php      # Barra lateral com navegação condicional
│   └── partials/              # head, header, flash-messages
├── public/                    # Páginas e formulários públicos
└── (módulos individuais)      # contributions, services, cell_meetings, etc.
```

---

## 2. Sistema de Layouts

O layout principal é estruturado em [app.blade.php](file:///home/fdev-ms/Filipe/proj_edificar/resources/views/layouts/app.blade.php), que implementa:

1. **Estado do Painel Lateral (`sidebarOpen`):** Gerido pelo Alpine.js e persistido no `localStorage`.
2. **Design Responsivo:** Painel lateral fixo em desktop (com transição suave de largura entre 80px e 280px) e painel lateral flutuante em mobile.
3. **Tooltip Global:** JavaScript nativo para mostrar tooltips flutuantes quando a barra lateral está recolhida.
4. **CSS Customizado (`layouts/partials/head`):** Importa as fontes (Inter/Outfit), Bootstrap Icons e o ficheiro de estilos compilado.

---

## 3. Componentes Blade Premium

O sistema utiliza componentes customizados para garantir consistência visual em toda a aplicação. Os principais são:

### 3.1 [badge.blade.php](file:///home/fdev-ms/Filipe/proj_edificar/resources/views/components/badge.blade.php)
- **Função:** Badges de estado estilizados com suporte a ícones.
- **Variantes:** `primary` (laranja), `secondary` (zinco), `success` (esmeralda), `danger` (vermelho), `warning` (âmbar), `info` (azul), `neutral` (cinza).

### 3.2 [button.blade.php](file:///home/fdev-ms/Filipe/proj_edificar/resources/views/components/button.blade.php)
- **Função:** Botão premium com suporte a estado de carregamento (`loading`) e variantes (fill, outline, ghost).

### 3.3 [card.blade.php](file:///home/fdev-ms/Filipe/proj_edificar/resources/views/components/card.blade.php)
- **Função:** Contentores com efeito de vidro (glassmorphism), bordas arredondadas e divisores subtis.

### 3.4 [text-input-premium.blade.php](file:///home/fdev-ms/Filipe/proj_edificar/resources/views/components/text-input-premium.blade.php)
- **Função:** Inputs com animações na borda ao focar, indicação de erros em vermelho e suporte a ícones internos.

### 3.5 [skeleton.blade.php](file:///home/fdev-ms/Filipe/proj_edificar/resources/views/components/skeleton.blade.php)
- **Função:** Skeleton loaders animados para ecrãs com carregamento assíncrono.

---

## 4. Integração CSS e Alpine.js

### 4.1 Tailwind CSS + Variáveis CSS
O sistema utiliza variáveis de CSS globais (`resources/css/layout.css`) para gerir tokens de design unificados:

- `--color-primary` (Laranja da marca)
- `--color-sidebar-bg` (Preto profundo para o painel lateral)
- `--font-sans` (Inter para o corpo do texto)
- `--font-title` (Outfit para títulos chamativos)

### 4.2 Alpine.js no Frontend
Alpine.js é amplamente utilizado nas views para:
- Controlo de modais de confirmação e formulários.
- Gestão de abas (Tabs) nos dashboards e relatórios.
- Filtros dinâmicos em tabelas.
- Animação de estados de carregamento nos botões de submissão.

---

## 5. Análise de Oportunidades e Dívida Técnica (Views)

> [!WARNING]
> ### Inconsistências e Falhas de Padrão
> 
> 1. **HTML retornado da lógica de Backend:**
>    - O model `Visitor` (`getStatusBadgeAttribute`) gera tags HTML diretas (`<span class="px-3 py-1 bg-yellow-50...">`). Isto viola o isolamento de camadas (MVC), pois alterações de estilo no status obrigam a mexer em código PHP de Model.
> 
> 2. **Mistura de Padrões de CSS:**
>    - Alguns ficheiros legados de views ainda contêm classes do Bootstrap (`d-none`, `badge-danger`) misturadas com TailwindCSS, o que pode causar inconsistências na compilação.
> 
> 3. **Tamanho excessivo da View `welcome.blade.php` (~95KB):**
>    - Contém toda a landing page pública da igreja num único ficheiro. Deveria ser modularizado em componentes Blade parciais (`layouts/partials/welcome/*`).
