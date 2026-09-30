<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyShowcase;
use Illuminate\Database\Seeder;

class CompanyShowcaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::where('name', 'like', '%Mitra Karya Analitika%')->first() ?? Company::first();
        $companyId = $company?->id;

        $items = [
            [
                'company_id' => $companyId,
                'tag' => 'Tentang Kami & Karir',
                'title' => 'Ruang untuk Bertumbuh dan Berkembang',
                'description' => 'Kami mendorong setiap individu untuk terus berkembang melalui pelatihan berkelanjutan, pengembangan sumber daya manusia, serta lingkungan kerja modern. Bersama Mitra Karya Analitika, kembangkan potensi, pengalaman, dan karier Anda secara optimal.',
                'img1' => 'asset-compro/aniv1.jpg',
                'img2' => 'asset-compro/outbond.jpg',
                'img3' => 'asset-compro/aniv.jpg',
                'badge_title' => 'CAREER',
                'badge_sub' => 'Growth & Development',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'company_id' => $companyId,
                'tag' => 'Acara & Kolaborasi',
                'title' => 'Bimbingan Teknis ASPADIN 2026: Sinergi Kompetensi',
                'description' => 'Menghadirkan sesi Bimbingan Teknis eksklusif di Semarang bagi para mitra industri. Kami berbagi pengetahuan, memamerkan inovasi solusi IoT terbaru, dan memperkuat jaringan untuk pertumbuhan profesional bersama.',
                'img1' => 'asset-compro/aspadin1.jpg',
                'img2' => 'asset-compro/aspadin2.jpg',
                'img3' => 'asset-compro/aspadin3.jpg',
                'badge_title' => 'EVENT',
                'badge_sub' => 'Technical Guidance',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'company_id' => $companyId,
                'tag' => 'Acara & Pameran',
                'title' => 'Partisipasi Aktif di Event HISFARIN 2025',
                'description' => 'Memperluas jaringan dan memperkenalkan solusi teknologi analitik terkini dalam Musyawarah Nasional HISFARIN 2025. Kami hadir langsung menyapa para profesional, memamerkan perangkat keras inovatif, dan membangun sinergi kolaboratif untuk mendukung kemajuan industri.',
                'img1' => 'asset-compro/hisfarin1.jpg',
                'img2' => 'asset-compro/hisfarin2.jpg',
                'img3' => 'asset-compro/hisfarin3.jpg',
                'badge_title' => 'EXHIBITION',
                'badge_sub' => 'HISFARIN 2025',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            CompanyShowcase::updateOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }
}
