<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SetTenantConnection
{
    public function handle(Request $request, Closure $next)
    {
        // 1️⃣ استخرج subdomain: أولوية لباراميتر المسار ثم من الدومين
        $subdomain = $request->route('subdomain');
        $host = $request->getHost();
        $baseHost = parse_url(config('app.url'), PHP_URL_HOST);

        if (!$subdomain) {
            if ($host !== $baseHost && str_ends_with($host, $baseHost)) {
                $subdomain = str_replace('.' . $baseHost, '', $host);
            }
        }

        abort_unless($subdomain, 404, 'Tenant subdomain missing');

        // 2️⃣ اجلب tenant من القاعدة الرئيسية
        $tenant = DB::connection('mysql')
            ->table('tenants')
            ->where('Subdomain', $subdomain)
            ->where('IsActive', 1)
            ->first();

        abort_unless($tenant, 404, 'Tenant not found');

        // 3️⃣ اسم قاعدة البيانات: استخدم DBName إن وجد، وإلا subdomain، مع تنظيف الفراغات
        $dbName = trim((string)($tenant->DBName ?? ''));
        if ($dbName === '') {
            $dbName = $subdomain;
        }

        if ($dbName === '') {
            abort(500, 'Tenant database name missing');
        }

        // 4️⃣ اضبط اتصال tenant
        Config::set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => $tenant->DBHost ?: config('database.connections.mysql.host'),
            'port' => $tenant->DBPort ?: config('database.connections.mysql.port'),
            'database' => $dbName,
            'username' => 'root',
            'password' => null,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');

        // تأكيد الاتصال بقاعدة بيانات المستأجر بدون تغيير الاتصال الافتراضي
        try {
            $currentDb = DB::connection('tenant')->getDatabaseName();
            if ($currentDb !== $dbName) {
                abort(500, 'لم يتم تفعيل قاعدة بيانات المستأجر بعد التهيئة: '.$dbName);
            }
        } catch (\Throwable $e) {
            abort(500, 'تعذر الاتصال بقاعدة بيانات المستأجر: '.$dbName.' | '.$e->getMessage());
        }

        return $next($request);
    }
}
