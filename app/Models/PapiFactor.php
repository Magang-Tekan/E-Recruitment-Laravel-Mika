<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PapiFactor extends Model
{
    use HasFactory;

    protected $fillable = [
        'aspect_id',
        'code',
        'name',
        'english_name',
        'type',
        'description',
    ];

    public function aspect(): BelongsTo
    {
        return $this->belongsTo(PapiAspect::class, 'aspect_id');
    }

    public function norms(): HasMany
    {
        return $this->hasMany(PapiNorm::class, 'factor_id')->orderBy('min_score');
    }
}
