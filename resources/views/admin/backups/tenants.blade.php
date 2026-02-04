@extends('layout.master')

@section('content')
<div class="content-wrapper tenant-layout">
  <div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <h4 class="mb-0">{{ __('نسخ احتياطي لقواعد بيانات المشتركين') }}</h4>
      <form method="POST" action="{{ route('admin.backups.tenants.backupAll') }}" onsubmit="return confirm('{{ __('هل أنت متأكد من نسخ قواعد بيانات جميع المشتركين؟') }}');">
        @csrf
        <button type="submit" class="btn btn-danger">
          <i class="mdi mdi-database-refresh"></i> {{ __('نسخ احتياطي للجميع') }}
        </button>
      </form>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ __('اسم المنشأة') }}</th>
              <th>{{ __('النطاق الفرعي') }}</th>
              <th>{{ __('اسم قاعدة البيانات') }}</th>
              <th class="text-right">{{ __('إجراءات') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tenants as $tenant)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $tenant->TenantName }}</td>
                <td>{{ $tenant->Subdomain }}</td>
                <td>{{ $tenant->DBName }}</td>
                <td class="text-right">
                  <form method="POST" action="{{ route('admin.backups.tenants.backup', $tenant->TenantID) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary" @if(! $tenant->DBName) disabled @endif>
                      <i class="mdi mdi-database-arrow-down"></i> {{ __('نسخ احتياطي') }}
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">{{ __('لا توجد منشآت مسجلة بعد.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
