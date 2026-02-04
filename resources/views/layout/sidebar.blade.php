<nav class="sidebar sidebar-offcanvas dynamic-active-class-disabled" id="sidebar">
  <ul class="nav">
    @php
      $currentName = Route::currentRouteName();
      $host = request()->getHost();
      $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
      $subFromHost = ($host !== $baseHost && \Illuminate\Support\Str::endsWith($host, $baseHost))
        ? str_replace('.'.$baseHost, '', $host)
        : null;
      $subdomain = request()->route('subdomain') ?? $subFromHost;
      $isTenantContext = ($subFromHost !== null) || \Illuminate\Support\Str::startsWith($currentName, 'tenant.subdomain.');
      $prefix = $isTenantContext ? 'tenant.subdomain' : 'tenant';
      $tenantUser = $isTenantContext ? \Illuminate\Support\Facades\Auth::guard('tenant')->user() : null;
      $isManager = $tenantUser && ($tenantUser->hasRole('admin') || $tenantUser->hasRole('Manager'));
    @endphp

    @php
      $isAdminContext = !$isTenantContext && \Illuminate\Support\Str::startsWith($currentName, 'admin.');
    @endphp

    @if($isTenantContext && $tenantUser)
      <li class="nav-item nav-profile not-navigation-link">
        <div class="nav-link">
          <div class="user-wrapper d-flex align-items-center">
            <div class="profile-image me-2">
              @php
                $tenantAvatarPath = $tenantUser->avatar ?? null;
                $tenantAvatarUrl = $tenantAvatarPath
                  ? asset('storage/' . $tenantAvatarPath)
                  : 'https://ui-avatars.com/api/?name=' . urlencode($tenantUser->name ?? 'User') . '&background=102c4f&color=fff&rounded=true&size=64';
              @endphp
              <img src="{{ $tenantAvatarUrl }}" alt="{{ __('app.profile_image_alt') }}"
                   onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($tenantUser->name ?? 'User') }}&background=102c4f&color=fff&rounded=true&size=64';">
            </div>
            <div class="text-wrapper">
              <p class="profile-name mb-0">{{ $tenantUser->name }}</p>
              <small class="designation text-muted">{{ __('Tenant User') }}</small>
            </div>
          </div>
        </div>
      </li>
      <li class="nav-item {{ active_class(['tenant.subdomain.dashboard']) }}">
        <a class="nav-link" href="{{ route($prefix.'.dashboard', ['subdomain' => $subdomain]) }}">
          <i class="menu-icon mdi mdi-view-dashboard"></i>
          <span class="menu-title">{{ __('app.dashboard') }}</span>
        </a>
      </li>
      @if($isManager)
      <li class="nav-item {{ active_class(['tenant.subdomain.users.*','tenant.subdomain.roles.*','tenant.subdomain.permissions.*']) }}">
        <a class="nav-link" data-toggle="collapse" href="#tenant-users-menu" aria-expanded="{{ is_active_route(['tenant.subdomain.users.*','tenant.subdomain.roles.*','tenant.subdomain.permissions.*']) }}" aria-controls="tenant-users-menu">
          <i class="menu-icon mdi mdi-account-multiple"></i>
          <span class="menu-title">{{ __('app.users') }}</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ show_class(['tenant.subdomain.users.*','tenant.subdomain.roles.*','tenant.subdomain.permissions.*']) }}" id="tenant-users-menu">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item {{ active_class(['tenant.subdomain.users.*']) }}">
              <a class="nav-link" href="{{ route($prefix.'.users.index', ['subdomain' => $subdomain]) }}">{{ __('app.users') }}</a>
            </li>
            <li class="nav-item {{ active_class(['tenant.subdomain.roles.*']) }}">
              <a class="nav-link" href="{{ route($prefix.'.roles.index', ['subdomain' => $subdomain]) }}">{{ __('app.roles') }}</a>
            </li>
            <li class="nav-item {{ active_class(['tenant.subdomain.permissions.*']) }}">
              <a class="nav-link" href="{{ route($prefix.'.permissions.index', ['subdomain' => $subdomain]) }}">{{ __('app.permissions') }}</a>
            </li>
          </ul>
        </div>
      </li>
      @endif
      @if($tenantUser->can('Attachement'))
        <li class="nav-item {{ active_class(['tenant.subdomain.attachments.*']) }}">
          <a class="nav-link" href="{{ route($prefix.'.attachments.index', ['subdomain' => $subdomain]) }}">
            <i class="menu-icon mdi mdi-paperclip"></i>
            <span class="menu-title">{{ __('app.attachments') }}</span>
          </a>
        </li>
      @endif
      <li class="nav-item {{ active_class(['tenant.subdomain.complaints.*']) }}">
        <a class="nav-link" href="{{ route($prefix.'.complaints.index', ['subdomain' => $subdomain]) }}">
          <i class="menu-icon mdi mdi-alert-circle-outline"></i>
          <span class="menu-title">{{ __('app.tenant_complaints_menu') }}</span>
        </a>
      </li>
    @elseif($isAdminContext)
      <li class="nav-item {{ active_class(['admin.dashboard']) }}">
        <a class="nav-link" href="{{ route('admin.dashboard', [], false) }}">
          <i class="menu-icon mdi mdi-view-dashboard"></i>
          <span class="menu-title">{{ __('app.dashboard') }}</span>
        </a>
      </li>
      <li class="nav-item {{ active_class(['admin.users.*','admin.roles.*','admin.permissions.*']) }}">
        <a class="nav-link" data-toggle="collapse" href="#admin-users-menu" aria-expanded="{{ is_active_route(['admin.users.*','admin.roles.*','admin.permissions.*']) }}" aria-controls="admin-users-menu">
          <i class="menu-icon mdi mdi-account-multiple"></i>
          <span class="menu-title">{{ __('app.users') }}</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ show_class(['admin.users.*','admin.roles.*','admin.permissions.*']) }}" id="admin-users-menu">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item {{ active_class(['admin.users.*']) }}">
              <a class="nav-link" href="{{ route('admin.users.index') }}">{{ __('app.users') }}</a>
            </li>
            <li class="nav-item {{ active_class(['admin.roles.*']) }}">
              <a class="nav-link" href="{{ route('admin.roles.index') }}">{{ __('app.roles') }}</a>
            </li>
            <li class="nav-item {{ active_class(['admin.permissions.*']) }}">
              <a class="nav-link" href="{{ route('admin.permissions.index') }}">{{ __('app.permissions') }}</a>
            </li>
          </ul>
        </div>
      </li>
      @php
        $plansSummary = \App\Models\Tenant::select('Plan', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
          ->groupBy('Plan')
          ->orderBy('Plan')
          ->get();
      @endphp
      <li class="nav-item {{ active_class(['admin.subscribers.*']) }}">
        <a class="nav-link" data-toggle="collapse" href="#admin-subscriptions-menu" aria-expanded="{{ is_active_route(['admin.subscribers.*']) }}" aria-controls="admin-subscriptions-menu">
          <i class="menu-icon mdi mdi-receipt"></i>
          <span class="menu-title">{{ __('app.subscriptions') }}</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ show_class(['admin.subscribers.*','admin.payments.*']) }}" id="admin-subscriptions-menu">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item {{ active_class(['admin.subscribers.index']) }}">
              <a class="nav-link" href="{{ route('admin.subscribers.index') }}">{{ __('app.view_all') }}</a>
            </li>
            <li class="nav-item {{ active_class(['admin.payments.*']) }}">
              <a class="nav-link" href="{{ route('admin.payments.index') }}">{{ __('app.payments') }}</a>
            </li>
            @foreach($plansSummary as $plan)
              <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.subscribers.index', ['plan' => $plan->Plan]) }}">
                  {{ $plan->Plan ?? '-' }} ({{ $plan->total }})
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </li>
      <li class="nav-item {{ active_class(['admin.plans.*']) }}">
        <a class="nav-link" href="{{ route('admin.plans.index') }}">
          <i class="menu-icon mdi mdi-tune"></i>
          <span class="menu-title">إعدادات الباقات</span>
        </a>
      </li>
      <li class="nav-item {{ active_class(['admin.subscription-plans.*']) }}">
        <a class="nav-link" href="{{ route('admin.subscription-plans.index') }}">
          <i class="menu-icon mdi mdi-cash-multiple"></i>
          <span class="menu-title">إدارة الباقات</span>
        </a>
      </li>
      <li class="nav-item {{ active_class(['admin.complaints.*']) }}">
        <a class="nav-link" href="{{ route('admin.complaints.index') }}">
          <i class="menu-icon mdi mdi-alert"></i>
          <span class="menu-title">{{ __('app.admin_complaints_menu') }}</span>
        </a>
      </li>
      <li class="nav-item {{ active_class(['admin.reports.*']) }}">
        <a class="nav-link" data-toggle="collapse" href="#admin-reports-menu" aria-expanded="{{ is_active_route(['admin.reports.*']) }}" aria-controls="admin-reports-menu">
          <i class="menu-icon mdi mdi-chart-line"></i>
          <span class="menu-title">{{ __('app.reports') }}</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ show_class(['admin.reports.*']) }}" id="admin-reports-menu">
          <ul class="nav flex-column sub-menu">
            {{--
            <li class="nav-item {{ active_class(['admin.reports.overview']) }}">
              <a class="nav-link" href="{{ route('admin.reports.overview') }}">{{ __('app.reports_overview') }}</a>
            </li>
            --}}
            <li class="nav-item {{ active_class(['admin.reports.upcoming-expirations']) }}">
              <a class="nav-link" href="{{ route('admin.reports.upcoming-expirations') }}">{{ __('app.upcoming_expirations') }}</a>
            </li>
            <li class="nav-item {{ active_class(['admin.reports.plans']) }}">
              <a class="nav-link" href="{{ route('admin.reports.plans') }}">{{ __('app.plans_report') }}</a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item {{ active_class(['admin.backups.tenants.*']) }}">
        <a class="nav-link" href="{{ route('admin.backups.tenants.index') }}">
          <i class="menu-icon mdi mdi-database-arrow-down"></i>
          <span class="menu-title">{{ __('نسخ احتياطي للمشتركين') }}</span>
        </a>
      </li>
    @else
    <li class="nav-item nav-profile not-navigation-link">
      <div class="nav-link">
        <div class="user-wrapper">
          <div class="profile-image">
            <img src="{{ url('assets/images/faces/face8.jpg') }}" alt="{{ __('app.profile_image_alt') }}">
          </div>
          <div class="text-wrapper">
            <p class="profile-name">Richard V.Welsh</p>
            <div class="dropdown" data-display="static">
              <a href="#" class="nav-link d-flex user-switch-dropdown-toggler" id="UsersettingsDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                <small class="designation text-muted">Manager</small>
                <span class="status-indicator online"></span>
              </a>
              <div class="dropdown-menu" aria-labelledby="UsersettingsDropdown">
                <a class="dropdown-item p-0">
                  <div class="d-flex border-bottom">
                    <div class="py-3 px-4 d-flex align-items-center justify-content-center">
                      <i class="mdi mdi-bookmark-plus-outline mr-0 text-gray"></i>
                    </div>
                    <div class="py-3 px-4 d-flex align-items-center justify-content-center border-left border-right">
                      <i class="mdi mdi-account-outline mr-0 text-gray"></i>
                    </div>
                    <div class="py-3 px-4 d-flex align-items-center justify-content-center">
                      <i class="mdi mdi-alarm-check mr-0 text-gray"></i>
                    </div>
                  </div>
                </a>
                <a class="dropdown-item mt-2">{{ __('app.manage_accounts') }}</a>
                <a class="dropdown-item">{{ __('app.change_password') }}</a>
                <a class="dropdown-item">{{ __('app.check_inbox') }}</a>
                <a class="dropdown-item">{{ __('app.sign_out') }}</a>
              </div>
            </div>
          </div>
        </div>
        <button class="btn btn-success btn-block">New Project <i class="mdi mdi-plus"></i>
        </button>
      </div>
    </li>
    <li class="nav-item {{ active_class(['/']) }}">
      <a class="nav-link" href="{{ url('/') }}">
        <i class="menu-icon mdi mdi-television"></i>
        <span class="menu-title">{{ __('Dashboard') }}</span>
      </a>
    </li>
    <li class="nav-item {{ active_class(['basic-ui/*']) }}">
      <a class="nav-link" data-toggle="collapse" href="#basic-ui" aria-expanded="{{ is_active_route(['basic-ui/*']) }}" aria-controls="basic-ui">
        <i class="menu-icon mdi mdi-dna"></i>
        <span class="menu-title">{{ __('Basic UI Elements') }}</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ show_class(['basic-ui/*']) }}" id="basic-ui">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item {{ active_class(['basic-ui/buttons']) }}">
            <a class="nav-link" href="{{ url('/basic-ui/buttons') }}">{{ __('Buttons') }}</a>
          </li>
          <li class="nav-item {{ active_class(['basic-ui/dropdowns']) }}">
            <a class="nav-link" href="{{ url('/basic-ui/dropdowns') }}">{{ __('Dropdowns') }}</a>
          </li>
          <li class="nav-item {{ active_class(['basic-ui/typography']) }}">
            <a class="nav-link" href="{{ url('/basic-ui/typography') }}">{{ __('Typography') }}</a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item {{ active_class(['charts/chartjs']) }}">
      <a class="nav-link" href="{{ url('/charts/chartjs') }}">
        <i class="menu-icon mdi mdi-chart-line"></i>
        <span class="menu-title">{{ __('Charts') }}</span>
      </a>
    </li>
    <li class="nav-item {{ active_class(['tables/basic-table']) }}">
      <a class="nav-link" href="{{ url('/tables/basic-table') }}">
        <i class="menu-icon mdi mdi-table-large"></i>
        <span class="menu-title">{{ __('Tables') }}</span>
      </a>
    </li>
    <li class="nav-item {{ active_class(['icons/material']) }}">
      <a class="nav-link" href="{{ url('/icons/material') }}">
        <i class="menu-icon mdi mdi-emoticon"></i>
        <span class="menu-title">{{ __('Icons') }}</span>
      </a>
    </li>
    <li class="nav-item {{ active_class(['user-pages/*']) }}">
      <a class="nav-link" data-toggle="collapse" href="#user-pages" aria-expanded="{{ is_active_route(['user-pages/*']) }}" aria-controls="user-pages">
        <i class="menu-icon mdi mdi-lock-outline"></i>
        <span class="menu-title">{{ __('User Pages') }}</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse {{ show_class(['user-pages/*']) }}" id="user-pages">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item {{ active_class(['user-pages/login']) }}">
            <a class="nav-link" href="{{ url('/user-pages/login') }}">{{ __('Login') }}</a>
          </li>
          <li class="nav-item {{ active_class(['user-pages/register']) }}">
            <a class="nav-link" href="{{ url('/user-pages/register') }}">{{ __('Register') }}</a>
          </li>
          <li class="nav-item {{ active_class(['user-pages/lock-screen']) }}">
            <a class="nav-link" href="{{ url('/user-pages/lock-screen') }}">{{ __('Lock Screen') }}</a>
          </li>
        </ul>
      </div>
    </li>
    @endif
  </ul>
</nav>