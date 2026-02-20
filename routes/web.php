<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\LiderDashboardController;
use App\Http\Controllers\Dashboard\MemberDashboardController;
use App\Http\Controllers\Dashboard\PastorDashboardController;
use App\Http\Controllers\Dashboard\SupervisorDashboardController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\SupervisionController;
use App\Http\Controllers\Admin\CellController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Contribution\ContributionController;
use App\Http\Controllers\CommitmentController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação (Breeze)
require __DIR__ . '/auth.php';

// Welcome Route
Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

// Public Course Enrollment
Route::get('/cursos/{course:slug}/inscricao', [\App\Http\Controllers\PublicCourseController::class, 'register'])->name('public.courses.register');
Route::post('/cursos/{course:slug}/inscricao', [\App\Http\Controllers\PublicCourseController::class, 'store'])->name('public.courses.store');
Route::get('/inscricao-casais', [\App\Http\Controllers\PublicCourseController::class, 'showCasaisForm'])->name('public.courses.casais');
Route::post('/inscricao-casais', [\App\Http\Controllers\PublicCourseController::class, 'storeCasaisEnrollment'])->name('public.courses.casais.store');
Route::get('/inscricao-pre-marital', [\App\Http\Controllers\PublicFormController::class, 'showPreMaritalForm'])->name('public.forms.pre-marital');
Route::post('/inscricao-pre-marital', [\App\Http\Controllers\PublicFormController::class, 'storePreMarital'])->name('public.forms.pre-marital.store');

// Ministerial Forms (Dynamic Slug)
Route::get('/inscricao/{slug}', [\App\Http\Controllers\PublicFormController::class, 'showMinisterialForm'])->name('public.forms.ministerial');
Route::post('/inscricao/ministerial', [\App\Http\Controllers\PublicFormController::class, 'storeMinisterialForm'])->name('public.forms.ministerial.store');

Route::get('/relatorio-trimestral', [\App\Http\Controllers\PublicFormController::class, 'showQuarterlyReportForm'])->name('public.reports.quarterly');
Route::post('/relatorio-trimestral', [\App\Http\Controllers\PublicFormController::class, 'storeQuarterlyReport'])->name('public.reports.quarterly.store');

// Setup Wizard Routes (No authentication required)
Route::prefix('setup')->group(function () {
    Route::get('/', [\App\Http\Controllers\SetupController::class, 'index'])->name('setup.index');
    Route::post('/step1', [\App\Http\Controllers\SetupController::class, 'step1'])->name('setup.step1');
    Route::post('/step2', [\App\Http\Controllers\SetupController::class, 'step2'])->name('setup.step2');
    Route::post('/step3', [\App\Http\Controllers\SetupController::class, 'step3'])->name('setup.step3');
    Route::post('/complete', [\App\Http\Controllers\SetupController::class, 'complete'])->name('setup.complete');
    Route::post('/upload-logo', [\App\Http\Controllers\SetupController::class, 'uploadLogo'])->name('setup.upload-logo');
});

// Rota para pesquisa AJAX (pode ser GET ou POST, mas GET é comum para buscas)
Route::get('/api/search', [SearchController::class, 'search'])
    ->middleware('auth') // Garante que apenas usuários logados podem pesquisar
    ->name('api.search');

// Rotas de Notificação
Route::prefix('notifications')->middleware('auth')->name('notifications.')->group(function () {

    // API - Retorna notificações não lidas (JSON)
    Route::get('/api', [NotificationController::class, 'index'])
        ->name('api.index');

    // Página de todas as notificações
    Route::get('/', [NotificationController::class, 'all'])
        ->name('all');

    // Marcar todas como lidas (AJAX)
    Route::post('/read', [NotificationController::class, 'markAllAsRead'])
        ->name('read');

    // Marcar uma específica como lida e redirecionar
    Route::get('/{id}/mark-read', [NotificationController::class, 'markAsRead'])
        ->name('mark-read');

    // Deletar uma notificação
    Route::delete('/{id}', [NotificationController::class, 'destroy'])
        ->name('destroy');

    // Limpar todas as notificações lidas
    Route::post('/clear-read', [NotificationController::class, 'clearRead'])
        ->name('clear-read');

    // Contagem de não lidas (AJAX)
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])
        ->name('unread-count');

    // Bulk delete notifications
    Route::post('/bulk-delete', [NotificationController::class, 'bulkDestroy'])
        ->name('bulk-delete');
});


// Register Route (Redundante, removido o duplicado no final)

// Rotas protegidas (autenticado)
Route::middleware('auth')->group(function () {

    // Dashboard Principal (Redireciona por Role)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Dashboard Admin
    Route::get('/admin/dashboard', AdminDashboardController::class)
        ->middleware('role:admin,pastor_senior')
        ->name('dashboard.admin');

    // Dashboard Pastor de Zona
    Route::get('/pastor/dashboard', PastorDashboardController::class)
        ->middleware('role:pastor_zona')
        ->name('dashboard.pastor');

    // Dashboard Supervisor
    Route::get('/supervisor/dashboard', SupervisorDashboardController::class)
        ->middleware('role:supervisor,pastor_zona')
        ->name('dashboard.supervisor');

    // Dashboard Líder de Célula
    Route::get('/lider/dashboard', LiderDashboardController::class)
        ->middleware('role:lider_celula,supervisor,pastor_zona')
        ->name('dashboard.lider');

    // Dashboard Membro
    Route::get('/membro/dashboard', MemberDashboardController::class)
        ->middleware('role:membro,lider_celula,supervisor,pastor_zona')
        ->name('dashboard.membro');

    // Dashboard Secretaria
    Route::get('/secretaria/dashboard', \App\Http\Controllers\Dashboard\SecretaryDashboardController::class)
        ->middleware('role:secretaria')
        ->name('dashboard.secretaria');

    // Dashboard Administração
    Route::get('/administracao/dashboard', \App\Http\Controllers\Dashboard\AdministracaoDashboardController::class)
        ->middleware('role:administracao')
        ->name('dashboard.administracao');

    // Criar Membros contexto das rotas abaixo
    // Criar Membros contexto das rotas abaixo
    Route::match(['get', 'post'], '/admin/visitors/batch-destroy', [\App\Http\Controllers\VisitorController::class, 'bulkDestroy'])
        ->middleware('role:admin,secretaria,pastor_zona')
        ->name('visitors.bulk-delete');

    Route::prefix('members')->middleware('role:lider_celula,supervisor,pastor_zona,admin,secretaria')->group(function () {
        Route::get('/', [UserController::class, 'members'])->name('members.index');
        Route::get('/create', [UserController::class, 'createFromContext'])->name('members.create');
        Route::post('/', [UserController::class, 'storeFromContext'])->name('members.store');

        // CORREÇÃO CRÍTICA: Mudança de /{user} para /{member}
        Route::post('/bulk-destroy', [UserController::class, 'bulkDestroyFromContext'])->name('members.bulk-destroy');
        Route::get('/{member}', [UserController::class, 'showFromContext'])->name('members.show');
        Route::get('/{member}/edit', [UserController::class, 'editFromContext'])->name('members.edit');
        Route::put('/{member}', [UserController::class, 'updateFromContext'])->name('members.update');
        Route::delete('/{member}', [UserController::class, 'destroyFromContext'])->name('members.destroy');
    });

    // Visitantes (Admin, Secretaria, Pastor de Zona)
    Route::middleware('role:admin,secretaria,pastor_zona,administracao')->prefix('visitors')->name('visitors.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VisitorController::class, 'index'])->name('index');

        Route::get('/export', [\App\Http\Controllers\VisitorController::class, 'export'])->name('export');
        Route::get('/create', [\App\Http\Controllers\VisitorController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\VisitorController::class, 'store'])->name('store');
        Route::get('/{visitor}', [\App\Http\Controllers\VisitorController::class, 'show'])->name('show');
        Route::get('/{visitor}/edit', [\App\Http\Controllers\VisitorController::class, 'edit'])->name('edit');
        Route::put('/{visitor}', [\App\Http\Controllers\VisitorController::class, 'update'])->name('update');
        Route::delete('/{visitor}', [\App\Http\Controllers\VisitorController::class, 'destroy'])->name('destroy');

        // Ações especiais
        Route::get('/api/cells-by-zone', [\App\Http\Controllers\VisitorController::class, 'getCellsByZone'])->name('cells-by-zone');
        Route::post('/{visitor}/assign-zone', [\App\Http\Controllers\VisitorController::class, 'assignToZone'])->name('assign-zone');
        Route::post('/{visitor}/assign-cell', [\App\Http\Controllers\VisitorController::class, 'assignToCell'])->name('assign-cell');
        Route::post('/{visitor}/mark-contacted', [\App\Http\Controllers\VisitorController::class, 'markAsContacted'])->name('mark-contacted');
    });

    // ===== ECCLESIASTICAL & ADMINISTRATIVE ROUTES =====
    Route::prefix('admin')->group(function () {

        // Settings Routes (Admin only)
        Route::middleware('role:admin')->prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('index');
            Route::post('/update', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
            Route::post('/upload-logo', [\App\Http\Controllers\Admin\SettingController::class, 'uploadLogo'])->name('upload-logo');
            Route::post('/reset', [\App\Http\Controllers\Admin\SettingController::class, 'resetToDefaults'])->name('reset');
            Route::get('/backup', [\App\Http\Controllers\Admin\SettingController::class, 'backup'])->name('backup');
            Route::get('/backup/{filename}', [\App\Http\Controllers\Admin\SettingController::class, 'downloadBackup'])->name('backup.download');

            // Public Form Settings
            Route::get('/public-forms', [\App\Http\Controllers\Admin\PublicFormSettingController::class, 'index'])->name('public-forms');
            Route::post('/public-forms', [\App\Http\Controllers\Admin\PublicFormSettingController::class, 'store'])->name('public-forms.store');
        });

        // Intermediate Restricted (Admin, Pastor Zona, Supervisor, Secretaria, Lider Celula)
        Route::middleware('role:admin,pastor_zona,supervisor,secretaria,pastor,lider_celula,administracao')->group(function () {
            // Centralized Zones Management
            Route::prefix('zones')->name('zones.')->group(function () {
                // Read Access (Pastors, Admins, etc.)
                Route::get('/', [ZoneController::class, 'index'])->name('index');
                Route::get('{zone}', [ZoneController::class, 'show'])->name('show')->where('zone', '[0-9]+');

                // Management Access (Admin/Secretaria only)
                Route::middleware('role:admin,secretaria')->group(function () {
                    Route::get('create', [ZoneController::class, 'create'])->name('create');
                    Route::post('/', [ZoneController::class, 'store'])->name('store');
                    Route::get('{zone}/edit', [ZoneController::class, 'edit'])->name('edit')->where('zone', '[0-9]+');
                    Route::put('{zone}', [ZoneController::class, 'update'])->name('update')->where('zone', '[0-9]+');
                    Route::get('{zone}/merge', [ZoneController::class, 'merge'])->name('merge')->where('zone', '[0-9]+');
                    Route::post('{zone}/merge', [ZoneController::class, 'processMerge'])->name('process-merge')->where('zone', '[0-9]+');
                    Route::delete('{zone}', [ZoneController::class, 'destroy'])->name('destroy')->where('zone', '[0-9]+');
                    Route::delete('bulk-destroy', [ZoneController::class, 'bulkDestroy'])->name('bulk-destroy');
                });
            });

            // Gestão de Supervisões
            Route::get('supervisions/{supervision}/merge', [SupervisionController::class, 'merge'])->name('supervisions.merge');
            Route::post('supervisions/{supervision}/merge', [SupervisionController::class, 'processMerge'])->name('supervisions.process-merge');
            Route::post('supervisions/{supervision}/reassign-zone', [SupervisionController::class, 'reassignZone'])->name('supervisions.reassign-zone');
            Route::delete('supervisions/bulk-destroy', [SupervisionController::class, 'bulkDestroy'])->name('supervisions.bulk-destroy');
            Route::post('supervisions/quick-supervisor', [SupervisionController::class, 'storeQuickSupervisor'])->name('supervisions.quick-supervisor');
            Route::resource('supervisions', SupervisionController::class);

            // Gestão de Células
            Route::get('/cells/{cell}/pdf', [CellController::class, 'downloadPdf'])->name('cells.pdf');
            Route::get('/cells/{cell}/attendance', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('cells.attendance');
            Route::post('/cells/{cell}/attendance', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('cells.attendance.store');
            Route::post('/cells/{cell}/visitors', [\App\Http\Controllers\AttendanceController::class, 'storeVisitor'])->name('cells.visitors.store');
            Route::post('/cells/{cell}/discipleships', [\App\Http\Controllers\AttendanceController::class, 'storeDiscipleship'])->name('cells.discipleships.store');
            Route::put('/cells/{cell}/discipleships/{discipleship}', [\App\Http\Controllers\AttendanceController::class, 'updateDiscipleship'])->name('cells.discipleships.update');
            Route::delete('/cells/{cell}/discipleships/{discipleship}', [\App\Http\Controllers\AttendanceController::class, 'destroyDiscipleship'])->name('cells.discipleships.destroy');
            Route::post('/cells/{cell}/conversions', [\App\Http\Controllers\AttendanceController::class, 'storeConversion'])->name('cells.conversions.store');
            Route::post('/cells/{cell}/reassign-supervision', [CellController::class, 'reassignSupervision'])->name('cells.reassign-supervision');
            Route::post('/cells/{cell}/assign-timoteo', [CellController::class, 'assignTimoteo'])->name('cells.assign-timoteo');
            Route::delete('cells/bulk-destroy', [CellController::class, 'bulkDestroy'])->name('cells.bulk-destroy');
            Route::resource('cells', CellController::class);

            // Cultos (Relatórios de Celebração)
            Route::get('/services/create-teaching', [\App\Http\Controllers\ServiceController::class, 'createTeaching'])->name('services.create-teaching');
            Route::get('/services/{service}/edit-teaching', [\App\Http\Controllers\ServiceController::class, 'editTeaching'])->name('services.edit-teaching');
            Route::get('/services/{service}/pdf', [\App\Http\Controllers\ServiceController::class, 'downloadPdf'])->name('services.download-pdf');
            Route::get('services/report', [\App\Http\Controllers\ServiceController::class, 'report'])->name('services.report');
            Route::get('services/export/monthly', [\App\Http\Controllers\ServiceController::class, 'exportMonthly'])->name('services.export.monthly');
            Route::get('services/export/monthly/excel', [\App\Http\Controllers\ServiceController::class, 'exportMonthlyExcel'])->name('services.export.monthly.excel');
            Route::get('services/export/quarterly', [\App\Http\Controllers\ServiceController::class, 'exportQuarterly'])->name('services.export.quarterly');
            Route::get('services/export/quarterly/excel', [\App\Http\Controllers\ServiceController::class, 'exportQuarterlyExcel'])->name('services.export.quarterly.excel');
            Route::get('services/export/custom', [\App\Http\Controllers\ServiceController::class, 'exportCustom'])->name('services.export.custom');
            Route::get('services/export/custom/excel', [\App\Http\Controllers\ServiceController::class, 'exportCustomExcel'])->name('services.export.custom.excel');
            Route::get('services/export/annual', [\App\Http\Controllers\ServiceController::class, 'exportAnnual'])->name('services.export.annual');
            Route::get('services/export/annual/excel', [\App\Http\Controllers\ServiceController::class, 'exportAnnualExcel'])->name('services.export.annual.excel');
            Route::post('services/bulk-delete', [\App\Http\Controllers\ServiceController::class, 'bulkDestroy'])->name('services.bulk-delete');
            Route::resource('services', \App\Http\Controllers\ServiceController::class);
        });

        // Highly Restricted (Admin & Secretaria Only)
        Route::middleware('role:admin,secretaria,pastor_senior')->group(function () {
            // Gestão de Utilizadores
            Route::delete('/users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
            Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::post('/users/{user}/reassign-cell', [UserController::class, 'reassignCell'])->name('users.reassign-cell');
            Route::post('/users/{user}/remove-from-cell', [UserController::class, 'removeFromCell'])->name('users.remove-from-cell');
            Route::post('/users/{user}/update-observations', [UserController::class, 'updateObservations'])->name('users.update-observations');
            Route::post('users/{user}/reassign-cell', [UserController::class, 'reassignCell'])->name('users.reassign-cell');
            Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::post('users/{user}/observations', [UserController::class, 'updateObservations'])->name('users.update-observations');
            Route::get('users/{user}/activity', [UserController::class, 'activity'])->name('users.activity');
            Route::resource('users', UserController::class);

        });
    });

    // Gestão de Pacotes (Acesso Expandido)
    Route::prefix('admin')->middleware('role:admin,secretaria,comissao_obra,responsavel_pacote,pastor_senior')->group(function () {
        Route::post('packages/{package}/assign', [PackageController::class, 'assignMember'])->name('packages.assign');
        Route::post('packages/{package}/update-member', [PackageController::class, 'updateMember'])->name('packages.update-member');
        Route::post('packages/{package}/bulk-sms', [PackageController::class, 'sendBulkSms'])->name('packages.send-bulk-sms');
        Route::post('packages/{package}/quick-member', [PackageController::class, 'storeQuickMember'])->name('packages.quick-member');
        Route::post('packages/{package}/members/{user}/send-sms', [PackageController::class, 'sendMemberSms'])->name('packages.members.send-sms');
        Route::get('packages/{package}/export', [PackageController::class, 'export'])->name('packages.export');
        Route::get('packages/{package}/export-pdf', [PackageController::class, 'exportPdf'])->name('packages.export-pdf');

        // Members Management
        Route::delete('packages/{package}/members/{user}', [PackageController::class, 'removeMember'])->name('packages.members.remove');
        Route::post('packages/{package}/members/{user}/change-package', [PackageController::class, 'changePackage'])->name('packages.members.change-package');
        Route::post('packages/{package}/bulk-remove-members', [PackageController::class, 'bulkRemoveMembers'])->name('packages.members.bulk-remove');
        Route::post('packages/{package}/notify-commission', [ContributionController::class, 'notifyCommission'])->name('packages.notify-commission');

        Route::get('packages/dashboard', \App\Http\Controllers\Dashboard\PackageManagerDashboardController::class)
            ->name('packages.dashboard');
        Route::resource('packages', PackageController::class);
    });

    // Rota de FORMULÁRIO GET para atribuir/modificar o compromisso de OUTRO usuário
    Route::get('users/{user}/commitment/set', [\App\Http\Controllers\CommitmentController::class, 'showSetCommitmentForm'])
        ->middleware('role:admin,pastor_zona,supervisor') // Apenas quem pode gerir
        ->name('users.commitment.set');

    // Rota de PROCESSAMENTO POST para salvar a atribuição de compromisso
    Route::post('users/{user}/commitment/assign', [\App\Http\Controllers\CommitmentController::class, 'assignCommitment'])
        ->middleware('role:admin,pastor_zona,supervisor')
        ->name('users.commitment.assign');
    // Validar contribuições
    Route::prefix('contributions')->middleware('role:admin,pastor_zona,comissao_obra')->group(function () {
        Route::get('/pending', [ContributionController::class, 'pendingAdmin'])
            ->name('contributions.pending');
        Route::post('/{contribution}/verify', [ContributionController::class, 'verify'])
            ->name('contributions.verify');
        Route::post('/{contribution}/reject', [ContributionController::class, 'reject'])
            ->name('contributions.reject');
        Route::post('/{contribution}/cancel', [ContributionController::class, 'cancel'])
            ->name('contributions.cancel');
        Route::get('/{contribution}/details', [ContributionController::class, 'adminShow'])
            ->name('admin.contributions.show');
    });

    // ===== CONTRIBUIÇÕES ROUTES =====
    Route::prefix('contributions')->/* middleware('not.admin')-> */ group(function () {

        // Listar contribuições do utilizador
        Route::get('/', [ContributionController::class, 'index'])
            ->name('contributions.index');


        // Criar contribuição (membro, líder, supervisor, pastor, admin)
        Route::get('/create', [ContributionController::class, 'create'])
            ->middleware('role:membro,lider_celula,supervisor,pastor_zona,admin,responsavel_pacote,comissao_obra')
            ->name('contributions.create');

        Route::post('/', [ContributionController::class, 'store'])
            ->middleware('role:membro,lider_celula,supervisor,pastor_zona,admin,responsavel_pacote,comissao_obra')
            ->name('contributions.store');
        // Editar contribuição pendente
        Route::get('/{contribution}/edit', [ContributionController::class, 'edit'])
            ->middleware('role:membro,lider_celula,supervisor,pastor_zona')
            ->name('contributions.edit');

        Route::put('/{contribution}', [ContributionController::class, 'update'])
            ->middleware('role:membro,lider_celula,supervisor,pastor_zona')
            ->name('contributions.update');

        // Ver detalhes
        Route::get('/{contribution}', [ContributionController::class, 'show'])
            ->name('contributions.show');

        // Ver comprovativo
        Route::get('/{contribution}/receipt', [ContributionController::class, 'downloadReceipt'])
            ->name('contributions.receipt');
    });
    // ===== PACOTES DE COMPROMISSO ROUTES =====
    Route::prefix('commitments')->middleware('not.admin')->group(function () {

        // Listar pacotes disponíveis
        Route::get('/', [CommitmentController::class, 'index'])
            ->name('commitments.index');

        // Escolher pacote
        Route::post('/choose', [CommitmentController::class, 'choose'])
            ->name('commitments.choose');

        // Ver pacote atual
        Route::get('/current', [CommitmentController::class, 'current'])
            ->name('commitments.current');
    });

    // ===== RELATÓRIOS ROUTES =====
    Route::prefix('reports')->middleware('role:lider_celula,supervisor,pastor_zona,admin,comissao_obra')->group(function () {

        // Relatório da célula (líder)
        Route::get('/cell', [ReportController::class, 'cellReport'])
            ->middleware('role:lider_celula,supervisor,pastor_zona,admin')
            ->name('reports.cell');

        // Relatório da supervisão
        Route::get('/supervision', [ReportController::class, 'supervisionReport'])
            ->middleware('role:supervisor,pastor_zona,admin')
            ->name('reports.supervision');

        // Relatório da zona
        Route::get('/zone', [ReportController::class, 'zoneReport'])
            ->middleware('role:pastor_zona,admin')
            ->name('reports.zone');

        // Relatório global (admin e comissao_obra)
        Route::get('/global', [ReportController::class, 'globalReport'])
            ->middleware('role:admin,comissao_obra')
            ->name('reports.global');

        // Exportar PDF
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])
            ->name('reports.export.pdf');

        // Exportar Excel
        Route::get('/export/excel', [ReportController::class, 'exportExcel'])
            ->name('reports.export.excel');
    });


    // ===== ENCONTROS DE CÉLULA (CELL MEETINGS) ROUTES =====
    Route::get('cell-meetings/export', [\App\Http\Controllers\CellMeetingController::class, 'export'])->name('cell-meetings.export');
    Route::delete('cell-meetings/bulk-destroy', [\App\Http\Controllers\CellMeetingController::class, 'bulkDestroy'])->name('cell-meetings.bulk-destroy');
    Route::get('cell-meetings/{cell_meeting}/pdf', [\App\Http\Controllers\CellMeetingController::class, 'downloadPdf'])->name('cell-meetings.pdf');
    Route::post('cell-meetings/{cell_meeting}/email', [\App\Http\Controllers\CellMeetingController::class, 'sendEmail'])->name('cell-meetings.email');
    Route::resource('cell-meetings', \App\Http\Controllers\CellMeetingController::class);

    // ===== RELATÓRIOS TRIMESTRAIS (QUARTERLY REPORTS) ROUTES =====
    Route::get('quarterly-reports/export', [\App\Http\Controllers\QuarterlyReportController::class, 'export'])->name('quarterly-reports.export');
    Route::get('quarterly-reports/export-annual', [\App\Http\Controllers\QuarterlyReportController::class, 'exportAnnual'])->name('quarterly-reports.export-annual');
    Route::delete('quarterly-reports/bulk-destroy', [\App\Http\Controllers\QuarterlyReportController::class, 'bulkDestroy'])->name('quarterly-reports.bulk-destroy');
    Route::resource('quarterly-reports', \App\Http\Controllers\QuarterlyReportController::class);
    // Quarterly Reports management handled by resource route at 392
    // Edificar Dashboard
    Route::get('project-edificar/dashboard', [\App\Http\Controllers\Admin\EdificarDashboardController::class, 'index'])
        ->middleware('role:admin,comissao_obra,pastor_senior,administracao')
        ->name('edificar.dashboard');

    // ===== EVENTOS E CERIMÓNIAS (EVENTS) ROUTES =====
    Route::get('events/feed', [\App\Http\Controllers\EventController::class, 'feed'])->name('events.feed');
    Route::get('events/{event}/pdf', [\App\Http\Controllers\EventController::class, 'downloadPdf'])->name('events.pdf');
    Route::post('events/{event}/email', [\App\Http\Controllers\EventController::class, 'sendEmail'])->name('events.email');
    Route::post('events/bulk-delete', [\App\Http\Controllers\EventController::class, 'bulkDestroy'])->name('events.bulk-delete');
    Route::resource('events', \App\Http\Controllers\EventController::class);

    // Manage Event Types
    Route::resource('event-types', \App\Http\Controllers\Admin\EventTypeController::class)
        ->middleware('role:admin,secretaria,pastor_zona');

    // ===== PAINEL FINANCEIRO (FINANCIAL DASHBOARD) ROUTES =====
    // ===== PAINEL FINANCEIRO (FINANCIAL DASHBOARD) ROUTES =====
    Route::get('financial-dashboard', [\App\Http\Controllers\FinancialDashboardController::class, 'index'])->name('financial.dashboard');

    // Requisitions Routes
    Route::post('requisitions/{requisition}/approve', [\App\Http\Controllers\Admin\RequisitionController::class, 'approve'])->name('requisitions.approve');
    Route::post('requisitions/{requisition}/reject', [\App\Http\Controllers\Admin\RequisitionController::class, 'reject'])->name('requisitions.reject');
    Route::resource('requisitions', \App\Http\Controllers\Admin\RequisitionController::class);

    // Expenses Routes
    Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class);

    // ===== CURSOS E FORMAÇÃO (COURSES) ROUTES =====
    Route::get('course-classes/upcoming-weddings', [\App\Http\Controllers\CourseClassController::class, 'upcomingWeddings'])->name('course-classes.upcoming-weddings');
    Route::get('course-classes/export-all', [\App\Http\Controllers\CourseClassController::class, 'exportAll'])->name('course-classes.export-all');
    Route::post('course-classes/bulk-delete', [\App\Http\Controllers\CourseClassController::class, 'bulkDestroy'])->name('course-classes.bulk-delete');
    Route::get('course-classes/{course_class}/export-pdf', [\App\Http\Controllers\CourseClassController::class, 'exportPdf'])->name('course-classes.export-pdf');
    Route::get('course-classes/{course_class}/attendance/{meeting}', [\App\Http\Controllers\CourseClassController::class, 'attendance'])->name('course-classes.attendance');
    Route::post('course-classes/{course_class}/attendance/{meeting}', [\App\Http\Controllers\CourseClassController::class, 'storeAttendance'])->name('course-classes.attendance.store');
    Route::post('course-classes/{course_class}/add-enrollment', [\App\Http\Controllers\CourseClassController::class, 'addEnrollment'])->name('course-classes.add-enrollment');
    Route::post('course-classes/{course_class}/assign-couple-enrollment', [\App\Http\Controllers\CourseClassController::class, 'assignCoupleEnrollment'])->name('course-classes.assign-couple-enrollment');
    Route::post('course-classes/{course_class}/assign-ministerial-enrollment', [\App\Http\Controllers\CourseClassController::class, 'assignMinisterialEnrollment'])->name('course-classes.assign-ministerial-enrollment');
    Route::post('course-classes/{course_class}/remove-enrollment', [\App\Http\Controllers\CourseClassController::class, 'removeEnrollment'])->name('course-classes.remove-enrollment');
    Route::post('course-classes/{course_class}/meetings', [\App\Http\Controllers\CourseClassController::class, 'storeMeeting'])->name('course-classes.meetings.store');
    Route::get('course-classes/{course_class}/report', [\App\Http\Controllers\CourseClassController::class, 'report'])->name('course-classes.report');
    Route::get('course-classes/{course_class}/export', [\App\Http\Controllers\CourseClassController::class, 'exportReport'])->name('course-classes.export');

    Route::resource('course-classes', \App\Http\Controllers\CourseClassController::class);

    Route::get('courses/export-global', [\App\Http\Controllers\CourseController::class, 'exportGlobalReport'])->name('courses.export-global');
    Route::post('course-classes/{course_class}/move', [\App\Http\Controllers\CourseClassController::class, 'move'])->name('course-classes.move');
    Route::post('courses/bulk-delete', [\App\Http\Controllers\CourseController::class, 'bulkDestroy'])->name('courses.bulk-delete');
    Route::post('courses/{course}/assign-public-enrollment', [\App\Http\Controllers\CourseController::class, 'assignPublicEnrollment'])->name('courses.assign-public-enrollment');
    Route::resource('courses', \App\Http\Controllers\CourseController::class);

    Route::post('course-enrollments/bulk-destroy', [\App\Http\Controllers\CourseEnrollmentController::class, 'bulkDestroy'])->name('course-enrollments.bulk-destroy');
    Route::resource('course-enrollments', \App\Http\Controllers\CourseEnrollmentController::class);
    Route::post('course-enrollments/{course_enrollment}/assign-class', [\App\Http\Controllers\CourseEnrollmentController::class, 'assignClass'])->name('course-enrollments.assign-class');
    Route::post('courses/{course}/enroll', [\App\Http\Controllers\CourseEnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::post('enrollments/{course_enrollment}/status', [\App\Http\Controllers\CourseEnrollmentController::class, 'updateStatus'])->name('enrollments.status');
    Route::post('couple-enrollments/{couple_enrollment}/status', [\App\Http\Controllers\CoupleEnrollmentController::class, 'updateStatus'])->name('couple-enrollments.status');

    Route::middleware('role:admin,pastor,secretaria,pastor_senior')->group(function () {
        // Couple Enrollments
        Route::get('couple-enrollments', [\App\Http\Controllers\CoupleEnrollmentController::class, 'index'])->name('couple-enrollments.index');
        Route::get('couple-enrollments/{couple_enrollment}', [\App\Http\Controllers\CoupleEnrollmentController::class, 'show'])->name('couple-enrollments.show');
        Route::get('couple-enrollments/{couple_enrollment}/edit', [\App\Http\Controllers\CoupleEnrollmentController::class, 'edit'])->name('couple-enrollments.edit');
        Route::put('couple-enrollments/{couple_enrollment}', [\App\Http\Controllers\CoupleEnrollmentController::class, 'update'])->name('couple-enrollments.update');
        Route::delete('couple-enrollments/{couple_enrollment}', [\App\Http\Controllers\CoupleEnrollmentController::class, 'destroy'])->name('couple-enrollments.destroy');
        Route::post('couple-enrollments/{couple_enrollment}/assign-class', [\App\Http\Controllers\CoupleEnrollmentController::class, 'assignClass'])->name('couple-enrollments.assign-class');
        Route::get('couple-enrollments-export', [\App\Http\Controllers\CoupleEnrollmentController::class, 'export'])->name('couple-enrollments.export');

        // Ministerial Enrollments
        Route::get('ministerial-enrollments', [\App\Http\Controllers\MinisterialEnrollmentController::class, 'index'])->name('ministerial-enrollments.index');
        Route::get('ministerial-enrollments/{ministerial_enrollment}', [\App\Http\Controllers\MinisterialEnrollmentController::class, 'show'])->name('ministerial-enrollments.show');
        Route::get('ministerial-enrollments/{ministerial_enrollment}/edit', [\App\Http\Controllers\MinisterialEnrollmentController::class, 'edit'])->name('ministerial-enrollments.edit');
        Route::put('ministerial-enrollments/{ministerial_enrollment}', [\App\Http\Controllers\MinisterialEnrollmentController::class, 'update'])->name('ministerial-enrollments.update');
        Route::delete('ministerial-enrollments/{ministerial_enrollment}', [\App\Http\Controllers\MinisterialEnrollmentController::class, 'destroy'])->name('ministerial-enrollments.destroy');
        Route::post('ministerial-enrollments/{ministerial_enrollment}/convert', [\App\Http\Controllers\MinisterialEnrollmentController::class, 'convertToUser'])->name('ministerial-enrollments.convert');
    });

    // ===== PERFIL DO UTILIZADOR =====
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Weddings
    Route::get('weddings/feed', [App\Http\Controllers\Admin\WeddingController::class, 'feed'])->name('weddings.feed');
    Route::get('weddings/pdf', [App\Http\Controllers\Admin\WeddingController::class, 'downloadPdf'])->name('weddings.pdf');
    Route::post('weddings/bulk-delete', [App\Http\Controllers\Admin\WeddingController::class, 'bulkDestroy'])->name('weddings.bulk-delete');
    Route::resource('weddings', App\Http\Controllers\Admin\WeddingController::class);
    Route::post('/test-email', [App\Http\Controllers\Admin\WeddingController::class, 'testEmail'])->name('test.email');

    // Inventário (Ecclesiastical)
    Route::resource('inventory-items', App\Http\Controllers\InventoryItemController::class);
});
# ============================================
# ROTA DE REGISTRO (PÚBLICA MAS CONTROLADA)
# ============================================

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register.store');
