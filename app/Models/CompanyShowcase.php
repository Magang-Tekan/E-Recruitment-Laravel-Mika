<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyShowcase extends Model
{
    protected $fillable = [
        'company_id',
        'tag',
        'title',
        'description',
        'img1',
        'img2',
        'img3',
        'badge_title',
        'badge_sub',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

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

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
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
