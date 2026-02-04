@extends('layout.master')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
          <h3 class="mb-1">{{ __('app.admin_complaint_detail_title') }}</h3>
          <p class="text-muted mb-0">{{ __('app.admin_complaint_detail_subtitle') }}</p>
        </div>
        <div class="mt-3 mt-md-0">
          <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left"></i> {{ __('app.back_to_list') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-7 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <h4 class="mb-3">{{ $complaint->subject }}</h4>
        <p class="text-muted mb-2">{{ __('app.admin_complaint_tenant') }}: {{ optional($complaint->tenant)->TenantName ?? '-' }}</p>
        @if(optional($complaint->tenant)->Subdomain)
          <p class="text-muted mb-2">{{ __('app.admin_complaint_tenant_domain') }}: {{ optional($complaint->tenant)->Subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}</p>
        @endif
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
  </div>

  <div class="col-md-5 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.admin_complaint_reply_title') }}</h4>

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

        <form action="{{ route('admin.complaints.reply', $complaint) }}" method="POST">
          @csrf
          <div class="form-group mb-3">
            <label for="admin_reply">{{ __('app.admin_complaint_reply_label') }}</label>
            <textarea name="admin_reply" id="admin_reply" rows="5" class="form-control" required>{{ old('admin_reply', $complaint->admin_reply) }}</textarea>
          </div>
          <div class="form-group mb-3">
            <label for="status">{{ __('app.admin_complaint_status_after_reply') }}</label>
            <select name="status" id="status" class="form-control">
              <option value="">{{ __('app.keep_as_is') }}</option>
              <option value="open" @if(old('status', $complaint->status) === 'open') selected @endif>{{ __('app.tenant_complaint_status_open') }}</option>
              <option value="in_progress" @if(old('status', $complaint->status) === 'in_progress') selected @endif>{{ __('app.tenant_complaint_status_in_progress') }}</option>
              <option value="closed" @if(old('status', $complaint->status) === 'closed') selected @endif>{{ __('app.tenant_complaint_status_closed') }}</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="mdi mdi-reply"></i> {{ __('app.admin_complaint_reply_save_button') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
