{{-- TAB 7: DATA TAMBAHAN & PREFERENSI KERJA --}}
<div x-show="activeDetailTab === 'data_tambahan'" class="space-y-4">
    {{-- Preferensi Kerja --}}
    <div>
        <h4 class="font-bold text-white mb-2.5 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Preferensi & Minat Kerja
        </h4>
        @if ($pref)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 bg-emerald-950/20 p-3.5 rounded-2xl border border-emerald-900/40">
                <div>
                    <span class="text-emerald-400 block text-[11px]">Ekspektasi Gaji:</span>
                    <span class="font-bold text-emerald-300 text-xs">
                        {{ $pref->expected_salary ? 'Rp ' . number_format($pref->expected_salary, 0, ',', '.') : '-' }}
                    </span>
                </div>
                <div>
                    <span class="text-emerald-400 block text-[11px]">Ketersediaan Kerja:</span>
                    <span class="font-semibold text-slate-200 text-xs">{{ $pref->notice_period ?: 'Dapat Segera Bekerja' }}</span>
                </div>
                <div>
                    <span class="text-emerald-400 block text-[11px]">Kesediaan Relokasi:</span>
                    <span class="font-semibold text-slate-200 text-xs">{{ $pref->is_willing_to_relocate ? 'Ya, Bersedia' : 'Tidak Bersedia' }}</span>
                </div>
                <div>
                    <span class="text-emerald-400 block text-[11px]">Bidang Diminati:</span>
                    <span class="font-semibold text-slate-200 text-xs">
                        {{ implode(', ', array_filter([$pref->interested_field_1, $pref->interested_field_2, $pref->interested_field_3])) ?: '-' }}
                    </span>
                </div>
            </div>
        @else
            <div class="p-3 bg-slate-800/40 rounded-xl text-slate-400 italic text-[11px] border border-slate-800">
                Preferensi kerja belum ditentukan oleh kandidat.
            </div>
        @endif
    </div>

    {{-- Skills & Languages --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        @foreach ([
            ['Keahlian (Skills)', $skills, 'indigo'],
            ['Kemampuan Bahasa', $languages, 'emerald'],
        ] as [$title, $items, $color])
            <div class="p-3.5 bg-slate-800/50 rounded-2xl border border-slate-800">
                <h5 class="font-bold text-white mb-2 text-xs">{{ $title }}</h5>
                @if ($items->count() > 0)
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($items as $item)
                            <span class="px-2.5 py-1 bg-{{ $color }}-950/50 text-{{ $color }}-300 rounded-lg text-[11px] font-semibold border border-{{ $color }}-900/40">
                                {{ $item->name ?? $item->skill_name ?? '-' }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <span class="text-slate-400 italic text-[11px]">Belum ada data.</span>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Sertifikasi & Pelatihan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
        @foreach ([
            ['Sertifikasi Keahlian', $certifications],
            ['Pelatihan / Kursus', $trainings],
        ] as [$title, $items])
            <div class="p-3.5 bg-slate-800/50 rounded-2xl border border-slate-800 space-y-2">
                <h5 class="font-bold text-white text-xs">{{ $title }}</h5>
                @forelse ($items as $item)
                    <div class="p-2.5 bg-slate-900 rounded-xl border border-slate-800 flex items-center justify-between gap-2">
                        <span class="font-semibold text-slate-200 truncate text-[11px]">{{ $item->name }}</span>
                        @if($item->certificate_path)
                            <a href="{{ \Illuminate\Support\Str::startsWith($item->certificate_path, ['http://', 'https://']) ? $item->certificate_path : asset('storage/' . $item->certificate_path) }}" target="_blank"
                                class="text-indigo-400 hover:underline font-semibold text-[10px] shrink-0">Lihat Berkas</a>
                        @endif
                    </div>
                @empty
                    <span class="text-slate-400 italic text-[11px]">Belum ada data.</span>
                @endforelse
            </div>
        @endforeach
    </div>
</div>
