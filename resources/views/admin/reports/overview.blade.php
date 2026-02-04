@extends('layout.master')

@push('style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  .reports-page {
    font-family: 'Cairo', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  }
  .reports-summary-card {
    border-radius: 18px;
    border: 1px solid rgba(16, 44, 79, 0.12);
    background: linear-gradient(135deg, #ffffff 0%, #f4f7fb 45%, #e8f0ff 100%);
    box-shadow: 0 14px 30px rgba(16, 44, 79, 0.08);
    transition: transform .18s ease, box-shadow .18s ease;
  }
  .reports-summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 40px rgba(16, 44, 79, 0.16);
  }
  .reports-summary-body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
  }
  .reports-summary-icon {
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
  .reports-summary-text {
    text-align: start;
  }
  [dir="rtl"] .reports-summary-text {
    text-align: end;
  }
  .reports-summary-label {
    font-size: .85rem;
    font-weight: 500;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: #6b7a90;
    margin-bottom: .2rem;
  }
  .reports-summary-value {
    font-size: 1.7rem;
    font-weight: 700;
    color: #102c4f;
    margin: 0;
  }
  .reports-summary-value.text-success {
    color: #198754 !important;
  }
  .reports-summary-value.text-danger {
    color: #dc3545 !important;
  }
  .reports-summary-sub {
    font-size: .78rem;
    color: #8a96ac;
  }
</style>
@endpush

@section('content')
<div class="reports-page">
<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h2 class="mb-0">{{ __('app.reports_overview') }}</h2>
  </div>
</div>
<div class="row">
  <div class="col-md-3 grid-margin stretch-card">
    <div class="card h-100 reports-summary-card">
      <div class="card-body reports-summary-body">
        <div class="reports-summary-text">
          <div class="reports-summary-label">{{ __('app.total_subscribers') }}</div>
          <p class="reports-summary-value mb-0">{{ $total }}</p>
        </div>
        <div class="reports-summary-icon">
          <i class="mdi mdi-account-multiple"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 grid-margin stretch-card">
    <div class="card h-100 reports-summary-card">
      <div class="card-body reports-summary-body">
        <div class="reports-summary-text">
          <div class="reports-summary-label">{{ __('app.active_subscribers') }}</div>
          <p class="reports-summary-value text-success mb-0">{{ $active }}</p>
        </div>
        <div class="reports-summary-icon" style="background: rgba(25,135,84,0.10); color:#198754;">
          <i class="mdi mdi-check-circle-outline"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 grid-margin stretch-card">
    <div class="card h-100 reports-summary-card">
      <div class="card-body reports-summary-body">
        <div class="reports-summary-text">
          <div class="reports-summary-label">{{ __('app.inactive_subscribers') }}</div>
          <p class="reports-summary-value text-danger mb-0">{{ $inactive }}</p>
        </div>
        <div class="reports-summary-icon" style="background: rgba(220,53,69,0.10); color:#dc3545;">
          <i class="mdi mdi-alert-circle-outline"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 grid-margin stretch-card">
    <div class="card h-100 reports-summary-card">
      <div class="card-body reports-summary-body">
        <div class="reports-summary-text">
          <div class="reports-summary-label">{{ __('app.new_this_month') }}</div>
          <p class="reports-summary-value mb-0">{{ $newThisMonth }}</p>
          <div class="reports-summary-sub mt-1">{{ __('app.new_this_week') }}: {{ $newThisWeek }}</div>
        </div>
        <div class="reports-summary-icon" style="background: rgba(255,193,7,0.16); color:#f0ad02;">
          <i class="mdi mdi-trending-up"></i>
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
          <canvas id="subscribersOverTimeChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6 grid-margin stretch-card">
    <div class="card h-100">
      <div class="card-body">
        <h4 class="card-title mb-3">{{ __('app.subscribers_by_plan') }}</h4>
        <div style="position: relative; height:260px;">
          <canvas id="subscribersByPlanChart"></canvas>
        </div>
        <div class="table-responsive mt-3">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th>{{ __('app.plan') }}</th>
                <th>#</th>
              </tr>
            </thead>
            <tbody>
              @foreach($byPlan as $plan)
                <tr>
                  <td>{{ $plan->Plan ?? '-' }}</td>
                  <td>{{ $plan->total }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection

@push('custom-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  (function() {
    var months = @json($months);
    var seriesActive = @json($seriesActive);

    var ctxLine = document.getElementById('subscribersOverTimeChart').getContext('2d');
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
        plugins: { legend: { display: true } },
        scales: {
          y: { beginAtZero: true, ticks: { precision:0 } }
        }
      }
    });

    var planLabels = @json($byPlan->pluck('Plan'));
    var planValues = @json($byPlan->pluck('total'));
    var ctxPie = document.getElementById('subscribersByPlanChart').getContext('2d');
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
        plugins: { legend: { position: 'bottom' } }
      }
    });
  })();
</script>
@endpush
