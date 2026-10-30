<div class="space-y-6">

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="text-xs sm:text-sm font-semibold">{{ session('message') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300 p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Main Card: Edit Teks Hero -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm space-y-6">

        <!-- Tabs Header: Bahasa Indonesia / English & Auto-Translate -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <button type="button"
                        wire:click="$set('activeTab', 'id')"
                        class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ $activeTab === 'id' ? 'bg-[#93F514] text-black shadow-sm font-extrabold' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 hover:text-gray-900' }}">
                    Bahasa Indonesia
                </button>
                <button type="button"
                        wire:click="$set('activeTab', 'en')"
                        class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ $activeTab === 'en' ? 'bg-[#93F514] text-black shadow-sm font-extrabold' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 hover:text-gray-900' }}">
                    English
                </button>
            </div>

            <button type="button" 
                    wire:click="autoTranslate"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-gray-50 dark:bg-slate-800/80 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-[#93F514] transition cursor-pointer self-start sm:self-auto">
                <span wire:loading.remove wire:target="autoTranslate">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                </span>
                <span wire:loading wire:target="autoTranslate" class="animate-spin">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </span>
                <span>{{ __('Auto-Terjemah ke EN') }}</span>
            </button>
        </div>

        <!-- Form Teks Bahasa Indonesia -->
        <div class="space-y-5 {{ $activeTab === 'id' ? 'block' : 'hidden' }}">
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1.5">
                    {{ __('Judul Baris 1 (Teks Putih)') }} <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       wire:model="title_prefix_id"
                       placeholder="Contoh: Temukan Karir Impian,"
                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-slate-800 border @error('title_prefix_id') border-rose-500 @else border-gray-200 dark:border-slate-700 @enderror text-gray-900 dark:text-white focus:ring-2 focus:ring-[#93F514] focus:outline-none transition">
                @error('title_prefix_id')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1.5 flex items-center gap-1.5">
                    <span>{{ __('Judul Baris 2 (Highlight Aksen Hijau Neon)') }}</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#93F514] inline-block shadow-xs"></span>
                    <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       wire:model="title_highlight_id"
                       placeholder="Contoh: Wujudkan Potensi Terbaikmu"
                       class="w-full px-4 py-2.5 text-sm font-semibold rounded-xl bg-gray-50 dark:bg-slate-800 border @error('title_highlight_id') border-rose-500 @else border-gray-200 dark:border-slate-700 @enderror text-emerald-600 dark:text-[#93F514] focus:ring-2 focus:ring-[#93F514] focus:outline-none transition">
                @error('title_highlight_id')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1.5">
                    {{ __('Deskripsi / Slogan') }} <span class="text-rose-500">*</span>
                </label>
                <textarea rows="3"
                          wire:model="description_id"
                          placeholder="Masukkan deskripsi singkat / slogan ajakan pelamar..."
                          class="w-full px-4 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-slate-800 border @error('description_id') border-rose-500 @else border-gray-200 dark:border-slate-700 @enderror text-gray-900 dark:text-white focus:ring-2 focus:ring-[#93F514] focus:outline-none transition"></textarea>
                @error('description_id')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Form Teks English -->
        <div class="space-y-5 {{ $activeTab === 'en' ? 'block' : 'hidden' }}">
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1.5">
                    {{ __('Judul Baris 1 - English (White Text)') }}
                </label>
                <input type="text"
                       wire:model="title_prefix_en"
                       placeholder="Example: Find Your Dream Career,"
                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#93F514] focus:outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1.5 flex items-center gap-1.5">
                    <span>{{ __('Judul Baris 2 - English (Neon Green Highlight)') }}</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#93F514] inline-block shadow-xs"></span>
                </label>
                <input type="text"
                       wire:model="title_highlight_en"
                       placeholder="Example: Realize Your Best Potential"
                       class="w-full px-4 py-2.5 text-sm font-semibold rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-emerald-600 dark:text-[#93F514] focus:ring-2 focus:ring-[#93F514] focus:outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1.5">
                    {{ __('Deskripsi / Slogan - English') }}
                </label>
                <textarea rows="3"
                          wire:model="description_en"
                          placeholder="Example: Join thousands of talented professionals. Explore job openings..."
                          class="w-full px-4 py-2.5 text-sm rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#93F514] focus:outline-none transition"></textarea>
            </div>
        </div>

        <!-- Optional Collapsible: Pengaturan Gambar Latar (Opsional) -->
        <div x-data="{ showImageConfig: false }" class="pt-2 border-t border-gray-100 dark:border-slate-800">
            <button type="button"
                    @click="showImageConfig = !showImageConfig"
                    class="text-xs font-semibold text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-slate-200 flex items-center gap-1.5 py-1 cursor-pointer">
                <svg class="w-4 h-4 transition-transform duration-200" :class="showImageConfig ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>{{ __('Pengaturan Gambar Latar Belakang (Opsional)') }}</span>
            </button>

            <div x-show="showImageConfig" x-collapse x-cloak class="mt-4 p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-700 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">
                        {{ __('Upload Gambar Baru (JPG, PNG, WebP maks 5MB)') }}
                    </label>
                    <input type="file" 
                           wire:model="uploaded_image" 
                           accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#93F514] file:text-black hover:file:bg-[#7edc0b] file:cursor-pointer cursor-pointer">
                    @error('uploaded_image')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">
                        {{ __('Atau Masukkan URL Gambar Eksternal') }}
                    </label>
                    <input type="url"
                           wire:model="custom_image_url"
                           placeholder="https://example.com/banner.jpg"
                           class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#93F514] focus:outline-none">
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-slate-700">
                    <span class="text-[11px] text-gray-500 dark:text-slate-400">
                        {{ __('Gambar saat ini:') }} {{ str_starts_with($current_background_image, 'http') ? 'URL Eksternal' : 'File Tersimpan' }}
                    </span>
                    <button type="button"
                            wire:click="resetToDefaultImage"
                            class="text-[11px] font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        {{ __('Reset Gambar ke Default') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Action Buttons -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
            <button type="button"
                    wire:click="resetToDefaultTexts"
                    class="text-xs font-semibold text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white transition flex items-center gap-1.5 self-start sm:self-auto">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>{{ __('Reset Teks ke Default') }}</span>
            </button>

            <button type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto px-6 py-2.5 text-xs font-bold rounded-xl bg-black dark:bg-[#93F514] hover:bg-zinc-800 dark:hover:bg-[#a6ff2e] text-white dark:text-black shadow-md shadow-black/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span wire:loading.remove wire:target="save">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                <span wire:loading wire:target="save" class="animate-spin">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </span>
                <span>{{ __('Simpan Perubahan') }}</span>
            </button>
        </div>

    </div>

</div>
