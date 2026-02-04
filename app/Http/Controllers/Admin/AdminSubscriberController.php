<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;

class AdminSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::query();

        if ($request->filled('plan')) {
            $query->where('Plan', $request->string('plan'));
        }

        // Fetch all tenants and let DataTables handle pagination/sorting on the client side
        $tenants = $query->orderByDesc('JoinDate')->get();

        // احسب عدد المستخدمين لكل تينانت من قاعدة بياناته المنفصلة
        foreach ($tenants as $tenant) {
            $tenant->user_count = $this->getTenantUserCount($tenant);
        }

        $plans = Tenant::select('Plan')->distinct()->orderBy('Plan')->pluck('Plan');

        return view('admin.subscribers.index', [
            'tenants' => $tenants,
            'plans' => $plans,
            'currentPlan' => $request->string('plan')->toString(),
        ]);
    }

    public function health(Tenant $tenant)
    {
        $totalUsers = $this->getTenantUserCount($tenant);
        $activeUsers7d = $this->getTenantActiveUsersRecent($tenant, 7);
        $lastLogin = $this->getTenantLastLogin($tenant);
        $complaints = $this->getTenantComplaintsStats($tenant);

        $now = Carbon::now();
        $subscriptionEnd = $tenant->SubscriptionEndDate ? Carbon::parse($tenant->SubscriptionEndDate) : null;
        $trialEnd = $tenant->TrialEndDate ? Carbon::parse($tenant->TrialEndDate) : null;

        $daysToEnd = $subscriptionEnd ? (int) $now->diffInDays($subscriptionEnd, false) : null;
        $daysToTrialEnd = $trialEnd ? $now->diffInDays($trialEnd, false) : null;

        $billingStatus = 'none';
        if ($trialEnd && $now->lt($trialEnd)) {
            $billingStatus = 'trial';
        } elseif ($subscriptionEnd) {
            if ($now->gt($subscriptionEnd)) {
                $billingStatus = 'expired';
            } elseif ($daysToEnd !== null && $daysToEnd <= 14) {
                $billingStatus = 'expiring_soon';
            } else {
                $billingStatus = 'active';
            }
        }

        $billing = [
            'plan' => $tenant->Plan,
            'status' => $billingStatus,
            'subscription_end' => $subscriptionEnd,
            'trial_end' => $trialEnd,
            'days_to_end' => $daysToEnd,
            'days_to_trial_end' => $daysToTrialEnd,
        ];

        return view('admin.subscribers.health', [
            'tenant' => $tenant,
            'totalUsers' => $totalUsers,
            'activeUsers7d' => $activeUsers7d,
            'lastLogin' => $lastLogin,
            'complaints' => $complaints,
            'billing' => $billing,
        ]);
    }

    public function risks(Request $request)
    {
        $windowDays = 30;
        $now = Carbon::now();

        $tenants = Tenant::whereNotNull('SubscriptionEndDate')
            ->orderBy('SubscriptionEndDate')
            ->get();

        $items = [];

        foreach ($tenants as $tenant) {
            if (! $tenant->SubscriptionEndDate) {
                continue;
            }

            $end = Carbon::parse($tenant->SubscriptionEndDate);
            $daysToEnd = (int) $now->diffInDays($end, false);

            if ($daysToEnd > $windowDays) {
                continue; // ليس قريب الانتهاء
            }

            $activeUsers7d = $this->getTenantActiveUsersRecent($tenant, 7);
            $complaints = $this->getTenantComplaintsStats($tenant);

            $openComplaints = $complaints['open'] ?? 0;
            $recentComplaints = $complaints['recent_30d'] ?? 0;

            $score = 0;

            // قرب انتهاء الاشتراك
            if ($daysToEnd <= 14) {
                $score += 2;
            } elseif ($daysToEnd <= 30) {
                $score += 1;
            }

            // استخدام قليل (مستخدم نشط واحد أو أقل خلال 7 أيام)
            if (($activeUsers7d ?? 0) <= 1) {
                $score += 1;
            }

            // بلاغات كثيرة أو مفتوحة
            if ($openComplaints >= 3 || $recentComplaints >= 5) {
                $score += 1;
            }

            // أي مشترك داخل نافذة الأيام يعتبر ضمن لوحة المخاطر
            if ($score >= 1) {
                if ($score >= 3) {
                    $riskLevel = 'high';
                } elseif ($score === 2) {
                    $riskLevel = 'medium';
                } else {
                    $riskLevel = 'low';
                }

                $items[] = [
                    'tenant' => $tenant,
                    'days_to_end' => (int) $daysToEnd,
                    'active_users_7d' => $activeUsers7d,
                    'open_complaints' => $openComplaints,
                    'recent_complaints' => $recentComplaints,
                    'risk_score' => $score,
                    'risk_level' => $riskLevel,
                ];
            }
        }

        usort($items, function (array $a, array $b) {
            if ($a['risk_score'] === $b['risk_score']) {
                return $a['days_to_end'] <=> $b['days_to_end'];
            }

            return $b['risk_score'] <=> $a['risk_score'];
        });

        return view('admin.subscribers.risks', [
            'items' => $items,
            'windowDays' => $windowDays,
        ]);
    }

    public function toggleStatus(Tenant $tenant)
    {
        $tenant->IsActive = ! (bool) $tenant->IsActive;
        $tenant->Status = $tenant->IsActive ? 1 : 0;

        $tenant->save();

        return back()->with('status', __('app.tenant_status_updated'));
    }

    protected function getTenantUserCount(Tenant $tenant): ?int
    {
        try {
            $dbName = trim((string)($tenant->DBName ?? ''));
            if ($dbName === '') {
                $dbName = $tenant->Subdomain;
            }

            if ($dbName === '') {
                return null;
            }

            $connectionConfig = [
                'driver' => 'mysql',
                'host' => $tenant->DBHost ?: config('database.connections.mysql.host'),
                'port' => $tenant->DBPort ?: config('database.connections.mysql.port'),
                'database' => $dbName,
                'username' => 'root',
                'password' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
            ];

            Config::set('database.connections.tenant', $connectionConfig);
            DB::purge('tenant');

            return DB::connection('tenant')->table('users')->count();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function getTenantLastLogin(Tenant $tenant): ?Carbon
    {
        try {
            $dbName = trim((string)($tenant->DBName ?? ''));
            if ($dbName === '') {
                $dbName = $tenant->Subdomain;
            }

            if ($dbName === '') {
                return null;
            }

            $connectionConfig = [
                'driver' => 'mysql',
                'host' => $tenant->DBHost ?: config('database.connections.mysql.host'),
                'port' => $tenant->DBPort ?: config('database.connections.mysql.port'),
                'database' => $dbName,
                'username' => 'root',
                'password' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
            ];

            Config::set('database.connections.tenant', $connectionConfig);
            DB::purge('tenant');

            // Use the activity_logs table to determine the most recent
            // recorded activity for any tenant user.
            $lastActivity = DB::connection('tenant')->table('activity_logs')
                ->whereNotNull('user_id')
                ->max('created_at');

            if (! $lastActivity) {
                return null;
            }

            return Carbon::parse($lastActivity);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function getTenantActiveUsersRecent(Tenant $tenant, int $days = 7): ?int
    {
        try {
            $dbName = trim((string)($tenant->DBName ?? ''));
            if ($dbName === '') {
                $dbName = $tenant->Subdomain;
            }

            if ($dbName === '') {
                return null;
            }

            $connectionConfig = [
                'driver' => 'mysql',
                'host' => $tenant->DBHost ?: config('database.connections.mysql.host'),
                'port' => $tenant->DBPort ?: config('database.connections.mysql.port'),
                'database' => $dbName,
                'username' => 'root',
                'password' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
            ];

            Config::set('database.connections.tenant', $connectionConfig);
            DB::purge('tenant');

            // Count distinct users who had any recorded activity in the last N days
            $since = Carbon::now()->subDays($days);

            return DB::connection('tenant')->table('activity_logs')
                ->whereNotNull('user_id')
                ->where('created_at', '>=', $since)
                ->distinct('user_id')
                ->count('user_id');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function getTenantComplaintsStats(Tenant $tenant): array
    {
        $tenantId = $tenant->TenantID;

        $open = TenantComplaint::where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->count();

        $total = TenantComplaint::where('tenant_id', $tenantId)->count();

        $recent30 = TenantComplaint::where('tenant_id', $tenantId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $lastAt = TenantComplaint::where('tenant_id', $tenantId)
            ->max('created_at');

        return [
            'open' => $open,
            'total' => $total,
            'recent_30d' => $recent30,
            'last_created_at' => $lastAt ? Carbon::parse($lastAt) : null,
        ];
    }
}
