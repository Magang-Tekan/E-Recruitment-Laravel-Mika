@php
    // Fallback otomatis jika $groupCompanies tidak di-pass dari view induk
    $groupCompanies = $groupCompanies ?? \App\Models\Company::where('name', '!=', 'PT Mitra Karya Analitika')
        ->where('name', 'not like', '%Mitra Karya Analitika%')
        ->orderBy('id', 'asc')
        ->get();
@endphp

@if(isset($groupCompanies) && $groupCompanies->count() > 0)
    <section id="meet-the-group" class="relative py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-t border-[#93F514]/20">


        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-14 sm:mb-16 relative z-10">
            {{-- <div class="reveal-on-scroll inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-bold uppercase tracking-wider mb-4 shadow-sm shadow-[#93F514]/15">
                Ekosistem Grup Perusahaan
            </div> --}}
            <h2 class="reveal-on-scroll text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#EEEEEE] tracking-tight" data-delay="100">
                Grup <span class="text-[#93F514]">Perusahaan</span>
            </h2>
            <p class="reveal-on-scroll mt-3.5 text-sm sm:text-base text-gray-300 max-w-2xl mx-auto leading-relaxed" data-delay="150">
                Sinergi strategis lintas entitas dalam menghadirkan solusi teknologi cerdas, manufaktur perangkat keras, dan jaringan distribusi terpadu.
            </p>
        </div>

        <!-- Cards Grid (1 kolom di Mobile, 2 kolom di Tablet, 3 kolom di Desktop) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 relative z-10">
            @foreach($groupCompanies as $idx => $companyItem)
                @php
                    // Helper URL website
                    $companyWebUrl = null;
                    if (!empty($companyItem->website)) {
                        $companyWebUrl = \Illuminate\Support\Str::startsWith($companyItem->website, ['http://', 'https://'])
                            ? $companyItem->website
                            : 'https://' . $companyItem->website;
                    }

                    // Helper logo URL
                    $companyLogoUrl = $companyItem->logo_url ?? null;
                    if (!$companyLogoUrl && !empty($companyItem->logo)) {
                        $companyLogoUrl = \Illuminate\Support\Str::startsWith($companyItem->logo, ['http://', 'https://', '//'])
                            ? $companyItem->logo
                            : asset('storage/' . ltrim($companyItem->logo, '/'));
                    }

                    // Fallback initials (misal: AKA -> AK, Tekna -> SN/TK, Agra -> AP)
                    $companyInitial = $companyItem->initial ?? strtoupper(substr($companyItem->name, 0, 2));
                @endphp

                <div class="reveal-on-scroll group relative rounded-3xl bg-gradient-to-b from-[#061506] via-[#040e04] to-[#020602] border border-[#93F514]/30 hover:border-[#93F514] p-7 sm:p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-black/60 flex flex-col justify-between overflow-hidden group-company-card" 
                     data-delay="{{ 100 + ($idx * 75) }}">

                    <div>
                        <!-- Header Kartu: Logo & Tagline / Badge Kategori -->
                        <div class="flex items-start justify-between gap-3 mb-6 relative z-10">
                            <!-- Logo / Initial Avatar (Klik ke Detail Perusahaan) -->
                            <a href="{{ route('companies.show', $companyItem->id) }}" 
                               class="shrink-0 transition-transform duration-300 hover:scale-105"
                               title="Lihat profil {{ $companyItem->name }}">
                                @if($companyLogoUrl)
                                    <div class="w-14 h-14 rounded-2xl bg-white p-2 flex items-center justify-center border border-[#93F514]/30 shadow-md group-hover:border-[#93F514] group-hover:shadow-md transition-all duration-300">
                                        <img src="{{ $companyLogoUrl }}" alt="{{ $companyItem->name }}" class="w-full h-full object-contain">
                                    </div>
                                @else
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#93F514]/20 via-[#0a230a] to-[#040e04] border border-[#93F514]/40 flex items-center justify-center text-[#93F514] font-black text-lg sm:text-xl tracking-wider shadow-md group-hover:bg-[#93F514] group-hover:text-black group-hover:shadow-md transition-all duration-300 group-initial-avatar">
                                        {{ $companyInitial }}
                                    </div>
                                @endif
                            </a>

                            <!-- Tagline / Badge Kategori -->
                            @if($companyItem->tagline)
                                <span class="inline-block px-3 py-1.5 rounded-full bg-[#93F514]/10 border border-[#93F514]/30 text-[#93F514] text-[11px] sm:text-xs font-bold text-right tracking-tight max-w-[190px] truncate shadow-sm group-tagline-badge">
                                    {{ $companyItem->tagline }}
                                </span>
                            @else
                                <span class="inline-block px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-gray-400 text-[11px] font-semibold group-sinergi-badge">
                                    Entitas Afiliasi
                                </span>
                            @endif
                        </div>

                        <!-- Nama Perusahaan (Tautan ke Detail Profil) -->
                        <h3 class="text-lg sm:text-xl font-extrabold text-[#EEEEEE] group-hover:text-[#93F514] transition-colors duration-200 mb-3 relative z-10">
                            <a href="{{ route('companies.show', $companyItem->id) }}" 
                               class="hover:underline flex items-center justify-between gap-2">
                                <span>{{ $companyItem->name }}</span>
                                <svg class="w-4 h-4 text-[#93F514] opacity-0 group-hover:opacity-100 transition-all duration-200 -translate-x-1 group-hover:translate-x-0 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </h3>

                        <!-- Deskripsi Singkat -->
                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed line-clamp-4 relative z-10">
                            {{ $companyItem->about ?: ($companyItem->tagline ?: 'Entitas bagian dari ekosistem grup yang bersinergi dalam memperkuat keandalan solusi dan keunggulan operasional.') }}
                        </p>
                    </div>

                    <!-- Footer Kartu: Lokasi & Tombol Aksi (Detail & Website) -->
                    <div class="mt-8 pt-5 border-t border-[#93F514]/15 flex items-center justify-between gap-3 relative z-10">
                        <!-- Indikator Lokasi -->
                        <div class="text-[11px] sm:text-xs text-gray-400 flex items-center gap-1.5 truncate">
                            <svg class="w-3.5 h-3.5 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ $companyItem->city ?? 'Semarang' }}</span>
                        </div>

                        <!-- Aksi: Detail Profil & Website -->
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('companies.show', $companyItem->id) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#93F514]/10 hover:bg-[#93F514] text-[#93F514] hover:text-black border border-[#93F514]/30 hover:border-[#93F514] font-bold text-xs transition-all duration-200 shadow-sm shrink-0 group/btn group-detail-btn">
                                <span>Detail</span>
                                <svg class="w-3.5 h-3.5 transition-transform group-hover/btn:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>

                            @if($companyWebUrl)
                                <a href="{{ $companyWebUrl }}" target="_blank" rel="noopener noreferrer" 
                                   title="Kunjungi Website Resmi: {{ $companyItem->name }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white border border-white/10 hover:border-[#93F514]/40 transition-all duration-200 shadow-sm shrink-0 group-website-icon-btn">
                                    <svg class="w-3.5 h-3.5 text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
