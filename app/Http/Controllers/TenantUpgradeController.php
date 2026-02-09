<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class TenantUpgradeController extends Controller
{
    /**
     * الخطط المتاحة للترقية من المجانية إلى المدفوعة.
     * نفس الأسعار المستخدمة في تسجيل التينانت الجديد.
     */
    private array $plans = [
        'pro' => ['name' => 'احترافية', 'amount' => 7900],    // 79.00 SAR
        'business' => ['name' => 'أعمال', 'amount' => 14900], // 149.00 SAR
    ];

    protected function findTenantBySubdomain(string $subdomain): ?Tenant
    {
        try {
            return Tenant::on('mysql')->where('Subdomain', $subdomain)->first();
        } catch (\Throwable $e) {
            Log::error('Find tenant for upgrade failed', ['subdomain' => $subdomain, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * صفحة عرض خطط الترقية للمستأجر الحالي (من نسخة مجانية).
     */
    public function showPlans(Request $request, string $subdomain)
    {
        $tenant = $this->findTenantBySubdomain($subdomain);
        if (! $tenant) {
            abort(404);
        }

        // نسمح بالترقية أساساً من الخطة المجانية
        if ($tenant->Plan !== 'free') {
            return redirect()->route('tenant.subdomain.dashboard', ['subdomain' => $subdomain])
                ->with('status', __('تمتلك خطة مدفوعة بالفعل.'));
        }

        $paidPlans = $this->plans;

        return view('pages.tenant.upgrade.index', [
            'tenant' => $tenant,
            'plans' => $paidPlans,
        ]);
    }

    /**
     * بدء جلسة الدفع مع Stripe لخطة معيّنة.
     */
    public function startCheckout(Request $request, string $subdomain, string $plan)
    {
        $tenant = $this->findTenantBySubdomain($subdomain);
        if (! $tenant) {
            abort(404);
        }

        if ($tenant->Plan !== 'free') {
            return redirect()->route('tenant.subdomain.dashboard', ['subdomain' => $subdomain])
                ->with('status', __('تمتلك خطة مدفوعة بالفعل.'));
        }

        $planKey = $plan;
        if (! array_key_exists($planKey, $this->plans)) {
            abort(404);
        }

        $planData = $this->plans[$planKey];

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
                        'name' => 'ترقية الاشتراك إلى ' . ($planData['name'] ?? $planKey),
                    ],
                    'unit_amount' => $planData['amount'],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'tenant_id' => (string) $tenant->TenantID,
                'plan' => $planKey,
                'upgrade' => '1',
            ],
            'success_url' => route('tenant.subdomain.upgrade.success', ['subdomain' => $subdomain]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('tenant.subdomain.upgrade.cancel', ['subdomain' => $subdomain]),
        ];

        if (! empty($tenant->Email)) {
            $params['customer_email'] = $tenant->Email;
        }

        try {
            $session = $stripe->checkout->sessions->create($params);
        } catch (\Throwable $e) {
            Log::error('Tenant upgrade checkout failed', ['tenant_id' => $tenant->TenantID, 'error' => $e->getMessage()]);

            return redirect()->route('tenant.subdomain.dashboard', ['subdomain' => $subdomain])
                ->with('error', __('تعذر إنشاء جلسة الدفع للترقية: ') . $e->getMessage());
        }

        return redirect($session->url);
    }

    /**
     * معالجة نجاح الدفع للترقية.
     */
    public function success(Request $request, string $subdomain)
    {
        $tenant = $this->findTenantBySubdomain($subdomain);
        if (! $tenant) {
            abort(404);
        }

        $sessionId = $request->query('session_id');
        if (! $sessionId) {
            return redirect()->route('tenant.subdomain.dashboard', ['subdomain' => $subdomain])
                ->with('error', __('لم يتم تأكيد الدفع للترقية'));
        }

        $stripe = new StripeClient(['api_key' => config('services.stripe.secret')]);

        try {
            $session = $stripe->checkout->sessions->retrieve($sessionId);
        } catch (\Throwable $e) {
            Log::error('Tenant upgrade session retrieve failed', ['session_id' => $sessionId, 'error' => $e->getMessage()]);

            return redirect()->route('tenant.subdomain.dashboard', ['subdomain' => $subdomain])
                ->with('error', __('تعذر قراءة جلسة الدفع للترقية'));
        }

        if (! $session || $session->payment_status !== 'paid') {
            return redirect()->route('tenant.subdomain.dashboard', ['subdomain' => $subdomain])
                ->with('error', __('فشل الدفع للترقية'));
        }

        $metaPlan = null;
        if (isset($session->metadata) && isset($session->metadata->plan)) {
            $metaPlan = (string) $session->metadata->plan;
        }

        // حدّث خطة التينانت وتواريخ الاشتراك
        $now = Carbon::now();
        $startDate = $now->toDateString();
        $endDate = $now->copy()->addYear()->subDay()->toDateString();

        if ($metaPlan && $metaPlan !== 'free') {
            $tenant->Plan = $metaPlan;
        }
        $tenant->IsActive = true;
        $tenant->Status = 1;
        $tenant->SubscriptionStartDate = $startDate;
        $tenant->SubscriptionEndDate = $endDate;
        // بعد الترقية إلى خطة مدفوعة لم تعد هناك فترة تجريبية
        $tenant->TrialEndDate = null;
        $tenant->save();

        // سجّل عملية الدفع للترقية في جدول المدفوعات
        try {
            $paymentIntentId = $session->payment_intent ?? null;
            $chargeId = null;
            $receiptUrl = null;

            if ($paymentIntentId) {
                $pi = $stripe->paymentIntents->retrieve($paymentIntentId, []);
                if (isset($pi->charges) && isset($pi->charges->data[0])) {
                    $charge = $pi->charges->data[0];
                    $chargeId = $charge->id ?? null;
                    $receiptUrl = $charge->receipt_url ?? null;
                }
            }

            Payment::create([
                'tenant_id' => $tenant->TenantID,
                'user_id' => null,
                'plan' => $tenant->Plan,
                'currency' => $session->currency ?? config('app.currency', 'sar'),
                'amount_total' => $session->amount_total ?? null,
                'status' => $session->payment_status ?? null,
                'type' => 'tenant_upgrade',
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
            Log::error('Tenant upgrade payment record failed', ['session_id' => $sessionId, 'error' => $e->getMessage()]);
        }

        return redirect()->route('tenant.subdomain.dashboard', ['subdomain' => $subdomain])
            ->with('status', __('تمت ترقية الاشتراك بنجاح.'));
    }

    public function cancel(string $subdomain)
    {
        return redirect()->route('tenant.subdomain.dashboard', ['subdomain' => $subdomain])
            ->with('status', __('تم إلغاء عملية الترقية، لم يتم أي خصم.'));
    }
}
