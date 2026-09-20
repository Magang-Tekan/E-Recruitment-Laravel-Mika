<div class="space-y-6" x-data="{ 
    showCreateModal: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }},
    showEditModal: {{ $errors->any() && old('is_edit') ? 'true' : 'false' }},
    showDeleteModal: false,
    isSubmittingCreate: false,
    isSubmittingEdit: false,
    isSubmittingDelete: false,
    editData: {
        id: '{{ old('id', '') }}',
        name: '{{ old('name', '') }}',
        email: '{{ old('email', '') }}',
        nik: '{{ old('nik', '') }}',
        role_id: '{{ old('role_id', '') }}'
    },
    deleteData: {
        id: '',
        name: ''
    },
    openEditModal(user) {
        this.editData = {
            id: user.id,
            name: user.name || '',
            email: user.email || '',
            nik: user.nik || '',
            role_id: user.role_id || ''
        };
        this.showEditModal = true;
    },
    openDeleteModal(user) {
        this.deleteData = {
            id: user.id,
            name: user.name
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

    <!-- Header & Action Section Card -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm p-5 sm:p-6 space-y-4">
        <!-- Title & Action Button Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Daftar Pengguna</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Kelola akun pengguna, hak akses peran (Role), dan verifikasi profil akun sistem.</p>
            </div>

            <button type="button" @click="showCreateModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold transition active:scale-95 shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Pengguna</span>
            </button>
        </div>

        <!-- Filter & Search Toolbar Row -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="relative lg:col-span-6">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari nama, email, NIK..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Role Filter -->
                <div class="relative lg:col-span-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <select wire:model.live="roleFilter" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="">Semua Role</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
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
                    <select wire:model.live="sortBy" class="w-full pl-9 pr-8 py-2.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition appearance-none cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                        <option value="latest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                        <option value="name_asc">Nama (A - Z)</option>
                        <option value="name_desc">Nama (Z - A)</option>
                        <option value="role_asc">Berdasarkan Role</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Section Card -->
    <div class="relative bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Livewire Loading Overlay -->
        <div wire:loading wire:target="search, roleFilter, sortBy, previousPage, nextPage, gotoPage, resetFilters" class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center z-10 transition">
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
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">NIK</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-gray-700 dark:text-slate-300">
                    @forelse ($users as $user)
                        @php
                            $photoUrl = null;
                            if (!empty($user->avatar)) {
                                $photoUrl = str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar);
                            } elseif (!empty($user->applicantProfile?->photo)) {
                                $photoUrl = asset('storage/' . $user->applicantProfile->photo);
                            } elseif (!empty($user->employeeProfile?->photo)) {
                                $photoUrl = asset('storage/' . $user->employeeProfile->photo);
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover ring-1 ring-gray-200 dark:ring-slate-700 shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-sm shrink-0 uppercase">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-bold text-gray-900 dark:text-white">
                                                {{ $user->name }}
                                            </span>
                                            @if(auth()->id() == $user->id)
                                                <span class="text-[10px] bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 px-1.5 py-0.5 rounded-md font-medium border border-indigo-200/60 dark:border-indigo-800/60">(Anda)</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1 text-[11px] text-gray-400 dark:text-slate-400">
                                            <span>{{ $user->email }}</span>
                                            @if($user->email_verified_at)
                                                <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" title="Email terverifikasi" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->nik)
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-mono text-gray-700 dark:text-slate-300 font-medium">{{ $user->nik }}</span>
                                        @if(strlen(preg_replace('/[^0-9]/', '', $user->nik)) !== 16)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800" title="Format NIK harus 16 digit angka">
                                                Bukan 16 Digit
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-slate-500 italic text-[11px]">Belum diisi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $roleName = strtolower($user->role?->name ?? '');
                                @endphp
                                @if(in_array($roleName, ['admin', 'superadmin']))
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                        {{ $user->role?->name ?? 'Admin' }}
                                    </span>
                                @elseif($roleName === 'recruiter')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        Recruiter
                                    </span>
                                @elseif(in_array($roleName, ['applicant', 'pelamar']))
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        Applicant
                                    </span>
                                @elseif(in_array($roleName, ['employee', 'karyawan']))
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        Employee
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300">
                                        {{ $user->role?->name ?? '-' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-700 dark:text-slate-300 font-medium">
                                    {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                                </div>
                                <div class="text-[10px] text-gray-400 dark:text-slate-500">
                                    {{ $user->created_at ? $user->created_at->diffForHumans() : '' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button @click="openEditModal({{ json_encode([
                                        'id' => $user->id,
                                        'name' => $user->name,
                                        'email' => $user->email,
                                        'nik' => $user->nik,
                                        'role_id' => $user->role_id,
                                    ]) }})"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                        title="Edit Pengguna">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    @if(auth()->id() != $user->id)
                                        <button @click="openDeleteModal({{ json_encode([
                                            'id' => $user->id,
                                            'name' => $user->name
                                        ]) }})"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                            title="Hapus Pengguna">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span class="text-sm font-medium">Data pengguna tidak ditemukan</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages() || $perPage != 10)
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
                @if ($users->hasPages())
                    <div>
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Create User Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCreateModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Tambah Pengguna Baru
                        </h3>
                        <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.user.store') }}" method="POST" @submit="isSubmittingCreate = true" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Nama Pengguna" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('name')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Alamat Email <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@domain.com" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('email')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="nik" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    NIK / Nomor Induk KTP (16 Digit)
                                </label>
                                <input type="text" name="nik" id="nik" value="{{ old('nik') }}" maxlength="16" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="16 digit angka NIK" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('nik')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="role_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Role Hak Akses <span class="text-rose-500">*</span>
                                </label>
                                <select name="role_id" id="role_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition [color-scheme:light] dark:[color-scheme:dark]">
                                    <option value="" class="bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400">-- Pilih Role --</option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                            {{ $r->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('password')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingCreate" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg x-show="isSubmittingCreate" class="animate-spin w-3.5 h-3.5 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmittingCreate ? 'Menyimpan...' : 'Simpan Pengguna'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showEditModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Edit Data Pengguna
                        </h3>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="'/admin/users/' + editData.id" method="POST" @submit="isSubmittingEdit = true" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_edit" value="1">

                        <div>
                            <label for="edit_name" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Nama Lengkap
                            </label>
                            <input type="text" name="name" id="edit_name" x-model="editData.name" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        </div>

                        <div>
                            <label for="edit_email" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Alamat Email
                            </label>
                            <input type="email" name="email" id="edit_email" x-model="editData.email" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="edit_nik" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    NIK / Nomor Induk KTP (16 Digit)
                                </label>
                                <input type="text" name="nik" id="edit_nik" x-model="editData.nik" maxlength="16" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="16 digit angka NIK" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            </div>

                            <div>
                                <label for="edit_role_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Role Hak Akses <span class="text-rose-500">*</span>
                                </label>
                                <select name="role_id" id="edit_role_id" x-model="editData.role_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition [color-scheme:light] dark:[color-scheme:dark]">
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}" class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white">
                                            {{ $r->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="edit_password" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Ganti Password (Kosongkan jika tidak diubah)
                            </label>
                            <input type="password" name="password" id="edit_password" placeholder="Password baru" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingEdit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                                <svg x-show="isSubmittingEdit" class="animate-spin w-3.5 h-3.5 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmittingEdit ? 'Menyimpan...' : 'Perbarui Pengguna'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
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
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                Konfirmasi Hapus Pengguna
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Tindakan ini permanen.</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs text-gray-600 dark:text-slate-300">
                            Apakah Anda yakin ingin menghapus akun pengguna <strong class="text-gray-900 dark:text-white font-semibold" x-text="deleteData.name"></strong>?
                        </p>
                    </div>

                    <form :action="'/admin/users/' + deleteData.id" method="POST" @submit="isSubmittingDelete = true" class="mt-6 flex items-center justify-end gap-3">
                        @csrf
                        @method('DELETE')

                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmittingDelete" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-md shadow-rose-500/20 transition flex items-center justify-center gap-2">
                            <svg x-show="isSubmittingDelete" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmittingDelete ? 'Menghapus...' : 'Hapus Pengguna'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
