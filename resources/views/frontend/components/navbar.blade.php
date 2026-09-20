@auth
    @php
        $navUser = auth()->user();
        $navRoleId = $navUser->role_id;
        $navRoleName = strtolower($navUser->role?->name ?? '');
        $navIsAdmin = $navRoleId == 1 || in_array($navRoleName, ['admin', 'superadmin']);
        $navIsRecruiter = $navRoleId == 2 || $navRoleName === 'recruiter';
        $navIsEmployee = $navRoleId == 4 || $navRoleName === 'employee';

        $navTargetDashboard = match(true) {
            $navIsAdmin => route('admin.dashboard'),
            $navIsRecruiter => route('recruiter.dashboard'),
            $navIsEmployee => route('employee.dashboard'),
            default => route('profile'),
        };

        $navRoleBadge = match(true) {
            $navIsAdmin => 'Admin',
            $navIsRecruiter => 'Recruiter',
            $navIsEmployee => 'Karyawan',
            default => 'Pelamar',
        };

        $navDisplayName = match(true) {
            $navIsAdmin || $navIsRecruiter => $navUser->name ?? 'User',
            $navIsEmployee => !empty($navUser->employeeProfile?->full_name) ? $navUser->employeeProfile->full_name : ($navUser->name ?? 'Karyawan'),
            default => !empty($navUser->applicantProfile?->full_name) ? $navUser->applicantProfile->full_name : ($navUser->name ?? 'Pelamar'),
        };
        $navShortName = \Illuminate\Support\Str::words($navDisplayName, 2, '');

        // Resolusi Foto Profil:
        // 1. Role spesifik foto jika ada (Employee / Applicant)
        // 2. Avatar dari User (Google OAuth avatar atau custom)
        $navRawPhoto = null;
        if ($navIsEmployee && !empty($navUser->employeeProfile?->photo)) {
            $navRawPhoto = $navUser->employeeProfile->photo;
        } elseif (!empty($navUser->applicantProfile?->photo)) {
            $navRawPhoto = $navUser->applicantProfile->photo;
        } elseif (!empty($navUser->employeeProfile?->photo)) {
            $navRawPhoto = $navUser->employeeProfile->photo;
        } elseif (!empty($navUser->avatar)) {
            $navRawPhoto = $navUser->avatar;
        }

        $navPhotoUrl = null;
        if (!empty($navRawPhoto)) {
            $navPhotoUrl = \Illuminate\Support\Str::startsWith($navRawPhoto, ['http://', 'https://'])
                ? $navRawPhoto
                : asset('storage/' . ltrim($navRawPhoto, '/'));
        }

        // Inisial 1 huruf kapital ala Google Account Profile Picture
        $navUserInitial = strtoupper(mb_substr($navDisplayName, 0, 1, 'UTF-8'));
        if (empty($navUserInitial)) {
            $navUserInitial = 'U';
        }

        // Palet warna Google Material Design avatar (deterministik berdasarkan nama)
        $googleMaterialColors = [
            '#1A73E8', // Google Blue
            '#D93025', // Google Red
            '#188038', // Google Green
            '#E37400', // Google Orange / Amber
            '#8E24AA', // Google Purple
            '#00897B', // Google Teal
            '#D81B60', // Google Pink
            '#3949AB', // Google Indigo
            '#F4511E', // Google Deep Orange
            '#00ACC1', // Google Cyan
        ];
        $colorIndex = abs(crc32($navDisplayName)) % count($googleMaterialColors);
        $navGoogleBg = $googleMaterialColors[$colorIndex];
    @endphp

    <!-- Speculation Rules API (Chrome / Edge / Chromium) - Auto-prefetch on hover for instant navigation -->
    <script type="speculationrules">
    {
      "prefetch": [
        {
          "source": "list",
          "urls": ["{{ $navTargetDashboard }}", "{{ route('profile') }}"],
          "eagerness": "moderate"
        }
      ]
    }
    </script>
    <script>
        (function() {
            var prefetched = new Set();
            window.mikaPrefetch = function(url) {
                if (!url || prefetched.has(url) || url === window.location.href) return;
                prefetched.add(url);
                try {
                    var link = document.createElement('link');
                    link.rel = 'prefetch';
                    link.href = url;
                    link.as = 'document';
                    document.head.appendChild(link);
                    if ('fetch' in window) {
                        fetch(url, { priority: 'low', credentials: 'same-origin' }).catch(function(){});
                    }
                } catch(e) {}
            };
        })();
    </script>
@endauth

<!-- Floating Blur Rounded Sticky Header on Scroll -->
<header x-data="{ mobileMenuOpen: false, scrolled: window.scrollY > 20 }"
    @scroll.window="scrolled = (window.pageYOffset || document.documentElement.scrollTop) > 20" x-init="scrolled = (window.pageYOffset || document.documentElement.scrollTop) > 20"
    class="sticky top-0 z-50 transition-all duration-500 ease-in-out py-3 sm:py-4 px-3 sm:px-6">

    <div :class="scrolled
        ?
        'max-w-5xl mx-auto rounded-full bg-[#050c05]/85 backdrop-blur-xl border border-[#93F514]/40 shadow-2xl shadow-black/80 py-2.5 px-6 sm:px-8' :
        'max-w-7xl mx-auto bg-transparent border-b border-[#EEEEEE]/10 py-3 px-4 sm:px-6'"
        class="transition-all duration-500 ease-in-out flex items-center justify-between">

        <!-- Brand Logo -->
        <div class="flex items-center gap-3">
            @php
                $navLogoUrl = (isset($mainCompany) && $mainCompany->logo_url)
                    ? $mainCompany->logo_url
                    : asset('storage/logo/mikaaaa.png');
            @endphp
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group" title="MIKA CAREER - {{ $mainCompany->name ?? 'Mitra Karya Analitika' }}">
                <img src="{{ $navLogoUrl }}" alt="{{ $mainCompany->name ?? 'Logo MIKA' }}"
                    class="h-10 w-auto object-contain rounded-lg group-hover:scale-105 transition-transform duration-300">
                <div>
                    <span class="text-lg sm:text-xl font-black tracking-tight text-[#EEEEEE] flex items-center gap-1">
                        MIKA <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-[#93F514] via-[#46ee40] to-[#5FE6B6]">CAREER</span>
                    </span>
                </div>
            </a>
        </div>

        <!-- Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
            <a href="{{ route('home') }}"
                class="{{ request()->routeIs('home') ? 'text-[#93F514] font-bold' : 'text-gray-300 hover:text-[#93F514]' }} transition-all duration-200 py-1">
                Beranda
            </a>
            <a href="{{ route('about') }}"
                class="{{ request()->routeIs('about') ? 'text-[#93F514] font-bold' : 'text-gray-300 hover:text-[#93F514]' }} transition-all duration-200 py-1">
                Tentang Kami
            </a>
            <a href="{{ route('jobs.index') }}"
                class="{{ request()->routeIs('jobs.*') ? 'text-[#93F514] font-bold' : 'text-gray-300 hover:text-[#93F514]' }} transition-all duration-200 py-1">
                Lowongan
            </a>
        </nav>

        <!-- Authentication Actions & Theme Toggle -->
        <div class="hidden md:flex items-center gap-3">

            <!-- Theme Toggle Button -->
            <button type="button" class="theme-toggle-btn relative overflow-hidden" @click="$store.theme.toggle()"
                :title="$store.theme.isDark ? 'Aktifkan Light Mode' : 'Aktifkan Dark Mode'" x-data>
                <!-- Sun icon (shown in dark mode → klik untuk ke light) -->
                <svg x-show="$store.theme.isDark" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 rotate-90 scale-50"
                     x-transition:enter-end="opacity-100 rotate-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 rotate-0 scale-100"
                     x-transition:leave-end="opacity-0 -rotate-90 scale-50"
                     class="w-5 h-5 text-amber-400 absolute"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <!-- Moon icon (shown in light mode → klik untuk ke dark) -->
                <svg x-show="!$store.theme.isDark" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -rotate-90 scale-50"
                     x-transition:enter-end="opacity-100 rotate-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 rotate-0 scale-100"
                     x-transition:leave-end="opacity-0 rotate-90 scale-50"
                     class="w-4 h-4 text-current absolute"
                     fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z" />
                </svg>
            </button>

            @auth
                <!-- Desktop User Profile Capsule & Dropdown -->
                <div class="relative" x-data="{ profileOpen: false }" @click.outside="profileOpen = false" @keydown.escape.window="profileOpen = false">
                    <button type="button"
                        @click="profileOpen = !profileOpen"
                        @mouseenter="window.mikaPrefetch && (window.mikaPrefetch('{{ $navTargetDashboard }}'), window.mikaPrefetch('{{ route('profile') }}'))"
                        @focus="window.mikaPrefetch && (window.mikaPrefetch('{{ $navTargetDashboard }}'), window.mikaPrefetch('{{ route('profile') }}'))"
                        title="{{ $navDisplayName }}"
                        class="nav-profile-pill group flex items-center gap-2.5 pl-1.5 pr-3.5 py-1.5 rounded-full bg-[#061806]/85 hover:bg-[#0c2e0c] border border-[#93F514]/40 hover:border-[#93F514] text-[#EEEEEE] transition-all duration-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 cursor-pointer">
                        
                        <!-- Circular Avatar (Photo or Google Initial Fallback) -->
                        <div x-data="{ imgError: false }" class="relative shrink-0 flex items-center justify-center">
                            @if(!empty($navPhotoUrl))
                                <img src="{{ $navPhotoUrl }}"
                                     alt="{{ $navDisplayName }}"
                                     referrerpolicy="no-referrer"
                                     loading="lazy"
                                     x-show="!imgError"
                                     x-on:error="imgError = true"
                                     class="w-7 h-7 sm:w-8 sm:h-8 rounded-full object-cover shrink-0 ring-2 ring-[#93F514]/40 group-hover:ring-[#93F514] transition-all duration-300 shadow-sm">
                            @endif
                            <div x-show="{{ empty($navPhotoUrl) ? 'true' : 'imgError' }}"
                                 {{ !empty($navPhotoUrl) ? 'x-cloak' : '' }}
                                 style="background-color: {{ $navGoogleBg }};"
                                 class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-bold text-white text-xs select-none shadow-inner shrink-0 ring-2 ring-[#93F514]/40 group-hover:ring-[#93F514] transition-all duration-300">
                                <span>{{ $navUserInitial }}</span>
                            </div>
                        </div>

                        <!-- User Name -->
                        <span class="nav-profile-name text-xs sm:text-sm font-semibold tracking-tight text-gray-200 group-hover:text-white transition-colors duration-200 truncate max-w-[130px] sm:max-w-[160px]">
                            {{ $navShortName }}
                        </span>

                        <!-- Dropdown Chevron Icon -->
                        <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-[#93F514] transition-transform duration-200 shrink-0"
                             :class="profileOpen ? 'rotate-180 text-[#93F514]' : ''"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Floating Dropdown Menu -->
                    <div x-show="profileOpen"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                         class="nav-profile-dropdown absolute right-0 mt-2 w-64 rounded-2xl bg-[#050c05]/95 backdrop-blur-2xl border border-[#93F514]/35 shadow-2xl shadow-black/80 py-2 z-50 overflow-hidden">
                        
                        <!-- User Header Summary -->
                        <div class="nav-profile-header px-4 py-3 border-b border-[#93F514]/20 bg-[#061806]/60">
                            <div class="flex items-center gap-3">
                                <div x-data="{ imgErrorDrop: false }" class="relative shrink-0 flex items-center justify-center">
                                    @if(!empty($navPhotoUrl))
                                        <img src="{{ $navPhotoUrl }}"
                                             alt="{{ $navDisplayName }}"
                                             referrerpolicy="no-referrer"
                                             loading="lazy"
                                             x-show="!imgErrorDrop"
                                             x-on:error="imgErrorDrop = true"
                                             class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-[#93F514]/40 shadow-sm">
                                    @endif
                                    <div x-show="{{ empty($navPhotoUrl) ? 'true' : 'imgErrorDrop' }}"
                                         {{ !empty($navPhotoUrl) ? 'x-cloak' : '' }}
                                         style="background-color: {{ $navGoogleBg }};"
                                         class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm select-none shadow-inner shrink-0 ring-2 ring-[#93F514]/40">
                                        <span>{{ $navUserInitial }}</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="nav-profile-name text-xs font-bold text-[#EEEEEE] truncate">{{ $navDisplayName }}</p>
                                    <p class="nav-profile-email text-[11px] text-gray-400 truncate">{{ $navUser->email }}</p>
                                    <span class="nav-profile-role inline-block mt-1 text-[9.5px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-[#93F514]/15 text-[#93F514] border border-[#93F514]/30">
                                        {{ $navRoleBadge }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Links -->
                        <div class="p-1.5 space-y-0.5 text-xs font-medium">
                            <a href="{{ $navTargetDashboard }}"
                               @mouseenter="window.mikaPrefetch && window.mikaPrefetch('{{ $navTargetDashboard }}')"
                               @touchstart.passive="window.mikaPrefetch && window.mikaPrefetch('{{ $navTargetDashboard }}')"
                               class="nav-profile-item flex items-center gap-2.5 px-3 py-2 rounded-xl text-gray-200 hover:text-[#93F514] hover:bg-[#93F514]/10 transition-colors duration-150">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                <span>Dashboard {{ $navRoleBadge }}</span>
                            </a>

                            <a href="{{ route('profile') }}"
                               @mouseenter="window.mikaPrefetch && window.mikaPrefetch('{{ route('profile') }}')"
                               @touchstart.passive="window.mikaPrefetch && window.mikaPrefetch('{{ route('profile') }}')"
                               class="nav-profile-item flex items-center gap-2.5 px-3 py-2 rounded-xl text-gray-200 hover:text-[#93F514] hover:bg-[#93F514]/10 transition-colors duration-150">
                                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Profil Saya</span>
                            </a>
                        </div>

                        <!-- Logout Button -->
                        <div class="nav-profile-divider border-t border-[#93F514]/20 pt-1 mt-1 px-1.5">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-colors duration-150 text-xs font-semibold text-left cursor-pointer">
                                    <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                    class="text-xs sm:text-sm font-semibold text-gray-300 hover:text-[#EEEEEE] px-4 py-2 rounded-full hover:bg-[#EEEEEE]/5 transition">
                    Masuk
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center px-5 py-2 text-xs sm:text-sm font-bold text-[#EEEEEE] hover:text-black rounded-full bg-[#061806] hover:bg-[#93F514] border border-[#93F514]/50 shadow-md shadow-black/30 transition-all duration-200">
                        Daftar
                    </a>
                @endif
            @endauth
        </div>

        <!-- Mobile Hamburger Button -->
        <div class="flex items-center md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                class="p-2 rounded-full text-gray-400 hover:text-[#EEEEEE] hover:bg-[#93F514]/20 border border-[#93F514]/40 focus:outline-none">
                <svg class="w-5 h-5" x-show="!mobileMenuOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg class="w-5 h-5" x-show="mobileMenuOpen" x-cloak fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-4 scale-95" x-cloak
        class="md:hidden mt-2 max-w-sm mx-auto bg-[#050c05]/95 backdrop-blur-2xl border border-[#93F514]/40 rounded-3xl p-5 shadow-2xl space-y-3">
        <div class="flex flex-col space-y-2 font-semibold">
            <a @click="mobileMenuOpen = false" href="{{ route('home') }}"
                class="px-4 py-2.5 rounded-2xl {{ request()->routeIs('home') ? 'bg-[#93F514]/20 text-[#93F514] font-bold border border-[#93F514]/40' : 'text-gray-300 hover:bg-[#EEEEEE]/5 hover:text-[#93F514]' }}">
                Beranda
            </a>
            <a @click="mobileMenuOpen = false" href="{{ route('about') }}"
                class="px-4 py-2.5 rounded-2xl {{ request()->routeIs('about') ? 'bg-[#93F514]/20 text-[#93F514] font-bold border border-[#93F514]/40' : 'text-gray-300 hover:bg-[#EEEEEE]/5 hover:text-[#93F514]' }}">
                Tentang Kami
            </a>
            <a @click="mobileMenuOpen = false" href="{{ route('jobs.index') }}"
                class="px-4 py-2.5 rounded-2xl {{ request()->routeIs('jobs.*') ? 'bg-[#93F514]/20 text-[#93F514] font-bold border border-[#93F514]/40' : 'text-gray-300 hover:bg-[#EEEEEE]/5 hover:text-[#93F514]' }}">
                Lowongan
            </a>
        </div>
        <div class="pt-3 border-t border-[#93F514]/20 flex flex-col gap-2">

            <!-- Theme Toggle (Mobile) -->
            <button type="button" @click="$store.theme.toggle()" x-data
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-[#93F514]/30 text-sm font-semibold transition"
                :class="$store.theme.isDark ? 'text-gray-300 bg-transparent' : 'text-[#5a9e08] bg-[#f0fde4]'">
                <div class="relative flex items-center justify-center w-5 h-5 overflow-hidden">
                    <!-- Sun icon (shown in dark mode) -->
                    <svg x-show="$store.theme.isDark" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 rotate-90 scale-50"
                         x-transition:enter-end="opacity-100 rotate-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 rotate-0 scale-100"
                         x-transition:leave-end="opacity-0 -rotate-90 scale-50"
                         class="w-4 h-4 text-amber-400 absolute" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <!-- Moon icon (shown in light mode) -->
                    <svg x-show="!$store.theme.isDark" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -rotate-90 scale-50"
                         x-transition:enter-end="opacity-100 rotate-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 rotate-0 scale-100"
                         x-transition:leave-end="opacity-0 rotate-90 scale-50"
                         class="w-4 h-4 text-current absolute" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z" />
                    </svg>
                </div>
                <span x-text="$store.theme.isDark ? 'Aktifkan Light Mode' : 'Aktifkan Dark Mode'"></span>
            </button>

            @auth
                <!-- Mobile User Profile Card -->
                <div class="nav-profile-mobile-card p-3 rounded-2xl bg-[#061806]/90 border border-[#93F514]/30 shadow-md">
                    <div class="flex items-center gap-3">
                        <div x-data="{ imgErrorMob: false }" class="relative shrink-0 flex items-center justify-center">
                            @if(!empty($navPhotoUrl))
                                <img src="{{ $navPhotoUrl }}"
                                     alt="{{ $navDisplayName }}"
                                     referrerpolicy="no-referrer"
                                     loading="lazy"
                                     x-show="!imgErrorMob"
                                     x-on:error="imgErrorMob = true"
                                     class="w-10 h-10 rounded-full object-cover ring-2 ring-[#93F514]/40 shadow-sm">
                            @endif
                            <div x-show="{{ empty($navPhotoUrl) ? 'true' : 'imgErrorMob' }}"
                                 {{ !empty($navPhotoUrl) ? 'x-cloak' : '' }}
                                 style="background-color: {{ $navGoogleBg }};"
                                 class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-white text-sm select-none shadow-md ring-2 ring-[#93F514]/40">
                                <span>{{ $navUserInitial }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="nav-profile-name text-xs font-bold text-[#EEEEEE] truncate">{{ $navDisplayName }}</p>
                            <p class="nav-profile-email text-[11px] text-gray-400 truncate">{{ $navUser->email }}</p>
                            <span class="nav-profile-role inline-block mt-0.5 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-[#93F514]/15 text-[#93F514] border border-[#93F514]/30">
                                {{ $navRoleBadge }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-3 pt-2.5 border-t border-[#93F514]/20">
                        <a @click="mobileMenuOpen = false" href="{{ $navTargetDashboard }}"
                           @touchstart.passive="window.mikaPrefetch && window.mikaPrefetch('{{ $navTargetDashboard }}')"
                           class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-[#93F514] hover:bg-[#82dc10] text-black font-bold text-xs shadow-sm transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        <a @click="mobileMenuOpen = false" href="{{ route('profile') }}"
                           @touchstart.passive="window.mikaPrefetch && window.mikaPrefetch('{{ route('profile') }}')"
                           class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-[#93F514]/15 hover:bg-[#93F514]/25 text-[#93F514] border border-[#93F514]/30 font-semibold text-xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Profil</span>
                        </a>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 border border-red-500/20 font-semibold text-xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Keluar Akun</span>
                        </button>
                    </form>
                </div>
            @else
                <a @click="mobileMenuOpen = false" href="{{ route('login') }}"
                    class="w-full text-center px-4 py-2.5 rounded-2xl bg-[#93F514]/10 border border-[#93F514]/40 text-[#EEEEEE] font-semibold text-sm">
                    Masuk
                </a>
                @if (Route::has('register'))
                    <a @click="mobileMenuOpen = false" href="{{ route('register') }}"
                        class="w-full text-center px-4 py-2.5 rounded-2xl bg-[#061806] hover:bg-[#93F514] text-[#EEEEEE] hover:text-black border border-[#93F514]/50 font-bold text-sm shadow-md shadow-black/30 transition">
                        Daftar Akun Baru
                    </a>
                @endif
            @endauth
        </div>
    </div>
</header>
