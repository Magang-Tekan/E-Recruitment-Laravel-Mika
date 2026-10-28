<?php

namespace App\Models;

use App\Models\Job;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'name',
        'tagline',
        'logo',
        'card_img1',
        'card_img2',
        'card_img3',
        'website',
        'social_media',
        'about',
        'vision',
        'missions',
        'core_values',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'registration_passkey',
    ];

    protected $casts = [
        'missions' => 'array',
        'core_values' => 'array',
        'social_media' => 'array',
    ];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('frontend_main_company');
            \Illuminate\Support\Facades\Cache::forget('master_companies_all');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('frontend_main_company');
            \Illuminate\Support\Facades\Cache::forget('master_companies_all');
        });
    }

    /**
     * Check if company has at least one valid social media link
     */
    public function getHasSocialMediaAttribute(): bool
    {
        if (empty($this->social_media) || !is_array($this->social_media)) {
            return false;
        }
        foreach ($this->social_media as $url) {
            if (!empty(trim((string)$url))) {
                return true;
            }
        }
        return false;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function showcases()
    {
        return $this->hasMany(CompanyShowcase::class);
    }

    public function resolveImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        $cleanPath = ltrim($path, '/');
        if (\Illuminate\Support\Str::startsWith($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }

        $base = request()->getBasePath();
        $prefix = $base ? rtrim($base, '/') : '';

        return $prefix . '/storage/' . $cleanPath;
    }

    public function getCardImg1UrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->card_img1);
    }

    public function getCardImg2UrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->card_img2);
    }

    public function getCardImg3UrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->card_img3);
    }

    /**
     * Get 1 to 3 preview images for the company card on homepage
     */
    public function getCardImagesAttribute(): array
    {
        // 1. Direct card photos uploaded in Company Profile (Highest Priority)
        $direct = array_values(array_filter([
            $this->card_img1_url,
            $this->card_img2_url,
            $this->card_img3_url,
        ]));

        if (!empty($direct)) {
            return $direct;
        }

        // 2. Fallback to CompanyShowcase photos
        $images = [];
        $showcaseItems = $this->relationLoaded('showcases')
            ? $this->showcases->where('is_active', true)
            : $this->showcases()->where('is_active', true)->get();

        foreach ($showcaseItems as $sc) {
            if (!empty($sc->img1_url)) $images[] = $sc->img1_url;
            if (!empty($sc->img2_url)) $images[] = $sc->img2_url;
            if (!empty($sc->img3_url)) $images[] = $sc->img3_url;
            if (count($images) >= 3) break;
        }

        if (!empty($images)) {
            return array_slice($images, 0, 3);
        }

        // 3. Fallback to default company culture images
        $pool = [
            asset('storage/asset-compro/aniv1.jpg'),
            asset('storage/asset-compro/aspadin1.jpg'),
            asset('storage/asset-compro/outbond.jpg'),
            asset('storage/asset-compro/hisfarin1.jpg'),
            asset('storage/asset-compro/aspadin2.jpg'),
            asset('storage/asset-compro/aniv.jpg'),
        ];
        $offset = (($this->id ?? 1) * 2) % count($pool);
        for ($i = 0; $i < 3; $i++) {
            $images[] = $pool[($offset + $i) % count($pool)];
        }

        return array_slice($images, 0, 3);
    }

    /**
     * Get formatted logo URL (Penyimpanan lokal server atau URL eksternal)
     */
    public function getLogoUrlAttribute()
    {
        if (empty($this->logo)) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($this->logo, ['http://', 'https://', '//'])) {
            return $this->logo;
        }

        return asset('storage/' . ltrim($this->logo, '/'));
    }

    /**
     * Get WhatsApp link from phone number
     */
    public function getWhatsappUrlAttribute()
    {
        if (empty($this->phone)) {
            return null;
        }

        $cleanNumber = preg_replace('/[^0-9]/', '', $this->phone);
        if (\Illuminate\Support\Str::startsWith($cleanNumber, '0')) {
            $cleanNumber = '62' . substr($cleanNumber, 1);
        } elseif (\Illuminate\Support\Str::startsWith($cleanNumber, '8')) {
            $cleanNumber = '62' . $cleanNumber;
        }

        return "https://wa.me/{$cleanNumber}";
    }

    /**
     * Get full formatted address string
     */
    public function getFormattedAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->province,
            $this->postal_code ? "Kode Pos {$this->postal_code}" : null,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get 2-letter initials from company name (ignoring PT/CV/UD prefixes)
     */
    public function getInitialAttribute()
    {
        $cleanName = trim(preg_replace('/^(PT|CV|UD|Firma)\b\.?\s*/i', '', $this->name ?? ''));
        if (empty($cleanName)) {
            return 'CO';
        }

        $words = preg_split('/\s+/', $cleanName);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($cleanName, 0, 2));
    }

    /**
     * Get localized company about description
     */
    public function getLocalizedAboutAttribute(): ?string
    {
        if (app()->getLocale() === 'en') {
            if (!empty($this->about_en)) {
                return $this->about_en;
            }
            if (!empty($this->about)) {
                $translated = \App\Services\TranslationService::translate($this->about);
                if (!empty($translated)) {
                    return $translated;
                }
            }
        }
        return $this->about ? __(trim($this->about)) : null;
    }

    /**
     * Get localized company tagline
     */
    public function getLocalizedTaglineAttribute(): ?string
    {
        if (app()->getLocale() === 'en') {
            if (!empty($this->tagline_en)) {
                return $this->tagline_en;
            }
            if (!empty($this->tagline)) {
                $translated = \App\Services\TranslationService::translate($this->tagline);
                if (!empty($translated)) {
                    return $translated;
                }
            }
        }
        return $this->tagline ? __(trim($this->tagline)) : null;
    }

    /**
     * Get localized company vision
     */
    public function getLocalizedVisionAttribute(): ?string
    {
        if (app()->getLocale() === 'en') {
            if (!empty($this->vision_en)) {
                return $this->vision_en;
            }
            if (!empty($this->vision)) {
                $translated = \App\Services\TranslationService::translate($this->vision);
                if (!empty($translated)) {
                    return $translated;
                }
            }
        }
        return $this->vision ? __(trim($this->vision)) : null;
    }
}
