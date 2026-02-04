<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use App\Models\Tenant;

class IdentifyTenant
{
public function handle($request, Closure $next)
{
    $host = $request->getHost();
    $parts = explode('.', $host);
    $subdomain = $parts[0];

    if (in_array($subdomain, ['www', 'tenants', 'localhost'])) {
        return $next($request);
    }

    $tenant = Tenant::where('Subdomain', $subdomain)
        ->where('IsActive', 1)
        ->first();

    if (!$tenant) {
        abort(404, 'Tenant not found');
    }

    // ✅ التعديل الصحيح
    config([
        'database.connections.tenant.database' => $tenant->DBName,
        'database.connections.tenant.username' => $tenant->DBUser ?? env('DB_USERNAME'),
        'database.connections.tenant.password' => $tenant->DBPassword ?? env('DB_PASSWORD'),
        'database.connections.tenant.host'     => $tenant->DBHost ?? env('DB_HOST'),
        'database.connections.tenant.port'     => $tenant->DBPort ?? env('DB_PORT'),
    ]);

    DB::purge('tenant');
    DB::setDefaultConnection('tenant');
    DB::reconnect('tenant');

    return $next($request);
}

}
