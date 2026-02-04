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
          <h2 class="tenant-page-header-title">{{ __('app.tenant_complaint_detail_title') }}</h2>
          <p class="tenant-page-header-subtitle">{{ __('app.tenant_complaint_detail_subtitle') }}</p>
        </div>
        <div class="tenant-page-header-actions mt-3 mt-md-0">
          <a href="{{ route('tenant.subdomain.complaints.index', ['subdomain' => $sub]) }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left"></i>
            <span>{{ __('app.back_to_list') }}</span>
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
        <h4 class="mb-3">{{ $complaint->subject }}</h4>
        <p class="text-muted mb-2">{{ __('app.tenant_complaint_created_at') }}: {{ optional($complaint->created_at)->format('Y-m-d H:i') }}</p>
        <p class="text-muted mb-2">{{ __('app.tenant_complaint_status_label') }}:
          @if($complaint->status === 'closed')
            <span class="badge badge-success">{{ __('app.tenant_complaint_status_closed') }}</span>
          @elseif($complaint->status === 'in_progress')
            <span class="badge badge-warning">{{ __('app.tenant_complaint_status_in_progress') }}</span>
          @else
            <span class="badge badge-info">{{ __('app.tenant_complaint_status_open') }}</span>
          @endif
        </p>
        <hr>
        <h5 class="mb-2">{{ __('app.tenant_complaint_message_label') }}</h5>
        <p>{{ $complaint->message }}</p>

        @if($complaint->attachment_path)
          <hr>
          <h5 class="mb-2">{{ __('app.tenant_complaint_attachment_label') }}</h5>
          <a href="{{ asset('storage/'.$complaint->attachment_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="mdi mdi-image"></i> {{ __('app.tenant_complaint_view_attachment') }}
          </a>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.tenant_complaint_reply_title') }}</h4>
        @if($complaint->admin_reply)
          <p class="text-muted mb-2">{{ __('app.tenant_complaint_reply_at') }}: {{ optional($complaint->admin_replied_at)->format('Y-m-d H:i') }}</p>
          <p>{{ $complaint->admin_reply }}</p>
        @else
          <p class="text-muted mb-0">{{ __('app.tenant_complaint_reply_pending') }}</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
