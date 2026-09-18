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
        'website',
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
    ];

    protected $casts = [
        'missions' => 'array',
        'core_values' => 'array',
    ];

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
}
