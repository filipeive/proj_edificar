<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseApiController
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Get dashboard metrics for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $metrics = $this->dashboardService->getMetricsForUser($request->user());

        return $this->sendResponse($metrics, 'Indicadores do painel carregados.');
    }
}
