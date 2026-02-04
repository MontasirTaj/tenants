@extends('layout.master')

@section('content')
<div class="row">
  <div class="col-md-6 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.edit') }} - {{ __('app.permissions') }}</h4>
        <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">{{ __('app.permissions') }}</label>
            <input type="text" name="name" value="{{ old('name', $permission->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('app.description') ?? 'الوصف' }}</label>
            <input type="text" name="description" value="{{ old('description', $permission->description) }}" class="form-control @error('description') is-invalid @enderror">
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn btn-primary">{{ __('app.save_changes') }}</button>
          <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary ms-2">{{ __('app.cancel') }}</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
