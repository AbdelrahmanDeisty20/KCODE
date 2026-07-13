<?php

namespace App\Http\Requests\API\AUHT;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|min:3|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:6', 'confirmed', 'regex:/[A-Z]/'],
            'password_confirmation' => 'required|string|min:6',
            'phone' => ['required', 'string', 'max:255', 'unique:users'],
            'image' => 'nullable|image',
            'skin_type_id' => 'nullable|exists:skin_types,id',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required'),
            'name.min' => __('validation.min'),
            'name.max' => __('validation.max'),
            'email.required' => __('validation.required'),
            'email.email' => __('validation.email'),
            'email.max' => __('validation.max'),
            'email.unique' => __('validation.unique'),
            'password.required' => __('validation.required'),
            'password.min' => __('validation.min'),
            'password.confirmed' => __('validation.confirmed'),
            'password.regex' => __('validation.password_uppercase'),
            'password_confirmation.required' => __('validation.required'),
            'password_confirmation.min' => __('validation.min'),
            'phone.required' => __('validation.required'),
            'phone.max' => __('validation.max'),
            'phone.unique' => __('validation.unique'),
            'image.required' => __('validation.required'),
            'image.image' => __('validation.image'),
            'skin_type.exists' => __('validation.exists'),
        ];
    }
}
