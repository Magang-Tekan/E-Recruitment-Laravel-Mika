<div class="space-y-6">

    <!-- 1. Header Banner: "Selamat Datang, [Nama Karyawan]" (Clean Enterprise Blueprint - Non-AI) -->
    <div
        class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-5 sm:p-7 transition-colors">
        
        <!-- Subtle Technical Blueprint Dot Grid (Authentic & Non-AI) -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.06]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 sm:gap-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div
                    class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-blue-50 dark:bg-[#14203A] border border-blue-100 dark:border-[#1D2E54] flex items-center justify-center text-blue-600 dark:text-[#93F514] text-xl sm:text-2xl font-black shadow-sm shrink-0 overflow-hidden">
                    @if ($employeeProfile?->photo)
                        <img src="{{ asset('storage/' . $employeeProfile->photo) }}" alt="Foto profil"
                            class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($employeeProfile?->full_name ?? ($user?->name ?? 'K'), 0, 1)) }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white break-words">
                            Selamat Datang, {{ $employeeProfile?->full_name ?? ($user?->name ?? 'User') }}
                        </h2>
                        @php
                            $typeLabel = match ($employeeProfile?->employee_type) {
                                'internship' => 'Magang',
                                'contract' => 'Kontrak',
                                'probation' => 'Probation',
                                default => 'Karyawan',
                            };
                        @endphp
                        <span
                            class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-[#93F514]/15 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30 whitespace-nowrap">
                            {{ $typeLabel }}
                        </span>
                    </div>
                    <div class="flex items-center gap-x-4 gap-y-1.5 text-xs sm:text-sm text-slate-500 dark:text-[#93A5C9] mt-2 flex-wrap">
                        <span class="flex items-center gap-1 font-medium whitespace-nowrap">
                            <span class="text-slate-400 dark:text-[#7E90B5]">NIK:</span>
                            <span class="text-slate-700 dark:text-slate-200">{{ $employeeProfile?->nik ?? ($user?->nik ?? '-') }}</span>
                        </span>
                        <span class="flex items-center gap-1 font-medium whitespace-nowrap">
                            <span class="text-slate-400 dark:text-[#7E90B5]">Perusahaan:</span>
                            <span class="text-slate-700 dark:text-slate-200">{{ $employeeProfile?->company?->name ?? ($employeeProfile?->department?->company?->name ?? '-') }}</span>
                        </span>
                        <span class="flex items-center gap-1 font-medium whitespace-nowrap">
                            <span class="text-slate-400 dark:text-[#7E90B5]">Divisi:</span>
                            <span class="text-slate-700 dark:text-slate-200">{{ $employeeProfile?->department?->name ?? 'Umum / Seluruh Divisi' }}</span>
                        </span>
                        <span class="flex items-center gap-1 font-medium whitespace-nowrap">
                            <span class="text-slate-400 dark:text-[#7E90B5]">Posisi:</span>
                            <span class="text-slate-700 dark:text-slate-200">{{ $employeeProfile?->position_title ?? 'Karyawan' }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Action or Stat -->
            <div
                class="grid grid-cols-2 sm:flex items-center gap-3 shrink-0 pt-3 lg:pt-0 border-t border-slate-100 dark:border-[#1D2E54] lg:border-t-0">
                <div
                    class="px-4 py-2.5 sm:px-5 sm:py-3 rounded-2xl bg-slate-50 dark:bg-[#14203A] shadow-sm border border-slate-200/80 dark:border-[#1D2E54] text-center min-w-[90px] sm:min-w-[95px]">
                    <span
                        class="block text-lg sm:text-xl font-black text-blue-600 dark:text-[#93F514] leading-tight">{{ $tests->count() }}</span>
                    <span class="text-[10px] sm:text-[11px] text-slate-500 dark:text-[#93A5C9] font-bold uppercase tracking-wider">Tugas Ujian</span>
                </div>
                <div
                    class="px-4 py-2.5 sm:px-5 sm:py-3 rounded-2xl bg-slate-50 dark:bg-[#14203A] shadow-sm border border-slate-200/80 dark:border-[#1D2E54] text-center min-w-[90px] sm:min-w-[95px]">
                    <span
                        class="block text-lg sm:text-xl font-black text-emerald-600 dark:text-[#93F514] leading-tight">{{ $attempts->where('status', '!=', 'in_progress')->count() }}</span>
                    <span
                        class="text-[10px] sm:text-[11px] text-slate-500 dark:text-[#93A5C9] font-bold uppercase tracking-wider">Selesai</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab View: Tugas Asesmen (muncul di tab 'dashboard' atau tab 'asesmen') -->
    <div x-show="activeTab === 'dashboard' || activeTab === 'asesmen'" class="space-y-6" x-cloak>

        <!-- 2. Kartu "Tugas Asesmen Tersedia" -->
        <div
            class="bg-white dark:bg-[#0D1527] rounded-2xl border border-gray-100 dark:border-[#1D2E54] shadow-sm p-4 sm:p-6">
            <div
                class="flex flex-row items-center justify-between gap-3 pb-4 mb-4 border-b border-gray-100 dark:border-[#1D2E54]">
                <div class="min-w-0">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-[#93F514] shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span class="truncate">Tugas Asesmen Tersedia</span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-[#93A5C9] mt-0.5">
                        Daftar paket ujian dan asesmen kompetensi yang ditugaskan untuk divisi Anda.
                    </p>
                </div>
                <span
                    class="px-2.5 sm:px-3 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 dark:bg-[#93F514]/15 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30 whitespace-nowrap shrink-0">
                    {{ $tests->count() }} Paket
                </span>
            </div>

            @if ($tests->isEmpty())
                <div class="text-center py-10">
                    <div
                        class="w-14 h-14 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-[#14203A] border border-transparent dark:border-[#1D2E54] flex items-center justify-center text-gray-400 dark:text-slate-400">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Belum Ada Tugas Asesmen</h4>
                    <p class="text-xs text-gray-500 dark:text-[#93A5C9] mt-1 max-w-sm mx-auto">
                        Saat ini belum ada paket asesmen aktif yang ditugaskan untuk departemen Anda.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @foreach ($tests as $test)
                        @php
                            $attempt = $attemptsByTestId->get($test->id);
                            $isCompleted = $attempt && in_array($attempt->status, ['completed', 'passed', 'failed']);
                            $isInProgress = $attempt && $attempt->status === 'in_progress';
                            $isDisc = str_contains(strtolower($test->category?->name ?? ''), 'disc');
                            $isPapi = str_contains(strtolower($test->category?->name ?? ''), 'papi');
                        @endphp
                        <div
                            class="p-4 sm:p-5 rounded-2xl border transition-all duration-200 flex flex-col justify-between {{ $isCompleted ? 'bg-gray-50/70 dark:bg-[#14203A]/40 border-gray-200 dark:border-[#1D2E54] opacity-90' : 'bg-white dark:bg-[#0D1527] border-gray-200 dark:border-[#1D2E54] hover:shadow-lg hover:shadow-blue-500/5 hover:border-indigo-300 dark:hover:border-[#93F514]/40' }}">
                            <div>
                                <div class="flex items-start justify-between gap-2 flex-wrap mb-3">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $isDisc ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60' : ($isPapi ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60' : 'bg-indigo-100 text-indigo-700 dark:bg-[#14203A] dark:text-[#38BDF8] border border-indigo-200 dark:border-[#253966]') }}">
                                        @if ($isDisc)
                                            Tes Psikologi (DISC)
                                        @elseif ($isPapi)
                                            Tes Kepribadian (PAPI Kostick)
                                        @else
                                            {{ $test->category?->name ?? 'Pilihan Ganda / Essay' }}
                                        @endif
                                    </span>

                                    @if ($isCompleted)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-[#93F514]/15 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Sudah Dikerjakan
                                        </span>
                                    @elseif ($isInProgress)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 animate-pulse">
                                            Sedang Dikerjakan
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800/60">
                                            Belum Dikerjakan
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-base font-bold text-gray-900 dark:text-white mb-3 leading-snug">
                                    {{ $test->title }}
                                </h4>

                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs mb-4 py-2.5 px-3 rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54]">
                                    <div class="flex items-center gap-2 text-gray-600 dark:text-[#93A5C9]">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-[#6378A0] shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Durasi: <strong
                                                class="text-gray-900 dark:text-white font-bold">{{ $test->duration_minutes }}
                                                Menit</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-600 dark:text-[#93A5C9]">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-[#6378A0] shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Soal: <strong
                                                class="text-gray-900 dark:text-white font-bold">{{ $test->total_questions ?? $test->questions->count() }}
                                                Butir</strong></span>
                                    </div>
                                    @if (!$isDisc && !$isPapi)
                                        <div
                                            class="flex items-center gap-2 text-gray-600 dark:text-[#93A5C9] sm:col-span-2 pt-1 border-t border-gray-200/60 dark:border-[#1D2E54]">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-[#6378A0] shrink-0" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>

                                            <span>Passing Grade: <strong
                                                    class="text-gray-900 dark:text-white font-bold">{{ $test->passing_score }}
                                                    Poin</strong></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Tombol aksi -->
                            <div class="pt-2">
                                @if ($isCompleted)
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        @if ($isDisc || $isPapi)
                                            <span
                                                class="inline-flex items-center gap-1.5 text-xs text-emerald-600 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/15 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-[#93F514]/30">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Jawaban Anda Telah Tersimpan
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-500 dark:text-[#93A5C9] font-medium">
                                                Nilai: <strong
                                                    class="text-gray-900 dark:text-white font-bold">{{ $attempt->total_score ?? '-' }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @elseif ($isInProgress)
                                    <a href="{{ route('employee.test', $test->id) }}"
                                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-transform active:scale-[0.99]">
                                        <svg class="w-5 h-5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Lanjutkan Pengerjaan Ujian
                                    </a>
                                @else
                                    <a href="{{ route('employee.test', $test->id) }}"
                                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all hover:scale-[1.01] active:scale-[0.99]">
                                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Mulai Kerjakan Asesmen
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- 3. Riwayat Asesmen yang Sudah Pernah Dikerjakan (muncul di tab 'dashboard' atau tab 'riwayat') -->
    <div x-show="activeTab === 'dashboard' || activeTab === 'riwayat'" class="space-y-6" x-cloak>
        <div
            class="bg-white dark:bg-[#0D1527] rounded-2xl border border-gray-100 dark:border-[#1D2E54] shadow-sm p-4 sm:p-6">
            <div
                class="flex flex-row items-center justify-between gap-3 pb-4 mb-4 border-b border-gray-100 dark:border-[#1D2E54]">
                <div class="min-w-0">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-[#93F514] shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="truncate">Riwayat Asesmen</span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-[#93A5C9] mt-0.5">
                        Daftar riwayat pengerjaan, nilai akhir, dan hasil evaluasi kompetensi.
                    </p>
                </div>
                <span
                    class="px-2.5 sm:px-3 py-1 text-xs font-bold rounded-full bg-indigo-50 text-indigo-700 dark:bg-[#14203A] dark:text-[#93A5C9] border border-indigo-200 dark:border-[#1D2E54] whitespace-nowrap shrink-0">
                    {{ $attempts->count() }} Riwayat
                </span>
            </div>

            @if ($attempts->isEmpty())
                <div class="text-center py-8">
                    <p class="text-xs text-gray-500 dark:text-[#93A5C9]">
                        Anda belum memiliki riwayat pengerjaan asesmen.
                    </p>
                </div>
            @else
                <!-- Mobile Card List (tampil di layar kecil < sm) -->
                <div class="space-y-3 sm:hidden">
                    @foreach ($attempts as $attempt)
                        @php
                            $isDisc = str_contains(strtolower($attempt->test?->category?->name ?? ''), 'disc');
                            $isPapi = str_contains(strtolower($attempt->test?->category?->name ?? ''), 'papi');
                        @endphp
                        <div
                            class="p-4 rounded-xl bg-gray-50/80 dark:bg-[#14203A] border border-gray-200/80 dark:border-[#1D2E54] space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <h4
                                        class="text-sm font-bold text-gray-900 dark:text-white leading-snug break-words">
                                        {{ $attempt->test?->title ?? 'Ujian' }}
                                    </h4>
                                    <span
                                        class="inline-block mt-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-white dark:bg-[#0D1527] text-gray-700 dark:text-[#DDE5F5] border border-gray-200 dark:border-[#1D2E54]">
                                        {{ $attempt->test?->category?->name ?? 'Umum' }}
                                    </span>
                                </div>
                                <div class="shrink-0">
                                    @if ($attempt->status === 'passed')
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-[#93F514]/15 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30">
                                            Lolos
                                        </span>
                                    @elseif ($attempt->status === 'failed')
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50">
                                            Tidak Lolos
                                        </span>
                                    @elseif ($attempt->status === 'completed')
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50">
                                            Selesai
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50">
                                            Sedang Dikerjakan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between text-xs pt-1.5 border-t border-gray-200/60 dark:border-[#1D2E54]">
                                <span class="text-gray-500 dark:text-[#93A5C9]">
                                    {{ $attempt->finished_at ? $attempt->finished_at->translatedFormat('d M Y, H:i') : ($attempt->started_at ? $attempt->started_at->translatedFormat('d M Y, H:i') : '-') }}
                                </span>
                                <div class="text-right font-bold text-gray-900 dark:text-white">
                                    @if ($isDisc || $isPapi)
                                        <span class="text-xs font-semibold text-emerald-600 dark:text-[#93F514]">
                                            Tersimpan
                                        </span>
                                    @else
                                        Skor: {{ $attempt->total_score ?? ($attempt->objective_score ?? '-') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table View (tampil di layar >= sm) -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr
                                class="border-b border-gray-100 dark:border-[#1D2E54] text-xs font-semibold text-gray-400 dark:text-[#93A5C9] uppercase">
                                <th class="py-3 px-3">Nama Paket Asesmen</th>
                                <th class="py-3 px-3">Kategori</th>
                                <th class="py-3 px-3">Tanggal Pengerjaan</th>
                                <th class="py-3 px-3">Skor / Hasil</th>
                                <th class="py-3 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#1D2E54]">
                            @foreach ($attempts as $attempt)
                                @php
                                    $isDisc = str_contains(strtolower($attempt->test?->category?->name ?? ''), 'disc');
                                @endphp
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-[#1A2A4C] transition">
                                    <td class="py-3.5 px-3 font-semibold text-gray-900 dark:text-white">
                                        {{ $attempt->test?->title ?? 'Ujian' }}
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <span
                                            class="px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-[#14203A] text-gray-700 dark:text-[#DDE5F5] border border-transparent dark:border-[#1D2E54]">
                                            {{ $attempt->test?->category?->name ?? 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-xs text-gray-500 dark:text-[#93A5C9]">
                                        {{ $attempt->finished_at ? $attempt->finished_at->translatedFormat('d M Y, H:i') : ($attempt->started_at ? $attempt->started_at->translatedFormat('d M Y, H:i') : '-') }}
                                    </td>
                                    @php
                                        $isDisc = str_contains(strtolower($attempt->test?->category?->name ?? ''), 'disc');
                                        $isPapi = str_contains(strtolower($attempt->test?->category?->name ?? ''), 'papi');
                                    @endphp
                                    <td class="py-3.5 px-3 font-bold text-gray-900 dark:text-white">
                                        @if ($isDisc || $isPapi)
                                            <span class="text-xs font-semibold text-emerald-600 dark:text-[#93F514]">
                                                Tersimpan
                                            </span>
                                        @else
                                            {{ $attempt->total_score ?? ($attempt->objective_score ?? '-') }}
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3">
                                        @if ($attempt->status === 'passed')
                                            <span
                                                class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-[#93F514]/15 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30">
                                                Lolos
                                            </span>
                                        @elseif ($attempt->status === 'failed')
                                            <span
                                                class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50">
                                                Tidak Lolos
                                            </span>
                                        @elseif ($attempt->status === 'completed')
                                            <span
                                                class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50">
                                                Selesai
                                            </span>
                                        @else
                                            <span
                                                class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50">
                                                Sedang Dikerjakan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- 4. Tab Pengaturan Sandi & Profil -->
    <div x-show="activeTab === 'pengaturan'" class="space-y-6" x-cloak>
        <livewire:employee.employee-profile-settings />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                class="p-6 bg-white dark:bg-[#0D1527] shadow-sm border border-gray-100 dark:border-[#1D2E54] rounded-2xl">
                <livewire:profile.update-profile-information-form />
            </div>
            <div
                class="p-6 bg-white dark:bg-[#0D1527] shadow-sm border border-gray-100 dark:border-[#1D2E54] rounded-2xl">
                <livewire:profile.update-password-form />
            </div>
        </div>
    </div>

</div>
