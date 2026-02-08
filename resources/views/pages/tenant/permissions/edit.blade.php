@extends('layout.master')

@section('content')
    @php
        $sub = request()->route('subdomain');
    @endphp

    <div class="row tenant-page-header">
        <div class="col-xl-8 mx-auto">
            <div class="card tenant-page-header-card">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h2 class="tenant-page-header-title">{{ __('app.permissions_title') }}</h2>
                        <p class="tenant-page-header-subtitle">تعديل الصلاحية</p>
                    </div>
                    <div class="tenant-page-header-actions mt-3 mt-md-0">
                        <a href="{{ route('tenant.subdomain.permissions.index', ['subdomain' => $sub]) }}"
                            class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-right-bold-circle-outline"></i>
                            <span>{{ __('app.back') ?? 'رجوع' }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">تعديل الصلاحية</h4>
                    <form method="POST"
                        action="{{ route('tenant.subdomain.permissions.update', ['subdomain' => $sub, 'permission' => $permission->id]) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">{{ __('app.permission_name') }}</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $permission->name) }}" required>
                        </div>
                        <button class="btn btn-primary tenant-action-btn">
                            <i class="mdi mdi-content-save-outline"></i>
                            <span>{{ __('app.save') ?? 'حفظ' }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
