<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        @php
            $defaultLogo = asset('assets/images/logo-w.png');
            $brandLogo = $defaultLogo;

            if (Auth::guard('tenant')->check()) {
                try {
                    $tenantSetting = \App\Models\TenantSetting::query()->first();
                    if (!$tenantSetting) {
                        $tenant = Auth::guard('tenant')->user()->tenant ?? null;
                        $tenantSetting = new \App\Models\TenantSetting([
                            'name' => $tenant->TenantName ?? null,
                        ]);
                    }

                    if ($tenantSetting && $tenantSetting->logo_path) {
                        $brandLogo = asset('storage/' . $tenantSetting->logo_path);
                    }
                } catch (\Throwable $e) {
                    $brandLogo = $defaultLogo;
                }
            }
        @endphp
        <a class="navbar-brand brand-logo" href="{{ url('/') }}">
            <img src="{{ $brandLogo }}" alt="logo" style="height:32px; object-fit:contain;" />
        </a>
        <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}">
            <img src="{{ $brandLogo }}" alt="logo" style="height:24px; object-fit:contain;" />
        </a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-left header-links">
            @php
                $currentName = Route::currentRouteName();
                $host = request()->getHost();
                $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
                $isAdminRoute = \Illuminate\Support\Str::startsWith($currentName, 'admin.');
                $subFromHost =
                    $host !== $baseHost && \Illuminate\Support\Str::endsWith($host, $baseHost)
                        ? str_replace('.' . $baseHost, '', $host)
                        : null;
                $subdomain = request()->route('subdomain') ?? $subFromHost;
                $isSub =
                    !$isAdminRoute &&
                    ($subFromHost !== null || \Illuminate\Support\Str::startsWith($currentName, 'tenant.subdomain.'));
                $prefix = $isSub ? 'tenant.subdomain' : 'tenant';
                $tenantGuard = \Illuminate\Support\Facades\Auth::guard('tenant');
                // اعتبر أن بيئة المستأجر جاهزة بمجرد أن يكون مستخدم المستأجر مسجلاً للدخول وفي سياق المستأجر فقط
                $tenantReady = !$isAdminRoute && $tenantGuard->check();
                $tenantUser = $tenantReady ? $tenantGuard->user() : null;
            @endphp
            @if ($tenantReady)
                @if ($tenantUser && ($tenantUser->hasRole('admin') || $tenantUser->hasRole('Manager')))
                    <li class="nav-item d-none d-md-flex">
                        <a href="{{ route($prefix . '.dashboard', ['subdomain' => $subdomain]) }}" class="nav-link">
                            <i class="mdi mdi-view-dashboard-outline"></i> {{ __('app.tenant_panel') }}
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-flex">
                        <a href="{{ route($prefix . '.settings.edit', ['subdomain' => $subdomain]) }}" class="nav-link">
                            <i class="mdi mdi-tune"></i> {{ __('app.tenant_settings_title') }}
                        </a>
                    </li>
                @endif
            @endif
        </ul>
        <ul class="navbar-nav navbar-nav-right">
            @php
                $currentLocale = app()->getLocale();
                $supportedLocales = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();
                $tenantGuard = \Illuminate\Support\Facades\Auth::guard('tenant');
                $currentName = Route::currentRouteName();
                $isAdminRoute = \Illuminate\Support\Str::startsWith($currentName, 'admin.');
            @endphp
            <li class="nav-item dropdown d-none d-md-flex">
                <a class="nav-link dropdown-toggle" id="languageDropdown" href="#" data-toggle="dropdown"
                    aria-expanded="false">
                    <i class="mdi mdi-translate"></i>
                    <span class="d-none d-lg-inline">{{ strtoupper($currentLocale) }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="languageDropdown">
                    @foreach ($supportedLocales as $localeCode => $properties)
                        <a class="dropdown-item {{ $localeCode === $currentLocale ? 'active' : '' }}"
                            href="{{ Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                            {{ $properties['native'] ?? strtoupper($localeCode) }}
                        </a>
                    @endforeach
                </div>
            </li>
            {{-- تنبيهات لوحة تحكم الأدمن (جرس واحد لكل الأنواع) --}}
            @if ($isAdminRoute)
                @php
                    $windowDays = 30;
                    $now = \Illuminate\Support\Carbon::now();
                    $upcomingTenants = \App\Models\Tenant::whereNotNull('SubscriptionEndDate')
                        ->get()
                        ->filter(function ($t) use ($now, $windowDays) {
                            $end = \Illuminate\Support\Carbon::parse($t->SubscriptionEndDate);
                            $days = (int) $now->diffInDays($end, false);
                            return $days >= 0 && $days <= $windowDays;
                        });
                    $upcomingCount = $upcomingTenants->count();
                @endphp

                <li class="nav-item dropdown" id="admin-complaints-notifications"
                    data-feed-url="{{ route('admin.complaints.feed', ['only_unreplied' => 1]) }}"
                    data-upcoming-count="{{ $upcomingCount }}">
                    <a class="nav-link count-indicator dropdown-toggle" id="adminComplaintsDropdown" href="#"
                        data-toggle="dropdown">
                        <i class="mdi mdi-bell-outline"></i>
                        <span class="count bg-danger {{ $upcomingCount > 0 ? '' : 'd-none' }}"
                            id="admin-complaints-count">
                            {{ $upcomingCount > 0 ? $upcomingCount : 0 }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0"
                        aria-labelledby="adminComplaintsDropdown">
                        {{-- قسم بلاغات المستأجرين --}}
                        <div class="dropdown-item py-3 border-bottom d-flex justify-content-between align-items-center">
                            <p class="mb-0 font-weight-medium">{{ __('app.admin_complaints_title') }}</p>
                            <a href="{{ route('admin.complaints.index') }}"
                                class="badge badge-pill badge-primary">{{ __('app.view_all') }}</a>
                        </div>
                        <div id="admin-complaints-list">
                            <p class="text-muted small px-3 py-2 mb-0">{{ __('app.tenant_complaint_list_empty') }}</p>
                        </div>

                        {{-- قسم اشتراكات المستأجرين القريبة الانتهاء --}}
                        <div
                            class="dropdown-item py-3 border-top border-bottom d-flex justify-content-between align-items-center mt-2">
                            <p class="mb-0 font-weight-medium">{{ __('app.upcoming_expirations') }}</p>
                            <a href="{{ route('admin.subscribers.risks') }}"
                                class="badge badge-pill badge-primary">{{ __('app.view_all') }}</a>
                        </div>
                        @if ($upcomingCount === 0)
                            <p class="text-muted small px-3 py-2 mb-0">{{ __('app.tenant_risks_empty') }}</p>
                        @else
                            @foreach ($upcomingTenants->take(5) as $upTenant)
                                @php
                                    $end = \Illuminate\Support\Carbon::parse($upTenant->SubscriptionEndDate);
                                    $daysLeft = (int) $now->diffInDays($end, false);
                                @endphp
                                <a class="dropdown-item preview-item d-flex align-items-center"
                                    href="{{ route('admin.subscribers.risks', ['tenant' => $upTenant->TenantID]) }}">
                                    <div
                                        class="preview-thumbnail bg-warning d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-chart-line text-white"></i>
                                    </div>
                                    <div class="preview-item-content flex-grow">
                                        <p class="preview-subject ellipsis mb-0">
                                            {{ $upTenant->TenantName }}
                                        </p>
                                        <p class="text-muted mb-0 small">
                                            {{ __('app.subscriptions_expiring_within', ['days' => $daysLeft]) }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </li>
            @elseif(!$isAdminRoute && $tenantGuard->check())
                <li class="nav-item dropdown" id="tenant-complaints-notifications"
                    data-feed-url="{{ route($prefix . '.complaints.feed', ['subdomain' => $subdomain]) }}">
                    <a class="nav-link count-indicator dropdown-toggle" id="tenantComplaintsDropdown" href="#"
                        data-toggle="dropdown">
                        <i class="mdi mdi-bell-outline"></i>
                        <span class="count bg-danger d-none" id="tenant-complaints-count">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0"
                        aria-labelledby="tenantComplaintsDropdown">
                        <div class="dropdown-item py-3 border-bottom d-flex justify-content-between align-items-center">
                            <p class="mb-0 font-weight-medium">{{ __('app.tenant_complaints_menu') }}</p>
                            <a href="{{ route($prefix . '.complaints.index', ['subdomain' => $subdomain]) }}"
                                class="badge badge-pill badge-primary">{{ __('app.view_all') }}</a>
                        </div>
                        <div id="tenant-complaints-list">
                            <p class="text-muted small px-3 py-2 mb-0">{{ __('app.tenant_complaint_list_empty') }}</p>
                        </div>
                    </div>
                </li>
            @endif
            @php
                $webGuard = \Illuminate\Support\Facades\Auth::guard('web');
            @endphp
            @if (!$isAdminRoute && $tenantGuard->check())
                @php
                    $headerUser = $tenantGuard->user();
                    $avatarPath = $headerUser->avatar ?? null;
                    $avatarUrl = $avatarPath
                        ? asset('storage/' . $avatarPath)
                        : 'https://ui-avatars.com/api/?name=' .
                            urlencode($headerUser->name ?? 'User') .
                            '&background=102c4f&color=fff&rounded=true&size=64';
                @endphp
                <li class="nav-item dropdown d-none d-xl-inline-block">
                    <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown"
                        aria-expanded="false">
                        <span class="profile-text d-none d-md-inline-flex">{{ $headerUser->name }}</span>
                        <img class="img-xs rounded-circle" src="{{ $avatarUrl }}"
                            alt="{{ __('app.profile_image_alt') }}"
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($headerUser->name ?? 'User') }}&background=102c4f&color=fff&rounded=true&size=64';">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                        @if ($headerUser && ($headerUser->hasRole('admin') || $headerUser->hasRole('Manager')))
                            <a class="dropdown-item mt-2"
                                href="{{ route($prefix . '.settings.edit', ['subdomain' => $subdomain]) }}">
                                {{ __('app.tenant_settings_title') }}
                            </a>
                            <a class="dropdown-item"
                                href="{{ route($prefix . '.dashboard', ['subdomain' => $subdomain]) }}">
                                {{ __('app.tenant_statistics') }}
                            </a>
                            <a class="dropdown-item"
                                href="{{ route($prefix . '.activity.index', ['subdomain' => $subdomain]) }}">
                                {{ __('app.activity_log_title') }}
                            </a>
                        @endif
                        <a class="dropdown-item mt-2"
                            href="{{ route($prefix . '.password.edit', ['subdomain' => $subdomain]) }}">
                            {{ __('app.change_password') }}
                        </a>
                        <a class="dropdown-item" href="{{ route($prefix . '.logout', ['subdomain' => $subdomain]) }}"
                            onclick="event.preventDefault(); document.getElementById('tenant-logout-form').submit();">
                            {{ __('app.logout') }}
                        </a>
                        <form id="tenant-logout-form"
                            action="{{ route($prefix . '.logout', ['subdomain' => $subdomain]) }}" method="POST"
                            style="display:none;">
                            @csrf
                        </form>
                    </div>
                </li>
            @elseif($webGuard->check())
                @php
                    $headerUser = $webGuard->user();
                @endphp
                <li class="nav-item dropdown d-none d-xl-inline-block">
                    <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown"
                        aria-expanded="false">
                        <span class="profile-text d-none d-md-inline-flex">{{ $headerUser->name }}</span>
                        <img class="img-xs rounded-circle" src="{{ url('assets/images/faces/face8.jpg') }}"
                            alt="{{ __('app.profile_image_alt') }}">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                        <a class="dropdown-item mt-2" href="#">{{ __('app.manage_accounts') }}</a>
                        <a class="dropdown-item" href="#">{{ __('app.change_password') }}</a>
                        <a class="dropdown-item" href="#">{{ __('app.check_inbox') }}</a>
                        <a class="dropdown-item" href="{{ route('admin.logout') }}"
                            onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                            {{ __('app.sign_out') }}
                        </a>
                        <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST"
                            style="display:none;">
                            @csrf
                        </form>
                    </div>
                </li>
            @endif
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="mdi mdi-menu icon-menu"></span>
        </button>
    </div>
</nav>

@if (!empty($isAdminRoute) && $isAdminRoute)
    @push('custom-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var container = document.getElementById('admin-complaints-notifications');
                if (!container || !window.axios) return;

                var feedUrl = container.getAttribute('data-feed-url');
                var badge = document.getElementById('admin-complaints-count');
                var list = document.getElementById('admin-complaints-list');
                var baseUpcoming = parseInt(container.getAttribute('data-upcoming-count') || '0', 10) || 0;

                async function refreshAdminComplaints() {
                    try {
                        var response = await window.axios.get(feedUrl);
                        var data = response.data || {};
                        var items = data.items || [];
                        var counts = data.counts || {};
                        var newUnreplied = counts.new_unreplied || 0;
                        var total = newUnreplied + baseUpcoming;

                        if (badge) {
                            if (total > 0) {
                                badge.textContent = total;
                                badge.classList.remove('d-none');
                            } else {
                                badge.classList.add('d-none');
                            }
                        }

                        if (list) {
                            if (!items.length) {
                                list.innerHTML =
                                    '<p class="text-muted small px-3 py-2 mb-0">{{ __('app.tenant_complaint_list_empty') }}</p>';
                                return;
                            }

                            var html = '';
                            items.slice(0, 5).forEach(function(item) {
                                var statusBadge = '';
                                if (item.status === 'closed') {
                                    statusBadge =
                                        '<span class="badge badge-success">{{ __('app.tenant_complaint_status_closed') }}</span>';
                                } else if (item.status === 'in_progress') {
                                    statusBadge =
                                        '<span class="badge badge-warning">{{ __('app.tenant_complaint_status_in_progress') }}</span>';
                                } else {
                                    statusBadge =
                                        '<span class="badge badge-info">{{ __('app.tenant_complaint_status_open') }}</span>';
                                }

                                var tenant = (item.tenant_name || '-') + (item.tenant_subdomain ?
                                    '<div class="text-muted small">' + item.tenant_subdomain +
                                    '.{{ parse_url(config('app.url'), PHP_URL_HOST) }}</div>' :
                                    ''
                                );

                                html += '<a href="' + item.show_url +
                                    '" class="dropdown-item preview-item py-3">' +
                                    '<div class="preview-thumbnail"><i class="mdi mdi-alert-circle-outline m-auto text-primary"></i></div>' +
                                    '<div class="preview-item-content">' +
                                    '<h6 class="preview-subject font-weight-normal text-dark mb-1">' + (item
                                        .subject || '') + '</h6>' +
                                    '<p class="font-weight-light small-text mb-0">' + tenant + '</p>' +
                                    '<p class="font-weight-light small-text mb-0 mt-1">' + (item
                                        .created_at || '') + ' · ' + statusBadge + '</p>' +
                                    '</div>' +
                                    '</a>';
                            });

                            list.innerHTML = html;
                        }
                    } catch (e) {
                        console.error('Failed to refresh admin complaints notifications', e);
                    }
                }

                refreshAdminComplaints();
                setInterval(refreshAdminComplaints, 15000);
            });
        </script>
    @endpush
@elseif(isset($tenantGuard) && $tenantGuard->check() && (empty($isAdminRoute) || !$isAdminRoute))
    @push('custom-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var container = document.getElementById('tenant-complaints-notifications');
                if (!container || !window.axios) return;

                var feedUrl = container.getAttribute('data-feed-url');
                var badge = document.getElementById('tenant-complaints-count');
                var list = document.getElementById('tenant-complaints-list');

                async function refreshTenantComplaints() {
                    try {
                        var response = await window.axios.get(feedUrl);
                        var data = response.data || {};
                        var items = data.items || [];
                        var counts = data.counts || {};
                        var pending = counts.pending || 0;

                        if (badge) {
                            if (pending > 0) {
                                badge.textContent = pending;
                                badge.classList.remove('d-none');
                            } else {
                                badge.classList.add('d-none');
                            }
                        }

                        if (list) {
                            if (!items.length) {
                                list.innerHTML =
                                    '<p class="text-muted small px-3 py-2 mb-0">{{ __('app.tenant_complaint_list_empty') }}</p>';
                                return;
                            }

                            var html = '';
                            items.slice(0, 5).forEach(function(item) {
                                var statusBadge = '';
                                if (item.status === 'closed') {
                                    statusBadge =
                                        '<span class="badge badge-success">{{ __('app.tenant_complaint_status_closed') }}</span>';
                                } else if (item.status === 'in_progress') {
                                    statusBadge =
                                        '<span class="badge badge-warning">{{ __('app.tenant_complaint_status_in_progress') }}</span>';
                                } else {
                                    statusBadge =
                                        '<span class="badge badge-info">{{ __('app.tenant_complaint_status_open') }}</span>';
                                }

                                html += '<a href="' + item.show_url +
                                    '" class="dropdown-item preview-item py-3">' +
                                    '<div class="preview-thumbnail"><i class="mdi mdi-alert-circle-outline m-auto text-primary"></i></div>' +
                                    '<div class="preview-item-content">' +
                                    '<h6 class="preview-subject font-weight-normal text-dark mb-1">' + (item
                                        .subject || '') + '</h6>' +
                                    '<p class="font-weight-light small-text mb-0 mt-1">' + (item
                                        .changed_at || '') + ' · ' + statusBadge + '</p>' +
                                    '</div>' +
                                    '</a>';
                            });

                            list.innerHTML = html;
                        }
                    } catch (e) {
                        console.error('Failed to refresh tenant complaints notifications', e);
                    }
                }

                refreshTenantComplaints();
                setInterval(refreshTenantComplaints, 15000);
            });
        </script>
    @endpush
@endif
