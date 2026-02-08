@extends('layout.master')

@section('content')
    @php
        $sub = request()->route('subdomain');
    @endphp

    <div class="row tenant-page-header">
        <div class="col-xl-10 mx-auto">
            <div class="card tenant-page-header-card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h2 class="tenant-page-header-title">{{ __('app.roles_permissions_title') }}</h2>
                        <p class="tenant-page-header-subtitle">{{ __('app.roles_permissions_subtitle') }}</p>
                    </div>
                    <div class="tenant-page-header-actions mt-3 mt-md-0">
                        <a href="{{ route('tenant.subdomain.dashboard', ['subdomain' => $sub]) }}"
                            class="btn btn-outline-primary">
                            <i class="mdi mdi-view-dashboard-outline"></i>
                            <span>{{ __('app.tenant_panel') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-10 mx-auto">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="mb-0">{{ __('app.roles_title') }}</h4>
                                <a href="{{ route('tenant.subdomain.roles.with-permissions', ['subdomain' => request()->route('subdomain')]) }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    {{ __('app.roles_with_permissions_link') }}
                                </a>
                            </div>
                            <p class="text-muted mb-3">{{ __('app.roles_intro') }}</p>
                            <form method="POST"
                                action="{{ route('tenant.subdomain.roles.store', ['subdomain' => request()->route('subdomain')]) }}"
                                class="mb-3">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">{{ __('app.role_name') }}</label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="{{ __('app.role_name_placeholder') }}" required>
                                </div>
                                <button class="btn btn-primary tenant-action-btn">
                                    <i class="mdi mdi-plus-circle-outline"></i>
                                    <span>{{ __('app.add') }}</span>
                                </button>
                            </form>
                            <ul class="list-group">
                                @foreach ($roles as $role)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $role->name }}</span>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('tenant.subdomain.roles.edit', ['subdomain' => $sub, 'role' => $role->id]) }}"
                                                class="btn btn-sm btn-outline-primary tenant-action-btn">
                                                <i class="mdi mdi-pencil-outline"></i>
                                                <span>{{ __('app.edit') }}</span>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('tenant.subdomain.roles.destroy', ['subdomain' => $sub, 'role' => $role->id]) }}"
                                                onsubmit="return confirm('{{ __('app.confirm_delete_role') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger tenant-action-btn"
                                                    type="submit">
                                                    <i class="mdi mdi-delete-outline"></i>
                                                    <span>{{ __('app.delete') }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="mb-2">{{ __('app.attach_permission_to_role') }}</h4>
                            <p class="text-muted mb-3">{{ __('app.attach_permission_to_role_intro') }}</p>
                            <form method="POST"
                                action="{{ route('tenant.subdomain.roles.attach', ['subdomain' => request()->route('subdomain')]) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">{{ __('app.choose_role') }}</label>
                                    <select name="role_id" class="form-control" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('app.choose_permission') }}</label>
                                    <select name="permission_id" class="form-control" required>
                                        @foreach ($permissions as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-primary tenant-action-btn">
                                    <i class="mdi mdi-link-variant"></i>
                                    <span>{{ __('app.attach') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="mb-2">{{ __('app.roles_permissions_title') }}</h4>
                            <p class="text-muted mb-3">{{ __('app.roles_permissions_subtitle') }}</p>
                            <div class="table-responsive tenant-table-wrapper">
                                <table class="table table-striped align-middle tenant-table"
                                    id="tenant-roles-permissions-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%">{{ __('app.role') }}</th>
                                            <th>{{ __('app.permissions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($roles as $role)
                                            <tr>
                                                <td>{{ $role->name }}</td>
                                                <td>
                                                    @if ($role->permissions->isEmpty())
                                                        <span class="text-muted">{{ __('app.permissions_list') }}</span>
                                                    @else
                                                        @foreach ($role->permissions as $permission)
                                                            <span
                                                                class="badge bg-primary me-1 mb-1">{{ $permission->name }}</span>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">{{ __('app.none') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ...
    </div>
@endsection

@push('plugin-styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('custom-scripts')
    <script>
        $(function() {
            $('#tenant-roles-permissions-table').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, '{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}']
                ],
                ordering: true,
                language: {
                    url: '{{ app()->getLocale() === 'ar' ? 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json' : 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/en-GB.json' }}'
                }
            });
        });
    </script>
@endpush
