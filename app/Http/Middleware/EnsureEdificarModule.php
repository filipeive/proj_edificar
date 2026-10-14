<?php

namespace App\Http\Middleware;

use App\Services\CongregationContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEdificarModule
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasEdificar = app(CongregationContext::class)->hasEdificar();

        if (!$hasEdificar) {
            abort(404, 'O módulo Projeto Edificar não está ativo para esta congregação.');
        }

        return $next($request);
    }
}
