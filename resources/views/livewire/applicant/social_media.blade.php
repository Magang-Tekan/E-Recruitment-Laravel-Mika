<div class="space-y-6">

    <!-- Header Card -->
    <div
        class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Social Media</h2>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] border border-slate-200 dark:border-[#1D2E54]">Opsional</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">* Cantumkan tautan profil media sosial Anda agar rekruter dapat mengenal Anda lebih dekat.</p>
            </div>
        </div>
    </div>

    <!-- Success Flash Alert -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-xl shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm font-medium">{{ session('message') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Form Social Media -->
    <form wire:submit.prevent="save" class="bg-white dark:bg-[#0D1527] p-6 md:p-8 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] shadow-sm space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- 1. LinkedIn -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider flex items-center gap-2">
                    <span class="p-1.5 bg-blue-100 dark:bg-[#14203A] text-blue-600 dark:text-blue-400 rounded-lg border border-transparent dark:border-[#1D2E54]">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                        </svg>
                    </span>
                    <span>LinkedIn</span>
                </label>
                <input type="url" wire:model="linkedin"
                    placeholder="https://linkedin.com/in/username"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                @error('linkedin')
                    <span class="text-xs text-red-500 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- 2. Instagram -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider flex items-center gap-2">
                    <span class="p-1.5 bg-pink-100 dark:bg-[#14203A] text-pink-600 dark:text-pink-400 rounded-lg border border-transparent dark:border-[#1D2E54]">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </span>
                    <span>Instagram</span>
                </label>
                <input type="url" wire:model="instagram"
                    placeholder="https://instagram.com/username"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                @error('instagram')
                    <span class="text-xs text-red-500 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- 3. Twitter / X -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider flex items-center gap-2">
                    <span class="p-1.5 bg-sky-100 dark:bg-[#14203A] text-sky-600 dark:text-sky-400 rounded-lg border border-transparent dark:border-[#1D2E54]">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </span>
                    <span>Twitter / X</span>
                </label>
                <input type="url" wire:model="twitter"
                    placeholder="https://x.com/username"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                @error('twitter')
                    <span class="text-xs text-red-500 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- 4. TikTok -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider flex items-center gap-2">
                    <span class="p-1.5 bg-stone-100 dark:bg-[#14203A] text-stone-700 dark:text-stone-300 rounded-lg border border-transparent dark:border-[#1D2E54]">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-1-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.28-3.05 1.52-6.04 4.39-7.1 1.07-.4 2.23-.51 3.36-.33v4.23c-.78-.18-1.61-.09-2.3.26-.95.48-1.55 1.47-1.55 2.53.01 1.34 1.03 2.47 2.37 2.54 1.27.07 2.45-.8 2.7-2.05.08-.41.1-.83.1-1.25V.02z"/>
                        </svg>
                    </span>
                    <span>TikTok</span>
                </label>
                <input type="url" wire:model="tiktok"
                    placeholder="https://tiktok.com/@username"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                @error('tiktok')
                    <span class="text-xs text-red-500 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- 5. Facebook -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider flex items-center gap-2">
                    <span class="p-1.5 bg-blue-100 dark:bg-[#14203A] text-blue-600 dark:text-blue-400 rounded-lg border border-transparent dark:border-[#1D2E54]">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </span>
                    <span>Facebook</span>
                </label>
                <input type="url" wire:model="facebook"
                    placeholder="https://facebook.com/username"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                @error('facebook')
                    <span class="text-xs text-red-500 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- 6. GitHub / Website / Portofolio -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider flex items-center gap-2">
                    <span class="p-1.5 bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 rounded-lg border border-transparent dark:border-[#1D2E54]">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/>
                        </svg>
                    </span>
                    <span>GitHub / Portofolio / Website</span>
                </label>
                <input type="url" wire:model="github"
                    placeholder="https://github.com/username atau website portofolio"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                @error('github')
                    <span class="text-xs text-red-500 block">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="flex items-center justify-end pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
            <button type="submit" wire:loading.attr="disabled"
                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold rounded-xl transition flex items-center gap-2">
                <span wire:loading.remove wire:target="save">Simpan Social Media</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </form>

</div>
