<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PapiTestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_attempt_id',
        'scores',
        'role_score',
        'need_score',
        'is_valid',
        'interpretations',
        'raw_answers',
    ];

    protected $casts = [
        'scores' => 'array',
        'interpretations' => 'array',
        'raw_answers' => 'array',
        'is_valid' => 'boolean',
        'role_score' => 'integer',
        'need_score' => 'integer',
    ];

    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }
}
