<div class="space-y-6" x-data="{ 
    showCreateModal: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }},
    showEditModal: {{ $errors->any() && old('is_edit') ? 'true' : 'false' }},
    showDeleteModal: false,
    editData: {
        id: '{{ old('id', '') }}',
        category_id: '{{ old('category_id', '') }}',
        name: '{{ old('name', '') }}',
        description: '{{ old('description', '') }}'
    },
    deleteData: {
        id: '',
        name: ''
    },
    openEditModal(sc) {
        this.editData = {
            id: sc.id,
            category_id: sc.category_id || '',
            name: sc.name || '',
            description: sc.description || ''
        };
        this.showEditModal = true;
    },
    openDeleteModal(sc) {
        this.deleteData = {
            id: sc.id,
            name: sc.name
        };
        this.showDeleteModal = true;
    }
}">
    <!-- Session Notifications -->
    @if (session('create') || session('update') || session('delete'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition 
             class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold">{{ session('create') ?? session('update') ?? session('delete') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="p-6 bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Master Sub-Kategori / Topik Soal</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Kelola pembagian materi atau topik di bawah kategori tes utama.</p>
            </div>
            <button type="button" @click="showCreateModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-md shadow-indigo-600/20 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                <span>Tambah Sub-Kategori</span>
            </button>
        </div>

        <!-- Toolbar Filters -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-6 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama sub-kategori atau kategori..."
                       class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>

            <div class="sm:col-span-3">
                <select wire:model.live="categoryId" class="w-full px-3 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <option value="">Semua Kategori Utama</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <select wire:model.live="sortBy" class="w-full px-3 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <option value="latest">Terbaru</option>
                    <option value="name_asc">Nama (A - Z)</option>
                    <option value="questions_desc">Soal Terbanyak</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-slate-800">
            <table class="w-full text-left text-xs text-gray-600 dark:text-slate-300">
                <thead class="bg-gray-50 dark:bg-slate-800/60 text-gray-500 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-gray-100 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5 w-12 text-center">No</th>
                        <th class="px-5 py-3.5">Kategori Utama</th>
                        <th class="px-5 py-3.5">Nama Sub-Kategori (Topik)</th>
                        <th class="px-5 py-3.5 text-center">Jumlah Soal</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse ($subCategories as $index => $sc)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-4 text-center font-medium">{{ $subCategories->firstItem() + $index }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-slate-800 text-gray-800 dark:text-slate-200 border border-gray-200 dark:border-slate-700">
                                    {{ $sc->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $sc->name }}</div>
                                @if($sc->description)
                                    <div class="text-[11px] text-gray-400 dark:text-slate-500 mt-0.5">{{ $sc->description }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300">
                                    {{ $sc->questions_count }} Soal
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal({{ json_encode($sc) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition" title="Edit Sub-Kategori">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button @click="openDeleteModal({{ json_encode($sc) }})" class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 transition" title="Hapus Sub-Kategori">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-xs text-gray-400">Tidak ada data sub-kategori ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4">
            {{ $subCategories->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 p-0">
            <div @click="showCreateModal = false" class="fixed inset-0 bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 space-y-4 border border-gray-200 dark:border-slate-800 shadow-xl">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Tambah Sub-Kategori</h3>
                <form action="{{ route('admin.test_sub_category.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Kategori Utama *</label>
                        <select name="category_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Nama Sub-Kategori / Topik *</label>
                        <input type="text" name="name" required placeholder="Contoh: Pajak – PPh 21 Karyawan" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Deskripsi (Opsional)</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-slate-800">
                        <button type="button" @click="showCreateModal = false" class="px-3 py-2 text-xs font-semibold text-gray-500">Batal</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 text-white rounded-xl">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 p-0">
            <div @click="showEditModal = false" class="fixed inset-0 bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 space-y-4 border border-gray-200 dark:border-slate-800 shadow-xl">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Edit Sub-Kategori</h3>
                <form :action="'{{ url('admin/test-sub-categories') }}/' + editData.id" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Kategori Utama *</label>
                        <select name="category_id" x-model="editData.category_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Nama Sub-Kategori / Topik *</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Deskripsi (Opsional)</label>
                        <textarea name="description" x-model="editData.description" rows="2" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-slate-800">
                        <button type="button" @click="showEditModal = false" class="px-3 py-2 text-xs font-semibold text-gray-500">Batal</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 text-white rounded-xl">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 p-0">
            <div @click="showDeleteModal = false" class="fixed inset-0 bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>
            <div class="relative bg-white dark:bg-slate-900 rounded-2xl max-w-sm w-full p-6 space-y-4 border border-gray-200 dark:border-slate-800 shadow-xl">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Hapus Sub-Kategori</h3>
                <p class="text-xs text-gray-500">Yakin ingin menghapus sub-kategori <strong x-text="deleteData.name"></strong>? Soal yang terkait tidak akan terhapus, hanya relasi sub-kategorinya di-set kosong.</p>
                <form :action="'{{ url('admin/test-sub-categories') }}/' + deleteData.id" method="POST" class="flex justify-end gap-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false" class="px-3 py-2 text-xs font-semibold text-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold bg-rose-600 text-white rounded-xl">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

