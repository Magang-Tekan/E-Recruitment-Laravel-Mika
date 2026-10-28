@extends('frontend.layouts.app')

@section('title', __('Beranda') . ' - Mika Career')

@section('content')
    <div class="relative overflow-hidden -mt-[88px] sm:-mt-[96px]">

        <!-- ==================== HERO SECTION (IMAGE BACKGROUND + MULTI-LAYER DARK GRADIENTS) ==================== -->
        <section
            class="relative min-h-screen flex items-center justify-center pt-[120px] sm:pt-[128px] pb-10 sm:pb-12 px-4 sm:px-6 lg:px-8 overflow-hidden bg-[#040804]">

            <!-- Hero Background Image (Clear & Crisp) -->
            <div class="absolute inset-0 z-0">
                <img src="https://i.pinimg.com/736x/20/dc/f0/20dcf0cfb0122a38d938e3c3011fb0d4.jpg"
                    alt="Recruitment Career Background"
                    class="w-full h-full object-cover object-center filter brightness-90 contrast-105">
            </div>

            <!-- Subtle Dark Gradient & Edge Vignette (Keeps Text Readable while Image Stays Crisp & Clear) -->
            <div class="absolute inset-0 z-0 bg-gradient-to-b from-[#040804]/60 via-[#040804]/40 to-[#040804]"></div>
            <div class="absolute inset-0 z-0 bg-black/35"></div>
            <div class="absolute inset-0 z-0 bg-[radial-gradient(ellipse_at_center,_transparent_40%,_#040804_95%)]"></div>



            <div class="relative max-w-5xl mx-auto text-center z-10 flex flex-col items-center">

                <!-- Main Headline Hero with Neon #93F514 -->
                <h1
                    class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-[#EEEEEE] leading-tight sm:leading-none max-w-4xl drop-shadow-[0_4px_16px_rgba(0,0,0,0.8)]">
                    {{ __('Temukan Karir Impian,') }} <br>
                    <span class="text-[#93F514]">
                        {{ __('Wujudkan Potensi Terbaikmu') }}
                    </span>
                </h1>

                <!-- Subtitle Description / Slogan Layer -->
                <p
                    class="mt-6 text-base sm:text-xl text-gray-200 max-w-2xl font-normal leading-relaxed drop-shadow-[0_2px_8px_rgba(0,0,0,0.9)]">
                    {{ __('Bergabunglah bersama ribuan profesional bertalenta. Jelajahi lowongan kerja, ikuti tes seleksi online terintegrasi, dan raih karir masa depan sekarang.') }}
                </p>

                @php
                    $getHomeFilterArr = function ($key) {
                        $val = request($key);
                        if (is_array($val)) {
                            return array_values(array_filter($val, fn($v) => !is_null($v) && $v !== ''));
                        }
                        if (is_string($val) && trim($val) !== '') {
                            return array_values(array_filter(explode(',', $val), fn($v) => trim($v) !== ''));
                        }
                        return [];
                    };
                    $homeSelectedCompanies = $getHomeFilterArr('company_id');
                    if (empty($homeSelectedCompanies)) {
                        $homeSelectedCompanies = $getHomeFilterArr('company_ids');
                    }
                    $homeSelectedDepartments = $getHomeFilterArr('department_id');
                    if (empty($homeSelectedDepartments)) {
                        $homeSelectedDepartments = $getHomeFilterArr('department_ids');
                    }
                    $homeSelectedTypes = $getHomeFilterArr('employment_type');
                    if (empty($homeSelectedTypes)) {
                        $homeSelectedTypes = $getHomeFilterArr('employment_types');
                    }
                @endphp

                <!-- Search Bar Form (Clean Segmented Solid Light Pill Bar) -->
                <div class="w-full max-w-5xl mt-10 p-2 sm:p-2.5 rounded-2xl lg:rounded-full bg-[#EEEEEE] text-gray-800 shadow-2xl shadow-black/80 border border-white/40 relative z-30"
                    style="background-color: #EEEEEE !important;">
                    <form action="{{ route('jobs.index') }}" method="GET"
                        class="flex flex-col lg:flex-row items-center gap-2 lg:gap-0 divide-y lg:divide-y-0 lg:divide-x divide-gray-300">

                        <!-- Search Input -->
                        <div class="w-full lg:flex-1 relative flex items-center px-4 py-1">
                            <div class="pr-2.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('Cari lowongan yang Anda inginkan...') }}"
                                style="background-color: transparent !important; color: #111827 !important;"
                                class="w-full py-2 bg-transparent text-gray-900 placeholder-gray-400 border-0 outline-none focus:outline-none focus:ring-0 focus:border-0 shadow-none text-sm font-medium">
                        </div>

                        <!-- Dropdown Semua Perusahaan -->
                        <div class="w-full lg:w-56 relative" :class="open ? 'z-50' : 'z-10'" x-data="{
                            open: false,
                            search: '',
                            selected: {{ json_encode(array_map('strval', $homeSelectedCompanies ?? [])) }},
                            tempSelected: {{ json_encode(array_map('strval', $homeSelectedCompanies ?? [])) }},
                            companiesMap: {
                                @foreach ($companies as $comp)
                                    '{{ $comp->id }}': '{{ addslashes($comp->name) }}', @endforeach
                            },
                            getDisplayText() {
                                if (!this.selected || this.selected.length === 0) return '{{ __('Semua Perusahaan') }}';
                                if (this.selected.length === 1) {
                                    return this.companiesMap[this.selected[0]] || '1 {{ __('Perusahaan') }}';
                                }
                                return this.selected.length + ' {{ __('Perusahaan') }}';
                            },
                            openDropdown() {
                                this.tempSelected = [...this.selected];
                                this.search = '';
                                this.open = true;
                            },
                            reset() {
                                this.tempSelected = [];
                                this.selected = [];
                                this.open = false;
                            },
                            apply() {
                                this.selected = [...this.tempSelected];
                                this.open = false;
                            }
                        }"
                            @click.outside="open = false">

                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="company_id[]" :value="id">
                            </template>

                            <button type="button" @click="open ? open = false : openDropdown()"
                                :class="selected.length > 0 ? 'text-[#93F514] font-bold' : 'text-gray-700'"
                                class="w-full py-2.5 px-4 flex items-center justify-between text-left text-sm transition">
                                <div class="flex items-center gap-2 truncate">
                                    <template x-if="selected.length > 0">
                                        <span
                                            class="min-w-5 h-5 px-1 rounded-full bg-[#93F514] text-black font-extrabold text-xs flex items-center justify-center shrink-0 shadow-sm"
                                            x-text="selected.length">
                                            {{ count($homeSelectedCompanies ?? []) }}
                                        </span>
                                    </template>
                                    <span class="truncate" x-text="getDisplayText()">
                                        @if (!empty($homeSelectedCompanies))
                                            @if (count($homeSelectedCompanies) === 1)
                                                {{ $companies->firstWhere('id', $homeSelectedCompanies[0])?->name ?? '1 ' . __('Perusahaan') }}
                                            @else
                                                {{ count($homeSelectedCompanies) }} {{ __('Perusahaan') }}
                                            @endif
                                        @else
                                            {{ __('Semua Perusahaan') }}
                                        @endif
                                    </span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                                    :class="open ? 'rotate-180 text-[#93F514]' : (selected.length > 0 ? 'text-[#93F514]' : '')"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Popover Panel -->
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                class="absolute top-full left-0 lg:left-auto lg:right-0 mt-2 w-80 sm:w-[460px] bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 z-50 text-gray-800 flex flex-col">

                                <!-- Search Input -->
                                <div class="relative mb-3">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" x-model="search" placeholder="{{ __('Temukan perusahaan...') }}"
                                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                                </div>

                                <!-- All Option -->
                                <label
                                    class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                                    <input type="checkbox" :checked="tempSelected.length === 0" @change="tempSelected = []"
                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer">
                                    <span>{{ __('Semua Perusahaan') }}</span>
                                </label>

                                <!-- Group Title -->
                                <div
                                    class="mt-3 mb-1.5 flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <span>{{ __('Pilihan Perusahaan') }}</span>
                                    <span x-show="tempSelected.length > 0" class="text-[#4fa304] font-semibold lowercase"
                                        x-text="tempSelected.length + ' {{ __('dipilih') }}'"></span>
                                </div>

                                <!-- 2-Column Grid List items (Compact & Scrollable) -->
                                <div class="max-h-44 overflow-y-auto pr-1 custom-scrollbar">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-1">
                                        @if (isset($companies))
                                            @foreach ($companies as $comp)
                                                <label
                                                    class="flex items-center gap-2.5 py-1.5 px-1.5 rounded-lg text-xs text-gray-600 hover:text-black hover:bg-gray-50 cursor-pointer transition"
                                                    x-show="!search || '{{ strtolower($comp->name) }}'.includes(search.toLowerCase())">
                                                    <input type="checkbox" value="{{ $comp->id }}"
                                                        x-model="tempSelected"
                                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer shrink-0">
                                                    <span class="truncate">{{ $comp->name }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Footer Buttons (Always Visible at Bottom) -->
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                                    <button type="button" @click="reset()"
                                        class="text-xs font-semibold text-gray-500 hover:text-[#93F514] transition">
                                        {{ __('Atur Ulang') }}
                                    </button>
                                    <button type="button" @click="apply()"
                                        class="px-5 py-2 rounded-xl bg-[#93F514] hover:bg-[#7edc0b] text-black font-bold text-xs shadow-sm transition">
                                        {{ __('Pilih') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown Semua Departemen / Fungsi -->
                        <div class="w-full lg:w-56 relative" :class="open ? 'z-50' : 'z-10'" x-data="{
                            open: false,
                            search: '',
                            selected: {{ json_encode(array_map('strval', $homeSelectedDepartments ?? [])) }},
                            tempSelected: {{ json_encode(array_map('strval', $homeSelectedDepartments ?? [])) }},
                            departmentsMap: {
                                @foreach ($departments as $dept)
                                    '{{ $dept->id }}': '{{ addslashes(__($dept->name)) }}', @endforeach
                            },
                            getDisplayText() {
                                if (!this.selected || this.selected.length === 0) return '{{ __('Semua Departemen') }}';
                                if (this.selected.length === 1) {
                                    return this.departmentsMap[this.selected[0]] || '1 {{ __('Departemen') }}';
                                }
                                return this.selected.length + ' {{ __('Departemen') }}';
                            },
                            openDropdown() {
                                this.tempSelected = [...this.selected];
                                this.search = '';
                                this.open = true;
                            },
                            reset() {
                                this.tempSelected = [];
                                this.selected = [];
                                this.open = false;
                            },
                            apply() {
                                this.selected = [...this.tempSelected];
                                this.open = false;
                            }
                        }"
                            @click.outside="open = false">

                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="department_id[]" :value="id">
                            </template>

                            <button type="button" @click="open ? open = false : openDropdown()"
                                :class="selected.length > 0 ? 'text-[#93F514] font-bold' : 'text-gray-700'"
                                class="w-full py-2.5 px-4 flex items-center justify-between text-left text-sm transition">
                                <div class="flex items-center gap-2 truncate">
                                    <template x-if="selected.length > 0">
                                        <span
                                            class="min-w-5 h-5 px-1 rounded-full bg-[#93F514] text-black font-extrabold text-xs flex items-center justify-center shrink-0 shadow-sm"
                                            x-text="selected.length">
                                            {{ count($homeSelectedDepartments ?? []) }}
                                        </span>
                                    </template>
                                    <span class="truncate" x-text="getDisplayText()">
                                        @if (!empty($homeSelectedDepartments))
                                            @if (count($homeSelectedDepartments) === 1)
                                                {{ __($departments->firstWhere('id', $homeSelectedDepartments[0])?->name ?? '1 ' . __('Departemen')) }}
                                            @else
                                                {{ count($homeSelectedDepartments) }} {{ __('Departemen') }}
                                            @endif
                                        @else
                                            {{ __('Semua Departemen') }}
                                        @endif
                                    </span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                                    :class="open ? 'rotate-180 text-[#93F514]' : (selected.length > 0 ? 'text-[#93F514]' : '')"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Popover Panel -->
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                class="absolute top-full left-0 lg:left-auto lg:right-0 mt-2 w-80 sm:w-[460px] bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 z-50 text-gray-800 flex flex-col">

                                <!-- Search Input -->
                                <div class="relative mb-3">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" x-model="search"
                                        placeholder="{{ __('Temukan fungsi / departemen...') }}"
                                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                                </div>

                                <!-- All Option -->
                                <label
                                    class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                                    <input type="checkbox" :checked="tempSelected.length === 0"
                                        @change="tempSelected = []"
                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer">
                                    <span>{{ __('Semua Departemen') }}</span>
                                </label>

                                <!-- Group Title -->
                                <div
                                    class="mt-3 mb-1.5 flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <span>{{ __('Pilihan Departemen') }}</span>
                                    <span x-show="tempSelected.length > 0" class="text-[#4fa304] font-semibold lowercase"
                                        x-text="tempSelected.length + ' {{ __('dipilih') }}'"></span>
                                </div>

                                <!-- 2-Column Grid List items (Compact & Scrollable) -->
                                <div class="max-h-44 overflow-y-auto pr-1 custom-scrollbar">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-1">
                                        @if (isset($departments))
                                            @foreach ($departments as $dept)
                                                <label
                                                    class="flex items-center gap-2.5 py-1.5 px-1.5 rounded-lg text-xs text-gray-600 hover:text-black hover:bg-gray-50 cursor-pointer transition"
                                                    x-show="!search || '{{ strtolower($dept->name) }}'.includes(search.toLowerCase())">
                                                    <input type="checkbox" value="{{ $dept->id }}"
                                                        x-model="tempSelected"
                                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer shrink-0">
                                                    <span class="truncate">{{ __($dept->name) }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Footer Buttons (Always Visible at Bottom) -->
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                                    <button type="button" @click="reset()"
                                        class="text-xs font-semibold text-gray-500 hover:text-[#93F514] transition">
                                        {{ __('Atur Ulang') }}
                                    </button>
                                    <button type="button" @click="apply()"
                                        class="px-5 py-2 rounded-xl bg-[#93F514] hover:bg-[#7edc0b] text-black font-bold text-xs shadow-sm transition">
                                        {{ __('Pilih') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown Semua Tipe (Employment Type) -->
                        <div class="w-full lg:w-48 relative" :class="open ? 'z-50' : 'z-10'" x-data="{
                            open: false,
                            search: '',
                            selected: {{ json_encode($homeSelectedTypes ?? []) }},
                            tempSelected: {{ json_encode($homeSelectedTypes ?? []) }},
                            types: ['Magang', 'Full Time', 'Part Time', 'Contract', 'Freelance', 'Remote'],
                            typesMap: {
                                'Magang': '{{ __('Magang') }}',
                                'Full Time': '{{ __('Full Time') }}',
                                'Part Time': '{{ __('Part Time') }}',
                                'Contract': '{{ __('Contract') }}',
                                'Freelance': '{{ __('Freelance') }}',
                                'Remote': '{{ __('Remote') }}'
                            },
                            getDisplayText() {
                                if (!this.selected || this.selected.length === 0) return '{{ __('Semua Tipe Pekerjaan') }}';
                                if (this.selected.length === 1) {
                                    return this.typesMap[this.selected[0]] || this.selected[0];
                                }
                                return this.selected.length + ' {{ __('Tipe Dipilih') }}';
                            },
                            openDropdown() {
                                this.tempSelected = [...this.selected];
                                this.search = '';
                                this.open = true;
                            },
                            reset() {
                                this.tempSelected = [];
                                this.selected = [];
                                this.open = false;
                            },
                            apply() {
                                this.selected = [...this.tempSelected];
                                this.open = false;
                            }
                        }"
                            @click.outside="open = false">

                            <template x-for="t in selected" :key="t">
                                <input type="hidden" name="employment_type[]" :value="t">
                            </template>

                            <button type="button" @click="open ? open = false : openDropdown()"
                                :class="selected.length > 0 ? 'text-[#93F514] font-bold' : 'text-gray-700'"
                                class="w-full py-2.5 px-4 flex items-center justify-between text-left text-sm transition">
                                <div class="flex items-center gap-2 truncate">
                                    <template x-if="selected.length > 0">
                                        <span
                                            class="min-w-5 h-5 px-1 rounded-full bg-[#93F514] text-black font-extrabold text-xs flex items-center justify-center shrink-0 shadow-sm"
                                            x-text="selected.length">
                                            {{ count($homeSelectedTypes ?? []) }}
                                        </span>
                                    </template>
                                    <span class="truncate" x-text="getDisplayText()">
                                        @if (!empty($homeSelectedTypes))
                                            @if (count($homeSelectedTypes) === 1)
                                                {{ __($homeSelectedTypes[0]) }}
                                            @else
                                                {{ count($homeSelectedTypes) }} {{ __('Tipe Dipilih') }}
                                            @endif
                                        @else
                                            {{ __('Semua Tipe Pekerjaan') }}
                                        @endif
                                    </span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                                    :class="open ? 'rotate-180 text-[#93F514]' : (selected.length > 0 ? 'text-[#93F514]' : '')"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Popover Panel -->
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                class="absolute top-full left-0 lg:left-auto lg:right-0 mt-2 w-80 sm:w-[420px] bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 z-50 text-gray-800 flex flex-col">

                                <!-- Search Input -->
                                <div class="relative mb-3">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" x-model="search"
                                        placeholder="{{ __('Temukan Tipe Pekerjaan...') }}"
                                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                                </div>

                                <!-- All Option -->
                                <label
                                    class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                                    <input type="checkbox" :checked="tempSelected.length === 0"
                                        @change="tempSelected = []"
                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer">
                                    <span>{{ __('Semua Tipe Pekerjaan') }}</span>
                                </label>

                                <!-- Group Title -->
                                <div
                                    class="mt-3 mb-1.5 flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <span>{{ __('Pilihan Tipe Pekerjaan') }}</span>
                                    <span x-show="tempSelected.length > 0" class="text-[#4fa304] font-semibold lowercase"
                                        x-text="tempSelected.length + ' {{ __('dipilih') }}'"></span>
                                </div>

                                <!-- 2-Column Grid List items (Compact & Scrollable) -->
                                <div class="max-h-44 overflow-y-auto pr-1 custom-scrollbar">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-1">
                                        <template
                                            x-for="type in types.filter(t => !search || t.toLowerCase().includes(search.toLowerCase()))"
                                            :key="type">
                                            <label
                                                class="flex items-center gap-2.5 py-1.5 px-1.5 rounded-lg text-xs text-gray-600 hover:text-black hover:bg-gray-50 cursor-pointer transition"
                                                x-show="!search || type.toLowerCase().includes(search.toLowerCase())">
                                                <input type="checkbox" :value="type" x-model="tempSelected"
                                                    class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer shrink-0">
                                                <span x-text="typesMap[type] || type" class="truncate"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <!-- Footer Buttons (Always Visible at Bottom) -->
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                                    <button type="button" @click="reset()"
                                        class="text-xs font-semibold text-gray-500 hover:text-[#93F514] transition">
                                        {{ __('Atur Ulang') }}
                                    </button>
                                    <button type="button" @click="apply()"
                                        class="px-5 py-2 rounded-xl bg-[#93F514] hover:bg-[#7edc0b] text-black font-bold text-xs shadow-sm transition">
                                        {{ __('Pilih') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Actions (Submit & Reset) -->
                        <div class="w-full lg:w-auto p-1 shrink-0 flex items-center gap-2">
                            <button type="submit"
                                class="w-full lg:w-auto py-2.5 px-6 rounded-xl lg:rounded-full bg-[#051405] hover:bg-[#93F514] text-[#EEEEEE] hover:text-black border border-[#93F514]/40 font-bold text-sm tracking-wide shadow-md shadow-black/25 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer outline-none group">
                                <svg class="w-4 h-4 text-[#93F514] group-hover:text-black transition-colors shrink-0"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span class="whitespace-nowrap">{{ __('Cari Lowongan') }}</span>
                            </button>
                            @if (request('search') ||
                                    !empty($homeSelectedCompanies) ||
                                    !empty($homeSelectedDepartments) ||
                                    !empty($homeSelectedTypes))
                                <a href="{{ route('home') }}" title="Reset Filter"
                                    class="p-2.5 rounded-xl lg:rounded-full bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-500 transition flex items-center justify-center border border-gray-200">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Popular Searches tags -->
                <div class="mt-3 sm:mt-3.5 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 text-xs text-gray-400">
                    <span class="font-bold text-[#93F514]">{{ __('Pencarian Populer:') }}</span>
                    <a href="{{ route('jobs.index', ['search' => 'Software Engineer']) }}"
                        class="px-2.5 py-0.5 rounded-full bg-[#051205] hover:bg-[#93F514]/20 border border-[#93F514]/30 text-gray-300 hover:text-[#93F514] transition text-[11px] sm:text-xs">Software
                        Engineer</a>
                    <a href="{{ route('jobs.index', ['search' => 'Staff']) }}"
                        class="px-2.5 py-0.5 rounded-full bg-[#051205] hover:bg-[#93F514]/20 border border-[#93F514]/30 text-gray-300 hover:text-[#93F514] transition text-[11px] sm:text-xs">Staff
                        Administrasi</a>
                    <a href="{{ route('jobs.index', ['search' => 'Marketing']) }}"
                        class="px-2.5 py-0.5 rounded-full bg-[#051205] hover:bg-[#93F514]/20 border border-[#93F514]/30 text-gray-300 hover:text-[#93F514] transition text-[11px] sm:text-xs">Digital
                        Marketing</a>
                    <a href="{{ route('jobs.index', ['location' => 'Remote']) }}"
                        class="px-2.5 py-0.5 rounded-full bg-[#051205] hover:bg-[#93F514]/20 border border-[#93F514]/30 text-gray-300 hover:text-[#93F514] transition text-[11px] sm:text-xs">Remote</a>
                </div>

                <!-- Quick Stats Summary -->
                <div
                    class="mt-4 sm:mt-5 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 w-full max-w-3xl border-t border-[#93F514]/20 pt-4 sm:pt-4.5">
                    <div class="reveal-on-scroll py-3 px-3.5 sm:py-3.5 sm:px-4 rounded-xl bg-gradient-to-b from-[#061506] to-[#040804] border border-[#93F514]/30 hover:border-[#93F514]/60 transition shadow-lg shadow-black/40 text-center"
                        data-delay="100">
                        <div class="text-xl sm:text-2xl font-black text-[#EEEEEE]">{{ $totalJobsCount ?? 0 }}+</div>
                        <div class="text-[11px] sm:text-xs text-[#93F514] font-semibold mt-0.5">{{ __('Lowongan Aktif') }}</div>
                    </div>
                    <div class="reveal-on-scroll py-3 px-3.5 sm:py-3.5 sm:px-4 rounded-xl bg-gradient-to-b from-[#061506] to-[#040804] border border-[#93F514]/30 hover:border-[#93F514]/60 transition shadow-lg shadow-black/40 text-center"
                        data-delay="200">
                        <div class="text-xl sm:text-2xl font-black text-[#EEEEEE]">{{ $companiesCount ?? 0 }}+</div>
                        <div class="text-[11px] sm:text-xs text-[#93F514] font-semibold mt-0.5">{{ __('Perusahaan Mitra') }}</div>
                    </div>
                    <div class="reveal-on-scroll py-3 px-3.5 sm:py-3.5 sm:px-4 rounded-xl bg-gradient-to-b from-[#061506] to-[#040804] border border-[#93F514]/30 hover:border-[#93F514]/60 transition shadow-lg shadow-black/40 text-center"
                        data-delay="300">
                        <div class="text-xl sm:text-2xl font-black text-[#EEEEEE]">{{ $departmentsCount ?? 0 }}+</div>
                        <div class="text-[11px] sm:text-xs text-[#93F514] font-semibold mt-0.5">{{ __('Departemen') }}</div>
                    </div>
                    <div class="reveal-on-scroll py-3 px-3.5 sm:py-3.5 sm:px-4 rounded-xl bg-gradient-to-b from-[#061506] to-[#040804] border border-[#93F514]/30 hover:border-[#93F514]/60 transition shadow-lg shadow-black/40 text-center"
                        data-delay="400">
                        <div class="text-xl sm:text-2xl font-black text-[#EEEEEE]">{{ $totalQuotaCount ?? 0 }}+</div>
                        <div class="text-[11px] sm:text-xs text-[#93F514] font-semibold mt-0.5">{{ __('Total Kuota Formasi') }}</div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ==================== QUICK CAREER TRACKS (EDITORIAL CARD DECK) ==================== -->
        <section class="reveal-on-scroll relative pt-8 sm:pt-12 pb-14 sm:pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto overflow-hidden">
            <!-- Header Title (Editorial Style - Light & Dark Aware) -->
            <div class="mb-10 sm:mb-14 text-center lg:text-left flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                <div>
                    <span class="text-xs uppercase tracking-[0.25em] text-[#2D6B00] dark:text-[#93F514] font-extrabold block mb-2">{{ __('EXPLORE TRACKS') }}</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black uppercase text-gray-900 dark:text-white tracking-tight">
                        {{ __('Pilihan Jalur Karir') }}
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 max-w-md font-medium leading-relaxed">
                    {{ __('Pilih jalur karir yang sesuai dengan tahap dan tujuan profesional Anda saat ini.') }}
                </p>
            </div>

            <!-- 4 Cards Deck Grid with 3D Flip Card Effect (Alpine.js & Tailwind CSS) -->
            @include('frontend.components.career-paths-deck')
        </section>

        <!-- ==================== ALUR PENDAFTARAN & SELEKSI (INTERACTIVE PIPELINE) ==================== -->
        <section id="alur-pendaftaran"
            class="reveal-on-scroll pt-14 sm:pt-20 pb-8 sm:pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto relative border-t border-gray-200/80 dark:border-white/[0.08]"
            x-data="{ activeStep: 1 }">

            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-10">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    {{ __('Alur Pendaftaran & Seleksi') }}</h2>
                <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-zinc-400 leading-relaxed max-w-2xl mx-auto">
                    {{ __('Ikuti 6 tahapan sistematis dan transparan untuk bergabung menjadi bagian dari talenta terbaik PT Mitra Karya Analitika (MIKA).') }}
                </p>
            </div>

            <!-- Connected Interactive Pipeline Stepper Bar -->
            <div class="mb-8">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 sm:gap-3">
                    <template
                        x-for="step in [
                        { num: 1, id: '01', title: '{{ __('Registrasi') }}', sub: '{{ __('Buat Akun Pelamar') }}' },
                        { num: 2, id: '02', title: '{{ __('Profil & CV') }}', sub: '{{ __('Lengkapi Berkas') }}' },
                        { num: 3, id: '03', title: '{{ __('Seleksi Berkas') }}', sub: '{{ __('Screening HRD') }}' },
                        { num: 4, id: '04', title: '{{ __('Asesmen CBT') }}', sub: '{{ __('Ujian & Tes DISC') }}' },
                        { num: 5, id: '05', title: '{{ __('Wawancara') }}', sub: '{{ __('HRD & User') }}' },
                        { num: 6, id: '06', title: '{{ __('Offering') }}', sub: '{{ __('Hasil & Kontrak') }}' }
                    ]"
                        :key="step.num">
                        <button @click="activeStep = step.num" type="button"
                            class="group relative text-left p-3.5 rounded-2xl border transition-all duration-200 cursor-pointer overflow-hidden"
                            :class="activeStep === step.num ?
                                'bg-white dark:bg-white/[0.08] border-[#3b8004] dark:border-[#93F514] shadow-sm' :
                                'bg-white dark:bg-white/[0.02] hover:bg-gray-50 dark:hover:bg-white/[0.05] border-gray-200 dark:border-white/[0.06] hover:border-gray-300 dark:hover:border-white/15 shadow-sm dark:shadow-none'">

                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-extrabold tracking-wider transition-colors"
                                    :class="activeStep === step.num ? 'text-[#3b8004] dark:text-[#93F514]' :
                                        'text-gray-400 dark:text-zinc-500 group-hover:text-gray-600 dark:group-hover:text-zinc-400'"
                                    x-text="step.id"></span>
                                <span class="w-2 h-2 rounded-full transition-colors"
                                    :class="activeStep === step.num ? 'bg-[#4fa304] dark:bg-[#93F514]' :
                                        'bg-gray-300 dark:bg-zinc-700'"></span>
                            </div>
                            <div class="text-xs sm:text-sm font-bold truncate transition-colors"
                                :class="activeStep === step.num ? 'text-gray-900 dark:text-white' :
                                    'text-gray-700 dark:text-zinc-300 group-hover:text-gray-900 dark:group-hover:text-white'"
                                x-text="step.title"></div>
                            <div class="text-[11px] font-medium truncate mt-0.5 transition-colors"
                                :class="activeStep === step.num ? 'text-gray-600 dark:text-zinc-400' :
                                    'text-gray-500 dark:text-zinc-500'"
                                x-text="step.sub"></div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Active Stage Detail Display Canvas -->
            <div
                class="relative rounded-3xl bg-white dark:bg-gradient-to-b dark:from-[#0a120a] dark:via-[#050905] dark:to-[#040604] border border-gray-200/90 dark:border-white/[0.08] p-6 sm:p-10 lg:p-12 shadow-xl shadow-gray-200/60 dark:shadow-2xl dark:shadow-black/80 overflow-hidden">

                <!-- ================= Step 1: Registrasi Pengguna ================= -->
                <div x-show="activeStep === 1" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <div class="lg:col-span-7 space-y-6">
                        <div class="space-y-3">
                            <div
                                class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>{{ __('Tahap 01 — Registrasi Akun') }}</span>
                            </div>
                            <h3
                                class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                {{ __('Buat Akun Pelamar dengan Cepat & Aman') }}
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                {{ __('Lakukan registrasi menggunakan Google Authentication atau Form Manual (Nama Lengkap, NIK KTP valid, dan Email aktif). Akun ini menjadi pusat portal seluruh aktivitas pelamaran, verifikasi dokumen, ujian seleksi, hingga penawaran kerja.') }}
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Autentikasi Cepat') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Google OAuth atau form NIK terverifikasi.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Akses Fleksibel') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Responsif 24/7 di smartphone & desktop.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Pemberitahuan') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Status seleksi langsung ke portal akun.') }}</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            @auth
                                @php
                                    $isAdminOrRecruiter =
                                        auth()->user()->role_id == 1 ||
                                        auth()->user()->role_id == 2 ||
                                        in_array(strtolower(auth()->user()->role?->name ?? ''), [
                                            'admin',
                                            'superadmin',
                                            'recruiter',
                                        ]);
                                    $dashRoute = $isAdminOrRecruiter
                                        ? (auth()->user()->role_id == 2 ||
                                        strtolower(auth()->user()->role?->name ?? '') === 'recruiter'
                                            ? route('recruiter.dashboard')
                                            : route('admin.dashboard'))
                                        : route('profile');
                                @endphp
                                <a href="{{ $dashRoute }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>{{ $isAdminOrRecruiter ? __('Buka Dashboard Manajemen') : __('Buka Profil Saya') }}</span>
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                    class="inline-flex items-center gap-3 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25 group">
                                    <span>{{ __('Daftar Akun Sekarang') }}</span>
                                    <span
                                        class="w-5 h-5 rounded-md bg-black/10 flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </span>
                                </a>
                            @endauth
                            <button type="button" @click="activeStep = 2"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                <span>{{ __('Lanjut: Profil & CV') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div
                            class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">{{ __('Ketentuan Tahap 01') }}</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ __('Tips Registrasi Akun') }}</h4>
                                <div
                                    class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    {{ __('Pastikan nomor NIK KTP dan alamat email yang didaftarkan aktif dan valid untuk memastikan kelancaran verifikasi identitas serta notifikasi status seleksi.') }}
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>{{ __('Data terlindungi dengan enkripsi keamanan portal resmi MIKA') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= Step 2: Kelengkapan Profil & CV ================= -->
                <div x-show="activeStep === 2" x-cloak x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <div class="lg:col-span-7 space-y-6">
                        <div class="space-y-3">
                            <div
                                class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>{{ __('Tahap 02 — Kelengkapan Profil & CV') }}</span>
                            </div>
                            <h3
                                class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                {{ __('Lengkapi Biodata, Riwayat & CV Digital') }}
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                {{ __('Lengkapi profil Anda secara menyeluruh: Biodata Pribadi, Kontak Keluarga, Riwayat Pendidikan, Pengalaman Kerja, Organisasi, Prestasi, hingga Keahlian/Sertifikasi. Sistem secara otomatis menyusun data Anda menjadi format CV profesional.') }}
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>ATS-Friendly</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Generate & preview CV digital terstandar.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Update Fleksibel') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Pembaruan data dapat dilakukan berkala.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Unggah Dokumen') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Ijazah, transkrip nilai, & sertifikat.') }}</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('profile') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>{{ __('Lengkapi Profil Sekarang') }}</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>{{ __('Masuk untuk Lengkapi Profil') }}</span>
                                </a>
                            @endauth
                            <button type="button" @click="activeStep = 3"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                <span>{{ __('Lanjut: Lamar & Seleksi') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div
                            class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">{{ __('Ketentuan Tahap 02') }}</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ __('Prioritas Screening') }}</h4>
                                <div
                                    class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    {{ __('Profil pelamar dengan persentase kelengkapan data di atas 85% memiliki prioritas lebih tinggi dalam peninjauan administrasi oleh tim HRD.') }}
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>{{ __('Format resume disinkronkan otomatis ke database rekruter') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= Step 3: Lamar Posisi & Seleksi Berkas ================= -->
                <div x-show="activeStep === 3" x-cloak x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <div class="lg:col-span-7 space-y-6">
                        <div class="space-y-3">
                            <div
                                class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>{{ __('Tahap 03 — Seleksi Berkas') }}</span>
                            </div>
                            <h3
                                class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                {{ __('Eksplorasi Lowongan & Seleksi Berkas') }}
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                {{ __('Temukan posisi karir yang sesuai dengan kompetensi Anda di PT Mitra Karya Analitika (MIKA) atau grup bisnis kami. Ajukan lamaran dalam satu klik, lalu tim HRD & Rekruter akan meninjau kualifikasi dan kesesuaian berkas Anda secara transparan.') }}
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>One-Click Apply</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Lamaran praktis dengan profil terintegrasi.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Pelacakan Status') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Update real-time (Terkirim, Review, Lolos).') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Notifikasi Hasil') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Kelulusan administrasi sebelum ke tahap ujian.') }}</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            <a href="{{ route('jobs.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                <span>{{ __('Jelajahi Lowongan Tersedia') }}</span>
                            </a>
                            <button type="button" @click="activeStep = 4"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                <span>{{ __('Lanjut: Asesmen CBT') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div
                            class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">{{ __('Ketentuan Tahap 03') }}</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ __('Seleksi Administrasi') }}</h4>
                                <div
                                    class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    {{ __('Tim Rekruter akan mencocokkan latar belakang pendidikan, kompetensi, dan pengalaman kerja Anda dengan kualifikasi formasi yang dilamar.') }}
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ __('Transparan tanpa perantara atau pungutan biaya') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= Step 4: Ujian Asesmen Online (CBT & DISC) ================= -->
                <div x-show="activeStep === 4" x-cloak x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <div class="lg:col-span-7 space-y-6">
                        <div class="space-y-3">
                            <div
                                class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>{{ __('Tahap 04 — Ujian Asesmen Online') }}</span>
                            </div>
                            <h3
                                class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                {{ __('Ikuti Ujian CBT & Tes Kepribadian DISC') }}
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                {{ __('Setelah dinyatakan lolos berkas administrasi, Anda dapat langsung mengakses sistem Ujian Online terintegrasi. Ujian meliputi Tes Kepribadian DISC (24 kuadran Most/Least) serta Tes Kompetensi Teknis (Pilihan Ganda & Soal Essay + Lampiran Berkas).') }}
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('CBT Terpadu') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Timer real-time dan evaluasi terstandar.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Tes DISC') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Kalkulasi otomatis profil kepribadian kerja.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Auto-Save Aman') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Penyimpanan jawaban berkala anti hilang.') }}</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('profile', ['tab' => 'riwayat']) }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>{{ __('Cek Status Ujian di Riwayat') }}</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>{{ __('Masuk ke Portal Ujian') }}</span>
                                </a>
                            @endauth
                            <button type="button" @click="activeStep = 5"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                <span>{{ __('Lanjut: Wawancara') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div
                            class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">{{ __('Ketentuan Tahap 04') }}</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ __('Persiapan Ujian') }}
                                </h4>
                                <div
                                    class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    {{ __('Gunakan perangkat komputer/laptop dengan koneksi internet yang stabil untuk pengalaman optimal saat menyelesaikan tes DISC dan ujian teknis.') }}
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <span>{{ __('Hasil terkomputasi langsung ke dashboard seleksi rekruter') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= Step 5: Sesi Wawancara Terjadwal ================= -->
                <div x-show="activeStep === 5" x-cloak x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <div class="lg:col-span-7 space-y-6">
                        <div class="space-y-3">
                            <div
                                class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>{{ __('Tahap 05 — Sesi Wawancara') }}</span>
                            </div>
                            <h3
                                class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                {{ __('Sesi Interview Daring atau Tatap Muka') }}
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                {{ __('Kandidat yang lolos tahap ujian asesmen (Shortlisted) akan dijadwalkan untuk sesi wawancara mendalam bersama tim HRD dan User. Informasi jadwal, pewawancara, serta tautan video conference (Google Meet) atau lokasi kantor ditampilkan langsung pada dashboard Anda.') }}
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Jadwal Terintegrasi') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Pewawancara dan waktu (WIB) tertera jelas.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Link Meet Langsung') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Akses video call dari kartu riwayat lamaran.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Pengingat Otomatis') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Notifikasi terjadwal di portal akun Anda.') }}</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('profile', ['tab' => 'riwayat']) }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>{{ __('Pantau Jadwal Wawancara') }}</span>
                                </a>
                            @else
                                <a href="{{ route('jobs.index') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>{{ __('Lihat Peluang Karir') }}</span>
                                </a>
                            @endauth
                            <button type="button" @click="activeStep = 6"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                <span>{{ __('Lanjut: Hasil Akhir') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div
                            class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">{{ __('Ketentuan Tahap 05') }}</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ __('Persiapan Wawancara') }}</h4>
                                <div
                                    class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    {{ __('Pelajari profil dan nilai inti MIKA (Menghargai, Integritas, Komitmen, Akuntabel), kenali tanggung jawab posisi, dan siapkan perangkat kamera/audio jika sesi berlangsung secara daring.') }}
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>{{ __('Sesi wawancara berlangsung profesional & solutif') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= Step 6: Hasil Akhir & Penawaran Karir ================= -->
                <div x-show="activeStep === 6" x-cloak x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <div class="lg:col-span-7 space-y-6">
                        <div class="space-y-3">
                            <div
                                class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>{{ __('Tahap 06 — Hasil Akhir & Penawaran') }}</span>
                            </div>
                            <h3
                                class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                {{ __('Pengumuman Kelulusan & Penawaran Kerja') }}
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                {{ __('Kandidat terbaik yang terpilih akan menerima pemberitahuan kelulusan resmi dengan status Diterima (Accepted). Tim MIKA akan menerbitkan surat penawaran kerja (*Offering Letter*) resmi beserta panduan onboarding kerja.') }}
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Hasil Transparan') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Status resmi langsung tertera di akun Anda.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('Offering Letter') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Surat penawaran resmi beserta paket benefit.') }}</p>
                            </div>
                            <div
                                class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>{{ __('100% Gratis') }}</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">
                                    {{ __('Tanpa pungutan biaya apapun dalam proses.') }}</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('profile', ['tab' => 'riwayat']) }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>{{ __('Lihat Riwayat & Status Saya') }}</span>
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                    class="inline-flex items-center gap-3 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25 group">
                                    <span>{{ __('Daftar & Raih Karir Impian') }}</span>
                                    <span
                                        class="w-5 h-5 rounded-md bg-black/10 flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </span>
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div
                            class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">{{ __('Ketentuan Tahap 06') }}</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ __('Selamat Bergabung!') }}</h4>
                                <div
                                    class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    {{ __('Siapkan diri Anda untuk melangkah ke babak baru perjalanan karir profesional masa depan bersama PT Mitra Karya Analitika (MIKA).') }}
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                <span>{{ __('Onboarding terstruktur & pendampingan tim MIKA') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ==================== TEMUKAN LOWONGAN BERDASARKAN PERUSAHAAN ==================== -->
        @if (isset($companies) && $companies->count() > 0)
            @php
                $orderedCompanies = $companies->sortBy(function ($comp) {
                    $name = strtolower($comp->name);
                    return (str_contains($name, 'mitra karya analitika') || str_contains($name, 'mika')) ? 0 : 1;
                })->values();
            @endphp
            <section id="perusahaan"
                class="reveal-on-scroll pt-8 sm:pt-10 pb-14 sm:pb-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto relative border-t border-[#93F514]/20">
                <!-- Header Rata Tengah -->
                <div class="text-center max-w-3xl mx-auto mb-6 sm:mb-8">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-[#EEEEEE]">
                        {{ __('Temukan Lowongan Berdasarkan') }} <span class="text-[#93F514]">{{ __('Perusahaan') }}</span>
                    </h2>
                    <p class="mt-2.5 text-sm sm:text-base text-gray-300 leading-relaxed">
                        {{ __('Pilih perusahaan mitra untuk melihat lowongan kerja dan karir yang sedang dibuka.') }}
                    </p>
                </div>

                <!-- Card Perusahaan (Rata Tengah Dinamis dengan 3D Flip & Auto-Scroll Gambar - Ukuran Compact) -->
                <div class="flex flex-wrap justify-center gap-4 sm:gap-5">
                    @foreach ($orderedCompanies as $comp)
                        @php
                            $jobCount = $comp->jobs_count ?? $comp->jobs()->where('status', 'Open')->count();
                            $images = $comp->card_images ?? [];
                        @endphp
                        <div x-data="{
                                flipped: false,
                                current: 0,
                                total: {{ count($images) }},
                                timer: null,
                                startSlide() {
                                    if (this.total > 1 && !this.timer) {
                                        this.timer = setInterval(() => {
                                            this.current = (this.current + 1) % this.total;
                                        }, {{ 3200 + ($loop->index % 3) * 600 }});
                                    }
                                },
                                stopSlide() {
                                    if (this.timer) {
                                        clearInterval(this.timer);
                                        this.timer = null;
                                    }
                                }
                            }"
                            x-init="startSlide()"
                            @mouseenter="stopSlide()"
                            @mouseleave="startSlide()"
                            @click.away="flipped = false"
                            class="reveal-on-scroll company-flip-wrap group relative w-full sm:w-[calc(50%-10px)] md:w-[285px] lg:w-[300px] max-w-[310px] h-[330px] sm:h-[345px]"
                            :class="{ 'is-flipped': flipped }"
                            data-delay="{{ ($loop->index % 4) * 100 }}">

                            <div class="company-flip-inner">
                                <!-- ================= SISI DEPAN (FRONT - COMPACT) ================= -->
                                <div class="company-card-face company-face-front rounded-2xl p-4 border flex flex-col justify-between
                                            bg-gradient-to-b from-[#061506] to-[#030a03] border-[#93F514]/25 shadow-md group-hover:border-[#93F514] transition-colors cursor-pointer"
                                     @click="if (window.innerWidth < 768) { flipped = !flipped; }">

                                    <!-- Header: Logo + Nama & Lokasi Perusahaan -->
                                    <div class="flex items-center gap-2.5">
                                        @if ($comp->logo_url)
                                            <div class="w-10 h-10 rounded-xl bg-white border border-[#93F514]/30 p-1.5 flex items-center justify-center shrink-0 overflow-hidden shadow-sm group-hover:border-[#93F514] transition-colors">
                                                <img src="{{ $comp->logo_url }}" alt="{{ $comp->name }}" class="w-full h-full object-contain">
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-white border border-[#93F514]/40 flex items-center justify-center text-gray-900 font-black text-xs shrink-0 group-hover:bg-gray-100 transition-colors">
                                                {{ strtoupper(substr($comp->name, 0, 2)) }}
                                            </div>
                                        @endif

                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xs sm:text-sm font-extrabold text-[#EEEEEE] group-hover:text-[#93F514] transition-colors tracking-tight truncate uppercase">
                                                {{ $comp->name }}
                                            </h3>
                                            <p class="text-[11px] text-gray-400 truncate font-medium">
                                                {{ $comp->city ?? ($comp->province ?? __('Mitra Perusahaan')) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Bagian Tengah: Gambar Otomatis Scroll (Tampil Utuh Tanpa Terpotong) -->
                                    <div class="relative w-full h-36 sm:h-40 rounded-xl overflow-hidden bg-black/80 border border-white/5 my-2">
                                        <div class="flex h-full transition-transform duration-700 ease-out"
                                             :style="`transform: translateX(-${current * 100}%);`">
                                            @foreach ($images as $img)
                                                <div class="w-full h-full flex-shrink-0 relative overflow-hidden flex items-center justify-center bg-black/90">
                                                    <!-- Background blur ambient agar sisi samping menyatu indah -->
                                                    <img src="{{ $img }}" alt="" class="absolute inset-0 w-full h-full object-cover filter blur-md opacity-35 scale-110 pointer-events-none [transform:translateZ(0)]" aria-hidden="true">
                                                    <!-- Foto utuh 100% tanpa terpotong (object-contain) -->
                                                    <img src="{{ $img }}" alt="{{ $comp->name }}" class="relative z-10 max-w-full max-h-full object-contain drop-shadow-md">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none z-20"></div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if (count($images) > 1)
                                            <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1.5 z-30 pointer-events-none">
                                                @foreach ($images as $idx => $img)
                                                    <span class="h-1 rounded-full transition-all duration-300"
                                                          :class="current === {{ $idx }} ? 'w-4 bg-[#93F514]' : 'w-1.5 bg-white/40'"></span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Footer: Lowongan Tersedia Badge -->
                                    <div class="pt-2 border-t border-[#93F514]/15 flex items-center justify-between">
                                        <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs {{ $jobCount > 0 ? 'text-[#93F514] font-bold' : 'text-gray-400' }}">
                                            <span>{{ $jobCount }} {{ __('Lowongan Tersedia') }}</span>
                                        </span>

                                        <span class="text-xs text-gray-400 group-hover:text-[#93F514] group-hover:translate-x-0.5 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <!-- ================= SISI BELAKANG (BACK - COMPACT 180 DERAJAT) ================= -->
                                <div class="company-card-face company-face-back rounded-2xl p-4 sm:p-4.5 flex flex-col justify-between bg-[#93F514] border border-[#82de0e] text-neutral-950 shadow-xl">
                                    <!-- Informasi Perusahaan -->
                                    <div class="flex flex-col">
                                        <h3 class="text-xs sm:text-sm font-black text-black uppercase tracking-tight leading-snug line-clamp-1">
                                            {{ $comp->name }}
                                        </h3>
                                        <p class="text-[11px] sm:text-xs text-neutral-800 mt-1 font-medium leading-relaxed line-clamp-2">
                                            {{ $comp->city ?? 'Semarang' }}. {{ $comp->tagline ?? ($comp->about ? Str::limit(strip_tags($comp->about), 60) : __('Perusahaan mitra resmi MIKA Career.')) }}
                                        </p>
                                        <div class="mt-1.5">
                                            <span class="company-back-jobcount text-[11px] sm:text-xs font-black text-black">
                                                {{ $jobCount }} {{ __('lowongan tersedia') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Tombol Aksi (Pill Sesuai Referensi Gambar 3 - Ukuran Compact) -->
                                    <div class="flex flex-col gap-2 mt-auto pt-2">
                                        @if ($comp->website)
                                            <a href="{{ $comp->website }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               @click.stop
                                               class="w-full py-1.5 sm:py-2 px-3 rounded-full bg-black text-[#93F514] hover:bg-neutral-900 font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm hover:scale-[1.02] active:scale-95">
                                                <span>{{ __('Kunjungi website') }}</span>
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7M17 7H7M17 7V17" />
                                                </svg>
                                            </a>
                                        @else
                                            <a href="{{ route('jobs.index', ['company_id' => [$comp->id]]) }}"
                                               @click.stop
                                               class="w-full py-1.5 sm:py-2 px-3 rounded-full bg-black text-[#93F514] hover:bg-neutral-900 font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm hover:scale-[1.02] active:scale-95">
                                                <span>{{ __('Kunjungi website') }}</span>
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7M17 7H7M17 7V17" />
                                                </svg>
                                            </a>
                                        @endif

                                        <a href="{{ route('jobs.index', ['company_id' => [$comp->id]]) }}"
                                           @click.stop
                                           class="w-full py-1.5 sm:py-2 px-3 rounded-full border-2 border-black text-black hover:bg-black/10 font-black text-xs flex items-center justify-center gap-1.5 transition-all hover:scale-[1.02] active:scale-95">
                                            <span>{{ __('Lihat lowongan') }}</span>
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7M17 7H7M17 7V17" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- ==================== PATTA-STYLE CURVED MOVING GALLERY (DYNAMIC TITLE PER PHOTO) ==================== -->
        @php
            $galleryItems = [];

            if (!empty($showcases)) {
                // Collect images per showcase
                $showcaseMap = [];
                foreach ($showcases as $sc) {
                    $tag = $sc['tag'] ?? __('KEHIDUPAN & BUDAYA KERJA');
                    $title = $sc['title'] ?? __('Ruang untuk Bertumbuh dan Berkembang');
                    $desc = $sc['description'] ?? '';

                    $imgs = [];
                    if (!empty($sc['img1'])) $imgs[] = $sc['img1'];
                    if (!empty($sc['img2'])) $imgs[] = $sc['img2'];
                    if (!empty($sc['img3'])) $imgs[] = $sc['img3'];

                    if (!empty($imgs)) {
                        $showcaseMap[] = [
                            'tag' => $tag,
                            'title' => $title,
                            'description' => $desc,
                            'images' => $imgs,
                        ];
                    }
                }

                // Interleave images across showcases so consecutive cards have different titles
                $maxImgs = 0;
                foreach ($showcaseMap as $sm) {
                    $maxImgs = max($maxImgs, count($sm['images']));
                }

                for ($imgIndex = 0; $imgIndex < $maxImgs; $imgIndex++) {
                    foreach ($showcaseMap as $sm) {
                        if (isset($sm['images'][$imgIndex])) {
                            $galleryItems[] = [
                                'tag' => $sm['tag'],
                                'title' => $sm['title'],
                                'description' => $sm['description'],
                                'img' => $sm['images'][$imgIndex],
                            ];
                        }
                    }
                }
            }

            // Fallback items if database showcases are empty or few
            $fallbacks = [
                [
                    'tag' => __('KEGIATAN & KEBERSAMAAN'),
                    'title' => __('Outbound & Sinergi Karyawan'),
                    'description' => __('Membangun kebersamaan, kekompakan, dan kerjasama tim yang solid melalui program employee gathering serta outbound berkala.'),
                    'img' => asset('storage/asset-compro/outbond.jpg'),
                ],
                [
                    'tag' => __('HUT PERUSAHAAN'),
                    'title' => __('Perayaan Hari Jadi MIKA Group'),
                    'description' => __('Merayakan setiap pencapaian dan perjalanan bertumbuh bersama seluruh keluarga besar PT Mitra Karya Analitika.'),
                    'img' => asset('storage/asset-compro/aniv1.jpg'),
                ],
                [
                    'tag' => __('EVENT & KONFERENSI'),
                    'title' => __('Partisipasi Aktif Event HISFARIN'),
                    'description' => __('Memperluas jejaring profesional dan memperkenalkan inovasi teknologi analitik terkini kepada mitra industri terpercaya.'),
                    'img' => asset('storage/asset-compro/hisfarin1.jpg'),
                ],
                [
                    'tag' => __('BIMBINGAN TEKNIS'),
                    'title' => __('Workshop & Sinergi ASPADIN'),
                    'description' => __('Menghadirkan bimbingan teknis, transfer ilmu terapan, dan inovasi IoT untuk memajukan kapabilitas industri nasional.'),
                    'img' => asset('storage/asset-compro/aspadin1.jpg'),
                ],
            ];

            if (empty($galleryItems)) {
                $galleryItems = $fallbacks;
            } elseif (count($galleryItems) < 6) {
                $galleryItems = array_merge($galleryItems, $fallbacks);
            }

            // Ensure unique images
            $uniqueMap = [];
            $curvedPhotos = [];
            foreach ($galleryItems as $item) {
                if (!isset($uniqueMap[$item['img']])) {
                    $uniqueMap[$item['img']] = true;
                    $curvedPhotos[] = $item;
                }
            }

            // Initial item for initial render
            $initialItem = $curvedPhotos[0];
        @endphp

        @if (!empty($curvedPhotos))
            <section class="reveal-on-scroll relative py-10 sm:py-16 overflow-hidden w-full select-none" id="curved-gallery-section">
                <!-- Header (Changes dynamically as photos glide through the center apex) -->
                <div id="curved-gallery-header" class="max-w-3xl mx-auto px-4 text-center mb-6 sm:mb-9 transition-all duration-300 ease-out">
                    <span id="gallery-header-tag" class="inline-block text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] text-[#3b8004] dark:text-[#93F514] mb-2">
                        {{ $initialItem['tag'] }}
                    </span>
                    <h2 id="gallery-header-title" class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-snug">
                        {{ $initialItem['title'] }}
                    </h2>
                    <p id="gallery-header-desc" class="mt-2.5 sm:mt-3 text-xs sm:text-sm text-gray-600 dark:text-gray-300 max-w-xl mx-auto leading-relaxed font-normal min-h-[36px] sm:min-h-[44px]">
                        {{ $initialItem['description'] }}
                    </p>
                </div>

                <!-- Arched Panoramic Curved Gallery Container -->
                <div id="curved-gallery-viewport" class="relative w-full overflow-hidden py-6 sm:py-10 [mask-image:linear-gradient(to_right,transparent,black_5%,black_95%,transparent)] [-webkit-mask-image:linear-gradient(to_right,transparent,black_5%,black_95%,transparent)]">
                    <div id="curved-gallery-track" class="flex items-center gap-4 sm:gap-6 w-max cursor-grab active:cursor-grabbing will-change-transform">
                        {{-- First Loop of Photos --}}
                        @foreach ($curvedPhotos as $photo)
                            <div class="curved-gallery-card shrink-0 w-64 sm:w-80 md:w-96 aspect-[16/10] rounded-xl sm:rounded-2xl overflow-hidden border border-gray-200 dark:border-white/10 bg-gray-100 dark:bg-white/5 shadow-md group transition-all duration-150 cursor-pointer"
                                 data-tag="{{ $photo['tag'] }}"
                                 data-title="{{ $photo['title'] }}"
                                 data-description="{{ $photo['description'] }}">
                                <img src="{{ $photo['img'] }}" 
                                     alt="{{ $photo['title'] }}" 
                                     loading="lazy"
                                     draggable="false"
                                     class="w-full h-full object-cover object-center pointer-events-none group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @endforeach

                        {{-- Second Duplicate Loop for Infinite Wrap --}}
                        @foreach ($curvedPhotos as $photo)
                            <div class="curved-gallery-card shrink-0 w-64 sm:w-80 md:w-96 aspect-[16/10] rounded-xl sm:rounded-2xl overflow-hidden border border-gray-200 dark:border-white/10 bg-gray-100 dark:bg-white/5 shadow-md group transition-all duration-150 cursor-pointer"
                                 aria-hidden="true"
                                 data-tag="{{ $photo['tag'] }}"
                                 data-title="{{ $photo['title'] }}"
                                 data-description="{{ $photo['description'] }}">
                                <img src="{{ $photo['img'] }}" 
                                     alt="{{ $photo['title'] }}" 
                                     loading="lazy"
                                     draggable="false"
                                     class="w-full h-full object-cover object-center pointer-events-none group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const viewport = document.getElementById('curved-gallery-viewport');
                    const track = document.getElementById('curved-gallery-track');
                    const header = document.getElementById('curved-gallery-header');
                    const tagEl = document.getElementById('gallery-header-tag');
                    const titleEl = document.getElementById('gallery-header-title');
                    const descEl = document.getElementById('gallery-header-desc');

                    if (!viewport || !track) return;

                    const cards = track.querySelectorAll('.curved-gallery-card');
                    if (!cards.length) return;

                    const uniqueCount = {{ count($curvedPhotos) }};
                    let cardWidth = cards[0].offsetWidth || 340;
                    let cardGap = 24;
                    let stride = cardWidth + cardGap;
                    let halfTrackWidth = uniqueCount * stride;

                    let currentOffset = 0;
                    let autoSpeed = 0.45; // Calm, relaxed auto-scroll speed
                    let isHovered = false;
                    let isDragging = false;
                    let startX = 0;
                    let dragStartOffset = 0;
                    let dragDeltaX = 0;
                    let animFrameId = null;

                    // Header title tracking
                    let currentActiveTitle = "{{ addslashes($initialItem['title']) }}";
                    let isTextTransitioning = false;

                    function recalculateMetrics() {
                        if (!cards[0]) return;
                        cardWidth = cards[0].offsetWidth || 340;
                        const style = window.getComputedStyle(track);
                        cardGap = parseFloat(style.gap) || (window.innerWidth < 640 ? 16 : 24);
                        stride = cardWidth + cardGap;
                        halfTrackWidth = uniqueCount * stride;
                    }

                    recalculateMetrics();
                    window.addEventListener('resize', recalculateMetrics, { passive: true });

                    // Respect reduced motion
                    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                    function updateHeaderContent(tag, title, desc) {
                        if (!header || !tagEl || !titleEl || !descEl) return;
                        if (isTextTransitioning || currentActiveTitle === title) return;

                        isTextTransitioning = true;
                        currentActiveTitle = title;

                        // Smooth crossfade out
                        header.classList.add('opacity-0', '-translate-y-1.5');

                        setTimeout(() => {
                            tagEl.textContent = tag;
                            titleEl.textContent = title;
                            descEl.textContent = desc;

                            // Smooth crossfade in
                            header.classList.remove('opacity-0', '-translate-y-1.5');

                            setTimeout(() => {
                                isTextTransitioning = false;
                            }, 250);
                        }, 180);
                    }

                    function updateCardTransforms() {
                        const viewportRect = viewport.getBoundingClientRect();
                        const centerX = viewportRect.width / 2;
                        const maxDist = viewportRect.width / 2;

                        // Wrap currentOffset seamlessly
                        if (halfTrackWidth > 0) {
                            while (currentOffset <= -halfTrackWidth) {
                                currentOffset += halfTrackWidth;
                            }
                            while (currentOffset > 0) {
                                currentOffset -= halfTrackWidth;
                            }
                        }

                        track.style.transform = `translate3d(${currentOffset}px, 0, 0)`;

                        let closestCard = null;
                        let minAbsDist = Infinity;

                        // Update parabolic arch for each card
                        for (let i = 0; i < cards.length; i++) {
                            const card = cards[i];
                            const cardCenterX = currentOffset + (i * stride) + (cardWidth / 2);
                            const normDist = (cardCenterX - centerX) / (maxDist || 1);
                            const absNormDist = Math.abs(normDist);

                            // Find center card
                            if (absNormDist < minAbsDist) {
                                minAbsDist = absNormDist;
                                closestCard = card;
                            }

                            // Parabola: 0 at center, drops downward towards outer edges (tuned for landscape)
                            const translateY = Math.min(45, Math.pow(normDist, 2) * 26);
                            // Tangent rotation: tilts inward to form the arch curve (tuned for landscape)
                            const rotate = Math.max(-8, Math.min(8, normDist * 5));
                            // Subtle scale for 3D depth
                            const scale = Math.max(0.94, 1 - Math.min(0.06, absNormDist * 0.04));

                            card.style.transform = `translate3d(0, ${translateY.toFixed(2)}px, 0) rotate(${rotate.toFixed(2)}deg) scale(${scale.toFixed(3)})`;

                            // Highlight center card subtly
                            if (absNormDist < 0.28) {
                                card.classList.add('ring-2', 'ring-[#93F514]', 'shadow-2xl');
                            } else {
                                card.classList.remove('ring-2', 'ring-[#93F514]', 'shadow-2xl');
                            }
                        }

                        // Update header title based on center card
                        if (closestCard && minAbsDist < 0.35) {
                            const newTag = closestCard.dataset.tag;
                            const newTitle = closestCard.dataset.title;
                            const newDesc = closestCard.dataset.description;

                            if (newTitle && newTitle !== currentActiveTitle) {
                                updateHeaderContent(newTag, newTitle, newDesc);
                            }
                        }
                    }

                    function animateLoop() {
                        if (!isDragging && !isHovered && !prefersReducedMotion) {
                            currentOffset -= autoSpeed;
                        }
                        updateCardTransforms();
                        animFrameId = requestAnimationFrame(animateLoop);
                    }

                    // Start loop
                    animFrameId = requestAnimationFrame(animateLoop);

                    // Hover to pause
                    viewport.addEventListener('mouseenter', () => { isHovered = true; });
                    viewport.addEventListener('mouseleave', () => { 
                        isHovered = false; 
                        if (isDragging) isDragging = false;
                    });

                    // Drag interactions (mouse)
                    viewport.addEventListener('mousedown', (e) => {
                        isDragging = true;
                        startX = e.clientX;
                        dragStartOffset = currentOffset;
                        dragDeltaX = 0;
                        e.preventDefault();
                    });

                    window.addEventListener('mousemove', (e) => {
                        if (!isDragging) return;
                        dragDeltaX = Math.abs(e.clientX - startX);
                        const delta = e.clientX - startX;
                        currentOffset = dragStartOffset + delta;
                    });

                    window.addEventListener('mouseup', () => {
                        if (isDragging) isDragging = false;
                    });

                    // Touch interactions (mobile)
                    viewport.addEventListener('touchstart', (e) => {
                        if (e.touches.length === 1) {
                            isDragging = true;
                            startX = e.touches[0].clientX;
                            dragStartOffset = currentOffset;
                            dragDeltaX = 0;
                        }
                    }, { passive: true });

                    viewport.addEventListener('touchmove', (e) => {
                        if (!isDragging || e.touches.length !== 1) return;
                        dragDeltaX = Math.abs(e.touches[0].clientX - startX);
                        const delta = e.touches[0].clientX - startX;
                        currentOffset = dragStartOffset + delta;
                    }, { passive: true });

                    viewport.addEventListener('touchend', () => {
                        isDragging = false;
                    });

                    // Click card to center it smoothly
                    cards.forEach((card, idx) => {
                        card.addEventListener('click', () => {
                            if (dragDeltaX > 8) return; // Prevent triggering click after drag
                            const viewportRect = viewport.getBoundingClientRect();
                            const centerX = viewportRect.width / 2;
                            const cardLocalCenter = (idx * stride) + (cardWidth / 2);
                            currentOffset = centerX - cardLocalCenter;
                            updateCardTransforms();
                        });
                    });
                });
            </script>
        @endif

        <!-- ==================== CALL TO ACTION BANNER (WHITE IN LIGHT MODE, LIME IN DARK MODE) ==================== -->
        <section class="reveal-on-scroll reveal-scale py-14 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="cta-banner relative rounded-3xl bg-white dark:bg-[#93F514] border border-gray-200 dark:border-black/10 p-8 sm:p-12 text-center flex flex-col items-center justify-center shadow-lg shadow-black/5 dark:shadow-xl dark:shadow-black/20 transition-colors duration-300">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black uppercase text-gray-900 dark:text-black tracking-tight leading-tight max-w-3xl">
                    {{ __('SIAP MEMULAI KARIR BARU BERSAMA KAMI?') }}
                </h2>
                <p class="mt-2.5 text-xs sm:text-sm text-gray-600 dark:text-black/85 font-medium max-w-xl leading-relaxed">
                    {{ __('Daftar akun sekarang dan ikuti seleksi online untuk posisi impian Anda.') }}
                </p>

                <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
                    @auth
                        @php
                            $isAdminOrRecruiter =
                                auth()->user()->role_id == 1 ||
                                auth()->user()->role_id == 2 ||
                                in_array(strtolower(auth()->user()->role?->name ?? ''), [
                                    'admin',
                                    'superadmin',
                                    'recruiter',
                                ]);
                            $dashRoute = $isAdminOrRecruiter
                                ? (auth()->user()->role_id == 2 ||
                                strtolower(auth()->user()->role?->name ?? '') === 'recruiter'
                                    ? route('recruiter.dashboard')
                                    : route('admin.dashboard'))
                                : route('profile');
                        @endphp
                        <a href="{{ $dashRoute }}"
                            class="cta-btn-primary w-full sm:w-auto px-8 py-3 rounded-full bg-black dark:bg-black text-white dark:text-[#93F514] font-extrabold text-xs sm:text-sm shadow-md hover:bg-zinc-800 dark:hover:bg-[#151d15] transition-all duration-200 text-center">
                            {{ $isAdminOrRecruiter ? __('Buka Panel Dashboard') : __('Buka Profil Pelamar') }}
                        </a>
                        <a href="{{ route('jobs.index') }}"
                            class="cta-btn-secondary w-full sm:w-auto px-7 py-3 rounded-full border-2 border-black text-black hover:bg-black hover:text-white dark:hover:bg-black/10 dark:hover:text-black font-bold text-xs sm:text-sm transition-all duration-200 text-center">
                            {{ __('Lihat Lowongan') }}
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="cta-btn-primary w-full sm:w-auto px-8 py-3 rounded-full bg-black dark:bg-black text-white dark:text-[#93F514] font-extrabold text-xs sm:text-sm shadow-md hover:bg-zinc-800 dark:hover:bg-[#151d15] transition-all duration-200 text-center">
                            {{ __('Daftar akun sekarang') }}
                        </a>
                        <a href="{{ route('login') }}"
                            class="cta-btn-secondary w-full sm:w-auto px-7 py-3 rounded-full border-2 border-black text-black hover:bg-black hover:text-white dark:hover:bg-black/10 dark:hover:text-black font-bold text-xs sm:text-sm transition-all duration-200 text-center">
                            {{ __('Masuk akun') }}
                        </a>
                    @endauth
                </div>
            </div>
        </section>

    </div>
@endsection
