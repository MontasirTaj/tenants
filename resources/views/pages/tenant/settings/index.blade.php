@extends('layout.master')

@section('content')
@php
  $isSub = Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'tenant.subdomain.');
  $prefix = $isSub ? 'tenant.subdomain' : 'tenant';
  $subdomain = request()->route('subdomain');
@endphp

<div class="row tenant-page-header">
  <div class="col-xl-10 mx-auto">
    <div class="card tenant-page-header-card">
      <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
          <h2 class="tenant-page-header-title">{{ __('app.tenant_settings_title') }}</h2>
          <p class="tenant-page-header-subtitle">{{ __('app.tenant_settings_subtitle') }}</p>
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
</div>

<div class="row">
  <div class="col-xl-10 mx-auto">
    <div class="row">
      <div class="col-lg-7 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h4 class="mb-3">{{ __('app.tenant_branding_title') }}</h4>
            <p class="text-muted mb-3">{{ __('app.tenant_branding_intro') }}</p>

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

            <form method="POST" action="{{ route($prefix.'.settings.update', ['subdomain' => $subdomain]) }}" enctype="multipart/form-data">
              @csrf

              <div class="form-group mb-3">
                <label for="name">{{ __('app.tenant_name_label') }}</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $setting->name) }}" placeholder="{{ __('app.tenant_name_placeholder') }}">
              </div>

              <div class="form-group mb-3">
                <label for="primary_color">{{ __('app.tenant_color_label') }}</label>
                <div class="d-flex align-items-center gap-2">
                  <input type="color" id="primary_color" name="primary_color" value="{{ old('primary_color', $setting->primary_color ?? '#102c4f') }}" style="width: 52px; height: 36px; padding: 0; border-radius: 8px;">
                  <input type="text" class="form-control ml-2" value="{{ old('primary_color', $setting->primary_color ?? '#102c4f') }}" oninput="document.getElementById('primary_color').value = this.value" placeholder="#102c4f">
                </div>
                <small class="form-text text-muted">{{ __('app.tenant_color_help') }}</small>
              </div>

              <div class="form-group mb-3">
                <label for="logo">{{ __('app.tenant_logo_label') }}</label>
                <input type="file" id="logo" name="logo" class="form-control-file" accept="image/*">
                <small class="form-text text-muted">{{ __('app.tenant_logo_help') }}</small>
              </div>

              <button type="submit" class="btn btn-primary mt-2">
                <i class="mdi mdi-content-save-outline mr-1"></i>
                {{ __('app.save_changes') }}
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-5 mb-4">
        <div class="card h-100">
          <div class="card-body text-center d-flex flex-column justify-content-center">
            <h5 class="mb-3">{{ __('app.tenant_preview_title') }}</h5>
            @php
              $logoUrl = $setting->logo_path ? asset('storage/' . $setting->logo_path) : asset('assets/images/logo-w.png');
              $color = $setting->primary_color ?? '#102c4f';
            @endphp
            <div class="mb-3">
              <div class="d-inline-flex align-items-center px-4 py-2 rounded" style="background: {{ $color }};">
                <img src="{{ $logoUrl }}" alt="Logo" style="height:32px; object-fit:contain;">
              </div>
            </div>
            <p class="text-muted mb-2">{{ __('app.tenant_preview_hint') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
