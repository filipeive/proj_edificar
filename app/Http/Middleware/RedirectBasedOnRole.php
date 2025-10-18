<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole {
    public function handle(Request $request, Closure $next): Response {
        if (auth()->check() && $request->path() === '/') {
            $role = auth()->user()->role;

            return match($role) {
                'admin' => redirect()->route('dashboard.admin'),
                'pastor_zona' => redirect()->route('dashboard.pastor'),
                'supervisor' => redirect()->route('dashboard.supervisor'),
                'lider_celula' => redirect()->route('dashboard.lider'),
                'membro' => redirect()->route('dashboard.membro'),
                default => redirect()->route('dashboard'),
            };
        }

        return $next($request);
    }
}