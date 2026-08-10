<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Chatbot\ChatRequest;
use App\Models\ChatbotMessage;
use App\Models\ChatbotSuggestion;
use App\Services\GroqChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $sessionId = $request->input('session_id') ?? $request->header('X-Session-ID');
        $userId = auth('sanctum')->id();

        $result = $this->groqChatService->ask($prompt, $history, null, $userId, $sessionId);

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

        $dbSuggestions = ChatbotSuggestion::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        if ($dbSuggestions->isNotEmpty()) {
            $suggestions = $dbSuggestions->map(function ($item) use ($isAr) {
                return $isAr ? $item->question_ar : $item->question_en;
            })->values()->toArray();
        } else {
            // Default fallback if database is empty
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

        return response()->json([
            'status' => true,
            'message' => __('messages.suggestions_retrieved_successfully', ['default' => 'تم جلب الأسئلة المقترحة بنجاح']),
            'data' => $suggestions,
        ]);
    }

    /**
     * Get chat history for the user or session.
     */
    public function history(Request $request): JsonResponse
    {
        $userId = auth('sanctum')->id();
        $sessionId = $request->input('session_id') ?? $request->header('X-Session-ID');

        $query = ChatbotMessage::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            return response()->json([
                'status' => true,
                'message' => __('messages.history_retrieved', ['default' => 'تم جلب سجل المحادثات بنجاح']),
                'data' => [],
            ]);
        }

        $messages = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'status' => true,
            'message' => __('messages.history_retrieved', ['default' => 'تم جلب سجل المحادثات بنجاح']),
            'data' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'prompt' => $msg->prompt,
                    'reply' => $msg->reply,
                    'recommended_products' => $msg->recommended_products ?? [],
                    'created_at' => $msg->created_at->toDateTimeString(),
                ];
            }),
        ]);
    }

    /**
     * Clear chat history for the user or session.
     */
    public function clearHistory(Request $request): JsonResponse
    {
        $userId = auth('sanctum')->id();
        $sessionId = $request->input('session_id') ?? $request->header('X-Session-ID');

        if ($userId) {
            ChatbotMessage::where('user_id', $userId)->delete();
        } elseif ($sessionId) {
            ChatbotMessage::where('session_id', $sessionId)->delete();
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.history_cleared', ['default' => 'تم مسح سجل المحادثات بنجاح']),
        ]);
    }
}
