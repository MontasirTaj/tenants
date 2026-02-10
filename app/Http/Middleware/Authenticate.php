<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Determine if request is on a tenant subdomain versus path-based
        $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
        $host = $request->getHost();
        $isSubdomainContext = ($host !== $baseHost) && str_ends_with($host, $baseHost);
        $isSubRoute = $request->routeIs('tenant.subdomain.*');
        $isPathBasedRoute = $request->routeIs('tenant.*');

        $subdomain = $request->route('subdomain');

        $locale = app()->getLocale();
        if (!$locale) {
            $locale = LaravelLocalization::getCurrentLocale() ?: config('app.locale', 'ar');
        }

        // Build login path context-aware
        if ($isSubdomainContext || $isSubRoute) {
            // Tenant subdomain login
            $path = '/'.$locale.'/login';
        } elseif ($isPathBasedRoute && $subdomain) {
            // Path-based tenant login (rare)
            $path = '/'.$locale.'/tenant/'.$subdomain.'/login';
        } elseif ($request->routeIs('admin.*') || str_starts_with($request->path(), $locale.'/admin')) {
            // Company admin area login
            $path = '/'.$locale.'/admin/login';
        } else {
            // Fallback: send to admin login on main domain
            $path = '/'.$locale.'/admin/login';
        }
        // Return direct path to avoid locale middleware interference on subdomains
        return $path;
    }
}
