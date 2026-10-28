<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewSchedule extends Model
{
    protected $table = 'interview_schedule';
    public $timestamps = false;

    protected $fillable = [
        'job_applications_id',
        'users_id',
        'interview_date',
        'location',
        'meeting_link',
        'status',
    ];

    protected $casts = [
        'interview_date' => 'datetime',
    ];

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class, 'job_applications_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Check if the interview is scheduled online
     */
    public function getIsOnlineAttribute(): bool
    {
        return !empty($this->meeting_link) || str_contains(strtolower($this->location ?? ''), 'online');
    }

    /**
     * Get Google Maps URL for offline interview location
     */
    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->is_online) {
            return null;
        }

        $loc = trim($this->location ?? '');

        // 1. If location in interview_schedule contains an explicit URL (e.g. https://maps.app.goo.gl/...)
        if (preg_match('/(https?:\/\/[^\s\)]+)/i', $loc, $urlMatches)) {
            return $urlMatches[1];
        }

        // 2. Retrieve related company info
        $company = $this->jobApplication?->job?->company;
        $rawCompanyAddress = trim($company?->address ?? '');

        // If company address itself contains an explicit Google Maps URL
        if (preg_match('/(https?:\/\/[^\s\)]+)/i', $rawCompanyAddress, $addrUrlMatches)) {
            return $addrUrlMatches[1];
        }

        $companyFormattedAddress = trim($company?->formatted_address ?: $rawCompanyAddress);
        $companyCity = trim($company?->city ?? '');
        $companyProvince = trim($company?->province ?? '');

        // 3. Determine the best search query for Google Maps
        // PENTING: Jangan menyertakan nama brand perusahaan yang BELUM terdaftar di Google Maps (seperti "PT Sapujagat Tekna").
        // Jika nama brand belum terdaftar di Google Maps, Google Maps akan melakukan fuzzy-match nama tersebut ke tempat lain
        // yang memiliki nama mirip di kota/pulau lain, sehingga pin lokasi menjadi SALAH TITIK.
        // Alamat fisik (Jalan, Nomor, Kelurahan/Kecamatan, Kota, Provinsi) 100% akurat dipetakan oleh Google Maps geocoding.

        $hasStreetInLocation = preg_match('/(jl\.|jalan|ruko|komplek|gedung\s+[a-z0-9]+|blvd|boulevard)/i', $loc);

        if (!empty($companyFormattedAddress)) {
            // Gunakan alamat fisik lengkap perusahaan
            $query = $companyFormattedAddress;
        } elseif ($hasStreetInLocation) {
            // Jika lokasi diisi alamat jalan tertentu
            $query = $loc . ($companyCity ? ', ' . $companyCity : '');
        } else {
            // Fallback
            $queryParts = array_filter([$loc, $companyCity, $companyProvince]);
            $query = !empty($queryParts) ? implode(', ', $queryParts) : 'Semarang, Jawa Tengah';
        }

        return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($query);
    }
}
