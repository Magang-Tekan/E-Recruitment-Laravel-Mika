@php
    $user = auth()->user();
    $isRecruiter = $user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter');
    $cardCount = $isRecruiter ? 4 : 5;
@endphp

<div class="space-y-6 animate-pulse" aria-hidden="true" role="status">
    <span class="sr-only">Memuat dashboard...</span>

    <!-- Welcome Banner Skeleton -->
    <div
        class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-6 sm:p-7 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 transition-colors">
        
        <div class="flex items-start sm:items-center gap-4 w-full md:w-auto">
            <!-- Icon Box Skeleton -->
            <div
                class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] shrink-0"></div>
            
            <div class="space-y-2 flex-1 min-w-0">
                <div class="h-6 sm:h-7 w-64 sm:w-80 bg-slate-200 dark:bg-[#1D2E54] rounded-lg"></div>
                <div class="h-3.5 sm:h-4 w-full max-w-md bg-slate-100 dark:bg-[#14203A] rounded-md"></div>
            </div>
        </div>

        <!-- Action Buttons Skeleton -->
        <div class="flex items-center gap-2.5 shrink-0 w-full sm:w-auto">
            <div class="h-10 w-32 bg-slate-200 dark:bg-[#1D2E54] rounded-xl shrink-0"></div>
            <div class="h-10 w-32 bg-slate-100 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl shrink-0"></div>
        </div>
    </div>

    <!-- Key Metrics Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-2 {{ $isRecruiter ? 'lg:grid-cols-4' : 'lg:grid-cols-5' }} gap-4">
        @for ($i = 0; $i < $cardCount; $i++)
            <div
                class="p-4 sm:p-5 bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] flex items-center gap-3.5">
                <!-- Icon Box Skeleton -->
                <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-[#14203A] shrink-0"></div>
                <!-- Content -->
                <div class="space-y-2 flex-1 min-w-0">
                    <div class="h-3 w-16 bg-slate-200 dark:bg-[#1D2E54] rounded"></div>
                    <div class="flex items-baseline gap-1.5">
                        <div class="h-6 w-10 bg-slate-200 dark:bg-[#1D2E54] rounded"></div>
                        <div class="h-3 w-12 bg-slate-100 dark:bg-[#14203A] rounded"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    <!-- Middle Content Grid: Recent Applications & Active Jobs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">

        <!-- Recent Applications Table Skeleton (2 Columns) -->
        <div
            class="lg:col-span-2 bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] p-5 sm:p-6 flex flex-col justify-between h-full">
            <div>
                <!-- Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#1D2E54]">
                    <div class="space-y-1.5">
                        <div class="h-4 w-32 bg-slate-200 dark:bg-[#1D2E54] rounded"></div>
                        <div class="h-3 w-56 max-w-full bg-slate-100 dark:bg-[#14203A] rounded"></div>
                    </div>
                    <div class="h-3.5 w-16 bg-slate-200 dark:bg-[#1D2E54] rounded"></div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto mt-2">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-[#1D2E54]">
                                <th class="py-2.5 px-3 w-[40%]"><div class="h-3 w-16 bg-slate-200 dark:bg-[#1D2E54] rounded"></div></th>
                                <th class="py-2.5 px-3 w-[24%]"><div class="h-3 w-14 bg-slate-200 dark:bg-[#1D2E54] rounded"></div></th>
                                <th class="py-2.5 px-3 w-[22%]"><div class="h-3 w-14 bg-slate-200 dark:bg-[#1D2E54] rounded"></div></th>
                                <th class="py-2.5 px-3 text-center w-[14%]"><div class="h-3 w-12 bg-slate-200 dark:bg-[#1D2E54] rounded mx-auto"></div></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-[#1D2E54]/60">
                            @for ($r = 0; $r < 5; $r++)
                                <tr>
                                    <!-- Candidate Avatar + Name -->
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-[#1D2E54] shrink-0"></div>
                                            <div class="space-y-1.5 flex-1 min-w-0">
                                                <div class="h-3.5 w-28 bg-slate-200 dark:bg-[#1D2E54] rounded"></div>
                                                <div class="h-2.5 w-36 bg-slate-100 dark:bg-[#14203A] rounded"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Posisi -->
                                    <td class="py-3 px-3">
                                        <div class="h-3.5 w-24 bg-slate-200 dark:bg-[#1D2E54] rounded"></div>
                                    </td>
                                    <!-- Tanggal -->
                                    <td class="py-3 px-3">
                                        <div class="h-3 w-20 bg-slate-100 dark:bg-[#14203A] rounded"></div>
                                    </td>
                                    <!-- Status Pill -->
                                    <td class="py-3 px-3 text-center">
                                        <div class="h-5 w-16 bg-slate-100 dark:bg-[#14203A] border border-slate-200/60 dark:border-[#1D2E54] rounded-full mx-auto"></div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Lowongan Terbaru Skeleton (1 Column) -->
        <div class="h-full flex flex-col">
            <div
                class="bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] p-5 sm:p-6 flex flex-col justify-between h-full">
                <div>
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#1D2E54]">
                        <div class="space-y-1.5">
                            <div class="h-4 w-32 bg-slate-200 dark:bg-[#1D2E54] rounded"></div>
                            <div class="h-3 w-36 bg-slate-100 dark:bg-[#14203A] rounded"></div>
                        </div>
                        <div class="h-3.5 w-10 bg-slate-200 dark:bg-[#1D2E54] rounded"></div>
                    </div>

                    <!-- Items -->
                    <div class="mt-2 divide-y divide-slate-100 dark:divide-[#1D2E54]/60">
                        @for ($j = 0; $j < 5; $j++)
                            <div class="py-3.5 flex items-center justify-between gap-3">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="h-3.5 w-32 bg-slate-200 dark:bg-[#1D2E54] rounded"></div>
                                    <div class="h-2.5 w-24 bg-slate-100 dark:bg-[#14203A] rounded"></div>
                                </div>
                                <div class="h-6 w-16 bg-slate-100 dark:bg-[#14203A] border border-slate-200/60 dark:border-[#1D2E54] rounded-lg shrink-0"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
