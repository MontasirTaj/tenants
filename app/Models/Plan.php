<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'code',
        'is_active',
        'is_featured',
        'sort_order',
        'price_monthly',
        'currency',
        'name_en',
        'name_ar',
        'subtitle_en',
        'subtitle_ar',
        'features_en',
        'features_ar',
        'more_features_en',
        'more_features_ar',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price_monthly' => 'float',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getNameForLocale(string $locale): string
    {
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getSubtitleForLocale(string $locale): ?string
    {
        return $locale === 'ar' ? $this->subtitle_ar : $this->subtitle_en;
    }

    public function getFeaturesForLocale(string $locale): array
    {
        $value = $locale === 'ar' ? $this->features_ar : $this->features_en;

        return $this->splitLines($value);
    }

    public function getMoreFeaturesForLocale(string $locale): array
    {
        $value = $locale === 'ar' ? $this->more_features_ar : $this->more_features_en;

        return $this->splitLines($value);
    }

    protected function splitLines(?string $value): array
    {
        if (! $value) {
            return [];
        }

        $lines = preg_split("/(\r\n|\r|\n)/", $value);

        return array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));
    }
}
