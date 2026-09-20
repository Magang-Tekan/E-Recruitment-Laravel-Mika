<div class="space-y-6" x-data="{
    showCropperModal: false,
    cropper: null,
    
    initCropper(imageElement) {
        if (this.cropper) {
            this.cropper.destroy();
        }
        this.cropper = new Cropper(imageElement, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 0.85,
            dragMode: 'move',
            background: false,
            responsive: true,
            restore: true,
            checkCrossOrigin: false,
        });
    },

    onFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const cropperImage = $refs.cropperImage;
            cropperImage.src = e.target.result;
            this.showCropperModal = true;

            this.$nextTick(() => {
                this.initCropper(cropperImage);
            });
        };
        reader.readAsDataURL(file);
        event.target.value = '';
    },

    applyCrop() {
        if (!this.cropper) return;
        const canvas = this.cropper.getCroppedCanvas({
            width: 450,
            height: 450,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        const croppedBase64 = canvas.toDataURL('image/jpeg', 0.92);
        $wire.set('cropped_photo_base64', croppedBase64);

        this.showCropperModal = false;
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }
    },

    cancelCrop() {
        this.showCropperModal = false;
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }
    },

    zoomIn() { if (this.cropper) this.cropper.zoom(0.1); },
    zoomOut() { if (this.cropper) this.cropper.zoom(-0.1); },
    rotateLeft() { if (this.cropper) this.cropper.rotate(-90); },
    rotateRight() { if (this.cropper) this.cropper.rotate(90); }
}">

    <!-- Load Cropper.js CDN Assets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <!-- Header Card (Clean Enterprise Blueprint) -->
    <div class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Data Pribadi</h2>
            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">* Isilah data dibawah dengan sebenarnya Anda.</p>
        </div>
    </div>

    <!-- Success Flash Alert -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-xl shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm font-medium">{{ session('message') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Main Form Card -->
    <div class="bg-white dark:bg-[#0D1527] p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54]">
        <form wire:submit.prevent="save" class="space-y-6">

            <!-- Photo & Basic Info Row -->
            <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-slate-200/80 dark:border-[#1D2E54]">
                <div class="relative group shrink-0">
                    <div class="w-28 h-28 rounded-full overflow-hidden bg-slate-100 dark:bg-[#14203A] border-2 border-emerald-500 dark:border-[#93F514] shadow-md flex items-center justify-center relative">
                        @if ($cropped_photo_base64)
                            <img src="{{ $cropped_photo_base64 }}" class="w-full h-full object-cover">
                        @elseif ($current_photo_url)
                            <img src="{{ $current_photo_url }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-14 h-14 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        @endif
                    </div>
                </div>

                <div class="flex-1 text-center sm:text-left space-y-2">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Foto Profil / Pas Foto <span class="text-red-500">*</span></label>
                    
                    <div class="flex flex-wrap items-center gap-3 justify-center sm:justify-start">
                        <label class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl cursor-pointer transition border border-slate-200/80 dark:border-[#1D2E54] flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Pilih & Atur Foto</span>
                            <input type="file" @change="onFileSelect" accept="image/*" class="hidden">
                        </label>
                    </div>

                    <p class="text-[11px] text-slate-400 dark:text-slate-500">* Anda dapat menggeser, memperbesar, dan memotong posisi foto sebelum disimpan.</p>
                    @error('photo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Form Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- NIK -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nik" placeholder="Masukkan 16 digit NIK"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('nik') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="full_name" placeholder="Nama lengkap sesuai KTP"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('full_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select wire:model="gender"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    @error('gender') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Nomor Telepon / WA -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="phone" placeholder="Contoh: 081234567890"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('phone') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="birth_place" placeholder="Kota tempat lahir"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('birth_place') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="birth_date"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition cursor-pointer dark:[color-scheme:dark]">
                    @error('birth_date') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- NPWP -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">NPWP</label>
                    <input type="text" wire:model="npwp" placeholder="Nomor NPWP (jika ada)"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('npwp') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Autocomplete Wilayah Domisili (Kota/Kabupaten & Provinsi) -->
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{
                    provinces: [],
                    regencies: [],
                    filteredRegencies: [],
                    filteredProvinces: [],
                    showCityDropdown: false,
                    showProvinceDropdown: false,
                    loadingCities: false,

                    async init() {
                        try {
                            const provRes = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                            if (provRes.ok) {
                                this.provinces = await provRes.json();
                            }
                        } catch (err) {
                            console.warn('Gagal memuat provinsi:', err);
                        }
                    },

                    async fetchRegenciesForProvince(provId) {
                        try {
                            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provId}.json`);
                            if (res.ok) {
                                const data = await res.json();
                                data.forEach(r => {
                                    if (!this.regencies.some(existing => existing.id == r.id)) {
                                        this.regencies.push(r);
                                    }
                                });
                            }
                        } catch (e) {}
                    },

                    async loadAllRegencies() {
                        if (this.regencies.length > 0 || this.loadingCities) return;
                        this.loadingCities = true;
                        try {
                            if (this.provinces.length === 0) {
                                const provRes = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                                if (provRes.ok) this.provinces = await provRes.json();
                            }
                            const fetchPromises = this.provinces.map(p => 
                                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${p.id}.json`)
                                    .then(r => r.ok ? r.json() : [])
                                    .catch(() => [])
                            );
                            const results = await Promise.all(fetchPromises);
                            this.regencies = results.flat();
                        } catch (e) {
                            console.warn('Gagal memuat kabupaten/kota:', e);
                        } finally {
                            this.loadingCities = false;
                        }
                    },

                    async searchRegency() {
                        this.showCityDropdown = true;
                        if (this.regencies.length === 0 && !this.loadingCities) {
                            await this.loadAllRegencies();
                        }
                        const val = $wire.city || '';
                        if (!val.trim()) {
                            this.filteredRegencies = this.regencies.slice(0, 20);
                            return;
                        }
                        const q = val.toLowerCase();
                        this.filteredRegencies = this.regencies
                            .filter(r => r.name.toLowerCase().includes(q))
                            .slice(0, 25);
                    },

                    selectRegency(reg) {
                        $wire.city = reg.name;
                        this.showCityDropdown = false;
                        
                        if (reg.province_id) {
                            const prov = this.provinces.find(p => p.id == reg.province_id);
                            if (prov) {
                                $wire.province = prov.name;
                            }
                        }
                    },

                    searchProv() {
                        this.showProvinceDropdown = true;
                        const val = $wire.province || '';
                        if (!val.trim()) {
                            this.filteredProvinces = this.provinces;
                            return;
                        }
                        const q = val.toLowerCase();
                        this.filteredProvinces = this.provinces.filter(p => p.name.toLowerCase().includes(q));
                    },

                    async selectProv(prov) {
                        $wire.province = prov.name;
                        this.showProvinceDropdown = false;
                        await this.fetchRegenciesForProvince(prov.id);
                    }
                }" @click.outside="showCityDropdown = false; showProvinceDropdown = false">

                    <!-- Kota / Kabupaten Dropdown -->
                    <div class="relative">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Kota / Kabupaten Domisili <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                x-model="$wire.city"
                                @focus="searchRegency()"
                                @input="searchRegency()"
                                placeholder="Ketik untuk mencari Kota / Kabupaten..."
                                class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List Kota/Kabupaten -->
                        <div x-show="showCityDropdown && filteredRegencies.length > 0" 
                            x-transition
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-xl max-h-60 overflow-y-auto"
                            style="display: none;">
                            <template x-for="reg in filteredRegencies" :key="reg.id">
                                <button type="button" 
                                    @click="selectRegency(reg)"
                                    class="w-full text-left px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] transition flex items-center justify-between border-b border-slate-100 dark:border-[#1D2E54]/50 last:border-0">
                                    <span x-text="reg.name"></span>
                                </button>
                            </template>
                        </div>
                        @error('city') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Provinsi Dropdown -->
                    <div class="relative">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Provinsi Domisili <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                x-model="$wire.province"
                                @focus="searchProv()"
                                @input="searchProv()"
                                placeholder="Ketik untuk mencari Provinsi..."
                                class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List Provinsi -->
                        <div x-show="showProvinceDropdown && filteredProvinces.length > 0" 
                            x-transition
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-xl max-h-60 overflow-y-auto"
                            style="display: none;">
                            <template x-for="prov in filteredProvinces" :key="prov.id">
                                <button type="button" 
                                    @click="selectProv(prov)"
                                    class="w-full text-left px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] transition flex items-center justify-between border-b border-slate-100 dark:border-[#1D2E54]/50 last:border-0">
                                    <span x-text="prov.name"></span>
                                </button>
                            </template>
                        </div>
                        @error('province') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                </div>

                <!-- Alamat Lengkap -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea wire:model="address" rows="3" placeholder="Alamat lengkap tempat tinggal saat ini"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition"></textarea>
                    @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tentang Saya -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Tentang Saya / Ringkasan Diri</label>
                    <textarea wire:model="about_me" rows="4" placeholder="Tuliskan gambaran singkat mengenai latar belakang, motivasi, dan keahlian Anda..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition"></textarea>
                    @error('about_me') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Upload Dokumen CV / Resume -->
                <div class="md:col-span-2 p-5 bg-slate-50/80 dark:bg-[#14203A]/60 border border-slate-200/80 dark:border-[#1D2E54] rounded-2xl space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Dokumen CV / Resume (PDF / DOCX) <span class="text-red-500">*</span></label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Unggah file CV terbaru Anda (Maksimal 5MB, Format PDF, DOC, atau DOCX)</p>
                        </div>
                        <a href="{{ route('profile.cv.preview') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-[#93F514] hover:underline">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Preview CV ATS System</span>
                        </a>
                    </div>

                    <!-- File Upload Input / Status -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <label class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl cursor-pointer shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition flex items-center gap-2 shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span>{{ $current_cv_path ? 'Ganti File CV' : 'Pilih & Unggah CV' }}</span>
                            <input type="file" wire:model.live="cv_file" accept=".pdf,.doc,.docx" class="hidden">
                        </label>

                        @if ($current_cv_url)
                            <div class="flex items-center gap-3 bg-white dark:bg-[#0D1527] px-4 py-2 rounded-xl border border-slate-200 dark:border-[#1D2E54] text-xs w-full sm:w-auto justify-between">
                                <a href="{{ $current_cv_url }}" target="_blank" class="flex items-center gap-2 text-emerald-600 dark:text-[#93F514] font-semibold hover:underline truncate">
                                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <span class="truncate">File CV Tersimpan</span>
                                </a>
                                <button type="button" wire:click="deleteCv" wire:confirm="Apakah Anda yakin ingin menghapus file CV ini?" class="text-red-500 hover:text-red-700 font-medium text-xs ml-2">
                                    Hapus
                                </button>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 dark:text-slate-500 italic">Belum ada file CV yang diunggah.</span>
                        @endif
                    </div>

                    <!-- Progress indicator -->
                    <div wire:loading wire:target="cv_file" class="text-xs text-emerald-600 dark:text-[#93F514] font-medium flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mengunggah file...
                    </div>

                    @error('cv_file') <span class="text-xs text-red-500 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                <button type="submit" wire:loading.attr="disabled"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-sm font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition duration-150 ease-in-out flex items-center gap-2 active:scale-95">
                    <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Image Cropper Modal -->
    <div x-show="showCropperModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" @click="cancelCrop"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Body -->
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200 dark:border-[#1D2E54]">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200/80 dark:border-[#1D2E54]">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 0L4 4m5.121 5.121L4 19" />
                        </svg>
                        Atur & Potong Foto Profil
                    </h3>
                    <button @click="cancelCrop" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Content / Cropper Area -->
                <div class="p-6 bg-slate-950 flex justify-center items-center min-h-[320px] max-h-[420px] overflow-hidden">
                    <img x-ref="cropperImage" class="max-w-full max-h-[380px] block">
                </div>

                <!-- Toolbar Controls -->
                <div class="flex items-center justify-center gap-3 py-3 bg-slate-50 dark:bg-[#14203A] border-t border-slate-200/80 dark:border-[#1D2E54]">
                    <button type="button" @click="zoomIn" title="Perbesar" class="p-2 rounded-lg bg-white dark:bg-[#0D1527] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1D2E54] shadow-sm border border-slate-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" /></svg>
                    </button>
                    <button type="button" @click="zoomOut" title="Perkecil" class="p-2 rounded-lg bg-white dark:bg-[#0D1527] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1D2E54] shadow-sm border border-slate-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" /></svg>
                    </button>
                    <button type="button" @click="rotateLeft" title="Putar Kiri" class="p-2 rounded-lg bg-white dark:bg-[#0D1527] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1D2E54] shadow-sm border border-slate-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                    </button>
                    <button type="button" @click="rotateRight" title="Putar Kanan" class="p-2 rounded-lg bg-white dark:bg-[#0D1527] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1D2E54] shadow-sm border border-slate-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6" /></svg>
                    </button>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527]">
                    <button type="button" @click="cancelCrop"
                        class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#14203A] hover:bg-slate-200 dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                        Batal
                    </button>
                    <button type="button" @click="applyCrop"
                        class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition flex items-center gap-1.5 active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Gunakan Foto Ini</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
