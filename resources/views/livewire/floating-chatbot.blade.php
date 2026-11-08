<div x-data="{ isOpen: false, showTooltip: true }" class="{{ app()->getLocale() === 'ar' ? 'is-rtl' : 'is-ltr' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <style>
        @keyframes bounceTooltip {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @keyframes slideUpChat {
            from { opacity: 0; transform: translateY(16px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .kcode-chatbot-root {
            position: fixed;
            bottom: 1.5rem;
            left: 1.5rem;
            right: auto;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            font-family: "29LT Zarid Sans AL", "29LT Zarid Sans", system-ui, sans-serif;
        }

        .is-ltr .kcode-chatbot-root {
            left: auto;
            right: 1.5rem;
            align-items: flex-end;
        }

        /* TOOLTIP BUBBLE (MATCHING VERCEL EXACTLY) */
        .kcode-chatbot-tooltip {
            background: var(--kcode-surface, #FBFAF7);
            border: 1px solid var(--kcode-border, #DCD5D2);
            border-radius: 8px;
            padding: 0.85rem 1.15rem;
            margin-bottom: 0.85rem;
            position: relative;
            box-shadow: 0 8px 30px rgba(46, 22, 51, 0.12);
            cursor: pointer;
            animation: bounceTooltip 3s ease-in-out infinite;
            text-align: right;
            max-width: 300px;
            box-sizing: border-box;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .is-ltr .kcode-chatbot-tooltip {
            text-align: left;
        }

        html.dark .kcode-chatbot-tooltip {
            background: var(--kcode-surface, #231826);
            border-color: var(--kcode-border, #3D2A43);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.6);
        }

        .kcode-chatbot-tooltip-close {
            position: absolute;
            top: 0.5rem;
            left: 0.65rem;
            right: auto;
            background: none;
            border: none;
            color: var(--kcode-text-secondary, #7A7077);
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: color 0.2s;
            font-size: 13px;
            line-height: 1;
        }

        .is-ltr .kcode-chatbot-tooltip-close {
            left: auto;
            right: 0.65rem;
        }

        .kcode-chatbot-tooltip-close:hover {
            color: var(--kcode-berry, #93254E);
        }

        .kcode-chatbot-tooltip-title {
            margin: 0;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--kcode-text-primary, #2E1633);
        }

        html.dark .kcode-chatbot-tooltip-title {
            color: var(--kcode-text-primary, #F5F0F3);
        }

        .kcode-chatbot-tooltip-sub {
            margin: 0.2rem 0 0 0;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--kcode-berry, #93254E);
        }

        html.dark .kcode-chatbot-tooltip-sub {
            color: var(--kcode-berry-light, #E28AAB);
        }

        .kcode-chatbot-tooltip-arrow {
            position: absolute;
            bottom: -6px;
            left: 20px;
            right: auto;
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid var(--kcode-surface, #FBFAF7);
        }

        .is-ltr .kcode-chatbot-tooltip-arrow {
            left: auto;
            right: 20px;
        }

        html.dark .kcode-chatbot-tooltip-arrow {
            border-top-color: var(--kcode-surface, #231826);
        }

        /* TRIGGER BUTTON (MATCHING VERCEL EXACTLY) */
        .kcode-chatbot-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--kcode-surface, #FBFAF7);
            border: 1.5px solid var(--kcode-berry, #93254E);
            color: var(--kcode-berry, #93254E);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(46, 22, 51, 0.16);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
        }

        .kcode-chatbot-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 12px 28px rgba(46, 22, 51, 0.22);
            border-color: var(--kcode-secondary, #6B2F5F);
        }

        html.dark .kcode-chatbot-btn {
            background: var(--kcode-surface, #231826);
            border-color: var(--kcode-berry-light, #E28AAB);
            color: var(--kcode-berry-light, #E28AAB);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
        }

        /* CHAT WINDOW (MATCHING VERCEL EXACTLY) */
        .kcode-chatbot-window {
            width: 390px;
            height: 560px;
            max-width: calc(100vw - 2rem);
            max-height: calc(100vh - 3.5rem);
            background: var(--kcode-canvas, #F5F2ED);
            border: 1px solid var(--kcode-border, #DCD5D2);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 50px rgba(46, 22, 51, 0.22);
            animation: slideUpChat 0.25s ease;
            box-sizing: border-box;
        }

        html.dark .kcode-chatbot-window {
            background: var(--kcode-canvas, #170F1A);
            border-color: var(--kcode-border, #3D2A43);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);
        }

        /* HEADER */
        .kcode-chatbot-header {
            padding: 0.9rem 1.25rem;
            background: var(--kcode-primary, #2E1633);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #FFFFFF;
        }

        .kcode-chatbot-avatar-wrp {
            position: relative;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: 1.5px solid var(--kcode-berry-light, #E28AAB);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--kcode-berry-light, #E28AAB);
            flex-shrink: 0;
        }

        .kcode-chatbot-online-dot {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #22C55E;
            border: 1.5px solid var(--kcode-primary, #2E1633);
        }

        .kcode-chatbot-header-title {
            margin: 0;
            font-size: 0.92rem;
            font-weight: 700;
            color: #FFFFFF;
        }

        .kcode-chatbot-header-sub {
            font-size: 0.72rem;
            color: var(--kcode-berry-light, #E28AAB);
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.1rem;
        }

        .kcode-chatbot-header-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.75);
            cursor: pointer;
            padding: 0.35rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .kcode-chatbot-header-btn:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.12);
        }

        /* QUICK NAV BAR */
        .kcode-chatbot-quick-nav {
            padding: 0.5rem 0.75rem;
            background: var(--kcode-surface, #FBFAF7);
            border-bottom: 1px solid var(--kcode-border, #DCD5D2);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            overflow-x: auto;
            white-space: nowrap;
            flex-shrink: 0;
            scrollbar-width: none;
        }

        .kcode-chatbot-quick-nav::-webkit-scrollbar {
            display: none;
        }

        html.dark .kcode-chatbot-quick-nav {
            background: var(--kcode-surface, #231826);
            border-bottom-color: var(--kcode-border, #3D2A43);
        }

        .kcode-chatbot-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            background: var(--kcode-surface-warm, #EDE8E4);
            border: 1px solid var(--kcode-border, #DCD5D2);
            color: var(--kcode-text-primary, #2E1633);
            font-size: 0.74rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .kcode-chatbot-pill:hover {
            background: var(--kcode-berry, #93254E);
            color: #FFFFFF;
            border-color: var(--kcode-berry, #93254E);
        }

        html.dark .kcode-chatbot-pill {
            background: var(--kcode-surface-warm, #2D1F31);
            border-color: var(--kcode-border, #3D2A43);
            color: var(--kcode-text-primary, #F5F0F3);
        }

        html.dark .kcode-chatbot-pill:hover {
            background: var(--kcode-berry-light, #E28AAB);
            color: #170F1A;
            border-color: var(--kcode-berry-light, #E28AAB);
        }

        /* MESSAGES AREA */
        .kcode-chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .kcode-chatbot-msg {
            display: flex;
            flex-direction: column;
            max-width: 86%;
            animation: slideUpChat 0.25s ease forwards;
        }

        .kcode-chatbot-msg.assistant {
            align-self: flex-start;
        }

        .kcode-chatbot-msg.user {
            align-self: flex-end;
        }

        .kcode-chatbot-bubble {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.875rem;
            line-height: 1.55;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .kcode-chatbot-msg.assistant .kcode-chatbot-bubble {
            background: var(--kcode-surface, #FBFAF7);
            color: var(--kcode-text-primary, #2E1633);
            border: 1px solid var(--kcode-border, #DCD5D2);
            box-shadow: 0 2px 8px rgba(46, 22, 51, 0.04);
        }

        html.dark .kcode-chatbot-msg.assistant .kcode-chatbot-bubble {
            background: var(--kcode-surface, #231826);
            color: var(--kcode-text-primary, #F5F0F3);
            border-color: var(--kcode-border, #3D2A43);
        }

        .kcode-chatbot-msg.user .kcode-chatbot-bubble {
            background: var(--kcode-primary, #2E1633);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(46, 22, 51, 0.2);
        }

        html.dark .kcode-chatbot-msg.user .kcode-chatbot-bubble {
            background: var(--kcode-primary, #5A2147);
        }

        /* PRODUCT CARDS IN CHAT */
        .kcode-chat-prod-card {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.5rem;
            border-radius: 8px;
            background: var(--kcode-surface-warm, #EDE8E4);
            border: 1px solid var(--kcode-border, #DCD5D2);
            margin-top: 0.4rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }

        .kcode-chat-prod-card:hover {
            border-color: var(--kcode-berry, #93254E);
            box-shadow: 0 2px 8px rgba(147, 37, 78, 0.15);
        }

        html.dark .kcode-chat-prod-card {
            background: var(--kcode-surface-warm, #2D1F31);
            border-color: var(--kcode-border, #3D2A43);
        }

        /* INPUT AREA */
        .kcode-chatbot-input-area {
            padding: 0.85rem 1rem;
            background: var(--kcode-surface, #FBFAF7);
            border-top: 1px solid var(--kcode-border, #DCD5D2);
        }

        html.dark .kcode-chatbot-input-area {
            background: var(--kcode-surface, #231826);
            border-top-color: var(--kcode-border, #3D2A43);
        }

        .kcode-chatbot-form {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .kcode-chatbot-input {
            flex: 1;
            padding: 0.65rem 0.95rem;
            border-radius: 8px;
            border: 1px solid var(--kcode-border, #DCD5D2);
            background: var(--kcode-canvas, #F5F2ED);
            font-size: 0.85rem;
            color: var(--kcode-text-primary, #2E1633);
            outline: none;
            transition: all 0.2s;
            font-family: inherit;
        }

        .kcode-chatbot-input:focus {
            border-color: var(--kcode-berry, #93254E);
            box-shadow: 0 0 0 3px rgba(147, 37, 78, 0.15);
        }

        html.dark .kcode-chatbot-input {
            background: var(--kcode-surface-warm, #2D1F31);
            border-color: var(--kcode-border, #3D2A43);
            color: var(--kcode-text-primary, #F5F0F3);
        }

        .kcode-chatbot-send-btn {
            padding: 0.65rem 1.15rem;
            border-radius: 8px;
            background: var(--kcode-berry, #93254E);
            color: #FFFFFF;
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(147, 37, 78, 0.25);
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-family: inherit;
        }

        .kcode-chatbot-send-btn:hover {
            background: var(--kcode-primary, #2E1633);
            transform: translateY(-1px);
        }

        html.dark .kcode-chatbot-send-btn {
            background: var(--kcode-berry, #B32C5E);
        }
    </style>

    <div class="kcode-chatbot-root">
        <!-- TOOLTIP BUBBLE (SHOWN ON PAGE LOAD) -->
        <div
            x-show="showTooltip && !isOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="kcode-chatbot-tooltip"
            @click="isOpen = true; showTooltip = false"
        >
            <button @click.stop="showTooltip = false" type="button" class="kcode-chatbot-tooltip-close" title="إغلاق">
                ✕
            </button>
            <div style="padding-left: 1.25rem;">
                <p class="kcode-chatbot-tooltip-title">مستشار KCODE الذكي</p>
                <p class="kcode-chatbot-tooltip-sub">نحن هنا من أجلك لمساعدتك</p>
            </div>
            <div class="kcode-chatbot-tooltip-arrow"></div>
        </div>

        <!-- TRIGGER BUTTON (ELEGANT CIRCLE WITH TEARDROP ICON) -->
        <button
            x-show="!isOpen"
            @click="isOpen = true; showTooltip = false"
            type="button"
            class="kcode-chatbot-btn"
            title="مستشار KCODE الذكي"
            aria-label="مستشار KCODE الذكي"
        >
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.69C12 2.69 6 9.5 6 14C6 17.31 8.69 20 12 20C15.31 20 18 17.31 18 14C18 9.5 12 2.69 12 2.69Z" />
            </svg>
        </button>

        <!-- CHAT WINDOW (MODAL DRAWER) -->
        <div
            x-show="isOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 transform translate-y-4 scale-95"
            class="kcode-chatbot-window"
        >
            <!-- Header -->
            <div class="kcode-chatbot-header">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div class="kcode-chatbot-avatar-wrp">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.69C12 2.69 6 9.5 6 14C6 17.31 8.69 20 12 20C15.31 20 18 17.31 18 14C18 9.5 12 2.69 12 2.69Z" />
                        </svg>
                        <div class="kcode-chatbot-online-dot"></div>
                    </div>
                    <div>
                        <h4 class="kcode-chatbot-header-title">مستشار KCODE الذكي</h4>
                        <span class="kcode-chatbot-header-sub">🟢 متصل ومستعد للمساعدة</span>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.35rem;">
                    <button wire:click="clearChat" type="button" class="kcode-chatbot-header-btn" title="مسح المحادثة">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/>
                        </svg>
                    </button>
                    <button @click="isOpen = false" type="button" class="kcode-chatbot-header-btn" title="إغلاق">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Quick Navigation Pills (Matching Vercel) -->
            <div class="kcode-chatbot-quick-nav">
                <button type="button" wire:click="sendMessage('ما هي أفضل المنتجات المتوفرة للبشرة؟')" class="kcode-chatbot-pill">
                    🛍️ المنتجات
                </button>
                <button type="button" wire:click="sendMessage('كيف أبدأ ببناء روتين كوري مناسب لبشرتي؟')" class="kcode-chatbot-pill">
                    🧴 بناء الروتين
                </button>
                <button type="button" wire:click="sendMessage('ما هي خطوات تحليل نوع ومشاكل البشرة؟')" class="kcode-chatbot-pill">
                    ✨ تحليل البشرة
                </button>
                <button type="button" wire:click="sendMessage('أعطني أهم نصائح العناية اليومية بالبشرة')" class="kcode-chatbot-pill">
                    💡 نصائح العناية
                </button>
            </div>

            <!-- Messages Stream Area -->
            <div class="kcode-chatbot-messages" id="floatChatMessages">
                @foreach ($messages as $index => $msg)
                    <div class="kcode-chatbot-msg {{ $msg['role'] }}" wire:key="msg-{{ $index }}">
                        <div class="kcode-chatbot-bubble">
                            {!! nl2br(e($msg['content'])) !!}

                            @if (!empty($msg['products']))
                                <div style="margin-top: 0.6rem; font-weight: 700; font-size: 0.78rem; color: var(--kcode-berry, #93254E);">
                                    🛍️ المنتجات المقترحة:
                                </div>
                                @foreach ($msg['products'] as $prod)
                                    <div class="kcode-chat-prod-card">
                                        @if (!empty($prod['image_path']))
                                            <img src="{{ $prod['image_path'] }}" alt="" style="width: 34px; height: 34px; object-fit: cover; border-radius: 6px;">
                                        @endif
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-weight: 600; font-size: 0.8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $prod['name_ar'] ?? $prod['name_en'] ?? ($prod['name'] ?? '') }}
                                            </div>
                                            <div style="font-weight: 700; font-size: 0.75rem; color: var(--kcode-berry, #93254E);">
                                                {{ $prod['price'] ?? 0 }} ر.ع
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Typing Indicator -->
                <div wire:loading wire:target="fetchAiResponse" class="kcode-chatbot-msg assistant">
                    <div class="kcode-chatbot-bubble" style="font-size: 0.8rem; font-style: italic; color: var(--kcode-berry, #93254E); display: flex; align-items: center; gap: 0.4rem;">
                        <span style="display: inline-block; animation: spin 1s linear infinite;">⏳</span> المستشار الذكي يكتب الآن...
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="kcode-chatbot-input-area">
                <form wire:submit="sendMessage" class="kcode-chatbot-form">
                    <input
                        type="text"
                        wire:model="userMessage"
                        placeholder="كتابة السؤال أو الاستفسار هنا..."
                        class="kcode-chatbot-input"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage, fetchAiResponse"
                    />
                    <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage, fetchAiResponse" class="kcode-chatbot-send-btn">
                        <span wire:loading.remove wire:target="sendMessage, fetchAiResponse">إرسال</span>
                        <span wire:loading wire:target="sendMessage, fetchAiResponse">...</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transform: rotate(-45deg);">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
            </div>
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
