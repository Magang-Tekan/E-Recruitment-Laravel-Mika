<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyShowcase extends Model
{
    protected $fillable = [
        'company_id',
        'tag',
        'tag_en',
        'title',
        'title_en',
        'description',
        'description_en',
        'img1',
        'img2',
        'img3',
        'badge_title',
        'badge_title_en',
        'badge_sub',
        'badge_sub_en',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function ($showcase) {
            // Auto translate to English if English fields are left empty
            if (empty($showcase->title_en) && !empty($showcase->title)) {
                $showcase->title_en = \App\Services\TranslationService::translate($showcase->title);
            }
            if (empty($showcase->description_en) && !empty($showcase->description)) {
                $showcase->description_en = \App\Services\TranslationService::translate($showcase->description);
            }
            if (empty($showcase->tag_en) && !empty($showcase->tag)) {
                $showcase->tag_en = \App\Services\TranslationService::translate($showcase->tag);
            }
            if (empty($showcase->badge_title_en) && !empty($showcase->badge_title)) {
                $showcase->badge_title_en = \App\Services\TranslationService::translate($showcase->badge_title);
            }
            if (empty($showcase->badge_sub_en) && !empty($showcase->badge_sub)) {
                $showcase->badge_sub_en = \App\Services\TranslationService::translate($showcase->badge_sub);
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('frontend_company_showcases');
            \Illuminate\Support\Facades\Cache::forget('frontend_company_showcases_id');
            \Illuminate\Support\Facades\Cache::forget('frontend_company_showcases_en');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('frontend_company_showcases');
            \Illuminate\Support\Facades\Cache::forget('frontend_company_showcases_id');
            \Illuminate\Support\Facades\Cache::forget('frontend_company_showcases_en');
        });
    }

    public function getLocalizedTitleAttribute(): string
    {
        if (app()->getLocale() === 'en' && !empty($this->title_en)) {
            return $this->title_en;
        }
        return __(trim($this->title));
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $this->description ? __(trim($this->description)) : null;
    }

    public function getLocalizedTagAttribute(): ?string
    {
        if (app()->getLocale() === 'en' && !empty($this->tag_en)) {
            return $this->tag_en;
        }
        return $this->tag ? __(trim($this->tag)) : __('Acara & Kolaborasi');
    }

    public function getLocalizedBadgeTitleAttribute(): ?string
    {
        if (app()->getLocale() === 'en' && !empty($this->badge_title_en)) {
            return $this->badge_title_en;
        }
        return $this->badge_title ? __(trim($this->badge_title)) : 'EVENT';
    }

    public function getLocalizedBadgeSubAttribute(): ?string
    {
        if (app()->getLocale() === 'en' && !empty($this->badge_sub_en)) {
            return $this->badge_sub_en;
        }
        return $this->badge_sub ? __(trim($this->badge_sub)) : null;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function resolveImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');
        if (Str::startsWith($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }

        // Gunakan path root-relatif dinamis agar URL gambar tidak terkunci ke domain/port tertentu (misal 127.0.0.1:8000 vs localhost) saat dicache
        $base = request()->getBasePath();
        $prefix = $base ? rtrim($base, '/') : '';

        return $prefix . '/storage/' . $cleanPath;
    }

    public function getImg1UrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->img1);
    }

    public function getImg2UrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->img2);
    }

    public function getImg3UrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->img3);
    }
}
