@extends('frontend.layouts.app')

@section('title', $job->title . ' - ' . ($job->company?->name ?? 'Perusahaan') . ' | Mika Career')

@section('meta')
    @php
        $metaPlainDesc = Str::limit(strip_tags($job->description ?? 'Lowongan kerja ' . $job->title . ' di ' . ($job->company?->name ?? 'PT Mitra Karya Analitika')), 160);
        $metaShareUrl = url()->current();
        $metaShareImage = $job->company?->logo_url 
            ? (Str::startsWith($job->company->logo_url, ['http://', 'https://']) ? $job->company->logo_url : url($job->company->logo_url)) 
            : asset('images/mikaaaa.png');
    @endphp
    <meta name="description" content="{{ $metaPlainDesc }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Mika Career">
    <meta property="og:title" content="{{ $job->title }} - {{ $job->company?->name ?? 'PT Mitra Karya Analitika' }}">
    <meta property="og:description" content="{{ $metaPlainDesc }}">
    <meta property="og:image" content="{{ $metaShareImage }}">
    <meta property="og:image:secure_url" content="{{ $metaShareImage }}">
    <meta property="og:url" content="{{ $metaShareUrl }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $job->title }} - {{ $job->company?->name ?? 'PT Mitra Karya Analitika' }}">
    <meta name="twitter:description" content="{{ $metaPlainDesc }}">
    <meta name="twitter:image" content="{{ $metaShareImage }}">
@endsection

@section('content')
@php
    $companyName = $job->company?->name ?? 'PT Mitra Karya Analitika';
    $currentShareUrl = url()->current();
    $deadlineText = $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('d F Y') : 'Hingga kuota terpenuhi';
    
    // Pesan template WhatsApp resmi dan profesional tanpa emoji
    $waMessage = "*INFORMASI LOWONGAN PEKERJAAN*\n\n" .
                 "*Perusahaan*: " . $companyName . "\n" .
                 "*Posisi*: " . $job->title . "\n" .
                 ($job->department ? "*Departemen*: " . $job->department->name . "\n" : "") .
                 "*Lokasi*: " . ($job->location ?? 'Indonesia') . "\n" .
                 "*Tipe Pekerjaan*: " . ($job->employment_type ?? 'Full Time') . "\n" .
                 "*Batas Akhir Pendaftaran*: " . $deadlineText . "\n\n" .
                 "Informasi kualifikasi, deskripsi pekerjaan, dan pendaftaran dapat diakses melalui tautan resmi berikut:\n" .
                 $currentShareUrl;
                 
    $waShareUrl = "https://api.whatsapp.com/send?text=" . rawurlencode($waMessage);
    $linkedInShareUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" . rawurlencode($currentShareUrl);

    $isAdminOrRecruiter = auth()->check() && (
        auth()->user()->role_id == 1 ||
        auth()->user()->role_id == 2 ||
        (bool) auth()->user()->is_recruiter ||
        in_array(strtolower(auth()->user()->role?->name ?? ''), [
            'admin',
            'superadmin',
            'recruiter',
        ])
    );
@endphp

<div class="relative py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
     x-data="{
         showConfirmModal: false,
         showIncompleteModal: false,
         isSubmitting: false,
         isMandatoryComplete: {{ ($isMandatoryComplete ?? false) ? 'true' : 'false' }},
         hasApplied: {{ ($hasApplied ?? false) ? 'true' : 'false' }},
         missingSections: {{ json_encode($missingMandatorySections ?? []) }},
         shareCopied: false,
         shareUrl: '{{ $currentShareUrl }}',
         shareTitle: '{{ addslashes($job->title) }}',
         handleApplyClick() {
             if (this.hasApplied) return;
             if (!this.isMandatoryComplete) {
                 this.showIncompleteModal = true;
             } else {
                 this.showConfirmModal = true;
             }
         },
         copyShareLink() {
             const url = this.shareUrl;
             if (navigator.clipboard && window.isSecureContext) {
                 navigator.clipboard.writeText(url).then(() => {
                     this.shareCopied = true;
                     setTimeout(() => this.shareCopied = false, 2500);
                 }).catch(() => {
                     this.fallbackCopy(url);
                 });
             } else {
                 this.fallbackCopy(url);
             }
         },
         fallbackCopy(text) {
             const textArea = document.createElement('textarea');
             textArea.value = text;
             textArea.style.position = 'fixed';
             textArea.style.left = '-999999px';
             document.body.appendChild(textArea);
             textArea.focus();
             textArea.select();
             try {
                 document.execCommand('copy');
                 this.shareCopied = true;
                 setTimeout(() => this.shareCopied = false, 2500);
             } catch (e) {
                 console.error('Copy failed', e);
             }
             document.body.removeChild(textArea);
         },
         shareNative() {
             if (navigator.share) {
                 navigator.share({
                     title: this.shareTitle,
                     text: 'Lowongan Kerja ' + this.shareTitle + ' di {{ addslashes($companyName) }}',
                     url: this.shareUrl
                 }).catch(() => {});
             } else {
                 this.copyShareLink();
             }
         }
     }">

    <!-- Flash Messages (Success / Error / Info) -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-400 flex items-center justify-between shadow-xl shadow-emerald-500/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-sm block">Lamaran Berhasil Diajukan!</span>
                    <span class="text-xs text-gray-300">{{ session('success') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('profile', ['tab' => 'riwayat']) }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-500 text-black font-bold text-xs hover:bg-emerald-400 transition">
                    Lihat Riwayat
                </a>
                <button @click="show = false" class="text-emerald-400/80 hover:text-emerald-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 rounded-2xl bg-blue-500/15 border border-blue-500/40 text-blue-400 flex items-center justify-between shadow-xl shadow-blue-500/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-sm block">Informasi</span>
                    <span class="text-xs text-gray-300">{{ session('info') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('profile', ['tab' => 'riwayat']) }}" class="px-3.5 py-1.5 rounded-xl bg-blue-500 text-white font-bold text-xs hover:bg-blue-400 transition">
                    Riwayat Lamaran
                </a>
                <button @click="show = false" class="text-blue-400/80 hover:text-blue-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 rounded-2xl bg-rose-500/15 border border-rose-500/40 text-rose-400 flex items-center justify-between shadow-xl shadow-rose-500/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-sm block">Perhatian</span>
                    <span class="text-xs text-gray-300">{{ session('error') }}</span>
                </div>
            </div>
            <button @click="show = false" class="text-rose-400/80 hover:text-rose-300">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-gray-400 mb-8">
        <a href="{{ route('home') }}" class="hover:text-[#93F514] transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('jobs.index') }}" class="hover:text-[#93F514] transition">Lowongan</a>
        <span>/</span>
        <span class="text-[#93F514] font-medium truncate max-w-xs sm:max-w-md">{{ $job->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Main Job Details (8 cols) -->
        <div class="lg:col-span-8 space-y-8">

            @if ($job->is_expired || $job->status !== 'Open')
                <!-- Banner Pemberitahuan Lowongan Ditutup / Kadaluwarsa -->
                <div class="reveal-on-scroll rounded-3xl bg-gradient-to-r from-rose-950/70 via-rose-900/40 to-black border border-rose-500/40 p-5 sm:p-6 flex items-start gap-4 shadow-xl shadow-rose-950/30" data-delay="50">
                    <div class="w-11 h-11 rounded-2xl bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-rose-400 shrink-0 shadow-lg shadow-rose-500/20">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-base font-extrabold text-rose-200">Pendaftaran Lowongan Ini Telah Ditutup</h4>
                        <p class="text-xs sm:text-sm text-gray-300 mt-1 leading-relaxed">
                            @if ($job->is_expired)
                                Lowongan ini telah melewati batas akhir pendaftaran pada tanggal <strong class="text-rose-300">{{ \Carbon\Carbon::parse($job->deadline)->format('d F Y') }}</strong> dan saat ini tidak lagi menerima pengajuan lamaran baru.
                            @elseif ($job->status === 'Closed')
                                Lowongan ini telah resmi ditutup oleh perusahaan/perekrut sehingga proses penerimaan berkas baru telah berakhir.
                            @else
                                Lowongan ini saat ini tidak berstatus aktif sehingga tidak menerima pengajuan lamaran baru.
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            <!-- Job Header Card -->
            <div
                class="reveal-on-scroll rounded-3xl bg-gradient-to-b from-[#061506] to-[#040804] border {{ $job->is_expired || $job->status !== 'Open' ? 'border-rose-500/30' : 'border-[#93F514]/30' }} p-6 sm:p-8 shadow-2xl {{ $job->is_expired || $job->status !== 'Open' ? 'shadow-rose-950/20' : 'shadow-black/80' }}">

                @if ($isAdminOrRecruiter)
                    <!-- Notice Banner Khusus Admin / Recruiter -->
                    <div class="admin-preview-banner mb-6 p-3.5 sm:p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-amber-300 block text-xs sm:text-sm">Mode Pratinjau ({{ auth()->user()->role?->name ?? 'Admin' }})</span>
                                <span class="text-gray-300 text-[11px] sm:text-xs">Akun staf/pengelola aktif. Tombol lamaran pekerjaan dinonaktifkan untuk akun ini.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                            @if (Route::has('admin.job'))
                                <a href="{{ route('admin.job') }}" class="px-3.5 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 font-bold text-xs transition flex items-center gap-1.5 whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Kelola di Admin</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                    <div class="flex items-start gap-4 min-w-0 flex-1">
                        @if ($job->company?->logo_url)
                            <div
                                class="w-16 h-16 rounded-2xl bg-white border border-[#93F514]/40 p-2 flex items-center justify-center shadow-md shadow-black/30 shrink-0 overflow-hidden company-logo-box">
                                <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}"
                                    class="w-full h-full object-contain">
                            </div>
                        @else
                            <div
                                class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#93F514] to-[#5ef558] flex items-center justify-center text-black font-extrabold text-2xl shadow-md shadow-black/30 shrink-0">
                                {{ strtoupper(substr($job->company?->name ?? 'M', 0, 2)) }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514]">
                                    {{ $job->employment_type }}
                                </span>
                                @if ($job->is_expired || $job->status === 'Closed')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 border border-rose-500/40 text-rose-400">
                                        Ditutup
                                    </span>
                                @elseif ($job->days_remaining === 0)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 border border-rose-500/40 text-rose-400 animate-pulse">
                                        Berakhir Hari Ini!
                                    </span>
                                @elseif ($job->days_remaining !== null && $job->days_remaining <= 3)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 border border-amber-500/40 text-amber-400">
                                        Sisa {{ $job->days_remaining }} Hari
                                    </span>
                                @endif
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#EEEEEE] mt-2 break-normal sm:break-words leading-tight">
                                {{ $job->title }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1.5 text-sm mt-2">
                                <span class="text-[#93F514] font-semibold flex items-center gap-1.5 company-badge whitespace-nowrap sm:whitespace-normal">
                                    <svg class="w-4 h-4 text-[#93F514]/80 company-icon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span>{{ $job->company?->name ?? 'Perusahaan Mitra' }}</span>
                                </span>

                                <span class="text-gray-300 font-medium flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    <span>{{ $job->department?->name ?? 'Umum' }}</span>
                                </span>

                                @if ($job->position)
                                    <span class="inline-flex items-center text-xs px-2.5 py-0.5 rounded-full bg-[#93F514]/10 text-[#93F514] border border-[#93F514]/30 font-medium whitespace-nowrap">
                                        Posisi: {{ $job->position->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions Container: Buttons + Share Dropdown (Rapi & Sejajar) -->
                    <div class="flex items-center gap-2.5 sm:gap-3 shrink-0 flex-wrap sm:flex-nowrap">
                        @if ($job->is_expired || $job->status !== 'Open')
                            <!-- Status Tombol Ditutup -->
                            <div class="flex-1 md:flex-initial h-11 sm:h-12 px-5 rounded-2xl bg-gray-900/90 border border-gray-700 text-gray-400 font-bold text-xs sm:text-sm inline-flex items-center justify-center gap-2 cursor-not-allowed shadow-inner select-none whitespace-nowrap">
                                <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>Pendaftaran Ditutup</span>
                            </div>
                        @else
                            @auth
                                @if ($isAdminOrRecruiter)
                                    <a href="{{ auth()->user()->isRecruiter() && !auth()->user()->isAdmin() ? route('recruiter.dashboard') : route('admin.dashboard') }}"
                                        class="admin-dashboard-btn h-11 sm:h-12 px-5 rounded-2xl bg-gray-900 hover:bg-gray-800 border border-gray-700 hover:border-[#93F514]/50 text-white font-bold text-xs sm:text-sm shadow-md inline-flex items-center justify-center gap-2 transition whitespace-nowrap">
                                        <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                        <span>Dashboard</span>
                                    </a>
                                @elseif($hasApplied)
                                    <div class="flex-1 md:flex-initial h-11 sm:h-12 px-4 sm:px-5 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] font-bold text-xs sm:text-sm inline-flex items-center justify-center gap-2 shadow-sm select-none whitespace-nowrap">
                                        <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Sudah Dilamar</span>
                                    </div>
                                    <a href="{{ route('profile', ['tab' => 'riwayat']) }}" 
                                       class="h-11 sm:h-12 px-4 sm:px-5 rounded-2xl bg-[#051405] hover:bg-[#93F514] border border-[#93F514]/30 text-white hover:text-black text-xs sm:text-sm font-semibold shadow-md transition inline-flex items-center justify-center whitespace-nowrap">
                                        <span>Pantau Status</span>
                                    </a>
                                @else
                                    <button type="button" 
                                            @click="handleApplyClick()"
                                            class="flex-1 md:flex-initial h-11 sm:h-12 px-6 rounded-2xl bg-[#93F514] hover:bg-[#82dc0a] text-black font-extrabold text-sm sm:text-base shadow-md shadow-black/30 transition cursor-pointer inline-flex items-center justify-center gap-2 whitespace-nowrap active:scale-[0.98]">
                                        <svg class="w-4 h-4 text-black shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        <span>Lamar Posisi Ini</span>
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}"
                                    class="flex-1 md:flex-initial h-11 sm:h-12 px-6 rounded-2xl bg-[#93F514] hover:bg-[#82dc0a] text-black font-extrabold text-sm sm:text-base shadow-md shadow-black/30 transition inline-flex items-center justify-center gap-2 whitespace-nowrap active:scale-[0.98]">
                                    <span>Masuk & Lamar</span>
                                </a>
                            @endauth
                        @endif

                        <!-- Dropdown Bagikan (Sejajar dengan Lamar) -->
                        <div class="relative shrink-0" x-data="{ openShare: false }" @click.outside="openShare = false">
                            <button type="button" 
                                    @click="openShare = !openShare"
                                    class="h-11 sm:h-12 px-4 sm:px-5 rounded-2xl bg-white/5 hover:bg-white/10 border border-[#93F514]/30 hover:border-[#93F514] text-[#EEEEEE] hover:text-[#93F514] font-bold text-xs sm:text-sm transition inline-flex items-center justify-center gap-2 cursor-pointer shadow-sm group whitespace-nowrap active:scale-[0.98]"
                                    :class="openShare ? 'border-[#93F514] text-[#93F514] bg-white/10 ring-2 ring-[#93F514]/20' : ''"
                                    title="Bagikan Lowongan Ini">
                                <svg class="w-4 h-4 text-[#93F514] shrink-0 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                <span>Bagikan</span>
                                <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-[#93F514] transition-transform duration-200 shrink-0" :class="openShare ? 'rotate-180 text-[#93F514]' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu Popover -->
                            <div x-show="openShare" 
                                 x-cloak 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute right-0 mt-2 w-52 rounded-2xl bg-[#061506] border border-[#93F514]/30 shadow-2xl shadow-black/90 p-2 z-50 text-left backdrop-blur-xl">
                                
                                <div class="px-3 py-1.5 text-xs font-bold text-gray-200">
                                    Bagikan Lowongan
                                </div>
                                <div class="h-px bg-white/10 mx-2 my-1"></div>

                                <!-- Salin Tautan -->
                                <button type="button" 
                                        @click="copyShareLink(); setTimeout(() => openShare = false, 1200)"
                                        class="w-full px-3 py-2.5 rounded-xl hover:bg-white/10 text-xs text-gray-300 hover:text-white flex items-center justify-between transition cursor-pointer group">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-5 h-5 flex items-center justify-center shrink-0">
                                            <template x-if="!shareCopied">
                                                <svg class="w-4 h-4 text-gray-400 group-hover:text-[#93F514] transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                </svg>
                                            </template>
                                            <template x-if="shareCopied">
                                                <svg class="w-4 h-4 text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </template>
                                        </div>
                                        <span class="font-medium" x-text="shareCopied ? 'Tautan disalin!' : 'Salin tautan'" :class="shareCopied ? 'text-[#93F514] font-bold' : ''"></span>
                                    </div>
                                    <template x-if="shareCopied">
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#93F514]/20 text-[#93F514] font-bold">Tersalin</span>
                                    </template>
                                </button>

                                <!-- WhatsApp -->
                                <a href="{{ $waShareUrl }}" target="_blank" rel="noopener noreferrer"
                                   @click="openShare = false"
                                   class="w-full px-3 py-2.5 rounded-xl hover:bg-white/10 text-xs text-gray-300 hover:text-white flex items-center gap-2.5 transition group cursor-pointer">
                                    <div class="w-5 h-5 flex items-center justify-center shrink-0">
                                        <svg class="w-[16px] h-[16px] text-emerald-400 group-hover:scale-110 transition-transform" viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">WhatsApp</span>
                                </a>

                                <!-- LinkedIn -->
                                <a href="{{ $linkedInShareUrl }}" target="_blank" rel="noopener noreferrer"
                                   @click="openShare = false"
                                   class="w-full px-3 py-2.5 rounded-xl hover:bg-white/10 text-xs text-gray-300 hover:text-white flex items-center gap-2.5 transition group cursor-pointer">
                                    <div class="w-5 h-5 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-[#0a66c2] group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium">LinkedIn</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Info Highlights Grid -->
                    <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 border-t border-[#93F514]/15">
                        <div class="p-3 rounded-xl bg-[#050c05] border border-[#93F514]/20">
                            <span class="text-[11px] text-gray-400 block">Lokasi</span>
                            <span
                                class="text-xs sm:text-sm font-bold text-[#EEEEEE] mt-0.5 block">{{ $job->location ?? 'Indonesia' }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-[#050c05] border border-[#93F514]/20">
                            <span class="text-[11px] text-gray-400 block">Estimasi Gaji</span>
                            <span class="text-xs sm:text-sm font-bold text-[#93F514] mt-0.5 block">
                                @if ($job->salary_min || $job->salary_max)
                                    Rp {{ number_format($job->salary_min / 1000000, 1) }} -
                                    {{ number_format($job->salary_max / 1000000, 1) }} Juta
                                @else
                                    Kompetitif
                                @endif
                            </span>
                        </div>
                        <div class="p-3 rounded-xl bg-[#050c05] border border-[#93F514]/20">
                            <span class="text-[11px] text-gray-400 block">Kuota Diterima</span>
                            <span class="text-xs sm:text-sm font-bold text-[#EEEEEE] mt-0.5 block">{{ $job->quota }}
                                Orang</span>
                        </div>
                        <div class="p-3 rounded-xl bg-[#050c05] border {{ $job->is_expired || $job->status === 'Closed' ? 'border-rose-500/30' : ($job->days_remaining !== null && $job->days_remaining <= 3 ? 'border-amber-500/40' : 'border-[#93F514]/20') }}">
                            <span class="text-[11px] text-gray-400 block">Batas Lamaran</span>
                            <span class="text-xs sm:text-sm font-bold {{ $job->is_expired || $job->status === 'Closed' ? 'text-rose-400' : 'text-[#EEEEEE]' }} mt-0.5 block">
                                {{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('d M Y') : 'Hingga Terpenuhi' }}
                            </span>
                            @if ($job->is_expired || $job->status === 'Closed')
                                <span class="text-[10px] font-semibold text-rose-400 block mt-0.5">Sudah Ditutup</span>
                            @elseif ($job->days_remaining === 0)
                                <span class="text-[10px] font-bold text-rose-400 animate-pulse block mt-0.5">Berakhir Hari Ini!</span>
                            @elseif ($job->days_remaining !== null && $job->days_remaining <= 3)
                                <span class="text-[10px] font-semibold text-amber-400 block mt-0.5">Sisa {{ $job->days_remaining }} Hari</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Job Description Content -->
                <div class="reveal-on-scroll rounded-3xl bg-[#050e05] border border-[#93F514]/25 p-6 sm:p-8 space-y-6" data-delay="100">
                    @php
                        $rawDesc = $job->description ?? '';
                        $isHtml =
                            str_contains($rawDesc, '<p>') ||
                            str_contains($rawDesc, '<ul>') ||
                            str_contains($rawDesc, '<ol>') ||
                            str_contains($rawDesc, '<h3>') ||
                            str_contains($rawDesc, '<h2>') ||
                            str_contains($rawDesc, '<li>');
                    @endphp

                    @if ($isHtml)
                        <!-- Rich Text HTML Content (Formatted with typography utilities) -->
                        <div
                            class="prose prose-invert max-w-none text-sm text-gray-300 leading-relaxed space-y-3 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mt-1 [&_h2]:text-lg [&_h2]:font-extrabold [&_h2]:text-[#EEEEEE] [&_h3]:text-base [&_h3]:font-bold [&_h3]:text-[#EEEEEE] [&_strong]:text-[#EEEEEE] [&_p]:mb-3 job-description-prose">
                            {!! $rawDesc !!}
                        </div>
                    @else
                        @php
                            $descPart = $rawDesc;
                            $reqPart = '';
                            if (str_contains($rawDesc, '### Persyaratan:') || str_contains($rawDesc, 'Persyaratan:')) {
                                $split = preg_split('/### Persyaratan:|Persyaratan:/', $rawDesc);
                                $descPart = trim(preg_replace('/### Deskripsi Pekerjaan:\s*/i', '', $split[0] ?? ''));
                                $reqPart = trim($split[1] ?? '');
                            }
                        @endphp

                        <!-- 1. Deskripsi Pekerjaan (Legacy Plain Text) -->
                        @if ($descPart)
                            <div>
                                <h2 class="text-lg sm:text-xl font-extrabold text-[#EEEEEE] mb-3 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#93F514]"></span>
                                    Deskripsi Pekerjaan
                                </h2>
                                <div class="text-sm text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ $descPart }}
                                </div>
                            </div>
                        @endif

                        <!-- 2. Persyaratan Pekerjaan (Legacy Plain Text) -->
                        @if ($reqPart)
                            <div class="pt-6 border-t border-[#93F514]/15">
                                <h2 class="text-lg sm:text-xl font-extrabold text-[#EEEEEE] mb-3 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#93F514]"></span>
                                    Persyaratan & Kualifikasi
                                </h2>
                                <div class="text-sm text-gray-300 leading-relaxed whitespace-pre-line">
                                    {{ $reqPart }}
                                </div>
                            </div>
                        @endif

                        @if (!$descPart && !$reqPart)
                            <div class="text-sm text-gray-400 italic">
                                Deskripsi detail pekerjaan belum dicantumkan.
                            </div>
                        @endif
                    @endif

                    @if ($job->degrees->isNotEmpty())
                        <div class="pt-6 border-t border-[#93F514]/15">
                            <h3 class="text-base font-bold text-[#EEEEEE] mb-3">Kualifikasi Pendidikan:</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($job->degrees as $deg)
                                    <span
                                        class="px-3 py-1.5 rounded-lg bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-semibold">
                                        {{ $deg->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($job->majors->isNotEmpty())
                        <div class="pt-6 border-t border-[#93F514]/15">
                            <h3 class="text-base font-bold text-[#EEEEEE] mb-3">Jurusan yang Dicari:</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($job->majors as $maj)
                                    <span
                                        class="px-3 py-1.5 rounded-lg bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-semibold">
                                        {{ $maj->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Sidebar / Company Info & Related Jobs (4 cols) -->
            <div class="reveal-on-scroll reveal-slide-right lg:col-span-4 space-y-6" data-delay="150">

                <!-- Company Card -->
                <div class="rounded-3xl bg-[#050e05] border border-[#93F514]/25 p-6 space-y-4">
                    <h3 class="text-base font-bold text-[#EEEEEE]">
                        Tentang Perusahaan
                    </h3>
                    <div class="flex items-center gap-3">
                        @if ($job->company_id)
                            <a href="{{ route('companies.show', $job->company_id) }}" class="shrink-0 transition hover:opacity-90">
                        @endif
                        @if ($job->company?->logo_url)
                            <div
                                class="w-12 h-12 rounded-xl bg-white border border-[#93F514]/40 p-1.5 flex items-center justify-center shrink-0 overflow-hidden shadow-sm">
                                <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}"
                                    class="w-full h-full object-contain">
                            </div>
                        @else
                            <div
                                class="w-12 h-12 rounded-xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] font-bold text-lg shrink-0">
                                {{ strtoupper(substr($job->company?->name ?? 'M', 0, 2)) }}
                            </div>
                        @endif
                        @if ($job->company_id)
                            </a>
                        @endif
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-[#EEEEEE] text-sm truncate">
                                @if ($job->company_id)
                                    <a href="{{ route('companies.show', $job->company_id) }}" class="hover:text-[#93F514] transition">
                                        {{ $job->company?->name ?? 'Perusahaan Mitra' }}
                                    </a>
                                @else
                                    {{ $job->company?->name ?? 'Perusahaan Mitra' }}
                                @endif
                            </h4>
                            <span class="text-xs text-gray-400 block truncate">
                                {{ implode(', ', array_filter([$job->company?->city, $job->company?->province])) ?: $job->company?->address ?? 'Indonesia' }}
                            </span>
                        </div>
                    </div>

                    @if ($job->company?->address)
                        <div
                            class="p-3 rounded-xl bg-[#030803] border border-[#93F514]/15 text-xs text-gray-300 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-[#93F514] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="leading-relaxed">{{ $job->company->address }}</span>
                        </div>
                    @endif

                    <!-- Tombol Aksi Profil & Website -->
                    <div class="space-y-2 pt-2">
                        @if ($job->company_id)
                            <a href="{{ route('companies.show', $job->company_id) }}"
                               class="w-full py-2.5 px-4 rounded-xl bg-white/5 hover:bg-[#93F514]/15 border border-white/10 hover:border-[#93F514]/40 text-gray-200 hover:text-[#93F514] font-bold text-xs transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Lihat Profil Perusahaan</span>
                                <svg class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endif

                        @if ($job->company?->website)
                            @php
                                $webUrl = Str::startsWith($job->company->website, ['http://', 'https://'])
                                    ? $job->company->website
                                    : 'https://' . $job->company->website;
                                $displayWeb = preg_replace(
                                    '/^https?:\/\/(www\.)?/',
                                    '',
                                    rtrim($job->company->website, '/'),
                                );
                            @endphp
                            <a href="{{ $webUrl }}" target="_blank" rel="noopener noreferrer"
                                class="w-full py-2 px-4 rounded-xl bg-[#93F514]/10 hover:bg-[#93F514] border border-[#93F514]/40 text-[#93F514] hover:text-black font-semibold text-xs transition flex items-center justify-center gap-2 group">
                                <svg class="w-3.5 h-3.5 transition-transform group-hover:scale-110" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                                <span class="truncate">{{ $displayWeb }}</span>
                                <svg class="w-3 h-3 opacity-70 group-hover:opacity-100" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Other Open Jobs -->
                @if ($relatedJobs->isNotEmpty())
                    <div class="rounded-3xl bg-[#050e05] border border-[#93F514]/25 p-6 space-y-4">
                        <h3 class="text-base font-bold text-[#EEEEEE]">Lowongan Terkait Lainnya</h3>
                        <div class="space-y-3">
                            @foreach ($relatedJobs as $rJob)
                                <a href="{{ route('jobs.show', $rJob->id) }}"
                                    class="block p-3.5 rounded-xl bg-[#030803] border border-[#93F514]/15 hover:border-[#93F514]/50 transition">
                                    <h4 class="text-xs font-bold text-[#EEEEEE] hover:text-[#93F514] transition truncate">
                                        {{ $rJob->title }}</h4>
                                    <div class="text-[11px] flex items-center gap-1.5 mt-1">
                                        <span class="font-medium text-[#93F514]/80 related-company-name">{{ $rJob->company?->name }}</span>
                                        @if ($rJob->location)
                                            <span class="text-gray-400">•</span>
                                            <span class="text-gray-400">{{ $rJob->location }}</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

        </div>

        <!-- Modal 1: Konfirmasi Pelamaran Pekerjaan -->
        <div x-show="showConfirmModal" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title-confirm" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div x-show="showConfirmModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     @click="showConfirmModal = false" 
                     class="fixed inset-0 transition-opacity bg-black/80 backdrop-blur-sm"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content Card -->
                <div x-show="showConfirmModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="confirm-modal-card inline-block align-bottom bg-[#061506] rounded-3xl text-left overflow-hidden shadow-2xl border border-[#93F514]/40 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    <div class="p-6 sm:p-8 space-y-6">
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] shadow-sm shrink-0">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg sm:text-xl font-extrabold text-[#EEEEEE]" id="modal-title-confirm">
                                        Konfirmasi Lamaran
                                    </h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Pastikan profil dan data lamaran Anda telah sesuai.</p>
                                </div>
                            </div>
                            <button type="button" @click="showConfirmModal = false" class="p-1.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Job & Applicant Summary Box (Putih Bersih di Light Mode) -->
                        <div class="confirm-summary-box p-4 rounded-2xl bg-[#040a04] border border-[#93F514]/20 space-y-3 text-xs shadow-xs">
                            <div class="flex items-center justify-between pb-2 border-b border-[#93F514]/15">
                                <span class="text-gray-400">Posisi yang Dilamar</span>
                                <span class="job-title-val font-bold text-[#EEEEEE]">{{ $job->title }}</span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b border-[#93F514]/15">
                                <span class="text-gray-400">Perusahaan</span>
                                <span class="company-val font-semibold text-[#93F514]">{{ $job->company?->name ?? 'Perusahaan Mitra' }}</span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b border-[#93F514]/15">
                                <span class="text-gray-400">Penempatan & Tipe</span>
                                <span class="placement-val text-gray-300">{{ $job->location ?? 'Indonesia' }} ({{ $job->employment_type }})</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Nama Pelamar</span>
                                <span class="applicant-val font-bold text-gray-200">{{ auth()->user()?->name ?? 'Pelamar' }}</span>
                            </div>
                        </div>

                        <!-- Statement Alert -->
                        <div class="confirm-statement-box p-3.5 rounded-xl bg-[#93F514]/10 border border-[#93F514]/30 text-gray-300 text-xs flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-[#93F514] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="leading-relaxed">
                                Apakah Anda yakin ingin mengirim lamaran untuk posisi ini? Data CV dan profil Anda akan langsung diteruskan ke tim rekruter untuk proses seleksi berkas.
                            </p>
                        </div>

                        <!-- Actions Form -->
                        <form action="{{ route('jobs.apply', $job->id) }}" method="POST" @submit="isSubmitting = true" class="flex items-center justify-end gap-3 pt-2">
                            @csrf
                            <button type="button" 
                                    @click="showConfirmModal = false" 
                                    class="confirm-cancel-btn px-5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-gray-700 text-gray-300 hover:text-white font-semibold text-xs transition">
                                Batal
                            </button>
                            <button type="submit" 
                                    :disabled="isSubmitting"
                                    class="confirm-submit-btn px-6 py-2.5 rounded-xl bg-[#93F514] hover:bg-[#82dc0a] text-black font-extrabold text-xs shadow-md shadow-black/30 transition flex items-center gap-2 cursor-pointer disabled:opacity-50">
                                <svg x-show="isSubmitting" class="animate-spin w-4 h-4 text-black" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="isSubmitting ? 'Mengirim Lamaran...' : 'Ya, Kirim Lamaran'"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal 2: Peringatan Data Wajib Belum Lengkap -->
        <div x-show="showIncompleteModal" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title-incomplete" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div x-show="showIncompleteModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     @click="showIncompleteModal = false" 
                     class="fixed inset-0 transition-opacity bg-black/80 backdrop-blur-sm"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content Card -->
                <div x-show="showIncompleteModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="incomplete-modal-card inline-block align-bottom bg-[#0a0707] rounded-3xl text-left overflow-hidden shadow-2xl border border-amber-500/40 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    <div class="p-6 sm:p-8 space-y-6">
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/40 flex items-center justify-center text-amber-400 shadow-sm shrink-0">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg sm:text-xl font-extrabold text-[#EEEEEE]" id="modal-title-incomplete">
                                        Data Wajib Belum Lengkap
                                    </h3>
                                    <p class="text-xs text-amber-400/90 mt-0.5">Lengkapi profil untuk dapat mengajukan lamaran.</p>
                                </div>
                            </div>
                            <button type="button" @click="showIncompleteModal = false" class="p-1.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Explanation -->
                        <p class="text-xs sm:text-sm text-gray-300 leading-relaxed">
                            Mohon maaf, Anda belum dapat melamar pekerjaan ini karena terdapat data profil wajib yang belum diisi. Rekruter memerlukan data berikut untuk evaluasi kualifikasi Anda:
                        </p>

                        <!-- Incomplete Section Pills List -->
                        <div class="incomplete-summary-box p-4 rounded-2xl bg-[#140c0c] border border-amber-500/20 space-y-2">
                            <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">Bagian yang Belum Dilengkapi:</span>
                            <div class="flex flex-wrap gap-2 pt-1">
                                <template x-for="(section, idx) in missingSections" :key="idx">
                                    <span class="px-3 py-1.5 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs font-semibold flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span x-text="section"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
                            <button type="button" 
                                    @click="showIncompleteModal = false" 
                                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-semibold text-xs transition">
                                Tutup
                            </button>
                            <a href="{{ route('profile') }}" 
                               class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-600 hover:to-amber-500 text-black font-extrabold text-xs shadow-md shadow-black/30 transition text-center flex items-center justify-center gap-2">
                                <span>Lengkapi Profil Sekarang</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
