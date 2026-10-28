<div class="space-y-8" 
    x-data="{
        self_concept: @js($self_concept),
        health: @js($health),
        other: @js($other),
        errorMessage: '',

        addStrength() {
            this.self_concept.strengths.push('');
        },

        removeStrength(index) {
            if (this.self_concept.strengths.length > 3) {
                this.self_concept.strengths.splice(index, 1);
            } else {
                this.self_concept.strengths[index] = '';
            }
        },

        addWeakness() {
            this.self_concept.weaknesses.push('');
        },

        removeWeakness(index) {
            if (this.self_concept.weaknesses.length > 3) {
                this.self_concept.weaknesses.splice(index, 1);
            } else {
                this.self_concept.weaknesses[index] = '';
            }
        },

        setHealth(key, isYes) {
            this.health[key].is_yes = isYes;
            if (!isYes) {
                this.health[key].detail = '';
            }
            this.errorMessage = '';
        },

        setOther(key, isYes) {
            this.other[key].is_yes = isYes;
            if (!isYes) {
                this.other[key].detail = '';
            }
            this.errorMessage = '';
        },

        submitForm() {
            this.errorMessage = '';

            // 0. Validasi Konsep Diri (minimal 1 kelebihan dan 1 kekurangan diisi)
            const cleanStrengths = (this.self_concept.strengths || []).filter(s => s && s.trim());
            const cleanWeaknesses = (this.self_concept.weaknesses || []).filter(w => w && w.trim());
            if (cleanStrengths.length < 1 || cleanWeaknesses.length < 1) {
                this.errorMessage = @js(__('Mohon isi minimal 1 poin Kelebihan dan 1 poin Kekurangan pada Konsep Diri.'));
                this.scrollToAlert();
                return;
            }

            // 1. Validasi Kesehatan (7 pertanyaan wajib dijawab)
            for (const [k, v] of Object.entries(this.health)) {
                if (v.is_yes === null || v.is_yes === undefined) {
                    this.errorMessage = @js(__('Semua pertanyaan Kesehatan wajib dijawab (pilih IYA atau TIDAK).'));
                    this.scrollToAlert();
                    return;
                }
                if (v.is_yes === true && (!v.detail || !v.detail.trim())) {
                    this.errorMessage = @js(__('Mohon isi rincian/penjelasan untuk pertanyaan Kesehatan yang dijawab IYA.'));
                    this.scrollToAlert();
                    return;
                }
            }

            // 2. Validasi Kesiapan Kerja (Siap / Tidak Siap)
            const readyKeys = ['business_trip_ready', 'mutation_position_ready', 'mutation_group_ready', 'relocation_city_ready'];
            for (const rk of readyKeys) {
                if (!this.other[rk]) {
                    this.errorMessage = @js(__('Seluruh opsi Kesiapan Perjalanan Dinas & Penempatan wajib dipilih (Siap atau Tidak Siap).'));
                    this.scrollToAlert();
                    return;
                }
            }

            // 3. Validasi Pertanyaan IYA/TIDAK pada Informasi Lain
            const yesNoKeys = ['crime_involvement', 'custody', 'other_applications', 'internal_relations', 'previously_applied'];
            for (const yk of yesNoKeys) {
                if (this.other[yk].is_yes === null || this.other[yk].is_yes === undefined) {
                    this.errorMessage = @js(__('Seluruh pertanyaan pilihan IYA/TIDAK pada Informasi Lain wajib dijawab.'));
                    this.scrollToAlert();
                    return;
                }
                if (this.other[yk].is_yes === true && (!this.other[yk].detail || !this.other[yk].detail.trim())) {
                    this.errorMessage = @js(__('Mohon isi penjelasan untuk butir Informasi Lain yang dijawab IYA.'));
                    this.scrollToAlert();
                    return;
                }
            }

            // 4. Validasi Isian Teks Informasi Lain
            const textFields = [
                { k: 'free_time_activity', label: @js(__('Aktivitas waktu luang/libur')) },
                { k: 'reason_applying_company', label: @js(__('Alasan melamar di perusahaan ini')) },
                { k: 'reason_applying_position', label: @js(__('Alasan melamar posisi ini')) },
                { k: 'vacancy_source', label: @js(__('Darimana info lowongan didapat')) },
                { k: 'ready_to_work', label: @js(__('Kapan Anda siap bekerja')) },
                { k: 'expected_salary', label: @js(__('Gaji yang Anda inginkan')) },
                { k: 'expected_benefits', label: @js(__('Fasilitas/tunjangan yang diinginkan')) },
            ];
            for (const tf of textFields) {
                if (!this.other[tf.k] || !this.other[tf.k].trim()) {
                    this.errorMessage = (@js(__('Kolom :field wajib diisi.'))).replace(':field', tf.label);
                    this.scrollToAlert();
                    return;
                }
            }

            // 5. Validasi Pernyataan Kebenaran Data Pelamar
            if (!this.other.statement_agreed) {
                this.errorMessage = @js(__('Anda wajib mencentang persetujuan pernyataan kebenaran data pelamar sebelum menyimpan.'));
                this.scrollToAlert();
                return;
            }

            $wire.save(this.health, this.other, this.self_concept);
        },

        scrollToAlert() {
            this.$nextTick(() => {
                const el = document.getElementById('validation-banner-bottom');
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
    }"
    x-on:salary-updated.window="
        if ($event.detail && $event.detail.formatted) {
            let val = $event.detail.formatted;
            let existing = (other.expected_salary || '').trim();
            if (!existing || /^Rp\s*[\d\.]+\s*$/i.test(existing) || /^\d+$/.test(existing)) {
                other.expected_salary = val;
            } else {
                let replaced = existing.replace(/(?:rp\.?\s*)?(\d{1,3}(?:\.\d{3})+|\d{4,})/i, val);
                if (replaced !== existing) {
                    other.expected_salary = replaced;
                } else {
                    other.expected_salary = val + ' ' + existing;
                }
            }
        }
    ">

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
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-[#93F514] border border-emerald-200/80 dark:border-[#93F514]/30">{{ __('Wajib Dilengkapi') }}</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">{{ __('* Lengkapi riwayat kesehatan fisik dan informasi kesiapan kerja Anda untuk keperluan verifikasi berkas dan wawancara.') }}</p>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Validasi Atas -->
    <div x-show="errorMessage" x-cloak x-transition
        class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/50 border border-amber-300 dark:border-amber-800 text-amber-800 dark:text-amber-300 flex items-center justify-between gap-3 text-xs font-semibold shadow-sm">
        <div class="flex items-center gap-2.5">
            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span x-text="errorMessage"></span>
        </div>
        <button type="button" @click="errorMessage = ''" class="text-amber-500 hover:text-amber-700 font-bold text-base px-2">
            &times;
        </button>
    </div>

    <form @submit.prevent="submitForm()" class="space-y-8">

        <!-- ========================================== -->
        <!-- F. KONSEP DIRI (Kelebihan & Kekurangan)   -->
        <!-- ========================================== -->
        <div class="bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] overflow-hidden">
            <div class="p-5 md:p-6 border-b border-slate-200/80 dark:border-[#1D2E54] flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-200/60 dark:border-indigo-900/40">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span>{{ __('Konsep Diri') }}</span>
                            {{-- <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200/80 dark:border-indigo-800/60">{{ __('F. Konsep Diri') }}</span> --}}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-[#93A5C9]">{{ __('Tuliskan kelebihan utama yang Anda miliki serta kekurangan yang sedang Anda kelola atau kembangkan.') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-5 md:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Kolom Kelebihan -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/40 dark:bg-emerald-950/10 border border-emerald-200/70 dark:border-emerald-800/40 space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-emerald-200/60 dark:border-emerald-800/40">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-emerald-900 dark:text-emerald-300">
                                    {{ __('Kelebihan (Strengths)') }}
                                </h4>
                            </div>
                            <span class="text-[11px] text-emerald-700/80 dark:text-emerald-400 font-medium">{{ __('Min. 1 Poin') }}</span>
                        </div>

                        <p class="text-[11.5px] text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ __('Contoh: Mampu beradaptasi cepat dengan lingkungan baru, komunikatif, teliti, disiplin waktu, dll.') }}
                        </p>

                        <div class="space-y-3">
                            <template x-for="(item, index) in self_concept.strengths" :key="'strength-' + index">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-6 text-center text-xs font-bold text-slate-400 dark:text-slate-500" x-text="(index + 1) + '.'"></span>
                                    <input type="text" 
                                           x-model="self_concept.strengths[index]" 
                                           :placeholder="'{{ __('Tuliskan kelebihan ke-') }}' + (index + 1)"
                                           class="flex-1 px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] focus:border-transparent placeholder-slate-400 transition" />
                                    <button type="button" 
                                            x-show="self_concept.strengths.length > 3" 
                                            @click="removeStrength(index)" 
                                            class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition cursor-pointer" 
                                            title="Hapus baris">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="pt-1">
                            <button type="button" @click="addStrength()" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100/60 dark:hover:bg-emerald-900/40 rounded-lg border border-dashed border-emerald-300 dark:border-emerald-800 transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>{{ __('Tambah Baris Kelebihan') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Kolom Kekurangan -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/40 dark:bg-amber-950/10 border border-amber-200/70 dark:border-amber-800/40 space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-amber-200/60 dark:border-amber-800/40">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-amber-900 dark:text-amber-300">
                                    {{ __('Kekurangan (Weaknesses)') }}
                                </h4>
                            </div>
                            <span class="text-[11px] text-amber-700/80 dark:text-amber-400 font-medium">{{ __('Min. 1 Poin') }}</span>
                        </div>

                        <p class="text-[11.5px] text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ __('Contoh: Kurang percaya diri di depan publik (sedang latihan aktif), pelupa (mengatasinya dengan to-do list), dll.') }}
                        </p>

                        <div class="space-y-3">
                            <template x-for="(item, index) in self_concept.weaknesses" :key="'weakness-' + index">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-6 text-center text-xs font-bold text-slate-400 dark:text-slate-500" x-text="(index + 1) + '.'"></span>
                                    <input type="text" 
                                           x-model="self_concept.weaknesses[index]" 
                                           :placeholder="'{{ __('Tuliskan kekurangan ke-') }}' + (index + 1)"
                                           class="flex-1 px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 focus:border-transparent placeholder-slate-400 transition" />
                                    <button type="button" 
                                            x-show="self_concept.weaknesses.length > 3" 
                                            @click="removeWeakness(index)" 
                                            class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition cursor-pointer" 
                                            title="Hapus baris">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="pt-1">
                            <button type="button" @click="addWeakness()" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-amber-700 dark:text-amber-300 hover:bg-amber-100/60 dark:hover:bg-amber-900/40 rounded-lg border border-dashed border-amber-300 dark:border-amber-800 transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>{{ __('Tambah Baris Kekurangan') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- KESEHATAN -->
        <!-- ========================================== -->
        <div class="bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] overflow-hidden">
            <div class="p-5 md:p-6 border-b border-slate-200/80 dark:border-[#1D2E54] flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-200/60 dark:border-rose-900/40">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span>{{ __('Kesehatan') }}</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-[#93A5C9]">{{ __('Pilih IYA / TIDAK secara jujur. Jika memilih IYA, sebutkan penjelasan detailnya.') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-5 md:p-6 divide-y divide-slate-100 dark:divide-[#1D2E54]/60">
                @php
                    $healthQuestions = [
                        'smoking' => [
                            'q' => __('Apakah saudara merokok?'),
                            'placeholder' => __('Jika IYA, berapa batang perhari? (Contoh: 10 batang / 1 bungkus sehari)'),
                            'label' => __('Berapa batang perhari?'),
                        ],
                        'alcohol' => [
                            'q' => __('Apakah saudara minum alcohol?'),
                            'placeholder' => __('Jika IYA, seberapa sering? (Contoh: Jarang, hanya saat acara sosial tertentu)'),
                            'label' => __('Seberapa sering?'),
                        ],
                        'hospitalized' => [
                            'q' => __('Apakah anda pernah masuk Rumah Sakit (INAP)?'),
                            'placeholder' => __('Jika IYA, Kapan? Mengapa bisa INAP? (Contoh: Tahun 2022, Demam Berdarah selama 5 hari)'),
                            'label' => __('Kapan & Mengapa INAP?'),
                        ],
                        'allergy' => [
                            'q' => __('Apakah anda memiliki riwayat alergi?'),
                            'placeholder' => __('Jika IYA, sebut dan Jelaskan! (Contoh: Alergi obat antibiotik jenis tertentu / debu / makanan laut)'),
                            'label' => __('Sebut & Jelaskan Alergi'),
                        ],
                        'ongoing_treatment' => [
                            'q' => __('Apakah anda sedang melakukan pengobatan?'),
                            'placeholder' => __('Jika IYA, Jelaskan! (Contoh: Sedang dalam terapi vitamin atau pengobatan rutin dokter)'),
                            'label' => __('Jelaskan Pengobatan'),
                        ],
                        'infectious_disease' => [
                            'q' => __('Apakah anda memiliki penyakit menular?'),
                            'placeholder' => __('Jika IYA, Sebut dan Jelaskan! (Contoh: Riwayat TBC yang telah sembuh total / lainnya)'),
                            'label' => __('Sebut & Jelaskan Penyakit Menular'),
                        ],
                        'disease_history' => [
                            'q' => __('Apakah anda memiliki riwayat penyakit?'),
                            'placeholder' => __('Jika IYA, Sebut dan Jelaskan! (Contoh: Riwayat maag/asam lambung, patah tulang masa lalu, dll)'),
                            'label' => __('Sebut & Jelaskan Riwayat Penyakit'),
                        ],
                    ];
                @endphp

                @foreach ($healthQuestions as $key => $item)
                    <div class="py-4 first:pt-0 last:pb-0 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-2.5 flex-1">
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 leading-snug">
                                    {{ $item['q'] }} <span class="text-rose-500">*</span>
                                </span>
                            </div>

                            {{-- Pilihan IYA / TIDAK (Default Unselected / Belum Diklik) --}}
                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                <button type="button" @click="setHealth('{{ $key }}', false)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="health['{{ $key }}'].is_yes === false 
                                        ? 'bg-slate-900 text-white border-slate-900 dark:bg-[#1D2E54] dark:text-white dark:border-[#2D457C] shadow-xs' 
                                        : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-200 dark:hover:bg-[#1D2E54]'">
                                    <svg x-show="health['{{ $key }}'].is_yes === false" class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('TIDAK') }}</span>
                                </button>

                                <button type="button" @click="setHealth('{{ $key }}', true)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="health['{{ $key }}'].is_yes === true 
                                        ? 'bg-rose-600 text-white border-rose-600 shadow-md shadow-rose-600/20' 
                                        : 'bg-slate-100 dark:bg-[#14203A] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-200 dark:hover:bg-[#1D2E54]'">
                                    <svg x-show="health['{{ $key }}'].is_yes === true" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('IYA') }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- Input Penjelasan Jika IYA (Hanya Muncul Jika IYA Dipilih) --}}
                        <div x-show="health['{{ $key }}'].is_yes === true" x-cloak x-transition.duration.150ms
                            class="mt-2.5 p-3.5 rounded-xl bg-rose-50/50 dark:bg-[#14203A] border border-rose-200/70 dark:border-[#1D2E54] space-y-1.5">
                            <label class="block text-[11px] font-bold text-rose-800 dark:text-rose-300">
                                {{ $item['label'] }} <span class="text-rose-500">*</span>
                            </label>
                            <textarea x-model="health['{{ $key }}'].detail" rows="2"
                                placeholder="{{ $item['placeholder'] }}"
                                class="w-full px-3.5 py-2 rounded-lg border border-rose-300/80 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-rose-500 dark:focus:ring-rose-400 focus:border-transparent transition"></textarea>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================== -->
        <!-- INFORMASI LAIN -->
        <!-- ========================================== -->
        <div class="bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] overflow-hidden">
            <div class="p-5 md:p-6 border-b border-slate-200/80 dark:border-[#1D2E54] flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-200/60 dark:border-blue-900/40">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span>{{ __('Informasi Lain') }}</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-[#93A5C9]">{{ __('Informasi seputar kesiapan kerja, integritas, dan motivasi berkarir.') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-5 md:p-6 space-y-8">

                <!-- 1. KELOMPOK KESIAPAN PENEMPATAN & MUTASI -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>{{ __('Kesiapan Perjalanan Dinas & Penempatan') }}</span>
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        {{-- Dinas --}}
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] flex flex-col justify-between gap-3">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ __('Apakah anda siap melakukan perjalanan dinas?') }} <span class="text-rose-500">*</span>
                            </span>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="other.business_trip_ready = 'siap'; errorMessage = ''"
                                    class="flex-1 text-center py-2 px-3 rounded-lg border text-xs font-bold transition flex items-center justify-center gap-1.5"
                                    :class="other.business_trip_ready === 'siap' 
                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.business_trip_ready === 'siap'" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('Siap') }}</span>
                                </button>
                                <button type="button" @click="other.business_trip_ready = 'tidak'; errorMessage = ''"
                                    class="flex-1 text-center py-2 px-3 rounded-lg border text-xs font-bold transition flex items-center justify-center gap-1.5"
                                    :class="other.business_trip_ready === 'tidak' 
                                        ? 'bg-slate-700 text-white border-slate-700 shadow-sm' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.business_trip_ready === 'tidak'" class="w-3.5 h-3.5 text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('Tidak Siap') }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- Mutasi Posisi --}}
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] flex flex-col justify-between gap-3">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ __('Apakah anda siap dimutasi atau dipindahkan bagian atau posisi?') }} <span class="text-rose-500">*</span>
                            </span>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="other.mutation_position_ready = 'siap'; errorMessage = ''"
                                    class="flex-1 text-center py-2 px-3 rounded-lg border text-xs font-bold transition flex items-center justify-center gap-1.5"
                                    :class="other.mutation_position_ready === 'siap' 
                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.mutation_position_ready === 'siap'" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('Siap') }}</span>
                                </button>
                                <button type="button" @click="other.mutation_position_ready = 'tidak'; errorMessage = ''"
                                    class="flex-1 text-center py-2 px-3 rounded-lg border text-xs font-bold transition flex items-center justify-center gap-1.5"
                                    :class="other.mutation_position_ready === 'tidak' 
                                        ? 'bg-slate-700 text-white border-slate-700 shadow-sm' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.mutation_position_ready === 'tidak'" class="w-3.5 h-3.5 text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('Tidak Siap') }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- Mutasi Group --}}
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] flex flex-col justify-between gap-3">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ __('Apakah anda siap dimutasi ke Group Perusahaan?') }} <span class="text-rose-500">*</span>
                            </span>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="other.mutation_group_ready = 'siap'; errorMessage = ''"
                                    class="flex-1 text-center py-2 px-3 rounded-lg border text-xs font-bold transition flex items-center justify-center gap-1.5"
                                    :class="other.mutation_group_ready === 'siap' 
                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.mutation_group_ready === 'siap'" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('Siap') }}</span>
                                </button>
                                <button type="button" @click="other.mutation_group_ready = 'tidak'; errorMessage = ''"
                                    class="flex-1 text-center py-2 px-3 rounded-lg border text-xs font-bold transition flex items-center justify-center gap-1.5"
                                    :class="other.mutation_group_ready === 'tidak' 
                                        ? 'bg-slate-700 text-white border-slate-700 shadow-sm' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.mutation_group_ready === 'tidak'" class="w-3.5 h-3.5 text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('Tidak Siap') }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- Kota Lain --}}
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] flex flex-col justify-between gap-3">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ __('Apakah anda siap ditempatkan di Kota lain?') }} <span class="text-rose-500">*</span>
                            </span>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="other.relocation_city_ready = 'siap'; errorMessage = ''"
                                    class="flex-1 text-center py-2 px-3 rounded-lg border text-xs font-bold transition flex items-center justify-center gap-1.5"
                                    :class="other.relocation_city_ready === 'siap' 
                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.relocation_city_ready === 'siap'" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('Siap') }}</span>
                                </button>
                                <button type="button" @click="other.relocation_city_ready = 'tidak'; errorMessage = ''"
                                    class="flex-1 text-center py-2 px-3 rounded-lg border text-xs font-bold transition flex items-center justify-center gap-1.5"
                                    :class="other.relocation_city_ready === 'tidak' 
                                        ? 'bg-slate-700 text-white border-slate-700 shadow-sm' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.relocation_city_ready === 'tidak'" class="w-3.5 h-3.5 text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('Tidak Siap') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. KELOMPOK INTEGRITAS & LATAR BELAKANG -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-[#1D2E54]/60">
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>{{ __('Integritas & Latar Belakang') }}</span>
                    </h4>

                    {{-- Waktu Luang --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1.5">
                            {{ __('Bagaimana cara anda menggunakan waktu luang/libur?') }} <span class="text-rose-500">*</span>
                        </label>
                        <textarea x-model="other.free_time_activity" rows="2"
                            placeholder="{{ __('Tuliskan aktivitas atau hobi yang biasa Anda lakukan saat waktu senggang...') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition"></textarea>
                    </div>

                    {{-- Kejahatan (Default Unselected) --}}
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ __('Apakah anda pernah terlibat kasus tindakan kejahatan?') }} <span class="text-rose-500">*</span>
                            </span>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="setOther('crime_involvement', false)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="other.crime_involvement.is_yes === false 
                                        ? 'bg-slate-900 text-white border-slate-900 dark:bg-[#1D2E54]' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.crime_involvement.is_yes === false" class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('TIDAK') }}</span>
                                </button>
                                <button type="button" @click="setOther('crime_involvement', true)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="other.crime_involvement.is_yes === true 
                                        ? 'bg-rose-600 text-white border-rose-600 shadow-md shadow-rose-600/20' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.crime_involvement.is_yes === true" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('IYA') }}</span>
                                </button>
                            </div>
                        </div>
                        <div x-show="other.crime_involvement.is_yes === true" x-cloak x-transition.duration.150ms
                            class="p-3 bg-white dark:bg-[#0D1527] rounded-lg border border-rose-300 dark:border-rose-900 space-y-1">
                            <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400">{{ __('Jelaskan keterlibatan perkara:') }} <span class="text-rose-500">*</span></span>
                            <textarea x-model="other.crime_involvement.detail" rows="2" placeholder="{{ __('Tuliskan rincian kasus dan status hukumnya...') }}"
                                class="w-full px-3 py-1.5 text-xs rounded border border-rose-200 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white"></textarea>
                        </div>
                    </div>

                    {{-- Sel Tahanan (Default Unselected) --}}
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ __('Apakah anda pernah masuk sel tahanan?') }} <span class="text-rose-500">*</span>
                            </span>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="setOther('custody', false)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="other.custody.is_yes === false 
                                        ? 'bg-slate-900 text-white border-slate-900 dark:bg-[#1D2E54]' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.custody.is_yes === false" class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('TIDAK') }}</span>
                                </button>
                                <button type="button" @click="setOther('custody', true)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="other.custody.is_yes === true 
                                        ? 'bg-rose-600 text-white border-rose-600 shadow-md shadow-rose-600/20' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.custody.is_yes === true" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('IYA') }}</span>
                                </button>
                            </div>
                        </div>
                        <div x-show="other.custody.is_yes === true" x-cloak x-transition.duration.150ms
                            class="p-3 bg-white dark:bg-[#0D1527] rounded-lg border border-rose-300 dark:border-rose-900 space-y-1">
                            <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400">{{ __('Jelaskan keterangan:') }} <span class="text-rose-500">*</span></span>
                            <textarea x-model="other.custody.detail" rows="2" placeholder="{{ __('Tuliskan alasan penahanan, kapan, dan berapa lama...') }}"
                                class="w-full px-3 py-1.5 text-xs rounded border border-rose-200 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white"></textarea>
                        </div>
                    </div>
                </div>

                <!-- 3. KELOMPOK MOTIVASI & RELASI PERUSAHAAN -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-[#1D2E54]/60">
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>{{ __('Motivasi Melamar & Relasi Perusahaan') }}</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Alasan melamar perusahaan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1.5">
                                {{ __('Apa alasan anda melamar di perusahaan ini?') }} <span class="text-rose-500">*</span>
                            </label>
                            <textarea x-model="other.reason_applying_company" rows="3"
                                placeholder="{{ __('Jelaskan ketertarikan Anda terhadap profil, nilai, atau reputasi perusahaan ini...') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition"></textarea>
                        </div>

                        {{-- Alasan posisi --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1.5">
                                {{ __('Apa alasan anda melamar pada posisi ini?') }} <span class="text-rose-500">*</span>
                            </label>
                            <textarea x-model="other.reason_applying_position" rows="3"
                                placeholder="{{ __('Jelaskan kesesuaian keahlian, pengalaman, dan minat Anda dengan posisi yang dilamar...') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition"></textarea>
                        </div>
                    </div>

                    {{-- Sumber lowongan --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1.5">
                            {{ __('Darimana info lowongan yang anda dapat?') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" x-model="other.vacancy_source"
                            placeholder="{{ __('Contoh: Website Perusahaan, LinkedIn, Jobstreet, Rekomendasi Teman, Instagram, dll.') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                    </div>

                    {{-- Sedang melamar di perusahaan lain (Default Unselected) --}}
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ __('Apakah anda sedang melamar pada perusahaan lain?') }} <span class="text-rose-500">*</span>
                            </span>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="setOther('other_applications', false)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="other.other_applications.is_yes === false 
                                        ? 'bg-slate-900 text-white border-slate-900 dark:bg-[#1D2E54]' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.other_applications.is_yes === false" class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('TIDAK') }}</span>
                                </button>
                                <button type="button" @click="setOther('other_applications', true)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="other.other_applications.is_yes === true 
                                        ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-600/20' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.other_applications.is_yes === true" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('IYA') }}</span>
                                </button>
                            </div>
                        </div>
                        <div x-show="other.other_applications.is_yes === true" x-cloak x-transition.duration.150ms
                            class="p-3 bg-white dark:bg-[#0D1527] rounded-lg border border-blue-200 dark:border-blue-900 space-y-1">
                            <span class="text-[11px] font-bold text-blue-700 dark:text-blue-400">{{ __('Sebutkan nama perusahaan & posisi:') }} <span class="text-rose-500">*</span></span>
                            <input type="text" x-model="other.other_applications.detail" placeholder="{{ __('Contoh: PT ABC (Posisi Staff IT) - Tahap Interview') }}"
                                class="w-full px-3 py-1.5 text-xs rounded border border-blue-200 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white">
                        </div>
                    </div>

                    {{-- Kerabat / Keluarga di Perusahaan (Default Unselected) --}}
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ __('Apakah Anda memiliki keluarga, kerabat atau teman yang bekerja di Perusahaan ini?') }} <span class="text-rose-500">*</span>
                            </span>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="setOther('internal_relations', false)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="other.internal_relations.is_yes === false 
                                        ? 'bg-slate-900 text-white border-slate-900 dark:bg-[#1D2E54]' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.internal_relations.is_yes === false" class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('TIDAK') }}</span>
                                </button>
                                <button type="button" @click="setOther('internal_relations', true)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="other.internal_relations.is_yes === true 
                                        ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-600/20' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.internal_relations.is_yes === true" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('IYA') }}</span>
                                </button>
                            </div>
                        </div>
                        <div x-show="other.internal_relations.is_yes === true" x-cloak x-transition.duration.150ms
                            class="p-3 bg-white dark:bg-[#0D1527] rounded-lg border border-blue-200 dark:border-blue-900 space-y-1">
                            <span class="text-[11px] font-bold text-blue-700 dark:text-blue-400">{{ __('Jika IYA, sebut nama dan posisi!') }} <span class="text-rose-500">*</span></span>
                            <input type="text" x-model="other.internal_relations.detail" placeholder="{{ __('Contoh: Bpk. Bambang Susanto (Manager Operasional)') }}"
                                class="w-full px-3 py-1.5 text-xs rounded border border-blue-200 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white">
                        </div>
                    </div>

                    {{-- Pernah melamar sebelumnya (Default Unselected) --}}
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ __('Apakah anda pernah melamar ke Perusahaan ini sebelumnya?') }} <span class="text-rose-500">*</span>
                            </span>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="setOther('previously_applied', false)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="other.previously_applied.is_yes === false 
                                        ? 'bg-slate-900 text-white border-slate-900 dark:bg-[#1D2E54]' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.previously_applied.is_yes === false" class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('TIDAK') }}</span>
                                </button>
                                <button type="button" @click="setOther('previously_applied', true)"
                                    class="px-4 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 border"
                                    :class="other.previously_applied.is_yes === true 
                                        ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-600/20' 
                                        : 'bg-white dark:bg-[#0D1527] text-slate-600 dark:text-[#93A5C9] border-slate-200/80 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#14203A]'">
                                    <svg x-show="other.previously_applied.is_yes === true" class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ __('IYA') }}</span>
                                </button>
                            </div>
                        </div>
                        <div x-show="other.previously_applied.is_yes === true" x-cloak x-transition.duration.150ms
                            class="p-3 bg-white dark:bg-[#0D1527] rounded-lg border border-blue-200 dark:border-blue-900 space-y-1">
                            <span class="text-[11px] font-bold text-blue-700 dark:text-blue-400">{{ __('Jika IYA, kapan?') }} <span class="text-rose-500">*</span></span>
                            <input type="text" x-model="other.previously_applied.detail" placeholder="{{ __('Contoh: Tahun 2023 untuk posisi Staff Admin') }}"
                                class="w-full px-3 py-1.5 text-xs rounded border border-blue-200 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- 4. KELOMPOK KESIAPAN KERJA & REMUNERASI -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-[#1D2E54]/60">
                    <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>{{ __('Kesiapan Mulai Kerja & Remunerasi') }}</span>
                    </h4>

                    {{-- Kesiapan kerja --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1.5">
                            {{ __('Jika Anda diterima/LOLOS, kapan anda siap kerja di perusahaan ini?') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" x-model="other.ready_to_work"
                            placeholder="{{ __('Contoh: Dapat Segera Bekerja / 1 Bulan setelah pengunduran diri (Notice Period) / 1 November 2026') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Gaji --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                                    {{ __('Berapa gaji yang anda inginkan? Jelaskan!') }} <span class="text-rose-500">*</span>
                                </label>
                                <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-[#93F514]">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    {{ __('Tersinkron dengan Minat Kerja') }}
                                </span>
                            </div>
                            <textarea x-model="other.expected_salary" rows="3"
                                placeholder="{{ __('Contoh: Rp 6.000.000 - Rp 7.500.000 (Berdasarkan pengalaman 3 tahun di bidang IT dan standar biaya hidup di lokasi kerja)') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition"></textarea>
                            <p class="text-[11px] text-slate-400 dark:text-[#93A5C9] mt-1">
                                {{ __('Nominal gaji ini terisi otomatis dari Minat Kerja dan akan saling menyinkronkan data.') }}
                            </p>
                        </div>

                        {{-- Fasilitas / Tunjangan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-800 dark:text-slate-200 mb-1.5">
                                {{ __('Apa Fasilitas/ Tunjangan yang anda inginkan?') }} <span class="text-rose-500">*</span>
                            </label>
                            <textarea x-model="other.expected_benefits" rows="3"
                                placeholder="{{ __('Contoh: BPJS Kesehatan & Ketenagakerjaan, Tunjangan Transportasi, Uang Makan, dan Asuransi Kesehatan') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] transition"></textarea>
                        </div>
                    </div>
                </div>

                <!-- 5. KELOMPOK PERNYATAAN KEBENARAN DATA & TANDA TANGAN DIGITAL -->
                <div class="pt-5 border-t-2 border-dashed border-slate-200 dark:border-[#1D2E54]">
                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/60 dark:bg-[#14203A] border border-amber-200/80 dark:border-[#2D457C] space-y-3.5 shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-[#93F514] flex items-center justify-center shrink-0 border border-amber-200 dark:border-[#93F514]/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                                    {{ __('Pernyataan Kebenaran Data Pelamar') }}
                                </h4>
                                <span class="text-[11px] text-slate-500 dark:text-[#93A5C9]">{{ __('Wajib dibaca dan disetujui sebelum menyimpan kuesioner profil.') }}</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-white dark:bg-[#0D1527] border border-amber-200/70 dark:border-[#1D2E54] text-xs text-slate-700 dark:text-slate-300 leading-relaxed italic">
                            &ldquo;{{ __('Dengan ini saya menyatakan bahwa seluruh data profil dan riwayat yang saya lampirkan dalam lamaran ini (Data Pribadi, Keluarga, Pendidikan, Pengalaman Kerja, hingga Data Tambahan) adalah') }} <strong class="text-slate-900 dark:text-white font-bold not-italic">{{ __('BENAR') }}</strong>. {{ __('Saya menyadari bahwa ketidakjujuran dapat mengakibatkan Pembatalan Lamaran atau Pemutusan Hubungan Kerja (PHK) dari Pihak Perusahaan.') }}&rdquo;
                        </div>

                        <label class="flex items-start sm:items-center gap-3 cursor-pointer select-none group p-2.5 rounded-xl hover:bg-amber-100/40 dark:hover:bg-[#1D2E54]/40 transition">
                            <input type="checkbox" x-model="other.statement_agreed"
                                class="mt-0.5 sm:mt-0 rounded border-slate-300 dark:border-[#1D2E54] text-emerald-600 dark:text-[#93F514] focus:ring-emerald-500 dark:focus:ring-[#93F514] dark:bg-[#0D1527] w-4 h-4 transition cursor-pointer">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-700 dark:group-hover:text-[#93F514] transition">
                                {{ __('Saya menyetujui pernyataan di atas dan menjamin kebenaran seluruh data saya.') }} <span class="text-rose-500">*</span>
                            </span>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Action Button Submit & Alert Bawah -->
            <div class="p-5 md:p-6 bg-slate-50/70 dark:bg-[#14203A]/40 border-t border-slate-200/80 dark:border-[#1D2E54] space-y-3">
                <div id="validation-banner-bottom" x-show="errorMessage" x-cloak x-transition
                    class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-300 dark:border-amber-800 text-amber-800 dark:text-amber-300 flex items-center justify-between gap-3 text-xs font-semibold shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span x-text="errorMessage"></span>
                    </div>
                    <button type="button" @click="errorMessage = ''" class="text-amber-500 hover:text-amber-700 font-bold text-sm px-1.5">
                        &times;
                    </button>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <span class="text-xs text-slate-500 dark:text-[#93A5C9] font-medium hidden sm:inline">
                        {{ __('Seluruh pertanyaan bertanda bintang (*) wajib dijawab sebelum menyimpan.') }}
                    </span>
                    <button type="submit" wire:loading.attr="disabled"
                        class="ml-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-70 disabled:cursor-not-allowed text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition duration-150 active:scale-95 cursor-pointer">
                        <svg wire:loading.remove wire:target="save" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin shrink-0 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ __('Simpan Data Tambahan') }}</span>
                        <span wire:loading wire:target="save">{{ __('Menyimpan...') }}</span>
                    </button>
                </div>
            </div>
        </div>

    </form>

</div>
