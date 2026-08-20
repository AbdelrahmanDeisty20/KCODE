<x-filament-panels::page>
    <style>
        .kcode-chat-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 14rem);
            min-height: 580px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            border-radius: 1.5rem;
            border: 1px solid rgba(194, 89, 117, 0.15);
            box-shadow: 0 12px 35px -8px rgba(194, 89, 117, 0.12);
            overflow: hidden;
        }

        html.dark .kcode-chat-container {
            background: rgba(26, 26, 32, 0.75);
            border-color: rgba(229, 162, 181, 0.15);
            box-shadow: 0 12px 35px -8px rgba(0, 0, 0, 0.5);
        }

        .kcode-chat-header {
            padding: 1.25rem 1.75rem;
            background: linear-gradient(135deg, rgba(194, 89, 117, 0.08) 0%, rgba(229, 162, 181, 0.03) 100%);
            border-bottom: 1px solid rgba(194, 89, 117, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        html.dark .kcode-chat-header {
            background: linear-gradient(135deg, rgba(194, 89, 117, 0.18) 0%, rgba(26, 26, 32, 0.4) 100%);
            border-bottom-color: rgba(229, 162, 181, 0.12);
        }

        .kcode-chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            scroll-behavior: smooth;
        }

        .kcode-message {
            display: flex;
            gap: 0.85rem;
            max-width: 82%;
            animation: fadeInUp 0.35s ease-out forwards;
        }

        .kcode-message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .kcode-message.assistant {
            align-self: flex-start;
        }

        .kcode-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: justify;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(194, 89, 117, 0.25);
        }

        .kcode-avatar.ai {
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            color: #ffffff;
        }

        .kcode-avatar.user-av {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: #ffffff;
        }

        .kcode-bubble {
            padding: 1rem 1.25rem;
            border-radius: 1.25rem;
            font-size: 0.95rem;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
            position: relative;
        }

        .kcode-message.assistant .kcode-bubble {
            background: #ffffff;
            color: #1f2937;
            border-top-left-radius: 0.25rem;
            border: 1px solid rgba(194, 89, 117, 0.12);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        html.dark .kcode-message.assistant .kcode-bubble {
            background: #23232a;
            color: #f3f4f6;
            border-color: rgba(229, 162, 181, 0.15);
        }

        .kcode-message.user .kcode-bubble {
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            color: #ffffff;
            border-top-right-radius: 0.25rem;
            box-shadow: 0 6px 18px rgba(194, 89, 117, 0.3);
        }

        .kcode-time {
            font-size: 0.72rem;
            opacity: 0.7;
            margin-top: 0.4rem;
            text-align: right;
        }

        .kcode-chat-input-area {
            padding: 1.25rem;
            background: rgba(255, 255, 255, 0.9);
            border-top: 1px solid rgba(194, 89, 117, 0.12);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        html.dark .kcode-chat-input-area {
            background: rgba(22, 22, 27, 0.95);
            border-top-color: rgba(229, 162, 181, 0.12);
        }

        .kcode-presets {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 0.25rem;
            scrollbar-width: thin;
        }

        .kcode-chip {
            padding: 0.4rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.82rem;
            background: rgba(194, 89, 117, 0.08);
            color: #c25975;
            border: 1px solid rgba(194, 89, 117, 0.2);
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .kcode-chip:hover {
            background: #c25975;
            color: #ffffff;
            transform: translateY(-2px);
        }

        html.dark .kcode-chip {
            background: rgba(229, 162, 181, 0.12);
            color: #e5a2b5;
            border-color: rgba(229, 162, 181, 0.25);
        }

        html.dark .kcode-chip:hover {
            background: #e5a2b5;
            color: #121215;
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
            border: 1px solid rgba(194, 89, 117, 0.25);
            background: #ffffff;
            color: #1f2937;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .kcode-input:focus {
            border-color: #c25975;
            box-shadow: 0 0 0 3px rgba(194, 89, 117, 0.2);
        }

        html.dark .kcode-input {
            background: #1a1a20;
            border-color: rgba(229, 162, 181, 0.25);
            color: #f3f4f6;
        }

        html.dark .kcode-input:focus {
            border-color: #e5a2b5;
            box-shadow: 0 0 0 3px rgba(229, 162, 181, 0.2);
        }

        .kcode-send-btn {
            padding: 0.85rem 1.5rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #c25975 0%, #aa3f5d 100%);
            color: white;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(194, 89, 117, 0.35);
            transition: all 0.2s ease;
        }

        .kcode-send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(194, 89, 117, 0.45);
        }

        .kcode-send-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .kcode-product-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.85rem;
            border-radius: 0.85rem;
            background: rgba(194, 89, 117, 0.05);
            border: 1px solid rgba(194, 89, 117, 0.15);
            margin-top: 0.6rem;
        }

        html.dark .kcode-product-card {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }
    </style>

    <div class="kcode-chat-container">
        <!-- Header -->
        <div class="kcode-chat-header">
            <div class="flex items-center gap-3">
                <div class="kcode-avatar ai">🤖</div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        {{ app()->getLocale() === 'ar' ? 'المستشار الذكي (KCODE AI Assistant)' : 'KCODE AI Assistant' }}
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-medium">
                            🔓 {{ app()->getLocale() === 'ar' ? 'متاح لكل المواضيع دون قيود' : 'Unrestricted Topics' }}
                        </span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ app()->getLocale() === 'ar' ? 'محتوى مفتوح: يمكنك السؤال عن أي موضوع، دعم فني، متجر، أو استفسار عام' : 'Open domain: Ask about anything, technical support, store products, or general inquiries' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="clearChat" type="button" class="text-xs px-3 py-1.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-rose-500 hover:text-white transition-all duration-200">
                    🗑️ {{ app()->getLocale() === 'ar' ? 'مسح المحادثة' : 'Clear Chat' }}
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="kcode-chat-messages" id="chatMessages">
            @foreach ($messages as $msg)
                <div class="kcode-message {{ $msg['role'] }}">
                    <div class="kcode-avatar {{ $msg['role'] === 'assistant' ? 'ai' : 'user-av' }}">
                        {{ $msg['role'] === 'assistant' ? '🤖' : '👤' }}
                    </div>
                    <div>
                        <div class="kcode-bubble">
                            {!! nl2br(e($msg['content'])) !!}

                            @if (!empty($msg['products']))
                                <div class="mt-3 font-semibold text-xs text-rose-600 dark:text-rose-400">
                                    🛍️ {{ app()->getLocale() === 'ar' ? 'المنتجات المقترحة ذات الصلة:' : 'Related Products:' }}
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1">
                                    @foreach ($msg['products'] as $prod)
                                        <div class="kcode-product-card">
                                            @if (!empty($prod['image_path']))
                                                <img src="{{ $prod['image_path'] }}" alt="{{ $prod['name_ar'] ?? $prod['name_en'] ?? '' }}" class="w-10 h-10 object-cover rounded-lg">
                                            @endif
                                            <div class="text-xs overflow-hidden">
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
                        <div class="kcode-time text-gray-400">
                            {{ $msg['time'] ?? '' }}
                            @if (!empty($msg['model']))
                                • <span class="text-rose-500 font-mono">{{ $msg['model'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <div wire:loading wire:target="sendMessage, sendPreset" class="kcode-message assistant">
                <div class="kcode-avatar ai">🤖</div>
                <div class="kcode-bubble animate-pulse">
                    ⏳ {{ app()->getLocale() === 'ar' ? 'جاري التفكير والتوليد...' : 'AI is thinking...' }}
                </div>
            </div>
        </div>

        <!-- Input & Presets -->
        <div class="kcode-chat-input-area">
            <!-- Preset Prompt Chips -->
            <div class="kcode-presets">
                <button type="button" wire:click="sendPreset('ما هي أهم مميزات متجر KCODE وكيف نزيد المبيعات؟')" class="kcode-chip">
                    💡 مميزات KCODE وزيادة المبيعات
                </button>
                <button type="button" wire:click="sendPreset('اقترح لي روتين عناية بالبشرة الدهنية مع المنتجات')" class="kcode-chip">
                    ✨ روتين للبشرة الدهنية
                </button>
                <button type="button" wire:click="sendPreset('اكتب لي كود لارافيل لحساب إجمالي المبيعات اليومية')" class="kcode-chip">
                    💻 كود Laravel مفيد
                </button>
                <button type="button" wire:click="sendPreset('ما هي أحدث صيحات التجارة الإلكترونية هذا العام؟')" class="kcode-chip">
                    🚀 صيحات التجارة الإلكترونية
                </button>
            </div>

            <form wire:submit.prevent="sendMessage" x-on:submit.prevent="$wire.sendMessage()" class="kcode-input-wrapper">
                <input
                    type="text"
                    wire:model="userMessage"
                    x-on:keydown.enter.prevent="$wire.sendMessage()"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب أي سؤال أو استفسار هنا (غير مقيد بالموضوع)...' : 'Type any question or prompt here (unrestricted)...' }}"
                    class="kcode-input"
                    autofocus
                />
                <button type="button" wire:click="sendMessage" wire:loading.attr="disabled" class="kcode-send-btn">
                    <span wire:loading.remove wire:target="sendMessage, sendPreset">إرسال 🚀</span>
                    <span wire:loading wire:target="sendMessage, sendPreset">جاري الإرسال...</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const scrollChat = () => {
                const container = document.getElementById('chatMessages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            };
            scrollChat();
            Livewire.hook('morph.updated', () => {
                scrollChat();
            });
        });
    </script>
</x-filament-panels::page>
