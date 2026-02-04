<?php

namespace App\Http\Controllers;

use App\Models\TenantActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TenantActivityController extends Controller
{
    public function index(string $subdomain, Request $request)
    {
        $user = Auth::guard('tenant')->user();
        if (! $user) {
            return redirect()->route('tenant.subdomain.login', ['subdomain' => $subdomain]);
        }

        $perPage = 20;
        $query = TenantActivityLog::with('user')->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('event')) {
            $query->where('event', 'like', '%'.$request->get('event').'%');
        }

        $logs = $query->paginate($perPage)->appends($request->query());

        // Aggregate for last 7 days
        $since = now()->subDays(6)->startOfDay();
        $daily = TenantActivityLog::selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('created_at', '>=', $since)
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $label = $day->format('d/m');
            $chartLabels[] = $label;
            $match = $daily->firstWhere('d', $day->toDateString());
            $chartData[] = $match ? (int) $match->c : 0;
        }

        return view('pages.tenant.activity.index', [
            'user' => $user,
            'subdomain' => $subdomain,
            'logs' => $logs,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
        ]);
    }

    public function exportExcel(string $subdomain, Request $request)
    {
        $userId = $request->filled('user_id') ? $request->integer('user_id') : null;
        $event = $request->filled('event') ? $request->get('event') : null;
        $query = TenantActivityLog::with('user')->orderByDesc('created_at');

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($event) {
            $query->where('event', 'like', '%'.$event.'%');
        }

        $logs = $query->get();

        $fileName = 'tenant_'.$subdomain.'_activity_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                __('app.activity_when'),
                __('app.activity_user'),
                __('app.activity_event'),
                __('app.activity_action'),
                __('app.activity_description'),
            ]);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    optional($log->created_at)->toDateTimeString(),
                    optional($log->user)->name,
                    $log->event,
                    $log->action,
                    $log->description,
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    public function exportPdf(string $subdomain, Request $request)
    {
        $user = Auth::guard('tenant')->user();
        if (! $user) {
            return redirect()->route('tenant.subdomain.login', ['subdomain' => $subdomain]);
        }

        $query = TenantActivityLog::with('user')->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('event')) {
            $query->where('event', 'like', '%'.$request->get('event').'%');
        }

        $logs = $query->get();
        return view('exports.tenant.activity', [
            'logs' => $logs,
            'subdomain' => $subdomain,
        ]);
    }
}
