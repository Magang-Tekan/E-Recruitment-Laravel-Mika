@php
    $dropCvUrl = auth()->check() ? route('profile') : route('login', ['redirect' => route('profile')]);

    // Use dynamic tracks from database if available, with graceful fallback
    $tracksList = (!empty($careerTracks) && is_array($careerTracks) && count($careerTracks) > 0)
        ? $careerTracks
        : [
            [
                'num' => '01',
                'badge' => __('Magang'),
                'prefix' => __('LOWONGAN'),
                'accent' => __('MAGANG'),
                'desc' => __('Peluang magang langsung di berbagai unit bisnis industri.'),
                'is_dark' => false,
                'img' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&auto=format&fit=crop&q=80',
                'back_badge' => __('INTERNSHIP TRACK'),
                'back_title' => __('PROGRAM MAGANG INDUSTRI'),
                'highlights' => [
                    __('Mentorship 1-on-1 langsung dari praktisi ahli industri'),
                    __('Terlibat langsung dalam proyek nyata berstandar global'),
                    __('Konversi SKS resmi & sertifikat kompetensi'),
                    __('Peluang fast-track rekrutmen karyawan tetap'),
                ],
                'cta' => __('Lihat Lowongan Magang'),
                'url' => route('jobs.index', ['experience_level' => 'Magang']),
                'experience_level' => 'Magang',
            ],
            [
                'num' => '02',
                'badge' => __('Fresh Graduate'),
                'prefix' => __('FRESH'),
                'accent' => __('GRADUATE'),
                'desc' => __('Peluang karir terbaik untuk lulusan baru dengan pengalaman hingga 2 tahun.'),
                'is_dark' => true,
                'img' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&auto=format&fit=crop&q=80',
                'back_badge' => __('GRADUATE PROGRAM'),
                'back_title' => __('GRADUATE ACCELERATION'),
                'highlights' => [
                    __('Onboarding komprehensif & kurikulum pengembangan'),
                    __('Eksplorasi rotasi divisi lintas fungsi kerja'),
                    __('Lingkungan kerja modern & kultur inovatif'),
                    __('Jenjang karir transparan dengan evaluasi berkala'),
                ],
                'cta' => __('Eksplorasi Lowongan FG'),
                'url' => route('jobs.index', ['experience_level' => 'Fresh Graduate']),
                'experience_level' => 'Fresh Graduate',
            ],
            [
                'num' => '03',
                'badge' => __('Profesional'),
                'prefix' => __('LOWONGAN'),
                'accent' => __('PROFESIONAL'),
                'desc' => __('Posisi strategis untuk para profesional berpengalaman di bidangnya.'),
                'is_dark' => false,
                'img' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=600&auto=format&fit=crop&q=80',
                'back_badge' => __('EXPERIENCED HIRE'),
                'back_title' => __('PROFESSIONAL TRACK'),
                'highlights' => [
                    __('Peran strategis memimpin proyek & tim spesialis'),
                    __('Remunerasi kompetitif & benefit kesehatan prima'),
                    __('Otonomi dalam inovasi & pengambilan keputusan'),
                    __('Dampak langsung pada pertumbuhan industri nasional'),
                ],
                'cta' => __('Lihat Posisi Profesional'),
                'url' => route('jobs.index', ['experience_level' => 'Profesional']),
                'experience_level' => 'Profesional',
            ],
            [
                'num' => '04',
                'badge' => __('Talent Pool'),
                'prefix' => __('DROP'),
                'accent' => __('YOUR CV'),
                'desc' => __('Belum ada posisi cocok? Kirimkan CV Anda untuk masa depan.'),
                'is_dark' => true,
                'img' => 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=600&auto=format&fit=crop&q=80',
                'back_badge' => __('TALENT NETWORK'),
                'back_title' => __('DATABASE TALENT POOL'),
                'highlights' => [
                    __('Data Anda terindeks prioritas di database HR'),
                    __('Dihubungi langsung saat lowongan relevan dibuka'),
                    __('Notifikasi peluang karir yang sesuai keahlian Anda'),
                    __('Kerahasiaan data terjamin dengan sistem terenkripsi'),
                ],
                'cta' => __('Kirimkan CV Sekarang'),
                'url' => $dropCvUrl,
                'experience_level' => null,
            ],
        ];
@endphp

<!-- Dynamic 4-Column Grid 3D Flip Cards (CMS-backed) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 lg:gap-4 items-stretch pt-2">
    @foreach ($tracksList as $index => $track)
        @php 
            $isDark = $track['is_dark'] ?? false;
            $cardDelay = ($index + 1) * 100;
        @endphp
        <div class="reveal-on-scroll reveal-drop-from-top h-full flex flex-col" data-delay="{{ $cardDelay }}">
            <div x-data="{ open: false }"
                 @click="if (window.innerWidth < 1024) open = !open"
                 @click.away="open = false"
                 class="flip-card-wrapper relative w-full h-[400px] cursor-pointer select-none group">
                
                <div class="flip-card-inner" :class="{ 'is-flipped': open }">

                    <!-- SISI DEPAN (FRONT) -->
                    <div class="flip-card-front {{ $isDark ? 'bg-[#141614] text-white border border-white/10 shadow-black/40' : 'bg-white text-gray-900 border border-gray-200 shadow-black/15' }} rounded-[22px] p-5 flex flex-col justify-between overflow-hidden shadow-xl">
                        <div>
                            <!-- Header Baris Atas: Badge Kategori & Nomor Urut -->
                            <div class="flex items-center justify-between mb-3.5">
                                <span class="px-3 py-1 rounded-full border {{ $isDark ? 'border-white/15 bg-white/10 text-white' : 'border-gray-300 bg-gray-100 text-gray-800' }} text-xs font-semibold">
                                    {{ $track['badge'] }}
                                </span>
                                <span class="font-bold text-sm tracking-tight shrink-0 {{ $isDark ? 'text-gray-300' : 'text-gray-500' }}">
                                    {{ $track['num'] }}
                                </span>
                            </div>

                            <!-- Judul (Card 1 & 3: Hitam tanpa shadow; Card 2 & 4: Putih + Neon) -->
                            <h3 class="text-xl sm:text-2xl font-black uppercase tracking-tight leading-none mb-2 {{ $isDark ? 'text-white' : 'text-black' }}">
                                <span>{{ $track['prefix'] }}</span><br>
                                <span class="{{ $isDark ? 'text-[#93F514]' : 'text-black' }} font-black">{{ $track['accent'] }}</span>
                            </h3>

                            <!-- Deskripsi Singkat -->
                            <p class="text-xs {{ $isDark ? 'text-gray-300/90' : 'text-gray-600' }} leading-snug">
                                {{ $track['desc'] }}
                            </p>
                        </div>

                        <!-- Area Gambar (Full Color Natural, No Grayscale) -->
                        <div class="rounded-xl overflow-hidden {{ $isDark ? 'border border-white/10 bg-black/40' : 'border border-gray-200 bg-gray-100' }} p-0.5 aspect-[16/10] relative mt-3 shrink-0 shadow-inner">
                            <img src="{{ $track['img'] }}"
                                 alt="{{ $track['accent'] }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover rounded-[10px] transition-transform duration-500 group-hover:scale-105">
                            
                            <!-- Circular Arrow Action Button -->
                            <div class="absolute bottom-2.5 right-2.5 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white text-gray-900 border border-black/10 flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-110">
                                <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- SISI BELAKANG (BACK) -->
                    <div class="flip-card-back bg-[#18461B] dark:bg-[#93F514] text-white dark:text-[#051405] border-2 border-[#93F514]/35 dark:border-black/10 rounded-[22px] p-5 pb-5 flex flex-col justify-between shadow-xl overflow-hidden">
                        <div>
                            <!-- Header Belakang -->
                            <div class="flex items-center justify-between mb-2">
                                <span class="card-back-badge px-2.5 py-0.5 rounded-full border border-white/25 dark:border-black/30 bg-white/10 dark:bg-black/10 text-white dark:text-[#051405] text-[10px] font-bold tracking-wider uppercase">
                                    {{ $track['back_badge'] }}
                                </span>
                                <span class="card-back-num font-bold text-xs text-white/90 dark:text-black/80 px-2 py-0.5 rounded-md border border-white/20 dark:border-black/20 bg-white/10 dark:bg-black/5 shrink-0">
                                    {{ $track['num'] }}
                                </span>
                            </div>

                            <h4 class="text-lg font-black uppercase tracking-tight leading-tight mb-2 text-white dark:text-black">
                                {{ $track['back_title'] }}
                            </h4>

                            <!-- Highlights -->
                            <ul class="space-y-1.5 text-[11px] font-semibold leading-snug text-gray-100 dark:text-black/90">
                                @foreach ($track['highlights'] as $highlight)
                                    <li class="flex items-start gap-1.5">
                                        <span class="w-3.5 h-3.5 rounded-full bg-[#93F514]/20 dark:bg-black/15 text-[#93F514] dark:text-black flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-2 h-2 stroke-[3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </span>
                                        <span>{{ $highlight }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- CTA Button with Intuitive Hover Affordance -->
                        <div class="card-back-divider pt-3 border-t border-white/15 dark:border-black/15">
                            <a href="{{ $track['url'] }}"
                               @click.stop
                               @if(Str::startsWith($track['url'], ['http://', 'https://']) && !Str::contains($track['url'], request()->getHost()))
                                   target="_blank" rel="noopener noreferrer"
                               @endif
                               class="flip-card-cta w-full py-2.5 pl-4 pr-2 rounded-full bg-white dark:bg-[#051405] text-[#0E2F11] dark:text-white hover:bg-emerald-50/90 dark:hover:bg-black hover:shadow-xl active:scale-[0.99] border border-white/90 dark:border-white/20 font-bold text-xs flex items-center justify-between transition-all duration-200 group/cta shadow-lg shadow-black/20 cursor-pointer">
                                <span class="text-[#0E2F11] dark:text-white group-hover/cta:text-[#071F09] dark:group-hover/cta:text-[#93F514] font-bold transition-colors duration-200 truncate mr-2">{{ $track['cta'] }}</span>
                                <span class="flip-card-cta-icon w-6 h-6 rounded-full bg-[#103613] dark:bg-[#93F514] text-white dark:text-black flex items-center justify-center shrink-0 transition-transform duration-200 group-hover/cta:scale-110 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-white dark:text-black">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
</div>
