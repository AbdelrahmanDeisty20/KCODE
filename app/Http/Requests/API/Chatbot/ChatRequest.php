<?php

namespace App\Http\Requests\API\Chatbot;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prompt' => 'required|string|min:2|max:1000',
            'history' => 'nullable|array',
            'history.*.role' => 'nullable|string|in:user,model,assistant',
            'history.*.content' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'prompt.required' => __('messages.prompt_required', ['default' => 'يرجى إدخال السؤال أو الرسالة']),
            'prompt.string' => __('messages.prompt_invalid'),
            'prompt.max' => __('messages.prompt_too_long', ['default' => 'الرسالة طويلة جداً']),
        ];
    }
}
