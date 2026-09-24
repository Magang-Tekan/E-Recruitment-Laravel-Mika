<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PapiAspect extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'english_name',
        'order_number',
    ];

    public function factors(): HasMany
    {
        return $this->hasMany(PapiFactor::class, 'aspect_id')->orderBy('id');
    }
}
