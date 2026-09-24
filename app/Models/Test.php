<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Test extends Model
{
    protected $fillable = [
        'job_id',
        'test_type',
        'department_id',
        'target_employee_type',
        'category_id',
        'title',
        'duration_minutes',
        'passing_score',
        'total_questions',
        'is_random',
    ];

    protected $casts = [
        'is_random' => 'boolean',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_test', 'test_id', 'job_id')
                    ->withTimestamps();
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_test', 'test_id', 'department_id')
                    ->withTimestamps();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TestCategory::class, 'category_id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(QuestionBank::class, 'test_questions', 'test_id', 'question_id')
                    ->withPivot('order_number')
                    ->orderByPivot('order_number', 'asc');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class, 'test_id');
    }
}
