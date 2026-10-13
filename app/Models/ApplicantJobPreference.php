<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantJobPreference extends Model
{
    use HasFactory;

    protected $table = 'applicant_job_preferences';

    protected $fillable = [
        'applicant_profile_id',
        'interested_field_1',
        'interested_field_2',
        'interested_field_3',
        'notice_period',
        'expected_salary',
        'is_willing_to_relocate',
        'job_search_status',
        'notification_period',
    ];

    protected $casts = [
        'is_willing_to_relocate' => 'boolean',
        'expected_salary' => 'decimal:2',
    ];

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class, 'applicant_profile_id');
    }

    /**
     * Get job search status with default 'Aktif' if not specified
     */
    public function getJobSearchStatusAttribute($value): string
    {
        return !empty($value) ? $value : 'Aktif';
    }
}
