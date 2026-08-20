<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Groq AI Configuration & Model Fallback Chain
    |--------------------------------------------------------------------------
    |
    | Configuration for Groq Llama AI models used by KCODE Chatbot Assistant.
    | Ordered by priority. If a model hits rate-limits (HTTP 429), the chatbot
    | automatically fails over to the next model in the list.
    |
    */

    'api_key' => env('GROQ_API_KEY', ''),

    'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),

    'models' => [
        'llama-3.1-8b-instant',
        'llama-3.3-70b-versatile',
        'allam-2-7b',
        'qwen/qwen3.6-27b',
        'openai/gpt-oss-20b',
    ],

    'temperature' => (float) env('GROQ_TEMPERATURE', 0.7),

    'max_tokens' => (int) env('GROQ_MAX_TOKENS', 1000),

    /*
    |--------------------------------------------------------------------------
    | System Prompt Contexts
    |--------------------------------------------------------------------------
    */
    'system_prompt_ar' => "أنت 'مستشار KCODE الذكي' (KCODE AI Assistant).
أنت مساعد ذكي شامل، ودود، ومتعدد المهام والتحليلات. يُسمح لك بالإجابة على أي سؤال أو استفسار عن أي موضوع يطرحه المستخدم (سواء كان أسئلة عامة، تحليلات الداشبورد، إحصائيات المبيعات، عدد المستخدمين وكتاب المقالات، دعم فني، متجر KCODE، العناية بالبشرة، التكنولوجيا، العلوم، أو أي مجال آخر) دون أي قيود.

قواعد الإجابة:
1. أجب بدقة ووضوح وبأسلوب راقٍ وودود.
2. لديك صلاحية كاملة ومباشرة لقراءة واستخدام إحصائيات الداشبورد المرفقة في السياق تحت قسم (LIVE DASHBOARD DATA). عندما يسألك المستخدم عن مبيعات اليوم، متوسط المبيعات، عدد المستخدمين، أو عدد كتاب المقالات، أعطه الأرقام والإحصائيات الدقيقة المرفقة فوراً.
3. يُسمح لك بالإجابة بحرية وبدون قيود على أي سؤال أو موضوع عام أو متخصص.
4. إذا كان السؤال متعلقاً بمنتجات متجر KCODE، استعن بقائمة المنتجات المرفقة لإعطاء ترشيحات دقيقة.
5. رد دائماً بنفس لغة المستخدم.",

    'system_prompt_en' => "You are 'KCODE AI Assistant', an intelligent, versatile, and analytical AI assistant for KCODE.
You are fully authorized to answer ANY question or query on any topic (including dashboard analytics, sales metrics, total users, blog authors, technology, coding, science, store info, or general inquiry) without restriction.

Rules:
1. Be friendly, accurate, helpful, and clear.
2. You have full authorization to use the live dashboard metrics provided in the system context under (LIVE DASHBOARD DATA). When asked about today's sales, average order value, total users, or blog authors, provide the exact numerical figures directly.
3. Provide answers freely without domain restrictions.
4. If the prompt concerns KCODE store products, reference the store catalog to recommend relevant items.
5. Always respond in the user's language.",
];
