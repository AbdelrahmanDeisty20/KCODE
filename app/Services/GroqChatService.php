<?php

namespace App\Services;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use App\Models\Category;
use App\Models\ChatbotMessage;
use App\Models\Concern;
use App\Models\Product;
use App\Models\SkinType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqChatService
{
    protected string $apiKey;
    protected string $defaultModel;
    protected array $models;
    protected float $temperature;
    protected int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('groq.api_key', env('GROQ_API_KEY', ''));
        $this->defaultModel = config('groq.model', env('GROQ_MODEL', 'llama-3.3-70b-versatile'));
        $this->models = config('groq.models', [
            'llama-3.3-70b-versatile',
            'llama-3.1-8b-instant',
            'allam-2-7b',
            'qwen/qwen3.6-27b',
            'openai/gpt-oss-20b',
        ]);
        $this->temperature = (float) config('groq.temperature', 0.7);
        $this->maxTokens = (int) config('groq.max_tokens', 1000);
    }

    /**
     * Handle chat request with Groq AI with automatic Model Fallback Chain.
     */
    public function ask(string $prompt, array $history = [], ?string $locale = null, ?int $userId = null, ?string $sessionId = null): array
    {
        $locale = $locale ?? app()->getLocale();

        // 1. Build KCODE Store Knowledge Context
        $storeContext = $this->buildStoreContext($locale);

        // 2. Prepare System Instruction
        $systemPrompt = $locale === 'ar'
            ? config('groq.system_prompt_ar')
            : config('groq.system_prompt_en');

        $fullSystemContext = $systemPrompt . "\n\n" . $storeContext;

        // 3. Fallback if API key is missing
        if (empty($this->apiKey)) {
            $result = $this->generateFallbackResponse($prompt, $locale);
            $this->saveChatMessage($prompt, $result['reply'], $result['recommended_products'], $userId, $sessionId);
            return $result;
        }

        // 4. Build OpenAI-compatible Messages Payload for Groq
        $messages = [];

        // System prompt
        $messages[] = [
            'role' => 'system',
            'content' => $fullSystemContext,
        ];

        // Format history
        foreach ($history as $msg) {
            $role = ($msg['role'] ?? 'user') === 'model' || ($msg['role'] ?? 'user') === 'assistant' ? 'assistant' : 'user';
            $content = $msg['content'] ?? $msg['text'] ?? '';
            if (!empty($content)) {
                $messages[] = [
                    'role' => $role,
                    'content' => $content,
                ];
            }
        }

        // Append current user prompt
        $messages[] = [
            'role' => 'user',
            'content' => $prompt,
        ];

        // 5. Automatic Model Fallback Chain Processing
        $modelsToTry = array_unique(array_merge([$this->defaultModel], $this->models));
        $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

        foreach ($modelsToTry as $currentModel) {
            try {
                $payload = [
                    'model' => $currentModel,
                    'messages' => $messages,
                    'temperature' => $this->temperature,
                    'max_tokens' => $this->maxTokens,
                ];

                $response = Http::timeout(15)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Content-Type' => 'application/json',
                    ])
                    ->post($endpoint, $payload);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $replyText = $responseData['choices'][0]['message']['content'] ?? null;

                    if ($replyText) {
                        // Clean up any internal reasoning tags if present
                        $cleanReplyText = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $replyText);
                        $cleanReplyText = trim($cleanReplyText);

                        if (!empty($cleanReplyText)) {
                            $recommendedProducts = $this->extractRecommendedProducts($cleanReplyText, $prompt);

                            $this->saveChatMessage($prompt, $cleanReplyText, $recommendedProducts, $userId, $sessionId);

                            Log::info("Groq Chat successfully responded using model [{$currentModel}]");

                            return [
                                'status' => true,
                                'model' => $currentModel,
                                'reply' => $cleanReplyText,
                                'recommended_products' => $recommendedProducts,
                            ];
                        }
                    }
                }

                Log::warning("Groq model [{$currentModel}] returned HTTP {$response->status()}. Failing over to next model...", [
                    'response' => substr($response->body(), 0, 200)
                ]);
            } catch (\Exception $e) {
                Log::warning("Groq model [{$currentModel}] exception: {$e->getMessage()}. Failing over to next model...");
            }
        }

        // 6. Hard Fallback Response if all Groq models fail or hit rate limits
        Log::error("All Groq AI models failed or hit rate limits. Serving intelligent fallback response.");
        $result = $this->generateFallbackResponse($prompt, $locale);
        $this->saveChatMessage($prompt, $result['reply'], $result['recommended_products'], $userId, $sessionId);
        return $result;
    }

    /**
     * Save message to chatbot_messages table.
     */
    protected function saveChatMessage(string $prompt, string $reply, array $recommendedProducts, ?int $userId = null, ?string $sessionId = null): void
    {
        try {
            ChatbotMessage::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'prompt' => $prompt,
                'reply' => $reply,
                'recommended_products' => $recommendedProducts,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save chatbot message: ' . $e->getMessage());
        }
    }

    /**
     * Build catalog knowledge context for system prompt.
     */
    protected function buildStoreContext(string $locale): string
    {
        return \Illuminate\Support\Facades\Cache::remember("groq_store_context_{$locale}", 300, function () use ($locale) {
            $products = Product::with(['category', 'brand', 'skinTypes', 'concerns'])
                ->where('status', 'active')
                ->orWhereNull('status')
                ->take(30)
                ->get();

            $skinTypes = SkinType::all()->pluck('name')->implode(', ');
            $concerns = Concern::all()->pluck('name')->implode(', ');

            $catalogSummary = "أنواع البشرة المدعومة: {$skinTypes}\nمشاكل البشرة المدعومة: {$concerns}\n\nقائمة منتجات KCODE المتاحة حالياً:\n";

            foreach ($products as $p) {
                $name = $locale === 'ar' ? ($p->name_ar ?? $p->name_en) : ($p->name_en ?? $p->name_ar);
                $cat = $p->category ? ($locale === 'ar' ? $p->category->name_ar : $p->category->name_en) : '';
                $price = $p->price;
                $catalogSummary .= "- [ID: {$p->id}] {$name} | القسم: {$cat} | السعر: {$price} EGP\n";
            }

            return $catalogSummary;
        });
    }

    /**
     * Match and extract products relevant to AI reply and prompt.
     */
    protected function extractRecommendedProducts(string $replyText, string $prompt): array
    {
        $matchedProducts = collect();

        // 1. Check for product IDs or names in text
        $products = Product::where('status', 'active')->orWhereNull('status')->get();

        foreach ($products as $product) {
            $nameAr = $product->name_ar;
            $nameEn = $product->name_en;

            if (
                ($nameAr && mb_stripos($replyText, $nameAr) !== false) ||
                ($nameEn && stripos($replyText, $nameEn) !== false) ||
                ($nameAr && mb_stripos($prompt, $nameAr) !== false) ||
                (stripos($replyText, "[ID: {$product->id}]") !== false)
            ) {
                $matchedProducts->push($product);
            }
        }

        // 2. If no direct match in reply, search products matching keywords in prompt
        if ($matchedProducts->isEmpty()) {
            $searchKeywords = array_filter(explode(' ', $prompt), fn($w) => mb_strlen($w) > 3);
            if (!empty($searchKeywords)) {
                $matchedProducts = Product::where(function ($q) use ($searchKeywords) {
                    foreach ($searchKeywords as $kw) {
                        $q->orWhere('name_ar', 'like', "%{$kw}%")
                          ->orWhere('name_en', 'like', "%{$kw}%");
                    }
                })->take(4)->get();
            }
        }

        // Return formatted product resources
        return ProductListResource::collection($matchedProducts->unique('id')->take(4))->resolve();
    }

    /**
     * Fallback response if Groq API key is missing or request fails.
     */
    protected function generateFallbackResponse(string $prompt, string $locale): array
    {
        $keywords = mb_strtolower($prompt);
        $products = Product::take(3)->get();

        if (mb_strpos($keywords, 'جاف') !== false || mb_strpos($keywords, 'dry') !== false) {
            $reply = $locale === 'ar'
                ? "للبشرة الجافة، يُفضل استخدام غسول كريمي مغذي متبوعاً بسيروم الهيالورونيك أسيد ومرطب غني بالسيراميد لحبس الرطوبة داخل الجلد. إليك أفضل منتجات KCODE المرشحة لبشرتك:"
                : "For dry skin, we recommend a gentle cream cleanser followed by a Hyaluronic Acid serum and a ceramide-rich moisturizer. Here are our top KCODE recommendations:";
        } elseif (mb_strpos($keywords, 'دهن') !== false || mb_strpos($keywords, 'oily') !== false || mb_strpos($keywords, 'حب') !== false) {
            $reply = $locale === 'ar'
                ? "للبشرة الدهنية والمعرضة لحب الشباب، يُنصح باستخدام غسول رغوي لطيف يحتوي على حمض الساليسيليك مع مرطب مائي خفيف خالي من الزيوت لتنظيم إفراز الدهون. إليك منتجاتنا المرشحة:"
                : "For oily and acne-prone skin, use a gentle foaming wash with Salicylic Acid and an oil-free gel moisturizer to balance sebum production. Recommended products:";
        } else {
            $reply = $locale === 'ar'
                ? "أهلاً بك في مستشار KCODE الذكي! أنا مساعدك الذكي الشامل. كيف يمكنني مساعدتك اليوم في أي استفسار أو موضوع؟"
                : "Welcome to KCODE AI Assistant! I am your versatile AI assistant. How can I help you with your query today?";
        }

        return [
            'status' => true,
            'reply' => $reply,
            'recommended_products' => ProductListResource::collection($products)->resolve(),
        ];
    }
}
