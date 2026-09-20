@props([
    'active' => false,
    'tab' => null,
    'href' => '#',
    'badge' => null,
])

@php
$activeClasses = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 text-white shadow-md shadow-emerald-600/20 dark:bg-[#93F514] dark:text-black dark:shadow-[#93F514]/25 group transition-all';
$inactiveClasses = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-[#93F514] hover:bg-gray-100 dark:hover:bg-[#14203A] group transition-colors';

$iconActiveClasses = 'shrink-0 text-white dark:text-black';
$iconInactiveClasses = 'text-gray-400 dark:text-slate-400 group-hover:text-emerald-600 dark:group-hover:text-[#93F514] shrink-0 transition-colors';
@endphp

@if ($tab)
<a href="{{ $href }}"
    x-bind:class="activeTab === '{{ $tab }}' ? '{{ $activeClasses }}' : '{{ $inactiveClasses }}'"
    {{ $attributes }}>
    <div class="flex items-center gap-3 min-w-0">
        @if (isset($icon))
            <div x-bind:class="activeTab === '{{ $tab }}' ? '{{ $iconActiveClasses }}' : '{{ $iconInactiveClasses }}'">
                {{ $icon }}
            </div>
        @endif
        <span class="truncate">{{ $slot }}</span>
    </div>

    @if (isset($append))
        <div class="shrink-0 ml-2">
            {{ $append }}
        </div>
    @elseif ($badge)
        <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 dark:bg-[#93F514]/20 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30">
            {{ $badge }}
        </span>
    @endif
</a>
@else
<a href="{{ $href }}" class="{{ $active ? $activeClasses : $inactiveClasses }}" {{ $attributes }}>
    <div class="flex items-center gap-3 min-w-0">
        @if (isset($icon))
            <div class="{{ $active ? $iconActiveClasses : $iconInactiveClasses }}">
                {{ $icon }}
            </div>
        @endif
        <span class="truncate">{{ $slot }}</span>
    </div>

    @if (isset($append))
        <div class="shrink-0 ml-2">
            {{ $append }}
        </div>
    @elseif ($badge)
        <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700 dark:bg-[#93F514]/20 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30">
            {{ $badge }}
        </span>
    @endif
</a>
@endif
