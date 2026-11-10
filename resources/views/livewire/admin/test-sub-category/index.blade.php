<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-bold text-lg sm:text-xl text-gray-800 dark:text-slate-100 leading-tight">
                {{ __('Data Sub-Kategori Soal') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.test_category') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700 transition">
                    ← Kategori Utama
                </a>
                <a href="{{ route('admin.question_bank') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 transition">
                    Bank Soal →
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <livewire:admin.test-sub-category-table lazy />
    </div>
</x-app-layout>

