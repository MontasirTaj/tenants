@extends('layout.master')

@section('content')
<div class="row">
  <div class="col-md-6 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.edit') }} - {{ __('app.users') }}</h4>
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">{{ __('app.name') }}</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('app.email') }}</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('app.password_optional') }}</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="********">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('app.roles') }}</label>
            <select name="role_id" class="form-control">
              <option value="">-</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected($currentRoleId == $role->id)>{{ $role->name }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-primary">{{ __('app.save_changes') }}</button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">{{ __('app.cancel') }}</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
