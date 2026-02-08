<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    @php
        $defaultTitle = 'DataInsight';
        $pageTitle = $defaultTitle;
        try {
            if (\Illuminate\Support\Facades\Auth::guard('tenant')->check()) {
                // نحاول أولاً استخدام اسم المنشأة من إعدادات التينانت
                $tenantSetting = \App\Models\TenantSetting::query()->first();
                if ($tenantSetting && !empty($tenantSetting->name)) {
                    $pageTitle = $tenantSetting->name;
                } else {
                    // في حال عدم وجود إعدادات، نرجع لاسم التينانت من الجدول الرئيسي
                    $tenantUser = \Illuminate\Support\Facades\Auth::guard('tenant')->user();
                    $tenant = $tenantUser->tenant ?? null;
                    if ($tenant && !empty($tenant->TenantName)) {
                        $pageTitle = $tenant->TenantName;
                    }
                }
            }
        } catch (\Throwable $e) {
            $pageTitle = $defaultTitle;
        }
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="_token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/logo-w.png') }}">

    <!-- plugin css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/@mdi/font/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.css') }}">
    <!-- end plugin css -->

    @stack('plugin-styles')

    <!-- common css -->
    @if (app()->getLocale() === 'ar')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap"
            rel="stylesheet">
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @if (app()->getLocale() === 'ar')
        <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @endif
    <!-- end common css -->

    @php
        $defaultBrandColor = '#102c4f';
        $brandColor = $defaultBrandColor;
        try {
            if (\Illuminate\Support\Facades\Auth::guard('tenant')->check()) {
                $tenantSetting = \App\Models\TenantSetting::query()->first();
                if ($tenantSetting && $tenantSetting->primary_color) {
                    $brandColor = $tenantSetting->primary_color;
                }
            }
        } catch (\Throwable $e) {
            $brandColor = $defaultBrandColor;
        }
    @endphp

    @stack('style')
    <style>
        body {
            font-family: 'IBM Plex Sans Arabic', 'Tajawal', 'Instrument Sans', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .footer.di-footer {
            background: {{ $brandColor }};
            color: #ffffff;
            border-top: none;
            padding-top: .9rem;
            padding-bottom: .9rem;
            text-align: center;
        }

        .footer.di-footer span {
            color: #ffffff;
        }

        .footer.di-footer .small {
            color: rgba(255, 255, 255, 0.8);
            margin-left: .5rem;
        }

        /* Global navbar theming to match site color */
        .navbar.default-layout {
            background: {{ $brandColor }};
            border-bottom: none;
        }

        .navbar.default-layout .navbar-brand-wrapper {
            background: {{ $brandColor }};
        }

        .navbar.default-layout .navbar-brand img {
            background-color: {{ $brandColor }};
            padding: 4px 10px;
            border-radius: 4px;
        }

        .navbar.default-layout .navbar-menu-wrapper {
            background: {{ $brandColor }};
        }

        .navbar.default-layout .navbar-nav .nav-link,
        .navbar.default-layout .navbar-brand {
            color: #ffffff !important;
        }

        /* Sidebar icons & titles sizing + brand color */
        #sidebar .nav .nav-item .nav-link .menu-icon {
            font-size: 1.4rem;
            color: {{ $brandColor }};
        }

        #sidebar .nav .nav-item .nav-link .menu-title {
            font-size: 0.95rem;
            font-weight: 400;
        }

        /* Tenant dashboard & forms visual refresh */
        .tenant-layout .content-wrapper {
            background: #f5f7fb;
        }

        .tenant-layout .card {
            border-radius: 18px;
            border: 1px solid rgba(15, 37, 68, 0.08);
            box-shadow: 0 18px 35px rgba(15, 37, 68, 0.06);
        }

        .tenant-layout .card-header {
            border-bottom: 0;
            background: transparent;
        }

        .tenant-layout .tenant-dashboard-hero {
            background: linear-gradient(135deg, {{ $brandColor }} 0%, {{ $brandColor }} 60%, {{ $brandColor }} 100%);
            color: #ffffff;
            border: none;
            box-shadow: 0 22px 45px rgba(16, 44, 79, 0.5);
        }

        .tenant-layout .tenant-dashboard-hero .tenant-dashboard-title {
            font-size: 1.9rem;
            font-weight: 600;
            margin-bottom: .25rem;
        }

        .tenant-layout .tenant-dashboard-hero .tenant-dashboard-subtitle {
            opacity: .9;
            font-size: .98rem;
        }

        .tenant-layout .tenant-dashboard-actions .btn {
            border-radius: 999px;
            padding: .45rem 1.3rem;
            font-weight: 500;
            margin: 0 .2rem .4rem;
        }

        .tenant-layout .tenant-dashboard-metrics {
            margin-bottom: .75rem;
        }

        .tenant-layout .tenant-metric-card {
            border-radius: 16px;
            padding: .9rem 1rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, #edf3ff 50%, #e0ecff 100%);
            box-shadow: 0 14px 30px rgba(15, 37, 68, 0.08);
            position: relative;
            overflow: hidden;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .tenant-layout .tenant-metric-card::after {
            content: '';
            position: absolute;
            inset-inline-end: -20px;
            top: -20px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: radial-gradient(circle at center, rgba(16, 44, 79, 0.18), transparent 65%);
            opacity: .8;
        }

        .tenant-layout .tenant-metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(15, 37, 68, 0.16);
        }

        .tenant-layout .tenant-metric-icon {
            font-size: 1.4rem;
            color: {{ $brandColor }};
            margin-bottom: .35rem;
            position: relative;
            z-index: 1;
        }

        .tenant-layout .tenant-metric-label {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c7a92;
            margin-bottom: .15rem;
            position: relative;
            z-index: 1;
        }

        .tenant-layout .tenant-metric-value {
            font-size: 1.3rem;
            font-weight: 700;
            color: #102c4f;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .tenant-layout .tenant-metric-emoji {
            font-size: 1.1rem;
        }

        .tenant-layout .tenant-dashboard-grid {
            margin-top: 1.5rem;
        }

        .tenant-layout .tenant-dashboard-card {
            height: 100%;
            border-radius: 18px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 55%, #eef3fb 100%);
            border: 1px solid {{ $brandColor }};
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .tenant-layout .tenant-dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 45px rgba(15, 37, 68, 0.15);
        }

        .tenant-layout .tenant-dashboard-icon {
            font-size: 1.6rem;
            color: {{ $brandColor }};
            margin-bottom: .75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(16, 44, 79, 0.08);
        }

        /* Tenant tables styling */
        .tenant-table-wrapper {
            border-radius: 16px;
            border: 1px solid #102c4f;
            box-shadow: 0 12px 30px rgba(15, 37, 68, 0.10);
            overflow: hidden;
            background: #ffffff;
            padding: 30px;
        }

        .tenant-table thead th {
            background: linear-gradient(90deg, rgba(16, 44, 79, 0.06), rgba(16, 44, 79, 0.03));
            border-bottom: 1px solid rgba(15, 37, 68, 0.16);
            font-size: 0.95rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #4a5672;
        }

        .tenant-table tbody tr:hover {
            background-color: #f5f7fb;
        }

        .tenant-table tbody tr {
            transition: background-color .18s ease, box-shadow .18s ease, transform .12s ease;
        }

        .tenant-table tbody tr:not(:last-child) {
            border-bottom: 1px solid rgba(15, 37, 68, 0.04);
        }

        .tenant-table tbody td {
            vertical-align: middle;
            font-size: 0.9rem;
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
        }

        .tenant-table .btn.tenant-action-btn {
            border-radius: 999px;
            padding-inline: 0.9rem;
        }

        .tenant-layout .tenant-dashboard-card h5 {
            font-weight: 600;
            margin-bottom: .5rem;
        }

        .tenant-layout .tenant-dashboard-card p {
            font-size: .9rem;
            color: #6c7a92;
            margin-bottom: .9rem;
        }

        .tenant-layout .btn-primary {
            background-color: {{ $brandColor }};
            border-color: {{ $brandColor }};
            border-radius: 999px;
            padding: .65rem 1.6rem;
            font-weight: 600;
            font-size: .98rem;
            min-width: 170px;
        }

        .tenant-layout .btn-primary:hover {
            filter: brightness(1.05);
            box-shadow: 0 6px 14px rgba(16, 44, 79, 0.4);
        }

        .tenant-layout .btn-outline-primary {
            color: {{ $brandColor }};
            border-radius: 999px;
            border-width: 2px;
            border-color: {{ $brandColor }};
            padding: .4rem 1.2rem;
            font-weight: 500;
            background-color: #ffffff;
        }

        .tenant-layout .btn-outline-primary:hover {
            background-color: {{ $brandColor }};
            color: #ffffff;
            box-shadow: 0 6px 14px rgba(16, 44, 79, 0.35);
        }

        .tenant-layout .btn-outline-danger {
            border-radius: 999px;
            border-width: 2px;
            padding: .35rem 1.1rem;
            font-weight: 500;
        }

        .tenant-layout .form-control,
        .tenant-layout .custom-file-upload {
            border-radius: 12px;
            border-color: #d0d7e2;
            background-color: #fbfcff;
        }

        .tenant-layout .form-control {
            padding: .5rem 1rem;
        }

        .tenant-layout .form-control:focus {
            border-color: {{ $brandColor }};
            box-shadow: 0 0 0 0.16rem rgba(16, 44, 79, 0.18);
            background-color: #ffffff;
        }

        .tenant-layout label {
            font-weight: 500;
            color: #102c4f;
        }

        .tenant-layout .list-group-item {
            border-radius: 12px;
            border-color: #e3e8f4;
            margin-bottom: .4rem;
        }

        .tenant-layout .tenant-page-header {
            margin-bottom: 1.25rem;
        }

        .tenant-layout .tenant-page-header-card {
            border-radius: 16px;
            border: 1px solid {{ $brandColor }};
            background: linear-gradient(135deg, #ffffff 0%, #f4f6fb 50%, #e7edf9 100%);
        }

        .tenant-layout .tenant-page-header-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #102c4f;
            margin-bottom: .15rem;
        }

        .tenant-layout .tenant-page-header-subtitle {
            font-size: .9rem;
            color: #6c7a92;
            margin-bottom: 0;
        }

        /* DataTables spacing: wrapper + length + pagination */
        .dataTables_wrapper {
            padding-top: .75rem;
            padding-bottom: .75rem;
        }

        div.table-responsive>div.dataTables_wrapper>div.row {
            margin-inline: 0 !important;
        }

        .dataTables_wrapper .dataTables_length {
            margin-top: .5rem;
            margin-bottom: .75rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: .75rem;
            margin-bottom: .5rem;
        }

        .tenant-layout .tenant-page-header-actions .btn {
            border-radius: 999px;
            padding: .35rem 1rem;
        }

        .tenant-layout .tenant-action-btn {
            border-radius: 6px !important;
            padding-inline: .8rem;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
        }

        .tenant-layout .tenant-action-btn i {
            font-size: 1rem;
        }

        /* Make action columns in tenant tables compact and always visible */
        .tenant-layout .tenant-table th:last-child,
        .tenant-layout .tenant-table td:last-child {
            white-space: nowrap;
            width: 1%;
        }

        @media (max-width: 767.98px) {
            .tenant-layout .navbar.default-layout .navbar-menu-wrapper {
                padding-inline: .5rem;
            }

            .tenant-layout .tenant-dashboard-hero .card-body {
                padding: 1rem 1.1rem;
            }

            .tenant-layout .tenant-dashboard-title {
                font-size: 1.4rem;
            }

            .tenant-layout .tenant-dashboard-subtitle {
                font-size: .9rem;
            }

            .tenant-layout .tenant-dashboard-actions {
                width: 100%;
                margin-top: .75rem;
                justify-content: flex-start;
            }

            .tenant-layout .tenant-dashboard-actions .btn {
                width: 100%;
                justify-content: center;
                margin-inline: 0;
            }

            .tenant-layout .tenant-dashboard-metrics .tenant-metric-card {
                padding: .8rem .9rem;
            }

            .tenant-layout .tenant-metric-value {
                font-size: 1.1rem;
            }

            .tenant-layout .tenant-page-header-card {
                padding-inline: .4rem;
            }

            .tenant-layout .content-wrapper {
                padding-inline: .8rem;
            }

            .tenant-layout .sidebar-offcanvas {
                width: 240px;
            }
        }
    </style>
    {{-- سكربت التحديث التلقائي لرسائل التينانت تم تعطيله مؤقتاً --}}
</head>

<body data-base-url="{{ url('/') }}"
    class="{{ app()->getLocale() === 'ar' ? 'rtl' : '' }} {{ \Illuminate\Support\Facades\Auth::guard('tenant')->check() ? 'tenant-layout' : '' }}">

    <div class="container-scroller" id="app">
        @include('layout.header')
        <div class="container-fluid page-body-wrapper">
            @include('layout.sidebar')
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>
                @include('layout.footer')
            </div>
        </div>
    </div>

    <!-- base js -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <!-- end base js -->

    <!-- plugin js -->
    @stack('plugin-scripts')
    <!-- end plugin js -->

    <!-- common js -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <!-- end common js -->

    @stack('custom-scripts')
</body>

</html>
