@php
    $isRecruiter = $isRecruiter ?? (auth()->check() && (auth()->user()->role_id == 2 || strtolower(auth()->user()->role?->name ?? '') === 'recruiter'));
    $formActionPrefix = $isRecruiter ? url('/recruiter/applicants') . '/' : url('/admin/applicants') . '/';
@endphp

<div class="space-y-6" x-data="{ 
    showDetailModal: false,
    showStatusModal: false,
    isSubmittingStatus: false,
    detailData: {},
    statusTemplates: {
        'Reviewed': 'Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.',
        'Shortlisted': 'Selamat! Anda dinyatakan lolos tahap seleksi dan masuk ke dalam daftar kandidat terpilih (Shortlisted). Kami akan segera menginformasikan jadwal wawancara.',
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
        send_email: true
    },
    openDetailModal(application) {
        this.detailData = application;
        this.showDetailModal = true;
    },
    openStatusModal(data) {
        const initialStatus = data.status || 'Submitted';
        let initialNotes = data.notes || '';

        // Jika catatan masih kosong, otomatis berikan template sesuai status awal
        if (!initialNotes && this.statusTemplates[initialStatus]) {
            initialNotes = this.statusTemplates[initialStatus];
        }

        this.statusData = {
            id: data.id,
            applicant_name: data.applicant_name || (data.applicant_profile ? data.applicant_profile.full_name : 'Pelamar'),
            applicant_email: data.applicant_email || (data.applicant_profile && data.applicant_profile.user ? data.applicant_profile.user.email : ''),
            job_title: data.job_title || (data.job ? data.job.title : 'Lowongan'),
            status: initialStatus,
            notes: initialNotes,
            send_email: true
        };
        this.showStatusModal = true;
    },
    applyTemplate(statusKey) {
        if (statusKey) {
            this.statusData.status = statusKey;
        }
        const targetStatus = statusKey || this.statusData.status;
        if (this.statusTemplates[targetStatus]) {
            this.statusData.notes = this.statusTemplates[targetStatus];
        }
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
                    @if(($stats['submitted'] ?? 0) > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            {{ $stats['submitted'] }} Perlu Diproses
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
                        'Submitted' => ['label' => 'Perlu Review', 'count' => $stats['submitted'] ?? 0],
                        'Reviewed' => ['label' => 'Lolos Berkas', 'count' => $stats['reviewed'] ?? 0],
                        'Shortlisted' => ['label' => 'Shortlisted', 'count' => $stats['shortlisted'] ?? 0],
                        'Interview' => ['label' => 'Wawancara', 'count' => $stats['interview'] ?? 0],
                        'Accepted' => ['label' => 'Diterima', 'count' => $stats['accepted'] ?? 0],
                        'Rejected' => ['label' => 'Ditolak', 'count' => $stats['rejected'] ?? 0],
                    ];
                @endphp
                @foreach($tabs as $tabKey => $tabData)
                    <button type="button" 
                            wire:click="setStatusFilter('{{ $tabKey }}')"
                            class="px-2.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0 {{ ($statusFilter === $tabKey && empty($selectedStatuses)) ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20' : 'bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700/80 border border-gray-200/60 dark:border-slate-700/60' }}">
                        <span>{{ $tabData['label'] }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold {{ ($statusFilter === $tabKey && empty($selectedStatuses)) ? 'bg-white/20 text-white' : 'bg-gray-200/70 dark:bg-slate-700 text-gray-700 dark:text-slate-300' }}">
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
                                'Submitted' => 'Submitted (Diajukan)',
                                'Reviewed' => 'Reviewed (Lolos Berkas)',
                                'Shortlisted' => 'Shortlisted (Lolos Ujian)',
                                'Interview' => 'Interview (Wawancara)',
                                'Accepted' => 'Accepted (Diterima)',
                                'Rejected' => 'Rejected (Ditolak)'
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
                            @if ($app->applicantProfile && $app->applicantProfile->photo)
                                <img src="{{ \Illuminate\Support\Str::startsWith($app->applicantProfile->photo, ['http://', 'https://']) ? $app->applicantProfile->photo : asset('storage/' . $app->applicantProfile->photo) }}" alt="{{ $app->applicantProfile->full_name }}" class="w-11 h-11 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-sm shrink-0">
                            @else
                                <div class="w-11 h-11 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    {{ strtoupper(substr($app->applicantProfile->full_name ?? 'P', 0, 2)) }}
                                </div>
                            @endif
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
                                    Accepted
                                </span>
                            @elseif ($app->status === 'Rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                    Rejected
                                </span>
                            @elseif ($app->status === 'Interview')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                    Interview
                                </span>
                            @elseif ($app->status === 'Shortlisted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                    Shortlisted
                                </span>
                            @elseif ($app->status === 'Reviewed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                    Reviewed
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                    Submitted
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
                            'id' => $app->id,
                            'applicant_name' => $app->applicantProfile->full_name ?? 'Pelamar',
                            'applicant_email' => $app->applicantProfile->user->email ?? '',
                            'job_title' => $app->job->title ?? 'Lowongan',
                            'status' => $app->status ?? 'Submitted',
                            'notes' => $app->notes ?? '',
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
                        <span class="text-sm font-medium">Belum ada data lamaran kerja</span>
                        <span class="text-xs">Lamaran yang diajukan oleh pelamar akan muncul di sini.</span>
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
                                        @if ($app->applicantProfile && $app->applicantProfile->photo)
                                            <img src="{{ \Illuminate\Support\Str::startsWith($app->applicantProfile->photo, ['http://', 'https://']) ? $app->applicantProfile->photo : asset('storage/' . $app->applicantProfile->photo) }}" alt="{{ $app->applicantProfile->full_name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-sm shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                {{ strtoupper(substr($app->applicantProfile->full_name ?? 'P', 0, 2)) }}
                                            </div>
                                        @endif
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
                                        @if ($app->status === 'Accepted')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                Accepted (Diterima)
                                            </span>
                                        @elseif ($app->status === 'Rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                Rejected (Ditolak)
                                            </span>
                                        @elseif ($app->status === 'Interview')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                                Interview (Wawancara)
                                            </span>
                                        @elseif ($app->status === 'Shortlisted')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                                Shortlisted (Lolos Ujian)
                                            </span>
                                        @elseif ($app->status === 'Reviewed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                                Reviewed (Lolos Berkas)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                                Submitted (Diajukan)
                                            </span>
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
                                            'id' => $app->id,
                                            'applicant_name' => $app->applicantProfile->full_name ?? 'Pelamar',
                                            'applicant_email' => $app->applicantProfile->user->email ?? '',
                                            'job_title' => $app->job->title ?? 'Lowongan',
                                            'status' => $app->status ?? 'Submitted',
                                            'notes' => $app->notes ?? '',
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
                                    <span class="text-sm font-medium">Belum ada data lamaran kerja</span>
                                    <span class="text-xs">Lamaran yang diajukan oleh pelamar akan muncul di sini.</span>
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

    <!-- Detail Applicant Modal (Read Only) -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-detail" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-3 sm:p-4 text-center sm:block sm:p-0">
            <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDetailModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom sm:align-middle bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl w-full border border-gray-200 dark:border-slate-800 my-auto">
                
                <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                    <div class="flex items-center justify-between pb-3 sm:pb-4 border-b border-gray-100 dark:border-slate-800">
                        <div class="flex items-center gap-3 min-w-0">
                            <template x-if="detailData.applicant_profile && detailData.applicant_profile.photo">
                                <img :src="detailData.applicant_profile.photo.startsWith('http') 
                                    ? detailData.applicant_profile.photo 
                                    : ('/storage/' + detailData.applicant_profile.photo)" 
                                    :alt="detailData.applicant_profile ? detailData.applicant_profile.full_name : 'Pelamar'" 
                                    class="w-11 h-11 sm:w-12 sm:h-12 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-md shrink-0">
                            </template>
                            <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.photo">
                                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-base flex items-center justify-center shrink-0 shadow-2xs">
                                    <span x-text="detailData.applicant_profile ? (detailData.applicant_profile.full_name || 'P').substring(0, 2).toUpperCase() : 'P'"></span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate max-w-[190px] sm:max-w-md" x-text="detailData.applicant_profile ? detailData.applicant_profile.full_name : 'Detail Pelamar'"></h3>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold truncate max-w-[200px] sm:max-w-md" x-text="detailData.job ? (detailData.job.title + ' • ' + (detailData.job.company ? detailData.job.company.name : '')) : ''"></p>
                                <p class="text-[11px] text-gray-400 mt-0.5" x-show="detailData.applied_at" x-text="'Dilamar: ' + formatDateTime(detailData.applied_at)"></p>
                            </div>
                        </div>
                        <button @click="showDetailModal = false" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 transition shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-5 sm:space-y-6 text-xs max-h-[68vh] sm:max-h-[75vh] overflow-y-auto pr-1 custom-scrollbar">
                        <!-- 1. Informasi Pribadi & Kontak -->
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Informasi Pribadi & Kontak
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-gray-50 dark:bg-slate-800/40 p-3 sm:p-4 rounded-2xl border border-gray-100 dark:border-slate-800">
                                <div>
                                    <span class="text-gray-400 block">NIK:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? (detailData.applicant_profile.nik || '-') : '-'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block">Email:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 break-all" x-text="detailData.applicant_profile && detailData.applicant_profile.user ? detailData.applicant_profile.user.email : '-'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block">Nomor Telepon:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? (detailData.applicant_profile.phone || '-') : '-'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block">Jenis Kelamin:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? (detailData.applicant_profile.gender || '-') : '-'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block">Tempat, Tgl Lahir:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? ((detailData.applicant_profile.birth_place || '') + ', ' + (detailData.applicant_profile.birth_date || '')) : '-'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block">Kota & Provinsi:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? ((detailData.applicant_profile.city || '') + ', ' + (detailData.applicant_profile.province || '')) : '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Tentang Saya -->
                        <template x-if="detailData.applicant_profile && detailData.applicant_profile.about_me">
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white mb-2">Tentang Pelamar</h4>
                                <p class="text-xs text-gray-600 dark:text-slate-300 leading-relaxed bg-gray-50 dark:bg-slate-800/40 p-3 rounded-xl border border-gray-200/60 dark:border-slate-800" x-text="detailData.applicant_profile.about_me"></p>
                            </div>
                        </template>

                        <!-- 2. Pengalaman Kerja (work_experiences) -->
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Pengalaman Kerja
                            </h4>
                            <template x-if="detailData.applicant_profile && detailData.applicant_profile.work_experiences && detailData.applicant_profile.work_experiences.length > 0">
                                <div class="space-y-3">
                                    <template x-for="work in detailData.applicant_profile.work_experiences" :key="work.id">
                                        <div class="p-3 sm:p-4 bg-gray-50 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-1">
                                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 sm:gap-2">
                                                <div>
                                                    <span class="font-bold text-gray-900 dark:text-white text-xs block" x-text="work.position"></span>
                                                    <span class="text-indigo-600 dark:text-indigo-400 font-semibold text-[11px]" x-text="work.company_name + ' • (' + work.employment_type + ')'"></span>
                                                </div>
                                                <span class="text-gray-400 text-[10.5px] bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg shrink-0 self-start" x-text="formatDate(work.start_date) + ' s/d ' + (work.currently_working ? 'Sekarang' : formatDate(work.end_date))"></span>
                                            </div>
                                            <p x-show="work.description" class="text-gray-600 dark:text-slate-300 text-[11px] pt-1" x-text="work.description"></p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.work_experiences || detailData.applicant_profile.work_experiences.length === 0">
                                <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">Belum ada riwayat pengalaman kerja.</div>
                            </template>
                        </div>

                        <!-- 3. Riwayat Pendidikan (educations) -->
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                                Riwayat Pendidikan
                            </h4>
                            <template x-if="detailData.applicant_profile && detailData.applicant_profile.educations && detailData.applicant_profile.educations.length > 0">
                                <div class="space-y-2">
                                    <template x-for="edu in detailData.applicant_profile.educations" :key="edu.id">
                                        <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 sm:gap-2">
                                            <div>
                                                <span class="font-semibold text-gray-900 dark:text-white block" x-text="edu.school_name || edu.institution_name"></span>
                                                <div class="flex items-center flex-wrap gap-1.5 mt-0.5">
                                                    <span class="text-indigo-600 dark:text-indigo-400 font-medium text-[11px]"
                                                        x-text="edu.degree || 'Pendidikan'"></span>
                                                    <span class="text-gray-400 text-[11px]" x-show="edu.major && edu.major !== '-'">•</span>
                                                    <span class="text-gray-700 dark:text-slate-300 font-medium text-[11px]"
                                                        x-show="edu.major && edu.major !== '-'">
                                                        Jurusan: <span class="font-semibold" x-text="edu.major"></span>
                                                    </span>
                                                    <span class="text-gray-400 text-[11px]" x-show="edu.study_program && edu.study_program !== edu.major">•</span>
                                                    <span class="text-gray-600 dark:text-slate-400 text-[11px]"
                                                        x-show="edu.study_program && edu.study_program !== edu.major">
                                                        Prodi: <span class="font-medium" x-text="edu.study_program"></span>
                                                    </span>
                                                    <span class="text-gray-400 text-[11px]" x-show="edu.gpa">•</span>
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium text-[11px]"
                                                        x-show="edu.gpa"
                                                        x-text="'IPK/Nilai: ' + edu.gpa"></span>
                                                </div>
                                            </div>
                                            <span class="text-gray-400 text-[10.5px] shrink-0 self-start" x-text="edu.start_year + ' - ' + (edu.end_year || 'Sekarang')"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.educations || detailData.applicant_profile.educations.length === 0">
                                <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">Belum ada data pendidikan.</div>
                            </template>
                        </div>

                        <!-- 4. Pengalaman Organisasi (organizations) -->
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                </svg>
                                Pengalaman Organisasi
                            </h4>
                            <template x-if="detailData.applicant_profile && detailData.applicant_profile.organizations && detailData.applicant_profile.organizations.length > 0">
                                <div class="space-y-2">
                                    <template x-for="org in detailData.applicant_profile.organizations" :key="org.id">
                                        <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 sm:gap-2">
                                            <div>
                                                <span class="font-semibold text-gray-900 dark:text-white block" x-text="org.name"></span>
                                                <span class="text-gray-500 dark:text-slate-400 text-[11px]" x-text="'Jabatan: ' + org.position"></span>
                                            </div>
                                            <span class="text-gray-400 text-[10.5px] shrink-0 self-start" x-text="(org.start_month || '') + ' ' + org.start_year + ' - ' + (org.is_active ? 'Aktif' : ((org.end_month || '') + ' ' + (org.end_year || '')))"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.organizations || detailData.applicant_profile.organizations.length === 0">
                                <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">Belum ada riwayat organisasi.</div>
                            </template>
                        </div>

                        <!-- 4.1 Prestasi & Penghargaan (achievements) -->
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                Prestasi & Penghargaan
                            </h4>
                            <template x-if="detailData.applicant_profile && detailData.applicant_profile.achievements && detailData.applicant_profile.achievements.length > 0">
                                <div class="space-y-2">
                                    <template x-for="ach in detailData.applicant_profile.achievements" :key="ach.id">
                                        <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-3">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-semibold text-gray-900 dark:text-white truncate" x-text="ach.name"></span>
                                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-md border border-amber-200 dark:border-amber-800 shrink-0" x-text="ach.scale"></span>
                                                </div>
                                                <span class="text-gray-500 dark:text-slate-400 text-[11px]" x-text="(ach.month || '') + ' ' + (ach.year || '')"></span>
                                            </div>
                                            <template x-if="ach.certificate_path">
                                                <a :href="ach.certificate_path.startsWith('http') ? ach.certificate_path : ('{{ asset('storage') }}/' + ach.certificate_path.replace(/^\/+/, ''))" 
                                                   target="_blank" 
                                                   class="px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/80 rounded-lg border border-indigo-200/80 dark:border-indigo-800/80 transition shrink-0 flex items-center gap-1 self-start sm:self-auto">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span>Lihat Berkas</span>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.achievements || detailData.applicant_profile.achievements.length === 0">
                                <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">Belum ada prestasi dicantumkan.</div>
                            </template>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- 5. Sertifikasi (certifications) -->
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                    Sertifikasi
                                </h4>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.certifications && detailData.applicant_profile.certifications.length > 0">
                                    <div class="space-y-2">
                                        <template x-for="cert in detailData.applicant_profile.certifications" :key="cert.id">
                                            <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-3">
                                                <span class="font-semibold text-gray-900 dark:text-white truncate" x-text="cert.name"></span>
                                                <template x-if="cert.certificate_path">
                                                    <a :href="cert.certificate_path.startsWith('http') ? cert.certificate_path : ('{{ asset('storage') }}/' + cert.certificate_path.replace(/^\/+/, ''))" 
                                                       target="_blank" 
                                                       class="px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/80 rounded-lg border border-indigo-200/80 dark:border-indigo-800/80 transition shrink-0 flex items-center gap-1 self-start sm:self-auto">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        <span>Lihat Berkas</span>
                                                    </a>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.certifications || detailData.applicant_profile.certifications.length === 0">
                                    <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">Belum ada sertifikasi.</div>
                                </template>
                            </div>

                            <!-- 6. Pelatihan & Kursus (trainings) -->
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                    Pelatihan / Kursus
                                </h4>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.trainings && detailData.applicant_profile.trainings.length > 0">
                                    <div class="space-y-2">
                                        <template x-for="tr in detailData.applicant_profile.trainings" :key="tr.id">
                                            <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-3">
                                                <span class="font-semibold text-gray-900 dark:text-white truncate" x-text="tr.name"></span>
                                                <template x-if="tr.certificate_path">
                                                    <a :href="tr.certificate_path.startsWith('http') ? tr.certificate_path : ('{{ asset('storage') }}/' + tr.certificate_path.replace(/^\/+/, ''))" 
                                                       target="_blank" 
                                                       class="px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/80 rounded-lg border border-indigo-200/80 dark:border-indigo-800/80 transition shrink-0 flex items-center gap-1 self-start sm:self-auto">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        <span>Lihat Berkas</span>
                                                    </a>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.trainings || detailData.applicant_profile.trainings.length === 0">
                                    <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">Belum ada riwayat pelatihan.</div>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- 7. Keahlian / Skills -->
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white mb-2">Keahlian (Skills)</h4>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.skills && detailData.applicant_profile.skills.length > 0">
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="sk in detailData.applicant_profile.skills" :key="sk.id">
                                            <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-300 rounded-lg text-[11px] font-semibold" x-text="sk.name || sk.skill_name"></span>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.skills || detailData.applicant_profile.skills.length === 0">
                                    <span class="text-gray-400 italic text-[11px]">Belum ada skill.</span>
                                </template>
                            </div>

                            <!-- 8. Kemampuan Bahasa (languages) -->
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white mb-2">Kemampuan Bahasa</h4>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.languages && detailData.applicant_profile.languages.length > 0">
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="lang in detailData.applicant_profile.languages" :key="lang.id">
                                            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-300 rounded-lg text-[11px] font-semibold" x-text="lang.name"></span>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.languages || detailData.applicant_profile.languages.length === 0">
                                    <span class="text-gray-400 italic text-[11px]">Belum ada data bahasa.</span>
                                </template>
                            </div>

                            <!-- 9. Media Sosial (social_medias) -->
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-1.5 text-xs">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                    <span>Media Sosial & Portofolio</span>
                                </h4>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.social_medias && detailData.applicant_profile.social_medias.length > 0">
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="sm in detailData.applicant_profile.social_medias" :key="sm.id">
                                            <a :href="sm.url" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50/80 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 font-semibold text-[11px] border border-indigo-200/60 dark:border-indigo-800/60 transition group shadow-2xs max-w-full">
                                                <span class="truncate" x-text="sm.platform_name"></span>
                                                <svg class="w-3 h-3 text-indigo-400 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.social_medias || detailData.applicant_profile.social_medias.length === 0">
                                    <span class="text-gray-400 italic text-[11px]">Belum ada media sosial.</span>
                                </template>
                            </div>
                        </div>

                        <!-- Riwayat Perubahan Status (application_status_history) -->
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Riwayat Perubahan Status Seleksi
                            </h4>
                            <template x-if="detailData.status_histories && detailData.status_histories.length > 0">
                                <div class="space-y-2 border-l-2 border-indigo-200 dark:border-indigo-900/60 ml-2 pl-4">
                                    <template x-for="history in detailData.status_histories" :key="history.id">
                                        <div class="relative pb-2">
                                            <div class="absolute -left-[21px] top-1.5 w-2.5 h-2.5 rounded-full bg-indigo-600 dark:bg-indigo-400"></div>
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-gray-900 dark:text-white text-xs" x-text="history.status"></span>
                                                <span class="text-[10px] text-gray-400 font-mono" x-text="formatDateTime(history.changed_at)"></span>
                                            </div>
                                            <p x-show="history.notes" class="text-gray-600 dark:text-slate-300 text-[11px] mt-0.5" x-text="'Catatan: ' + history.notes"></p>
                                            <div x-show="history.changed_by" class="text-[10px] text-gray-400 italic" x-text="'Diubah oleh: ' + (history.changed_by ? (history.changed_by.name || 'Admin') : 'Admin')"></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Jadwal Wawancara Pelamar -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Jadwal Wawancara (Interview)
                                </h4>
                                <a :href="'{{ $isRecruiter ? route('recruiter.interview_schedule') : route('admin.interview_schedule') }}'" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                    <span>Kelola Jadwal</span>
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>

                            <template x-if="(detailData.interview_schedules && detailData.interview_schedules.length > 0) || (detailData.interviewSchedules && detailData.interviewSchedules.length > 0)">
                                <div class="space-y-2">
                                    <template x-for="item in (detailData.interview_schedules || detailData.interviewSchedules || [])" :key="item.id">
                                        <div class="p-3 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-emerald-900 dark:text-emerald-300 text-xs" x-text="new Date(item.interview_date).toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' }) + ' WIB'"></span>
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300" x-text="item.status"></span>
                                            </div>
                                            <div class="text-[11px] text-gray-700 dark:text-gray-300">
                                                <p><strong class="font-semibold">Lokasi/Metode:</strong> <span x-text="item.location"></span></p>
                                                <p x-show="item.meeting_link">
                                                    <strong class="font-semibold">Link:</strong> 
                                                    <a :href="item.meeting_link" target="_blank" class="text-indigo-600 dark:text-indigo-400 underline font-semibold break-all" x-text="item.meeting_link"></a>
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="(!detailData.interview_schedules || detailData.interview_schedules.length === 0) && (!detailData.interviewSchedules || detailData.interviewSchedules.length === 0)">
                                <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">
                                    Belum ada jadwal wawancara untuk pelamar ini.
                                </div>
                            </template>
                        </div>

                        <!-- Dokumen CV / Resume Pelamar -->
                        <template x-if="detailData.applicant_profile">
                            <div class="pt-3 border-t border-gray-100 dark:border-slate-800 space-y-2">
                                <span class="font-bold text-gray-900 dark:text-white text-xs block">Dokumen CV / Resume Pelamar</span>
                                <div class="flex flex-wrap items-center gap-2">
                                    <template x-if="detailData.applicant_profile.cv_file_url || detailData.applicant_profile.cv_file_path">
                                        <a :href="detailData.applicant_profile.cv_file_url ? detailData.applicant_profile.cv_file_url : ('/storage/' + detailData.applicant_profile.cv_file_path)" target="_blank"
                                            class="w-full sm:w-auto px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>Unduh / Buka File CV</span>
                                        </a>
                                    </template>
                                    <template x-if="!detailData.applicant_profile.cv_file_url && !detailData.applicant_profile.cv_file_path">
                                        <span class="text-xs text-gray-400 dark:text-slate-400 italic">Pelamar belum mengunggah file CV.</span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-2.5 pt-4 border-t border-gray-100 dark:border-slate-800 mt-4">
                        <button type="button" @click="showDetailModal = false" class="w-full sm:w-auto px-4 py-2.5 text-xs font-semibold text-center text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Tutup
                        </button>
                        <button type="button" @click="showDetailModal = false; openStatusModal({
                            id: detailData.id,
                            applicant_name: detailData.applicant_profile ? detailData.applicant_profile.full_name : 'Pelamar',
                            applicant_email: detailData.applicant_profile && detailData.applicant_profile.user ? detailData.applicant_profile.user.email : '',
                            job_title: detailData.job ? detailData.job.title : 'Lowongan',
                            status: detailData.status || 'Submitted',
                            notes: detailData.notes || ''
                        })" class="w-full sm:w-auto px-4 py-2.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Update Status Lamaran</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Update Status Modal (Update Only) -->
    <div x-show="showStatusModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-status" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-3 sm:p-4 text-center sm:block sm:p-0">
            <div x-show="showStatusModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showStatusModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showStatusModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom sm:align-middle bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md w-full border border-gray-200 dark:border-slate-800 my-auto">
                
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between pb-3 sm:pb-4 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title-status">
                            Update Status Lamaran
                        </h3>
                        <button @click="showStatusModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'{{ $formActionPrefix }}' + statusData.id" method="POST" @submit="isSubmittingStatus = true" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200/60 dark:border-slate-700 text-xs space-y-1">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2">
                                <span class="text-gray-400 block text-[11px]">Pelamar & Posisi</span>
                                <template x-if="statusData.applicant_email">
                                    <span class="inline-flex items-center gap-1 text-[11px] text-indigo-600 dark:text-indigo-400 font-medium bg-indigo-50 dark:bg-indigo-950/50 px-2 py-0.5 rounded-md border border-indigo-200/60 dark:border-indigo-800/60 truncate max-w-full sm:max-w-[220px]" :title="statusData.applicant_email">
                                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="truncate" x-text="statusData.applicant_email"></span>
                                    </span>
                                </template>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white block mt-0.5" x-text="statusData.applicant_name"></span>
                            <span class="text-indigo-600 dark:text-indigo-400 block text-[11px]" x-text="statusData.job_title"></span>
                        </div>

                        <!-- Status Selection -->
                        <div>
                            <label for="status_select" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Status Lamaran <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" id="status_select" x-model="statusData.status" @change="onStatusChange($event.target.value)" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                <option value="Submitted">Submitted (Diajukan)</option>
                                <option value="Reviewed">Reviewed (Lolos Berkas / Tahap Tes)</option>
                                <option value="Shortlisted">Shortlisted (Lolos Ujian / Siap Wawancara)</option>
                                <option value="Interview">Interview (Wawancara)</option>
                                <option value="Accepted">Accepted (Diterima)</option>
                                <option value="Rejected">Rejected (Ditolak)</option>
                            </select>
                        </div>

                        <!-- Quick Template Chips -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-semibold text-gray-600 dark:text-slate-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span>Pilihan Cepat / Template Pesan:</span>
                                </span>
                                <button type="button" @click="statusData.notes = ''" class="text-gray-400 hover:text-rose-500 transition text-[10px]">
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

                        <!-- Notes -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="notes_text" class="block text-xs font-semibold text-gray-700 dark:text-slate-300">
                                    Catatan / Pesan untuk Pelamar
                                </label>
                                <button type="button" @click="applyTemplate()" class="text-[10px] text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-0.5">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Terapkan Pesan Status</span>
                                </button>
                            </div>
                            <textarea name="notes" id="notes_text" rows="3" x-model="statusData.notes" placeholder="Tambahkan catatan untuk proses seleksi pelamar ini..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition leading-relaxed"></textarea>
                            <p class="text-[10.5px] text-gray-400 dark:text-slate-500 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Pesan ini akan langsung dibaca pelamar pada menu <strong>Riwayat Lamaran</strong>.</span>
                            </p>
                        </div>

                        <!-- Email Notification Option -->
                        <div class="p-3 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/60">
                            <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                <input type="hidden" name="send_email" value="0">
                                <input type="checkbox" name="send_email" value="1" x-model="statusData.send_email" class="w-4 h-4 mt-0.5 text-indigo-600 rounded border-gray-300 dark:border-slate-600 focus:ring-indigo-500 transition">
                                <div class="space-y-0.5">
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span>Kirim notifikasi email otomatis ke pelamar</span>
                                    </span>
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-tight">
                                        Pelamar akan menerima email resmi berisi pemberitahuan status, instruksi pengerjaan tes/tahap berikutnya, dan tombol langsung ke sistem.
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2.5 sm:gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showStatusModal = false" class="w-full sm:w-auto px-4 py-2.5 text-xs font-medium text-center text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingStatus" class="w-full sm:w-auto px-4 py-2.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-500/20 transition flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                <svg x-show="isSubmittingStatus" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmittingStatus ? 'Menyimpan...' : 'Simpan Perubahan Status'"></span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
