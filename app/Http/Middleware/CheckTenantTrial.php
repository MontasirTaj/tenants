<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckTenantTrial
{
    /**
     * Handle an incoming request.
     *
     * إذا كانت الخطة مجانية وتم انتهاء فترة التجربة، نُعيد التوجيه لصفحة الترقية.
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        // استثناء مسارات الترقية نفسها من الفحص حتى لا ندخل في حلقة إعادة توجيه
        if ($routeName && str_starts_with($routeName, 'tenant.subdomain.upgrade.')) {
            return $next($request);
        }

        $subdomain = $route ? $route->parameter('subdomain') : null;
        if (! $subdomain) {
            return $next($request);
        }

        try {
            $tenant = Tenant::on('mysql')->where('Subdomain', $subdomain)->first();
        } catch (\Throwable $e) {
            Log::error('CheckTenantTrial: failed to load tenant', [
                'subdomain' => $subdomain,
                'error' => $e->getMessage(),
            ]);
            return $next($request);
        }

        if (! $tenant) {
            return $next($request);
        }

        // إذا كانت الخطة مجانية نتحقق من تاريخ نهاية التجربة
        if ($tenant->Plan === 'free') {
            $now = Carbon::now();

            // نحاول استخدام TrialEndDate، وإن لم تكن موجودة نحسبها من تاريخ الانضمام JoinDate (7 أيام ناقص يوم)
            if ($tenant->TrialEndDate) {
                $trialEnd = Carbon::parse($tenant->TrialEndDate);
            } elseif ($tenant->JoinDate) {
                $trialEnd = Carbon::parse($tenant->JoinDate)->addWeek()->subDay();
            } else {
                $trialEnd = null;
            }

            if ($trialEnd && $now->gt($trialEnd)) {
                return redirect()->route('tenant.subdomain.upgrade.plans', ['subdomain' => $subdomain])
                    ->with('error', __('انتهت فترة التجربة المجانية، يرجى ترقية الاشتراك لمواصلة الاستخدام.'));
            }
        }

        return $next($request);
    }
}
