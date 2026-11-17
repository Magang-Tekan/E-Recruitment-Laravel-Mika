<?php

namespace Database\Seeders;

use App\Models\RegistrationStep;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class RegistrationStepSeeder extends Seeder
{
    /**
     * Seed the 6 default registration & selection stages.
     *
     * Only runs on an empty table so admin edits are never overwritten.
     */
    public function run(): void
    {
        // Jangan timpa konten yang sudah diedit admin saat seeder dijalankan ulang.
        if (RegistrationStep::exists()) {
            $this->command?->info('Alur pendaftaran sudah ada, seeding dilewati.');
            return;
        }

        $steps = [
            [
                'sort_order' => 1,
                'short_title' => 'Registrasi',
                'short_sub' => 'Buat Akun Pelamar',
                'eyebrow' => 'Registrasi Akun',
                'title' => 'Buat Akun Pelamar dengan Cepat & Aman',
                'description' => 'Lakukan registrasi menggunakan Google Authentication atau Form Manual (Nama Lengkap, NIK KTP valid, dan Email aktif). Akun ini menjadi pusat portal seluruh aktivitas pelamaran, verifikasi dokumen, ujian seleksi, hingga penawaran kerja.',
                'highlights' => [
                    ['title' => 'Autentikasi Cepat', 'desc' => 'Google OAuth atau form NIK terverifikasi.'],
                    ['title' => 'Akses Fleksibel', 'desc' => 'Responsif 24/7 di smartphone & desktop.'],
                    ['title' => 'Pemberitahuan', 'desc' => 'Status seleksi langsung ke portal akun.'],
                ],
                'tips_title' => 'Tips Registrasi Akun',
                'tips_body' => 'Pastikan nomor NIK KTP dan alamat email yang didaftarkan aktif dan valid untuk memastikan kelancaran verifikasi identitas serta notifikasi status seleksi.',
                'footnote' => 'Data terlindungi dengan enkripsi keamanan portal resmi MIKA',
                'icon' => 'shield',
                'cta_action' => 'register',
                'cta_action_guest' => 'register',
                'cta_label' => 'Buka Profil Saya',
                'cta_label_guest' => 'Daftar Akun Sekarang',
                'cta_label_admin' => 'Buka Dashboard Manajemen',
                'next_label' => 'Lanjut: Profil & CV',
            ],
            [
                'sort_order' => 2,
                'short_title' => 'Profil & CV',
                'short_sub' => 'Lengkapi Berkas',
                'eyebrow' => 'Kelengkapan Profil & CV',
                'title' => 'Lengkapi Biodata, Riwayat & CV Digital',
                'description' => 'Lengkapi profil Anda secara menyeluruh: Biodata Pribadi, Kontak Keluarga, Riwayat Pendidikan, Pengalaman Kerja, Organisasi, Prestasi, hingga Keahlian/Sertifikasi. Sistem secara otomatis menyusun data Anda menjadi format CV profesional.',
                'highlights' => [
                    ['title' => 'ATS-Friendly', 'desc' => 'Generate & preview CV digital terstandar.'],
                    ['title' => 'Update Fleksibel', 'desc' => 'Pembaruan data dapat dilakukan berkala.'],
                    ['title' => 'Unggah Dokumen', 'desc' => 'Ijazah, transkrip nilai, & sertifikat.'],
                ],
                'tips_title' => 'Prioritas Screening',
                'tips_body' => 'Profil pelamar dengan persentase kelengkapan data di atas 85% memiliki prioritas lebih tinggi dalam peninjauan administrasi oleh tim HRD.',
                'footnote' => 'Format resume disinkronkan otomatis ke database rekruter',
                'icon' => 'document',
                'cta_action' => 'profile',
                'cta_action_guest' => 'login',
                'cta_label' => 'Lengkapi Profil Sekarang',
                'cta_label_guest' => 'Masuk untuk Lengkapi Profil',
                'cta_label_admin' => null,
                'next_label' => 'Lanjut: Lamar & Seleksi',
            ],
            [
                'sort_order' => 3,
                'short_title' => 'Seleksi Berkas',
                'short_sub' => 'Screening HRD',
                'eyebrow' => 'Seleksi Berkas',
                'title' => 'Eksplorasi Lowongan & Seleksi Berkas',
                'description' => 'Temukan posisi karir yang sesuai dengan kompetensi Anda di PT Mitra Karya Analitika (MIKA) atau grup bisnis kami. Ajukan lamaran dalam satu klik, lalu tim HRD & Rekruter akan meninjau kualifikasi dan kesesuaian berkas Anda secara transparan.',
                'highlights' => [
                    ['title' => 'One-Click Apply', 'desc' => 'Lamaran praktis dengan profil terintegrasi.'],
                    ['title' => 'Pelacakan Status', 'desc' => 'Update real-time (Terkirim, Review, Lolos).'],
                    ['title' => 'Notifikasi Hasil', 'desc' => 'Kelulusan administrasi sebelum ke tahap ujian.'],
                ],
                'tips_title' => 'Seleksi Administrasi',
                'tips_body' => 'Tim Rekruter akan mencocokkan latar belakang pendidikan, kompetensi, dan pengalaman kerja Anda dengan kualifikasi formasi yang dilamar.',
                'footnote' => 'Transparan tanpa perantara atau pungutan biaya',
                'icon' => 'briefcase',
                'cta_action' => 'jobs',
                'cta_action_guest' => 'jobs',
                'cta_label' => 'Jelajahi Lowongan Tersedia',
                'cta_label_guest' => null,
                'cta_label_admin' => null,
                'next_label' => 'Lanjut: Asesmen CBT',
            ],
            [
                'sort_order' => 4,
                'short_title' => 'Asesmen CBT',
                'short_sub' => 'Ujian & Tes DISC',
                'eyebrow' => 'Ujian Asesmen Online',
                'title' => 'Ikuti Ujian CBT & Tes Kepribadian DISC',
                'description' => 'Setelah dinyatakan lolos berkas administrasi, Anda dapat langsung mengakses sistem Ujian Online terintegrasi. Ujian meliputi Tes Kepribadian DISC (24 kuadran Most/Least) serta Tes Kompetensi Teknis (Pilihan Ganda & Soal Essay + Lampiran Berkas).',
                'highlights' => [
                    ['title' => 'CBT Terpadu', 'desc' => 'Timer real-time dan evaluasi terstandar.'],
                    ['title' => 'Tes DISC', 'desc' => 'Kalkulasi otomatis profil kepribadian kerja.'],
                    ['title' => 'Auto-Save Aman', 'desc' => 'Penyimpanan jawaban berkala anti hilang.'],
                ],
                'tips_title' => 'Persiapan Ujian',
                'tips_body' => 'Gunakan perangkat komputer/laptop dengan koneksi internet yang stabil untuk pengalaman optimal saat menyelesaikan tes DISC dan ujian teknis.',
                'footnote' => 'Hasil terkomputasi langsung ke dashboard seleksi rekruter',
                'icon' => 'clipboard',
                'cta_action' => 'profile_history',
                'cta_action_guest' => 'login',
                'cta_label' => 'Cek Status Ujian di Riwayat',
                'cta_label_guest' => 'Masuk ke Portal Ujian',
                'cta_label_admin' => null,
                'next_label' => 'Lanjut: Wawancara',
            ],
            [
                'sort_order' => 5,
                'short_title' => 'Wawancara',
                'short_sub' => 'HRD & User',
                'eyebrow' => 'Sesi Wawancara',
                'title' => 'Sesi Interview Daring atau Tatap Muka',
                'description' => 'Kandidat yang lolos tahap ujian asesmen (Shortlisted) akan dijadwalkan untuk sesi wawancara mendalam bersama tim HRD dan User. Informasi jadwal, pewawancara, serta tautan video conference (Google Meet) atau lokasi kantor ditampilkan langsung pada dashboard Anda.',
                'highlights' => [
                    ['title' => 'Jadwal Terintegrasi', 'desc' => 'Pewawancara dan waktu (WIB) tertera jelas.'],
                    ['title' => 'Link Meet Langsung', 'desc' => 'Akses video call dari kartu riwayat lamaran.'],
                    ['title' => 'Pengingat Otomatis', 'desc' => 'Notifikasi terjadwal di portal akun Anda.'],
                ],
                'tips_title' => 'Persiapan Wawancara',
                'tips_body' => 'Pelajari profil dan nilai inti MIKA (Menghargai, Integritas, Komitmen, Akuntabel), kenali tanggung jawab posisi, dan siapkan perangkat kamera/audio jika sesi berlangsung secara daring.',
                'footnote' => 'Sesi wawancara berlangsung profesional & solutif',
                'icon' => 'video',
                'cta_action' => 'profile_history',
                'cta_action_guest' => 'jobs',
                'cta_label' => 'Pantau Jadwal Wawancara',
                'cta_label_guest' => 'Lihat Peluang Karir',
                'cta_label_admin' => null,
                'next_label' => 'Lanjut: Hasil Akhir',
            ],
            [
                'sort_order' => 6,
                'short_title' => 'Offering',
                'short_sub' => 'Hasil & Kontrak',
                'eyebrow' => 'Hasil Akhir & Penawaran',
                'title' => 'Pengumuman Kelulusan & Penawaran Kerja',
                'description' => 'Kandidat terbaik yang terpilih akan menerima pemberitahuan kelulusan resmi dengan status Diterima (Accepted). Tim MIKA akan menerbitkan surat penawaran kerja (Offering Letter) resmi beserta panduan onboarding kerja.',
                'highlights' => [
                    ['title' => 'Hasil Transparan', 'desc' => 'Status resmi langsung tertera di akun Anda.'],
                    ['title' => 'Offering Letter', 'desc' => 'Surat penawaran resmi beserta paket benefit.'],
                    ['title' => '100% Gratis', 'desc' => 'Tanpa pungutan biaya apapun dalam proses.'],
                ],
                'tips_title' => 'Selamat Bergabung!',
                'tips_body' => 'Siapkan diri Anda untuk melangkah ke babak baru perjalanan karir profesional masa depan bersama PT Mitra Karya Analitika (MIKA).',
                'footnote' => 'Onboarding terstruktur & pendampingan tim MIKA',
                'icon' => 'award',
                'cta_action' => 'profile_history',
                'cta_action_guest' => 'register',
                'cta_label' => 'Lihat Riwayat & Status Saya',
                'cta_label_guest' => 'Daftar & Raih Karir Impian',
                'cta_label_admin' => null,
                'next_label' => null,
            ],
        ];

        // Data contoh tidak perlu diterjemahkan via API: terjemahan EN sudah ada di lang/en.json.
        RegistrationStep::withoutAutoTranslate(function () use ($steps) {
            foreach ($steps as $step) {
                RegistrationStep::updateOrCreate(
                    ['sort_order' => $step['sort_order']],
                    array_merge($step, ['is_active' => true])
                );
            }
        });

        Setting::set(
            'registration_flow_title',
            'Alur Pendaftaran & Seleksi',
            'Judul section alur pendaftaran di beranda'
        );

        Setting::set(
            'registration_flow_subtitle',
            'Ikuti 6 tahapan sistematis dan transparan untuk bergabung menjadi bagian dari talenta terbaik PT Mitra Karya Analitika (MIKA).',
            'Subjudul section alur pendaftaran di beranda'
        );

        Setting::set('registration_flow_enabled', '1', 'Tampilkan section alur pendaftaran di beranda');
    }
}
