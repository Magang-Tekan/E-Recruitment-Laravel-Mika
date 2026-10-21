<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantFamily extends Model
{
    use HasFactory;

    protected $table = 'applicant_families';

    protected $fillable = [
        'applicant_profile_id',
        'father_name',
        'father_birth_year',
        'father_last_education',
        'father_occupation',
        'father_company',
        'mother_name',
        'mother_birth_year',
        'mother_last_education',
        'mother_occupation',
        'mother_company',
        'emergency_contacts',
    ];

    protected function casts(): array
    {
        return [
            'emergency_contacts' => 'array',
        ];
    }

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class, 'applicant_profile_id');
    }
}
