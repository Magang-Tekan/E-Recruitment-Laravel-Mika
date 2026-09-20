<div class="space-y-6">

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-xs font-semibold">{{ session('message') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Card 1: Header & Action Section Card -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm p-5 sm:p-6 space-y-4">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Data Karyawan Terdaftar</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Kelola data seluruh karyawan internal, status kepegawaian, dan pengangkatan magang.</p>
            </div>
        </div>

        <!-- Filter & Search Toolbar Row -->
        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-3.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="relative lg:col-span-4">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari nama karyawan, NIK, jabatan, email..." 
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

                <!-- Filter Departemen -->
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

            <!-- Toolbar Bottom Row: Quick Tipe Pegawai Badges & Reset Button -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase mr-1">Tipe:</span>
                    <button type="button" wire:click="$set('employeeType', '')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $employeeType === '' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-800' }}">
                        Semua
                    </button>
                    <button type="button" wire:click="$set('employeeType', 'permanent')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $employeeType === 'permanent' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/40' }}">
                        Karyawan Tetap
                    </button>
                    <button type="button" wire:click="$set('employeeType', 'contract')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $employeeType === 'contract' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800 hover:bg-blue-50 dark:hover:bg-blue-950/40' }}">
                        Kontrak
                    </button>
                    <button type="button" wire:click="$set('employeeType', 'internship')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $employeeType === 'internship' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 hover:bg-amber-50 dark:hover:bg-amber-950/40' }}">
                        Magang
                    </button>
                    <button type="button" wire:click="$set('employeeType', 'probation')" class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition cursor-pointer {{ $employeeType === 'probation' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800 hover:bg-purple-50 dark:hover:bg-purple-950/40' }}">
                        Probation
                    </button>
                </div>

                @if ($search || $companyId || $departmentId || $employeeType)
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
        <div wire:loading wire:target="search, companyId, departmentId, employeeType, previousPage, nextPage, gotoPage, resetFilters" class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center z-10 transition">
            <div class="flex items-center gap-2.5 px-4 py-2.5 bg-slate-900/90 dark:bg-slate-800/90 text-white rounded-xl shadow-xl text-xs font-semibold">
                <svg class="animate-spin w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memuat data...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 text-gray-500 dark:text-slate-400 uppercase tracking-wider font-semibold text-[11px]">
                        <th class="py-4 px-6 w-12 text-center">No</th>
                        <th class="py-4 px-6">Identitas Karyawan</th>
                        <th class="py-4 px-6">Tipe Pegawai</th>
                        <th class="py-4 px-6">Departemen / Divisi</th>
                        <th class="py-4 px-6">Jabatan / Posisi</th>
                        <th class="py-4 px-6">Kontak / Email</th>
                        <th class="py-4 px-6 text-center">Status Akun</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-xs text-gray-700 dark:text-slate-300">
                    @forelse ($employees as $index => $emp)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition duration-150">
                            <td class="py-4 px-6 text-center font-medium text-gray-400 dark:text-slate-500">
                                {{ $employees->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    @php
                                        $empPhoto = $emp->photo ?? $emp->user?->avatar;
                                        $empPhotoUrl = null;
                                        if ($empPhoto) {
                                            $empPhotoUrl = str_starts_with($empPhoto, 'http') ? $empPhoto : asset('storage/' . $empPhoto);
                                        }
                                    @endphp
                                    @if ($empPhotoUrl)
                                        <img src="{{ $empPhotoUrl }}" alt="{{ $emp->full_name ?? ($emp->user?->name ?? 'Karyawan') }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-indigo-500/20 shadow-xs shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold text-xs flex items-center justify-center ring-2 ring-indigo-500/20 shrink-0">
                                            {{ strtoupper(substr($emp->full_name ?? ($emp->user?->name ?? 'K'), 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-sm">
                                            {{ $emp->full_name ?? ($emp->user?->name ?? '-') }}
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-wrap text-[11px] text-gray-400 dark:text-slate-500 mt-0.5">
                                            <span>NIK:</span>
                                            <span class="font-mono font-medium text-gray-600 dark:text-slate-300">{{ $emp->nik ?? ($emp->user?->nik ?? '-') }}</span>
                                            @php
                                                $rawNik = preg_replace('/[^0-9]/', '', (string) ($emp->nik ?? ($emp->user?->nik ?? '')));
                                            @endphp
                                            @if (!empty($rawNik) && strlen($rawNik) !== 16)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9.5px] font-semibold bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-900/60" title="Format NIK harus 16 digit angka">
                                                    Bukan 16 Digit
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if ($emp->employee_type === 'internship')
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 font-semibold text-[11px] border border-amber-200 dark:border-amber-800/60 inline-flex items-center gap-1">
                                        Magang
                                    </span>
                                @elseif ($emp->employee_type === 'contract')
                                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-semibold text-[11px] border border-blue-200 dark:border-blue-800/60 inline-flex items-center gap-1">
                                        Kontrak
                                    </span>
                                @elseif ($emp->employee_type === 'probation')
                                    <span class="px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-semibold text-[11px] border border-purple-200 dark:border-purple-800/60 inline-flex items-center gap-1">
                                        Probation
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-semibold text-[11px] border border-emerald-200 dark:border-emerald-800/60 inline-flex items-center gap-1">
                                        Tetap
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 font-semibold text-[11px] border border-indigo-200 dark:border-indigo-800/60">
                                    {{ $emp->department?->name ?? 'Belum Diatur' }}
                                </span>
                                @if ($emp->department?->company)
                                    <div class="text-[10px] text-gray-400 mt-1">{{ $emp->department->company->name }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $pos = trim($emp->position?->name ?? ($emp->position_title ?? ''));
                                    $isGeneric = empty($pos) || strtolower($pos) === 'magang' || strtolower($pos) === 'intern' || strtolower($pos) === 'staff internal';
                                @endphp
                                <div class="font-semibold text-gray-800 dark:text-slate-200">
                                    @if (!$isGeneric)
                                        {{ $pos }}
                                    @elseif ($emp->employee_type === 'internship')
                                        {{ 'Intern ' . ($emp->department?->name ?? 'Divisi') }}
                                    @else
                                        {{ 'Staff ' . ($emp->department?->name ?? 'Internal') }}
                                    @endif
                                </div>
                                @if ($emp->position)
                                    <span class="inline-flex items-center text-[10px] text-indigo-600 dark:text-indigo-400 font-medium">
                                        {{ $emp->position->department?->name }}
                                    </span>
                                @elseif ($emp->employee_type === 'internship' && !$isGeneric)
                                    <span class="text-[10px] text-amber-600 dark:text-amber-400 block mt-0.5 font-medium">Posisi Magang</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-700 dark:text-slate-300 font-medium">{{ $emp->user?->email ?? '-' }}</div>
                                @if ($emp->phone_number)
                                    <div class="text-[11px] text-gray-400">{{ $emp->phone_number }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if ($emp->employee_type === 'internship')
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 font-semibold text-[11px] border border-amber-200/80 dark:border-amber-800/60">
                                        {{-- <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> --}}
                                        Peserta Magang
                                    </span>
                                @elseif ($emp->employee_type === 'probation')
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-semibold text-[11px] border border-purple-200/80 dark:border-purple-800/60">
                                        {{-- <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> --}}
                                        Masa Percobaan
                                    </span>
                                @elseif ($emp->employee_type === 'contract')
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-semibold text-[11px] border border-blue-200/80 dark:border-blue-800/60">
                                        {{-- <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> --}}
                                        Kontrak Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-semibold text-[11px] border border-emerald-200/80 dark:border-emerald-800/60">
                                        {{-- <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> --}}
                                        Karyawan Tetap
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                     @if ($emp->employee_type === 'internship')
                                         <button wire:click="openPromoteModal({{ $emp->id }})" 
                                                 class="px-2.5 py-1 rounded-xl bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 font-semibold text-[11px] transition-all flex items-center gap-1.5 active:scale-95 shadow-sm" 
                                                 title="Angkat jadi Karyawan">
                                             <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                             </svg>
                                             <span>Angkat Karyawan</span>
                                         </button>
                                     @endif

                                     <button wire:click="openEdit({{ $emp->id }})" 
                                             class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition" 
                                             title="Edit Data / Status Pegawai">
                                         <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                         </svg>
                                     </button>

                                     <button wire:click="openDeleteModal({{ $emp->id }})" 
                                             class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition" 
                                             title="Hapus Pegawai">
                                         <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                         </svg>
                                     </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Belum ada karyawan internal yang mendaftar.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages() || $perPage != 10)
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
                @if ($employees->hasPages())
                    <div>
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- DOUBLE PERMISSION MODAL: Angkat Peserta Magang Jadi Karyawan -->
    @if ($showPromoteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="closePromoteModal" class="fixed inset-0 transition-opacity bg-gray-900/50 dark:bg-black/60 backdrop-blur-sm"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 dark:border-slate-800">
                    
                    <!-- Modal Header with Badge & Icon -->
                    <div class="p-6 bg-amber-50/50 dark:bg-amber-950/20 border-b border-gray-100 dark:border-slate-800/80">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-2xl bg-amber-100/70 dark:bg-amber-950/50 border border-amber-200/60 dark:border-amber-800/40 flex items-center justify-center text-amber-700 dark:text-amber-400 shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-amber-100/80 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 mb-1 border border-amber-200/80 dark:border-amber-800/60">
                                        Konfirmasi Pengangkatan
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">
                                        Promosikan {{ $promoteFullName }}
                                    </h3>
                                </div>
                            </div>
                            <button wire:click="closePromoteModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body / Form -->
                    <form wire:submit.prevent="confirmPromotion" class="p-6 space-y-4">
                        
                        <!-- Info Card Ringkasan Transisi -->
                        <div class="p-3.5 rounded-2xl bg-gray-50/80 dark:bg-slate-800/40 border border-gray-100 dark:border-slate-700/60 grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold block">Posisi Saat Ini</span>
                                <span class="font-bold text-gray-700 dark:text-slate-300 block truncate">{{ $promoteCurrentPosition }}</span>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] bg-amber-100/70 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 font-semibold">
                                    Magang
                                </span>
                            </div>
                            <div class="border-l border-gray-200/80 dark:border-slate-700 pl-3">
                                <span class="text-[10px] text-gray-400 uppercase font-semibold block">Departemen</span>
                                <span class="font-bold text-gray-700 dark:text-slate-300 block truncate">{{ $promoteDepartmentName }}</span>
                            </div>
                        </div>

                        <!-- 1. Pilihan Status Baru -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Status Baru <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-all {{ $promoteTargetType === 'permanent' ? 'bg-indigo-50/60 dark:bg-indigo-950/30 border-indigo-400 text-indigo-900 dark:text-indigo-200' : 'bg-gray-50/40 dark:bg-slate-800/30 border-gray-200/80 dark:border-slate-700 text-gray-600 dark:text-gray-400' }}">
                                    <input type="radio" wire:model.live="promoteTargetType" value="permanent" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <span class="text-xs font-bold block">Karyawan Tetap</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Permanent</span>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-all {{ $promoteTargetType === 'contract' ? 'bg-indigo-50/60 dark:bg-indigo-950/30 border-indigo-400 text-indigo-900 dark:text-indigo-200' : 'bg-gray-50/40 dark:bg-slate-800/30 border-gray-200/80 dark:border-slate-700 text-gray-600 dark:text-gray-400' }}">
                                    <input type="radio" wire:model.live="promoteTargetType" value="contract" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <span class="text-xs font-bold block">Kontrak (PKWT)</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Contract</span>
                                    </div>
                                </label>
                            </div>
                            @error('promoteTargetType') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- 2. Departemen & Posisi Baru Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Penempatan Departemen
                                </label>
                                <select wire:model.live="promoteDepartmentId"
                                    class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition cursor-pointer">
                                    <option value="">-- Tetap di Departemen Saat Ini --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }} {{ $dept->company ? '('.$dept->company->name.')' : '' }}</option>
                                    @endforeach
                                </select>
                                @error('promoteDepartmentId') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                    <span>Pilih Posisi Baku</span>
                                    <span class="text-[10px] text-gray-400 font-normal">Otomatis</span>
                                </label>
                                <select wire:model.live="promotePositionId"
                                    class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition cursor-pointer">
                                    <option value="">-- Pilih Posisi Baru --</option>
                                    @foreach ($positions as $pos)
                                        @if (!$promoteDepartmentId || $promoteDepartmentId == $pos->department_id)
                                            <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->department?->name }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('promotePositionId') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- 3. Jabatan / Posisi Baru -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Jabatan / Posisi Baru <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model="promoteNewPosition" required placeholder="Contoh: Junior Backend Developer"
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition font-medium">
                            <span class="text-[10px] text-gray-400 mt-0.5 block">Sesuaikan nama jabatan resmi karyawan.</span>
                            @error('promoteNewPosition') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Modal Actions -->
                        <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between gap-3">
                            <button type="button" wire:click="closePromoteModal"
                                class="px-4 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 active:scale-95 rounded-xl transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Ya, Angkat Menjadi Karyawan</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

    <!-- Edit Employee Modal -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="closeEditModal" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-slate-800 p-6">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                Edit Status & Data Pegawai
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Ubah status hubungan kerja, jabatan, atau divisi pegawai.</p>
                        </div>
                        <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveEmployee" class="mt-4 space-y-4">
                        
                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model="editFullName" required
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('editFullName') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- NIK (16 Digit) -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                NIK / Nomor Induk KTP (16 Digit) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model="editNik" maxlength="16" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="16 digit angka NIK" required
                                class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('editNik') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status / Kategori Hubungan Kerja (PENTING) -->
                        <div>
                            <label class="block text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-1">
                                Status / Kategori Hubungan Kerja <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model="editEmployeeType" required
                                class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl bg-white dark:bg-slate-800 border border-indigo-300 dark:border-indigo-600/70 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                                <option value="permanent" class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white py-1">Karyawan Tetap (Permanent)</option>
                                <option value="contract" class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white py-1">Karyawan Kontrak (Contract)</option>
                                <option value="internship" class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white py-1">Magang / Internship</option>
                                <option value="probation" class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white py-1">Masa Percobaan (Probation)</option>
                            </select>
                            @error('editEmployeeType') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Departemen / Divisi & Master Posisi Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Departemen / Divisi
                                </label>
                                <select wire:model.live="editDepartmentId"
                                    class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                                    <option value="" class="bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400">-- Pilih Departemen --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" class="bg-white dark:bg-slate-800 text-gray-900 dark:text-white py-1">{{ $dept->name }} {{ $dept->company ? '('.$dept->company->name.')' : '' }}</option>
                                    @endforeach
                                </select>
                                @error('editDepartmentId') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                    <span>Pilih Posisi Baku</span>
                                    <span class="text-[10px] text-gray-400 font-normal">Otomatis</span>
                                </label>
                                <select wire:model.live="editPositionId"
                                    class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition cursor-pointer [color-scheme:light] dark:[color-scheme:dark]">
                                    <option value="">-- Bebas / Khusus --</option>
                                    @foreach ($positions as $pos)
                                        @if (!$editDepartmentId || $editDepartmentId == $pos->department_id)
                                            <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->department?->name }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('editPositionId') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Posisi / Jabatan -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                <span>Jabatan / Posisi Karyawan</span>
                                <span class="text-[10px] text-gray-400 font-normal">Dapat disesuaikan</span>
                            </label>
                            <input type="text" wire:model="editPositionTitle" placeholder="Contoh: Junior Backend Dev"
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('editPositionTitle') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Modal Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" wire:click="closeEditModal"
                                class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-500/20 transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

    <!-- Delete Employee Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="closeDeleteModal" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-slate-800 p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-2xl bg-rose-100 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900/50 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                Hapus Data Pegawai
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 leading-relaxed">
                                Apakah Anda yakin ingin menghapus data pegawai <strong class="text-gray-800 dark:text-slate-200">{{ $deleteName }}</strong>? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 mt-4 border-t border-gray-100 dark:border-slate-800">
                        <button type="button" wire:click="closeDeleteModal"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-xl transition">
                            Batal
                        </button>
                        <button type="button" wire:click="deleteEmployee"
                            class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl shadow-md shadow-rose-500/20 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Ya, Hapus Pegawai</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
