<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Models\User;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TenantSignupController;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\TenantDashboardController;

// Route::get('/', function (Request $request) {
//     return [
//         'host'     => $request->getHost(),
//         'database' => DB::connection()->getDatabaseName(),
//     ];
// });

// Route::get('/check', function () {
//     return [
//         'connection' => DB::getDefaultConnection(),
//         'database'   => DB::connection()->getDatabaseName(),
//         'data'       => DB::table('test_table')->get(),
//     ];
// });

Route::get('/stripe-test', function () {
    $user = User::first();
    $user->createAsStripeCustomer();

    return 'Stripe customer created: ' . $user->stripe_id;
});

// Stripe Cashier webhook (not localized)
Route::post('/stripe/webhook', [CashierWebhookController::class, 'handleWebhook'])->name('cashier.webhook');


// Redirect root to default localized homepage (جميع النطاقات)
Route::get('/', function () {
    return redirect(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getDefaultLocale(), '/'));
});

// Normalize duplicated locale prefix like /ar/ar/* -> /ar/* on any host
Route::get('ar/ar/{any}', function ($any) {
    return redirect('/ar/' . $any);
})->where('any', '.*')->name('locale.normalize');

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function() {
    // الصفحة الرئيسية: صفحة الهبوط
    Route::get('/', function () {
        return view('landing');
    })->name('landing');

    // مسار إضافي اختياري لصفحة الهبوط
    Route::get('/landing', function () {
        return view('landing');
    });

    // Keep dashboard (CPanel) accessible for later stages
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ملاحظة: روابط الاشتراك ستكون محصورة بالدومين الرئيسي عبر مجموعة منفصلة أدناه

// Route::get('/','DashboardController@index');

Route::group(['prefix' => 'basic-ui'], function(){
    Route::get('accordions', function () { return view('pages.basic-ui.accordions'); });
    Route::get('buttons', function () { return view('pages.basic-ui.buttons'); });
    Route::get('badges', function () { return view('pages.basic-ui.badges'); });
    Route::get('breadcrumbs', function () { return view('pages.basic-ui.breadcrumbs'); });
    Route::get('dropdowns', function () { return view('pages.basic-ui.dropdowns'); });
    Route::get('modals', function () { return view('pages.basic-ui.modals'); });
    Route::get('progress-bar', function () { return view('pages.basic-ui.progress-bar'); });
    Route::get('pagination', function () { return view('pages.basic-ui.pagination'); });
    Route::get('tabs', function () { return view('pages.basic-ui.tabs'); });
    Route::get('typography', function () { return view('pages.basic-ui.typography'); });
    Route::get('tooltips', function () { return view('pages.basic-ui.tooltips'); });
});

// روابط الاشتراك على الدومين الرئيسي فقط مع المسارات المترجمة
Route::domain(parse_url(config('app.url'), PHP_URL_HOST))->group(function () {
    Route::group([
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ], function() {
        // بدء الاشتراك (مترجم + على الدومين الرئيسي)
        Route::get('subscribe/{package}', [SubscriptionController::class, 'subscribe'])->name('subscribe.start');

        // صفحات الباقات وتسجيل المنشأة (مترجم + على الدومين الرئيسي)
        Route::get('plans', [TenantSignupController::class, 'plans'])->name('tenants.plans');
        Route::get('signup/{plan}', [TenantSignupController::class, 'showSignup'])->name('tenants.signup');
        Route::post('signup', [TenantSignupController::class, 'store'])->name('tenants.store');
    });
    // روابط النجاح/الإلغاء خارج الترجمة لمنع تكرار البادئة اللغوية
    Route::get('subscribe/success', [SubscriptionController::class, 'success'])->name('subscribe.success');
    Route::get('subscribe/cancel', [SubscriptionController::class, 'cancel'])->name('subscribe.cancel');

    // نجاح/إلغاء دفع الاشتراك الخاص بالمنشآت
    Route::get('signup/success/{tenant}', [TenantSignupController::class, 'success'])->name('tenants.success');
    Route::get('signup/cancel/{tenant}', [TenantSignupController::class, 'cancel'])->name('tenants.cancel');
});

// مسارات لوحة تحكم المنشآت: عبر مسار يحمل اسم النطاق الفرعي (path-based)
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function() {
    Route::group(['prefix' => 'tenant/{subdomain}'], function() {
        // Tenant DB connection required (include login so header can check auth safely)
        Route::group(['middleware' => [\App\Http\Middleware\SetTenantConnection::class]], function() {
            // GET login (localized)
            Route::get('login', [TenantAuthController::class, 'showLoginForm'])->name('tenant.login');
            // صفحة تجريبية لفحص اتصال قاعدة بيانات المستأجر (بدون مصادقة)
            Route::get('db-test', function(\Illuminate\Http\Request $request) {
                $conn = DB::connection('tenant');
                $info = [
                    'host' => config('database.connections.tenant.host'),
                    'port' => config('database.connections.tenant.port'),
                    'database' => $conn->getDatabaseName(),
                    'username' => config('database.connections.tenant.username'),
                    'default_connection' => DB::getDefaultConnection(),
                    'subdomain' => $request->route('subdomain'),
                ];
                // تجربة استعلام بسيط
                try {
                    $usersCount = $conn->table('users')->count();
                    $info['users_count'] = $usersCount;
                } catch (\Throwable $e) {
                    $info['users_count_error'] = $e->getMessage();
                }
                return response()->json($info);
            })->name('tenant.dbtest');
            // JSON users preview (no auth, limited fields)
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
            })->name('tenant.users.test');
            Route::post('login', [TenantAuthController::class, 'login'])->name('tenant.login.post');
            Route::post('logout', [TenantAuthController::class, 'logout'])->name('tenant.logout');

            Route::middleware('auth:tenant')->group(function() {
                Route::get('dashboard', [TenantDashboardController::class, 'index'])->name('tenant.dashboard');

                // Roles & Permissions & Users management
                Route::get('roles', [\App\Http\Controllers\TenantRoleController::class, 'index'])->name('tenant.roles.index');
                Route::post('roles', [\App\Http\Controllers\TenantRoleController::class, 'store'])->name('tenant.roles.store');
                Route::post('roles/attach', [\App\Http\Controllers\TenantRoleController::class, 'attachPermission'])->name('tenant.roles.attach');

                Route::get('permissions', [\App\Http\Controllers\TenantPermissionController::class, 'index'])->name('tenant.permissions.index');
                Route::post('permissions', [\App\Http\Controllers\TenantPermissionController::class, 'store'])->name('tenant.permissions.store');

                Route::get('users', [\App\Http\Controllers\TenantUserController::class, 'index'])->name('tenant.users.index');
                Route::post('users', [\App\Http\Controllers\TenantUserController::class, 'store'])->name('tenant.users.store');
                Route::get('users/{user}/edit', [\App\Http\Controllers\TenantUserController::class, 'edit'])->name('tenant.users.edit');
                Route::match(['put','patch'], 'users/{user}', [\App\Http\Controllers\TenantUserController::class, 'update'])->name('tenant.users.update');
                Route::delete('users/{user}', [\App\Http\Controllers\TenantUserController::class, 'destroy'])->name('tenant.users.destroy');
            });
        });
    });
});

// مسار تجريبي لفحص اتصال قاعدة بيانات المستأجر بدون بادئة اللغة (path-based)
Route::get('tenant/{subdomain}/db-test-raw', function(\Illuminate\Http\Request $request) {
    $conn = DB::connection('tenant');
    $info = [
        'host' => config('database.connections.tenant.host'),
        'port' => config('database.connections.tenant.port'),
        'database' => $conn->getDatabaseName(),
        'username' => config('database.connections.tenant.username'),
        'default_connection' => DB::getDefaultConnection(),
        'subdomain' => $request->route('subdomain'),
    ];
    try {
        $info['users_count'] = $conn->table('users')->count();
    } catch (\Throwable $e) {
        $info['users_count_error'] = $e->getMessage();
    }
    return response()->json($info);
})->middleware([\App\Http\Middleware\SetTenantConnection::class])->name('tenant.dbtest.raw');

// JSON users preview (raw path-based, no locale)
Route::get('tenant/{subdomain}/users-test-raw', function(\Illuminate\Http\Request $request) {
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
})->middleware([\App\Http\Middleware\SetTenantConnection::class])->name('tenant.users.test.raw');

// تشخيص سريع بدون وسيط: يعرض كشف النطاق الفرعي وسجل المستأجر من قاعدة التطبيق
Route::get('tenant/{subdomain}/diag', function(\Illuminate\Http\Request $request) {
    $host = $request->getHost();
    $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
    $subParam = $request->route('subdomain');
    $subFromHost = ($host !== $baseHost && str_ends_with($host, $baseHost))
        ? str_replace('.'.$baseHost, '', $host)
        : null;
    $subdomain = $subParam ?: $subFromHost;
    $tenant = DB::connection('mysql')->table('tenants')->where('Subdomain', $subdomain)->first();
    return response()->json([
        'host' => $host,
        'base_host' => $baseHost,
        'sub_param' => $subParam,
        'sub_from_host' => $subFromHost,
        'resolved_subdomain' => $subdomain,
        'tenant_found' => (bool) $tenant,
        'tenant_is_active' => $tenant ? (bool) $tenant->IsActive : null,
        'tenant_dbname' => $tenant->DBName ?? null,
    ]);
})->name('tenant.diag');
// مسارات لوحة تحكم المنشآت عبر النطاق الفرعي مباشرة (subdomain)
Route::domain('{subdomain}.' . parse_url(config('app.url'), PHP_URL_HOST))->group(function () {
    // تطبيع الروابط إذا تكررت بادئة اللغة ar/ar على النطاق الفرعي
    Route::get('ar/ar/{any}', function ($any) {
        return redirect('/ar/' . $any);
    })->where('any', '.*')->name('tenant.subdomain.locale.normalize');

    // روابط مباشرة تحت /ar مع وسيط قاعدة بيانات المستأجر فقط لضمان عملها
    Route::get('ar/login', [TenantAuthController::class, 'showLoginForm'])
        ->middleware([\App\Http\Middleware\SetTenantConnection::class])
        ->name('tenant.subdomain.login.direct');

    Route::get('ar/users-test', function(\Illuminate\Http\Request $request) {
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
    })->middleware([\App\Http\Middleware\SetTenantConnection::class])
      ->name('tenant.subdomain.users.test.direct');

    // رابط اختبار مباشر تحت /ar بدون مجموعة الترجمة (لتجنب أي تعارض)
    Route::get('ar/users-test', function(\Illuminate\Http\Request $request) {
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
    })->middleware([\App\Http\Middleware\SetTenantConnection::class])->name('tenant.subdomain.users.test.direct');
    

    // مجموعة ثابتة تحت /ar مع وسيط قاعدة بيانات المستأجر فقط
    Route::group([
        'prefix' => 'ar',
        'middleware' => [\App\Http\Middleware\SetTenantConnection::class]
    ], function() {
        // GET login (localized, tied to tenant DB like dashboard)
        Route::get('login', [TenantAuthController::class, 'showLoginForm'])->name('tenant.subdomain.login');
        // صفحة تجريبية لفحص اتصال قاعدة بيانات المستأجر (بدون مصادقة)
        Route::get('db-test', function(\Illuminate\Http\Request $request) {
            $conn = DB::connection('tenant');
            $info = [
                'host' => config('database.connections.tenant.host'),
                'port' => config('database.connections.tenant.port'),
                'database' => $conn->getDatabaseName(),
                'username' => config('database.connections.tenant.username'),
                'default_connection' => DB::getDefaultConnection(),
                'subdomain' => $request->route('subdomain'),
            ];
            try {
                $usersCount = $conn->table('users')->count();
                $info['users_count'] = $usersCount;
            } catch (\Throwable $e) {
                $info['users_count_error'] = $e->getMessage();
            }
            return response()->json($info);
        })->name('tenant.subdomain.dbtest');
        // JSON users preview (no auth, limited fields)
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
        Route::post('login', [TenantAuthController::class, 'login'])->name('tenant.subdomain.login.post');
        Route::post('logout', [TenantAuthController::class, 'logout'])->name('tenant.subdomain.logout');

        Route::middleware('auth:tenant')->group(function() {
            Route::get('dashboard', [TenantDashboardController::class, 'index'])->name('tenant.subdomain.dashboard');

            Route::get('roles', [\App\Http\Controllers\TenantRoleController::class, 'index'])->name('tenant.subdomain.roles.index');
            Route::post('roles', [\App\Http\Controllers\TenantRoleController::class, 'store'])->name('tenant.subdomain.roles.store');
            Route::post('roles/attach', [\App\Http\Controllers\TenantRoleController::class, 'attachPermission'])->name('tenant.subdomain.roles.attach');

            Route::get('permissions', [\App\Http\Controllers\TenantPermissionController::class, 'index'])->name('tenant.subdomain.permissions.index');
            Route::post('permissions', [\App\Http\Controllers\TenantPermissionController::class, 'store'])->name('tenant.subdomain.permissions.store');

            Route::get('users', [\App\Http\Controllers\TenantUserController::class, 'index'])->name('tenant.subdomain.users.index');
            Route::post('users', [\App\Http\Controllers\TenantUserController::class, 'store'])->name('tenant.subdomain.users.store');
            Route::get('users/{user}/edit', [\App\Http\Controllers\TenantUserController::class, 'edit'])->name('tenant.subdomain.users.edit');
            Route::match(['put','patch'], 'users/{user}', [\App\Http\Controllers\TenantUserController::class, 'update'])->name('tenant.subdomain.users.update');
            Route::delete('users/{user}', [\App\Http\Controllers\TenantUserController::class, 'destroy'])->name('tenant.subdomain.users.destroy');
        });
    });

    // مسار تجريبي لفحص اتصال قاعدة بيانات المستأجر بدون بادئة اللغة (subdomain)
    Route::get('db-test-raw', function(\Illuminate\Http\Request $request) {
        $conn = DB::connection('tenant');
        $info = [
            'host' => config('database.connections.tenant.host'),
            'port' => config('database.connections.tenant.port'),
            'database' => $conn->getDatabaseName(),
            'username' => config('database.connections.tenant.username'),
            'default_connection' => DB::getDefaultConnection(),
            'subdomain' => $request->route('subdomain'),
        ];
        try {
            $info['users_count'] = $conn->table('users')->count();
        } catch (\Throwable $e) {
            $info['users_count_error'] = $e->getMessage();
        }
        return response()->json($info);
    })->middleware([\App\Http\Middleware\SetTenantConnection::class])->name('tenant.subdomain.dbtest.raw');

    // JSON users preview (raw subdomain, no locale)
    Route::get('users-test-raw', function(\Illuminate\Http\Request $request) {
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
    })->middleware([\App\Http\Middleware\SetTenantConnection::class])->name('tenant.subdomain.users.test.raw');

    // Fallback debug for unmatched /ar/* on subdomain to surface actual path
    Route::any('ar/{any}', function(\Illuminate\Http\Request $request, $any) {
        $host = $request->getHost();
        $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
        return response()->json([
            'message' => 'No ar/* route matched on subdomain',
            'request_path' => $request->path(),
            'any' => $any,
            'host' => $host,
            'base_host' => $baseHost,
            'subdomain' => $request->route('subdomain'),
        ], 404);
    })->where('any','.*')->name('tenant.subdomain.fallback');

    // تشخيص سريع بدون وسيط: يعرض كشف النطاق الفرعي وسجل المستأجر من قاعدة التطبيق
    Route::get('diag', function(\Illuminate\Http\Request $request) {
        $host = $request->getHost();
        $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
        $subdomain = $request->route('subdomain');
        $tenant = DB::connection('mysql')->table('tenants')->where('Subdomain', $subdomain)->first();
        return response()->json([
            'host' => $host,
            'base_host' => $baseHost,
            'resolved_subdomain' => $subdomain,
            'tenant_found' => (bool) $tenant,
            'tenant_is_active' => $tenant ? (bool) $tenant->IsActive : null,
            'tenant_dbname' => $tenant->DBName ?? null,
        ]);
    })->name('tenant.subdomain.diag');
});

// (أزيل تقييد الدومين: صفحة الهبوط تعمل الآن على جميع النطاقات)

Route::group(['prefix' => 'advanced-ui'], function(){
    Route::get('dragula', function () { return view('pages.advanced-ui.dragula'); });
    Route::get('clipboard', function () { return view('pages.advanced-ui.clipboard'); });
    Route::get('context-menu', function () { return view('pages.advanced-ui.context-menu'); });
    Route::get('popups', function () { return view('pages.advanced-ui.popups'); });
    Route::get('sliders', function () { return view('pages.advanced-ui.sliders'); });
    Route::get('carousel', function () { return view('pages.advanced-ui.carousel'); });
    Route::get('loaders', function () { return view('pages.advanced-ui.loaders'); });
    Route::get('tree-view', function () { return view('pages.advanced-ui.tree-view'); });
});

Route::group(['prefix' => 'forms'], function(){
    Route::get('basic-elements', function () { return view('pages.forms.basic-elements'); });
    Route::get('advanced-elements', function () { return view('pages.forms.advanced-elements'); });
    Route::get('dropify', function () { return view('pages.forms.dropify'); });
    Route::get('form-validation', function () { return view('pages.forms.form-validation'); });
    Route::get('step-wizard', function () { return view('pages.forms.step-wizard'); });
    Route::get('wizard', function () { return view('pages.forms.wizard'); });
});

Route::group(['prefix' => 'editors'], function(){
    Route::get('text-editor', function () { return view('pages.editors.text-editor'); });
    Route::get('code-editor', function () { return view('pages.editors.code-editor'); });
});

Route::group(['prefix' => 'charts'], function(){
    Route::get('chartjs', function () { return view('pages.charts.chartjs'); });
    Route::get('morris', function () { return view('pages.charts.morris'); });
    Route::get('flot', function () { return view('pages.charts.flot'); });
    Route::get('google-charts', function () { return view('pages.charts.google-charts'); });
    Route::get('sparklinejs', function () { return view('pages.charts.sparklinejs'); });
    Route::get('c3-charts', function () { return view('pages.charts.c3-charts'); });
    Route::get('chartist', function () { return view('pages.charts.chartist'); });
    Route::get('justgage', function () { return view('pages.charts.justgage'); });
});

Route::group(['prefix' => 'tables'], function(){
    Route::get('basic-table', function () { return view('pages.tables.basic-table'); });
    Route::get('data-table', function () { return view('pages.tables.data-table'); });
    Route::get('js-grid', function () { return view('pages.tables.js-grid'); });
    Route::get('sortable-table', function () { return view('pages.tables.sortable-table'); });
});

Route::get('notifications', function () {
    return view('pages.notifications.index');
});

Route::group(['prefix' => 'icons'], function(){
    Route::get('material', function () { return view('pages.icons.material'); });
    Route::get('flag-icons', function () { return view('pages.icons.flag-icons'); });
    Route::get('font-awesome', function () { return view('pages.icons.font-awesome'); });
    Route::get('simple-line-icons', function () { return view('pages.icons.simple-line-icons'); });
    Route::get('themify', function () { return view('pages.icons.themify'); });
});

Route::group(['prefix' => 'maps'], function(){
    Route::get('vector-map', function () { return view('pages.maps.vector-map'); });
    Route::get('mapael', function () { return view('pages.maps.mapael'); });
    Route::get('google-maps', function () { return view('pages.maps.google-maps'); });
});

// Removed demo user-pages routes to prevent confusion; tenants should use their own login

Route::group(['prefix' => 'error-pages'], function(){
    Route::get('error-404', function () { return view('pages.error-pages.error-404'); });
    Route::get('error-500', function () { return view('pages.error-pages.error-500'); });
});

Route::group(['prefix' => 'general-pages'], function(){
    Route::get('blank-page', function () { return view('pages.general-pages.blank-page'); });
    Route::get('landing-page', function () { return view('pages.general-pages.landing-page'); });
    Route::get('profile', function () { return view('pages.general-pages.profile'); });
    Route::get('email-templates', function () { return view('pages.general-pages.email-templates'); });
    Route::get('faq', function () { return view('pages.general-pages.faq'); });
    Route::get('faq-2', function () { return view('pages.general-pages.faq-2'); });
    Route::get('news-grid', function () { return view('pages.general-pages.news-grid'); });
    Route::get('timeline', function () { return view('pages.general-pages.timeline'); });
    Route::get('search-results', function () { return view('pages.general-pages.search-results'); });
    Route::get('portfolio', function () { return view('pages.general-pages.portfolio'); });
    Route::get('user-listing', function () { return view('pages.general-pages.user-listing'); });
});

Route::group(['prefix' => 'ecommerce'], function(){
    Route::get('invoice', function () { return view('pages.ecommerce.invoice'); });
    Route::get('invoice-2', function () { return view('pages.ecommerce.invoice-2'); });
    Route::get('pricing', function () { return view('pages.ecommerce.pricing'); });
    Route::get('product-catalogue', function () { return view('pages.ecommerce.product-catalogue'); });
    Route::get('project-list', function () { return view('pages.ecommerce.project-list'); });
    Route::get('orders', function () { return view('pages.ecommerce.orders'); });
});

});

// For Clear cache
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

// Temporarily disable custom catch-all 404 to aid debugging
// Route::any('/{page?}',function(){
//     return View::make('pages.error-pages.error-404');
// })->where('page','.*');
