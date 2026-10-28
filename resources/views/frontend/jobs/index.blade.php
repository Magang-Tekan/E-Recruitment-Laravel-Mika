@extends('frontend.layouts.app')

@section('title', __('Lowongan') . ' - Mika Career')

@section('content')
    <div class="relative py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="reveal-on-scroll jobs-page-header mb-8 max-w-3xl">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#EEEEEE] tracking-tight leading-tight">
                {{ __('Jelajahi') }} <span class="text-[#93F514]">{{ __('Lowongan Pekerjaan') }}</span>
            </h1>
            <p class="mt-3 text-sm sm:text-base text-gray-400 leading-relaxed">
                {{ __('Temukan posisi yang sesuai dengan keahlian, minat, dan kualifikasi Anda. Lamar sekarang dan ikuti tahapan seleksi terintegrasi.') }}
            </p>
        </div>

        <!-- Filter & Search Bar (Clean Segmented Solid Light Pill Bar, samakan dengan Beranda) -->
        <div class="reveal-on-scroll relative z-30 w-full mb-10 p-2 sm:p-2.5 rounded-2xl lg:rounded-full bg-[#EEEEEE] text-gray-800 shadow-2xl shadow-black/80 border border-white/40"
            style="background-color: #EEEEEE !important;" data-delay="100">
            <form action="{{ route('jobs.index') }}" method="GET"
                class="flex flex-col lg:flex-row items-center gap-2 lg:gap-0 divide-y lg:divide-y-0 lg:divide-x divide-gray-300">

                <!-- Keyword Search -->
                <div class="w-full lg:flex-1 relative flex items-center px-4 py-1">
                    <div class="pr-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Cari lowongan yang Anda inginkan...') }}"
                        class="w-full py-2 bg-transparent text-gray-900 placeholder-gray-400 border-0 outline-none focus:outline-none focus:ring-0 focus:border-0 shadow-none text-sm font-medium">
                </div>

                <!-- Dropdown Semua Perusahaan -->
                <div class="w-full lg:w-56 relative" :class="open ? 'z-50' : 'z-10'" x-data="{
                    open: false,
                    search: '',
                    selected: {{ json_encode(array_map('strval', $selectedCompanyIds ?? [])) }},
                    tempSelected: {{ json_encode(array_map('strval', $selectedCompanyIds ?? [])) }},
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
                                    {{ count($selectedCompanyIds ?? []) }}
                                </span>
                            </template>
                            <span class="truncate" x-text="getDisplayText()">
                                @if (!empty($selectedCompanyIds))
                                    @if (count($selectedCompanyIds) === 1)
                                        {{ $companies->firstWhere('id', $selectedCompanyIds[0])?->name ?? ('1 ' . __('Perusahaan')) }}
                                    @else
                                        {{ count($selectedCompanyIds) }} {{ __('Perusahaan') }}
                                    @endif
                                @else
                                    {{ __('Semua Perusahaan') }}
                                @endif
                            </span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                            :class="open ? 'rotate-180 text-[#93F514]' : (selected.length > 0 ? 'text-[#93F514]' : '')"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
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
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
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
                                            <input type="checkbox" value="{{ $comp->id }}" x-model="tempSelected"
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
                                class="px-5 py-2 rounded-xl bg-[#93F514] hover:bg-[#7edc0b] text-black font-bold text-xs shadow-md shadow-black/25 transition">
                                {{ __('Pilih') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dropdown Semua Departemen / Fungsi -->
                <div class="w-full lg:w-56 relative" :class="open ? 'z-50' : 'z-10'" x-data="{
                    open: false,
                    search: '',
                    selected: {{ json_encode(array_map('strval', $selectedDepartmentIds ?? [])) }},
                    tempSelected: {{ json_encode(array_map('strval', $selectedDepartmentIds ?? [])) }},
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
                                    {{ count($selectedDepartmentIds ?? []) }}
                                </span>
                            </template>
                            <span class="truncate" x-text="getDisplayText()">
                                @if (!empty($selectedDepartmentIds))
                                    @if (count($selectedDepartmentIds) === 1)
                                        {{ __($departments->firstWhere('id', $selectedDepartmentIds[0])?->name ?? ('1 ' . __('Departemen'))) }}
                                    @else
                                        {{ count($selectedDepartmentIds) }} {{ __('Departemen') }}
                                    @endif
                                @else
                                    {{ __('Semua Departemen') }}
                                @endif
                            </span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                            :class="open ? 'rotate-180 text-[#93F514]' : (selected.length > 0 ? 'text-[#93F514]' : '')"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
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
                            <input type="text" x-model="search" placeholder="{{ __('Temukan fungsi / departemen...') }}"
                                class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                        </div>

                        <!-- All Option -->
                        <label
                            class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                            <input type="checkbox" :checked="tempSelected.length === 0" @change="tempSelected = []"
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
                                            <input type="checkbox" value="{{ $dept->id }}" x-model="tempSelected"
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
                                class="px-5 py-2 rounded-xl bg-[#93F514] hover:bg-[#7edc0b] text-black font-bold text-xs shadow-md shadow-black/25 transition">
                                {{ __('Pilih') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dropdown Semua Tipe Pekerjaan (Employment Type) -->
                <div class="w-full lg:w-48 relative" :class="open ? 'z-50' : 'z-10'" x-data="{
                    open: false,
                    search: '',
                    selected: {{ json_encode($selectedEmploymentTypes ?? []) }},
                    tempSelected: {{ json_encode($selectedEmploymentTypes ?? []) }},
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
                                    {{ count($selectedEmploymentTypes ?? []) }}
                                </span>
                            </template>
                            <span class="truncate" x-text="getDisplayText()">
                                @if (!empty($selectedEmploymentTypes))
                                    @if (count($selectedEmploymentTypes) === 1)
                                        {{ __($selectedEmploymentTypes[0]) }}
                                    @else
                                        {{ count($selectedEmploymentTypes) }} {{ __('Tipe Dipilih') }}
                                    @endif
                                @else
                                    {{ __('Semua Tipe Pekerjaan') }}
                                @endif
                            </span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                            :class="open ? 'rotate-180 text-[#93F514]' : (selected.length > 0 ? 'text-[#93F514]' : '')"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
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
                            <input type="text" x-model="search" placeholder="{{ __('Temukan Tipe Pekerjaan...') }}"
                                class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                        </div>

                        <!-- All Option -->
                        <label
                            class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                            <input type="checkbox" :checked="tempSelected.length === 0" @change="tempSelected = []"
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
                                class="px-5 py-2 rounded-xl bg-[#93F514] hover:bg-[#7edc0b] text-black font-bold text-xs shadow-md shadow-black/25 transition">
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
                            !empty($selectedCompanyIds) ||
                            !empty($selectedDepartmentIds) ||
                            !empty($selectedEmploymentTypes) ||
                            !empty($selectedExperienceLevels))
                        <a href="{{ route('jobs.index') }}" title="{{ __('Reset Filter') }}"
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

        <div x-data="{
            viewMode: localStorage.getItem('mika_jobs_view') || 'grid',
            setView(mode) {
                this.viewMode = mode;
                localStorage.setItem('mika_jobs_view', mode);
            }
        }">
            <!-- Jobs Header / Title & View Mode Controls -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-[#EEEEEE]">
                        @if (!empty($selectedCompanyIds))
                            @if (count($selectedCompanyIds) === 1)
                                {{ __('Lowongan di') }} <span
                                    class="text-[#93F514]">{{ $companies->firstWhere('id', $selectedCompanyIds[0])?->name ?? __('Perusahaan Terpilih') }}</span>
                            @else
                                {{ __('Lowongan di') }} <span class="text-[#93F514]">{{ count($selectedCompanyIds) }} {{ __('Perusahaan Terpilih') }}</span>
                            @endif
                        @elseif (!empty($selectedDepartmentIds))
                            @if (count($selectedDepartmentIds) === 1)
                                {{ __('Lowongan di Departemen') }} <span
                                    class="text-[#93F514]">{{ __($departments->firstWhere('id', $selectedDepartmentIds[0])?->name ?? __('Departemen Terpilih')) }}</span>
                            @else
                                {{ __('Lowongan di') }} <span class="text-[#93F514]">{{ count($selectedDepartmentIds) }} {{ __('Departemen Terpilih') }}</span>
                            @endif
                        @else
                            {{ __('Daftar') }} <span class="text-[#93F514]">{{ __('Semua Lowongan') }}</span>
                        @endif
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-400 mt-0.5">
                        {{ __('Menampilkan total') }} <strong class="text-[#93F514]">{{ $jobs->total() }}</strong> {{ __('posisi lowongan kerja') }}
                    </p>
                </div>

                <div class="flex items-center gap-3 self-end sm:self-auto">
                    @if (
                        !empty($selectedCompanyIds) ||
                            request('search') ||
                            !empty($selectedDepartmentIds) ||
                            !empty($selectedEmploymentTypes) ||
                            !empty($selectedExperienceLevels))
                        <a href="{{ route('jobs.index') }}"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#93F514] hover:text-[#52fa4d] transition shrink-0 px-3 py-1.5 rounded-xl bg-[#93F514]/10 border border-[#93F514]/30 hover:bg-[#93F514]/20">
                            <span>{{ __('Reset Filter') }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif

                    <!-- View Mode Toggle Pill (Grid / List Switcher) -->
                    <div
                        class="view-mode-toggle-pill inline-flex items-center p-1 rounded-2xl bg-[#061506] border border-[#93F514]/30 shadow-inner">
                        <!-- Grid View Button -->
                        <button type="button" @click="setView('grid')"
                            :class="viewMode === 'grid'
                                ?
                                'bg-[#93F514] text-black shadow-md font-bold' :
                                'text-gray-400 hover:text-[#93F514] bg-transparent'"
                            class="px-3 py-2 rounded-xl transition-all duration-200 flex items-center justify-center cursor-pointer outline-none"
                            title="{{ __('Tampilan Grid') }}">
                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <rect x="2" y="2" width="4.5" height="4.5" rx="1" />
                                <rect x="8" y="2" width="4.5" height="4.5" rx="1" />
                                <rect x="14" y="2" width="4.5" height="4.5" rx="1" />
                                <rect x="2" y="8" width="4.5" height="4.5" rx="1" />
                                <rect x="8" y="8" width="4.5" height="4.5" rx="1" />
                                <rect x="14" y="8" width="4.5" height="4.5" rx="1" />
                                <rect x="2" y="14" width="4.5" height="4.5" rx="1" />
                                <rect x="8" y="14" width="4.5" height="4.5" rx="1" />
                                <rect x="14" y="14" width="4.5" height="4.5" rx="1" />
                            </svg>
                        </button>

                        <!-- List View Button -->
                        <button type="button" @click="setView('list')"
                            :class="viewMode === 'list'
                                ?
                                'bg-[#93F514] text-black shadow-md font-bold' :
                                'text-gray-400 hover:text-[#93F514] bg-transparent'"
                            class="px-3 py-2 rounded-xl transition-all duration-200 flex items-center justify-center cursor-pointer outline-none"
                            title="{{ __('Tampilan List') }}">
                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <rect x="2" y="3" width="3.5" height="3.5" rx="0.8" />
                                <rect x="7.5" y="3.5" width="10.5" height="2.5" rx="1" />
                                <rect x="2" y="8.5" width="3.5" height="3.5" rx="0.8" />
                                <rect x="7.5" y="9" width="10.5" height="2.5" rx="1" />
                                <rect x="2" y="14" width="3.5" height="3.5" rx="0.8" />
                                <rect x="7.5" y="14.5" width="10.5" height="2.5" rx="1" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Jobs Content: Grid Mode -->
            <div x-show="viewMode === 'grid'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
                @forelse($jobs as $job)
                    @php
                    // Clean description: insert spaces before strip_tags so block tags don't concatenate words
$rawDesc = $job->localized_description
    ? preg_replace('/<(\/div|\/p|\/li|br\s*\/@endphp|\/h[1-6])>/i', ' ', $job->localized_description)
    : '';
$cleanDesc = !empty($rawDesc)
    ? trim(preg_replace('/\s+/', ' ', strip_tags($rawDesc)))
    : __('Klik tombol detail untuk membaca rincian kualifikasi dan persyaratan lowongan pekerjaan ini.');

// Check if position name is distinct from job title and department
$showPositionBadge =
    $job->position &&
    strtolower(trim($job->position->name)) !== strtolower(trim($job->title)) &&
    strtolower(trim($job->position->name)) !== strtolower(trim($job->department?->name ?? ''));
?>
                    <div class="job-card-item reveal-on-scroll group relative rounded-3xl bg-gradient-to-b from-[#061506] to-[#030803] border border-[#93F514]/30 hover:border-[#93F514] p-6 flex flex-col justify-between transition-all duration-300 hover:shadow-2xl hover:shadow-black/60 hover:-translate-y-1 h-full"
                        data-delay="{{ ($loop->index % 6) * 100 }}">

                        <!-- Top Content Area -->
                        <div class="flex-1 flex flex-col">
                            <!-- Header Card: Logo + Company Info + Employment Type -->
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    @if ($job->company?->logo_url)
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-white border border-[#93F514]/40 p-1.5 flex items-center justify-center shrink-0 overflow-hidden shadow-sm">
                                            <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}"
                                                class="w-full h-full object-contain">
                                        </div>
                                    @else
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-white border border-[#93F514]/40 flex items-center justify-center text-gray-900 font-extrabold text-base shrink-0 shadow-sm">
                                            {{ strtoupper(substr($job->company?->name ?? 'M', 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-xs font-bold text-gray-300 uppercase tracking-wider truncate"
                                            title="{{ $job->company?->name ?? __('Perusahaan Mitra') }}">
                                            {{ $job->company?->name ?? __('Perusahaan Mitra') }}
                                        </h4>
                                        <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                            <span
                                                class="inline-flex items-center text-[11px] font-semibold text-[#93F514] truncate max-w-[130px]">
                                                {{ __($job->department?->name ?? 'Umum') }}
                                            </span>
                                            @if ($showPositionBadge)
                                                <span
                                                    class="inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-[#93F514]/10 text-[#93F514] border border-[#93F514]/30 font-semibold truncate max-w-[120px]">
                                                    {{ __($job->position->name) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap justify-end shrink-0 self-start">
                                    @if ($job->experience_level)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#EEEEEE]/10 border border-white/20 text-gray-200 shadow-sm whitespace-nowrap">
                                            {{ __($job->experience_level) }}
                                        </span>
                                    @endif
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514] whitespace-nowrap shadow-sm">
                                        {{ __($job->employment_type) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Job Title (Consistent 1 line with clean hover state) -->
                            <a href="{{ route('jobs.show', $job->id) }}" class="block mt-1">
                                <h3 class="text-lg font-extrabold text-[#EEEEEE] group-hover:text-[#93F514] transition-colors line-clamp-1 leading-snug"
                                    title="{{ $job->localized_title }}">
                                    {{ $job->localized_title }}
                                </h3>
                            </a>

                            <!-- Description (Fixed height h-10 with line-clamp-2 so every card below has identical vertical positioning) -->
                            <p class="text-xs sm:text-[13px] text-gray-400 mt-2 line-clamp-2 leading-relaxed h-10 overflow-hidden"
                                title="{{ $cleanDesc }}">
                                {{ $cleanDesc }}
                            </p>

                            <!-- Details / Metadata Section -->
                            <div class="mt-auto space-y-2.5 pt-4 border-t border-[#93F514]/15">
                                <!-- Location Row -->
                                <div class="flex items-center gap-2.5 text-xs text-gray-300">
                                    <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="truncate">{{ __($job->location ?? 'Indonesia') }}</span>
                                </div>

                                <!-- Salary Row (Only rendered if salary is specified) -->
                                @if (($job->salary_min && $job->salary_min > 0) || ($job->salary_max && $job->salary_max > 0))
                                    <div class="flex items-center gap-2.5 text-xs">
                                        <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        @if ($job->salary_min && $job->salary_max)
                                            <span class="text-[#93F514] font-semibold truncate">
                                                Rp {{ number_format($job->salary_min, 0, ',', '.') }} –
                                                {{ number_format($job->salary_max, 0, ',', '.') }}
                                            </span>
                                        @elseif ($job->salary_min)
                                            <span class="text-[#93F514] font-semibold truncate">
                                                {{ __('Mulai Rp') }} {{ number_format($job->salary_min, 0, ',', '.') }}
                                            </span>
                                        @elseif ($job->salary_max)
                                            <span class="text-[#93F514] font-semibold truncate">
                                                {{ __('Hingga Rp') }} {{ number_format($job->salary_max, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Deadline Row (Always rendered so heights never jump) -->
                                <div class="flex items-center gap-2.5 text-xs">
                                    <svg class="w-4 h-4 {{ $job->days_remaining === 0 ? 'text-rose-400' : ($job->days_remaining !== null && $job->days_remaining <= 3 ? 'text-amber-400' : 'text-[#93F514]') }} shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    @if ($job->deadline)
                                        <span
                                            class="{{ $job->days_remaining === 0 ? 'text-rose-400 font-bold' : ($job->days_remaining !== null && $job->days_remaining <= 3 ? 'text-amber-400 font-semibold' : 'text-gray-400') }} truncate">
                                            @if ($job->days_remaining === 0)
                                                {{ __('Berakhir Hari Ini!') }}
                                            @elseif ($job->days_remaining !== null && $job->days_remaining <= 3)
                                                {{ __('Sisa') }} {{ $job->days_remaining }} {{ __('Hari') }}
                                                ({{ \Carbon\Carbon::parse($job->deadline)->translatedFormat('d M') }})
                                            @else
                                                {{ __('Batas:') }} {{ \Carbon\Carbon::parse($job->deadline)->translatedFormat('d M Y') }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400">
                                            {{ __('Pendaftaran Terbuka') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Footer Card Action: Pinned firmly at bottom -->
                        <div class="mt-6 pt-4 border-t border-[#93F514]/15 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                                <svg class="w-3.5 h-3.5 text-[#93F514]/70 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ __('Kuota:') }} <strong
                                        class="text-[#EEEEEE] font-bold">{{ $job->quota ?? 1 }}</strong>@if ($job->is_quota_full) <span class="text-[10px] text-amber-400 font-semibold">({{ __('Terpenuhi') }})</span>@endif</span>
                            </div>
                            <a href="{{ route('jobs.show', $job->id) }}"
                                class="job-detail-btn inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#93F514] hover:bg-[#a6ff2e] text-black font-extrabold text-xs shadow-md shadow-black/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                                <span>{{ __('Detail & Lamar') }}</span>
                                <svg class="w-3 h-3 transition-transform duration-200 group-hover:translate-x-0.5"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center rounded-3xl bg-[#050e05] border border-[#93F514]/30">
                        <div
                            class="w-16 h-16 mx-auto rounded-full bg-[#93F514]/15 flex items-center justify-center text-[#93F514] mb-4">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#EEEEEE]">{{ __('Tidak Ada Lowongan Ditemukan') }}</h3>
                        <p class="text-xs sm:text-sm text-gray-400 mt-1">
                            {{ __('Silakan ubah filter atau kata kunci pencarian Anda.') }}
                        </p>
                        <a href="{{ route('jobs.index') }}"
                            class="inline-block mt-4 px-5 py-2 rounded-full bg-[#93F514] text-black font-extrabold text-xs">
                            {{ __('Reset Semua Filter') }}
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Jobs Content: List Mode -->
            <div x-show="viewMode === 'list'" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="flex flex-col gap-4">
                @forelse($jobs as $job)
                    @php
                        $rawDescList = $job->localized_description
                        ? preg_replace('/<(\/div|\/p|\/li|br\s*\/@endphp|\/h[1-6])>/i', ' ', $job->localized_description)
    : '';
$cleanDescList = !empty($rawDescList)
    ? trim(preg_replace('/\s+/', ' ', strip_tags($rawDescList)))
    : '';
$showPositionBadgeList =
    $job->position &&
    strtolower(trim($job->position->name)) !== strtolower(trim($job->title)) &&
    strtolower(trim($job->position->name)) !== strtolower(trim($job->department?->name ?? ''));
?>
                    <div class="job-card-item reveal-on-scroll group relative rounded-3xl bg-gradient-to-r from-[#061506] via-[#051205] to-[#030803] border border-[#93F514]/30 hover:border-[#93F514] p-5 sm:p-6 transition-all duration-300 hover:shadow-2xl hover:shadow-black/60 hover:-translate-y-0.5 flex flex-col md:flex-row items-start md:items-center justify-between gap-5"
                        data-delay="{{ ($loop->index % 6) * 80 }}">

                        <!-- Left / Main Info -->
                        <div class="flex items-start sm:items-center gap-4.5 flex-1 min-w-0">
                            <!-- Logo -->
                            @if ($job->company?->logo_url)
                                <div
                                    class="w-14 h-14 mr-5 rounded-2xl bg-white border border-[#93F514]/40 p-2 flex items-center justify-center shrink-0 overflow-hidden shadow-sm">
                                    <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}"
                                        class="w-full h-full object-contain">
                                </div>
                            @else
                                <div
                                    class="w-14 h-14 mr-5 rounded-2xl bg-white border border-[#93F514]/40 flex items-center justify-center text-gray-900 font-extrabold text-xl shrink-0 shadow-sm">
                                    {{ strtoupper(substr($job->company?->name ?? 'M', 0, 2)) }}
                                </div>
                            @endif

                            <div class="flex-1 min-w-0 space-y-1.5">
                                <!-- Header Badges -->
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="text-xs font-bold text-gray-300 uppercase tracking-wider truncate max-w-[200px]"
                                        title="{{ $job->company?->name ?? __('Perusahaan Mitra') }}">
                                        {{ $job->company?->name ?? __('Perusahaan Mitra') }}
                                    </span>
                                    <span class="text-xs text-[#93F514] font-semibold">
                                        {{ __($job->department?->name ?? 'Umum') }}
                                    </span>
                                    @if ($showPositionBadgeList)
                                        <span
                                            class="inline-flex items-center gap-1 text-[11px] px-2.5 py-0.5 rounded-full bg-[#93F514]/10 text-[#93F514] border border-[#93F514]/30 font-semibold">
                                            <span>{{ __('Posisi:') }} {{ __($job->position->name) }}</span>
                                        </span>
                                    @endif
                                    @if ($job->experience_level)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#EEEEEE]/10 border border-white/20 text-gray-200">
                                            {{ __($job->experience_level) }}
                                        </span>
                                    @endif
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#93F514]/15 border border-[#93F514]/40 text-[#93F514]">
                                        {{ __($job->employment_type) }}
                                    </span>
                                </div>

                                <!-- Job Title -->
                                <a href="{{ route('jobs.show', $job->id) }}" class="block">
                                    <h3
                                        class="text-lg sm:text-xl font-extrabold text-[#EEEEEE] group-hover:text-[#93F514] transition-colors truncate">
                                        {{ $job->localized_title }}
                                    </h3>
                                </a>

                                <!-- Meta Badges: Location, Salary, Deadline, Quota -->
                                <div class="flex flex-wrap items-center gap-y-1.5 gap-x-4 text-xs text-gray-400 pt-1">
                                    <div class="flex items-center gap-1.5 text-gray-300">
                                        <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ __($job->location ?? 'Indonesia') }}</span>
                                    </div>

                                    @if (($job->salary_min && $job->salary_min > 0) || ($job->salary_max && $job->salary_max > 0))
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            @if ($job->salary_min && $job->salary_max)
                                                <span class="text-[#93F514] font-semibold">Rp
                                                    {{ number_format($job->salary_min, 0, ',', '.') }} –
                                                    {{ number_format($job->salary_max, 0, ',', '.') }}</span>
                                            @elseif ($job->salary_min)
                                                <span class="text-[#93F514] font-semibold">{{ __('Mulai Rp') }}
                                                    {{ number_format($job->salary_min, 0, ',', '.') }}</span>
                                            @elseif ($job->salary_max)
                                                <span class="text-[#93F514] font-semibold">{{ __('Hingga Rp') }}
                                                    {{ number_format($job->salary_max, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-1.5 text-xs">
                                        <svg class="w-4 h-4 {{ $job->days_remaining === 0 ? 'text-rose-400' : ($job->days_remaining !== null && $job->days_remaining <= 3 ? 'text-amber-400' : 'text-[#93F514]') }} shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        @if ($job->deadline)
                                            <span
                                                class="{{ $job->days_remaining === 0 ? 'text-rose-400 font-bold' : ($job->days_remaining !== null && $job->days_remaining <= 3 ? 'text-amber-400 font-semibold' : 'text-gray-400') }}">
                                                @if ($job->days_remaining === 0)
                                                    {{ __('Berakhir Hari Ini!') }}
                                                @elseif ($job->days_remaining !== null && $job->days_remaining <= 3)
                                                    {{ __('Sisa') }} {{ $job->days_remaining }} {{ __('Hari') }}
                                                    ({{ \Carbon\Carbon::parse($job->deadline)->translatedFormat('d M') }})
                                                @else
                                                    {{ __('Batas:') }} {{ \Carbon\Carbon::parse($job->deadline)->translatedFormat('d M Y') }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-gray-400">{{ __('Pendaftaran Terbuka') }}</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1.5 text-gray-400">
                                        <svg class="w-3.5 h-3.5 text-[#93F514]/70 shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ __('Kuota:') }} <strong
                                                class="text-[#EEEEEE] font-bold">{{ $job->quota ?? 1 }}</strong>@if ($job->is_quota_full) <span class="text-[10px] text-amber-400 font-semibold">({{ __('Terpenuhi') }})</span>@endif</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right / Action -->
                        <div
                            class="w-full md:w-auto flex md:flex-col items-center md:items-end justify-between md:justify-center gap-3 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-[#93F514]/15">
                            <a href="{{ route('jobs.show', $job->id) }}"
                                class="job-detail-btn w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#93F514] hover:bg-[#a6ff2e] text-black font-extrabold text-xs sm:text-sm shadow-md shadow-black/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 text-center">
                                <span>{{ __('Detail & Lamar') }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center rounded-3xl bg-[#050e05] border border-[#93F514]/30">
                        <div
                            class="w-16 h-16 mx-auto rounded-full bg-[#93F514]/15 flex items-center justify-center text-[#93F514] mb-4">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#EEEEEE]">{{ __('Tidak Ada Lowongan Ditemukan') }}</h3>
                        <p class="text-xs sm:text-sm text-gray-400 mt-1">
                            {{ __('Silakan ubah filter atau kata kunci pencarian Anda.') }}
                        </p>
                        <a href="{{ route('jobs.index') }}"
                            class="inline-block mt-4 px-5 py-2 rounded-full bg-[#93F514] text-black font-extrabold text-xs">
                            {{ __('Reset Semua Filter') }}
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-10 mb-16">
            {{ $jobs->links('frontend.components.pagination') }}
        </div>

    </div>
@endsection
