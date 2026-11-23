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

        // 1. Detect any SKU patterns in prompt (e.g. KC0159)
        $queriedSkus = [];
        if (preg_match_all('/\b(KC\d{4})\b/i', $prompt, $skuMatches)) {
            $queriedSkus = array_unique(array_map('strtoupper', $skuMatches[1]));
        }

        // 2. Build Authoritative SKU Directives to strictly bind AI output to Catalog Master
        $skuDirectives = $this->buildSkuDirectives($queriedSkus, $locale);

        // 3. Build KCODE Store Knowledge Context
        $storeContext = $this->buildStoreContext($locale);

        // 4. Prepare System Instruction
        $systemPrompt = $locale === 'ar'
            ? config('groq.system_prompt_ar')
            : config('groq.system_prompt_en');

        $fullSystemContext = $systemPrompt;
        if (!empty($skuDirectives)) {
            $fullSystemContext .= "\n\n" . $skuDirectives;
        }
        $fullSystemContext .= "\n\n" . $storeContext;

        // 5. Fallback if API key is missing
        if (empty($this->apiKey)) {
            $result = $this->generateFallbackResponse($prompt, $locale, $queriedSkus);
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

        // 5. Automatic Model Fallback Chain Processing (bounded to fastest 3 models to prevent worker exhaustion)
        $modelsToTry = array_slice(
            array_values(array_unique(array_merge([$this->defaultModel], $this->models))),
            0,
            3
        );
        $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

        foreach ($modelsToTry as $currentModel) {
            try {
                $payload = [
                    'model' => $currentModel,
                    'messages' => $messages,
                    'temperature' => $this->temperature,
                    'max_tokens' => $this->maxTokens,
                ];

                $response = Http::timeout(5)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Content-Type' => 'application/json',
                    ])
                    ->post($endpoint, $payload);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $replyText = $responseData['choices'][0]['message']['content'] ?? null;

                    if ($replyText) {
                        // Clean up internal reasoning & prompt artifacts
                        $cleanReplyText = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $replyText);
                        $cleanReplyText = preg_replace('/\[(SYSTEM|ID:\s*\d+|ID)\][^\n]*\n?/i', '', $cleanReplyText);
                        $cleanReplyText = preg_replace('/\[ID:\s*\d+\]/i', '', $cleanReplyText);

                        // Strict Disclaimer & Apology Sanitizer:
                        $cleanReplyText = preg_replace('/^(أعتذر|عذراً|أسف|عذرا|I apologize|Sorry|Apologies)[\s\S]*?(؟|\.|\!\n|\n)/u', '', $cleanReplyText);
                        $cleanReplyText = preg_replace('/(أعتذر عن الالتباس السابق|أعتذر عن الالتباس|عذراً عن الالتباس|أعتذر عن الخلل|عذراً على الإزعاج|عذرا على الخطأ|I apologize for the confusion)/u', '', $cleanReplyText);
                        $cleanReplyText = preg_replace('/(بصفتي مساعد ذكاء اصطناعي،? ليس لديّ? اسمٌ? حقيقي[\.\،\!]?|كمساعد ذكاء (اصطناعي|صناعي)،? لا أملك اسمًا شخصيًا كإنسان[\.\،\!]?|كمساعد ذكاء (اصطناعي|صناعي)[^،\.\!\n]*[،\.\!\n]?)/u', 'أنا مستشار KCODE الذكي 🤖، ', $cleanReplyText);
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
        $result = $this->generateFallbackResponse($prompt, $locale, $queriedSkus);
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
        try {
            // 1. Fetch Live Dashboard Metrics (cached for 5 minutes to protect MySQL under chat bursts)
            $dashboardMetrics = \Illuminate\Support\Facades\Cache::remember("groq_dash_metrics_{$locale}", 300, function () use ($locale) {
                $totalSales = \App\Models\Order::whereNotIn('order_status', ['cancelled', 'failed'])->sum('total');
                $todaySalesCount = \App\Models\Order::whereDate('created_at', today())->count();
                $todaySalesRevenue = \App\Models\Order::whereDate('created_at', today())->whereNotIn('order_status', ['cancelled', 'failed'])->sum('total');
                $avgOrderValue = \App\Models\Order::whereNotIn('order_status', ['cancelled', 'failed'])->avg('total') ?? 0;

                $totalUsers = \App\Models\User::count();
                $totalCustomers = \App\Models\User::where('type', 'user')->count();
                $blogAuthorsCount = \App\Models\User::where('type', 'blog_author')
                    ->orWhereHas('roles', fn($q) => $q->where('name', 'like', '%blog%'))
                    ->count();
                $adminsCount = \App\Models\User::where('type', 'admin')->count();

                $totalOrders = \App\Models\Order::count();
                $pendingOrders = \App\Models\Order::whereIn('order_status', ['pending', 'processing'])->count();
                $completedOrders = \App\Models\Order::where('order_status', 'completed')->count();

                $totalProducts = Product::count();
                $activeProducts = Product::where('status', 'active')->count();

                $totalBlogs = \App\Models\Blog::count();
                $totalReviews = \App\Models\Review::count();
                $avgRating = \App\Models\Review::avg('rating') ?? 0;

                // Detailed Today Orders Breakdown
                $todayOrders = \App\Models\Order::with('items')
                    ->whereDate('created_at', today())
                    ->latest()
                    ->take(10)
                    ->get();

                $todayOrdersText = "";
                if ($todayOrders->count() > 0) {
                    $todayOrdersText .= "تفاصيل طلبات وتنفيذ المبيعات لليوم:\n";
                    foreach ($todayOrders as $ord) {
                        $itemNames = [];
                        foreach ($ord->items as $it) {
                            $itemNames[] = ($it->product_name ?? 'منتج') . " (الكمية: {$it->quantity})";
                        }
                        $itemsStr = implode(', ', $itemNames);
                        $todayOrdersText .= "- طلب #{$ord->order_number} | العميل: " . ($ord->user_name ?? 'مشتري') . " | الإجمالي: {$ord->total} EGP | الحالة: {$ord->order_status} | العناصر: [{$itemsStr}]\n";
                    }
                } else {
                    $todayOrdersText .= "ملاحظة: لم تُسجل طلبات شراء جديدة اليوم حتى الآن.\n";
                }

                if ($locale === 'ar') {
                    return "=== إحصائيات ومعلومات الداشبورد والمتجر المباشرة (LIVE DASHBOARD DATA) ===\n" .
                           "- إجمالي عدد الطلبات الكلي بالمتجر كله (منذ الإنشاء حتى الآن): {$totalOrders} طلب (الطلبات قيد المعالجة: {$pendingOrders}، الطلبات المكتملة: {$completedOrders})\n" .
                           "- عدد طلبات اليوم فقط (اليوم فقط): {$todaySalesCount} طلب\n" .
                           "- إجمالي إيرادات المبيعات اليوم: " . number_format($todaySalesRevenue, 2) . " EGP\n" .
                           "- متوسط قيمة المبيعات والطلبات: " . number_format($avgOrderValue, 2) . " EGP\n" .
                           "- إجمالي المبيعات الكلية للمتجر: " . number_format($totalSales, 2) . " EGP\n" .
                           "- إجمالي عدد المستخدمين كلهم: {$totalUsers} مستخدم\n" .
                           "- عدد العملاء المشترين (Customers): {$totalCustomers}\n" .
                           "- عدد كتاب المقالات (Blog Authors): {$blogAuthorsCount}\n" .
                           "- عدد المدراء (Admins): {$adminsCount}\n" .
                           "- إجمالي عدد المنتجات بالمتجر: {$totalProducts} (المنتجات النشطة: {$activeProducts})\n" .
                           "- عدد المقالات المنشورة: {$totalBlogs} مقال\n" .
                           "- إجمالي تقييمات العملاء: {$totalReviews} تقييم (متوسط التقييم: " . number_format($avgRating, 1) . " من 5)\n\n" .
                           "تنويه هام للغاية:\n" .
                           "- إذا سُئلت عن إجمالي الطلبات الكلي في المتجر، أجب فوراً بـ: {$totalOrders} طلب.\n" .
                           "- إذا سُئلت عن طلبات اليوم، أجب بـ: {$todaySalesCount} طلب.\n\n" .
                           $todayOrdersText . "\n";
                } else {
                    return "=== LIVE DASHBOARD & STORE METRICS ===\n" .
                           "- Total All-Time Store Orders: {$totalOrders} orders (Pending/Processing: {$pendingOrders}, Completed: {$completedOrders})\n" .
                           "- Today Orders Count Only: {$todaySalesCount} orders\n" .
                           "- Today Sales Revenue: " . number_format($todaySalesRevenue, 2) . " EGP\n" .
                           "- Average Order/Sales Value: " . number_format($avgOrderValue, 2) . " EGP\n" .
                           "- Total All-Time Revenue: " . number_format($totalSales, 2) . " EGP\n" .
                           "- Total All Users Count: {$totalUsers}\n" .
                           "- Total Customers Count: {$totalCustomers}\n" .
                           "- Total Blog Authors Count: {$blogAuthorsCount}\n" .
                           "- Total Admins Count: {$adminsCount}\n" .
                           "- Total Products Count: {$totalProducts} (Active: {$activeProducts})\n" .
                           "- Total Blog Articles Count: {$totalBlogs}\n" .
                           "- Total Customer Reviews: {$totalReviews} (Avg Rating: " . number_format($avgRating, 1) . "/5)\n\n" .
                           "IMPORTANT DIRECTIVE:\n" .
                           "- If asked about total store orders, answer: {$totalOrders} orders.\n" .
                           "- If asked about today's orders, answer: {$todaySalesCount} orders.\n\n" .
                           $todayOrdersText . "\n";
                }
            });

            // 2. Fetch Store Product Catalog Context
            $catalogSummary = \Illuminate\Support\Facades\Cache::remember("groq_store_context_{$locale}", 180, function () use ($locale) {
                $products = Product::with(['category', 'brand', 'skinTypes', 'concerns'])
                    ->where('status', 'active')
                    ->orWhereNull('status')
                    ->take(30)
                    ->get();

                $skinTypes = SkinType::all()->pluck('name')->implode(', ');
                $concerns = Concern::all()->pluck('name')->implode(', ');

                $summary = "أنواع البشرة المدعومة: {$skinTypes}\nمشاكل البشرة المدعومة: {$concerns}\n\nقائمة منتجات KCODE المتاحة حالياً:\n";

                foreach ($products as $p) {
                    $name = $locale === 'ar' ? ($p->name_ar ?? $p->name_en) : ($p->name_en ?? $p->name_ar);
                    $cat = $p->category ? ($locale === 'ar' ? $p->category->name_ar : $p->category->name_en) : '';
                    $price = $p->price;
                    $summary .= "- {$name} | القسم: {$cat} | السعر: {$price} EGP\n";
                }

                return $summary;
            });

            return $dashboardMetrics . "\n" . $catalogSummary;
        } catch (\Throwable $e) {
            Log::warning('GroqChatService::buildStoreContext error: ' . $e->getMessage());
            return "متجر KCODE: المتجر الرائد للمنتجات الكورية وروتينات العناية المتخصصة بالبشرة.";
        }
    }

    /**
     * Authoritatively resolve a product by its SKU code from the database.
     */
    public function resolveProductBySku(string $sku): ?array
    {
        $sku = strtoupper(trim($sku));
        $productModel = null;

        try {
            $productModel = Product::with(['category', 'brand', 'skinTypes', 'concerns'])
                ->where('sku', $sku)
                ->first();
        } catch (\Throwable $e) {
            $productModel = null;
        }

        if (!$productModel) {
            return null;
        }

        $nameEn = $productModel->name_en ?: $sku;
        $nameAr = $productModel->name_ar ?: $nameEn;
        $brand = $productModel->brand?->name ?? 'KCODE';
        $category = $productModel->category?->name_ar ?? '';

        $isSunscreen = ($sku === 'KC0159')
            || in_array($sku, ['KC0076', 'KC0077', 'KC0121', 'KC0218'], true)
            || stripos($nameEn, 'sunscreen') !== false
            || mb_stripos($nameAr, 'واقي') !== false
            || mb_stripos($category, 'واقي') !== false;

        if ($isSunscreen) {
            $category = 'واقي شمس (حماية كيميائية خفيفة من الأشعة فوق البنفسجية)';
            $usage = 'يُوضع صباحاً كآخر خطوة في روتين العناية بالبشرة (بعد المرطب وقبل المكياج) قبل التعرّض للشمس بنحو 15 دقيقة، ويُترك على البشرة دون شطف بالماء نهائياً (هو واقي شمس وليس غسولاً رغوياً).';
            $frequency = 'صباحاً يومياً، مع ضرورة إعادة التطبيق.';
            $reapplication = 'أعد تطبيقه كل ساعتين عند البقاء في الخارج، وجدّده فوراً بعد السباحة أو التعرّق أو تجفيف الوجه بالمنشفة.';
            $guidance = 'يناسب البشرة الدهنية والمختلطة والعادية والحساسة بقوام خفيف لا يترك أثراً أبيض. لا تخلطه بالمرطب أو كريم الأساس لتخفيفه، وتجنب ملامسة العينين.';
        } else {
            $usage = $productModel->how_to_use_ar ?: 'اتبع الترتيب المناسب في الروتين حسب نوع المنتج.';
            $frequency = $productModel->usage_frequency_ar ?: 'يومياً أو حسب إرشادات الروتين.';
            $reapplication = 'حسب الحاجة والروتين اليومي.';
            $guidance = $productModel->brief_insight_ar ?: '';
        }

        return [
            'sku' => $sku,
            'name_ar' => $nameAr,
            'name_en' => $nameEn,
            'brand' => $brand,
            'category' => $category,
            'is_sunscreen' => $isSunscreen,
            'usage' => $usage,
            'frequency' => $frequency,
            'reapplication' => $reapplication,
            'guidance' => $guidance,
            'model' => $productModel,
        ];
    }

    /**
     * Build authoritative directives for queried SKUs to inject into the system prompt.
     */
    protected function buildSkuDirectives(array $skus, string $locale): string
    {
        if (empty($skus)) {
            return '';
        }

        $directives = "=== توجيهات إلزامية خاصة بأكواد المنتجات المطلوبة (AUTHORITATIVE SKU DIRECTIVES) ===\n";

        foreach ($skus as $sku) {
            $info = $this->resolveProductBySku($sku);
            if ($info) {
                $directives .= "[منتج مسجل ومعتمد في كتالوج المتجر: {$sku}]\n";
                $directives .= "- الاسم الكامل: {$info['name_ar']} ({$info['name_en']})\n";
                $directives .= "- الماركة المعتمدة: {$info['brand']}\n";
                $directives .= "- التصنيف والنوع: {$info['category']}\n";
                $directives .= "- طريقة الاستخدام: {$info['usage']}\n";
                $directives .= "- تكرار الاستخدام: {$info['frequency']}\n";
                $directives .= "- إعادة التطبيق: {$info['reapplication']}\n";
                $directives .= "- توجيه وإرشادات: {$info['guidance']}\n";
                if ($info['is_sunscreen']) {
                    $directives .= "- تنبيه إلزامي صارم: هذا المنتج واقي شمس نهاري، وليس غسولاً رغوياً على الإطلاق، ولا يُشطف بالماء، ويجب إعادة تطبيقه كل ساعتين خارج المنزل.\n";
                }
                $directives .= "توجيه صارم للنموذج: التزم بالبيانات أعلاه نصاً ومعنى. يُحظر تماماً اختراع وظائف أو تصنيفات أخرى أو تخمين استخدامات تخالف الكتالوج.\n\n";
            } else {
                $directives .= "[كود غير مسجل في كتالوج المتجر: {$sku}]\n";
                $directives .= "هذا الكود غير موجود في كتالوج منتجات متجر KCODE.\n";
                $directives .= "توجيه صارم للنموذج: صرّح بوضوح واعتذار لطيف بأن كود المنتج ({$sku}) غير مسجل في كتالوج KCODE المعتمد ولا تتوفر أي بيانات أو تعليمات استخدام له، ويُحظر تماماً اختراع أي منتج أو تخمين طريقة استخدام له.\n\n";
            }
        }

        return $directives;
    }

    /**
     * Match and extract products relevant to AI reply and prompt.
     */
    protected function extractRecommendedProducts(string $replyText, string $prompt): array
    {
        $matchedProducts = collect();

        // 1. Direct SKU extraction from prompt or reply (single batch query instead of N+1)
        if (preg_match_all('/\b(KC\d{4})\b/i', $prompt . ' ' . $replyText, $matches)) {
            $skus = array_values(array_unique(array_map('strtoupper', $matches[1])));
            try {
                $productsBySku = Product::with(['brand', 'subCategory', 'category', 'offers' => fn ($q) => $q->active()])
                    ->whereIn('sku', $skus)
                    ->get()
                    ->keyBy(fn ($p) => strtoupper((string) $p->sku));

                foreach ($skus as $sku) {
                    $p = $productsBySku->get($sku);
                    if ($p) {
                        $matchedProducts->push($p);
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 2. Check for product IDs or names in text
        try {
            $products = Product::with(['brand', 'subCategory', 'category', 'offers' => fn ($q) => $q->active()])
                ->where('status', 'active')
                ->orWhereNull('status')
                ->get();

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

            // 3. Search by keywords if product intent present
            if ($matchedProducts->isEmpty()) {
                $productIntentKeywords = ['منتج', 'منتجات', 'روتين', 'غسول', 'سيروم', 'كريم', 'واقي', 'تونر', 'بشرة', 'حبوب', 'تفتيح', 'تجاعيد', 'مرطب', 'ماسك', 'شمس', 'كولاجين', 'عناية', 'skincare', 'product', 'routine', 'cream', 'serum', 'toner'];
                $hasProductIntent = false;

                foreach ($productIntentKeywords as $pik) {
                    if (mb_stripos($prompt, $pik) !== false || mb_stripos($replyText, $pik) !== false) {
                        $hasProductIntent = true;
                        break;
                    }
                }

                if ($hasProductIntent) {
                    $searchKeywords = array_filter(explode(' ', $prompt), fn($w) => mb_strlen($w) > 3);
                    if (!empty($searchKeywords)) {
                        $matchedProducts = Product::with(['brand', 'subCategory', 'category', 'offers' => fn ($q) => $q->active()])
                            ->where(function ($q) use ($searchKeywords) {
                                foreach ($searchKeywords as $kw) {
                                    $q->orWhere('name_ar', 'like', "%{$kw}%")
                                      ->orWhere('name_en', 'like', "%{$kw}%");
                                }
                            })->take(2)->get();
                    }
                }
            }
        } catch (\Throwable $e) {}

        // Return formatted product resources
        return $matchedProducts->isNotEmpty()
            ? ProductListResource::collection($matchedProducts->unique('id')->take(4))->resolve()
            : [];
    }

    /**
     * Fallback response if Groq API key is missing or request fails.
     */
    protected function generateFallbackResponse(string $prompt, string $locale, array $queriedSkus = []): array
    {
        // 1. If explicit SKU(s) were queried in prompt
        if (!empty($queriedSkus)) {
            $replies = [];
            $matchedModels = collect();

            foreach ($queriedSkus as $sku) {
                $info = $this->resolveProductBySku($sku);
                if ($info) {
                    if ($info['model']) {
                        $matchedModels->push($info['model']);
                    }
                    if ($info['is_sunscreen']) {
                        $replies[] = "المنتج ذو الكود **{$sku}** هو: **{$info['name_en']}** ({$info['name_ar']}) من علامة **{$info['brand']}**.\n\n" .
                            "• **النوع والتصنيف:** {$info['category']}.\n" .
                            "• **طريقة الاستخدام الصحيحة:**\n" .
                            "  1. يُوضع صباحاً كآخر خطوة في روتين العناية، بعد المرطب وقبل المكياج بنحو 15 دقيقة.\n" .
                            "  2. يُوزّع بالتساوي على الوجه والرقبة والأذنين، ويُترك على البشرة **دون شطف بالماء** نهائياً.\n" .
                            "  3. **إعادة التطبيق:** يجب إعادة تطبيقه كل ساعتين عند البقاء خارج المنزل، وتجديده فوراً بعد السباحة أو التعرّق أو تجفيف الوجه.\n" .
                            "• **التوجيه والملاءمة:** {$info['guidance']}";
                    } else {
                        $replies[] = "المنتج ذو الكود **{$sku}** هو: **{$info['name_en']}** ({$info['name_ar']}) من علامة **{$info['brand']}**.\n\n" .
                            "• **النوع والتصنيف:** {$info['category']}.\n" .
                            "• **طريقة الاستخدام:** {$info['usage']}\n" .
                            "• **تكرار الاستخدام:** {$info['frequency']}";
                    }
                } else {
                    $replies[] = "عذراً، كود المنتج (**{$sku}**) غير مسجل في كتالوج منتجات متجر KCODE المعتمد، ولا تتوفر لدينا أي بيانات أو تعليمات استخدام خاصة به.";
                }
            }

            return [
                'status' => true,
                'reply' => implode("\n\n---\n\n", $replies),
                'recommended_products' => $matchedModels->isNotEmpty()
                    ? ProductListResource::collection($matchedModels)->resolve()
                    : [],
            ];
        }

        // 2. If general sunscreen inquiry
        $keywords = mb_strtolower($prompt);
        if (mb_stripos($keywords, 'واقي') !== false || mb_stripos($keywords, 'شمس') !== false || stripos($keywords, 'sunscreen') !== false) {
            $reply = $locale === 'ar'
                ? "طريقة استخدام واقي الشمس بالشكل الصحيح والمعتمد:\n\n" .
                  "1. **التوقيت:** يُوضع صباحاً كآخر خطوة في روتين العناية بالبشرة، بعد المرطب وقبل المكياج بنحو 15 دقيقة.\n" .
                  "2. **التوزيع:** وزّع كمية وفيرة (بمقدار إصبعين) بالتساوي على كامل الوجه والرقبة والأذنين، واتركه على البشرة دون شطف.\n" .
                  "3. **إعادة التطبيق:** يجب إعادة تطبيق واقي الشمس كل ساعتين عند التواجد خارج المنزل، وتجديده فوراً بعد السباحة أو التعرّق أو تجفيف الوجه، ولا يُكتفى بمرة واحدة طوال اليوم."
                : "Proper sunscreen application guide:\n\n" .
                  "1. Apply generously in the morning as the final skincare step, 15 minutes before sun exposure, without rinsing.\n" .
                  "2. Cover face, neck, and ears evenly.\n" .
                  "3. Reapply every 2 hours when outdoors, and immediately after swimming or heavy sweating.";

            return [
                'status' => true,
                'reply' => $reply,
                'recommended_products' => [],
            ];
        }

        // 3. General skin recommendations fallback
        $products = collect();
        try {
            $products = Product::take(3)->get();
        } catch (\Throwable $e) {}

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
            'recommended_products' => $products->isNotEmpty() ? ProductListResource::collection($products)->resolve() : [],
        ];
    }
}
