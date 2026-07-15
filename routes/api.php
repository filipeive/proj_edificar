<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\MembersController;
use App\Http\Controllers\Api\V1\CellsController;
use App\Http\Controllers\Api\V1\MinistriesController;
use App\Http\Controllers\Api\V1\EventsController;
use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\ReportsController;
use App\Http\Controllers\Api\V1\NotificationsController;
use App\Http\Controllers\Api\V1\WeddingsController;
use App\Http\Controllers\Api\V1\QuarterlyReportsController;
use App\Http\Controllers\Api\V1\RequisitionsController;
use App\Http\Controllers\Api\V1\ExpensesController;
use App\Http\Controllers\Api\V1\ContributionsController;
use App\Http\Controllers\Api\V1\ServicesController;
use App\Http\Controllers\Api\V1\PackagesController;
use App\Http\Controllers\Api\V1\InventoryItemsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->as('api.v1.')->group(function () {
    // Public Authentication
    Route::post('/login', [AuthController::class, 'login']);

    // Authenticated Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth & Profile
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Members CRUD
        Route::apiResource('members', MembersController::class);

        // Cells CRUD & Reassignments
        Route::apiResource('cells', CellsController::class);
        Route::post('cells/transfer-member', [CellsController::class, 'transferMember']);

        // Ministries CRUD (Ministerial Enrollments)
        Route::apiResource('ministries', MinistriesController::class);

        // Events CRUD & Course/Event Enrollments
        Route::apiResource('events', EventsController::class);
        Route::post('events/{course}/enroll', [EventsController::class, 'enroll']);

        // Attendances
        Route::get('attendance', [AttendanceController::class, 'index']);
        Route::post('attendance', [AttendanceController::class, 'store']);

        // Reports
        Route::get('reports/contributions', [ReportsController::class, 'contributions']);

        // Notifications
        Route::get('notifications', [NotificationsController::class, 'index']);
        Route::post('notifications/{id}/read', [NotificationsController::class, 'markAsRead']);
        Route::post('notifications/read-all', [NotificationsController::class, 'markAllAsRead']);
        Route::delete('notifications/{id}', [NotificationsController::class, 'destroy']);

        // Weddings
        Route::apiResource('weddings', WeddingsController::class);

        // Quarterly Reports
        Route::apiResource('quarterly-reports', QuarterlyReportsController::class);

        // Requisitions
        Route::apiResource('requisitions', RequisitionsController::class);
        Route::post('requisitions/{requisition}/approve', [RequisitionsController::class, 'approve']);
        Route::post('requisitions/{requisition}/reject', [RequisitionsController::class, 'reject']);

        // Expenses
        Route::apiResource('expenses', ExpensesController::class);

        // Contributions
        Route::apiResource('contributions', ContributionsController::class);
        Route::post('contributions/{contribution}/verify', [ContributionsController::class, 'verify']);
        Route::post('contributions/{contribution}/reject', [ContributionsController::class, 'reject']);
        Route::post('contributions/{contribution}/cancel', [ContributionsController::class, 'cancel']);

        // Services
        Route::apiResource('services', ServicesController::class);

        // Packages
        Route::apiResource('packages', PackagesController::class);

        // Inventory Items
        Route::apiResource('inventory-items', InventoryItemsController::class);
    });
});
