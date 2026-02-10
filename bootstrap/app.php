<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Load global helper functions
require_once __DIR__ . '/../app/Helpers/helpers.php';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Define middleware aliases for localization
        $middleware->alias([
            'localize'              => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'  => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'  => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'        => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            // Override default auth middleware to use tenant-aware redirect
            'auth'                  => \App\Http\Middleware\Authenticate::class,
            'tenant.auth'           => \App\Http\Middleware\TenantAuthenticate::class,
            // Spatie permissions middleware aliases (package namespace: Spatie\\Permission\\Middleware)
            'permission'            => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role'                  => \Spatie\Permission\Middleware\RoleMiddleware::class,
            // Inactivity auto-logout
            'auto.logout'           => \App\Http\Middleware\AutoLogoutInactive::class,
        ]);

        // Apply inactivity auto-logout to all web routes (only affects authenticated users)
        $middleware->web(append: [
            \App\Http\Middleware\AutoLogoutInactive::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
