<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light">
    <title>Pemberitahuan Pengangkatan &amp; Aktivasi Portal Karyawan</title>
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
            border-left: 4px solid #059669;
            background-color: #f0fdf4;
            border-top: 1px solid #bbf7d0;
            border-right: 1px solid #bbf7d0;
            border-bottom: 1px solid #bbf7d0;
        }
        .notice-title {
            font-size: 14px;
            font-weight: 700;
            color: #047857;
            margin: 0 0 6px 0;
        }
        .notice-desc {
            font-size: 13px;
            color: #334155;
            line-height: 1.65;
            margin: 0;
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
            width: 40%;
            color: #64748b;
            font-weight: 500;
        }
        .details-val {
            width: 60%;
            color: #0f172a;
            font-weight: 600;
        }
        .btn-wrapper {
            margin: 28px 0 20px 0;
            text-align: left;
        }
        .btn-action-cell {
            background-color: #059669 !important;
        }
        .btn-action-text {
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
        $company = $employeeProfile->company ?? $job?->company;
        $department = $employeeProfile->department ?? $job?->department;
        $candidateName = $employeeProfile->full_name ?? ($application->applicantProfile?->full_name ?? ($employeeProfile->user?->name ?? 'Karyawan'));
        $positionTitle = $employeeProfile->position_title ?? ($job?->title ?? 'Karyawan');
        $companyName = $company?->name ?? config('app.name', 'MIKA CAREER');

        $typeLabel = match ($employeeProfile->employee_type) {
            'contract'   => 'Karyawan Kontrak (PKWT)',
            'internship' => 'Peserta Magang / Internship',
            'probation'  => 'Masa Percobaan (Probation)',
            default      => 'Karyawan Tetap (Permanent)',
        };

        $startDateText = $employeeProfile->contract_start_date
            ? $employeeProfile->contract_start_date->translatedFormat('d F Y')
            : '-';
        $endDateText = $employeeProfile->contract_end_date
            ? $employeeProfile->contract_end_date->translatedFormat('d F Y')
            : ($employeeProfile->employee_type === 'permanent' ? 'Tanpa Batas Waktu' : 'Belum Ditentukan');

        $logoPath = public_path('images/mikaaaa.png');
        $hasLogo = file_exists($logoPath);
        $logoSrc = (isset($message) && method_exists($message, 'embed') && $hasLogo)
            ? $message->embed($logoPath)
            : ($hasLogo ? asset('images/mikaaaa.png') : null);
    @endphp

    <div class="wrapper">
        <div class="main-card">
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
                            Onboarding HR
                        </td>
                    </tr>
                </table>
            </div>

            <div class="content">
                <div class="salutation">Yth. Sdr/i. {{ $candidateName }},</div>

                <p class="paragraph">
                    Selamat bergabung di keluarga besar <strong>{{ $companyName }}</strong>. Akun Anda telah resmi diaktifkan sebagai <strong>{{ $typeLabel }}</strong> dan kini dapat mengakses <strong>Portal Karyawan</strong>.
                </p>

                <div class="notice-card">
                    <h3 class="notice-title">Pengangkatan &amp; Aktivasi Portal Karyawan</h3>
                    <p class="notice-desc">
                        Data kepegawaian Anda telah dibuat oleh tim Human Resources. Silakan masuk menggunakan email dan kata sandi akun yang sama untuk mengakses portal, melihat informasi kepegawaian, serta mengerjakan asesmen karyawan apabila tersedia.
                    </p>
                </div>

                <table class="details-table">
                    <tr>
                        <td class="details-label">Perusahaan</td>
                        <td class="details-val">: {{ $companyName }}</td>
                    </tr>
                    @if ($department)
                        <tr>
                            <td class="details-label">Divisi / Departemen</td>
                            <td class="details-val">: {{ $department->name }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="details-label">Posisi / Jabatan</td>
                        <td class="details-val">: {{ $positionTitle }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Status Hubungan Kerja</td>
                        <td class="details-val">: {{ $typeLabel }}</td>
                    </tr>
                    <tr>
                        <td class="details-label">Tanggal Mulai Bekerja</td>
                        <td class="details-val">: {{ $startDateText }}</td>
                    </tr>
                    @if ($employeeProfile->employee_type !== 'permanent')
                        <tr>
                            <td class="details-label">Bekerja Sampai</td>
                            <td class="details-val">: {{ $endDateText }}</td>
                        </tr>
                    @endif
                </table>

                <div class="btn-wrapper">
                    <table border="0" cellspacing="0" cellpadding="0" role="presentation" style="border-collapse: separate;">
                        <tr>
                            <td align="center" bgcolor="#059669" class="btn-action-cell" style="background-color: #059669; border-radius: 8px; border: 1px solid #047857; padding: 0;">
                                <a href="{{ route('employee.dashboard') }}" target="_blank" style="display: inline-block; background-color: #059669; color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; text-decoration: none; font-size: 13px; font-weight: 700; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; padding: 12px 28px; border-radius: 8px; text-align: center; letter-spacing: 0.3px; line-height: 1.2;">
                                    <span class="btn-action-text" style="display: inline-block; color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; font-weight: 700; text-decoration: none;">
                                        Buka Portal Karyawan
                                    </span>
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="footnote">
                    <strong>Catatan Akun:</strong> Gunakan email dan kata sandi akun pelamar Anda sebelumnya untuk masuk ke Portal Karyawan. Apabila ada ketidaksesuaian data penempatan atau jadwal mulai kerja, silakan hubungi tim HR {{ $companyName }}.
                </div>

                <div class="sign-off">
                    Hormat kami,<br>
                    <strong>Tim Human Resources</strong><br>
                    {{ $companyName }}
                </div>
            </div>

            <div class="footer">
                Email pemberitahuan resmi ini dikirimkan secara otomatis melalui Sistem E-Recruitment &amp; HR {{ $companyName }}. Mohon tidak membalas email ini secara langsung.<br>
                &copy; {{ date('Y') }} {{ $companyName }}. Hak cipta dilindungi undang-undang.
            </div>
        </div>
    </div>
</body>
</html>
