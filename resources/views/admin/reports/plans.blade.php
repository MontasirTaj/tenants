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
    <h2 class="mb-0">{{ __('app.plans_report') }}</h2>
  </div>
</div>
<div class="row">
  <div class="col-md-6 grid-margin stretch-card">
    <div class="card h-100">
      <div class="card-body">
        <h4 class="card-title mb-3">{{ __('app.subscribers_by_plan') }}</h4>
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
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
  <div class="col-md-6 grid-margin stretch-card">
    <div class="card h-100">
      <div class="card-body">
        <h4 class="card-title mb-3">{{ __('app.subscribers_by_plan_chart') }}</h4>
        <div style="position: relative; height:260px;">
          <canvas id="plansDoughnutChart"></canvas>
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
    var planLabels = @json($byPlan->pluck('Plan'));
    var planValues = @json($byPlan->pluck('total'));
    var ctx = document.getElementById('plansDoughnutChart').getContext('2d');
    new Chart(ctx, {
      type: 'pie',
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
