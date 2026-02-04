<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'TenantName' => ['required','string','max:255'],
            'OwnerName' => ['nullable','string','max:255'],
            'PhoneNumber' => ['nullable','string','max:20','regex:/^\+?[0-9\-\s]{6,20}$/'],
            // Subdomain will be auto-assigned to app_{TenantID} after create; allow optional input
            'Subdomain' => ['nullable','string','max:50','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/','unique:tenants,Subdomain'],
            'Email' => ['nullable','email','max:255','unique:tenants,Email'],
            'Address' => ['nullable','string','max:500'],
            'Plan' => ['required','in:free,pro,business'],
        ];
    }

    public function messages(): array
    {
        return [
            'TenantName.required' => __('اسم المنشأة مطلوب'),
            'TenantName.string' => __('اسم المنشأة يجب أن يكون نصاً'),
            'TenantName.max' => __('اسم المنشأة لا يزيد عن 255 حرفاً'),

            'OwnerName.string' => __('اسم المالك يجب أن يكون نصاً'),
            'OwnerName.max' => __('اسم المالك لا يزيد عن 255 حرفاً'),

            'PhoneNumber.regex' => __('رقم الجوال غير صالح'),
            'PhoneNumber.max' => __('رقم الجوال لا يزيد عن 20 حرفاً'),

            'Subdomain.regex' => __('النطاق الفرعي بصيغة سليمة (أحرف إنجليزية وأرقام وشرطات وسطية فقط)'),
            'Subdomain.unique' => __('هذا النطاق الفرعي مستخدم مسبقاً'),
            'Subdomain.max' => __('النطاق الفرعي لا يزيد عن 50 حرفاً'),
            // Informational: subdomain is auto-generated
            'Subdomain.nullable' => __('سيتم تعيين النطاق الفرعي تلقائياً بصيغة app_{id}'),

            'Email.email' => __('بريد إلكتروني غير صالح'),
            'Email.unique' => __('البريد الإلكتروني مستخدم مسبقاً'),
            'Email.max' => __('البريد الإلكتروني لا يزيد عن 255 حرفاً'),

            'Address.max' => __('العنوان لا يزيد عن 500 حرفاً'),

            'Plan.required' => __('اختيار الباقة مطلوب'),
            'Plan.in' => __('الباقة غير صحيحة'),
        ];
    }
}
