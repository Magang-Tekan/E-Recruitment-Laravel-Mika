<div class="space-y-6" x-data="{ 
    showCreateModal: {{ (isset($errors) && $errors->any() && !old('is_edit')) ? 'true' : 'false' }},
    showEditModal: {{ (isset($errors) && $errors->any() && old('is_edit')) ? 'true' : 'false' }},
    showDeleteModal: false,
    isSubmittingCreate: false,
    isSubmitting: false,
    isSubmittingDelete: false,
    activeEditTab: 'general',
    editData: {
        id: '{{ old('id', '') }}',
        role_id: '{{ old('role_id', '') }}',
        name: '{{ old('name', '') }}',
        tagline: '{{ old('tagline', '') }}',
        website: '{{ old('website', '') }}',
        phone: '{{ old('phone', '') }}',
        email: '{{ old('email', '') }}',
        city: '{{ old('city', '') }}',
        province: '{{ old('province', '') }}',
        postal_code: '{{ old('postal_code', '') }}',
        address: '{{ old('address', '') }}',
        about: '{{ old('about', '') }}',
        vision: '{{ old('vision', '') }}',
        missions: ['', '', ''],
        core_values: [
            { code: 'M', title: 'Menghargai', subtitle: 'Respect', description: '' },
            { code: 'I', title: 'Integritas', subtitle: 'Integrity', description: '' },
            { code: 'K', title: 'Komitmen', subtitle: 'Commitment', description: '' },
            { code: 'A', title: 'Akuntabel', subtitle: 'Accountable', description: '' }
        ],
        logo_url: '',
        is_mika: false
    },
    deleteData: {
        id: '',
        name: ''
    },
    isMikaCompany() {
        let name = (this.editData.name || '').toLowerCase().trim();
        return Boolean(this.editData.is_mika || name.includes('mitra karya analitika') || /\bmika\b/i.test(name));
    },
    openEditModal(company) {
        let name = company.name || '';
        let isMika = Boolean(company.is_mika || name.toLowerCase().includes('mitra karya analitika') || /\bmika\b/i.test(name));
        let missions = Array.isArray(company.missions) && company.missions.length > 0 ? company.missions : ['', '', ''];
        let defaultMikaValues = [
            { code: 'M', title: 'Menghargai', subtitle: 'Respect', description: '' },
            { code: 'I', title: 'Integritas', subtitle: 'Integrity', description: '' },
            { code: 'K', title: 'Komitmen', subtitle: 'Commitment', description: '' },
            { code: 'A', title: 'Akuntabel', subtitle: 'Accountable', description: '' }
        ];
        let coreValues = isMika 
            ? (Array.isArray(company.core_values) && company.core_values.length > 0 ? company.core_values : defaultMikaValues)
            : [];

        this.editData = {
            id: company.id,
            role_id: company.role_id || '',
            name: name,
            is_mika: isMika,
            tagline: company.tagline || '',
            website: company.website || '',
            phone: company.phone || '',
            email: company.email || '',
            city: company.city || '',
            province: company.province || '',
            postal_code: company.postal_code || '',
            address: company.address || '',
            about: company.about || '',
            vision: company.vision || '',
            missions: missions,
            core_values: coreValues,
            logo_url: company.logo || ''
        };
        this.activeEditTab = 'general';
        this.showEditModal = true;
    },
    addMission() {
        this.editData.missions.push('');
    },
    removeMission(index) {
        if (this.editData.missions.length > 1) {
            this.editData.missions.splice(index, 1);
        } else {
            this.editData.missions[0] = '';
        }
    },
    applyMikaTemplate() {
        this.editData.tagline = 'The Best Choice for Your Business Partner';
        this.editData.about = 'PT Mitra Karya Analitika (MIKA) adalah perusahaan terkemuka yang bergerak di bidang distribusi dan penyediaan instrumen laboratorium presisi, peralatan keselamatan dan kesehatan kerja (Health, Safety & Environment / HSE), serta instrumen pemantauan lingkungan (Environmental Monitoring). Berdiri sejak tahun 2014 dan berpusat di Semarang, Jawa Tengah, MIKA telah dipercaya oleh ratusan industri manufaktur, instansi riset, universitas, dan laboratorium pengujian di seluruh penjuru Indonesia sebagai mitra andalan.';
        this.editData.vision = 'Menjadi mitra bisnis terdepan, terpercaya, dan menjadi pilihan utama di Indonesia dalam penyediaan solusi komprehensif peralatan laboratorium, perlindungan keselamatan kerja, dan teknologi pemantauan lingkungan.';
        this.editData.missions = [
            'Menyediakan instrumen laboratorium, alat pelindung keselamatan kerja (HSE), dan sistem monitoring lingkungan berkualitas tinggi berstandar internasional.',
            'Memberikan layanan purna jual, kalibrasi, konsultasi teknis terpadu, dan dukungan profesional yang responsif demi kepuasan mitra bisnis.',
            'Membangun ekosistem kemitraan strategis yang berkelanjutan bersama industri manufaktur, institusi riset, akademisi, dan instansi pemerintah di seluruh Indonesia.'
        ];
        this.editData.core_values = [
            { code: 'M', title: 'Menghargai', subtitle: 'Respect', description: 'Menjunjung tinggi rasa hormat, menghargai keberagaman pandangan, serta membina komunikasi kerja yang inklusif dan harmonis bagi seluruh insan perusahaan dan mitra.' },
            { code: 'I', title: 'Integritas', subtitle: 'Integrity', description: 'Berpikir, berkata, dan bertindak secara jujur, adil, transparan, serta berpegang teguh pada prinsip moral dan kode etik bisnis profesional tanpa kompromi.' },
            { code: 'K', title: 'Komitmen', subtitle: 'Commitment', description: 'Berdedikasi tinggi untuk memberikan pelayanan berkualitas terbaik, menepati janji kesepakatan, dan terus berinovasi menjawab kebutuhan industri secara berkesinambungan.' },
            { code: 'A', title: 'Akuntabel', subtitle: 'Accountable', description: 'Bertanggung jawab penuh atas setiap keputusan, tindakan, dan hasil kerja demi menjaga kepercayaan mitra serta pemangku kepentingan secara transparan.' }
        ];
    },
    openDeleteModal(company) {
        this.deleteData = {
            id: company.id,
            name: company.name
        };
        this.showDeleteModal = true;
    },
    submitEdit(e) {
        e.preventDefault();
        let form = e.target;
        let formData = new FormData(form);
        formData.set('missions', JSON.stringify(this.editData.missions.filter(m => m && m.trim() !== '')));
        if (this.isMikaCompany()) {
            formData.set('core_values', JSON.stringify(this.editData.core_values));
        } else {
            formData.set('core_values', JSON.stringify([]));
        }
        this.isSubmitting = true;
        fetch('/admin/companies/' + this.editData.id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => {
            if (res.ok || res.status === 200) {
                window.location.href = '{{ route("admin.company") }}?updated=true';
            } else {
                return res.json().then(data => { throw data; });
            }
        })
        .catch(err => {
            this.isSubmitting = false;
            alert(err.message || 'Terjadi kesalahan saat memperbarui data perusahaan.');
        });
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

    @if (request('updated'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold">Company berhasil diperbarui</span>
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
    <div
        class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 overflow-hidden shadow-sm rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Perusahaan</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Kelola informasi data perusahaan terdaftar.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari perusahaan / kota..."
                        class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <button @click="showCreateModal = true"
                    class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 w-full sm:w-auto shrink-0 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Perusahaan</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div
        class="relative bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Livewire Loading Overlay -->
        <div wire:loading wire:target="search, previousPage, nextPage, gotoPage" class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center z-10 transition">
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
                    <tr
                        class="border-b border-gray-200 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 text-gray-500 dark:text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="px-6 py-4">Perusahaan</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Website</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-gray-700 dark:text-slate-300">
                    @forelse ($companies as $company)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($company->logo)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($company->logo, 'http') ? $company->logo : asset('storage/' . $company->logo) }}" alt="{{ $company->name }}"
                                            class="w-10 h-10 rounded-xl object-cover border border-gray-200 dark:border-slate-700 shadow-sm shrink-0">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm shrink-0">
                                            {{ strtoupper(substr($company->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span
                                            class="font-bold text-gray-900 dark:text-white block">{{ $company->name }}</span>
                                        <span
                                            class="text-[11px] text-gray-400 dark:text-slate-400 truncate max-w-xs block">{{ $company->address ?? 'Alamat belum diatur' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($company->city || $company->province)
                                    <span
                                        class="location-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ implode(', ', array_filter([$company->city, $company->province])) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($company->website)
                                    <a href="{{ $company->website }}" target="_blank"
                                        class="company-website-link text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                        {{ Str::limit(str_replace(['http://', 'https://'], '', $company->website), 25) }}
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('about') }}" target="_blank"
                                        class="action-view p-1.5 rounded-lg text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                        title="Lihat Halaman Publik (Tentang Kami)">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <button @click="openEditModal({{ json_encode([
                                        'id' => $company->id,
                                        'role_id' => $company->role_id,
                                        'name' => $company->name,
                                        'is_mika' => str_contains(strtolower($company->name), 'mitra karya analitika') || preg_match('/\bmika\b/i', $company->name) === 1,
                                        'tagline' => $company->tagline,
                                        'website' => $company->website,
                                        'phone' => $company->phone,
                                        'email' => $company->email,
                                        'city' => $company->city,
                                        'province' => $company->province,
                                        'postal_code' => $company->postal_code,
                                        'address' => $company->address,
                                        'about' => $company->about,
                                        'vision' => $company->vision,
                                        'missions' => $company->missions ?? [],
                                        'core_values' => $company->core_values ?? [],
                                        'logo' => $company->logo ? (\Illuminate\Support\Str::startsWith($company->logo, 'http') ? $company->logo : asset('storage/' . $company->logo)) : null
                                    ]) }})"
                                        class="action-edit p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
                                        title="Edit Profil Lengkap">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="openDeleteModal({{ json_encode([
                                        'id' => $company->id,
                                        'name' => $company->name
                                     ]) }})"
                                        class="action-delete p-1.5 rounded-lg text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300 dark:text-slate-700" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="text-sm font-medium">Belum ada data perusahaan</span>
                                    <span class="text-xs">Klik tombol "Tambah Perusahaan" untuk menambahkan data
                                        baru.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($companies->hasPages() || $perPage != 10)
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
                @if ($companies->hasPages())
                    <div>
                        {{ $companies->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Create Company Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showCreateModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Card -->
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title">
                            Tambah Perusahaan Baru
                        </h3>
                        <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.company.store') }}" method="POST" enctype="multipart/form-data" @submit="isSubmittingCreate = true" class="mt-4 space-y-4">
                        @csrf
                        <!-- Role Selection -->
                        <div>
                            <label for="role_id" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Role Hak Akses <span class="text-rose-500">*</span>
                            </label>
                            <select name="role_id" id="role_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                <option value="">-- Pilih Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Nama Perusahaan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: PT. Technology Indonesia" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('name')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tagline -->
                        <div>
                            <label for="tagline" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Tagline / Slogan Perusahaan
                            </label>
                            <input type="text" name="tagline" id="tagline" value="{{ old('tagline') }}" placeholder="The Best Choice for Your Business Partner" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                            @error('tagline')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Logo File Upload -->
                        <div>
                            <label for="logo" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Logo Perusahaan (JPG, PNG, JPEG, Max 4MB) <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/jpg" required class="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none file:mr-3 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                            @error('logo')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Website, Email, Phone Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label for="website" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Website
                                </label>
                                <input type="url" name="website" id="website" value="{{ old('website') }}" placeholder="https://example.com" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('website')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Email
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="info@example.com" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('email')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Telepon / WA
                                </label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="0812..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('phone')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- City, Province, Postal Code Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label for="city" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Kota
                                </label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" placeholder="Contoh: Semarang" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('city')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="province" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Provinsi
                                </label>
                                <input type="text" name="province" id="province" value="{{ old('province') }}" placeholder="Contoh: Jawa Tengah" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('province')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="postal_code" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Kode Pos
                                </label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" placeholder="50272" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                @error('postal_code')
                                    <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                Alamat Lengkap
                            </label>
                            <textarea name="address" id="address" rows="2" placeholder="Alamat kantor perusahaan..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-[11px] text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingCreate" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                                <svg x-show="isSubmittingCreate" class="animate-spin w-3.5 h-3.5 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmittingCreate ? 'Menyimpan...' : 'Simpan Perusahaan'"></span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit Company Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showEditModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Card (Spacious Multi-tab Editor for Company Profile) -->
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full border border-gray-200 dark:border-slate-800">
                
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800 gap-3">
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2" id="modal-title-edit">
                                <span>Kelola Profil & Halaman Tentang Kami</span>
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-semibold" x-text="editData.name"></span>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5" x-text="isMikaCompany() ? 'Kelola identitas perusahaan, visi misi, dan nilai budaya MIKA secara dinamis.' : 'Kelola identitas perusahaan serta visi misi secara dinamis.'"></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" x-show="isMikaCompany()" @click="applyMikaTemplate()" class="px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition flex items-center gap-1.5" title="Muat draf standar profil PT Mitra Karya Analitika">
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                <span>Isi Template MIKA</span>
                            </button>
                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <div class="mt-4 flex flex-wrap gap-2 border-b border-gray-100 dark:border-slate-800 pb-3">
                        <button type="button" @click="activeEditTab = 'general'" :class="activeEditTab === 'general' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700'" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>1. Info & Kontak</span>
                        </button>
                        <button type="button" @click="activeEditTab = 'profile'" :class="activeEditTab === 'profile' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700'" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>2. Profil & Tagline</span>
                        </button>
                        <button type="button" @click="activeEditTab = 'vision'" :class="activeEditTab === 'vision' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700'" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>3. Visi & Misi</span>
                        </button>
                        <button type="button" x-show="isMikaCompany()" @click="activeEditTab = 'values'" :class="activeEditTab === 'values' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700'" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            <span>4. Nilai Budaya (MIKA)</span>
                        </button>
                    </div>

                    <form @submit="submitEdit($event)" class="mt-4">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="is_edit" value="1">
                        <input type="hidden" name="id" x-model="editData.id">

                        <!-- TAB 1: INFORMASI UMUM & KONTAK -->
                        <div x-show="activeEditTab === 'general'" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Role Selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Role Hak Akses
                                    </label>
                                    <select name="role_id" x-model="editData.role_id" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                        <option value="">-- Pilih Role --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Name -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Nama Perusahaan <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="editData.name" required placeholder="PT Mitra Karya Analitika" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                            </div>

                            <!-- Logo File Upload -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Logo Perusahaan
                                </label>
                                <div class="flex items-center gap-3 mb-2" x-show="editData.logo_url">
                                    <img :src="editData.logo_url" class="w-10 h-10 rounded-xl object-contain border border-gray-200 dark:border-slate-700 shadow-sm p-1 bg-white">
                                    <span class="text-[11px] text-gray-400">Logo saat ini aktif</span>
                                </div>
                                <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg" class="w-full px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none file:mr-3 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                            </div>

                            <!-- Website, Email, Phone Row -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Website Resmi
                                    </label>
                                    <input type="url" name="website" x-model="editData.website" placeholder="https://mikacares.co.id" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Email Resmi
                                    </label>
                                    <input type="email" name="email" x-model="editData.email" placeholder="info@mikacares.co.id" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Nomor Telepon / WhatsApp
                                    </label>
                                    <input type="text" name="phone" x-model="editData.phone" placeholder="081225588888" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                            </div>

                            <!-- City, Province, Postal Code -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Kota
                                    </label>
                                    <input type="text" name="city" x-model="editData.city" placeholder="Semarang" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Provinsi
                                    </label>
                                    <input type="text" name="province" x-model="editData.province" placeholder="Jawa Tengah" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                        Kode Pos
                                    </label>
                                    <input type="text" name="postal_code" x-model="editData.postal_code" placeholder="50272" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                </div>
                            </div>

                            <!-- Address -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Alamat Lengkap Kantor
                                </label>
                                <textarea name="address" rows="2" x-model="editData.address" placeholder="Jl. Klipang Ruko Amsterdam No.9D, Sendangmulyo..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"></textarea>
                            </div>
                        </div>

                        <!-- TAB 2: PROFIL & TAGLINE -->
                        <div x-show="activeEditTab === 'profile'" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Tagline Slogan Perusahaan
                                </label>
                                <input type="text" name="tagline" x-model="editData.tagline" placeholder="The Best Choice for Your Business Partner" class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                                <p class="text-[11px] text-gray-400 mt-1">Ditampilkan pada bagian Hero & Header halaman Tentang Kami.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Deskripsi Profil / Tentang Perusahaan
                                </label>
                                <textarea name="about" rows="6" x-model="editData.about" placeholder="Ceritakan sejarah pendirian, fokus bidang usaha (alat lab, HSE, environmental), dan komitmen mutu perusahaan..." class="w-full px-3 py-2.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition leading-relaxed"></textarea>
                                <p class="text-[11px] text-gray-400 mt-1">Narasi utama pada section Profil Singkat halaman Tentang Kami.</p>
                            </div>
                        </div>

                        <!-- TAB 3: VISI & MISI -->
                        <div x-show="activeEditTab === 'vision'" class="space-y-5 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                            <!-- Vision -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">
                                    Pernyataan Visi Utama
                                </label>
                                <textarea name="vision" rows="3" x-model="editData.vision" placeholder="Menjadi mitra bisnis terdepan, terpercaya, dan menjadi pilihan utama di Indonesia..." class="w-full px-3 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"></textarea>
                            </div>

                            <!-- Missions Dynamic List -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">
                                        Daftar Butir Misi Berkelanjutan
                                    </label>
                                    <button type="button" @click="addMission()" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>Tambah Butir Misi</span>
                                    </button>
                                </div>

                                <div class="space-y-2.5">
                                    <template x-for="(mission, index) in editData.missions" :key="index">
                                        <div class="flex items-start gap-2">
                                            <span class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-bold text-xs flex items-center justify-center shrink-0 mt-1" x-text="index + 1"></span>
                                            <textarea rows="2" x-model="editData.missions[index]" placeholder="Tuliskan butir misi..." class="flex-1 px-3 py-1.5 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"></textarea>
                                            <button type="button" @click="removeMission(index)" class="p-1.5 text-rose-500 hover:text-rose-700 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30 transition mt-1" title="Hapus butir">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: NILAI PERUSAHAAN (CORE VALUES MIKA) -->
                        <div x-show="activeEditTab === 'values' && isMikaCompany()" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                Kelola 4 pilar budaya kerja MIKA (Menghargai, Integritas, Komitmen, Akuntabel).
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="(val, idx) in editData.core_values" :key="idx">
                                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-slate-800/60 border border-gray-200 dark:border-slate-700 space-y-3">
                                        <div class="flex items-center justify-between pb-2 border-b border-gray-200 dark:border-slate-700">
                                            <div class="flex items-center gap-2">
                                                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-black text-sm flex items-center justify-center shrink-0" x-text="val.code || ('P' + (idx+1))"></span>
                                                <span class="text-xs font-bold text-gray-900 dark:text-white" x-text="'Pilar ' + (idx + 1)"></span>
                                            </div>
                                            <input type="text" x-model="val.code" placeholder="Huruf" class="w-16 px-2 py-1 text-center text-xs font-black uppercase rounded-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200">
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-slate-400 mb-0.5">Judul (Indonesia)</label>
                                                <input type="text" x-model="val.title" placeholder="Contoh: Menghargai" class="w-full px-2.5 py-1.5 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-slate-400 mb-0.5">Subtitle (English)</label>
                                                <input type="text" x-model="val.subtitle" placeholder="Contoh: Respect" class="w-full px-2.5 py-1.5 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-slate-400 mb-0.5">Deskripsi Penjelasan</label>
                                            <textarea rows="3" x-model="val.description" placeholder="Penjelasan makna dan penerapan pilar..." class="w-full px-2.5 py-1.5 text-xs rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="flex items-center justify-between pt-5 mt-4 border-t border-gray-100 dark:border-slate-800">
                            <span class="text-[11px] text-gray-400">Pastikan data yang diisi telah sesuai sebelum menyimpan.</span>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                                    Batal
                                </button>
                                <button type="submit" :disabled="isSubmitting" class="px-5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 rounded-xl transition flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                                    <svg x-show="isSubmitting" class="animate-spin w-3.5 h-3.5 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-delete" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDeleteModal = false" class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Card -->
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
                                Konfirmasi Hapus Perusahaan
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs text-gray-600 dark:text-slate-300">
                            Apakah Anda yakin ingin menghapus data perusahaan <strong class="text-gray-900 dark:text-white font-semibold" x-text="deleteData.name"></strong>?
                        </p>
                    </div>

                    <form :action="'/admin/companies/' + deleteData.id" method="POST" @submit="isSubmittingDelete = true" class="mt-6 flex items-center justify-end gap-3">
                        @csrf
                        @method('DELETE')

                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmittingDelete" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-md shadow-rose-500/20 transition flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                            <svg x-show="isSubmittingDelete" class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmittingDelete ? 'Menghapus...' : 'Hapus Perusahaan'"></span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
