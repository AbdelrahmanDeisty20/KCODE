<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Chatbot\ChatRequest;
use App\Http\Resources\API\CHATBOT\ChatbotMessageResource;
use App\Services\ChatbotService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    use ApiResponse;

    public function __construct(protected ChatbotService $chatbotService) {}

    /**
     * Send prompt to Groq AI Chatbot and get skincare advice + product recommendations.
     */
    public function chat(ChatRequest $request): JsonResponse
    {
        $userId = auth('sanctum')->id();
        $result = $this->chatbotService->chat($userId, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success($result['data'], $result['message']);
    }

    /**
     * Get quick skincare suggestion chips for frontend chatbot UI.
     */
    public function suggestions(): JsonResponse
    {
        $result = $this->chatbotService->getSuggestions();

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success($result['data'], $result['message']);
    }

    /**
     * Get paginated chat history for the authenticated user or session.
     */
    public function history(Request $request): JsonResponse
    {
        $userId = auth('sanctum')->id();
        $sessionId = $request->input('session_id') ?? $request->header('X-Session-ID');
        $perPage = (int) $request->input('per_page', 10);

        $result = $this->chatbotService->getHistory($userId, $sessionId, $perPage);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->paginated(
            ChatbotMessageResource::class,
            $result['data'],
            $result['message']
        );
    }

    /**
     * Clear chat history for the authenticated user or session.
     */
    public function clearHistory(Request $request): JsonResponse
    {
        $userId = auth('sanctum')->id();
        $sessionId = $request->input('session_id') ?? $request->header('X-Session-ID');

        $result = $this->chatbotService->clearHistory($userId, $sessionId);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success([], $result['message']);
    }
}
