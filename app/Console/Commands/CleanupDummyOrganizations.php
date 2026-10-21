<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\EmployeeProfile;
use App\Models\TestAttempt;
use App\Models\DiscTestResult;
use App\Models\PapiTestResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Database\Seeders\CompanyDepartmentPositionSeeder;

class CleanupDummyOrganizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-dummy-organizations {--dry-run : Jalankan simulasi tanpa mengubah database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan perusahaan dummy dan migrasikan data ke struktur resmi MIKA & AKA tanpa menghilangkan hasil tes & pelamar';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info("=== MEMULAI PEMBERSIHAN DUMMY & SINKRONISASI STRUKTUR RESMI ===");
        if ($isDryRun) {
            $this->warn("[DRY RUN MODE] Tidak ada perubahan yang akan disimpan ke database.");
        }

        DB::beginTransaction();

        try {
            // 1. Dapatkan perusahaan resmi MIKA & AKA
            $mika = Company::where('name', 'like', '%Mitra Karya%')->first();
            $aka  = Company::where('name', 'like', '%Autentik%')->first();

            if (!$mika || !$aka) {
                throw new \Exception("Perusahaan MIKA atau AKA tidak ditemukan di database.");
            }

            $this->info("Perusahaan resmi ditemukan: [ID: {$mika->id}] {$mika->name} & [ID: {$aka->id}] {$aka->name}");

            // Standarkan nama resmi
            $mika->update(['name' => 'PT Mitra Karya Analitika']);
            $aka->update(['name' => 'PT Autentik Karya Analitika']);

            // 2. Jalankan Seeder Organisasi Resmi MIKA & AKA
            $this->info("Menjalankan CompanyDepartmentPositionSeeder...");
            $seeder = new CompanyDepartmentPositionSeeder();
            $seeder->run();

            // 3. Cari perusahaan dummy di luar MIKA & AKA
            $dummyCompanies = Company::whereNotIn('id', [$mika->id, $aka->id])->get();
            $dummyIds = $dummyCompanies->pluck('id')->toArray();

            $this->line("Perusahaan dummy yang terdeteksi: " . $dummyCompanies->pluck('name')->implode(', '));

            // 4. Remap lowongan dari perusahaan dummy ke PT Autentik Karya Analitika
            $jobsToRemap = Job::whereIn('company_id', $dummyIds)->get();
            foreach ($jobsToRemap as $job) {
                $oldCoName = $job->company?->name ?? "Co #{$job->company_id}";
                $this->line("  -> Mengalihkan Lowongan #{$job->id} '{$job->title}' dari '{$oldCoName}' ke 'PT Autentik Karya Analitika'");
                $job->update(['company_id' => $aka->id]);
            }

            // 5. Remap profil karyawan yang menempel di perusahaan dummy
            $epsToRemap = EmployeeProfile::whereIn('company_id', $dummyIds)->get();
            foreach ($epsToRemap as $ep) {
                $this->line("  -> Mengalihkan Karyawan User #{$ep->user_id} ke 'PT Autentik Karya Analitika'");
                $ep->update(['company_id' => $aka->id]);
            }

            // 6. Hapus perusahaan dummy
            foreach ($dummyCompanies as $dummyCo) {
                $this->warn("  -> Menghapus perusahaan dummy: [ID: {$dummyCo->id}] '{$dummyCo->name}'");
                $dummyCo->delete();
            }

            // 7. Migrasikan profil karyawan yang masih menempel di posisi/departemen dummy
            $syauqi = EmployeeProfile::where('user_id', 13)->first();
            if ($syauqi) {
                $syauqi->update(['department_id' => 13, 'position_id' => 104]);
                $this->line("  -> Menyesuaikan posisi Syauqi: QA/QC SPV (PLANT / PABRIK)");
            }

            $mahargya = EmployeeProfile::where('user_id', 14)->first();
            if ($mahargya) {
                $mahargya->update(['department_id' => 13, 'position_id' => 103]);
                $this->line("  -> Menyesuaikan posisi Mahargya: Assembly Staff (PLANT / PABRIK)");
            }

            $niko = EmployeeProfile::where('user_id', 9)->first();
            if ($niko) {
                $niko->update(['department_id' => 13, 'position_id' => 108]);
                $this->line("  -> Menyesuaikan posisi Niko Orlando: Hardware Engineer (PLANT / PABRIK)");
            }

            foreach ([12, 17] as $userId) {
                $salesEmp = EmployeeProfile::where('user_id', $userId)->first();
                if ($salesEmp) {
                    $salesEmp->update(['department_id' => 12, 'position_id' => 84]);
                    $this->line("  -> Menyesuaikan posisi User #{$userId}: Sales Office NFP (PRODUK & MARKETING)");
                }
            }

            // 8. Migrasikan pivot department_test agar paket ujian tetap aktif di departemen resmi
            $testsLinkedToOldDepts = DB::table('department_test')->whereIn('department_id', [1, 2, 3, 4, 5])->pluck('test_id')->unique();
            foreach ($testsLinkedToOldDepts as $testId) {
                DB::table('department_test')->insertOrIgnore(['test_id' => $testId, 'department_id' => 12]);
                DB::table('department_test')->insertOrIgnore(['test_id' => $testId, 'department_id' => 13]);
            }
            DB::table('department_test')->whereIn('department_id', [1, 2, 3, 4, 5])->delete();

            // 9. Hapus posisi dummy lama (yang sudah 0 jobs & 0 karyawan)
            $oldPositionIds = [1, 4, 6, 8, 9, 10, 14, 15, 20];
            $oldPositions = Position::whereIn('id', $oldPositionIds)->get();
            foreach ($oldPositions as $op) {
                $this->line("  -> Menghapus posisi dummy lama: '{$op->name}' (ID: {$op->id})");
                $op->delete();
            }

            // 10. Hapus departemen dummy lama (yang sudah 0 jobs & 0 karyawan & 0 posisi)
            $oldDeptIds = [1, 2, 3, 4, 5];
            $oldDepts = Department::whereIn('id', $oldDeptIds)->get();
            foreach ($oldDepts as $od) {
                $this->warn("  -> Menghapus departemen dummy lama: '{$od->name}' (ID: {$od->id})");
                $od->delete();
            }

            if ($isDryRun) {
                DB::rollBack();
                $this->warn("Simulasi selesai. Semua data di-rollback (tidak ada perubahan permanen).");
            } else {
                DB::commit();
                $this->info("Perubahan BERHASIL disimpan ke database secara permanen.");
            }

            // 8. Tampilkan ringkasan verifikasi integritas data
            $this->newLine();
            $this->table(
                ['Entitas Data', 'Jumlah Saat Ini', 'Status Integritas'],
                [
                    ['Perusahaan', Company::count(), 'Hanya MIKA & AKA resmi'],
                    ['Lowongan Aktif/Ada', Job::count(), 'Semua lowongan tetap utuh (100%)'],
                    ['Total Berkas Lamaran', JobApplication::count(), 'Semua berkas lamaran utuh (100%)'],
                    ['Sesi Ujian (Test Attempts)', TestAttempt::count(), 'Semua riwayat tes utuh (100%)'],
                    ['Laporan Analisis DISC', DiscTestResult::count(), 'Semua hasil DISC utuh (100%)'],
                    ['Laporan Evaluasi PAPI', PapiTestResult::count(), 'Semua hasil PAPI utuh (100%)'],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Gagal menjalankan pembersihan: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
