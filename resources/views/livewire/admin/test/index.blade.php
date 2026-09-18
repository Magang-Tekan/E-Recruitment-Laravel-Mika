<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg sm:text-xl text-gray-800 dark:text-slate-100 leading-tight">
            {{ __('Paket Ujian Tes Online') }}
        </h2>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <livewire:admin.test-table lazy />
    </div>
</x-app-layout>
