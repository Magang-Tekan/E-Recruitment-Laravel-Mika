<?php

namespace Database\Seeders;

use App\Models\CareerTrack;
use Illuminate\Database\Seeder;

class CareerTrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tracks = [
            [
                'num' => '01',
                'badge' => 'Magang',
                'prefix' => 'LOWONGAN',
                'accent' => 'MAGANG',
                'description' => 'Peluang magang langsung di berbagai unit bisnis industri.',
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&auto=format&fit=crop&q=80',
                'back_badge' => 'INTERNSHIP TRACK',
                'back_title' => 'PROGRAM MAGANG INDUSTRI',
                'highlights' => [
                    'Mentorship 1-on-1 langsung dari praktisi ahli industri',
                    'Terlibat langsung dalam proyek nyata berstandar global',
                    'Konversi SKS resmi & sertifikat kompetensi',
                    'Peluang fast-track rekrutmen karyawan tetap',
                ],
                'cta_text' => 'Lihat Lowongan Magang',
                'experience_level' => 'Magang',
                'custom_url' => null,
                'is_dark' => false,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'num' => '02',
                'badge' => 'Fresh Graduate',
                'prefix' => 'FRESH',
                'accent' => 'GRADUATE',
                'description' => 'Peluang karir terbaik untuk lulusan baru dengan pengalaman hingga 2 tahun.',
                'image' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&auto=format&fit=crop&q=80',
                'back_badge' => 'GRADUATE PROGRAM',
                'back_title' => 'GRADUATE ACCELERATION',
                'highlights' => [
                    'Onboarding komprehensif & kurikulum pengembangan',
                    'Eksplorasi rotasi divisi lintas fungsi kerja',
                    'Lingkungan kerja modern & kultur inovatif',
                    'Jenjang karir transparan dengan evaluasi berkala',
                ],
                'cta_text' => 'Eksplorasi Lowongan FG',
                'experience_level' => 'Fresh Graduate',
                'custom_url' => null,
                'is_dark' => true,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'num' => '03',
                'badge' => 'Profesional',
                'prefix' => 'LOWONGAN',
                'accent' => 'PROFESIONAL',
                'description' => 'Posisi strategis untuk para profesional berpengalaman di bidangnya.',
                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600&auto=format&fit=crop&q=80',
                'back_badge' => 'EXPERIENCED HIRE',
                'back_title' => 'PROFESSIONAL TRACK',
                'highlights' => [
                    'Peran strategis memimpin proyek & tim spesialis',
                    'Remunerasi kompetitif & benefit kesehatan prima',
                    'Otonomi dalam inovasi & pengambilan keputusan',
                    'Dampak langsung pada pertumbuhan industri nasional',
                ],
                'cta_text' => 'Lihat Posisi Profesional',
                'experience_level' => 'Profesional',
                'custom_url' => null,
                'is_dark' => false,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'num' => '04',
                'badge' => 'Talent Pool',
                'prefix' => 'DROP',
                'accent' => 'YOUR CV',
                'description' => 'Belum ada posisi cocok? Kirimkan CV Anda untuk masa depan.',
                'image' => 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=600&auto=format&fit=crop&q=80',
                'back_badge' => 'TALENT NETWORK',
                'back_title' => 'DATABASE TALENT POOL',
                'highlights' => [
                    'Data Anda terindeks prioritas di database HR',
                    'Dihubungi langsung saat lowongan relevan dibuka',
                    'Notifikasi peluang karir yang sesuai keahlian Anda',
                    'Kerahasiaan data terjamin dengan sistem terenkripsi',
                ],
                'cta_text' => 'Kirimkan CV Sekarang',
                'experience_level' => null,
                'custom_url' => null,
                'is_dark' => true,
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($tracks as $track) {
            CareerTrack::updateOrCreate(
                ['num' => $track['num']],
                $track
            );
        }
    }
}
