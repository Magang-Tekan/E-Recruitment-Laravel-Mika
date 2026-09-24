<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Tes PAPI Kostick - {{ $participantName }}</title>
    <style>
        @page {
            margin: 8mm 10mm 8mm 10mm;
            size: a4 portrait;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            line-height: 1.35;
            color: #0f172a;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .w-full {
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Top Header */
        .doc-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .system-badge {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .doc-title {
            font-size: 13.5px;
            font-weight: 900;
            color: #0f172a;
            margin: 2px 0 1px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .doc-subtitle {
            font-size: 8px;
            color: #64748b;
        }

        .confidential-tag {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 2.5px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .align-top {
            vertical-align: top;
        }

        .align-middle {
            vertical-align: middle;
        }

        /* Candidate Bio Card */
        .bio-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            padding: 5px 8px;
            margin-bottom: 7px;
        }

        .bio-label {
            font-size: 7.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 2px;
            display: block;
        }

        .bio-value {
            font-size: 10px;
            color: #0f172a;
            font-weight: bold;
        }

        /* Section Headings */
        .section-header {
            margin-top: 8px;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #0f172a;
        }

        .section-title {
            font-size: 10px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .section-sub {
            font-size: 8px;
            color: #64748b;
            font-weight: normal;
        }

        /* Riwayat Jawaban Table (18 columns, full width) */
        .sheet-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 6px;
        }

        .sheet-table td {
            border: 1px solid #94a3b8;
            padding: 3.5px 1px;
            text-align: center;
            line-height: 1.1;
        }

        .cell-num {
            background-color: #fef3c7;
            color: #0f172a;
            font-weight: bold;
            width: 5.55%;
        }

        .cell-ans {
            background-color: #ffffff;
            color: #0f172a;
            font-weight: bold;
            width: 5.55%;
        }

        /* Interpretation Table */
        .interp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 6px;
        }

        .interp-table th {
            background-color: #fde68a;
            color: #0f172a;
            font-weight: 800;
            border: 1px solid #94a3b8;
            padding: 4px 4px;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5px;
        }

        .interp-table td {
            border: 1px solid #94a3b8;
            padding: 3.5px 5px;
            vertical-align: middle;
            line-height: 1.3;
        }

        .col-aspect {
            background-color: #cffafe;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            width: 14%;
        }

        .col-factor-name {
            background-color: #ffffff;
            color: #0f172a;
            width: 25%;
        }

        .col-factor-code {
            background-color: #fef08a;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            width: 6%;
            font-size: 9px;
        }

        .col-score {
            background-color: #ffffff;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            width: 6%;
            font-size: 9px;
        }

        .col-interpretation {
            background-color: #ffffff;
            color: #1e293b;
            width: 49%;
        }

        /* Summary Table */
        .summary-table {
            width: 50%;
            border-collapse: collapse;
            font-size: 9px;
            margin-top: 4px;
            margin-bottom: 8px;
        }

        .summary-table td {
            border: 1px solid #94a3b8;
            padding: 3.5px 6px;
        }

        .summary-label {
            background-color: #fed7aa;
            color: #0f172a;
            font-weight: bold;
            width: 48%;
        }

        .summary-val {
            background-color: #cffafe;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            width: 22%;
        }

        .summary-note {
            background-color: #ffffff;
            color: #dc2626;
            font-weight: bold;
            width: 30%;
            text-align: center;
        }

        /* Footer */
        .doc-footer {
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            font-size: 7.5px;
            color: #64748b;
            margin-top: 8px;
        }
    </style>
</head>

<body>

    <!-- Document Header -->
    <div class="doc-header">
        <table class="w-full">
            <tr>
                @php
                    $logoPath = public_path('images/mikaaaa.png');
                    $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
                @endphp
                @if ($logoBase64)
                    <td class="text-left" style="vertical-align: middle; width: 44px; padding-right: 8px;">
                        <img src="{{ $logoBase64 }}" style="height: 36px; width: auto; display: block;" alt="Mika Logo">
                    </td>
                @endif
                <td class="text-left" style="vertical-align: middle;">
                    <span class="system-badge">Mika Career & Assessment System</span>
                    <h1 class="doc-title">Laporan Hasil Evaluasi Tes Kepribadian PAPI Kostick</h1>
                    <span class="doc-subtitle">Personality and Preference Inventory Standard Evaluation Report</span>
                </td>
                <td class="text-right" style="vertical-align: middle; width: 160px;">
                    <span class="confidential-tag">Rahasia / Confidential</span>
                    <div style="font-size: 7.5px; color: #64748b; margin-top: 2px;">
                        No. Dokumen: #EV-{{ str_pad($attempt->id, 5, '0', STR_PAD_LEFT) }}<br>
                        Tanggal: {{ now()->translatedFormat('d F Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @php
        $isEmployeeAttempt = ($attempt->attempt_type === 'employee') || empty($attempt->job_application_id);
        $applicant = $attempt->jobApplication?->applicantProfile;
        $employee = $attempt->user?->employeeProfile;
        $user = $attempt->user ?? $applicant?->user;
        $job = $attempt->jobApplication?->job;
        $company = $job?->company ?? $employee?->company ?? $employee?->department?->company ?? $attempt->test?->department?->company;
        $dept = $job?->department ?? $employee?->department ?? $attempt->test?->department;

        $participantName = !empty($attempt->participant_name)
            ? $attempt->participant_name
            : ($isEmployeeAttempt 
                ? ($employee?->full_name ?? ($user?->name ?? 'Karyawan')) 
                : ($applicant?->full_name ?? ($user?->name ?? 'Pelamar')));

        $participantTitle = $isEmployeeAttempt 
            ? ($employee?->position_title ?? ($dept?->name ?? 'Karyawan Internal')) 
            : ($job?->title ?? '-');

        $participantNik = $isEmployeeAttempt ? ($employee?->nik ?? ($user?->nik ?? '-')) : ($applicant?->nik ?? '-');

        $companyName = $company?->name ?? ($dept?->name ?? 'Autentik Karya Analitika');

        $rawGender = $attempt->participant_gender ?? ($isEmployeeAttempt ? $employee?->gender : $applicant?->gender);
        $genderLabel = match(strtolower($rawGender ?? '')) {
            'male', 'laki-laki', 'pria' => 'Laki-laki',
            'female', 'perempuan', 'wanita' => 'Perempuan',
            default => '-',
        };

        $participantAge = $attempt->participant_age 
            ?? ($employee?->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->age : ($applicant?->birth_date ? \Carbon\Carbon::parse($applicant->birth_date)->age : null));

        $testDateFormatted = $attempt->test_date 
            ? \Carbon\Carbon::parse($attempt->test_date)->translatedFormat('d F Y')
            : \Carbon\Carbon::parse($attempt->finished_at ?? $attempt->started_at ?? now())->translatedFormat('d F Y');

        $employeeTypeLabel = match($employee?->employee_type) {
            'internship' => 'Magang (Internship)',
            'contract' => 'Karyawan Kontrak',
            'probation' => 'Masa Percobaan (Probation)',
            default => 'Karyawan Tetap',
        };
        $statusPeserta = $isEmployeeAttempt ? $employeeTypeLabel : 'Kandidat Pelamar';
    @endphp

    <!-- Candidate / Employee Bio Box -->
    <div class="bio-box">
        <table class="w-full">
            <tr>
                <td class="align-top" style="width: 28%;">
                    <div class="bio-label">{{ $isEmployeeAttempt ? ($employee?->employee_type === 'internship' ? 'Nama Peserta Magang' : 'Nama Karyawan') : 'Nama Pelamar' }}</div>
                    <div class="bio-value">{{ $participantName }}</div>
                </td>
                <td class="align-top" style="width: 25%;">
                    <div class="bio-label">{{ $isEmployeeAttempt ? 'Jabatan / Departemen' : 'Posisi Lowongan' }}</div>
                    <div class="bio-value">{{ $participantTitle }}</div>
                </td>
                <td class="align-top" style="width: 23%;">
                    <div class="bio-label">Unit / Perusahaan</div>
                    <div class="bio-value">{{ $company?->name ?? ($dept?->name ?? 'Autentik Karya Analitika') }}</div>
                </td>
                <td class="align-top" style="width: 24%;">
                    <div class="bio-label">Tanggal Pelaksanaan Tes</div>
                    <div class="bio-value">{{ $testDateFormatted }}</div>
                </td>
            </tr>
            <tr>
                <td class="align-top" style="padding-top: 5px;">
                    <div class="bio-label">NIK / Identitas</div>
                    <div class="bio-value" style="font-size: 10px;">{{ $participantNik }}</div>
                </td>
                <td class="align-top" style="padding-top: 5px;">
                    <div class="bio-label">Email</div>
                    <div class="bio-value" style="font-size: 10px;">{{ $user?->email ?? '-' }}</div>
                </td>
                <td class="align-top" style="padding-top: 5px;">
                    <div class="bio-label">Jenis Kelamin / Usia</div>
                    <div class="bio-value" style="font-size: 10px;">
                        {{ $genderLabel }}
                        @if ($participantAge)
                            ({{ $participantAge }} Thn)
                        @endif
                    </div>
                </td>
                <td class="align-top" style="padding-top: 5px;">
                    <div class="bio-label">Status Peserta</div>
                    <div class="bio-value" style="font-size: 10px; font-weight: bold; color: {{ $isEmployeeAttempt && $employee?->employee_type === 'internship' ? '#b45309' : '#0f172a' }};">
                        {{ $statusPeserta }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================== -->
    <!-- BAGIAN 1: RIWAYAT JAWABAN (ATAS)            -->
    <!-- ========================================== -->
    <div class="section-header">
        <table class="w-full">
            <tr>
                <td class="text-left">
                    <span class="section-title">I. Riwayat Jawaban (Lembar Jawaban PAPI Kostick)</span>
                </td>
                <td class="text-right">
                    <span class="section-sub">90 Butir Soal (Pilihan a/b) &bull; Urutan Kiri ke Kanan (1–90)</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- 10 Rows x 18 Columns Grid -->
    <table class="sheet-table">
        <tbody>
            @foreach ($sheetRows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td class="{{ $cell['isNum'] ? 'cell-num' : 'cell-ans' }}">
                            {{ $cell['text'] }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Keterangan Lembar Jawaban -->
    <div style="font-size: 7.5px; color: #64748b; margin-bottom: 8px; padding: 3px 6px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 2px;">
        <table class="w-full">
            <tr>
                <td class="text-left">
                    <strong>Keterangan:</strong> Sel kuning merupakan <strong>Nomor Soal</strong>, sel putih merupakan <strong>Pilihan Jawaban Peserta (a/b)</strong>.
                </td>
                <td class="text-right" style="width: 250px;">
                    <strong>Total Atas (Role):</strong> {{ $roleScore }} | <strong>Total Bawah (Need):</strong> {{ $needScore }} (Total: {{ $roleScore + $needScore }})
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================== -->
    <!-- BAGIAN 2: TABEL INTERPRETASI (BAWAH)       -->
    <!-- ========================================== -->
    <div class="section-header" style="page-break-before: auto;">
        <table class="w-full">
            <tr>
                <td class="text-left">
                    <span class="section-title">II. Profil & Interpretasi 20 Faktor Kepribadian</span>
                </td>
                <td class="text-right">
                    <span class="section-sub">7 Aspek Utama - Rentang Skor 0–9</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="interp-table">
        <thead>
            <tr>
                <th style="width: 14%;">ASPEK</th>
                <th style="width: 25%;">FAKTOR</th>
                <th style="width: 6%;">FAKTOR</th>
                <th style="width: 6%;">NILAI</th>
                <th style="width: 49%;">INTERPRETASI</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($papiAspects as $aspect)
                @php $aspectCount = count($aspect['factors']); @endphp
                @foreach ($aspect['factors'] as $fIdx => $factor)
                    @php
                        $code = $factor['code'];
                        $score = $scores[$code] ?? ($interpretations[$code]['score'] ?? 0);
                        $interpText = $interpretations[$code]['interpretation'] ?? ($interpretations[$code]['description'] ?? '-');
                    @endphp
                    <tr>
                        @if ($fIdx === 0)
                            <td rowspan="{{ $aspectCount }}" class="col-aspect">
                                {{ $aspect['name'] }}
                            </td>
                        @endif
                        <td class="col-factor-name">
                            {{ $factor['name'] }}
                        </td>
                        <td class="col-factor-code">
                            {{ $code }}
                        </td>
                        <td class="col-score">
                            {{ $score }}
                        </td>
                        <td class="col-interpretation">
                            {{ $interpText }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <!-- Ringkasan Total Atas & Total Bawah Sesuai Gambar Acuan -->
    <table class="summary-table">
        <tr>
            <td class="summary-label">Total Atas</td>
            <td class="summary-val">{{ $roleScore }}</td>
            <td class="summary-note">(Harus 45)</td>
        </tr>
        <tr>
            <td class="summary-label">Total Bawah</td>
            <td class="summary-val">{{ $needScore }}</td>
            <td class="summary-note">(Harus 45)</td>
        </tr>
    </table>

    <div style="font-size: 7.5px; color: #64748b; line-height: 1.35; padding: 4px 6px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 2px; margin-top: 4px;">
        <strong>Catatan Interpretasi:</strong> Hasil evaluasi PAPI Kostick menggambarkan kecenderungan peran (<em>Role</em>/Total Atas) dan kebutuhan (<em>Need</em>/Total Bawah) individu dalam dinamika kerja. Nilai 0–9 menggambarkan intensitas tiap faktor secara komprehensif. Pola keseluruhan harus dianalisis bersama aspek psikologis lainnya.
    </div>

    <!-- Footer -->
    <div class="doc-footer">
        <table class="w-full">
            <tr>
                <td class="text-left">
                    Laporan Resmi Hasil Evaluasi Tes PAPI Kostick - Peserta: {{ $participantName }}
                </td>
                <td class="text-right">
                    Digenerate otomatis oleh Mika Career & Assessment System
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
