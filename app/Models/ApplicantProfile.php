<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantProfile extends Model
{
    protected $table = 'applicant_profile';

    // Fillable disesuaikan dengan kolom di database
    protected $fillable = [
        'user_id', 'nik', 'full_name', 'gender', 'birth_place',
        'birth_date', 'phone', 'address', 'ktp_address', 'city', 'province',
        'photo', 'npwp', 'child_sequence', 'total_siblings', 'marital_status',
        'marriage_date', 'ethnicity', 'nationality', 'driving_licenses',
        'daily_transportation', 'work_distance_km',
        'about_me', 'generated_cv_url', 'cv_file_path',
        'religion', 'postal_code', 'blood_type',
        'health_answers', 'other_answers', 'self_concept',
    ];

    protected function casts(): array
    {
        return [
            'driving_licenses' => 'array',
            'health_answers' => 'array',
            'other_answers' => 'array',
            'self_concept' => 'array',
            'marriage_date' => 'date',
            'birth_date' => 'date',
            'work_distance_km' => 'float',
        ];
    }

    protected $appends = ['cv_url', 'cv_file_url', 'photo_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        // 1. Uploaded photo on applicant profile
        if (! empty($this->photo)) {
            return \Illuminate\Support\Str::startsWith($this->photo, ['http://', 'https://'])
                ? $this->photo
                : asset('storage/' . $this->photo);
        }

        // 2. Default email avatar / Google OAuth avatar from user account
        $avatar = $this->user?->avatar;
        if (! empty($avatar)) {
            return \Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://'])
                ? $avatar
                : asset('storage/' . $avatar);
        }

        return null;
    }

    public function getCvFileUrlAttribute(): ?string
    {
        return $this->cv_file_path ? asset('storage/' . $this->cv_file_path) : null;
    }

    public function getCvUrlAttribute(): ?string
    {
        if ($this->cv_file_path) {
            return asset('storage/' . $this->cv_file_path);
        }
        if ($this->generated_cv_url) {
            return $this->generated_cv_url;
        }
        return route('profile.cv.preview');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class, 'profile_id');
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class, 'profile_id');
    }

    public function educations()
    {
        return $this->hasMany(Education::class, 'profile_id');
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class, 'profile_id');
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class, 'profile_id');
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class, 'profile_id');
    }

    public function trainings()
    {
        return $this->hasMany(Training::class, 'profile_id');
    }

    public function skills()
    {
        return $this->hasMany(Skill::class, 'profile_id');
    }

    public function computerSkills()
    {
        return $this->hasMany(Skill::class, 'profile_id')->where('category', 'computer');
    }

    public function generalSkills()
    {
        return $this->hasMany(Skill::class, 'profile_id')->where(function ($q) {
            $q->where('category', 'general')->orWhereNull('category');
        });
    }

    public function socialMedias()
    {
        return $this->hasMany(SocialMedia::class, 'profile_id');
    }

    public function languages()
    {
        return $this->hasMany(Language::class, 'profile_id');
    }

    public function jobPreference()
    {
        return $this->hasOne(ApplicantJobPreference::class, 'applicant_profile_id');
    }

    public function family()
    {
        return $this->hasOne(ApplicantFamily::class, 'applicant_profile_id');
    }

    /**
     * Internal cache for section statuses during a single request lifecycle
     */
    protected ?array $cachedSectionStatuses = null;

    public function clearSectionStatusesCache(): void
    {
        $this->cachedSectionStatuses = null;
    }

    /**
     * Get section completion status map
     */
    public function getSectionStatusesAttribute(): array
    {
        if ($this->cachedSectionStatuses !== null) {
            return $this->cachedSectionStatuses;
        }

        return $this->cachedSectionStatuses = [
            'pribadi' => ! empty($this->nik) && ! empty($this->full_name) && ! empty($this->phone) && ! empty($this->address) && ! empty($this->marital_status),
            'cv' => ! empty($this->cv_file_path) || ! empty($this->generated_cv_url),
            'keluarga' => $this->relationLoaded('family') ? (bool) $this->family : ($this->family()->exists() || ! empty($this->child_sequence)),
            'pendidikan' => ($this->relationLoaded('educations') ? $this->educations->isNotEmpty() : $this->educations()->exists())
                || ($this->relationLoaded('certifications') ? $this->certifications->isNotEmpty() : $this->certifications()->exists())
                || ($this->relationLoaded('trainings') ? $this->trainings->isNotEmpty() : $this->trainings()->exists())
                || ($this->relationLoaded('languages') ? $this->languages->isNotEmpty() : $this->languages()->exists()),
            'skill' => $this->relationLoaded('skills') ? $this->skills->isNotEmpty() : $this->skills()->exists(),
            'pengalaman' => $this->relationLoaded('workExperiences') ? $this->workExperiences->isNotEmpty() : $this->workExperiences()->exists(),
            'organisasi' => $this->relationLoaded('organizations') ? $this->organizations->isNotEmpty() : $this->organizations()->exists(),
            'prestasi' => $this->relationLoaded('achievements') ? $this->achievements->isNotEmpty() : $this->achievements()->exists(),
            'organisasi_prestasi' => ($this->relationLoaded('organizations') ? $this->organizations->isNotEmpty() : $this->organizations()->exists())
                || ($this->relationLoaded('achievements') ? $this->achievements->isNotEmpty() : $this->achievements()->exists()),
            'data_tambahan' => $this->isDataTambahanComplete(),
            'social_media' => $this->relationLoaded('socialMedias') ? $this->socialMedias->isNotEmpty() : $this->socialMedias()->exists(),
        ];
    }

    /**
     * Check if data tambahan is completely filled out
     */
    public function isDataTambahanComplete(): bool
    {
        if (empty($this->health_answers) || empty($this->other_answers)) {
            return false;
        }

        // Validasi Konsep Diri (minimal 1 kelebihan dan 1 kekurangan diisi)
        if (!empty($this->self_concept)) {
            $strengths = array_filter($this->self_concept['strengths'] ?? [], fn($v) => !empty(trim((string)$v)));
            $weaknesses = array_filter($this->self_concept['weaknesses'] ?? [], fn($v) => !empty(trim((string)$v)));
            if (count($strengths) < 1 || count($weaknesses) < 1) {
                return false;
            }
        } else {
            return false;
        }

        $h = $this->health_answers;
        $healthKeys = ['smoking', 'alcohol', 'hospitalized', 'allergy', 'ongoing_treatment', 'infectious_disease', 'disease_history'];
        foreach ($healthKeys as $k) {
            if (!isset($h[$k]['is_yes']) || $h[$k]['is_yes'] === null) {
                return false;
            }
            if ($h[$k]['is_yes'] === true && empty(trim($h[$k]['detail'] ?? ''))) {
                return false;
            }
        }

        $o = $this->other_answers;
        $readyKeys = ['business_trip_ready', 'mutation_position_ready', 'mutation_group_ready', 'relocation_city_ready'];
        foreach ($readyKeys as $rk) {
            if (empty($o[$rk])) {
                return false;
            }
        }

        $yesNoKeys = ['crime_involvement', 'custody', 'other_applications', 'internal_relations', 'previously_applied'];
        foreach ($yesNoKeys as $yk) {
            if (!isset($o[$yk]['is_yes']) || $o[$yk]['is_yes'] === null) {
                return false;
            }
            if ($o[$yk]['is_yes'] === true && empty(trim($o[$yk]['detail'] ?? ''))) {
                return false;
            }
        }

        $textKeys = ['free_time_activity', 'reason_applying_company', 'reason_applying_position', 'vacancy_source', 'ready_to_work', 'expected_salary', 'expected_benefits'];
        foreach ($textKeys as $tk) {
            if (empty(trim($o[$tk] ?? ''))) {
                return false;
            }
        }

        if (empty($o['statement_agreed'])) {
            return false;
        }

        return true;
    }

    /**
     * Check whether all mandatory profile data are complete
     */
    public function getIsMandatoryCompleteAttribute(): bool
    {
        $statuses = $this->section_statuses;
        return ! empty($statuses['pribadi'])
            && ! empty($statuses['cv'])
            && ! empty($statuses['keluarga'])
            && ! empty($statuses['pendidikan'])
            && ! empty($statuses['skill']);
    }

    /**
     * Calculate mandatory profile completion percentage (0 - 80%)
     */
    public function getMandatoryCompletionPercentageAttribute(): int
    {
        $statuses = $this->section_statuses;
        $mandatoryWeights = [
            'pribadi' => 20,
            'cv' => 20,
            'keluarga' => 15,
            'pendidikan' => 15,
            'skill' => 10,
        ];

        $total = 0;
        foreach ($mandatoryWeights as $key => $weight) {
            if (! empty($statuses[$key])) {
                $total += $weight;
            }
        }

        return $total;
    }

    /**
     * Get count of completed mandatory profile sections
     */
    public function getMandatoryCompletedCountAttribute(): int
    {
        $statuses = $this->section_statuses;
        $mandatoryKeys = ['pribadi', 'cv', 'keluarga', 'pendidikan', 'skill'];
        
        $completedCount = 0;
        foreach ($mandatoryKeys as $key) {
            if (! empty($statuses[$key])) {
                $completedCount++;
            }
        }

        return $completedCount;
    }

    /**
     * Get list of uncompleted mandatory profile section labels
     */
    public function getMissingMandatorySectionsAttribute(): array
    {
        $statuses = $this->section_statuses;
        $labels = [
            'pribadi' => __('Data Pribadi'),
            'cv' => __('Unggah Dokumen CV'),
            'keluarga' => __('Data Keluarga'),
            'pendidikan' => __('Pendidikan'),
            'skill' => __('Skill / Keahlian'),
        ];

        $missing = [];
        foreach ($labels as $key => $label) {
            if (empty($statuses[$key])) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    /**
     * Parse numeric salary from plain number or formatted string (e.g. "Rp 10.000.000", "7000000", "Rp 6.000.000 - Rp 7.500.000")
     */
    public static function parseSalaryNumber($val): ?float
    {
        if (empty($val)) {
            return null;
        }
        $val = trim((string) $val);

        if (is_numeric($val)) {
            return (float) $val;
        }

        if (preg_match('/(?:rp\.?\s*)?(\d{1,3}(?:\.\d{3})+|\d{4,})/i', $val, $matches)) {
            $clean = str_replace('.', '', $matches[1]);
            if (is_numeric($clean) && (float) $clean > 0) {
                return (float) $clean;
            }
        }

        $digits = preg_replace('/[^\d]/', '', $val);
        if (!empty($digits) && (float) $digits > 0) {
            return (float) $digits;
        }

        return null;
    }

    /**
     * Format a salary value into standard Indonesian Rupiah "Rp X.XXX.XXX"
     */
    public static function formatSalaryString($val): string
    {
        $num = static::parseSalaryNumber($val);
        if ($num !== null && $num > 0) {
            return 'Rp ' . number_format($num, 0, ',', '.');
        }
        return (string) $val;
    }

    /**
     * Calculate total profile completion percentage (0 - 100%)
     */
    public function getCompletionPercentageAttribute(): int
    {
        $statuses = $this->section_statuses;
        $weights = [
            // Data Wajib (80% total)
            'pribadi' => 20,
            'cv' => 20,
            'keluarga' => 15,
            'pendidikan' => 15,
            'skill' => 10,
            // Data Pelengkap (20% total)
            'pengalaman' => 5,
            'organisasi_prestasi' => 8,
            'data_tambahan' => 4,
            'social_media' => 3,
        ];

        $totalPercentage = 0;
        foreach ($weights as $key => $weight) {
            if (! empty($statuses[$key])) {
                $totalPercentage += $weight;
            }
        }

        return min(100, $totalPercentage);
    }
}
