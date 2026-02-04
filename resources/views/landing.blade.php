@extends('layout.master-mini')

@section('content')
<div class="content-wrapper">
  @push('style')
  <style>
    :root { --di-primary: #102c4f; }
    .testimonial-card::after,
    .faq-card::before,
    .faq-card::after {
      display: none;
    }
    .counter { font-size: 2rem; font-weight: 600; }
    .muted { color: #6c757d; }
    .readmore { cursor: pointer; color: var(--di-primary); font-weight: 500; }
    .in-view { animation: fadeInUp .6s ease both; }
    /* Hero carousel */
    #diHeroCarousel .carousel-item { min-height: 520px; position: relative; background-size: cover; background-position: center; }
    #diHeroCarousel .di-hero-overlay { position: absolute; inset: 0; background: rgba(16,44,79,0.55); z-index: 1; }
    #diHeroCarousel .di-hero-content { position: relative; z-index: 2; }
    #diHeroCarousel .container { position: relative; z-index: 2; }
    /* Ensure all slideshow text is white for readability */
    #diHeroCarousel,
    #diHeroCarousel .di-hero-content,
    #diHeroCarousel h1,
    #diHeroCarousel h2,
    #diHeroCarousel h3,
    #diHeroCarousel p,
    #diHeroCarousel a,
    #diHeroCarousel .lead,
    #diHeroCarousel .muted { color: #ffffff; }
    #diHeroCarousel .di-btn-outline { color: #ffffff; border-color: #ffffff; }
    #diHeroCarousel .carousel-indicators li { height: 4px; }
    #diHeroCarousel .carousel-control-prev, #diHeroCarousel .carousel-control-next { z-index: 3; }
    .partner-card img { filter: grayscale(30%); opacity: .9; transition: filter .2s ease, opacity .2s ease; }
    .partner-card:hover img { filter: none; opacity: 1; }
    .partner-card img { height: 56px; width: auto; object-fit: contain; }
    .section-title { font-size: 2.25rem; font-weight: 700; letter-spacing: .2px; }
    .section-subtitle { font-size: 1.05rem; color: #6c757d; }
    @media (max-width: 576px) { .section-title { font-size: 1.85rem; } }
    .di-section-alt { background: #f7f9fc; }
    .di-section-gradient { background: linear-gradient(135deg, #0f2544 0%, #143a66 60%, #1b4f88 100%); color: #fff; }
    .di-section-contrast { background: #fff; }
    /* Inline badge for plan highlight */
    .di-badge { display: inline-block; background: #ffcc00; color: var(--di-primary); font-weight: 600; font-size: .8rem; padding: 2px 8px; border-radius: 999px; }
    html[dir='rtl'] .di-badge { margin-right: 8px; }
    .di-btn-group .btn + .btn { margin-left: 12px; }
    html[dir='rtl'] .di-btn-group .btn + .btn { margin-left: 0; margin-right: 12px; }
    /* Logical margin utilities (start/end) for RTL/LTR */
    .di-ms-2 { margin-left: .5rem; }
    .di-me-2 { margin-right: .5rem; }
    html[dir='rtl'] .di-ms-2 { margin-left: 0; margin-right: .5rem; }
    html[dir='rtl'] .di-me-2 { margin-right: 0; margin-left: .5rem; }
    /* Sections background helpers */
    .di-section-alt { background: #f7f9fc; }
    .di-section-gradient { background: linear-gradient(135deg, #0f2544 0%, #143a66 60%, #1b4f88 100%); color: #fff; }
    .di-section-contrast { background: #fff; }
    /* Hero offset and anchor scroll margin */
    .di-hero-carousel { margin-top: 0; }
    #features, #plans, #testimonials, #faq, #contact { scroll-margin-top: 80px; }
    /* RTL content alignment */
    html[dir='rtl'] .di-hero-content { text-align: right; }
    html[dir='rtl'] .card .card-body { text-align: right; }
    /* Collapse fallback */
    .collapse { display: none; }
    .collapse.show { display: block; }
    /* Pricing ribbons */
    .ribbon { position: absolute; top: 12px; right: -8px; background: #ffcc00; color: #102c4f; padding: 4px 10px; font-weight: 600; border-radius: 3px; }
    .plan-card { position: relative; }

    /* Feature & solutions cards */
    .feature-card {
      border-radius: 16px;
      border: 1px solid rgba(16,44,79,0.08);
      box-shadow: 0 10px 26px rgba(15,37,68,0.06);
      overflow: hidden;
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
      background: #ffffff;
    }
    .feature-card .card-body {
      padding: 1.6rem 1.7rem 1.7rem;
    }
    .feature-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--di-primary);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      margin-bottom: 1rem;
      box-shadow: 0 8px 20px rgba(16,44,79,0.4);
      font-size: 1.25rem;
    }
    .feature-icon--subtle {
      background: #ffffff;
      color: var(--di-primary);
      box-shadow: 0 6px 16px rgba(15,37,68,0.18);
    }
    .feature-card .card-title {
      font-weight: 700;
      margin-bottom: .5rem;
      color: var(--di-primary);
    }
    .feature-card .card-text {
      color: #4b5563;
      line-height: 1.7;
    }
    .feature-card::before {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at top left, rgba(16,44,79,0.12), transparent 55%);
      opacity: 0;
      pointer-events: none;
      transition: opacity .18s ease;
    }
    .feature-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 34px rgba(15,37,68,0.12);
      border-color: rgba(16,44,79,0.22);
    }
    .feature-card:hover::before {
      opacity: 1;
    }

    /* Solutions cards with subtle brand background */
    .solutions-card {
      background: linear-gradient(145deg, #f7f9fc 0%, #ffffff 45%, #f1f4fb 100%);
    }
    .solutions-card .card-title {
      display: flex;
      align-items: center;
      gap: .5rem;
    }
    .solutions-card .card-title::before {
      content: "";
      width: 10px;
      height: 10px;
      border-radius: 999px;
      background: var(--di-primary);
      box-shadow: 0 0 0 4px rgba(16,44,79,0.18);
    }
    /* Testimonials */
    .testimonial-card { border: 1px solid rgba(0,0,0,.08); border-radius: 8px; padding: 20px; height: 100%; }
    .testimonial-quote { font-style: italic; }
    .testimonial-author { display: flex; align-items: center; margin-top: 12px; }
    .testimonial-author img { width: 36px; height: 36px; border-radius: 50%; margin-right: 10px; }
    #diTestimonialsRow { scroll-behavior: smooth; }
    .testimonial-item { min-width: 280px; }
    @keyframes fadeInUp { from { opacity: 0; transform: translate3d(0, 14px, 0);} to { opacity: 1; transform: none; } }
    /* Ensure readable text inside cards on gradient section */
    .di-section-gradient .card { background: #ffffff; color: #212529; }
    .di-section-gradient .card .card-title,
    .di-section-gradient .card .card-text,
    .di-section-gradient .card ul,
    .di-section-gradient .card .readmore { color: #212529 !important; }
    .di-section-gradient .card .text-muted { color: #6c757d !important; }
    /* Featured plan styling */
    .plan-featured { border: 2px solid #ffd54f; box-shadow: 0 12px 28px rgba(255, 213, 79, .25); transform: translateY(-4px); }
    .plan-featured h4 { color: #102c4f; }
    .plan-featured .di-badge { background: #ffd54f; color: #102c4f; }
    /* FAQ accordion */
    .faq-accordion { max-width: 900px; margin: 0 auto; }
    .faq-card { background: #ffffff; border-radius: 12px; border: 1px solid rgba(16,44,79,0.12); box-shadow: 0 6px 16px rgba(15,37,68,0.05); margin-bottom: 1rem; overflow: hidden; }
    .faq-header { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #ffffff; border: 0; outline: none; cursor: pointer; text-align: left; font-weight: 600; color: var(--di-primary); }
    html[dir='rtl'] .faq-header { text-align: right; }
    .faq-header:hover { background: #f5f7fb; }
    .faq-title { flex: 1; }
    .faq-arrow { margin-left: .75rem; transition: transform .2s ease; color: #6c757d; display: flex; align-items: center; }
    html[dir='rtl'] .faq-arrow { margin-left: 0; margin-right: .75rem; }
    .faq-toggle[aria-expanded="true"] .faq-arrow { transform: rotate(180deg); }
    .faq-body { padding: 0 1.25rem 1rem; border-top: 1px solid #e9ecef; background: #ffffff; }
    .faq-body p { margin-bottom: 0; color: #6c757d; }

    /* Plan buttons styling */
    .plan-card .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: .9rem 1.6rem;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 999px;
    }
    .plan-card .btn.di-btn-outline,
    .plan-card .btn.btn-outline {
      border-width: 2px;
      background-color: #ffffff;
      border-color: var(--di-primary);
      color: var(--di-primary);
      box-shadow: 0 8px 18px rgba(15,37,68,0.18);
    }
    .plan-card .btn.di-btn-outline:hover,
    .plan-card .btn.btn-outline:hover {
      background-color: #f0f4fb;
      border-color: var(--di-primary);
      color: var(--di-primary);
    }
    .plan-featured .btn {
      background-color: var(--di-primary);
      border-color: var(--di-primary);
      color: #ffffff;
      box-shadow: 0 10px 24px rgba(16,44,79,0.4);
    }
    .plan-featured .btn:hover {
      filter: brightness(1.05);
      color: #ffffff;
    }

    /* Testimonials controls */
    .di-testimonials-controls {
      display: flex;
      justify-content: center;
      gap: 0.75rem;
      margin-top: 1rem;
    }
    .di-testimonials-btn {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background-color: var(--di-primary);
      color: #ffffff;
      border: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      box-shadow: 0 8px 20px rgba(16,44,79,0.45);
      cursor: pointer;
    }
    .di-testimonials-btn:hover {
      filter: brightness(1.05);
    }
  </style>
  @endpush

  <section class="di-bg-primary text-white">
    <div id="diHeroCarousel" class="carousel slide di-hero-carousel" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#diHeroCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#diHeroCarousel" data-slide-to="1"></li>
        <li data-target="#diHeroCarousel" data-slide-to="2"></li>
        <li data-target="#diHeroCarousel" data-slide-to="3"></li>
      </ol>
      <div class="carousel-inner">
        <div class="carousel-item active" style="background-image:url('{{ asset('assets/images/carousel/slide1.png') }}');">
          <div class="di-hero-overlay"></div>
          <div class="container py-5 di-hero-content">
            <div class="row">
              <div class="col-lg-8">
                <div class="mb-3"><img src="{{ asset('assets/images/logo-w.png') }}" alt="DataInsight" style="height:48px;"></div>
                <h1 class="display-4 mb-3">{{ __('Welcome to') }} <span>DataInsight</span></h1>
                <p class="lead mb-4">{{ __('We are a management consulting firm helping organizations make data-driven decisions.') }}</p>
                <p class="mb-4">{{ __('Our multi-tenant SaaS helps you manage tenants, users, and operations with isolated databases for each tenant.') }}</p>
                <div class="di-btn-group">
                  <a href="#plans" class="btn di-btn-primary btn-lg">{{ __('View Plans') }}</a>
                  <a href="#features" class="btn di-btn-outline-light btn-lg">{{ __('Learn More') }}</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item" style="background-image:url('{{ asset('assets/images/carousel/slide2.png') }}');">
          <div class="di-hero-overlay"></div>
          <div class="container py-5 di-hero-content">
            <div class="row">
              <div class="col-lg-8">
                <h2 class="mb-3">{{ __('Modern Dashboard') }}</h2>
                <p class="mb-4">{{ __('Responsive dashboard with charts, tables, and role-based access.') }}</p>
                <a href="#features" class="btn di-btn-outline btn-lg">{{ __('Learn More') }}</a>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item" style="background-image:url('{{ asset('assets/images/carousel/slide3.png') }}');">
          <div class="di-hero-overlay"></div>
          <div class="container py-5 di-hero-content">
            <div class="row">
              <div class="col-lg-8">
                <h2 class="mb-3">{{ __('Choose Your Plan') }}</h2>
                <p class="mb-4">{{ __('All plans include secure multi-tenant architecture.') }}</p>
                <a href="#plans" class="btn di-btn-primary btn-lg">{{ __('View Plans') }}</a>
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item" style="background-image:url('{{ asset('assets/images/carousel/slide4.png') }}');">
          <div class="di-hero-overlay"></div>
          <div class="container py-5 di-hero-content">
            <div class="row">
              <div class="col-lg-8">
                <h2 class="mb-3">{{ __('AI Insights for Your Documents') }}</h2>
                <p class="mb-4">
                  @if(app()->getLocale() === 'ar')
                    حوِّل المستندات غير المنظمة إلى لوحات معلومات غنية بالتحليلات والمؤشرات.
                  @else
                    {{ __('Turn unstructured documents into dashboards of actionable insights and metrics.') }}
                  @endif
                </p>
                <a href="#plans" class="btn di-btn-primary btn-lg">{{ __('Start Now') }}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <a class="carousel-control-prev" href="#diHeroCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#diHeroCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
  </section>

  <section id="features" class="py-5 di-section-alt">
    <div class="container">
      <div class="row mb-4">
        <div class="col text-center">
          <h2 class="section-title">{{ __('Key Benefits') }}</h2>
          <p class="section-subtitle">{{ __('Built for scalability, security, and speed.') }}</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 mb-4 in-view">
          <div class="card h-100 feature-card">
            <div class="card-body">
              <div class="feature-icon"><i class="mdi mdi-database"></i></div>
              <h5 class="card-title">{{ __('Isolated Databases') }}</h5>
              <p class="card-text">{{ __('Each tenant gets its own database for maximum isolation and security.') }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4 in-view">
          <div class="card h-100 feature-card">
            <div class="card-body">
              <div class="feature-icon"><i class="mdi mdi-rocket"></i></div>
              <h5 class="card-title">{{ __('Quick Onboarding') }}</h5>
              <p class="card-text">{{ __('Invite teams and start fast with streamlined setup and defaults.') }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4 in-view">
          <div class="card h-100 feature-card">
            <div class="card-body">
              <div class="feature-icon"><i class="mdi mdi-view-dashboard"></i></div>
              <h5 class="card-title">{{ __('Modern Dashboard') }}</h5>
              <p class="card-text">{{ __('Responsive dashboard with charts, tables, and role-based access.') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats -->
  <section class="py-5 di-section-contrast">
    <div class="container">
      <div class="row text-center">
        <div class="col-md-3 mb-4">
          <div class="counter di-text-primary" data-target="12800">0</div>
          <div class="muted">{{ __('Visitors') }}</div>
        </div>
        <div class="col-md-3 mb-4">
          <div class="counter di-text-primary" data-target="560">0</div>
          <div class="muted">{{ __('Customers') }}</div>
        </div>
        <div class="col-md-3 mb-4">
          <div class="counter di-text-primary" data-target="240">0</div>
          <div class="muted">{{ __('Active Tenants') }}</div>
        </div>
        <div class="col-md-3 mb-4">
          <div class="counter di-text-primary" data-target="99.99">0</div>
          <div class="muted">{{ __('Uptime %') }}</div>
        </div>
      </div>
    </div>
  </section>
  <section id="plans" class="py-5 di-section-gradient">
    <div class="container">
      <div class="row mb-4">
        <div class="col text-center">
          <h2 class="section-title">{{ __('Choose Your Plan') }}</h2>
          <p class="section-subtitle" style="color:#e2e6ea;">{{ __('All plans include secure multi-tenant architecture.') }}</p>
        </div>
      </div>
      <div class="row">
        @php
          $locale = app()->getLocale();
          $plansCollection = $plans ?? collect();
        @endphp
        @if($plansCollection->count())
        @foreach($plansCollection as $index => $plan)
          @php
            $mainFeatures = $plan->getFeaturesForLocale($locale);
            $moreFeatures = $plan->getMoreFeaturesForLocale($locale);
            $collapseId = 'plan-more-'.$plan->id;
            $isFeatured = $plan->is_featured;
          @endphp
          <div class="col-md-4 mb-4">
            <div class="card h-100 {{ $isFeatured ? 'border-primary plan-card plan-featured' : 'border plan-card' }}">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-2">
                  <h4 class="mb-0 di-me-2">{{ $plan->getNameForLocale($locale) }}</h4>
                  @if($isFeatured)
                    <span class="di-badge di-ms-2">{{ __('Most Popular') }}</span>
                  @endif
                </div>
                @if($plan->getSubtitleForLocale($locale))
                  <p class="text-muted mb-2">{{ $plan->getSubtitleForLocale($locale) }}</p>
                @endif
                <h2 class="mt-3">
                  {{ $plan->price_monthly > 0 ? $plan->price_monthly : 0 }}
                  <span class="text-muted">{{ $plan->currency }}/mo</span>
                </h2>
                @if(count($mainFeatures))
                  <ul class="list-unstyled mt-3 mb-3">
                    @foreach($mainFeatures as $feat)
                      <li><i class="mdi mdi-check-circle-outline di-text-primary"></i> {{ $feat }}</li>
                    @endforeach
                  </ul>
                @endif
                @if(count($moreFeatures))
                  <div class="collapse" id="{{ $collapseId }}">
                    <ul class="list-unstyled mb-3">
                      @foreach($moreFeatures as $feat)
                        <li>• {{ $feat }}</li>
                      @endforeach
                    </ul>
                  </div>
                  <span class="readmore" data-toggle="collapse" data-target="#{{ $collapseId }}">{{ __('Show more') }}</span>
                @endif
                <a href="{{ route('tenants.signup', ['plan' => $plan->code]) }}" class="btn {{ $isFeatured ? 'di-btn-primary' : 'di-btn-outline' }} mt-auto">{{ __('Subscribe') }}</a>
              </div>
            </div>
          </div>
        @endforeach
        @else
          {{-- Fallback: no plans configured --}}
          <div class="col-12 text-center text-white-50">
            {{ __('No plans are configured yet. Please contact the administrator.') }}
          </div>
        @endif
      </div>
    </div>
  </section>

  <!-- Solutions (new distinct section) -->
  <section id="solutions" class="py-5 di-section-contrast">
    <div class="container">
      <div class="row mb-4">
        <div class="col text-center">
          <h2 class="section-title">{{ __('app.solutions_title') }}</h2>
          <p class="section-subtitle">{{ __('app.solutions_subtitle') }}</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 mb-4 in-view">
          <div class="card h-100 feature-card solutions-card">
            <div class="card-body">
              <div class="feature-icon feature-icon--subtle"><i class="mdi mdi-cogs"></i></div>
              <h5 class="card-title">{{ __('app.operations_suite') }}</h5>
              <p class="card-text">{{ __('app.operations_desc') }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4 in-view">
          <div class="card h-100 feature-card solutions-card">
            <div class="card-body">
              <div class="feature-icon feature-icon--subtle"><i class="mdi mdi-chart-bar"></i></div>
              <h5 class="card-title">{{ __('app.analytics_hub') }}</h5>
              <p class="card-text">{{ __('app.analytics_desc') }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4 in-view">
          <div class="card h-100 feature-card solutions-card">
            <div class="card-body">
              <div class="feature-icon feature-icon--subtle"><i class="mdi mdi-shield-lock"></i></div>
              <h5 class="card-title">{{ __('app.security_center') }}</h5>
              <p class="card-text">{{ __('app.security_desc') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Partners -->
  <section class="py-5 di-section-alt">
    <div class="container">
      <div class="row mb-4">
        <div class="col text-center">
          <h2 class="section-title di-text-primary">{{ __('Trusted by partners') }}</h2>
          <p class="section-subtitle">{{ __('We collaborate with leading organizations.') }}</p>
        </div>
      </div>
      <div class="row justify-content-center text-center align-items-center">
        @php
          $partners = [
            'assets/images/brand_icons/oval.jpg',
            'assets/images/brand_icons/bitmap.jpg',
            'assets/images/brand_icons/oval-copy.jpg',
            'assets/images/brand_icons/oval.jpg',
            'assets/images/brand_icons/bitmap.jpg',
            'assets/images/brand_icons/oval-copy.jpg',
            'assets/images/brand_icons/oval.jpg',
          ];
        @endphp
        @foreach($partners as $p)
        <div class="col-6 col-md-3 col-lg-2 mb-3 d-flex justify-content-center">
          <div class="partner-card p-3 border rounded w-100 d-flex justify-content-center">
            <img src="{{ asset($p) }}" alt="{{ __('Partner Logo') }}">
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Testimonials with horizontal slider -->
  <section id="testimonials" class="py-5 di-section-contrast">
    <div class="container">
      <div class="row mb-4">
        <div class="col-12 text-center">
          <h2 class="section-title di-text-primary">{{ __('What our customers say') }}</h2>
          <p class="section-subtitle">{{ __('Real stories from teams using DataInsight') }}</p>
          <div class="di-testimonials-controls">
            <button id="diTestimonialsPrev" type="button" class="di-testimonials-btn" aria-label="Previous testimonial"><i class="mdi mdi-chevron-left"></i></button>
            <button id="diTestimonialsNext" type="button" class="di-testimonials-btn" aria-label="Next testimonial"><i class="mdi mdi-chevron-right"></i></button>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div id="diTestimonialsRow" class="d-flex flex-nowrap overflow-auto">
            <div class="testimonial-item pr-3">
              <div class="testimonial-card h-100">
                <p class="testimonial-quote">“{{ __('DataInsight helped us launch multi-tenant ops in weeks, not months.') }}”</p>
                <div class="testimonial-author">
                  <img src="{{ asset('assets/images/faces/face10.jpg') }}" alt="">
                  <div>
                    <strong>{{ __('Marian Garner') }}</strong><br>
                    <span class="muted">{{ __('COO, FinTech Co.') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="testimonial-item pr-3">
              <div class="testimonial-card h-100">
                <p class="testimonial-quote">“{{ __('The isolation and security features are best-in-class.') }}”</p>
                <div class="testimonial-author">
                  <img src="{{ asset('assets/images/faces/face12.jpg') }}" alt="">
                  <div>
                    <strong>{{ __('David Grey') }}</strong><br>
                    <span class="muted">{{ __('CTO, HealthTech') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="testimonial-item pr-3">
              <div class="testimonial-card h-100">
                <p class="testimonial-quote">“{{ __('We scaled to dozens of tenants with zero friction.') }}”</p>
                <div class="testimonial-author">
                  <img src="{{ asset('assets/images/faces/face3.jpg') }}" alt="">
                  <div>
                    <strong>{{ __('Travis Jenkins') }}</strong><br>
                    <span class="muted">{{ __('Head of Ops, Retail') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="testimonial-item pr-3">
              <div class="testimonial-card h-100">
                <p class="testimonial-quote">“{{ __('We finally have a single place to manage all tenants securely.') }}”</p>
                <div class="testimonial-author">
                  <img src="{{ asset('assets/images/faces/face10.jpg') }}" alt="">
                  <div>
                    <strong>{{ __('Sarah Ibrahim') }}</strong><br>
                    <span class="muted">{{ __('Operations Lead, SaaS Group') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="testimonial-item pr-3">
              <div class="testimonial-card h-100">
                <p class="testimonial-quote">“{{ __('The analytics and reports changed how our leadership sees the business.') }}”</p>
                <div class="testimonial-author">
                  <img src="{{ asset('assets/images/faces/face12.jpg') }}" alt="">
                  <div>
                    <strong>{{ __('Ahmed Al-Qahtani') }}</strong><br>
                    <span class="muted">{{ __('Head of BI, Retail Group') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="testimonial-item pr-3">
              <div class="testimonial-card h-100">
                <p class="testimonial-quote">“{{ __('Multi-tenant security and audit trails are exactly what we needed.') }}”</p>
                <div class="testimonial-author">
                  <img src="{{ asset('assets/images/faces/face3.jpg') }}" alt="">
                  <div>
                    <strong>{{ __('Lama Al-Saud') }}</strong><br>
                    <span class="muted">{{ __('CISO, Financial Services') }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ moved to a dedicated page: see route('static.faq') -->
</div>
@endsection

@push('custom-scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    // Simple counter animation
    document.querySelectorAll('.counter').forEach(function(el){
      var target = parseFloat(el.getAttribute('data-target'));
      var isPercent = String(target).indexOf('.') !== -1;
      var duration = 1200;
      var start = null;
      function step(ts){
        if(!start) start = ts;
        var progress = Math.min((ts - start)/duration, 1);
        var val = target * progress;
        el.textContent = isPercent ? val.toFixed(2) : Math.floor(val);
        if(progress < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    });

    // Reveal on scroll
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(e){ if(e.isIntersecting) e.target.classList.add('in-view'); });
    }, { threshold: 0.2 });
    document.querySelectorAll('.feature-card, .plan-card, .partner-card').forEach(function(el){ io.observe(el); });

    // Minimal carousel logic (vanilla JS)
    var carousel = document.getElementById('diHeroCarousel');
    if(carousel){
      var items = carousel.querySelectorAll('.carousel-item');
      var indicators = carousel.querySelectorAll('.carousel-indicators li');
      var prev = carousel.querySelector('.carousel-control-prev');
      var next = carousel.querySelector('.carousel-control-next');
      var index = 0;

      function show(i){
        items[index].classList.remove('active');
        indicators[index].classList.remove('active');
        index = (i + items.length) % items.length;
        items[index].classList.add('active');
        indicators[index].classList.add('active');
      }

      indicators.forEach(function(ind, i){ ind.addEventListener('click', function(){ show(i); }); });
      if(prev) prev.addEventListener('click', function(e){ e.preventDefault(); show(index - 1); });
      if(next) next.addEventListener('click', function(e){ e.preventDefault(); show(index + 1); });

      setInterval(function(){ show(index + 1); }, 6000);
    }

    // Navbar toggle
    var toggle = document.querySelector('.di-nav-toggle');
    var links = document.getElementById('diNavLinks');
    if(toggle && links){
      toggle.addEventListener('click', function(){ links.classList.toggle('show'); });
    }

    // Simple collapse toggles for elements using data-toggle="collapse"
    document.querySelectorAll('[data-toggle="collapse"]').forEach(function(trigger){
      var targetSel = trigger.getAttribute('data-target');
      var target = document.querySelector(targetSel);
      if(!target) return;
      trigger.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var willShow = !target.classList.contains('show');

        // Accordion behavior for FAQ: close siblings when opening a new one
        var accordion = trigger.closest('.faq-accordion');
        if(accordion && willShow){
          accordion.querySelectorAll('.collapse.show').forEach(function(openEl){
            if(openEl !== target){ openEl.classList.remove('show'); }
          });
          accordion.querySelectorAll('[data-toggle="collapse"][aria-expanded="true"]').forEach(function(openTrigger){
            if(openTrigger !== trigger){ openTrigger.setAttribute('aria-expanded', 'false'); }
          });
        }

        target.classList.toggle('show', willShow);
        trigger.setAttribute('aria-expanded', willShow ? 'true' : 'false');
      });
    });

    // Testimonials horizontal slider (with arrows + auto-scroll)
    var testimonialsRow = document.getElementById('diTestimonialsRow');
    var testimonialsPrev = document.getElementById('diTestimonialsPrev');
    var testimonialsNext = document.getElementById('diTestimonialsNext');
    if(testimonialsRow && testimonialsPrev && testimonialsNext){
      var item = testimonialsRow.querySelector('.testimonial-item');
      var step = item ? (item.offsetWidth + 16) : 320;

      function slideTestimonials(direction){
        if(direction === 'next'){
          testimonialsRow.scrollLeft += step;
        } else {
          testimonialsRow.scrollLeft -= step;
        }
      }

      testimonialsPrev.addEventListener('click', function(){ slideTestimonials('prev'); });
      testimonialsNext.addEventListener('click', function(){ slideTestimonials('next'); });

      // Auto-scroll every few seconds to feel like a carousel
      setInterval(function(){ slideTestimonials('next'); }, 7000);
    }
  });
</script>
@endpush
