<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Tes Kompetensi - {{ $candidateName }}</title>
    <style>
        @page {
            margin: 10mm 12mm 10mm 12mm;
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
            color: #1E293B;
            background-color: #FFFFFF;
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

        tr {
            page-break-inside: avoid;
        }

        /* Header Banner */
        .header-banner {
            background-color: #16264A;
            color: #FFFFFF;
            padding: 12px 16px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .header-tag {
            color: #E8B95A;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .header-title {
            color: #FFFFFF;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.3px;
            margin: 0;
        }

        .header-doc-id {
            color: #FFFFFF;
            font-family: monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .header-doc-date {
            color: #94A3B8;
            font-size: 8px;
            margin-top: 3px;
        }

        /* Top Details & Score Section */
        .info-card {
            border: 1px solid #CBD5E1;
            border-radius: 5px;
            background-color: #FFFFFF;
            padding: 6px 10px;
        }

        .info-table td {
            padding: 2px 4px;
            font-size: 8.5px;
            vertical-align: middle;
        }

        .info-label {
            color: #64748B;
            width: 78px;
            font-weight: 500;
        }

        .info-colon {
            width: 6px;
            color: #94A3B8;
        }

        .info-value {
            color: #0F172A;
            font-weight: 600;
        }

        /* Score Card Box */
        .score-card {
            border-radius: 5px;
            padding: 8px 6px;
            text-align: center;
        }

        .score-card-pass {
            background-color: #EAF8EE;
            border: 1.5px solid #9FE0B7;
        }

        .score-card-fail {
            background-color: #FDF0ED;
            border: 1.5px solid #F3A99B;
        }

        .score-label {
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #64748B;
            margin-bottom: 2px;
        }

        .score-number {
            font-size: 28px;
            font-weight: 800;
            line-height: 1;
        }

        .score-denom {
            font-size: 12px;
            font-weight: 600;
            color: #64748B;
        }

        .badge {
            display: inline-block;
            padding: 2.5px 12px;
            border-radius: 10px;
            font-size: 8.5px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .badge-pass {
            background-color: #10B981;
            color: #FFFFFF;
        }

        .badge-fail {
            background-color: #E11D48;
            color: #FFFFFF;
        }

        .score-threshold-note {
            font-size: 7.5px;
            color: #64748B;
            margin-top: 4px;
            line-height: 1.2;
        }

        /* Narrative Summary Box */
        .narrative-box {
            background-color: #F8FAFC;
            border-left: 3.5px solid #16264A;
            border-top: 1px solid #E2E8F0;
            border-right: 1px solid #E2E8F0;
            border-bottom: 1px solid #E2E8F0;
            border-radius: 0 4px 4px 0;
            padding: 6px 10px;
            font-size: 8.5px;
            color: #334155;
            line-height: 1.35;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        /* 5 Metric Stat Cards */
        .stat-card {
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            background-color: #FFFFFF;
            padding: 6px 3px;
            text-align: center;
        }

        .stat-card-title {
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748B;
            margin-bottom: 2px;
        }

        .stat-card-val {
            font-size: 16px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 1px;
        }

        .stat-card-sub {
            font-size: 7px;
            color: #94A3B8;
        }

        /* Section Headings */
        .section-header {
            margin-top: 10px;
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #16264A;
        }

        .section-title {
            font-size: 10px;
            font-weight: 800;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .section-note {
            font-size: 7px;
            color: #64748B;
            font-style: italic;
        }

        /* Standard Evaluation Table */
        .eval-table {
            border: 1px solid #CBD5E1;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .eval-table th {
            background-color: #16264A;
            color: #FFFFFF;
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 4px 6px;
            border: none;
        }

        .eval-table td {
            padding: 4px 6px;
            font-size: 8px;
            border-bottom: 1px solid #E2E8F0;
            vertical-align: middle;
        }

        .eval-table tr:nth-child(even) td {
            background-color: #F8FAFC;
        }

        .eval-table tr:last-child td {
            border-bottom: none;
        }

        /* Progress Bar with 60% Marker */
        .bar-wrapper {
            position: relative;
            height: 10px;
            background-color: #EEF2F6;
            border-radius: 2px;
            overflow: hidden;
            border: 1px solid #E2E8F0;
            width: 100%;
        }

        .bar-fill {
            height: 10px;
            border-radius: 2px 0 0 2px;
        }

        .bar-marker {
            position: absolute;
            left: 60%;
            top: 0;
            height: 10px;
            width: 1.5px;
            background-color: #64748B;
            z-index: 5;
        }

        .bar-color-pass {
            background-color: #10B981;
        }

        .bar-color-fail {
            background-color: #F43F5E;
        }

        /* Violations Report Box */
        .violation-card {
            border-radius: 4px;
            padding: 6px 10px;
            margin-top: 6px;
            margin-bottom: 6px;
        }

        .violation-clean {
            background-color: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #166534;
        }

        .violation-dirty {
            background-color: #FFFBEB;
            border: 1px solid #FDE68A;
            color: #92400E;
        }

        .violation-danger {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
        }

        /* Sign / Footer Note */
        .footer-note {
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid #E2E8F0;
            font-size: 7px;
            color: #94A3B8;
        }
    </style>
</head>

<body>

    <!-- Header Banner -->
    <div class="header-banner">
        <table class="w-full">
            <tr>
                <td style="vertical-align: middle; text-align: left;">
                    <div class="header-tag">— {{ strtoupper($testCategoryName) }}</div>
                    <div class="header-title">Laporan Hasil Tes Kompetensi</div>
                </td>
                <td style="vertical-align: middle; text-align: right; width: 220px;">
                    <div class="header-doc-id">{{ $docId }}</div>
                    <div class="header-doc-date">Dibuat {{ $createdDate }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Candidate Meta Details & Score Card -->
    <table class="w-full" style="margin-bottom: 4px;">
        <tr>
            <!-- Left: Meta Details Table -->
            <td style="width: 68%; vertical-align: top; padding-right: 8px;">
                <div class="info-card">
                    <table class="info-table w-full">
                        <tr>
                            <td class="info-label">Nama Peserta</td>
                            <td class="info-colon">:</td>
                            <td class="info-value" style="font-size: 9px; color: #0F172A;">{{ $candidateName }}</td>
                            <td class="info-label" style="padding-left: 8px;">Tanggal Ujian</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $testDateFormatted }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Instansi / Unit</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $companyName }}</td>
                            <td class="info-label" style="padding-left: 8px;">Waktu Mulai</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $startedAtFormatted }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Status Peserta</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $participantStatus }}</td>
                            <td class="info-label" style="padding-left: 8px;">Dikumpulkan</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $finishedAtFormatted }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Token / ID Ujian</td>
                            <td class="info-colon">:</td>
                            <td class="info-value" style="font-family: monospace;">{{ $token }}</td>
                            <td class="info-label" style="padding-left: 8px;">Waktu Terpakai</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $durationFormatted }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Paket Soal</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $packageName }}</td>
                            <td class="info-label" style="padding-left: 8px;">Pengumpulan</td>
                            <td class="info-colon">:</td>
                            <td class="info-value">{{ $submissionType }}</td>
                        </tr>
                    </table>
                </div>
            </td>

            <!-- Right: Final Score Card -->
            <td style="width: 32%; vertical-align: top;">
                <div class="score-card {{ $isPassed ? 'score-card-pass' : 'score-card-fail' }}">
                    <div class="score-label">NILAI AKHIR</div>
                    <div class="score-number" style="color: {{ $isPassed ? '#0B6A36' : '#991B1B' }};">
                        {{ round($score) }} <span class="score-denom">/ 100</span>
                    </div>
                    <div>
                        <span class="badge {{ $isPassed ? 'badge-pass' : 'badge-fail' }}">
                            {{ $isPassed ? 'LULUS' : 'TIDAK LULUS' }}
                        </span>
                    </div>
                    <div class="score-threshold-note">
                        Batas kelulusan (KKM): {{ $passingScore }}%<br>
                        (Minimal {{ $requiredCorrect }} dari {{ $totalQuestions }} soal benar)
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Narrative Summary Box -->
    <div class="narrative-box">
        <strong>Ringkasan Hasil Evaluasi:</strong> {{ $narrativeText }}
    </div>

    <!-- 5 Metric Stat Cards -->
    <table class="w-full" style="margin-bottom: 8px;">
        <tr>
            <!-- Card 1: Benar -->
            <td style="width: 20%; padding-right: 3px;">
                <div class="stat-card" style="border-top: 3px solid #10B981;">
                    <div class="stat-card-title">Benar</div>
                    <div class="stat-card-val" style="color: #059669;">{{ $correctCount }}</div>
                    <div class="stat-card-sub">{{ $correctPct }}% dari total</div>
                </div>
            </td>
            <!-- Card 2: Salah -->
            <td style="width: 20%; padding-right: 3px; padding-left: 2px;">
                <div class="stat-card" style="border-top: 3px solid #EF4444;">
                    <div class="stat-card-title">Salah</div>
                    <div class="stat-card-val" style="color: #DC2626;">{{ $wrongCount }}</div>
                    <div class="stat-card-sub">{{ $wrongPct }}% dari total</div>
                </div>
            </td>
            <!-- Card 3: Kosong -->
            <td style="width: 20%; padding-right: 3px; padding-left: 2px;">
                <div class="stat-card" style="border-top: 3px solid #F59E0B;">
                    <div class="stat-card-title">Kosong</div>
                    <div class="stat-card-val" style="color: #D97706;">{{ $emptyCount }}</div>
                    <div class="stat-card-sub">{{ $emptyPct }}% dari total</div>
                </div>
            </td>
            <!-- Card 4: Pelanggaran -->
            <td style="width: 20%; padding-right: 3px; padding-left: 2px;">
                <div class="stat-card" style="border-top: 3px solid #475569;">
                    <div class="stat-card-title">Pelanggaran</div>
                    <div class="stat-card-val" style="color: #334155;">{{ $violationsCount }}</div>
                    <div class="stat-card-sub">Toleransi maks. 3x</div>
                </div>
            </td>
            <!-- Card 5: Waktu Terpakai -->
            <td style="width: 20%; padding-left: 2px;">
                <div class="stat-card" style="border-top: 3px solid #6366F1;">
                    <div class="stat-card-title">Waktu Pakai</div>
                    <div class="stat-card-val" style="color: #4F46E5; font-size: 14px;">{{ $durationShort }}</div>
                    <div class="stat-card-sub">dari {{ $durationLimitMinutes }} menit</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Nilai Per Aspek -->
    <div style="margin-top: 4px;">
        <table class="w-full section-header">
            <tr>
                <td class="section-title text-left">Nilai per Aspek</td>
                <td class="section-note text-right">Garis vertikal abu-abu menandai acuan 60%</td>
            </tr>
        </table>

        <table class="eval-table w-full">
            <thead>
                <tr>
                    <th style="width: 32%; text-align: left;">Aspek</th>
                    <th style="width: 10%; text-align: center;">Poin</th>
                    <th style="width: 26%; text-align: left;">Capaian</th>
                    <th style="width: 8%; text-align: right;">Nilai</th>
                    <th style="width: 24%; text-align: left;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aspectEvaluation['aspects'] as $aspect)
                    @php
                        $aspectBarColor = $aspect['status_type'] === 'success'
                            ? '#10B981'
                            : ($aspect['status_type'] === 'danger' ? '#F43F5E' : '#D97706');
                        $aspectStatusStyle = $aspect['status_type'] === 'success'
                            ? 'background-color: #EAF8EE; color: #0B6A36; border: 1px solid #9FE0B7;'
                            : ($aspect['status_type'] === 'danger'
                                ? 'background-color: #FDF0ED; color: #991B1B; border: 1px solid #F3A99B;'
                                : 'background-color: #FFFBEB; color: #92400E; border: 1px solid #FDE68A;');
                    @endphp
                    <tr>
                        <td class="font-bold" style="color: #0F172A;">{{ $aspect['name'] }}</td>
                        <td class="text-center" style="font-weight: 600; color: #475569;">{{ $aspect['poin_str'] }}</td>
                        <td>
                            <div class="bar-wrapper">
                                <div class="bar-marker"></div>
                                <div class="bar-fill" style="background-color: {{ $aspectBarColor }}; width: {{ min(100, $aspect['score']) }}%;"></div>
                            </div>
                        </td>
                        <td class="text-right" style="font-weight: 800; font-size: 10px; color: {{ $aspectBarColor }};">
                            {{ $aspect['score'] }}
                        </td>
                        <td>
                            <span style="display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 7.5px; font-weight: 700; {{ $aspectStatusStyle }}">
                                {{ $aspect['status'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="color: #94A3B8; padding: 8px;">Tidak ada data aspek tersedia</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if (!empty($aspectEvaluation['flagged_for_interview']))
            <div class="narrative-box" style="margin-top: 4px; margin-bottom: 4px;">
                <strong>Catatan Wawancara:</strong> Aspek yang perlu didalami saat wawancara —
                {{ implode(', ', $aspectEvaluation['flagged_for_interview']) }}.
            </div>
        @endif

        <div class="score-threshold-note" style="margin-top: 3px;">
            Syarat lulus: skor total di atas 60 dan nilai Etika &amp; Integritas minimal 70. Aspek lain di bawah 50 ditandai untuk digali saat wawancara (tidak menggugurkan).
        </div>
    </div>

    <!-- Ketepatan Per Materi Uji -->
    <div style="margin-top: 6px;">
        <table class="w-full section-header">
            <tr>
                <td class="section-title text-left">Ketepatan per Materi Uji</td>
                <td class="section-note text-right">Diurutkan dari materi yang paling dikuasai</td>
            </tr>
        </table>

        <table class="eval-table w-full">
            <thead>
                <tr>
                    <th style="width: 6%; text-align: center;">No</th>
                    <th style="width: 44%; text-align: left;">Materi Uji</th>
                    <th style="width: 14%; text-align: center;">Benar</th>
                    <th style="width: 24%; text-align: left;">Grafik Ketepatan</th>
                    <th style="width: 12%; text-align: right;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($materiList as $materi)
                    @php
                        $mPass = $materi['percentage'] >= 60;
                    @endphp
                    <tr>
                        <td class="text-center" style="color: #64748B;">{{ $no++ }}</td>
                        <td style="font-weight: 600; color: #1E293B;">{{ $materi['name'] }}</td>
                        <td class="text-center" style="color: #475569;">{{ $materi['correct'] }} / {{ $materi['total'] }}</td>
                        <td>
                            <div class="bar-wrapper" style="height: 8px;">
                                <div class="bar-marker" style="height: 8px;"></div>
                                <div class="bar-fill {{ $mPass ? 'bar-color-pass' : 'bar-color-fail' }}"
                                     style="height: 8px; width: {{ min(100, $materi['percentage']) }}%;"></div>
                            </div>
                        </td>
                        <td class="text-right" style="font-weight: 700; color: {{ $mPass ? '#059669' : ($materi['percentage'] > 0 ? '#D97706' : '#DC2626') }};">
                            {{ $materi['percentage'] }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="color: #94A3B8; padding: 8px;">Tidak ada data materi tersedia</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Laporan Pelanggaran Keamanan Sesi -->
    <div style="margin-top: 6px;">
        <table class="w-full section-header" style="margin-bottom: 3px;">
            <tr>
                <td class="section-title text-left">Laporan Pengawasan & Pelanggaran Sesi Ujian</td>
            </tr>
        </table>

        @if($violationsCount == 0)
            <div class="violation-card violation-clean">
                <table class="w-full">
                    <tr>
                        <td style="width: 75px; font-weight: 800; font-size: 10px;">0 KALI</td>
                        <td style="font-size: 8px;">
                            <strong>Status Bersih:</strong> Tidak terdeteksi pelanggaran selama sesi pengerjaan ujian. Peserta mematuhi aturan mode layar penuh dan integritas pengawasan.
                        </td>
                    </tr>
                </table>
            </div>
        @elseif($violationsCount < 3)
            <div class="violation-card violation-dirty">
                <table class="w-full">
                    <tr>
                        <td style="width: 75px; font-weight: 800; font-size: 10px; color: #B45309; vertical-align: top;">{{ $violationsCount }} KALI</td>
                        <td style="font-size: 8px;">
                            <strong>Peringatan Pengawasan:</strong> Terdeteksi {{ $violationsCount }} kali aktivitas di luar jendela ujian / keluar dari mode layar penuh selama sesi pengerjaan.
                            @if (!empty($violationLogs))
                                <div style="margin-top: 4px; padding-top: 3px; border-top: 1px dashed #FDE68A; font-size: 7.5px; color: #78350F; line-height: 1.4;">
                                    @foreach ($violationLogs as $vLog)
                                        <div>• Pelanggaran ke-{{ $vLog['violation'] ?? $loop->iteration }}: {{ $vLog['reason'] ?? 'Keluar dari mode layar penuh / berpindah jendela' }}
                                            @if(!empty($vLog['time']))
                                                <span style="color: #92400E;">({{ \Carbon\Carbon::parse($vLog['time'])->timezone('Asia/Jakarta')->format('H:i:s') }} WIB)</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        @else
            <div class="violation-card violation-danger">
                <table class="w-full">
                    <tr>
                        <td style="width: 75px; font-weight: 800; font-size: 10px; color: #DC2626; vertical-align: top;">{{ $violationsCount }} KALI</td>
                        <td style="font-size: 8px;">
                            <strong>Pelanggaran Maksimal / Sesi Dihentikan:</strong> Peserta telah mencapai batas toleransi 3 kali pelanggaran. Sistem otomatis menghentikan dan mengumpulkan lembar ujian secara paksa.
                            @if (!empty($violationLogs))
                                <div style="margin-top: 4px; padding-top: 3px; border-top: 1px dashed #FECACA; font-size: 7.5px; color: #991B1B; line-height: 1.4;">
                                    @foreach ($violationLogs as $vLog)
                                        <div>• Pelanggaran ke-{{ $vLog['violation'] ?? $loop->iteration }}: {{ $vLog['reason'] ?? 'Keluar dari mode layar penuh / berpindah jendela' }}
                                            @if(!empty($vLog['time']))
                                                <span>({{ \Carbon\Carbon::parse($vLog['time'])->timezone('Asia/Jakarta')->format('H:i:s') }} WIB)</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    </div>

    <!-- Footer Note & Verification -->
    <div class="footer-note">
        <table class="w-full">
            <tr>
                <td class="text-left" style="font-size: 7px; color: #64748B;">
                    Dokumen ini digenerate secara otomatis oleh Sistem CBT & E-Recruitment {{ $companyName }}.<br>
                    Validitas laporan dapat diverifikasi dengan Token Ujian dan ID Dokumen: <strong>{{ $docId }}</strong>.
                </td>
                <td class="text-right" style="vertical-align: bottom; font-size: 7px; color: #94A3B8;">
                    Waktu Cetak: {{ now()->timezone('Asia/Jakarta')->isoFormat('D MMMM Y, HH:mm') }} WIB
                </td>
            </tr>
        </table>
    </div>

    <!-- DomPDF Canvas Page Numbers Script -->
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Helvetica", "normal");
            $size = 7;
            $color = array(0.5, 0.55, 0.6);
            $y = $pdf->get_height() - 20;

            // Kiri: Doc ID dan Peserta
            $leftText = "{{ $docId }} | Hasil Tes: {{ $candidateName }} ({{ $packageName }})";
            $pdf->page_text(34, $y, $leftText, $font, $size, $color);

            // Kanan: Halaman X dari Y
            $pageText = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $pdf->page_text($pdf->get_width() - 95, $y, $pageText, $font, $size, $color);
        }
    </script>

</body>

</html>
