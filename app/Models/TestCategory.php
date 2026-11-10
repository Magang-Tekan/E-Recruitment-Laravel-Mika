<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionBank::class, 'category_id');
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(TestSubCategory::class, 'category_id');
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class, 'category_id');
    }

    /**
     * Get cached list of all test categories.
     */
    public static function cachedAll()
    {
        return \Illuminate\Support\Facades\Cache::remember('master_test_categories_all', 86400, function () {
            return static::orderBy('name', 'asc')->get();
        });
    }
}
