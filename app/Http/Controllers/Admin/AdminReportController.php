<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function overview()
    {
        $total = Tenant::count();
        $active = Tenant::where('IsActive', 1)->count();
        $inactive = Tenant::where('IsActive', 0)->count();

        $today = now();
        $startOfMonth = $today->copy()->startOfMonth();
        $startOfWeek = $today->copy()->startOfWeek();

        $newThisMonth = Tenant::whereDate('created_at', '>=', $startOfMonth)->count();
        $newThisWeek = Tenant::whereDate('created_at', '>=', $startOfWeek)->count();

        // If you have a cancellation flag/column, it can be added later
        $canceledThisMonth = 0;

        // Distribution by plan for chart/table
        $byPlan = Tenant::select('Plan', DB::raw('count(*) as total'))
            ->groupBy('Plan')
            ->orderBy('Plan')
            ->get();

        // Build simple time series (last 6 months)
        $months = [];
        $seriesActive = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = $today->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $today->copy()->subMonths($i)->endOfMonth();
            $label = $monthStart->format('Y-m');

            // Approx: tenants created up to end of month and still active (if you add cancellation later you can refine)
            $countForMonth = Tenant::whereDate('created_at', '<=', $monthEnd)->count();

            $months[] = $label;
            $seriesActive[] = $countForMonth;
        }

        return view('admin.reports.overview', [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'newThisMonth' => $newThisMonth,
            'newThisWeek' => $newThisWeek,
            'canceledThisMonth' => $canceledThisMonth,
            'byPlan' => $byPlan,
            'months' => $months,
            'seriesActive' => $seriesActive,
        ]);
    }

    public function upcomingExpirations()
    {
        // Assuming SubscriptionEndDate column on tenants table
        $today = now();
        $limitDate = $today->copy()->addDays(30);

        $tenants = Tenant::whereNotNull('SubscriptionEndDate')
            ->whereBetween('SubscriptionEndDate', [$today, $limitDate])
            ->orderBy('SubscriptionEndDate')
            ->get();

        return view('admin.reports.upcoming-expirations', [
            'tenants' => $tenants,
            'today' => $today,
            'limitDate' => $limitDate,
        ]);
    }

    public function plans()
    {
        $byPlan = Tenant::select('Plan', DB::raw('count(*) as total'))
            ->groupBy('Plan')
            ->orderBy('Plan')
            ->get();

        return view('admin.reports.plans', [
            'byPlan' => $byPlan,
        ]);
    }
}
