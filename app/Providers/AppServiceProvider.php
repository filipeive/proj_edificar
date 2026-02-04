<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('moz_phone', function ($attribute, $value) {
            if ($value === null || $value === '') {
                return true;
            }

            $digits = preg_replace('/\D/', '', (string) $value);
            if (str_starts_with($digits, '00')) {
                $digits = substr($digits, 2);
            }

            if ($digits === '') {
                return false;
            }

            if (strlen($digits) === 9) {
                return true;
            }

            return str_starts_with($digits, '258') && strlen($digits) === 12;
        });

        // Policies serão registadas automaticamente
        // Basta ter o arquivo ContributionPolicy.php em app/Policies/

        // Gates
        Gate::define('verify-contribution', function ($user) {
            return $user->isAdmin() || $user->isSecretaria() || $user->isPastorZona();
        });

        // Compartilhar dados globais com as views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $view->with('authUser', $user);
                $view->with('role', $user->role ?? 'membro');
                $view->with('unreadNotifications', $user->unreadNotifications()->count());
                $view->with('pendingCount', \App\Models\Contribution::where('status', 'pendente')->count());
            } else {
                $view->with('authUser', null);
                $view->with('role', 'membro');
                $view->with('unreadNotifications', 0);
                $view->with('pendingCount', 0);
            }
        });
    }
}
