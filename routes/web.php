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
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

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


// Register Route (Público para criar conta)
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register.store');

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
    // ===== ADMIN ROUTES =====
    Route::prefix('admin')->middleware('role:admin')->group(function () {

        // Gestão de Zonas
        Route::resource('zones', ZoneController::class);

        // Gestão de Supervisões
        Route::resource('supervisions', SupervisionController::class);

        // Gestão de Células
        Route::resource('cells', CellController::class);

        // Gestão de Utilizadores
        Route::resource('users', UserController::class);

        // Gestão de Pacotes
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
    Route::prefix('contributions')->/* middleware('not.admin')-> */group(function () {

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
    // ===== ROTAS DE GESTÃO DE CONTRIBUIÇÕES (ADMIN) =====
    Route::prefix('contributions')->middleware('role:admin,pastor_zona')->group(function () {

        // Ação de Verificação (Confirmação)
        Route::post('/{contribution}/verify', [ContributionController::class, 'verify'])
            ->name('contributions.verify');

        // Ação de Rejeição
        Route::post('/{contribution}/reject', [ContributionController::class, 'reject'])
            ->name('contributions.reject');
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

    // ===== PERFIL DO UTILIZADOR =====
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
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
