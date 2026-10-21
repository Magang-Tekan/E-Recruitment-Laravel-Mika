<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Language extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'profile_id',
        'name',
        'oral',
        'written',
        'conversation',
        'certificate_path',
    ];

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class, 'profile_id');
    }
}
