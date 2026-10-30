<div 
    x-data="{
        isOpen: false,
        optimisticUserMessage: null,
        scrollBottom() {
            this.$nextTick(() => {
                const el = this.$refs.chatMessages;
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            });
        },
        submitMessage() {
            const inputEl = this.$refs.chatInput;
            const text = (inputEl ? inputEl.value : $wire.input || '').trim();
            if (!text) return;

            const now = new Date();
            const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

            // 1. Tampilkan pesan user seketika (0ms) di layar
            this.optimisticUserMessage = {
                text: text,
                time: timeStr
            };
            this.scrollBottom();

            // 2. Kosongkan input field segera agar terasa responsif
            if (inputEl) inputEl.value = '';

            // 3. Kirim ke Livewire di background
            if (typeof $wire !== 'undefined') {
                $wire.set('input', text, false);
                $wire.sendMessage().then(() => {
                    this.optimisticUserMessage = null;
                    this.scrollBottom();
                }).catch(() => {
                    this.optimisticUserMessage = null;
                });
            }
        },
        askPill(question) {
            const now = new Date();
            const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

            this.optimisticUserMessage = {
                text: question,
                time: timeStr
            };
            this.scrollBottom();

            if (typeof $wire !== 'undefined') {
                $wire.askQuick(question).then(() => {
                    this.optimisticUserMessage = null;
                    this.scrollBottom();
                }).catch(() => {
                    this.optimisticUserMessage = null;
                });
            }
        }
    }"
    x-init="
        window.addEventListener('chatbot-scroll-bottom', () => scrollBottom());
        if (typeof $wire !== 'undefined') {
            $wire.on('chatbot-scroll-bottom', () => scrollBottom());
        }
    "
    class="relative z-[9999]">

    <!-- Mobile Backdrop Overlay (Hanya muncul di layar HP saat chat terbuka) -->
    <div 
        x-show="isOpen" 
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="isOpen = false"
        class="sm:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[9998]"
        style="display: none;">
    </div>

    <!-- Floating Launcher Button -->
    <div class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[9999] flex items-center pointer-events-auto">
        <!-- Main Button -->
        <button 
            @click.stop="isOpen = !isOpen; if(isOpen) scrollBottom();"
            type="button"
            aria-label="Toggle Mika Assistant Chatbot"
            :class="isOpen ? 'rotate-90' : 'rotate-0'"
            class="relative flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-zinc-950 hover:bg-zinc-800 dark:bg-[#93F514] dark:hover:bg-[#82db10] text-white dark:text-black border border-zinc-800/80 dark:border-transparent shadow-2xl shadow-black/30 dark:shadow-[#93F514]/25 hover:scale-105 active:scale-95 transition-all duration-300 ease-in-out cursor-pointer touch-manipulation focus:outline-none">
            
            <!-- Close (X) Icon -->
            <svg 
                x-show="isOpen" 
                x-cloak
                x-transition:enter="transition duration-300 ease-out transform"
                x-transition:enter-start="-rotate-90 scale-50 opacity-0"
                x-transition:enter-end="rotate-0 scale-100 opacity-100"
                x-transition:leave="transition duration-200 ease-in transform"
                x-transition:leave-start="rotate-0 scale-100 opacity-100"
                x-transition:leave-end="-rotate-90 scale-50 opacity-0"
                class="w-5 h-5 sm:w-6 sm:h-6 text-white dark:text-black absolute" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>

            <!-- Chat Icon (Heroicons chat-bubble-oval-left-ellipsis) -->
            <svg 
                x-show="!isOpen" 
                x-transition:enter="transition duration-300 ease-out transform"
                x-transition:enter-start="rotate-90 scale-50 opacity-0"
                x-transition:enter-end="rotate-0 scale-100 opacity-100"
                x-transition:leave="transition duration-200 ease-in transform"
                x-transition:leave-start="rotate-0 scale-100 opacity-100"
                x-transition:leave-end="rotate-90 scale-50 opacity-0"
                class="w-6 h-6 sm:w-7 sm:h-7 text-white dark:text-black absolute" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke-width="1.6" 
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
            </svg>
        </button>
    </div>

    <!-- Chat Modal Window (Responsive: Bottom sheet di Mobile, Floating Card di Desktop) -->
    <div 
        x-show="isOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-250 transform"
        x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
        @click.outside="if (isOpen) isOpen = false"
        @keydown.escape.window="isOpen = false"
        class="fixed inset-x-3 bottom-20 sm:bottom-24 sm:right-6 sm:left-auto sm:w-[390px] h-[75vh] max-h-[580px] sm:h-[540px] bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xl shadow-black/50 flex flex-col overflow-hidden z-[9999] pointer-events-auto"
        style="display: none;">
        
        <style>
            /* Sembunyikan scrollbar visual di Windows/Chrome/Firefox/Safari tapi tetap bisa di-scroll */
            .chat-no-scrollbar::-webkit-scrollbar {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
            }
            .chat-no-scrollbar {
                -ms-overflow-style: none !important;
                scrollbar-width: none !important;
            }
        </style>

        <!-- Header -->
        <div class="px-3.5 py-3 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="relative flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-zinc-900 dark:bg-zinc-800 border border-zinc-800 dark:border-zinc-700 text-white dark:text-[#93F514] font-bold text-xs sm:text-sm select-none">
                    M
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 dark:bg-[#93F514] border-2 border-white dark:border-zinc-950"></span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h3 class="text-xs sm:text-sm font-semibold tracking-tight text-zinc-900 dark:text-white leading-none">Mika Assistant</h3>
                        <span class="px-1.5 py-0.2 text-[9px] font-medium bg-zinc-100 text-zinc-700 border-zinc-300/80 dark:bg-[#93F514]/15 dark:text-[#93F514] rounded border dark:border-[#93F514]/30 leading-tight">AI</span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 leading-none">{{ __('Asisten Rekrutmen Virtual') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <!-- Reset Conversation Button -->
                <button 
                    wire:click="clearMessages"
                    @click.prevent="if (typeof $wire !== 'undefined') $wire.clearMessages(); scrollBottom();"
                    type="button" 
                    title="{{ __('Reset Percakapan') }}"
                    class="p-1.5 text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-zinc-200 dark:hover:bg-zinc-800 rounded-md transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <!-- Close Button -->
                <button 
                    @click="isOpen = false"
                    type="button" 
                    title="{{ __('Tutup Chat') }}"
                    class="p-1.5 text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-zinc-200 dark:hover:bg-zinc-800 rounded-md transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Body -->
        <div 
            x-ref="chatMessages"
            class="chat-no-scrollbar flex-1 p-3.5 sm:p-4 overflow-y-auto space-y-3 text-xs bg-zinc-50 dark:bg-zinc-950/70 scroll-smooth">
            
            @foreach($messages as $msg)
                @if($msg['role'] === 'user')
                    <!-- Candidate Message -->
                    <div class="flex justify-end">
                        <div class="max-w-[85%] bg-zinc-900 text-white dark:bg-zinc-800 dark:text-zinc-100 rounded-2xl rounded-tr-none px-3.5 py-2.5 shadow-sm border border-zinc-800/80">
                            <p class="leading-relaxed whitespace-pre-wrap break-words">{{ $msg['text'] }}</p>
                            <span class="block text-[9px] text-zinc-400 text-right mt-1">{{ $msg['time'] ?? '' }}</span>
                        </div>
                    </div>
                @else
                    <!-- Assistant Message -->
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-md bg-zinc-900 border border-zinc-800 flex items-center justify-center text-[10px] text-white dark:text-[#93F514] font-bold shrink-0 mt-0.5 select-none">
                            M
                        </div>
                        <div class="max-w-[85%] bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 rounded-2xl rounded-tl-none px-3.5 py-2.5 shadow-sm border border-zinc-200 dark:border-zinc-800/80">
                            <div class="leading-relaxed space-y-1 break-words">
                                {!! nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', e($msg['text']))) !!}
                            </div>
                            <span class="block text-[9px] text-zinc-400 dark:text-zinc-500 mt-1.5">{{ $msg['time'] ?? '' }}</span>
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Optimistic User Bubble (Langsung muncul saat tombol Kirim ditekan) -->
            <template x-if="optimisticUserMessage">
                <div class="flex justify-end">
                    <div class="max-w-[85%] bg-zinc-900 text-white dark:bg-zinc-800 dark:text-zinc-100 rounded-2xl rounded-tr-none px-3.5 py-2.5 shadow-sm border border-zinc-800/80 animate-fade-in">
                        <p class="leading-relaxed whitespace-pre-wrap break-words" x-text="optimisticUserMessage.text"></p>
                        <span class="block text-[9px] text-zinc-400 text-right mt-1" x-text="optimisticUserMessage.time"></span>
                    </div>
                </div>
            </template>

            <!-- Typing Indicator (Muncul saat menunggu bot merespons) -->
            <div 
                x-show="optimisticUserMessage" 
                class="flex items-start gap-2 animate-fade-in"
                style="display: none;">
                <div class="w-6 h-6 rounded-md bg-zinc-900 border border-zinc-800 flex items-center justify-center text-[10px] text-white dark:text-[#93F514] font-bold shrink-0">
                    M
                </div>
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl rounded-tl-none px-3 py-2 text-zinc-500 flex items-center gap-1.5 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-[#93F514] animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-[#93F514] animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-[#93F514] animate-bounce" style="animation-delay: 300ms"></span>
                    <span class="text-[10px] ml-1 text-zinc-500 dark:text-zinc-400">{{ __('Mika Assistant sedang mengetik...') }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Questions Carousel / Pills -->
        <div class="chat-no-scrollbar px-3 py-2 bg-zinc-100/90 dark:bg-zinc-900/90 border-t border-zinc-200 dark:border-zinc-800 flex items-center gap-1.5 overflow-x-auto select-none">
            @foreach($quickPills as $pill)
                <button 
                    @click.prevent="askPill('{{ addslashes($pill) }}')"
                    type="button"
                    class="shrink-0 px-2.5 py-1 text-[11px] bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-full border border-zinc-300 dark:border-zinc-700 hover:border-zinc-400 dark:hover:border-[#93F514]/60 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer">
                    {{ $pill }}
                </button>
            @endforeach
        </div>

        <!-- Input Form -->
        <form 
            @submit.prevent="submitMessage()" 
            autocomplete="off"
            class="p-2 sm:p-2.5 bg-white dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 flex items-center gap-2 shrink-0">
            
            <input 
                x-ref="chatInput"
                wire:model="input"
                @keydown.enter.prevent="submitMessage()"
                type="text" 
                maxlength="300"
                autocomplete="off"
                placeholder="{{ __('Tanya seputar lowongan, cara melamar...') }}" 
                class="flex-1 bg-zinc-100 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 text-[14px] sm:text-xs px-3.5 py-2 sm:py-2.5 rounded-xl border border-zinc-300 dark:border-zinc-800 focus:outline-none focus:border-zinc-500 dark:focus:border-[#93F514]/70 transition-colors"
            />

            <button 
                type="submit" 
                aria-label="{{ __('Kirim Pesan') }}"
                class="w-9 h-9 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-white dark:hover:text-[#93F514] border border-zinc-800 dark:border-zinc-700 dark:hover:border-[#93F514]/70 flex items-center justify-center transition-all cursor-pointer shrink-0 shadow-sm focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transition-colors">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
            </button>
        </form>

        <!-- Bottom WhatsApp Contact (Di bawah kolom pertanyaan) -->
        <div class="px-3 py-1.5 bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-center gap-2 text-xs shrink-0 select-none">
            <span class="text-[11px] text-zinc-500 dark:text-zinc-400 font-medium">{{ __('Hubungi Tim HR:') }}</span>
            <a 
                href="{{ $waUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                title="Hubungi WhatsApp HR Resmi ({{ $rawPhone }})"
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#25D366]/15 hover:bg-[#25D366]/25 text-[#25D366] border border-[#25D366]/40 hover:border-[#25D366] transition-all font-semibold text-[11px] cursor-pointer shadow-sm">
                <!-- Chat Bubble Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                </svg>
                <span>{{ __('Admin HR') }}</span>
            </a>
        </div>
    </div>
</div>
