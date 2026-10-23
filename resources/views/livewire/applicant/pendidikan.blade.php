<div class="space-y-6">

    <!-- Header Card (Clean Enterprise Blueprint) -->
    <div class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-emerald-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Riwayat Pendidikan & Kualifikasi') }}</h2>
            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">{{ __('* Kelola riwayat pendidikan formal, kemampuan bahasa, keahlian teknis, sertifikasi profesional, dan pelatihan Anda.') }}</p>
        </div>
    </div>



    <!-- Education List Cards -->
    <div class="space-y-4">
        @forelse ($educations as $item)
            <div
                class="bg-white dark:bg-[#0D1527] p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] hover:border-slate-300 dark:hover:border-[#2D457C] transition duration-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="px-3 py-1 bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-slate-200 font-semibold text-xs rounded-lg border border-slate-200/80 dark:border-[#1D2E54]">
                            {{ __($item->degreeRelation ? $item->degreeRelation->name : ($item->degree ?? 'Pendidikan')) }}
                        </span>
                        @if ($item->gpa)
                            @php
                                $degreeName = strtoupper(
                                    $item->degreeRelation ? $item->degreeRelation->name : $item->degree ?? '',
                                );
                                $isSchoolItem =
                                    str_contains($degreeName, 'SMA') ||
                                    str_contains($degreeName, 'SMK') ||
                                    str_contains($degreeName, 'SLTA') ||
                                    str_contains($degreeName, 'SMP') ||
                                    str_contains($degreeName, 'SD') ||
                                    $item->gpa > 4.0;
                            @endphp
                            <span
                                class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-medium text-xs rounded-lg border border-emerald-200 dark:border-emerald-800">
                                {{ $isSchoolItem ? __('Nilai:') : __('IPK:') }} {{ number_format($item->gpa, 2) }}
                            </span>
                        @endif
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-medium ml-auto md:ml-0">
                            {{ $item->start_year }} - {{ $item->end_year ? $item->end_year : __('Sekarang') }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $item->school_name }}
                    </h3>

                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                        {{ $item->majorRelation ? $item->majorRelation->name : $item->major ?? '' }}
                        @if ($item->study_program && $item->study_program !== $item->major)
                            <span class="text-slate-400 dark:text-slate-500">({{ $item->study_program }})</span>
                        @endif
                    </p>

                    @if ($item->description)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                            {{ $item->description }}
                        </p>
                    @endif

                    @if ($item->certificate_path)
                        <div class="pt-1.5 flex items-center gap-2">
                            <a href="{{ asset('storage/' . ltrim($item->certificate_path, '/')) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition shadow-sm">
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>{{ __('Lihat Ijazah') }}</span>
                            </a>
                        </div>
                    @elseif (!$item->end_year)
                        <div class="pt-1.5 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                {{ __('Sedang Menempuh Studi (Belum Lulus)') }}
                            </span>
                        </div>
                    @else
                        <div class="pt-1.5 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[11px] font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50">
                                <svg class="w-3 h-3 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                {{ __('Ijazah Belum Diunggah') }}
                            </span>
                        </div>
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
                <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('Belum ada data untuk ditampilkan') }}</p>
                <button wire:click="openModal"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-sm rounded-xl transition shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 active:scale-95">
                    <svg class="w-5 h-5 text-white dark:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('Tambah Pendidikan') }}</span>
                </button>
            </div>
        @endforelse
    </div>

    @if ($educations->isNotEmpty())
        <div class="pt-1 flex justify-start">
            <button wire:click="openModal"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>{{ __('Tambah Pendidikan') }}</span>
            </button>
        </div>
    @endif

    <!-- Modal Form (Tambah / Edit Pendidikan) -->
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
                            <svg class="w-5 h-5 text-emerald-600 dark:text-[#93F514]" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                            </svg>

                            <span>{{ $isEdit ? __('Edit Riwayat Pendidikan') : __('Tambah Riwayat Pendidikan') }}</span>
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

                            <!-- Tingkat Pendidikan (Degree) -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    {{ __('Tingkat / Jenjang Pendidikan') }}
                                </label>
                                <select wire:model.live="degree_id"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    <option value="">{{ __('-- Pilih Jenjang --') }}</option>
                                    @foreach ($degrees as $deg)
                                        <option value="{{ $deg->id }}">{{ __($deg->name) }}</option>
                                    @endforeach
                                </select>
                                @error('degree_id')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Nama Sekolah / Universitas -->
                            <div x-data="{
                                open: false,
                                query: @entangle('school_name').live,
                                dictionary: {{ json_encode($uniDict) }},
                                selectedIndex: 0,
                                get matches() {
                                    if (!this.query || this.query.trim().length < 2) return [];
                                    let q = this.query.trim().toLowerCase();
                                    let results = [];

                                    for (const [abbr, fullName] of Object.entries(this.dictionary)) {
                                        let a = abbr.toLowerCase();
                                        let f = fullName.toLowerCase();
                                        if (q === a) {
                                            if (!results.includes(fullName)) results.unshift(fullName);
                                        } else if (a.startsWith(q)) {
                                            if (!results.includes(fullName)) results.push(fullName);
                                        } else if (q.includes(a) && q !== f) {
                                            if (!results.includes(fullName)) results.push(fullName);
                                        } else if (f.includes(q) && q !== f) {
                                            if (!results.includes(fullName)) results.push(fullName);
                                        }
                                    }

                                    return results.filter(name => name.toLowerCase() !== q).slice(0, 5);
                                },
                                select(name) {
                                    this.query = name;
                                    $wire.applySchoolSuggestion(name);
                                    this.open = false;
                                    this.selectedIndex = 0;
                                },
                                onInput() {
                                    this.open = this.matches.length > 0;
                                    this.selectedIndex = 0;
                                },
                                handleKeydown(e) {
                                    if (this.open && this.matches.length > 0) {
                                        if (e.key === 'ArrowDown') {
                                            e.preventDefault();
                                            this.selectedIndex = (this.selectedIndex + 1) % this.matches.length;
                                            return;
                                        }
                                        if (e.key === 'ArrowUp') {
                                            e.preventDefault();
                                            this.selectedIndex = (this.selectedIndex - 1 + this.matches.length) % this.matches.length;
                                            return;
                                        }
                                        if (e.key === 'Enter') {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            if (this.matches[this.selectedIndex]) {
                                                this.select(this.matches[this.selectedIndex]);
                                            }
                                            return;
                                        }
                                        if (e.key === 'Tab') {
                                            if (this.matches[this.selectedIndex]) {
                                                this.select(this.matches[this.selectedIndex]);
                                            }
                                            return;
                                        }
                                        if (e.key === 'Escape') {
                                            this.open = false;
                                            return;
                                        }
                                    }
                                }
                            }" class="relative" @click.outside="open = false">
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    {{ __('Nama Institusi / Sekolah') }} <span class="text-red-500">*</span>
                                </label>

                                <div class="relative">
                                    <input type="text"
                                        x-model="query"
                                        @input="onInput()"
                                        @focus="if (matches.length > 0) open = true"
                                        @keydown="handleKeydown($event)"
                                        placeholder="{{ __('Nama Sekolah / Universitas') }}"
                                        class="w-full px-4 py-2.5 rounded-xl border @error('school_name') border-red-500 ring-2 ring-red-500/20 text-red-900 dark:text-red-200 @else border-slate-200/80 dark:border-[#1D2E54] text-slate-900 dark:text-white @enderror bg-slate-50 dark:bg-[#14203A] text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                </div>

                                <!-- Dropdown Rekomendasi Nama Resmi -->
                                <div x-show="open && matches.length > 0"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-1"
                                    class="absolute z-50 left-0 right-0 mt-1.5 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-2xl overflow-hidden"
                                    style="display: none;">

                                    <div class="p-1 space-y-0.5 max-h-56 overflow-y-auto">
                                        <template x-for="(item, idx) in matches" :key="item">
                                            <button type="button"
                                                @click="select(item)"
                                                @mouseenter="selectedIndex = idx"
                                                :class="selectedIndex === idx ? 'bg-emerald-500/10 dark:bg-[#93F514]/15 text-emerald-800 dark:text-[#93F514] font-semibold' : 'text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A]'"
                                                class="w-full text-left px-3 py-2.5 rounded-lg text-xs md:text-sm flex items-center gap-2.5 transition cursor-pointer">
                                                <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="truncate" x-text="item"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                @error('school_name')
                                    <span class="text-xs text-red-500 mt-1.5 flex items-start gap-1 font-medium">
                                        <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </span>
                                @enderror
                            </div>

                            <!-- Jurusan (Major) -->
                            <div x-data="{
                                open: false,
                                search: '',
                                majors: {{ $majors->toJson() }},
                                get filteredMajors() {
                                    if (!this.search) return this.majors;
                                    return this.majors.filter(m => m.name.toLowerCase().includes(this.search.toLowerCase()));
                                },
                                selectMajor(id, name) {
                                    $wire.set('major_id', id);
                                    $wire.set('is_custom_major', false);
                                    $wire.set('custom_major', '');
                                    this.search = name;
                                    this.open = false;
                                },
                                selectOther() {
                                    $wire.set('major_id', 'other');
                                    $wire.set('is_custom_major', true);
                                    this.search = '';
                                    this.open = false;
                                },
                                init() {
                                    let currentId = $wire.get('major_id');
                                    if (currentId === 'other' || $wire.get('is_custom_major')) {
                                        this.search = '';
                                    } else if (currentId) {
                                        let found = this.majors.find(m => m.id == currentId);
                                        if (found) this.search = found.name;
                                    }
                                }
                            }" class="relative">
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    {{ __('Jurusan') }}
                                </label>
                                @if ($this->isSchoolDegree())
                                    <input type="text" value="{{ __('Tidak Berlaku (SMA/SMK)') }}" disabled
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-100 dark:bg-[#14203A]/50 text-slate-400 dark:text-slate-500 text-sm cursor-not-allowed font-medium select-none">
                                    <p
                                        class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 font-medium flex items-start gap-1">
                                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ __('* Untuk jenjang SMA/SMK, silakan isikan jurusan/konsentrasi keahlian Anda pada kolom') }} <strong>{{ __('Program Studi / Konsentrasi') }}</strong> {{ __('di sebelah.') }}</span>
                                    </p>
                                @else
                                    @if ($is_custom_major)
                                        <div class="space-y-2">
                                            <div class="relative">
                                                <input type="text" wire:model="custom_major"
                                                    placeholder="{{ __('Ketik nama jurusan Anda di sini...') }}"
                                                    autofocus
                                                    class="w-full px-4 py-2.5 pr-10 rounded-xl border border-emerald-400/80 dark:border-[#93F514] bg-emerald-50/20 dark:bg-[#93F514]/10 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] focus:border-transparent transition placeholder:text-slate-400">
                                                <button type="button" wire:click="setCustomMajorMode(false)"
                                                    title="{{ __('Kembali ke pilihan daftar jurusan') }}"
                                                    class="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 bg-slate-200 dark:bg-[#1D2E54] px-2 py-0.5 rounded-lg transition">
                                                    {{ __('Batal') }}
                                                </button>
                                            </div>
                                            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                                                <span>{{ __('* Jurusan bebas / tidak ada di daftar') }}</span>
                                                <button type="button" wire:click="setCustomMajorMode(false)" class="text-emerald-600 dark:text-[#93F514] hover:underline font-semibold">
                                                    {{ __('Pilih dari daftar jurusan') }}
                                                </button>
                                            </div>
                                            @error('custom_major')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @else
                                        <div class="relative" @click.away="open = false">
                                            <div class="relative">
                                                <input type="text"
                                                    x-model="search"
                                                    @focus="open = true"
                                                    @input="open = true"
                                                    placeholder="{{ __('Ketik untuk cari jurusan...') }}"
                                                    class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                                
                                                <div class="absolute right-3 top-3 pointer-events-none text-slate-400">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <!-- Dropdown Menu List -->
                                            <div x-show="open"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-95"
                                                class="absolute z-30 left-0 right-0 mt-1.5 max-h-56 overflow-y-auto bg-white dark:bg-[#0D1527] border border-slate-200/90 dark:border-[#1D2E54] rounded-xl shadow-xl divide-y divide-slate-100 dark:divide-[#1D2E54]/50"
                                                style="display: none;">
                                                
                                                <!-- Button Khusus: Lainnya / Tulis Sendiri (Posisi Teratas) -->
                                                <div class="p-1 bg-emerald-50/40 dark:bg-[#14203A]/80 sticky top-0 z-10 backdrop-blur-sm border-b border-slate-100 dark:border-[#1D2E54]">
                                                    <button type="button"
                                                        @click="selectOther()"
                                                        class="w-full text-left px-3 py-2 text-xs md:text-sm rounded-lg font-bold text-emerald-600 dark:text-[#93F514] hover:bg-emerald-100/70 dark:hover:bg-[#93F514]/20 flex items-center gap-2 transition">
                                                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        <span>{{ __('Jurusan Lainnya') }}</span>
                                                    </button>
                                                </div>

                                                <!-- Daftar Jurusan -->
                                                <div class="p-1">
                                                    <template x-for="maj in filteredMajors" :key="maj.id">
                                                        <button type="button"
                                                            @click="selectMajor(maj.id, maj.name)"
                                                            class="w-full text-left px-3 py-2 text-xs md:text-sm rounded-lg text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] flex items-center justify-between transition">
                                                            <span x-text="maj.name"></span>
                                                            <span x-show="$wire.major_id == maj.id" class="text-emerald-600 dark:text-[#93F514] font-bold text-xs">✓</span>
                                                        </button>
                                                    </template>

                                                    <div x-show="filteredMajors.length === 0" class="px-3 py-2.5 text-xs text-slate-400 dark:text-slate-500 italic">
                                                        {{ __('Jurusan') }} "<span x-text="search"></span>" {{ __('tidak ditemukan di daftar. Klik opsi "Lainnya" di atas untuk mengetik sendiri.') }}
                                                    </div>
                                                </div>
                                            </div>

                                            @error('major_id')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Program Studi / Konsentrasi -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    {{ __('Program Studi / Konsentrasi') }}
                                </label>
                                <input type="text" wire:model="study_program"
                                    placeholder="{{ $this->isSchoolDegree() ? __('Contoh: IPA / IPS / Teknik Komputer & Jaringan') : __('Contoh: Teknik Informatika / Manajemen') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                @error('study_program')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Tahun Masuk -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    {{ __('Tahun Masuk') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model="start_year" placeholder="{{ __('Contoh: 2020') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                @error('start_year')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Tahun Lulus / Selesai -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label
                                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                        {{ __('Tahun Lulus') }}
                                    </label>
                                    <label
                                        class="flex items-center gap-1.5 cursor-pointer text-xs font-medium text-emerald-600 dark:text-[#93F514] select-none">
                                        <input type="checkbox" wire:model.live="is_ongoing"
                                            class="w-3.5 h-3.5 rounded border-slate-300 dark:border-[#1D2E54] text-emerald-600 focus:ring-emerald-500 dark:bg-[#14203A]">
                                        <span>{{ __('Masih Berlangsung / Belum Lulus') }}</span>
                                    </label>
                                </div>
                                <input type="number" wire:model="end_year"
                                    placeholder="{{ $is_ongoing ? __('Masih Berlangsung') : __('Contoh: 2024') }}"
                                    @if ($is_ongoing) disabled @endif
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition {{ $is_ongoing ? 'bg-slate-100 dark:bg-[#14203A]/50 text-slate-400 dark:text-slate-500 cursor-not-allowed select-none' : 'bg-slate-50 dark:bg-[#14203A]' }}">
                                @error('end_year')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- IPK / Nilai Akhir -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    {{ $this->isSchoolDegree() ? __('Nilai Rata-Rata / Nilai Ujian (Skala 0 - 100)') : __('IPK / Nilai Akhir (Skala 0.00 - 4.00)') }}
                                </label>
                                <input type="number" step="0.01" min="0"
                                    max="{{ $this->isSchoolDegree() ? '100' : '4.00' }}" wire:model="gpa"
                                    placeholder="{{ $this->isSchoolDegree() ? __('Contoh: 85.50') : __('Contoh: 3.75') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                @error('gpa')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Deskripsi / Catatan -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    {{ __('Deskripsi / Prestasi Pendidikan') }}
                                </label>
                                <textarea wire:model="description" rows="3"
                                    placeholder="{{ __('Tuliskan judul skripsi, prestasi akademik, atau kegiatan ekstrakurikuler penting...') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition"></textarea>
                                @error('description')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Upload Ijazah / SKL (Wajib jika sudah lulus) -->
                            <div class="md:col-span-2 p-4 bg-slate-50/80 dark:bg-[#14203A]/60 border border-slate-200/80 dark:border-[#1D2E54] rounded-2xl space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                        {{ __('File Ijazah / Surat Keterangan Lulus (SKL)') }}
                                        @if (!$is_ongoing)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    @if (!$is_ongoing)
                                        <span class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-md border border-amber-200 dark:border-amber-800/40">
                                            {{ __('Wajib diunggah untuk status lulus') }}
                                        </span>
                                    @endif
                                </div>

                                @if ($is_ongoing)
                                    <div class="p-3 rounded-xl bg-slate-100 dark:bg-[#0D1527] border border-slate-200/80 dark:border-[#1D2E54] flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ __('Pelamar yang masih menempuh studi / belum lulus tidak diwajibkan melampirkan ijazah.') }}</span>
                                    </div>
                                @else
                                    <input type="file" wire:model="certificate_file"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-600/10 file:text-emerald-700 dark:file:bg-[#93F514]/15 dark:file:text-[#93F514] hover:file:bg-emerald-600/20 transition cursor-pointer">
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                        {{ __('Format yang didukung: PDF, JPG, JPEG, PNG (Maksimal 2MB).') }}
                                    </p>

                                    <div wire:loading wire:target="certificate_file" class="flex items-center gap-2 text-xs font-semibold text-emerald-600 dark:text-[#93F514]">
                                        <svg class="animate-spin h-3.5 w-3.5 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>{{ __('Mengunggah file ijazah...') }}</span>
                                    </div>

                                    {{-- Live Preview Saat File Baru Dipilih --}}
                                    @if ($certificate_file)
                                        @php
                                            $isImg = false;
                                            $fileExt = '';
                                            try {
                                                $fileExt = strtolower($certificate_file->getClientOriginalExtension());
                                                $isImg = in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp']);
                                            } catch (\Throwable $e) {}
                                        @endphp
                                        <div class="p-3 rounded-xl bg-emerald-50/70 dark:bg-[#0D1527] border border-emerald-200 dark:border-emerald-800/60 space-y-2">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    <span class="text-xs font-bold text-emerald-800 dark:text-[#93F514] truncate">
                                                        {{ $certificate_file->getClientOriginalName() }}
                                                    </span>
                                                </div>
                                                <span class="text-[10px] text-emerald-600 dark:text-[#93F514] font-mono shrink-0 ml-2">
                                                    {{ number_format($certificate_file->getSize() / 1024, 1) }} KB
                                                </span>
                                            </div>
                                            @if ($isImg)
                                                <div class="rounded-lg overflow-hidden border border-emerald-200 dark:border-emerald-800/60 max-h-48 flex items-center justify-center bg-black/5 dark:bg-black/20 p-1">
                                                    <img src="{{ $certificate_file->temporaryUrl() }}" alt="Preview Ijazah" class="max-h-44 w-auto object-contain rounded">
                                                </div>
                                            @else
                                                <div class="flex items-center gap-2.5 p-2 bg-white dark:bg-[#14203A] rounded-lg border border-emerald-200 dark:border-emerald-800/60">
                                                    <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">{{ __('Dokumen PDF Terpilih') }}</p>
                                                        <p class="text-[10px] text-slate-500">{{ __('File ijazah siap disimpan bersama riwayat pendidikan.') }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @elseif ($existing_certificate_path)
                                        <div class="flex items-center justify-between p-2.5 bg-white dark:bg-[#0D1527] rounded-xl border border-slate-200/80 dark:border-[#1D2E54]">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div class="min-w-0">
                                                    <p class="text-xs text-slate-700 dark:text-slate-300 font-medium truncate">{{ basename($existing_certificate_path) }}</p>
                                                    <p class="text-[10px] text-emerald-600 dark:text-[#93F514] font-semibold">{{ __('Ijazah sudah tersimpan (Pilih file di atas jika ingin mengganti)') }}</p>
                                                </div>
                                            </div>
                                            <a href="{{ asset('storage/' . ltrim($existing_certificate_path, '/')) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="px-3 py-1.5 text-xs font-semibold text-emerald-600 dark:text-[#93F514] hover:bg-emerald-50 dark:hover:bg-[#14203A] rounded-lg transition shrink-0 flex items-center gap-1 cursor-pointer border border-emerald-200/80 dark:border-emerald-800/60">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                <span>{{ __('Lihat Berkas') }}</span>
                                            </a>
                                        </div>
                                    @endif

                                    @error('certificate_file')
                                        <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>

                        </div>

                        <!-- Footer Actions -->
                        <div
                            class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                            <button type="button" wire:click="closeModal"
                                class="px-5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#14203A] hover:bg-slate-200 dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
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
                                <span wire:loading.remove wire:target="save">{{ $isEdit ? __('Simpan Perubahan') : __('Tambah Pendidikan') }}</span>
                                <span wire:loading wire:target="save">{{ __('Menyimpan...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Pendidikan -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="cancelDelete"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200 dark:border-[#1D2E54] p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-950/60 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('Konfirmasi Hapus Data') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('Apakah Anda yakin ingin menghapus riwayat pendidikan ini? Tindakan ini tidak dapat dibatalkan.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                        <button type="button" wire:click="cancelDelete" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#14203A] hover:bg-slate-200 dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="button" wire:click="delete" wire:loading.attr="disabled" class="px-5 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-md shadow-red-500/20 transition flex items-center gap-1.5 active:scale-95">
                            <span wire:loading.remove wire:target="delete">{{ __('Ya, Hapus') }}</span>
                            <span wire:loading wire:target="delete">{{ __('Menghapus...') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- SECTION 2: KEMAMPUAN BAHASA -->
    <!-- ========================================== -->
    <div class="mt-10 space-y-4 pt-6 border-t border-slate-200/80 dark:border-[#1D2E54]">
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
                                <button wire:click="editExtra('language', {{ $lang->id }})" title="{{ __('Edit Bahasa') }}"
                                    class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-[#93F514] rounded-lg hover:bg-slate-100 dark:hover:bg-[#14203A] transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDeleteExtra('language', {{ $lang->id }})" title="{{ __('Hapus Bahasa') }}"
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
                    <button wire:click="openExtraModal('language')"
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
                <button wire:click="openExtraModal('language')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Tambah Bahasa') }}</span>
                </button>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- SECTION 3: KEMAMPUAN TEKNIS & APLIKASI KOMPUTER -->
    <!-- ========================================== -->
    <div class="mt-10 space-y-4 pt-6 border-t border-slate-200/80 dark:border-[#1D2E54]">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    {{ __('Kemampuan Teknis dan Aplikasi Komputer') }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ __('Software atau aplikasi komputer yang dikuasai beserta tingkat kemahiran dan keterangan') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse ($skills as $skill)
                <div class="bg-white dark:bg-[#0D1527] p-4 rounded-xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] hover:border-slate-300 dark:hover:border-[#2D457C] transition flex flex-col justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                {{ $skill->name }}
                            </h4>
                            <div class="flex items-center gap-1 shrink-0">
                                <button wire:click="editSkill({{ $skill->id }})" class="p-1.5 text-slate-400 hover:text-emerald-600 dark:hover:text-[#93F514] rounded-lg hover:bg-slate-100 dark:hover:bg-[#14203A] transition" title="{{ __('Edit Aplikasi') }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDeleteSkill({{ $skill->id }})" class="p-1.5 text-slate-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-slate-100 dark:hover:bg-[#14203A] transition" title="{{ __('Hapus Aplikasi') }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @if ($skill->proficiency)
                            @php
                                $badgeColor = match($skill->proficiency) {
                                    'sangat_baik' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
                                    'baik' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800',
                                    'cukup' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
                                    default => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800',
                                };
                                $labelProficiency = match($skill->proficiency) {
                                    'sangat_baik' => __('Sangat Baik'),
                                    'baik' => __('Baik'),
                                    'cukup' => __('Cukup'),
                                    'kurang' => __('Kurang'),
                                    default => ucfirst(str_replace('_', ' ', $skill->proficiency)),
                                };
                            @endphp
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold border {{ $badgeColor }}">
                                    {{ $labelProficiency }}
                                </span>
                            </div>
                        @endif

                        @if ($skill->description)
                            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-2 line-clamp-2">
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ __('Keterangan:') }}</span> {{ $skill->description }}
                            </p>
                        @endif

                        @if ($skill->certificate_path)
                            <div class="mt-2.5">
                                <a href="{{ asset('storage/' . ltrim($skill->certificate_path, '/')) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-[#14203A] hover:bg-emerald-100 dark:hover:bg-[#1D2E54] rounded-lg border border-emerald-200/80 dark:border-[#1D2E54] transition shadow-2xs cursor-pointer">
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
                <!-- Empty State matching monitor icon style -->
                <div class="col-span-full bg-white dark:bg-[#0D1527] p-8 md:p-10 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] text-center flex flex-col items-center justify-center space-y-4">
                    <svg class="w-10 h-10 text-slate-300 dark:text-[#1D2E54]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm font-medium text-slate-500 dark:text-[#93A5C9]">{{ __('Belum ada kemampuan aplikasi komputer ditambahkan') }}</p>
                    <button wire:click="openSkillModal"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-sm font-bold rounded-xl transition shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 active:scale-95">
                        <svg class="w-5 h-5 text-white dark:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ __('Tambah Aplikasi Komputer') }}</span>
                    </button>
                </div>
            @endforelse
        </div>

        @if ($skills->isNotEmpty())
            <div class="pt-1 flex justify-start">
                <button wire:click="openSkillModal"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Tambah Aplikasi Komputer') }}</span>
                </button>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- SECTION 4: SERTIFIKASI PROFESIONAL -->
    <!-- ========================================== -->
    <div class="mt-10 space-y-4 pt-6 border-t border-slate-200/80 dark:border-[#1D2E54]">
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
                        <button wire:click="editExtra('certification', {{ $cert->id }})" title="{{ __('Edit Sertifikasi') }}"
                            class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-[#93F514] rounded-lg hover:bg-slate-100 dark:hover:bg-[#14203A] transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="confirmDeleteExtra('certification', {{ $cert->id }})" title="{{ __('Hapus Sertifikasi') }}"
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
                    <button wire:click="openExtraModal('certification')"
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
                <button wire:click="openExtraModal('certification')"
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
    <!-- SECTION 5: PELATIHAN & KURSUS -->
    <!-- ========================================== -->
    <div class="mt-10 space-y-4 pt-6 border-t border-slate-200/80 dark:border-[#1D2E54]">
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
                        <button wire:click="editExtra('training', {{ $training->id }})" title="{{ __('Edit Pelatihan') }}"
                            class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-[#93F514] rounded-lg hover:bg-slate-100 dark:hover:bg-[#14203A] transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="confirmDeleteExtra('training', {{ $training->id }})" title="{{ __('Hapus Pelatihan') }}"
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
                    <button wire:click="openExtraModal('training')"
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
                <button wire:click="openExtraModal('training')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('Tambah Pelatihan') }}</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Modal Form (Tambah / Edit Skill Komputer) -->
    @if ($showSkillModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="closeSkillModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-[#1D2E54]">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200/80 dark:border-[#1D2E54]">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ $isSkillEdit ? __('Edit Aplikasi Komputer') : __('Tambah Aplikasi Komputer') }}
                        </h3>
                        <button type="button" wire:click="closeSkillModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form wire:submit.prevent="saveSkill" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                                {{ __('Aplikasi Komputer') }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model="skill_name" placeholder="{{ __('Misal: Microsoft Excel, AutoCAD, Photoshop, SAP...') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            @error('skill_name')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                                {{ __('Tingkat Kemahiran') }}
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach(['sangat_baik' => __('Sangat Baik'), 'baik' => __('Baik'), 'cukup' => __('Cukup'), 'kurang' => __('Kurang')] as $val => $lbl)
                                    <label class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border cursor-pointer text-xs font-semibold transition
                                        {{ $skill_proficiency === $val ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 dark:border-[#93F514] text-emerald-700 dark:text-[#93F514]' : 'bg-slate-50 dark:bg-[#14203A] border-slate-200/80 dark:border-[#1D2E54] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1A2A4C]' }}">
                                        <input type="radio" wire:model.live="skill_proficiency" value="{{ $val }}" class="sr-only">
                                        <span>{{ $lbl }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('skill_proficiency')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                                {{ __('Keterangan (Opsional)') }}
                            </label>
                            <textarea wire:model="skill_description" rows="2"
                                placeholder="{{ __('Contoh: Formula Excel (VLOOKUP, Pivot), AutoCAD 2D/3D, dll.') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition"></textarea>
                            @error('skill_description')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                                {{ __('File Sertifikat / Bukti Pendukung (Opsional)') }}
                            </label>
                            <input type="file" wire:model="skill_certificate" class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 dark:file:bg-[#14203A] dark:file:text-slate-200 hover:file:bg-slate-200 transition">
                            
                            <div wire:loading wire:target="skill_certificate" class="mt-1.5 flex items-center gap-2 text-xs font-semibold text-emerald-600 dark:text-[#93F514]">
                                <svg class="animate-spin h-3.5 w-3.5 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ __('Mengunggah file...') }}</span>
                            </div>

                            {{-- Live Preview Saat File Dipilih --}}
                            @if ($skill_certificate)
                                @php
                                    $isImg = false;
                                    $fileExt = '';
                                    try {
                                        $fileExt = strtolower($skill_certificate->getClientOriginalExtension());
                                        $isImg = in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    } catch (\Throwable $e) {}
                                @endphp
                                <div class="mt-2.5 p-3 rounded-xl bg-emerald-50/70 dark:bg-[#14203A] border border-emerald-200 dark:border-emerald-800/60">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span class="text-xs font-bold text-emerald-800 dark:text-[#93F514] truncate">
                                                {{ $skill_certificate->getClientOriginalName() }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-emerald-600 dark:text-[#93F514] font-mono shrink-0 ml-2">
                                            {{ number_format($skill_certificate->getSize() / 1024, 1) }} KB
                                        </span>
                                    </div>
                                    @if ($isImg)
                                        <div class="rounded-lg overflow-hidden border border-emerald-200 dark:border-emerald-800/60 max-h-48 flex items-center justify-center bg-black/5 dark:bg-black/20 p-1">
                                            <img src="{{ $skill_certificate->temporaryUrl() }}" alt="Preview" class="max-h-44 w-auto object-contain rounded">
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
                            @elseif ($existing_skill_certificate)
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-200/80 dark:border-[#1D2E54] mt-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="text-xs text-slate-700 dark:text-slate-300 font-medium truncate">{{ basename($existing_skill_certificate) }}</span>
                                    </div>
                                    <a href="{{ asset('storage/' . ltrim($existing_skill_certificate, '/')) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:text-[#93F514] hover:bg-emerald-100 dark:hover:bg-[#1D2E54] rounded-lg transition shrink-0 flex items-center gap-1 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        <span>{{ __('Buka di Tab Baru') }}</span>
                                    </a>
                                </div>
                            @endif
                            @error('skill_certificate')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                            <button type="button" wire:click="closeSkillModal" class="px-5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#14203A] hover:bg-slate-200 dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                                {{ __('Batal') }}
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-70 disabled:cursor-not-allowed text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold rounded-xl transition active:scale-95 cursor-pointer">
                                <svg wire:loading.remove wire:target="saveSkill" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isSkillEdit ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' }}" />
                                </svg>
                                <svg wire:loading wire:target="saveSkill" class="w-4 h-4 animate-spin shrink-0 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="saveSkill">{{ $isSkillEdit ? __('Simpan Perubahan') : __('Tambah Aplikasi Komputer') }}</span>
                                <span wire:loading wire:target="saveSkill">{{ __('Menyimpan...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Skill -->
    @if ($showDeleteSkillModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="cancelDeleteSkill"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200 dark:border-[#1D2E54] p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-950/60 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('Konfirmasi Hapus Data') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('Apakah Anda yakin ingin menghapus data kemampuan aplikasi komputer ini?') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                        <button type="button" wire:click="cancelDeleteSkill" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#14203A] hover:bg-slate-200 dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="button" wire:click="deleteSkill" wire:loading.attr="disabled" class="px-5 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-md shadow-red-500/20 transition flex items-center gap-1.5 active:scale-95">
                            <span wire:loading.remove wire:target="deleteSkill">{{ __('Ya, Hapus') }}</span>
                            <span wire:loading wire:target="deleteSkill">{{ __('Menghapus...') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL FORM EXTRA (SERTIFIKASI, PELATIHAN, BAHASA) -->
    @if ($showExtraModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="closeExtraModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-[#1D2E54]">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200/80 dark:border-[#1D2E54]">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>{{ $isExtraEdit ? __('Edit :type', ['type' => __($this->getExtraTypeLabel($extraModalType))]) : __('Tambah :type', ['type' => __($this->getExtraTypeLabel($extraModalType))]) }}</span>
                        </h3>
                        <button wire:click="closeExtraModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveExtra" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                                {{ __('Nama :type', ['type' => __($this->getExtraTypeLabel($extraModalType))]) }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="extraName"
                                placeholder="{{ $extraModalType === 'certification' ? __('Contoh: AWS Certified Solutions Architect / BNSP') : ($extraModalType === 'training' ? __('Contoh: Digital Marketing Bootcamp 2023') : __('Contoh: Bahasa Inggris / Bahasa Jepang')) }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                            @error('extraName')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Input Khusus Kemampuan Berbahasa --}}
                        @if ($extraModalType === 'language')
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider">
                                    {{ __('Tingkat Penguasaan') }}
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54]">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                            {{ __('Lisan') }}
                                        </label>
                                        <select wire:model="extraOral" class="w-full px-3 py-2 rounded-lg border border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                                            <option value="baik">{{ __('Baik') }}</option>
                                            <option value="cukup">{{ __('Cukup') }}</option>
                                            <option value="kurang">{{ __('Kurang') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                            {{ __('Tulisan') }}
                                        </label>
                                        <select wire:model="extraWritten" class="w-full px-3 py-2 rounded-lg border border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                                            <option value="baik">{{ __('Baik') }}</option>
                                            <option value="cukup">{{ __('Cukup') }}</option>
                                            <option value="kurang">{{ __('Kurang') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                            {{ __('Percakapan') }}
                                        </label>
                                        <select wire:model="extraConversation" class="w-full px-3 py-2 rounded-lg border border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
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
                            <input type="file" wire:model="extraCertificate" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 dark:file:bg-[#14203A] dark:file:text-slate-300 hover:file:bg-slate-200 dark:hover:file:bg-[#1A2A4C] transition">
                            
                            <div wire:loading wire:target="extraCertificate" class="mt-1.5 flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-[#93F514]">
                                <svg class="animate-spin h-3.5 w-3.5 text-blue-600 dark:text-[#93F514]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ __('Mengunggah file...') }}</span>
                            </div>

                            @error('extraCertificate')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                            {{-- Live Preview Saat File Dipilih --}}
                            @if ($extraCertificate)
                                @php
                                    $isImg = false;
                                    $fileExt = '';
                                    try {
                                        $fileExt = strtolower($extraCertificate->getClientOriginalExtension());
                                        $isImg = in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    } catch (\Throwable $e) {}
                                @endphp
                                <div class="mt-2.5 p-3 rounded-xl bg-emerald-50/70 dark:bg-[#14203A] border border-emerald-200 dark:border-emerald-800/60">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span class="text-xs font-bold text-emerald-800 dark:text-[#93F514] truncate">
                                                {{ $extraCertificate->getClientOriginalName() }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-emerald-600 dark:text-[#93F514] font-mono shrink-0 ml-2">
                                            {{ number_format($extraCertificate->getSize() / 1024, 1) }} KB
                                        </span>
                                    </div>
                                    @if ($isImg)
                                        <div class="rounded-lg overflow-hidden border border-emerald-200 dark:border-emerald-800/60 max-h-48 flex items-center justify-center bg-black/5 dark:bg-black/20 p-1">
                                            <img src="{{ $extraCertificate->temporaryUrl() }}" alt="Preview" class="max-h-44 w-auto object-contain rounded">
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
                            @elseif ($existingExtraCertificate)
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-[#14203A] rounded-xl border border-slate-200/80 dark:border-[#1D2E54] mt-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="text-xs text-slate-700 dark:text-slate-300 font-medium truncate">{{ basename($existingExtraCertificate) }}</span>
                                    </div>
                                    <a href="{{ asset('storage/' . ltrim($existingExtraCertificate, '/')) }}"
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
                            <button type="button" wire:click="closeExtraModal"
                                class="px-5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                                {{ __('Batal') }}
                            </button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="saveExtra, extraCertificate"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-70 disabled:cursor-not-allowed text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold rounded-xl transition active:scale-95 cursor-pointer">
                                <svg wire:loading.remove wire:target="saveExtra, extraCertificate" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isExtraEdit ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' }}" />
                                </svg>
                                <svg wire:loading wire:target="saveExtra" class="w-4 h-4 animate-spin shrink-0 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg wire:loading wire:target="extraCertificate" class="w-4 h-4 animate-spin shrink-0 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="saveExtra, extraCertificate">{{ $isExtraEdit ? __('Simpan Perubahan') : __('Tambah :type', ['type' => __($this->getExtraTypeLabel($extraModalType))]) }}</span>
                                <span wire:loading wire:target="saveExtra">{{ __('Menyimpan...') }}</span>
                                <span wire:loading wire:target="extraCertificate">{{ __('Mengunggah File...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS EXTRA (SERTIFIKASI, PELATIHAN, BAHASA) -->
    @if ($showDeleteExtraModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="cancelDeleteExtra"></div>
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
                                {{ __('Apakah Anda yakin ingin menghapus data :type ini? Tindakan ini tidak dapat dibatalkan.', ['type' => strtolower(__($this->getExtraTypeLabel($deleteExtraType)))]) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                        <button type="button" wire:click="cancelDeleteExtra" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                            {{ __('Batal') }}
                        </button>
                        <button type="button" wire:click="deleteExtra" wire:loading.attr="disabled" class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-500/20 transition flex items-center gap-1.5">
                            <span wire:loading.remove wire:target="deleteExtra">{{ __('Ya, Hapus') }}</span>
                            <span wire:loading wire:target="deleteExtra">{{ __('Menghapus...') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
