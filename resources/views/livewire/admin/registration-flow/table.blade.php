<div class="space-y-6">

    {{-- ===================== NOTIFIKASI ===================== --}}
    @foreach (['create', 'update', 'delete', 'section_saved'] as $flashKey)
        @if (session($flashKey))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-semibold">{{ session($flashKey) }}</span>
                </div>
                <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif
    @endforeach

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
            class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold">{{ session('error') }}</span>
            </div>
            <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- ===================== PENGATURAN SECTION ===================== --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pengaturan Section</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                    Judul, subjudul, dan visibilitas section "Alur Pendaftaran & Seleksi" di halaman beranda. Versi Bahasa Inggris terjemahan otomatis.
                </p>
            </div>
            <a href="{{ route('home') }}#alur-pendaftaran" target="_blank"
                class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-[#93F514]/10 border border-emerald-200 dark:border-[#93F514]/30 hover:bg-emerald-100 dark:hover:bg-[#93F514]/20 transition shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span>Lihat Halaman</span>
            </a>
        </div>

        <form wire:submit="saveSection" class="mt-5 space-y-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Judul Section*</label>
                    <input type="text" wire:model="section_title"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('section_title')
                        <span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Subjudul Section</label>
                    <input type="text" wire:model="section_subtitle"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @error('section_subtitle')
                        <span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-slate-800">
                <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" wire:model="section_enabled"
                        class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:bg-slate-800 dark:border-slate-700">
                    <span class="text-xs font-semibold text-gray-700 dark:text-slate-300">
                        Tampilkan section di beranda
                        <span class="block font-normal text-[11px] text-gray-500 dark:text-slate-400 mt-0.5">
                            Jika dimatikan, section disembunyikan tanpa menghapus data tahap.
                        </span>
                    </span>
                </label>

                <button type="submit" wire:loading.attr="disabled" wire:target="saveSection"
                    class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-xl text-xs font-bold transition shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 active:scale-95 cursor-pointer shrink-0 flex items-center justify-center gap-1.5 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="saveSection">Simpan Pengaturan</span>
                    <span wire:loading.flex wire:target="saveSection" class="flex items-center gap-1.5">
                        <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Menyimpan...</span>
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- ===================== DAFTAR TAHAP ===================== --}}
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-slate-800">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>Daftar Tahap</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-[#93F514]/10 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30">
                        {{ $steps->count() }} Tahap
                    </span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                    Urutan tampil di stepper mengikuti tombol naik/turun. Nomor tahap dibuat otomatis.
                </p>
            </div>
            <button type="button" wire:click="openCreate"
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 shrink-0 self-start sm:self-auto active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Tahap</span>
            </button>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-slate-800">
            @forelse ($steps as $step)
                @php
                    $stepHighlights = is_array($step->highlights) ? $step->highlights : [];
                @endphp
                <div wire:key="step-row-{{ $step->id }}"
                    class="p-5 sm:px-6 flex flex-col lg:flex-row lg:items-center gap-4 {{ $step->is_active ? '' : 'opacity-60' }}">
                    <div class="flex items-start gap-3.5 flex-1 min-w-0">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 font-bold text-gray-800 dark:text-slate-200 text-xs shrink-0">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $step->short_title }}</h4>
                                @if ($step->short_sub)
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 border border-gray-200 dark:border-slate-700">
                                        {{ $step->short_sub }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 truncate">{{ $step->title }}</p>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5 text-[11px] text-gray-500 dark:text-slate-400">
                                <span>{{ count($stepHighlights) }} poin info</span>
                                <span class="text-gray-300 dark:text-slate-700">•</span>
                                <span>Tombol: {{ $step->cta_label ?: '—' }}</span>
                                @if ($step->next_label)
                                    <span class="text-gray-300 dark:text-slate-700">•</span>
                                    <span>{{ $step->next_label }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 lg:gap-3 shrink-0 self-end lg:self-auto">
                        {{-- Urutan --}}
                        <div class="inline-flex rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                            <button type="button" wire:click="moveUp({{ $step->id }})" @disabled($loop->first)
                                title="Naikkan urutan"
                                class="p-2 text-gray-500 hover:text-emerald-600 dark:hover:text-[#93F514] hover:bg-gray-50 dark:hover:bg-slate-800 disabled:opacity-30 disabled:cursor-not-allowed transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                            </button>
                            <button type="button" wire:click="moveDown({{ $step->id }})" @disabled($loop->last)
                                title="Turunkan urutan"
                                class="p-2 border-l border-gray-200 dark:border-slate-700 text-gray-500 hover:text-emerald-600 dark:hover:text-[#93F514] hover:bg-gray-50 dark:hover:bg-slate-800 disabled:opacity-30 disabled:cursor-not-allowed transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>

                        {{-- Status --}}
                        <div class="inline-flex flex-col items-center gap-1">
                            <button type="button" wire:click="toggleActive({{ $step->id }})" role="switch"
                                aria-checked="{{ $step->is_active ? 'true' : 'false' }}" title="Klik untuk mengubah status"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 active:scale-95 {{ $step->is_active ? 'bg-emerald-500 dark:bg-[#93F514]' : 'bg-gray-300 dark:bg-slate-700' }}">
                                <span class="sr-only">Toggle Status</span>
                                <span aria-hidden="true"
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-200 {{ $step->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                            <span class="text-[11px] font-medium select-none {{ $step->is_active ? 'text-emerald-600 dark:text-[#93F514]' : 'text-gray-400 dark:text-slate-500' }}">
                                {{ $step->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        {{-- Aksi --}}
                        <div class="flex items-center gap-1.5">
                            <button type="button" wire:click="edit({{ $step->id }})"
                                class="p-2 rounded-lg text-gray-500 hover:text-emerald-600 dark:hover:text-[#93F514] hover:bg-gray-100 dark:hover:bg-slate-800 transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button type="button" wire:click="confirmDelete({{ $step->id }})"
                                class="p-2 rounded-lg text-gray-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center text-gray-400 dark:text-slate-500">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <svg class="w-8 h-8 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="text-xs">Belum ada tahap. Jalankan seeder atau tambah tahap baru.</span>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ===================== MODAL FORM ===================== --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-gray-200 dark:border-slate-800">
                    <form wire:submit="save">
                        <div class="px-6 pt-6 pb-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ $editingId ? 'Edit Tahap' : 'Tambah Tahap' }}
                                </h3>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-0.5">
                                    Tulis dalam Bahasa Indonesia — versi Inggris dibuat otomatis. Kolom bertanda * wajib diisi.
                                </p>
                            </div>
                            <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Tab form --}}
                        <div class="px-6 pt-2 border-b border-gray-100 dark:border-slate-800">
                            <div class="flex gap-1 overflow-x-auto custom-scrollbar">
                                @foreach ([1 => 'Konten Tahap', 2 => 'Panel Ketentuan', 3 => 'Tombol & Status'] as $key => $label)
                                    <button type="button" wire:click="$set('tab', {{ $key }})"
                                        class="relative shrink-0 px-3.5 py-2.5 -mb-px text-xs font-semibold border-b-2 transition {{ $tab === $key ? 'border-emerald-500 text-emerald-700 dark:border-[#93F514] dark:text-[#93F514]' : 'border-transparent text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200' }}">
                                        {{ $label }}
                                        @if ($tabErrors[$key] ?? false)
                                            <span class="ml-1 inline-block w-1.5 h-1.5 rounded-full bg-rose-500 align-middle" title="Ada kolom yang perlu diperbaiki"></span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="px-6 py-5 max-h-[62vh] overflow-y-auto custom-scrollbar text-xs">
                            @if ($errors->any())
                                <div class="mb-4 flex items-start gap-2 rounded-xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 px-3 py-2.5 text-rose-700 dark:text-rose-300">
                                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                    </svg>
                                    <span class="font-medium leading-relaxed">Ada kolom yang perlu diperbaiki. Titik merah menandai tab yang bermasalah.</span>
                                </div>
                            @endif

                            @if ($tab === 1)
                            {{-- Identitas Tahap --}}
                            <div class="space-y-3">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500">Identitas Tahap</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Judul Singkat (Stepper)*</label>
                                        <input type="text" wire:model="short_title" maxlength="100" placeholder="Misal: Registrasi"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        @error('short_title')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Keterangan Singkat</label>
                                        <input type="text" wire:model="short_sub" maxlength="150" placeholder="Misal: Buat Akun Pelamar"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        @error('short_sub')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Label Tahap (Eyebrow)</label>
                                    <input type="text" wire:model="eyebrow" maxlength="180" placeholder="Misal: Registrasi Akun"
                                        class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">
                                        Tampil sebagai "Tahap 01 — {{ $eyebrow ?: ($short_title ?: 'Judul singkat') }}". Nomor dibuat otomatis; kosongkan bila ingin memakai judul singkat.
                                    </p>
                                    @error('eyebrow')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Judul Utama*</label>
                                    <input type="text" wire:model="title" maxlength="200" placeholder="Misal: Buat Akun Pelamar dengan Cepat & Aman"
                                        class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    @error('title')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Deskripsi</label>
                                    <textarea wire:model="description" rows="3" placeholder="Penjelasan singkat tahap ini..."
                                        class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none leading-relaxed"></textarea>
                                    @error('description')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            {{-- Poin Info --}}
                            <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-2">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">
                                    Poin Info (maks. 3 kolom)
                                    <span class="text-gray-400 font-normal ml-1">(1 baris = 1 poin, format: Judul | Deskripsi)</span>
                                </label>
                                <textarea wire:model="highlightsText" rows="4"
                                    placeholder="Autentikasi Cepat | Google OAuth atau form NIK terverifikasi.&#10;Akses Fleksibel | Responsif 24/7 di smartphone & desktop."
                                    class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none leading-relaxed"></textarea>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400">
                                    Jika hanya diisi judul tanpa tanda "|", deskripsi akan kosong.
                                </p>
                                @error('highlightsText')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                            </div>
                            @endif

                            @if ($tab === 2)
                            {{-- Panel Ketentuan --}}
                            <div class="space-y-3">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500">Panel Ketentuan (tampil di sisi kanan kartu)</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Judul Panel</label>
                                        <input type="text" wire:model="panel_title" maxlength="180" placeholder="Kosongkan = Ketentuan Tahap 01"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        @error('panel_title')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Ikon Panel</label>
                                        <select wire:model="icon"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                            <option value="shield">Perisai (keamanan)</option>
                                            <option value="document">Dokumen</option>
                                            <option value="briefcase">Tas kerja</option>
                                            <option value="clipboard">Papan ujian</option>
                                            <option value="video">Video / wawancara</option>
                                            <option value="award">Penghargaan</option>
                                        </select>
                                        @error('icon')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Judul Tips</label>
                                    <input type="text" wire:model="tips_title" maxlength="180" placeholder="Misal: Tips Registrasi Akun"
                                        class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    @error('tips_title')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Isi Tips</label>
                                    <textarea wire:model="tips_body" rows="3" placeholder="Saran atau ketentuan untuk pelamar..."
                                        class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none leading-relaxed"></textarea>
                                    @error('tips_body')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Catatan Kaki</label>
                                    <input type="text" wire:model="footnote" placeholder="Misal: Data terlindungi dengan enkripsi keamanan portal resmi MIKA"
                                        class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    @error('footnote')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            @endif

                            @if ($tab === 3)
                            {{-- Tombol Aksi --}}
                            <div class="space-y-3">
                                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500">Tombol Aksi</h4>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400 -mt-1">
                                    Kosongkan label untuk memakai teks default. Pengaturan pengunjung/admin hanya perlu diisi bila tombolnya berbeda.
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Aksi Tombol*</label>
                                        <select wire:model.live="cta_action"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                            <option value="register">Otomatis: Daftar / Dashboard / Profil</option>
                                            <option value="login">Masuk (Login)</option>
                                            <option value="profile">Halaman Profil Pelamar</option>
                                            <option value="profile_history">Riwayat Lamaran Pelamar</option>
                                            <option value="jobs">Daftar Lowongan</option>
                                            <option value="custom">URL Kustom</option>
                                        </select>
                                        @error('cta_action')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Aksi untuk Pengunjung (belum login)</label>
                                        <select wire:model="cta_action_guest"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                            <option value="">Sama seperti aksi utama</option>
                                            <option value="register">Daftar Akun</option>
                                            <option value="login">Masuk (Login)</option>
                                            <option value="profile">Halaman Profil (redirect login)</option>
                                            <option value="profile_history">Riwayat Lamaran (redirect login)</option>
                                            <option value="jobs">Daftar Lowongan</option>
                                            <option value="custom">URL Kustom</option>
                                        </select>
                                        @error('cta_action_guest')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                </div>

                                @if ($cta_action === 'custom')
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">URL Tujuan*</label>
                                        <input type="text" wire:model="cta_url" placeholder="https://... atau /halaman"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        @error('cta_url')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Label Tombol</label>
                                        <input type="text" wire:model="cta_label" maxlength="100" placeholder="Kosongkan = Selengkapnya"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        @error('cta_label')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Label untuk Pengunjung</label>
                                        <input type="text" wire:model="cta_label_guest" maxlength="100" placeholder="Kosongkan = pakai label utama"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        @error('cta_label_guest')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Label untuk Admin</label>
                                        <input type="text" wire:model="cta_label_admin" maxlength="100" placeholder="Kosongkan = pakai label utama"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        @error('cta_label_admin')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Label Tombol "Lanjut"</label>
                                        <input type="text" wire:model="next_label" maxlength="100" placeholder="Kosongkan = tidak ada tombol lanjut"
                                            class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        @error('next_label')<span class="text-[11px] text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Status & Urutan --}}
                            <div class="pt-4 border-t border-gray-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-3 items-end">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Urutan</label>
                                    <input type="number" wire:model="sort_order" min="0"
                                        class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1">Bisa juga diatur lewat tombol naik/turun di daftar.</p>
                                </div>
                                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none pb-2">
                                    <input type="checkbox" wire:model="is_active"
                                        class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 dark:bg-slate-800 dark:border-slate-700">
                                    <span class="text-xs font-semibold text-gray-700 dark:text-slate-300">Tampilkan tahap ini</span>
                                </label>
                            </div>
                            @endif
                        </div>

                        <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800/60 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between gap-3">
                            <span class="hidden sm:block text-[11px] text-gray-500 dark:text-slate-400">Versi Bahasa Inggris diperbarui otomatis saat disimpan.</span>
                            <button type="button" wire:click="closeModal"
                                class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-xs font-semibold hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold transition shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 active:scale-95 flex items-center justify-center gap-1.5 disabled:opacity-60 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="save">{{ $editingId ? 'Simpan Perubahan' : 'Tambah Tahap' }}</span>
                                <span wire:loading.flex wire:target="save" class="flex items-center gap-1.5">
                                    <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Menyimpan...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ===================== MODAL HAPUS ===================== --}}
    @if ($deletingId)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" wire:click="$set('deletingId', null)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-200 dark:border-slate-800 p-6">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Hapus tahap ini?</h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 leading-relaxed">
                                <span class="font-semibold text-gray-700 dark:text-slate-300">{{ $deletingTitle }}</span>
                                akan dihapus permanen dari beranda. Nomor tahap berikutnya menyesuaikan otomatis.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-2">
                        <button type="button" wire:click="$set('deletingId', null)"
                            class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-xs font-semibold hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                            Batal
                        </button>
                        <button type="button" wire:click="delete" wire:loading.attr="disabled" wire:target="delete"
                            class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold transition shadow-md shadow-rose-600/20 active:scale-95 flex items-center justify-center gap-1.5 disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="delete">Ya, Hapus</span>
                            <span wire:loading.flex wire:target="delete" class="flex items-center gap-1.5">
                                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Menghapus...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
