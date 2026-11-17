<?php

namespace App\Models;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
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
        'pref1_company_id',
        'pref1_department_id',
        'pref1_position_id',
        'interested_field_2',
        'pref2_company_id',
        'pref2_department_id',
        'pref2_position_id',
        'interested_field_3',
        'pref3_company_id',
        'pref3_department_id',
        'pref3_position_id',
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

    /**
     * Relasi master untuk tiga minat lowongan (perusahaan/departemen/posisi),
     * dipakai saat menampilkan minat kerja di detail kandidat dan PDF profil.
     */
    public const PREF_RELATIONS = [
        'pref1Company', 'pref1Department', 'pref1Position',
        'pref2Company', 'pref2Department', 'pref2Position',
        'pref3Company', 'pref3Department', 'pref3Position',
    ];

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class, 'applicant_profile_id');
    }

    public function pref1Company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'pref1_company_id');
    }

    public function pref1Department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'pref1_department_id');
    }

    public function pref1Position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'pref1_position_id');
    }

    public function pref2Company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'pref2_company_id');
    }

    public function pref2Department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'pref2_department_id');
    }

    public function pref2Position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'pref2_position_id');
    }

    public function pref3Company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'pref3_company_id');
    }

    public function pref3Department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'pref3_department_id');
    }

    public function pref3Position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'pref3_position_id');
    }

    /**
     * Get job search status with default 'Aktif' if not specified
     */
    public function getJobSearchStatusAttribute($value): string
    {
        return !empty($value) ? $value : 'Aktif';
    }
}
