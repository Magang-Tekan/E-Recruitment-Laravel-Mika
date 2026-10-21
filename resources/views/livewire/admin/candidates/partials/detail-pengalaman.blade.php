{{-- TAB 4: PENGALAMAN KERJA --}}
<div x-show="activeDetailTab === 'pengalaman'" class="space-y-3">
    <h4 class="font-bold text-white mb-2 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        Riwayat Karir & Pengalaman Kerja
    </h4>
    @forelse ($works as $work)
        <div class="p-4 bg-slate-800/50 rounded-2xl border border-slate-800 space-y-1.5">
            <div class="flex justify-between items-start gap-2">
                <div>
                    <span class="font-bold text-white text-xs block">{{ $work->position }}</span>
                    <span class="text-indigo-400 font-semibold text-[11px]">
                        {{ $work->company_name }}{{ $work->employment_type ? ' • (' . $work->employment_type . ')' : '' }}
                    </span>
                </div>
                <span class="text-slate-400 text-[11px] bg-slate-900 px-2.5 py-1 rounded-xl border border-slate-800 shrink-0">
                    {{ $work->start_date ? \Carbon\Carbon::parse($work->start_date)->translatedFormat('M Y') : '-' }} s/d 
                    {{ $work->currently_working ? 'Sekarang' : ($work->end_date ? \Carbon\Carbon::parse($work->end_date)->translatedFormat('M Y') : '-') }}
                </span>
            </div>
            @if($work->description)
                <p class="text-slate-300 text-[11px] leading-relaxed pt-1">{{ $work->description }}</p>
            @endif
        </div>
    @empty
        <div class="p-4 bg-slate-800/40 rounded-2xl text-slate-400 italic text-[11px] text-center border border-slate-800">
            Belum ada riwayat pengalaman kerja (Fresh Graduate).
        </div>
    @endforelse
</div>
