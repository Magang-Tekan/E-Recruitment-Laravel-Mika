<!-- 4-Dot Loading Overlay saat Pergantian Bahasa (Identik dengan Loader Login Mika) -->
<div id="mika-locale-loader"
    class="fixed inset-0 z-[99999] bg-[#040904]/85 backdrop-blur-[2px] hidden flex-col items-center justify-center select-none pointer-events-auto transition-opacity duration-200"
    style="display: none; opacity: 0;"
    aria-hidden="true"
    role="status"
    aria-live="polite">
    <!-- 4-Dot Jumping Wave Loader (Sesuai Referensi Login) -->
    <div class="flex items-center gap-3 h-10 px-2">
        <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-1 inline-block"></span>
        <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-2 inline-block"></span>
        <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-3 inline-block"></span>
        <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-4 inline-block"></span>
    </div>
    <p id="mika-locale-loader-text" class="mt-2.5 text-xs font-semibold text-white tracking-wide">
        <span class="text-[#93F514]">Memproses</span> Bahasa...
    </p>
</div>

<style>
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
        animation: mikaDotBounce 1.3s infinite ease-in-out !important;
        animation-delay: 0s !important;
    }
    .animate-dot-2 {
        animation: mikaDotBounce 1.3s infinite ease-in-out !important;
        animation-delay: 0.16s !important;
    }
    .animate-dot-3 {
        animation: mikaDotBounce 1.3s infinite ease-in-out !important;
        animation-delay: 0.32s !important;
    }
    .animate-dot-4 {
        animation: mikaDotBounce 1.3s infinite ease-in-out !important;
        animation-delay: 0.48s !important;
    }
</style>

<script>
    (function() {
        if (window.mikaSwitchLocaleInitialized) return;
        window.mikaSwitchLocaleInitialized = true;

        window.mikaSwitchLocale = function(newLocale, currentUrl) {
            const current = '{{ app()->getLocale() }}';
            if (newLocale === current && !window.mikaLocaleForce) return;
            if (window.mikaLocaleSwitching) return;
            window.mikaLocaleSwitching = true;

            // 1. Tampilkan 4-Dot Loading Overlay identik dengan halaman Login
            let loader = document.getElementById('mika-locale-loader');
            let loaderText = document.getElementById('mika-locale-loader-text');
            
            if (!loader) {
                // Fallback jika elemen belum ada di DOM: buat secara on-the-fly
                loader = document.createElement('div');
                loader.id = 'mika-locale-loader';
                loader.className = 'fixed inset-0 z-[99999] bg-[#040904]/85 backdrop-blur-[2px] flex flex-col items-center justify-center select-none pointer-events-auto transition-opacity duration-200';
                loader.innerHTML = `
                    <div class="flex items-center gap-3 h-10 px-2">
                        <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-1 inline-block"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-2 inline-block"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-3 inline-block"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-4 inline-block"></span>
                    </div>
                    <p id="mika-locale-loader-text" class="mt-2.5 text-xs font-semibold text-white tracking-wide"></p>
                `;
                document.body.appendChild(loader);
                loaderText = document.getElementById('mika-locale-loader-text');
            }

            if (loader) {
                if (loaderText) {
                    if (newLocale === 'en') {
                        loaderText.innerHTML = (current === 'id') 
                            ? '<span class="text-[#93F514]">Mengubah</span> ke Bahasa Inggris...' 
                            : '<span class="text-[#93F514]">Switching</span> to English...';
                    } else {
                        loaderText.innerHTML = (current === 'en') 
                            ? '<span class="text-[#93F514]">Switching</span> to Indonesian...' 
                            : '<span class="text-[#93F514]">Mengubah</span> ke Bahasa Indonesia...';
                    }
                }
                loader.style.display = 'flex';
                loader.removeAttribute('aria-hidden');
                void loader.offsetWidth;
                loader.style.opacity = '1';
            }

            // 2. Set client cookie instan (plaintext, unencrypted berkat bootstrap/app.php)
            document.cookie = 'locale=' + encodeURIComponent(newLocale) + ';path=/;max-age=31536000;SameSite=Lax';

            // 3. Optimistic visual switch pada tombol pill bahasa
            const isDarkTheme = document.documentElement.classList.contains('dark') || !document.documentElement.classList.contains('light-mode');
            document.querySelectorAll('[data-lang-btn]').forEach(function(el) {
                const btnLocale = el.getAttribute('data-lang-btn');
                if (btnLocale === newLocale) {
                    el.classList.add('nav-lang-btn-active', 'font-black');
                    if (isDarkTheme) {
                        el.classList.add('bg-[#93F514]', 'text-black');
                        el.classList.remove('text-gray-400', 'text-slate-500', 'text-slate-400', 'bg-[#3C7800]', 'bg-emerald-600', 'text-white');
                    } else {
                        el.classList.add('bg-[#3C7800]', 'text-white');
                        el.classList.remove('text-gray-400', 'text-slate-500', 'text-slate-400', 'bg-[#93F514]', 'text-black');
                    }
                } else {
                    el.classList.remove('nav-lang-btn-active', 'bg-[#93F514]', 'text-black', 'bg-[#3C7800]', 'bg-emerald-600', 'text-white', 'font-black');
                    if (isDarkTheme) {
                        el.classList.add('text-gray-400');
                        el.classList.remove('text-slate-500');
                    } else {
                        el.classList.add('text-slate-500');
                        el.classList.remove('text-gray-400');
                    }
                }
            });

            // 4. URL Tujuan & CSRF
            const target = currentUrl || window.location.href;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            let reloaded = false;
            const doReload = function() {
                if (!reloaded) {
                    reloaded = true;
                    // Tetap pertahankan loader saat browser reload
                    window.location.reload();
                }
            };

            // Animasi gelombang 4-dot berjalan ~280ms agar terasa cepat namun mulus & memuaskan
            const startTime = performance.now();
            const minDisplayMs = 280;

            const triggerSmoothReload = function() {
                const elapsed = performance.now() - startTime;
                const delay = Math.max(0, minDisplayMs - elapsed);
                setTimeout(doReload, delay);
            };

            // Fallback timeout: Jika server/jaringan lambat, tetap reload setelah maksimal 1.2 detik
            const safetyTimer = setTimeout(function() {
                if (!reloaded) {
                    reloaded = true;
                    window.location.href = '/lang/' + newLocale + '?return=' + encodeURIComponent(target);
                }
            }, 1200);

            try {
                fetch('/lang/' + newLocale, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ return: target })
                }).then(function() {
                    clearTimeout(safetyTimer);
                    triggerSmoothReload();
                }).catch(function() {
                    clearTimeout(safetyTimer);
                    triggerSmoothReload();
                });
            } catch (err) {
                clearTimeout(safetyTimer);
                triggerSmoothReload();
            }
        };
    })();
</script>
