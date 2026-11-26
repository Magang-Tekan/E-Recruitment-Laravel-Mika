<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal Pengiriman Email Rekomendasi Lowongan Kerja Sesuai Pilihan Pelamar
Schedule::command('app:send-job-recommendations Harian')->dailyAt('08:00');
Schedule::command('app:send-job-recommendations Mingguan')->weeklyOn(1, '08:00'); // Setiap Senin jam 08:00
Schedule::command('app:send-job-recommendations Bulanan')->monthlyOn(1, '08:00');  // Tanggal 1 setiap bulan jam 08:00

// Jadwal Otomatis Penutupan Lowongan yang Melewati Batas Tanggal (Deadline)
Schedule::command('app:close-expired-jobs')->hourly();

// Jadwal Penanda Kontrak Karyawan yang Sudah Berakhir (role akun tidak diubah)
Schedule::command('app:mark-expired-contracts')->dailyAt('00:10');

