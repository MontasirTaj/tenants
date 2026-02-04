<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\TenantRole;

class ProvisionTenants extends Command
{
    protected $signature = 'tenants:provision {--tenant=} {--force}';
    protected $description = 'Create tenant databases, run tenant migrations, and seed admin users';

    public function handle(): int
    {
        $tenantId = $this->option('tenant');
        $tenants = Tenant::query()
            ->when($tenantId, fn($q) => $q->where('TenantID', $tenantId))
            ->where('IsActive', true)
            ->get();

        if ($tenants->isEmpty()) {
            $this->warn('No active tenants found to provision.');
            return self::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            $this->info("Provisioning tenant #{$tenant->TenantID} ({$tenant->Subdomain})...");

            // 1) Ensure database exists
            $dbName = $tenant->DBName;
            DB::statement('CREATE DATABASE IF NOT EXISTS `'.$dbName.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

            // 2) Configure tenant connection
            $connection = [
                'driver' => 'mysql',
                'host' => $tenant->DBHost ?: config('database.connections.mysql.host'),
                'port' => $tenant->DBPort ?: config('database.connections.mysql.port'),
                'database' => $dbName,
                'username' => 'root',
                'password' => '',
                'unix_socket' => config('database.connections.mysql.unix_socket'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                ]) : [],
            ];
            Config::set('database.connections.tenant', $connection);
            DB::purge('tenant');
            DB::reconnect('tenant');

            // 3) Run tenant migrations
            Artisan::call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--database' => 'tenant',
                '--force' => true,
            ]);
            $this->line(Artisan::output());

            // 4) Run tenant seeder (admin user, roles/permissions)
            Artisan::call('db:seed', [
                '--class' => \Database\Seeders\Tenant\TenantDatabaseSeeder::class,
                '--database' => 'tenant',
                '--force' => true,
            ]);
            $this->line(Artisan::output());

            $this->info("Provisioned tenant #{$tenant->TenantID} and ran TenantDatabaseSeeder.");
        }

        return self::SUCCESS;
    }
}
