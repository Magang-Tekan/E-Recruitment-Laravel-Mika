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
    },
    isImg(path) {
        if (!path) return false;
        const ext = path.split('.').pop().toLowerCase();
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext);
    },
    getFileExt(path) {
        if (!path) return 'FILE';
        return path.split('.').pop().toUpperCase();
    },
    getFileName(path) {
        if (!path) return 'Lampiran File';
        return path.split('/').pop();
    }
}" x-init="if ('{{ $testState }}' === 'taking') { startTimer(); }">

    <!-- Header Breadcrumb & Title -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-[#93A5C9] mb-1">
                <a href="{{ route('profile', ['tab' => 'riwayat']) }}" class="hover:text-blue-600 dark:hover:text-[#93F514]">Riwayat Lamaran</a>
                <span>/</span>
                <span class="text-slate-900 dark:text-white font-medium">{{ $application->job->title ?? 'Ujian' }}</span>
                <span>/</span>
                <span class="text-blue-600 dark:text-[#93F514] font-semibold">{{ $test->title ?? 'Tes Rekrutmen' }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">
                {{ $test->title }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-0.5">
                Lowongan: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $application->job->title }}</span> di <span class="font-semibold text-blue-600 dark:text-[#93F514]">{{ $application->job->company->name ?? 'Perusahaan' }}</span>
            </p>
        </div>

        @if ($testState === 'taking')
            <!-- Countdown Timer Badge -->
            <div class="flex items-center gap-3 bg-white dark:bg-[#0D1527] px-4 py-2.5 rounded-xl border border-slate-200 dark:border-[#1D2E54] shadow-xs shrink-0">
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-[#14203A] text-amber-600 dark:text-amber-400 border border-transparent dark:border-[#1D2E54] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:text-[#93A5C9]">Sisa Waktu</span>
                    <span class="text-base font-bold text-rose-600 dark:text-rose-400 font-mono" x-text="formatTime(timeRemaining)">--:--</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Flash Messages (Errors / Warnings / Success) -->
    @if (session()->has('test_error') || session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 flex items-start gap-3 text-rose-800 dark:text-rose-300">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1 text-xs">
                <p class="font-semibold text-rose-900 dark:text-rose-200 mb-0.5">Ujian Belum Dapat Dikirim</p>
                <p class="text-rose-700 dark:text-rose-300 leading-relaxed font-normal">
                    {{ session('test_error') ?: session('error') }}
                </p>
            </div>
            <button type="button" @click="show = false" class="text-rose-400 hover:text-rose-600 dark:hover:text-rose-200 transition p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-3 text-xs text-emerald-800 dark:text-emerald-200">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        <div class="bg-white dark:bg-[#0D1527] rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] p-6 sm:p-8 space-y-6 shadow-sm">
            <div class="flex items-center gap-4 pb-6 border-b border-slate-100 dark:border-[#1D2E54]">
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-[#14203A] border border-blue-200/80 dark:border-[#1D2E54] text-blue-600 dark:text-[#93F514] flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#1D2E54]">
                        {{ $test->category->name ?? 'Kategori Ujian' }}
                    </span>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mt-1">Petunjuk Pelaksanaan Ujian</h2>
                </div>
            </div>

            <!-- Ringkasan Info Ujian -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/60 dark:border-[#1D2E54] text-center">
                    <span class="block text-xs text-slate-500 dark:text-[#93A5C9] mb-1">Durasi Waktu</span>
                    <span class="text-base font-bold text-slate-900 dark:text-white">{{ $test->duration_minutes }} Menit</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/60 dark:border-[#1D2E54] text-center">
                    <span class="block text-xs text-slate-500 dark:text-[#93A5C9] mb-1">Nilai KKM Kelulusan</span>
                    <span class="text-base font-bold text-emerald-600 dark:text-[#93F514]">{{ number_format($test->passing_score, 0) }}%</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/60 dark:border-[#1D2E54] text-center">
                    <span class="block text-xs text-slate-500 dark:text-[#93A5C9] mb-1">Jumlah Pertanyaan</span>
                    <span class="text-base font-bold text-blue-600 dark:text-[#93F514]">{{ count($questions) > 0 ? count($questions) : ($test->total_questions ?: 'Sesuai Soal') }} Soal</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/60 dark:border-[#1D2E54] text-center">
                    <span class="block text-xs text-slate-500 dark:text-[#93A5C9] mb-1">Metode Urutan</span>
                    <span class="text-base font-bold text-slate-800 dark:text-slate-200">{{ $test->is_random ? 'Acak' : 'Urut' }}</span>
                </div>
            </div>

            <!-- Ketentuan Ujian -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A]/70 border border-slate-200 dark:border-[#1D2E54] text-xs text-slate-700 dark:text-slate-300 space-y-2">
                <h3 class="font-semibold text-xs text-slate-900 dark:text-white">Peraturan & Hal yang Perlu Diperhatikan:</h3>
                <ul class="list-disc list-inside space-y-1 text-slate-600 dark:text-slate-400 leading-relaxed">
                    <li>Pastikan koneksi internet Anda stabil sebelum menekan tombol <strong>Mulai Ujian</strong>.</li>
                    <li>Waktu akan langsung berjalan otomatis dan tidak dapat dijeda (pause).</li>
                    <li>Jawaban Anda otomatis tersimpan setiap kali Anda memilih opsi atau mengetik jawaban.</li>
                    <li>Jika butir soal memiliki <strong>file lampiran / studi kasus</strong>, tautan unduhan akan muncul di atas pertanyaan.</li>
                    <li>Ujian akan otomatis disubmit jika batas waktu pengerjaan telah habis.</li>
                </ul>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-[#1D2E54]">
                <a href="{{ route('profile', ['tab' => 'riwayat']) }}" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-[#93A5C9] hover:bg-slate-100 dark:hover:bg-[#14203A] rounded-xl transition">
                    Kembali ke Riwayat
                </a>
                <button type="button" 
                        wire:click="startTest" 
                        wire:loading.attr="disabled"
                        wire:target="startTest"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-60 disabled:cursor-not-allowed text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition">
                    <svg wire:loading wire:target="startTest" class="animate-spin w-4 h-4 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="startTest">Mulai Ujian</span>
                    <span wire:loading wire:target="startTest">Memulai Ujian...</span>
                    <svg wire:loading.remove wire:target="startTest" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- STATE 2: SEDANG MENGERJAKAN UJIAN (TAKING TEST) -->
    @if ($testState === 'taking')
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
            <!-- Left Side: Lembar Soal & Jawaban (3 Cols) -->
            <div class="lg:col-span-3 space-y-6">
                @if ($currentQuestion)
                    <div class="bg-white dark:bg-[#0D1527] rounded-3xl border border-slate-200/80 dark:border-[#1D2E54] shadow-sm p-6 sm:p-8 space-y-6">
                        <!-- Question Header -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#1D2E54]">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-blue-600 dark:bg-[#93F514] text-white dark:text-black font-black text-xs">
                                    Soal No. {{ $currentQuestionIndex + 1 }}
                                </span>
                                <span class="px-2.5 py-1 rounded-xl text-[11px] font-semibold bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-[#1D2E54]">
                                    @if ($currentQuestion['question_type'] === 'multiple_choice')
                                        Pilihan Ganda
                                    @elseif ($currentQuestion['question_type'] === 'disc')
                                        Pernyataan DISC
                                    @else
                                        Uraian / Essay
                                    @endif
                                </span>
                            </div>
                            <span class="text-xs font-bold text-emerald-600 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800">
                                Bobot: {{ $currentQuestion['points'] ?? 1 }} Poin
                            </span>
                        </div>

                        <!-- LAMPIRAN FILE ATAU GAMBAR SOAL (JIKA ADA) -->
                        @if (!empty($currentQuestion['image_path']))
                            @php
                                $filePath = $currentQuestion['image_path'];
                                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                $fileUrl = asset('storage/' . $filePath);
                            @endphp

                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Dokumen & Lampiran Studi Kasus Soal
                                    </span>
                                    <a href="{{ $fileUrl }}" target="_blank" download class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-xl text-xs font-bold shadow-xs transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Unduh / Buka Dokumen ({{ strtoupper($ext) }})</span>
                                    </a>
                                </div>

                                @if ($isImage)
                                    <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] p-2 text-center">
                                        <img src="{{ $fileUrl }}" alt="Lampiran Soal" class="max-h-80 mx-auto object-contain rounded-lg">
                                    </div>
                                @else
                                    <p class="text-[11px] text-slate-600 dark:text-[#93A5C9]">
                                        Silakan unduh atau buka dokumen di atas untuk membaca deskripsi lengkap studi kasus terkait soal ini.
                                    </p>
                                @endif
                            </div>
                        @endif

                        <!-- Pertanyaan Teks -->
                        <div class="text-sm sm:text-base font-medium text-slate-900 dark:text-white whitespace-pre-line leading-relaxed">
                            {{ $currentQuestion['question'] }}
                        </div>

                        <!-- OPSI PILIHAN GANDA -->
                        @if ($currentQuestion['question_type'] === 'multiple_choice')
                            @php
                                $labels = ['A', 'B', 'C', 'D', 'E'];
                                $selectedOpt = $answers[$currentQuestion['id']] ?? null;
                            @endphp
                            <div class="space-y-3 pt-2"
                                 wire:key="mc-box-{{ $currentQuestion['id'] }}"
                                 wire:ignore
                                 x-data="{ selected: {{ $selectedOpt ?? 'null' }} }">
                                @foreach ($currentQuestion['options'] as $idx => $opt)
                                    <label @click="selected = {{ $opt['id'] }}; $wire.saveAnswer({{ $currentQuestion['id'] }}, {{ $opt['id'] }})"
                                        class="flex items-center gap-3.5 p-4 rounded-2xl border cursor-pointer transition-all duration-200"
                                        :class="selected == {{ $opt['id'] }}
                                            ? 'bg-blue-50/80 dark:bg-[#14203A] border-blue-500 dark:border-[#93F514] ring-2 ring-blue-500/20 dark:ring-[#93F514]/20 text-slate-900 dark:text-white font-semibold'
                                            : 'bg-slate-50/50 dark:bg-[#14203A]/40 border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-slate-200 hover:bg-slate-100/80 dark:hover:bg-[#14203A]'">
                                        <input type="radio" name="opt_{{ $currentQuestion['id'] }}" value="{{ $opt['id'] }}" :checked="selected == {{ $opt['id'] }}" class="sr-only">
                                        <span class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0"
                                              :class="selected == {{ $opt['id'] }} ? 'bg-blue-600 dark:bg-[#93F514] text-white dark:text-black' : 'bg-slate-200 dark:bg-[#0D1527] text-slate-700 dark:text-slate-300'">
                                            {{ $labels[$idx] ?? ($idx + 1) }}
                                        </span>
                                        <span class="text-xs sm:text-sm flex-1 leading-snug">
                                            {{ $opt['option_text'] }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <!-- FORM ESSAY / URAIAN -->
                        @if ($currentQuestion['question_type'] === 'essay')
                            <div class="space-y-4 pt-2" wire:key="essay-question-{{ $currentQuestion['id'] }}">
                                <div class="space-y-2">
                                    <label for="essay_{{ $currentQuestion['id'] }}" class="block text-xs font-bold text-slate-700 dark:text-[#93A5C9]">
                                        Tuliskan Jawaban Uraian / Analisis Anda:
                                    </label>
                                    <textarea id="essay_{{ $currentQuestion['id'] }}"
                                        rows="6"
                                        wire:model.lazy="answers.{{ $currentQuestion['id'] }}"
                                        wire:change="submitEssayAnswer({{ $currentQuestion['id'] }})"
                                        placeholder="Ketikkan jawaban essay Anda secara lengkap dan terstruktur... (Jika menyertakan link video/Google Drive, cantumkan di sini)"
                                        class="w-full p-4 rounded-2xl bg-slate-50 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-xs sm:text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:outline-none transition leading-relaxed"></textarea>
                                    <div class="flex items-center justify-between text-[11px] text-slate-400 dark:text-[#93A5C9]">
                                        <span>Jawaban teks tersimpan otomatis saat Anda berpindah soal atau menekan tombol navigasi.</span>
                                    </div>
                                </div>

                                <!-- PERHATIAN UNTUK FILE BESAR / VIDEO VIA GOOGLE DRIVE -->
                                <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/95 dark:bg-amber-950/40 border-2 border-amber-300/80 dark:border-amber-700/60 shadow-sm flex items-start gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 dark:bg-amber-400/20 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 space-y-2 text-amber-950 dark:text-amber-100">
                                        <div class="text-sm sm:text-base font-bold flex items-center gap-2 text-amber-900 dark:text-amber-200">
                                            <span>Perhatian Khusus File Video / Ukuran Besar:</span>
                                        </div>
                                        <p class="text-xs sm:text-sm text-amber-900/90 dark:text-amber-200 leading-relaxed font-normal">
                                            Batas unggah langsung dokumen adalah <strong>10 MB</strong>. Jika jawaban Anda membutuhkan lampiran berukuran besar atau berbentuk <strong>Video</strong>, mohon unggah terlebih dahulu ke <strong>Google Drive / Cloud Storage</strong> dan cantumkan link tautannya pada kolom jawaban uraian di atas.
                                        </p>
                                        <div class="p-2.5 sm:p-3 rounded-xl bg-amber-100/80 dark:bg-amber-900/50 border border-amber-300/60 dark:border-amber-700/60 text-xs sm:text-sm text-amber-900 dark:text-amber-200 font-medium flex items-start gap-2.5">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span>Pastikan akses link Google Drive telah diatur ke <strong class="underline underline-offset-2">"Siapa saja yang memiliki link" (Anyone with the link)</strong> agar penguji dapat membukanya.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- UPLOAD FILE ATTACHMENT ESSAY (MAKS 10MB) -->
                                <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-[#14203A]/70 border border-slate-200 dark:border-[#1D2E54] space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 dark:text-white">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span>Lampiran File Pendukung (Opsional)</span>
                                        </div>
                                        <span class="text-[11px] font-medium text-slate-500 dark:text-[#93A5C9] bg-white dark:bg-[#0D1527] px-2.5 py-0.5 rounded-full border border-slate-200 dark:border-[#1D2E54]">
                                            Dokumen Pendukung - Maks. 10 MB
                                        </span>
                                    </div>

                                    @error('essayFiles.' . $currentQuestion['id'])
                                        <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-2">
                                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror

                                    @php
                                        $uploadedAttachment = $essayAttachments[$currentQuestion['id']] ?? null;
                                    @endphp

                                    @if ($uploadedAttachment)
                                        <!-- PREVIEW FILE YANG SUDAH DIUNGGAH -->
                                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-blue-50/70 dark:bg-[#14203A] border border-blue-200 dark:border-[#1D2E54] transition">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-10 h-10 rounded-xl bg-blue-600/10 dark:bg-[#1D2E54] text-blue-600 dark:text-[#93F514] flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div class="truncate">
                                                    <a href="{{ $uploadedAttachment['url'] }}" target="_blank" class="text-xs font-semibold text-blue-700 dark:text-[#93F514] hover:underline truncate block">
                                                        {{ $uploadedAttachment['name'] }}
                                                    </a>
                                                    <span class="text-[11px] text-slate-500 dark:text-[#93A5C9]">
                                                         @if(!empty($uploadedAttachment['size']))
                                                             {{ $uploadedAttachment['size'] >= 1048576 ? round($uploadedAttachment['size'] / 1048576, 2) . ' MB' : round($uploadedAttachment['size'] / 1024, 1) . ' KB' }} •
                                                         @endif
                                                         File Tersimpan
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0 ml-2">
                                                <a href="{{ $uploadedAttachment['url'] }}" target="_blank" download class="p-2 text-xs font-medium text-blue-600 dark:text-[#93F514] hover:bg-blue-100 dark:hover:bg-[#1D2E54] rounded-lg transition" title="Buka / Unduh File">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                                <button type="button" wire:click="removeEssayAttachment({{ $currentQuestion['id'] }})" wire:confirm="Apakah Anda yakin ingin menghapus lampiran file ini?" class="p-2 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition" title="Hapus File">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <!-- INPUT UPLOAD FILE BARU (AUTO SAVE) -->
                                        <div>
                                            <label class="flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed border-slate-300 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] hover:border-blue-400 dark:hover:border-[#93F514] cursor-pointer transition group">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-[#14203A] text-blue-600 dark:text-[#93F514] flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 truncate">
                                                    <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">
                                                        Pilih atau drag file dokumen/analisis (Maks. 10 MB)
                                                    </span>
                                                    <span class="block text-[10px] text-slate-400 dark:text-[#93A5C9]">File otomatis tersimpan setelah dipilih • Khusus video harap cantumkan link Google Drive</span>
                                                </div>
                                                <input type="file" wire:model="essayFiles.{{ $currentQuestion['id'] }}" class="sr-only">
                                            </label>
                                        </div>

                                        <!-- Livewire Uploading & Saving Indicator -->
                                        <div wire:loading wire:target="essayFiles.{{ $currentQuestion['id'] }}" class="p-3 rounded-xl bg-blue-50/70 dark:bg-[#14203A] border border-blue-200 dark:border-[#1D2E54] text-xs text-blue-700 dark:text-[#93F514] flex items-center gap-2.5">
                                            <svg class="animate-spin w-4 h-4 text-blue-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                            <span class="font-medium">Sedang mengunggah dan menyimpan file lampiran...</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- DISC QUESTION TYPE (LAYOUT HORIZONTAL DENGAN ATURAN: BARIS YANG SAMA TIDAK BISA DIKLIK SEBERANGNYA) -->
                        @if ($currentQuestion['question_type'] === 'disc')
                            @php
                                $mostOpt = $answers[$currentQuestion['id']]['most'] ?? null;
                                $leastOpt = $answers[$currentQuestion['id']]['least'] ?? null;
                            @endphp
                            <div class="space-y-4 pt-2" 
                                 wire:key="disc-box-{{ $currentQuestion['id'] }}"
                                 wire:ignore
                                 x-data="{
                                     discMost: {{ $mostOpt ?? 'null' }},
                                     discLeast: {{ $leastOpt ?? 'null' }},
                                     selectDisc(optId, type) {
                                         if (type === 'most' && this.discLeast == optId) return;
                                         if (type === 'least' && this.discMost == optId) return;
                                         if (type === 'most') this.discMost = optId;
                                         else this.discLeast = optId;
                                         $wire.saveAnswer({{ $currentQuestion['id'] }}, optId, null, type);
                                     }
                                 }">
                                <!-- Instruction Header -->
                                <div class="flex items-center justify-between text-xs font-semibold px-1 text-slate-600 dark:text-[#93A5C9]">
                                    <span>Pilihlah <strong>1 pernyataan P</strong> (Paling Menggambarkan) dan <strong>1 pernyataan K</strong> (Kurang Menggambarkan).</span>
                                    <div class="flex items-center gap-1.5 font-mono text-[11px]">
                                        <span class="px-2.5 py-0.5 rounded font-bold"
                                              :class="discMost ? 'bg-amber-400 text-amber-950' : 'bg-slate-100 dark:bg-[#14203A] text-slate-400 dark:text-[#93A5C9]'">
                                            P: <span x-text="discMost ? '✓' : '-'"></span>
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded font-bold"
                                              :class="discLeast ? 'bg-sky-400 text-sky-950' : 'bg-slate-100 dark:bg-[#14203A] text-slate-400 dark:text-[#93A5C9]'">
                                            K: <span x-text="discLeast ? '✓' : '-'"></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Custom DISC Table Form matching Screenshot -->
                                <div class="overflow-x-auto rounded-xl border-2 border-slate-900 dark:border-[#1D2E54] shadow-sm">
                                    <table class="w-full text-xs border-collapse text-left">
                                        <thead>
                                            <tr class="font-extrabold text-sm border-b-2 border-slate-900 dark:border-[#1D2E54] text-center">
                                                <th class="w-14 py-2.5 px-3 bg-slate-300 dark:bg-[#14203A] text-slate-900 dark:text-white border-r-2 border-slate-900 dark:border-[#1D2E54]">No.</th>
                                                <th class="w-14 py-2.5 px-3 bg-amber-400 text-slate-900 border-r-2 border-slate-900 dark:border-[#1D2E54]">P</th>
                                                <th class="w-14 py-2.5 px-3 bg-sky-400 text-slate-900 border-r-2 border-slate-900 dark:border-[#1D2E54]">K</th>
                                                <th class="py-2.5 px-4 bg-slate-800 dark:bg-[#070B14] text-white font-bold text-left tracking-wide">Gambaran Diri</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y-2 divide-slate-900 dark:divide-[#1D2E54] font-medium text-xs">
                                            @foreach ($currentQuestion['options'] as $idx => $opt)
                                                <tr class="hover:bg-slate-50 dark:hover:bg-[#14203A]/60 transition"
                                                    :class="{
                                                        'bg-amber-50/40 dark:bg-amber-950/20': discMost == {{ $opt['id'] }},
                                                        'bg-sky-50/40 dark:bg-sky-950/20': discLeast == {{ $opt['id'] }}
                                                    }"
                                                    wire:key="opt-row-{{ $currentQuestion['id'] }}-{{ $opt['id'] }}">
                                                    @if ($idx === 0)
                                                        <td rowspan="{{ count($currentQuestion['options']) }}" class="text-center font-black text-lg bg-slate-200 dark:bg-[#14203A] text-slate-900 dark:text-white border-r-2 border-slate-900 dark:border-[#1D2E54]">
                                                            {{ $currentQuestionIndex + 1 }}
                                                        </td>
                                                    @endif

                                                    <!-- Kolom P (Kuning / Amber) -->
                                                    <td class="text-center border-r-2 border-slate-900 dark:border-[#1D2E54] p-0"
                                                        :class="discLeast == {{ $opt['id'] }} ? 'bg-slate-200 dark:bg-[#0D1527] opacity-40 cursor-not-allowed' : 'bg-amber-400/90'">
                                                        <label class="w-full h-11 flex items-center justify-center"
                                                               :class="discLeast == {{ $opt['id'] }} ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-amber-500/80 transition'">
                                                            <input type="radio" 
                                                                   name="disc_most_{{ $currentQuestion['id'] }}" 
                                                                   value="{{ $opt['id'] }}" 
                                                                   :checked="discMost == {{ $opt['id'] }}"
                                                                   :disabled="discLeast == {{ $opt['id'] }}"
                                                                   @click="selectDisc({{ $opt['id'] }}, 'most')"
                                                                   class="w-5 h-5 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 border-2 border-slate-900"
                                                                   :class="discLeast == {{ $opt['id'] }} ? 'cursor-not-allowed opacity-30' : 'cursor-pointer'">
                                                        </label>
                                                    </td>

                                                    <!-- Kolom K (Biru / Sky) -->
                                                    <td class="text-center border-r-2 border-slate-900 dark:border-[#1D2E54] p-0"
                                                        :class="discMost == {{ $opt['id'] }} ? 'bg-slate-200 dark:bg-[#0D1527] opacity-40 cursor-not-allowed' : 'bg-sky-400/90'">
                                                        <label class="w-full h-11 flex items-center justify-center"
                                                               :class="discMost == {{ $opt['id'] }} ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-sky-500/80 transition'">
                                                            <input type="radio" 
                                                                   name="disc_least_{{ $currentQuestion['id'] }}" 
                                                                   value="{{ $opt['id'] }}" 
                                                                   :checked="discLeast == {{ $opt['id'] }}"
                                                                   :disabled="discMost == {{ $opt['id'] }}"
                                                                   @click="selectDisc({{ $opt['id'] }}, 'least')"
                                                                   class="w-5 h-5 text-rose-600 focus:ring-rose-500 focus:ring-offset-0 border-2 border-slate-900"
                                                                   :class="discMost == {{ $opt['id'] }} ? 'cursor-not-allowed opacity-30' : 'cursor-pointer'">
                                                        </label>
                                                    </td>

                                                    <!-- Kolom Teks Gambaran Diri -->
                                                    <td class="py-2.5 px-4 text-slate-900 dark:text-slate-100 font-semibold bg-white dark:bg-[#0D1527] text-xs sm:text-sm">
                                                        {{ $opt['option_text'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Navigation Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t border-slate-100 dark:border-[#1D2E54] gap-3">
                            <button type="button" wire:click="prevQuestion" {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 dark:border-[#1D2E54] bg-white dark:bg-[#14203A] text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#1A2A4C] disabled:opacity-40 disabled:cursor-not-allowed transition">
                                <svg class="w-4 h-4 text-slate-500 dark:text-[#93A5C9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                <span>Sebelumnya</span>
                            </button>

                            @if ($currentQuestionIndex < count($questions) - 1)
                                <button type="button" wire:click="nextQuestion"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition shadow-xs">
                                    <span>Selanjutnya</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @else
                                <button type="button" @click="showConfirmModal = true"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Kirim Jawaban & Selesai</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Side: Nomor Soal Grid & Submit Button (1 Col) -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white dark:bg-[#0D1527] rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] p-5 space-y-4 sticky top-24 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-[#1D2E54]">
                        <h3 class="text-xs font-semibold text-slate-900 dark:text-white uppercase tracking-wider">
                            Navigasi Soal
                        </h3>
                        <span class="text-xs font-medium text-slate-500 dark:text-[#93A5C9]">
                            {{ $completedCount }} / {{ $totalQuestions }} Terisi
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-[#93A5C9]">
                            <span>Kelengkapan</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $progressPercent }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 dark:bg-[#14203A] rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-600 dark:bg-[#93F514] transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>

                    <!-- Number Grid -->
                    <div class="grid grid-cols-5 gap-1.5">
                        @foreach ($questions as $idx => $q)
                            @php
                                $status = 'unanswered';
                                if ($q['question_type'] === 'disc') {
                                    $hasMost = !empty($answers[$q['id']]['most']);
                                    $hasLeast = !empty($answers[$q['id']]['least']);
                                    if ($hasMost && $hasLeast) {
                                        $status = 'completed';
                                    } elseif ($hasMost || $hasLeast) {
                                        $status = 'partial';
                                    }
                                } elseif ($q['question_type'] === 'multiple_choice') {
                                    $status = (!empty($answers[$q['id']])) ? 'completed' : 'unanswered';
                                } elseif ($q['question_type'] === 'essay') {
                                    $hasText = !empty($answers[$q['id']]) && trim($answers[$q['id']]) !== '';
                                    $hasAttachment = !empty($essayAttachments[$q['id']]);
                                    $status = ($hasText || $hasAttachment) ? 'completed' : 'unanswered';
                                }
                                $isCurrent = ($currentQuestionIndex === $idx);
                            @endphp
                            <button type="button" wire:click="selectQuestion({{ $idx }})"
                                class="h-8 rounded-lg text-xs font-medium flex items-center justify-center transition border {{ $isCurrent ? 'ring-2 ring-blue-500 dark:ring-[#93F514] ring-offset-1 border-blue-500 dark:border-[#93F514]' : '' }} {{ $status === 'completed' ? 'bg-emerald-600 text-white border-emerald-600 dark:bg-[#93F514] dark:text-black dark:border-[#93F514]' : ($status === 'partial' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-700 font-semibold' : 'bg-slate-50 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#1A2A4C]') }}"
                                title="Soal No. {{ $idx + 1 }}">
                                {{ $idx + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Status Legend -->
                    <div class="pt-3 border-t border-slate-100 dark:border-[#1D2E54] space-y-1.5 text-[11px] text-slate-500 dark:text-[#93A5C9]">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 dark:bg-[#93F514] shrink-0"></span>
                            <span>Sudah Lengkap {{ collect($questions)->contains('question_type', 'disc') ? '(P & K)' : '' }}</span>
                        </div>
                        @if (collect($questions)->contains('question_type', 'disc'))
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
                                <span>Kurang P atau K</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-600 shrink-0"></span>
                            <span>Belum Dijawab</span>
                        </div>
                    </div>

                    @if ($completedCount < $totalQuestions)
                        <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 text-[11px] text-amber-800 dark:text-amber-300">
                            Masih ada {{ $totalQuestions - $completedCount }} butir soal yang belum lengkap.
                        </div>
                    @endif

                    <button type="button" @click="showConfirmModal = true"
                        class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 mt-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Selesaikan Ujian</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Confirmation Modal (Replacing Browser Native Confirm) -->
        <div x-show="showConfirmModal" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" 
             x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" 
                     @click="showConfirmModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Dialog Panel -->
                <div x-show="showConfirmModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200 dark:border-[#1D2E54] p-6 space-y-4">
                    
                    @if ($completedCount === $totalQuestions)
                        <!-- State 1: Semua Soal Sudah Terisi Lengkap -->
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-600 dark:text-[#93F514] shrink-0 border border-emerald-200 dark:border-emerald-800/80">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                    Selesaikan Ujian?
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 leading-relaxed">
                                    Seluruh <strong>{{ $totalQuestions }} butir soal</strong> telah terisi. Setelah dikirim, jawaban tidak dapat diubah kembali.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-[#1D2E54]">
                            <button type="button" 
                                    @click="showConfirmModal = false" 
                                    class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                                Batal
                            </button>
                            <button type="button" 
                                    wire:click="finishTest" 
                                    @click="showConfirmModal = false" 
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-xl transition shadow-sm">
                                <span wire:loading.remove wire:target="finishTest">Ya, Selesaikan</span>
                                <span wire:loading wire:target="finishTest">Menyimpan...</span>
                            </button>
                        </div>
                    @else
                        <!-- State 2: Ada Soal Belum Lengkap -->
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0 border border-amber-200 dark:border-amber-800/80">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                    Jawaban Belum Lengkap
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-[#93A5C9] mt-1 leading-relaxed">
                                    Masih terdapat <strong>{{ $totalQuestions - $completedCount }} butir soal</strong> yang belum lengkap / belum dijawab. Seluruh butir soal wajib terisi sebelum menyelesaikan ujian ini.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-[#1D2E54]">
                            <button type="button" 
                                    @click="showConfirmModal = false; $wire.finishTest()" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition shadow-xs">
                                <span>Lengkapi Jawaban Sekarang</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
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

        <div class="max-w-2xl mx-auto bg-white dark:bg-[#0D1527] rounded-3xl border border-slate-200/80 dark:border-[#1D2E54] shadow-sm p-6 sm:p-10 text-center space-y-6">
            <div class="w-16 h-16 rounded-3xl {{ $isWaitingReview ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-[#93F514]' }} flex items-center justify-center mx-auto shadow-md">
                @if ($isWaitingReview)
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </div>

            <div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white">
                    {{ $isWaitingReview ? 'Jawaban Ujian Berhasil Terkirim!' : 'Ujian Berhasil Diselesaikan!' }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1">
                    Terima kasih telah menyelesaikan ujian untuk posisi <span class="font-bold text-slate-800 dark:text-white">{{ $application->job->title }}</span>.
                </p>
            </div>

            @if ($isWaitingReview)
                <!-- Banner Khusus Menunggu Review Essay -->
                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 text-left space-y-2">
                    <div class="flex items-center gap-2 text-amber-800 dark:text-amber-200 font-bold text-xs">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Menunggu Evaluasi & Penilaian dari HR / Penguji</span>
                    </div>
                    <p class="text-[11px] text-amber-700 dark:text-amber-300 leading-relaxed">
                        Ujian ini memuat soal berbentuk <strong>Uraian / Essay</strong>. Jawaban Anda telah tersimpan dengan aman dan saat ini sedang dalam antrean pemeriksaan oleh tim penilai rekruter.
                    </p>
                </div>
            @endif

            @if ($discResult || str_contains(strtolower($test->category?->name ?? ''), 'disc'))
                <!-- Khusus Tes DISC: Sembunyikan detail hasil dari pelamar, hanya notifikasi tersimpan -->
                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-center space-y-2">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-[#93F514] mb-1">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Jawaban Anda Telah Tersimpan</h3>
                    <p class="text-xs text-slate-600 dark:text-[#93A5C9] max-w-md mx-auto leading-relaxed">
                        Terima kasih telah menyelesaikan inventori kepribadian DISC. Seluruh jawaban Anda telah tersimpan dengan aman dan akan dievaluasi langsung oleh Tim HR / Tim Rekruter.
                    </p>
                </div>
            @else
                <!-- Score Summary Card untuk Tes Non-DISC (Pilihan Ganda & Essay) -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-[#14203A] border border-slate-100 dark:border-[#1D2E54] space-y-3">
                    <div class="flex items-center justify-between text-xs text-slate-600 dark:text-[#93A5C9]">
                        <span>Status Pengerjaan:</span>
                        @if ($isWaitingReview)
                            <span class="font-bold px-3 py-1 rounded-full text-[11px] bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                Menunggu Review Essay
                            </span>
                        @elseif ($attempt && $attempt->status === 'passed')
                            <span class="font-bold px-3 py-1 rounded-full text-[11px] bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800">
                                Lulus (Passed)
                            </span>
                        @elseif ($attempt && $attempt->status === 'failed')
                            <span class="font-bold px-3 py-1 rounded-full text-[11px] bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                Belum Lolos KKM
                            </span>
                        @else
                            <span class="font-bold px-3 py-1 rounded-full text-[11px] bg-slate-200 text-slate-700 dark:bg-[#0D1527] dark:text-[#93A5C9] border border-slate-300 dark:border-[#1D2E54]">
                                Selesai (Completed)
                            </span>
                        @endif
                    </div>

                    @if ($hasMultipleChoice)
                        <div class="flex items-center justify-between text-xs text-slate-600 dark:text-[#93A5C9]">
                            <span>Skor Pilihan Ganda:</span>
                            <span class="font-extrabold text-blue-600 dark:text-[#93F514] text-sm">
                                {{ number_format($attempt->objective_score ?? 0, 1) }} Poin
                            </span>
                        </div>
                    @endif

                    @if ($hasEssayQuestions)
                        <div class="flex items-center justify-between text-xs text-slate-600 dark:text-[#93A5C9]">
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

                    <div class="pt-2 border-t border-slate-200 dark:border-[#1D2E54] flex items-center justify-between">
                        <span class="font-bold text-xs text-slate-900 dark:text-white">Total Skor Akhir:</span>
                        @if ($isWaitingReview)
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2.5 py-1 rounded-lg border border-amber-200 dark:border-amber-800">
                                Menunggu Penilaian Essay
                            </span>
                        @else
                            <span class="text-xl font-black text-slate-900 dark:text-white">
                                {{ number_format($attempt->total_score ?? 0, 1) }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <p class="text-xs text-slate-400 dark:text-[#93A5C9] leading-relaxed pt-4">
                Anda dapat memantau perkembangan nilai dan tahapan seleksi selanjutnya pada menu <strong>Riwayat Lamaran</strong>.
            </p>

            <div class="pt-4">
                <a href="{{ route('profile', ['tab' => 'riwayat']) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Riwayat Lamaran</span>
                </a>
            </div>
        </div>
    @endif

</div>
