<?php

use App\Livewire\Actions\Logout;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
                Verifikasi Email
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1.5 leading-relaxed">
                Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda melalui tautan yang telah kami kirimkan ke kotak masuk email Anda.
            </p>
        </div>

        <!-- Session Status Alert -->
        @if (session('status') == 'verification-link-sent')
            <div class="mb-5 p-3.5 rounded-xl bg-[#93F514]/10 border border-[#93F514]/30 text-[#93F514] text-xs font-medium flex items-start gap-2.5">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="leading-relaxed">Tautan verifikasi baru telah berhasil dikirim ke alamat email Anda.</span>
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-3">
            <button wire:click="sendVerification" 
                    type="button"
                    wire:loading.attr="disabled"
                    wire:loading.class="!cursor-not-allowed opacity-75"
                    class="w-full py-3 px-4 bg-[#93F514] hover:bg-[#82dc0e] active:scale-[0.99] disabled:opacity-60 text-black font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                <span wire:loading.remove wire:target="sendVerification">Kirim Ulang Email Verifikasi</span>
                <span wire:loading wire:target="sendVerification" class="inline-flex items-center gap-2">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-black" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Mengirim...</span>
                </span>
            </button>

            <button wire:click="logout" 
                    type="button" 
                    class="w-full py-2.5 px-4 text-xs font-medium text-gray-400 hover:text-white rounded-xl hover:bg-white/5 transition cursor-pointer">
                Keluar / Ganti Akun
            </button>
        </div>
    </div>
</div>
