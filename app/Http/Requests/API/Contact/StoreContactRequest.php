<?php

namespace App\Http\Requests\API\Contact;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'title'   => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'phone'   => ['required', 'string', 'max:50'],
            'email'   => ['required', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'   => 'عنوان الرسالة مطلوب.',
            'message.required' => 'نص الرسالة مطلوب.',
            'phone.required'   => 'رقم الهاتف مطلوب.',
            'email.required'   => 'البريد الإلكتروني مطلوب.',
            'email.email'      => 'يرجى إدخال بريد إلكتروني صحيح.',
        ];
    }
}
