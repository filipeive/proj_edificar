# Portal Life Church — Documentação Técnica e Funcional Mestre

> Sistema de Gestão Eclesiástica e Financeira — Índice Geral da Biblioteca de Engenharia Reversa e Auditoria.

---

## 1. Ritual de Iniciação de Agentes (AI)

Se és um agente de IA a atuar neste repositório, lê este documento antes de iniciares o teu trabalho:

* [00 — Ritual de Iniciação para Agentes (Onboarding)](00-agente-ritual.md)

---

## 2. Engenharia Reversa do Sistema (Core)

Estes documentos contêm o mapeamento completo de toda a estrutura técnica do Portal Life Church (36 Models, 44 Controllers, 91 Migrations, 200+ Rotas).

| # | Documento | Conteúdo Principal |
|---|-----------|--------------------|
| 20 | [Visão Geral do Projeto](20-project-overview.md) | Objetivo do sistema, stack tecnológica detalhada, servidores e perfis de utilizadores (roles). |
| 21 | [Arquitetura do Sistema](21-architecture.md) | MVC detalhado, fluxo de request, diagramas Mermaid de camadas, policies, providers e exports. |
| 22 | [Esquema de Base de Dados](22-database.md) | Diagrama ER Mermaid completo, dicionário de tabelas, tipos de dados, chaves estrangeiras e índices. |
| 23 | [Mapeamento de Módulos](23-modules.md) | Detalhe funcional de cada um dos 19 módulos ativos (controllers, models, views e regras). |
| 24 | [Fluxo de Processos](24-system-flow.md) | Sequence & State diagrams Mermaid dos fluxos de login, contribuições, cultos, visitantes e inscrição. |
| 25 | [Ficheiro de Rotas](25-routes.md) | Mapeamento completo de todos os 225+ endpoints da aplicação com métodos, URLs e middlewares. |
| 26 | [Dicionário de Controllers](26-controllers.md) | Responsabilidades, injeção de dependências e responsabilidades de cada uma das 44 classes. |
| 27 | [Dicionário de Models](27-models.md) | Mapeamento de atributos, scopes, mutators, accessors e relações dos 36 Eloquent Models. |

---

## 3. Relatórios de Auditoria e Qualidade

Auditoria detalhada sobre a qualidade de código, segurança da informação e performance operacional da plataforma.

| # | Documento | Conteúdo Principal |
|---|-----------|--------------------|
| 28 | [Estrutura de Views](28-views.md) | Layouts base, componentes Blade premium, variáveis CSS (Design Tokens) e interações Alpine.js. |
| 29 | [Auditoria de Segurança](29-security.md) | Mecanismos de autenticação, dupla camada de autorização (Middleware/Policies), CSRF, logs e falhas OWASP. |
| 30 | [Auditoria de Performance](30-performance.md) | Pontos críticos de queries N+1, otimização de caching (Setting/Static), indexação de BD e sugestões de queues. |
| 31 | [Relatório UI & UX](31-ui-ux-report.md) | Estudo de responsividade mobile, acessibilidade para daltónicos e análise de 5 problemas críticos de usabilidade. |

---

## 4. Conclusões e Direcionamento Estratégico

Documentação estratégica para governança do projeto, dívida acumulada e planeamento de evolução técnica.

| # | Documento | Conteúdo Principal |
|---|-----------|--------------------|
| 32 | [Estado Atual do Projeto](32-project-status.md) | Estado da aplicação em produção, cobertura de recursos e a falha de migração nos testes automatizados. |
| 33 | [Mapeamento de Dívida Técnica](33-technical-debt.md) | Catálogo de acoplamento de código, controllers gordos, falta de Form Requests, enums inconsistentes e soft deletes. |
| 34 | [Registo de Decisões de Arquitetura (ADR)](34-architecture-decisions.md) | Registo formal de decisões sobre frontend (Tailwind/Alpine), autorização, ciclos de contribuição e SMS Strategy. |
| 35 | [Roadmap de Evolução](35-roadmap.md) | Linha cronológica de ações recomendadas a curto, médio e longo prazo para estabilização e refatoração. |

---

## 5. Documentação de Design e Redesign UI/UX (Histórico)

Preservação dos relatórios e logs gerados durante os ciclos anteriores de redesign visual da interface.

- [00 — Visão Geral UI](00-visao-geral.md)
- [01 — Arquitetura de UI](01-arquitetura-ui.md)
- [08 — Roadmap de UI](08-roadmap-ui.md)
- [09 — Decisões de Design (UI)](09-decisoes-de-design.md)
- [10 — Changelog de UI](10-changelog-ui.md)
- [13 — Checklist de Usabilidade](13-checklist-ux.md)
- [15 — Backlog UI](15-backlog-ui.md)
- [16 — Relatório de Auditoria Final (UI)](16-relatorio-auditoria-final.md)
- [17 — Integração e Configuração SMS](17-integracao-sms.md)

---

## Governança de Desenvolvimento

1. **Memória Operacional:** A pasta [`.ai/`](../.ai/) contém o histórico operacional e diretrizes do assistente de inteligência artificial.
2. **Branch de Trabalho:** Todas as evoluções futuras devem seguir as prioridades do [Roadmap](35-roadmap.md) e ser integradas via Pull Request aprovado.
