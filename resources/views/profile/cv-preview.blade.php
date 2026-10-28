<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Curriculum Vitae - {{ $profile?->full_name ?? $user->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Dark Mode Init Sync -->
    <script>
        (function () {
            var mikaTheme = localStorage.getItem('mika-theme');
            var html = document.documentElement;
            if (mikaTheme === 'light') {
                html.classList.add('light-mode');
                html.classList.remove('dark');
            } else {
                html.classList.remove('light-mode');
                html.classList.add('dark');
            }
        })();
    </script>
    
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .cv-container { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; position: relative !important; background: white !important; }
            .cv-powered-by {
                display: block !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-[#070B14] text-slate-800 dark:text-slate-200 min-h-screen font-sans antialiased py-8 px-4 sm:px-6">

    <!-- Top Action Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('profile') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white dark:bg-[#0D1527] text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-50 dark:hover:bg-[#14203A] transition shadow-xs">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Profil
        </a>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-slate-950 text-xs font-bold rounded-xl shadow-sm hover:shadow transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Main CV Document Container -->
    <main class="max-w-4xl mx-auto bg-white dark:bg-[#0D1527] rounded-2xl shadow-xl border border-slate-200/80 dark:border-[#1D2E54] p-8 sm:p-12 cv-container relative overflow-hidden">
        
        <div class="relative z-10">
        
        <!-- Header Section -->
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b-2 border-emerald-500 dark:border-[#93F514] pb-6 mb-8 gap-6">
            <div class="space-y-1">
                <!-- Powered By Tag Directly Above Name -->
                <p class="cv-powered-by text-xs font-semibold text-emerald-600 dark:text-[#93F514] tracking-wide flex items-center gap-1 pb-1">
                    <span>Powered by</span> <span class="font-bold">Sistem Rekrutmen MIKA</span>
                </p>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ $profile?->full_name ?? $user->name }}
                </h1>
                <p class="text-xs text-emerald-600 dark:text-[#93F514] font-bold tracking-wider uppercase">
                    Curriculum Vitae Pelamar
                </p>
                <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-600 dark:text-[#93A5C9] pt-1">
                    @if($user->email)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $user->email }}
                        </span>
                    @endif
                    @if($profile?->phone)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $profile->phone }}
                        </span>
                    @endif
                    @if($profile?->city || $profile?->province)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ implode(', ', array_filter([$profile->city, $profile->province])) }}
                        </span>
                    @endif
                </div>
            </div>

            @php
                $cvPhoto = $profile?->photo_url;
            @endphp
            @if($cvPhoto)
                <img src="{{ $cvPhoto }}" alt="Foto Profile" referrerpolicy="no-referrer" class="w-24 h-28 object-cover rounded-xl border border-slate-200 dark:border-[#1D2E54] shadow-sm flex-shrink-0" onerror="this.style.display='none'">
            @endif
        </header>

        <!-- Profile / Summary Section -->
        @if($profile?->about_me)
            <section class="mb-8">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] border-b border-slate-200/80 dark:border-[#1D2E54] pb-1 mb-3">
                    Tentang Saya
                </h2>
                <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                    {{ $profile->about_me }}
                </p>
            </section>
        @endif

        <!-- Data Pribadi Details -->
        <section class="mb-8">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] border-b border-slate-200/80 dark:border-[#1D2E54] pb-1 mb-3">
                Informasi Pribadi
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2.5 gap-x-6 text-sm">
                <div><span class="text-slate-500 dark:text-[#93A5C9]">NIK:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->nik ?? '-' }}</span></div>
                <div><span class="text-slate-500 dark:text-[#93A5C9]">NPWP:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->npwp ?? '-' }}</span></div>
                <div><span class="text-slate-500 dark:text-[#93A5C9]">Jenis Kelamin:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ ucfirst($profile?->gender ?? '-') }}</span></div>
                <div><span class="text-slate-500 dark:text-[#93A5C9]">Tempat, Tgl Lahir:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->birth_place ?? '-' }}, {{ $profile?->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->isoFormat('D MMMM YYYY') : '-' }}</span></div>
                <div><span class="text-slate-500 dark:text-[#93A5C9]">Agama:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->religion ?? '-' }}</span></div>
                <div><span class="text-slate-500 dark:text-[#93A5C9]">Golongan Darah:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->blood_type ?? '-' }}</span></div>
                <div><span class="text-slate-500 dark:text-[#93A5C9]">Suku:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->ethnicity ?? '-' }}</span></div>
                <div><span class="text-slate-500 dark:text-[#93A5C9]">Kebangsaan:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->nationality ?? '-' }}</span></div>
                <div>
                    <span class="text-slate-500 dark:text-[#93A5C9]">Status Pernikahan:</span> 
                    <span class="font-semibold text-slate-900 dark:text-white capitalize">
                        {{ $profile?->marital_status ? (ucfirst($profile->marital_status) . ($profile->marriage_date ? ' (' . \Carbon\Carbon::parse($profile->marriage_date)->isoFormat('D MMMM YYYY') . ')' : '')) : '-' }}
                    </span>
                </div>
                <div><span class="text-slate-500 dark:text-[#93A5C9]">Transportasi:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->daily_transportation ?? '-' }}</span></div>
                <div><span class="text-slate-500 dark:text-[#93A5C9]">Jarak ke Perusahaan:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->work_distance_km ? ($profile->work_distance_km . ' km') : '-' }}</span></div>
                <div><span class="text-slate-500 dark:text-[#93A5C9]">Kode Pos:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->postal_code ?? '-' }}</span></div>
                @if($profile?->ktp_address)
                    <div class="sm:col-span-2"><span class="text-slate-500 dark:text-[#93A5C9]">Alamat KTP:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile->ktp_address }}{{ ($profile->ktp_city ?? $profile->city) ? ', ' . ($profile->ktp_city ?? $profile->city) : '' }}{{ ($profile->ktp_province ?? $profile->province) ? ', ' . ($profile->ktp_province ?? $profile->province) : '' }}</span></div>
                @endif
                <div class="sm:col-span-2"><span class="text-slate-500 dark:text-[#93A5C9]">Alamat Domisili:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ $profile?->address ?? '-' }}{{ $profile?->city ? ', ' . $profile->city : '' }}{{ $profile?->province ? ', ' . $profile->province : '' }}</span></div>
                @if(!empty($profile?->driving_licenses) && is_array($profile->driving_licenses))
                    @php
                        $licStrings = [];
                        foreach ($profile->driving_licenses as $lic) {
                            if (!empty($lic['type']) || !empty($lic['number'])) {
                                $licStrings[] = ($lic['type'] ?: 'SIM') . ': ' . ($lic['number'] ?: '-');
                            }
                        }
                    @endphp
                    @if(!empty($licStrings))
                        <div class="sm:col-span-2"><span class="text-slate-500 dark:text-[#93A5C9]">Kepemilikan SIM:</span> <span class="font-semibold text-slate-900 dark:text-white">{{ implode(' | ', $licStrings) }}</span></div>
                    @endif
                @endif
            </div>
        </section>

        <!-- Pendidikan -->
        @if($profile?->educations && $profile->educations->count() > 0)
            <section class="mb-8">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] border-b border-slate-200/80 dark:border-[#1D2E54] pb-1 mb-3">
                    Riwayat Pendidikan
                </h2>
                <div class="space-y-4">
                    @foreach($profile->educations as $edu)
                        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $edu->school_name }}</h3>
                                <p class="text-xs text-slate-600 dark:text-slate-300">
                                    {{ $edu->degree }} - {{ $edu->major }} {{ $edu->study_program ? '('.$edu->study_program.')' : '' }}
                                </p>
                                @if($edu->gpa)
                                    <p class="text-xs font-bold text-emerald-600 dark:text-[#93F514] mt-0.5">IPK: {{ $edu->gpa }}</p>
                                @endif
                            </div>
                            <span class="text-xs text-slate-500 dark:text-[#93A5C9] font-medium">
                                {{ $edu->start_year }} - {{ $edu->end_year ?? 'Sekarang' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Pengalaman Kerja -->
        @if($profile?->workExperiences && $profile->workExperiences->count() > 0)
            <section class="mb-8">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] border-b border-slate-200/80 dark:border-[#1D2E54] pb-1 mb-3">
                    Pengalaman Kerja
                </h2>
                <div class="space-y-5">
                    @foreach($profile->workExperiences as $exp)
                        <div>
                            <div class="flex flex-col sm:flex-row sm:items-baseline justify-between mb-1">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $exp->position }}
                                    @if($exp->initial_position)
                                        <span class="text-xs font-normal text-slate-400 dark:text-slate-500">(Awal: {{ $exp->initial_position }})</span>
                                    @endif
                                    <span class="font-normal text-slate-600 dark:text-[#93A5C9]">di {{ $exp->company_name }}</span>
                                    @if($exp->business_field)
                                        <span class="text-xs font-normal text-slate-400 dark:text-slate-500">[{{ $exp->business_field }}]</span>
                                    @endif
                                </h3>
                                <span class="text-xs text-slate-500 dark:text-[#93A5C9] font-medium">
                                    {{ \Carbon\Carbon::parse($exp->start_date)->isoFormat('MMM YYYY') }} - 
                                    {{ $exp->currently_working ? 'Sekarang' : ($exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->isoFormat('MMM YYYY') : '-') }}
                                </span>
                            </div>
                            @if($exp->description)
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $exp->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Pengalaman Organisasi -->
        @if($profile?->organizations && $profile->organizations->count() > 0)
            <section class="mb-8">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] border-b border-slate-200/80 dark:border-[#1D2E54] pb-1 mb-3">
                    Pengalaman Organisasi
                </h2>
                <div class="space-y-4">
                    @foreach($profile->organizations as $org)
                        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $org->position }} - {{ $org->name }}</h3>
                                @if($org->description)
                                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">{{ $org->description }}</p>
                                @endif
                            </div>
                            <span class="text-xs text-slate-500 dark:text-[#93A5C9] font-medium">
                                {{ $org->start_month }} {{ $org->start_year }} - {{ $org->is_active ? 'Sekarang' : ($org->end_year ? $org->end_month.' '.$org->end_year : '-') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Prestasi & Penghargaan -->
        @if($profile?->achievements && $profile->achievements->count() > 0)
            <section class="mb-8">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] border-b border-slate-200/80 dark:border-[#1D2E54] pb-1 mb-3">
                    Prestasi & Penghargaan
                </h2>
                <div class="space-y-3">
                    @foreach($profile->achievements as $ach)
                        <div class="flex items-baseline justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $ach->name }}</span>
                                <span class="ml-2 px-2 py-0.5 bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 rounded text-[10px] font-semibold">{{ $ach->scale }}</span>
                                @if($ach->description)
                                    <p class="text-slate-600 dark:text-slate-300 mt-0.5">{{ $ach->description }}</p>
                                @endif
                            </div>
                            <span class="text-slate-500 dark:text-[#93A5C9] font-medium">{{ $ach->month }} {{ $ach->year }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Keahlian, Sertifikasi & Pelatihan -->
        @php
            $generalSkills = $profile?->skills ? $profile->skills->where(fn($s) => $s->category === 'general' || empty($s->category)) : collect();
            $computerSkills = $profile?->skills ? $profile->skills->where('category', 'computer') : collect();
            $hasSkills = $generalSkills->count() > 0;
            $hasCompSkills = $computerSkills->count() > 0;
            $hasCerts = $profile?->certifications && $profile->certifications->count() > 0;
            $hasTrainings = $profile?->trainings && $profile->trainings->count() > 0;
            $hasLangs = $profile?->languages && $profile->languages->count() > 0;
        @endphp

        @if($hasSkills || $hasCompSkills || $hasCerts || $hasTrainings || $hasLangs)
            <section class="mb-8">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] border-b border-slate-200/80 dark:border-[#1D2E54] pb-1 mb-3">
                    {{ __('Keahlian & Data Tambahan') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    @if($hasSkills)
                        <div>
                            <h3 class="font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('Keahlian (Skills):') }}</h3>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($generalSkills as $sk)
                                    <span class="px-2.5 py-1 bg-emerald-50 dark:bg-[#14203A] text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-[#1D2E54] rounded-lg text-xs font-medium">
                                        {{ $sk->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($hasCompSkills)
                        <div>
                            <h3 class="font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('Aplikasi Komputer:') }}</h3>
                            <div class="space-y-1.5">
                                @foreach($computerSkills as $csk)
                                    <div class="p-2 rounded-lg bg-slate-50 dark:bg-[#14203A] border border-slate-200/70 dark:border-[#1D2E54] flex items-center justify-between gap-2">
                                        <div>
                                            <span class="font-semibold text-slate-800 dark:text-white">{{ $csk->name }}</span>
                                            @if($csk->description)
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block">{{ $csk->description }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            @if($csk->proficiency)
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                    {{ ucfirst(str_replace('_', ' ', $csk->proficiency)) }}
                                                </span>
                                            @endif
                                            @if($csk->certificate_path)
                                                <a href="{{ asset('storage/' . ltrim($csk->certificate_path, '/')) }}" target="_blank" class="no-print text-[10px] font-semibold text-emerald-600 dark:text-[#93F514] hover:underline flex items-center gap-0.5">
                                                    <span>{{ __('Berkas') }}</span>
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($hasCerts)
                        <div>
                            <h3 class="font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('Sertifikasi:') }}</h3>
                            <ul class="space-y-1 text-slate-700 dark:text-slate-300">
                                @foreach($profile->certifications as $cert)
                                    <li class="flex items-center justify-between gap-2">
                                        <span>&bull; {{ $cert->name }}</span>
                                        @if($cert->certificate_path)
                                            <a href="{{ asset('storage/' . ltrim($cert->certificate_path, '/')) }}" target="_blank" class="no-print text-[10.5px] font-semibold text-emerald-600 dark:text-[#93F514] hover:underline flex items-center gap-0.5 shrink-0">
                                                <span>{{ __('Berkas') }}</span>
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($hasTrainings)
                        <div>
                            <h3 class="font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('Pelatihan:') }}</h3>
                            <ul class="space-y-1 text-slate-700 dark:text-slate-300">
                                @foreach($profile->trainings as $tr)
                                    <li class="flex items-center justify-between gap-2">
                                        <span>&bull; {{ $tr->name }}</span>
                                        @if($tr->certificate_path)
                                            <a href="{{ asset('storage/' . ltrim($tr->certificate_path, '/')) }}" target="_blank" class="no-print text-[10.5px] font-semibold text-emerald-600 dark:text-[#93F514] hover:underline flex items-center gap-0.5 shrink-0">
                                                <span>{{ __('Berkas') }}</span>
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($hasLangs)
                        <div class="sm:col-span-2">
                            <h3 class="font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('Kemampuan Bahasa:') }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($profile->languages as $lang)
                                    <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-[#14203A] border border-slate-200/70 dark:border-[#1D2E54] flex flex-col justify-between gap-1.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-bold text-slate-800 dark:text-white">{{ $lang->name }}</span>
                                            @if($lang->certificate_path)
                                                <a href="{{ asset('storage/' . ltrim($lang->certificate_path, '/')) }}" target="_blank" class="no-print text-[10.5px] font-semibold text-emerald-600 dark:text-[#93F514] hover:underline flex items-center gap-0.5 shrink-0">
                                                    <span>{{ __('Berkas') }}</span>
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-500 dark:text-[#93A5C9] flex gap-2">
                                            <span>{{ __('Lisan') }}: <strong>{{ ucfirst($lang->oral ?? '-') }}</strong></span>
                                            <span>&bull;</span>
                                            <span>{{ __('Tulisan') }}: <strong>{{ ucfirst($lang->written ?? '-') }}</strong></span>
                                            <span>&bull;</span>
                                            <span>{{ __('Percakapan') }}: <strong>{{ ucfirst($lang->conversation ?? '-') }}</strong></span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        <!-- Social Media -->
        @if($profile?->socialMedias && $profile->socialMedias->count() > 0)
            <section>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] border-b border-slate-200/80 dark:border-[#1D2E54] pb-1 mb-3">
                    Media Sosial
                </h2>
                <div class="flex flex-wrap gap-4 text-xs">
                    @foreach($profile->socialMedias as $soc)
                        <a href="{{ $soc->url }}" target="_blank" class="text-emerald-600 dark:text-[#93F514] font-medium hover:underline flex items-center gap-1">
                            <span class="font-bold">{{ $soc->platform_name }}:</span> {{ $soc->url }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
        </div>
    </main>
</body>
</html>
