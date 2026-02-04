@extends('layout.master')

@section('content')
<div class="content-wrapper tenant-layout">
  <div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-0">إدارة الباقات</h4>
        <span class="text-muted small">إنشاء وتعديل تفاصيل الباقات الظاهرة في الموقع</span>
      </div>
      <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary">
        <i class="mdi mdi-plus"></i> إضافة باقة جديدة
      </a>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>الكود</th>
              <th>الاسم (AR)</th>
              <th>Name (EN)</th>
              <th>السعر الشهري</th>
              <th>العملة</th>
              <th>نشط؟</th>
              <th>بارز؟</th>
              <th>ترتيب العرض</th>
              <th>إجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($plans as $index => $plan)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td><code>{{ $plan->code }}</code></td>
                <td>{{ $plan->name_ar }}</td>
                <td>{{ $plan->name_en }}</td>
                <td>{{ number_format($plan->price_monthly, 2) }}</td>
                <td>{{ $plan->currency }}</td>
                <td><span class="badge {{ $plan->is_active ? 'badge-success' : 'badge-danger' }}">{{ $plan->is_active ? 'نشطة' : 'متوقفة' }}</span></td>
                <td><span class="badge {{ $plan->is_featured ? 'badge-info' : 'badge-secondary' }}">{{ $plan->is_featured ? 'مميزة' : 'عادية' }}</span></td>
                <td>{{ $plan->sort_order }}</td>
                <td>
                  <a href="{{ route('admin.subscription-plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center text-muted">لا توجد باقات بعد.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
