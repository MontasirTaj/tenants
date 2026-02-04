@extends('layout.master')

@section('content')
<div class="row">
  <div class="col-md-6 mx-auto">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.edit') }} - {{ __('app.roles') }}</h4>
        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">{{ __('app.roles') }}</label>
            <input type="text" name="name" value="{{ old('name', $role->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('app.description') ?? 'الوصف' }}</label>
            <input type="text" name="description" value="{{ old('description', $role->description) }}" class="form-control @error('description') is-invalid @enderror">
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('app.permissions') }}</label>
            <select name="permissions[]" class="form-control" multiple size="8">
              @foreach($permissions as $permission)
                <option value="{{ $permission->id }}" @selected(in_array($permission->id, $assigned))>{{ $permission->name }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-primary">{{ __('app.save_changes') }}</button>
          <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary ms-2">{{ __('app.cancel') }}</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
