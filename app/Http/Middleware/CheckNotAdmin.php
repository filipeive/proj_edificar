<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckNotAdmin {
    public function handle(Request $request, Closure $next): Response {
        if (auth()->check() && auth()->user()->role === 'admin') {
            abort(403, 'Admins não podem acessar esta área');
        }

        return $next($request);
    }
}