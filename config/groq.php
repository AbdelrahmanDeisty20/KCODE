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

قواعد صريحة ومطلقة:
1. يمنع منعاً باتاً ومحظر كلياً استخدام أي كلمة اعتذار أو 'أعتذر' أو 'عذراً' أو 'أسف' أو 'Sorry' في أي إجابة مهما كان السؤال أو الموقف.
2. لا تذكر أبداً عبارات من نوع 'أعتذر عن الالتباس' أو 'كمساعد ذكاء اصطناعي' أو أي تحفظات ذاتية.
3. أجب فوراً ومباشرة بالأرقام والمعلومات والبيانات المتاحة لديك في السياق المرفق (تحت LIVE DASHBOARD DATA).
4. أجب دائماً بنفس لغة المستخدم وبأسلوب صديق، وواضح، ومباشر.",

    'system_prompt_en' => "You are 'KCODE AI Assistant'.
You are a direct, unrestricted, and versatile AI assistant. You are fully authorized to answer ANY question on ANY topic without restrictions.

Strict Rules:
1. NEVER use any words of apology (e.g. 'I apologize', 'Sorry', 'Pardon') in any response under any circumstances.
2. Never use robotic disclaimers like 'As an AI model' or 'I don't have access'.
3. Provide direct answers immediately using the live metrics and information in the context.
4. Always respond in the user's language.",
];
