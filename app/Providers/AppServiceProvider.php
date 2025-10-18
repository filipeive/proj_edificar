<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        // Policies serão registadas automaticamente
        // Basta ter o arquivo ContributionPolicy.php em app/Policies/
        
        // Gates
        \Gate::define('verify-contribution', function ($user) {
            return $user->role === 'admin';
        });
    }
}