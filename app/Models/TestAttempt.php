<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\HasOne;

class TestAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'job_application_id',
        'user_id',
        'attempt_type',
        'participant_name',
        'participant_age',
        'participant_gender',
        'test_date',
        'test_id',
        'started_at',
        'finished_at',
        'duration',
        'objective_score',
        'essay_score',
        'total_score',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'test_date' => 'date',
    ];

    protected $appends = [
        'score',
        'is_psychometric',
    ];

    public function getScoreAttribute(): ?float
    {
        if ($this->total_score !== null) {
            return (float) $this->total_score;
        }

        if ($this->objective_score !== null) {
            return (float) $this->objective_score;
        }

        return null;
    }

    public function getIsPsychometricAttribute(): bool
    {
        $title = strtolower($this->test?->title ?? '');
        $catName = strtolower($this->test?->category?->name ?? '');

        return str_contains($title, 'disc')
            || str_contains($title, 'papi')
            || str_contains($catName, 'disc')
            || str_contains($catName, 'papi')
            || (bool) $this->discTestResult
            || (bool) $this->papiTestResult;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class, 'test_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class, 'attempt_id');
    }

    public function discTestResult(): HasOne
    {
        return $this->hasOne(DiscTestResult::class, 'test_attempt_id');
    }

    public function papiTestResult(): HasOne
    {
        return $this->hasOne(PapiTestResult::class, 'test_attempt_id');
    }
}
