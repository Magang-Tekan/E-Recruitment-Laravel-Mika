<div class="space-y-6" x-data="{ 
    showCreateModal: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }},
    showEditModal: {{ $errors->any() && old('is_edit') ? 'true' : 'false' }},
    showDeleteModal: false,
    showDetailModal: false,
    showImportModal: false,
    
    createType: '{{ old('question_type', 'multiple_choice') }}',

    editData: {
        id: '{{ old('id', '') }}',
        category_id: '{{ old('category_id', '') }}',
        question: '{{ old('question', '') }}',
        question_type: '{{ old('question_type', 'multiple_choice') }}',
        points: '{{ old('points', '1') }}',
        options: [
            '{{ old('options.0', '') }}',
            '{{ old('options.1', '') }}',
            '{{ old('options.2', '') }}',
            '{{ old('options.3', '') }}'
        ],
        correct_option: '{{ old('correct_option', '0') }}',
        image_url: null
    },

    deleteData: {
        id: '',
        question: ''
    },

    detailData: {
        id: '',
        category_name: '',
        question: '',
        question_type: '',
        points: 1,
        image_url: null,
        options: []
    },

    openEditModal(q) {
        let correctIdx = 0;
        let opts = ['', '', '', ''];
        if (q.options && q.options.length > 0) {
            q.options.forEach((opt, idx) => {
                opts[idx] = opt.option_text;
                if (opt.is_correct) {
                    correctIdx = idx;
                }
            });
        }

        this.editData = {
            id: q.id,
            category_id: q.category_id,
            question: q.question,
            question_type: q.question_type,
            points: q.points || 1,
            options: opts,
            correct_option: correctIdx,
            image_path: q.image_path || null,
            image_url: q.image_path ? '{{ asset("storage") }}/' + q.image_path : null
        };
        this.showEditModal = true;
    },

    openDeleteModal(q) {
        this.deleteData = {
            id: q.id,
            question: q.question
        };
        this.showDeleteModal = true;
    },

    openDetailModal(q) {
        this.detailData = {
            id: q.id,
            category_name: q.category ? q.category.name : '-',
            question: q.question,
            question_type: q.question_type,
            points: q.points || 1,
            image_path: q.image_path || null,
            image_url: q.image_path ? '{{ asset("storage") }}/' + q.image_path : null,
            options: q.options || []
        };
        this.showDetailModal = true;
    },

    isImage(path) {
        if (!path) return false;
        const ext = path.split('.').pop().toLowerCase();
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(ext);
    },

    getFileName(path) {
        if (!path) return '';
        return path.split('/').pop();
    },

    getFileExt(path) {
        if (!path) return '';
        return path.split('.').pop().toUpperCase();
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
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Daftar Bank Soal</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Kelola butir soal ujian, kunci jawaban, dan integrasi paket tes seleksi.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <button type="button" @click="showImportModal = true" class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition active:scale-95 shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Import Excel</span>
                </button>

                <button type="button" @click="showCreateModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition active:scale-95 shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Soal</span>
                </button>
            </div>
        </div>

        <!-- Filter & Search Toolbar Row -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="relative lg:col-span-4">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari pertanyaan, kategori, opsi..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Kategori -->
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

                <!-- Filter Status Penggunaan di Tes -->
                <div class="relative lg:col-span-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <select wire:model.live="usageFilter" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">Semua Status Tes</option>
                        <option value="used">Sudah Dipakai di Tes</option>
                        <option value="unused">Belum Dipakai (Standalone)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Urutkan / Sort By -->
                <div class="relative lg:col-span-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                    </div>
                    <select wire:model.live="sortBy" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="latest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                        <option value="points_desc">Poin Tertinggi</option>
                        <option value="points_asc">Poin Terendah</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Toolbar Bottom Row: Quick Type Pills, Media Filter & Reset Button -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Quick Type Filter -->
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase mr-1">Jenis:</span>
                        <button type="button" wire:click="$set('type', '')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $type === '' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                            Semua
                        </button>
                        <button type="button" wire:click="$set('type', 'multiple_choice')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $type === 'multiple_choice' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/40' }}">
                            Pilihan Ganda
                        </button>
                        <button type="button" wire:click="$set('type', 'essay')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $type === 'essay' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/40' }}">
                            Essay
                        </button>
                        <button type="button" wire:click="$set('type', 'disc')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $type === 'disc' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800 hover:bg-purple-50 dark:hover:bg-purple-950/40' }}">
                            DISC
                        </button>
                    </div>

                    <div class="hidden sm:block h-4 w-px bg-gray-200 dark:bg-slate-700"></div>

                    <!-- Quick Media Filter -->
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase mr-1">Media:</span>
                        <button type="button" wire:click="$set('hasImage', '')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $hasImage === '' ? 'bg-slate-700 dark:bg-slate-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                            Semua
                        </button>
                        <button type="button" wire:click="$set('hasImage', 'with_image')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $hasImage === 'with_image' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                            Ada Lampiran
                        </button>
                    </div>
                </div>

                @if ($search || $categoryId || $type || $usageFilter || $hasImage || $sortBy !== 'latest')
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
        <div wire:loading wire:target="search, categoryId, type, usageFilter, hasImage, sortBy, previousPage, nextPage, gotoPage, resetFilters" class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center z-10 transition">
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
                        <th class="px-6 py-4">Pertanyaan</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Jenis Soal</th>
                        <th class="px-6 py-4">Poin</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-gray-700 dark:text-slate-300">
                    @forelse ($questions as $index => $q)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-500 dark:text-slate-400">
                                {{ $questions->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 max-w-md">
                                <div class="space-y-1">
                                    <p class="font-bold text-gray-900 dark:text-white line-clamp-2" title="{{ $q->question }}">
                                        {{ Str::limit(trim($q->question), 120) }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-2 pt-0.5">
                                        @if ($q->image_path)
                                            @php
                                                $ext = strtolower(pathinfo($q->image_path, PATHINFO_EXTENSION));
                                                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                            @endphp
                                            <div class="flex items-center gap-1 text-[11px] text-indigo-600 dark:text-indigo-400 font-medium">
                                                @if ($isImg)
                                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>Gambar ({{ strtoupper($ext) }})</span>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <span>Berkas ({{ strtoupper($ext) }})</span>
                                                @endif
                                            </div>
                                        @endif

                                        @if ($q->tests_count > 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60" title="Digunakan dalam {{ $q->tests_count }} paket tes">
                                                <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                <span>{{ $q->tests_count }} Paket Tes</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] text-gray-400 dark:text-slate-500">
                                                Belum masuk paket tes
                                            </span>
                                        @endif
                                    </div>
                                    @if ($q->question_type === 'multiple_choice')
                                        <div class="text-[11px] text-gray-500 dark:text-slate-400">
                                            <span class="font-medium">Kunci:</span> 
                                            @php
                                                $correct = $q->options->where('is_correct', true)->first();
                                                $labels = ['A', 'B', 'C', 'D'];
                                                $correctIdx = $q->options->values()->search(fn($item) => $item->is_correct);
                                            @endphp
                                            @if ($correctIdx !== false && isset($labels[$correctIdx]))
                                                <span class="font-bold text-emerald-600 dark:text-emerald-400">Opsi {{ $labels[$correctIdx] }}</span> ({{ Str::limit($correct->option_text ?? '', 30) }})
                                            @else
                                                <span class="text-rose-500">Belum diset</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-slate-800 text-gray-800 dark:text-slate-200 border border-gray-200 dark:border-slate-700">
                                    {{ $q->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($q->question_type === 'multiple_choice')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Pilihan Ganda
                                    </span>
                                @elseif ($q->question_type === 'disc')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Tes DISC
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Uraian / Essay
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                {{ $q->points }} Poin
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button @click="openDetailModal({{ json_encode($q) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Detail Soal">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="openEditModal({{ json_encode($q) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Edit Soal">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="openDeleteModal({{ json_encode($q) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Hapus Soal">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium">Belum ada data bank soal</span>
                                    <span class="text-xs">Klik tombol "Tambah Soal" untuk menambahkan soal baru.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($questions->hasPages() || $perPage != 10)
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
                @if ($questions->hasPages())
                    <div>
                        {{ $questions->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Create Question Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCreateModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Tambah Soal Baru
                        </h3>
                        <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.question_bank.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                        @csrf

                        <!-- Jenis Soal & Kategori -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="create_question_type" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Jenis Soal <span class="text-rose-500">*</span>
                                </label>
                                <select name="question_type" id="create_question_type" x-model="createType" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    <option value="multiple_choice">Pilihan Ganda</option>
                                    <option value="essay">Uraian / Essay</option>
                                    <option value="disc">Tes DISC</option>
                                </select>
                                @error('question_type')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="create_category_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Kategori Soal <span class="text-rose-500">*</span>
                                </label>
                                <select name="category_id" id="create_category_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Pertanyaan Teks -->
                        <div>
                            <label for="create_question" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Pertanyaan / Teks Soal <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="question" id="create_question" rows="3" required placeholder="Tuliskan pertanyaan di sini..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">{{ old('question') }}</textarea>
                            @error('question')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Upload File/Gambar Pendukung & Poin -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="create_image" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    File / Gambar Pendukung (Opsional)
                                </label>
                                <input type="file" name="image" id="create_image" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.doc,.docx,.xls,.xlsx,.csv" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100 transition">
                                <p class="mt-1 text-[10px] text-gray-400 dark:text-slate-500">Mendukung Gambar (JPG, PNG), PDF, Word (.docx), dan Excel (.xlsx). Maks 10 MB.</p>
                                @error('image')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="create_points" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Bobot Poin <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="points" id="create_points" min="1" value="{{ old('points', 1) }}" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('points')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Options section (Only if Pilihan Ganda) -->
                        <div x-show="createType === 'multiple_choice'" x-transition class="space-y-3 pt-3 border-t border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-gray-800 dark:text-slate-200">
                                    Opsi Pilihan Ganda & Kunci Jawaban <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-medium">Pilih radio button pada opsi yang menjadi Kunci Jawaban</span>
                            </div>

                            @php
                                $optLabels = ['A', 'B', 'C', 'D'];
                            @endphp
                            @foreach ($optLabels as $idx => $label)
                                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 dark:bg-slate-800/60 border border-gray-200 dark:border-slate-700">
                                    <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                        <input type="radio" name="correct_option" value="{{ $idx }}" {{ old('correct_option', 0) == $idx ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="font-bold text-xs w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 flex items-center justify-center">
                                            {{ $label }}
                                        </span>
                                    </label>
                                    <input type="text" name="options[{{ $idx }}]" value="{{ old('options.' . $idx) }}" :required="createType === 'multiple_choice'" placeholder="Isi opsi {{ $label }}..." class="flex-1 px-3 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                                @error('options.' . $idx)
                                    <p class="text-[11px] text-rose-500 pl-11">{{ $message }}</p>
                                @enderror
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-500/20 transition">
                                Simpan Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Question Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showEditModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Edit Soal
                        </h3>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'{{ url('admin/question-banks') }}/' + editData.id" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_edit" value="1">

                        <!-- Jenis Soal & Kategori -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_question_type" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Jenis Soal <span class="text-rose-500">*</span>
                                </label>
                                <select name="question_type" id="edit_question_type" x-model="editData.question_type" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    <option value="multiple_choice">Pilihan Ganda</option>
                                    <option value="essay">Uraian / Essay</option>
                                    <option value="disc">Tes DISC</option>
                                </select>
                                @error('question_type')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="edit_category_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Kategori Soal <span class="text-rose-500">*</span>
                                </label>
                                <select name="category_id" id="edit_category_id" x-model="editData.category_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Pertanyaan Teks -->
                        <div>
                            <label for="edit_question" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Pertanyaan / Teks Soal <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="question" id="edit_question" rows="3" x-model="editData.question" required placeholder="Tuliskan pertanyaan di sini..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"></textarea>
                            @error('question')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Upload File/Gambar Pendukung & Poin -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_image" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Ganti File / Gambar (Opsional)
                                </label>
                                <input type="file" name="image" id="edit_image" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.doc,.docx,.xls,.xlsx,.csv" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100 transition">
                                <p class="mt-1 text-[10px] text-gray-400 dark:text-slate-500">Mendukung Gambar (JPG, PNG), PDF, Word (.docx), dan Excel (.xlsx). Maks 10 MB.</p>
                                
                                <template x-if="editData.image_url">
                                    <div class="mt-2.5 p-2 rounded-xl bg-gray-50 dark:bg-slate-800/80 border border-gray-200 dark:border-slate-700 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <template x-if="isImage(editData.image_path)">
                                                <img :src="editData.image_url" alt="Preview Gambar" class="w-10 h-10 object-cover rounded-lg border border-gray-200 dark:border-slate-700 shrink-0">
                                            </template>
                                            <template x-if="!isImage(editData.image_path)">
                                                <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xs shrink-0" x-text="getFileExt(editData.image_path)">
                                                </div>
                                            </template>
                                            <div class="min-w-0 text-left">
                                                <a :href="editData.image_url" target="_blank" class="block text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline truncate" x-text="getFileName(editData.image_path)"></a>
                                                <span class="block text-[10px] text-gray-400" x-text="isImage(editData.image_path) ? 'File Gambar' : 'Dokumen Terlampir'"></span>
                                            </div>
                                        </div>
                                        <label class="flex items-center gap-1.5 text-xs text-rose-600 dark:text-rose-400 cursor-pointer shrink-0 font-medium">
                                            <input type="checkbox" name="remove_image" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                            Hapus File
                                        </label>
                                    </div>
                                </template>
                                @error('image')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="edit_points" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Bobot Poin <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="points" id="edit_points" min="1" x-model="editData.points" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('points')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Options section (Only if Pilihan Ganda) -->
                        <div x-show="editData.question_type === 'multiple_choice'" x-transition class="space-y-3 pt-3 border-t border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-gray-800 dark:text-slate-200">
                                    Opsi Pilihan Ganda & Kunci Jawaban <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-medium">Pilih radio button pada opsi yang menjadi Kunci Jawaban</span>
                            </div>

                            @foreach ($optLabels as $idx => $label)
                                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 dark:bg-slate-800/60 border border-gray-200 dark:border-slate-700">
                                    <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                        <input type="radio" name="correct_option" value="{{ $idx }}" :checked="editData.correct_option == {{ $idx }}" class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="font-bold text-xs w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 flex items-center justify-center">
                                            {{ $label }}
                                        </span>
                                    </label>
                                    <input type="text" name="options[{{ $idx }}]" x-model="editData.options[{{ $idx }}]" :required="editData.question_type === 'multiple_choice'" placeholder="Isi opsi {{ $label }}..." class="flex-1 px-3 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                                @error('options.' . $idx)
                                    <p class="text-[11px] text-rose-500 pl-11">{{ $message }}</p>
                                @enderror
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md shadow-indigo-500/20 transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Question Modal -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDetailModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDetailModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800" x-text="detailData.category_name"></span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800" x-text="detailData.points + ' Poin'"></span>
                        </div>
                        <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Pertanyaan:</h4>
                        <p class="text-sm font-medium text-gray-900 dark:text-white whitespace-pre-line" x-text="detailData.question"></p>
                    </div>

                    <template x-if="detailData.image_url">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Lampiran Soal / File Pendukung:</h4>
                            <template x-if="isImage(detailData.image_path)">
                                <div class="rounded-xl border border-gray-200 dark:border-slate-800 p-2 bg-gray-50 dark:bg-slate-800/40 inline-block">
                                    <img :src="detailData.image_url" alt="Gambar Pendukung Soal" class="max-h-64 rounded-lg object-contain">
                                </div>
                            </template>
                            <template x-if="!isImage(detailData.image_path)">
                                <div class="p-3.5 rounded-xl border border-indigo-200 dark:border-indigo-800/80 bg-indigo-50/60 dark:bg-indigo-950/40 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0" x-text="getFileExt(detailData.image_path)">
                                        </div>
                                        <div class="min-w-0 text-left">
                                            <span class="block text-xs font-bold text-gray-900 dark:text-white truncate" x-text="getFileName(detailData.image_path)"></span>
                                            <span class="block text-[11px] text-indigo-700 dark:text-indigo-300">File lampiran dokumen studi kasus / materi soal</span>
                                        </div>
                                    </div>
                                    <a :href="detailData.image_url" target="_blank" download class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5 shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Unduh / Buka File</span>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="detailData.question_type === 'multiple_choice'">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Daftar Opsi Jawaban:</h4>
                            <div class="space-y-2">
                                <template x-for="(opt, idx) in detailData.options" :key="opt.id || idx">
                                    <div class="flex items-center justify-between p-3 rounded-xl border transition" :class="opt.is_correct ? 'bg-emerald-50/70 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 font-semibold' : 'bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300'">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold" :class="opt.is_correct ? 'bg-emerald-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-700 dark:text-slate-300'" x-text="['A', 'B', 'C', 'D'][idx]"></span>
                                            <span class="text-xs" x-text="opt.option_text"></span>
                                        </div>
                                        <template x-if="opt.is_correct">
                                            <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 dark:text-emerald-300 font-bold">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Kunci Jawaban
                                            </span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="detailData.question_type === 'disc'">
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Daftar Opsi Pernyataan DISC:</h4>
                            <div class="space-y-2">
                                <template x-for="(opt, idx) in detailData.options" :key="opt.id || idx">
                                    <div class="flex items-center justify-between p-3 rounded-xl border bg-purple-50/50 dark:bg-purple-950/20 border-purple-200 dark:border-purple-800/50 text-gray-800 dark:text-slate-200">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold bg-purple-600 text-white" x-text="(idx + 1)"></span>
                                            <span class="text-xs font-medium" x-text="opt.option_text"></span>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <template x-if="opt.most_tag || opt.least_tag">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800" x-text="'P: ' + (opt.most_tag || opt.attribute_tag || '*')"></span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800" x-text="'K: ' + (opt.least_tag || opt.attribute_tag || '*')"></span>
                                                </div>
                                            </template>
                                            <template x-if="!opt.most_tag && !opt.least_tag">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-700" x-text="'Dimensi ' + (opt.attribute_tag || '-')"></span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="detailData.question_type === 'essay'">
                        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs">
                            💡 Soal ini berjenis <strong>Essay / Uraian</strong>. Hasil jawaban peserta tes akan dinilai manual oleh penguji/admin.
                        </div>
                    </template>

                    <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex justify-end">
                        <button type="button" @click="showDetailModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Question Modal -->
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
                                Hapus Soal
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 line-clamp-3">
                                Apakah Anda yakin ingin menghapus soal "<span class="font-bold text-gray-800 dark:text-gray-200" x-text="deleteData.question"></span>"? Tindakan ini juga akan menghapus opsi jawaban terkait.
                            </p>
                        </div>
                    </div>

                    <form :action="'{{ url('admin/question-banks') }}/' + deleteData.id" method="POST" class="mt-6 flex items-center justify-end gap-3">
                        @csrf
                        @method('DELETE')

                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-md shadow-rose-500/20 transition">
                            Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div x-show="showImportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showImportModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">Import Soal Excel</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Unggah file Excel untuk memasukkan data soal secara masal.</p>
                            </div>
                        </div>
                        <button type="button" @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Template download info box -->
                    <div class="p-4 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-xs text-indigo-900 dark:text-indigo-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold flex items-center gap-1.5">
                                💡 Format File Excel
                            </span>
                            <a href="{{ route('admin.question_bank.download_template') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Template
                            </a>
                        </div>
                        <p class="text-[11px] text-indigo-700 dark:text-indigo-300 leading-relaxed">
                            Gunakan template Excel resmi agar format kolom sesuai (Kategori, Soal, Tipe Soal, Poin, Opsi A - D, Kunci Jawaban).
                        </p>
                    </div>

                    <form action="{{ route('admin.question_bank.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-2">
                                Pilih File Excel (.xlsx, .xls, .csv) <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" 
                                   name="excel_file" 
                                   accept=".xlsx, .xls, .csv" 
                                   required 
                                   class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950/60 dark:file:text-indigo-300 hover:file:bg-indigo-100 transition border border-gray-200 dark:border-slate-700 rounded-xl bg-gray-50 dark:bg-slate-800 p-2">
                        </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end gap-3">
                            <button type="button" @click="showImportModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl shadow-md shadow-emerald-500/20 transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Upload & Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
