<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    protected $fillable = [
        'keyword',
        'source',
    ];

    /**
     * Simpan satu pencarian. Keyword dinormalisasi agar tidak menghasilkan
     * banyak baris hanya karena spasi/kapitalisasi berbeda.
     */
    public static function record(?string $keyword, string $source = 'home'): void
    {
        $keyword = trim(preg_replace('/\s+/', ' ', (string) $keyword));

        if (mb_strlen($keyword) < 2) {
            return;
        }

        static::create([
            'keyword' => $keyword,
            'source'  => $source,
        ]);
    }
}
