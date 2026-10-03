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
    </style>
</head>

<body class="font-sans antialiased text-[#EEEEEE] bg-[#040904] min-h-screen relative overflow-x-hidden selection:bg-[#93F514] selection:text-black flex items-center justify-center p-3 sm:p-6 lg:p-10">
    
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
                {{ request()->routeIs('login') ? 'Memuat Halaman Masuk...' : 'Memuat Halaman Pendaftaran...' }}
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

</body>

</html>
