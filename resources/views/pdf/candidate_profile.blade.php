<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Profil Pelamar' }}</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: helvetica, sans-serif;
        }

        @page {
            margin: 24px 28px 24px 28px;
            font-family: helvetica, sans-serif;
        }

        body,
        table,
        th,
        td,
        div,
        p,
        span,
        h1,
        h2,
        h3,
        h4,
        strong,
        b,
        em,
        i {
            font-family: helvetica, sans-serif;
        }

        body {
            font-family: helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        table {
            font-family: helvetica, sans-serif;
        }

        th,
        td {
            font-family: helvetica, sans-serif;
        }

        .avoid-break {
            page-break-inside: avoid;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            margin-bottom: 12px;
        }

        .header-table td {
            padding-bottom: 8px;
        }

        .title {
            font-size: 15.5pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.3px;
            margin: 0;
            line-height: 1.2;
        }

        .subtitle {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 3px;
        }

        .badge-app {
            background-color: #0f172a;
            color: #ffffff;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
        }

        .badge-status {
            padding: 2.5px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 500;
            display: inline-block;
        }

        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 3px;
            margin-top: 11px;
            margin-bottom: 5px;
            page-break-after: avoid;
            break-after: avoid;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .info-table td {
            padding: 3px 5px;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .info-label {
            width: 22%;
            color: #64748b;
            font-size: 8.5pt;
        }

        .info-sep {
            width: 2%;
            color: #94a3b8;
        }

        .info-value {
            width: 26%;
            color: #0f172a;
            font-weight: normal;
            font-size: 8.5pt;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            margin-bottom: 6px;
            border: 1px solid #cbd5e1;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .data-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 8.5pt;
            vertical-align: top;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .bold {
            font-weight: bold;
        }

        .text-muted {
            color: #64748b;
        }

        .text-primary {
            color: #2563eb;
        }

        .footer {
            margin-top: 6px;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
            font-size: 7.5pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>

<body>

    @php
        // 1. Konversi Foto Pelamar ke Base64 untuk keandalan penuh pada DomPDF
        $photoBase64 = null;
        $rawPhoto = $profile->photo ?? ($candidate->avatar ?? null);
        if (!empty($rawPhoto)) {
            if (filter_var($rawPhoto, FILTER_VALIDATE_URL)) {
                try {
                    $ctx = stream_context_create([
                        'http' => ['timeout' => 3],
                        'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false]
                    ]);
                    $contents = @file_get_contents($rawPhoto, false, $ctx);
                    if ($contents) {
                        $photoBase64 = 'data:image/jpeg;base64,' . base64_encode($contents);
                    }
                } catch (\Throwable $e) {}
            } else {
                $localPath = null;
                if (file_exists(public_path('storage/' . $rawPhoto))) {
                    $localPath = public_path('storage/' . $rawPhoto);
                } elseif (file_exists(storage_path('app/public/' . $rawPhoto))) {
                    $localPath = storage_path('app/public/' . $rawPhoto);
                } elseif (file_exists(public_path($rawPhoto))) {
                    $localPath = public_path($rawPhoto);
                }

                if ($localPath && is_file($localPath)) {
                    $mime = @mime_content_type($localPath) ?: 'image/jpeg';
                    $photoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($localPath));
                }
            }
        }

        // 2. Formatter Gaji Otomatis (Menambahkan 'Rp ' dan pemisah ribuan titik '.')
        $formatSalary = function ($val) {
            if (empty($val) || trim((string) $val) === '-') {
                return '-';
            }
            $val = trim((string) $val);
            if (is_numeric($val)) {
                return 'Rp ' . number_format((float) $val, 0, ',', '.');
            }
            // Format angka >= 4 digit atau format angka dengan titik sebelumnya jika ada penjelasan lanjutan (contoh: "10000000 karena banyak")
            return preg_replace_callback(
                '/(?:rp\.?\s*)?(\d{1,3}(?:\.\d{3})+|\d{4,})/i',
                function ($m) {
                    $num = str_replace('.', '', $m[1]);
                    return 'Rp ' . number_format((float) $num, 0, ',', '.');
                },
                $val,
                1
            );
        };
    @endphp

    <!-- Header Dokumen -->
    <table class="header-table">
        <tr>
            <!-- Foto Pelamar -->
            <td style="width: 84px; vertical-align: middle; padding-right: 12px;">
                @if ($photoBase64)
                    <img src="{{ $photoBase64 }}" style="width: 78px; height: 88px; border-radius: 4px; border: 1px solid #cbd5e1; display: block;" alt="Foto Pelamar">
                @else
                    <div style="width: 78px; height: 88px; border: 1px dashed #cbd5e1; border-radius: 4px; background-color: #f8fafc; text-align: center; color: #94a3b8; font-size: 7.5pt; line-height: 1.2;">
                        <div style="padding-top: 30px;">
                            <strong>PAS FOTO</strong><br>
                            <span style="font-size: 6.5pt; color: #94a3b8;">(3 x 4)</span>
                        </div>
                    </div>
                @endif
            </td>

            <td style="vertical-align: middle;">
                <h1 class="title">{{ $profile->full_name ?? $candidate->name }}</h1>
                <div class="subtitle">
                    {{ $profile->city ? $profile->city . ($profile->province ? ', ' . $profile->province : '') : 'Indonesia' }}
                </div>
                <div style="font-size: 8pt; color: #64748b; margin-top: 2px;">
                    {{ $profile->phone ?? '-' }} &ndash; {{ $candidate->email }}
                </div>
                @if ($isApplicant && $application)
                    @php
                        $statusLower = strtolower(trim($application->status ?? ''));
                        $statusBg = '#f1f5f9';
                        $statusColor = '#1e293b';
                        $statusBorder = '#cbd5e1';

                        if (in_array($statusLower, ['reviewed', 'lolos berkas'])) {
                            $statusBg = '#eff6ff';
                            $statusColor = '#1e40af';
                            $statusBorder = '#bfdbfe';
                        } elseif (in_array($statusLower, ['interview', 'wawancara'])) {
                            $statusBg = '#faf5ff';
                            $statusColor = '#6b21a8';
                            $statusBorder = '#e9d5ff';
                        } elseif (in_array($statusLower, ['accepted', 'hired', 'diterima'])) {
                            $statusBg = '#f0fdf4';
                            $statusColor = '#166534';
                            $statusBorder = '#bbf7d0';
                        } elseif (in_array($statusLower, ['rejected', 'ditolak'])) {
                            $statusBg = '#fef2f2';
                            $statusColor = '#991b1b';
                            $statusBorder = '#fecaca';
                        }
                    @endphp
                    <table style="border-collapse: collapse; margin-top: 5px;">
                        <tr>
                            <td style="padding: 0; vertical-align: middle;">
                                <span class="badge-app">Pelamar Lowongan: {{ $application->job?->title ?? '-' }}{{ $application->job?->company?->name ? ' (' . $application->job->company->name . ')' : '' }}</span>
                            </td>
                            <td style="padding: 0 0 0 8px; vertical-align: middle;">
                                <span class="badge-status" style="background-color: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusBorder }};">
                                    Status: <strong>{{ $application->status_label ?? ucfirst($application->status) }}</strong>
                                </span>
                            </td>
                        </tr>
                    </table>
                @endif
            </td>
            <td style="width: 220px; text-align: right; vertical-align: middle;">
                <div style="font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: bold; white-space: nowrap;">E-Recruitment System</div>
                <div style="font-size: 10.5pt; font-weight: bold; color: #0f172a; margin-top: 2px; white-space: nowrap;">CV &amp; PROFIL LENGKAP</div>
                <div style="font-size: 7.5pt; color: #94a3b8; margin-top: 3px; white-space: nowrap;">Dicetak: {{ now()->translatedFormat('d M Y, H:i') }} WIB</div>
            </td>
        </tr>
    </table>

    <!-- 1. DATA PRIBADI & KONTAK -->
    <div class="section-title">I. Informasi Pribadi & Kontak</div>
    <table class="info-table">
        <tr>
            <td class="info-label">NIK (KTP)</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->nik ?? '-' }}</td>
            <td class="info-label">NPWP</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->npwp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Email</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $candidate->email }}</td>
            <td class="info-label">Telepon / WhatsApp</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->phone ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Tempat, Tanggal Lahir</td>
            <td class="info-sep">:</td>
            <td class="info-value">
                {{ $profile->birth_place ?? '' }}{{ $profile->birth_date ? ', ' . \Carbon\Carbon::parse($profile->birth_date)->translatedFormat('d F Y') : '-' }}
            </td>
            <td class="info-label">Jenis Kelamin</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->gender ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Agama</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->religion ?? '-' }}</td>
            <td class="info-label">Golongan Darah</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->blood_type ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Suku</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->ethnicity ?? '-' }}</td>
            <td class="info-label">Kebangsaan</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->nationality ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Status Pernikahan</td>
            <td class="info-sep">:</td>
            <td class="info-value">
                {{ $profile->marital_status ? ucfirst($profile->marital_status) : '-' }}
                {{ $profile->marriage_date ? ' (' . \Carbon\Carbon::parse($profile->marriage_date)->translatedFormat('d M Y') . ')' : '' }}
            </td>
            <td class="info-label">Kode Pos</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->postal_code ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Alamat Sesuai KTP</td>
            <td class="info-sep">:</td>
            <td class="info-value" colspan="4">
                {{ $profile->ktp_address ?? '-' }}{{ $profile->city ? ', ' . $profile->city : '' }}{{ $profile->province ? ', ' . $profile->province : '' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Alamat Domisili</td>
            <td class="info-sep">:</td>
            <td class="info-value" colspan="4">
                {{ $profile->address ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Transportasi Harian</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->daily_transportation ?? '-' }}</td>
            <td class="info-label">Jarak ke Tempat Kerja</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->work_distance_km ? $profile->work_distance_km . ' km' : '-' }}</td>
        </tr>
        @if (!empty($profile->driving_licenses) && is_array($profile->driving_licenses))
            <tr>
                <td class="info-label">Kepemilikan SIM</td>
                <td class="info-sep">:</td>
                <td class="info-value" colspan="4">
                    @php
                        $licStrings = [];
                        foreach ($profile->driving_licenses as $lic) {
                            if (!empty($lic['type']) || !empty($lic['number'])) {
                                $licStrings[] = ($lic['type'] ?: 'SIM') . ': ' . ($lic['number'] ?: '-');
                            }
                        }
                    @endphp
                    {{ !empty($licStrings) ? implode(' | ', $licStrings) : '-' }}
                </td>
            </tr>
        @endif
    </table>

    @if (!empty($profile->about_me))
        <div
            style="background-color: #f8fafc; border-left: 3px solid #3b82f6; padding: 6px 10px; margin-bottom: 8px; font-size: 9.5px; color: #334155;">
            <strong>Tentang Saya:</strong> {{ $profile->about_me }}
        </div>
    @endif

    <!-- 2. DATA KELUARGA & KONTAK DARURAT -->
    <div class="section-title">II. Susunan Keluarga & Kontak Darurat</div>
    @php
        $family = $profile->family;
    @endphp
    <table class="info-table" style="margin-bottom: 4px;">
        <tr>
            <td class="info-label">Anak Ke-</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->child_sequence ? 'Ke-' . $profile->child_sequence : '-' }}</td>
            <td class="info-label">Dari Jumlah Bersaudara</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->total_siblings ? $profile->total_siblings . ' Bersaudara' : '-' }}</td>
        </tr>
    </table>

    @if ($family && !empty($family->family_members) && count($family->family_members) > 0)
        <table class="data-table" style="margin-bottom: 6px;">
            <thead>
                <tr>
                    <th style="width: 14%;">Hubungan</th>
                    <th style="width: 20%;">Nama Lengkap</th>
                    <th style="width: 12%;">Tgl Lahir</th>
                    <th style="width: 13%;">Tempat Lahir</th>
                    <th style="width: 8%; text-align: center;">Usia</th>
                    <th style="width: 11%;">Pendidikan</th>
                    <th style="width: 11%;">Pekerjaan</th>
                    <th style="width: 11%;">Perusahaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($family->family_members as $mem)
                    <tr>
                        <td class="bold">{{ $mem['relation'] ?? '-' }}</td>
                        <td>{{ $mem['name'] ?? '-' }}</td>
                        <td>{{ !empty($mem['birth_date']) ? \Carbon\Carbon::parse($mem['birth_date'])->format('d/m/Y') : '-' }}
                        </td>
                        <td>{{ $mem['birth_place'] ?? '-' }}</td>
                        <td style="text-align: center;">{{ !empty($mem['age']) ? $mem['age'] . ' Thn' : '-' }}</td>
                        <td>{{ $mem['education'] ?? '-' }}</td>
                        <td>{{ $mem['occupation'] ?? '-' }}</td>
                        <td>{{ $mem['company'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($family && (!empty($family->father_name) || !empty($family->mother_name)))
        <table class="info-table" style="margin-bottom: 6px;">
            <tr>
                <td class="info-label">Nama Ayah</td>
                <td class="info-sep">:</td>
                <td class="info-value">{{ $family->father_name ?? '-' }}
                    ({{ $family->father_occupation ?? 'Pekerjaan: -' }})</td>
                <td class="info-label">Kontak Ayah</td>
                <td class="info-sep">:</td>
                <td class="info-value">{{ $family->father_phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Nama Ibu</td>
                <td class="info-sep">:</td>
                <td class="info-value">{{ $family->mother_name ?? '-' }}
                    ({{ $family->mother_occupation ?? 'Pekerjaan: -' }})</td>
                <td class="info-label">Kontak Ibu</td>
                <td class="info-sep">:</td>
                <td class="info-value">{{ $family->mother_phone ?? '-' }}</td>
            </tr>
            @if (!empty($family->spouse_name))
                <tr>
                    <td class="info-label">Suami / Istri</td>
                    <td class="info-sep">:</td>
                    <td class="info-value">{{ $family->spouse_name }} ({{ $family->spouse_occupation ?? '-' }})</td>
                    <td class="info-label">Kontak Pasangan</td>
                    <td class="info-sep">:</td>
                    <td class="info-value">{{ $family->spouse_phone ?? '-' }}</td>
                </tr>
            @endif
        </table>
    @else
        <div style="font-style: italic; color: #64748b; font-size: 9px; margin-bottom: 6px; padding: 4px;">Data
            keluarga belum diisi.</div>
    @endif

    {{-- Kontak Darurat --}}
    @if ($family)
        @php
            $emergencies = $family->emergency_contacts ?? [];
            $sameHouse = $emergencies['same_house'] ?? [];
            $diffHouse = $emergencies['diff_house'] ?? [];
            $hasStructuredEc = !empty($sameHouse) || !empty($diffHouse);
        @endphp
        @if ($hasStructuredEc || !empty($family->emergency_contact_name) || !empty($family->emergency_contact_phone))
            <div style="font-weight: bold; font-size: 8.5pt; color: #0f172a; margin-top: 8px; margin-bottom: 3px;">
                Kontak Darurat (Bisa Dihubungi Segera)</div>
            <table class="data-table" style="margin-top: 2px; margin-bottom: 8px; border: 1px solid #cbd5e1;">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center; border-right: 1px solid #cbd5e1;">No</th>
                        <th style="width: 25%; border-right: 1px solid #cbd5e1;">Status Tempat Tinggal</th>
                        <th style="width: 32%; border-right: 1px solid #cbd5e1;">Nama Lengkap Kontak</th>
                        <th style="width: 18%; border-right: 1px solid #cbd5e1;">Hubungan</th>
                        <th style="width: 20%;">Nomor Telepon / HP</th>
                    </tr>
                </thead>
                <tbody>
                    @php $ecIndex = 1; @endphp
                    @if ($hasStructuredEc)
                        @foreach ($sameHouse as $ec)
                            <tr>
                                <td style="text-align: center; border-right: 1px solid #cbd5e1;">{{ $ecIndex++ }}
                                </td>
                                <td style="border-right: 1px solid #cbd5e1; font-weight: bold; color: #166534;">Tinggal
                                    Serumah</td>
                                <td style="border-right: 1px solid #cbd5e1; font-weight: bold;">
                                    {{ $ec['name'] ?? '-' }}</td>
                                <td style="border-right: 1px solid #cbd5e1;">{{ $ec['relation'] ?? '-' }}</td>
                                <td style="font-weight: bold; color: #0f172a;">{{ $ec['phone'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                        @foreach ($diffHouse as $ec)
                            <tr>
                                <td style="text-align: center; border-right: 1px solid #cbd5e1;">{{ $ecIndex++ }}
                                </td>
                                <td style="border-right: 1px solid #cbd5e1; font-weight: bold; color: #1e40af;">Tidak
                                    Serumah</td>
                                <td style="border-right: 1px solid #cbd5e1; font-weight: bold;">
                                    {{ $ec['name'] ?? '-' }}</td>
                                <td style="border-right: 1px solid #cbd5e1;">{{ $ec['relation'] ?? '-' }}</td>
                                <td style="font-weight: bold; color: #0f172a;">{{ $ec['phone'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @elseif(!empty($family->emergency_contact_name) || !empty($family->emergency_contact_phone))
                        <tr>
                            <td style="text-align: center; border-right: 1px solid #cbd5e1;">1</td>
                            <td style="border-right: 1px solid #cbd5e1; color: #64748b;">Kontak Utama</td>
                            <td style="border-right: 1px solid #cbd5e1; font-weight: bold;">
                                {{ $family->emergency_contact_name ?? '-' }}</td>
                            <td style="border-right: 1px solid #cbd5e1;">
                                {{ $family->emergency_contact_relation ?? '-' }}</td>
                            <td style="font-weight: bold; color: #0f172a;">
                                {{ $family->emergency_contact_phone ?? '-' }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    @endif

    <!-- 3. RIWAYAT PENDIDIKAN -->
    <div class="section-title">III. Riwayat Pendidikan Formal</div>
    @if ($profile->educations && $profile->educations->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Jenjang</th>
                    <th style="width: 35%;">Nama Sekolah / Institusi</th>
                    <th style="width: 25%;">Jurusan & Program Studi</th>
                    <th style="width: 10%;">IPK / Nilai</th>
                    <th style="width: 15%; text-align: right;">Tahun</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($profile->educations as $edu)
                    <tr>
                        <td class="bold">{{ $edu->degree ?? '-' }}</td>
                        <td>{{ $edu->school_name ?? ($edu->institution_name ?? '-') }}</td>
                        <td>
                            {{ $edu->major ?? '-' }}
                            @if ($edu->study_program && $edu->study_program !== $edu->major)
                                <div style="font-size: 8.5px; color: #64748b;">Prodi: {{ $edu->study_program }}</div>
                            @endif
                        </td>
                        <td class="bold">{{ $edu->gpa ?? '-' }}</td>
                        <td style="text-align: right;">
                            <div>{{ $edu->start_year }} - {{ $edu->end_year ?? 'Sekarang' }}</div>
                            @if ($edu->certificate_path)
                                <div style="font-size: 7pt; color: #059669; font-weight: bold; margin-top: 2px;">
                                    Ijazah Terlampir
                                </div>
                            @elseif (!$edu->end_year)
                                <div style="font-size: 7pt; color: #d97706; margin-top: 2px;">
                                    Sedang Studi
                                </div>
                            @else
                                <div style="font-size: 7pt; color: #94a3b8; margin-top: 2px;">
                                    Ijazah: -
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted" style="font-style: italic; margin: 4px 0 10px 0;">Belum ada data pendidikan formal
            dicantumkan.</p>
    @endif

    <!-- 4. PENGALAMAN KERJA -->
    <div class="section-title">IV. Riwayat Pengalaman Kerja</div>
    @if ($profile->workExperiences && $profile->workExperiences->count() > 0)
        <table class="data-table" style="margin-top: 2px; margin-bottom: 8px; border: 1px solid #cbd5e1;">
            <thead>
                <tr>
                    <th style="width: 4%; text-align: center; border-right: 1px solid #cbd5e1;">No</th>
                    <th style="width: 26%; border-right: 1px solid #cbd5e1;">Perusahaan & Atasan</th>
                    <th style="width: 20%; border-right: 1px solid #cbd5e1;">Posisi / Jabatan</th>
                    <th style="width: 14%; text-align: center; border-right: 1px solid #cbd5e1;">Periode Kerja</th>
                    <th style="width: 16%; border-right: 1px solid #cbd5e1;">Gaji & Fasilitas</th>
                    <th style="width: 20%;">Uraian Tanggung Jawab</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($profile->workExperiences as $idx => $work)
                    <tr>
                        <td style="text-align: center; vertical-align: top; border-right: 1px solid #cbd5e1;">
                            {{ $idx + 1 }}</td>
                        <td style="vertical-align: top; border-right: 1px solid #cbd5e1;">
                            <div style="font-weight: bold; color: #0f172a;">{{ $work->company_name }}</div>
                            @if ($work->business_field)
                                <div style="font-size: 7.5pt; color: #64748b;">Bidang: {{ $work->business_field }}
                                </div>
                            @endif
                            @if ($work->supervisor_name)
                                <div style="font-size: 7.5pt; color: #475569;">Atasan:
                                    <strong>{{ $work->supervisor_name }}</strong></div>
                            @endif
                            @if ($work->company_address)
                                <div style="font-size: 7pt; color: #94a3b8;">{{ $work->company_address }}</div>
                            @endif
                        </td>
                        <td style="vertical-align: top; border-right: 1px solid #cbd5e1;">
                            <div style="font-weight: bold; color: #1e293b;">{{ $work->position }}</div>
                            @if ($work->initial_position && $work->initial_position !== $work->position)
                                <div style="font-size: 7.5pt; color: #64748b;">(Posisi Awal:
                                    {{ $work->initial_position }})</div>
                            @endif
                            @if ($work->employment_type)
                                <div style="font-size: 7.5pt; color: #059669; font-weight: bold;">
                                    {{ $work->employment_type }}</div>
                            @endif
                        </td>
                        <td
                            style="text-align: center; vertical-align: top; border-right: 1px solid #cbd5e1; font-size: 8pt;">
                            <div>
                                {{ $work->start_date ? \Carbon\Carbon::parse($work->start_date)->translatedFormat('M Y') : '-' }}
                            </div>
                            <div style="color: #64748b; font-size: 7pt;">s/d</div>
                            <div
                                style="font-weight: bold; color: {{ $work->currently_working ? '#059669' : '#0f172a' }};">
                                {{ $work->currently_working ? 'Sekarang' : ($work->end_date ? \Carbon\Carbon::parse($work->end_date)->translatedFormat('M Y') : '-') }}
                            </div>
                        </td>
                        <td style="vertical-align: top; border-right: 1px solid #cbd5e1; font-size: 7.5pt;">
                            @if ($work->final_salary)
                                <div style="font-weight: bold; color: #059669;">
                                    {{ $formatSalary($work->final_salary) }}
                                </div>
                            @else
                                <div style="color: #94a3b8;">-</div>
                            @endif
                            @if ($work->benefits)
                                <div style="color: #475569; margin-top: 2px;">{{ $work->benefits }}</div>
                            @endif
                        </td>
                        <td style="vertical-align: top; font-size: 7.5pt; color: #334155; line-height: 1.35;">
                            {{ $work->description ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted" style="font-style: italic; margin: 4px 0 10px 0;">Belum ada pengalaman kerja (Fresh
            Graduate).</p>
    @endif

    <!-- 5. PENGALAMAN ORGANISASI & PRESTASI -->
    <div class="avoid-break">
        <div class="section-title">V. Organisasi & Prestasi</div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <!-- Kolom Organisasi -->
                <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                    <div style="font-weight: bold; font-size: 8.5pt; color: #0f172a; margin-bottom: 3px;">A. Pengalaman
                        Organisasi</div>
                    <table class="data-table" style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th style="width: 8%; text-align: center;">No</th>
                                <th style="width: 47%;">Nama Organisasi</th>
                                <th style="width: 25%;">Jabatan</th>
                                <th style="width: 20%; text-align: center;">Periode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($profile->organizations ?? [] as $oIdx => $org)
                                <tr>
                                    <td style="text-align: center;">{{ $oIdx + 1 }}</td>
                                    <td style="font-weight: bold;">{{ $org->name }}</td>
                                    <td>{{ $org->position }}</td>
                                    <td style="text-align: center;">{{ $org->start_year }} -
                                        {{ $org->is_active ? 'Aktif' : $org->end_year ?? '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        style="text-align: center; color: #94a3b8; font-style: italic;">Tidak ada data
                                        organisasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
                <!-- Kolom Prestasi -->
                <td style="width: 50%; vertical-align: top; padding-left: 6px;">
                    <div style="font-weight: bold; font-size: 8.5pt; color: #0f172a; margin-bottom: 3px;">B. Prestasi &
                        Penghargaan</div>
                    <table class="data-table" style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th style="width: 8%; text-align: center;">No</th>
                                <th style="width: 52%;">Nama Penghargaan / Lomba</th>
                                <th style="width: 25%;">Tingkat</th>
                                <th style="width: 15%; text-align: center;">Tahun</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($profile->achievements ?? [] as $aIdx => $ach)
                                <tr>
                                    <td style="text-align: center;">{{ $aIdx + 1 }}</td>
                                    <td style="font-weight: bold;">{{ $ach->name }}</td>
                                    <td>{{ $ach->scale ?? 'Kompetisi' }}</td>
                                    <td style="text-align: center;">{{ $ach->year }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        style="text-align: center; color: #94a3b8; font-style: italic;">Tidak ada data
                                        prestasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- 6. KEAHLIAN, BAHASA, SERTIFIKASI & PELATIHAN -->
    <div class="section-title">VI. Kemampuan Bahasa, Komputer & Sertifikasi</div>

    <!-- KEMAMPUAN BERBAHASA -->
    <div style="font-weight: bold; font-size: 9.5px; color: #1e293b; margin-top: 6px; margin-bottom: 3px;">Kemampuan
        Berbahasa</div>
    <table class="data-table" style="margin-top: 2px; margin-bottom: 4px; border: 1px solid #cbd5e1;">
        <thead>
            <tr>
                <th rowspan="2"
                    style="vertical-align: middle; width: 28%; text-align: left; border-right: 1px solid #cbd5e1;">
                    Bahasa Asing</th>
                <th colspan="3"
                    style="text-align: center; border-right: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                    Lisan</th>
                <th colspan="3"
                    style="text-align: center; border-right: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                    Tulisan</th>
                <th colspan="3" style="text-align: center; border-bottom: 1px solid #cbd5e1;">Percakapan</th>
            </tr>
            <tr>
                <th style="width: 8%; text-align: center; font-size: 8px; border-right: 1px solid #cbd5e1;">Baik</th>
                <th style="width: 8%; text-align: center; font-size: 8px; border-right: 1px solid #cbd5e1;">Cukup</th>
                <th style="width: 8%; text-align: center; font-size: 8px; border-right: 1px solid #cbd5e1;">Kurang</th>
                <th style="width: 8%; text-align: center; font-size: 8px; border-right: 1px solid #cbd5e1;">Baik</th>
                <th style="width: 8%; text-align: center; font-size: 8px; border-right: 1px solid #cbd5e1;">Cukup</th>
                <th style="width: 8%; text-align: center; font-size: 8px; border-right: 1px solid #cbd5e1;">Kurang</th>
                <th style="width: 8%; text-align: center; font-size: 8px; border-right: 1px solid #cbd5e1;">Baik</th>
                <th style="width: 8%; text-align: center; font-size: 8px; border-right: 1px solid #cbd5e1;">Cukup</th>
                <th style="width: 8%; text-align: center; font-size: 8px;">Kurang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($profile->languages as $lang)
                <tr>
                    <td style="font-weight: bold; border-right: 1px solid #cbd5e1;">{{ $lang->name }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($lang->oral) === 'baik' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($lang->oral) === 'cukup' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($lang->oral) === 'kurang' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($lang->written) === 'baik' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($lang->written) === 'cukup' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($lang->written) === 'kurang' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($lang->conversation) === 'baik' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($lang->conversation) === 'cukup' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ strtolower($lang->conversation) === 'kurang' ? 'X' : '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #94a3b8; font-style: italic;">Tidak ada data
                        kemampuan bahasa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- KEMAMPUAN APLIKASI KOMPUTER -->
    <div style="font-weight: bold; font-size: 9.5px; color: #1e293b; margin-top: 4px; margin-bottom: 3px;">Kemampuan
        Aplikasi Komputer</div>
    <table class="data-table" style="margin-top: 2px; margin-bottom: 8px; border: 1px solid #cbd5e1;">
        <thead>
            <tr>
                <th style="width: 32%; border-right: 1px solid #cbd5e1;">Aplikasi Komputer</th>
                <th style="width: 12%; text-align: center; border-right: 1px solid #cbd5e1;">Sangat Baik</th>
                <th style="width: 12%; text-align: center; border-right: 1px solid #cbd5e1;">Baik</th>
                <th style="width: 12%; text-align: center; border-right: 1px solid #cbd5e1;">Cukup</th>
                <th style="width: 12%; text-align: center; border-right: 1px solid #cbd5e1;">Kurang</th>
                <th style="width: 20%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $computerSkills = $profile->skills ? $profile->skills->where('category', 'computer') : collect();
                $generalSkills = $profile->skills
                    ? $profile->skills->where(fn($s) => $s->category === 'general' || empty($s->category))
                    : collect();
            @endphp
            @forelse($computerSkills as $cSkill)
                <tr>
                    <td style="font-weight: bold; border-right: 1px solid #cbd5e1;">{{ $cSkill->name }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($cSkill->proficiency) === 'sangat_baik' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($cSkill->proficiency) === 'baik' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($cSkill->proficiency) === 'cukup' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">
                        {{ strtolower($cSkill->proficiency) === 'kurang' ? 'X' : '' }}</td>
                    <td>{{ $cSkill->description ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8; font-style: italic;">Tidak ada data
                        kemampuan aplikasi komputer.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SERTIFIKASI KEAHLIAN & PELATIHAN / KURSUS -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; margin-bottom: 8px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                <div style="font-weight: bold; font-size: 8.5pt; color: #0f172a; margin-bottom: 3px;">Sertifikasi
                    Keahlian</div>
                <table class="data-table" style="margin-top: 0; border: 1px solid #cbd5e1;">
                    <thead>
                        <tr>
                            <th style="width: 12%; text-align: center; border-right: 1px solid #cbd5e1;">No</th>
                            <th style="width: 88%;">Nama Sertifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profile->certifications ?? [] as $cIdx => $cert)
                            <tr>
                                <td style="text-align: center; border-right: 1px solid #cbd5e1;">{{ $cIdx + 1 }}
                                </td>
                                <td style="font-weight: bold;">{{ $cert->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: #94a3b8; font-style: italic;">
                                    Tidak ada data sertifikasi keahlian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 6px;">
                <div style="font-weight: bold; font-size: 8.5pt; color: #0f172a; margin-bottom: 3px;">Pelatihan /
                    Kursus</div>
                <table class="data-table" style="margin-top: 0; border: 1px solid #cbd5e1;">
                    <thead>
                        <tr>
                            <th style="width: 12%; text-align: center; border-right: 1px solid #cbd5e1;">No</th>
                            <th style="width: 88%;">Nama Pelatihan / Kursus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profile->trainings ?? [] as $tIdx => $tr)
                            <tr>
                                <td style="text-align: center; border-right: 1px solid #cbd5e1;">{{ $tIdx + 1 }}
                                </td>
                                <td style="font-weight: bold;">{{ $tr->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: #94a3b8; font-style: italic;">
                                    Tidak ada data pelatihan / kursus.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- 7. PREFERENSI KERJA & PORTOFOLIO DIGITAL -->
    <div class="avoid-break">
        <div class="section-title">VII. Preferensi Kerja & Portofolio Digital</div>
        @php
            $pref = $profile->jobPreference;
        @endphp

        <!-- Tabel Preferensi Kerja -->
        <div style="font-weight: bold; font-size: 8.5pt; color: #0f172a; margin-top: 4px; margin-bottom: 3px;">A.
            Preferensi & Kesiapan Kerja</div>
        <table class="data-table" style="margin-top: 2px; margin-bottom: 8px;">
            <thead>
                <tr>
                    <th style="width: 28%;">Parameter</th>
                    <th style="width: 72%;">Keterangan / Preferensi Kandidat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold; background-color: #f8fafc;">Ekspektasi Gaji</td>
                    <td style="font-weight: bold; color: #059669;">
                        @if ($pref && $pref->expected_salary)
                            {{ $formatSalary($pref->expected_salary) }}
                        @elseif (!empty($profile->other_answers['expected_salary']) && $formatSalary($profile->other_answers['expected_salary']) !== '-')
                            {{ $formatSalary($profile->other_answers['expected_salary']) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background-color: #f8fafc;">Ketersediaan Mulai Kerja</td>
                    <td>{{ $pref->notice_period ?? 'Dapat Segera Bekerja' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background-color: #f8fafc;">Kesiapan Relokasi Kota</td>
                    <td>{{ $pref ? ($pref->is_willing_to_relocate ? 'Ya, Bersedia Ditempatkan / Relokasi' : 'Tidak Bersedia') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background-color: #f8fafc;">Status Pencarian Kerja</td>
                    <td style="font-weight: bold;">{{ $pref->job_search_status ?? 'Aktif Mencari Kerja' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background-color: #f8fafc;">Bidang Pekerjaan yang Diminati</td>
                    <td>
                        @if ($pref)
                            {{ implode(', ', array_filter([$pref->interested_field_1, $pref->interested_field_2, $pref->interested_field_3])) ?: '-' }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Tabel Portofolio Digital & Media Sosial -->
        <div style="font-weight: bold; font-size: 8.5pt; color: #0f172a; margin-top: 6px; margin-bottom: 3px;">B.
            Portofolio Digital & Media Sosial</div>
        <table class="data-table" style="margin-top: 2px; margin-bottom: 8px;">
            <thead>
                <tr>
                    <th style="width: 6%; text-align: center;">No</th>
                    <th style="width: 28%;">Platform / Jejaring Sosial</th>
                    <th style="width: 66%;">Tautan Akun / URL Portofolio</th>
                </tr>
            </thead>
            <tbody>
                @forelse($profile->socialMedias ?? [] as $smIdx => $sm)
                    <tr>
                        <td style="text-align: center;">{{ $smIdx + 1 }}</td>
                        <td style="font-weight: bold;">{{ $sm->platform_name }}</td>
                        <td style="word-break: break-all; color: #2563eb;">{{ $sm->url }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #94a3b8; font-style: italic;">Tidak ada
                            tautan portofolio atau media sosial dicantumkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 8. KONSEP DIRI -->
    <div class="avoid-break" style="margin-top: 6px;">
        <div class="section-title">VIII. Konsep Diri</div>
        @php
            $selfConcept = $profile->self_concept ?? [];
            $strengths = $selfConcept['strengths'] ?? [];
            $weaknesses = $selfConcept['weaknesses'] ?? [];
            $maxRows = max(3, count($strengths), count($weaknesses));
        @endphp
        <table class="data-table" style="margin-top: 4px; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 50%; text-align: center; font-size: 8.5pt; font-weight: bold; background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 6px;">
                        Kelebihan
                    </th>
                    <th style="width: 50%; text-align: center; font-size: 8.5pt; font-weight: bold; background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 6px;">
                        Kekurangan
                    </th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < $maxRows; $i++)
                    @php
                        $strengthVal = $strengths[$i] ?? '';
                        $weaknessVal = $weaknesses[$i] ?? '';
                    @endphp
                    <tr>
                        <td style="height: 22px; vertical-align: top; padding: 5px 8px; border: 1px solid #cbd5e1;">
                            @if (!empty(trim((string)$strengthVal)))
                                <span style="font-weight: bold; color: #0f172a;">{{ $i + 1 }}.</span> {{ $strengthVal }}
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                        <td style="height: 22px; vertical-align: top; padding: 5px 8px; border: 1px solid #cbd5e1;">
                            @if (!empty(trim((string)$weaknessVal)))
                                <span style="font-weight: bold; color: #0f172a;">{{ $i + 1 }}.</span> {{ $weaknessVal }}
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- 9. KUESIONER KESEHATAN -->
    <div class="avoid-break">
        <div class="section-title">IX. Kuesioner Kesehatan</div>
        @php
            $healthAnswers = $profile->health_answers ?? [];
            $healthPdfList = [
                'smoking' => ['q' => 'Apakah saudara merokok?', 'if_yes' => 'Jika IYA, berapa batang perhari?'],
                'alcohol' => ['q' => 'Apakah saudara minum alcohol?', 'if_yes' => 'Jika IYA, seberapa sering?'],
                'hospitalized' => [
                    'q' => 'Apakah anda pernah masuk Rumah Sakit (INAP)?',
                    'if_yes' => 'Jika IYA, Kapan? Mengapa bisa INAP?',
                ],
                'allergy' => [
                    'q' => 'Apakah anda memiliki riwayat alergi?',
                    'if_yes' => 'Jika IYA, sebut dan Jelaskan!',
                ],
                'ongoing_treatment' => [
                    'q' => 'Apakah anda sedang melakukan pengobatan?',
                    'if_yes' => 'Jika IYA, Jelaskan!',
                ],
                'infectious_disease' => [
                    'q' => 'Apakah anda memiliki penyakit menular?',
                    'if_yes' => 'Jika IYA, Sebut dan Jelaskan!',
                ],
                'disease_history' => [
                    'q' => 'Apakah anda memiliki riwayat penyakit?',
                    'if_yes' => 'Jika IYA, Sebut dan Jelaskan!',
                ],
            ];
        @endphp
        <table class="data-table" style="margin-top: 2px; margin-bottom: 8px;">
            <thead>
                <tr>
                    <th style="width: 35%;">Pertanyaan</th>
                    <th style="width: 7%; text-align: center;">IYA</th>
                    <th style="width: 7%; text-align: center;">TIDAK</th>
                    <th style="width: 25%;">Keterangan Jika IYA</th>
                    <th style="width: 26%;">Jawaban / Penjelasan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($healthPdfList as $k => $item)
                    @php
                        $isYes = !empty($healthAnswers[$k]['is_yes']);
                        $detail = $healthAnswers[$k]['detail'] ?? '';
                    @endphp
                    <tr>
                        <td style="font-weight: bold;">{{ $item['q'] }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $isYes ? 'X' : '' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ !$isYes ? 'X' : '' }}</td>
                        <td style="font-size: 8.5px; color: #64748b;">{{ $item['if_yes'] }}</td>
                        <td>{{ $isYes ? ($detail ?: 'Iya (Tanpa rincian)') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 10. INFORMASI LAIN -->
    <div class="avoid-break">
        <div class="section-title">X. Informasi Tambahan & Kesiapan Kerja</div>
        @php
            $otherAns = $profile->other_answers ?? [];
            $otherList = [
                1 => [
                    'q' => 'Bagaimana cara anda menggunakan waktu luang/libur?',
                    'a' => $otherAns['free_time_activity'] ?? '-',
                ],
                2 => [
                    'q' => 'Apakah anda pernah terlibat kasus tindakan kejahatan?',
                    'a' => !empty($otherAns['crime_involvement']['is_yes'])
                        ? 'IYA (' . ($otherAns['crime_involvement']['detail'] ?? 'Ada riwayat') . ')'
                        : 'TIDAK',
                ],
                3 => [
                    'q' => 'Apakah anda pernah masuk sel tahanan?',
                    'a' => !empty($otherAns['custody']['is_yes'])
                        ? 'IYA (' . ($otherAns['custody']['detail'] ?? 'Ada riwayat') . ')'
                        : 'TIDAK',
                ],
                4 => [
                    'q' => 'Apa alasan anda melamar di perusahaan ini?',
                    'a' => $otherAns['reason_applying_company'] ?? '-',
                ],
                5 => [
                    'q' => 'Apa alasan anda melamar pada posisi ini?',
                    'a' => $otherAns['reason_applying_position'] ?? '-',
                ],
                6 => ['q' => 'Darimana info lowongan yang anda dapat?', 'a' => $otherAns['vacancy_source'] ?? '-'],
                7 => [
                    'q' => 'Apakah anda sedang melamar pada perusahaan lain?',
                    'a' => !empty($otherAns['other_applications']['is_yes'])
                        ? 'IYA (' . ($otherAns['other_applications']['detail'] ?? 'Ada lamaran lain') . ')'
                        : 'TIDAK',
                ],
                8 => [
                    'q' => 'Apakah anda siap melakukan perjalanan dinas?',
                    'a' => strtoupper($otherAns['business_trip_ready'] ?? 'SIAP'),
                ],
                9 => [
                    'q' => 'Apakah anda siap dimutasi atau dipindahkan bagian atau posisi?',
                    'a' => strtoupper($otherAns['mutation_position_ready'] ?? 'SIAP'),
                ],
                10 => [
                    'q' => 'Apakah anda siap dimutasi ke Group Perusahaan?',
                    'a' => strtoupper($otherAns['mutation_group_ready'] ?? 'SIAP'),
                ],
                11 => [
                    'q' => 'Apakah anda siap ditempatkan di Kota lain?',
                    'a' => strtoupper($otherAns['relocation_city_ready'] ?? 'SIAP'),
                ],
                12 => [
                    'q' => 'Memiliki keluarga/kerabat/teman di Perusahaan ini?',
                    'a' => !empty($otherAns['internal_relations']['is_yes'])
                        ? 'IYA (' . ($otherAns['internal_relations']['detail'] ?? '-') . ')'
                        : 'TIDAK',
                ],
                13 => [
                    'q' => 'Apakah anda pernah melamar ke Perusahaan ini sebelumnya?',
                    'a' => !empty($otherAns['previously_applied']['is_yes'])
                        ? 'IYA (' . ($otherAns['previously_applied']['detail'] ?? '-') . ')'
                        : 'TIDAK',
                ],
                14 => [
                    'q' => 'Jika diterima/LOLOS, kapan anda siap kerja di perusahaan ini?',
                    'a' => $otherAns['ready_to_work'] ?? 'Dapat Segera Bekerja',
                ],
                15 => [
                    'q' => 'Berapa gaji yang anda inginkan? Jelaskan!',
                    'a' => !empty($otherAns['expected_salary']) && $formatSalary($otherAns['expected_salary']) !== '-'
                        ? $formatSalary($otherAns['expected_salary'])
                        : ($pref && $pref->expected_salary ? $formatSalary($pref->expected_salary) : '-'),
                ],
                16 => [
                    'q' => 'Apa Fasilitas/ Tunjangan yang anda inginkan?',
                    'a' => $otherAns['expected_benefits'] ?? '-',
                ],
            ];
        @endphp
        <table class="data-table" style="margin-top: 2px; margin-bottom: 8px;">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 50%;">Pertanyaan & Aspek Kesiapan Kerja</th>
                    <th style="width: 45%;">Keterangan / Jawaban Kandidat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($otherList as $num => $item)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $num }}</td>
                        <td>{{ $item['q'] }}</td>
                        <td>{{ $item['a'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- PERNYATAAN KEBENARAN DATA & TANDA TANGAN DIGITAL PELAMAR -->
    <div
        style="page-break-inside: avoid; margin-top: 8px; border: 1.5px solid #0f172a; border-radius: 5px; padding: 7px 10px; background-color: #f8fafc;">
        <div
            style="font-size: 9.5px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #cbd5e1; padding-bottom: 3px; margin-bottom: 5px;">
            Pernyataan Kebenaran Data & Persetujuan Digital Pelamar
        </div>

        <p
            style="font-size: 9px; color: #334155; line-height: 1.45; margin: 0 0 6px 0; text-align: justify; font-style: italic;">
            "Dengan ini saya menyatakan bahwa seluruh data profil dan riwayat yang saya lampirkan dalam lamaran ini
            (Data Pribadi, Keluarga, Pendidikan, Pengalaman Kerja, hingga Data Tambahan) adalah <strong>BENAR</strong>.
            Saya menyadari bahwa ketidakjujuran dapat mengakibatkan Pembatalan Lamaran atau Pemutusan Hubungan Kerja
            (PHK) dari Pihak Perusahaan."
        </p>

        <div
            style="margin-bottom: 6px; padding: 4px 8px; background-color: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    {{-- <td style="width: 18px; vertical-align: middle;">
                        <span
                            style="display: inline-block; width: 13px; height: 13px; border: 1.5px solid #0f172a; background-color: #0f172a; color: #ffffff; text-align: center; line-height: 12px; font-size: 9px; font-weight: bold; border-radius: 3px;">&#10003;</span>
                    </td> --}}
                    <td style="font-size: 9px; font-weight: bold; color: #0f172a; vertical-align: middle;">
                        Saya menyetujui pernyataan di atas dan menjamin kebenaran seluruh data saya.
                    </td>
                </tr>
            </table>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-top: 4px;">
            <tr>
                <td style="width: 58%; vertical-align: top; font-size: 8.5px; color: #64748b; line-height: 1.45;">
                    <strong style="color: #475569;">Ketentuan Hukum & Validasi Sistem:</strong><br>
                    Dokumen ini disetujui secara digital oleh pelamar melalui akun resmi sistem E-Recruitment.<br>
                    Berdasarkan UU ITE yang berlaku, persetujuan elektronik ini sah, mengikat, dan dapat dijadikan bukti
                    otentik dalam proses verifikasi dan seleksi kerja.
                </td>
                <td style="width: 42%; vertical-align: top; text-align: right;">
                    <div style="font-size: 8.5px; color: #64748b;">Ditandatangani secara digital oleh:</div>
                    <div style="font-size: 11px; font-weight: bold; color: #0f172a; margin-top: 2px;">
                        {{ strtoupper($profile->full_name ?? $candidate->name) }}
                    </div>
                    <div style="font-size: 8.5px; color: #475569; margin-top: 1px;">
                        NIK: {{ $profile->nik ?? ($candidate->nik ?? '-') }}
                    </div>
                    <div
                        style="display: inline-block; margin-top: 4px; padding: 2px 7px; background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 4px; font-size: 8px; font-weight: bold; color: #059669;">
                        TERVERIFIKASI SISTEM DIGITAL
                    </div>
                    <div style="font-size: 8px; color: #64748b; margin-top: 3px;">
                        Waktu:
                        {{ isset($application) && $application->applied_at ? \Carbon\Carbon::parse($application->applied_at)->translatedFormat('d F Y, H:i') . ' WIB' : now()->translatedFormat('d F Y, H:i') . ' WIB' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen ini dibuat otomatis melalui Sistem E-Recruitment resmi - Dokumen Rahasia (Confidential) untuk keperluan
        seleksi kerja.
    </div>

</body>

</html>
