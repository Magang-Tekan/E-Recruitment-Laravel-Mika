<div class="space-y-6">
    @php
        $isAdmin = !$isRecruiter;
        $candidateRoute = $isRecruiter ? route('recruiter.candidate') : route('admin.candidate');
        $applicationRoute = $isRecruiter ? route('recruiter.application') : route('admin.application');
    @endphp

    <!-- Welcome Header Card (Clean Enterprise Blueprint - Authentic & Non-AI) -->
    <div
        class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-6 sm:p-7 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 transition-colors">

        <!-- Subtle Technical Blueprint Dot Grid (Authentic & Non-AI) -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.06]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>

        <div class="relative z-10 flex items-start sm:items-center gap-4">
            <div
                class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-[#14203A] border border-blue-100 dark:border-[#1D2E54] text-blue-600 dark:text-[#93F514] flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
            </div>
            <div>
                <h1
                    class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <span>{{ $isRecruiter ? 'Selamat Datang di Panel Recruiter E-Rekrutmen' : 'Selamat Datang di Panel Admin E-Rekrutmen' }}</span>
                    <span class="text-xl">👋</span>
                </h1>
                <p class="text-slate-500 dark:text-[#93A5C9] text-sm mt-1 leading-relaxed max-w-xl">
                    {{ $isRecruiter ? 'Kelola dan seleksi lamaran masuk kandidat pada lowongan yang aktif secara terstruktur.' : 'Pantau perkembangan rekrutmen, pelamar masuk, dan hasil tes online secara real-time.' }}
                </p>
            </div>
        </div>

        <div class="relative z-10 flex items-center gap-2.5 shrink-0 w-full sm:w-auto">
            @if (!$isRecruiter)
                <a href="{{ route('admin.job') }}"
                    class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-slate-950 dark:shadow-[#93F514]/20 font-bold rounded-xl text-sm transition-all flex items-center justify-center gap-1.5 w-full sm:w-auto shrink-0 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                    <span>Pasang Lowongan</span>
                </a>
            @else
                <a href="{{ $candidateRoute }}"
                    class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-slate-950 dark:shadow-[#93F514]/20 font-bold rounded-xl text-sm transition-all flex items-center justify-center gap-1.5 w-full sm:w-auto shrink-0 active:scale-95">
                    <span>Data Kandidat</span>
                </a>
            @endif
            <a href="{{ $applicationRoute }}"
                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1D2E54] border border-slate-200/80 dark:border-[#1D2E54] text-slate-700 dark:text-slate-200 font-semibold rounded-xl text-sm transition shadow-sm flex items-center justify-center gap-1.5 w-full sm:w-auto">
                <span>Seleksi Pelamar</span>
            </a>
        </div>
    </div>

    <!-- Key Metrics Cards Grid (Clickable Shortcuts) -->
    <div class="grid grid-cols-2 sm:grid-cols-2 {{ $isRecruiter ? 'lg:grid-cols-4' : 'lg:grid-cols-5' }} gap-4">
        <!-- Card 1: Lowongan Aktif -->
        <a href="{{ $isRecruiter ? '#' : route('admin.job') }}"
            class="p-4 sm:p-5 bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] hover:border-blue-400 dark:hover:border-blue-500/50 hover:shadow-md transition-all flex items-center gap-3.5 group">
            <div
                class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-sky-400 dark:border dark:border-blue-500/20 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
            <div class="min-w-0">
                <p
                    class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] group-hover:text-blue-600 dark:group-hover:text-sky-400 transition-colors truncate">
                    Lowongan Aktif</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span
                        class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">{{ $activeJobs }}</span>
                    @if (!$isRecruiter)
                        <span class="text-[11px] text-slate-500 dark:text-[#7E90B5] truncate">/ {{ $totalJobs }}
                            total</span>
                    @else
                        <span class="text-[11px] text-emerald-600 dark:text-[#93F514] font-medium truncate">Aktif</span>
                    @endif
                </div>
            </div>
        </a>

        <!-- Card 2: Kandidat -->
        <a href="{{ $candidateRoute }}"
            class="p-4 sm:p-5 bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] hover:border-indigo-400 dark:hover:border-indigo-500/50 hover:shadow-md transition-all flex items-center gap-3.5 group">
            <div
                class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border dark:border-indigo-500/20 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <p
                    class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                    Kandidat</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span
                        class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">{{ $totalCandidates }}</span>
                    <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-medium truncate">Terdaftar</span>
                </div>
            </div>
        </a>

        <!-- Card 3: Pelamar -->
        <a href="{{ $applicationRoute }}"
            class="p-4 sm:p-5 bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] hover:border-emerald-400 dark:hover:border-emerald-500/50 hover:shadow-md transition-all flex items-center gap-3.5 group">
            <div
                class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-[#93F514] dark:border dark:border-emerald-500/20 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
            <div class="min-w-0">
                <p
                    class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] group-hover:text-emerald-600 dark:group-hover:text-[#93F514] transition-colors truncate">
                    Pelamar</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span
                        class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">{{ $totalApplicants }}</span>
                    <span class="text-[11px] text-emerald-600 dark:text-[#93F514] font-medium truncate">Masuk</span>
                </div>
            </div>
        </a>

        <!-- Card 4: Perlu Direview -->
        <a href="{{ $applicationRoute }}"
            class="p-4 sm:p-5 bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] hover:border-amber-400 dark:hover:border-amber-500/50 hover:shadow-md transition-all flex items-center gap-3.5 group">
            <div
                class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 dark:border dark:border-amber-500/20 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="min-w-0">
                <p
                    class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors truncate">
                    Perlu Direview</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span
                        class="text-xl sm:text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $pendingReview }}</span>
                    <span class="text-[11px] text-slate-500 dark:text-[#7E90B5] truncate">Screening</span>
                </div>
            </div>
        </a>

        @if (!$isRecruiter)
            <!-- Card 5: Paket Tes (Admin Only) -->
            <a href="{{ route('admin.test') }}"
                class="p-4 sm:p-5 bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] hover:border-purple-400 dark:hover:border-purple-500/50 hover:shadow-md transition-all flex items-center gap-3.5 group">
                <div
                    class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400 dark:border dark:border-purple-500/20 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p
                        class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9] group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors truncate">
                        Paket Tes</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <span
                            class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">{{ $totalTests }}</span>
                        <span class="text-[11px] text-slate-500 dark:text-[#7E90B5] truncate">({{ $totalQuestions }}
                            Soal)</span>
                    </div>
                </div>
            </a>
        @endif
    </div>

    <!-- Middle Content Grid: Recent Applications & Active Jobs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">

        <!-- Recent Applications Table (2 Columns) -->
        <div
            class="lg:col-span-2 bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] p-5 sm:p-6 flex flex-col justify-between h-full">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#1D2E54]">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Pelamar Terbaru</h2>
                        <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-0.5">
                            {{ $isRecruiter ? 'Daftar pelamar terbaru pada lowongan yang aktif saat ini' : 'Daftar 5 kandidat yang baru saja mengirimkan lamaran' }}
                        </p>
                    </div>
                    <a href="{{ $applicationRoute }}"
                        class="text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-[#93F514] dark:hover:text-[#aef74b] transition flex items-center gap-1 group">
                        <span>Lihat Semua</span>
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>

                <div class="overflow-x-auto overflow-y-auto max-h-[300px] mt-2 custom-scrollbar">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="sticky top-0 bg-white dark:bg-[#0D1527] z-10 shadow-sm">
                            <tr
                                class="text-xs uppercase font-bold text-slate-400 dark:text-[#93A5C9] border-b border-slate-100 dark:border-[#1D2E54]">
                                <th class="py-2.5 px-3 text-[11px] tracking-wider w-[40%]">Kandidat</th>
                                <th class="py-2.5 px-3 text-[11px] tracking-wider w-[24%]">Posisi</th>
                                <th class="py-2.5 px-3 text-[11px] tracking-wider w-[22%]">Tanggal</th>
                                <th class="py-2.5 px-3 text-[11px] tracking-wider text-center w-[14%]">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-[#1D2E54]/60">
                            @forelse($recentApplications as $app)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-[#14203A]/60 transition-colors">
                                    <!-- Kandidat Column -->
                                    <td class="py-3 px-3 align-middle">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 min-w-[32px] min-h-[32px] max-w-[32px] max-h-[32px] rounded-full bg-blue-50 text-blue-700 dark:bg-[#14203A] dark:text-[#93F514] border border-blue-200/60 dark:border-[#253D75] flex items-center justify-center font-bold text-xs uppercase shadow-sm shrink-0">
                                                {{ substr($app->applicantProfile->full_name ?? ($app->applicantProfile->user->name ?? 'A'), 0, 2) }}
                                            </div>
                                            <div class="min-w-0 pr-2">
                                                <span
                                                    class="font-semibold text-xs sm:text-sm text-slate-900 dark:text-white truncate block leading-tight">
                                                    {{ $app->applicantProfile->full_name ?? ($app->applicantProfile->user->name ?? 'Pelamar') }}
                                                </span>
                                                @if (!empty($app->applicantProfile->user->email))
                                                    <span
                                                        class="text-[11px] text-slate-400 dark:text-[#7E90B5] truncate block mt-0.5">
                                                        {{ $app->applicantProfile->user->email }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Posisi Column -->
                                    <td
                                        class="py-3 px-3 align-middle text-xs font-medium text-slate-600 dark:text-[#C5D5F5]">
                                        <span
                                            class="truncate block max-w-[170px]">{{ $app->job->title ?? '-' }}</span>
                                    </td>
                                    <!-- Tanggal Column -->
                                    <td
                                        class="py-3 px-3 align-middle text-xs text-slate-500 dark:text-[#889BC2] whitespace-nowrap">
                                        {{ $app->applied_at ? \Carbon\Carbon::parse($app->applied_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                                    </td>
                                    <!-- Status Column -->
                                    <td class="py-3 px-3 align-middle text-center whitespace-nowrap">
                                        @php
                                            $statusKey = strtolower(trim($app->status ?? ''));
                                            $statusClasses = [
                                                'reviewed' =>
                                                    'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-500/15 dark:text-sky-300 dark:border-sky-500/30',
                                                'interview' =>
                                                    'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:border-amber-500/30',
                                                'shortlisted' =>
                                                    'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-500/15 dark:text-purple-300 dark:border-purple-500/30',
                                                'screening' =>
                                                    'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-500/15 dark:text-violet-300 dark:border-violet-500/30',
                                                'accepted' =>
                                                    'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/30',
                                                'hired' =>
                                                    'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-[#93F514]/15 dark:text-[#93F514] dark:border-[#93F514]/30',
                                                'rejected' =>
                                                    'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/15 dark:text-rose-300 dark:border-rose-500/30',
                                                'applied' =>
                                                    'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/15 dark:text-blue-300 dark:border-blue-500/30',
                                                'submitted' =>
                                                    'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/15 dark:text-blue-300 dark:border-blue-500/30',
                                                'pending' =>
                                                    'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:border-amber-500/30',
                                            ];
                                            $badgeClass =
                                                $statusClasses[$statusKey] ??
                                                'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
                                        @endphp
                                        <span
                                            class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-semibold rounded-full capitalize border {{ $badgeClass }}">
                                            {{ $app->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="py-8 text-center text-sm text-slate-400 dark:text-[#889BC2]">
                                        Belum ada data lamaran masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Lowongan Terbaru (1 Column) -->
        <div class="h-full flex flex-col">
            <div
                class="bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] p-5 sm:p-6 flex flex-col justify-between h-full">
                <div>
                    <div
                        class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#1D2E54]">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Lowongan Terbaru</h3>
                            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-0.5">Daftar lowongan yang aktif</p>
                        </div>
                        @if ($isAdmin)
                            <a href="{{ route('admin.job') }}"
                                class="text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-[#93F514] dark:hover:text-[#aef74b] transition flex items-center gap-1 group">
                                <span>Lihat</span>
                                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </a>
                        @endif
                    </div>
                    <div
                        class="mt-2 divide-y divide-slate-100 dark:divide-[#1D2E54]/60 overflow-y-auto max-h-[300px] custom-scrollbar pr-1">
                        @forelse($recentJobs as $job)
                            <div class="py-2.5 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <h4
                                        class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white truncate">
                                        {{ $job->title }}
                                    </h4>
                                    <p class="text-[11px] text-slate-500 dark:text-[#93A5C9] truncate mt-0.5">
                                        {{ $job->department->name ?? 'Dept' }} &bull; Kuota: {{ $job->quota ?? '-' }}
                                    </p>
                                </div>
                                <span
                                    class="px-2 py-1 text-[11px] font-bold bg-blue-50 text-blue-700 dark:bg-sky-500/15 dark:text-sky-300 dark:border dark:border-sky-500/30 rounded-lg shrink-0 whitespace-nowrap">
                                    {{ $job->job_applications_count }} Pelamar
                                </span>
                            </div>
                        @empty
                            <p class="py-6 text-center text-xs text-slate-400 dark:text-[#889BC2]">Belum ada lowongan.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
