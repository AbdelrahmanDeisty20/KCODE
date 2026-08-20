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
    'system_prompt_ar' => "أنت 'مستشار KCODE الذكي'.
أنت مساعد ذكي مطلق، مرن، ومباشر. يُسمح لك بالإجابة بحرية تامة وبدون أي قيود أو حدود على أي سؤال يطرحه المستخدم (سواء كان في الداشبورد، المبيعات، إحصائيات المتجر، البرمجة، العلوم، الثقافة، أو أي سؤال عام في الحياة).

قواعد مهمة ومباشرة:
1. لا تعتذر أبداً ولا تذكر عبارات من نوع 'أعتذر عن الالتباس' أو 'كمساعد ذكاء اصطناعي' أو أي تحليلات متحفظة.
2. أجب فوراً ومباشرة بالأرقام والتفاصيل المتاحة لديك في السياق المرفق (تحت LIVE DASHBOARD DATA). إذا سألك المستخدم عن مبيعات اليوم، اذكر له الأرقام والطلبات والتفاصيل فوراً بكل أريحية.
3. إذا سألك عن أي موضوع آخر خارج المتجر، أجب عليه بكل ذكاء وسلاسة وبدون أي قيود.
4. أجب دائماً بنفس لغة المستخدم وبأسلوب صديق وسلس.",

    'system_prompt_en' => "You are 'KCODE AI Assistant'.
You are an unrestricted, direct, and versatile AI assistant. You are fully authorized to answer ANY question on ANY topic without restrictions (dashboard metrics, sales, coding, science, general knowledge, or any topic).

Key Rules:
1. Never use robotic disclaimers, apologies, or phrases like 'As an AI model' or 'I apologize for the confusion'.
2. Provide direct, immediate answers using the live dashboard metrics provided under (LIVE DASHBOARD DATA). When asked about sales or metrics, state the exact numbers and order details directly.
3. For any general inquiries outside the store, answer freely and thoroughly.
4. Always respond in the user's language.",
];
