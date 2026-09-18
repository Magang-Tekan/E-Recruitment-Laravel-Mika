<div class="space-y-6" x-data="{ 
    showCreateModal: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }},
    showEditModal: {{ $errors->any() && old('is_edit') ? 'true' : 'false' }},
    showDeleteModal: false,
    createStep: {{ $errors->has('description') ? '2' : '1' }},
    editStep: {{ $errors->has('description') ? '2' : '1' }},
    createCompanyId: '{{ old('company_id', '') }}',
    createDepartmentId: '{{ old('department_id', '') }}',
    createPositionId: '{{ old('position_id', '') }}',
    goToCreateStep2() {
        let company = document.getElementById('company_id');
        let dept = document.getElementById('department_id');
        let title = document.getElementById('title');
        let quota = document.getElementById('quota');
        let deadline = document.getElementById('deadline');

        if (company && !company.value) {
            company.reportValidity();
            return;
        }
        if (dept && !dept.value) {
            dept.reportValidity();
            return;
        }
        if (title && !title.value.trim()) {
            title.reportValidity();
            return;
        }
        if (quota && !quota.value) {
            quota.reportValidity();
            return;
        }
        if (deadline && !deadline.value) {
            deadline.reportValidity();
            return;
        }

        this.createStep = 2;
        this.$nextTick(() => {
            this.initQuillCreate();
        });
    },
    goToEditStep2() {
        let company = document.getElementById('edit_company_id');
        let dept = document.getElementById('edit_department_id');
        let title = document.getElementById('edit_title');
        let quota = document.getElementById('edit_quota');
        let deadline = document.getElementById('edit_deadline');

        if (company && !company.value) {
            company.reportValidity();
            return;
        }
        if (dept && !dept.value) {
            dept.reportValidity();
            return;
        }
        if (title && !title.value.trim()) {
            title.reportValidity();
            return;
        }
        if (quota && !quota.value) {
            quota.reportValidity();
            return;
        }
        if (deadline && !deadline.value) {
            deadline.reportValidity();
            return;
        }

        this.editStep = 2;
        this.$nextTick(() => {
            this.initQuillEdit(this.editData.description || '');
        });
    },
    onCompanyChange(isEdit = false) {
        if (!isEdit) {
            this.createDepartmentId = '';
            this.createPositionId = '';
        } else {
            this.editData.department_id = '';
            this.editData.position_id = '';
        }
    },
    onDepartmentChange(isEdit = false) {
        if (!isEdit) {
            this.createPositionId = '';
        } else {
            this.editData.position_id = '';
        }
    },
    createQuill: null,
    editQuill: null,
    editData: {
        id: '{{ old('id', '') }}',
        company_id: '{{ old('company_id', '') }}',
        department_id: '{{ old('department_id', '') }}',
        position_id: '{{ old('position_id', '') }}',
        title: '{{ old('title', '') }}',
        description: '{{ old('description', '') }}',
        employment_type: '{{ old('employment_type', 'Full-time') }}',
        location: '{{ old('location', '') }}',
        salary_min: '{{ old('salary_min', '') }}',
        salary_max: '{{ old('salary_max', '') }}',
        quota: '{{ old('quota', '1') }}',
        deadline: '{{ old('deadline', '') }}',
        status: '{{ old('status', 'Open') }}'
    },
    deleteData: {
        id: '',
        title: ''
    },
    onPositionSelect(event, isEdit = false) {
        let select = event.target;
        let selectedOption = select.options[select.selectedIndex];
        let selectedText = selectedOption?.dataset?.name || '';
        if (selectedText) {
            if (!isEdit) {
                let titleInput = document.getElementById('title');
                if (titleInput) {
                    titleInput.value = selectedText;
                }
            } else {
                this.editData.title = selectedText;
            }
        }
    },
    initQuillCreate() {
        if (this.createQuill) return;
        this.$nextTick(() => {
            let container = document.getElementById('create_quill_editor');
            if (!container) return;
            this.createQuill = new Quill(container, {
                theme: 'snow',
                placeholder: 'Tuliskan deskripsi pekerjaan, tanggung jawab, dan rincian persyaratan kualifikasi di sini...',
                modules: {
                    toolbar: [
                        [{ 'header': [2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });

            this.createQuill.on('text-change', () => {
                let html = this.createQuill.root.innerHTML;
                let isEmpty = this.createQuill.getText().trim().length === 0;
                let input = document.getElementById('create_final_description');
                if (input) input.value = isEmpty ? '' : html;
            });
        });
    },
    initQuillEdit(content) {
        this.$nextTick(() => {
            let container = document.getElementById('edit_quill_editor');
            if (!container) return;
            if (!this.editQuill) {
                this.editQuill = new Quill(container, {
                    theme: 'snow',
                    placeholder: 'Tuliskan deskripsi pekerjaan dan persyaratan di sini...',
                    modules: {
                        toolbar: [
                            [{ 'header': [2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['clean']
                        ]
                    }
                });

                this.editQuill.on('text-change', () => {
                    let html = this.editQuill.root.innerHTML;
                    let isEmpty = this.editQuill.getText().trim().length === 0;
                    this.editData.description = isEmpty ? '' : html;
                    let input = document.getElementById('edit_final_description');
                    if (input) input.value = isEmpty ? '' : html;
                });
            }
            
            // Set existing content (support both HTML & legacy markdown/plain text)
            if (content) {
                if (content.includes('<p>') || content.includes('<ul>') || content.includes('<ol>') || content.includes('<h3>')) {
                    this.editQuill.root.innerHTML = content;
                } else {
                    let formatted = content
                        .replace(/### Persyaratan:\s*/g, '<h3>Persyaratan</h3>')
                        .replace(/### Deskripsi Pekerjaan:\s*/g, '<h3>Deskripsi Pekerjaan</h3>')
                        .replace(/\n/g, '<br>');
                    this.editQuill.root.innerHTML = formatted;
                }
            } else {
                this.editQuill.root.innerHTML = '';
            }
        });
    },
    openCreateModal() {
        this.createStep = 1;
        this.createCompanyId = '';
        this.createDepartmentId = '';
        this.createPositionId = '';
        let titleInput = document.getElementById('title');
        if (titleInput) {
            titleInput.value = '';
        }
        this.showCreateModal = true;
        this.$nextTick(() => {
            if (this.createQuill) {
                this.createQuill.root.innerHTML = '';
            }
            let input = document.getElementById('create_final_description');
            if (input) input.value = '';
        });
    },
    openEditModal(job) {
        this.editStep = 1;
        this.editData = {
            id: job.id,
            company_id: job.company_id ? String(job.company_id) : '',
            department_id: job.department_id ? String(job.department_id) : '',
            position_id: job.position_id ? String(job.position_id) : '',
            title: job.title || '',
            description: job.description || '',
            employment_type: job.employment_type || 'Full-time',
            location: job.location || '',
            salary_min: job.salary_min || '',
            salary_max: job.salary_max || '',
            quota: job.quota !== undefined && job.quota !== null && job.quota !== '' ? Number(job.quota) : 1,
            deadline: job.deadline ? job.deadline.split('T')[0] : '',
            status: job.status || 'Open'
        };
        this.showEditModal = true;
    },
    openDeleteModal(job) {
        this.deleteData = {
            id: job.id,
            title: job.title
        };
        this.showDeleteModal = true;
    }
}">

    <!-- Quill.js Stylesheet & Script (Loaded securely via CDN) -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <style>
        /* Quill Snow Editor Base (Light Mode) */
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-color: #e2e8f0;
            background-color: #f8fafc;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            border-color: #e2e8f0;
            font-family: inherit;
            font-size: 0.85rem;
            min-height: 180px;
            background-color: #ffffff;
            color: #1e293b;
        }
        .ql-editor {
            min-height: 180px;
            max-height: 280px;
            overflow-y: auto;
            line-height: 1.6;
            color: #1e293b;
        }
        .ql-editor.ql-blank::before {
            color: #94a3b8;
            font-style: italic;
        }
        .ql-editor ul {
            list-style-type: disc;
            padding-left: 1.25rem;
        }
        .ql-editor ol {
            list-style-type: decimal;
            padding-left: 1.25rem;
        }
        .ql-editor h2, .ql-editor h3 {
            font-weight: bold;
            margin-top: 0.75rem;
            margin-bottom: 0.25rem;
        }

        /* Dark Mode Support (via media query, .dark class, and parent dark classes) */
        @media (prefers-color-scheme: dark) {
            .ql-toolbar.ql-snow {
                border-color: #334155 !important;
                background-color: #1e293b !important;
            }
            .ql-snow .ql-stroke {
                stroke: #cbd5e1 !important;
            }
            .ql-snow .ql-fill {
                fill: #cbd5e1 !important;
            }
            .ql-snow .ql-picker {
                color: #cbd5e1 !important;
            }
            .ql-snow .ql-picker-options {
                background-color: #1e293b !important;
                border-color: #334155 !important;
            }
            .ql-snow .ql-picker-item {
                color: #cbd5e1 !important;
            }
            .ql-snow .ql-picker-label:hover,
            .ql-snow .ql-picker-label.ql-active,
            .ql-snow .ql-picker-item:hover,
            .ql-snow .ql-picker-item.ql-selected {
                color: #93F514 !important;
            }
            .ql-snow button:hover .ql-stroke,
            .ql-snow button.ql-active .ql-stroke {
                stroke: #93F514 !important;
            }
            .ql-snow button:hover .ql-fill,
            .ql-snow button.ql-active .ql-fill {
                fill: #93F514 !important;
            }
            .ql-container.ql-snow {
                border-color: #334155 !important;
                background-color: #0f172a !important;
                color: #f1f5f9 !important;
            }
            .ql-editor {
                background-color: #0f172a !important;
                color: #f1f5f9 !important;
            }
            .ql-editor p,
            .ql-editor span,
            .ql-editor li,
            .ql-editor strong,
            .ql-editor em,
            .ql-editor h1,
            .ql-editor h2,
            .ql-editor h3 {
                color: #f1f5f9 !important;
            }
            .ql-editor.ql-blank::before {
                color: #64748b !important;
            }
        }

        .dark .ql-toolbar.ql-snow,
        .dark .quill-dark-wrapper .ql-toolbar.ql-snow {
            border-color: #334155 !important;
            background-color: #1e293b !important;
        }
        .dark .ql-snow .ql-stroke,
        .dark .quill-dark-wrapper .ql-snow .ql-stroke {
            stroke: #cbd5e1 !important;
        }
        .dark .ql-snow .ql-fill,
        .dark .quill-dark-wrapper .ql-snow .ql-fill {
            fill: #cbd5e1 !important;
        }
        .dark .ql-snow .ql-picker,
        .dark .quill-dark-wrapper .ql-snow .ql-picker {
            color: #cbd5e1 !important;
        }
        .dark .ql-snow .ql-picker-options,
        .dark .quill-dark-wrapper .ql-snow .ql-picker-options {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }
        .dark .ql-snow .ql-picker-item,
        .dark .quill-dark-wrapper .ql-snow .ql-picker-item {
            color: #cbd5e1 !important;
        }
        .dark .ql-snow .ql-picker-label:hover,
        .dark .ql-snow .ql-picker-label.ql-active,
        .dark .ql-snow .ql-picker-item:hover,
        .dark .ql-snow .ql-picker-item.ql-selected {
            color: #93F514 !important;
        }
        .dark .ql-snow button:hover .ql-stroke,
        .dark .ql-snow button.ql-active .ql-stroke {
            stroke: #93F514 !important;
        }
        .dark .ql-snow button:hover .ql-fill,
        .dark .ql-snow button.ql-active .ql-fill {
            fill: #93F514 !important;
        }
        .dark .ql-container.ql-snow,
        .dark .quill-dark-wrapper .ql-container.ql-snow {
            border-color: #334155 !important;
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
        }
        .dark .ql-editor,
        .dark .quill-dark-wrapper .ql-editor {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
        }
        .dark .ql-editor p,
        .dark .ql-editor span,
        .dark .ql-editor li,
        .dark .ql-editor strong,
        .dark .ql-editor em,
        .dark .ql-editor h1,
        .dark .ql-editor h2,
        .dark .ql-editor h3 {
            color: #f1f5f9 !important;
        }
        .dark .ql-editor.ql-blank::before,
        .dark .quill-dark-wrapper .ql-editor.ql-blank::before {
            color: #64748b !important;
        }
    </style>
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
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold">{{ session('delete') }}</span>
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
        
        <!-- Header & Action Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Daftar Lowongan Kerja</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Kelola dan publikasikan lowongan pekerjaan baru.</p>
            </div>

            <button type="button" @click="openCreateModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition active:scale-95 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Lowongan</span>
            </button>
        </div>

        <!-- Filter & Search Toolbar Row -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="relative lg:col-span-4">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari posisi, lokasi, perusahaan..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Perusahaan -->
                <div class="relative lg:col-span-4">
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
                <div class="relative lg:col-span-4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <select wire:model.live="departmentId" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">{{ $companyId ? 'Semua Departemen di Perusahaan Ini' : 'Semua Departemen' }}</option>
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
            </div>

            <!-- Toolbar Bottom Row: Quick Status Filter Badges & Reset Button -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase mr-1">Status:</span>
                    <button type="button" wire:click="$set('statusFilter', '')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $statusFilter === '' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Semua
                    </button>
                    <button type="button" wire:click="$set('statusFilter', 'Open')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $statusFilter === 'Open' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }}">
                        Open
                    </button>
                    <button type="button" wire:click="$set('statusFilter', 'Closed')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $statusFilter === 'Closed' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800 hover:bg-rose-50 dark:hover:bg-rose-950/40' }}">
                        Closed
                    </button>
                </div>

                @if ($search || $companyId || $departmentId || $statusFilter)
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
        <div wire:loading wire:target="search, companyId, departmentId, statusFilter, previousPage, nextPage, gotoPage, resetFilters" class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center z-10 transition">
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
                        <th class="px-6 py-4">Lowongan Pekerjaan</th>
                        <th class="px-6 py-4">Tipe & Lokasi</th>
                        <th class="px-6 py-4">Estimasi Gaji & Kuota</th>
                        <th class="px-6 py-4">Tenggat & Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-gray-700 dark:text-slate-300">
                    @forelse ($jobs as $job)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-white text-sm block">{{ $job->title }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if ($job->company)
                                            <span class="text-[11px] font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-md border border-indigo-100 dark:border-indigo-900/50">
                                                {{ $job->company->name }}
                                            </span>
                                        @endif
                                        @if ($job->department)
                                            <span class="text-[11px] text-gray-500 dark:text-slate-400">
                                                - {{ $job->department->name }}
                                            </span>
                                        @endif
                                        @if ($job->position)
                                            <span class="text-[10px] font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md border border-emerald-100 dark:border-emerald-900/50">
                                                {{ $job->position->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $job->employment_type }}
                                    </span>
                                    <div class="text-[11px] text-gray-500 dark:text-slate-400 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        {{ $job->location ?? 'Remote' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-0.5">
                                    <div class="font-semibold text-gray-900 dark:text-slate-200">
                                        @if ($job->salary_min || $job->salary_max)
                                            Rp{{ number_format($job->salary_min ?? 0, 0, ',', '.') }} - Rp{{ number_format($job->salary_max ?? 0, 0, ',', '.') }}
                                        @else
                                            <span class="text-gray-400 italic font-normal">Kompetitif</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        Kuota: <span class="font-medium text-gray-700 dark:text-slate-300">{{ $job->quota }} orang</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if ($job->status === 'Open')
                                        @if ($job->is_expired)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800" title="Lowongan Open tetapi tanggal deadline sudah terlewat">
                                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> --}}
                                                Open (Expired)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> --}}
                                                Open
                                            </span>
                                        @endif
                                    @elseif ($job->status === 'Closed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                            Closed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            Draft
                                        </span>
                                    @endif
                                    <div class="text-[11px] {{ $job->is_expired ? 'text-rose-500 font-medium' : 'text-gray-400' }}">
                                        Deadline: {{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('d M Y') : 'Hingga Terpenuhi' }}
                                        @if ($job->is_expired)
                                            <span class="block text-[10px] text-rose-400 font-semibold">(Melewati batas)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal({{ json_encode($job) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="openDeleteModal({{ json_encode($job) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-medium">Belum ada data lowongan pekerjaan</span>
                                    <span class="text-xs">Klik tombol "Tambah Lowongan" untuk menambahkan data baru.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($jobs->hasPages() || $perPage != 10)
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
                @if ($jobs->hasPages())
                    <div>
                        {{ $jobs->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Create Job Modal -->
    <div wire:ignore.self x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCreateModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title">
                            Tambah Lowongan Pekerjaan Baru
                        </h3>
                        <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Step Indicator Tabs -->
                    <div class="flex items-center gap-2 border-b border-gray-100 dark:border-slate-800 py-3 mb-4">
                        <button type="button" 
                                @click="createStep = 1"
                                class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-semibold transition-all border"
                                :class="createStep === 1 
                                    ? 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 shadow-sm' 
                                    : 'bg-gray-50 dark:bg-slate-800/60 border-transparent text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800'">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-colors"
                                  :class="createStep === 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-slate-300'">
                                1
                            </span>
                            <span class="truncate">1. Informasi Lowongan</span>
                        </button>

                        <svg class="w-4 h-4 text-gray-300 dark:text-slate-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>

                        <button type="button" 
                                @click="goToCreateStep2()"
                                class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-semibold transition-all border"
                                :class="createStep === 2 
                                    ? 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 shadow-sm' 
                                    : 'bg-gray-50 dark:bg-slate-800/60 border-transparent text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800'">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-colors"
                                  :class="createStep === 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-slate-300'">
                                2
                            </span>
                            <span class="truncate">2. Deskripsi & Persyaratan</span>
                        </button>
                    </div>

                    <form action="{{ route('admin.job.store') }}" method="POST" id="createJobForm" @keydown.enter="if (createStep === 1 && $event.target.tagName !== 'TEXTAREA') { $event.preventDefault(); goToCreateStep2(); }" @submit="if(createQuill) { let html = createQuill.root.innerHTML; let isEmpty = createQuill.getText().trim().length === 0; document.getElementById('create_final_description').value = isEmpty ? '' : html; }" class="space-y-4">
                        @csrf

                        <!-- PAGE 1: Informasi Lowongan -->
                        <div x-show="createStep === 1" class="space-y-3.5">
                            <!-- Company -->
                            <div>
                                <label for="company_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Perusahaan <span class="text-rose-500">*</span>
                                </label>
                                <select name="company_id" id="company_id" x-model="createCompanyId" @change="onCompanyChange(false)" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    <option value="">-- Pilih Perusahaan --</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Department & Master Position Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Department -->
                                <div>
                                    <label for="department_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Departemen <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="department_id" id="department_id" x-model="createDepartmentId" @change="onDepartmentChange(false)" required :disabled="!createCompanyId" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="" x-text="!createCompanyId ? '-- Pilih Perusahaan Terlebih Dahulu --' : '-- Pilih Departemen --'">-- Pilih Departemen --</option>
                                        @foreach ($departments as $dept)
                                            <option 
                                                value="{{ $dept->id }}" 
                                                data-company-id="{{ $dept->company_id }}"
                                                x-show="createCompanyId == '{{ $dept->company_id }}'"
                                                :hidden="createCompanyId != '{{ $dept->company_id }}'"
                                                :disabled="createCompanyId != '{{ $dept->company_id }}'"
                                                {{ old('department_id') == $dept->id ? 'selected' : '' }}
                                            >
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Position Master Selection -->
                                <div>
                                    <label for="position_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                        <span>Posisi / Jabatan Baku</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Pilih otomatis</span>
                                    </label>
                                    <select name="position_id" id="position_id" x-model="createPositionId" @change="onPositionSelect($event, false)" :disabled="!createDepartmentId" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="" x-text="!createDepartmentId ? '-- Pilih Departemen Terlebih Dahulu --' : '-- Bebas / Tanpa Posisi Baku --'">-- Bebas / Tanpa Posisi Baku --</option>
                                        @foreach ($positions as $pos)
                                            <option 
                                                value="{{ $pos->id }}" 
                                                data-name="{{ $pos->name }}"
                                                data-department-id="{{ $pos->department_id }}"
                                                x-show="createDepartmentId == '{{ $pos->department_id }}'" 
                                                :hidden="createDepartmentId != '{{ $pos->department_id }}'"
                                                :disabled="createDepartmentId != '{{ $pos->department_id }}'"
                                                {{ old('position_id') == $pos->id ? 'selected' : '' }}
                                            >
                                                {{ $pos->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Judul Posisi / Pekerjaan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Contoh: Senior Full Stack Developer" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Employment Type -->
                                <div>
                                    <label for="employment_type" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Tipe Pekerjaan <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="employment_type" id="employment_type" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                        <option value="Full-time">Full-time</option>
                                        <option value="Part-time">Part-time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Internship">Internship</option>
                                        <option value="Freelance">Freelance</option>
                                    </select>
                                </div>

                                <!-- Location -->
                                <div>
                                    <label for="location" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Lokasi Kerja
                                    </label>
                                    <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="Contoh: Jakarta / Remote" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Salary Min -->
                                <div>
                                    <label for="salary_min" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Gaji Minimal (Rp)
                                    </label>
                                    <input type="number" name="salary_min" id="salary_min" value="{{ old('salary_min') }}" placeholder="5000000" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>

                                <!-- Salary Max -->
                                <div>
                                    <label for="salary_max" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Gaji Maksimal (Rp)
                                    </label>
                                    <input type="number" name="salary_max" id="salary_max" value="{{ old('salary_max') }}" placeholder="8000000" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Quota -->
                                <div>
                                    <label for="quota" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Kuota (Orang) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number" name="quota" id="quota" min="1" value="{{ old('quota', 1) }}" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>

                                <!-- Deadline -->
                                <div>
                                    <label for="deadline" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Tenggat Waktu <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Status <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="status" id="status" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                        <option value="Open">Open</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Draft">Draft</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- PAGE 2: Deskripsi & Persyaratan -->
                        <div x-show="createStep === 2" class="space-y-4" style="display: none;">
                            <!-- Quill Rich Text Visual Editor for Description & Requirements -->
                            <input type="hidden" name="description" id="create_final_description">

                            <div class="space-y-1.5 pt-1">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-gray-800 dark:text-slate-200">
                                        Deskripsi & Persyaratan Pekerjaan
                                    </label>
                                    <span class="text-[11px] text-gray-400 dark:text-slate-400">
                                        Gunakan toolbar untuk format Heading, Bullet List (•), Numbering, atau Bold
                                    </span>
                                </div>
                                
                                <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 quill-dark-wrapper">
                                    <div id="create_quill_editor"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Actions for Step 1 -->
                        <div x-show="createStep === 1" class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="button" @click="goToCreateStep2()" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-500/20 transition flex items-center gap-1.5">
                                <span>Lanjut: Deskripsi & Syarat</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Actions for Step 2 -->
                        <div x-show="createStep === 2" class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-slate-800" style="display: none;">
                            <button type="button" @click="createStep = 1" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                <span>Kembali ke Informasi</span>
                            </button>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="showCreateModal = false" class="px-3 py-2 text-xs font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-500/20 transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Simpan Lowongan</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit Job Modal -->
    <div wire:ignore.self x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showEditModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title-edit">
                            Edit Lowongan Pekerjaan
                        </h3>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Step Indicator Tabs -->
                    <div class="flex items-center gap-2 border-b border-gray-100 dark:border-slate-800 py-3 mb-4">
                        <button type="button" 
                                @click="editStep = 1"
                                class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-semibold transition-all border"
                                :class="editStep === 1 
                                    ? 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 shadow-sm' 
                                    : 'bg-gray-50 dark:bg-slate-800/60 border-transparent text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800'">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-colors"
                                  :class="editStep === 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-slate-300'">
                                1
                            </span>
                            <span class="truncate">1. Informasi Lowongan</span>
                        </button>

                        <svg class="w-4 h-4 text-gray-300 dark:text-slate-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>

                        <button type="button" 
                                @click="goToEditStep2()"
                                class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-semibold transition-all border"
                                :class="editStep === 2 
                                    ? 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 shadow-sm' 
                                    : 'bg-gray-50 dark:bg-slate-800/60 border-transparent text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800'">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-colors"
                                  :class="editStep === 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-slate-300'">
                                2
                            </span>
                            <span class="truncate">2. Deskripsi & Persyaratan</span>
                        </button>
                    </div>

                    <form :action="'/admin/jobs/' + editData.id" method="POST" id="editJobForm" @keydown.enter="if (editStep === 1 && $event.target.tagName !== 'TEXTAREA') { $event.preventDefault(); goToEditStep2(); }" @submit="if(editQuill) { let html = editQuill.root.innerHTML; let isEmpty = editQuill.getText().trim().length === 0; editData.description = isEmpty ? '' : html; document.getElementById('edit_final_description').value = isEmpty ? '' : html; }" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_edit" value="1">
                        <input type="hidden" name="id" x-model="editData.id">

                        <!-- PAGE 1: Informasi Lowongan -->
                        <div x-show="editStep === 1" class="space-y-3.5">
                            <!-- Company -->
                            <div>
                                <label for="edit_company_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Perusahaan
                                </label>
                                <select name="company_id" id="edit_company_id" x-model="editData.company_id" @change="onCompanyChange(true)" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    <option value="">-- Pilih Perusahaan --</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Department & Master Position Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Department -->
                                <div>
                                    <label for="edit_department_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Departemen
                                    </label>
                                    <select name="department_id" id="edit_department_id" x-model="editData.department_id" @change="onDepartmentChange(true)" :disabled="!editData.company_id" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="" x-text="!editData.company_id ? '-- Pilih Perusahaan Terlebih Dahulu --' : '-- Pilih Departemen --'">-- Pilih Departemen --</option>
                                        @foreach ($departments as $dept)
                                            <option 
                                                value="{{ $dept->id }}" 
                                                data-company-id="{{ $dept->company_id }}"
                                                x-show="editData.company_id == '{{ $dept->company_id }}'"
                                                :hidden="editData.company_id != '{{ $dept->company_id }}'"
                                                :disabled="editData.company_id != '{{ $dept->company_id }}'"
                                            >
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Position Master Selection -->
                                <div>
                                    <label for="edit_position_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                        <span>Posisi / Jabatan Baku</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Pilih otomatis</span>
                                    </label>
                                    <select name="position_id" id="edit_position_id" x-model="editData.position_id" @change="onPositionSelect($event, true)" :disabled="!editData.department_id" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="" x-text="!editData.department_id ? '-- Pilih Departemen Terlebih Dahulu --' : '-- Bebas / Tanpa Posisi Baku --'">-- Bebas / Tanpa Posisi Baku --</option>
                                        @foreach ($positions as $pos)
                                            <option 
                                                value="{{ $pos->id }}" 
                                                data-name="{{ $pos->name }}"
                                                data-department-id="{{ $pos->department_id }}"
                                                x-show="editData.department_id == '{{ $pos->department_id }}'" 
                                                :hidden="editData.department_id != '{{ $pos->department_id }}'"
                                                :disabled="editData.department_id != '{{ $pos->department_id }}'"
                                            >
                                                {{ $pos->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Title -->
                            <div>
                                <label for="edit_title" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Judul Posisi / Pekerjaan
                                </label>
                                <input type="text" name="title" id="edit_title" x-model="editData.title" placeholder="Judul Posisi" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Employment Type -->
                                <div>
                                    <label for="edit_employment_type" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Tipe Pekerjaan
                                    </label>
                                    <select name="employment_type" id="edit_employment_type" x-model="editData.employment_type" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                        <option value="Full-time">Full-time</option>
                                        <option value="Part-time">Part-time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Internship">Internship</option>
                                        <option value="Freelance">Freelance</option>
                                    </select>
                                </div>

                                <!-- Location -->
                                <div>
                                    <label for="edit_location" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Lokasi Kerja
                                    </label>
                                    <input type="text" name="location" id="edit_location" x-model="editData.location" placeholder="Lokasi" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Salary Min -->
                                <div>
                                    <label for="edit_salary_min" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Gaji Minimal (Rp)
                                    </label>
                                    <input type="number" name="salary_min" id="edit_salary_min" x-model="editData.salary_min" placeholder="5000000" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>

                                <!-- Salary Max -->
                                <div>
                                    <label for="edit_salary_max" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Gaji Maksimal (Rp)
                                    </label>
                                    <input type="number" name="salary_max" id="edit_salary_max" x-model="editData.salary_max" placeholder="8000000" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Quota -->
                                <div>
                                    <label for="edit_quota" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Kuota (Orang) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number" name="quota" id="edit_quota" min="1" x-model.number="editData.quota" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>

                                <!-- Deadline -->
                                <div>
                                    <label for="edit_deadline" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Tenggat Waktu
                                    </label>
                                    <input type="date" name="deadline" id="edit_deadline" x-model="editData.deadline" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="edit_status" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Status
                                    </label>
                                    <select name="status" id="edit_status" x-model="editData.status" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                        <option value="Open">Open</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Draft">Draft</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- PAGE 2: Deskripsi & Persyaratan -->
                        <div x-show="editStep === 2" class="space-y-4" style="display: none;">
                            <!-- Quill Rich Text Visual Editor for Description & Requirements -->
                            <input type="hidden" name="description" id="edit_final_description" x-model="editData.description">

                            <div class="space-y-1.5 pt-1">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-gray-800 dark:text-slate-200">
                                        Deskripsi & Persyaratan Pekerjaan
                                    </label>
                                    <span class="text-[11px] text-gray-400 dark:text-slate-400">
                                        Gunakan toolbar untuk format Heading, Bullet List (•), Numbering, atau Bold
                                    </span>
                                </div>
                                
                                <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 quill-dark-wrapper">
                                    <div id="edit_quill_editor"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Actions for Step 1 -->
                        <div x-show="editStep === 1" class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="button" @click="goToEditStep2()" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-500/20 transition flex items-center gap-1.5">
                                <span>Lanjut: Deskripsi & Syarat</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Actions for Step 2 -->
                        <div x-show="editStep === 2" class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-slate-800" style="display: none;">
                            <button type="button" @click="editStep = 1" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                <span>Kembali ke Informasi</span>
                            </button>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="showEditModal = false" class="px-3 py-2 text-xs font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-500/20 transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Perbarui Lowongan</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div wire:ignore.self x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-delete" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDeleteModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center gap-4 pb-4 border-b border-gray-100 dark:border-slate-800">
                        <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center shrink-0 text-rose-600 dark:text-rose-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title-delete">
                                Konfirmasi Hapus Lowongan
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs text-gray-600 dark:text-slate-300">
                            Apakah Anda yakin ingin menghapus data lowongan <strong class="text-gray-900 dark:text-white font-semibold" x-text="deleteData.title"></strong>?
                        </p>
                    </div>

                    <form :action="'/admin/jobs/' + deleteData.id" method="POST" class="mt-6 flex items-center justify-end gap-3">
                        @csrf
                        @method('DELETE')

                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-md shadow-rose-500/20 transition">
                            Hapus Lowongan
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
