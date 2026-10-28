<div class="space-y-6" x-data="{ 
    showCreateModal: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }},
    showEditModal: {{ $errors->any() && old('is_edit') ? 'true' : 'false' }},
    showDeleteModal: false,
    isSubmittingCreate: false,
    isSubmittingEdit: false,
    isSubmittingDelete: false,
    createTargetType: '{{ old('custom_url') ? 'custom' : (old('experience_level') ? 'filter' : 'filter') }}',
    createExperienceLevel: '{{ old('experience_level', 'Magang') }}',
    createCustomUrl: '{{ old('custom_url', '') }}',
    setCreateTargetType(type) {
        this.createTargetType = type;
        if (type === 'filter' && !this.createExperienceLevel) {
            this.createExperienceLevel = 'Magang';
        }
    },
    editTargetType: 'filter',
    setEditTargetType(type) {
        this.editTargetType = type;
        if (type === 'filter' && !this.editData.experience_level) {
            this.editData.experience_level = 'Magang';
        }
    },
    editData: {
        id: '{{ old('id', '') }}',
        num: '{{ old('num', '01') }}',
        sort_order: '{{ old('sort_order', '0') }}',
        badge: '{{ old('badge', '') }}',
        prefix: '{{ old('prefix', '') }}',
        accent: '{{ old('accent', '') }}',
        description: '{{ old('description', '') }}',
        back_badge: '{{ old('back_badge', '') }}',
        back_title: '{{ old('back_title', '') }}',
        highlights: '{{ old('highlights', '') }}',
        cta_text: '{{ old('cta_text', '') }}',
        experience_level: '{{ old('experience_level', '') }}',
        custom_url: '{{ old('custom_url', '') }}',
        is_dark: {{ old('is_dark', '0') ? 'true' : 'false' }},
        is_active: {{ old('is_active', '1') ? 'true' : 'false' }},
        image_url: ''
    },
    deleteData: {
        id: '',
        title: ''
    },
    openEditModal(item) {
        let highlightsText = '';
        if (Array.isArray(item.highlights)) {
            highlightsText = item.highlights.join('\n');
        } else if (typeof item.highlights === 'string') {
            try {
                let parsed = JSON.parse(item.highlights);
                highlightsText = Array.isArray(parsed) ? parsed.join('\n') : item.highlights;
            } catch(e) {
                highlightsText = item.highlights;
            }
        }

        let targetType = 'talent_pool';
        if (item.custom_url) {
            targetType = 'custom';
        } else if (item.experience_level) {
            targetType = 'filter';
        }
        this.editTargetType = targetType;

        this.editData = {
            id: item.id,
            num: item.num || '01',
            sort_order: item.sort_order || 0,
            badge: item.badge || '',
            prefix: item.prefix || '',
            accent: item.accent || '',
            description: item.description || '',
            back_badge: item.back_badge || '',
            back_title: item.back_title || '',
            highlights: highlightsText,
            cta_text: item.cta_text || '',
            experience_level: item.experience_level || 'Magang',
            custom_url: item.custom_url || '',
            is_dark: item.is_dark ? true : false,
            is_active: item.is_active ? true : false,
            image_url: item.image_url || ''
        };
        this.showEditModal = true;
    },
    openDeleteModal(item) {
        this.deleteData = {
            id: item.id,
            title: (item.prefix ? item.prefix + ' ' : '') + (item.accent || item.badge)
        };
        this.showDeleteModal = true;
    }
}">
    <!-- Session Notifications -->
    @if (session('create'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
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
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
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
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
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
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-sm">
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

    <!-- Header & Action Section -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 overflow-hidden shadow-sm rounded-2xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>Pilihan Jalur Karir</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-[#93F514]/10 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30">
                        {{ $tracks->total() }} Jalur
                    </span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                    Kelola kartu jalur karir beranda, termasuk foto, highlight keuntungan, dan tombol aksi.
                </p>
            </div>
            <button @click="showCreateModal = true"
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 shrink-0 self-start sm:self-auto active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Jalur Karir</span>
            </button>
        </div>

        <!-- Filter Toolbar -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800">
            <div class="relative w-full sm:w-80">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari judul, kategori, atau deskripsi jalur..."
                    class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="relative bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Livewire Loading Overlay -->
        <div wire:loading wire:target="search, previousPage, nextPage, gotoPage"
            class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center z-10 transition">
            <div class="flex items-center gap-2.5 px-4 py-2.5 bg-slate-900/90 dark:bg-slate-800/90 text-white rounded-xl shadow-xl text-xs font-semibold">
                <svg class="animate-spin w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memuat data...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 text-gray-500 dark:text-slate-400 uppercase tracking-wider font-semibold text-[11px]">
                        <th class="px-5 py-3.5 w-16 text-center">No</th>
                        <th class="px-5 py-3.5 w-24">Foto</th>
                        <th class="px-5 py-3.5">Kartu Depan</th>
                        <th class="px-5 py-3.5">Kartu Belakang</th>
                        <th class="px-5 py-3.5 w-44">Tombol CTA</th>
                        <th class="px-5 py-3.5 w-28 text-center">Status</th>
                        <th class="px-5 py-3.5 w-24 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-gray-700 dark:text-slate-300">
                    @forelse ($tracks as $item)
                        <tr wire:key="track-row-{{ $item->id }}" class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <!-- No Urut & Sort -->
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 font-semibold text-gray-800 dark:text-slate-200 text-xs">
                                    {{ $item->num }}
                                </span>
                            </td>

                            <!-- Foto Banner Kartu -->
                            <td class="px-5 py-4">
                                <div class="w-16 h-12 rounded-lg bg-gray-100 dark:bg-slate-800 overflow-hidden border border-gray-200 dark:border-slate-700 shrink-0">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->accent }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&auto=format&fit=crop&q=80'" class="w-full h-full object-cover">
                                </div>
                            </td>

                            <!-- Sisi Depan: Badge, Judul, Desc -->
                            <td class="px-5 py-4">
                                <div class="space-y-1 max-w-sm">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 dark:bg-slate-800 text-gray-800 dark:text-slate-200 border border-gray-200 dark:border-slate-700">
                                            {{ $item->badge }}
                                        </span>
                                        @if($item->is_dark)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-900 text-gray-200 dark:bg-black dark:text-[#93F514]">Dark</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-300">Light</span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                        {{ $item->prefix }} <span class="{{ $item->is_dark ? 'text-emerald-600 dark:text-[#93F514]' : '' }}">{{ $item->accent }}</span>
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                        {{ $item->description }}
                                    </p>
                                </div>
                            </td>

                            <!-- Sisi Belakang: Back Title & Highlights -->
                            <td class="px-5 py-4">
                                <div class="space-y-1 max-w-xs">
                                    <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 block">
                                        {{ $item->back_badge ?? 'Program Track' }}
                                    </span>
                                    <div class="font-bold text-xs text-gray-800 dark:text-gray-200">
                                        {{ $item->back_title }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-slate-400">
                                        @php
                                            $highlights = is_array($item->highlights) ? $item->highlights : (json_decode($item->highlights, true) ?: []);
                                        @endphp
                                        <span>{{ count($highlights) }} poin keunggulan</span>
                                    </div>
                                </div>
                            </td>

                            <!-- CTA Button & Filter -->
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <div class="font-semibold text-xs text-gray-900 dark:text-white">
                                        {{ $item->cta_text }}
                                    </div>
                                    @if($item->custom_url)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-md border border-amber-200 dark:border-amber-800 truncate max-w-[150px]" title="{{ $item->custom_url }}">
                                            Link: {{ Str::limit($item->custom_url, 16) }}
                                        </span>
                                    @elseif($item->experience_level)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800">
                                            {{ $item->experience_level }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] text-gray-500 dark:text-slate-400 bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">
                                            Drop CV / Profil
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Toggle -->
                            <td class="px-5 py-4 text-center">
                                <div class="inline-flex flex-col items-center gap-1">
                                    <button type="button" 
                                            wire:click="toggleActive({{ $item->id }})" 
                                            wire:loading.attr="disabled"
                                            wire:target="toggleActive({{ $item->id }})"
                                            role="switch" 
                                            aria-checked="{{ $item->is_active ? 'true' : 'false' }}"
                                            class="group relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500/40 active:scale-95 disabled:opacity-60 {{ $item->is_active ? 'bg-emerald-500 dark:bg-[#93F514]' : 'bg-gray-300 dark:bg-slate-700' }}" 
                                            title="Klik untuk mengubah status">
                                        <span class="sr-only">Toggle Status</span>
                                        <span aria-hidden="true" 
                                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition-transform duration-200 ease-in-out {{ $item->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                    <span class="text-[11px] font-medium select-none {{ $item->is_active ? 'text-emerald-600 dark:text-[#93F514]' : 'text-gray-400 dark:text-slate-500' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Aksi Edit & Delete -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button @click="openEditModal({{ json_encode($item) }})" class="p-1.5 rounded-lg text-gray-500 hover:text-emerald-600 dark:hover:text-[#93F514] hover:bg-gray-100 dark:hover:bg-slate-800 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="openDeleteModal({{ json_encode($item) }})" class="p-1.5 rounded-lg text-gray-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span class="text-xs">Belum ada data jalur karir.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tracks->hasPages())
            <div class="px-5 py-3.5 border-t border-gray-200 dark:border-slate-800">
                {{ $tracks->links() }}
            </div>
        @endif
    </div>

    <!-- ==========================================
         MODAL CREATE
    =========================================== -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition.opacity @click="showCreateModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showCreateModal" x-transition class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-slate-800">
                
                <form action="{{ route('admin.career-track.store') }}" method="POST" enctype="multipart/form-data" @submit="isSubmittingCreate = true">
                    @csrf
                    <div class="px-6 pt-6 pb-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Tambah Jalur Karir</h3>
                        <button @click="showCreateModal = false" type="button" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-4 max-h-[75vh] overflow-y-auto custom-scrollbar text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Nomor Kartu*</label>
                                <input type="text" name="num" value="{{ old('num', '01') }}" required placeholder="Contoh: 01" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Urutan</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', '0') }}" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div class="flex flex-col justify-end">
                                <label class="inline-flex items-center gap-2 cursor-pointer pb-2">
                                    <input type="checkbox" name="is_dark" value="1" {{ old('is_dark') ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded-sm border-gray-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-emerald-500">
                                    <span class="text-xs font-semibold text-gray-700 dark:text-slate-300">Tampilan Kartu Gelap</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Badge Kategori*</label>
                                <input type="text" name="badge" value="{{ old('badge') }}" required placeholder="Misal: Magang" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Awalan Judul*</label>
                                <input type="text" name="prefix" value="{{ old('prefix') }}" required placeholder="Misal: Lowongan" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Aksen Judul*</label>
                                <input type="text" name="accent" value="{{ old('accent') }}" required placeholder="Misal: Magang" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Deskripsi Singkat</label>
                            <textarea name="description" rows="2" placeholder="Ringkasan penjelasan pada kartu depan..." class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Foto Kartu Depan*</label>
                            <input type="file" name="image" required accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#93F514]/10 dark:file:text-[#93F514] hover:file:bg-emerald-100 cursor-pointer">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-gray-100 dark:border-slate-800">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Badge Kartu Belakang</label>
                                <input type="text" name="back_badge" value="{{ old('back_badge', 'Program Track') }}" placeholder="Misal: Internship Track" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Judul Kartu Belakang*</label>
                                <input type="text" name="back_title" value="{{ old('back_title') }}" required placeholder="Misal: Program Magang Industri" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                <span>Poin-Poin Keunggulan (Highlights)*</span>
                                <span class="text-gray-400 font-normal ml-1">(Tulis 1 poin per baris)</span>
                            </label>
                            <textarea name="highlights" rows="4" required placeholder="Mentorship langsung dari praktisi ahli industri&#10;Terlibat langsung dalam proyek nyata berstandar global&#10;Konversi SKS resmi & sertifikat kompetensi&#10;Peluang fast-track rekrutmen karyawan tetap" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none leading-relaxed">{{ old('highlights') }}</textarea>
                        </div>

                        <div class="pt-2 border-t border-gray-100 dark:border-slate-800 space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Label Tombol CTA*</label>
                                <input type="text" name="cta_text" value="{{ old('cta_text', 'Lihat Lowongan') }}" required class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>

                            <!-- Kotak Pengaturan Tujuan Tombol (3 Tab Pilihan) -->
                            <div class="p-3.5 rounded-2xl bg-gray-50/90 dark:bg-slate-800/60 border border-gray-200/80 dark:border-slate-700/80 space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-semibold text-gray-800 dark:text-slate-200">
                                        Tujuan Link Tombol*
                                    </label>
                                    <span class="text-[11px] text-gray-500 dark:text-slate-400">Pilih aksi saat tombol diklik</span>
                                </div>

                                <!-- 3 Tab Pilihan -->
                                <div class="grid grid-cols-3 gap-1.5 p-1 bg-white dark:bg-slate-900 rounded-xl border border-gray-200/70 dark:border-slate-800 text-xs">
                                    <button type="button" 
                                            @click="setCreateTargetType('filter')"
                                            :class="createTargetType === 'filter' ? 'bg-emerald-50 dark:bg-[#93F514]/10 text-emerald-700 dark:text-[#93F514] font-semibold border-emerald-200 dark:border-[#93F514]/30' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white border-transparent'"
                                            class="py-2 px-2 rounded-lg border text-center transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <span class="truncate">Filter Lowongan</span>
                                    </button>

                                    <button type="button" 
                                            @click="setCreateTargetType('talent_pool')"
                                            :class="createTargetType === 'talent_pool' ? 'bg-emerald-50 dark:bg-[#93F514]/10 text-emerald-700 dark:text-[#93F514] font-semibold border-emerald-200 dark:border-[#93F514]/30' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white border-transparent'"
                                            class="py-2 px-2 rounded-lg border text-center transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="truncate">Drop CV / Profil</span>
                                    </button>

                                    <button type="button" 
                                            @click="setCreateTargetType('custom')"
                                            :class="createTargetType === 'custom' ? 'bg-emerald-50 dark:bg-[#93F514]/10 text-emerald-700 dark:text-[#93F514] font-semibold border-emerald-200 dark:border-[#93F514]/30' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white border-transparent'"
                                            class="py-2 px-2 rounded-lg border text-center transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        <span class="truncate">URL Kustom</span>
                                    </button>
                                </div>

                                <!-- Nilai input yang dikirim ke server -->
                                <input type="hidden" name="experience_level" :value="createTargetType === 'filter' ? createExperienceLevel : ''">
                                <input type="hidden" name="custom_url" :value="createTargetType === 'custom' ? createCustomUrl : ''">

                                <!-- Konten 1: Filter Lowongan -->
                                <div x-show="createTargetType === 'filter'" class="space-y-1.5">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300">
                                        Pilih Tingkat Lowongan:
                                    </label>
                                    <select x-model="createExperienceLevel" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none text-xs">
                                        <option value="Magang">Magang (Internship)</option>
                                        <option value="Fresh Graduate">Fresh Graduate</option>
                                        <option value="Profesional">Profesional (Experienced)</option>
                                    </select>
                                    <div class="text-[11px] text-gray-500 dark:text-slate-400 flex items-center gap-1.5 pt-0.5">
                                        <span>Preview Link:</span>
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 font-mono text-[10.5px]" x-text="'/lowongan?experience_level=' + createExperienceLevel"></span>
                                    </div>
                                </div>

                                <!-- Konten 2: Drop CV / Talent Pool -->
                                <div x-show="createTargetType === 'talent_pool'" class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-gray-200/70 dark:border-slate-800 text-xs">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-slate-200">Mode Talent Pool</p>
                                            <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-relaxed mt-0.5">
                                                Pelamar otomatis diarahkan ke formulir profil / CV (<span class="font-mono text-emerald-600 dark:text-emerald-400">/profile</span> atau diarahkan login terlebih dahulu jika belum masuk).
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Konten 3: URL Kustom -->
                                <div x-show="createTargetType === 'custom'" class="space-y-1.5">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300">
                                        Alamat URL Tujuan:
                                    </label>
                                    <input type="text" 
                                           x-model="createCustomUrl" 
                                           placeholder="Contoh: https://pin.it/..., https://forms.gle/..., atau link lainnya" 
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none text-xs">
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-relaxed">
                                        Tautan eksternal otomatis dibuka di tab baru saat pelamar mengklik tombol kartu.
                                    </p>
                                </div>
                            </div>

                            <!-- Opsi Tampilkan di Beranda -->
                            <div class="pt-1">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-emerald-600 rounded-sm border-gray-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-emerald-500">
                                    <span class="text-xs font-semibold text-gray-700 dark:text-slate-300">Tampilkan Jalur Karir ini di Beranda</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end gap-3 text-xs">
                        <button @click="showCreateModal = false" type="button" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-800 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmittingCreate" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 dark:bg-[#93F514] dark:hover:bg-[#82dc12] text-white dark:text-black font-semibold shadow-xs transition cursor-pointer disabled:opacity-50">
                            <span x-show="!isSubmittingCreate">Simpan</span>
                            <span x-show="isSubmittingCreate" x-cloak>Menyimpan...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- ==========================================
         MODAL EDIT
    =========================================== -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" x-transition.opacity @click="showEditModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditModal" x-transition class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-slate-800">
                
                <form :action="'{{ url('admin/career-tracks') }}/' + editData.id" method="POST" enctype="multipart/form-data" @submit="isSubmittingEdit = true">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_edit" value="1">
                    <input type="hidden" name="id" :value="editData.id">

                    <div class="px-6 pt-6 pb-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Edit Jalur Karir</h3>
                        <button @click="showEditModal = false" type="button" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-4 max-h-[75vh] overflow-y-auto custom-scrollbar text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Nomor Kartu*</label>
                                <input type="text" name="num" x-model="editData.num" required class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Urutan</label>
                                <input type="number" name="sort_order" x-model="editData.sort_order" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div class="flex flex-col justify-end">
                                <label class="inline-flex items-center gap-2 cursor-pointer pb-2">
                                    <input type="checkbox" name="is_dark" value="1" x-model="editData.is_dark" class="w-4 h-4 text-emerald-600 rounded-sm border-gray-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-emerald-500">
                                    <span class="text-xs font-semibold text-gray-700 dark:text-slate-300">Tampilan Kartu Gelap</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Badge Kategori*</label>
                                <input type="text" name="badge" x-model="editData.badge" required class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Awalan Judul*</label>
                                <input type="text" name="prefix" x-model="editData.prefix" required class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Aksen Judul*</label>
                                <input type="text" name="accent" x-model="editData.accent" required class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Deskripsi Singkat</label>
                            <textarea name="description" rows="2" x-model="editData.description" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Foto Kartu Depan</label>
                            <div class="flex items-center gap-3">
                                <template x-if="editData.image_url">
                                    <img :src="editData.image_url" alt="Preview" class="w-14 h-11 rounded-lg object-cover border border-gray-200 dark:border-slate-700 shrink-0">
                                </template>
                                <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#93F514]/10 dark:file:text-[#93F514] hover:file:bg-emerald-100 cursor-pointer">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-gray-100 dark:border-slate-800">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Badge Kartu Belakang</label>
                                <input type="text" name="back_badge" x-model="editData.back_badge" class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Judul Kartu Belakang*</label>
                                <input type="text" name="back_title" x-model="editData.back_title" required class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                <span>Poin-Poin Keunggulan (Highlights)*</span>
                                <span class="text-gray-400 font-normal ml-1">(1 baris per poin)</span>
                            </label>
                            <textarea name="highlights" rows="4" x-model="editData.highlights" required class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none leading-relaxed"></textarea>
                        </div>

                        <div class="pt-2 border-t border-gray-100 dark:border-slate-800 space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Label Tombol CTA*</label>
                                <input type="text" name="cta_text" x-model="editData.cta_text" required class="w-full px-3 py-2 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>

                            <!-- Kotak Pengaturan Tujuan Tombol (3 Tab Pilihan) -->
                            <div class="p-3.5 rounded-2xl bg-gray-50/90 dark:bg-slate-800/60 border border-gray-200/80 dark:border-slate-700/80 space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-semibold text-gray-800 dark:text-slate-200">
                                        Tujuan Link Tombol*
                                    </label>
                                    <span class="text-[11px] text-gray-500 dark:text-slate-400">Pilih aksi saat tombol diklik</span>
                                </div>

                                <!-- 3 Tab Pilihan -->
                                <div class="grid grid-cols-3 gap-1.5 p-1 bg-white dark:bg-slate-900 rounded-xl border border-gray-200/70 dark:border-slate-800 text-xs">
                                    <button type="button" 
                                            @click="setEditTargetType('filter')"
                                            :class="editTargetType === 'filter' ? 'bg-emerald-50 dark:bg-[#93F514]/10 text-emerald-700 dark:text-[#93F514] font-semibold border-emerald-200 dark:border-[#93F514]/30' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white border-transparent'"
                                            class="py-2 px-2 rounded-lg border text-center transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <span class="truncate">Filter Lowongan</span>
                                    </button>

                                    <button type="button" 
                                            @click="setEditTargetType('talent_pool')"
                                            :class="editTargetType === 'talent_pool' ? 'bg-emerald-50 dark:bg-[#93F514]/10 text-emerald-700 dark:text-[#93F514] font-semibold border-emerald-200 dark:border-[#93F514]/30' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white border-transparent'"
                                            class="py-2 px-2 rounded-lg border text-center transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="truncate">Drop CV / Profil</span>
                                    </button>

                                    <button type="button" 
                                            @click="setEditTargetType('custom')"
                                            :class="editTargetType === 'custom' ? 'bg-emerald-50 dark:bg-[#93F514]/10 text-emerald-700 dark:text-[#93F514] font-semibold border-emerald-200 dark:border-[#93F514]/30' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white border-transparent'"
                                            class="py-2 px-2 rounded-lg border text-center transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        <span class="truncate">URL Kustom</span>
                                    </button>
                                </div>

                                <!-- Nilai input yang dikirim ke server -->
                                <input type="hidden" name="experience_level" :value="editTargetType === 'filter' ? editData.experience_level : ''">
                                <input type="hidden" name="custom_url" :value="editTargetType === 'custom' ? editData.custom_url : ''">

                                <!-- Konten 1: Filter Lowongan -->
                                <div x-show="editTargetType === 'filter'" class="space-y-1.5">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300">
                                        Pilih Tingkat Lowongan:
                                    </label>
                                    <select x-model="editData.experience_level" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none text-xs">
                                        <option value="Magang">Magang (Internship)</option>
                                        <option value="Fresh Graduate">Fresh Graduate</option>
                                        <option value="Profesional">Profesional (Experienced)</option>
                                    </select>
                                    <div class="text-[11px] text-gray-500 dark:text-slate-400 flex items-center gap-1.5 pt-0.5">
                                        <span>Preview Link:</span>
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 font-mono text-[10.5px]" x-text="'/lowongan?experience_level=' + editData.experience_level"></span>
                                    </div>
                                </div>

                                <!-- Konten 2: Drop CV / Talent Pool -->
                                <div x-show="editTargetType === 'talent_pool'" class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-gray-200/70 dark:border-slate-800 text-xs">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 dark:text-slate-200">Mode Talent Pool</p>
                                            <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-relaxed mt-0.5">
                                                Pelamar otomatis diarahkan ke formulir profil / CV (<span class="font-mono text-emerald-600 dark:text-emerald-400">/profile</span> atau diarahkan login terlebih dahulu jika belum masuk).
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Konten 3: URL Kustom -->
                                <div x-show="editTargetType === 'custom'" class="space-y-1.5">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300">
                                        Alamat URL Tujuan:
                                    </label>
                                    <input type="text" 
                                           x-model="editData.custom_url" 
                                           placeholder="Contoh: https://pin.it/..., https://forms.gle/..., atau link lainnya" 
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none text-xs">
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-relaxed">
                                        Tautan eksternal otomatis dibuka di tab baru saat pelamar mengklik tombol kartu.
                                    </p>
                                </div>
                            </div>

                            <!-- Opsi Tampilkan di Beranda -->
                            <div class="pt-1">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" x-model="editData.is_active" class="w-4 h-4 text-emerald-600 rounded-sm border-gray-300 dark:border-slate-700 dark:bg-slate-800 focus:ring-emerald-500">
                                    <span class="text-xs font-semibold text-gray-700 dark:text-slate-300">Tampilkan Jalur Karir ini di Beranda</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end gap-3 text-xs">
                        <button @click="showEditModal = false" type="button" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-800 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmittingEdit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 dark:bg-[#93F514] dark:hover:bg-[#82dc12] text-white dark:text-black font-semibold shadow-xs transition cursor-pointer disabled:opacity-50">
                            <span x-show="!isSubmittingEdit">Perbarui</span>
                            <span x-show="isSubmittingEdit" x-cloak>Memperbarui...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- ==========================================
         MODAL DELETE
    =========================================== -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" x-transition.opacity @click="showDeleteModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteModal" x-transition class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-200 dark:border-slate-800">
                <form :action="'{{ url('admin/career-tracks') }}/' + deleteData.id" method="POST" @submit="isSubmittingDelete = true">
                    @csrf
                    @method('DELETE')
                    <div class="p-6 text-center">
                        <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">Hapus Jalur Karir?</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-6">
                            Apakah Anda yakin ingin menghapus jalur karir <span class="font-semibold text-gray-800 dark:text-white" x-text="deleteData.title"></span>?
                        </p>
                        <div class="flex items-center justify-center gap-3">
                            <button @click="showDeleteModal = false" type="button" class="w-full px-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 font-semibold hover:bg-gray-100 dark:hover:bg-slate-800 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingDelete" class="w-full px-4 py-2 text-xs rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold shadow-md transition cursor-pointer disabled:opacity-50">
                                <span x-show="!isSubmittingDelete">Ya, Hapus</span>
                                <span x-show="isSubmittingDelete" x-cloak>Menghapus...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
