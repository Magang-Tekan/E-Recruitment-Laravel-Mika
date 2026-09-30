<div class="space-y-6" x-data="{ 
    showCreateModal: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }},
    showEditModal: {{ $errors->any() && old('is_edit') ? 'true' : 'false' }},
    showDeleteModal: false,
    isSubmittingCreate: false,
    isSubmittingEdit: false,
    isSubmittingDelete: false,
    editData: {
        id: '{{ old('id', '') }}',
        company_id: '{{ old('company_id', '') }}',
        tag: '{{ old('tag', 'Acara & Kolaborasi') }}',
        title: '{{ old('title', '') }}',
        description: '{{ old('description', '') }}',
        badge_title: '{{ old('badge_title', 'EVENT') }}',
        badge_sub: '{{ old('badge_sub', '') }}',
        is_active: {{ old('is_active', '1') ? 'true' : 'false' }},
        img1_url: '',
        img2_url: '',
        img3_url: ''
    },
    deleteData: {
        id: '',
        title: ''
    },
    openEditModal(item) {
        this.editData = {
            id: item.id,
            company_id: item.company_id || '',
            tag: item.tag || '',
            title: item.title || '',
            description: item.description || '',
            badge_title: item.badge_title || '',
            badge_sub: item.badge_sub || '',
            is_active: item.is_active ? true : false,
            img1_url: item.img1_url || '',
            img2_url: item.img2_url || '',
            img3_url: item.img3_url || ''
        };
        this.showEditModal = true;
    },
    openDeleteModal(item) {
        this.deleteData = {
            id: item.id,
            title: item.title
        };
        this.showDeleteModal = true;
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

    <!-- Header & Action Section -->
    <div class="bg-white dark:bg-[#0D1527] border border-gray-200 dark:border-[#1D2E54] overflow-hidden shadow-xs rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>Showcase Acara & Dokumentasi Perusahaan</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-[#93F514]/10 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30">
                        {{ $showcases->total() }} Kegiatan
                    </span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                    Kelola foto kolase (3 foto per event), judul, dan deskripsi yang tampil di slider Beranda & Tentang Kami.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul / tag kegiatan..." class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <!-- Add Button -->
                <button @click="showCreateModal = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 dark:bg-[#93F514] dark:hover:bg-[#82dc12] text-white dark:text-black font-bold text-xs shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Kegiatan Acara</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white dark:bg-[#0D1527] border border-gray-200 dark:border-[#1D2E54] overflow-hidden shadow-xs rounded-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-[#1D2E54] bg-gray-50/75 dark:bg-[#14203A] text-gray-500 dark:text-slate-400 uppercase font-semibold text-[11px] tracking-wider">
                        <th class="px-5 py-3.5 w-16 text-center">No</th>
                        <th class="px-5 py-3.5 w-56">Kolase Foto (3 Foto)</th>
                        <th class="px-5 py-3.5">Informasi Acara & Deskripsi</th>
                        <th class="px-5 py-3.5 w-36">Badge Box</th>
                        <th class="px-5 py-3.5 w-24 text-center">Status</th>
                        <th class="px-5 py-3.5 w-24 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1D2E54]/60 text-gray-700 dark:text-slate-200">
                    @forelse ($showcases as $item)
                        <tr wire:key="showcase-row-{{ $item->id }}" class="hover:bg-gray-50/80 dark:hover:bg-[#14203A]/60 transition-colors">
                            <!-- No (Urutan Slide Otomatis) -->
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] font-bold text-gray-700 dark:text-slate-300 text-xs" title="Slide ke-{{ $showcases->firstItem() + $loop->index }} (Otomatis)">
                                    {{ $showcases->firstItem() + $loop->index }}
                                </span>
                            </td>

                            <!-- 3 Kolase Foto -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-1.5">
                                    <!-- Foto 1 (Utama) -->
                                    <div class="relative group/pic1 w-16 h-14 rounded-lg bg-black/40 overflow-hidden border border-gray-200 dark:border-[#1D2E54] shrink-0" title="Foto Utama / Poster">
                                        <img src="{{ $item->img1_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover/pic1:scale-110 transition-transform duration-300">
                                        <span class="absolute bottom-0 inset-x-0 bg-black/75 text-[9px] text-white text-center py-0.5 font-bold">1. Utama</span>
                                    </div>
                                    <!-- Foto 2 -->
                                    <div class="relative group/pic2 w-12 h-14 rounded-lg bg-black/40 overflow-hidden border border-gray-200 dark:border-[#1D2E54] shrink-0" title="Foto Aktivitas">
                                        @if($item->img2_url)
                                            <img src="{{ $item->img2_url }}" alt="Foto 2" class="w-full h-full object-cover group-hover/pic2:scale-110 transition-transform duration-300">
                                            <span class="absolute bottom-0 inset-x-0 bg-black/75 text-[8.5px] text-gray-300 text-center py-0.5">2</span>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400 bg-gray-100 dark:bg-[#14203A]">-</div>
                                        @endif
                                    </div>
                                    <!-- Foto 3 -->
                                    <div class="relative group/pic3 w-12 h-14 rounded-lg bg-black/40 overflow-hidden border border-gray-200 dark:border-[#1D2E54] shrink-0" title="Foto Pameran/Display">
                                        @if($item->img3_url)
                                            <img src="{{ $item->img3_url }}" alt="Foto 3" class="w-full h-full object-cover group-hover/pic3:scale-110 transition-transform duration-300">
                                            <span class="absolute bottom-0 inset-x-0 bg-black/75 text-[8.5px] text-gray-300 text-center py-0.5">3</span>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400 bg-gray-100 dark:bg-[#14203A]">-</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Informasi Acara & Deskripsi -->
                            <td class="px-5 py-4">
                                <div class="space-y-1 max-w-lg">
                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-zinc-400 border border-gray-200 dark:border-white/10">
                                        <span>{{ $item->tag ?? 'Kegiatan' }}</span>
                                    </div>
                                    <h4 class="font-bold text-sm text-gray-900 dark:text-white leading-snug">
                                        {{ $item->title }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-zinc-400 line-clamp-2 leading-relaxed">
                                        {{ $item->description }}
                                    </p>
                                </div>
                            </td>

                            <!-- Badge Teks -->
                            <td class="px-5 py-4">
                                <div class="p-2 rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] space-y-0.5">
                                    <div class="font-black text-[11px] text-emerald-600 dark:text-[#93F514] tracking-wide">
                                        {{ $item->badge_title ?? 'EVENT' }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 dark:text-slate-400 truncate">
                                        {{ $item->badge_sub ?? '-' }}
                                    </div>
                                </div>
                            </td>

                            <!-- Status (Toggle Switch Geser) -->
                            <td class="px-5 py-4 text-center">
                                <div class="inline-flex flex-col items-center gap-1">
                                    <button type="button" 
                                            wire:click="toggleActive({{ $item->id }})" 
                                            wire:loading.attr="disabled"
                                            wire:target="toggleActive({{ $item->id }})"
                                            role="switch" 
                                            aria-checked="{{ $item->is_active ? 'true' : 'false' }}"
                                            class="group relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:focus:ring-[#93F514]/40 active:scale-95 disabled:opacity-60 {{ $item->is_active ? 'bg-emerald-500 dark:bg-[#93F514]' : 'bg-gray-300 dark:bg-slate-700' }}" 
                                            title="Klik untuk geser status ({{ $item->is_active ? 'Aktif' : 'Nonaktif' }})">
                                        <span class="sr-only">Toggle Status</span>
                                        <span aria-hidden="true" 
                                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition-transform duration-200 ease-in-out {{ $item->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                    <span class="text-[10px] font-bold tracking-wider uppercase select-none transition-colors duration-200 {{ $item->is_active ? 'text-emerald-600 dark:text-[#93F514]' : 'text-gray-400 dark:text-slate-500' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit Button -->
                                    <button @click="openEditModal({{ json_encode([
                                        'id' => $item->id,
                                        'company_id' => $item->company_id,
                                        'tag' => $item->tag,
                                        'title' => $item->title,
                                        'description' => $item->description,
                                        'badge_title' => $item->badge_title,
                                        'badge_sub' => $item->badge_sub,
                                        'is_active' => $item->is_active,
                                        'img1_url' => $item->img1_url,
                                        'img2_url' => $item->img2_url,
                                        'img3_url' => $item->img3_url,
                                    ]) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-600 dark:hover:text-[#93F514] hover:bg-gray-100 dark:hover:bg-[#14203A] transition cursor-pointer" title="Edit Data & Ganti Foto">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <button @click="openDeleteModal({{ json_encode([
                                        'id' => $item->id,
                                        'title' => $item->title
                                    ]) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-gray-100 dark:hover:bg-[#14203A] transition cursor-pointer" title="Hapus Kegiatan">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-medium">Belum ada kegiatan showcase</span>
                                    <span class="text-xs">Klik tombol "Tambah Kegiatan Acara" untuk menambahkan item baru ke slider.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($showcases->hasPages() || $perPage != 10)
            <div class="px-6 py-4 border-t border-gray-200 dark:border-[#1D2E54] flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 dark:text-slate-400">Tampilkan</span>
                    <select wire:model.live="perPage" class="pl-2.5 pr-7 py-1.5 text-xs rounded-lg bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-700 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-xs text-gray-500 dark:text-slate-400">data per halaman</span>
                </div>
                @if ($showcases->hasPages())
                    <div>
                        {{ $showcases->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- ==================== MODAL CREATE ==================== -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCreateModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-[#0D1527] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-[#1D2E54]">
                
                <form action="{{ route('admin.showcase.store') }}" method="POST" enctype="multipart/form-data" @submit="isSubmittingCreate = true">
                    @csrf
                    <div class="p-6 space-y-5">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-[#1D2E54]">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    Tambah Kegiatan / Showcase Baru
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                    Data ini akan langsung tampil di slider carousel Beranda & Tentang Kami.
                                </p>
                            </div>
                            <button type="button" @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- 3 Foto Kolase Upload Section -->
                        <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-[#14203A]/60 border border-gray-200 dark:border-[#1D2E54] space-y-3">
                            <span class="block text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                Upload 3 Kolase Foto Kegiatan
                            </span>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Foto 1: Utama (Wajib) -->
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Foto 1 (Utama/Poster) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="file" name="img1" required accept="image/*" class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#93F514]/20 dark:file:text-[#93F514] hover:file:bg-emerald-100 cursor-pointer">
                                </div>

                                <!-- Foto 2: Aktivitas (Opsional) -->
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Foto 2 (Aktivitas/Detail)
                                    </label>
                                    <input type="file" name="img2" accept="image/*" class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#93F514]/20 dark:file:text-[#93F514] hover:file:bg-emerald-100 cursor-pointer">
                                </div>

                                <!-- Foto 3: Display (Opsional) -->
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Foto 3 (Display/Pameran)
                                    </label>
                                    <input type="file" name="img3" accept="image/*" class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#93F514]/20 dark:file:text-[#93F514] hover:file:bg-emerald-100 cursor-pointer">
                                </div>
                            </div>
                            <p class="text-[10.5px] text-gray-400 dark:text-slate-500">Format: JPG, PNG, WEBP. Maksimal 5 MB per foto.</p>
                        </div>

                        <!-- Tag & Judul -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Label Kategori / Tag
                                </label>
                                <input type="text" name="tag" placeholder="Misal: Acara & Kolaborasi" value="{{ old('tag', 'Acara & Kolaborasi') }}" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Judul Kegiatan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="title" required placeholder="Misal: Bimbingan Teknis ASPADIN 2026: Sinergi Kompetensi" value="{{ old('title') }}" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Deskripsi Kegiatan
                            </label>
                            <textarea name="description" rows="3" placeholder="Tuliskan gambaran ringkas kegiatan ini..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none leading-relaxed">{{ old('description') }}</textarea>
                        </div>

                        <!-- Badge Box Info (Kanan Bawah Foto) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Badge Judul (Kotak Kanan Bawah)
                                </label>
                                <input type="text" name="badge_title" placeholder="Misal: EVENT atau CAREER" value="{{ old('badge_title', 'EVENT') }}" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Badge Subtitle
                                </label>
                                <input type="text" name="badge_sub" placeholder="Misal: Technical Guidance" value="{{ old('badge_sub') }}" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>

                        <!-- Status Tampil & Keterangan Otomatis -->
                        <div class="p-3.5 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-800/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 text-emerald-800 dark:text-emerald-300 text-xs">
                                <svg class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Urutan otomatis: Kegiatan baru akan <strong>langsung tampil di slide paling depan (#1)</strong>.</span>
                            </div>
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none shrink-0">
                                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300 dark:border-[#1D2E54] dark:bg-[#14203A]">
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Aktifkan & Tampilkan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-[#14203A] border-t border-gray-100 dark:border-[#1D2E54] flex items-center justify-end gap-3">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmittingCreate" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 dark:bg-[#93F514] dark:hover:bg-[#82dc12] text-white dark:text-black font-bold text-xs shadow-md transition cursor-pointer active:scale-95 disabled:opacity-50">
                            <span x-show="!isSubmittingCreate">Simpan & Terbitkan</span>
                            <span x-show="isSubmittingCreate" x-cloak>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL EDIT ==================== -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showEditModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-[#0D1527] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-200 dark:border-[#1D2E54]">
                
                <form :action="'{{ url('admin/showcases') }}/' + editData.id" method="POST" enctype="multipart/form-data" @submit="isSubmittingEdit = true">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_edit" value="1">
                    <input type="hidden" name="id" :value="editData.id">

                    <div class="p-6 space-y-5">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-[#1D2E54]">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    Edit Kegiatan Showcase & Ganti Foto
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                                    Unggah foto baru jika ingin mengganti dokumentasi yang sudah lama.
                                </p>
                            </div>
                            <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- 3 Foto Kolase Section (With Current Image Previews) -->
                        <div class="p-4 rounded-xl bg-gray-50/80 dark:bg-[#14203A]/60 border border-gray-200 dark:border-[#1D2E54] space-y-3">
                            <span class="block text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                Foto Kolase Saat Ini & Ganti Foto
                            </span>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Foto 1: Utama -->
                                <div class="space-y-1.5">
                                    <label class="block text-[11px] font-semibold text-gray-700 dark:text-slate-300">
                                        Foto 1 (Utama/Poster)
                                    </label>
                                    <div class="w-full h-20 rounded-lg bg-black/40 overflow-hidden border border-gray-200 dark:border-[#1D2E54] relative">
                                        <template x-if="editData.img1_url">
                                            <img :src="editData.img1_url" alt="Foto 1" class="w-full h-full object-cover">
                                        </template>
                                        <span class="absolute bottom-0 inset-x-0 bg-black/75 text-[9px] text-white text-center py-0.5 font-bold">Saat Ini</span>
                                    </div>
                                    <input type="file" name="img1" accept="image/*" class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-1 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#93F514]/20 dark:file:text-[#93F514] hover:file:bg-emerald-100 cursor-pointer">
                                    <span class="block text-[10px] text-gray-400 dark:text-slate-500">Pilih file untuk mengganti</span>
                                </div>

                                <!-- Foto 2: Aktivitas -->
                                <div class="space-y-1.5">
                                    <label class="block text-[11px] font-semibold text-gray-700 dark:text-slate-300">
                                        Foto 2 (Aktivitas)
                                    </label>
                                    <div class="w-full h-20 rounded-lg bg-black/40 overflow-hidden border border-gray-200 dark:border-[#1D2E54] relative">
                                        <template x-if="editData.img2_url">
                                            <img :src="editData.img2_url" alt="Foto 2" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!editData.img2_url">
                                            <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400">Kosong</div>
                                        </template>
                                        <span class="absolute bottom-0 inset-x-0 bg-black/75 text-[9px] text-white text-center py-0.5 font-bold">Saat Ini</span>
                                    </div>
                                    <input type="file" name="img2" accept="image/*" class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-1 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#93F514]/20 dark:file:text-[#93F514] hover:file:bg-emerald-100 cursor-pointer">
                                    <span class="block text-[10px] text-gray-400 dark:text-slate-500">Pilih file untuk mengganti</span>
                                </div>

                                <!-- Foto 3: Display -->
                                <div class="space-y-1.5">
                                    <label class="block text-[11px] font-semibold text-gray-700 dark:text-slate-300">
                                        Foto 3 (Display)
                                    </label>
                                    <div class="w-full h-20 rounded-lg bg-black/40 overflow-hidden border border-gray-200 dark:border-[#1D2E54] relative">
                                        <template x-if="editData.img3_url">
                                            <img :src="editData.img3_url" alt="Foto 3" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!editData.img3_url">
                                            <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400">Kosong</div>
                                        </template>
                                        <span class="absolute bottom-0 inset-x-0 bg-black/75 text-[9px] text-white text-center py-0.5 font-bold">Saat Ini</span>
                                    </div>
                                    <input type="file" name="img3" accept="image/*" class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-1 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-[#93F514]/20 dark:file:text-[#93F514] hover:file:bg-emerald-100 cursor-pointer">
                                    <span class="block text-[10px] text-gray-400 dark:text-slate-500">Pilih file untuk mengganti</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tag & Judul -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Label Kategori / Tag
                                </label>
                                <input type="text" name="tag" x-model="editData.tag" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Judul Kegiatan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="title" required x-model="editData.title" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Deskripsi Kegiatan
                            </label>
                            <textarea name="description" rows="3" x-model="editData.description" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none leading-relaxed"></textarea>
                        </div>

                        <!-- Badge Box Info -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Badge Judul
                                </label>
                                <input type="text" name="badge_title" x-model="editData.badge_title" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Badge Subtitle
                                </label>
                                <input type="text" name="badge_sub" x-model="editData.badge_sub" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-[#14203A] border border-gray-200 dark:border-[#1D2E54] text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                        </div>

                        <!-- Status Tampil & Keterangan Otomatis -->
                        <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-[#14203A]/60 border border-gray-200 dark:border-[#1D2E54] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 text-gray-600 dark:text-slate-400 text-xs">
                                <svg class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Urutan slide diatur otomatis dari kegiatan yang paling baru diunggah.</span>
                            </div>
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none shrink-0">
                                <input type="checkbox" name="is_active" value="1" x-model="editData.is_active" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300 dark:border-[#1D2E54] dark:bg-[#14203A]">
                                <span class="text-xs font-semibold text-gray-800 dark:text-slate-200">Aktifkan & Tampilkan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-[#14203A] border-t border-gray-100 dark:border-[#1D2E54] flex items-center justify-end gap-3">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmittingEdit" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 dark:bg-[#93F514] dark:hover:bg-[#82dc12] text-white dark:text-black font-bold text-xs shadow-md transition cursor-pointer active:scale-95 disabled:opacity-50">
                            <span x-show="!isSubmittingEdit">Simpan Perubahan</span>
                            <span x-show="isSubmittingEdit" x-cloak>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL DELETE ==================== -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDeleteModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-[#0D1527] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-[#1D2E54]">
                
                <form :action="'{{ url('admin/showcases') }}/' + deleteData.id" method="POST" @submit="isSubmittingDelete = true">
                    @csrf
                    @method('DELETE')
                    <div class="p-6">
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-4 border border-rose-200 dark:border-rose-900/60">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Hapus Kegiatan Acara?
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Apakah Anda yakin ingin menghapus kegiatan <strong class="text-gray-800 dark:text-white" x-text="deleteData.title"></strong>? Kegiatan ini tidak akan tampil lagi di slider website. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-[#14203A] border-t border-gray-100 dark:border-[#1D2E54] flex items-center justify-end gap-3">
                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmittingDelete" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md transition cursor-pointer active:scale-95 disabled:opacity-50">
                            <span x-show="!isSubmittingDelete">Ya, Hapus</span>
                            <span x-show="isSubmittingDelete" x-cloak>Menghapus...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
