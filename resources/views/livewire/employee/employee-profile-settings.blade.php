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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    @if (session()->has('employee_profile_message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-xl shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm font-medium">{{ session('employee_profile_message') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <div class="bg-white dark:bg-[#0D1527] p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-[#1D2E54]">
        <form wire:submit.prevent="save" class="space-y-6">

            <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-100 dark:border-[#1D2E54]">
                <div class="relative group shrink-0">
                    <div class="w-28 h-28 rounded-full overflow-hidden bg-indigo-100 dark:bg-[#14203A] border-2 border-indigo-500 dark:border-[#93F514] shadow-md flex items-center justify-center relative">
                        @if ($cropped_photo_base64)
                            <img src="{{ $cropped_photo_base64 }}" class="w-full h-full object-cover" alt="Foto profil">
                        @elseif ($current_photo_url)
                            <img src="{{ $current_photo_url }}" class="w-full h-full object-cover" alt="Foto profil">
                        @else
                            <svg class="w-14 h-14 text-indigo-400 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        @endif
                    </div>
                </div>

                <div class="flex-1 text-center sm:text-left space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Foto Profil</label>
                    <div class="flex flex-wrap items-center gap-3 justify-center sm:justify-start">
                        <label class="px-4 py-2 bg-indigo-50 dark:bg-[#14203A] text-indigo-700 dark:text-[#38BDF8] hover:bg-indigo-100 dark:hover:bg-[#1A2A4C] text-xs font-semibold rounded-xl cursor-pointer transition border border-indigo-200 dark:border-[#253966] flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Pilih & Atur Foto</span>
                            <input type="file" @change="onFileSelect" accept="image/*" class="hidden">
                        </label>
                    </div>
                    <p class="text-[11px] text-gray-400 dark:text-[#93A5C9]">* Anda dapat menggeser, memperbesar, dan memotong foto sebelum disimpan.</p>
                    @error('photo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">NIK (16 Digit Sesuai KTP)</label>
                    <input type="text" wire:model="nik" maxlength="16" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="16 digit nomor NIK"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('nik') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input type="text" wire:model="full_name" placeholder="Nama lengkap"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('full_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Perusahaan</label>
                    <select wire:model.live="company_id"
                        class="w-full pl-4 pr-8 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition cursor-pointer">
                        <option value="">-- Pilih Perusahaan --</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Divisi / Departemen</label>
                    <select wire:model.live="department_id"
                        class="w-full pl-4 pr-8 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition cursor-pointer">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">
                                {{ $department->name }}{{ $department->company && ! $company_id ? ' (' . $department->company->name . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2 flex items-center justify-between">
                        <span>Pilih Jabatan Baku</span>
                        <span class="text-[10px] text-gray-400 font-normal lowercase">otomatis</span>
                    </label>
                    <select wire:model.live="position_id"
                        class="w-full pl-4 pr-8 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition cursor-pointer">
                        <option value="">-- Pilih Posisi (Opsional) --</option>
                        @foreach ($positions as $pos)
                            @if (!$department_id || $department_id == $pos->department_id)
                                <option value="{{ $pos->id }}">
                                    {{ $pos->name }} ({{ $pos->department?->name }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('position_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Posisi / Jabatan Spesifik</label>
                    <input type="text" wire:model="position_title" placeholder="Contoh: Staff IT, Supervisor, dll"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('position_title') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Kategori / Tipe Pegawai</label>
                    <select wire:model="employee_type"
                        class="w-full pl-4 pr-8 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition cursor-pointer">
                        <option value="permanent">Karyawan Tetap (Permanent)</option>
                        <option value="contract">Karyawan Kontrak (Contract)</option>
                        <option value="internship">Magang / Internship</option>
                        <option value="probation">Masa Percobaan (Probation)</option>
                    </select>
                    @error('employee_type') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-[#1D2E54]">
                <button type="submit" wire:loading.attr="disabled"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-500/20 transition duration-150 ease-in-out flex items-center gap-2">
                    <span wire:loading.remove wire:target="save">Simpan Profil</span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <div x-show="showCropperModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" @click="cancelCrop"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-200 dark:border-[#1D2E54]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-[#1D2E54]">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Atur & Potong Foto Profil</h3>
                    <button @click="cancelCrop" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 bg-gray-900 flex justify-center items-center min-h-[320px] max-h-[420px] overflow-hidden">
                    <img x-ref="cropperImage" class="max-w-full max-h-[380px] block" alt="Crop preview">
                </div>
                <div class="flex items-center justify-center gap-3 py-3 bg-gray-50 dark:bg-[#14203A] border-t border-gray-100 dark:border-[#1D2E54]">
                    <button type="button" @click="zoomIn" class="p-2 rounded-lg bg-white dark:bg-[#14203A] text-gray-700 dark:text-[#DDE5F5] hover:bg-gray-100 dark:hover:bg-[#1A2A4C] shadow-sm border border-gray-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" /></svg>
                    </button>
                    <button type="button" @click="zoomOut" class="p-2 rounded-lg bg-white dark:bg-[#14203A] text-gray-700 dark:text-[#DDE5F5] hover:bg-gray-100 dark:hover:bg-[#1A2A4C] shadow-sm border border-gray-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" /></svg>
                    </button>
                    <button type="button" @click="rotateLeft" class="p-2 rounded-lg bg-white dark:bg-[#14203A] text-gray-700 dark:text-[#DDE5F5] hover:bg-gray-100 dark:hover:bg-[#1A2A4C] shadow-sm border border-gray-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                    </button>
                    <button type="button" @click="rotateRight" class="p-2 rounded-lg bg-white dark:bg-[#14203A] text-gray-700 dark:text-[#DDE5F5] hover:bg-gray-100 dark:hover:bg-[#1A2A4C] shadow-sm border border-gray-200 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6" /></svg>
                    </button>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527]">
                    <button type="button" @click="cancelCrop"
                        class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-[#93A5C9] bg-gray-100 dark:bg-[#14203A] hover:bg-gray-200 dark:hover:bg-[#1A2A4C] border border-transparent dark:border-[#1D2E54] rounded-xl transition">
                        Batal
                    </button>
                    <button type="button" @click="applyCrop"
                        class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-500/20 transition">
                        Gunakan Foto Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
