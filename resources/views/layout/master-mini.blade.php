<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <title>DataInsight</title>
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

    <!-- plugin css -->
    @stack('plugin-styles')
    <!-- end plugin css -->

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

    <!-- marketing layout styles (navbar, hero, footer, sections) -->
    <style>
        :root {
            --di-primary: #102c4f;
        }

        .di-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10;
            background: rgba(16, 44, 79, 0.95);
            color: #fff;
        }

        .di-navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
        }

        .di-brand {
            display: flex;
            align-items: center;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        .di-brand img {
            height: 28px;
            margin-right: 10px;
        }

        html[dir='rtl'] .di-brand img {
            margin-right: 0;
            margin-left: 10px;
        }

        .di-nav-links {
            display: flex;
            align-items: center;
        }

        .di-nav-links a {
            color: #fff;
            margin: 0 12px;
            text-decoration: none;
            opacity: .95;
        }

        .di-nav-links a:hover {
            opacity: 1;
        }

        .di-nav-toggle {
            display: none;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 24px;
        }

        @media (max-width: 992px) {
            .di-nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(16, 44, 79, 0.98);
                padding: 10px 0;
            }

            .di-nav-links.show {
                display: block;
            }

            .di-nav-links a {
                display: block;
                padding: 8px 16px;
            }

            .di-nav-toggle {
                display: inline-block;
            }
        }

        .di-section-alt {
            background: #f7f9fc;
        }

        .di-section-gradient {
            background: linear-gradient(135deg, #0f2544 0%, #143a66 60%, #1b4f88 100%);
            color: #fff;
        }

        .di-section-contrast {
            background: #fff;
        }

        /* Ensure page content appears below fixed navbar */
        .page-body-wrapper.full-page-wrapper .content-wrapper {
            padding-top: 64px;
        }

        /* Footer styling shared */
        .di-footer {
            padding-top: 3rem;
            padding-bottom: 2.5rem;
        }

        #contact {
            background-color: #102c4f;
            box-shadow: 0 -18px 40px rgba(0, 0, 0, 0.4);
        }

        #contact h6 {
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        #contact a {
            text-decoration: none;
        }
    </style>

    @stack('style')
</head>

<body data-base-url="{{ url('/') }}" class="{{ app()->getLocale() === 'ar' ? 'rtl' : '' }}">

    <div class="container-scroller" id="app">
        <!-- Shared marketing navbar -->
        <nav class="di-navbar">
            <div class="container">
                <a href="{{ route('landing') }}" class="di-brand">
                    <img src="{{ asset('assets/images/logo-w.png') }}" alt="DataInsight">
                    <span>DataInsight</span>
                </a>
                <button class="di-nav-toggle" type="button" aria-label="Toggle navigation"><span
                        class="mdi mdi-menu"></span></button>
                <div class="di-nav-links" id="diNavLinks">
                    <a href="{{ route('landing') }}#features">{{ __('Key Benefits') }}</a>
                    <a href="{{ route('landing') }}#plans">{{ __('Choose Your Plan') }}</a>
                    <a href="{{ route('landing') }}#testimonials">{{ __('What our customers say') }}</a>
                    <a href="{{ route('static.about') }}">{{ __('app.about_title') }}</a>
                    <a href="{{ route('static.faq') }}">{{ __('app.faq_title') }}</a>
                    <a href="{{ route('landing') }}#contact">{{ __('app.contact') }}</a>
                    <span class="mx-2">|</span>
                    @php($locales = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales())
                    @foreach ($locales as $code => $props)
                        <a
                            href="{{ Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($code, null, [], true) }}">{{ $props['native'] }}</a>
                    @endforeach
                </div>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper full-page-wrapper">
            @yield('content')
        </div>

        <!-- Shared marketing footer -->
        <section id="contact" class="py-5 di-bg-primary text-white">
            <div class="container di-footer">
                <footer class="row">
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('assets/images/logo-w.png') }}" alt="DataInsight" style="height:36px;">
                        <p class="mt-3" style="opacity:.95; color:#fff;">
                            {{ __('We are a management consulting firm helping organizations make data-driven decisions.') }}
                        </p>
                        <div class="mt-2">
                            <a href="#" class="di-me-2" style="color:#fff; opacity:.9;"><i
                                    class="mdi mdi-twitter"></i></a>
                            <a href="#" class="di-me-2" style="color:#fff; opacity:.9;"><i
                                    class="mdi mdi-linkedin"></i></a>
                            <a href="#" class="di-me-2" style="color:#fff; opacity:.9;"><i
                                    class="mdi mdi-facebook"></i></a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="mb-2">{{ __('app.resources') }}</h6>
                        <ul class="list-unstyled" style="opacity:.9;">
                            <li><a href="{{ route('landing') }}#features"
                                    style="color:#fff;">{{ __('Key Benefits') }}</a></li>
                            <li><a href="{{ route('landing') }}#plans"
                                    style="color:#fff;">{{ __('Choose Your Plan') }}</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="mb-2">{{ __('app.contact') }}</h6>
                        <ul class="list-unstyled" style="opacity:.9;">
                            <li><i class="mdi mdi-email-outline mr-1"></i> info@datainsight.example</li>
                            <li><i class="mdi mdi-phone-outline mr-1"></i> +966-55-123-4567</li>
                            <li><i class="mdi mdi-whatsapp mr-1"></i> +966-55-987-6543</li>
                            <li><i class="mdi mdi-map-marker-outline mr-1"></i> {{ __('Riyadh, Saudi Arabia') }}</li>
                        </ul>
                    </div>
                    <div class="col-12 text-center mt-3" style="opacity:.8;">
                        <small>© {{ date('Y') }} DataInsight — {{ __('Management Consulting') }}</small>
                    </div>
                </footer>
            </div>
        </section>
    </div>

    <!-- base js -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- end base js -->

    <!-- plugin js -->
    @stack('plugin-scripts')
    <!-- end plugin js -->

    @stack('custom-scripts')
    @include('layout.partials.auto-logout')
</body>

</html>
