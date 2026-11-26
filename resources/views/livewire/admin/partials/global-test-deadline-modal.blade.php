<!-- Global Test Deadline Setting Modal -->
<div x-show="showGlobalDeadlineModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showGlobalDeadlineModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showGlobalDeadlineModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showGlobalDeadlineModal"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @keydown.escape.window="showGlobalDeadlineModal = false"
            class="inline-block align-bottom sm:align-middle bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md w-full border border-gray-200 dark:border-slate-800">

            <form action="{{ route('admin.settings.test_deadline') }}" method="POST" @submit="isSubmittingGlobalDeadline = true" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-3 pb-4 border-b border-gray-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Default Batas Tes Global</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Berlaku otomatis untuk semua lowongan yang tidak mengatur batas khusus.</p>
                    </div>
                </div>

                <div>
                    <label for="global_default_test_deadline_days" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                        Lama Pengerjaan Tes (Hari Kerja Senin–Jumat) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" id="global_default_test_deadline_days" name="default_test_deadline_days" min="1" max="30" required
                            value="{{ old('default_test_deadline_days', $globalTestDeadlineDays) }}"
                            class="w-28 px-3 py-2 text-sm font-bold rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <span class="text-xs font-medium text-gray-600 dark:text-slate-300">hari kerja (&times; 24 jam kerja)</span>
                    </div>
                    <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-2 leading-relaxed">
                        Menghitung hari Senin–Jumat (Sabtu &amp; Minggu dilewati). Perubahan ini hanya berlaku untuk pelamar yang baru lolos berkas (Reviewed) atau baru ditautkan tes setelah pengaturan disimpan.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100 dark:border-slate-800">
                    <button type="button" @click="showGlobalDeadlineModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingGlobalDeadline" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 active:scale-95 rounded-xl shadow-xs transition cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                        <span x-text="isSubmittingGlobalDeadline ? 'Menyimpan...' : 'Simpan Default Global'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
