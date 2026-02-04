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
          <h2 class="tenant-page-header-title">{{ __('app.permissions_title') }}</h2>
          <p class="tenant-page-header-subtitle">{{ __('app.permissions_subtitle') }}</p>
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
  <div class="col-xl-8 mx-auto">
    <div class="card mb-4">
      <div class="card-body">
        <h4 class="mb-2">{{ __('app.permissions_title') }}</h4>
        <p class="text-muted mb-3">{{ __('app.permissions_intro') }}</p>
        <form method="POST" action="{{ route('tenant.subdomain.permissions.store', ['subdomain' => request()->route('subdomain')]) }}" class="mb-0">
          @csrf
          <div class="row align-items-end">
            <div class="col-md-8 mb-3 mb-md-0">
              <label class="form-label">{{ __('app.permission_name') }}</label>
              <input type="text" name="name" class="form-control" placeholder="{{ __('app.permission_name_placeholder') }}" required>
            </div>
            <div class="col-md-4 text-md-end">
              <button class="btn btn-primary tenant-action-btn">
                <i class="mdi mdi-plus-circle-outline"></i>
                <span>{{ __('app.add') }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
          <h5 class="mb-0">{{ __('app.permissions_list') }}</h5>
          <div class="mt-3 mt-md-0">
            <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('app.export') }}">
              <a href="{{ route('tenant.subdomain.permissions.export.excel', ['subdomain' => $sub]) }}" class="btn btn-outline-success">
                <i class="mdi mdi-file-excel-outline"></i>
                <span>{{ __('app.export_excel') }}</span>
              </a>
              <a href="{{ route('tenant.subdomain.permissions.export.pdf', ['subdomain' => $sub]) }}" class="btn btn-outline-danger">
                <i class="mdi mdi-file-pdf-box"></i>
                <span>{{ __('app.export_pdf') }}</span>
              </a>
            </div>
          </div>
        </div>
        <div class="table-responsive tenant-table-wrapper">
          <table class="table table-striped table-hover align-middle tenant-table" id="tenant-permissions-table">
            <thead>
              <tr>
                <th>{{ __('app.permission_name') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($permissions as $p)
              <tr>
                <td>{{ $p->name }}</td>
              </tr>
              @endforeach
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
    $('#tenant-permissions-table').DataTable({
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
