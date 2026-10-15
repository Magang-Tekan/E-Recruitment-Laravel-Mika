<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'profile_id',
        'category',
        'name',
        'proficiency',
        'description',
        'certificate_path',
    ];

    public function scopeComputer($query)
    {
        return $query->where('category', 'computer');
    }

    public function scopeGeneral($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'general')
              ->orWhereNull('category');
        });
    }

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class, 'profile_id');
    }
}
