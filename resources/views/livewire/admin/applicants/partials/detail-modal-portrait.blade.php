{{-- Detail Applicant Modal (Portrait Document View - Read Only) --}}
<div x-show="showDetailModal" 
     x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title-detail" 
     role="dialog" 
     aria-modal="true"
     @keydown.escape.window="showDetailModal = false">
    {{-- Backdrop --}}
    <div x-show="showDetailModal" 
        x-transition:enter="ease-out duration-300" 
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100" 
        x-transition:leave="ease-in duration-200" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0" 
        @click="showDetailModal = false" 
        class="fixed inset-0 transition-opacity bg-gray-900/60 dark:bg-black/75 backdrop-blur-sm"></div>

    {{-- Centered Modal Wrapper --}}
    <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-2 sm:p-4 md:p-6 pointer-events-none">
        {{-- Modal Dialog Container: Portrait Optimized (max-w-4xl) --}}
        <div x-show="showDetailModal" 
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
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white leading-tight" id="modal-title-detail">Detail Pelamar</h3>
                    </div>
                </div>

                {{-- Header Action Buttons --}}
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    {{-- <template x-if="detailData && detailData.id">
                        <a :href="'{{ $formActionPrefix }}' + detailData.id + '/profile-pdf'" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold shadow-xs transition" title="Unduh Profil Lengkap PDF">
                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Unduh PDF</span>
                        </a>
                    </template> --}}
                    {{-- Close Button --}}
                    <button @click="showDetailModal = false" class="p-1.5 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 transition shrink-0 cursor-pointer" title="Tutup Modal">
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
                <button type="button" @click="document.getElementById('sec-biodata')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                    Biodata & Kontak
                </button>
                <button type="button" @click="document.getElementById('sec-keluarga')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                    Data Keluarga
                </button>
                <button type="button" @click="document.getElementById('sec-pendidikan')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                    Pendidikan
                </button>
                <button type="button" @click="document.getElementById('sec-pengalaman')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                    Pengalaman Kerja
                </button>
                <button type="button" @click="document.getElementById('sec-prestasi')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                    Organisasi & Prestasi
                </button>
                <button type="button" @click="document.getElementById('sec-keahlian')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 transition shrink-0 cursor-pointer">
                    Keahlian & Medsos
                </button>
                <button type="button" @click="document.getElementById('sec-seleksi')?.scrollIntoView({ behavior: 'smooth', block: 'start' })" class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 transition shrink-0 cursor-pointer font-bold">
                    Status & Seleksi
                </button>
            </div>

            {{-- 3. Scrollable Portrait Document Container --}}
            <div id="portrait-scroll-container" class="flex-1 overflow-y-auto p-3 sm:p-6 custom-scrollbar space-y-4">
                
                {{-- THE PORTRAIT DOSSIER SHEET (Lembar Dokumen Portrait) --}}
                <div class="max-w-3xl mx-auto bg-white dark:bg-[#0D1527] rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] p-5 sm:p-10 space-y-8 relative overflow-hidden">
                    
                    {{-- A. Header Profil Pelamar --}}
                    <div class="border-b border-gray-100 dark:border-slate-800 pb-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                            <div class="flex items-start sm:items-center gap-5 sm:gap-6 min-w-0 flex-1">
                                {{-- Foto Pelamar --}}
                                <div class="shrink-0">
                                    <template x-if="getApplicantPhotoUrl(detailData) && !modalImgFailed">
                                        <img :src="getApplicantPhotoUrl(detailData)" 
                                            :alt="detailData.applicant_profile ? detailData.applicant_profile.full_name : 'Pelamar'" 
                                            x-on:error="modalImgFailed = true"
                                            class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-2xl border border-gray-200 dark:border-slate-700 shadow-xs">
                                    </template>
                                    <template x-if="!getApplicantPhotoUrl(detailData) || modalImgFailed">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-500 dark:text-slate-400 font-semibold text-xl flex items-center justify-center shadow-xs">
                                            <span x-text="detailData.applicant_profile ? (detailData.applicant_profile.full_name || 'P').substring(0, 2).toUpperCase() : 'P'"></span>
                                        </div>
                                    </template>
                                </div>

                                {{-- Identitas & Posisi --}}
                                <div class="space-y-1 min-w-0 flex-1">
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white leading-tight" x-text="detailData.applicant_profile ? detailData.applicant_profile.full_name : 'Pelamar'"></h1>
                                        <span class="text-xs font-mono text-gray-500 dark:text-slate-400 bg-gray-100 dark:bg-slate-800 px-2 py-0.5 rounded border border-gray-200 dark:border-slate-700" x-show="detailData.id" x-text="'#APP-' + detailData.id"></span>
                                    </div>
                                    <div class="flex items-center flex-wrap gap-1.5 text-xs sm:text-sm text-gray-600 dark:text-slate-300">
                                        <span class="font-semibold text-indigo-600 dark:text-indigo-400" x-text="detailData.job ? detailData.job.title : 'Lowongan Pekerjaan'"></span>
                                        <template x-if="detailData.job && detailData.job.company">
                                            <span class="text-gray-400 dark:text-slate-600">• <span class="text-gray-700 dark:text-slate-300 font-normal" x-text="detailData.job.company.name"></span></span>
                                        </template>
                                        <template x-if="detailData.job && detailData.job.department">
                                            <span class="text-gray-400 dark:text-slate-600">• <span class="text-gray-500 dark:text-slate-400 font-normal" x-text="detailData.job.department.name"></span></span>
                                        </template>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-slate-500 flex items-center gap-1.5 pt-0.5" x-show="detailData.applied_at">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Dilamar pada: <span class="font-medium text-gray-600 dark:text-slate-300" x-text="formatDateTime(detailData.applied_at)"></span></span>
                                    </p>
                                </div>
                            </div>

                            {{-- Status Badge di Header --}}
                            <div class="shrink-0 self-start sm:self-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider inline-flex items-center gap-1.5"
                                      :class="{
                                          'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800': detailData.status === 'Accepted',
                                          'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800': detailData.status === 'Interview',
                                          'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800': detailData.status === 'Shortlisted',
                                          'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800': detailData.status === 'Reviewed',
                                          'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 border border-sky-200 dark:border-sky-800': detailData.status === 'Partial Approved',
                                          'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800': detailData.status === 'Rejected',
                                          'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800': detailData.status === 'Submitted' || !detailData.status
                                      }">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          :class="{
                                              'bg-emerald-500': detailData.status === 'Accepted',
                                              'bg-indigo-500': detailData.status === 'Interview',
                                              'bg-purple-500': detailData.status === 'Shortlisted',
                                              'bg-amber-500': detailData.status === 'Reviewed',
                                              'bg-sky-500': detailData.status === 'Partial Approved',
                                              'bg-rose-500': detailData.status === 'Rejected',
                                              'bg-blue-500': detailData.status === 'Submitted' || !detailData.status
                                          }"></span>
                                    <span x-text="detailData.status_label || ({'Submitted':'Diajukan','Reviewed':'Lolos Berkas','Shortlisted':'Lolos Ujian','Interview':'Wawancara','Accepted':'Diterima','Rejected':'Ditolak','Partial Approved':'Partial Approved'}[detailData.status] || detailData.status || 'Diajukan')"></span>
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
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 truncate block" x-text="detailData.applicant_profile && detailData.applicant_profile.user ? detailData.applicant_profile.user.email : '-'"></span>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-xl bg-gray-50/70 dark:bg-slate-800/40 border border-gray-100 dark:border-slate-800 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-gray-400 dark:text-slate-500 block">Telepon / WhatsApp</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 truncate block" x-text="detailData.applicant_profile ? (detailData.applicant_profile.phone || '-') : '-'"></span>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-xl bg-gray-50/70 dark:bg-slate-800/40 border border-gray-100 dark:border-slate-800 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-gray-400 dark:text-slate-500 block">NIK</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 truncate block font-mono" x-text="detailData.applicant_profile ? (detailData.applicant_profile.nik || '-') : '-'"></span>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-xl bg-gray-50/70 dark:bg-slate-800/40 border border-gray-100 dark:border-slate-800 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-gray-400 dark:text-slate-500 block">Domisili</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 truncate block" x-text="detailData.applicant_profile ? ((detailData.applicant_profile.city || '') + (detailData.applicant_profile.province ? (', ' + detailData.applicant_profile.province) : '')) || '-' : '-'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- B. Tentang Pelamar (Bio Singkat) --}}
                    <template x-if="detailData.applicant_profile && detailData.applicant_profile.about_me">
                        <div class="p-4 sm:p-5 rounded-2xl bg-indigo-50/40 dark:bg-[#14203A]/50 border border-indigo-100 dark:border-[#1D2E54]">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-indigo-700 dark:text-indigo-400 mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                Ringkasan Diri Pelamar (Bio)
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-700 dark:text-slate-200 leading-relaxed italic" x-text="'“' + detailData.applicant_profile.about_me + '”'"></p>
                        </div>
                    </template>

                    {{-- SEKSI 01: INFORMASI PRIBADI & KONTAK --}}
                    <section id="sec-biodata" class="space-y-3 pt-2">
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
                                <span class="font-semibold text-gray-800 dark:text-slate-200 font-mono" x-text="detailData.applicant_profile ? (detailData.applicant_profile.nik || '-') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">NPWP:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200 font-mono" x-text="detailData.applicant_profile ? (detailData.applicant_profile.npwp || '-') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Email:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200 break-all" x-text="detailData.applicant_profile && detailData.applicant_profile.user ? detailData.applicant_profile.user.email : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Nomor Telepon / WhatsApp:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? (detailData.applicant_profile.phone || '-') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Jenis Kelamin:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200 capitalize" x-text="detailData.applicant_profile ? (detailData.applicant_profile.gender || '-') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Tempat, Tanggal Lahir:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? ((detailData.applicant_profile.birth_place || '') + (detailData.applicant_profile.birth_date ? (', ' + formatDate(detailData.applicant_profile.birth_date)) : '')) : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Agama:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? (detailData.applicant_profile.religion || '-') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Golongan Darah:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? (detailData.applicant_profile.blood_type || '-') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Suku:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? (detailData.applicant_profile.ethnicity || '-') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Kebangsaan:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? (detailData.applicant_profile.nationality || '-') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Status Pernikahan:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200 capitalize" 
                                    x-text="detailData.applicant_profile && detailData.applicant_profile.marital_status 
                                        ? (detailData.applicant_profile.marital_status + (detailData.applicant_profile.marriage_date ? (' (' + formatDate(detailData.applicant_profile.marriage_date) + ')') : '')) 
                                        : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Transportasi Harian:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? (detailData.applicant_profile.daily_transportation || '-') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Jarak ke Tempat Kerja:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile && detailData.applicant_profile.work_distance_km ? (detailData.applicant_profile.work_distance_km + ' km') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Kota Domisili:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile ? ((detailData.applicant_profile.city || '') + (detailData.applicant_profile.province ? (', ' + detailData.applicant_profile.province) : '')) : '-'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Kode Pos:</span>
                                <span class="font-semibold text-gray-800 dark:text-slate-200 font-mono" x-text="detailData.applicant_profile ? (detailData.applicant_profile.postal_code || '-') : '-'"></span>
                            </div>
                            <div class="sm:col-span-2 md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2.5 border-t border-gray-100 dark:border-slate-800">
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Alamat Sesuai KTP:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 leading-relaxed" x-text="detailData.applicant_profile ? ((detailData.applicant_profile.ktp_address || '-') + (detailData.applicant_profile.ktp_city ? (', ' + detailData.applicant_profile.ktp_city) : '') + (detailData.applicant_profile.ktp_province ? (', ' + detailData.applicant_profile.ktp_province) : '')) : '-'"></span>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Alamat Domisili Saat Ini:</span>
                                    <span class="font-semibold text-gray-800 dark:text-slate-200 leading-relaxed" x-text="detailData.applicant_profile ? ((detailData.applicant_profile.address || '-') + (detailData.applicant_profile.city ? (', ' + detailData.applicant_profile.city) : '') + (detailData.applicant_profile.province ? (', ' + detailData.applicant_profile.province) : '')) : '-'"></span>
                                </div>
                            </div>
                            <template x-if="getDrivingLicenses(detailData).length > 0">
                                <div class="sm:col-span-2 md:col-span-3 pt-2.5 border-t border-gray-100 dark:border-slate-800">
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px] mb-1.5">Kepemilikan SIM (Surat Izin Mengemudi):</span>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="(lic, lIdx) in getDrivingLicenses(detailData)" :key="lIdx">
                                            <span class="px-2.5 py-1 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-medium text-gray-800 dark:text-slate-200 shadow-xs">
                                                <strong class="text-emerald-600 dark:text-emerald-400" x-text="(lic.type || 'SIM') + ':'"></strong>
                                                <span x-text="lic.number || '-'"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Dokumen Berkas CV Pelamar Card --}}
                        <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div>
                                <span class="font-bold text-gray-900 dark:text-white block">Dokumen Berkas CV / Resume Pelamar</span>
                                <p class="text-[11px] text-gray-500 dark:text-slate-400">File berkas curriculum vitae resmi yang diunggah saat pendaftaran.</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <template x-if="detailData.applicant_profile && (detailData.applicant_profile.cv_file_url || detailData.applicant_profile.cv_file_path)">
                                    <a :href="detailData.applicant_profile.cv_file_url ? detailData.applicant_profile.cv_file_url : ('/storage/' + detailData.applicant_profile.cv_file_path)" target="_blank"
                                        class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Buka File Asli</span>
                                    </a>
                                </template>
                                <template x-if="!detailData.applicant_profile || (!detailData.applicant_profile.cv_file_url && !detailData.applicant_profile.cv_file_path)">
                                    <span class="text-xs text-gray-400 dark:text-slate-400 italic">Belum mengunggah file CV</span>
                                </template>
                            </div>
                        </div>
                    </section>

                    {{-- SEKSI 02: DATA KELUARGA & KONTAK DARURAT --}}
                    <section id="sec-keluarga" class="space-y-3 pt-2">
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
                                    <span class="font-bold text-gray-900 dark:text-white" x-text="detailData.applicant_profile && detailData.applicant_profile.child_sequence ? ('Anak ke-' + detailData.applicant_profile.child_sequence) : '-'"></span>
                                </div>
                            </div>
                            <div class="p-3 bg-gray-50/80 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div>
                                    <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Jumlah Bersaudara:</span>
                                    <span class="font-bold text-gray-900 dark:text-white" 
                                        x-text="detailData.applicant_profile && detailData.applicant_profile.total_siblings 
                                            ? (detailData.applicant_profile.total_siblings + ' Bersaudara') 
                                            : '-'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Lengkap Anggota Keluarga (Jika Tersedia) --}}
                        <template x-if="detailData.applicant_profile && detailData.applicant_profile.family && detailData.applicant_profile.family.family_members && detailData.applicant_profile.family.family_members.length > 0">
                            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/30">
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
                                        <template x-for="(mem, mIdx) in detailData.applicant_profile.family.family_members" :key="mIdx">
                                            <tr class="hover:bg-white dark:hover:bg-slate-800/60">
                                                <td class="py-1.5 px-2.5 font-bold text-gray-900 dark:text-white" x-text="mem.relation || '-'"></td>
                                                <td class="py-1.5 px-2.5 font-medium" x-text="mem.name || '-'"></td>
                                                <td class="py-1.5 px-2.5 text-gray-500 dark:text-slate-400" x-text="mem.birth_date ? formatDate(mem.birth_date) : '-'"></td>
                                                <td class="py-1.5 px-2.5 text-gray-500 dark:text-slate-400" x-text="mem.birth_place || '-'"></td>
                                                <td class="py-1.5 px-2 text-center" x-text="mem.age ? (mem.age + ' Thn') : '-'"></td>
                                                <td class="py-1.5 px-2.5" x-text="mem.education || '-'"></td>
                                                <td class="py-1.5 px-2.5" x-text="mem.occupation || '-'"></td>
                                                <td class="py-1.5 px-2.5 text-gray-500 dark:text-slate-400" x-text="mem.company || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>

                        {{-- Ayah & Ibu Grid (Fallback jika belum format tabel) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs" x-show="!detailData.applicant_profile || !detailData.applicant_profile.family || !detailData.applicant_profile.family.family_members || detailData.applicant_profile.family.family_members.length === 0">
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
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.family && detailData.applicant_profile.family.father_name">
                                    <div class="space-y-1.5 text-[11px]">
                                        <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                            <span class="text-gray-400 dark:text-slate-500">Nama:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.father_name"></span>
                                        </div>
                                        <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                            <span class="text-gray-400 dark:text-slate-500">Tahun Lahir:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.father_birth_year || '-'"></span>
                                        </div>
                                        <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                            <span class="text-gray-400 dark:text-slate-500">Pendidikan:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.father_last_education || '-'"></span>
                                        </div>
                                        <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                            <span class="text-gray-400 dark:text-slate-500">Pekerjaan:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.father_occupation || '-'"></span>
                                        </div>
                                        <div class="flex justify-between py-0.5">
                                            <span class="text-gray-400 dark:text-slate-500">Perusahaan / Instansi:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.father_company || '-'"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.family || !detailData.applicant_profile.family.father_name">
                                    <div class="p-3 bg-white/50 dark:bg-slate-900/50 rounded-xl text-gray-400 italic text-center text-[11px]">
                                        Data ayah belum dilengkapi.
                                    </div>
                                </template>
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
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.family && detailData.applicant_profile.family.mother_name">
                                    <div class="space-y-1.5 text-[11px]">
                                        <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                            <span class="text-gray-400 dark:text-slate-500">Nama:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.mother_name"></span>
                                        </div>
                                        <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                            <span class="text-gray-400 dark:text-slate-500">Tahun Lahir:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.mother_birth_year || '-'"></span>
                                        </div>
                                        <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                            <span class="text-gray-400 dark:text-slate-500">Pendidikan:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.mother_last_education || '-'"></span>
                                        </div>
                                        <div class="flex justify-between py-0.5 border-b border-gray-100 dark:border-slate-800">
                                            <span class="text-gray-400 dark:text-slate-500">Pekerjaan:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.mother_occupation || '-'"></span>
                                        </div>
                                        <div class="flex justify-between py-0.5">
                                            <span class="text-gray-400 dark:text-slate-500">Perusahaan / Instansi:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.mother_company || '-'"></span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.family || !detailData.applicant_profile.family.mother_name">
                                    <div class="p-3 bg-white/50 dark:bg-slate-900/50 rounded-xl text-gray-400 italic text-center text-[11px]">
                                        Data ibu belum dilengkapi.
                                    </div>
                                </template>
                            </div>

                            {{-- Pasangan (Suami / Istri - jika ada) --}}
                            <template x-if="detailData.applicant_profile && detailData.applicant_profile.family && detailData.applicant_profile.family.spouse_name">
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
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.spouse_name"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Pekerjaan:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.spouse_occupation || '-'"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Telepon:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.spouse_phone || '-'"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Tgl Lahir / Usia:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.spouse_birth_date ? formatDate(detailData.applicant_profile.family.spouse_birth_date) : '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Kontak Darurat --}}
                            <template x-if="detailData.applicant_profile && detailData.applicant_profile.family && (detailData.applicant_profile.family.emergency_contact_name || detailData.applicant_profile.family.emergency_contact_phone)">
                                <div class="p-3.5 bg-rose-50/50 dark:bg-rose-950/20 rounded-2xl border border-rose-200 dark:border-rose-900/40 space-y-2 md:col-span-2">
                                    <div class="flex items-center gap-2 border-b border-rose-200/60 dark:border-rose-900/40 pb-1.5">
                                        <div class="w-6 h-6 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-xs">!</div>
                                        <span class="font-bold text-rose-700 dark:text-rose-300 text-xs">Kontak Darurat (Emergency Contact)</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px]">
                                        <div>
                                            <span class="text-gray-500 dark:text-rose-400/80 block text-[10.5px]">Nama Kontak:</span>
                                            <span class="font-bold text-gray-900 dark:text-slate-100" x-text="detailData.applicant_profile.family.emergency_contact_name || '-'"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-rose-400/80 block text-[10.5px]">Hubungan / Relasi:</span>
                                            <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.family.emergency_contact_relation || '-'"></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-rose-400/80 block text-[10.5px]">Nomor Telepon:</span>
                                            <span class="font-bold text-gray-900 dark:text-slate-100" x-text="detailData.applicant_profile.family.emergency_contact_phone || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </section>

                    {{-- SEKSI 03: RIWAYAT PENDIDIKAN --}}
                    <section id="sec-pendidikan" class="space-y-3 pt-2">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">03</span>
                                <span>Riwayat Pendidikan</span>
                            </h2>
                            <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium" x-text="(detailData.applicant_profile && detailData.applicant_profile.educations) ? (detailData.applicant_profile.educations.length + ' Riwayat') : '0 Riwayat'"></span>
                        </div>

                        <template x-if="detailData.applicant_profile && detailData.applicant_profile.educations && detailData.applicant_profile.educations.length > 0">
                            <div class="space-y-2.5">
                                <template x-for="edu in detailData.applicant_profile.educations" :key="edu.id">
                                    <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 text-xs">
                                        <div class="space-y-1">
                                            <span class="font-bold text-gray-900 dark:text-white block" x-text="edu.school_name || edu.institution_name"></span>
                                            <div class="flex items-center flex-wrap gap-1.5 text-[11px]">
                                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold" x-text="edu.degree || 'Pendidikan'"></span>
                                                <span class="text-gray-400" x-show="edu.major && edu.major !== '-'">•</span>
                                                <span class="text-gray-700 dark:text-slate-300 font-medium" x-show="edu.major && edu.major !== '-'">
                                                    Jurusan: <strong class="font-semibold" x-text="edu.major"></strong>
                                                </span>
                                                <span class="text-gray-400" x-show="edu.study_program && edu.study_program !== edu.major">•</span>
                                                <span class="text-gray-600 dark:text-slate-400" x-show="edu.study_program && edu.study_program !== edu.major">
                                                    Prodi: <span class="font-medium" x-text="edu.study_program"></span>
                                                </span>
                                                <span class="text-gray-400" x-show="edu.gpa">•</span>
                                                <span class="text-emerald-600 dark:text-emerald-400 font-bold" x-show="edu.gpa" x-text="'IPK: ' + edu.gpa"></span>
                                            </div>
                                            <p x-show="edu.description" class="text-gray-500 dark:text-slate-400 text-[11px] pt-0.5" x-text="edu.description"></p>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 self-start">
                                            <template x-if="edu.certificate_path">
                                                <a :href="edu.certificate_path.startsWith('http') ? edu.certificate_path : ('{{ asset('storage') }}/' + edu.certificate_path.replace(/^\/+/, ''))"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[10.5px] font-semibold text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900 border border-indigo-200 dark:border-indigo-800 rounded-lg transition">
                                                    <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                    <span>Lihat Ijazah</span>
                                                </a>
                                            </template>
                                            <span class="text-gray-500 dark:text-slate-400 text-[10.5px] bg-white dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-gray-200/60 dark:border-slate-700 font-mono" x-text="edu.start_year + ' - ' + (edu.end_year || 'Sekarang')"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.educations || detailData.applicant_profile.educations.length === 0">
                            <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl text-gray-400 italic text-center text-xs border border-gray-100 dark:border-slate-800">
                                Belum ada data riwayat pendidikan dicantumkan.
                            </div>
                        </template>
                    </section>

                    {{-- SEKSI 04: RIWAYAT PENGALAMAN KERJA & PREFERENSI KARIR --}}
                    <section id="sec-pengalaman" class="space-y-4 pt-2">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">04</span>
                                <span>Pengalaman Kerja & Preferensi Karir</span>
                            </h2>
                            <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">Rekam Jejak Profesional</span>
                        </div>

                        {{-- List Pengalaman Kerja --}}
                        <template x-if="detailData.applicant_profile && detailData.applicant_profile.work_experiences && detailData.applicant_profile.work_experiences.length > 0">
                            <div class="space-y-2.5">
                                <template x-for="work in detailData.applicant_profile.work_experiences" :key="work.id">
                                    <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-1.5 text-xs">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 sm:gap-2">
                                            <div>
                                                <span class="font-bold text-gray-900 dark:text-white block" x-text="work.position"></span>
                                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold text-[11px]" x-text="work.company_name + (work.employment_type ? (' • ' + work.employment_type) : '')"></span>
                                            </div>
                                            <span class="text-gray-500 dark:text-slate-400 text-[10.5px] bg-white dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-gray-200/60 dark:border-slate-700 shrink-0 self-start font-mono" x-text="formatDate(work.start_date) + ' s/d ' + (work.currently_working ? 'Sekarang' : formatDate(work.end_date))"></span>
                                        </div>
                                        <p x-show="work.description" class="text-gray-600 dark:text-slate-300 text-[11px] pt-1 leading-relaxed" x-text="work.description"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.work_experiences || detailData.applicant_profile.work_experiences.length === 0">
                            <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl text-gray-400 italic text-center text-xs border border-gray-100 dark:border-slate-800">
                                Belum ada riwayat pengalaman kerja (Fresh Graduate).
                            </div>
                        </template>

                        {{-- Sub-card Preferensi Karir --}}
                        <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2.5 text-xs">
                            <h3 class="font-bold text-gray-900 dark:text-white text-xs flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Preferensi & Ekspektasi Kerja
                            </h3>
                            <template x-if="detailData.applicant_profile && detailData.applicant_profile.job_preference">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    <div>
                                        <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Gaji yang Diharapkan:</span>
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400 text-xs" x-text="detailData.applicant_profile.job_preference.expected_salary ? formatCurrency(detailData.applicant_profile.job_preference.expected_salary) : '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Notice Period (Kesiapan):</span>
                                        <span class="font-semibold text-gray-800 dark:text-slate-200" x-text="detailData.applicant_profile.job_preference.notice_period || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Kesediaan Relokasi:</span>
                                        <span class="font-semibold" :class="detailData.applicant_profile.job_preference.is_willing_to_relocate ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-700 dark:text-slate-300'" x-text="detailData.applicant_profile.job_preference.is_willing_to_relocate ? 'Ya, Bersedia' : 'Tidak'"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Status Pencarian Kerja:</span>
                                        <template x-if="detailData.applicant_profile.job_preference.job_search_status === 'Aktif' || (!detailData.applicant_profile.job_preference.job_search_status && detailData.applicant_profile.job_preference.id)">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 mt-0.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Aktif
                                            </span>
                                        </template>
                                        <template x-if="detailData.applicant_profile.job_preference.job_search_status === 'Pasif'">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50 mt-0.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Pasif
                                            </span>
                                        </template>
                                        <template x-if="detailData.applicant_profile.job_preference.job_search_status === 'Tidak Aktif'">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-300 border border-gray-200 dark:border-slate-700 mt-0.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                Tidak Aktif
                                            </span>
                                        </template>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <span class="text-gray-400 dark:text-slate-500 block text-[10.5px]">Minat Industri / Bidang:</span>
                                        <div class="flex items-center flex-wrap gap-1.5 mt-1">
                                            <template x-if="detailData.applicant_profile.job_preference.interested_field_1">
                                                <span class="px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10.5px] font-medium border border-indigo-100 dark:border-indigo-900/40" x-text="detailData.applicant_profile.job_preference.interested_field_1"></span>
                                            </template>
                                            <template x-if="detailData.applicant_profile.job_preference.interested_field_2">
                                                <span class="px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10.5px] font-medium border border-indigo-100 dark:border-indigo-900/40" x-text="detailData.applicant_profile.job_preference.interested_field_2"></span>
                                            </template>
                                            <template x-if="detailData.applicant_profile.job_preference.interested_field_3">
                                                <span class="px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10.5px] font-medium border border-indigo-100 dark:border-indigo-900/40" x-text="detailData.applicant_profile.job_preference.interested_field_3"></span>
                                            </template>
                                            <template x-if="!detailData.applicant_profile.job_preference.interested_field_1 && !detailData.applicant_profile.job_preference.interested_field_2 && !detailData.applicant_profile.job_preference.interested_field_3">
                                                <span class="text-gray-400 italic text-[11px]">-</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.job_preference">
                                <div class="text-gray-400 italic text-[11px]">Data preferensi karir belum diisi.</div>
                            </template>
                        </div>
                    </section>

                    {{-- SEKSI 05: PENGALAMAN ORGANISASI & PRESTASI --}}
                    <section id="sec-prestasi" class="space-y-4 pt-2">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">05</span>
                                <span>Pengalaman Organisasi & Prestasi</span>
                            </h2>
                            <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">Aktivitas & Pencapaian</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            {{-- Organisasi --}}
                            <div class="space-y-2.5">
                                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Pengalaman Organisasi
                                </h3>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.organizations && detailData.applicant_profile.organizations.length > 0">
                                    <div class="space-y-2">
                                        <template x-for="org in detailData.applicant_profile.organizations" :key="org.id">
                                            <div class="p-3 bg-gray-50/80 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 space-y-1">
                                                <div class="flex justify-between items-start gap-1">
                                                    <div>
                                                        <span class="font-semibold text-gray-900 dark:text-white block text-xs" x-text="org.name"></span>
                                                        <span class="text-indigo-600 dark:text-indigo-400 font-medium text-[11px]" x-text="'Jabatan: ' + org.position"></span>
                                                    </div>
                                                    <span class="text-gray-500 dark:text-slate-400 text-[10px] bg-white dark:bg-slate-800 px-2 py-0.5 rounded border border-gray-200/60 dark:border-slate-700 shrink-0 font-mono" x-text="(org.start_month || '') + ' ' + org.start_year + ' - ' + (org.is_active ? 'Aktif' : ((org.end_month || '') + ' ' + (org.end_year || '')))"></span>
                                                </div>
                                                <p x-show="org.description" class="text-gray-500 dark:text-slate-400 text-[11px] pt-0.5" x-text="org.description"></p>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.organizations || detailData.applicant_profile.organizations.length === 0">
                                    <div class="p-3 bg-gray-50/80 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-center text-[11px] border border-gray-100 dark:border-slate-800">
                                        Belum ada pengalaman organisasi.
                                    </div>
                                </template>
                            </div>

                            {{-- Prestasi & Penghargaan --}}
                            <div class="space-y-2.5">
                                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5 text-xs">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    Prestasi & Penghargaan
                                </h3>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.achievements && detailData.applicant_profile.achievements.length > 0">
                                    <div class="space-y-2">
                                        <template x-for="ach in detailData.applicant_profile.achievements" :key="ach.id">
                                            <div class="p-3 bg-gray-50/80 dark:bg-slate-800/40 rounded-xl border border-gray-100 dark:border-slate-800 flex justify-between items-center gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span class="font-semibold text-gray-900 dark:text-white text-xs truncate" x-text="ach.name"></span>
                                                        <span class="px-1.5 py-0.5 text-[9.5px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded border border-amber-200 dark:border-amber-800 shrink-0" x-text="ach.scale"></span>
                                                    </div>
                                                    <span class="text-gray-500 dark:text-slate-400 text-[10.5px]" x-text="(ach.month || '') + ' ' + (ach.year || '')"></span>
                                                </div>
                                                <template x-if="ach.certificate_path">
                                                    <a :href="ach.certificate_path.startsWith('http') ? ach.certificate_path : ('{{ asset('storage') }}/' + ach.certificate_path.replace(/^\/+/, ''))" 
                                                       target="_blank" 
                                                       class="px-2 py-1 text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 rounded-lg border border-indigo-200 dark:border-indigo-800 transition shrink-0 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Berkas</span>
                                                    </a>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.achievements || detailData.applicant_profile.achievements.length === 0">
                                    <div class="p-3 bg-gray-50/80 dark:bg-slate-800/40 rounded-xl text-gray-400 italic text-center text-[11px] border border-gray-100 dark:border-slate-800">
                                        Belum ada prestasi dicantumkan.
                                    </div>
                                </template>
                            </div>
                        </div>
                    </section>

                    {{-- SEKSI 06: KEAHLIAN, BAHASA, SERTIFIKASI & MEDIA SOSIAL --}}
                    <section id="sec-keahlian" class="space-y-4 pt-2">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">06</span>
                                <span>Keahlian, Bahasa, Sertifikasi & Medsos</span>
                            </h2>
                            <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">Kompetensi & Portofolio</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                            {{-- Keahlian / Kemampuan Teknis & Aplikasi Komputer --}}
                            <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                <h3 class="font-bold text-gray-900 dark:text-white text-xs flex items-center gap-1.5 border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Kemampuan Teknis & Aplikasi Komputer
                                </h3>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.skills && detailData.applicant_profile.skills.length > 0">
                                    <div class="space-y-2 pt-1">
                                        <template x-for="sk in detailData.applicant_profile.skills" :key="sk.id">
                                            <div class="p-2.5 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 text-[11px] flex flex-col gap-1">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-1.5 flex-wrap min-w-0">
                                                        <span class="font-bold text-gray-800 dark:text-slate-200" x-text="sk.name || sk.skill_name"></span>
                                                        <template x-if="sk.proficiency">
                                                            <span class="text-[9.5px] font-semibold px-2 py-0.5 rounded-md border"
                                                                :class="{
                                                                    'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800': sk.proficiency === 'sangat_baik',
                                                                    'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800': sk.proficiency === 'baik',
                                                                    'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800': sk.proficiency === 'cukup',
                                                                    'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800': sk.proficiency === 'kurang'
                                                                }"
                                                                x-text="sk.proficiency === 'sangat_baik' ? 'Sangat Baik' : (sk.proficiency === 'baik' ? 'Baik' : (sk.proficiency === 'cukup' ? 'Cukup' : (sk.proficiency === 'kurang' ? 'Kurang' : sk.proficiency)))">
                                                            </span>
                                                        </template>
                                                    </div>
                                                    <template x-if="sk.certificate_path">
                                                        <a :href="sk.certificate_path.startsWith('http') ? sk.certificate_path : ('{{ asset('storage') }}/' + sk.certificate_path.replace(/^\/+/, ''))" 
                                                           target="_blank" 
                                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200/80 dark:border-emerald-800 transition shrink-0 shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                            <span>Lihat Sertifikat</span>
                                                        </a>
                                                    </template>
                                                    <template x-if="!sk.certificate_path">
                                                        <span class="text-[9.5px] text-gray-400 dark:text-slate-500 italic bg-gray-100 dark:bg-slate-800/80 px-2 py-0.5 rounded-md border border-gray-200/60 dark:border-slate-700/60 shrink-0">Tanpa Berkas</span>
                                                    </template>
                                                </div>
                                                <template x-if="sk.description">
                                                    <p class="text-[10.5px] text-gray-500 dark:text-slate-400 leading-relaxed" x-text="sk.description"></p>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.skills || detailData.applicant_profile.skills.length === 0">
                                    <span class="text-gray-400 italic text-[11px] block pt-1">Belum ada daftar keahlian dicantumkan.</span>
                                </template>
                            </div>

                            {{-- Kemampuan Bahasa --}}
                            <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                <h3 class="font-bold text-gray-900 dark:text-white text-xs flex items-center gap-1.5 border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                                    Kemampuan Bahasa
                                </h3>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.languages && detailData.applicant_profile.languages.length > 0">
                                    <div class="space-y-2 pt-1">
                                        <template x-for="lang in detailData.applicant_profile.languages" :key="lang.id">
                                            <div class="p-2.5 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 text-[11px] space-y-2">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-bold text-gray-800 dark:text-slate-200 text-xs" x-text="lang.name"></span>
                                                    <template x-if="lang.certificate_path">
                                                        <a :href="lang.certificate_path.startsWith('http') ? lang.certificate_path : ('{{ asset('storage') }}/' + lang.certificate_path.replace(/^\/+/, ''))" 
                                                           target="_blank" 
                                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200/80 dark:border-emerald-800 transition shrink-0 shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                            <span>Lihat Sertifikat</span>
                                                        </a>
                                                    </template>
                                                    <template x-if="!lang.certificate_path">
                                                        <span class="text-[9.5px] text-gray-400 dark:text-slate-500 italic bg-gray-100 dark:bg-slate-800/80 px-2 py-0.5 rounded-md border border-gray-200/60 dark:border-slate-700/60 shrink-0">Tanpa Berkas</span>
                                                    </template>
                                                </div>

                                                <template x-if="lang.oral || lang.written || lang.conversation">
                                                    <div class="grid grid-cols-3 gap-1.5 text-center text-[10.5px]">
                                                        <div class="px-2 py-1 rounded-lg bg-gray-50/90 dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700/60">
                                                            <span class="block text-[9px] uppercase tracking-wider text-gray-400 dark:text-slate-400 font-bold">Lisan</span>
                                                            <span class="font-bold text-gray-800 dark:text-slate-200" x-text="lang.oral ? (lang.oral.charAt(0).toUpperCase() + lang.oral.slice(1)) : '-'"></span>
                                                        </div>
                                                        <div class="px-2 py-1 rounded-lg bg-gray-50/90 dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700/60">
                                                            <span class="block text-[9px] uppercase tracking-wider text-gray-400 dark:text-slate-400 font-bold">Tulisan</span>
                                                            <span class="font-bold text-gray-800 dark:text-slate-200" x-text="lang.written ? (lang.written.charAt(0).toUpperCase() + lang.written.slice(1)) : '-'"></span>
                                                        </div>
                                                        <div class="px-2 py-1 rounded-lg bg-gray-50/90 dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700/60">
                                                            <span class="block text-[9px] uppercase tracking-wider text-gray-400 dark:text-slate-400 font-bold">Percakapan</span>
                                                            <span class="font-bold text-gray-800 dark:text-slate-200" x-text="lang.conversation ? (lang.conversation.charAt(0).toUpperCase() + lang.conversation.slice(1)) : '-'"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.languages || detailData.applicant_profile.languages.length === 0">
                                    <span class="text-gray-400 italic text-[11px] block pt-1">Belum ada data bahasa.</span>
                                </template>
                            </div>

                            {{-- Sertifikasi Profesional --}}
                            <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                <h3 class="font-bold text-gray-900 dark:text-white text-xs flex items-center gap-1.5 border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    Sertifikasi Profesional
                                </h3>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.certifications && detailData.applicant_profile.certifications.length > 0">
                                    <div class="space-y-1.5 pt-1">
                                        <template x-for="cert in detailData.applicant_profile.certifications" :key="cert.id">
                                            <div class="p-2.5 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 flex items-center justify-between gap-2">
                                                <span class="font-bold text-gray-800 dark:text-slate-200 truncate text-xs" x-text="cert.name"></span>
                                                <template x-if="cert.certificate_path">
                                                    <a :href="cert.certificate_path.startsWith('http') ? cert.certificate_path : ('{{ asset('storage') }}/' + cert.certificate_path.replace(/^\/+/, ''))" 
                                                       target="_blank" 
                                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200/80 dark:border-emerald-800 transition shrink-0 shadow-2xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                        <span>Lihat Sertifikat</span>
                                                    </a>
                                                </template>
                                                <template x-if="!cert.certificate_path">
                                                    <span class="text-[9.5px] text-gray-400 dark:text-slate-500 italic bg-gray-100 dark:bg-slate-800/80 px-2 py-0.5 rounded-md border border-gray-200/60 dark:border-slate-700/60 shrink-0">Tanpa Berkas</span>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.certifications || detailData.applicant_profile.certifications.length === 0">
                                    <span class="text-gray-400 italic text-[11px] block pt-1">Belum ada sertifikasi.</span>
                                </template>
                            </div>

                            {{-- Pelatihan & Kursus --}}
                            <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2">
                                <h3 class="font-bold text-gray-900 dark:text-white text-xs flex items-center gap-1.5 border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    Pelatihan & Kursus
                                </h3>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.trainings && detailData.applicant_profile.trainings.length > 0">
                                    <div class="space-y-1.5 pt-1">
                                        <template x-for="trn in detailData.applicant_profile.trainings" :key="trn.id">
                                            <div class="p-2.5 bg-white/70 dark:bg-slate-900/50 rounded-xl border border-gray-100 dark:border-slate-800 flex items-center justify-between gap-2">
                                                <span class="font-bold text-gray-800 dark:text-slate-200 truncate text-xs" x-text="trn.name"></span>
                                                <template x-if="trn.certificate_path">
                                                    <a :href="trn.certificate_path.startsWith('http') ? trn.certificate_path : ('{{ asset('storage') }}/' + trn.certificate_path.replace(/^\/+/, ''))" 
                                                       target="_blank" 
                                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-semibold text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200/80 dark:border-emerald-800 transition shrink-0 shadow-2xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                        <span>Lihat Sertifikat</span>
                                                    </a>
                                                </template>
                                                <template x-if="!trn.certificate_path">
                                                    <span class="text-[9.5px] text-gray-400 dark:text-slate-500 italic bg-gray-100 dark:bg-slate-800/80 px-2 py-0.5 rounded-md border border-gray-200/60 dark:border-slate-700/60 shrink-0">Tanpa Berkas</span>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.trainings || detailData.applicant_profile.trainings.length === 0">
                                    <span class="text-gray-400 italic text-[11px] block pt-1">Belum ada pelatihan.</span>
                                </template>
                            </div>

                            {{-- Akun Medsos & Portofolio Online --}}
                            <div class="p-3.5 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2 md:col-span-2">
                                <h3 class="font-bold text-gray-900 dark:text-white text-xs flex items-center gap-1.5 border-b border-gray-200/60 dark:border-slate-700/60 pb-1.5">
                                    <svg class="w-3.5 h-3.5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    Media Sosial & Portofolio
                                </h3>
                                <template x-if="detailData.applicant_profile && detailData.applicant_profile.social_medias && detailData.applicant_profile.social_medias.length > 0">
                                    <div class="space-y-1.5 pt-1">
                                        <template x-for="sm in detailData.applicant_profile.social_medias" :key="sm.id">
                                            <a :href="sm.url" target="_blank" class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-800/80 border border-gray-100 dark:border-slate-700/60 hover:border-indigo-300 transition text-xs group">
                                                <span class="font-semibold text-gray-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400" x-text="sm.platform_name"></span>
                                                <span class="text-indigo-600 dark:text-indigo-400 text-[10.5px] truncate max-w-[140px]" x-text="sm.url"></span>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!detailData.applicant_profile || !detailData.applicant_profile.social_medias || detailData.applicant_profile.social_medias.length === 0">
                                    <span class="text-gray-400 italic text-[11px] block pt-1">Belum ada akun media sosial.</span>
                                </template>
                            </div>
                        </div>
                    </section>

                    {{-- SEKSI 07: STATUS PROSES SELEKSI & TAHAP EVALUASI --}}
                    <section id="sec-seleksi" class="space-y-4 pt-2">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 pb-2">
                            <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white flex items-center justify-center font-extrabold text-[11px]">07</span>
                                <span>Status Proses Seleksi & Tahap Evaluasi</span>
                            </h2>
                            <span class="text-[11px] text-gray-400 dark:text-slate-500 font-medium">Recruitment Pipeline</span>
                        </div>

                        {{-- Banner Status Seleksi Terkini --}}
                        <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div>
                                <span class="text-gray-400 dark:text-slate-500 text-[10.5px] block">Status Lamaran Saat Ini:</span>
                                <span class="text-base font-bold text-gray-900 dark:text-white" x-text="detailData.status_label || ({'Submitted':'Diajukan','Reviewed':'Lolos Berkas','Shortlisted':'Lolos Ujian','Interview':'Wawancara','Accepted':'Diterima','Rejected':'Ditolak','Partial Approved':'Partial Approved'}[detailData.status] || detailData.status || 'Diajukan')"></span>
                                <p x-show="detailData.notes" class="text-gray-600 dark:text-slate-300 text-[11px] mt-0.5" x-text="'Catatan: ' + detailData.notes"></p>
                            </div>
                            <button type="button" @click="showDetailModal = false; openStatusModal({
                                id: detailData.id,
                                applicant_name: detailData.applicant_profile ? detailData.applicant_profile.full_name : 'Pelamar',
                                applicant_email: detailData.applicant_profile && detailData.applicant_profile.user ? detailData.applicant_profile.user.email : '',
                                job_title: detailData.job ? detailData.job.title : 'Lowongan',
                                status: detailData.status || 'Submitted',
                                notes: detailData.notes || '',
                                recruiter_approval: detailData.recruiter_approval || 'pending',
                                recruiter_notes: detailData.recruiter_notes || '',
                                admin_approval: detailData.admin_approval || 'pending',
                                admin_notes: detailData.admin_notes || '',
                                interview_recruiter_approval: detailData.interview_recruiter_approval || 'pending',
                                interview_recruiter_notes: detailData.interview_recruiter_notes || '',
                                interview_recruiter: detailData.interview_recruiter || detailData.interviewRecruiter || null,
                                interview_admin_approval: detailData.interview_admin_approval || 'pending',
                                interview_admin_notes: detailData.interview_admin_notes || '',
                                interview_admin: detailData.interview_admin || detailData.interviewAdmin || null,
                            })" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold text-xs transition flex items-center justify-center gap-1.5 self-start sm:self-auto shadow-2xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                <span>Update Status</span>
                            </button>
                        </div>

                        {{-- Tahap 1: Verifikasi Berkas Administrasi --}}
                        <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-900 dark:text-white text-xs block flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    Tahap 1: Verifikasi Berkas Administrasi
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300': (detailData.status === 'Shortlisted' || detailData.status === 'Accepted' || (detailData.recruiter_approval === 'approved' && detailData.admin_approval === 'approved')),
                                        'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 border border-sky-200 dark:border-sky-800': (detailData.status === 'Partial Approved' || detailData.recruiter_approval === 'approved' || detailData.admin_approval === 'approved'),
                                        'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300': (detailData.status === 'Rejected' || detailData.recruiter_approval === 'rejected' || detailData.admin_approval === 'rejected'),
                                        'bg-gray-100 text-gray-700 dark:bg-slate-800 dark:text-slate-300': (!detailData.recruiter_approval || detailData.recruiter_approval === 'pending') && (!detailData.admin_approval || detailData.admin_approval === 'pending')
                                    }"
                                    x-text="(detailData.recruiter_approval === 'approved' && detailData.admin_approval === 'approved') ? 'Double Approved (Lolos Berkas)' : ((detailData.recruiter_approval === 'approved' || detailData.admin_approval === 'approved') ? 'Partial Approved (1/2 Disetujui)' : 'Menunggu Review')">
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- Tahap 1: Recruiter --}}
                                <div class="p-3 rounded-xl border text-xs"
                                     :class="detailData.recruiter_approval === 'approved' ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/60' : (detailData.recruiter_approval === 'rejected' ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/60' : 'bg-gray-50/60 dark:bg-slate-800/40 border-gray-200/60 dark:border-slate-700')">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-gray-800 dark:text-slate-200">1. Verifikasi Recruiter</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                              :class="detailData.recruiter_approval === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300' : (detailData.recruiter_approval === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300')"
                                              x-text="detailData.recruiter_approval === 'approved' ? 'Disetujui' : (detailData.recruiter_approval === 'rejected' ? 'Ditolak' : 'Pending')">
                                        </span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-gray-500 dark:text-slate-400">
                                        <template x-if="detailData.recruiter">
                                            <p class="font-medium text-gray-700 dark:text-slate-300" x-text="'Penilai: ' + detailData.recruiter.name"></p>
                                        </template>
                                        <template x-if="detailData.recruiter_notes">
                                            <p class="italic text-[10.5px] mt-1 bg-white/70 dark:bg-slate-900/50 p-1.5 rounded-lg border border-gray-100 dark:border-slate-800" x-text="'“' + detailData.recruiter_notes + '”'"></p>
                                        </template>
                                    </div>
                                </div>

                                {{-- Tahap 1: Admin HR --}}
                                <div class="p-3 rounded-xl border text-xs"
                                     :class="detailData.admin_approval === 'approved' ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/60' : (detailData.admin_approval === 'rejected' ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/60' : 'bg-gray-50/60 dark:bg-slate-800/40 border-gray-200/60 dark:border-slate-700')">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-gray-800 dark:text-slate-200">2. Verifikasi HR / Admin</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                              :class="detailData.admin_approval === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300' : (detailData.admin_approval === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300')"
                                              x-text="detailData.admin_approval === 'approved' ? 'Disetujui' : (detailData.admin_approval === 'rejected' ? 'Ditolak' : 'Pending')">
                                        </span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-gray-500 dark:text-slate-400">
                                        <template x-if="detailData.admin">
                                            <p class="font-medium text-gray-700 dark:text-slate-300" x-text="'Admin: ' + detailData.admin.name"></p>
                                        </template>
                                        <template x-if="detailData.admin_notes">
                                            <p class="italic text-[10.5px] mt-1 bg-white/70 dark:bg-slate-900/50 p-1.5 rounded-lg border border-gray-100 dark:border-slate-800" x-text="'“' + detailData.admin_notes + '”'"></p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tahap 2: Evaluasi Ujian Online & Psikotes --}}
                        <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-900 dark:text-white text-xs block flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Tahap 2: Evaluasi Ujian Online & Psikotes
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300': (detailData.test_recruiter_approval === 'approved' && detailData.test_admin_approval === 'approved'),
                                        'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 border border-sky-200 dark:border-sky-800': ((detailData.test_recruiter_approval === 'approved' && detailData.test_admin_approval !== 'approved') || (detailData.test_admin_approval === 'approved' && detailData.test_recruiter_approval !== 'approved')),
                                        'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300': (detailData.test_recruiter_approval === 'rejected' || detailData.test_admin_approval === 'rejected'),
                                        'bg-gray-100 text-gray-700 dark:bg-slate-800 dark:text-slate-300': (!detailData.test_recruiter_approval || detailData.test_recruiter_approval === 'pending') && (!detailData.test_admin_approval || detailData.test_admin_approval === 'pending')
                                    }"
                                    x-text="(detailData.test_recruiter_approval === 'approved' && detailData.test_admin_approval === 'approved') ? 'Double Approved (Lolos Ujian)' : ((detailData.test_recruiter_approval === 'approved') ? 'Menunggu HR (Teknis ✓)' : ((detailData.test_admin_approval === 'approved') ? 'Menunggu Recruiter (HR ✓)' : ((detailData.test_recruiter_approval === 'rejected' || detailData.test_admin_approval === 'rejected') ? 'Ditolak Ujian' : 'Belum Evaluasi')))">
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3 rounded-xl border text-xs"
                                     :class="detailData.test_recruiter_approval === 'approved' ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/60' : (detailData.test_recruiter_approval === 'rejected' ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/60' : 'bg-gray-50/60 dark:bg-slate-800/40 border-gray-200/60 dark:border-slate-700')">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-gray-800 dark:text-slate-200">1. Penilai Teknis (Recruiter)</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                              :class="detailData.test_recruiter_approval === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300' : (detailData.test_recruiter_approval === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300')"
                                              x-text="detailData.test_recruiter_approval === 'approved' ? 'Lolos Teknis' : (detailData.test_recruiter_approval === 'rejected' ? 'Tidak Lolos' : 'Pending')">
                                        </span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-gray-500 dark:text-slate-400">
                                        <template x-if="detailData.test_recruiter || detailData.testRecruiter">
                                            <p class="font-medium text-gray-700 dark:text-slate-300" x-text="'Penilai: ' + (detailData.test_recruiter ? detailData.test_recruiter.name : detailData.testRecruiter.name)"></p>
                                        </template>
                                        <template x-if="detailData.test_recruiter_notes">
                                            <p class="italic text-[10.5px] mt-1 bg-white/70 dark:bg-slate-900/50 p-1.5 rounded-lg border border-gray-100 dark:border-slate-800" x-text="'“' + detailData.test_recruiter_notes + '”'"></p>
                                        </template>
                                    </div>
                                </div>

                                <div class="p-3 rounded-xl border text-xs"
                                     :class="detailData.test_admin_approval === 'approved' ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/60' : (detailData.test_admin_approval === 'rejected' ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/60' : 'bg-gray-50/60 dark:bg-slate-800/40 border-gray-200/60 dark:border-slate-700')">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-gray-800 dark:text-slate-200">2. HR Verifikasi (Psikotes)</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                              :class="detailData.test_admin_approval === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300' : (detailData.test_admin_approval === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300')"
                                              x-text="detailData.test_admin_approval === 'approved' ? 'Disetujui HR' : (detailData.test_admin_approval === 'rejected' ? 'Ditolak' : 'Pending')">
                                        </span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-gray-500 dark:text-slate-400">
                                        <template x-if="detailData.test_admin || detailData.testAdmin">
                                            <p class="font-medium text-gray-700 dark:text-slate-300" x-text="'Admin HR: ' + (detailData.test_admin ? detailData.test_admin.name : detailData.testAdmin.name)"></p>
                                        </template>
                                        <template x-if="detailData.test_admin_notes">
                                            <p class="italic text-[10.5px] mt-1 bg-white/70 dark:bg-slate-900/50 p-1.5 rounded-lg border border-gray-100 dark:border-slate-800" x-text="'“' + detailData.test_admin_notes + '”'"></p>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- List attempt hasil tes --}}
                            <template x-if="detailData.test_attempts && detailData.test_attempts.length > 0">
                                <div class="mt-2 pt-2 border-t border-gray-100 dark:border-slate-800/80">
                                    <span class="text-[10.5px] font-bold text-gray-600 dark:text-slate-400 block mb-1">Riwayat Pengerjaan Tes Online:</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                        <template x-for="attempt in detailData.test_attempts" :key="attempt.id">
                                            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50/80 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-800 text-[11px]"
                                                 x-data="{
                                                     get isPsychometric() {
                                                         const cat = (attempt.test?.category?.name || attempt.test?.category || '').toLowerCase();
                                                         const title = (attempt.test?.title || '').toLowerCase();
                                                         return Boolean(attempt.is_psychometric) || 
                                                             cat.includes('disc') || cat.includes('papi') ||
                                                             title.includes('disc') || title.includes('papi') ||
                                                             Boolean(attempt.disc_test_result) || Boolean(attempt.papi_test_result) ||
                                                             Boolean(attempt.discTestResult) || Boolean(attempt.papiTestResult);
                                                     },
                                                     get scoreVal() {
                                                         return (attempt.total_score !== null && attempt.total_score !== undefined) 
                                                             ? attempt.total_score 
                                                             : ((attempt.score !== null && attempt.score !== undefined) 
                                                                 ? attempt.score 
                                                                 : attempt.objective_score);
                                                     },
                                                     get hasScore() {
                                                         return this.scoreVal !== null && this.scoreVal !== undefined && this.scoreVal !== '';
                                                     },
                                                     get isCompleted() {
                                                         return attempt.status === 'completed' || attempt.status === 'passed' || Boolean(attempt.finished_at) || this.hasScore;
                                                     },
                                                     get formattedScore() {
                                                         if (!this.hasScore) return null;
                                                         const num = parseFloat(this.scoreVal);
                                                         return isNaN(num) ? this.scoreVal : (num % 1 === 0 ? parseInt(num) : num.toFixed(1));
                                                     },
                                                     get isPassed() {
                                                         if (!this.hasScore) return false;
                                                         const passing = parseFloat(attempt.test?.passing_score ?? 70);
                                                         return parseFloat(this.scoreVal) >= passing;
                                                     }
                                                 }">
                                                <div class="truncate mr-2">
                                                    <span class="font-semibold text-gray-800 dark:text-slate-200 block truncate" x-text="attempt.test ? attempt.test.title : 'Tes Online'"></span>
                                                    <span class="text-[9.5px] text-gray-400 uppercase tracking-wider" x-text="attempt.test ? (attempt.test.category?.name || attempt.test.category || 'Tes') : ''"></span>
                                                </div>
                                                <div class="text-right shrink-0">
                                                    <template x-if="isPsychometric">
                                                        <span class="font-bold px-1.5 py-0.5 rounded text-[10px]"
                                                              :class="isCompleted ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : (attempt.status === 'in_progress' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-400')"
                                                              x-text="isCompleted ? 'Selesai' : (attempt.status === 'in_progress' ? 'Sedang Dikerjakan' : 'Belum Selesai')">
                                                        </span>
                                                    </template>
                                                    <template x-if="!isPsychometric">
                                                        <div>
                                                            <template x-if="hasScore">
                                                                <span class="font-bold px-1.5 py-0.5 rounded text-[10px]" 
                                                                      :class="isPassed ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300'" 
                                                                      x-text="'Skor: ' + formattedScore">
                                                                </span>
                                                            </template>
                                                            <template x-if="!hasScore">
                                                                <span class="font-medium px-1.5 py-0.5 rounded text-[10px] bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-slate-400"
                                                                      x-text="attempt.status === 'in_progress' ? 'Sedang Dikerjakan' : (attempt.status === 'completed' ? 'Menunggu Nilai' : 'Belum Ada Skor')">
                                                                </span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Tahap 3: Final Gate Pasca Wawancara --}}
                        <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-2.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-900 dark:text-white text-xs block flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Tahap 3: Final Gate Pasca Wawancara
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                    :class="{
                                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300': (detailData.interview_recruiter_approval === 'approved' && detailData.interview_admin_approval === 'approved'),
                                        'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 border border-sky-200 dark:border-sky-800': ((detailData.interview_recruiter_approval === 'approved' && detailData.interview_admin_approval !== 'approved') || (detailData.interview_admin_approval === 'approved' && detailData.interview_recruiter_approval !== 'approved')),
                                        'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300': (detailData.interview_recruiter_approval === 'rejected' || detailData.interview_admin_approval === 'rejected'),
                                        'bg-gray-100 text-gray-700 dark:bg-slate-800 dark:text-slate-300': (!detailData.interview_recruiter_approval || detailData.interview_recruiter_approval === 'pending') && (!detailData.interview_admin_approval || detailData.interview_admin_approval === 'pending')
                                    }"
                                    x-text="(detailData.interview_recruiter_approval === 'approved' && detailData.interview_admin_approval === 'approved') ? 'Double Approved (Diterima Resmi)' : ((detailData.interview_recruiter_approval === 'approved') ? 'Menunggu HR (Recruiter ✓)' : ((detailData.interview_admin_approval === 'approved') ? 'Menunggu Recruiter (HR ✓)' : ((detailData.interview_recruiter_approval === 'rejected' || detailData.interview_admin_approval === 'rejected') ? 'Ditolak' : 'Menunggu Wawancara')))">
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3 rounded-xl border text-xs"
                                     :class="detailData.interview_recruiter_approval === 'approved' ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/60' : (detailData.interview_recruiter_approval === 'rejected' ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/60' : 'bg-gray-50/60 dark:bg-slate-800/40 border-gray-200/60 dark:border-slate-700')">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-gray-800 dark:text-slate-200">1. Recruiter</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                              :class="detailData.interview_recruiter_approval === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300' : (detailData.interview_recruiter_approval === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300')"
                                              x-text="detailData.interview_recruiter_approval === 'approved' ? 'Kompetensi Layak' : (detailData.interview_recruiter_approval === 'rejected' ? 'Tidak Layak' : 'Pending')">
                                        </span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-gray-500 dark:text-slate-400">
                                        <template x-if="detailData.interview_recruiter || detailData.interviewRecruiter">
                                            <p class="font-medium text-gray-700 dark:text-slate-300" x-text="'Pewawancara: ' + (detailData.interview_recruiter ? detailData.interview_recruiter.name : detailData.interviewRecruiter.name)"></p>
                                        </template>
                                        <template x-if="detailData.interview_recruiter_notes">
                                            <p class="italic text-[10.5px] mt-1 bg-white/70 dark:bg-slate-900/50 p-1.5 rounded-lg border border-gray-100 dark:border-slate-800" x-text="'“' + detailData.interview_recruiter_notes + '”'"></p>
                                        </template>
                                    </div>
                                </div>

                                <div class="p-3 rounded-xl border text-xs"
                                     :class="detailData.interview_admin_approval === 'approved' ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/60' : (detailData.interview_admin_approval === 'rejected' ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/60' : 'bg-gray-50/60 dark:bg-slate-800/40 border-gray-200/60 dark:border-slate-700')">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-gray-800 dark:text-slate-200">2. HR / Admin</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                              :class="detailData.interview_admin_approval === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300' : (detailData.interview_admin_approval === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300')"
                                              x-text="detailData.interview_admin_approval === 'approved' ? 'Disetujui HR' : (detailData.interview_admin_approval === 'rejected' ? 'Ditolak' : 'Pending')">
                                        </span>
                                    </div>
                                    <div class="mt-1 text-[11px] text-gray-500 dark:text-slate-400">
                                        <template x-if="detailData.interview_admin || detailData.interviewAdmin">
                                            <p class="font-medium text-gray-700 dark:text-slate-300" x-text="'Admin HR: ' + (detailData.interview_admin ? detailData.interview_admin.name : detailData.interviewAdmin.name)"></p>
                                        </template>
                                        <template x-if="detailData.interview_admin_notes">
                                            <p class="italic text-[10.5px] mt-1 bg-white/70 dark:bg-slate-900/50 p-1.5 rounded-lg border border-gray-100 dark:border-slate-800" x-text="'“' + detailData.interview_admin_notes + '”'"></p>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Jadwal Wawancara Pelamar --}}
                        <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 text-xs">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-xs">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Jadwal Wawancara (Interview)
                                </h3>
                                <a :href="'{{ $isRecruiter ? route('recruiter.interview_schedule') : route('admin.interview_schedule') }}'" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                    <span>Kelola Jadwal</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>

                            <template x-if="(detailData.interview_schedules && detailData.interview_schedules.length > 0) || (detailData.interviewSchedules && detailData.interviewSchedules.length > 0)">
                                <div class="space-y-2">
                                    <template x-for="item in (detailData.interview_schedules || detailData.interviewSchedules || [])" :key="item.id">
                                        <div class="p-3 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-emerald-900 dark:text-emerald-300 text-xs" x-text="new Date(item.interview_date).toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' }) + ' WIB'"></span>
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300" x-text="item.status"></span>
                                            </div>
                                            <div class="text-[11px] text-gray-700 dark:text-gray-300">
                                                <p><strong class="font-semibold">Lokasi/Metode:</strong> <span x-text="item.location"></span></p>
                                                <p x-show="item.meeting_link">
                                                    <strong class="font-semibold">Link Meeting:</strong> 
                                                    <a :href="item.meeting_link" target="_blank" class="text-indigo-600 dark:text-indigo-400 underline font-semibold break-all" x-text="item.meeting_link"></a>
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="(!detailData.interview_schedules || detailData.interview_schedules.length === 0) && (!detailData.interviewSchedules || detailData.interviewSchedules.length === 0)">
                                <div class="p-3 bg-white/50 dark:bg-slate-900/50 rounded-xl text-gray-400 italic text-[11px]">
                                    Belum ada jadwal wawancara untuk pelamar ini.
                                </div>
                            </template>
                        </div>

                        {{-- Riwayat Perubahan Status (Timeline History) --}}
                        <div class="p-4 bg-gray-50/80 dark:bg-slate-800/40 rounded-2xl border border-gray-100 dark:border-slate-800 text-xs">
                            <h3 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2 text-xs">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Riwayat Perubahan Status Seleksi
                            </h3>
                            <template x-if="detailData.status_histories && detailData.status_histories.length > 0">
                                <div class="space-y-2 border-l-2 border-indigo-200 dark:border-indigo-900/60 ml-2 pl-4">
                                    <template x-for="history in detailData.status_histories" :key="history.id">
                                        <div class="relative pb-2">
                                            <div class="absolute -left-[21px] top-1.5 w-2.5 h-2.5 rounded-full bg-indigo-600 dark:bg-indigo-400"></div>
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-gray-900 dark:text-white text-xs" x-text="history.status_label || ({'Submitted':'Diajukan','Reviewed':'Lolos Berkas','Shortlisted':'Lolos Ujian','Interview':'Wawancara','Accepted':'Diterima','Rejected':'Ditolak','Partial Approved':'Partial Approved'}[history.status] || history.status || 'Diajukan')"></span>
                                                <span class="text-[10px] text-gray-400 font-mono" x-text="formatDateTime(history.changed_at)"></span>
                                            </div>
                                            <p x-show="history.notes" class="text-gray-600 dark:text-slate-300 text-[11px] mt-0.5" x-text="'Catatan: ' + history.notes"></p>
                                            <div x-show="history.changed_by" class="text-[10px] text-gray-400 italic" x-text="'Diubah oleh: ' + (history.changed_by ? (history.changed_by.name || 'Admin') : 'Admin')"></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!detailData.status_histories || detailData.status_histories.length === 0">
                                <div class="text-gray-400 italic text-[11px]">
                                    Belum ada riwayat perubahan status tercatat.
                                </div>
                            </template>
                        </div>
                    </section>

                </div>
            </div>

            {{-- 4. Sticky Modal Bottom Actions --}}
            <div class="px-4 sm:px-6 py-3.5 bg-white/95 dark:bg-[#0d1527]/95 border-t border-gray-200 dark:border-slate-800 flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-2.5 shrink-0 z-20">
                <button type="button" @click="showDetailModal = false" class="w-full sm:w-auto px-4 py-2.5 text-xs font-semibold text-center text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer">
                    Tutup
                </button>
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
                    <a :href="'{{ ($isRecruiter && !$isAdmin) ? url('/recruiter/applicants') : url('/admin/applicants') }}/' + detailData.id + '/profile-pdf'"
                       target="_blank"
                       class="w-full sm:w-auto px-4 py-2.5 text-xs font-semibold text-slate-200 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span>Preview & Cetak PDF</span>
                    </a>
                    <template x-if="detailData.applicant_profile && (detailData.applicant_profile.cv_file_url || detailData.applicant_profile.cv_file_path)">
                        <a :href="detailData.applicant_profile.cv_file_url ? detailData.applicant_profile.cv_file_url : ('/storage/' + detailData.applicant_profile.cv_file_path)"
                           target="_blank"
                           class="w-full sm:w-auto px-4 py-2.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Buka CV Asli</span>
                        </a>
                    </template>
                    <button type="button" @click="showDetailModal = false; openStatusModal({
                        id: detailData.id,
                        applicant_name: detailData.applicant_profile ? detailData.applicant_profile.full_name : 'Pelamar',
                        applicant_email: detailData.applicant_profile && detailData.applicant_profile.user ? detailData.applicant_profile.user.email : '',
                        job_title: detailData.job ? detailData.job.title : 'Lowongan',
                        status: detailData.status || 'Submitted',
                        notes: detailData.notes || '',
                        recruiter_approval: detailData.recruiter_approval || 'pending',
                        recruiter_notes: detailData.recruiter_notes || '',
                        admin_approval: detailData.admin_approval || 'pending',
                        admin_notes: detailData.admin_notes || '',
                        test_recruiter_approval: detailData.test_recruiter_approval || 'pending',
                        test_recruiter_notes: detailData.test_recruiter_notes || '',
                        test_recruiter: detailData.test_recruiter || detailData.testRecruiter || null,
                        test_admin_approval: detailData.test_admin_approval || 'pending',
                        test_admin_notes: detailData.test_admin_notes || '',
                        test_admin: detailData.test_admin || detailData.testAdmin || null,
                        interview_recruiter_approval: detailData.interview_recruiter_approval || 'pending',
                        interview_recruiter_notes: detailData.interview_recruiter_notes || '',
                        interview_recruiter: detailData.interview_recruiter || detailData.interviewRecruiter || null,
                        interview_admin_approval: detailData.interview_admin_approval || 'pending',
                        interview_admin_notes: detailData.interview_admin_notes || '',
                        interview_admin: detailData.interview_admin || detailData.interviewAdmin || null,
                    })" class="w-full sm:w-auto px-4 py-2.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-600 dark:hover:bg-indigo-500 rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Proses / Update Status</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
