<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Mail\TenantWelcomeMail;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Stripe\StripeClient;
use App\Models\Payment;

class TenantSignupController extends Controller
{
    private array $plans = [
        'free' => ['name' => 'مجانية', 'amount' => 0],
        'pro' => ['name' => 'احترافية', 'amount' => 7900], // 79.00 SAR (example)
        'business' => ['name' => 'أعمال', 'amount' => 14900], // 149.00 SAR (example)
    ];

    public function plans()
    {
        return view('pages.tenants.plans', ['plans' => $this->plans]);
    }

    public function showSignup(Request $request, string $plan)
    {
        if (!array_key_exists($plan, $this->plans)) {
            abort(404);
        }
        return view('pages.tenants.signup', [
            'plan' => $plan,
            'planData' => $this->plans[$plan],
        ]);
    }

    public function store(StoreTenantRequest $request)
    {
        $validated = $request->validated();
        $now = Carbon::now();

        // منع تكرار الاشتراك المجاني لنفس البريد
        if (($validated['Plan'] ?? null) === 'free') {
            $email = $validated['Email'] ?? null;
            if ($email) {
                $alreadyFree = Tenant::where('Email', $email)
                    ->where('Plan', 'free')
                    ->exists();

                if ($alreadyFree) {
                    return back()
                        ->withErrors(['Email' => __('هذا البريد استخدم مسبقاً في اشتراك مجاني، لا يمكن تكرار الاشتراك المجاني لنفس البريد.')])
                        ->withInput();
                }
            }
        }

        $tenant = new Tenant();
        $tenant->TenantName = $validated['TenantName'];
        $tenant->OwnerName = $validated['OwnerName'] ?? null;
        $tenant->PhoneNumber = $validated['PhoneNumber'] ?? null;
        // Use a temporary unique subdomain until we know TenantID
        $tenant->Subdomain = 'app-pending-' . $now->format('YmdHis') . '-' . Str::lower(Str::random(5));
        $tenant->Email = $validated['Email'] ?? null;
        $tenant->Address = $validated['Address'] ?? null;
        $tenant->Plan = $validated['Plan'];
        $tenant->JoinDate = $now->toDateString();
        $tenant->IsActive = false;
        $tenant->Status = 0; // pending

        // Assign temporary unique DB fields to satisfy NOT NULL + UNIQUE before first insert
        $tenant->DBName = 'app_pending_' . $now->format('YmdHis') . '_' . Str::lower(Str::random(6));
        $tenant->DBUser = 'tenant_user_pending_' . Str::lower(Str::random(6));
        $tenant->DBPassword = Str::random(16);
        $tenant->DBHost = config('database.connections.mysql.host');
        $tenant->DBPort = (string) config('database.connections.mysql.port');

        // Save first to obtain TenantID
        $tenant->save();

        // Now set definitive names based on actual TenantID
        $seq = $tenant->TenantID;
        $tenant->DBName = sprintf('app_%d', $seq);
        $tenant->DBUser = sprintf('tenant_user_%d', $seq);
        // Keep previously generated password/host/port
        $tenant->Subdomain = sprintf('app_%d', $seq);
        $tenant->save();

        // معالجة خطة مجانية دون Stripe
        $amount = $this->plans[$tenant->Plan]['amount'];
        if ($amount <= 0) {
            // تفعيل مباشر بدون دفع
            $tenantModel = $tenant;
            $tenantModel->IsActive = true;
            $tenantModel->Status = 1;

            // بداية الاشتراك الآن، ونهايته بعد أسبوع ناقص يوم واحد
            $startDate = Carbon::now()->toDateString();
            $trialEnd = Carbon::parse($startDate)->addWeek()->subDay()->toDateString();
            $endDate = $trialEnd;

            $tenantModel->SubscriptionStartDate = $startDate;
            $tenantModel->SubscriptionEndDate = $endDate;
            // في حالة الاشتراك المجاني نملأ أيضاً تاريخ نهاية التجربة
            $tenantModel->TrialEndDate = $trialEnd;
            $tenantModel->save();

            // سجل عملية مجانية
            try {
                Payment::create([
                    'tenant_id' => $tenantModel->TenantID,
                    'user_id' => null,
                    'plan' => $tenantModel->Plan,
                    'currency' => config('app.currency', 'sar'),
                    'amount_total' => 0,
                    'status' => 'free',
                    'type' => 'tenant_signup_free',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'stripe_session_id' => null,
                    'stripe_payment_intent_id' => null,
                    'stripe_customer_id' => null,
                    'stripe_charge_id' => null,
                    'receipt_url' => null,
                    'customer_details' => null,
                    'metadata' => ['note' => 'Free plan activation'],
                ]);
            } catch (\Throwable $e) {
                \Log::error('Free plan record failed', ['error' => $e->getMessage()]);
            }

            // Prepare per-tenant admin credentials for seeder
            $ownerEmail = $tenantModel->Email;
            $ownerName = $tenantModel->OwnerName ?: $tenantModel->TenantName;

            if ($ownerEmail) {
                Config::set('tenant.provision.admin_email', $ownerEmail);
                Config::set('tenant.provision.admin_name', $ownerName);
            }

            // Provision tenant DB and seed admin
            Artisan::call('tenants:provision', ['--tenant' => $tenantModel->TenantID, '--force' => true]);

            return redirect()->route('tenant.subdomain.login', ['subdomain' => $tenantModel->Subdomain]);
        }

        // Paid plans: create checkout session via Stripe
        $stripe = new StripeClient([
            'api_key' => config('services.stripe.secret'),
        ]);

        $currency = config('app.currency', 'sar');

        $params = [
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => 'اشتراك ' . $this->plans[$tenant->Plan]['name'],
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'tenant_id' => (string) $tenant->TenantID,
                'plan' => $tenant->Plan,
            ],
            'success_url' => route('tenants.success', ['tenant' => $tenant->TenantID]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('tenants.cancel', ['tenant' => $tenant->TenantID]),
        ];

        if (!empty($tenant->Email)) {
            $params['customer_email'] = $tenant->Email;
        } else {
            $params['customer_creation'] = 'always';
        }

        try {
            $session = $stripe->checkout->sessions->create($params);
        } catch (\Throwable $e) {
            $id = $tenant->TenantID;
            $tenant->delete();
            return redirect()->route('tenants.cancel', ['tenant' => $id])->with('error', __('تعذر إنشاء جلسة الدفع: ') . $e->getMessage());
        }

        return redirect($session->url);
    }

    public function success(Request $request, int $tenant)
    {
        $sessionId = $request->query('session_id');
        $tenantModel = Tenant::findOrFail($tenant);

        // يشترط وجود session_id وتأكيد الدفع

        if (!$sessionId) {
            return redirect()->route('tenants.cancel', ['tenant' => $tenant])->with('error', __('لم يتم تأكيد الدفع'));
        }

        $stripe = new StripeClient(['api_key' => config('services.stripe.secret')]);
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        if ($session && $session->payment_status === 'paid') {
            $tenantModel->IsActive = true;
            $tenantModel->Status = 1;

            // بداية الاشتراك الآن، ونهايته بعد سنة ناقص يوم واحد
            $startDate = Carbon::now()->toDateString();
            $endDate = Carbon::parse($startDate)->addYear()->subDay()->toDateString();

            $tenantModel->SubscriptionStartDate = $startDate;
            $tenantModel->SubscriptionEndDate = $endDate;
            $tenantModel->save();

            // سجل عملية الدفع في قاعدة البيانات الرئيسية
            try {
                $paymentIntentId = $session->payment_intent ?? null;
                $chargeId = null;
                $receiptUrl = null;

                if ($paymentIntentId) {
                    $pi = $stripe->paymentIntents->retrieve($paymentIntentId, []);
                    // Attempt to get latest_charge and receipt_url
                    if (isset($pi->charges) && isset($pi->charges->data[0])) {
                        $charge = $pi->charges->data[0];
                        $chargeId = $charge->id ?? null;
                        $receiptUrl = $charge->receipt_url ?? null;
                    }
                }

                Payment::create([
                    'tenant_id' => $tenantModel->TenantID,
                    'user_id' => null,
                    'plan' => $tenantModel->Plan,
                    'currency' => $session->currency ?? config('app.currency', 'sar'),
                    'amount_total' => $session->amount_total ?? null,
                    'status' => $session->payment_status ?? null,
                    'type' => 'tenant_signup',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'stripe_session_id' => $session->id ?? null,
                    'stripe_payment_intent_id' => $paymentIntentId,
                    'stripe_customer_id' => $session->customer ?? null,
                    'stripe_charge_id' => $chargeId,
                    'receipt_url' => $receiptUrl,
                    'customer_details' => isset($session->customer_details) ? (array) $session->customer_details : null,
                    'metadata' => isset($session->metadata) ? (array) $session->metadata : null,
                ]);
            } catch (\Throwable $e) {
                // لا تمنع الفشل في التسجيل من استمرار التفعيل، فقط سجّل الخطأ
                \Log::error('Payment record creation failed', ['error' => $e->getMessage(), 'session_id' => $sessionId]);
            }

            // Prepare per-tenant admin credentials for seeder
            $ownerEmail = $tenantModel->Email;
            $ownerName = $tenantModel->OwnerName ?: $tenantModel->TenantName;

            if ($ownerEmail) {
                Config::set('tenant.provision.admin_email', $ownerEmail);
                Config::set('tenant.provision.admin_name', $ownerName);
            }

            // Provision tenant DB and seed admin after payment success
            Artisan::call('tenants:provision', ['--tenant' => $tenantModel->TenantID, '--force' => true]);

            // Redirect directly to tenant subdomain login
            return redirect()->route('tenant.subdomain.login', ['subdomain' => $tenantModel->Subdomain]);
        }

        return redirect()->route('tenants.cancel', ['tenant' => $tenant])->with('error', __('فشل الدفع'));
    }

    public function cancel(int $tenant)
    {
        $tenantModel = Tenant::find($tenant);
        if ($tenantModel) {
            $tenantModel->delete();
        }
        return view('pages.tenants.cancel');
    }

    private function provisionTenantDatabase(\App\Models\Tenant $tenant): void
    {
        // Create database if not exists (requires privileges)
        try {
            $dbName = $tenant->DBName;
            DB::statement('CREATE DATABASE IF NOT EXISTS `'.$dbName.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        } catch (\Throwable $e) {
            // ignore failures silently; admin can create manually
        }

        // Configure tenant connection
        $connection = [
            'driver' => 'mysql',
            'host' => $tenant->DBHost ?: config('database.connections.mysql.host'),
            'port' => $tenant->DBPort ?: config('database.connections.mysql.port'),
            'database' => $tenant->DBName,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
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
        config(['database.connections.tenant' => $connection]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Run tenant-specific migrations
        try {
            \Artisan::call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--database' => 'tenant',
                '--force' => true,
            ]);
        } catch (\Throwable $e) {
            // You can log error here if needed
        }
    }
}
