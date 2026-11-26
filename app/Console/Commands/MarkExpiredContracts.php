<?php

namespace App\Console\Commands;

use App\Models\EmployeeProfile;
use Illuminate\Console\Command;

class MarkExpiredContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-expired-contracts {--dry-run : Jalankan simulasi tanpa mengubah data di database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menandai karyawan kontrak/magang/probation yang tanggal berakhirnya sudah lewat sebagai contract_ended tanpa mengubah role akun';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = (bool) $this->option('dry-run');
        $today = now()->toDateString();

        $this->info("Memeriksa kontrak yang berakhir sebelum [{$today}]...");

        $query = EmployeeProfile::query()
            ->where('employment_status', EmployeeProfile::STATUS_ACTIVE)
            ->whereIn('employee_type', ['contract', 'internship', 'probation'])
            ->whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', '<', $today);

        $count = $query->count();

        if ($count === 0) {
            $this->info('Tidak ada kontrak yang perlu ditandai. Semua status kepegawaian sudah sesuai.');

            return self::SUCCESS;
        }

        if ($isDryRun) {
            $this->warn("[Dry-run] {$count} karyawan akan ditandai sebagai 'Kontrak Berakhir'.");

            return self::SUCCESS;
        }

        $query->update(['employment_status' => EmployeeProfile::STATUS_CONTRACT_ENDED]);

        $this->info("{$count} karyawan ditandai sebagai 'Kontrak Berakhir'. Role akun tidak diubah.");

        return self::SUCCESS;
    }
}
