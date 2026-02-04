@extends('layout.master')

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3>{{ __('app.tenant_risks_title') }}</h3>
                <p class="text-muted mb-0">{{ __('app.tenant_risks_subtitle', ['days' => $windowDays]) }}</p>
            </div>
            <a href="{{ route('admin.subscribers.index') }}" class="btn btn-sm btn-outline-secondary">
                {{ __('app.back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (empty($items))
                        <p class="text-muted mb-0">{{ __('app.tenant_risks_empty') }}</p>
                    @else
                        <div class="table-responsive tenant-table-wrapper">
                            <table class="table table-striped table-bordered tenant-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('app.tenant_name') }}</th>
                                        <th>{{ __('app.domain') }}</th>
                                        <th>{{ __('app.plan') }}</th>
                                        <th>{{ __('app.subscription_end') }}</th>
                                        <th>{{ __('app.tenant_risks_days_to_end') }}</th>
                                        <th>{{ __('app.tenant_health_users_short') }}</th>
                                        <th>{{ __('app.tenant_health_complaints_short') }}</th>
                                        <th>{{ __('app.tenant_risk_score') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $highlightTenantId = request()->get('tenant');
                                    @endphp
                                    @foreach ($items as $row)
                                        @php
                                            $tenant = $row['tenant'];
                                            $isHighlighted =
                                                $highlightTenantId &&
                                                (int) $highlightTenantId === (int) $tenant->TenantID;
                                        @endphp
                                        <tr class="{{ $isHighlighted ? 'risk-row-highlight' : '' }}">
                                            <td>{{ $tenant->TenantID }}</td>
                                            <td>{{ $tenant->TenantName }}</td>
                                            <td>{{ $tenant->Subdomain }}</td>
                                            <td>{{ $tenant->Plan }}</td>
                                            <td>{{ $tenant->SubscriptionEndDate ? \Illuminate\Support\Carbon::parse($tenant->SubscriptionEndDate)->toDateString() : '-' }}
                                            </td>
                                            <td>{{ $row['days_to_end'] }}</td>
                                            <td>{{ $row['active_users_7d'] ?? '0' }}</td>
                                            <td>{{ $row['open_complaints'] }} / {{ $row['recent_complaints'] }}</td>
                                            <td>
                                                <span
                                                    class="badge
                        @if ($row['risk_level'] === 'high') badge-danger
                        @elseif($row['risk_level'] === 'medium') badge-warning
                        @else badge-secondary @endif
                      ">
                                                    {{ __('app.tenant_risk_level_' . $row['risk_level']) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.subscribers.health', $tenant->TenantID) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-chart-line"></i>
                                                    {{ __('app.tenant_health_short') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .risk-row-highlight {
            background-color: #ffd6d6 !important;
        }
    </style>
@endpush
