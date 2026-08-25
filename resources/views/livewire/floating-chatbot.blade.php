<div x-data="{ isOpen: false, showTooltip: true }" class="{{ app()->getLocale() === 'ar' ? 'is-rtl' : 'is-ltr' }}">
    <style>
        @keyframes kcodePulseGlow {
            0%, 100% {
                box-shadow: 0 8px 25px -4px rgba(194, 89, 117, 0.45), 0 0 18px rgba(244, 114, 182, 0.35);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 12px 32px -2px rgba(194, 89, 117, 0.7), 0 0 28px rgba(244, 114, 182, 0.55);
                transform: scale(1.04);
            }
        }

        @keyframes kcodeFloatWave {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .kcode-drop-icon {
            width: 22px !important;
            height: 22px !important;
            min-width: 22px !important;
            min-height: 22px !important;
            max-width: 22px !important;
            max-height: 22px !important;
            display: inline-block !important;
            fill: #ffffff !important;
        }

        .kcode-drop-icon-sm {
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
            fill: #ffffff !important;
        }

        .kcode-float-btn-wrapper {
            position: fixed;
            bottom: 28px;
            left: 28px;
            right: auto;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .is-ltr .kcode-float-btn-wrapper {
            right: 28px;
            left: auto;
        }

        .kcode-float-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #fce7f3;
            border: 2.5px solid rgba(194, 89, 117, 0.35);
            animation: kcodePulseGlow 3s infinite ease-in-out;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }

        .kcode-float-btn:hover {
            transform: scale(1.12) rotate(-6deg);
        }

        .kcode-float-icon-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.35), 0 4px 12px rgba(194, 89, 117, 0.4);
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .kcode-float-icon-inner.is-active {
            transform: rotate(180deg) scale(1.1);
        }

        /* TOOLTIP BUBBLE CARD */
        .kcode-float-tooltip {
            position: fixed;
            bottom: 30px;
            left: 100px;
            right: auto;
            z-index: 9998;
            background: #ffffff;
            border: 1px solid rgba(194, 89, 117, 0.25);
            border-radius: 1.25rem;
            padding: 0.75rem 1.15rem 0.75rem 1.75rem;
            box-shadow: 0 12px 30px -5px rgba(194, 89, 117, 0.22);
            min-width: 200px;
            max-width: 260px;
            animation: kcodeFloatWave 3.5s infinite ease-in-out;

            opacity: 0;
            visibility: hidden;
            transform: translateX(-10px) scale(0.9);
            pointer-events: none;
            transition: opacity 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        visibility 0.3s step-end;
        }

        .is-ltr .kcode-float-tooltip {
            right: 100px;
            left: auto;
            padding: 0.75rem 1.75rem 0.75rem 1.15rem;
            transform: translateX(10px) scale(0.9);
        }

        .kcode-float-tooltip.is-visible {
            opacity: 1;
            visibility: visible;
            transform: translateX(0) scale(1);
            pointer-events: auto;
            transition: opacity 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
                        visibility 0s step-start;
        }

        html.dark .kcode-float-tooltip {
            background: #1c1c22;
            border-color: rgba(229, 162, 181, 0.25);
            box-shadow: 0 12px 30px -5px rgba(0, 0, 0, 0.6);
        }

        .kcode-float-tooltip-close {
            position: absolute;
            top: 6px;
            left: 8px;
            right: auto;
            background: none;
            border: none;
            color: #9ca3af;
            font-size: 11px;
            cursor: pointer;
            padding: 2px 4px;
            line-height: 1;
            transition: color 0.2s;
        }

        .is-ltr .kcode-float-tooltip-close {
            right: 8px;
            left: auto;
        }

        .kcode-float-tooltip-close:hover {
            color: #c25975;
        }

        /* CHAT WINDOW POPUP DRAWER */
        .kcode-float-modal {
            position: fixed;
            bottom: 98px;
            left: 28px;
            right: auto;
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
            transform-origin: bottom left;

            opacity: 0;
            visibility: hidden;
            transform: translateY(28px) scale(0.7) rotate(-3deg);
            pointer-events: none;
            transition: opacity 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
                        transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
                        visibility 0.35s step-end;
        }

        .is-ltr .kcode-float-modal {
            right: 28px;
            left: auto;
            transform-origin: bottom right;
            transform: translateY(28px) scale(0.7) rotate(3deg);
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
            font-size: 0.875rem;
            line-height: 1.55;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .kcode-float-msg.assistant .kcode-float-bubble {
            background: #ffffff;
            color: #1f2937;
            border-top-right-radius: 0.2rem;
            border: 1px solid rgba(194, 89, 117, 0.12);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        html.dark .kcode-float-msg.assistant .kcode-float-bubble {
            background: #272730;
            color: #f3f4f6;
            border-color: rgba(229, 162, 181, 0.12);
        }

        .kcode-float-msg.user .kcode-float-bubble {
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            color: #ffffff;
            border-top-left-radius: 0.2rem;
            box-shadow: 0 4px 12px rgba(194, 89, 117, 0.3);
        }

        .kcode-float-input-area {
            padding: 0.85rem 1rem;
            background: rgba(249, 250, 251, 0.85);
            border-top: 1px solid rgba(194, 89, 117, 0.15);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        html.dark .kcode-float-input-area {
            background: rgba(24, 24, 30, 0.85);
            border-top-color: rgba(229, 162, 181, 0.15);
        }

        .kcode-float-input-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .kcode-float-input {
            flex: 1;
            padding: 0.65rem 1rem;
            border-radius: 9999px;
            border: 1px solid rgba(194, 89, 117, 0.25);
            background: #ffffff;
            font-size: 0.85rem;
            color: #111827;
            outline: none;
            transition: all 0.2s ease;
        }

        html.dark .kcode-float-input {
            background: #18181b;
            border-color: rgba(229, 162, 181, 0.25);
            color: #f3f4f6;
        }

        .kcode-float-input:focus {
            border-color: #c25975;
            box-shadow: 0 0 0 3px rgba(194, 89, 117, 0.2);
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

    <!-- TOOLTIP BUBBLE CARD -->
    <div class="kcode-float-tooltip" :class="{ 'is-visible': showTooltip && !isOpen }">
        <button @click.stop="showTooltip = false" type="button" class="kcode-float-tooltip-close" title="إغلاق">
            ✕
        </button>
        <div class="font-bold text-xs sm:text-sm text-gray-900 dark:text-white flex items-center gap-1">
            مستشار KCODE الذكي ✨
        </div>
        <div class="text-[11px] sm:text-xs text-rose-500 dark:text-rose-400 font-medium mt-0.5">
            نحن هنا من أجلك لمساعدتك 💖
        </div>
    </div>

    <!-- BUTTON TRIGGER WITH ANIMATED KCODE DROPLET ICON -->
    <div class="kcode-float-btn-wrapper">
        <button @click="isOpen = !isOpen; showTooltip = false" type="button" class="kcode-float-btn" title="مستشار KCODE الذكي">
            <div class="kcode-float-icon-inner" :class="{ 'is-active': isOpen }">
                <template x-if="isOpen">
                    <span class="text-sm font-bold">✕</span>
                </template>
                <template x-if="!isOpen">
                    <svg class="kcode-drop-icon" viewBox="0 0 24 24">
                        <path d="M12 2.69C12 2.69 6 9.5 6 14C6 17.31 8.69 20 12 20C15.31 20 18 17.31 18 14C18 9.5 12 2.69 12 2.69Z" />
                    </svg>
                </template>
            </div>
        </button>
    </div>

    <!-- CHAT WINDOW POPUP DRAWER -->
    <div class="kcode-float-modal" :class="{ 'is-open': isOpen }">

        <!-- Header -->
        <div class="kcode-float-header">
            <div class="flex items-center gap-2.5">
                <div class="rounded-full bg-gradient-to-r from-rose-500 to-rose-700 text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0" style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;">
                    <svg class="kcode-drop-icon-sm" viewBox="0 0 24 24">
                        <path d="M12 2.69C12 2.69 6 9.5 6 14C6 17.31 8.69 20 12 20C15.31 20 18 17.31 18 14C18 9.5 12 2.69 12 2.69Z" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-1.5">
                        مستشار KCODE الذكي
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-medium border border-emerald-500/20">
                            ⚡ متصل الآن
                        </span>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400">
                        أي سؤال عن البشرة، المنتجات، أو الداشبورد
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
            @foreach ($messages as $index => $msg)
                <div class="kcode-float-msg {{ $msg['role'] }}" wire:key="msg-{{ $index }}">
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

            <!-- Immediate Loading Indicator -->
            <div wire:loading wire:target="fetchAiResponse" class="kcode-float-msg assistant">
                <div class="kcode-float-bubble text-xs italic text-rose-500 font-medium animate-pulse flex items-center gap-1.5">
                    <span class="inline-block animate-spin">⚡</span> جاري التفكير والرد الآن...
                </div>
            </div>
        </div>

        <!-- Input Area (Without preset chips) -->
        <div class="kcode-float-input-area">
            <form wire:submit="sendMessage" class="kcode-float-input-wrapper">
                <input
                    type="text"
                    wire:model="userMessage"
                    placeholder="اسأل المستشار الذكي عن أي شيء..."
                    class="kcode-float-input"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage, fetchAiResponse"
                />
                <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage, fetchAiResponse" class="kcode-float-send-btn">
                    <span wire:loading.remove wire:target="sendMessage, fetchAiResponse">إرسال 🚀</span>
                    <span wire:loading wire:target="sendMessage, fetchAiResponse">⚡...</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const autoScrollFloat = () => {
                const el = document.getElementById('floatChatMessages');
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            };

            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    setTimeout(autoScrollFloat, 50);
                });
            });
        });
    </script>
</div>
