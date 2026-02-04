@extends('layout.master')

@push('plugin-styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row mb-3">
  <div class="col-md-8 col-lg-7 col-xl-6 mx-auto">
    <div class="card mb-4">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.roles') }} - {{ __('app.create') }}</h4>
        @if(session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('admin.roles.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">{{ __('app.roles') }}</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">{{ __('app.description') ?? 'الوصف' }}</label>
            <input type="text" name="description" value="{{ old('description') }}" class="form-control @error('description') is-invalid @enderror">
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn btn-primary">{{ __('app.create') }}</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3">{{ __('app.roles') }}</h4>
        <div class="table-responsive tenant-table-wrapper">
          <table id="admin-roles-table" class="table table-striped table-bordered tenant-table">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ __('app.roles') }}</th>
                <th>{{ __('app.description') ?? 'الوصف' }}</th>
                <th>{{ __('app.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($roles as $role)
                <tr>
                  <td>{{ $role->id }}</td>
                  <td>{{ $role->name }}</td>
                  <td>{{ $role->description }}</td>
                  <td>
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">{{ __('app.edit') }}</a>
                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" style="display:inline-block" onsubmit="return confirm('{{ __('app.delete') }}?');">
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
    $('#admin-roles-table').DataTable({
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
