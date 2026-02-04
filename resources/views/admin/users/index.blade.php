@extends('layout.master')

@push('plugin-styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row mb-3">
  <div class="col-md-8 col-lg-7 col-xl-6 mx-auto">
    <div class="card mb-4">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.users') }} - {{ __('app.create') }}</h4>
        @if(session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('admin.users.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">{{ __('app.name') }}</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('app.email') }}</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('app.password') }}</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('app.roles') }}</label>
            <select name="role_id" class="form-control">
              <option value="">-</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-primary">{{ __('app.create') }}</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.users') }}</h4>
        <div class="table-responsive tenant-table-wrapper">
          <table id="admin-users-table" class="table table-striped table-bordered tenant-table">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('app.name') }}</th>
                <th>{{ __('app.email') }}</th>
                <th>{{ __('app.roles') }}</th>
                <th>{{ __('app.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
                <tr>
                  <td>{{ $user->id }}</td>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td>{{ implode(', ', $user->adminRoles->pluck('name')->toArray()) }}</td>
                  <td>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">{{ __('app.edit') }}</a>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline-block" onsubmit="return confirm('{{ __('app.delete') }}?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('app.delete') }}</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('custom-scripts')
<script>
  $(function () {
    $('#admin-users-table').DataTable({
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, '{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}']],
      ordering: true,
      language: {
        url: '{{ app()->getLocale() === 'ar' ? 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json' : 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/en-GB.json' }}'
      }
    });
  });
</script>
@endpush
