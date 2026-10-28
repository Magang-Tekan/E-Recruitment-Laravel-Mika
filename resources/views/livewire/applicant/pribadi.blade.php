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
    <div
        class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-emerald-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                {{ __('Data Pribadi') }}</h2>
            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">
                {{ __('* Isilah data dibawah dengan sebenarnya Anda.') }}</p>
        </div>
    </div>



    <!-- Main Form Card -->
    <div
        class="bg-white dark:bg-[#0D1527] p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54]">
        <form wire:submit.prevent="save" class="space-y-6">

            <!-- Photo & Basic Info Row -->
            @php
                $applicantDisplayName = $full_name ?: auth()->user()->name ?? 'Pelamar';
                $applicantInitial = strtoupper(mb_substr($applicantDisplayName, 0, 1, 'UTF-8')) ?: 'P';
                $googleColors = [
                    '#1A73E8',
                    '#D93025',
                    '#188038',
                    '#E37400',
                    '#8E24AA',
                    '#00897B',
                    '#D81B60',
                    '#3949AB',
                    '#F4511E',
                    '#00ACC1',
                ];
                $applicantBgColor = $googleColors[abs(crc32($applicantDisplayName)) % count($googleColors)];
            @endphp
            <div
                class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-slate-200/80 dark:border-[#1D2E54]">
                <div class="relative group shrink-0">
                    <div
                        class="w-28 h-28 rounded-full overflow-hidden border-2 border-emerald-500 dark:border-[#93F514] shadow-md flex items-center justify-center relative bg-slate-100 dark:bg-[#14203A]">
                        @if ($cropped_photo_base64)
                            <img src="{{ $cropped_photo_base64 }}" class="w-full h-full object-cover">
                        @elseif ($current_photo_url)
                            <img src="{{ $current_photo_url }}" alt="{{ $applicantDisplayName }}"
                                referrerpolicy="no-referrer" class="w-full h-full object-cover"
                                onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full items-center justify-center font-black text-white text-3xl select-none shadow-inner"
                                style="background-color: {{ $applicantBgColor }};">
                                <span>{{ $applicantInitial }}</span>
                            </div>
                        @else
                            {{-- Tampilan bawaan saat daftar manual / belum ada foto profil (Initial avatar cerah & elegan ala Google Material) --}}
                            <div class="w-full h-full flex items-center justify-center font-black text-white text-3xl select-none shadow-inner transition-transform group-hover:scale-105"
                                style="background-color: {{ $applicantBgColor }};">
                                <span>{{ $applicantInitial }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex-1 text-center sm:text-left space-y-2">
                    <label
                        class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('Foto Profil / Pas Foto') }}
                        <span class="text-red-500">*</span></label>

                    <div class="flex flex-wrap items-center gap-3 justify-center sm:justify-start">
                        <label
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl cursor-pointer transition border border-slate-200/80 dark:border-[#1D2E54] flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ __('Pilih & Atur Foto') }}</span>
                            <input type="file" @change="onFileSelect" accept="image/*" class="hidden">
                        </label>
                    </div>

                    <p class="text-[11px] text-slate-400 dark:text-slate-500">
                        {{ __('* Anda dapat menggeser, memperbesar, dan memotong posisi foto sebelum disimpan.') }}</p>
                    @error('photo')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Form Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- NIK -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('NIK (Nomor Induk Kependudukan)') }}
                        <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nik" placeholder="{{ __('Masukkan 16 digit NIK') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('nik')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Nama Lengkap') }}
                        <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="full_name" placeholder="{{ __('Nama lengkap sesuai KTP') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('full_name')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Jenis Kelamin') }}
                        <span class="text-red-500">*</span></label>
                    <select wire:model="gender"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                        <option value="">{{ __('-- Pilih Jenis Kelamin --') }}</option>
                        <option value="Laki-laki">{{ __('Laki-laki') }}</option>
                        <option value="Perempuan">{{ __('Perempuan') }}</option>
                    </select>
                    @error('gender')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nomor Telepon / WA -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('No. Telepon / WhatsApp') }}
                        <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="phone" placeholder="{{ __('Contoh: 081234567890') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('phone')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tempat Lahir -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Tempat Lahir') }}
                        <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="birth_place" placeholder="{{ __('Kota tempat lahir') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('birth_place')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Tanggal Lahir') }}
                        <span class="text-red-500">*</span></label>
                    <input type="date" wire:model.blur="birth_date"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition cursor-pointer dark:[color-scheme:dark]">
                    @error('birth_date')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Agama -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Agama') }}</label>
                    <select wire:model="religion"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                        <option value="" class="dark:bg-[#14203A] dark:text-white">
                            {{ __('-- Pilih Agama --') }}</option>
                        <option value="Islam" class="dark:bg-[#14203A] dark:text-white">{{ __('Islam') }}</option>
                        <option value="Kristen Protestan" class="dark:bg-[#14203A] dark:text-white">
                            {{ __('Kristen Protestan') }}</option>
                        <option value="Katolik" class="dark:bg-[#14203A] dark:text-white">{{ __('Katolik') }}
                        </option>
                        <option value="Hindu" class="dark:bg-[#14203A] dark:text-white">{{ __('Hindu') }}</option>
                        <option value="Buddha" class="dark:bg-[#14203A] dark:text-white">{{ __('Buddha') }}</option>
                        <option value="Konghucu" class="dark:bg-[#14203A] dark:text-white">{{ __('Konghucu') }}
                        </option>
                        <option value="Lainnya" class="dark:bg-[#14203A] dark:text-white">{{ __('Lainnya') }}
                        </option>
                    </select>
                    @error('religion')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Golongan Darah -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Golongan Darah') }}</label>
                    <select wire:model="blood_type"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                        <option value="" class="dark:bg-[#14203A] dark:text-white">
                            {{ __('-- Pilih Golongan Darah --') }}</option>
                        <option value="A" class="dark:bg-[#14203A] dark:text-white">A</option>
                        <option value="B" class="dark:bg-[#14203A] dark:text-white">B</option>
                        <option value="AB" class="dark:bg-[#14203A] dark:text-white">AB</option>
                        <option value="O" class="dark:bg-[#14203A] dark:text-white">O</option>
                    </select>
                    @error('blood_type')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Suku -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Suku') }}</label>
                    <input type="text" wire:model="ethnicity" placeholder="{{ __('Contoh: Jawa, Sunda, Batak, Minang, dll.') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('ethnicity')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Kebangsaan -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Kebangsaan') }}</label>
                    <input type="text" wire:model="nationality" placeholder="{{ __('Contoh: WNI atau Indonesia') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('nationality')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- NPWP -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('NPWP') }}</label>
                    <input type="text" wire:model="npwp" placeholder="{{ __('Nomor NPWP (jika ada)') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('npwp')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Kode Pos -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Kode Pos') }}</label>
                    <input type="text" wire:model="postal_code" placeholder="{{ __('Contoh: 12345') }}"
                        maxlength="10"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    @error('postal_code')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Autocomplete Wilayah Sesuai KTP (Kota/Kabupaten & Provinsi) -->
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
                }"
                    @click.outside="showCityDropdown = false; showProvinceDropdown = false">

                    <!-- Kota / Kabupaten Dropdown -->
                    <div class="relative">
                        <label
                            class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ __('Kota / Kabupaten Sesuai KTP') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" x-model="$wire.city" @focus="searchRegency()"
                                @input="searchRegency()"
                                placeholder="{{ __('Ketik untuk mencari Kota / Kabupaten...') }}"
                                class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List Kota/Kabupaten -->
                        <div x-show="showCityDropdown && filteredRegencies.length > 0" x-transition
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-xl max-h-60 overflow-y-auto"
                            style="display: none;">
                            <template x-for="reg in filteredRegencies" :key="reg.id">
                                <button type="button" @click="selectRegency(reg)"
                                    class="w-full text-left px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] transition flex items-center justify-between border-b border-slate-100 dark:border-[#1D2E54]/50 last:border-0">
                                    <span x-text="reg.name"></span>
                                </button>
                            </template>
                        </div>
                        @error('city')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Provinsi Dropdown -->
                    <div class="relative">
                        <label
                            class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ __('Provinsi Sesuai KTP') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" x-model="$wire.province" @focus="searchProv()"
                                @input="searchProv()" placeholder="{{ __('Ketik untuk mencari Provinsi...') }}"
                                class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List Provinsi -->
                        <div x-show="showProvinceDropdown && filteredProvinces.length > 0" x-transition
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-xl max-h-60 overflow-y-auto"
                            style="display: none;">
                            <template x-for="prov in filteredProvinces" :key="prov.id">
                                <button type="button" @click="selectProv(prov)"
                                    class="w-full text-left px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] transition flex items-center justify-between border-b border-slate-100 dark:border-[#1D2E54]/50 last:border-0">
                                    <span x-text="prov.name"></span>
                                </button>
                            </template>
                        </div>
                        @error('province')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <!-- Status Pernikahan -->
                <div class="md:col-span-2 p-5 bg-slate-50/80 dark:bg-[#14203A]/60 border border-slate-200/80 dark:border-[#1D2E54] rounded-2xl space-y-4">
                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                        {{ __('Status Pernikahan') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach([
                            'lajang' => 'Lajang',
                            'menikah' => 'Menikah',
                            'janda' => 'Janda',
                            'duda' => 'Duda',
                        ] as $val => $label)
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] cursor-pointer hover:border-emerald-500 dark:hover:border-[#93F514] transition">
                                <input type="radio" wire:model.live="marital_status" value="{{ $val }}"
                                    class="w-4 h-4 text-emerald-600 border-slate-300 dark:border-[#1D2E54] focus:ring-emerald-500 dark:focus:ring-[#93F514] dark:bg-[#14203A]">
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __($label) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('marital_status')
                        <span class="text-xs text-red-500 block">{{ $message }}</span>
                    @enderror

                    @if($marital_status === 'menikah')
                        <div class="pt-3 border-t border-slate-200/60 dark:border-[#1D2E54]/60 max-w-sm">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                {{ __('Tanggal Pernikahan') }}
                            </label>
                            <input type="date" wire:model.blur="marriage_date"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition cursor-pointer dark:[color-scheme:dark]">
                            @error('marriage_date')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>

                <!-- Alamat sesuai KTP -->
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            {{ __('Alamat Sesuai KTP') }}
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-emerald-600 dark:text-[#93F514] select-none">
                            <input type="checkbox" wire:model.live="is_same_address"
                                class="rounded border-slate-300 dark:border-[#1D2E54] text-emerald-600 focus:ring-emerald-500 dark:focus:ring-[#93F514] dark:bg-[#14203A]">
                            <span>{{ __('Alamat Domisili sama dengan KTP') }}</span>
                        </label>
                    </div>
                    <textarea wire:model.live.debounce.300ms="ktp_address" rows="2" placeholder="{{ __('Alamat lengkap seperti yang tercantum pada KTP Anda') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition"></textarea>
                    @error('ktp_address')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Alamat Domisili Saat Ini -->
                <div class="md:col-span-2">
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Alamat Domisili Saat Ini') }}
                        <span class="text-red-500">*</span></label>
                    <textarea wire:model="address" rows="2" placeholder="{{ __('Alamat lengkap tempat tinggal saat ini') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition"></textarea>
                    @error('address')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Kepemilikan SIM (Surat Izin Mengemudi) -->
                <div class="md:col-span-2 p-5 bg-slate-50/80 dark:bg-[#14203A]/60 border border-slate-200/80 dark:border-[#1D2E54] rounded-2xl space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                {{ __('Kepemilikan SIM (Surat Izin Mengemudi)') }}
                            </label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ __('Tersedia 2 slot SIM default sesuai form fisik, Anda dapat menambah slot jika memiliki lebih dari 2 SIM.') }}
                            </p>
                        </div>
                        {{-- <button type="button" wire:click="addDrivingLicense"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-600 dark:bg-[#93F514]/10 dark:hover:bg-[#93F514]/20 dark:text-[#93F514] border border-emerald-500/20 dark:border-[#93F514]/30 transition self-start sm:self-auto">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>{{ __('Tambah SIM Lainnya') }}</span>
                        </button> --}}
                    </div>

                    <div class="space-y-3">
                        @foreach($driving_licenses as $index => $license)
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 p-3 bg-white dark:bg-[#0D1527] rounded-xl border border-slate-200 dark:border-[#1D2E54]" wire:key="license-slot-{{ $index }}">
                                <div class="w-full sm:w-48 shrink-0">
                                    <label class="block text-[10px] font-semibold text-slate-400 uppercase mb-1">{{ __('Jenis SIM') }} #{{ $index + 1 }}</label>
                                    <select wire:model="driving_licenses.{{ $index }}.type"
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] transition">
                                        <option value="">{{ __('-- Pilih Jenis SIM --') }}</option>
                                        <option value="SIM A">{{ __('SIM A (Mobil Pribadi)') }}</option>
                                        <option value="SIM B1">SIM B1</option>
                                        <option value="SIM B2">SIM B2</option>
                                        <option value="SIM C">{{ __('SIM C (Sepeda Motor)') }}</option>
                                        <option value="SIM C1">SIM C1</option>
                                        <option value="SIM C2">SIM C2</option>
                                        <option value="SIM D">{{ __('SIM D (Disabilitas)') }}</option>
                                        <option value="Lainnya">{{ __('Lainnya') }}</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-[10px] font-semibold text-slate-400 uppercase mb-1">{{ __('Nomor SIM') }}</label>
                                    <input type="text" wire:model="driving_licenses.{{ $index }}.number" placeholder="{{ __('Nomor kartu SIM (kosongkan jika tidak ada)') }}"
                                        class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] transition">
                                </div>
                                @if(count($driving_licenses) > 2)
                                    <div class="sm:self-end pt-1 sm:pt-0">
                                        <button type="button" wire:click="removeDrivingLicense({{ $index }})"
                                            title="{{ __('Hapus baris SIM') }}"
                                            class="w-full sm:w-auto p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition text-xs flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span class="sm:hidden">{{ __('Hapus') }}</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Transportasi Sehari-hari yang Dipakai -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        {{ __('Transportasi Sehari-hari yang Dipakai') }}
                    </label>
                    <select wire:model="daily_transportation"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                        <option value="">{{ __('-- Pilih Transportasi --') }}</option>
                        <option value="Sepeda Motor Pribadi">{{ __('Sepeda Motor Pribadi') }}</option>
                        <option value="Mobil Pribadi">{{ __('Mobil Pribadi') }}</option>
                        <option value="Transportasi Umum (KRL / Kereta)">{{ __('Transportasi Umum (KRL / Kereta)') }}</option>
                        <option value="Transportasi Umum (TransJakarta / Bus / Angkot)">{{ __('Transportasi Umum (TransJakarta / Bus / Angkot)') }}</option>
                        <option value="Ojek Online / Taksi Online">{{ __('Ojek Online / Taksi Online') }}</option>
                        <option value="Sepeda">{{ __('Sepeda') }}</option>
                        <option value="Jalan Kaki">{{ __('Jalan Kaki') }}</option>
                        <option value="Lainnya">{{ __('Lainnya') }}</option>
                    </select>
                    @error('daily_transportation')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Jarak ke Tempat Kerja -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        {{ __('Jarak Rumah/Kost ke Tempat Kerja') }}
                    </label>
                    <div class="relative">
                        <input type="number" step="0.1" min="0" wire:model="work_distance_km" placeholder="{{ __('Contoh: 12.5') }}"
                            class="w-full px-4 py-2.5 pr-14 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-xs font-bold text-slate-400 dark:text-slate-500">
                            km
                        </div>
                    </div>
                    @error('work_distance_km')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tentang Saya -->
                <div class="md:col-span-2">
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('Tentang Saya / Ringkasan Diri') }}</label>
                    <textarea wire:model="about_me" rows="4"
                        placeholder="{{ __('Tuliskan gambaran singkat mengenai latar belakang, motivasi, dan keahlian Anda...') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition"></textarea>
                    @error('about_me')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Upload Dokumen CV / Resume -->
                <div
                    class="md:col-span-2 p-5 bg-slate-50/80 dark:bg-[#14203A]/60 border border-slate-200/80 dark:border-[#1D2E54] rounded-2xl space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label
                                class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">{{ __('Dokumen CV / Resume (PDF / DOCX)') }}
                                <span class="text-red-500">*</span></label>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ __('Unggah file CV terbaru Anda (Maksimal 5MB, Format PDF, DOC, atau DOCX)') }}</p>
                        </div>
                        <a href="{{ route('profile.cv.preview') }}" target="_blank"
                            class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-[#93F514] hover:underline">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>{{ __('Preview CV ATS System') }}</span>
                        </a>
                    </div>

                    <!-- File Upload Input / Status -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <label
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl cursor-pointer shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition flex items-center gap-2 shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span>{{ $current_cv_path ? __('Ganti File CV') : __('Pilih & Unggah CV') }}</span>
                            <input type="file" wire:model.live="cv_file" accept=".pdf,.doc,.docx" class="hidden">
                        </label>

                        @if ($current_cv_url)
                            <div
                                class="flex items-center gap-3 bg-white dark:bg-[#0D1527] px-4 py-2 rounded-xl border border-slate-200 dark:border-[#1D2E54] text-xs w-full sm:w-auto justify-between">
                                <a href="{{ $current_cv_url }}" target="_blank"
                                    class="flex items-center gap-2 text-emerald-600 dark:text-[#93F514] font-semibold hover:underline truncate">
                                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <span class="truncate">{{ __('File CV Tersimpan') }}</span>
                                </a>
                                <button type="button" wire:click="deleteCv"
                                    wire:confirm="{{ __('Apakah Anda yakin ingin menghapus file CV ini?') }}"
                                    class="text-red-500 hover:text-red-700 font-medium text-xs ml-2">
                                    {{ __('Hapus') }}
                                </button>
                            </div>
                        @else
                            <span
                                class="text-xs text-slate-400 dark:text-slate-500 italic">{{ __('Belum ada file CV yang diunggah.') }}</span>
                        @endif
                    </div>

                    <!-- Progress indicator -->
                    <div wire:loading wire:target="cv_file"
                        class="text-xs text-emerald-600 dark:text-[#93F514] font-medium flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ __('Mengunggah file...') }}
                    </div>

                    @error('cv_file')
                        <span class="text-xs text-red-500 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-70 disabled:cursor-not-allowed text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition duration-150 ease-in-out active:scale-95 cursor-pointer">
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin shrink-0 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="save">{{ __('Simpan Perubahan') }}</span>
                    <span wire:loading wire:target="save">{{ __('Menyimpan...') }}</span>
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
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200 dark:border-[#1D2E54]">

                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-slate-200/80 dark:border-[#1D2E54]">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 0L4 4m5.121 5.121L4 19" />
                        </svg>
                        {{ __('Atur & Potong Foto Profil') }}
                    </h3>
                    <button @click="cancelCrop" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Content / Cropper Area -->
                <div
                    class="p-6 bg-slate-950 flex justify-center items-center min-h-[320px] max-h-[420px] overflow-hidden">
                    <img x-ref="cropperImage" class="max-w-full max-h-[380px] block">
                </div>

                <!-- Toolbar Controls -->
                <div
                    class="flex items-center justify-center gap-3 py-3 bg-slate-50 dark:bg-[#14203A] border-t border-slate-200/80 dark:border-[#1D2E54]">
                    <button type="button" @click="zoomIn" title="{{ __('Perbesar') }}"
                        class="p-2 rounded-lg bg-white dark:bg-[#0D1527] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1D2E54] shadow-sm border border-slate-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                        </svg>
                    </button>
                    <button type="button" @click="zoomOut" title="{{ __('Perkecil') }}"
                        class="p-2 rounded-lg bg-white dark:bg-[#0D1527] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1D2E54] shadow-sm border border-slate-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                        </svg>
                    </button>
                    <button type="button" @click="rotateLeft" title="{{ __('Putar Kiri') }}"
                        class="p-2 rounded-lg bg-white dark:bg-[#0D1527] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1D2E54] shadow-sm border border-slate-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    </button>
                    <button type="button" @click="rotateRight" title="{{ __('Putar Kanan') }}"
                        class="p-2 rounded-lg bg-white dark:bg-[#0D1527] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1D2E54] shadow-sm border border-slate-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 10H11a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Footer -->
                <div
                    class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527]">
                    <button type="button" @click="cancelCrop"
                        class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#14203A] hover:bg-slate-200 dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                        {{ __('Batal') }}
                    </button>
                    <button type="button" @click="applyCrop"
                        class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition flex items-center gap-1.5 active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ __('Gunakan Foto Ini') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
