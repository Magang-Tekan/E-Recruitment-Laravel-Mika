<?php

namespace App\Console\Commands;

use App\Models\CompanyShowcase;
use App\Models\Job;
use App\Services\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TranslateExistingContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:translate-content {--force : Terjemahkan ulang semua data meskipun kolom EN sudah terisi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menerjemahkan konten dinamis (Jobs & Company Showcase) yang belum memiliki terjemahan bahasa Inggris (EN) setelah migrasi database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = (bool) $this->option('force');

        $this->info('=====================================================');
        $this->info('  Memulai Proses Auto-Translate Konten Dinamis (ID -> EN)  ');
        $this->info('=====================================================');

        // 1. Translasi Lowongan Pekerjaan (Jobs)
        $this->newLine();
        $this->comment('1. Memeriksa data Lowongan Pekerjaan (Jobs)...');
        $jobs = Job::all();
        $jobCount = $jobs->count();
        $jobUpdated = 0;

        $bar = $this->output->createProgressBar($jobCount);
        $bar->start();

        foreach ($jobs as $job) {
            $changed = false;

            // Terjemahkan Judul
            if (($force || empty($job->title_en)) && !empty($job->title)) {
                $translatedTitle = TranslationService::translate($job->title, 'en', 'id');
                if (!empty($translatedTitle)) {
                    $job->title_en = $translatedTitle;
                    $changed = true;
                }
            }

            // Terjemahkan Deskripsi
            if (($force || empty($job->description_en)) && !empty($job->description)) {
                $translatedDesc = TranslationService::translate($job->description, 'en', 'id');
                if (!empty($translatedDesc)) {
                    $job->description_en = $translatedDesc;
                    $changed = true;
                }
            }

            if ($changed) {
                // Simpan langsung tanpa memicu hook lagi jika sudah terisi
                $job->saveQuietly();
                $jobUpdated++;
            }

            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        $this->info(" -> Berhasil memperbarui {$jobUpdated} dari total {$jobCount} lowongan.");

        // 2. Translasi Showcase Perusahaan (CompanyShowcase)
        $this->newLine();
        $this->comment('2. Memeriksa data Showcase Acara / Perusahaan (CompanyShowcase)...');
        $showcases = CompanyShowcase::all();
        $showcaseCount = $showcases->count();
        $showcaseUpdated = 0;

        $barShowcase = $this->output->createProgressBar($showcaseCount);
        $barShowcase->start();

        foreach ($showcases as $showcase) {
            $changed = false;

            if (($force || empty($showcase->title_en)) && !empty($showcase->title)) {
                $trans = TranslationService::translate($showcase->title, 'en', 'id');
                if (!empty($trans)) {
                    $showcase->title_en = $trans;
                    $changed = true;
                }
            }

            if (($force || empty($showcase->description_en)) && !empty($showcase->description)) {
                $trans = TranslationService::translate($showcase->description, 'en', 'id');
                if (!empty($trans)) {
                    $showcase->description_en = $trans;
                    $changed = true;
                }
            }

            if (($force || empty($showcase->tag_en)) && !empty($showcase->tag)) {
                $trans = TranslationService::translate($showcase->tag, 'en', 'id');
                if (!empty($trans)) {
                    $showcase->tag_en = $trans;
                    $changed = true;
                }
            }

            if (($force || empty($showcase->badge_title_en)) && !empty($showcase->badge_title)) {
                $trans = TranslationService::translate($showcase->badge_title, 'en', 'id');
                if (!empty($trans)) {
                    $showcase->badge_title_en = $trans;
                    $changed = true;
                }
            }

            if (($force || empty($showcase->badge_sub_en)) && !empty($showcase->badge_sub)) {
                $trans = TranslationService::translate($showcase->badge_sub, 'en', 'id');
                if (!empty($trans)) {
                    $showcase->badge_sub_en = $trans;
                    $changed = true;
                }
            }

            if ($changed) {
                $showcase->saveQuietly();
                $showcaseUpdated++;
            }

            $barShowcase->advance();
        }
        $barShowcase->finish();
        $this->newLine();
        $this->info(" -> Berhasil memperbarui {$showcaseUpdated} dari total {$showcaseCount} showcase.");

        // 3. Bersihkan Cache Showcase & Frontend
        $this->newLine();
        $this->comment('3. Membersihkan cache frontend & showcase...');
        Cache::forget('frontend_company_showcases');
        Cache::forget('frontend_company_showcases_id');
        Cache::forget('frontend_company_showcases_en');
        Cache::forget('frontend_main_company');
        Cache::forget('about_group_companies');

        $this->info('=====================================================');
        $this->info('  Auto-Translate Selesai! Semua konten kini berbahasa Inggris saat locale EN aktif.  ');
        $this->info('=====================================================');

        return 0;
    }
}
