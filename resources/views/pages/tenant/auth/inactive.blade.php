@extends('layout.master-mini')

@section('content')
<div class="content-wrapper">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="alert alert-warning mt-4" role="alert">
        <h4 class="alert-heading">الحساب غير مُفعل</h4>
        <p>المنشأة ({{ $subdomain }}) غير مُفعلة حالياً. برجاء إتمام عملية الاشتراك/الدفع لتفعيل الحساب ثم إعادة المحاولة.</p>
        <hr>
        <p class="mb-0">للمتابعة، ارجع إلى الصفحة الرئيسية ثم أكمل التسجيل.</p>
      </div>
      <a href="{{ url(app()->getLocale().'/') }}" class="btn btn-primary">العودة للصفحة الرئيسية</a>
    </div>
  </div>
</div>
@endsection
