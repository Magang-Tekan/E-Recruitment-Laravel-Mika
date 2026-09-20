<div class="space-y-6">
    <!-- Header Card (Clean Enterprise Blueprint) -->
    <div class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Data Keluarga</h2>
            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">* Isilah data susunan dan latar belakang keluarga Anda dengan sebenarnya.</p>
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
        <form wire:submit.prevent="save" class="space-y-8">

            <!-- Section 1: Urutan Anak -->
            <div class="pb-6 border-b border-slate-200/80 dark:border-[#1D2E54]">
                <div class="flex flex-wrap items-center gap-3">
                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        Anak ke <span class="text-red-500">*</span>
                    </label>
                    <input type="number" min="1" wire:model="child_sequence" placeholder="0"
                        class="w-20 px-3 py-2 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm text-center focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                    
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">dari</span>

                    <input type="number" min="1" wire:model="total_siblings" placeholder="0"
                        class="w-20 px-3 py-2 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm text-center focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">

                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">bersaudara</span>
                </div>
                <div class="flex gap-4 mt-1">
                    @error('child_sequence') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    @error('total_siblings') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section 2: Data Orang Tua (Ayah & Ibu) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pb-6 border-b border-slate-200/80 dark:border-[#1D2E54]"
                x-data="{
                    years: @js($years),
                    educationOptions: @js($educationOptions),
                    occupationOptions: @js($occupationOptions),

                    // Ayah dropdown states & search logic
                    openFatherYear: false,
                    openFatherEdu: false,
                    openFatherOcc: false,
                    fatherYearQuery: '',
                    fatherEduQuery: '',
                    fatherOccQuery: '',

                    get filteredFatherYears() {
                        if (!this.fatherYearQuery) return this.years;
                        return this.years.filter(y => String(y).includes(this.fatherYearQuery));
                    },
                    get filteredFatherEdus() {
                        if (!this.fatherEduQuery) return this.educationOptions;
                        return this.educationOptions.filter(e => e.toLowerCase().includes(this.fatherEduQuery.toLowerCase()));
                    },
                    get filteredFatherOccs() {
                        if (!this.fatherOccQuery) return this.occupationOptions;
                        return this.occupationOptions.filter(o => o.toLowerCase().includes(this.fatherOccQuery.toLowerCase()));
                    },

                    // Ibu dropdown states & search logic
                    openMotherYear: false,
                    openMotherEdu: false,
                    openMotherOcc: false,
                    motherYearQuery: '',
                    motherEduQuery: '',
                    motherOccQuery: '',

                    get filteredMotherYears() {
                        if (!this.motherYearQuery) return this.years;
                        return this.years.filter(y => String(y).includes(this.motherYearQuery));
                    },
                    get filteredMotherEdus() {
                        if (!this.motherEduQuery) return this.educationOptions;
                        return this.educationOptions.filter(e => e.toLowerCase().includes(this.motherEduQuery.toLowerCase()));
                    },
                    get filteredMotherOccs() {
                        if (!this.motherOccQuery) return this.occupationOptions;
                        return this.occupationOptions.filter(o => o.toLowerCase().includes(this.motherOccQuery.toLowerCase()));
                    }
                }" @click.outside="openFatherYear = false; openFatherEdu = false; openFatherOcc = false; openMotherYear = false; openMotherEdu = false; openMotherOcc = false;">
                
                <!-- Kolom Ayah -->
                <div class="space-y-5">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-blue-600 dark:text-[#93F514] flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Ayah
                    </h3>

                    <!-- Nama Ayah -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Nama Ayah <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="father_name" placeholder="Masukkan Nama Lengkap"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                        @error('father_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tahun Lahir Ayah (Autocomplete Searchable Input) -->
                    <div class="relative">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Tahun Lahir Ayah <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                x-model="$wire.father_birth_year"
                                @focus="openFatherYear = true; fatherYearQuery = ''"
                                @input="openFatherYear = true; fatherYearQuery = $event.target.value"
                                placeholder="Pilih / Ketik Tahun Lahir"
                                class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List Tahun Lahir Ayah -->
                        <div x-show="openFatherYear && filteredFatherYears.length > 0" 
                            x-transition
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            <template x-for="yr in filteredFatherYears" :key="yr">
                                <button type="button" 
                                    @click="$wire.father_birth_year = yr; openFatherYear = false"
                                    class="w-full text-left px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] transition flex items-center justify-between border-b border-slate-100 dark:border-[#1D2E54]/50 last:border-0">
                                    <span x-text="yr"></span>
                                </button>
                            </template>
                        </div>
                        @error('father_birth_year') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pendidikan Terakhir Ayah (Autocomplete Searchable Input) -->
                    <div class="relative">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Pendidikan Terakhir Ayah <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                x-model="$wire.father_last_education"
                                @focus="openFatherEdu = true; fatherEduQuery = ''"
                                @input="openFatherEdu = true; fatherEduQuery = $event.target.value"
                                placeholder="Pilih / Ketik Pendidikan"
                                class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List Pendidikan Ayah -->
                        <div x-show="openFatherEdu && filteredFatherEdus.length > 0" 
                            x-transition
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            <template x-for="edu in filteredFatherEdus" :key="edu">
                                <button type="button" 
                                    @click="$wire.father_last_education = edu; openFatherEdu = false"
                                    class="w-full text-left px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] transition flex items-center justify-between border-b border-slate-100 dark:border-[#1D2E54]/50 last:border-0">
                                    <span x-text="edu"></span>
                                </button>
                            </template>
                        </div>
                        @error('father_last_education') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pekerjaan Ayah (Autocomplete Searchable Input) -->
                    <div class="relative">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Pekerjaan Ayah <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                x-model="$wire.father_occupation"
                                @focus="openFatherOcc = true; fatherOccQuery = ''"
                                @input="openFatherOcc = true; fatherOccQuery = $event.target.value"
                                placeholder="Pilih / Ketik Pekerjaan"
                                class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List Pekerjaan Ayah -->
                        <div x-show="openFatherOcc && filteredFatherOccs.length > 0" 
                            x-transition
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            <template x-for="occ in filteredFatherOccs" :key="occ">
                                <button type="button" 
                                    @click="$wire.father_occupation = occ; openFatherOcc = false"
                                    class="w-full text-left px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] transition flex items-center justify-between border-b border-slate-100 dark:border-[#1D2E54]/50 last:border-0">
                                    <span x-text="occ"></span>
                                </button>
                            </template>
                        </div>
                        @error('father_occupation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Perusahaan / Institusi Ayah -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Perusahaan / Institusi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="father_company" placeholder="Masukkan nama perusahaan/institusi"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                        @error('father_company') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Kolom Ibu -->
                <div class="space-y-5">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-[#93F514] flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Ibu
                    </h3>

                    <!-- Nama Ibu -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Nama Ibu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="mother_name" placeholder="Masukkan Nama Lengkap"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                        @error('mother_name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tahun Lahir Ibu (Autocomplete Searchable Input) -->
                    <div class="relative">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Tahun Lahir Ibu <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                x-model="$wire.mother_birth_year"
                                @focus="openMotherYear = true; motherYearQuery = ''"
                                @input="openMotherYear = true; motherYearQuery = $event.target.value"
                                placeholder="Pilih / Ketik Tahun Lahir"
                                class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List Tahun Lahir Ibu -->
                        <div x-show="openMotherYear && filteredMotherYears.length > 0" 
                            x-transition
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            <template x-for="yr in filteredMotherYears" :key="yr">
                                <button type="button" 
                                    @click="$wire.mother_birth_year = yr; openMotherYear = false"
                                    class="w-full text-left px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] transition flex items-center justify-between border-b border-slate-100 dark:border-[#1D2E54]/50 last:border-0">
                                    <span x-text="yr"></span>
                                </button>
                            </template>
                        </div>
                        @error('mother_birth_year') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pendidikan Terakhir Ibu (Autocomplete Searchable Input) -->
                    <div class="relative">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Pendidikan Terakhir Ibu <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                x-model="$wire.mother_last_education"
                                @focus="openMotherEdu = true; motherEduQuery = ''"
                                @input="openMotherEdu = true; motherEduQuery = $event.target.value"
                                placeholder="Pilih / Ketik Pendidikan"
                                class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List Pendidikan Ibu -->
                        <div x-show="openMotherEdu && filteredMotherEdus.length > 0" 
                            x-transition
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            <template x-for="edu in filteredMotherEdus" :key="edu">
                                <button type="button" 
                                    @click="$wire.mother_last_education = edu; openMotherEdu = false"
                                    class="w-full text-left px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] transition flex items-center justify-between border-b border-slate-100 dark:border-[#1D2E54]/50 last:border-0">
                                    <span x-text="edu"></span>
                                </button>
                            </template>
                        </div>
                        @error('mother_last_education') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pekerjaan Ibu (Autocomplete Searchable Input) -->
                    <div class="relative">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Pekerjaan Ibu <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                x-model="$wire.mother_occupation"
                                @focus="openMotherOcc = true; motherOccQuery = ''"
                                @input="openMotherOcc = true; motherOccQuery = $event.target.value"
                                placeholder="Pilih / Ketik Pekerjaan"
                                class="w-full px-4 py-2.5 pr-10 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown List Pekerjaan Ibu -->
                        <div x-show="openMotherOcc && filteredMotherOccs.length > 0" 
                            x-transition
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded-xl shadow-xl max-h-60 overflow-y-auto">
                            <template x-for="occ in filteredMotherOccs" :key="occ">
                                <button type="button" 
                                    @click="$wire.mother_occupation = occ; openMotherOcc = false"
                                    class="w-full text-left px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] hover:text-emerald-600 dark:hover:text-[#93F514] transition flex items-center justify-between border-b border-slate-100 dark:border-[#1D2E54]/50 last:border-0">
                                    <span x-text="occ"></span>
                                </button>
                            </template>
                        </div>
                        @error('mother_occupation') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Perusahaan / Institusi Ibu -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Perusahaan / Institusi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="mother_company" placeholder="Masukkan nama perusahaan/institusi"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                        @error('mother_company') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>

            <!-- Section 3: Status Pernikahan Anda -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                    Status Pernikahan Anda <span class="text-red-500">*</span>
                </label>
                <div class="flex flex-wrap items-center gap-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="marital_status" value="lajang"
                            class="w-4 h-4 text-emerald-600 border-slate-300 dark:border-[#1D2E54] focus:ring-emerald-500 dark:focus:ring-[#93F514] dark:bg-[#14203A]">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Lajang</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="marital_status" value="menikah"
                            class="w-4 h-4 text-emerald-600 border-slate-300 dark:border-[#1D2E54] focus:ring-emerald-500 dark:focus:ring-[#93F514] dark:bg-[#14203A]">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Menikah</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="marital_status" value="bercerai"
                            class="w-4 h-4 text-emerald-600 border-slate-300 dark:border-[#1D2E54] focus:ring-emerald-500 dark:focus:ring-[#93F514] dark:bg-[#14203A]">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Bercerai</span>
                    </label>
                </div>
                @error('marital_status') <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span> @enderror
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
</div>
