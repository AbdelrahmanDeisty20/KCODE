<?php

namespace App\Http\Requests\API\FORGETPASSWORD;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'email' => 'required|email|exists:otps,email',
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed|regex:/[A-Z]/',
            'password_confirmation' => 'required|same:password',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => __('validation.required'),
            'email.email' => __('validation.email'),
            'email.exists' => __('validation.exists'),
            'token.required' => __('validation.required'),
            'password.required' => __('validation.required'),
            'password.min' => __('validation.min'),
            'password.confirmed' => __('validation.confirmed'),
            'password.regex' => __('validation.password_uppercase'),
            'password_confirmation.required' => __('validation.required'),
            'password_confirmation.same' => __('validation.same'),
        ];
    }
}
