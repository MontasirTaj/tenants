<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AutoLogoutInactive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $isDefaultAuth = Auth::check();
        $isTenantAuth = Auth::guard('tenant')->check();

        if ($isDefaultAuth || $isTenantAuth) {
            $now = now()->timestamp;
            $last = (int) $request->session()->get('lastActivityTime', $now);
            $timeoutMinutes = (int) config('session.lifetime', 10);
            $timeoutSeconds = max(1, $timeoutMinutes) * 60;

            if (($now - $last) >= $timeoutSeconds) {
                if ($isTenantAuth) {
                    Auth::guard('tenant')->logout();
                }
                if ($isDefaultAuth) {
                    Auth::logout();
                }
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Redirect to the appropriate login page depending on area/guard
                if ($isTenantAuth && Route::has('tenant.subdomain.login')) {
                    $subdomain = $request->route('subdomain');
                    $loginUrl = route('tenant.subdomain.login', ['subdomain' => $subdomain]);
                } elseif (Route::has('admin.login')) {
                    $loginUrl = route('admin.login');
                } else {
                    // Fallback to localized landing page root
                    $loginUrl = url('/');
                }
                return redirect($loginUrl)->with('status', __('تم تسجيل الخروج بسبب عدم النشاط'));
            }

            $request->session()->put('lastActivityTime', $now);
        }

        return $next($request);
    }
}
