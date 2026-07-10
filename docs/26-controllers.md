# CONTROLLERS — Life Church Management System

> **Data:** 2026-07-10 | **Total:** 44 Controllers (12 Admin, 9 Auth, 8 Dashboard, 1 Contribution, 1 Report, 13 Gerais)

---

## 1. Visão Geral por Directório

Os controllers estão divididos em sub-directórios temáticos sob `app/Http/Controllers/`:

```
app/Http/Controllers/
├── Admin/                     # 12 Controllers de administração de recursos
├── Auth/                      # 9 Controllers do scaffolding de login/registo
├── Contribution/              # 1 Controller do workflow de contribuições
├── Dashboard/                 # 8 Controllers de ecrãs iniciais customizados
├── Report/                    # 1 Controller de relatórios consolidados
└── (Root)                     # 13 Controllers gerais e públicos
```

---

## 2. Controllers Core / Públicos (Root)

### 2.1 [WelcomeController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/WelcomeController.php)
- **Responsabilidade:** Renderiza a página pública inicial da igreja.
- **Métodos:** `index()`
- **Dados Injetados/Carregados:** Carrega informações públicas da igreja das configurações (`Setting`).

### 2.2 [SetupController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/SetupController.php)
- **Responsabilidade:** Assistente de configuração inicial do sistema (Setup Wizard).
- **Métodos:** `index()`, `step1()`, `step2()`, `step3()`, `complete()`, `uploadLogo()`
- **Fluxo:** Configura nome da igreja, cria o utilizador `super_admin` inicial, carrega logotipo e marca as configurações como concluídas.

### 2.3 [PublicCourseController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/PublicCourseController.php)
- **Responsabilidade:** Formulários públicos de inscrição para cursos ministeriais e turmas de casais.
- **Métodos:** `register()`, `store()`, `showCasaisForm()`, `storeCasaisEnrollment()`
- **Modelos:** `Course`, `CourseClass`, `CourseEnrollment`, `CoupleEnrollment`

### 2.4 [PublicFormController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/PublicFormController.php)
- **Responsabilidade:** Inscrições abertas para casamentos, cursos pré-maritais e formulários ministeriais.
- **Métodos:** `showPreMaritalForm()`, `storePreMarital()`, `showMinisterialForm()`, `storeMinisterialForm()`, `showQuarterlyReportForm()`, `storeQuarterlyReport()`

### 2.5 [VisitorController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/VisitorController.php)
- **Responsabilidade:** Acompanhamento e integração de novos convertidos e visitantes.
- **Métodos:** `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`, `assignToZone()`, `assignToCell()`, `markAsContacted()`, `export()`, `bulkDestroy()`
- **Notificação:** Dispara um SMS ao líder da célula quando um visitante é atribuído.

### 2.6 [ServiceController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/ServiceController.php)
- **Responsabilidade:** Registo financeiro e presenças de cultos (Celebração e Ensino).
- **Tamanho:** ~33KB (Contém lógica de dízimos, ofertas por tipo, e presenças por zona para cultos de ensino).
- **Métodos:** CRUD regular + `createTeaching()`, `editTeaching()`, `downloadPdf()`, `report()`, `exportMonthly()`, `exportQuarterly()`, `exportCustom()`, `exportAnnual()`, `bulkDestroy()`

### 2.7 [CellMeetingController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/CellMeetingController.php)
- **Responsabilidade:** Registo dos relatórios semanais de encontros de células.
- **Tamanho:** ~27KB
- **Métodos:** CRUD regular + `export()`, `downloadPdf()`, `sendEmail()`, `bulkDestroy()`

### 2.8 [QuarterlyReportController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/QuarterlyReportController.php)
- **Responsabilidade:** Geração dos relatórios consolidados trimestrais de saúde celular.
- **Métodos:** CRUD regular + `export()`, `exportAnnual()`, `exportPdf()`, `bulkDestroy()`

### 2.9 [CourseController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/CourseController.php)
- **Responsabilidade:** Gestão do catálogo de cursos da escola ministerial.
- **Métodos:** CRUD regular + `exportGlobalReport()`, `bulkDestroy()`, `assignPublicEnrollment()`

### 2.10 [CourseClassController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/CourseClassController.php)
- **Responsabilidade:** Gestão de turmas de formação, docentes, presenças e avaliações.
- **Métodos:** CRUD regular + `upcomingWeddings()`, `exportAll()`, `exportPdf()`, `attendance()`, `storeAttendance()`, `addEnrollment()`, `removeEnrollment()`, `meetings()`, `report()`, `exportReport()`, `move()`, `bulkDestroy()`

### 2.11 [CourseEnrollmentController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/CourseEnrollmentController.php)
- **Responsabilidade:** Controlo de matrículas e status de alunos.
- **Métodos:** CRUD + `bulkDestroy()`, `assignClass()`, `enroll()`, `updateStatus()`

### 2.12 [CoupleEnrollmentController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/CoupleEnrollmentController.php)
- **Responsabilidade:** Cadastro de casais para formação pré-marital e casamentos.
- **Métodos:** CRUD + `updateStatus()`, `assignClass()`, `export()`

### 2.13 [MinisterialEnrollmentController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/MinisterialEnrollmentController.php)
- **Responsabilidade:** Cadastro de candidatos aos cursos de ministérios.
- **Métodos:** CRUD + `convertToUser()`

### 2.14 [SearchController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/SearchController.php)
- **Responsabilidade:** Pesquisa AJAX global e instantânea de membros, células e zonas.

---

## 3. Controllers Administrativos (`Admin/`)

### 3.1 [UserController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/UserController.php)
- **Responsabilidade:** Gestão de utilizadores, papéis (roles), logs de atividade e alterações de célula.
- **Tamanho:** ~26KB
- **Métodos:** CRUD regular + `members()`, `createFromContext()`, `storeFromContext()`, `showFromContext()`, `editFromContext()`, `updateFromContext()`, `destroyFromContext()`, `bulkDestroy()`, `bulkDestroyFromContext()`, `resetPassword()`, `reassignCell()`, `removeFromCell()`, `updateObservations()`, `toggleStatus()`, `activity()`

### 3.2 [PackageController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/PackageController.php)
- **Responsabilidade:** Gestão dos Pacotes de Compromisso financeiro da Campanha Edificar.
- **Tamanho:** ~28KB
- **Métodos:** CRUD regular + `assignMember()`, `updateMember()`, `sendBulkSms()`, `storeQuickMember()`, `sendMemberSms()`, `export()`, `exportPdf()`, `whatsappExport()`, `removeMember()`, `changePackage()`, `bulkRemoveMembers()`

### 3.3 [ZoneController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/ZoneController.php)
- **Responsabilidade:** Gestão de Zonas da igreja, fusão (merge) de zonas e pastores.
- **Métodos:** CRUD + `merge()`, `processMerge()`, `bulkDestroy()`

### 3.4 [SupervisionController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/SupervisionController.php)
- **Responsabilidade:** Gestão de Supervisões, atribuição a zonas, supervisores e sub-supervisores.
- **Métodos:** CRUD + `merge()`, `processMerge()`, `reassignZone()`, `bulkDestroy()`, `storeQuickSupervisor()`

### 3.5 [CellController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/CellController.php)
- **Responsabilidade:** Gestão de Células, líderes de células e fusões de estruturas.
- **Métodos:** CRUD + `reassignSupervision()`, `assignTimoteo()`, `downloadPdf()`, `bulkDestroy()`

### 3.6 [SettingController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/SettingController.php)
- **Responsabilidade:** Configurações globais do sistema, cópias de segurança (backups) e templates de comunicação.
- **Métodos:** `index()`, `update()`, `uploadLogo()`, `resetToDefaults()`, `backup()`, `downloadBackup()`

### 3.7 [RequisitionController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/RequisitionController.php)
- **Responsabilidade:** Workflow de aprovação de requisições financeiras para despesas de ministérios.
- **Métodos:** CRUD + `approve()`, `reject()`

### 3.8 [ExpenseController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/ExpenseController.php)
- **Responsabilidade:** Registo e controlo de despesas efetivas da igreja.

### 3.9 [WeddingController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/WeddingController.php)
- **Responsabilidade:** Calendário de casamentos e logística associada.
- **Métodos:** CRUD + `feed()`, `downloadPdf()`, `bulkDestroy()`, `testEmail()`

### 3.10 [EdificarDashboardController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/EdificarDashboardController.php)
- **Responsabilidade:** Ecrã de análise de métricas financeiras da Campanha de Obras Edificar.

### 3.11 [EventTypeController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/EventTypeController.php)
- **Responsabilidade:** Gestão de categorias de eventos.

### 3.12 [PublicFormSettingController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Admin/PublicFormSettingController.php)
- **Responsabilidade:** Habilitação/desabilitação de inscrições públicas.

---

## 4. Controllers de Dashboard (`Dashboard/`)

Cada ecrã inicial é processado por um controller de invocação única (`__invoke` ou com index), filtrando métricas específicas de acordo com o papel:

| Controller | Ecrã e Métricas Relevantes |
|------------|----------------------------|
| `AdminDashboardController` | Estatísticas gerais da igreja, dízimos de cultos, novos membros, gráficos de crescimento de zonas. |
| `PastorDashboardController` | Métricas da zona atribuída, relatórios pendentes, contribuições da zona. |
| `SupervisorDashboardController` | Células da supervisão, total de membros ativos, reuniões de célula concluídas. |
| `LiderDashboardController` | Presenças na célula, novos visitantes na célula, contribuições dos membros da célula. |
| `MemberDashboardController` | Compromisso pessoal, histórico de dízimos do membro, links de cursos disponíveis. |
| `SecretaryDashboardController` | Visitantes pendentes de contacto, aniversariantes do mês, cultos sem relatório. |
| `AdministracaoDashboardController` | Estatísticas administrativas consolidadas de inventário e cultos. |
| `PackageManagerDashboardController` | Progresso de arrecadação do pacote financeiro sob gestão. |

---

## 5. Controllers de Autenticação (`Auth/`)

São controllers padrão do **Laravel Breeze**, modificados para suportar o direcionamento de roles no logout e a ativação/desativação de contas (`is_active` check no login):

- **AuthenticatedSessionController:** Trata login (com validação se o utilizador está ativo) e logout.
- **RegisteredUserController:** Cadastro inicial de novos utilizadores.
- **ConfirmablePasswordController / PasswordController / NewPasswordController / PasswordResetLinkController:** Fluxos de segurança de palavras-passe.
- **VerifyEmailController / EmailVerificationPromptController / EmailVerificationNotificationController:** Gestão de confirmação de email.

---

## 6. Controllers Especiais de Domínio

### 6.1 [ContributionController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Contribution/ContributionController.php)
- **Responsabilidade:** Core financeiro do sistema. Gere submissão de comprovativos, validações, cancelamentos e download de recibos.
- **Métricas:** Controla o ciclo do 20 ao 5 de cada mês.
- **Workflow:** `store()` cria pendente → `verify()` valida → `reject()` recusa → `cancel()` anula.

### 6.2 [ReportController](file:///home/fdev-ms/Filipe/proj_edificar/app/Http/Controllers/Report/ReportController.php)
- **Responsabilidade:** Agregação de dados para os PDFs e planilhas Excel consolidadas.
- **Métodos:** `cellReport()`, `supervisionReport()`, `zoneReport()`, `globalReport()`, `exportPdf()`, `exportExcel()`

---

## 7. Análise de Dívida Técnica nos Controllers

> [!WARNING]
> ### Controllers Monolíticos e Complexos
> 
> 1. **`ServiceController` (~33KB) e `ContributionController` (~32KB):**
>    - Ambos contêm muita lógica de negócio inline que deveria estar na camada de serviços (ex: cálculo de dízimos agregados, geração de PDFs, movimentação de ficheiros físicos).
>    - A validação de ficheiros de comprovativo é feita inline.
> 
> 2. **`PackageController` (~28KB):**
>    - Contém lógica para exportações especializadas de PDF e mensagens estruturadas para WhatsApp e SMS. Deveria ser refatorado usando classes de Action.
> 
> 3. **Ausência de Form Requests:**
>    - Quase todas as validações de dados são feitas diretamente dentro dos métodos dos controllers usando `$request->validate([...])`. Isto infla os métodos e dificulta a reutilização das regras de validação.
