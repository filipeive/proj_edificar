<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', \App\Http\Middleware\IdentifyCongregation::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\IdentifyCongregation::class);

        $middleware->prependToPriorityList(
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \App\Http\Middleware\IdentifyCongregation::class,
        );

        $middleware->validateCsrfTokens(except: [
            'setup/*',
            'setup',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'super.admin' => \App\Http\Middleware\RequireSuperAdmin::class,
            'redirect.role' => \App\Http\Middleware\RedirectBasedOnRole::class,
            'not.admin' => \App\Http\Middleware\CheckNotAdmin::class,
            'module.edificar' => \App\Http\Middleware\EnsureEdificarModule::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();