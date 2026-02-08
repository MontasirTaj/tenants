<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TenantRole as Role;
use App\Models\TenantPermission as Permission;

class TenantRoleController extends Controller
{
    public function index(string $subdomain)
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('pages.tenant.roles.index', compact('roles','permissions'));
    }

    public function store(Request $request, string $subdomain)
    {
        $data = $request->validate(['name' => 'required|string|max:64']);
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'tenant']);

        tenant_activity('tenant.roles.store', 'create_role', $role, [
            'description' => 'تم إنشاء دور جديد',
            'name' => $role->name,
        ]);

        return back()->with('status', __('تم إنشاء الدور'));
    }

    public function attachPermission(Request $request, string $subdomain)
    {
        $data = $request->validate(['role_id' => 'required|integer', 'permission_id' => 'required|integer']);
        $role = Role::findOrFail($data['role_id']);
        $permission = Permission::findOrFail($data['permission_id']);
        $role->givePermissionTo($permission);

        tenant_activity('tenant.roles.attach_permission', 'attach_permission', $role, [
            'description' => 'تم ربط صلاحية بدور',
            'role' => $role->name,
            'permission' => $permission->name,
        ]);

        return back()->with('status', __('تم ربط الصلاحية بالدور'));
    }

    public function edit(string $subdomain, int $role)
    {
        $model = Role::findOrFail($role);
        return view('pages.tenant.roles.edit', ['role' => $model]);
    }

    public function update(Request $request, string $subdomain, int $role)
    {
        $model = Role::findOrFail($role);

        $data = $request->validate([
            // نستخدم اتصال "tenant" لضمان التحقق داخل قاعدة بيانات المستأجر
            'name' => 'required|string|max:64|unique:tenant.roles,name,' . $model->id,
        ]);

        $oldName = $model->name;
        $model->name = $data['name'];
        $model->save();

        tenant_activity('tenant.roles.update', 'update_role', $model, [
            'description' => 'تم تحديث اسم الدور',
            'old_name' => $oldName,
            'new_name' => $model->name,
        ]);

        return redirect()->route('tenant.subdomain.roles.index', ['subdomain' => $subdomain])
            ->with('status', __('تم تحديث الدور'));
    }

    public function destroy(string $subdomain, int $role)
    {
        $model = Role::findOrFail($role);

        tenant_activity('tenant.roles.destroy', 'delete_role', $model, [
            'description' => 'تم حذف دور',
            'name' => $model->name,
        ]);

        $model->delete();

        return back()->with('status', __('تم حذف الدور'));
    }

    /**
     * عرض جميع الأدوار مع الصلاحيات المرتبطة بكل دور.
     */
    public function withPermissions(string $subdomain)
    {
        $roles = Role::with('permissions')->get();
        return view('pages.tenant.roles.with-permissions', compact('roles'));
    }
}
