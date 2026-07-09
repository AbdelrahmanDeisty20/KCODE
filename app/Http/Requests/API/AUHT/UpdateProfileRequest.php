<?php

namespace App\Http\Requests\API\AUHT;

use Auth;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'name' => 'nullable|string|min:3|max:255',
            'email' => 'nullable|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'skin_type_id' => 'nullable|exists:skin_types,id',
            'password' => 'nullable|string|confirmed|min:8|max:255',
            'current_password' => 'required|string|current_password',
        ];
    }

    public function messages()
    {
        return [
            'name.min' => __('validation.min'),
            'name.max' => __('validation.max'),
            'email.unique' => __('validation.unique'),
            'image.max' => __('validation.max'),
            'skin_type_id.exists' => __('validation.exists'),
            'password.confirmed' => __('validation.confirmed'),
            'password.min' => __('validation.min'),
            'password.max' => __('validation.max'),
            'current_password.required' => __('validation.required'),
            'current_password.current_password' => __('validation.current_password'),
        ];
    }
}
