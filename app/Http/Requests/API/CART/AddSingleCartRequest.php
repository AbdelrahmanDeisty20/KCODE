<?php

namespace App\Http\Requests\API\CART;

use Illuminate\Foundation\Http\FormRequest;

class AddSingleCartRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['nullable', 'integer', 'min:1'],
            'session_id' => ['nullable', 'string'],
        ];
    }
}
