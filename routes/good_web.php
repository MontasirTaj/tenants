<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TenantSignupController;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\TenantDashboardController;
use App\Http\Controllers\TenantUserController;
use App\Http\Controllers\TenantRoleController;
use App\Http\Controllers\TenantPermissionController;

/*
|--------------------------------------------------------------------------
| Stripe Webhook (بدون ترجمة)
|--------------------------------------------------------------------------
*/
Route::post('/stripe/webhook', [CashierWebhookController::class, 'handleWebhook'])
    ->name('cashier.webhook');

/*
|--------------------------------------------------------------------------
| Redirect root → default locale
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect(
        LaravelLocalization::getLocalizedURL(
            LaravelLocalization::getDefaultLocale(),
            '/'
        )
    );
});

/*
|--------------------------------------------------------------------------
| MAIN DOMAIN ROUTES (Signup + Subscription)
|--------------------------------------------------------------------------
*/
Route::domain(parse_url(config('app.url'), PHP_URL_HOST))
->group(function () {

    Route::group([
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [
            'localize',
            'localeSessionRedirect',
            'localizationRedirect',
            'localeViewPath'
        ]
    ], function () {

        // Landing page
        Route::get('/', function () {
            return view('landing');
        })->name('landing');

        // Plans & Signup
        Route::get('plans', [TenantSignupController::class, 'plans'])
            ->name('tenants.plans');

        Route::get('signup/{plan}', [TenantSignupController::class, 'showSignup'])
            ->name('tenants.signup');

        Route::post('signup', [TenantSignupController::class, 'store'])
            ->name('tenants.store');

        // Start subscription (Stripe Checkout)
        Route::get('subscribe/{package}', [SubscriptionController::class, 'subscribe'])
            ->name('subscribe.start');
    });

    // Stripe redirects (بدون locale)
    Route::get('subscribe/success', [SubscriptionController::class, 'success'])
        ->name('subscribe.success');

    Route::get('subscribe/cancel', [SubscriptionController::class, 'cancel'])
        ->name('subscribe.cancel');

    Route::get('signup/success/{tenant}', [TenantSignupController::class, 'success'])
        ->name('tenants.success');

    Route::get('signup/cancel/{tenant}', [TenantSignupController::class, 'cancel'])
        ->name('tenants.cancel');
});

/*
|--------------------------------------------------------------------------
| TENANT ROUTES (Subdomain-based)
|--------------------------------------------------------------------------
*/
Route::domain('{subdomain}.' . parse_url(config('app.url'), PHP_URL_HOST))
->group(function () {

    // ثابت: جميع مسارات المستأجر على النطاق الفرعي تحت /ar مع وسيط اتصال قاعدة بيانات المستأجر فقط
    Route::group([
        'prefix' => 'ar',
        'middleware' => [
            \App\Http\Middleware\SetTenantConnection::class
        ]
    ], function () {

        /*
        |-----------------------
        | Tenant Authentication
        |-----------------------
        */
        Route::get('login', [TenantAuthController::class, 'showLoginForm'])
            ->name('tenant.subdomain.login');

        Route::post('login', [TenantAuthController::class, 'login'])
            ->name('tenant.subdomain.login.post');

        Route::post('logout', [TenantAuthController::class, 'logout'])
            ->name('tenant.subdomain.logout');

        // تشخيص سريع: يعرض حالة الاتصال وقيم التكوين
        Route::get('diag', function(\Illuminate\Http\Request $request) {
            $host = $request->getHost();
            $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
            $subdomain = $request->route('subdomain');
            $tenant = DB::connection('mysql')->table('tenants')->where('Subdomain', $subdomain)->first();
            $dbName = DB::connection('tenant')->getDatabaseName();
            $configDb = config('database.connections.tenant.database');
            return [
                'host' => $host,
                'base_host' => $baseHost,
                'subdomain_param' => $subdomain,
                'tenant_dbname_from_main' => $tenant->DBName ?? null,
                'tenant_db_active' => $tenant->IsActive ?? null,
                'tenant_db_in_use' => $dbName,
                'tenant_db_config' => $configDb,
                'default_connection' => DB::getDefaultConnection(),
            ];
        })->name('tenant.subdomain.diag');

        /*
        |-----------------------
        | Tenant Dashboard
        |-----------------------
        */

        // فحص الاتصال السريع
        Route::get('db-test', function () {
            return [
                'connection' => DB::getDefaultConnection(),
                'database'   => DB::connection()->getDatabaseName(),
            ];
        })->name('tenant.subdomain.dbtest');

        Route::middleware('tenant.auth')->group(function () {

            Route::get('dashboard', [TenantDashboardController::class, 'index'])
                ->name('tenant.subdomain.dashboard');

            /*
            | Users
            */
            Route::get('users', [TenantUserController::class, 'index'])
                ->name('tenant.subdomain.users.index');

            Route::post('users', [TenantUserController::class, 'store'])
                ->name('tenant.subdomain.users.store');

            Route::get('users/{user}/edit', [TenantUserController::class, 'edit'])
                ->name('tenant.subdomain.users.edit');

            Route::match(['put','patch'], 'users/{user}', [TenantUserController::class, 'update'])
                ->name('tenant.subdomain.users.update');

            Route::delete('users/{user}', [TenantUserController::class, 'destroy'])
                ->name('tenant.subdomain.users.destroy');

            /*
            | Roles
            */
            Route::get('roles', [TenantRoleController::class, 'index'])
                ->name('tenant.subdomain.roles.index');

            Route::post('roles', [TenantRoleController::class, 'store'])
                ->name('tenant.subdomain.roles.store');

            Route::post('roles/attach', [TenantRoleController::class, 'attachPermission'])
                ->name('tenant.subdomain.roles.attach');

            // عرض الأدوار مع صلاحياتها
            Route::get('roles-with-permissions', [TenantRoleController::class, 'withPermissions'])
                ->name('tenant.subdomain.roles.with-permissions');

            /*
            | Permissions
            */
            Route::get('permissions', [TenantPermissionController::class, 'index'])
                ->name('tenant.subdomain.permissions.index');

            Route::post('permissions', [TenantPermissionController::class, 'store'])
                ->name('tenant.subdomain.permissions.store');

            // Attachments page (requires Attachement permission on tenant guard)
            Route::get('attachments', [\App\Http\Controllers\TenantAttachmentController::class, 'index'])
                ->middleware('permission:Attachement,tenant')
                ->name('tenant.subdomain.attachments.index');

            Route::post('attachments', [\App\Http\Controllers\TenantAttachmentController::class, 'store'])
                ->middleware('permission:Attachement,tenant')
                ->name('tenant.subdomain.attachments.store');

            Route::get('attachments/{attachment}/edit', [\App\Http\Controllers\TenantAttachmentController::class, 'edit'])
                ->middleware('permission:Attachement,tenant')
                ->name('tenant.subdomain.attachments.edit');

            Route::match(['put','patch'], 'attachments/{attachment}', [\App\Http\Controllers\TenantAttachmentController::class, 'update'])
                ->middleware('permission:Attachement,tenant')
                ->name('tenant.subdomain.attachments.update');

            Route::delete('attachments/{attachment}', [\App\Http\Controllers\TenantAttachmentController::class, 'destroy'])
                ->middleware('permission:Attachement,tenant')
                ->name('tenant.subdomain.attachments.destroy');
        });

        // JSON users preview (اختبار سريع بدون مصادقة)
        Route::get('users-test', function(\Illuminate\Http\Request $request) {
            $conn = DB::connection('tenant');
            try {
                $users = $conn->table('users')
                    ->select('id','name','email','created_at')
                    ->orderBy('id','desc')
                    ->limit(50)
                    ->get();
                return response()->json([
                    'subdomain' => $request->route('subdomain'),
                    'count' => $users->count(),
                    'users' => $users,
                ]);
            } catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        })->name('tenant.subdomain.users.test');
    });
});
