@extends('layout.master')

@section('content')
<div class="content-wrapper tenant-layout">
  <div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <h4 class="mb-0">إعدادات الباقات</h4>
      <span class="text-muted small">تحكم في عدد المستخدمين لكل باقة</span>
    </div>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.plans.update') }}">
        @csrf
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>رمز الباقة</th>
                <th>اسم الباقة</th>
                <th>الحد الأقصى لعدد المستخدمين</th>
              </tr>
            </thead>
            <tbody>
              @php($i = 1)
              @foreach($plans as $code => $plan)
                <tr>
                  <td>{{ $i++ }}</td>
                  <td><code>{{ $code }}</code></td>
                  <td>{{ $plan['label'] }}</td>
                  <td style="max-width:220px;">
                    <input
                      type="number"
                      name="plans[{{ $code }}][max_users]"
                      class="form-control @error('plans.'.$code.'.max_users') is-invalid @enderror"
                      min="1"
                      placeholder="بدون حد"
                      value="{{ old('plans.'.$code.'.max_users', $plan['limit']->max_users) }}"
                    >
                    @error('plans.'.$code.'.max_users')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">اتركه فارغًا للسماح بعدد غير محدود.</small>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3 text-right">
          <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
