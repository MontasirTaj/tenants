@extends('layout.master')

@push('style')
    <style>
        .dashboard-reports-summary-card {
            border-radius: 18px;
            border: 1px solid rgba(16, 44, 79, 0.12);
            background: linear-gradient(135deg, #ffffff 0%, #f4f7fb 45%, #e8f0ff 100%);
            box-shadow: 0 14px 30px rgba(16, 44, 79, 0.08);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .dashboard-reports-summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(16, 44, 79, 0.16);
        }

        .dashboard-reports-summary-body {
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .dashboard-reports-summary-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        [dir="ltr"] .dashboard-reports-summary-header {
            flex-direction: row;
        }

        [dir="rtl"] .dashboard-reports-summary-header {
            flex-direction: row-reverse;
        }

        .dashboard-reports-summary-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(16, 44, 79, 0.08);
            color: #102c4f;
            font-size: 1.4rem;
        }

        .dashboard-reports-summary-text {
            text-align: start;
            padding-inline-end: .4rem;
        }

        [dir="rtl"] .dashboard-reports-summary-text {
            text-align: end;
        }

        .dashboard-reports-summary-label {
            font-size: .8rem;
            font-weight: 500;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #6b7a90;
            margin-bottom: .15rem;
        }

        .dashboard-reports-summary-value {
            font-size: 1.7rem;
            font-weight: 700;
            color: #102c4f;
            margin: 0;
        }

        .dashboard-reports-summary-value.text-success {
            color: #198754 !important;
        }

        .dashboard-reports-summary-value.text-danger {
            color: #dc3545 !important;
        }

        .dashboard-reports-summary-sub {
            font-size: .78rem;
            color: #8a96ac;
        }

        .dashboard-reports-summary-number {
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('app.dashboard') }}</h2>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card h-100 dashboard-reports-summary-card">
                <div class="card-body dashboard-reports-summary-body">
                    <div class="dashboard-reports-summary-header mb-1">
                        <div class="dashboard-reports-summary-text">
                            <div class="dashboard-reports-summary-label">{{ __('app.total_subscribers') }}</div>
                        </div>
                        <div class="dashboard-reports-summary-icon">
                            <i class="mdi mdi-account-multiple"></i>
                        </div>
                    </div>
                    <div class="dashboard-reports-summary-number">
                        <p class="dashboard-reports-summary-value mb-0">{{ $totalSubscribers }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card h-100 dashboard-reports-summary-card">
                <div class="card-body dashboard-reports-summary-body">
                    <div class="dashboard-reports-summary-header mb-1">
                        <div class="dashboard-reports-summary-text">
                            <div class="dashboard-reports-summary-label">{{ __('app.active_subscribers') }}</div>
                        </div>
                        <div class="dashboard-reports-summary-icon"
                            style="background: rgba(25,135,84,0.10); color:#198754;">
                            <i class="mdi mdi-check-circle-outline"></i>
                        </div>
                    </div>
                    <div class="dashboard-reports-summary-number">
                        <p class="dashboard-reports-summary-value text-success mb-0">{{ $activeSubscribers }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card h-100 dashboard-reports-summary-card">
                <div class="card-body dashboard-reports-summary-body">
                    <div class="dashboard-reports-summary-header mb-1">
                        <div class="dashboard-reports-summary-text">
                            <div class="dashboard-reports-summary-label">{{ __('app.inactive_subscribers') }}</div>
                        </div>
                        <div class="dashboard-reports-summary-icon"
                            style="background: rgba(220,53,69,0.10); color:#dc3545;">
                            <i class="mdi mdi-alert-circle-outline"></i>
                        </div>
                    </div>
                    <div class="dashboard-reports-summary-number">
                        <p class="dashboard-reports-summary-value text-danger mb-0">{{ $inactiveSubscribers }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card h-100 dashboard-reports-summary-card">
                <div class="card-body dashboard-reports-summary-body">
                    <div class="dashboard-reports-summary-header mb-1">
                        <div class="dashboard-reports-summary-text">
                            <div class="dashboard-reports-summary-label">{{ __('app.new_this_month') }}</div>
                        </div>
                        <div class="dashboard-reports-summary-icon"
                            style="background: rgba(255,193,7,0.16); color:#f0ad02;">
                            <i class="mdi mdi-trending-up"></i>
                        </div>
                    </div>
                    <div class="dashboard-reports-summary-number">
                        <p class="dashboard-reports-summary-value mb-0">{{ $newThisMonth }}</p>
                        <div class="dashboard-reports-summary-sub mt-1">{{ __('app.new_this_week') }}: {{ $newThisWeek }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card h-100 dashboard-reports-summary-card">
                <div class="card-body dashboard-reports-summary-body">
                    <div class="dashboard-reports-summary-header mb-1">
                        <div class="dashboard-reports-summary-text">
                            <div class="dashboard-reports-summary-label">{{ __('app.expired_subscribers') }}</div>
                        </div>
                        <div class="dashboard-reports-summary-icon"
                            style="background: rgba(220,53,69,0.10); color:#dc3545;">
                            <i class="mdi mdi-timer-off"></i>
                        </div>
                    </div>
                    <div class="dashboard-reports-summary-number">
                        <p class="dashboard-reports-summary-value text-danger mb-0">{{ $expiredSubscribers }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card h-100 dashboard-reports-summary-card">
                <div class="card-body dashboard-reports-summary-body">
                    <div class="dashboard-reports-summary-header mb-1">
                        <div class="dashboard-reports-summary-text">
                            <div class="dashboard-reports-summary-label">{{ __('app.expiring_soon_subscribers') }}</div>
                        </div>
                        <div class="dashboard-reports-summary-icon"
                            style="background: rgba(255,193,7,0.16); color:#f0ad02;">
                            <i class="mdi mdi-calendar-alert"></i>
                        </div>
                    </div>
                    <div class="dashboard-reports-summary-number">
                        <p class="dashboard-reports-summary-value mb-0">{{ $expiringSoonSubscribers }}</p>
                        <div class="dashboard-reports-summary-sub mt-1">
                            {{ __('app.subscriptions_expiring_within', ['days' => $expiringSoonWindowDays]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card h-100 dashboard-reports-summary-card">
                <div class="card-body dashboard-reports-summary-body">
                    <div class="dashboard-reports-summary-header mb-1">
                        <div class="dashboard-reports-summary-text">
                            <div class="dashboard-reports-summary-label">{{ __('app.top_plan') }}</div>
                        </div>
                        <div class="dashboard-reports-summary-icon"
                            style="background: rgba(13,110,253,0.12); color:#0d6efd;">
                            <i class="mdi mdi-star-circle"></i>
                        </div>
                    </div>
                    <div class="dashboard-reports-summary-number">
                        @if ($topPlanName)
                            <p class="dashboard-reports-summary-value mb-0">{{ $topPlanName }}</p>
                            <div class="dashboard-reports-summary-sub mt-1">
                                {{ $topPlanTotal }} {{ __('app.subscribers') }}
                            </div>
                        @else
                            <p class="dashboard-reports-summary-value mb-0">-</p>
                            <div class="dashboard-reports-summary-sub mt-1">
                                {{ __('app.top_plan_no_data') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card h-100 dashboard-reports-summary-card">
                <div class="card-body dashboard-reports-summary-body">
                    <div class="dashboard-reports-summary-header mb-1">
                        <div class="dashboard-reports-summary-text">
                            <div class="dashboard-reports-summary-label">{{ __('app.admin_complaints_open') }}</div>
                        </div>
                        <div class="dashboard-reports-summary-icon"
                            style="background: rgba(220,53,69,0.10); color:#dc3545;">
                            <i class="mdi mdi-alert-decagram"></i>
                        </div>
                    </div>
                    <div class="dashboard-reports-summary-number">
                        <p class="dashboard-reports-summary-value text-danger mb-0">{{ $openComplaints }}</p>
                        <div class="dashboard-reports-summary-sub mt-1">
                            {{ __('app.admin_complaints_total') }}: {{ $totalComplaints }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card h-100 d-flex align-items-center justify-content-center text-center"
                style="border-radius:18px; position:relative; overflow:hidden;">
                <div class="card-body">
                    <h4 class="card-title mb-3">{{ __('app.subscribers') }}</h4>
                    <div class="d-flex justify-content-center mb-3">
                        <div
                            style="width:90px;height:90px;border-radius:50%;background:#102c4f;display:flex;align-items:center;justify-content:center;box-shadow:0 12px 28px rgba(16,44,79,0.45);">
                            <span
                                style="color:#fff;font-size:2rem;font-weight:700;line-height:1;">{{ $totalSubscribers }}</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('admin.subscribers.index') }}"
                            class="btn btn-outline-primary btn-sm mb-2">{{ __('app.view_all') }}</a>
                        <a href="{{ route('admin.payments.index') }}"
                            class="btn btn-primary btn-sm">{{ __('app.payments') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title mb-3">{{ __('app.subscribers') }} / {{ __('app.plan') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('app.plan') }}</th>
                                    <th>#</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // ألوان مميزة متعاقبة لكل باقة
                                    $rowColors = [
                                        'rgba(16, 44, 79, 0.10)', // أزرق داكن خفيف
                                        'rgba(25, 135, 84, 0.10)', // أخضر
                                        'rgba(13, 110, 253, 0.10)', // أزرق فاتح
                                        'rgba(255, 193, 7, 0.16)', // أصفر/ذهبي
                                        'rgba(220, 53, 69, 0.12)', // أحمر لطيف
                                    ];
                                @endphp
                                @foreach ($plans as $index => $plan)
                                    @php($bg = $rowColors[$index % count($rowColors)])
                                    <tr style="background: {{ $bg }};">
                                        <td class="font-weight-500">{{ $plan->Plan ?? '-' }}</td>
                                        <td><span class="badge badge-light"
                                                style="min-width:40px;">{{ $plan->total }}</span></td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.subscribers.index', ['plan' => $plan->Plan]) }}"
                                                class="btn btn-sm btn-outline-primary">{{ __('app.view') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title mb-3">{{ __('app.subscribers_over_time') }}</h4>
                    <div style="position: relative; height:260px;">
                        <canvas id="dashSubscribersOverTimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title mb-3">{{ __('app.subscribers_by_plan') }}</h4>
                    <div style="position: relative; height:260px;">
                        <canvas id="dashSubscribersByPlanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title mb-3">{{ __('app.most_complaining_tenants') }}</h4>
                    @if (!$mostComplainingTenants->count())
                        <p class="text-muted mb-0">{{ __('app.no_records') }}</p>
                    @else
                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('app.tenant_name') }}</th>
                                                <th>{{ __('app.complaints_count') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mostComplainingTenants as $row)
                                                <tr>
                                                    <td>{{ optional($row->tenant)->TenantName ?? ($row->tenant_subdomain ?? 'N/A') }}
                                                    </td>
                                                    <td><span class="badge badge-light"
                                                            style="min-width:40px;">{{ $row->total }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="position: relative; height:260px;">
                                    <canvas id="dashTopComplaintsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            var months = @json($months ?? []);
            var seriesActive = @json($seriesActive ?? []);

            var lineCanvas = document.getElementById('dashSubscribersOverTimeChart');
            if (lineCanvas && months.length && seriesActive.length) {
                var ctxLine = lineCanvas.getContext('2d');
                new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: '{{ __('app.total_subscribers') }}',
                            data: seriesActive,
                            borderColor: 'rgba(16, 44, 79, 0.9)',
                            backgroundColor: 'rgba(16, 44, 79, 0.15)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            var byPlan = @json($plans ?? []);
            var pieCanvas = document.getElementById('dashSubscribersByPlanChart');
            if (pieCanvas && byPlan.length) {
                var planLabels = byPlan.map(function(p) {
                    return p.Plan;
                });
                var planValues = byPlan.map(function(p) {
                    return p.total;
                });
                var ctxPie = pieCanvas.getContext('2d');
                new Chart(ctxPie, {
                    type: 'doughnut',
                    data: {
                        labels: planLabels,
                        datasets: [{
                            data: planValues,
                            backgroundColor: [
                                'rgba(16, 44, 79, 0.9)',
                                'rgba(13, 110, 253, 0.9)',
                                'rgba(25, 135, 84, 0.9)',
                                'rgba(255, 193, 7, 0.9)',
                                'rgba(220, 53, 69, 0.9)'
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            var topComplaints = @json($topComplaintsChartData ?? []);
            var complaintsCanvas = document.getElementById('dashTopComplaintsChart');
            if (complaintsCanvas && topComplaints.length) {
                var labels = topComplaints.map(function(item) {
                    return item.name;
                });
                var values = topComplaints.map(function(item) {
                    return item.total;
                });
                var ctxBar = complaintsCanvas.getContext('2d');
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '{{ __('app.complaints_count') }}',
                            data: values,
                            backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }
        })();
    </script>
@endpush
