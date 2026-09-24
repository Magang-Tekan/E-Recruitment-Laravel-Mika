<?php

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmployeeProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $account_type = 'applicant'; // 'applicant' | 'employee'
    public string $name = '';
    public string $nik = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Field khusus Karyawan Internal
    public string $employee_type = 'permanent'; // 'permanent' | 'contract' | 'internship'
    public string $company_passkey = '';
    public string $company_id = '';
    public string $department_id = '';
    public string $position_id = '';
    public string $position_title = '';

    public function updatedCompanyId(): void
    {
        $this->department_id = '';
        $this->position_id = '';
        $this->position_title = '';
    }

    public function updatedDepartmentId(): void
    {
        $this->position_id = '';
        $this->position_title = '';
    }

    public function updatedPositionId(): void
    {
        if ($this->position_id) {
            $pos = Position::find($this->position_id);
            if ($pos) {
                $this->position_title = $pos->name;
            }
        } else {
            $this->position_title = '';
        }
    }

    public function with(): array
    {
        $companies = Company::orderBy('name')->get();

        $departments = collect();
        if (!empty($this->company_id)) {
            $departments = Department::where('company_id', $this->company_id)->orderBy('name')->get();
        }

        $positions = collect();
        if (!empty($this->department_id)) {
            $positions = Position::where('department_id', $this->department_id)->orderBy('name')->get();
        }

        return [
            'companies' => $companies,
            'departments' => $departments,
            'positions' => $positions,
        ];
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $rules = [
            'account_type' => ['required', 'in:applicant,employee'],
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16', 'unique:users,nik'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];

        $messages = [
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari tepat 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar dalam sistem.',
        ];

        if ($this->account_type === 'employee') {
            $expectedPasskey = env('EMPLOYEE_REGISTRATION_PASSKEY', 'MIKA2026');
            $rules['employee_type'] = ['required', 'in:permanent,contract,internship'];
            $rules['company_id'] = ['required', 'exists:companies,id'];
            $rules['department_id'] = ['required', 'exists:departments,id'];
            $rules['position_id'] = ['nullable', 'exists:positions,id'];
            $rules['position_title'] = ['nullable', 'string', 'max:100'];
            $rules['company_passkey'] = [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($expectedPasskey) {
                    if (trim($value) !== trim($expectedPasskey)) {
                        $fail('Kode token perusahaan tidak valid. Silakan hubungi tim HR.');
                    }
                },
            ];

            $messages['company_id.required'] = 'Silakan pilih Perusahaan tempat Anda bekerja.';
            $messages['department_id.required'] = 'Silakan pilih Departemen tempat Anda bekerja.';
        }

        $validated = $this->validate($rules, $messages);

        $userData = [
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $this->account_type === 'employee' ? 4 : 3,
        ];

        $user = User::create($userData);

        if ($this->account_type === 'employee') {
            $pos = !empty($this->position_id) ? Position::find($this->position_id) : null;
            $finalPosTitle = $this->position_title ?: ($pos?->name ?? null);

            EmployeeProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nik' => $user->nik,
                    'full_name' => $user->name,
                    'company_id' => (int)$this->company_id,
                    'department_id' => (int)$this->department_id,
                    'position_id' => !empty($this->position_id) ? (int)$this->position_id : null,
                    'position_title' => $finalPosTitle,
                    'employee_type' => $this->employee_type ?: 'permanent',
                ]
            );
        }

        event(new Registered($user));

        session()->flash('status', 'Registrasi berhasil! Silakan masuk menggunakan akun Anda.');
        $this->redirect(route('login', absolute: false));
    }
}; ?>

<div class="w-full max-w-5xl mx-auto">
    <!-- 4-Dot Loading Overlay saat Proses Daftar (Livewire Submission) -->
    <div wire:loading.flex wire:target="register"
        class="fixed inset-0 z-[100] bg-black/40 backdrop-blur-[2px] flex flex-col items-center justify-center select-none transition-all">
        <!-- 4-Dot Jumping Wave Loader (Sesuai Referensi, Tanpa Shadow) -->
        <div class="flex items-center gap-3 h-10 px-2">
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-1 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-2 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-3 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-4 inline-block"></span>
        </div>
        <p class="mt-2.5 text-xs font-semibold text-white tracking-wide">
            <span class="text-[#93F514]">Mendaftarkan</span> Akun...
        </p>
    </div>

    <!-- 4-Dot Loading Overlay saat Daftar Cepat dengan Google -->
    <div id="google-auth-loader"
        class="hidden fixed inset-0 z-[100] bg-black/40 backdrop-blur-[2px] flex flex-col items-center justify-center select-none transition-all">
        <!-- 4-Dot Jumping Wave Loader (Sesuai Referensi, Tanpa Shadow) -->
        <div class="flex items-center gap-3 h-10 px-2">
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-1 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-2 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-3 inline-block"></span>
            <span class="w-3.5 h-3.5 rounded-full bg-[#93F514] animate-dot-4 inline-block"></span>
        </div>
        <p class="mt-2.5 text-xs font-semibold text-white tracking-wide">
            Memuat Daftar Google...
        </p>
    </div>

    <!-- Main 2-Column Card Container -->
    <div class="glass-card-main rounded-[2rem] sm:rounded-[2.5rem] p-3 sm:p-4 md:p-6 shadow-2xl relative overflow-hidden">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-stretch">
            
            <!-- LEFT COLUMN: Showcase / Value Proposition Banner (5 Cols) -->
            <div class="lg:col-span-5 banner-gradient rounded-[1.75rem] p-6 sm:p-8 lg:p-9 flex flex-col justify-between relative overflow-hidden min-h-[360px] lg:min-h-[560px]">
                
                <!-- Ambient Glow inside Left Panel -->
                <div class="absolute -top-20 -left-20 w-52 h-52 bg-[#93F514]/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-20 -right-20 w-52 h-52 bg-[#46ee40]/15 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Hexagon / Geometric Background Motifs -->
                <div class="absolute inset-0 opacity-10 pointer-events-none flex items-center justify-center">
                    <svg class="w-full h-full text-[#93F514]" viewBox="0 0 400 400" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polygon points="200,60 280,105 280,195 200,240 120,195 120,105"/>
                        <polygon points="320,130 380,165 380,235 320,270 260,235 260,165"/>
                        <polygon points="80,130 140,165 140,235 80,270 20,235 20,165"/>
                        <polygon points="200,220 280,265 280,355 200,400 120,355 120,265"/>
                    </svg>
                </div>

                <!-- Top Area: Brand Logo & Heading -->
                <div class="relative z-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                        <div class="p-2 rounded-xl bg-black/40 border border-[#93F514]/30 shadow-md group-hover:scale-105 transition-transform duration-200">
                            <img src="{{ asset('storage/logo/mikaaaa.png') }}" 
                                 alt="Logo MIKA" 
                                 class="h-8 w-auto object-contain rounded-lg">
                        </div>
                        <div>
                            <span class="heading-font text-base sm:text-lg font-bold tracking-tight text-white flex items-center gap-1">
                                MIKA <span class="text-[#93F514]">CAREER</span>
                            </span>
                            <span class="block text-[10px] text-gray-400 font-normal">
                                Portal Rekrutmen & Asesmen Online
                            </span>
                        </div>
                    </a>

                    <!-- Slogan Heading -->
                    <div class="mt-8">
                        <h2 class="heading-font text-2xl sm:text-3xl font-bold text-white leading-tight">
                            Start your journey with <br>
                            <span class="text-[#93F514]">
                                Mitra Karya Analitika Group
                            </span>
                        </h2>
                    </div>
                </div>

                <!-- Middle / Illustration Showcase -->
                <div class="relative z-10 my-auto py-5 flex items-center justify-center">
                    <div class="relative w-full max-w-[280px] flex flex-col items-center">
                        
                        <!-- Glow Behind Person -->
                        <div class="absolute w-44 h-44 rounded-full bg-[#93F514]/20 blur-2xl pointer-events-none top-4"></div>

                        <!-- Vector Professional Person Illustration -->
                        <div class="relative z-10 flex flex-col items-center">
                            <svg class="w-48 h-48 sm:w-52 sm:h-52 drop-shadow-[0_15px_25px_rgba(0,0,0,0.8)]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="120" cy="120" r="90" fill="#051406" stroke="#93F514" stroke-width="2" stroke-dasharray="6 6" stroke-opacity="0.6"/>
                                <circle cx="120" cy="120" r="76" fill="#08220a" fill-opacity="0.8"/>
                                
                                <path d="M45 75h8m-4-4v8" stroke="#93F514" stroke-width="2" stroke-linecap="round"/>
                                <path d="M195 90h8m-4-4v8" stroke="#46ee40" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="50" cy="160" r="3" fill="#93F514" fill-opacity="0.6"/>
                                <circle cx="190" cy="150" r="3.5" fill="#5FE6B6" fill-opacity="0.7"/>

                                <path d="M60 216c0-33 27-56 60-56s60 23 60 56v4H60v-4z" fill="#0d3511"/>
                                <path d="M85 220c3-25 18-42 35-42s32 17 35 42H85z" fill="#144d1a"/>
                                
                                <path d="M116 160l4 35 4-35h-8z" fill="#93F514"/>
                                <circle cx="120" cy="162" r="3" fill="#ffffff"/>

                                <path d="M102 142l18 20 18-20-10-8h-16l-10 8z" fill="#EEEEEE"/>
                                <path d="M110 128h20v18h-20z" fill="#e0a97a"/>

                                <path d="M96 95c0 22 10.7 39 24 39s24-17 24-39-10.7-35-24-35-24 13-24 35z" fill="#f5c296"/>
                                
                                <path d="M93 85c2-22 15-32 32-32 15 0 26 8 28 20 2 12-2 18-5 18s-4-10-12-12c-9-2-19 3-23 8-3 4-8 4-10-2z" fill="#1b241c"/>
                                <path d="M93 88c0 8 2 15 3 17 1.5-3 3-8 3-12 0-8-3-10-6-5z" fill="#1b241c"/>

                                <circle cx="95" cy="98" r="5.5" fill="#e0a97a"/>
                                <circle cx="145" cy="98" r="5.5" fill="#e0a97a"/>

                                <circle cx="111" cy="94" r="2.5" fill="#1b241c"/>
                                <circle cx="129" cy="94" r="2.5" fill="#1b241c"/>
                                <circle cx="112" cy="93" r="0.8" fill="#ffffff"/>
                                <circle cx="130" cy="93" r="0.8" fill="#ffffff"/>

                                <path d="M106 88c3-2 8-2 10 0" stroke="#1b241c" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M124 88c2-2 7-2 10 0" stroke="#1b241c" stroke-width="1.8" stroke-linecap="round"/>

                                <path d="M113 112c3.5 4 10.5 4 14 0" stroke="#a65832" stroke-width="2" stroke-linecap="round"/>

                                <g transform="translate(142, 140)">
                                    <rect width="44" height="34" rx="6" fill="#051406" stroke="#93F514" stroke-width="1.5"/>
                                    <path d="M12 0h20a3 3 0 013 3v4H9V3a3 3 0 013-3z" fill="#93F514" fill-opacity="0.3" stroke="#93F514" stroke-width="1.5"/>
                                    <circle cx="22" cy="18" r="4" fill="#93F514"/>
                                    <path d="M14 26h16" stroke="#93F514" stroke-width="1.5" stroke-linecap="round" opacity="0.6"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Bottom Back Link -->
                <div class="relative z-10 pt-2">
                    <a href="{{ url('/') }}" 
                       class="inline-flex items-center gap-2 text-xs font-medium text-gray-300 hover:text-[#93F514] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>

            <!-- RIGHT COLUMN: Registration Form (7 Cols) -->
            <div class="lg:col-span-7 p-4 sm:p-6 lg:p-8 flex flex-col justify-center relative z-10">
                
                <!-- Form Header -->
                <div class="mb-4 sm:mb-5">
                    <div class="flex items-center justify-between mb-1">
                        <h1 class="heading-font text-2xl sm:text-3xl font-bold text-white tracking-tight">
                            Buat Akun Baru
                        </h1>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-400 font-normal">
                        Silakan pilih tipe pendaftaran dan lengkapi data Anda.
                    </p>
                </div>

                <!-- Session Error Message -->
                @if (session('error'))
                    <div class="mb-4 p-3.5 rounded-xl bg-red-500/15 border border-red-500/40 text-red-400 text-xs font-medium flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- SEGMENTED SELECTOR: Tipe Pendaftar -->
                <div class="mb-5 p-1 bg-black/50 border border-white/10 rounded-2xl grid grid-cols-2 gap-1 shadow-inner">
                    <button type="button" 
                            wire:click="$set('account_type', 'applicant')"
                            class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 cursor-pointer {{ $account_type === 'applicant' ? 'bg-[#93F514] text-black shadow-md shadow-[#93F514]/20 scale-[1.01]' : 'text-gray-400 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Pelamar Kerja</span>
                    </button>

                    <button type="button" 
                            wire:click="$set('account_type', 'employee')"
                            class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-200 cursor-pointer {{ $account_type === 'employee' ? 'bg-[#93F514] text-black shadow-md shadow-[#93F514]/20 scale-[1.01]' : 'text-gray-400 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Karyawan Internal</span>
                    </button>
                </div>

                @if ($account_type === 'applicant')
                    <!-- Google Sign Up Button for Applicant -->
                    <a href="{{ route('auth.google') }}" 
                       onclick="document.getElementById('google-auth-loader')?.classList.remove('hidden')"
                       class="w-full py-3 px-4 mb-4 rounded-xl border border-white/20 bg-white/5 hover:bg-white/10 active:scale-[0.99] text-white font-medium text-sm flex items-center justify-center gap-3 transition shadow-sm hover:border-white/30 group">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        <span>Daftar dengan Google</span>
                    </a>

                    <!-- Or Divider -->
                    <div class="relative flex items-center justify-center mb-4">
                        <div class="border-t border-white/10 w-full"></div>
                        <span class="bg-[#051406] px-3 text-[11px] text-gray-400 uppercase tracking-wider font-medium absolute">atau daftar manual</span>
                    </div>
                @endif

                @if ($account_type === 'employee')
                    <!-- Information Callout for Employee -->
                    <div class="mb-4 p-3 bg-[#93F514]/10 border border-[#93F514]/30 rounded-xl text-xs text-[#93F514] flex items-start gap-2.5">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <span class="font-semibold block text-white">Khusus Karyawan Internal MIKA Group</span>
                            Masukkan kode token perusahaan yang diberikan oleh tim HR untuk memverifikasi akun karyawan Anda.
                        </div>
                    </div>
                @endif

                <form wire:submit="register" class="space-y-3 sm:space-y-3.5">
                    
                    <!-- NIK Field -->
                    <div>
                        <label for="nik" class="block text-xs font-semibold text-gray-300 mb-1">
                            Nomor Induk Kependudukan (NIK KTP - 16 Digit)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <input wire:model="nik" 
                                   id="nik" 
                                   type="text" 
                                   name="nik" 
                                   maxlength="16"
                                   inputmode="numeric"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   required 
                                   autofocus 
                                   autocomplete="nik" 
                                   placeholder="16 digit nomor NIK sesuai KTP"
                                   class="w-full pl-10 pr-4 py-2.5 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />
                        </div>
                        @if ($errors->has('nik'))
                            <p class="mt-1 text-xs text-red-400 font-normal flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $errors->first('nik') }}
                            </p>
                        @endif
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-300 mb-1">
                            Nama Lengkap
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input wire:model="name" 
                                   id="name" 
                                   type="text" 
                                   name="name" 
                                   required 
                                   autocomplete="name" 
                                   placeholder="Nama lengkap"
                                   class="w-full pl-10 pr-4 py-2.5 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />
                        </div>
                        @if ($errors->has('name'))
                            <p class="mt-1 text-xs text-red-400 font-normal flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $errors->first('name') }}
                            </p>
                        @endif
                    </div>

                    <!-- Alamat Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-300 mb-1">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input wire:model="email" 
                                   id="email" 
                                   type="email" 
                                   name="email" 
                                   required 
                                   autocomplete="username" 
                                   placeholder="nama@email.com"
                                   class="w-full pl-10 pr-4 py-2.5 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />
                        </div>
                        @if ($errors->has('email'))
                            <p class="mt-1 text-xs text-red-400 font-normal flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $errors->first('email') }}
                            </p>
                        @endif
                    </div>

                    @if ($account_type === 'employee')
                        <!-- EMPLOYEE ONLY: Tipe Pegawai / Status Hubungan Kerja -->
                        <div>
                            <label for="employee_type" class="block text-xs font-semibold text-gray-300 mb-1">
                                Status / Kategori Pegawai <span class="text-[#93F514]">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model="employee_type" 
                                        id="employee_type" 
                                        required
                                        class="w-full px-3.5 py-2.5 bg-black/60 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white transition outline-none cursor-pointer">
                                    <option value="permanent" class="bg-gray-900 text-white">Karyawan Tetap (Permanent)</option>
                                    <option value="contract" class="bg-gray-900 text-white">Karyawan Kontrak (Contract / PKWT)</option>
                                    <option value="internship" class="bg-gray-900 text-white">Magang / Internship</option>
                                </select>
                            </div>
                            @if ($errors->has('employee_type'))
                                <p class="mt-1 text-xs text-red-400 font-normal">
                                    {{ $errors->first('employee_type') }}
                                </p>
                            @endif
                        </div>

                        <!-- EMPLOYEE ONLY: Perusahaan / Entitas (Company) -->
                        <div>
                            <label for="company_id" class="block text-xs font-semibold text-gray-300 mb-1 flex items-center justify-between">
                                <span>Perusahaan / Entitas <span class="text-[#93F514]">*</span></span>
                                <span class="text-[10px] text-gray-400 font-normal">Wajib dipilih</span>
                            </label>
                            <div class="relative">
                                <select wire:model.live="company_id" 
                                        id="company_id" 
                                        required
                                        class="w-full px-3.5 py-2.5 bg-black/60 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white transition outline-none cursor-pointer">
                                    <option value="" class="bg-gray-900 text-gray-400">-- Pilih Perusahaan --</option>
                                    @foreach ($companies as $comp)
                                        <option value="{{ $comp->id }}" class="bg-gray-900 text-white">
                                            {{ $comp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('company_id'))
                                <p class="mt-1 text-xs text-red-400 font-normal">
                                    {{ $errors->first('company_id') }}
                                </p>
                            @endif
                        </div>

                        <!-- EMPLOYEE ONLY: Departemen & Posisi Grid (Cascading) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-3">
                            <!-- Departemen -->
                            <div>
                                <label for="department_id" class="block text-xs font-semibold text-gray-300 mb-1 flex items-center justify-between">
                                    <span>Departemen / Divisi <span class="text-[#93F514]">*</span></span>
                                    @if (!$company_id)
                                        <span class="text-[10px] text-amber-400/80 font-normal">Pilih Perusahaan dulu</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <select wire:model.live="department_id" 
                                            id="department_id" 
                                            required
                                            {{ !$company_id ? 'disabled' : '' }}
                                            class="w-full px-3.5 py-2.5 bg-black/60 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white transition outline-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        @if (!$company_id)
                                            <option value="" class="bg-gray-900 text-gray-400">-- Pilih Perusahaan Dahulu --</option>
                                        @else
                                            <option value="" class="bg-gray-900 text-gray-400">-- Pilih Departemen --</option>
                                            @forelse ($departments as $dept)
                                                <option value="{{ $dept->id }}" class="bg-gray-900 text-white">
                                                    {{ $dept->name }}
                                                </option>
                                            @empty
                                                <option value="" disabled class="bg-gray-900 text-gray-500">Tidak ada departemen terdaftar</option>
                                            @endforelse
                                        @endif
                                    </select>
                                </div>
                                @if ($errors->has('department_id'))
                                    <p class="mt-1 text-xs text-red-400 font-normal">
                                        {{ $errors->first('department_id') }}
                                    </p>
                                @endif
                            </div>

                            <!-- Pilihan Posisi Baku (Master Data) -->
                            <div>
                                <label for="position_id" class="block text-xs font-semibold text-gray-300 mb-1 flex items-center justify-between">
                                    <span>Pilih Jabatan Baku</span>
                                    @if (!$department_id)
                                        <span class="text-[10px] text-gray-500 font-normal">Pilih Dept dulu</span>
                                    @else
                                        <span class="text-[10px] text-[#93F514] font-normal">Tersaring</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <select wire:model.live="position_id" 
                                            id="position_id" 
                                            {{ !$department_id ? 'disabled' : '' }}
                                            class="w-full px-3.5 py-2.5 bg-black/60 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white transition outline-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        @if (!$department_id)
                                            <option value="" class="bg-gray-900 text-gray-400">-- Pilih Departemen Dahulu --</option>
                                        @else
                                            <option value="" class="bg-gray-900 text-gray-400">-- Pilih Posisi (Opsional) --</option>
                                            @forelse ($positions as $pos)
                                                <option value="{{ $pos->id }}" class="bg-gray-900 text-white">
                                                    {{ $pos->name }}
                                                </option>
                                            @empty
                                                <option value="" disabled class="bg-gray-900 text-gray-500">Tidak ada posisi terdaftar</option>
                                            @endforelse
                                        @endif
                                    </select>
                                </div>
                                @if ($errors->has('position_id'))
                                    <p class="mt-1 text-xs text-red-400 font-normal">
                                        {{ $errors->first('position_id') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Posisi / Jabatan Input Kustom -->
                        <div>
                            <label for="position_title" class="block text-xs font-semibold text-gray-300 mb-1 flex items-center justify-between">
                                <span>Nama Jabatan / Posisi Spesifik</span>
                                <span class="text-[10px] text-gray-400 font-normal">Dapat disesuaikan</span>
                            </label>
                            <input wire:model="position_title" 
                                   id="position_title" 
                                   type="text" 
                                   placeholder="Contoh: Staff IT, Supervisor HR, Account Executive, dll"
                                   class="w-full px-3.5 py-2.5 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />
                            @if ($errors->has('position_title'))
                                <p class="mt-1 text-xs text-red-400 font-normal">
                                    {{ $errors->first('position_title') }}
                                </p>
                            @endif
                        </div>

                        <!-- EMPLOYEE ONLY: Token Perusahaan (Passkey) -->
                        <div>
                            <label for="company_passkey" class="block text-xs font-semibold text-[#93F514] mb-1 flex items-center justify-between">
                                <span>Kode Token Perusahaan (Passkey HR) *</span>
                                <span class="text-[10px] text-gray-400 font-normal">Wajib diisi</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#93F514]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                </div>
                                <input wire:model="company_passkey" 
                                       id="company_passkey" 
                                       type="password" 
                                       required 
                                       placeholder="Masukkan kode token dari HR"
                                       class="w-full pl-10 pr-4 py-2.5 bg-black/40 border border-[#93F514]/40 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />
                            </div>
                            @if ($errors->has('company_passkey'))
                                <p class="mt-1 text-xs text-red-400 font-normal flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $errors->first('company_passkey') }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <!-- Password and Confirmation Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-3">
                        <!-- Password -->
                        <div x-data="{ showPassword: false }">
                            <label for="password" class="block text-xs font-semibold text-gray-300 mb-1">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <input wire:model="password" 
                                       id="password" 
                                       x-bind:type="showPassword ? 'text' : 'password'"
                                       type="password" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password" 
                                       placeholder="Min. 8 karakter"
                                       class="w-full pl-3.5 pr-10 py-2.5 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />

                                <button type="button" @click="showPassword = !showPassword" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#93F514] transition focus:outline-none" 
                                        title="Tampilkan/Sembunyikan Kata Sandi">
                                    <svg x-show="!showPassword" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @if ($errors->has('password'))
                                <p class="mt-1 text-xs text-red-400 font-normal">
                                    {{ $errors->first('password') }}
                                </p>
                            @endif
                        </div>

                        <!-- Confirm Password -->
                        <div x-data="{ showConfirmPassword: false }">
                            <label for="password_confirmation" class="block text-xs font-semibold text-gray-300 mb-1">
                                Ulangi Sandi
                            </label>
                            <div class="relative">
                                <input wire:model="password_confirmation" 
                                       id="password_confirmation" 
                                       x-bind:type="showConfirmPassword ? 'text' : 'password'"
                                       type="password" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password" 
                                       placeholder="Ulangi sandi"
                                       class="w-full pl-3.5 pr-10 py-2.5 bg-black/40 border border-white/15 focus:border-[#93F514] focus:ring-1 focus:ring-[#93F514] rounded-xl text-sm text-white placeholder-gray-500 transition outline-none" />

                                <button type="button" @click="showConfirmPassword = !showConfirmPassword" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#93F514] transition focus:outline-none" 
                                        title="Tampilkan/Sembunyikan Konfirmasi Sandi">
                                    <svg x-show="!showConfirmPassword" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showConfirmPassword" x-cloak class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @if ($errors->has('password_confirmation'))
                                <p class="mt-1 text-xs text-red-400 font-normal">
                                    {{ $errors->first('password_confirmation') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="w-full py-3 px-4 bg-[#93F514] hover:bg-[#82dc0e] active:scale-[0.99] disabled:opacity-60 disabled:cursor-not-allowed text-black font-semibold text-sm rounded-xl shadow-lg shadow-[#93F514]/20 hover:shadow-[#93F514]/30 transition flex items-center justify-center gap-2 group cursor-pointer">
                            <span wire:loading.remove wire:target="register" class="inline-flex items-center gap-2">
                                <span>{{ $account_type === 'employee' ? 'Daftar Sebagai Karyawan' : 'Daftar Sekarang' }}</span>
                                {{-- <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg> --}}
                            </span>
                            <span wire:loading.inline-flex wire:target="register" class="items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-black shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mendaftarkan...</span>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Divider & Login Link -->
                <div class="mt-6 pt-4 border-t border-white/10 text-center">
                    <p class="text-xs text-gray-400">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" 
                           class="font-semibold text-[#93F514] hover:underline ml-1">
                            Masuk
                        </a>
                    </p>
                </div>

            </div>

        </div>

    </div>
</div>
