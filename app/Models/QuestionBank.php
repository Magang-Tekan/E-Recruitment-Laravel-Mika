<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuestionBank extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'question',
        'question_type',
        'metadata',
        'image_path',
        'points',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TestCategory::class, 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(TestSubCategory::class, 'sub_category_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }

    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(Test::class, 'test_questions', 'question_id', 'test_id')
                    ->withPivot('order_number');
    }
}
