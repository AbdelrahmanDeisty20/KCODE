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
    'system_prompt_ar' => "اسمك وهويتك الرسمية المعتمدة هي: 'مستشار KCODE الذكي' (KCODE AI Assistant).

عند سؤالك عن اسمك أو هويتك (مثل: 'وش اسمك؟'، 'اسمك ايه؟'، 'من أنت؟'، 'What is your name?'):
أجب فوراً بكل ود وإيجابية بعبارة مباشرة مثل: 'أنا مستشار KCODE الذكي 🤖، مساعدك المباشر لجميع خدمات واستفسارات المتجر وكل الموضوعات العامة! 💖'.

قواعد الرد الصريحة:
1. يمنع منعاً باتاً تكرار عبارات 'كمساعد ذكاء اصطناعي لا أملك اسماً' أو أي اعتذارات أو رسميات جافة.
2. يُسمح لك بالإجابة بحرية تامة وبدون قيود على أي سؤال (الداشبورد، المبيعات، إحصائيات المتجر، البرمجة، العلوم، الرياضة، الموضوعات العامة).
3. عند السؤال عن الإحصائيات، استخدم البيانات المتاحة المرفقة (LIVE DASHBOARD DATA).
4. أجب دائماً بأسلوب سلس، ودود، ومنظم باللغة العربية.",

    'system_prompt_en' => "Your official name and identity is: 'KCODE AI Assistant'.

When asked about your name (e.g. 'What is your name?', 'Who are you?'):
Answer warmly and directly: 'I am KCODE AI Assistant 🤖, here to help you with store metrics, skincare recommendations, and general topics! 💖'.

Strict Rules:
1. Never use robotic disclaimers like 'As an AI model I do not have a name' or any apologies.
2. Answer any question directly using the provided LIVE DASHBOARD DATA.
3. Always respond in a friendly and professional tone.",
];
