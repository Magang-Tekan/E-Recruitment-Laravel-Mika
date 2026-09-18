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
                    <img src="{{ $logoUrl }}" 
                         alt="{{ $mainCompany->name ?? 'Logo MIKA' }}" 
                         class="h-10 w-auto object-contain rounded-lg">
                    <span class="text-xl font-extrabold tracking-tight text-[#EEEEEE]">
                        {{ isset($mainCompany) ? $mainCompany->name : 'MIKA CAREER' }}
                    </span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    Platform rekrutmen digital terintegrasi yang mempertemukan talenta profesional unggul dengan peluang karir terbaik di {{ isset($mainCompany) ? $mainCompany->name : 'Mitra Karya Analitika' }}.
                </p>
                @if(isset($mainCompany) && $mainCompany->website)
                    <div>
                        <a href="{{ Str::startsWith($mainCompany->website, ['http://', 'https://']) ? $mainCompany->website : 'https://' . $mainCompany->website }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           title="Kunjungi Website Perusahaan"
                           class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-[#050e05] border border-[#93F514]/40 text-xs font-semibold text-gray-300 hover:text-[#93F514] hover:border-[#93F514] transition footer-official-btn">
                            <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            <span>Official Website</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Quick Links -->
            <div class="w-full lg:w-auto">
                <h4 class="text-sm font-bold tracking-wider uppercase text-[#93F514] mb-4">Navigasi</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-[#93F514] transition inline-block">Beranda</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-[#93F514] transition inline-block">Tentang Kami</a></li>
                    <li><a href="{{ route('jobs.index') }}" class="hover:text-[#93F514] transition inline-block">Cari Lowongan</a></li>
                    @guest
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}" class="hover:text-[#93F514] transition inline-block">Daftar</a></li>
                        @endif
                        <li><a href="{{ route('login') }}" class="hover:text-[#93F514] transition inline-block">Masuk</a></li>
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
                                $roleId == 1 || in_array($roleName, ['admin', 'superadmin']) || $roleId == 2 || $roleName === 'recruiter' => 'Dashboard',
                                $roleId == 4 || $roleName === 'employee' => 'Panel Pegawai',
                                default => 'Profil & Lamaran Saya',
                            };
                        @endphp
                        <li><a href="{{ $targetDashboard }}" class="hover:text-[#93F514] transition inline-block">{{ $dashboardLabel }}</a></li>
                    @endguest
                </ul>
            </div>

            <!-- Company Categories -->
            <div class="w-full lg:w-auto">
                <h4 class="text-sm font-bold tracking-wider uppercase text-[#93F514] mb-4">Kategori Pekerjaan</h4>
                <ul class="space-y-3 text-sm">
                    @forelse($footerDepartments ?? [] as $fDept)
                        <li>
                            <a href="{{ route('jobs.index', ['department_id' => $fDept->id]) }}" class="hover:text-[#93F514] transition inline-block">
                                {{ $fDept->name }}
                            </a>
                        </li>
                    @empty
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-[#93F514] transition inline-block">Semua Lowongan</a></li>
                    @endforelse
                </ul>
            </div>

            <!-- Contact & Location -->
            <div class="w-full lg:w-auto lg:max-w-xs xl:max-w-sm">
                <h4 class="text-sm font-bold tracking-wider uppercase text-[#93F514] mb-4">Hubungi Kami</h4>
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
                            <a href="{{ $mainCompany->whatsapp_url ?? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $mainCompany->phone) }}" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-[#93F514] transition leading-relaxed" title="{{ $mainCompany->phone }}">
                                {{ $mainCompany->phone }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-14 pt-8 border-t border-[#93F514]/20 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
            <p>&copy; {{ date('Y') }} {{ isset($mainCompany) ? $mainCompany->name : 'Mitra Karya Analitika' }}. All rights reserved.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-[#93F514] transition">Kebijakan Privasi</a>
                <a href="#" class="hover:text-[#93F514] transition">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>
</footer>
