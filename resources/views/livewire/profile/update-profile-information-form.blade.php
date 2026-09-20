<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $photo = null;
    public ?string $currentPhotoUrl = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->currentPhotoUrl = $this->resolvePhotoUrl($user);
    }

    /**
     * Resolve photo URL for the user.
     */
    protected function resolvePhotoUrl($user): ?string
    {
        if (empty($user?->avatar)) {
            return null;
        }

        return Str::startsWith($user->avatar, ['http://', 'https://'])
            ? $user->avatar
            : asset('storage/' . $user->avatar);
    }

    /**
     * Remove the user's avatar.
     */
    public function removePhoto(): void
    {
        $user = Auth::user();

        if (!empty($user->avatar) && Str::startsWith($user->avatar, 'avatars/') && !str_contains($user->avatar, '..')) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
        }

        $user->avatar = null;
        $user->save();

        if ($user->applicantProfile) {
            $user->applicantProfile->update(['photo' => null]);
        }

        $this->photo = null;
        $this->currentPhotoUrl = null;

        $this->dispatch('profile-updated', name: $user->name, photo: null);
        Session::flash('status', 'photo-removed');
    }

    /**
     * Cancel temporary uploaded photo before saving.
     */
    public function cancelUpload(): void
    {
        $this->photo = null;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.mimes' => 'Format foto yang diizinkan adalah JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal adalah 3MB.',
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($this->photo) {
            // Delete existing uploaded photo if stored in avatars directory
            if (!empty($user->avatar) && Str::startsWith($user->avatar, 'avatars/') && !str_contains($user->avatar, '..')) {
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }

            $path = $this->photo->store('avatars', 'public');
            $user->avatar = $path;

            // Sync with ApplicantProfile photo if exists
            if ($user->applicantProfile) {
                $user->applicantProfile->update(['photo' => $path]);
            }

            $this->photo = null;
        }

        $user->save();

        // Sync with EmployeeProfile full_name if exists
        if ($user->employeeProfile) {
            $user->employeeProfile->update(['full_name' => $user->name]);
        }

        $this->currentPhotoUrl = $this->resolvePhotoUrl($user);

        $this->dispatch('profile-updated', name: $user->name, photo: $this->currentPhotoUrl);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header class="flex items-start gap-4 pb-5 border-b border-slate-100 dark:border-[#1D2E54]">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-[#14203A] border border-blue-200/80 dark:border-[#1D2E54] flex items-center justify-center text-blue-600 dark:text-[#93F514] shrink-0 shadow-xs">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                Informasi Profil
            </h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-[#93A5C9] leading-relaxed">
                Perbarui foto profil, nama lengkap, dan alamat email utama akun Anda.
            </p>
        </div>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-5">
        
        <!-- Foto Profil -->
        <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-[#14203A]/50 border border-slate-100 dark:border-[#1D2E54]">
            <label class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-3">
                Foto Profil
            </label>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                <!-- Avatar Preview with Loading Overlay -->
                <div class="relative group shrink-0">
                    <div class="w-20 h-20 sm:w-22 sm:h-22 rounded-full overflow-hidden ring-4 ring-blue-500/15 dark:ring-[#93F514]/25 shadow-md border-2 border-white dark:border-[#1D2E54] flex items-center justify-center bg-white dark:bg-[#0D1527] relative">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview Foto" class="w-full h-full object-cover">
                        @elseif ($currentPhotoUrl)
                            <img src="{{ $currentPhotoUrl }}" alt="{{ $name }}" class="w-full h-full object-cover" x-on:error="$el.style.display = 'none'">
                        @else
                            <div class="w-full h-full bg-blue-600 dark:bg-[#14203A] text-white dark:text-[#93F514] flex items-center justify-center font-extrabold text-2xl shadow-inner">
                                <span>{{ strtoupper(substr($name ?: 'A', 0, 1)) }}</span>
                            </div>
                        @endif

                        <!-- Uploading Spinner Overlay -->
                        <div wire:loading wire:target="photo" class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs flex flex-col items-center justify-center text-white rounded-full">
                            <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-[10px] font-semibold mt-1">Mengunggah...</span>
                        </div>
                    </div>

                    @if ($photo)
                        <span class="absolute top-0 right-0 px-2 py-0.5 bg-amber-500 text-white text-[9px] font-bold rounded-full shadow-xs tracking-wider uppercase animate-pulse">
                            Pratinjau
                        </span>
                    @endif
                </div>

                <!-- Action Controls & Description -->
                <div class="flex-1 text-center sm:text-left space-y-2.5">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <!-- Custom File Input Button -->
                        <label class="cursor-pointer inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200/80 dark:border-[#1D2E54] transition duration-150 active:scale-[0.98] shadow-2xs">
                            <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $photo || $currentPhotoUrl ? 'Ganti Foto' : 'Unggah Foto' }}</span>
                            <input type="file" wire:model="photo" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden">
                        </label>

                        @if ($photo)
                            <!-- Batal Pratinjau Button -->
                            <button type="button" wire:click="cancelUpload" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl border border-slate-200/80 dark:border-[#1D2E54] transition duration-150 active:scale-[0.98]">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Batalkan Pilihan</span>
                            </button>
                        @elseif ($currentPhotoUrl)
                            <!-- Hapus Foto Button -->
                            <button type="button" 
                                    wire:click="removePhoto" 
                                    wire:confirm="Apakah Anda yakin ingin menghapus foto profil dan kembali menggunakan inisial nama?"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-semibold rounded-xl border border-rose-200 dark:border-rose-800 transition duration-150 active:scale-[0.98]">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Hapus Foto</span>
                            </button>
                        @endif
                    </div>

                    <p class="text-[11px] text-slate-400 dark:text-[#93A5C9]">
                        Format: <span class="font-medium text-slate-600 dark:text-slate-300">JPG, PNG, atau WEBP</span>. Maksimal <span class="font-medium text-slate-600 dark:text-slate-300">3 MB</span>. Rasio 1:1 disarankan.
                    </p>

                    <x-input-error class="mt-1" :messages="$errors->get('photo')" />

                    @if (session('status') === 'photo-removed')
                        <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">
                            ✓ Foto profil berhasil dihapus dan kembali ke inisial nama.
                        </p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <div class="relative rounded-xl shadow-xs">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <input wire:model="name" 
                       id="name" 
                       name="name" 
                       type="text" 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-[#14203A] focus:border-blue-500 dark:focus:border-[#93F514] focus:ring-4 focus:ring-blue-500/10 dark:focus:ring-[#93F514]/10 transition duration-150" 
                       placeholder="Masukkan nama lengkap Anda"
                       required autofocus autocomplete="name" />
            </div>
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-[#93A5C9] uppercase tracking-wider mb-2">
                Alamat Email <span class="text-rose-500">*</span>
            </label>
            <div class="relative rounded-xl shadow-xs">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input wire:model="email" 
                       id="email" 
                       name="email" 
                       type="email" 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-[#14203A] focus:border-blue-500 dark:focus:border-[#93F514] focus:ring-4 focus:ring-blue-500/10 dark:focus:ring-[#93F514]/10 transition duration-150" 
                       placeholder="nama@email.com"
                       required autocomplete="username" />
            </div>
            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs">
                    <p class="text-amber-800 dark:text-amber-300">
                        Alamat email Anda belum diverifikasi.
                        <button wire:click.prevent="sendVerification" class="font-bold underline text-amber-900 dark:text-amber-200 hover:text-amber-700 ml-1">
                            Kirim ulang email verifikasi
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-semibold text-emerald-700 dark:text-[#93F514]">
                            ✓ Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Tombol Aksi -->
        <div class="pt-3 flex items-center justify-between">
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition-all duration-200 active:scale-[0.98] disabled:opacity-50">
                <svg wire:loading.remove wire:target="updateProfileInformation" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg wire:loading wire:target="updateProfileInformation" class="w-4 h-4 animate-spin text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Simpan Profil</span>
            </button>

            <x-action-message class="text-xs font-medium text-emerald-600 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/50 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800" on="profile-updated">
                ✓ Profil berhasil diperbarui!
            </x-action-message>
        </div>
    </form>
</section>
