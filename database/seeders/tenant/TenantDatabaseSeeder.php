<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\TenantUser;
use App\Models\TenantRole;
use App\Models\TenantPermission;
use App\Models\TenantSetting;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default permissions
        $permissions = [
            'manage users',
            'manage roles',
            'manage permissions',
            'view dashboard',
            'Attachement',
        ];
        foreach ($permissions as $p) {
            TenantPermission::firstOrCreate(['name' => $p, 'guard_name' => 'tenant']);
        }

        // Create admin role and assign all permissions
        $adminRole = TenantRole::firstOrCreate(['name' => 'admin', 'guard_name' => 'tenant']);
        $adminRole->syncPermissions(TenantPermission::all());

        // Bootstrap admin user (prefer per-tenant values passed via config)
        $configEmail = config('tenant.provision.admin_email');
        $configName = config('tenant.provision.admin_name');

        $adminEmail = $configEmail ?: (env('TENANT_ADMIN_EMAIL') ?: 'admin@test.test');
        $adminName = $configName ?: 'Administrator';
        // استخدم كلمة مرور ثابتة من env أو القيمة الافتراضية
        $plainPassword = env('TENANT_ADMIN_PASSWORD') ?: 'Admin@123456';

        $user = TenantUser::firstOrCreate(
            ['email' => $adminEmail],
            ['name' => $adminName, 'password' => Hash::make($plainPassword)]
        );
        // ألزمه بتغيير كلمة المرور في أول تسجيل دخول
        if (! $user->must_change_password) {
            $user->must_change_password = true;
            $user->save();
        }
        if (!$user->hasRole('admin')) {
            $user->assignRole($adminRole);
        }

        // Ensure a default tenant setting exists (name/logo/color)
        TenantSetting::firstOrCreate(
            [],
            [
                'name' => config('app.name', 'Tenant Workspace'),
                'primary_color' => '#102c4f',
                'logo_path' => null,
            ]
        );
    }
}
