<div class="space-y-6">
    <!-- Header Card -->
    <div
        class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Riwayat Lamaran Kerja') }}</h2>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">{{ __('* Pantau status progress seleksi, riwayat perubahan tahapan, dan jadwal wawancara dari lowongan yang telah Anda lamar.') }}</p>
            </div>
            <div>
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95 shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>{{ __('Cari Lowongan Lain') }}</span>
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
                    $diffText = $isToday ? __('Hari Ini') : ($isTomorrow ? __('Besok') : $dateObj->translatedFormat('d M Y'));
                    $isOnline = !empty($interview->meeting_link) || str_contains(strtolower($interview->location ?? ''), 'online');
                    $statusFormatted = match($interview->status) {
                        'Scheduled' => __('Terjadwal'),
                        'Rescheduled' => __('Jadwal Ulang'),
                        'Completed' => __('Selesai'),
                        'Cancelled' => __('Dibatalkan'),
                        default => $interview->status,
                    };
                @endphp
                <div class="rounded-2xl bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-4 sm:p-5 md:p-6 shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
                        <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                            <!-- Calendar / Interview Icon -->
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-blue-50 dark:bg-[#14203A] border border-blue-200/60 dark:border-[#1D2E54] text-blue-600 dark:text-[#93F514] flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <div class="space-y-2 min-w-0 flex-1">
                                <!-- Status Badges -->
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] sm:text-xs font-semibold {{ $isToday ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30' : 'bg-blue-50 dark:bg-[#14203A] text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-[#1D2E54]' }}">
                                        {{ __('Jadwal Wawancara') }} {{ $diffText }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] sm:text-xs font-medium bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] border border-slate-200 dark:border-[#1D2E54]">
                                        {{ __('Status:') }} {{ $statusFormatted }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] sm:text-xs font-semibold {{ $isOnline ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#1D2E54]' }}">
                                        {{ $isOnline ? __('Online Video') : __('Tatap Muka') }}
                                    </span>
                                </div>

                                <!-- Job Title & Company -->
                                <div>
                                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-slate-900 dark:text-white leading-tight break-words">
                                        {{ __('Wawancara:') }} {{ $job->title ?? __('Posisi Pekerjaan') }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-0.5 break-words">
                                        {{ $company->name ?? __('Perusahaan') }} • {{ __('Pewawancara:') }} <strong class="text-slate-800 dark:text-white">{{ $interview->user->name ?? __('Tim HR / Rekruter') }}</strong>
                                    </p>
                                </div>

                                <!-- Details (Waktu & Lokasi) -->
                                <div class="space-y-2 pt-1 text-xs">
                                    <!-- Waktu Wawancara -->
                                    <div class="flex items-center gap-2.5 p-2 sm:p-2.5 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/70 dark:border-[#1D2E54] text-slate-700 dark:text-slate-300 min-w-0">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <div class="min-w-0 flex-1 leading-snug">
                                            <span class="font-semibold text-slate-800 dark:text-slate-200 break-words">
                                                {{ $dateObj->translatedFormat('l, d F Y • H:i') }} WIB
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Lokasi Wawancara (Text Memanjang & Tombol Maps Full di Bawah) -->
                                    <div class="p-2.5 sm:p-3 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/70 dark:border-[#1D2E54] text-slate-700 dark:text-slate-300 space-y-2.5 min-w-0">
                                        <div class="flex items-start gap-2.5 min-w-0">
                                            @if ($isOnline)
                                                <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                                <div class="min-w-0 flex-1 leading-snug">
                                                    <span class="font-semibold text-slate-800 dark:text-slate-200 break-words">{{ __('Online Video Meeting') }}</span>
                                                </div>
                                            @else
                                                <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <div class="min-w-0 flex-1 leading-snug">
                                                    <span class="font-semibold text-slate-800 dark:text-slate-200 break-words">{{ $interview->location }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        @if (!$isOnline && !empty($interview->location))
                                            <div class="pt-1 border-t border-slate-200/60 dark:border-[#1D2E54]">
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($interview->location) }}" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer" 
                                                   class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-blue-100/70 hover:bg-blue-200 active:bg-blue-300 dark:bg-[#1D2E54] dark:hover:bg-[#253B6D] text-xs font-bold text-blue-700 dark:text-[#93F514] border border-blue-200/80 dark:border-blue-900/60 transition shadow-2xs active:scale-[0.99] text-center" 
                                                   title="{{ __('Buka di Google Maps') }}">
                                                    <svg class="w-4 h-4 shrink-0 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span>{{ __('Buka di Google Maps') }}</span>
                                                    <svg class="w-3.5 h-3.5 shrink-0 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if ($interview->notes)
                                    <div class="flex items-start gap-2 p-2.5 rounded-xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/40 text-amber-800 dark:text-amber-200 text-xs">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="min-w-0 flex-1">
                                            <strong class="font-semibold">{{ __('Catatan Rekruter:') }}</strong>
                                            <span class="break-words">{{ $interview->notes }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons (Full-Width Thumb-Friendly on Mobile, Stacked/Sleek on Desktop) -->
                        <div class="flex flex-col sm:flex-row md:flex-col items-stretch gap-2 shrink-0 w-full md:w-48 pt-3 md:pt-0 border-t border-slate-100 dark:border-[#1D2E54]/70 md:border-0">
                            @if ($isOnline && $interview->meeting_link)
                                <a href="{{ $interview->meeting_link }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs shadow-xs transition text-center active:scale-[0.99]">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ __('Buka Link Meeting') }}</span>
                                </a>
                            @endif

                            <button type="button" wire:click="openDetail({{ $interview->job_applications_id }})" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] text-slate-700 dark:text-slate-300 font-semibold text-xs border border-slate-200/80 dark:border-[#1D2E54] transition text-center shadow-2xs active:scale-[0.99]">
                                <svg class="w-3.5 h-3.5 text-slate-500 dark:text-[#93A5C9] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>{{ __('Lihat Rincian') }}</span>
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
                <span class="text-xs font-bold text-slate-500 dark:text-[#93A5C9] uppercase tracking-wider">{{ __('Total Lamaran') }}</span>
                <span class="p-2 rounded-xl bg-blue-50 dark:bg-[#14203A] text-blue-600 dark:text-blue-400 border border-transparent dark:border-[#1D2E54]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-2">{{ $stats['total'] }}</p>
            <span class="text-[11px] text-slate-400 dark:text-[#93A5C9]">{{ __('Semua berkas terkirim') }}</span>
        </div>

        <!-- Dalam Proses -->
        <div wire:click="$set('statusFilter', 'process')"
            class="cursor-pointer p-4 rounded-2xl border transition-all duration-200 {{ $statusFilter === 'process' ? 'bg-amber-50/70 dark:bg-[#14203A] border-amber-300 dark:border-amber-700 shadow-sm ring-2 ring-amber-500/20' : 'bg-white dark:bg-[#0D1527] border-slate-200/80 dark:border-[#1D2E54] hover:border-amber-200 dark:hover:border-slate-600 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">{{ __('Dalam Proses') }}</span>
                <span class="p-2 rounded-xl bg-amber-50 dark:bg-[#14203A] text-amber-600 dark:text-amber-400 border border-transparent dark:border-[#1D2E54]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-2">{{ $stats['process'] }}</p>
            <span class="text-[11px] text-slate-400 dark:text-[#93A5C9]">{{ __('Sedang ditinjau / seleksi') }}</span>
        </div>

        <!-- Diterima -->
        <div wire:click="$set('statusFilter', 'Accepted')"
            class="cursor-pointer p-4 rounded-2xl border transition-all duration-200 {{ $statusFilter === 'Accepted' ? 'bg-emerald-50/70 dark:bg-[#14203A] border-emerald-300 dark:border-emerald-700 shadow-sm ring-2 ring-emerald-500/20' : 'bg-white dark:bg-[#0D1527] border-slate-200/80 dark:border-[#1D2E54] hover:border-emerald-200 dark:hover:border-slate-600 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-600 dark:text-[#93F514] uppercase tracking-wider">{{ __('Diterima') }}</span>
                <span class="p-2 rounded-xl bg-emerald-50 dark:bg-[#14203A] text-emerald-600 dark:text-[#93F514] border border-transparent dark:border-[#1D2E54]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-emerald-600 dark:text-[#93F514] mt-2">{{ $stats['accepted'] }}</p>
            <span class="text-[11px] text-slate-400 dark:text-[#93A5C9]">{{ __('Lolos tahap akhir') }}</span>
        </div>

        <!-- Tidak Lolos -->
        <div wire:click="$set('statusFilter', 'Rejected')"
            class="cursor-pointer p-4 rounded-2xl border transition-all duration-200 {{ $statusFilter === 'Rejected' ? 'bg-rose-50/70 dark:bg-[#14203A] border-rose-300 dark:border-rose-700 shadow-sm ring-2 ring-rose-500/20' : 'bg-white dark:bg-[#0D1527] border-slate-200/80 dark:border-[#1D2E54] hover:border-rose-200 dark:hover:border-slate-600 shadow-xs' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">{{ __('Tidak Lolos') }}</span>
                <span class="p-2 rounded-xl bg-rose-50 dark:bg-[#14203A] text-rose-600 dark:text-rose-400 border border-transparent dark:border-[#1D2E54]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-2">{{ $stats['rejected'] }}</p>
            <span class="text-[11px] text-slate-400 dark:text-[#93A5C9]">{{ __('Belum berhasil') }}</span>
        </div>
    </div>

    <!-- Filter Pills Status (Clean Horizontal Scrollbar) -->
    <div class="bg-white dark:bg-[#0D1527] p-3 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] shadow-xs">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar scroll-smooth">
            <button type="button" wire:click="$set('statusFilter', 'all')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'all' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                {{ __('Semua Status') }}
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Submitted')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Submitted' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                {{ __('Diajukan') }}
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Reviewed')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Reviewed' ? 'bg-amber-600 text-white shadow-sm shadow-amber-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                {{ __('Lolos Berkas') }}
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Shortlisted')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Shortlisted' ? 'bg-purple-600 text-white shadow-sm shadow-purple-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                {{ __('Lolos Ujian') }}
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Interview')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Interview' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                {{ __('Wawancara') }}
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Accepted')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Accepted' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                {{ __('Diterima') }}
            </button>
            <button type="button" wire:click="$set('statusFilter', 'Rejected')"
                class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 shrink-0 {{ $statusFilter === 'Rejected' ? 'bg-rose-600 text-white shadow-sm shadow-rose-500/20' : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] hover:bg-slate-200 dark:hover:bg-[#1A2A4C]' }}">
                {{ __('Ditolak') }}
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
                    'Partial Approved', 'partial approved' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border-sky-200 dark:border-sky-800/60',
                    'Reviewed', 'reviewed' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                    default => 'bg-slate-100 text-slate-700 dark:bg-[#14203A] dark:text-[#93A5C9] border-slate-200 dark:border-[#1D2E54]',
                };
                
                $statusLabel = match($status) {
                    'Accepted', 'accepted' => __('Diterima'),
                    'Rejected', 'rejected' => __('Ditolak'),
                    'Interview', 'interview' => __('Wawancara'),
                    'Shortlisted', 'shortlisted' => __('Lolos Ujian'),
                    'Partial Approved', 'partial approved' => __('Partial Approved'),
                    'Reviewed', 'reviewed' => __('Lolos Berkas'),
                    default => __('Diajukan'),
                };

                // Determine step stage index
                // 1: Submitted, 2: Lolos Berkas (Reviewed), 3: Tes Online (Reviewed = ikut tes),
                // 4: Shortlisted / Lolos Tes / Siap Wawancara, 5: Interview, 6: Keputusan Akhir
                $stepStage = 1;

                if (in_array(strtolower($status), ['reviewed'])) {
                    $stepStage = 2;
                } elseif (in_array(strtolower($status), ['shortlisted'])) {
                    $stepStage = 4; // Tes sudah selesai, kini menunggu wawancara
                } elseif (in_array(strtolower($status), ['interview'])) {
                    $stepStage = 4;
                } elseif (in_array(strtolower($status), ['accepted', 'rejected'])) {
                    $stepStage = 5;
                }
            @endphp

            <div class="bg-white dark:bg-[#0D1527] rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] shadow-xs hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 p-4 sm:p-5 md:p-6 space-y-4">
                <!-- Top Row: Company & Job Information -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 sm:gap-4">
                    <div class="flex items-start gap-3 sm:gap-3.5 min-w-0 flex-1">
                        <!-- Company Logo / Initials -->
                        @if ($app->job && $app->job->company && $app->job->company->logo)
                            <img src="{{ \Illuminate\Support\Str::startsWith($app->job->company->logo, ['http://', 'https://']) ? $app->job->company->logo : asset('storage/' . $app->job->company->logo) }}" alt="{{ $app->job->company->name }}"
                                class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl object-contain bg-white border border-slate-200 dark:border-slate-700 p-1 shrink-0 shadow-xs">
                        @else
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-white border border-slate-200 dark:border-slate-700 flex items-center justify-center text-blue-600 dark:text-blue-600 font-bold text-sm sm:text-base shadow-xs shrink-0">
                                {{ strtoupper(substr($app->job->company->name ?? ($app->job->title ?? 'J'), 0, 2)) }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-tight break-words">
                                    {{ $app->job->title ?? __('Lowongan Pekerjaan') }}
                                </h3>
                                @if ($app->job && $app->job->employment_type)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] border border-slate-200/80 dark:border-[#1D2E54] shrink-0">
                                        {{ $app->job->employment_type }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-y-1 gap-x-2 text-xs text-slate-500 dark:text-[#93A5C9] mt-1">
                                <span class="font-semibold text-blue-600 dark:text-[#93F514] truncate max-w-[140px] sm:max-w-none">
                                    {{ $app->job->company->name ?? __('Perusahaan') }}
                                </span>
                                @if ($app->job && $app->job->department)
                                    <span>•</span>
                                    <span class="truncate max-w-[120px] sm:max-w-none">{{ $app->job->department->name }}</span>
                                @endif
                                @if ($app->job && $app->job->location)
                                    <span>•</span>
                                    <span class="inline-flex items-center gap-1 truncate max-w-[140px] sm:max-w-none">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-1 shrink-0 pt-1 sm:pt-0 border-t border-slate-100 dark:border-[#1D2E54]/60 sm:border-0">
                        <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
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
                        <span class="text-[10px] sm:text-[11px] text-slate-400 dark:text-[#93A5C9]">
                            {{ __('Dilamar:') }} {{ \Carbon\Carbon::parse($app->applied_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                        </span>
                    </div>
                </div>

                <!-- Middle Row: Visual Timeline Progress Bar (Horizontal Connected Stepper) -->
                <div class="bg-slate-50 dark:bg-[#14203A]/70 p-3 sm:p-4 rounded-2xl border border-slate-100 dark:border-[#1D2E54]">
                    @php
                        $progressWidths = [1 => '0%', 2 => '25%', 3 => '50%', 4 => '75%', 5 => '100%'];
                        $activeWidth = $progressWidths[$stepStage] ?? '0%';
                    @endphp
                    <div class="relative flex items-start justify-between">
                        <!-- Connecting Background Line (Runs exactly between step 1 and step 5 circle centers) -->
                        <div class="absolute left-[10%] right-[10%] top-4 -translate-y-1/2 h-1 bg-slate-200 dark:bg-[#1D2E54] rounded-full overflow-hidden">
                            <!-- Active Progress Line -->
                            <div class="h-full bg-gradient-to-r from-blue-600 via-emerald-500 to-[#93F514] rounded-full transition-all duration-500" style="width: {{ $activeWidth }};"></div>
                        </div>

                        <!-- Step 1: Terkirim -->
                        <div class="relative z-10 w-1/5 flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $stepStage >= 1 ? 'bg-emerald-600 dark:bg-[#93F514] text-white dark:text-black ring-4 ring-emerald-100 dark:ring-[#93F514]/25' : 'bg-white dark:bg-[#0D1527] border-2 border-slate-300 dark:border-[#1D2E54] text-slate-400 dark:text-[#93A5C9]' }}">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold mt-2 text-center leading-tight break-words px-0.5 {{ $stepStage >= 1 ? 'text-emerald-600 dark:text-[#93F514]' : 'text-slate-400 dark:text-[#93A5C9]' }}">
                                {{ __('Terkirim') }}
                            </span>
                        </div>

                        <!-- Step 2: Seleksi Berkas -->
                        <div class="relative z-10 w-1/5 flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $stepStage >= 2 ? 'bg-emerald-600 dark:bg-[#93F514] text-white dark:text-black ring-4 ring-emerald-100 dark:ring-[#93F514]/25' : 'bg-white dark:bg-[#0D1527] border-2 border-slate-300 dark:border-[#1D2E54] text-slate-400 dark:text-[#93A5C9]' }}">
                                @if ($stepStage > 2)
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    2
                                @endif
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold mt-2 text-center leading-tight break-words px-0.5 {{ $stepStage >= 2 ? 'text-emerald-600 dark:text-[#93F514]' : 'text-slate-400 dark:text-[#93A5C9]' }}">
                                {{ __('Seleksi Berkas') }}
                            </span>
                        </div>

                        <!-- Step 3: Tes Online -->
                        <div class="relative z-10 w-1/5 flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $stepStage >= 3 ? 'bg-emerald-600 dark:bg-[#93F514] text-white dark:text-black ring-4 ring-emerald-100 dark:ring-[#93F514]/25' : 'bg-white dark:bg-[#0D1527] border-2 border-slate-300 dark:border-[#1D2E54] text-slate-400 dark:text-[#93A5C9]' }}">
                                @if ($stepStage > 3)
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    3
                                @endif
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold mt-2 text-center leading-tight break-words px-0.5 {{ $stepStage >= 3 ? 'text-emerald-600 dark:text-[#93F514]' : 'text-slate-400 dark:text-[#93A5C9]' }}">
                                {{ __('Tes Online') }}
                            </span>
                        </div>

                        <!-- Step 4: Wawancara -->
                        <div class="relative z-10 w-1/5 flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $stepStage >= 4 ? 'bg-emerald-600 dark:bg-[#93F514] text-white dark:text-black ring-4 ring-emerald-100 dark:ring-[#93F514]/25' . ($stepStage === 4 ? ' animate-pulse' : '') : 'bg-white dark:bg-[#0D1527] border-2 border-slate-300 dark:border-[#1D2E54] text-slate-400 dark:text-[#93A5C9]' }}">
                                @if ($stepStage > 4)
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    4
                                @endif
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold mt-2 text-center leading-tight break-words px-0.5 {{ $stepStage >= 4 ? 'text-emerald-600 dark:text-[#93F514]' : 'text-slate-400 dark:text-[#93A5C9]' }}">
                                {{ __('Wawancara') }}
                            </span>
                        </div>

                        <!-- Step 5: Hasil Akhir -->
                        <div class="relative z-10 w-1/5 flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-sm {{ $stepStage >= 5 ? (strtolower($status) === 'accepted' ? 'bg-emerald-600 dark:bg-[#93F514] text-white dark:text-black ring-4 ring-emerald-100 dark:ring-[#93F514]/25' : 'bg-rose-600 text-white ring-4 ring-rose-100 dark:ring-rose-950/60') : 'bg-white dark:bg-[#0D1527] border-2 border-slate-300 dark:border-[#1D2E54] text-slate-400 dark:text-[#93A5C9]' }}">
                                @if ($stepStage >= 5)
                                    @if (strtolower($status) === 'accepted')
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @endif
                                @else
                                    5
                                @endif
                            </div>
                            <span class="text-[10px] sm:text-xs font-bold mt-2 text-center leading-tight break-words px-0.5 {{ $stepStage >= 5 ? (strtolower($status) === 'accepted' ? 'text-emerald-600 dark:text-[#93F514]' : 'text-rose-600 dark:text-rose-400') : 'text-slate-400 dark:text-[#93A5C9]' }}">
                                {{ strtolower($status) === 'accepted' ? __('Diterima') : (strtolower($status) === 'rejected' ? __('Ditolak') : __('Hasil Akhir')) }}
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
                        $isTodayApp = $intDate->isToday();
                        $isTomorrowApp = $intDate->isTomorrow();
                        $diffAppText = $isTodayApp ? __('Hari Ini') : ($isTomorrowApp ? __('Besok') : null);
                        $isOnlineInt = !empty($appActiveInterview->meeting_link) || str_contains(strtolower($appActiveInterview->location ?? ''), 'online');
                        $appStatusText = match($appActiveInterview->status) {
                            'Scheduled' => __('Terjadwal'),
                            'Rescheduled' => __('Jadwal Ulang'),
                            default => $appActiveInterview->status,
                        };
                    @endphp
                    <div class="rounded-2xl p-3.5 sm:p-4 bg-gradient-to-br from-blue-50/80 via-slate-50/60 to-white dark:from-[#14203A] dark:via-[#111B32] dark:to-[#0D1527] border border-blue-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] shadow-xs space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0 flex-1">
                                <div class="w-9 h-9 rounded-xl bg-blue-600 dark:bg-[#93F514] text-white dark:text-black flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="space-y-1 min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white">
                                            {{ __('Jadwal Wawancara') }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100/80 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            {{ $appStatusText }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $isOnlineInt ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-[#1D2E54] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}">
                                            {{ $isOnlineInt ? __('Online Video') : __('Tatap Muka') }}
                                        </span>
                                        @if ($diffAppText)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30 animate-pulse">
                                                {{ $diffAppText }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $intDate->translatedFormat('l, d F Y • H:i') }} WIB</span>
                                    </p>
                                    <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-[11px] text-slate-500 dark:text-[#93A5C9] pt-0.5">
                                        <span class="inline-flex items-center gap-1 min-w-0">
                                            @if ($isOnlineInt)
                                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                                <span class="truncate">{{ __('Tautan video meeting telah tersedia') }}</span>
                                            @else
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="break-words">{{ $appActiveInterview->location }}</span>
                                            @endif
                                        </span>
                                        @if ($appActiveInterview->user)
                                            <span class="inline-flex items-center gap-1 shrink-0">
                                                <span>•</span>
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span>{{ $appActiveInterview->user->name }}</span>
                                            </span>
                                        @endif
                                    </div>
                                    @if ($appActiveInterview->notes)
                                        <p class="text-[11px] text-amber-700 dark:text-amber-300 italic pt-1 border-t border-blue-100/60 dark:border-[#1D2E54]/60 break-words">
                                            <strong>{{ __('Catatan:') }}</strong> {{ $appActiveInterview->notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            @if ($isOnlineInt && $appActiveInterview->meeting_link)
                                <div class="pt-1 sm:pt-0 shrink-0 w-full sm:w-auto">
                                    <a href="{{ $appActiveInterview->meeting_link }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-xs transition active:scale-[0.99] text-center">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ __('Buka Link Meeting') }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Bottom Row: Notes & Detail Button -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-100 dark:border-[#1D2E54]">
                    <div class="min-w-0 flex-1">
                        @if ($app->notes)
                            <p class="text-xs text-slate-600 dark:text-slate-300 break-words line-clamp-2">
                                <strong class="text-slate-800 dark:text-white">{{ __('Catatan:') }}</strong> {{ $app->notes }}
                            </p>
                        @elseif ($appActiveInterview)
                            <p class="text-xs text-emerald-600 dark:text-[#93F514] font-medium flex items-center gap-1.5 truncate">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ __('Harap hadir tepat waktu sesuai jadwal wawancara di atas.') }}</span>
                            </p>
                        @elseif ($app->interviewSchedules && $app->interviewSchedules->isNotEmpty())
                            @php $latestInterview = $app->interviewSchedules->last(); @endphp
                            <p class="text-xs text-blue-600 dark:text-[#93F514] font-semibold flex items-center gap-1.5 truncate">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ __('Riwayat Wawancara:') }} {{ \Carbon\Carbon::parse($latestInterview->interview_date)->translatedFormat('d M Y, H:i') }} ({{ $latestInterview->status }})</span>
                            </p>
                        @else
                            <p class="text-xs text-slate-400 dark:text-[#93A5C9] truncate">
                                {{ __('Belum ada catatan tambahan dari tim rekruter.') }}
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-wrap sm:flex-nowrap items-center justify-between sm:justify-end gap-2 shrink-0 w-full sm:w-auto">
                        @php
                            $appTestProgress = $testProgressMap[$app->id] ?? null;
                            $singleTest      = $app->job && $app->job->tests ? $app->job->tests->first() : null;
                            $latestSingleAttempt = $singleTest
                                ? ($app->testAttempts ? $app->testAttempts->where('test_id', $singleTest->id)->sortByDesc('id')->first() : null)
                                : null;
                            $canTakeTest = in_array(strtolower($status), ['reviewed', 'shortlisted', 'interview', 'accepted']);
                        @endphp

                        {{-- CASE A: Multiple tests → tampilkan stepper --}}
                        @if ($appTestProgress && count($appTestProgress) > 1)
                            @if (!in_array(strtolower($status), ['rejected']))
                                <div class="w-full mt-1">
                                    <p class="text-[10px] font-bold uppercase text-slate-400 dark:text-[#93A5C9] mb-2 tracking-wider">{{ __('Tahapan Tes Online') }}</p>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @foreach ($appTestProgress as $idx => $step)
                                            @php
                                                $isTestDisc = str_contains(strtolower($step['test']->category?->name ?? ''), 'disc');
                                                $testLabel  = $step['test']->title;
                                            @endphp

                                            {{-- Connector line between steps --}}
                                            @if ($idx > 0)
                                                <div class="flex-1 h-px bg-slate-200 dark:bg-[#1D2E54] min-w-[12px] max-w-[24px]"></div>
                                            @endif

                                            {{-- Step item --}}
                                            @if ($step['status'] === 'completed')
                                                <a href="{{ route('applicant.test', ['applicationId' => $app->id, 'testId' => $step['test']->id]) }}"
                                                    title="{{ $testLabel }} — {{ __('Selesai') }}"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-[#93F514] text-[10px] font-bold whitespace-nowrap transition hover:bg-emerald-100 dark:hover:bg-emerald-900/50 shrink-0">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    <span class="max-w-[80px] truncate">{{ $testLabel }}</span>
                                                </a>
                                            @elseif ($step['status'] === 'in_progress')
                                                <a href="{{ route('applicant.test', ['applicationId' => $app->id, 'testId' => $step['test']->id]) }}"
                                                    title="{{ $testLabel }} — {{ __('Sedang dikerjakan') }}"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-blue-600 dark:bg-blue-700 border border-blue-700 dark:border-blue-600 text-white text-[10px] font-bold whitespace-nowrap transition hover:bg-blue-700 shrink-0 animate-pulse">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span class="max-w-[80px] truncate">{{ $testLabel }}</span>
                                                </a>
                                            @elseif ($step['status'] === 'unlocked' && $canTakeTest)
                                                <a href="{{ route('applicant.test', ['applicationId' => $app->id, 'testId' => $step['test']->id]) }}"
                                                    title="{{ $testLabel }} — {{ __('Kerjakan sekarang') }}"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-600 dark:bg-[#93F514] border border-emerald-700 dark:border-[#93F514] text-white dark:text-black text-[10px] font-bold whitespace-nowrap transition hover:bg-emerald-500 dark:hover:bg-[#82dc12] shrink-0">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="max-w-[80px] truncate">{{ $testLabel }}</span>
                                                </a>
                                            @elseif ($step['status'] === 'unlocked' && !$canTakeTest)
                                                <div title="{{ $testLabel }} — {{ __('Menunggu seleksi berkas') }}"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-amber-600 dark:text-amber-300 text-[10px] font-bold whitespace-nowrap shrink-0">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="max-w-[80px] truncate">{{ $testLabel }}</span>
                                                </div>
                                            @else
                                                {{-- Locked --}}
                                                <div title="{{ $testLabel }} — {{ __('Selesaikan tes sebelumnya terlebih dahulu') }}"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-400 dark:text-[#93A5C9] text-[10px] font-bold whitespace-nowrap shrink-0 cursor-not-allowed opacity-70">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    <span class="max-w-[80px] truncate">{{ $testLabel }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        {{-- CASE B: Single test → tombol seperti semula --}}
                        @elseif ($singleTest && !in_array(strtolower($status), ['rejected']))
                            @if ($latestSingleAttempt && $latestSingleAttempt->status !== 'in_progress')
                                @php $isTestDisc = str_contains(strtolower($singleTest->category?->name ?? ''), 'disc'); @endphp
                                <a href="{{ route('applicant.test', ['applicationId' => $app->id, 'testId' => $singleTest->id]) }}"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-[#93F514] text-xs font-semibold rounded-xl transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $isTestDisc ? __('Lihat Status Tes') : __('Lihat Hasil Tes') }}</span>
                                </a>
                            @elseif ($canTakeTest || ($latestSingleAttempt && $latestSingleAttempt->status === 'in_progress'))
                                <a href="{{ route('applicant.test', ['applicationId' => $app->id, 'testId' => $singleTest->id]) }}"
                                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs rounded-xl shadow-xs transition text-center">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>{{ $latestSingleAttempt ? __('Lanjutkan Ujian') : __('Mulai Ujian Online') }}</span>
                                </a>
                            @else
                                <div class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-amber-700 dark:text-amber-300 text-xs font-medium rounded-xl text-center" title="{{ __('Ujian online akan terbuka setelah berkas lamaran Anda selesai diverifikasi & disetujui tim HR.') }}">
                                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <span class="truncate">{{ __('Ujian: Menunggu Berkas') }}</span>
                                </div>
                            @endif
                        @endif

                        <button wire:click="openDetail({{ $app->id }})"
                            class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2 border border-slate-200/80 dark:border-[#1D2E54] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#14203A] text-xs font-semibold rounded-xl transition shadow-2xs text-center active:scale-95">
                            <svg class="w-4 h-4 shrink-0 text-slate-500 dark:text-[#93A5C9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>{{ __('Lihat Detail') }}</span>
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
                    {{ !empty($search) || $statusFilter !== 'all' ? __('Tidak ada lamaran yang sesuai dengan filter pencarian.') : __('Belum ada data untuk ditampilkan') }}
                </p>
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-emerald-600 dark:border-[#93F514] text-emerald-700 dark:text-[#93F514] hover:bg-emerald-50 dark:hover:bg-[#93F514]/10 text-sm font-bold rounded-2xl transition shadow-2xs">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>{{ __('Jelajahi Lowongan Kerja') }}</span>
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
                    <div class="px-4 py-4 sm:px-6 sm:py-5 border-b border-slate-200/80 dark:border-[#1D2E54] flex items-center justify-between bg-slate-50/50 dark:bg-[#14203A]/50">
                        <div class="flex items-center gap-3 min-w-0 flex-1 pr-2">
                            @if ($selectedApplication->job && $selectedApplication->job->company && $selectedApplication->job->company->logo)
                                <img src="{{ \Illuminate\Support\Str::startsWith($selectedApplication->job->company->logo, ['http://', 'https://']) ? $selectedApplication->job->company->logo : asset('storage/' . $selectedApplication->job->company->logo) }}" alt="{{ $selectedApplication->job->company->name }}"
                                    class="w-10 h-10 rounded-xl object-contain bg-white border border-slate-200 dark:border-slate-700 p-1 shrink-0 shadow-xs">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 dark:border-slate-700 text-blue-600 font-bold text-sm flex items-center justify-center shrink-0 shadow-xs">
                                    {{ strtoupper(substr($selectedApplication->job->company->name ?? 'J', 0, 2)) }}
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate">
                                    {{ $selectedApplication->job->title ?? __('Detail Lamaran') }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-[#93A5C9] truncate">
                                    {{ $selectedApplication->job->company->name ?? '-' }} • {{ $selectedApplication->job->department->name ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeDetail" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-4 sm:p-6 space-y-5 sm:space-y-6 max-h-[75vh] overflow-y-auto">
                        <!-- Summary Info Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div class="p-3 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-100 dark:border-[#1D2E54]">
                                <span class="block text-[10px] font-bold text-slate-400 dark:text-[#93A5C9] uppercase">{{ __('Status Terkini') }}</span>
                                <span class="block text-xs font-bold text-blue-600 dark:text-[#93F514] mt-0.5">
                                    {{ $selectedApplication->status_label ?? $selectedApplication->status }}
                                </span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-100 dark:border-[#1D2E54]">
                                <span class="block text-[10px] font-bold text-slate-400 dark:text-[#93A5C9] uppercase">{{ __('Tanggal Melamar') }}</span>
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                                    {{ \Carbon\Carbon::parse($selectedApplication->applied_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                                </span>
                            </div>
                            <div class="p-3 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-100 dark:border-[#1D2E54] col-span-2 sm:col-span-1">
                                <span class="block text-[10px] font-bold text-slate-400 dark:text-[#93A5C9] uppercase">{{ __('Lokasi Penempatan') }}</span>
                                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                                    {{ $selectedApplication->job->location ?? __('Tidak ditentukan') }}
                                </span>
                            </div>
                        </div>

                        <!-- Section: Riwayat Perubahan Status (Horizontal Timeline Tracker) -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ __('Riwayat Perubahan Status Seleksi') }}</span>
                            </h4>

                            @if ($selectedApplication->statusHistories && $selectedApplication->statusHistories->isNotEmpty())
                                <div class="overflow-x-auto pb-3 pt-1 scrollbar-thin">
                                    <div class="flex items-start gap-4 min-w-max">
                                        @foreach ($selectedApplication->statusHistories->sortBy('changed_at') as $index => $hist)
                                            <div class="flex items-start gap-3">
                                                <div class="w-64 p-3.5 bg-slate-50 dark:bg-[#14203A] rounded-2xl border border-slate-100 dark:border-[#1D2E54] space-y-1.5 shadow-2xs relative">
                                                    <div class="flex items-center justify-between">
                                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-blue-50 dark:bg-[#0D1527] text-blue-700 dark:text-[#93F514] border border-blue-200 dark:border-[#1D2E54]">
                                                            {{ $hist->status_label ?? $hist->status }}
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
                                                            {{ __('Status diperbarui.') }}
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
                                    {{ __('Belum ada log riwayat perubahan status tercatat.') }}
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
                                    <span>{{ __('Jadwal & Agenda Wawancara') }}</span>
                                </h4>
                                <div class="space-y-3">
                                    @foreach ($selectedApplication->interviewSchedules as $interview)
                                        @php
                                            $isModalOnline = !empty($interview->meeting_link) || str_contains(strtolower($interview->location ?? ''), 'online');
                                            $isModalActive = in_array($interview->status, ['Scheduled', 'Rescheduled']);
                                            $intModalDate = \Carbon\Carbon::parse($interview->interview_date);
                                            $modalStatusLabel = match($interview->status) {
                                                'Scheduled' => __('Terjadwal'),
                                                'Rescheduled' => __('Jadwal Ulang'),
                                                'Completed' => __('Selesai'),
                                                'Cancelled' => __('Dibatalkan'),
                                                default => $interview->status,
                                            };
                                        @endphp
                                        <div class="p-3.5 sm:p-4 rounded-2xl border {{ $isModalActive ? 'bg-gradient-to-br from-blue-50/70 via-slate-50/50 to-white dark:from-[#14203A] dark:via-[#111B32] dark:to-[#0D1527] border-blue-200 dark:border-[#1D2E54] ring-2 ring-blue-500/10' : 'bg-slate-50 dark:bg-[#14203A] border-slate-100 dark:border-[#1D2E54]' }} space-y-3">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 sm:gap-2">
                                                <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>{{ $intModalDate->translatedFormat('l, d F Y • H:i') }} WIB</span>
                                                </span>
                                                <div class="flex items-center gap-1.5 shrink-0">
                                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded-md {{ $isModalOnline ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-[#1D2E54] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}">
                                                        {{ $isModalOnline ? __('Online Video') : __('Tatap Muka') }}
                                                    </span>
                                                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full {{ $interview->status === 'Completed' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-[#93F514]' : ($isModalActive ? 'bg-blue-600 text-white dark:bg-[#93F514] dark:text-black' : 'bg-slate-200 dark:bg-[#0D1527] text-slate-700 dark:text-slate-300') }}">
                                                        {{ $modalStatusLabel }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="text-xs text-slate-700 dark:text-slate-300 space-y-2 bg-white/70 dark:bg-[#0D1527]/70 p-3 rounded-xl border border-slate-100 dark:border-[#1D2E54]">
                                                <div class="flex items-start gap-2">
                                                    <span class="text-slate-400 dark:text-[#93A5C9] shrink-0 font-medium min-w-[70px] sm:min-w-[80px]">{{ __('Lokasi') }}:</span>
                                                    <span class="font-semibold text-slate-800 dark:text-white break-words flex-1">{{ $isModalOnline ? __('Online Video Meeting') : $interview->location }}</span>
                                                </div>
                                                <div class="flex items-start gap-2">
                                                    <span class="text-slate-400 dark:text-[#93A5C9] shrink-0 font-medium min-w-[70px] sm:min-w-[80px]">{{ __('Pewawancara') }}:</span>
                                                    <span class="font-semibold text-slate-800 dark:text-white flex-1">{{ $interview->user->name ?? __('Tim HR / Rekruter') }}</span>
                                                </div>
                                                @if ($interview->notes)
                                                    <div class="flex items-start gap-2 pt-2 border-t border-slate-100 dark:border-[#1D2E54]">
                                                        <span class="text-slate-400 dark:text-[#93A5C9] shrink-0 font-medium min-w-[70px] sm:min-w-[80px]">{{ __('Catatan') }}:</span>
                                                        <span class="text-slate-600 dark:text-slate-300 italic break-words flex-1">{{ $interview->notes }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            @if ($interview->meeting_link)
                                                <div class="pt-1">
                                                    <a href="{{ $interview->meeting_link }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs rounded-xl shadow-xs transition active:scale-[0.99] text-center">
                                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                        <span>{{ __('Buka Tautan Video Meeting') }}</span>
                                                    </a>
                                                </div>
                                            @endif
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
                                    <span>{{ __('Tes Rekrutmen Online') }}</span>
                                </h4>
                                <div class="space-y-2">
                                    @foreach ($selectedApplication->testAttempts as $attempt)
                                        @php
                                            $catName = strtolower($attempt->test?->category?->name ?? '');
                                            $testTitle = strtolower($attempt->test?->title ?? '');
                                            $isPsychometric = (bool) ($attempt->is_psychometric ?? false)
                                                || str_contains($catName, 'disc')
                                                || str_contains($catName, 'papi')
                                                || str_contains($testTitle, 'disc')
                                                || str_contains($testTitle, 'papi')
                                                || (bool) ($attempt->discTestResult ?? false)
                                                || (bool) ($attempt->papiTestResult ?? false);
                                        @endphp
                                        <div class="p-3.5 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-100 dark:border-[#1D2E54] flex items-center justify-between">
                                            <div>
                                                <h5 class="text-xs font-bold text-slate-900 dark:text-white">
                                                    {{ $attempt->test->title ?? __('Ujian Tes Online') }}
                                                </h5>
                                                <span class="text-[10px] text-slate-400 dark:text-[#93A5C9]">
                                                    {{ __('Status:') }} {{ ucfirst($attempt->status) }}
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                @if ($isPsychometric)
                                                    <span class="text-xs font-bold text-emerald-600 dark:text-[#93F514]">
                                                        {{ __('Selesai') }}
                                                    </span>
                                                @else
                                                    <span class="text-xs font-extrabold text-blue-600 dark:text-[#93F514]">
                                                        {{ __('Skor:') }} {{ $attempt->total_score ?? ($attempt->objective_score ?? '-') }}
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
                            {{ __('Tutup') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
