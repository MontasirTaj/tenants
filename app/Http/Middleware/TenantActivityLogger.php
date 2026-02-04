<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantActivityLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            if (auth('tenant')->check()) {
                tenant_activity(null, $request->method(), null, [
                    'description' => 'Route hit',
                    'path' => $request->path(),
                    'status' => $response->getStatusCode(),
                ]);
            }
        } catch (\Throwable $e) {
            // لا شيء، مجرد حماية
        }

        return $response;
    }
}
