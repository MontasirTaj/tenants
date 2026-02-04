<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\TenantSetting;
use App\Models\TenantUser;
use App\Models\TenantRole;
use App\Models\TenantPermission;
use App\Models\TenantAttachment;

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

        return view('pages.tenant.dashboard', [
            'user' => $user,
            'tenantSetting' => $tenantSetting,
            'stats' => $stats,
        ]);
    }
}
