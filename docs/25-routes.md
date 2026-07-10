# ROUTES — Life Church Management System

> **Data:** 2026-07-10 | **Ficheiro:** `routes/web.php` (517 linhas) + `routes/auth.php`

---

## Resumo

| Tipo | Quantidade |
|------|-----------|
| Rotas Públicas (sem auth) | ~15 |
| Rotas Autenticadas | ~200+ |
| Rotas de Autenticação (Breeze) | ~10 |
| Resource Routes | 13 |
| **Total Estimado** | **~225+** |

---

## 1. Rotas Públicas

| Método | URL | Controller | Route Name |
|--------|-----|-----------|-----------|
| GET | `/` | WelcomeController@index | `welcome` |
| GET | `/cursos/{course:slug}/inscricao` | PublicCourseController@register | `public.courses.register` |
| POST | `/cursos/{course:slug}/inscricao` | PublicCourseController@store | `public.courses.store` |
| GET | `/inscricao-casais` | PublicCourseController@showCasaisForm | `public.courses.casais` |
| POST | `/inscricao-casais` | PublicCourseController@storeCasaisEnrollment | `public.courses.casais.store` |
| GET | `/inscricao-pre-marital` | PublicFormController@showPreMaritalForm | `public.forms.pre-marital` |
| POST | `/inscricao-pre-marital` | PublicFormController@storePreMarital | `public.forms.pre-marital.store` |
| GET | `/inscricao/{slug}` | PublicFormController@showMinisterialForm | `public.forms.ministerial` |
| POST | `/inscricao/ministerial` | PublicFormController@storeMinisterialForm | `public.forms.ministerial.store` |
| GET | `/relatorio-trimestral` | PublicFormController@showQuarterlyReportForm | `public.reports.quarterly` |
| POST | `/relatorio-trimestral` | PublicFormController@storeQuarterlyReport | `public.reports.quarterly.store` |
| GET | `/register` | RegisteredUserController@create | `register` |
| POST | `/register` | RegisteredUserController@store | `register.store` |

---

## 2. Setup Wizard (Sem Auth)

| Método | URL | Controller | Route Name |
|--------|-----|-----------|-----------|
| GET | `/setup` | SetupController@index | `setup.index` |
| POST | `/setup/step1` | SetupController@step1 | `setup.step1` |
| POST | `/setup/step2` | SetupController@step2 | `setup.step2` |
| POST | `/setup/step3` | SetupController@step3 | `setup.step3` |
| POST | `/setup/complete` | SetupController@complete | `setup.complete` |
| POST | `/setup/upload-logo` | SetupController@uploadLogo | `setup.upload-logo` |

---

## 3. Dashboards

| Método | URL | Middleware Role | Route Name |
|--------|-----|----------------|-----------|
| GET | `/dashboard` | auth | `dashboard` |
| GET | `/admin/dashboard` | super_admin,admin,pastor_senior | `dashboard.admin` |
| GET | `/pastor/dashboard` | pastor_zona | `dashboard.pastor` |
| GET | `/supervisor/dashboard` | supervisor,pastor_zona | `dashboard.supervisor` |
| GET | `/lider/dashboard` | lider_celula,supervisor,pastor_zona | `dashboard.lider` |
| GET | `/membro/dashboard` | membro,lider_celula,supervisor,pastor_zona | `dashboard.membro` |
| GET | `/secretaria/dashboard` | secretaria | `dashboard.secretaria` |
| GET | `/administracao/dashboard` | administracao | `dashboard.administracao` |
| GET | `/admin/packages/dashboard` | (pacotes middleware) | `packages.dashboard` |
| GET | `/project-edificar/dashboard` | super_admin,admin,comissao_obra,pastor_senior,administracao | `edificar.dashboard` |
| GET | `/financial-dashboard` | auth | `financial.dashboard` |

---

## 4. Notificações

| Método | URL | Route Name |
|--------|-----|-----------|
| GET | `/notifications/api` | `notifications.api.index` |
| GET | `/notifications` | `notifications.all` |
| POST | `/notifications/read` | `notifications.read` |
| GET | `/notifications/{id}/mark-read` | `notifications.mark-read` |
| DELETE | `/notifications/{id}` | `notifications.destroy` |
| POST | `/notifications/clear-read` | `notifications.clear-read` |
| GET | `/notifications/unread-count` | `notifications.unread-count` |
| POST | `/notifications/bulk-delete` | `notifications.bulk-delete` |

---

## 5. Membros

**Middleware:** `role:lider_celula,supervisor,pastor_zona,super_admin,admin,secretaria`

| Método | URL | Route Name |
|--------|-----|-----------|
| GET | `/members` | `members.index` |
| GET | `/members/create` | `members.create` |
| POST | `/members` | `members.store` |
| POST | `/members/bulk-destroy` | `members.bulk-destroy` |
| GET | `/members/{member}` | `members.show` |
| GET | `/members/{member}/edit` | `members.edit` |
| PUT | `/members/{member}` | `members.update` |
| DELETE | `/members/{member}` | `members.destroy` |

---

## 6. Visitantes

**Middleware:** `role:super_admin,admin,secretaria,pastor_zona,supervisor,administracao`

| Método | URL | Route Name |
|--------|-----|-----------|
| GET | `/visitors` | `visitors.index` |
| GET | `/visitors/export` | `visitors.export` |
| GET | `/visitors/create` | `visitors.create` |
| POST | `/visitors` | `visitors.store` |
| GET | `/visitors/{visitor}` | `visitors.show` |
| GET/PUT/DELETE | `/visitors/{visitor}/edit/update/destroy` | `visitors.*` |
| GET | `/visitors/api/cells-by-zone` | `visitors.cells-by-zone` |
| POST | `/visitors/{visitor}/assign-zone` | `visitors.assign-zone` |
| POST | `/visitors/{visitor}/assign-cell` | `visitors.assign-cell` |
| POST | `/visitors/{visitor}/mark-contacted` | `visitors.mark-contacted` |

---

## 7. Admin — Zonas, Supervisões, Células

### Zonas
**Read:** `role:super_admin,admin,pastor_zona,supervisor,secretaria,pastor,lider_celula,administracao`  
**Write:** `role:super_admin,admin,secretaria`

| Método | URL | Route Name |
|--------|-----|-----------|
| GET | `/admin/zones` | `zones.index` |
| GET | `/admin/zones/{zone}` | `zones.show` |
| GET/POST | `/admin/zones/create/store` | `zones.create/store` |
| GET/PUT | `/admin/zones/{zone}/edit/update` | `zones.edit/update` |
| GET/POST | `/admin/zones/{zone}/merge` | `zones.merge/process-merge` |
| DELETE | `/admin/zones/{zone}` | `zones.destroy` |
| DELETE | `/admin/zones/bulk-destroy` | `zones.bulk-destroy` |

### Supervisões
**Resource completo** + merge, reassign-zone, bulk-destroy, quick-supervisor

### Células
**Resource completo** + pdf, attendance, visitors, discipleships, conversions, reassign-supervision, assign-timoteo, bulk-destroy

---

## 8. Cultos (Services)

| Método | URL | Route Name |
|--------|-----|-----------|
| Resource | `/admin/services/*` | `services.*` |
| GET | `/admin/services/create-teaching` | `services.create-teaching` |
| GET | `/admin/services/{service}/edit-teaching` | `services.edit-teaching` |
| GET | `/admin/services/{service}/pdf` | `services.download-pdf` |
| GET | `/admin/services/report` | `services.report` |
| GET | `/admin/services/export/monthly` | `services.export.monthly` |
| GET | `/admin/services/export/monthly/excel` | `services.export.monthly.excel` |
| GET | `/admin/services/export/quarterly` | `services.export.quarterly` |
| GET | `/admin/services/export/annual` | `services.export.annual` |
| POST | `/admin/services/bulk-delete` | `services.bulk-delete` |

---

## 9. Contribuições

### Validação (Admin)
**Middleware:** `role:super_admin,admin,pastor_zona,comissao_obra`

| Método | URL | Route Name |
|--------|-----|-----------|
| GET | `/contributions/pending` | `contributions.pending` |
| POST | `/contributions/{id}/verify` | `contributions.verify` |
| POST | `/contributions/{id}/reject` | `contributions.reject` |
| POST | `/contributions/{id}/cancel` | `contributions.cancel` |
| DELETE | `/contributions/{id}` | `contributions.destroy` |

### Utilizador
| Método | URL | Route Name |
|--------|-----|-----------|
| GET | `/contributions` | `contributions.index` |
| GET/POST | `/contributions/create/store` | `contributions.create/store` |
| GET/PUT | `/contributions/{id}/edit/update` | `contributions.edit/update` |
| GET | `/contributions/{id}` | `contributions.show` |
| GET | `/contributions/{id}/receipt` | `contributions.receipt` |

---

## 10. Pacotes (Edificar)

**Middleware:** `role:super_admin,admin,secretaria,comissao_obra,responsavel_pacote,pastor_senior`

| Método | URL | Route Name |
|--------|-----|-----------|
| Resource | `/admin/packages/*` | `packages.*` |
| POST | `/admin/packages/{id}/assign` | `packages.assign` |
| POST | `/admin/packages/{id}/bulk-sms` | `packages.send-bulk-sms` |
| GET | `/admin/packages/{id}/export` | `packages.export` |
| GET | `/admin/packages/{id}/export-pdf` | `packages.export-pdf` |
| DELETE | `/admin/packages/{id}/members/{user}` | `packages.members.remove` |

---

## 11. Escola Ministerial

| Grupo | Route Names |
|-------|------------|
| Courses | `courses.*` (resource + export-global, bulk-delete) |
| Course Classes | `course-classes.*` (resource + attendance, meetings, report, export, move) |
| Course Enrollments | `course-enrollments.*` (resource + assign-class, enroll, status) |
| Couple Enrollments | `couple-enrollments.*` (CRUD + assign-class, export) |
| Ministerial Enrollments | `ministerial-enrollments.*` (CRUD + convert) |

---

## 12. Outros Módulos

| Módulo | Resource | Extras |
|--------|----------|--------|
| Events | `events.*` | feed (JSON), pdf, email, bulk-delete |
| Event Types | `event-types.*` | Resource simples |
| Cell Meetings | `cell-meetings.*` | export, bulk-destroy, pdf, email |
| Quarterly Reports | `quarterly-reports.*` | export, export-annual, export-pdf, bulk-destroy |
| Weddings | `weddings.*` | feed, pdf, bulk-delete, test-email |
| Requisitions | `requisitions.*` | approve, reject |
| Expenses | `expenses.*` | Resource simples |
| Inventory Items | `inventory-items.*` | Resource simples |
| Profile | `profile.*` | edit, update, destroy |
| Users | `users.*` | reset-password, reassign-cell, remove-from-cell, toggle-status, activity |
| Commitments | `commitments.*` | index, choose, current |
