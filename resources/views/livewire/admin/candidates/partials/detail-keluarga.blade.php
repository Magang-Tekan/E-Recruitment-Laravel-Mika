{{-- TAB 2: DATA KELUARGA --}}
<div x-show="activeDetailTab === 'keluarga'" class="space-y-4">
    <h4 class="font-bold text-white mb-2 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
        </svg>
        Susunan & Data Keluarga Pelamar
    </h4>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-800/50 p-4 rounded-2xl border border-slate-800">
        <div>
            <span class="text-slate-400 block text-[11px]">Status Pernikahan:</span>
            <span class="font-semibold text-slate-200 capitalize">{{ $prof?->marital_status ?? '-' }}</span>
        </div>
        <div>
            <span class="text-slate-400 block text-[11px]">Anak Ke-:</span>
            <span class="font-semibold text-slate-200">{{ $prof?->child_sequence ? 'Ke-' . $prof->child_sequence : '-' }}</span>
        </div>
        <div>
            <span class="text-slate-400 block text-[11px]">Dari Berapa Bersaudara:</span>
            <span class="font-semibold text-slate-200">{{ $prof?->total_siblings ? $prof->total_siblings . ' Bersaudara' : '-' }}</span>
        </div>
    </div>

    @if ($fam)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            {{-- Data Ayah & Ibu via loop --}}
            @foreach ([
                ['A', 'blue', 'Data Ayah', 'father'],
                ['I', 'pink', 'Data Ibu', 'mother'],
            ] as [$initial, $color, $title, $prefix])
                <div class="p-4 bg-slate-800/50 rounded-2xl border border-slate-800 space-y-2">
                    <div class="flex items-center gap-2 pb-2 border-b border-slate-700/60">
                        <div class="w-6 h-6 rounded-lg bg-{{ $color }}-900/50 text-{{ $color }}-400 flex items-center justify-center font-bold text-xs">{{ $initial }}</div>
                        <h5 class="font-bold text-white text-xs">{{ $title }}</h5>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Nama:</span>
                            <span class="font-semibold text-slate-200">{{ $fam->{$prefix . '_name'} ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Pekerjaan:</span>
                            <span class="font-semibold text-slate-200">{{ $fam->{$prefix . '_occupation'} ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Telepon / Kontak:</span>
                            <span class="font-semibold text-slate-200">{{ $fam->{$prefix . '_phone'} ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Tgl Lahir / Usia:</span>
                            <span class="font-semibold text-slate-200">{{ $fam->{$prefix . '_birth_date'} ? \Carbon\Carbon::parse($fam->{$prefix . '_birth_date'})->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Pasangan --}}
            @if(!empty($fam->spouse_name))
                <div class="p-4 bg-slate-800/50 rounded-2xl border border-slate-800 space-y-2 md:col-span-2">
                    <div class="flex items-center gap-2 pb-2 border-b border-slate-700/60">
                        <div class="w-6 h-6 rounded-lg bg-emerald-900/50 text-emerald-400 flex items-center justify-center font-bold text-xs">P</div>
                        <h5 class="font-bold text-white text-xs">Data Suami / Istri</h5>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                        @foreach ([
                            ['Nama Pasangan', $fam->spouse_name],
                            ['Pekerjaan', $fam->spouse_occupation ?? '-'],
                            ['Telepon', $fam->spouse_phone ?? '-'],
                            ['Tgl Lahir', $fam->spouse_birth_date ? \Carbon\Carbon::parse($fam->spouse_birth_date)->translatedFormat('d M Y') : '-'],
                        ] as [$label, $value])
                            <div>
                                <span class="text-slate-400 block text-[11px]">{{ $label }}:</span>
                                <span class="font-semibold text-slate-200">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Kontak Darurat --}}
            @if(!empty($fam->emergency_contact_name) || !empty($fam->emergency_contact_phone))
                <div class="p-4 bg-rose-950/20 rounded-2xl border border-rose-900/40 space-y-2 md:col-span-2">
                    <div class="flex items-center gap-2 pb-2 border-b border-rose-900/40">
                        <div class="w-6 h-6 rounded-lg bg-rose-900/50 text-rose-400 flex items-center justify-center font-bold text-xs">!</div>
                        <h5 class="font-bold text-rose-300 text-xs">Kontak Darurat (Emergency Contact)</h5>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                        @foreach ([
                            ['Nama Kontak', $fam->emergency_contact_name ?? '-'],
                            ['Hubungan / Relasi', $fam->emergency_contact_relation ?? '-'],
                            ['Nomor Telepon', $fam->emergency_contact_phone ?? '-'],
                        ] as [$label, $value])
                            <div>
                                <span class="text-rose-400/80 block text-[11px]">{{ $label }}:</span>
                                <span class="font-{{ $loop->first || $loop->last ? 'bold text-slate-100' : 'semibold text-slate-200' }}">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="p-4 bg-slate-800/40 rounded-2xl text-slate-400 italic text-[11px] text-center border border-slate-800">
            Data rincian keluarga belum diisi oleh pelamar.
        </div>
    @endif
</div>
