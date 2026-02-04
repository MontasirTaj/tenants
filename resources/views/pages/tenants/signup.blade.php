@extends('layout.master-mini')

@section('content')
<div class="content-wrapper">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <h3 class="mb-3">تسجيل بيانات المنشأة - باقة {{ $planData['name'] }}</h3>
          <form method="POST" action="{{ route('tenants.store') }}" novalidate>
            @csrf
            <input type="hidden" name="Plan" value="{{ $plan }}">

            <div class="mb-3">
              <label class="form-label">اسم المنشأة *</label>
              <input type="text" name="TenantName" value="{{ old('TenantName') }}" class="form-control @error('TenantName') is-invalid @enderror" required maxlength="255">
              @error('TenantName')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label class="form-label">اسم المالك</label>
              <input type="text" name="OwnerName" value="{{ old('OwnerName') }}" class="form-control @error('OwnerName') is-invalid @enderror" maxlength="255">
              @error('OwnerName')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label class="form-label">البريد الإلكتروني</label>
              <input type="email" name="Email" value="{{ old('Email') }}" class="form-control @error('Email') is-invalid @enderror" maxlength="255">
              @error('Email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label class="form-label">رقم الجوال</label>
              <input type="text" name="PhoneNumber" value="{{ old('PhoneNumber') }}" class="form-control @error('PhoneNumber') is-invalid @enderror" maxlength="20" placeholder="مثال: +966512345678">
              @error('PhoneNumber')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label class="form-label">العنوان</label>
              <textarea name="Address" class="form-control @error('Address') is-invalid @enderror" maxlength="500" rows="2">{{ old('Address') }}</textarea>
              @error('Address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <a href="{{ route('tenants.plans') }}" class="btn btn-light">رجوع</a>
              <button type="submit" class="btn btn-primary">متابعة إلى الدفع</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
