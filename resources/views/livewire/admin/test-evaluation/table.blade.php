@php
    $isRecruiter = auth()->check() && (auth()->user()->role_id == 2 || strtolower(auth()->user()->role?->name ?? '') === 'recruiter');
    $gradeActionPrefix = $isRecruiter ? '/recruiter/test-evaluations/' : '/admin/test-evaluations/';
@endphp

<div class="space-y-6" x-data="{ 
    showGradingModal: false,
    isSubmittingGrading: false,
    statusTemplates: {
        'Reviewed': 'Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.',
        'Shortlisted': 'Selamat! Anda dinyatakan lolos tahap evaluasi ujian dan masuk ke dalam daftar kandidat terpilih (Shortlisted). Kami akan segera menginformasikan jadwal wawancara.',
        'Interview': 'Anda diundang untuk mengikuti tahap wawancara kerja. Silakan periksa jadwal dan informasi meeting yang tertera pada akun Anda.',
        'Accepted': 'Selamat! Anda dinyatakan DITERIMA untuk bergabung bersama kami. Tim HR akan segera menghubungi Anda terkait proses offering dan onboarding.',
        'Rejected': 'Terima kasih atas partisipasi Anda dalam mengikuti rangkaian ujian seleksi. Saat ini hasil evaluasi belum sesuai dengan kriteria yang kami butuhkan. Tetap semangat dan sukses selalu.'
    },
    gradingData: {
        id: '',
        applicant_name: '',
        applicant_email: '',
        applicant_photo: null,
        job_title: '',
        test_title: '',
        passing_score: 0,
        objective_score: 0,
        essay_score: 0,
        total_score: 0,
        status: '',
        application_status: 'Reviewed',
        application_notes: '',
        send_email: true,
        answers: []
    },

    openGradingModal(att, isDisc = false, isPapi = false) {
        let name = 'Pelamar';
        let email = '';
        let photo = null;
        if (att.job_application && att.job_application.applicant_profile) {
            name = att.job_application.applicant_profile.full_name || 'Pelamar';
            let p = att.job_application.applicant_profile.photo || (att.job_application.applicant_profile.user ? att.job_application.applicant_profile.user.avatar : null);
            if (p) {
                photo = p.startsWith('http') ? p : ('/storage/' + p);
            }
            if (att.job_application.applicant_profile.user) {
                email = att.job_application.applicant_profile.user.email || '';
            }
        }

        const initialStatus = att.job_application ? (att.job_application.status || 'Reviewed') : 'Reviewed';
        let initialNotes = att.job_application ? (att.job_application.notes || '') : '';

        // Jika catatan masih kosong, gunakan template status default
        if (!initialNotes && this.statusTemplates[initialStatus]) {
            initialNotes = this.statusTemplates[initialStatus];
        }

        this.gradingData = {
            id: att.id,
            applicant_name: name,
            applicant_email: email,
            applicant_photo: photo,
            job_title: att.job_application && att.job_application.job ? att.job_application.job.title : '-',
            test_title: att.test ? att.test.title : '-',
            passing_score: att.test ? att.test.passing_score : 0,
            objective_score: att.objective_score || 0,
            essay_score: att.essay_score || 0,
            total_score: att.total_score || 0,
            status: att.status || 'in_progress',
            application_status: initialStatus,
            application_notes: initialNotes,
            send_email: true,
            answers: att.answers || [],
            disc_result: att.disc_test_result || null,
            is_disc: isDisc,
            papi_result: att.papi_test_result || null,
            is_papi: isPapi,
            participant_name: att.participant_name || name,
            participant_age: att.participant_age || (att.job_application && att.job_application.applicant_profile ? att.job_application.applicant_profile.age : null),
            participant_gender: att.participant_gender || (att.job_application && att.job_application.applicant_profile ? att.job_application.applicant_profile.gender : null),
            test_date: att.test_date || (att.started_at ? att.started_at.substring(0, 10) : null)
        };
        this.showGradingModal = true;
    },

    applyTemplate(statusKey) {
        if (statusKey) {
            this.gradingData.application_status = statusKey;
        }
        const targetStatus = statusKey || this.gradingData.application_status;
        if (this.statusTemplates[targetStatus]) {
            this.gradingData.application_notes = this.statusTemplates[targetStatus];
        }
    },

    onStatusChange(newStatus) {
        const currentNotes = (this.gradingData.application_notes || '').trim();
        const isExistingTemplate = Object.values(this.statusTemplates).some(t => t.trim() === currentNotes);

        if (!currentNotes || isExistingTemplate) {
            if (this.statusTemplates[newStatus]) {
                this.gradingData.application_notes = this.statusTemplates[newStatus];
            }
        }
    },

    calcY(score) {
        let val = Math.max(-8, Math.min(8, parseFloat(score) || 0));
        return 80 - (val * 8.125);
    },

    getGroupedQuestions() {
        if (!this.gradingData || !this.gradingData.answers) return [];
        
        let groups = [];
        let map = {};

        this.gradingData.answers.forEach(ans => {
            let qId = ans.question_id || (ans.question ? ans.question.id : 0);
            if (!map[qId]) {
                map[qId] = {
                    question_id: qId,
                    question: ans.question,
                    question_type: ans.question ? ans.question.question_type : (ans.answer_type === 'most' || ans.answer_type === 'least' ? 'disc' : 'multiple_choice'),
                    points: ans.question ? ans.question.points : 1,
                    answers: [],
                    most_answer: null,
                    least_answer: null,
                    single_answer: null
                };
                groups.push(map[qId]);
            }

            map[qId].answers.push(ans);

            if (ans.answer_type === 'most') {
                map[qId].most_answer = ans;
            } else if (ans.answer_type === 'least') {
                map[qId].least_answer = ans;
            } else {
                map[qId].single_answer = ans;
            }
        });

        return groups;
    },

    getPolyline(scores) {
        if (!scores) return '';
        let dY = this.calcY(scores.D || 0);
        let iY = this.calcY(scores.I || 0);
        let sY = this.calcY(scores.S || 0);
        let cY = this.calcY(scores.C || 0);
        return `35,${dY} 85,${iY} 135,${sY} 185,${cY}`;
    },

    getPapiSheetRows() {
        if (!this.gradingData || !this.gradingData.papi_result) return [];
        let raw = this.gradingData.papi_result.raw_answers || {};
        let rows = [];
        let startCols = [1, 11, 21, 31, 41, 51, 61, 71, 81];
        for (let r = 0; r < 10; r++) {
            let cells = [];
            startCols.forEach(startNum => {
                let qNum = startNum + r;
                let ans = raw[qNum] || raw[String(qNum)];
                let choice = '-';
                if (ans) {
                    if (ans.choice) {
                        choice = String(ans.choice).toLowerCase();
                    } else if (ans.tag) {
                        choice = String(ans.tag).toLowerCase();
                    }
                }
                // Cell 1: Nomor Soal (peach background)
                cells.push({
                    text: qNum,
                    isNum: true
                });
                // Cell 2: Pilihan Jawaban a/b (white background)
                cells.push({
                    text: choice,
                    isNum: false
                });
            });
            rows.push(cells);
        }
        return rows;
    },

    getPapiScore(code) {
        if (!this.gradingData || !this.gradingData.papi_result) return 0;
        let scores = this.gradingData.papi_result.scores || {};
        if (scores[code] !== undefined) return scores[code];
        let interp = this.gradingData.papi_result.interpretations || {};
        return interp[code] ? (interp[code].score || 0) : 0;
    },

    getPapiInterpretation(code) {
        if (!this.gradingData || !this.gradingData.papi_result) return '-';
        let interp = this.gradingData.papi_result.interpretations || {};
        if (interp[code]) {
            return interp[code].interpretation || interp[code].description || '-';
        }
        return '-';
    }
}">

    <!-- Session Notifications -->
    @if (session('update'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold">{{ session('update') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Card 1: Header & Action Section Card -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm p-5 sm:p-6 space-y-4">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Hasil & Evaluasi Ujian Pelamar</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Tinjau riwayat pengerjaan tes pelamar, berikan penilaian (grading) soal essay, dan evaluasi hasil tes.</p>
            </div>
        </div>

        <!-- Filter & Search Toolbar Row -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="relative lg:col-span-3">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari nama pelamar / ujian..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Perusahaan -->
                <div class="relative lg:col-span-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <select wire:model.live="companyId" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">Semua Perusahaan</option>
                        @foreach ($companies as $comp)
                            <option value="{{ $comp->id }}">{{ $comp->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Lowongan (Otomatis terfilter sesuai perusahaan) -->
                <div class="relative lg:col-span-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <select wire:model.live="jobId" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">{{ $companyId ? 'Semua Lowongan di Perusahaan Ini' : 'Semua Lowongan' }}</option>
                        @foreach ($jobs as $job)
                            <option value="{{ $job->id }}">
                                {{ $job->title }} @if(!$companyId && $job->company)({{ $job->company->name }})@endif
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Status -->
                <div class="relative lg:col-span-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <select wire:model.live="status" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">Semua Status</option>
                        <option value="needs_grading">Perlu Koreksi Essay</option>
                        <option value="passed">Lulus (Passed)</option>
                        <option value="failed">Gagal / Ditolak</option>
                        <option value="disc">Tes Kepribadian (DISC)</option>
                        <option value="in_progress">Sedang Dikerjakan</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Quick Filter Badges & Reset Button -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase mr-1">Status:</span>
                    <button type="button" wire:click="$set('status', '')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $status === '' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Semua
                    </button>
                    <button type="button" wire:click="$set('status', 'needs_grading')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $status === 'needs_grading' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/40' }}">
                        Perlu Koreksi Essay
                    </button>
                    <button type="button" wire:click="$set('status', 'passed')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $status === 'passed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }}">
                        Lulus (Passed)
                    </button>
                    <button type="button" wire:click="$set('status', 'failed')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $status === 'failed' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800 hover:bg-rose-50 dark:hover:bg-rose-950/40' }}">
                        Gagal
                    </button>
                    <button type="button" wire:click="$set('status', 'disc')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $status === 'disc' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800 hover:bg-purple-50 dark:hover:bg-purple-950/40' }}">
                        DISC
                    </button>
                    <button type="button" wire:click="$set('status', 'in_progress')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $status === 'in_progress' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800 hover:bg-blue-50 dark:hover:bg-blue-950/40' }}">
                        Sedang Dikerjakan
                    </button>
                </div>

                @if ($search || $companyId || $jobId || $status || $sortField !== 'id')
                    <button type="button" wire:click="resetFilters" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline inline-flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Reset Filter</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
        
    <!-- Card 2: Data Table Section Card -->
    <div class="relative bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Livewire Loading Overlay -->
        <div wire:loading wire:target="search, companyId, jobId, status, sortField, sortDirection, sortBy, previousPage, nextPage, gotoPage, resetFilters" class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center z-10 transition">
            <div class="flex items-center gap-2.5 px-4 py-2.5 bg-slate-900/90 dark:bg-slate-800/90 text-white rounded-xl shadow-xl text-xs font-semibold">
                <svg class="animate-spin w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memuat data...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 text-gray-500 dark:text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="px-6 py-4 w-12">No</th>
                        <th class="px-6 py-4 cursor-pointer hover:text-indigo-600 transition" wire:click="sortBy('applicant')">
                            <div class="flex items-center gap-1">
                                <span>Pelamar & Lowongan</span>
                                @if ($sortField === 'applicant')
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4">Paket Ujian</th>
                        <th class="px-6 py-4 cursor-pointer hover:text-indigo-600 transition" wire:click="sortBy('started_at')">
                            <div class="flex items-center gap-1">
                                <span>Waktu Pengerjaan</span>
                                @if ($sortField === 'started_at')
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4">Nilai P. Ganda / Essay</th>
                        <th class="px-6 py-4 cursor-pointer hover:text-indigo-600 transition" wire:click="sortBy('score')">
                            <div class="flex items-center gap-1">
                                <span>Total & KKM</span>
                                @if ($sortField === 'score')
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 cursor-pointer hover:text-indigo-600 transition" wire:click="sortBy('status')">
                            <div class="flex items-center gap-1">
                                <span>Status</span>
                                @if ($sortField === 'status')
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-gray-700 dark:text-slate-300">
                    @forelse ($attempts as $index => $att)
                        @php
                            $profile = $att->jobApplication->applicantProfile ?? null;
                            $applicantName = $profile->full_name ?? 'Pelamar';
                            $hasUnreviewedEssay = $att->answers->contains(function($ans) {
                                return $ans->question && $ans->question->question_type === 'essay' && is_null($ans->reviewed_by);
                            });
                            $papiResult = $att->papiTestResult;
                            $isPapi = ($papiResult && (
                                str_contains(strtolower($att->test?->title ?? ''), 'papi') ||
                                str_contains(strtolower($att->test?->category?->name ?? ''), 'papi') ||
                                $att->answers->contains(fn($ans) => $ans->question?->question_type === 'papi_kostick')
                            )) || ($att->test && (str_contains(strtolower($att->test->title ?? ''), 'papi') || str_contains(strtolower($att->test->category?->name ?? ''), 'papi')));
                            $isDisc = ($att->discTestResult && (
                                str_contains(strtolower($att->test?->title ?? ''), 'disc') ||
                                str_contains(strtolower($att->test?->category?->name ?? ''), 'disc') ||
                                $att->answers->contains(fn($ans) => $ans->question?->question_type === 'disc' || in_array($ans->answer_type, ['most', 'least']))
                            )) || ($att->test && (str_contains(strtolower($att->test->title ?? ''), 'disc') || str_contains(strtolower($att->test->category?->name ?? ''), 'disc')));
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-500 dark:text-slate-400">
                                {{ $attempts->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $candPhoto = $profile?->photo ?? $profile?->user?->avatar;
                                        $candPhotoUrl = null;
                                        if ($candPhoto) {
                                            $candPhotoUrl = \Illuminate\Support\Str::startsWith($candPhoto, ['http://', 'https://']) ? $candPhoto : asset('storage/' . $candPhoto);
                                        }
                                    @endphp
                                    @if ($candPhotoUrl)
                                        <img src="{{ $candPhotoUrl }}" alt="{{ $applicantName }}" class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-2xs shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($applicantName, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <span class="block font-bold text-gray-900 dark:text-white truncate">{{ $applicantName }}</span>
                                        <span class="text-[11px] text-gray-500 dark:text-slate-400 font-medium truncate block">
                                            {{ $att->jobApplication->job->title ?? '-' }}
                                            @if($att->jobApplication?->job?->company)
                                                <span class="text-gray-400 font-normal">• {{ $att->jobApplication->job->company->name }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-0.5">
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $att->test->title ?? '-' }}</span>
                                    <span class="block text-[11px] text-gray-400 dark:text-slate-500">{{ $att->test->category->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-0.5 text-[11px]">
                                    <span class="block text-gray-700 dark:text-slate-300 font-medium">
                                        {{ $att->started_at ? \Carbon\Carbon::parse($att->started_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                                    </span>
                                    @if ($att->duration)
                                        <span class="text-gray-400 dark:text-slate-500">Durasi: {{ round($att->duration / 60) }} menit</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($isPapi || $isDisc)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium {{ $isPapi ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 border-slate-200/60 dark:border-slate-700/60' }} border">
                                        Self-Inventory
                                    </span>
                                @else
                                    <div class="space-y-0.5 text-[11px]">
                                        <div class="flex items-center gap-1.5 text-indigo-600 dark:text-indigo-400 font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            <span>PG: {{ number_format($att->objective_score ?? 0, 1) }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400 font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Essay: {{ number_format($att->essay_score ?? 0, 1) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-0.5">
                                    @if ($isPapi && $papiResult)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            PAPI Kostick
                                        </span>
                                        <span class="block text-[11px] {{ $papiResult->is_valid ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-rose-500 font-semibold' }}">
                                            {{ $papiResult->is_valid ? 'Valid (45/45)' : 'Perlu Cek' }}
                                        </span>
                                    @elseif ($isPapi)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            Belum Ada Hasil
                                        </span>
                                        <span class="block text-[10px] text-gray-400">PAPI Kostick</span>
                                    @elseif ($isDisc && $att->discTestResult)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                            DISC: {{ $att->discTestResult->discProfile->pattern_code ?? 'Profile' }}
                                        </span>
                                        <span class="block text-[11px] text-gray-400 dark:text-slate-500">Tes Kepribadian</span>
                                    @elseif ($isDisc)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            Belum Ada Hasil
                                        </span>
                                        <span class="block text-[10px] text-gray-400">Tes Kepribadian</span>
                                    @else
                                        <span class="block text-sm font-extrabold text-gray-900 dark:text-white">
                                            {{ number_format($att->total_score ?? 0, 1) }}
                                        </span>
                                        <span class="text-[11px] text-gray-400 dark:text-slate-500">KKM: {{ number_format($att->test->passing_score ?? 0, 0) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1.5">
                                    @if ($isPapi && $papiResult)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            PAPI Terbentuk
                                        </span>
                                    @elseif ($isPapi)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            Belum Lengkap
                                        </span>
                                    @elseif ($isDisc && $att->discTestResult)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Profil Terbentuk
                                        </span>
                                    @elseif ($isDisc)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            Belum Lengkap
                                        </span>
                                    @elseif ($hasUnreviewedEssay)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 animate-pulse">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Perlu Koreksi Essay
                                        </span>
                                    @elseif ($att->status === 'passed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Lulus (Passed)
                                        </span>
                                    @elseif ($att->status === 'failed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                            <svg class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Gagal (Failed)
                                        </span>
                                    @elseif ($att->status === 'in_progress')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-sky-50 dark:bg-sky-950/50 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-ping"></span>
                                            Sedang Pengerjaan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            Selesai (Completed)
                                        </span>
                                    @endif

                                    @if ($att->jobApplication)
                                        @php
                                            $appStatus = $att->jobApplication->status;
                                            $badgeBg = match(strtolower($appStatus)) {
                                                'accepted' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 border-emerald-200 dark:border-emerald-800',
                                                'rejected' => 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/50 border-rose-200 dark:border-rose-800',
                                                'interview' => 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 border-indigo-200 dark:border-indigo-800',
                                                'shortlisted' => 'text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/50 border-purple-200 dark:border-purple-800',
                                                'reviewed' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 border-amber-200 dark:border-amber-800',
                                                default => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 border-blue-200 dark:border-blue-800',
                                            };
                                        @endphp
                                        <div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeBg }}">
                                                Lamaran: {{ $appStatus }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end">
                                    <button @click="openGradingModal({{ \Illuminate\Support\Js::from($att) }}, {{ $isDisc ? 'true' : 'false' }}, {{ $isPapi ? 'true' : 'false' }})" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-sm transition-all flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>{{ $isPapi ? 'Riwayat & Interpretasi PAPI' : ($isDisc ? 'Riwayat Jawaban & Profil' : 'Riwayat Jawaban & Nilai') }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium">Belum ada data pengerjaan tes pelamar</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($attempts->hasPages() || $perPage != 10)
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 dark:text-slate-400">Tampilkan</span>
                    <select wire:model.live="perPage" class="pl-2.5 pr-7 py-1.5 text-xs rounded-lg bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-xs text-gray-500 dark:text-slate-400">data per halaman</span>
                </div>
                @if ($attempts->hasPages())
                    <div>
                        {{ $attempts->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    @php
        $papiAspects = [
            [
                'name' => 'Arah kerja',
                'factors' => [
                    ['code' => 'N', 'name' => 'Penyelesaian secara prestasi'],
                    ['code' => 'G', 'name' => 'Peranan sebagai pekerja keras'],
                    ['code' => 'A', 'name' => 'Hasrat untuk berprestasi'],
                ],
            ],
            [
                'name' => 'Kepemimpinan',
                'factors' => [
                    ['code' => 'L', 'name' => 'Peran sebagai pimpinan'],
                    ['code' => 'P', 'name' => 'Pengendalian orang lain'],
                    ['code' => 'I', 'name' => 'Mudah dalam mengambil keputusan'],
                ],
            ],
            [
                'name' => 'Aktivitas',
                'factors' => [
                    ['code' => 'T', 'name' => 'Tipe selalu sibuk'],
                    ['code' => 'V', 'name' => 'Tipe yang bersemangat'],
                ],
            ],
            [
                'name' => 'Pergaulan',
                'factors' => [
                    ['code' => 'X', 'name' => 'Kebutuhan untuk mendapatkan perhatian'],
                    ['code' => 'S', 'name' => 'Pergaulan luas'],
                    ['code' => 'B', 'name' => 'Kebutuhan berkelompok'],
                    ['code' => 'O', 'name' => 'Kebutuhan untuk dekat dan menyayangi'],
                ],
            ],
            [
                'name' => 'Gaya kerja',
                'factors' => [
                    ['code' => 'R', 'name' => 'Tipe teoritikal'],
                    ['code' => 'D', 'name' => 'Suka pekerjaan yang terperinci'],
                    ['code' => 'C', 'name' => 'Tipe teratur'],
                ],
            ],
            [
                'name' => 'Sifat',
                'factors' => [
                    ['code' => 'Z', 'name' => 'Hasrat untuk berubah'],
                    ['code' => 'E', 'name' => 'Pengendalian emosi'],
                    ['code' => 'K', 'name' => 'Agresi'],
                ],
            ],
            [
                'name' => 'Ketaatan',
                'factors' => [
                    ['code' => 'F', 'name' => 'Dukungan terhadap atasan'],
                    ['code' => 'W', 'name' => 'Kebutuhan taat pada aturan dan pengarahan'],
                ],
            ],
        ];
    @endphp

    <!-- Modal Evaluasi & Essay Grading -->
    <div x-show="showGradingModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto custom-scrollbar" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showGradingModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showGradingModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showGradingModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800 gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <template x-if="gradingData.applicant_photo">
                                <img :src="gradingData.applicant_photo" :alt="gradingData.applicant_name" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-sm shrink-0">
                            </template>
                            <template x-if="!gradingData.applicant_photo">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    <span x-text="gradingData.applicant_name ? gradingData.applicant_name.substring(0, 2).toUpperCase() : 'P'"></span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white truncate" x-text="gradingData.is_papi ? 'Riwayat Jawaban & Interpretasi PAPI Kostick Pelamar' : (gradingData.is_disc ? 'Riwayat Jawaban & Profil Kepribadian Pelamar' : 'Evaluasi & Penilaian Jawaban Pelamar')">
                                    Evaluasi & Penilaian Jawaban Pelamar
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 truncate">
                                    Pelamar: <span class="font-bold text-gray-800 dark:text-gray-200" x-text="gradingData.applicant_name"></span> | Lowongan: <span class="font-medium text-indigo-600 dark:text-indigo-400" x-text="gradingData.job_title"></span>
                                </p>
                            </div>
                        </div>
                        <button @click="showGradingModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- DISC Personality Analysis Report (If DISC Result exists) -->
                    <template x-if="gradingData.disc_result">
                        <div class="my-4 p-5 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800 space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-purple-200 dark:border-purple-800/80 gap-3">
                                <div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-600 text-white uppercase tracking-wider">
                                        Hasil Profil DISC Pelamar
                                    </span>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mt-1" x-text="gradingData.disc_result.disc_profile ? (gradingData.disc_result.disc_profile.pattern_code + ' - ' + gradingData.disc_result.disc_profile.title) : 'Tipe Kepribadian DISC'"></h4>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a :href="'{{ $isRecruiter ? '/recruiter/test-evaluations/' : '/admin/test-evaluations/' }}' + gradingData.id + '/disc-pdf'" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-100/80 hover:bg-purple-200 dark:bg-purple-900/60 dark:hover:bg-purple-800 text-purple-900 dark:text-purple-200 border border-purple-200 dark:border-purple-700 rounded-xl text-xs font-semibold shadow-2xs transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Preview PDF</span>
                                    </a>
                                    <a :href="'{{ $isRecruiter ? '/recruiter/test-evaluations/' : '/admin/test-evaluations/' }}' + gradingData.id + '/disc-pdf?download=1'" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-semibold shadow-sm shadow-purple-500/20 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Download PDF</span>
                                    </a>
                                </div>
                            </div>

                            <!-- DISC Score Table -->
                            <div class="overflow-x-auto rounded-xl border border-purple-200 dark:border-purple-800/80 shadow-2xs">
                                <table class="w-full text-xs text-center border-collapse bg-white dark:bg-slate-900">
                                    <thead>
                                        <tr class="bg-purple-600 text-white font-bold text-[11px]">
                                            <th class="py-2 px-3 text-left">Line / Dimensi</th>
                                            <th class="py-2 px-2">D</th>
                                            <th class="py-2 px-2">I</th>
                                            <th class="py-2 px-2">S</th>
                                            <th class="py-2 px-2">C</th>
                                            <th class="py-2 px-2">*</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-purple-100 dark:divide-purple-900/40 text-[11px] font-medium">
                                        <tr>
                                            <td class="py-1.5 px-3 text-left font-bold text-gray-700 dark:text-slate-300">1 (MOST - Public Self)</td>
                                            <td class="py-1.5 px-2 font-bold" x-text="gradingData.disc_result.line_1_scores?.raw?.D ?? 0"></td>
                                            <td class="py-1.5 px-2 font-bold" x-text="gradingData.disc_result.line_1_scores?.raw?.I ?? 0"></td>
                                            <td class="py-1.5 px-2 font-bold" x-text="gradingData.disc_result.line_1_scores?.raw?.S ?? 0"></td>
                                            <td class="py-1.5 px-2 font-bold" x-text="gradingData.disc_result.line_1_scores?.raw?.C ?? 0"></td>
                                            <td class="py-1.5 px-2 text-gray-400" x-text="gradingData.disc_result.line_1_scores?.raw?.['*'] ?? 0"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-3 text-left font-bold text-gray-700 dark:text-slate-300">2 (LEAST - Core Self)</td>
                                            <td class="py-1.5 px-2 font-bold" x-text="gradingData.disc_result.line_2_scores?.raw?.D ?? 0"></td>
                                            <td class="py-1.5 px-2 font-bold" x-text="gradingData.disc_result.line_2_scores?.raw?.I ?? 0"></td>
                                            <td class="py-1.5 px-2 font-bold" x-text="gradingData.disc_result.line_2_scores?.raw?.S ?? 0"></td>
                                            <td class="py-1.5 px-2 font-bold" x-text="gradingData.disc_result.line_2_scores?.raw?.C ?? 0"></td>
                                            <td class="py-1.5 px-2 text-gray-400" x-text="gradingData.disc_result.line_2_scores?.raw?.['*'] ?? 0"></td>
                                        </tr>
                                        <tr class="bg-purple-50/60 dark:bg-purple-950/40 font-bold">
                                            <td class="py-1.5 px-3 text-left text-purple-700 dark:text-purple-300">3 (CHANGE - Perceived Self)</td>
                                            <td class="py-1.5 px-2" x-text="gradingData.disc_result.line_3_scores?.raw?.D ?? 0"></td>
                                            <td class="py-1.5 px-2" x-text="gradingData.disc_result.line_3_scores?.raw?.I ?? 0"></td>
                                            <td class="py-1.5 px-2" x-text="gradingData.disc_result.line_3_scores?.raw?.S ?? 0"></td>
                                            <td class="py-1.5 px-2" x-text="gradingData.disc_result.line_3_scores?.raw?.C ?? 0"></td>
                                            <td class="py-1.5 px-2 text-gray-400">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- 3 Graph SVG Visualization Cards for Admin -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Graph 1: MOST -->
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-rose-200 dark:border-rose-900/60 text-center space-y-2">
                                    <span class="text-[10px] font-bold text-rose-600 uppercase">Graph 1 (MOST) - Mask / Public</span>
                                    <div class="bg-rose-50/40 dark:bg-slate-950/40 rounded-lg p-1.5 border border-rose-100 dark:border-rose-950">
                                        <svg viewBox="0 0 220 160" class="w-full h-36">
                                            <!-- Grid Lines -->
                                            <line x1="20" y1="15" x2="205" y2="15" stroke="#f43f5e" stroke-dasharray="2" stroke-opacity="0.3" />
                                            <text x="5" y="18" fill="#9ca3af" font-size="8" font-family="monospace">+8</text>

                                            <line x1="20" y1="80" x2="205" y2="80" stroke="#f43f5e" stroke-width="1.5" stroke-opacity="0.8" />
                                            <text x="5" y="83" fill="#f43f5e" font-size="9" font-weight="bold" font-family="monospace">0</text>

                                            <line x1="20" y1="145" x2="205" y2="145" stroke="#f43f5e" stroke-dasharray="2" stroke-opacity="0.3" />
                                            <text x="5" y="148" fill="#9ca3af" font-size="8" font-family="monospace">-8</text>

                                            <!-- Axes & Labels -->
                                            <line x1="35" y1="10" x2="35" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="35" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">D</text>

                                            <line x1="85" y1="10" x2="85" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="85" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">I</text>

                                            <line x1="135" y1="10" x2="135" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="135" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">S</text>

                                            <line x1="185" y1="10" x2="185" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="185" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">C</text>

                                            <!-- Polyline -->
                                            <polyline fill="none" stroke="#e11d48" stroke-width="2.2" :points="getPolyline(gradingData.disc_result.line_1_scores?.converted)" stroke-linecap="round" stroke-linejoin="round" />

                                            <!-- Value Labels -->
                                            <text x="35" :y="calcY(gradingData.disc_result.line_1_scores?.converted?.D) - 5" fill="#be123c" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_1_scores?.converted?.D || 0).toFixed(1)"></text>
                                            <text x="85" :y="calcY(gradingData.disc_result.line_1_scores?.converted?.I) - 5" fill="#be123c" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_1_scores?.converted?.I || 0).toFixed(1)"></text>
                                            <text x="135" :y="calcY(gradingData.disc_result.line_1_scores?.converted?.S) - 5" fill="#be123c" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_1_scores?.converted?.S || 0).toFixed(1)"></text>
                                            <text x="185" :y="calcY(gradingData.disc_result.line_1_scores?.converted?.C) - 5" fill="#be123c" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_1_scores?.converted?.C || 0).toFixed(1)"></text>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Graph 2: LEAST -->
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-900/60 text-center space-y-2">
                                    <span class="text-[10px] font-bold text-amber-600 uppercase">Graph 2 (LEAST) - Core / Private</span>
                                    <div class="bg-amber-50/40 dark:bg-slate-950/40 rounded-lg p-1.5 border border-amber-100 dark:border-amber-950">
                                        <svg viewBox="0 0 220 160" class="w-full h-36">
                                            <!-- Grid Lines -->
                                            <line x1="20" y1="15" x2="205" y2="15" stroke="#f59e0b" stroke-dasharray="2" stroke-opacity="0.3" />
                                            <text x="5" y="18" fill="#9ca3af" font-size="8" font-family="monospace">+8</text>

                                            <line x1="20" y1="80" x2="205" y2="80" stroke="#f59e0b" stroke-width="1.5" stroke-opacity="0.8" />
                                            <text x="5" y="83" fill="#f59e0b" font-size="9" font-weight="bold" font-family="monospace">0</text>

                                            <line x1="20" y1="145" x2="205" y2="145" stroke="#f59e0b" stroke-dasharray="2" stroke-opacity="0.3" />
                                            <text x="5" y="148" fill="#9ca3af" font-size="8" font-family="monospace">-8</text>

                                            <!-- Axes & Labels -->
                                            <line x1="35" y1="10" x2="35" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="35" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">D</text>

                                            <line x1="85" y1="10" x2="85" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="85" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">I</text>

                                            <line x1="135" y1="10" x2="135" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="135" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">S</text>

                                            <line x1="185" y1="10" x2="185" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="185" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">C</text>

                                            <!-- Polyline -->
                                            <polyline fill="none" stroke="#d97706" stroke-width="2.2" :points="getPolyline(gradingData.disc_result.line_2_scores?.converted)" stroke-linecap="round" stroke-linejoin="round" />

                                            <!-- Value Labels -->
                                            <text x="35" :y="calcY(gradingData.disc_result.line_2_scores?.converted?.D) - 5" fill="#b45309" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_2_scores?.converted?.D || 0).toFixed(1)"></text>
                                            <text x="85" :y="calcY(gradingData.disc_result.line_2_scores?.converted?.I) - 5" fill="#b45309" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_2_scores?.converted?.I || 0).toFixed(1)"></text>
                                            <text x="135" :y="calcY(gradingData.disc_result.line_2_scores?.converted?.S) - 5" fill="#b45309" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_2_scores?.converted?.S || 0).toFixed(1)"></text>
                                            <text x="185" :y="calcY(gradingData.disc_result.line_2_scores?.converted?.C) - 5" fill="#b45309" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_2_scores?.converted?.C || 0).toFixed(1)"></text>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Graph 3: CHANGE -->
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-900/60 text-center space-y-2">
                                    <span class="text-[10px] font-bold text-indigo-600 uppercase">Graph 3 (CHANGE) - Mirror / Perceived</span>
                                    <div class="bg-indigo-50/40 dark:bg-slate-950/40 rounded-lg p-1.5 border border-indigo-100 dark:border-indigo-950">
                                        <svg viewBox="0 0 220 160" class="w-full h-36">
                                            <!-- Grid Lines -->
                                            <line x1="20" y1="15" x2="205" y2="15" stroke="#6366f1" stroke-dasharray="2" stroke-opacity="0.3" />
                                            <text x="5" y="18" fill="#9ca3af" font-size="8" font-family="monospace">+8</text>

                                            <line x1="20" y1="80" x2="205" y2="80" stroke="#6366f1" stroke-width="1.5" stroke-opacity="0.8" />
                                            <text x="5" y="83" fill="#6366f1" font-size="9" font-weight="bold" font-family="monospace">0</text>

                                            <line x1="20" y1="145" x2="205" y2="145" stroke="#6366f1" stroke-dasharray="2" stroke-opacity="0.3" />
                                            <text x="5" y="148" fill="#9ca3af" font-size="8" font-family="monospace">-8</text>

                                            <!-- Axes & Labels -->
                                            <line x1="35" y1="10" x2="35" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="35" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">D</text>

                                            <line x1="85" y1="10" x2="85" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="85" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">I</text>

                                            <line x1="135" y1="10" x2="135" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="135" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">S</text>

                                            <line x1="185" y1="10" x2="185" y2="148" stroke="#cbd5e1" stroke-dasharray="2" stroke-opacity="0.4" />
                                            <text x="185" y="157" fill="#64748b" font-size="9" font-weight="bold" text-anchor="middle">C</text>

                                            <!-- Polyline -->
                                            <polyline fill="none" stroke="#4f46e5" stroke-width="2.2" :points="getPolyline(gradingData.disc_result.line_3_scores?.converted)" stroke-linecap="round" stroke-linejoin="round" />

                                            <!-- Value Labels -->
                                            <text x="35" :y="calcY(gradingData.disc_result.line_3_scores?.converted?.D) - 5" fill="#4338ca" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_3_scores?.converted?.D || 0).toFixed(1)"></text>
                                            <text x="85" :y="calcY(gradingData.disc_result.line_3_scores?.converted?.I) - 5" fill="#4338ca" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_3_scores?.converted?.I || 0).toFixed(1)"></text>
                                            <text x="135" :y="calcY(gradingData.disc_result.line_3_scores?.converted?.S) - 5" fill="#4338ca" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_3_scores?.converted?.S || 0).toFixed(1)"></text>
                                            <text x="185" :y="calcY(gradingData.disc_result.line_3_scores?.converted?.C) - 5" fill="#4338ca" font-size="8" font-weight="bold" text-anchor="middle" x-text="parseFloat(gradingData.disc_result.line_3_scores?.converted?.C || 0).toFixed(1)"></text>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Description (High Contrast Premium Card) -->
                            <template x-if="gradingData.disc_result.disc_profile">
                                <div class="p-4 rounded-xl bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-950 text-white border border-indigo-500/40 shadow-md text-xs space-y-1.5">
                                    <div class="flex items-center gap-2 pb-1.5 border-b border-indigo-800/60">
                                        <span class="p-1 rounded bg-indigo-600 text-white text-[10px]">📖</span>
                                        <span class="text-[10px] font-black text-indigo-300 uppercase tracking-wider">Deskripsi Kepribadian:</span>
                                    </div>
                                    <p class="text-indigo-100/90 leading-relaxed font-normal pt-0.5" x-text="gradingData.disc_result.disc_profile.general_description || 'Analisis kepribadian pelamar berhasil dibentuk.'"></p>
                                </div>
                            </template>

                            <!-- Suitable Jobs Recommendation (Standar Internasional) -->
                            <template x-if="gradingData.disc_result.disc_profile && gradingData.disc_result.disc_profile.suitable_jobs">
                                <div class="rounded-xl overflow-hidden border border-amber-400/70 dark:border-amber-500/50 shadow-sm text-xs">
                                    <div class="bg-gradient-to-r from-amber-400 via-amber-300 to-yellow-400 dark:from-amber-500 dark:via-amber-400 dark:to-yellow-500 py-1.5 px-3 text-center">
                                        <h5 class="text-xs font-black text-gray-950 uppercase tracking-wide flex items-center justify-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-gray-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span>Profesi yang cocok :</span>
                                        </h5>
                                    </div>
                                    <div class="p-3 bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-100 font-medium leading-relaxed text-center" x-text="gradingData.disc_result.disc_profile.suitable_jobs"></div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Banner jika DISC tapi belum ada profil -->
                    <template x-if="gradingData.is_disc && !gradingData.disc_result">
                        <div class="my-4 p-4 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/80 text-amber-800 dark:text-amber-300 text-xs flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="font-bold text-amber-900 dark:text-amber-200">Hasil Analisis Profil Belum Terbentuk</p>
                                <p class="mt-0.5 text-amber-700 dark:text-amber-300">
                                    Tes ini merupakan Tes Kepribadian (DISC) tanpa penilaian skor/angka. Hasil profil kepribadian belum terbentuk karena butir jawaban soal tidak lengkap atau tidak tersimpan pada sesi ujian ini.
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- ===== PAPI KOSTICK RESULT BLOCK (Khusus Pelamar) ===== -->
                    <template x-if="gradingData.is_papi && gradingData.papi_result">
                        <div class="my-4 space-y-6">

                            <!-- Header PAPI & Validitas -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-200 dark:border-slate-700 gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white uppercase tracking-wider">
                                            Hasil PAPI Kostick Pelamar
                                        </span>
                                        <template x-if="gradingData.papi_result.is_valid">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200 dark:border-emerald-800">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Valid (Total Atas & Bawah = 45)
                                            </span>
                                        </template>
                                        <template x-if="!gradingData.papi_result.is_valid">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300 text-[11px] font-bold border border-rose-200 dark:border-rose-800">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Perlu Cek (Atas: <span x-text="gradingData.papi_result.role_score"></span>, Bawah: <span x-text="gradingData.papi_result.need_score"></span>)
                                            </span>
                                        </template>
                                    </div>
                                    <h4 class="text-base font-bold text-gray-900 dark:text-white mt-1">
                                        Lembar Evaluasi Profil Kepribadian PAPI Kostick
                                    </h4>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a :href="'{{ $isRecruiter ? '/recruiter/test-evaluations/' : '/admin/test-evaluations/' }}' + gradingData.id + '/papi-pdf'"
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100/80 hover:bg-amber-200 dark:bg-amber-900/60 dark:hover:bg-amber-800 text-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-700 rounded-xl text-xs font-semibold shadow-2xs transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Buka Lembar PDF PAPI</span>
                                    </a>
                                    <a :href="'{{ $isRecruiter ? '/recruiter/test-evaluations/' : '/admin/test-evaluations/' }}' + gradingData.id + '/papi-pdf?download=1'"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-semibold shadow-sm transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Unduh PDF</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Info Biodata Peserta -->
                            <template x-if="gradingData.participant_name">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                    <div class="p-2.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/60">
                                        <span class="block text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-0.5">Nama</span>
                                        <span class="font-bold text-gray-900 dark:text-white" x-text="gradingData.participant_name || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/60">
                                        <span class="block text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-0.5">Usia</span>
                                        <span class="font-bold text-gray-900 dark:text-white" x-text="(gradingData.participant_age ? gradingData.participant_age + ' Tahun' : '-')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/60">
                                        <span class="block text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-0.5">Jenis Kelamin</span>
                                        <span class="font-bold text-gray-900 dark:text-white" x-text="gradingData.participant_gender === 'male' ? 'Laki-laki' : (gradingData.participant_gender === 'female' ? 'Perempuan' : '-')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-900/60">
                                        <span class="block text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-0.5">Tanggal Tes</span>
                                        <span class="font-bold text-gray-900 dark:text-white" x-text="gradingData.test_date ? gradingData.test_date.substring(0, 10) : '-'"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- 1. LEMBAR JAWABAN (RIWAYAT JAWABAN 90 BUTIR) -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="text-xs font-bold text-gray-800 dark:text-slate-100 flex items-center gap-1.5 uppercase tracking-wide">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        Riwayat Jawaban (Lembar Jawaban PAPI Kostick)
                                    </h5>
                                    <span class="text-[11px] text-gray-400">90 Butir Soal (Pilihan a/b)</span>
                                </div>
                                <div class="overflow-x-auto rounded-lg border border-gray-400 dark:border-slate-600 shadow-xs inline-block min-w-full">
                                    <table class="w-full text-xs border-collapse">
                                        <tbody>
                                            <template x-for="(row, ri) in getPapiSheetRows()" :key="ri">
                                                <tr>
                                                    <template x-for="(cell, ci) in row" :key="ci">
                                                        <td :class="cell.isNum ? 'bg-amber-100 dark:bg-amber-950/70 text-gray-900 dark:text-amber-100 font-bold' : 'bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-200 font-semibold'"
                                                            class="text-center border border-gray-400 dark:border-slate-600 py-1.5 px-2 text-xs w-9 sm:w-10"
                                                            x-text="cell.text"></td>
                                                    </template>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 2. TABEL INTERPRETASI (20 FAKTOR KEPRIBADIAN) -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="text-xs font-bold text-gray-800 dark:text-slate-100 flex items-center gap-1.5 uppercase tracking-wide">
                                        <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                                        Tabel Interpretasi Hasil PAPI Kostick
                                    </h5>
                                    <span class="text-[11px] text-gray-400">20 Faktor Kepribadian</span>
                                </div>
                                <div class="overflow-x-auto rounded-lg border border-gray-400 dark:border-slate-600 shadow-xs">
                                    <table class="w-full text-xs border-collapse">
                                        <thead>
                                            <tr class="bg-amber-200 dark:bg-amber-900/60 text-gray-900 dark:text-amber-100 font-bold">
                                                <th class="py-2 px-3 text-center border border-gray-400 dark:border-slate-600 w-28 uppercase">ASPEK</th>
                                                <th class="py-2 px-3 text-center border border-gray-400 dark:border-slate-600 uppercase">FAKTOR</th>
                                                <th class="py-2 px-2 text-center border border-gray-400 dark:border-slate-600 w-16 uppercase">FAKTOR</th>
                                                <th class="py-2 px-2 text-center border border-gray-400 dark:border-slate-600 w-14 uppercase">NILAI</th>
                                                <th class="py-2 px-3 text-center border border-gray-400 dark:border-slate-600 uppercase">INTERPRETASI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($papiAspects as $aspectGroup)
                                                @foreach ($aspectGroup['factors'] as $fIndex => $f)
                                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/40 transition">
                                                        @if ($fIndex === 0)
                                                            <td rowspan="{{ count($aspectGroup['factors']) }}"
                                                                class="bg-cyan-200 dark:bg-cyan-950/70 text-gray-900 dark:text-cyan-200 font-bold text-center border border-gray-400 dark:border-slate-600 py-2 px-3 text-xs align-middle">
                                                                {{ $aspectGroup['name'] }}
                                                            </td>
                                                        @endif
                                                        <td class="bg-white dark:bg-slate-900 text-gray-800 dark:text-slate-200 border border-gray-400 dark:border-slate-600 py-1.5 px-3 text-xs">
                                                            {{ $f['name'] }}
                                                        </td>
                                                        <td class="bg-yellow-200 dark:bg-yellow-500/30 text-gray-900 dark:text-yellow-200 font-bold text-center border border-gray-400 dark:border-slate-600 py-1.5 px-2 text-xs w-16">
                                                            {{ $f['code'] }}
                                                        </td>
                                                        <td class="bg-white dark:bg-slate-900 text-gray-900 dark:text-white font-bold text-center border border-gray-400 dark:border-slate-600 py-1.5 px-2 text-xs w-14"
                                                            x-text="getPapiScore('{{ $f['code'] }}')">
                                                        </td>
                                                        <td class="bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 border border-gray-400 dark:border-slate-600 py-1.5 px-3 text-xs leading-relaxed"
                                                            x-text="getPapiInterpretation('{{ $f['code'] }}')">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Total Atas & Total Bawah Box -->
                                <div class="mt-4 flex flex-col gap-1 max-w-sm">
                                    <table class="border-collapse text-xs">
                                        <tbody>
                                            <tr>
                                                <td class="bg-amber-200 dark:bg-amber-900/60 text-gray-900 dark:text-amber-100 font-bold py-1.5 px-4 border border-gray-400 dark:border-slate-600 text-right w-36">
                                                    Total Atas
                                                </td>
                                                <td class="bg-cyan-100 dark:bg-cyan-950/60 text-gray-900 dark:text-cyan-200 font-extrabold py-1.5 px-3 border border-gray-400 dark:border-slate-600 text-center w-14"
                                                    x-text="(gradingData.papi_result && gradingData.papi_result.role_score) !== undefined ? gradingData.papi_result.role_score : 0">
                                                </td>
                                                <td class="py-1.5 px-3 font-bold text-rose-600 dark:text-rose-400 text-xs">
                                                    (Harus 45)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="bg-amber-200 dark:bg-amber-900/60 text-gray-900 dark:text-amber-100 font-bold py-1.5 px-4 border border-gray-400 dark:border-slate-600 text-right w-36">
                                                    Total Bawah
                                                </td>
                                                <td class="bg-cyan-100 dark:bg-cyan-950/60 text-gray-900 dark:text-cyan-200 font-extrabold py-1.5 px-3 border border-gray-400 dark:border-slate-600 text-center w-14"
                                                    x-text="(gradingData.papi_result && gradingData.papi_result.need_score) !== undefined ? gradingData.papi_result.need_score : 0">
                                                </td>
                                                <td class="py-1.5 px-3 font-bold text-rose-600 dark:text-rose-400 text-xs">
                                                    (Harus 45)
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </template>

                    <!-- Banner jika PAPI tapi belum ada hasil -->
                    <template x-if="gradingData.is_papi && !gradingData.papi_result">
                        <div class="my-4 p-4 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/80 text-amber-800 dark:text-amber-300 text-xs flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="font-bold text-amber-900 dark:text-amber-200">Hasil PAPI Kostick Belum Terbentuk</p>
                                <p class="mt-0.5 text-amber-700 dark:text-amber-300">
                                    Tes ini merupakan Tes Kepribadian PAPI Kostick, namun hasil interpretasi belum tersedia. Pastikan pelamar telah menyelesaikan seluruh 90 butir soal.
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- Score Card Header (Untuk Tes Objektif / Essay Non-DISC / Non-PAPI) -->
                    <template x-if="!gradingData.disc_result && !gradingData.is_disc && !gradingData.is_papi">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 my-4">
                            <div class="p-3 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800">
                                <span class="block text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 uppercase">Skor Pilihan Ganda</span>
                                <span class="text-base font-extrabold text-indigo-900 dark:text-indigo-200" x-text="gradingData.objective_score"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-amber-50/60 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800">
                                <span class="block text-[10px] font-semibold text-amber-600 dark:text-amber-400 uppercase">Skor Essay</span>
                                <span class="text-base font-extrabold text-amber-900 dark:text-amber-200" x-text="gradingData.essay_score"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800">
                                <span class="block text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Total Skor Akhir</span>
                                <span class="text-base font-extrabold text-emerald-900 dark:text-emerald-200" x-text="gradingData.total_score"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
                                <span class="block text-[10px] font-semibold text-gray-500 uppercase">KKM Minimum</span>
                                <span class="text-base font-extrabold text-gray-800 dark:text-slate-200" x-text="gradingData.passing_score + '%'"></span>
                            </div>
                        </div>
                    </template>

                    <!-- Form Penilaian Essay -->
                    <form :action="'{{ $gradeActionPrefix }}' + gradingData.id + '/grade'" method="POST" @submit="isSubmittingGrading = true" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <template x-if="!gradingData.is_papi">
                            <div class="max-h-96 overflow-y-auto space-y-4 pr-1">
                                <template x-if="getGroupedQuestions().length === 0">
                                    <div class="py-10 text-center text-gray-400 dark:text-slate-500 bg-gray-50/50 dark:bg-slate-800/30 rounded-2xl border border-dashed border-gray-200 dark:border-slate-700/60">
                                        <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-xs font-medium text-gray-600 dark:text-slate-400">Tidak ada data jawaban yang tersimpan untuk sesi pengerjaan ujian ini.</p>
                                        <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-0.5">Kandidat mungkin menyelesaikan ujian sebelum butir soal diisi atau durasi waktu habis.</p>
                                    </div>
                                </template>
                                <template x-for="(item, index) in getGroupedQuestions()" :key="item.question_id || index">
                                    <div class="p-4 rounded-xl border transition" :class="item.question_type === 'essay' ? 'bg-amber-50/30 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/60' : (item.question_type === 'disc' || item.most_answer || item.least_answer ? 'bg-purple-50/40 dark:bg-purple-950/20 border-purple-200 dark:border-purple-800/60' : 'bg-gray-50/50 dark:bg-slate-800/40 border-gray-200 dark:border-slate-700')">
                                        
                                        <div class="flex items-start justify-between gap-3 mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-lg bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300 font-bold text-xs flex items-center justify-center" x-text="index + 1"></span>
                                                
                                                <template x-if="item.question_type === 'multiple_choice'">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                                        Pilihan Ganda
                                                    </span>
                                                </template>
                                                <template x-if="item.question_type === 'essay'">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                                                        Essay / Uraian
                                                    </span>
                                                </template>
                                                <template x-if="item.question_type === 'disc' || item.most_answer || item.least_answer">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                                                        Nomor DISC (P & K)
                                                    </span>
                                                </template>

                                                <template x-if="item.question_type !== 'disc' && !item.most_answer && !item.least_answer">
                                                    <span class="text-xs font-bold text-gray-500 dark:text-slate-400" x-text="'(Bobot Max: ' + (item.points || 1) + ' Poin)'"></span>
                                                </template>
                                            </div>

                                            <!-- Reviewer badge if already reviewed -->
                                            <template x-if="item.single_answer && item.single_answer.reviewer">
                                                <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Dinilai oleh: <span x-text="item.single_answer.reviewer.name"></span>
                                                </span>
                                            </template>
                                        </div>

                                        <!-- Pertanyaan -->
                                        <p class="text-xs font-bold text-gray-900 dark:text-white mb-2" x-text="item.question ? item.question.question : ('Nomor Soal ' + (index + 1))"></p>

                                        <!-- 1. Tipe Pilihan Ganda Display -->
                                        <template x-if="item.question_type === 'multiple_choice' && item.single_answer">
                                            <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-xs space-y-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-gray-500 dark:text-slate-400">Jawaban Pelamar:</span>
                                                    <span class="font-bold" :class="item.single_answer.option && item.single_answer.option.is_correct ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" x-text="item.single_answer.option ? item.single_answer.option.option_text : 'Tidak Dijawab'"></span>
                                                </div>
                                                <div class="flex items-center justify-between pt-1 border-t border-gray-100 dark:border-slate-800">
                                                    <span class="text-gray-500 dark:text-slate-400">Nilai Otomatis:</span>
                                                    <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="(item.single_answer.score || 0) + ' Poin'"></span>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- 2. Tipe DISC (1 Soal = P dan K) -->
                                        <template x-if="item.question_type === 'disc' || item.most_answer || item.least_answer">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                                <!-- Paling Sesuai (P) -->
                                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border-2 border-emerald-300 dark:border-emerald-700/70 shadow-2xs space-y-1">
                                                    <div class="flex items-center justify-between">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-extrabold text-[10px]">
                                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                            P (Paling Sesuai / Most)
                                                        </span>
                                                        <span class="text-[10px] font-bold text-gray-500 dark:text-slate-400" x-text="item.most_answer?.option?.most_tag || item.most_answer?.option?.attribute_tag ? 'Dimensi: ' + (item.most_answer?.option?.most_tag || item.most_answer?.option?.attribute_tag) : ''"></span>
                                                    </div>
                                                    <p class="font-bold text-gray-800 dark:text-slate-100 text-xs pt-1" x-text="item.most_answer?.option ? item.most_answer.option.option_text : '(Tidak Dipilih)'"></p>
                                                </div>

                                                <!-- Kurang Sesuai (K) -->
                                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border-2 border-rose-300 dark:border-rose-700/70 shadow-2xs space-y-1">
                                                    <div class="flex items-center justify-between">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-extrabold text-[10px]">
                                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                                            K (Kurang Sesuai / Least)
                                                        </span>
                                                        <span class="text-[10px] font-bold text-gray-500 dark:text-slate-400" x-text="item.least_answer?.option?.least_tag || item.least_answer?.option?.attribute_tag ? 'Dimensi: ' + (item.least_answer?.option?.least_tag || item.least_answer?.option?.attribute_tag) : ''"></span>
                                                    </div>
                                                    <p class="font-bold text-gray-800 dark:text-slate-100 text-xs pt-1" x-text="item.least_answer?.option ? item.least_answer.option.option_text : '(Tidak Dipilih)'"></p>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- 3. Tipe Essay Grading Box -->
                                        <template x-if="item.question_type === 'essay' && item.single_answer">
                                            <div class="space-y-3">
                                                <div class="p-3 rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-xs">
                                                    <span class="block text-[11px] font-semibold text-gray-400 uppercase mb-1">Jawaban Teks Pelamar:</span>
                                                    <p class="font-medium text-gray-800 dark:text-slate-200 whitespace-pre-line" x-text="item.single_answer.essay_answer || '(Pelamar tidak mengisikan jawaban teks)'"></p>
                                                </div>

                                                <!-- Tautan Terdeteksi (Google Drive / Video dll) -->
                                                <template x-if="item.single_answer.essay_answer && item.single_answer.essay_answer.match(/https?:\/\/[^\s]+/i)">
                                                    <div class="space-y-2 pt-0.5">
                                                        <template x-for="url in (item.single_answer.essay_answer.match(/https?:\/\/[^\s]+/g) || [])" :key="url">
                                                            <div class="flex items-center justify-between p-3 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 shadow-2xs transition">
                                                                <div class="flex items-center gap-3 min-w-0">
                                                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                                        </svg>
                                                                    </div>
                                                                    <div class="min-w-0 truncate">
                                                                        <div class="flex items-center gap-2">
                                                                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                                                                                :class="url.includes('drive.google.com') ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300' : 'bg-indigo-100 dark:bg-indigo-900/60 text-indigo-800 dark:text-indigo-300'"
                                                                                x-text="url.includes('drive.google.com') ? 'Tautan Google Drive' : 'Tautan Terdeteksi'"></span>
                                                                        </div>
                                                                        <span class="text-[11px] text-gray-500 dark:text-slate-400 font-mono truncate block mt-0.5" x-text="url"></span>
                                                                    </div>
                                                                </div>
                                                                <a :href="url" target="_blank"
                                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg shadow-sm transition shrink-0 ml-3">
                                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                    </svg>
                                                                    <span>Buka Tautan</span>
                                                                </a>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>

                                                <!-- Lampiran File Pelamar (Local Storage) -->
                                                <template x-if="item.single_answer.attachment_url">
                                                    <div class="flex items-center justify-between p-3 rounded-lg bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 text-xs">
                                                        <div class="flex items-center gap-2.5 min-w-0">
                                                            <div class="w-8 h-8 rounded-lg bg-indigo-600/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                                </svg>
                                                            </div>
                                                            <div class="truncate">
                                                                <span class="block font-semibold text-indigo-900 dark:text-indigo-200 truncate" x-text="item.single_answer.attachment_name || 'Lampiran File Jawaban'"></span>
                                                                <span class="text-[10px] text-gray-500 dark:text-slate-400" x-text="(item.single_answer.attachment_size ? (item.single_answer.attachment_size >= 1048576 ? (item.single_answer.attachment_size / 1048576).toFixed(2) + ' MB • ' : Math.round(item.single_answer.attachment_size / 1024) + ' KB • ') : '') + 'Lampiran Tersimpan'"></span>
                                                            </div>
                                                        </div>
                                                        <a :href="item.single_answer.attachment_url" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg shadow-sm transition shrink-0 ml-2">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                            </svg>
                                                            <span>Buka / Unduh File</span>
                                                        </a>
                                                    </div>
                                                </template>

                                                <div class="flex items-center justify-between bg-amber-100/50 dark:bg-amber-950/40 p-3 rounded-lg border border-amber-200 dark:border-amber-800/80">
                                                    <label :for="'score_' + item.single_answer.id" class="text-xs font-bold text-amber-900 dark:text-amber-200">
                                                        Beri Nilai Skor (Max: <span x-text="item.points || 1"></span>):
                                                    </label>
                                                    <div class="flex items-center gap-2">
                                                        <input type="number" step="0.5" min="0" :max="item.points || 1" :name="'essay_scores[' + item.single_answer.id + ']'" :id="'score_' + item.single_answer.id" :value="item.single_answer.score !== null ? item.single_answer.score : ''" required placeholder="0" class="w-24 px-3 py-1 text-xs rounded-lg bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 text-gray-900 dark:text-white font-bold text-center focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                                        <span class="text-xs font-semibold text-amber-800 dark:text-amber-300">Poin</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- KEPUTUSAN STATUS LAMARAN OLEH HR (ONE-STOP DECISION) -->
                        <div class="mt-6 p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/60 space-y-3.5">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                        ✓
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-white">
                                            Tindakan & Keputusan Status Lamaran
                                        </h4>
                                        <p class="text-[11px] text-gray-500 dark:text-slate-400">
                                            Perbarui status lamaran kandidat secara langsung setelah evaluasi ujian selesai
                                        </p>
                                    </div>
                                </div>

                                <template x-if="gradingData.applicant_email">
                                    <span class="inline-flex items-center gap-1 text-[11px] text-indigo-600 dark:text-indigo-400 font-medium bg-white dark:bg-slate-900 px-2.5 py-1 rounded-lg border border-indigo-200 dark:border-indigo-800 shadow-2xs">
                                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span x-text="gradingData.applicant_email"></span>
                                    </span>
                                </template>
                            </div>

                            <!-- Pilihan Cepat / Template Pesan -->
                            <div class="space-y-1.5 pt-1">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-semibold text-gray-600 dark:text-slate-400 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <span>Pilihan Cepat / Template Keputusan:</span>
                                    </span>
                                    <button type="button" @click="gradingData.application_notes = ''" class="text-gray-400 hover:text-rose-500 transition text-[10px]">
                                        Kosongkan
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" @click="applyTemplate('Reviewed')" class="px-2 py-1 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 border border-amber-200 dark:border-amber-800/80 rounded-lg text-[10px] font-semibold text-amber-800 dark:text-amber-300 transition flex items-center gap-1">
                                        <span>Lolos Berkas & Lanjut Tes</span>
                                    </button>
                                    <button type="button" @click="applyTemplate('Shortlisted')" class="px-2 py-1 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 border border-indigo-200 dark:border-indigo-800/80 rounded-lg text-[10px] font-semibold text-indigo-700 dark:text-indigo-300 transition flex items-center gap-1">
                                        <span>Lolos Ujian / Shortlisted</span>
                                    </button>
                                    <button type="button" @click="applyTemplate('Interview')" class="px-2 py-1 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/60 border border-blue-200 dark:border-blue-800/80 rounded-lg text-[10px] font-semibold text-blue-700 dark:text-blue-300 transition flex items-center gap-1">
                                        <span>Wawancara</span>
                                    </button>
                                    <button type="button" @click="applyTemplate('Accepted')" class="px-2 py-1 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800/80 rounded-lg text-[10px] font-semibold text-emerald-700 dark:text-emerald-300 transition flex items-center gap-1">
                                        <span>Diterima</span>
                                    </button>
                                    <button type="button" @click="applyTemplate('Rejected')" class="px-2 py-1 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800/80 rounded-lg text-[10px] font-semibold text-rose-700 dark:text-rose-300 transition flex items-center gap-1">
                                        <span>Ditolak</span>
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label for="eval_app_status" class="block text-[11px] font-bold text-gray-700 dark:text-slate-300 mb-1">
                                        Pilih Status Lamaran:
                                    </label>
                                    <select name="application_status" id="eval_app_status" x-model="gradingData.application_status" @change="onStatusChange($event.target.value)" class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                        <option value="Reviewed">Reviewed (Lolos Berkas / Tahap Tes)</option>
                                        <option value="Shortlisted">Shortlisted (Lolos Ujian / Siap Wawancara)</option>
                                        <option value="Interview">Interview (Wawancara)</option>
                                        <option value="Accepted">Accepted (Diterima)</option>
                                        <option value="Rejected">Rejected (Ditolak)</option>
                                    </select>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label for="eval_app_notes" class="block text-[11px] font-bold text-gray-700 dark:text-slate-300">
                                            Catatan / Feedback Evaluasi:
                                        </label>
                                        <button type="button" @click="applyTemplate()" class="text-[10px] text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-0.5">
                                            <span>Terapkan Pesan</span>
                                        </button>
                                    </div>
                                    <textarea name="application_notes" id="eval_app_notes" rows="2" x-model="gradingData.application_notes" placeholder="Tambahkan catatan evaluasi untuk pelamar ini..." class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition leading-relaxed"></textarea>
                                </div>
                            </div>

                            <!-- Opsi Kirim Email Notifikasi Otomatis -->
                            <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-indigo-100 dark:border-indigo-900/60 shadow-2xs">
                                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                    <input type="hidden" name="send_email" value="0">
                                    <input type="checkbox" name="send_email" value="1" x-model="gradingData.send_email" class="w-4 h-4 mt-0.5 text-indigo-600 rounded border-gray-300 dark:border-slate-600 focus:ring-indigo-500 transition">
                                    <div class="space-y-0.5">
                                        <span class="text-xs font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span>Kirim notifikasi email otomatis ke pelamar</span>
                                        </span>
                                        <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-tight">
                                            Pelamar akan menerima email resmi berisi pembaruan status hasil evaluasi, pesan catatan di atas, dan langkah berikutnya.
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showGradingModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingGrading" class="px-5 py-2.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-500/20 transition flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                <svg x-show="isSubmittingGrading" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmittingGrading ? 'Menyimpan...' : 'Simpan Evaluasi & Perbarui Status'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
