<div class="space-y-6 animate-pulse">
    <!-- Welcome Banner Skeleton -->
    <div class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full space-y-2.5">
            <div class="h-6 sm:h-7 w-2/3 max-w-md bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
            <div class="h-3.5 sm:h-4 w-full max-w-lg bg-slate-200/70 dark:bg-slate-700/70 rounded-md"></div>
        </div>
        <div class="flex items-center gap-2.5 shrink-0 w-full md:w-auto">
            <div class="h-9 w-32 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
            <div class="h-9 w-28 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
        </div>
    </div>

    <!-- Key Metrics Cards Grid (4 Stat Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @for ($i = 0; $i < 4; $i++)
            <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-800 flex items-center gap-4">
                <!-- Icon Box Skeleton -->
                <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-slate-800 shrink-0"></div>
                <!-- Content -->
                <div class="space-y-2 flex-1 min-w-0">
                    <div class="h-3 w-20 bg-gray-200 dark:bg-slate-800 rounded"></div>
                    <div class="flex items-baseline gap-2">
                        <div class="h-6 w-12 bg-gray-200 dark:bg-slate-800 rounded"></div>
                        <div class="h-3 w-16 bg-gray-100 dark:bg-slate-800/60 rounded"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    <!-- Middle Content Grid: Recent Applications & Sidebar Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Applications Table Skeleton (2 Columns) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-800 p-6 flex flex-col justify-between">
            <div>
                <!-- Table Header Row -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-slate-800">
                    <div class="space-y-1.5">
                        <div class="h-4 w-32 bg-gray-200 dark:bg-slate-800 rounded"></div>
                        <div class="h-3 w-56 max-w-full bg-gray-100 dark:bg-slate-800/60 rounded"></div>
                    </div>
                    <div class="h-3 w-16 bg-gray-200 dark:bg-slate-800 rounded"></div>
                </div>

                <!-- Table Columns Header -->
                <div class="grid grid-cols-12 gap-4 py-3 border-b border-gray-100 dark:border-slate-800/60 mt-1 text-xs">
                    <div class="col-span-4 h-3 w-16 bg-gray-200 dark:bg-slate-800 rounded"></div>
                    <div class="col-span-3 h-3 w-14 bg-gray-200 dark:bg-slate-800 rounded"></div>
                    <div class="col-span-3 h-3 w-14 bg-gray-200 dark:bg-slate-800 rounded"></div>
                    <div class="col-span-2 h-3 w-12 bg-gray-200 dark:bg-slate-800 rounded mx-auto"></div>
                </div>

                <!-- Table Rows -->
                <div class="divide-y divide-gray-100 dark:divide-slate-800/50">
                    @for ($r = 0; $r < 5; $r++)
                        <div class="grid grid-cols-12 gap-4 py-3.5 items-center">
                            <!-- Candidate Avatar + Name -->
                            <div class="col-span-4 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-slate-800 shrink-0"></div>
                                <div class="h-3.5 w-24 bg-gray-200 dark:bg-slate-800 rounded"></div>
                            </div>
                            <!-- Position -->
                            <div class="col-span-3">
                                <div class="h-3.5 w-28 max-w-full bg-gray-100 dark:bg-slate-800/60 rounded"></div>
                            </div>
                            <!-- Date -->
                            <div class="col-span-3">
                                <div class="h-3 w-20 bg-gray-100 dark:bg-slate-800/60 rounded"></div>
                            </div>
                            <!-- Status Pill -->
                            <div class="col-span-2 flex justify-center">
                                <div class="h-5 w-16 bg-gray-200 dark:bg-slate-800 rounded-full"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Sidebar Widgets Skeleton (1 Column) -->
        <div class="space-y-6">
            <!-- Quick Shortcuts Skeleton -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-800 p-6">
                <div class="h-4 w-28 bg-gray-200 dark:bg-slate-800 rounded mb-4"></div>
                <div class="grid grid-cols-2 gap-3">
                    @for ($m = 0; $m < 4; $m++)
                        <div class="p-3 bg-gray-50 dark:bg-slate-800/50 rounded-xl flex flex-col items-center gap-2 border border-gray-100/80 dark:border-slate-800">
                            <div class="w-10 h-10 rounded-xl bg-gray-200 dark:bg-slate-700"></div>
                            <div class="h-3 w-14 bg-gray-200 dark:bg-slate-700 rounded"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Active Jobs Summary Skeleton -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-200 dark:border-slate-800 p-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-slate-800">
                    <div class="h-4 w-32 bg-gray-200 dark:bg-slate-800 rounded"></div>
                    <div class="h-3 w-10 bg-gray-200 dark:bg-slate-800 rounded"></div>
                </div>
                <div class="mt-3 divide-y divide-gray-100 dark:divide-slate-800/50">
                    @for ($j = 0; $j < 3; $j++)
                        <div class="py-3 flex items-center justify-between">
                            <div class="space-y-1.5 flex-1 mr-3">
                                <div class="h-3.5 w-32 bg-gray-200 dark:bg-slate-800 rounded"></div>
                                <div class="h-3 w-24 bg-gray-100 dark:bg-slate-800/60 rounded"></div>
                            </div>
                            <div class="h-6 w-16 bg-gray-200 dark:bg-slate-800 rounded-lg shrink-0"></div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
