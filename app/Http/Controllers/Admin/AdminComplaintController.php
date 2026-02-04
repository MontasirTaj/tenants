<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminComplaintController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString() ?: null;

        $query = TenantComplaint::with('tenant')->orderByDesc('created_at');

        if ($status && in_array($status, ['open', 'in_progress', 'closed'], true)) {
            $query->where('status', $status);
        }

        $complaints = $query->limit(100)->get();

        $stats = [
            'total' => TenantComplaint::count(),
            'open' => TenantComplaint::where('status', 'open')->count(),
            'in_progress' => TenantComplaint::where('status', 'in_progress')->count(),
            'closed' => TenantComplaint::where('status', 'closed')->count(),
        ];

        return view('admin.complaints.index', [
            'complaints' => $complaints,
            'stats' => $stats,
            'currentStatus' => $status,
        ]);
    }

    public function show(TenantComplaint $complaint)
    {
        $complaint->load('tenant');

        return view('admin.complaints.show', [
            'complaint' => $complaint,
        ]);
    }

    public function reply(Request $request, TenantComplaint $complaint)
    {
        $validated = $request->validate([
            'admin_reply' => ['required', 'string'],
            'status' => ['nullable', 'in:open,in_progress,closed'],
        ]);

        $complaint->admin_reply = $validated['admin_reply'];
        $complaint->admin_user_id = Auth::id();
        $complaint->admin_replied_at = now();
        // عند وجود رد جديد نعيد ضبط حالة القراءة للتينانت ليظهر في جرسه
        $complaint->tenant_seen_at = null;

        if (! empty($validated['status'])) {
            $complaint->status = $validated['status'];
        } elseif ($complaint->status === 'open') {
            $complaint->status = 'closed';
        }

        $complaint->save();

        // TODO: يمكن لاحقاً إرسال إشعار أو بريد للتينانت هنا

        return redirect()
            ->back()
            ->with('status', __('app.admin_complaint_reply_saved'));
    }

    public function feed(Request $request)
    {
        $status = $request->string('status')->toString() ?: null;
        $onlyUnreplied = $request->boolean('only_unreplied');

        $query = TenantComplaint::with('tenant')->orderByDesc('created_at');

        if ($status && in_array($status, ['open', 'in_progress', 'closed'], true)) {
            $query->where('status', $status);
        }

        if ($onlyUnreplied) {
            // في لوحة التحكم الأم: نريد البلاغات الجديدة التي لم يُرَد عليها ولم تتغير حالتها (ما زالت open)
            $query->whereNull('admin_reply')
                  ->where('status', 'open');
        }

        $complaints = $query->limit(100)->get();

        $data = $complaints->map(function (TenantComplaint $complaint) {
            return [
                'id' => $complaint->id,
                'tenant_name' => optional($complaint->tenant)->TenantName,
                'tenant_subdomain' => optional($complaint->tenant)->Subdomain,
                'subject' => $complaint->subject,
                'status' => $complaint->status,
                'has_reply' => ! empty($complaint->admin_reply),
                'created_at' => optional($complaint->created_at)->format('Y-m-d H:i'),
                'show_url' => route('admin.complaints.show', $complaint),
            ];
        });

        $newUnreplied = TenantComplaint::where('status', 'open')
            ->whereNull('admin_reply')
            ->count();

        return response()->json([
            'items' => $data,
            'counts' => [
                'total' => TenantComplaint::count(),
                'open' => TenantComplaint::where('status', 'open')->count(),
                'in_progress' => TenantComplaint::where('status', 'in_progress')->count(),
                'closed' => TenantComplaint::where('status', 'closed')->count(),
                'new_unreplied' => (int) $newUnreplied,
            ],
        ]);
    }
}
