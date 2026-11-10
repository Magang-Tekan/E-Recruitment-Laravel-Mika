<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestSubCategory extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TestCategory::class, 'category_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionBank::class, 'sub_category_id');
    }
}

