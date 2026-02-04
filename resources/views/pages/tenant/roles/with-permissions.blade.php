@extends('layout.master')

@section('content')
@php
  $sub = request()->route('subdomain');
@endphp

<div class="row tenant-page-header">
  <div class="col-xl-10 mx-auto">
    <div class="card tenant-page-header-card">
      <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
          <h2 class="tenant-page-header-title">{{ __('app.roles_with_permissions_title') }}</h2>
          <p class="tenant-page-header-subtitle">{{ __('app.roles_with_permissions_subtitle') }}</p>
        </div>
        <div class="tenant-page-header-actions mt-3 mt-md-0">
          <a href="{{ route('tenant.subdomain.dashboard', ['subdomain' => $sub]) }}" class="btn btn-outline-primary">
            <i class="mdi mdi-view-dashboard-outline"></i>
            <span>{{ __('app.tenant_panel') }}</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-10 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-2">{{ __('app.roles_permissions_title') }}</h4>
        <p class="text-muted mb-3">{{ __('app.roles_permissions_subtitle') }}</p>
        <div class="table-responsive tenant-table-wrapper">
          <table class="table table-striped align-middle tenant-table" id="tenant-roles-permissions-table">
        <thead>
          <tr>
            <th style="width: 25%">{{ __('app.role') }}</th>
            <th>{{ __('app.permissions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($roles as $role)
            <tr>
              <td>{{ $role->name }}</td>
              <td>
                @if($role->permissions->isEmpty())
                  <span class="text-muted">{{ __('app.permissions_list') }}</span>
                @else
                  @foreach($role->permissions as $permission)
                    <span class="badge bg-primary me-1 mb-1">{{ $permission->name }}</span>
                  @endforeach
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="2" class="text-center text-muted">{{ __('app.none') }}</td>
            </tr>
          @endforelse
        </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@push('plugin-scripts')
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('custom-scripts')
<script>
  $(function () {
    $('#tenant-roles-permissions-table').DataTable({
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, '{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}']],
      ordering: true,
      language: {
        url: '{{ app()->getLocale() === 'ar' ? 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json' : 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/en-GB.json' }}'
      }
    });
  });
</script>
@endpush
