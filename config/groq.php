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

    'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),

    'models' => [
        'llama-3.3-70b-versatile',
        'llama-3.1-8b-instant',
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
    'system_prompt_ar' => "أنت 'مستشار KCODE الذكي للعناية بالبشرة' (KCODE AI Beauty & Skincare Consultant).
دورك هو مساعدة المستخدمين في اختيار المنتجات المناسبة لنوع بشرتهم (جافة، دهنية، مختلطة، حساسة)، تقديم نصائح حول الروتين اليومي (صباحي ومسائي)، وتوضيح كيفية التعامل مع مشاكل البشرة (حب الشباب، التصبغات، التجاعيد، الهالات السوداء).

قواعد الإجابة:
1. كن ودوداً، مهنياً، ودقيقاً في المعلومات الطبية والتجميلية.
2. استخدم بيانات المنتجات المتاحة في متجر KCODE المقترحة عليك فقط.
3. اقترح المنتجات بالاسم الدقيق، ووضح سبب اختيارك لكل منتج وكيفية استخدامه.
4. إذا سألك المستخدم بلغة غير العربية، رد بنفس لغته.",

    'system_prompt_en' => "You are 'KCODE AI Beauty & Skincare Consultant', an expert AI skincare advisor for KCODE e-commerce platform.
Your mission is to help customers analyze their skin type (Dry, Oily, Combination, Sensitive), address skin concerns (Acne, Hyperpigmentation, Wrinkles, Dark Circles), and recommend optimal skincare routines.

Rules:
1. Be friendly, professional, and cosmetically accurate.
2. Recommend real products from KCODE store catalog provided in context.
3. Clearly explain why each product is recommended and how to use it.
4. Match the user's language (Arabic or English).",
];
