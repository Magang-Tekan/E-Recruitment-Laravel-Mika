@extends('frontend.layouts.app')

@section('title', __('Beranda') . ' - Mika Career')

@section('content')
    <div class="relative overflow-hidden -mt-[88px] sm:-mt-[96px]">

        <!-- ==================== HERO SECTION (IMAGE BACKGROUND + MULTI-LAYER DARK GRADIENTS) ==================== -->
        <section
            class="relative z-20 min-h-screen flex items-center justify-center pt-[120px] sm:pt-[128px] pb-10 sm:pb-12 px-4 sm:px-6 lg:px-8 overflow-visible bg-[#040804]">

            <!-- Hero Background Image (Clear & Crisp) -->
            <div class="absolute inset-0 z-0">
                <img src="{{ $heroSettings['bg_image'] ?? 'https://i.pinimg.com/736x/20/dc/f0/20dcf0cfb0122a38d938e3c3011fb0d4.jpg' }}"
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
                    {{ $heroSettings['prefix'] ?? __('Temukan Karir Impian,') }} <br>
                    <span class="text-[#93F514]">
                        {{ $heroSettings['highlight'] ?? __('Wujudkan Potensi Terbaikmu') }}
                    </span>
                </h1>

                <!-- Subtitle Description / Slogan Layer -->
                <p
                    class="mt-6 text-base sm:text-xl text-gray-200 max-w-2xl font-normal leading-relaxed drop-shadow-[0_2px_8px_rgba(0,0,0,0.9)]">
                    {{ $heroSettings['description'] ?? __('Bergabunglah bersama ribuan profesional bertalenta. Jelajahi lowongan kerja, ikuti tes seleksi online terintegrasi, dan raih karir masa depan sekarang.') }}
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
                <div class="w-full max-w-5xl mt-10 p-2 sm:p-2.5 rounded-2xl lg:rounded-full bg-[#EEEEEE] text-gray-800 shadow-2xl shadow-black/80 border border-white/40 relative transition-all duration-200"
                    style="background-color: #EEEEEE !important;"
                    x-data="{
                        openDropdown: null,

                        // Perusahaan
                        companySearch: '',
                        selectedCompanies: {{ json_encode(array_map('strval', $homeSelectedCompanies ?? [])) }},
                        tempCompanies: {{ json_encode(array_map('strval', $homeSelectedCompanies ?? [])) }},
                        companiesMap: {
                            @foreach ($companies as $comp)
                                '{{ $comp->id }}': '{{ addslashes($comp->name) }}',
                            @endforeach
                        },

                        // Departemen
                        departmentSearch: '',
                        selectedDepartments: {{ json_encode(array_map('strval', $homeSelectedDepartments ?? [])) }},
                        tempDepartments: {{ json_encode(array_map('strval', $homeSelectedDepartments ?? [])) }},
                        allDepartments: [
                            @foreach ($departments as $dept)
                                {
                                    id: '{{ $dept->id }}',
                                    company_id: '{{ $dept->company_id }}',
                                    name: '{{ addslashes(__($dept->name)) }}'
                                },
                            @endforeach
                        ],

                        // Tipe Pekerjaan
                        typeSearch: '',
                        selectedTypes: {{ json_encode($homeSelectedTypes ?? []) }},
                        tempTypes: {{ json_encode($homeSelectedTypes ?? []) }},
                        types: ['Magang', 'Full Time', 'Part Time', 'Contract', 'Freelance', 'Remote'],
                        typesMap: {
                            'Magang': '{{ __('Magang') }}',
                            'Full Time': '{{ __('Full Time') }}',
                            'Part Time': '{{ __('Part Time') }}',
                            'Contract': '{{ __('Contract') }}',
                            'Freelance': '{{ __('Freelance') }}',
                            'Remote': '{{ __('Remote') }}'
                        },

                        // Toggle Popover
                        toggleDropdown(name) {
                            if (this.openDropdown === name) {
                                this.openDropdown = null;
                            } else {
                                this.openDropdown = name;
                                if (name === 'company') {
                                    this.tempCompanies = [...this.selectedCompanies];
                                    this.companySearch = '';
                                } else if (name === 'department') {
                                    this.tempDepartments = [...this.selectedDepartments];
                                    this.departmentSearch = '';
                                } else if (name === 'type') {
                                    this.tempTypes = [...this.selectedTypes];
                                    this.typeSearch = '';
                                }
                            }
                        },
                        closeAll() {
                            this.openDropdown = null;
                        },

                        // Company Handlers
                        getCompanyDisplayText() {
                            if (!this.selectedCompanies || this.selectedCompanies.length === 0) return '{{ __('Semua Perusahaan') }}';
                            if (this.selectedCompanies.length === 1) {
                                return this.companiesMap[this.selectedCompanies[0]] || '1 {{ __('Perusahaan') }}';
                            }
                            return this.selectedCompanies.length + ' {{ __('Perusahaan') }}';
                        },
                        applyCompany() {
                            this.selectedCompanies = [...this.tempCompanies];
                            this.pruneInvalidDepartments();
                            this.openDropdown = null;
                        },
                        resetCompany() {
                            this.tempCompanies = [];
                            this.selectedCompanies = [];
                            this.pruneInvalidDepartments();
                            this.openDropdown = null;
                        },

                        // Department Handlers (Dinamis sesuai perusahaan yang dipilih)
                        get filteredDepartments() {
                            let list = this.allDepartments;
                            // Filter berdasarkan perusahaan terpilih
                            if (this.selectedCompanies && this.selectedCompanies.length > 0) {
                                const selectedCompanyIds = this.selectedCompanies.map(String);
                                list = list.filter(d => selectedCompanyIds.includes(String(d.company_id)));
                            }
                            // Filter berdasarkan input pencarian nama departemen
                            if (this.departmentSearch && this.departmentSearch.trim() !== '') {
                                const s = this.departmentSearch.toLowerCase().trim();
                                list = list.filter(d => d.name.toLowerCase().includes(s));
                            }
                            return list;
                        },
                        getDepartmentDisplayText() {
                            if (!this.selectedDepartments || this.selectedDepartments.length === 0) return '{{ __('Semua Departemen') }}';
                            if (this.selectedDepartments.length === 1) {
                                const found = this.allDepartments.find(d => String(d.id) === String(this.selectedDepartments[0]));
                                return found ? found.name : '1 {{ __('Departemen') }}';
                            }
                            return this.selectedDepartments.length + ' {{ __('Departemen') }}';
                        },
                        applyDepartment() {
                            this.selectedDepartments = [...this.tempDepartments];
                            this.openDropdown = null;
                        },
                        resetDepartment() {
                            this.tempDepartments = [];
                            this.selectedDepartments = [];
                            this.openDropdown = null;
                        },
                        pruneInvalidDepartments() {
                            if (this.selectedCompanies.length > 0) {
                                const validDeptIds = this.allDepartments
                                    .filter(d => this.selectedCompanies.map(String).includes(String(d.company_id)))
                                    .map(d => String(d.id));
                                this.selectedDepartments = this.selectedDepartments.filter(id => validDeptIds.includes(String(id)));
                                this.tempDepartments = this.tempDepartments.filter(id => validDeptIds.includes(String(id)));
                            }
                        },

                        // Type Handlers
                        getTypeDisplayText() {
                            if (!this.selectedTypes || this.selectedTypes.length === 0) return '{{ __('Semua Tipe Pekerjaan') }}';
                            if (this.selectedTypes.length === 1) {
                                return this.typesMap[this.selectedTypes[0]] || this.selectedTypes[0];
                            }
                            return this.selectedTypes.length + ' {{ __('Tipe Dipilih') }}';
                        },
                        applyType() {
                            this.selectedTypes = [...this.tempTypes];
                            this.openDropdown = null;
                        },
                        resetType() {
                            this.tempTypes = [];
                            this.selectedTypes = [];
                            this.openDropdown = null;
                        }
                    }"
                    :class="openDropdown ? 'z-[100]' : 'z-30'"
                    @click.outside="closeAll()">

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

                        <!-- Dropdown 1: Semua Perusahaan -->
                        <div class="w-full lg:w-56 relative" :class="openDropdown === 'company' ? 'z-[105]' : 'z-10'">

                            <template x-for="id in selectedCompanies" :key="'c-' + id">
                                <input type="hidden" name="company_id[]" :value="id">
                            </template>

                            <button type="button" @click="toggleDropdown('company')"
                                :class="selectedCompanies.length > 0 ? 'text-[#93F514] font-bold' : 'text-gray-700'"
                                class="w-full py-2.5 px-4 flex items-center justify-between text-left text-sm transition cursor-pointer">
                                <div class="flex items-center gap-2 truncate">
                                    <template x-if="selectedCompanies.length > 0">
                                        <span
                                            class="min-w-5 h-5 px-1 rounded-full bg-[#93F514] text-black font-extrabold text-xs flex items-center justify-center shrink-0 shadow-sm"
                                            x-text="selectedCompanies.length">
                                        </span>
                                    </template>
                                    <span class="truncate" x-text="getCompanyDisplayText()">
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
                                    :class="openDropdown === 'company' ? 'rotate-180 text-[#93F514]' : (selectedCompanies.length > 0 ? 'text-[#93F514]' : '')"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Popover Panel Perusahaan -->
                            <div x-show="openDropdown === 'company'" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                class="absolute top-full left-0 lg:left-auto lg:right-0 mt-2 w-80 sm:w-[460px] max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 z-[110] text-gray-800 flex flex-col">

                                <!-- Search Input -->
                                <div class="relative mb-3">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" x-model="companySearch" placeholder="{{ __('Temukan perusahaan...') }}"
                                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                                </div>

                                <!-- All Option -->
                                <label
                                    class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                                    <input type="checkbox" :checked="tempCompanies.length === 0" @change="tempCompanies = []"
                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer">
                                    <span>{{ __('Semua Perusahaan') }}</span>
                                </label>

                                <!-- Group Title -->
                                <div
                                    class="mt-3 mb-1.5 flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <span>{{ __('Pilihan Perusahaan') }}</span>
                                    <span x-show="tempCompanies.length > 0" class="text-[#4fa304] font-semibold lowercase"
                                        x-text="tempCompanies.length + ' {{ __('dipilih') }}'"></span>
                                </div>

                                <!-- 2-Column Grid List items (Compact & Scrollable) -->
                                <div class="max-h-40 overflow-y-auto pr-1.5 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_transparent] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-1">
                                        @if (isset($companies))
                                            @foreach ($companies as $comp)
                                                <label
                                                    class="flex items-center gap-2.5 py-1.5 px-1.5 rounded-lg text-xs text-gray-600 hover:text-black hover:bg-gray-50 cursor-pointer transition"
                                                    x-show="!companySearch || '{{ strtolower(addslashes($comp->name)) }}'.includes(companySearch.toLowerCase())">
                                                    <input type="checkbox" value="{{ $comp->id }}"
                                                        x-model="tempCompanies"
                                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer shrink-0">
                                                    <span class="truncate">{{ $comp->name }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Footer Buttons (Always Visible at Bottom) -->
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                                    <button type="button" @click="resetCompany()"
                                        class="text-xs font-semibold text-gray-500 hover:text-[#93F514] transition cursor-pointer">
                                        {{ __('Atur Ulang') }}
                                    </button>
                                    <button type="button" @click="applyCompany()"
                                        class="px-5 py-2 rounded-xl bg-black hover:bg-gray-800 text-white dark:bg-[#93F514] dark:hover:bg-[#7edc0b] dark:text-black font-bold text-xs shadow-sm transition cursor-pointer">
                                        {{ __('Pilih') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown 2: Semua Departemen / Fungsi (Dinamis Sesuai Perusahaan Terpilih) -->
                        <div class="w-full lg:w-56 relative" :class="openDropdown === 'department' ? 'z-[105]' : 'z-10'">

                            <template x-for="id in selectedDepartments" :key="'d-' + id">
                                <input type="hidden" name="department_id[]" :value="id">
                            </template>

                            <button type="button" @click="toggleDropdown('department')"
                                :class="selectedDepartments.length > 0 ? 'text-[#93F514] font-bold' : 'text-gray-700'"
                                class="w-full py-2.5 px-4 flex items-center justify-between text-left text-sm transition cursor-pointer">
                                <div class="flex items-center gap-2 truncate">
                                    <template x-if="selectedDepartments.length > 0">
                                        <span
                                            class="min-w-5 h-5 px-1 rounded-full bg-[#93F514] text-black font-extrabold text-xs flex items-center justify-center shrink-0 shadow-sm"
                                            x-text="selectedDepartments.length">
                                        </span>
                                    </template>
                                    <span class="truncate" x-text="getDepartmentDisplayText()">
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
                                    :class="openDropdown === 'department' ? 'rotate-180 text-[#93F514]' : (selectedDepartments.length > 0 ? 'text-[#93F514]' : '')"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Popover Panel Departemen -->
                            <div x-show="openDropdown === 'department'" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                class="absolute top-full left-0 lg:left-auto lg:right-0 mt-2 w-80 sm:w-[460px] max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 z-[110] text-gray-800 flex flex-col">

                                <!-- Search Input -->
                                <div class="relative mb-3">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" x-model="departmentSearch"
                                        placeholder="{{ __('Temukan fungsi / departemen...') }}"
                                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                                </div>

                                <!-- Filter Perusahaan Aktif Indicator -->
                                <template x-if="selectedCompanies.length > 0">
                                    <div class="mb-2.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-[11px] font-medium text-emerald-800 flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-1.5 truncate">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="truncate">
                                                {{ __('Dari:') }} <span class="font-bold" x-text="getCompanyDisplayText()"></span>
                                            </span>
                                        </div>
                                        <button type="button" @click="resetCompany()" class="text-[10px] text-emerald-700 hover:text-emerald-950 underline shrink-0 font-bold cursor-pointer">
                                            {{ __('Lihat Semua') }}
                                        </button>
                                    </div>
                                </template>

                                <!-- All Option -->
                                <label
                                    class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                                    <input type="checkbox" :checked="tempDepartments.length === 0"
                                        @change="tempDepartments = []"
                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer">
                                    <span>{{ __('Semua Departemen') }}</span>
                                </label>

                                <!-- Group Title -->
                                <div
                                    class="mt-3 mb-1.5 flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <span>{{ __('Pilihan Departemen') }}</span>
                                    <span x-show="tempDepartments.length > 0" class="text-[#4fa304] font-semibold lowercase"
                                        x-text="tempDepartments.length + ' {{ __('dipilih') }}'"></span>
                                </div>

                                <!-- 2-Column Grid List items (Compact & Scrollable, Reaktif Terhadap Filter) -->
                                <div class="max-h-40 overflow-y-auto pr-1.5 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_transparent] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-1">
                                        <template x-for="dept in filteredDepartments" :key="dept.id">
                                            <label class="flex items-center gap-2.5 py-1.5 px-1.5 rounded-lg text-xs text-gray-600 hover:text-black hover:bg-gray-50 cursor-pointer transition">
                                                <input type="checkbox" :value="String(dept.id)"
                                                    x-model="tempDepartments"
                                                    class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer shrink-0">
                                                <span class="truncate" x-text="dept.name"></span>
                                            </label>
                                        </template>
                                    </div>

                                    <!-- Empty state jika tidak ada departemen untuk perusahaan terpilih -->
                                    <template x-if="filteredDepartments.length === 0">
                                        <div class="py-6 px-3 text-center text-xs text-gray-400">
                                            <svg class="w-6 h-6 mx-auto mb-1.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>{{ __('Tidak ada departemen yang sesuai.') }}</span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Footer Buttons (Always Visible at Bottom) -->
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                                    <button type="button" @click="resetDepartment()"
                                        class="text-xs font-semibold text-gray-500 hover:text-[#93F514] transition cursor-pointer">
                                        {{ __('Atur Ulang') }}
                                    </button>
                                    <button type="button" @click="applyDepartment()"
                                        class="px-5 py-2 rounded-xl bg-black hover:bg-gray-800 text-white dark:bg-[#93F514] dark:hover:bg-[#7edc0b] dark:text-black font-bold text-xs shadow-sm transition cursor-pointer">
                                        {{ __('Pilih') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown 3: Semua Tipe (Employment Type) -->
                        <div class="w-full lg:w-48 relative" :class="openDropdown === 'type' ? 'z-[105]' : 'z-10'">

                            <template x-for="t in selectedTypes" :key="'t-' + t">
                                <input type="hidden" name="employment_type[]" :value="t">
                            </template>

                            <button type="button" @click="toggleDropdown('type')"
                                :class="selectedTypes.length > 0 ? 'text-[#93F514] font-bold' : 'text-gray-700'"
                                class="w-full py-2.5 px-4 flex items-center justify-between text-left text-sm transition cursor-pointer">
                                <div class="flex items-center gap-2 truncate">
                                    <template x-if="selectedTypes.length > 0">
                                        <span
                                            class="min-w-5 h-5 px-1 rounded-full bg-[#93F514] text-black font-extrabold text-xs flex items-center justify-center shrink-0 shadow-sm"
                                            x-text="selectedTypes.length">
                                        </span>
                                    </template>
                                    <span class="truncate" x-text="getTypeDisplayText()">
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
                                    :class="openDropdown === 'type' ? 'rotate-180 text-[#93F514]' : (selectedTypes.length > 0 ? 'text-[#93F514]' : '')"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Popover Panel Tipe Pekerjaan -->
                            <div x-show="openDropdown === 'type'" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                class="absolute top-full left-0 lg:left-auto lg:right-0 mt-2 w-80 sm:w-[420px] max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-gray-100 p-4 z-[110] text-gray-800 flex flex-col">

                                <!-- Search Input -->
                                <div class="relative mb-3">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" x-model="typeSearch"
                                        placeholder="{{ __('Temukan Tipe Pekerjaan...') }}"
                                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#93F514]/40 focus:border-[#93F514]">
                                </div>

                                <!-- All Option -->
                                <label
                                    class="flex items-center gap-3 py-2 px-1 text-xs font-semibold text-gray-700 hover:text-black cursor-pointer border-b border-gray-100">
                                    <input type="checkbox" :checked="tempTypes.length === 0"
                                        @change="tempTypes = []"
                                        class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer">
                                    <span>{{ __('Semua Tipe Pekerjaan') }}</span>
                                </label>

                                <!-- Group Title -->
                                <div
                                    class="mt-3 mb-1.5 flex items-center justify-between text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <span>{{ __('Pilihan Tipe Pekerjaan') }}</span>
                                    <span x-show="tempTypes.length > 0" class="text-[#4fa304] font-semibold lowercase"
                                        x-text="tempTypes.length + ' {{ __('dipilih') }}'"></span>
                                </div>

                                <!-- 2-Column Grid List items (Compact & Scrollable) -->
                                <div class="max-h-40 overflow-y-auto pr-1.5 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_transparent] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-1">
                                        <template
                                            x-for="type in types.filter(t => !typeSearch || t.toLowerCase().includes(typeSearch.toLowerCase()))"
                                            :key="type">
                                            <label
                                                class="flex items-center gap-2.5 py-1.5 px-1.5 rounded-lg text-xs text-gray-600 hover:text-black hover:bg-gray-50 cursor-pointer transition">
                                                <input type="checkbox" :value="type" x-model="tempTypes"
                                                    class="w-4 h-4 rounded border-gray-300 text-[#93F514] focus:ring-[#93F514] cursor-pointer shrink-0">
                                                <span x-text="typesMap[type] || type" class="truncate"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <!-- Footer Buttons (Always Visible at Bottom) -->
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                                    <button type="button" @click="resetType()"
                                        class="text-xs font-semibold text-gray-500 hover:text-[#93F514] transition cursor-pointer">
                                        {{ __('Atur Ulang') }}
                                    </button>
                                    <button type="button" @click="applyType()"
                                        class="px-5 py-2 rounded-xl bg-black hover:bg-gray-800 text-white dark:bg-[#93F514] dark:hover:bg-[#7edc0b] dark:text-black font-bold text-xs shadow-sm transition cursor-pointer">
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
                @if (!empty($popularSearches))
                <div class="mt-3 sm:mt-3.5 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 text-xs text-gray-400 relative z-10">
                    <span class="font-bold text-[#93F514]">{{ __('Pencarian Populer:') }}</span>
                    @foreach ($popularSearches as $tag)
                        <a href="{{ route('jobs.index', $tag['query']) }}" title="{{ $tag['label'] }}"
                            class="inline-block max-w-[180px] sm:max-w-[220px] truncate px-2.5 py-0.5 rounded-full bg-[#051205] hover:bg-[#93F514]/20 border border-[#93F514]/30 text-gray-300 hover:text-[#93F514] transition text-[11px] sm:text-xs">{{ $tag['label'] }}</a>
                    @endforeach
                </div>
                @endif

                <!-- Quick Stats Summary -->
                <div
                    class="mt-4 sm:mt-5 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 w-full max-w-3xl border-t border-[#93F514]/20 pt-4 sm:pt-4.5 relative z-10">
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

        <!-- ALUR PENDAFTARAN & SELEKSI (DATA DINAMIS DARI ADMIN) -->
        @include('frontend.home.partials.registration-flow')

        <!-- ==================== TEMUKAN LOWONGAN BERDASARKAN PERUSAHAAN ==================== -->
        @if (isset($companies) && $companies->count() > 0)
            @php
                $orderedCompanies = $companies->sortBy(function ($comp) {
                    $name = strtolower($comp->name);
                    return (str_contains($name, 'mitra karya analitika') || str_contains($name, 'mika')) ? 0 : 1;
                })->values();

                // Deteksi apakah ada setidaknya satu perusahaan yang memiliki foto
                $hasAnyPhotos = $orderedCompanies->contains(function ($comp) {
                    return !empty($comp->card_images);
                });
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

                @if (!$hasAnyPhotos)
                    <!-- KONDISI 1: SEMUA PERUSAHAAN TANPA FOTO (TAMPILAN COMPACT SESUAI GAMBAR 2) -->
                    <div class="flex flex-wrap justify-center gap-4 sm:gap-5">
                        @foreach ($orderedCompanies as $comp)
                            @php
                                $jobCount = $comp->jobs_count ?? $comp->jobs()->where('status', 'Open')->count();
                            @endphp
                            <a href="{{ route('jobs.index', ['company_id' => [$comp->id]]) }}"
                                class="company-card reveal-on-scroll group relative rounded-2xl p-4 sm:p-5 border transition-all duration-300 flex flex-col justify-between w-full sm:w-[calc(50%-10px)] md:w-[calc(33.333%-14px)] lg:w-[calc(25%-16px)] max-w-sm
                                       bg-gradient-to-b from-[#061506] to-[#030a03] border-[#93F514]/25 shadow-md hover:border-[#93F514] hover:shadow-xl hover:shadow-[#93F514]/15 hover:-translate-y-1 block text-left"
                                data-delay="{{ ($loop->index % 4) * 100 }}">

                                <!-- Header: Logo + Nama + Lokasi Perusahaan -->
                                <div class="flex items-start gap-3.5">
                                    @if ($comp->logo_url)
                                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-white border border-[#93F514]/30 p-1.5 sm:p-2 flex items-center justify-center shrink-0 overflow-hidden shadow-sm group-hover:border-[#93F514] transition-colors">
                                            <img src="{{ $comp->logo_url }}" alt="{{ $comp->name }}" class="w-full h-full object-contain">
                                        </div>
                                    @else
                                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-white border border-[#93F514]/40 flex items-center justify-center text-gray-900 font-extrabold text-base sm:text-lg shrink-0 group-hover:bg-gray-100 transition-colors">
                                            {{ strtoupper(substr($comp->name, 0, 2)) }}
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs sm:text-sm font-bold text-[#EEEEEE] group-hover:text-[#93F514] transition-colors truncate uppercase tracking-tight">
                                            {{ $comp->name }}
                                        </h3>
                                        <p class="text-[11px] sm:text-xs text-gray-400 mt-0.5 truncate font-medium">
                                            {{ $comp->city ?? ($comp->province ?? __('Mitra Perusahaan')) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Footer: Lowongan Tersedia Badge -->
                                <div class="mt-4 pt-3 border-t border-[#93F514]/15 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 text-xs {{ $jobCount > 0 ? 'text-[#93F514] font-bold' : 'text-gray-400' }}">
                                        <span>{{ $jobCount }} {{ __('Lowongan Tersedia') }}</span>
                                    </span>

                                    <span class="text-xs text-gray-400 group-hover:text-[#93F514] group-hover:translate-x-1 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <!-- KONDISI 2: HYBRID / DENGAN FOTO (TAMPILAN TINGGI SERAGAM & ELEGAN SESUAI GAMBAR 1) -->
                    <div class="flex flex-wrap justify-center gap-3.5 sm:gap-4.5">
                        @foreach ($orderedCompanies as $comp)
                            @php
                                $jobCount = $comp->jobs_count ?? $comp->jobs()->where('status', 'Open')->count();
                                $images = $comp->card_images ?? [];
                                $hasPhotos = !empty($images);
                            @endphp
                            <a href="{{ route('jobs.index', ['company_id' => [$comp->id]]) }}"
                               x-data="{
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
                               class="company-card reveal-on-scroll group relative w-full sm:w-[calc(50%-10px)] md:w-[245px] lg:w-[260px] max-w-[270px] h-[280px] sm:h-[295px] rounded-2xl p-3 sm:p-3.5 border flex flex-col justify-between bg-gradient-to-b from-[#061506] to-[#030a03] border-[#93F514]/25 shadow-md hover:border-[#93F514] hover:-translate-y-1.5 hover:shadow-xl hover:shadow-[#93F514]/15 transition-all duration-300 cursor-pointer block text-left"
                               data-delay="{{ ($loop->index % 4) * 100 }}">

                                <!-- Header: Logo + Nama & Lokasi Perusahaan -->
                                <div class="flex items-center gap-2">
                                    @if ($comp->logo_url)
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white border border-[#93F514]/30 p-1 flex items-center justify-center shrink-0 overflow-hidden shadow-sm group-hover:border-[#93F514] transition-colors">
                                            <img src="{{ $comp->logo_url }}" alt="{{ $comp->name }}" class="w-full h-full object-contain">
                                        </div>
                                    @else
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white border border-[#93F514]/40 flex items-center justify-center text-gray-900 font-black text-[10px] shrink-0 group-hover:bg-gray-100 transition-colors">
                                            {{ strtoupper(substr($comp->name, 0, 2)) }}
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xs sm:text-[13px] font-extrabold text-[#EEEEEE] group-hover:text-[#93F514] transition-colors tracking-tight truncate uppercase">
                                            {{ $comp->name }}
                                        </h3>
                                        <p class="text-[10px] sm:text-[11px] text-gray-400 truncate font-medium">
                                            {{ $comp->city ?? ($comp->province ?? __('Mitra Perusahaan')) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Bagian Tengah: Foto Slider ATAU Kartu Identitas Elegan jika Belum Ada Foto (Hybrid) -->
                                @if ($hasPhotos)
                                    <!-- Jika Perusahaan Memiliki Foto: Slider Otomatis (Canvas PUTIH Bersih di Dark & Light Mode agar Teks/Logo Gelap Selalu Terbaca) -->
                                    <div class="company-card-img-wrap relative w-full h-28 sm:h-32 rounded-xl overflow-hidden bg-white border border-gray-200/90 dark:border-white/10 my-1.5 shadow-sm">
                                        <div class="flex h-full transition-transform duration-700 ease-out"
                                             :style="`transform: translateX(-${current * 100}%);`">
                                            @foreach ($images as $img)
                                                <div class="company-card-img-slide w-full h-full flex-shrink-0 relative overflow-hidden flex items-center justify-center bg-white p-2">
                                                    <!-- Foto / Logo utuh tanpa crop di atas canvas putih bersih -->
                                                    <img src="{{ $img }}" alt="{{ $comp->name }}" class="relative z-10 max-w-full max-h-full object-contain drop-shadow-sm">
                                                </div>
                                            @endforeach
                                        </div>

                                        @if (count($images) > 1)
                                            <div class="absolute bottom-1.5 left-0 right-0 flex justify-center gap-1 z-30 pointer-events-none">
                                                @foreach ($images as $idx => $img)
                                                    <span class="h-1 rounded-full transition-all duration-300"
                                                          :class="current === {{ $idx }} ? 'w-3 bg-[#168038]' : 'w-1 bg-gray-300'"></span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <!-- Jika Perusahaan Belum Memiliki Foto (Hybrid): Kartu Identitas & Tagline Elegan Bersih di Light Mode -->
                                    <div class="company-card-tagline-box relative w-full h-28 sm:h-32 rounded-xl overflow-hidden bg-gradient-to-br from-[#F4FAF2] to-[#EBF6E8] dark:from-[#061506] dark:via-[#092009]/60 dark:to-[#020802] border border-emerald-600/15 dark:border-[#93F514]/15 my-1.5 flex flex-col items-center justify-center p-3 text-center group-hover:border-emerald-600/40 dark:group-hover:border-[#93F514]/40 transition-colors shadow-[inset_0_1px_2px_rgba(0,0,0,0.03)] dark:shadow-none">
                                        <div class="relative z-10 flex flex-col items-center justify-center h-full gap-1.5">
                                            @if (!empty($comp->tagline))
                                                <p class="text-[11px] sm:text-xs text-gray-900 dark:text-gray-300 italic font-semibold line-clamp-2 px-1 leading-snug">
                                                    “{{ $comp->tagline }}”
                                                </p>
                                            @else
                                                <span class="tagline-badge inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-700/10 dark:bg-[#93F514]/10 border border-emerald-700/20 dark:border-[#93F514]/20 text-[10px] font-bold text-emerald-800 dark:text-[#93F514] tracking-wide">
                                                    {{ __('Mitra Resmi MIKA') }}
                                                </span>
                                                <p class="tagline-subtext text-[10px] sm:text-[11px] text-gray-600 dark:text-gray-400 font-medium truncate max-w-[190px]">
                                                    {{ $comp->city ?? __('Jaringan Karir') }}
                                                </p>
                                            @endif

                                            <span class="profile-link text-[9px] uppercase tracking-wider text-emerald-700 dark:text-gray-400 group-hover:text-emerald-900 dark:group-hover:text-[#93F514] font-extrabold transition-colors inline-flex items-center gap-0.5 mt-0.5">
                                                <span>{{ __('Lihat Profil') }}</span>
                                                <span aria-hidden="true">&rarr;</span>
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Footer: Lowongan Tersedia Badge -->
                                <div class="pt-1.5 border-t border-[#93F514]/15 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1 text-[11px] {{ $jobCount > 0 ? 'text-[#93F514] font-bold' : 'text-gray-400' }}">
                                        <span>{{ $jobCount }} {{ __('Lowongan Tersedia') }}</span>
                                    </span>

                                    <span class="text-xs text-gray-400 group-hover:text-[#93F514] group-hover:translate-x-1 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
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

            // Acak urutan foto galeri agar variatif dan dinamis setiap kunjungan
            if (!empty($curvedPhotos)) {
                shuffle($curvedPhotos);

                // Upayakan agar foto bersebelahan tidak memiliki judul acara yang sama persis
                $totalPhotos = count($curvedPhotos);
                for ($i = 0; $i < $totalPhotos - 1; $i++) {
                    if ($curvedPhotos[$i]['title'] === $curvedPhotos[$i + 1]['title']) {
                        for ($j = $i + 2; $j < $totalPhotos; $j++) {
                            if ($curvedPhotos[$j]['title'] !== $curvedPhotos[$i]['title']) {
                                $temp = $curvedPhotos[$i + 1];
                                $curvedPhotos[$i + 1] = $curvedPhotos[$j];
                                $curvedPhotos[$j] = $temp;
                                break;
                            }
                        }
                    }
                }
            }

            // Initial item for initial render
            $initialItem = !empty($curvedPhotos) ? $curvedPhotos[0] : null;
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
