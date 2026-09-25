@props([
    'title' => '',
    'active' => false,
    'icon' => null,
    'tooltip' => null,
])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="open = !open" 
            type="button"
            title="{{ $tooltip ?? $title }}"
            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $active ? 'text-emerald-700 dark:text-[#93F514] bg-emerald-50 dark:bg-[#93F514]/10' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-[#93F514] hover:bg-gray-100 dark:hover:bg-[#0a1f0a]' }} group">
        <div class="flex items-center gap-3 min-w-0">
            @if ($icon)
                <div class="{{ $active ? 'text-emerald-700 dark:text-[#93F514]' : 'text-gray-400 dark:text-gray-500 group-hover:text-emerald-600 dark:group-hover:text-[#93F514]' }} transition-colors duration-200 shrink-0">
                    {{ $icon }}
                </div>
            @endif
            <span class="truncate">{{ $title }}</span>
        </div>
        
        <svg class="w-4 h-4 transition-transform duration-200 text-gray-400 group-hover:text-gray-600 dark:text-gray-500 dark:group-hover:text-[#93F514]"
             :class="{ 'rotate-90': open }"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    <div x-show="open" 
         x-cloak 
         class="pl-9 pr-2 py-1 space-y-1 border-l-2 border-gray-200 dark:border-[#93F514]/20 ml-4 my-1">
        {{ $slot }}
    </div>
</div>
