@extends('layout.master')

@section('content')
@php
  $isSub = Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'tenant.subdomain.');
  $prefix = $isSub ? 'tenant.subdomain' : 'tenant';
  $subdomain = request()->route('subdomain');
@endphp

<div class="row">
  <div class="col-12 mb-3 tenant-page-header">
    <div class="card tenant-page-header-card">
      <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <div>
          <h4 class="tenant-page-header-title mb-1">{{ __('app.change_password') }}</h4>
          <p class="tenant-page-header-subtitle mb-0">{{ __('app.change_password_subtitle') }}</p>
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

  <div class="col-lg-7 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3">{{ __('app.password_section_title') }}</h5>

        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if (session('status'))
          <div class="alert alert-success mb-3">
            {{ session('status') }}
          </div>
        @endif

        <form method="POST" action="{{ route($prefix.'.password.update', ['subdomain' => $subdomain]) }}">
          @csrf

          <div class="form-group">
            <label for="current_password">{{ __('app.current_password') }}</label>
            <input id="current_password" type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
          </div>

          <div class="form-group">
            <label for="password">{{ __('app.new_password') }}</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
          </div>

          <div class="form-group">
            <label for="password_confirmation">{{ __('app.confirm_new_password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
          </div>

          <button type="submit" class="btn btn-primary mt-2">
            <i class="mdi mdi-lock-reset mr-1"></i> {{ __('app.save_changes') }}
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-5 grid-margin stretch-card">
    <div class="card">
      <div class="card-body text-center">
        <h5 class="card-title mb-3">{{ __('app.avatar_section_title') }}</h5>
        @php
          $avatar = $user->avatar ?? null;
          $avatarUrl = $avatar
            ? asset('storage/' . $avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=102c4f&color=fff&rounded=true&size=96';
        @endphp
        <div class="mb-3">
          <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle" style="width: 96px; height: 96px; object-fit: cover;">
        </div>

        <form method="POST" action="{{ route($prefix.'.password.update', ['subdomain' => $subdomain]) }}" enctype="multipart/form-data">
          @csrf
          <div class="form-group text-left">
            <label for="avatar">{{ __('app.avatar_label') }}</label>
            <input id="avatar" type="file" name="avatar" class="form-control-file @error('avatar') is-invalid @enderror" accept="image/*">
            @error('avatar')
              <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
            @enderror
          </div>
          <button type="submit" class="btn btn-outline-primary btn-sm mt-2">
            <i class="mdi mdi-image-edit-outline mr-1"></i> {{ __('app.update_avatar') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
