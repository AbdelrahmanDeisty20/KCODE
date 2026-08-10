<?php

namespace App\Services;

use App\Models\ChatbotMessage;
use App\Models\ChatbotSuggestion;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    public function __construct(protected GroqChatService $groqChatService) {}

    /**
     * Handle user chat prompt with Groq AI.
     */
    public function chat(?int $userId, array $data): array
    {
        try {
            $prompt = $data['prompt'];
            $history = $data['history'] ?? [];
            $sessionId = $data['session_id'] ?? null;

            $result = $this->groqChatService->ask($prompt, $history, null, $userId, $sessionId);

            return [
                'status' => $result['status'] ?? true,
                'message' => __('messages.chatbot_response_success'),
                'data' => [
                    'reply' => $result['reply'],
                    'recommended_products' => $result['recommended_products'],
                ]
            ];
        } catch (\Exception $e) {
            Log::error('ChatbotService chat error: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get chatbot question suggestions.
     */
    public function getSuggestions(): array
    {
        try {
            $isAr = app()->getLocale() === 'ar';

            $dbSuggestions = ChatbotSuggestion::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();

            if ($dbSuggestions->isNotEmpty()) {
                $suggestions = $dbSuggestions->map(function ($item) use ($isAr) {
                    return $isAr ? $item->question_ar : $item->question_en;
                })->values()->toArray();
            } else {
                $suggestions = $isAr ? [
                    "ما هو الروتين المناسب للبشرة الجافة في الشتاء؟",
                    "أفضل سيروم لتفتيح التصبغات والبقع الداكنة؟",
                    "طريقة استخدام واقي الشمس بالشكل الصحيح؟",
                    "علاج حب الشباب والحد من إفرازات الدهون؟",
                    "ترتيب خطوات الروتين المسائي قبل النوم؟",
                ] : [
                    "What is the best routine for dry skin in winter?",
                    "Top recommended serum for hyperpigmentation and dark spots?",
                    "How to apply sunscreen correctly?",
                    "How to control acne and excess oil?",
                    "What is the correct order for a night skincare routine?",
                ];
            }

            return [
                'status' => true,
                'message' => __('messages.suggestions_retrieved_successfully'),
                'data' => $suggestions,
            ];
        } catch (\Exception $e) {
            Log::error('ChatbotService getSuggestions error: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get paginated chat history for the user.
     */
    public function getHistory(?int $userId, ?string $sessionId = null, int $perPage = 10): array
    {
        try {
            $query = ChatbotMessage::query();

            if ($userId) {
                $query->where('user_id', $userId);
            } elseif ($sessionId) {
                $query->where('session_id', $sessionId);
            } else {
                return [
                    'status' => false,
                    'message' => __('messages.no_chat_history_found'),
                    'data' => collect()
                ];
            }

            $messages = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return [
                'status' => true,
                'message' => __('messages.history_retrieved_successfully'),
                'data' => $messages,
            ];
        } catch (\Exception $e) {
            Log::error('ChatbotService getHistory error: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => collect()
            ];
        }
    }

    /**
     * Clear chat history for the user.
     */
    public function clearHistory(?int $userId, ?string $sessionId = null): array
    {
        try {
            if ($userId) {
                ChatbotMessage::where('user_id', $userId)->delete();
            } elseif ($sessionId) {
                ChatbotMessage::where('session_id', $sessionId)->delete();
            }

            return [
                'status' => true,
                'message' => __('messages.history_cleared_successfully'),
            ];
        } catch (\Exception $e) {
            Log::error('ChatbotService clearHistory error: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
