<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    public $timestamps = false;
    protected $table = 'educations';

    protected $fillable = [
        'profile_id',
        'degree_id',
        'degree',
        'major_id',
        'major',
        'school_name',
        'study_program',
        'start_year',
        'end_year',
        'gpa',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_year' => 'integer',
            'end_year' => 'integer',
            'gpa' => 'decimal:2',
        ];
    }

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class, 'profile_id');
    }

    public function degreeRelation(): BelongsTo
    {
        return $this->belongsTo(Degree::class, 'degree_id');
    }

    public function majorRelation(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id');
    }
}
