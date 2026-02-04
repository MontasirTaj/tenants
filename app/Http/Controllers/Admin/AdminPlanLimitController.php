<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanLimit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminPlanLimitController extends Controller
{
    protected array $knownPlans = [
        'free' => 'مجانية',
        'pro' => 'احترافية',
        'business' => 'أعمال',
    ];

    public function index()
    {
        $plans = [];
        foreach ($this->knownPlans as $code => $label) {
            $limit = PlanLimit::firstOrCreate(['plan' => $code]);
            $plans[$code] = [
                'label' => $label,
                'limit' => $limit,
            ];
        }

        return view('admin.plans.index', [
            'plans' => $plans,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'plans' => ['array'],
            'plans.*.max_users' => ['nullable', 'integer', 'min:1'],
        ]);

        $plansInput = $data['plans'] ?? [];

        foreach ($plansInput as $code => $values) {
            if (! array_key_exists($code, $this->knownPlans)) {
                continue;
            }

            $limit = PlanLimit::firstOrCreate(['plan' => $code]);
            $maxUsers = $values['max_users'] ?? null;
            $limit->max_users = $maxUsers !== null && $maxUsers !== '' ? (int) $maxUsers : null;
            $limit->save();
        }

        return back()->with('status', __('تم تحديث إعدادات الباقات بنجاح.'));
    }
}
