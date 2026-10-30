<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $defaultSettings = [
            [
                'key' => 'hero_title_prefix',
                'value' => 'Temukan Karir Impian,',
                'description' => 'Teks judul baris pertama pada Hero Banner Beranda (Bahasa Indonesia)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'hero_title_prefix_en',
                'value' => 'Find Your Dream Career,',
                'description' => 'Teks judul baris pertama pada Hero Banner Beranda (Bahasa Inggris)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'hero_title_highlight',
                'value' => 'Wujudkan Potensi Terbaikmu',
                'description' => 'Teks highlight hijau neon baris kedua pada Hero Banner Beranda (Bahasa Indonesia)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'hero_title_highlight_en',
                'value' => 'Realize Your Best Potential',
                'description' => 'Teks highlight hijau neon baris kedua pada Hero Banner Beranda (Bahasa Inggris)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'hero_description',
                'value' => 'Bergabunglah bersama ribuan profesional bertalenta. Jelajahi lowongan kerja, ikuti tes seleksi online terintegrasi, dan raih karir masa depan sekarang.',
                'description' => 'Teks deskripsi / slogan di bawah headline Hero Banner Beranda (Bahasa Indonesia)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'hero_description_en',
                'value' => 'Join thousands of talented professionals. Explore job openings, take integrated online tests, and achieve your future career now.',
                'description' => 'Teks deskripsi / slogan di bawah headline Hero Banner Beranda (Bahasa Inggris)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'hero_background_image',
                'value' => 'https://i.pinimg.com/736x/20/dc/f0/20dcf0cfb0122a38d938e3c3011fb0d4.jpg',
                'description' => 'URL atau path gambar background Hero Banner Beranda',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description'],
                    'created_at' => $setting['created_at'],
                    'updated_at' => $setting['updated_at'],
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'hero_title_prefix',
            'hero_title_prefix_en',
            'hero_title_highlight',
            'hero_title_highlight_en',
            'hero_description',
            'hero_description_en',
            'hero_background_image',
        ])->delete();
    }
};

