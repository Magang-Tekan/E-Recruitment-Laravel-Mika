@props([
    'columns' => 5,
    'rows' => 5,
    'hasSearch' => true,
    'hasButton' => true,
    'buttonWidth' => 'w-36',
    'titleWidth' => 'w-44',
    'circleAvatar' => true,
])

<div {{ $attributes->merge(['class' => 'space-y-6 animate-pulse']) }} aria-hidden="true" role="status">
    <span class="sr-only">Memuat data...</span>

    <!-- 1. Header & Action Section Skeleton -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 overflow-hidden shadow-sm rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-2">
                <!-- Title Placeholder -->
                <div class="h-6 bg-slate-200 dark:bg-slate-700 rounded-lg {{ $titleWidth }}"></div>
                <!-- Subtitle Placeholder -->
                <div class="h-3.5 bg-slate-200/70 dark:bg-slate-700/70 rounded w-64"></div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                @if ($hasSearch)
                    <!-- Search Input Placeholder -->
                    <div class="h-10 w-full sm:w-64 bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 rounded-xl"></div>
                @endif
                @if ($hasButton)
                    <!-- Add/Action Button Placeholder -->
                    <div class="h-10 w-full sm:{{ $buttonWidth }} bg-slate-200 dark:bg-slate-700 rounded-xl shrink-0"></div>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. Data Table Section Skeleton -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <!-- Table Header Mockup -->
                <thead>
                    <tr class="border-b border-gray-200 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50">
                        @for ($c = 0; $c < $columns; $c++)
                            <th class="px-6 py-4 {{ $c === $columns - 1 ? 'text-center w-28' : ($c === 0 ? 'w-2/5' : '') }}">
                                <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded {{ $c === $columns - 1 ? 'w-12 mx-auto' : ($c === 0 ? 'w-24' : 'w-16') }}"></div>
                            </th>
                        @endfor
                    </tr>
                </thead>

                <!-- Table Rows Mockup -->
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @for ($r = 0; $r < $rows; $r++)
                        <tr class="hover:bg-gray-50/30 dark:hover:bg-slate-800/30 transition-colors">
                            @for ($c = 0; $c < $columns; $c++)
                                <td class="px-6 py-4 {{ $c === $columns - 1 ? 'text-center' : '' }}">
                                    @if ($c === 0)
                                        <!-- Column 1: Main Data (Avatar/Icon + Title + Subtitle) -->
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-9 h-9 {{ $circleAvatar ? 'rounded-full' : 'rounded-xl' }} bg-slate-200 dark:bg-slate-700 shrink-0"></div>
                                            <div class="space-y-1.5 flex-1">
                                                <div class="h-3.5 bg-slate-200 dark:bg-slate-700 rounded w-36"></div>
                                                <div class="h-2.5 bg-slate-200/70 dark:bg-slate-700/70 rounded w-24"></div>
                                            </div>
                                        </div>
                                    @elseif ($c === $columns - 1)
                                        <!-- Last Column: Action Buttons -->
                                        <div class="flex items-center justify-center gap-1.5">
                                            <div class="w-7 h-7 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                                            <div class="w-7 h-7 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                                        </div>
                                    @elseif ($c === $columns - 2)
                                        <!-- Penultimate Column: Status Badge or Pill -->
                                        <div class="h-5 bg-slate-200 dark:bg-slate-700 rounded-full w-20"></div>
                                    @else
                                        <!-- Middle Columns: Text/Data -->
                                        <div class="space-y-1.5">
                                            <div class="h-3.5 bg-slate-200 dark:bg-slate-700 rounded w-28"></div>
                                            <div class="h-2.5 bg-slate-200/70 dark:bg-slate-700/70 rounded w-20"></div>
                                        </div>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Pagination Skeleton Bar -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="h-3.5 bg-slate-200 dark:bg-slate-700 rounded w-40"></div>
            <div class="flex items-center gap-2">
                <div class="h-8 w-16 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                <div class="h-8 w-8 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                <div class="h-8 w-8 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                <div class="h-8 w-16 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
            </div>
        </div>
    </div>
</div>
