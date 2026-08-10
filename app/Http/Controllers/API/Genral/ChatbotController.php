<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Chatbot\ChatRequest;
use App\Services\GroqChatService;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    public function __construct(protected GroqChatService $groqChatService) {}

    /**
     * Send prompt to Groq AI Chatbot and get skincare advice + product recommendations.
     */
    public function chat(ChatRequest $request): JsonResponse
    {
        $prompt = $request->input('prompt');
        $history = $request->input('history', []);

        $result = $this->groqChatService->ask($prompt, $history);

        return response()->json([
            'status' => $result['status'] ?? true,
            'message' => __('messages.chatbot_response_success', ['default' => 'تم رد المستشار الذكي بنجاح']),
            'data' => [
                'reply' => $result['reply'],
                'recommended_products' => $result['recommended_products'],
            ]
        ]);
    }

    /**
     * Get quick skincare suggestion chips for frontend chatbot UI.
     */
    public function suggestions(): JsonResponse
    {
        $isAr = app()->getLocale() === 'ar';

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

        return response()->json([
            'status' => true,
            'message' => __('messages.suggestions_retrieved_successfully', ['default' => 'تم جلب الأسئلة المقترحة بنجاح']),
            'data' => $suggestions,
        ]);
    }
}
