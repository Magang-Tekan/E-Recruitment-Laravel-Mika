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
@endphp
Yth. Sdr/i. {!! $candidateName !!},

Selamat bergabung di keluarga besar {!! $companyName !!}. Akun Anda telah resmi diaktifkan sebagai {!! $typeLabel !!} dan kini dapat mengakses Portal Karyawan.

RINGKASAN KEPEGAWAIAN:
- Perusahaan: {!! $companyName !!}
@if ($department)
- Divisi / Departemen: {!! $department->name !!}
@endif
- Posisi / Jabatan: {!! $positionTitle !!}
- Status Hubungan Kerja: {!! $typeLabel !!}
- Tanggal Mulai Bekerja: {!! $startDateText !!}
@if ($employeeProfile->employee_type !== 'permanent')
- Bekerja Sampai: {!! $endDateText !!}
@endif

AKSES PORTAL KARYAWAN:
Silakan masuk menggunakan email dan kata sandi akun Anda sebelumnya melalui tautan berikut:
{!! route('employee.dashboard') !!}

Hormat kami,
Tim Human Resources
{!! $companyName !!}
