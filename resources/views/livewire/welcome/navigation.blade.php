<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: false);
    }
}; ?>

<nav class="-mx-3 flex flex-1 justify-end items-center gap-3">
    @auth
        @if(in_array(auth()->user()->role_id, [1, 2]) || in_array(strtolower(auth()->user()->role?->name ?? ''), ['admin', 'superadmin', 'recruiter']))
            @php
                $isAdminUser = auth()->user()->role_id == 1 || in_array(strtolower(auth()->user()->role?->name ?? ''), ['admin', 'superadmin']);
                $dashboardRoute = $isAdminUser ? route('admin.dashboard') : route('recruiter.dashboard');
            @endphp
            <a
                href="{{ $dashboardRoute }}"
                class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-black dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition"
            >
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ auth()->user()->name }}</span>
            </a>
        @elseif(auth()->user()->role_id == 4 || strtolower(auth()->user()->role?->name ?? '') === 'employee')
            <a
                href="{{ route('employee.dashboard') }}"
                class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-black dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition"
            >
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ auth()->user()->name }}</span>
            </a>
        @else
            <a
                href="{{ route('profile') }}"
                class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-black dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ auth()->user()->name }}</span>
            </a>
        @endif

        <button
            wire:click="logout"
            class="rounded-md px-3 py-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium transition text-sm cursor-pointer"
        >
            Keluar
        </button>
    @else
        <a
            href="{{ route('login') }}"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
            Masuk
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                Daftar
            </a>
        @endif
    @endauth
</nav>

