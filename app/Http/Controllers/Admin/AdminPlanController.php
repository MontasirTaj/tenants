<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::orderBy('sort_order')->get();

        return view('admin.plans.manage', compact('plans'));
    }

    public function create(): View
    {
        return view('admin.plans.form', [
            'plan' => new Plan(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        // اجعل الحقول البوليانية صريحة حتى عند عدم تحديد الـ checkbox
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        Plan::create($data);

        return redirect()->route('admin.subscription-plans.index')
            ->with('status', __('تم إنشاء الباقة بنجاح.'));
    }

    public function edit(Plan $plan): View
    {
        return view('admin.plans.form', [
            'plan' => $plan,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $this->validateData($request, $plan->id);

        // إذا أُزيلت علامة الصح من الـ checkbox لن يُرسل الحقل؛ نحدده يدويًا هنا
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $plan->update($data);

        return redirect()->route('admin.subscription-plans.index')
            ->with('status', __('تم تحديث الباقة بنجاح.'));
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        $uniqueRule = 'unique:plans,code';
        if ($id) {
            $uniqueRule .= ',' . $id;
        }

        return $request->validate([
            'code' => ['required', 'string', 'max:50', $uniqueRule],
            'is_active' => ['nullable'],
            'is_featured' => ['nullable'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'subtitle_ar' => ['nullable', 'string', 'max:255'],
            'features_en' => ['nullable', 'string'],
            'features_ar' => ['nullable', 'string'],
            'more_features_en' => ['nullable', 'string'],
            'more_features_ar' => ['nullable', 'string'],
        ], [
            'code.required' => 'رمز الباقة مطلوب',
            'name_ar.required' => 'اسم الباقة بالعربية مطلوب',
            'name_en.required' => 'اسم الباقة بالإنجليزية مطلوب',
        ]);
    }
}
