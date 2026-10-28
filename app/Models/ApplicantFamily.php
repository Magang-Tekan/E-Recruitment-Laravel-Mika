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
        'family_members',
    ];

    protected function casts(): array
    {
        return [
            'emergency_contacts' => 'array',
            'family_members' => 'array',
        ];
    }

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class, 'applicant_profile_id');
    }

    public function getSpouseMemberAttribute(): ?array
    {
        if (empty($this->family_members) || !is_array($this->family_members)) {
            return null;
        }

        foreach ($this->family_members as $member) {
            $rel = strtolower($member['relation'] ?? '');
            if (str_contains($rel, 'suami') || str_contains($rel, 'istri') || str_contains($rel, 'pasangan')) {
                return $member;
            }
        }

        return null;
    }

    public function getSpouseNameAttribute(): ?string
    {
        return $this->spouse_member['name'] ?? null;
    }

    public function getSpouseOccupationAttribute(): ?string
    {
        return $this->spouse_member['occupation'] ?? null;
    }

    public function getSpousePhoneAttribute(): ?string
    {
        return $this->spouse_member['phone'] ?? null;
    }

    public function getSpouseBirthDateAttribute(): ?string
    {
        return $this->spouse_member['birth_date'] ?? null;
    }
}
