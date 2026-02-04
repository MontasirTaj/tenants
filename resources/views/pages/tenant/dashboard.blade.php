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
      $isSub = \Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'tenant.subdomain.');
      $prefix = $isSub ? 'tenant.subdomain' : 'tenant';
      $sub = request()->route('subdomain');
      $tenantUser = \Illuminate\Support\Facades\Auth::guard('tenant')->user();
      $isManager = $tenantUser && ($tenantUser->hasRole('admin') || $tenantUser->hasRole('Manager'));
    @endphp
          <div class="tenant-dashboard-actions d-flex flex-wrap">
            @if($isManager)
              <a href="#" class="btn btn-outline-light" onclick="window.location='{{ route($prefix.'.users.index', ['subdomain' => $sub]) }}'">{{ __('app.users') }}</a>
              <a href="#" class="btn btn-outline-light" onclick="window.location='{{ route($prefix.'.roles.index', ['subdomain' => $sub]) }}'">{{ __('app.roles') }}</a>
              <a href="#" class="btn btn-outline-light" onclick="window.location='{{ route($prefix.'.permissions.index', ['subdomain' => $sub]) }}'">{{ __('app.permissions') }}</a>
            @endif
            @if($tenantUser && $tenantUser->can('Attachement'))
              <a href="#" class="btn btn-outline-light" onclick="window.location='{{ route($prefix.'.attachments.index', ['subdomain' => $sub]) }}'">{{ __('app.attachments') }}</a>
            @endif
            <a href="{{ route($prefix.'.logout', ['subdomain' => $sub]) }}" class="btn btn-outline-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('app.logout') }}</a>
            <form id="logout-form" method="POST" action="{{ route($prefix.'.logout', ['subdomain' => $sub]) }}" class="d-none">@csrf</form>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if(isset($stats))
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

  <div class="row tenant-dashboard-grid">
    @if($isManager)
      <div class="col-md-6 col-lg-3 mb-3">
        <div class="card tenant-dashboard-card">
          <div class="card-body">
            <i class="mdi mdi-account-multiple-outline tenant-dashboard-icon"></i>
            <h5>{{ __('app.users') }}</h5>
            <p>{{ __('app.tenant_dash_users_desc') }}</p>
            <a href="#" class="btn btn-primary btn-sm" onclick="window.location='{{ route($prefix.'.users.index', ['subdomain' => $sub]) }}'">{{ __('app.users') }}</a>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 mb-3">
        <div class="card tenant-dashboard-card">
          <div class="card-body">
            <i class="mdi mdi-shield-account-outline tenant-dashboard-icon"></i>
            <h5>{{ __('app.roles') }}</h5>
            <p>{{ __('app.tenant_dash_roles_desc') }}</p>
            <a href="#" class="btn btn-primary btn-sm" onclick="window.location='{{ route($prefix.'.roles.index', ['subdomain' => $sub]) }}'">{{ __('app.roles') }}</a>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 mb-3">
        <div class="card tenant-dashboard-card">
          <div class="card-body">
            <i class="mdi mdi-lock-outline tenant-dashboard-icon"></i>
            <h5>{{ __('app.permissions') }}</h5>
            <p>{{ __('app.tenant_dash_permissions_desc') }}</p>
            <a href="#" class="btn btn-primary btn-sm" onclick="window.location='{{ route($prefix.'.permissions.index', ['subdomain' => $sub]) }}'">{{ __('app.permissions') }}</a>
          </div>
        </div>
      </div>
    @endif
    @if($tenantUser && $tenantUser->can('Attachement'))
      <div class="col-md-6 col-lg-3 mb-3">
        <div class="card tenant-dashboard-card">
          <div class="card-body">
            <i class="mdi mdi-file-upload-outline tenant-dashboard-icon"></i>
            <h5>{{ __('app.attachments') }}</h5>
            <p>{{ __('app.tenant_dash_attachments_desc') }}</p>
            <a href="#" class="btn btn-primary btn-sm" onclick="window.location='{{ route($prefix.'.attachments.index', ['subdomain' => $sub]) }}'">{{ __('app.attachments') }}</a>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
