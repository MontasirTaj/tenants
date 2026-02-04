<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use Illuminate\Http\Request;

class AdminPermissionController extends Controller
{
    public function index()
    {
        $permissions = AdminPermission::orderBy('name')->get();

        return view('admin.permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:admin_permissions,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        AdminPermission::create($data);

        return redirect()->route('admin.permissions.index')
            ->with('status', __('app.create'));
    }

    public function edit(AdminPermission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, AdminPermission $permission)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:admin_permissions,name,'.$permission->id],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $permission->update($data);

        return redirect()->route('admin.permissions.index')
            ->with('status', __('app.save_changes'));
    }

    public function destroy(AdminPermission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('status', __('app.delete'));
    }
}
