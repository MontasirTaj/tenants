@extends('layout.master')

@push('plugin-styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h3>{{ __('app.payments') }}</h3>
    <form method="GET" action="{{ route('admin.payments.index') }}" class="form-inline">
      <label class="me-2">{{ __('app.plan') }}</label>
      <input type="text" name="plan" class="form-control form-control-sm me-2" value="{{ $currentPlan }}" placeholder="e.g. basic, pro"/>
      <button type="submit" class="btn btn-sm btn-primary">{{ __('app.view') }}</button>
    </form>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">{{ __('app.revenue_by_plan') }}</h5>
        <canvas id="payments-by-plan" height="100"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">{{ __('app.payments') }}</h5>
        <div class="table-responsive tenant-table-wrapper">
          <table id="admin-payments-table" class="table table-striped table-bordered tenant-table">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('app.plan') }}</th>
                <th>{{ __('app.amount') }}</th>
                <th>{{ __('app.currency') }}</th>
                <th>{{ __('app.status') }}</th>
                <th>{{ __('app.created_at') }}</th>
                <th>{{ __('app.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($payments as $p)
                <tr>
                  <td>{{ $p->id }}</td>
                  <td>{{ $p->plan ?? '-' }}</td>
                  <td>{{ isset($p->amount_total) ? number_format($p->amount_total / 100, 2) : '-' }}</td>
                  <td>{{ strtoupper($p->currency ?? '-') }}</td>
                  <td>{{ $p->status ?? '-' }}</td>
                  <td>{{ $p->created_at?->toDateString() }}</td>
                  <td>
                    @if($p->receipt_url)
                      <a href="{{ $p->receipt_url }}" target="_blank" class="btn btn-sm btn-outline-primary">{{ __('app.view') }}</a>
                    @else
                      -
                    @endif
                  </td>
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

@push('plugin-scripts')
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@push('custom-scripts')
<script>
  $(function () {
    $('#admin-payments-table').DataTable({
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, '{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}']],
      ordering: true,
      language: {
        url: '{{ app()->getLocale() === 'ar' ? 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json' : 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/en-GB.json' }}'
      }
    });

    const ctx = document.getElementById('payments-by-plan');
    if (ctx && window.Chart) {
      const data = {
        labels: {!! json_encode($totalsByPlan->pluck('plan')->map(fn($v) => $v ?: '-') ) !!},
        datasets: [{
          label: '{{ __('app.revenue') }}',
          data: {!! json_encode($totalsByPlan->pluck('total')->map(fn($v) => round(($v ?? 0)/100, 2)) ) !!},
          backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796'],
          borderWidth: 1
        }]
      };
      new Chart(ctx, {
        type: 'bar',
        data,
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: '{{ __('app.amount') }}' }
            }
          }
        }
      });
    }
  });
</script>
@endpush
