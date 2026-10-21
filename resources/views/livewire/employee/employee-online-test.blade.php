<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{
    timeRemaining: {{ $timeRemainingSeconds }},
    timerInterval: null,
    showConfirmModal: false,
    formatTime(sec) {
        if (sec <= 0) return '00:00:00';
        let h = Math.floor(sec / 3600);
        let m = Math.floor((sec % 3600) / 60);
        let s = sec % 60;
        return (h > 0 ? String(h).padStart(2, '0') + ':' : '') + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    },
    startTimer() {
        if (this.timeRemaining > 0 && !this.timerInterval) {
            this.timerInterval = setInterval(() => {
                if (this.timeRemaining > 0) {
                    this.timeRemaining--;
                } else {
                    clearInterval(this.timerInterval);
                    $wire.finishTestAuto();
                }
            }, 1000);
        }
    }
}" x-init="if ('{{ $testState }}' === 'taking') { startTimer(); }">

    <!-- Header Breadcrumb & Title -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('profile') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Dashboard
                    Asesmen</a>
                <span>/</span>
                <span
                    class="text-gray-900 dark:text-white font-medium">{{ $test->department->name ?? 'Seluruh Divisi' }}</span>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ $test->title }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                {{ $test->title }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Kategori: <span
                    class="font-bold text-indigo-700 dark:text-indigo-300">{{ $test->category->name ?? 'Asesmen Karyawan' }}</span>
                &bull;
                Divisi: <span
                    class="font-semibold text-gray-700 dark:text-gray-300">{{ $test->department->name ?? 'Umum / Semua Divisi' }}</span>
            </p>
        </div>

        @if ($testState === 'taking')
            <!-- Countdown Timer Badge -->
            <div
                class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs shrink-0">
                <div
                    class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span
                        class="block text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sisa
                        Waktu</span>
                    <span class="text-base font-bold text-rose-600 dark:text-rose-400 font-mono"
                        x-text="formatTime(timeRemaining)">--:--</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Flash Messages (Errors / Warnings / Success) -->
    @if (session()->has('test_error') || session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/80 flex items-start gap-3 text-red-800 dark:text-red-300">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1 text-xs">
                <p class="font-semibold text-red-900 dark:text-red-200 mb-0.5">Ujian Belum Dapat Dikirim</p>
                <p class="text-red-700 dark:text-red-300 leading-relaxed font-normal">
                    {{ session('test_error') ?: session('error') }}
                </p>
            </div>
            <button type="button" @click="show = false"
                class="text-red-400 hover:text-red-600 dark:hover:text-red-200 transition p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-3 text-xs text-emerald-800 dark:text-emerald-200">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- STATE 1: INTRO / PETUNJUK PENGERJAAN -->
    @if ($testState === 'intro')
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-4 pb-6 border-b border-gray-100 dark:border-gray-700">
                <div
                    class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span
                        class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        {{ $test->category->name ?? 'Kategori Asesmen' }}
                    </span>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mt-1">Petunjuk Pelaksanaan Asesmen
                        Karyawan</h2>
                </div>
            </div>

            <!-- Ringkasan Info Ujian -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div
                    class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200/60 dark:border-gray-800 text-center">
                    <span class="block text-xs text-gray-500 mb-1">Durasi Waktu</span>
                    <span class="text-base font-bold text-gray-900 dark:text-white">{{ $test->duration_minutes }}
                        Menit</span>
                </div>
                <div
                    class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200/60 dark:border-gray-800 text-center">
                    <span class="block text-xs text-gray-500 mb-1">Nilai KKM Kelulusan</span>
                    <span
                        class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($test->passing_score, 0) }}%</span>
                </div>
                <div
                    class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200/60 dark:border-gray-800 text-center">
                    <span class="block text-xs text-gray-500 mb-1">Jumlah Pertanyaan</span>
                    <span
                        class="text-base font-bold text-indigo-600 dark:text-indigo-400">{{ count($questions) > 0 ? count($questions) : ($test->total_questions ?: 'Sesuai Soal') }}
                        Soal</span>
                </div>
                <div
                    class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200/60 dark:border-gray-800 text-center">
                    <span class="block text-xs text-gray-500 mb-1">Metode Urutan</span>
                    <span
                        class="text-base font-bold text-gray-800 dark:text-gray-200">{{ $test->is_random ? 'Acak' : 'Urut' }}</span>
                </div>
            </div>

            <!-- Formulir Konfirmasi Biodata Peserta -->
            <div
                class="p-5 rounded-2xl bg-gradient-to-br from-indigo-50/70 via-blue-50/40 to-purple-50/50 dark:from-indigo-950/40 dark:via-gray-900/60 dark:to-purple-950/30 border border-indigo-100 dark:border-indigo-800/60 shadow-xs space-y-4">
                <div
                    class="flex items-center justify-between pb-3 border-b border-indigo-100 dark:border-indigo-800/60">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Konfirmasi Biodata Peserta Tes
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Data berikut akan dicantumkan secara resmi pada lembar hasil laporan & evaluasi PDF asesmen
                            Anda.
                        </p>
                    </div>
                    <span
                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        Wajib Diisi
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="participantName"
                            class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="participantName" wire:model.defer="participantName"
                            placeholder="Masukkan nama lengkap..."
                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-900 dark:text-white outline-none transition shadow-2xs">
                        @error('participantName')
                            <p class="mt-1 text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Usia -->
                    <div>
                        <label for="participantAge"
                            class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Usia (Tahun) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="participantAge" wire:model.defer="participantAge" min="15"
                            max="99" placeholder="Contoh: 24"
                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-900 dark:text-white outline-none transition shadow-2xs">
                        @error('participantAge')
                            <p class="mt-1 text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="participantGender"
                            class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select id="participantGender" wire:model.defer="participantGender"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-900 dark:text-white outline-none transition shadow-2xs">
                            <option value="male">Laki-laki / Pria</option>
                            <option value="female">Perempuan / Wanita</option>
                        </select>
                        @error('participantGender')
                            <p class="mt-1 text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Pelaksanaan Tes -->
                    <div>
                        <label for="testDate"
                            class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Tanggal Tes <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="testDate" wire:model.defer="testDate"
                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-900 dark:text-white outline-none transition shadow-2xs">
                        @error('testDate')
                            <p class="mt-1 text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Ketentuan Ujian -->
            <div
                class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300 space-y-2">
                <h3 class="font-semibold text-xs text-gray-900 dark:text-white">Peraturan & Hal yang Perlu
                    Diperhatikan:
                </h3>
                <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-400 leading-relaxed">
                    <li>Pastikan biodata di atas sudah sesuai sebelum menekan tombol <strong>Mulai Ujian</strong>.</li>
                    <li>Pastikan koneksi internet Anda stabil sebelum pengerjaan dimulai.</li>
                    <li>Waktu akan langsung berjalan otomatis dan tidak dapat dijeda (pause).</li>
                    <li>Jawaban Anda otomatis tersimpan setiap kali Anda memilih opsi atau mengetik jawaban.</li>
                    <li>Jika butir soal memiliki <strong>file lampiran / studi kasus</strong>, tautan unduhan akan
                        muncul di atas pertanyaan.</li>
                    <li>Ujian akan otomatis disubmit jika batas waktu pengerjaan telah habis.</li>
                </ul>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('employee.dashboard') }}"
                    class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    &larr; Kembali ke Dashboard
                </a>
                <button type="button" wire:click="startTest" wire:loading.attr="disabled" wire:target="startTest"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-xs font-medium rounded-lg transition shadow-xs cursor-pointer">
                    <svg wire:loading wire:target="startTest" class="animate-spin w-4 h-4 text-white" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span wire:loading.remove wire:target="startTest">Mulai Ujian</span>
                    <span wire:loading wire:target="startTest">Menyimpan & Memulai...</span>
                    <svg wire:loading.remove wire:target="startTest" class="w-4 h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- STATE 2: SEDANG MENGERJAKAN UJIAN (TAKING TEST) -->
    @if ($testState === 'taking')
        @php
            $isPapiPage  = collect($pageQuestions)->contains('question_type', 'papi_kostick');
            $isDiscPage  = collect($pageQuestions)->contains('question_type', 'disc');
            $isMultiPage = !$isPapiPage && !$isDiscPage;
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
            <!-- Left Side: Lembar Soal & Jawaban (3 Cols) -->
            <div class="lg:col-span-3 space-y-5">

                {{-- ============================================================ --}}
                {{-- LAYOUT TABEL: PAPI KOSTICK (semua soal halaman dalam 1 tabel) --}}
                {{-- ============================================================ --}}
                @if ($isPapiPage)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                        <!-- Header -->
                        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 bg-indigo-900">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2.5 py-0.5 rounded-lg bg-white/20 text-white font-black text-xs">
                                    Soal {{ $pageStart + 1 }}–{{ min($pageStart + $questionsPerPage, $totalQuestions) }}
                                </span>
                                <span class="text-white/70 text-xs font-medium">dari {{ $totalQuestions }} Soal PAPI Kostick</span>
                            </div>
                            <span class="text-[11px] font-semibold text-white/80">Pilih 1 pernyataan yang paling sesuai diri Anda (A atau B)</span>
                        </div>

                        <!-- Tabel semua soal di halaman ini -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs border-collapse">
                                <thead>
                                    <tr class="border-b-2 border-gray-300 dark:border-gray-600 text-center text-[11px] font-extrabold uppercase tracking-wider">
                                        <th class="w-12 py-2.5 px-2 bg-gray-200 dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-r-2 border-gray-300 dark:border-gray-600">No</th>
                                        <th class="w-14 py-2.5 px-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-800 dark:text-indigo-300 border-r-2 border-gray-300 dark:border-gray-600">Pilih</th>
                                        <th class="py-2.5 px-4 bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-gray-300 text-left">Pernyataan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pageQuestions as $pgIdx => $pq)
                                        @php
                                            $globalIdx    = $pageStart + $pgIdx;
                                            $selectedPapi = $answers[$pq['id']] ?? null;
                                            $isEven       = $pgIdx % 2 === 0;
                                        @endphp
                                        {{-- Wrapper Alpine per soal: state selected lokal agar highlight instan --}}
                                        <tbody wire:key="papi-group-{{ $pq['id'] }}" wire:ignore
                                            x-data="{ sel: {{ $selectedPapi ?? 'null' }} }"
                                            class="border-t-4 {{ $isEven ? 'border-indigo-300 dark:border-indigo-700' : 'border-gray-400 dark:border-gray-500' }}">

                                            @foreach ($pq['options'] as $oIdx => $opt)
                                                @php $label = ['A','B','C','D','E'][$oIdx] ?? ($oIdx+1); @endphp
                                                <tr class="cursor-pointer"
                                                    :class="sel == {{ $opt['id'] }}
                                                        ? 'bg-indigo-50 dark:bg-indigo-950/40'
                                                        : '{{ $isEven ? 'hover:bg-slate-50 dark:hover:bg-slate-800/50' : 'hover:bg-gray-50 dark:hover:bg-gray-800/40' }}'"
                                                    @click="sel = {{ $opt['id'] }}; $wire.saveAnswer({{ $pq['id'] }}, {{ $opt['id'] }})">

                                                    @if ($oIdx === 0)
                                                        <td rowspan="{{ count($pq['options']) }}"
                                                            class="text-center font-black text-sm w-12 border-r-2 border-gray-300 dark:border-gray-600 select-none align-middle
                                                                {{ $isEven ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-900 dark:text-indigo-100' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white' }}">
                                                            {{ $globalIdx + 1 }}
                                                        </td>
                                                    @endif

                                                    {{-- Kolom radio --}}
                                                    <td class="text-center w-14 border-r-2 border-gray-200 dark:border-gray-700 py-0"
                                                        @click.stop="sel = {{ $opt['id'] }}; $wire.saveAnswer({{ $pq['id'] }}, {{ $opt['id'] }})">
                                                        <label class="flex items-center justify-center w-full h-11 cursor-pointer">
                                                            <input type="radio"
                                                                id="papi_opt_{{ $opt['id'] }}"
                                                                name="papi_q_{{ $pq['id'] }}"
                                                                value="{{ $opt['id'] }}"
                                                                :checked="sel == {{ $opt['id'] }}"
                                                                class="w-4 h-4 text-indigo-600 border-2 border-gray-400 focus:ring-indigo-500 cursor-pointer">
                                                        </label>
                                                    </td>

                                                    {{-- Teks pernyataan --}}
                                                    <td class="py-3 px-4 text-gray-800 dark:text-gray-200 font-medium text-xs sm:text-sm leading-snug">
                                                        <span class="inline-flex items-center gap-2.5">
                                                            <span class="w-6 h-6 rounded-md flex items-center justify-center text-[11px] font-black shrink-0 transition-colors duration-100"
                                                                :class="sel == {{ $opt['id'] }}
                                                                    ? 'bg-indigo-600 text-white shadow'
                                                                    : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300'">
                                                                {{ $label }}
                                                            </span>
                                                            <span :class="sel == {{ $opt['id'] }} ? 'text-indigo-900 dark:text-indigo-100 font-semibold' : ''">
                                                                {{ $opt['option_text'] }}
                                                            </span>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>



                {{-- ============================================================ --}}
                {{-- LAYOUT TABEL: DISC (semua soal halaman dalam 1 tabel)         --}}
                {{-- ============================================================ --}}
                @elseif ($isDiscPage)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                        <!-- Header -->
                        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-200 dark:border-gray-700 bg-indigo-900">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2.5 py-0.5 rounded-lg bg-white/20 text-white font-black text-xs">
                                    Soal {{ $questionsPerPage > 1 ? ($pageStart + 1) . '–' . min($pageStart + $questionsPerPage, $totalQuestions) : ($pageStart + 1) }}
                                </span>
                                <span class="text-white/70 text-xs font-medium">dari {{ $totalQuestions }} Pernyataan DISC</span>
                            </div>
                            <span class="text-[11px] font-semibold text-white/80">Pilih 1 kolom <strong class="text-amber-300">P</strong> (Paling) dan 1 kolom <strong class="text-sky-300">K</strong> (Kurang) per baris soal</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs border-collapse text-left">
                                <thead>
                                    <tr class="font-extrabold text-sm border-b-2 border-gray-900 dark:border-gray-600 text-center">
                                        <th class="w-10 py-2.5 px-2 bg-gray-400 text-gray-900 border-r-2 border-gray-900 dark:border-gray-600">No.</th>
                                        <th class="w-12 py-2.5 px-2 bg-amber-400 text-gray-900 border-r-2 border-gray-900 dark:border-gray-600">P</th>
                                        <th class="w-12 py-2.5 px-2 bg-sky-400 text-gray-900 border-r-2 border-gray-900 dark:border-gray-600">K</th>
                                        <th class="py-2.5 px-4 bg-indigo-900 text-white font-bold text-left tracking-wide">Gambaran Diri</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y-2 divide-gray-300 dark:divide-gray-600 font-medium text-xs">
                                    @foreach ($pageQuestions as $pgIdx => $dq)
                                        @php
                                            $globalIdx  = $pageStart + $pgIdx;
                                            $mostOpt    = $answers[$dq['id']]['most'] ?? null;
                                            $leastOpt   = $answers[$dq['id']]['least'] ?? null;
                                        @endphp
                                        @foreach ($dq['options'] as $oIdx => $opt)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition border-t border-gray-200 dark:border-gray-700"
                                                wire:key="disc-row-{{ $dq['id'] }}-{{ $opt['id'] }}">
                                                @if ($oIdx === 0)
                                                    <td rowspan="{{ count($dq['options']) }}"
                                                        class="text-center font-black text-base bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white border-r-2 border-gray-900 dark:border-gray-600 w-10">
                                                        {{ $globalIdx + 1 }}
                                                    </td>
                                                @endif
                                                <!-- Kolom P -->
                                                <td class="text-center border-r-2 border-gray-900 dark:border-gray-600 p-0 w-12
                                                    {{ $leastOpt == $opt['id'] ? 'bg-gray-200 dark:bg-gray-700 opacity-40' : 'bg-amber-400/90' }}">
                                                    <label class="w-full h-10 flex items-center justify-center
                                                        {{ $leastOpt == $opt['id'] ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-amber-500/80 transition' }}">
                                                        <input type="radio"
                                                            name="disc_most_{{ $dq['id'] }}"
                                                            value="{{ $opt['id'] }}"
                                                            {{ $mostOpt == $opt['id'] ? 'checked' : '' }}
                                                            {{ $leastOpt == $opt['id'] ? 'disabled' : '' }}
                                                            wire:click="saveAnswer({{ $dq['id'] }}, {{ $opt['id'] }}, null, 'most')"
                                                            class="w-5 h-5 text-emerald-600 border-2 border-gray-900 {{ $leastOpt == $opt['id'] ? 'cursor-not-allowed opacity-30' : 'cursor-pointer' }}">
                                                    </label>
                                                </td>
                                                <!-- Kolom K -->
                                                <td class="text-center border-r-2 border-gray-900 dark:border-gray-600 p-0 w-12
                                                    {{ $mostOpt == $opt['id'] ? 'bg-gray-200 dark:bg-gray-700 opacity-40' : 'bg-sky-400/90' }}">
                                                    <label class="w-full h-10 flex items-center justify-center
                                                        {{ $mostOpt == $opt['id'] ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-sky-500/80 transition' }}">
                                                        <input type="radio"
                                                            name="disc_least_{{ $dq['id'] }}"
                                                            value="{{ $opt['id'] }}"
                                                            {{ $leastOpt == $opt['id'] ? 'checked' : '' }}
                                                            {{ $mostOpt == $opt['id'] ? 'disabled' : '' }}
                                                            wire:click="saveAnswer({{ $dq['id'] }}, {{ $opt['id'] }}, null, 'least')"
                                                            class="w-5 h-5 text-rose-600 border-2 border-gray-900 {{ $mostOpt == $opt['id'] ? 'cursor-not-allowed opacity-30' : 'cursor-pointer' }}">
                                                    </label>
                                                </td>
                                                <!-- Teks Gambaran Diri -->
                                                <td class="py-2.5 px-4 text-gray-900 dark:text-gray-100 font-semibold bg-white dark:bg-gray-800 text-xs sm:text-sm">
                                                    {{ $opt['option_text'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                {{-- ============================================================ --}}
                {{-- LAYOUT KARTU: MULTIPLE CHOICE / ESSAY (5 soal berurutan)     --}}
                {{-- ============================================================ --}}
                @else
                    @foreach ($pageQuestions as $pgIdx => $pq)
                        @php $globalIdx = $pageStart + $pgIdx; @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 sm:p-6 space-y-4"
                             wire:key="question-card-{{ $pq['id'] }}">
                            <!-- Question Header -->
                            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-black text-xs">
                                        No. {{ $globalIdx + 1 }}
                                    </span>
                                    <span class="px-2 py-1 rounded-lg text-[11px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        @if ($pq['question_type'] === 'multiple_choice') Pilihan Ganda
                                        @elseif ($pq['question_type'] === 'essay') Essay / Uraian
                                        @else {{ ucfirst($pq['question_type']) }}
                                        @endif
                                    </span>
                                </div>
                                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                                    {{ $pq['points'] ?? 1 }} Poin
                                </span>
                            </div>

                            <!-- Lampiran Soal -->
                            @if (!empty($pq['image_path']))
                                @php
                                    $fpExt = strtolower(pathinfo($pq['image_path'], PATHINFO_EXTENSION));
                                    $fpUrl = asset('storage/' . $pq['image_path']);
                                    $fpImg = in_array($fpExt, ['jpg','jpeg','png','gif','webp','svg']);
                                    $fpPdf = ($fpExt === 'pdf');
                                @endphp

                                @if ($fpPdf)
                                    <div class="p-3.5 rounded-xl bg-rose-50/80 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 flex items-center justify-between gap-3 shadow-2xs">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-9 h-9 rounded-lg bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM9.5 17.5c-.28 0-.5-.22-.5-.5v-4c0-.28.22-.5.5-.5s.5.22.5.5v4c0 .28-.22.5-.5.5zm2-4.5h.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5H12v1c0 .28-.22.5-.5.5s-.5-.22-.5-.5v-4c0-.28.22-.5.5-.5zm.5 2c.28 0 .5-.22.5-.5s-.22-.5-.5-.5H12v1h.5zm2.5-2h1c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-1c-.28 0-.5-.22-.5-.5v-4c0-.28.22-.5.5-.5zm.5 3h.5c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5H15v2z" />
                                                </svg>
                                            </div>
                                            <div class="truncate">
                                                <span class="text-xs sm:text-sm font-bold text-rose-900 dark:text-rose-200 block truncate">Dokumen Lampiran Soal (PDF)</span>
                                                <span class="text-[11px] text-gray-500 dark:text-gray-400 block truncate">Klik untuk membaca materi studi kasus di tab baru</span>
                                            </div>
                                        </div>
                                        <a href="{{ $fpUrl }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-lg shadow-xs transition shrink-0" title="Buka Dokumen di Tab Baru">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            <span>Buka di Tab Baru</span>
                                        </a>
                                    </div>
                                @elseif ($fpImg)
                                    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/60 p-2 text-center">
                                        <img src="{{ $fpUrl }}" alt="Lampiran Soal" class="max-h-72 mx-auto rounded-lg object-contain">
                                    </div>
                                @else
                                    <div class="p-3 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900 flex items-center justify-between gap-2">
                                        <span class="text-xs font-semibold text-indigo-800 dark:text-indigo-200">Lampiran Soal ({{ strtoupper($fpExt) }})</span>
                                        <a href="{{ $fpUrl }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold transition">
                                            Buka Dokumen
                                        </a>
                                    </div>
                                @endif
                            @endif

                            <!-- Teks Soal -->
                            <div class="text-sm font-medium text-gray-900 dark:text-white whitespace-pre-line leading-relaxed">
                                {{ $pq['question'] }}
                            </div>

                            <!-- Opsi Multiple Choice -->
                            @if ($pq['question_type'] === 'multiple_choice')
                                @php
                                    $labels = ['A','B','C','D','E'];
                                    $selMC  = $answers[$pq['id']] ?? null;
                                @endphp
                                <div class="space-y-2 pt-1" wire:key="mc-{{ $pq['id'] }}" wire:ignore
                                    x-data="{ selected: {{ $selMC ?? 'null' }} }">
                                    @foreach ($pq['options'] as $oi => $opt)
                                        <label @click="selected = {{ $opt['id'] }}; $wire.saveAnswer({{ $pq['id'] }}, {{ $opt['id'] }})"
                                            class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all duration-150"
                                            :class="selected == {{ $opt['id'] }}
                                                ? 'bg-indigo-50/80 dark:bg-indigo-950/50 border-indigo-500 ring-2 ring-indigo-500/20 font-semibold text-indigo-900 dark:text-indigo-100'
                                                : 'bg-gray-50/50 dark:bg-gray-900/40 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-100/80 dark:hover:bg-gray-700/50'">
                                            <input type="radio" name="mc_{{ $pq['id'] }}" value="{{ $opt['id'] }}" :checked="selected == {{ $opt['id'] }}" class="sr-only">
                                            <span class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs shrink-0"
                                                :class="selected == {{ $opt['id'] }} ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
                                                {{ $labels[$oi] ?? $oi+1 }}
                                            </span>
                                            <span class="text-xs flex-1 leading-snug">{{ $opt['option_text'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Essay -->
                            @if ($pq['question_type'] === 'essay')
                                <div class="space-y-3 pt-1" wire:key="essay-{{ $pq['id'] }}">
                                    <textarea rows="5"
                                        wire:model.lazy="answers.{{ $pq['id'] }}"
                                        wire:change="submitEssayAnswer({{ $pq['id'] }})"
                                        placeholder="Ketikkan jawaban essay Anda..."
                                        class="w-full p-3 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition leading-relaxed"></textarea>
                                    <p class="text-[11px] text-gray-400">Jawaban tersimpan otomatis saat Anda berpindah soal.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif

                {{-- UPLOAD FILE LAMPIRAN & FORM KHUSUS LINK (1 FILE & 1 LINK UNTUK SELURUH SOAL URAIAN) --}}
                @if (collect($pageQuestions)->contains('question_type', 'essay') || collect($questions)->contains('question_type', 'essay'))
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 space-y-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-800 dark:text-white">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <span>Lampiran File & Tautan Jawaban Uraian (Opsional)</span>
                            </div>
                            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/60 px-2.5 py-0.5 rounded-full border border-gray-200 dark:border-gray-700">
                                1 File & 1 Tautan untuk Seluruh Soal Uraian
                            </span>
                        </div>

                        {{-- Peringatan & Panduan Upload File / Video --}}
                        <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-700/60 space-y-2">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/20 dark:bg-amber-400/20 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 space-y-1.5 text-xs text-amber-900 dark:text-amber-200 leading-relaxed">
                                    <p class="font-bold text-sm text-amber-900 dark:text-amber-100">Petunjuk File Dokumen & Link Video</p>
                                    <ul class="list-disc ml-4 space-y-1">
                                        <li><strong>File Dokumen (Kolom Kiri):</strong> Gunakan jika file Anda berupa PDF, Word, Excel, atau Gambar dengan ukuran <strong>maksimal 10 MB</strong>.</li>
                                        <li><strong>Tautan Video / Eksternal (Kolom Kanan):</strong> Jika jawaban berupa <strong>Video</strong> atau file besar, unggah ke <strong>Google Drive</strong> atau <strong>YouTube</strong> lalu tempel link-nya di kolom tautan.</li>
                                    </ul>
                                    <div class="mt-2 p-2.5 rounded-lg bg-amber-100/80 dark:bg-amber-900/50 border border-amber-300/60 dark:border-amber-700/60 font-medium flex items-start gap-2">
                                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span>Pastikan akses link diatur ke <strong class="underline underline-offset-2">"Siapa saja yang memiliki link"</strong> (Google Drive) atau <strong class="underline underline-offset-2">"Unlisted"</strong> (YouTube) agar penilai dapat membukanya.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- DUA KOLOM BERSANDING: UPLOAD FILE & FORM KHUSUS LINK --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            {{-- KOLOM 1: UPLOAD FILE LAMPIRAN (MAKS 10MB) --}}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span>1. Unggah File Fisik (Maks. 10 MB)</span>
                                    </label>
                                    @if ($essayAttachment)
                                        <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            File Tersimpan
                                        </span>
                                    @endif
                                </div>

                                @error('essayFile')
                                    <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror

                                @if ($essayAttachment)
                                    {{-- PREVIEW FILE YANG SUDAH DIUNGGAH --}}
                                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-indigo-50/70 dark:bg-gray-900/60 border border-indigo-200 dark:border-gray-700 transition">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl bg-indigo-600/10 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="truncate">
                                                <a href="{{ $essayAttachment['url'] }}" target="_blank" class="text-xs font-semibold text-indigo-700 dark:text-indigo-400 hover:underline truncate block">
                                                    {{ $essayAttachment['name'] }}
                                                </a>
                                                <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                                    @if(!empty($essayAttachment['size']))
                                                        {{ $essayAttachment['size'] >= 1048576 ? round($essayAttachment['size'] / 1048576, 2) . ' MB' : round($essayAttachment['size'] / 1024, 1) . ' KB' }} •
                                                    @endif
                                                    File Tersimpan
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 ml-2">
                                            <a href="{{ $essayAttachment['url'] }}" target="_blank" class="p-2 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-gray-700 rounded-lg transition" title="Buka / Preview File">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                            <button type="button" wire:click="removeEssayAttachment" wire:confirm="Apakah Anda yakin ingin menghapus lampiran file ini?" class="p-2 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition" title="Hapus File">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    {{-- INPUT UPLOAD FILE BARU (AUTO SAVE) --}}
                                    <div>
                                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900/40 hover:border-indigo-400 dark:hover:border-indigo-500 cursor-pointer transition group">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 truncate">
                                                <span class="text-xs text-gray-700 dark:text-gray-300 font-medium">
                                                    Pilih atau drag file dokumen (Maks. 10 MB)
                                                </span>
                                                <span class="block text-[10px] text-gray-400 dark:text-gray-500">File otomatis tersimpan setelah dipilih</span>
                                            </div>
                                            <input type="file" wire:model="essayFile" class="sr-only">
                                        </label>
                                    </div>

                                    {{-- Livewire Uploading Indicator --}}
                                    <div wire:loading wire:target="essayFile" class="p-3 rounded-xl bg-indigo-50/70 dark:bg-gray-900/60 border border-indigo-200 dark:border-gray-700 text-xs text-indigo-700 dark:text-indigo-400 flex items-center gap-2.5">
                                        <svg class="animate-spin w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        <span class="font-medium">Sedang mengunggah dan menyimpan file lampiran...</span>
                                    </div>
                                @endif
                            </div>

                            {{-- KOLOM 2: FORM KHUSUS LINK (GOOGLE DRIVE / YOUTUBE) --}}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        <span>2. Tautan Video / File Eksternal (Opsional)</span>
                                    </label>
                                    @if ($essayLink)
                                        <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Tautan Tersimpan
                                        </span>
                                    @endif
                                </div>

                                @error('essayLink')
                                    <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror

                                @if (session()->has('link_message'))
                                    <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>{{ session('link_message') }}</span>
                                    </div>
                                @endif

                                @if ($essayLink)
                                    @php
                                        $isGdrive = str_contains($essayLink, 'drive.google.com');
                                        $isYt = str_contains($essayLink, 'youtube.com') || str_contains($essayLink, 'youtu.be');
                                    @endphp
                                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-indigo-50/70 dark:bg-gray-900/60 border border-indigo-200 dark:border-gray-700 transition">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl {{ $isGdrive ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : ($isYt ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300') }} flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                </svg>
                                            </div>
                                            <div class="truncate">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $isGdrive ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300' : ($isYt ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300') }}">
                                                        {{ $isGdrive ? 'Google Drive' : ($isYt ? 'YouTube' : 'Tautan Eksternal') }}
                                                    </span>
                                                </div>
                                                <a href="{{ $essayLink }}" target="_blank" class="text-xs font-mono text-gray-600 dark:text-gray-300 hover:underline truncate block mt-0.5">
                                                    {{ $essayLink }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 ml-2">
                                            <a href="{{ $essayLink }}" target="_blank" class="p-2 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-gray-700 rounded-lg transition" title="Uji / Buka Tautan">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                            <button type="button" wire:click="removeEssayLink" wire:confirm="Hapus tautan ini?" class="p-2 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition" title="Hapus Tautan">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2">
                                            <div class="relative flex-1">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                    </svg>
                                                </div>
                                                <input type="url"
                                                    wire:model.lazy="essayLink"
                                                    wire:keydown.enter.prevent="saveEssayLink"
                                                    placeholder="https://drive.google.com/... atau https://youtu.be/..."
                                                    class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-white dark:bg-gray-900/60 border border-gray-300 dark:border-gray-600 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition font-mono">
                                            </div>
                                            <button type="button" wire:click="saveEssayLink"
                                                class="px-3.5 py-2.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-xs transition shrink-0 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>Simpan Link</span>
                                            </button>
                                        </div>
                                        <span class="block text-[10px] text-gray-400 dark:text-gray-500">Ketik/tempel link lalu tekan Enter atau klik Simpan Link.</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- =================== NAVIGASI HALAMAN =================== -->
                <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-3 shadow-xs">
                    <button type="button" wire:click="prevQuestion"
                        {{ $currentPage === 0 ? 'disabled' : '' }}
                        wire:loading.attr="disabled"
                        wire:target="prevQuestion,nextQuestion,selectQuestion"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Kembali
                    </button>

                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                        Halaman <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $currentPage + 1 }}</span> / {{ $totalPages }}
                        &nbsp;·&nbsp; {{ $questionsPerPage > 1 ? 'Soal ' . ($pageStart + 1) . '–' . min($pageStart + $questionsPerPage, $totalQuestions) : 'Soal ' . ($pageStart + 1) }}
                    </span>

                    @if ($currentPage < $totalPages - 1)
                        <button type="button" wire:click="nextQuestion"
                            wire:loading.attr="disabled"
                            wire:target="prevQuestion,nextQuestion,selectQuestion"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition disabled:opacity-60">
                            <span>Lanjut</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    @else
                        <button type="button" @click="showConfirmModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Kirim Jawaban & Selesai
                        </button>
                    @endif
                </div>
            </div>

            <!-- Right Side: Nomor Soal Grid & Submit Button (1 Col) -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 space-y-4 sticky top-24">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider">
                            Navigasi Soal
                        </h3>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            {{ $completedCount }} / {{ $totalQuestions }} Terisi
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
                            <span>Kelengkapan</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $progressPercent }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-600 transition-all duration-300"
                                style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>

                    <!-- Number Grid: klik langsung lompat ke halaman yang memuat soal tersebut -->
                    <div class="grid grid-cols-5 gap-1.5">
                        @foreach ($questions as $idx => $q)
                            @php
                                $status = 'unanswered';
                                if ($q['question_type'] === 'disc') {
                                    $hasMost  = !empty($answers[$q['id']]['most']);
                                    $hasLeast = !empty($answers[$q['id']]['least']);
                                    if ($hasMost && $hasLeast)   { $status = 'completed'; }
                                    elseif ($hasMost || $hasLeast) { $status = 'partial'; }
                                } elseif ($q['question_type'] === 'multiple_choice' || $q['question_type'] === 'papi_kostick') {
                                    $status = !empty($answers[$q['id']]) ? 'completed' : 'unanswered';
                                } elseif ($q['question_type'] === 'essay') {
                                    $firstEssay = collect($questions)->firstWhere('question_type', 'essay');
                                    $isFirstEssay = ($firstEssay && $firstEssay['id'] == $q['id']);
                                    $hasText = !empty($answers[$q['id']]) && trim($answers[$q['id']]) !== '';
                                    $hasFileOrLink = $isFirstEssay && ($essayAttachment || !empty($essayLink));
                                    $status = ($hasText || $hasFileOrLink) ? 'completed' : 'unanswered';
                                }
                                // Soal aktif = soal yang ada di halaman saat ini
                                $isOnCurrentPage = ($idx >= $pageStart && $idx < $pageStart + $questionsPerPage);
                            @endphp
                            <button type="button" wire:click="selectQuestion({{ $idx }})"
                                class="h-8 rounded-lg text-xs font-medium flex items-center justify-center transition border
                                    {{ $isOnCurrentPage ? 'ring-2 ring-indigo-500 ring-offset-1 border-indigo-500' : '' }}
                                    {{ $status === 'completed'
                                        ? 'bg-emerald-600 text-white border-emerald-600'
                                        : ($status === 'partial'
                                            ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-700 font-semibold'
                                            : 'bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700') }}"
                                title="Soal No. {{ $idx + 1 }}">
                                {{ $idx + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Status Legend -->
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700 space-y-1.5 text-[11px] text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 shrink-0"></span>
                            <span>Sudah Lengkap {{ collect($questions)->contains('question_type', 'disc') ? '(P & K)' : '' }}</span>
                        </div>
                        @if (collect($questions)->contains('question_type', 'disc'))
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
                                <span>Kurang P atau K</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-300 dark:bg-gray-600 shrink-0"></span>
                            <span>Belum Dijawab</span>
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-2.5 h-2.5 rounded-full border-2 border-indigo-500 shrink-0"></span>
                            <span>Halaman Saat Ini</span>
                        </div>
                    </div>

                    @if ($completedCount < $totalQuestions)
                        <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 text-[11px] text-amber-800 dark:text-amber-300">
                            Masih ada {{ $totalQuestions - $completedCount }} butir soal yang belum lengkap.
                        </div>
                    @endif

                    <button type="button" @click="showConfirmModal = true"
                        class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition flex items-center justify-center gap-2 mt-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Selesaikan Ujian</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Confirmation Modal -->
        <div x-show="showConfirmModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/75 backdrop-blur-xs"
                    @click="showConfirmModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showConfirmModal" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-200 dark:border-gray-700 p-6 space-y-4">

                    @if ($completedCount === $totalQuestions)
                        <!-- State 1: Semua Soal Sudah Terisi Lengkap -->
                        <div class="flex items-start gap-3.5">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0 border border-emerald-200 dark:border-emerald-800/80">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                    Selesaikan Ujian?
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                                    Seluruh <strong>{{ $totalQuestions }} butir soal</strong> telah terisi. Setelah
                                    dikirim, jawaban tidak dapat diubah kembali.
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="showConfirmModal = false"
                                class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition">
                                Batal
                            </button>
                            <button type="button" wire:click="finishTest" @click="showConfirmModal = false"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition shadow-xs">
                                <span wire:loading.remove wire:target="finishTest">Ya, Selesaikan</span>
                                <span wire:loading wire:target="finishTest">Menyimpan...</span>
                            </button>
                        </div>
                    @else
                        <!-- State 2: Ada Soal Belum Lengkap -->
                        <div class="flex items-start gap-3.5">
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0 border border-amber-200 dark:border-amber-800/80">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                    Jawaban Belum Lengkap
                                </h3>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                    Masih terdapat <strong>{{ $totalQuestions - $completedCount }} butir soal</strong>
                                    yang belum lengkap / belum dijawab. Seluruh butir soal wajib terisi sebelum
                                    menyelesaikan ujian ini.
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="showConfirmModal = false; $wire.finishTest()"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition shadow-xs">
                                <span>Lengkapi Jawaban Sekarang</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif

    <!-- STATE 3: COMPLETED / HASIL UJIAN -->
    @if ($testState === 'completed')
        @php
            $hasEssayQuestions = collect($questions)->contains('question_type', 'essay');
            $hasMultipleChoice = collect($questions)->contains('question_type', 'multiple_choice');
            $isWaitingReview = $hasEssayQuestions && ($attempt && is_null($attempt->essay_score));
        @endphp

        <div
            class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-10 text-center space-y-6">
            <div
                class="w-16 h-16 rounded-3xl {{ $isWaitingReview ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400' }} flex items-center justify-center mx-auto shadow-md">
                @if ($isWaitingReview)
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </div>

            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">
                    {{ $isWaitingReview ? 'Jawaban Asesmen Berhasil Terkirim!' : 'Asesmen Berhasil Diselesaikan!' }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Terima kasih telah menyelesaikan paket asesmen <span
                        class="font-bold text-gray-800 dark:text-gray-200">{{ $test->title }}</span>.
                </p>
            </div>

            @if ($isWaitingReview)
                <!-- Banner Khusus Menunggu Review Essay -->
                <div
                    class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 text-left space-y-2">
                    <div class="flex items-center gap-2 text-amber-800 dark:text-amber-200 font-bold text-xs">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Menunggu Evaluasi & Penilaian dari Penguji / HR</span>
                    </div>
                    <p class="text-[11px] text-amber-700 dark:text-amber-300 leading-relaxed">
                        Asesmen ini memuat soal berbentuk <strong>Uraian / Essay</strong>. Jawaban Anda telah tersimpan
                        dengan aman dan saat ini sedang dalam antrean pemeriksaan oleh tim penilai.
                    </p>
                </div>
            @endif

            @if ($discResult || str_contains(strtolower($test->category?->name ?? ''), 'disc'))
                <!-- Khusus Tes DISC: Sembunyikan detail hasil dari employee, hanya notifikasi tersimpan -->
                <div
                    class="p-6 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-800/60 text-center space-y-2">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 mb-1">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Jawaban Anda Telah Tersimpan</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                        Terima kasih telah menyelesaikan inventori kepribadian DISC. Seluruh jawaban Anda telah
                        tersimpan dengan aman dan akan dievaluasi langsung oleh Tim HR / Tim Penilai.
                    </p>
                </div>
            @elseif ($papiResult || str_contains(strtolower($test->category?->name ?? ''), 'papi'))

                <!-- Tes PAPI Kostick: Tampilkan hanya notifikasi tersimpan -->
                <div class="p-6 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-800/60 text-center space-y-2">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 mb-1">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Jawaban Anda Telah Tersimpan</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                        Terima kasih telah menyelesaikan inventori PAPI Kostick. Seluruh jawaban Anda telah
                        tersimpan dengan aman dan akan dievaluasi langsung oleh Tim HR / Tim Penilai.
                    </p>
                </div>
            @else
                <!-- Score Summary Card untuk Tes Non-DISC (Pilihan Ganda & Essay) -->
                <div
                    class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800 space-y-3">
                    <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                        <span>Status Pengerjaan:</span>
                        @if ($isWaitingReview)
                            <span
                                class="font-bold px-3 py-1 rounded-full text-[11px] bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                Menunggu Review Essay
                            </span>
                        @elseif ($attempt && $attempt->status === 'passed')
                            <span
                                class="font-bold px-3 py-1 rounded-full text-[11px] bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                Lulus (Passed)
                            </span>
                        @elseif ($attempt && $attempt->status === 'failed')
                            <span
                                class="font-bold px-3 py-1 rounded-full text-[11px] bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                Belum Lolos KKM
                            </span>
                        @else
                            <span
                                class="font-bold px-3 py-1 rounded-full text-[11px] bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                Selesai (Completed)
                            </span>
                        @endif
                    </div>

                    @if ($hasMultipleChoice)
                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span>Skor Pilihan Ganda:</span>
                            <span class="font-extrabold text-indigo-600 dark:text-indigo-400 text-sm">
                                {{ number_format($attempt->objective_score ?? 0, 1) }} Poin
                            </span>
                        </div>
                    @endif

                    @if ($hasEssayQuestions)
                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span>Skor Essay / Uraian:</span>
                            @if ($attempt && $attempt->essay_score !== null)
                                <span class="font-extrabold text-amber-600 dark:text-amber-400 text-sm">
                                    {{ number_format($attempt->essay_score, 1) }} Poin
                                </span>
                            @else
                                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 italic">
                                    Sedang Dinilai Tim HR
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <span class="font-bold text-xs text-gray-900 dark:text-white">Total Skor Akhir:</span>
                        @if ($isWaitingReview)
                            <span
                                class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2.5 py-1 rounded-lg border border-amber-200 dark:border-amber-800">
                                Menunggu Penilaian Essay
                            </span>
                        @else
                            <span class="text-xl font-black text-gray-900 dark:text-white">
                                {{ number_format($attempt->total_score ?? 0, 1) }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif


            <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed pt-4">
                Hasil asesmen ini telah tersimpan dalam rekam jejak kompetensi karyawan.
            </p>

            <div class="pt-4">
                <a href="{{ route('profile') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-500/20 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Dashboard Asesmen</span>
                </a>
            </div>
        </div>
    @endif

</div>
