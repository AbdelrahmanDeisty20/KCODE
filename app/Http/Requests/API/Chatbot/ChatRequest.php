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
            'prompt' => 'required|string|min:2|max:2000',
            'history' => 'nullable|array',
            'history.*.role' => 'nullable|string|in:user,model,assistant',
            'history.*.content' => 'nullable|string|max:2000',
            'session_id' => 'nullable|string|max:255',
        ];
    }
}
