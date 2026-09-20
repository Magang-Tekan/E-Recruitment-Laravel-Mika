<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component
{
    #[On('profile-updated')]
    public function refreshNavigation(): void
    {
        // Re-renders component when profile is updated
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: false);
    }
}; ?>

@php
    $user = auth()->user()?->fresh();
    $roleName = strtolower($user?->role?->name ?? '');
    $roleId = $user?->role_id;
    
    $isAdmin = $roleId == 1 || in_array($roleName, ['admin', 'superadmin']);
    $isRecruiter = $roleId == 2 || $roleName === 'recruiter';
    $isEmployee = $roleId == 4 || $roleName === 'employee';
    
    $profile = match(true) {
        $isEmployee => $user?->employeeProfile,
        $isAdmin || $isRecruiter => null,
        default => $user?->applicantProfile,
    };

    $roleLabel = match(true) {
        $isAdmin => 'Admin',
        $isRecruiter => 'Recruiter',
        $isEmployee => match($profile?->employee_type) {
            'internship' => 'Magang',
            'contract' => 'Kontrak',
            'probation' => 'Probation',
            default => 'Karyawan',
        },
        default => 'Pelamar',
    };

    $profileRoute = match(true) {
        $isAdmin => route('admin.profile'),
        $isRecruiter => route('recruiter.profile'),
        $isEmployee => route('employee.dashboard'),
        default => route('profile'),
    };
    
    $photoUrl = null;
    if ($profile && !empty($profile->photo)) {
        $photoUrl = \Illuminate\Support\Str::startsWith($profile->photo, ['http://', 'https://']) ? $profile->photo : asset('storage/' . $profile->photo);
    } elseif ($user?->applicantProfile && !empty($user->applicantProfile->photo)) {
        $photoUrl = \Illuminate\Support\Str::startsWith($user->applicantProfile->photo, ['http://', 'https://']) ? $user->applicantProfile->photo : asset('storage/' . $user->applicantProfile->photo);
    } elseif (!empty($user->avatar)) {
        $photoUrl = \Illuminate\Support\Str::startsWith($user->avatar, ['http://', 'https://']) ? $user->avatar : asset('storage/' . $user->avatar);
    }

    $displayName = match(true) {
        $isAdmin || $isRecruiter => $user->name ?? 'Admin',
        $isEmployee => $profile && !empty($profile->full_name) ? $profile->full_name : ($user->name ?? 'Karyawan'),
        default => $profile && !empty($profile->full_name) ? $profile->full_name : ($user->name ?? 'User'),
    };
    $userInitial = strtoupper(substr($displayName, 0, 1));
@endphp

<nav x-data="{ open: false }" class="bg-transparent">
    <div class="flex items-center justify-between">
        <!-- Settings Dropdown -->
        <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">

            <!-- Dark/Light Mode Toggle Button (Desktop) -->
            <button
                type="button"
                x-data="{
                    isDark: document.documentElement.classList.contains('dark'),
                    toggle() {
                        if (typeof window.toggleMikaTheme === 'function') {
                            this.isDark = window.toggleMikaTheme();
                        } else {
                            this.isDark = !this.isDark;
                            const html = document.documentElement;
                            if (this.isDark) {
                                html.classList.add('dark');
                                html.classList.remove('light-mode');
                                localStorage.setItem('mika-theme', 'dark');
                            } else {
                                html.classList.remove('dark');
                                html.classList.add('light-mode');
                                localStorage.setItem('mika-theme', 'light');
                            }
                        }
                    }
                }"
                @theme-changed.window="isDark = $event.detail.isDark"
                @click="toggle()"
                :title="isDark ? 'Aktifkan Light Mode' : 'Aktifkan Dark Mode'"
                class="relative flex items-center justify-center w-9 h-9 rounded-full border border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#14203A] hover:bg-slate-50 dark:hover:bg-[#1A2A4C] shadow-sm transition-all duration-200 group focus:outline-none overflow-hidden"
            >
                <!-- Sun Icon (shown in dark mode) -->
                <svg x-show="isDark" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 rotate-90 scale-50"
                     x-transition:enter-end="opacity-100 rotate-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 rotate-0 scale-100"
                     x-transition:leave-end="opacity-0 -rotate-90 scale-50"
                     class="w-5 h-5 text-amber-400 absolute" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <!-- Moon Icon (shown in light mode) -->
                <svg x-show="!isDark" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -rotate-90 scale-50"
                     x-transition:enter-end="opacity-100 rotate-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 rotate-0 scale-100"
                     x-transition:leave-end="opacity-0 rotate-90 scale-50"
                     class="w-4 h-4 text-emerald-600 absolute" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z" />
                </svg>
            </button>

            <x-dropdown align="right" width="56">
                <x-slot name="trigger">
                    <button class="inline-flex items-center gap-2.5 px-3 py-1.5 border border-slate-200/80 dark:border-[#1D2E54] rounded-full text-sm font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-[#14203A] hover:bg-slate-50 dark:hover:bg-[#1A2A4C] focus:outline-none transition shadow-2xs group">
                        <div x-data="{{ json_encode(['photo' => $photoUrl, 'initial' => $userInitial]) }}"
                             x-on:profile-updated.window="if ($event.detail && 'photo' in $event.detail) photo = $event.detail.photo"
                             class="shrink-0 flex items-center justify-center">
                            <template x-if="photo">
                                <img :src="photo" alt="{{ $displayName }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-500/20 dark:ring-[#93F514]/30 group-hover:ring-emerald-500 dark:group-hover:ring-[#93F514] transition">
                            </template>
                            <template x-if="!photo">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-600 via-teal-500 to-emerald-500 dark:from-[#93F514] dark:to-emerald-400 dark:text-black flex items-center justify-center text-white font-bold text-xs shadow-2xs">
                                    <span x-text="initial"></span>
                                </div>
                            </template>
                        </div>

                        <div class="text-left leading-tight max-w-[150px] truncate">
                            <span class="block text-xs font-bold text-gray-800 dark:text-gray-200 truncate" x-data="{{ json_encode(['name' => $displayName]) }}" x-text="name" x-on:profile-updated.window="if ($event.detail && $event.detail.name) name = $event.detail.name"></span>
                            <span class="block text-[9.5px] font-semibold text-emerald-600 dark:text-[#93F514] uppercase tracking-wider">{{ $roleLabel }}</span>
                        </div>

                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-200 transition-transform group-hover:translate-y-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-2.5 border-b border-slate-100 dark:border-[#1D2E54] bg-slate-50/50 dark:bg-[#14203A]/50">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $displayName }}</p>
                        <p class="text-[11px] text-slate-500 dark:text-[#93A5C9] truncate">{{ auth()->user()->email }}</p>
                    </div>

                    <x-dropdown-link :href="url('/')">
                        {{ __('Lihat Beranda Website') }}
                    </x-dropdown-link>

                    @if($isAdmin)
                        <x-dropdown-link :href="route('admin.dashboard')">
                            {{ __('Dashboard Admin') }}
                        </x-dropdown-link>
                    @elseif($isRecruiter)
                        <x-dropdown-link :href="route('recruiter.dashboard')">
                            {{ __('Dashboard Recruiter') }}
                        </x-dropdown-link>
                    @elseif($isEmployee)
                        <x-dropdown-link :href="route('employee.dashboard')">
                            {{ __('Portal Asesmen') }}
                        </x-dropdown-link>
                    @endif

                    <x-dropdown-link :href="$profileRoute">
                        {{ __('Profil Saya') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <button wire:click="logout" class="w-full text-start border-t border-slate-100 dark:border-[#1D2E54] mt-1">
                        <x-dropdown-link class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </button>
                </x-slot>
            </x-dropdown>
        </div>

        <div class="-me-2 flex items-center sm:hidden gap-2">

            <!-- Dark/Light Mode Toggle Button (Mobile) -->
            <button
                type="button"
                x-data="{
                    isDark: document.documentElement.classList.contains('dark'),
                    toggle() {
                        if (typeof window.toggleMikaTheme === 'function') {
                            this.isDark = window.toggleMikaTheme();
                        } else {
                            this.isDark = !this.isDark;
                            const html = document.documentElement;
                            if (this.isDark) {
                                html.classList.add('dark');
                                html.classList.remove('light-mode');
                                localStorage.setItem('mika-theme', 'dark');
                            } else {
                                html.classList.remove('dark');
                                html.classList.add('light-mode');
                                localStorage.setItem('mika-theme', 'light');
                            }
                        }
                    }
                }"
                @theme-changed.window="isDark = $event.detail.isDark"
                @click="toggle()"
                :title="isDark ? 'Aktifkan Light Mode' : 'Aktifkan Dark Mode'"
                class="relative flex items-center justify-center w-8 h-8 rounded-full border border-slate-200/80 dark:border-[#1D2E54] bg-white dark:bg-[#14203A] hover:bg-slate-50 dark:hover:bg-[#1A2A4C] shadow-sm transition-all duration-200 focus:outline-none overflow-hidden"
            >
                <svg x-show="isDark" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 rotate-90 scale-50"
                     x-transition:enter-end="opacity-100 rotate-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 rotate-0 scale-100"
                     x-transition:leave-end="opacity-0 -rotate-90 scale-50"
                     class="w-4 h-4 text-amber-400 absolute" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <svg x-show="!isDark" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -rotate-90 scale-50"
                     x-transition:enter-end="opacity-100 rotate-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 rotate-0 scale-100"
                     x-transition:leave-end="opacity-0 rotate-90 scale-50"
                     class="w-4 h-4 text-emerald-600 absolute" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z" />
                </svg>
            </button>

            <button @click="open = ! open" class="inline-flex items-center gap-2 p-1.5 rounded-full border border-slate-200/80 dark:border-[#1D2E54] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#14203A] focus:outline-none transition" title="Toggle User Menu">
                @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $displayName }}" class="w-7 h-7 rounded-full object-cover">
                @else
                    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 dark:from-[#93F514] dark:to-emerald-400 dark:text-black flex items-center justify-center text-white font-bold text-xs">
                        {{ $userInitial }}
                    </div>
                @endif
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
         @click.outside="open = false" 
         class="sm:hidden fixed top-16 right-3 left-3 max-w-sm ml-auto z-50 bg-white dark:bg-[#0D1527] rounded-2xl shadow-2xl border border-slate-100 dark:border-[#1D2E54] overflow-hidden" 
         x-cloak>
        <!-- Responsive Settings Options -->
        <div class="pt-3.5 pb-2">
            <div class="px-4 pb-3 flex items-center gap-3 border-b border-slate-100 dark:border-[#1D2E54]">
                <div x-data="{{ json_encode(['photo' => $photoUrl, 'initial' => $userInitial]) }}"
                     x-on:profile-updated.window="if ($event.detail && 'photo' in $event.detail) photo = $event.detail.photo"
                     class="shrink-0 flex items-center justify-center">
                    <template x-if="photo">
                        <img :src="photo" alt="{{ $displayName }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-emerald-500/20 dark:ring-[#93F514]/30 shrink-0">
                    </template>
                    <template x-if="!photo">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 dark:from-[#93F514] dark:to-emerald-400 dark:text-black flex items-center justify-center text-white font-bold text-sm shrink-0">
                            <span x-text="initial"></span>
                        </div>
                    </template>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-sm text-gray-900 dark:text-white truncate" x-data="{{ json_encode(['name' => $displayName]) }}" x-text="name" x-on:profile-updated.window="if ($event.detail && $event.detail.name) name = $event.detail.name"></div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</span>
                        <span class="px-1.5 py-0.5 text-[9px] font-extrabold uppercase rounded bg-emerald-100 dark:bg-[#93F514]/15 text-emerald-800 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30 shrink-0">{{ $roleLabel }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-2 space-y-0.5 px-2">
                <x-responsive-nav-link :href="url('/')" class="rounded-xl">
                    {{ __('Lihat Beranda Website') }}
                </x-responsive-nav-link>

                @if($isAdmin)
                    <x-responsive-nav-link :href="route('admin.dashboard')" class="rounded-xl">
                        {{ __('Dashboard Admin') }}
                    </x-responsive-nav-link>
                @elseif($isRecruiter)
                    <x-responsive-nav-link :href="route('recruiter.dashboard')" class="rounded-xl">
                        {{ __('Dashboard Recruiter') }}
                    </x-responsive-nav-link>
                @elseif($isEmployee)
                    <x-responsive-nav-link :href="route('employee.dashboard')" class="rounded-xl">
                        {{ __('Portal Asesmen') }}
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="$profileRoute" class="rounded-xl">
                    {{ __('Profil Saya') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <div class="pt-1 mt-1 border-t border-slate-100 dark:border-[#1D2E54]">
                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-xl">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
