<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantComplaint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalSubscribers = Tenant::count();

        // Distribution by plan (used in table + chart)
        $plans = Tenant::select('Plan', DB::raw('count(*) as total'))
            ->groupBy('Plan')
            ->orderBy('Plan')
            ->get();

        // High-level subscriber stats (mirroring reports overview)
        $activeSubscribers = Tenant::where('IsActive', 1)->count();
        $inactiveSubscribers = Tenant::where('IsActive', 0)->count();

        $today = Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth();
        $startOfWeek = $today->copy()->startOfWeek();

        $newThisMonth = Tenant::whereDate('created_at', '>=', $startOfMonth)->count();
        $newThisWeek = Tenant::whereDate('created_at', '>=', $startOfWeek)->count();

        // Expired and expiring-soon subscriptions
        $expiredSubscribers = Tenant::whereNotNull('SubscriptionEndDate')
            ->where('SubscriptionEndDate', '<', $today)
            ->count();

        $expiringSoonWindowDays = 30;
        $expiringSoonSubscribers = Tenant::whereNotNull('SubscriptionEndDate')
            ->whereBetween('SubscriptionEndDate', [$today, $today->copy()->addDays($expiringSoonWindowDays)])
            ->count();

        // Simple time series for last 6 months (cumulative growth)
        $months = [];
        $seriesActive = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthEnd = $today->copy()->subMonths($i)->endOfMonth();
            $label = $monthEnd->format('Y-m');

            $countForMonth = Tenant::whereDate('created_at', '<=', $monthEnd)->count();

            $months[] = $label;
            $seriesActive[] = $countForMonth;
        }

        // Top plan by number of subscribers
        $topPlan = $plans->sortByDesc('total')->first();
        $topPlanName = $topPlan ? $topPlan->Plan : null;
        $topPlanTotal = $topPlan ? (int) $topPlan->total : 0;

        // Most complaining tenants (overall count)
        $mostComplainingTenants = TenantComplaint::select('tenant_id', DB::raw('count(*) as total'))
            ->groupBy('tenant_id')
            ->orderByDesc('total')
            ->with('tenant')
            ->limit(5)
            ->get();

        $topComplaintsChartData = $mostComplainingTenants->map(function (TenantComplaint $row) {
            $name = $row->tenant ? $row->tenant->TenantName : ($row->tenant_subdomain ?? 'N/A');

            return [
                'name' => $name,
                'total' => (int) $row->total,
            ];
        })->values();

        // High-level complaints metrics
        $totalComplaints = TenantComplaint::count();
        $openComplaints = TenantComplaint::where('status', 'open')->count();

        return view('admin.dashboard', [
            'totalSubscribers'          => $totalSubscribers,
            'plans'                     => $plans,
            'activeSubscribers'         => $activeSubscribers,
            'inactiveSubscribers'       => $inactiveSubscribers,
            'newThisMonth'              => $newThisMonth,
            'newThisWeek'               => $newThisWeek,
            'months'                    => $months,
            'seriesActive'              => $seriesActive,
            'expiredSubscribers'        => $expiredSubscribers,
            'expiringSoonSubscribers'   => $expiringSoonSubscribers,
            'expiringSoonWindowDays'    => $expiringSoonWindowDays,
            'topPlanName'               => $topPlanName,
            'topPlanTotal'              => $topPlanTotal,
            'mostComplainingTenants'    => $mostComplainingTenants,
            'topComplaintsChartData'    => $topComplaintsChartData,
            'totalComplaints'           => $totalComplaints,
            'openComplaints'            => $openComplaints,
        ]);
    }
}
