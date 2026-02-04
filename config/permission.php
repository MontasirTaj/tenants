<?php

return [
    'models' => [
        'permission' => App\Models\TenantPermission::class,
        'role' => App\Models\TenantRole::class,
    ],

    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],

    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'spatie.permission.cache',
        'model_key' => 'name',
        // Use array store to avoid needing 'cache' table per-tenant
        'store' => 'array',
    ],

    'defaults' => [
        'guard' => 'tenant',
    ],
];
