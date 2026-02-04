<?php

namespace App\Http\Middleware;

use App\Models\TenantUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        $subdomain = $request->route('subdomain');

        // إذا كان هناك مستأجر نشط مختلف عن المستأجر الحالي، اعتبر الجلسة غير صالحة لهذا المستأجر
        $activeSubdomain = $request->session()->get('tenant_active_subdomain');
        if ($activeSubdomain && $activeSubdomain !== $subdomain) {
            Auth::guard('tenant')->logout();
            $request->session()->forget('tenant_active_subdomain');
        }

        // إذا كان الحارس لا يملك مستخدمًا، حاول استعادته من السيشن المربوط بالمستأجر الحالي فقط
        if (! Auth::guard('tenant')->check() && $subdomain) {
            $sessionKey = 'tenant_sessions.' . $subdomain . '.user_id';
            $id = $request->session()->get($sessionKey);
            if ($id) {
                $user = TenantUser::find($id);
                if ($user) {
                    Auth::guard('tenant')->setUser($user);
                } else {
                    $request->session()->forget($sessionKey);
                }
            }
        }

        if (! Auth::guard('tenant')->check()) {
            return redirect()
                ->route('tenant.subdomain.login', ['subdomain' => $subdomain])
                ->with('login_error', __('يرجى تسجيل الدخول'));
        }

        $user = Auth::guard('tenant')->user();

        // إذا كان المستخدم مجبَرًا على تغيير كلمة المرور، امنعه من الوصول إلى أي صفحة أخرى
        // غير صفحة تغيير كلمة المرور وتسجيل الخروج
        if ($user && ($user->must_change_password ?? false)) {
            $routeName = $request->route() ? $request->route()->getName() : null;
            $allowedRoutes = [
                'tenant.subdomain.password.edit',
                'tenant.subdomain.password.update',
                'tenant.subdomain.logout',
            ];

            if (! in_array($routeName, $allowedRoutes, true)) {
                return redirect()->route('tenant.subdomain.password.edit', ['subdomain' => $subdomain]);
            }
        }

        // ثبّت المستأجر النشط الحالي
        if ($subdomain) {
            $request->session()->put('tenant_active_subdomain', $subdomain);
        }

        return $next($request);
    }
}
