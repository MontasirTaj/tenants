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
use App\Http\Controllers\TenantUpgradeController;
use App\Http\Controllers\TenantProfileController;
use App\Http\Controllers\TenantActivityController;
use App\Http\Controllers\TenantSettingsController;
use App\Http\Controllers\TenantComplaintController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSubscriberController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminPermissionController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminComplaintController;
use App\Models\Plan;

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
            $plans = Plan::active()->orderBy('sort_order')->get();

            return view('landing', compact('plans'));
        })->name('landing');

        // Static marketing pages
        Route::view('faq', 'static.faq')->name('static.faq');
        Route::view('about', 'static.about')->name('static.about');

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

        /*
        |------------------------------------------------------------------
        | MAIN ADMIN (non-tenant) - login & dashboard
        |------------------------------------------------------------------
        */

        // Admin auth (main DB users)
        Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])
            ->name('admin.login');

        Route::post('admin/login', [AdminAuthController::class, 'login'])
            ->name('admin.login.post');

        Route::post('admin/logout', [AdminAuthController::class, 'logout'])
            ->name('admin.logout');

        Route::get('admin/logout-success', function () {
            return view('admin.auth.logout');
        })->name('admin.logout.success');

        // Protected admin area
        Route::middleware('auth:web')->prefix('admin')->as('admin.')->group(function () {
            Route::get('dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            // Complaints from tenants
            Route::get('complaints', [AdminComplaintController::class, 'index'])
                ->name('complaints.index');
            Route::get('complaints/{complaint}', [AdminComplaintController::class, 'show'])
                ->name('complaints.show');
            Route::post('complaints/{complaint}/reply', [AdminComplaintController::class, 'reply'])
                ->name('complaints.reply');
            Route::get('complaints-feed', [AdminComplaintController::class, 'feed'])
                ->name('complaints.feed');

            // Reports (overview disabled for now)
            // Route::get('reports/overview', [AdminReportController::class, 'overview'])
            //     ->name('reports.overview');
            Route::get('reports/upcoming-expirations', [AdminReportController::class, 'upcomingExpirations'])
                ->name('reports.upcoming-expirations');
            Route::get('reports/plans', [AdminReportController::class, 'plans'])
                ->name('reports.plans');

            // Subscribers (Tenants overview)
            Route::get('subscribers', [AdminSubscriberController::class, 'index'])
                ->name('subscribers.index');
            Route::get('subscribers/{tenant}/health', [AdminSubscriberController::class, 'health'])
                ->name('subscribers.health');
            Route::get('subscribers-risks', [AdminSubscriberController::class, 'risks'])
                ->name('subscribers.risks');
            Route::patch('subscribers/{tenant}', [AdminSubscriberController::class, 'toggleStatus'])
                ->name('subscribers.toggle');

            // Main DB users CRUD
            Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
            Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
            Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

            // Subscription plan limits
            Route::get('plans', [\App\Http\Controllers\Admin\AdminPlanLimitController::class, 'index'])
                ->name('plans.index');
            Route::post('plans', [\App\Http\Controllers\Admin\AdminPlanLimitController::class, 'update'])
                ->name('plans.update');

            // Marketing / subscription plans catalog (names, prices, features)
            Route::get('subscription-plans', [AdminPlanController::class, 'index'])
                ->name('subscription-plans.index');
            Route::get('subscription-plans/create', [AdminPlanController::class, 'create'])
                ->name('subscription-plans.create');
            Route::post('subscription-plans', [AdminPlanController::class, 'store'])
                ->name('subscription-plans.store');
            Route::get('subscription-plans/{plan}/edit', [AdminPlanController::class, 'edit'])
                ->name('subscription-plans.edit');
            Route::put('subscription-plans/{plan}', [AdminPlanController::class, 'update'])
                ->name('subscription-plans.update');

            // Tenant DB backups
            Route::get('backups/tenants', [\App\Http\Controllers\Admin\AdminTenantBackupController::class, 'index'])
                ->name('backups.tenants.index');
            Route::post('backups/tenants', [\App\Http\Controllers\Admin\AdminTenantBackupController::class, 'backupAll'])
                ->name('backups.tenants.backupAll');
            Route::post('backups/tenants/{tenant}', [\App\Http\Controllers\Admin\AdminTenantBackupController::class, 'backupTenant'])
                ->name('backups.tenants.backup');

            // Roles CRUD
            Route::get('roles', [AdminRoleController::class, 'index'])->name('roles.index');
            Route::post('roles', [AdminRoleController::class, 'store'])->name('roles.store');
            Route::get('roles/{role}/edit', [AdminRoleController::class, 'edit'])->name('roles.edit');
            Route::put('roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
            Route::delete('roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');

            // Permissions CRUD
            Route::get('permissions', [AdminPermissionController::class, 'index'])->name('permissions.index');
            Route::post('permissions', [AdminPermissionController::class, 'store'])->name('permissions.store');
            Route::get('permissions/{permission}/edit', [AdminPermissionController::class, 'edit'])->name('permissions.edit');
            Route::put('permissions/{permission}', [AdminPermissionController::class, 'update'])->name('permissions.update');
            Route::delete('permissions/{permission}', [AdminPermissionController::class, 'destroy'])->name('permissions.destroy');

            // Payments overview
            Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        });
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

    // جميع مسارات المستأجر على النطاق الفرعي مع دعم تعدد اللغات
    Route::group([
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [
            'localize',
            'localeSessionRedirect',
            'localizationRedirect',
            'localeViewPath',
            \App\Http\Middleware\SetTenantConnection::class,
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

        Route::middleware(['tenant.auth', \App\Http\Middleware\TenantActivityLogger::class, \App\Http\Middleware\CheckTenantTrial::class])->group(function () {

            Route::get('dashboard', [TenantDashboardController::class, 'index'])
                ->name('tenant.subdomain.dashboard');

            // Upgrade from free plan to paid
            Route::get('upgrade/plans', [TenantUpgradeController::class, 'showPlans'])
                ->name('tenant.subdomain.upgrade.plans');
            Route::get('upgrade/checkout/{plan}', [TenantUpgradeController::class, 'startCheckout'])
                ->name('tenant.subdomain.upgrade.checkout');
            Route::get('upgrade/success', [TenantUpgradeController::class, 'success'])
                ->name('tenant.subdomain.upgrade.success');
            Route::get('upgrade/cancel', [TenantUpgradeController::class, 'cancel'])
                ->name('tenant.subdomain.upgrade.cancel');

            // Conversations & messages
            Route::get('messages', [\App\Http\Controllers\TenantConversationController::class, 'index'])
                ->name('tenant.subdomain.messages.index');

            Route::get('messages/conversations/{conversation}', [\App\Http\Controllers\TenantConversationController::class, 'show'])
                ->name('tenant.subdomain.messages.show');

            Route::post('messages/direct', [\App\Http\Controllers\TenantConversationController::class, 'startDirect'])
                ->name('tenant.subdomain.messages.direct');

            Route::post('messages/group', [\App\Http\Controllers\TenantConversationController::class, 'storeGroup'])
                ->name('tenant.subdomain.messages.group');

            Route::post('messages/conversations/{conversation}', [\App\Http\Controllers\TenantConversationController::class, 'storeMessage'])
                ->name('tenant.subdomain.messages.store');

            Route::get('messages/unread-summary', [\App\Http\Controllers\TenantConversationController::class, 'unreadSummary'])
                ->name('tenant.subdomain.messages.unread');

            // Tenant settings (branding, name, color, logo)
            Route::get('settings', [TenantSettingsController::class, 'edit'])
                ->name('tenant.subdomain.settings.edit');
            Route::post('settings', [TenantSettingsController::class, 'update'])
                ->name('tenant.subdomain.settings.update');

            // Activity log reports
            Route::get('activity', [TenantActivityController::class, 'index'])
                ->name('tenant.subdomain.activity.index');

            Route::get('activity/export/excel', [TenantActivityController::class, 'exportExcel'])
                ->name('tenant.subdomain.activity.export.excel');

            Route::get('activity/export/pdf', [TenantActivityController::class, 'exportPdf'])
                ->name('tenant.subdomain.activity.export.pdf');

            // Tenant complaints to main company
            Route::get('complaints', [TenantComplaintController::class, 'index'])
                ->name('tenant.subdomain.complaints.index');
            Route::post('complaints', [TenantComplaintController::class, 'store'])
                ->name('tenant.subdomain.complaints.store');
            Route::get('complaints/{complaint}', [TenantComplaintController::class, 'show'])
                ->name('tenant.subdomain.complaints.show');
            Route::get('complaints-feed', [TenantComplaintController::class, 'feed'])
                ->name('tenant.subdomain.complaints.feed');

            // Tenant profile & security
            Route::get('profile/password', [TenantProfileController::class, 'editPassword'])
                ->name('tenant.subdomain.password.edit');
            Route::post('profile/password', [TenantProfileController::class, 'updatePassword'])
                ->name('tenant.subdomain.password.update');

            /*
            | Users
            */
            Route::get('users', [TenantUserController::class, 'index'])
                ->name('tenant.subdomain.users.index');

            Route::get('users/export/excel', [TenantUserController::class, 'exportExcel'])
                ->name('tenant.subdomain.users.export.excel');

            Route::get('users/export/pdf', [TenantUserController::class, 'exportPdf'])
                ->name('tenant.subdomain.users.export.pdf');

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

            Route::get('roles/{role}/edit', [TenantRoleController::class, 'edit'])
                ->name('tenant.subdomain.roles.edit');

            Route::match(['put','patch'], 'roles/{role}', [TenantRoleController::class, 'update'])
                ->name('tenant.subdomain.roles.update');

            Route::post('roles/attach', [TenantRoleController::class, 'attachPermission'])
                ->name('tenant.subdomain.roles.attach');

            Route::delete('roles/{role}', [TenantRoleController::class, 'destroy'])
                ->name('tenant.subdomain.roles.destroy');

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

            Route::get('permissions/{permission}/edit', [TenantPermissionController::class, 'edit'])
                ->name('tenant.subdomain.permissions.edit');

            Route::match(['put','patch'], 'permissions/{permission}', [TenantPermissionController::class, 'update'])
                ->name('tenant.subdomain.permissions.update');

            Route::get('permissions/export/excel', [TenantPermissionController::class, 'exportExcel'])
                ->name('tenant.subdomain.permissions.export.excel');

            Route::get('permissions/export/pdf', [TenantPermissionController::class, 'exportPdf'])
                ->name('tenant.subdomain.permissions.export.pdf');

            Route::delete('permissions/{permission}', [TenantPermissionController::class, 'destroy'])
                ->name('tenant.subdomain.permissions.destroy');

            // Attachments page (requires Attachement permission on tenant guard)
            Route::get('attachments', [\App\Http\Controllers\TenantAttachmentController::class, 'index'])
                ->middleware('permission:Attachement,tenant')
                ->name('tenant.subdomain.attachments.index');

            Route::get('attachments/export/excel', [\App\Http\Controllers\TenantAttachmentController::class, 'exportExcel'])
                ->middleware('permission:Attachement,tenant')
                ->name('tenant.subdomain.attachments.export.excel');

            Route::get('attachments/export/pdf', [\App\Http\Controllers\TenantAttachmentController::class, 'exportPdf'])
                ->middleware('permission:Attachement,tenant')
                ->name('tenant.subdomain.attachments.export.pdf');

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
