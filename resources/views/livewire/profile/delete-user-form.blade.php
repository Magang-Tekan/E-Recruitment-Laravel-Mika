<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: false);
    }
}; ?>

<section class="space-y-6">
    <header class="flex items-start gap-4 pb-5 border-b border-slate-100 dark:border-[#1D2E54]">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-100 dark:border-rose-900/60 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0 shadow-xs">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                Hapus Akun Permanen
            </h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-[#93A5C9] leading-relaxed">
                Tindakan ini tidak dapat dibatalkan. Semua berkas, data lamaran, dan riwayat Anda akan terhapus.
            </p>
        </div>
    </header>

    <div class="p-4 bg-rose-50/60 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 rounded-xl flex items-start gap-3">
        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div class="text-xs text-rose-800 dark:text-rose-300 leading-relaxed">
            <strong class="font-semibold block mb-0.5">Perhatian:</strong>
            Sebelum menghapus akun, pastikan Anda telah mengunduh semua data atau dokumen penting yang ingin disimpan.
        </div>
    </div>

    <div class="pt-2">
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-50 hover:bg-rose-600 dark:bg-rose-950/40 dark:hover:bg-rose-600 text-rose-600 hover:text-white dark:text-rose-300 dark:hover:text-white border border-rose-200 dark:border-rose-800 hover:border-rose-600 text-xs font-semibold rounded-xl shadow-xs hover:shadow-md hover:shadow-rose-500/20 transition-all duration-200 active:scale-[0.98]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span>Hapus Akun Saya</span>
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6 sm:p-7 space-y-6 bg-white dark:bg-[#0D1527] rounded-2xl border border-slate-200 dark:border-[#1D2E54]">

            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                        Konfirmasi Penghapusan Akun
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-0.5">
                        Tindakan ini permanen dan tidak dapat dipulihkan kembali.
                    </p>
                </div>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-[#14203A] p-4 rounded-xl border border-slate-100 dark:border-[#1D2E54]">
                Apakah Anda benar-benar yakin? Masukkan kata sandi akun Anda untuk mengonfirmasi permintaan hapus akun secara permanen.
            </p>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                    Kata Sandi Anda <span class="text-rose-500">*</span>
                </label>
                <div class="relative rounded-xl shadow-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input
                        wire:model="password"
                        id="password"
                        name="password"
                        type="password"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition duration-150"
                        placeholder="Ketik kata sandi untuk konfirmasi"
                    />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition active:scale-[0.98]">
                    Batalkan
                </button>

                <button type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 focus:ring-4 focus:ring-rose-500/20 text-white text-xs font-semibold rounded-xl shadow-md hover:shadow-rose-500/25 transition-all duration-200 active:scale-[0.98] disabled:opacity-50">
                    <svg wire:loading wire:target="deleteUser" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Ya, Hapus Akun Sekarang</span>
                </button>
            </div>
        </form>
    </x-modal>
</section>
