<?php

namespace App\Services;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use App\Models\Category;
use App\Models\Concern;
use App\Models\Product;
use App\Models\SkinType;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqChatService
{
    protected string $apiKey;
    protected string $model;
    protected float $temperature;
    protected int $maxTokens;

    public function __construct()
    {
        $this->apiKey = config('groq.api_key', env('GROQ_API_KEY', ''));
        $this->model = config('groq.model', env('GROQ_MODEL', 'llama-3.3-70b-versatile'));
        $this->temperature = (float) config('groq.temperature', 0.7);
        $this->maxTokens = (int) config('groq.max_tokens', 1000);
    }

    /**
     * Handle chat request with Groq AI (Llama models).
     */
    public function ask(string $prompt, array $history = [], ?string $locale = null): array
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
            return $this->generateFallbackResponse($prompt, $locale);
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

        try {
            $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

            $payload = [
                'model' => $this->model,
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
                    $recommendedProducts = $this->extractRecommendedProducts($replyText, $prompt);

                    return [
                        'status' => true,
                        'reply' => trim($replyText),
                        'recommended_products' => $recommendedProducts,
                    ];
                }
            }

            Log::error('Groq API Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Groq API Exception: ' . $e->getMessage());
        }

        return $this->generateFallbackResponse($prompt, $locale);
    }

    /**
     * Build catalog knowledge context for system prompt.
     */
    protected function buildStoreContext(string $locale): string
    {
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
                ? "أهلاً بك في مستشار KCODE الذكي! يسعدني مساعدتك في اختيار أفضل روتين ومنتجات لبشرتك. يرجى توضيح نوع بشرتك أو المشكلة التي تعاني منها للحصول على ترشيح مخصص."
                : "Welcome to KCODE AI Consultant! I am happy to help you find the best skincare routine and products. Please specify your skin type or concerns for tailored advice.";
        }

        return [
            'status' => true,
            'reply' => $reply,
            'recommended_products' => ProductListResource::collection($products)->resolve(),
        ];
    }
}
