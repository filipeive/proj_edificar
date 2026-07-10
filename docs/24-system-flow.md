# SYSTEM FLOW — Life Church Management System

> **Data:** 2026-07-10 | **Responsável:** Chief Digital Transformation Architect

---

## 1. Fluxo de Autenticação

```mermaid
sequenceDiagram
    participant U as Utilizador
    participant B as Browser
    participant MW as Middleware
    participant AC as AuthController
    participant DC as DashboardController
    participant DB as Database

    U->>B: Acede ao sistema
    B->>MW: GET /
    MW-->>B: Redirect → /login

    U->>B: Preenche credenciais
    B->>AC: POST /login
    AC->>DB: Verificar email + password
    DB-->>AC: User válido
    AC->>AC: Registar last_login_at
    AC->>AC: Registar UserActivity (login)
    AC-->>B: Redirect → /dashboard

    B->>DC: GET /dashboard
    DC->>DC: match(user.role)
    DC-->>B: Redirect → /admin/dashboard (ou outro)
```

### Mapa de Redirecionamento por Role

| Role | Dashboard Destino | Route Name |
|------|------------------|------------|
| `super_admin` / `admin` / `pastor_senior` | `/admin/dashboard` | `dashboard.admin` |
| `pastor_zona` | `/pastor/dashboard` | `dashboard.pastor` |
| `supervisor` | `/supervisor/dashboard` | `dashboard.supervisor` |
| `lider_celula` | `/lider/dashboard` | `dashboard.lider` |
| `membro` | `/membro/dashboard` | `dashboard.membro` |
| `secretaria` | `/secretaria/dashboard` | `dashboard.secretaria` |
| `tesouraria` | `/financial-dashboard` | `financial.dashboard` |
| `comissao_obra` | `/project-edificar/dashboard` | `edificar.dashboard` |
| `responsavel_pacote` | `/admin/packages/dashboard` | `packages.dashboard` |
| `administracao` | `/administracao/dashboard` | `dashboard.administracao` |

---

## 2. Fluxo de Contribuições (Edificar)

```mermaid
stateDiagram-v2
    [*] --> Pendente: Membro cria contribuição
    Pendente --> Verificada: Admin/PastorZona verifica
    Pendente --> Rejeitada: Admin/PastorZona rejeita
    Pendente --> Cancelada: Admin cancela
    Rejeitada --> Pendente: Membro pode re-submeter
    Verificada --> [*]: Contabilizada
    Cancelada --> [*]: Encerrada
```

```mermaid
sequenceDiagram
    participant M as Membro
    participant CC as ContributionController
    participant DB as Database
    participant N as Notifications
    participant A as Admin/PastorZona

    M->>CC: GET /contributions/create
    CC-->>M: Formulário (pacote, valor, comprovativo)
    M->>CC: POST /contributions
    CC->>DB: Criar contribuição (status=pendente)
    CC->>N: ContributionCreatedNotification → Membro
    CC->>N: ContributionPendingValidationNotification → Admin

    A->>CC: GET /contributions/pending
    CC-->>A: Lista de contribuições pendentes
    A->>CC: POST /contributions/{id}/verify
    CC->>DB: Update status → verificada
    CC->>N: ContributionVerifiedNotification → Membro
    CC->>N: ContributionVerifiedForManagerNotification → Gestor
```

### Ciclo Financeiro

O sistema usa um **ciclo mensal personalizado** para contribuições: **dia 20 do mês até dia 5 do mês seguinte**. Isto é implementado em:

- `User::getTotalContributedThisMonth()`
- `Cell::getTotalContributedThisMonth()`
- `Zone::getTotalContributedThisMonth()`
- `Contribution::scopeThisMonth()`

---

## 3. Fluxo de Cultos (Services)

```mermaid
sequenceDiagram
    participant S as Secretaria/Admin
    participant SC as ServiceController
    participant DB as Database

    S->>SC: GET /admin/services/create
    SC-->>S: Formulário (data, tipo, pregador, tema)
    S->>SC: POST /admin/services
    SC->>DB: Criar Service
    SC->>DB: Criar ServiceOfferings (por tipo)
    SC->>DB: Criar ServiceTithes (dízimos individuais)
    SC->>DB: Criar ServiceZoneParticipations (ensino)
    SC-->>S: Redirect com sucesso

    S->>SC: GET /admin/services/{id}
    SC-->>S: Detalhes + totais financeiros calculados

    S->>SC: GET /admin/services/report
    SC-->>S: Relatório com filtros (mensal/trimestral/anual)
    S->>SC: GET /admin/services/{id}/pdf
    SC-->>S: Download PDF do relatório
```

### Tipos de Culto

| Tipo | Código | Descrição |
|------|--------|-----------|
| 1º Culto | `1st` | Domingo manhã (1º) |
| 2º Culto | `2nd` | Domingo manhã (2º) |
| 3º Culto | `3rd` | Domingo tarde |
| 4º Culto | `4th` | Domingo noite |
| Especial | `special` | Cultos especiais |
| Ensino | `teaching` | Quarta-feira (com presenças por zona) |

---

## 4. Fluxo de Visitantes

```mermaid
sequenceDiagram
    participant Sec as Secretaria
    participant VC as VisitorController
    participant DB as Database
    participant SMS as httpSMS
    participant Lid as Líder de Célula

    Sec->>VC: POST /visitors (dados do visitante)
    VC->>DB: Criar Visitor (contact_status=pendente)
    
    alt Visitante atribuído a célula
        DB->>DB: Visitor::booted() → saved event
        DB->>SMS: notifyCellLeaderAboutAssignment()
        SMS-->>Lid: SMS com dados do visitante
    end

    Lid->>VC: POST /admin/cells/{cell}/visitors/{visitor}/feedback
    VC->>DB: Atualizar contact_status + notes
```

### Estados do Visitante

```mermaid
stateDiagram-v2
    [*] --> Pendente: Registado no culto
    Pendente --> Contatado: Líder faz contacto
    Contatado --> Integrado: Visitante integra célula
    Contatado --> SemInteresse: Visitante não quer continuar
    Pendente --> Integrado: Integração directa
```

---

## 5. Fluxo de Gestão de Membros

```mermaid
graph TD
    A[Admin/Secretaria cria utilizador] --> B{Atribuir célula?}
    B -->|Sim| C[Selecionar Zona → Supervisão → Célula]
    B -->|Não| D[Membro sem célula]
    C --> E[Membro pertence à célula]
    E --> F[Líder vê membro na Ficha Guia]
    E --> G[Membro pode criar contribuições]
    E --> H[Membro aparece nos relatórios]
    
    I[Admin/Pastor pode promover role] --> J{Novo role?}
    J -->|lider_celula| K[Atribuído como leader de Cell]
    J -->|supervisor| L[Atribuído como supervisor de Supervision]
    J -->|pastor_zona| M[Atribuído como pastor de Zone]
```

---

## 6. Fluxo da Hierarquia Eclesiástica

```mermaid
graph TD
    subgraph "Estrutura da Igreja"
        Igreja["Igreja (Life Church)"]
        Igreja --> Z1["Zona 1"]
        Igreja --> Z2["Zona 2"]
        Igreja --> ZN["Zona N"]
        
        Z1 --> S1["Supervisão 1.1"]
        Z1 --> S2["Supervisão 1.2"]
        
        S1 --> C1["Célula A"]
        S1 --> C2["Célula B"]
        
        C1 --> M1["Membro 1"]
        C1 --> M2["Membro 2"]
        C1 --> M3["Timóteo"]
        C1 --> L1["Líder"]
    end

    subgraph "Responsáveis"
        Z1 -.->|pastor_id| PZ["Pastor de Zona"]
        S1 -.->|supervisor_id| SUP["Supervisor"]
        S1 -.->|sub_supervisor_id| SSUP["Sub-Supervisor"]
        C1 -.->|leader_id| L1
        C1 -.->|timoteo_id| M3
    end
```

---

## 7. Fluxo de Relatórios Trimestrais

```mermaid
sequenceDiagram
    participant SUP as Supervisor
    participant QRC as QuarterlyReportController
    participant DB as Database
    participant PZ as Pastor de Zona
    participant ADM as Admin

    SUP->>QRC: GET /quarterly-reports/create
    QRC-->>SUP: Formulário (zona, supervisão, trimestre)
    SUP->>QRC: POST /quarterly-reports
    QRC->>DB: Criar QuarterlyReport (status=draft)
    
    SUP->>QRC: PUT /quarterly-reports/{id}
    QRC->>DB: Preencher métricas + scores
    QRC->>DB: Update status → submitted

    PZ->>QRC: GET /quarterly-reports
    QRC-->>PZ: Lista filtrada por zona

    ADM->>QRC: GET /quarterly-reports/export
    QRC-->>ADM: Excel com dados consolidados
```

---

## 8. Fluxo da Escola Ministerial

```mermaid
graph TD
    A[Admin cria Curso] --> B[Define slug, categoria, target_role]
    B --> C[Abre inscrições]
    C --> D{Tipo de inscrição}
    
    D -->|Individual| E[Utilizador inscreve-se via sistema]
    D -->|Público| F[Formulário público /cursos/slug/inscricao]
    D -->|Casais| G[Formulário /inscricao-casais]
    D -->|Ministerial| H[Formulário /inscricao/slug]
    D -->|Pré-Marital| I[Formulário /inscricao-pre-marital]
    
    E --> J[CourseEnrollment criado]
    F --> J
    G --> K[CoupleEnrollment criado]
    H --> L[MinisterialEnrollment criado]
    I --> K
    
    J --> M[Admin atribui a Turma]
    K --> M
    L --> M
    
    M --> N[Aulas e Presenças]
    N --> O[Relatório de Turma / Exportação]
```

---

## 9. Fluxo de Eventos

```mermaid
sequenceDiagram
    participant U as Utilizador
    participant EC as EventController
    participant DB as Database

    U->>EC: GET /events/create
    EC-->>U: Formulário (tipo, nome, data, zona)
    U->>EC: POST /events
    EC->>DB: Criar Event
    EC-->>U: Redirect com sucesso

    U->>EC: GET /events
    EC-->>U: Calendário + lista de eventos
    
    U->>EC: GET /events/feed (JSON)
    EC-->>U: Feed para calendário JS

    U->>EC: GET /events/{id}/pdf
    EC-->>U: PDF do evento
```

---

## 10. Fluxo do Painel Financeiro

```mermaid
graph TD
    A[FinancialDashboardController] --> B[Carregar Cultos do mês]
    A --> C[Carregar Contribuições verificadas]
    A --> D[Carregar Despesas]
    A --> E[Carregar Requisições]
    
    B --> F[Calcular Total Dízimos]
    B --> G[Calcular Total Ofertas por tipo]
    C --> H[Calcular Total Edificar]
    D --> I[Calcular Total Despesas]
    
    F --> J[Dashboard com gráficos]
    G --> J
    H --> J
    I --> J
    
    J --> K[Filtros: Mês + Ano]
    J --> L[Tabela detalhada]
    J --> M[Gráficos Chart.js]
```

---

## 11. Fluxo de Notificações

```mermaid
graph LR
    subgraph "Gatilhos"
        A1[Contribuição Criada]
        A2[Contribuição Verificada]
        A3[Contribuição Rejeitada]
        A4[Membro Criado]
        A5[Membro Adicionado a Célula]
        A6[Compromisso Escolhido]
        A7[Compromisso Expirando]
        A8[Promoção de Role]
        A9[Reset de Password]
    end

    subgraph "Canal"
        DB[(Database notifications)]
    end

    subgraph "Destino"
        B1[Membro]
        B2[Admin/PastorZona]
        B3[Gestor de Pacote]
    end

    A1 --> DB --> B1
    A2 --> DB --> B1
    A3 --> DB --> B1
    A4 --> DB --> B2
    A5 --> DB --> B1
    A6 --> DB --> B2
    A7 --> DB --> B1
    A8 --> DB --> B1
    A9 --> DB --> B1
```

### Preferências de Notificação

Cada utilizador pode configurar quais notificações quer receber via `notification_preferences` (JSON):

| Preferência | Default |
|------------|---------|
| `contribution_created` | ✅ |
| `contribution_pending_validation` | ✅ |
| `pending_contributions` | ✅ |
| `contribution_verified` | ✅ |
| `contribution_rejected` | ✅ |
| `commitment_chosen` | ✅ |
| `commitment_expiring` | ✅ |
| `member_created` | ✅ |
| `member_added_to_cell` | ✅ |
| `user_promoted` | ✅ |

---

## 12. Fluxo do Setup Wizard

```mermaid
sequenceDiagram
    participant U as Administrador
    participant SC as SetupController
    participant DB as Database

    U->>SC: GET /setup
    SC->>DB: Verificar Setting(system.setup_completed)
    
    alt Setup já concluído
        SC-->>U: Redirect → /dashboard
    else Setup pendente
        SC-->>U: Step 1 - Informações da Igreja
        U->>SC: POST /setup/step1 (nome, contactos)
        SC-->>U: Step 2 - Criar Admin
        U->>SC: POST /setup/step2 (nome, email, password)
        SC-->>U: Step 3 - Personalização
        U->>SC: POST /setup/step3 (cores, logo)
        SC-->>U: Step 4 - Finalização
        U->>SC: POST /setup/complete
        SC->>DB: Setting::set(system.setup_completed, true)
        SC-->>U: Redirect → /login
    end
```
