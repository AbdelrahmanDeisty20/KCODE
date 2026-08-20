<div x-data="{ isOpen: false, showTooltip: true }" class="{{ app()->getLocale() === 'ar' ? 'is-rtl' : 'is-ltr' }}">
    <style>
        @keyframes kcodePulseGlow {
            0%, 100% {
                box-shadow: 0 8px 25px -4px rgba(194, 89, 117, 0.4), 0 0 15px rgba(194, 89, 117, 0.2);
            }
            50% {
                box-shadow: 0 12px 32px -2px rgba(194, 89, 117, 0.65), 0 0 25px rgba(194, 89, 117, 0.4);
            }
        }

        @keyframes kcodeFloatWave {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .kcode-float-btn-wrapper {
            position: fixed;
            bottom: 28px;
            right: 28px;
            left: auto;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kcode-float-btn {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffffff 0%, #fdf4f7 100%);
            border: 2px solid #c25975;
            animation: kcodePulseGlow 3s infinite ease-in-out;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }

        .kcode-float-btn:hover {
            transform: scale(1.12) rotate(-8deg);
        }

        .kcode-float-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(194, 89, 117, 0.35);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .kcode-float-icon.is-active {
            transform: rotate(180deg) scale(1.15);
        }

        /* TOOLTIP BUBBLE CARD */
        .kcode-float-tooltip {
            position: fixed;
            bottom: 96px;
            right: 28px;
            left: auto;
            z-index: 9998;
            background: #ffffff;
            border: 1px solid rgba(194, 89, 117, 0.25);
            border-radius: 1.25rem;
            padding: 0.85rem 1.15rem;
            box-shadow: 0 12px 30px -5px rgba(194, 89, 117, 0.25);
            max-width: 260px;
            transform-origin: bottom right;
            animation: kcodeFloatWave 3.5s infinite ease-in-out;

            opacity: 0;
            visibility: hidden;
            transform: translateY(12px) scale(0.85);
            pointer-events: none;
            transition: opacity 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        visibility 0.3s step-end;
        }

        .kcode-float-tooltip.is-visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            pointer-events: auto;
            transition: opacity 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        visibility 0s step-start;
        }

        html.dark .kcode-float-tooltip {
            background: #1e1e24;
            border-color: rgba(229, 162, 181, 0.25);
            box-shadow: 0 12px 30px -5px rgba(0, 0, 0, 0.5);
        }

        .kcode-float-tooltip::after {
            content: '';
            position: absolute;
            bottom: -8px;
            right: 24px;
            left: auto;
            width: 14px;
            height: 14px;
            background: inherit;
            border-right: 1px solid rgba(194, 89, 117, 0.25);
            border-bottom: 1px solid rgba(194, 89, 117, 0.25);
            transform: rotate(45deg);
        }

        /* CHAT WINDOW POPUP DRAWER (SPRING EXPANSION & CONTRACTION) */
        .kcode-float-modal {
            position: fixed;
            bottom: 96px;
            right: 28px;
            left: auto;
            width: 395px;
            max-width: calc(100vw - 32px);
            height: 565px;
            max-height: calc(100vh - 120px);
            z-index: 10000;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(20px);
            border-radius: 1.6rem;
            border: 1.5px solid rgba(194, 89, 117, 0.22);
            box-shadow: 0 20px 45px -10px rgba(194, 89, 117, 0.35);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transform-origin: bottom right;

            opacity: 0;
            visibility: hidden;
            transform: translateY(28px) scale(0.7) rotate(3deg);
            pointer-events: none;
            transition: opacity 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
                        transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
                        visibility 0.35s step-end;
        }

        /* DYNAMIC RTL / ARABIC ADAPTATION */
        /* When in Arabic (RTL), Sidebar is on the RIGHT -> Chatbot automatically moves to BOTTOM-LEFT */
        .is-rtl .kcode-float-btn-wrapper,
        html[dir="rtl"] .kcode-float-btn-wrapper,
        [dir="rtl"] .kcode-float-btn-wrapper {
            right: auto !important;
            left: 28px !important;
        }

        .is-rtl .kcode-float-tooltip,
        html[dir="rtl"] .kcode-float-tooltip,
        [dir="rtl"] .kcode-float-tooltip {
            right: auto !important;
            left: 28px !important;
            transform-origin: bottom left !important;
        }

        .is-rtl .kcode-float-tooltip::after,
        html[dir="rtl"] .kcode-float-tooltip::after,
        [dir="rtl"] .kcode-float-tooltip::after {
            right: auto !important;
            left: 24px !important;
        }

        .is-rtl .kcode-float-modal,
        html[dir="rtl"] .kcode-float-modal,
        [dir="rtl"] .kcode-float-modal {
            right: auto !important;
            left: 28px !important;
            transform-origin: bottom left !important;
            transform: translateY(28px) scale(0.7) rotate(-3deg);
        }

        .is-rtl .kcode-float-modal.is-open,
        html[dir="rtl"] .kcode-float-modal.is-open,
        [dir="rtl"] .kcode-float-modal.is-open {
            transform: translateY(0) scale(1) rotate(0deg);
        }

        .kcode-float-modal.is-open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1) rotate(0deg);
            pointer-events: auto;
            transition: opacity 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
                        transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
                        visibility 0s step-start;
        }

        html.dark .kcode-float-modal {
            background: rgba(22, 22, 28, 0.96);
            border-color: rgba(229, 162, 181, 0.22);
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.65);
        }

        .kcode-float-header {
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, rgba(194, 89, 117, 0.12) 0%, rgba(229, 162, 181, 0.04) 100%);
            border-bottom: 1px solid rgba(194, 89, 117, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        html.dark .kcode-float-header {
            background: linear-gradient(135deg, rgba(194, 89, 117, 0.22) 0%, rgba(22, 22, 28, 0.4) 100%);
            border-bottom-color: rgba(229, 162, 181, 0.15);
        }

        .kcode-float-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            scroll-behavior: smooth;
        }

        .kcode-float-msg {
            display: flex;
            gap: 0.65rem;
            max-width: 88%;
            animation: kcodeMsgPop 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes kcodeMsgPop {
            from { opacity: 0; transform: translateY(8px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .kcode-float-msg.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .kcode-float-msg.assistant {
            align-self: flex-start;
        }

        .kcode-float-bubble {
            padding: 0.75rem 1rem;
            border-radius: 1.15rem;
            font-size: 0.88rem;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .kcode-float-msg.assistant .kcode-float-bubble {
            background: #ffffff;
            color: #1f2937;
            border-top-left-radius: 0.2rem;
            border: 1px solid rgba(194, 89, 117, 0.12);
            box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        }

        html.dark .kcode-float-msg.assistant .kcode-float-bubble {
            background: #282832;
            color: #f3f4f6;
            border-color: rgba(229, 162, 181, 0.15);
        }

        .kcode-float-msg.user .kcode-float-bubble {
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            color: #ffffff;
            border-top-right-radius: 0.2rem;
            box-shadow: 0 4px 14px rgba(194, 89, 117, 0.35);
        }

        .kcode-float-input-area {
            padding: 0.85rem 1rem;
            background: rgba(255, 255, 255, 0.95);
            border-top: 1px solid rgba(194, 89, 117, 0.12);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        html.dark .kcode-float-input-area {
            background: rgba(18, 18, 22, 0.95);
            border-top-color: rgba(229, 162, 181, 0.12);
        }

        .kcode-float-input-wrapper {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .kcode-float-input {
            flex: 1;
            padding: 0.65rem 1rem;
            border-radius: 9999px;
            border: 1px solid rgba(194, 89, 117, 0.25);
            background: #ffffff;
            color: #1f2937;
            font-size: 0.88rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .kcode-float-input:focus {
            border-color: #c25975;
            box-shadow: 0 0 0 3px rgba(194, 89, 117, 0.2);
        }

        html.dark .kcode-float-input {
            background: #1a1a20;
            border-color: rgba(229, 162, 181, 0.25);
            color: #f3f4f6;
        }

        .kcode-float-send-btn {
            padding: 0.65rem 1.15rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(194, 89, 117, 0.35);
            transition: all 0.2s ease;
        }

        .kcode-float-send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(194, 89, 117, 0.45);
        }
    </style>

    <!-- PURE CSS TOOLTIP CARD -->
    <div class="kcode-float-tooltip" :class="{ 'is-visible': showTooltip && !isOpen }">
        <button @click.stop="showTooltip = false" type="button" class="absolute top-1.5 left-2 text-gray-400 hover:text-gray-600 dark:hover:text-white text-xs p-1">
            ✕
        </button>
        <div class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-1">
            مستشار KCODE الذكي ✨
        </div>
        <div class="text-xs text-rose-600 dark:text-rose-400 font-medium mt-1">
            نحن هنا من أجلك لمساعدتك 💖
        </div>
    </div>

    <!-- PURE CSS BUTTON TRIGGER WITH ANIMATED ICON -->
    <div class="kcode-float-btn-wrapper">
        <button @click="isOpen = !isOpen; showTooltip = false" type="button" class="kcode-float-btn" title="مستشار KCODE الذكي">
            <div class="kcode-float-icon" :class="{ 'is-active': isOpen }">
                <span x-show="isOpen">✕</span>
                <span x-show="!isOpen">🤖</span>
            </div>
        </button>
    </div>

    <!-- PURE CSS SPRING EXPANSION CHAT POPUP WINDOW -->
    <div class="kcode-float-modal" :class="{ 'is-open': isOpen }">

        <!-- Header -->
        <div class="kcode-float-header">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-rose-500 to-rose-700 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    🤖
                </div>
                <div>
                    <div class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-1.5">
                        مستشار KCODE الذكي
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-medium border border-emerald-500/20">
                            ⚡ سريع الجيل
                        </span>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                        أي سؤال، دعم، أو موضوع عام
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                <button wire:click="clearChat" type="button" class="text-xs p-1 text-gray-400 hover:text-rose-500 transition-colors" title="مسح المحادثة">
                    🗑️
                </button>
                <button @click="isOpen = false" type="button" class="text-sm p-1 text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors" title="إغلاق">
                    ✕
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="kcode-float-messages" id="floatChatMessages">
            @foreach ($messages as $msg)
                <div class="kcode-float-msg {{ $msg['role'] }}">
                    <div class="kcode-float-bubble">
                        {!! nl2br(e($msg['content'])) !!}

                        @if (!empty($msg['products']))
                            <div class="mt-2 text-xs font-bold text-rose-500">
                                🛍️ المنتجات المقترحة:
                            </div>
                            <div class="flex flex-col gap-1.5 mt-1">
                                @foreach ($msg['products'] as $prod)
                                    <div class="flex items-center gap-2 p-1.5 rounded-lg bg-rose-500/5 border border-rose-500/10">
                                        @if (!empty($prod['image_path']))
                                            <img src="{{ $prod['image_path'] }}" alt="" class="w-8 h-8 object-cover rounded">
                                        @endif
                                        <div class="text-[11px] overflow-hidden">
                                            <div class="font-bold truncate text-gray-800 dark:text-gray-200">
                                                {{ $prod['name_ar'] ?? $prod['name_en'] ?? ($prod['name'] ?? '') }}
                                            </div>
                                            <div class="text-rose-500 font-medium">
                                                {{ $prod['price'] ?? 0 }} EGP
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            @if ($isThinking)
                <div class="kcode-float-msg assistant">
                    <div class="kcode-float-bubble text-xs italic text-rose-500 font-medium animate-pulse flex items-center gap-1.5">
                        <span class="inline-block animate-spin">⚡</span> جاري التفكير والرد الآن...
                    </div>
                </div>
            @endif
        </div>

        <!-- Input & Presets -->
        <div class="kcode-float-input-area">
            <div class="flex gap-1 overflow-x-auto pb-1 text-[11px]">
                <button type="button" wire:click="sendPreset('اقترح لي روتين للبشرة الدهنية')" class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 whitespace-nowrap hover:bg-rose-500 hover:text-white transition-all">
                    ✨ روتين للبشرة
                </button>
                <button type="button" wire:click="sendPreset('كيف تزيد مبيعات متجر KCODE؟')" class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 whitespace-nowrap hover:bg-rose-500 hover:text-white transition-all">
                    🚀 زيادة المبيعات
                </button>
                <button type="button" wire:click="sendPreset('اكتب لي كود لارافيل مفيد')" class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 whitespace-nowrap hover:bg-rose-500 hover:text-white transition-all">
                    💻 كود Laravel
                </button>
            </div>

            <form wire:submit.prevent="sendMessage" class="kcode-float-input-wrapper">
                <input
                    type="text"
                    wire:model="userMessage"
                    placeholder="اسأل المستشار الذكي عن أي شيء..."
                    class="kcode-float-input"
                />
                <button type="submit" wire:loading.attr="disabled" class="kcode-float-send-btn">
                    <span wire:loading.remove wire:target="sendMessage">إرسال 🚀</span>
                    <span wire:loading wire:target="sendMessage">⚡...</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const scrollFloat = () => {
                const c = document.getElementById('floatChatMessages');
                if (c) c.scrollTop = c.scrollHeight;
            };
            scrollFloat();
            Livewire.hook('morph.updated', () => {
                scrollFloat();
            });
        });
    </script>
</div>
