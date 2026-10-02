<?php

use App\Models\Company;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $err = __($status);
            if ($err === 'passwords.token' || $err === $status) {
                $err = 'Token reset kata sandi ini tidak valid atau sudah kedaluwarsa.';
            } elseif ($err === 'passwords.user') {
                $err = 'Kami tidak dapat menemukan akun dengan alamat email tersebut.';
            }

            $this->addError('email', $err);

            return;
        }

        $successMsg = __($status);
        if ($successMsg === 'passwords.reset' || $successMsg === $status) {
            $successMsg = 'Kata sandi Anda berhasil diatur ulang. Silakan masuk menggunakan kata sandi baru Anda.';
        }

        Session::flash('status', $successMsg);

        $this->redirectRoute('login', navigate: true);
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
                Buat Sandi Baru
            </h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1.5 leading-relaxed">
                Silakan masukkan kata sandi baru untuk akun Anda.
            </p>
        </div>

        <form wire:submit="resetPassword" class="space-y-4 sm:space-y-5">
            <!-- Email Address (Read-only or verify) -->
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

            <!-- New Password -->
            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-xs font-semibold text-gray-300 mb-1.5">
                    Kata Sandi Baru
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input wire:model="password" 
                           id="password" 
                           x-bind:type="showPassword ? 'text' : 'password'"
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="new-password" 
                           placeholder="Minimal 8 karakter"
                           class="w-full pl-10 pr-11 py-2.5 sm:py-3 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />

                    <button type="button" @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-[#93F514] transition focus:outline-none cursor-pointer" 
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
                @if ($errors->has('password'))
                    <p class="mt-1.5 text-xs text-red-400 font-normal flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $errors->first('password') }}</span>
                    </p>
                @endif
            </div>

            <!-- Confirm New Password -->
            <div x-data="{ showConfirmPassword: false }">
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-300 mb-1.5">
                    Konfirmasi Sandi Baru
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <input wire:model="password_confirmation" 
                           id="password_confirmation" 
                           x-bind:type="showConfirmPassword ? 'text' : 'password'"
                           type="password" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password" 
                           placeholder="Ulangi kata sandi baru"
                           class="w-full pl-10 pr-11 py-2.5 sm:py-3 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />

                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-[#93F514] transition focus:outline-none cursor-pointer" 
                            title="Tampilkan/Sembunyikan Konfirmasi Sandi">
                        <svg x-show="!showConfirmPassword" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showConfirmPassword" x-cloak class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @if ($errors->has('password_confirmation'))
                    <p class="mt-1.5 text-xs text-red-400 font-normal flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $errors->first('password_confirmation') }}</span>
                    </p>
                @endif
            </div>

            <!-- Submit Button (Matte, High Contrast, Centered Loading State) -->
            <div class="pt-2">
                <button type="submit" 
                        wire:loading.attr="disabled"
                        wire:loading.class="!cursor-not-allowed opacity-75"
                        class="w-full py-3 px-4 bg-[#93F514] hover:bg-[#82dc0e] active:scale-[0.99] disabled:opacity-60 text-black font-semibold text-sm rounded-xl transition flex items-center justify-center cursor-pointer">
                    <span wire:loading.remove wire:target="resetPassword" class="inline-flex items-center justify-center gap-2">
                        <span>Simpan Sandi Baru</span>
                    </span>
                    <span wire:loading.flex wire:target="resetPassword" class="items-center justify-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-black shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="whitespace-nowrap">Menyimpan Sandi...</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
