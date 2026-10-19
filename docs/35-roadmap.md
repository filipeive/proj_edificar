# ROADMAP — Life Church Management System

> **Data:** 2026-07-10 | **Responsável:** Chief Digital Transformation Architect

---

## 1. Fase 1: Correções Imediatas (Curto Prazo — Estabilização)

### 1.1 Correção dos Testes Automatizados (PHPUnit) — ✅ Concluído
- **Ação:** Refatorar a migração do Timóteo (`2026_02_17_102103_add_timoteo_role_to_users_table.php`) e semelhantes para usar condicionais de driver (`if (DB::getDriverName() === 'sqlite') { return; }`), permitindo que a base de dados SQLite em memória corra sem erros durante os testes.
- **Estado:** ✅ Concluído. Todas as 5 migrações com instruções raw MySQL foram protegidas e a suíte PHPUnit corre com 100% de sucesso.

### 1.2 Rate Limiting em Formulários Públicos — ✅ Concluído
- **Ação:** Adicionar o middleware `throttle` (ex: `throttle:10,1` - 10 submissões por minuto) nas rotas de cadastro de casamentos, cursos e relatórios trimestrais públicos para prevenir ataques de spam.
- **Estado:** ✅ Concluído. Middleware ativo em todas as rotas públicas de submissão e validado por testes de feature.

---

## 2. Fase 2: Otimizações de Performance (Médio Prazo)

### 2.1 Processamento em Fila (Queues)
- **Ação:** Configurar a tabela de `jobs` na base de dados e migrar o envio de SMS (httpSMS) e e-mails para execução assíncrona.
- **Esforço:** Médio.

### 2.2 Eager Loading e Otimização de Dashboards — ✅ Concluído
- **Ação:** Eliminar as queries N+1 identificadas no `AdminDashboardController` e no `UserCommitment` através da pré-carga de relações e contagens (`with` e `withCount`).
- **Estado:** ✅ Concluído. Queries agregadas no `AdminDashboardController` e suporte a `user.contributions` pré-carregadas em `UserCommitment`.

### 2.3 Ajuste no Limpador de Cache — ✅ Concluído
- **Ação:** Alterar o método `Setting::clearCache()` para expirar apenas chaves com prefixo `setting.` em vez de executar o `Cache::flush()` total, protegendo as sessões ativas dos utilizadores.
- **Estado:** ✅ Concluído. O método agora expira pontualmente apenas as chaves de settings registradas, preservando o cache global e sessões.

---

## 3. Fase 3: Qualidade de Código e Refatoração (Longo Prazo)

```mermaid
gantt
    title Evolução Arquitetural
    dateFormat  YYYY-MM-DD
    section Fase 1
    Testes e Segurança      :done, a1, 2026-07-10, 5d
    section Fase 2
    Filas & Queues          :active, after a1, 10d
    Eager Loading & Cache   : 5d
    section Fase 3
    Refatorar Controllers   :20d
    Soft Deletes            :7d
```

### 3.1 Refatoração de Controllers Monolíticos
- **Ação:** Extrair a lógica de cálculo financeiro e exportação de relatórios do `ServiceController` e `ContributionController` para classes de Action (Single Responsibility Pattern) e mover validações para Form Requests.
- **Esforço:** Alto.

### 3.2 Implementação de Soft Deletes
- **Ação:** Adicionar suporte a eliminação lógica (`SoftDeletes`) nos modelos `User`, `Cell`, `Supervision` e `Zone`, garantindo a integridade referencial nas tabelas de contribuições financeiras.
- **Esforço:** Médio.

### 3.3 Remoção de HTML de Models — ✅ Concluído
- **Ação:** Transferir métodos de representação de ecrã (como `status_badge` no model `Visitor`) para componentes Blade ou classes Presenters dedicadas.
- **Estado:** ✅ Concluído. Criado o componente `<x-visitor-status-badge />` e desacoplado o HTML do modelo `Visitor`.
