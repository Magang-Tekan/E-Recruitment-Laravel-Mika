@props([
    'activeTab' => 'pribadi',
])

<div>
    <!-- Off-canvas / Mobile backdrop -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-gray-900/50 dark:bg-gray-950/80 backdrop-blur-sm lg:hidden" x-cloak></div>

    <!-- Main Sidebar Container -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed top-0 left-0 z-50 h-screen w-64 bg-white dark:bg-[#0D1527] border-r border-gray-200 dark:border-[#1D2E54] text-gray-700 dark:text-gray-300 transition-transform duration-300 ease-in-out flex flex-col justify-between shadow-xl">

        <!-- Top Section: Brand Header & Navigation -->
        <div class="flex-1 overflow-y-auto px-4 py-5 custom-scrollbar">

            <!-- Brand Logo Header -->
            <div class="flex items-center justify-between pb-5 mb-3 px-2">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/mikaaaa.png') }}" alt="Logo MIKA"
                        class="h-8 w-auto object-contain rounded-lg group-hover:scale-105 transition-transform duration-200">
                    <div>
                        <span
                            class="block font-bold text-lg tracking-tight text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-[#93F514] transition-colors">E-Rekrutmen</span>
                        <span
                            class="block text-[10px] font-semibold tracking-widest text-emerald-600 dark:text-[#93F514] uppercase">
                            Panel Pelamar
                        </span>
                    </div>
                </a>

                <!-- Mobile Close Button -->
                <button @click="sidebarOpen = false"
                    class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#14203A]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @php
                $applicantProfile = auth()->user()?->applicantProfile;
                $completionPercentage = $applicantProfile ? $applicantProfile->completion_percentage : 0;
                $mandatoryPercentage = $applicantProfile ? $applicantProfile->mandatory_completion_percentage : 0;
                $isMandatoryComplete = $applicantProfile ? $applicantProfile->is_mandatory_complete : false;
                $missingMandatorySections = $applicantProfile ? $applicantProfile->missing_mandatory_sections : [];
                $sectionStatuses = $applicantProfile ? $applicantProfile->section_statuses : [];
                $hasUpcomingInterview = false;
                if ($applicantProfile) {
                    $hasUpcomingInterview = \App\Models\InterviewSchedule::whereHas('jobApplication', function (
                        $q,
                    ) use ($applicantProfile) {
                        $q->where('profile_id', $applicantProfile->id);
                    })
                        ->whereIn('status', ['Scheduled', 'Rescheduled'])
                        ->where('interview_date', '>=', now()->subHours(4))
                        ->exists();
                }
            @endphp

            <!-- Navigation Sections -->
            <nav class="space-y-6">

                <!-- Section 1: Data Profil -->
                <div>
                    <div
                        class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 dark:text-[#6378A0] uppercase flex items-center justify-between">
                        <span>Data Profil</span>
                    </div>

                    <!-- Profile Completeness Progress Bar Card -->
                    <div class="px-1 mb-3">
                        <div
                            class="p-3 bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl shadow-2xs">
                            <div
                                class="flex items-center justify-between text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">
                                <span>Kelengkapan Profil</span>
                                <span class="text-emerald-600 dark:text-[#93F514] font-extrabold">{{ $completionPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-[#0D1527] rounded-full h-2 overflow-hidden mb-2">
                                <div class="bg-emerald-500 dark:bg-[#93F514] h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $completionPercentage }}%"></div>
                            </div>
                            <div class="text-[10px] font-medium leading-snug">
                                @if ($completionPercentage == 100)
                                    <div
                                        class="p-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-lg text-emerald-700 dark:text-[#93F514] font-bold flex items-start gap-1.5">
                                        <span>Profilmu sudah 100% lengkap! Yuk, kirim lamaran sekarang dan buat rekruter
                                            melirik.</span>
                                    </div>
                                @elseif ($isMandatoryComplete)
                                    <div
                                        class="p-2 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-lg text-emerald-700 dark:text-[#93F514] font-bold flex items-start gap-1.5">
                                        <span>Profil kamu sudah cukup. Silakan kirimkan lamaran Anda sekarang
                                            juga!</span>
                                    </div>
                                @else
                                    <div
                                        class="p-2 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-lg text-amber-800 dark:text-amber-300">
                                        <div
                                            class="font-bold flex items-center gap-1 text-amber-700 dark:text-amber-400 mb-0.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            Data Wajib {{ $mandatoryPercentage }}%
                                        </div>
                                        <p class="text-[9.5px] opacity-90">
                                            Lengkapi: <span
                                                class="font-bold text-rose-600 dark:text-rose-400">{{ implode(', ', $missingMandatorySections) }}</span>
                                            agar bisa melamar.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <!-- 1. Data Pribadi -->
                        <x-sidebar.single-nav-link :href="route('profile', ['tab' => 'pribadi'])" tab="pribadi"
                            x-on:click.prevent="activeTab = 'pribadi'; $dispatch('switch-tab', 'pribadi')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                </svg>
                            </x-slot:icon>
                            Data Pribadi
                            @if (!empty($sectionStatuses['pribadi']) && !empty($sectionStatuses['cv']))
                                <x-slot:append>
                                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </x-slot:append>
                            @endif
                        </x-sidebar.single-nav-link>

                        <!-- Data Keluarga -->
                        <x-sidebar.single-nav-link :href="route('profile', ['tab' => 'keluarga'])" tab="keluarga"
                            x-on:click.prevent="activeTab = 'keluarga'; $dispatch('switch-tab', 'keluarga')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </x-slot:icon>
                            Data Keluarga
                            @if (!empty($sectionStatuses['keluarga']))
                                <x-slot:append>
                                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </x-slot:append>
                            @endif
                        </x-sidebar.single-nav-link>

                        <!-- 2. Pendidikan -->
                        <x-sidebar.single-nav-link :href="route('profile', ['tab' => 'pendidikan'])" tab="pendidikan"
                            x-on:click.prevent="activeTab = 'pendidikan'; $dispatch('switch-tab', 'pendidikan')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            </x-slot:icon>
                            Pendidikan
                            @if (!empty($sectionStatuses['pendidikan']))
                                <x-slot:append>
                                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </x-slot:append>
                            @endif
                        </x-sidebar.single-nav-link>

                        <!-- 3. Pengalaman Kerja & Minat Kerja -->
                        <x-sidebar.single-nav-link :href="route('profile', ['tab' => 'pengalaman'])" tab="pengalaman"
                            x-on:click.prevent="activeTab = 'pengalaman'; $dispatch('switch-tab', 'pengalaman')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </x-slot:icon>
                            Pengalaman Kerja
                            @if (!empty($sectionStatuses['pengalaman']))
                                <x-slot:append>
                                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </x-slot:append>
                            @endif
                        </x-sidebar.single-nav-link>

                        <!-- 4. Organisasi & Prestasi -->
                        <x-sidebar.single-nav-link :href="route('profile', ['tab' => 'prestasi'])" tab="prestasi"
                            x-on:click.prevent="activeTab = 'prestasi'; $dispatch('switch-tab', 'prestasi')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                </svg>

                            </x-slot:icon>
                            Organisasi & Prestasi
                            @if (!empty($sectionStatuses['prestasi']) || !empty($sectionStatuses['organisasi']))
                                <x-slot:append>
                                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </x-slot:append>
                            @endif
                        </x-sidebar.single-nav-link>

                        <!-- 6. Social Media -->
                        <x-sidebar.single-nav-link :href="route('profile', ['tab' => 'social_media'])" tab="social_media"
                            x-on:click.prevent="activeTab = 'social_media'; $dispatch('switch-tab', 'social_media')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </x-slot:icon>
                            Social Media
                            @if (!empty($sectionStatuses['social_media']))
                                <x-slot:append>
                                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </x-slot:append>
                            @endif
                        </x-sidebar.single-nav-link>

                        <!-- 7. Data Tambahan -->
                        <x-sidebar.single-nav-link :href="route('profile', ['tab' => 'data_tambahan'])" tab="data_tambahan"
                            x-on:click.prevent="activeTab = 'data_tambahan'; $dispatch('switch-tab', 'data_tambahan')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </x-slot:icon>
                            Data Tambahan
                            @if (!empty($sectionStatuses['data_tambahan']))
                                <x-slot:append>
                                    <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </x-slot:append>
                            @endif
                        </x-sidebar.single-nav-link>

                        <!-- Status Pencari Kerja Badge Card -->
                        <div class="px-1 mb-3">
                            <button type="button" wire:click="openJobSearchStatusModal"
                                class="w-full p-3 text-left bg-slate-50 dark:bg-[#14203A] border border-gray-200/80 dark:border-[#1D2E54] hover:border-emerald-500/50 dark:hover:border-[#93F514]/40 rounded-xl transition duration-200 group relative">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-[#93A5C9]">Status
                                        Pencari Kerja</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-[#93F514] group-hover:translate-x-0.5 transition-transform"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                                <div class="mt-1 flex items-center gap-1.5">
                                    @if ($job_search_status === 'Aktif')
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span
                                            class="text-xs font-bold text-emerald-600 dark:text-[#93F514]">Aktif</span>
                                    @elseif ($job_search_status === 'Pasif')
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Pasif</span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                        <span class="text-xs font-bold text-gray-500 dark:text-slate-400">Tidak
                                            Aktif</span>
                                    @endif
                                </div>
                            </button>
                        </div>

                        <!-- Generate CV Action Button -->
                        <div class="pt-1 px-1">
                            <a href="{{ route('profile.cv.preview') }}" target="_blank"
                                class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 transition-all duration-200 active:scale-95 cursor-pointer group">
                                <svg class="w-4 h-4 text-white dark:text-black group-hover:scale-110 transition-transform"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Generate / Cetak CV</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Aktivitas & Pengaturan -->
                <div>
                    <div
                        class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 dark:text-[#6378A0] uppercase">
                        Aktivitas & Pengaturan
                    </div>
                    <div class="space-y-1">
                        <x-sidebar.single-nav-link :href="route('profile', ['tab' => 'riwayat'])" tab="riwayat"
                            x-on:click.prevent="activeTab = 'riwayat'; $dispatch('switch-tab', 'riwayat')">
                            <x-slot:icon>
                                <div class="relative">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @if ($hasUpcomingInterview)
                                        <span
                                            class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white dark:ring-gray-800"></span>
                                    @endif
                                </div>
                            </x-slot:icon>
                            Riwayat Lamaran
                            <x-slot:append>
                                @if ($hasUpcomingInterview)
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80">
                                        Wawancara
                                    </span>
                                @elseif (!empty($applicationCount) && $applicationCount > 0)
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 border border-indigo-200/80 dark:border-indigo-800/80">
                                        {{ $applicationCount }}
                                    </span>
                                @endif
                            </x-slot:append>
                        </x-sidebar.single-nav-link>

                        <x-sidebar.single-nav-link :href="route('profile', ['tab' => 'pengaturan'])" tab="pengaturan"
                            x-on:click.prevent="activeTab = 'pengaturan'; $dispatch('switch-tab', 'pengaturan')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </x-slot:icon>
                            Pengaturan
                        </x-sidebar.single-nav-link>
                    </div>
                </div>

            </nav>
        </div>

        <!-- Bottom Section: Logout (Commented out) -->
        {{-- <div class="p-4 border-t border-gray-200 dark:border-gray-700/80">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-2.5 px-3.5 py-2.5 rounded-xl text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-900/60 transition-all duration-200 group border border-red-200/60 dark:border-red-900/40 shadow-sm">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div> --}}
    </aside>

    <!-- Modal Status Pencari Kerja -->
    @if ($showJobSearchStatusModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm"
                    wire:click="closeJobSearchStatusModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Dialog -->
                <div
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100 dark:border-[#1D2E54]">

                    <!-- Header -->
                    <div
                        class="px-6 py-5 border-b border-gray-100 dark:border-[#1D2E54] flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Status Pencari Kerja</h3>
                            <p class="text-xs text-gray-500 dark:text-[#93A5C9] mt-0.5">Tentukan status pencarian
                                kerjamu</p>
                        </div>
                        <button type="button" wire:click="closeJobSearchStatusModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-[#93F514]">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Sub Tab Navigation -->
                    <div class="flex border-b border-gray-200 dark:border-[#1D2E54] px-6 pt-2">
                        <button type="button" wire:click="$set('activeSubTab', 'status')"
                            class="py-3 px-4 text-xs font-bold transition border-b-2 flex items-center gap-1.5 {{ $activeSubTab === 'status' ? 'border-emerald-600 text-emerald-600 dark:border-[#93F514] dark:text-[#93F514]' : 'border-transparent text-gray-500 dark:text-[#93A5C9] hover:text-gray-700 dark:hover:text-white' }}">
                            <span>Status Pencari Kerja</span>
                            <span class="text-red-500">*</span>
                        </button>

                        <button type="button"
                            @if ($job_search_status === 'Tidak Aktif') disabled title="Notifikasi tidak tersedia saat status Tidak Aktif" 
                            @else 
                                wire:click="$set('activeSubTab', 'notification')" @endif
                            class="py-3 px-4 text-xs font-bold transition border-b-2 flex items-center gap-1.5 {{ $job_search_status === 'Tidak Aktif' ? 'opacity-40 cursor-not-allowed text-gray-400 border-transparent' : ($activeSubTab === 'notification' ? 'border-emerald-600 text-emerald-600 dark:border-[#93F514] dark:text-[#93F514]' : 'border-transparent text-gray-500 dark:text-[#93A5C9] hover:text-gray-700 dark:hover:text-white') }}">
                            <span>Periode Pengiriman Notifikasi</span>
                            <span class="text-red-500">*</span>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form wire:submit.prevent="saveJobSearchStatus" class="p-6">

                        <!-- TAB 1: Status Pencari Kerja -->
                        @if ($activeSubTab === 'status')
                            <div class="space-y-4">
                                <!-- Option 1: Aktif -->
                                <label
                                    class="block p-4 rounded-xl border border-gray-200 dark:border-[#1D2E54] hover:border-emerald-500/50 dark:hover:border-[#93F514]/40 bg-gray-50/40 dark:bg-[#14203A] cursor-pointer transition">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" wire:model.live="job_search_status" value="Aktif"
                                            class="mt-1 w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500 dark:bg-[#0D1527] dark:border-[#1D2E54]">
                                        <div>
                                            <span
                                                class="block text-sm font-bold text-gray-900 dark:text-white">Aktif</span>
                                            <span
                                                class="block text-xs text-gray-500 dark:text-[#93A5C9] mt-1 leading-relaxed">
                                                Saya sedang mencari pekerjaan dan terbuka untuk menerima undangan
                                                pekerjaan serta email terkait lowongan yang sesuai dengan profil saya
                                            </span>
                                        </div>
                                    </div>
                                </label>

                                <!-- Option 2: Pasif -->
                                <label
                                    class="block p-4 rounded-xl border border-gray-200 dark:border-[#1D2E54] hover:border-emerald-500/50 dark:hover:border-[#93F514]/40 bg-gray-50/40 dark:bg-[#14203A] cursor-pointer transition">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" wire:model.live="job_search_status" value="Pasif"
                                            class="mt-1 w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500 dark:bg-[#0D1527] dark:border-[#1D2E54]">
                                        <div>
                                            <span
                                                class="block text-sm font-bold text-gray-900 dark:text-white">Pasif</span>
                                            <span
                                                class="block text-xs text-gray-500 dark:text-[#93A5C9] mt-1 leading-relaxed">
                                                Saya tidak mencari pekerjaan tetapi saya terbuka untuk menerima email
                                                terkait lowongan yang sesuai dengan profil saya
                                            </span>
                                        </div>
                                    </div>
                                </label>

                                <!-- Option 3: Tidak Aktif -->
                                <label
                                    class="block p-4 rounded-xl border border-gray-200 dark:border-[#1D2E54] hover:border-emerald-500/50 dark:hover:border-[#93F514]/40 bg-gray-50/40 dark:bg-[#14203A] cursor-pointer transition">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" wire:model.live="job_search_status" value="Tidak Aktif"
                                            class="mt-1 w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500 dark:bg-[#0D1527] dark:border-[#1D2E54]">
                                        <div>
                                            <span class="block text-sm font-bold text-gray-900 dark:text-white">Tidak
                                                Aktif</span>
                                            <span
                                                class="block text-xs text-gray-500 dark:text-[#93A5C9] mt-1 leading-relaxed">
                                                Saya tidak mencari pekerjaan dan tidak ingin menerima email apapun
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endif

                        <!-- TAB 2: Periode Pengiriman Notifikasi -->
                        @if ($activeSubTab === 'notification')
                            <div class="space-y-4">
                                @if ($job_search_status === 'Tidak Aktif')
                                    <div
                                        class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 rounded-xl text-amber-800 dark:text-amber-300 text-xs">
                                        Status Anda saat ini adalah <strong>Tidak Aktif</strong>. Periode pengiriman
                                        notifikasi tidak dapat diatur.
                                    </div>
                                @else
                                    <!-- Harian -->
                                    <label
                                        class="block p-4 rounded-xl border border-gray-200 dark:border-[#1D2E54] hover:border-emerald-500/50 dark:hover:border-[#93F514]/40 bg-gray-50/40 dark:bg-[#14203A] cursor-pointer transition">
                                        <div class="flex items-start gap-3">
                                            <input type="radio" wire:model="notification_period" value="Harian"
                                                class="mt-1 w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500 dark:bg-[#0D1527] dark:border-[#1D2E54]">
                                            <div>
                                                <span
                                                    class="block text-sm font-bold text-gray-900 dark:text-white">Harian</span>
                                                <span
                                                    class="block text-xs text-gray-500 dark:text-[#93A5C9] mt-1 leading-relaxed">
                                                    Kamu akan mendapatkan email terkait lowongan yang sesuai dengan
                                                    profilmu sebanyak maksimal satu kali sehari
                                                </span>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Mingguan -->
                                    <label
                                        class="block p-4 rounded-xl border border-gray-200 dark:border-[#1D2E54] hover:border-emerald-500/50 dark:hover:border-[#93F514]/40 bg-gray-50/40 dark:bg-[#14203A] cursor-pointer transition">
                                        <div class="flex items-start gap-3">
                                            <input type="radio" wire:model="notification_period" value="Mingguan"
                                                class="mt-1 w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500 dark:bg-[#0D1527] dark:border-[#1D2E54]">
                                            <div>
                                                <span
                                                    class="block text-sm font-bold text-gray-900 dark:text-white">Mingguan</span>
                                                <span
                                                    class="block text-xs text-gray-500 dark:text-[#93A5C9] mt-1 leading-relaxed">
                                                    Kamu akan mendapatkan email terkait lowongan yang sesuai dengan
                                                    profilmu sebanyak maksimal satu kali seminggu
                                                </span>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Bulanan -->
                                    <label
                                        class="block p-4 rounded-xl border border-gray-200 dark:border-[#1D2E54] hover:border-emerald-500/50 dark:hover:border-[#93F514]/40 bg-gray-50/40 dark:bg-[#14203A] cursor-pointer transition">
                                        <div class="flex items-start gap-3">
                                            <input type="radio" wire:model="notification_period" value="Bulanan"
                                                class="mt-1 w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500 dark:bg-[#0D1527] dark:border-[#1D2E54]">
                                            <div>
                                                <span
                                                    class="block text-sm font-bold text-gray-900 dark:text-white">Bulanan</span>
                                                <span
                                                    class="block text-xs text-gray-500 dark:text-[#93A5C9] mt-1 leading-relaxed">
                                                    Kamu akan mendapatkan email terkait lowongan yang sesuai dengan
                                                    profilmu sebanyak maksimal satu kali sebulan
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                @endif
                            </div>
                        @endif

                        <!-- Footer Actions -->
                        <div
                            class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-100 dark:border-[#1D2E54]">
                            <button type="button" wire:click="closeJobSearchStatusModal"
                                class="px-5 py-2.5 text-xs font-semibold text-gray-600 dark:text-[#93A5C9] bg-gray-100 dark:bg-[#14203A] hover:bg-gray-200 dark:hover:bg-[#1A2A4C] dark:hover:text-white rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled"
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 transition flex items-center gap-2 active:scale-95 cursor-pointer">
                                <span wire:loading.remove wire:target="saveJobSearchStatus">Simpan Status</span>
                                <span wire:loading wire:target="saveJobSearchStatus" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
