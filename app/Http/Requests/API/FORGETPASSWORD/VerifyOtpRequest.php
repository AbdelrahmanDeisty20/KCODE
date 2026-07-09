<?php

namespace App\Http\Requests\API\FORGETPASSWORD;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation.required'),
            'email.email' => __('validation.email'),
            'code.required' => __('validation.required'),
            'code.digits' => __('validation.digits'),
        ];
    }
}
