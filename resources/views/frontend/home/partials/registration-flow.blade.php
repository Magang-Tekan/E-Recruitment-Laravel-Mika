@php
    $steps = isset($registrationSteps) ? collect($registrationSteps) : collect();
    $stepCount = $steps->count();
    $flowTitle = $registrationFlow['title'] ?? 'Alur Pendaftaran & Seleksi';
    $flowSubtitle = $registrationFlow['subtitle'] ?? '';
    $flowEnabled = $registrationFlow['enabled'] ?? true;

    $stepperCols = match (true) {
        $stepCount <= 3 => 'sm:grid-cols-3',
        $stepCount === 4 => 'sm:grid-cols-2 lg:grid-cols-4',
        $stepCount === 5 => 'sm:grid-cols-3 lg:grid-cols-5',
        default => 'sm:grid-cols-3 lg:grid-cols-6',
    };

    $icons = [
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'briefcase' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'clipboard' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'video' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
        'award' => 'M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5',
    ];
@endphp

@if ($flowEnabled && $stepCount > 0)
    <section id="alur-pendaftaran"
        class="reveal-on-scroll pt-14 sm:pt-20 pb-8 sm:pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto relative border-t border-gray-200/80 dark:border-white/[0.08]"
        x-data="{ activeStep: 1 }">

        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ $flowTitle }}</h2>
            @if ($flowSubtitle)
                <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-zinc-400 leading-relaxed max-w-2xl mx-auto">
                    {{ $flowSubtitle }}</p>
            @endif
        </div>

        <!-- Connected Interactive Pipeline Stepper Bar -->
        <div class="mb-8">
            <div class="grid grid-cols-2 {{ $stepperCols }} gap-2.5 sm:gap-3">
                @foreach ($steps as $step)
                    <button type="button" @click="activeStep = {{ $loop->iteration }}"
                        class="group relative text-left p-3.5 rounded-2xl border transition-all duration-200 cursor-pointer overflow-hidden"
                        :class="activeStep === {{ $loop->iteration }} ?
                            'bg-white dark:bg-white/[0.08] border-[#1a4d00] dark:border-[#93F514] shadow-sm' :
                            'bg-white dark:bg-white/[0.02] hover:bg-emerald-50/40 dark:hover:bg-white/[0.05] border-gray-200 dark:border-white/[0.06] hover:border-[#1a4d00] dark:hover:border-white/15 shadow-sm dark:shadow-none'">

                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-extrabold tracking-wider transition-colors"
                                :class="activeStep === {{ $loop->iteration }} ? 'text-[#1a4d00] dark:text-[#93F514]' :
                                    'text-gray-400 dark:text-zinc-500 group-hover:text-[#1a4d00] dark:group-hover:text-zinc-400'">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="w-2 h-2 rounded-full transition-colors"
                                :class="activeStep === {{ $loop->iteration }} ? 'bg-[#1a4d00] dark:bg-[#93F514]' :
                                    'bg-gray-300 dark:bg-zinc-700 group-hover:bg-[#1a4d00] dark:group-hover:bg-zinc-500'"></span>
                        </div>
                        <div class="text-xs sm:text-sm font-bold truncate transition-colors"
                            :class="activeStep === {{ $loop->iteration }} ? 'text-gray-900 dark:text-white' :
                                'text-gray-700 dark:text-zinc-300 group-hover:text-gray-900 dark:group-hover:text-white'">
                            {{ $step->localized_short_title }}</div>
                        @if ($step->short_sub)
                            <div class="text-[11px] font-medium truncate mt-0.5 transition-colors"
                                :class="activeStep === {{ $loop->iteration }} ? 'text-[#1a4d00] dark:text-zinc-400' :
                                    'text-gray-500 dark:text-zinc-500 group-hover:text-[#1a4d00] dark:group-hover:text-zinc-400'">
                                {{ $step->localized_short_sub }}</div>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Active Stage Detail Display Canvas -->
        <div
            class="relative rounded-3xl bg-white dark:bg-gradient-to-b dark:from-[#0a120a] dark:via-[#050905] dark:to-[#040604] border border-gray-200/90 dark:border-white/[0.08] p-6 sm:p-10 lg:p-12 shadow-xl shadow-gray-200/60 dark:shadow-2xl dark:shadow-black/80 overflow-hidden">

            @foreach ($steps as $step)
                @php
                    $cta = $step->cta;
                    $highlights = $step->localized_highlights;
                    $highlightCols = match (count($highlights)) {
                        1 => 'sm:grid-cols-1',
                        2 => 'sm:grid-cols-2',
                        default => 'sm:grid-cols-3',
                    };
                    $eyebrow = $step->localizedEyebrowLine($loop->iteration);
                    $panelTitle = $step->localizedPanelHeading($loop->iteration);
                    $iconPath = $icons[$step->icon] ?? $icons['shield'];
                @endphp

                <div x-show="activeStep === {{ $loop->iteration }}" x-cloak
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                    <div class="lg:col-span-7 space-y-6">
                        <div class="space-y-3">
                            <div
                                class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                <span>{{ $eyebrow }}</span>
                            </div>
                            <h3
                                class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                {{ $step->localized_title }}
                            </h3>
                            @if ($step->description)
                                <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                    {{ $step->localized_description }}
                                </p>
                            @endif
                        </div>

                        @if (count($highlights) > 0)
                            <!-- Structured Milestone Items -->
                            <div class="grid grid-cols-1 {{ $highlightCols }} gap-3 pt-2">
                                @foreach ($highlights as $highlight)
                                    @php
                                        $itemTitle = is_array($highlight) ? ($highlight['title'] ?? '') : $highlight;
                                        $itemDesc = is_array($highlight) ? ($highlight['desc'] ?? '') : '';
                                    @endphp
                                    <div
                                        class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                        <div
                                            class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514] shrink-0"></span>
                                            <span>{{ $itemTitle }}</span>
                                        </div>
                                        @if ($itemDesc)
                                            <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                                {{ $itemDesc }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            @if ($cta['arrow'])
                                <a href="{{ $cta['url'] }}"
                                    class="inline-flex items-center gap-3 px-6 py-2.5 rounded-xl stage-cta bg-black text-white dark:bg-[#93F514] dark:text-black font-bold text-xs sm:text-sm hover:bg-[#1a4d00] hover:text-white dark:hover:bg-[#82e00b] dark:hover:text-black transition shadow-md shadow-black/25 dark:shadow-[#93F514]/25 group">
                                    <span>{{ $cta['label'] }}</span>
                                    <span
                                        class="w-5 h-5 rounded-md bg-white/15 dark:bg-black/10 group-hover:bg-white/25 flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </span>
                                </a>
                            @else
                                <a href="{{ $cta['url'] }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl stage-cta bg-black text-white dark:bg-[#93F514] dark:text-black font-bold text-xs sm:text-sm hover:bg-[#1a4d00] hover:text-white dark:hover:bg-[#82e00b] dark:hover:text-black transition shadow-md shadow-black/25 dark:shadow-[#93F514]/25">
                                    <span>{{ $cta['label'] }}</span>
                                </a>
                            @endif

                            @if (! $loop->last && $step->next_label)
                                <button type="button" @click="activeStep = {{ $loop->iteration + 1 }}"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                    <span>{{ $step->localized_next_label }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div
                            class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">{{ $panelTitle }}</span>
                            </div>
                            @if ($step->tips_title || $step->tips_body)
                                <div class="space-y-2">
                                    @if ($step->tips_title)
                                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                            {{ $step->localized_tips_title }}</h4>
                                    @endif
                                    @if ($step->tips_body)
                                        <div
                                            class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                            {{ $step->localized_tips_body }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if ($step->footnote)
                                <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                    <svg class="w-4 h-4 shrink-0 text-[#4fa304] dark:text-[#93F514]" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $iconPath }}" />
                                    </svg>
                                    <span>{{ $step->localized_footnote }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </section>
@endif
