@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Navigasi Halaman') }}" class="flex items-center justify-center pt-6 border-t border-[#93F514]/20">
        <!-- Tombol Halaman & Navigasi -->
        <div class="inline-flex items-center gap-1.5 p-1 rounded-2xl bg-[#061506] border border-[#93F514]/30 shadow-lg shadow-black/40">
            
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-transparent cursor-not-allowed select-none flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('Sebelumnya') }}</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="px-3 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-black bg-transparent hover:bg-[#93F514] border border-transparent hover:border-[#93F514] transition-all duration-200 flex items-center gap-1 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('Sebelumnya') }}</span>
                </a>
            @endif

            {{-- Angka Nomor Halaman --}}
            <div class="flex items-center gap-1">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-gray-500">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="w-8 h-8 rounded-xl bg-[#93F514] text-black font-extrabold text-xs flex items-center justify-center shadow-md shadow-[#93F514]/30 border border-[#93F514] select-none">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="w-8 h-8 rounded-xl bg-transparent hover:bg-[#93F514]/15 text-gray-300 hover:text-[#93F514] border border-transparent hover:border-[#93F514]/40 font-semibold text-xs flex items-center justify-center transition-all duration-200 cursor-pointer">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Tombol Selanjutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="px-3 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-black bg-transparent hover:bg-[#93F514] border border-transparent hover:border-[#93F514] transition-all duration-200 flex items-center gap-1 cursor-pointer">
                    <span class="hidden sm:inline">{{ __('Selanjutnya') }}</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="px-3 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-transparent cursor-not-allowed select-none flex items-center gap-1">
                    <span class="hidden sm:inline">{{ __('Selanjutnya') }}</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif

        </div>

    </nav>
@endif
