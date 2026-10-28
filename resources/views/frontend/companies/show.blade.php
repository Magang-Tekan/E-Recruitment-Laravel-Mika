@extends('frontend.layouts.app')

@section('title', $company->name . ($company->tagline ? ' - ' . $company->tagline : '') . ' - Mika Career')

@section('meta')
    @php
        $metaDesc = Str::limit(strip_tags($company->about ?? ($company->tagline ?? 'Profil dan informasi entitas ' . $company->name . ' pada ekosistem MIKA Grup.')), 160);
        $metaUrl = url()->current();
        $metaImage = $company->logo_url 
            ? (Str::startsWith($company->logo_url, ['http://', 'https://']) ? $company->logo_url : url($company->logo_url)) 
            : asset('images/mikaaaa.png');
    @endphp
    <meta name="description" content="{{ $metaDesc }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Mika Career">
    <meta property="og:title" content="{{ $company->name }} - Profil Perusahaan">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:url" content="{{ $metaUrl }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $company->name }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
@endsection

@section('content')
@php
    $logoUrl = $company->logo_url;
    $initials = $company->initial ?? strtoupper(substr($company->name, 0, 2));
    
    // Website format helper
    $companyWebUrl = null;
    $displayWebsite = null;
    if (!empty($company->website)) {
        $companyWebUrl = Str::startsWith($company->website, ['http://', 'https://'])
            ? $company->website
            : 'https://' . $company->website;
        $displayWebsite = preg_replace('/^https?:\/\/(www\.)?/', '', rtrim($company->website, '/'));
    }

    $hasVisionOrMissions = !empty($company->vision) || (!empty($company->missions) && is_array($company->missions) && count($company->missions) > 0);
    $hasCoreValues = !empty($company->core_values) && is_array($company->core_values) && count($company->core_values) > 0;
    $hasAddress = !empty($company->address) || !empty($company->city) || !empty($company->province);
    $companySocial = is_array($company->social_media) ? array_filter($company->social_media) : [];
    $hasSocial = !empty($companySocial);
    $hasContact = !empty($company->phone) || !empty($company->email) || !empty($companyWebUrl) || $hasSocial;
@endphp

<div class="relative py-10 sm:py-14 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

    <!-- Ambient Glow Effects -->
    <div class="absolute top-12 left-1/3 -translate-x-1/2 w-96 sm:w-[540px] h-72 bg-[#93F514]/10 rounded-full blur-[140px] pointer-events-none -z-10"></div>
    <div class="absolute top-1/2 right-10 w-80 h-80 bg-[#5FE6B6]/10 rounded-full blur-[130px] pointer-events-none -z-10"></div>

    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-400 mb-8 overflow-x-auto whitespace-nowrap pb-1">
        <a href="{{ route('home') }}" class="hover:text-[#93F514] transition flex items-center gap-1.5">
            <span>{{ __('Beranda') }}</span>
        </a>
        <span class="text-gray-600">/</span>
        <a href="{{ route('about') }}#meet-the-group" class="hover:text-[#93F514] transition">
            {{ __('Grup Perusahaan') }}
        </a>
        <span class="text-gray-600">/</span>
        <span class="text-[#93F514] font-semibold truncate max-w-xs sm:max-w-md">{{ $company->name }}</span>
    </nav>

    <!-- ====================================================
         HERO / HEADER ENTITAS PERUSAHAAN
    ==================================================== -->
    <div class="reveal-on-scroll relative rounded-3xl bg-gradient-to-b from-[#061806] via-[#041004] to-[#020702] border border-[#93F514]/30 p-6 sm:p-10 mb-10 shadow-2xl shadow-[#93F514]/10 overflow-hidden company-hero-card"
         data-delay="50">
        
        <!-- Corner Ambient Glow -->
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-[#93F514]/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 sm:gap-8">
            <!-- Left: Logo & Core Info -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 sm:gap-6">
                <!-- Logo Box -->
                @if($logoUrl)
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl sm:rounded-3xl bg-white p-2.5 sm:p-3 flex items-center justify-center border border-[#93F514]/40 shadow-xl shadow-[#93F514]/20 shrink-0">
                        <img src="{{ $logoUrl }}" alt="{{ $company->name }}" class="w-full h-full object-contain">
                    </div>
                @else
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#93F514]/20 via-[#0a230a] to-[#040e04] border border-[#93F514]/50 flex items-center justify-center text-[#93F514] font-black text-2xl sm:text-3xl tracking-wider shrink-0 shadow-xl shadow-[#93F514]/20 company-initial-avatar">
                        {{ $initials }}
                    </div>
                @endif

                <!-- Titles & Badges -->
                <div class="space-y-2.5 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-[11px] sm:text-xs font-bold uppercase tracking-wider shadow-xs company-ecosystem-badge">
                            {{ __('Grup Perusahaan MIKA') }}
                        </span>

                        @if($company->tagline)
                            <span class="inline-block px-3 py-1 rounded-full bg-white/5 border border-white/10 text-gray-300 text-xs font-medium truncate max-w-[280px] company-tagline-chip">
                                {{ $company->tagline }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#EEEEEE] tracking-tight leading-tight company-name-heading">
                        {{ $company->name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-y-2 gap-x-4 text-xs sm:text-sm text-gray-400">
                        @if($company->city || $company->province)
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ implode(', ', array_filter([$company->city, $company->province])) }}</span>
                            </div>
                        @endif

                        @if($activeJobs->count() > 0)
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $activeJobs->count() }} {{ __('Posisi Lowongan Aktif') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-[#93F514]/15">
                @if($activeJobs->count() > 0)
                    <a href="#lowongan-terbuka"
                       class="flex-1 md:flex-initial inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-[#93F514] hover:bg-[#82dc0a] text-black font-extrabold text-xs sm:text-sm shadow-lg shadow-[#93F514]/25 transition-all duration-200 active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{ __('Lihat Lowongan') }} ({{ $activeJobs->count() }})</span>
                    </a>
                @endif

                @if($companyWebUrl)
                    <a href="{{ $companyWebUrl }}" target="_blank" rel="noopener noreferrer"
                       class="flex-1 md:flex-initial inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white/5 hover:bg-white/10 border border-[#93F514]/40 hover:border-[#93F514] text-[#EEEEEE] hover:text-[#93F514] font-bold text-xs sm:text-sm transition-all duration-200 group active:scale-[0.98]">
                        <svg class="w-4 h-4 text-[#93F514] transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <span>{{ __('Website Resmi') }}</span>
                        <svg class="w-3.5 h-3.5 opacity-60 group-hover:opacity-100 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @endif

                @if($hasSocial)
                    <div class="flex items-center gap-1.5 shrink-0">
                        @if(!empty($companySocial['instagram']))
                            <a href="{{ $companySocial['instagram'] }}" target="_blank" rel="noopener noreferrer" title="Instagram" class="w-10 h-10 rounded-2xl bg-white/5 hover:bg-white/10 border border-[#93F514]/40 hover:border-[#93F514] text-gray-300 hover:text-[#93F514] flex items-center justify-center transition active:scale-95">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        @endif
                        @if(!empty($companySocial['linkedin']))
                            <a href="{{ $companySocial['linkedin'] }}" target="_blank" rel="noopener noreferrer" title="LinkedIn" class="w-10 h-10 rounded-2xl bg-white/5 hover:bg-white/10 border border-[#93F514]/40 hover:border-[#93F514] text-gray-300 hover:text-[#93F514] flex items-center justify-center transition active:scale-95">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                        @endif
                        @if(!empty($companySocial['facebook']))
                            <a href="{{ $companySocial['facebook'] }}" target="_blank" rel="noopener noreferrer" title="Facebook" class="w-10 h-10 rounded-2xl bg-white/5 hover:bg-white/10 border border-[#93F514]/40 hover:border-[#93F514] text-gray-300 hover:text-[#93F514] flex items-center justify-center transition active:scale-95">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                            </a>
                        @endif
                        @if(!empty($companySocial['youtube']))
                            <a href="{{ $companySocial['youtube'] }}" target="_blank" rel="noopener noreferrer" title="YouTube" class="w-10 h-10 rounded-2xl bg-white/5 hover:bg-white/10 border border-[#93F514]/40 hover:border-[#93F514] text-gray-300 hover:text-[#93F514] flex items-center justify-center transition active:scale-95">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                        @endif
                        @if(!empty($companySocial['tiktok']))
                            <a href="{{ $companySocial['tiktok'] }}" target="_blank" rel="noopener noreferrer" title="TikTok" class="w-10 h-10 rounded-2xl bg-white/5 hover:bg-white/10 border border-[#93F514]/40 hover:border-[#93F514] text-gray-300 hover:text-[#93F514] flex items-center justify-center transition active:scale-95">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-1.01-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                            </a>
                        @endif
                        @if(!empty($companySocial['x']))
                            <a href="{{ $companySocial['x'] }}" target="_blank" rel="noopener noreferrer" title="X (Twitter)" class="w-10 h-10 rounded-2xl bg-white/5 hover:bg-white/10 border border-[#93F514]/40 hover:border-[#93F514] text-gray-300 hover:text-[#93F514] flex items-center justify-center transition active:scale-95">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ====================================================
         TWO COLUMN LAYOUT: MAIN CONTENT + SIDEBAR
    ==================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- ==================== LEFT / MAIN COLUMN (8 cols) ==================== -->
        <div class="lg:col-span-8 space-y-8">

            <!-- 1. TENTANG PERUSAHAAN (Hanya tampil jika field 'about' di DB ada) -->
            @if(!empty($company->about))
                <div class="reveal-on-scroll rounded-3xl bg-[#050e05] border border-[#93F514]/25 p-6 sm:p-7 shadow-xl company-detail-card"
                     data-delay="100">
                    <div class="flex items-center gap-3 pb-3 border-b border-[#93F514]/15">
                        <div class="w-9 h-9 rounded-xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-[#EEEEEE]">
                                {{ __('Tentang Perusahaan') }}
                            </h2>
                            <p class="text-xs text-gray-400">{{ __('Profil dan fokus operasional bisnis') }}</p>
                        </div>
                    </div>

                    <div class="text-sm sm:text-base text-gray-300 leading-relaxed text-justify whitespace-pre-line mt-0.5 company-about-text">
                        {{ $company->localized_about ?? __(trim($company->about)) }}
                    </div>
                </div>
            @endif

            <!-- 2. VISI & MISI (Hanya tampil jika 'vision' atau 'missions' di DB terisi) -->
            @if($hasVisionOrMissions)
                <div class="reveal-on-scroll rounded-3xl bg-[#050e05] border border-[#93F514]/25 p-6 sm:p-8 space-y-6 shadow-xl company-detail-card"
                     data-delay="150">
                    <div class="flex items-center gap-3 pb-3 border-b border-[#93F514]/15">
                        <div class="w-9 h-9 rounded-xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-[#EEEEEE]">
                                {{ __('Visi & Misi Perusahaan') }}
                            </h2>
                            <p class="text-xs text-gray-400">{{ __('Arah strategis dan komitmen masa depan') }}</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Visi Card -->
                        @if(!empty($company->vision))
                            <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-[#061806] to-[#030903] border border-[#93F514]/30 shadow-md company-vision-box">
                                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#93F514] mb-2.5">
                                    {{ __('Visi Utama') }}
                                </div>
                                <p class="text-sm sm:text-base text-gray-200 font-medium leading-relaxed italic">
                                    "{{ __(trim($company->vision)) }}"
                                </p>
                            </div>
                        @endif

                        <!-- Misi List -->
                        @if(!empty($company->missions) && is_array($company->missions) && count($company->missions) > 0)
                            <div class="space-y-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">
                                    {{ __('Misi Perusahaan') }}
                                </span>
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach($company->missions as $idx => $misi)
                                        @if(!empty(trim($misi)))
                                            <div class="flex items-start gap-3.5 p-4 rounded-2xl bg-[#030803] border border-[#93F514]/15 hover:border-[#93F514]/40 transition-colors company-mission-item">
                                                <div class="w-7 h-7 rounded-xl bg-[#93F514]/15 text-[#93F514] border border-[#93F514]/30 font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                                    {{ $idx + 1 }}
                                                </div>
                                                <p class="text-xs sm:text-sm text-gray-300 leading-relaxed flex-1">
                                                    {{ __(trim($misi)) }}
                                                </p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 3. NILAI-NILAI PERUSAHAAN / CORE VALUES (Hanya tampil jika ada Visi & Misi dan 'core_values' di DB terisi) -->
            @if($hasVisionOrMissions && $hasCoreValues)
                <div class="reveal-on-scroll rounded-3xl bg-[#050e05] border border-[#93F514]/25 p-6 sm:p-8 space-y-6 shadow-xl company-detail-card"
                     data-delay="150">
                    <div class="flex items-center gap-3 pb-3 border-b border-[#93F514]/15">
                        <div class="w-9 h-9 rounded-xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-[#EEEEEE]">
                                {{ __('Nilai Budaya Perusahaan') }}
                            </h2>
                            <p class="text-xs text-gray-400">{{ __('Prinsip kerja yang memandu integritas dan profesionalisme tim') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($company->core_values as $val)
                            @php
                                $valCode = $val['code'] ?? null;
                                $valTitle = trim($val['title'] ?? '');
                                $valSub = trim($val['subtitle'] ?? '');
                                $valDesc = $val['description'] ?? null;

                                $isEn = app()->getLocale() === 'en';
                                if ($isEn) {
                                    $displayTitle = !empty($valSub) ? __($valSub) : __($valTitle);
                                    $displaySub = $valTitle;
                                } else {
                                    $displayTitle = $valTitle;
                                    $displaySub = $valSub;
                                }
                            @endphp
                            @if($valTitle || $valDesc)
                                <div class="p-5 rounded-2xl bg-[#030803] border border-[#93F514]/20 hover:border-[#93F514]/50 transition-all duration-200 shadow-sm flex flex-col justify-between company-value-box">
                                    <div>
                                        <div class="flex items-center gap-2.5 mb-2.5">
                                            @if($valCode)
                                                <span class="w-8 h-8 rounded-xl bg-[#93F514]/20 text-[#93F514] font-black text-sm flex items-center justify-center border border-[#93F514]/40 shrink-0">
                                                    {{ $valCode }}
                                                </span>
                                            @endif
                                            <div>
                                                <h3 class="text-sm sm:text-base font-extrabold text-[#EEEEEE]">
                                                    {{ $displayTitle }}
                                                </h3>
                                                @if(!empty($displaySub))
                                                    <span class="text-[11px] text-[#93F514]/80 font-medium block">
                                                        {{ $displaySub }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($valDesc)
                                            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed mt-2">
                                                {{ __(trim($valDesc)) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 4. LOWONGAN TERBUKA DI PERUSAHAAN INI -->
            <div id="lowongan-terbuka" class="reveal-on-scroll rounded-3xl bg-[#050e05] border border-[#93F514]/25 p-6 sm:p-8 space-y-6 shadow-xl company-detail-card"
                 data-delay="200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#93F514]/15">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-[#EEEEEE]">
                                {{ __('Lowongan Kerja di') }} {{ $company->name }}
                            </h2>
                            <p class="text-xs text-gray-400">{{ __('Peluang karir aktif yang siap menerima lamaran Anda') }}</p>
                        </div>
                    </div>

                    @if($activeJobs->count() > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#93F514]/15 border border-[#93F514]/30 text-[#93F514] text-xs font-bold w-fit">
                            {{ $activeJobs->count() }} {{ __('Lowongan Dibuka') }}
                        </span>
                    @endif
                </div>

                @if($activeJobs->count() > 0)
                    <!-- Job Cards Grid -->
                    <div class="space-y-4">
                        @foreach($activeJobs as $jobItem)
                            @php
                                $deadlineFormat = $jobItem->deadline ? \Carbon\Carbon::parse($jobItem->deadline)->translatedFormat('d M Y') : __('Hingga Terpenuhi');
                            @endphp
                            <div class="p-5 sm:p-6 rounded-2xl bg-[#030803] border border-[#93F514]/20 hover:border-[#93F514] transition-all duration-300 hover:shadow-lg hover:shadow-[#93F514]/15 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group company-job-item">
                                <div class="space-y-2 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-md bg-[#93F514]/15 text-[#93F514] text-[11px] font-bold border border-[#93F514]/30">
                                            {{ __($jobItem->employment_type) }}
                                        </span>
                                        @if($jobItem->department)
                                            <span class="px-2.5 py-0.5 rounded-md bg-white/5 text-gray-300 text-[11px] font-medium border border-white/10">
                                                {{ __($jobItem->department->name) }}
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-base sm:text-lg font-bold text-[#EEEEEE] group-hover:text-[#93F514] transition-colors truncate">
                                        <a href="{{ route('jobs.show', $jobItem->id) }}">
                                            {{ __($jobItem->title) }}
                                        </a>
                                    </h3>

                                    <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-xs text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ __($jobItem->location ?? 'Indonesia') }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ __('Batas') }}: {{ $deadlineFormat }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2.5 shrink-0">
                                    <a href="{{ route('jobs.show', $jobItem->id) }}"
                                       class="job-detail-btn w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#93F514]/10 hover:bg-[#93F514] text-[#93F514] hover:text-black border border-[#93F514]/40 hover:border-[#93F514] font-bold text-xs transition-all duration-200">
                                        <span>{{ __('Detail & Lamar') }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Graceful Empty State Lowongan -->
                    <div class="p-8 sm:p-10 rounded-2xl bg-[#030803] border border-dashed border-[#93F514]/20 text-center space-y-3 company-empty-jobs">
                        <div class="w-12 h-12 rounded-2xl bg-[#93F514]/10 border border-[#93F514]/30 text-[#93F514] flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-[#EEEEEE]">
                            {{ __('Belum Ada Posisi Dibuka Saat Ini') }}
                        </h4>
                        <p class="text-xs sm:text-sm text-gray-400 max-w-md mx-auto leading-relaxed">
                            {{ __('Saat ini belum ada lowongan kerja aktif khusus di') }} <strong class="text-gray-200">{{ $company->name }}</strong>. {{ __('Anda tetap dapat menjelajahi lowongan yang tersedia di grup perusahaan lainnya.') }}
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('jobs.index') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#93F514]/10 hover:bg-[#93F514] text-[#93F514] hover:text-black border border-[#93F514]/30 font-bold text-xs transition">
                                <span>{{ __('Lihat Semua Lowongan Karir') }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <!-- ==================== RIGHT SIDEBAR (4 cols) ==================== -->
        <div class="lg:col-span-4 space-y-6">

            <!-- 1. INFORMASI KONTAK & KANTOR (Hanya tampil jika field alamat/kontak ada) -->
            @if($hasAddress || $hasContact)
                <div class="reveal-on-scroll rounded-3xl bg-[#050e05] border border-[#93F514]/25 p-6 space-y-5 shadow-xl company-detail-card"
                     data-delay="100">
                    <h3 class="text-base font-bold text-[#EEEEEE] pb-3 border-b border-[#93F514]/15 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ __('Informasi Kontak & Kantor') }}</span>
                    </h3>

                    <div class="space-y-4 text-xs sm:text-sm">
                        <!-- Alamat -->
                        @if(!empty($company->address))
                            <div class="flex items-start gap-3 text-gray-300">
                                <div class="w-7 h-7 rounded-lg bg-[#93F514]/15 text-[#93F514] flex items-center justify-center shrink-0 mt-0.5 border border-[#93F514]/30">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 leading-relaxed">
                                    <span class="text-gray-400 block text-[11px]">{{ __('Alamat Kantor') }}</span>
                                    <span>{{ $company->address }}</span>
                                    @if($company->city || $company->province || $company->postal_code)
                                        <span class="block text-gray-400 mt-0.5">
                                            {{ implode(', ', array_filter([$company->city, $company->province, $company->postal_code ? __('Kode Pos') . ' ' . $company->postal_code : null])) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @elseif($company->city || $company->province)
                            <div class="flex items-start gap-3 text-gray-300">
                                <div class="w-7 h-7 rounded-lg bg-[#93F514]/15 text-[#93F514] flex items-center justify-center shrink-0 mt-0.5 border border-[#93F514]/30">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <span class="text-gray-400 block text-[11px]">{{ __('Lokasi') }}</span>
                                    <span>{{ implode(', ', array_filter([$company->city, $company->province])) }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Telepon / WhatsApp -->
                        @if(!empty($company->phone))
                            <div class="flex items-center gap-3 text-gray-300">
                                <div class="w-7 h-7 rounded-lg bg-[#93F514]/15 text-[#93F514] flex items-center justify-center shrink-0 border border-[#93F514]/30">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <span class="text-gray-400 block text-[11px]">{{ __('Kontak / WhatsApp') }}</span>
                                    <a href="{{ $company->whatsapp_url ?? 'tel:' . $company->phone }}" target="_blank" rel="noopener noreferrer"
                                       class="font-semibold text-[#EEEEEE] hover:text-[#93F514] transition">
                                        {{ $company->phone }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Email -->
                        @if(!empty($company->email))
                            <div class="flex items-center gap-3 text-gray-300">
                                <div class="w-7 h-7 rounded-lg bg-[#93F514]/15 text-[#93F514] flex items-center justify-center shrink-0 border border-[#93F514]/30">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 truncate">
                                    <span class="text-gray-400 block text-[11px]">{{ __('Email Resmi') }}</span>
                                    <a href="mailto:{{ $company->email }}" class="font-semibold text-[#EEEEEE] hover:text-[#93F514] transition truncate block">
                                        {{ $company->email }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Website Resmi -->
                        @if($companyWebUrl)
                            <div class="flex items-center gap-3 text-gray-300">
                                <div class="w-7 h-7 rounded-lg bg-[#93F514]/15 text-[#93F514] flex items-center justify-center shrink-0 border border-[#93F514]/30">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                </div>
                                <div class="flex-1 truncate">
                                    <span class="text-gray-400 block text-[11px]">{{ __('Situs Web') }}</span>
                                    <a href="{{ $companyWebUrl }}" target="_blank" rel="noopener noreferrer"
                                       class="font-semibold text-[#93F514] hover:underline truncate block">
                                        {{ $displayWebsite }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Media Sosial Resmi Perusahaan -->
                        @if(!empty($companySocial))
                            <div class="pt-3 mt-1 border-t border-[#93F514]/15">
                                <span class="text-gray-400 block text-[11px] mb-2 font-medium">{{ __('Media Sosial Resmi') }}</span>
                                <div class="flex flex-wrap items-center gap-2">
                                    @if(!empty($companySocial['instagram']))
                                        <a href="{{ $companySocial['instagram'] }}" target="_blank" rel="noopener noreferrer" title="Instagram" class="w-8 h-8 rounded-lg bg-black/40 border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                        </a>
                                    @endif
                                    @if(!empty($companySocial['linkedin']))
                                        <a href="{{ $companySocial['linkedin'] }}" target="_blank" rel="noopener noreferrer" title="LinkedIn" class="w-8 h-8 rounded-lg bg-black/40 border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                        </a>
                                    @endif
                                    @if(!empty($companySocial['facebook']))
                                        <a href="{{ $companySocial['facebook'] }}" target="_blank" rel="noopener noreferrer" title="Facebook" class="w-8 h-8 rounded-lg bg-black/40 border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                                        </a>
                                    @endif
                                    @if(!empty($companySocial['youtube']))
                                        <a href="{{ $companySocial['youtube'] }}" target="_blank" rel="noopener noreferrer" title="YouTube" class="w-8 h-8 rounded-lg bg-black/40 border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                        </a>
                                    @endif
                                    @if(!empty($companySocial['tiktok']))
                                        <a href="{{ $companySocial['tiktok'] }}" target="_blank" rel="noopener noreferrer" title="TikTok" class="w-8 h-8 rounded-lg bg-black/40 border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-1.01-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                        </a>
                                    @endif
                                    @if(!empty($companySocial['x']))
                                        <a href="{{ $companySocial['x'] }}" target="_blank" rel="noopener noreferrer" title="X (Twitter)" class="w-8 h-8 rounded-lg bg-black/40 border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 2. SISTER COMPANIES / GRUP perusahaan LAINNYA -->
            @if($otherCompanies->count() > 0)
                <div class="reveal-on-scroll rounded-3xl bg-[#050e05] border border-[#93F514]/25 p-6 space-y-4 shadow-xl company-detail-card"
                     data-delay="150">
                    <h3 class="text-base font-bold text-[#EEEEEE] pb-3 border-b border-[#93F514]/15 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>{{ __('Grup Perusahaan Lainnya') }}</span>
                    </h3>

                    <div class="space-y-3">
                        @foreach($otherCompanies as $oComp)
                            @php
                                $oLogo = $oComp->logo_url;
                                $oInit = $oComp->initial ?? strtoupper(substr($oComp->name, 0, 2));
                            @endphp
                            <a href="{{ route('companies.show', $oComp->id) }}"
                               class="p-3.5 rounded-2xl bg-[#030803] border border-[#93F514]/15 hover:border-[#93F514]/50 flex items-center gap-3 transition-all duration-200 group company-other-item">
                                @if($oLogo)
                                    <div class="w-10 h-10 rounded-xl bg-white p-1.5 flex items-center justify-center shrink-0 border border-gray-200">
                                        <img src="{{ $oLogo }}" alt="{{ $oComp->name }}" class="w-full h-full object-contain">
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-[#93F514]/15 text-[#93F514] font-bold text-xs flex items-center justify-center shrink-0 border border-[#93F514]/30 group-hover:bg-[#93F514] group-hover:text-black transition">
                                        {{ $oInit }}
                                    </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs sm:text-sm font-bold text-[#EEEEEE] group-hover:text-[#93F514] transition truncate">
                                        {{ $oComp->name }}
                                    </h4>
                                    <span class="text-[11px] text-gray-400 block truncate">
                                        {{ $oComp->tagline ?: ($oComp->city ?? __('Ekosistem Grup')) }}
                                    </span>
                                </div>

                                <svg class="w-4 h-4 text-gray-500 group-hover:text-[#93F514] group-hover:translate-x-0.5 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endforeach
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('about') }}#meet-the-group"
                           class="w-full py-2.5 px-4 rounded-xl bg-white/5 hover:bg-[#93F514]/10 border border-white/10 hover:border-[#93F514]/30 text-xs font-semibold text-gray-300 hover:text-[#93F514] transition flex items-center justify-center gap-1.5">
                            <span>{{ __('Lihat Semua Anggota Grup') }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
