<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Retorna as notificações não lidas do usuário logado (AJAX).
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([], 401);
            }

            // Verificar se o user tem o trait Notifiable
            if (!method_exists($user, 'unreadNotifications')) {
                return response()->json([]);
            }

            $unreadNotifications = $user->unreadNotifications()
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->data['title'] ?? 'Nova Notificação',
                        'message' => $notification->data['message'] ?? 'Clique para ver detalhes.',
                        'time_ago' => $notification->created_at->diffForHumans(),
                        'link' => $notification->data['link'] ?? '#',
                        'type' => $notification->data['type'] ?? 'general',
                    ];
                });

            return response()->json($unreadNotifications);

        } catch (\Exception $e) {
            Log::error('NotificationController@index Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Página de todas as notificações (Blade View).
     * @return \Illuminate\View\View
     */
    public function all(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter', 'all'); // all, unread, read

        $query = $user->notifications();

        // Aplicar filtro
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->latest()
            ->paginate(20);

        return view('notifications.index', [
            'notifications' => $notifications,
            'filter' => $filter,
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marca todas as notificações não lidas como lidas.
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Não autenticado'], 401);
            }
            return redirect()->route('login');
        }

        if (method_exists($user, 'unreadNotifications')) {
            $user->unreadNotifications()->update(['read_at' => now()]);
        }

        // Se for chamada via AJAX, manter JSON; caso contrário redirecionar com flash message
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Todas as notificações foram marcadas como lidas.']);
        }

        return redirect()->back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }

    /**
     * Marca uma notificação específica como lida.
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            
            // Redirecionar para o link da notificação se existir
            $link = $notification->data['link'] ?? route('notifications.all');
            return redirect($link);
        }

        return redirect()->route('notifications.all')
            ->with('warning', 'Notificação não encontrada.');
    }

    /**
     * Deleta uma notificação específica.
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->delete();
            return redirect()->back()
                ->with('success', 'Notificação removida.');
        }

        return redirect()->back()
            ->with('error', 'Notificação não encontrada.');
    }

    /**
     * Deleta todas as notificações lidas.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearRead()
    {
        $user = Auth::user();
        
        $deleted = $user->notifications()
            ->whereNotNull('read_at')
            ->delete();

        return redirect()->back()
            ->with('success', "{$deleted} notificações lidas foram removidas.");
    }

    /**
     * Retorna a contagem de notificações não lidas (AJAX).
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount()
    {
        $user = Auth::user();
        
        $count = $user->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }
}