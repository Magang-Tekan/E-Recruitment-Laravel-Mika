<div class="space-y-6">

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

    <!-- Card 1: Master Passkey Global -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm p-6 space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-gray-100 dark:border-slate-800">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 flex items-center justify-center text-emerald-600 dark:text-[#93F514] shrink-0 shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>Master Passkey Registrasi Karyawan</span>
                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800/60">
                            Global Default
                        </span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                        Token pendaftaran utama yang berlaku untuk semua perusahaan yang tidak memiliki passkey khusus.
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-slate-400 bg-gray-50 dark:bg-slate-800/60 px-3.5 py-2 rounded-xl border border-gray-100 dark:border-slate-700/60">
                <span>Saat ini aktif:</span>
                <span class="font-mono font-bold text-emerald-600 dark:text-[#93F514] tracking-wider">{{ $activeMasterPasskey }}</span>
            </div>
        </div>

        <form wire:submit.prevent="saveGlobalPasskey" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-8 space-y-1.5" x-data="{ showPasskey: true, copied: false }">
                    <div class="flex items-center justify-between">
                        <label for="globalPasskey" class="text-xs font-semibold text-gray-700 dark:text-slate-300">
                            Ubah Master Passkey Global <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" 
                                @click="
                                    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                                    let rand = '';
                                    for (let i = 0; i < 4; i++) rand += chars.charAt(Math.floor(Math.random() * chars.length));
                                    const newKey = 'MIKA' + (new Date().getFullYear()) + '-' + rand;
                                    $wire.set('globalPasskey', newKey);
                                    showPasskey = true;
                                "
                                class="text-[11px] font-semibold text-emerald-600 dark:text-[#93F514] hover:underline flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Acak Kode Otomatis</span>
                        </button>
                    </div>

                    <div class="relative">
                        <input :type="showPasskey ? 'text' : 'password'" 
                               wire:model="globalPasskey" 
                               id="globalPasskey" 
                               placeholder="Contoh: MIKA2026"
                               class="w-full pl-3.5 pr-20 py-2.5 text-xs font-mono tracking-wider rounded-xl bg-gray-50 dark:bg-slate-800 border @error('globalPasskey') border-rose-500 @else border-gray-200 dark:border-slate-700 @enderror text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">

                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center gap-1">
                            <!-- Toggle Visibility (0ms Instant Alpine.js) -->
                            <button type="button" 
                                    @click="showPasskey = !showPasskey" 
                                    class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition rounded-lg cursor-pointer focus:outline-none" 
                                    :title="showPasskey ? 'Sembunyikan kode passkey' : 'Tampilkan kode passkey'">
                                <!-- Eye Open (Ditampilkan saat sandi tersembunyi, klik untuk menampilkan) -->
                                <svg x-show="!showPasskey" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Eye Slashed (Ditampilkan saat sandi sedang terlihat, klik untuk menyembunyikan) -->
                                <svg x-show="showPasskey" x-cloak class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>

                            <!-- Copy Button -->
                            <button type="button" 
                                    @click="navigator.clipboard.writeText(document.getElementById('globalPasskey').value); copied = true; setTimeout(() => copied = false, 2000)" 
                                    class="p-1.5 text-gray-400 hover:text-emerald-600 dark:hover:text-[#93F514] transition rounded-lg cursor-pointer" 
                                    title="Salin Passkey">
                                <svg x-show="!copied" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                <svg x-show="copied" x-cloak class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('globalPasskey')
                        <p class="text-[11px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-4 flex items-center">
                    <button type="submit" wire:loading.attr="disabled" class="w-full py-2.5 px-4 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer active:scale-95 disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveGlobalPasskey">Simpan Master Passkey</span>
                        <span wire:loading wire:target="saveGlobalPasskey" class="flex items-center gap-1.5">
                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Card 2: Passkey Khusus per Perusahaan -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm space-y-4 p-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Passkey Khusus per Perusahaan</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                    Atur token registrasi unik jika ada unit/cabang perusahaan yang memerlukan kode pendaftaran tersendiri.
                </p>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="pt-3 border-t border-gray-100 dark:border-slate-800">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6 relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari perusahaan, kota, atau token passkey..."
                        class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="sm:col-span-4">
                    <select wire:model.live="filterStatus" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                        <option value="">Semua Status Passkey</option>
                        <option value="custom">Memiliki Passkey Khusus</option>
                        <option value="default">Mengikuti Master Global</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <select wire:model.live="perPage" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                        <option value="10">10 per hal</option>
                        <option value="25">25 per hal</option>
                        <option value="50">50 per hal</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-slate-800">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800 bg-gray-50/70 dark:bg-slate-800/60 text-gray-500 dark:text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="px-5 py-3.5">Perusahaan</th>
                        <th class="px-5 py-3.5">Lokasi</th>
                        <th class="px-5 py-3.5">Status Passkey Registrasi</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800 text-gray-700 dark:text-slate-300">
                    @forelse ($companies as $comp)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if ($comp->logo)
                                        <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 p-1 flex items-center justify-center shadow-xs shrink-0 overflow-hidden">
                                            <img src="{{ \Illuminate\Support\Str::startsWith($comp->logo, 'http') ? $comp->logo : asset('storage/' . $comp->logo) }}" alt="{{ $comp->name }}"
                                                class="w-full h-full object-contain">
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-xs shadow-xs shrink-0">
                                            {{ strtoupper(substr($comp->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-bold text-gray-900 dark:text-white block">{{ $comp->name }}</span>
                                        <span class="text-[11px] text-gray-400 dark:text-slate-400 truncate max-w-xs block">{{ $comp->address ?? 'Alamat belum diatur' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-[11px] text-gray-600 dark:text-slate-300">
                                    {{ implode(', ', array_filter([$comp->city, $comp->province])) ?: '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if (!empty($comp->registration_passkey))
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800/70 font-mono font-semibold text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        <span>{{ $comp->registration_passkey }}</span>
                                        <span class="text-[9px] uppercase px-1 py-0.2 rounded bg-emerald-200 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 font-sans font-bold">Khusus</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 text-xs font-medium">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <span>Mengikuti Master: <strong class="font-mono text-gray-800 dark:text-slate-200">{{ $activeMasterPasskey }}</strong></span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <button type="button" wire:click="openCompanyModal({{ $comp->id }})"
                                    class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-[#93F514] border border-emerald-200 dark:border-emerald-800/80 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition inline-flex items-center gap-1.5 cursor-pointer active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Atur Passkey</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-xs">
                                Tidak ada data perusahaan yang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($companies->hasPages())
            <div class="pt-2">
                {{ $companies->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Atur Passkey Perusahaan -->
    @if ($showCompanyModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-company-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div wire:click="closeCompanyModal" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-slate-800 p-6 space-y-5">
                    
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-company-title">
                                Atur Passkey Perusahaan
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5 font-semibold text-emerald-600 dark:text-[#93F514]">
                                {{ $editingCompanyName }}
                            </p>
                        </div>
                        <button type="button" wire:click="closeCompanyModal" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveCompanyPasskey" class="space-y-4" x-data="{ showCompKey: true }">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="companyPasskey" class="text-xs font-semibold text-gray-700 dark:text-slate-300">
                                    Passkey Khusus Karyawan
                                </label>
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            @click="
                                                const prefix = '{{ strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $editingCompanyName), 0, 4)) ?: 'CORP' }}';
                                                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                                                let rand = '';
                                                for (let i = 0; i < 4; i++) rand += chars.charAt(Math.floor(Math.random() * chars.length));
                                                $wire.set('companyPasskey', prefix + '-' + rand);
                                                showCompKey = true;
                                            "
                                            class="text-[11px] font-semibold text-emerald-600 dark:text-[#93F514] hover:underline cursor-pointer">
                                        Acak Kode
                                    </button>
                                    @if (!empty($companyPasskey))
                                        <span class="text-gray-300">|</span>
                                        <button type="button" wire:click="useGlobalDefault" class="text-[11px] font-semibold text-rose-500 hover:underline cursor-pointer">
                                            Reset ke Global
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="relative">
                                <input :type="showCompKey ? 'text' : 'password'" 
                                       wire:model="companyPasskey" 
                                       id="companyPasskey" 
                                       placeholder="Kosongkan jika mengikuti Master Passkey Global"
                                       class="w-full pl-3.5 pr-10 py-2.5 text-xs font-mono tracking-wider rounded-xl bg-gray-50 dark:bg-slate-800 border @error('companyPasskey') border-rose-500 @else border-gray-200 dark:border-slate-700 @enderror text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">

                                <button type="button" @click="showCompKey = !showCompKey" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition focus:outline-none cursor-pointer">
                                    <svg x-show="!showCompKey" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showCompKey" x-cloak class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                                Jika dikosongkan, pendaftar yang memilih <strong>{{ $editingCompanyName }}</strong> akan diverifikasi menggunakan Master Passkey Global (<span class="font-mono">{{ $activeMasterPasskey }}</span>).
                            </p>
                            @error('companyPasskey')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" wire:click="closeCompanyModal" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition flex items-center gap-2 cursor-pointer active:scale-95 disabled:opacity-60">
                                <span wire:loading.remove wire:target="saveCompanyPasskey">Simpan Passkey</span>
                                <span wire:loading wire:target="saveCompanyPasskey" class="flex items-center gap-1.5">
                                    <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Menyimpan...</span>
                                </span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

</div>
