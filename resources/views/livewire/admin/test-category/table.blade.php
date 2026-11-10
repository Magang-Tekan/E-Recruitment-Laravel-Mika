<div class="space-y-6" x-data="{ 
    showCreateModal: {{ $errors->any() && !old('is_edit') && !old('is_sub_category') ? 'true' : 'false' }},
    showEditModal: {{ $errors->any() && old('is_edit') && !old('is_sub_category') ? 'true' : 'false' }},
    showDeleteModal: false,
    showSubCategoryModal: {{ $errors->any() && old('is_sub_category') ? 'true' : 'false' }},
    showDeleteSubCategoryModal: false,

    isSubmittingCreate: false,
    isSubmittingEdit: false,
    isSubmittingDelete: false,
    isSubmittingSubCategoryCreate: false,
    isSubmittingSubCategoryEdit: false,
    isSubmittingSubCategoryDelete: false,

    editData: {
        id: '{{ old('id', '') }}',
        name: '{{ old('name', '') }}',
        description: '{{ old('description', '') }}'
    },
    deleteData: {
        id: '',
        name: ''
    },
    activeCategory: {
        id: '{{ old('category_id', '') }}',
        name: '{{ old('category_id') ? (optional($categories->firstWhere('id', old('category_id')))->name ?? '') : '' }}',
        sub_categories: @js(old('category_id') && $categories->firstWhere('id', old('category_id')) ? $categories->firstWhere('id', old('category_id'))->subCategories->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'description' => $s->description,
            'questions_count' => $s->questions_count ?? 0,
        ])->values()->all() : [])
    },
    editingSubId: null,
    editingSubName: '',
    editingSubDescription: '',
    deleteSubData: {
        id: '',
        name: ''
    },

    openEditModal(category) {
        this.editData = {
            id: category.id,
            name: category.name || '',
            description: category.description || ''
        };
        this.showEditModal = true;
    },
    openDeleteModal(category) {
        this.deleteData = {
            id: category.id,
            name: category.name
        };
        this.showDeleteModal = true;
    },
    openSubCategoryModal(category) {
        this.activeCategory = {
            id: category.id,
            name: category.name,
            sub_categories: Array.isArray(category.sub_categories) ? JSON.parse(JSON.stringify(category.sub_categories)) : []
        };
        this.editingSubId = null;
        this.showSubCategoryModal = true;
    },
    startEditSub(sub) {
        this.editingSubId = sub.id;
        this.editingSubName = sub.name;
        this.editingSubDescription = sub.description || '';
    },
    cancelEditSub() {
        this.editingSubId = null;
        this.editingSubName = '';
        this.editingSubDescription = '';
    },
    openDeleteSubModal(sub) {
        this.deleteSubData = {
            id: sub.id,
            name: sub.name
        };
        this.showDeleteSubCategoryModal = true;
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
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Daftar Kategori Soal</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                    Kelola kategori materi tes dan sub-kategori (opsional) untuk pengelompokan bank soal.
                </p>
            </div>

            <button type="button" @click="showCreateModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold transition active:scale-95 shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Kategori</span>
            </button>
        </div>

        <!-- Filter & Search Toolbar Row -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="relative lg:col-span-5">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari kategori, topik, atau deskripsi..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Status Soal -->
                <div class="relative lg:col-span-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <select wire:model.live="statusFilter" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">Semua Soal</option>
                        <option value="has_questions">Ada Soal (> 0)</option>
                        <option value="empty">Kosong (0 Soal)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Topik / Sub-Kategori -->
                <div class="relative lg:col-span-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <select wire:model.live="subCategoryFilter" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">Semua Topik</option>
                        <option value="with_sub">Memiliki Topik</option>
                        <option value="without_sub">Tanpa Topik (Umum)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Urutkan / Sort By -->
                <div class="relative lg:col-span-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                    </div>
                    <select wire:model.live="sortBy" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="latest">Terbaru</option>
                        <option value="name_asc">Nama (A - Z)</option>
                        <option value="name_desc">Nama (Z - A)</option>
                        <option value="questions_desc">Total Soal Terbanyak</option>
                        <option value="sub_categories_desc">Total Topik Terbanyak</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Toolbar Bottom Row: Quick Status Filter Pills & Reset Button -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase mr-1">Status:</span>
                    <button type="button" wire:click="$set('statusFilter', '')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $statusFilter === '' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Semua
                    </button>
                    <button type="button" wire:click="$set('statusFilter', 'has_questions')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $statusFilter === 'has_questions' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }}">
                        Ada Soal
                    </button>
                    <button type="button" wire:click="$set('statusFilter', 'empty')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $statusFilter === 'empty' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800 hover:bg-rose-50 dark:hover:bg-rose-950/40' }}">
                        Kosong
                    </button>
                </div>

                @if ($search || $statusFilter || $subCategoryFilter || $sortBy !== 'latest')
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
        <div wire:loading wire:target="search, statusFilter, subCategoryFilter, sortBy, previousPage, nextPage, gotoPage, resetFilters" class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center z-10 transition">
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
                        <th class="px-6 py-4 w-14">No</th>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">Topik / Sub-Kategori (Opsional)</th>
                        <th class="px-6 py-4 w-28">Total Soal</th>
                        <th class="px-6 py-4 text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-gray-700 dark:text-slate-300">
                    @forelse ($categories as $index => $category)
                        @php
                            $catJson = [
                                'id' => $category->id,
                                'name' => $category->name,
                                'sub_categories' => $category->subCategories->map(fn($s) => [
                                    'id' => $s->id,
                                    'name' => $s->name,
                                    'description' => $s->description,
                                    'questions_count' => $s->questions_count ?? 0,
                                ])->values()->all(),
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-500 dark:text-slate-400">
                                {{ $categories->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-semibold shrink-0">
                                        {{ strtoupper(substr($category->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-500 dark:text-slate-400 line-clamp-2">
                                    {{ $category->description ?? 'Tidak ada deskripsi' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($category->sub_categories_count > 0)
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                @click="openSubCategoryModal(@js($catJson))" 
                                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 transition cursor-pointer group shadow-xs"
                                                title="Klik untuk mengelola topik pada kategori ini">
                                            <svg class="w-3.5 h-3.5 text-indigo-500 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <span class="font-bold">{{ $category->sub_categories_count }} Topik</span>
                                            <span class="text-[10px] text-indigo-400 font-semibold group-hover:translate-x-0.5 transition-transform">Kelola</span>
                                        </button>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400 dark:text-slate-500 italic">Umum (Tanpa Topik)</span>
                                        <button type="button" 
                                                @click="openSubCategoryModal(@js($catJson))" 
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-[#93F514]/10 hover:bg-emerald-100 dark:hover:bg-[#93F514]/20 border border-emerald-200/80 dark:border-[#93F514]/30 transition cursor-pointer"
                                                title="Tambah topik/sub-kategori opsional untuk kategori ini">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span>Tambah Topik</span>
                                        </button>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    {{ $category->questions_count }} Soal
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="openEditModal(@js([
                                        'id' => $category->id,
                                        'name' => $category->name,
                                        'description' => $category->description
                                    ]))" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Edit Kategori">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="openDeleteModal(@js([
                                        'id' => $category->id,
                                        'name' => $category->name
                                    ]))" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Hapus Kategori">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span class="text-sm font-medium">Belum ada data kategori soal</span>
                                    <span class="text-xs">Klik tombol "Tambah Kategori" untuk menambahkan data baru.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages() || $perPage != 10)
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
                @if ($categories->hasPages())
                    <div>
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Create Category Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCreateModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Tambah Kategori Soal Baru
                        </h3>
                        <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.test_category.store') }}" method="POST" @submit="isSubmittingCreate = true" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Nama Kategori <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Tes Potensi Akademik / Finance & Accounting" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('name')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Deskripsi Kategori
                            </label>
                            <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat mengenai jenis atau tujuan tes..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingCreate" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                                <svg x-show="isSubmittingCreate" class="animate-spin w-3.5 h-3.5 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmittingCreate ? 'Menyimpan...' : 'Simpan Data'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showEditModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Edit Kategori Soal
                        </h3>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'{{ url('admin/test-categories') }}/' + editData.id" method="POST" @submit="isSubmittingEdit = true" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_edit" value="1">
                        <input type="hidden" name="id" :value="editData.id">

                        <div>
                            <label for="edit_name" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Nama Kategori <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="edit_name" x-model="editData.name" required placeholder="Contoh: Tes Potensi Akademik" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('name')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="edit_description" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Deskripsi Kategori
                            </label>
                            <textarea name="description" id="edit_description" rows="3" x-model="editData.description" placeholder="Deskripsi singkat mengenai jenis atau tujuan tes..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"></textarea>
                            @error('description')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingEdit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                                <svg x-show="isSubmittingEdit" class="animate-spin w-3.5 h-3.5 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmittingEdit ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Category Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDeleteModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                Hapus Kategori Soal
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                Apakah Anda yakin ingin menghapus kategori soal <span class="font-bold text-gray-800 dark:text-gray-200" x-text="deleteData.name"></span>? Seluruh topik di dalamnya juga akan terhapus. Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>

                    <form :action="'{{ url('admin/test-categories') }}/' + deleteData.id" method="POST" @submit="isSubmittingDelete = true" class="mt-6 flex items-center justify-end gap-3">
                        @csrf
                        @method('DELETE')

                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmittingDelete" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-md shadow-rose-500/20 transition flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                            <svg x-show="isSubmittingDelete" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmittingDelete ? 'Menghapus...' : 'Hapus Data'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Sub-Category / Topics Modal (Rekomendasi 1) -->
    <div x-show="showSubCategoryModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-sub-category-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showSubCategoryModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 @click="showSubCategoryModal = false" 
                 class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showSubCategoryModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-5 sm:p-6 space-y-4">
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-sub-category-title">
                                        Kelola Topik / Sub-Kategori
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                        Opsional
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                    Kategori Induk: <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="activeCategory.name || 'Memuat...'"></span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="showSubCategoryModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Explanatory Banner: Mengapa Opsional -->
                    <div class="p-3.5 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/60 dark:border-indigo-800/60 text-indigo-950 dark:text-indigo-200 flex items-start gap-3 text-xs leading-relaxed">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-bold">Sub-kategori ini sepenuhnya opsional.</span> Gunakan hanya jika kategori ini perlu dipecah ke topik-topik spesifik (misal pada tes FAT: Pajak PPh 21, PPN, Akuntansi Biaya). Jika kategori bersifat umum atau tunggal, biarkan kosong tanpa topik.
                        </div>
                    </div>

                    <!-- Form: Tambah Topik Baru -->
                    <form action="{{ route('admin.test_sub_category.store') }}" method="POST" @submit="isSubmittingSubCategoryCreate = true" class="p-4 rounded-xl bg-gray-50 dark:bg-slate-800/60 border border-gray-200 dark:border-slate-700/60 space-y-3">
                        @csrf
                        <input type="hidden" name="is_sub_category" value="1">
                        <input type="hidden" name="category_id" :value="activeCategory.id">

                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-800 dark:text-slate-200 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Topik Baru
                            </span>
                            <span class="text-[10px] text-gray-400 dark:text-slate-500">Akan terhubung ke kategori ini</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
                            <div class="sm:col-span-5">
                                <input type="text" 
                                       name="name" 
                                       required 
                                       placeholder="Nama Topik (misal: PPh Pasal 21)" 
                                       class="w-full px-3 py-2 text-xs rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            </div>
                            <div class="sm:col-span-5">
                                <input type="text" 
                                       name="description" 
                                       placeholder="Deskripsi singkat topik (opsional)" 
                                       class="w-full px-3 py-2 text-xs rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            </div>
                            <div class="sm:col-span-2">
                                <button type="submit" 
                                        :disabled="isSubmittingSubCategoryCreate" 
                                        class="w-full h-full py-2 px-3 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-sm dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-lg transition flex items-center justify-center gap-1.5 disabled:opacity-60 cursor-pointer">
                                    <svg x-show="isSubmittingSubCategoryCreate" class="animate-spin w-3 h-3 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="isSubmittingSubCategoryCreate ? '...' : '+ Tambah'"></span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- List Topik Terdaftar -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between text-xs font-bold text-gray-700 dark:text-slate-300">
                            <span>Daftar Topik Terdaftar (<span x-text="activeCategory.sub_categories ? activeCategory.sub_categories.length : 0"></span>)</span>
                            <span class="text-[11px] font-normal text-gray-400 dark:text-slate-500">Klik ikon pensil untuk mengedit nama</span>
                        </div>

                        <div class="max-h-72 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                            <!-- Empty state -->
                            <template x-if="!activeCategory.sub_categories || activeCategory.sub_categories.length === 0">
                                <div class="py-8 px-4 text-center rounded-xl border border-dashed border-gray-200 dark:border-slate-800 text-gray-400 dark:text-slate-500 text-xs space-y-1">
                                    <div class="font-semibold text-gray-600 dark:text-slate-400">Belum ada topik khusus untuk kategori ini.</div>
                                    <div>Seluruh soal yang menggunakan kategori ini akan otomatis diperlakukan sebagai kategori umum.</div>
                                </div>
                            </template>

                            <!-- Sub-category items -->
                            <template x-for="sub in (activeCategory.sub_categories || [])" :key="sub.id">
                                <div class="p-3 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 transition hover:border-gray-300 dark:hover:border-slate-700">
                                    
                                    <!-- View Mode -->
                                    <div x-show="editingSubId !== sub.id" class="flex items-center justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-900 dark:text-white truncate" x-text="sub.name"></span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300" x-text="(sub.questions_count || 0) + ' Soal'"></span>
                                            </div>
                                            <p class="text-[11px] text-gray-400 dark:text-slate-500 truncate mt-0.5" x-text="sub.description || 'Tidak ada deskripsi'"></p>
                                        </div>

                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button" @click="startEditSub(sub)" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition" title="Edit Topik">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button type="button" @click="openDeleteSubModal(sub)" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition" title="Hapus Topik">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Inline Edit Mode -->
                                    <form x-show="editingSubId === sub.id" 
                                          :action="'{{ url('admin/test-sub-categories') }}/' + sub.id" 
                                          method="POST" 
                                          @submit="isSubmittingSubCategoryEdit = true" 
                                          class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_sub_category" value="1">
                                        <input type="hidden" name="category_id" :value="activeCategory.id">

                                        <div class="flex-1">
                                            <input type="text" 
                                                   name="name" 
                                                   x-model="editingSubName" 
                                                   required 
                                                   placeholder="Nama Topik" 
                                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="flex-1">
                                            <input type="text" 
                                                   name="description" 
                                                   x-model="editingSubDescription" 
                                                   placeholder="Deskripsi (opsional)" 
                                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <button type="submit" 
                                                    :disabled="isSubmittingSubCategoryEdit" 
                                                    class="px-3 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-lg transition disabled:opacity-60 cursor-pointer">
                                                Simpan
                                            </button>
                                            <button type="button" 
                                                    @click="cancelEditSub()" 
                                                    class="px-2.5 py-1.5 text-xs font-semibold text-gray-500 hover:text-gray-700 dark:hover:text-slate-300 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition cursor-pointer">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end pt-3 border-t border-gray-100 dark:border-slate-800">
                        <button type="button" 
                                @click="showSubCategoryModal = false" 
                                class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Sub-Category Confirmation Modal -->
    <div x-show="showDeleteSubCategoryModal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-delete-sub-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteSubCategoryModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 @click="showDeleteSubCategoryModal = false" 
                 class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteSubCategoryModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-delete-sub-title">
                                Hapus Topik / Sub-Kategori
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                Apakah Anda yakin ingin menghapus topik <span class="font-bold text-gray-800 dark:text-gray-200" x-text="deleteSubData.name"></span>? Soal-soal yang ada akan tetap tersimpan di bawah kategori utama sebagai materi umum tanpa topik.
                            </p>
                        </div>
                    </div>

                    <form :action="'{{ url('admin/test-sub-categories') }}/' + deleteSubData.id" method="POST" @submit="isSubmittingSubCategoryDelete = true" class="mt-6 flex items-center justify-end gap-3">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="is_sub_category" value="1">

                        <button type="button" @click="showDeleteSubCategoryModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmittingSubCategoryDelete" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-md shadow-rose-500/20 transition flex items-center justify-center gap-2 disabled:opacity-60 cursor-pointer">
                            <svg x-show="isSubmittingSubCategoryDelete" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmittingSubCategoryDelete ? 'Menghapus...' : 'Ya, Hapus Topik'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
