<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\TenantSetting;
use App\Models\TenantUser;
use App\Models\TenantRole;
use App\Models\TenantPermission;
use App\Models\TenantAttachment;
use App\Models\TenantActivityLog;
use App\Models\TenantComplaint;
use App\Models\Tenant;

class TenantDashboardController extends Controller
{
    public function index(string $subdomain)
    {
        $user = Auth::guard('tenant')->user();
        if (!$user) {
            return redirect()->route('tenant.subdomain.login', ['subdomain' => $subdomain]);
        }
        $tenantSetting = null;
        try {
            $tenantSetting = TenantSetting::query()->first();
        } catch (\Throwable $e) {
            $tenantSetting = null;
        }

        // Basic tenant statistics
        $stats = [
            'users' => 0,
            'roles' => 0,
            'permissions' => 0,
            'attachments' => 0,
        ];
        try {
            $stats['users'] = TenantUser::query()->count();
        } catch (\Throwable $e) {}
        try {
            $stats['roles'] = TenantRole::query()->count();
        } catch (\Throwable $e) {}
        try {
            $stats['permissions'] = TenantPermission::query()->count();
        } catch (\Throwable $e) {}
        try {
            $stats['attachments'] = TenantAttachment::query()->count();
        } catch (\Throwable $e) {}

        // Determine tenant record in main database for subscription info & complaints
        $tenant = null;
        $billing = [
            'plan' => null,
            'status' => 'none',
            'subscription_end' => null,
            'days_to_end' => null,
        ];
        $openComplaints = 0;

        try {
            $tenant = Tenant::on('mysql')->where('Subdomain', $subdomain)->first();
            if ($tenant) {
                $billing['plan'] = $tenant->Plan;

                $now = Carbon::now();
                $subscriptionEnd = $tenant->SubscriptionEndDate ? Carbon::parse($tenant->SubscriptionEndDate) : null;
                $trialEnd = $tenant->TrialEndDate ? Carbon::parse($tenant->TrialEndDate) : null;

                $daysToEnd = $subscriptionEnd ? (int) $now->diffInDays($subscriptionEnd, false) : null;
                $billing['subscription_end'] = $subscriptionEnd;
                $billing['days_to_end'] = $daysToEnd;

                // نعتبر حالة "فترة تجريبية" فقط إذا كانت الخطة الحالية مجانية وما زال داخل مدة التجربة
                if ($tenant->Plan === 'free' && $trialEnd && $now->lt($trialEnd)) {
                    $billing['status'] = 'trial';
                } elseif ($subscriptionEnd) {
                    if ($now->gt($subscriptionEnd)) {
                        $billing['status'] = 'expired';
                    } elseif ($daysToEnd !== null && $daysToEnd <= 14) {
                        $billing['status'] = 'expiring_soon';
                    } else {
                        $billing['status'] = 'active';
                    }
                }

                $openComplaints = TenantComplaint::where('tenant_id', $tenant->TenantID)
                    ->where('status', 'open')
                    ->count();
            }
        } catch (\Throwable $e) {
            // ignore if main tenant record not available
        }

        // Activity metrics from tenant activity logs
        $lastLoginAt = null;
        $lastImportantActivityAt = null;
        $operationsToday = 0;
        $operationsThisWeek = 0;
        try {
            // آخر نشاط (من كل الأنشطة) نعتبره هنا مرجعاً عامّاً
            $lastLoginRaw = TenantActivityLog::max('created_at');
            $lastLoginAt = $lastLoginRaw ? Carbon::parse($lastLoginRaw) : null;

            $importantActions = [
                'create_user',
                'update_user',
                'delete_user',
                'create_role',
                'attach_permission',
                'create_permission',
                'upload_attachment',
                'update_attachment',
                'delete_attachment',
                'send_message',
            ];

            $lastImportantRaw = TenantActivityLog::whereIn('action', $importantActions)
                ->max('created_at');
            $lastImportantActivityAt = $lastImportantRaw ? Carbon::parse($lastImportantRaw) : null;

            // عمليات اليوم وهذا الأسبوع (نستثني Route hit العامة فقط)
            $now = Carbon::now();
            $baseQuery = TenantActivityLog::where(function ($q) {
                $q->whereNull('description')
                    ->orWhere('description', '!=', 'Route hit');
            });

            $operationsToday = (clone $baseQuery)
                ->whereDate('created_at', $now->toDateString())
                ->count();

            $operationsThisWeek = (clone $baseQuery)
                ->where('created_at', '>=', $now->copy()->startOfWeek())
                ->count();
        } catch (\Throwable $e) {
            // ignore if tenant connection not ready
        }

        // Recent activity timeline (excluding generic route hits and messaging events)
        $recentActivities = collect();
        try {
            $recentActivities = TenantActivityLog::with('user')
                ->where(function ($q) {
                    // استبعد سجلات Route hit العامة
                    $q->whereNull('description')
                        ->orWhere('description', '!=', 'Route hit');
                })
                ->where(function ($q) {
                    // استبعد أي أحداث مرتبطة بالرسائل والمحادثات
                    $q->where(function ($inner) {
                        $inner->whereNull('event')
                            ->orWhere('event', 'not like', 'tenant.messages.%');
                    })
                    ->where(function ($inner) {
                        $inner->whereNull('subject_type')
                            ->orWhere('subject_type', '!=', \App\Models\TenantMessage::class);
                    })
                    ->where(function ($inner) {
                        $inner->whereNull('action')
                            ->orWhere('action', '!=', 'send_message');
                    });
                })
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) {
        }

        return view('pages.tenant.dashboard', [
            'user' => $user,
            'tenantSetting' => $tenantSetting,
            'stats' => $stats,
            'billing' => $billing,
            'openComplaints' => $openComplaints,
            'lastLoginAt' => $lastLoginAt,
            'lastImportantActivityAt' => $lastImportantActivityAt,
            'operationsToday' => $operationsToday,
            'operationsThisWeek' => $operationsThisWeek,
            'recentActivities' => $recentActivities,
        ]);
    }
}
