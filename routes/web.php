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
});


// Register Route (Redundante, removido o duplicado no final)

// Rotas protegidas (autenticado)
Route::middleware('auth')->group(function () {

    // Dashboard Principal (Redireciona por Role)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Dashboard Admin
    Route::get('/admin/dashboard', AdminDashboardController::class)
        ->middleware('role:admin')
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

    // Criar Membros contexto das rotas abaixo
    // Criar Membros contexto das rotas abaixo
    Route::prefix('members')->middleware('role:lider_celula,supervisor,pastor_zona,admin')->group(function () {
        Route::get('/', [UserController::class, 'members'])->name('members.index');
        Route::get('/create', [UserController::class, 'createFromContext'])->name('members.create');
        Route::post('/', [UserController::class, 'storeFromContext'])->name('members.store');

        // CORREÇÃO CRÍTICA: Mudança de /{user} para /{member}
        Route::get('/{member}', [UserController::class, 'showFromContext'])->name('members.show');
        Route::get('/{member}/edit', [UserController::class, 'editFromContext'])->name('members.edit');
        Route::put('/{member}', [UserController::class, 'updateFromContext'])->name('members.update');
        Route::delete('/{member}', [UserController::class, 'destroyFromContext'])->name('members.destroy');
    });

    // Visitantes (Admin, Secretaria, Pastor de Zona)
    Route::middleware('role:admin,secretaria,pastor_zona')->prefix('visitors')->name('visitors.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VisitorController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\VisitorController::class, 'export'])->name('export');
        Route::get('/create', [\App\Http\Controllers\VisitorController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\VisitorController::class, 'store'])->name('store');
        Route::get('/{visitor}', [\App\Http\Controllers\VisitorController::class, 'show'])->name('show');
        Route::get('/{visitor}/edit', [\App\Http\Controllers\VisitorController::class, 'edit'])->name('edit');
        Route::put('/{visitor}', [\App\Http\Controllers\VisitorController::class, 'update'])->name('update');
        Route::delete('/{visitor}', [\App\Http\Controllers\VisitorController::class, 'destroy'])->name('destroy');

        // Ações especiais
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
        });

        // Intermediate Restricted (Admin, Pastor Zona, Supervisor, Secretaria, Lider Celula)
        Route::middleware('role:admin,pastor_zona,supervisor,secretaria,pastor,lider_celula')->group(function () {
            // Gestão de Supervisões
            Route::resource('supervisions', SupervisionController::class);

            // Gestão de Células
            Route::get('/cells/{cell}/pdf', [CellController::class, 'downloadPdf'])->name('cells.pdf');
            Route::get('/cells/{cell}/attendance', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('cells.attendance');
            Route::post('/cells/{cell}/attendance', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('cells.attendance.store');
            Route::post('/cells/{cell}/visitors', [\App\Http\Controllers\AttendanceController::class, 'storeVisitor'])->name('cells.visitors.store');
            Route::post('/cells/{cell}/discipleships', [\App\Http\Controllers\AttendanceController::class, 'storeDiscipleship'])->name('cells.discipleships.store');
            Route::post('/cells/{cell}/conversions', [\App\Http\Controllers\AttendanceController::class, 'storeConversion'])->name('cells.conversions.store');
            Route::resource('cells', CellController::class);

            // Cultos (Relatórios de Celebração)
            Route::get('/services/{service}/pdf', [\App\Http\Controllers\ServiceController::class, 'downloadPdf'])->name('services.download-pdf');
            Route::get('services/report', [\App\Http\Controllers\ServiceController::class, 'report'])->name('services.report');
            Route::resource('services', \App\Http\Controllers\ServiceController::class);
        });

        // Highly Restricted (Admin & Secretaria Only)
        Route::middleware('role:admin,secretaria')->group(function () {
            // Gestão de Zonas
            Route::resource('zones', ZoneController::class);
            // Gestão de Utilizadores
            Route::resource('users', UserController::class);
            Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            // Gestão de Pacotes
            Route::resource('packages', PackageController::class);
        });
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
    Route::prefix('contributions')->middleware('role:admin,pastor_zona')->group(function () {
        Route::get('/pending', [ContributionController::class, 'pendingAdmin'])
            ->name('contributions.pending');
        Route::post('/{contribution}/verify', [ContributionController::class, 'verify'])
            ->name('contributions.verify');
        Route::post('/{contribution}/reject', [ContributionController::class, 'reject'])
            ->name('contributions.reject');
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
            ->middleware('role:membro,lider_celula,supervisor,pastor_zona,admin')
            ->name('contributions.create');

        Route::post('/', [ContributionController::class, 'store'])
            ->middleware('role:membro,lider_celula,supervisor,pastor_zona,admin')
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
    Route::prefix('reports')->middleware('role:lider_celula,supervisor,pastor_zona,admin')->group(function () {

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

        // Relatório global (admin)
        Route::get('/global', [ReportController::class, 'globalReport'])
            ->middleware('role:admin')
            ->name('reports.global');

        // Exportar PDF
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])
            ->name('reports.export.pdf');

        // Exportar Excel
        Route::get('/export/excel', [ReportController::class, 'exportExcel'])
            ->name('reports.export.excel');
    });


    // ===== ENCONTROS DE CÉLULA (CELL MEETINGS) ROUTES =====
    Route::get('cell-meetings/{cell_meeting}/pdf', [\App\Http\Controllers\CellMeetingController::class, 'downloadPdf'])->name('cell-meetings.pdf');
    Route::post('cell-meetings/{cell_meeting}/email', [\App\Http\Controllers\CellMeetingController::class, 'sendEmail'])->name('cell-meetings.email');
    Route::resource('cell-meetings', \App\Http\Controllers\CellMeetingController::class);

    // ===== RELATÓRIOS TRIMESTRAIS (QUARTERLY REPORTS) ROUTES =====
    Route::get('quarterly-reports/export', [\App\Http\Controllers\QuarterlyReportController::class, 'export'])->name('quarterly-reports.export');
    Route::resource('quarterly-reports', \App\Http\Controllers\QuarterlyReportController::class);

    // ===== EVENTOS E CERIMÓNIAS (EVENTS) ROUTES =====
    Route::get('events/feed', [\App\Http\Controllers\EventController::class, 'feed'])->name('events.feed');
    Route::get('events/{event}/pdf', [\App\Http\Controllers\EventController::class, 'downloadPdf'])->name('events.pdf');
    Route::post('events/{event}/email', [\App\Http\Controllers\EventController::class, 'sendEmail'])->name('events.email');
    Route::resource('events', \App\Http\Controllers\EventController::class);

    // ===== PAINEL FINANCEIRO (FINANCIAL DASHBOARD) ROUTES =====
    Route::get('financial-dashboard', [\App\Http\Controllers\FinancialDashboardController::class, 'index'])->name('financial.dashboard');

    // ===== CURSOS E FORMAÇÃO (COURSES) ROUTES =====
    Route::resource('courses', \App\Http\Controllers\CourseController::class);
    Route::resource('course-classes', \App\Http\Controllers\CourseClassController::class);
    Route::get('course-classes/{course_class}/attendance/{meeting}', [\App\Http\Controllers\CourseClassController::class, 'attendance'])->name('course-classes.attendance');
    Route::post('course-classes/{course_class}/attendance/{meeting}', [\App\Http\Controllers\CourseClassController::class, 'storeAttendance'])->name('course-classes.attendance.store');
    Route::post('course-classes/{course_class}/add-enrollment', [\App\Http\Controllers\CourseClassController::class, 'addEnrollment'])->name('course-classes.add-enrollment');
    Route::post('course-classes/{course_class}/remove-enrollment', [\App\Http\Controllers\CourseClassController::class, 'removeEnrollment'])->name('course-classes.remove-enrollment');
    Route::post('course-classes/{course_class}/meetings', [\App\Http\Controllers\CourseClassController::class, 'storeMeeting'])->name('course-classes.meetings.store');

    Route::resource('course-enrollments', \App\Http\Controllers\CourseEnrollmentController::class);
    Route::get('course-classes/upcoming-weddings', [\App\Http\Controllers\CourseClassController::class, 'upcomingWeddings'])->name('course-classes.upcoming-weddings');
    Route::get('course-classes/{course_class}/report', [\App\Http\Controllers\CourseClassController::class, 'report'])->name('course-classes.report');
    Route::get('course-classes/{course_class}/export', [\App\Http\Controllers\CourseClassController::class, 'exportReport'])->name('course-classes.export');

    Route::get('courses/export-global', [\App\Http\Controllers\CourseController::class, 'exportGlobalReport'])->name('courses.export-global');
    Route::post('courses/{course}/enroll', [\App\Http\Controllers\CourseEnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::post('enrollments/{course_enrollment}/status', [\App\Http\Controllers\CourseEnrollmentController::class, 'updateStatus'])->name('enrollments.status');

    // ===== PERFIL DO UTILIZADOR =====
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Weddings
    Route::get('weddings/feed', [App\Http\Controllers\Admin\WeddingController::class, 'feed'])->name('weddings.feed');
    Route::get('weddings/pdf', [App\Http\Controllers\Admin\WeddingController::class, 'downloadPdf'])->name('weddings.pdf');
    Route::resource('weddings', App\Http\Controllers\Admin\WeddingController::class);
    Route::post('/test-email', [App\Http\Controllers\Admin\WeddingController::class, 'testEmail'])->name('test.email');
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
