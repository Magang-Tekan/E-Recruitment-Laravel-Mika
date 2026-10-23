{{-- Detail Candidate Modal (Portrait Document View - Pre-rendered Blade with Alpine ID Switch) --}}
@foreach ($candidates as $modalCandidate)
    @php
        $prof = $modalCandidate->applicantProfile;
        $fam = $prof?->family;
        $pref = $prof?->jobPreference;
        $educations = $prof?->educations ?? collect();
        $works = $prof?->workExperiences ?? collect();
        $orgs = $prof?->organizations ?? collect();
        $achievements = $prof?->achievements ?? collect();
        $skills = $prof?->skills ?? collect();
        $languages = $prof?->languages ?? collect();
        $certifications = $prof?->certifications ?? collect();
        $trainings = $prof?->trainings ?? collect();
        $socials = $prof?->socialMedias ?? collect();
        $apps = $prof?->jobApplications ?? collect();

        $photoUrl = $prof?->photo_url ?? $modalCandidate->avatar_url;
        $cvUrl = $prof?->cv_file_url ?? ($prof?->cv_file_path ? asset('storage/' . $prof->cv_file_path) : null);
        $candidateName = $prof?->full_name ?? $modalCandidate->name;
    @endphp

    <div x-show="activeCandidateModal === {{ $modalCandidate->id }}" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title-candidate-{{ $modalCandidate->id }}" 
         role="dialog" 
         aria-modal="true"
         @keydown.escape.window="if (activeCandidateModal === {{ $modalCandidate->id }}) activeCandidateModal = null">
        
        {{-- Backdrop --}}
        <div x-show="activeCandidateModal === {{ $modalCandidate->id }}" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="ease-in duration-200" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0" 
            @click="activeCandidateModal = null" 
            class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/75 backdrop-blur-sm"></div>

        {{-- Centered Modal Wrapper --}}
        <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-2 sm:p-4 md:p-6 pointer-events-none">
            
            {{-- Modal Dialog Container: Portrait Optimized (max-w-4xl) --}}
            <div x-show="activeCandidateModal === {{ $modalCandidate->id }}" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="pointer-events-auto relative w-full sm:max-w-4xl bg-slate-100 dark:bg-[#070b14] rounded-2xl sm:rounded-3xl text-left overflow-hidden shadow-2xl border border-gray-200 dark:border-slate-800 max-h-[92vh] flex flex-col transform transition-all">
                
                {{-- 1. Top Sticky Toolbar --}}
                <div class="px-4 sm:px-6 py-3 bg-white/95 dark:bg-[#0d1527]/95 backdrop-blur-md border-b border-gray-200 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0 z-20">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/50">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white leading-tight" id="modal-title-candidate-{{ $modalCandidate->id }}">Detail Profil Kandidat</h3>
                        </div>
                    </div>

                    {{-- Header Action Buttons --}}
                    <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                        {{-- <a href="{{ (auth()->check() && (auth()->user()->role_id == 2 || strtolower(auth()->user()->role?->name ?? '') === 'recruiter' || (bool) auth()->user()->is_recruiter)) ? route('recruiter.candidate.pdf', $modalCandidate->id) : route('admin.candidate.pdf', $modalCandidate->id) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold shadow-xs transition" title="Unduh Profil Lengkap PDF">
                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Unduh PDF</span>
                        </a> --}}
                        {{-- Close Button --}}
                        <button @click="activeCandidateModal = null" class="p-1.5 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 transition shrink-0 cursor-pointer" title="Tutup Modal">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- 2. Sticky Quick-Jump Anchor Navigation Bar --}}
                <div class="px-4 sm:px-6 py-2 bg-slate-50/95 dark:bg-[#090f1d]/95 backdrop-blur-sm border-b border-gray-200/80 dark:border-slate-800 flex items-center gap-1.5 overflow-x-auto text-[11px] font-semibold text-slate-600 dark:text-slate-300 custom-scrollbar shrink-0">
                    <span class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-wider font-bold shrink-0 mr-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        Lompat:
                    </span>
                    <button type="button" @click="document.getElementById('cand-{{ $modalCandidate->id }}-sec-biodata')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                        Biodata & Kontak
                    </button>
                    <button type="button" @click="document.getElementById('cand-{{ $modalCandidate->id }}-sec-keluarga')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                        Data Keluarga
                    </button>
                    <button type="button" @click="document.getElementById('cand-{{ $modalCandidate->id }}-sec-pendidikan')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                        Pendidikan
                    </button>
                    <button type="button" @click="document.getElementById('cand-{{ $modalCandidate->id }}-sec-pengalaman')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                        Pengalaman Kerja
                    </button>
                    <button type="button" @click="document.getElementById('cand-{{ $modalCandidate->id }}-sec-prestasi')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                        Organisasi & Prestasi
                    </button>
                    <button type="button" @click="document.getElementById('cand-{{ $modalCandidate->id }}-sec-keahlian')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                        Keahlian & Medsos
                    </button>
                    <button type="button" @click="document.getElementById('cand-{{ $modalCandidate->id }}-sec-kuesioner')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                        Kuesioner (Kesehatan & Info Lain)
                    </button>
                    <button type="button" @click="document.getElementById('cand-{{ $modalCandidate->id }}-sec-lamaran')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 transition shrink-0 cursor-pointer font-bold">
                        Riwayat Lamaran
                    </button>
                </div>

                {{-- 3. Scrollable Portrait Document Container --}}
                <div id="candidate-portrait-scroll-{{ $modalCandidate->id }}" class="flex-1 overflow-y-auto p-3 sm:p-6 custom-scrollbar space-y-4">
                    
                    {{-- THE PORTRAIT DOSSIER SHEET (Lembar Dokumen Portrait) --}}
                    <div class="max-w-3xl mx-auto bg-white dark:bg-[#0D1527] rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] p-5 sm:p-10 space-y-8 relative overflow-hidden">
                        
                        {{-- A. Header Profil Kandidat --}}
                        <div class="border-b border-gray-100 dark:border-slate-800 pb-6">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                                <div class="flex items-start sm:items-center gap-5 sm:gap-6 min-w-0 flex-1">
                                    {{-- Foto Profil --}}
                                    <div class="shrink-0">
                                        @if ($photoUrl)
                                            <div class="relative w-16 h-16 sm:w-20 sm:h-20 shrink-0">
                                                <img src="{{ $photoUrl }}" 
                                                    alt="{{ $candidateName }}" 
                                                    referrerpolicy="no-referrer" 
                                                    class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-2xl border border-gray-200 dark:border-slate-700 shadow-xs"
                                                    onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                                <div class="hidden w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400 font-semibold text-xl items-center justify-center shadow-xs">
                                                    <span>{{ strtoupper(substr($candidateName, 0, 2)) }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400 font-semibold text-xl flex items-center justify-center shadow-xs shrink-0">
                                                <span>{{ strtoupper(substr($candidateName, 0, 2)) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Identitas & Kontak Singkat --}}
                                    <div class="space-y-1 min-w-0 flex-1">
                                        <div class="flex items-center gap-2.5 flex-wrap">
                                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                                                {{ $candidateName }}
                                            </h1>
                                            <span class="text-xs font-mono text-gray-500 dark:text-slate-400 bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded border border-gray-200 dark:border-slate-700">
                                                #KND-{{ $modalCandidate->id }}
                                            </span>
                                        </div>
                                        <div class="flex items-center flex-wrap gap-1.5 text-xs sm:text-sm text-gray-600 dark:text-slate-300">
                                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $modalCandidate->email }}</span>
                                            @if ($prof?->phone)
                                                <span class="text-gray-400 dark:text-slate-600">• <span class="text-gray-700 dark:text-slate-300 font-normal">{{ $prof->phone }}</span></span>
                                            @endif
                                            @if ($prof?->city || $prof?->province)
                                                <span class="text-gray-400 dark:text-slate-600">• <span class="text-gray-500 dark:text-slate-400 font-normal">{{ trim(($prof->city ?? '') . ($prof->province ? ', ' . $prof->province : ''), ', ') }}</span></span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-400 dark:text-slate-500 flex items-center gap-1.5 pt-0.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span>Terdaftar sebagai Kandidat Sistem</span>
                                        </p>
                                    </div>
                                </div>

                                {{-- Status Badge di Header Profil --}}
                                <div class="shrink-0 self-start sm:self-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                        <span>{{ $apps->count() }} Lamaran</span>
                                    </span>
                                </div>
                            </div>

                            {{-- Fast Contact Badges Grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5 mt-5 pt-4 border-t border-gray-100 dark:border-slate-800/80 text-[11.5px]">
                                <div class="p-2.5 rounded-xl bg-gray-50/70 dark:bg-slate-800/40 border border-gray-100 dark:border-slate-800 flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] text-gray-400 dark:text-slate-500 block">Email</span>
                                        <span class="font-semibold text-gray-800 dark:text-slate-200 truncate block">{{ $modalCandidate->email }}</span>
                                    </div>
                                </div>

                                <div class="p-2.5 rounded-xl bg-gray-50/70 dark:bg-slate-800/40 border border-gray-100 dark:border-slate-800 flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] text-gray-400 dark:text-slate-500 block">Telepon / WhatsApp</span>
                                        <span class="font-semibold text-gray-800 dark:text-slate-200 truncate block">{{ $prof?->phone ?: '-' }}</span>
                                    </div>
                                </div>

                                <div class="p-2.5 rounded-xl bg-gray-50/70 dark:bg-slate-800/40 border border-gray-100 dark:border-slate-800 flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] text-gray-400 dark:text-slate-500 block">NIK</span>
                                        <span class="font-semibold text-gray-800 dark:text-slate-200 truncate block font-mono">{{ $prof?->nik ?: '-' }}</span>
                                    </div>
                                </div>

                                <div class="p-2.5 rounded-xl bg-gray-50/70 dark:bg-slate-800/40 border border-gray-100 dark:border-slate-800 flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10px] text-gray-400 dark:text-slate-500 block">Domisili</span>
                                        <span class="font-semibold text-gray-800 dark:text-slate-200 truncate block">{{ trim(($prof?->city ?? '') . ($prof?->province ? ', ' . $prof->province : ''), ', ') ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- B. Tentang Kandidat (Bio Singkat) --}}
                        @if ($prof?->about_me)
                            <div class="p-4 sm:p-5 rounded-2xl bg-indigo-50/40 dark:bg-[#14203A]/50 border border-indigo-100 dark:border-[#1D2E54]">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-400 mb-2 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                    Ringkasan Diri Kandidat (Bio)
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-700 dark:text-slate-200 leading-relaxed italic">
                                    “{{ $prof->about_me }}”
                                </p>
                            </div>
                        @endif

                        {{-- SEKSI 01: INFORMASI PRIBADI & KONTAK --}}
                        <section id="cand-{{ $modalCandidate->id }}-sec-biodata" class="space-y-3 pt-2">
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">01</span>
                                    <span>Informasi Pribadi & Kontak</span>
                                </h2>
                                <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">Biodata Lengkap</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-gray-50/80 dark:bg-slate-800/40 p-4 rounded-2xl border border-gray-100 dark:border-slate-800 text-xs">
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">NIK:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 font-mono">{{ $prof?->nik ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">NPWP:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 font-mono">{{ $prof?->npwp ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Email:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 break-all">{{ $modalCandidate->email }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Nomor Telepon / WhatsApp:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $prof?->phone ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Jenis Kelamin:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $prof?->gender ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Tempat, Tanggal Lahir:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">
                                        {{ ($prof?->birth_place ?? '') . ($prof?->birth_date ? ', ' . \Carbon\Carbon::parse($prof->birth_date)->translatedFormat('d M Y') : '') ?: '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Agama:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $prof?->religion ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Golongan Darah:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $prof?->blood_type ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Suku:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $prof?->ethnicity ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Kebangsaan:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $prof?->nationality ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Status Pernikahan:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 capitalize">
                                        {{ $prof?->marital_status ? (ucfirst($prof->marital_status) . ($prof->marriage_date ? ' (' . \Carbon\Carbon::parse($prof->marriage_date)->translatedFormat('d M Y') . ')' : '')) : '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Transportasi Harian:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $prof?->daily_transportation ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Jarak ke Tempat Kerja:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $prof?->work_distance_km ? ($prof->work_distance_km . ' km') : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Kota & Provinsi:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">
                                        {{ trim(($prof?->city ?? '') . ($prof?->province ? ', ' . $prof->province : ''), ', ') ?: '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Kode Pos:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $prof?->postal_code ?: '-' }}</span>
                                </div>
                                <div class="sm:col-span-2 md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2.5 border-t border-gray-100 dark:border-slate-800">
                                    <div>
                                        <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Alamat Sesuai KTP:</span>
                                        <span class="font-semibold text-gray-800 dark:text-slate-200 leading-relaxed">{{ $prof?->ktp_address ?: '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Alamat Domisili Saat Ini:</span>
                                        <span class="font-semibold text-gray-800 dark:text-slate-200 leading-relaxed">{{ $prof?->address ?: '-' }}</span>
                                    </div>
                                </div>
                                @if(!empty($prof?->driving_licenses) && is_array($prof->driving_licenses))
                                    @php
                                        $validLics = array_filter($prof->driving_licenses, fn($l) => !empty($l['type']) || !empty($l['number']));
                                    @endphp
                                    @if(!empty($validLics))
                                        <div class="sm:col-span-2 md:col-span-3 pt-2.5 border-t border-gray-100 dark:border-slate-800">
                                            <span class="text-gray-400 dark:text-slate-500 block text-[10.5px] mb-1.5">Kepemilikan SIM (Surat Izin Mengemudi):</span>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($validLics as $lic)
                                                    <span class="px-2.5 py-1 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-medium text-gray-800 dark:text-slate-200 shadow-xs">
                                                        <strong class="text-emerald-600 dark:text-emerald-400">{{ $lic['type'] ?: 'SIM' }}:</strong> {{ $lic['number'] ?: '-' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- Dokumen CV --}}
                            <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-100 dark:border-rose-900/40">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-gray-900 dark:text-white block">Dokumen CV / Resume Kandidat</span>
                                        <p class="text-[11px] text-gray-500 dark:text-slate-400">Berkas Curriculum Vitae resmi yang diunggah kandidat.</p>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    @if ($cvUrl)
                                        <a href="{{ $cvUrl }}" target="_blank"
                                            class="px-3.5 py-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded-xl border border-emerald-200 dark:border-emerald-800 transition inline-flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Buka File CV Asli
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum mengunggah CV</span>
                                    @endif
                                </div>
                            </div>
                        </section>

                        {{-- SEKSI 02: DATA KELUARGA & KONTAK DARURAT --}}
                        <section id="cand-{{ $modalCandidate->id }}-sec-keluarga" class="space-y-3 pt-2">
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">02</span>
                                    <span>Data Keluarga & Kontak Darurat</span>
                                </h2>
                                <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">Latar Belakang Keluarga</span>
                            </div>

                            {{-- Ringkasan Susunan Saudara --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="p-3 bg-gray-50/80 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Urutan Kelahiran:</span>
                                        <span class="font-bold text-gray-900 dark:text-white">
                                            {{ $prof?->child_sequence ? ('Anak ke-' . $prof->child_sequence) : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-3 bg-gray-50/80 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Jumlah Bersaudara:</span>
                                        <span class="font-bold text-gray-900 dark:text-white">
                                            {{ $prof?->total_siblings ? ($prof->total_siblings . ' Bersaudara') : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if ($fam && !empty($fam->family_members) && count($fam->family_members) > 0)
                                {{-- Tabel Lengkap Anggota Keluarga --}}
                                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30 mb-3">
                                    <table class="w-full text-left text-[11px] text-gray-700 dark:text-slate-300">
                                        <thead class="bg-gray-100 dark:bg-slate-800 text-gray-800 dark:text-slate-200 uppercase font-bold text-[10px]">
                                            <tr>
                                                <th class="py-2 px-2.5">Hubungan</th>
                                                <th class="py-2 px-2.5">Nama</th>
                                                <th class="py-2 px-2.5">Tgl Lahir</th>
                                                <th class="py-2 px-2.5">Tempat Lahir</th>
                                                <th class="py-2 px-2 text-center">Usia</th>
                                                <th class="py-2 px-2.5">Pendidikan</th>
                                                <th class="py-2 px-2.5">Pekerjaan</th>
                                                <th class="py-2 px-2.5">Perusahaan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                                            @foreach($fam->family_members as $mem)
                                                <tr class="hover:bg-white dark:hover:bg-slate-800/60">
                                                    <td class="py-1.5 px-2.5 font-bold text-gray-900 dark:text-white">{{ $mem['relation'] ?? '-' }}</td>
                                                    <td class="py-1.5 px-2.5 font-medium">{{ $mem['name'] ?? '-' }}</td>
                                                    <td class="py-1.5 px-2.5 text-gray-500 dark:text-slate-400">{{ !empty($mem['birth_date']) ? \Carbon\Carbon::parse($mem['birth_date'])->translatedFormat('d M Y') : '-' }}</td>
                                                    <td class="py-1.5 px-2.5 text-gray-500 dark:text-slate-400">{{ $mem['birth_place'] ?? '-' }}</td>
                                                    <td class="py-1.5 px-2 text-center">{{ !empty($mem['age']) ? $mem['age'] . ' Thn' : '-' }}</td>
                                                    <td class="py-1.5 px-2.5">{{ $mem['education'] ?? '-' }}</td>
                                                    <td class="py-1.5 px-2.5">{{ $mem['occupation'] ?? '-' }}</td>
                                                    <td class="py-1.5 px-2.5 text-gray-500 dark:text-slate-400">{{ $mem['company'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                {{-- Ayah & Ibu Grid (Clean SVG icons - NO EMOJIS) --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                {{-- Data Ayah --}}
                                <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                    <div class="flex items-center gap-2 border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                        <div class="w-5 h-5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white text-xs">Data Ayah</span>
                                    </div>
                                    @if ($fam?->father_name)
                                        <div class="space-y-1.5 text-[11px]">
                                            <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                                <span class="text-gray-400 dark:text-slate-500">Nama:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->father_name }}</span>
                                            </div>
                                            <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                                <span class="text-gray-400 dark:text-slate-500">Tahun Lahir:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->father_birth_year ?: '-' }}</span>
                                            </div>
                                            <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                                <span class="text-gray-400 dark:text-slate-500">Pendidikan Terakhir:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->father_last_education ?: '-' }}</span>
                                            </div>
                                            <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                                <span class="text-gray-400 dark:text-slate-500">Pekerjaan:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->father_occupation ?: '-' }}</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-400 dark:text-slate-500">Perusahaan / Instansi:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->father_company ?: '-' }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-3 bg-white/50 dark:bg-slate-900/50 rounded-xl text-gray-400 italic text-center text-[11px]">
                                            Data ayah belum dilengkapi.
                                        </div>
                                    @endif
                                </div>

                                {{-- Data Ibu --}}
                                <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                    <div class="flex items-center gap-2 border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                        <div class="w-5 h-5 rounded-md bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white text-xs">Data Ibu</span>
                                    </div>
                                    @if ($fam?->mother_name)
                                        <div class="space-y-1.5 text-[11px]">
                                            <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                                <span class="text-gray-400 dark:text-slate-500">Nama:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->mother_name }}</span>
                                            </div>
                                            <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                                <span class="text-gray-400 dark:text-slate-500">Tahun Lahir:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->mother_birth_year ?: '-' }}</span>
                                            </div>
                                            <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                                <span class="text-gray-400 dark:text-slate-500">Pendidikan Terakhir:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->mother_last_education ?: '-' }}</span>
                                            </div>
                                            <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                                <span class="text-gray-400 dark:text-slate-500">Pekerjaan:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->mother_occupation ?: '-' }}</span>
                                            </div>
                                            <div class="flex justify-between py-0.5">
                                                <span class="text-gray-400 dark:text-slate-500">Perusahaan / Instansi:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->mother_company ?: '-' }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-3 bg-white/50 dark:bg-slate-900/50 rounded-xl text-gray-400 italic text-center text-[11px]">
                                            Data ibu belum dilengkapi.
                                        </div>
                                    @endif
                                </div>

                                {{-- Pasangan (Suami / Istri - jika ada) --}}
                                @if ($fam?->spouse_name)
                                    <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2 md:col-span-2">
                                        <div class="flex items-center gap-2 border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                            <div class="w-5 h-5 rounded-md bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                            <span class="font-bold text-gray-900 dark:text-white text-xs">Data Suami / Istri</span>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px]">
                                            <div>
                                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Nama Pasangan:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->spouse_name }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Pekerjaan:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->spouse_occupation ?: '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Telepon:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->spouse_phone ?: '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Tgl Lahir / Usia:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">
                                                    {{ $fam->spouse_birth_date ? \Carbon\Carbon::parse($fam->spouse_birth_date)->translatedFormat('d M Y') : '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs mt-3">
                                {{-- Kontak Darurat --}}
                                @php
                                    $emergencies = $fam?->emergency_contacts ?? [];
                                    $sameHouse = $emergencies['same_house'] ?? [];
                                    $diffHouse = $emergencies['diff_house'] ?? [];
                                @endphp
                                @if (!empty($sameHouse) || !empty($diffHouse))
                                    <div class="p-3.5 bg-rose-50/50 dark:bg-rose-950/20 rounded-2xl border border-rose-200 dark:border-rose-900/40 space-y-3 md:col-span-2">
                                        <div class="flex items-center gap-2 border-b border-rose-200/60 dark:border-rose-900/40 pb-1.5">
                                            <div class="w-6 h-6 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-xs">!</div>
                                            <span class="font-bold text-rose-700 dark:text-rose-300 text-xs">Kontak Darurat (Emergency Contact)</span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px]">
                                            @if (!empty($sameHouse))
                                                <div class="p-2.5 rounded-xl bg-white/70 dark:bg-slate-900/70 border border-rose-100 dark:border-rose-950/50 space-y-2">
                                                    <span class="font-bold text-emerald-700 dark:text-emerald-400 block text-[10.5px]">Satu Tempat Tinggal (Rumah)</span>
                                                    @foreach ($sameHouse as $i => $c)
                                                        <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800 last:border-0">
                                                            <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $c['name'] ?? '-' }} ({{ $c['relation'] ?? '-' }})</span>
                                                            <span class="text-rose-600 dark:text-rose-400 font-bold">{{ $c['phone'] ?? '-' }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if (!empty($diffHouse))
                                                <div class="p-2.5 rounded-xl bg-white/70 dark:bg-slate-900/70 border border-rose-100 dark:border-rose-950/50 space-y-2">
                                                    <span class="font-bold text-blue-700 dark:text-blue-400 block text-[10.5px]">Tidak Satu Tempat Tinggal (Rumah)</span>
                                                    @foreach ($diffHouse as $i => $c)
                                                        <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800 last:border-0">
                                                            <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $c['name'] ?? '-' }} ({{ $c['relation'] ?? '-' }})</span>
                                                            <span class="text-rose-600 dark:text-rose-400 font-bold">{{ $c['phone'] ?? '-' }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($fam?->emergency_contact_name || $fam?->emergency_contact_phone)
                                    <div class="p-3.5 bg-rose-50/50 dark:bg-rose-950/20 rounded-2xl border border-rose-200 dark:border-rose-900/40 space-y-2 md:col-span-2">
                                        <div class="flex items-center gap-2 border-b border-rose-200/60 dark:border-rose-900/40 pb-1.5">
                                            <div class="w-6 h-6 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-xs">!</div>
                                            <span class="font-bold text-rose-700 dark:text-rose-300 text-xs">Kontak Darurat (Emergency Contact)</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px]">
                                            <div>
                                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Nama Kontak:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->emergency_contact_name ?: '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Hubungan:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->emergency_contact_relation ?: '-' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Nomor Telepon:</span>
                                                <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $fam->emergency_contact_phone ?: '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </section>

                        {{-- SEKSI 03: RIWAYAT PENDIDIKAN --}}
                        <section id="cand-{{ $modalCandidate->id }}-sec-pendidikan" class="space-y-3 pt-2">
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">03</span>
                                    <span>Riwayat Pendidikan</span>
                                </h2>
                                <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">
                                    {{ $educations->count() }} Riwayat
                                </span>
                            </div>

                            <div class="space-y-2.5">
                                @forelse ($educations->sortByDesc('end_year') as $edu)
                                    <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-gray-900 dark:text-white text-xs block">
                                                    {{ $edu->school_name ?? ($edu->institution_name ?? '-') }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/60">
                                                    {{ $edu->degree ?: 'Pendidikan' }}
                                                </span>
                                            </div>
                                            <div class="flex items-center flex-wrap gap-1.5 mt-1 text-[11px] text-gray-600 dark:text-slate-300">
                                                @if ($edu->major && $edu->major !== '-')
                                                    <span>Jurusan: <strong class="font-semibold text-gray-800 dark:text-slate-200">{{ $edu->major }}</strong></span>
                                                @endif
                                                @if ($edu->study_program && $edu->study_program !== $edu->major)
                                                    <span>• Prodi: <span>{{ $edu->study_program }}</span></span>
                                                @endif
                                                @if ($edu->gpa)
                                                    <span>• <strong class="text-emerald-600 dark:text-emerald-400 font-semibold">IPK: {{ $edu->gpa }}</strong></span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 self-start sm:self-center">
                                            @if ($edu->certificate_path)
                                                <a href="{{ \Illuminate\Support\Str::startsWith($edu->certificate_path, ['http://', 'https://']) ? $edu->certificate_path : asset('storage/' . $edu->certificate_path) }}" target="_blank"
                                                    class="px-2.5 py-1 text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    Lihat Ijazah
                                                </a>
                                            @endif
                                            <span class="text-gray-500 dark:text-slate-400 text-[11px] font-medium bg-white dark:bg-slate-900 px-2.5 py-1 rounded-xl border border-gray-200 dark:border-slate-800">
                                                {{ $edu->start_year ?: '-' }} - {{ $edu->end_year ?: 'Sekarang' }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 bg-gray-50/50 dark:bg-slate-800/20 rounded-2xl text-gray-400 dark:text-slate-500 italic text-[11px] text-center border border-gray-100 dark:border-slate-800">
                                        Belum ada data riwayat pendidikan formal yang dicantumkan.
                                    </div>
                                @endforelse
                            </div>
                        </section>

                        {{-- SEKSI 04: PENGALAMAN KERJA & PREFERENSI KARIR --}}
                        <section id="cand-{{ $modalCandidate->id }}-sec-pengalaman" class="space-y-4 pt-2">
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">04</span>
                                    <span>Pengalaman Kerja & Preferensi Karir</span>
                                </h2>
                                <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">
                                    {{ $works->count() }} Pengalaman
                                </span>
                            </div>

                            {{-- List Pengalaman Kerja --}}
                            <div class="space-y-3">
                                @forelse ($works->sortByDesc('start_date') as $work)
                                    <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 border-b border-gray-200/60 dark:border-slate-700/60 pb-2">
                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm">
                                                        {{ $work->position ?: '-' }}
                                                    </h4>
                                                    @if ($work->initial_position)
                                                        <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">
                                                            (Awal: {{ $work->initial_position }})
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex flex-wrap items-center gap-1.5 mt-0.5">
                                                    <span>{{ $work->company_name ?? ($work->company ?? '-') }}</span>
                                                    @if ($work->business_field)
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-gray-200/60 dark:bg-slate-700/60 text-gray-700 dark:text-slate-300 font-normal">
                                                            {{ $work->business_field }}
                                                        </span>
                                                    @endif
                                                    @if ($work->employment_type)
                                                        <span class="text-gray-400 dark:text-slate-500 font-normal">• {{ $work->employment_type }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <span class="text-[11px] font-medium text-gray-500 dark:text-slate-400 bg-white dark:bg-slate-900 px-2.5 py-1 rounded-xl border border-gray-200 dark:border-slate-800 self-start sm:self-auto">
                                                {{ $work->start_date ? \Carbon\Carbon::parse($work->start_date)->translatedFormat('M Y') : '-' }} s/d 
                                                {{ $work->currently_working ? 'Sekarang' : ($work->end_date ? \Carbon\Carbon::parse($work->end_date)->translatedFormat('M Y') : '-') }}
                                            </span>
                                        </div>

                                        {{-- Extra info chips --}}
                                        @if ($work->supervisor_name || $work->company_address || $work->initial_salary || $work->final_salary || $work->benefits)
                                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-gray-500 dark:text-slate-400 pt-0.5">
                                                @if ($work->supervisor_name)
                                                    <span>Atasan: <strong class="text-gray-700 dark:text-slate-200">{{ $work->supervisor_name }}</strong></span>
                                                @endif
                                                @if ($work->company_address)
                                                    <span>Alamat: <span class="text-gray-600 dark:text-slate-300">{{ $work->company_address }}</span></span>
                                                @endif
                                                @if ($work->final_salary)
                                                    <span>Gaji Akhir: <strong class="text-emerald-600 dark:text-emerald-400">Rp {{ number_format($work->final_salary, 0, ',', '.') }}</strong></span>
                                                @endif
                                                @if ($work->benefits)
                                                    <span>Fasilitas: <span class="text-gray-600 dark:text-slate-300">{{ $work->benefits }}</span></span>
                                                @endif
                                            </div>
                                        @endif

                                        @if ($work->description)
                                            <p class="text-xs text-gray-600 dark:text-slate-300 leading-relaxed whitespace-pre-line pt-1">
                                                {{ $work->description }}
                                            </p>
                                        @endif
                                    </div>
                                @empty
                                    <div class="p-4 bg-gray-50/50 dark:bg-slate-800/20 rounded-2xl text-gray-400 dark:text-slate-500 italic text-[11px] text-center border border-gray-100 dark:border-slate-800">
                                        Belum ada riwayat pengalaman kerja (Fresh Graduate).
                                    </div>
                                @endforelse
                            </div>

                            {{-- Preferensi Kerja & Gaji --}}
                            <div class="p-4 rounded-2xl bg-gray-50/80 dark:bg-slate-800/40 border border-gray-100 dark:border-slate-800 space-y-2">
                                <h4 class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Preferensi & Ekspektasi Kerja</span>
                                </h4>
                                @if ($pref)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs pt-1">
                                        <div>
                                            <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Ekspektasi Gaji:</span>
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                                {{ $pref->expected_salary ? 'Rp ' . number_format($pref->expected_salary, 0, ',', '.') : '-' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Status Pencarian Kerja:</span>
                                            @if (($pref->job_search_status ?? 'Aktif') === 'Aktif')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 mt-0.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Aktif
                                                </span>
                                            @elseif (($pref->job_search_status ?? '') === 'Pasif')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50 mt-0.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Pasif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 border border-gray-200 dark:border-slate-700 mt-0.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Ketersediaan Kerja:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $pref->notice_period ?: 'Dapat Segera Bekerja' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Kesediaan Relokasi:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200">{{ $pref->is_willing_to_relocate ? 'Ya, Bersedia' : 'Tidak Bersedia' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Bidang Diminati:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200">
                                                {{ collect([$pref->interested_field_1, $pref->interested_field_2, $pref->interested_field_3])->filter()->join(', ') ?: '-' }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-[11px] text-gray-400 italic">Data preferensi karir belum diisi.</p>
                                @endif
                            </div>
                        </section>

                        {{-- SEKSI 05: ORGANISASI & PRESTASI --}}
                        <section id="cand-{{ $modalCandidate->id }}-sec-prestasi" class="space-y-4 pt-2">
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">05</span>
                                    <span>Pengalaman Organisasi & Prestasi</span>
                                </h2>
                                <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">Aktivitas & Pencapaian</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                {{-- Organisasi --}}
                                <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                    <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span>Organisasi ({{ $orgs->count() }})</span>
                                    </h4>
                                    <div class="space-y-2 pt-1">
                                        @forelse ($orgs->sortByDesc('start_year') as $org)
                                            <div class="p-2.5 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 space-y-1">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-bold text-gray-900 dark:text-white">{{ $org->name }}</span>
                                                    <span class="text-[10px] text-gray-500 dark:text-slate-400 bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded">
                                                        {{ $org->start_year ?: '-' }} - {{ $org->is_active ? 'Aktif' : ($org->end_year ?: '-') }}
                                                    </span>
                                                </div>
                                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold block text-[11px]">Jabatan: {{ $org->position }}</span>
                                                @if ($org->description)
                                                    <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-normal">{{ $org->description }}</p>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="text-[11px] text-gray-400 italic text-center py-2">Belum ada data organisasi.</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Prestasi --}}
                                <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                    <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                        <span>Prestasi & Penghargaan ({{ $achievements->count() }})</span>
                                    </h4>
                                    <div class="space-y-2 pt-1">
                                        @forelse ($achievements->sortByDesc('year') as $ach)
                                            <div class="p-2.5 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 space-y-1">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-bold text-gray-900 dark:text-white">{{ $ach->name }}</span>
                                                    <span class="text-[10px] text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded font-semibold border border-amber-200/60 dark:border-amber-900/40">
                                                        {{ $ach->scale ?: 'Kompetisi' }}
                                                    </span>
                                                </div>
                                                <span class="text-gray-500 dark:text-slate-400 block text-[10.5px]">
                                                    {{ $ach->month ? $ach->month . ' ' : '' }}{{ $ach->year ?: '' }}
                                                </span>
                                                @if ($ach->description)
                                                    <p class="text-[11px] text-gray-500 dark:text-slate-400 leading-normal">{{ $ach->description }}</p>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="text-[11px] text-gray-400 italic text-center py-2">Belum ada data prestasi.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- SEKSI 06: KEAHLIAN, BAHASA, SERTIFIKASI, PELATIHAN & MEDSOS --}}
                        <section id="cand-{{ $modalCandidate->id }}-sec-keahlian" class="space-y-4 pt-2">
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">06</span>
                                    <span>Keahlian, Bahasa, Sertifikasi & Medsos</span>
                                </h2>
                                <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">Kompilasi Kompetensi</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                {{-- Keahlian / Kemampuan Teknis & Aplikasi Komputer --}}
                                <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                    <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span>Kemampuan Teknis & Aplikasi Komputer</span>
                                    </h4>
                                    <div class="space-y-2 pt-1">
                                        @forelse ($skills as $sk)
                                            @php
                                                $skillCert = $sk->certificate_path ?? ($sk->certificate_file_path ?? null);
                                                $pColor = match($sk->proficiency) {
                                                    'sangat_baik' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                                                    'baik' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
                                                    'cukup' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
                                                    'kurang' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
                                                    default => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800',
                                                };
                                                $pLabel = match($sk->proficiency) {
                                                    'sangat_baik' => 'Sangat Baik',
                                                    'baik' => 'Baik',
                                                    'cukup' => 'Cukup',
                                                    'kurang' => 'Kurang',
                                                    default => ($sk->proficiency ? ucfirst(str_replace('_', ' ', $sk->proficiency)) : ($sk->proficiency_level ?? null)),
                                                };
                                            @endphp
                                            <div class="p-2.5 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 text-[11px] flex flex-col gap-1.5">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-1.5 flex-wrap min-w-0">
                                                        <span class="font-bold text-gray-800 dark:text-slate-200">{{ $sk->skill_name ?? ($sk->name ?? '-') }}</span>
                                                        @if ($pLabel)
                                                            <span class="text-[9.5px] font-semibold px-2 py-0.5 rounded-md border {{ $pColor }}">
                                                                {{ $pLabel }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if ($skillCert)
                                                        <a href="{{ asset('storage/' . ltrim($skillCert, '/')) }}" target="_blank"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200/80 dark:border-emerald-800 transition shrink-0 shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                            <span>Lihat Sertifikat</span>
                                                        </a>
                                                    @else
                                                        <span class="text-[9.5px] text-gray-400 dark:text-slate-500 italic bg-gray-100 dark:bg-slate-800/80 px-2 py-0.5 rounded-md border border-gray-200/60 dark:border-slate-700/60 shrink-0">Tanpa Berkas</span>
                                                    @endif
                                                </div>
                                                @if ($sk->description)
                                                    <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-relaxed">{{ $sk->description }}</p>
                                                @endif
                                            </div>
                                        @empty
                                            <span class="text-gray-400 italic text-[11px] block py-1">Belum ada keahlian ditambahkan.</span>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Bahasa --}}
                                <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                    <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                                        <span>Kemampuan Bahasa</span>
                                    </h4>
                                    <div class="space-y-2 pt-1">
                                        @forelse ($languages as $lang)
                                            @php
                                                $langCert = $lang->certificate_path ?? ($lang->certificate_file_path ?? null);
                                            @endphp
                                            <div class="p-2.5 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 text-[11px] space-y-2">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-bold text-gray-800 dark:text-slate-200 text-xs">{{ $lang->language ?? ($lang->name ?? '-') }}</span>
                                                    @if ($langCert)
                                                        <a href="{{ asset('storage/' . ltrim($langCert, '/')) }}" target="_blank"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200/80 dark:border-emerald-800 transition shrink-0 shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                            <span>Lihat Sertifikat</span>
                                                        </a>
                                                    @else
                                                        <span class="text-[9.5px] text-gray-400 dark:text-slate-500 italic bg-gray-100 dark:bg-slate-800/80 px-2 py-0.5 rounded-md border border-gray-200/60 dark:border-slate-700/60 shrink-0">Tanpa Berkas</span>
                                                    @endif
                                                </div>

                                                @if ($lang->oral || $lang->written || $lang->conversation)
                                                    <div class="grid grid-cols-3 gap-1.5 text-center text-[10.5px]">
                                                        <div class="px-2 py-1 rounded-lg bg-gray-50/90 dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700/60">
                                                            <span class="block text-[9px] uppercase tracking-wider text-gray-400 dark:text-slate-400 font-bold">Lisan</span>
                                                            <span class="font-bold text-gray-800 dark:text-slate-200">{{ ucfirst($lang->oral ?? '-') }}</span>
                                                        </div>
                                                        <div class="px-2 py-1 rounded-lg bg-gray-50/90 dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700/60">
                                                            <span class="block text-[9px] uppercase tracking-wider text-gray-400 dark:text-slate-400 font-bold">Tulisan</span>
                                                            <span class="font-bold text-gray-800 dark:text-slate-200">{{ ucfirst($lang->written ?? '-') }}</span>
                                                        </div>
                                                        <div class="px-2 py-1 rounded-lg bg-gray-50/90 dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700/60">
                                                            <span class="block text-[9px] uppercase tracking-wider text-gray-400 dark:text-slate-400 font-bold">Percakapan</span>
                                                            <span class="font-bold text-gray-800 dark:text-slate-200">{{ ucfirst($lang->conversation ?? '-') }}</span>
                                                        </div>
                                                    </div>
                                                @elseif ($lang->proficiency)
                                                    <div class="text-[10.5px] text-gray-500">
                                                        <span>Kemahiran: <strong class="text-gray-700 dark:text-slate-300">{{ $lang->proficiency }}</strong></span>
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <span class="text-gray-400 italic text-[11px] block py-1">Belum ada data bahasa.</span>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Sertifikasi --}}
                                <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                    <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                        <span>Sertifikasi Profesional ({{ $certifications->count() }})</span>
                                    </h4>
                                    <div class="space-y-1.5 pt-1">
                                        @forelse ($certifications as $cert)
                                            @php
                                                $certPath = $cert->certificate_path ?? ($cert->certificate_file_path ?? null);
                                            @endphp
                                            <div class="p-2.5 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 text-[11px] flex justify-between items-center gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <span class="font-bold text-gray-800 dark:text-slate-200 block truncate">{{ $cert->certificate_name ?? ($cert->name ?? '-') }}</span>
                                                    @if (!empty($cert->issuing_organization))
                                                        <span class="text-gray-400 dark:text-slate-500 text-[10.5px] block truncate">{{ $cert->issuing_organization }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 shrink-0">
                                                    @if ($certPath)
                                                        <a href="{{ asset('storage/' . ltrim($certPath, '/')) }}" target="_blank"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200/80 dark:border-emerald-800 transition shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                            <span>Lihat Sertifikat</span>
                                                        </a>
                                                    @else
                                                        <span class="text-[9.5px] text-gray-400 dark:text-slate-500 italic bg-gray-100 dark:bg-slate-800/80 px-2 py-0.5 rounded-md border border-gray-200/60 dark:border-slate-700/60">Tanpa Berkas</span>
                                                    @endif
                                                    @if (!empty($cert->issue_year) || !empty($cert->year))
                                                        <span class="text-[10px] text-gray-400">{{ $cert->issue_year ?? $cert->year }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-[11px] text-gray-400 italic text-center py-1">Belum ada data sertifikasi.</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Pelatihan / Training --}}
                                <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                    <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        <span>Pelatihan & Kursus ({{ $trainings->count() }})</span>
                                    </h4>
                                    <div class="space-y-1.5 pt-1">
                                        @forelse ($trainings as $trn)
                                            @php
                                                $trnPath = $trn->certificate_path ?? ($trn->certificate_file_path ?? null);
                                            @endphp
                                            <div class="p-2.5 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 text-[11px] flex justify-between items-center gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <span class="font-bold text-gray-800 dark:text-slate-200 block truncate">{{ $trn->training_name ?? ($trn->title ?? ($trn->name ?? '-')) }}</span>
                                                    @if (!empty($trn->organizer))
                                                        <span class="text-gray-400 dark:text-slate-500 text-[10.5px] block truncate">{{ $trn->organizer }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 shrink-0">
                                                    @if ($trnPath)
                                                        <a href="{{ asset('storage/' . ltrim($trnPath, '/')) }}" target="_blank"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200/80 dark:border-emerald-800 transition shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                            <span>Lihat Sertifikat</span>
                                                        </a>
                                                    @else
                                                        <span class="text-[9.5px] text-gray-400 dark:text-slate-500 italic bg-gray-100 dark:bg-slate-800/80 px-2 py-0.5 rounded-md border border-gray-200/60 dark:border-slate-700/60">Tanpa Berkas</span>
                                                    @endif
                                                    @if (!empty($trn->year))
                                                        <span class="text-[10px] text-gray-400">{{ $trn->year }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-[11px] text-gray-400 italic text-center py-1">Belum ada data pelatihan.</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Media Sosial & Portofolio --}}
                                <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2 md:col-span-2">
                                    <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        <span>Media Sosial & Tautan Portofolio</span>
                                    </h4>
                                    <div class="flex flex-wrap gap-2 pt-1">
                                        @forelse ($socials as $soc)
                                            <a href="{{ $soc->url ?? ($soc->profile_url ?? '#') }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 hover:bg-indigo-50 dark:hover:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-semibold text-[11px] border border-gray-200 dark:border-slate-700 transition shadow-2xs">
                                                <span class="w-4 h-4 rounded-full bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center text-[9px] font-bold">
                                                    {{ strtoupper(substr($soc->platform_name ?? ($soc->platform ?? 'S'), 0, 1)) }}
                                                </span>
                                                <span>{{ $soc->platform_name ?? ($soc->platform ?? 'Link') }}</span>
                                                <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @empty
                                            <span class="text-gray-400 italic text-[11px]">Belum ada media sosial dicantumkan.</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- SEKSI 07: KUESIONER KESEHATAN (G) & INFORMASI LAIN (H) --}}
                        <section id="cand-{{ $modalCandidate->id }}-sec-kuesioner" class="space-y-4 pt-2">
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">07</span>
                                    <span>Kuesioner Kesehatan & Informasi Tambahan</span>
                                </h2>
                                <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">Data Tambahan</span>
                            </div>

                            @php
                                $hAnswers = $prof?->health_answers ?? [];
                                $oAnswers = $prof?->other_answers ?? [];
                                $healthKeys = [
                                    'smoking' => 'Merokok',
                                    'alcohol' => 'Minum Alkohol',
                                    'hospitalized' => 'Rawat Inap RS',
                                    'allergy' => 'Riwayat Alergi',
                                    'ongoing_treatment' => 'Pengobatan Berjalan',
                                    'infectious_disease' => 'Penyakit Menular',
                                    'disease_history' => 'Riwayat Penyakit',
                                ];
                            @endphp

                            {{-- Ringkasan Kesehatan --}}
                            <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    <span>G. Kuesioner Riwayat Kesehatan</span>
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-1">
                                    @foreach($healthKeys as $hk => $hl)
                                        @php
                                            $isY = !empty($hAnswers[$hk]['is_yes']);
                                            $det = $hAnswers[$hk]['detail'] ?? '';
                                        @endphp
                                        <div class="p-2.5 rounded-xl bg-white/70 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 flex flex-col gap-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-600 dark:text-slate-300 font-semibold">{{ $hl }}:</span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isY ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' }}">
                                                    {{ $isY ? 'IYA' : 'TIDAK' }}
                                                </span>
                                            </div>
                                            @if($isY && $det)
                                                <span class="text-[10.5px] text-gray-500 dark:text-slate-400 italic">{{ $det }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Ringkasan Informasi Lain --}}
                            <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>H. Kuesioner Informasi Lain & Kesiapan</span>
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-xs pt-1">
                                    <div class="p-2 rounded-xl bg-white/70 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 text-[11px]">
                                        <span class="text-gray-400 block text-[10px]">Perjalanan Dinas</span>
                                        <span class="font-bold text-gray-800 dark:text-slate-200">{{ strtoupper($oAnswers['business_trip_ready'] ?? 'SIAP') }}</span>
                                    </div>
                                    <div class="p-2 rounded-xl bg-white/70 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 text-[11px]">
                                        <span class="text-gray-400 block text-[10px]">Mutasi Bagian/Posisi</span>
                                        <span class="font-bold text-gray-800 dark:text-slate-200">{{ strtoupper($oAnswers['mutation_position_ready'] ?? 'SIAP') }}</span>
                                    </div>
                                    <div class="p-2 rounded-xl bg-white/70 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 text-[11px]">
                                        <span class="text-gray-400 block text-[10px]">Mutasi Group</span>
                                        <span class="font-bold text-gray-800 dark:text-slate-200">{{ strtoupper($oAnswers['mutation_group_ready'] ?? 'SIAP') }}</span>
                                    </div>
                                    <div class="p-2 rounded-xl bg-white/70 dark:bg-slate-900/50 border border-gray-100 dark:border-slate-800 text-[11px]">
                                        <span class="text-gray-400 block text-[10px]">Penempatan Kota Lain</span>
                                        <span class="font-bold text-gray-800 dark:text-slate-200">{{ strtoupper($oAnswers['relocation_city_ready'] ?? 'SIAP') }}</span>
                                    </div>
                                </div>
                                <div class="space-y-1.5 text-xs pt-1">
                                    <div class="p-2 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 text-[11px]">
                                        <span class="text-gray-400 block text-[10px]">Kesiapan Mulai Kerja:</span>
                                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $oAnswers['ready_to_work'] ?? 'Dapat Segera Bekerja' }}</span>
                                    </div>
                                    @if(!empty($oAnswers['expected_salary']))
                                        <div class="p-2 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 text-[11px]">
                                            <span class="text-gray-400 block text-[10px]">Ekspektasi Gaji & Alasan:</span>
                                            <span class="text-gray-700 dark:text-slate-200 font-medium">{{ $oAnswers['expected_salary'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($oAnswers['internal_relations']['is_yes']))
                                        <div class="p-2 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-900/50 text-[11px]">
                                            <span class="text-amber-800 dark:text-amber-300 font-bold block text-[10px]">Keluarga/Kerabat di Perusahaan:</span>
                                            <span class="text-amber-900 dark:text-amber-200">{{ $oAnswers['internal_relations']['detail'] ?? 'Ada relasi' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </section>

                        {{-- SEKSI 08: RIWAYAT LAMARAN PEKERJAAN --}}
                        <section id="cand-{{ $modalCandidate->id }}-sec-lamaran" class="space-y-4 pt-2">
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">08</span>
                                    <span>Riwayat Lamaran Pekerjaan</span>
                                </h2>
                                <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">
                                    {{ $apps->count() }} Lamaran Terdaftar
                                </span>
                            </div>

                            <div class="space-y-2.5">
                                @forelse ($apps as $app)
                                    @php
                                        $job = $app->job;
                                        $company = $job?->company;
                                        $status = $app->status ?? 'Submitted';
                                        
                                        $badgeClasses = match($status) {
                                            'Accepted' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                            'Interview' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                                            'Shortlisted' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                            'Reviewed', 'Partial Approved' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                            'Rejected' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'
                                        };
                                    @endphp
                                    <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm">
                                                    {{ $job?->title ?: 'Lowongan Pekerjaan' }}
                                                </h4>
                                                <span class="text-[10px] font-mono text-gray-500 dark:text-slate-400 bg-white dark:bg-slate-900 px-2 py-0.5 rounded border border-gray-200 dark:border-slate-800">
                                                    #APP-{{ $app->id }}
                                                </span>
                                            </div>
                                            <div class="flex items-center flex-wrap gap-2 text-[11px] text-gray-500 dark:text-slate-400 mt-1">
                                                @if ($company?->name)
                                                    <span class="font-semibold text-gray-700 dark:text-slate-300">{{ $company->name }}</span>
                                                @endif
                                                @if ($job?->department?->name)
                                                    <span>• Dept: {{ $job->department->name }}</span>
                                                @endif
                                                <span>• Dilamar: 
                                                    {{ $app->applied_at ? \Carbon\Carbon::parse($app->applied_at)->translatedFormat('d M Y, H:i') . ' WIB' : ($app->created_at ? \Carbon\Carbon::parse($app->created_at)->translatedFormat('d M Y, H:i') . ' WIB' : '-') }}
                                                </span>
                                            </div>
                                            @if ($app->notes)
                                                <p class="text-[11px] text-gray-500 dark:text-slate-400 italic mt-1.5 bg-white/60 dark:bg-slate-900/40 p-2 rounded-xl border border-gray-100 dark:border-slate-800">
                                                    Catatan: “{{ $app->notes }}”
                                                </p>
                                            @endif
                                        </div>
                                        <div class="shrink-0 self-start sm:self-center">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold border inline-flex items-center gap-1.5 {{ $badgeClasses }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                <span>{{ $app->status_label ?? $status }}</span>
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 bg-gray-50/50 dark:bg-slate-800/20 rounded-2xl text-gray-400 dark:text-slate-500 italic text-[11px] text-center border border-gray-100 dark:border-slate-800">
                                        Kandidat ini belum memiliki riwayat lamaran pada posisi manapun.
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    </div>
                </div>

                {{-- 4. Bottom Sticky / Fixed Action Footer Toolbar --}}
                <div class="px-4 sm:px-6 py-3 bg-white/95 dark:bg-[#0d1527]/95 backdrop-blur-md border-t border-gray-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0 z-20">
                    <div class="text-xs text-gray-500 dark:text-slate-400 flex items-center gap-2">
                        {{-- <span class="w-2 h-2 rounded-full bg-emerald-500"></span> --}}
                        <span>Mode Tinjauan Profil Portrait Lengkap (Read-Only)</span>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="activeCandidateModal = null" 
                            class="px-4 py-2 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 text-gray-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Tutup
                        </button>
                        
                        <a href="{{ $pdfUrlPrefix }}/{{ $modalCandidate->id }}/pdf" target="_blank"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Preview & Cetak PDF</span>
                        </a>

                        @if ($cvUrl)
                            <a href="{{ $cvUrl }}" target="_blank"
                                class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer" title="Buka CV Asli">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>Buka CV Asli</span>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endforeach
