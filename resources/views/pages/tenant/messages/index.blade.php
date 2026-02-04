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
          <h2 class="tenant-page-header-title">{{ __('app.messages_title') }}</h2>
          <p class="tenant-page-header-subtitle">{{ __('app.messages_subtitle') }}</p>
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
  <div class="col-xl-10 mx-auto">
    <div class="card mb-4">
      <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
          <h4 class="mb-1">{{ __('app.messages_overview_title') }}</h4>
          <p class="text-muted mb-0">{{ __('app.messages_overview_subtitle') }}</p>
        </div>
        <div class="mt-3 mt-md-0 text-md-end">
          <div class="small text-muted">
            <span class="me-3">{{ __('app.messages_total_conversations') }}: {{ $stats['total_conversations'] }}</span>
            <span>{{ __('app.messages_total_messages') }}: {{ $stats['total_messages'] }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">{{ __('app.messages_list_title') }}</h5>
        @if($conversations->isEmpty())
          <p class="text-muted mb-0">{{ __('app.messages_empty') }}</p>
        @else
          <div class="list-group">
            @foreach($conversations as $conv)
              @php
                $lastMessage = $conv->messages->sortByDesc('created_at')->first();
                $participantNames = $conv->participants->pluck('user.name')->implode(', ');
              @endphp
              <a href="{{ route('tenant.subdomain.messages.show', ['subdomain' => $sub, 'conversation' => $conv->id]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                <div class="me-3">
                  <div class="fw-bold">
                    {{ $conv->type === 'group' ? ($conv->title ?: __('app.messages_group')) : __('app.messages_direct') }}
                  </div>
                  <div class="small text-muted">
                    {{ $participantNames }}
                  </div>
                  @if($lastMessage)
                    <div class="small mt-1 text-truncate" style="max-width: 420px;">
                      {{ $lastMessage->body }}
                    </div>
                  @endif
                </div>
                <div class="text-end small text-muted">
                  @if($lastMessage && $lastMessage->created_at)
                    <div>{{ $lastMessage->created_at->diffForHumans() }}</div>
                  @endif
                </div>
              </a>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
