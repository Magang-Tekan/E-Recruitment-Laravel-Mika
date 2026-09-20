<div class="space-y-6">

    <!-- Header Card (Clean Enterprise Blueprint) -->
    <div class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Riwayat Pendidikan</h2>
            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">* Tambahkan riwayat pendidikan formal dan non-formal Anda.</p>
        </div>
    </div>

    <!-- Success Flash Alert -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-xl shadow-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
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

    <!-- Education List Cards -->
    <div class="space-y-4">
        @forelse ($educations as $item)
            <div
                class="bg-white dark:bg-[#0D1527] p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] hover:border-slate-300 dark:hover:border-[#2D457C] transition duration-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="px-3 py-1 bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-slate-200 font-semibold text-xs rounded-lg border border-slate-200/80 dark:border-[#1D2E54]">
                            {{ $item->degreeRelation ? $item->degreeRelation->name : $item->degree ?? 'Pendidikan' }}
                        </span>
                        @if ($item->gpa)
                            @php
                                $degreeName = strtoupper(
                                    $item->degreeRelation ? $item->degreeRelation->name : $item->degree ?? '',
                                );
                                $isSchoolItem =
                                    str_contains($degreeName, 'SMA') ||
                                    str_contains($degreeName, 'SMK') ||
                                    str_contains($degreeName, 'SLTA') ||
                                    str_contains($degreeName, 'SMP') ||
                                    str_contains($degreeName, 'SD') ||
                                    $item->gpa > 4.0;
                            @endphp
                            <span
                                class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-medium text-xs rounded-lg border border-emerald-200 dark:border-emerald-800">
                                {{ $isSchoolItem ? 'Nilai:' : 'IPK:' }} {{ number_format($item->gpa, 2) }}
                            </span>
                        @endif
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-medium ml-auto md:ml-0">
                            {{ $item->start_year }} - {{ $item->end_year ? $item->end_year : 'Sekarang' }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $item->school_name }}
                    </h3>

                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                        {{ $item->majorRelation ? $item->majorRelation->name : $item->major ?? '' }}
                        @if ($item->study_program && $item->study_program !== $item->major)
                            <span class="text-slate-400 dark:text-slate-500">({{ $item->study_program }})</span>
                        @endif
                    </p>

                    @if ($item->description)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                            {{ $item->description }}
                        </p>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center gap-2 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-[#1D2E54] shrink-0">
                    <button wire:click="edit({{ $item->id }})"
                        class="px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14203A] rounded-xl transition flex items-center gap-1.5 border border-slate-200/80 dark:border-[#1D2E54]">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Edit</span>
                    </button>
                    <button wire:click="confirmDelete({{ $item->id }})"
                        class="px-3 py-2 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/50 rounded-xl transition flex items-center gap-1.5 border border-red-200 dark:border-red-800">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        @empty
            <!-- Empty State matching Admin Panel Style -->
            <div class="col-span-full bg-white dark:bg-[#0D1527] p-8 md:p-10 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] text-center flex flex-col items-center justify-center space-y-4">
                <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Belum ada data untuk ditampilkan</p>
                <button wire:click="openModal"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-sm rounded-xl transition shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 active:scale-95">
                    <svg class="w-5 h-5 text-white dark:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Tambah Pendidikan</span>
                </button>
            </div>
        @endforelse
    </div>

    @if ($educations->isNotEmpty())
        <div class="pt-1 flex justify-start">
            <button wire:click="openModal"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Pendidikan</span>
            </button>
        </div>
    @endif

    <!-- Modal Form (Tambah / Edit Pendidikan) -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="closeModal">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200 dark:border-[#1D2E54]">

                    <!-- Header -->
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-slate-200/80 dark:border-[#1D2E54]">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-[#93F514]" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                            </svg>

                            <span>{{ $isEdit ? 'Edit Riwayat Pendidikan' : 'Tambah Riwayat Pendidikan' }}</span>
                        </h3>
                        <button wire:click="closeModal"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body Form -->
                    <form wire:submit.prevent="save" class="p-6 space-y-5">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <!-- Tingkat Pendidikan (Degree) -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Tingkat / Jenjang Pendidikan
                                </label>
                                <select wire:model.live="degree_id"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    <option value="">-- Pilih Jenjang --</option>
                                    @foreach ($degrees as $deg)
                                        <option value="{{ $deg->id }}">{{ $deg->name }}</option>
                                    @endforeach
                                </select>
                                @error('degree_id')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Nama Sekolah / Universitas -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Nama Institusi / Sekolah <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model="school_name"
                                    placeholder="Contoh: Universitas Diponegoro / SMA 1"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                @error('school_name')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Jurusan (Major) -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Jurusan
                                </label>
                                @if ($this->isSchoolDegree())
                                    <input type="text" value="Tidak Berlaku (SMA/SMK)" disabled
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-100 dark:bg-[#14203A]/50 text-slate-400 dark:text-slate-500 text-sm cursor-not-allowed font-medium select-none">
                                    <p
                                        class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 font-medium flex items-start gap-1">
                                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>* Untuk jenjang SMA/SMK, silakan isikan jurusan/konsentrasi keahlian Anda
                                            pada kolom <strong>Program Studi / Konsentrasi</strong> di sebelah.</span>
                                    </p>
                                @else
                                    <select wire:model="major_id"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                        <option value="">-- Pilih Jurusan --</option>
                                        @foreach ($majors as $maj)
                                            <option value="{{ $maj->id }}">{{ $maj->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('major_id')
                                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>

                            <!-- Program Studi / Konsentrasi -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Program Studi / Konsentrasi
                                </label>
                                <input type="text" wire:model="study_program"
                                    placeholder="{{ $this->isSchoolDegree() ? 'Contoh: IPA / IPS / Teknik Komputer & Jaringan' : 'Contoh: Teknik Informatika / Manajemen' }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                @error('study_program')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Tahun Masuk -->
                            <div>
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Tahun Masuk <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model="start_year" placeholder="Contoh: 2020"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                @error('start_year')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Tahun Lulus / Selesai -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label
                                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                        Tahun Lulus
                                    </label>
                                    <label
                                        class="flex items-center gap-1.5 cursor-pointer text-xs font-medium text-emerald-600 dark:text-[#93F514] select-none">
                                        <input type="checkbox" wire:model.live="is_ongoing"
                                            class="w-3.5 h-3.5 rounded border-slate-300 dark:border-[#1D2E54] text-emerald-600 focus:ring-emerald-500 dark:bg-[#14203A]">
                                        <span>Masih Berlangsung / Belum Lulus</span>
                                    </label>
                                </div>
                                <input type="number" wire:model="end_year"
                                    placeholder="{{ $is_ongoing ? 'Masih Berlangsung' : 'Contoh: 2024' }}"
                                    @if ($is_ongoing) disabled @endif
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition {{ $is_ongoing ? 'bg-slate-100 dark:bg-[#14203A]/50 text-slate-400 dark:text-slate-500 cursor-not-allowed select-none' : 'bg-slate-50 dark:bg-[#14203A]' }}">
                                @error('end_year')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- IPK / Nilai Akhir -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    {{ $this->isSchoolDegree() ? 'Nilai Rata-Rata / Nilai Ujian (Skala 0 - 100)' : 'IPK / Nilai Akhir (Skala 0.00 - 4.00)' }}
                                </label>
                                <input type="number" step="0.01" min="0"
                                    max="{{ $this->isSchoolDegree() ? '100' : '4.00' }}" wire:model="gpa"
                                    placeholder="{{ $this->isSchoolDegree() ? 'Contoh: 85.50' : 'Contoh: 3.75' }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                @error('gpa')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Deskripsi / Catatan -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Deskripsi / Prestasi Pendidikan
                                </label>
                                <textarea wire:model="description" rows="3"
                                    placeholder="Tuliskan judul skripsi, prestasi akademik, atau kegiatan ekstrakurikuler penting..."
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition"></textarea>
                                @error('description')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Footer Actions -->
                        <div
                            class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                            <button type="button" wire:click="closeModal"
                                class="px-5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#14203A] hover:bg-slate-200 dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled"
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold rounded-xl transition flex items-center gap-2 active:scale-95">
                                <span wire:loading.remove
                                    wire:target="save">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Pendidikan' }}</span>
                                <span wire:loading wire:target="save" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Pendidikan -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="cancelDelete"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200 dark:border-[#1D2E54] p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-950/60 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Konfirmasi Hapus Data</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Apakah Anda yakin ingin menghapus riwayat pendidikan ini? Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                        <button type="button" wire:click="cancelDelete" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#14203A] hover:bg-slate-200 dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                            Batal
                        </button>
                        <button type="button" wire:click="delete" wire:loading.attr="disabled" class="px-5 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-md shadow-red-500/20 transition flex items-center gap-1.5 active:scale-95">
                            <span wire:loading.remove wire:target="delete">Ya, Hapus</span>
                            <span wire:loading wire:target="delete">Menghapus...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- SECTION 2: KEAHLIAN / SKILL (Wajib)        -->
    <!-- ========================================== -->
    <div class="mt-10 space-y-4 pt-6 border-t border-slate-200/80 dark:border-[#1D2E54]">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    Keahlian / Skill
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">Tambahkan keahlian teknis atau soft-skill yang Anda miliki.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse ($skills as $skill)
                <div class="bg-white dark:bg-[#0D1527] p-4 rounded-xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] hover:border-slate-300 dark:hover:border-[#2D457C] transition flex items-center justify-between gap-3">
                    <div class="space-y-1 overflow-hidden">
                        <span class="font-bold text-sm text-slate-900 dark:text-white block truncate">{{ $skill->name }}</span>
                        @if ($skill->certificate_path)
                            <a href="{{ Storage::url($skill->certificate_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 dark:text-[#93F514] hover:underline font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <span>Sertifikat</span>
                            </a>
                        @endif
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <button wire:click="editSkill({{ $skill->id }})" class="p-1.5 text-slate-400 hover:text-emerald-600 dark:hover:text-[#93F514] rounded-lg hover:bg-slate-100 dark:hover:bg-[#14203A] transition" title="Edit Skill">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="confirmDeleteSkill({{ $skill->id }})" class="p-1.5 text-slate-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-slate-100 dark:hover:bg-[#14203A] transition" title="Hapus Skill">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <!-- Empty State matching standard section style -->
                <div class="col-span-full bg-white dark:bg-[#0D1527] p-8 md:p-10 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] text-center flex flex-col items-center justify-center space-y-4">
                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.816c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6A2.25 2.25 0 0 0 4.727 20.25h14.546a2.25 2.25 0 0 0 2.224-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                    </svg>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Belum ada data untuk ditampilkan</p>
                    <button wire:click="openSkillModal"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-sm font-bold rounded-xl transition shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 active:scale-95">
                        <svg class="w-5 h-5 text-white dark:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Tambah Skill</span>
                    </button>
                </div>
            @endforelse
        </div>

        @if ($skills->isNotEmpty())
            <div class="pt-1 flex justify-start">
                <button wire:click="openSkillModal"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Skill</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Modal Form (Tambah / Edit Skill) -->
    @if ($showSkillModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="closeSkillModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-[#1D2E54]">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200/80 dark:border-[#1D2E54]">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ $isSkillEdit ? 'Edit Skill / Keahlian' : 'Tambah Skill / Keahlian' }}
                        </h3>
                        <button type="button" wire:click="closeSkillModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form wire:submit.prevent="saveSkill" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Nama Skill / Keahlian <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" wire:model="skill_name" placeholder="Misal: PHP Laravel, Public Speaking, Graphic Design..."
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                            @error('skill_name')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                File Sertifikat / Bukti Pendukung (Opsional)
                            </label>
                            <input type="file" wire:model="skill_certificate" class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 dark:file:bg-[#14203A] dark:file:text-slate-200 hover:file:bg-slate-200 transition">
                            @if ($existing_skill_certificate && !$skill_certificate)
                                <p class="text-xs text-slate-500 mt-1">
                                    File saat ini: <a href="{{ Storage::url($existing_skill_certificate) }}" target="_blank" class="text-emerald-600 dark:text-[#93F514] hover:underline">Lihat Sertifikat</a>
                                </p>
                            @endif
                            @error('skill_certificate')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                            <button type="button" wire:click="closeSkillModal" class="px-5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#14203A] hover:bg-slate-200 dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black dark:shadow-[#93F514]/20 text-xs font-bold rounded-xl transition flex items-center gap-2 active:scale-95">
                                <span wire:loading.remove wire:target="saveSkill">{{ $isSkillEdit ? 'Simpan Perubahan' : 'Tambah Skill' }}</span>
                                <span wire:loading wire:target="saveSkill" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
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
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Skill -->
    @if ($showDeleteSkillModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" wire:click="cancelDeleteSkill"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200 dark:border-[#1D2E54] p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-950/60 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Konfirmasi Hapus Skill</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Apakah Anda yakin ingin menghapus data keahlian ini?</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                        <button type="button" wire:click="cancelDeleteSkill" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-[#14203A] hover:bg-slate-200 dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition">
                            Batal
                        </button>
                        <button type="button" wire:click="deleteSkill" wire:loading.attr="disabled" class="px-5 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-md shadow-red-500/20 transition flex items-center gap-1.5 active:scale-95">
                            <span wire:loading.remove wire:target="deleteSkill">Ya, Hapus</span>
                            <span wire:loading wire:target="deleteSkill">Menghapus...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
