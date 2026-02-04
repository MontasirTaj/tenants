<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\TenantUser;
use App\Models\TenantRole as Role;

class TenantUserController extends Controller
{
    public function index(string $subdomain)
    {
        $users = TenantUser::all();
        $roles = Role::all();
        return view('pages.tenant.users.index', compact('users','roles'));
    }

    public function store(Request $request, string $subdomain)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'nullable|integer'
        ]);

        // تحقق من الحد الأقصى لعدد المستخدمين بناءً على باقة المستأجر
        try {
            $tenant = \App\Models\Tenant::on('mysql')->where('Subdomain', $subdomain)->first();
            if ($tenant && $tenant->Plan) {
                $planLimit = \App\Models\PlanLimit::where('plan', $tenant->Plan)->first();
                if ($planLimit && $planLimit->max_users !== null) {
                    $currentCount = TenantUser::count();
                    if ($currentCount >= $planLimit->max_users) {
                        return back()
                            ->withErrors(['general' => __('تم الوصول إلى الحد الأقصى لعدد المستخدمين المسموح به لهذه الباقة.')])
                            ->withInput();
                    }
                }
            }
        } catch (\Throwable $e) {
            // في حال وجود خطأ في قراءة إعدادات الباقة، لا نمنع الإنشاء ولكن يمكن تسجيل الخطأ لاحقًا عند الحاجة
        }
        $user = TenantUser::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
        if (!empty($data['role_id'])) {
            $role = Role::findOrFail($data['role_id']);
            $user->assignRole($role);
        }

        tenant_activity('tenant.users.store', 'create_user', $user, [
            'description' => 'تم إنشاء مستخدم جديد',
            'email' => $user->email,
        ]);

        return back()->with('status', __('تم إنشاء المستخدم'));
    }

    public function edit(string $subdomain, int $user)
    {
        // Resolve user after tenant connection is set by middleware
        $model = TenantUser::findOrFail($user);
        $roles = Role::all();
        return view('pages.tenant.users.edit', ['user' => $model, 'roles' => $roles]);
    }

    public function update(Request $request, string $subdomain, int $user)
    {
        $user = TenantUser::findOrFail($user);
        $originalEmail = $user->email;
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'nullable|integer'
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        if (array_key_exists('role_id', $data)) {
            if (!empty($data['role_id'])) {
                $role = Role::findOrFail($data['role_id']);
                $user->syncRoles([$role]);
            } else {
                $user->syncRoles([]);
            }
        }

        tenant_activity('tenant.users.update', 'update_user', $user, [
            'description' => 'تم تحديث بيانات المستخدم',
            'old_email' => $originalEmail,
            'new_email' => $user->email,
        ]);

        return redirect()->route(
            request()->routeIs('tenant.subdomain.*') ? 'tenant.subdomain.users.index' : 'tenant.users.index',
            ['subdomain' => $subdomain]
        )->with('status', __('تم تحديث المستخدم'));
    }

    public function destroy(string $subdomain, int $user)
    {
        $model = TenantUser::findOrFail($user);

        tenant_activity('tenant.users.destroy', 'delete_user', $model, [
            'description' => 'تم حذف مستخدم',
            'email' => $model->email,
        ]);

        $model->delete();

        return back()->with('status', __('تم حذف المستخدم'));
    }

    public function exportExcel(string $subdomain)
    {
        $users = TenantUser::with('roles')->get();

        $fileName = 'tenant_'.$subdomain.'_users_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                __('app.name'),
                __('app.email'),
                __('app.roles'),
                __('app.created_at'),
            ]);

            foreach ($users as $user) {
                $roles = method_exists($user, 'getRoleNames')
                    ? $user->getRoleNames()->implode(', ')
                    : '';

                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $roles,
                    optional($user->created_at)->toDateTimeString(),
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    public function exportPdf(string $subdomain)
    {
        $users = TenantUser::with('roles')->get();
        // نعرض صفحة قابلة للطباعة، ويمكن للمستخدم حفظها كـ PDF من المتصفح
        return view('exports.tenant.users', [
            'users' => $users,
            'subdomain' => $subdomain,
        ]);
    }
}
