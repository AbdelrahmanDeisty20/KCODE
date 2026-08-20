<?php

namespace App\Livewire;

use App\Services\GroqChatService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class FloatingChatbot extends Component
{
    public bool $isOpen = false;
    public bool $showTooltip = true;
    public array $messages = [];
    public string $userMessage = '';
    public bool $isThinking = false;

    public function mount(): void
    {
        $isAr = app()->getLocale() === 'ar';
        $welcomeText = $isAr
            ? "مرحباً بك! أنا 'مستشار KCODE الذكي'. يمكنك سؤالي عن أي موضوع بدون قيود! 💖"
            : "Welcome! I am 'KCODE AI Assistant'. Ask me anything without restrictions! 💖";

        $this->messages = [
            [
                'role' => 'assistant',
                'content' => $welcomeText,
                'time' => now()->format('H:i'),
                'products' => [],
            ],
        ];
    }

    public function toggleChat(): void
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->showTooltip = false;
        }
    }

    public function dismissTooltip(): void
    {
        $this->showTooltip = false;
    }

    public function sendMessage(?string $message = null): void
    {
        $prompt = trim($message ?? $this->userMessage);
        if (empty($prompt)) {
            return;
        }

        // 1. Immediately push user message to conversation
        $this->messages[] = [
            'role' => 'user',
            'content' => $prompt,
            'time' => now()->format('H:i'),
            'products' => [],
        ];

        // 2. Clear input state
        $this->userMessage = '';
        $this->isThinking = true;

        // 3. Process AI Response
        try {
            /** @var GroqChatService $chatService */
            $chatService = app(GroqChatService::class);

            $history = array_map(function ($msg) {
                return [
                    'role' => $msg['role'],
                    'content' => $msg['content'],
                ];
            }, array_slice($this->messages, -10));

            $response = $chatService->ask(
                prompt: $prompt,
                history: $history,
                locale: app()->getLocale(),
                userId: auth()->id(),
                sessionId: 'admin-floating-' . (auth()->id() ?? 'guest')
            );

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response['reply'] ?? (app()->getLocale() === 'ar' ? 'لم أتمكن من استخراج الإجابة حالياً.' : 'Could not generate response currently.'),
                'time' => now()->format('H:i'),
                'model' => $response['model'] ?? 'Groq AI',
                'products' => $response['recommended_products'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error("Floating AI Chatbot error: " . $e->getMessage());

            $this->messages[] = [
                'role' => 'assistant',
                'content' => (app()->getLocale() === 'ar' ? 'تنبيه: ' : 'Notice: ') . $e->getMessage(),
                'time' => now()->format('H:i'),
                'products' => [],
            ];
        } finally {
            $this->isThinking = false;
        }
    }

    public function sendPreset(string $text): void
    {
        $this->sendMessage($text);
    }

    public function clearChat(): void
    {
        $this->mount();
        $this->isThinking = false;
    }

    public function render()
    {
        return view('livewire.floating-chatbot');
    }
}
