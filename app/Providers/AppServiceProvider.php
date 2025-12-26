<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Policies serão registadas automaticamente
        // Basta ter o arquivo ContributionPolicy.php em app/Policies/

        // Gates
        Gate::define('verify-contribution', function ($user) {
            return $user->role === 'admin' || $user->role === 'pastor_zona';
        });

        // Compartilhar dados globais com as views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $view->with('user', $user);
                $view->with('role', $user->role ?? 'membro');
                $view->with('unreadNotifications', $user->unreadNotifications()->count());
                $view->with('pendingCount', \App\Models\Contribution::where('status', 'pendente')->count());
            } else {
                $view->with('user', null);
                $view->with('role', 'membro');
                $view->with('unreadNotifications', 0);
                $view->with('pendingCount', 0);
            }
        });
    }
}