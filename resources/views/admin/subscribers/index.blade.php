@extends('layout.master')

@push('plugin-styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <style>
        .tenant-switch-toggle {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        .tenant-switch-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .tenant-switch-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ced4da;
            transition: .4s;
            border-radius: 34px;
        }

        .tenant-switch-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: #fff;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        }

        .tenant-switch-toggle input:checked+.tenant-switch-slider {
            background-color: #28a745;
        }

        .tenant-switch-toggle input:focus+.tenant-switch-slider {
            box-shadow: 0 0 1px #28a745;
        }

        .tenant-switch-toggle input:checked+.tenant-switch-slider:before {
            transform: translateX(22px);
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3>{{ __('app.subscribers') }}</h3>
            <div class="d-flex align-items-center">
                <form method="GET" action="{{ route('admin.subscribers.index') }}" class="form-inline me-3">
                    <label class="me-2">{{ __('app.plan') }}</label>
                    <select name="plan" class="form-control form-control-sm me-2" onchange="this.form.submit()">
                        <option value="">{{ __('app.all') }}</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan }}" @selected($currentPlan === $plan)>{{ $plan }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('admin.subscribers.risks') }}" class="btn btn-sm btn-outline-danger">
                    <i class="mdi mdi-alert-outline"></i> {{ __('app.tenant_risks_short') }}
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive tenant-table-wrapper">
                        <table id="admin-subscribers-table" class="table table-striped table-bordered tenant-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('app.name') }}</th>
                                    <th>{{ __('app.domain') }}</th>
                                    <th>{{ __('app.plan') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th>{{ __('app.subscription_end') }}</th>
                                    <th>{{ __('app.users_count') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tenants as $tenant)
                                    <tr>
                                        <td>{{ $tenant->TenantID }}</td>
                                        <td>{{ $tenant->TenantName }}</td>
                                        <td>{{ $tenant->Subdomain }}</td>
                                        <td>{{ $tenant->Plan }}</td>
                                        <td>
                                            @php
                                                $isActive =
                                                    (int) ($tenant->Status ?? ($tenant->IsActive ? 1 : 0)) === 1;
                                            @endphp
                                            <span class="badge {{ $isActive ? 'badge-success' : 'badge-danger' }}">
                                                {{ $isActive ? __('app.tenant_status_active') : __('app.tenant_status_inactive') }}
                                            </span>
                                        </td>
                                        <td>{{ $tenant->SubscriptionEndDate ? \Illuminate\Support\Carbon::parse($tenant->SubscriptionEndDate)->toDateString() : '-' }}
                                        </td>
                                        <td>{{ $tenant->user_count ?? '—' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <form method="POST"
                                                    action="{{ route('admin.subscribers.toggle', $tenant->TenantID) }}"
                                                    class="me-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <label class="tenant-switch-toggle"
                                                        for="tenant-active-{{ $tenant->TenantID }}">
                                                        <input type="checkbox" id="tenant-active-{{ $tenant->TenantID }}"
                                                            onchange="this.form.submit()" {{ $isActive ? 'checked' : '' }}>
                                                        <span class="tenant-switch-slider"></span>
                                                    </label>
                                                </form>
                                                <a href="{{ route('admin.subscribers.health', $tenant->TenantID) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-chart-line"></i>
                                                    {{ __('app.tenant_health_short') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination is handled client-side by DataTables now --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('custom-scripts')
    <script>
        $(function() {
            $('#admin-subscribers-table').DataTable({
                pageLength: 10,
                ordering: true,
                language: {
                    url: '{{ app()->getLocale() === 'ar' ? 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json' : 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/en-GB.json' }}'
                }
            });
        });
    </script>
@endpush
