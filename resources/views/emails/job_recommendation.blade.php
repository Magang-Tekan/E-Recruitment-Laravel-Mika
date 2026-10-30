<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekomendasi Lowongan Kerja</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 30px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 24px;
        }
        .greeting {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
        }
        .intro {
            font-size: 13px;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .job-item {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 14px;
            background-color: #fafafa;
        }
        .job-title {
            font-size: 16px;
            font-weight: 700;
            color: #4f46e5;
            margin: 0 0 4px 0;
        }
        .job-meta {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .job-meta span {
            display: inline-block;
            margin-right: 12px;
        }
        .btn-apply {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
        }
        .footer {
            background-color: #f9fafb;
            padding: 18px 24px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="email-card">
        <div class="header">
            <h1>Rekomendasi Lowongan Kerja</h1>
            <p>Notifikasi {{ $period }} Sesuai Minat Karir Anda</p>
        </div>

        <div class="content">
            <div class="greeting">Halo, {{ $user->name }}!</div>
            <div class="intro">
                Berdasarkan kriteria <strong>Minat Kerja</strong> yang Anda daftarkan di portal E-Rekrutmen, berikut adalah rekomendasi lowongan pekerjaan terbaru yang sesuai dengan profil Anda:
            </div>

            @foreach ($matchedJobs as $job)
                <div class="job-item">
                    <div class="job-title">{{ $job->title }}</div>
                    <div class="job-meta">
                        <span>🏢 {{ $job->company ? $job->company->name : 'Perusahaan' }}</span>
                        <span>📍 {{ $job->location ?: 'Indonesia' }}</span>
                        @if ($job->department)
                            <span>📂 {{ $job->department->name }}</span>
                        @endif
                    </div>
                    @if ($job->salary_min || $job->salary_max)
                        <div style="font-size: 12px; font-weight: 600; color: #059669; margin-bottom: 10px;">
                            Estimasi Gaji: Rp {{ number_format($job->salary_min ?: 0, 0, ',', '.') }} - Rp {{ number_format($job->salary_max ?: 0, 0, ',', '.') }}
                        </div>
                    @endif
                    <a href="{{ route('jobs.show', $job->id) }}" class="btn-apply">Lihat & Melamar Lowongan</a>
                </div>
            @endforeach

            <div style="margin-top: 24px; text-align: center;">
                <a href="{{ route('profile', ['tab' => 'pengalaman']) }}" style="font-size: 12px; color: #6b7280; text-decoration: underline;">
                    Ubah Minat Kerja atau Berhenti Menerima Notifikasi Ini
                </a>
            </div>
        </div>

        <div class="footer">
            Anda menerima email ini karena mengaktifkan notifikasi berkala rekomendasi pekerjaan ({{ $period }}).<br>
            Email ini dikirim secara otomatis oleh Sistem E-Rekrutmen MIKA CAREER. Mohon untuk tidak membalas email ini.<br>
            &copy; {{ date('Y') }} MIKA CAREER. Hak cipta dilindungi undang-undang.
        </div>
    </div>
</body>
</html>
