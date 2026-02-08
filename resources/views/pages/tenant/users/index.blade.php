@extends('layout.master')

@section('content')
    @php
        $isSub = Illuminate\Support\Str::startsWith(Route::currentRouteName(), 'tenant.subdomain.');
        $prefix = $isSub ? 'tenant.subdomain' : 'tenant';
        $sub = request()->route('subdomain');
    @endphp

    <div class="row tenant-page-header">
        <div class="col-xl-10 mx-auto">
            <div class="card tenant-page-header-card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h2 class="tenant-page-header-title">{{ __('app.tenant_users_title') }}</h2>
                        <p class="tenant-page-header-subtitle">{{ __('app.tenant_users_subtitle') }}</p>
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
                            <h4 class="mb-2">{{ __('app.tenant_users_create_title') }}</h4>
                            <p class="text-muted mb-3">{{ __('app.tenant_users_create_intro') }}</p>
                            @if (session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="POST"
                                action="{{ route($prefix . '.users.store', ['subdomain' => request()->route('subdomain')]) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">{{ __('app.name') }}</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('app.email') }}</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('app.password') }}</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('app.role') }}</label>
                                    <select name="role_id" class="form-control">
                                        <option value="">{{ __('app.none') }}</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                                {{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-primary tenant-action-btn">
                                    <i class="mdi mdi-account-plus-outline"></i>
                                    <span>{{ __('app.create') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-2">
                                <div>
                                    <h4 class="mb-1">{{ __('app.tenant_users_list_title') }}</h4>
                                    <p class="text-muted mb-0">{{ __('app.tenant_users_list_intro') }}</p>
                                </div>
                                <div class="mt-3 mt-md-0">
                                    <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                                        <div class="btn-group btn-group-sm" role="group"
                                            aria-label="{{ __('app.export') }}">
                                            <a href="{{ route($prefix . '.users.export.excel', ['subdomain' => $sub]) }}"
                                                class="btn btn-outline-success">
                                                <i class="mdi mdi-file-excel-outline"></i>
                                                <span>{{ __('app.export_excel') }}</span>
                                            </a>
                                            <a href="{{ route($prefix . '.users.export.pdf', ['subdomain' => $sub]) }}"
                                                class="btn btn-outline-danger">
                                                <i class="mdi mdi-file-pdf-box"></i>
                                                <span>{{ __('app.export_pdf') }}</span>
                                            </a>
                                        </div>
                                        <button type="button" id="toggle-users-view"
                                            class="btn btn-sm btn-outline-secondary tenant-action-btn">
                                            <i class="mdi mdi-view-agenda-outline"></i>
                                            <span>عرض كبطاقات</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="tenant-users-table-wrapper" class="table-responsive tenant-table-wrapper">
                                <table class="table table-striped table-hover align-middle tenant-table"
                                    id="tenant-users-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('app.name') }}</th>
                                            <th>{{ __('app.email') }}</th>
                                            <th>{{ __('app.roles') }}</th>
                                            <th class="text-center" style="width: 120px;">{{ __('app.last_activity') }}
                                            </th>
                                            <th class="text-center">{{ __('app.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $u)
                                            <tr>
                                                <td>{{ $u->name }}</td>
                                                <td>{{ $u->email }}</td>
                                                <td>{{ implode(', ', $u->getRoleNames()->toArray()) }}</td>
                                                <td class="text-center">
                                                    @if (!empty($u->last_activity_at))
                                                        @php
                                                            $last = \Illuminate\Support\Carbon::parse(
                                                                $u->last_activity_at,
                                                            );
                                                        @endphp
                                                        <div>{{ $last->format('Y-m-d') }}</div>
                                                        <div class="text-muted small">{{ $last->format('H:i') }}</div>
                                                    @else
                                                        <span class="text-muted">&#8212;</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route($prefix . '.users.edit', ['subdomain' => request()->route('subdomain'), 'user' => $u->id]) }}"
                                                        class="btn btn-sm btn-outline-primary tenant-action-btn">
                                                        <i class="mdi mdi-pencil-outline"></i>
                                                        <span>{{ __('app.edit') }}</span>
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route($prefix . '.users.destroy', ['subdomain' => request()->route('subdomain'), 'user' => $u->id]) }}"
                                                        class="d-inline-block"
                                                        onsubmit="return confirm('{{ __('app.confirm_delete_user') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger tenant-action-btn"
                                                            type="submit">
                                                            <i class="mdi mdi-delete-outline"></i>
                                                            <span>{{ __('app.delete') }}</span>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div id="tenant-users-cards" class="row g-3 d-none mt-1">
                                @foreach ($users as $u)
                                    <div class="col-12 col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div
                                                    class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-1">
                                                    <div>
                                                        <h5 class="mb-1">{{ $u->name }}</h5>
                                                        <div class="text-muted small">{{ $u->email }}</div>
                                                    </div>
                                                    <div class="text-nowrap">
                                                        <a href="{{ route($prefix . '.users.edit', ['subdomain' => request()->route('subdomain'), 'user' => $u->id]) }}"
                                                            class="btn btn-sm btn-outline-primary tenant-action-btn mb-1">
                                                            <i class="mdi mdi-pencil-outline"></i>
                                                            <span>{{ __('app.edit') }}</span>
                                                        </a>
                                                        <form method="POST"
                                                            action="{{ route($prefix . '.users.destroy', ['subdomain' => request()->route('subdomain'), 'user' => $u->id]) }}"
                                                            class="d-inline-block mb-1"
                                                            onsubmit="return confirm('{{ __('app.confirm_delete_user') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger tenant-action-btn"
                                                                type="submit">
                                                                <i class="mdi mdi-delete-outline"></i>
                                                                <span>{{ __('app.delete') }}</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="small text-muted mb-1">
                                                    <strong>{{ __('app.roles') }}:</strong>
                                                    {{ implode(', ', $u->getRoleNames()->toArray()) ?: __('app.none') }}
                                                </div>
                                                <div class="small text-muted">
                                                    <strong>{{ __('app.last_activity') }}:</strong>
                                                    @if (!empty($u->last_activity_at))
                                                        @php
                                                            $last = \Illuminate\Support\Carbon::parse(
                                                                $u->last_activity_at,
                                                            );
                                                        @endphp
                                                        <span>{{ $last->format('Y-m-d') }}</span><br>
                                                        <span class="text-muted">{{ $last->format('H:i') }}</span>
                                                    @else
                                                        <span class="text-muted">&#8212;</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                @if (isset($inactiveUsers) && $inactiveUsers->count())
                    <div class="col-12 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-2">
                                    <div>
                                        <h4 class="mb-1">
                                            {{ __('app.inactive_users_title', ['days' => $inactiveThresholdDays ?? 30]) }}
                                        </h4>
                                        <p class="text-muted mb-0">
                                            {{ __('app.inactive_users_intro', ['days' => $inactiveThresholdDays ?? 30]) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="table-responsive tenant-table-wrapper">
                                    <table class="table table-sm table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>{{ __('app.name') }}</th>
                                                <th>{{ __('app.email') }}</th>
                                                <th class="text-center" style="width: 120px;">
                                                    {{ __('app.last_activity') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($inactiveUsers as $u)
                                                <tr>
                                                    <td>{{ $u->name }}</td>
                                                    <td>{{ $u->email }}</td>
                                                    <td class="text-center">
                                                        @if (!empty($u->last_activity_at))
                                                            @php
                                                                $last = \Illuminate\Support\Carbon::parse(
                                                                    $u->last_activity_at,
                                                                );
                                                            @endphp
                                                            <div>{{ $last->format('Y-m-d') }}</div>
                                                            <div class="text-muted small">{{ $last->format('H:i') }}</div>
                                                        @else
                                                            <span class="text-muted">&#8212;</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
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
            $('#tenant-users-table').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, '{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}']
                ],
                ordering: true,
                scrollX: true,
                language: {
                    url: '{{ app()->getLocale() === 'ar' ? 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/ar.json' : 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/en-GB.json' }}'
                }
            });

            let usersViewIsCards = false;
            $('#toggle-users-view').on('click', function() {
                usersViewIsCards = !usersViewIsCards;
                $('#tenant-users-table-wrapper').toggleClass('d-none', usersViewIsCards);
                $('#tenant-users-cards').toggleClass('d-none', !usersViewIsCards);

                const $icon = $(this).find('i');
                const $text = $(this).find('span');
                if (usersViewIsCards) {
                    $icon.removeClass('mdi-view-agenda-outline').addClass('mdi-table');
                    $text.text('عرض كجدول');
                } else {
                    $icon.removeClass('mdi-table').addClass('mdi-view-agenda-outline');
                    $text.text('عرض كبطاقات');
                }
            });
        });
    </script>
@endpush
