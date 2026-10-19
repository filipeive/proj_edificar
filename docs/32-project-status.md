# PROJECT STATUS — Life Church Management System

> **Data:** 2026-07-10 | **Responsável:** Chief Digital Transformation Architect

---

## 1. Estado Geral da Aplicação

O sistema encontra-se na versão **1.x** (em produção e funcional) alojado na infraestrutura **Oracle Cloud**:

- **Produção:** Totalmente operacional para os utilizadores finais (Pastores, Líderes, Membros, Tesouraria).
- **Funcionalidades Core:** A gestão de membros, dízimos, cultos, encontros de célula e o workflow financeiro do Projeto Edificar estão a ser ativamente utilizados.
- **Visual:** O redesign completo de UI/UX (ciclos de 1 a 6) foi concluído com sucesso, restabelecendo a consistência visual com TailwindCSS, Outfit/Inter e componentes estilizados premium.

---

## 2. Estado dos Testes Automatizados (Critério de Aceitação)

> [!NOTE]
> ### Testes Automatizados (PHPUnit) — 100% Operacionais e Estáveis
> A suíte completa de testes corre agora com sucesso total via `vendor/bin/phpunit` com **79/79 testes aprovados (285 asserções, 0 falhas)**.
> 
> **Resolução Aplicada:**
> 1. Todas as migrações que executavam instruções nativas do MySQL (`ALTER TABLE users MODIFY COLUMN role ENUM(...)`) foram protegidas com verificação de driver (`if (DB::getDriverName() === 'sqlite') { return; }`), garantindo total compatibilidade tanto com MySQL quanto com SQLite.
> 2. O isolamento de cache em `Setting::clearCache()` foi corrigido e coberto por testes unitários e de integração.
> 3. Proteções de rate limiting nos formulários públicos e a extração do status badge de visitantes foram validadas.

---

## 3. Cobertura Funcional (Mapeamento de Módulos)

| Módulo | Estado de Desenvolvimento | Utilidade Prática |
|--------|---------------------------|-------------------|
| **Autenticação & Controlo de Acesso** | 🟢 100% Concluído | Login, logout, ativação/desativação de utilizadores e redirecionamentos automáticos por papel. |
| **Gestão de Células (Ficha Guia)** | 🟢 100% Concluído | Relatório de presenças, conversões e discipulados. |
| **Visitantes (SMS Integration)** | 🟢 100% Concluído | Registo de visitantes e envio de SMS automatizado via httpSMS para o líder atribuído. |
| **Workflow de Contribuições** | 🟢 100% Concluído | Envio de comprovativos de dízimos/ofertas pelos membros e validação pela equipa financeira. |
| **Escola Ministerial** | 🟢 100% Concluído | Gestão de turmas de casais, ministerial, pré-marital e controlo de assiduidade de alunos. |
| **Painel Financeiro Geral** | 🟢 100% Concluído | Lançamento de despesas, aprovação de requisições e visibilidade de balanços mensais. |
| **Relatórios Trimestrais** | 🟢 100% Concluído | Geração de relatórios com scores qualitativos preenchidos por supervisores. |
| **Cerimónias (Casamentos/Eventos)** | 🟢 90% Concluído | Gestão física de agendas. Notificações por email ainda em fase de testes manuais. |
| **Exportações de Dados (Excel/PDF)** | 🟢 100% Concluído | 14 classes de exportação ativas e relatórios gerados via DomPDF. |

---

## 4. Métricas de Complexidade do Código

- **Migrations acumuladas:** 91 migrações (indica um ciclo longo de desenvolvimento incremental).
- **Models de Base de Dados:** 36 classes ativas.
- **Controllers de Lógica:** 44 classes em uso.
- **Nº de Linhas de Rotas Web:** 517 linhas.
- **Views Blade Criadas/Alteradas:** ~150+ ficheiros Blade compilados.
