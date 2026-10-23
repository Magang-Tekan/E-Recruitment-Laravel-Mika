<div class="space-y-6">

    <!-- Header Card -->
    <div class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-emerald-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Pengalaman Organisasi') }}</h2>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] border border-slate-200 dark:border-[#1D2E54]">{{ __('Opsional') }}</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">{{ __('* Tambahkan riwayat pengalaman organisasi, komunitas, atau kepanitiaan Anda (opsional).') }}</p>
            </div>
        </div>
    </div>



    <!-- Organization List Cards -->
    <div class="space-y-4">
        @forelse ($organizations as $item)
            <div
                class="bg-white dark:bg-[#0D1527] p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] hover:border-slate-300 dark:hover:border-slate-600 transition duration-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="px-3 py-1 bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-lg border border-slate-200/80 dark:border-[#1D2E54]">
                            {{ $item->position }}
                        </span>
                        @if ($item->is_active)
                            <span
                                class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-[#93F514] font-medium text-xs rounded-lg border border-emerald-200 dark:border-emerald-800/60">
                                {{ __('Masih Aktif') }}
                            </span>
                        @endif
                        <span class="text-xs text-slate-400 dark:text-[#93A5C9] font-medium ml-auto md:ml-0">
                            {{ __($item->start_month) }} {{ $item->start_year }} -
                            {{ $item->is_active ? __('Sekarang') : ($item->end_year ? __($item->end_month) . ' ' . $item->end_year : '-') }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $item->name }}
                    </h3>

                    @if ($item->description)
                        <p
                            class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-3 leading-relaxed whitespace-pre-line">
                            {{ $item->description }}
                        </p>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center gap-2 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-[#1D2E54] shrink-0">
                    <button wire:click="edit({{ $item->id }})"
                        class="px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] rounded-xl transition flex items-center gap-1.5 border border-slate-200/80 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>{{ __('Edit') }}</span>
                    </button>
                    <button wire:click="confirmDelete({{ $item->id }})"
                        class="px-3 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/50 rounded-xl transition flex items-center gap-1.5 border border-red-200 dark:border-red-800">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>{{ __('Hapus') }}</span>
                    </button>
                </div>
            </div>
        @empty
            <!-- Empty State matching Admin Panel Style -->
            <div class="col-span-full bg-white dark:bg-[#0D1527] p-8 md:p-10 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] text-center flex flex-col items-center justify-center space-y-4">
                <svg class="w-10 h-10 text-slate-300 dark:text-[#1D2E54]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-sm font-medium text-slate-500 dark:text-[#93A5C9]">{{ __('Belum ada data untuk ditampilkan') }}</p>
                <button wire:click="openModal"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-sm rounded-xl transition shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 active:scale-95">
                    <svg class="w-5 h-5 text-white dark:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('Tambah Organisasi') }}</span>
                </button>
            </div>
        @endforelse
    </div>

    @if ($organizations->isNotEmpty())
        <div class="pt-1 flex justify-start">
            <button wire:click="openModal"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>{{ __('Tambah Organisasi') }}</span>
            </button>
        </div>
    @endif

    <!-- Modal Form (Tambah / Edit Organisasi) -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="closeModal">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200 dark:border-[#1D2E54]">

                    <!-- Header -->
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-slate-200/80 dark:border-[#1D2E54]">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>

                            <span>{{ $isEdit ? __('Edit Riwayat Organisasi') : __('Tambah Riwayat Organisasi') }}</span>
                        </h3>
                        <button wire:click="closeModal"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body Form -->
                    <form wire:submit.prevent="save" class="p-6 space-y-5">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <!-- Nama Organisasi -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                                    {{ __('Nama Organisasi / Komunitas') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="name"
                                    placeholder="{{ __('Contoh: BEM Universitas / Karang Taruna') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                @error('name')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Posisi / Jabatan -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                                    {{ __('Jabatan / Peran') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="position"
                                    placeholder="{{ __('Contoh: Ketua Dept. Humas / Anggota') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                @error('position')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Periode Mulai -->
                            <div class="space-y-2">
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider">
                                    {{ __('Periode Mulai') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <select wire:model="start_month"
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                        <option value="">{{ __('-- Bulan --') }}</option>
                                        @foreach ($months as $m)
                                            <option value="{{ $m }}">{{ __($m) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" wire:model="start_year" placeholder="{{ __('Tahun (2022)') }}"
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                </div>
                                @error('start_month')
                                    <span class="text-xs text-red-500 block">{{ $message }}</span>
                                @enderror
                                @error('start_year')
                                    <span class="text-xs text-red-500 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Periode Selesai -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label
                                        class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider">
                                        {{ __('Periode Selesai') }}
                                    </label>
                                    <label
                                        class="flex items-center gap-1.5 cursor-pointer text-xs font-medium text-blue-600 dark:text-[#93F514] select-none">
                                        <input type="checkbox" wire:model.live="is_active"
                                            class="w-3.5 h-3.5 rounded border-slate-300 dark:border-[#1D2E54] text-blue-600 dark:text-[#93F514] focus:ring-blue-500 dark:focus:ring-[#93F514] dark:bg-[#14203A]">
                                        <span>{{ __('Masih Aktif') }}</span>
                                    </label>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <select wire:model="end_month" @if ($is_active) disabled @endif
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition {{ $is_active ? 'bg-slate-100 dark:bg-[#070B14] text-slate-400 dark:text-slate-500 cursor-not-allowed select-none' : 'bg-slate-50 dark:bg-[#14203A]' }}">
                                        <option value="">{{ __('-- Bulan --') }}</option>
                                        @foreach ($months as $m)
                                            <option value="{{ $m }}">{{ __($m) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" wire:model="end_year" placeholder="{{ __('Tahun (2024)') }}"
                                        @if ($is_active) disabled @endif
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition {{ $is_active ? 'bg-slate-100 dark:bg-[#070B14] text-slate-400 dark:text-slate-500 cursor-not-allowed select-none' : 'bg-slate-50 dark:bg-[#14203A]' }}">
                                </div>
                                @error('end_year')
                                    <span class="text-xs text-red-500 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Deskripsi & Kegiatan -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                                    {{ __('Deskripsi & Tanggung Jawab') }}
                                </label>
                                <textarea wire:model="description" rows="3"
                                    placeholder="{{ __('Tuliskan program kerja utama, peran penting, atau pencapaian selama berorganisasi...') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition"></textarea>
                                @error('description')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Footer Actions -->
                        <div
                            class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                            <button type="button" wire:click="closeModal"
                                class="px-5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                                {{ __('Batal') }}
                            </button>
                            <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-70 disabled:cursor-not-allowed text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold rounded-xl transition active:scale-95 cursor-pointer">
                                <svg wire:loading.remove wire:target="save" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEdit ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' }}" />
                                </svg>
                                <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin shrink-0 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove
                                    wire:target="save">{{ $isEdit ? __('Simpan Perubahan') : __('Tambah Organisasi') }}</span>
                                <span wire:loading wire:target="save">{{ __('Menyimpan...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="cancelDelete"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200 dark:border-[#1D2E54] p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-950/60 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('Konfirmasi Hapus Data') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-0.5">{{ __('Apakah Anda yakin ingin menghapus riwayat organisasi ini? Tindakan ini tidak dapat dibatalkan.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                        <button type="button" wire:click="cancelDelete" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="button" wire:click="delete" wire:loading.attr="disabled" class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-500/20 transition flex items-center gap-1.5">
                            <span wire:loading.remove wire:target="delete">{{ __('Ya, Hapus') }}</span>
                            <span wire:loading wire:target="delete">{{ __('Menghapus...') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
