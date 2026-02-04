@extends('layout.master-mini')

@section('content')
<div class="content-wrapper">
  <div class="container py-5 text-center">
    <h1 class="display-4">500</h1>
    <p class="lead">{{ __('Server Error') }}</p>
    <p class="text-muted">{{ __('Something went wrong on our end. Please try again later.') }}</p>
    <a href="{{ Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale(), '/') }}" class="btn btn-primary mt-3">{{ __('Go Home') }}</a>
  </div>
</div>
@endsection
