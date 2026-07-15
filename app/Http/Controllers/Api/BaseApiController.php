<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Send standard success API response.
     */
    public function sendResponse($result, string $message = 'Operation completed successfully', array $meta = [], int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $result,
            'meta'    => $meta,
            'errors'  => null,
        ], $code);
    }

    /**
     * Send standard error API response.
     */
    public function sendError(string $error, array $errorMessages = [], int $code = 404): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $error,
            'errors'  => $errorMessages ?: null,
        ], $code);
    }
}
