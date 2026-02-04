@extends('layout.master')

@push('style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  .reports-page {
    font-family: 'Cairo', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  }
</style>
@endpush

@section('content')
<div class="reports-page">
<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h2 class="mb-0">{{ __('app.upcoming_expirations') }}</h2>
  </div>
</div>
<div class="row">
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title mb-3">{{ __('app.subscriptions_expiring_within', ['days' => $today->diffInDays($limitDate)]) }}</h4>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>{{ __('app.tenant_name') }}</th>
                <th>{{ __('app.plan') }}</th>
                <th>{{ __('app.subscription_end') }}</th>
                <th>{{ __('app.status') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tenants as $tenant)
                <tr>
                  <td>{{ $tenant->Name ?? $tenant->Subdomain }}</td>
                  <td>{{ $tenant->Plan ?? '-' }}</td>
                  <td>{{ optional($tenant->SubscriptionEndDate)->format('Y-m-d') }}</td>
                  <td>
                    @if($tenant->IsActive)
                      <span class="badge badge-success">{{ __('app.active') }}</span>
                    @else
                      <span class="badge badge-danger">{{ __('app.inactive') }}</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">{{ __('app.no_records') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection
