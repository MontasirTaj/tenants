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

    /**
     * عرض جميع الأدوار مع الصلاحيات المرتبطة بكل دور.
     */
    public function withPermissions(string $subdomain)
    {
        $roles = Role::with('permissions')->get();
        return view('pages.tenant.roles.with-permissions', compact('roles'));
    }
}
