@extends('frontend.layouts.app')

{{-- @section('title', 'Tentang Kami | ' . ($company->name ?? 'PT Mitra Karya Analitika')) --}}
@section('title', 'Tentang Kami | Mika Career')

@section('content')
    <div class="relative overflow-hidden">

        <!-- ==================== HERO SECTION (SPLIT LAYOUT WITH DYNAMIC PHOTO COLLAGE) ==================== -->
        <section class="relative min-h-[70vh] lg:min-h-[82vh] flex items-center pt-16 pb-20 px-4 sm:px-6 lg:px-8 bg-[#040804] overflow-hidden"
            x-data="{
                currentSlide: 0,
                slides: [
                    {
                        tag: 'Keluarga & Kebersamaan',
                        img1: '{{ asset('storage/asset-compro/aniv1.jpg') }}',
                        img2: '{{ asset('storage/asset-compro/outbond.jpg') }}',
                        img3: '{{ asset('storage/asset-compro/aniv.jpg') }}',
                        badgeTitle: 'CAREER',
                        badgeSub: 'Culture & Growth'
                    },
                    {
                        tag: 'Bimtek & Kompetensi',
                        img1: '{{ asset('storage/asset-compro/aspadin1.jpg') }}',
                        img2: '{{ asset('storage/asset-compro/aspadin2.jpg') }}',
                        img3: '{{ asset('storage/asset-compro/aspadin3.jpg') }}',
                        badgeTitle: 'EVENT',
                        badgeSub: 'Technical Guidance'
                    },
                    {
                        tag: 'Pameran & Inovasi',
                        img1: '{{ asset('storage/asset-compro/hisfarin1.jpg') }}',
                        img2: '{{ asset('storage/asset-compro/hisfarin2.jpg') }}',
                        img3: '{{ asset('storage/asset-compro/hisfarin3.jpg') }}',
                        badgeTitle: 'EXHIBITION',
                        badgeSub: 'HISFARIN 2025'
                    }
                ],
                autoplayTimer: null,
                duration: 4500,
                init() {
                    this.startAutoplay();
                },
                startAutoplay() {
                    this.stopAutoplay();
                    this.autoplayTimer = setInterval(() => {
                        this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                    }, this.duration);
                },
                stopAutoplay() {
                    if (this.autoplayTimer) clearInterval(this.autoplayTimer);
                },
                goTo(index) {
                    this.currentSlide = index;
                    this.startAutoplay();
                },
                next() {
                    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                    this.startAutoplay();
                },
                prev() {
                    this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
                    this.startAutoplay();
                }
            }"
            @mouseenter="stopAutoplay()"
            @mouseleave="startAutoplay()">



            <!-- Subtle Grid Background Pattern -->
            <div class="absolute inset-0 z-0 opacity-15 bg-[radial-gradient(#93F514_1px,transparent_1px)] [background-size:28px_28px] pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto w-full z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">

                    <!-- Left Column: Staggered Dynamic Photo Collage & Floating Badge -->
                    <div class="lg:col-span-6 relative flex items-center justify-center lg:justify-start py-6 lg:py-0 order-2 lg:order-1">
                        <div class="relative w-full max-w-[540px] h-[340px] sm:h-[400px]">

                            <template x-for="(slide, index) in slides" :key="index">
                                <div x-show="currentSlide === index"
                                    x-transition:enter="transition ease-out duration-700"
                                    x-transition:enter-start="opacity-0 scale-95 translate-x-4"
                                    x-transition:enter-end="opacity-100 scale-100 translate-x-0"
                                    x-transition:leave="transition ease-in duration-500 absolute inset-0"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95 -translate-x-4"
                                    class="w-full h-full relative">

                                    <!-- Main Large Photo (Left Background Layer) -->
                                    <div class="absolute left-0 top-6 w-[56%] sm:w-[58%] h-[260px] sm:h-[310px] rounded-2xl sm:rounded-3xl p-1.5 bg-gradient-to-br from-[#93F514]/50 via-white/10 to-transparent shadow-2xl shadow-black/80 group">
                                        <div class="w-full h-full rounded-[14px] sm:rounded-[22px] overflow-hidden bg-black/40 border border-white/20">
                                            <img :src="slide.img1" alt="Dokumentasi Perusahaan"
                                                class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-500 filter brightness-95">
                                        </div>
                                    </div>

                                    <!-- Top-Right Secondary Photo Layer -->
                                    <div class="absolute right-0 top-0 w-[48%] sm:w-[50%] h-[200px] sm:h-[235px] rounded-2xl sm:rounded-3xl p-1.5 bg-gradient-to-bl from-[#93F514]/60 via-white/15 to-transparent shadow-2xl shadow-black/90 group z-10">
                                        <div class="w-full h-full rounded-[14px] sm:rounded-[22px] overflow-hidden bg-black/40 border border-white/20">
                                            <img :src="slide.img2" alt="Dokumentasi Aktivitas"
                                                class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-500 filter brightness-95">
                                        </div>
                                    </div>

                                    <!-- Bottom Center/Right Overlapping Tertiary Photo -->
                                    <div class="absolute left-[35%] sm:left-[32%] bottom-0 w-[50%] sm:w-[52%] h-[190px] sm:h-[220px] rounded-2xl sm:rounded-3xl p-1.5 bg-gradient-to-tr from-[#93F514]/60 via-white/15 to-transparent shadow-2xl shadow-black/95 group z-20">
                                        <div class="w-full h-full rounded-[14px] sm:rounded-[22px] overflow-hidden bg-black/40 border border-white/25">
                                            <img :src="slide.img3" alt="Dokumentasi Event"
                                                class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-500 filter brightness-95">
                                        </div>
                                    </div>

                                    <!-- Floating Glass Badge (Bottom Right) -->
                                    <div class="absolute -right-1 sm:-right-3 bottom-2 z-30 transform hover:scale-105 transition-transform">
                                        <div class="about-hero-badge px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl bg-[#061806]/90 border border-[#93F514]/50 shadow-lg shadow-black/80 backdrop-blur-md flex items-center">
                                            <div>
                                                <div class="about-hero-badge-title text-[11px] sm:text-xs font-bold tracking-wider text-[#93F514] leading-none"
                                                    x-text="slide.badgeTitle"></div>
                                                <div class="about-hero-badge-sub text-[10px] text-gray-300 font-medium mt-0.5 leading-none"
                                                    x-text="slide.badgeSub"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </template>

                            <!-- Slide Prev/Next Arrows overlay for easy control -->
                            <div class="absolute -bottom-10 left-0 z-30 flex items-center gap-2">
                                <button @click="prev()" aria-label="Previous Slide"
                                    class="w-8 h-8 rounded-lg bg-[#061506] hover:bg-[#93F514] text-gray-300 hover:text-black border border-[#93F514]/40 transition-all flex items-center justify-center cursor-pointer shadow-md">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button @click="next()" aria-label="Next Slide"
                                    class="w-8 h-8 rounded-lg bg-[#061506] hover:bg-[#93F514] text-gray-300 hover:text-black border border-[#93F514]/40 transition-all flex items-center justify-center cursor-pointer shadow-md">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>

                        </div>
                    </div>

                    <!-- Right Column: Typography & Content -->
                    <div class="lg:col-span-6 space-y-6 text-left order-1 lg:order-2">
                        <!-- Main Heading -->
                        <h1 class="reveal-on-scroll text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold tracking-tight text-[#EEEEEE] leading-[1.15]" data-delay="100">
                            Mengenal Lebih Dekat <br>
                            <span class="text-[#93F514]">
                                {{ $company->name ?? 'PT Mitra Karya Analitika' }}
                            </span>
                        </h1>

                        <!-- Tagline Quote -->
                        <p class="reveal-on-scroll text-base sm:text-lg lg:text-xl text-gray-200 font-medium italic leading-relaxed" data-delay="150">
                            &ldquo;{{ $company->tagline ?? 'The Best Choice for Your Business Partner' }}&rdquo;
                        </p>

                        <!-- Subtitle -->
                        <p class="reveal-on-scroll text-xs sm:text-sm md:text-base text-gray-300 leading-relaxed max-w-xl" data-delay="200">
                            Mitra terpercaya di Indonesia dalam penyediaan instrumen laboratorium modern, keselamatan kerja (HSE), dan teknologi pemantauan lingkungan sejak 2014.
                        </p>

                        <!-- Action Buttons & Quick Nav -->
                        <div class="reveal-on-scroll pt-2 flex flex-wrap items-center gap-3.5" data-delay="250">
                            <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-[#93F514] hover:bg-[#a6ff2e] text-black font-extrabold text-xs sm:text-sm tracking-wide shadow-md shadow-black/30 hover:scale-[1.02] transition-all duration-200">
                                <span>Lihat Lowongan Karir</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                            <a href="#meet-the-group" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-[#061506] hover:bg-[#092209] text-[#EEEEEE] hover:text-[#93F514] border border-[#93F514]/40 font-bold text-xs sm:text-sm transition-all duration-200 shadow-sm">
                                <span>Ekosistem Grup</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </div>

                        <!-- Slide Navigation Dots (Controls) -->
                        <div class="reveal-on-scroll pt-4 flex items-center gap-2.5" data-delay="300">
                            <span class="text-xs text-gray-400 font-semibold mr-1 about-doc-label">Dokumentasi:</span>
                            <template x-for="(slide, index) in slides" :key="index">
                                <button @click="goTo(index)"
                                    :class="currentSlide === index ? 'w-8 bg-[#93F514] shadow-sm about-dot-active' : 'w-2.5 bg-white/25 hover:bg-white/45 border border-white/10 about-dot-inactive'"
                                    class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                                    :aria-label="'Slide ' + (index + 1)">
                                </button>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        <!-- ==================== PROFIL SINGKAT & CERITA PERJALANAN ==================== -->
        <section class="relative py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Narrative Column (7 cols) -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="reveal-on-scroll inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-bold uppercase tracking-wider">
                        Profil & Eksistensi Perusahaan
                    </div>

                    <h2 class="reveal-on-scroll text-2xl sm:text-4xl font-extrabold text-[#EEEEEE] leading-tight" data-delay="100">
                        Solusi Presisi untuk <span class="text-[#93F514]">Laboratorium, HSE, & Lingkungan</span>
                    </h2>

                    <div class="reveal-on-scroll text-sm sm:text-base text-gray-300 leading-relaxed space-y-4" data-delay="150">
                        @if($company->about)
                            <p class="text-justify">
                                {{ $company->about }}
                            </p>
                        @else
                            <p class="text-justify">
                                <strong>{{ $company->name ?? 'PT Mitra Karya Analitika' }} (MIKA)</strong> didirikan sejak tahun 2014 di Kota Semarang, Jawa Tengah. Berawal dari komitmen tinggi untuk mendukung kemajuan riset, industri, dan keberlanjutan lingkungan hidup di Indonesia, kami bertumbuh menjadi penyedia solusi instrumen analitika dan keselamatan terpercaya.
                            </p>
                            <p class="text-justify">
                                Selama lebih dari 10 tahun perjalanan, kami secara konsisten menyuplai instrumen laboratorium mutakhir, alat pelindung diri (HSE), hingga peralatan pemantauan kualitas lingkungan hidup kepada ratusan mitra bisnis di seluruh nusantara.
                            </p>
                        @endif
                    </div>

                    <!-- Bullet Highlights -->
                    <div class="reveal-on-scroll grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2" data-delay="200">
                        <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-[#061506] border border-[#93F514]/25">
                            <div class="w-8 h-8 rounded-xl bg-[#93F514]/15 text-[#93F514] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-[#EEEEEE]">Kualitas Internasional</h4>
                                <p class="text-xs text-gray-400 mt-0.5">Produk berstandar mutu tinggi dari manufaktur teruji.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3.5 rounded-2xl bg-[#061506] border border-[#93F514]/25">
                            <div class="w-8 h-8 rounded-xl bg-[#93F514]/15 text-[#93F514] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-[#EEEEEE]">Dukungan Purna Jual</h4>
                                <p class="text-xs text-gray-400 mt-0.5">Layanan teknis, garansi resmi, dan pendampingan ahli.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Visual Column (5 cols) -->
                <div class="lg:col-span-5 reveal-on-scroll" data-delay="200">
                    <div class="relative rounded-3xl bg-gradient-to-b from-[#071a07] via-[#051105] to-[#040804] border border-[#93F514]/40 p-8 shadow-2xl shadow-black/60 overflow-hidden">

                        <!-- Brand Logo Presentation -->
                        <div class="flex items-center gap-4 pb-6 border-b border-[#93F514]/20">
                            @if($company->logo_url)
                                <div class="w-16 h-16 rounded-2xl bg-white p-2.5 flex items-center justify-center border border-[#93F514]/30 shadow-md shrink-0">
                                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="w-full h-full object-contain">
                                </div>
                            @else
                                <div class="w-16 h-16 rounded-2xl bg-[#93F514]/20 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] font-black text-2xl shrink-0">
                                    M
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-bold text-[#EEEEEE]">{{ $company->name ?? 'PT Mitra Karya Analitika' }}</h3>
                                <p class="text-xs text-[#93F514] font-semibold tracking-wide">Business Partner of Excellence</p>
                            </div>
                        </div>

                        <!-- Highlights List -->
                        <div class="py-6 space-y-4 text-xs text-gray-300">
                            <div class="flex items-center justify-between py-2 border-b border-white/5">
                                <span class="text-gray-400">Tahun Pendirian:</span>
                                <span class="font-bold text-[#EEEEEE]">2014</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-white/5">
                                <span class="text-gray-400">Pengalaman:</span>
                                <span class="font-bold text-[#93F514]">10+ Tahun Berkelanjutan</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-white/5">
                                <span class="text-gray-400">Fokus Utama:</span>
                                <span class="font-bold text-[#EEEEEE]">Lab, HSE & Environmental</span>
                            </div>
                            {{-- <div class="flex items-center justify-between py-2 border-b border-white/5">
                                <span class="text-gray-400">Status Operasional:</span>
                                <span class="inline-flex items-center gap-1.5 font-bold text-emerald-400">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    Aktif & Berkembang
                                </span>
                            </div> --}}
                            @if(isset($totalJobsCount) && $totalJobsCount > 0)
                                <div class="flex items-center justify-between pt-2">
                                    <span class="text-gray-400">Peluang Karir:</span>
                                    <a href="{{ route('jobs.index') }}" class="font-bold text-[#93F514] hover:underline inline-flex items-center gap-1 group">
                                        <span>{{ $totalJobsCount }} Lowongan Terbuka</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 shrink-0 transition-transform group-hover:translate-x-0.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Action Button inside Card -->
                        <div class="pt-4 border-t border-[#93F514]/20">
                            <a href="{{ route('jobs.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#93F514] hover:bg-[#a6ff2e] text-black font-extrabold text-xs sm:text-sm tracking-wide shadow-md shadow-black/30 hover:scale-[1.02] transition-all duration-200">
                                <span>Bergabung Bersama Kami</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- ==================== VISI & MISI ==================== -->
        <section class="relative py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-t border-[#93F514]/20">


            <div class="text-center max-w-3xl mx-auto mb-16">
                {{-- <div class="reveal-on-scroll inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-bold uppercase tracking-wider mb-4">
                    Landasan Strategis
                </div> --}}
                <h2 class="reveal-on-scroll text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#EEEEEE]" data-delay="100">
                    Visi & <span class="text-[#93F514]">Misi Kami</span>
                </h2>
                <p class="reveal-on-scroll mt-3 text-sm sm:text-base text-gray-300" data-delay="150">
                    Arah tujuan jangka panjang dan komitmen berkesinambungan yang memandu setiap langkah operasional kami.
                </p>
            </div>

            <!-- Visi Card (Executive Showcase) -->
            <div class="reveal-on-scroll relative rounded-3xl bg-gradient-to-br from-[#061806] via-[#041004] to-[#020702] border border-[#93F514]/35 hover:border-[#93F514]/70 p-8 sm:p-12 mb-14 shadow-2xl shadow-black/60 overflow-hidden transition-all duration-300 visi-card" data-delay="150">
                <span class="absolute -right-4 -bottom-10 text-[180px] font-serif font-black text-[#93F514]/5 pointer-events-none select-none leading-none">&ldquo;</span>

                <div class="relative z-10">
                    <!-- Top Badge & Context -->
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-bold uppercase tracking-wider shadow-sm visi-badge">
                            <span>Visi Perusahaan</span>
                        </div>
                        <span class="text-xs text-gray-400 font-semibold tracking-wide hidden sm:inline-block">Arah & Komitmen Strategis</span>
                    </div>

                    <!-- Vision Quote Statement -->
                    <blockquote class="pl-6 sm:pl-8 border-l-4 border-[#93F514] my-4">
                        <p class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-[#EEEEEE] leading-snug sm:leading-relaxed tracking-tight">
                            &ldquo;{{ $company->vision ?? 'Menjadi mitra bisnis terdepan, terpercaya, dan menjadi pilihan utama di Indonesia dalam penyediaan solusi komprehensif peralatan laboratorium, perlindungan keselamatan kerja, dan teknologi pemantauan lingkungan.' }}&rdquo;
                        </p>
                    </blockquote>

                    <!-- 3 Strategic Focus Pillars Beneath Vision -->
                    <div class="mt-10 pt-8 border-t border-[#93F514]/15 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Pillar 1: Laboratory -->
                        <div class="flex items-center gap-3.5 p-4 rounded-2xl bg-[#030d03]/80 border border-[#93F514]/20 hover:border-[#93F514]/40 transition visi-pillar-card">
                            <div class="w-10 h-10 rounded-xl bg-[#93F514]/15 border border-[#93F514]/30 text-[#93F514] flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-xs sm:text-sm font-bold text-[#EEEEEE]">Solusi Laboratorium</h5>
                                <p class="text-[11px] text-gray-400">Presisi & Teruji Mutu</p>
                            </div>
                        </div>

                        <!-- Pillar 2: HSE -->
                        <div class="flex items-center gap-3.5 p-4 rounded-2xl bg-[#030d03]/80 border border-[#93F514]/20 hover:border-[#93F514]/40 transition visi-pillar-card">
                            <div class="w-10 h-10 rounded-xl bg-[#93F514]/15 border border-[#93F514]/30 text-[#93F514] flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-xs sm:text-sm font-bold text-[#EEEEEE]">Keselamatan Kerja (HSE)</h5>
                                <p class="text-[11px] text-gray-400">Proteksi Standar Internasional</p>
                            </div>
                        </div>

                        <!-- Pillar 3: Environmental -->
                        <div class="flex items-center gap-3.5 p-4 rounded-2xl bg-[#030d03]/80 border border-[#93F514]/20 hover:border-[#93F514]/40 transition visi-pillar-card">
                            <div class="w-10 h-10 rounded-xl bg-[#93F514]/15 border border-[#93F514]/30 text-[#93F514] flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-xs sm:text-sm font-bold text-[#EEEEEE]">Monitoring Lingkungan</h5>
                                <p class="text-[11px] text-gray-400">Solusi Berkelanjutan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Misi 3 Poin Grid -->
            <div>
                <h3 class="reveal-on-scroll text-lg font-bold text-gray-300 mb-6 flex items-center gap-2" data-delay="200">
                    {{-- <span class="w-2.5 h-2.5 rounded-full bg-[#93F514]"></span> --}}
                    Poin Misi Berkelanjutan
                </h3>

                @php
                    $missionsList = $company->missions;
                    if (empty($missionsList) || !is_array($missionsList)) {
                        $missionsList = [
                            'Menyediakan instrumen laboratorium, alat pelindung keselamatan kerja (HSE), dan sistem monitoring lingkungan berkualitas tinggi berstandar internasional.',
                            'Memberikan layanan purna jual, kalibrasi, konsultasi teknis terpadu, dan dukungan profesional yang responsif demi kepuasan mitra bisnis.',
                            'Membangun ekosistem kemitraan strategis yang berkelanjutan bersama industri manufaktur, institusi riset, akademisi, dan instansi pemerintah di seluruh Indonesia.',
                        ];
                    }
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($missionsList as $index => $mission)
                        <div class="reveal-on-scroll relative rounded-3xl bg-gradient-to-b from-[#061506] to-[#030803] border border-[#93F514]/30 hover:border-[#93F514] p-7 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-black/60 flex flex-col justify-between misi-card" data-delay="{{ 200 + ($index * 100) }}">
                            <div>
                                <div class="flex items-center justify-between mb-5">
                                    <span class="text-3xl font-black text-[#93F514]/70 tracking-tight">0{{ $index + 1 }}</span>
                                    <div class="w-10 h-10 rounded-xl bg-[#93F514]/15 border border-[#93F514]/30 flex items-center justify-center text-[#93F514]">
                                        @if($index === 0)
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                            </svg>
                                        @elseif($index === 1)
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm text-gray-200 leading-relaxed">
                                    {{ $mission }}
                                </p>
                            </div>
                            <div class="mt-6 pt-4 border-t border-[#93F514]/15 flex items-center gap-2 text-xs text-[#93F514] font-semibold">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#93F514]"></span> --}}
                                <span>Misi Berkelanjutan</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        <!-- ==================== CORE VALUES (MIKA) ==================== -->
        <section class="relative py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-t border-[#93F514]/20">
            <div class="text-center max-w-3xl mx-auto mb-16">
                {{-- <div class="reveal-on-scroll inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-bold uppercase tracking-wider mb-4">
                    Budaya Perusahaan
                </div> --}}
                <h2 class="reveal-on-scroll text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#EEEEEE]" data-delay="100">
                    Nilai Perusahaan <span class="text-[#93F514]">(MIKA)</span>
                </h2>
                <p class="reveal-on-scroll mt-3 text-sm sm:text-base text-gray-300" data-delay="150">
                    Empat pilar integritas yang menjadi kompas perilaku, etika bisnis, dan budaya kerja seluruh insan PT Mitra Karya Analitika.
                </p>
            </div>

            @php
                $coreValuesList = $company->core_values;
                if (empty($coreValuesList) || !is_array($coreValuesList)) {
                    $coreValuesList = [
                        [
                            'code' => 'M',
                            'title' => 'Menghargai',
                            'subtitle' => 'Respect',
                            'description' => 'Menjunjung tinggi rasa hormat, menghargai keberagaman pandangan, serta membina komunikasi kerja yang inklusif dan harmonis bagi seluruh insan perusahaan dan mitra.',
                        ],
                        [
                            'code' => 'I',
                            'title' => 'Integritas',
                            'subtitle' => 'Integrity',
                            'description' => 'Berpikir, berkata, dan bertindak secara jujur, adil, transparan, serta berpegang teguh pada prinsip moral dan kode etik bisnis profesional tanpa kompromi.',
                        ],
                        [
                            'code' => 'K',
                            'title' => 'Komitmen',
                            'subtitle' => 'Commitment',
                            'description' => 'Berdedikasi tinggi untuk memberikan pelayanan berkualitas terbaik, menepati janji kesepakatan, dan terus berinovasi menjawab kebutuhan industri secara berkesinambungan.',
                        ],
                        [
                            'code' => 'A',
                            'title' => 'Akuntabel',
                            'subtitle' => 'Accountable',
                            'description' => 'Bertanggung jawab penuh atas setiap keputusan, tindakan, dan hasil kerja demi menjaga kepercayaan mitra serta pemangku kepentingan secara transparan.',
                        ],
                    ];
                }
            @endphp

            <!-- 4 Interactive Pillar Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($coreValuesList as $i => $val)
                    @php
                        $codeLetter = $val['code'] ?? substr($val['title'] ?? 'M', 0, 1);
                        $isLetterI = ($codeLetter === 'I');
                    @endphp
                    <div class="reveal-on-scroll group relative rounded-3xl bg-gradient-to-b from-[#061506] via-[#040e04] to-[#020602] border border-[#93F514]/30 hover:border-[#93F514] p-7 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-black/60 flex flex-col justify-between overflow-hidden mika-card" data-delay="{{ 150 + ($i * 75) }}">
                        <!-- Glowing letter background watermark (same font family) -->
                        <span class="absolute {{ $isLetterI ? 'right-4 sm:right-6 -bottom-6' : '-right-4 -bottom-6' }} text-8xl font-black text-[#93F514]/5 group-hover:text-[#93F514]/15 transition-colors duration-300 pointer-events-none select-none mika-watermark">
                            {{ $codeLetter }}
                        </span>

                        <div>
                            <!-- Header Icon & Letter Pill (same font family for all M-I-K-A) -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] font-black text-3xl group-hover:bg-[#93F514] group-hover:text-black transition-all duration-300 shadow-sm mika-letter-box">
                                    <span class="leading-none {{ $isLetterI ? 'scale-y-110 tracking-wider' : '' }}">
                                        {{ $codeLetter }}
                                    </span>
                                </div>
                                <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 group-hover:text-[#93F514] transition-colors">
                                    Pilar {{ $i + 1 }}
                                </span>
                            </div>

                            <!-- Title & Subtitle -->
                            <h3 class="text-xl font-extrabold text-[#EEEEEE] group-hover:text-[#93F514] transition-colors">
                                {{ $val['title'] ?? '' }}
                            </h3>
                            <span class="inline-block text-xs font-semibold text-[#5FE6B6] mb-3 mika-subtitle">
                                {{ $val['subtitle'] ?? '' }}
                            </span>

                            <!-- Description -->
                            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                                {{ $val['description'] ?? '' }}
                            </p>
                        </div>

                        <!-- Card Footer -->
                        <div class="mt-8 pt-4 border-t border-[#93F514]/15 flex items-center gap-2">
                            {{-- <span class="w-2 h-2 rounded-full bg-[#93F514]"></span> --}}
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Nilai Inti MIKA</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>


        <!-- ==================== UNIT BISNIS & FOKUS LAYANAN ==================== -->
        <section class="relative py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-t border-[#93F514]/20">
            <div class="text-center max-w-3xl mx-auto mb-16">
                {{-- <div class="reveal-on-scroll inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-bold uppercase tracking-wider mb-4">
                    Bidang Keahlian
                </div> --}}
                <h2 class="reveal-on-scroll text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#EEEEEE]" data-delay="100">
                    Unit Bisnis & <span class="text-[#93F514]">Fokus Layanan</span>
                </h2>
                <p class="reveal-on-scroll mt-3 text-sm sm:text-base text-gray-300" data-delay="150">
                    Tiga spesialisasi utama yang menjadi keunggulan komparatif kami dalam melayani berbagai sektor industri di Indonesia.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Unit 1: Laboratory -->
                <div class="reveal-on-scroll rounded-3xl bg-gradient-to-b from-[#061506] to-[#030803] border border-[#93F514]/30 hover:border-[#93F514] p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-black/60 flex flex-col justify-between">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] mb-6">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-[#EEEEEE] mb-2">Laboratory Solutions</h3>
                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed mb-6">
                            Penyediaan instrumen laboratorium umum dan analitika presisi tinggi untuk riset universitas, quality control (QC) industri, dan pengujian ilmiah.
                        </p>
                        <ul class="space-y-2.5 text-xs text-gray-300">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Alat Analitika & Spektrofotometer</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Glassware, Reagen & Chemical Supplies</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Jasa Kalibrasi & Pemeliharaan Rutin</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8 pt-4 border-t border-[#93F514]/15 text-xs font-semibold text-[#93F514]">
                        Sektor Riset & Pengujian Mutu
                    </div>
                </div>

                <!-- Unit 2: HSE -->
                <div class="reveal-on-scroll rounded-3xl bg-gradient-to-b from-[#061506] to-[#030803] border border-[#93F514]/30 hover:border-[#93F514] p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-black/60 flex flex-col justify-between" data-delay="100">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] mb-6">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-[#EEEEEE] mb-2">Health & Safety (HSE)</h3>
                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed mb-6">
                            Perlindungan total bagi tenaga kerja dengan standar K3 ketat untuk industri pertambangan, manufaktur, konstruksi, dan oil & gas.
                        </p>
                        <ul class="space-y-2.5 text-xs text-gray-300">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Alat Pelindung Diri (APD) Bersertifikat</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Detektor Gas Portabel & Tetap</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Perlengkapan Darurat & Safety Shower</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8 pt-4 border-t border-[#93F514]/15 text-xs font-semibold text-[#93F514]">
                        Standar Keselamatan Kerja K3
                    </div>
                </div>

                <!-- Unit 3: Environmental -->
                <div class="reveal-on-scroll rounded-3xl bg-gradient-to-b from-[#061506] to-[#030803] border border-[#93F514]/30 hover:border-[#93F514] p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-black/60 flex flex-col justify-between" data-delay="200">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] mb-6">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-[#EEEEEE] mb-2">Environmental Solutions</h3>
                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed mb-6">
                            Solusi pengawasan kualitas lingkungan untuk kepatuhan regulasi baku mutu lingkungan hidup, emisi, dan konservasi sumber daya alam.
                        </p>
                        <ul class="space-y-2.5 text-xs text-gray-300">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Alat Uji & Sampling Kualitas Air Limbah</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Stasiun Pemantau Kualitas Udara Ambien</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Instrumen Meteorologi & Pengukuran Tanah</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8 pt-4 border-t border-[#93F514]/15 text-xs font-semibold text-[#93F514]">
                        Kepatuhan Regulasi & Lingkungan
                    </div>
                </div>
            </div>
        </section>


        <!-- ==================== MEET THE GROUP (EKOSISTEM GRUP PERUSAHAAN) ==================== -->
        @include('frontend.components.meet-the-group', ['groupCompanies' => $groupCompanies])


        <!-- ==================== KONTAK & ALAMAT SECTION ==================== -->
        <section class="relative py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-t border-[#93F514]/20">
            <div class="text-center max-w-3xl mx-auto mb-16">
                {{-- <div class="reveal-on-scroll inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-bold uppercase tracking-wider mb-4">
                    Hubungi & Kunjungi Kami
                </div> --}}
                <h2 class="reveal-on-scroll text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#EEEEEE]" data-delay="100">
                    Informasi <span class="text-[#93F514]">Kontak & Alamat</span>
                </h2>
                <p class="reveal-on-scroll mt-3 text-sm sm:text-base text-gray-300" data-delay="150">
                    Kami siap melayani kebutuhan konsultasi, penawaran produk, dan kerjasama bisnis Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Alamat Card -->
                <div class="reveal-on-scroll rounded-3xl bg-gradient-to-b from-[#061506] to-[#030803] border border-[#93F514]/30 p-6 flex flex-col justify-between hover:border-[#93F514] transition">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/30 flex items-center justify-center text-[#93F514] mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-[#EEEEEE] mb-2">Kantor Pusat</h4>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            {{ $company->formatted_address ?: 'Jl. Klipang Ruko Amsterdam No.9D, Sendangmulyo, Kec. Tembalang, Kota Semarang, Jawa Tengah 50272' }}
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#93F514]/15">
                        <a href="https://maps.google.com/?q={{ urlencode($company->formatted_address ?: 'Jl. Klipang Ruko Amsterdam No.9D Semarang') }}" target="_blank" rel="noopener noreferrer" class="text-xs font-bold text-[#93F514] hover:underline inline-flex items-center gap-1 group">
                            <span>Buka di Google Maps</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 shrink-0 transition-transform group-hover:translate-x-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Email Card -->
                <div class="reveal-on-scroll rounded-3xl bg-gradient-to-b from-[#061506] to-[#030803] border border-[#93F514]/30 p-6 flex flex-col justify-between hover:border-[#93F514] transition" data-delay="100">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/30 flex items-center justify-center text-[#93F514] mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-[#EEEEEE] mb-2">Email Resmi</h4>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Kirimkan pertanyaan, inquiry penawaran harga, atau kerjasama via email resmi kami.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#93F514]/15">
                        <a href="mailto:{{ $company->email ?? 'info@mikacares.co.id' }}" class="text-xs font-bold text-[#93F514] hover:underline inline-flex items-center gap-1 group max-w-full">
                            <span class="truncate">{{ $company->email ?? 'info@mikacares.co.id' }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 shrink-0 transition-transform group-hover:translate-x-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- WhatsApp Card -->
                <div class="reveal-on-scroll rounded-3xl bg-gradient-to-b from-[#061506] to-[#030803] border border-[#93F514]/30 p-6 flex flex-col justify-between hover:border-[#93F514] transition" data-delay="150">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/30 flex items-center justify-center text-[#93F514] mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-[#EEEEEE] mb-2">WhatsApp Resmi</h4>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Respon cepat untuk konsultasi pengadaan barang dan layanan teknis.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#93F514]/15">
                        <a href="{{ $company->whatsapp_url ?? 'https://wa.me/6281807701210' }}" target="_blank" rel="noopener noreferrer" class="text-xs font-bold text-[#93F514] hover:underline inline-flex items-center gap-1 group">
                            <span>Chat WhatsApp</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 shrink-0 transition-transform group-hover:translate-x-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Website Card -->
                <div class="reveal-on-scroll rounded-3xl bg-gradient-to-b from-[#061506] to-[#030803] border border-[#93F514]/30 p-6 flex flex-col justify-between hover:border-[#93F514] transition" data-delay="200">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/30 flex items-center justify-center text-[#93F514] mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-[#EEEEEE] mb-2">Website Resmi</h4>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Jelajahi portofolio produk, sertifikasi, dan katalog lengkap perusahaan kami.
                        </p>
                    </div>
                    <div class="mt-6 pt-3 border-t border-[#93F514]/15">
                        <a href="{{ $company->website ?? 'https://mikacares.co.id/' }}" target="_blank" rel="noopener noreferrer" class="text-xs font-bold text-[#93F514] hover:underline inline-flex items-center gap-1 group">
                            <span class="truncate">{{ preg_replace('#^https?://#', '', rtrim($company->website ?? 'mikacares.co.id', '/')) }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 shrink-0 transition-transform group-hover:translate-x-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>


        <!-- ==================== RECRUITMENT CTA BANNER ==================== -->
        <section class="relative py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="reveal-on-scroll relative rounded-3xl bg-gradient-to-r from-[#041a04] via-[#062906] to-[#031203] border border-[#93F514]/40 p-8 sm:p-14 overflow-hidden shadow-2xl shadow-black/80 text-center">

                <div class="relative z-10 max-w-2xl mx-auto">
                    {{-- <span class="inline-block px-3 py-1 rounded-full bg-[#93F514]/10 border border-[#93F514]/30 text-[#93F514] text-xs font-bold uppercase tracking-wider mb-4">
                        Peluang Karir Terbuka
                    </span> --}}
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-[#EEEEEE] leading-tight">
                        Ingin Menjadi Bagian dari <span class="text-[#93F514]">Keluarga Besar MIKA?</span>
                    </h2>
                    <p class="mt-4 text-xs sm:text-base text-gray-300 leading-relaxed">
                        Kami senantiasa mencari talenta berintegritas tinggi, profesional, dan bersemangat untuk tumbuh bersama. Temukan posisi yang selaras dengan aspirasi Anda.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('jobs.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-full bg-[#93F514] hover:bg-[#82dc0a] text-black font-extrabold text-sm shadow-md shadow-black/40 hover:scale-105 transition-all duration-200">
                            Lihat Lowongan Kerja Aktif
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-full bg-black/50 hover:bg-black/70 text-[#EEEEEE] hover:text-[#93F514] border border-[#93F514]/40 font-bold text-sm transition-colors duration-200">
                                Buat Akun Pelamar
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection
