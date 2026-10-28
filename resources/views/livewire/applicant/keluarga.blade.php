<div class="space-y-6">
    <!-- Header Card (Clean Enterprise Blueprint) -->
    <div
        class="relative overflow-hidden bg-white dark:bg-[#0D1527] rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54] border-l-4 border-l-emerald-600 dark:border-l-[#93F514] p-6 md:p-7 transition-colors">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span>{{ __('Data Latar Belakang Keluarga') }}</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 font-medium">
                    {{ __('* Isilah data susunan keluarga inti (orang tua, saudara) dan keluarga Anda (suami/istri, anak) dengan lengkap dan sebenarnya.') }}
                </p>
            </div>
            @if($marital_status)
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-xs font-semibold text-slate-700 dark:text-slate-300 self-start md:self-auto">
                    <span class="w-2 h-2 rounded-full {{ $marital_status === 'menikah' ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
                    <span>{{ __('Status Pernikahan:') }} <strong class="text-slate-900 dark:text-white capitalize">{{ __($marital_status) }}</strong></span>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white dark:bg-[#0D1527] p-6 md:p-8 rounded-2xl shadow-sm border border-slate-200/80 dark:border-[#1D2E54]">
        <form wire:submit.prevent="save" class="space-y-8">

            <!-- Section 1: Urutan Anak & Jumlah Bersaudara (Smart Reactive Inputs) -->
            <div class="p-5 bg-slate-50/70 dark:bg-[#14203A]/40 rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                            {{ __('Susunan Urutan Kelahiran') }} <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ __('Tabel keluarga otomatis menampilkan baris Kakak & Adik berdasarkan angka yang Anda masukkan.') }}
                        </p>
                    </div>

                    @php
                        $cSeq = (int) $child_sequence;
                        $tSib = (int) $total_siblings;
                        $kakakCount = $cSeq > 1 ? ($cSeq - 1) : 0;
                        $adikCount = ($tSib > $cSeq && $cSeq >= 1) ? ($tSib - $cSeq) : 0;
                    @endphp

                    @if($cSeq > 0 && $tSib > 0)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/40 text-[11px] font-semibold text-emerald-700 dark:text-[#93F514] w-fit">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $kakakCount }} {{ __('Kakak') }} &ndash; {{ __('Anda (Anak ke-:seq)', ['seq' => $cSeq]) }} &bull; {{ $adikCount }} {{ __('Adik') }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 sm:gap-3 pt-1">
                    <div class="inline-flex items-center gap-2 bg-white dark:bg-[#0D1527] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl px-3 py-1.5 shadow-2xs">
                        <span class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300 shrink-0">{{ __('Anak ke') }}</span>
                        <input type="number" min="1" max="25" wire:model.live.debounce.300ms="child_sequence" placeholder="1"
                            class="w-16 px-2 py-1.5 rounded-lg border border-slate-200/90 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm font-bold text-center focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] focus:border-transparent transition shrink-0">
                    </div>

                    <div class="inline-flex items-center gap-2 bg-white dark:bg-[#0D1527] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl px-3 py-1.5 shadow-2xs">
                        <span class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 shrink-0">{{ __('dari') }}</span>
                        <input type="number" min="1" max="25" wire:model.live.debounce.300ms="total_siblings" placeholder="1"
                            class="w-16 px-2 py-1.5 rounded-lg border border-slate-200/90 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-sm font-bold text-center focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] focus:border-transparent transition shrink-0">
                        <span class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 shrink-0">{{ __('bersaudara') }}</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-1 sm:gap-4">
                    @error('child_sequence')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                    @error('total_siblings')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Section 2: TABEL LATAR BELAKANG KELUARGA (Format 8 Kolom Lengkap Gambar 2) -->
            <div class="space-y-4 pt-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80 dark:border-[#1D2E54]">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>{{ __('Latar Belakang Keluarga') }}</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ __('Lengkapi data rincian anggota keluarga pada tabel di bawah ini.') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        @if($marital_status === 'menikah' || !collect($family_members)->contains(fn($m) => str_starts_with($m['relation'] ?? '', 'Anak')))
                            <button type="button" wire:click="addChild"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 dark:bg-purple-950/40 dark:hover:bg-purple-900/50 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800/50 transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>{{ __('Tambah Anak') }}</span>
                            </button>
                        @endif

                        <button type="button" wire:click="addSibling"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:hover:bg-blue-900/50 dark:text-blue-300 border border-blue-200/80 dark:border-blue-800/50 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>{{ __('Tambah Saudara Lain') }}</span>
                        </button>
                    </div>
                </div>

                <!-- DESKTOP VIEW: Sleek Table 8 Kolom (Sesuai Gambar 2) -->
                <div class="hidden lg:block overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] shadow-xs">
                    <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300 border-collapse">
                        <thead class="bg-slate-100/80 dark:bg-[#14203A] text-slate-800 dark:text-slate-200 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200 dark:border-[#1D2E54]">
                            <tr>
                                <th scope="col" class="py-3 px-3 w-32 font-bold">{{ __('Hubungan') }}</th>
                                <th scope="col" class="py-3 px-3 min-w-[170px] font-bold">{{ __('Nama Lengkap') }}</th>
                                <th scope="col" class="py-3 px-3 w-36 font-bold">{{ __('Tanggal Lahir') }}</th>
                                <th scope="col" class="py-3 px-3 min-w-[130px] font-bold">{{ __('Tempat Lahir') }}</th>
                                <th scope="col" class="py-3 px-2 w-20 font-bold text-center">{{ __('Usia') }}</th>
                                <th scope="col" class="py-3 px-3 w-36 font-bold">{{ __('Pendidikan Terakhir') }}</th>
                                <th scope="col" class="py-3 px-3 min-w-[150px] font-bold">{{ __('Pekerjaan') }}</th>
                                <th scope="col" class="py-3 px-3 min-w-[160px] font-bold">{{ __('Nama Perusahaan') }}</th>
                                <th scope="col" class="py-3 px-2 w-12 text-center font-bold">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/70 dark:divide-[#1D2E54]">
                            @forelse($family_members as $index => $member)
                                @php
                                    $rel = $member['relation'] ?? '';
                                    $isParent = in_array($rel, ['Ayah', 'Ibu']);
                                    $isSpouse = str_contains(strtolower($rel), 'suami') || str_contains(strtolower($rel), 'istri');
                                    $isChild = str_starts_with($rel, 'Anak');
                                    $isSibling = str_starts_with($rel, 'Kakak') || str_starts_with($rel, 'Adik') || str_starts_with($rel, 'Saudara');
                                    $displayRel = preg_match('/^(Kakak|Adik|Anak|Saudara Lain)\s*(\d+)$/', $rel, $m)
                                        ? (__($m[1]) . ' ' . $m[2])
                                        : __($rel);
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-[#14203A]/40 transition" wire:key="family-row-desktop-{{ $index }}">
                                    <!-- 1. Hubungan -->
                                    <td class="py-2.5 px-3 font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            @if($isParent)
                                                <span class="w-2 h-2 rounded-full {{ $rel === 'Ayah' ? 'bg-blue-500' : 'bg-pink-500' }}"></span>
                                            @elseif($isSpouse)
                                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                            @elseif($isChild)
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            @else
                                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                            @endif
                                            <span>{{ $displayRel }}</span>
                                            @if($isParent)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 2. Nama Lengkap -->
                                    <td class="py-2.5 px-2">
                                        <input type="text" wire:model="family_members.{{ $index }}.name"
                                            placeholder="{{ __('Nama Lengkap') }}"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] transition">
                                        @error("family_members.{$index}.name")
                                            <span class="text-[10px] text-red-500 block">{{ $message }}</span>
                                        @enderror
                                    </td>

                                    <!-- 3. Tanggal Lahir -->
                                    <td class="py-2.5 px-2">
                                        <input type="date" wire:model.live="family_members.{{ $index }}.birth_date"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] transition dark:[color-scheme:dark] cursor-pointer">
                                    </td>

                                    <!-- 4. Tempat Lahir -->
                                    <td class="py-2.5 px-2">
                                        <input type="text" wire:model="family_members.{{ $index }}.birth_place"
                                            placeholder="{{ __('Kota Lahir') }}"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] transition">
                                    </td>

                                    <!-- 5. Usia -->
                                    <td class="py-2.5 px-2 text-center">
                                        <input type="number" min="0" max="130" wire:model="family_members.{{ $index }}.age"
                                            placeholder="{{ __('Usia') }}"
                                            title="{{ __('Terisi otomatis saat memilih tanggal lahir, atau dapat diketik manual') }}"
                                            class="w-16 px-1.5 py-1.5 rounded-lg border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs text-center font-medium focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] transition">
                                    </td>

                                    <!-- 6. Pendidikan Terakhir -->
                                    <td class="py-2.5 px-2">
                                        <select wire:model="family_members.{{ $index }}.education"
                                            class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] transition">
                                            <option value="">{{ __('-- Pilih --') }}</option>
                                            @foreach($educationOptions as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- 7. Pekerjaan -->
                                    <td class="py-2.5 px-2">
                                        <select wire:model="family_members.{{ $index }}.occupation"
                                            class="w-full px-2 py-1.5 rounded-lg border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] transition">
                                            <option value="">{{ __('-- Pilih Pekerjaan --') }}</option>
                                            @foreach($occupationOptions as $occ)
                                                <option value="{{ $occ }}">{{ $occ }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- 8. Nama Perusahaan -->
                                    <td class="py-2.5 px-2">
                                        <input type="text" wire:model="family_members.{{ $index }}.company"
                                            placeholder="{{ __('Nama Instansi / Perusahaan') }}"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] transition">
                                    </td>

                                    <!-- Aksi (Hapus row jika deletable) -->
                                    <td class="py-2.5 px-2 text-center">
                                        @if(!empty($member['deletable']) || (!in_array($rel, ['Ayah', 'Ibu']) && ($isChild || str_starts_with($rel, 'Saudara'))))
                                            <button type="button" wire:click="removeMember({{ $index }})"
                                                class="w-7 h-7 rounded-lg inline-flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition"
                                                title="{{ __('Hapus baris ini') }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @else
                                            <span class="text-slate-300 dark:text-slate-600 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-4 text-center text-slate-400 italic">
                                        {{ __('Belum ada data anggota keluarga.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE & TABLET VIEW: Card-Based Responsive View -->
                <div class="block lg:hidden space-y-4">
                    @foreach($family_members as $index => $member)
                        @php
                            $rel = $member['relation'] ?? '';
                            $isParent = in_array($rel, ['Ayah', 'Ibu']);
                            $isSpouse = str_contains(strtolower($rel), 'suami') || str_contains(strtolower($rel), 'istri');
                            $isChild = str_starts_with($rel, 'Anak');
                            $displayRel = preg_match('/^(Kakak|Adik|Anak|Saudara Lain)\s*(\d+)$/', $rel, $m)
                                ? (__($m[1]) . ' ' . $m[2])
                                : __($rel);
                        @endphp
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-slate-50/60 dark:bg-[#14203A]/40 space-y-3" wire:key="family-row-mobile-{{ $index }}">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-[#1D2E54]">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $isParent ? ($rel === 'Ayah' ? 'bg-blue-500' : 'bg-pink-500') : ($isSpouse ? 'bg-purple-500' : ($isChild ? 'bg-emerald-500' : 'bg-slate-400')) }}"></span>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                                        {{ $displayRel }}
                                        @if($isParent) <span class="text-red-500">*</span> @endif
                                    </h4>
                                </div>
                                @if(!empty($member['deletable']) || (!in_array($rel, ['Ayah', 'Ibu']) && ($isChild || str_starts_with($rel, 'Saudara'))))
                                    <button type="button" wire:click="removeMember({{ $index }})"
                                        class="text-xs text-red-500 hover:text-red-600 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>{{ __('Hapus') }}</span>
                                    </button>
                                @endif
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase mb-1">{{ __('Nama Lengkap') }}</label>
                                    <input type="text" wire:model="family_members.{{ $index }}.name" placeholder="{{ __('Nama Lengkap') }}"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase mb-1">{{ __('Tanggal Lahir') }}</label>
                                        <input type="date" wire:model.live="family_members.{{ $index }}.birth_date"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs dark:[color-scheme:dark]">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase mb-1">{{ __('Tempat Lahir') }}</label>
                                        <input type="text" wire:model="family_members.{{ $index }}.birth_place" placeholder="{{ __('Kota Lahir') }}"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase mb-1">{{ __('Usia (Tahun)') }}</label>
                                        <input type="number" min="0" max="130" wire:model="family_members.{{ $index }}.age" placeholder="{{ __('Usia') }}"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase mb-1">{{ __('Pendidikan Terakhir') }}</label>
                                        <select wire:model="family_members.{{ $index }}.education"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs">
                                            <option value="">{{ __('-- Pilih --') }}</option>
                                            @foreach($educationOptions as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase mb-1">{{ __('Pekerjaan') }}</label>
                                        <select wire:model="family_members.{{ $index }}.occupation"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs">
                                            <option value="">{{ __('-- Pilih Pekerjaan --') }}</option>
                                            @foreach($occupationOptions as $occ)
                                                <option value="{{ $occ }}">{{ $occ }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase mb-1">{{ __('Nama Perusahaan / Instansi') }}</label>
                                        <input type="text" wire:model="family_members.{{ $index }}.company" placeholder="{{ __('Perusahaan / Sekolah') }}"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] text-slate-900 dark:text-white text-xs">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Section 3: Nomor yang Dapat Dihubungi Ketika Terjadi Keadaan Darurat -->
            <div class="pt-6 border-t border-slate-200/80 dark:border-[#1D2E54] space-y-6">
                <div class="flex items-start gap-3 sm:gap-3.5">
                    <div
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200/80 dark:border-rose-500/20 flex items-center justify-center text-rose-500 dark:text-rose-400 shrink-0 mt-0.5 shadow-xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-snug sm:leading-normal">
                            {{ __('Nomor yang Dapat Dihubungi Ketika Terjadi Keadaan Darurat') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 leading-relaxed font-normal sm:font-medium">
                            {{ __('* Mohon cantumkan kontak darurat keluarga/kerabat yang dapat dihubungi sewaktu-waktu (opsional 1 atau 2 orang).') }}
                        </p>
                        <div class="mt-2.5 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/40 text-[11px] font-medium text-amber-800 dark:text-amber-300">
                            <svg class="w-4 h-4 shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>
                                <strong>{{ __('Perhatian:') }}</strong> {{ __('Nomor kontak darurat tidak boleh menggunakan nomor telepon pribadi Anda') }}@if(!empty($applicant_phone)) <span class="font-bold underline decoration-amber-400/80">({{ $applicant_phone }})</span>@endif.
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Kelompok 1: Satu Tempat Tinggal (Rumah) -->
                    <div
                        class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 dark:bg-[#14203A]/40 border border-slate-200/80 dark:border-[#1D2E54] space-y-5">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-200/80 dark:border-[#1D2E54]">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                {{ __('Satu Tempat Tinggal (Rumah)') }}
                            </h4>
                        </div>

                        <!-- Baris 1 -->
                        <div
                            class="space-y-3 p-4 rounded-xl bg-white dark:bg-[#0D1527] border border-slate-200/60 dark:border-[#1D2E54]/70">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('Kontak Darurat 1') }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('Nama Lengkap') }}
                                    </label>
                                    <input type="text" wire:model="emergency_same_house_name_1"
                                        placeholder="{{ __('Nama') }}"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_same_house_name_1')
                                        <span class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('No. Telp / No. HP') }}
                                    </label>
                                    <input type="tel" wire:model.live.debounce.400ms="emergency_same_house_phone_1"
                                        placeholder="{{ __('08xxxxxxxxxx') }}"
                                        class="w-full px-3 py-2 rounded-xl border @error('emergency_same_house_phone_1') border-red-500 ring-2 ring-red-500/20 text-red-900 dark:text-red-200 @else border-slate-200/80 dark:border-[#1D2E54] text-slate-900 dark:text-white @enderror bg-slate-50 dark:bg-[#14203A] text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_same_house_phone_1')
                                        <span class="text-[10px] text-red-500 mt-1 flex items-center gap-1 font-medium">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <span>{{ $message }}</span>
                                        </span>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('Hubungan') }}
                                    </label>
                                    <input type="text" wire:model="emergency_same_house_relation_1"
                                        placeholder="{{ __('Cth: Ayah / Suami / Kakak') }}"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_same_house_relation_1')
                                        <span class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Baris 2 -->
                        <div
                            class="space-y-3 p-4 rounded-xl bg-white dark:bg-[#0D1527] border border-slate-200/60 dark:border-[#1D2E54]/70">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('Kontak Darurat 2') }}</span>
                                <span class="text-[10px] text-slate-400">{{ __('(Opsional)') }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('Nama Lengkap') }}
                                    </label>
                                    <input type="text" wire:model="emergency_same_house_name_2"
                                        placeholder="{{ __('Nama') }}"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_same_house_name_2')
                                        <span class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('No. Telp / No. HP') }}
                                    </label>
                                    <input type="tel" wire:model.live.debounce.400ms="emergency_same_house_phone_2"
                                        placeholder="{{ __('08xxxxxxxxxx') }}"
                                        class="w-full px-3 py-2 rounded-xl border @error('emergency_same_house_phone_2') border-red-500 ring-2 ring-red-500/20 text-red-900 dark:text-red-200 @else border-slate-200/80 dark:border-[#1D2E54] text-slate-900 dark:text-white @enderror bg-slate-50 dark:bg-[#14203A] text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_same_house_phone_2')
                                        <span class="text-[10px] text-red-500 mt-1 flex items-center gap-1 font-medium">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <span>{{ $message }}</span>
                                        </span>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('Hubungan') }}
                                    </label>
                                    <input type="text" wire:model="emergency_same_house_relation_2"
                                        placeholder="{{ __('Cth: Ibu / Adik') }}"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_same_house_relation_2')
                                        <span class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Kelompok 2: Tidak Satu Tempat Tinggal (Rumah) -->
                    <div
                        class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 dark:bg-[#14203A]/40 border border-slate-200/80 dark:border-[#1D2E54] space-y-5">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-200/80 dark:border-[#1D2E54]">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                {{ __('Tidak Satu Tempat Tinggal (Rumah)') }}
                            </h4>
                        </div>

                        <!-- Baris 1 -->
                        <div
                            class="space-y-3 p-4 rounded-xl bg-white dark:bg-[#0D1527] border border-slate-200/60 dark:border-[#1D2E54]/70">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('Kontak Darurat 1') }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('Nama Lengkap') }}
                                    </label>
                                    <input type="text" wire:model="emergency_diff_house_name_1"
                                        placeholder="{{ __('Nama') }}"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_diff_house_name_1')
                                        <span class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('No. Telp / No. HP') }}
                                    </label>
                                    <input type="tel" wire:model.live.debounce.400ms="emergency_diff_house_phone_1"
                                        placeholder="{{ __('08xxxxxxxxxx') }}"
                                        class="w-full px-3 py-2 rounded-xl border @error('emergency_diff_house_phone_1') border-red-500 ring-2 ring-red-500/20 text-red-900 dark:text-red-200 @else border-slate-200/80 dark:border-[#1D2E54] text-slate-900 dark:text-white @enderror bg-slate-50 dark:bg-[#14203A] text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_diff_house_phone_1')
                                        <span class="text-[10px] text-red-500 mt-1 flex items-center gap-1 font-medium">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <span>{{ $message }}</span>
                                        </span>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('Hubungan') }}
                                    </label>
                                    <input type="text" wire:model="emergency_diff_house_relation_1"
                                        placeholder="{{ __('Cth: Paman / Bibi / Sahabat') }}"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_diff_house_relation_1')
                                        <span class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Baris 2 -->
                        <div
                            class="space-y-3 p-4 rounded-xl bg-white dark:bg-[#0D1527] border border-slate-200/60 dark:border-[#1D2E54]/70">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('Kontak Darurat 2') }}</span>
                                <span class="text-[10px] text-slate-400">{{ __('(Opsional)') }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('Nama Lengkap') }}
                                    </label>
                                    <input type="text" wire:model="emergency_diff_house_name_2"
                                        placeholder="{{ __('Nama') }}"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_diff_house_name_2')
                                        <span class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('No. Telp / No. HP') }}
                                    </label>
                                    <input type="tel" wire:model.live.debounce.400ms="emergency_diff_house_phone_2"
                                        placeholder="{{ __('08xxxxxxxxxx') }}"
                                        class="w-full px-3 py-2 rounded-xl border @error('emergency_diff_house_phone_2') border-red-500 ring-2 ring-red-500/20 text-red-900 dark:text-red-200 @else border-slate-200/80 dark:border-[#1D2E54] text-slate-900 dark:text-white @enderror bg-slate-50 dark:bg-[#14203A] text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_diff_house_phone_2')
                                        <span class="text-[10px] text-red-500 mt-1 flex items-center gap-1 font-medium">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <span>{{ $message }}</span>
                                        </span>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ __('Hubungan') }}
                                    </label>
                                    <input type="text" wire:model="emergency_diff_house_relation_2"
                                        placeholder="{{ __('Cth: Sepupu / Kerabat') }}"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200/80 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A] text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:border-transparent transition">
                                    @error('emergency_diff_house_relation_2')
                                        <span class="text-[10px] text-red-500 mt-0.5 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-slate-200/80 dark:border-[#1D2E54]">
                <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-70 disabled:cursor-not-allowed text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition duration-150 ease-in-out active:scale-95 cursor-pointer">
                    <svg wire:loading.remove wire:target="save" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin shrink-0 text-white dark:text-black" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="save">{{ __('Simpan Data Keluarga') }}</span>
                    <span wire:loading wire:target="save">{{ __('Menyimpan...') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
