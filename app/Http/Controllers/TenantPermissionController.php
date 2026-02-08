<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TenantPermission as Permission;

class TenantPermissionController extends Controller
{
    public function index(string $subdomain)
    {
        $permissions = Permission::all();
        return view('pages.tenant.permissions.index', compact('permissions'));
    }

    public function store(Request $request, string $subdomain)
    {
        $data = $request->validate(['name' => 'required|string|max:64']);
        $permission = Permission::create(['name' => $data['name'], 'guard_name' => 'tenant']);

        tenant_activity('tenant.permissions.store', 'create_permission', $permission, [
            'description' => 'تم إنشاء صلاحية جديدة',
            'name' => $permission->name,
        ]);

        return back()->with('status', __('تم إنشاء الصلاحية'));
    }

    public function edit(string $subdomain, int $permission)
    {
        $model = Permission::findOrFail($permission);
        return view('pages.tenant.permissions.edit', ['permission' => $model]);
    }

    public function update(Request $request, string $subdomain, int $permission)
    {
        $model = Permission::findOrFail($permission);

        $data = $request->validate([
            // نستخدم اتصال "tenant" لضمان التحقق داخل قاعدة بيانات المستأجر
            'name' => 'required|string|max:64|unique:tenant.permissions,name,' . $model->id,
        ]);

        $oldName = $model->name;
        $model->name = $data['name'];
        $model->save();

        tenant_activity('tenant.permissions.update', 'update_permission', $model, [
            'description' => 'تم تحديث اسم الصلاحية',
            'old_name' => $oldName,
            'new_name' => $model->name,
        ]);

        return redirect()->route('tenant.subdomain.permissions.index', ['subdomain' => $subdomain])
            ->with('status', __('تم تحديث الصلاحية'));
    }

    public function exportExcel(string $subdomain)
    {
        $permissions = Permission::all();

        $fileName = 'tenant_'.$subdomain.'_permissions_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () use ($permissions) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                __('app.permission_name'),
                __('app.created_at'),
            ]);

            foreach ($permissions as $permission) {
                fputcsv($handle, [
                    $permission->name,
                    optional($permission->created_at)->toDateTimeString(),
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    public function exportPdf(string $subdomain)
    {
        $permissions = Permission::all();
        return view('exports.tenant.permissions', [
            'permissions' => $permissions,
            'subdomain' => $subdomain,
        ]);
    }

    public function destroy(string $subdomain, int $permission)
    {
        $model = Permission::findOrFail($permission);

        tenant_activity('tenant.permissions.destroy', 'delete_permission', $model, [
            'description' => 'تم حذف صلاحية',
            'name' => $model->name,
        ]);

        $model->delete();

        return back()->with('status', __('تم حذف الصلاحية'));
    }
}
