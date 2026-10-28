{{-- TAB 3: RIWAYAT PENDIDIKAN --}}
<div x-show="activeDetailTab === 'pendidikan'" class="space-y-3">
    <h4 class="font-bold text-white mb-2 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
        </svg>
        Riwayat Pendidikan Formal
    </h4>
    @forelse ($educations as $edu)
        <div class="p-3.5 bg-slate-800/50 rounded-2xl border border-slate-800 flex justify-between items-center gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-white text-xs block">{{ $edu->school_name ?? $edu->institution_name }}</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-indigo-950/60 text-indigo-400 border border-indigo-800/60">{{ $edu->degree ?? 'Pendidikan' }}</span>
                </div>
                <div class="flex items-center flex-wrap gap-1.5 mt-1">
                    @if($edu->major && $edu->major !== '-')
                        <span class="text-slate-300 font-medium text-[11px]">Jurusan: <span class="font-semibold">{{ $edu->major }}</span></span>
                    @endif
                    @if($edu->study_program && $edu->study_program !== $edu->major)
                        <span class="text-slate-400 text-[11px]">-</span>
                        <span class="text-slate-400 text-[11px]">Prodi: <span class="font-medium">{{ $edu->study_program }}</span></span>
                    @endif
                    @if($edu->gpa)
                        <span class="text-slate-400 text-[11px]">-</span>
                        <span class="text-emerald-400 font-semibold text-[11px]">IPK / Nilai: {{ $edu->gpa }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($edu->certificate_path)
                    <a href="{{ \Illuminate\Support\Str::startsWith($edu->certificate_path, ['http://', 'https://']) ? $edu->certificate_path : asset('storage/' . $edu->certificate_path) }}" target="_blank"
                        class="px-2.5 py-1 text-[11px] font-semibold text-indigo-300 bg-indigo-950/80 hover:bg-indigo-900 border border-indigo-700/60 rounded-lg transition inline-flex items-center gap-1">
                        <svg class="w-3 h-3 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Lihat Ijazah</span>
                    </a>
                @endif
                <span class="text-slate-400 text-[11px] font-medium bg-slate-900 px-2.5 py-1 rounded-xl border border-slate-800">
                    {{ $edu->start_year }} - {{ $edu->end_year ?: 'Sekarang' }}
                </span>
            </div>
        </div>
    @empty
        <div class="p-4 bg-slate-800/40 rounded-2xl text-slate-400 italic text-[11px] text-center border border-slate-800">
            Belum ada riwayat pendidikan formal yang dicantumkan.
        </div>
    @endforelse
</div>
