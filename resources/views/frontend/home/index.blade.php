@extends('frontend.layouts.app')

@section('title', 'Beranda | Mika Career')

@section('content')
    <div class="relative overflow-hidden -mt-[88px] sm:-mt-[96px]">

        <!-- ==================== HERO SECTION (IMAGE BACKGROUND + MULTI-LAYER DARK GRADIENTS) ==================== -->
        <section
            class="relative min-h-screen flex items-center justify-center pt-[120px] sm:pt-[128px] pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden bg-[#040804]">

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

                <!-- Main Headline Hero with Neon #93F514 & White Gradients -->
                <h1
                    class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-[#EEEEEE] leading-tight sm:leading-none max-w-4xl drop-shadow-[0_4px_16px_rgba(0,0,0,0.8)]">
                    Temukan Karir Impian, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#93F514] via-[#75f06a] to-[#5FE6B6]">
                        Wujudkan Potensi Terbaikmu
                    </span>
                </h1>

                <!-- Subtitle Description / Slogan Layer -->
                <p
                    class="mt-6 text-base sm:text-xl text-gray-200 max-w-2xl font-normal leading-relaxed drop-shadow-[0_2px_8px_rgba(0,0,0,0.9)]">
                    Bergabunglah bersama ribuan profesional bertalenta. Jelajahi lowongan kerja, ikuti tes seleksi online
                    terintegrasi, dan raih karir masa depan sekarang.
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
                                placeholder="Cari lowongan yang Anda inginkan..."
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
                                if (!this.selected || this.selected.length === 0) return 'Semua Perusahaan';
                                if (this.selected.length === 1) {
                                    return this.companiesMap[this.selected[0]] || '1 Perusahaan';
                                }
                                return this.selected.length + ' Perusahaan';
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
                                                {{ $companies->firstWhere('id', $homeSelectedCompanies[0])?->name ?? '1 Perusahaan' }}
                                            @else
                                                {{ count($homeSelectedCompanies) }} Perusahaan
                                            @endif
                                        @else
                                            Semua Perusahaan
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
                                    <input type="text" x-model="search" placeholder="Temukan perusahaan..."
                                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                                </div>

                                <!-- All Option -->
                                <label
                                    class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                                    <input type="checkbox" :checked="tempSelected.length === 0" @change="tempSelected = []"
                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer">
                                    <span>Semua Perusahaan</span>
                                </label>

                                <!-- Group Title -->
                                <div
                                    class="mt-3 mb-1.5 flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <span>Pilihan Perusahaan</span>
                                    <span x-show="tempSelected.length > 0" class="text-[#4fa304] font-semibold lowercase"
                                        x-text="tempSelected.length + ' dipilih'"></span>
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
                                        Atur Ulang
                                    </button>
                                    <button type="button" @click="apply()"
                                        class="px-5 py-2 rounded-xl bg-[#93F514] hover:bg-[#7edc0b] text-black font-bold text-xs shadow-sm transition">
                                        Pilih
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
                                    '{{ $dept->id }}': '{{ addslashes($dept->name) }}', @endforeach
                            },
                            getDisplayText() {
                                if (!this.selected || this.selected.length === 0) return 'Semua Departemen';
                                if (this.selected.length === 1) {
                                    return this.departmentsMap[this.selected[0]] || '1 Departemen';
                                }
                                return this.selected.length + ' Departemen';
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
                                                {{ $departments->firstWhere('id', $homeSelectedDepartments[0])?->name ?? '1 Departemen' }}
                                            @else
                                                {{ count($homeSelectedDepartments) }} Departemen
                                            @endif
                                        @else
                                            Semua Departemen
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
                                    <input type="text" x-model="search" placeholder="Temukan fungsi / departemen..."
                                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                                </div>

                                <!-- All Option -->
                                <label
                                    class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                                    <input type="checkbox" :checked="tempSelected.length === 0"
                                        @change="tempSelected = []"
                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer">
                                    <span>Semua Departemen</span>
                                </label>

                                <!-- Group Title -->
                                <div
                                    class="mt-3 mb-1.5 flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <span>Pilihan Departemen</span>
                                    <span x-show="tempSelected.length > 0" class="text-[#4fa304] font-semibold lowercase"
                                        x-text="tempSelected.length + ' dipilih'"></span>
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
                                                    <span class="truncate">{{ $dept->name }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Footer Buttons (Always Visible at Bottom) -->
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                                    <button type="button" @click="reset()"
                                        class="text-xs font-semibold text-gray-500 hover:text-[#93F514] transition">
                                        Atur Ulang
                                    </button>
                                    <button type="button" @click="apply()"
                                        class="px-5 py-2 rounded-xl bg-[#93F514] hover:bg-[#7edc0b] text-black font-bold text-xs shadow-sm transition">
                                        Pilih
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
                            getDisplayText() {
                                if (!this.selected || this.selected.length === 0) return 'Semua Tipe Pekerjaan';
                                if (this.selected.length === 1) {
                                    return this.selected[0];
                                }
                                return this.selected.length + ' Tipe Dipilih';
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
                                                {{ $homeSelectedTypes[0] }}
                                            @else
                                                {{ count($homeSelectedTypes) }} Tipe Dipilih
                                            @endif
                                        @else
                                            Semua Tipe Pekerjaan
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
                                    <input type="text" x-model="search" placeholder="Temukan Tipe Pekerjaan..."
                                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                                </div>

                                <!-- All Option -->
                                <label
                                    class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                                    <input type="checkbox" :checked="tempSelected.length === 0"
                                        @change="tempSelected = []"
                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer">
                                    <span>Semua Tipe Pekerjaan</span>
                                </label>

                                <!-- Group Title -->
                                <div
                                    class="mt-3 mb-1.5 flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <span>Pilihan Tipe Pekerjaan</span>
                                    <span x-show="tempSelected.length > 0" class="text-[#4fa304] font-semibold lowercase"
                                        x-text="tempSelected.length + ' dipilih'"></span>
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
                                                <span x-text="type" class="truncate"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <!-- Footer Buttons (Always Visible at Bottom) -->
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                                    <button type="button" @click="reset()"
                                        class="text-xs font-semibold text-gray-500 hover:text-[#93F514] transition">
                                        Atur Ulang
                                    </button>
                                    <button type="button" @click="apply()"
                                        class="px-5 py-2 rounded-xl bg-[#93F514] hover:bg-[#7edc0b] text-black font-bold text-xs shadow-sm transition">
                                        Pilih
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
                                <span class="whitespace-nowrap">Cari Lowongan</span>
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
                <div class="mt-5 flex flex-wrap items-center justify-center gap-2 text-xs text-gray-400">
                    <span class="font-bold text-[#93F514]">Pencarian Populer:</span>
                    <a href="{{ route('jobs.index', ['search' => 'Software Engineer']) }}"
                        class="px-3 py-1 rounded-full bg-[#051205] hover:bg-[#93F514]/20 border border-[#93F514]/30 text-gray-300 hover:text-[#93F514] transition">Software
                        Engineer</a>
                    <a href="{{ route('jobs.index', ['search' => 'Staff']) }}"
                        class="px-3 py-1 rounded-full bg-[#051205] hover:bg-[#93F514]/20 border border-[#93F514]/30 text-gray-300 hover:text-[#93F514] transition">Staff
                        Administrasi</a>
                    <a href="{{ route('jobs.index', ['search' => 'Marketing']) }}"
                        class="px-3 py-1 rounded-full bg-[#051205] hover:bg-[#93F514]/20 border border-[#93F514]/30 text-gray-300 hover:text-[#93F514] transition">Digital
                        Marketing</a>
                    <a href="{{ route('jobs.index', ['location' => 'Remote']) }}"
                        class="px-3 py-1 rounded-full bg-[#051205] hover:bg-[#93F514]/20 border border-[#93F514]/30 text-gray-300 hover:text-[#93F514] transition">Remote</a>
                </div>

                <!-- Quick Stats Summary -->
                <div
                    class="mt-14 grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 w-full max-w-4xl border-t border-[#93F514]/20 pt-8">
                    <div class="reveal-on-scroll p-4 rounded-2xl bg-gradient-to-b from-[#061506] to-[#040804] border border-[#93F514]/30 hover:border-[#93F514]/60 transition shadow-lg shadow-black/40"
                        data-delay="100">
                        <div class="text-2xl sm:text-3xl font-black text-[#EEEEEE]">{{ $totalJobsCount ?? 0 }}+</div>
                        <div class="text-xs text-[#93F514] font-semibold mt-1">Lowongan Aktif</div>
                    </div>
                    <div class="reveal-on-scroll p-4 rounded-2xl bg-gradient-to-b from-[#061506] to-[#040804] border border-[#93F514]/30 hover:border-[#93F514]/60 transition shadow-lg shadow-black/40"
                        data-delay="200">
                        <div class="text-2xl sm:text-3xl font-black text-[#EEEEEE]">{{ $companiesCount ?? 0 }}+</div>
                        <div class="text-xs text-[#93F514] font-semibold mt-1">Perusahaan Mitra</div>
                    </div>
                    <div class="reveal-on-scroll p-4 rounded-2xl bg-gradient-to-b from-[#061506] to-[#040804] border border-[#93F514]/30 hover:border-[#93F514]/60 transition shadow-lg shadow-black/40"
                        data-delay="300">
                        <div class="text-2xl sm:text-3xl font-black text-[#EEEEEE]">{{ $departmentsCount ?? 0 }}+</div>
                        <div class="text-xs text-[#93F514] font-semibold mt-1">Bidang / Departemen</div>
                    </div>
                    <div class="reveal-on-scroll p-4 rounded-2xl bg-gradient-to-b from-[#061506] to-[#040804] border border-[#93F514]/30 hover:border-[#93F514]/60 transition shadow-lg shadow-black/40"
                        data-delay="400">
                        <div class="text-2xl sm:text-3xl font-black text-[#EEEEEE]">{{ $totalQuotaCount ?? 0 }}+</div>
                        <div class="text-xs text-[#93F514] font-semibold mt-1">Total Kuota Formasi</div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ==================== SHOWCASE / ABOUT COMPANY CAROUSEL SECTION ==================== -->
        <section class="reveal-on-scroll relative py-16 sm:py-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto overflow-hidden"
            x-data="{
                currentSlide: 0,
                companyName: '{{ isset($mainCompany) ? $mainCompany->name : 'Mitra Karya Analitika' }}',
                companyWebsite: '{{ isset($mainCompany) && $mainCompany->website ? (Str::startsWith($mainCompany->website, ['http://', 'https://']) ? $mainCompany->website : 'https://' . $mainCompany->website) : '' }}',
                slides: [{
                        tag: 'Tentang Kami & Karir',
                        title: 'Ruang untuk Bertumbuh dan Berkembang',
                        description: 'Kami mendorong setiap individu untuk terus berkembang melalui pelatihan berkelanjutan, pengembangan sumber daya manusia, serta lingkungan kerja modern. Bersama {{ isset($mainCompany) ? $mainCompany->name : 'Mitra Karya Analitika' }}, kembangkan potensi, pengalaman, dan karier Anda secara optimal.',
                        img1: '{{ asset('storage/asset-compro/aniv1.jpg') }}',
                        img2: '{{ asset('storage/asset-compro/outbond.jpg') }}',
                        img3: '{{ asset('storage/asset-compro/aniv.jpg') }}',
                        badgeTitle: 'CAREER.',
                        badgeSub: 'Growth & Development'
                    },
                    {
                        tag: 'Acara & Kolaborasi',
                        title: 'Bimbingan Teknis ASPADIN 2026: Sinergi Kompetensi',
                        description: 'Menghadirkan sesi Bimbingan Teknis eksklusif di Semarang bagi para mitra industri. Kami berbagi pengetahuan, memamerkan inovasi solusi IoT terbaru, dan memperkuat jaringan untuk pertumbuhan profesional bersama.',
                        img1: '{{ asset('storage/asset-compro/aspadin1.jpg') }}', // Gambar poster utama acara
                        img2: '{{ asset('storage/asset-compro/aspadin2.jpg') }}', // Kolase foto aktivitas detail dan interaksi
                        img3: '{{ asset('storage/asset-compro/aspadin3.jpg') }}', // Kolase foto pameran produk dan pertemuan
                        badgeTitle: 'EVENT',
                        badgeSub: 'Technical Guidance'
                    },
                    {
                        tag: 'Acara & Pameran',
                        title: 'Partisipasi Aktif di Event HISFARIN 2025',
                        description: 'Memperluas jaringan dan memperkenalkan solusi teknologi analitik terkini dalam Musyawarah Nasional HISFARIN 2025. Kami hadir langsung menyapa para profesional, memamerkan perangkat keras inovatif, dan membangun sinergi kolaboratif untuk mendukung kemajuan industri.',
                        img1: '{{ asset('storage/asset-compro/hisfarin1.jpg') }}', // Gambar poster Event HISFARIN 2025 (tanggal & lokasi)
                        img2: '{{ asset('storage/asset-compro/hisfarin2.jpg') }}', // Kolase foto antusiasme pengunjung dan interaksi di booth MIKA
                        img3: '{{ asset('storage/asset-compro/hisfarin3.jpg') }}', // Dokumentasi display produk, presentasi, dan foto bersama
                        badgeTitle: 'EVENT',
                        badgeSub: 'Exhibition & Networking'
                    }
                ],
                autoplayTimer: null,
                progressTimer: null,
                duration: 6000,
                progress: 0,
                isPaused: false,
                observer: null,
                init() {
                    this.startAutoplay();
                    if ('IntersectionObserver' in window) {
                        this.observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    this.resume();
                                } else {
                                    this.pause();
                                }
                            });
                        }, { threshold: 0.05 });
                        this.observer.observe(this.$el);
                    }
                },
                next() {
                    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                    this.restartAutoplay();
                },
                prev() {
                    this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
                    this.restartAutoplay();
                },
                goTo(index) {
                    this.currentSlide = index;
                    this.restartAutoplay();
                },
                startAutoplay() {
                    this.isPaused = false;
                    this.stopAutoplay();
                    const intervalMs = 50;
                    const step = (intervalMs / this.duration) * 100;
                    this.progressTimer = setInterval(() => {
                        if (!this.isPaused) {
                            this.progress += step;
                            if (this.progress >= 100) {
                                this.progress = 0;
                                this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                            }
                        }
                    }, intervalMs);
                },
                stopAutoplay() {
                    if (this.progressTimer) clearInterval(this.progressTimer);
                    if (this.autoplayTimer) clearInterval(this.autoplayTimer);
                },
                pause() {
                    this.isPaused = true;
                },
                resume() {
                    this.isPaused = false;
                },
                restartAutoplay() {
                    this.progress = 0;
                    this.startAutoplay();
                }
            }">

            <!-- Background Decorative Patterns -->
            <div
                class="absolute inset-0 bg-gradient-to-b from-[#040804] via-[#051405] to-[#040804] rounded-3xl sm:rounded-[2.5rem] border border-[#93F514]/25 shadow-2xl shadow-black/80 overflow-hidden pointer-events-none">
                <!-- Hexagon Pattern Overlay -->
                <div
                    class="absolute inset-0 opacity-[0.07] bg-[radial-gradient(#93F514_1.5px,transparent_1.5px)] [background-size:24px_24px]">
                </div>

                <!-- Concentric Circular Background Ripples -->
                <div
                    class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full border border-[#93F514]/15 pointer-events-none">
                </div>
                <div
                    class="absolute -bottom-40 -left-40 w-[30rem] h-[30rem] rounded-full border border-[#93F514]/10 pointer-events-none">
                </div>
                <div
                    class="absolute -bottom-56 -left-56 w-[40rem] h-[40rem] rounded-full border border-[#93F514]/5 pointer-events-none">
                </div>

            </div>

            <!-- Main Inner Container -->
            <div class="relative z-10 p-6 sm:p-10 lg:p-14" @mouseenter="pause()" @mouseleave="resume()">

                <!-- Slides Content Wrapper with consistent minimum height to prevent layout shift -->
                <div class="relative min-h-[580px] sm:min-h-[500px] lg:min-h-[460px] flex items-center">
                    <template x-for="(slide, index) in slides" :key="index">
                        <div x-show="currentSlide === index" x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-x-8"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-400 absolute inset-0"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 -translate-x-8"
                            class="w-full grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                            <!-- Left Column: Typography & Description -->
                            <div class="lg:col-span-5 flex flex-col justify-between h-full space-y-6">
                                <div>
                                    <!-- Tag / Category Header -->
                                    <div
                                        class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] text-xs font-bold uppercase tracking-wider mb-4">
                                        {{-- <span class="w-2 h-2 rounded-full bg-[#93F514] animate-pulse"></span> --}}
                                        <span x-text="slide.tag"></span>
                                    </div>

                                    <!-- Main Slide Title -->
                                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#EEEEEE] tracking-tight leading-tight sm:leading-snug"
                                        x-text="slide.title">
                                    </h2>

                                    <div class="w-16 h-1 bg-gradient-to-r from-[#93F514] to-transparent rounded-full my-4">
                                    </div>

                                    <!-- Slide Paragraph -->
                                    <p class="text-sm sm:text-base text-gray-300 font-normal leading-relaxed text-justify sm:text-left"
                                        x-text="slide.description">
                                    </p>
                                </div>

                                <!-- Left-Bottom Controls & Actions -->
                                <div class="pt-4 flex flex-wrap items-center gap-3">
                                    <a href="{{ route('jobs.index') }}"
                                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#93F514] hover:bg-[#7edc0b] text-black font-extrabold text-xs sm:text-sm tracking-wide shadow-md shadow-black/40 transition duration-200">
                                        <span>Lihat Lowongan</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>

                                    <template x-if="companyWebsite">
                                        <a :href="companyWebsite" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white/5 hover:bg-white/10 border border-[#93F514]/40 text-[#EEEEEE] hover:text-[#93F514] font-semibold text-xs sm:text-sm transition duration-200 backdrop-blur-sm">
                                            <svg class="w-4 h-4 text-[#93F514]" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                            </svg>
                                            <span>Kunjungi Website</span>
                                            <svg class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </template>
                                </div>
                            </div>

                            <!-- Right Column: Staggered Dynamic Photo Collage & Floating Badge -->
                            <div
                                class="lg:col-span-7 relative flex items-center justify-center lg:justify-end py-6 lg:py-0">
                                <div class="relative w-full max-w-[560px] h-[340px] sm:h-[390px]">

                                    <!-- Main Large Photo (Left Background Layer) -->
                                    <div
                                        class="absolute left-0 top-6 w-[56%] sm:w-[58%] h-[260px] sm:h-[310px] rounded-2xl sm:rounded-3xl p-1.5 bg-gradient-to-br from-[#93F514]/50 via-white/10 to-transparent shadow-2xl shadow-black/80 group">
                                        <div
                                            class="w-full h-full rounded-[14px] sm:rounded-[22px] overflow-hidden bg-black/40 border border-white/20">
                                            <img :src="slide.img1" alt="Team Collaboration"
                                                class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-500 filter brightness-95">
                                        </div>
                                    </div>

                                    <!-- Top-Right Secondary Photo Layer -->
                                    <div
                                        class="absolute right-0 top-0 w-[48%] sm:w-[50%] h-[200px] sm:h-[235px] rounded-2xl sm:rounded-3xl p-1.5 bg-gradient-to-bl from-[#93F514]/60 via-white/15 to-transparent shadow-2xl shadow-black/90 group z-10">
                                        <div
                                            class="w-full h-full rounded-[14px] sm:rounded-[22px] overflow-hidden bg-black/40 border border-white/20">
                                            <img :src="slide.img2" alt="Professional Presentation"
                                                class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-500 filter brightness-95">
                                        </div>
                                    </div>

                                    <!-- Bottom Center/Right Overlapping Tertiary Photo -->
                                    <div
                                        class="absolute left-[35%] sm:left-[32%] bottom-0 w-[50%] sm:w-[52%] h-[190px] sm:h-[220px] rounded-2xl sm:rounded-3xl p-1.5 bg-gradient-to-tr from-[#93F514]/60 via-white/15 to-transparent shadow-2xl shadow-black/95 group z-20">
                                        <div
                                            class="w-full h-full rounded-[14px] sm:rounded-[22px] overflow-hidden bg-black/40 border border-white/25">
                                            <img :src="slide.img3" alt="Meeting and Discussion"
                                                class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-500 filter brightness-95">
                                        </div>
                                    </div>

                                    <!-- Simple, Light & Modern Floating Glass Badge (Bottom Right) -->
                                    <div
                                        class="absolute -right-1 sm:-right-3 bottom-2 z-30 transform hover:scale-105 transition-transform">
                                        <div
                                            class="px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl bg-[#061806]/85 border border-[#93F514]/50 shadow-lg shadow-black/60 backdrop-blur-md flex items-center gap-2.5">
                                            {{-- <div class="w-2 h-2 rounded-full bg-[#93F514] animate-ping"></div> --}}
                                            <div>
                                                <div class="text-[11px] sm:text-xs font-bold tracking-wider text-[#93F514] leading-none"
                                                    x-text="slide.badgeTitle">CAREER.</div>
                                                <div class="text-[10px] text-gray-300 font-medium mt-0.5 leading-none"
                                                    x-text="slide.badgeSub">Growth & Development</div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- Bottom Navigation Bar: Arrows + Live Animated Progress Indicator Lines -->
                <div
                    class="mt-8 pt-6 border-t border-[#93F514]/20 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Prev / Next Navigation Arrows -->
                    <div class="flex items-center gap-3">
                        <button @click="prev()" aria-label="Previous Slide"
                            class="w-10 h-10 rounded-xl bg-[#061506] hover:bg-[#93F514] text-gray-300 hover:text-black border border-[#93F514]/40 transition-all flex items-center justify-center cursor-pointer shadow-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button @click="next()" aria-label="Next Slide"
                            class="w-10 h-10 rounded-xl bg-[#061506] hover:bg-[#93F514] text-gray-300 hover:text-black border border-[#93F514]/40 transition-all flex items-center justify-center cursor-pointer shadow-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <span class="text-xs text-gray-400 font-semibold ml-2">
                            <span class="text-[#93F514] font-bold" x-text="currentSlide + 1"></span> / <span
                                x-text="slides.length"></span>
                        </span>
                    </div>

                    <!-- Lightweight Segmented Progress Indicator Bar with Real-time Fill Animation -->
                    <div class="flex items-center gap-2.5 w-full sm:w-80">
                        <template x-for="(slide, index) in slides" :key="index">
                            <button @click="goTo(index)" :aria-label="'Go to slide ' + (index + 1)"
                                class="h-1.5 sm:h-2 flex-1 rounded-full transition-all duration-300 cursor-pointer overflow-hidden relative bg-white/10 hover:bg-white/20">
                                <!-- Background fill for active slide with smooth real-time progress -->
                                <div class="h-full bg-gradient-to-r from-[#93F514] to-[#5ef558] rounded-full transition-[width] ease-linear"
                                    :style="currentSlide === index ?
                                        `width: ${progress}%; transition-duration: ${isPaused ? '0ms' : '50ms'};` : (
                                            currentSlide > index ? 'width: 100%; transition-duration: 0ms;' :
                                            'width: 0%; transition-duration: 0ms;')">
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

            </div>
        </section>

        <!-- ==================== ALUR PENDAFTARAN & SELEKSI (INTERACTIVE PIPELINE) ==================== -->
        <section id="alur-pendaftaran"
            class="reveal-on-scroll py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto relative border-t border-gray-200/80 dark:border-white/[0.08]"
            x-data="{ activeStep: 1 }">
            
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-10">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">Alur Pendaftaran & Seleksi</h2>
                <p class="mt-3 text-sm sm:text-base text-gray-600 dark:text-zinc-400 leading-relaxed max-w-2xl mx-auto">
                    Ikuti 6 tahapan sistematis dan transparan untuk bergabung menjadi bagian dari talenta terbaik PT Mitra Karya Analitika (MIKA).
                </p>
            </div>

            <!-- Connected Interactive Pipeline Stepper Bar -->
            <div class="mb-8">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 sm:gap-3">
                    <template x-for="step in [
                        { num: 1, id: '01', title: 'Registrasi', sub: 'Buat Akun Pelamar' },
                        { num: 2, id: '02', title: 'Profil & CV', sub: 'Lengkapi Berkas' },
                        { num: 3, id: '03', title: 'Seleksi Berkas', sub: 'Screening HRD' },
                        { num: 4, id: '04', title: 'Asesmen CBT', sub: 'Ujian & Tes DISC' },
                        { num: 5, id: '05', title: 'Wawancara', sub: 'HRD & User' },
                        { num: 6, id: '06', title: 'Offering', sub: 'Hasil & Kontrak' }
                    ]" :key="step.num">
                        <button @click="activeStep = step.num" type="button"
                            class="group relative text-left p-3.5 rounded-2xl border transition-all duration-200 cursor-pointer overflow-hidden"
                            :class="activeStep === step.num ? 
                                'bg-white dark:bg-white/[0.08] border-[#93F514] dark:border-[#93F514]/70 shadow-lg shadow-gray-200/80 dark:shadow-black/60 ring-2 ring-[#93F514]/20' : 
                                'bg-white dark:bg-white/[0.02] hover:bg-gray-50 dark:hover:bg-white/[0.05] border-gray-200 dark:border-white/[0.06] hover:border-gray-300 dark:hover:border-white/15 shadow-sm dark:shadow-none'">
                            <!-- Top subtle highlight when active -->
                            <div x-show="activeStep === step.num" 
                                class="absolute top-0 left-0 right-0 h-[2.5px] bg-[#93F514]">
                            </div>
                            
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-extrabold tracking-wider transition-colors"
                                    :class="activeStep === step.num ? 'text-[#3b8004] dark:text-[#93F514]' : 'text-gray-400 dark:text-zinc-500 group-hover:text-gray-600 dark:group-hover:text-zinc-400'"
                                    x-text="step.id"></span>
                                <span class="w-2 h-2 rounded-full transition-colors"
                                    :class="activeStep === step.num ? 'bg-[#4fa304] dark:bg-[#93F514]' : 'bg-gray-300 dark:bg-zinc-700'"></span>
                            </div>
                            <div class="text-xs sm:text-sm font-bold truncate transition-colors"
                                :class="activeStep === step.num ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-zinc-300 group-hover:text-gray-900 dark:group-hover:text-white'"
                                x-text="step.title"></div>
                            <div class="text-[11px] font-medium truncate mt-0.5 transition-colors"
                                :class="activeStep === step.num ? 'text-gray-600 dark:text-zinc-400' : 'text-gray-500 dark:text-zinc-500'"
                                x-text="step.sub"></div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Active Stage Detail Display Canvas -->
            <div class="relative rounded-3xl bg-white dark:bg-gradient-to-b dark:from-[#0a120a] dark:via-[#050905] dark:to-[#040604] border border-gray-200/90 dark:border-white/[0.08] p-6 sm:p-10 lg:p-12 shadow-xl shadow-gray-200/60 dark:shadow-2xl dark:shadow-black/80 overflow-hidden">
                <!-- Subtle directional glow (dark mode) -->
                <div class="absolute -top-32 right-1/4 w-[500px] h-[250px] bg-[#93F514]/[0.05] blur-[120px] rounded-full pointer-events-none hidden dark:block"></div>

                <!-- ================= Step 1: Registrasi Pengguna ================= -->
                <div x-show="activeStep === 1" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <div class="lg:col-span-7 space-y-6">
                        <div class="space-y-3">
                            <div class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>Tahap 01 — Registrasi Akun</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                Buat Akun Pelamar dengan Cepat & Aman
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                Lakukan registrasi menggunakan Google Authentication atau Form Manual (Nama Lengkap, NIK KTP valid, dan Email aktif). Akun ini menjadi pusat portal seluruh aktivitas pelamaran, verifikasi dokumen, ujian seleksi, hingga penawaran kerja.
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Autentikasi Cepat</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Google OAuth atau form NIK terverifikasi.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Akses Fleksibel</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Responsif 24/7 di smartphone & desktop.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Pemberitahuan</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Status seleksi langsung ke portal akun.</p>
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
                                    <span>{{ $isAdminOrRecruiter ? 'Buka Dashboard Manajemen' : 'Buka Profil Saya' }}</span>
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                    class="inline-flex items-center gap-3 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25 group">
                                    <span>Daftar Akun Sekarang</span>
                                    <span class="w-5 h-5 rounded-md bg-black/10 flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </span>
                                </a>
                            @endauth
                            <button type="button" @click="activeStep = 2"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                <span>Lanjut: Profil & CV</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Ketentuan Tahap 01</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">Tips Registrasi Akun</h4>
                                <div class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    Pastikan nomor NIK KTP dan alamat email yang didaftarkan aktif dan valid untuk memastikan kelancaran verifikasi identitas serta notifikasi status seleksi.
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span>Data terlindungi dengan enkripsi keamanan portal resmi MIKA</span>
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
                            <div class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>Tahap 02 — Kelengkapan Profil & CV</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                Lengkapi Biodata, Riwayat & CV Digital
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                Lengkapi profil Anda secara menyeluruh: Biodata Pribadi, Kontak Keluarga, Riwayat Pendidikan, Pengalaman Kerja, Organisasi, Prestasi, hingga Keahlian/Sertifikasi. Sistem secara otomatis menyusun data Anda menjadi format CV profesional.
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>ATS-Friendly</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Generate & preview CV digital terstandar.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Update Fleksibel</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Pembaruan data dapat dilakukan berkala.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Unggah Dokumen</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Ijazah, transkrip nilai, & sertifikat.</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('profile') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>Lengkapi Profil Sekarang</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>Masuk untuk Lengkapi Profil</span>
                                </a>
                            @endauth
                            <button type="button" @click="activeStep = 3"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                <span>Lanjut: Lamar & Seleksi</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Ketentuan Tahap 02</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">Prioritas Screening</h4>
                                <div class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    Profil pelamar dengan persentase kelengkapan data di atas 85% memiliki prioritas lebih tinggi dalam peninjauan administrasi oleh tim HRD.
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Format resume disinkronkan otomatis ke database rekruter</span>
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
                            <div class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>Tahap 03 — Seleksi Berkas</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                Eksplorasi Lowongan & Seleksi Berkas
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                Temukan posisi karir yang sesuai dengan kompetensi Anda di PT Mitra Karya Analitika (MIKA) atau grup bisnis kami. Ajukan lamaran dalam satu klik, lalu tim HRD & Rekruter akan meninjau kualifikasi dan kesesuaian berkas Anda secara transparan.
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>One-Click Apply</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Lamaran praktis dengan profil terintegrasi.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Pelacakan Status</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Update real-time (Terkirim, Review, Lolos).</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Notifikasi Hasil</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Kelulusan administrasi sebelum ke tahap ujian.</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            <a href="{{ route('jobs.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                <span>Jelajahi Lowongan Tersedia</span>
                            </a>
                            <button type="button" @click="activeStep = 4"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                <span>Lanjut: Asesmen CBT</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Ketentuan Tahap 03</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">Seleksi Administrasi</h4>
                                <div class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    Tim Rekruter akan mencocokkan latar belakang pendidikan, kompetensi, dan pengalaman kerja Anda dengan kualifikasi formasi yang dilamar.
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>Transparan tanpa perantara atau pungutan biaya</span>
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
                            <div class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>Tahap 04 — Ujian Asesmen Online</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                Ikuti Ujian CBT & Tes Kepribadian DISC
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                Setelah dinyatakan lolos berkas administrasi, Anda dapat langsung mengakses sistem Ujian Online terintegrasi. Ujian meliputi Tes Kepribadian DISC (24 kuadran Most/Least) serta Tes Kompetensi Teknis (Pilihan Ganda & Soal Essay + Lampiran Berkas).
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>CBT Terpadu</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Timer real-time dan evaluasi terstandar.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Tes DISC</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Kalkulasi otomatis profil kepribadian kerja.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Auto-Save Aman</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Penyimpanan jawaban berkala anti hilang.</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('profile', ['tab' => 'riwayat']) }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>Cek Status Ujian di Riwayat</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>Masuk ke Portal Ujian</span>
                                </a>
                            @endauth
                            <button type="button" @click="activeStep = 5"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                <span>Lanjut: Wawancara</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Ketentuan Tahap 04</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">Persiapan Ujian</h4>
                                <div class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    Gunakan perangkat komputer/laptop dengan koneksi internet yang stabil untuk pengalaman optimal saat menyelesaikan tes DISC dan ujian teknis.
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <span>Hasil terkomputasi langsung ke dashboard seleksi rekruter</span>
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
                            <div class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>Tahap 05 — Sesi Wawancara</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                Sesi Interview Daring atau Tatap Muka
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                Kandidat yang lolos tahap ujian asesmen (Shortlisted) akan dijadwalkan untuk sesi wawancara mendalam bersama tim HRD dan User. Informasi jadwal, pewawancara, serta tautan video conference (Google Meet) atau lokasi kantor ditampilkan langsung pada dashboard Anda.
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Jadwal Terintegrasi</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Pewawancara dan waktu (WIB) tertera jelas.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Link Meet Langsung</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Akses video call dari kartu riwayat lamaran.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Pengingat Otomatis</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Notifikasi terjadwal di portal akun Anda.</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('profile', ['tab' => 'riwayat']) }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>Pantau Jadwal Wawancara</span>
                                </a>
                            @else
                                <a href="{{ route('jobs.index') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>Lihat Peluang Karir</span>
                                </a>
                            @endauth
                            <button type="button" @click="activeStep = 6"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] text-gray-700 hover:text-gray-900 dark:text-zinc-300 dark:hover:text-white font-semibold text-xs sm:text-sm border border-gray-200 dark:border-white/10 transition cursor-pointer">
                                <span>Lanjut: Hasil Akhir</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Ketentuan Tahap 05</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">Persiapan Wawancara</h4>
                                <div class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    Pelajari profil dan nilai inti MIKA (Menghargai, Integritas, Komitmen, Akuntabel), kenali tanggung jawab posisi, dan siapkan perangkat kamera/audio jika sesi berlangsung secara daring.
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span>Sesi wawancara berlangsung profesional & solutif</span>
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
                            <div class="inline-flex items-center gap-2 text-xs text-[#3b8004] dark:text-[#93F514] font-bold uppercase tracking-wider">
                                {{-- <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span> --}}
                                <span>Tahap 06 — Hasil Akhir & Penawaran</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight leading-snug">
                                Pengumuman Kelulusan & Penawaran Kerja
                            </h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-zinc-300 leading-relaxed">
                                Kandidat terbaik yang terpilih akan menerima pemberitahuan kelulusan resmi dengan status Diterima (Accepted). Tim MIKA akan menerbitkan surat penawaran kerja (*Offering Letter*) resmi beserta panduan onboarding kerja.
                            </p>
                        </div>

                        <!-- Structured Milestone Items -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Hasil Transparan</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Status resmi langsung tertera di akun Anda.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>Offering Letter</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Surat penawaran resmi beserta paket benefit.</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50/80 dark:bg-white/[0.03] border border-gray-200/80 dark:border-white/[0.06] space-y-1">
                                <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa304] dark:bg-[#93F514]"></span>
                                    <span>100% Gratis</span>
                                </div>
                                <p class="text-[11px] text-gray-600 dark:text-zinc-400 leading-relaxed">Tanpa pungutan biaya apapun dalam proses.</p>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="pt-3 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route('profile', ['tab' => 'riwayat']) }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25">
                                    <span>Lihat Riwayat & Status Saya</span>
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                    class="inline-flex items-center gap-3 px-6 py-2.5 rounded-xl bg-[#93F514] text-black font-bold text-xs sm:text-sm hover:bg-[#82e00b] transition shadow-md shadow-[#93F514]/25 group">
                                    <span>Daftar & Raih Karir Impian</span>
                                    <span class="w-5 h-5 rounded-md bg-black/10 flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </span>
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Right Column: Stage Intelligence & Guidance Panel -->
                    <div class="lg:col-span-5">
                        <div class="p-6 rounded-2xl bg-gray-50/80 dark:bg-white/[0.02] border border-gray-200/80 dark:border-white/10 space-y-4">
                            <div class="pb-3 border-b border-gray-200/80 dark:border-white/[0.06]">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Ketentuan Tahap 06</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">Selamat Bergabung!</h4>
                                <div class="p-3.5 rounded-xl bg-[#93F514]/10 dark:bg-white/[0.02] border-l-4 border-[#4fa304] dark:border-[#93F514] text-xs text-gray-700 dark:text-zinc-300 leading-relaxed">
                                    Siapkan diri Anda untuk melangkah ke babak baru perjalanan karir profesional masa depan bersama PT Mitra Karya Analitika (MIKA).
                                </div>
                            </div>
                            <div class="pt-1 flex items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-[#4fa304] dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                <span>Onboarding terstruktur & pendampingan tim MIKA</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ==================== KATEGORI DEPARTEMEN ==================== -->
        <section id="kategori"
            class="reveal-on-scroll py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto relative border-t border-[#93F514]/20">
            <div class="mb-8">
                {{-- <div class="inline-flex items-center gap-2 text-[#93F514] text-xs font-bold uppercase tracking-widest mb-2">
                <span class="w-2 h-2 rounded-full bg-[#93F514]"></span> Bidang Karir
            </div> --}}
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#EEEEEE]">Kategori Departemen</h2>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($departments as $dept)
                    <a href="{{ route('jobs.index', ['department_id' => $dept->id]) }}"
                        class="reveal-on-scroll group p-5 rounded-2xl bg-gradient-to-b from-[#061506] to-[#040804] border border-[#93F514]/30 hover:border-[#93F514] hover:shadow-xl hover:shadow-black/60 transition-all duration-300 flex flex-col justify-between"
                        data-delay="{{ ($loop->index % 4) * 100 }}">
                        <div
                            class="w-10 h-10 rounded-xl bg-[#93F514]/15 border border-[#93F514]/40 flex items-center justify-center text-[#93F514] group-hover:bg-[#93F514] group-hover:text-black transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="mt-4">
                            <h3
                                class="font-bold text-[#EEEEEE] group-hover:text-[#93F514] transition-colors text-sm sm:text-base">
                                {{ $dept->name }}
                            </h3>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-xs text-gray-400">Belum ada data departemen.</div>
                @endforelse
            </div>
        </section>

        <!-- ==================== CALL TO ACTION BANNER ==================== -->
        <section class="reveal-on-scroll reveal-scale py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div
                class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#041a04] via-[#062906] to-[#031203] border border-[#93F514]/50 p-8 sm:p-12 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-8 shadow-2xl shadow-black/80">
                <div class="max-w-xl z-10">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#EEEEEE] leading-tight">
                        Siap Memulai Karir Baru Bersama Kami?
                    </h2>
                    <p class="mt-2 text-sm text-gray-300 leading-relaxed">
                        Daftar akun sekarang dan ikuti seleksi online untuk posisi pekerjaan impian Anda.
                    </p>
                </div>

                <div class="z-10 flex flex-col sm:flex-row items-center gap-3 shrink-0">
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
                            class="px-8 py-3.5 rounded-full bg-gradient-to-r from-[#93F514] to-[#5ef558] hover:from-[#7edc0b] hover:to-[#43e63d] text-black font-extrabold text-sm shadow-md shadow-black/40 transition-all duration-300">
                            {{ $isAdminOrRecruiter ? 'Buka Panel Dashboard' : 'Buka Profil Pelamar' }}
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-gradient-to-r from-[#93F514] to-[#5ef558] hover:from-[#7edc0b] hover:to-[#43e63d] text-black font-extrabold text-sm shadow-md shadow-black/40 transition-all duration-300 text-center">
                            Daftar Akun Sekarang
                        </a>
                        <a href="{{ route('login') }}"
                            class="w-full sm:w-auto px-6 py-3.5 rounded-full bg-black/50 hover:bg-black/80 border border-[#93F514]/50 text-[#EEEEEE] font-semibold text-sm transition text-center">
                            Masuk Akun
                        </a>
                    @endauth
                </div>
            </div>
        </section>

    </div>
@endsection
