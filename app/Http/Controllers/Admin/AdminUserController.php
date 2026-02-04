<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        $roles = AdminRole::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['nullable', 'exists:admin_roles,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['role_id'])) {
            $user->adminRoles()->sync([$data['role_id']]);
        }

        return redirect()->route('admin.users.index')
            ->with('status', __('app.create'));
    }

    public function edit(User $user)
    {
        $roles = AdminRole::orderBy('name')->get();
        $currentRoleId = $user->adminRoles()->pluck('admin_roles.id')->first();

        return view('admin.users.edit', compact('user', 'roles', 'currentRoleId'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role_id' => ['nullable', 'exists:admin_roles,id'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        $user->adminRoles()->sync(!empty($data['role_id']) ? [$data['role_id']] : []);

        return redirect()->route('admin.users.index')
            ->with('status', __('app.save_changes'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', __('app.delete'));
    }
}
