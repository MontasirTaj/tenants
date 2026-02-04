<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminAclSeeder extends Seeder
{
    public function run(): void
    {
        // Create base permissions for main admin panel
        $permissionNames = [
            'manage_users',
            'manage_roles',
            'manage_permissions',
            'view_subscriptions',
        ];

        $permissions = [];
        foreach ($permissionNames as $name) {
            $permissions[] = AdminPermission::firstOrCreate(
                ['name' => $name],
                ['description' => ucfirst(str_replace('_', ' ', $name))]
            );
        }

        // Create admin role with all permissions
        $adminRole = AdminRole::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Super administrator']
        );

        $adminRole->permissions()->sync(collect($permissions)->pluck('id')->all());

        // Attach admin role to first user (e.g. test@example.com)
        $user = User::where('email', 'test@example.com')->orWhere('id', 1)->first();
        if ($user) {
            $user->adminRoles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}
