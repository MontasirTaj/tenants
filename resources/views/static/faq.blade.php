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
    .faq-accordion { max-width: 960px; margin: 2.5rem auto 3rem; }
    .faq-card {
      background: #ffffff;
      border-radius: 12px;
      border: 1px solid rgba(16,44,79,0.12);
      box-shadow: 0 6px 16px rgba(15,37,68,0.05);
      margin-bottom: 1rem;
      overflow: hidden;
    }
    .faq-header {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 1.25rem;
      background: #ffffff;
      border: 0;
      outline: none;
      cursor: pointer;
      text-align: left;
      font-weight: 600;
      color: var(--di-primary);
    }
    html[dir='rtl'] .faq-header { text-align: right; }
    .faq-header:hover { background: #f5f7fb; }
    .faq-title { flex: 1; }
    .faq-arrow { margin-left: .75rem; transition: transform .2s ease; color: #6c757d; display: flex; align-items: center; }
    html[dir='rtl'] .faq-arrow { margin-left: 0; margin-right: .75rem; }
    .faq-toggle[aria-expanded="true"] .faq-arrow { transform: rotate(180deg); }
    .faq-body { padding: 0 1.25rem 1rem; border-top: 1px solid #e9ecef; background: #ffffff; }
    .faq-body p { margin-bottom: 0; color: #4b5563; line-height: 1.7; }
  </style>
  @endpush

  <section class="static-hero">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 d-flex align-items-center">
          <div class="static-hero-icon">
            <i class="mdi mdi-help-circle-outline"></i>
          </div>
          <div>
            <h1 class="static-hero-title">{{ __('app.faq_title') }}</h1>
            <p class="static-hero-subtitle">{{ __('app.faq_subtitle') }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <div class="faq-accordion">
        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_1" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q1') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_1" class="faq-body collapse">
            <p>{{ __('app.faq_a1') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_2" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q2') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_2" class="faq-body collapse">
            <p>{{ __('app.faq_a2') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_3" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q3') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_3" class="faq-body collapse">
            <p>{{ __('app.faq_a3') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_4" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q4') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_4" class="faq-body collapse">
            <p>{{ __('app.faq_a4') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_5" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q5') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_5" class="faq-body collapse">
            <p>{{ __('app.faq_a5') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_6" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q6') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_6" class="faq-body collapse">
            <p>{{ __('app.faq_a6') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_7" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q7') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_7" class="faq-body collapse">
            <p>{{ __('app.faq_a7') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_8" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q8') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_8" class="faq-body collapse">
            <p>{{ __('app.faq_a8') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_9" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q9') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_9" class="faq-body collapse">
            <p>{{ __('app.faq_a9') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_10" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q10') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_10" class="faq-body collapse">
            <p>{{ __('app.faq_a10') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_11" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q11') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_11" class="faq-body collapse">
            <p>{{ __('app.faq_a11') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_12" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q12') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_12" class="faq-body collapse">
            <p>{{ __('app.faq_a12') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_13" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q13') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_13" class="faq-body collapse">
            <p>{{ __('app.faq_a13') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_14" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q14') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_14" class="faq-body collapse">
            <p>{{ __('app.faq_a14') }}</p>
          </div>
        </div>

        <div class="faq-card">
          <button class="faq-header faq-toggle" type="button" data-toggle="collapse" data-target="#faq_static_15" aria-expanded="false">
            <span class="faq-title">{{ __('app.faq_q15') }}</span>
            <span class="faq-arrow"><i class="mdi mdi-chevron-down"></i></span>
          </button>
          <div id="faq_static_15" class="faq-body collapse">
            <p>{{ __('app.faq_a15') }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@push('custom-scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('[data-toggle="collapse"]').forEach(function(trigger){
      var targetSel = trigger.getAttribute('data-target');
      var target = document.querySelector(targetSel);
      if(!target) return;
      trigger.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        var willShow = !target.classList.contains('show');
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
  });
</script>
@endpush
