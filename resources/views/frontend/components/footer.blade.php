<footer class="relative bg-[#020602] border-t border-[#93F514]/30 text-gray-400 overflow-hidden">


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:flex lg:flex-row lg:justify-between items-start gap-8 md:gap-10 lg:gap-8 xl:gap-12">
            <!-- Brand Info -->
            <div class="w-full lg:w-auto lg:max-w-sm space-y-4">
                <div class="flex items-center gap-3">
                    @php
                        $logoUrl = (isset($mainCompany) && $mainCompany->logo_url)
                            ? $mainCompany->logo_url
                            : asset('storage/logo/mikalight.png');
                    @endphp
                    {{-- <img src="{{ $logoUrl }}" 
                         alt="{{ $mainCompany->name ?? 'Logo MIKA' }}" 
                         class="h-10 w-auto object-contain rounded-lg"> --}}
                    <span class="text-xl font-extrabold tracking-tight text-[#EEEEEE]">
                        MIKA <span class="text-[#93F514]">Group </span>Career
                    </span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    {{ __('Platform rekrutmen digital terintegrasi yang mempertemukan talenta profesional unggul dengan peluang karir terbaik di') }} MIKA Group Career.
                </p>
                @php
                    $mainSocial = (isset($mainCompany) && is_array($mainCompany->social_media)) ? array_filter($mainCompany->social_media) : [];
                @endphp

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    @if(isset($mainCompany) && $mainCompany->website)
                        <a href="{{ Str::startsWith($mainCompany->website, ['http://', 'https://']) ? $mainCompany->website : 'https://' . $mainCompany->website }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           title="{{ __('Kunjungi Website Perusahaan') }}"
                           class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-[#050e05] border border-[#93F514]/40 text-xs font-semibold text-gray-300 hover:text-[#93F514] hover:border-[#93F514] transition footer-official-btn">
                            <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            <span>{{ __('Official Website') }}</span>
                        </a>
                    @endif
                </div>

                @if(!empty($mainSocial))
                    <div class="pt-2">
                        <span class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-wider">{{ __('Media Sosial Kami') }}</span>
                        <div class="flex flex-wrap items-center gap-2">
                            @if(!empty($mainSocial['instagram']))
                                <a href="{{ $mainSocial['instagram'] }}" target="_blank" rel="noopener noreferrer" title="Instagram" class="w-8 h-8 rounded-lg bg-[#050e05] border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </a>
                            @endif
                            @if(!empty($mainSocial['linkedin']))
                                <a href="{{ $mainSocial['linkedin'] }}" target="_blank" rel="noopener noreferrer" title="LinkedIn" class="w-8 h-8 rounded-lg bg-[#050e05] border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                </a>
                            @endif
                            @if(!empty($mainSocial['facebook']))
                                <a href="{{ $mainSocial['facebook'] }}" target="_blank" rel="noopener noreferrer" title="Facebook" class="w-8 h-8 rounded-lg bg-[#050e05] border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                                </a>
                            @endif
                            @if(!empty($mainSocial['youtube']))
                                <a href="{{ $mainSocial['youtube'] }}" target="_blank" rel="noopener noreferrer" title="YouTube" class="w-8 h-8 rounded-lg bg-[#050e05] border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                            @if(!empty($mainSocial['tiktok']))
                                <a href="{{ $mainSocial['tiktok'] }}" target="_blank" rel="noopener noreferrer" title="TikTok" class="w-8 h-8 rounded-lg bg-[#050e05] border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-1.01-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                </a>
                            @endif
                            @if(!empty($mainSocial['x']))
                                <a href="{{ $mainSocial['x'] }}" target="_blank" rel="noopener noreferrer" title="X (Twitter)" class="w-8 h-8 rounded-lg bg-[#050e05] border border-[#93F514]/30 hover:border-[#93F514] text-gray-400 hover:text-[#93F514] flex items-center justify-center transition hover:scale-105">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Links -->
            <div class="w-full lg:w-auto">
                <h4 class="text-sm font-bold tracking-wider uppercase text-[#93F514] mb-4">{{ __('Navigasi') }}</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-[#93F514] transition inline-block">{{ __('Beranda') }}</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-[#93F514] transition inline-block">{{ __('Tentang Kami') }}</a></li>
                    <li><a href="{{ route('jobs.index') }}" class="hover:text-[#93F514] transition inline-block">{{ __('Cari Lowongan') }}</a></li>
                    @guest
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}" class="hover:text-[#93F514] transition inline-block">{{ __('Daftar') }}</a></li>
                        @endif
                        <li><a href="{{ route('login') }}" class="hover:text-[#93F514] transition inline-block">{{ __('Masuk') }}</a></li>
                    @else
                        @php
                            $roleId = auth()->user()->role_id;
                            $roleName = strtolower(auth()->user()->role?->name ?? '');
                            $targetDashboard = match(true) {
                                $roleId == 1 || in_array($roleName, ['admin', 'superadmin']) => route('admin.dashboard'),
                                $roleId == 2 || $roleName === 'recruiter' => route('recruiter.dashboard'),
                                $roleId == 4 || $roleName === 'employee' => route('employee.dashboard'),
                                default => route('profile'),
                            };
                            $dashboardLabel = match(true) {
                                $roleId == 1 || in_array($roleName, ['admin', 'superadmin']) || $roleId == 2 || $roleName === 'recruiter' => __('Dashboard'),
                                $roleId == 4 || $roleName === 'employee' => __('Panel Pegawai'),
                                default => __('Profil & Lamaran Saya'),
                            };
                        @endphp
                        <li><a href="{{ $targetDashboard }}" class="hover:text-[#93F514] transition inline-block">{{ $dashboardLabel }}</a></li>
                    @endguest
                </ul>
            </div>

            <!-- Company Categories -->
            <div class="w-full lg:w-auto">
                <h4 class="text-sm font-bold tracking-wider uppercase text-[#93F514] mb-4">{{ __('Kategori Pekerjaan') }}</h4>
                <ul class="space-y-3 text-sm">
                    @forelse($footerDepartments ?? [] as $fDept)
                        <li>
                            <a href="{{ route('jobs.index', ['department_id' => $fDept->id]) }}" class="hover:text-[#93F514] transition inline-block">
                                {{ $fDept->name }}
                            </a>
                        </li>
                    @empty
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-[#93F514] transition inline-block">{{ __('Semua Lowongan') }}</a></li>
                    @endforelse
                </ul>
            </div>

            <!-- Contact & Location -->
            <div class="w-full lg:w-auto lg:max-w-xs xl:max-w-sm">
                <h4 class="text-sm font-bold tracking-wider uppercase text-[#93F514] mb-4">{{ __('Hubungi Kami') }}</h4>
                @php
                    $addressParts = [];
                    if (isset($mainCompany)) {
                        if (!empty($mainCompany->address)) $addressParts[] = trim($mainCompany->address);
                        if (!empty($mainCompany->city)) $addressParts[] = trim($mainCompany->city);
                        if (!empty($mainCompany->province)) $addressParts[] = trim($mainCompany->province);
                    }
                    $fullAddress = !empty($addressParts) ? implode(', ', $addressParts) : 'Semarang, Jawa Tengah, Indonesia';
                @endphp
                <ul class="space-y-3.5 text-sm">
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-[#93F514] shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-gray-300 leading-relaxed">{{ $fullAddress }}</span>
                    </li>
                    @if(isset($mainCompany) && $mainCompany->email)
                        <li class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-[#93F514] shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:{{ $mainCompany->email }}" class="text-gray-300 hover:text-[#93F514] transition break-all sm:break-normal leading-relaxed" title="{{ $mainCompany->email }}">
                                {{ $mainCompany->email }}
                            </a>
                        </li>
                    @endif
                    @if(isset($mainCompany) && $mainCompany->phone)
                        <li class="flex items-start gap-3">
                            <svg class="w-4 h-4 text-[#93F514] shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="{{ $mainCompany->whatsapp_url ?? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $mainCompany->phone) }}" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-[#93F514] transition leading-relaxed" title="{{ $mainCompany->phone }} (HRD)">
                                {{ $mainCompany->phone }} (HRD)
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-14 pt-8 border-t border-[#93F514]/20 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
            <p>&copy; {{ date('Y') }} {{ isset($mainCompany) ? $mainCompany->name : 'Mitra Karya Analitika' }}. {{ __('All rights reserved.') }}</p>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-[#93F514] transition">{{ __('Kebijakan Privasi') }}</a>
                <a href="#" class="hover:text-[#93F514] transition">{{ __('Syarat & Ketentuan') }}</a>
            </div>
        </div>
    </div>
</footer>
