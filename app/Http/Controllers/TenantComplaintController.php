<?php

namespace App\Http\Controllers;

use App\Models\TenantComplaint;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TenantComplaintController extends Controller
{
    public function index(string $subdomain, Request $request)
    {
        $user = Auth::guard('tenant')->user();
        if (! $user) {
            return redirect()->route('tenant.subdomain.login', ['subdomain' => $subdomain]);
        }

        $tenant = Tenant::where('Subdomain', $subdomain)->firstOrFail();

        $complaints = TenantComplaint::where('tenant_id', $tenant->TenantID)
            ->orderByDesc('created_at')
            ->get();

        return view('pages.tenant.complaints.index', [
            'user' => $user,
            'subdomain' => $subdomain,
            'tenant' => $tenant,
            'complaints' => $complaints,
        ]);
    }

    public function store(string $subdomain, Request $request)
    {
        $user = Auth::guard('tenant')->user();
        if (! $user) {
            return redirect()->route('tenant.subdomain.login', ['subdomain' => $subdomain]);
        }

        $tenant = Tenant::where('Subdomain', $subdomain)->firstOrFail();

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'attachment' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('complaints', 'public');
        }

        TenantComplaint::create([
            'tenant_id' => $tenant->TenantID,
            'tenant_subdomain' => $tenant->Subdomain,
            'reporter_id' => $user->id,
            'reporter_name' => $user->name,
            'reporter_email' => $user->email,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'attachment_path' => $path,
            'status' => 'open',
        ]);

        return redirect()
            ->back()
            ->with('status', __('app.tenant_complaint_submitted'));
    }

    public function show(string $subdomain, TenantComplaint $complaint)
    {
        $user = Auth::guard('tenant')->user();
        if (! $user) {
            return redirect()->route('tenant.subdomain.login', ['subdomain' => $subdomain]);
        }

        $tenant = Tenant::where('Subdomain', $subdomain)->firstOrFail();

        if ($complaint->tenant_id !== $tenant->TenantID) {
            abort(404);
        }

        // عند عرض البلاغ من قبل التينانت نعتبره "مقروء" إذا كان هناك رد أو تم تغيير الحالة
        if (($complaint->status !== 'open' || ! empty($complaint->admin_reply)) && is_null($complaint->tenant_seen_at)) {
            $complaint->tenant_seen_at = now();
            $complaint->save();
        }

        return view('pages.tenant.complaints.show', [
            'user' => $user,
            'subdomain' => $subdomain,
            'tenant' => $tenant,
            'complaint' => $complaint,
        ]);
    }

    public function feed(string $subdomain, Request $request)
    {
        $user = Auth::guard('tenant')->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $tenant = Tenant::where('Subdomain', $subdomain)->firstOrFail();

        // bell في التينانت: البلاغات التي تم الرد عليها أو تغيّرَت حالتها
        // ولم يقم التينانت بمشاهدتها بعد (tenant_seen_at IS NULL)
        $complaints = TenantComplaint::where('tenant_id', $tenant->TenantID)
            ->whereNull('tenant_seen_at')
            ->where(function ($q) {
                $q->where('status', '!=', 'open')
                  ->orWhereNotNull('admin_reply');
            })
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get();

        $pendingCount = TenantComplaint::where('tenant_id', $tenant->TenantID)
            ->whereNull('tenant_seen_at')
            ->where(function ($q) {
                $q->where('status', '!=', 'open')
                  ->orWhereNotNull('admin_reply');
            })
            ->count();

        $counts = [
            'pending' => (int) $pendingCount,
        ];

        return response()->json([
            'items' => $complaints->map(function (TenantComplaint $complaint) use ($subdomain) {
                $changedAt = $complaint->admin_replied_at ?: $complaint->updated_at ?: $complaint->created_at;
                return [
                    'id' => $complaint->id,
                    'subject' => $complaint->subject,
                    'status' => $complaint->status,
                    'has_reply' => ! empty($complaint->admin_reply),
                    'changed_at' => optional($changedAt)->format('Y-m-d H:i'),
                    'show_url' => route('tenant.subdomain.complaints.show', [
                        'subdomain' => $subdomain,
                        'complaint' => $complaint->id,
                    ]),
                ];
            }),
            'counts' => $counts,
        ]);
    }
}
