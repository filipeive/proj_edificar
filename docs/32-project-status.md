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

> [!CAUTION]
> ### Falha Crítica na Execução de Testes (PHPUnit)
> Ao correr os testes automatizados da aplicação com o comando `vendor/bin/phpunit`, **25 de 26 testes falham** devido a um erro de migração da base de dados:
> 
> ```
> PDOException: SQLSTATE[HY000]: General error: 1 near "MODIFY": syntax error
> ```
> 
> **Origem do Erro:**
> O ficheiro de configuração `phpunit.xml` está definido para correr testes em memória usando o driver **SQLite**:
> ```xml
> <env name="DB_CONNECTION" value="sqlite"/>
> <env name="DB_DATABASE" value=":memory:"/>
> ```
> No entanto, a migração [2026_02_17_102103_add_timoteo_role_to_users_table.php](file:///home/fdev-ms/Filipe/proj_edificar/database/migrations/2026_02_17_102103_add_timoteo_role_to_users_table.php) (e potencialmente outras migrações que alteram enums) utiliza instruções de SQL nativo específicas para **MySQL**:
> ```php
> DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(...)");
> ```
> Como o SQLite não suporta o comando `MODIFY COLUMN` nem o tipo `ENUM`, a migração falha e impede que os testes sequer comecem a correr.
> 
> **Resolução Recomendada (Sem alterar código nesta fase):**
> 1. Modificar o kernel de testes para usar uma base de dados MySQL dedicada a testes (`_testing`).
> 2. Alternativamente, usar um condicional de driver nas migrações:
>    ```php
>    if (DB::getDriverName() === 'mysql') {
>        DB::statement("ALTER TABLE users MODIFY COLUMN...");
>    }
>    ```

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
