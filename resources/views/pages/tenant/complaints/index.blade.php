@extends('layout.master')

@section('content')
@php
  $sub = $subdomain ?? request()->route('subdomain');
@endphp

<div class="row tenant-page-header">
  <div class="col-xl-10 mx-auto">
    <div class="card tenant-page-header-card">
      <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
          <h2 class="tenant-page-header-title">{{ __('app.tenant_complaints_title') }}</h2>
          <p class="tenant-page-header-subtitle">{{ __('app.tenant_complaints_subtitle') }}</p>
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

<div class="row mb-4">
  <div class="col-xl-10 mx-auto">
    <div class="card mb-4">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.tenant_complaint_new_title') }}</h4>
        <p class="text-muted mb-3">{{ __('app.tenant_complaint_new_subtitle') }}</p>

        @if(session('status'))
          <div class="alert alert-success mb-3">{{ session('status') }}</div>
        @endif
        @if($errors->any())
          <div class="alert alert-danger mb-3">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('tenant.subdomain.complaints.store', ['subdomain' => $sub]) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="form-group mb-3">
            <label for="subject">{{ __('app.tenant_complaint_subject') }}</label>
            <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" required>
          </div>
          <div class="form-group mb-3">
            <label for="message">{{ __('app.tenant_complaint_message') }}</label>
            <textarea name="message" id="message" rows="4" class="form-control" required>{{ old('message') }}</textarea>
          </div>
          <div class="form-group mb-3">
            <label for="attachment">{{ __('app.tenant_complaint_attachment') }}</label>
            <input type="file" name="attachment" id="attachment" class="form-control-file" accept="image/*">
            <small class="form-text text-muted">{{ __('app.tenant_complaint_attachment_hint') }}</small>
          </div>
          <button type="submit" class="btn btn-primary tenant-action-btn">
            <i class="mdi mdi-send"></i>
            <span>{{ __('app.tenant_complaint_send') }}</span>
          </button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.tenant_complaint_list_title') }}</h4>
        @if($complaints->isEmpty())
          <p class="text-muted mb-0">{{ __('app.tenant_complaint_list_empty') }}</p>
        @else
          <div class="table-responsive">
            <table class="table tenant-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{{ __('app.tenant_complaint_table_subject') }}</th>
                  <th>{{ __('app.tenant_complaint_table_status') }}</th>
                  <th>{{ __('app.tenant_complaint_table_created') }}</th>
                  <th>{{ __('app.tenant_complaint_table_reply') }}</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($complaints as $complaint)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
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
                      <a href="{{ route('tenant.subdomain.complaints.show', ['subdomain' => $sub, 'complaint' => $complaint->id]) }}" class="btn btn-sm btn-outline-primary tenant-action-btn">
                        <i class="mdi mdi-eye-outline"></i>
                        <span>{{ __('app.view_details') }}</span>
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
