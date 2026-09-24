<div class="space-y-6" x-data="{
    showDetailModal: false,
    detailData: {},
    openDetailModal(candidate) {
        this.detailData = candidate;
        this.showDetailModal = true;
    },
    formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return months[d.getMonth()] + ' ' + d.getFullYear();
    }
}">
    <!-- Header & Filter Section -->
    <div
        class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 overflow-hidden shadow-sm rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Database Kandidat & Pelamar</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">Daftar seluruh pengguna terdaftar yang memiliki
                    profil pelamar di sistem.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <!-- Search Input -->
                <div class="relative w-full sm:w-72">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama, NIK, email, kota..."
                        class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
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
                        <th class="px-6 py-4">Kandidat</th>
                        <th class="px-6 py-4">Kontak & Lokasi</th>
                        <th class="px-6 py-4">Pendidikan Terakhir</th>
                        <th class="px-6 py-4">Total Lamaran</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/60 text-gray-700 dark:text-slate-300">
                    @forelse ($candidates as $candidate)
                        @php
                            $profile = $candidate->applicantProfile;
                            $latestEdu = $profile?->educations?->sortByDesc('end_year')->first();
                            $appCount = $profile?->jobApplications?->count() ?? 0;
                            $photo = $profile?->photo ?? $candidate->avatar;
                            $photoUrl = $photo ? (\Illuminate\Support\Str::startsWith($photo, ['http://', 'https://']) ? $photo : asset('storage/' . $photo)) : null;
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="{{ $profile?->full_name ?? $candidate->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-sm shrink-0">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($profile?->full_name ?? $candidate->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-semibold text-gray-900 dark:text-white block">
                                            {{ $profile?->full_name ?? $candidate->name }}
                                        </span>
                                        <span class="text-[11px] text-gray-500 dark:text-slate-400 block">
                                            NIK: {{ $profile?->nik ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 space-y-0.5">
                                <div class="text-gray-900 dark:text-white font-medium">{{ $candidate->email }}</div>
                                <div class="text-[11px] text-gray-500 dark:text-slate-400">
                                    {{ $profile?->phone ?? '-' }} • {{ $profile?->city ?? '-' }},
                                    {{ $profile?->province ?? '-' }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @if ($latestEdu)
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 block">
                                        {{ $latestEdu->degree ?? '' }}
                                        @if($latestEdu->major && $latestEdu->major !== '-')
                                            - {{ $latestEdu->major }}
                                        @endif
                                        @if($latestEdu->study_program && $latestEdu->study_program !== $latestEdu->major)
                                            <span class="text-xs font-normal text-gray-500 dark:text-slate-400">({{ $latestEdu->study_program }})</span>
                                        @endif
                                    </span>
                                    <span
                                        class="text-[11px] text-gray-500 dark:text-slate-400 block">{{ $latestEdu->school_name ?? ($latestEdu->institution_name ?? '-') }}</span>
                                @else
                                    <span class="text-gray-400 dark:text-slate-500 italic">Belum diisi</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $appCount > 0 ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                    {{ $appCount }} Lowongan
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <button @click="openDetailModal({{ \Illuminate\Support\Js::from($candidate) }})"
                                    class="px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 font-semibold transition text-xs">
                                    Detail Profil Lengkap
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-slate-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                    </svg>
                                    <span>Tidak ada data kandidat ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($candidates->hasPages() || $perPage != 10)
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 dark:text-slate-400">Tampilkan</span>
                    <select wire:model.live="perPage" class="pl-2.5 pr-7 py-1.5 text-xs rounded-lg bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-xs text-gray-500 dark:text-slate-400">data per halaman</span>
                </div>
                @if ($candidates->hasPages())
                    <div>
                        {{ $candidates->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Modal Detail Kandidat Lengkap (8 Relasi) -->
    <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 dark:bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
        x-cloak>

        <div @click.away="showDetailModal = false"
            class="bg-white dark:bg-slate-900 rounded-3xl max-w-4xl w-full p-6 shadow-2xl border border-gray-200 dark:border-slate-800 space-y-6 my-8 max-h-[90vh] overflow-y-auto custom-scrollbar">

            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <template x-if="(detailData.applicant_profile && detailData.applicant_profile.photo) || detailData.avatar">
                        <img :src="((detailData.applicant_profile && detailData.applicant_profile.photo) || detailData.avatar).startsWith('http') 
                            ? ((detailData.applicant_profile && detailData.applicant_profile.photo) || detailData.avatar) 
                            : ('/storage/' + ((detailData.applicant_profile && detailData.applicant_profile.photo) || detailData.avatar))" 
                            :alt="detailData.applicant_profile ? (detailData.applicant_profile.full_name || detailData.name) : detailData.name" 
                            class="w-12 h-12 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-md shrink-0">
                    </template>
                    <template x-if="(!detailData.applicant_profile || !detailData.applicant_profile.photo) && !detailData.avatar">
                        <div
                            class="w-12 h-12 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-base flex items-center justify-center shrink-0 shadow-2xs">
                            <span
                                x-text="detailData.applicant_profile ? (detailData.applicant_profile.full_name || detailData.name || 'K').substring(0, 2).toUpperCase() : (detailData.name ? detailData.name.substring(0,2).toUpperCase() : 'K')"></span>
                        </div>
                    </template>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white"
                            x-text="detailData.applicant_profile ? (detailData.applicant_profile.full_name || detailData.name) : detailData.name">
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400" x-text="detailData.email"></p>
                    </div>
                </div>
                <button @click="showDetailModal = false"
                    class="p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content Modal: Detail 8 Relasi -->
            <div class="space-y-6 text-xs">
                <!-- 1. Informasi Pribadi & Biodata -->
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informasi Pribadi & Kontak
                    </h4>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-gray-50 dark:bg-slate-800/40 p-4 rounded-2xl border border-gray-100 dark:border-slate-800">
                        <div>
                            <span class="text-gray-400 block">NIK:</span>
                            <span class="font-semibold text-gray-800 dark:text-slate-200"
                                x-text="detailData.applicant_profile ? (detailData.applicant_profile.nik || '-') : '-'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">NPWP:</span>
                            <span class="font-semibold text-gray-800 dark:text-slate-200"
                                x-text="detailData.applicant_profile ? (detailData.applicant_profile.npwp || '-') : '-'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Nomor Telepon:</span>
                            <span class="font-semibold text-gray-800 dark:text-slate-200"
                                x-text="detailData.applicant_profile ? (detailData.applicant_profile.phone || '-') : '-'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Jenis Kelamin:</span>
                            <span class="font-semibold text-gray-800 dark:text-slate-200"
                                x-text="detailData.applicant_profile ? (detailData.applicant_profile.gender || '-') : '-'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Tempat, Tgl Lahir:</span>
                            <span class="font-semibold text-gray-800 dark:text-slate-200"
                                x-text="detailData.applicant_profile ? ((detailData.applicant_profile.birth_place || '') + ', ' + (detailData.applicant_profile.birth_date || '')) : '-'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Kota & Provinsi:</span>
                            <span class="font-semibold text-gray-800 dark:text-slate-200"
                                x-text="detailData.applicant_profile ? ((detailData.applicant_profile.city || '') + ', ' + (detailData.applicant_profile.province || '')) : '-'"></span>
                        </div>
                        <div class="sm:col-span-2 md:col-span-3">
                            <span class="text-gray-400 block">Alamat Lengkap:</span>
                            <span class="font-semibold text-gray-800 dark:text-slate-200"
                                x-text="detailData.applicant_profile ? (detailData.applicant_profile.address || '-') : '-'"></span>
                        </div>
                    </div>
                </div>

                <!-- Tentang Kandidat -->
                <template x-if="detailData.applicant_profile && detailData.applicant_profile.about_me">
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2">Tentang Saya (Ringkasan Profil)</h4>
                        <p class="text-xs text-gray-600 dark:text-slate-300 leading-relaxed bg-gray-50 dark:bg-slate-800/40 p-3 rounded-xl border border-gray-200/60 dark:border-slate-800"
                            x-text="detailData.applicant_profile.about_me"></p>
                    </div>
                </template>

                <!-- 2. Pengalaman Kerja (work_experiences) -->
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Pengalaman Kerja
                    </h4>
                    <template
                        x-if="detailData.applicant_profile && detailData.applicant_profile.work_experiences && detailData.applicant_profile.work_experiences.length > 0">
                        <div class="space-y-3">
                            <template x-for="work in detailData.applicant_profile.work_experiences"
                                :key="work.id">
                                <div
                                    class="p-4 bg-gray-50 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="font-bold text-gray-900 dark:text-white text-xs block"
                                                x-text="work.position"></span>
                                            <span
                                                class="text-indigo-600 dark:text-indigo-400 font-semibold text-[11px]"
                                                x-text="work.company_name + ' • (' + work.employment_type + ')'"></span>
                                        </div>
                                        <span
                                            class="text-gray-400 text-[11px] bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg"
                                            x-text="formatDate(work.start_date) + ' s/d ' + (work.currently_working ? 'Sekarang' : formatDate(work.end_date))"></span>
                                    </div>
                                    <p x-show="work.description"
                                        class="text-gray-600 dark:text-slate-300 text-[11px] pt-1"
                                        x-text="work.description"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template
                        x-if="!detailData.applicant_profile || !detailData.applicant_profile.work_experiences || detailData.applicant_profile.work_experiences.length === 0">
                        <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">
                            Belum ada riwayat pengalaman kerja.</div>
                    </template>
                </div>

                <!-- 3. Riwayat Pendidikan (educations) -->
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                        Riwayat Pendidikan
                    </h4>
                    <template
                        x-if="detailData.applicant_profile && detailData.applicant_profile.educations && detailData.applicant_profile.educations.length > 0">
                        <div class="space-y-2">
                            <template x-for="edu in detailData.applicant_profile.educations" :key="edu.id">
                                <div
                                    class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex justify-between items-center">
                                    <div>
                                        <span class="font-semibold text-gray-900 dark:text-white block"
                                            x-text="edu.school_name || edu.institution_name"></span>
                                        <div class="flex items-center flex-wrap gap-1.5 mt-0.5">
                                            <span class="text-indigo-600 dark:text-indigo-400 font-medium text-[11px]"
                                                x-text="edu.degree || 'Pendidikan'"></span>
                                            <span class="text-gray-400 text-[11px]" x-show="edu.major && edu.major !== '-'">•</span>
                                            <span class="text-gray-700 dark:text-slate-300 font-medium text-[11px]"
                                                x-show="edu.major && edu.major !== '-'">
                                                Jurusan: <span class="font-semibold" x-text="edu.major"></span>
                                            </span>
                                            <span class="text-gray-400 text-[11px]" x-show="edu.study_program && edu.study_program !== edu.major">•</span>
                                            <span class="text-gray-600 dark:text-slate-400 text-[11px]"
                                                x-show="edu.study_program && edu.study_program !== edu.major">
                                                Prodi: <span class="font-medium" x-text="edu.study_program"></span>
                                            </span>
                                            <span class="text-gray-400 text-[11px]" x-show="edu.gpa">•</span>
                                            <span class="text-emerald-600 dark:text-emerald-400 font-medium text-[11px]"
                                                x-show="edu.gpa"
                                                x-text="'IPK/Nilai: ' + edu.gpa"></span>
                                        </div>
                                    </div>
                                    <span class="text-gray-400 text-[11px] shrink-0"
                                        x-text="edu.start_year + ' - ' + (edu.end_year || 'Sekarang')"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template
                        x-if="!detailData.applicant_profile || !detailData.applicant_profile.educations || detailData.applicant_profile.educations.length === 0">
                        <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">
                            Belum ada data pendidikan.</div>
                    </template>
                </div>

                <!-- 4. Pengalaman Organisasi (organizations) -->
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-indigo-500" mlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>

                        Pengalaman Organisasi
                    </h4>
                    <template
                        x-if="detailData.applicant_profile && detailData.applicant_profile.organizations && detailData.applicant_profile.organizations.length > 0">
                        <div class="space-y-2">
                            <template x-for="org in detailData.applicant_profile.organizations" :key="org.id">
                                <div
                                    class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex justify-between items-center">
                                    <div>
                                        <span class="font-semibold text-gray-900 dark:text-white block"
                                            x-text="org.name"></span>
                                        <span class="text-gray-500 dark:text-slate-400 text-[11px]"
                                            x-text="'Jabatan: ' + org.position"></span>
                                    </div>
                                    <span class="text-gray-400 text-[11px]"
                                        x-text="(org.start_month || '') + ' ' + org.start_year + ' - ' + (org.is_active ? 'Aktif' : ((org.end_month || '') + ' ' + (org.end_year || '')))"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template
                        x-if="!detailData.applicant_profile || !detailData.applicant_profile.organizations || detailData.applicant_profile.organizations.length === 0">
                        <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">
                            Belum ada riwayat organisasi.</div>
                    </template>
                </div>

                <!-- 4.1 Prestasi & Penghargaan (achievements) -->
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        Prestasi & Penghargaan
                    </h4>
                    <template x-if="detailData.applicant_profile && detailData.applicant_profile.achievements && detailData.applicant_profile.achievements.length > 0">
                        <div class="space-y-2">
                            <template x-for="ach in detailData.applicant_profile.achievements" :key="ach.id">
                                <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-900 dark:text-white truncate" x-text="ach.name"></span>
                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-md border border-amber-200 dark:border-amber-800 shrink-0" x-text="ach.scale"></span>
                                        </div>
                                        <span class="text-gray-500 dark:text-slate-400 text-[11px]" x-text="(ach.month || '') + ' ' + (ach.year || '')"></span>
                                    </div>
                                    <template x-if="ach.certificate_path">
                                        <a :href="ach.certificate_path.startsWith('http') ? ach.certificate_path : ('{{ asset('storage') }}/' + ach.certificate_path.replace(/^\/+/, ''))" 
                                           target="_blank" 
                                           class="px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/80 rounded-lg border border-indigo-200/80 dark:border-indigo-800/80 transition shrink-0 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Berkas</span>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.achievements || detailData.applicant_profile.achievements.length === 0">
                        <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">Belum ada prestasi dicantumkan.</div>
                    </template>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 5. Sertifikasi (certifications) -->
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            Sertifikasi
                        </h4>
                        <template
                            x-if="detailData.applicant_profile && detailData.applicant_profile.certifications && detailData.applicant_profile.certifications.length > 0">
                            <div class="space-y-2">
                                <template x-for="cert in detailData.applicant_profile.certifications"
                                    :key="cert.id">
                                    <div
                                        class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex items-center justify-between gap-3">
                                        <span class="font-semibold text-gray-900 dark:text-white truncate"
                                            x-text="cert.name"></span>
                                        <template x-if="cert.certificate_path">
                                            <a :href="cert.certificate_path.startsWith('http') ? cert.certificate_path : ('{{ asset('storage') }}/' + cert.certificate_path.replace(/^\/+/, ''))"
                                                target="_blank"
                                                class="px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/80 rounded-lg border border-indigo-200/80 dark:border-indigo-800/80 transition shrink-0 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Lihat Berkas</span>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template
                            x-if="!detailData.applicant_profile || !detailData.applicant_profile.certifications || detailData.applicant_profile.certifications.length === 0">
                            <div
                                class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">
                                Belum ada sertifikasi.</div>
                        </template>
                    </div>

                    <!-- 6. Pelatihan & Kursus (trainings) -->
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            Pelatihan / Kursus
                        </h4>
                        <template
                            x-if="detailData.applicant_profile && detailData.applicant_profile.trainings && detailData.applicant_profile.trainings.length > 0">
                            <div class="space-y-2">
                                <template x-for="tr in detailData.applicant_profile.trainings" :key="tr.id">
                                    <div
                                        class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex items-center justify-between gap-3">
                                        <span class="font-semibold text-gray-900 dark:text-white truncate"
                                            x-text="tr.name"></span>
                                        <template x-if="tr.certificate_path">
                                            <a :href="tr.certificate_path.startsWith('http') ? tr.certificate_path : ('{{ asset('storage') }}/' + tr.certificate_path.replace(/^\/+/, ''))" 
                                                target="_blank"
                                                class="px-2.5 py-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/80 rounded-lg border border-indigo-200/80 dark:border-indigo-800/80 transition shrink-0 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Lihat Berkas</span>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template
                            x-if="!detailData.applicant_profile || !detailData.applicant_profile.trainings || detailData.applicant_profile.trainings.length === 0">
                            <div
                                class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">
                                Belum ada riwayat pelatihan.</div>
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- 7. Keahlian / Skills -->
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2">Keahlian (Skills)</h4>
                        <template
                            x-if="detailData.applicant_profile && detailData.applicant_profile.skills && detailData.applicant_profile.skills.length > 0">
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="sk in detailData.applicant_profile.skills" :key="sk.id">
                                    <span
                                        class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-300 rounded-lg text-[11px] font-semibold"
                                        x-text="sk.name || sk.skill_name"></span>
                                </template>
                            </div>
                        </template>
                        <template
                            x-if="!detailData.applicant_profile || !detailData.applicant_profile.skills || detailData.applicant_profile.skills.length === 0">
                            <span class="text-gray-400 italic text-[11px]">Belum ada skill.</span>
                        </template>
                    </div>

                    <!-- 8. Kemampuan Bahasa (languages) -->
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2">Kemampuan Bahasa</h4>
                        <template
                            x-if="detailData.applicant_profile && detailData.applicant_profile.languages && detailData.applicant_profile.languages.length > 0">
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="lang in detailData.applicant_profile.languages"
                                    :key="lang.id">
                                    <span
                                        class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-300 rounded-lg text-[11px] font-semibold"
                                        x-text="lang.name"></span>
                                </template>
                            </div>
                        </template>
                        <template
                            x-if="!detailData.applicant_profile || !detailData.applicant_profile.languages || detailData.applicant_profile.languages.length === 0">
                            <span class="text-gray-400 italic text-[11px]">Belum ada data bahasa.</span>
                        </template>
                    </div>

                    <!-- 9. Media Sosial (social_medias) -->
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                            <span>Media Sosial & Portofolio</span>
                        </h4>
                        <template
                            x-if="detailData.applicant_profile && detailData.applicant_profile.social_medias && detailData.applicant_profile.social_medias.length > 0">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="sm in detailData.applicant_profile.social_medias" :key="sm.id">
                                    <a :href="sm.url" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50/80 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 font-semibold text-[11px] border border-indigo-200/60 dark:border-indigo-800/60 transition group shadow-2xs">
                                        <span x-text="sm.platform_name"></span>
                                        <svg class="w-3 h-3 text-indigo-400 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </template>
                            </div>
                        </template>
                        <template
                            x-if="!detailData.applicant_profile || !detailData.applicant_profile.social_medias || detailData.applicant_profile.social_medias.length === 0">
                            <span class="text-gray-400 italic text-[11px]">Belum ada media sosial.</span>
                        </template>
                    </div>
                </div>

                <!-- Riwayat Lamaran Lowongan -->
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Riwayat Lowongan yang Dilamar
                    </h4>
                    <template
                        x-if="detailData.applicant_profile && detailData.applicant_profile.job_applications && detailData.applicant_profile.job_applications.length > 0">
                        <div class="space-y-2">
                            <template x-for="app in detailData.applicant_profile.job_applications"
                                :key="app.id">
                                <div
                                    class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex justify-between items-center">
                                    <div>
                                        <span class="font-semibold text-gray-900 dark:text-white block"
                                            x-text="app.job ? app.job.title : 'Lowongan'"></span>
                                        <span class="text-gray-500 dark:text-slate-400 text-[11px]"
                                            x-text="app.job && app.job.company ? app.job.company.name : '-'"></span>
                                    </div>
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-200 dark:bg-slate-700 text-gray-800 dark:text-slate-200"
                                        x-text="app.status"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template
                        x-if="!detailData.applicant_profile || !detailData.applicant_profile.job_applications || detailData.applicant_profile.job_applications.length === 0">
                        <div class="p-3 bg-gray-50 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-[11px]">
                            Kandidat ini belum melamar lowongan apa pun.
                        </div>
                    </template>
                </div>

                <!-- Dokumen CV / Resume -->
                <template x-if="detailData.applicant_profile">
                    <div class="pt-3 border-t border-gray-100 dark:border-slate-800 space-y-2">
                        <span class="font-bold text-gray-900 dark:text-white text-xs block">Dokumen CV / Resume Kandidat</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <template x-if="detailData.applicant_profile.cv_file_url || detailData.applicant_profile.cv_file_path">
                                <a :href="detailData.applicant_profile.cv_file_url ? detailData.applicant_profile.cv_file_url : ('/storage/' + detailData.applicant_profile.cv_file_path)" target="_blank"
                                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Unduh / Buka File CV</span>
                                </a>
                            </template>
                            <template x-if="!detailData.applicant_profile.cv_file_url && !detailData.applicant_profile.cv_file_path">
                                <span class="text-xs text-gray-400 dark:text-slate-400 italic">Kandidat belum mengunggah file CV.</span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Modal -->
            <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex justify-end">
                <button @click="showDetailModal = false"
                    class="px-4 py-2 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 font-semibold rounded-xl text-xs hover:bg-gray-200 dark:hover:bg-slate-700 transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
