<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationsController extends BaseApiController
{
    /**
     * Display a listing of user notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $filter = $request->query('filter', 'all'); // all, unread, read

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->latest()->paginate($request->input('per_page', 20));

        $formatted = collect($notifications->items())->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Nova Notificação',
                'message' => $notification->data['message'] ?? '',
                'link' => $notification->data['link'] ?? '#',
                'type' => $notification->data['type'] ?? 'general',
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at' => $notification->created_at?->toIso8601String(),
            ];
        });

        return $this->sendResponse($formatted, 'Notificações recuperadas.', [
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'per_page' => $notifications->perPage(),
            'total' => $notifications->total(),
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->find($id);

        if (!$notification) {
            return $this->sendError('Notificação não encontrada.', [], 404);
        }

        $notification->markAsRead();

        return $this->sendResponse(null, 'Notificação marcada como lida.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->sendResponse(null, 'Todas as notificações marcadas como lidas.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->notifications()->find($id);

        if (!$notification) {
            return $this->sendError('Notificação não encontrada.', [], 404);
        }

        $notification->delete();

        return $this->sendResponse(null, 'Notificação excluída com sucesso.');
    }
}
