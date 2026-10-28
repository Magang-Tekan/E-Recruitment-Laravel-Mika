{{-- TAB 1: DATA PRIBADI --}}
<div x-show="activeDetailTab === 'pribadi'" class="space-y-4">
    <div>
        <h4 class="font-bold text-white mb-3 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Informasi Pribadi & Kontak
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-slate-800/50 p-4 rounded-2xl border border-slate-800">
            @foreach ([
                ['NIK', $prof?->nik],
                ['NPWP', $prof?->npwp],
                ['Email', $selectedCandidate->email, 'break-all'],
                ['Nomor Telepon / WhatsApp', $prof?->phone],
                ['Jenis Kelamin', $prof?->gender],
                ['Tempat, Tanggal Lahir', ($prof?->birth_place ?? '') . ($prof?->birth_date ? ', ' . \Carbon\Carbon::parse($prof->birth_date)->translatedFormat('d M Y') : '')],
                ['Agama', $prof?->religion],
                ['Suku', $prof?->ethnicity],
                ['Kebangsaan', $prof?->nationality],
                ['Golongan Darah', $prof?->blood_type],
                ['Status Pernikahan', $prof?->marital_status ? (ucfirst($prof->marital_status) . ($prof->marriage_date ? ' (' . \Carbon\Carbon::parse($prof->marriage_date)->translatedFormat('d M Y') . ')' : '')) : null],
                ['Transportasi Harian', $prof?->daily_transportation],
                ['Jarak ke Tempat Kerja', $prof?->work_distance_km ? $prof->work_distance_km . ' km' : null],
                ['Kota & Provinsi Sesuai KTP', ($prof?->city ?? '') . ($prof?->province ? ', ' . $prof->province : '')],
                ['Kode Pos', $prof?->postal_code],
            ] as $field)
                <div>
                    <span class="text-slate-400 block text-[11px]">{{ $field[0] }}:</span>
                    <span class="font-semibold text-slate-200 {{ $field[2] ?? '' }}">{{ $field[1] ?: '-' }}</span>
                </div>
            @endforeach
            <div class="sm:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-700/60">
                <div>
                    <span class="text-slate-400 block text-[11px]">Alamat Sesuai KTP:</span>
                    <span class="font-semibold text-slate-200">{{ $prof?->ktp_address ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Alamat Domisili Saat Ini:</span>
                    <span class="font-semibold text-slate-200">{{ $prof?->address ?? '-' }}</span>
                </div>
            </div>
            @if(!empty($prof?->driving_licenses) && is_array($prof->driving_licenses))
                <div class="sm:col-span-3 pt-2 border-t border-slate-700/60">
                    <span class="text-slate-400 block text-[11px] mb-1.5">Kepemilikan SIM (Surat Izin Mengemudi):</span>
                    <div class="flex flex-wrap gap-2">
                        @php $hasLic = false; @endphp
                        @foreach($prof->driving_licenses as $lic)
                            @if(!empty($lic['type']) || !empty($lic['number']))
                                @php $hasLic = true; @endphp
                                <span class="px-2.5 py-1 bg-slate-900/60 border border-slate-700 rounded-lg text-xs font-medium text-slate-200">
                                    <strong class="text-emerald-400">{{ $lic['type'] ?: 'SIM' }}:</strong> {{ $lic['number'] ?: '-' }}
                                </span>
                            @endif
                        @endforeach
                        @if(!$hasLic)
                            <span class="text-xs text-slate-500 italic">Tidak ada SIM yang dicantumkan</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- CV / Resume --}}
    <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <span class="font-bold text-white text-xs block">Dokumen CV / Resume Pelamar</span>
            <p class="text-[11px] text-slate-400 mt-0.5">File berkas curriculum vitae yang diunggah pelamar saat pendaftaran.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            @if($prof?->cv_file_url || $prof?->cv_file_path)
                <a href="{{ $prof->cv_file_url ?: asset('storage/' . $prof->cv_file_path) }}" target="_blank"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Buka File CV
                </a>
            @else
                <span class="text-xs text-slate-400 italic">Belum mengunggah file CV</span>
            @endif
        </div>
    </div>

    {{-- Tentang Saya --}}
    @if($prof?->about_me)
        <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-800">
            <h4 class="font-bold text-white mb-1.5 text-xs">Tentang Saya / Ringkasan Diri</h4>
            <p class="text-xs text-slate-300 leading-relaxed">{{ $prof->about_me }}</p>
        </div>
    @endif
</div>
