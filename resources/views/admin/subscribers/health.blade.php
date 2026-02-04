@extends('layout.master')

@push('style')
    <style>
        .tenant-health-card {
            border-radius: 18px;
            border: 1px solid rgba(16, 44, 79, 0.08);
            background: linear-gradient(135deg, #ffffff 0%, #f5f7fb 40%, #e9f0ff 100%);
            box-shadow: 0 14px 30px rgba(16, 44, 79, 0.06);
            transition: transform .18s ease, box-shadow .18s ease;
            width: 100%;
        }

        .tenant-health-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(16, 44, 79, 0.15);
        }

        .tenant-health-card .card-body {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .tenant-health-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        [dir="rtl"] .tenant-health-card-header {
            flex-direction: row-reverse;
        }

        .tenant-health-card-text {
            text-align: start;
            padding-inline-end: .4rem;
        }

        [dir="rtl"] .tenant-health-card-text {
            text-align: end;
        }

        .tenant-health-card-label {
            font-size: .8rem;
            font-weight: 500;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #6b7a90;
            margin-bottom: .15rem;
        }

        .tenant-health-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(16, 44, 79, 0.08);
            color: #102c4f;
            font-size: 1.2rem;
        }

        .tenant-health-card-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #102c4f;
            margin-top: 1rem;
        }

        .tenant-health-card-sub {
            font-size: .8rem;
            color: #8a96ac;
            margin-top: .35rem;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3>{{ __('app.tenant_health_title') }}</h3>
                <p class="text-muted mb-0">{{ $tenant->TenantName }} ({{ $tenant->Subdomain }})</p>
            </div>
            <a href="{{ route('admin.subscribers.index') }}"
                class="btn btn-sm btn-light border d-inline-flex align-items-center">
                <i class="mdi mdi-arrow-left me-1"></i>
                <span>{{ __('app.back') }}</span>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6 mb-3 d-flex">
            <div class="card tenant-health-card h-100">
                <div class="card-body">
                    <div class="tenant-health-card-header mb-1">
                        <div class="tenant-health-card-text">
                            <div class="tenant-health-card-label">{{ __('app.tenant_health_last_login') }}</div>
                            <p class="mb-0 text-muted small">{{ __('app.tenant_health_last_login_desc') }}</p>
                        </div>
                        <div class="tenant-health-card-icon">
                            <i class="mdi mdi-clock-outline"></i>
                        </div>
                    </div>
                    <div class="tenant-health-card-value">
                        @if ($lastLogin)
                            {{ $lastLogin->toDateTimeString() }}
                        @else
                            <span class="text-muted small d-block">{{ __('app.tenant_health_never_logged') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3 d-flex">
            <div class="card tenant-health-card h-100">
                <div class="card-body">
                    <div class="tenant-health-card-header mb-1">
                        <div class="tenant-health-card-text">
                            <div class="tenant-health-card-label">{{ __('app.tenant_health_users_short') }}</div>
                            <p class="mb-0 text-muted small">{{ __('app.tenant_health_users_desc') }}</p>
                        </div>
                        <div class="tenant-health-card-icon" style="background: rgba(25,135,84,0.10); color:#198754;">
                            <i class="mdi mdi-account-group-outline"></i>
                        </div>
                    </div>
                    <div class="tenant-health-card-value">
                        {{ $activeUsers7d ?? 0 }}
                        <span class="h6 text-muted">/ {{ $totalUsers ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3 d-flex">
            <div class="card tenant-health-card h-100">
                <div class="card-body">
                    <div class="tenant-health-card-header mb-1">
                        <div class="tenant-health-card-text">
                            <div class="tenant-health-card-label">{{ __('app.tenant_health_complaints_short') }}</div>
                            <p class="mb-0 text-muted small">{{ __('app.tenant_health_complaints_desc') }}</p>
                        </div>
                        <div class="tenant-health-card-icon" style="background: rgba(220,53,69,0.10); color:#dc3545;">
                            <i class="mdi mdi-alert-circle-outline"></i>
                        </div>
                    </div>
                    <div class="tenant-health-card-value">
                        {{ $complaints['open'] ?? 0 }}
                        <span class="h6 text-muted">/ {{ $complaints['total'] ?? 0 }}</span>
                    </div>
                    @if (!empty($complaints['last_created_at']))
                        <div class="tenant-health-card-sub">
                            {{ __('app.tenant_health_last_complaint') }}:
                            {{ $complaints['last_created_at']->toDateTimeString() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3 d-flex">
            <div class="card tenant-health-card h-100">
                <div class="card-body">
                    <div class="tenant-health-card-header mb-1">
                        <div class="tenant-health-card-text">
                            <div class="tenant-health-card-label">{{ __('app.tenant_health_billing') }}</div>
                            <p class="mb-0 text-muted small">{{ __('app.tenant_health_billing_desc') }}</p>
                        </div>
                        <div class="tenant-health-card-icon" style="background: rgba(13,110,253,0.12); color:#0d6efd;">
                            <i class="mdi mdi-credit-card-outline"></i>
                        </div>
                    </div>
                    <div class="tenant-health-card-value">
                        {{ $billing['plan'] ?? '-' }}
                    </div>
                    <div class="tenant-health-card-sub">
                        <span
                            class="badge
            @switch($billing['status'])
              @case('trial') badge-info @break
              @case('expiring_soon') badge-warning @break
              @case('expired') badge-danger @break
              @case('active') badge-success @break
              @default badge-secondary
            @endswitch
          ">
                            @switch($billing['status'])
                                @case('trial')
                                    {{ __('app.tenant_billing_trial') }}
                                @break

                                @case('expiring_soon')
                                    {{ __('app.tenant_billing_expiring_soon') }}
                                @break

                                @case('expired')
                                    {{ __('app.tenant_billing_expired') }}
                                @break

                                @case('active')
                                    {{ __('app.tenant_billing_active') }}
                                @break

                                @default
                                    {{ __('app.tenant_billing_none') }}
                            @endswitch
                        </span>
                        @if (!empty($billing['subscription_end']))
                            <p class="mt-2 mb-0 text-muted small">
                                {{ __('app.subscription_end') }}: {{ $billing['subscription_end']->toDateString() }}
                                @if (!is_null($billing['days_to_end']))
                                    ({{ trans_choice('app.tenant_billing_days_left', $billing['days_to_end'], ['count' => $billing['days_to_end']]) }})
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
