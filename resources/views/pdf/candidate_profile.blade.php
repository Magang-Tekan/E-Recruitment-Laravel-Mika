<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Profil Pelamar' }}</title>
    <style>
        @page {
            margin: 28px 32px 32px 32px;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            line-height: 1.45;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.3px;
            margin: 0;
        }
        .subtitle {
            font-size: 11px;
            color: #475569;
            margin-top: 3px;
        }
        .badge-app {
            background-color: #0f172a;
            color: #ffffff;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: bold;
            display: inline-block;
        }
        .section-title {
            font-size: 11.5px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 3px;
            margin-top: 14px;
            margin-bottom: 8px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .info-label {
            width: 22%;
            color: #64748b;
            font-size: 9.5px;
        }
        .info-sep {
            width: 2%;
            color: #94a3b8;
        }
        .info-value {
            width: 26%;
            color: #0f172a;
            font-weight: 500;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
            padding: 5px 6px;
            border-bottom: 1px solid #cbd5e1;
        }
        .data-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9.5px;
            vertical-align: top;
        }
        .card-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 7px 10px;
            margin-bottom: 6px;
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
        .pill {
            display: inline-block;
            background-color: #e2e8f0;
            color: #1e293b;
            padding: 2px 7px;
            border-radius: 12px;
            font-size: 9px;
            margin-right: 4px;
            margin-bottom: 4px;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 8.5px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <table class="header-table">
        <tr>
            <td style="width: 70%; vertical-align: middle;">
                <h1 class="title">{{ $profile->full_name ?? $candidate->name }}</h1>
                <div class="subtitle">
                    {{ $profile->city ? $profile->city . ', ' . $profile->province : 'Indonesia' }} &bull;
                    {{ $profile->phone ?? '-' }} &bull;
                    {{ $candidate->email }}
                </div>
                @if($isApplicant && $application)
                    <div style="margin-top: 5px;">
                        <span class="badge-app">Pelamar Lowongan: {{ $application->job?->title ?? '-' }} ({{ $application->job?->company?->name ?? 'Perusahaan' }})</span>
                        <span style="font-size: 9px; color: #64748b; margin-left: 6px;">Status: <strong>{{ $application->status }}</strong></span>
                    </div>
                @endif
            </td>
            <td style="width: 30%; text-align: right; vertical-align: middle;">
                <div style="font-size: 9px; color: #64748b;">E-Recruitment System</div>
                <div style="font-size: 9.5px; font-weight: bold; color: #0f172a; margin-top: 2px;">CV & PROFIL LENGKAP</div>
                <div style="font-size: 8.5px; color: #94a3b8; margin-top: 2px;">Dicetak: {{ now()->translatedFormat('d M Y, H:i') }} WIB</div>
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
            <td class="info-label">Status Pernikahan</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->marital_status ? ucfirst($profile->marital_status) : '-' }}</td>
            <td class="info-label">Kode Pos</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->postal_code ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Alamat Lengkap</td>
            <td class="info-sep">:</td>
            <td class="info-value" colspan="4">
                {{ $profile->address ?? '-' }}{{ $profile->city ? ', ' . $profile->city : '' }}{{ $profile->province ? ', ' . $profile->province : '' }}
            </td>
        </tr>
    </table>

    @if(!empty($profile->about_me))
        <div style="background-color: #f8fafc; border-left: 3px solid #3b82f6; padding: 6px 10px; margin-bottom: 8px; font-size: 9.5px; color: #334155;">
            <strong>Tentang Saya:</strong> {{ $profile->about_me }}
        </div>
    @endif

    <!-- 2. DATA KELUARGA & KONTAK DARURAT -->
    <div class="section-title">II. Susunan Keluarga & Kontak Darurat</div>
    @php
        $family = $profile->family;
    @endphp
    <table class="info-table">
        <tr>
            <td class="info-label">Anak Ke-</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->child_sequence ? 'Ke-' . $profile->child_sequence : '-' }}</td>
            <td class="info-label">Dari Jumlah Bersaudara</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $profile->total_siblings ? $profile->total_siblings . ' Bersaudara' : '-' }}</td>
        </tr>
        @if($family)
            <tr>
                <td class="info-label">Nama Ayah</td>
                <td class="info-sep">:</td>
                <td class="info-value">{{ $family->father_name ?? '-' }} ({{ $family->father_occupation ?? 'Pekerjaan: -' }})</td>
                <td class="info-label">Kontak Ayah</td>
                <td class="info-sep">:</td>
                <td class="info-value">{{ $family->father_phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Nama Ibu</td>
                <td class="info-sep">:</td>
                <td class="info-value">{{ $family->mother_name ?? '-' }} ({{ $family->mother_occupation ?? 'Pekerjaan: -' }})</td>
                <td class="info-label">Kontak Ibu</td>
                <td class="info-sep">:</td>
                <td class="info-value">{{ $family->mother_phone ?? '-' }}</td>
            </tr>
            @if(!empty($family->spouse_name))
                <tr>
                    <td class="info-label">Suami / Istri</td>
                    <td class="info-sep">:</td>
                    <td class="info-value">{{ $family->spouse_name }} ({{ $family->spouse_occupation ?? '-' }})</td>
                    <td class="info-label">Kontak Pasangan</td>
                    <td class="info-sep">:</td>
                    <td class="info-value">{{ $family->spouse_phone ?? '-' }}</td>
                </tr>
            @endif
            @php
                $emergencies = $family->emergency_contacts ?? [];
                $sameHouse = $emergencies['same_house'] ?? [];
                $diffHouse = $emergencies['diff_house'] ?? [];
            @endphp
            @if(!empty($sameHouse) || !empty($diffHouse))
                <tr>
                    <td class="info-label" style="color: #dc2626; font-weight: bold; vertical-align: top;">Kontak Darurat</td>
                    <td class="info-sep" style="vertical-align: top;">:</td>
                    <td class="info-value" colspan="4">
                        @if(!empty($sameHouse))
                            <div style="margin-bottom: 3px;">
                                <strong style="font-size: 8pt; color: #166534;">[Satu Rumah]</strong>
                                @foreach($sameHouse as $idx => $ec)
                                    <span>{{ $ec['name'] ?? '-' }} ({{ $ec['relation'] ?? '-' }}) - Telp: {{ $ec['phone'] ?? '-' }}{{ !$loop->last ? ' | ' : '' }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if(!empty($diffHouse))
                            <div>
                                <strong style="font-size: 8pt; color: #1e40af;">[Tidak Satu Rumah]</strong>
                                @foreach($diffHouse as $idx => $ec)
                                    <span>{{ $ec['name'] ?? '-' }} ({{ $ec['relation'] ?? '-' }}) - Telp: {{ $ec['phone'] ?? '-' }}{{ !$loop->last ? ' | ' : '' }}</span>
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
            @elseif(!empty($family->emergency_contact_name) || !empty($family->emergency_contact_phone))
                <tr>
                    <td class="info-label" style="color: #dc2626; font-weight: bold;">Kontak Darurat</td>
                    <td class="info-sep">:</td>
                    <td class="info-value" colspan="4">
                        <strong>{{ $family->emergency_contact_name ?? '-' }}</strong> 
                        ({{ $family->emergency_contact_relation ?? 'Hubungan: -' }}) &bull; 
                        No. Telp: <strong>{{ $family->emergency_contact_phone ?? '-' }}</strong>
                    </td>
                </tr>
            @endif
        @else
            <tr>
                <td colspan="6" class="text-muted" style="font-style: italic; padding: 4px;">Data keluarga belum diisi.</td>
            </tr>
        @endif
    </table>

    <!-- 3. RIWAYAT PENDIDIKAN -->
    <div class="section-title">III. Riwayat Pendidikan Formal</div>
    @if($profile->educations && $profile->educations->count() > 0)
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
                @foreach($profile->educations as $edu)
                    <tr>
                        <td class="bold">{{ $edu->degree ?? '-' }}</td>
                        <td>{{ $edu->school_name ?? $edu->institution_name ?? '-' }}</td>
                        <td>
                            {{ $edu->major ?? '-' }}
                            @if($edu->study_program && $edu->study_program !== $edu->major)
                                <div style="font-size: 8.5px; color: #64748b;">Prodi: {{ $edu->study_program }}</div>
                            @endif
                        </td>
                        <td class="bold">{{ $edu->gpa ?? '-' }}</td>
                        <td style="text-align: right;">{{ $edu->start_year }} - {{ $edu->end_year ?? 'Sekarang' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted" style="font-style: italic; margin: 4px 0 10px 0;">Belum ada data pendidikan formal dicantumkan.</p>
    @endif

    <!-- 4. PENGALAMAN KERJA -->
    <div class="section-title">IV. Riwayat Pengalaman Kerja</div>
    @if($profile->workExperiences && $profile->workExperiences->count() > 0)
        @foreach($profile->workExperiences as $work)
            <div class="card-box">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 70%;">
                            <span class="bold" style="font-size: 10.5px;">{{ $work->position }}</span>
                            @if($work->initial_position)
                                <span class="text-muted" style="font-size: 9px;">(Awal: {{ $work->initial_position }})</span>
                            @endif
                            <span class="text-primary bold"> &bull; {{ $work->company_name }}</span>
                            @if($work->business_field)
                                <span class="text-muted" style="font-size: 9px;">[{{ $work->business_field }}]</span>
                            @endif
                            @if($work->employment_type)
                                <span class="text-muted" style="font-size: 9px;">({{ $work->employment_type }})</span>
                            @endif
                        </td>
                        <td style="width: 30%; text-align: right; color: #64748b; font-size: 9px;">
                            {{ $work->start_date ? \Carbon\Carbon::parse($work->start_date)->translatedFormat('M Y') : '-' }} s/d 
                            {{ $work->currently_working ? 'Sekarang' : ($work->end_date ? \Carbon\Carbon::parse($work->end_date)->translatedFormat('M Y') : '-') }}
                        </td>
                    </tr>
                    @if($work->supervisor_name || $work->company_address)
                        <tr>
                            <td colspan="2" style="font-size: 8.5px; color: #64748b; padding-top: 2px;">
                                @if($work->supervisor_name) Atasan: <strong>{{ $work->supervisor_name }}</strong> @endif
                                @if($work->supervisor_name && $work->company_address) &bull; @endif
                                @if($work->company_address) Alamat: {{ $work->company_address }} @endif
                            </td>
                        </tr>
                    @endif
                    @if($work->initial_salary || $work->final_salary || $work->benefits)
                        <tr>
                            <td colspan="2" style="font-size: 8.5px; color: #475569; padding-top: 2px;">
                                @if($work->final_salary) Gaji Akhir: <strong>Rp {{ number_format($work->final_salary, 0, ',', '.') }}</strong> @endif
                                @if($work->final_salary && $work->benefits) &bull; @endif
                                @if($work->benefits) Fasilitas: {{ $work->benefits }} @endif
                            </td>
                        </tr>
                    @endif
                    @if($work->description)
                        <tr>
                            <td colspan="2" style="padding-top: 4px; font-size: 9px; color: #334155; line-height: 1.4;">
                                {{ $work->description }}
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        @endforeach
    @else
        <p class="text-muted" style="font-style: italic; margin: 4px 0 10px 0;">Belum ada pengalaman kerja (Fresh Graduate).</p>
    @endif

    <!-- 5. PENGALAMAN ORGANISASI & PRESTASI -->
    <div class="section-title">V. Organisasi & Prestasi</div>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
        <tr>
            <!-- Kolom Organisasi -->
            <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                <div style="font-weight: bold; font-size: 9.5px; color: #475569; margin-bottom: 4px;">A. PENGALAMAN ORGANISASI</div>
                @if($profile->organizations && $profile->organizations->count() > 0)
                    @foreach($profile->organizations as $org)
                        <div style="padding: 4px 0; border-bottom: 1px dotted #e2e8f0;">
                            <div class="bold">{{ $org->name }}</div>
                            <div style="font-size: 9px; color: #64748b;">
                                Jabatan: <strong>{{ $org->position }}</strong> &bull; 
                                {{ $org->start_year }} - {{ $org->is_active ? 'Aktif' : ($org->end_year ?? '') }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-muted" style="font-style: italic; font-size: 9px;">Tidak ada data organisasi.</div>
                @endif
            </td>
            <!-- Kolom Prestasi -->
            <td style="width: 50%; vertical-align: top; padding-left: 8px;">
                <div style="font-weight: bold; font-size: 9.5px; color: #475569; margin-bottom: 4px;">B. PRESTASI & PENGHARGAAN</div>
                @if($profile->achievements && $profile->achievements->count() > 0)
                    @foreach($profile->achievements as $ach)
                        <div style="padding: 4px 0; border-bottom: 1px dotted #e2e8f0;">
                            <div class="bold">{{ $ach->name }}</div>
                            <div style="font-size: 9px; color: #64748b;">
                                Skala: <strong>{{ $ach->scale ?? 'Kompetisi' }}</strong> &bull; 
                                {{ $ach->month ? $ach->month . ' ' : '' }}{{ $ach->year }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-muted" style="font-style: italic; font-size: 9px;">Tidak ada data prestasi.</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- 6. KEAHLIAN, BAHASA, SERTIFIKASI & PELATIHAN -->
    <div class="section-title">VI. Kemampuan Bahasa, Komputer & Sertifikasi</div>
    
    <!-- KEMAMPUAN BERBAHASA -->
    <div style="font-weight: bold; font-size: 9.5px; color: #1e293b; margin-top: 6px; margin-bottom: 3px;">Kemampuan Berbahasa</div>
    <table class="data-table" style="margin-top: 2px; margin-bottom: 4px; border: 1px solid #cbd5e1;">
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle; width: 28%; text-align: left; border-right: 1px solid #cbd5e1;">Bahasa Asing</th>
                <th colspan="3" style="text-align: center; border-right: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">Lisan</th>
                <th colspan="3" style="text-align: center; border-right: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">Tulisan</th>
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
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($lang->oral) === 'baik' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($lang->oral) === 'cukup' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($lang->oral) === 'kurang' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($lang->written) === 'baik' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($lang->written) === 'cukup' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($lang->written) === 'kurang' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($lang->conversation) === 'baik' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($lang->conversation) === 'cukup' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ strtolower($lang->conversation) === 'kurang' ? 'X' : '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #94a3b8; font-style: italic;">Tidak ada data kemampuan bahasa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div style="font-size: 8px; color: #64748b; margin-top: -2px; margin-bottom: 8px;">*) Beri tanda 'X' pada kolom yang sesuai</div>

    <!-- KEMAMPUAN APLIKASI KOMPUTER -->
    <div style="font-weight: bold; font-size: 9.5px; color: #1e293b; margin-top: 4px; margin-bottom: 3px;">Kemampuan Aplikasi Komputer</div>
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
                $generalSkills = $profile->skills ? $profile->skills->where(fn($s) => $s->category === 'general' || empty($s->category)) : collect();
            @endphp
            @forelse($computerSkills as $cSkill)
                <tr>
                    <td style="font-weight: bold; border-right: 1px solid #cbd5e1;">{{ $cSkill->name }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($cSkill->proficiency) === 'sangat_baik' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($cSkill->proficiency) === 'baik' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($cSkill->proficiency) === 'cukup' ? 'X' : '' }}</td>
                    <td style="text-align: center; font-weight: bold; border-right: 1px solid #cbd5e1;">{{ strtolower($cSkill->proficiency) === 'kurang' ? 'X' : '' }}</td>
                    <td>{{ $cSkill->description ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8; font-style: italic;">Tidak ada data kemampuan aplikasi komputer.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- KEAHLIAN LAIN, SERTIFIKASI & PELATIHAN -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                <div style="font-weight: bold; font-size: 9.5px; color: #475569; margin-bottom: 4px;">Keahlian Umum (Skills):</div>
                <div>
                    @if($generalSkills->count() > 0)
                        @foreach($generalSkills as $skill)
                            <span class="pill">{{ $skill->name ?? $skill->skill_name }}</span>
                        @endforeach
                    @else
                        <span class="text-muted" style="font-style: italic;">-</span>
                    @endif
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 8px;">
                <div style="font-weight: bold; font-size: 9.5px; color: #475569; margin-bottom: 4px;">Sertifikasi Keahlian:</div>
                @if($profile->certifications && $profile->certifications->count() > 0)
                    @foreach($profile->certifications as $cert)
                        <div style="font-size: 9px; padding: 2px 0;">&bull; <strong>{{ $cert->name }}</strong></div>
                    @endforeach
                @else
                    <div class="text-muted" style="font-style: italic; font-size: 9px;">-</div>
                @endif

                <div style="font-weight: bold; font-size: 9.5px; color: #475569; margin-top: 6px; margin-bottom: 4px;">Pelatihan / Kursus:</div>
                @if($profile->trainings && $profile->trainings->count() > 0)
                    @foreach($profile->trainings as $tr)
                        <div style="font-size: 9px; padding: 2px 0;">&bull; <strong>{{ $tr->name }}</strong></div>
                    @endforeach
                @else
                    <div class="text-muted" style="font-style: italic; font-size: 9px;">-</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- 7. PREFERENSI KERJA & SOCIAL MEDIA -->
    <div class="section-title">VII. Preferensi Kerja & Portofolio Digital</div>
    @php
        $pref = $profile->jobPreference;
    @endphp
    <table class="info-table">
        <tr>
            <td class="info-label">Ekspektasi Gaji</td>
            <td class="info-sep">:</td>
            <td class="info-value bold" style="color: #059669;">
                {{ $pref && $pref->expected_salary ? 'Rp ' . number_format($pref->expected_salary, 0, ',', '.') : '-' }}
            </td>
            <td class="info-label">Ketersediaan Mulai Kerja</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $pref->notice_period ?? 'Dapat Segera Bekerja' }}</td>
        </tr>
        <tr>
            <td class="info-label">Bersedia Relokasi</td>
            <td class="info-sep">:</td>
            <td class="info-value">{{ $pref ? ($pref->is_willing_to_relocate ? 'Ya, Bersedia' : 'Tidak') : '-' }}</td>
            <td class="info-label">Status Pencarian Kerja</td>
            <td class="info-sep">:</td>
            <td class="info-value font-bold">{{ $pref->job_search_status ?? 'Aktif' }}</td>
        </tr>
        <tr>
            <td class="info-label">Bidang Minat</td>
            <td class="info-sep">:</td>
            <td class="info-value" colspan="4">
                @if($pref)
                    {{ implode(', ', array_filter([$pref->interested_field_1, $pref->interested_field_2, $pref->interested_field_3])) ?: '-' }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">Media Sosial / Portofolio</td>
            <td class="info-sep">:</td>
            <td class="info-value" colspan="4">
                @if($profile->socialMedias && $profile->socialMedias->count() > 0)
                    @foreach($profile->socialMedias as $sm)
                        <span style="margin-right: 12px;"><strong>{{ $sm->platform_name }}:</strong> {{ $sm->url }}</span>
                    @endforeach
                @else
                    <span class="text-muted" style="font-style: italic;">Tidak ada tautan media sosial dicantumkan.</span>
                @endif
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis melalui Sistem E-Recruitment resmi &bull; Dokumen Rahasia (Confidential) untuk keperluan seleksi kerja.
    </div>

</body>
</html>
