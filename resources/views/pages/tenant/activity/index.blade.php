@extends('layout.master')

@section('content')
@php
  $isSub = Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'tenant.subdomain.');
  $prefix = $isSub ? 'tenant.subdomain' : 'tenant';
  $subdomain = request()->route('subdomain');
@endphp

<div class="row tenant-page-header">
  <div class="col-xl-10 mx-auto">
    <div class="card tenant-page-header-card">
      <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
          <h2 class="tenant-page-header-title">{{ __('app.activity_log_title') }}</h2>
          <p class="tenant-page-header-subtitle">{{ __('app.activity_log_subtitle') }}</p>
        </div>
        <div class="tenant-page-header-actions mt-3 mt-md-0">
          <a href="{{ route($prefix.'.dashboard', ['subdomain' => $subdomain]) }}" class="btn btn-outline-primary">
            <i class="mdi mdi-view-dashboard-outline"></i>
            <span>{{ __('app.tenant_panel') }}</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-xl-10 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.activity_log_chart_title') }}</h4>
        <p class="text-muted mb-3">{{ __('app.activity_log_chart_desc') }}</p>
        <div style="height:260px">
          <canvas id="tenant-activity-chart" height="260"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-xl-10 mx-auto">
    <div class="card">
      <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
          <h4 class="mb-0">{{ __('app.activity_log_table_title') }}</h4>
          <div class="mt-3 mt-md-0">
            <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('app.export') }}">
              <a href="{{ route($prefix.'.activity.export.excel', ['subdomain' => $subdomain] + request()->only('user_id','event')) }}" class="btn btn-outline-success">
                <i class="mdi mdi-file-excel-outline"></i>
                <span>{{ __('app.export_excel') }}</span>
              </a>
              <a href="{{ route($prefix.'.activity.export.pdf', ['subdomain' => $subdomain] + request()->only('user_id','event')) }}" class="btn btn-outline-danger">
                <i class="mdi mdi-file-pdf-box"></i>
                <span>{{ __('app.export_pdf') }}</span>
              </a>
            </div>
          </div>
        </div>
        <div class="table-responsive tenant-table-wrapper">
          <table class="table table-striped table-hover align-middle tenant-table">
            <thead>
              <tr>
                <th>{{ __('app.activity_when') }}</th>
                <th>{{ __('app.activity_user') }}</th>
                <th>{{ __('app.activity_event') }}</th>
                <th>{{ __('app.activity_action') }}</th>
                <th>{{ __('app.activity_description') }}</th>
              </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
              <tr>
                <td>{{ optional($log->created_at)->format('Y-m-d') }}</td>
                <td>{{ optional($log->user)->name ?? '—' }}</td>
                <td>{{ $log->event }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->description }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">{{ __('app.activity_log_empty') }}</td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-3 d-flex justify-content-center">
          {{ $logs->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script>
    (function() {
      var ctx = document.getElementById('tenant-activity-chart');
      if (!ctx) return;

      var labels = @json($chartLabels);
      var data = @json($chartData);

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: '{{ __('app.activity_log_chart_label') }}',
            data: data,
            borderColor: '#4c6fff',
            backgroundColor: 'rgba(76, 111, 255, 0.15)',
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointRadius: 3,
            pointBackgroundColor: '#4c6fff',
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              }
            }
          },
          plugins: {
            legend: {
              display: false
            }
          }
        }
      });
    })();
  </script>
@endpush
