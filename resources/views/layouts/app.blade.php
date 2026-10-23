<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MIKA CAREER') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/mika-favicon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Quill.js Rich Text Editor Assets -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
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
        (function() {
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
        window.toggleMikaTheme = function() {
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

            window.dispatchEvent(new CustomEvent('theme-changed', {
                detail: {
                    isDark: nextIsDark
                }
            }));
            return nextIsDark;
        };

        // Theme store helper for Alpine.js across all lifecycle events
        function registerThemeStore() {
            if (window.Alpine && window.Alpine.store) {
                try {
                    if (!window.Alpine.store('theme')) {
                        window.Alpine.store('theme', {
                            isDark: document.documentElement.classList.contains('dark'),
                            toggle: function() {
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

        window.createAppLayout = function(initialTab) {
            return {
                sidebarOpen: false,
                activeTab: initialTab || 'pribadi',
                pendingTab: null,
                pendingUrl: null,
                showUnsavedModal: false,
                isSavingFromModal: false,

                init: function() {
                    window.__appNav = this;
                },

                isFormDirty: function() {
                    var tab = this.activeTab;
                    var pane = document.querySelector('[data-tab-pane="' + tab + '"]') ||
                               document.querySelector('[data-tab-pane*="' + tab + '"]');
                    if (!pane) {
                        return Boolean(window.__hasUnsavedChanges && document.querySelector('form[data-form-dirty="true"]'));
                    }

                    if (pane.getAttribute('data-tab-dirty') === 'true') return true;
                    if (pane.querySelector('form[data-form-dirty="true"]')) return true;
                    if (pane.querySelector('[data-dirty]')) return true;

                    return false;
                },

                switchTab: function(targetTab) {
                    if (this.activeTab === targetTab) return;

                    if (this.isFormDirty()) {
                        this.pendingTab = targetTab;
                        this.showUnsavedModal = true;
                        return;
                    }

                    this.executeTabSwitch(targetTab);
                },

                executeTabSwitch: function(targetTab) {
                    this.activeTab = targetTab;
                    window.dispatchEvent(new CustomEvent('switch-tab', { detail: targetTab }));
                    if (window.innerWidth < 1024) {
                        this.sidebarOpen = false;
                    }
                    try {
                        var url = new URL(window.location);
                        url.searchParams.set('tab', targetTab);
                        window.history.replaceState({}, '', url);
                    } catch (e) {}
                },

                saveAndSwitchTab: function() {
                    if (this.isSavingFromModal) return;

                    var tab = this.activeTab;
                    var pane = document.querySelector('[data-tab-pane="' + tab + '"]') ||
                               document.querySelector('[data-tab-pane*="' + tab + '"]');
                    if (!pane) {
                        this.cancelSwitchTab();
                        return;
                    }

                    var form = pane.querySelector('form');
                    if (!form) {
                        this.cancelSwitchTab();
                        return;
                    }

                    var submitBtn = form.querySelector('button[type="submit"]');
                    this.isSavingFromModal = true;

                    if (submitBtn) {
                        submitBtn.click();
                    } else {
                        form.requestSubmit();
                    }

                    // Fallback timer if commit hook does not fire (stays on current tab)
                    setTimeout(() => {
                        if (this.isSavingFromModal) {
                            this.isSavingFromModal = false;
                            this.showUnsavedModal = false;
                            this.pendingTab = null;
                            this.pendingUrl = null;
                            this.clearDirty();
                        }
                    }, 2000);
                },

                confirmSwitchTab: function() {
                    this.clearDirty();
                    this.showUnsavedModal = false;
                    this.isSavingFromModal = false;
                    if (this.pendingUrl) {
                        var dest = this.pendingUrl;
                        this.pendingUrl = null;
                        window.location.href = dest;
                        return;
                    }
                    if (this.pendingTab) {
                        var tab = this.pendingTab;
                        this.pendingTab = null;
                        var url = new URL(window.location.href);
                        url.searchParams.set('tab', tab);
                        window.location.href = url.toString();
                        return;
                    }
                },

                cancelSwitchTab: function() {
                    this.showUnsavedModal = false;
                    this.pendingTab = null;
                    this.pendingUrl = null;
                    this.isSavingFromModal = false;
                },

                clearDirty: function() {
                    window.__hasUnsavedChanges = false;
                    var tab = this.activeTab;
                    var pane = document.querySelector('[data-tab-pane="' + tab + '"]') ||
                               document.querySelector('[data-tab-pane*="' + tab + '"]');
                    if (pane) {
                        pane.removeAttribute('data-tab-dirty');
                        pane.querySelectorAll('[data-form-dirty]').forEach(function(el) {
                            el.removeAttribute('data-form-dirty');
                        });
                        pane.querySelectorAll('[data-dirty]').forEach(function(el) {
                            el.removeAttribute('data-dirty');
                        });
                    }
                    document.querySelectorAll('[data-tab-dirty]').forEach(function(el) {
                        el.removeAttribute('data-tab-dirty');
                    });
                    document.querySelectorAll('[data-form-dirty]').forEach(function(el) {
                        el.removeAttribute('data-form-dirty');
                    });
                }
            };
        };

        window.appLayout = window.createAppLayout;

        window.switchProfileTab = function(targetTab) {
            if (window.__appNav && typeof window.__appNav.switchTab === 'function') {
                window.__appNav.switchTab(targetTab);
            }
        };

        function registerAppLayout() {
            if (window.Alpine && window.Alpine.data) {
                try {
                    window.Alpine.data('appLayout', window.createAppLayout);
                } catch (e) {}
            }
        }

        document.addEventListener('alpine:init', registerAppLayout);
        document.addEventListener('livewire:init', registerAppLayout);
        document.addEventListener('livewire:navigated', registerAppLayout);
        if (window.Alpine) {
            registerAppLayout();
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
        html.dark body>.min-h-screen,
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
        html.dark .divide-gray-100> :not([hidden])~ :not([hidden]),
        html.dark .divide-gray-700> :not([hidden])~ :not([hidden]),
        html.dark .divide-slate-800> :not([hidden])~ :not([hidden]),
        html.dark .divide-slate-800\/60> :not([hidden])~ :not([hidden]) {
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
        html.dark .bg-gray-100.dark\:bg-slate-800:not(.rounded-full):not([class*="h-1"]):not([class*="h-2"]):not(.progress-track) {
            background-color: #14203A !important;
            border: 1px solid #253966 !important;
            color: #DDE5F5 !important;
        }

        /* Progress Bar Track: Flat, Clean & Seamless (Tanpa Outline di Dark Mode & Light Mode) */
        .progress-track,
        html.dark .progress-track,
        .progress-track > div,
        html.dark .progress-track > div {
            border: none !important;
            border-width: 0 !important;
            outline: none !important;
            box-shadow: none !important;
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
    $hasRecruiterAccess = (bool) ($user?->is_recruiter ?? false);
    $isRecruiterRoute = request()->is('recruiter*') || request()->routeIs('recruiter.*');
    $isAdminRoute = request()->is('admin*') || request()->routeIs('admin.*');

    $isAdminOrRecruiter =
        auth()->check() &&
        (in_array($roleName, ['admin', 'superadmin', 'recruiter']) ||
            in_array($user->role_id, [1, 2]) ||
            ($hasRecruiterAccess && $isRecruiterRoute));
    $isAdminSection = $isAdminRoute || $isRecruiterRoute || $isAdminOrRecruiter;
    $isEmployee = auth()->check() && ($user->role_id == 4 || $roleName === 'employee') && !$isRecruiterRoute;
    $isDeepNavySection = true;
    $isApplicantProfile = auth()->check() && !$isAdminOrRecruiter && !$isEmployee;
    $hasSidebar = $isAdminOrRecruiter || $isApplicantProfile || $isEmployee;
    $defaultTab = $isEmployee ? 'dashboard' : 'pribadi';
    $currentTab = request('tab', $defaultTab);
@endphp

<body
    class="font-sans antialiased bg-[#F1F5F1] dark:bg-[#070B14] text-gray-900 dark:text-[#EEEEEE] selection:bg-[#93F514] selection:text-black"
    x-data="appLayout('{{ $currentTab }}')"
    x-on:request-switch-tab.window="switchTab($event.detail)">
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
                    <div class="flex items-center gap-2 sm:gap-4 shrink-0">
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

    <!-- Locale Switcher Loader (4-Dot Wave Loader identik halaman login) -->
    <x-locale-loader />

    <!-- Page Ready Guard to prevent toast on initial page load / language switch -->
    <script>
        window.__pageReady = false;
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(() => { window.__pageReady = true; }, 600);
        } else {
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => { window.__pageReady = true; }, 600);
            });
        }
    </script>

    <!-- Toast Peringatan Tanda Data Telah Disimpan / Diperbarui / Dihapus -->
    <div x-data="{
            show: false,
            message: '{{ __('Perubahan data Anda telah berhasil disimpan.') }}',
            title: '{{ __('Data Berhasil Disimpan') }}',
            type: 'update',
            timeout: null,
            notify(msg, customTitle, customType) {
                if (msg) this.message = msg;

                // Smart auto-detection if customType is not explicitly provided
                let detectedType = customType;
                if (!detectedType) {
                    const lower = (msg || '').toLowerCase();
                    if (lower.includes('hapus') || lower.includes('delet') || lower.includes('hilang') || lower.includes('remov')) {
                        detectedType = 'delete';
                    } else if (lower.includes('tambah') || lower.includes('creat') || lower.includes('unggah') || lower.includes('upload') || lower.includes('add')) {
                        detectedType = 'create';
                    } else {
                        detectedType = 'update';
                    }
                }
                this.type = detectedType;

                // Smart title detection
                if (customTitle) {
                    this.title = customTitle;
                } else {
                    if (this.type === 'delete') {
                        this.title = '{{ __('Data Berhasil Dihapus') }}';
                    } else if (this.type === 'create') {
                        this.title = '{{ __('Data Berhasil Ditambahkan') }}';
                    } else {
                        this.title = '{{ __('Data Berhasil Diperbarui') }}';
                    }
                }

                this.show = true;
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => { this.show = false; }, 4000);
            }
         }"
         x-on:data-saved.window="if (window.__pageReady) notify($event.detail?.message, $event.detail?.title, $event.detail?.action || $event.detail?.type)"
         x-init="
            let lastToastTime = 0;
            window.showSavedToast = (msg, title, actionType) => {
                if (!window.__pageReady) return;
                const now = Date.now();
                if (now - lastToastTime < 1000) return;
                lastToastTime = now;
                notify(msg, title, actionType);
            };
         "
         x-show="show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="-translate-y-4 sm:translate-y-0 sm:translate-x-4 opacity-0"
         x-transition:enter-end="translate-y-0 sm:translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 sm:translate-x-0 opacity-100"
         x-transition:leave-end="-translate-y-4 sm:translate-y-0 sm:translate-x-4 opacity-0"
         class="fixed top-20 inset-x-3 sm:inset-x-auto sm:right-6 z-50 w-auto sm:w-full sm:max-w-sm pointer-events-auto"
         x-cloak>
        <div class="bg-white/95 dark:bg-[#0D1527]/95 backdrop-blur-md rounded-2xl shadow-xl p-3.5 sm:p-4 flex items-center sm:items-start gap-3 sm:gap-3.5 ring-1 ring-black/5 dark:ring-white/10 transition-colors"
             :class="{
                'border border-rose-200/90 dark:border-rose-900/60': type === 'delete',
                'border border-emerald-200/90 dark:border-[#1D2E54]': type !== 'delete'
             }">
            <!-- Badge Icon based on action type -->
            <template x-if="type === 'delete'">
                <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
            </template>
            <template x-if="type === 'create'">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 flex items-center justify-center text-emerald-600 dark:text-[#93F514] shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
            </template>
            <template x-if="type !== 'delete' && type !== 'create'">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 flex items-center justify-center text-emerald-600 dark:text-[#93F514] shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </template>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <h4 class="text-xs sm:text-sm font-bold flex items-center gap-1.5 leading-snug"
                    :class="type === 'delete' ? 'text-rose-600 dark:text-rose-400' : 'text-gray-900 dark:text-white'"
                    x-text="title">
                    {{ __('Data Berhasil Disimpan') }}
                </h4>
                <p class="text-[11px] sm:text-xs text-gray-500 dark:text-[#93A5C9] mt-0.5 leading-relaxed line-clamp-2 break-words" x-text="message">
                    {{ __('Perubahan data Anda telah berhasil disimpan.') }}
                </p>
            </div>

            <!-- Close Button -->
            <button type="button" @click="show = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-white p-2 sm:p-1.5 -mr-1 rounded-xl hover:bg-gray-100 dark:hover:bg-[#14203A] transition shrink-0 active:scale-95"
                    aria-label="{{ __('Tutup') }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    @if ($isApplicantProfile || request()->routeIs('profile'))
        <!-- Modal Peringatan Data Belum Disimpan (Simple & Elegant Enterprise Blueprint) -->
        <div x-show="showUnsavedModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop with subtle blur -->
                <div class="fixed inset-0 transition-opacity bg-gray-900/75 dark:bg-gray-950/80 backdrop-blur-sm"
                     x-show="showUnsavedModal"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="cancelSwitchTab()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content Card -->
                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-[#1D2E54] p-6 relative z-10"
                     x-show="showUnsavedModal"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <!-- Close Button (X) -->
                    <button type="button" @click="cancelSwitchTab()"
                            class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- Warning Icon Badge -->
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800/60 flex items-center justify-center text-amber-600 dark:text-amber-400 mx-auto mb-4 shadow-sm">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>

                    <!-- Title & Message -->
                    <h3 class="text-base font-bold text-gray-900 dark:text-white text-center">
                        {{ __('Data Belum Disimpan') }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-[#93A5C9] mt-2 text-center leading-relaxed">
                        {{ __('Ada perubahan data yang belum disimpan. Pilih apakah Anda ingin menyimpan perubahan terlebih dahulu atau langsung meninggalkan halaman.') }}
                    </p>

                    <!-- Buttons: Batal, Tinggalkan Halaman, Simpan Perubahan -->
                    <div class="mt-6 flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5">
                        <button type="button" @click="cancelSwitchTab()"
                                class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 dark:border-[#1D2E54] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#14203A] text-xs font-semibold transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="button" @click="confirmSwitchTab()"
                                class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/30 dark:hover:bg-rose-950/50 text-rose-600 dark:text-rose-400 text-xs font-bold transition active:scale-95">
                            {{ __('Tinggalkan Halaman') }}
                        </button>
                        <button type="button" @click="saveAndSwitchTab()" :disabled="isSavingFromModal"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-70 disabled:cursor-not-allowed text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95 cursor-pointer">
                            <template x-if="!isSavingFromModal">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="isSavingFromModal">
                                <svg class="w-4 h-4 animate-spin shrink-0 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSavingFromModal ? '{{ __('Menyimpan...') }}' : '{{ __('Simpan Perubahan') }}'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Dirty Form Tracking & Interception -->
        <script>
            window.__hasUnsavedChanges = false;
            window.__wasFormSubmitted = false;

            document.addEventListener('input', (e) => {
                if (e.target.type === 'search' || e.target.name === 'search') return;
                const pane = e.target.closest('[data-tab-pane]');
                if (pane) pane.setAttribute('data-tab-dirty', 'true');
                const form = e.target.closest('form');
                if (form) form.setAttribute('data-form-dirty', 'true');
                window.__hasUnsavedChanges = true;
            });

            document.addEventListener('change', (e) => {
                if (e.target.type === 'search' || e.target.name === 'search') return;
                const pane = e.target.closest('[data-tab-pane]');
                if (pane) pane.setAttribute('data-tab-dirty', 'true');
                const form = e.target.closest('form');
                if (form) form.setAttribute('data-form-dirty', 'true');
                window.__hasUnsavedChanges = true;
            });

            document.addEventListener('submit', (e) => {
                const pane = e.target.closest('[data-tab-pane]');
                if (pane) pane.removeAttribute('data-tab-dirty');
                const form = e.target.closest('form');
                if (form) form.removeAttribute('data-form-dirty');
                window.__hasUnsavedChanges = false;
                window.__wasFormSubmitted = true;
            });

            window.addEventListener('beforeunload', (e) => {
                if (window.__appNav && typeof window.__appNav.isFormDirty === 'function' && window.__appNav.isFormDirty()) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href]:not([target="_blank"])');
                if (!link) return;
                if (link.hasAttribute('x-on:click.prevent') || link.getAttribute('@click.prevent')) return;
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

                if (window.__appNav && typeof window.__appNav.isFormDirty === 'function' && window.__appNav.isFormDirty()) {
                    e.preventDefault();
                    window.__appNav.pendingUrl = href;
                    window.__appNav.showUnsavedModal = true;
                }
            });

            window.addEventListener('profile-updated', (e) => {
                if (!window.__pageReady) return;
                window.__hasUnsavedChanges = false;
                document.querySelectorAll('[data-form-dirty]').forEach(el => el.removeAttribute('data-form-dirty'));
                document.querySelectorAll('[data-tab-dirty]').forEach(el => el.removeAttribute('data-t                      rty'));

                if (typeof window.showSavedToast === 'function') {
                    const msg = (e.detail && e.detail.message) ? e.detail.message : '{{ __('Perubahan data Anda telah berhasil disimpan.') }}';
                    const title = (e.detail && e.detail.title) ? e.detail.title : null;
                    const action = (e.detail && (e.detail.action || e.detail.type)) ? (e.detail.action || e.detail.type) : null;
                    window.showSavedToast(msg, title, action);
                }
            });

            document.addEventListener('livewire:init', () => {
                Livewire.hook('commit', ({ succeed, fail }) => {
                    succeed(() => {
                        if (window.__appNav && window.__appNav.isSavingFromModal) {
                            setTimeout(() => {
                                const nav = window.__appNav;
                                if (!nav || !nav.isSavingFromModal) return;

                                const tab = nav.activeTab;
                                const pane = document.querySelector('[data-tab-pane=\"' + tab + '\"]') ||
                                             document.querySelector('[data-tab-pane*=\"' + tab + '\"]');
                                const errorEls = pane ? pane.querySelectorAll('.text-red-500:not(label *):not(label), .text-rose-500:not(label *):not(label)') : [];
                                let hasError = false;
                                errorEls.forEach(el => {
                                    const text = el.textContent.trim();
                                    if (text.length > 1 && text !== '*') {
                                        hasError = true;
                                    }
                                });

                                const targetTab = nav.pendingTab;
                                const targetUrl = nav.pendingUrl;

                                nav.isSavingFromModal = false;
                                nav.showUnsavedModal = false;
                                nav.pendingTab = null;
                                nav.pendingUrl = null;

                                if (hasError) {
                                    return;
                                }

                                if (typeof window.showSavedToast === 'function') {
                                    window.showSavedToast('{{ __('Perubahan data Anda telah berhasil disimpan.') }}', '{{ __('Data Berhasil Diperbarui') }}', 'update');
                                }
                                nav.clearDirty();

                                if (targetUrl) {
                                    window.location.href = targetUrl;
                                } else if (targetTab) {
                                    nav.executeTabSwitch(targetTab);
                                }
                            }, 150);
                        } else {
                            if (window.__wasFormSubmitted) {
                                window.__wasFormSubmitted = false;
                                if (window.__pageReady && typeof window.showSavedToast === 'function') {
                                    window.showSavedToast('{{ __('Perubahan data Anda telah berhasil disimpan.') }}', '{{ __('Data Berhasil Diperbarui') }}', 'update');
                                }
                            }
                            window.__hasUnsavedChanges = false;
                            document.querySelectorAll('[data-form-dirty]').forEach(el => el.removeAttribute('data-form-dirty'));
                            document.querySelectorAll('[data-tab-dirty]').forEach(el => el.removeAttribute('data-tab-dirty'));
                        }
                    });
                    fail(() => {
                        if (window.__appNav) {
                            window.__appNav.isSavingFromModal = false;
                            window.__appNav.showUnsavedModal = false;
                        }
                    });
                });
            });
        </script>
    @endif
</body>

</html>
