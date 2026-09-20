<div class="space-y-6">
    <!-- Header Card -->
    <div
        class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-blue-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.05]"
            style="background-image: radial-gradient(#93F514 1px, transparent 1px); background-size: 20px 20px;">
        </div>
        <div class="relative z-10">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Pengaturan Keamanan Akun</h2>
            <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">* Perbarui kata sandi atau kelola akun Anda.</p>
        </div>
    </div>

    <!-- Single Column Layout -->
    <div class="space-y-6">
        <!-- Update Password Card -->
        <div class="bg-white dark:bg-[#0D1527] p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54]">
            <livewire:profile.update-password-form />
        </div>

        <!-- Delete Account Card -->
        <div class="bg-white dark:bg-[#0D1527] p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54]">
            <livewire:profile.delete-user-form />
        </div>
    </div>
</div>
