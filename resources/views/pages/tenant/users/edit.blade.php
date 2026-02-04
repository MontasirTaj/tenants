@extends('layout.master')

@section('content')
<div class="row">
  <div class="col-md-8 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">تعديل المستخدم</h4>
        @php
          $isSub = \Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'tenant.subdomain.');
          $prefix = $isSub ? 'tenant.subdomain' : 'tenant';
        @endphp
        <form method="POST" action="{{ route($prefix.'.users.update', ['subdomain' => request()->route('subdomain'), 'user' => $user->id]) }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label>الاسم</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
          </div>
          <div class="mb-3">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
          </div>
          <div class="mb-3">
            <label>كلمة المرور (اختياري)</label>
            <input type="password" name="password" class="form-control" placeholder="اتركه فارغًا بدون تغيير">
          </div>
          <div class="mb-3">
            <label>الدور</label>
            <select name="role_id" class="form-control">
              <option value="">بدون</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ $user->roles->contains('id', $role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary">حفظ</button>
            <a href="{{ route($prefix.'.users.index', ['subdomain' => request()->route('subdomain')]) }}" class="btn btn-light">رجوع</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
