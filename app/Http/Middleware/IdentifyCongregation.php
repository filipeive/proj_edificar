<?php

namespace App\Http\Middleware;

use App\Models\Congregation;
use App\Services\CongregationContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyCongregation
{
    public function handle(Request $request, Closure $next): Response
    {
        $authenticatedUser = $request->user();

        if (!$authenticatedUser && $request->is('api/*')) {
            $authenticatedUser = $request->user('sanctum');
        } elseif (!$authenticatedUser && $request->hasSession()) {
            $sessionAuthKey = \Illuminate\Support\Facades\Auth::getName();
            $sessionUserId = $request->session()->get($sessionAuthKey);
            if ($sessionUserId) {
                $authenticatedUser = \App\Models\User::withoutGlobalScope('congregation')->find($sessionUserId);
            }
        }

        if (!$authenticatedUser) {
            $authenticatedUser = auth()->user();
        }

        $hostCongregation = $this->resolveFromHost($request);
        $userCongregation = $authenticatedUser?->congregation_id
            ? Congregation::where('is_active', true)->find($authenticatedUser->congregation_id)
            : null;

        if ($hostCongregation && $userCongregation && $hostCongregation->id !== $userCongregation->id) {
            abort(403, 'A sua conta pertence a outra congregação.');
        }

        $congregation = $hostCongregation ?? $userCongregation;

        if (!$congregation) {
            $slug = $request->input('congregation')
                ?? $request->query('congregation')
                ?? ($request->hasSession() ? $request->session()->get('current_congregation_slug') : null);

            if ($slug) {
                $congregation = Congregation::where('slug', $slug)->where('is_active', true)->first();
                if (!$congregation) {
                    abort(404, 'Congregação não encontrada.');
                }
            }
        }

        if (!$congregation && $request->is('login') && $request->isMethod('post') && $request->filled('email')) {
            $candidateUser = \App\Models\User::withoutGlobalScope('congregation')
                ->where('email', $request->input('email'))
                ->first();
            if ($candidateUser && $candidateUser->congregation) {
                $congregation = $candidateUser->congregation;
            }
        }

        $congregation ??= Congregation::where('is_primary', true)->where('is_active', true)->first();
        abort_unless($congregation, 503, 'Nenhuma congregação ativa está configurada.');

        app(CongregationContext::class)->set($congregation);
        if ($request->hasSession()) {
            $request->session()->put('current_congregation_slug', $congregation->slug);
        }

        if ($authenticatedUser && !$request->is('api/*')) {
            \Illuminate\Support\Facades\Auth::setUser($authenticatedUser);
            $request->setUserResolver(fn () => $authenticatedUser);
        }

        if ($userCongregation && (int) $userCongregation->id !== (int) $congregation->id) {
            abort(403, 'A sua conta pertence a outra congregação.');
        }

        return $next($request);
    }

    private function resolveFromHost(Request $request): ?Congregation
    {
        $host = strtolower($request->getHost());

        $congregation = Congregation::where('domain', $host)
            ->where('is_active', true)
            ->first();

        if ($congregation) {
            return $congregation;
        }

        $hostLabel = explode('.', $host)[0] ?? '';
        if ($hostLabel === '' || in_array($hostLabel, ['www', 'localhost', '127'], true)) {
            return null;
        }

        return Congregation::where('subdomain', $hostLabel)
            ->where('is_active', true)
            ->first();
    }
}