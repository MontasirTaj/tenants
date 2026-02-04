@extends('layout.master-mini')

@section('content')
<div class="content-wrapper">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h3 class="mb-3">{{ __('app.tenant_panel') }} ({{ $subdomain }})</h3>
          @if(session('login_error'))
            <div class="alert alert-danger">
              {{ session('login_error') }}
            </div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger">
              {{ $errors->first('email') }}
            </div>
          @endif
          @php
            $isSub = \Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'tenant.subdomain.');
            $prefix = $isSub ? 'tenant.subdomain' : 'tenant';
          @endphp
          <form method="POST" action="{{ route($prefix.'.login.post', ['subdomain' => $subdomain]) }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">{{ __('app.email') }}</label>
              <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('app.password') }}</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-check mb-3">
              <input type="checkbox" name="remember" class="form-check-input" id="remember">
              <label class="form-check-label" for="remember">{{ __('app.remember_me') }}</label>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('app.login') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
