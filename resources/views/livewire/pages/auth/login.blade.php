<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: $this->form->redirectAfterLogin());
    }
}; ?>

<div class="w-full max-w-5xl mx-auto">
    <!-- 4-Dot Loading Overlay saat Proses Masuk (Livewire Submission) -->
    <div wire:loading.flex wire:target="login"
        class="fixed inset-0 z-[100] bg-black/40 backdrop-blur-[2px] flex flex-col items-center justify-center select-none transition-all">
        <!-- 4-Dot Jumping Wave Loader (Sesuai Referensi, Tanpa Shadow) -->
        <div class="flex items-center gap-3 h-10 px-2">
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-1 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-2 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-3 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-4 inline-block"></span>
        </div>
        <p class="mt-2.5 text-xs font-semibold text-white tracking-wide">
            <span class="text-[#93F514]">Memproses</span> Masuk...
        </p>
    </div>

    <!-- 4-Dot Loading Overlay saat Masuk dengan Google -->
    <div id="google-auth-loader"
        class="hidden fixed inset-0 z-[100] bg-black/40 backdrop-blur-[2px] flex flex-col items-center justify-center select-none transition-all">
        <!-- 4-Dot Jumping Wave Loader (Sesuai Referensi, Tanpa Shadow) -->
        <div class="flex items-center gap-3 h-10 px-2">
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-1 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-2 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-3 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-4 inline-block"></span>
        </div>
        <p class="mt-2.5 text-xs font-semibold text-white tracking-wide">
            Memuat Masuk Google...
        </p>
    </div>

    <!-- Main 2-Column Card Container -->
    <div class="glass-card-main rounded-[2rem] sm:rounded-[2.5rem] p-3 sm:p-4 md:p-6 shadow-2xl relative overflow-hidden">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-stretch">
            
            <!-- LEFT COLUMN: Banner / Illustration Showcase (5 Cols) -->
            <div class="lg:col-span-5 banner-gradient rounded-[1.75rem] p-6 sm:p-8 lg:p-9 flex flex-col justify-between relative overflow-hidden min-h-[360px] lg:min-h-[540px]">
                
                <!-- Ambient Glow inside Left Panel -->
                <div class="absolute -top-20 -left-20 w-52 h-52 bg-[#93F514]/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-20 -right-20 w-52 h-52 bg-[#46ee40]/15 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Hexagon Background Motifs (Subtle) -->
                <div class="absolute inset-0 opacity-10 pointer-events-none flex items-center justify-center">
                    <svg class="w-full h-full text-[#93F514]" viewBox="0 0 400 400" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polygon points="200,60 280,105 280,195 200,240 120,195 120,105"/>
                        <polygon points="320,130 380,165 380,235 320,270 260,235 260,165"/>
                        <polygon points="80,130 140,165 140,235 80,270 20,235 20,165"/>
                        <polygon points="200,220 280,265 280,355 200,400 120,355 120,265"/>
                    </svg>
                </div>

                <!-- Top Area: Brand Logo & Tagline -->
                <div class="relative z-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                        <div class="p-2 rounded-xl bg-black/40 border border-[#93F514]/30 shadow-md group-hover:scale-105 transition-transform duration-200">
                            <img src="{{ asset('storage/logo/mikaaaa.png') }}" 
                                 alt="Logo MIKA" 
                                 class="h-8 w-auto object-contain rounded-lg">
                        </div>
                        <div>
                            <span class="heading-font text-base sm:text-lg font-bold tracking-tight text-white flex items-center gap-1">
                                MIKA <span class="text-[#93F514]">CAREER</span>
                            </span>
                            <span class="block text-[10px] text-gray-400 font-normal">
                                Portal Rekrutmen Online
                            </span>
                        </div>
                    </a>

                    <!-- Slogan Heading -->
                    <div class="mt-8">
                        {{-- <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-[#93F514]/15 text-[#93F514] border border-[#93F514]/30 mb-3">
                            Portal Rekrutmen
                        </span> --}}
                        <h2 class="heading-font text-2xl sm:text-3xl font-bold text-white leading-tight">
                            One click to step into your
                            <span class="text-[#93F514]">
                                dream career.
                            </span>
                        </h2>
                    </div>
                </div>

                <!-- Middle / Bottom Illustration -->
                <div class="relative z-10 my-auto py-6 flex items-center justify-center">
                    <div class="relative w-full max-w-[280px]">
                        <!-- Dashboard UI Preview Mockup Card -->
                        <div class="bg-[#051406]/95 backdrop-blur-md rounded-2xl border border-[#93F514]/30 p-4 shadow-2xl">
                            <!-- Mockup Window Bar -->
                            <div class="flex items-center justify-between pb-2 mb-3 border-b border-white/10">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-red-400"></div>
                                    <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                                    <div class="w-2 h-2 rounded-full bg-[#93F514]"></div>
                                </div>
                                <div class="h-1.5 w-14 bg-white/15 rounded-full"></div>
                            </div>
                            
                            <!-- Mockup Dashboard Content -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-[#93F514]/20 border border-[#93F514]/40 flex items-center justify-center text-[#93F514]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div class="flex-1 space-y-1.5">
                                        <div class="h-2 w-24 bg-white/30 rounded-full"></div>
                                        <div class="h-1.5 w-16 bg-[#93F514]/60 rounded-full"></div>
                                    </div>
                                </div>

                                <!-- Mock Chart -->
                                <div class="p-2.5 rounded-lg bg-black/60 border border-white/5 flex items-end justify-between h-14 px-3">
                                    <div class="w-3 bg-white/20 rounded-t h-4"></div>
                                    <div class="w-3 bg-[#93F514]/40 rounded-t h-7"></div>
                                    <div class="w-3 bg-white/20 rounded-t h-5"></div>
                                    <div class="w-3 bg-[#93F514] rounded-t h-10 shadow-[0_0_10px_#93F514]"></div>
                                    <div class="w-3 bg-white/20 rounded-t h-6"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Badge -->
                        {{-- <div class="absolute -bottom-2.5 -right-1 bg-black/90 backdrop-blur-md border border-[#93F514]/40 rounded-xl px-3 py-1.5 shadow-lg flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#93F514]"></span>
                            <span class="text-xs font-semibold text-white">Lowongan Aktif</span>
                        </div> --}}
                    </div>
                </div>

                <!-- Bottom Back Link in Left Side -->
                <div class="relative z-10 pt-2">
                    <a href="{{ url('/') }}" 
                       class="inline-flex items-center gap-2 text-xs font-medium text-gray-300 hover:text-[#93F514] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>

            <!-- RIGHT COLUMN: Sign In Form (7 Cols) -->
            <div class="lg:col-span-7 p-4 sm:p-6 lg:p-8 flex flex-col justify-center relative z-10">
                
                <!-- Form Header -->
                <div class="mb-7">
                    <div class="flex items-center justify-between mb-1.5">
                        <h1 class="heading-font text-2xl sm:text-3xl font-bold text-white tracking-tight">
                            Selamat Datang
                        </h1>
                        {{-- <span class="text-xs font-medium px-3 py-1 rounded-full bg-[#93F514]/10 text-[#93F514] border border-[#93F514]/30">
                            Masuk
                        </span> --}}
                    </div>
                    <p class="text-sm text-gray-400 font-normal">
                        Masuk ke akun <span class="text-white font-medium">MIKA CAREER</span> untuk melanjutkan lamaran Anda.
                    </p>
                </div>

                <!-- Session Status Message (Hilang Otomatis setelah 5 Detik) -->
                @if (session('status'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="mb-5 p-3.5 rounded-xl bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-medium flex items-center justify-between gap-2 shadow-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                        <button type="button" @click="show = false" class="text-[#93F514]/60 hover:text-[#93F514] text-sm leading-none focus:outline-none cursor-pointer">&times;</button>
                    </div>
                @endif

                @php
                    $errorMessage = session('error');
                    if (! $errorMessage && request()->has('timeout')) {
                        $idleMinutes = (int) config('session.idle_timeout', 5);
                        $errorMessage = "Sesi Anda telah berakhir karena tidak ada aktivitas selama {$idleMinutes} menit. Silakan masuk kembali.";
                    }
                @endphp

                @if ($errorMessage)
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="mb-5 p-3.5 rounded-xl bg-red-500/15 border border-red-500/40 text-red-400 text-xs font-medium flex items-center justify-between gap-2 shadow-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $errorMessage }}</span>
                        </div>
                        <button type="button" @click="show = false" class="text-red-400/60 hover:text-red-400 text-sm leading-none focus:outline-none cursor-pointer" title="Tutup">&times;</button>
                    </div>
                @endif

                <!-- Google Sign In Button -->
                <a href="{{ route('auth.google') }}" 
                   onclick="document.getElementById('google-auth-loader')?.classList.remove('hidden')"
                   class="w-full py-3 px-4 mb-5 rounded-xl border border-white/20 bg-white/5 hover:bg-white/10 active:scale-[0.99] text-white font-medium text-sm flex items-center justify-center gap-3 transition shadow-sm hover:border-white/30 group">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span>Masuk dengan Google</span>
                </a>

                <!-- Or Divider -->
                <div class="relative flex items-center justify-center mb-5">
                    <div class="border-t border-white/10 w-full"></div>
                    <span class="bg-[#051406] px-3 text-[11px] text-gray-400 uppercase tracking-wider font-medium absolute">atau email</span>
                </div>

                <form wire:submit="login" class="space-y-4 sm:space-y-5">
                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-300 mb-1.5">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input wire:model="form.email" 
                                   id="email" 
                                   type="email" 
                                   name="email" 
                                   required 
                                   autofocus 
                                   autocomplete="username" 
                                   placeholder="nama@email.com"
                                   class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />
                        </div>
                        @if ($errors->has('form.email'))
                            <p class="mt-1.5 text-xs text-red-400 font-normal flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $errors->first('form.email') }}
                            </p>
                        @endif
                    </div>

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-gray-300">
                                Kata Sandi
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" 
                                   class="text-xs text-gray-400 hover:text-[#93F514] transition">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            
                            <input wire:model="form.password" 
                                   id="password" 
                                   x-bind:type="showPassword ? 'text' : 'password'"
                                   type="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="••••••••"
                                   class="w-full pl-10 pr-11 py-2.5 sm:py-3 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />

                            <button type="button" @click="showPassword = !showPassword" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-[#93F514] transition focus:outline-none" 
                                    title="Tampilkan/Sembunyikan Kata Sandi">
                                <svg x-show="!showPassword" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @if ($errors->has('form.password'))
                            <p class="mt-1.5 text-xs text-red-400 font-normal flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $errors->first('form.password') }}
                            </p>
                        @endif
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember" class="inline-flex items-center cursor-pointer select-none">
                            <input wire:model="form.remember" 
                                   id="remember" 
                                   type="checkbox" 
                                   class="w-4 h-4 rounded bg-black/50 border-white/20 text-[#93F514] focus:ring-[#93F514] focus:ring-offset-0 transition" 
                                   name="remember">
                            <span class="ms-2 text-xs text-gray-300">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:loading.class="!cursor-not-allowed"
                                class="w-full py-3 px-4 bg-[#93F514] hover:bg-[#82dc0e] active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed text-black font-semibold text-sm rounded-xl shadow-lg shadow-[#93F514]/20 hover:shadow-[#93F514]/30 transition flex items-center justify-center gap-2 group cursor-pointer">
                            <span wire:loading.remove wire:target="login" class="inline-flex items-center gap-2">
                                <span>Masuk</span>
                                {{-- <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg> --}}
                            </span>
                            <span wire:loading.inline-flex wire:target="login" class="items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-black shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memproses...</span>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Divider & Register Link -->
                <div class="mt-6 pt-5 border-t border-white/10 text-center">
                    <p class="text-xs text-gray-400">
                        Belum punya akun?
                        <a href="{{ route('register') }}" 
                           class="font-semibold text-[#93F514] hover:underline ml-1">
                            Daftar Sekarang
                        </a>
                    </p>
                </div>

            </div>

        </div>

    </div>
</div>

