<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : Alamat email penerima pengujian}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email uji coba untuk memverifikasi konfigurasi mailer (Resend/SMTP)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipient = $this->argument('email') ?? $this->ask('Masukkan alamat email tujuan pengujian');

        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error("Format email '{$recipient}' tidak valid.");
            return 1;
        }

        $mailer = config('mail.default');
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        $this->info("=== Mengirim Email Uji Coba ===");
        $this->line("Mailer Aktif : <comment>{$mailer}</comment>");
        $this->line("Pengirim     : <comment>{$fromName} <{$fromAddress}></comment>");
        $this->line("Penerima     : <comment>{$recipient}</comment>");
        $this->line("Sedang memproses...");

        try {
            Mail::raw(
                "Halo!\n\nIni adalah email uji coba dari sistem E-Recruitment (" . config('app.name') . ").\n" .
                "Jika Anda membaca pesan ini, konfigurasi pengiriman email ({$mailer}) telah berjalan dengan baik.\n\n" .
                "Waktu pengiriman: " . now()->format('Y-m-d H:i:s') . "\n\n" .
                "Salam,\nTim Teknis E-Recruitment",
                function ($message) use ($recipient) {
                    $message->to($recipient)
                            ->subject("Uji Coba Pengiriman Email - " . config('app.name'));
                }
            );

            $this->newLine();
            $this->info("✓ Email uji coba berhasil dikirim ke: {$recipient}");
            $this->comment("Silakan periksa folder Inbox (atau Spam/Promotions jika domain baru).");
            return 0;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error("✗ Gagal mengirim email:");
            $this->line($e->getMessage());
            $this->newLine();
            $this->line("<comment>Tips:</comment>");
            $this->line("1. Jika menggunakan Resend (MAIL_MAILER=resend), pastikan RESEND_API_KEY diisi dengan benar.");
            $this->line("2. Jika belum memverifikasi domain di Resend, gunakan MAIL_FROM_ADDRESS='onboarding@resend.dev' dan kirim HANYA ke email akun Resend Anda.");
            $this->line("3. Jika menggunakan SMTP Gmail, pastikan App Password (16 digit) masih aktif.");
            return 1;
        }
    }
}

