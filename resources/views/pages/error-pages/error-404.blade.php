@extends('layout.master-mini')

@section('content')
<div class="content-wrapper">
  <div class="container py-5 text-center">
    <h1 class="display-4">404</h1>
    <p class="lead">{{ __('Page not found') }}</p>
    <p class="text-muted">{{ __('The page you are looking for does not exist or has been moved.') }}</p>
    <a href="{{ Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale(), '/') }}" class="btn btn-primary mt-3">{{ __('Go Home') }}</a>
  </div>
</div>
@endsection
