@php
    $isAdmin = auth()->check() && (auth()->user()->role_id == 1 || strtolower(auth()->user()->role?->name ?? '') === 'admin');
    $isRecruiter = $isRecruiter ?? (auth()->check() && (auth()->user()->role_id == 2 || strtolower(auth()->user()->role?->name ?? '') === 'recruiter' || (bool) auth()->user()->is_recruiter));
    $formActionPrefix = ($isRecruiter && !$isAdmin) ? url('/recruiter/applicants') . '/' : url('/admin/applicants') . '/';
@endphp

<div class="space-y-6" x-data="{ 
    showDetailModal: false,
    showStatusModal: false,
    isSubmittingStatus: false,
    detailData: {},
    activeDetailTab: 'pribadi',
    statusTemplates: {
        'Reviewed': 'Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.',
        'Partial Approved': 'Lamaran Anda telah disetujui pada salah satu tahap verifikasi (Partial Approved) dan sedang dalam proses peninjauan akhir.',
        'Shortlisted': 'Selamat! Anda dinyatakan lolos seluruh tahap seleksi berkas (Double Approved) dan masuk ke dalam daftar kandidat terpilih (Shortlisted). Kami akan segera menginformasikan jadwal wawancara.',
        'Interview': 'Anda diundang untuk mengikuti tahap wawancara. Silakan periksa jadwal dan informasi meeting yang tertera.',
        'Accepted': 'Selamat! Anda dinyatakan DITERIMA untuk bergabung bersama kami. Tim HR akan segera menghubungi Anda terkait proses offering dan onboarding.',
        'Rejected': 'Terima kasih atas partisipasi Anda. Saat ini kualifikasi Anda belum sesuai dengan kriteria yang kami butuhkan. Tetap semangat dan sukses untuk kesempatan berikutnya.',
        'Submitted': 'Lamaran Anda telah kami terima dan sedang dalam proses seleksi berkas oleh tim rekruter.'
    },
    statusData: {
        id: '',
        applicant_name: '',
        applicant_email: '',
        job_title: '',
        status: '',
        notes: '',
        recruiter_approval: 'pending',
        recruiter_notes: '',
        admin_approval: 'pending',
        admin_notes: '',
        test_recruiter_approval: 'pending',
        test_recruiter_notes: '',
        test_recruiter_name: '',
        test_admin_approval: 'pending',
        test_admin_notes: '',
        test_admin_name: '',
        interview_recruiter_approval: 'pending',
        interview_recruiter_notes: '',
        interview_recruiter_name: '',
        interview_admin_approval: 'pending',
        interview_admin_notes: '',
        interview_admin_name: '',
        approval_mode: '{{ ($isRecruiter && !$isAdmin) ? "recruiter_review" : "admin_review" }}',
        decision: 'approved',
        send_email: true
    },
    modalImgFailed: false,
    getApplicantPhotoUrl(app) {
        if (!app || !app.applicant_profile) return null;
        const prof = app.applicant_profile;
        if (prof.photo_url) return prof.photo_url;
        const raw = prof.photo || (prof.user ? prof.user.avatar : null);
        if (raw) {
            return (raw.startsWith('http://') || raw.startsWith('https://')) ? raw : ('/storage/' + raw);
        }
        return null;
    },
    getDrivingLicenses(app) {
        if (!app || !app.applicant_profile || !app.applicant_profile.driving_licenses) return [];
        let lics = app.applicant_profile.driving_licenses;
        if (typeof lics === 'string') {
            try { lics = JSON.parse(lics); } catch (e) { return []; }
        }
        if (!Array.isArray(lics)) return [];
        return lics.filter(l => l && (l.type || l.number));
    },
    openDetailModal(application) {
        this.detailData = application;
        this.modalImgFailed = false;
        this.activeDetailTab = 'pribadi';
        this.showDetailModal = true;
    },
    openStatusModal(data) {
        const initialStatus = data.status || 'Submitted';
        let initialNotes = data.notes || '';

        // Jika catatan masih kosong, otomatis berikan template sesuai status awal
        if (!initialNotes && this.statusTemplates[initialStatus]) {
            initialNotes = this.statusTemplates[initialStatus];
        }

        const isRecruiterOnly = {{ ($isRecruiter && !$isAdmin) ? 'true' : 'false' }};
        let defaultMode = isRecruiterOnly ? 'recruiter_review' : 'admin_review';
        if (initialStatus === 'Interview') {
            defaultMode = isRecruiterOnly ? 'interview_recruiter_review' : 'interview_admin_review';
        } else if (initialStatus === 'Reviewed' || initialStatus === 'Shortlisted') {
            defaultMode = isRecruiterOnly ? 'test_recruiter_review' : 'test_admin_review';
        } else if (!isRecruiterOnly && initialStatus === 'Accepted') {
            defaultMode = 'direct';
        }

        let defaultDecision = 'approved';
        if (isRecruiterOnly) {
            if (initialStatus === 'Interview' && data.interview_recruiter_approval === 'rejected') {
                defaultDecision = 'rejected';
            } else if ((initialStatus === 'Reviewed' || initialStatus === 'Shortlisted') && data.test_recruiter_approval === 'rejected') {
                defaultDecision = 'rejected';
            } else if (data.recruiter_approval === 'rejected') {
                defaultDecision = 'rejected';
            }
        } else {
            if (initialStatus === 'Interview' && data.interview_admin_approval === 'rejected') {
                defaultDecision = 'rejected';
            } else if ((initialStatus === 'Reviewed' || initialStatus === 'Shortlisted') && data.test_admin_approval === 'rejected') {
                defaultDecision = 'rejected';
            } else if (data.admin_approval === 'rejected') {
                defaultDecision = 'rejected';
            }
        }

        this.statusData = {
            id: data.id,
            applicant_name: data.applicant_name || (data.applicant_profile ? data.applicant_profile.full_name : 'Pelamar'),
            applicant_email: data.applicant_email || (data.applicant_profile && data.applicant_profile.user ? data.applicant_profile.user.email : ''),
            job_title: data.job_title || (data.job ? data.job.title : 'Lowongan'),
            job_quota: Number(data.job_quota || (data.job ? (data.job.quota || 0) : 0)),
            job_accepted_count: Number(data.job_accepted_count !== undefined ? data.job_accepted_count : (data.job ? (data.job.accepted_count || 0) : 0)),
            job_remaining_quota: Number(data.job_remaining_quota !== undefined ? data.job_remaining_quota : (data.job ? (data.job.remaining_quota || 0) : 0)),
            job_is_quota_full: Boolean(data.job_is_quota_full !== undefined ? data.job_is_quota_full : (data.job ? (data.job.is_quota_full || false) : false)),
            status: initialStatus,
            initial_status: initialStatus,
            notes: initialNotes,
            recruiter_approval: data.recruiter_approval || 'pending',
            recruiter_notes: data.recruiter_notes || '',
            admin_approval: data.admin_approval || 'pending',
            admin_notes: data.admin_notes || '',
            test_recruiter_approval: data.test_recruiter_approval || 'pending',
            test_recruiter_notes: data.test_recruiter_notes || '',
            test_recruiter_name: (data.test_recruiter ? data.test_recruiter.name : (data.testRecruiter ? data.testRecruiter.name : '')),
            test_admin_approval: data.test_admin_approval || 'pending',
            test_admin_notes: data.test_admin_notes || '',
            test_admin_name: (data.test_admin ? data.test_admin.name : (data.testAdmin ? data.testAdmin.name : '')),
            interview_recruiter_approval: data.interview_recruiter_approval || 'pending',
            interview_recruiter_notes: data.interview_recruiter_notes || '',
            interview_recruiter_name: (data.interview_recruiter ? data.interview_recruiter.name : (data.interviewRecruiter ? data.interviewRecruiter.name : '')),
            interview_admin_approval: data.interview_admin_approval || 'pending',
            interview_admin_notes: data.interview_admin_notes || '',
            interview_admin_name: (data.interview_admin ? data.interview_admin.name : (data.interviewAdmin ? data.interviewAdmin.name : '')),
            approval_mode: defaultMode,
            decision: defaultDecision,
            send_email: true
        };
        this.showStatusModal = true;
    },
    isAcceptedAction() {
        if (this.statusData.approval_mode === 'interview_admin_review') {
            return this.statusData.decision === 'override_accepted' || 
                   (this.statusData.decision === 'approved' && this.statusData.interview_recruiter_approval === 'approved');
        }
        if (this.statusData.approval_mode === 'interview_recruiter_review') {
            return this.statusData.decision === 'approved' && this.statusData.interview_admin_approval === 'approved';
        }
        return this.statusData.status === 'Accepted';
    },
    setDecision(dec, targetTemplateKey = null) {
        this.statusData.decision = dec;
        if (targetTemplateKey && this.statusTemplates[targetTemplateKey]) {
            const currentNotes = (this.statusData.notes || '').trim();
            const isExistingTemplate = Object.values(this.statusTemplates).some(t => t.trim() === currentNotes);
            if (!currentNotes || isExistingTemplate) {
                this.statusData.notes = this.statusTemplates[targetTemplateKey];
            }
        }
    },
    applyTemplate(statusKey = null) {
        if (statusKey && this.statusData.approval_mode === 'direct') {
            this.statusData.status = statusKey;
        }
        let target = statusKey;
        if (!target) {
            if (this.statusData.approval_mode === 'direct') {
                target = this.statusData.status;
            } else if (this.statusData.decision === 'rejected') {
                target = 'Rejected';
            } else if (this.statusData.decision === 'override_reviewed') {
                target = 'Reviewed';
            } else if (this.statusData.decision === 'override_shortlisted') {
                target = 'Shortlisted';
            } else if (this.statusData.decision === 'override_accepted') {
                target = 'Accepted';
            } else if (this.statusData.approval_mode === 'admin_review') {
                target = (this.statusData.recruiter_approval === 'approved') ? 'Reviewed' : 'Partial Approved';
            } else if (this.statusData.approval_mode === 'test_admin_review' && this.statusData.test_recruiter_approval === 'approved') {
                target = 'Shortlisted';
            } else if (this.statusData.approval_mode === 'interview_admin_review' && this.statusData.interview_recruiter_approval === 'approved') {
                target = 'Accepted';
            } else {
                target = this.statusData.status;
            }
        }
        if (this.statusTemplates[target]) {
            this.statusData.notes = this.statusTemplates[target];
        }
    },
    getSubmitButtonText() {
        if (this.isSubmittingStatus) return 'Menyimpan...';
        const mode = this.statusData.approval_mode;
        const dec = this.statusData.decision;
        if (mode === 'recruiter_review') return 'Kirim Rekomendasi';
        if (mode === 'admin_review') {
            if (dec === 'override_reviewed') return 'Eksekusi Override (Lolos Berkas)';
            if (dec === 'rejected') return 'Tolak Berkas';
            return 'Simpan Keputusan HR';
        }
        if (mode === 'test_recruiter_review') return 'Simpan Rekomendasi Teknis';
        if (mode === 'test_admin_review') {
            if (dec === 'override_shortlisted') return 'Eksekusi Override (Lolos Ujian)';
            if (dec === 'rejected') return 'Tolak Hasil Ujian';
            return 'Simpan Keputusan HR';
        }
        if (mode === 'interview_recruiter_review') return 'Simpan Evaluasi Wawancara';
        if (mode === 'interview_admin_review') {
            if (dec === 'override_accepted') return 'Eksekusi Override (Diterima)';
            if (dec === 'rejected') return 'Tolak Kandidat';
            return 'Simpan Persetujuan HR';
        }
        return 'Simpan Perubahan Status';
    },
    onStatusChange(newStatus) {
        const currentNotes = (this.statusData.notes || '').trim();
        const isExistingTemplate = Object.values(this.statusTemplates).some(t => t.trim() === currentNotes);

        // Jika catatan kosong atau masih berupa template bawaan sebelumnya, gantikan otomatis dengan template status baru
        if (!currentNotes || isExistingTemplate) {
            if (this.statusTemplates[newStatus]) {
                this.statusData.notes = this.statusTemplates[newStatus];
            }
        }
    },
    formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    },
    formatDateTime(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const pad = (n) => n < 10 ? '0' + n : n;
        return pad(d.getDate()) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear() + ', ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ' WIB';
    },
    formatCurrency(val) {
        if (!val || isNaN(val)) return '-';
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
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
    <!-- Header, Quick Tabs & Filter Section -->
    <div class="relative z-20 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 shadow-sm rounded-2xl p-4 sm:p-5 space-y-4">
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3.5 pb-3 border-b border-gray-100 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex flex-wrap items-center gap-2">
                    <span>Daftar Lamaran Kerja Masuk</span>
                    @php
                        $pendingTotal = ($stats['submitted'] ?? 0) + ($stats['partial_approved'] ?? 0);
                    @endphp
                    @if($pendingTotal > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                            {{-- <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> --}}
                            {{ $pendingTotal }} Perlu Diproses
                        </span>
                    @endif
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Kelola dan seleksi pelamar dari tahap pendaftaran hingga diterima.</p>
            </div>

            <!-- Quick Status Horizontal Pill Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1.5 xl:pb-0 -mx-4 px-4 sm:mx-0 sm:px-0 custom-scrollbar touch-pan-x">
                @php
                    $tabs = [
                        '' => ['label' => 'Semua', 'count' => $stats['total'] ?? 0],
                        'need_review' => ['label' => 'Perlu Review', 'count' => $pendingTotal],
                        'Submitted' => ['label' => 'Diajukan', 'count' => $stats['submitted'] ?? 0],
                        'Partial Approved' => ['label' => 'Partial Approved', 'count' => $stats['partial_approved'] ?? 0],
                        'Reviewed' => ['label' => 'Lolos Berkas', 'count' => $stats['reviewed'] ?? 0],
                        'Shortlisted' => ['label' => 'Lolos Ujian', 'count' => $stats['shortlisted'] ?? 0],
                        'Interview' => ['label' => 'Wawancara', 'count' => $stats['interview'] ?? 0],
                        'Accepted' => ['label' => 'Diterima', 'count' => $stats['accepted'] ?? 0],
                        'Rejected' => ['label' => 'Ditolak', 'count' => $stats['rejected'] ?? 0],
                    ];
                @endphp
                @foreach($tabs as $tabKey => $tabData)
                    <button type="button" 
                            wire:click="setStatusFilter('{{ $tabKey }}')"
                            class="px-2.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0 {{ (strtolower($statusFilter) === strtolower($tabKey) && empty($selectedStatuses)) ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700/80 border border-gray-200/60 dark:border-slate-700/60' }}">
                        <span>{{ $tabData['label'] }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ (strtolower($statusFilter) === strtolower($tabKey) && empty($selectedStatuses)) ? 'bg-white/20 text-white' : 'bg-gray-200/70 dark:bg-slate-700 text-gray-700 dark:text-slate-300' }}">
                            {{ $tabData['count'] }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
        
        <!-- Filter Controls Toolbar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <!-- Search and Select Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-1 lg:items-center gap-2.5">
                <!-- Search Input (Full width on mobile, max-w-xs on desktop) -->
                <div class="relative sm:col-span-2 lg:col-span-1 lg:flex-1 lg:max-w-xs">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           placeholder="Cari pelamar, email, NIK, posisi..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Company Filter -->
                <div class="relative w-full lg:w-52">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <select wire:model.live="companyFilter" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">Semua Perusahaan</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Job Filter -->
                <div class="relative w-full lg:w-56">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <select wire:model.live="jobFilter" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">{{ $companyFilter ? 'Semua Lowongan di Perusahaan Ini' : 'Semua Lowongan' }}</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job->id }}">{{ $job->title }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Checklist Status & Atur Kolom) -->
            <div class="flex flex-wrap items-center gap-2 pt-1 lg:pt-0">
                <!-- Multi-Checklist Status Filter Dropdown -->
                <div x-data="{ open: false }" class="relative flex-1 sm:flex-initial">
                    <button @click="open = !open" 
                            type="button" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700/80 transition focus:outline-none cursor-pointer shadow-2xs">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="whitespace-nowrap">Checklist Status</span>
                        @if(!empty($selectedStatuses))
                            <span class="px-1.5 py-0.5 text-[10px] font-bold bg-indigo-600 text-white rounded-full shrink-0">
                                {{ count($selectedStatuses) }}
                            </span>
                        @endif
                        <svg class="w-3.5 h-3.5 text-gray-400 ml-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" 
                         @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         x-cloak
                         class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-56 max-w-[calc(100vw-2.5rem)] rounded-2xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 shadow-xl z-50 p-3 space-y-2">
                        
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-slate-800">
                            <span class="text-xs font-bold text-gray-900 dark:text-white">Filter Multi-Status</span>
                            @if(!empty($selectedStatuses))
                                <button wire:click="$set('selectedStatuses', [])" class="text-[10px] text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Reset
                                </button>
                            @endif
                        </div>

                        <div class="space-y-1.5 max-h-48 overflow-y-auto custom-scrollbar">
                            @foreach([
                                'Submitted'        => 'Diajukan',
                                'Partial Approved' => 'Partial Approved',
                                'Reviewed'         => 'Lolos Berkas',
                                'Shortlisted'      => 'Lolos Ujian',
                                'Interview'        => 'Wawancara',
                                'Accepted'         => 'Diterima',
                                'Rejected'         => 'Ditolak'
                            ] as $val => $label)
                                <label class="flex items-center gap-2.5 px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-slate-800/60 rounded-xl cursor-pointer transition text-xs text-gray-700 dark:text-slate-300">
                                    <input type="checkbox" 
                                           value="{{ $val }}" 
                                           wire:model.live="selectedStatuses"
                                           class="w-3.5 h-3.5 rounded border-gray-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500">
                                    <span class="truncate">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Dynamic Columns Toggle Dropdown (Desktop & Tablet) -->
                <div x-data="{ open: false }" class="relative flex-1 sm:flex-initial hidden sm:block">
                    <button @click="open = !open" 
                            type="button" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 py-2 text-xs rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition focus:outline-none font-semibold cursor-pointer">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                        <span class="whitespace-nowrap">Atur Kolom</span>
                        <span class="px-1.5 py-0.5 text-[10px] font-bold bg-indigo-600 text-white rounded-full shrink-0">
                            {{ count($selectedColumns) }}
                        </span>
                        <svg class="w-3.5 h-3.5 text-indigo-400 ml-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" 
                         @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         x-cloak
                         class="absolute right-0 mt-2 w-60 max-w-[calc(100vw-2.5rem)] rounded-2xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 shadow-xl z-50 p-3 space-y-2">
                        
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-slate-800">
                            <span class="text-xs font-bold text-gray-900 dark:text-white">Tampilkan Kolom</span>
                            <div class="flex gap-2 text-[10px]">
                                <button wire:click="selectAllColumns" class="text-indigo-600 dark:text-indigo-400 hover:underline">Semua</button>
                                <span class="text-gray-300 dark:text-slate-700">•</span>
                                <button wire:click="resetColumnSelection" class="text-gray-500 hover:underline">Reset</button>
                            </div>
                        </div>

                        <div class="space-y-1.5 max-h-56 overflow-y-auto custom-scrollbar">
                            @foreach([
                                'applicant' => 'Pelamar (Nama & Email)',
                                'contact' => 'Kontak & NIK',
                                'job' => 'Posisi & Perusahaan',
                                'applied_at' => 'Tanggal Melamar',
                                'status' => 'Status & Catatan',
                                'actions' => 'Aksi'
                            ] as $colKey => $colLabel)
                                <label class="flex items-center gap-2.5 px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-slate-800/60 rounded-xl cursor-pointer transition text-xs text-gray-700 dark:text-slate-300">
                                    <input type="checkbox" 
                                           value="{{ $colKey }}" 
                                           wire:model.live="selectedColumns"
                                           class="w-3.5 h-3.5 rounded border-gray-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500">
                                    <span>{{ $colLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if(!empty($selectedStatuses) || $statusFilter || $companyFilter || $jobFilter || $search || $sortField !== 'id')
                    <button wire:click="resetAllFilters" class="w-full sm:w-auto px-3 py-2 text-xs rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition font-medium cursor-pointer text-center justify-center">
                        Reset Filter
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="relative bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Livewire Loading Overlay -->
        <div wire:loading wire:target="search, statusFilter, sortField, sortDirection, sortBy, selectedStatuses, selectedColumns, previousPage, nextPage, gotoPage" class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center z-10 transition">
            <div class="flex items-center gap-2.5 px-4 py-2.5 bg-slate-900/90 dark:bg-slate-800/90 text-white rounded-xl shadow-xl text-xs font-semibold">
                <svg class="animate-spin w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memuat data...</span>
            </div>
        </div>

        <!-- Mobile Card List View (Visible on mobile screens < 768px) -->
        <div class="block md:hidden divide-y divide-gray-100 dark:divide-slate-800">
            @forelse ($applications as $app)
                @php
                    $cvUrl = $app->applicantProfile?->cv_file_url ?? ($app->applicantProfile?->cv_file_path ? asset('storage/' . $app->applicantProfile->cv_file_path) : null);
                @endphp
                <div class="p-4 space-y-3 hover:bg-gray-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <!-- Top Row: Candidate Profile Info & Status Badge -->
                    <div class="flex items-start justify-between gap-2.5">
                        <div class="flex items-center gap-3 min-w-0">
                            @php
                                $applicantPhoto = $app->applicantProfile?->photo_url;
                                $applicantName = $app->applicantProfile?->full_name ?? 'Pelamar';
                                $applicantInitials = strtoupper(substr($applicantName, 0, 2));
                            @endphp
                            <div class="relative w-11 h-11 shrink-0">
                                @if ($applicantPhoto)
                                    <img src="{{ $applicantPhoto }}" 
                                         alt="{{ $applicantName }}" 
                                         referrerpolicy="no-referrer" 
                                         class="w-11 h-11 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-sm"
                                         onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                    <div class="hidden w-11 h-11 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs items-center justify-center shadow-2xs">
                                        {{ $applicantInitials }}
                                    </div>
                                @else
                                    <div class="w-11 h-11 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shadow-2xs">
                                        {{ $applicantInitials }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-gray-900 dark:text-white text-sm truncate max-w-[170px] sm:max-w-xs">{{ $app->applicantProfile->full_name ?? 'Pelamar' }}</span>
                                    @if ($app->status === 'Submitted' || \Carbon\Carbon::parse($app->applied_at)->greaterThanOrEqualTo(now()->subHours(48)))
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-[9.5px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-300/60 shrink-0">
                                            Baru
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-gray-500 dark:text-slate-400 block truncate">{{ $app->applicantProfile->user->email ?? '-' }}</span>
                                @if($app->applicantProfile && $app->applicantProfile->phone)
                                    <span class="text-[10.5px] text-gray-400 dark:text-slate-500 block">{{ $app->applicantProfile->phone }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="shrink-0">
                            @if ($app->status === 'Accepted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    Diterima
                                </span>
                            @elseif ($app->status === 'Rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                    Ditolak
                                </span>
                            @elseif ($app->status === 'Interview')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                    Wawancara
                                </span>
                            @elseif ($app->status === 'Shortlisted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                    Lolos Ujian
                                </span>
                            @elseif ($app->status === 'Partial Approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                    Partial Approved
                                </span>
                            @elseif ($app->status === 'Reviewed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                    Lolos Berkas
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                    Diajukan
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Job Position & Details Container -->
                    <div class="bg-gray-50/80 dark:bg-slate-800/40 rounded-xl p-3 border border-gray-100 dark:border-slate-800 space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-xs text-gray-900 dark:text-white truncate">{{ $app->job->title ?? 'Lowongan' }}</span>
                            @if ($app->job && $app->job->company)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/60 shrink-0">
                                    {{ $app->job->company->name }}
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-y-1 text-[11px] text-gray-500 dark:text-slate-400 pt-1 border-t border-gray-200/60 dark:border-slate-700/60">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @if ($app->job && $app->job->department)
                                    <span class="text-gray-600 dark:text-slate-400 font-medium">{{ $app->job->department->name }}</span>
                                @endif
                                @if($app->applicantProfile && $app->applicantProfile->nik)
                                    <span class="text-gray-400 dark:text-slate-500">• NIK: {{ $app->applicantProfile->nik }}</span>
                                @endif
                            </div>
                            <span class="text-[10.5px] text-gray-400 dark:text-slate-500 font-medium">
                                {{ \Carbon\Carbon::parse($app->applied_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>

                    @if ($app->notes)
                        <div class="px-3 py-2 rounded-xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40 text-[11px] text-amber-800 dark:text-amber-300 italic leading-relaxed">
                            "{{ $app->notes }}"
                        </div>
                    @endif

                    <!-- Touch-Friendly Action Buttons -->
                    <div class="flex items-center gap-2 pt-1">
                        @if($cvUrl)
                            <a href="{{ $cvUrl }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-rose-600 dark:text-rose-400 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 dark:hover:bg-rose-900/60 transition border border-rose-200/80 dark:border-rose-900/60 text-xs font-semibold cursor-pointer shadow-2xs" title="Buka Dokumen CV">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span>CV</span>
                            </a>
                        @endif

                        <button type="button" @click="openDetailModal({{ \Illuminate\Support\Js::from($app) }})" class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-gray-700 dark:text-slate-200 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition border border-gray-200 dark:border-slate-700 text-xs font-semibold cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Detail</span>
                        </button>

                        <button type="button" @click="openStatusModal({{ \Illuminate\Support\Js::from([
                            'id'                 => $app->id,
                            'applicant_name'     => $app->applicantProfile->full_name ?? 'Pelamar',
                            'applicant_email'    => $app->applicantProfile->user->email ?? '',
                            'job_title'          => $app->job->title ?? 'Lowongan',
                            'job_quota'          => (int) ($app->job->quota ?? 0),
                            'job_accepted_count' => (int) ($app->job->accepted_count ?? 0),
                            'job_remaining_quota'=> (int) ($app->job->remaining_quota ?? 0),
                            'job_is_quota_full'  => (bool) ($app->job->is_quota_full ?? false),
                            'status'             => $app->status ?? 'Submitted',
                            'notes'              => $app->notes ?? '',
                            'recruiter_approval' => $app->recruiter_approval ?? 'pending',
                            'recruiter_notes'    => $app->recruiter_notes ?? '',
                            'admin_approval'     => $app->admin_approval ?? 'pending',
                            'admin_notes'        => $app->admin_notes ?? '',
                        ]) }})" class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-white bg-emerald-600 hover:bg-emerald-500 transition shadow-sm text-xs font-semibold cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Proses</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center text-gray-400 dark:text-slate-500">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <svg class="w-10 h-10 text-gray-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        @if(!empty($isDesignatedRecruiter) && ($assignedJobsCount ?? 0) === 0)
                            <span class="text-sm font-bold text-gray-800 dark:text-slate-200">Belum Ada Lowongan yang Ditugaskan ke Anda</span>
                            <span class="text-xs text-gray-500 dark:text-slate-400 max-w-sm">Hak Recruiter Anda sudah aktif. Namun Admin HR belum memilih Anda sebagai Reviewer pada lowongan kerja mana pun. Setelah ditugaskan pada lowongan, pelamar posisi tersebut akan muncul di sini.</span>
                        @else
                            <span class="text-sm font-medium">Belum ada data lamaran kerja</span>
                            <span class="text-xs">Lamaran yang diajukan oleh pelamar akan muncul di sini.</span>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View (Visible on md: screens and above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 text-gray-500 dark:text-slate-400 uppercase tracking-wider font-semibold">
                        @if(in_array('applicant', $selectedColumns))
                            <th class="px-6 py-4 cursor-pointer select-none hover:text-indigo-600 dark:hover:text-indigo-400 transition" wire:click="sortBy('applicant')">
                                <div class="flex items-center gap-1.5">
                                    <span>Pelamar</span>
                                    @if($sortField === 'applicant')
                                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                            @endif
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 text-gray-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                        @endif
                        @if(in_array('contact', $selectedColumns))
                            <th class="px-6 py-4">Kontak & NIK</th>
                        @endif
                        @if(in_array('job', $selectedColumns))
                            <th class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <button type="button" wire:click="sortBy('position')" class="flex items-center gap-1 cursor-pointer select-none hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        <span>Posisi</span>
                                        @if($sortField === 'position')
                                            <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                @if($sortDirection === 'asc')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                @endif
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 text-gray-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        @endif
                                    </button>
                                    <span class="text-gray-300 dark:text-slate-700">|</span>
                                    <button type="button" wire:click="sortBy('company')" class="flex items-center gap-1 cursor-pointer select-none hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        <span>Perusahaan</span>
                                        @if($sortField === 'company')
                                            <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                @if($sortDirection === 'asc')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                @endif
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 text-gray-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                            </svg>
                                        @endif
                                    </button>
                                </div>
                            </th>
                        @endif
                        @if(in_array('applied_at', $selectedColumns))
                            <th class="px-6 py-4 cursor-pointer select-none hover:text-indigo-600 dark:hover:text-indigo-400 transition" wire:click="sortBy('applied_at')">
                                <div class="flex items-center gap-1.5">
                                    <span>Tanggal Melamar</span>
                                    @if($sortField === 'applied_at')
                                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                            @endif
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 text-gray-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                        @endif
                        @if(in_array('status', $selectedColumns))
                            <th class="px-6 py-4 cursor-pointer select-none hover:text-indigo-600 dark:hover:text-indigo-400 transition" wire:click="sortBy('status')">
                                <div class="flex items-center gap-1.5">
                                    <span>Status & Catatan</span>
                                    @if($sortField === 'status')
                                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                            @endif
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 text-gray-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    @endif
                                </div>
                            </th>
                        @endif
                        @if(in_array('actions', $selectedColumns))
                            <th class="px-6 py-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-gray-700 dark:text-slate-300">
                    @forelse ($applications as $app)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            @if(in_array('applicant', $selectedColumns))
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $applicantPhoto = $app->applicantProfile?->photo_url;
                                            $applicantName = $app->applicantProfile?->full_name ?? 'Pelamar';
                                            $applicantInitials = strtoupper(substr($applicantName, 0, 2));
                                        @endphp
                                        <div class="relative w-10 h-10 shrink-0">
                                            @if ($applicantPhoto)
                                                <img src="{{ $applicantPhoto }}" 
                                                     alt="{{ $applicantName }}" 
                                                     referrerpolicy="no-referrer" 
                                                     class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-sm"
                                                     onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                                <div class="hidden w-10 h-10 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs items-center justify-center shadow-2xs">
                                                    {{ $applicantInitials }}
                                                </div>
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shadow-2xs">
                                                    {{ $applicantInitials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $app->applicantProfile->full_name ?? 'Pelamar' }}</span>
                                                @if ($app->status === 'Submitted' || \Carbon\Carbon::parse($app->applied_at)->greaterThanOrEqualTo(now()->subHours(48)))
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-300/60" title="Lamaran baru diajukan">
                                                        Baru
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[11px] text-gray-500 dark:text-slate-400 block mt-0.5">{{ $app->applicantProfile->user->email ?? '-' }}</span>
                                            @if($app->applicantProfile && $app->applicantProfile->phone)
                                                <span class="text-[10.5px] text-gray-400 dark:text-slate-500 block">{{ $app->applicantProfile->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            @endif

                            @if(in_array('contact', $selectedColumns))
                                <td class="px-6 py-4">
                                    <div>
                                        <span class="font-medium text-gray-800 dark:text-slate-200 block">{{ $app->applicantProfile->phone ?? '-' }}</span>
                                        <span class="text-[11px] text-gray-400 block">NIK: {{ $app->applicantProfile->nik ?? '-' }}</span>
                                    </div>
                                </td>
                            @endif

                            @if(in_array('job', $selectedColumns))
                                <td class="px-6 py-4">
                                    <div>
                                        <span class="font-semibold text-gray-800 dark:text-slate-200 block text-xs">{{ $app->job->title ?? 'Lowongan' }}</span>
                                        <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                            @if ($app->job && $app->job->company)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/60">
                                                    {{ $app->job->company->name }}
                                                </span>
                                            @endif
                                            @if ($app->job && $app->job->department)
                                                <span class="text-[11px] text-gray-400 dark:text-slate-500">
                                                    {{ $app->job->department->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            @endif

                            @if(in_array('applied_at', $selectedColumns))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <span class="text-gray-900 dark:text-white font-medium text-xs block">
                                            {{ \Carbon\Carbon::parse($app->applied_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                                        </span>
                                        <span class="text-[10.5px] text-gray-400 dark:text-slate-500 block">
                                            {{ \Carbon\Carbon::parse($app->applied_at)->timezone('Asia/Jakarta')->diffForHumans() }}
                                        </span>
                                    </div>
                                </td>
                            @endif

                            @if(in_array('status', $selectedColumns))
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        @php
                                            $appStatus = $app->status;
                                            $isOverride = str_contains($app->notes ?? '', 'Admin Override')
                                                || str_contains($app->recruiter_notes ?? '', 'Admin Override')
                                                || str_contains($app->admin_notes ?? '', 'Admin Override')
                                                || str_contains($app->test_recruiter_notes ?? '', 'Admin Override')
                                                || str_contains($app->test_admin_notes ?? '', 'Admin Override')
                                                || str_contains($app->interview_recruiter_notes ?? '', 'Admin Override')
                                                || str_contains($app->interview_admin_notes ?? '', 'Admin Override');
                                        @endphp

                                        @if ($appStatus === 'Accepted')
                                            @if ($isOverride)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800 shadow-2xs" title="Diterima melalui Admin Override / Emergency Approval">
                                                    <span>Diterima (Override)</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800" title="Double Approved oleh Recruiter & HR">
                                                    <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Diterima (Double Approved)</span>
                                                </span>
                                            @endif
                                        @elseif ($appStatus === 'Rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                Ditolak
                                            </span>
                                        @elseif ($appStatus === 'Interview')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                                Wawancara
                                            </span>
                                        @elseif ($appStatus === 'Shortlisted')
                                            @if ($isOverride)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800 shadow-2xs" title="Lolos Ujian melalui Admin Override (Hak Veto)">
                                                    <span>Lolos Ujian (Override)</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                                    Lolos Ujian
                                                </span>
                                            @endif
                                        @elseif ($appStatus === 'Partial Approved')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                                Partial Approved
                                            </span>
                                        @elseif ($appStatus === 'Reviewed')
                                            @if ($isOverride)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800 shadow-2xs" title="Lolos Berkas melalui Admin Override (Hak Veto)">
                                                    <span>Lolos Berkas (Override)</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                                    Lolos Berkas
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                                Diajukan
                                            </span>
                                        @endif

                                        @if($appStatus === 'Interview' || $appStatus === 'Accepted')
                                            <!-- Tahap 3 (Pasca Wawancara) Double Approval Indicator -->
                                            <div class="flex items-center gap-1.5 mt-1 text-[10.5px]">
                                                <span class="text-[10px] text-gray-400 font-semibold">T3 Wawancara:</span>
                                                <span class="inline-flex items-center gap-0.5 font-medium {{ $app->interview_recruiter_approval === 'approved' ? 'text-emerald-600 dark:text-emerald-400' : ($app->interview_recruiter_approval === 'rejected' ? 'text-rose-500' : 'text-amber-600 dark:text-amber-400') }}" title="Kelayakan Recruiter: {{ ucfirst($app->interview_recruiter_approval) }}{{ $app->interview_recruiter_notes ? ' - ' . $app->interview_recruiter_notes : '' }}">
                                                    <span class="text-[9.5px]">Recruiter</span>
                                                    @if($app->interview_recruiter_approval === 'approved')
                                                        <span class="font-bold text-emerald-600">✓</span>
                                                    @elseif($app->interview_recruiter_approval === 'rejected')
                                                        <span class="font-bold text-rose-500">✕</span>
                                                    @else
                                                        <span class="font-bold text-amber-500">⏳</span>
                                                    @endif
                                                </span>
                                                <span class="text-gray-300 dark:text-slate-700">•</span>
                                                <span class="inline-flex items-center gap-0.5 font-medium {{ $app->interview_admin_approval === 'approved' ? 'text-emerald-600 dark:text-emerald-400' : ($app->interview_admin_approval === 'rejected' ? 'text-rose-500' : 'text-amber-600 dark:text-amber-400') }}" title="Persetujuan HR: {{ ucfirst($app->interview_admin_approval) }}{{ $app->interview_admin_notes ? ' - ' . $app->interview_admin_notes : '' }}">
                                                    <span class="text-[9.5px]">HR</span>
                                                    @if($app->interview_admin_approval === 'approved')
                                                        <span class="font-bold text-emerald-600">✓</span>
                                                    @elseif($app->interview_admin_approval === 'rejected')
                                                        <span class="font-bold text-rose-500">✕</span>
                                                    @else
                                                        <span class="font-bold text-amber-500">⏳</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @elseif($appStatus === 'Reviewed' || $appStatus === 'Shortlisted' || $app->test_recruiter_approval !== 'pending' || $app->test_admin_approval !== 'pending')
                                            <!-- Tahap 2 (Ujian Online & Psikotes) Double Approval Indicator -->
                                            <div class="flex items-center gap-1.5 mt-1 text-[10.5px]">
                                                <span class="text-[10px] text-gray-400 font-semibold">T2 Ujian:</span>
                                                <span class="inline-flex items-center gap-0.5 font-medium {{ $app->test_recruiter_approval === 'approved' ? 'text-emerald-600 dark:text-emerald-400' : ($app->test_recruiter_approval === 'rejected' ? 'text-rose-500' : 'text-amber-600 dark:text-amber-400') }}" title="Recruiter Teknis: {{ ucfirst($app->test_recruiter_approval) }}{{ $app->test_recruiter_notes ? ' - ' . $app->test_recruiter_notes : '' }}">
                                                    <span class="text-[9.5px]">Teknis</span>
                                                    @if($app->test_recruiter_approval === 'approved')
                                                        <span class="font-bold text-emerald-600">✓</span>
                                                    @elseif($app->test_recruiter_approval === 'rejected')
                                                        <span class="font-bold text-rose-500">✕</span>
                                                    @else
                                                        <span class="font-bold text-amber-500">⏳</span>
                                                    @endif
                                                </span>
                                                <span class="text-gray-300 dark:text-slate-700">•</span>
                                                <span class="inline-flex items-center gap-0.5 font-medium {{ $app->test_admin_approval === 'approved' ? 'text-emerald-600 dark:text-emerald-400' : ($app->test_admin_approval === 'rejected' ? 'text-rose-500' : 'text-amber-600 dark:text-amber-400') }}" title="HR Verifikasi: {{ ucfirst($app->test_admin_approval) }}{{ $app->test_admin_notes ? ' - ' . $app->test_admin_notes : '' }}">
                                                    <span class="text-[9.5px]">HR</span>
                                                    @if($app->test_admin_approval === 'approved')
                                                        <span class="font-bold text-emerald-600">✓</span>
                                                    @elseif($app->test_admin_approval === 'rejected')
                                                        <span class="font-bold text-rose-500">✕</span>
                                                    @else
                                                        <span class="font-bold text-amber-500">⏳</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @else
                                            <!-- Tahap 1 (Berkas Administrasi) Double Approval Indicator -->
                                            <div class="flex items-center gap-1.5 mt-1 text-[10.5px]">
                                                <span class="text-[10px] text-gray-400 font-semibold">T1 Berkas:</span>
                                                <span class="inline-flex items-center gap-0.5 font-medium {{ $app->recruiter_approval === 'approved' ? 'text-emerald-600 dark:text-emerald-400' : ($app->recruiter_approval === 'rejected' ? 'text-rose-500' : 'text-gray-400 dark:text-slate-500') }}" title="Verifikasi Recruiter: {{ ucfirst($app->recruiter_approval) }}{{ $app->recruiter_notes ? ' - ' . $app->recruiter_notes : '' }}">
                                                    <span class="text-[9.5px]">Recruiter</span>
                                                    @if($app->recruiter_approval === 'approved')
                                                        <span class="font-bold text-emerald-600">✓</span>
                                                    @elseif($app->recruiter_approval === 'rejected')
                                                        <span class="font-bold text-rose-500">✕</span>
                                                    @else
                                                        <span class="font-bold text-amber-500">⏳</span>
                                                    @endif
                                                </span>
                                                <span class="text-gray-300 dark:text-slate-700">•</span>
                                                <span class="inline-flex items-center gap-0.5 font-medium {{ $app->admin_approval === 'approved' ? 'text-emerald-600 dark:text-emerald-400' : ($app->admin_approval === 'rejected' ? 'text-rose-500' : 'text-gray-400 dark:text-slate-500') }}" title="Verifikasi HR/Admin: {{ ucfirst($app->admin_approval) }}{{ $app->admin_notes ? ' - ' . $app->admin_notes : '' }}">
                                                    <span class="text-[9.5px]">HR</span>
                                                    @if($app->admin_approval === 'approved')
                                                        <span class="font-bold text-emerald-600">✓</span>
                                                    @elseif($app->admin_approval === 'rejected')
                                                        <span class="font-bold text-rose-500">✕</span>
                                                    @else
                                                        <span class="font-bold text-amber-500">⏳</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endif

                                        @if ($app->notes)
                                            <p class="text-[11px] text-gray-400 dark:text-slate-500 italic truncate max-w-xs" title="{{ $app->notes }}">
                                                "{{ $app->notes }}"
                                            </p>
                                        @endif
                                    </div>
                                </td>
                            @endif

                            @if(in_array('actions', $selectedColumns))
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @php
                                            $cvUrl = $app->applicantProfile?->cv_file_url ?? ($app->applicantProfile?->cv_file_path ? asset('storage/' . $app->applicantProfile->cv_file_path) : null);
                                        @endphp
                                        @if($cvUrl)
                                            <!-- Quick CV Document Preview Link -->
                                            <a href="{{ $cvUrl }}" target="_blank" class="px-2 py-1.5 rounded-xl text-rose-600 dark:text-rose-400 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/50 dark:hover:bg-rose-900/60 transition-colors border border-rose-200/80 dark:border-rose-900/60 flex items-center gap-1 text-[11px] font-semibold cursor-pointer" title="Buka Dokumen CV Pelamar (PDF)">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                <span>CV</span>
                                            </a>
                                        @endif

                                        <!-- View Detail Profile Modal Button -->
                                        <button @click="openDetailModal({{ \Illuminate\Support\Js::from($app) }})" class="p-1.5 rounded-xl text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors border border-transparent hover:border-gray-200 dark:hover:border-slate-700 cursor-pointer" title="Lihat Profil Lengkap">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <!-- Update Status Button -->
                                        <button @click="openStatusModal({{ \Illuminate\Support\Js::from([
                                            'id'                           => $app->id,
                                            'applicant_name'               => $app->applicantProfile->full_name ?? 'Pelamar',
                                            'applicant_email'              => $app->applicantProfile->user->email ?? '',
                                            'job_title'                    => $app->job->title ?? 'Lowongan',
                                            'job_quota'                    => (int) ($app->job->quota ?? 0),
                                            'job_accepted_count'           => (int) ($app->job->accepted_count ?? 0),
                                            'job_remaining_quota'          => (int) ($app->job->remaining_quota ?? 0),
                                            'job_is_quota_full'            => (bool) ($app->job->is_quota_full ?? false),
                                            'status'                       => $app->status ?? 'Submitted',
                                            'notes'                        => $app->notes ?? '',
                                            'recruiter_approval'           => $app->recruiter_approval ?? 'pending',
                                            'recruiter_notes'              => $app->recruiter_notes ?? '',
                                            'admin_approval'               => $app->admin_approval ?? 'pending',
                                            'admin_notes'                  => $app->admin_notes ?? '',
                                            'test_recruiter_approval'      => $app->test_recruiter_approval ?? 'pending',
                                            'test_recruiter_notes'         => $app->test_recruiter_notes ?? '',
                                            'test_recruiter'               => $app->testRecruiter ? ['name' => $app->testRecruiter->name] : null,
                                            'test_admin_approval'          => $app->test_admin_approval ?? 'pending',
                                            'test_admin_notes'             => $app->test_admin_notes ?? '',
                                            'test_admin'                   => $app->testAdmin ? ['name' => $app->testAdmin->name] : null,
                                            'interview_recruiter_approval' => $app->interview_recruiter_approval ?? 'pending',
                                            'interview_recruiter_notes'    => $app->interview_recruiter_notes ?? '',
                                            'interview_recruiter'          => $app->interviewRecruiter ? ['name' => $app->interviewRecruiter->name] : null,
                                            'interview_admin_approval'     => $app->interview_admin_approval ?? 'pending',
                                            'interview_admin_notes'        => $app->interview_admin_notes ?? '',
                                            'interview_admin'              => $app->interviewAdmin ? ['name' => $app->interviewAdmin->name] : null,
                                        ]) }})" class="px-2.5 py-1.5 rounded-xl text-emerald-700 dark:text-emerald-300 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 transition-colors border border-emerald-200/80 dark:border-emerald-800/80 flex items-center gap-1 text-[11px] font-semibold cursor-pointer" title="Update Status Lamaran">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span>Proses</span>
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ max(count($selectedColumns), 1) }}" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    @if(!empty($isDesignatedRecruiter) && ($assignedJobsCount ?? 0) === 0)
                                        <span class="text-sm font-bold text-gray-800 dark:text-slate-200">Belum Ada Lowongan yang Ditugaskan ke Anda</span>
                                        <span class="text-xs text-gray-500 dark:text-slate-400 max-w-md">Hak Recruiter Anda sudah aktif. Namun Admin HR belum memilih Anda sebagai Reviewer pada lowongan kerja mana pun. Setelah ditugaskan pada lowongan, pelamar posisi tersebut akan muncul di sini.</span>
                                    @else
                                        <span class="text-sm font-medium">Belum ada data lamaran kerja</span>
                                        <span class="text-xs">Lamaran yang diajukan oleh pelamar akan muncul di sini.</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($applications->hasPages() || $perPage != 10)
            <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-t border-gray-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center justify-between sm:justify-start w-full sm:w-auto gap-2">
                    <span class="text-xs text-gray-500 dark:text-slate-400">Tampilkan</span>
                    <select wire:model.live="perPage" class="pl-2.5 pr-7 py-1.5 text-xs rounded-lg bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-xs text-gray-500 dark:text-slate-400">data per halaman</span>
                </div>
                @if ($applications->hasPages())
                    <div class="w-full sm:w-auto flex justify-center sm:justify-end overflow-x-auto">
                        {{ $applications->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Detail Applicant Modal (Portrait Document View - Read Only) -->
    @include('livewire.admin.applicants.partials.detail-modal-portrait')

    <!-- Update Status Modal (Update Only) -->
    <div x-show="showStatusModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-status" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-3 sm:p-4 text-center sm:block sm:p-0">
            <div x-show="showStatusModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showStatusModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showStatusModal" 
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                @keydown.escape.window="showStatusModal = false"
                class="inline-block align-bottom sm:align-middle bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl w-full border border-gray-200 dark:border-slate-800 my-auto">
                
                <div class="p-4 sm:p-6">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-3.5 border-b border-gray-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/50">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white leading-tight" id="modal-title-status">
                                    <span x-show="statusData.approval_mode === 'recruiter_review'">Rekomendasi Berkas (Recruiter)</span>
                                    <span x-show="statusData.approval_mode === 'admin_review'">Verifikasi Berkas HR / Admin</span>
                                    <span x-show="statusData.approval_mode === 'test_recruiter_review'">Evaluasi Teknis Ujian (Recruiter)</span>
                                    <span x-show="statusData.approval_mode === 'test_admin_review'">Verifikasi Hasil Ujian HR / Admin</span>
                                    <span x-show="statusData.approval_mode === 'interview_recruiter_review'">Persetujuan Recruiter (Pasca Wawancara)</span>
                                    <span x-show="statusData.approval_mode === 'interview_admin_review'">Persetujuan HR / Admin (Pasca Wawancara)</span>
                                    <span x-show="statusData.approval_mode === 'direct'">Ubah Status Lamaran Langsung</span>
                                </h3>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400">Pembaruan keputusan dan alur seleksi pelamar</p>
                            </div>
                        </div>
                        <button type="button" @click="showStatusModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body Form -->
                    <form :action="'{{ $formActionPrefix }}' + statusData.id" method="POST" @submit="setTimeout(() => isSubmittingStatus = true, 50)" class="mt-4 space-y-3.5">
                        @csrf
                        @method('PUT')

                        <!-- Hidden Approval Type Field -->
                        <input type="hidden" name="approval_type" :value="statusData.approval_mode">

                        <!-- Scrollable Form Body Container -->
                        <div class="max-h-[62vh] overflow-y-auto space-y-3.5 pr-1.5 custom-scrollbar">

                            <!-- Compact Applicant Identity & Workflow Status Strip -->
                            <div class="p-3 rounded-xl bg-gray-50/80 dark:bg-slate-800/60 border border-gray-200/70 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-sm text-gray-900 dark:text-white" x-text="statusData.applicant_name"></span>
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-gray-200/70 dark:bg-slate-700 text-gray-700 dark:text-slate-200 shrink-0" x-text="statusData.initial_status || statusData.status"></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-slate-400 mt-0.5 flex-wrap">
                                        <span class="font-medium text-indigo-600 dark:text-indigo-400" x-text="statusData.job_title"></span>
                                        <template x-if="statusData.applicant_email">
                                            <span class="inline-flex items-center gap-1 text-gray-400 dark:text-slate-500">
                                                <span>&bull;</span>
                                                <span x-text="statusData.applicant_email" class="truncate max-w-[220px]"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Mini Stage Progress Badges -->
                                <div class="flex items-center gap-1.5 shrink-0 flex-wrap">
                                    <!-- Tahap 1: Berkas -->
                                    <template x-if="statusData.status === 'Submitted' || statusData.status === 'Partial Approved' || statusData.approval_mode === 'recruiter_review' || statusData.approval_mode === 'admin_review'">
                                        <div class="flex items-center gap-1.5 text-[11px]">
                                            <span class="px-2 py-0.5 rounded-md border text-[10.5px]" :class="statusData.recruiter_approval === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-medium' : (statusData.recruiter_approval === 'rejected' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 font-medium' : 'bg-white dark:bg-slate-900 border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400')">
                                                Recruiter: <strong x-text="statusData.recruiter_approval === 'approved' ? 'Disetujui' : (statusData.recruiter_approval === 'rejected' ? 'Ditolak' : 'Menunggu')"></strong>
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md border text-[10.5px]" :class="statusData.admin_approval === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-medium' : (statusData.admin_approval === 'rejected' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 font-medium' : 'bg-white dark:bg-slate-900 border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400')">
                                                HR: <strong x-text="statusData.admin_approval === 'approved' ? 'Disetujui' : (statusData.admin_approval === 'rejected' ? 'Ditolak' : 'Menunggu')"></strong>
                                            </span>
                                        </div>
                                    </template>

                                    <!-- Tahap 2: Ujian -->
                                    <template x-if="statusData.status === 'Reviewed' || statusData.status === 'Shortlisted' || statusData.approval_mode === 'test_recruiter_review' || statusData.approval_mode === 'test_admin_review'">
                                        <div class="flex items-center gap-1.5 text-[11px]">
                                            <span class="px-2 py-0.5 rounded-md border text-[10.5px]" :class="statusData.test_recruiter_approval === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-medium' : (statusData.test_recruiter_approval === 'rejected' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 font-medium' : 'bg-white dark:bg-slate-900 border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400')">
                                                Teknis: <strong x-text="statusData.test_recruiter_approval === 'approved' ? 'Lolos' : (statusData.test_recruiter_approval === 'rejected' ? 'Tidak Lolos' : 'Menunggu')"></strong>
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md border text-[10.5px]" :class="statusData.test_admin_approval === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-medium' : (statusData.test_admin_approval === 'rejected' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 font-medium' : 'bg-white dark:bg-slate-900 border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400')">
                                                HR: <strong x-text="statusData.test_admin_approval === 'approved' ? 'Disetujui' : (statusData.test_admin_approval === 'rejected' ? 'Ditolak' : 'Menunggu')"></strong>
                                            </span>
                                        </div>
                                    </template>

                                    <!-- Tahap 3: Wawancara -->
                                    <template x-if="statusData.status === 'Interview' || statusData.status === 'Accepted' || statusData.approval_mode === 'interview_recruiter_review' || statusData.approval_mode === 'interview_admin_review'">
                                        <div class="flex items-center gap-1.5 text-[11px]">
                                            <span class="px-2 py-0.5 rounded-md border text-[10.5px]" :class="statusData.interview_recruiter_approval === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-medium' : (statusData.interview_recruiter_approval === 'rejected' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 font-medium' : 'bg-white dark:bg-slate-900 border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400')">
                                                Recruiter: <strong x-text="statusData.interview_recruiter_approval === 'approved' ? 'Layak' : (statusData.interview_recruiter_approval === 'rejected' ? 'Tidak Layak' : 'Menunggu')"></strong>
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md border text-[10.5px]" :class="statusData.interview_admin_approval === 'approved' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-medium' : (statusData.interview_admin_approval === 'rejected' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 font-medium' : 'bg-white dark:bg-slate-900 border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400')">
                                                HR: <strong x-text="statusData.interview_admin_approval === 'approved' ? 'Disetujui' : (statusData.interview_admin_approval === 'rejected' ? 'Ditolak' : 'Menunggu')"></strong>
                                            </span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Reactive Quota Status Alert -->
                            <template x-if="statusData.job_quota > 0 && isAcceptedAction()">
                                <div class="transition-all duration-200">
                                    <template x-if="statusData.job_is_quota_full">
                                        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-xs flex items-start gap-2.5 shadow-xs">
                                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <div class="space-y-0.5">
                                                <span class="font-bold block text-amber-800 dark:text-amber-300">Pemberitahuan: Target Kuota Posisi Sudah Terpenuhi</span>
                                                <p class="text-[11px] leading-relaxed text-amber-700 dark:text-amber-300/90">
                                                    Target kuota untuk lowongan ini telah terisi penuh (<strong x-text="statusData.job_accepted_count + '/' + statusData.job_quota + ' orang'"></strong>). Menyetujui kandidat ini akan menambah penerimaan melebihi kuota awal. Keputusan tetap berada di tangan Anda.
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!statusData.job_is_quota_full">
                                        <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-between gap-2 shadow-xs">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Status Kuota: <strong x-text="statusData.job_accepted_count + '/' + statusData.job_quota"></strong> kuota telah terisi (Sisa: <strong x-text="statusData.job_remaining_quota + ' orang'"></strong>).</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Subtle Recruiter Feedback Note (if present) -->
                            <template x-if="(statusData.approval_mode === 'test_admin_review' && statusData.test_recruiter_notes) || (statusData.approval_mode === 'interview_admin_review' && statusData.interview_recruiter_notes) || (statusData.approval_mode === 'admin_review' && statusData.recruiter_notes)">
                                <div class="px-3 py-2 rounded-xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-800/50 text-[11px] text-amber-900 dark:text-amber-200 flex items-start gap-2">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    <div>
                                        <span class="font-semibold">Catatan Recruiter:</span>
                                        <span class="italic" x-text="'“' + (statusData.test_recruiter_notes || statusData.interview_recruiter_notes || statusData.recruiter_notes) + '”'"></span>
                                    </div>
                                </div>
                            </template>

                            @if($isAdmin)
                                <!-- Mode Switcher Tabs for Admin -->
                                <div class="flex items-center p-1 bg-gray-100 dark:bg-slate-800 rounded-xl text-xs font-medium">
                                    <template x-if="statusData.initial_status === 'Interview'">
                                        <button type="button" 
                                            @click="statusData.approval_mode = 'interview_admin_review'; statusData.status = statusData.initial_status"
                                            :class="statusData.approval_mode === 'interview_admin_review' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'"
                                            class="flex-1 py-1.5 px-3 rounded-lg transition text-center flex items-center justify-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            <span>Verifikasi HR Wawancara</span>
                                        </button>
                                    </template>
                                    <template x-if="statusData.initial_status === 'Reviewed' || statusData.initial_status === 'Shortlisted'">
                                        <button type="button" 
                                            @click="statusData.approval_mode = 'test_admin_review'; statusData.status = statusData.initial_status"
                                            :class="statusData.approval_mode === 'test_admin_review' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'"
                                            class="flex-1 py-1.5 px-3 rounded-lg transition text-center flex items-center justify-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            <span>Verifikasi HR Ujian</span>
                                        </button>
                                    </template>
                                    <template x-if="statusData.initial_status !== 'Interview' && statusData.initial_status !== 'Reviewed' && statusData.initial_status !== 'Shortlisted'">
                                        <button type="button" 
                                            @click="statusData.approval_mode = 'admin_review'; statusData.status = statusData.initial_status"
                                            :class="statusData.approval_mode === 'admin_review' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'"
                                            class="flex-1 py-1.5 px-3 rounded-lg transition text-center flex items-center justify-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            <span>Verifikasi HR Berkas</span>
                                        </button>
                                    </template>
                                    <button type="button" 
                                        @click="statusData.approval_mode = 'direct'"
                                        :class="statusData.approval_mode === 'direct' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm font-bold' : 'text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white'"
                                        class="flex-1 py-1.5 px-3 rounded-lg transition text-center flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                        <span>Ubah Status Bebas</span>
                                    </button>
                                </div>
                            @endif

                            <!-- Double Approval Review Decision (For Recruiter or Admin Review Mode) -->
                            <div x-show="statusData.approval_mode !== 'direct'" class="space-y-3">
                                <input type="hidden" name="decision" :value="statusData.decision">

                                <!-- Tahap 2: Recruiter Test Review -->
                                <template x-if="statusData.approval_mode === 'test_recruiter_review'">
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">
                                            Keputusan Kelayakan Teknis Ujian <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 gap-2.5">
                                            <button type="button" 
                                                @click="setDecision('approved', 'Reviewed')"
                                                :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 ring-1 ring-emerald-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-emerald-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="font-bold text-xs flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Lolos Tes Teknis
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'approved'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Nilai esai/teknis &ge; KKM. Diteruskan ke HR untuk verifikasi karakter & psikotes.
                                                </p>
                                            </button>

                                            <button type="button" 
                                                @click="setDecision('rejected', 'Rejected')"
                                                :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-50/70 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200 ring-1 ring-rose-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-rose-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="font-bold text-xs flex items-center gap-1.5 text-rose-600 dark:text-rose-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Tidak Lolos Teknis
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'rejected'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Nilai teknis belum mencapai KKM. Diteruskan ke HR untuk konfirmasi penolakan.
                                                </p>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Tahap 2: HR / Admin Test Review -->
                                <template x-if="statusData.approval_mode === 'test_admin_review'">
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">
                                            Keputusan HR / Kuasa Admin Ujian <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                            <!-- Option 1: Persetujuan HR Normal -->
                                            <button type="button" 
                                                @click="setDecision('approved', (statusData.test_recruiter_approval === 'approved' ? 'Shortlisted' : null))"
                                                :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 ring-1 ring-emerald-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-emerald-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer group">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="inline-flex items-center gap-1.5 font-bold text-xs text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span>Setujui HR</span>
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'approved'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Normal. Status otomatis <strong>Shortlisted</strong> jika Recruiter sudah setuju teknis.
                                                </p>
                                            </button>

                                            <!-- Option 2: Admin Override (Hak Veto) -->
                                            <button type="button" 
                                                @click="setDecision('override_shortlisted', 'Shortlisted')"
                                                :class="statusData.decision === 'override_shortlisted' ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 ring-1 ring-amber-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-amber-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer group">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="inline-flex items-center gap-1.5 font-bold text-xs text-amber-600 dark:text-amber-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                        </svg>
                                                        <span>Admin Override</span>
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'override_shortlisted' ? 'border-amber-500 bg-amber-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'override_shortlisted'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Hak Veto. Bypass recruiter & seketika loloskan ke <strong>Shortlisted</strong>.
                                                </p>
                                            </button>

                                            <!-- Option 3: Tolak Hasil Ujian -->
                                            <button type="button" 
                                                @click="setDecision('rejected', 'Rejected')"
                                                :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-50/70 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200 ring-1 ring-rose-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-rose-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer group">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="inline-flex items-center gap-1.5 font-bold text-xs text-rose-600 dark:text-rose-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        <span>Tolak Ujian</span>
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'rejected'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Tolak kandidat pada tahap ini (status seketika <strong>Rejected</strong>).
                                                </p>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Tahap 3: Recruiter Interview Review -->
                                <template x-if="statusData.approval_mode === 'interview_recruiter_review'">
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">
                                            Keputusan Kelayakan Kompetensi Wawancara <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 gap-2.5">
                                            <button type="button" 
                                                @click="setDecision('approved', 'Interview')"
                                                :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 ring-1 ring-emerald-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-emerald-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="font-bold text-xs flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Kompetensi Layak
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'approved'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Kandidat memenuhi kualifikasi tim. Diteruskan ke HR untuk verifikasi offering & kontrak.
                                                </p>
                                            </button>

                                            <button type="button" 
                                                @click="setDecision('rejected', 'Rejected')"
                                                :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-50/70 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200 ring-1 ring-rose-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-rose-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="font-bold text-xs flex items-center gap-1.5 text-rose-600 dark:text-rose-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Tidak Layak
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'rejected'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Kompetensi teknis belum sesuai standar tim kerja.
                                                </p>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Tahap 3: HR / Admin Interview Review -->
                                <template x-if="statusData.approval_mode === 'interview_admin_review'">
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">
                                            Keputusan HR / Kuasa Admin Wawancara <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                            <!-- Option 1: Persetujuan HR Normal -->
                                            <button type="button" 
                                                @click="setDecision('approved', (statusData.interview_recruiter_approval === 'approved' ? 'Accepted' : null))"
                                                :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 ring-1 ring-emerald-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-emerald-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer group">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="inline-flex items-center gap-1.5 font-bold text-xs text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span>Setujui HR</span>
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'approved'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Setujui penawaran. Otomatis <strong>Accepted</strong> jika Recruiter sudah setuju kompetensi.
                                                </p>
                                            </button>

                                            <!-- Option 2: Admin Override (Hak Veto) -->
                                            <button type="button" 
                                                @click="setDecision('override_accepted', 'Accepted')"
                                                :class="statusData.decision === 'override_accepted' ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 ring-1 ring-amber-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-amber-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer group">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="inline-flex items-center gap-1.5 font-bold text-xs text-amber-600 dark:text-amber-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                        </svg>
                                                        <span>Admin Override</span>
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'override_accepted' ? 'border-amber-500 bg-amber-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'override_accepted'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Hak Veto. Bypass recruiter & seketika putuskan <strong>Resmi Diterima (Accepted)</strong>.
                                                </p>
                                            </button>

                                            <!-- Option 3: Tolak Kandidat -->
                                            <button type="button" 
                                                @click="setDecision('rejected', 'Rejected')"
                                                :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-50/70 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200 ring-1 ring-rose-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-rose-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer group">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="inline-flex items-center gap-1.5 font-bold text-xs text-rose-600 dark:text-rose-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        <span>Tolak Kandidat</span>
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'rejected'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Keputusan akhir tolak pelamar (status seketika <strong>Rejected</strong>).
                                                </p>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Tahap 1: Recruiter Berkas Review -->
                                <template x-if="statusData.approval_mode === 'recruiter_review'">
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">
                                            Keputusan Rekomendasi Berkas <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 gap-2.5">
                                            <button type="button" 
                                                @click="setDecision('approved', (statusData.admin_approval === 'approved' ? 'Reviewed' : 'Partial Approved'))"
                                                :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 ring-1 ring-emerald-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-emerald-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="font-bold text-xs flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Rekomendasi Lolos
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'approved'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Kandidat memenuhi kriteria awal CV. Diteruskan ke HR untuk verifikasi administrasi.
                                                </p>
                                            </button>

                                            <button type="button" 
                                                @click="setDecision('rejected', 'Rejected')"
                                                :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-50/70 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200 ring-1 ring-rose-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-rose-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="font-bold text-xs flex items-center gap-1.5 text-rose-600 dark:text-rose-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Rekomendasi Tolak
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'rejected'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Kualifikasi berkas belum sesuai standar kebutuhan lowongan.
                                                </p>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Tahap 1: HR / Admin Berkas Review -->
                                <template x-if="statusData.approval_mode === 'admin_review'">
                                    <div class="space-y-2">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">
                                            Keputusan HR / Kuasa Admin Berkas <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                            <!-- Option 1: Persetujuan HR Normal -->
                                            <button type="button" 
                                                @click="setDecision('approved', (statusData.recruiter_approval === 'approved' ? 'Reviewed' : 'Partial Approved'))"
                                                :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-200 ring-1 ring-emerald-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-emerald-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer group">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="inline-flex items-center gap-1.5 font-bold text-xs text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span>Setujui HR</span>
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'approved' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'approved'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Normal. Status otomatis <strong>Reviewed</strong> jika Recruiter sudah setuju berkas.
                                                </p>
                                            </button>

                                            <!-- Option 2: Admin Override (Hak Veto) -->
                                            <button type="button" 
                                                @click="setDecision('override_reviewed', 'Reviewed')"
                                                :class="statusData.decision === 'override_reviewed' ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 ring-1 ring-amber-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-amber-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer group">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="inline-flex items-center gap-1.5 font-bold text-xs text-amber-600 dark:text-amber-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                        </svg>
                                                        <span>Admin Override</span>
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'override_reviewed' ? 'border-amber-500 bg-amber-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'override_reviewed'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Hak Veto. Bypass recruiter & seketika loloskan ke <strong>Reviewed</strong>.
                                                </p>
                                            </button>

                                            <!-- Option 3: Tolak Berkas -->
                                            <button type="button" 
                                                @click="setDecision('rejected', 'Rejected')"
                                                :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-50/70 dark:bg-rose-950/40 text-rose-900 dark:text-rose-200 ring-1 ring-rose-500' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 hover:border-rose-300 dark:hover:border-slate-600'"
                                                class="p-3 rounded-xl border text-left transition flex flex-col justify-between cursor-pointer group">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="inline-flex items-center gap-1.5 font-bold text-xs text-rose-600 dark:text-rose-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        <span>Tolak Berkas</span>
                                                    </span>
                                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0" :class="statusData.decision === 'rejected' ? 'border-rose-500 bg-rose-500' : 'border-gray-300 dark:border-slate-600'">
                                                        <span x-show="statusData.decision === 'rejected'" class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                </div>
                                                <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-snug">
                                                    Tolak kandidat pada tahap ini (status seketika <strong>Rejected</strong>).
                                                </p>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Direct Status Selection (Only when approval_mode === 'direct') -->
                            <div x-show="statusData.approval_mode === 'direct'" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label for="status_select" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                            Status Lamaran <span class="text-rose-500">*</span>
                                        </label>
                                        <select name="status" id="status_select" x-model="statusData.status" @change="onStatusChange($event.target.value)" :disabled="statusData.approval_mode !== 'direct'" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition cursor-pointer">
                                            <option value="Submitted">Diajukan</option>
                                            <option value="Reviewed">Lolos Berkas</option>
                                            <option value="Partial Approved">Partial Approved</option>
                                            <option value="Shortlisted">Lolos Ujian</option>
                                            <option value="Interview">Wawancara</option>
                                            <option value="Accepted">Diterima</option>
                                            <option value="Rejected">Ditolak</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                            Template Cepat Pesan:
                                        </label>
                                        <div class="flex flex-wrap gap-1.5">
                                            <button type="button" @click="applyTemplate('Reviewed')" class="px-2 py-1 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 border border-amber-200 dark:border-amber-800/80 rounded-lg text-[10px] font-semibold text-amber-800 dark:text-amber-300 transition cursor-pointer">
                                                Lolos Berkas
                                            </button>
                                            <button type="button" @click="applyTemplate('Shortlisted')" class="px-2 py-1 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 border border-indigo-200 dark:border-indigo-800/80 rounded-lg text-[10px] font-semibold text-indigo-700 dark:text-indigo-300 transition cursor-pointer">
                                                Lolos Ujian
                                            </button>
                                            <button type="button" @click="applyTemplate('Interview')" class="px-2 py-1 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-900/60 border border-blue-200 dark:border-blue-800/80 rounded-lg text-[10px] font-semibold text-blue-700 dark:text-blue-300 transition cursor-pointer">
                                                Wawancara
                                            </button>
                                            <button type="button" @click="applyTemplate('Accepted')" class="px-2 py-1 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800/80 rounded-lg text-[10px] font-semibold text-emerald-700 dark:text-emerald-300 transition cursor-pointer">
                                                Diterima
                                            </button>
                                            <button type="button" @click="applyTemplate('Rejected')" class="px-2 py-1 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800/80 rounded-lg text-[10px] font-semibold text-rose-700 dark:text-rose-300 transition cursor-pointer">
                                                Ditolak
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes / Justification Textarea -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="notes_text" class="block text-xs font-semibold text-gray-700 dark:text-slate-300">
                                        <span x-show="statusData.approval_mode === 'recruiter_review'">Catatan Review Berkas</span>
                                        <span x-show="statusData.approval_mode === 'admin_review'">Catatan Verifikasi Berkas HR</span>
                                        <span x-show="statusData.approval_mode === 'test_recruiter_review'">Catatan Teknis Recruiter</span>
                                        <span x-show="statusData.approval_mode === 'test_admin_review'">Catatan Verifikasi Ujian HR</span>
                                        <span x-show="statusData.approval_mode === 'interview_recruiter_review'">Catatan Evaluasi Wawancara</span>
                                        <span x-show="statusData.approval_mode === 'interview_admin_review'">Catatan Verifikasi HR (Offering & Kontrak)</span>
                                        <span x-show="statusData.approval_mode === 'direct'">Pesan untuk Pelamar</span>
                                    </label>
                                    <button type="button" @click="applyTemplate()" class="text-[10.5px] text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        <span>Terapkan Pesan Template</span>
                                    </button>
                                </div>
                                <textarea name="notes" id="notes_text" rows="2" x-model="statusData.notes" :placeholder="statusData.approval_mode === 'interview_recruiter_review' ? 'Tuliskan catatan evaluasi kompetensi pelamar...' : (statusData.approval_mode === 'interview_admin_review' ? 'Tuliskan catatan offering gaji, kuota & kontrak...' : 'Tuliskan catatan atau pesan pemberitahuan status untuk pelamar...')" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition leading-relaxed custom-scrollbar"></textarea>
                            </div>

                            @if($isAdmin)
                                <!-- Email Notification Option (Admin Only) - Compact Inline -->
                                <div class="px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800/60 border border-gray-200/60 dark:border-slate-700/60 flex items-center justify-between">
                                    <label class="flex items-center gap-2.5 cursor-pointer select-none text-xs font-medium text-gray-800 dark:text-slate-200">
                                        <input type="hidden" name="send_email" value="0">
                                        <input type="checkbox" name="send_email" value="1" x-model="statusData.send_email" class="w-4 h-4 text-indigo-600 rounded border-gray-300 dark:border-slate-600 focus:ring-indigo-500 transition cursor-pointer">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-indigo-500 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span>Kirim email pemberitahuan otomatis ke pelamar</span>
                                        </span>
                                    </label>
                                    <span class="text-[10px] text-gray-400 dark:text-slate-500 hidden sm:inline">Notifikasi resmi terkirim otomatis</span>
                                </div>
                            @else
                                <!-- Recruiter Notification Note -->
                                <div class="px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60 text-[10.5px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Pengiriman email resmi dan keputusan akhir lamaran diproses oleh HR/Admin.</span>
                                </div>
                                <input type="hidden" name="send_email" value="0">
                            @endif

                        </div>

                        <!-- Modal Actions Footer -->
                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showStatusModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingStatus" class="px-5 py-2 text-xs font-bold text-slate-950 bg-[#93F514] hover:bg-[#82dc12] rounded-xl shadow-sm transition flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                                <svg x-show="isSubmittingStatus" class="animate-spin w-3.5 h-3.5 text-slate-950" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="getSubmitButtonText()">Simpan Keputusan HR</span>
                            </button>
                        </div>
                    </form>
                </div>

</div>
