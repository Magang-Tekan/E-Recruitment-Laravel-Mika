@php
    $isRecruiter = auth()->check() && (auth()->user()->role_id == 2 || strtolower(auth()->user()->role?->name ?? '') === 'recruiter');
    $routePrefix = $isRecruiter ? '/recruiter/interview-schedules' : '/admin/interview-schedules';
@endphp

<div x-data="{
    showCreateModal: false,
    showEditModal: false,
    showCompleteModal: false,
    showDeleteModal: false,
    createStep: 1,

    // Candidates list for searchable combobox
    allCandidates: @js($activeApplications),
    candidateSearch: '',
    isCandidateDropdownOpen: false,
    selectedCandidate: null,

    filteredCandidates() {
        if (!this.candidateSearch || this.candidateSearch.trim() === '') {
            return this.allCandidates;
        }
        let q = this.candidateSearch.toLowerCase().trim();
        return this.allCandidates.filter(c => {
            return (c.name && c.name.toLowerCase().includes(q)) ||
                   (c.job_title && c.job_title.toLowerCase().includes(q)) ||
                   (c.company && c.company.toLowerCase().includes(q)) ||
                   (c.email && c.email.toLowerCase().includes(q)) ||
                   (c.status && c.status.toLowerCase().includes(q));
        });
    },

    selectCandidate(candidate) {
        this.selectedCandidate = candidate;
        this.createData.job_applications_id = candidate ? candidate.id : '';
        this.isCandidateDropdownOpen = false;
        this.candidateSearch = '';
    },

    clearCandidate() {
        this.selectedCandidate = null;
        this.createData.job_applications_id = '';
        this.candidateSearch = '';
    },
    
    // Create form data
    createData: {
        job_applications_id: '',
        users_id: '{{ auth()->id() }}',
        interview_date: '',
        interview_type: 'online',
        location: 'Online (Google Meet)',
        meeting_link: '',
        status: 'Scheduled',
        notes: '',
        applicationStatus: 'Interview', // default: ubah ke Interview
        sendEmail: true,                // default: kirim email
    },

    // Helper to get candidate photo from schedule
    getCandidatePhoto(schedule) {
        let p = schedule?.job_application?.applicant_profile?.photo || schedule?.job_application?.applicant_profile?.user?.avatar || null;
        if (!p) return null;
        return p.startsWith('http') ? p : ('/storage/' + p);
    },

    // Edit form data
    editData: {
        id: null,
        applicant_name: '',
        applicant_photo: null,
        job_title: '',
        users_id: '',
        interview_date: '',
        interview_type: 'online',
        location: '',
        meeting_link: '',
        status: 'Scheduled',
        notes: ''
    },

    // Complete modal data
    completeData: {
        id: null,
        applicant_name: '',
        applicant_photo: null,
        job_title: '',
        company_name: '',
        users_id: '',
        interview_date: '',
        interview_type: 'online',
        location: '',
        meeting_link: '',
        status: 'Completed',
        score: '',
        application_decision: 'Accepted',
        notes: ''
    },

    // Delete modal data
    deleteData: {
        id: null,
        applicant_name: '',
        applicant_photo: null,
        interview_date: ''
    },

    openCreateModal(appId = '') {
        this.createStep = 1;
        this.createData.job_applications_id = appId;
        this.createData.users_id = '{{ auth()->id() }}';
        this.createData.interview_date = '';
        this.createData.interview_type = 'online';
        this.createData.location = 'Online (Google Meet)';
        this.createData.meeting_link = '';
        this.createData.status = 'Scheduled';
        this.createData.notes = '';
        this.createData.sendEmail = true;
        this.candidateSearch = '';
        this.isCandidateDropdownOpen = false;

        if (appId) {
            this.selectedCandidate = this.allCandidates.find(c => c.id == appId) || null;
        } else {
            this.selectedCandidate = null;
        }


        this.showCreateModal = true;
    },

    openEditModal(schedule) {
        this.editData.id = schedule.id;
        this.editData.applicant_name = schedule.job_application?.applicant_profile?.full_name || 'Kandidat';
        this.editData.applicant_photo = this.getCandidatePhoto(schedule);
        this.editData.job_title = schedule.job_application?.job?.title || 'Posisi';
        this.editData.users_id = schedule.users_id;
        
        // Format datetime-local (YYYY-MM-DDTHH:mm)
        if (schedule.interview_date) {
            let d = new Date(schedule.interview_date);
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let day = String(d.getDate()).padStart(2, '0');
            let hours = String(d.getHours()).padStart(2, '0');
            let minutes = String(d.getMinutes()).padStart(2, '0');
            this.editData.interview_date = `${d.getFullYear()}-${month}-${day}T${hours}:${minutes}`;
        } else {
            this.editData.interview_date = '';
        }

        let isOnline = !!(schedule.meeting_link || (schedule.location && schedule.location.toLowerCase().includes('online')));
        this.editData.interview_type = isOnline ? 'online' : 'offline';
        this.editData.location = schedule.location || (isOnline ? 'Online (Google Meet)' : '');
        this.editData.meeting_link = schedule.meeting_link || '';
        this.editData.status = schedule.status || 'Scheduled';
        this.editData.notes = '';
        this.showEditModal = true;
    },

    openCompleteModal(schedule) {
        this.completeData.id = schedule.id;
        this.completeData.applicant_name = schedule.job_application?.applicant_profile?.full_name || 'Kandidat';
        this.completeData.applicant_photo = this.getCandidatePhoto(schedule);
        this.completeData.job_title = schedule.job_application?.job?.title || 'Posisi';
        this.completeData.company_name = schedule.job_application?.job?.company?.name || '';
        this.completeData.users_id = schedule.users_id;
        this.completeData.interview_date = schedule.interview_date;
        this.completeData.interview_type = schedule.meeting_link ? 'online' : 'offline';
        this.completeData.location = schedule.location || '';
        this.completeData.meeting_link = schedule.meeting_link || '';
        this.completeData.status = 'Completed';
        this.completeData.score = '';
        this.completeData.application_decision = 'Accepted';
        this.completeData.notes = '';
        this.showCompleteModal = true;
    },

    openDeleteModal(schedule) {
        this.deleteData.id = schedule.id;
        this.deleteData.applicant_name = schedule.job_application?.applicant_profile?.full_name || 'Kandidat';
        this.deleteData.applicant_photo = this.getCandidatePhoto(schedule);
        this.deleteData.interview_date = schedule.interview_date;
        this.showDeleteModal = true;
    },

    copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            alert('Link meeting berhasil disalin ke clipboard!');
        });
    }
}" class="space-y-6">

    <!-- Top Summary Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4">
        <!-- Total Jadwal -->
        <div wire:click="$set('statusFilter', '')" class="cursor-pointer p-4 rounded-2xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 shadow-sm flex items-center justify-between hover:border-indigo-200 transition">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Jadwal</p>
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $stats['total'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- Mendatang / Terjadwal -->
        <div wire:click="$set('statusFilter', 'Scheduled')" class="cursor-pointer p-4 rounded-2xl bg-white dark:bg-slate-900 border border-blue-100 dark:border-blue-900/30 shadow-sm flex items-center justify-between hover:border-blue-300 transition">
            <div>
                <p class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Terjadwal</p>
                <h3 class="text-xl sm:text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $stats['upcoming'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!--  Lolos Diterima (Accepted) -->
        <div wire:click="$set('statusFilter', 'Accepted')" class="cursor-pointer p-4 rounded-2xl bg-white dark:bg-slate-900 border border-emerald-100 dark:border-emerald-900/30 shadow-sm flex items-center justify-between hover:border-emerald-300 transition">
            <div>
                <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Lolos Diterima</p>
                <h3 class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['accepted'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!--  Tidak Lolos (Rejected) -->
        <div wire:click="$set('statusFilter', 'Rejected')" class="cursor-pointer p-4 rounded-2xl bg-white dark:bg-slate-900 border border-rose-100 dark:border-rose-900/30 shadow-sm flex items-center justify-between hover:border-rose-300 transition">
            <div>
                <p class="text-[11px] font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Tidak Lolos</p>
                <h3 class="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $stats['rejected'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Selesai Sesi (Completed) -->
        <div wire:click="$set('statusFilter', 'Completed')" class="cursor-pointer p-4 rounded-2xl bg-white dark:bg-slate-900 border border-purple-100 dark:border-purple-900/30 shadow-sm flex items-center justify-between col-span-2 sm:col-span-1 hover:border-purple-300 transition">
            <div>
                <p class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Selesai Wawancara</p>
                <h3 class="text-xl sm:text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ $stats['completed'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 1: Header & Action Section Card -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm p-5 sm:p-6 space-y-4">
        
        <!-- Header & Action Button Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Agenda & Jadwal Wawancara</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola jadwal interview online (Google Meet/Zoom) & offline (tatap muka) dengan kandidat</p>
            </div>

            <button type="button" @click="openCreateModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold transition active:scale-95 shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Jadwalkan Wawancara</span>
            </button>
        </div>

        <!-- Filter & Search Toolbar Row -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="relative lg:col-span-3">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari pelamar, lokasi, pewawancara..." 
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
                    <select wire:model.live="companyFilter" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
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

                <!-- Filter Lowongan (Otomatis terfilter sesuai perusahaan yang dipilih) -->
                <div class="relative lg:col-span-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <select wire:model.live="jobFilter" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">{{ $companyFilter ? 'Semua Lowongan di Perusahaan Ini' : 'Semua Lowongan' }}</option>
                        @foreach ($jobs as $job)
                            <option value="{{ $job->id }}">
                                {{ $job->title }} @if(!$companyFilter && $job->company)({{ $job->company->name }})@endif
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Pewawancara -->
                <div class="relative lg:col-span-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <select wire:model.live="interviewerFilter" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">Semua Pewawancara</option>
                        @foreach ($interviewers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Quick Filter Badges (Time, Type, Status) -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase mr-1">Waktu:</span>
                    <button type="button" wire:click="$set('timeFilter', 'all')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $timeFilter === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Semua
                    </button>
                    <button type="button" wire:click="$set('timeFilter', 'today')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $timeFilter === 'today' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Hari Ini
                    </button>
                    <button type="button" wire:click="$set('timeFilter', 'upcoming')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $timeFilter === 'upcoming' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Mendatang
                    </button>

                    <div class="h-4 w-px bg-gray-300 dark:bg-slate-700 mx-1 hidden sm:block"></div>

                    <span class="text-[11px] font-bold text-gray-400 uppercase mr-1">Metode:</span>
                    <button type="button" wire:click="$set('typeFilter', 'all')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $typeFilter === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Semua
                    </button>
                    <button type="button" wire:click="$set('typeFilter', 'online')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $typeFilter === 'online' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Online (Video)
                    </button>
                    <button type="button" wire:click="$set('typeFilter', 'offline')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $typeFilter === 'offline' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Tatap Muka
                    </button>

                    <div class="h-4 w-px bg-gray-300 dark:bg-slate-700 mx-1 hidden sm:block"></div>

                    <span class="text-[11px] font-bold text-gray-400 uppercase mr-1">Keputusan:</span>
                    <button type="button" wire:click="$set('statusFilter', '')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $statusFilter === '' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Semua
                    </button>
                    <button type="button" wire:click="$set('statusFilter', 'Accepted')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $statusFilter === 'Accepted' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }}">
                        Lolos (Diterima)
                    </button>
                    <button type="button" wire:click="$set('statusFilter', 'Rejected')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $statusFilter === 'Rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800 hover:bg-rose-50 dark:hover:bg-rose-950/40' }}">
                        Tidak Lolos
                    </button>
                    <button type="button" wire:click="$set('statusFilter', 'Scheduled')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $statusFilter === 'Scheduled' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800 hover:bg-blue-50 dark:hover:bg-blue-950/40' }}">
                        Terjadwal
                    </button>
                </div>

                <!-- Reset Filter Button -->
                @if ($search || $companyFilter || $jobFilter || $interviewerFilter || $statusFilter || $timeFilter !== 'all' || $typeFilter !== 'all')
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

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-6">Kandidat & Posisi</th>
                        <th class="py-3.5 px-4 cursor-pointer select-none hover:text-indigo-600" wire:click="sortBy('interview_date')">
                            <div class="flex items-center gap-1">
                                <span>Waktu Wawancara</span>
                                @if ($sortField === 'interview_date')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </div>
                        </th>
                        <th class="py-3.5 px-4">Metode & Lokasi</th>
                        <th class="py-3.5 px-4">Pewawancara</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-xs">
                    @forelse ($schedules as $schedule)
                        @php
                            $interviewDate = \Carbon\Carbon::parse($schedule->interview_date);
                            $isToday = $interviewDate->isToday();
                            $isPast = $interviewDate->isPast();
                            $isOnline = $schedule->is_online;
                            
                            $status = $schedule->status;
                            $statusClasses = match(strtolower($status)) {
                                'scheduled' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                                'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                'rescheduled' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                'cancelled' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                'no show' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                                default => 'bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition">
                            
                            <!-- Kandidat & Posisi -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    @php
                                        $candProfile = $schedule->jobApplication?->applicantProfile;
                                        $candPhoto = $candProfile?->photo ?? $candProfile?->user?->avatar;
                                        $candPhotoUrl = null;
                                        if ($candPhoto) {
                                            $candPhotoUrl = \Illuminate\Support\Str::startsWith($candPhoto, ['http://', 'https://'])
                                                ? $candPhoto
                                                : asset('storage/' . $candPhoto);
                                        }
                                        $candName = $candProfile->full_name ?? 'Pelamar Tidak Ditemukan';
                                    @endphp
                                    @if ($candPhotoUrl)
                                        <img src="{{ $candPhotoUrl }}" alt="{{ $candName }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-sm shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($candName, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 dark:text-white truncate">
                                            {{ $candName }}
                                        </p>
                                        <p class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold truncate">
                                            {{ $schedule->jobApplication->job->title ?? '-' }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 truncate">
                                            {{ $schedule->jobApplication->job->company->name ?? '-' }} • {{ $candProfile->user->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Waktu Wawancara -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5 font-bold text-gray-900 dark:text-white">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $interviewDate->translatedFormat('l, d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                            {{ $interviewDate->translatedFormat('H:i') }} WIB
                                        </span>
                                        @if ($isToday)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 animate-pulse">
                                                Hari Ini
                                            </span>
                                        @elseif ($isPast)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 dark:bg-gray-800 text-gray-500">
                                                Lewat
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400">
                                                Mendatang
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Metode & Lokasi -->
                            <td class="py-4 px-4">
                                <div class="space-y-1">
                                    @if ($isOnline)
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            <span>Online (Video Call)</span>
                                        </div>

                                        @if ($schedule->meeting_link)
                                            <div class="flex items-center gap-2 pt-0.5">
                                                <a href="{{ $schedule->meeting_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline max-w-[200px] truncate">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                    <span class="truncate">{{ $schedule->meeting_link }}</span>
                                                </a>
                                                <button type="button" @click="copyToClipboard('{{ $schedule->meeting_link }}')" class="p-1 rounded text-gray-400 hover:text-indigo-600 hover:bg-gray-100 dark:hover:bg-slate-800 transition" title="Salin Link Meeting">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <p class="text-[11px] text-gray-400 italic">Tautan meeting belum diisi</p>
                                        @endif
                                    @else
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span>Tatap Muka (Offline)</span>
                                        </div>
                                        <p class="text-[11px] text-gray-700 dark:text-gray-300 font-medium line-clamp-2 max-w-xs mt-1">
                                            {{ $schedule->location }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            <!-- Pewawancara -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    @php
                                        $interviewerPhoto = $schedule->user?->avatar ?? $schedule->user?->employeeProfile?->photo;
                                        $interviewerPhotoUrl = null;
                                        if ($interviewerPhoto) {
                                            $interviewerPhotoUrl = \Illuminate\Support\Str::startsWith($interviewerPhoto, ['http://', 'https://'])
                                                ? $interviewerPhoto
                                                : asset('storage/' . $interviewerPhoto);
                                        }
                                    @endphp
                                    @if ($interviewerPhotoUrl)
                                        <img src="{{ $interviewerPhotoUrl }}" alt="{{ $schedule->user->name ?? 'Pewawancara' }}" class="w-7 h-7 rounded-full object-cover border border-gray-200 dark:border-slate-700 shrink-0 shadow-2xs">
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-indigo-50 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-slate-700 flex items-center justify-center font-bold text-[10px] shrink-0">
                                            {{ strtoupper(substr($schedule->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-gray-800 dark:text-gray-200 text-xs">
                                            {{ $schedule->user->name ?? '-' }}
                                        </p>
                                        <p class="text-[10px] text-gray-400">
                                            {{ $schedule->user->role->name ?? 'Recruiter/Admin' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Status Sesi & Tanda Hasil Keputusan -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="flex flex-col items-start gap-1.5">
                                    <!-- 1. Status Sesi Jadwal -->
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusClasses }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ strtolower($status) === 'completed' ? 'bg-emerald-500' : (strtolower($status) === 'scheduled' ? 'bg-indigo-500' : (strtolower($status) === 'rescheduled' ? 'bg-amber-500' : 'bg-rose-500')) }}"></span>
                                        {{ $status }}
                                    </span>

                                    <!-- 2. Status Keputusan Pelamar -->
                                    @if ($schedule->jobApplication)
                                        @php
                                            $appStatus = strtolower($schedule->jobApplication->status);
                                        @endphp
                                        @if ($appStatus === 'accepted')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>Lolos (Diterima)</span>
                                            </span>
                                        @elseif ($appStatus === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                                <svg class="w-3 h-3 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span>Tidak Lolos</span>
                                            </span>
                                        @elseif (strtolower($status) === 'completed')
                                            <button type="button" @click="openCompleteModal({{ json_encode($schedule) }})" title="Klik untuk menentukan keputusan lolos/tidak lolos pelamar ini" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800 hover:border-amber-400 dark:hover:border-amber-600 transition cursor-pointer group">
                                                <svg class="w-3 h-3 text-amber-500 shrink-0 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Menunggu Keputusan</span>
                                            </button>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                <svg class="w-3 h-3 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span>Menunggu Sesi</span>
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Selesaikan Wawancara / Beri Keputusan (Quick Complete) -->
                                    @php
                                        $canCompleteOrEvaluate = $schedule->status !== 'Completed' || ($schedule->jobApplication && strtolower($schedule->jobApplication->status) === 'interview');
                                    @endphp
                                    @if ($canCompleteOrEvaluate)
                                        <button type="button" @click="openCompleteModal({{ json_encode($schedule) }})" title="{{ $schedule->status === 'Completed' ? 'Tentukan Keputusan (Lolos / Tidak Lolos)' : 'Tandai Selesai & Beri Catatan' }}" class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    @endif

                                    <!-- Edit Jadwal -->
                                    <button type="button" @click="openEditModal({{ json_encode($schedule) }})" title="Edit / Reschedule Jadwal" class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>

                                    <!-- Hapus Jadwal -->
                                    <button type="button" @click="openDeleteModal({{ json_encode($schedule) }})" title="Hapus Jadwal Wawancara" class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-500 mx-auto flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Tidak ada jadwal wawancara ditemukan</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Belum ada jadwal yang cocok dengan filter pencarian Anda atau belum ada wawancara yang dijadwalkan.
                                    </p>
                                    <div class="pt-2">
                                        <button type="button" @click="openCreateModal()" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-sm transition">
                                            Jadwalkan Wawancara Pertama
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($schedules->hasPages() || $perPage != 10)
            <div class="p-4 sm:p-5 border-t border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
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
                @if ($schedules->hasPages())
                    <div>
                        {{ $schedules->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- ==================== MODAL: BUAT JADWAL WAWANCARA BARU (2-Step Wizard) ==================== -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6" aria-labelledby="modal-title-create" role="dialog" aria-modal="true">

        {{-- Backdrop --}}
        <div x-show="showCreateModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showCreateModal = false"
             class="fixed inset-0 bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm">
        </div>

        {{-- Modal Panel --}}
        <div x-show="showCreateModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-800 overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-tight" id="modal-title-create">Buat Jadwal Wawancara</h3>
                        <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-0.5">
                            <span x-text="createStep === 1 ? 'Langkah 1 dari 2 — Data Jadwal' : 'Langkah 2 dari 2 — Konfirmasi'"></span>
                        </p>
                    </div>
                </div>
                <button @click="showCreateModal = false; createStep = 1" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Progress Bar --}}
            <div class="h-0.5 bg-gray-100 dark:bg-slate-800">
                <div class="h-0.5 bg-indigo-500 transition-all duration-500"
                     :style="createStep === 1 ? 'width: 50%' : 'width: 100%'"></div>
            </div>

            {{-- FORM (semua step dalam satu form) --}}
            <form action="{{ $routePrefix }}" method="POST" id="form-create-interview">
                @csrf
                <input type="hidden" name="application_status_override" value="Interview">
                <input type="hidden" name="send_email" id="create_send_email_val" :value="createData.sendEmail ? '1' : '0'">
                <input type="hidden" name="job_applications_id" :value="createData.job_applications_id">
                <input type="hidden" name="users_id" :value="createData.users_id">
                <input type="hidden" name="interview_date" :value="createData.interview_date">
                <input type="hidden" name="interview_type" :value="createData.interview_type">
                <input type="hidden" name="location" :value="createData.location">
                <input type="hidden" name="meeting_link" :value="createData.meeting_link">
                <input type="hidden" name="notes" :value="createData.notes">
                <input type="hidden" name="status" value="Scheduled">

                {{-- ===========================
                     STEP 1 — DATA JADWAL
                     =========================== --}}
                <div x-show="createStep === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="px-6 py-5 space-y-4 max-h-[68vh] overflow-y-auto">

                        {{-- Pilih Kandidat --}}
                        <div class="relative" @click.outside="isCandidateDropdownOpen = false">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                                Kandidat Pelamar <span class="text-rose-500">*</span>
                                <span class="font-normal text-gray-400 ml-1">(Status: Wawancara / Shortlisted)</span>
                            </label>

                            {{-- Selected Card --}}
                            <div x-show="selectedCandidate" class="p-3 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <template x-if="selectedCandidate && selectedCandidate.photo">
                                        <img :src="selectedCandidate.photo" :alt="selectedCandidate.name" class="w-9 h-9 rounded-full object-cover border border-indigo-200 dark:border-indigo-800 shrink-0">
                                    </template>
                                    <template x-if="!selectedCandidate || !selectedCandidate.photo">
                                        <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                            <span x-text="selectedCandidate ? selectedCandidate.name.charAt(0).toUpperCase() : 'K'"></span>
                                        </div>
                                    </template>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 dark:text-white text-xs truncate" x-text="selectedCandidate ? selectedCandidate.name : ''"></p>
                                        <p class="text-[11px] text-indigo-600 dark:text-indigo-400 truncate" x-text="selectedCandidate ? (selectedCandidate.job_title + (selectedCandidate.company ? ' • ' + selectedCandidate.company : '')) : ''"></p>
                                        <p class="text-[10px] text-gray-400 truncate" x-text="selectedCandidate ? selectedCandidate.email : ''"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300" x-text="selectedCandidate ? selectedCandidate.status : ''"></span>
                                    <button type="button" @click="clearCandidate()" class="p-1 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition" title="Ganti Kandidat">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Trigger --}}
                            <div x-show="!selectedCandidate">
                                <button type="button" @click="isCandidateDropdownOpen = !isCandidateDropdownOpen"
                                    class="w-full px-3.5 py-2.5 text-left text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-500 hover:border-indigo-400 dark:hover:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition flex items-center justify-between">
                                    <span class="text-gray-400">— Klik untuk Cari &amp; Pilih Pelamar —</span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="isCandidateDropdownOpen ? 'rotate-180 text-indigo-600' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Dropdown --}}
                            <div x-show="isCandidateDropdownOpen" x-cloak
                                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-30 mt-1 w-full bg-white dark:bg-slate-900 rounded-2xl border border-gray-200 dark:border-slate-800 shadow-xl overflow-hidden">
                                <div class="p-2.5 border-b border-gray-100 dark:border-slate-800 bg-gray-50/70 dark:bg-slate-800/40">
                                    <div class="relative">
                                        <input type="text" x-model="candidateSearch" placeholder="Cari nama, posisi, atau email..."
                                            class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                                            @keydown.escape="isCandidateDropdownOpen = false">
                                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="max-h-48 overflow-y-auto divide-y divide-gray-50 dark:divide-slate-800/60 p-1">
                                    <template x-for="cand in filteredCandidates()" :key="cand.id">
                                        <button type="button" @click="selectCandidate(cand)"
                                            class="w-full text-left p-2.5 rounded-xl hover:bg-indigo-50/80 dark:hover:bg-slate-800/80 transition flex items-center justify-between gap-3 group">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <template x-if="cand.photo">
                                                    <img :src="cand.photo" :alt="cand.name" class="w-8 h-8 rounded-full object-cover border border-gray-200 dark:border-slate-700 shrink-0">
                                                </template>
                                                <template x-if="!cand.photo">
                                                    <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 group-hover:bg-indigo-600 group-hover:text-white text-gray-600 dark:text-gray-300 flex items-center justify-center font-bold text-[11px] shrink-0 transition">
                                                        <span x-text="cand.name.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                </template>
                                                <div class="min-w-0">
                                                    <p class="font-bold text-gray-900 dark:text-white text-xs truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400" x-text="cand.name"></p>
                                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate" x-text="cand.job_title + (cand.company ? ' • ' + cand.company : '')"></p>
                                                </div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/60 shrink-0" x-text="cand.status"></span>
                                        </button>
                                    </template>
                                    <div x-show="filteredCandidates().length === 0" class="py-6 px-4 text-center">
                                        <svg class="w-6 h-6 text-gray-300 dark:text-slate-600 mx-auto mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        <p class="text-xs font-semibold text-gray-600 dark:text-slate-300">Tidak ada kandidat ditemukan</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">Pastikan status pelamar sudah 'Interview' atau 'Shortlisted'.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Waktu & Pewawancara --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="create_interview_date" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                                    Waktu Wawancara <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" id="create_interview_date" x-model="createData.interview_date"
                                    class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            </div>
                            <div>
                                <label for="create_users_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                                    Pewawancara <span class="text-rose-500">*</span>
                                </label>
                                <select id="create_users_id" x-model="createData.users_id"
                                    class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    @foreach ($interviewers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? 'Staff' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Metode --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                                Metode Wawancara <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button"
                                    @click="createData.interview_type = 'online'; if(!createData.location || createData.location.includes('Kantor')) createData.location = 'Online (Google Meet)';"
                                    :class="createData.interview_type === 'online' ? 'border-indigo-500 bg-indigo-50/80 dark:bg-indigo-950/40 ring-2 ring-indigo-500/20' : 'border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800'"
                                    class="p-3 rounded-xl border text-left flex items-center gap-3 transition">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800 dark:text-white">Online</p>
                                        <p class="text-[10px] text-gray-400">Google Meet / Zoom</p>
                                    </div>
                                </button>
                                <button type="button"
                                    @click="createData.interview_type = 'offline'; if(!createData.location || createData.location.includes('Online')) createData.location = 'Kantor - Ruang Wawancara HR';"
                                    :class="createData.interview_type === 'offline' ? 'border-purple-500 bg-purple-50/80 dark:bg-purple-950/40 ring-2 ring-purple-500/20' : 'border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800'"
                                    class="p-3 rounded-xl border text-left flex items-center gap-3 transition">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800 dark:text-white">Tatap Muka</p>
                                        <p class="text-[10px] text-gray-400">On-site di Kantor</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        {{-- Link Meeting (Online only) --}}
                        <div x-show="createData.interview_type === 'online'" x-transition>
                            <label for="create_meeting_link" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                                Tautan Meeting (URL) <span class="text-blue-500">*</span>
                            </label>
                            <input type="url" id="create_meeting_link" x-model="createData.meeting_link"
                                placeholder="https://meet.google.com/xxx-yyyy-zzz atau link Zoom"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            <p class="text-[10px] text-gray-400 mt-1">Kandidat dapat langsung mengklik tautan dari akun mereka.</p>
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label for="create_location" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                                Lokasi / Ruangan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="create_location" x-model="createData.location"
                                placeholder="Contoh: Online (Google Meet) atau Gd. A Lt. 2 Ruang HR"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label for="create_notes" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                                Catatan untuk Kandidat <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea id="create_notes" rows="2" x-model="createData.notes"
                                placeholder="Contoh: Harap hadir 10 menit sebelum waktu dan siapkan kartu identitas..."
                                class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition resize-none"></textarea>
                        </div>

                    </div>

                    {{-- Footer Step 1 --}}
                    <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50">
                        <button type="button" @click="showCreateModal = false; createStep = 1"
                            class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Batal
                        </button>
                        <button type="button"
                            @click="
                                if (!createData.job_applications_id) { alert('Pilih kandidat terlebih dahulu.'); return; }
                                if (!createData.interview_date) { alert('Isi waktu wawancara terlebih dahulu.'); return; }
                                if (!createData.location) { alert('Isi lokasi / ruangan terlebih dahulu.'); return; }
                                createStep = 2;
                            "
                            class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-600/20 transition">
                            Lanjutkan
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- ===========================
                     STEP 2 — KONFIRMASI & EMAIL
                     =========================== --}}
                <div x-show="createStep === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="px-6 py-5 space-y-4">

                        {{-- Ringkasan Jadwal --}}
                        <div class="rounded-2xl border border-gray-100 dark:border-slate-800 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-slate-800/60 border-b border-gray-100 dark:border-slate-800">
                                <p class="text-xs font-bold text-gray-700 dark:text-slate-300">Ringkasan Jadwal Wawancara</p>
                            </div>
                            <div class="divide-y divide-gray-50 dark:divide-slate-800/60">
                                {{-- Kandidat --}}
                                <div class="px-4 py-3 flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-500 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold">Kandidat</p>
                                        <p class="text-xs font-bold text-gray-800 dark:text-white truncate" x-text="selectedCandidate ? selectedCandidate.name : '—'"></p>
                                        <p class="text-[11px] text-indigo-500 dark:text-indigo-400 truncate" x-text="selectedCandidate ? (selectedCandidate.job_title + (selectedCandidate.company ? ' • ' + selectedCandidate.company : '')) : ''"></p>
                                    </div>
                                </div>
                                {{-- Waktu --}}
                                <div class="px-4 py-3 flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-500 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold">Waktu</p>
                                        <p class="text-xs font-bold text-gray-800 dark:text-white"
                                           x-text="createData.interview_date ? new Date(createData.interview_date).toLocaleString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit'}) + ' WIB' : '—'"></p>
                                    </div>
                                </div>
                                {{-- Metode & Lokasi --}}
                                <div class="px-4 py-3 flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg shrink-0 flex items-center justify-center"
                                         :class="createData.interview_type === 'online' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-500' : 'bg-purple-50 dark:bg-purple-950/40 text-purple-500'">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  x-bind:d="createData.interview_type === 'online' ? 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z' : 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold" x-text="createData.interview_type === 'online' ? 'Online' : 'Tatap Muka (Offline)'"></p>
                                        <p class="text-xs font-bold text-gray-800 dark:text-white" x-text="createData.location || '—'"></p>
                                        <p class="text-[11px] text-blue-500 dark:text-blue-400 truncate" x-show="createData.meeting_link" x-text="createData.meeting_link"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status Otomatis Badge --}}
                        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs text-emerald-700 dark:text-emerald-300">
                                Status kandidat akan otomatis berubah ke <strong>Wawancara (Interview)</strong>.
                            </p>
                        </div>

                        {{-- Checkbox Email --}}
                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                               :class="createData.sendEmail ? 'border-indigo-500 bg-indigo-50/60 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50'">
                            <div class="relative shrink-0 mt-0.5">
                                <input type="checkbox" x-model="createData.sendEmail" class="sr-only peer"
                                       @change="document.getElementById('create_send_email_val').value = createData.sendEmail ? '1' : '0'">
                                <div class="w-4.5 h-4.5 w-[18px] h-[18px] rounded flex items-center justify-center border-2 transition-all"
                                     :class="createData.sendEmail ? 'bg-indigo-600 border-indigo-600' : 'bg-white dark:bg-slate-900 border-gray-300 dark:border-slate-600'">
                                    <svg x-show="createData.sendEmail" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-indigo-500 dark:text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs font-semibold text-gray-800 dark:text-white">Kirim Email Undangan ke Kandidat</p>
                                </div>
                                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 leading-relaxed">
                                    Kandidat akan menerima email resmi berisi detail jadwal wawancara, lokasi, dan catatan tambahan.
                                </p>
                                <p class="text-[11px] font-medium mt-1.5"
                                   :class="createData.sendEmail ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400'"
                                   x-text="createData.sendEmail ? 'Email akan dikirim ke: ' + (selectedCandidate ? selectedCandidate.email : '—') : 'Email tidak dikirim'">
                                </p>
                            </div>
                        </label>

                    </div>

                    {{-- Footer Step 2 --}}
                    <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-900/50">
                        <button type="button" @click="createStep = 1"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali
                        </button>
                        <button type="submit" form="form-create-interview"
                            class="inline-flex items-center gap-2 px-6 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 dark:bg-[#93F514] dark:text-black dark:hover:bg-[#82dc12] rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan &amp; Jadwalkan
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- ==================== MODAL: EDIT / RESCHEDULE JADWAL ==================== -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showEditModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title-edit">
                                Ubah Jadwal Wawancara
                            </h3>
                        </div>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'{{ $routePrefix }}/' + editData.id" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Info Pelamar -->
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200/60 dark:border-slate-700 text-xs flex items-center gap-3">
                            <template x-if="editData.applicant_photo">
                                <img :src="editData.applicant_photo" :alt="editData.applicant_name" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-700 shrink-0 shadow-sm">
                            </template>
                            <template x-if="!editData.applicant_photo">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    <span x-text="editData.applicant_name ? editData.applicant_name.substring(0, 2).toUpperCase() : 'K'"></span>
                                </div>
                            </template>
                            <div class="min-w-0">
                                <span class="text-gray-400 block text-[10px] uppercase font-semibold tracking-wider">Kandidat & Posisi</span>
                                <span class="font-bold text-gray-900 dark:text-white block truncate text-sm" x-text="editData.applicant_name"></span>
                                <span class="text-indigo-600 dark:text-indigo-400 block text-xs font-semibold truncate" x-text="editData.job_title"></span>
                            </div>
                        </div>

                        <!-- Waktu & Pewawancara -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="edit_interview_date" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Waktu Wawancara (WIB) <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" name="interview_date" id="edit_interview_date" x-model="editData.interview_date" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            </div>

                            <div>
                                <label for="edit_users_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Pewawancara <span class="text-rose-500">*</span>
                                </label>
                                <select name="users_id" id="edit_users_id" x-model="editData.users_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    @foreach ($interviewers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? 'Staff' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Metode Online vs Offline -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                                Metode Wawancara <span class="text-rose-500">*</span>
                            </label>
                            <input type="hidden" name="interview_type" :value="editData.interview_type">
                            
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="editData.interview_type = 'online'" :class="editData.interview_type === 'online' ? 'border-indigo-600 bg-indigo-50/70 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20' : 'border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-gray-400'" class="p-3 rounded-xl border text-left flex items-center gap-3 transition">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-bold">Online</span>
                                </button>

                                <button type="button" @click="editData.interview_type = 'offline'" :class="editData.interview_type === 'offline' ? 'border-purple-600 bg-purple-50/70 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 ring-2 ring-purple-500/20' : 'border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 text-gray-600 dark:text-gray-400'" class="p-3 rounded-xl border text-left flex items-center gap-3 transition">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-bold">Tatap Muka (Offline)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Link Meeting (Khusus Online) -->
                        <div x-show="editData.interview_type === 'online'" x-transition>
                            <label for="edit_meeting_link" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Tautan Meeting (URL)
                            </label>
                            <input type="url" name="meeting_link" id="edit_meeting_link" x-model="editData.meeting_link" placeholder="https://meet.google.com/xxx-yyyy-zzz" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="edit_location" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Lokasi / Ruangan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="location" id="edit_location" x-model="editData.location" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        </div>

                        <!-- Status Jadwal -->
                        <div>
                            <label for="edit_status" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Status Jadwal Wawancara <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" id="edit_status" x-model="editData.status" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                <option value="Scheduled">Scheduled (Dijadwalkan)</option>
                                <option value="Completed">Completed (Selesai Dilaksanakan)</option>
                                <option value="Rescheduled">Rescheduled (Dijadwalkan Ulang)</option>
                                <option value="Cancelled">Cancelled (Dibatalkan)</option>
                                <option value="No Show">No Show (Kandidat Tidak Hadir)</option>
                            </select>
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label for="edit_notes" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Catatan Perubahan (Opsional)
                            </label>
                            <textarea name="notes" id="edit_notes" rows="2" x-model="editData.notes" placeholder="Tambahkan catatan perubahan jadwal..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"></textarea>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition cursor-pointer">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- ==================== MODAL: SELESAIKAN & EVALUASI HASIL WAWANCARA ==================== -->
    <div x-show="showCompleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-complete" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCompleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCompleteModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showCompleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title-complete">
                                    Selesaikan & Beri Nilai Wawancara
                                </h3>
                                <p class="text-[11px] text-gray-400">Tentukan keputusan lolos / tidak lolos dan catatan evaluasi pelamar</p>
                            </div>
                        </div>
                        <button @click="showCompleteModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'{{ $routePrefix }}/' + completeData.id" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="users_id" :value="completeData.users_id">
                        <input type="hidden" name="interview_date" :value="completeData.interview_date">
                        <input type="hidden" name="interview_type" :value="completeData.interview_type">
                        <input type="hidden" name="location" :value="completeData.location">
                        <input type="hidden" name="meeting_link" :value="completeData.meeting_link">
                        <input type="hidden" name="status" value="Completed">

                        <!-- Info Pelamar Card -->
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800/80 border border-gray-200/70 dark:border-slate-700 text-xs flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="completeData.applicant_photo">
                                    <img :src="completeData.applicant_photo" :alt="completeData.applicant_name" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-700 shrink-0 shadow-sm">
                                </template>
                                <template x-if="!completeData.applicant_photo">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                        <span x-text="completeData.applicant_name ? completeData.applicant_name.substring(0, 2).toUpperCase() : 'K'"></span>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <span class="text-gray-400 font-semibold block text-[10px] uppercase tracking-wider">Kandidat & Posisi</span>
                                    <span class="font-bold text-gray-900 dark:text-white block mt-0.5 text-xs truncate" x-text="completeData.applicant_name"></span>
                                    <span class="text-indigo-600 dark:text-indigo-400 block text-[11px] font-semibold truncate" x-text="completeData.job_title + (completeData.company_name ? ' • ' + completeData.company_name : '')"></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shrink-0">
                                Selesai Wawancara
                            </span>
                        </div>

                        <!-- 1. Keputusan Hasil Wawancara -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1.5">
                                Keputusan Hasil Wawancara <span class="text-rose-500">*</span>
                            </label>
                            <input type="hidden" name="application_decision" :value="completeData.application_decision">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <!-- Option: Lolos & Diterima (Accepted) -->
                                <button type="button" @click="completeData.application_decision = 'Accepted'" :class="completeData.application_decision === 'Accepted' ? 'border-emerald-500 bg-emerald-50/80 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-200 ring-2 ring-emerald-500/20' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50'" class="p-2.5 rounded-xl border text-left flex items-center gap-2.5 transition">
                                    <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        ✓
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold truncate">Lolos & Diterima</p>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400">Status: Accepted (Offering)</p>
                                    </div>
                                </button>

                                <!-- Option: Tidak Lolos (Rejected) -->
                                <button type="button" @click="completeData.application_decision = 'Rejected'" :class="completeData.application_decision === 'Rejected' ? 'border-rose-500 bg-rose-50/80 dark:bg-rose-950/50 text-rose-800 dark:text-rose-200 ring-2 ring-rose-500/20' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50'" class="p-2.5 rounded-xl border text-left flex items-center gap-2.5 transition">
                                    <div class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        ✕
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold truncate">Tidak Lolos</p>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400">Status: Rejected (Gagal)</p>
                                    </div>
                                </button>

                                <!-- Option: Lolos ke Tahap Berikutnya (Interview) -->
                                <button type="button" @click="completeData.application_decision = 'Interview'" :class="completeData.application_decision === 'Interview' ? 'border-indigo-500 bg-indigo-50/80 dark:bg-indigo-950/50 text-indigo-800 dark:text-indigo-200 ring-2 ring-indigo-500/20' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50'" class="p-2.5 rounded-xl border text-left flex items-center gap-2.5 transition">
                                    <div class="w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        ➔
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold truncate">Tahap Berikutnya</p>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400">Lanjut Interview User/Direksi</p>
                                    </div>
                                </button>

                                <!-- Option: Simpan Saja (Keep) -->
                                <button type="button" @click="completeData.application_decision = 'keep'" :class="completeData.application_decision === 'keep' ? 'border-gray-500 bg-gray-100 dark:bg-slate-800 text-gray-800 dark:text-gray-200 ring-2 ring-gray-400/20' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50'" class="p-2.5 rounded-xl border text-left flex items-center gap-2.5 transition">
                                    <div class="w-6 h-6 rounded-full bg-gray-400 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        •
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold truncate">Simpan Evaluasi</p>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400">Keputusan ditentukan nanti</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- 2. Nilai / Skor Wawancara -->
                        <div>
                            <label for="complete_score" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Nilai / Skor Wawancara (Skala 0 - 100) <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="interview_score" id="complete_score" min="0" max="100" step="1" x-model="completeData.score" placeholder="Contoh: 85" class="w-full pl-3 pr-12 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-gray-400 pointer-events-none">
                                    / 100
                                </span>
                            </div>
                        </div>

                        <!-- 3. Catatan Evaluasi Wawancara -->
                        <div>
                            <label for="complete_notes" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Catatan Hasil / Feedback Evaluasi Wawancara
                            </label>
                            <textarea name="notes" id="complete_notes" rows="3" x-model="completeData.notes" placeholder="Contoh: Kandidat memiliki komunikasi yang sangat baik, pemahaman teknis mendalam, direkomendasikan untuk offering..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition"></textarea>
                            <span class="text-[10px] text-gray-400 mt-1 block">Catatan dan keputusan ini akan tersimpan otomatis ke riwayat seleksi pelamar.</span>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showCompleteModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Simpan Hasil & Selesaikan</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- ==================== MODAL: KONFIRMASI HAPUS JADWAL ==================== -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-delete" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDeleteModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>

                    <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title-delete">
                        Hapus Jadwal Wawancara?
                    </h3>
                    
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Apakah Anda yakin ingin menghapus jadwal wawancara untuk kandidat ini? Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="p-3 my-3 rounded-xl bg-gray-50 dark:bg-slate-800/70 border border-gray-200/60 dark:border-slate-700 text-xs flex items-center gap-3 text-left">
                        <template x-if="deleteData.applicant_photo">
                            <img :src="deleteData.applicant_photo" :alt="deleteData.applicant_name" class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-slate-700 shrink-0 shadow-xs">
                        </template>
                        <template x-if="!deleteData.applicant_photo">
                            <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                <span x-text="deleteData.applicant_name ? deleteData.applicant_name.substring(0, 2).toUpperCase() : 'K'"></span>
                            </div>
                        </template>
                        <div class="min-w-0">
                            <span class="font-bold text-gray-900 dark:text-white block truncate text-xs" x-text="deleteData.applicant_name"></span>
                            <span class="text-[10px] text-gray-400 block truncate">Jadwal wawancara yang akan dihapus</span>
                        </div>
                    </div>

                    <form :action="'{{ $routePrefix }}/' + deleteData.id" method="POST" class="mt-6 flex items-center justify-center gap-3">
                        @csrf
                        @method('DELETE')

                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-md shadow-rose-500/20 transition">
                            Ya, Hapus Jadwal
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>
