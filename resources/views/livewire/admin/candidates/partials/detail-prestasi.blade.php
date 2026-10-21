{{-- TAB 5: ORGANISASI & PRESTASI --}}
<div x-show="activeDetailTab === 'prestasi'" class="space-y-5">
    {{-- Organisasi --}}
    <div>
        <h4 class="font-bold text-white mb-2.5 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>
            Pengalaman Organisasi
        </h4>
        @forelse ($orgs as $org)
            <div class="p-3 bg-slate-800/50 rounded-xl border border-slate-800 flex justify-between items-center gap-3 mb-2">
                <div>
                    <span class="font-semibold text-white block text-xs">{{ $org->name }}</span>
                    <span class="text-indigo-400 font-medium text-[11px]">Jabatan: {{ $org->position }}</span>
                </div>
                <span class="text-slate-400 text-[11px] shrink-0">
                    {{ $org->start_year }} - {{ $org->is_active ? 'Aktif' : ($org->end_year ?? '') }}
                </span>
            </div>
        @empty
            <div class="p-3 bg-slate-800/40 rounded-xl text-slate-400 italic text-[11px] border border-slate-800">
                Belum ada riwayat organisasi.
            </div>
        @endforelse
    </div>

    {{-- Prestasi --}}
    <div>
        <h4 class="font-bold text-white mb-2.5 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
            </svg>
            Prestasi & Penghargaan
        </h4>
        @forelse ($achievements as $ach)
            <div class="p-3 bg-slate-800/50 rounded-xl border border-slate-800 flex items-center justify-between gap-3 mb-2">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-white truncate text-xs">{{ $ach->name }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-950/60 text-amber-400 rounded-md border border-amber-800 shrink-0">{{ $ach->scale ?? 'Kompetisi' }}</span>
                    </div>
                    <span class="text-slate-400 text-[11px]">{{ $ach->month ? $ach->month . ' ' : '' }}{{ $ach->year }}</span>
                </div>
                @if($ach->certificate_path)
                    <a href="{{ \Illuminate\Support\Str::startsWith($ach->certificate_path, ['http://', 'https://']) ? $ach->certificate_path : asset('storage/' . $ach->certificate_path) }}" target="_blank"
                        class="px-2.5 py-1 text-xs font-semibold text-indigo-400 bg-indigo-950/60 hover:bg-indigo-900/80 rounded-lg border border-indigo-800/80 transition shrink-0">
                        Lihat Berkas
                    </a>
                @endif
            </div>
        @empty
            <div class="p-3 bg-slate-800/40 rounded-xl text-slate-400 italic text-[11px] border border-slate-800">
                Belum ada prestasi dicantumkan.
            </div>
        @endforelse
    </div>
</div>
