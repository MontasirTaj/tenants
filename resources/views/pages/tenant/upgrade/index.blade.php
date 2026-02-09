@extends('layout.master')

@section('content')
    @php
        $sub = request()->route('subdomain');
    @endphp

    <div class="row tenant-page-header">
        <div class="col-xl-8 mx-auto">
            <div class="card tenant-page-header-card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h2 class="tenant-page-header-title">ترقية الاشتراك</h2>
                        <p class="tenant-page-header-subtitle mb-0">
                            نسختك الحالية مجانية، يمكنك الترقية إلى خطة مدفوعة للاستفادة من مزايا إضافية.
                        </p>
                    </div>
                    <div class="tenant-page-header-actions mt-3 mt-md-0">
                        <a href="{{ route('tenant.subdomain.dashboard', ['subdomain' => $sub]) }}"
                            class="btn btn-outline-primary">
                            <i class="mdi mdi-view-dashboard-outline"></i>
                            <span>لوحة التحكم</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="row">
                @foreach ($plans as $code => $plan)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h4 class="mb-1">{{ $plan['name'] ?? $code }}</h4>
                                <p class="text-muted mb-2">خطة {{ $plan['name'] ?? $code }} للمستأجرين.</p>
                                <div class="mb-3">
                                    <span class="h3 mb-0">{{ number_format(($plan['amount'] ?? 0) / 100, 2) }}</span>
                                    <span class="text-muted">{{ strtoupper(config('app.currency', 'sar')) }}</span>
                                </div>
                                <p class="small text-muted mb-3">
                                    السعر شهري لمثال توضيحي، حدّث الأسعار حسب إعداداتك في Stripe.
                                </p>
                                <div class="mt-auto">
                                    <a href="{{ route('tenant.subdomain.upgrade.checkout', ['subdomain' => $sub, 'plan' => $code]) }}"
                                        class="btn btn-primary tenant-action-btn w-100">
                                        <i class="mdi mdi-arrow-up-bold-circle-outline"></i>
                                        <span>ترقية إلى هذه الخطة</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
