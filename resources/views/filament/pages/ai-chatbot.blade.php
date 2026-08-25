<x-filament-panels::page>
    <style>
        .kcode-chat-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 170px);
            min-height: 520px;
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid rgba(194, 89, 117, 0.2);
            box-shadow: 0 10px 30px -5px rgba(194, 89, 117, 0.15);
            overflow: hidden;
        }

        html.dark .kcode-chat-container {
            background: #18181b;
            border-color: rgba(229, 162, 181, 0.2);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.5);
        }

        .kcode-chat-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, rgba(194, 89, 117, 0.1) 0%, rgba(229, 162, 181, 0.03) 100%);
            border-bottom: 1px solid rgba(194, 89, 117, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        html.dark .kcode-chat-header {
            background: linear-gradient(135deg, rgba(194, 89, 117, 0.2) 0%, rgba(24, 24, 27, 0.4) 100%);
            border-bottom-color: rgba(229, 162, 181, 0.15);
        }

        .kcode-chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .kcode-message {
            display: flex;
            gap: 0.85rem;
            max-width: 82%;
            animation: kcodeMsgPop 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .kcode-message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .kcode-message.assistant {
            align-self: flex-start;
        }

        .kcode-drop-icon-page {
            width: 18px !important;
            height: 18px !important;
            min-width: 18px !important;
            min-height: 18px !important;
            max-width: 18px !important;
            max-height: 18px !important;
            display: inline-block !important;
            fill: #ffffff !important;
        }

        .kcode-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .kcode-avatar.ai {
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            color: #ffffff;
        }

        .kcode-avatar.user {
            background: #4b5563;
            color: #ffffff;
        }

        .kcode-bubble {
            padding: 1rem 1.25rem;
            border-radius: 1.25rem;
            font-size: 0.95rem;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .kcode-message.assistant .kcode-bubble {
            background: #f9fafb;
            color: #111827;
            border-top-left-radius: 0.25rem;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        html.dark .kcode-message.assistant .kcode-bubble {
            background: #27272a;
            color: #f3f4f6;
            border-color: rgba(255, 255, 255, 0.08);
        }

        .kcode-message.user .kcode-bubble {
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            color: #ffffff;
            border-top-right-radius: 0.25rem;
            box-shadow: 0 4px 14px rgba(194, 89, 117, 0.3);
        }

        .kcode-chat-input-area {
            padding: 1.25rem 1.5rem;
            background: rgba(249, 250, 251, 0.8);
            border-top: 1px solid rgba(194, 89, 117, 0.15);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        html.dark .kcode-chat-input-area {
            background: rgba(24, 24, 27, 0.8);
            border-top-color: rgba(229, 162, 181, 0.15);
        }

        .kcode-input-wrapper {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .kcode-input {
            flex: 1;
            padding: 0.85rem 1.25rem;
            border-radius: 9999px;
            border: 1.5px solid rgba(194, 89, 117, 0.25);
            background: #ffffff;
            color: #111827;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .kcode-input:focus {
            border-color: #c25975;
            box-shadow: 0 0 0 3px rgba(194, 89, 117, 0.2);
        }

        html.dark .kcode-input {
            background: #27272a;
            border-color: rgba(229, 162, 181, 0.25);
            color: #f3f4f6;
        }

        .kcode-send-btn {
            padding: 0.85rem 1.75rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(194, 89, 117, 0.35);
            transition: all 0.2s ease;
        }

        .kcode-send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(194, 89, 117, 0.45);
        }

        .kcode-presets {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 0.25rem;
        }

        .kcode-chip {
            padding: 0.4rem 0.85rem;
            border-radius: 9999px;
            background: rgba(194, 89, 117, 0.1);
            color: #c25975;
            border: 1px solid rgba(194, 89, 117, 0.2);
            font-size: 0.8rem;
            font-weight: 500;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .kcode-chip:hover {
            background: #c25975;
            color: #ffffff;
            transform: translateY(-1px);
        }

        html.dark .kcode-chip {
            color: #e5a2b5;
        }
    </style>

    <div class="kcode-chat-container">
        <!-- Header -->
        <div class="kcode-chat-header">
            <div class="flex items-center gap-3">
                <div class="kcode-avatar ai">
                    <svg class="kcode-drop-icon-page" viewBox="0 0 24 24">
                        <path d="M12 2.69C12 2.69 6 9.5 6 14C6 17.31 8.69 20 12 20C15.31 20 18 17.31 18 14C18 9.5 12 2.69 12 2.69Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                        {{ app()->getLocale() === 'en' ? 'KCODE Skincare AI Assistant' : 'مستشار KCODE الذكي للمتجر' }}
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-semibold border border-emerald-500/20">
                            {{ app()->getLocale() === 'en' ? 'Online' : 'متصل الآن' }}
                        </span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ app()->getLocale() === 'en' ? 'Ask anything about skincare, store metrics, software or general info.' : 'اسأل عن أي شيء خاص بالبشرة، إحصائيات الداشبورد، أو أي استفسار عام.' }}
                    </p>
                </div>
            </div>

            <button wire:click="clearChat" type="button" class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-gray-300 hover:bg-rose-500 hover:text-white transition-colors">
                🗑️ {{ app()->getLocale() === 'en' ? 'Clear Chat' : 'مسح المحادثة' }}
            </button>
        </div>

        <!-- Messages Feed -->
        <div class="kcode-chat-messages" id="chatMessages">
            @foreach ($messages as $index => $msg)
                <div class="kcode-message {{ $msg['role'] }}" wire:key="pmsg-{{ $index }}">
                    <div class="kcode-avatar {{ $msg['role'] === 'user' ? 'user' : 'ai' }}">
                        @if ($msg['role'] === 'user')
                            👤
                        @else
                            <svg class="kcode-drop-icon-page" viewBox="0 0 24 24">
                                <path d="M12 2.69C12 2.69 6 9.5 6 14C6 17.31 8.69 20 12 20C15.31 20 18 17.31 18 14C18 9.5 12 2.69 12 2.69Z" />
                            </svg>
                        @endif
                    </div>
                    <div class="kcode-bubble">
                        {!! nl2br(e($msg['content'])) !!}

                        @if (!empty($msg['products']))
                            <div class="mt-3 font-bold text-sm text-rose-500">
                                🛍️ {{ app()->getLocale() === 'ar' ? 'المنتجات المقترحة:' : 'Recommended Products:' }}
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                                @foreach ($msg['products'] as $prod)
                                    <div class="flex items-center gap-3 p-2 rounded-xl bg-white dark:bg-zinc-800 border border-gray-100 dark:border-zinc-700 shadow-sm">
                                        @if (!empty($prod['image_path']))
                                            <img src="{{ $prod['image_path'] }}" alt="" class="w-12 h-12 object-cover rounded-lg">
                                        @endif
                                        <div>
                                            <div class="font-bold text-xs text-gray-900 dark:text-white">
                                                {{ $prod['name_ar'] ?? $prod['name_en'] ?? ($prod['name'] ?? '') }}
                                            </div>
                                            <div class="text-xs text-rose-500 font-bold mt-0.5">
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

            <div wire:loading wire:target="fetchAiResponse" class="kcode-message assistant">
                <div class="kcode-avatar ai">
                    <svg class="kcode-drop-icon-page" viewBox="0 0 24 24">
                        <path d="M12 2.69C12 2.69 6 9.5 6 14C6 17.31 8.69 20 12 20C15.31 20 18 17.31 18 14C18 9.5 12 2.69 12 2.69Z" />
                    </svg>
                </div>
                <div class="kcode-bubble animate-pulse">
                    ⏳ {{ app()->getLocale() === 'ar' ? 'جاري التفكير والتوليد...' : 'AI is thinking...' }}
                </div>
            </div>
        </div>

        <!-- Input Area (Without Presets) -->
        <div class="kcode-chat-input-area">
            <form wire:submit="sendMessage" class="kcode-input-wrapper">
                <input
                    type="text"
                    wire:model="userMessage"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب أي سؤال أو استفسار هنا (غير مقيد بالموضوع)...' : 'Type any question or prompt here (unrestricted)...' }}"
                    class="kcode-input"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage, fetchAiResponse"
                    autofocus
                />
                <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage, fetchAiResponse" class="kcode-send-btn">
                    <span wire:loading.remove wire:target="sendMessage, fetchAiResponse">إرسال 🚀</span>
                    <span wire:loading wire:target="sendMessage, fetchAiResponse">جاري الإرسال...</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const autoScrollPage = () => {
                const el = document.getElementById('chatMessages');
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            };

            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    setTimeout(autoScrollPage, 50);
                });
            });
        });
    </script>
</x-filament-panels::page>
