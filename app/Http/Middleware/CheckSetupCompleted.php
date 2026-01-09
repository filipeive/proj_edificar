<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSetupCompleted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for setup routes
        if ($request->is('setup*')) {
            return $next($request);
        }

        // Check if setup is completed
        if (!Setting::get('system.setup_completed', false)) {
            return redirect('/setup');
        }

        return $next($request);
    }
}
