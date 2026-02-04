<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Free plan
        Plan::updateOrCreate(
            ['code' => 'free'],
            [
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
                'price_monthly' => 0,
                'currency' => 'USD',
                'name_en' => 'Free',
                'name_ar' => 'مجانية',
                'subtitle_en' => 'Best for trials and small teams',
                'subtitle_ar' => 'مثالية للتجربة والفرق الصغيرة',
                'features_en' => implode("\n", [
                    'Up to 3 tenants',
                    'Email support',
                    'Basic analytics',
                    'Tenant isolation',
                ]),
                'features_ar' => implode("\n", [
                    'حتى 3 تينانت',
                    'دعم عبر البريد الإلكتروني',
                    'تحليلات أساسية',
                    'عزل كامل لكل مستأجر',
                ]),
                'more_features_en' => implode("\n", [
                    'Community access',
                    'Daily backups',
                ]),
                'more_features_ar' => implode("\n", [
                    'وصول لمجتمع المستخدمين',
                    'نسخ احتياطية يومية',
                ]),
            ]
        );

        // Pro plan (featured)
        Plan::updateOrCreate(
            ['code' => 'pro'],
            [
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'price_monthly' => 29,
                'currency' => 'USD',
                'name_en' => 'Pro',
                'name_ar' => 'احترافية',
                'subtitle_en' => 'Most popular for growing teams',
                'subtitle_ar' => 'الأكثر شعبية للفرق المتنامية',
                'features_en' => implode("\n", [
                    'Up to 15 tenants',
                    'Priority support',
                    'Advanced analytics',
                    'Role-based access control',
                ]),
                'features_ar' => implode("\n", [
                    'حتى 15 تينانت',
                    'دعم بأولوية أعلى',
                    'تحليلات متقدمة',
                    'صلاحيات مبنية على الأدوار',
                ]),
                'more_features_en' => implode("\n", [
                    'RBAC & granular permissions',
                    'API access & webhooks',
                    'Audit logs & activity stream',
                    'Custom reports & exports',
                    'Multi-region hosting',
                    'Priority email & chat',
                ]),
                'more_features_ar' => implode("\n", [
                    'أدوار وصلاحيات تفصيلية',
                    'وصول للـ API و Webhooks',
                    'سجلات تدقيق وتتبّع للنشاط',
                    'تقارير مخصصة وتصدير للبيانات',
                    'استضافة متعددة المناطق',
                    'دعم بأولوية عبر البريد والدردشة',
                ]),
            ]
        );

        // Business plan
        Plan::updateOrCreate(
            ['code' => 'business'],
            [
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'price_monthly' => 149,
                'currency' => 'USD',
                'name_en' => 'Business',
                'name_ar' => 'أعمال',
                'subtitle_en' => 'For enterprises and mission-critical workloads',
                'subtitle_ar' => 'للمنشآت الكبيرة والأنظمة الحرجة',
                'features_en' => implode("\n", [
                    'Unlimited tenants',
                    'Custom integrations',
                    'Dedicated success manager',
                    'SSO & SAML',
                    'Advanced security & SLAs',
                ]),
                'features_ar' => implode("\n", [
                    'عدد غير محدود من التينانت',
                    'تكاملات مخصصة',
                    'مسؤول نجاح مخصص',
                    'دعم SSO و SAML',
                    'أمان متقدم واتفاقيات مستوى خدمة',
                ]),
                'more_features_en' => implode("\n", [
                    'SLAs & security audits',
                    'Custom pricing',
                    'Multi-region hosting',
                    'Audit logs & activity stream',
                ]),
                'more_features_ar' => implode("\n", [
                    'اتفاقيات مستوى خدمة وفحوصات أمان',
                    'تسعير مخصص',
                    'استضافة متعددة المناطق',
                    'سجلات تدقيق وتتبّع للنشاط',
                ]),
            ]
        );
    }
}
