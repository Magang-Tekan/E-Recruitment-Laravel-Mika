<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'profile_id',
        'company_name',
        'business_field',
        'company_address',
        'supervisor_name',
        'position',
        'initial_position',
        'employment_type',
        'start_date',
        'end_date',
        'currently_working',
        'initial_salary',
        'final_salary',
        'benefits',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'currently_working' => 'boolean',
            'initial_salary' => 'decimal:2',
            'final_salary' => 'decimal:2',
        ];
    }

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class, 'profile_id');
    }
}
