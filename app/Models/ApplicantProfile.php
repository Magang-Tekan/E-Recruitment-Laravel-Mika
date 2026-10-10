<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantProfile extends Model
{
    protected $table = 'applicant_profile';

    // Fillable disesuaikan dengan kolom di database
    protected $fillable = [
        'user_id', 'nik', 'full_name', 'gender', 'birth_place',
        'birth_date', 'phone', 'address', 'city', 'province',
        'photo', 'npwp', 'child_sequence', 'total_siblings', 'marital_status',
        'about_me', 'generated_cv_url', 'cv_file_path',
        'religion', 'postal_code', 'blood_type',
    ];

    protected $appends = ['cv_url', 'cv_file_url'];

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
            'pribadi' => ! empty($this->nik) && ! empty($this->full_name) && ! empty($this->phone) && ! empty($this->address),
            'cv' => ! empty($this->cv_file_path) || ! empty($this->generated_cv_url),
            'keluarga' => $this->relationLoaded('family') ? (bool) $this->family : ($this->family()->exists() || (! empty($this->child_sequence) && ! empty($this->marital_status))),
            'pendidikan' => $this->relationLoaded('educations') ? $this->educations->isNotEmpty() : $this->educations()->exists(),
            'skill' => $this->relationLoaded('skills') ? $this->skills->isNotEmpty() : $this->skills()->exists(),
            'pengalaman' => $this->relationLoaded('workExperiences') ? $this->workExperiences->isNotEmpty() : $this->workExperiences()->exists(),
            'organisasi' => $this->relationLoaded('organizations') ? $this->organizations->isNotEmpty() : $this->organizations()->exists(),
            'prestasi' => $this->relationLoaded('achievements') ? $this->achievements->isNotEmpty() : $this->achievements()->exists(),
            'social_media' => $this->relationLoaded('socialMedias') ? $this->socialMedias->isNotEmpty() : $this->socialMedias()->exists(),
            'data_tambahan' => ($this->relationLoaded('certifications') ? $this->certifications->isNotEmpty() : $this->certifications()->exists())
                || ($this->relationLoaded('trainings') ? $this->trainings->isNotEmpty() : $this->trainings()->exists())
                || ($this->relationLoaded('languages') ? $this->languages->isNotEmpty() : $this->languages()->exists()),
        ];
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
            'pribadi' => 'Data Pribadi',
            'cv' => 'Unggah Dokumen CV',
            'keluarga' => 'Data Keluarga',
            'pendidikan' => 'Pendidikan',
            'skill' => 'Skill / Keahlian',
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
            'organisasi' => 4,
            'prestasi' => 4,
            'social_media' => 3,
            'data_tambahan' => 4,
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
