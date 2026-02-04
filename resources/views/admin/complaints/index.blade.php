@extends('layout.master')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
          <h3 class="mb-1">{{ __('app.admin_complaints_title') }}</h3>
          <p class="text-muted mb-0">{{ __('app.admin_complaints_subtitle') }}</p>
        </div>
        <div class="mt-3 mt-md-0 text-md-end">
          <div class="small text-muted">
            <span class="me-3">{{ __('app.admin_complaints_total') }}: {{ $stats['total'] }}</span>
            <span class="me-3">{{ __('app.admin_complaints_open') }}: {{ $stats['open'] }}</span>
            <span class="me-3">{{ __('app.admin_complaints_in_progress') }}: {{ $stats['in_progress'] }}</span>
            <span>{{ __('app.admin_complaints_closed') }}: {{ $stats['closed'] }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
          <h4 class="mb-0">{{ __('app.admin_complaints_list_title') }}</h4>
          <div class="mt-3 mt-md-0">
            <form method="GET" class="form-inline">
              <label class="mr-2">{{ __('app.filter_status') }}</label>
              <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                <option value="">{{ __('app.all') }}</option>
                <option value="open" @if($currentStatus === 'open') selected @endif>{{ __('app.tenant_complaint_status_open') }}</option>
                <option value="in_progress" @if($currentStatus === 'in_progress') selected @endif>{{ __('app.tenant_complaint_status_in_progress') }}</option>
                <option value="closed" @if($currentStatus === 'closed') selected @endif>{{ __('app.tenant_complaint_status_closed') }}</option>
              </select>
            </form>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped" id="admin-complaints-table" data-feed-url="{{ route('admin.complaints.feed', ['status' => $currentStatus]) }}">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('app.admin_complaint_tenant') }}</th>
                <th>{{ __('app.admin_complaint_subject') }}</th>
                <th>{{ __('app.admin_complaint_status') }}</th>
                <th>{{ __('app.admin_complaint_created') }}</th>
                <th>{{ __('app.admin_complaint_reply') }}</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="admin-complaints-tbody">
              @foreach($complaints as $complaint)
                <tr>
                  <td>{{ $complaint->id }}</td>
                  <td>
                    {{ optional($complaint->tenant)->TenantName ?? '-' }}
                    @if(optional($complaint->tenant)->Subdomain)
                      <div class="text-muted small">{{ optional($complaint->tenant)->Subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}</div>
                    @endif
                  </td>
                  <td>{{ $complaint->subject }}</td>
                  <td>
                    @if($complaint->status === 'closed')
                      <span class="badge badge-success">{{ __('app.tenant_complaint_status_closed') }}</span>
                    @elseif($complaint->status === 'in_progress')
                      <span class="badge badge-warning">{{ __('app.tenant_complaint_status_in_progress') }}</span>
                    @else
                      <span class="badge badge-info">{{ __('app.tenant_complaint_status_open') }}</span>
                    @endif
                  </td>
                  <td>{{ optional($complaint->created_at)->format('Y-m-d H:i') }}</td>
                  <td>
                    @if($complaint->admin_reply)
                      <span class="badge badge-primary">{{ __('app.tenant_complaint_has_reply') }}</span>
                    @else
                      <span class="text-muted">{{ __('app.tenant_complaint_no_reply') }}</span>
                    @endif
                  </td>
                  <td class="text-right">
                    <a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-secondary">{{ __('app.view_details') }}</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <p class="text-muted small mt-3 mb-0">{{ __('app.admin_complaints_autorefresh_note') }}</p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('custom-scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('admin-complaints-table');
    if (!table || !window.axios) return;

    const feedUrl = table.getAttribute('data-feed-url');
    const tbody = document.getElementById('admin-complaints-tbody');

    async function refreshComplaints() {
      try {
        const response = await window.axios.get(feedUrl);
        const items = response.data.items || [];

        let html = '';
        items.forEach(function (item) {
          let statusBadge = '';
          if (item.status === 'closed') {
            statusBadge = '<span class="badge badge-success">{{ __('app.tenant_complaint_status_closed') }}</span>';
          } else if (item.status === 'in_progress') {
            statusBadge = '<span class="badge badge-warning">{{ __('app.tenant_complaint_status_in_progress') }}</span>';
          } else {
            statusBadge = '<span class="badge badge-info">{{ __('app.tenant_complaint_status_open') }}</span>';
          }

          const replyText = item.has_reply
            ? '<span class="badge badge-primary">{{ __('app.tenant_complaint_has_reply') }}</span>'
            : '<span class="text-muted">{{ __('app.tenant_complaint_no_reply') }}</span>';

          const tenantInfo = (item.tenant_name || '-') +
            (item.tenant_subdomain ? '<div class="text-muted small">' + item.tenant_subdomain + '.{{ parse_url(config('app.url'), PHP_URL_HOST) }}</div>' : '');

          html += '<tr>' +
            '<td>' + item.id + '</td>' +
            '<td>' + tenantInfo + '</td>' +
            '<td>' + item.subject + '</td>' +
            '<td>' + statusBadge + '</td>' +
            '<td>' + (item.created_at || '') + '</td>' +
            '<td>' + replyText + '</td>' +
            '<td class="text-right"><a href="' + item.show_url + '" class="btn btn-sm btn-outline-secondary">{{ __('app.view_details') }}</a></td>' +
            '</tr>';
        });

        tbody.innerHTML = html;
      } catch (e) {
        // يمكن لاحقاً تسجيل الخطأ في الكونسول فقط
        console.error('Failed to refresh complaints', e);
      }
    }

    // تحديث أولي ثم تحديث دوري
    refreshComplaints();
    setInterval(refreshComplaints, 15000);
  });
</script>
@endpush
