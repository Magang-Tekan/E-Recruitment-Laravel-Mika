{{-- TAB 6: SOCIAL MEDIA & PORTOFOLIO --}}
<div x-show="activeDetailTab === 'social_media'" class="space-y-4">
    <h4 class="font-bold text-white mb-2 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
        </svg>
        Media Sosial & Portofolio Digital
    </h4>
    @forelse ($socials as $sm)
        @if($loop->first)<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">@endif
            <a href="{{ $sm->url }}" target="_blank"
                class="p-3 bg-slate-800/50 hover:bg-slate-800 rounded-2xl border border-slate-800 transition flex items-center justify-between group">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-950/50 text-indigo-400 flex items-center justify-center font-bold text-xs uppercase">
                        {{ strtoupper(substr($sm->platform_name ?? 'SM', 0, 2)) }}
                    </div>
                    <div>
                        <span class="font-bold text-white text-xs block">{{ $sm->platform_name }}</span>
                        <span class="text-slate-400 text-[11px] truncate max-w-[200px] block">{{ $sm->url }}</span>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-400 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        @if($loop->last)</div>@endif
    @empty
        <div class="p-4 bg-slate-800/40 rounded-2xl text-slate-400 italic text-[11px] text-center border border-slate-800">
            Belum ada akun media sosial atau tautan portofolio yang ditautkan.
        </div>
    @endforelse
</div>
