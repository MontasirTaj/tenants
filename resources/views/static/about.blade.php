@extends('layout.master-mini')

@section('content')
<div class="content-wrapper di-section-alt">
  @push('style')
  <style>
    :root { --di-primary: #102c4f; }
    .static-hero {
      background: linear-gradient(135deg, #0f2544 0%, #143a66 60%, #1b4f88 100%);
      color: #fff;
      padding: 3rem 0 2.5rem;
    }
    .static-hero-icon {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: rgba(255,255,255,0.12);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin-inline-end: 0.75rem;
    }
    .static-hero-title {
      font-size: 2.1rem;
      font-weight: 700;
      margin-bottom: .35rem;
    }
    .static-hero-subtitle {
      font-size: 1.02rem;
      color: #e5edf7;
      opacity: 1;
    }

    .about-section { padding: 3rem 0 3.5rem; }
    .about-badge {
      display: inline-block;
      padding: .25rem .9rem;
      border-radius: 999px;
      background: rgba(16,44,79,0.06);
      color: #1f2933;
      font-size: .78rem;
      font-weight: 600;
      letter-spacing: .04em;
      text-transform: uppercase;
      margin-bottom: .9rem;
    }

    .why-card {
      border-radius: 18px;
      border: none;
      box-shadow: 0 18px 40px rgba(15,37,68,0.12);
      padding: 1.8rem 1.9rem;
      height: 100%;
      position: relative;
      overflow: hidden;
      color: #ffffff;
    }
    .why-card::before {
      content: "";
      position: absolute;
      inset-inline-start: -40px;
      inset-block-start: -40px;
      width: 110px;
      height: 110px;
      border-radius: 50%;
      background: rgba(255,255,255,0.12);
      opacity: .65;
    }
    .why-icon {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      background: rgba(255,255,255,0.16);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      margin-bottom: 1rem;
      position: relative;
      z-index: 1;
    }
    .why-title {
      font-size: 1.05rem;
      font-weight: 700;
      margin-bottom: .4rem;
      position: relative;
      z-index: 1;
    }
    .why-body {
      font-size: .9rem;
      line-height: 1.7;
      opacity: .95;
      position: relative;
      z-index: 1;
    }

    .why-card-theme-1 { background: linear-gradient(135deg, #102c4f 0%, #1b4f88 100%); }
    .why-card-theme-2 { background: linear-gradient(135deg, #0b7285 0%, #1098ad 100%); }
    .why-card-theme-3 { background: linear-gradient(135deg, #9f1239 0%, #e11d48 100%); }
    .why-card-theme-4 { background: linear-gradient(135deg, #7c2d12 0%, #ea580c 100%); }
    .why-card-theme-5 { background: linear-gradient(135deg, #14532d 0%, #16a34a 100%); }
    .why-card-theme-6 { background: linear-gradient(135deg, #1d2a4a 0%, #4b5563 100%); }
    .why-card-theme-7 { background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%); }
    .why-card-theme-8 { background: linear-gradient(135deg, #6b21a8 0%, #db2777 100%); }
    .why-card-theme-9 { background: linear-gradient(135deg, #164e63 0%, #0891b2 100%); }
    .why-card-theme-10 { background: linear-gradient(135deg, #0f172a 0%, #1f2937 100%); }
  </style>
  @endpush

  <section class="static-hero">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 d-flex align-items-center">
          <div class="static-hero-icon">
            <i class="mdi mdi-information-outline"></i>
          </div>
          <div>
            <h1 class="static-hero-title">{{ __('app.about_title') }}</h1>
            <p class="static-hero-subtitle">{{ __('app.about_subtitle') }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="about-section">
    <div class="container">
      <div class="row justify-content-center mb-4">
        <div class="col-lg-8 text-center">
          <span class="about-badge">{{ __('Why teams choose us') }}</span>
          <h2 class="section-title mb-2">{{ __('app.about_title') }}</h2>
          <p class="section-subtitle">{{ __('app.about_subtitle') }}</p>
        </div>
      </div>

      @php
        $features = [
          // 1) هيكلة بيانات احترافية
          ['icon' => 'mdi-database', 'theme' => 'why-card-theme-1', 'title' => 'about_feature_1_title', 'body' => 'about_feature_1_body'],
          ['icon' => 'mdi-view-dashboard-outline', 'theme' => 'why-card-theme-2', 'title' => 'about_feature_2_title', 'body' => 'about_feature_2_body'],
          // 3) دعم تعدد الفروع
          ['icon' => 'mdi-store', 'theme' => 'why-card-theme-3', 'title' => 'about_feature_3_title', 'body' => 'about_feature_3_body'],
          ['icon' => 'mdi-shield-account-outline', 'theme' => 'why-card-theme-4', 'title' => 'about_feature_4_title', 'body' => 'about_feature_4_body'],
          ['icon' => 'mdi-file-chart-outline', 'theme' => 'why-card-theme-5', 'title' => 'about_feature_5_title', 'body' => 'about_feature_5_body'],
          ['icon' => 'mdi-account-group-outline', 'theme' => 'why-card-theme-6', 'title' => 'about_feature_6_title', 'body' => 'about_feature_6_body'],
          // 7) أمان وحفظ نسخ احتياطية
          ['icon' => 'mdi-lock', 'theme' => 'why-card-theme-7', 'title' => 'about_feature_7_title', 'body' => 'about_feature_7_body'],
          ['icon' => 'mdi-account-multiple-check', 'theme' => 'why-card-theme-8', 'title' => 'about_feature_8_title', 'body' => 'about_feature_8_body'],
          ['icon' => 'mdi-link-variant', 'theme' => 'why-card-theme-9', 'title' => 'about_feature_9_title', 'body' => 'about_feature_9_body'],
          // 10) تتبع للنشاط وسجل عمليات
          ['icon' => 'mdi-clipboard-text', 'theme' => 'why-card-theme-10', 'title' => 'about_feature_10_title', 'body' => 'about_feature_10_body'],
          // 11) تحديثات مستمرة
          ['icon' => 'mdi-update', 'theme' => 'why-card-theme-2', 'title' => 'about_feature_11_title', 'body' => 'about_feature_11_body'],
          ['icon' => 'mdi-gift-outline', 'theme' => 'why-card-theme-5', 'title' => 'about_feature_12_title', 'body' => 'about_feature_12_body'],
          ['icon' => 'mdi-clock-start', 'theme' => 'why-card-theme-1', 'title' => 'about_feature_13_title', 'body' => 'about_feature_13_body'],
          ['icon' => 'mdi-chart-line-variant', 'theme' => 'why-card-theme-3', 'title' => 'about_feature_14_title', 'body' => 'about_feature_14_body'],
          ['icon' => 'mdi-trending-up', 'theme' => 'why-card-theme-4', 'title' => 'about_feature_15_title', 'body' => 'about_feature_15_body'],
          ['icon' => 'mdi-palette-outline', 'theme' => 'why-card-theme-6', 'title' => 'about_feature_16_title', 'body' => 'about_feature_16_body'],
          ['icon' => 'mdi-headset', 'theme' => 'why-card-theme-7', 'title' => 'about_feature_17_title', 'body' => 'about_feature_17_body'],
          ['icon' => 'mdi-cellphone-link', 'theme' => 'why-card-theme-8', 'title' => 'about_feature_18_title', 'body' => 'about_feature_18_body'],
          ['icon' => 'mdi-cash-multiple', 'theme' => 'why-card-theme-9', 'title' => 'about_feature_19_title', 'body' => 'about_feature_19_body'],
          ['icon' => 'mdi-target-variant', 'theme' => 'why-card-theme-10', 'title' => 'about_feature_20_title', 'body' => 'about_feature_20_body'],
        ];
      @endphp

      <div class="row">
        @foreach($features as $index => $feature)
          <div class="col-md-6 mb-4">
            <div class="why-card {{ $feature['theme'] }}">
              <div class="why-icon">
                <i class="mdi {{ $feature['icon'] }}"></i>
              </div>
              <div class="why-title">{{ __('app.'.$feature['title']) }}</div>
              <div class="why-body">{{ __('app.'.$feature['body']) }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
</div>
@endsection
