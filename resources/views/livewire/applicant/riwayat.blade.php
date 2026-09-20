<div class="space-y-6">
    <!-- Header Card -->
    <div
        class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Riwayat Lamaran Kerja</h2>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">* Pantau status progress seleksi, riwayat perubahan tahapan, dan jadwal wawancara dari lowongan yang telah Anda lamar.</p>
            </div>
            <div>
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95 shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Cari Lowongan Lain</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Peringatan & Notifikasi Jadwal Wawancara Aktif -->
    @if ($upcomingInterviews && $upcomingInterviews->isNotEmpty())
        <div class="space-y-3">
            @foreach ($upcomingInterviews as $interview)
                @php
                    $job = $interview->jobApplication->job ?? null;
                    $company = $job->company ?? null;
                    $dateObj = \Carbon\Carbon::parse($interview->interview_date);
                    $isToday = $dateObj->isToday();
                    $isTomorrow = $dateObj->isTomorrow();
                    $diffText = $isToday ? 'Hari Ini' : ($isTomorrow ? 'Besok' : $dateObj->translatedFormat('d M Y'));
                    $isOnline = !empty($interview->meeting_link) || str_contains(strtolower($interview->location ?? ''), 'online');
                @endphp
                <div class="rounded-2xl bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-5 sm:p-6 shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
                        <div class="flex items-start gap-4">
                            <!-- Calendar / Interview Icon -->
                            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-[#14203A] border border-blue-200/60 dark:border-[#1D2E54] text-blue-600 dark:text-[#93F514] flex items-center justify-center shrink-0 shadow-xs">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $isToday ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30' : 'bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-[#93A5C9] border border-slate-200 dark:border-[#1D2E54]' }}">
                                        Jadwal Wawancara {{ $diffText }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] border border-slate-200 dark:border-[#1D2E54]">
                                        Status: {{ $interview->status }}
                                    </span>
                                </div>

                                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-tight">
                                    Wawancara: {{ $job->title ?? 'Posisi Pekerjaan' }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-[#93A5C9]">
                                    {{ $company->name ?? 'Perusahaan' }} • Pewawancara: <strong class="text-slate-800 dark:text-white">{{ $interview->user->name ?? 'Tim HR / Rekruter' }}</strong>
                                </p>

                                <div class="flex flex-wrap items-center gap-2 pt-1.5 text-xs">
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] text-slate-700 dark:text-slate-300 font-medium">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>
                                            {{ $dateObj->translatedFormat('l, d F Y • H:i') }} WIB
                                        </span>
                                    </div>

                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] text-slate-700 dark:text-slate-300 font-medium">
                                        @if ($isOnline)
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            <span class="truncate">Online Video Meeting</span>
                                        @else
                                            <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="truncate">{{ $interview->location }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-row md:flex-col items-stretch gap-2 shrink-0 pt-2 md:pt-0">
                            @if ($isOnline && $interview->meeting_link)
                                <a href="{{ $interview->meeting_link }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs shadow-xs transition text-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span>Buka Link Meeting</span>
                                </a>
                            @endif

                            <button type="button" wire:click="openDetail({{ $interview->job_applications_id }})" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] text-slate-700 dark:text-slate-300 font-semibold text-xs border border-slate-200/80 dark:border-[#1D2E54] transition text-center shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-slate-500 dark:text-[#93A5C9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>Lihat Rincian</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Lamaran -->
        <div wire:click="$set('statusFilter', 'all')"
            class="cursor-pointer p-4 rounded-2xl border transition-all duration-200 {{ $statusFilter === 'all' ? 'bg-blue-50/70 dark:bg-[#14203A] border-blue-300 dark:border-blue-700 shadow-sm ring-2 ring-blue-500/20' : 'bg-white dark:bg-[#0D1527] border-slate-200/80 dark:border-[#1D2E54] hover:border-slate-300 dark:hover:border-slate-600 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-[#93A5C9] uppercase tracking-wider">Total Lamaran</span>
                <span class="p-2 rounded-xl bg-blue-50 dark:bg-[#14203A] text-blue-600 dark:text-blue-400 border border-transparent dark:border-[#1D2E54]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-2">{{ $stats['total'] }}</p>
            <span class="text-[11px] text-slate-400 dark:text-[#93A5C9]">Semua berkas terkirim</span>
        </div>

        <!-- Dalam Proses -->
        <div wire:click="$set('statusFilter', 'process')"
            class="cursor-pointer p-4 rounded-2xl border transition-all duration-200 {{ $statusFilter === 'process' ? 'bg-amber-50/70 dark:bg-[#14203A] border-amber-300 dark:border-amber-700 shadow-sm ring-2 ring-amber-500/20' : 'bg-white dark:bg-[#0D1527] border-slate-200/80 dark:border-[#1D2E54] hover:border-amber-200 dark:hover:border-slate-600 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Dalam Proses</span>
                <span class="p-2 rounded-xl bg-amber-50 dark:bg-[#14203A] text-amber-600 dark:text-amber-400 border border-transparent dark:border-[#1D2E54]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-2">{{ $stats['process'] }}</p>
            <span class="text-[11px] text-slate-400 dark:text-[#93A5C9]">Sedang ditinjau / seleksi</span>
        </div>

        <!-- Diterima -->
        <div wire:click="$set('statusFilter', 'Accepted')"
            class="cursor-pointer p-4 rounded-2xl border transition-all duration-200 {{ $statusFilter === 'Accepted' ? 'bg-emerald-50/70 dark:bg-[#14203A] border-emerald-300 dark:border-emerald-700 shadow-sm ring-2 ring-emerald-500/20' : 'bg-white dark:bg-[#0D1527] border-slate-200/80 dark:border-[#1D2E54] hover:border-emerald-200 dark:hover:border-slate-600 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-600 dark:text-[#93F514] uppercase tracking-wider">Diterima</span>
                <span class="p-2 rounded-xl bg-emerald-50 dark:bg-[#14203A] text-emerald-600 dark:text-[#93F514] border border-transparent dark:border-[#1D2E54]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-emerald-600 dark:text-[#93F514] mt-2">{{ $stats['accepted'] }}</p>
            <span class="text-[11px] text-slate-400 dark:text-[#93A5C9]">Lolos tahap akhir</span>
        </div>

        <!-- Tidak Lolos -->
        <div wire:click="$set('statusFilter', 'Rejected')"
            class="cursor-pointer p-4 rounded-2xl border transition-all duration-200 {{ $statusFilter === 'Rejected' ? 'bg-rose-50/70 dark:bg-[#14203A] border-rose-300 dark:border-rose-700 shadow-sm ring-2 ring-rose-500/20' : 'bg-white dark:bg-[#0D1527] border-slate-200/80 dark:border-[#1D2E54] hover:border-rose-200 dark:hover:border-slate-600 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Tidak Lolos</span>
                <span class="p-2 rounded-xl bg-rose-50 dark:bg-[#14203A] text-rose-600 dark:text-rose-400 border border-transparent dark:border-[#1D2E54]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-2">{{ $stats['rejected'] }}</p>
            <span class="text-[11px] text-slate-400 dark:text-[#93A5C9]">Belum berhasil</span>
        </div>
    </div>

    <!-- Filter Pills Status (Clean Horizontal Scrollbar) -->
    <div class="bg-white dark:bg-[#0D1527] p-3 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] shadow-xs">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar scroll-smooth">
            <button type="button" wire:click="$set('statusFilter', 'all')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'all' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                Semua Status
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Submitted')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Submitted' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                Terkirim (Submitted)
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Reviewed')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Reviewed' ? 'bg-amber-600 text-white shadow-sm shadow-amber-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                Lolos Berkas / Tahap Tes (Reviewed)
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Shortlisted')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Shortlisted' ? 'bg-purple-600 text-white shadow-sm shadow-purple-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                Lolos Ujian / Siap Wawancara (Shortlisted)
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Interview')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Interview' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                Wawancara (Interview)
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Accepted')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Accepted' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                Diterima (Accepted)
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Rejected')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Rejected' ? 'bg-rose-600 text-white shadow-sm shadow-rose-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                Ditolak (Rejected)
            </button>
        </div>
    </div>

    <!-- Application List -->
    <div class="space-y-4">
        @forelse ($applications as $app)
            @php
                $status = $app->status;
                $statusClass = match($status) {
                    'Accepted', 'accepted' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-[#93F514] border-emerald-200 dark:border-emerald-800/60',
                    'Rejected', 'rejected' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border-rose-200 dark:border-rose-900/60',
                    'Interview', 'interview' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border-blue-200 dark:border-blue-800/60',
                    'Shortlisted', 'shortlisted' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 border-purple-200 dark:border-purple-800/60',
                    'Reviewed', 'reviewed' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                    default => 'bg-slate-100 text-slate-700 dark:bg-[#14203A] dark:text-[#93A5C9] border-slate-200 dark:border-[#1D2E54]',
                };
                
                $statusLabel = match($status) {
                    'Accepted', 'accepted' => 'Diterima (Accepted)',
                    'Rejected', 'rejected' => 'Tidak Lolos (Rejected)',
                    'Interview', 'interview' => 'Tahap Wawancara (Interview)',
                    'Shortlisted', 'shortlisted' => 'Lolos Ujian / Siap Wawancara (Shortlisted)',
                    'Reviewed', 'reviewed' => 'Lolos Berkas / Tahap Ujian (Reviewed)',
                    default => 'Lamaran Diajukan (Submitted)',
                };

                // Determine step stage index (1: Submitted, 2: Lolos Berkas (Reviewed) / Ikut Tes, 3: Lolos Ujian (Shortlisted), 4: Wawancara, 5: Keputusan Akhir)
                $stepStage = 1;
                $hasCompletedTest = $app->testAttempts && $app->testAttempts->where('status', 'passed')->isNotEmpty();

                if (in_array(strtolower($status), ['reviewed'])) {
                    $stepStage = 2;
                } elseif (in_array(strtolower($status), ['shortlisted'])) {
                    $stepStage = 3;
                } elseif (in_array(strtolower($status), ['interview'])) {
                    $stepStage = 4;
                } elseif (in_array(strtolower($status), ['accepted', 'rejected'])) {
                    $stepStage = 5;
                }
            @endphp

            <div class="bg-white dark:bg-[#0D1527] rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] shadow-xs hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 p-5 md:p-6 space-y-4">
                <!-- Top Row: Company & Job Information -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <!-- Company Logo / Initials -->
                        @if ($app->job && $app->job->company && $app->job->company->logo)
                            <img src="{{ \Illuminate\Support\Str::startsWith($app->job->company->logo, ['http://', 'https://']) ? $app->job->company->logo : asset('storage/' . $app->job->company->logo) }}" alt="{{ $app->job->company->name }}"
                                class="w-12 h-12 rounded-xl object-contain bg-slate-50 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] p-1 shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] flex items-center justify-center text-blue-600 dark:text-[#93F514] font-bold text-base shadow-xs shrink-0">
                                {{ strtoupper(substr($app->job->company->name ?? ($app->job->title ?? 'J'), 0, 2)) }}
                            </div>
                        @endif

                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white leading-tight">
                                    {{ $app->job->title ?? 'Lowongan Pekerjaan' }}
                                </h3>
                                @if ($app->job && $app->job->employment_type)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] border border-slate-200/80 dark:border-[#1D2E54]">
                                        {{ $app->job->employment_type }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-y-1 gap-x-2 text-xs text-slate-500 dark:text-[#93A5C9] mt-1">
                                <span class="font-semibold text-blue-600 dark:text-[#93F514]">
                                    {{ $app->job->company->name ?? 'Perusahaan' }}
                                </span>
                                @if ($app->job && $app->job->department)
                                    <span>•</span>
                                    <span>{{ $app->job->department->name }}</span>
                                @endif
                                @if ($app->job && $app->job->location)
                                    <span>•</span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $app->job->location }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-1 shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                            @if (in_array(strtolower($status), ['accepted']))
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @elseif (in_array(strtolower($status), ['rejected']))
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            @endif
                            <span>{{ $statusLabel }}</span>
                        </span>
                        <span class="text-[11px] text-slate-400 dark:text-[#93A5C9]">
                            Dilamar: {{ \Carbon\Carbon::parse($app->applied_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                        </span>
                    </div>
                </div>

                <!-- Middle Row: Visual Timeline Progress Bar (Horizontal Connected Stepper) -->
                <div class="bg-slate-50 dark:bg-[#14203A]/70 p-4 rounded-2xl border border-slate-100 dark:border-[#1D2E54]">
                    <div class="relative flex items-center justify-between">
                        <!-- Connecting Background Line -->
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 dark:bg-[#1D2E54] -z-0"></div>
                        
                        <!-- Active Progress Line -->
                        @php
                            $progressWidths = [1 => '0%', 2 => '25%', 3 => '50%', 4 => '75%', 5 => '100%'];
                            $activeWidth = $progressWidths[$stepStage] ?? '0%';
                        @endphp
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-gradient-to-r from-blue-600 via-emerald-500 to-[#93F514] transition-all duration-500 -z-0" style="width: {{ $activeWidth }};"></div>

                        <!-- Step 1: Terkirim -->
                        <div class="relative z-10 flex flex-col items-center group">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $stepStage >= 1 ? 'bg-emerald-600 dark:bg-[#93F514] text-white dark:text-black ring-4 ring-emerald-100 dark:ring-[#93F514]/20' : 'bg-slate-200 dark:bg-[#0D1527] border border-slate-300 dark:border-[#1D2E54] text-slate-500 dark:text-[#93A5C9]' }}">
                                ✓
                            </div>
                            <span class="text-[11px] font-bold mt-1.5 whitespace-nowrap {{ $stepStage >= 1 ? 'text-emerald-600 dark:text-[#93F514]' : 'text-slate-400 dark:text-[#93A5C9]' }}">
                                Terkirim
                            </span>
                        </div>

                        <!-- Step 2: Seleksi Berkas -->
                        <div class="relative z-10 flex flex-col items-center group">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $stepStage >= 2 ? 'bg-emerald-600 dark:bg-[#93F514] text-white dark:text-black ring-4 ring-emerald-100 dark:ring-[#93F514]/20' : 'bg-slate-200 dark:bg-[#0D1527] border border-slate-300 dark:border-[#1D2E54] text-slate-500 dark:text-[#93A5C9]' }}">
                                {{ $stepStage > 2 ? '✓' : '2' }}
                            </div>
                            <span class="text-[11px] font-bold mt-1.5 whitespace-nowrap {{ $stepStage >= 2 ? 'text-emerald-600 dark:text-[#93F514]' : 'text-slate-400 dark:text-[#93A5C9]' }}">
                                Seleksi Berkas
                            </span>
                        </div>

                        <!-- Step 3: Tes Online -->
                        <div class="relative z-10 flex flex-col items-center group">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $stepStage >= 3 ? 'bg-emerald-600 dark:bg-[#93F514] text-white dark:text-black ring-4 ring-emerald-100 dark:ring-[#93F514]/20 animate-pulse' : 'bg-slate-200 dark:bg-[#0D1527] border border-slate-300 dark:border-[#1D2E54] text-slate-500 dark:text-[#93A5C9]' }}">
                                {{ $stepStage > 3 ? '✓' : '3' }}
                            </div>
                            <span class="text-[11px] font-bold mt-1.5 whitespace-nowrap {{ $stepStage >= 3 ? 'text-emerald-600 dark:text-[#93F514]' : 'text-slate-400 dark:text-[#93A5C9]' }}">
                                Tes Online
                            </span>
                        </div>

                        <!-- Step 4: Wawancara -->
                        <div class="relative z-10 flex flex-col items-center group">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $stepStage >= 4 ? 'bg-emerald-600 dark:bg-[#93F514] text-white dark:text-black ring-4 ring-emerald-100 dark:ring-[#93F514]/20' : 'bg-slate-200 dark:bg-[#0D1527] border border-slate-300 dark:border-[#1D2E54] text-slate-500 dark:text-[#93A5C9]' }}">
                                {{ $stepStage > 4 ? '✓' : '4' }}
                            </div>
                            <span class="text-[11px] font-bold mt-1.5 whitespace-nowrap {{ $stepStage >= 4 ? 'text-emerald-600 dark:text-[#93F514]' : 'text-slate-400 dark:text-[#93A5C9]' }}">
                                Wawancara
                            </span>
                        </div>

                        <!-- Step 5: Hasil Akhir -->
                        <div class="relative z-10 flex flex-col items-center group">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $stepStage >= 5 ? (strtolower($status) === 'accepted' ? 'bg-emerald-600 dark:bg-[#93F514] text-white dark:text-black ring-4 ring-emerald-100 dark:ring-[#93F514]/20' : 'bg-rose-600 text-white ring-4 ring-rose-100 dark:ring-rose-950/60') : 'bg-slate-200 dark:bg-[#0D1527] border border-slate-300 dark:border-[#1D2E54] text-slate-500 dark:text-[#93A5C9]' }}">
                                {{ $stepStage >= 5 ? (strtolower($status) === 'accepted' ? '✓' : '✕') : '5' }}
                            </div>
                            <span class="text-[11px] font-bold mt-1.5 whitespace-nowrap {{ $stepStage >= 5 ? (strtolower($status) === 'accepted' ? 'text-emerald-600 dark:text-[#93F514]' : 'text-rose-600 dark:text-rose-400') : 'text-slate-400 dark:text-[#93A5C9]' }}">
                                {{ strtolower($status) === 'accepted' ? 'Diterima' : (strtolower($status) === 'rejected' ? 'Ditolak' : 'Hasil Akhir') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Active Interview Alert Box (jika ada jadwal wawancara untuk lowongan ini) -->
                @php
                    $appActiveInterview = $app->interviewSchedules ? $app->interviewSchedules->whereIn('status', ['Scheduled', 'Rescheduled'])->last() : null;
                @endphp
                @if ($appActiveInterview)
                    @php
                        $intDate = \Carbon\Carbon::parse($appActiveInterview->interview_date);
                        $isOnlineInt = !empty($appActiveInterview->meeting_link) || str_contains(strtolower($appActiveInterview->location ?? ''), 'online');
                    @endphp
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 dark:bg-[#93F514] text-white dark:text-black flex items-center justify-center shrink-0 shadow-xs">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-slate-900 dark:text-white">
                                        Jadwal Wawancara ({{ $appActiveInterview->status }})
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $isOnlineInt ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800' : 'bg-blue-50 dark:bg-[#1D2E54] text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' }}">
                                        {{ $isOnlineInt ? 'Online Video' : 'Tatap Muka' }}
                                    </span>
                                </div>
                                <p class="text-xs font-medium text-slate-700 dark:text-slate-300">
                                    {{ $intDate->translatedFormat('l, d F Y • H:i') }} WIB
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-[#93A5C9]">
                                    {{ $isOnlineInt ? 'Tautan meeting telah tersedia' : 'Lokasi: ' . $appActiveInterview->location }}
                                </p>
                            </div>
                        </div>

                        @if ($isOnlineInt && $appActiveInterview->meeting_link)
                            <a href="{{ $appActiveInterview->meeting_link }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-xs transition shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>Buka Link Meeting</span>
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Bottom Row: Notes & Detail Button -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1 border-t border-slate-100 dark:border-[#1D2E54]">
                    <div class="min-w-0 flex-1">
                        @if ($app->notes)
                            <p class="text-xs text-slate-600 dark:text-slate-300 truncate">
                                <strong class="text-slate-800 dark:text-white">Catatan:</strong> {{ $app->notes }}
                            </p>
                        @elseif ($app->interviewSchedules && $app->interviewSchedules->isNotEmpty())
                            @php $latestInterview = $app->interviewSchedules->last(); @endphp
                            <p class="text-xs text-blue-600 dark:text-[#93F514] font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Jadwal Wawancara: {{ \Carbon\Carbon::parse($latestInterview->interview_date)->translatedFormat('d M Y, H:i') }}</span>
                            </p>
                        @else
                            <p class="text-xs text-slate-400 dark:text-[#93A5C9]">
                                Belum ada catatan tambahan dari tim rekruter.
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @php
                            $availableTest = $app->job && $app->job->tests ? $app->job->tests->first() : null;
                            $latestAttempt = $app->testAttempts ? $app->testAttempts->where('test_id', $availableTest?->id)->last() : null;
                            $canTakeTest = in_array(strtolower($status), ['reviewed', 'shortlisted', 'interview', 'accepted']);
                        @endphp

                        @if ($availableTest && !in_array(strtolower($status), ['rejected']))
                            @if ($latestAttempt && $latestAttempt->status !== 'in_progress')
                                @php
                                    $isTestDisc = str_contains(strtolower($availableTest->category?->name ?? ''), 'disc');
                                @endphp
                                <!-- Sudah Mengerjakan Tes -->
                                <a href="{{ route('applicant.test', ['applicationId' => $app->id, 'testId' => $availableTest->id]) }}"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-[#93F514] text-xs font-semibold rounded-xl transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $isTestDisc ? 'Lihat Status Tes' : 'Lihat Hasil Tes' }}</span>
                                </a>
                            @elseif ($canTakeTest || ($latestAttempt && $latestAttempt->status === 'in_progress'))
                                <!-- Sudah Lolos Berkas / Diizinkan Ikut Tes -->
                                <a href="{{ route('applicant.test', ['applicationId' => $app->id, 'testId' => $availableTest->id]) }}"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs rounded-xl shadow-xs transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>{{ $latestAttempt ? 'Lanjutkan Ujian' : 'Mulai Ujian Online' }}</span>
                                </a>
                            @else
                                <!-- Masih tahap awal Submitted (Belum Lolos Berkas) -->
                                <div class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-amber-700 dark:text-amber-300 text-xs font-medium rounded-xl" title="Ujian online akan terbuka setelah berkas lamaran Anda selesai diverifikasi & disetujui tim HR.">
                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <span>Ujian: Menunggu Seleksi Berkas</span>
                                </div>
                            @endif
                        @endif

                        <button wire:click="openDetail({{ $app->id }})"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 border border-slate-200/80 dark:border-[#1D2E54] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#14203A] text-xs font-semibold rounded-xl transition shadow-2xs">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Lihat Detail</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State matching project design standard -->
            <div class="col-span-full bg-white dark:bg-[#0D1527] p-8 md:p-10 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] text-center flex flex-col items-center justify-center space-y-4">
                <svg class="w-10 h-10 text-slate-300 dark:text-[#1D2E54]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.816c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6A2.25 2.25 0 0 0 4.727 20.25h14.546a2.25 2.25 0 0 0 2.224-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                </svg>
                <p class="text-sm font-medium text-slate-500 dark:text-[#93A5C9]">
                    {{ !empty($search) || $statusFilter !== 'all' ? 'Tidak ada lamaran yang sesuai dengan filter pencarian.' : 'Belum ada data untuk ditampilkan' }}
                </p>
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-emerald-600 dark:border-[#93F514] text-emerald-700 dark:text-[#93F514] hover:bg-emerald-50 dark:hover:bg-[#93F514]/10 text-sm font-bold rounded-2xl transition shadow-2xs">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Jelajahi Lowongan Kerja</span>
                </a>
            </div>
        @endforelse
    </div>

    <!-- Modal Detail Lamaran -->
    @if ($showDetailModal && $selectedApplication)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="closeDetail"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200 dark:border-[#1D2E54]">
                    <!-- Modal Header -->
                    <div class="px-6 py-5 border-b border-slate-200/80 dark:border-[#1D2E54] flex items-center justify-between bg-slate-50/50 dark:bg-[#14203A]/50">
                        <div class="flex items-center gap-3">
                            @if ($selectedApplication->job && $selectedApplication->job->company && $selectedApplication->job->company->logo)
                                <img src="{{ \Illuminate\Support\Str::startsWith($selectedApplication->job->company->logo, ['http://', 'https://']) ? $selectedApplication->job->company->logo : asset('storage/' . $selectedApplication->job->company->logo) }}" alt="{{ $selectedApplication->job->company->name }}"
                                    class="w-10 h-10 rounded-xl object-contain bg-white dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] p-1 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-blue-600 dark:bg-[#93F514] text-white dark:text-black flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($selectedApplication->job->company->name ?? 'J', 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ $selectedApplication->job->title ?? 'Detail Lamaran' }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-[#93A5C9]">
                                    {{ $selectedApplication->job->company->name ?? '-' }} • {{ $selectedApplication->job->department->name ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeDetail" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                        <!-- Summary Info Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div class="p-3 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-100 dark:border-[#1D2E54]">
                                <span class="block text-[10px] font-bold text-slate-400 dark:text-[#93A5C9] uppercase">Status Terkini</span>
                                <span class="block text-xs font-bold text-blue-600 dark:text-[#93F514] mt-0.5">
                                    {{ $selectedApplication->status }}
                                </span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-100 dark:border-[#1D2E54]">
                                <span class="block text-[10px] font-bold text-slate-400 dark:text-[#93A5C9] uppercase">Tanggal Melamar</span>
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                                    {{ \Carbon\Carbon::parse($selectedApplication->applied_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                                </span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-100 dark:border-[#1D2E54] col-span-2 sm:col-span-1">
                                <span class="block text-[10px] font-bold text-slate-400 dark:text-[#93A5C9] uppercase">Lokasi Penempatan</span>
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                                    {{ $selectedApplication->job->location ?? 'Tidak ditentukan' }}
                                </span>
                            </div>
                        </div>

                        <!-- Section: Riwayat Perubahan Status (Horizontal Timeline Tracker) -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Riwayat Perubahan Status Seleksi</span>
                            </h4>

                            @if ($selectedApplication->statusHistories && $selectedApplication->statusHistories->isNotEmpty())
                                <div class="overflow-x-auto pb-3 pt-1 scrollbar-thin">
                                    <div class="flex items-start gap-4 min-w-max">
                                        @foreach ($selectedApplication->statusHistories->sortBy('changed_at') as $index => $hist)
                                            <div class="flex items-start gap-3">
                                                <div class="w-64 p-3.5 bg-slate-50 dark:bg-[#14203A] rounded-2xl border border-slate-100 dark:border-[#1D2E54] space-y-1.5 shadow-2xs relative">
                                                    <div class="flex items-center justify-between">
                                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-blue-50 dark:bg-[#0D1527] text-blue-700 dark:text-[#93F514] border border-blue-200 dark:border-[#1D2E54]">
                                                            {{ $hist->status }}
                                                        </span>
                                                        <span class="text-[10px] text-slate-400 dark:text-[#93A5C9] font-mono">
                                                            {{ \Carbon\Carbon::parse($hist->changed_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                                                        </span>
                                                    </div>
                                                    @if ($hist->notes)
                                                        <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-3 leading-snug">
                                                            {{ $hist->notes }}
                                                        </p>
                                                    @else
                                                        <p class="text-[11px] text-slate-400 dark:text-[#93A5C9] italic">
                                                            Status diperbarui.
                                                        </p>
                                                    @endif
                                                </div>

                                                @if (!$loop->last)
                                                    <div class="pt-6 text-slate-300 dark:text-slate-600">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="p-3.5 bg-slate-50 dark:bg-[#14203A] rounded-xl text-xs text-slate-400 dark:text-[#93A5C9] text-center border border-slate-100 dark:border-[#1D2E54]">
                                    Belum ada log riwayat perubahan status tercatat.
                                </div>
                            @endif
                        </div>

                        <!-- Section: Jadwal Wawancara -->
                        @if ($selectedApplication->interviewSchedules && $selectedApplication->interviewSchedules->isNotEmpty())
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Jadwal & Agenda Wawancara</span>
                                </h4>
                                <div class="space-y-3">
                                    @foreach ($selectedApplication->interviewSchedules as $interview)
                                        @php
                                            $isModalOnline = !empty($interview->meeting_link) || str_contains(strtolower($interview->location ?? ''), 'online');
                                            $isModalActive = in_array($interview->status, ['Scheduled', 'Rescheduled']);
                                        @endphp
                                        <div class="p-4 rounded-2xl border {{ $isModalActive ? 'bg-blue-50/70 dark:bg-[#14203A] border-blue-200 dark:border-[#1D2E54] ring-2 ring-blue-500/10' : 'bg-slate-50 dark:bg-[#14203A] border-slate-100 dark:border-[#1D2E54]' }} space-y-2.5">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($interview->interview_date)->translatedFormat('l, d F Y • H:i') }} WIB
                                                </span>
                                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full {{ $interview->status === 'Completed' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-[#93F514]' : 'bg-blue-100 dark:bg-[#0D1527] text-blue-700 dark:text-blue-300' }}">
                                                    {{ $interview->status }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-slate-700 dark:text-slate-300 space-y-1">
                                                <p><strong>Metode & Lokasi:</strong> {{ $isModalOnline ? 'Online Video Meeting' : $interview->location }}</p>
                                                <p><strong>Pewawancara:</strong> {{ $interview->user->name ?? 'Tim HR / Rekruter' }}</p>
                                                @if ($interview->notes)
                                                    <p class="text-slate-600 dark:text-slate-400"><strong>Catatan:</strong> {{ $interview->notes }}</p>
                                                @endif
                                                @if ($interview->meeting_link)
                                                    <div class="pt-1.5">
                                                        <a href="{{ $interview->meeting_link }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs rounded-xl shadow-xs transition">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                            </svg>
                                                            <span>Buka Tautan Video Meeting</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Section: Riwayat Tes Online -->
                        @if ($selectedApplication->testAttempts && $selectedApplication->testAttempts->isNotEmpty())
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>Tes Rekrutmen Online</span>
                                </h4>
                                <div class="space-y-2">
                                    @foreach ($selectedApplication->testAttempts as $attempt)
                                        @php
                                            $isAttemptDisc = str_contains(strtolower($attempt->test?->category?->name ?? ''), 'disc');
                                        @endphp
                                        <div class="p-3.5 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-100 dark:border-[#1D2E54] flex items-center justify-between">
                                            <div>
                                                <h5 class="text-xs font-bold text-slate-900 dark:text-white">
                                                    {{ $attempt->test->title ?? 'Ujian Tes Online' }}
                                                </h5>
                                                <span class="text-[10px] text-slate-400 dark:text-[#93A5C9]">
                                                    Status: {{ ucfirst($attempt->status) }}
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                @if ($isAttemptDisc)
                                                    <span class="text-xs font-bold text-emerald-600 dark:text-[#93F514]">
                                                        Tersimpan
                                                    </span>
                                                @else
                                                    <span class="text-xs font-extrabold text-blue-600 dark:text-[#93F514]">
                                                        Skor: {{ $attempt->total_score ?? ($attempt->objective_score ?? '-') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-[#14203A]/50 border-t border-slate-200/80 dark:border-[#1D2E54] flex justify-end">
                        <button type="button" wire:click="closeDetail"
                            class="px-5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A] rounded-xl transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
