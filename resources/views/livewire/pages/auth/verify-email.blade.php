<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: false);
    }
}; ?>

<div class="w-full max-w-md mx-auto">
    <!-- Glassmorphic Card -->
    <div class="glass-card rounded-3xl p-7 sm:p-9 shadow-2xl relative overflow-hidden transition-all duration-300">
        
        <!-- Decorative Glow Accents inside Card -->
        <div class="absolute -top-16 -right-16 w-36 h-36 bg-[#93F514]/15 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-36 h-36 bg-[#46ee40]/10 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Header -->
        <div class="text-center mb-6 relative z-10">
            <div class="inline-flex items-center justify-center p-2.5 rounded-2xl bg-white/[0.04] border border-[#93F514]/30 shadow-lg shadow-[#93F514]/10 mb-4">
                <img src="{{ asset('storage/logo/mikaaaa.png') }}" 
                     alt="Logo MIKA" 
                     class="h-12 w-auto object-contain rounded-lg">
            </div>

            <h1 class="text-2xl font-extrabold text-[#EEEEEE] tracking-tight">
                Verifikasi Email
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-2 font-medium">
                Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda melalui tautan yang telah kami kirimkan.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-5 p-3.5 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Tautan verifikasi baru telah dikirim ke alamat email Anda.</span>
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-3 relative z-10">
            <button wire:click="sendVerification" 
                    type="button"
                    class="w-full py-3.5 px-4 bg-[#93F514] hover:bg-[#82dc0e] active:scale-[0.99] text-black font-extrabold text-sm rounded-xl shadow-lg shadow-[#93F514]/25 hover:shadow-[#93F514]/40 transition-all duration-200 flex items-center justify-center gap-2">
                <span>Kirim Ulang Email Verifikasi</span>
            </button>

            <button wire:click="logout" 
                    type="button" 
                    class="w-full py-2.5 px-4 text-xs font-semibold text-gray-400 hover:text-red-400 rounded-xl hover:bg-white/[0.03] transition">
                Keluar / Ganti Akun
            </button>
        </div>
    </div>
</div>
