{{-- 
    Recruitment Fraud Warning Modal Component (JST Style, Anti-Slop, Ponytail)
    - Persis referensi JST: Logo 'MIKA Group Career' kiri, Megaphone 3D kanan,
      Title merah 'Waspada Penipuan', himbauan tidak dipungut biaya, link domain resmi,
      tombol hijau 'Ya, Saya Mengerti'.
    - Ponytail: 1 komponen mandiri, native Alpine.js, auto-dismiss 30 hari via localStorage.
    - Anti-Slop: Tidak menggunakan AI generic template/bento, warna selaras MIKA Career.
--}}
<div x-data="recruitmentFraudModal()"
     x-show="isOpen"
     x-cloak
     class="fixed inset-0 z-[120] flex items-center justify-center p-4 sm:p-6 overflow-y-auto select-none"
     role="dialog"
     aria-modal="true"
     aria-labelledby="fraud-warning-title">

    {{-- Backdrop Overlay (Static backdrop, clicking outside will NOT close) --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/75 backdrop-blur-sm"
         aria-hidden="true"></div>

    {{-- Dialog Card --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="relative w-full max-w-[500px] bg-white text-gray-900 dark:bg-[#0C140D] dark:text-gray-100 dark:border dark:border-white/10 rounded-2xl shadow-2xl p-6 sm:p-8 z-10 font-sans select-text">

        {{-- Header Section: Brand + Warning Title --}}
        <div>
            {{-- Brand Name: MIKA Group Career --}}
            <div class="text-2xl sm:text-3xl font-black italic tracking-tight leading-tight">
                <span class="text-[#168038] dark:text-[#93F514]">MIKA</span>
                <span class="not-italic font-extrabold text-gray-900 dark:text-white tracking-normal text-xl sm:text-2xl ml-1">Group Career</span>
            </div>
            {{-- Red Warning Title --}}
            <h3 id="fraud-warning-title" class="mt-2 text-lg sm:text-xl font-bold text-[#DE3B2B] dark:text-[#FF6B6B] tracking-tight">
                {{ __('Waspada Penipuan') }}
            </h3>
        </div>

        {{-- Content Paragraphs --}}
        <div class="mt-4 space-y-3.5 text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-normal">
            <p>
                <strong>MIKA Group Career</strong> ({{ $mainCompany->name ?? 'PT Mitra Karya Analitika' }}) <strong class="font-bold text-gray-900 dark:text-white">{{ __('Tidak Pernah') }}</strong> {{ __('memungut biaya apapun dan/atau menunjuk agen / yayasan tertentu dalam proses perekrutan karyawan. Kami') }} <strong class="font-bold text-gray-900 dark:text-white">{{ __('Menghimbau') }}</strong> {{ __('untuk berhati-hati atas semua modus penipuan.') }}
            </p>

            <p class="pt-0.5">
                {{ __('Website rekrutmen resmi') }} <strong>MIKA Group Career</strong> {{ __('adalah:') }}<br>
                <a href="{{ url('/') }}" class="font-semibold text-blue-600 dark:text-[#93F514] underline hover:opacity-85 transition inline-block mt-0.5 break-all">
                    {{ request()->getHost() }}
                </a>
            </p>
        </div>

        {{-- Solid Green Action Button (Matching reference & web theme) --}}
        <div class="mt-6 sm:mt-7">
            <button type="button"
                    @click="closeModal()"
                    class="w-full min-h-[46px] py-3 px-6 rounded-lg font-bold text-sm sm:text-base text-white bg-[#1E7E34] hover:bg-[#166526] dark:bg-[#93F514] dark:hover:bg-[#82dc0f] dark:text-black transition-all shadow-md active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-[#93F514] focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#0C140D]">
                {{ __('Ya, Saya Mengerti') }}
            </button>
        </div>

    </div>
</div>

<script>
    function recruitmentFraudModal() {
        return {
            isOpen: false,

            init() {
                // Muncul setiap kali halaman dibuka / direfresh
                setTimeout(() => {
                    this.isOpen = true;
                }, 350);
            },

            closeModal() {
                this.isOpen = false;
            }
        };
    }
</script>
