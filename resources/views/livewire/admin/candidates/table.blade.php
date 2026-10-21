@php
    $isAdmin = auth()->check() && (auth()->user()->role_id == 1 || in_array(strtolower(auth()->user()->role?->name ?? ''), ['admin', 'superadmin']));
    $isRecruiter = !$isAdmin && (auth()->check() && (auth()->user()->role_id == 2 || strtolower(auth()->user()->role?->name ?? '') === 'recruiter' || (bool) auth()->user()->is_recruiter));
    $pdfUrlPrefix = ($isRecruiter && !$isAdmin) ? url('/recruiter/candidates') : url('/admin/candidates');
@endphp

<div class="space-y-6" x-data="{ activeCandidateModal: null }">
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
                            $photoUrl = $profile?->photo_url ?? $candidate->avatar_url;
                            $candidateName = $profile?->full_name ?? $candidate->name;
                            $candidateInitials = strtoupper(substr($candidateName, 0, 2));
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-10 h-10 shrink-0">
                                        @if ($photoUrl)
                                            <img src="{{ $photoUrl }}" alt="{{ $candidateName }}" referrerpolicy="no-referrer" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-700 shadow-sm" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                            <div class="hidden w-10 h-10 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs items-center justify-center shadow-2xs">
                                                {{ $candidateInitials }}
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-[#93F514] font-bold text-xs flex items-center justify-center shadow-2xs">
                                                {{ $candidateInitials }}
                                            </div>
                                        @endif
                                    </div>
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
                                <button type="button" @click="activeCandidateModal = {{ $candidate->id }}"
                                    class="px-3.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 font-semibold transition text-xs inline-flex items-center gap-1.5 cursor-pointer" title="Lihat Profil Lengkap">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Detail Profil Lengkap</span>
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

    {{-- MODAL DETAIL KANDIDAT (Portrait Document View - Client-side Alpine) --}}
    @include('livewire.admin.candidates.partials.detail-modal-portrait')
</div>
