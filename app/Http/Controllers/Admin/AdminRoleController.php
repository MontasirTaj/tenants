<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\AdminPermission;
use Illuminate\Http\Request;

class AdminRoleController extends Controller
{
    public function index()
    {
        $roles = AdminRole::with('users')->orderBy('name')->get();
        $permissions = AdminPermission::orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:admin_roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        AdminRole::create($data);

        return redirect()->route('admin.roles.index')
            ->with('status', __('app.create'));
    }

    public function edit(AdminRole $role)
    {
        $permissions = AdminPermission::orderBy('name')->get();
        $assigned = $role->permissions()->pluck('admin_permissions.id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'assigned'));
    }

    public function update(Request $request, AdminRole $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:admin_roles,name,'.$role->id],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:admin_permissions,id'],
        ]);

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('status', __('app.save_changes'));
    }

    public function destroy(AdminRole $role)
    {
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('status', __('app.delete'));
    }
}
