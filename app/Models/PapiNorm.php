<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PapiNorm extends Model
{
    use HasFactory;

    protected $fillable = [
        'factor_id',
        'factor_code',
        'min_score',
        'max_score',
        'interpretation',
        'description',
    ];

    public function factor(): BelongsTo
    {
        return $this->belongsTo(PapiFactor::class, 'factor_id');
    }
}
