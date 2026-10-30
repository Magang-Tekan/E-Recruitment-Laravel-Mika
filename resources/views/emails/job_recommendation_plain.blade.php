Halo {!! $user->name !!},

Berdasarkan kriteria Minat Kerja yang Anda daftarkan di portal E-Rekrutmen, berikut adalah rekomendasi lowongan pekerjaan terbaru yang sesuai dengan profil Anda:

@foreach ($matchedJobs as $job)
--------------------------------------------------
* {!! $job->title !!}
Perusahaan : {!! $job->company ? $job->company->name : 'Perusahaan' !!}
Lokasi     : {!! $job->location ?: 'Indonesia' !!}
@if ($job->department)
Departemen : {!! $job->department->name !!}
@endif
@if ($job->salary_min || $job->salary_max)
Estimasi Gaji: Rp {{ number_format($job->salary_min ?: 0, 0, ',', '.') }} - Rp {{ number_format($job->salary_max ?: 0, 0, ',', '.') }}
@endif
Tautan Lowongan: {{ route('jobs.show', $job->id) }}
@endforeach

--------------------------------------------------
PENGATURAN NOTIFIKASI / UNSUBSCRIBE:
Anda menerima email ini karena mengaktifkan notifikasi rekomendasi lowongan (Periode: {!! $period !!}).
Untuk mengubah preferensi minat atau menghentikan notifikasi ini, silakan kunjungi:
{{ route('profile', ['tab' => 'pengalaman']) }}

Hormat kami,
Tim E-Recruitment MIKA CAREER
(c) {!! date('Y') !!} MIKA CAREER. Hak cipta dilindungi undang-undang.
