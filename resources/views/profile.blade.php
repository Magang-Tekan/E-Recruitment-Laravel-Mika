<x-app-layout>
    @php
        $user = auth()->user();
        $roleName = strtolower($user?->role?->name ?? '');
        $isRecruiterRoute = request()->is('recruiter*') || request()->routeIs('recruiter.*');
        $isAdminOrRecruiter = auth()->check() && (
            in_array($user->role_id, [1, 2]) ||
            in_array($roleName, ['admin', 'superadmin', 'recruiter']) ||
            ((bool) ($user->is_recruiter ?? false) && $isRecruiterRoute)
        );
        $isEmployee = auth()->check() && ($user->role_id == 4 || $roleName === 'employee') && !$isRecruiterRoute;
        $roleLabel = $user->role?->name ?? ($isAdminOrRecruiter ? 'Admin' : ($isEmployee ? 'Employee' : 'Pelamar'));
        $employeeProfile = $isEmployee ? $user->employeeProfile : null;
        $adminAvatarUrl = null;
        if (!empty($user->avatar)) {
            $adminAvatarUrl = \Illuminate\Support\Str::startsWith($user->avatar, ['http://', 'https://'])
                ? $user->avatar
                : asset('storage/' . $user->avatar);
        }
    @endphp

    <div x-on:switch-tab.window="activeTab = $event.detail">
        <x-slot name="header">
            <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight">
                {{ $isEmployee ? __('Portal Karyawan') : ($isAdminOrRecruiter ? __('Profil Internal') : __('Profil Saya')) }}
            </h2>
        </x-slot>

        <div class="py-4 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto space-y-4">

                @if ($isAdminOrRecruiter)
                    <!-- Admin / Recruiter Profile View (Clean Enterprise Blueprint - Non-AI) -->
                    <div class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-6 flex flex-col sm:flex-row items-center justify-between gap-4"
                         x-data="{ 
                             bannerPhoto: @js($adminAvatarUrl), 
                             bannerName: @js(auth()->user()->name ?? 'Admin'),
                             get bannerInitial() { return (this.bannerName || 'A').charAt(0).toUpperCase(); }
                         }"
                         x-on:profile-updated.window="
                             if ($event.detail) {
                                 if ('photo' in $event.detail) bannerPhoto = $event.detail.photo;
                                 if ($event.detail.name) bannerName = $event.detail.name;
                             }
                         ">
                        <!-- Subtle Technical Blueprint Dot Grid (Authentic & Non-AI) -->
                        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.06]"
                            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
                        </div>

                        <div class="relative z-10 flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-[#14203A] border border-blue-100 dark:border-[#1D2E54] overflow-hidden flex items-center justify-center text-blue-600 dark:text-[#93F514] text-2xl font-black shadow-sm shrink-0 relative">
                                <template x-if="bannerPhoto">
                                    <img :src="bannerPhoto" :alt="bannerName" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!bannerPhoto">
                                    <span x-text="bannerInitial"></span>
                                </template>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white" x-text="bannerName">{{ auth()->user()->name }}</h3>
                                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-0.5">{{ auth()->user()->email }} &bull; <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-[#93F514]/15 dark:text-[#93F514] border border-emerald-200 dark:border-[#93F514]/30 font-semibold">{{ $roleLabel }}</span></p>
                            </div>
                        </div>
                        <a href="{{ auth()->user()->isRecruiter() && !auth()->user()->isAdmin() ? route('recruiter.dashboard') : route('admin.dashboard') }}" 
                           class="relative z-10 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm shadow-emerald-600/20 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-slate-950 dark:shadow-[#93F514]/20 font-bold text-xs transition shrink-0 active:scale-95">
                            Buka Panel Dashboard
                        </a>
                    </div>

                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-2xl flex items-start gap-3 text-amber-800 dark:text-amber-300 text-xs">
                        <svg class="w-5 h-5 shrink-0 text-amber-600 dark:text-amber-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-bold block mb-0.5">Informasi Akun Internal</span>
                            Akun Anda terdaftar sebagai <strong>{{ $roleLabel }}</strong> untuk manajemen rekrutmen. Pengisian biodata pelamar & pelamaran lowongan dinonaktifkan untuk akun ini. Anda dapat memperbarui profil dan kata sandi di bawah ini.
                        </div>
                    </div>

                    <!-- Profile Information & Password Form -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-6 bg-white dark:bg-[#0D1527] shadow-sm border border-slate-200/80 dark:border-[#1D2E54] rounded-2xl">
                            <livewire:profile.update-profile-information-form />
                        </div>
                        <div class="p-6 bg-white dark:bg-[#0D1527] shadow-sm border border-slate-200/80 dark:border-[#1D2E54] rounded-2xl">
                            <livewire:profile.update-password-form />
                        </div>
                    </div>
                @elseif ($isEmployee)
                    @if(auth()->user()->is_recruiter)
                        <!-- Recruiter Announcement Banner for Employee -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-emerald-500/15 via-emerald-500/5 to-transparent border border-emerald-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-500/20">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        <span>Hak Akses Recruiter / Penilai Aktif</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-[#93F514]/20 dark:text-[#93F514]">Recruiter</span>
                                    </h4>
                                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-0.5">
                                        Anda ditugaskan oleh Admin/HR untuk meninjau kualifikasi berkas pelamar dan memberikan persetujuan (Double Approval).
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('recruiter.dashboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 shadow-md shadow-emerald-600/20 transition-all shrink-0">
                                <span>Buka Dashboard Panel Recruiter</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    @endif

                    <!-- Employee Internal Assessment Portal -->
                    <livewire:employee.employee-assessment-portal />
                @else
                    <!-- Tab 1: Data Pribadi -->
                    <div x-show="activeTab === 'pribadi'" class="space-y-3" x-cloak>
                        <livewire:applicant.pribadi />
                    </div>

                    <!-- Tab 1.5: Data Keluarga -->
                    <div x-show="activeTab === 'keluarga'" class="space-y-3" x-cloak>
                        <livewire:applicant.keluarga />
                    </div>

                    <!-- Tab 2: Pendidikan -->
                    <div x-show="activeTab === 'pendidikan'" class="space-y-3" x-cloak>
                        <livewire:applicant.pendidikan />
                    </div>

                    <!-- Tab 3: Pengalaman Kerja -->
                    <div x-show="activeTab === 'pengalaman'" class="space-y-3" x-cloak>
                        <livewire:applicant.pengalaman />
                    </div>

                    <!-- Tab 4 & 5: Organisasi & Prestasi -->
                    <div x-show="activeTab === 'prestasi' || activeTab === 'organisasi' || activeTab === 'organisasi_prestasi'" class="space-y-3" x-cloak>
                        <livewire:applicant.prestasi />
                    </div>

                    <!-- Tab 6: Social Media -->
                    <div x-show="activeTab === 'social_media'" class="space-y-3" x-cloak>
                        <livewire:applicant.social-media />
                    </div>

                    <!-- Tab 7: Data Tambahan (Keahlian / Skill) -->
                    <div x-show="activeTab === 'data_tambahan'" class="space-y-3" x-cloak>
                        <livewire:applicant.data-tambahan />
                    </div>

                    <!-- Tab 8: Riwayat Lamaran -->
                    <div x-show="activeTab === 'riwayat'" class="space-y-3" x-cloak>
                        <livewire:applicant.riwayat />
                    </div>

                    <!-- Tab 9: Pengaturan Akun -->
                    <div x-show="activeTab === 'pengaturan'" class="space-y-3" x-cloak>
                        @include('livewire.applicant.pengaturan')
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
