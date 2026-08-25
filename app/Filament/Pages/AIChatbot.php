<?php

namespace App\Filament\Pages;

use App\Services\GroqChatService;
use BackedEnum;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class AIChatbot extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'icon-chatbot-messages';

    protected string $view = 'filament.pages.ai-chatbot';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Project & System Settings' : 'عن المشروع وإعدادات النظام';
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
    public string $pendingPrompt = '';
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

        // Push user message immediately
        $this->messages[] = [
            'role' => 'user',
            'content' => $prompt,
            'time' => now()->format('H:i'),
            'products' => [],
        ];

        $this->pendingPrompt = $prompt;
        $this->userMessage = '';
        $this->isThinking = true;

        // Dispatch async AI fetch
        $this->dispatch('fetch-page-ai-response');
    }

    #[On('fetch-page-ai-response')]
    public function fetchAiResponse(): void
    {
        if (empty($this->pendingPrompt)) {
            return;
        }

        $prompt = $this->pendingPrompt;
        $this->pendingPrompt = '';

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

    public function clearChat(): void
    {
        $this->mount();
        $this->isThinking = false;
        $this->pendingPrompt = '';
    }
}
