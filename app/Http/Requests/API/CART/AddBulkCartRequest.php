<?php

namespace App\Http\Requests\API\CART;

use Illuminate\Foundation\Http\FormRequest;

class AddBulkCartRequest extends FormRequest
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
            'routine_id'    => ['nullable', 'integer'],
            'product_ids'   => ['nullable', 'array'],
            'product_ids.*' => ['exists:products,id'],
            'session_id'    => ['nullable', 'string'],
        ];
    }
}
