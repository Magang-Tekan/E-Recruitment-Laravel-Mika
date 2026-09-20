<div class="space-y-6" x-data="{ 
    showCreateModal: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }},
    showEditModal: {{ $errors->any() && old('is_edit') ? 'true' : 'false' }},
    showDeleteModal: false,
    showDetailModal: false,

    createCategoryId: '{{ old('category_id', '') }}',
    createSelectedQuestions: [],
    createTargetScope: '{{ old('target_scope', old('department_ids') ? 'specific' : 'all') }}',
    createSelectedDepartments: {{ json_encode(array_map('strval', old('department_ids', []))) }},

    editData: {
        id: '{{ old('id', '') }}',
        target_scope: 'all',
        department_ids: [],
        target_employee_type: '{{ old('target_employee_type', 'all') }}',
        category_id: '{{ old('category_id', '') }}',
        title: '{{ old('title', '') }}',
        duration_minutes: '{{ old('duration_minutes', '60') }}',
        passing_score: '{{ old('passing_score', '70') }}',
        total_questions: '{{ old('total_questions', '10') }}',
        is_random: {{ old('is_random') ? 'true' : 'false' }},
        selected_questions: []
    },

    deleteData: {
        id: '',
        title: '',
        attempts_count: 0
    },
    forceDeleteConfirmed: false,

    detailData: {
        id: '',
        departments: [],
        department_name: '',
        target_employee_type: 'all',
        category_name: '',
        title: '',
        duration_minutes: 0,
        passing_score: 0,
        total_questions: 0,
        is_random: false,
        questions: []
    },

    categoriesMap: {{ json_encode($categories->pluck('name', 'id')) }},
    isDiscCategory(catId) {
        let name = this.categoriesMap[catId] || '';
        return name.toLowerCase().includes('disc');
    },

    openEditModal(test) {
        let qIds = [];
        if (test.questions) {
            qIds = test.questions.map(q => q.id.toString());
        }

        let deptIds = [];
        if (test.departments && test.departments.length > 0) {
            deptIds = test.departments.map(d => d.id.toString());
        } else if (test.department_id) {
            deptIds = [test.department_id.toString()];
        }

        this.editData = {
            id: test.id,
            target_scope: deptIds.length > 0 ? 'specific' : 'all',
            department_ids: deptIds,
            target_employee_type: test.target_employee_type || 'all',
            category_id: test.category_id,
            title: test.title,
            duration_minutes: test.duration_minutes,
            passing_score: test.passing_score,
            total_questions: test.total_questions,
            is_random: Boolean(test.is_random),
            selected_questions: qIds
        };
        this.showEditModal = true;
    },

    openDeleteModal(test) {
        this.deleteData = {
            id: test.id,
            title: test.title,
            attempts_count: Number(test.attempts_count) || 0
        };
        this.forceDeleteConfirmed = false;
        this.showDeleteModal = true;
    },

    openDetailModal(test) {
        let depts = test.departments || [];
        if (depts.length === 0 && test.department) {
            depts = [test.department];
        }

        this.detailData = {
            id: test.id,
            departments: depts,
            department_name: depts.length > 0 
                ? depts.map(d => d.name + (d.company ? ' (' + d.company.name + ')' : '')).join(', ')
                : 'Semua Departemen (Umum)',
            target_employee_type: test.target_employee_type || 'all',
            category_name: test.category ? test.category.name : '-',
            title: test.title,
            duration_minutes: test.duration_minutes,
            passing_score: test.passing_score,
            total_questions: test.total_questions,
            is_random: Boolean(test.is_random),
            questions: test.questions || []
        };
        this.showDetailModal = true;
    }
}">

    <!-- Session Notifications -->
    @if (session('create'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold">{{ session('create') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

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

    @if (session('delete'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span class="text-xs font-semibold">{{ session('delete') }}</span>
            </div>
            <button @click="show = false" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-sm">
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
        
        <!-- Header & Action Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Merakit Paket Asesmen Karyawan</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Atur durasi, KKM, jumlah soal, opsi acak, dan rakit pertanyaan tes untuk karyawan tetap, kontrak, magang.</p>
            </div>

            <button type="button" @click="showCreateModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold transition active:scale-95 shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Paket Asesmen</span>
            </button>
        </div>

        <!-- Filter & Search Toolbar Row -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="relative lg:col-span-3">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari judul asesmen..." 
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

                <!-- Filter Departemen (Menyesuaikan dengan perusahaan yang dipilih) -->
                <div class="relative lg:col-span-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <select wire:model.live="departmentId" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">{{ $companyId ? 'Semua Departemen di Perusahaan Ini' : 'Semua Departemen' }}</option>
                        <option value="all">Khusus Umum (Semua)</option>
                        @foreach ($filterDepartments as $dept)
                            <option value="{{ $dept->id }}">
                                {{ $dept->name }} @if(!$companyId && $dept->company)({{ $dept->company->name }})@endif
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Kategori Soal -->
                <div class="relative lg:col-span-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <select wire:model.live="categoryId" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Toolbar Bottom Row: Quick Target Peserta Badges & Reset Button -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase mr-1">Sasaran:</span>
                    <button type="button" wire:click="$set('targetEmployeeType', '')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $targetEmployeeType === '' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Semua
                    </button>
                    <button type="button" wire:click="$set('targetEmployeeType', 'all')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $targetEmployeeType === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Umum (Semua)
                    </button>
                    <button type="button" wire:click="$set('targetEmployeeType', 'internship')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $targetEmployeeType === 'internship' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Magang
                    </button>
                    <button type="button" wire:click="$set('targetEmployeeType', 'permanent')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $targetEmployeeType === 'permanent' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Karyawan Tetap
                    </button>
                    <button type="button" wire:click="$set('targetEmployeeType', 'contract')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $targetEmployeeType === 'contract' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Karyawan Kontrak
                    </button>
                </div>

                @if ($search || $companyId || $departmentId || $categoryId || $targetEmployeeType)
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
        <div wire:loading wire:target="search, companyId, departmentId, categoryId, targetEmployeeType, previousPage, nextPage, gotoPage, resetFilters" class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center z-10 transition">
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
                        <th class="px-6 py-4">Judul Paket Asesmen</th>
                        <th class="px-6 py-4">Target Departemen & Sasaran</th>
                        <th class="px-6 py-4">Kategori Soal</th>
                        <th class="px-6 py-4">Durasi & KKM</th>
                        <th class="px-6 py-4">Soal & Opsi Acak</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-gray-700 dark:text-slate-300">
                    @forelse ($tests as $index => $t)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-500 dark:text-slate-400">
                                {{ $tests->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-semibold shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-gray-900 dark:text-white">{{ $t->title }}</span>
                                        <span class="text-[11px] text-gray-400 font-normal">{{ $t->attempts_count }} Percobaan Karyawan</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5 items-start">
                                    @php
                                        $deptCount = $t->departments->count();
                                    @endphp
                                    @if ($deptCount > 0)
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @foreach ($t->departments->take(2) as $d)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800" title="{{ $d->name }} {{ $d->company ? '('.$d->company->name.')' : '' }}">
                                                    {{ $d->name }}
                                                </span>
                                            @endforeach
                                            @if ($deptCount > 2)
                                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 border border-gray-200 dark:border-slate-700" title="{{ $t->departments->pluck('name')->implode(', ') }}">
                                                    +{{ $deptCount - 2 }} lainnya
                                                </span>
                                            @endif
                                        </div>
                                    @elseif ($t->department)
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            {{ $t->department->name }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Semua Departemen
                                        </span>
                                    @endif

                                    @if ($t->target_employee_type === 'internship')
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            Magang
                                        </span>
                                    @elseif ($t->target_employee_type === 'permanent')
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            Karyawan Tetap
                                        </span>
                                    @elseif ($t->target_employee_type === 'contract')
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            Karyawan Kontrak
                                        </span>
                                    @else
                                        <span class="text-[10px] text-gray-400">
                                            Semua Karyawan & Magang
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700">
                                    {{ $t->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-1.5 text-gray-800 dark:text-slate-200 font-semibold">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $t->duration_minutes }} Menit
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-slate-400">
                                        @if (str_contains(strtolower($t->category?->name ?? ''), 'disc'))
                                            <span class="text-purple-600 dark:text-purple-400 font-semibold">Tanpa KKM (DISC)</span>
                                        @else
                                            KKM: <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ (float) $t->passing_score }}%</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700">
                                        {{ $t->questions_count }} Soal Terikat
                                    </span>
                                    <span class="block text-[11px] {{ $t->is_random ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }} font-medium">
                                        {{ $t->is_random ? '✓ Urutan Diacak' : '— Urutan Tetap' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button @click="openDetailModal({{ json_encode($t) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Detail Ujian">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="openEditModal({{ json_encode($t) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Edit Ujian">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="openDeleteModal({{ json_encode($t) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Hapus Ujian">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span class="text-sm font-medium">Belum ada paket asesmen karyawan</span>
                                    <span class="text-xs">Klik tombol "Buat Paket Asesmen" untuk merakit asesmen baru.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tests->hasPages() || $perPage != 10)
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
                @if ($tests->hasPages())
                    <div>
                        {{ $tests->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Create Test Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCreateModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Rakit Paket Asesmen Karyawan
                        </h3>
                        <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.employee_test.store') }}" method="POST" class="mt-4 space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Target Departemen -->
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Target Departemen
                                </label>
                                
                                <div class="flex items-center gap-3 mb-2">
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="target_scope" value="all" x-model="createTargetScope" @change="createSelectedDepartments = []" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Semua Departemen</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="target_scope" value="specific" x-model="createTargetScope" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Pilih Spesifik</span>
                                    </label>
                                </div>

                                <div x-show="createTargetScope === 'specific'" x-transition class="border border-gray-200 dark:border-slate-700 rounded-xl p-2.5 bg-gray-50 dark:bg-slate-800/60 space-y-2">
                                    <div class="flex items-center justify-between pb-1.5 border-b border-gray-200 dark:border-slate-700">
                                        <span class="text-[11px] font-semibold text-gray-600 dark:text-slate-400" x-text="createSelectedDepartments.length + ' dipilih'"></span>
                                        <button type="button" 
                                            @click="
                                                let allDeptIds = {{ json_encode($departments->pluck('id')->map(fn($id) => (string)$id)) }};
                                                if (createSelectedDepartments.length === allDeptIds.length) {
                                                    createSelectedDepartments = [];
                                                } else {
                                                    createSelectedDepartments = allDeptIds;
                                                }
                                            " 
                                            class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                            <span x-text="createSelectedDepartments.length === {{ count($departments) }} ? 'Hapus Semua' : 'Pilih Semua'"></span>
                                        </button>
                                    </div>
                                    <div class="max-h-36 overflow-y-auto space-y-1 pr-1">
                                        @foreach ($departments as $dept)
                                            <label class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-800 cursor-pointer text-xs text-gray-700 dark:text-slate-300 transition">
                                                <input type="checkbox" name="department_ids[]" value="{{ $dept->id }}" x-model="createSelectedDepartments" class="rounded text-indigo-600 focus:ring-indigo-500">
                                                <span class="truncate">{{ $dept->name }} <span class="text-[10px] text-gray-400">{{ $dept->company ? '('.$dept->company->name.')' : '' }}</span></span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <template x-if="createTargetScope === 'all'">
                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 block mt-1 font-medium">
                                        ✓ Tes ini akan tersedia untuk seluruh departemen perusahaan.
                                    </span>
                                </template>
                                @error('department_ids')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sasaran / Target Peserta (Magang / Tetap / Semua) -->
                            <div class="sm:col-span-1">
                                <label for="create_target_employee_type" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Sasaran / Target Peserta <span class="text-rose-500">*</span>
                                </label>
                                <select name="target_employee_type" id="create_target_employee_type" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    <option value="all" {{ old('target_employee_type') == 'all' ? 'selected' : '' }}>Semua (Karyawan & Magang)</option>
                                    <option value="internship" {{ old('target_employee_type') == 'internship' ? 'selected' : '' }}>Magang (Internship)</option>
                                    <option value="permanent" {{ old('target_employee_type') == 'permanent' ? 'selected' : '' }}>Karyawan Tetap</option>
                                    <option value="contract" {{ old('target_employee_type') == 'contract' ? 'selected' : '' }}>Karyawan Kontrak</option>
                                </select>
                                <span class="text-[10px] text-gray-400">Tentukan siapa saja yang dapat melihat dan mengerjakan tes ini</span>
                                @error('target_employee_type')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Kategori Soal -->
                            <div class="sm:col-span-2">
                                <label for="create_category_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Kategori Soal <span class="text-rose-500">*</span>
                                </label>
                                <select name="category_id" id="create_category_id" x-model="createCategoryId" 
                                    @change="
                                        if (isDiscCategory(createCategoryId)) {
                                            $nextTick(() => {
                                                let kkmInput = document.getElementById('create_passing_score');
                                                let totalInput = document.getElementById('create_total_questions');
                                                if (kkmInput) kkmInput.value = 0;
                                                if (totalInput) totalInput.value = 24;
                                            });
                                        }
                                    "
                                    required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    <option value="">-- Pilih Kategori Soal --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Judul Asesmen -->
                        <div>
                            <label for="create_title" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Judul Paket Asesmen <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="title" id="create_title" value="{{ old('title') }}" required placeholder="Contoh: Evaluasi Kepribadian DISC Karyawan 2026" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('title')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Parameter Ujian: Durasi, KKM, Total Soal, Acak -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div>
                                <label for="create_duration" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Durasi (Menit) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="duration_minutes" id="create_duration" min="1" value="{{ old('duration_minutes', 60) }}" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('duration_minutes')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="create_passing_score" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Nilai KKM (%) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="passing_score" id="create_passing_score" min="0" max="100" 
                                    :value="isDiscCategory(createCategoryId) ? 0 : 75" 
                                    :readonly="isDiscCategory(createCategoryId)"
                                    :class="isDiscCategory(createCategoryId) ? 'opacity-60 bg-gray-200 cursor-not-allowed' : ''"
                                    required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                <template x-if="isDiscCategory(createCategoryId)">
                                    <p class="mt-1 text-[10px] text-purple-500 font-semibold">Tanpa KKM (Tes Profil)</p>
                                </template>
                                @error('passing_score')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="create_total_questions" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Jumlah Soal <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="total_questions" id="create_total_questions" min="1" 
                                    :value="isDiscCategory(createCategoryId) ? 24 : 10" 
                                    :readonly="isDiscCategory(createCategoryId)"
                                    :class="isDiscCategory(createCategoryId) ? 'opacity-60 bg-gray-200 cursor-not-allowed' : ''"
                                    required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                <template x-if="isDiscCategory(createCategoryId)">
                                    <p class="mt-1 text-[10px] text-purple-500 font-semibold">Standar 24 Soal DISC</p>
                                </template>
                                @error('total_questions')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Opsi Acak Soal
                                </label>
                                <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                    <input type="checkbox" name="is_random" value="1" {{ old('is_random') ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-700 dark:text-slate-300 font-medium">Acak Urutan Soal</span>
                                </label>
                            </div>
                        </div>

                        <!-- Question Picker Section -->
                        <div class="space-y-3 pt-3 border-t border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-gray-800 dark:text-slate-200">
                                        Pilih Pertanyaan dari Bank Soal
                                    </label>
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400">Centang soal-soal yang ingin disertakan ke dalam paket asesmen ini.</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" 
                                        @click="
                                            let availableIds = {{ json_encode($allQuestions->map(fn($q) => ['id' => (string)$q->id, 'category_id' => (string)$q->category_id])) }}.filter(q => !createCategoryId || q.category_id == createCategoryId).map(q => q.id);
                                            let allSelected = availableIds.every(id => createSelectedQuestions.includes(id));
                                            if (allSelected) {
                                                createSelectedQuestions = createSelectedQuestions.filter(id => !availableIds.includes(id));
                                            } else {
                                                createSelectedQuestions = Array.from(new Set([...createSelectedQuestions, ...availableIds]));
                                            }
                                        " 
                                        class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Pilih Semua
                                    </button>
                                    <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400" x-text="createSelectedQuestions.length + ' Soal Dipilih'"></span>
                                </div>
                            </div>

                            <div class="max-h-60 overflow-y-auto border border-gray-200 dark:border-slate-700 rounded-xl divide-y divide-gray-100 dark:divide-slate-800">
                                @forelse ($allQuestions as $q)
                                    <label x-show="!createCategoryId || createCategoryId == '{{ $q->category_id }}'" class="flex items-start gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-800/60 cursor-pointer transition">
                                        <input type="checkbox" name="selected_questions[]" value="{{ $q->id }}" x-model="createSelectedQuestions" class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500">
                                        <div class="flex-1 text-xs">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300">
                                                    {{ $q->category->name ?? '-' }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                                                    {{ $q->question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Essay' }}
                                                </span>
                                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                                    {{ $q->points }} Poin
                                                </span>
                                            </div>
                                            <p class="font-medium text-gray-800 dark:text-slate-200 line-clamp-2">{{ $q->question }}</p>
                                        </div>
                                    </label>
                                @empty
                                    <div class="p-4 text-center text-xs text-gray-400">
                                        Belum ada soal pada Bank Soal. Silakan buat soal terlebih dahulu di menu Bank Soal.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition cursor-pointer">
                                Simpan Paket Asesmen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Test Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showEditModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Edit Paket Asesmen Karyawan
                        </h3>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'{{ url('admin/employee-tests') }}/' + editData.id" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_edit" value="1">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Target Departemen -->
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Target Departemen
                                </label>
                                
                                <div class="flex items-center gap-3 mb-2">
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="target_scope" value="all" x-model="editData.target_scope" @change="editData.department_ids = []" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Semua Departemen</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="target_scope" value="specific" x-model="editData.target_scope" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs font-medium text-gray-700 dark:text-slate-300">Pilih Spesifik</span>
                                    </label>
                                </div>

                                <div x-show="editData.target_scope === 'specific'" x-transition class="border border-gray-200 dark:border-slate-700 rounded-xl p-2.5 bg-gray-50 dark:bg-slate-800/60 space-y-2">
                                    <div class="flex items-center justify-between pb-1.5 border-b border-gray-200 dark:border-slate-700">
                                        <span class="text-[11px] font-semibold text-gray-600 dark:text-slate-400" x-text="editData.department_ids.length + ' dipilih'"></span>
                                        <button type="button" 
                                            @click="
                                                let allDeptIds = {{ json_encode($departments->pluck('id')->map(fn($id) => (string)$id)) }};
                                                if (editData.department_ids.length === allDeptIds.length) {
                                                    editData.department_ids = [];
                                                } else {
                                                    editData.department_ids = allDeptIds;
                                                }
                                            " 
                                            class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                            <span x-text="editData.department_ids.length === {{ count($departments) }} ? 'Hapus Semua' : 'Pilih Semua'"></span>
                                        </button>
                                    </div>
                                    <div class="max-h-36 overflow-y-auto space-y-1 pr-1">
                                        @foreach ($departments as $dept)
                                            <label class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white dark:hover:bg-slate-800 cursor-pointer text-xs text-gray-700 dark:text-slate-300 transition">
                                                <input type="checkbox" name="department_ids[]" value="{{ $dept->id }}" x-model="editData.department_ids" class="rounded text-indigo-600 focus:ring-indigo-500">
                                                <span class="truncate">{{ $dept->name }} <span class="text-[10px] text-gray-400">{{ $dept->company ? '('.$dept->company->name.')' : '' }}</span></span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <template x-if="editData.target_scope === 'all'">
                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 block mt-1 font-medium">
                                        ✓ Tes ini akan tersedia untuk seluruh departemen perusahaan.
                                    </span>
                                </template>
                                @error('department_ids')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sasaran / Target Peserta -->
                            <div class="sm:col-span-1">
                                <label for="edit_target_employee_type" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Sasaran / Target Peserta <span class="text-rose-500">*</span>
                                </label>
                                <select name="target_employee_type" id="edit_target_employee_type" x-model="editData.target_employee_type" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    <option value="all">Semua (Karyawan & Magang)</option>
                                    <option value="internship">Magang (Internship)</option>
                                    <option value="permanent">Karyawan Tetap</option>
                                    <option value="contract">Karyawan Kontrak</option>
                                </select>
                                <span class="text-[10px] text-gray-400">Tentukan siapa saja yang dapat melihat dan mengerjakan tes ini</span>
                                @error('target_employee_type')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Kategori Soal -->
                            <div class="sm:col-span-2">
                                <label for="edit_category_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Kategori Soal <span class="text-rose-500">*</span>
                                </label>
                                <select name="category_id" id="edit_category_id" x-model="editData.category_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    <option value="">-- Pilih Kategori Soal --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Judul Asesmen -->
                        <div>
                            <label for="edit_title" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Judul Paket Asesmen <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="title" id="edit_title" x-model="editData.title" required placeholder="Contoh: Evaluasi Kepribadian DISC Karyawan 2026" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('title')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Parameter Ujian -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div>
                                <label for="edit_duration" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Durasi (Menit) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="duration_minutes" id="edit_duration" min="1" x-model="editData.duration_minutes" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('duration_minutes')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="edit_passing_score" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Nilai KKM (%) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="passing_score" id="edit_passing_score" min="0" max="100" 
                                    x-model="editData.passing_score" 
                                    :readonly="isDiscCategory(editData.category_id)"
                                    :class="isDiscCategory(editData.category_id) ? 'opacity-60 bg-gray-200 cursor-not-allowed' : ''"
                                    required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                <template x-if="isDiscCategory(editData.category_id)">
                                    <p class="mt-1 text-[10px] text-purple-500 font-semibold">Tanpa KKM (Tes Profil)</p>
                                </template>
                                @error('passing_score')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="edit_total_questions" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Jumlah Soal <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="total_questions" id="edit_total_questions" min="1" 
                                    x-model="editData.total_questions" 
                                    :readonly="isDiscCategory(editData.category_id)"
                                    :class="isDiscCategory(editData.category_id) ? 'opacity-60 bg-gray-200 cursor-not-allowed' : ''"
                                    required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                <template x-if="isDiscCategory(editData.category_id)">
                                    <p class="mt-1 text-[10px] text-purple-500 font-semibold">Standar 24 Soal DISC</p>
                                </template>
                                @error('total_questions')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Opsi Acak Soal
                                </label>
                                <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                    <input type="checkbox" name="is_random" value="1" x-model="editData.is_random" class="rounded text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-xs text-gray-700 dark:text-slate-300 font-medium">Acak Urutan Soal</span>
                                </label>
                            </div>
                        </div>

                        <!-- Question Picker Section -->
                        <div class="space-y-3 pt-3 border-t border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-gray-800 dark:text-slate-200">
                                        Kelola Pertanyaan Terikat
                                    </label>
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400">Centang untuk menambahkan atau melepaskan soal dari paket asesmen ini.</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" 
                                        @click="
                                            let availableIds = {{ json_encode($allQuestions->map(fn($q) => ['id' => (string)$q->id, 'category_id' => (string)$q->category_id])) }}.filter(q => !editData.category_id || q.category_id == editData.category_id).map(q => q.id);
                                            let allSelected = availableIds.every(id => editData.selected_questions.includes(id));
                                            if (allSelected) {
                                                editData.selected_questions = editData.selected_questions.filter(id => !availableIds.includes(id));
                                            } else {
                                                editData.selected_questions = Array.from(new Set([...editData.selected_questions, ...availableIds]));
                                            }
                                        " 
                                        class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Pilih Semua
                                    </button>
                                    <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400" x-text="editData.selected_questions.length + ' Soal Terikat'"></span>
                                </div>
                            </div>

                            <div class="max-h-60 overflow-y-auto border border-gray-200 dark:border-slate-700 rounded-xl divide-y divide-gray-100 dark:divide-slate-800">
                                @forelse ($allQuestions as $q)
                                    <label x-show="!editData.category_id || editData.category_id == '{{ $q->category_id }}'" class="flex items-start gap-3 p-3 hover:bg-gray-50 dark:hover:bg-slate-800/60 cursor-pointer transition">
                                        <input type="checkbox" name="selected_questions[]" value="{{ $q->id }}" x-model="editData.selected_questions" class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500">
                                        <div class="flex-1 text-xs">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-300">
                                                    {{ $q->category->name ?? '-' }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                                                    {{ $q->question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Essay' }}
                                                </span>
                                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                                    {{ $q->points }} Poin
                                                </span>
                                            </div>
                                            <p class="font-medium text-gray-800 dark:text-slate-200 line-clamp-2">{{ $q->question }}</p>
                                        </div>
                                    </label>
                                @empty
                                    <div class="p-4 text-center text-xs text-gray-400">
                                        Belum ada soal pada Bank Soal.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition cursor-pointer">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Test Modal -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDetailModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800" x-text="detailData.category_name"></span>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mt-1" x-text="detailData.title"></h3>
                        </div>
                        <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Parameter Badges -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="col-span-2 sm:col-span-4 p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
                            <span class="block text-[10px] text-gray-400 font-semibold uppercase mb-1">Target Departemen</span>
                            <template x-if="detailData.departments && detailData.departments.length > 0">
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="dept in detailData.departments" :key="dept.id">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800" x-text="dept.name + (dept.company ? ' (' + dept.company.name + ')' : '')"></span>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!detailData.departments || detailData.departments.length === 0">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    Semua Departemen (Umum)
                                </span>
                            </template>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
                            <span class="block text-[10px] text-gray-400 font-semibold uppercase">Durasi Ujian</span>
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400" x-text="detailData.duration_minutes + ' Menit'"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
                            <span class="block text-[10px] text-gray-400 font-semibold uppercase">Nilai KKM</span>
                            <span class="text-xs font-bold" :class="isDiscCategory(detailData.category_id) ? 'text-purple-600 dark:text-purple-400' : 'text-emerald-600 dark:text-emerald-400'" x-text="isDiscCategory(detailData.category_id) ? 'Tanpa KKM' : detailData.passing_score + '%'"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
                            <span class="block text-[10px] text-gray-400 font-semibold uppercase">Opsi Acak</span>
                            <span class="text-xs font-bold" :class="detailData.is_random ? 'text-emerald-600' : 'text-gray-500'" x-text="detailData.is_random ? 'Acak Soal' : 'Urutan Tetap'"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
                            <span class="block text-[10px] text-gray-400 font-semibold uppercase">Target Peserta</span>
                            <span class="text-xs font-bold text-gray-800 dark:text-slate-200 capitalize" x-text="detailData.target_employee_type === 'all' ? 'Semua Karyawan & Magang' : detailData.target_employee_type"></span>
                        </div>
                    </div>

                    <!-- Soal Terikat List -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Daftar Soal Terikat:</h4>
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400" x-text="detailData.questions.length + ' Soal'"></span>
                        </div>

                        <div class="max-h-60 overflow-y-auto space-y-2">
                            <template x-for="(q, idx) in detailData.questions" :key="q.id">
                                <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold text-xs flex items-center justify-center shrink-0" x-text="idx + 1"></span>
                                    <div class="flex-1 text-xs">
                                        <p class="font-semibold text-gray-800 dark:text-slate-200" x-text="q.question"></p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300" x-text="q.question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Essay'"></span>
                                            <span class="text-[10px] font-bold text-emerald-600" x-text="q.points + ' Poin'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="detailData.questions.length === 0">
                                <div class="p-4 text-center text-xs text-gray-400 border border-dashed border-gray-200 dark:border-slate-800 rounded-xl">
                                    Belum ada soal spesifik yang terikat. Sistem akan mengambil soal secara acak berdasarkan Kategori saat ujian dilaksanakan.
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex justify-end">
                        <button type="button" @click="showDetailModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Test Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDeleteModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                Hapus Paket Asesmen
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                Apakah Anda yakin ingin menghapus paket asesmen "<span class="font-bold text-gray-800 dark:text-gray-200" x-text="deleteData.title"></span>"? Tindakan ini tidak dapat dibatalkan.
                            </p>

                            <!-- Warning jika ada riwayat pengerjaan karyawan -->
                            <template x-if="deleteData.attempts_count > 0">
                                <div class="mt-4 p-3.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 rounded-xl space-y-3">
                                    <div class="flex items-start gap-2.5">
                                        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <div class="text-xs text-rose-800 dark:text-rose-300">
                                            <p class="font-bold">Perhatian: Paket Memiliki Riwayat Pengerjaan Karyawan</p>
                                            <p class="mt-1 text-[11px] leading-relaxed text-rose-700 dark:text-rose-400">
                                                Terdapat <span class="font-bold underline" x-text="deleteData.attempts_count"></span> percobaan asesmen pada paket ini. Menghapus paket ini akan <span class="font-bold underline">menghapus permanen seluruh riwayat evaluasi, nilai, dan jawaban karyawan</span> yang bersangkutan.
                                            </p>
                                        </div>
                                    </div>

                                    <label class="flex items-start gap-2.5 p-2 bg-white/60 dark:bg-slate-900/60 border border-rose-200/80 dark:border-rose-800/50 rounded-lg cursor-pointer select-none">
                                        <input type="checkbox" x-model="forceDeleteConfirmed" class="mt-0.5 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                        <span class="text-[11px] font-semibold text-rose-900 dark:text-rose-200 leading-tight">
                                            Saya memahami risikonya dan setuju untuk menghapus paksa paket ini beserta seluruh riwayat asesmen karyawan.
                                        </span>
                                    </label>
                                </div>
                            </template>
                        </div>
                    </div>

                    <form :action="'{{ url('admin/employee-tests') }}/' + deleteData.id" method="POST" class="mt-6 flex items-center justify-end gap-3">
                        @csrf
                        @method('DELETE')

                        <template x-if="deleteData.attempts_count > 0 && forceDeleteConfirmed">
                            <input type="hidden" name="force_delete" value="1">
                        </template>

                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" 
                                :disabled="deleteData.attempts_count > 0 && !forceDeleteConfirmed"
                                :class="(deleteData.attempts_count > 0 && !forceDeleteConfirmed) ? 'opacity-50 cursor-not-allowed bg-gray-400 dark:bg-gray-600' : 'bg-rose-600 hover:bg-rose-500 shadow-md shadow-rose-500/20'"
                                class="px-4 py-2 text-xs font-semibold text-white rounded-xl transition">
                            <span x-text="deleteData.attempts_count > 0 ? 'Hapus Paksa Paket' : 'Hapus Data'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
