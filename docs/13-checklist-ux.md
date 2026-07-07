# 13 — Checklist UX

> Checklist de verificação para cada tela/componente do sistema.

---

## Critérios Gerais

| # | Critério | Aplicação |
|---|---------|-----------|
| 1 | Informação mais importante visível sem scroll | Todas as telas |
| 2 | Ação principal clara e acessível | Formulários, listagens |
| 3 | Feedback visual para todas as ações | Botões, forms, links |
| 4 | Empty states com mensagem útil e CTA | Listagens, dashboards |
| 5 | Loading states durante operações | Formulários, AJAX |
| 6 | Mensagens de erro descritivas e acionáveis | Validação, API |
| 7 | Confirmação antes de ações destrutivas | Delete, cancel |
| 8 | Navegação de retorno clara (breadcrumb ou botão) | Todas as sub-páginas |
| 9 | Touch targets ≥ 44x44px em mobile | Botões, links, inputs |
| 10 | Contraste de texto ≥ 4.5:1 (WCAG AA) | Todo o texto |
| 11 | Tabelas com scroll indicator no mobile | Todas as tabelas |
| 12 | Formulários com labels visíveis (não placeholder-only) | Todos os inputs |

## Status por Módulo

| Módulo | Critérios OK | Critérios Falha | Score |
|--------|-------------|----------------|-------|
| Dashboard Admin | 7/12 | 5 | 58% |
| Contribuições | 8/12 | 4 | 67% |
| Membros | 8/12 | 4 | 67% |
| Cultos | 6/12 | 6 | 50% |
| Visitantes | 7/12 | 5 | 58% |
| Eventos | 7/12 | 5 | 58% |
| Login | 10/12 | 2 | 83% |
| Sidebar | 8/12 | 4 | 67% |

---

## Problemas Recorrentes

1. ❌ **Sem breadcrumbs** — Nenhuma página tem breadcrumb navigation
2. ❌ **Métricas ocultas em mobile** — Dashboard admin esconde KPIs em mobile
3. ❌ **Tabelas sem scroll hint** — Utilizador não sabe que pode scrollar
4. ❌ **Touch targets pequenos** — Botões de ação < 44px em mobile
5. ❌ **Empty states básicos** — Apenas texto, sem ilustração ou CTA
