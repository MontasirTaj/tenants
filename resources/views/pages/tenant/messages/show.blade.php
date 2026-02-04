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
          <h2 class="tenant-page-header-title">
            {{ $conversation->type === 'group' ? ($conversation->title ?: __('app.messages_group')) : __('app.messages_direct') }}
          </h2>
          <p class="tenant-page-header-subtitle">
            {{ __('app.messages_participants') }}: {{ $conversation->participants->pluck('user.name')->implode(', ') }}
          </p>
        </div>
        <div class="tenant-page-header-actions mt-3 mt-md-0">
          <a href="{{ route('tenant.subdomain.messages.index', ['subdomain' => $sub]) }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-arrow-left"></i>
            <span>{{ __('app.back') }}</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-10 mx-auto">
    <div class="card mb-4">
      <div class="card-body" style="max-height: 420px; overflow-y: auto;">
        @forelse($conversation->messages as $message)
          <div class="d-flex mb-3 {{ $message->sender_id === auth('tenant')->id() ? 'justify-content-end' : 'justify-content-start' }}">
            <div class="p-2 rounded" style="max-width: 70%; background-color: {{ $message->sender_id === auth('tenant')->id() ? 'rgba(16,44,79,0.08)' : '#f3f4f6' }};">
              <div class="small fw-bold mb-1">{{ $message->sender?->name }}</div>
              <div class="small">{{ $message->body }}</div>
              <div class="text-muted mt-1" style="font-size: 0.75rem;">{{ $message->created_at?->format('Y-m-d H:i') }}</div>
            </div>
          </div>
        @empty
          <p class="text-muted mb-0">{{ __('app.messages_empty_conversation') }}</p>
        @endforelse
      </div>
      <div class="card-footer">
        <form method="POST" action="{{ route('tenant.subdomain.messages.store', ['subdomain' => $sub, 'conversation' => $conversation->id]) }}">
          @csrf
          <div class="input-group">
            <input type="text" name="body" class="form-control" placeholder="{{ __('app.messages_placeholder') }}" required>
            <button class="btn btn-primary tenant-action-btn" type="submit">
              <i class="mdi mdi-send"></i>
              <span>{{ __('app.send') }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
