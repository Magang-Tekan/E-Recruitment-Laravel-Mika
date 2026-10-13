<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light">
    <title>Pemberitahuan Status Lamaran Pekerjaan</title>
    <style>
        :root {
            color-scheme: light only;
            supported-color-schemes: light;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #1e293b;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding: 40px 16px;
        }
        .main-card {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        .header {
            padding: 28px 32px 20px 32px;
            border-bottom: 1px solid #f1f5f9;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logo {
            max-height: 40px;
            max-width: 160px;
            height: auto;
            display: block;
        }
        .header-category {
            text-align: right;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
        }
        .content {
            padding: 32px;
        }
        .salutation {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .paragraph {
            font-size: 14px;
            color: #334155;
            line-height: 1.65;
            margin: 0 0 16px 0;
        }
        .notice-card {
            margin: 24px 0;
            padding: 18px 20px;
            border-radius: 8px;
            border-left: 4px solid #0f172a;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }
        .notice-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 6px 0;
        }
        .notice-desc {
            font-size: 13px;
            color: #334155;
            line-height: 1.65;
            margin: 0;
        }
        .notice-desc strong {
            color: #0f172a;
            font-weight: 700;
        }
        .memo-box {
            margin: 20px 0;
            padding: 16px 20px;
            background-color: #f1f5f9;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .memo-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #475569;
            margin-bottom: 6px;
        }
        .memo-text {
            font-size: 13px;
            color: #1e293b;
            font-style: italic;
            line-height: 1.6;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        .details-table td {
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .details-label {
            width: 38%;
            color: #64748b;
            font-weight: 500;
        }
        .details-val {
            width: 62%;
            color: #0f172a;
            font-weight: 600;
        }
        .btn-wrapper {
            margin: 28px 0 20px 0;
            text-align: left;
        }
        .btn-primary {
            display: inline-block;
            background-color: #059669;
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            padding: 12px 28px;
            border-radius: 8px;
            text-align: center;
            letter-spacing: 0.3px;
        }
        .btn-action-cell {
            background-color: #059669 !important;
        }
        .btn-action-text {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
        }
        @media (prefers-color-scheme: dark) {
            .btn-action-cell {
                background-color: #059669 !important;
            }
            .btn-action-text {
                color: #ffffff !important;
                -webkit-text-fill-color: #ffffff !important;
            }
        }
        [data-ogsc] .btn-action-cell {
            background-color: #059669 !important;
        }
        [data-ogsc] .btn-action-text {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
        }
        u + .email-body .btn-action-text {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
        }
        .footnote {
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px dashed #e2e8f0;
        }
        .sign-off {
            margin-top: 28px;
            font-size: 13px;
            color: #334155;
            line-height: 1.6;
        }
        .sign-off strong {
            color: #0f172a;
        }
        .footer {
            padding: 24px 32px;
            background-color: #fafbfc;
            border-top: 1px solid #f1f5f9;
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }
        .footer strong {
            color: #334155;
        }
    </style>
</head>
<body class="email-body">
    @php
        $job = $application->job;
        $company = $job?->company;
        $applicantProfile = $application->applicantProfile;
        $candidateName = $applicantProfile?->full_name ?? ($applicantProfile?->user?->name ?? 'Pelamar');
        $jobTitle = $job?->title ?? 'Posisi Pekerjaan';
        $companyName = $company?->name ?? config('app.name', 'MIKA CAREER');

        // Embed logo resmi secara inline agar selalu tampil di semua aplikasi email
        $logoPath = public_path('images/mikaaaa.png');
        $hasLogo = file_exists($logoPath);
        $logoSrc = (isset($message) && method_exists($message, 'embed') && $hasLogo) 
            ? $message->embed($logoPath) 
            : ($hasLogo ? asset('images/mikaaaa.png') : null);

        // Teks dan instruksi profesional sesuai status
        $statusInfo = match($status) {
            'Reviewed' => [
                'border_color' => '#2563eb',
                'bg_color'     => '#eff6ff',
                'title'        => 'Pemberitahuan: Lolos Seleksi Berkas & Undangan Ujian Online',
                'desc'         => 'Berdasarkan hasil penelaahan kualifikasi terhadap berkas lamaran yang Anda ajukan, kami menyampaikan bahwa berkas Anda resmi dinyatakan <strong style="color: #1d4ed8;">LOLOS SELEKSI BERKAS</strong>. Anda kami undang untuk melanjutkan ke tahapan <strong style="color: #1d4ed8;">Ujian Seleksi Online</strong>.',
                'btn_text'     => 'Masuk ke Portal & Mulai Ujian',
                'btn_url'      => route('profile', ['tab' => 'riwayat']),
                'has_exam'     => true,
            ],
            'Shortlisted' => [
                'border_color' => '#0284c7',
                'bg_color'     => '#f0f9ff',
                'title'        => 'Pemberitahuan: Lolos Tahap Evaluasi (Shortlisted)',
                'desc'         => 'Hasil evaluasi dan penilaian tes Anda telah kami tinjau. Profil Anda dinyatakan memenuhi standar kompetensi dan resmi masuk ke dalam daftar <strong style="color: #0369a1;">KANDIDAT TERPILIH (SHORTLISTED)</strong> untuk proses seleksi lebih lanjut.',
                'btn_text'     => 'Buka Riwayat Lamaran',
                'btn_url'      => route('profile', ['tab' => 'riwayat']),
                'has_exam'     => false,
            ],
            'Interview' => [
                'border_color' => '#6366f1',
                'bg_color'     => '#eef2ff',
                'title'        => 'Undangan Tahap Wawancara Kerja',
                'desc'         => 'Kami mengundang Anda untuk mengikuti tahapan <strong style="color: #4338ca;">WAWANCARA KERJA (INTERVIEW)</strong> bersama tim rekruter. Detail mengenai waktu pelaksanaan dan media pertemuan telah kami sematkan pada akun Anda.',
                'btn_text'     => 'Lihat Jadwal Wawancara',
                'btn_url'      => route('profile', ['tab' => 'riwayat']),
                'has_exam'     => false,
            ],
            'Accepted' => [
                'border_color' => '#16a34a',
                'bg_color'     => '#f0fdf4',
                'title'        => 'Pemberitahuan Penerimaan Kerja (Accepted)',
                'desc'         => 'Dengan senang hati kami sampaikan bahwa Anda resmi dinyatakan <strong style="color: #15803d;">DITERIMA BEKERJA (ACCEPTED)</strong> untuk bergabung bersama <strong>' . $companyName . '</strong>. Tim Human Resources akan segera menghubungi Anda untuk koordinasi penawaran resmi (offering) serta administrasi onboarding.',
                'btn_text'     => 'Akses Akun Pelamar',
                'btn_url'      => route('profile', ['tab' => 'riwayat']),
                'has_exam'     => false,
            ],
            'Rejected' => [
                'border_color' => '#dc2626',
                'bg_color'     => '#fef2f2',
                'title'        => 'Informasi Perkembangan Seleksi Lamaran',
                'desc'         => 'Setelah melalui proses pertimbangan yang mendalam terhadap seluruh berkas kandidat yang masuk, saat ini kualifikasi Anda <strong style="color: #b91c1c;">BELUM SESUAI</strong> dengan kebutuhan posisi ini. Kami sangat mengapresiasi waktu dan minat yang telah Anda berikan, serta akan tetap menyimpan profil Anda untuk peluang yang sesuai di masa mendatang.',
                'btn_text'     => 'Lihat Lowongan Lainnya',
                'btn_url'      => route('jobs.index'),
                'has_exam'     => false,
            ],
            default => [
                'border_color' => '#0f172a',
                'bg_color'     => '#f8fafc',
                'title'        => 'Pembaruan Perkembangan Lamaran Pekerjaan',
                'desc'         => 'Terdapat pembaruan informasi pada status lamaran pekerjaan yang Anda ajukan menjadi <strong>' . e($application->status_label ?? $status) . '</strong>. Silakan masuk ke akun Anda untuk meninjau detail perkembangan seleksi.',
                'btn_text'     => 'Buka Riwayat Lamaran',
                'btn_url'      => route('profile', ['tab' => 'riwayat']),
                'has_exam'     => false,
            ]
        };
    @endphp

    <div class="wrapper">
        <div class="main-card">
            <!-- Header with Company Logo -->
            <div class="header">
                <table class="header-table">
                    <tr>
                        <td align="left" style="vertical-align: middle;">
                            @if ($logoSrc)
                                <img src="{{ $logoSrc }}" alt="{{ $companyName }}" class="header-logo">
                            @else
                                <span style="font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px;">{{ $companyName }}</span>
                            @endif
                        </td>
                        <td align="right" class="header-category" style="vertical-align: middle;">
                            Rekrutmen Resmi
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="salutation">Yth. Sdr/i. {{ $candidateName }},</div>

                <p class="paragraph">
                    Terima kasih atas minat dan partisipasi Anda dalam proses seleksi penerimaan karyawan di <strong>{{ $companyName }}</strong> untuk posisi <strong>{{ $jobTitle }}</strong>.
                </p>

                <!-- Official Notice Callout -->
                <div class="notice-card" style="border-left-color: {{ $statusInfo['border_color'] }}; background-color: {{ $statusInfo['bg_color'] ?? '#f8fafc' }};">
                    <h3 class="notice-title" style="color: {{ $statusInfo['border_color'] }};">{{ $statusInfo['title'] }}</h3>
                    <p class="notice-desc">{!! $statusInfo['desc'] !!}</p>
                </div>

                <!-- Catatan Khusus Rekruter jika diisi -->
                @if (!empty(trim($notes ?? '')))
                    <div class="memo-box">
                        <div class="memo-label">Catatan Tim Rekrutmen:</div>
                        <div class="memo-text">"{{ $notes }}"</div>
                    </div>
                @endif

                <!-- Ringkasan Lamaran -->
                <table class="details-table">
                    <tr>
                        <td class="details-label">Posisi yang Dilamar</td>
                        <td class="details-val">: {{ $jobTitle }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Perusahaan</td>
                        <td class="details-val">: {{ $companyName }}</td>
                    </tr>
                    @if ($job?->department)
                        <tr>
                            <td class="details-label">Departemen</td>
                            <td class="details-val">: {{ $job->department->name }}</td>
                        </tr>
                    @endif
                    @if ($application->applied_at)
                        <tr>
                            <td class="details-label">Tanggal Lamaran</td>
                            <td class="details-val">: {{ \Carbon\Carbon::parse($application->applied_at)->translatedFormat('d F Y, H:i') }} WIB</td>
                        </tr>
                    @endif
                </table>

                <!-- Action Button -->
                <div class="btn-wrapper" style="margin: 28px 0 20px 0;">
                    <table border="0" cellspacing="0" cellpadding="0" role="presentation" style="border-collapse: separate;">
                        <tr>
                            <td align="center" bgcolor="#059669" class="btn-action-cell" style="background-color: #059669; border-radius: 8px; border: 1px solid #047857; padding: 0;">
                                <a href="{{ $statusInfo['btn_url'] }}" target="_blank" class="btn-primary" style="display: inline-block; background-color: #059669; color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; text-decoration: none; font-size: 13px; font-weight: 700; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; padding: 12px 28px; border-radius: 8px; text-align: center; letter-spacing: 0.3px; line-height: 1.2;">
                                    <span class="btn-action-text" style="display: inline-block; color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; font-weight: 700; text-decoration: none;">
                                        {{ $statusInfo['btn_text'] }}
                                    </span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>

                @if ($statusInfo['has_exam'])
                    <div class="footnote">
                        <strong>Panduan Teknis:</strong> Untuk kenyamanan dan kelancaran pengerjaan ujian online, disarankan menggunakan perangkat komputer atau laptop dengan koneksi internet yang stabil serta peramban web versi terbaru.
                    </div>
                @endif

                <div class="sign-off">
                    Hormat kami,<br>
                    <strong>Tim Rekrutmen & Pengembangan Talenta</strong><br>
                    {{ $companyName }}
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <strong>Pemberitahuan:</strong> Seluruh proses seleksi dan penerimaan karyawan di <strong>{{ $companyName }}</strong> sama sekali <strong>TIDAK DIPUNGUT BIAYA APAPUN</strong> (Gratis). Harap waspada dan berhati-hati terhadap segala bentuk penipuan yang mengatasnamakan perusahaan atau meminta pembayaran tiket/akomodasi.<br><br>
                Email ini dikirimkan secara otomatis melalui Sistem E-Recruitment {{ $companyName }}. Mohon untuk tidak membalas email ini secara langsung.<br>
                &copy; {{ date('Y') }} {{ $companyName }}. Hak cipta dilindungi undang-undang.
            </div>
        </div>
    </div>
</body>
</html>
