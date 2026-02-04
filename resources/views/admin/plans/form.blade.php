@extends('layout.master')

@section('content')
    <div class="content-wrapper tenant-layout">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $mode === 'create' ? 'إضافة باقة جديدة' : 'تعديل الباقة' }}</h4>
                <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-light">عودة للباقات</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST"
                    action="{{ $mode === 'create' ? route('admin.subscription-plans.store') : route('admin.subscription-plans.update', $plan) }}">
                    @csrf
                    @if ($mode === 'edit')
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">رمز الباقة (code)</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                value="{{ old('code', $plan->code) }}" {{ $mode === 'edit' ? 'readonly' : '' }}>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">مثال: free, pro, business (يُستخدم في الروابط وربط المستأجر)</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">السعر الشهري</label>
                            <input type="number" step="0.01" min="0" name="price_monthly"
                                class="form-control @error('price_monthly') is-invalid @enderror"
                                value="{{ old('price_monthly', $plan->price_monthly) }}">
                            @error('price_monthly')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">العملة</label>
                            <input type="text" name="currency"
                                class="form-control @error('currency') is-invalid @enderror"
                                value="{{ old('currency', $plan->currency ?: 'USD') }}">
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم الباقة (AR)</label>
                            <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                                value="{{ old('name_ar', $plan->name_ar) }}">
                            @error('name_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Plan Name (EN)</label>
                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
                                value="{{ old('name_en', $plan->name_en) }}">
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">وصف قصير (AR)</label>
                            <input type="text" name="subtitle_ar"
                                class="form-control @error('subtitle_ar') is-invalid @enderror"
                                value="{{ old('subtitle_ar', $plan->subtitle_ar) }}">
                            @error('subtitle_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Short Subtitle (EN)</label>
                            <input type="text" name="subtitle_en"
                                class="form-control @error('subtitle_en') is-invalid @enderror"
                                value="{{ old('subtitle_en', $plan->subtitle_en) }}">
                            @error('subtitle_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الخصائص الرئيسية (AR)</label>
                            <textarea name="features_ar" rows="6" class="form-control @error('features_ar') is-invalid @enderror"
                                placeholder="اكتب كل سطر كميزة منفصلة">{{ old('features_ar', $plan->features_ar) }}</textarea>
                            @error('features_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">كل سطر = نقطة في قائمة الخصائص.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Main Features (EN)</label>
                            <textarea name="features_en" rows="6" class="form-control @error('features_en') is-invalid @enderror"
                                placeholder="One feature per line">{{ old('features_en', $plan->features_en) }}</textarea>
                            @error('features_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">خصائص إضافية (AR)</label>
                            <textarea name="more_features_ar" rows="5" class="form-control @error('more_features_ar') is-invalid @enderror"
                                placeholder="سطر لكل ميزة إضافية">{{ old('more_features_ar', $plan->more_features_ar) }}</textarea>
                            @error('more_features_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">More Features (EN)</label>
                            <textarea name="more_features_en" rows="5" class="form-control @error('more_features_en') is-invalid @enderror"
                                placeholder="One extra feature per line">{{ old('more_features_en', $plan->more_features_en) }}</textarea>
                            @error('more_features_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ترتيب العرض</label>
                            <input type="number" min="1" name="sort_order"
                                class="form-control @error('sort_order') is-invalid @enderror"
                                value="{{ old('sort_order', $plan->sort_order ?: 1) }}">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-center">
                            <div class="form-check mt-3 mt-md-4">
                                <input class="form-check-input" type="checkbox" value="1" id="is_active"
                                    name="is_active" {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">الباقة نشطة (تظهر في الموقع)</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-center">
                            <div class="form-check mt-3 mt-md-4">
                                <input class="form-check-input" type="checkbox" value="1" id="is_featured"
                                    name="is_featured"
                                    {{ old('is_featured', $plan->is_featured ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">باقة مميزة (تظهر بشكل بارز)</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
