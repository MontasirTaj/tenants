@extends('layout.master-mini')

@section('content')
<div class="content-wrapper">
  <div class="container py-5 text-center">
    <h1 class="mb-3">تم إلغاء العملية</h1>
    <p class="text-muted">لم يتم تحصيل أي مدفوعات. يمكنك إعادة المحاولة لاحقاً.</p>
    <a href="{{ route('tenants.plans') }}" class="btn btn-primary mt-3">العودة لاختيار الباقة</a>
  </div>
</div>
@endsection
