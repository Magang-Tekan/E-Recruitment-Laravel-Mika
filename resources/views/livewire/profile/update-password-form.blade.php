<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function with(): array
    {
        return [
            'hasCurrentPassword' => ! empty(Auth::user()->password),
        ];
    }

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        $user = Auth::user();
        $hasCurrentPassword = ! empty($user->password);

        $rules = [
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ];

        if ($hasCurrentPassword) {
            $rules['current_password'] = ['required', 'string', 'current_password'];
        }

        try {
            $validated = $this->validate($rules);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section x-data="{
    showCurrent: false,
    showNew: false,
    showConfirm: false,
    newPass: @entangle('password'),
    get lengthValid() { return (this.newPass || '').length >= 8; },
    get hasNumber() { return /\d/.test(this.newPass || ''); },
    get hasMixed() { return /[a-z]/.test(this.newPass || '') && /[A-Z]/.test(this.newPass || ''); }
}">
    <header class="flex items-start gap-4 pb-5 border-b border-slate-100 dark:border-[#1D2E54]">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-[#14203A] border border-blue-200/80 dark:border-[#1D2E54] text-blue-600 dark:text-[#93F514] flex items-center justify-center shrink-0 shadow-xs">
            @if ($hasCurrentPassword)
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            @else
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            @endif
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                {{ $hasCurrentPassword ? 'Perbarui Kata Sandi' : 'Buat Kata Sandi Akun' }}
            </h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-[#93A5C9] leading-relaxed">
                {{ $hasCurrentPassword 
                    ? 'Amankan akun Anda dengan menggunakan kombinasi kata sandi yang kuat dan unik.' 
                    : 'Akun Anda saat ini masuk melalui Google dan belum memiliki kata sandi lokal. Buat kata sandi agar Anda juga dapat masuk dengan email dan kata sandi biasa.' }}
            </p>
        </div>
    </header>

    @if (! $hasCurrentPassword)
        <!-- Google Account Notification Callout -->
        <div class="mt-5 p-4 rounded-xl bg-blue-50/80 dark:bg-[#14203A] border border-blue-200/80 dark:border-[#1D2E54] flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-[#93F514] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                <span class="font-bold block text-slate-900 dark:text-white mb-0.5">Akun Anda Terhubung Melalui Google</span>
                Email akun Anda terhubung dengan Google (<strong>{{ auth()->user()->email }}</strong>). Saat ini akun belum memiliki kata sandi lokal. Setelah membuat kata sandi baru di bawah ini, Anda memiliki fleksibilitas untuk masuk dengan <strong>tombol Google</strong> ataupun dengan <strong>email & kata sandi biasa</strong>.
            </div>
        </div>
    @endif

    <form wire:submit="updatePassword" class="mt-6 space-y-5">
        @if ($hasCurrentPassword)
            <!-- Current Password -->
            <div>
                <label for="update_password_current_password" class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                    Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                </label>
                <div class="relative rounded-xl shadow-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <input wire:model="current_password" 
                           id="update_password_current_password" 
                           name="current_password" 
                           :type="showCurrent ? 'text' : 'password'" 
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-[#14203A] focus:border-blue-500 dark:focus:border-[#93F514] focus:ring-4 focus:ring-blue-500/10 dark:focus:ring-[#93F514]/10 transition duration-150" 
                           placeholder="Masukkan kata sandi lama Anda"
                           autocomplete="current-password" />
                    <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                        <svg x-show="!showCurrent" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showCurrent" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('current_password')" class="mt-1.5" />
            </div>
        @endif

        <!-- New Password -->
        <div>
            <label for="update_password_password" class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                {{ $hasCurrentPassword ? 'Kata Sandi Baru' : 'Kata Sandi' }} <span class="text-rose-500">*</span>
            </label>
            <div class="relative rounded-xl shadow-xs">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <input wire:model="password" 
                       id="update_password_password" 
                       name="password" 
                       :type="showNew ? 'text' : 'password'" 
                       class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-[#14203A] focus:border-blue-500 dark:focus:border-[#93F514] focus:ring-4 focus:ring-blue-500/10 dark:focus:ring-[#93F514]/10 transition duration-150" 
                       placeholder="Minimal 8 karakter"
                       autocomplete="new-password" />
                <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                    <svg x-show="!showNew" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showNew" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            
            <!-- Dynamic Password Criteria Badge Checklist -->
            <div class="mt-2.5 flex flex-wrap gap-2 text-[11px]">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-medium transition"
                      :class="lengthValid ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-500 dark:bg-[#14203A] dark:text-[#93A5C9] border border-transparent dark:border-[#1D2E54]'">
                    <svg class="w-3 h-3" :class="lengthValid ? 'text-emerald-600 dark:text-[#93F514]' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    Min. 8 Karakter
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-medium transition"
                      :class="hasNumber ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-500 dark:bg-[#14203A] dark:text-[#93A5C9] border border-transparent dark:border-[#1D2E54]'">
                    <svg class="w-3 h-3" :class="hasNumber ? 'text-emerald-600 dark:text-[#93F514]' : 'text-slate-400'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    Ada Angka
                </span>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
            </label>
            <div class="relative rounded-xl shadow-xs">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <input wire:model="password_confirmation" 
                       id="update_password_password_confirmation" 
                       name="password_confirmation" 
                       :type="showConfirm ? 'text' : 'password'" 
                       class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-[#14203A] focus:border-blue-500 dark:focus:border-[#93F514] focus:ring-4 focus:ring-blue-500/10 dark:focus:ring-[#93F514]/10 transition duration-150" 
                       placeholder="Ulangi kata sandi baru"
                       autocomplete="new-password" />
                <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                    <svg x-show="!showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <!-- Action Buttons & Feedback -->
        <div class="pt-3 flex items-center justify-between">
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition-all duration-200 active:scale-[0.98] disabled:opacity-50">
                <svg wire:loading.remove wire:target="updatePassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg wire:loading wire:target="updatePassword" class="w-4 h-4 animate-spin text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ $hasCurrentPassword ? 'Simpan Perubahan' : 'Simpan & Buat Kata Sandi' }}</span>
            </button>

            <x-action-message class="text-xs font-medium text-emerald-600 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/50 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800" on="password-updated">
                {{ $hasCurrentPassword ? '✓ Kata sandi berhasil diperbarui!' : '✓ Kata sandi baru berhasil dibuat!' }}
            </x-action-message>
        </div>
    </form>
</section>
