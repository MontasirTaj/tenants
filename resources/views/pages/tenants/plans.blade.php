@extends('layout.master')

@section('content')
<div class="row">
  <div class="col-12 mb-4">
    <h2 class="mb-2">اختر الباقة المناسبة</h2>
    <p class="text-muted">حدد الباقة ثم تابع تسجيل بيانات منشأتك.</p>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body text-center">
        <h4>مجانية</h4>
        <p class="text-muted">0 ريال / شهر</p>
        <a href="{{ route('tenants.signup', ['plan' => 'free']) }}" class="btn btn-primary">اختيار</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body text-center">
        <h4>احترافية</h4>
        <p class="text-muted">79 ريال / شهر</p>
        <a href="{{ route('tenants.signup', ['plan' => 'pro']) }}" class="btn btn-primary">اختيار</a>
      </div>
    </div>
  </div>
</div>
@endsection
