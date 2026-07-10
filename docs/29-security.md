# SECURITY — Life Church Management System

> **Data:** 2026-07-10 | **Responsável:** Chief Digital Transformation Architect

---

## 1. Modelo de Autenticação

O sistema utiliza o scaffolding nativo do **Laravel Breeze** para a gestão do ciclo de vida das sessões:

- **Autenticação:** Baseada em sessões web tradicionais guardadas na base de dados (`sessions` table).
- **Verificação de Estado (`is_active`):** Durante o processo de login (`AuthenticatedSessionController`), o sistema valida se a coluna `is_active` do utilizador é `true`. Utilizadores inativos são rejeitados com uma mensagem apropriada.
- **Proteção contra Força Bruta:** Rate limiting nativo do Breeze aplicado na rota `/login` (máximo de 5 tentativas por minuto por IP/email).

---

## 2. Modelo de Autorização

O sistema possui uma autorização em duas camadas principais:

### 2.1 Middleware de Roles (`CheckRole.php`)
Aplicado ao nível das rotas no ficheiro `web.php` para bloquear o acesso geral a sub-secções inteiras da aplicação:

```php
Route::get('/admin/dashboard', AdminDashboardController::class)
    ->middleware('role:super_admin,admin,pastor_senior');
```

- **Mapeamento de Acesso:** O middleware verifica o método `$user->hasRole($role)` no model `User`.
- **Hierarquia Embutida:** No model `User`, os papéis `super_admin`, `admin` e `pastor_senior` ganham acesso automático (`return true`) em qualquer verificação de role.

### 2.2 Policies Granulares (9 Policies)
Implementadas no directório `app/Policies/` para controlo de acesso focado no recurso (Record-level authorization):

| Policy | Recurso | Regra de Negócio Crítica |
|--------|---------|-------------------------|
| `UserPolicy` | Utilizador | Líder de célula só pode visualizar membros da sua própria célula, e só pode editar se o utilizador de destino tiver papel `membro`. |
| `ContributionPolicy` | Contribuição | O utilizador só pode editar a sua própria contribuição se esta estiver com o status `pendente`. Admins podem editar sempre. |
| `CellPolicy` | Célula | Líderes de célula só podem gerir/visualizar a sua própria célula. Supervisores gerem as células da sua supervisão. |
| `QuarterlyReportPolicy` | Relatório Trimestral | Apenas supervisores da supervisão correspondente ou pastores da zona correspondente podem gerir relatórios. |

---

## 3. Validação e Higienização de Dados

### 3.1 Proteção contra Vulnerabilidades OWASP
- **SQL Injection:** Eloquent ORM é utilizado em toda a aplicação. As queries utilizam placeholders parametrizados automaticamente nas cláusulas `where` e `find`, mitigando injeção de SQL.
- **Cross-Site Scripting (XSS):** O Blade compila as directivas `{{ $variable }}` escapando automaticamente caracteres perigosos. 
- **CSRF (Cross-Site Request Forgery):** O middleware `VerifyCsrfToken` é executado globalmente nas rotas POST, PUT e DELETE.

### 3.2 Validação Customizada (`moz_phone`)
Criada uma extensão de validação em [AppServiceProvider](file:///home/fdev-ms/Filipe/proj_edificar/app/Providers/AppServiceProvider.php):
- Valida números de Moçambique: Aceita formato local (9 dígitos) ou formato internacional (12 dígitos iniciando por `258`).

---

## 4. Auditoria (Log de Atividades)

O sistema possui um mecanismo de auditoria ativa através da trait [LogsActivity](file:///home/fdev-ms/Filipe/proj_edificar/app/Models/Concerns/LogsActivity.php):

- **Como funciona:** O model `User` utiliza esta trait para interceptar alterações nos dados.
- **Registo:** Grava na tabela `user_activities` a ação (`login`, `create`, `update`, `delete`), o modelo afetado (`model_type` + `model_id`), o IP e o User Agent do utilizador responsável.

---

## 5. Vulnerabilidades e Fraquezas Identificadas

> [!WARNING]
> ### Problemas de Segurança a Corrigir
> 
> 1. **Falta de Rate Limiting em Formulários de Inscrição Públicos:**
>    - As rotas públicas `/inscricao-casais`, `/inscricao-pre-marital` e `/inscricao/{slug}` não possuem nenhum rate limiting. Um atacante pode fazer spam de falsas inscrições na base de dados, esgotando recursos (DoS) ou enchendo o storage.
> 
> 2. **Validação de Ficheiros de Comprovativo permissiva:**
>    - No `ContributionController`, a validação do upload do ficheiro aceita `mimes:jpg,jpeg,png,pdf` mas o limite de tamanho (`max:10240` - 10MB) é excessivo para comprovativos simples e facilita ataques de esgotamento de disco.
> 
> 3. **Ausência de HTTPS Forçado em Produção:**
>    - O ficheiro `.env` em produção aponta para `http://146.235.224.99/edificar/`. O tráfego sem criptografia TLS/SSL permite a interceção de palavras-passe em redes públicas (Sniffing).
