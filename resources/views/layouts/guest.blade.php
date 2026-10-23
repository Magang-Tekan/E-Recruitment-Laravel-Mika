<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="guest-html-root">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MIKA CAREER') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/mika-favicon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Tailwind CSS CDN Fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>

    <!-- Sync tema dengan frontend & app (mika-theme): apply sebelum render -->
    <script>
        (function () {
            var root = document.getElementById('guest-html-root');
            var mikaTheme = localStorage.getItem('mika-theme');
            if (mikaTheme === 'light') {
                root.classList.add('light-mode');
                root.classList.remove('dark');
            } else {
                // Default = dark (sama dengan frontend)
                root.classList.remove('light-mode');
                root.classList.add('dark');
            }
        })();
    </script>

    <style>
        :root {
            --primary-green: #93F514;
            --primary-green-glow: rgba(147, 245, 20, 0.35);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #040904;
        }

        .heading-font {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Ambient Glow Animations */
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.35; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.08); }
        }
        .animate-pulse-glow {
            animation: pulseGlow 8s ease-in-out infinite;
        }
        .animate-pulse-glow-delayed {
            animation: pulseGlow 10s ease-in-out infinite 3s;
        }
        .animate-pulse-glow-delayed {
            animation: pulseGlow 10s ease-in-out infinite 3s;
        }

        /* Glassmorphism Styles */
        .glass-card-main {
            background: rgba(10, 24, 10, 0.75);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(147, 245, 20, 0.25);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75), 0 0 40px rgba(147, 245, 20, 0.08);
        }

        /* Subtle grid background */
        .bg-grid-pattern {
            background-size: 36px 36px;
            background-image: 
                linear-gradient(to right, rgba(147, 245, 20, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(147, 245, 20, 0.03) 1px, transparent 1px);
        }

        /* Left Banner gradient */
        .banner-gradient {
            background: linear-gradient(145deg, #071f08 0%, #0d3810 50%, #155e1b 100%);
            border: 1px solid rgba(147, 245, 20, 0.3);
        }

        /* 4-Dot Jumping Wave Animation (Clean flat, no shadow/glow) */
        @keyframes mikaDotBounce {
            0%, 80%, 100% {
                transform: translateY(0);
                opacity: 0.45;
            }
            40% {
                transform: translateY(-16px);
                opacity: 1;
            }
        }
        .animate-dot-1 {
            animation: mikaDotBounce 1.3s infinite ease-in-out;
            animation-delay: 0s;
        }
        .animate-dot-2 {
            animation: mikaDotBounce 1.3s infinite ease-in-out;
            animation-delay: 0.16s;
        }
        .animate-dot-3 {
            animation: mikaDotBounce 1.3s infinite ease-in-out;
            animation-delay: 0.32s;
        }
        .animate-dot-4 {
            animation: mikaDotBounce 1.3s infinite ease-in-out;
            animation-delay: 0.48s;
        }
        html.light-mode .nav-lang-pill {
            background-color: rgba(243, 244, 246, 0.95) !important;
            border-color: rgba(0, 0, 0, 0.12) !important;
            color: #4b5563 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }
        html.light-mode .nav-lang-pill:hover {
            border-color: rgba(61, 122, 0, 0.35) !important;
        }
        html.light-mode .nav-lang-btn {
            color: #64748b !important;
        }
        html.light-mode .nav-lang-btn:hover:not(.nav-lang-btn-active) {
            color: #3C7800 !important;
            background-color: rgba(60, 120, 0, 0.08) !important;
        }
        html.light-mode .nav-lang-btn-active {
            background-color: #3C7800 !important;
            color: #ffffff !important;
            box-shadow: 0 1px 3px rgba(60, 120, 0, 0.35) !important;
            font-weight: 800 !important;
        }
    </style>
</head>

<body class="font-sans antialiased text-[#EEEEEE] bg-[#040904] min-h-screen relative overflow-x-hidden selection:bg-[#93F514] selection:text-black flex items-center justify-center p-3 sm:p-6 lg:p-10">
    
    <!-- Language Switcher Dropdown (Top Right) -->
    <div class="fixed top-3 right-3 sm:top-5 sm:right-6 z-40 flex items-center gap-2">
        @php
            $guestCurrentLocale = app()->getLocale();
            $guestCurrentUrl = request()->fullUrl();
        @endphp
        <div class="relative" x-data="{ guestLangOpen: false }" @click.outside="guestLangOpen = false" @keydown.escape.window="guestLangOpen = false">
            <button type="button" 
               @click="guestLangOpen = !guestLangOpen"
               aria-haspopup="true"
               :aria-expanded="guestLangOpen"
               title="{{ $guestCurrentLocale === 'en' ? 'Switch Language (Current: English)' : 'Ganti Bahasa (Saat ini: Indonesia)' }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 h-9 rounded-full border border-slate-200/90 dark:border-[#93F514]/30 bg-slate-100/90 dark:bg-[#061806]/85 backdrop-blur-md hover:bg-slate-200/80 dark:hover:bg-[#0c2e0c] text-xs font-semibold text-slate-700 dark:text-gray-200 shadow-md transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-[#3C7800]/20 dark:focus:ring-[#93F514]/30 group cursor-pointer">
                @if($guestCurrentLocale === 'en')
                    <svg class="w-4 h-4 rounded-full shrink-0 shadow-2xs border border-slate-200 dark:border-slate-700" viewBox="80 0 480 480" aria-hidden="true">
                        <path fill="#012169" d="M0 0h640v480H0z"/>
                        <path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z"/>
                        <path fill="#C8102E" d="m424 281 216 159v40L369 281h55zm-104-41 240 180H480L320 300v-60zM80 0l240 180h-80L0 45V0h80zM0 420l240-180h55L79 420H0z"/>
                        <path fill="#FFF" d="M241 0v480h160V0H241zM0 160v160h640V160H0z"/>
                        <path fill="#C8102E" d="M267 0v480h106V0H267zM0 187v106h640V187H0z"/>
                    </svg>
                    <span class="font-bold tracking-wide">EN</span>
                @else
                    <svg class="w-4 h-4 rounded-full shrink-0 shadow-2xs border border-slate-200 dark:border-slate-700" viewBox="0 0 512 512" aria-hidden="true">
                        <path fill="#e12237" d="M0 0h512v256H0z"/>
                        <path fill="#ffffff" d="M0 256h512v256H0z"/>
                    </svg>
                    <span class="font-bold tracking-wide">ID</span>
                @endif
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 dark:group-hover:text-gray-200 transition-transform duration-200"
                     :class="{ 'rotate-180': guestLangOpen }"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Language Dropdown Menu -->
            <div x-show="guestLangOpen" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                 class="absolute right-0 mt-2 w-48 rounded-2xl bg-white dark:bg-[#071307] border border-slate-200/90 dark:border-[#93F514]/30 shadow-2xl p-1.5 z-50 focus:outline-none">
                
                <div class="px-2.5 py-1 text-[10px] font-bold text-slate-400 dark:text-[#93F514]/60 uppercase tracking-wider">
                    {{ __('Pilih Bahasa') }}
                </div>

                <!-- Bahasa Indonesia -->
                <button type="button"
                        data-lang-btn="id"
                        onclick="window.mikaSwitchLocale('id', '{{ $guestCurrentUrl }}')"
                        class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-colors duration-150 cursor-pointer {{ $guestCurrentLocale === 'id' ? 'bg-[#3C7800]/10 dark:bg-[#93F514]/15 text-[#3C7800] dark:text-[#93F514]' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-white/5' }}">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 rounded-full shrink-0 shadow-2xs border border-slate-200 dark:border-slate-700" viewBox="0 0 512 512" aria-hidden="true">
                            <path fill="#e12237" d="M0 0h512v256H0z"/>
                            <path fill="#ffffff" d="M0 256h512v256H0z"/>
                        </svg>
                        <span class="truncate">{{ __('Bahasa Indonesia') }}</span>
                    </div>
                    @if($guestCurrentLocale === 'id')
                        <svg class="w-4 h-4 text-[#3C7800] dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </button>

                <!-- English -->
                <button type="button"
                        data-lang-btn="en"
                        onclick="window.mikaSwitchLocale('en', '{{ $guestCurrentUrl }}')"
                        class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-colors duration-150 cursor-pointer {{ $guestCurrentLocale === 'en' ? 'bg-[#3C7800]/10 dark:bg-[#93F514]/15 text-[#3C7800] dark:text-[#93F514]' : 'text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-white/5' }}">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 rounded-full shrink-0 shadow-2xs border border-slate-200 dark:border-slate-700" viewBox="80 0 480 480" aria-hidden="true">
                            <path fill="#012169" d="M0 0h640v480H0z"/>
                            <path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0h75z"/>
                            <path fill="#C8102E" d="m424 281 216 159v40L369 281h55zm-104-41 240 180H480L320 300v-60zM80 0l240 180h-80L0 45V0h80zM0 420l240-180h55L79 420H0z"/>
                            <path fill="#FFF" d="M241 0v480h160V0H241zM0 160v160h640V160H0z"/>
                            <path fill="#C8102E" d="M267 0v480h106V0H267zM0 187v106h640V187H0z"/>
                        </svg>
                        <span class="truncate">{{ __('English') }}</span>
                    </div>
                    @if($guestCurrentLocale === 'en')
                        <svg class="w-4 h-4 text-[#3C7800] dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </button>
            </div>
        </div>
    </div>

    @if(request()->routeIs('login') || request()->routeIs('register'))
        <!-- 4-Dot Loading Overlay saat memuat Halaman Masuk & Daftar -->
        <div id="auth-page-loader"
            class="fixed inset-0 z-[100] bg-[#040904]/85 backdrop-blur-[2px] flex flex-col items-center justify-center select-none transition-opacity duration-300">
            <!-- 4-Dot Jumping Wave Loader (Sesuai Referensi, Tanpa Shadow) -->
            <div class="flex items-center gap-3 h-10 px-2">
                <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-1 inline-block"></span>
                <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-2 inline-block"></span>
                <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-3 inline-block"></span>
                <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-4 inline-block"></span>
            </div>
            <p class="mt-2.5 text-xs font-semibold text-white tracking-wide">
                {{ request()->routeIs('login') ? __('Memuat Halaman Masuk...') : __('Memuat Halaman Pendaftaran...') }}
            </p>
        </div>

        <script>
            (function() {
                const hideAuthLoader = function() {
                    const loader = document.getElementById('auth-page-loader');
                    if (loader) {
                        loader.style.opacity = '0';
                        loader.style.pointerEvents = 'none';
                        setTimeout(function() {
                            if (loader && loader.parentNode) {
                                loader.parentNode.removeChild(loader);
                            }
                        }, 300);
                    }
                };

                if (document.readyState === 'complete') {
                    setTimeout(hideAuthLoader, 120);
                } else {
                    window.addEventListener('load', function() {
                        setTimeout(hideAuthLoader, 160);
                    });
                    setTimeout(hideAuthLoader, 1500);
                }
            })();
        </script>
    @endif

    <!-- Background Ambient Glows & Grid Pattern -->
    <div class="fixed inset-0 bg-grid-pattern pointer-events-none z-0"></div>
    <div class="fixed top-[-10%] left-[10%] w-[500px] sm:w-[700px] h-[500px] sm:h-[700px] rounded-full bg-[#93F514]/12 blur-[130px] pointer-events-none animate-pulse-glow z-0"></div>
    <div class="fixed bottom-[-10%] right-[10%] w-[450px] sm:w-[600px] h-[450px] sm:h-[600px] rounded-full bg-[#46ee40]/10 blur-[140px] pointer-events-none animate-pulse-glow-delayed z-0"></div>
    <div class="fixed top-[40%] right-[25%] w-[350px] h-[350px] rounded-full bg-[#5FE6B6]/5 blur-[120px] pointer-events-none z-0"></div>

    <!-- Main Content Container -->
    <main class="relative z-10 w-full flex justify-center items-center">
        {{ $slot }}
    </main>

    <!-- 4-Dot Locale Switch Loader Component -->
    <x-locale-loader />

</body>

</html>
