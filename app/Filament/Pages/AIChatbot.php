<?php

namespace App\Filament\Pages;

use App\Services\GroqChatService;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;

class AIChatbot extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'icon-chatbot-messages';

    protected string $view = 'filament.pages.ai-chatbot';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'AI Chatbot' : 'المستشار الذكي';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Live AI Chatbot' : 'تجربة الشات بوت (مباشر)';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'en' ? 'Interactive AI Chatbot' : 'المستشار الذكي المباشر (AI Assistant)';
    }

    public array $messages = [];
    public string $userMessage = '';
    public bool $isThinking = false;

    public function mount(): void
    {
        $isAr = app()->getLocale() === 'ar';
        $welcomeText = $isAr
            ? "مرحباً بك! أنا 'مستشار KCODE الذكي'. يمكنك سؤالي عن أي موضوع، منتج، برمجيات، أو استفسار عام بدون أي قيود. كيف يمكنني مساعدتك اليوم؟"
            : "Welcome! I am 'KCODE AI Assistant'. You can ask me about any topic, product, software, or general inquiry without restrictions. How can I help you today?";

        $this->messages = [
            [
                'role' => 'assistant',
                'content' => $welcomeText,
                'time' => now()->format('H:i'),
                'products' => [],
            ],
        ];
    }

    public function sendMessage(?string $text = null): void
    {
        $prompt = trim($text ?? $this->userMessage);
        if (empty($prompt)) {
            return;
        }

        // Push user message
        $this->messages[] = [
            'role' => 'user',
            'content' => $prompt,
            'time' => now()->format('H:i'),
            'products' => [],
        ];

        $this->userMessage = '';
        $this->isThinking = true;

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
                sessionId: 'admin-dashboard-' . auth()->id()
            );

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response['reply'] ?? (app()->getLocale() === 'ar' ? 'لم أتمكن من استخراج الإجابة حالياً.' : 'Could not generate response currently.'),
                'time' => now()->format('H:i'),
                'model' => $response['model'] ?? 'Groq AI',
                'products' => $response['recommended_products'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::error("Dashboard AI Chatbot error: " . $e->getMessage());

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
}
