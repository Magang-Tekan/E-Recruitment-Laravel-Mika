@php
    $job = $application->job;
    $company = $job?->company;
    $applicantProfile = $application->applicantProfile;
    $candidateName = $applicantProfile?->full_name ?? ($applicantProfile?->user?->name ?? 'Pelamar');
    $jobTitle = $job?->title ?? 'Posisi Pekerjaan';
    $companyName = $company?->name ?? config('app.name', 'MIKA CAREER');

    $effectiveDeadlineDays = $application->effectiveTestDeadlineDays();
    $testDeadlineText = $application->test_deadline_at
        ? $application->test_deadline_at->copy()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y \p\u\k\u\l H:i') . ' WIB'
        : null;

    $statusDetails = match($status) {
        'Reviewed' => [
            'title' => 'Lolos Seleksi Berkas & Undangan Ujian Online',
            'desc'  => 'Berdasarkan hasil penelaahan kualifikasi terhadap berkas lamaran yang Anda ajukan, berkas Anda resmi dinyatakan Lolos Seleksi Berkas. Anda kami undang untuk melanjutkan ke tahapan Ujian Seleksi Online.'
                . ($testDeadlineText
                    ? "\n\nMohon segera mengerjakan ujian online dalam waktu {$effectiveDeadlineDays} hari kerja ({$effectiveDeadlineDays} x 24 jam kerja) agar proses seleksi Anda dapat lebih cepat kami tinjau. Batas akhir akses pengerjaan ujian adalah: {$testDeadlineText} (tetap dapat dikerjakan 24 jam termasuk akhir pekan sebelum batas waktu tersebut)."
                    : ''),
            'url'   => route('profile', ['tab' => 'riwayat']),
        ],
        'Shortlisted' => [
            'title' => 'Lolos Tahap Evaluasi (Shortlisted)',
            'desc'  => 'Hasil evaluasi dan penilaian tes Anda telah kami tinjau. Profil Anda dinyatakan memenuhi standar kompetensi dan resmi masuk ke dalam daftar Kandidat Terpilih (Shortlisted) untuk proses seleksi lebih lanjut.',
            'url'   => route('profile', ['tab' => 'riwayat']),
        ],
        'Interview' => [
            'title' => 'Undangan Tahap Wawancara Kerja',
            'desc'  => 'Kami mengundang Anda untuk mengikuti tahapan Wawancara Kerja (Interview) bersama tim rekruter. Detail jadwal dan tata cara pelaksanaan dapat Anda tinjau pada portal.',
            'url'   => route('profile', ['tab' => 'riwayat']),
        ],
        'Accepted' => [
            'title' => 'Pemberitahuan Penerimaan Kerja (Accepted)',
            'desc'  => 'Dengan senang hati kami sampaikan bahwa Anda resmi dinyatakan Diterima Bekerja (Accepted) untuk bergabung bersama ' . $companyName . '. Tim Human Resources akan segera menghubungi Anda untuk koordinasi penawaran resmi (offering) serta administrasi onboarding.',
            'url'   => route('profile', ['tab' => 'riwayat']),
        ],
        'Rejected' => [
            'title' => 'Informasi Perkembangan Seleksi Lamaran',
            'desc'  => 'Setelah melalui proses pertimbangan yang mendalam terhadap seluruh berkas kandidat yang masuk, saat ini kualifikasi Anda belum sesuai dengan kebutuhan posisi ini. Kami mengapresiasi waktu dan minat yang telah Anda berikan, serta tetap menyimpan profil Anda untuk peluang di masa mendatang.',
            'url'   => route('jobs.index'),
        ],
        default => [
            'title' => 'Pembaruan Perkembangan Lamaran Pekerjaan',
            'desc'  => 'Terdapat pembaruan informasi pada status lamaran pekerjaan Anda menjadi: ' . ($application->status_label ?? $status) . '. Silakan masuk ke akun Anda untuk meninjau detailnya.',
            'url'   => route('profile', ['tab' => 'riwayat']),
        ],
    };
@endphp
Yth. Sdr/i. {!! $candidateName !!},

Terima kasih atas minat dan partisipasi Anda dalam proses seleksi penerimaan karyawan di {!! $companyName !!} untuk posisi {!! $jobTitle !!}.

INFORMASI STATUS SELEKSI:
{!! $statusDetails['title'] !!}
--------------------------------------------------
{!! $statusDetails['desc'] !!}

@if (!empty(trim($notes ?? '')))
Catatan Tim Rekrutmen:
"{!! $notes !!}"

@endif
RINGKASAN LAMARAN:
- Posisi: {!! $jobTitle !!}
- Perusahaan: {!! $companyName !!}
@if ($job?->department)
- Departemen: {!! $job->department->name !!}
@endif
@if ($application->applied_at)
- Tanggal Lamaran: {!! \Carbon\Carbon::parse($application->applied_at)->translatedFormat('d F Y, H:i') !!} WIB
@endif
@if ($status === 'Reviewed' && $testDeadlineText)
- Batas Pengerjaan Tes: {!! $testDeadlineText !!}
@endif

TINDAK LANJUT / DETAIL LENGKAP:
Silakan buka tautan berikut melalui peramban web Anda:
{!! $statusDetails['url'] !!}

Hormat kami,
Tim Rekrutmen & Pengembangan Talenta
{!! $companyName !!}

==================================================
Pemberitahuan:
Seluruh proses seleksi dan penerimaan karyawan di {!! $companyName !!} sama sekali tidak dipungut biaya apapun (Gratis). Harap waspada terhadap segala bentuk penipuan yang mengatasnamakan perusahaan.

Email resmi ini dikirimkan secara otomatis melalui Sistem E-Recruitment {!! $companyName !!}.
(c) {!! date('Y') !!} {!! $companyName !!}. Hak cipta dilindungi undang-undang.
