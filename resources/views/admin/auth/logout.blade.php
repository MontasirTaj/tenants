@extends('layout.master-mini')

@section('content')
<div class="content-wrapper">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card">
        <div class="card-body text-center">
          <h3 class="mb-3">{{ __('app.logout') }}</h3>
          <p class="mb-4">{{ __('لقد تم تسجيل خروجك بنجاح.') }}</p>
          <a href="{{ route('admin.login') }}" class="btn btn-primary">{{ __('app.login') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
