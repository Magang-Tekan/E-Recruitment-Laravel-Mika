<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth" id="html-root">
<script>
    // Early theme detection — runs before CSS renders to prevent flash
    (function() {
        var mikaTheme = localStorage.getItem('mika-theme');
        var html = document.getElementById('html-root');
        if (mikaTheme === 'light') {
            html.classList.add('light-mode');
            html.classList.remove('dark');
        } else {
            html.classList.remove('light-mode');
            html.classList.add('dark');
        }
    })();
</script>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mika Career | Temukan Karir Impian Masa Depanmu')</title>

    <!-- Meta / SEO / OpenGraph Tags -->
    @if(View::hasSection('meta'))
        @yield('meta')
    @else
        <meta name="description" content="Platform rekrutmen digital terintegrasi untuk menemukan karir terbaik di PT Mitra Karya Analitika.">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Mika Career">
        <meta property="og:title" content="Mika Career | Temukan Karir Impian Masa Depanmu">
        <meta property="og:description" content="Platform rekrutmen digital terintegrasi untuk menemukan karir terbaik di PT Mitra Karya Analitika.">
        <meta property="og:image" content="{{ asset('images/mikaaaa.png') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Mika Career | Temukan Karir Impian Masa Depanmu">
        <meta name="twitter:description" content="Platform rekrutmen digital terintegrasi untuk menemukan karir terbaik di PT Mitra Karya Analitika.">
        <meta name="twitter:image" content="{{ asset('images/mikaaaa.png') }}">
    @endif
    @stack('meta')

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/mika-favicon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Tailwind CSS CDN Fallback for instant styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --primary-green: #93F514;
            --primary-green-glow: rgba(147, 245, 20, 0.4);
            --primary-green-dark: #6bbd08;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Custom Neon Green #93F514 Accents */
        .text-neon-green {
            color: #93F514;
            text-shadow: 0 0 20px rgba(147, 245, 20, 0.6);
        }

        .bg-neon-green {
            background-color: #93F514;
        }

        .glow-green {
            box-shadow: 0 0 35px -5px rgba(147, 245, 20, 0.45);
        }

        .glow-green-lg {
            box-shadow: 0 0 80px -10px rgba(147, 245, 20, 0.55);
        }

        .border-neon-green {
            border-color: rgba(147, 245, 20, 0.4);
            box-shadow: 0 0 15px rgba(147, 245, 20, 0.2);
        }

        .bg-mesh-dark {
            background-color: #040804;
        }

        /* Lightweight Page Entrance Animation */
        @keyframes pageFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-page-fade {
            animation: pageFadeIn 0.55s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes pulseGlow {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.8;
            }

            50% {
                transform: scale(1.08);
                opacity: 1;
                filter: drop-shadow(0 0 16px rgba(147, 245, 20, 0.8));
            }
        }

        .animate-brand-pulse {
            animation: pulseGlow 1.2s ease-in-out infinite;
        }

        /* Scroll Reveal Animation Engine (Hardware-Accelerated & 60fps Smooth) */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
            backface-visibility: hidden;
        }

        .reveal-on-scroll.is-revealed {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* Directional and Scale Variants */
        .reveal-slide-left {
            transform: translateX(-32px);
        }
        .reveal-slide-left.is-revealed {
            transform: translateX(0);
        }

        .reveal-slide-right {
            transform: translateX(32px);
        }
        .reveal-slide-right.is-revealed {
            transform: translateX(0);
        }

        .reveal-scale {
            transform: scale(0.94);
        }
        .reveal-scale.is-revealed {
            transform: scale(1);
        }

        .reveal-fade {
            transform: none;
        }
        .reveal-fade.is-revealed {
            transform: none;
        }

        /* Stagger Delays for Cards & Grids */
        .reveal-on-scroll[data-delay="50"], [data-delay="50"] { transition-delay: 50ms; }
        .reveal-on-scroll[data-delay="100"], [data-delay="100"] { transition-delay: 100ms; }
        .reveal-on-scroll[data-delay="150"], [data-delay="150"] { transition-delay: 150ms; }
        .reveal-on-scroll[data-delay="200"], [data-delay="200"] { transition-delay: 200ms; }
        .reveal-on-scroll[data-delay="250"], [data-delay="250"] { transition-delay: 250ms; }
        .reveal-on-scroll[data-delay="300"], [data-delay="300"] { transition-delay: 300ms; }
        .reveal-on-scroll[data-delay="350"], [data-delay="350"] { transition-delay: 350ms; }
        .reveal-on-scroll[data-delay="400"], [data-delay="400"] { transition-delay: 400ms; }
        .reveal-on-scroll[data-delay="500"], [data-delay="500"] { transition-delay: 500ms; }

        /* Accessibility: respect users who prefer reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .reveal-on-scroll {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
            .animate-page-fade {
                animation: none !important;
            }
        }

        /* ============================================================
           LIGHT MODE OVERRIDES
           Aktif ketika <html> memiliki class "light-mode"
        ============================================================ */

        /* --- Body & Page Background --- */
        html.light-mode body {
            background-color: #F1F5F1 !important;
            color: #111827 !important;
        }

        /* --- Loader Overlay --- */
        html.light-mode .fixed.inset-0.z-\[100\] {
            background-color: #F1F5F1 !important;
        }

        /* --- Footer: TETAP GELAP untuk kontras premium --- */
        /* (Footer tidak perlu override, warna gelap aslinya tampil bagus di light mode) */

        /* --- Navbar scrolled (pill container only) --- */
        html.light-mode header>div.rounded-full,
        html.light-mode header>div[class*="max-w-5xl"] {
            background-color: rgba(255, 255, 255, 0.96) !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.10), 0 1px 4px rgba(0, 0, 0, 0.06) !important;
        }

        /* Mobile drawer */
        html.light-mode header>div.rounded-3xl,
        html.light-mode header>div[class*="max-w-sm"] {
            background-color: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.14) !important;
        }

        /* Navbar non-scrolled border */
        html.light-mode header .border-b {
            border-color: rgba(0, 0, 0, 0.07) !important;
        }

        /* Navbar navigation links in light mode */
        html.light-mode header nav a {
            color: #787d85ff !important;
        }

        html.light-mode header nav a:hover {
            color: #5f5f5fff !important;
        }

        html.light-mode header nav a[class*="text-[#93F514]"] {
            color: #2D6B00 !important;
            filter: none !important;
        }

        /* Navbar User Profile Capsule & Dropdown in Light Mode */
        html.light-mode .nav-profile-pill {
            background-color: rgba(243, 244, 246, 0.95) !important;
            border-color: rgba(0, 0, 0, 0.12) !important;
            color: #1f2937 !important;
        }
        html.light-mode .nav-profile-pill:hover {
            background-color: #e5e7eb !important;
            border-color: rgba(95, 178, 14, 0.6) !important;
        }
        html.light-mode .nav-profile-name {
            color: #111827 !important;
        }
        html.light-mode .nav-profile-dropdown {
            background-color: rgba(255, 255, 255, 0.98) !important;
            border-color: rgba(0, 0, 0, 0.1) !important;
            box-shadow: 0 16px 36px -6px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
        }
        html.light-mode .nav-profile-header {
            background-color: #f9fafb !important;
            border-color: #e5e7eb !important;
        }
        html.light-mode .nav-profile-item {
            color: #374151 !important;
        }
        html.light-mode .nav-profile-item:hover {
            background-color: #f0fdf4 !important;
            color: #15803d !important;
        }
        html.light-mode .nav-profile-divider {
            border-color: #e5e7eb !important;
        }
        html.light-mode .nav-profile-email {
            color: #6b7280 !important;
        }
        html.light-mode .nav-profile-role {
            background-color: #dcfce7 !important;
            color: #166534 !important;
            border-color: #bbf7d0 !important;
        }
        html.light-mode .nav-profile-mobile-card {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06) !important;
        }

        /* =====================================================
           BACKGROUND UTILITIES: dark → light
        ===================================================== */

        /* Body / page surface */
        html.light-mode .bg-\[\#040804\] {
            background-color: #F1F5F1 !important;
        }

        /* Footer bg: BIARKAN GELAP — tidak di-override */
        /* html.light-mode .bg-\[\#020602\] → dibiarkan agar footer tetap dark */

        /* Section header banners → sage-green gradient feel */
        html.light-mode .bg-\[\#051c05\] {
            background-color: #EFF8E6 !important;
        }

        html.light-mode .bg-\[\#072907\] {
            background-color: #E4F5D6 !important;
        }

        html.light-mode .bg-\[\#031103\] {
            background-color: #F4FDE8 !important;
        }

        /* Cards → putih bersih dengan shadow */
        html.light-mode .bg-\[\#061506\] {
            background-color: #FFFFFF !important;
        }

        html.light-mode .bg-\[\#030803\] {
            background-color: #FBFFFE !important;
        }

        html.light-mode .bg-\[\#030a03\] {
            background-color: #FBFFFE !important;
        }

        html.light-mode .bg-\[\#050c05\] {
            background-color: #FFFFFF !important;
        }

        /* Subtle surface & icon containers */
        html.light-mode .bg-\[\#050e05\] {
            background-color: #F2FAE8 !important;
        }

        html.light-mode .bg-\[\#051205\] {
            background-color: #EBF7D8 !important;
        }

        html.light-mode .bg-\[\#041404\] {
            background-color: #E4F5C8 !important;
        }

        /* =====================================================
           MODAL KONFIRMASI LAMARAN (LIGHT MODE)
        ===================================================== */
        html.light-mode .confirm-modal-card {
            background-color: #FFFFFF !important;
            border-color: rgba(0, 0, 0, 0.10) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.20) !important;
        }

        html.light-mode .confirm-modal-card h3 {
            color: #111827 !important;
        }

        html.light-mode .confirm-modal-card p {
            color: #4B5563 !important;
        }

        /* Summary Info Box: Putih Bersih di Light Mode */
        html.light-mode .confirm-summary-box,
        html.light-mode .bg-\[\#040a04\] {
            background-color: #FFFFFF !important;
            background: #FFFFFF !important;
            border: 1px solid #E5E7EB !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .confirm-summary-box .border-b {
            border-color: #F3F4F6 !important;
        }

        html.light-mode .confirm-summary-box span.text-gray-400,
        html.light-mode .confirm-summary-box span.text-gray-500 {
            color: #6B7280 !important;
        }

        html.light-mode .confirm-summary-box .job-title-val {
            color: #111827 !important;
        }

        html.light-mode .confirm-summary-box .company-val {
            color: #2D6B00 !important;
        }

        html.light-mode .confirm-summary-box .placement-val {
            color: #374151 !important;
        }

        html.light-mode .confirm-summary-box .applicant-val {
            color: #111827 !important;
        }

        /* Statement Alert Box */
        html.light-mode .confirm-statement-box {
            background-color: #F4FDE8 !important;
            border-color: rgba(147, 245, 20, 0.5) !important;
            color: #374151 !important;
        }

        html.light-mode .confirm-statement-box svg {
            color: #2D6B00 !important;
        }

        html.light-mode .confirm-statement-box p {
            color: #374151 !important;
        }

        /* Batal Button */
        html.light-mode .confirm-cancel-btn {
            background-color: #F3F4F6 !important;
            border-color: #D1D5DB !important;
            color: #374151 !important;
        }

        html.light-mode .confirm-cancel-btn:hover {
            background-color: #E5E7EB !important;
            color: #111827 !important;
        }

        /* Ya, Kirim Lamaran Button */
        html.light-mode .confirm-submit-btn {
            background-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 4px 14px rgba(147, 245, 20, 0.40) !important;
        }

        html.light-mode .confirm-submit-btn:hover {
            background-color: #82DC0A !important;
            color: #000000 !important;
        }

        html.light-mode .confirm-submit-btn svg {
            color: #000000 !important;
            stroke: #000000 !important;
        }

        /* Incomplete Modal Card */
        html.light-mode .incomplete-modal-card,
        html.light-mode .bg-\[\#0a0707\] {
            background-color: #FFFFFF !important;
            border-color: rgba(245, 158, 11, 0.4) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.20) !important;
        }

        html.light-mode .incomplete-modal-card h3 {
            color: #111827 !important;
        }

        html.light-mode .incomplete-modal-card p {
            color: #4B5563 !important;
        }

        html.light-mode .incomplete-summary-box,
        html.light-mode .bg-\[\#140c0c\] {
            background-color: #FFFDF5 !important;
            border: 1px solid rgba(245, 158, 11, 0.3) !important;
        }

        /* Active/selected company card */
        html.light-mode .bg-\[\#0a2e0a\] {
            background-color: #CCEFAA !important;
        }

        /* CTA button #93F514 di light mode: tetap warna neon #93F514 solid + teks hitam pekat */
        html.light-mode .bg-\[\#93F514\],
        html.light-mode button.bg-\[\#93F514\],
        html.light-mode a.bg-\[\#93F514\] {
            background-color: #93F514 !important;
            color: #000000 !important;
        }

        html.light-mode .bg-\[\#93F514\]:hover,
        html.light-mode button.bg-\[\#93F514\]:hover,
        html.light-mode a.bg-\[\#93F514\]:hover {
            background-color: #82dc0a !important;
            color: #000000 !important;
        }

        /* "Cari Lowongan" & "Pantau Status" submit button: normal hitam pekat + teks putih & icon neon, hover warna neon #93F514 + teks & icon hitam */
        html.light-mode .bg-\[\#051405\],
        html.light-mode a.bg-\[\#051405\],
        html.light-mode button.bg-\[\#051405\] {
            background-color: #051405 !important;
            color: #FFFFFF !important;
            border-color: rgba(147, 245, 20, 0.45) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.20) !important;
        }

        html.light-mode .bg-\[\#051405\] span,
        html.light-mode a.bg-\[\#051405\] span,
        html.light-mode button.bg-\[\#051405\] span,
        html.light-mode .bg-\[\#051405\].text-\[\#EEEEEE\],
        html.light-mode .bg-\[\#051405\].text-white,
        html.light-mode .bg-\[\#051405\].text-gray-300 {
            color: #FFFFFF !important;
        }

        html.light-mode .bg-\[\#051405\] svg {
            color: #93F514 !important;
        }

        html.light-mode .bg-\[\#051405\]:hover,
        html.light-mode a.bg-\[\#051405\]:hover,
        html.light-mode button.bg-\[\#051405\]:hover {
            background-color: #93F514 !important;
            border-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 6px 22px rgba(147, 245, 20, 0.60) !important;
        }

        html.light-mode .bg-\[\#051405\]:hover span,
        html.light-mode a.bg-\[\#051405\]:hover span,
        html.light-mode button.bg-\[\#051405\]:hover span,
        html.light-mode .bg-\[\#051405\]:hover svg,
        html.light-mode .bg-\[\#051405\]:hover.text-\[\#EEEEEE\],
        html.light-mode .bg-\[\#051405\]:hover.text-white,
        html.light-mode .bg-\[\#051405\]:hover.text-gray-300 {
            color: #000000 !important;
            stroke: #000000;
        }

        /* "Daftar" button (Navbar desktop & mobile): normal hitam pekat + teks putih, hover warna neon #93F514 + teks hitam (sama seperti Cari Lowongan) */
        html.light-mode .bg-\[\#061806\],
        html.light-mode .bg-\[\#061806\].text-\[\#EEEEEE\],
        html.light-mode header a[href*="register"] {
            background-color: #061806 !important;
            color: #FFFFFF !important;
            border-color: rgba(147, 245, 20, 0.50) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.20) !important;
        }

        html.light-mode .bg-\[\#061806\]:hover,
        html.light-mode .bg-\[\#061806\]:hover.text-\[\#EEEEEE\],
        html.light-mode header a[href*="register"]:hover {
            background-color: #93F514 !important;
            border-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 6px 22px rgba(147, 245, 20, 0.60) !important;
        }

        /* =====================================================
           GRADIENT OVERRIDES: from / via / to
           (Hanya gradient section header & card, BUKAN hero overlay)
        ===================================================== */

        /* Section header (page banner) gradients → sage lime */
        html.light-mode .from-\[\#051c05\] {
            --tw-gradient-from: #EFF8E6 var(--tw-gradient-from-position, ) !important;
        }

        html.light-mode .from-\[\#072907\] {
            --tw-gradient-from: #E4F5D6 var(--tw-gradient-from-position, ) !important;
        }

        html.light-mode .from-\[\#031103\] {
            --tw-gradient-from: #F4FDE8 var(--tw-gradient-from-position, ) !important;
        }

        html.light-mode .via-\[\#072907\] {
            --tw-gradient-via: #DCF0C8 var(--tw-gradient-via-position, ) !important;
        }

        html.light-mode .to-\[\#031103\] {
            --tw-gradient-to: #F4FDE8 var(--tw-gradient-to-position, ) !important;
        }

        /* Card gradients → putih ke off-white */
        html.light-mode .from-\[\#061506\] {
            --tw-gradient-from: #FFFFFF var(--tw-gradient-from-position, ) !important;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.07), 0 1px 4px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .from-\[\#061506\]:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.11), 0 2px 8px rgba(0, 0, 0, 0.06) !important;
        }

        html.light-mode .from-\[\#030803\] {
            --tw-gradient-from: #FBFFFE var(--tw-gradient-from-position, ) !important;
        }

        html.light-mode .from-\[\#030a03\] {
            --tw-gradient-from: #F2FAE8 var(--tw-gradient-from-position, ) !important;
        }

        html.light-mode .to-\[\#030803\] {
            --tw-gradient-to: #F5FFF0 var(--tw-gradient-to-position, ) !important;
        }

        html.light-mode .to-\[\#030a03\] {
            --tw-gradient-to: #EFF8E5 var(--tw-gradient-to-position, ) !important;
        }

        html.light-mode .to-\[\#041404\] {
            --tw-gradient-to: #E4F5C8 var(--tw-gradient-to-position, ) !important;
        }

        /* Active company card gradient */
        html.light-mode .from-\[\#0a2e0a\] {
            --tw-gradient-from: #CCEFAA var(--tw-gradient-from-position, ) !important;
        }

        /* =====================================================
           COMPANY MITRA CARDS (Temukan Lowongan Berdasarkan Perusahaan)
           Warna Putih Bersih (#FFFFFF)
        ===================================================== */
        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030a03\] {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 2px 14px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03) !important;
        }

        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030a03\]:hover {
            background: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.50) !important;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.09), 0 2px 6px rgba(0, 0, 0, 0.04) !important;
        }

        /* Active Company Card */
        html.light-mode .bg-gradient-to-b.from-\[\#0a2e0a\].to-\[\#041404\] {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: #93F514 !important;
            box-shadow: 0 4px 16px rgba(147, 245, 20, 0.25) !important;
        }

        /* Logo box inside Company Card */
        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030a03\] .w-14.h-14,
        html.light-mode .bg-gradient-to-b.from-\[\#0a2e0a\].to-\[\#041404\] .w-14.h-14 {
            background-color: #F8FAFC !important;
            border-color: rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        }

        /* Company Card Texts */
        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030a03\] h3,
        html.light-mode .bg-gradient-to-b.from-\[\#0a2e0a\].to-\[\#041404\] h3 {
            color: #111827 !important;
        }

        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030a03\]:hover h3 {
            color: #2D6B00 !important;
        }

        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030a03\] p,
        html.light-mode .bg-gradient-to-b.from-\[\#0a2e0a\].to-\[\#041404\] p {
            color: #4B5563 !important;
        }

        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030a03\] .text-\[\#93F514\],
        html.light-mode .bg-gradient-to-b.from-\[\#0a2e0a\].to-\[\#041404\] .text-\[\#93F514\] {
            color: #2D6B00 !important;
        }

        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030a03\] .border-t,
        html.light-mode .bg-gradient-to-b.from-\[\#0a2e0a\].to-\[\#041404\] .border-t {
            border-color: rgba(0, 0, 0, 0.06) !important;
        }

        /* =====================================================
           JOB CARDS (Main Listing Grid)
        ===================================================== */
        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030803\] {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 3px 18px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030803\]:hover {
            border-color: rgba(45, 107, 0, 0.5) !important;
            box-shadow: 0 8px 30px rgba(45, 107, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06) !important;
        }

        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030803\] h3 {
            color: #111827 !important;
        }

        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030803\]:hover h3 {
            color: #2D6B00 !important;
        }

        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030803\] p {
            color: #4B5563 !important;
        }

        /* Stats mini card (from-[#061506] to-[#040804]) — to-[#040804] jangan dioverride
           karena itu juga dipakai hero overlay. Override via bg class sudah cukup. */

        /* =====================================================
           TEXT COLORS
        ===================================================== */

        /* Primary text: gelap dan tajam */
        html.light-mode .text-\[\#EEEEEE\] {
            color: #111827 !important;
        }

        html.light-mode .text-gray-200 {
            color: #374151 !important;
        }

        html.light-mode .text-gray-300 {
            color: #4B5563 !important;
        }

        html.light-mode .text-gray-400 {
            color: #6B7280 !important;
        }

        html.light-mode .text-gray-500 {
            color: #374151 !important;
        }

        /* Accent text: #93F514 terlalu terang di bg putih → gelapkan */
        html.light-mode .text-\[\#93F514\] {
            color: #3D7A00 !important;
        }

        /* Footer text: biarkan tetap terang (footer background tetap gelap) */
        html.light-mode footer .text-\[\#EEEEEE\] {
            color: #EEEEEE !important;
        }

        html.light-mode footer .text-gray-300 {
            color: #D1D5DB !important;
        }

        html.light-mode footer .text-gray-400 {
            color: #9CA3AF !important;
        }

        html.light-mode footer .text-gray-500 {
            color: #6B7280 !important;
        }

        html.light-mode footer .text-\[\#93F514\] {
            color: #93F514 !important;
        }

        /* Gradient text heading (from-[#93F514]) → gelapkan untuk header biasa */
        html.light-mode .from-\[\#93F514\] {
            --tw-gradient-from: #3D7A00 var(--tw-gradient-from-position, ) !important;
        }

        /* =====================================================
           BORDERS & DIVIDERS
        ===================================================== */
        html.light-mode .border-\[\#EEEEEE\]\/10 {
            border-color: rgba(0, 0, 0, 0.07) !important;
        }

        html.light-mode .divide-\[\#EEEEEE\]\/10>*+* {
            border-color: rgba(0, 0, 0, 0.07) !important;
        }

        /* Green border: naikkan opacity agar terlihat di bg putih */
        html.light-mode .border-\[\#93F514\]\/30 {
            border-color: rgba(61, 122, 0, 0.30) !important;
        }

        html.light-mode .border-\[\#93F514\]\/40 {
            border-color: rgba(61, 122, 0, 0.40) !important;
        }

        /* Footer borders: biarkan tetap seperti dark mode */
        html.light-mode footer .border-\[\#93F514\]\/30 {
            border-color: rgba(147, 245, 20, 0.25) !important;
        }

        html.light-mode footer .border-\[\#93F514\]\/20 {
            border-color: rgba(147, 245, 20, 0.20) !important;
        }

        /* =====================================================
           SHADOWS (lebih subtle di bg terang)
        ===================================================== */
        html.light-mode .shadow-black\/80 {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
        }

        html.light-mode .shadow-black\/40 {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08) !important;
        }

        html.light-mode .shadow-black\/25 {
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07) !important;
        }

        html.light-mode .shadow-2xl.shadow-black\/80 {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.14) !important;
        }

        /* =====================================================
           HERO SECTION — Gradasi Putih + Teks Kontras
        ===================================================== */

        /* Hero section bg → putih agar gradasi blend ke body */
        html.light-mode section.bg-\[\#040804\] {
            background-color: #F1F5F1 !important;
        }

        /* Hero gradient overlays → PUTIH (menggantikan gelap) */
        html.light-mode .bg-gradient-to-b.from-\[\#040804\]\/60 {
            background: linear-gradient(to bottom,
                    rgba(241, 245, 241, 0.55) 0%,
                    rgba(241, 245, 241, 0.35) 40%,
                    rgba(241, 245, 241, 0.95) 100%) !important;
        }

        html.light-mode .bg-black\/35 {
            background-color: rgba(255, 255, 255, 0.25) !important;
        }

        html.light-mode .bg-\[radial-gradient\(ellipse_at_center\,_transparent_40\%\,_\#040804_95\%\)\] {
            background: radial-gradient(ellipse at center, transparent 30%, rgba(241, 245, 241, 0.85) 90%) !important;
        }

        /* Ambient green glow → hide in light mode (noisy on white) */
        html.light-mode .bg-\[\#93F514\]\/15.blur-\[100px\] {
            opacity: 0 !important;
        }

        /* Hero heading "Temukan Karir Impian" — hitam tebal */
        html.light-mode section h1.text-\[\#EEEEEE\] {
            color: #0F1F0F !important;
            text-shadow: none !important;
            -webkit-text-stroke: 0 !important;
        }

        /* Hero subtitle "Bergabunglah bersama ribuan..." — gelap terbaca */
        html.light-mode section p.text-gray-200 {
            color: #374151 !important;
            text-shadow: none !important;
        }

        /* Hero text span "Wujudkan Potensi Terbaikmu" — Solid Neon Green #93F514 (Tanpa Gradasi) + Jelas */
        html.light-mode section h1 .bg-gradient-to-r.from-\[\#93F514\] {
            background: none !important;
            background-image: none !important;
            -webkit-background-clip: unset !important;
            background-clip: unset !important;
            -webkit-text-fill-color: #93F514 !important;
            color: #93F514 !important;
            text-shadow:
                0 2px 6px rgba(0, 0, 0, 0.25),
                0 0 10px rgba(147, 245, 20, 0.35),
                0 0 24px rgba(147, 245, 20, 0.2),
                0 0 48px rgba(147, 245, 20, 0.1) !important;
            filter: none !important;
        }

        /* Override via/to gradient vars for this span */
        html.light-mode section h1 .via-\[\#75f06a\] {
            --tw-gradient-via: transparent !important;
        }

        html.light-mode section h1 .to-\[\#5FE6B6\] {
            --tw-gradient-to: transparent !important;
        }

        /* Hero drop-shadow remove di light mode */
        html.light-mode .drop-shadow-\[0_4px_16px_rgba\(0\,0\,0\,0\.8\)\] {
            filter: none !important;
        }

        html.light-mode .drop-shadow-\[0_2px_8px_rgba\(0\,0\,0\,0\.9\)\] {
            filter: none !important;
        }

        html.light-mode .drop-shadow-\[0_0_25px_rgba\(147\,245\,20\,0\.4\)\] {
            filter: none !important;
        }

        /* Hero background image — brighten di light mode */
        html.light-mode section .filter.brightness-90 {
            filter: brightness(1.1) contrast(0.95) saturate(0.8) !important;
        }

        /* =====================================================
           STATS CARDS — Putih + Border Hijau + Shadow
           (Lowongan Aktif, Perusahaan Mitra, dll)
        ===================================================== */

        /* Stats cards: bg putih, border hijau gelap solid, shadow nyata */
        html.light-mode .border-\[\#93F514\]\/30.hover\:border-\[\#93F514\]\/60 {
            background: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.35) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06), 0 1px 4px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .border-\[\#93F514\]\/30.hover\:border-\[\#93F514\]\/60:hover {
            border-color: rgba(45, 107, 0, 0.6) !important;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.09), 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        }

        /* Stats number (3+, 4+, 5+, 9+) — gelap tebal */
        html.light-mode .border-\[\#93F514\]\/30.hover\:border-\[\#93F514\]\/60 .text-\[\#EEEEEE\] {
            color: #0F1F0F !important;
        }

        /* Stats label — hijau gelap readable */
        html.light-mode .border-\[\#93F514\]\/30.hover\:border-\[\#93F514\]\/60 .text-\[\#93F514\] {
            color: #2D6B00 !important;
        }

        /* Stats border-top separator */
        html.light-mode .border-t.border-\[\#93F514\]\/20 {
            border-color: rgba(45, 107, 0, 0.18) !important;
        }

        /* =====================================================
           POPULAR SEARCH TAGS — Kontras di bg terang
        ===================================================== */
        html.light-mode .bg-\[\#051205\] {
            background-color: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.25) !important;
        }

        html.light-mode .bg-\[\#051205\]:hover {
            background-color: #EBF7D8 !important;
        }

        /* =====================================================
           SHOWCASE / CAROUSEL SECTION
           (Big dark rounded card with company photos)
        ===================================================== */

        /* Main carousel container bg → putih bersih */
        html.light-mode .bg-gradient-to-b.from-\[\#040804\].via-\[\#051405\].to-\[\#040804\] {
            background: linear-gradient(to bottom, #FFFFFF, #F7FAF4, #FFFFFF) !important;
            border-color: rgba(45, 107, 0, 0.25) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.06), 0 1px 4px rgba(0, 0, 0, 0.04) !important;
        }

        /* Carousel dot pattern overlay → hide di light mode */
        html.light-mode .opacity-\[0\.07\] {
            opacity: 0.03 !important;
        }

        /* Carousel ambient glow blobs → hide */
        html.light-mode .bg-\[\#93F514\]\/10.blur-\[100px\],
        html.light-mode .bg-\[\#93F514\]\/10.blur-\[90px\] {
            opacity: 0 !important;
        }

        /* Carousel concentric circle borders → subtle */
        html.light-mode .border-\[\#93F514\]\/15 {
            border-color: rgba(45, 107, 0, 0.08) !important;
        }

        html.light-mode .border-\[\#93F514\]\/10 {
            border-color: rgba(45, 107, 0, 0.05) !important;
        }

        html.light-mode .border-\[\#93F514\]\/5 {
            border-color: rgba(45, 107, 0, 0.03) !important;
        }

        /* Carousel "Kunjungi Website" button → kontras di bg putih */
        html.light-mode .bg-white\/5 {
            background-color: rgba(45, 107, 0, 0.06) !important;
        }

        html.light-mode .bg-white\/5:hover,
        html.light-mode .hover\:bg-white\/10:hover {
            background-color: rgba(45, 107, 0, 0.12) !important;
        }

        /* Carousel photo frame borders → softer */
        html.light-mode .border-white\/20 {
            border-color: rgba(0, 0, 0, 0.08) !important;
        }

        html.light-mode .border-white\/25 {
            border-color: rgba(0, 0, 0, 0.10) !important;
        }

        /* Carousel photo bg container */
        html.light-mode .bg-black\/40 {
            background-color: rgba(0, 0, 0, 0.05) !important;
        }

        /* Floating glass badge in carousel & hero collage */
        html.light-mode .bg-\[\#061806\]\/85,
        html.light-mode .bg-\[\#061806\]\/90,
        html.light-mode .about-hero-badge {
            background-color: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.25) !important;
            box-shadow: 0 8px 24px -2px rgba(0, 0, 0, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04) !important;
            backdrop-filter: none !important;
        }

        html.light-mode .bg-\[\#061806\]\/85 .text-\[\#93F514\],
        html.light-mode .bg-\[\#061806\]\/90 .text-\[\#93F514\],
        html.light-mode .about-hero-badge .about-hero-badge-title,
        html.light-mode .about-hero-badge .text-\[\#93F514\] {
            color: #2D6B00 !important;
            font-weight: 800 !important;
        }

        html.light-mode .bg-\[\#061806\]\/85 .text-gray-300,
        html.light-mode .bg-\[\#061806\]\/90 .text-gray-300,
        html.light-mode .about-hero-badge .about-hero-badge-sub,
        html.light-mode .about-hero-badge .text-gray-300 {
            color: #4B5563 !important;
            font-weight: 600 !important;
        }

        html.light-mode .bg-\[\#061806\]\/85 .bg-\[\#93F514\] {
            background-color: #2D6B00 !important;
        }

        /* Carousel nav buttons */
        html.light-mode .bg-\[\#061506\].hover\:bg-\[\#93F514\] {
            background-color: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.30) !important;
        }

        html.light-mode .bg-\[\#061506\].hover\:bg-\[\#93F514\]:hover {
            background-color: #93F514 !important;
        }

        /* Progress bar bg */
        html.light-mode .bg-white\/10 {
            background-color: rgba(45, 107, 0, 0.10) !important;
        }

        html.light-mode .hover\:bg-white\/20:hover {
            background-color: rgba(45, 107, 0, 0.18) !important;
        }

        /* =====================================================
           ALUR PENDAFTARAN & SELEKSI
        ===================================================== */

        /* Step tab switcher container */
        html.light-mode .bg-\[\#061206\] {
            background-color: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06) !important;
        }

        /* Inactive step tab text */
        html.light-mode .bg-\[\#061206\] button {
            color: #4B5563 !important;
            background: transparent !important;
        }

        html.light-mode .bg-\[\#061206\] button:hover {
            color: #111827 !important;
            background-color: rgba(0, 0, 0, 0.04) !important;
        }

        /* Active step tab button (e.g. 1. Registrasi Akun) → Solid Neon #93F514 + Text Hitam Pekat */
        html.light-mode .bg-\[\#061206\] button.font-extrabold,
        html.light-mode .bg-\[\#061206\] button[class*="from-\[\#93F514\]"] {
            background: #93F514 !important;
            background-image: none !important;
            color: #000000 !important;
            font-weight: 800 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12) !important;
        }

        /* Alur content card → putih + border hijau */
        html.light-mode .bg-gradient-to-b.from-\[\#071a07\].via-\[\#051105\].to-\[\#040804\] {
            background: linear-gradient(to bottom, #FFFFFF, #FBFFFE, #F7FAF4) !important;
            border-color: rgba(45, 107, 0, 0.30) !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06), 0 1px 4px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .from-\[\#071a07\] {
            --tw-gradient-from: #FFFFFF var(--tw-gradient-from-position, ) !important;
        }

        html.light-mode .via-\[\#051105\] {
            --tw-gradient-via: #FBFFFE var(--tw-gradient-via-position, ) !important;
        }

        /* Alur glow blob (top right) → hide */
        html.light-mode .bg-\[\#93F514\]\/15.blur-3xl {
            opacity: 0 !important;
        }

        /* =====================================================
           PAGE HEADER BANNER (Jelajahi Lowongan Pekerjaan)
        ===================================================== */
        html.light-mode .bg-gradient-to-r.from-\[\#051c05\].via-\[\#072907\].to-\[\#031103\] {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06), 0 1px 4px rgba(0, 0, 0, 0.04) !important;
        }

        /* Hide glow in Jelajahi Lowongan header */
        html.light-mode .bg-gradient-to-r.from-\[\#051c05\].via-\[\#072907\].to-\[\#031103\] .blur-3xl {
            opacity: 0 !important;
        }

        /* Heading text "Jelajahi" in light mode */
        html.light-mode .bg-gradient-to-r.from-\[\#051c05\].via-\[\#072907\].to-\[\#031103\] h1.text-\[\#EEEEEE\] {
            color: #111827 !important;
        }

        /* Span "Lowongan Pekerjaan" in light mode -> deep emerald green #2D6B00 */
        html.light-mode .bg-gradient-to-r.from-\[\#051c05\].via-\[\#072907\].to-\[\#031103\] h1 span {
            background: none !important;
            background-image: none !important;
            -webkit-background-clip: unset !important;
            background-clip: unset !important;
            -webkit-text-fill-color: #2D6B00 !important;
            color: #2D6B00 !important;
            font-weight: 800 !important;
        }

        /* Description subtitle text */
        html.light-mode .bg-gradient-to-r.from-\[\#051c05\].via-\[\#072907\].to-\[\#031103\] p.text-gray-300 {
            color: #4B5563 !important;
        }

        /* =====================================================
           KATEGORI DEPARTEMEN CARDS
        ===================================================== */

        /* Department card → putih + border + shadow */
        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#040804\].border-\[\#93F514\]\/30 {
            background: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.22) !important;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#040804\].border-\[\#93F514\]\/30:hover {
            border-color: rgba(45, 107, 0, 0.5) !important;
            box-shadow: 0 8px 28px rgba(45, 107, 0, 0.12), 0 2px 6px rgba(0, 0, 0, 0.06) !important;
        }

        /* Department card icon container */
        html.light-mode .bg-\[\#93F514\]\/15.border-\[\#93F514\]\/40 {
            background-color: rgba(45, 107, 0, 0.08) !important;
            border-color: rgba(45, 107, 0, 0.25) !important;
        }

        /* =====================================================
           CTA BANNER ("Siap Memulai Karir Baru Bersama Kami?")
           Warna Putih Bersih (#FFFFFF)
        ===================================================== */
        html.light-mode .bg-gradient-to-r.from-\[\#041a04\].via-\[\#062906\].to-\[\#031203\] {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06), 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }

        /* CTA banner heading & text */
        html.light-mode .bg-gradient-to-r.from-\[\#041a04\] h2.text-\[\#EEEEEE\],
        html.light-mode .bg-gradient-to-r.from-\[\#041a04\] .text-\[\#EEEEEE\] {
            color: #111827 !important;
        }

        html.light-mode .bg-gradient-to-r.from-\[\#041a04\] p.text-gray-300,
        html.light-mode .bg-gradient-to-r.from-\[\#041a04\] .text-gray-300 {
            color: #4B5563 !important;
        }

        /* CTA "Daftar Akun Sekarang" button: normal hitam pekat + teks putih, hover warna neon #93F514 + teks hitam */
        html.light-mode .bg-gradient-to-r.from-\[\#041a04\] a.bg-gradient-to-r.from-\[\#93F514\] {
            background: #061806 !important;
            background-image: none !important;
            color: #FFFFFF !important;
            border: 1px solid rgba(147, 245, 20, 0.50) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.20) !important;
        }

        html.light-mode .bg-gradient-to-r.from-\[\#041a04\] a.bg-gradient-to-r.from-\[\#93F514\]:hover {
            background: #93F514 !important;
            border-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 6px 22px rgba(147, 245, 20, 0.60) !important;
        }

        /* Alur Pendaftaran "Daftar Akun Sekarang" button */
        html.light-mode #alur-pendaftaran a[href*="register"] {
            background: #061806 !important;
            background-image: none !important;
            color: #FFFFFF !important;
            border: 1px solid rgba(147, 245, 20, 0.50) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.20) !important;
        }

        html.light-mode #alur-pendaftaran a[href*="register"]:hover {
            background: #93F514 !important;
            border-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 6px 22px rgba(147, 245, 20, 0.60) !important;
        }

        /* CTA "Masuk Akun" button: Putih + Border Halus */
        html.light-mode .bg-gradient-to-r.from-\[\#041a04\] a.bg-black\/50 {
            background-color: #FFFFFF !important;
            color: #111827 !important;
            border-color: rgba(147, 245, 20, 0.40) !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .bg-gradient-to-r.from-\[\#041a04\] a.bg-black\/50:hover {
            background-color: #E8F6DC !important;
            border-color: #93F514 !important;
        }

        /* =====================================================
           JOB CARDS (GRID & LIST VIEW) - LIGHT MODE (PUTIH BERSIH)
        ===================================================== */
        html.light-mode .job-card-item,
        html.light-mode .bg-gradient-to-r.from-\[\#061506\].via-\[\#051205\].to-\[\#030803\],
        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030803\] {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: rgba(45, 107, 0, 0.18) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }

        html.light-mode .job-card-item:hover,
        html.light-mode .bg-gradient-to-r.from-\[\#061506\].via-\[\#051205\].to-\[\#030803\]:hover,
        html.light-mode .bg-gradient-to-b.from-\[\#061506\].to-\[\#030803\]:hover {
            border-color: rgba(45, 107, 0, 0.45) !important;
            box-shadow: 0 12px 30px -4px rgba(45, 107, 0, 0.12), 0 4px 10px -2px rgba(0, 0, 0, 0.04) !important;
        }

        /* Card Text & Headings in Light Mode */
        html.light-mode .job-card-item h3,
        html.light-mode .job-card-item h3.text-\[\#EEEEEE\] {
            color: #111827 !important;
        }

        html.light-mode .job-card-item h3:hover,
        html.light-mode .job-card-item a:hover h3 {
            color: #2D6B00 !important;
        }

        html.light-mode .job-card-item h4.text-gray-400,
        html.light-mode .job-card-item .text-gray-400 {
            color: #6B7280 !important;
        }

        html.light-mode .job-card-item p.text-gray-400 {
            color: #4B5563 !important;
        }

        html.light-mode .job-card-item .text-gray-300 {
            color: #374151 !important;
        }

        html.light-mode .job-card-item strong.text-\[\#EEEEEE\] {
            color: #111827 !important;
        }

        html.light-mode .job-card-item span.text-\[\#93F514\] {
            color: #2D6B00 !important;
            filter: none !important;
        }

        html.light-mode .job-card-item svg.text-\[\#93F514\] {
            color: #3D7A00 !important;
        }

        /* Card Logo Container in Light Mode */
        html.light-mode .job-card-item .bg-\[\#051205\] {
            background-color: #F3F9EE !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .job-card-item .bg-\[\#93F514\]\/15 {
            background-color: #E8F7D6 !important;
            border-color: rgba(45, 107, 0, 0.30) !important;
            color: #2D6B00 !important;
        }

        /* Card Badges in Light Mode */
        html.light-mode .job-card-item span.bg-\[\#93F514\]\/15 {
            background-color: #EAF8DA !important;
            color: #2D6B00 !important;
            border-color: rgba(45, 107, 0, 0.25) !important;
        }

        /* Card Inner Dividers in Light Mode */
        html.light-mode .job-card-item .border-\[\#93F514\]\/15 {
            border-color: rgba(0, 0, 0, 0.08) !important;
        }

        /* Card Action Button in Light Mode */
        html.light-mode .job-card-item a.bg-\[\#93F514\] {
            background-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 3px 12px rgba(147, 245, 20, 0.35) !important;
        }

        html.light-mode .job-card-item a.bg-\[\#93F514\]:hover {
            background-color: #82e408 !important;
            box-shadow: 0 4px 16px rgba(147, 245, 20, 0.50) !important;
        }

        /* View Mode Switcher Pill in Light Mode */
        html.light-mode .view-mode-toggle-pill {
            background-color: #FFFFFF !important;
            border-color: rgba(0, 0, 0, 0.12) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .view-mode-toggle-pill button.text-gray-400 {
            color: #9CA3AF !important;
        }

        html.light-mode .view-mode-toggle-pill button.text-gray-400:hover {
            color: #2D6B00 !important;
        }

        html.light-mode .view-mode-toggle-pill button.bg-\[\#93F514\] {
            background-color: #93F514 !important;
            color: #000000 !important;
        }

        /* Section Heading in Light Mode */
        html.light-mode h2.text-\[\#EEEEEE\],
        html.light-mode h2 .text-\[\#EEEEEE\] {
            color: #111827 !important;
        }

        html.light-mode h2 span.text-\[\#93F514\] {
            color: #2D6B00 !important;
        }

        /* Headings Gradient Span in Light Mode (Visi Misi, MIKA, Grup Perusahaan, dll) */
        html.light-mode h1 span.bg-gradient-to-r.from-\[\#93F514\],
        html.light-mode h2 span.bg-gradient-to-r.from-\[\#93F514\] {
            background: linear-gradient(135deg, #1E5200 0%, #2D6B00 50%, #3D7A00 100%) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            filter: none !important;
            text-shadow: none !important;
        }

        /* Ambient Glow & Section Borders in Light Mode */
        html.light-mode .blur-\[140px\] {
            opacity: 0.15 !important;
        }

        html.light-mode section.border-t.border-\[\#93F514\]\/20 {
            border-color: rgba(45, 107, 0, 0.15) !important;
        }

        /* =====================================================
           TENTANG KAMI: HERO SECTION (LIGHT MODE)
        ===================================================== */
        html.light-mode .about-hero-badge {
            background-color: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.25) !important;
            box-shadow: 0 8px 24px -2px rgba(0, 0, 0, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04) !important;
            backdrop-filter: none !important;
        }

        html.light-mode .about-hero-badge .about-hero-badge-title {
            color: #2D6B00 !important;
            font-weight: 800 !important;
        }

        html.light-mode .about-hero-badge .about-hero-badge-sub {
            color: #4B5563 !important;
            font-weight: 600 !important;
        }

        html.light-mode a[href="#meet-the-group"] {
            background-color: #FFFFFF !important;
            color: #111827 !important;
            border-color: rgba(45, 107, 0, 0.35) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode a[href="#meet-the-group"]:hover {
            background-color: #EAF8DA !important;
            color: #2D6B00 !important;
            border-color: #2D6B00 !important;
        }

        /* Slide Navigation Dots (Controls) in Light Mode */
        html.light-mode .about-doc-label {
            color: #4B5563 !important;
        }

        html.light-mode .about-dot-inactive {
            background-color: rgba(45, 107, 0, 0.20) !important;
        }

        html.light-mode .about-dot-inactive:hover {
            background-color: rgba(45, 107, 0, 0.40) !important;
        }

        html.light-mode .about-dot-active {
            background-color: #2D6B00 !important;
        }

        /* =====================================================
           TENTANG KAMI: VISI & MISI KAMI (LIGHT MODE)
        ===================================================== */
        /* Main Executive Visi Card */
        html.light-mode .visi-card {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 10px 35px -5px rgba(0, 0, 0, 0.06), 0 2px 6px rgba(0, 0, 0, 0.03) !important;
        }

        html.light-mode .visi-card:hover {
            border-color: rgba(45, 107, 0, 0.45) !important;
            box-shadow: 0 16px 40px -6px rgba(45, 107, 0, 0.12), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
        }

        /* Hide dark mode glow blob inside Visi Card */
        html.light-mode .visi-card .blur-3xl {
            opacity: 0 !important;
        }

        /* Watermark quote in Visi Card */
        html.light-mode .visi-card span.font-serif {
            color: rgba(45, 107, 0, 0.05) !important;
        }

        /* Visi Perusahaan Badge */
        html.light-mode .visi-card .visi-badge,
        html.light-mode .visi-card .rounded-full.bg-\[\#93F514\]\/15 {
            background-color: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.30) !important;
            color: #2D6B00 !important;
            box-shadow: 0 1px 3px rgba(45, 107, 0, 0.08) !important;
        }

        /* Visi Quote Statement & Left Accent Border */
        html.light-mode .visi-card blockquote {
            border-left-color: #2D6B00 !important;
        }

        html.light-mode .visi-card blockquote p {
            color: #111827 !important;
        }

        /* 3 Strategic Focus Pillars inside Visi Card */
        html.light-mode .visi-pillar-card {
            background-color: #F8FAF7 !important;
            border-color: rgba(45, 107, 0, 0.16) !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }

        html.light-mode .visi-pillar-card:hover {
            background-color: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.45) !important;
            box-shadow: 0 4px 16px rgba(45, 107, 0, 0.08) !important;
        }

        html.light-mode .visi-pillar-card .w-10.h-10 {
            background-color: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.30) !important;
            color: #2D6B00 !important;
        }

        html.light-mode .visi-pillar-card h5 {
            color: #111827 !important;
        }

        html.light-mode .visi-pillar-card p {
            color: #4B5563 !important;
        }

        /* Misi Cards (3 Items Grid) */
        html.light-mode .misi-card {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }

        html.light-mode .misi-card:hover {
            border-color: rgba(45, 107, 0, 0.50) !important;
            box-shadow: 0 12px 30px -4px rgba(45, 107, 0, 0.12), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
        }

        /* Number 01, 02, 03 - Deep Contrast Emerald */
        html.light-mode .misi-card span.text-3xl {
            color: #2D6B00 !important;
        }

        /* Misi Icon Box */
        html.light-mode .misi-card .w-10.h-10 {
            background-color: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.28) !important;
            color: #2D6B00 !important;
        }

        /* Misi Description Text */
        html.light-mode .misi-card p {
            color: #374151 !important;
        }

        /* Misi Card Footer */
        html.light-mode .misi-card .border-t {
            border-color: rgba(45, 107, 0, 0.15) !important;
        }

        html.light-mode .misi-card .border-t span {
            color: #2D6B00 !important;
            font-weight: 700 !important;
        }

        /* =====================================================
           TENTANG KAMI: NILAI PERUSAHAAN (MIKA) LIGHT MODE
        ===================================================== */
        html.light-mode .mika-card {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }

        html.light-mode .mika-card:hover {
            border-color: #2D6B00 !important;
            box-shadow: 0 14px 35px -4px rgba(45, 107, 0, 0.14), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
        }

        /* Giant background watermark letter */
        html.light-mode .mika-card .mika-watermark {
            color: rgba(45, 107, 0, 0.06) !important;
        }

        html.light-mode .mika-card:hover .mika-watermark {
            color: rgba(45, 107, 0, 0.14) !important;
        }

        /* Monogram Letter Box (M, I, K, A) */
        html.light-mode .mika-card .mika-letter-box {
            background-color: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.35) !important;
            color: #2D6B00 !important;
            box-shadow: 0 2px 8px rgba(45, 107, 0, 0.10) !important;
        }

        html.light-mode .mika-card:hover .mika-letter-box {
            background-color: #93F514 !important;
            border-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 4px 16px rgba(147, 245, 20, 0.40) !important;
        }

        /* "Pilar 1", "Pilar 2", etc. */
        html.light-mode .mika-card span.tracking-widest {
            color: #6B7280 !important;
        }

        html.light-mode .mika-card:hover span.tracking-widest {
            color: #2D6B00 !important;
        }

        /* Title e.g. Menghargai, Integritas */
        html.light-mode .mika-card h3 {
            color: #111827 !important;
        }

        html.light-mode .mika-card:hover h3 {
            color: #2D6B00 !important;
        }

        /* English Subtitle e.g. Respect, Integrity */
        html.light-mode .mika-card .mika-subtitle {
            color: #2D6B00 !important;
            font-weight: 700 !important;
        }

        /* Description text */
        html.light-mode .mika-card p {
            color: #4B5563 !important;
        }

        /* Card footer */
        html.light-mode .mika-card .border-t {
            border-color: rgba(45, 107, 0, 0.15) !important;
        }

        html.light-mode .mika-card .border-t span {
            color: #6B7280 !important;
        }

        /* =====================================================
           TENTANG KAMI / COMPONENT: GRUP PERUSAHAAN (LIGHT MODE)
        ===================================================== */
        html.light-mode .group-company-card {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        }

        html.light-mode .group-company-card:hover {
            border-color: rgba(45, 107, 0, 0.50) !important;
            box-shadow: 0 14px 35px -4px rgba(45, 107, 0, 0.14), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
        }

        /* Corner ambient light blob - hide in light mode */
        html.light-mode .group-company-card .blur-2xl {
            opacity: 0 !important;
        }

        /* Initial Avatar Fallback */
        html.light-mode .group-company-card .group-initial-avatar {
            background: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.35) !important;
            color: #2D6B00 !important;
        }

        html.light-mode .group-company-card:hover .group-initial-avatar {
            background: #93F514 !important;
            border-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 4px 16px rgba(147, 245, 20, 0.35) !important;
        }

        /* Tagline pill badge */
        html.light-mode .group-company-card .group-tagline-badge {
            background-color: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.25) !important;
            color: #2D6B00 !important;
        }

        /* Company Name */
        html.light-mode .group-company-card h3 {
            color: #111827 !important;
        }

        html.light-mode .group-company-card:hover h3 {
            color: #2D6B00 !important;
        }

        /* Company Description */
        html.light-mode .group-company-card p {
            color: #4B5563 !important;
        }

        /* Footer border */
        html.light-mode .group-company-card .border-t {
            border-color: rgba(45, 107, 0, 0.15) !important;
        }

        /* City location text & icon */
        html.light-mode .group-company-card .border-t .text-gray-400 {
            color: #6B7280 !important;
        }

        html.light-mode .group-company-card .border-t svg.text-\[\#93F514\] {
            color: #2D6B00 !important;
        }

        /* Kunjungi Website Button */
        html.light-mode .group-company-card .group-website-btn {
            background-color: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.35) !important;
            color: #2D6B00 !important;
        }

        html.light-mode .group-company-card .group-website-btn:hover {
            background-color: #93F514 !important;
            border-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 4px 14px rgba(147, 245, 20, 0.40) !important;
        }

        /* Sinergi Grup badge fallback */
        html.light-mode .group-company-card .group-sinergi-badge {
            background-color: #F3F4F6 !important;
            border-color: #E5E7EB !important;
            color: #6B7280 !important;
        }

        /* Group Detail button in light mode */
        html.light-mode .group-company-card .group-detail-btn {
            background-color: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.35) !important;
            color: #2D6B00 !important;
        }

        html.light-mode .group-company-card .group-detail-btn:hover {
            background-color: #93F514 !important;
            border-color: #93F514 !important;
            color: #000000 !important;
            box-shadow: 0 4px 14px rgba(147, 245, 20, 0.40) !important;
        }

        /* Group Website icon button in light mode */
        html.light-mode .group-company-card .group-website-icon-btn {
            background-color: #F3F4F6 !important;
            border-color: #E5E7EB !important;
            color: #374151 !important;
        }

        html.light-mode .group-company-card .group-website-icon-btn:hover {
            background-color: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.40) !important;
            color: #2D6B00 !important;
        }

        /* =====================================================
           HALAMAN DETAIL PROFIL PERUSAHAAN (LIGHT MODE)
        ===================================================== */
        html.light-mode .company-hero-card {
            background: #FFFFFF !important;
            background-image: none !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 4px 12px rgba(45, 107, 0, 0.04) !important;
        }

        html.light-mode .company-hero-card .company-name-heading {
            color: #111827 !important;
        }

        html.light-mode .company-hero-card svg.text-\[\#93F514\] {
            color: #2D6B00 !important;
        }

        html.light-mode .company-hero-card .text-gray-400 {
            color: #4B5563 !important;
        }

        html.light-mode .company-hero-card .company-initial-avatar {
            background: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.35) !important;
            color: #2D6B00 !important;
        }

        html.light-mode .company-ecosystem-badge {
            background-color: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.35) !important;
            color: #2D6B00 !important;
        }

        html.light-mode .company-tagline-chip {
            background-color: #F3F4F6 !important;
            border-color: #E5E7EB !important;
            color: #4B5563 !important;
        }

        html.light-mode .company-detail-card {
            background: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.18) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .company-detail-card h2,
        html.light-mode .company-detail-card h3 {
            color: #111827 !important;
        }

        html.light-mode .company-detail-card .company-about-text {
            color: #374151 !important;
            text-align: justify !important;
        }

        html.light-mode .company-vision-box {
            background: #F6FCF0 !important;
            border-color: rgba(45, 107, 0, 0.30) !important;
        }

        html.light-mode .company-vision-box p {
            color: #1F2937 !important;
        }

        html.light-mode .company-mission-item,
        html.light-mode .company-value-box,
        html.light-mode .company-other-item {
            background: #F9FAFB !important;
            border-color: #E5E7EB !important;
        }

        html.light-mode .company-mission-item:hover,
        html.light-mode .company-value-box:hover,
        html.light-mode .company-other-item:hover {
            border-color: rgba(45, 107, 0, 0.40) !important;
            background: #F3F9EE !important;
        }

        html.light-mode .company-mission-item p,
        html.light-mode .company-value-box p {
            color: #4B5563 !important;
        }

        html.light-mode .company-job-item {
            background: #FFFFFF !important;
            border-color: rgba(45, 107, 0, 0.20) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        }

        html.light-mode .company-job-item:hover {
            border-color: #2D6B00 !important;
            box-shadow: 0 8px 24px rgba(45, 107, 0, 0.12) !important;
        }

        html.light-mode .company-job-item h3 a {
            color: #111827 !important;
        }

        html.light-mode .company-job-item h3 a:hover {
            color: #2D6B00 !important;
        }

        html.light-mode .company-empty-jobs {
            background: #F9FAFB !important;
            border-color: #E5E7EB !important;
        }

        html.light-mode .company-empty-jobs h4 {
            color: #111827 !important;
        }

        /* =====================================================
           FOOTER: OFFICIAL WEBSITE BUTTON (LIGHT MODE)
        ===================================================== */
        html.light-mode footer .footer-official-btn,
        html.light-mode footer a[title="Kunjungi Website Perusahaan"] {
            background-color: #EAF8DA !important;
            border-color: rgba(45, 107, 0, 0.45) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
        }

        html.light-mode footer .footer-official-btn span,
        html.light-mode footer a[title="Kunjungi Website Perusahaan"] span,
        html.light-mode footer a.footer-official-btn span {
            color: #111827 !important;
            font-weight: 700 !important;
        }

        html.light-mode footer .footer-official-btn svg,
        html.light-mode footer a[title="Kunjungi Website Perusahaan"] svg {
            color: #2D6B00 !important;
        }

        html.light-mode footer .footer-official-btn:hover,
        html.light-mode footer a[title="Kunjungi Website Perusahaan"]:hover {
            background-color: #93F514 !important;
            border-color: #93F514 !important;
            box-shadow: 0 4px 16px rgba(147, 245, 20, 0.45) !important;
        }

        html.light-mode footer .footer-official-btn:hover span,
        html.light-mode footer a[title="Kunjungi Website Perusahaan"]:hover span {
            color: #000000 !important;
        }

        html.light-mode footer .footer-official-btn:hover svg,
        html.light-mode footer a[title="Kunjungi Website Perusahaan"]:hover svg {
            color: #000000 !important;
        }

        /* =====================================================
           JOB DETAIL (SHOW PAGE) - LIGHT MODE
        ===================================================== */
        /* Ikon Perusahaan di header atas lowongan: ubah jadi grey */
        html.light-mode .company-icon,
        html.light-mode .company-badge svg,
        html.light-mode .department-badge svg {
            color: #9CA3AF !important;
        }

        /* Container Logo Perusahaan di header card */
        html.light-mode .company-logo-box {
            background-color: #FFFFFF !important;
            border-color: #E5E7EB !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
        }

        /* Deskripsi Pekerjaan & Kualifikasi: teks bold & heading jelas dan terbaca tajam */
        html.light-mode .job-description-prose,
        html.light-mode .job-description-prose.prose {
            color: #374151 !important;
            --tw-prose-body: #374151 !important;
            --tw-prose-headings: #111827 !important;
            --tw-prose-bold: #111827 !important;
            --tw-prose-bullets: #2D6B00 !important;
        }

        html.light-mode .job-description-prose strong,
        html.light-mode .job-description-prose b,
        html.light-mode .prose strong,
        html.light-mode [class*="[&_strong]:text-[#EEEEEE]"] strong {
            color: #111827 !important;
            font-weight: 700 !important;
        }

        html.light-mode .job-description-prose h1,
        html.light-mode .job-description-prose h2,
        html.light-mode .job-description-prose h3,
        html.light-mode .job-description-prose h4,
        html.light-mode [class*="[&_h2]:text-[#EEEEEE]"] h2,
        html.light-mode [class*="[&_h3]:text-[#EEEEEE]"] h3 {
            color: #111827 !important;
            font-weight: 800 !important;
        }

        html.light-mode .job-description-prose p,
        html.light-mode .job-description-prose li {
            color: #374151 !important;
        }

        html.light-mode .job-description-prose ul > li::marker,
        html.light-mode .job-description-prose ol > li::marker {
            color: #2D6B00 !important;
        }

        /* Lowongan Terkait Lainnya: nama perusahaan ubah warna #2D6B00 */
        html.light-mode .related-company-name {
            color: #2D6B00 !important;
            font-weight: 600 !important;
        }

        /* Admin Mode Banner & Dashboard Button di Light Mode */
        html.light-mode .admin-preview-banner {
            background-color: #FFFBEB !important;
            border-color: rgba(245, 158, 11, 0.40) !important;
            box-shadow: 0 1px 4px rgba(245, 158, 11, 0.08) !important;
        }

        html.light-mode .admin-preview-banner .text-amber-300 {
            color: #92400E !important;
        }

        html.light-mode .admin-preview-banner .text-gray-300 {
            color: #78350F !important;
        }

        html.light-mode .admin-preview-banner .bg-amber-500\/20 {
            background-color: #FEF3C7 !important;
            color: #B45309 !important;
            border-color: rgba(245, 158, 11, 0.35) !important;
        }

        html.light-mode .admin-preview-banner a {
            color: #92400E !important;
        }

        html.light-mode .admin-dashboard-btn {
            background-color: #111827 !important;
            border-color: #374151 !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
        }

        html.light-mode .admin-dashboard-btn:hover {
            background-color: #1F2937 !important;
            border-color: #93F514 !important;
        }

        /* =====================================================
           MISC
        ===================================================== */
        html.light-mode .placeholder-gray-400::placeholder {
            color: #9ca3af !important;
        }

        html.light-mode .hover\:bg-\[\#EEEEEE\]\/5:hover {
            background-color: rgba(0, 0, 0, 0.04) !important;
        }

        /* =====================================================
           THEME TOGGLE BUTTON
        ===================================================== */
        .theme-toggle-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            border: 1.5px solid rgba(147, 245, 20, 0.4);
            background: rgba(147, 245, 20, 0.08);
            color: #93F514;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s, transform 0.2s;
            flex-shrink: 0;
            overflow: hidden;
        }

        .theme-toggle-btn:hover {
            background: rgba(147, 245, 20, 0.18);
            border-color: #93F514;
            transform: scale(1.08);
        }

        html.light-mode .theme-toggle-btn {
            background: rgba(61, 122, 0, 0.10);
            color: #3D7A00;
            border-color: rgba(61, 122, 0, 0.35);
        }

        html.light-mode .theme-toggle-btn:hover {
            background: rgba(61, 122, 0, 0.18);
            border-color: #3D7A00;
        }
    </style>
</head>

<body
    class="antialiased bg-[#040804] text-[#EEEEEE] selection:bg-[#93F514] selection:text-black min-h-screen flex flex-col justify-between">

    <!-- Top Navigation Header -->
    @include('frontend.components.navbar')

    <!-- Main Content with smooth fade entrance -->
    <main class="flex-grow animate-page-fade">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('frontend.components.footer')

    @stack('scripts')

    {{-- Theme toggle logic (Alpine.js store) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                isDark: localStorage.getItem('mika-theme') !== 'light',

                toggle() {
                    this.isDark = !this.isDark;
                    const html = document.getElementById('html-root');
                    if (this.isDark) {
                        html.classList.remove('light-mode');
                        html.classList.add('dark');
                        localStorage.setItem('mika-theme', 'dark');
                    } else {
                        html.classList.remove('dark');
                        html.classList.add('light-mode');
                        localStorage.setItem('mika-theme', 'light');
                    }
                }
            });
        });

        // =========================================================================
        // High-Performance Hardware-Accelerated Scroll Reveal Engine (Zero Dependencies)
        // =========================================================================
        (function() {
            function initScrollReveal() {
                const reveals = document.querySelectorAll('.reveal-on-scroll:not(.is-revealed)');
                if (!reveals.length) return;

                if ('IntersectionObserver' in window) {
                    const observer = new IntersectionObserver((entries, obs) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('is-revealed');
                                obs.unobserve(entry.target); // Stop observing once revealed for 0% memory overhead
                            }
                        });
                    }, {
                        root: null,
                        threshold: 0.05,
                        rootMargin: '0px 0px -25px 0px' // Triggers slightly before element enters view for butter smoothness
                    });

                    reveals.forEach(el => observer.observe(el));
                } else {
                    reveals.forEach(el => el.classList.add('is-revealed'));
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initScrollReveal);
            } else {
                initScrollReveal();
            }

            // Re-bind on Livewire navigations or dynamic DOM changes if present
            if (window.Livewire) {
                document.addEventListener('livewire:navigated', initScrollReveal);
            }
        })();
    </script>

    <!-- Session Heartbeat (Sliding Refresh saat aktif bekerja) -->
    <x-session-heartbeat />
</body>

</html>
