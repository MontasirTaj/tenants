@extends('layout.master')

@section('content')
    <div class="tenant-dashboard">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card tenant-dashboard-hero">
                    <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                        <div class="mb-3 mb-lg-0">
                            @php
                                $tenantName = $tenantSetting->name ?? __('app.tenant_panel');
                            @endphp
                            <h2 class="tenant-dashboard-title">{{ $tenantName }}</h2>
                            <p class="tenant-dashboard-subtitle">{{ __('app.welcome', ['name' => $user->name]) }}</p>
                        </div>
                        @php
                            $isSub = \Illuminate\Support\Str::startsWith(
                                Route::currentRouteName(),
                                'tenant.subdomain.',
                            );
                            $prefix = $isSub ? 'tenant.subdomain' : 'tenant';
                            $sub = request()->route('subdomain');
                            $tenantUser = \Illuminate\Support\Facades\Auth::guard('tenant')->user();
                            $isManager =
                                $tenantUser && ($tenantUser->hasRole('admin') || $tenantUser->hasRole('Manager'));
                        @endphp
                        <div class="tenant-dashboard-actions d-flex flex-wrap">
                            @if ($isManager)
                                <a href="#" class="btn btn-outline-light"
                                    onclick="window.location='{{ route($prefix . '.users.index', ['subdomain' => $sub]) }}'">{{ __('app.users') }}</a>
                                <a href="#" class="btn btn-outline-light"
                                    onclick="window.location='{{ route($prefix . '.roles.index', ['subdomain' => $sub]) }}'">{{ __('app.roles') }}</a>
                                <a href="#" class="btn btn-outline-light"
                                    onclick="window.location='{{ route($prefix . '.permissions.index', ['subdomain' => $sub]) }}'">{{ __('app.permissions') }}</a>
                            @endif
                            @if ($tenantUser && $tenantUser->can('Attachement'))
                                <a href="#" class="btn btn-outline-light"
                                    onclick="window.location='{{ route($prefix . '.attachments.index', ['subdomain' => $sub]) }}'">{{ __('app.attachments') }}</a>
                            @endif
                            <a href="{{ route($prefix . '.logout', ['subdomain' => $sub]) }}"
                                class="btn btn-outline-danger"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('app.logout') }}</a>
                            <form id="logout-form" method="POST"
                                action="{{ route($prefix . '.logout', ['subdomain' => $sub]) }}" class="d-none">@csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($stats))
            <div class="row tenant-dashboard-metrics">
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="tenant-metric-card">
                        <div class="tenant-metric-icon"><i class="mdi mdi-account-multiple-outline"></i></div>
                        <div class="tenant-metric-label">{{ __('app.users') }}</div>
                        <div class="tenant-metric-value">
                            {{ $stats['users'] ?? 0 }}
                            <span class="tenant-metric-emoji">👥</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="tenant-metric-card">
                        <div class="tenant-metric-icon"><i class="mdi mdi-shield-account-outline"></i></div>
                        <div class="tenant-metric-label">{{ __('app.roles') }}</div>
                        <div class="tenant-metric-value">
                            {{ $stats['roles'] ?? 0 }}
                            <span class="tenant-metric-emoji">🛡️</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="tenant-metric-card">
                        <div class="tenant-metric-icon"><i class="mdi mdi-lock-outline"></i></div>
                        <div class="tenant-metric-label">{{ __('app.permissions') }}</div>
                        <div class="tenant-metric-value">
                            {{ $stats['permissions'] ?? 0 }}
                            <span class="tenant-metric-emoji">🔑</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="tenant-metric-card">
                        <div class="tenant-metric-icon"><i class="mdi mdi-file-upload-outline"></i></div>
                        <div class="tenant-metric-label">{{ __('app.attachments') }}</div>
                        <div class="tenant-metric-value">
                            {{ $stats['attachments'] ?? 0 }}
                            <span class="tenant-metric-emoji">📎</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($isManager)
            <div class="row tenant-dashboard-metrics mt-3">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card tenant-dashboard-card h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    {{ __('app.tenant_dashboard_users_count') }}</h6>
                                <h3 class="card-title mb-0">{{ $stats['users'] ?? 0 }}</h3>
                            </div>
                            <div class="mt-2 text-muted small">{{ __('app.tenant_dashboard_users_hint') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card tenant-dashboard-card h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">{{ __('app.tenant_dash_operations') }}
                                </h6>
                                <h3 class="card-title mb-0">
                                    {{ $operationsToday ?? 0 }}
                                </h3>
                            </div>
                            <div class="mt-2 text-muted small">
                                {{ __('app.tenant_dash_operations_today_week', [
                                    'today' => $operationsToday ?? 0,
                                    'week' => $operationsThisWeek ?? 0,
                                ]) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card tenant-dashboard-card h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">{{ __('app.tenant_dash_last_activity') }}
                                </h6>
                                <h5 class="card-title mb-0">
                                    @if ($lastImportantActivityAt)
                                        {{ $lastImportantActivityAt->format('Y-m-d H:i') }}
                                    @else
                                        <span class="text-muted">&#8212;</span>
                                    @endif
                                </h5>
                            </div>
                            <div class="mt-2 text-muted small">{{ __('app.tenant_dash_last_activity') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card tenant-dashboard-card h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    {{ __('app.tenant_dash_subscription_status') }}</h6>
                                <h5 class="card-title mb-0">
                                    @php
                                        $status = $billing['status'] ?? 'none';
                                    @endphp
                                    @switch($status)
                                        @case('trial')
                                            {{ __('app.subscriber_status_trial') ?? 'Trial' }}
                                        @break

                                        @case('expiring_soon')
                                            {{ __('app.subscriber_status_expiring_soon') ?? 'Expiring soon' }}
                                        @break

                                        @case('expired')
                                            {{ __('app.subscriber_status_expired') ?? 'Expired' }}
                                        @break

                                        @case('active')
                                            {{ __('app.subscriber_status_active') ?? 'Active' }}
                                        @break

                                        @default
                                            <span class="text-muted">&#8212;</span>
                                    @endswitch
                                </h5>
                            </div>
                            <div class="mt-2 text-muted small">
                                {{ __('app.tenant_dash_open_complaints') }}: {{ $openComplaints ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('app.tenant_dash_activity_timeline') }}</h5>
                        </div>
                        <div class="card-body">
                            @if ($recentActivities->isEmpty())
                                <p class="text-muted mb-0">{{ __('app.tenant_dash_activity_empty') }}</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach ($recentActivities as $activity)
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-0 me-3">
                                                <div class="fw-semibold small">
                                                    {{ $activity->user ? $activity->user->name : __('app.unknown_user') }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $activity->description ?: $activity->action ?: $activity->event }}
                                                </div>
                                            </div>
                                            <span
                                                class="text-muted small">{{ $activity->created_at->format('Y-m-d H:i') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
