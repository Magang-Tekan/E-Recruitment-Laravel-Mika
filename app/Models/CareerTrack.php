<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CareerTrack extends Model
{
    protected $fillable = [
        'num',
        'badge',
        'badge_en',
        'prefix',
        'prefix_en',
        'accent',
        'accent_en',
        'description',
        'description_en',
        'image',
        'back_badge',
        'back_badge_en',
        'back_title',
        'back_title_en',
        'highlights',
        'highlights_en',
        'cta_text',
        'cta_text_en',
        'experience_level',
        'custom_url',
        'is_dark',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'highlights' => 'array',
        'highlights_en' => 'array',
        'is_dark' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function ($track) {
            if (empty($track->badge_en) && !empty($track->badge)) {
                $track->badge_en = \App\Services\TranslationService::translate($track->badge);
            }
            if (empty($track->prefix_en) && !empty($track->prefix)) {
                $track->prefix_en = \App\Services\TranslationService::translate($track->prefix);
            }
            if (empty($track->accent_en) && !empty($track->accent)) {
                $track->accent_en = \App\Services\TranslationService::translate($track->accent);
            }
            if (empty($track->description_en) && !empty($track->description)) {
                $track->description_en = \App\Services\TranslationService::translate($track->description);
            }
            if (empty($track->back_badge_en) && !empty($track->back_badge)) {
                $track->back_badge_en = \App\Services\TranslationService::translate($track->back_badge);
            }
            if (empty($track->back_title_en) && !empty($track->back_title)) {
                $track->back_title_en = \App\Services\TranslationService::translate($track->back_title);
            }
            if (empty($track->cta_text_en) && !empty($track->cta_text)) {
                $track->cta_text_en = \App\Services\TranslationService::translate($track->cta_text);
            }
        });

        static::saved(function () {
            self::clearCaches();
        });

        static::deleted(function () {
            self::clearCaches();
        });
    }

    public static function clearCaches(): void
    {
        \Illuminate\Support\Facades\Cache::forget('frontend_career_tracks_id');
        \Illuminate\Support\Facades\Cache::forget('frontend_career_tracks_en');
        \Illuminate\Support\Facades\Cache::forget('home_job_counts_by_level');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function getLocalizedBadgeAttribute(): string
    {
        if (app()->getLocale() === 'en' && !empty($this->badge_en)) {
            return $this->badge_en;
        }
        return $this->badge ? __($this->badge) : '';
    }

    public function getLocalizedPrefixAttribute(): string
    {
        if (app()->getLocale() === 'en' && !empty($this->prefix_en)) {
            return $this->prefix_en;
        }
        return $this->prefix ? __($this->prefix) : '';
    }

    public function getLocalizedAccentAttribute(): string
    {
        if (app()->getLocale() === 'en' && !empty($this->accent_en)) {
            return $this->accent_en;
        }
        return $this->accent ? __($this->accent) : '';
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        if (app()->getLocale() === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $this->description ? __($this->description) : null;
    }

    public function getLocalizedBackBadgeAttribute(): ?string
    {
        if (app()->getLocale() === 'en' && !empty($this->back_badge_en)) {
            return $this->back_badge_en;
        }
        return $this->back_badge ? __($this->back_badge) : '';
    }

    public function getLocalizedBackTitleAttribute(): string
    {
        if (app()->getLocale() === 'en' && !empty($this->back_title_en)) {
            return $this->back_title_en;
        }
        return $this->back_title ? __($this->back_title) : '';
    }

    public function getLocalizedCtaTextAttribute(): string
    {
        if (app()->getLocale() === 'en' && !empty($this->cta_text_en)) {
            return $this->cta_text_en;
        }
        return $this->cta_text ? __($this->cta_text) : '';
    }

    public function getLocalizedHighlightsAttribute(): array
    {
        if (app()->getLocale() === 'en' && !empty($this->highlights_en) && is_array($this->highlights_en)) {
            return $this->highlights_en;
        }

        $items = $this->highlights;
        if (!is_array($items)) {
            $items = json_decode($items, true) ?: [];
        }

        return array_map(function ($text) {
            return __($text);
        }, $items);
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

        $base = request()->getBasePath();
        $prefix = $base ? rtrim($base, '/') : '';

        return $prefix . '/storage/' . $cleanPath;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->image);
    }

    public function getTargetUrlAttribute(): string
    {
        if (!empty($this->custom_url)) {
            $url = trim($this->custom_url);
            if (!Str::startsWith($url, ['http://', 'https://', '/', '#', 'mailto:', 'tel:'])) {
                $url = 'https://' . $url;
            }
            return $url;
        }

        if (!empty($this->experience_level)) {
            return route('jobs.index', ['experience_level' => $this->experience_level]);
        }

        return auth()->check() ? route('profile') : route('login', ['redirect' => route('profile')]);
    }
}
