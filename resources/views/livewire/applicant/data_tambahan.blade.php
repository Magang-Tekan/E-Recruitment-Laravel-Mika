<div class="space-y-8">

    <!-- Main Header Card -->
    <div
        class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-emerald-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Data Tambahan') }}</h2>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] border border-slate-200 dark:border-[#1D2E54]">{{ __('Opsional') }}</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">{{ __('* Kelola keahlian, sertifikasi profesional, pelatihan, dan kemampuan bahasa Anda.') }}</p>
            </div>
        </div>
    </div>



    <!-- ========================================== -->
    <!-- SECTION 2: SERTIFIKASI PROFESIONAL -->
    <!-- ========================================== -->
    <div class="space-y-4 pt-2">
        <div class="border-b border-slate-200 dark:border-[#1D2E54] pb-3 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Sertifikasi Profesional') }}</h3>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9]">{{ __('Lisensi, lisensi keahlian, atau sertifikat kompetensi resmi') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse ($certifications as $cert)
                <div class="bg-white dark:bg-[#0D1527] p-4 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] hover:border-slate-300 dark:hover:border-slate-600 shadow-xs transition flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                            {{ $cert->name }}
                        </h4>
                        @if ($cert->certificate_path)
                            <div class="mt-1">
                                <a href="{{ asset('storage/' . ltrim($cert->certificate_path, '/')) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-blue-600 dark:text-[#93F514] bg-blue-50 dark:bg-[#14203A] hover:bg-blue-100 dark:hover:bg-[#1D2E54] rounded-lg border border-blue-200/80 dark:border-[#1D2E54] transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    <span>{{ __('Lihat Sertifikat') }}</span>
                                </a>
                            </div>
                        @else
                            <div class="mt-1">
                                <span class="inline-flex items-center text-[10px] text-slate-400 dark:text-slate-500 italic bg-slate-100 dark:bg-[#14203A] px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-[#1D2E54]">
                                    {{ __('Tanpa Berkas') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <button wire:click="edit('certification', {{ $cert->id }})" title="{{ __('Edit Sertifikasi') }}"
                            class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-[#93F514] rounded-lg hover:bg-slate-100 dark:hover:bg-[#14203A] transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="confirmDelete('certification', {{ $cert->id }})" title="{{ __('Hapus Sertifikasi') }}"
                            class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/50 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-[#0D1527] p-7 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] text-center flex flex-col items-center justify-center space-y-3">
                    <svg class="w-9 h-9 text-slate-300 dark:text-[#1D2E54]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.816c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6A2.25 2.25 0 0 0 4.727 20.25h14.546a2.25 2.25 0 0 0 2.224-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                    </svg>
                    <p class="text-xs font-medium text-slate-500 dark:text-[#93A5C9]">{{ __('Belum ada sertifikasi ditambahkan') }}</p>
                    <button wire:click="openModal('certification')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ __('Tambah Sertifikasi') }}</span>
                    </button>
                </div>
            @endforelse
        </div>

        @if ($certifications->isNotEmpty())
            <div class="pt-1 flex justify-start">
                <button wire:click="openModal('certification')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Tambah Sertifikasi') }}</span>
                </button>
            </div>
        @endif
    </div>


    <!-- ========================================== -->
    <!-- SECTION 3: PELATIHAN & KURSUS -->
    <!-- ========================================== -->
    <div class="space-y-4 pt-2">
        <div class="border-b border-slate-200 dark:border-[#1D2E54] pb-3 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Pelatihan & Kursus') }}</h3>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9]">{{ __('Workshop, bootcamp, atau pelatihan yang pernah diikuti') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse ($trainings as $training)
                <div class="bg-white dark:bg-[#0D1527] p-4 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] hover:border-slate-300 dark:hover:border-slate-600 shadow-xs transition flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                            {{ $training->name }}
                        </h4>
                        @if ($training->certificate_path)
                            <div class="mt-1">
                                <a href="{{ asset('storage/' . ltrim($training->certificate_path, '/')) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-blue-600 dark:text-[#93F514] bg-blue-50 dark:bg-[#14203A] hover:bg-blue-100 dark:hover:bg-[#1D2E54] rounded-lg border border-blue-200/80 dark:border-[#1D2E54] transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    <span>{{ __('Lihat Sertifikat') }}</span>
                                </a>
                            </div>
                        @else
                            <div class="mt-1">
                                <span class="inline-flex items-center text-[10px] text-slate-400 dark:text-slate-500 italic bg-slate-100 dark:bg-[#14203A] px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-[#1D2E54]">
                                    {{ __('Tanpa Berkas') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <button wire:click="edit('training', {{ $training->id }})" title="{{ __('Edit Pelatihan') }}"
                            class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-[#93F514] rounded-lg hover:bg-slate-100 dark:hover:bg-[#14203A] transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="confirmDelete('training', {{ $training->id }})" title="{{ __('Hapus Pelatihan') }}"
                            class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/50 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-[#0D1527] p-7 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] text-center flex flex-col items-center justify-center space-y-3">
                    <svg class="w-9 h-9 text-slate-300 dark:text-[#1D2E54]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.816c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6A2.25 2.25 0 0 0 4.727 20.25h14.546a2.25 2.25 0 0 0 2.224-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                    </svg>
                    <p class="text-xs font-medium text-slate-500 dark:text-[#93A5C9]">{{ __('Belum ada pelatihan ditambahkan') }}</p>
                    <button wire:click="openModal('training')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ __('Tambah Pelatihan') }}</span>
                    </button>
                </div>
            @endforelse
        </div>

        @if ($trainings->isNotEmpty())
            <div class="pt-1 flex justify-start">
                <button wire:click="openModal('training')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Tambah Pelatihan') }}</span>
                </button>
            </div>
        @endif
    </div>


    <!-- ========================================== -->
    <!-- SECTION 4: KEMAMPUAN BAHASA -->
    <!-- ========================================== -->
    <div class="space-y-4 pt-2">
        <div class="border-b border-slate-200 dark:border-[#1D2E54] pb-3 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Kemampuan Berbahasa') }}</h3>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9]">{{ __('Bahasa asing yang dikuasai beserta penilaian lisan, tulisan, dan percakapan') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse ($languages as $lang)
                <div class="bg-white dark:bg-[#0D1527] p-4 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] hover:border-slate-300 dark:hover:border-slate-600 shadow-xs transition flex flex-col justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                {{ $lang->name }}
                            </h4>
                            <div class="flex items-center gap-1 shrink-0">
                                <button wire:click="edit('language', {{ $lang->id }})" title="{{ __('Edit Bahasa') }}"
                                    class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-[#93F514] rounded-lg hover:bg-slate-100 dark:hover:bg-[#14203A] transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete('language', {{ $lang->id }})" title="{{ __('Hapus Bahasa') }}"
                                    class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/50 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Level Penilaian Lisan, Tulisan, Percakapan -->
                        <div class="mt-2.5 grid grid-cols-3 gap-1 text-[11px]">
                            <div class="px-2 py-1 rounded-lg bg-slate-50 dark:bg-[#14203A] border border-slate-200/70 dark:border-[#1D2E54] text-center">
                                <span class="block text-[10px] text-slate-400 uppercase font-semibold">{{ __('Lisan') }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $lang->oral ? ucfirst(__($lang->oral)) : '-' }}</span>
                            </div>
                            <div class="px-2 py-1 rounded-lg bg-slate-50 dark:bg-[#14203A] border border-slate-200/70 dark:border-[#1D2E54] text-center">
                                <span class="block text-[10px] text-slate-400 uppercase font-semibold">{{ __('Tulisan') }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $lang->written ? ucfirst(__($lang->written)) : '-' }}</span>
                            </div>
                            <div class="px-2 py-1 rounded-lg bg-slate-50 dark:bg-[#14203A] border border-slate-200/70 dark:border-[#1D2E54] text-center">
                                <span class="block text-[10px] text-slate-400 uppercase font-semibold">{{ __('Percakapan') }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $lang->conversation ? ucfirst(__($lang->conversation)) : '-' }}</span>
                            </div>
                        </div>

                        @if ($lang->certificate_path)
                            <div class="mt-2.5">
                                <a href="{{ asset('storage/' . ltrim($lang->certificate_path, '/')) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-blue-600 dark:text-[#93F514] bg-blue-50 dark:bg-[#14203A] hover:bg-blue-100 dark:hover:bg-[#1D2E54] rounded-lg border border-blue-200/80 dark:border-[#1D2E54] transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    <span>{{ __('Lihat Sertifikat') }}</span>
                                </a>
                            </div>
                        @else
                            <div class="mt-2.5">
                                <span class="inline-flex items-center text-[10px] text-slate-400 dark:text-slate-500 italic bg-slate-100 dark:bg-[#14203A] px-2 py-0.5 rounded-md border border-slate-200/60 dark:border-[#1D2E54]">
                                    {{ __('Tanpa Berkas') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-[#0D1527] p-7 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] text-center flex flex-col items-center justify-center space-y-3">
                    <svg class="w-9 h-9 text-slate-300 dark:text-[#1D2E54]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.816c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6A2.25 2.25 0 0 0 4.727 20.25h14.546a2.25 2.25 0 0 0 2.224-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                    </svg>
                    <p class="text-xs font-medium text-slate-500 dark:text-[#93A5C9]">{{ __('Belum ada kemampuan bahasa ditambahkan') }}</p>
                    <button wire:click="openModal('language')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ __('Tambah Bahasa') }}</span>
                    </button>
                </div>
            @endforelse
        </div>

        @if ($languages->isNotEmpty())
            <div class="pt-1 flex justify-start">
                <button wire:click="openModal('language')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Tambah Bahasa') }}</span>
                </button>
            </div>
        @endif
    </div>


    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-[#1D2E54]">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200/80 dark:border-[#1D2E54]">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>{{ $isEdit ? __('Edit :type', ['type' => __($this->getTypeLabel($modalType))]) : __('Tambah :type', ['type' => __($this->getTypeLabel($modalType))]) }}</span>
                        </h3>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                                {{ __('Nama :type', ['type' => __($this->getTypeLabel($modalType))]) }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="name"
                                placeholder="{{ $modalType === 'certification' ? __('Contoh: AWS Certified Solutions Architect / BNSP') : ($modalType === 'training' ? __('Contoh: Digital Marketing Bootcamp 2023') : __('Contoh: Bahasa Inggris / Bahasa Jepang')) }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                            @error('name')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Input Khusus Kemampuan Berbahasa --}}
                        @if ($modalType === 'language')
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider">
                                    {{ __('Tingkat Penguasaan') }}
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54]">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                            {{ __('Lisan') }}
                                        </label>
                                        <select wire:model="oral" class="w-full px-3 py-2 rounded-lg border border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                                            <option value="baik">{{ __('Baik') }}</option>
                                            <option value="cukup">{{ __('Cukup') }}</option>
                                            <option value="kurang">{{ __('Kurang') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                            {{ __('Tulisan') }}
                                        </label>
                                        <select wire:model="written" class="w-full px-3 py-2 rounded-lg border border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                                            <option value="baik">{{ __('Baik') }}</option>
                                            <option value="cukup">{{ __('Cukup') }}</option>
                                            <option value="kurang">{{ __('Kurang') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                            {{ __('Percakapan') }}
                                        </label>
                                        <select wire:model="conversation" class="w-full px-3 py-2 rounded-lg border border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                                            <option value="baik">{{ __('Baik') }}</option>
                                            <option value="cukup">{{ __('Cukup') }}</option>
                                            <option value="kurang">{{ __('Kurang') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                                {{ __('Upload Sertifikat / Bukti (Opsional, PDF / Gambar max 2MB)') }}
                            </label>
                            <input type="file" wire:model="certificate" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 dark:file:bg-[#14203A] dark:file:text-slate-300 hover:file:bg-slate-200 dark:hover:file:bg-[#1A2A4C] transition">
                            
                            <div wire:loading wire:target="certificate" class="mt-1.5 flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-[#93F514]">
                                <svg class="animate-spin h-3.5 w-3.5 text-blue-600 dark:text-[#93F514]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ __('Mengunggah file...') }}</span>
                            </div>

                            @error('certificate')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                            {{-- Live Preview Saat File Dipilih --}}
                            @if ($certificate)
                                @php
                                    $isImg = false;
                                    $fileExt = '';
                                    try {
                                        $fileExt = strtolower($certificate->getClientOriginalExtension());
                                        $isImg = in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    } catch (\Throwable $e) {}
                                @endphp
                                <div class="mt-2.5 p-3 rounded-xl bg-emerald-50/70 dark:bg-[#14203A] border border-emerald-200 dark:border-emerald-800/60">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span class="text-xs font-bold text-emerald-800 dark:text-[#93F514] truncate">
                                                {{ $certificate->getClientOriginalName() }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-emerald-600 dark:text-[#93F514] font-mono shrink-0 ml-2">
                                            {{ number_format($certificate->getSize() / 1024, 1) }} KB
                                        </span>
                                    </div>
                                    @if ($isImg)
                                        <div class="rounded-lg overflow-hidden border border-emerald-200 dark:border-emerald-800/60 max-h-48 flex items-center justify-center bg-black/5 dark:bg-black/20 p-1">
                                            <img src="{{ $certificate->temporaryUrl() }}" alt="Preview" class="max-h-44 w-auto object-contain rounded">
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2.5 p-2 bg-white dark:bg-[#0D1527] rounded-lg border border-emerald-200 dark:border-emerald-800/60">
                                            <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">Dokumen PDF Terpilih</p>
                                                <p class="text-[10px] text-slate-500">File siap disimpan bersama data</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @elseif ($existing_certificate)
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-200/80 dark:border-[#1D2E54] mt-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="text-xs text-slate-700 dark:text-slate-300 font-medium truncate">{{ basename($existing_certificate) }}</span>
                                    </div>
                                    <a href="{{ asset('storage/' . ltrim($existing_certificate, '/')) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="px-2.5 py-1 text-xs font-semibold text-blue-600 dark:text-[#93F514] hover:bg-blue-100 dark:hover:bg-[#1D2E54] rounded-lg transition shrink-0 flex items-center gap-1 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        <span>{{ __('Buka di Tab Baru') }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                            <button type="button" wire:click="closeModal"
                                class="px-5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                                {{ __('Batal') }}
                            </button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="save, certificate"
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold rounded-xl transition flex items-center gap-2">
                                <span wire:loading.remove wire:target="save, certificate">{{ $isEdit ? __('Simpan Perubahan') : __('Tambah :type', ['type' => __($this->getTypeLabel($modalType))]) }}</span>
                                <span wire:loading wire:target="save">{{ __('Menyimpan...') }}</span>
                                <span wire:loading wire:target="certificate">{{ __('Mengunggah File...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS -->
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
                            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-0.5">
                                {{ __('Apakah Anda yakin ingin menghapus data :type ini? Tindakan ini tidak dapat dibatalkan.', ['type' => strtolower(__($this->getTypeLabel($deleteType)))]) }}
                            </p>
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
