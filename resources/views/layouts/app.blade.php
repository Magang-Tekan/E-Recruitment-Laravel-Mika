<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MIKA CAREER') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/mikaaaa.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Tailwind CSS CDN Fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Configure Tailwind CDN for class-based dark mode & Mika colors
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'neon-green': '#93F514',
                        'mika-dark': '#040804',
                        'mika-surface': '#061506',
                    }
                }
            }
        };
    </script>

    <!-- Dark Mode Init: Sync dengan frontend (mika-theme) — apply sebelum render -->
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

        // Global theme toggle function
        window.toggleMikaTheme = function () {
            var html = document.documentElement;
            var isCurrentlyDark = html.classList.contains('dark');
            var nextIsDark = !isCurrentlyDark;

            if (nextIsDark) {
                html.classList.add('dark');
                html.classList.remove('light-mode');
                localStorage.setItem('mika-theme', 'dark');
            } else {
                html.classList.remove('dark');
                html.classList.add('light-mode');
                localStorage.setItem('mika-theme', 'light');
            }

            if (window.Alpine && window.Alpine.store) {
                try {
                    var store = window.Alpine.store('theme');
                    if (store) {
                        store.isDark = nextIsDark;
                    }
                } catch (e) {}
            }

            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: nextIsDark } }));
            return nextIsDark;
        };

        // Theme store helper for Alpine.js across all lifecycle events
        function registerThemeStore() {
            if (window.Alpine && window.Alpine.store) {
                try {
                    if (!window.Alpine.store('theme')) {
                        window.Alpine.store('theme', {
                            isDark: document.documentElement.classList.contains('dark'),
                            toggle: function () {
                                return window.toggleMikaTheme();
                            }
                        });
                    } else {
                        window.Alpine.store('theme').isDark = document.documentElement.classList.contains('dark');
                    }
                } catch (e) {}
            }
        }

        document.addEventListener('alpine:init', registerThemeStore);
        document.addEventListener('livewire:init', registerThemeStore);
        document.addEventListener('livewire:navigated', registerThemeStore);
        if (window.Alpine) {
            registerThemeStore();
        }
    </script>

    <!-- Native Date/Time Picker & Mika Theme Centralized Styling -->
    <style>
        :root {
            --mika-green: #93F514;
            --mika-green-glow: rgba(147, 245, 20, 0.4);
            --mika-green-dark: #6bbd08;
            --mika-bg-dark: #070B14;
            --mika-card-dark: #0D1527;
            --mika-surface-dark: #14203A;
            --mika-border-dark: #1D2E54;
            --mika-bg-light: #F1F5F1;
            --mika-card-light: #FFFFFF;
        }

        /* Selection styling matching frontend */
        ::selection {
            background-color: #93F514 !important;
            color: #000000 !important;
        }

        body {
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Light Mode: Base Page Background (#F1F5F1) */
        html:not(.dark) body,
        html.light-mode body,
        html:not(.dark) .bg-gray-100,
        html.light-mode .bg-gray-100 {
            background-color: #F1F5F1 !important;
        }

        /* =========================================================================
           AUTHENTICATED PORTAL DARK THEME: DEEP NAVY PALETTE
           (Admin, Recruiter, Employee & Pelamar)
           ========================================================================= */
        /* Base Page Background (#070B14) */
        html.dark body,
        html.dark body > .min-h-screen,
        html.dark .bg-gray-900:not([class*="fixed"]):not([class*="inset-0"]),
        html.dark .bg-slate-950:not([class*="fixed"]):not([class*="inset-0"]) {
            background-color: #070B14 !important;
        }

        /* Modal & Dialog Containers: Keep flex wrapper transparent so backdrop blur shows through */
        .fixed .min-h-screen,
        [role="dialog"] .min-h-screen,
        [aria-modal="true"] .min-h-screen {
            background-color: transparent !important;
        }

        /* Modal Backdrop: Translucent Deep Navy with elegant blur in Dark Mode (matching Light Mode visibility) */
        html.dark .fixed.inset-0.transition-opacity,
        html.dark .fixed.inset-0[class*="backdrop-blur"],
        html.dark [class*="backdrop-blur"].fixed.inset-0 {
            background-color: rgba(7, 11, 20, 0.60) !important;
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
        }

        /* Cards, Panels, Sidebar & Headers (#0D1527) */
        html.dark header,
        html.dark aside,
        html.dark .bg-gray-800,
        html.dark .bg-slate-900,
        html.dark .dark\:bg-gray-800,
        html.dark .dark\:bg-slate-900,
        html.dark aside.bg-gray-800,
        html.dark aside.dark\:bg-gray-800,
        html.dark header.bg-gray-800,
        html.dark header.dark\:bg-gray-800 {
            background-color: #0D1527 !important;
            border-color: #1D2E54 !important;
        }

        html.dark header {
            background-color: #0D1527 !important;
            border-bottom: 1px solid #1D2E54 !important;
        }

        html.dark header h2 {
            color: #FFFFFF !important;
            font-weight: 700 !important;
        }

        /* Top Nav Theme Toggle & User Button */
        html.dark header button.border-gray-200,
        html.dark header button.dark\:border-gray-700,
        html.dark header button.dark\:border-gray-700\/80 {
            background-color: #14203A !important;
            border-color: #1D2E54 !important;
            color: #F8FAFC !important;
        }
        html.dark header button:hover {
            background-color: #1A2A4C !important;
        }

        /* Secondary Surfaces, Inputs, Skeletons, Modals & Table Header (#14203A) */
        html.dark .bg-gray-700,
        html.dark .bg-slate-800,
        html.dark .dark\:bg-gray-700,
        html.dark .dark\:bg-slate-800,
        html.dark .dark\:bg-slate-800\/50,
        html.dark .dark\:bg-gray-800\/50,
        html.dark .dark\:bg-slate-700,
        html.dark .dark\:bg-slate-700\/70,
        html.dark .dark\:bg-gray-900\/50:not([class*="fixed"]):not([class*="inset-0"]),
        html.dark .dark\:bg-gray-900\/60:not([class*="fixed"]):not([class*="inset-0"]),
        html.dark .dark\:bg-gray-900\/40:not([class*="fixed"]):not([class*="inset-0"]),
        html.dark .dark\:bg-gray-900\/30:not([class*="fixed"]):not([class*="inset-0"]),
        html.dark .dark\:bg-gray-700\/40,
        html.dark .dark\:bg-gray-700\/50 {
            background-color: #14203A !important;
        }

        /* Table Header Row & Column Labels (Super Crisp!) */
        html.dark thead tr {
            background-color: #14203A !important;
            border-bottom: 1px solid #1D2E54 !important;
        }
        html.dark thead th,
        html.dark thead th span {
            color: #DDE5F5 !important;
            font-weight: 700 !important;
            letter-spacing: 0.05em !important;
        }

        /* Table Body Rows */
        html.dark tbody tr {
            background-color: #0D1527 !important;
            border-color: #1D2E54 !important;
            transition: background-color 0.15s ease !important;
        }
        html.dark tbody tr:hover,
        html.dark .dark\:hover\:bg-slate-800\/40:hover,
        html.dark .dark\:hover\:bg-slate-800:hover,
        html.dark .hover\:bg-gray-50\/80:hover,
        html.dark .dark\:hover\:bg-gray-700:hover,
        html.dark .dark\:hover\:bg-gray-700\/60:hover,
        html.dark .dark\:hover\:bg-gray-700\/40:hover,
        html.dark .dark\:hover\:bg-gray-800:hover,
        html.dark .dark\:focus\:bg-gray-800:focus {
            background-color: #1A2A4C !important;
        }

        /* Text Contrast Enhancements */
        html.dark .text-slate-400,
        html.dark .dark\:text-slate-400,
        html.dark .text-gray-400,
        html.dark .text-gray-500,
        html.dark .dark\:text-gray-400,
        html.dark .dark\:text-gray-500 {
            color: #93A5C9 !important;
        }

        html.dark .text-slate-300,
        html.dark .dark\:text-slate-300,
        html.dark .dark\:text-gray-300 {
            color: #DDE5F5 !important;
        }

        html.dark .text-gray-900,
        html.dark .dark\:text-white,
        html.dark .dark\:text-slate-100,
        html.dark .dark\:text-gray-100 {
            color: #FFFFFF !important;
        }

        /* Borders & Dividers */
        html.dark .border-gray-700,
        html.dark .border-gray-800,
        html.dark .border-slate-800,
        html.dark .border-slate-700,
        html.dark .dark\:border-gray-700,
        html.dark .dark\:border-gray-700\/80,
        html.dark .dark\:border-gray-700\/60,
        html.dark .dark\:border-gray-800,
        html.dark .dark\:border-slate-800,
        html.dark .dark\:border-slate-700,
        html.dark .divide-gray-100 > :not([hidden]) ~ :not([hidden]),
        html.dark .divide-gray-700 > :not([hidden]) ~ :not([hidden]),
        html.dark .divide-slate-800 > :not([hidden]) ~ :not([hidden]),
        html.dark .divide-slate-800\/60 > :not([hidden]) ~ :not([hidden]) {
            border-color: #1D2E54 !important;
        }

        /* Form Controls: Inputs, Search Bar, Textarea & Select */
        html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not(.bg-transparent):not([style*="transparent"]),
        html.dark select,
        html.dark textarea {
            background-color: #14203A !important;
            border-color: #1D2E54 !important;
            color: #F8FAFC !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #6378A0 !important;
        }
        html.dark input:focus,
        html.dark select:focus,
        html.dark textarea:focus {
            border-color: #93F514 !important;
            box-shadow: 0 0 0 2px rgba(147, 245, 20, 0.25) !important;
        }

        /* Badges & Pills */
        html.dark .location-badge,
        html.dark .bg-gray-100.dark\:bg-slate-800 {
            background-color: #14203A !important;
            border: 1px solid #253966 !important;
            color: #DDE5F5 !important;
        }
        html.dark .location-badge svg {
            color: #38BDF8 !important;
        }

        /* Links */
        html.dark .company-website-link,
        html.dark td a.text-indigo-600,
        html.dark td a.dark\:text-indigo-400 {
            color: #38BDF8 !important;
        }
        html.dark .company-website-link:hover,
        html.dark td a.text-indigo-600:hover,
        html.dark td a.dark\:text-indigo-400:hover {
            color: #93F514 !important;
        }

        /* Action Icons */
        html.dark .action-view,
        html.dark a.text-emerald-600 {
            color: #34D399 !important;
        }
        html.dark .action-edit,
        html.dark button.hover\:text-indigo-600,
        html.dark button.dark\:hover\:text-indigo-400 {
            color: #93A5C9 !important;
        }
        html.dark button.hover\:text-indigo-600:hover,
        html.dark button.dark\:hover\:text-indigo-400:hover {
            color: #60A5FA !important;
            background-color: #14203A !important;
        }
        html.dark .action-delete,
        html.dark button.hover\:text-rose-600,
        html.dark button.dark\:hover\:text-rose-400 {
            color: #93A5C9 !important;
        }
        html.dark button.hover\:text-rose-600:hover,
        html.dark button.dark\:hover\:text-rose-400:hover {
            color: #FB7185 !important;
            background-color: rgba(251, 113, 133, 0.15) !important;
        }

        /* Quill Editor Dark Mode in Admin */
        html.dark .ql-toolbar.ql-snow {
            background-color: #14203A !important;
            border-color: #1D2E54 !important;
        }
        html.dark .ql-container.ql-snow {
            background-color: #0D1527 !important;
            border-color: #1D2E54 !important;
            color: #F8FAFC !important;
        }
        html.dark .ql-snow .ql-stroke {
            stroke: #93A5C9 !important;
        }
        html.dark .ql-snow .ql-fill {
            fill: #93A5C9 !important;
        }
        html.dark .ql-snow .ql-picker {
            color: #93A5C9 !important;
        }

        /* Buttons: Universal Primary / Action Button Theme Alignment */
        html.dark button.bg-indigo-600,
        html.dark a.bg-indigo-600,
        html.dark .bg-indigo-600 {
            background-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 4px 14px rgba(147, 245, 20, 0.25) !important;
        }
        html.dark button.bg-indigo-600:hover,
        html.dark a.bg-indigo-600:hover,
        html.dark .bg-indigo-600:hover {
            background-color: #82dc12 !important;
            color: #000000 !important;
        }
        html.dark button.bg-indigo-600 svg,
        html.dark a.bg-indigo-600 svg,
        html.dark .bg-indigo-600 svg {
            color: #000000 !important;
            stroke: #000000 !important;
        }
        html.dark button.bg-indigo-600 span,
        html.dark a.bg-indigo-600 span,
        html.dark .bg-indigo-600 span,
        html.dark button.bg-indigo-600 *,
        html.dark a.bg-indigo-600 * {
            color: #000000 !important;
        }

        html:not(.dark) button.bg-indigo-600,
        html:not(.dark) a.bg-indigo-600,
        html.light-mode button.bg-indigo-600,
        html.light-mode a.bg-indigo-600 {
            background-color: #059669 !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.20) !important;
        }
        html:not(.dark) button.bg-indigo-600:hover,
        html:not(.dark) a.bg-indigo-600:hover,
        html.light-mode button.bg-indigo-600:hover,
        html.light-mode a.bg-indigo-600:hover {
            background-color: #047857 !important;
            color: #FFFFFF !important;
        }

        /* Native Date/Time Picker Controls */
        input[type="date"],
        input[type="datetime-local"],
        input[type="time"],
        input[type="month"],
        input[type="week"] {
            color-scheme: light;
        }

        html.dark input[type="date"],
        html.dark input[type="datetime-local"],
        html.dark input[type="time"],
        html.dark input[type="month"],
        html.dark input[type="week"],
        .dark input[type="date"],
        .dark input[type="datetime-local"],
        .dark input[type="time"],
        .dark input[type="month"],
        .dark input[type="week"] {
            color-scheme: dark !important;
        }

        input[type="date"]::-webkit-calendar-picker-indicator,
        input[type="datetime-local"]::-webkit-calendar-picker-indicator,
        input[type="time"]::-webkit-calendar-picker-indicator,
        input[type="month"]::-webkit-calendar-picker-indicator,
        input[type="week"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.85;
            transition: opacity 0.15s ease;
        }

        input[type="date"]::-webkit-calendar-picker-indicator:hover,
        input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover,
        input[type="time"]::-webkit-calendar-picker-indicator:hover,
        input[type="month"]::-webkit-calendar-picker-indicator:hover,
        input[type="week"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }

        /* =========================================================================
           CUSTOM SLEEK SCROLLBAR: SUBTLE & BARELY VISIBLE ("AGAK TIDAK TERLIHAT")
           (Sidebar Admin, Pelamar, Karyawan, Recruiter)
           ========================================================================= */
        .custom-scrollbar {
            scrollbar-width: thin !important;
            scrollbar-color: rgba(148, 163, 184, 0.2) transparent !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px !important;
            height: 4px !important;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent !important;
            border-radius: 9999px !important;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.18) !important;
            border-radius: 9999px !important;
            transition: background-color 0.2s ease !important;
        }

        .custom-scrollbar:hover::-webkit-scrollbar-thumb,
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(148, 163, 184, 0.38) !important;
        }

        html.dark .custom-scrollbar {
            scrollbar-color: rgba(255, 255, 255, 0.07) transparent !important;
        }

        html.dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.07) !important;
        }

        html.dark .custom-scrollbar:hover::-webkit-scrollbar-thumb,
        html.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255, 255, 255, 0.22) !important;
        }

        /* Sidebar Specific: Ultra-discreet thin scrollbar */
        aside .custom-scrollbar,
        aside div[class*="overflow-y-auto"] {
            scrollbar-width: thin !important;
            scrollbar-color: rgba(148, 163, 184, 0.12) transparent !important;
        }

        aside .custom-scrollbar::-webkit-scrollbar,
        aside div[class*="overflow-y-auto"]::-webkit-scrollbar {
            width: 3px !important;
        }

        aside .custom-scrollbar::-webkit-scrollbar-thumb,
        aside div[class*="overflow-y-auto"]::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.12) !important;
            border-radius: 9999px !important;
            transition: background-color 0.2s ease !important;
        }

        aside:hover .custom-scrollbar::-webkit-scrollbar-thumb,
        aside .custom-scrollbar:hover::-webkit-scrollbar-thumb,
        aside:hover div[class*="overflow-y-auto"]::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.32) !important;
        }

        html.dark aside .custom-scrollbar,
        html.dark aside div[class*="overflow-y-auto"] {
            scrollbar-color: rgba(255, 255, 255, 0.05) transparent !important;
        }

        html.dark aside .custom-scrollbar::-webkit-scrollbar-thumb,
        html.dark aside div[class*="overflow-y-auto"]::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-radius: 9999px !important;
        }

        html.dark aside:hover .custom-scrollbar::-webkit-scrollbar-thumb,
        html.dark aside .custom-scrollbar:hover::-webkit-scrollbar-thumb,
        html.dark aside:hover div[class*="overflow-y-auto"]::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.18) !important;
        }

    </style>
</head>

    @php
        $user = auth()->user();
        $roleName = strtolower($user?->role?->name ?? '');
        $isAdminOrRecruiter =
            auth()->check() &&
            (in_array($roleName, ['admin', 'superadmin', 'recruiter']) || in_array($user->role_id, [1, 2]));
        $isAdminSection = request()->is('admin*') || request()->routeIs('admin.*') || request()->is('recruiter*') || request()->routeIs('recruiter.*') || $isAdminOrRecruiter;
        $isEmployee = auth()->check() && ($user->role_id == 4 || $roleName === 'employee');
        $isDeepNavySection = true;
        $isApplicantProfile = auth()->check() && !$isAdminOrRecruiter && !$isEmployee;
        $hasSidebar = $isAdminOrRecruiter || $isApplicantProfile || $isEmployee;
        $defaultTab = $isEmployee ? 'dashboard' : 'pribadi';
        $currentTab = request('tab', $defaultTab);
    @endphp

<body class="font-sans antialiased bg-[#F1F5F1] dark:bg-[#070B14] text-gray-900 dark:text-[#EEEEEE] selection:bg-[#93F514] selection:text-black"
    x-data="{ sidebarOpen: false, activeTab: '{{ $currentTab }}' }"
    x-on:switch-tab.window="activeTab = $event.detail">
    <div class="min-h-screen flex bg-[#F1F5F1] dark:bg-[#070B14]">

        <!-- Sidebar Component Based on Role -->
        @if ($isAdminOrRecruiter)
            <x-sidebar.sidebar />
        @elseif($isEmployee)
            <livewire:employee.employee-sidebar :active-tab="request('tab', 'dashboard')" />
        @elseif($isApplicantProfile)
            <livewire:applicant.applicant-sidebar :active-tab="request('tab', 'pribadi')" />
        @endif

        <!-- Main Content Container -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 {{ $hasSidebar ? 'lg:pl-64' : '' }}">

            <!-- Top Navbar / Header -->
            <header
                class="sticky top-0 z-30 bg-white dark:bg-[#0D1527] dark:border-[#1D2E54] shadow-xs border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <!-- Mobile Hamburger Button (Visible when sidebar present) -->
                    @if ($hasSidebar)
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 mr-2 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#14203A] focus:outline-none">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    @endif

                    <div class="flex-1 min-w-0">
                        @if (isset($header))
                            {{ $header }}
                        @endif
                    </div>

                    <!-- User Navigation / Dropdown -->
                    <div class="flex items-center gap-4">
                        <livewire:layout.navigation />
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
</div>

    <!-- Alpine.js Global Store: Dark Mode sync check -->
    <script>
        if (typeof registerThemeStore === 'function') {
            registerThemeStore();
        }
    </script>

    <!-- Session Heartbeat (Sliding Refresh saat aktif bekerja) -->
    <x-session-heartbeat />
</body>

</html>
