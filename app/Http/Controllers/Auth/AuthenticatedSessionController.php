<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Congregation;
use App\Services\CongregationContext;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login', [
            'congregations' => Congregation::where('is_active', true)->orderBy('name')->get(),
            'currentCongregation' => app(CongregationContext::class)->get(),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (! $request->boolean('remember')) {
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget(\Illuminate\Support\Facades\Auth::guard('web')->getRecallerName()));
        }

        // Record last login and log activity
        $user = auth()->user();
        $user->update(['last_login_at' => now()]);
        $user->logActivity('login', 'Sessão iniciada no sistema');

        $intended = session()->get('url.intended');
        if ($intended && (str_contains($intended, '/pdf') || str_contains($intended, '/export') || $intended === url('/'))) {
            session()->forget('url.intended');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log logout activity before session is invalidated
        if (auth()->check()) {
            auth()->user()->logActivity('logout', 'Sessão terminada');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
