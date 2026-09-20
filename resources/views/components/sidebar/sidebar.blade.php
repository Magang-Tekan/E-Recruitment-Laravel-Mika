@if (auth()->check() &&
        (in_array(strtolower(auth()->user()->role?->name ?? ''), ['admin', 'superadmin', 'recruiter']) ||
            in_array(auth()->user()->role_id, [1, 2])))
    @php
        $isAdmin =
            auth()->user()->role_id == 1 ||
            strtolower(auth()->user()->role?->name ?? '') === 'admin' ||
            strtolower(auth()->user()->role?->name ?? '') === 'superadmin';
    @endphp
    <!-- Off-canvas / Mobile backdrop -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-gray-900/50 dark:bg-gray-950/80 backdrop-blur-sm lg:hidden" x-cloak></div>

    <!-- Main Sidebar Container -->
    <aside x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed top-0 left-0 z-50 h-screen w-64 bg-white dark:bg-[#0D1527] border-r border-gray-200 dark:border-[#1D2E54] text-gray-700 dark:text-gray-300 transition-transform duration-300 ease-in-out flex flex-col justify-between shadow-xl">

        <!-- Top Section: Brand Header & Navigation -->
        <div class="flex-1 overflow-y-auto px-4 py-5 custom-scrollbar">

            <!-- Brand Logo Header -->
            <div class="flex items-center justify-between pb-5 mb-3 px-2">
                <a href="{{ $isAdmin ? route('admin.dashboard') : route('recruiter.dashboard') }}"
                    class="flex items-center gap-3 group">
                    <img src="{{ asset('images/mikaaaa.png') }}" alt="Logo MIKA"
                        class="h-8 w-auto object-contain rounded-lg group-hover:scale-105 transition-transform duration-200">
                    <div>
                        <span
                            class="block font-bold text-lg tracking-tight text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-[#93F514] transition-colors">E-Rekrutmen</span>
                        <span
                            class="block text-[10px] font-semibold tracking-widest text-emerald-600 dark:text-[#93F514] uppercase">
                            {{ $isAdmin ? 'Panel Admin' : 'Panel Recruiter' }}
                        </span>
                    </div>
                </a>

                <!-- Mobile Close Button -->
                <button @click="sidebarOpen = false"
                    class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Sections -->
            <nav class="space-y-6">

                <!-- Section 1: Dashboard -->
                <div>
                    <div
                        class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 dark:text-gray-500 uppercase">
                        Menu Utama
                    </div>
                    <div class="space-y-1">
                        <x-sidebar.single-nav-link :href="$isAdmin ? route('admin.dashboard') : route('recruiter.dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('recruiter.dashboard')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </x-slot:icon>
                            Dashboard
                        </x-sidebar.single-nav-link>
                    </div>
                </div>

                @if ($isAdmin)
                    <!-- Section 3: Perusahaan & Divisi (Admin Only) -->
                    <div>
                        <div
                            class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 dark:text-gray-500 uppercase">
                            Data Master
                        </div>
                        <div class="space-y-1">
                            <x-sidebar.nested-nav-link title="Perusahaan" :active="request()->routeIs('admin.company*') || request()->routeIs('admin.department*') || request()->routeIs('admin.position*')">
                                <x-slot:icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                    </svg>
                                </x-slot:icon>

                                <a href="{{ route('admin.company') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.company*') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Profil Perusahaan
                                </a>
                                <a href="{{ route('admin.department') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.department*') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Departemen
                                </a>
                                <a href="{{ route('admin.position') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.position*') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Posisi
                                </a>
                            </x-sidebar.nested-nav-link>
                        </div>
                    </div>
                @endif

                <!-- Section 2: Manajemen rekrutmen -->
                <div>
                    <div
                        class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 dark:text-gray-500 uppercase">
                        Rekrutmen
                    </div>
                    <div class="space-y-1">
                        <x-sidebar.nested-nav-link title="Data Rekrutmen" :active="request()->routeIs('admin.job*') ||
                            request()->routeIs('admin.application*') ||
                            request()->routeIs('recruiter.application*') ||
                            request()->routeIs('admin.candidate*') ||
                            request()->routeIs('recruiter.candidate*') ||
                            request()->routeIs('admin.test*') ||
                            request()->routeIs('admin.test_evaluation*') ||
                            request()->routeIs('recruiter.test_evaluation*') ||
                            request()->routeIs('admin.interview_schedule*') ||
                            request()->routeIs('recruiter.interview_schedule*')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>

                            </x-slot:icon>

                            <a href="{{ $isAdmin ? route('admin.candidate') : route('recruiter.candidate') }}"
                                class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.candidate') || request()->routeIs('recruiter.candidate') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                Kandidat Pelamar
                            </a>

                            @if ($isAdmin)
                                <a href="{{ route('admin.job') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.job') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Lowongan Kerja
                                </a>
                            @endif

                            <a href="{{ $isAdmin ? route('admin.application') : route('recruiter.application') }}"
                                class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.application') || request()->routeIs('recruiter.application') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                Lamaran Masuk
                            </a>

                            @if ($isAdmin)
                                <a href="{{ route('admin.test') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.test') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Paket Ujian Tes
                                </a>
                            @endif

                            <a href="{{ $isAdmin ? route('admin.test_evaluation') : route('recruiter.test_evaluation') }}"
                                class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.test_evaluation*') || request()->routeIs('recruiter.test_evaluation*') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                Evaluasi & Nilai Ujian
                            </a>

                            <a href="{{ $isAdmin ? route('admin.interview_schedule') : route('recruiter.interview_schedule') }}"
                                class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.interview_schedule*') || request()->routeIs('recruiter.interview_schedule*') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                Jadwal Wawancara
                            </a>
                        </x-sidebar.nested-nav-link>
                    </div>
                </div>

                <!-- Section 3: Asesmen Karyawan (Internal) -->
                <div>
                    <div
                        class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 dark:text-gray-500 uppercase">
                        Asesmen Karyawan
                    </div>
                    <div class="space-y-1">
                        <x-sidebar.nested-nav-link title="Asesmen Internal" :active="request()->routeIs('admin.employee_test') ||
                            request()->routeIs('admin.employee_test.*') ||
                            request()->routeIs('admin.employee_test_evaluation*') ||
                            request()->routeIs('recruiter.employee_test_evaluation*') ||
                            request()->routeIs('admin.employee*')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </x-slot:icon>

                            @if ($isAdmin)
                                <a href="{{ route('admin.employee_test') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.employee_test') || request()->routeIs('admin.employee_test.*') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Paket Asesmen Karyawan
                                </a>
                            @endif

                            <a href="{{ $isAdmin ? route('admin.employee_test_evaluation') : route('recruiter.employee_test_evaluation') }}"
                                class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.employee_test_evaluation*') || request()->routeIs('recruiter.employee_test_evaluation*') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                Hasil & Evaluasi Karyawan
                            </a>

                            @if ($isAdmin)
                                <a href="{{ route('admin.employee') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.employee') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Data Karyawan
                                </a>
                            @endif
                        </x-sidebar.nested-nav-link>
                    </div>
                </div>

                @if ($isAdmin)
                    <!-- Section 4: Data Master (Admin Only) -->
                    <div>
                        <div
                            class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 dark:text-gray-500 uppercase">
                            Data Soal
                        </div>
                        <div class="space-y-1">
                            <x-sidebar.nested-nav-link title="Kualifikasi & Tes" :active="request()->routeIs('admin.major') ||
                                request()->routeIs('admin.degree') ||
                                request()->routeIs('admin.test_category') ||
                                request()->routeIs('admin.question_bank')">
                                <x-slot:icon>
                                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                    </svg>
                                </x-slot:icon>
                                <a href="{{ route('admin.degree') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.degree') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Tingkat Pendidikan
                                </a>
                                <a href="{{ route('admin.major') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.major') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Jurusan
                                </a>
                                <a href="{{ route('admin.test_category') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.test_category') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Kategori Soal
                                </a>
                                <a href="{{ route('admin.question_bank') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.question_bank') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Bank Soal
                                </a>
                            </x-sidebar.nested-nav-link>
                        </div>
                    </div>

                    <!-- Section 5: Pengguna & Hak Akses (Admin Only) -->
                    <div>
                        <div
                            class="px-3 mb-2 text-[11px] font-bold tracking-wider text-gray-400 dark:text-gray-500 uppercase">
                            Hak Akses
                        </div>
                        <div class="space-y-1">
                            <x-sidebar.nested-nav-link title="Pengguna & Role" :active="request()->routeIs('admin.user*') || request()->routeIs('admin.role*')">
                                <x-slot:icon>
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </x-slot:icon>

                                <a href="{{ route('admin.user') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.user*') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Kelola Pengguna
                                </a>
                                <a href="{{ route('admin.role') }}"
                                    class="block px-3 py-2 text-xs font-medium {{ request()->routeIs('admin.role*') ? 'text-emerald-700 dark:text-[#93F514] font-semibold bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700/60' }} rounded-lg transition-colors">
                                    Kelola Role
                                </a>
                            </x-sidebar.nested-nav-link>
                        </div>
                    </div>
                @endif
            </nav>
        </div>

    </aside>
@endif
