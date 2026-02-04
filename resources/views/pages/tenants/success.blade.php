@extends('layout.master-mini')

@section('content')
<div class="content-wrapper">
  <div class="container py-5 text-center">
    <h1 class="mb-3">تم تفعيل حسابك بنجاح</h1>
    <p class="lead">مرحباً {{ $tenant->TenantName }} 🎉</p>
    <p class="text-muted">يمكنك الآن الدخول عبر النطاق: <strong>{{ $tenant->Subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}</strong></p>
    <p class="text-muted">بيانات قاعدة البيانات: <strong>{{ $tenant->DBName }}</strong></p>
    <a href="/" class="btn btn-primary mt-3">العودة للرئيسية</a>
  </div>
</div>
@endsection
