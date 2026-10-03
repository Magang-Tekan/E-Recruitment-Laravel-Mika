<?php

use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $errorMessage = __($status);
            if ($errorMessage === 'passwords.user' || $errorMessage === $status) {
                $errorMessage = 'Kami tidak dapat menemukan akun dengan alamat email tersebut.';
            }

            $this->addError('email', $errorMessage);

            return;
        }

        $this->reset('email');

        $successMessage = __($status);
        if ($successMessage === 'passwords.sent' || $successMessage === $status) {
            $successMessage = 'Kami telah mengirimkan tautan reset kata sandi ke alamat email Anda. Silakan periksa kotak masuk atau spam email Anda.';
        }

        session()->flash('status', $successMessage);
    }

    public function with(): array
    {
        $mainCompany = Cache::remember('frontend_main_company', 86400, function () {
            return Company::where('name', 'like', '%Mitra Karya Analitika%')
                ->orWhere('name', 'like', '%MIKA%')
                ->first() ?? Company::first();
        });

        return [
            'mainCompany' => $mainCompany,
            'logoUrl' => $mainCompany?->logo_url ?: asset('storage/logo/mikaaaa.png'),
        ];
    }
}; ?>

<div class="w-full max-w-md mx-auto">
    <!-- Back to Login Navigation -->
    <div class="mb-5">
        <a href="{{ route('login') }}" 
           class="inline-flex items-center gap-2 text-xs font-medium text-gray-400 hover:text-[#93F514] transition-colors group">
            <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Halaman Masuk</span>
        </a>
    </div>

    <!-- Main Solid Matte Card (Clean, Sharp, Anti-Slop, No Glow) -->
    <div class="bg-[#0A120A] rounded-2xl border border-white/15 p-6 sm:p-8 shadow-2xl relative">
        
        <!-- Brand Header (Matching Login Dynamic Brand) -->
        <div class="flex items-center justify-between pb-5 border-b border-white/10">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group" title="{{ $mainCompany->name ?? 'Mitra Karya Analitika' }}">
                <div class="p-2 rounded-xl bg-black/60 border border-white/15 group-hover:border-[#93F514]/40 transition-colors">
                    <img src="{{ $logoUrl }}" 
                         alt="{{ $mainCompany->name ?? 'Logo MIKA' }}" 
                         class="h-8 w-auto object-contain rounded-lg"
                         onerror="this.onerror=null; this.src='{{ asset('storage/logo/mikaaaa.png') }}';">
                </div>
                <div class="text-left">
                    <span class="heading-font text-base sm:text-lg font-bold tracking-tight text-white flex items-center gap-1">
                        MIKA <span class="text-[#93F514]">CAREER</span>
                    </span>
                    <span class="block text-[11px] text-gray-400 font-normal">
                        Portal Rekrutmen Online
                    </span>
                </div>
            </a>
        </div>

        <!-- Section Title & Description -->
        <div class="mt-5 mb-6">
            <h1 class="heading-font text-xl sm:text-2xl font-bold text-white tracking-tight">
                Lupa Kata Sandi?
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1.5 leading-relaxed">
                Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi akun Anda.
            </p>
        </div>

        <!-- Session Status Alert -->
        @php
            $statusText = session('status');
            if ($statusText === 'passwords.sent' || $statusText === 'We have emailed your password reset link.') {
                $statusText = 'Kami telah mengirimkan tautan reset kata sandi ke alamat email Anda. Silakan periksa kotak masuk atau spam email Anda.';
            }
        @endphp
        @if ($statusText)
            <div class="mb-5 p-3.5 rounded-xl bg-[#93F514]/10 border border-[#93F514]/30 text-[#93F514] text-xs font-medium flex items-start gap-2.5">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="leading-relaxed">{{ $statusText }}</span>
            </div>
        @endif

        <form wire:submit="sendPasswordResetLink" class="space-y-4 sm:space-y-5">
            <!-- Email Address Input -->
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
                    <input wire:model="email" 
                           id="email" 
                           type="email" 
                           name="email" 
                           required 
                           autofocus 
                           placeholder="nama@email.com"
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />
                </div>
                @if ($errors->has('email'))
                    <p class="mt-1.5 text-xs text-red-400 font-normal flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $errors->first('email') }}</span>
                    </p>
                @endif
            </div>

            <!-- Submit Button (Matte, High Contrast, Centered Loading State) -->
            <div class="pt-2">
                <button type="submit" 
                        wire:loading.attr="disabled"
                        wire:loading.class="!cursor-not-allowed opacity-75"
                        class="w-full py-3 px-4 bg-[#93F514] hover:bg-[#82dc0e] active:scale-[0.99] disabled:opacity-60 text-black font-semibold text-sm rounded-xl transition flex items-center justify-center cursor-pointer">
                    <span wire:loading.remove wire:target="sendPasswordResetLink" class="inline-flex items-center justify-center gap-2">
                        <span>Kirim Tautan Reset</span>
                    </span>
                    <span wire:loading.flex wire:target="sendPasswordResetLink" class="items-center justify-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-black shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="whitespace-nowrap">Mengirim Tautan...</span>
                    </span>
                </button>
            </div>
        </form>

        <!-- Footer Note -->
        <div class="pt-4 mt-6 border-t border-white/10 text-center">
            <p class="text-xs text-gray-400">
                Sudah ingat kata sandi Anda?
                <a href="{{ route('login') }}" class="text-[#93F514] hover:underline font-semibold ml-1 transition">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</div>
